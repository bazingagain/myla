<?php

namespace App\Http\Controllers;

use App\Feedback;
use Redis;
use Cache;
use Storage;
use Illuminate\Support\Facades\DB;
use App\ClientUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;

use JPush;

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

        $client = new JPush(self::$APP_KEY, self::$MASTER_SECRET);
        $result = $client->push()
            ->setPlatform(array('ios', 'android'))
            ->addAlias('alias1')
            ->addTag(array('tag1', 'tag2'))
            ->setNotificationAlert('Hi, JPush')
            ->addAndroidNotification('Hi, android notification', 'notification title', 1, array("key1" => "value1", "key2" => "value2"))
            ->addIosNotification("Hi, iOS notification", 'iOS sound', JPush::DISABLE_BADGE, true, 'iOS category', array("key1" => "value1", "key2" => "value2"))
            ->setMessage("msg content", 'msg title', 'type', array("key1" => "value1", "key2" => "value2"))
            ->setOptions(100000, 3600, null, false)
            ->send();
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
        $temp = ClientUser::where('clientName', '=', $array['clientName'])->get();
        if (count($temp) == 0) {
            return response()->json(['login' => false, 'message' => '用户不存在', 'nickname' => null, 'sex' => null, 'address' => null, 'signature' => null]);
        } else {
            if ($temp[0]['password'] != $array['password']) {
                return response()->json(['login' => false, 'message' => '密码错误', 'nickname' => null, 'sex' => null, 'address' => null, 'signature' => null]);
            } else {
                $clientUser = $temp[0];
                return response()->json(['login' => true, 'message' => '登录成功', 'nickname' => $clientUser['nick_name'], 'sex' => $clientUser['sex'], 'address' => $clientUser['address'], 'signature' => $clientUser['signature']]);
            }
        }
    }

    public function setSignature(Request $request)
    {
        $jsonstr = $request->input('userSignature');
        $array = json_decode($jsonstr, true);
        $temp = ClientUser::where('clientName', $array['clientName'])->get();
        if (count($temp) == 0) {
            return response()->json(['saveSignature' => false, 'message' => '更新签名失败']);
        } else {
            $clientUser = $temp[0];
            $clientUser->signature = $array['signature'];
            $clientUser->save();
            return response()->json(['saveSignature' => true, 'message' => '更新签名成功']);
        }

    }

    public function setNickname(Request $request)
    {
        $jsonstr = $request->input('userNickname');
        $array = json_decode($jsonstr, true);
        $temp = ClientUser::where('clientName', $array['clientName'])->get();
        if (count($temp) == 0) {
            return response()->json(['saveNickname' => false, 'message' => '更新签名失败']);
        } else {
            $clientUser = $temp[0];
            $clientUser->nick_name = $array['nickname'];
            $clientUser->save();
            return response()->json(['saveNickname' => true, 'message' => '更新签名成功']);
        }
    }

    //原密码在客户端已经判断是否正确，无需在服务器端验证
    public function modifyPassword(Request $request)
    {
        $jsonstr = $request->input('userModifyPassword');
        $array = json_decode($jsonstr, true);
        $temp = ClientUser::where('clientName', $array['clientName'])->get();
        if (count($temp) == 0) {
            return response()->json(['savePassword' => false, 'message' => '修改密码失败']);
        } else {
            $clientUser = $temp[0];
            $clientUser->password = $array['newPassword'];
            $clientUser->save();
            return response()->json(['savePassword' => true, 'message' => '修改密码成功']);
        }
    }

    public function setSex(Request $request)
    {
        $jsonstr = $request->input('userSex');
        $array = json_decode($jsonstr, true);
        $temp = ClientUser::where('clientName', $array['clientName'])->get();
        if (count($temp) == 0) {
            return response()->json(['saveSex' => false, 'message' => '修改性别失败']);
        } else {
            $clientUser = $temp[0];
            $clientUser->sex = $array['sex'];
            $clientUser->save();
            return response()->json(['saveSex' => true, 'message' => '修改性别成功']);
        }
    }

    public function setIcon(Request $request)
    {
        error_log('存储头像响应');
        $jsonstr = $request->input('setClientPic');
        $clientIcon = $request->file('file');
        if($clientIcon->isValid()) {
            $array = json_decode($jsonstr, true);
//            $temp = ClientUser::where('clientName', $array['clientName'])->get();

            //客户端上传的头像，前面加加上'USER_ （.... .PNG）'
            if (!Storage::disk('local')->exists('userPic')) {
                Storage::disk('local')->makeDirectory('userPic');
            }
            error_log('创建存放用户头像的文件名:' . $array['fileNameWithNoSuffix'] . ':EndWith>' . $array['userPicFilePathEnd']);
            if ($array['userPicFilePathEnd'] == 'jpg') {
                if(Storage::disk('local')->exists('userPic/USER_' . md5($array['clientName']).'.jpg')){
                    Storage::disk('local')->delete('userPic/USER_' . md5($array['clientName']).'.jpg');
                }
                error_log('存储qian');
                Storage::disk('local')->put('userPic/USER_' .md5($array['clientName']). '.jpg', file_get_contents($request->file('file')));
                error_log('存储jpg');

            } else if ($array['userPicFilePath'] == 'jpeg') {
                if(Storage::disk('local')->exists('userPic/USER_' . md5($array['clientName']).'.jpeg')){
                    Storage::disk('local')->delete('userPic/USER_' . md5($array['clientName']).'.jpeg');
                }
                Storage::disk('local')->put('userPic/USER_' . md5($array['clientName']) . '.jpeg', file_get_contents($request->file('file')));
                error_log('存储jpeg');

            } else if ($array['userPicFilePath'] == 'png') {
                if(Storage::disk('local')->exists('userPic/USER_' . md5($array['clientName']).'.png')){
                    Storage::disk('local')->delete('userPic/USER_' . md5($array['clientName']).'.png');
                }
                Storage::disk('local')->put('userPic/USER_' . md5($array['clientName']) . '.png', file_get_contents($request->file('file')));
                error_log('存储png');
            }
            error_log('存储头像成功');
            return response()->json(['sendUserPicResult' => true, 'message' => '更改头像成功']);
        }else{
            return response()->json(['sendUserPicResult' => false, 'message' => '头像图片无效']);
        }
    }

    public function setAddress(Request $request)
    {
        $jsonstr = $request->input('userAddress');
        $array = json_decode($jsonstr, true);
        $temp = ClientUser::where('clientName', $array['clientName'])->get();
        if (count($temp) == 0) {
            return response()->json(['saveAddress' => false, 'message' => '修改地址失败']);
        } else {
            $clientUser = $temp[0];
            $clientUser->address = $array['address'];
            $clientUser->save();
            return response()->json(['saveAddress' => true, 'message' => '修改地址成功']);
        }
    }

    public function feedback(Request $request)
    {
        $jsonstr = $request->input('userFeedback');
        $array = json_decode($jsonstr, true);
        $temp = ClientUser::where('clientName', $array['clientName'])->get();
        if (count($temp) == 0) {
            return response()->json(['saveFeedback' => false, 'message' => '反馈失败']);
        } else {
            $feedback = new Feedback();
            $feedback->clientName = $array['clientName'];
            $feedback->email = $array['email'];
            $feedback->content = $array['content'];
            $feedback->save();
            return response()->json(['saveFeedback' => true, 'message' => '反馈成功']);
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
        //查找要添加的朋友是否在数据库中，
        $temp = ClientUser::where('clientName', '=', $array['friendName'])->get();
        if (count($temp) == 0) {
            return response()->json(['add' => false, 'message' => '用户不存在']);
        } else {
            $temp = ClientUser::where('clientName', '=', $array['clientName'])->get();
            if (count($temp) == 0) {  //  自己不存在
                return response()->json(['add' => false, 'message' => '请求非法']);
            } else {
                $requsetUser = $temp[0];
                //TODO   找到要添加用户，发送JPUSH  推送请求  离线消息保留1天 开发环境
                $client = new JPush(self::$APP_KEY, self::$MASTER_SECRET);
                $result = $client->push()
                    ->setPlatform('android')
                    ->addAlias($array['friendName'])
                    ->addAndroidNotification($array['clientName'] . '请求添加您为好友', null, 1, array('type' => 'add', "friend_name" => $array['clientName'], 'friend_nickname' => $requsetUser['nick_name'], 'pic_url' => $requsetUser['pic_url'], 'friend_sex' => $requsetUser['sex'], 'friend_address' => $requsetUser['address'], 'friend_signature' => $requsetUser['signature']))
                    ->setOptions(100000, 86400, null, false)
                    ->send();
                error_log('添加好友成功');
                return response()->json(['add' => true, 'message' => '已发送请求']);
            }

        }
    }

    //Test 用
    public function sendPush(Request $request)
    {
        ini_set("display_errors", "On");
        error_reporting(E_ALL | E_STRICT);
//        if($request->ajax()){
        $client = new JPush('b20d0b83a6f3c8dc393932c6', 'e521b9a8be050411fe1155b2');
        $result = $client->push()
            ->setPlatform('all')
            ->addAllAudience()
            ->addAndroidNotification('hi')
            ->setOptions(100000, 86400, null, false)
            ->send();
//        echo 'Result=' . json_encode($result).'<br/>';
    }

    /**
     * 同意 添加好友请求
     */
    public function agree(Request $request)
    {
        $jsonstr = $request->input('userAgree');
        $array = json_decode($jsonstr, true);

        $temp = ClientUser::where('clientName', '=', $array['clientName'])->get();
        if (count($temp) == 0) {
            return response()->json(['saveAgree' => false, 'message' => '用户不存在']);
        } else {
            $agreeUser = $temp[0];
            DB::insert('insert into friend_relations (userName, friendName) values (?, ?)', [$array['clientName'], $array['requsetUserName']]);
            DB::insert('insert into friend_relations (userName, friendName) values (?, ?)', [$array['requsetUserName'], $array['clientName']]);
            error_log('好友关系' . $array['clientName'] . ':' . $array['requsetUserName'] . '插入成功');
            $client = new \JPush(self::$APP_KEY, self::$MASTER_SECRET);
            $result = $client->push()
                ->setPlatform('android')
                ->addAlias($array['requsetUserName'])
                ->addAndroidNotification('添加好友' . $array['clientName'] . '成功', null, 1, array('type' => 'agree', "friend_name" => $agreeUser->clientName, 'friend_nickname' => $agreeUser->nick_name, 'pic_url' => $agreeUser->pic_url, 'friend_sex' => $agreeUser->sex, 'friend_address' => $agreeUser->address, 'friend_signature' => $agreeUser->signature))
                ->setOptions(100000, 3600, null, false)
                ->send();
            return response()->json(['saveAgree' => true, 'message' => '同意好友添加请求']);
        }

    }

    public function userSyncRelationTable(Request $request)
    {
        error_log('查询关系表');
        $jsonstr = $request->input('sync');
        $array = json_decode($jsonstr, true);
        error_log($array['clientName']);
        $friends = DB::table('friend_relations')
            ->join('client_users', 'friend_relations.friendName','=', 'client_users.clientName')
            ->select('friend_relations.*', 'client_users.*')
            ->where('friend_relations.userName', '=', $array['clientName'])
            ->get();
        error_log(count($friends));
        foreach($friends as $friend){
//            error_log($friend->friendName);
         }
        return response()->json(
            $friends
        );
    }

    private function registerID($alias)
    {
        $client = new JPush(self::$APP_KEY, self::$MASTER_SECRET, null, null);
        $result = $client->device()->getAliasDevices($alias);
        if (is_null($result))
            error_log('没有找到别名为:' . $alias . '的设备');
        else {
//            找到别名对应的设别注册ID
            $data = json_encode($result);
            $dl = json_decode($data, true);
            error_log($dl['data']['registration_ids'][0]);
        }
    }

    private function report()
    {
        $client = new JPush(self::$APP_KEY, self::$MASTER_SECRET);
        $report = $client->report();
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

    public function shareLocation(Request $request)
    {
        $jsonstr = $request->input('requsetShareLoc');
        $array = json_decode($jsonstr, true);
        $clientName = $array['clientName'];
        $temp = ClientUser::where('clientName', '=', $clientName)->get();
        $user = $temp[0];
        $client = new JPush(self::$APP_KEY, self::$MASTER_SECRET);
        $result = $client->push()
            ->setPlatform('android')
            ->addAlias($array['contactName'])
            ->addAndroidNotification($array['clientName'] . '请求共享位置', null, 1, array('type' => 'requestShareLoc', "friend_name" => $user->clientName, 'friend_nickname' => $user->nick_name, 'pic_url' => $user->pic_url, 'friend_sex' => $user->sex, 'friend_address' => $user->address, 'friend_signature' => $user->signature))
            ->setOptions(100000, 3600, null, false)
            ->send();
        error_log('服务器发送 shareLocation成功');
        return response()->json(['shareLoc' => true, 'message' => '已发送位置共享请求']);
    }

    public function getContactLocation(Request $request)
    {
        $jsonstr = $request->input('getContactLoc');
        $array = json_decode($jsonstr, true);
        $contactName = $array['getLocFriendName'];
        $temp = ClientUser::where('clientName', '=', $contactName)->get();
        if (count($temp) == 0) {  //  自己不存在
            return response()->json(['getContactLoc' => false, 'message' => '用户不存在']);
        } else {
            return response()->json(array(
                'getContactLoc' => true,
                'message' => '返回位置',
                'location_latitude' => Cache::has('location_latitude:' . $contactName) ? (Cache::get('location_latitude:' . $contactName)) : 0,
                'location_lontitude' => Cache::has('location_lontitude:' . $contactName) ? (Cache::get('location_lontitude:' . $contactName)) : 0,
            ));
        }
    }

    public function agreeShareLocation(Request $request)
    {
        $jsonstr = $request->input('agreeShareLoc');
        $array = json_decode($jsonstr, true);
        $clientName = $array['clientName'];
        $temp = ClientUser::where('clientName', '=', $clientName)->get();
        $agreeUser = $temp[0];
        $client = new JPush(self::$APP_KEY, self::$MASTER_SECRET);
        $result = $client->push()
            ->setPlatform('android')
            ->addAlias($array['agreeShareUserName'])
            ->addAndroidNotification($array['clientName'] . '同意共享位置', null, 1, array('type' => 'agreeShareLoc', "friend_name" => $agreeUser->clientName, 'friend_nickname' => $agreeUser->nick_name, 'pic_url' => $agreeUser->pic_url, 'friend_sex' => $agreeUser->sex, 'friend_address' => $agreeUser->address, 'friend_signature' => $agreeUser->signature))
            ->setOptions(100000, 3600, null, false)
            ->send();
        error_log('服务器发送 agreeShareLocation成功');
        return response()->json(['agreeShareLoc' => true, 'message' => '已发送同意位置共享请求']);
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
//                $check = "<a href='#user_info' data-toggle=\"modal\" class=\"btn btn-primary btn-large\" onclick='getUserDetail(num)'>查看</a>";

                $tabstr = "<table class='table'>";
                $tabstr .= "<thead align=\"center\">
                            <tr>
                                <td>客户ID</td>
                                <td>客户名</td>
                                <td>昵称</td>
                                <td>性别</td>
                                <td>地址</td>
                                <td>详细信息</td>
                            </tr>
                        </thead><tbody align=\"center\">";
                foreach ($temps as $temp) {
                    $id = $temp->id;
                    $clientName = $temp->clientName;
                    $nickName = $temp->nick_name;
                    $sex = $temp->sex;
                    $address = $temp->address;
                    $check = "<a href='#user_info' data-name =$temp->clientName data-toggle=\"modal\" class=\"btn btn-primary btn-large\" onclick='getUserDetail(this)'>查看</a>";
                    $tabstr .= "<tr><td>" . $id . "</td><td >" . $clientName . "</td><td>" . $nickName . "</td><td>" . $sex . "</td><td>" . $address . "</td><td>" . $check . "</td></tr>";
                }
                $tabstr .= "</tbody></table>";
                return response()->json(array(
                    'status' => 1,
                    'msg' => $tabstr,
                ));
            }
        }

    }

    public function detail(Request $request)
    {
        if ($request->ajax()) {
            $nameStr = $request->input('name');
//            temps是一个集合
            $temps = ClientUser::where('clientName', '=', $nameStr)->get();
            $ttt = count($temps);
            if (count($temps) == 0) {
                return Redirect::back()->withInput()->withErrors('查询失败!');
            } else {
                //只循环一次
                $pic_url = null;
                $signature = null;
                $created_at = null;
                $updated_at = null;
                foreach ($temps as $temp) {
                    $pic_url = $temp->pic_url;
                    $signature = $temp->signature;
                    $created_at = $temp->created_at;
                    $updated_at = $temp->updated_at;
                }
                return response()->json(array(
                    'status' => 1,
                    'msg' => '成功',
                    'pic_url' => '' . $pic_url,
                    'signature' => '' . $signature,
                    'created_at' => '' . $created_at,
                    'updated_at' => '' . $updated_at,
                ));
            }
        }
    }

    public function feedbackAll(Request $request)
    {
        if ($request->ajax()) {
//            temps是一个集合
            $temps = Feedback::all();
            $ttt = count($temps);
            if (count($temps) <= 0) {
                return response()->json(array(
                    'status' => 1,
                    'msg' => '<p>暂无用户反馈</p>',
                ));
            } else {
                $tabstr = "<table class='table'>";
                $tabstr .= "<thead align=\"center\">
                            <tr>
                                <td>反馈ID</td>
                                <td>客户名</td>
                                <td>邮箱</td>
                                <td>反馈摘要</td>
                                <td>反馈详情</td>
                                <td></td>
                            </tr>
                        </thead><tbody align=\"center\">";
                foreach ($temps as $temp) {
                    $id = $temp->id;
                    $clientName = $temp->clientName;
                    $email = $temp->email;
                    $feedbackDigest = self::cubstr($temp->content, 0, 8);
                    $check = "<a href='#feedback_info' data-id =$temp->id data-name =$temp->clientName data-toggle=\"modal\" class=\"btn btn-primary btn-large\" onclick='getFeedbackDetail(this)'>查看</a>";
                    $hand = "<a href='#' data-id =$temp->id data-name =$temp->clientName data-toggle=\"modal\" class=\"btn btn-primary btn-large\" onclick='handleFeedback(this)'>处理</a>";
                    $tabstr .= "<tr><td>" . $id . "</td><td >" . $clientName . "</td><td>" . $email . "</td><td>" . $feedbackDigest . "</td><td>" . $check . "</td><td>".$hand."</td></tr>";
                }
                $tabstr .= "</tbody></table>";
                return response()->json(array(
                    'status' => 1,
                    'msg' => $tabstr,
                ));
            }
        }
    }

    /**
     * 解决substr 截取汉字乱码问题
     * @param $string
     * @param $beginIndex
     * @param $length
     * @return string
     */
    function cubstr($string, $beginIndex, $length)
    {
        if (strlen($string) < $length) {
            return substr($string, $beginIndex);
        }

        $char = ord($string[$beginIndex + $length - 1]);
        if ($char >= 224 && $char <= 239) {
            $str = substr($string, $beginIndex, $length - 1);
            return $str;
        }

        $char = ord($string[$beginIndex + $length - 2]);
        if ($char >= 224 && $char <= 239) {
            $str = substr($string, $beginIndex, $length - 2);
            return $str;
        }

        return substr($string, $beginIndex, $length);
    }

    public function feedbackDetail(Request $request)
    {
        if ($request->ajax()) {
            $idStr = $request->input('id');
//            temps是一个集合
            $temps = Feedback::where('id', '=', $idStr)->get();
            if (count($temps) == 0) {
                return Redirect::back()->withInput()->withErrors('查询失败!');
            } else {
                //只循环一次
                $feedback_content = null;
                $created_at = null;
                foreach ($temps as $temp) {
                    $feedback_content = $temp->content;
                    $created_at = $temp->created_at;
                }
                error_log($feedback_content);
                return response()->json(array(
                    'status' => 1,
                    'msg' => '成功',
                    'feedback_content' => $feedback_content,
                    'created_at' => '' . $created_at,
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
                error_log('location_latitude:' . Cache::get('location_latitude:' . $nameStr));
                error_log('location_lontitude:' . Cache::get('location_lontitude:' . $nameStr));

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
    public function trackUser(Request $request)
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


}
