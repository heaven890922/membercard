<?php 
	session_start();
	// $customerID = $_SESSION['customerID'];
 	$customerID = 127;
 	//$tel = $_SESSION['tel'];
 	$tel = '13798182705';
 	
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

	function isBindCarNum(){
		$apiUrl =$GLOBALS['url'].'/user/iscar';
		$item = array('apiKey'=>$GLOBALS['apiKey'],'customerID'=>$GLOBALS['customerID']);
		$res = curl($apiUrl,$item);
		
		return $res;
	}

	function isBindPayPwd(){
		$apiUrl =$GLOBALS['url'].'/user/ispwd';
		$item = array('apiKey'=>$GLOBALS['apiKey'],'customerID'=>$GLOBALS['customerID']);
		$res = curl($apiUrl,$item);
		
		return $res;
	}

	function BindCarNum($carNum,$payPwd){
		$apiUrl =$GLOBALS['url'].'/user/bindcar';
		$item = array('apiKey'=>$GLOBALS['apiKey'],'customerID'=>$GLOBALS['customerID'],'carNum'=>$GLOBALS['carNum'],'payPwd'=>$payPwd);
		$res = curl($apiUrl,$item);
		
		return $res;
	}

	function getInfo(){
		$apiUrl =$GLOBALS['url'].'/user/info';
		$item = array('apiKey'=>$GLOBALS['apiKey'],'customerID'=>$GLOBALS['customerID']);
		$res = curl($apiUrl,$item);
		
		return $res;
	}

	function payInfo($money,$shopID,$shopname){
		$_SESSION['money'] = $money;
		$_SESSION['shopID'] = $shopID;
		$_SESSION['shopname'] = $shopname;
	}

	function confirmPay($payPwd){
		$money = $_SESSION['money'];
		$shopID = $_SESSION['shopID'];

		$apiUrl =$GLOBALS['url'].'/user/pay';
		$item = array('apiKey'=>$GLOBALS['apiKey'],'customerID'=>$GLOBALS['customerID'],'money'=>$money,'shopID'=>$shopID,'payPwd'=>$payPwd);
		$res = curl($apiUrl,$item);
		
		return $res;
	}

	function getOrderInfo($orderNum){
		$apiUrl =$GLOBALS['url'].'/user/payorderinfo';
		$item = array('apiKey'=>$GLOBALS['apiKey'],'customerID'=>$GLOBALS['customerID'],'orderNum'=>$orderNum);
		$res = curl($apiUrl,$item);
		
		return $res;
	}

	function getChargeRecord($page){
		$apiUrl =$GLOBALS['url'].'/user/chargeorder';
		$item = array('apiKey'=>$GLOBALS['apiKey'],'customerID'=>$GLOBALS['customerID'],'page'=>$page);
		$res = curl($apiUrl,$item);
		
		return $res;
	}

	function getConsumeRecord($page){
		$apiUrl =$GLOBALS['url'].'/user/payorder';
		$item = array('apiKey'=>$GLOBALS['apiKey'],'customerID'=>$GLOBALS['customerID'],'page'=>$page);
		$res = curl($apiUrl,$item);
		
		return $res;
	}

	function getShopNearBy($longitude,$latitude,$page,$num){
		$apiUrl =$GLOBALS['url'].'/user/shoplistbyd';
		$item = array('apiKey'=>$GLOBALS['apiKey'],'longitude'=>$longitude,'latitude'=>$latitude,'page'=>$page,'num'=>$num);
		$res = curl($apiUrl,$item);
		
		return $res;
	}

	function chargeRecord($page)
	{
	    $apiUrl = $GLOBALS['url'] . '/user/chargeorder';
	    $item = array('apiKey' => $GLOBALS['apiKey'], 'customerID' => $GLOBALS['customerID'], 'page' => $page);
	    $res = curl($apiUrl, $item);
	    return $res;
	}
 ?>