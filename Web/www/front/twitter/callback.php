<?php


/**
 * @file
 * Take the user when they return from Twitter. Get access tokens.
 * Verify credentials and redirect to based on response from Twitter.
 */

/* Start session and load lib */
session_start();

$Dir="../../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/ext/func.php");


require_once('twitteroauth/twitteroauth.php');
require_once('config.php');

/* If the oauth_token is old redirect to the connect page. */
if (isset($_REQUEST['oauth_token']) && $_SESSION['oauth_token'] !== $_REQUEST['oauth_token']) {
  $_SESSION['oauth_status'] = 'oldtoken';
  header('Location: ./clearsessions.php');
}

/* Create TwitteroAuth object with app key/secret and token key/secret from default phase */
$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);

/* Request access tokens from twitter */
$access_token = $connection->getAccessToken($_REQUEST['oauth_verifier']);

/* Save the access tokens. Normally these would be saved in a database for future use. */
$_SESSION['access_token'] = $access_token;

/* Remove no longer needed request tokens */
unset($_SESSION['oauth_token']);
unset($_SESSION['oauth_token_secret']);

/* If HTTP response is 200 continue otherwise send to connect page to retry */
if (200 == $connection->http_code) {
  /* The user has been verified and the access tokens can be saved for future use */
  $_SESSION['status'] = 'verified';

$memid = $_ShopInfo->getMemid();
if($_data->sns_ok == "Y" && strlen($memid)>0){
	$sql = "SELECT * FROM tblmembersnsinfo WHERE id='".$memid."' AND type ='t' ";
	$chk_result =@mysql_query($sql,get_db_conn());

	if(mysql_num_rows($chk_result)){
		$sql = "UPDATE tblmembersnsinfo SET ";
	}else{
		$sql = "INSERT tblmembersnsinfo SET ";
		$sql.= "id = '".$memid."', ";
		$sql.= "type = 't', ";
	}
	$sql.= "user_id = '".$_SESSION['access_token']['user_id']."', ";
	$sql.= "oauth_token = '".$_SESSION['access_token']['oauth_token']."', ";
	$sql.= "oauth_token2 = '".$_SESSION['access_token']['oauth_token_secret']."', ";
	$sql.= "screen_name	= '".iconv("UTF-8","EUC-KR", $_SESSION['screen_name'])."', ";
	$sql.= "state		= 'Y', ";
	$sql.= "regidate	= '".time()."', ";
	$sql.= "link = 'https://twitter.com/".$_SESSION['access_token']['screen_name']."' ";
	if(mysql_num_rows($chk_result)){
		$sql .= "WHERE id = '".$memid."' ";
		$sql.= "AND type = 't' ";
	}

	//_pr($_SESSION);
	//echo "<hr>".$sql;
	//exit;
	mysql_query($sql,get_db_conn());
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
<?
  //header('Location: ./index.php');
} else {
  /* Save HTTP status for error dialog on connnect page.*/
  header('Location: ./clearsessions.php');
}
