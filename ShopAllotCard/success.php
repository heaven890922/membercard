<?php
include_once 'config.php';
$shopName = $_SESSION['shopName'];
$name = $_SESSION['name'];
$payType = $_SESSION['payType'];
$money = $_SESSION['money'];
$tel = $_SESSION['tel'];
$carNum = $_SESSION['carNum'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>充值完成</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
    <link rel="stylesheet" href="style/css/weui.css"/>
    <script src="style/js/vue.js"></script>
    <script src="style/js/vue-resource.js"></script>
</head>
<body ontouchstart>
<div class="page">
    <div class="page__bd page__bd_spacing" id="successInfo">
        <div class="weui-cells">
            <div class="weui-cell weui-cell_access">
                <div class="weui-cell__hd" v-on:click="back">
                    <img src="style/images/goback.png" alt="" style="width:40px;margin-right:5px;display:block">
                </div>
                <div class="weui-cell__bd weui-cell_primary">
                    <p>扫码支付完成发卡成功</p>
                </div>
            </div>
        </div>
        <div class="weui-cell">
            <p class="weui-flex__item" style="text-align: center">扫码支付完成发卡成功</p>
        </div>
        <div class="weui-cells__title">
            <p weui-flex__item>发卡店铺：{{shopName}}</p>
            <p weui-flex__item>发&nbsp;&nbsp;卡&nbsp;&nbsp;人：{{name}}</p>
            <p weui-flex__item>支付方式：{{payType}}</p>
            <p weui-flex__item>充值金额：{{money}}</p>
            <p weui-flex__item>车主电话：{{tel}}</p>
            <p weui-flex__item>车牌号码：{{carNum}}</p>
        </div>
        <div class="weui-btn-area">
            <button v-on:click="goHome" class="weui-btn weui-btn_primary">完成</button>
        </div>
    </div>
</div>
<script type="text/javascript">
    Vue.http.options.emulateJSON = true;
    var payInfo = new Vue({
        el: "#successInfo",
        data: {
            tel: '',
            carNum: '',
            money: '',
            payType: '',
            name: '',
            shopName: ''
        },
        methods: {
            goHome: function () {
                window.location.href = 'home.php';
            },
            showPayInfo: function () {
                this.tel = '<?php echo $tel;?>'
                this.carNum = '<?php echo $carNum;?>'
                this.shopName = '<?php echo $shopName;?>'
                this.name = '<?php echo $name;?>'
                this.money = '<?php echo $money;?>'
                this.payType = '<?php echo $payType;?>'
                if (this.payType == 'cash') {
                    this.payType = '发卡人收取现金'
                } else if (this.payType == 'wechat') {
                    this.payType = '微信充值'
                } else {
                    this.payType = '支付宝充值'
                }
            },
            back: function () {
                history.go(-1);
            }
        }
    });
    payInfo.showPayInfo();
</script>
</body>
</html>