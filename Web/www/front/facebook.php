<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
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
if($user){
  try {
	// Proceed knowing you have a logged in user who's authenticated.
	$user_profile = $facebook->api('/me');
	if($_data->sns_ok == "Y" && strlen($memid)>0){
		$sql = "SELECT * FROM tblmembersnsinfo WHERE id='".$memid."' AND type ='f' ";
		$chk_result =@mysql_query($sql,get_db_conn());
		$facebook->setExtendedAccessToken();
		if(mysql_num_rows($chk_result)){
			//$row = mysql_fetch_assoc($chk_result);
			//$facebook->setAccessToken($row['oauth_token']);
			$sql = "UPDATE tblmembersnsinfo SET ";
		}else{
			$sql = "INSERT tblmembersnsinfo SET ";
			$sql.= "id			= '".$memid."', ";
			$sql.= "type		= 'f', ";
		}
		$sql.= "user_id		= '".$user_profile[id]."', ";
		$sql.= "oauth_token	= '".$facebook->getAccessToken()."', ";
		$sql.= "oauth_token2= '', ";
		$sql.= "screen_name	= '".iconv("UTF-8","EUC-KR", $user_profile[name])."', ";
		$sql.= "profile_img	= 'https://graph.facebook.com/".$user."/picture', ";
		$sql.= "state		= 'Y', ";
		$sql.= "regidate	= '".time()."', ";
		$sql.= "extval	= '".$_SESSION['fb_'.FACEBOOK_ID.'_code']."', ";
		$sql.= "link = '".$user_profile['link']."' ";
		if(mysql_num_rows($chk_result)){
			$sql .= "WHERE id	= '".$memid."' ";
			$sql.= "AND type	= 'f' ";
		}
		mysql_query($sql,get_db_conn());
	}

  } catch (FacebookApiException $e) {
	//print_r($e);
	$user = null;
  }
}else{
    $args = array('scope' => 'publish_stream,user_birthday,user_location,user_work_history,user_about_me,user_hometown');
   	$loginUrl = $facebook->getLoginUrl($args);
	//echo $loginUrl;
	 @header('Location: ' . $loginUrl);
	exit;

}
?>
<html>
<head>
<script type="text/javascript">
window.opener.snsInfo();
self.close();
</script>
</head>
<body>
</body>
</html>