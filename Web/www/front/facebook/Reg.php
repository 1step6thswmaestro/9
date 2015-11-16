<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$comment = $_POST["comment"];
$seq = $_POST["seq"];
$link = $_POST["link"];
$name = $_POST["name"];
$picture = $_POST["picture"];
$gb = $_POST["gb"];
if(!$comment){
	$comment = $name;
}

require "./facebook/facebook.php";

$fb = new Facebook(array("appId"=>FACEBOOK_ID,"secret"=>FACEBOOK_SECRET));

$user = $fb->getUser();
if(!$user){
	$sql = "SELECT * FROM tblmembersnsinfo WHERE id='".$memid."' AND type ='f' and state='Y' ";
	$chk_result =@mysql_query($sql,get_db_conn());
	if(mysql_num_rows($chk_result)){
		$row = mysql_fetch_assoc($chk_result);
		$facebook->setAccessToken($row['oauth_token']);
	}
}

if($user){
	try{
		$arSenddata = array();
		//$arSenddata["access_token"] = $fb->getAccessToken();
		$arSenddata["message"] = $comment;
		$arSenddata["link"] = $link;
		if($picture) $arSenddata["picture"] = $picture;
		if($name) $arSenddata["name"] = $name;
		// $rtn =  $fb->api("/".$fb->getUser()."/feed?access_token=".$fb->getAccessToken(), "POST", $arSenddata);
		$rtn =  $fb->api("/me/feed", "POST", $arSenddata);
	}catch(FacebookApiException $e){
		$rtn->error = $e->__toString();
	}
	if($rtn->error) {
		snsRegFail($seq, $gb, "f");
		$return_data["result"] = "false";
		$return_data["message"] = $rtn->error;
	}else {
		$return_data["result"] = "true";
		$return_data["message"] = "OK";
	}
}else{
	mysql_query("delete FROM tblmembersnsinfo WHERE id='".$_ShopInfo->getMemid()."' AND type ='f'",get_db_conn());
	$return_data["result"] = "false";
	$return_data["message"] = "페이스북연동오류";
}
echo json_encode($return_data);
?>