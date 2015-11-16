<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once("./twitter/class.twitter.php");
session_start();

$tobj = new tempTwitter();
if($tobj->_status()){
	//_pr($tobj);
	// token을 이용하여 twitter에 접근합니다.
	//$connection = new TwitterOAuth(TWITTER_ID, TWITTER_SECRET, $oauth_token, $oauth_token_secret);
	//$tobj->_postMsg('wirtetest');	
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
}else{
	$tobj->_requestAuth();
}//$tobj->_redirect();
?>
