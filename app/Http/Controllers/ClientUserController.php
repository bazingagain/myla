<?php

namespace App\Http\Controllers;

use App\AddRequestInfo;
use App\Feedback;
use App\ShareRequestInfo;
use App\User;
use Redis;
use Auth;
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
            $clientUser->tempshare = $array['tempshare'];
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
        if ($clientIcon->isValid()) {
            $array = json_decode($jsonstr, true);
//            $temp = ClientUser::where('clientName', $array['clientName'])->get();

            //客户端上传的头像，前面加加上'USER_ （.... .PNG）'
            if (!Storage::disk('local')->exists('userPic')) {
                Storage::disk('local')->makeDirectory('userPic');
            }
            error_log('创建存放用户头像的文件名:' . $array['fileNameWithNoSuffix'] . ':EndWith>' . $array['userPicFilePathEnd']);
            if ($array['userPicFilePathEnd'] == 'jpg') {
                if (Storage::disk('local')->exists('userPic/USER_' . md5($array['clientName']) . '.jpg')) {
                    Storage::disk('local')->delete('userPic/USER_' . md5($array['clientName']) . '.jpg');
                }
                error_log('存储qian');
                Storage::disk('local')->put('userPic/USER_' . md5($array['clientName']) . '.jpg', file_get_contents($request->file('file')));
                error_log('存储jpg');

            } else if ($array['userPicFilePath'] == 'jpeg') {
                if (Storage::disk('local')->exists('userPic/USER_' . md5($array['clientName']) . '.jpeg')) {
                    Storage::disk('local')->delete('userPic/USER_' . md5($array['clientName']) . '.jpeg');
                }
                Storage::disk('local')->put('userPic/USER_' . md5($array['clientName']) . '.jpeg', file_get_contents($request->file('file')));
                error_log('存储jpeg');

            } else if ($array['userPicFilePath'] == 'png') {
                if (Storage::disk('local')->exists('userPic/USER_' . md5($array['clientName']) . '.png')) {
                    Storage::disk('local')->delete('userPic/USER_' . md5($array['clientName']) . '.png');
                }
                Storage::disk('local')->put('userPic/USER_' . md5($array['clientName']) . '.png', file_get_contents($request->file('file')));
                error_log('存储png');
            }
            error_log('存储头像成功');
            return response()->json(['sendUserPicResult' => true, 'message' => '更改头像成功']);
        } else {
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
    public function updateTempshare(Request $request)
    {
        $jsonstr = $request->input('updateTempshare');
        $array = json_decode($jsonstr, true);
        $temp = ClientUser::where('clientName', $array['clientName'])->get();
        if (count($temp) == 0) {
            return response()->json(['saveTempshare' => false, 'message' => '修改tempshare失败']);
        } else {
            $clientUser = $temp[0];
            $clientUser->tempshare = $array['tempshare'];
            $clientUser->save();
            return response()->json(['saveTempshare' => true, 'message' => '修改tempshare成功']);
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
            $feedback->status = '未处理';
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

                $addreq = new AddRequestInfo();
                $addreq->addrequestName = $array['clientName'];
                $addreq->addrequestedName = $array['friendName'];
                $addreq->save();

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
            $client = new \JPush(self::$APP_KEY, self::$MASTER_SECRET);
            $result = $client->push()
                ->setPlatform('android')
                ->addAlias($array['requsetUserName'])
                ->addAndroidNotification('添加好友' . $array['clientName'] . '成功', null, 1, array('type' => 'agree', "friend_name" => $agreeUser->clientName, 'friend_nickname' => $agreeUser->nick_name, 'pic_url' => $agreeUser->pic_url, 'friend_sex' => $agreeUser->sex, 'friend_address' => $agreeUser->address, 'friend_signature' => $agreeUser->signature))
                ->setOptions(100000, 3600, null, false)
                ->send();
            DB::insert('insert into friend_relations (userName, friendName, sharestatusme, sharestatusfr) values (?, ?, ?, ?)', [$array['clientName'], $array['requsetUserName'], false, false]);
            DB::insert('insert into friend_relations (userName, friendName, sharestatusme, sharestatusfr) values (?, ?, ?, ?)', [$array['requsetUserName'], $array['clientName'] ,false, false]);
            error_log('好友关系' . $array['clientName'] . ':' . $array['requsetUserName'] . '插入成功');
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
            ->join('client_users', 'friend_relations.friendName', '=', 'client_users.clientName')
            ->select('friend_relations.*', 'client_users.*')
            ->where('friend_relations.userName', '=', $array['clientName'])
            ->get();
        error_log(count($friends));
        foreach ($friends as $friend) {
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

        //存共享请求数
        $sharereq = new ShareRequestInfo();
        $sharereq->sharerequestName = $array['clientName'];
        $sharereq->sharerequestedName = $array['contactName'];
        $sharereq->save();

        $result = $client->push()
            ->setPlatform('android')
            ->addAlias($array['contactName'])
            ->addAndroidNotification($array['clientName'] . '请求共享位置', null, 1, array('type' => 'requestShareLoc', "friend_name" => $user->clientName, 'friend_nickname' => $user->nick_name, 'pic_url' => $user->pic_url, 'friend_sex' => $user->sex, 'friend_address' => $user->address, 'friend_signature' => $user->signature))
            ->setOptions(100000, 3600, null, false)
            ->send();
        error_log('服务器发送 shareLocation成功');
        DB::update('update friend_relations set sharestatusme = TRUE where userName = ? and friendName = ?', [$array['clientName'], $array['contactName']]);
        DB::update('update friend_relations set sharestatusfr = TRUE where userName = ? and friendName = ?', [$array['contactName'], $array['clientName']]);
        return response()->json(['shareLoc' => true, 'message' => '已发送位置共享请求']);
    }

    public function getContactLocation(Request $request)
    {
        $jsonstr = $request->input('getContactLoc');
        $array = json_decode($jsonstr, true);
        $contactName = $array['getLocFriendName'];
        return response()->json(array(
            'getContactLoc' => true,
            'message' => '返回位置',
            'location_latitude' => Cache::has('location_latitude:' . $contactName) ? (Cache::get('location_latitude:' . $contactName)) : 0,
            'location_lontitude' => Cache::has('location_lontitude:' . $contactName) ? (Cache::get('location_lontitude:' . $contactName)) : 0,
        ));
    }

    public function updateUserLoc(Request $request)
    {
        if ($request->ajax()) {
            $nameStr = $request->input('name');
            return response()->json(array(
                'status' => 1,
                'location_latitude' => Cache::has('location_latitude:' . $nameStr) ? (Cache::get('location_latitude:' . $nameStr)) : 0,
                'location_lontitude' => Cache::has('location_lontitude:' . $nameStr) ? (Cache::get('location_lontitude:' . $nameStr)) : 0,
            ));
        }
    }

    /**
     * 向外部发送 这个用户的实时运动轨迹
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function showSingleUserLoc(Request $request)
    {
        if ($request->ajax()) {
            $nameStr = $request->input('name');
            $user = ClientUser::where('clientName', $nameStr)->first();
            if($user->tempshare){
                return response()->json(array(
                    'status' => 1,
                    'location_latitude' => Cache::has('location_latitude:' . $nameStr) ? (Cache::get('location_latitude:' . $nameStr)) : 0,
                    'location_lontitude' => Cache::has('location_lontitude:' . $nameStr) ? (Cache::get('location_lontitude:' . $nameStr)) : 0,
                    'canshare' => true
                ));
            }else{
                return response()->json(array(
                    'status' => 1,
                    'canshare' => false
                ));
            }
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
        DB::update('update friend_relations set sharestatusme = TRUE where userName = ? and friendName = ?', [$array['clientName'], $array['agreeShareUserName']]);
        DB::update('update friend_relations set sharestatusfr = TRUE where userName = ? and friendName = ?', [$array['agreeShareUserName'], $array['clientName']]);
        error_log('服务器发送 agreeShareLocation成功');
        return response()->json(['agreeShareLoc' => true, 'message' => '已发送同意位置共享请求']);
    }

    public function closeShareLocation(Request $request)
    {
        $jsonstr = $request->input('closeShareLocation');
        $array = json_decode($jsonstr, true);
        $clientName = $array['clientName'];
        $contactName = $array['contactName'];
        DB::update('update friend_relations set sharestatusme = FALSE where userName = ? and friendName = ?', [$clientName, $contactName]);
        DB::update('update friend_relations set sharestatusfr = FALSE where userName = ? and friendName = ?', [$contactName, $clientName]);
        error_log('服务器关闭共享:'.$array['clientName'].">".$array['contactName']);
        return response()->json(['agreeCloseShareLoc' => true, 'message' => '已同意关闭位置共享请求']);
    }

    public function showAllShareUser(Request $request)
    {
        if($request->ajax()){
            $results = DB::select('select DISTINCT userName from friend_relations where sharestatusme = ?', [true]);
            foreach($results as $res){
                error_log('showAllShareUser:'.$res->userName);
            }
            return response()->json([
                'status' => 1,
                'user' => $results,
                'count' => count($results)
            ]);
        }
    }

    public function find(Request $request)
    {

        if ($request->ajax()) {
            $nameStr = $request->input('name');
            $id = $request->input('id');
            if(is_null(User::find($id)->role) || User::find($id)->role==""){
                return response()->json(array(
                    'status' => 1,
                    'msg' => '<p>您的访问权限不够</p>',
                ));
            }

//            error_log($id);
//            temps是一个集合
            $temps = ClientUser::where('clientName', 'like', $nameStr . '%')->get();
            $ttt = count($temps);
            if (count($temps) == 0) {
                return Redirect::back()->withInput()->withErrors('查询失败!');
            } else {
                $isRoot = false;
                if("root" == User::find($id)->role){
                    $isRoot = true;
                }

                $tabstr = "<table class='table'>";
                $tabstr .= "<thead align=\"center\">
                            <tr>
                                <td>客户ID</td>
                                <td>客户名</td>
                                <td>昵称</td>
                                <td>性别</td>
                                <td>地区</td>
                                <td>操作</td>
                            </tr>
                        </thead><tbody align=\"center\">";
                foreach ($temps as $temp) {
                    $id = $temp->id;
                    $clientName = $temp->clientName;
                    $nickName = $temp->nick_name;
                    $sex = $temp->sex;
                    $address = $temp->address;
                    $check = "<a href='#user_info' data-name =$temp->clientName data-toggle=\"modal\" class=\"btn btn-primary btn-large\" onclick='getUserDetail(this)'>查看</a>";
                    if($isRoot){
                        $modify = "<a href='#' data-id=$id data-name =$temp->clientName data-toggle=\"modal\" class=\"btn btn-success btn-large\" onclick='modifyUserInfo(this)'>修改</a>";
                        $delete = "<a href='#user_del' data-id=$id data-toggle=\"modal\" class=\"btn btn-danger btn-large\" onclick='deleteUserDetail(this)'>删除</a>";  //onclick='deleteUserInfo(this)'
                    }else{
                        $modify = "";
                        $delete="";
                    }
                    $tabstr .= "<tr><td>" . $id . "</td><td >" . $clientName . "</td><td>" . $nickName . "</td><td>" . $sex . "</td><td>" . $address . "</td><td>" . $check . $modify . $delete . "</td></tr>";
                }
                $tabstr .= "</tbody></table>";
                return response()->json(array(
                    'status' => 1,
                    'msg' => $tabstr,
                ));
            }
        }

    }

    public function createUser(Request $request)
    {
        if ($request->ajax()) {
            $id = $request->input('id');
            if(is_null(User::find($id)->role) || User::find($id)->role==""){
                return response()->json(array(
                    'status' => 1,
                    'msg' => '<p>您的访问权限不够</p>',
                ));
            }

            $creatStr = "<form class=\"form-horizontal\" role=\"form\">
               <div id='inputNameDiv' class=\"form-group\">
                <label for=\"name\">用户名</label>
                <input type=\"text\" class=\"form-control\" id='inputName' placeholder=\"请输入用户名\" onfocus='inputNameFocus()'>
                <span class=\"help-block\" id='hp_name'></span>
              </div>
              <div id='inputPasswordDiv' class=\"form-group\">
                 <label for=\"name\">密码</label>
                <input type=\"password\" class=\"form-control\" id='inputPassword' placeholder=\"请输入密码\" onfocus='inputPasswordFocus()'>
                <span class=\"help-block\" id='hp_passeword'></span>
              </div>
                  <div class=\"form-group\">
                  <table>
                  <tbody>
                    <tr><td> <label  for=\"name\">头像</label></td>
                    <td style='padding-left: 100px'><img  src='USER_202cb962ac59075b964b07152d234b70.jpg'  width='100px' height='100px' class='img-circle'></td>
                    <td style='padding-left: 50px'><input type=\"file\"  id=\"inputfile\"></td></tr>
                    </tbody>
                </table>
                  </div>
              <div class=\"form-group\">
                 <label for=\"name\">昵称</label>
                <input type=\"text\" class=\"form-control\" id='inputNickname' placeholder=\"请输入昵称\">
              </div>
              <div class=\"form-group\">
                 <label for=\"name\">性别</label>
                <label class=\"checkbox-inline\">
                      <input type=\"radio\" name=\"optionsRadiosinline\" id=\"inputSexMan\"
                         value=\"男\" checked>男
                   </label>
                   <label class=\"checkbox-inline\">
                      <input type=\"radio\" name=\"optionsRadiosinline\" id=\"inputSexWoman\"
                         value=\"女\">女
                  </label>
              </div>
              <div class=\"form-group\">
                 <label for=\"name\">地区</label>
                          <select id = 'inputAddress' class=\"form-control\">
                             <option>安徽</option>
                             <option>澳门</option>
                             <option>北京</option>
                             <option>福建</option>
                             <option>广东</option>
                             <option>甘肃</option>
                             <option>广西</option>
                             <option>贵州</option>
                             <option>河北</option>
                             <option>湖北</option>
                             <option>黑龙江</option>
                             <option>海南</option>
                             <option>河南</option>
                             <option>湖南</option>
                             <option>吉林</option>
                             <option>江苏</option>
                             <option>江西</option>
                             <option>辽宁</option>
                             <option>青海</option>
                             <option>四川</option>
                             <option>山东</option>
                             <option>上海</option>
                             <option>陕西</option>
                             <option>山西</option>
                             <option>天津</option>
                             <option>台湾</option>
                             <option>西藏</option>
                             <option>香港</option>
                             <option>新疆</option>
                             <option>云南</option>
                             <option>浙江</option>
                          </select>
              </div>
              <div class=\"form-group\">
                 <label for=\"name\">个性签名</label>
                <input type=\"text\" class=\"form-control\" id='inputSignature' placeholder=\"请输入个性签名\">
              </div>
            </form>";
            $creatStr .= "<p style='float: right'><a href='#' class=\"btn btn-primary btn-large\" onclick='findUser()'>取消</a>";
            $creatStr .= "<a href='#' class=\"btn btn-success\" onclick='saveCreateUser()'>创建</a></p>";
            return response()->json([
                'status' => 1,
                'msg' => $creatStr
            ]);
        }
    }

    public function saveCreateUser(Request $request)
    {
        if ($request->ajax()) {
            if (count(ClientUser::where('clientName', $request->input('inputName'))->get()) == 0) {


                $clientUser = new ClientUser();
                $clientUser->clientName = $request->input('inputName');
                $clientUser->password = $request->input('inputPassword');
                $clientUser->pic_url = $request->input('inputIcon');
                $clientUser->nick_name = $request->input('inputNickname');
                $clientUser->sex = $request->input('inputSex');
                $clientUser->address = $request->input('inputAddress');
                $clientUser->signature = $request->input('inputSignature');
                $clientUser->save();
                return response()->json([
                    'status' => 1,
                    'msg' => '成功',
                    'contained' => false
                ]);
            } else {
                return response()->json([
                    'status' => 1,
                    'msg' => '成功',
                    'contained' => true
                ]);
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

    public function modifyUserInfo(Request $request)
    {
        if ($request->ajax()) {
            $idStr = $request->input('id');
            $user = ClientUser::where('id', $idStr)->first();
            if (!$user->exists) {
                return Redirect::back()->withInput()->withErrors('查询失败!');
            } else {
                $tabstr = "<table class='table'>";
                $tabstr .= "<thead align=\"center\">
                            <tr>
                                <td>客户信息</td>
                                <td>内容</td>
                                <td>新的内容</td>
                            </tr>
                        </thead><tbody align=\"center\">";
                $pic = '/USER_' . md5($user->clientName) . 'jpg';
                $tabstr .= "<tr><td>用户名:</td><td>" . $user->clientName . "</td><td></td></tr>";
                $tabstr .= "<tr><td>用户头像:</td><td>" . "<img src=$pic class='img-circle'>" . "</td><td>
                <div class=\"form-group\">
                      <input type=\"file\" id=\"inputfile\">
                  </div></td></tr>";
                $tabstr .= "<tr><td>用户昵称:</td><td>" . $user->nick_name . "</td><td><form role='form'><div class='form-group'><input type='text' id='new_nickname' class='form-control' placeholder='新昵称'></div></form></td></tr>";
                $tabstr .= "<tr><td>用户性别:</td><td>" . $user->sex . "</td><td>
                        <label class=\"checkbox-inline\">
                      <input type=\"radio\" name=\"optionsRadiosinline\" id=\"optionsRadiosMan\"
                         value=\"男\" checked>男
                   </label>
                   <label class=\"checkbox-inline\">
                      <input type=\"radio\" name=\"optionsRadiosinline\" id=\"optionsRadiosWoman\"
                         value=\"女\">女
                   </label></td></tr>";
                $tabstr .= "<tr><td>用户地区:</td><td>" . $user->address . "</td><td><form role=\"form\">
                       <div class=\"form-group\">
                          <select id = 'new_address' class=\"form-control\">
                             <option>安徽</option>
                             <option>澳门</option>
                             <option>北京</option>
                             <option>福建</option>
                             <option>广东</option>
                             <option>甘肃</option>
                             <option>广西</option>
                             <option>贵州</option>
                             <option>河北</option>
                             <option>湖北</option>
                             <option>黑龙江</option>
                             <option>海南</option>
                             <option>河南</option>
                             <option>湖南</option>
                             <option>吉林</option>
                             <option>江苏</option>
                             <option>江西</option>
                             <option>辽宁</option>
                             <option>青海</option>
                             <option>四川</option>
                             <option>山东</option>
                             <option>上海</option>
                             <option>陕西</option>
                             <option>山西</option>
                             <option>天津</option>
                             <option>台湾</option>
                             <option>西藏</option>
                             <option>香港</option>
                             <option>新疆</option>
                             <option>云南</option>
                             <option>浙江</option>
                          </select>
                       </div>
                    </form></td></tr>";
                $tabstr .= "<tr><td>用户签名:</td><td>" . $user->signature . "</td><td><form role='form'><div class='form-group'><input type='text' id='new_signature' class='form-control' placeholder='新签名'></div></form></td></tr>";
                $tabstr .= "</tbody></table>";
                $tabstr .= "<p style='float: right'><a href='#' data-toggle=\"modal\" class=\"btn btn-primary btn-large\" onclick='findUser()'>取消</a>";
                $tabstr .= "<a href='#' data-id =$idStr  class=\"btn btn-success\" onclick='saveUserInfo(this)'>确认</a></p>";
                return response()->json([
                    'status' => 1,
                    'msg' => $tabstr
                ]);
            }
        }
    }

    public function saveUserInfo(Request $request)
    {
        if ($request->ajax()) {
            $idStr = $request->input('id');
            $user = ClientUser::where('id', $idStr)->first();
            if (!$user->exists) {
                return Redirect::back()->withInput()->withErrors('查询失败!');
            } else {
                if (strlen($request->input('new_nickname')) != 0) {
                    $user->nick_name = $request->input('new_nickname');
                }
                $user->sex = $request->input('new_sex');
                $user->address = $request->input('new_address');
                if (strlen($request->input('new_signature')) != 0) {
                    $user->signature = $request->input('new_signature');
                }
                $user->save();
                return response()->json([
                    'status' => 1,
                    'msg' => 'OK'
                ]);
            }
        }
    }

    public function deleteUserInfo(Request $request)
    {
        if ($request->ajax()) {
            $idStr = $request->input('id');
            $user = ClientUser::where('id', $idStr)->first();
            if (!$user->exists) {
                return Redirect::back()->withInput()->withErrors('查询失败!');
            } else {
                $user->delete();
                return response()->json([
                    'status' => 1,
                    'msg' => 'OK'
                ]);
            }
        }
    }

    public function feedbackAll(Request $request)
    {
        if ($request->ajax()) {
//            temps是一个集合
            $id = $request->input('id');
            if(is_null(User::find($id)->role) || User::find($id)->role==""){
                return response()->json(array(
                    'status' => 1,
                    'msg' => '<p>您的访问权限不够</p>',
                ));
            }
            $temps = Feedback::all();
            $ttt = count($temps);
            if (count($temps) <= 0) {
                return response()->json(array(
                    'status' => 1,
                    'msg' => '<p>暂无用户反馈</p>',
                ));
            } else {
                $isRoot = false;
                if("root" == User::find($id)->role){
                    $isRoot = true;
                }
                $tabstr = "<table class='table'>";
                $tabstr .= "<thead align=\"center\">
                            <tr>
                                <td>反馈ID</td>
                                <td>客户名</td>
                                <td>邮箱</td>
                                <td>反馈摘要</td>
                                <td>处理状态</td>
                                <td>操作</td>
                            </tr>
                        </thead><tbody align=\"center\">";
                foreach ($temps as $temp) {
                    $id = $temp->id;
                    $clientName = $temp->clientName;
                    $email = $temp->email;
                    $status = $temp->status;
                    $feedbackDigest = self::cubstr($temp->content, 0, 30);
                    $check = "<a href='#feedback_info' data-id =$temp->id data-name =$temp->clientName data-toggle=\"modal\" class=\"btn btn-primary btn-large\" onclick='getFeedbackDetail(this)'>查看</a>";
                    $hand = "<a href='#' data-id =$temp->id data-name =$temp->clientName data-toggle=\"modal\" class=\"btn  btn-success btn-large\" onclick='handleFeedback(this)'>处理</a>";
                    if($isRoot){
                        $delete = "<a href='#user_del' data-id =$temp->id data-toggle=\"modal\" class=\"btn btn-danger btn-large\" onclick='deleteUserDetail(this)'>删除</a>";
                    }else{
                        $delete="";
                    }
                    $tabstr .= "<tr><td>" . $id . "</td><td >" . $clientName . "</td><td>" . $email . "</td><td>" . $feedbackDigest . "</td><td>" . $status . "</td><td>" . $check . $hand . $delete . "</td></tr>";
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
                $usershareattr = DB::select('select * from friend_relations where userName = ? and  sharestatusme = ?', [$nameStr, true]);
                if(count($usershareattr) <=0)
                {
                    error_log('无位置');
                    return response()->json(array(
                        'status' => 1,
                        'msg' => '此用户没有共享位置',
                        'isUser' => false
                    ));
                }
                error_log($nameStr.'>location_latitude:' . Cache::get('location_latitude:' . $nameStr));
                error_log($nameStr.'>location_lontitude:' . Cache::get('location_lontitude:' . $nameStr));
//                self::registerID($nameStr);

                return response()->json(array(
                    'status' => 1,
                    'msg' => '找到此人',
                    'location_latitude' => Cache::has('location_latitude:' . $nameStr) ? (Cache::get('location_latitude:' . $nameStr)) : 0,
                    'location_lontitude' => Cache::has('location_lontitude:' . $nameStr) ? (Cache::get('location_lontitude:' . $nameStr)) : 0,
                    'sex' => $temps[0]['sex'],
                    'address' => $temps[0]['address'],
                    'signature' => $temps[0]['signature'],
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
                $usershareattr = DB::select('select * from friend_relations where userName = ? and  sharestatusme = ?', [$nameStr, true]);
                if(count($usershareattr) <=0)
                {
                    error_log('无位置共享');
                    return response()->json(array(
                        'status' => 1,
                        'msg' => '此用户没有共享位置',
                        'isUser' => false
                    ));
                }
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
