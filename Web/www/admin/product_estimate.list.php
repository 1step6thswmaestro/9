<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
INCLUDE ("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "pr-5";
$MenuCode = "nomenu";
if (!$_usersession->isAllowedTask($PageCode)) {
	INCLUDE ("AccessDeny.inc.php");
	exit;
}
#########################################################

$mode=$_POST["mode"];
$etccode=$_POST["etccode"];
$codes=$_POST["codes"];
$change=$_POST["change"];

if ($mode=="insert" && strlen($etccode)==12) {
	$codeA=substr($etccode,0,3); $codeB=substr($etccode,3,3);
	$codeC=substr($etccode,6,3); $codeD=substr($etccode,9,3);

	$sql = "SELECT COUNT(*) as cnt FROM tblproductcode WHERE estimate_set != 999 AND codeA='".$codeA."' ";
	if($codeB!="000") $sql.= "AND (codeB='".$codeB."' OR codeB='000') ";
	if($codeC!="000") $sql.= "AND (codeC='".$codeC."' OR codeC='000') ";
	if($codeD!="000") $sql.= "AND (codeD='".$codeD."' OR codeD='000') ";
	#echo $sql; exit;
	$result=mysql_query($sql,get_db_conn());
	$row=mysql_fetch_object($result);
	if ($row->cnt!=0) {
		echo "<script>location='".$_SERVER[PHP_SELF]."';parent.HiddenFrame.alert(\"선택된 카테고리가 이미 등록되었거나 상위카테고리가 등록되어 있습니다.\");</script>";
		exit;
	}
	mysql_free_result($result);

	$sql = "SELECT COUNT(*) as cnt FROM tblproductcode WHERE estimate_set != 999 ";
	$result=mysql_query($sql,get_db_conn());
	$row = mysql_fetch_object($result);
	mysql_free_result($result);
	if ($row->cnt>= 50) {
		echo "<script>location='".$_SERVER[PHP_SELF]."';parent.HiddenFrame.alert(\"견적서 카테고리는 50개까지 등록 가능합니다.\");</script>";
	} else {
		$sql = "UPDATE tblproductcode SET estimate_set = '".$row->cnt."' ";
		$sql.= "WHERE codeA='".$codeA."' ";
		$sql.= "AND codeB='".$codeB."' AND codeC='".$codeC."' AND codeD='".$codeD."' ";
		$update = mysql_query($sql,get_db_conn());
		$onload="<script>parent.ProcessResult('insert','".$etccode."');alert('견적서 카테고리 등록이 완료되었습니다.');</script>";
	}
} else if ($mode=="delete" && strlen($etccode)>0) {
	$codearray = explode("|",$etccode);
	$codeA=substr($codearray[0],0,3); $codeB=substr($codearray[0],3,3);
	$codeC=substr($codearray[0],6,3); $codeD=substr($codearray[0],9,3);
	$sql = "UPDATE tblproductcode SET estimate_set = 999 WHERE codeA='".$codeA."' AND codeB='".$codeB."' ";
	$sql.= "AND codeC='".$codeC."' AND codeD='".$codeD."' ";
	$update = mysql_query($sql,get_db_conn());

	$sql = "UPDATE tblproductcode SET estimate_set = estimate_set-1 ";
	$sql.= "WHERE estimate_set!=999 AND estimate_set>".$codearray[1];
	$update = mysql_query($sql,get_db_conn());
	$onload="<script>parent.ProcessResult('delete','".$codearray[0]."');alert('견적서 카테고리 삭제가 완료되었습니다.');</script>";
} else if ($mode=="sequence" && strlen($codes)>0) {
	$codesarray = explode(",",$codes);
	for($i=0;$i<count($codesarray);$i++){
		$codearray = explode("|", $codesarray[$i]);
		$codeA=substr($codearray[0],0,3); $codeB=substr($codearray[0],3,3);
		$codeC=substr($codearray[0],6,3); $codeD=substr($codearray[0],9,3);
		$sql = "UPDATE tblproductcode SET estimate_set=".$i." WHERE codeA='".$codeA."' AND codeB='".$codeB."' ";
		$sql.= "AND codeC='".$codeC."' AND codeD='".$codeD."' ";
		$update = mysql_query($sql,get_db_conn());
		$onload="<script>alert('견적서 카테고리 순서 변경이 완료되었습니다.');</script>";
	}
}
?>

<? INCLUDE "header.php"; ?>
<style>td {line-height:18pt;}</style>
<script type="text/javascript" src="lib.js.php"></script>
<script>var LH = new LH_create();</script>
<script for=window event=onload>LH.exec();</script>
<script>LH.add("parent_resizeIframe('ListFrame')");</script>

<script language="JavaScript">
function move(gbn) {
	change_idx = document.form1.est.selectedIndex;
	if (change_idx<0) {
		alert("순서를 변경할 견적서 카테고리를 선택하세요.");
		return;
	}
	if (gbn=="up" && change_idx==0) {
		alert("선택하신 견적서 카테고리는 더이상 위로 이동되지 않습니다.");
		return;
	}
	if (gbn=="down" && change_idx==(document.form1.est.length-1)) {
		alert("선택하신 견적서 카테고리는 더이상 아래로 이동되지 않습니다.");
		return;
	}
	if (gbn=="up") idx = change_idx-1;
	else idx = change_idx+1;

	idx_value = document.form1.est.options[idx].value;
	idx_text = document.form1.est.options[idx].text;

	document.form1.est.options[idx].value = document.form1.est.options[change_idx].value;
	document.form1.est.options[idx].text = document.form1.est.options[change_idx].text;

	document.form1.est.options[change_idx].value = idx_value;
	document.form1.est.options[change_idx].text = idx_text;

	document.form1.est.selectedIndex = idx;
	document.form2.change.value="Y";
}

function MoveSave() {
	if (document.form2.change.value!="Y") {
		alert("순서변경을 하지 않았습니다.");
		return;
	}
	if (!confirm("현재의 순서대로 저장하시겠습니까?")) return;
	codes = "";
	for (i=0;i<=(document.form1.est.length-1);i++) {
		codes+=","+document.form1.est.options[i].value;
	}
	document.form2.codes.value = codes;
	document.form2.submit();
}

</script>
<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0 height="100%" bgcolor="#FFFFFF" style="table-layout:fixed">
<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post>
<input type=hidden name=mode>
<col width=></col>
<col width=50></col>
<TR>
	<TD height="100%">
	<select name=est size=20 style="width:100%;height:100%;" class="select">
<?
	$sql = "SELECT CONCAT(codeA,codeB,codeC,codeD) as code,type,estimate_set,code_name FROM tblproductcode ";
	$sql.= "WHERE estimate_set != 999 ORDER BY estimate_set ";
	$result = mysql_query($sql,get_db_conn());

	$count=1;
	while ($row = mysql_fetch_object($result)) {
		$value = $row->code."|".$row->estimate_set;
		echo "<option value='".$value."'>".$count.".".$row->code_name;
		if(substr($row->code,3,9)=="000000000") {
			$cd_gbn="1";
		} else if(substr($row->code,6,6)=="000000") {
			$cd_gbn="2";
		} else if(substr($row->code,9,3)=="000") {
			$cd_gbn="3";
		} else {
			$cd_gbn="4";
		}

		if(ereg("X",$row->type)) {
			echo "(".$cd_gbn."차 단일카테고리)";
		} else {
			echo "(".$cd_gbn."차 카테고리)";
		}
		
		echo "</option>\n";
		$count++;
	}
	mysql_free_result($result);
?>
	</select>
	</TD>
	<TD align=middle>
	<table cellpadding="0" cellspacing="0">
	<TR>
		<TD align=middle><A href="JavaScript:move('up');"><IMG src="images/code_up.gif" align=absMiddle border=0 vspace="2"></A></td>
	</tr>
	<TR>
		<TD align=middle><IMG src="images/code_sort.gif" border="0"></td>
	</tr>
	<TR>
		<TD align=middle><A href="JavaScript:move('down');"><IMG src="images/code_down.gif" align=absMiddle border=0 vspace="2"></A></td>
	</tr>
	<TR>
		<TD height="20"></td>
	</tr>
	<TR>
		<TD align=middle><A href="JavaScript:MoveSave();"><IMG src="images/code_save.gif" align=absMiddle border=0 vspace="2"></A></td>
	</tr>
	</table>
	</TD>
</TR>
</form>
<form name=form2 action="<?=$_SERVER[PHP_SELF]?>" method=post>
<input type=hidden name=mode value="sequence">
<input type=hidden name=codes>
<input type=hidden name=change value="N">
</form>
</TABLE>
<?=$onload?>
