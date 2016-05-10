@extends('layouts.app')

@section('content')
    <div>
        <div class="container">
            <div class="row">
                <div class="col-md-10 ">
                    <div class="panel panel-default">
                        <div class="panel-heading">地图显示</div>

                        <div class="panel-body">

                            <div class="navbar-form">
                                <p id="username">{{$name}}</p>
                                {{--<p id="sharetime">{{$time}}</p>--}}
                                {{--{!! Form::label('mapPosition', '的实时运动轨迹'!!}--}}
                            </div>
                            <div id="tips" style="height: 20px">
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
        /**
         * 实时跟踪用户，在地图上描绘出其运动轨迹
         */
        function track(la, lo) {
            var myIcon = new BMap.Icon("http://developer.baidu.com/map/jsdemo/img/Mario.png", new BMap.Size(32, 70), {    //小车图片
                //offset: new BMap.Size(0, -5),    //相当于CSS精灵
                imageOffset: new BMap.Size(0, 0)    //图片的偏移量。为了是图片底部中心对准坐标点。
            });

            var pts = new BMap.Point(lo, la);
            var carMk = new BMap.Marker(pts, {icon: myIcon});
            map.clearOverlays();
            map.addOverlay(carMk);
            carMk.setAnimation(BMAP_ANIMATION_BOUNCE); //跳动的动画
            var i = 0;
            map.panTo(pts);

//            var polyline = new BMap.Polyline([
//                new BMap.Point(116.399, 39.910),
//                new BMap.Point(116.405, 39.920),
//            ], {strokeColor: "blue", strokeWeight: 2, strokeOpacity: 0.5});   //创建折线

            var oldPts = pts;
            function update() {
                i+=0.001;
                $.ajax({
                    type: 'get',
                    dataType: 'json',
                    url: '/myla/public/showSingleUserLoc',
                    data: {name: currentTrackUser},
                    success : function(json){
                        if(json.canshare){
                            console.log(i);
                            oldPts = pts;
                            pts = new BMap.Point(json.location_lontitude + i, json.location_latitude + i);
                        }else{
                            //共享关闭， 则停止共享
                            clearInterval(tick);
                            map.clearOverlays();
                            var tip = "<span class='label label-warning' style='height: 20px;margin-left: 62px;margin-top: 0px'><strong>用户停止共享</strong></span>";
                            $("#tips").html(tip);
                        }
                    },
                    error : function(json){

                    }
                });
//                i += 0.000001;
                polyline = new BMap.Polyline([
                    oldPts,
                    pts,
                ], {strokeColor: "blue", strokeWeight: 2, strokeOpacity: 0.5});   //创建折线
                map.addOverlay(polyline);   //增加折线
                carMk.setPosition(pts);
//                map.panTo(pts);
            }
            tick = setInterval(update, 1000);
        }


        /**
         * 跟踪用户
         */
//        function trackUser() {
//            if (currentTrackUser == $("#username").val()) {
//                alert('相同');
//                return;
//            }
            $.ajax({
                type: 'post',
                dataType: 'json',
                url: '/myla/public/trackUser',
                data: {name: $('#username').text()},
                success: function (json) {
                    if (json.isUser) {
                        if ((currentTrackUser == null) || (currentTrackUser != ($('#username').text()))) {
                            currentTrackUser = $('#username').text();
                            //如果以前没有定义跟踪
                            if (tick == null) {
                                track(json.location_latitude, json.location_lontitude);
                            }
                            else {
                                clearInterval(tick);
                                tick = null;
                                track(json.location_latitude, json.location_lontitude);
                            }
                        }
                    }
                },
                error: function (xhr, type) {
                }
            });
//        }

    </script>
@endsection


