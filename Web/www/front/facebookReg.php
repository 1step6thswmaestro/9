<?php

$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/ext/func.php");
require "./facebook/facebook.php";

$memid = $_ShopInfo->getMemid();
$facebook = new Facebook(array( "appId" => FACEBOOK_ID,"secret" => FACEBOOK_SECRET));
if(!empty($memid) && empty($_SESSION[$skey.'_code'])){
	$sql = "SELECT * FROM tblmembersnsinfo WHERE id='".$memid."' AND type ='f' and state='Y' ";

	$chk_result =@mysql_query($sql,get_db_conn());
	if(mysql_num_rows($chk_result)){
		$row = mysql_fetch_assoc($chk_result);
		$facebook->setAccessToken($row['oauth_token']);
	}
}

$user = $facebook->getUser();

$error = "";

if($user){

	try {
		$comment = $_REQUEST["comment"];
		$seq = $_REQUEST["seq"];
		$link = $_REQUEST["link"];
		$name = $_REQUEST["name"];
		$picture = $_REQUEST["picture"];
		$gb = $_REQUEST["gb"];
		if(strlen($comment)==0){
			$comment = $name;
		}

		$arSenddata = array();
		//$arSenddata["access_token"] = $fb->getAccessToken();
		$arSenddata["message"] = $comment;
		$arSenddata["link"] = $link;
		if($picture) $arSenddata["picture"] = $picture;
		if($name) $arSenddata["name"] = $name;

		$rtn = $facebook->api('/me/feed', 'post', $arSenddata);

	} catch (FacebookApiException $e) {
		$error = $e->getMessage();
	}

	if( strlen($error) > 0 ) {
		$return_data["result"] = "false";
		$return_data["message"] = $error;
	}else {
		$return_data["result"] = "true";
		$return_data["message"] = "OK";
	}

}else{
	//mysql_query("delete FROM tblmembersnsinfo WHERE id='".$_ShopInfo->getMemid()."' AND type ='f'",get_db_conn());
	$return_data["result"] = "false";
	$return_data["message"] = "페이스북연동오류";
}

echo json_encode($return_data);

?>