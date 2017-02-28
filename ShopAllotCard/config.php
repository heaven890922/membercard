<?php 
	session_start();
	// $shopID = $_SESSION['shopID'];
 	$shopID = 207;
 	$apiKey = 'e0684fed76aaa6611f436a381491687e';
 	$url = 'http://120.76.251.222:9980/membercard/tp5/public/index.php';

	function curl($url,$post_data){
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch,CURLOPT_POST,1);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$post_data);

		$result = curl_exec($ch);

		return $result;
	}

	function getShopInfo(){
		$apiUrl =$GLOBALS['url'].'/shop/info';
		$item = array('apiKey'=>$GLOBALS['apiKey'],'shopID'=>$GLOBALS['shopID']);
		$res = curl($apiUrl,$item);
		
		return $res;
	}

	function checkCustomer($tel,$carNum){
		$apiUrl =$GLOBALS['url'].'/qrcode/check';
		$item = array('apiKey'=>$GLOBALS['apiKey'],'tel'=>$tel,'carNum'=>$carNum);
		$res = curl($apiUrl,$item);
		
		return $res;
	} 
	
	function checkPay($shopID,$money,$customerID){
		$apiUrl =$GLOBALS['url'].'/qrcode/moneycheck';
		$item = array('apiKey'=>$GLOBALS['apiKey'],'shopID'=>$shopID,'money'=>$money,'customerID'=>$customerID);
		$res = curl($apiUrl,$item);
		
		return $res;
	}

	function checkPayPwd($shopID,$payPwd){
		$apiUrl =$GLOBALS['url'].'/shop/checkpwd';
		$item = array('apiKey'=>$GLOBALS['apiKey'],'shopID'=>$shopID,'payPwd'=>$payPwd);
		$res = curl($apiUrl,$item);
		
		return $res;
	}

	function getQrcode($money,$methodID,$customerID,$shopID,$discount,$remarks,$carNum){
		$apiUrl =$GLOBALS['url'].'/qrcode/create';
		$item = array('apiKey'=>$GLOBALS['apiKey'],'money'=>$money,'methodID'=>$methodID,'customerID'=>$customerID,'shopID'=>$shopID,'discount'=>$discount,'remarks'=>$remarks,'carNum'=>$carNum);
		$res = curl($apiUrl,$item);
		
		return $res;
	}

	function checkOrder($orderNum){
		$apiUrl =$GLOBALS['url'].'/order/check';
		$item = array('apiKey'=>$GLOBALS['apiKey'],'orderNum'=>$orderNum);
		$res = curl($apiUrl,$item);
		
		return $res;
	}

	function getDetail($shopID,$type,$page){
		$apiUrl =$GLOBALS['url'].'/shop/consume';
		$item = array('apiKey'=>$GLOBALS['apiKey'],'shopID'=>$shopID,'type'=>$type,'page'=>$page);
		$res = curl($apiUrl,$item);
		
		return $res;
	}

	function getAllotRecord($shopID,$payType){
		$apiUrl =$GLOBALS['url'].'/shop/count';
		$item = array('apiKey'=>$GLOBALS['apiKey'],'shopID'=>$shopID,'payType'=>$payType);
		$res = curl($apiUrl,$item);
		
		return $res;
	}

	function getOrder($shopID,$page){
		$apiUrl =$GLOBALS['url'].'/shop/order';
		$item = array('apiKey'=>$GLOBALS['apiKey'],'shopID'=>$shopID,'page'=>$page);
		$res = curl($apiUrl,$item);
		
		return $res;
	}

	function search($search){
		$shopID = $GLOBALS['shopID'];
		$apiUrl =$GLOBALS['url'].'/shop/search';
		$item = array('apiKey'=>$GLOBALS['apiKey'],'shopID'=>$shopID,'search'=>$search);
		$res = curl($apiUrl,$item);
		
		return $res;
	}
 ?>