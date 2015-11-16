<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/admin_more.php");

if(strlen($_ShopInfo->getId())==0){
	echo "<script>alert('정상적인 경로로 접근하시기 바랍니다.');window.close();</script>";
	exit;
}

$vender=$_POST["vender"];
$date=$_POST["date"];

if($vender==NULL || strlen($date)!=8) {
	echo "<script>alert('잘못된 접근입니다.');window.close();</script>";
	exit;
}

/*
if($date>date("Ymd")) {
	echo "<html></head><body onload=\"alert('날짜가 잘못되었습니다.');window.close();\"></body></html>";exit;
}
*/

$tmpdate=substr($date,0,4)."/".substr($date,4,2)."/".substr($date,6,2);

$sql = "SELECT * FROM tblvenderinfo WHERE vender='".$vender."' AND delflag='N' ";
$result=mysql_query($sql,get_db_conn());
if(!$vdata=mysql_fetch_object($result)) {
	echo "<html><head></head><body onload=\"alert('해당 입점업체가 존재하지 않습니다.');window.close();\"></body></html>";exit;
}
mysql_free_result($result);

$str_account="";
if(strlen($vdata->bank_account)>0) {
	$tmpaccount=explode("=",$vdata->bank_account);
	$str_account=$tmpaccount[0]." ".$tmpaccount[1]." (예금주 : ".$tmpaccount[2].")";
}

$sql = "SELECT * FROM order_account_new WHERE vender='".$vender."' AND date='".$date."' ";
$result=mysql_query($sql,get_db_conn());
if($adata=mysql_fetch_object($result)) {
	$type="update";
} else {
	$type="insert";
}
mysql_free_result($result);

if ($type=="insert") {
	$price_array = getVenderOrderAdjust($vender, substr($date,0,4), substr($date,4,2), substr($date,6,2));
	$price = $price_array['adjust'];

	if ($price==0) {
		echo "<html></head><body onload=\"alert('정산할 내역이 없습니다.');window.close();\"></body></html>";exit;
	}
}

$mode=$_POST["mode"];
if($mode=="insert" && $type=="insert") {
	$price=$_POST["price"];
	$bank_account=$_POST["bank_account"];
	$memo=$_POST["memo"];
	
	/*
	$sql = "INSERT order_account_new SET ";
	$sql.= "vender		= '".$vender."', ";
	$sql.= "date		= '".$date."', ";
	$sql.= "price		= '".$price."', ";
	$sql.= "bank_account= '".$bank_account."', ";
	$sql.= "memo		= '".$memo."' ";
	mysql_query($sql,get_db_conn());
	*/
	 insertVenderOrderAccount($vender, $date, $price, $bank_account, $memo);

	//echo "<html><head></head><body onload=\"alert('".$tmpdate." 정산내역이 등록되었습니다.');opener.formSubmit();window.close();\"></body></html>";exit;
	echo "<html><head></head><body onload=\"alert('".$tmpdate." 정산내역이 등록되었습니다.');opener.location.reload();window.close();\"></body></html>";exit;
} else if($mode=="update" && $type=="update") {

	$price=$_POST["price"];

	$bank_account=$_POST["bank_account"];
	$memo=$_POST["memo"];
	$sql = "UPDATE order_account_new SET ";
	if($adata->confirm=="N") {
		$sql.= "price		= '".$price."', ";
		$sql.= "bank_account= '".$bank_account."', ";
	}
	$sql.= "reg_date	= now(), ";
	$sql.= "memo		= '".$memo."' ";
	$sql.= "WHERE vender='".$vender."' AND date='".$date."' ";
	mysql_query($sql,get_db_conn());
	//echo "<html><head></head><body onload=\"alert('".$tmpdate." 정산내역 수정이 완료되었습니다.');opener.formSubmit();window.close();\"></body></html>";exit;
	echo "<html><head></head><body onload=\"alert('".$tmpdate." 정산내역 수정이 완료되었습니다.');opener.location.reload();window.close();\"></body></html>";exit;
} else if($mode=="delete" && $type=="update") {
	$sql = "DELETE FROM order_account_new ";
	$sql.= "WHERE vender='".$vender."' AND date='".$date."' ";
	mysql_query($sql,get_db_conn());
	//echo "<html><head></head><body onload=\"alert('".$tmpdate." 정산내역이 삭제되었습니다.');opener.formSubmit();window.close();\"></body></html>";exit;
	echo "<html><head></head><body onload=\"alert('".$tmpdate." 정산내역이 삭제되었습니다.');opener.location.reload();window.close();\"></body></html>";exit;
}

?>

<html>
<head>
<title>관리자 페이지</title>
<meta http-equiv="Content-Type" content="text/html; charset=EUC-KR">
<script type="text/javascript" src="lib.js.php"></script>
<link rel="stylesheet" href="style.css">
<script language=Javascript>
function PageResize() {
	var oWidth = document.all.table_body.clientWidth + 10;
	var oHeight = document.all.table_body.clientHeight + 65;

	window.resizeTo(oWidth,oHeight);
}
<?if($type=="update"){?>
function formUpdate() {
	if(typeof(document.form1.price)=="object") {
		if(document.form1.price.value.length==0) {
			alert("정산금액을 입력하세요.");
			document.form1.price.focus();
			return;
		}
	}
	if(typeof(document.form1.bank_account)=="object") {
		if(document.form1.bank_account.value.length==0) {
			alert("입금 계좌를 입력하세요.");
			document.form1.bank_account.focus();
			return;
		}
	}
	if(confirm("정산내역 정보를 수정하시겠습니까?")) {
		document.form1.mode.value="update";
		document.form1.submit();
	}
}

function formDelete() {
	if(confirm("정말 삭제하시겠습니까?")) {
		document.form1.mode.value="delete";
		document.form1.submit();
	}
}
<?}else{?>
function formWrite() {
	if(document.form1.price.value.length==0) {
		alert("정산금액을 입력하세요.");
		document.form1.price.focus();
		return;
	}
	if(document.form1.bank_account.value.length==0) {
		alert("입금 계좌를 입력하세요.");
		document.form1.bank_account.focus();
		return;
	}
	//if(confirm("정산 처리를 하시겠습니까?")) {
	if(confirm("지급완료 처리 시 정산처리단계 수정이 불가능합니다.\n지금 지급완료 처리하시겠습니까?")) {

		document.form1.mode.value="insert";
		document.form1.submit();
	}
}
<?}?>
</script>
</head>

