<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style/css/weui.css"/>
    <link rel="stylesheet" href="style/css/weuiadd.css"/>
    <title>充值订单记录</title>
</head>
<body>

<div id="app">
    <div class="container">
        <my-head :title="title">
        </my-head>
        <simple-grid :news-list="newsList" :no-data="noData" :loading="loading" :no-more="noMore" :pay_methods="payMethods" :active_class="activeClass" >
        </simple-grid>
    </div>
</div>
<?php
include "head.php"
?>
<template id="my-template">
    <div v-scroll="loadMore">
        <div class="weui-cell" v-for="(news, index) in newsList">
            <div class="weui-cell__bd">
                <p class="weui-flex__item">订单编号：<span style="font-size: .85em;line-height: 1.6;">{{news.cardOrderNum}}</span></p>
                <p class="weui-flex__item">充值时间：{{news.createTime}}</p>
                <p class="weui-flex__item">充值店铺：{{news.shopname}}</p>
                <p class="weui-flex__item">发&nbsp;&nbsp;卡&nbsp;&nbsp;人：{{news.name}}</p>
                <p class="weui-flex__item">充值金额：{{news.quota}}</p>
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
        el: '#app',
        data: {
            newsList: [],
            loading : false,
            noData : false,
            noMore : false,
            seen : false,
            title : '充值记录',
            apiUrl : 'action.php?action=getCharge',
            items : {'page' :0},
        },
        created: function () {
            this.loadMore();
        },
        methods: {
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
    })
</script>
</body>
</html>