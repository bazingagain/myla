<?php

namespace App\Http\Controllers;

use App\AddRequestInfo;
use App\ClientUser;
use App\ShareRequestInfo;
use Illuminate\Http\Request;
use DB;
use DateTime;

use App\Http\Requests;

class StatisticController extends Controller
{
    public function getUserNumInfoYear(Request $request)
    {
        if($request->ajax())
        {
            $counts = ClientUser::all();
            $now=date('Y-m',time());
            $nowMonth = date('m', time());
            $time = [];
            for($i = 11; $i >=0; $i--){
                //月份要减1
                array_push($time, date('Y-m', strtotime((-($i))." month")));
            }

            $data = [0,0,0,0,0,0,0,0,0,0,0,0];
            foreach($counts as $count)
            {
                $datedd =date('Y-m', strtotime($count->created_at));

                if($datedd >= date('Y-m', strtotime("-11 month"))){
                    $tmp =date('m', strtotime($count->created_at));
                    error_log(($nowMonth + $tmp)%11 +1);
                    $data[($nowMonth + $tmp)%11 +1] +=1;
                }
            }
            return response()->json([
                'status' => 1,
                'timeStamp' => $time,
                'msg1' => $data,
            ]);
        }
    }
    public function getUserNumInfoThirthday(Request $request)
    {
        if($request->ajax())
        {
            $counts = ClientUser::all();
            $now=date('Y-m-d',time());

            $time = [];
            for($i = 29; $i >=0; $i--){
                array_push($time, date('y-m-d', strtotime(-($i)." day")));
            }
            $data = [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0];
            foreach($counts as $count)
            {
                $datedd =date('Y-m-d', strtotime($count->created_at));
                $dif = (strtotime($now) - strtotime($datedd))/86400; //计算天数差
                if($dif < 30){
                    $data[29 - $dif] +=1;
                }
            }
            return response()->json([
                'status' => 1,
                'timeStamp' => $time,
                'msg1' => $data
            ]);
        }
    }
    public function getUserNumInfoSevenday(Request $request)
    {
        if($request->ajax())
        {
            $counts = ClientUser::all();
            $now=date('Y-m-d',time());
            $time = [];
            for($i = 6; $i >=0; $i--){
                array_push($time, date('y-m-d', strtotime(-($i)." day")));
            }
            $data = [0,0,0,0,0,0,0];
            foreach($counts as $count)
            {
                $datedd =date('Y-m-d', strtotime($count->created_at));
                $dif = (strtotime($now) - strtotime($datedd))/86400; //计算天数差
                if($dif < 7){
                    $data[6 - $dif] +=1;
                }
            }
            return response()->json([
                'status' => 1,
                'timeStamp' => $time,
                'msg1' => $data,
            ]);
        }
    }


