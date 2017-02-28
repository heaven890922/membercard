<template id="head">
    <div class="weui-cells" style="margin-top: 0px;">
        <div class="weui-cell weui-cell_access">
            <div class="weui-cell__hd">
                <a href="#" onClick="javascript :history.back(-1);"><img src="style/images/goback.png" alt="" style="width:40px;margin-right:5px;display:block" ></a>
            </div>
            <div class="weui-cell__bd weui-cell_primary">
                <p style="width: 100%;">{{title}} <span class="search" onclick="location.href='explain.php'" >会员说明</span></p>
            </div>
        </div>
    </div>
</template>