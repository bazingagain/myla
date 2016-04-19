<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
</head>
<body>
<div id="fe">
    <h1>{{message}}</h1>
    <h2>{{time}}</h2>
    <h2>{{location_latitude}}</h2>
    <h2>{{location_lontitude}}</h2>
    <button onclick="getLocation()">测试</button>
    <div id="test1"></div>
    <div id="test2"></div>

    <pre>
        {{ $data | json}}
    </pre>
</div>
<script src="vue/vue.min.js"></script>
<script type="text/javascript">
    var dat = {
        message : 'hello world!',
        location_latitude : 0,
        location_lontitude : 0,
        time : getNowFormatDate()
    };
    var s = new Vue({
        el: '#fe',
        data: dat
    });

    var t1 = setInterval(update, 1000);

    function update()
    {
        dat.time = getNowFormatDate();
        getLocation();
    }

    function getLocation()
    {
        $.ajax({
            type: 'get',
            url: 'userGetLocation',
            success: function (json) {
                dat.location_latitude = json.location_latitude;
                dat.location_lontitude = json.location_lontitude;
            },
            error: function (xhr, type) {
                dat.location_latitude = 0;
                dat.location_lontitude = 0;
            }
        });
    }


    function getNowFormatDate() {
        var date = new Date();
        var seperator1 = "-";
        var seperator2 = ":";
        var month = date.getMonth() + 1;
        var strDate = date.getDate();
        if (month >= 1 && month <= 9) {
            month = "0" + month;
        }
        if (strDate >= 0 && strDate <= 9) {
            strDate = "0" + strDate;
        }
        var currentdate = date.getFullYear() + seperator1 + month + seperator1 + strDate
            + " " + date.getHours() + seperator2 + date.getMinutes()
            + seperator2 + date.getSeconds();
        return currentdate;
    }


</script>
</body>
</html>

