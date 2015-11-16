<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
INCLUDE ("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "sh-3";
$MenuCode = "shop";
if (!$_usersession->isAllowedTask($PageCode)) {
	INCLUDE ("AccessDeny.inc.php");
	exit;
}
#########################################################

$type=$_POST["type"];
$up_recom_ok=$_POST["up_recom_ok"];
$up_recom_addreserve=$_POST["up_recom_addreserve"];
$up_recom_memreserve_type=$_POST["up_recom_memreserve_type"];
$up_recom_memreserve=$_POST["up_recom_memreserve_$up_recom_memreserve_type"];
$up_recom_memreserve_chk=$_POST["up_recom_memreserve_chk"];
$up_recom_memreserve_chk2=$_POST["up_recom_memreserve_chk2"];

$orgMemRecommandReserve = $_POST["orgMemRecommandReserve"];
$orgMemRecommandReserveType1 = $_POST["orgMemRecommandReserveType1"];
$orgMemRecommandReserveType2 = $_POST["orgMemRecommandReserveType2"];
$newMemRecommandReserve = $_POST["newMemRecommandReserve"];

if($up_recom_memreserve_type =="A")
{
	$up_recom_memreserve_chk="";$up_recom_memreserve_chk2="";
}
$recom_memreserve_type = $up_recom_memreserve_type."".$up_recom_memreserve_chk."".$up_recom_memreserve_chk2;
$up_recom_limit=$_POST["up_recom_limit"];
$up_recom_url_ok=$_POST["up_recom_url_ok"];
if(!$up_recom_url_ok) $up_recom_url_ok="N";






// 정보 수정
if ($type=="up") {
	$sql = "UPDATE tblshopinfo SET ";
	$sql.= "recom_ok			= '".$up_recom_ok."', ";
	$sql.= "recom_url_ok			= '".$up_recom_url_ok."', ";
	$sql.= "recom_memreserve	= '".$up_recom_memreserve."', ";
	$sql.= "recom_memreserve_type	= '".$recom_memreserve_type."', ";
	$sql.= "recom_addreserve	= '".$up_recom_addreserve."', ";
	if(strlen($up_recom_limit)==0) {
		$sql.= "recom_limit	= NULL ";
	} else {
		$sql.= "recom_limit	= '".$up_recom_limit."' ";
	}
	mysql_query($sql,get_db_conn());

	// 추천인 정보 저장 ====================
	$arr = array();
	$arr['orgMemRecommandReserve'] = $orgMemRecommandReserve;
	$arr['orgMemRecommandReserveType1'] = $orgMemRecommandReserveType1;
	$arr['orgMemRecommandReserveType2'] = $orgMemRecommandReserveType2;
	$arr['newMemRecommandReserve'] = $newMemRecommandReserve;
	recommandSetting( $arr ); // 저장


	$onload="<script>alert('추천인 제도 설정이 완료되었습니다.');</script>\n";
} else{
	$orgMemRecommandReserve = 0; // 가입 추천한 회원 적립금
	$orgMemRecommandType = "join"; // 가입 추천한 회원 적립 지급 타입 join : 가입즉시적립 / orderA : 상품구매완료 1회적립 / 상품구매완료때 마다 지속 적립
	$newMemRecommandReserve = 0; // 추천받아 가입한 회원 적립금

	$set = recommandSetting(); // 호출
	foreach( $set as $k => $v ) {
		${$k} = $v;
	}
}



$sql = "SELECT recom_ok,recom_url_ok,recom_memreserve,recom_memreserve_type,recom_addreserve,recom_limit FROM tblshopinfo ";
$result=mysql_query($sql,get_db_conn());
if ($row=mysql_fetch_object($result)) {
	$recom_ok=$row->recom_ok;
	$recom_url_ok=$row->recom_url_ok;
	$recom_memreserve=$row->recom_memreserve;
	$recom_memreserve_type=$row->recom_memreserve_type;
	$recom_addreserve=$row->recom_addreserve;
	$recom_limit=$row->recom_limit;
	$arRecomType = explode("",$recom_memreserve_type);
}
mysql_free_result($result);
${"check_recom_ok".$recom_ok} = "checked";








?>

<? INCLUDE "header.php"; ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm() {
	if(isNaN(document.form1.up_recom_limit.value)){
		alert('추천인 인원 제한수는 숫자만 입력 가능합니다.');
		document.form1.up_recom_limit.focus();
		return;
	}
	document.form1.type.value="up";
	document.form1.submit();
}
function rsvType(val){
	if(val =="A"){
		document.form1.up_recom_memreserve_B.value="";
		document.getElementById("up_recom_memreserve_A").disabled = false;
		document.getElementById("recom_typeB").style.display = "none";
	}else if(val =="B"){
		document.getElementById("up_recom_memreserve_A").disabled = true;
		document.getElementById("recom_typeB").style.display = "block";
	}
}
function set_RecomUrl(val){
	if(val =="N"){
		document.form1.up_recom_url_ok.disabled = true;
		document.form1.up_recom_url_ok.checked = false;
	}else if(val =="Y"){
		document.form1.up_recom_url_ok.disabled = false;
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
			<? include ("menu_shop.php"); ?>
			</td>

			<td></td>
			<td valign="top">




<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td height="29" colspan="3">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 상점관리 &gt; 쇼핑몰 운영 설정 &gt; <span class="2depth_select">추천인 제도 설정</span></td>
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
					<TD><IMG SRC="images/shop_recommand_title.gif"  ALT=""></TD>
					</tr>
<tr>
<TD width="100%" background="images/title_bg.gif" height="21"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height="3"></td></tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/distribute_01.gif"></TD>
					<TD COLSPAN=2 background="images/distribute_02.gif"></TD>
					<TD><IMG SRC="images/distribute_03.gif"></TD>
				</TR>
				<TR>
					<TD background="images/distribute_04.gif"></TD>
					<TD class="notice_blue"><IMG SRC="images/distribute_img.gif" ></TD>
					<TD width="100%" class="notice_blue"><p>회원가입을 추천한 추천인에게 각종 혜택을 부여할 수 있습니다. 타 쇼핑몰과 차별화되는 추천인제도를 활용해 보세요.</p></TD>
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
			<tr><td height="20"></td></tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/shop_recommand_stitle1.gif" WIDTH="151" HEIGHT=31 ALT=""></TD>
					<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif">&nbsp;</TD>
					<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height=3></td></tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/distribute_01.gif"></TD>
					<TD COLSPAN=2 background="images/distribute_02.gif"></TD>
					<TD><IMG SRC="images/distribute_03.gif"></TD>
				</TR>
				<TR>
					<TD background="images/distribute_04.gif"></TD>
					<TD class="notice_blue"><IMG SRC="images/distribute_img.gif" ></TD>
					<TD width="100%" class="notice_blue">1) 추천인 사용시 회원가입페이지에 추천인 입력란이 자동 생성됩니다.<br>2) 추천에 대한 혜택이 있을 경우에는 부작용을 예방하기 위해 <b>실명인증서비스</b> 이용을 권장합니다.</TD>
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
			<tr><td height=3></td></tr>
			<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post>
			<input type=hidden name=type>
			<tr>
				<td>
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<TD colspan=2 background="images/table_top_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">추천인 적용여부 선택</TD>
					<TD class="td_con1">
						<span style="float:left;"><input type=radio id="idx_recom_ok2" name=up_recom_ok value="N" <?=$check_recom_okN?> onclick="set_RecomUrl('N')"><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_recom_ok2>추천인 사용불가</label>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<input type=radio id="idx_recom_ok1" name=up_recom_ok value="Y" <?=$check_recom_okY?>  onclick="set_RecomUrl('Y')"><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_recom_ok1>추천인 사용</label></span>
						<span id="hongbo_wrap" style="float:left;">(<input type=checkbox id="up_recom_url_ok" name=up_recom_url_ok value="Y" <?=($recom_url_ok=="Y")? "checked":""?> <?=($check_recom_okY)? "":"disabled"?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=up_recom_url_ok>회원가입 홍보url 기능사용</label>)</span>
					</TD>
				</TR>
				<TR>
					<TD colspan=2 background="images/table_top_line.gif"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height="30"></td></tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/shop_recommand_stitle2.gif" WIDTH="151" HEIGHT=31 ALT=""></TD>
					<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif">&nbsp;</TD>
					<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height=3></td></tr>





			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/distribute_01.gif"></TD>
					<TD COLSPAN=2 background="images/distribute_02.gif"></TD>
					<TD><IMG SRC="images/distribute_03.gif"></TD>
				</TR>
				<TR>
					<TD background="images/distribute_04.gif"></TD>
					<TD class="notice_blue"><IMG SRC="images/distribute_img.gif" ></TD>
					<TD width="100%" class="notice_blue">
						1) My page > 적립금에서 추가된 적립금 확인 가능합니다.<br>
						2) 적립금을 사용 하지 않으시려면 0원으로 표기 하시면 됩니다.<br>
						3) 가입 추천한 회원은 즉시적립 되거나 추천받은 회원이 상품 구매시 최초1회 또는 지속적으로 구매시마다 적립됩니다.<br>
						4) 추천받아 가입한 회원은 가입즉시 적립됩니다.
					</TD>
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
			<tr><td height=3></td></tr>
			<tr>
				<td>
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<TD colspan=2 background="images/table_top_line.gif"></TD>
				</TR>
				<TR id="snsTypeWrap">
					<TD colspan="2">
					<table WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
					<col width="139">
					<col>
					<TR>
						<TD colspan="2"  background="images/table_con_line.gif"></TD>
					</TR>
					<TR>
						<TD class="table_cell" width="139px"><img src="images/icon_point2.gif" width="8" height="11" border="0">가입 추천한 회원</TD>
						<TD class="td_con1" >
							<input name="orgMemRecommandReserve" value="<?=$orgMemRecommandReserve?>" size=10 maxlength=6 class="input" style="text-align:right;"> 원
							<select name="orgMemRecommandReserveType1" class="select" onChange="orgMemRecommandReserveType2.style.display=(this.value=='join')?'none':'inline';">
								<option value="join"<?=($orgMemRecommandType == "join")?" selected":""?>>가입즉시 적립</option>
								<option value="order"<?=($orgMemRecommandType == "orderA" OR $orgMemRecommandType == "orderB" )?" selected":""?>>상품 구매완료시 적립</option>
							</select>
							<select name="orgMemRecommandReserveType2" class="select" style="display:<?=($orgMemRecommandType=="join"?"none":"inline")?>">
								<option value="orderA"<?=($orgMemRecommandType == "orderA" )?" selected":""?>>1회지급</option>
								<option value="orderB"<?=($orgMemRecommandType == "orderB" )?" selected":""?>>지속지급</option>
							</select>
						</TD>
					</TR>
					<TR>
						<TD colspan="2"  background="images/table_con_line.gif"></TD>
					</TR>
					<TR>
						<TD class="table_cell" width="139px"><img src="images/icon_point2.gif" width="8" height="11" border="0">추천받아 가입한 회원</TD>
						<TD class="td_con1">
							<input name="newMemRecommandReserve" value="<?=$newMemRecommandReserve?>" size=10 maxlength=6 class="input" style="text-align:right;"> 원 (가입즉시 적립)
						</TD>
					</TR>


					</table>
					</TD>
				</TR>


				<TR>
					<TD colspan=2 background="images/table_top_line.gif"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height="30"></td></tr>
















			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/distribute_01.gif"></TD>
					<TD COLSPAN=2 background="images/distribute_02.gif"></TD>
					<TD><IMG SRC="images/distribute_03.gif"></TD>
				</TR>
				<TR>
					<TD background="images/distribute_04.gif"></TD>
					<TD class="notice_blue"><IMG SRC="images/distribute_img.gif" ></TD>
					<TD width="100%" class="notice_blue">1) 1인당 추천수를 제한 할 수 있습니다.<br>2) 숫자를 입력하지 않을 경우 무제한 추천이 가능합니다.("0"도 동일) <b>미사용시는 추천인 사용불가</b> 설정을 해주세요.</TD>
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
			<tr><td height=3></td></tr>
			<tr>
				<td>
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<TD colspan=2 background="images/table_top_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">추천 가능한 인원</TD>
					<TD class="td_con1"><input type=text name=up_recom_limit value="<?=$recom_limit?>" size=5 maxlength=4 class="input"> 명 <span class="font_orange">* <b>추천인 사용으로 설정한 경우</b> 추천 가능한 회원수</span></TD>
				</TR>
				<TR>
					<TD colspan=2 background="images/table_top_line.gif"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height=10></td></tr>
			<tr>
				<td align="center"><a href="javascript:CheckForm();"><img src="images/botteon_save.gif" width="113" height="38" border="0"></a></td>
			</tr>
			</form>
			<tr><td height=20></td></tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/manual_top1.gif" WIDTH=15 HEIGHT=45 ALT=""></TD>
					<TD><IMG SRC="images/manual_title.gif" WIDTH=113 HEIGHT=45 ALT=""></TD>
					<TD width="100%" background="images/manual_bg.gif"></TD>
					<TD background="images/manual_bg.gif"></TD>
					<TD><IMG SRC="images/manual_top2.gif" WIDTH=18 HEIGHT=45 ALT=""></TD>
				</TR>
				<TR>
					<TD background="images/manual_left1.gif"></TD>
					<TD COLSPAN=3 width="100%" valign="top" bgcolor="white" style="padding-top:8pt; padding-bottom:8pt; padding-left:4pt;"class="menual_bg">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td ><span class="font_dotline">추천한 회원이 탈퇴한 경우</span></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td  class="space_top">- 추천만 하고 탈퇴하더라도 회원가입 즉시 추가된 적립금은 환수가 안됩니다.</td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td  class="space_top">- 추천으로 받은 적립금을 관리자가 차감할 수 있으나 회원정보 기록에 남습니다.</td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td  class="space_top">- <a href="javascript:parent.topframe.GoMenu(3,'member_list.php');"><span class="font_blue">회원관리 > 회원정보관리 > 회원정보관리</span></a> 에서 회원의 적립금을 관리할 수 있습니다.</td>
					</tr>
					<tr>
						<td height="20" colspan="2"></td>
					</tr>
					<tr>
						<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td ><span class="font_dotline">추천 가능한 인원의 기준</span></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td  class="space_top">- 현재 회원으로 유지되고 있는 추천한 회원을 기준으로 합니다.</td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td  class="space_top">- 추천만하고 탈퇴한 경우는 제한인원에 포함되지 않습니다.</td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td  class="space_top">- 회원 탈퇴시 관리자 인증 후 탈퇴로 설정해 놓으면 추천으로 받은 적립금을 검토하여 차감유무를 처리하는데 편리합니다.</td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td  class="space_top">- <a href="javascript:parent.topframe.GoMenu(1,'shop_member.php');"><span class="font_blue">상점관리 > 쇼핑몰 운영 설정 > 회원가입 관련 설정</span></a> 에서 회원 탈퇴를 설정할 수 있습니다.</td>
					</tr>
					<tr>
						<td height="20" colspan="2"></td>
					</tr>
					<tr>
						<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td ><span class="font_dotline">탈퇴한 회원을 추천 인원 제한에 포함시키지 않는 이유</span></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td  class="space_top">- 정상적 이유로도 탈퇴할 수 있음으로 탈퇴한 회원까지 제한인원에 포함할 경우<br><b>&nbsp;&nbsp;</b>선의의 추천을 더 할 수 없는 경우가 발생할 수 있기 때문에 제한인원에 포함시키지 않고 있습니다.</td>
					</tr>
					</table>
					</TD>
					<TD background="images/manual_right1.gif"></TD>
				</TR>
				<TR>
					<TD><IMG SRC="images/manual_left2.gif" WIDTH=15 HEIGHT=8 ALT=""></TD>
					<TD COLSPAN=3 background="images/manual_down.gif"></TD>
					<TD><IMG SRC="images/manual_right2.gif" WIDTH=18 HEIGHT=8 ALT=""></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height="50"></td></tr>
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
<script type="text/javascript">
rsvType('<?=$arRecomType[0]?>');
</script>
<? INCLUDE "copyright.php"; ?>