<?php
header('Content-Type: text/html; charset=euc-kr'); 
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

$code=$_REQUEST["code"];
$depth=$_REQUEST["depth"];

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
$sql = "SELECT codeA,codeB,codeC,codeD,code_name,type FROM tblproductcode ";
$sql.= "WHERE 1=1 ";
if(strlen($codeA)==3) {
	$sql.= "AND codeA='".$codeA."' ";
	if(strlen($codeB)==3) {
		$sql.= "AND codeB='".$codeB."' ";
		if(strlen($codeC)==3) {
			$sql.= "AND codeC='".$codeC."' ";
			if(strlen($codeD)==3) {
				$sql.= "AND codeD='".$codeD."' ";
			}
		}
	}
}
if($depth==1) $sql.= "AND codeB ='000' AND codeC='000' AND codeD='000' ";
if($depth==2) $sql.= "AND codeB!='000' AND codeC='000' AND codeD='000' ";
if($depth==3) $sql.= "AND codeC!='000' AND codeD='000' ";
if($depth==4) $sql.= "AND codeD!='000' ";
$sql.= "AND type LIKE 'L%' ORDER BY sequence DESC ";
$result=mysql_query($sql,get_db_conn());
?>

<select name="code<?=$depth?>" style="BACKGROUND-COLOR: #ebebeb;width:100%" class="select" onchange="selectCode(<?=$depth?>,this.options[this.selectedIndex]);">
	<option value="<?=$code?>" ctype="X"><?=$depth?>차분류 선택</option>
<?
while($row=mysql_fetch_object($result)) {
	$codeval=$row->codeA;
	if($depth==2) $codeval .=$row->codeB;
	else if($depth==3) $codeval .=$row->codeC;
	else if($depth==4) $codeval .=$row->codeD;
	$ctype=substr($row->type,-1);
	if($ctype!="X") $ctype="";
	echo "<option value=\"".$codeval."\" ctype='".$ctype."'>".$row->code_name."";
	if($ctype=="X" && $len<9) {
		echo " (단일분류)";
	}
	echo "</option>\n";
}
mysql_free_result($result);
?>
	</select>

