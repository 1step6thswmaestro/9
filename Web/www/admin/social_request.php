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

$mode = $_POST["mode"];
$up_seq = $_POST["seq"];
$up_rqt_state = $_POST["rqt_state"];

if($mode == "modify"){
	$sql="UPDATE tblsnsGongguCmt set rqt_state='".$up_rqt_state."' WHERE c_seq ='".$up_seq."'";
	mysql_query($sql, get_db_conn());
}else if($mode == "delete") {
	$sql="DELETE FROM tblsnsGongguCmt WHERE c_seq ='".$up_seq."'";
	mysql_query($sql, get_db_conn());
}


$sql = "SELECT COUNT(*) as t_count FROM tblsnsGongguCmt  WHERE c_order=1 ".$sCondition;
$result = mysql_query($sql,get_db_conn());

$row = mysql_fetch_object($result);
$t_count = $row->t_count;
mysql_free_result($result);
$pagecount = (($t_count - 1) / $setup[list_num]) + 1;
//echo $t_count;

$arIconImage = array("t"=>"twitter","f"=>"facebook","m"=>"me2day");
$sql = "SELECT A.*, B.tinyimage, B.productname, B.social_chk ";
$sql .="FROM tblsnsGongguCmt A, tblproduct B ";
$sql .="WHERE A.pcode=B.productcode ";
$sql .="AND c_order=1 ".$sCondition;
$sql .="ORDER BY regidate DESC ";
$sql .= "LIMIT " . ($setup[list_num] * ($gotopage - 1)) . ", " . $setup[list_num];
$result=mysql_query($sql,get_db_conn());
?>

<? INCLUDE "header.php"; ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function ProductInfo(code,prcode,popup,chk) {
	document.form_reg.code.value=code;
	document.form_reg.prcode.value=prcode;
	document.form_reg.popup.value=popup;
	if (popup=="YES") {
		if(chk == "0") { document.form_reg.action="product_register.add.php";}
		else if(chk == "3") { document.form_reg.action="social_shopping2.php";}
		else {document.form_reg.action="product2_register.add.php";}
		document.form_reg.target="register";
		window.open("about:blank","register","width=820,height=700,scrollbars=yes,status=no");
	} else {
		document.form_reg.action="product_register.php";
		document.form_reg.target="";
	}
	document.form_reg.submit();
}

function GoPage(block,gotopage) {
	document.gongcmtFrm.seq.value= "";
	document.gongcmtFrm.mode.value= "";
	document.gongcmtFrm.block.value = block;
	document.gongcmtFrm.gotopage.value = gotopage;
	document.gongcmtFrm.submit();
}

function modifyState(obj, seq){
	for(i = 0 ;i < obj.length;i++){
		if(obj.options[i].selected == true){
			rqtstate_txt = obj.options[i].text;
			rqtstate = obj.options[i].value;
			break;
		}
	}
	if(confirm("["+rqtstate_txt+ "] 상태로 변경하시겠습니까?")){
		document.gongcmtFrm.seq.value= seq;
		document.gongcmtFrm.rqt_state.value= rqtstate;
		document.gongcmtFrm.mode.value= "modify";
		document.gongcmtFrm.submit();
	}
}

function deleteGonggu(seq){
	if(confirm("함께 신청한 게시글까지 삭제됨니다. \n\n삭제하시겠습니까?")){
		document.gongcmtFrm.seq.value= seq;
		document.gongcmtFrm.mode.value= "delete";
		document.gongcmtFrm.submit();
	}
}
function regGonggu(pcode,seq){
	document.gong_reg.cproductcode.value=pcode;
	document.gong_reg.gong_seq.value=seq;
	document.gong_reg.action="social_product_copy.php";
	document.gong_reg.target="copyPrdt";
	window.open("about:blank","copyPrdt","width=820,height=400,scrollbars=yes,status=no");
	document.gong_reg.submit();
}
function sendGonggu(seq){
	document.sendFrm.c_seq.value=seq;
	document.sendFrm.target="sendMail";
	window.open("about:blank","sendMail","width=620,height=500,scrollbars=yes,status=no");
	document.sendFrm.submit();
}

