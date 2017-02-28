<!DOCTYPE html>
<html>
<head>
    <title>商户发卡记录</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
    <link rel="stylesheet" href="style/css/weui.css"/>
    <link rel="stylesheet" href="style/css/weuiadd.css"/>
</head>
<body ontouchstart>
    <div class="page" id="app">
        <div class="container">
            <my-head :title="title">
            </my-head>
        </div>
        <the-head :statistics="statistics" :total-money="totalMoney" :total-num="totalNum" :title="title"></the-head>
        <div style="height: 10px; background-color: #f5f5f5; border: 1px solid #ccc;"></div>
        <simple-grid :news-list="newsList" :no-data="noData" :loading="loading" :no-more="noMore" :pay_methods="payMethods" :active_class="activeClass" >
        </simple-grid>
    </div>
    <?php
    include 'head.php';
    ?>
    <template id="header">
        <div>
            <div class="weui-cells__title">
                <p weui-flex__item style="text-align: center">发卡总额(元)</p>
                <p weui-flex__item style="text-align: center;color: #FF772E;font-size: 32px">+ {{totalMoney}}</p>
                <p weui-flex__item style="text-align: center">发卡单数:{{totalNum}}</p>
            </div>
            <div class="weui-cell">
                <div class="weui-cells__title" style="width: 33%;padding-left: 0; padding-right: 0;">
                    <p weui-flex__item style="text-align: center;">
                        现金充值(元)<br>
                        <label style="color: #F42F34;font-size: 16px">+{{statistics.cash.money?statistics.cash.money:0}}</label><br>
                        充值单数:{{statistics.cash.num}}
                    </p>
                </div>
                <div class="weui-cells__title" style="width: 34%;padding-left: 0; padding-right: 0;">
                    <p weui-flex__item style="text-align: center;">
                        微信充值(元)<br>
                        <label style="color: #86C610;text-align: center;font-size: 16px">+{{statistics.wx.money?statistics.wx.money:0}}</label><br>
                        充值单数:{{statistics.wx.num}}
                    </p>
                </div>
                <div class="weui-cells__title" style="width: 33%;padding-left: 0; padding-right: 0;">
                    <p weui-flex__item style="text-align: center;">
                        支付宝充值(元)<br>
                        <label style="color: #47ABF3;text-align: center;font-size: 16px">+{{statistics.al.money?statistics.al.money:0}}</label><br>
                        充值单数:{{statistics.al.num}}
                    </p>
                </div>
            </div>
        </div>
    </template>
    <template id="my-template">
        <div v-scroll="loadMore">
            <div class="weui-cell" v-for="(news, index) in newsList">
                <div class="weui-cell__bd">
                    <p class="weui-flex__item">订单编号：<span style="font-size: .85em;line-height: 1.6;">{{news.cardOrderNum}}</span></p>
                    <p class="weui-flex__item">充值时间：{{news.payTime}}</p>
                    <p class="weui-flex__item">充值金额：{{news.quota}}</p>
                    <p class="weui-flex__item">车主手机：{{news.tel}}</p>
                    <p class="weui-flex__item">车主车牌：{{news.carNum}}</p>
                    <p class="weui-flex__item">发&nbsp;&nbsp;卡&nbsp;&nbsp;人：{{news.name}}</p>
                    <p class="weui-flex__item">支付状态：{{news.payState=='Y'?'已支付':'未支付'}}</p>
                    <p class="weui-flex__item">购卡方式：<span class="pay_method cashPayUrl" v-bind:class="news.port">{{news.payMethodName}}</span></p>
                </div>
            </div>
            <div class="weui-loadmore weui-loadmore_line" v-if="noData">
                <span class="weui-loadmore__tips">暂无数据</span>
            </div>
            <div class="weui-loadmore" v-if="loading">
                <i class="weui-loading"></i>
                <span class="weui-loadmore__tips">正在加载</span>
            </div>
            <div class="weui-loadmore weui-loadmore_line weui-loadmore_dot" v-if="noMore">
                <span class="weui-loadmore__tips"></span>
            </div>
            <p class="weui-flex__item" style="color: red" v-if="seen">{{result}}</p>
        </div>
    </template>

    <script src="style/js/vue.js"></script>
    <script src="style/js/vue-resource.js"></script>
    <script src="style/js/my_template.js"></script>
<script type="text/javascript">
    Vue.http.options.emulateJSON = true;
    let demo = new Vue({
        el : "#app",
        data : {
            title : "发卡记录",
            statistics : {
                "wx": { "num": 0, "money": 0 },
                "al": { "num": 0, "money": 0 },
                "cash": { "num": 0, "money": 0 }
            },
            types : { 'payType' : '1,2,3'},
            totalMoney : 0,
            totalNum : 0,
            newsList: [],
            loading : false,
            noData : false,
            noMore : false,
            apiUrl : 'action.php?action=getOrder',
            items : {'page' :0},
        },
        created:function () {
            this.getInfo();
            this.loadMore();
        },
        methods : {
            getInfo : function () {
                this.$http.post('action.php?action=getAllotRecord',this.types).then(response => {
                    if(response.data.code==200){
                        //this.statistics = response.data.data;
                        let money = 0,num = 0;
                        for(let i in response.data.data) {
                            money += response.data.data[i].money;
                            num += response.data.data[i].num;
                            if (i == 1) {
                                this.statistics.wx = response.data.data[i];
                            } else if (i == 2) {
                                this.statistics.al = response.data.data[i];
                            } else if (i == 3) {
                                this.statistics.cash = response.data.data[i];
                            }
                        }
                        this.totalMoney = money;
                        this.totalNum = num;
                    }else{
                        this.result = response.data.msg
                    }
                })
            },
            loadMore: function () {
                if(this.noData || this.loading || this.noMore) {
                    return false;
                } else {
                    this.loading = true;
                    this.$http.post(this.apiUrl,this.items).then((response) => {
                        if (response.data.code == 200) {
                            if (this.items.page == 0 && response.data.data.length == 0) {
                                this.noData = true;
                            } else {
                                this.items.page++;
                                if (response.data.data.length < 6) {
                                    this.noMore = true
                                }
                                this.newsList = this.newsList.concat(response.data.data);
                            }
                        } else {
                            this.seen=true;
                            this.result = response.data.msg;
                        }
                        this.loading =false;
                    });
                }

            }
        }
    });
</script>
</body>
</html>