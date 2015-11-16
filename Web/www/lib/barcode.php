<?
require_once dirname(__FILE__)."/Image/Barcode.php";
$str = $_REQUEST['str'];
$bar = new Image_Barcode;
if($str != '') $bar->draw($str,'code128','gif');
/*
?>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
<style>
body, td, p, textarea, select, button {
	font-family: Tahoma,굴림; 
	font-size: 9pt; 
	color: #222222;
	word-wrap:break-word;word-break:break-all;}

img { border: 0px; }

a:link, a:visited, a:active { 
	text-decoration: none; 
	color: #466C8A; 
}
a:hover { text-decoration: underline; }
</style>

<form name="form1">
Barcode generate <input type="text" name="str" size="10" value="<?=$str?>"> 
<input type="submit" value="Run">
</form>

<p>
*/
?>