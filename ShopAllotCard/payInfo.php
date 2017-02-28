<?php include_once 'config.php'; ?>

<!DOCTYPE html>
<html>
<head>
    <title>支付信息</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
    <link rel="stylesheet" href="style/css/weui.css"/>
    <script src="style/js/vue.js"></script>
    <script src="style/js/vue-resource.js"></script>
</head>
<body ontouchstart>
<div class="page">
    <div class="page__bd page__bd_spacing" id="payInfo">
        <div class="weui-cells">
            <div class="weui-cell weui-cell_access">
                <div class="weui-cell__hd" v-on:click="back">
                    <img src="style/images/goback.png" alt="" style="width:40px;margin-right:5px;display:block">
                </div>
                <div class="weui-cell__bd weui-cell_primary">
                    <p>输入充值金额</p>
                </div>
            </div>
        </div>
        <div class="weui-cell">
            <div class="weui-cell__hd"><label class="weui-label">￥</label></div>
            <div class="weui-cell__bd">
                <input class="weui-input" type="number" placeholder="输入100的整数倍" v-model="item.money"/>
            </div>
        </div>
        <div class="weui-cell"></div>
        <div class="weui-cells weui-cells_radio">
            <div class="weui-cell">
                <p class="weui-flex__item" style="color: grey">选择支付方式:</p>
            </div>
            <label class="weui-cell weui-check__label" for="cash">
                <div class="weui-cell__bd" valign="center">
                    <p><img src="style/images/cashpay.png" align="top" style="width:32px;margin-right:20px;">发卡人收取现金</p>
                </div>
                <div class="weui-cell__ft">
                    <input type="radio" class="weui-check" value="cash" v-model="item.payType" id="cash">
                    <span class="weui-icon-checked"></span>
                </div>
            </label>
            <label class="weui-cell weui-check__label" for="wechat">
                <div class="weui-cell__bd">
                    <p><img src="style/images/wxpay.png" align="top" style="width:32px;margin-right:20px;">微信充值</p>
                </div>
                <div class="weui-cell__ft">
                    <input type="radio" value="wechat" v-model="item.payType" class="weui-check" id="wechat">
                    <span class="weui-icon-checked"></span>
                </div>
            </label>
            <label class="weui-cell weui-check__label" for="alipay">
                <div class="weui-cell__bd" valign="center">
                    <p><img src="style/images/alipay.png" align="top" style="width:32px;margin-right:20px;">支付宝充值</p>
                </div>
                <div class="weui-cell__ft">
                    <input type="radio" value="apliay" v-model="item.payType" class="weui-check" id="alipay">
                    <span class="weui-icon-checked"></span>
                </div>
            </label>
        </div>
        <div class="weui-cell">
            <p weui-flex__item style="color: red;text-align: center">{{result}}</p>
        </div>
        <div class="weui-btn-area">
            <button v-on:click="checkPayInfo" class="weui-btn weui-btn_primary">下一步</button>
        </div>
        <div class="weui-cells__title" style="text-align: center">
            <p weui-flex__item>车主单日购买金额上限为1000元</p>
            <p weui-flex__item>车主充值最高金额上限为3000元</p>
        </div>
    </div>
</div>
<script type="text/javascript">
    Vue.http.options.emulateJSON = true;
    var payInfo = new Vue({
        el: "#payInfo",
        data: {
            result: '',

            item: {'money': '', 'payType': ''}
        },
        methods: {
            checkPayInfo: function () {
                if (this.item.payType == '') {
                    this.result = '请选择支付方式'
                    return false
                }
                ;
                this.$http.post('action.php?action=checkMoney', this.item).then((response) => {
                    if (response.data.code == 200) {
                        window.location.href = encodeURI('checkPassword.php');
                    } else {
                        this.result = response.data.msg
                    }
                })
            },
            back: function () {
                history.go(-1);
            }
        }
    })
</script>
</body>
</html>