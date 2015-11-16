<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/venderlib.php");

$isaccesspass=true;
INCLUDE ("access.php");

$depth=$_REQUEST["depth"];

$code=$_REQUEST["code"];
$codeA=substr($code,0,3);
$codeB=substr($code,3,3);
$codeC=substr($code,6,3);
$codeD=substr($code,9,3);
if(strlen($codeA)!=3) $codeA="000";
if(strlen($codeB)!=3) $codeB="000";
if(strlen($codeC)!=3) $codeC="000";
if(strlen($codeD)!=3) $codeD="000";
$code=$codeA.$codeB.$codeC.$codeD;

$codes=array();
$len=0;
if(strlen($codeA)>0) {
	$likecode=$codeA;
	if(strlen($codeB>0)) $likecode.=$codeB;
	if(strlen($codeC>0)) $likecode.=$codeC;
	if(strlen($codeD>0)) $likecode.=$codeD;

	$len=strlen($likecode);
	if((($depth-1)*3)==$len) {
		$sql = "SELECT SUBSTRING(productcode,1,".($len+3).") as prcode FROM tblproduct ";
		$sql.= "WHERE vender='".$_VenderInfo->getVidx()."' ";
		$sql.= "AND productcode LIKE '".$likecode."%' ";
		$sql.= "GROUP BY prcode ";
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
}

$code_str="분류선택";
if($depth=="2") $code_str="중 분 류";
else if($depth=="3") $code_str="소 분 류";
else if($depth=="4") $code_str="세 분 류";
?>

<html>
<head>
<title></title>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META http-equiv="X-UA-Compatible" content="IE=5" />
<link rel=stylesheet href="style.css" type=text/css>
<script language="javascript">
var pcode="<?=$code?>";
function BCodeSendIt(code) {
	if(code.length==0) {
		parent.sForm.code.value=pcode;
	} else {
		parent.sForm.code.value=code;
	}

	url="coupon_productchoice.ctgr.php?code="+code+"&depth=3";
	durl="coupon_productchoice.ctgr.php?depth=4";

	parent.DCodeCtgr.iForm.code.value="";
	parent.CCodeCtgr.iForm.code.value="";

	parent.CCodeCtgr.location.href=url;
	parent.DCodeCtgr.location.href=durl;
	parent.productSubmit(code);
}

function CCodeSendIt(code) {
	if(code.length==0) {
		parent.sForm.code.value=pcode;
	} else {
		parent.sForm.code.value=code;
	}

	url="coupon_productchoice.ctgr.php?code="+code+"&depth=4";

	parent.DCodeCtgr.iForm.code.value="";
	parent.DCodeCtgr.location.href=url;
	parent.productSubmit(code);
}

function DCodeSendIt(code) {
	if(code.length==0) {
		parent.sForm.code.value=pcode;
	} else {
		parent.sForm.code.value=code;
	}
	parent.productSubmit(code);
}


function sectSendIt(code,type) {
	parent.sForm.type.value=type;
<?
	if($len==3) {
		echo "BCodeSendIt(code);";
	} else if($len==6) {
		echo "CCodeSendIt(code);";
	} else if($len==9) {
		echo "DCodeSendIt(code);";
	}
?>
}

</script>

</head>
<body topmargin=0 leftmargin=0 rightmargin=0 marginheight=0 marginwidth=0>
<form name="iForm" method="post" action="">
<table border="0" cellpadding="0" cellspacing=0>
<tr>
	<td>
	<select name="code" style=width:155 onchange="sectSendIt(this.options[this.selectedIndex].value,this.options[this.selectedIndex].otype)">
	<option value="">------ <?=$code_str?> ------</option>
<?
	if(count($codes)>0) {
		$sql = "SELECT codeA,codeB,codeC,codeD,code_name,type FROM tblproductcode ";
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
			if($len==3) $codeval.=$row->codeB."000000";
			else if($len==6) $codeval.=$row->codeB.$row->codeC."000";
			else if($len==9) $codeval.=$row->codeB.$row->codeC.$row->codeD;
			echo "<option value=\"".$codeval."\" otype=\"".substr($row->type,-1,1)."\"";
			if(substr($select_code,0,strlen($codeval))==$codeval) echo " selected";
			echo ">".$row->code_name."</option>\n";
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