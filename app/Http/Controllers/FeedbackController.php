<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ClientUser;
use App\Feedback;
use Mail;
use DB;

use App\Http\Requests;

class FeedbackController extends Controller
{
    public function handleFeedback(Request $request)
    {
        if ($request->ajax()) {
            $idStr = $request->input('id');
            $temps = Feedback::where('id', '=', $idStr)->get();
            if (count($temps) == 0) {
                return Redirect::back()->withInput()->withErrors('查询失败!');
            } else {
                //只循环一次
                $name = null;
                $email = null;
                $feedback_content = null;
                $created_at = null;
                foreach ($temps as $temp) {
                    $name = $temp->clientName;
                    $email = $temp->email;
                    $feedback_content = $temp->content;
                    $created_at = $temp->created_at;
                }
                $tabstr = "<p>用户名:" . $name . "</p><p>邮箱：" . $email . "</p><p>反馈内容：" . $feedback_content . "</p><p>反馈提交时间：" . $created_at . "</p>";
                $tabstr .= '<p><form role="form">
                              <div class="form-group">
                                <label for="name">反馈处理回复</label>
                                <textarea id = "lejianResponse" class="form-control" rows="10"></textarea>
                              </div>
                            </form></p>';
                $check = "<p style='float: right'><a href='#' data-id =$idStr data-name =$name data-toggle=\"modal\" class=\"btn btn-primary btn-large\" onclick='showAllFeedback()'>取消</a>";
                $hand = "<a href='#' data-id =$idStr data-mail=$email data-name =$name class=\"btn btn-success\" onclick='sendFeedbackMail(this)'>发送</a></p>";
                $tabstr .= $check;
                $tabstr .= $hand;

                return response()->json(array(
                    'status' => 1,
                    'msg' => $tabstr
                ));
            }

        }
    }

    public function sendFeedbackResponse(Request $request)
    {
        if($request->ajax()){

            $data = ['email'=>$request->input('mail'), 'name'=>$request->input('name')];
//            Mail::send(['text' => 'welocme'], $data, function($message) use($data)
//            {
//                $message->to($data['email'], $data['name'])->subject('乐见关于您反馈问题的回复');
//            });
            $bodyText = $request->input('lejianResponse');
            Mail::raw($bodyText,function ($message) use ($data){
                $message->to($data['email'], $data['name'])->subject('乐见关于您反馈问题的回复');
            });
            DB::table('feedbacks')->where('id', $request->input('id'))->update(['status' => '已处理']);
            return response()->json([
               'status' => 1,
                'msg' => 'OK'
            ]);
        }
    }

    public function deleteFeedback(Request $request)
    {
        if($request->ajax()){

            DB::table('feedbacks')->where('id', $request->input('id'))->delete();
            return response()->json([
                'status' => 1,
                'msg' => 'OK'
            ]);
        }
    }
}