    public function getMessageInfoYear(Request $request)
    {
        if($request->ajax())
        {
            $counts = ClientUser::all();
            $now=date('Y-m',time());
            $nowMonth = date('m', time());
            $time = [];
            for($i = 11; $i >=0; $i--){
                //月份要减1
                array_push($time, date('Y-m', strtotime((-($i))." month")));
            }

            $data = [0,0,0,0,0,0,0,0,0,0,0,0];
            foreach($counts as $count)
            {
                $datedd =date('Y-m', strtotime($count->created_at));

                if($datedd >= date('Y-m', strtotime("-11 month"))){
                    $tmp =date('m', strtotime($count->created_at));
                    error_log(($nowMonth + $tmp)%11 +1);
                    $data[($nowMonth + $tmp)%11 +1] +=1;
                }
            }
            $addNumData = [0,0,0,0,0,0,0,0,0,0,0,0];
            $addCounts = AddRequestInfo::all();
            foreach($addCounts as $addcount)
            {
                $datedd =date('Y-m-d', strtotime($addcount->created_at));
                $dif = (strtotime($now) - strtotime($datedd))/86400/30; //计算月份差
                if($dif < 12){
                    $addNumData[11 - $dif] +=1;
                }
            }
            $shareNumData = [0,0,0,0,0,0,0,0,0,0,0,0];
            $shareCounts = ShareRequestInfo::all();
            foreach($shareCounts as $sharecount)
            {
                $datedd =date('Y-m-d', strtotime($sharecount->created_at));
                $dif = (strtotime($now) - strtotime($datedd))/86400/30; //计算月份差
                if($dif < 12){
                    $shareNumData[11 - $dif] +=1;
                }
            }

            return response()->json([
                'status' => 1,
                'timeStamp' => $time,
                'msg1' => $data,
                'msg2' => $addNumData,
                'msg3' => $shareNumData
            ]);
        }
    }
    public function getMessageInfoThirthday(Request $request)
    {
        if($request->ajax())
        {
            $counts = ClientUser::all();
            $now=date('Y-m-d',time());

            $time = [];
            for($i = 29; $i >=0; $i--){
                array_push($time, date('y-m-d', strtotime(-($i)." day")));
            }
            $data = [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0];
            foreach($counts as $count)
            {
                $datedd =date('Y-m-d', strtotime($count->created_at));
                $dif = (strtotime($now) - strtotime($datedd))/86400; //计算天数差
                if($dif < 30){
                    $data[29 - $dif] +=1;
                }
            }

            $addNumData = [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0];
            $addCounts = AddRequestInfo::all();
            foreach($addCounts as $addcount)
            {
                $datedd =date('Y-m-d', strtotime($addcount->created_at));
                $dif = (strtotime($now) - strtotime($datedd))/86400;
                if($dif < 30){
                    $addNumData[29 - $dif] +=1;
                }
            }
            $shareNumData = [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0];
            $shareCounts = ShareRequestInfo::all();
            foreach($shareCounts as $sharecount)
            {
                $datedd =date('Y-m-d', strtotime($sharecount->created_at));
                $dif = (strtotime($now) - strtotime($datedd))/86400;
                if($dif < 30){
                    $shareNumData[29 - $dif] +=1;
                }
            }

            return response()->json([
                'status' => 1,
                'timeStamp' => $time,
                'msg1' => $data,
                'msg2' => $addNumData,
                'msg3' => $shareNumData
            ]);
        }
    }
    public function getMessageInfoSevenday(Request $request)
    {
        if($request->ajax()){
            $counts = ClientUser::all();
            $now=date('Y-m-d',time());
            $time = [];
            for($i = 6; $i >=0; $i--){
                array_push($time, date('y-m-d', strtotime(-($i)." day")));
            }
            $data = [0,0,0,0,0,0,0];
            foreach($counts as $count)
            {
                $datedd =date('Y-m-d', strtotime($count->created_at));
                $dif = (strtotime($now) - strtotime($datedd))/86400; //计算天数差
                if($dif < 7){
                    $data[6 - $dif] +=1;
                }
            }
            $addNumData = [0,0,0,0,0,0,0];
            $addCounts = AddRequestInfo::all();
            foreach($addCounts as $addcount)
            {
                $datedd =date('Y-m-d', strtotime($addcount->created_at));
                $dif = (strtotime($now) - strtotime($datedd))/86400;
                if($dif < 7){
                    $addNumData[6 - $dif] +=1;
                }
            }
            $shareNumData = [0,0,0,0,0,0,0];
            $shareCounts = ShareRequestInfo::all();
            foreach($shareCounts as $sharecount)
            {
                $datedd =date('Y-m-d', strtotime($sharecount->created_at));
                $dif = (strtotime($now) - strtotime($datedd))/86400;
                if($dif < 7){
                    $shareNumData[6 - $dif] +=1;
                }
            }

            return response()->json([
                'status' => 1,
                'timeStamp' => $time,
                'msg1' => $data,
                'msg2' => $addNumData,
                'msg3' => $shareNumData
            ]);
        }
    }



    public function getUserAddressInfo(Request $request)
    {
        if ($request->ajax()) {
           $counts =  DB::table('client_users')->select(DB::raw('count(*) as user_count, address'))->groupBy('address')->get();
            $data = [];
            foreach($counts as $count)
            {
                $item = ['value' => $count->user_count, 'name' => ''.$count->address];
                $data[] = $item;
            }
            return response()->json([
                'status' => 1,
                'msg1' => $data,
                'msg2' => $data
            ]);
        }
    }
    public function getUserSexInfo(Request $request)
    {
        if ($request->ajax()) {
           $countsMan =  DB::table('client_users')->select(DB::raw('count(*) as user_count, address'))->where('sex', '男')->groupBy('address')->get();
            $dataMan = [];
            foreach($countsMan as $countm)
            {
                $item = ['name' => $countm->address, 'value' => $countm->user_count,];
                $dataMan[] = $item;
            }
           $countsWoman =  DB::table('client_users')->select(DB::raw('count(*) as user_count, address'))->where('sex', '女')->groupBy('address')->get();
            $dataWoman = [];
            foreach($countsWoman as $countw)
            {
                $item = ['name' => $countw->address, 'value' => $countw->user_count,];
                $dataWoman[] = $item;
            }
            return response()->json([
                'status' => 1,
                'msg1' => $dataMan,
                'msg2' => $dataWoman
            ]);
        }
    }
}
