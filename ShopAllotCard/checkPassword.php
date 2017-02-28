<?php
include_once 'config.php';
$tel = $_SESSION['tel'];
$carNum = $_SESSION['carNum'];
$money = $_SESSION['money'];
$payType = $_SESSION['payType'];

?>

<!DOCTYPE html>
<html>
<head>
    <title>输入支付密码</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
    <link rel="stylesheet" href="style/css/weui.css"/>
    <script src="style/js/vue.js"></script>
    <script src="style/js/vue-resource.js"></script>
</head>
<body ontouchstart>
<div class="page">
    <div class="page__bd page__bd_spacing" id="checkPwdInfo">
        <div class="weui-cells">
            <div class="weui-cell weui-cell_access">
                <div class="weui-cell__hd" v-on:click="back">
                    <img src="style/images/goback.png" alt="" style="width:40px;margin-right:5px;display:block">
                </div>
                <div class="weui-cell__bd weui-cell_primary">
                    <p>输入支付密码</p>
                </div>
            </div>
        </div>
        <div class="weui-cells__title" style="font-size: 1.05em">
            <p weui-flex__item>车主电话：{{tel}}</p>
            <p weui-flex__item>车牌号码：{{carNum.toUpperCase()}}</p>
            <p weui-flex__item>充值金额：{{money}}元</p>
            <p weui-flex__item>支付方式：{{payType}}</p>
            <br>
        </div>
        <div class="weui-cell">
            <div class="weui-cell__bd">
                <input class="weui-input" type="password" placeholder="核对信息后在这里输入密码" v-model="item.payPwd"/>
            </div>
        </div>
        <div class="weui-cell">
            <p weui-flex__item style="color: red;text-align: center">{{result}}</p>
        </div>
        <div class="weui-btn-area">
            <button v-on:click="checkPayInfo" class="weui-btn weui-btn_primary">下一步</button>
        </div>
    </div>
    <div class="weui-cells__title">
        <p weui-flex__item>密码提示：</p>
        <p weui-flex__item>先核对顶部信息，然后输入支付密码。支付密码与积分商城密码为同一密码，忘记密码请拔打4000006313咨询客服</p>
    </div>
</div>
<script type="text/javascript">
    Vue.http.options.emulateJSON = true;
    var checkPwdInfo = new Vue({
        el: "#checkPwdInfo",
        data: {
            result: '',
            tel: '',
            carNum: '',
            money: '',
            payType: '',
            item: {'payPwd': ''}
        },
        methods: {
            checkPayInfo: function () {
                this.$http.post('action.php?action=checkPayPwd', this.item).then((response) => {
                    if (response.data.code == 200) {
                        window.location.href = encodeURI('showQrcode.php');
                    } else {
                        this.result = response.data.msg
                    }
                })
            },
            showPayInfo: function () {
                this.tel = '<?php echo $tel;?>'
                this.carNum = '<?php echo $carNum;?>'
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
    checkPwdInfo.showPayInfo();
</script>
</body>
</html>