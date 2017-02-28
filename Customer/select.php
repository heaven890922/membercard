<?php
include_once 'config.php';
$lng = $_SESSION['lng'];
$lat = $_SESSION['lat'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>选择店铺</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
    <link rel="stylesheet" href="style/css/weui.css"/>
    <script src="style/js/vue.js"></script>
    <script src="style/js/vue-resource.js"></script>
</head>
<body ontouchstart>
<div class="page">
    <div class="page__bd page__bd_spacing" id="shopInfo">
        <div class="weui-cells">
            <div class="weui-cell weui-cell_access">
                <div class="weui-cell__hd" v-on:click="back">
                    <img src="style/images/goback.png" alt="" style="width:40px;margin-right:5px;display:block">
                </div>
                <div class="weui-cell__bd weui-cell_primary">
                    <p>选择店铺</p>
                </div>
            </div>
        </div>
        <div class="weui-loadmore weui-loadmore_line" v-if="noData">
            <span class="weui-loadmore__tips">暂无数据</span>
        </div>
        <div class="weui-loadmore" v-if="loading">
            <i class="weui-loading"></i>
            <span class="weui-loadmore__tips">正在加载</span>
        </div>
        <div class="weui-cell" v-for="list in shopInfo" v-show="show">
            <div class="weui-cell__bd" v-on:click="goBack(list.shopid,list.shopname)">
                <div style="float:left; width:25%;">
                    <img v-bind:src="baseUrl+list.pictureurl.split('|')[0]" style="width: 64px;height:64px">
                </div>
                <div style="float:left; width:75%;">
                    <p class="weui-flex__item" style="font-size: .85em;">店铺名称：{{list.shopname}}</p>
                    <p class="weui-flex__item" style="color: grey;font-size: .75em;">{{list.shopaddress}}</p>
                    <p class="weui-flex__item" style="color: grey;font-size: .75em;">距离：{{list.juli/1000}}km</p>
                </div>

            </div>
        </div>
        <div class="weui-cell">
            <p weui-flex__item style="color: red;text-align: center;">{{result}}</p>
        </div>

    </div>
</div>

<script type="text/javascript">
    Vue.http.options.emulateJSON = true;
    var shopInfo = new Vue({
        el: "#shopInfo",
        data: {
            result: '',
            shopInfo: '',
            show: false,
            noData: false,
            loading: false,
            url: decodeURI(location.href),
            baseUrl: 'http://www.xiaochelife.com/Upload/',
            backInfo: {'shopID': '', 'shopname': ''},
            item: {'page': 0, 'num': 10, 'longitude': '113.373188', 'latitude': '23.128096'}
        },
        created: function () {
            this.getShopNearBy();
        },
        methods: {
            getShopNearBy: function () {
                var longitude = '<?php echo $lng;?>';
                var latitude = '<?php echo $lat;?>';
                if (longitude != '') {
                    this.item.longitude = longitude;
                }
                if (latitude != '') {
                    this.item.latitude = latitude;
                }
                this.loading = true

                this.$http.post('action.php?action=getShopNearBy', this.item).then((response) => {
                    if (response.data.code == 200) {
                        this.show = true;
                        if (response.data.data.length != 0) {
                            this.shopInfo = response.data.data
                        } else {
                            this.noData = true
                        }
                    } else {
                        this.result = response.data.msg;
                    }
                    this.loading = false
                })
            },
            goExplain: function () {
                window.location.href = 'explain.php'
            },
            goBack: function (shopID, shopname) {
                this.backInfo.shopID = shopID;
                this.backInfo.shopname = shopname;
                var m = this.url.split("?")[1].split("=")[1]
                this.$http.post('action.php?action=backInfo', this.backInfo).then((response) => {
                    window.location.href = "payInfo.php?m=" + m
                })
            },
            back: function () {
                history.go(-1);
            }
        }
    });

</script>


</body>
</html>