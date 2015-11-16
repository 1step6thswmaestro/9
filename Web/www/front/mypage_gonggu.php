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

$arIconImage = array("t"=>"twitter","f"=>"facebook","m"=>"me2day");
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
<TITLE><?=$_data->shoptitle?> - 마이페이지> 공동구매신청 > 내가 제안한 상품</TITLE>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META http-equiv="X-UA-Compatible" content="IE=5" />
<META name="description" content="<?=(strlen($_data->shopdescription)>0?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<script type="text/javascript" src="<?=$Dir?>lib/jquery-1.4.2.min.js"></script>
<?include($Dir."lib/style.php")?>
<SCRIPT LANGUAGE="JavaScript">
<!--
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
			<?if($_data->recom_url_ok == "Y" || $_data->sns_ok == "Y"){?><TD><A HREF="<?=$Dir.FrontDir?>mypage_promote.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin<?=$designMypage?>_menu10.gif" BORDER="0"></A></TD><? } ?>
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
								<td><a href="../front/mypage_gonggu.php"><img src="../images/design/grouppurchase_tap01r.gif" width="148" height="31" border="0"></a></td>
								<td><a href="../front/mypage_gonggu1.php"><img src="../images/design/grouppurchase_tap02.gif" width="148" height="31" border="0"></a></td>
								<td><a href="../front/mypage_gonggu2.php"><img src="../images/design/grouppurchase_tap03.gif" width="147" height="31" border="0"></a></td>
								<td><a href="../front/mypage_gonggu3.php"><img src="../images/design/grouppurchase_tap04.gif" width="147" height="31" border="0"></a></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td height="2" bgcolor="#333333"></td>
				</tr>
				<tr>
					<td height="10"></td>
				</tr>
				<tr>
					<td align="center">
						<table cellpadding="0" cellspacing="0" width="97%">

<?
$sql = "SELECT COUNT(*) as t_count FROM tblsnsGongguCmt  A inner join tblproduct B on A.pcode=B.productcode WHERE A.c_order=1 AND A.id='".$_ShopInfo->getMemid()."' ";
$result = mysql_query($sql,get_db_conn());

$row = mysql_fetch_object($result);
$t_count = $row->t_count;
mysql_free_result($result);
$pagecount = (($t_count - 1) / $list_num) + 1;

