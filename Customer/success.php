<?php
include_once 'config.php';
$orderNum = $_SESSION['orderNum'];
// $orderNum = '201701221531270486188482';
?>

<!DOCTYPE html>
<html>
<head>
    <title>支付完成</title>
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
                    <p>支付完成</p>
                </div>
                <div style="color: grey" v-on:click="goExplain"><p>会员说明</p></div>
            </div>
        </div>
        <div class="weui-cell">
            <p class="weui-flex__item" style="text-align: center;font-size: 28px">支付成功</p>
        </div>
        <div class="weui-cells__title" style="text-align: center;">
            <table width="100%">
                <tr>
                    <td align="right" width="45%">订单编号：</td>
                    <td align="left" width="55%">{{item.orderNum}}</td>
                </tr>
                <tr>
                    <td align="right" width="45%">充值时间：</td>
                    <td align="left" width="55%">{{createTime}}</td>
                </tr>
                <tr>
                    <td align="right" width="45%">充值店铺：</td>
                    <td align="left" width="55%">{{shopname}}</td>
                </tr>
                <tr>
                    <td align="right" width="45%">充值金额：</td>
                    <td align="left" width="55%">{{consumeQuota}}元</td>
                </tr>
            </table>
        </div>
        <div class="weui-cell">
            <p weui-flex__item style="color: red;text-align: center;">{{result}}</p>
        </div>
        <div class="weui-btn-area">
            <button v-on:click="goHome" class="weui-btn weui-btn_primary">完成</button>
        </div>
    </div>
</div>
<script type="text/javascript">
    Vue.http.options.emulateJSON = true;
    var successInfo = new Vue({
        el: "#successInfo",
        data: {
            result: '',
            createTime: '',
            shopname: '',
            consumeQuota: '',
            item: {'orderNum': ''}
        },
        methods: {
            getSuccessInfo: function () {
                this.item.orderNum = '<?php echo $orderNum;?>'
                this.$http.post('action.php?action=orderInfo', this.item).then((response) => {
                    if (response.data.code == 200) {
                        this.consumeQuota = response.data.data[0].consumeQuota
                        this.createTime = response.data.data[0].createTime
                        this.shopname = response.data.data[0].shopname
                    } else {
                        this.result = response.data.msg;
                    }
                })
            },
            goExplain: function () {
                window.location.href = 'explain.php'
            },
            goHome: function () {
                window.location.href = 'home.php'
            },
            back: function () {
                history.go(-1);
            }

        }
    });
    successInfo.getSuccessInfo();
</script>
</body>
</html>