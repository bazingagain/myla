<?php

namespace App\Http\Controllers;

use Redis;
use Cache;
use Illuminate\Support\Facades\DB;
use App\ClientUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class ClientUserController extends Controller
{
    private static $APP_KEY = 'b20d0b83a6f3c8dc393932c6';
    private static $MASTER_SECRET = 'e521b9a8be050411fe1155b2';

    /**
     * @param Request $request
     */
    public function register(Request $request)
    {
        error_log($request);
        error_log($request->input('json'));
        $jsonstr = $request->input('json');
        $array = json_decode($jsonstr, true);
        error_log(count($array));
        error_log($array['uid']);
    }

    /**
     * 测试redis
     *
     * @param  int $id
     * @return Response
     */
    public function test()
    {
//        Redis::set('name', 'Taylor');
//        Redis::set()
//        $values = Redis::lrange('names', 5, 10);
        /*$values = Redis::command('set', ['name', 'Taylor']);
        Redis::pipeline(function ($pipe) {
            for ($i = 0; $i < 10; $i++) {
                $pipe->set("key:$i", $i);
            }
        });*/

        $br = '<br/>';
        $client = new \JPush(self::$APP_KEY, self::$MASTER_SECRET);
        $result = $client->push()
            ->setPlatform('all')
            ->addAllAudience()
            ->setNotificationAlert('Hi, JPush')
            ->send();

        echo 'Result=' . json_encode($result) . $br;
    }


    /**
     *
     * 注册一个新的客户端用户并添加到数据库中
     * 模型的属性名要与表的列名相对应
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request)
    {
        $jsonstr = $request->input('json');
        $array = json_decode($jsonstr, true);
        $temp = ClientUser::where('clientName', $array['clientName'])->first();
        if (count($temp) == 0) {
            $clientUser = new ClientUser();
            $clientUser->clientName = $array['clientName'];
            $clientUser->password = $array['password'];
            $clientUser->save();
            return response()->json(['register' => true, 'message' => '创建成功']);
        } else
            return response()->json(['register' => false, 'message' => '用户已存在']);

    }

    /**
     *
     * 验证客户端用户登录接口
     *
     * @param Request $request
     */
    public function login(Request $request)
    {
        error_log($request->input('login'));
        $jsonstr = $request->input('login');
        $array = json_decode($jsonstr, true);
        $temp = ClientUser::where('clientName', '=', $array['clientName'])->where('password', '=', $array['password'])->get();
        if (count($temp) == 0) {
            return response()->json(['login' => false, 'message' => '用户不存在', 'nickname'=>null, 'sex'=>null, 'address'=>null, 'signature'=>null]);
        } else {
            $clientUser = $temp[0];
            return response()->json(['login' => true, 'message' => '登录成功','nickname'=>$clientUser['nick_name'], 'sex'=>$clientUser['sex'], 'address'=>$clientUser['address'], 'signature'=>$clientUser['signature']]);
        }
    }

    public function setSignature(Request $request){
        $jsonstr = $request->input('userSignature');
        $array = json_decode($jsonstr, true);
        $temp = ClientUser::where('clientName', $array['clientName'])->get();
        if (count($temp) == 0) {
            return response()->json(['saveSignature' => false, 'message' => '更新签名失败']);
        } else{
            $clientUser = $temp[0];
            $clientUser->signature = $array['signature'];
            $clientUser->save();
            return response()->json(['saveSignature' => true, 'message' => '更新签名成功']);
        }

    }

    public function setNickname(Request $request){
        $jsonstr = $request->input('userNickname');
        $array = json_decode($jsonstr, true);
        $temp = ClientUser::where('clientName', $array['clientName'])->get();
        if (count($temp) == 0) {
            return response()->json(['saveNickname' => false, 'message' => '更新签名失败']);
        } else{
            $clientUser = $temp[0];
            $clientUser->nick_name = $array['nickname'];
            $clientUser->save();
            return response()->json(['saveNickname' => true, 'message' => '更新签名成功']);
        }
    }

    /**
     *
     * 客户端用户请求添加好友
     *
     * @param Request $request
     */
    public function add(Request $request)
    {
        $jsonstr = $request->input('add');
        $array = json_decode($jsonstr, true);
        $temp = ClientUser::where('clientName', '=', $array['friendName'])->get();
        if (count($temp) == 0) {
            return response()->json(['add' => false, 'message' => '用户不存在']);
        } else {
            //TODO   找到要添加用户，发送JPUSH  推送请求
            $client = new \JPush(self::$APP_KEY, self::$MASTER_SECRET);
            $result = $client->push()
                ->setPlatform('android')
                ->addAlias($array['friendName'])
                ->addAndroidNotification($array['clientName'] . '请求添加您为好友', null, 1, array("newFriend" => $array['clientName']))
                ->setOptions(100000, 3600, null, false)
                ->send();
//            echo 'Result=' . json_encode($result) . $br;
//            return response()->json(['add' => true, 'message' => '登录成功']);
            return response()->json(['add' => true, 'message' => '已发送请求']);

        }
    }

    /**
     *
     * 获取来自手机客户端的位置信息
     * locationData{
     *      clientName :
     *      location_latitude :
     *      location_lontitude :
     * }
     *
     * @param Request $request
     */
    public function setLocation(Request $request)
    {
        $jsonstr = $request->input('location');
//        error_log($jsonstr);
        $array = json_decode($jsonstr, true);
        if (($array['clientName'] == 'tempUser') || is_null($array['clientName']))
            return;
        $clientName = $array['clientName'];
//        注意冒号
        Cache::forever('location_latitude:' . $clientName, $array['latitude']);
        Cache::forever('location_lontitude:' . $clientName, $array['lontitude']);
//        error_log("Cache:".$array['clientName'].':'.$array['latitude'].':'.$array['lontitude']);
    }

    public function getLocation(Request $request)
    {

        if ($request->ajax()) {
            return response()->json(array(
                'status' => 1,
                'location_latitude' => Cache::has('location_latitude') ? (Cache::get('location_latitude')) : 0,
                'location_lontitude' => Cache::has('location_lontitude') ? (Cache::get('location_lontitude')) : 0,
                'test' => rand(0, 1000)
            ));
        }

    }

    public function find(Request $request)
    {

        if ($request->ajax()) {
            $nameStr = $request->input('name');
//            temps是一个集合
            $temps = ClientUser::where('clientName', 'like', $nameStr . '%')->get();
            $ttt = count($temps);
            if (count($temps) == 0) {
                return Redirect::back()->withInput()->withErrors('查询失败!');
            } else {
                $check = "<a href='#user_info' data-toggle=\"modal\" class=\"btn btn-primary btn-large\">查看</a>";

                $tabstr = "<table class='table'>";
                $tabstr .= "<thead align=\"center\">
                            <tr>
                                <td>客户ID</td>
                                <td>客户名</td>
                                <td>创建时间</td>
                                <td>更新时间</td>
                                <td>详细信息</td>
                            </tr>
                        </thead><tbody align=\"center\">";
                foreach ($temps as $temp) {
                    $id = $temp->id;
                    $clientName = $temp->clientName;
                    $created_at = $temp->created_at;
                    $updated_at = $temp->updated_at;
                    $tabstr .= "<tr><td>" . $id . "</td><td>" . $clientName . "</td><td>" . $created_at . "</td><td>" . $updated_at . "</td><td>" . $check . "</td></tr>";
                }
                $tabstr .= "</table>";
                return response()->json(array(
                    'status' => 1,
                    'msg' => $tabstr,
                ));
            }
        }

    }

    public function lockUser(Request $request)
    {
        if ($request->ajax()) {
            $nameStr = $request->input('name');
//            temps是一个集合
            $temps = ClientUser::where('clientName', $nameStr)->get();
            $ttt = count($temps);
            if (count($temps) == 0) {
                return response()->json(array(
                    'status' => 1,
                    'msg' => '没有此人',
                    'isUser' => false
                ));
            } else {
//                self::registerID($nameStr);
                error_log('location_latitude:'.Cache::get('location_latitude:' . $nameStr));
                error_log('location_lontitude:'.Cache::get('location_lontitude:' . $nameStr));

                return response()->json(array(
                    'status' => 1,
                    'msg' => '找到此人',
                    'location_latitude' => Cache::has('location_latitude:' . $nameStr) ? (Cache::get('location_latitude:' . $nameStr)) : 0,
                    'location_lontitude' => Cache::has('location_lontitude:' . $nameStr) ? (Cache::get('location_lontitude:' . $nameStr)) : 0,
                    'isUser' => true
                ));
            }
        }
    }

    /**
     * 跟踪用户
     */
    public function trackUser(Request $request){
        if($request->ajax()){
            $nameStr = $request->input('name');
//            temps是一个集合
            $temps = ClientUser::where('clientName', $nameStr)->get();
            $ttt = count($temps);
            if (count($temps) == 0) {
                return response()->json(array(
                    'status' => 1,
                    'msg' => '没有此人',
                    'isUser' => false
                ));
            } else {
                //TODO 这里用户cache会保存最后一次用户所在的位置，(要判断用户是否在线)
                return response()->json(array(
                    'status' => 1,
                    'msg' => '找到此人',
                    'location_latitude' => Cache::has('location_latitude:' . $nameStr) ? (Cache::get('location_latitude:' . $nameStr)) : 0,
                    'location_lontitude' => Cache::has('location_lontitude:' . $nameStr) ? (Cache::get('location_lontitude:' . $nameStr)) : 0,
                    'isUser' => true
                ));
            }
        }
    }

    private function registerID($alias)
    {
        $client = new \JPush(self::$APP_KEY, self::$MASTER_SECRET, null, null);
        $result = $client->device()->getAliasDevices($alias);
        if(is_null($result))
            error_log('没有找到别名为:' . $alias .'的设备');
        else{
//            找到别名对应的设别注册ID
            $data = json_encode($result);
            $dl = json_decode($data,true);
            error_log($dl['data']['registration_ids'][0]);
        }
    }

    private function report()
    {
        $client = new \JPush(self::$APP_KEY, self::$MASTER_SECRET);
        $report = $client->report();
    }


}
