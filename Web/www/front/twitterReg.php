<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."front/twitter/class.twitter.php");

$name = $_POST["name"];
$comment = $_POST["comment"];	//iconv("EUC-KR", "UTF-8","캔디컬러 후드집업 아웃부문 주문 1위 | http://t.co/sSgGTx4");
if($name)
	$comment = "[".$name."] ".$comment;
$seq = $_POST["seq"];
$gb = $_POST["gb"];

session_start();
$tobj = new tempTwitter();
if($tobj->_status()){
	$twRs = $tobj->_postMsg($comment);
	if($twRs->error){
		//글등록 실패시
		snsRegFail($seq, $gb,"t");
		$return_data["result"] = "false";
		$return_data["message"] = $twRs->error;
		if($twRs->error == "Could not authenticate with OAuth."){
			//@mysql_query("DELETE FROM tblmembersnsinfo WHERE id='".$_ShopInfo->getMemid()."' AND type='t' ",get_db_conn());
			$return_data["message"] = "트위터 인증오류";
		}
	}else{
		$return_data["result"] = "true";
	}
}else{
	$return_data["result"] = "false";
	$return_data["message"] = "트위터 인증오류";
}

echo json_encode($return_data);

?>
