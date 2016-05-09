@extends('layouts.app')

@section('content')
    <div>
        <div class="col-md-2">
            <ul class="nav nav-pills nav-stacked">
                <li role="presentation"><a href="#">Home</a></li>
                <li role="presentation"><a href="{{url('/user_manage')}}">用户管理</a></li>
                <li role="presentation"><a href="{{url('/show_map')}}">地图显示</a></li>
                <li role="presentation" class="active"><a href="{{url('/statistic_data')}}">数据统计</a></li>
                <li role="presentation"><a href="{{url('/show_feedback')}}">用户反馈</a></li>
                <li role="presentation"><a href="{{url('/func_test')}}">功能测试</a></li>
            </ul>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-md-10 ">
                    <div class="panel panel-default">
                        <div class="panel-heading">数据统计</div>

                        <div class="panel-body">
                            <p>
                            <div style="float:right;" class="btn-group">
                                <button id="recentBtnText" type="button" class="btn btn-primary dropdown-toggle"
                                        data-toggle="dropdown">
                                    最近一年<span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu" role="menu">
                                    <li><a href="#" onclick="showRecentSevenDay()">最近7天</a></li>
                                    <li><a href="#" onclick="showRecentThirtyDay()">最近30天</a></li>
                                    <li><a href="#" onclick="showRecentYear()">最近一年</a></li>
                                </ul>
                            </div>
                            <div id="main" style="width: 800px;height:400px;"></div>
                            </p>
                            <hr>
                            <p>
                            <div id="pic2" style="float:right;" class="btn-group">
                                <button id="recentClientBtnText" type="button" class="btn btn-primary dropdown-toggle"
                                        data-toggle="dropdown">
                                    最近一年<span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu" role="menu">
                                    <li><a href="#pic2" onclick="showClientRecentSevenDay()">最近7天</a></li>
                                    <li><a href="#pic2" onclick="showClientRecentThirtyDay()">最近30天</a></li>
                                    <li><a href="#pic2" onclick="showClientRecentYear()">最近一年</a></li>
                                </ul>
                            </div>
                            <div id="clientInfo" style="width: 800px;height:400px;"></div>
                            </p>
                            <hr>
                            <p>
                            <div id="mapTrend" style="width: 800px;height:400px;"></div>
                            </p>
                            <hr>
                            <p>
                            <div id="pieTrend" style="width: 800px;height:400px;"></div>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">

        // 指定图表的配置项和数据
        var option = {
            title: {
                text: '乐见用户增长图'
            },
            tooltip: {},
            toolbox: {
                feature: {
                    saveAsImage: {}
                }
            },
            legend: {
                data: ['新增用户数']
            },
            xAxis: {
                data: []
            },
            yAxis: {},
            series: [{
                name: '新增用户数',
                type: 'bar',
                data: []
            }]
        };
        // 使用刚指定的配置项和数据显示图表。
        var mainChart = echarts.init(document.getElementById('main'));
        mainChart.showLoading();
        mainChart.setOption(option);
        showRecentYear();
        function showRecentSevenDay() {
            $.ajax({
                type: 'get',
                url: 'getUserNumInfo/sevenday',
                success: function (data) {
                    $('#recentBtnText').text('最近7天');
                    mainChart.hideLoading();
                    mainChart.setOption({
                        xAxis: {
                            data: data.timeStamp
                        },
                        series: [
                            {
                                name: '新增用户数',
                                data: data.msg1
                            }
                        ]
                    });
                },
                error: function (data) {
                    mainChart.hideLoading();
                    mainChart.setOption('error');
                }
            });
        }

        function showRecentThirtyDay() {
            $.ajax({
                type: 'get',
                url: 'getUserNumInfo/thirtyday',
                success: function (data) {
                    $('#recentBtnText').text('最近30天');
                    mainChart.hideLoading();
                    mainChart.setOption({
                        xAxis: {
                            data: data.timeStamp
                        },
                        series: [
                            {
                                name: '新增用户数',
                                data: data.msg1
                            }
                        ]
                    });
                },
                error: function (data) {
                    mainChart.hideLoading();
                    mainChart.setOption('error');
                }
            });
        }
        function showRecentYear() {
            $.ajax({
                type: 'get',
                url: 'getUserNumInfo/year',
                success: function (data) {
                    $('#recentBtnText').text('最近一年');
                    mainChart.hideLoading();
                    mainChart.setOption({
                        xAxis: {
                            data: data.timeStamp
                        },
                        series: [
                            {
                                name: '新增用户数',
                                data: data.msg1
                            }
                        ]
                    });
                },
                error: function (data) {
                    mainChart.hideLoading();
                    mainChart.setOption('error');
                }
            });
        }

    </script>

    <script type="text/javascript">
        option = {
            title: {
                text: '用户推送信息'
            },
            tooltip: {
                trigger: '用户推送信息'
            },
            legend: {
                data: ['新增用户数量', '添加请求数量', '共享请求数量']
            },
            grid: {
                left: '3%',
                right: '4%',
                bottom: '3%',
                containLabel: true
            },
            toolbox: {
                feature: {
                    saveAsImage: {}
                }
            },
            xAxis: {
                type: 'category',
                boundaryGap: false,
                data: []
            },
            yAxis: {
                type: 'value'
            },
            series: [
                {
                    name: '新增用户数量',
                    type: 'line',
                    stack: '总量',
                    data: []
                },
                {
                    name: '添加请求数量',
                    type: 'line',
                    stack: '总量',
                    data: []
                },
                {
                    name: '共享请求数量',
                    type: 'line',
                    stack: '总量',
                    data: []
                }
            ]
        };

        var infoChart = echarts.init(document.getElementById('clientInfo'));
        infoChart.showLoading();
        infoChart.setOption(option);
        showClientRecentYear();
        function showClientRecentYear() {
            $.ajax({
                type: 'get',
                url: 'getMessageInfo/year',
                success: function (data) {
                    infoChart.hideLoading();
                    $('#recentClientBtnText').text('最近一年');
                    infoChart.hideLoading();
                    infoChart.setOption({
                        xAxis: {
                            data: data.timeStamp
                        },
                        series: [
                            {
                                name: '新增用户数量',
                                data: data.msg1
                            },
                            {
                                name: '添加请求数量',
                                data: data.msg2
                            },
                            {
                                name: '共享请求数量',
                                data: data.msg3
                            }
                        ]
                    });
                },
                error: function (data) {
                    infoChart.hideLoading();
                    infoChart.setOption('error');
                }
            });
        }
        function showClientRecentThirtyDay() {
            $.ajax({
                type: 'get',
                url: 'getMessageInfo/thirtyday',
                success: function (data) {
                    $('#recentClientBtnText').text('最近30天');
                    infoChart.hideLoading();
                    infoChart.setOption({
                        xAxis: {
                            data: data.timeStamp
                        },
                        series: [
                            {
                                name: '新增用户数量',
                                data: data.msg1
                            },
                            {
                                name: '添加请求数量',
                                data: data.msg2
                            },
                            {
                                name: '共享请求数量',
                                data: data.msg3
                            }
                        ]
                    });
                },
                error: function (data) {
                    infoChart.hideLoading();
                    infoChart.setOption('error');
                }
            });
        }
        function showClientRecentSevenDay() {
            $.ajax({
                type: 'get',
                url: 'getMessageInfo/sevenday',
                success: function (data) {
                    $('#recentClientBtnText').text('最近7天');
                    infoChart.hideLoading();
                    infoChart.setOption({
                        xAxis: {
                            data: data.timeStamp
                        },
                        series: [
                            {
                                name: '新增用户数量',
                                data: data.msg1
                            },
                            {
                                name: '添加请求数量',
                                data: data.msg2
                            },
                            {
                                name: '共享请求数量',
                                data: data.msg3
                            }
                        ]
                    });
                },
                error: function (data) {
                    infoChart.hideLoading();
                    infoChart.setOption('error');
                }
            });
        }

    </script>


    <script type="text/javascript">
        sexOption = {
            title: {
                text: '客户性别分布',
                subtext: '各省分布情况',
                left: 'center'
            },
            tooltip: {
                trigger: 'item'
            },
            legend: {
                orient: 'vertical',
                left: 'left',
                data: ['男', '女']
            },
            visualMap: {
                min: 0,
                max: 100,
                left: 'left',
                top: 'bottom',
                text: ['高', '低'],           // 文本，默认为数值文本
                calculable: true
            },
            toolbox: {
                show: true,
                orient: 'vertical',
                left: 'right',
                feature: {
                    dataView: {readOnly: false},
                    restore: {},
                    saveAsImage: {}
                }
            },
            series: [
                {
                    name: '男',
                    type: 'map',
                    mapType: 'china',
                    roam: false,
                    selectedMode: 'multiple',
                    mapValueCalculation: 'sum',
                    label: {
                        normal: {
                            show: true
                        },
                        emphasis: {
                            show: true
                        }
                    },
                    data: []
                },
                {
                    name: '女',
                    type: 'map',
                    mapType: 'china',
                    selectedMode: 'multiple',
                    mapValueCalculation: 'sum',
                    label: {
                        normal: {
                            show: true
                        },
                        emphasis: {
                            show: true
                        }
                    },
                    data: []
                }

            ]
        };
        var sexChart = echarts.init(document.getElementById('mapTrend'));
        sexChart.showLoading();
        sexChart.setOption(sexOption);
        $.ajax({
            type: 'get',
            url: 'getUserSexInfo',
            success: function (data) {
                sexChart.hideLoading();
                sexChart.setOption({
                    series: [
                        {
                            name: '男',
                            data: data.msg1
                        },
                        {
                            name: '女',
                            data: data.msg2
                        }
                    ]
                });
            },
            error: function (data) {
                sexChart.hideLoading();
                sexChart.setOption('error');
            }
        });

    </script>
    {{--地理分布--}}
    <script type="text/javascript">

        var pieOption = {
            title: {
                text: '用户地理分布',
                x: 'center'
            },
            tooltip: {
                trigger: 'item',
                formatter: "{a} <br/>{b} : {c} ({d}%)"
            },
            legend: {
                x: 'center',
                y: 'bottom',
                data: ['北京', '天津', '上海', '重庆', '河北', '河南', '云南', '辽宁', '黑龙江', '湖南', '安徽',
                    '山东', '新疆', '江苏', '浙江', '江西', '湖北', '广西', '甘肃', '山西', '内蒙古', '陕西', '吉林', '福建',
                    '贵州', '广东', '青海', '西藏', '四川', '海南', '台湾', '香港', '澳门']
            },
            toolbox: {
                show: true,
                orient: 'vertical',
                left: 'right',
                feature: {

                    mark: {show: true},
                    dataView: {show: true, readOnly: false},
                    magicType: {
                        show: true,
                        type: ['pie', 'funnel']
                    },
                    restore: {show: true},
                    saveAsImage: {show: true}
                }
            },
            calculable: true,
            series: [
                {
                    name: '半径模式',
                    type: 'pie',
                    radius: [20, 110],
                    center: ['25%', 200],
                    roseType: 'radius',
                    label: {
                        normal: {
                            show: false
                        },
                        emphasis: {
                            show: true
                        }
                    },
                    lableLine: {
                        normal: {
                            show: false
                        },
                        emphasis: {
                            show: true
                        }
                    },
                    data: [
                        //TODO
                    ]
                },
                {
                    name: '面积模式',
                    type: 'pie',
                    radius: [30, 110],
                    center: ['75%', 200],
                    roseType: 'area',
                    data: [
                        //TODO
                    ]
                }
            ]
        };
        var pieChart = echarts.init(document.getElementById('pieTrend'));
        pieChart.showLoading();
        pieChart.setOption(pieOption);
        $.ajax({
            type: 'get',
            url: 'getUserAddressInfo',
            success: function (data) {
                pieChart.hideLoading();
                pieChart.setOption({
                    series: [
                        {
                            name: '半径模式',
                            data: data.msg1
                        },
                        {
                            name: '面积模式',
                            data: data.msg2
                        }
                    ]
                });
            },
            error: function (data) {
                pieChart.hideLoading();
                pieChart.setOption('error');
            }
        });

    </script>

@endsection
