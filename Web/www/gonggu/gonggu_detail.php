<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$gongguimagepath=$Dir.DataDir."shopimages/gonggu/";


$type=$_REQUEST["type"];
if(strlen($type)==0 || $type!="complete") $type="";

$block=$_REQUEST["block"];
$gotopage=$_REQUEST["gotopage"];
$sort=$_REQUEST["sort"];
$type=$_REQUEST["type"];
$seq=$_REQUEST["seq"];

$sql = "SELECT * FROM tblgonginfo WHERE gong_seq='".$seq."' ";
$result = mysql_query($sql,get_db_conn());
if($row = mysql_fetch_object($result)) {
	$end_date = $row->end_date;
	if($end_date<=$cur_date) {
		$type="complete";
	}
	$gdata=$row;
} else {
	header("Location:gonggu.php?type=".$type."&sort=".$sort."&block=".$block."&gotopage=".$gotopage);
	exit;
}
mysql_free_result($result);

$time=mktime((substr($gdata->end_date,8,2)*1),(substr($gdata->end_date,10,2)*1),0,(substr($gdata->end_date,4,2)*1),(substr($gdata->end_date,6,2)*1),(substr($gdata->end_date,0,4)*1));
if (time() > $time) {
	$isEnd = "1";
	$txtTime = "공구마감";
} else {
	$isEnd = "0";
	$tmpTime = $time - time();

	$txtTime_s = ($tmpTime % 60);	//남은초
	$txtTime_i = @floor(($tmpTime % 3600) / 60); //남은 분
	$txtTime_h = @floor(($tmpTime % 86400) / (60*60));
	$txtTime_d = @floor($tmpTime/86400);	//남은 일

	if ($txtTime_d) $txtTime .= $txtTime_d."일 ";
	$txtTime .= $txtTime_h."시간 ".$txtTime_i."분 ".$txtTime_s."초";
}

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

var txtDay = "<?=$txtTime_d?>";
var txtHour = "<?=$txtTime_h?>";
var txtMinute = "<?=$txtTime_i?>";
var txtSec = "<?=$txtTime_s?>";
function finaltimer() {
	setTimeout("TimerControll(\""+txtDay+"\",\""+txtHour+"\",\""+txtMinute+"\",\""+txtSec+"\");",1000);
}

