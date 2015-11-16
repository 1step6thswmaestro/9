<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

if(strlen($_ShopInfo->getMemid())==0) {
	Header("Location:".$Dir.FrontDir."login.php?chUrl=".getUrl());
	exit;
}

$sql = "SELECT * FROM tblmember WHERE id='".$_ShopInfo->getMemid()."' ";
$result=mysql_query($sql,get_db_conn());
if($row=mysql_fetch_object($result)) {
	$_mdata=$row;
	if($row->member_out=="Y") {
		$_ShopInfo->SetMemNULL();
		$_ShopInfo->Save();
		echo "<html><head><title></title></head><body onload=\"alert('회원 아이디가 존재하지 않습니다.');location.href='".$Dir.FrontDir."login.php';\"></body></html>";exit;
	}

	if($row->authidkey!=$_ShopInfo->getAuthidkey()) {
		$_ShopInfo->SetMemNULL();
		$_ShopInfo->Save();
		echo "<html><head><title></title></head><body onload=\"alert('처음부터 다시 시작하시기 바랍니다.');location.href='".$Dir.FrontDir."login.php';\"></body></html>";exit;
	}
}
mysql_free_result($result);

$gotopage = $_REQUEST["gotopage"];
$block=$_REQUEST["block"];

$pageSize = 10;
$list_num = 10;

if ($block != "") {
	$nowblock = $block;
	$curpage  = $block * $pageSize + $gotopage;
} else {
	$nowblock = 0;
}

if (($gotopage == "") || ($gotopage == 0)) {
	$gotopage = 1;
}
?>
<HTML>
<HEAD>
<TITLE><?=$_data->shoptitle?> - 마이페이지> 공동구매신청 > 진행상품 나의 참여내역</TITLE>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META http-equiv="X-UA-Compatible" content="IE=5" />
<META name="description" content="<?=(strlen($_data->shopdescription)>0?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<script type="text/javascript" src="<?=$Dir?>lib/jquery-1.4.2.min.js"></script>
<?include($Dir."lib/style.php")?>
<SCRIPT LANGUAGE="JavaScript">
<!--
function OrderDetailPop(ordercode) {
	document.detailform.ordercode.value=ordercode;
	window.open("about:blank","orderpop","width=610,height=500,scrollbars=yes");
	document.detailform.submit();
}
function GoPage(block,gotopage) {
	document.form1.block.value=block;
	document.form1.gotopage.value=gotopage;
	document.form1.submit();
}
//-->
</SCRIPT>
</HEAD>

