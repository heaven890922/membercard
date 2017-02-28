<?php include_once 'config.php';?>

<!DOCTYPE html>
<html>
<head>
	<title>校验客户信息</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
	<link rel="stylesheet" href="style/css/weui.css"/>
	<script src="style/js/vue.js"></script>
	<script src="style/js/vue-resource.js"></script>
    <style type="text/css">
    <!--
    .carInfo {
    width:21%;
    height:10%;
    display: inline-block;
    text-decoration: none;
    text-align: center;
    float: left;
    margin-left: 9px;
    margin-top:3%;

    border-radius: 5px;
    -moz-border-radius: 5px;
    -webkit-border-radius: 5px;

    -moz-box-shadow: 0 1px 2px rgba(0,0,0,0.5);
    -webkit-box-shadow: 0 1px 2px rgba(0,0,0,0.5);
    text-shadow: 0 -1px 1px rgba(0,0,0,0.25);
    border-bottom: 1px solid rgba(0,0,0,0.25);
    position: relative;
    cursor: pointer;
    }
    -->
    </style>

</head>
<body ontouchstart>
<div class="page">
    <div class="page__bd page__bd_spacing" id="checkCustomerInfo">
                <div class="weui-cells">
                    <div class="weui-cell weui-cell_access">
                        <div class="weui-cell__hd" v-on:click="back">
                            <img src="style/images/goback.png" alt="" style="width:40px;margin-right:5px;display:block" >
                        </div>
                        <div class="weui-cell__bd weui-cell_primary">
                            <p>输入购卡人信息</p>
                        </div>
                    </div>
                </div>
                <div class="weui-cell">
                    <div class="weui-cell__hd"><label class="weui-label">手机号码</label></div>
                    <div class="weui-cell__bd">
                        <input class="weui-input" type="tel" placeholder="请输入手机号码" v-model="item.tel"/>
                    </div>
                </div>
                <div class="weui-cell">
                    <div class="weui-cell__hd"><label class="weui-label" >&nbsp;&nbsp;<a v-on:click="getCar" class="weui-btn weui-btn_mini weui-btn_default" style="font-size: 16px;">{{name}}</a></label></div>
                    <div class="weui-cell__bd">
                        <input class="weui-input"  placeholder="请输入车牌号码" v-model="num" style="text-transform:uppercase;"/>
                    </div>
                </div>
                <div class="weui-cell">
                    <p weui-flex__item style="color: red;text-align: center;">{{result}}</p>
                </div>
                <div class="weui-btn-area">
                    <button v-on:click="getData" class="weui-btn weui-btn_primary">下一步</button>
                </div>
        <div class="weui-cells__title">
        	<p weui-flex__item>充值提示：</p>
        	<p weui-flex__item>充值前车主先在小车生活账户中绑定自己的车牌，电话要输入小车生活注册账户的电话，与车牌一致才能充值成功。</p>
        </div>
        <div class="weui-cells__title" >
            <div class="weui-cell__bd" v-show="show">
                <p weui-flex__item style="text-align: center;"><img v-bind:src="qrcodeImg" style="width: 280px;height: 280px"></p>
            </div>
        </div>
        <div style="position: absolute;bottom: 0px; width: 100%;background-color: #EFEFEF; display: none;" v-show="display">
                <div v-for="item in carInfo">
                    <div v-on:click="backCar(item)" class="carInfo">{{item}}</div>
                </div>
                <div v-on:click="close"  class="carInfo" style="width: 45%">关闭</div>
                <div style="clear: both;"></div>
                <div style="height: 8px; width: 100%;"></div>
        </div>
    </div>
</div>

<script type="text/javascript">
	Vue.http.options.emulateJSON = true;
	var checkInfo = new Vue({
		el:"#checkCustomerInfo",
		data:{
			result:'',
            qrcodeImg:'',
            show:false,
            name:'粤',
            num:'',
            display:false,
            carInfo:['粤','京','浙','津','皖','泸','闽','渝','赣','港','鲁','澳','豫','蒙','鄂','新','湘','宁','藏','琼','桂','川','冀','贵','晋','云','辽','陕','吉','甘','黑','青','苏','台'],
			item:{'tel':'','carNum':''}
		},
		methods:{
			getData:function(){
                this.item.carNum = this.name+this.num
				this.$http.post('action.php?action=checkCustomer',this.item).then((response)=>{
                    if(response.data.code==200){
                        window.location.href=encodeURI('payInfo.php');
                    }else if(response.data.code==802){
                        this.result = response.data.msg
                        this.show = true
                        this.qrcodeImg = 'style/images/qrcode.jpg'
                    }else{
                        this.show = false
                        this.result = response.data.msg
                    }
				})
			},
            back:function(){
                history.go(-1);
            },
            getCar:function(){

                this.display = true
            },
            backCar:function(name){
                this.name = name
                this.display = false
            },
            close:function(name){
                this.display = false
            }
		}
	});
</script>
</body>
</html>