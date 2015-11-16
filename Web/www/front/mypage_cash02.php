<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

if(strlen($_ShopInfo->getMemid())==0) {
	Header("Location:".$Dir.FrontDir."login.php?chUrl=".getUrl());
	exit;
}

if($_data->reserve_maxuse<0) {
	echo "<html><head><title></title></head><body onload=\"alert('본 쇼핑몰에서는 적립금 기능을 지원하지 않습니다.');location.href='".$Dir.FrontDir."mypage.php'\"></body></html>";exit;
}

$maxreserve=$_data->reserve_maxuse;

//리스트 세팅
$setup[page_num] = 10;
$setup[list_num] = 10;

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


$reserve=0;
$sql = "SELECT id,name,reserve FROM tblmember WHERE id='".$_ShopInfo->getMemid()."' ";
$result=mysql_query($sql,get_db_conn());
if($row=mysql_fetch_object($result)) {
	$coderow = $row;
	$id=$row->id;
	$name=$row->name;
	$reserve=$row->reserve;
} else {
	echo "<html><head><title></title></head><body onload=\"alert('회원정보가 존재하지 않습니다.');location.href='".$_SERVER[PHP_SELF]."?type=logout'\"></body></html>";exit;
}
mysql_free_result($result);


$cr_limit_arr = array("","일 1회","주 1회","월 1회");
if($_data->cr_edate) {
	$cr_date = $_data->cr_sdate."~".$_data->cr_edate;
}
else $cr_date = $_data->cr_sdate;


?>

<HTML>
<HEAD>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<TITLE><?=$_data->shoptitle?> - 전환신청 결과</TITLE>
<META name="description" content="<?=(strlen($_data->shopdescription)>0?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<?include($Dir."lib/style.php")?>
</HEAD>

<SCRIPT LANGUAGE="JavaScript">
<!--
function GoPage(block,gotopage) {
	document.form1.block.value=block;
	document.form1.gotopage.value=gotopage;
	document.form1.submit();
}
//-->
</SCRIPT>

