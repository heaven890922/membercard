<!DOCTYPE html>
<html>
<head>
    <title>会员卡消费记录</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
    <link rel="stylesheet" href="style/css/weui.css"/>
    <link rel="stylesheet" href="style/css/weuiadd.css"/>
    <script src="style/js/vue.js"></script>
    <script src="style/js/vue-resource.js"></script>
</head>
<body ontouchstart>
<!-- 定义三个temp模板，用于切换 -->
<template id="my-template">
    <div v-scroll="loadMore">
        <div class="weui-cell" v-for="(news, index) in income.newsList">
            <div class="weui-cell__bd">
                <p class="weui-flex__item">订单编号：{{news.consumeOrderNum}}</p>
                <p class="weui-flex__item">充值时间：{{news.createTime}}</p>
                <p class="weui-flex__item">消费金额：{{news.consumeQuota}}</p>
                <p class="weui-flex__item">车主手机：{{news.tel}}</p>
                <p class="weui-flex__item">车主车牌：{{news.carNum}}</p>
            </div>
        </div>
        <div class="weui-loadmore weui-loadmore_line" v-if="income.noData" >
            <span class="weui-loadmore__tips">暂无数据</span>
        </div>
        <div class="weui-loadmore" v-if="income.loading">
            <i class="weui-loading"></i>
            <span class="weui-loadmore__tips">正在加载</span>
        </div>
        <div class="weui-loadmore weui-loadmore_line weui-loadmore_dot" v-if="income.noMore">
            <span class="weui-loadmore__tips"></span>
        </div>
        <p class="weui-flex__item" style="color: red" v-if="seen">{{result}}</p>
    </div>
</template>
<template id="temp-tab02">
    <div v-scroll="loadMore">
        <div class="weui-cell" v-for="(news, index) in expend.newsList">
            <div class="weui-cell__bd">
                <p class="weui-flex__item">订单编号：{{news.consumeOrderNum}}</p>
                <p class="weui-flex__item">充值时间：{{news.createTime}}</p>
                <p class="weui-flex__item">消费金额：{{news.consumeQuota}}</p>
                <p class="weui-flex__item">车主手机：{{news.tel}}</p>
                <p class="weui-flex__item">车主车牌：{{news.carNum}}</p>
                <p class="weui-flex__item">消费类型：{{news.changeType}}</p>
            </div>
        </div>
        <div class="weui-loadmore weui-loadmore_line"  v-if="expend.noData">
            <span class="weui-loadmore__tips">暂无数据</span>
        </div>
        <div class="weui-loadmore" v-if="expend.loading">
            <i class="weui-loading"></i>
            <span class="weui-loadmore__tips">正在加载</span>
        </div>
        <div class="weui-loadmore weui-loadmore_line weui-loadmore_dot" v-if="expend.noMore">
            <span class="weui-loadmore__tips"></span>
        </div>
        <p class="weui-flex__item" style="color: red" v-if="seen">{{result}}</p>
    </div>
</template>
<div id="app">
    <div class="container">
        <my-head :title="title">
        </my-head>
    </div>
    <div class="weui-tab">
        <div class="weui-navbar">
            <div class="weui-navbar__item" v-bind:class="{'weui-bar__item_on':table1}" @click="toggleTabs(tab01Text);">
                会员消费收入
            </div>
            <div class="weui-navbar__item " v-bind:class="{'weui-bar__item_on':table2}" @click="toggleTabs(tab02Text);">
                会员消费支出
            </div>
        </div>
        <div class="weui-tab__panel">
            <component :is="currentView" :income="income" :expend="expend"  keep-alive></component>
        </div>
    </div>
</div>
<?php
include 'head.php';
?>
<script src="style/js/my_template.js"></script>
<script type="text/javascript">
    Vue.http.options.emulateJSON = true;
    //扩展组件tab01
    var tab01 = Vue.extend({
        template: '#my-template',
        props: ['income'],
        directives: {
            scroll: {
                bind: function (el, binding) {
                    window.addEventListener('scroll', () => {
                        if (document.body.scrollTop + window.innerHeight >= document.body.clientHeight) {
                            demo.loadMore();
                        }
                    })
                }
            }
        }
    });
    //扩展组件tab02
    var tab02 = Vue.extend({
        template: '#temp-tab02',
        props: ['expend'],
        directives: {
            scroll: {
                bind: function (el, binding) {
                    window.addEventListener('scroll', () => {
                        if (document.body.scrollTop + window.innerHeight >= document.body.clientHeight) {
                            demo.loadMore();
                        }
                    })
                }
            }
        }
    });

    //新建vue实例
    var demo = new Vue({
        el: "#app",
        data: {
            tab01Text: "tab01", //导航栏文本1
            tab02Text: "tab02", //导航栏文本2
            currentView: 'tab01', //默认选中的导航栏
            title: "消费记录", //标题title
            income: {
                newsList: [],
                loading: false,
                noData: false,
                noMore: false,
                seen: false,
                items: {'type': '1', 'page': 0},
                result : "",
            },
            expend: {
                newsList: [],
                loading: false,
                noData: false,
                noMore: false,
                seen: false,
                items: {'type': '2', 'page': 0},
                result : "",
            },
            table1: true,
            table2: false,
            apiUrl: "action.php?action=getDetail",
            url:decodeURI(location.href),
        },
        //局部注册组件
        components: {
            tab01: tab01,
            tab02: tab02,
        },
        mounted: function () {
            let tmp = this.url.split("?")[1];
            let type = tmp.split("&")[0].split("=")[1];
            if (type == 2) {
                this.toggleTabs('tab02');
            }
            this.loadMore();
        },
        methods: {
            //绑定tab的切换事件
            toggleTabs: function (tabText) {
                this.currentView = tabText;
                if (tabText == 'tab01') {
                    this.table1 = true;
                    this.table2 = false;
                    if (this.income.newsList.length == 0) {
                        this.loadMore();
                    }
                } else {
                    this.table2 = true;
                    this.table1 = false;
                    if (this.expend.newsList.length == 0) {
                        this.loadMore();
                    }
                }
            },
            loadMore: function () {

                if (this.table1) {
                    let income = this.income;
                    if (income.noData || income.loading || income.noMore ) {
                        return false;
                    } else {
                        income.loading = true;
                        this.$http.post(this.apiUrl, income.items).then((response) => {
                            if (response.data.code == 200) {
                                if (income.newsList.length == 0 && response.data.income.length == 0) {
                                    income.noData = true;
                                } else {
                                    income.items.page++;
                                    if (response.data.income.length < 6) {
                                        income.noMore = true;
                                    }
                                    income.newsList = income.newsList.concat(response.data.income);
                                }
                            } else {
                                income.seen = true;
                                income.result = response.data.msg;
                            }
                            income.loading = false;
                        });
                    }
                } else {
                    let expend = this.expend;
                    if (expend.noData || expend.loading || expend.noMore) {
                        return false;
                    } else {
                        expend.loading = true;
                        this.$http.post(this.apiUrl, expend.items).then((response) => {
                            if (response.data.code == 200) {
                                if (expend.newsList.length == 0 && response.data.expend.length == 0) {
                                    expend.noData = true;
                                } else {
                                    expend.items.page++;
                                    if (response.data.expend.length < 6) {
                                        expend.noMore = true
                                    }
                                    this.expend.newsList = this.expend.newsList.concat(response.data.expend);
                                }
                            } else {
                                expend.seen = true;
                                expend.result = response.data.msg;
                            }
                            expend.loading = false;
                        });
                    }
                }
            },
        }
    });
</script>
</body>
</html>