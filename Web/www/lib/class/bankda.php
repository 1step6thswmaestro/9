<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/lib/ext/func.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/scheduled_delivery/scheduled.func.php';
class bankda{
	var $authkey = "";
	var $shopurl = "";
	var $query	 = "";
	var $apiHost = "getmall.co.kr";
	var $apiUrl = "/API/authkey.php";
	var $bankUrl = "/API/bankda.php";
	var $statusArr = array("N"=>"확인전","T"=>"입금확인(자동)","B"=>"입금확인(수동)","S" => "동명이인","F" => "실패(불일치)","A" => "관리자입금확인");

	var $rn = "\r\n";

	function bankda(){
		$this->__construct();
	}

	function __construct(){
		$this->_init(true);
	}

	function _statusTxt($status){
		if(isset($this->statusArr[$status])) return $this->statusArr[$status];
		else return 'unKnown';
	}

	// 설정 초기화 - 저장된 계정등 로드
	function _init($reinit=false){
		if(empty($this->shopurl) && !empty($_SERVER['HTTP_HOST'])){
			$this->shopurl = $_SERVER['HTTP_HOST'];
		}
		$this->getAuthKey();
	}

	function getAuthKey(){
		$this->authkey = '';
		if($f=file($GLOBALS['Dir'].AuthkeyDir.".shopaccess")) $this->authkey =trim($f[0]);
	}

	function _checkSolutionAuth(){
		//$query = "authkey=".$this->authkey."&shopurl=".$this->shopurl;
		$param = array();
		$param['authkey'] = $this->authkey;
		$param['shopurl'] = $this->shopurl;
		//$re = SendSocketPost($this->apiHost,$this->bankUrl,$query,80);
		$re = $this->_sendHTTP('POST',$this->apiHost,$this->bankUrl,$param);
		$re = json_decode($re,true);
		if(PHP_VERSION > '5.2') array_walk($re,'_iconvFromUtf8');
		return $re;
	}

	function _getBankInfos($toArray=true){
		$param['authkey'] = $this->authkey;
		$param['shopurl'] = $this->shopurl;
		$param['act'] = 'bankInfos';
		$re = $this->_sendHTTP('POST',$this->apiHost,$this->bankUrl,$param);

		if($toArray === true){
			$re = json_decode($re,true);
			if(PHP_VERSION > '5.2') array_walk($re,'_iconvFromUtf8');
		}
		return $re;
	}

	function _addAccount($param=array()){
		$param['authkey'] = $this->authkey;
		$param['shopurl'] = $this->shopurl;
		$param['act'] = 'addAccount';
		$re = $this->_sendHTTP('POST',$this->apiHost,$this->bankUrl,$param);
		$re = json_decode($re,true);
		if(PHP_VERSION > '5.2') array_walk($re,'_iconvFromUtf8');
		return $re;
	}


	function _sendHTTP($method,$host,$uri='/',$param=array(),$port=80,$timeout=30){
		$method = (strtoupper($method) == 'POST')?'POST':'GET';
		if(is_array($param) && count($param) > 0){
			$param_get = ($method == 'GET')?'?'.http_build_query($param):'';
			$param_post = ($method == 'POST')?http_build_query($param):'';
		}
		$req = array();
		$req[] = $method.' '.$uri.$param_get.' HTTP/1.1';
	    $req[] = 'Host: '. $host;
 		$req[] = 'Content-Type: application/x-www-form-urlencoded';
		if($method == 'POST') $req[] = 'Content-Length: '.strlen($param_post);
		$req[] ="Connection: close".$this->rn;
		if($method == 'POST') $req[] = $param_post.$this->rn;

		$req = implode($this->rn,$req).$this->rn;

		if(false !== $fp = @fsockopen($host,$port,$errcode,$errmsg,$timeout)){
			fputs($fp,$req);
			$buff = array('','');
			$i=0;
			while(!feof($fp)){
				$cont = fgets($fp, 1024);
				if($i < 1 && $cont == $this->rn) $i++;
				$buff[$i] .= $cont;
			}
			//$temp_result.=fread($fp,1024);
			fclose($fp);
			if(!empty($buff[1])) $buff[1] = $this->unchunk($buff[1]);
			return $buff[1];
		}else{
			echo 'syncErr'.'['.$errcode.']'.$errmsg;
			return '';
		}
	}

	function unchunk($result) {
		return preg_replace_callback(
			'/(?:(?:\r\n|\n)|^)([0-9A-F]+)(?:\r\n|\n){1,2}(.*?)'
			.'((?:\r\n|\n)(?:[0-9A-F]+(?:\r\n|\n))|$)/si',
			create_function(
				'$matches',
				'return hexdec($matches[1]) == strlen($matches[2]) ?
					 $matches[2] :
					 $matches[0];'
			),
			$result
		);
	}


	/// 동기화 관련
	function match($param=array()){

	}

	function _getList($param=array()){
		$param['authkey'] = $this->authkey;
		$param['shopurl'] = $this->shopurl;
		$param['act'] = 'getList';

		$re = $this->_sendHTTP('POST',$this->apiHost,$this->bankUrl,$param);
		$re = json_decode($re,true);
		if(PHP_VERSION > '5.2') array_walk($re,'_iconvFromUtf8');
		return $re;
	}

