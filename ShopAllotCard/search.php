<?php include_once 'config.php'; ?>

<!DOCTYPE html>
<html>
<head>
    <title>搜索</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
    <link rel="stylesheet" href="style/css/weui.css"/>
    <script src="style/js/vue.js"></script>
    <script src="style/js/vue-resource.js"></script>
</head>
<body ontouchstart>
<div class="page">
    <div class="page__bd page__bd_spacing" id="searchInfo">
        <div class="weui-cells">
            <div class="weui-cell weui-cell_access">
                <div class="weui-cell__hd" v-on:click="back">
                    <img src="style/images/goback.png" alt="" style="width:40px;margin-right:5px;display:block">
                </div>
                <div class="weui-cell__bd weui-cell_primary">
                    <p>搜索</p>
                </div>
            </div>
        </div>
        <div class="weui-cell">
            <div class="weui-cell__bd">
                <input class="weui-input" placeholder="请输入手机号码/车牌号码" v-model="item.search"/>
            </div>
        </div>
        <div class="weui-cell">
        </div>
        <div class="weui-btn-area">
            <button v-on:click="getOrderInfo" class="weui-btn weui-btn_primary">搜索</button>
            <p weui-flex__item style="color: red;text-align: center;">{{result}}</p>
        </div>
        <div class="weui-cell" v-for="list in chargeInfo" v-if="show">
            <div class="weui-cell__bd">
                <p class="weui-flex__item"><label style="background-color: #FF772E;color: white;">会员充值</label></p>
                <p class="weui-flex__item">订单编号：<span
                            style="font-size: .75em;line-height: 1.6;">{{list.cardOrderNum}}</span></p>
                <p class="weui-flex__item">充值时间：{{list.payTime}}</p>
                <p class="weui-flex__item">充值金额：{{list.quota}}</p>
                <p class="weui-flex__item">车主手机：{{list.tel}}</p>
                <p class="weui-flex__item">车牌号码：{{list.carNum}}</p>
                <p class="weui-flex__item">发&nbsp;&nbsp;卡&nbsp;&nbsp;人：{{list.name}}</p>
                <p class="weui-flex__item">购卡方式：
                    <label style="background-color: #86C610;color: white;">{{list.payMethodName}}</label></p>
            </div>
        </div>
        <div class="weui-cell" v-for="list in consumeInfo" v-if="show">
            <div class="weui-cell__bd">
                <p class="weui-flex__item" v-if="list.changeType=='IN'"><label
                            style="background-color: red;color: white;">消费收入</label></p>
                <p class="weui-flex__item" v-else><label style="background-color: blue;color: white;">消费支出</label></p>
                <p class="weui-flex__item">订单编号：<span style="font-size: .85em;line-height: 1.6;">{{list.consumeOrderNum}}</span>
                </p>
                <p class="weui-flex__item">消费时间：{{list.createTime}}</p>
                <p class="weui-flex__item">消费金额：{{list.consumeQuota}}</p>
                <p class="weui-flex__item">车主手机：{{list.tel}}</p>
            </div>
        </div>
        <div class="weui-loadmore weui-loadmore_line" v-if="noData" >
            <span class="weui-loadmore__tips">查无数据</span>
        </div>
        <div class="weui-loadmore" v-if="loading">
            <i class="weui-loading"></i>
            <span class="weui-loadmore__tips">正在加载</span>
        </div>
        <div class="weui-loadmore weui-loadmore_line weui-loadmore_dot" v-if="noMore">
            <span class="weui-loadmore__tips"></span>
        </div>
    </div>
</div>
<script type="text/javascript">
    var shopID = '<?php echo $shopID;?>';
    Vue.http.options.emulateJSON = true;
    var searchInfo = new Vue({
        el: "#searchInfo",
        data: {
            result: '',
            chargeInfo: '',
            consumeInfo: '',
            show: false,
            item: {'search': ''},
            noData : false,
            loading : false,
            noMore : false
        },
        methods: {
            getOrderInfo: function () {
                this.loading = true;
                this.$http.post('action.php?action=search', this.item).then((response) => {
                    this.loading = false;
                    if (response.data.code == 200) {
                        this.show = true;
                        this.result = '';
                        this.chargeInfo = response.data.charge;
                        this.consumeInfo = response.data.consume;
                        if (response.data.charge.length == 0 && response.data.consume == 0){
                            this.noData = true;
                            this.noMore =false;
                        } else {
                            this.noData = false;
                            this.noMore = true;
                        }

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