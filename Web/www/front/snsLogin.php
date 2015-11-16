<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
$type = $_GET["type"];

if($type =="t"){

	/* Start session and load library. */
	session_start();
	include_once($Dir."front/twitter/config.php");
	include_once($Dir."front/twitter/class.twitter.php");
	$tobj = new tempTwitter();
	if($tobj->_status()){

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

}
?>