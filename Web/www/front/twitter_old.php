<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
/**
 * @file
 * /front/twitter/callback.php 함수 변경
 */

session_start();
require_once("./twitter/twitteroauth/twitteroauth.php");

if (isset($_REQUEST['oauth_token']) && $_SESSION['oauth_token'] !== $_REQUEST['oauth_token']) {
  $_SESSION['oauth_status'] = 'oldtoken';
  header('Location: ./twitterclearsession.php');
}else{
	$connection = new TwitterOAuth(TWITTER_ID, TWITTER_SECRET, 'adfasdfasf', 'ddddddd');
	$token = $connection->getRequestToken('http://getmalltest.objet.co.kr/front/twitter.php');
	_pr($token);
	exit;
}
//$access_token = $connection->getAccessToken($_REQUEST['oauth_verifier']);

$_SESSION['access_token'] = $access_token;

unset($_SESSION['oauth_token']);
unset($_SESSION['oauth_token_secret']);

if (200 == $connection->http_code) {
	$_SESSION['status'] = 'verified';

####################################################################################
	$content = $connection->get('account/verify_credentials');

	if($_data->sns_ok == "Y" && strlen($_ShopInfo->getMemid())>0) {

		$sql = "SELECT COUNT(*) as cnt FROM tblmembersnsinfo WHERE id='".$_ShopInfo->getMemid()."' AND type ='t' ";
		$chk_result =@mysql_query($sql,get_db_conn());
		$row=@mysql_fetch_object($chk_result);;	
		@mysql_free_result($chk_result);
		if($row->cnt > 0){
			$sql = "UPDATE tblmembersnsinfo SET ";
		}else{
			$sql = "INSERT tblmembersnsinfo SET ";
			$sql.= "id			= '".$_ShopInfo->getMemid()."', ";
			$sql.= "type		= 't', ";
		}
		$sql.= "user_id		= '".$access_token[screen_name]."', ";
		$sql.= "oauth_token	= '".$access_token[oauth_token]."', ";
		$sql.= "oauth_token2= '".$access_token[oauth_token_secret]."', ";
		$sql.= "screen_name	= '".iconv("UTF-8","EUC-KR", $content[name])."', ";
		$sql.= "profile_img	= '".$content["profile_image_url"]."', ";
		$sql.= "state		= 'Y', ";
		$sql.= "regidate	= '".time()."' ";
		if($row->cnt > 0){
			$sql .= "WHERE id	= '".$_ShopInfo->getMemid()."' ";
			$sql.= "AND type	= 't' ";
		}
		mysql_query($sql,get_db_conn());
	}
/*
	echo "<pre>";
	print_r($content);
	echo "</pre>";
	echo $sql;
	exit;
*/
####################################################################################
}else {
  /* Save HTTP status for error dialog on connnect page.*/
  header('Location: ./twitterclearsession.php');
}
?>
<html>
<head>
<script type="text/javascript">
window.opener.snsInfo();
window.close();
</script>
</head>
<body>
</body>
</html>
