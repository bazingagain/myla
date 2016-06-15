@extends('layouts.app')

@section('content')
    <div>
        <div class="col-md-2">
            <ul class="nav nav-pills nav-stacked">
                <li role="presentation"><a href="#">Home</a></li>
                <li role="presentation"><a href="{{url('/houtai_manager')}}">后台用户管理</a></li>
                <li role="presentation"><a href="{{url('/user_manage')}}">前台用户管理</a></li>
                <li role="presentation"><a href="{{url('/show_map')}}">地图显示</a></li>
                <li role="presentation"><a href="{{url('/statistic_data')}}">数据统计</a></li>
                <li role="presentation"><a href="{{url('/show_feedback')}}">用户反馈</a></li>
                {{--<li role="presentation" class="active"><a href="{{url('/func_test')}}">功能测试</a></li>--}}
            </ul>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-md-10 ">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <div class="navbar-form">
                               用户反馈
                                <div class="form-group" style="float: right">
                                    <input type="text" class="form-control" id="userName"
                                           placeholder="请输入用户名">
                                    <button type="submit" class="btn btn-default" onclick="sendJPush()">发送JPush</button>
                                </div>
                            </div>
                        </div>

                        <div id="table" class="panel-body">

                        </div>

                        <div class="container">
                            {{--这个是展示用户详细信息的--}}
                        </div>


                    </div>
                </div>
            </div>


        </div>
    </div>
@endsection


<script type="text/javascript">

    function sendJPush(){

        $.ajax({
            type: 'post',
            dataType: 'json',
            url: 'sendPush',
            data: {name: $("#userName").val()},
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            },
            success: function (data) {
                $("#table").html(data.msg);
            },
            error: function (xhr, type) {
                $("#table").html("<p>没有此人</p>");
            }
        });
    }

</script>
