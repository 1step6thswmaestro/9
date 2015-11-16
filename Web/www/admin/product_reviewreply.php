<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

if(strlen($_ShopInfo->getId())==0){
	echo "<script>alert('정상적인 경로로 접근하시기 바랍니다.');window.close();</script>";
	exit;
}

$productcode=$_POST["productcode"];
$date=$_POST["date"];

if (strlen($productcode)==0 || strlen($date)==0) {
	echo "<script>window.close();</script>";
	exit;
}

$mode=$_POST["mode"];
$content1=$_POST["content1"];
$content2=$_POST["content2"];

/*if ($mode=="up") {
	if(strlen($content2)>0) $content = $content1."=".$content2;
	else $content = $content1;

	$sql = "UPDATE tblproductreview SET ";
	$sql.= "content = '".$content."' ";
	$sql.= "WHERE productcode='".$productcode."' AND date='".$date."'";
	mysql_query($sql,get_db_conn());
	echo "<script> alert ('해당 상품리뷰 정보가 저장되었습니다.');self.close();</script>\n";
	exit;
}*/


switch($mode){
	case "up":
		if(strlen($content2)>0) $content = $content1."=".$content2;
		else $content = $content1;

		$sql = "UPDATE tblproductreview SET ";
		$sql.= "content = '".$content."' ";
		$sql.= "WHERE productcode='".$productcode."' AND date='".$date."'";
		mysql_query($sql,get_db_conn());
		echo "<script> alert ('해당 상품리뷰 정보가 저장되었습니다.');self.close();</script>\n";
		exit;
	break;
	case "bcheck":
		$best = isset($_POST['best'])?trim($_POST['best']):"";
		
		if(strlen($best)>0){
			$sql = "UPDATE tblproductreview SET ";
			$sql.= "best = '".$best."' ";
			$sql.= "WHERE productcode='".$productcode."' AND date='".$date."'";
			mysql_query($sql,get_db_conn());
			if($best == "Y"){
				echo "<script> alert ('베스트로 지정되었습니다.');self.close();</script>\n";
			}else{
				echo "<script> alert ('베스트에서 제외되었습니다.');self.close();</script>\n";
			}
			exit;
		}else{
			echo "<script> alert ('필수값이 전달되지 않았습니다.');history.go(-1);</script>\n";
			exit;
		}
	break;
}
$attechdir ="../data/shopimages/productreview/";
$attechfile ="";
$src="";
$sql = "SELECT * FROM tblproductreview WHERE productcode = '".$productcode."' AND date = '".$date."' ";
$result = mysql_query($sql,get_db_conn());
if ($row = mysql_fetch_object($result)) {
	$reviewcontent = explode("=",$row->content);
	if(strlen($row->img) >0){
		$attechfile = $row->img;
		$src = $attechdir.$attechfile;
	}
	switch($row->best){
		case "N":
			$best="Y";
			$printmsg = "<img src=\"images/btn_bestreview.gif\" border=\"0\" align=\"absmiddle\" alt=\"베스트리뷰 지정\" />";
		break;
		case "Y":
			$best="N";
			$printmsg = "<img src=\"images/btn_bestreview_cancel.gif\" border=\"0\" align=\"absmiddle\" alt=\"베스트리뷰 취소\" />";
		break;
		default:
			$best="";
			$printmsg = "";
		break;
	}
	
} else {
	echo "<script>window.close();</script>";
	exit;
}




?>

<html>
<head>
<meta http-equiv='Content-Type' content='text/html;charset=euc-kr'>
<title>상품리뷰 수정/답변</title>
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
	var oHeight = document.all.table_body.clientHeight + 72;

	window.resizeTo(oWidth,oHeight);
}

function CheckForm() {
	if (confirm("해당 상품리뷰를 현재 정보로 저장 하시겠습니까?")) {
		document.form1.submit();
	}
}

