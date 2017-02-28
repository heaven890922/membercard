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
    <title>车主扫码充值</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
    <link rel="stylesheet" href="style/css/weui.css"/>
    <script src="style/js/vue.js"></script>
    <script src="style/js/vue-resource.js"></script>
</head>
<body ontouchstart>
<div class="page">
    <div class="page__bd page__bd_spacing" id="qrcodeInfo">
        <div class="weui-cells">
            <div class="weui-cell weui-cell_access">
                <div class="weui-cell__hd" v-on:click="back">
                    <img src="style/images/goback.png" alt="" style="width:40px;margin-right:5px;display:block">
                </div>
                <div class="weui-cell__bd weui-cell_primary">
                    <p>车主扫码充值</p>
                </div>
            </div>
        </div>
        <div class="weui-cells__title" style="margin-left: 5%;font-size: 1.05em">
            <p weui-flex__item>发卡店铺：{{shopName}}</p>
            <p weui-flex__item>发&nbsp;&nbsp;卡&nbsp;&nbsp;人：{{name}}</p>
            <p weui-flex__item>支付方式：{{payType}}</p>
            <p weui-flex__item>充值金额：{{money}}元</p>
            <p weui-flex__item>车主电话：{{tel}}</p>
            <p weui-flex__item>车牌号码：{{carNum.toUpperCase()}}</p>
        </div>
        <div class="weui-cells__title">
            <div class="weui-cell__bd">
                <p weui-flex__item style="text-align: center;"><img v-bind:src="qrcodeImg"
                                                                    style="width: 280px;height: 280px"></p>
                <p weui-flex__item style="text-align: center;">请车主用微信扫码支付</p>
                <p weui-flex__item style="text-align: center;">二维码10分钟后作废</p>
            </div>
        </div>
        <div class="weui-cell">
            <p weui-flex__item style="color: red;text-align: center">{{result}}</p>
        </div>
        <div class="weui-btn-area">
            <button v-on:click="goSuccess" class="weui-btn weui-btn_primary">扫码完成，充值成功</button>
        </div>
    </div>
</div>
<script type="text/javascript">
    Vue.http.options.emulateJSON = true;
    var qrcodeInfo = new Vue({
        el: "#qrcodeInfo",
        data: {
            result: '',
            qrcodeImg: '',
            tel: '',
            carNum: '',
            money: '',
            payType: '',
            name: '',
            shopName: '',
            order: {'orderNum': ''},
            item: {'discount': '', 'remarks': 'remarks'}
        },
        methods: {
            getQrcode: function () {
                this.$http.post('action.php?action=getQrcode', this.item).then((response) => {
                    if (response.data.code == 200) {
                        this.qrcodeImg = 'http://120.76.251.222:9980/membercard/tp5/public/' + response.data.png
                        this.order.orderNum = response.data.orderNum
                    } else {
                        this.result = response.data.msg
                    }
                })
            },
            goSuccess: function () {
                this.$http.post('action.php?action=checkOrder', this.order).then((response) => {
                    if (response.data.code == 200) {
                        var payState = response.data.data[0].payState;
                        if (payState == 'N') {
                            this.result = '订单未支付'
                        } else {
                            window.location.href = encodeURI('success.php')
                        }
                    } else {
                        this.result = response.data.msg
                    }
                })
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
    qrcodeInfo.getQrcode();
    qrcodeInfo.showPayInfo();
</script>
</body>
</html>