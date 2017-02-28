<?php 
	include_once 'config.php';
	
	$action = $_REQUEST['action'];

	if($action=='checkCustomer'){
		$tel = $_REQUEST['tel'];
		$carNum = $_REQUEST['carNum'];
		$outPut = checkCustomer($tel,$carNum);
		$res = json_decode($outPut,true);
		
		if($res['code']==200){
			$_SESSION['tel'] = $tel;
			$_SESSION['carNum'] = $carNum;
			$_SESSION['customerID'] = $res['customerID'];
		}
		
		echo $outPut;
	
	}elseif($action=='checkMoney'){
		$money = $_REQUEST['money'];
		$payType = $_REQUEST['payType'];

		$customerID = $_SESSION['customerID'];
		$outPut = checkPay($shopID,$money,$customerID);
		$res = json_decode($outPut,true);
		
		if($res['code']==200){
			$_SESSION['money'] = $money;
			$_SESSION['payType'] = $payType;
		}
		
		echo $outPut;
	
	}elseif($action=='checkPayPwd'){
		$payPwd = $_REQUEST['payPwd'];

		$outPut = checkPayPwd($shopID,$payPwd);
				
		echo $outPut;

	}elseif($action=='getQrcode'){
		$money = $_SESSION['money'];
		$payType = $_SESSION['payType'];
		$carNum = $_SESSION['carNum'];
		$customerID = $_SESSION['customerID'];
		$discount = $_REQUEST['discount'];
		$remarks = $_REQUEST['remarks'];
		
		if($payType=='wechat'){
		    $methodID = 1;
		}elseif($payType=='alipay'){
		    $methodID = 2;
		}else{
		    $methodID = 3;
		}

		$outPut = getQrcode($money,$methodID,$customerID,$shopID,$discount,$remarks,$carNum);
				
		echo $outPut;

	}elseif($action=='checkOrder'){
		$orderNum = $_REQUEST['orderNum'];

		$outPut = checkOrder($orderNum);
				
		echo $outPut;

	}elseif($action=='getDetail'){
		$type = $_REQUEST['type'];
		$page = $_REQUEST['page'];

		$outPut = getDetail($shopID,$type,$page);
				
		echo $outPut;

	}elseif($action=='getAllotRecord'){
		$payType = $_REQUEST['payType'];

		$outPut = getAllotRecord($shopID,$payType);
				
		echo $outPut;

	}elseif($action=='getOrder'){
		$page = $_REQUEST['page'];

		$outPut = getOrder($shopID,$page);
				
		echo $outPut;
		
	}elseif($action=='search'){
		$search = $_REQUEST['search'];

		$outPut = search($search);
				
		echo $outPut;
		
	}
 ?>