function bestCheck(type){
	var _form = document.bestForm;
	
	if(type != ""){
		_form.best.value = type;
		_form.submit();
		return;
	}else{
		alert("필수값이 누락되었습니다.");
		return;
	}

}
//-->
</SCRIPT>
</head>
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 oncontextmenu="return false" style="overflow-x:hidden;overflow-y:hidden;" ondragstart="return false" onselectstart="return false" oncontextmenu="return false" onLoad="PageResize();">
<TABLE WIDTH="450" BORDER=0 CELLPADDING=0 CELLSPACING=0 style="table-layout:fixed;" id=table_body>
	<TR>
		<TD>
			<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td><img src="images/product_review_titlea.gif" border="0" width="212" height="31"></td>
					<td width="100%" background="images/member_find_titlebg.gif">&nbsp;</td>
					<td align=right><img src="images/member_find_titleimg.gif" width="20" height="31" border="0"></td>
				</tr>
			</table>
		</TD>
	</TR>
	<TR>
		<TD background="images/member_zipsearch_bg.gif">
			<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="18"></td>
					<td></td>
					<td width="18" height=10></td>
				</tr>
				<tr>
					<td width="18">&nbsp;</td>
					<td>
						<table cellpadding="0" cellspacing="0" width="100%">
							<tr>
								<td width="100%">
									<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post>
										<input type=hidden name=mode value="up">
										<input type=hidden name=productcode value="<?=$productcode?>">
										<input type=hidden name=date value="<?=$date?>">
										<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
											<col width=61></col>
											<col width=></col>
											<TR>
												<TD colspan=2 background="images/table_top_line.gif"></TD>
											</TR>
											<TR>
												<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">이름</TD>
												<TD class="td_con1"><B><?=$row->name?></B></TD>
											</TR>
											<TR>
												<TD colspan="2" background="images/table_con_line.gif"></TD>
											</TR>
											<TR>
												<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">평점</TD>
												<TD class="td_con1">
													<SPAN class=font_orange>
														<B>
															<?
																for($i=1;$i<=$row->marks;$i++) {
																echo "★";
															}
															?>
														</B>
													</SPAN>
												</TD>
											</TR>
											<TR>
												<TD colspan="2" background="images/table_con_line.gif"></TD>
											</TR>

											<? if(is_file($src)){ ?>
											<TR>
												<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">첨부파일</TD>
												<TD class="td_con1"><img src="<?=$src?>" width="120" alt="" /></TD>
											</TR>
											<TR>
												<TD colspan="2" background="images/table_con_line.gif"></TD>
											</TR>
											<?}?>

											<TR>
												<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">내용</TD>
												<TD class="td_con1"><textarea name="content1" style="width:100%; height:120; word-break:break-all;" class="textarea"><?=$reviewcontent[0]?></textarea></TD>
											</TR>
											<TR>
												<TD colspan="2" background="images/table_con_line.gif"></TD>
											</TR>
											<TR>
												<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">답변</TD>
												<TD class="td_con1"><textarea name="content2" style="width:100%; height:120; word-break:break-all;" class="textarea"><?=$reviewcontent[1]?></textarea></TD>
											</TR>
											<TR>
												<TD colspan=2 background="images/table_top_line.gif"></TD>
											</TR>
										</TABLE>
									</form>
								</td>
							</tr>
						</table>
					</td>
					<td width="18">&nbsp;</td>
				</tr>
				<tr>
					<td colspan="3" align="center">
						<a href="javascript:bestCheck('<?=$best?>');"><?=$printmsg?></a>&nbsp;<a href="javascript:CheckForm();"><img src="images/btn_save.gif" border="0" align="absmiddle" /></a>&nbsp;<a href="javascript:window.close();"><img src="images/btn_close.gif" border="0" align="absmiddle" /></a>
					</td>
				</tr>
				<tr>
					<td colspan="3" height="10"></td>
				</tr>
			</table>
		</TD>
	</TR>
</TABLE>
<form name="bestForm" action="<?=$_SERVER[PHP_SELF]?>" method="post">
	<input type="hidden" name="productcode" value="<?=$productcode?>"/>
	<input type="hidden" name="date" value="<?=$row->date?>"/>
	<input type="hidden" name="mode" value="bcheck"/>
	<input type="hidden" name="best" value=""/>
</form>
</body>
</html>