<body<?=(substr($_data->layoutdata["MOUSEKEY"],0,1)=="Y"?" oncontextmenu=\"return false;\"":"")?><?=(substr($_data->layoutdata["MOUSEKEY"],1,1)=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=(substr($_data->layoutdata["MOUSEKEY"],2,1)=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>

<? include ($Dir.MainDir.$_data->menu_type.".php") ?>

	<table border="0" cellpadding="0" cellspacing="0" width="100%">
<?
$leftmenu="Y";
if($_data->design_mypage=="U") {
	$sql="SELECT body,leftmenu FROM ".$designnewpageTables." WHERE type='mygonggu'";
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
	if ($_data->title_type=="Y" && file_exists($Dir.DataDir."design/mygonggu_title.gif")) {
		echo "<td><img src=\"".$Dir.DataDir."design/mygonggu_title.gif\" border=\"0\" alt=\"마이페이지\"></td>\n";
	} else {
		echo "<td>\n";
		echo "<TABLE WIDTH=100% BORDER=0 CELLPADDING=0 CELLSPACING=0>\n";
		echo "<TR>\n";
		echo "	<TD><IMG SRC=".$Dir."images/".$_data->icon_type."/mygonggu_title_head.gif ALT=></TD>\n";
		echo "	<TD width=100% valign=top background=".$Dir."images/".$_data->icon_type."/mypage_title_bg.gif></TD>\n";
		echo "	<TD width=40><IMG SRC=".$Dir."images/".$_data->icon_type."/mypage_title_tail.gif ALT=></TD>\n";
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
	<td style="padding:5px;padding-top:0px;">
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td>
		<TABLE WIDTH="100%" BORDER="0" CELLPADDING="0" CELLSPACING="0">
		<TR>
			<TD><A HREF="<?=$Dir.FrontDir?>mypage.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin<?=$designMypage?>_menu1.gif" BORDER="0"></A></TD>
			<TD><A HREF="<?=$Dir.FrontDir?>mypage_orderlist.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin<?=$designMypage?>_menu2.gif" BORDER="0"></A></TD>
			<TD><A HREF="<?=$Dir.FrontDir?>mypage_personal.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin<?=$designMypage?>_menu3.gif" BORDER="0"></A></TD>
			<TD><A HREF="<?=$Dir.FrontDir?>wishlist.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin<?=$designMypage?>_menu4.gif" BORDER="0"></A></TD>
			<TD><A HREF="<?=$Dir.FrontDir?>mypage_reserve.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin<?=$designMypage?>_menu5.gif" BORDER="0"></A></TD>
			<TD><A HREF="<?=$Dir.FrontDir?>mypage_coupon.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin<?=$designMypage?>_menu6.gif" BORDER="0"></A></TD>
			<TD><A HREF="<?=$Dir.FrontDir?>mypage_promote.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin<?=$designMypage?>_menu10.gif" BORDER="0"></A></TD>
			<TD><A HREF="<?=$Dir.FrontDir?>mypage_gonggu.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin<?=$designMypage?>_menu11r.gif" BORDER="0"></A></TD>
			<? if(getVenderUsed()==true) { ?><TD><A HREF="<?=$Dir.FrontDir?>mypage_custsect.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin<?=$designMypage?>_menu9.gif" BORDER="0"></A></TD><? } ?>
			<TD><A HREF="<?=$Dir.FrontDir?>mypage_usermodify.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin<?=$designMypage?>_menu7.gif" BORDER="0"></A></TD>
			<TD><A HREF="<?=$Dir.FrontDir?>mypage_memberout.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin<?=$designMypage?>_menu8.gif" BORDER="0"></A></TD>
			<TD width="100%" background="<?=$Dir?>images/common/mypersonal_skin<?=$designMypage?>_menubg.gif"></TD>
		</TR>
		</TABLE>
		</td>
	</tr>
	</table>
	</td>
  </tr>
	<tr>
		<td>
			<table align="center" cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td height="20"></td>
				</tr>
				<tr>
					<td>
						<table cellpadding="0" cellspacing="0">
							<tr>
								<td><a href="../front/mypage_gonggu.php"><img src="../images/design/grouppurchase_tap01.gif" width="148" height="31" border="0"></a></td>
								<td><a href="../front/mypage_gonggu1.php"><img src="../images/design/grouppurchase_tap02.gif" width="148" height="31" border="0"></a></td>
								<td><a href="../front/mypage_gonggu2.php"><img src="../images/design/grouppurchase_tap03r.gif" width="147" height="31" border="0"></a></td>
								<td><a href="../front/mypage_gonggu3.php"><img src="../images/design/grouppurchase_tap04.gif" width="147" height="31" border="0"></a></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td>
						<table cellpadding="0" cellspacing="0" width="100%">
						<col width="50"></col>
						<col></col>
						<col></col>
						<col></col>
						<col></col>
						<col></col>
						<col></col>
						<col></col>
						<tr>
							<td height="2" colspan="8" bgcolor="#333333"></td>
						</tr>
						<tr align="center" height="34"  bgcolor="#F8F8F8">
							<td class="mypage_text1">NO</td>
							<td class="mypage_text1">상품명</td>
							<td class="mypage_text1">참여일자</td>
							<td class="mypage_text1">구매가격</td>
							<td class="mypage_text1">참여수</td>
							<td class="mypage_text1">마감시간</td>
							<td class="mypage_text1">수량</td>
							<td class="mypage_text1">상세정보</td>
						</tr>
						<tr>
							<td height="1" colspan="8" bgcolor="#E4E4E4"></td>
						</tr>
<?
$cnt=0;
$in_qry = "";
$today=time();
$sql = "SELECT productcode FROM tblproduct P, tblproduct_social S ";
$sql.= "WHERE P.productcode = S.pcode ";
$sql.= "AND display='Y' AND P.social_chk ='Y' ";
$sql.= "AND sell_startdate <= '".$today."' AND sell_enddate > '".$today."' AND (quantity is null OR quantity <> 0 ) ";
$sql.= "ORDER BY sell_enddate ASC ";
$result = mysql_query($sql,get_db_conn());
$arInpcode = array();
while($row=mysql_fetch_object($result)) {
	$arInpcode[]="'".$row->productcode."'";
}
$in_qry = implode(",",$arInpcode);
if($in_qry){
	$in_qry = " AND B.productcode in (".$in_qry.") ";

	$sql = "SELECT COUNT(*) as t_count ";
	$sql.= "FROM tblorderinfo A, tblorderproduct B, tblproduct C, tblproduct_social D ";
	$sql.= "WHERE id='".$_ShopInfo->getMemid()."' ";
	$sql.= "AND A.ordercode=B.ordercode ";
	$sql.= "AND B.productcode=C.productcode ";
	$sql.= "AND C.productcode=D.pcode ";
	$sql.= "AND (A.del_gbn='N' OR A.del_gbn='A') ";
	$sql.= "AND gift='3' ";
	$sql.=$in_qry;

	$result=mysql_query($sql,get_db_conn());
	$row=mysql_fetch_object($result);
	$t_count = (int)$row->t_count;
	mysql_free_result($result);
	$pagecount = (($t_count - 1) / $list_num) + 1;

	$sql = "SELECT A.ordercode, A.price, A.paymethod, A.pay_admin_proc, A.pay_flag, A.bank_date, A.deli_gbn, B.productcode ";
	$sql.= ",C.productname, C.quantity, D.sell_startdate, D.sell_enddate, D.sellcount_type, D.sellcount_add ";
	$sql.= "FROM tblorderinfo A, tblorderproduct B, tblproduct C, tblproduct_social D ";
	$sql.= "WHERE id='".$_ShopInfo->getMemid()."' ";
	$sql.= "AND A.ordercode=B.ordercode ";
	$sql.= "AND B.productcode=C.productcode ";
	$sql.= "AND C.productcode=D.pcode ";
	$sql.= "AND (A.del_gbn='N' OR A.del_gbn='A') ";
	$sql.= "AND gift='3' ";
	$sql.=$in_qry;
	$sql.= "ORDER BY A.ordercode DESC ";
	$sql.= "LIMIT " . ($list_num * ($gotopage - 1)) . ", " . $list_num;
	$result=mysql_query($sql,get_db_conn());

	while($row=mysql_fetch_object($result)) {
		$number = ($t_count-($list_num * ($gotopage-1))-$cnt);

		$sql = "SELECT IFNULL(sum(quantity), 0) as totcnt FROM tblorderproduct P, tblorderinfo O ";
		$sql.= "WHERE P.ordercode = O.ordercode ";
		$sql.= "AND O.deli_gbn IN ('S','Y','N','X') ";
		$sql.= "AND O.pay_admin_proc !='C' ";
		$sql.= "AND (del_gbn='N' OR del_gbn='A') ";
		$sql.= "AND P.productcode='".$row->productcode."' ";

		$result2=mysql_query($sql,get_db_conn());
		$row2=mysql_fetch_object($result2);
		$totcnt=$row2->totcnt;
		mysql_free_result($result2);
		switch($row->sellcount_type){
			case "B": $nowSailCnt = 0;break;
			case "C": $nowSailCnt = $row->sellcount_add;break;
			case "R": $nowSailCnt = intval($totcnt* $row->sellcount_add / 100);break;
			case "A":
				//if ($type!="complete") {
					//시간마다 +
					$limitTime = ($today > $row->sell_enddate)? $row->sell_enddate : $today ;
					$passTime = intval(($limitTime - $row->sell_startdate) / (60*60));
					$nowSailCnt = $row->sellcount_add * $passTime;
				//}
				break;
		}
		$nowSailCnt = $nowSailCnt + $totcnt;

		if($row->quantity =="")
			$stockCnt ="무제한";
		else if($row->quantity =="0")
			$stockCnt ="품절";
		else
			$stockCnt = $row->quantity;
?>
						<tr align="center" height="30">
							<td class="mypage_textcon3"><?=$number?></td>
							<td class="mypage_text1" align="left"><?=$row->productname?></td>
							<td class="mypage_textcon3"><?=substr($row->ordercode,0,4)."/".substr($row->ordercode,4,2)."/".substr($row->ordercode,6,2)."(".substr($row->ordercode,8,2).":".substr($row->ordercode,10,2).")"?></td>
							<td class="mypage_textcon3a"><?=number_format($row->price)?>원</td>
							<td class="mypage_textcon3a"><?=$nowSailCnt?>개</td>
							<td class="mypage_textcon3"><?=date("Y/m/d(H:i)", $row->sell_enddate) ?></td>
							<td class="mypage_textcon3"><?=$stockCnt?></td>
							<td class="mypage_textcon3"><a href="javascript:OrderDetailPop('<?=$row->ordercode?>')" ><img src="../images/design/btn_detail.gif"></a></td>
						</tr>
						<tr>
							<td height="1" colspan="8" bgcolor="#E4E4E4"></td>
						</tr>
<?
		$cnt++;
	}
	mysql_free_result($result);
}
if($cnt == 0){
	echo "						<tr><td height=\"30\" colspan=\"8\" align=\"center\">참여하신 내역이 없습니다.</td></tr><tr><td height=\"1\" colspan=\"8\" bgcolor=\"#E4E4E4\"></td></tr>";
}
?>
						</table>
					</td>
				</tr>
				<tr>
					<td class="table01_con2" align="center" height="55">
<?
		$total_block = intval($pagecount / $pageSize);

		if (($pagecount % $pageSize) > 0) {
			$total_block = $total_block + 1;
		}

		$total_block = $total_block - 1;

		if (ceil($t_count/$list_num ) > 0) {
			// 이전	x개 출력하는 부분-시작
			$a_first_block = "";
			if ($nowblock > 0) {
				$a_first_block .= "<a href='javascript:GoPage(0,1);' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='첫 페이지';return true\"><img src=\"../images/design/btn_first.gif\" height=\"22\" border=\"0\" align=\"absmiddle\"></a>&nbsp;&nbsp;";

				$prev_page_exists = true;
			}

			$a_prev_page = "";
			if ($nowblock > 0) {
				$a_prev_page .= "<a href='javascript:GoPage(".($nowblock-1).",".($pageSize*($block-1)+$pageSize).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='이전 ".$pageSize." 페이지';return true\"><img src=\"../images/design/btn_pre.gif\" height=\"22\" border=\"0\" align=\"absmiddle\"></a>&nbsp;&nbsp;";

				$a_prev_page = $a_first_block.$a_prev_page;
			}

			// 일반 블럭에서의 페이지 표시부분-시작

			if (intval($total_block) <> intval($nowblock)) {
				$print_page = "";
				for ($gopage = 1; $gopage <= $pageSize; $gopage++) {
					if ((intval($nowblock*$pageSize) + $gopage) == intval($gotopage)) {
						$print_page .= "<b><font color=\"#FF511B\">".(intval($nowblock*$pageSize) + $gopage)."</font></b> ";
					} else {
						$print_page .= "<a href='javascript:GoPage(".$nowblock.",".(intval($nowblock*$pageSize) + $gopage).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$pageSize) + $gopage)."';return true\">".(intval($nowblock*$pageSize) + $gopage)."</a> ";
					}
				}
			} else {
				if (($pagecount % $pageSize) == 0) {
					$lastpage = $pageSize;
				} else {
					$lastpage = $pagecount % $pageSize;
				}

				for ($gopage = 1; $gopage <= $lastpage; $gopage++) {
					if (intval($nowblock*$pageSize) + $gopage == intval($gotopage)) {
						$print_page .= "<b><font color=\"#FF511B\">".(intval($nowblock*$pageSize) + $gopage)."</font></b> ";
					} else {
						$print_page .= "<a href='javascript:GoPage(".$nowblock.",".(intval($nowblock*$pageSize) + $gopage).");' onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$pageSize) + $gopage)."';return true\">".(intval($nowblock*$pageSize) + $gopage)."</a> ";
					}
				}
			}		// 마지막 블럭에서의 표시부분-끝


			$a_last_block = "";
			if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
				$last_block = ceil($t_count/($list_num *$pageSize)) - 1;
				$last_gotopage = ceil($t_count/$list_num );

				$a_last_block .= "&nbsp;&nbsp;<a href='javascript:GoPage(".$last_block.",".$last_gotopage.");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='마지막 페이지';return true\"><img src=\"../images/design/btn_end.gif\" height=\"22\" border=\"0\" align=\"absmiddle\"></a>";

				$next_page_exists = true;
			}

			// 다음 10개 처리부분...

			$a_next_page = "";
			if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
				$a_next_page .= "&nbsp;&nbsp;<a href='javascript:GoPage(".($nowblock+1).",".($pageSize*($nowblock+1)+1).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='다음 ".$pageSize." 페이지';return true\"><img src=\"../images/design/btn_next.gif\" height=\"22\" border=\"0\" align=\"absmiddle\"></a>";

				$a_next_page = $a_next_page.$a_last_block;
			}
		} else {
			$print_page = "<font color=\"#FF511B\">1</font>";
		}
		echo $a_div_prev_page.$a_prev_page.$print_page.$a_next_page.$a_div_next_page
?>
					</td>
				</tr>
                <tr>
					<td><img src="../images/design/con_line02.gif" width="100%" height="1" border="0"></td>
                </tr>
				<tr>
					<td height="100"></td>
				</tr>
			</table>

		</td>
	</tr>
	<tr>
		<td height="60"></td>
	</tr>
	</table>
<form name=form1 method=get action="<?=$_SERVER[PHP_SELF]?>">
<input type=hidden name=block value="<?=$block?>">
<input type=hidden name=gotopage value="<?=$gotopage?>">
</form>
<form name=detailform method=post action="<?=$Dir.FrontDir?>orderdetailpop.php" target="orderpop">
<input type=hidden name=ordercode>
</form>
<? include ($Dir."lib/bottom.php") ?>

</BODY>
</HTML>