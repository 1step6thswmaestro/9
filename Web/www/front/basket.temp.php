<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");


$code = explode("-",$_POST['code']);
foreach ( $code as $val ){
	$sub = explode("|",$val);
	if( strlen($sub[0]) > 0 ) $test[$sub[0]] += $sub[1];
}

$basketItems = getBasketByArray('',$test);

// รั น่ผÛบ๑
echo $basketItems['deli_price'];
?>