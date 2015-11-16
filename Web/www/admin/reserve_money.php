<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

if(strlen($_ShopInfo->getId())==0){
	echo "<script>alert('정상적인 경로로 접근하시기 바랍니다.');window.close();</script>";
	exit;
}

$type=$_POST["type"];
$mode=$_POST["mode"];
$id=$_POST["id"];
$allid=$_POST["allid"];
$date=$_POST["date"];
$productcode=$_POST["productcode"];
$reserve=$_POST["reserve"];
$content=$_POST["content"];

if (strlen($content)==0) {
	if ($type=="review") {
		$content = "상품리뷰 작성으로 인한 적립금";
	} else {
		$content = "관리자 임의 적립금 처리";
	}
}
if ($mode=="insert") {
	if (!empty($id) && !empty($reserve)) {
		if ($type!="review") $date = date("YmdHis");
		if($reserve>0) $reserve_yn="Y";
		else if($reserve<0) $reserve_yn="N";
		$sql.= "INSERT tblreserve SET ";
		$sql.= "id				= '".$id."', ";
		$sql.= "reserve			= ".$reserve.", ";
		$sql.= "reserve_yn		= '".$reserve_yn."', ";
		$sql.= "content			= '".$content."', ";
		$sql.= "date			= '".$date."' ";
		mysql_query($sql,get_db_conn());

		if (mysql_errno()==1062)  {
			echo "<script>alert('이미 적립금 반영이 되었습니다.');opener.location.reload();window.close();</script>";
			exit;
		} else {
			if($reserve<0) {
				$sql = "UPDATE tblmember SET reserve=if(reserve<".abs($reserve).",0,reserve-".abs($reserve).") WHERE id = '".$id."' ";
			} else {
				$sql = "UPDATE tblmember SET reserve=reserve+".abs($reserve)." WHERE id='".$id."' ";
			}

			mysql_query($sql,get_db_conn());
			if($type=="review" && strlen($productcode)>0){
				$sql = "UPDATE tblproductreview SET reserve=$reserve ";
				$sql.= "WHERE id = '".$id."' AND productcode = '".$productcode."' AND date = '".$date."' ";
				mysql_query($sql,get_db_conn());
			}
		}
	}
	echo "<script>alert('적립금 처리가 완료되었습니다.');opener.location.reload();window.close();</script>";
	exit;
} else if ($mode=="allinsert" && strlen($allid)>0) {
	$date = date("YmdHis");
	if($reserve>0) $reserve_yn="Y";
	else if($reserve<0) $reserve_yn="N";
	$allid=ereg_replace("\\\\","",substr($allid,0,-1));
	$exid=explode(",",$allid);
	$num=count($exid);
	$sql = "INSERT INTO tblreserve (id,reserve,reserve_yn,content,date) VALUES ";
	for($i=0;$i<$num;$i++) $sql.= " (".$exid[$i].",".$reserve.",'".$reserve_yn."','".$content."','".$date."'),";
	$sql=substr($sql,0,-1);
	mysql_query($sql,get_db_conn());
	if (mysql_errno()==1062) {
		echo "<script>alert('이미 적립금 반영이 되었습니다.');opener.location.reload();window.close();</script>";
		exit;
	} else {
		if($reserve<0) {
			$sql = "UPDATE tblmember SET reserve=if(reserve<".abs($reserve).",0,reserve-".abs($reserve).") WHERE id IN (".$allid.") ";
		} else {
			$sql = "UPDATE tblmember SET reserve=reserve+".abs($reserve)." WHERE id IN (".$allid.") ";
		}
		mysql_query($sql,get_db_conn());

		echo "<script>alert('선택하신 회원님들의 적립금 처리를 완료하였습니다.');opener.location.reload();window.close();</script>";
		exit;
	}
}
?>

<html>
<head>
<meta http-equiv='Content-Type' content='text/html;charset=euc-kr'>
<title>적립금 지급/차감</title>
<link rel="stylesheet" href="style.css" type="text/css">
<SCRIPT LANGUAGE="JavaScript">
<!--
document.onkeydown = CheckKeyPress;
document.onkeyup = CheckKeyPress;
function CheckKeyPress() {
	ekey = event.keyCode;

	if(ekey == 38 || ekey == 40 || ekey == 112 || ekey ==17 || ekey == 18 || ekey == 25 || ekey == 122 || ekey == 116) {
		event.keyCode = 0;
		return false;
	}
}

function PageResize() {
	var oWidth = document.all.table_body.clientWidth + 10;
	var oHeight = document.all.table_body.clientHeight + 80;

	window.resizeTo(oWidth,oHeight);
}

