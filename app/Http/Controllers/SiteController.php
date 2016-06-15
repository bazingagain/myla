<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class SiteController extends Controller
{
    public function about()
    {
        return view('sites.about');
    }

    public function managerUser()
    {
//        $name = '<span style="color:red">jelly</span>';
//        return view('sites.manager_user', ['userName' => $name]);
        $userName = 'name1';
//        $people = ['he1', 'he2', 'h3'];
        $people = [];
        return view('sites.manager_user', compact('people'));
    }

    public function showMap()
    {
        return view('sites.show_map');
    }

    public function statisticData()
    {
        return view('sites.statistic_data');
    }

    public function showFeedback()
    {
        return view('sites.feedback');
    }

    public function showFunctest()
    {
        return view('sites.functest');
    }

    public function showSignalUserLoc($sharename){
        $name =$sharename;  //分享的用户名
        $time = 10;  //minute  可见时间
        return view('sites.showSignalUserLoc', compact('name', 'time'));
    }

    public function showTrack(){
        return view('sites.showtrace');
    }

    public function houtaiUser(){
        return view('sites.houtaiUser');
    }
}

