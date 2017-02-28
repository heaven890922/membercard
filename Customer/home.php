<?php include_once 'config.php'; ?>

<!DOCTYPE html>
<html>
<head>
    <title>会员登录</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
    <link rel="stylesheet" href="style/css/weui.css"/>
    <script src="style/js/vue.js"></script>
    <script src="style/js/vue-resource.js"></script>
</head>
<body ontouchstart>
<div class="page">
    <div class="page__bd page__bd_spacing" id="homeInfo">

        <div class="weui-cells">
            <div class="weui-cell weui-cell_access">
                <div class="weui-cell__hd" v-on:click="back">
                    <img src="style/images/goback.png" alt="" style="width:40px;margin-right:5px;display:block">
                </div>
                <div class="weui-cell__bd weui-cell_primary">
                    <p>小车会员</p>
                </div>
                <div style="color: grey" v-on:click="goExplain"><p>会员说明</p></div>
            </div>
        </div>
        <div class="weui-cell">
            <div class="weui-cell__hd"><label class="weui-label">车主电话:</label></div>
            <div class="weui-cell__bd"><label class="weui-label">{{tel}}</label></div>
        </div>
        <div class="weui-cell">
            <div class="weui-cell__hd"><label class="weui-label">车牌号码:</label></div>
            <div class="weui-cell__bd"><label class="weui-label">{{carNum}}</label></div>
        </div>
        <div class="weui-cell"></div>
        <div class="weui-cells__title" style="text-align: center">
            <p weui-flex__item>会员账户余额(元)</p>
            <p weui-flex__item style="text-align: center;color: #FF772E;font-size: 36px">{{balance}}</p>
            <p weui-flex__item style="color: red;text-align: center;">{{result}}</p>
            <p weui-flex__item>
                <button v-on:click="payMoney" class="weui-btn weui-btn_primary">到店付款</button>
            </p>
        </div>
        <div style="margin-top:3%">
            <div style="width: 50%;float: left;" v-on:click='goChargeRecord'>
                <p weui-flex__item style="text-align: center;color:grey">充值记录</p>
            </div>
            <div style="width: 50%;float: left;" v-on:click='goConsumeRecord'>
                <p weui-flex__item style="text-align: center;color:grey">消费记录</p>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    Vue.http.options.emulateJSON = true;
    var homeInfo = new Vue({
        el: "#homeInfo",
        data: {
            result: '',
            tel: '',
            carNum: '',
            balance: '',
            item: {}
        },
        methods: {
            payMoney: function () {
                window.location.href = encodeURI('payInfo.php');
            },
            getInfo: function () {
                this.$http.post('action.php?action=getInfo', this.item).then((response) => {
                    if (response.data.code == 200) {
                        this.tel = response.data.info.tel
                        this.carNum = response.data.info.carNum
                        this.balance = response.data.info.balance
                    } else {
                        this.result = response.data.msg;
                    }
                })
            },
            goExplain: function () {
                window.location.href = 'explain.php'
            },
            goChargeRecord: function () {
                window.location.href = 'chargeRecord.php'
            },
            goConsumeRecord: function () {
                window.location.href = 'consumeRecord.php'
            },
            back: function () {
                history.go(-1);
            }
        }
    });
    homeInfo.getInfo();
</script>
</body>
</html>