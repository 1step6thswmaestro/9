<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/venderlib.php");

$isaccesspass=true;
INCLUDE ("access.php");

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

$codes=array();
$len=0;
if(strlen($codeA)>0) {
	$likecode=$codeA;
	if(strlen($codeB>0)) $likecode.=$codeB;
	if(strlen($codeC>0)) $likecode.=$codeC;
	if(strlen($codeD>0)) $likecode.=$codeD;

	$len=strlen($likecode);
	$sql = "SELECT SUBSTRING(productcode,1,".($len+3).") as prcode FROM tblproduct ";
	$sql.= "WHERE vender='".$_VenderInfo->getVidx()."' ";
	$sql.= "AND productcode LIKE '".$likecode."%' ";
	$sql.= "AND display='Y' GROUP BY prcode ";
	$result=mysql_query($sql,get_db_conn());
	$i=0;
	while($row=mysql_fetch_object($result)) {
		$codes[$i]["A"]=substr($row->prcode,0,3);
		$codes[$i]["B"]=substr($row->prcode,3,3);
		$codes[$i]["C"]=substr($row->prcode,6,3);
		$codes[$i]["D"]=substr($row->prcode,9,3);
		$i++;
	}
	mysql_free_result($result);
}

?>

<html>
<head>
<title></title>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META http-equiv="X-UA-Compatible" content="IE=5" />
<link rel=stylesheet href="style.css" type=text/css>
<script language="javascript">
function BCodeSendIt(code) {
	url="product_code.ctgr.php?code="+code;
	durl="product_code.ctgr.php";

	parent.DCodeCtgr.iForm.code.value="";
	parent.CCodeCtgr.iForm.code.value="";

	parent.CCodeCtgr.location.href=url;
	parent.DCodeCtgr.location.href=durl;
}

function CCodeSendIt(code) {
	url="product_code.ctgr.php?code="+code;

	parent.DCodeCtgr.iForm.code.value="";
	parent.DCodeCtgr.location.href=url;
}


function sectSendIt(code) {
<?
	if($len==3) {
		echo "BCodeSendIt(code);";
	} else if($len==6) {
		echo "CCodeSendIt(code);";
	}
?>
	parent.f_getData();
}

</script>

</head>
<body leftmargin=0 topmargin=0>
<form name="iForm" method="post" action="">
<table border="0" cellpadding="0">
<tr>
	<td>
	<select name="code" style=width:100 onchange="sectSendIt(this.options[this.selectedIndex].value)" >
	<option value="">--선택하세요--</option>
<?
	if(count($codes)>0) {
		$sql = "SELECT codeA,codeB,codeC,codeD,code_name FROM tblproductcode ";
		$sql.= "WHERE (";
		for($i=0;$i<count($codes);$i++) {
			if($i>0) $sql.= " OR ";
			$sql.= "(codeA='".$codes[$i]["A"]."' ";
			if(strlen($codes[$i]["B"])==3) {
				$sql.= "AND codeB='".$codes[$i]["B"]."' ";
				if(strlen($codes[$i]["C"])==3) {
					$sql.= "AND codeC='".$codes[$i]["C"]."' ";
					if(strlen($codes[$i]["D"])==3) {
						$sql.= "AND codeD='".$codes[$i]["D"]."' ";
					} else {
						$sql.= "AND codeD='000' ";
					}
				} else {
					$sql.= "AND codeC='000' AND codeD='000' ";
				}
			} else {
				$sql.= "AND codeB='000' AND codeC='000' AND codeD='000' ";
			}
			$sql.= ") ";
		}
		$sql.= ") ";
		if($len==3) $sql.= "AND codeB!='000' ";
		if($len==6) $sql.= "AND codeC!='000' ";
		if($len==9) $sql.= "AND codeD!='000' ";
		$sql.= "AND type LIKE 'L%' ORDER BY sequence DESC ";
		//echo $sql; exit;
		$result=mysql_query($sql,get_db_conn());
		while($row=mysql_fetch_object($result)) {
			$codeval=$row->codeA;
			if($len==3) $codeval.=$row->codeB;
			else if($len==6) $codeval.=$row->codeB.$row->codeC;
			else if($len==9) $codeval.=$row->codeB.$row->codeC.$row->codeD;
			echo "<option value=\"".$codeval."\">".$row->code_name."</option>\n";
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