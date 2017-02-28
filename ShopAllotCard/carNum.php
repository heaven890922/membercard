<!DOCTYPE html>
<html>
<head>
    <title>选择地区</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
    <link rel="stylesheet" href="style/css/weui.css"/>
    <script src="style/js/vue.js"></script>
    <script src="style/js/vue-resource.js"></script>
</head>
<body ontouchstart>
<div class="page">
    <div class="page__bd page__bd_spacing" id="explain">
        <div class="button-sp-area">
            <a href="javascript:;" class="weui-btn weui-btn_plain-default">按钮</a>
            <a href="javascript:;" class="weui-btn weui-btn_plain-default weui-btn_plain-disabled">按钮</a>
            <a href="javascript:;" class="weui-btn weui-btn_plain-primary">按钮</a>
            <a href="javascript:;" class="weui-btn weui-btn_plain-primary weui-btn_plain-disabled">按钮</a>
            <a href="javascript:;" class="weui-btn weui-btn_mini weui-btn_primary">按钮</a>
            <a href="javascript:;" class="weui-btn weui-btn_mini weui-btn_default">按钮</a>
            <a href="javascript:;" class="weui-btn weui-btn_mini weui-btn_warn">按钮</a>
        </div>
        <div class="weui-cell">
            <div class="weui-cells__title">
                <p weui-flex__item>发卡说明：</p>
                <p weui-flex__item>1、小车生活加盟商户可以通过平台给店铺会员车主发卡。</p>
                <p weui-flex__item>2、商户发卡人给车主充值后可以收取现金或选择网络支付，网络支付金额直接充值到可提现金额。</p>
                <p weui-flex__item>3、在车主消费后会在账户中扣取相应金额。</p>
                <p weui-flex__item>4、车主充值后可以在所有加盟店使用，并享受会员特权。</p>
                <p weui-flex__item>5、发卡限制如下：</p>
                <p weui-flex__item>单日车主购买金额上限限制1000元；车主充值最高金额上限限制3000元；单日车主支付金额上限限制1000元；车主月度流水金额上限限制10000元。</p>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    Vue.http.options.emulateJSON = true;
    var explain = new Vue({
        el: "#explain",
        data: {
            result: ''
        },
        methods: {
            back: function () {
                history.go(-1);
            }
        }
    });
</script>
</body>
</html>