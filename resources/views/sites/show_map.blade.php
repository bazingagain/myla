@extends('layouts.app')

@section('content')
    <div>
        <div class="col-md-2">
            <ul class="nav nav-pills nav-stacked">
                <li role="presentation"><a href="#">Home</a></li>
                <li role="presentation"><a href="{{url('/user_manage')}}">用户管理</a></li>
                <li role="presentation" class="active"><a href="{{url('/show_map')}}">地图显示</a></li>
                <li role="presentation"><a href="{{url('/statistic_data')}}">数据统计</a></li>
            </ul>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-md-10 ">
                    <div class="panel panel-default">
                        <div class="panel-heading">地图显示</div>

                        <div class="panel-body">
                            <!--- maplabel Field --->

                            <div class="navbar-form">
                                {!! csrf_field() !!}
                                {!! Form::label('mapPosition', '用户定位')!!}
                                {!! Form::text('userName', null, ['class' => 'form-control', 'id'=>'userName', 'placeholder'=>"请输入用户名"]) !!}
                                {!! Form::submit('定位', ['class' => 'btn btn-default', 'onclick' => 'lockUser()']) !!}
                                {!! Form::submit('跟踪', ['class' => 'btn btn-default', 'onclick' => 'trackUser()']) !!}
                                {!! Form::submit('轨迹记录', ['class' => 'btn btn-default','disabled'=>'disabled', 'id'=>'trackLine','onclick' => 'trackLine()']) !!}
                                <div id="tips" style="height: 20px">
                                </div>
                            </div>
                            <div id="allmapcontainer" style=" height: 400px; text-align: center">
                                <div id="allmap" style="height: 400px">
                                </div>
                            </div>
                            <hr>


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript"
            src="http://api.map.baidu.com/api?v=2.0&ak=siHaBv2ERNA419jqHjf3z9IXGudDozjL"></script>
    <script type="text/javascript">
        // 百度地图API功能

        var tick = null;
        var currentTrackUser = null;
        var sContent =
                "<h4 style='margin:0 0 5px 0;padding:0.2em 0'>天安门</h4>" +
                "<img style='float:right;margin:4px' id='imgDemo' src='http://app.baidu.com/map/images/tiananmen.jpg' width='139' height='104' title='天安门'/>" +
                "<p style='margin:0;line-height:1.5;font-size:13px;text-indent:2em'>天安门坐落在中国北京市中心,故宫的南侧,与天安门广场隔长安街相望,是清朝皇城的大门...</p>" +
                "</div>";

        var map = new BMap.Map("allmap");    // 创建Map实例
        var point = new BMap.Point(116.404, 39.915);
        map.centerAndZoom(point, 11);  // 初始化地图,设置中心点坐标和地图级别
        map.enableScrollWheelZoom(true);     //开启鼠标滚轮缩放
        addMarker(map, point, sContent);
        map.addControl(new BMap.MapTypeControl());   //添加地图类型控件

        function addMarker(map, point, content) {
            var marker = new BMap.Marker(point);        // 创建标注
            var infoWindow = new BMap.InfoWindow(content);  // 创建信息窗口对象
            map.addOverlay(marker);                     //将标注加到地图层
            marker.addEventListener("click", function () {
                this.openInfoWindow(infoWindow);
                //图片加载完毕重绘infowindow
                document.getElementById('imgDemo').onload = function () {
                    infoWindow.redraw();   //防止在网速较慢，图片未加载时，生成的信息框高度比图片的总高度小，导致图片部分被隐藏
                }
            });

        }
        {{--定位用户，在地图中定位到具体点--}}
        function lockUser() {
            if (hasError())
                return;
            $.ajax({
                type: 'post',
                dataType: 'json',
                url: 'lockUser',
                data: {name: $("#userName").val()},
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
                success: function (json) {
                    if (json.isUser) {
                        var point = new BMap.Point(json.location_lontitude, json.location_latitude);
                        if (tick != null) {
//                            alert('清除跟踪')
                            clearInterval(tick);
                            map.panTo(point);
                            tick = null;
                            currentTrackUser = null; //清除跟踪用户

                        }

                        map.clearOverlays();
                        var content =
                                "<h4 style='margin:0 0 5px 0;padding:0.2em 0'>" + $("#userName").val() + "</h4>" +
                                "<img style='float:right;margin:4px' id='imgDemo' src='http://app.baidu.com/map/images/tiananmen.jpg' width='139' height='104'/>" +
                                "<p style='margin:0;line-height:1.5;font-size:13px;'>性别：     " + "</p>" +
                                "<p style='margin:0;line-height:1.5;font-size:13px;'>地区：     " + "</p>" +
                                "<p style='margin:0;line-height:1.5;font-size:13px;'>个性签名：  " + "</p>" +
                                "<p style='margin:0;line-height:1.5;font-size:13px;'>是否在线：  " + "</p>" +
                                "</div>";
                        addMarker(map, point, content);
                        map.panTo(point);
                        $("#trackLine").attr('disabled', true);  //定位模式下设置轨迹不可见
                    } else {
                        var tip = "<span class='label label-warning' style='height: 20px;margin-left: 62px;margin-top: 0px'><strong>您查找的用户不存在</strong></span>";
                        $("#tips").html(tip);
                    }
                },
                error: function (xhr, type) {

                }
            });
        }
        /**
         * 实时跟踪用户，在地图上描绘出其运动轨迹
         */
        function track() {
            var myIcon = new BMap.Icon("http://developer.baidu.com/map/jsdemo/img/Mario.png", new BMap.Size(32, 70), {    //小车图片
                //offset: new BMap.Size(0, -5),    //相当于CSS精灵
                imageOffset: new BMap.Size(0, 0)    //图片的偏移量。为了是图片底部中心对准坐标点。
            });
            var pts = new BMap.Point(116.3786889372559, 39.90762965106183);
            var carMk = new BMap.Marker(pts, {icon: myIcon});
            map.clearOverlays();
            map.addOverlay(carMk);
            carMk.setAnimation(BMAP_ANIMATION_BOUNCE); //跳动的动画
            var i = 0;
            map.panTo(pts);

            var polyline = new BMap.Polyline([
                new BMap.Point(116.399, 39.910),
                new BMap.Point(116.405, 39.920),
            ], {strokeColor: "blue", strokeWeight: 2, strokeOpacity: 0.5});   //创建折线

            function update() {
                i += 0.000001;
//                map.addOverlay(carMk);
                var oldPts = pts;
                pts = new BMap.Point(116.3786889372559 + i, 39.90762965106183);

                polyline = new BMap.Polyline([
                    oldPts,
                    pts,
                ], {strokeColor: "blue", strokeWeight: 2, strokeOpacity: 0.5});   //创建折线
                map.addOverlay(polyline);   //增加折线
                carMk.setPosition(pts);
//                map.panTo(pts);
            }

            tick = setInterval(update, 100);
        }

        function trackUser() {
            if (hasError())
                return;
            if (currentTrackUser == $("#userName").val()) {
                alert('相同');
                return;
            }
            $.ajax({
                type: 'post',
                dataType: 'json',
                url: 'trackUser',
                data: {name: $("#userName").val()},
                success: function (json) {
                    if (json.isUser) {
                        if ((currentTrackUser == null) || (currentTrackUser != Document.getElementById('userName').value())) {
                            currentTrackUser = $("#userName").val();
                            //如果以前没有定义跟踪
                            if (tick == null) {
                                track();
                            }
                            else {
                                clearInterval(tick);
                                tick = null;
                                track();
                            }
                        }
                        //设置只有在跟踪模式下才能记录轨迹
                        $("#trackLine").attr('disabled', false);

                    } else {
                        var tip = "<span class='label label-warning' style='height: 20px;margin-left: 62px;margin-top: 0px'><strong>您跟踪的用户不存在</strong></span>";
                        $("#tips").html(tip);
                    }
                },
                error: function (xhr, type) {
                }
            });
        }

        function trackLine() {

        }

        function hasError() {
//            alert('输入不能为空');
//            var temp = $("#userName").val();

            if ($("#userName").val() == '') {
                var tip = "<span class='label label-warning' style='height: 20px;margin-left: 62px;margin-top: 0px'><strong>输入不能为空</strong></span>";
                $("#tips").html(tip);
                return true;
            }
            else {
                $("#tips").html('');
                return false;
            }
        }


    </script>


@endsection
