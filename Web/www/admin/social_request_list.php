<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
INCLUDE ("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "go-4";
$MenuCode = "gong";
if (!$_usersession->isAllowedTask($PageCode)) {
	INCLUDE ("AccessDeny.inc.php");
	exit;
}
#########################################################

//리스트 세팅
$setup[page_num] = 10;
$setup[list_num] = 15;

$block=$_REQUEST["block"];
$gotopage=$_REQUEST["gotopage"];

if ($block != "") {
	$nowblock = $block;
	$curpage  = $block * $setup[page_num] + $gotopage;
} else {
	$nowblock = 0;
}

if (($gotopage == "") || ($gotopage == 0)) {
	$gotopage = 1;
}

$seq = $_POST["seq"];
$mode = $_POST["mode"];

if($mode == "delete") {
	$checkseqs = $_POST["checkseq"];
	$checkseqs = substr($checkseqs,0,-1);
	$arSeq = explode(",",$checkseqs);
	$sql="DELETE FROM tblsnsGongguCmt WHERE seq in (".$checkseqs.")";
	mysql_query($sql, get_db_conn());
	$sql="UPDATE tblsnsGongguCmt SET count = count- ".count($arSeq)." WHERE seq =".$seq ;
	mysql_query($sql, get_db_conn());
}

$arIconImage = array("t"=>"twitter","f"=>"facebook","m"=>"me2day");
$sql = "SELECT A.* ";
$sql .=", (SELECT profile_img FROM tblmembersnsinfo B WHERE A.id=B.id ORDER BY B.regidate DESC limit 1) profile_img ";
$sql .="FROM tblsnsGongguCmt A ";
$sql .="WHERE c_seq='".$seq."' ";
$sql .="ORDER BY c_order, regidate DESC ";
$result=mysql_query($sql,get_db_conn());
?>

<? INCLUDE "header.php"; ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckDelete(){
	if(confirm("삭제시 복구가 불가능합니다.\n\n선택하신 목록을 삭제하시겠습니까?")){
		var checkvalue=false;
		if(document.Frm.seqchk[0].checked == true) {
			if(confirm("제안글을 삭제하시면 모든 글이 삭제됩니다.\n\n선택하신 목록을 삭제하시겠습니까?")){
				chkvalue = true;
				CheckAll();
			}else{
				return false;
			}
		}
		for(i=0;i<document.Frm.seqchk.length;i++){
			if(document.Frm.seqchk[i].checked == true){
				checkvalue=true;
				document.Frm.checkseq.value +=document.Frm.seqchk[i].value+",";
			}
		}
		if(checkvalue!=true){
			alert('삭제할 데이터가 선택되지 않았습니다.');
			return;
		}
		document.Frm.mode.value="delete";
		document.Frm.submit();
	}
}
var chkvalue = true;
function CheckAll(){
	document.Frm.seqchk.checked
	for(i=0;i<document.Frm.seqchk.length;i++){
		document.Frm.seqchk[i].checked = chkvalue;
	}
	if(chkvalue == true){
		chkvalue = false;
	}else{
		chkvalue = true;
	}
}
</script>

<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
<tr>
	<td valign="top">
	<table cellpadding="0" cellspacing="0" width=100% style="table-layout:fixed">
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed"  background="images/con_bg.gif">
		<col width=198></col>
		<col width=10></col>
		<col width=></col>
		<tr>
			<td valign="top"  background="images/leftmenu_bg.gif">
			<? include ("menu_gong.php"); ?>
			</td>

			<td></td>
			<td valign="top">

<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td height="29" colspan="3">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 소셜·경매 &gt; 소셜쇼핑 &gt; <span class="2depth_select">공동구매 신청관리</span></td>
			</tr>
			</table>
		</td>
	</tr>
	<tr>
        <td width="16"><img src="images/con_t_01.gif" width="16" height="16" border="0"></td>
        <td background="images/con_t_01_bg.gif"></td>
        <td width="16"><img src="images/con_t_02.gif" width="16" height="16" border="0"></td>
    </tr>
    <tr>
        <td width="16" background="images/con_t_04_bg1.gif"></td>
        <td bgcolor="#ffffff" style="padding:10px">

			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/social_request_title.gif" ALT="공동구매 신청관리"></TD>
					</tr><tr>
					<TD width="100%" background="images/title_bg.gif" height="21"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height=20></td></tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/social_request_stitle2.gif"  ALT="공동구매 요청자 목록"></TD>
					<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif">&nbsp;</TD>
					<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height=3></td></tr>
			<tr>
				<td style="padding-bottom:3pt;">
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/distribute_01.gif"></TD>
					<TD COLSPAN=2 background="images/distribute_02.gif"></TD>
					<TD><IMG SRC="images/distribute_03.gif"></TD>
				</TR>
				<TR>
					<TD background="images/distribute_04.gif"></TD>
					<TD class="notice_blue"><IMG SRC="images/distribute_img.gif" ></TD>
					<TD width="100%" class="notice_blue">공동구매 신청 가능 상품에 공동구매 요청된 목록을 관리할 수 있습니다..</TD>
					<TD background="images/distribute_07.gif"></TD>
				</TR>
				<TR>
					<TD><IMG SRC="images/distribute_08.gif"></TD>
					<TD COLSPAN=2 background="images/distribute_09.gif"></TD>
					<TD><IMG SRC="images/distribute_10.gif"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height=20></td></tr>
			<tr>
				<td>
				<form name="Frm" method="post" action="<?=$_SERVER[PHP_SELF]?>">
				<input type=hidden name=mode value="">
				<input type=hidden name=checkseq value="">
				<input type=hidden name=seq value="<?=$seq?>">
				<input type=hidden name=block value="<?=$block?>">
				<input type=hidden name=gotopage value="<?=$gotopage?>">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0 style="table-layout:fixed">
				<col width=40></col>
				<col width=50></col>
				<col width=70></col>
				<col width=80></col>
				<col width=100></col>
				<col ></col>
				<TR>
					<TD colspan=6 background="images/table_top_line.gif"></TD>
				</TR>
<?
$cnt=0;
while($row=mysql_fetch_object($result)) {
	$icon="";
	$artype = explode(",",$row->sns_type);
	for($i=0;$i<sizeof($artype)-1;$i++){
		$icon .= "<img src=\"../images/design/icon_".$arIconImage[$artype[$i]]."_on.gif\" align=\"absmiddle\" WIDTH=\"17\" HEIGHT=\"17\"> ";
	}
	$id = $row->id;
	$comment = $row->comment;
	$cmt_count = $row->count;
	$sns_date = date("Y-m-d H:i:s", $row->regidate);
	$profile_img = $row->profile_img;
	if(strlen($profile_img) == 0){
		$profile_img="/images/design/sns_default.jpg";
	}
	$mem_id = ($_ShopInfo->getMemid() == $row->id)? "":$row->id;

?>
				<TR align=center>
					<TD class="td_con" rowspan="3"><input type="checkbox" name="seqchk" value="<?=$row->seq?>"></TD>
					<TD class="td_con1" rowspan="3"><?=($row->c_order>1)? "":"제안글"?></TD>
					<TD class="td_con1" rowspan="3"><IMG SRC="<?=$profile_img?>" WIDTH="48" HEIGHT="48" ALT="" class="img"></TD>
					<TD class="td_con1"><?=$icon?>&nbsp;</td>
					<TD class="td_con1"><?=$id?></td>
					<TD class="td_con1" align="left"><?=$sns_date?></td>
				</TR>
				<TR>
					<TD colspan="3" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="td_con1" colspan="3"><?=$comment?></TD>
				</TR>
				<TR>
					<TD colspan="6" background="images/table_con_line.gif"></TD>
				</TR>
<?
	$cnt++;
}
if($cnt ==0){
?>
				<TR>
					<TD colspan="6"> 데이터가 없습니다.</TD>
				</TR>
<?}?>
				</TABLE>
				</form>
				</td>
			</tr>
			<tr>
				<td><div style="float:left"><input type="button" value="전체선택" onclick="CheckAll()"> <input type="button" value="선택삭제" onclick="CheckDelete()"></div>
				<div style="float:right">
				<form name="gongcmtFrm" method="post" action="social_request.php">
				<input type=hidden name=block value="<?=$block?>">
				<input type=hidden name=gotopage value="<?=$gotopage?>">
				<input type="submit" value="목록으로">
				</form>
				</div>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/manual_top1.gif" WIDTH=15 height="45" ALT=""></TD>
					<TD><IMG SRC="images/manual_title.gif" WIDTH=113 height="45" ALT=""></TD>
					<TD width="100%" background="images/manual_bg.gif" height="35"></TD>
					<TD background="images/manual_bg.gif">&nbsp;</TD>
					<td background="images/manual_bg.gif"><IMG SRC="images/manual_top2.gif" WIDTH=18 height="45" ALT=""></td>
				</TR>
				<TR>
					<TD background="images/manual_left1.gif"></TD>
					<TD COLSPAN=3 width="100%" valign="top" bgcolor="white" style="padding-top:8pt; padding-bottom:8pt; padding-left:4pt;">
					<table cellpadding="0" cellspacing="0" width="100%">
					<col width=20></col>
					<col width=></col>
					<tr>
						<td align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td><span class="font_dotline">공동구매 신청 관리</span></td>
					</tr>
					<tr>
						<td align="right">&nbsp;</td>
						<td class="space_top" style="letter-spacing:-0.5pt;">- 일반상품에서 공동구매가 가능한 상품들 중 이용자가 공동구매를 요청한 내역이 출력됨니다..</td>
					</tr>
					<tr>
						<td align="right">&nbsp;</td>
						<td class="space_top" style="letter-spacing:-0.5pt;">- 제안인 아이디와 함께 함께 신청한 사람수를 클릭하면 함께 신청한 목록을 보실수 있습니다.</td>
					</tr>
					<tr>
						<td align="right">&nbsp;</td>
						<td class="space_top" style="letter-spacing:-0.5pt;">- <b>[신청중]</b> - 쇼핑몰에서 함께 신청하기가 가능한 상태<br>
						&nbsp;&nbsp;<b>[미진행]</b> - 함께 신청하기가 불가능한 상태이고, 공동구매 상품으로 진행하지 않은 상태<br>
						&nbsp;&nbsp;<b>[진행중]</b> - 함께 신청하기가 불가능한 상태이고, 공동구매 진행 중인 상태<br>
						&nbsp;&nbsp;<b>[완료]</b> - 함께 신청하기가 불가능한 상태이고, 공동구매를 진행했으나 완료된 상태</td>
					</tr>
					<tr>
						<td align="right">&nbsp;</td>
						<td class="space_top" style="letter-spacing:-0.5pt;">- 공구등록 : 신청한 상품을 공동구매(소셜쇼핑) 상품으로 복사하는 기능.</td>
					</tr>
					<tr>
						<td align="right">&nbsp;</td>
						<td class="space_top" style="letter-spacing:-0.5pt;">- 공구알림 : 공동구매 신청시 메일,문자 알림을 요청한 이용자에게 공동구매 진행을 알리는 기능.</td>
					</tr>
					</table>
					</TD>
					<TD background="images/manual_right1.gif"><IMG SRC="images/manual_right1.gif" WIDTH=18 HEIGHT="2" ALT=""></TD>
				</TR>
				<TR>
					<TD><IMG SRC="images/manual_left2.gif" WIDTH=15 HEIGHT=8 ALT=""></TD>
					<TD COLSPAN=3 background="images/manual_down.gif"><IMG SRC="images/manual_down.gif" WIDTH="4" HEIGHT=8 ALT=""></TD>
					<TD><IMG SRC="images/manual_right2.gif" WIDTH=18 HEIGHT=8 ALT=""></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height="50"></td>
			</tr>

			</table>

				</td>
				<td width="16" background="images/con_t_02_bg.gif"></td>
			</tr>
			<tr>
				<td width="16"><img src="images/con_t_04.gif" width="16" height="16" border="0"></td>
				<td background="images/con_t_04_bg.gif"></td>
				<td width="16"><img src="images/con_t_03.gif" width="16" height="16" border="0"></td>
			</tr>
			<tr><td height="20"></td></tr>
		</table>

			</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>
</table>

<?=$onload?>

<? INCLUDE "copyright.php"; ?>