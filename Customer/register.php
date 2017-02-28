<?php include_once 'config.php'; ?>

<!DOCTYPE html>
<html>
<head>
    <title>完善信息</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
    <link rel="stylesheet" href="style/css/weui.css"/>
    <script src="style/js/vue.js"></script>
    <script src="style/js/vue-resource.js"></script>
</head>
<body ontouchstart>
<div class="page">
    <div class="page__bd page__bd_spacing" id="registerInfo">
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
            <div class="weui-cell__hd"><label class="weui-label">车主电话：</label></div>
            <div class="weui-cell__bd">
                <label class="weui-label">{{tel}}</label>
            </div>
        </div>
        <div class="weui-cell">
            <div class="weui-cell__hd"><label class="weui-label">车牌号码：</label></div>
            <div class="weui-cell__bd">
                <input class="weui-input" placeholder="请输入车牌号码" v-model="item.carNum"
                       style="text-transform:uppercase;"/>
            </div>
        </div>
        <div class="weui-cell">
            <div class="weui-cell__hd"><label class="weui-label">支付密码：</label></div>
            <div class="weui-cell__bd">
                <input class="weui-input" type="password" placeholder="绑定需输入6位支付密码" v-model="item.payPwd"/>
            </div>
        </div>
        <div class="weui-cell">
            <p weui-flex__item style="color: red;text-align: center;">{{result}}</p>
        </div>
        <div class="weui-btn-area">
            <button v-on:click="bindInfo" class="weui-btn weui-btn_primary">确认绑定</button>
        </div>
    </div>
</div>
<script type="text/javascript">
    Vue.http.options.emulateJSON = true;
    var registerInfo = new Vue({
        el: "#registerInfo",
        data: {
            result: '',
            tel: '',
            item: {'carNum': '', 'payPwd': ''}
        },
        methods: {
            bindInfo: function () {
                this.$http.post('action.php?action=bindCarNum', this.item).then((response) => {
                    if (response.data.code == 200) {
                        window.location.href = 'home.php'
                    } else {
                        this.result = response.data.msg;
                    }
                });
            },
            getTel: function () {
                this.tel = '<?php echo $tel;?>'
            },
            goExplain: function () {
                window.location.href = 'explain.php'
            },
            back: function () {
                history.go(-1);
            }
        }
    });
    registerInfo.getTel();
</script>
</body>
</html>