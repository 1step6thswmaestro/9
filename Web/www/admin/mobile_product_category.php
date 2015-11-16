<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

$code=$_REQUEST["code"];
$depth=$_REQUEST["depth"];



$codeA="000";
$codeB="000";
$codeC="000";
$codeD="000";


$codeA=substr($code,0,3);
$codeB=substr($code,3,3);
$codeC=substr($code,6,3);
$codeD=substr($code,9,3);

$tempA=substr($code,0,3);
$tempB=substr($code,3,3);
$tempC=substr($code,6,3);
$tempD=substr($code,9,3);

if(strlen($codeA)!=3) $codeA="";
if(strlen($codeB)!=3) $codeB="";
if(strlen($codeC)!=3) $codeC="";
if(strlen($codeD)!=3) $codeD="";
$code=$codeA.$codeB.$codeC.$codeD;
if($codeA=="000") {
	$code=$codeA=$codeB=$codeC=$codeD="";
}

if(strlen($codeA)>0) {
	$likecode=$codeA;
	if(strlen($codeB>0)) $likecode.=$codeB;
	if(strlen($codeC>0)) $likecode.=$codeC;
	if(strlen($codeD>0)) $likecode.=$codeD;

	$len=strlen($likecode);
}

$level=2;
if($depth==2) $level=2;
else if($depth==3) $level=3;
else if($depth==4) $level=4;


$code_str="분류선택";
if($depth=="2") $code_str="중 분 류";
else if($depth=="3") $code_str="소 분 류";
else if($depth=="4") $code_str="세 분 류";

?>

<html>
<head>
<title><?=$code?>A:<?=$codeA?> B:<?=$codeB?> C:<?=$codeC?> D:<?=$codeD?></title>

<script type="text/javascript" src="lib.js.php"></script>
<SCRIPT LANGUAGE="JavaScript">
<!--
function sectSendIt(f,obj,x) {
	if(obj.value.length>0) {
		if(x == 2) {
			if(obj.ctype=="X") {
				f.code.value = obj.value+"000000";
			} else {
				f.code.value = obj.value;
			}
			durl = "mobile_product_category.php?depth=4";
			url = "mobile_product_category.php?depth=3&code="+obj.value;
			parent.CCodeCtgr.location.href = url;
			parent.DCodeCtgr.location.href = durl;
		} else if(x == 3) {
			if(obj.ctype=="X") {
				f.code.value = obj.value+"000";
			} else {
				f.code.value = obj.value;
			}
			url = "mobile_product_category.php?depth=4&code="+obj.value;
			parent.DCodeCtgr.location.href = url;
		} else if(x == 4) {
			f.code.value = obj.value;
		}
	} else {
		if(x == 2) {
			f.code.value=f.code1.options[f.code1.selectedIndex].value;
			durl = "mobile_product_category.php?depth=4";
			url = "mobile_product_category.php?depth=3";
			parent.CCodeCtgr.location.href = url;
			parent.DCodeCtgr.location.href = durl;
		} else if(x == 3) {
			f.code.value=parent.BCodeCtgr.form1.code.options[parent.BCodeCtgr.form1.code.selectedIndex].value;
			url = "mobile_product_category.php?depth=4";
			parent.DCodeCtgr.location.href = url;
		} else if(x == 4) {
			f.code.value=parent.CCodeCtgr.form1.code.options[parent.CCodeCtgr.form1.code.selectedIndex].value;
		}
	}
}
//-->
</SCRIPT>
<link rel=stylesheet href="style.css" type=text/css>
</head>
<body leftmargin=0 topmargin=0>
<form name="form1" method="get" action="">
	<table border="0" cellpadding="0" cellspacing=0>
		<tr>
			<td>
				<select class="select" name="code" style="width:143;height:21;" onchange="sectSendIt(parent.form1,this.options[this.selectedIndex],<?=$level?>);">
					<option value="">---- <?=$code_str?> ----</option>
					<?
							if(strlen($code)>=3) {
								$sql = "SELECT codeA,codeB,codeC,codeD,code_name,type FROM tblproductcode ";
								$sql.= "WHERE codeA='".$codeA."' ";
								
								if($depth==4){
									$sql.= "AND codeB='".$codeB."' AND codeC='".$codeC."' AND codeD!='000' ";
								}elseif($depth==3){
									$sql.= "AND codeB='".$codeB."' AND codeC!='000' AND codeD='000' ";
								}elseif($depth==2){
									$sql.= "AND codeB!='000' AND codeC='000' AND codeD='000' ";
								}
								
								$sql.= "AND type LIKE 'L%' ORDER BY sequence DESC ";
								$result=mysql_query($sql,get_db_conn());
															
								while($row=mysql_fetch_object($result)) {
									$codeval=$row->codeA;
									if($depth==2) $codeval.=$row->codeB;
									else if($depth==3) $codeval.=$row->codeB.$row->codeC;
									else if($depth==4) $codeval.=$row->codeB.$row->codeC.$row->codeD;
									$ctype=substr($row->type,-1);
									if($ctype!="X") {
										$ctype="";
									}

									$str_sel="";
									if($row->codeB == $tempB && $tempB !="000" && $depth==2){
										$str_sel="selected";
									}else if($row->codeC == $tempC && $tempC !="000" && $depth==3){
										$str_sel="selected";
									}else if($row->codeD == $tempD && $tempD !="000" && $depth==4){
										$str_sel="selected";
									}
									echo "<option value=\"".$codeval."\" ctype='".$ctype."' ".$str_sel.">".$row->code_name."";
									
									if($ctype=="X") {
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
	<p style="font-size:10px;width:90px"></p>
</form>
</body>
</html>