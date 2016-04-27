@extends('layouts.app')

@section('content')
    <div>
        <div class="col-md-2">
            <ul class="nav nav-pills nav-stacked">
                <li role="presentation"><a href="#">Home</a></li>
                <li role="presentation"><a href="{{url('/user_manage')}}">用户管理</a></li>
                <li role="presentation"><a href="{{url('/show_map')}}">地图显示</a></li>
                <li role="presentation"><a href="{{url('/statistic_data')}}">数据统计</a></li>
                <li role="presentation" class="active"><a href="{{url('/show_feedback')}}">用户反馈</a></li>
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
                                    <button type="button" class="btn btn-default" onclick="showAllFeedback()">所有反馈</button>
                                </div>
                            </div>
                        </div>

                        <div id="table" class="panel-body">

                        </div>

                        <div class="container">
                            {{--这个是展示用户详细信息的--}}
                            <div id="feedback_info" class="modal" style="display:none;">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <a class="close" data-dismiss="modal">X</a>
                                            <h3>反馈详情</h3>
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
                                                    <td>反馈内容：</td>
                                                    <td><div id="feedback_content"></div></td>
                                                </tr>
                                                <tr>
                                                    <td>创建时间：</td>
                                                    <td><div id="feedback_created_at"></div></td>
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

    function showAllFeedback(){

        $.ajax({
            type: 'get',
            url: 'feedbackAll',
            success: function (data) {
                $("#table").html(data.msg);
            },
            error: function (xhr, type) {
                $("#table").html("<p>暂无反馈</p>");
            }
        });
    }

    function getFeedbackDetail(e){
        $.ajax({
            type: 'post',
            url: 'feedbackDetail',
            data: {id: e.getAttribute("data-id")},
            success: function (data) {
                $("#user_name").html(e.getAttribute("data-name"));
                $("#feedback_content").html(data.feedback_content);
                $("#feedback_created_at").html(data.created_at);
            },
            error: function (xhr, type) {
                $("#user_name").html(e.getAttribute("data-name"));
                $("#feedback_content").html('无');
                $("#feedback_created_at").html('无');
            }
        });
    }

</script>
