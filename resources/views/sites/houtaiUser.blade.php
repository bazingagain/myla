@extends('layouts.app')

@section('content')
    <div>
        <div class="col-md-2">
            <ul class="nav nav-pills nav-stacked">
                <li role="presentation"><a href="#">Home</a></li>
                <li role="presentation" class="active"><a href="{{url('/houtai_manager')}}">后台用户管理</a></li>
                <li role="presentation"><a href="{{url('/user_manage')}}">前台用户管理</a></li>
                <li role="presentation"><a href="{{url('/show_map')}}">地图显示</a></li>
                <li role="presentation"><a href="{{url('/statistic_data')}}">数据统计</a></li>
                <li role="presentation"><a href="{{url('/show_feedback')}}">用户反馈</a></li>
{{--                <li role="presentation" ><a href="{{url('/func_test')}}">功能测试</a></li>--}}
            </ul>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-md-10 ">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <div class="navbar-form">
                                后台用户管理
                                <div class="form-group" style="float: right">
                                    <input type="hidden" id="curUserId" value="{{Auth::user()->id}}">
                                    <input type="text" class="form-control" id="userName"
                                           placeholder="请输入用户名" onchange="findUser()" onfocus="changeWidth()" onblur="backWidth()">
                                    <button type="submit" class="btn btn-default" onclick="findUser()">查找</button>
                                    {{--@if("root" == Auth::user()->role)--}}
                                        {{--<button type="submit" class="btn btn-default" onclick="createUser()">新建</button>--}}
                                    {{--@endif--}}
                                </div>
                            </div>
                        </div>

                        <div id="table" class="panel-body">

                        </div>
                        <<div class="container">
                            <div id="user_del" class="modal" style="display:none;">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <a class="close" data-dismiss="modal">X</a>
                                            <h3>删除用户</h3>
                                        </div>

                                        <div class="modal-body">
                                            <p>您确认要删除此用户吗？确认会删除用户的一切信息，请谨慎操作</p>
                                        </div>
                                        <div class="modal-footer">
                                            <a href="#" id="delUser_id" data-dismiss="modal" data-id=""  onclick="deleteUserInfo(this)" class="btn btn-success">确认</a>
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

    function deleteUserDetail(e){
        $('#delUser_id').attr('data-id', e.getAttribute("data-id"));
        alert($('#delUser_id').getAttribute('data-id'));
    }

    function changeWidth()
    {
        $('#userName').animate({width:'+=100px'}, "slow");
    }
    function backWidth()
    {
        $('#userName').animate({width:'-=100px'}, "slow");
    }

    function findUser() {

        $.ajax({
            type: 'post',
            dataType: 'json',
            url: 'htuserFind',
            data: {name: $("#userName").val(), id:$("#curUserId").val()},
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
    function createUser()
    {
        $.ajax({
            type : 'post',
            url : 'htcreateUser',
            data: {id:$("#curUserId").val()},
            success : function(data){
                $("#table").html(data.msg);
            },
            error : function(data){

            }

        });
    }

    function showIcon()
    {
        $('#inputfile').val();
    }
    function saveCreateUser()
    {
        alert($("#curUserId").val());
        if($.trim($('#inputName').val()) == ''){
            $('#inputNameDiv').attr('class','form-group has-error');
            $('#hp_name').html('用户名不能为空');
            return ;
        }
        if($.trim($('#inputPassword').val()) == ''){
            $('#inputPasswordDiv').attr('class','form-group has-error');
            $('#hp_password').html('密码不能为空');
            return ;
        }
        $.ajax({
           type : 'post',
            url : 'htsaveCreateUser',
            data : {
                id:$("#curUserId").val(),
                inputName : $('#inputName').val(),
                inputPassword : $('#inputPassword').val(),
                inputRole : $('#inputRole').val()
            },
            success : function(data){
                if(data.contained){
                    $('#inputNameDiv').attr('class','form-group has-error');
                    $('#hp_name').html('此用户已存在');
                }else if(!data.contained){
                    findUser();
                }
            },
            error : function(data){
                $("#table").html('创建失败');
            }
        });
    }

    function inputNameFocus()
    {
        $('#inputNameDiv').attr('class','form-group');
        $('#hp_name').html('');
    }
    function inputPasswordFocus()
    {
        $('#inputPasswordDiv').attr('class','form-group');
        $('#hp_password').html('');
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

    function modifyUserInfo(e)
    {
        $.ajax({
            type: 'post',
            url: 'htmodifyUserInfo',
            data:{id: e.getAttribute("data-id")},
            success: function (data) {
                $("#table").html(data.msg);
            },
            error: function (xhr, type) {
            }
        });
    }

    function saveUserInfo(e)
    {

        $.ajax({
            type: 'post',
            url: 'htsaveUserInfo',
            data: {
                id: e.getAttribute("data-id"),
                new_role : $('#new_role').val(),
            },
            success: function (data) {
                findUser();
            },
            error: function (xhr, type) {
                $("#table").html('修改失败');
            }
        });
    }
    function deleteUserInfo(e)
    {
        $.ajax({
            type: 'post',
            url: 'htdeleteUserInfo',
            data: {
                id: e.getAttribute("data-id")
            },
            success: function (data) {
                findUser();
            },
            error: function (xhr, type) {
                $("#table").html('删除失败');
            }
        });
    }

</script>