function CheckForm() {
	document.form1.reserve.value = str_replace(",","",document.form1.reserve.value);
	if(document.form1.reserve.value.length==0 || isNaN(document.form1.reserve.value)){
		alert('적립금을 입력하지 않으셨거나 숫자가 아닙니다.\n 다시 확인하시고 입력바랍니다.');
		document.form1.reserve.focus();
		return;
	}
	document.form1.submit();
}

function str_replace ( search, replace, subject ) {
    // Replace all occurrences of the search string with the replacement string
    //
    // +    discuss at: http://kevin.vanzonneveld.net/techblog/article/javascript_equivalent_for_phps_str_replace/
    // +       version: 801.3120
    // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // *     example 1: str_replace(' ', '.', 'Kevin van Zonneveld');
    // *     returns 1: 'Kevin.van.Zonneveld'

    var result = "";
    var prev_i = 0;
    for (i = subject.indexOf(search); i > -1; i = subject.indexOf(search, i)) {
        result += subject.substring(prev_i, i);
        result += replace;
        i += search.length;
        prev_i = i;
    }

    return result + subject.substring(prev_i, subject.length);
}

function number_format(num) {
	var num = num.toString();
	cks = '';
	num = num.replace(/,/g, "");
	var result = '';

	if(num.indexOf('-')!=-1) {
		cks = '-';
		num = num.replace(/-/g, "");
	}

	for(var i=0; i<num.length; i++) {
		var tmp = num.length-(i+1);
		if(i%3==0 && i!=0) result = ',' + result;
		result = num.charAt(tmp) + result;
	}

	if(cks=='-') return '-' + result;
	else return result;
}

function cnum(obj){
	vls = str_replace(",","",obj.value);
	obj.value = number_format(vls);
}

//-->
</SCRIPT>
</head>
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 oncontextmenu="return false" style="overflow-x:hidden;overflow-y:hidden;" ondragstart="return false" onselectstart="return false" oncontextmenu="return false" onLoad="PageResize();">
<TABLE WIDTH="450" BORDER=0 CELLPADDING=0 CELLSPACING=0 style="table-layout:fixed;" id=table_body>
<TR>
	<TD height="31">
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td><img src="images/poin_pm_title.gif"  height="31"></td>
		<td width="100%" background="images/member_mailallsend_imgbg.gif"></td>
	</tr>
	</table>
	</TD>
</TR>
<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post>
<input type=hidden name=mode value="<?=($type=="inreserve"?"allinsert":"insert")?>">
<input type=hidden name=id value="<?=$id?>">
<input type=hidden name=allid value="<?=$allid?>">
<?if($type=="review"){?>
<input type=hidden name=type value="<?=$type?>">
<input type=hidden name=date value="<?=$date?>">
<input type=hidden name=productcode value="<?=$productcode?>">
<?}?>
<TR>
	<TD style="padding:5pt;">
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td>
		<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
		<col width=118></col>
		<col width=></col>
		<TR>
			<TD colspan=2 background="images/table_top_line.gif"></TD>
		</TR>
		<TR>
			<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">적립/차감액 입력</TD>
			<TD class="td_con1"><input type=text name=reserve maxlength=10 style="width:80;text-align:right" class="input" onFocus="cnum(this);" onKeyUp="cnum(this);" onKeyDown="cnum(this);">원</TD>
		</TR>
		<TR>
			<TD colspan="2" background="images/table_con_line.gif"></TD>
		</TR>
		<TR>
			<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">적립/차감 사유</TD>
			<TD class="td_con1"><textarea name="content" style="width:98%; height:100px;" class="textarea"><?=$content?></textarea></TD>
		</TR>
		<TR>
			<TD colspan="2"></TD>
		</TR>
		<TR>
			<TD colspan=2 background="images/table_top_line.gif"></TD>
		</TR>
		</TABLE>
		</td>
	</tr>
	<tr>
		<td class="font_blue" style="padding-top:5pt; padding-bottom:5pt; padding-left:8pt;">* <b>예)적립시 500입력, 차감시 -500입력</b><br>* 적립/차감 사유는 처리 성격에 맞게 입력하시기 바랍니다.</td>
	</tr>
	<tr>
		<td class="font_blue"><hr size="1" noshade color="#F3F3F3"></td>
	</tr>
	</table>
	</TD>
</TR>
<TR>
	<TD align=center><a href="javascript:CheckForm();"><img src="images/btn_ok.gif" width="36" height="18" border="0" vspace="0" border=0></a>&nbsp;&nbsp;<a href="javascript:window.close();"><img src="images/btn_close.gif" width="36" height="18" border="0" vspace="0" border=0 hspace="2"></a></TD>
</TR>
</form>
</TABLE>
</body>
</html>