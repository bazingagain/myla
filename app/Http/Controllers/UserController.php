<?php

namespace App\Http\Controllers;

use App\User;
use DB;
use Auth;
use Redis;
use Cache;
use Storage;
//use Illuminate\Support\Facades\DB;
use App\ClientUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function find(Request $request)
    {
            $nameStr = $request->input('name');
            $id = $request->input('id');
        if(is_null(User::find($id)->role) || User::find($id)->role==""){
            return response()->json(array(
                'status' => 1,
                'msg' => '<p>您的访问权限不够</p>',
            ));
        }

//            temps是一个集合
            $temps = User::where('name', 'like', $nameStr . '%')->get();
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
                                <td>后台用户ID</td>
                                <td>用户名</td>
                                <td>角色</td>";
                if($isRoot){
                    $tabstr .="<td>操作</td>";
                }
                $tabstr .="</tr></thead><tbody align=\"center\">";
                foreach ($temps as $temp) {
                    $id = $temp->id;
                    $name = $temp->name;
                    $role = $temp->role;
                    //是否是root用户
                    if("root" == $role){
                        $role = "管理员";
                    }else if("normal" == $role){
                        $role = "普通用户";
                    }else{
                        $role="注册用户";
                    }
                    $modify = "<a href='#' data-id=$id data-name =$temp->name  class=\"btn btn-success btn-large\" onclick='modifyUserInfo(this)'>修改</a>";
                    $delete = "<a href='#user_del' data-toggle=\"modal\" data-id=$id  class=\"btn btn-danger btn-large\" onclick='deleteUserDetail(this)'>删除</a>";
                    if($isRoot){
                        $tabstr .= "<tr><td>" . $id . "</td><td>" . $name . "</td><td>" . $role . "</td><td>" . $modify . $delete . "</td></tr>";
                    }else{
                        $tabstr .= "<tr><td>" . $id . "</td><td>" . $name . "</td><td>" .$role."</td></tr>";
                    }
                }
                $tabstr .= "</tbody></table>";
                    return response()->json(array(
                        'status' => 1,
                        'msg' => $tabstr,
                    ));
                }
    }

    public function createUser(Request $request)
    {
        if ($request->ajax()) {
            $creatStr = "<form class=\"form-horizontal\" role=\"form\">
               <div id='inputNameDiv' class=\"form-group\">
                <label for=\"name\">用户名</label>
                <input type=\"text\" class=\"form-control\" id='inputName' placeholder=\"请输入用户名\" onfocus='inputNameFocus()'>
                <span class=\"help-block\" id='hp_name'></span>
              </div>
              <div id='inputPasswordDiv' class=\"form-group\">
                 <label for=\"name\">密码</label>
                <input type=\"password\" class=\"form-control\" id='inputPassword' placeholder=\"请输入密码\" onfocus='inputPasswordFocus()'>
                <span class=\"help-block\" id='hp_password'></span>
              </div>
              <div class=\"form-group\">
                 <label for=\"name\">用户权限</label>
                  <select id = 'inputRole' class=\"form-control\">
                     <option value=''>注册用户</option>
                     <option value='normal'>普通用户</option>
                     <option value='root'>管理员</option>
                  </select>
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
            error_log($request->input('id'));
            error_log($request->input('inputName'));
            error_log($request->input('inputPassword'));
            error_log($request->input('inputRole'));
            error_log(count(User::where('name', "".$request->input('inputName'))->get()));
            if (count(User::where('name', $request->input('inputName'))->get()) <= 0) {
                DB::table('users')->insert(
                    ['name' =>$request->input('inputName'),
                        'password' => md5($request->input('inputPassword')),
                            'role'=>$request->input('inputRole')]);
//                $user = new User();
//                $user->name = 'xiaoxiao';
//                $user->password = md5('xiaoxiao');
//                $user->role = 'normal';
//                $user->save();
                error_log('cunshu');
                return response()->json([
                    'status' => 1,
                    'msg' => '成功',
                    'contained' => false
                ]);
            } else {
                error_log('包含');
                return response()->json([
                    'status' => 1,
                    'msg' => '成功',
                    'contained' => true
                ]);
            }
    }


    public function modifyUserInfo(Request $request)
    {
        if ($request->ajax()) {
            $idStr = $request->input('id');
            error_log($idStr);
            $user = User::find($idStr);
            if (!$user->exists) {
                return Redirect::back()->withInput()->withErrors('查询失败!');
            } else {
                $tabstr = "<table class='table'>";
                $tabstr .= "<thead align=\"center\">
                            <tr>
                                <td>后台用户信息</td>
                                <td>内容</td>
                                <td>新的内容</td>
                            </tr>
                        </thead><tbody align=\"center\">";
                $tabstr .= "<tr><td>用户名:</td><td>" . $user->name . "</td><td></td></tr>";
                $role = '';
                switch($user->role){
                    case 'root': $role = '管理员';break;
                    case 'normal': $role = '普通用户';break;
                    default:$role = '注册用户';break;
                }
                $tabstr .= "<tr><td>用户权限:</td><td>" . $role . "</td><td><form role=\"form\">
                       <div class=\"form-group\">
                          <select id = 'new_role' class=\"form-control\">
                             <option value=''>注册用户</option>
                             <option value='normal'>普通用户</option>
                             <option value='root'>管理员</option>
                          </select>
                       </div>
                    </form></td></tr>";
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
            $user = User::find($idStr);
            if (!$user->exists) {
                return Redirect::back()->withInput()->withErrors('查询失败!');
            } else {
                    $user->role = $request->input('new_role');
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
            $user = User::where('id', $idStr)->first();
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
}