<body<?=(substr($_data->layoutdata["MOUSEKEY"],0,1)=="Y"?" oncontextmenu=\"return false;\"":"")?><?=(substr($_data->layoutdata["MOUSEKEY"],1,1)=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=(substr($_data->layoutdata["MOUSEKEY"],2,1)=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>

<? include ($Dir.MainDir.$_data->menu_type.".php") ?>

<table border="0" cellpadding="0" cellspacing="0" width="100%">
<tr>
<td valign="top">
	<table border="0" cellpadding="0" cellspacing="0" width="100%">
<?
$leftmenu="Y";
if($_data->design_myreserve=="U") {
	$sql="SELECT body,leftmenu FROM ".$designnewpageTables." WHERE type='myreserve'";
	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		$body=$row->body;
		$body=str_replace("[DIR]",$Dir,$body);
		$leftmenu=$row->leftmenu;
		$newdesign="Y";
	}
	mysql_free_result($result);
}

if ($leftmenu!="N") {
	echo "<tr>\n";
	if ($_data->title_type=="Y" && file_exists($Dir.DataDir."design/myreserve_title.gif")) {
		echo "<td><img src=\"".$Dir.DataDir."design/myreserve_title.gif\" border=\"0\" alt=\"적립금 내역\"></td>\n";
	} else {
		echo "<td>\n";
		echo "<TABLE WIDTH=100% BORDER=0 CELLPADDING=0 CELLSPACING=0>\n";
		echo "<TR>\n";
		echo "	<TD><IMG SRC=".$Dir."images/".$_data->icon_type."/myreserve_title_head.gif ALT=></TD>\n";
		echo "	<TD width=100% valign=top background=".$Dir."images/".$_data->icon_type."/myreserve_title_bg.gif></TD>\n";
		echo "	<TD width=40><IMG SRC=".$Dir."images/".$_data->icon_type."/myreserve_title_tail.gif ALT=></TD>\n";
		echo "</TR>\n";
		echo "</TABLE>\n";
		echo "</td>\n";
	}
	echo "</tr>\n";
}
if($_data->design_mypage =="001")
	$designMypage = "3";
else if($_data->design_mypage =="002")
	$designMypage = "2";
else if($_data->design_mypage =="003")
	$designMypage = "1";
else
	$designMypage = "3";
?>
	<tr>
		<td style="padding:0px;padding-top:0px;">
			<table align="center" cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER="0" CELLPADDING="0" CELLSPACING="0">
				<TR>
					<TD><A HREF="<?=$Dir.FrontDir?>mypage.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin<?=$designMypage?>_menu1.gif" BORDER="0"></A></TD>
					<TD><A HREF="<?=$Dir.FrontDir?>mypage_orderlist.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin<?=$designMypage?>_menu2.gif" BORDER="0"></A></TD>
					<TD><A HREF="<?=$Dir.FrontDir?>mypage_personal.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin<?=$designMypage?>_menu3.gif" BORDER="0"></A></TD>
					<TD><A HREF="<?=$Dir.FrontDir?>wishlist.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin<?=$designMypage?>_menu4.gif" BORDER="0"></A></TD>
					<TD><A HREF="<?=$Dir.FrontDir?>mypage_reserve.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin<?=$designMypage?>_menu5r.gif" BORDER="0"></A></TD>
					<TD><A HREF="<?=$Dir.FrontDir?>mypage_coupon.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin<?=$designMypage?>_menu6.gif" BORDER="0"></A></TD>
					<?if($_data->recom_url_ok == "Y" || $_data->sns_ok == "Y"){?><TD><A HREF="<?=$Dir.FrontDir?>mypage_promote.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin<?=$designMypage?>_menu10.gif" BORDER="0"></A></TD><?}?>
					<TD><A HREF="<?=$Dir.FrontDir?>mypage_gonggu.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin<?=$designMypage?>_menu11.gif" BORDER="0"></A></TD>
					<? if(getVenderUsed()==true) { ?><TD><A HREF="<?=$Dir.FrontDir?>mypage_custsect.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin<?=$designMypage?>_menu9.gif" BORDER="0"></A></TD><? } ?>
					<TD><A HREF="<?=$Dir.FrontDir?>mypage_usermodify.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin<?=$designMypage?>_menu7.gif" BORDER="0"></A></TD>
					<TD><A HREF="<?=$Dir.FrontDir?>mypage_memberout.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin<?=$designMypage?>_menu8.gif" BORDER="0"></A></TD>
					<TD width="100%" background="<?=$Dir?>images/common/mypersonal_skin<?=$designMypage?>_menubg.gif"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height="20"></td>
			</tr>
			<tr>
				<td>
					<TABLE cellSpacing=0 cellPadding=0 width="100%">
					<TR>
						<TD><a href="../front/mypage_cash01.php"><IMG SRC="../images/design/promote3_cash_tap01.gif" WIDTH=107 HEIGHT=31 ALT="" border="0"></a><IMG SRC="../images/design/promote3_cash_tap02r.gif" WIDTH=117 HEIGHT=31 ALT="" border="0"></TD>
					</TR>
					<TR>
						<TD><img src="../images/design/con_line01.gif" width="100%" height="2" border="0"></TD>
					</TR>
					<TR>
						<TD>
							<TABLE cellSpacing=0 cellPadding=0 width="100%">
							<TR>
								<TD>
									<TABLE cellSpacing=0 cellPadding=0 width="100%">
									<COLGROUP>
									<COL width=50></COL>
									<COL width=130></COL>
									<COL width=100></COL>
									<COL></COL>
									<COL width=80></COL>
									<TR height=34 align="middle" bgColor=#f8f8f8>
										<TD class="mypage_text1">NO</TD>
										<TD class="mypage_text1">전환신청일</TD>
										<TD class="mypage_text1">전환금액</TD>
										<TD class="mypage_text1">계좌번호</TD>
										<TD class="mypage_text1">전환결과</TD>
									</TR>
									<TR>
										<TD bgColor="#e4e4e4" height=1 colSpan="5"></TD>
									</TR>
<?
		$status_arr = array('A'=>'전환신청','B'=>'접수완료','C'=>'입금완료');
		$sql = "SELECT COUNT(*) as t_count FROM tblcrinfo ";
		$sql.= "WHERE id='".$_ShopInfo->getMemid()."' ";
		$result = mysql_query($sql,get_db_conn());
		$row = mysql_fetch_object($result);
		$t_count = $row->t_count;
		mysql_free_result($result);
		$pagecount = (($t_count - 1) / $setup[list_num]) + 1;

		$sql = "SELECT * FROM tblcrinfo WHERE id='".$_ShopInfo->getMemid()."' ";
		$sql.= "ORDER BY uid DESC LIMIT " . ($setup[list_num] * ($gotopage - 1)) . ", " . $setup[list_num];
		$result=mysql_query($sql,get_db_conn());
		$cnt=0;
		while($row=mysql_fetch_array($result)) {
			$number = ($t_count-($setup[list_num] * ($gotopage-1))-$cnt);
			$date= date("Y-m-d(H:i)",$row['signdate']);
			$bank = "{$row['bank_name']} : {$row['bank_num']} (예금주 : {$row['name']})";
			$price = number_format($row['price']);
			$status = $status_arr[$row['status']];
?>
									<TR height="30" align="middle">
										<TD class="mypage_textcon3"><?=$number?></TD>
										<TD class="mypage_textcon3"><?=$date?></TD>
										<td class="mypage_textcon3a"><?=$price?>원</td>
										<td class="mypage_textcon3a"><?=$bank?></td>
										<td class="mypage_textcon3a"><?=$status?></td>
									</TR>
									<TR>
										<TD bgColor="#e4e4e4" height=1 colSpan="5"></TD>
									</TR>
<?
			$cnt++;
		}
		mysql_free_result($result);
		if ($cnt==0) {
			echo "									<TR height=\"30\"><TD colspan=\"5\" align=\"center\">해당내역이 없습니다.</TD></TR>\n";
			echo "									<TR><TD bgColor=\"#e4e4e4\" height=1 colSpan=\"5\"></TD></TR>\n";
		}
?>
									</TABLE>
								</TD>
							</TR>
							</TABLE>
						</TD>
					</TR>
					<tr>
						<td height="55" class="table01_con2" align="right">
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
				$a_first_block .= "<a href='javascript:GoPage(0,1);' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='첫 페이지';return true\"><img src=\"../images/design/btn_first.gif\" border=\"0\" hspace=\"0\"></a>&nbsp;&nbsp;";

				$prev_page_exists = true;
			}

			$a_prev_page = "";
			if ($nowblock > 0) {
				$a_prev_page .= "<a href='javascript:GoPage(".($nowblock-1).",".($setup[page_num]*($block-1)+$setup[page_num]).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='이전 ".$setup[page_num]." 페이지';return true\"><img src=\"../images/design/btn_pre.gif\" border=\"0\" hspace=\"3\"></a>&nbsp;&nbsp;";

				$a_prev_page = $a_first_block.$a_prev_page;
			}

			// 일반 블럭에서의 페이지 표시부분-시작
			if (intval($total_block) <> intval($nowblock)) {
				$print_page = "";
				for ($gopage = 1; $gopage <= $setup[page_num]; $gopage++) {
					if ((intval($nowblock*$setup[page_num]) + $gopage) == intval($gotopage)) {
						$print_page .= "<b><font color=\"#FF511B\">".(intval($nowblock*$setup[page_num]) + $gopage)."</font></b> ";
					} else {
						$print_page .= "<a href='javascript:GoPage(".$nowblock.",".(intval($nowblock*$setup[page_num]) + $gopage).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$setup[page_num]) + $gopage)."';return true\">".(intval($nowblock*$setup[page_num]) + $gopage)."</a> ";
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
						$print_page .= "<b><font color=\"#FF511B\">".(intval($nowblock*$setup[page_num]) + $gopage)."</font></b> ";
					} else {
						$print_page .= "<a href='javascript:GoPage(".$nowblock.",".(intval($nowblock*$setup[page_num]) + $gopage).");' onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$setup[page_num]) + $gopage)."';return true\">".(intval($nowblock*$setup[page_num]) + $gopage)."</a> ";
					}
				}
			}		// 마지막 블럭에서의 표시부분-끝

			$a_last_block = "";
			if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
				$last_block = ceil($t_count/($setup[list_num]*$setup[page_num])) - 1;
				$last_pageidx = ceil($t_count/$setup[list_num]);

				$a_last_block .= "&nbsp;&nbsp;<a href='javascript:GoPage(".$last_block.",".$last_pageidx.");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='마지막 페이지';return true\"><img src=\"../images/design/btn_end.gif\" border=\"0\" hspace=\"0\"></a>";

				$next_page_exists = true;
			}

			// 다음 10개 처리부분...

			$a_next_page = "";
			if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
				$a_next_page .= "&nbsp;&nbsp;<a href='javascript:GoPage(".($nowblock+1).",".($setup[page_num]*($nowblock+1)+1).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='다음 ".$setup[page_num]." 페이지';return true\"><img src=\"../images/design/btn_next.gif\" border=\"0\" hspace=\"3\"></a>";

				$a_next_page = $a_next_page.$a_last_block;
			}
		} else {
			$print_page = "<b><font color=\"#FF511B\">1</font></b>";
		}
		echo $a_div_prev_page.$a_prev_page.$print_page.$a_next_page.$a_div_next_page
?>
						</td>
					</tr>
					<tr>
						<td><img src="../images/design/con_line02.gif" width="100%" height="1" border="0"></td>
					</tr>
					</TABLE>
				</td>
			</tr>
			<tr>
				<td height="100"></td>
			</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td height="20"></td>
	</tr>
	</table>
</td>
</tr>
</table>
<form name=form1 method=post action="<?=$_SERVER[PHP_SELF]?>">
<input type=hidden name=block>
<input type=hidden name=gotopage>
</form>

<? include ($Dir."lib/bottom.php") ?>

</BODY>
</HTML>
