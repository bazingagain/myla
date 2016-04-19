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
                                            <h3>我是拟态框的头部</h3>
                                        </div>

                                        <div class="modal-body">
                                            <h4>我是拟态框的中间部分</h4>
                                            <p>我是描述部分</p>
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

@section('footer')
    @if(count($people)>0)
        <ul>
            @foreach($people as $item)
                <li>{{$item}}</li>
            @endforeach
        </ul>
    @endif
@endsection

<script type="text/javascript">
    function creatTable2() {
        var table = document.getElementById("table");
        var tabstr = "";
        tabstr += "<table class='table'>";
        for (var i = 0; i < 5; i++) {
            tabstr += "<tr>";
            for (var j = 0; j < 5; j++) {
                tabstr += "<td>heh</td>";
            }
            tabstr += "</tr>";
        }
        tabstr += "</table>";
        table.innerHTML = tabstr;
    }

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
</script>