function viewList(seq){
	document.gongcmtFrm.seq.value=seq;
	document.gongcmtFrm.action="social_request_list.php";
	document.gongcmtFrm.submit();
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
					<TD><IMG SRC="images/social_request_stitle1.gif"  ALT="공동구매 요청 상품리스트"></TD>
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
				<form name="gongcmtFrm" method="post" action="<?=$_SERVER[PHP_SELF]?>">
				<input type=hidden name=mode value="">
				<input type=hidden name=seq value="">
				<input type=hidden name=rqt_state value="">
				<input type=hidden name=block value="<?=$block?>">
				<input type=hidden name=gotopage value="<?=$gotopage?>">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0 style="table-layout:fixed">
				<col width=45></col>
				<col width=50></col>
				<col width=230></col>
				<col width=60></col>
				<col width=80></col>
				<col width=70></col>
				<col width=45></col>
				<col width=150></col>
				<TR>
					<TD colspan=8 background="images/table_top_line.gif"></TD>
				</TR>
				<TR align=center>
					<TD class="table_cell">번호</TD>
					<TD class="table_cell1" colspan="2">공동구매 신청 상품</TD>
					<TD class="table_cell1">제안인<br>(신청수)</TD>
					<TD class="table_cell1">신청일자</TD>
					<TD class="table_cell1">상태</TD>
					<TD class="table_cell1">삭제</TD>
					<TD class="table_cell1">기타</TD>
				</TR>
				<TR>
					<TD colspan="8" background="images/table_con_line.gif"></TD>
				</TR>
<?
$cnt=0;
while($row=mysql_fetch_object($result)) {
	$number = ($t_count-($setup[list_num] * ($gotopage-1))-$cnt);
	$icon="";
	$artype = explode(",",$row->sns_type);
	for($i=0;$i<sizeof($artype)-1;$i++){
		$icon .= "<img src=\"../img/cmn/icon_".$arIconImage[$artype[$i]]."_on.gif\" align=\"absmiddle\" WIDTH=\"17\" HEIGHT=\"17\"> ";
	}
	$id = $row->id;
	$comment = $row->comment;
	$cmt_count = $row->count;
	$sns_date = date("Y-m-d H:i:s", $row->regidate);
	$mem_id = ($_ShopInfo->getMemid() == $row->id)? "":$row->id;

	$pGbn = 0;
	if(substr($row->pcode,0,3) == "999"){
		$pGbn= 1;
	}
	if($row->social_chk =="Y"){
		$pGbn= 3;
	}

	$sPdtThumb ="";
	if(strlen($pcode)==0){
		if(strlen($row->tinyimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)) {
			$width=GetImageSize($Dir.DataDir."shopimages/product/".$row->tinyimage);
			if($width[0]>=50) $width[0]=50;
			else if (strlen($width[0])==0) $width[0]=50;
			$sPdtThumb .= "<img src=\"".$Dir.DataDir."shopimages/product/".$row->tinyimage."\" border=\"0\" width=\"".$width[0]."\" class=\"img\">";
		} else {
			$sPdtThumb .= "<img src=\"".$Dir."images/no_img.gif\" border=\"0\" WIDTH=50  class=\"img\">";
		}
	}
?>
				<TR align=center>
					<TD class="td_con"><?=$number?></TD>
					<TD class="td_con1"><?=$sPdtThumb?></TD>
					<TD class="td_con1" align="left"><a href="JavaScript:ProductInfo('<?=substr($row->pcode,0,12)?>','<?=$row->pcode?>','YES','<?=$pGbn?>')"><?=$row->productname?></a></TD>
					<TD class="td_con1"><a href="javascript:viewList('<?=$row->seq?>')"><?=$id?><br>(<?=$cmt_count?>)</a></TD>
					<TD class="td_con1"><?=$sns_date?></TD>
					<TD class="td_con1">
					 <select name="rqt_state<?=$row->seq?>" onchange="modifyState(this,'<?=$row->seq?>')" class="input">
					 <option value="1" <?=($row->rqt_state ==1)?"selected":""?>>신청중</option>
					 <option value="2" <?=($row->rqt_state ==2)?"selected":""?>>미진행</option>
					 <option value="3" <?=($row->rqt_state ==3)?"selected":""?>>진행</option>
					 <option value="4" <?=($row->rqt_state ==4)?"selected":""?>>완료</option>
					 </select>
					</TD>
					<TD class="td_con1">
					 <input type="button" value="삭제" class="btnstyle" onclick="deleteGonggu('<?=$row->seq?>');">
					</TD>
					<TD class="td_con1">
					<?if($row->reg_prdt=="N"){?><input type="button" value="공구등록" class="btnstyle"  onclick="regGonggu('<?=$row->pcode?>', '<?=$row->seq?>')"><?}else{?><a href="JavaScript:ProductInfo('<?=substr($row->reg_prdt,0,12)?>','<?=$row->reg_prdt?>','YES','3')"><span class="btnstyle2">등록완료</span></a><?}?>
					<input type="button" value="공구알림" class="btnstyle"  onclick="sendGonggu('<?=$row->seq?>')">
					</TD>
				</TR>
				<TR>
					<TD colspan="8" background="images/table_con_line.gif"></TD>
				</TR>
<?
	$cnt++;
}
?>
				</TABLE>
				</form>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td align=center class="font_size">
<?
					$total_block = intval($pagecount / $setup[page_num]);

					if (($pagecount % $setup[page_num]) > 0) {
						$total_block = $total_block + 1;
					}

					$total_block = $total_block - 1;

					if (ceil($t_count/$setup[list_num]) > 0) {
						// 이전	x개 출력하는 부분-시작
						$a_first_block = "";
						if ($nowblock > 0) {
							$a_first_block .= "<a href='javascript:GoPage(0,1);' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='첫 페이지';return true\"><IMG src=\"images/icon_first.gif\" border=0 align=\"absmiddle\"></a>&nbsp;&nbsp;";

							$prev_page_exists = true;
						}

						$a_prev_page = "";
						if ($nowblock > 0) {
							$a_prev_page .= "<a href='javascript:GoPage(".($nowblock-1).",".($setup[page_num]*($block-1)+$setup[page_num]).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='이전 ".$setup[page_num]." 페이지';return true\">[prev]</a>&nbsp;&nbsp;";

							$a_prev_page = $a_first_block.$a_prev_page;
						}

						// 일반 블럭에서의 페이지 표시부분-시작

						if (intval($total_block) <> intval($nowblock)) {
							$print_page = "";
							for ($gopage = 1; $gopage <= $setup[page_num]; $gopage++) {
								if ((intval($nowblock*$setup[page_num]) + $gopage) == intval($gotopage)) {
									$print_page .= "<span class=font_orange2><B>[".(intval($nowblock*$setup[page_num]) + $gopage)."]</B></span> ";
								} else {
									$print_page .= "<a href='javascript:GoPage(".$nowblock.",".(intval($nowblock*$setup[page_num]) + $gopage).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$setup[page_num]) + $gopage)."';return true\">[".(intval($nowblock*$setup[page_num]) + $gopage)."]</a> ";
								}
							}
						} else {
							if (($pagecount % $setup[page_num]) == 0) {
								$lastpage = $setup[page_num];
							} else {
								$lastpage = $pagecount % $setup[page_num];
							}

							for ($gopage = 1; $gopage <= $lastpage; $gopage++) {
								if (intval($nowblock*$setup[page_num]) + $gopage == intval($gotopage)) {
									$print_page .= "<span class=font_orange2><B>[".(intval($nowblock*$setup[page_num]) + $gopage)."]</B></span> ";
								} else {
									$print_page .= "<a href='javascript:GoPage(".$nowblock.",".(intval($nowblock*$setup[page_num]) + $gopage).");' onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$setup[page_num]) + $gopage)."';return true\">[".(intval($nowblock*$setup[page_num]) + $gopage)."]</a> ";
								}
							}
						}		// 마지막 블럭에서의 표시부분-끝


						$a_last_block = "";
						if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
							$last_block = ceil($t_count/($setup[list_num]*$setup[page_num])) - 1;
							$last_gotopage = ceil($t_count/$setup[list_num]);

							$a_last_block .= "&nbsp;&nbsp;<a href='javascript:GoPage(".$last_block.",".$last_gotopage.");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='마지막 페이지';return true\"><IMG src=\"images/icon_last.gif\" border=0 align=\"absmiddle\" width=\"17\" height=\"14\"></a>";

							$next_page_exists = true;
						}

						// 다음 10개 처리부분...

						$a_next_page = "";
						if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
							$a_next_page .= "&nbsp;&nbsp;<a href='javascript:GoPage(".($nowblock+1).",".($setup[page_num]*($nowblock+1)+1).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='다음 ".$setup[page_num]." 페이지';return true\">[next]</a>";

							$a_next_page = $a_next_page.$a_last_block;
						}
					} else {
						$print_page = "<B>[1]</B>";
					}
?>
					<?=$a_div_prev_page.$a_prev_page.$print_page.$a_next_page.$a_div_next_page?>
					</td>
				</tr>
				</table>
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
					<TD COLSPAN=3 width="100%" valign="top" class=menual_bg style="padding-top:8pt; padding-bottom:8pt; padding-left:4pt;">
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

			<form name=form_reg action="product_register.php" method=post>
			<input type=hidden name=code>
			<input type=hidden name=prcode>
			<input type=hidden name=popup>
			</form>

			<form name=gong_reg action="social_product_copy.php" method=post>
			<input type=hidden name=cproductcode>
			<input type=hidden name=gong_seq>
			</form>

			<form name=sendFrm action="social_product_send.php" method=post>
			<input type=hidden name=c_seq>
			</form>

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