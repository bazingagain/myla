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
            </ul>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-md-10 ">
                    <div class="panel panel-default">
                        <div class="panel-heading">数据统计</div>

                        <div class="panel-body">

                            <p>
                            <div id="main" style="width: 800px;height:400px;"></div>
                            </p>
                            <hr >
                            <p>
                            <div id="clienTrend" style="width: 800px;height:400px;"></div>
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
        // 基于准备好的dom，初始化echarts实例
        var mainChart = echarts.init(document.getElementById('main'));
        var clienTrendChart = echarts.init(document.getElementById('clienTrend'));

        // 指定图表的配置项和数据
        var option = {
            title: {
                text: '乐见新增用户增长图'
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
                data: ["1月", "2月", "3月", "4月", "5月", "6月", "7月", "8月", "9月", "10月", "11月", "12月"]
            },
            yAxis: {},
            series: [{
                name: '新增用户数',
                type: 'bar',
                data: [5, 5, 5, 5, 5, 5, 5, 5, 20, 36, 10, 10]
            }]
        };

        // 使用刚指定的配置项和数据显示图表。
        mainChart.setOption(option);

        trendOption = {
            title: {
                text: '用户统计'
            },
            tooltip: {
                trigger: 'axis'
            },
            legend: {
                data: ['新增用户', '在线用户', '活跃用户']
            },
            grid: {
//                left: '3%',
//                right: '4%',
//                bottom: '3%',
//                containLabel: true
            },
            toolbox: {
                feature: {
                    saveAsImage: {}
                }
            },
            xAxis: {
                type: 'category',
                boundaryGap: false,
                data: ["1月", "2月", "3月", "4月", "5月", "6月", "7月", "8月", "9月", "10月", "11月", "12月"]
            },
            yAxis: {
                type: 'value'
            },
            series: [
                {
                    name: '新增用户',
                    type: 'line',
                    stack: '总量',
                    data: [120, 132, 101, 134, 90, 230, 210, 120, 132, 101, 134, 90]
                },
                {
                    name: '在线用户',
                    type: 'line',
                    stack: '总量',
                    data: [220, 182, 191, 234, 290, 330, 310, 220, 182, 191, 234, 290]
                },
                {
                    name: '活跃用户',
                    type: 'line',
                    stack: '总量',
                    data: [150, 232, 201, 154, 190, 330, 410, 150, 232, 201, 154, 190]
                }
            ]
        };

        clienTrendChart.setOption(trendOption);

    </script>

    <script type="text/javascript">

        var mapChart = echarts.init(document.getElementById('mapTrend'));
        function randomData() {
            return Math.round(Math.random() * 1000);
        }

        mapOption = {
            title: {
                text: '客户分布',
                subtext: '各省客户分布情况',
                left: 'center'
            },
            tooltip: {
                trigger: 'item'
            },
            legend: {
                orient: 'vertical',
                left: 'left',
                data: ['iphone3', 'iphone4', 'iphone5']
            },
            visualMap: {
                min: 0,
                max: 800,
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
                    name: 'iphone3',
                    type: 'map',
                    mapType: 'china',
                    roam: false,
                    label: {
                        normal: {
                            show: true
                        },
                        emphasis: {
                            show: true
                        }
                    },
                    data: [
                        {name: '北京', value: randomData()},
                        {name: '天津', value: randomData()},
                        {name: '上海', value: randomData()},
                        {name: '重庆', value: randomData()},
                        {name: '河北', value: randomData()},
                        {name: '河南', value: randomData()},
                        {name: '云南', value: randomData()},
                        {name: '辽宁', value: randomData()},
                        {name: '黑龙江', value: randomData()},
                        {name: '湖南', value: randomData()},
                        {name: '安徽', value: randomData()},
                        {name: '山东', value: randomData()},
                        {name: '新疆', value: randomData()},
                        {name: '江苏', value: randomData()},
                        {name: '浙江', value: randomData()},
                        {name: '江西', value: randomData()},
                        {name: '湖北', value: randomData()},
                        {name: '广西', value: randomData()},
                        {name: '甘肃', value: randomData()},
                        {name: '山西', value: randomData()},
                        {name: '内蒙古', value: randomData()},
                        {name: '陕西', value: randomData()},
                        {name: '吉林', value: randomData()},
                        {name: '福建', value: randomData()},
                        {name: '贵州', value: randomData()},
                        {name: '广东', value: randomData()},
                        {name: '青海', value: randomData()},
                        {name: '西藏', value: randomData()},
                        {name: '四川', value: randomData()},
                        {name: '宁夏', value: randomData()},
                        {name: '海南', value: randomData()},
                        {name: '台湾', value: randomData()},
                        {name: '香港', value: randomData()},
                        {name: '澳门', value: randomData()}
                    ]
                },
                {
                    name: 'iphone4',
                    type: 'map',
                    mapType: 'china',
                    label: {
                        normal: {
                            show: true
                        },
                        emphasis: {
                            show: true
                        }
                    },
                    data: [
                        {name: '北京', value: randomData()},
                        {name: '天津', value: randomData()},
                        {name: '上海', value: randomData()},
                        {name: '重庆', value: randomData()},
                        {name: '河北', value: randomData()},
                        {name: '安徽', value: randomData()},
                        {name: '新疆', value: randomData()},
                        {name: '浙江', value: randomData()},
                        {name: '江西', value: randomData()},
                        {name: '山西', value: randomData()},
                        {name: '内蒙古', value: randomData()},
                        {name: '吉林', value: randomData()},
                        {name: '福建', value: randomData()},
                        {name: '广东', value: randomData()},
                        {name: '西藏', value: randomData()},
                        {name: '四川', value: randomData()},
                        {name: '宁夏', value: randomData()},
                        {name: '香港', value: randomData()},
                        {name: '澳门', value: randomData()}
                    ]
                },
                {
                    name: 'iphone5',
                    type: 'map',
                    mapType: 'china',
                    label: {
                        normal: {
                            show: true
                        },
                        emphasis: {
                            show: true
                        }
                    },
                    data: [
                        {name: '北京', value: randomData()},
                        {name: '天津', value: randomData()},
                        {name: '上海', value: randomData()},
                        {name: '广东', value: randomData()},
                        {name: '台湾', value: randomData()},
                        {name: '香港', value: randomData()},
                        {name: '澳门', value: randomData()}
                    ]
                }
            ]
        };

        mapChart.setOption(mapOption);
    </script>

    <script type="text/javascript">
        pieOption = {
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
                data: ['北京',
                    '天津',
                    '上海',
                    '重庆',
                    '河北',
                    '河南',
                    '云南',
                    '辽宁',
                    '黑龙江',
                    '湖南',
                    '安徽',
                    '山东',
                    '新疆',
                    '江苏',
                    '浙江',
                    '江西',
                    '湖北',
                    '广西',
                    '甘肃',
                    '山西',
                    '内蒙古',
                    '陕西',
                    '吉林',
                    '福建',
                    '贵州',
                    '广东',
                    '青海',
                    '西藏',
                    '四川',
                    '海南',
                    '台湾',
                    '香港',
                    '澳门']
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
                        {value: 10, name: '北京'},
                        {value: 5, name: '天津'},
                        {value: 15, name: '上海'},
                        {value: 25, name: '重庆'},
                        {value: 20, name: '河北'},
                        {value: 35, name: '河南'},
                        {value: 30, name: '云南'},
                        {value: 40, name: '辽宁'},
                        {value: 40, name: '黑龙江'},
                        {value: 40, name: '湖南'},
                        {value: 40, name: '安徽'},
                        {value: 40, name: '山东'},
                        {value: 40, name: '新疆'},
                        {value: 40, name: '江苏'},
                        {value: 40, name: '浙江'},
                        {value: 40, name: '江西'},
                        {value: 40, name: '湖北'},
                        {value: 40, name: '广西'},
                        {value: 40, name: '甘肃'},
                        {value: 40, name: '山西'},
                        {value: 40, name: '内蒙古'},
                        {value: 40, name: '陕西'},
                        {value: 40, name: '吉林'},
                        {value: 40, name: '福建'},
                        {value: 40, name: '贵州'},
                        {value: 40, name: '广东'},
                        {value: 40, name: '青海'},
                        {value: 40, name: '西藏'},
                        {value: 40, name: '四川'},
                        {value: 40, name: '宁夏'},
                        {value: 40, name: '海南'},
                        {value: 40, name: '台湾'},
                        {value: 40, name: '香港'},
                        {value: 40, name: '澳门'}
                    ]
                },
                {
                    name: '面积模式',
                    type: 'pie',
                    radius: [30, 110],
                    center: ['75%', 200],
                    roseType: 'area',
                    data: [
                        {value: 10, name: '北京'},
                        {value: 5, name: '天津'},
                        {value: 15, name: '上海'},
                        {value: 25, name: '重庆'},
                        {value: 20, name: '河北'},
                        {value: 35, name: '河南'},
                        {value: 30, name: '云南'},
                        {value: 40, name: '辽宁'},
                        {value: 40, name: '黑龙江'},
                        {value: 40, name: '湖南'},
                        {value: 40, name: '安徽'},
                        {value: 40, name: '山东'},
                        {value: 40, name: '新疆'},
                        {value: 40, name: '江苏'},
                        {value: 40, name: '浙江'},
                        {value: 40, name: '江西'},
                        {value: 40, name: '湖北'},
                        {value: 40, name: '广西'},
                        {value: 40, name: '甘肃'},
                        {value: 40, name: '山西'},
                        {value: 40, name: '内蒙古'},
                        {value: 40, name: '陕西'},
                        {value: 40, name: '吉林'},
                        {value: 40, name: '福建'},
                        {value: 40, name: '贵州'},
                        {value: 40, name: '广东'},
                        {value: 40, name: '青海'},
                        {value: 40, name: '西藏'},
                        {value: 40, name: '四川'},
                        {value: 40, name: '宁夏'},
                        {value: 40, name: '海南'},
                        {value: 40, name: '台湾'},
                        {value: 40, name: '香港'},
                        {value: 40, name: '澳门'}
                    ]
                }
            ]
        };
        var pieChart = echarts.init(document.getElementById('pieTrend'));
        pieChart.setOption(pieOption);
    </script>

@endsection
