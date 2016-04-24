@extends('layouts.app')

@section('content')
    <div>
        <div class="col-md-2">
            <ul class="nav nav-pills nav-stacked">
                <li role="presentation"><a href="#">Home</a></li>
                <li role="presentation" class="active"><a href="{{url('/user_manage')}}">用户管理</a></li>
                <li role="presentation"><a href="{{url('/show_map')}}">地图显示</a></li>
                <li role="presentation"><a href="{{url('/statistic_data')}}">数据统计</a></li>
            </ul>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-md-10 ">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <div class="navbar-form">
                                用户管理
                                <div class="form-group" style="float: right">
                                    <input type="text" class="form-control" id="userName"
                                           placeholder="请输入用户名">
                                    <button type="submit" class="btn btn-default" onclick="findUser()">查找</button>
                                </div>
                            </div>
                        </div>

                        <div id="table" class="panel-body">

                        </div>

                        <div class="container">
                            {{--这个是展示用户详细信息的--}}
                            <div id="user_info" class="modal" style="display:none;">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <a class="close" data-dismiss="modal">X</a>
                                            <h3>用户详情</h3>
                                        </div>

                                        <div class="modal-body">
                                            <table class="table">
                                                <thead>
                                                <tr>
                                                    <td><div>用户：</div> </td>
                                                    <td><div id="user_name"></div> </td>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr>
                                                    <td>用户头像：</td>
                                                    <td ><div id="user_pic"></div></td>
                                                </tr>
                                                <tr>
                                                    <td>个性签名：</td>
                                                    <td><div id="user_signature"></div></td>
                                                </tr>
                                                <tr>
                                                    <td>创建时间：</td>
                                                    <td><div id="user_created_at"></div></td>
                                                </tr>
                                                <tr>
                                                    <td>更新时间：</td>
                                                    <td><div id="user_updated_at"></div></td>
                                                </tr>
                                                <tr>
                                                    <td>最近一次所在位置：</td>
                                                    <td></td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="modal-footer">
                                            <a href="#" class="btn btn-success">成功</a>
                                            <a href="#" class="btn" data-dismiss="modal">关闭</a>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>


                    </div>
                </div>
            </div>


        </div>
    </div>
@endsection


<script type="text/javascript">

    function findUser() {

        $.ajax({
            type: 'post',
            dataType: 'json',
            url: 'userFind',
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

    function getUserDetail(e){
        $.ajax({
            type: 'post',
            url: 'userDetail',
            data: {name: e.getAttribute("data-name")},
            success: function (data) {
                $("#user_name").html(e.getAttribute("data-name"));
                $("#user_pic").html(data.pic_url);
                $("#user_signature").html(data.signature);
                $("#user_created_at").html(data.created_at);
                $("#user_updated_at").html(data.updated_at);
            },
            error: function (xhr, type) {
                $("#user_name").html(e.getAttribute("data-name"));
                $("#user_pic").html('无');
                $("#user_signature").html('无');
                $("#user_created_at").html('无');
                $("#user_updated_at").html('无');
            }
        });
    }

</script>
