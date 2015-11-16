<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

$depth=$_REQUEST["depth"];
$select_code=$_REQUEST["select_code"];
$codeAsel=substr($select_code,0,3);
$codeBsel=substr($select_code,3,3);
$codeCsel=substr($select_code,6,3);
$codeDsel=substr($select_code,9,3);
if(strlen($codeAsel)!=3) $codeAsel="000";
if(strlen($codeBsel)!=3) $codeBsel="000";
if(strlen($codeCsel)!=3) $codeCsel="000";
if(strlen($codeDsel)!=3) $codeDsel="000";
$select_code=$codeAsel.$codeBsel.$codeCsel.$codeDsel;

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
	if((($depth-1)*3)!=$len) {
		$code="";
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
<link rel=stylesheet href="style.css" type=text/css>
<script language="javascript">
function BCodeSendIt(code) {
	parent.sForm.code.value=code;

	url="vender_prdtlist.ctgr.php?code="+code+"&depth=3";
	durl="vender_prdtlist.ctgr.php?depth=4";

	parent.DCodeCtgr.iForm.code.value="";
	parent.CCodeCtgr.iForm.code.value="";

	parent.CCodeCtgr.location.href=url;
	parent.DCodeCtgr.location.href=durl;
}
  
function CCodeSendIt(code) {
	parent.sForm.code.value=code;

	url="vender_prdtlist.ctgr.php?code="+code+"&depth=4";

	parent.DCodeCtgr.iForm.code.value="";
	parent.DCodeCtgr.location.href=url;
}

function DCodeSendIt(code) {
	parent.sForm.code.value=code;
}


function sectSendIt(code) {
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
	<select name="code" style=width:155 onchange="sectSendIt(this.options[this.selectedIndex].value)">
	<option value="">------ <?=$code_str?> ------</option>
<?
	if(strlen($code)>0) {
		$sql = "SELECT codeA,codeB,codeC,codeD,code_name FROM tblproductcode ";
		$sql.= "WHERE codeA='".$codeA."' ";
		if(strlen($codeB)>0) {
			$sql.= "AND codeB='".$codeB."' ";
			if(strlen($codeC)>0) {
				$sql.= "AND codeC='".$codeC."' ";
				if(strlen($codeD)>0) {
					$sql.= "AND codeD='".$codeD."' ";
				} else {
					$sql.= "AND codeD!='000' ";
				}
			} else {
				$sql.= "AND codeC!='000' AND codeD='000' ";
			}
		} else {
			$sql.= "AND codeB!='000' AND codeC='000' AND codeD='000' ";
		}
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
			echo "<option value=\"".$codeval."\"";
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