<body marginwidth=0 marginheight=0 leftmargin=0 topmargin=0 style="overflow-x:hidden;overflow-y:hidden;" onLoad="PageResize();">
<center>
<table border=0 cellpadding=0 cellspacing=0 width=450 style="table-layout:fixed;" id=table_body>
<tr>
	<td width=100%>
	<table border=0 cellpadding=3 cellspacing=0 width=100% style="table-layout:fixed;">
	<tr>
		<td height="31" background="images/member_mailallsend_imgbg.gif" style="padding-left:15px;"><FONT COLOR="#ffffff"><B><?=$tmpdate?>
		<?
		if($type=="update") echo " 정산내역";
		if($type=="insert") echo " 정산처리";
		?>
		</B></FONT></td>
	</tr>
	</table>

	<table border=0 cellpadding=0 cellspacing=0 width=100%>
	<form name=form1 method=post action="<?=$_SERVER[PHP_SELF]?>">
	<input type=hidden name=mode>
	<input type=hidden name=date value="<?=$date?>">
	<input type=hidden name=vender value="<?=$vender?>">
	<tr>
		<td style="padding:5px;">
		<table border="0" cellpadding="0" cellspacing="0" width="100%">
		<col width=90></col>
		<col width=></col>
		<TR>
			<TD colspan=2 background="images/table_top_line.gif"></TD>
		</TR>
		<tr>
			<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">입점업체</td>
			<TD class="td_con1"><B><?=$vdata->id?></B> - <?=$vdata->com_name?></td>
		</tr>
		<TR>
			<TD colspan="2" background="images/table_con_line.gif"></TD>
		</TR>
		<tr>
			<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">정산일자</td>
			<TD class="td_con1"><B><?=$tmpdate?></B></td>
		</tr>
		<TR>
			<TD colspan="2" background="images/table_con_line.gif"></TD>
		</TR>
		<tr>
			<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">정산금액</td>
			<TD class="td_con1">
			<?if($type=="update"){?>
				<?if($adata->confirm=="Y"){?>
				<B><?=number_format($adata->price)?>원</B>
				<?}else{?>
					<input type="text" name="price" value="<?=$adata->price?>" maxlength=10 onkeyup="strnumkeyup(this)" style="width:80px; text-align:right" class="input">원
				<?}?>
			<?}else{?>
				<input type="text" name="price" maxlength="10" onkeyup="strnumkeyup(this)" value="<?= $price ?>" style="width:80px; text-align:right" class="input">원
			<?}?>
			</td>
		</tr>
		<TR>
			<TD colspan="2" background="images/table_con_line.gif"></TD>
		</TR>
		<?if($type=="update"){?>
		<tr>
			<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">입금확인</td>
			<TD class="td_con1"><B><?=($adata->confirm=="Y"?"입점업체 입금 확인":"<font color=red>입점업체 입금 미확인</font>")?></B>
			</td>
		</tr>
		<TR>
			<TD colspan="2" background="images/table_con_line.gif"></TD>
		</TR>
		<?}?>
		<tr>
			<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">입금계좌</td>
			<TD class="td_con1">
			<?if($type=="update"){?>
				<?if($adata->confirm=="Y"){?>
				<B><?=$adata->bank_account?></B>
				<?}else{?>
				<input type=text name=bank_account value="<?=$adata->bank_account?>" maxlength=100 style="width:96%;">
				<?}?>
			<?}else{?>
				<input type=text name=bank_account value="<?=$str_account?>" maxlength=100 style="width:96%;">
			<?}?>
			</td>
		</tr>
		<TR>
			<TD colspan="2" background="images/table_con_line.gif"></TD>
		</TR>
		<tr>
			<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">정산메모</td>
			<TD class="td_con1">
			<textarea name="memo" style="width:96%; height:80px" class="textarea"><?=$adata->memo?></textarea>
			</td>
		</tr>
		<TR>
			<TD colspan=2 background="images/table_top_line.gif"></TD>
		</TR>
		<tr><td height=10></td></tr>
		<tr>
			<td colspan="2" align="center">
				<?if($type=="update"){?>
				&nbsp;<A HREF="javascript:formUpdate()"><img src="images/btn_modify02.gif" border="0" alt="" /></A>
				<!--
				&nbsp;<A HREF="javascript:formDelete()"><img src=images/btn_delete.gif border=0></A>
				-->
				<?} else {?>
				&nbsp;<A HREF="javascript:formWrite();"><img src="images/btn_calculate02.gif" border="0" alt="" /></A>
				<?}?>
				<A HREF="javascript:window.close();"><img src="images/btn_close.gif" border="0" alt="" /></A>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	</form>
	<tr><td height=10></td></tr>
	</table>
	</td>
</tr>

</table>
</center>
</body>
</html>