	function _authMatch(){
		$param['authkey'] = $this->authkey;
		$param['shopurl'] = $this->shopurl;
		$param['act'] = 'getList';
		$param['synctype'] = 'backmatch';

		do{
			$getinfo = $this->_sendHTTP('POST',$this->apiHost,$this->bankUrl,$param);
			$getinfo = json_decode($getinfo,true);
			if(PHP_VERSION > '5.2') array_walk($getinfo,'_iconvFromUtf8');
			$resend = array();
			if($getinfo['err'] == 'ok' && count($getinfo['items'])){
				foreach($getinfo['items'] as $item){
					//매칭 주문 찾기
					//$sql="SELECT * FROM tblorderinfo WHERE ordercode >= '".$item['Bkdate']."' and (bankname = '".$item['Bkjukyo']."' or sender_name =  '".$item['Bkjukyo']."') and price = '".$item['Bkinput']."' and paymethod = 'B' and pay_data like '".$item['Bkname']."%' and (bank_date IS NULL OR bank_date = '')";
					//$sql="SELECT * FROM tblorderinfo WHERE ordercode <= '".$item['Bkdate']."' and (bankname = '".$item['Bkjukyo']."' or sender_name =  '".$item['Bkjukyo']."') and price = '".$item['Bkinput']."' and paymethod = 'B' and pay_data like '".$item['Bkname']."%'";
					// $sql="SELECT * FROM tblorderinfo WHERE substr(ordercode,1,8) <= '".$item['Bkdate']."' and (bankname = '".$item['Bkjukyo']."' or sender_name =  '".$item['Bkjukyo']."') and price = '".$item['Bkinput']."' and paymethod = 'B' and pay_data like '".$item['Bkname']."%'";
					$limt = date('Ymd',strtotime('-5 day',strtotime($item['Bkdate'])));
					$sql="SELECT * FROM tblorderinfo WHERE substr(ordercode,1,8) >= '".$limt."' and substr(ordercode,1,8) <= '".$item['Bkdate']."' and (bankname = '".$item['Bkjukyo']."' or sender_name =  '".$item['Bkjukyo']."') and price = '".$item['Bkinput']."' and paymethod = 'B' and pay_data like '".$item['Bkname']."%' and deli_gbn='N'";
					if(false === $res = mysql_query($sql,get_db_conn())) continue;
					
					$tmp = array();
					$isscheduled = false;
					
					if(mysql_num_rows($res) < 1){
						//정기배송 주문 조회
						$sql="SELECT * FROM scheduled_delivery_order WHERE substr(replace(ordertime,'-',''),1,8) <= '".$item['Bkdate']."' and payauth = '".$item['Bkjukyo']."' and price = '".$item['Bkinput']."' and paymethod = 'B' and payinfo like '".$item['Bkname']."%'";						
						if(false === $res = mysql_query($sql,get_db_conn())) continue;
						$isscheduled = true; //정기배송 플래그
					}
						
							
							
					if(mysql_num_rows($res) < 1){
						$tmp['status'] = 'F';
					}else if(mysql_num_rows($res) > 1){ // 중복 회원 보완루틴 필요할지도
						$tmp['status'] = 'S';
					}else{
						$info = mysql_fetch_assoc($res);
						
						if(!$isscheduled){						
							if(!_empty($info['bank_date'])){
								$tmp['status'] = 'A';
								$tmp['ordercode'] = $info['ordercode'];
							}else{
								$sql = "UPDATE tblorderinfo SET bank_date='".$item['Bkxferdatetime']."' WHERE ordercode='".$info['ordercode']."' ";
								if(@mysql_query($sql,get_db_conn())){
									//$this->sendSms();
									$tmp['status'] = 'T';
									$tmp['ordercode'] = $info['ordercode'];
								}
							}
						}else{
						//	$soidx = intval(substr($ordr_idxx,3))
							$tmp['ordercode'] = scheduledDeliveryOrdercodeOnSoidx($info['soidx']);
							if($info['paystatus'] == '1'){
								$tmp['status'] = 'A';								
							}else{
								$systemAuto = time();
								$param['soidx'] = $info['soidx'];
								$param['paymethod'] = 'B';
								$param['paystatus'] = '1';		
								$param['systemAuto'] = $systemAuto;								
								$param['payflag'] = substr($item['Bkxferdatetime'],0,4).'-'.substr($item['Bkxferdatetime'],4,2).'-'.substr($item['Bkxferdatetime'],6,2);
							 	$param['paydate'] = $param['payflag'].' '.substr($item['Bkxferdatetime'],8,2).':'.substr($item['Bkxferdatetime'],10,2).':'.substr($item['Bkxferdatetime'],12,2);
								
								$ret = chgScheduledDeliveryOrderStatusChange($param);
								
								$tmp['status'] = 'T';
								$tmp['ordercode'] = $info['ordercode'];
							}
						}
					}
					$resend[$item['Bkid']] = $tmp;
					$param['Bkid'] = $item['Bkid'];
				}
			}
			$rparam = array();
			$rparam['authkey'] = $this->authkey;
			$rparam['shopurl'] = $this->shopurl;
			$rparam['act'] = 'autoSync';
			$rparam['items'] = $resend;
			$rinfo = $this->_sendHTTP('POST',$this->apiHost,$this->bankUrl,$rparam);
			$rinfo = json_decode($rinfo,true);
			if(PHP_VERSION > '5.2') array_walk($rinfo,'_iconvFromUtf8');
		}while($getinfo['totalpage'] > $getinfo['page'] && count($getinfo['items']));
	}


	function _deleteList($items=array()){
		$param['authkey'] = $this->authkey;
		$param['shopurl'] = $this->shopurl;
		$param['act'] = 'deleteList';
		$param['items'] = $items;
		$getinfo = $this->_sendHTTP('POST',$this->apiHost,$this->bankUrl,$param);
		$getinfo = json_decode($getinfo,true);
		if(PHP_VERSION > '5.2') array_walk($getinfo,'_iconvFromUtf8');
		return $getinfo;
	}


}
?>