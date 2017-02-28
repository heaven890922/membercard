<?php 
	include_once 'config.php';

	$carNumInfo = isBindCarNum();
	$payPwdInfo = isBindPayPwd();
	$isCar = json_decode($carNumInfo,true);
	$isPwd = json_decode($payPwdInfo,true);
	
	if($isCar['code']==200){
		if($isCar['isCar']==1){
			echo "<script>window.location.href='home.php';</script>";
		}else{
			echo "<script>window.location.href='register.php';</script>";
		}
	}
	if($isPwd['code']==200){
		if($isPwd['isSet']==1){
			echo "<script>window.location.href='home.php';</script>";
		}else{
			echo "<script>window.location.href='register.php';</script>";
		}
	}

 ?>