$sql = "SELECT A.*,B.productname, B.consumerprice, B.sellprice, B.tinyimage, B.etctype, B.selfcode, B.option_price ";
$sql .="FROM tblsnsGongguCmt A inner join tblproduct B on A.pcode=B.productcode ";
$sql .="WHERE A.c_order=1 AND A.id='".$_ShopInfo->getMemid()."' ";
$sql .="ORDER BY A.regidate DESC ";
$sql .= "LIMIT " . ($list_num * ($gotopage - 1)) . ", " . $list_num;
$result=mysql_query($sql,get_db_conn());
while($row=mysql_fetch_object($result)) {
	$icon="";
	$artype = explode(",",$row->sns_type);
	for($i=0;$i<sizeof($artype)-1;$i++){
		$icon .= "<img src=\"../images/design/icon_".$arIconImage[$artype[$i]]."_on.gif\" align=\"absmiddle\" WIDTH=\"17\" HEIGHT=\"17\"> ";
	}
	$id = $row->id;
	$comment = str_replace("\n","<br>",$row->comment);
	$cmt_count = $row->count;
	$sns_date = date("Y-m-d H:i:s", $row->regidate);
	$profile_img = $row->profile_img;
	if(strlen($profile_img) == 0){
		$profile_img="/images/common/sns_default.jpg";
	}
	$cmtsubList ="";
	$cmtState ="";
	if($cmt_count>1){
		$cmtsubList ="<IMG SRC=\"../images/design/gonggu_order_btn03.gif\" WIDTH=101 HEIGHT=22 ALT=\"\" class=\"cmtlistBtn\" style=\"cursor:pointer;margin-left:7px;\"><span style=\"display:none;\">".$row->seq."</span>";
	}
	$mem_id = ($_ShopInfo->getMemid() == $row->id)? "":$row->id;

	switch($row->rqt_state){
		case "1":
			$cmtState = "<IMG SRC=\"../images/design/btn_progress.gif\" align=\"absmiddle\" height=\"19\" border=\"0\">";break;
		case "2":
			$cmtState = "<IMG SRC=\"../images/design/btn_progress_n.gif\" align=\"absmiddle\" height=\"19\" border=\"0\">";break; //미진행
		case "3":
			$cmtState = "<IMG SRC=\"../images/design/btn_progress_y.gif\" align=\"absmiddle\" height=\"19\" border=\"0\">";break; //진행
		case "4":
			$cmtState = "<IMG SRC=\"../images/design/btn_progress_e.gif\" align=\"absmiddle\" height=\"19\" border=\"0\">";break; //종료
	}
	$sPdtThumb ="";
	if(strlen($pcode)==0){
		$sPdtThumb = "<a href=\"../front/productdetail.php?productcode=".$row->pcode."\">";
		if(strlen($row->tinyimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)) {
			$width=GetImageSize($Dir.DataDir."shopimages/product/".$row->tinyimage);
			if($width[0]>=94) $width[0]=94;
			else if (strlen($width[0])==0) $width[0]=94;
			$sPdtThumb .= "<img src=\"".$Dir.DataDir."shopimages/product/".$row->tinyimage."\" border=\"0\" width=\"".$width[0]."\" class=\"img\">";
		} else {
			$sPdtThumb .= "<img src=\"".$Dir."images/no_img.gif\" border=\"0\" WIDTH=94 HEIGHT=72 class=\"img\">";
		}
		$sPdtThumb .= "</a>\n";
	}
	$delBtn = "";
	if($cmt_count==1){
		$delBtn="<a href=\"javascript:;\" onclick=\"delGongguCmt('".$row->seq."')\"><IMG SRC=\"../images/design/btn_mdel.gif\" ALT=\"\" style=\"cursor:pointer;\" width=\"13\" height=\"11\"></a>";
	}
	$sellprice ="";
	if($dicker=dickerview($row->etctype,number_format($row->sellprice)."원",1)) {
		$sellprice = $dicker;
	} else if(strlen($_data->proption_price)==0) {
		$sellprice = number_format($row->sellprice)."원";
		if (strlen($row->option_price)!=0) $sellprice .= "(기본가)";
	} else {
		if (strlen($row->option_price)==0) $sellprice = number_format($row->sellprice)."원";
		else $sellprice = ereg_replace("\[PRICE\]",number_format($row->sellprice),$_data->proption_price);
	}

?>
							<tr>
								<td valign="top">
									<table cellpadding="0" cellspacing="0" width="100%" style="margin-bottom:10px;margin-top:10px">
									<tr>
										<td valign="top">
											<TABLE cellSpacing=0 cellPadding=0 width="330">
											<TR>
												<TD>
													<TABLE cellSpacing=0 cellPadding=0>
													<TR>
														<TD style="PADDING-RIGHT: 10px"><?=$icon ?></TD>
														<TD style="PADDING-RIGHT: 10px" class="gongguing_order_id"><?=$id ?></TD>
														<TD style="PADDING-RIGHT: 10px" class="gongguing_order_date"><?=$sns_date?></TD>
														<TD align="right"><?=$delBtn?></TD>
													</TR>
													</TABLE>
												</TD>
											</TR>
											<TR>
												<TD class="table_td" height="10"></TD>
											</TR>
											<TR>
												<TD class="table_td"><?=$comment?></TD>
											</TR>
											<TR>
												<TD class="table_td" height="40"></TD>
											</TR>
											<TR>
												<TD class="table_td">공동구매 희망 : <b><font color="#666666"><?=$cmt_count?></font></b>건<?=$cmtState?></TD>
											</TR>
											</TABLE>
										</td>
										<td width="25" valign="top"></td>
										<td valign="top">
											<TABLE cellSpacing=0 cellPadding=0 width="330">
											<TR>
												<TD vAlign=top width="94"><?=$sPdtThumb?></TD>
												<TD vAlign=top><IMG border=0 src="../images/design/space_line.gif" width=10 height=1></TD>
												<TD vAlign=top>
													<table cellpadding="0" cellspacing="0" width="100%">
														<tr>
															<td class="table_td"><?=viewproductname($row->productname,$row->etctype,$row->selfcode)?></td>
														</tr>
														<tr>
															<td height="10"></td>
														</tr>
														<tr>
															<td class="table_td"><img src="../images/design/gonggu_end_price.gif" align="absmiddle" width="34" height="17" border="0"><?=number_format($row->consumerprice)?>원</td>
														</tr>
														<tr>
															<td class="table_td"><img src="../images/design/icon_price.gif" align="absmiddle" width="34" height="17" border="0"><b><font color="#3455DE"><?=$sellprice?></font></b></td>
														</tr>
													</table>
												</TD>
											</TR>
											</TABLE>
										</td>
									</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td><img src="../images/design/con_line02.gif" width="100%" height="1" border="0" vspace="0"></td>
							</tr>
<?}
if($t_count == 0){
	echo "							<tr><td class=\"table_td\" align=\"center\" height=\"50\">등록한 내용이 없습니다.</td></tr><tr><td><img src=\"../images/design/con_line02.gif\" width=\"100%\" height=\"1\" border=\"0\" vspace=\"0\"></td></tr>\n";
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
			</table>

		</td>
	</tr>
	<tr>
		<td height="60"></td>
	</tr>
	</table>
<script type="text/javascript">
<!--
//리스트삭제
function delGongguCmt(seq){
	if(confirm("삭제하시겠습니까?")) {
		$j.post(
			"snsAction.php",
			{ method: "delGongguCmt", seq:seq},
			  function(data){
				if ( data.result == 'true' ) {
					alert("삭제되었습니다.");
					location.reload();
				}else
				{
					alert("이미 글이 등록 되어 삭제 할 수 없습니다.");
				}
			},
			"json"
		);
	}
}
//-->
</script>
<form name=form1 method=get action="<?=$_SERVER[PHP_SELF]?>">
<input type=hidden name=block value="<?=$block?>">
<input type=hidden name=gotopage value="<?=$gotopage?>">
</form>
<? include ($Dir."lib/bottom.php") ?>

</BODY>
</HTML>