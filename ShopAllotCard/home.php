<?php
include_once 'config.php';
$shopInfo = getShopInfo();

$data = json_decode($shopInfo, true);
if ($data['code'] == 200) {
    $_SESSION['name'] = $data['data']['name'];
    $_SESSION['shopName'] = $data['data']['shopName'];
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>加盟商户发卡</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
    <link rel="stylesheet" href="style/css/weui.css"/>
    <script src="style/js/vue.js"></script>
    <script src="style/js/vue-resource.js"></script>
    <script src="style/js/vue-router.js"></script>
</head>
<body ontouchstart>
<div class="page">
    <div class="page__bd page__bd_spacing" id="home">
        <div class="weui-cells">
            <div class="weui-cell weui-cell_access">
                <div class="weui-cell__hd" v-on:click="back">
                    <img src="style/images/goback.png" alt="" style="width:40px;margin-right:5px;display:block">
                </div>
                <div class="weui-cell__bd weui-cell_primary">
                    <p>商户发卡</p>
                </div>
                <div style="color: grey" v-on:click="goSearch"><p>搜索</p></div>
            </div>
        </div>
        <div class="weui-cell">
            <p class="weui-flex__item" style="text-align: center">{{shopInfo.shopName}} <label
                        style="background-color: red;color: white;">加盟商户</label></p>
        </div>
        <div class="weui-cell" style="background-color: #F5F5F5">
            <p class="weui-flex__item" style="color: grey;text-align: center;">发卡人:{{shopInfo.name}}&nbsp;&nbsp;&nbsp;联系方式:{{shopInfo.phone}}</p>
        </div>
        <div class="weui-cells__title" v-on:click="goAllOrder">
            <p weui-flex__item style="text-align: center">今日发卡收入(元)</p>
            <p weui-flex__item style="text-align: center;color: #FF772E;font-size: 32px">+{{chargeInfo.chargeMoney}}</p>
            <p weui-flex__item style="text-align: center">今日发卡单数：{{chargeInfo.chargeNum}}</p>
        </div>
        <div class="weui-btn-area">
            <button v-on:click="goCheckCustomer" class="weui-btn weui-btn_primary">商户发卡</button>
        </div>
        <div class="weui-cells__title">
            <p weui-flex__item style="text-align: center">剩余发卡额度：{{shopInfo.remainQuota}}元 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label
                        v-on:click="goExplain" style="color: grey">会员说明</label></p>
        </div>
        <div class="weui-cell">
            <div class="weui-cells__title" style="width: 50%" v-on:click='goDetail(1)'>
                <p weui-flex__item style="text-align: center;">
                    今日会员消费收入(元)<br>
                    <label style="color: #FF772E;font-size: 26px">+{{consumeInfo.consumMoney}}</label><br>
                    今日交易单数：{{consumeInfo.consumeNum}}
                </p>
            </div>
            <div class="weui-cells__title" style="width: 50%">
                <p weui-flex__item style="text-align: center;" v-on:click='goDetail(2)'>
                    今日会员消费支出(元)<br>
                    <label style="color: #47ABF3;text-align: center;font-size: 26px">-{{payInfo.payMoney}}</label><br>
                    今日交易单数：{{payInfo.payNum}}
                </p>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        Vue.http.options.emulateJSON = true;
        var payInfo = new Vue({
            el: "#home",
            data: {
                result: '',
                shopInfo: '',
                consumeInfo: '',
                payInfo: '',
                chargeInfo: ''
            },
            methods: {
                goCheckCustomer: function () {
                    window.location.href = encodeURI('checkCustomer.php')
                },
                getShopInfo: function () {
                    var info = '<?php echo $shopInfo;?>'
                    var shopInfo = JSON.parse(info)
                    if (shopInfo.code == 200) {
                        this.shopInfo = shopInfo.data
                        this.payInfo = shopInfo.data.payData
                        this.chargeInfo = shopInfo.data.chargeData
                        this.consumeInfo = shopInfo.data.consumeData
                    } else {
                        this.result = shopInfo.msg
                    }
                },
                goDetail: function (type) {
                    window.location.href = encodeURI('consumeDetail.php?type=' + type)
                },
                goAllOrder: function () {
                    window.location.href = encodeURI('shopAllotRecord.php')
                },
                goSearch: function () {
                    window.location.href = encodeURI('search.php')
                },
                goExplain: function () {
                    window.location.href = encodeURI('explain.php')
                },
                back: function () {
                    history.go(-1);
                }
            }
        });
        payInfo.getShopInfo();
    </script>
</body>
</html>