function TimerControll(tDay,tHour,tMin,tSec) {
	var nowing = true;
	txtSec = eval(tSec - 1);
	if (txtSec < 0) {
		txtSec = 59;
		if (tMin > 0) {
			tMin = eval(tMin - 1);
			txtMinute = tMin;
		} else {
			if (tHour > 0) {
				tHour = eval(tHour - 1);
				txtHour = tHour;
			} else {
				if (tDay > 0) {
					tDay = eval(tDay - 1);
					txtDay = tDay;
				} else {
					//종료
					nowing = false;
				}
			}
		}
	}

	if (nowing == true) {
		var txtValue = "";
		if (txtDay > 0) {
			txtValue += txtDay+"일 ";
		}
		txtValue += txtHour+"시간 ";
		txtValue += txtMinute+"분 ";
		txtValue += txtSec+"초 ";
		document.all.finaltime.value = txtValue;
		setTimeout("TimerControll(\""+txtDay+"\",\""+txtHour+"\",\""+txtMinute+"\",\""+txtSec+"\");",1000);
	} else {
		document.all.finaltime.value = "공구마감";
		document.all.idx_gong_ok.style.display="none";
	}
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
<?
$num=intval($gdata->bid_cnt/$gdata->count);
$price=$gdata->start_price-($num*$gdata->down_price);
if($price<$gdata->mini_price) $price=$gdata->mini_price;

$receipt_date=date("Ymd",mktime(0,0,0,substr($gdata->end_date,4,2),substr($gdata->end_date,6,2)+$gdata->receipt_end,substr($gdata->end_date,0,4)));

if(strlen($gdata->deli_money)==0) $delivery="무료 배송";
else if($gdata->deli_money==0) $delivery="착불";
else $delivery="배송료 ".number_format($gdata->deli_money)."원";

$curdate=date("YmdHis");

if($gdata->end_date<$curdate) $gubun="입금 통보";
else if($receipt_date<substr($curdate,0,8) || $gdata->bid_cnt==$gdata->quantity) $gubun="마감";
else if($gdata->start_date<=$curdate) $gubun="진행";
else $gubun="공구 예정";

?>
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
		<td height="2" background="images/line2.gif" width="100%"></td>
	</tr>
	<tr>
		<td height="20"></td>
	</tr>
	<tr>
		<td style="padding:10">
		<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
		<col width="322"></col>
		<col width="15"></col>
		<col></col>
		<tr>
			<td>
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td>
				<table cellpadding="0" cellspacing="1" width="100%" height="320" bgcolor="#E5E5E5">
				<tr>
					<td bgcolor="FFFFFF" align="center">
<?
			if(strlen($gdata->image2)>0 && file_exists($gongguimagepath.$gdata->image2)) {
				echo "<img src=\"".$gongguimagepath.$gdata->image2."\" border=0 ";
				$size=GetImageSize($gongguimagepath.$gdata->image2);
				if(($size[0]>320 || $size[1]>320) && $size[0]>$size[1]) {
					echo " width=\"320\"";
				} else if($size[0]>320 || $size[1]>320) {
					echo " height=\"320\"";
				}
				echo "></td>";
			} else {
				echo "<img src=\"images/product_no_img.gif\" border=\"0\"></td>";
			}
?>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td align="right">
<?
			if(strlen($gdata->image1)>0 && file_exists($gongguimagepath.$gdata->image1)) {
				echo "<A HREF=\"javascript:OpenImage('".$gdata->image1."')\"><IMG SRC=\"images/detail_btnbig.gif\" border=\"0\" vspace=\"3\"></A>";
			}
?>
				</td>
			</tr>
			</table>
			</td>
			<td></td>
			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
			<tr>
				<td><font color="#FF4C00" style="font-size:15px;letter-spacing:-0.5pt;"><b><?=$gdata->production?></b></font></td>
			</tr>
			<tr>
				<td><font color="#FF4C00" style="font-size:15px;letter-spacing:-0.5pt;"><b><?=$gdata->gong_name?></b></font></td>
			</tr>
			<tr>
				<td height="5"></td>
			</tr>
			<tr>
				<td height="4" background="images/detail_titleline.gif"></td>
			</tr>
			<tr>
				<td height="5"></td>
			</tr>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
				<col width="6"></col>
				<col width="63"></col>
				<col width="18"></col>
				<col></col>
				<tr>
					<td><IMG SRC="images/detail_pointa.gif" border="0"></td>
					<td>시중가격</td>
					<td align="center">:</td>
					<td><IMG SRC="images/detail_won1.gif" border="0"> <strong><s><?=number_format($gdata->origin_price)?>원</s></strong></td>
				</tr>
				<tr>
					<td><IMG SRC="images/detail_pointa.gif" border="0"></td>
					<td>현재가격</td>
					<td align="center">:</td>
					<td><IMG SRC="images/detail_won.gif" border="0"> <font color="#F02800"><b><?=number_format($price)?>원</b></font></td>
				</tr>
				<tr valign="top">
					<td style="padding-top:4px;"><IMG SRC="images/detail_pointa.gif" border="0"></td>
					<td>가격변동표</td>
					<td align="center">:</td>
					<td>
					<table cellpadding="0" cellspacing="0" width="230" height="52" bordercolordark="black" bordercolorlight="black">
					<tr>
						<td background="images/gong_graph1.gif">
						<TABLE cellSpacing="0" cellPadding="0" border="0" height="52" border="0" style="table-layout:fixed">
						<col width="60"></col>
						<col width="60"></col>
						<col width="60"></col>
						<tr>
							<td>
							<table width="100%" height="52" cellpadding="0" cellspacing="0" border="0">
							<tr>
								<td align="center"><font color="#696969" style="font-size:11px;"><?=number_format($gdata->start_price)?>원</font></td>
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
								<td align="center"><font color="#696969" style="font-size:11px;"><?=number_format($gdata->mini_price)?>원</font></td>
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
				<tr>
					<td colspan="4" height="15"><hr size="1" noshade color="#E5E5E5"></td>
				</tr>
				<tr>
					<td><IMG SRC="images/detail_pointa.gif" border="0"></td>
					<td>입금마감</td>
					<td align="center">:</td>
					<td><?=substr($receipt_date,0,4)."년 ".substr($receipt_date,4,2)."월 ".substr($receipt_date,6,2)."일까지"?></td>
				</tr>
				<tr>
					<td><IMG SRC="images/detail_pointa.gif" border="0"></td>
					<td>배송정보</td>
					<td align="center">:</td>
					<td><?=$delivery?></td>
				</tr>
				<tr>
					<td><IMG SRC="images/detail_pointa.gif" border="0"></td>
					<td>구매수량</td>
					<td align="center">:</td>
					<td><b><?=$gdata->bid_cnt?></b> 개 (총 판매수량 <?=$gdata->quantity?>개)</td>
				</tr>
				<tr>
					<td><IMG SRC="images/detail_pointa.gif" border="0"></td>
					<td>남은시간</td>
					<td align="center">:</td>
					<td><input type=text name="finaltime" value="<?=$txtTime?>" size="25" style="background-color:#FFFFFF;border:none;color:#F02800;font-weight:bold;height:16px;" readonly></td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td height="5"></td>
			</tr>
			<tr>
				<td height="4" background="images/pdetail_skin1_titleline1.gif"></td>
			</tr>
			<tr>
				<td height="18"></td>
			</tr>
			<?if($gubun=="진행"){?>
			<tr>
				<td align="center" id="idx_gong_ok"><A HREF="gonggu_ok.php?seq=<?=$gdata->gong_seq?>&sort=<?=$sort?>&block=<?=$block?>&gotopage=<?=$gotopage?>"><IMG SRC="images/gonggu_ok.gif" border="0"></A></td>
			</tr>
			<?}?>
			<tr>
				<td height="20"></td>
			</tr>
			</table>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td height="20"></td>
	</tr>
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
		<tr>
			<td background="images/gongdetail_titlebg.gif"><img src="images/gongdetail_title.gif" border="0"></td>
			<td background="images/gongdetail_titlebg.gif"></td>
			<td align="right" background="images/gongdetail_titlebg.gif"><img src="images/gongdetail_titleend.gif" border="0"></td>
		</tr>
		<tr>
			<td colspan="3" style="padding:5px;">
<?
			if(substr($gdata->content,0,21)=="<!DOCTYPE HTML PUBLIC")
				echo $gdata->content;
			else if (strpos($gdata->content,"table>")!=false || strpos($gdata->content,"TABLE>")!=false)
				echo "<pre>".$gdata->content."</pre>";
			else if(strpos($gdata->content,"</")!=false)
				echo ereg_replace("\n","<br>",$gdata->content);
			else if(strpos($gdata->content,"img")!=false || strpos($gdata->content,"IMG")!=false)
				echo ereg_replace("\n","<br>",$gdata->content);
			else
				echo ereg_replace(" ","&nbsp;",ereg_replace("\n","<br>",$gdata->content));
?>
			</td>
		</tr>
		<tr>
			<td height="1" colspan="3" bgcolor="#DDDDDD"></td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td height="20"></td>
	</tr>
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
		<tr>
			<td><img src="images/gongdetail_title1.gif" border="0"></td>
		</tr>
		<tr>
			<td>			
			<table cellpadding="0" cellspacing="0" width="100%">
			<col width="100"></col>
			<col width="80"></col>
			<col width="50"></col>
			<col></col>
			<tr>
				<td height="2" colspan="4" bgcolor="#000000"></td>
			</tr>
			<tr height="30" align="center" bgcolor="#F8F8F8" style="letter-spacing:-0.5pt;">
				<td><font color="#333333"><b>참여일</b></font></td>
				<td><font color="#333333"><b>아이디</b></font></td>
				<td><font color="#333333"><b>수량</b></font></td>
				<td><font color="#333333"><b>내용</b></font></td>
			</tr>
			<tr>
				<td height="1" colspan="4" bgcolor="#DDDDDD"></td>
			</tr>
<?
			//리스트 세팅
			$setup[page_num] = 10;
			$setup[list_num] = 20;

			$block2=$_REQUEST["block2"];
			$gotopage2=$_REQUEST["gotopage2"];

			if ($block2 != "") {
				$nowblock = $block2;
				$curpage  = $block2 * $setup[page_num] + $gotopage2;
			} else {
				$nowblock = 0;
			}

			if (($gotopage2 == "") || ($gotopage2 == 0)) {
				$gotopage2 = 1;
			}

			$sql = "SELECT COUNT(*) as t_count FROM tblgongresult WHERE gong_seq='".$seq."' ";
			$result = mysql_query($sql,get_db_conn());
			$row = mysql_fetch_object($result);
			$t_count = $row->t_count;
			mysql_free_result($result);
			$pagecount = (($t_count - 1) / $setup[list_num]) + 1;

			$sql = "SELECT * FROM tblgongresult WHERE gong_seq='".$seq."' ORDER BY date DESC ";
			$sql.= "LIMIT " . ($setup[list_num] * ($gotopage2 - 1)) . ", " . $setup[list_num];
			$result=mysql_query($sql,get_db_conn());
			$i=0;
			while($row=mysql_fetch_object($result)) {
				$rdate=mktime((substr($row->date,8,2)*1),(substr($row->date,10,2)*1),(substr($row->date,12,2)*1),(substr($row->date,4,2)*1),(substr($row->date,6,2)*1),(substr($row->date,0,4)*1));

				if ($i % 2 == 0) $trbg = "#fcfcfc";
				else $trbg = "#f4f4f4";
				echo "<tr height=\"26\" align=\"center\">\n";
				echo "	<td><font color=\"#333333\">".date("Y-m-d H:i",$rdate)."</font></td>\n";
				echo "	<td><font color=\"#333333\">".$row->id."</font></td>\n";
				echo "	<td><font color=\"#333333\">".$row->buy_cnt."개</font></td>\n";
				echo "	<td align=\"left\"><font color=\"#333333\">".$row->memo."</font></td>\n";
				echo "</tr>\n";
				echo "<tr>\n";
				echo "	<td height=\"1\" colspan=\"4\" bgcolor=\"#DDDDDD\"></td>\n";
				echo "</tr>\n";
				$i++;
			}
			mysql_free_result($result);

			if($i==0) {
				echo "<tr><td height=\"30\" colspan=\"4\" align=\"center\"><font color=\"#333333\">공구에 참여한 회원이 없습니다.</font></td></tr>";
				echo "<tr>\n";
				echo "	<td height=\"1\" colspan=\"4\" bgcolor=\"#DDDDDD\"></td>\n";
				echo "</tr>\n";
			}
?>
			</table>
			</td>
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
					$a_first_block .= "<a href='".$_SERVER[PHP_SELF]."?seq=".$seq."&block=".$block."&gotopage=".$gotopage."&block2=0&gotopage2=1&type=".$type."&sort=".$sort."' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='첫 페이지';return true\">[1...]</a>&nbsp;&nbsp;";
					$prev_page_exists = true;
				}
				$a_prev_page = "";
				if ($nowblock > 0) {
					$a_prev_page .= "<a href='".$_SERVER[PHP_SELF]."?seq=".$seq."&block=".$block."&gotopage=".$gotopage."&block2=".($nowblock-1)."&gotopage2=".($setup[page_num]*($block2-1)+$setup[page_num])."&type=".$type."&sort=".$sort."' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='이전 ".$setup[page_num]." 페이지';return true\">[prev]</a>&nbsp;&nbsp;";
					$a_prev_page = $a_first_block.$a_prev_page;
				}
				if (intval($total_block) <> intval($nowblock)) {
					$print_page = "";
					for ($gopage = 1; $gopage <= $setup[page_num]; $gopage++) {
						if ((intval($nowblock*$setup[page_num]) + $gopage) == intval($gotopage2)) {
							$print_page .= "<FONT color=red><B>".(intval($nowblock*$setup[page_num]) + $gopage)."</B></font> ";
						} else {
							$print_page .= "<a href='".$_SERVER[PHP_SELF]."?seq=".$seq."&block=".$block."&gotopage=".$gotopage."&block2=".$nowblock."&gotopage2=".(intval($nowblock*$setup[page_num]) + $gopage)."&type=".$type."&sort=".$sort."' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$setup[page_num]) + $gopage)."';return true\">[".(intval($nowblock*$setup[page_num]) + $gopage)."]</a> ";
						}
					}
				} else {
					if (($pagecount % $setup[page_num]) == 0) {
						$lastpage = $setup[page_num];
					} else {
						$lastpage = $pagecount % $setup[page_num];
					}

					for ($gopage = 1; $gopage <= $lastpage; $gopage++) {
						if (intval($nowblock*$setup[page_num]) + $gopage == intval($gotopage2)) {
							$print_page .= "<FONT color=red><B>".(intval($nowblock*$setup[page_num]) + $gopage)."</B></FONT> ";
						} else {
							$print_page .= "<a href='".$_SERVER[PHP_SELF]."?seq=".$seq."&block=".$block."&gotopage=".$gotopage."&block2=".$nowblock."&gotopage2=".(intval($nowblock*$setup[page_num]) + $gopage)."&type=".$type."&sort=".$sort."' onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$setup[page_num]) + $gopage)."';return true\">[".(intval($nowblock*$setup[page_num]) + $gopage)."]</a> ";
						}
					}
				}
				$a_last_block = "";
				if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
					$last_block = ceil($t_count/($setup[list_num]*$setup[page_num])) - 1;
					$last_gotopage = ceil($t_count/$setup[list_num]);

					$a_last_block .= "&nbsp;&nbsp;<a href='".$_SERVER[PHP_SELF]."?seq=".$seq."&block=".$block."&gotopage=".$gotopage."&block2=".$last_block."&gotopage2=".$last_gotopage."&type=".$type."&sort=".$sort."' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='마지막 페이지';return true\">[...".$last_gotopage."]</a>";
					$next_page_exists = true;
				}
				$a_next_page = "";
				if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
					$a_next_page .= "&nbsp;&nbsp;<a href='".$_SERVER[PHP_SELF]."?seq=".$seq."&block=".$block."&gotopage=".$gotopage."&block2=".($nowblock+1)."&gotopage2=".($setup[page_num]*($nowblock+1)+1)."&type=".$type."&sort=".$sort."' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='다음 ".$setup[page_num]." 페이지';return true\">[next]</a>";

					$a_next_page = $a_next_page.$a_last_block;
				}
			} else {
				$print_page = "<B>1</B>";
			}
			echo "<tr>\n";
			echo "	<td width=\"100%\" style=\"padding-top:0pt; padding-right:0; padding-bottom:0; padding-left:0pt;\" align=\"center\">\n";
			echo "	".$a_div_prev_page.$a_prev_page.$print_page.$a_next_page.$a_div_next_page;
			echo "	</td>\n";
			echo "</tr>\n";
?>
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>
<tr>
	<td height="20"></td>
</tr>
</table>
<?
if ($isEnd == 0) {
	echo "<script>window.onload = finaltimer;</script>";
}
?>

<? include ($Dir."lib/bottom.php") ?>

</BODY>
</HTML>