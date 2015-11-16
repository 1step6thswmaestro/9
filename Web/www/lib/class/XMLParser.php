<?
if(PHP_VERSION < '5'){
	require_once(dirname(__FILE__).'/xmlparser_php4.php');
}else if(PHP_VERSION < '4'){
	exit('Version Check');
}else{
	require_once(dirname(__FILE__).'/xmlparser_php5.php');
}
?>