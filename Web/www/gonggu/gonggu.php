<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$gongguimagepath=$Dir.DataDir."shopimages/gonggu/";


$gong_num=(int)$_data->gong_num;
if($gong_num==0) $gong_num=10;

$sort=$_REQUEST["sort"];
if(strlen($sort)==0) $sort=0;

$type=$_REQUEST["type"];
if(strlen($type)==0 || $type!="complete") $type="";

$tname0="gong_menu1.gif";
$tname1="gong_menu2.gif";
if($type=="complete") {
	$tname1="gong_menu2r.gif";
} else {
	$tname0="gong_menu1r.gif";
}

?>
<HTML>
<HEAD>
<TITLE><?=$_data->shoptitle?> - 공동구매</TITLE>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META name="description" content="<?=(strlen($_data->shopdescription)>0?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<?include($Dir."lib/style.php")?>
<SCRIPT LANGUAGE="JavaScript">
<!--
function OpenImage(image) {
	window.open("image_view.php?image="+image,"image_view","resizable=yes,scrollbars=yes,x=100,y=200,width=370,height=250");
}
//-->
</SCRIPT>
</HEAD>

<body<?=(substr($_data->layoutdata["MOUSEKEY"],0,1)=="Y"?" oncontextmenu=\"return false;\"":"")?><?=(substr($_data->layoutdata["MOUSEKEY"],1,1)=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=(substr($_data->layoutdata["MOUSEKEY"],2,1)=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>

<? include ($Dir.MainDir.$_data->menu_type.".php") ?>

<table border="0" cellpadding="0" cellspacing="0" width="100%">
<tr>
<?
if ($_data->title_type=="Y") {
	echo "<td><img src=\"".$Dir.DataDir."design/gonggu_title.gif\" border=\"0\" alt=\"공동구매\"></td>\n";
} else {
	echo "<td>\n";
	echo "<TABLE WIDTH=100% BORDER=0 CELLPADDING=0 CELLSPACING=0>\n";
	echo "<TR>\n";
	echo "	<TD><IMG SRC=".$Dir."images/".$_data->icon_type."/gonggu_title_head.gif ALT=></TD>\n";
	echo "	<TD width=100% valign=top background=".$Dir."images/".$_data->icon_type."/gonggu_title_bg.gif></TD>\n";
	echo "	<TD width=40><IMG SRC=".$Dir."images/".$_data->icon_type."/gonggu_title_tail.gif ALT=></TD>\n";
	echo "</TR>\n";
	echo "</TABLE>\n";
	echo "</td>\n";
}
?>
</tr>
<tr>
	<td style="padding-left:10px;padding-right:10px;">
	<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
	<tr>
		<td align="right">
		<table cellpadding="0" cellspacing="0">
		<tr>
			<td><a href="gonggu.php"><img src="images/<?=$tname0?>" border="0"></a></td>
			<td><a href="gonggu.php?type=complete"><img src="images/<?=$tname1?>" border="0"></a></td>
			<td><a href="mygonggu.php"><img src="images/gong_menu3.gif" border="0"></a></td>
			<td><a href="mygonggu.php?type=complete"><img src="images/gong_menu4.gif" border="0"></a></td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td height="2" bgcolor="#000000"></td>
	</tr>
	<tr>
		<td height="20"></td>
	</tr>
	<tr>
		<td style="padding-top:3pt;" align="right"><FONT color="#000000">정렬방식 : </FONT><select name=sort onChange="location.href='<?=$PHP_SELF?>?type=<?=$type?>&sort='+this.options[this.selectedIndex].value;" style="font-size:11px;letter-spacing:-0.5pt;background-color:#404040;">
		<?if($type!="complete"){?>
		<option value="0" <?if($sort=="0")echo"selected";?> style="color:#ffffff;">마감 임박순</option>
		<?} else {?>
		<option value="0" <?if($sort=="0")echo"selected";?> style="color:#ffffff;">마감일순</option>
		<?}?>
		<option value="1" <?if($sort=="1")echo"selected";?> style="color:#ffffff;">마감일 역순</option>
		<option value="2" <?if($sort=="2")echo"selected";?> style="color:#ffffff;">구매신청순</option>
		</SELECT></td>
	</tr>
	<tr>
		<td height="2"></td>
	</tr>
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%" style="gable-layout:fixed">
		<tr>
			<td>
			<table cellpadding="0" cellspacing="0" width="100%">
<?
	//리스트 세팅
	$setup[page_num] = 10;
	$setup[list_num] = $gong_num;

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

	$today=date("YmdHis");
	$sql = "SELECT COUNT(*) as t_count FROM tblgonginfo ";
	if ($type=="complete") $sql.= "WHERE end_date < '".$today."' ";
	else $sql.= "WHERE start_date <= '".$today."' AND end_date > '".$today."' ";
	$result = mysql_query($sql,get_db_conn());
	$row = mysql_fetch_object($result);
	$t_count = $row->t_count;
	mysql_free_result($result);
	$pagecount = (($t_count - 1) / $setup[list_num]) + 1;

	$sql = "SELECT * FROM tblgonginfo ";
	if ($type=="complete") $sql.= "WHERE end_date < '".$today."' ";
	else $sql.= "WHERE start_date <= '".$today."' AND end_date > '".$today."' ";
	if($sort=="0") $sql.= "ORDER BY end_date ASC ";
	else if($sort=="1") $sql.= "ORDER BY end_date DESC ";
	else if($sort=="2") $sql.= "ORDER BY bid_cnt DESC ";
	$sql.= "LIMIT " . ($setup[list_num] * ($gotopage - 1)) . ", " . $setup[list_num];
	$result = mysql_query($sql,get_db_conn());
	if($type!="complete"){
		$complete_tag_img = "<IMG src=\"images/ic_clockprogress.gif\" border=\"0\" align=absMiddle>";
	} else {
		$complete_tag_img = "<img src=\"images/ic_clockprogress1.gif\" border=\"0\" align=\"absmiddle\">";
	}
	$i=0;
	while($row=mysql_fetch_object($result)) {
		if($i==0) echo "<tr>";
		else if ($i%2==0) echo "</tr><tr><td colspan=\"3\" height=\"20\"></td></tr><tr>\n";
		else if ($i%2!=0) echo "<td width=\"6%\"></td>\n";
		$num=intval($row->bid_cnt/$row->count);
		$price=$row->start_price-($num*$row->down_price);
		if($price<$row->mini_price) $price=$row->mini_price;

		$end_time=mktime((substr($row->end_date,8,2)*1),(substr($row->end_date,10,2)*1),0,(substr($row->end_date,4,2)*1),(substr($row->end_date,6,2)*1),(substr($row->end_date,0,4)*1));

		$i++;
?>
			<td width="47%">
			<TABLE WIDTH="100%" BORDER="0" CELLPADDING="0" CELLSPACING="0">
			<TR>
				<TD height="27" background="images/gonggu_table01.gif" style="padding-left:11pt;padding-left:4px"><div style="padding-left:15px;white-space:nowrap;width:270px;overflow:hidden;text-overflow:ellipsis;"><a href="gonggu_detail.php?seq=<?=$row->gong_seq?>"><b><?=$row->gong_name?></b></a></div></TD>
			</TR>
			<TR>
				<TD background="images/gonggu_table01bg.gif" style="padding:5px;">
				<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
				<tr>
					<td valign="top" width="36%">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td valign="top">
<?
				if(strlen($row->image1)>0 && file_exists($gongguimagepath.$row->image1)) {
					echo "<a href=\"javascript:OpenImage('".$row->image1."');\"><img src=\"images/icon_zoom.gif\" border=\"0\"></a>";
				}
?>
						</td>
						<td align="center" width="100%">
<?
				if(strlen($row->image3)>0 && file_exists($gongguimagepath.$row->image3)) {
					echo "<a href=\"gonggu_detail.php?seq=".$row->gong_seq."\"><img src=\"".$gongguimagepath.$row->image3."\" border=\"0\" ";
					$size=GetImageSize($gongguimagepath.$row->image3);
					if(($size[0]>90 || $size[1]>90) && $size[0]>$size[1]) {
						echo " width=\"90\"";
					} else if($size[0]>90 || $size[1]>90) {
						echo " height=\"90\"";
					}
					echo "></a></td>";
				} else {
					echo "<a href=\"gonggu_detail.php?seq=".$row->gong_seq."\"><img src=\"images/product_no_img.gif\" width=\"90\" height=\"90\" border=\"0\"></a></td>";
				}
?>
					</tr>
					<tr>
						<td></td>
						<td align="center"><a href="gonggu_detail.php?seq=<?=$row->gong_seq?>"><IMG SRC="images/design_plist_gong_view.gif" border="0" vspace="5"></a></td>
					</tr>
					</table>
					</td>
					<td width="2%"></td>
					<td valign="top" width="62%">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td style="font-size:11px;" height="16"><IMG SRC="images/nero.gif" border="0" align="absmiddle"> 시중가 : <s><?=number_format($row->origin_price)?>원</s></td>
					</tr>
					<tr>
						<td height="16"><IMG SRC="images/nero.gif" border="0" align="absmiddle"> 현재가 : <font color="#F02800" style="font-size:11px;letter-spacing:-0.5pt;"><b><?=number_format($price)?>원</b></font></td>
					</tr>
					<tr>
						<td style="font-size:11px;" height="16"><IMG SRC="images/nero.gif" border="0" align="absmiddle"> 총신청수량 : <?=$row->quantity?>개</td>
					</tr>
					<tr>
						<td style="font-size:11px;"><IMG SRC="images/nero.gif" border="0" align="absmiddle"> 총판매수량 : <?=$row->bid_cnt?>개</td>
					</tr>
					<tr>
						<td style="font-size:11px;" height="20"><?=$complete_tag_img?> <span class="text_blue"><b><?=date("Y/m/d H:i",$end_time)?></b></span></td>
					</tr>
					<tr>
						<td>
						<table cellpadding="0" cellspacing="0" width="230" height="52" bordercolordark="black" bordercolorlight="black">
						<tr>
							<td background="images/gong_graph1.gif">
							<TABLE height="52" cellSpacing="0" cellPadding="0" border="0" style="table-layout:fixed">
							<col width="60"></col>
							<col width="60"></col>
							<col width="60"></col>
							<tr>
								<td>
								<table width="100%" height="52" cellpadding="0" cellspacing="0" border="0">
								<tr>
									<td align="center"><font color="#696969" style="font-size:11px;"><?=number_format($row->start_price)?>원</font></td>
								</tr>
								<tr>
									<td height="24"></td>
								</tr>
								</table>
								</td>
								<td>
								<table width="100%" height="52" cellpadding="0" cellspacing="0" border="0">
								<tr>
									<td height="4"></td>
								</tr>
								<tr>
									<td align="center"><font color="#696969" style="font-size:11px;"><?=number_format($price)?>원</font></td>
								</tr>
								</table>
								</td>
								<td>
								<table width="100%" height="52" cellpadding="0" cellspacing="0" border="0">
								<tr>
									<td height="26"></td>
								</tr>
								<tr>
									<td align="center"><font color="#696969" style="font-size:11px;"><?=number_format($row->mini_price)?>원</font></td>
								</tr>
								</table>
								</td>
							</tr>
							</TABLE>
							</td>
						</tr>
						<tr>
							<td>
							<table border="0" cellpadding="0" cellspacing="0">
							<tr align="center" style="letter-spacing:-0.5pt;">
								<td width="60">시작가</td>
								<td width="60">공구가</td>
								<td width="60">최저가</td>
							</tr>
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
				</TD>
			</TR>
			<TR>
				<TD><IMG SRC="images/gonggu_table01down.gif" border="0"></TD>
			</TR>
			</TABLE>
			</td>
<?
	}
	mysql_free_result($result);
	if ($i>0 && $i%2==0) {
		echo "</tr>";
	} else if($i>0 && $i%2==1) {
		echo "<td width=\"6%\"></td><td width=\"47%\"></td></tr>";
	}
	if($i==0) {
		if($type=="complete") $msg="마감된 공동구매가 없습니다.";
		else $msg="진행중인 공동구매가 없습니다.";
		echo "<tr height=\"30\"><td colspan=\"3\" align=\"center\">".$msg."</td></tr>\n";
	}
	echo "<tr><td colspan=\"3\" height=\"5\" bgcolor=\"#FFFFFF\"></td></tr>";
?>
			</table>
			</td>
		</tr>
		<tr>
			<td height="5"></td>
		</tr>
		<tr>
			<td height="1" bgcolor="#DDDDDD"></td>
		</tr>
		<tr>
			<td height="20"></td>
		</tr>
<?
	$total_block = intval($pagecount / $setup[page_num]);
	if (($pagecount % $setup[page_num]) > 0) {
		$total_block = $total_block + 1;
	}
	$total_block = $total_block - 1;
	if (ceil($t_count/$setup[list_num]) > 0) {
		$a_first_block = "";
		if ($nowblock > 0) {
			$a_first_block .= "<a href='".$_SERVER[PHP_SELF]."?block=0&gotopage=1&type=".$type."&sort=".$sort."' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='첫 페이지';return true\">[1...]</a>&nbsp;&nbsp;";
			$prev_page_exists = true;
		}
		$a_prev_page = "";
		if ($nowblock > 0) {
			$a_prev_page .= "<a href='".$_SERVER[PHP_SELF]."?block=".($nowblock-1)."&gotopage=".($setup[page_num]*($block-1)+$setup[page_num])."&type=".$type."&sort=".$sort."' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='이전 ".$setup[page_num]." 페이지';return true\">[prev]</a>&nbsp;&nbsp;";
			$a_prev_page = $a_first_block.$a_prev_page;
		}
		if (intval($total_block) <> intval($nowblock)) {
			$print_page = "";
			for ($gopage = 1; $gopage <= $setup[page_num]; $gopage++) {
				if ((intval($nowblock*$setup[page_num]) + $gopage) == intval($gotopage)) {
					$print_page .= "<FONT color=red><B>".(intval($nowblock*$setup[page_num]) + $gopage)."</B></font> ";
				} else {
					$print_page .= "<a href='".$_SERVER[PHP_SELF]."?block=".$nowblock."&gotopage=".(intval($nowblock*$setup[page_num]) + $gopage)."&type=".$type."&sort=".$sort."' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$setup[page_num]) + $gopage)."';return true\">[".(intval($nowblock*$setup[page_num]) + $gopage)."]</a> ";
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
					$print_page .= "<FONT color=red><B>".(intval($nowblock*$setup[page_num]) + $gopage)."</B></FONT> ";
				} else {
					$print_page .= "<a href='".$_SERVER[PHP_SELF]."?block=".$nowblock."&gotopage=".(intval($nowblock*$setup[page_num]) + $gopage)."&type=".$type."&sort=".$sort."' onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$setup[page_num]) + $gopage)."';return true\">[".(intval($nowblock*$setup[page_num]) + $gopage)."]</a> ";
				}
			}
		}
		$a_last_block = "";
		if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
			$last_block = ceil($t_count/($setup[list_num]*$setup[page_num])) - 1;
			$last_gotopage = ceil($t_count/$setup[list_num]);

			$a_last_block .= "&nbsp;&nbsp;<a href='".$_SERVER[PHP_SELF]."?block=".$last_block."&gotopage=".$last_gotopage."&type=".$type."&sort=".$sort."' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='마지막 페이지';return true\">[...".$last_gotopage."]</a>";
			$next_page_exists = true;
		}
		$a_next_page = "";
		if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
			$a_next_page .= "&nbsp;&nbsp;<a href='".$_SERVER[PHP_SELF]."?block=".($nowblock+1)."&gotopage=".($setup[page_num]*($nowblock+1)+1)."&type=".$type."&sort=".$sort."' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='다음 ".$setup[page_num]." 페이지';return true\">[next]</a>";

			$a_next_page = $a_next_page.$a_last_block;
		}
	} else {
		$print_page = "<B>1</B>";
	}
	echo "<tr>\n";
	echo "	<td width=\"100%\" style=\"font-size:11px;\" align=\"center\">\n";
	echo "	".$a_div_prev_page.$a_prev_page.$print_page.$a_next_page.$a_div_next_page;
	echo "	</td>\n";
	echo "</tr>\n";
?>
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
<? include ($Dir."lib/bottom.php") ?>

</BODY>
</HTML>