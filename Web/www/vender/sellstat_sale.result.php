<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/venderlib.php");
INCLUDE ("access.php");

$code=$_POST["code"];
$prcode=$_POST["prcode"];
$date_year=$_POST["date_year"];
$date_month=$_POST["date_month"];
$age1=$_POST["age1"];
$age2=$_POST["age2"];
$loc=$_POST["loc"];
$sex=$_POST["sex"];
$member=$_POST["member"];
$paymethod="";

if(strlen($date_year)==0) $date_year=date("Y");
if(strlen($date_month)==0) $date_month=date("m");

?>

<html>
<head>
<title></title>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META http-equiv="X-UA-Compatible" content="IE=5" />
<script type="text/javascript" src="lib.js.php"></script>
<script>var LH = new LH_create();</script>
<script for=window event=onload>LH.exec();</script>
<script>LH.add("parent_resizeIframe('StatIfrm')");</script>
<link rel=stylesheet href="style.css" type=text/css>

</head>
<body marginwidth=0 marginheight=0 leftmargin=0 topmargin=0>
<table border=0 cellpadding=0 cellspacing=0 width=100% bgcolor=#FFFFFF>
<tr>
	<td>
<?
	$codeA = substr($code,0,3);
	$codeB = substr($code,3,3);
	$codeC = substr($code,6,3);
	$codeD = substr($code,9,3);
	$likecode=$codeA;
	if($codeB!="000") {
		$likecode.=$codeB;
		if($codeC!="000") {
			$likecode.=$codeC;
			if($codeD!="000") {
				$likecode.=$codeD;
			}
		}
	}
	unset($codeA);unset($codeB);unset($codeC);unset($codeD);

	if($date_month=="ALL") {
		$date_month="";
		include "sellstat_sale.year.php";
	} else {
		include "sellstat_sale.month.php";
	}
?>
	</td>
</tr>
</table>
</body>
</html>