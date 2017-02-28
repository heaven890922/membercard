<?php 
	include_once 'config.php';
	
	$action = $_REQUEST['action'];

	if($action=='checkCarNum'){
		$outPut = checkCarNum();
				
		echo $outPut;
	
	}elseif($action=='bindCarNum'){
		$carNum = $_REQUEST['carNum'];
		$payPwd = $_REQUEST['payPwd'];
		
		$outPut = bindCarNum($carNum,$payPwd);
		
		echo $outPut;
	
	}elseif($action=='getInfo'){
		$outPut = getInfo();
								
		echo $outPut;

	}elseif($action=='payInfo'){
		$money = $_REQUEST['money'];
		$shopID = $_REQUEST['shopID'];
		$shopname = $_REQUEST['shopname'];
		
		payInfo($money,$shopID,$shopname);
	
	}elseif($action=='confirmPay'){
		$payPwd = $_REQUEST['payPwd'];
		
		$outPut = confirmPay($payPwd);
		
		$res = json_decode($outPut,true);
		
		if($res['code']==200){
			$_SESSION['orderNum'] = $res['orderNum'];
		}

		echo $outPut;

	}elseif($action=='orderInfo'){
		$orderNum = $_REQUEST['orderNum'];

		$outPut = getOrderInfo($orderNum);
		
		echo $outPut;
	}elseif($action=='getChargeRecord'){
		$page = $_REQUEST['page'];
		
		$outPut = getChargeRecord($page);
		
		echo $outPut;
	}elseif($action=='getConsumeRecord'){
		$page = $_REQUEST['page'];
		
		$outPut = getConsumeRecord($page);
		
		echo $outPut;
	}elseif($action=='getShopNearBy'){
		$longitude = $_REQUEST['longitude'];
		$latitude = $_REQUEST['latitude'];
		$page = $_REQUEST['page'];
		$num = $_REQUEST['num'];
		$outPut = getShopNearBy($longitude,$latitude,$page,$num);
		
		echo $outPut;
	} elseif ($action == 'getCharge') {
        $page = $_REQUEST['page'];
        $outPut = chargeRecord($page);
        echo $outPut;
    }elseif($action == 'getLocation'){
    	@$lng = $_REQUEST['lng'];
    	@$lat = $_REQUEST['lat'];
    	
    	$_SESSION['lng'] = $lng;
    	$_SESSION['lat'] = $lat;

    }elseif($action == 'backInfo'){
    	@$shopID = $_REQUEST['shopID'];
    	@$shopname = $_REQUEST['shopname'];
    	
    	$_SESSION['shopID'] = $shopID;
    	$_SESSION['shopname'] = $shopname;
    }

 ?>