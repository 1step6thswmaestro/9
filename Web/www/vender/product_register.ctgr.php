<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/venderlib.php");

$isaccesspass=true;
INCLUDE ("access.php");

$select_code=$_REQUEST["select_code"];
$code=$_REQUEST["code"];
$codeA=substr($code,0,3);
$codeB=substr($code,3,3);
$codeC=substr($code,6,3);
$codeD=substr($code,9,3);
if(strlen($codeA)!=3) $codeA="";
if(strlen($codeB)!=3) $codeB="";
if(strlen($codeC)!=3) $codeC="";
if(strlen($codeD)!=3) $codeD="";
$code=$codeA.$codeB.$codeC.$codeD;
if($codeA=="000") {
	$code=$codeA=$codeB=$codeC=$codeD="";
}

$len=0;
if(strlen($codeA)>0) {
	$likecode=$codeA;
	if(strlen($codeB>0)) $likecode.=$codeB;
	if(strlen($codeC>0)) $likecode.=$codeC;
	if(strlen($codeD>0)) $likecode.=$codeD;

	$len=strlen($likecode);
}

$level=2;
if($len==3) $level=2;
else if($len==6) $level=3;
else if($len==9) $level=4;

?>

<html>
<head>
<title></title>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META http-equiv="X-UA-Compatible" content="IE=5" />
<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="PrdtRegist.js.php"></script>
<link rel=stylesheet href="style.css" type=text/css>
</head>
<body leftmargin=0 topmargin=0>
<form name="form1" method="post" action="">
<table border="0" cellpadding="0">
<tr>
	<td>
	<select name="code" style=width:148 onchange="sectSendIt(parent.form1,this.options[this.selectedIndex],<?=$level?>);"
<?
	if(strlen($select_code)==12){
		echo " disabled";
	} else {
		echo " size='7'";
	}
	echo ">\n";

	if(strlen($code)>=3) {
		$sql = "SELECT codeA,codeB,codeC,codeD,code_name,type FROM tblproductcode ";
		$sql.= "WHERE codeA='".$codeA."' ";
		if(strlen($codeB)==3) {
			$sql.= "AND codeB='".$codeB."' ";
			if(strlen($codeC)==3) {
				$sql.= "AND codeC='".$codeC."' ";
				if(strlen($codeD)==3) {
					$sql.= "AND codeD='".$codeD."' ";
				}
			}
		}
		if($len==3) $sql.= "AND codeB!='000' AND codeC='000' AND codeD='000' ";
		if($len==6) $sql.= "AND codeC!='000' AND codeD='000' ";
		if($len==9) $sql.= "AND codeD!='000' ";
		$sql.= "AND type LIKE 'L%' ORDER BY sequence DESC ";
		//echo $sql; exit;
		$result=mysql_query($sql,get_db_conn());
		while($row=mysql_fetch_object($result)) {
			$codeval=$row->codeA;
			if($len==3) $codeval.=$row->codeB;
			else if($len==6) $codeval.=$row->codeB.$row->codeC;
			else if($len==9) $codeval.=$row->codeB.$row->codeC.$row->codeD;
			$ctype=substr($row->type,-1);
			if($ctype!="X") $ctype="";
			echo "<option value=\"".$codeval."\" ctype='".$ctype."'";
			if(substr($select_code,0,strlen($codeval))==$codeval) {
				echo " selected";
			}
			echo ">".$row->code_name."";
			if($ctype=="X" && $len<9) {
				echo " (단일분류)";
			}
			echo "</option>\n";
		}
		mysql_free_result($result);
	}
?>
	</select>
	</td>
</tr>
</table>
</form>
</body>
</html>