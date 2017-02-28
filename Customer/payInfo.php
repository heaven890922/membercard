<?php include_once 'config.php';

if (isset($_SESSION['shopID'])) {
    $shopID = $_SESSION['shopID'];
} else {
    $shopID = '';
}
if (isset($_SESSION['shopname'])) {
    $shopname = $_SESSION['shopname'];
} else {
    $shopname = '选择支付的店铺';
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>支付信息</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
    <link rel="stylesheet" href="style/css/weui.css"/>
    <script src="style/js/vue.js"></script>
    <script src="style/js/vue-resource.js"></script>
    <style type="text/css">
        #allmap {
            width: 100%;
            height: 100%;
            overflow: hidden;
            margin: 0;
            font-family: "微软雅黑";
        }
    </style>
</head>
<body ontouchstart>
<div class="page">
    <div class="page__hd">
        <h1 class="page__title">
        </h1>
    </div>
    <div class="page__bd page__bd_spacing" id="payInfo">
        <div class="weui-cells">
            <div class="weui-cell weui-cell_access">
                <div class="weui-cell__hd" v-on:click="back">
                    <img src="style/images/goback.png" alt="" style="width:40px;margin-right:5px;display:block">
                </div>
                <div class="weui-cell__bd weui-cell_primary">
                    <p>输入支付金额</p>
                </div>
                <div style="color: grey" v-on:click="goExplain"><p>会员说明</p></div>
            </div>
        </div>
        <div class="weui-cell" style="font-size: 20px;">
            <div class="weui-cell__hd"><label class="weui-label">￥</label></div>
            <div class="weui-cell__bd">
                <input class="weui-input" type="number" placeholder="输入支付金额" v-model="item.money"/>
            </div>
        </div>
        <div class="weui-cell"></div>
        <div class="weui-cells">
            <a class="weui-cell weui-cell_access" href="javascript:;" v-on:click="goSelect">
                <div class="weui-cell__bd">
                    <p>{{item.shopname}}</p>
                </div>
                <div class="weui-cell__ft">
                </div>
            </a>
        </div>
        <div id="allmap">

        </div>
        <input type="hidden" name="lng" id="lng">
        <input type="hidden" name="lat" id="lat">
        <div class="weui-cell">
            <p weui-flex__item style="color: red;text-align: center;">{{result}}</p>
        </div>
        <div class="weui-btn-area">
            <button v-on:click="next" class="weui-btn weui-btn_primary">下一步</button>
        </div>
    </div>
</div>
<script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=EXS6SaCzZnQck0K7rWrgL5URSohXZtNn"></script>
<script type="text/javascript">
    var wd = document.body.clientWidth;
    var map = new BMap.Map("allmap");
    var point = new BMap.Point(116.331398, 39.897445);
    map.centerAndZoom(point, 12);

    var geolocation = new BMap.Geolocation();
    var gc = new BMap.Geocoder();
    geolocation.getCurrentPosition(function (r) {   //定位结果对象会传递给r变量
            if (this.getStatus() == BMAP_STATUS_SUCCESS) {  //通过Geolocation类的getStatus()可以判断是否成功定位。
                var pt = r.point;
                var mk = new BMap.Marker(r.point);
                map.addOverlay(mk);
                map.panTo(r.point);

                gc.getLocation(pt, function (rs) {
                    var addComp = rs.addressComponents;
                    // alert(addComp.province + addComp.city + addComp.district + addComp.street + addComp.streetNumber);
                    // var longitude = r.point.lng;
                    // var latitude = r.point.lat;
                    // alert(longitude);
                    // alert(latitude);
                    document.getElementById("lng").value = r.point.lng;
                    document.getElementById("lat").value = r.point.lat;
                    // $("#lng").val(r.point.lng);
                    // $("#lat").val(r.point.lat);
                });
            } else {
                //关于状态码
                //BMAP_STATUS_SUCCESS   检索成功。对应数值“0”。
                //BMAP_STATUS_CITY_LIST 城市列表。对应数值“1”。
                //BMAP_STATUS_UNKNOWN_LOCATION  位置结果未知。对应数值“2”。
                //BMAP_STATUS_UNKNOWN_ROUTE 导航结果未知。对应数值“3”。
                //BMAP_STATUS_INVALID_KEY   非法密钥。对应数值“4”。
                //BMAP_STATUS_INVALID_REQUEST   非法请求。对应数值“5”。
                //BMAP_STATUS_PERMISSION_DENIED 没有权限。对应数值“6”。(自 1.1 新增)
                //BMAP_STATUS_SERVICE_UNAVAILABLE   服务不可用。对应数值“7”。(自 1.1 新增)
                //BMAP_STATUS_TIMEOUT   超时。对应数值“8”。(自 1.1 新增)
                switch (this.getStatus()) {
                    case 2:
                        alert('位置结果未知 获取位置失败.');
                        break;
                    case 3:
                        alert('导航结果未知 获取位置失败..');
                        break;
                    case 4:
                        alert('非法密钥 获取位置失败.');
                        break;
                    case 5:
                        alert('对不起,非法请求位置  获取位置失败.');
                        break;
                    case 6:
                        alert('对不起,当前 没有权限 获取位置失败.');
                        break;
                    case 7:
                        alert('对不起,服务不可用 获取位置失败.');
                        break;
                    case 8:
                        alert('对不起,请求超时 获取位置失败.');
                        break;

                }
            }
        },
        {enableHighAccuracy: true}
    )
</script>
<script type="text/javascript">
    Vue.http.options.emulateJSON = true;
    var payInfo = new Vue({
        el: "#payInfo",
        data: {
            result: '',
            url: decodeURI(location.href),
            item: {'money': '', 'shopID': '', 'shopname': '选择支付的店铺', 'lng': '', 'lat': ''}
        },
        methods: {
            next: function () {
                if (this.item.money <= 0) {
                    this.result = '请输入支付金额';
                    return false;
                }
                if (this.item.shopID == '') {
                    this.result = '请选择支付店铺';
                    return false;
                }
                this.$http.post('action.php?action=payInfo', this.item).then((response) => {
                    window.location.href = 'confirmPay.php'
                })
            },
            getInfo: function () {
                if (this.url.indexOf("?") > 0) {
                    this.item.money = this.url.split("?")[1].split("=")[1]
                } else {
                    this.item.money = '';
                }
                var shopID = '<?php echo $shopID;?>';
                var shopname = '<?php echo $shopname;?>';

                if (shopID != '') {
                    this.item.shopID = shopID
                }
                if (shopname != '') {
                    this.item.shopname = shopname
                }
            },
            goExplain: function () {
                window.location.href = 'explain.php'
            },
            goSelect: function () {
                this.item.lng = document.getElementById("lng").value;
                this.item.lat = document.getElementById("lat").value;
                var money = this.item.money
                this.$http.post('action.php?action=getLocation', this.item).then((response) => {
                    window.location.href = encodeURI('select.php?m=' + money)
                })
            },
            back: function () {
                history.go(-1);
            }
        }
    });
    payInfo.getInfo();
</script>
</body>
</html>