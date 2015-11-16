<?
if(strlen($Dir)==0) {
	$Dir="../";
}
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");


$_data->layoutdata["SHOPLEFTMENUWIDTH"] = (	$_data->layoutdata["SHOPLEFTMENUWIDTH"] =="")?"200":	$_data->layoutdata["SHOPLEFTMENUWIDTH"];

if ((strlen($_REQUEST["id"])>0 && strlen($_REQUEST["passwd"])>0) || $_REQUEST["type"]=="logout" || $_REQUEST["type"]=="exit") {
	include($Dir."lib/loginprocess.php");
	exit;
}

?>

<html>
	<head>
		<META http-equiv="X-UA-Compatible" content="IE=5" >
	</head>
<body onload="<? echo $onload ?>">
<?if($_data->align_type=="Y") echo "<center>";?>
</body>
</html>