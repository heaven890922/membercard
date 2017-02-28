<?php
include_once 'config.php';

$money = $_SESSION['money'];
$shopname = $_SESSION['shopname'];
$payTime = date('Y-m-d H:i:s', time());
?>

<!DOCTYPE html>
<html>
<head>
    <title>会员付款</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
    <link rel="stylesheet" href="style/css/weui.css"/>
    <script src="style/js/vue.js"></script>
    <script src="style/js/vue-resource.js"></script>
</head>
<body ontouchstart>
<div class="page">
    <div class="page__bd page__bd_spacing" id="confirmPay">

        <div class="weui-cells">
            <div class="weui-cell weui-cell_access">
                <div class="weui-cell__hd" v-on:click="back">
                    <img src="style/images/goback.png" alt="" style="width:40px;margin-right:5px;display:block">
                </div>
                <div class="weui-cell__bd weui-cell_primary">
                    <p>会员付款</p>
                </div>
                <div style="color: grey" v-on:click="goExplain"><p>会员说明</p></div>
            </div>
        </div>
        <div class="weui-cell"></div>
        <div class="weui-cells__title">
            <p weui-flex__item style="text-align: center;">付款金额(元)</p>
            <p weui-flex__item style="text-align: center;color: #FF772E;font-size: 36px">{{money}}</p>
            <table width="100%" align="center">
                <tr>
                    <td align="right" width="45%">支付店铺：</td>
                    <td align="left" width="55%">{{shopname}}</td>
                </tr>
                <tr>
                    <td align="right" width="45%">支付时间：</td>
                    <td align="left" width="55%">{{payTime}}</td>
                </tr>
            </table>
            <br>
        </div>
        <div class="weui-cell">
            <div class="weui-cell__hd"><label class="weui-label">支付密码:</label></div>
            <div class="weui-cell__bd">
                <input class="weui-input" type="password" placeholder="核对信息后输入支付密码" v-model="item.payPwd"/>
            </div>
        </div>
        <div class="weui-cell">
            <p weui-flex__item style="color: red;text-align: center;">{{result}}</p>
        </div>
        <div class="weui-btn-area">
            <button v-on:click="confirm" class="weui-btn weui-btn_primary">确认付款</button>
            <button v-on:click="cancel" class="weui-btn weui-btn_default">取消付款</button>
        </div>
        <div class="weui-cells__title">
            <p weui-flex__item>付款提示：</p>
            <p weui-flex__item>到店服务点击完成付款，支付的款项将直接进入商户账户，请谨慎操作。</p>
        </div>
    </div>
</div>
<script type="text/javascript">
    Vue.http.options.emulateJSON = true;
    var confirmPay = new Vue({
        el: "#confirmPay",
        data: {
            result: '',
            money: '',
            shopname: '',
            payTime: '',
            item: {'payPwd': ''}
        },
        methods: {
            payInfo: function () {
                this.money = '<?php echo $money;?>'
                this.shopname = '<?php echo $shopname;?>'
                this.payTime = '<?php echo $payTime;?>'
            },
            goExplain: function () {
                window.location.href = 'explain.php'
            },
            confirm: function () {
                this.$http.post('action.php?action=confirmPay', this.item).then((response) => {
                    if (response.data.code == 200) {
                        window.location.href = 'success.php'
                    } else {
                        this.result = response.data.msg;
                    }
                })

            },
            cancel: function () {
                window.location.href = 'home.php'
            },
            back: function () {
                history.go(-1);
            }
        }
    });
    confirmPay.payInfo();
</script>
</body>
</html>