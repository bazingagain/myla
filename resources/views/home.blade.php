@extends('layouts.app')

@section('content')
    <div>
        <div class="col-md-2">
            <ul class="nav nav-pills nav-stacked" >
                <li role="presentation" class="active"><a href="#">Home</a></li>
                <li role="presentation"><a href="{{url('/user_manage')}}">用户管理</a></li>
                <li role="presentation"><a href="{{url('/show_map')}}">地图显示</a></li>
                <li role="presentation"><a href="{{url('/static_data')}}">数据统计</a></li>
                <li role="presentation"><a href="{{url('/show_feedback')}}">用户反馈</a></li>
                <li role="presentation"><a href="{{url('/func_test')}}">用户反馈</a></li>
            </ul>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-md-10 ">
                    <div class="panel panel-default">
                        <div class="panel-heading">Home</div>

                        <div class="panel-body">
                            登录成功！
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
