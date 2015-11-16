<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$auctionimagepath=$Dir.DataDir."shopimages/auction/";

if(strlen($_ShopInfo->getMemid())==0) {
	Header("Location:".$Dir.FrontDir."login.php?chUrl=".getUrl());
	exit;
}

$block=$_REQUEST["block"];
$gotopage=$_REQUEST["gotopage"];
$sort=$_REQUEST["sort"];
$type=$_REQUEST["type"];
$seq=$_REQUEST["seq"];
$start_date=$_REQUEST["start_date"];

$mode=$_POST["mode"];
$bid_price=(int)$_POST["bid_price"];
$bid_msg=$_POST["bid_msg"];

$auct_moveday=(int)$_data->auct_moveday;

if($auct_moveday==0) $curdate = date("YmdHis");
else $curdate = date("YmdHis",mktime(0,0,0,date("m"),date("d")-$auct_moveday,date("Y")));

$sql = "SELECT * FROM tblauctioninfo WHERE auction_seq='".$seq."' AND start_date='".$start_date."' ";
$result = mysql_query($sql,get_db_conn());
if($row = mysql_fetch_object($result)) {
	$end_date = $row->end_date;
	if($end_date<=$cur_date) {
		$type="complete";
	}
	$adata=$row;
} else {
	header("Location:auction.php?type=".$type."&sort=".$sort."&block=".$block."&gotopage=".$gotopage);
	exit;
}
mysql_free_result($result);

$start_date=substr($adata->start_date,0,4)."/".substr($adata->start_date,4,2)."/".substr($adata->start_date,6,2)." ".substr($adata->start_date,8,2).":".substr($adata->start_date,10,2);
$end_date=substr($adata->end_date,0,4)."/".substr($adata->end_date,4,2)."/".substr($adata->end_date,6,2)." ".substr($adata->end_date,8,2).":".substr($adata->end_date,10,2);
$end_time=mktime((substr($adata->end_date,8,2)*1),(substr($adata->end_date,10,2)*1),0,(substr($adata->end_date,4,2)*1),(substr($adata->end_date,6,2)*1),(substr($adata->end_date,0,4)*1));

if($end_time<=time()) {
	echo "<html><head><title></title></head><body onload=\"alert('경매가 종료되었습니다.');location.href='auction_detail.php?seq=".$seq."&start_date=".$adata->start_date."&type=".$type."&sort=".$sort."&block=".$block."&gotopage=".$gotopage."';\"></body></html>";exit;
}

if ($adata->mini_unit>0) {
	$danwi=$adata->mini_unit;
} else {
	if ($adata->last_price<5000) $danwi=100;
	else if ($adata->last_price<50000) $danwi=500;
	else if ($adata->last_price<100000) $danwi=1000;
	else $danwi=2000;
}

if ($adata->bid_cnt < $adata->quantity) {
	$accept_pay = $adata->last_price;
} else {
	$accept_pay = $adata->last_price + $danwi;
}

if($mode=="insert") {
	if ($bid_price < $accept_pay) {
		echo "<html><head><title></title></head><body onload=\"alert('입찰금액 입력이 잘못되었습니다.\\n\\n다시 확인하시기 바랍니다.');history.go(-1);\"></body></html>";exit;
	} else {
		$bid_msg=ereg_replace("'","",stripslashes($bid_msg));
		$sql = "INSERT tblauctionresult SET ";
		$sql.= "auction_seq	= '".$seq."', ";
		$sql.= "start_date	= '".$adata->start_date."', ";
		$sql.= "id			= '".$_ShopInfo->getMemid()."', ";
		$sql.= "quantity	= '1', ";
		$sql.= "price		= '".$bid_price."', ";
		$sql.= "date		= '".date("YmdHis")."', ";
		$sql.= "content		= '".$bid_msg."' ";
		mysql_query($sql,get_db_conn());

		if (mysql_errno()) {
			echo "<html><head><title></title></head><body onload=\"alert('입찰중 오류가 발생하였습니다.\\n\\n다시 입찰하시기 바랍니다.');history.go(-1);\"></body></html>";exit;
			sendmail(AdminMail,"경매입찰에러!","$sql\n".mysql_error(),"Content-Type: text/plain\r\n");
		} else {
			$sql = "UPDATE tblauctioninfo SET ";
			$sql.= "bid_cnt		= bid_cnt + 1, ";
			$sql.= "last_price	= '".$bid_price."' ";
			$sql.= "WHERE auction_seq='".$seq."' AND start_date='".$adata->start_date."' ";
			mysql_query($sql,get_db_conn());
			if (mysql_errno()) {
				sendmail(AdminMail,"경매금에러!","$sql\n".mysql_error(),"Content-Type: text/plain\r\n");
			}
			echo "<html><head><title></title></head><body onload=\"alert('입찰되었습니다.');location.href='auction_detail.php?seq=".$seq."&start_date=".$adata->start_date."&type=".$type."&sort=".$sort."&block=".$block."&gotopage=".$gotopage."';\"></body></html>";exit;
			sendmail(AdminMail,"경매입찰에러!","$sql\n".mysql_error(),"Content-Type: text/plain\r\n");
		}
	}
}

$type=$_REQUEST["type"];
if(strlen($type)==0 || $type!="complete") $type="";

$tname0="auction_menu1.gif";
$tname1="auction_menu2.gif";
if($type=="complete") {
	$tname1="auction_menu1r.gif";
} else {
	$tname0="auction_menu1r.gif";
}
?>
<HTML>
<HEAD>
<TITLE><?=$_data->shoptitle?> - 경매</TITLE>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META name="description" content="<?=(strlen($_data->shopdescription)>0?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<?include($Dir."lib/style.php")?>
<SCRIPT LANGUAGE="JavaScript">
<!--
function CheckForm() {
	form=document.form1;
	if (form.bid_price.value < <?=$accept_pay?>) {
		alert("입찰금액이 최저입찰금액보다 적습니다.\n\n다시 확인하시기 바랍니다.");
		form.bid_price.focus();
		return;
	}
	if (form.bid_price.value % 100 != 0) {
		alert("10원 단위로 입찰하실 수 없습니다.");
		form.bid_price.focus();
		return;
	}	
	if(confirm("입찰에 참여하시겠습니까?")) {
		form.mode.value="insert";
		form.submit();
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
	echo "<td><img src=\"".$Dir.DataDir."design/auction_title.gif\" border=\"0\" alt=\"경매\"></td>\n";
} else {
	echo "<td>\n";
	echo "<TABLE WIDTH=100% BORDER=0 CELLPADDING=0 CELLSPACING=0>\n";
	echo "<TR>\n";
	echo "	<TD><IMG SRC=".$Dir."images/".$_data->icon_type."/auction_title_head.gif ALT=></TD>\n";
	echo "	<TD width=100% valign=top background=".$Dir."images/".$_data->icon_type."/auction_title_bg.gif></TD>\n";
	echo "	<TD width=40><IMG SRC=".$Dir."images/".$_data->icon_type."/auction_title_tail.gif ALT=></TD>\n";
	echo "</TR>\n";
	echo "</TABLE>\n";
	echo "</td>\n";
}
?>
</tr>
<tr>
	<td style="padding-left:10px;padding-right:10px;">
	<table align="center" cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
	<tr>
		<td width="100%">
		<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
		<tr>
			<td align="right">
			<table cellpadding="0" cellspacing="0">
			<tr>
				<td><a href="auction.php"><img src="images/<?=$tname0?>" border="0"></a></td>
				<td><a href="auction.php?type=complete"><img src="images/<?=$tname1?>" border="0"></a></td>
				<td><a href="myauction.php"><img src="images/auction_menu3.gif" border="0"></a></td>
				<td><a href="myauction.php?type=complete"><img src="images/auction_menu4.gif" border="0"></a></td>
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
			<td style="padding-top:5px;padding-bottom:5px;font-size:15px;letter-spacing:-0.5pt;"><font color="#FF4C00"><b><?=$adata->auction_name?></b></font></td>
		</tr>
		<tr>
			<td>
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
			<col width="21"></col>
			<col></col>
			<col width="21"></col>
			<tr>
				<td><IMG SRC="images/auction_detail_t01_left.gif" border="0"></td>
				<td background="images/auction_detail_t01.gif"></td>
				<td><IMG SRC="images/auction_detail_t01_right.gif" border="0"></td>
			</tr>
			<tr>
				<td background="images/auction_detail_t02.gif"><img src="images/auction_detail_t02.gif" border="0"></td>
				<td valign="top">
				<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<col width="300"></col>
				<col width="15"></col>
				<col></col>
				<tr>
					<td valign="top">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="100%">
						<table cellpadding="0" cellspacing="1" width="298" height="198" bgcolor="#E5E5E5">
						<tr>
							<td width="298" bgcolor="FFFFFF" align="center">
<?
							if(strlen($adata->product_image)>0 && file_exists($auctionimagepath.$adata->product_image)) {
								echo "<img src=\"".$auctionimagepath.$adata->product_image."\" border=\"0\" ";
								$size=GetImageSize($auctionimagepath.$adata->product_image);
								if(($size[0]>298 || $size[1]>198) && $size[0]>$size[1]) {
									echo " width=\"298\"";
								} else if($size[0]>298 || $size[1]>198) {
									echo " height=\"198\"";
								}
								echo "></td>";
							} else {
								echo "<img src=\"images/product_no_img_big.gif\" width=\"298\" height=\"198\" border=\"0\"></td>";
							}
?>
						</tr>
						</table>
						</td>
					</tr>
					</table>
					</td>
					<td></td>
					<td valign="top">
					<table cellpadding="0" cellspacing="0" width="100%">
					<col width="16"></col>
					<col width="80"></col>
					<col width="10"></col>
					<col></col>
					<tr>
						<td colspan="4"><IMG SRC="images/auction_detail_icon.gif" border="0" style="margin-bottom:5px;"></td>
					</tr>
					<tr>
						<td colspan="4" height="4" background="images/auction_detail_line1.gif"></td>
					</tr>
					<tr height="22" bgcolor="#F8F8F8">
						<td align="center"><IMG SRC="images/auction_detail_icon1.gif" border="0"></td>
						<td>경매마감일</td>
						<td align="center">:</td>
						<td><font color="#F02800"><b><?=date("Y-m-d H:i:s",$end_time)?></b></font></td>
					</tr>
					<tr height="22" bgcolor="#F8F8F8">
						<td align="center"><IMG SRC="images/auction_detail_icon1.gif" border="0"></td>
						<td>시작가</td>
						<td align="center">:</td>
						<td><IMG SRC="images/detail_won1.gif" border="0"> <?=number_format($adata->start_price)?> 원</td>
					</tr>
					<tr height="22" bgcolor="#F8F8F8">
						<td align="center"><IMG SRC="images/auction_detail_icon1.gif" border="0"></td>
						<td>현재가</td>
						<td align="center">:</td>
						<td><IMG SRC="images/detail_won.gif" border="0"> <font color="#F02800"><b><?=number_format($adata->last_price)?> 원</b></font></td>
					</tr>
					<tr height="22" bgcolor="#F8F8F8">
						<td align="center"><IMG SRC="images/auction_detail_icon1.gif" border="0"></td>
						<td>입찰자수</td>
						<td align="center">:</td>
						<td><b><?=$adata->bid_cnt?></b> 명</td>
					</tr>
					<tr>
						<td height="1" colspan="4" bgcolor="#E8E8E8"></td>
					</tr>
					<tr>
						<td colspan="4" height="5"></td>
					</tr>
					<tr height="22">
						<td align="center"><IMG SRC="images/auction_detail_icon2.gif" border="0"></td>
						<td>판매자</td>
						<td align="center">:</td>
						<td>쇼핑몰 운영자</td>
					</tr>
					<tr height="22">
						<td align="center"><IMG SRC="images/auction_detail_icon2.gif" border="0"></td>
						<td>사용기간</td>
						<td align="center">:</td>
						<td><?=$adata->used_period?></td>
					</tr>
					<tr height="22">
						<td align="center"><IMG SRC="images/auction_detail_icon2.gif" border="0"></td>
						<td>배달가능지역</td>
						<td align="center">:</td>
						<td><?=$adata->deli_area?></td>
					</tr>
					<tr>
						<td colspan="4">
						<table cellpadding="0" cellspacing="0">
						<tr>
							<td><img src="images/action_bg.gif" border="0"></td>
						</tr>
						<tr>
							<td style="" background="images/action_bg1.gif" style="padding-left:5pt;font-size:11px;letter-spacing:-0.5pt;line-height:15px;"><font color="#ffffff">* <FONT color="#FFFF99"><b>입찰단위는 최소 <?=number_format($danwi)?>원 단위</b></font>부터 입찰가능합니다.<BR>
							* 입찰가격 입력시에는 <FONT color="#FFDD00"><b>콤마(,)</b></FONT>를 제외하고 입력하시기 바랍니다.<BR>
							* 허위입찰시 관리자가 임의로 삭제가 가능하오니 이점 양지바랍니다.</font></td>
						</tr>
						<tr>
							<td><img src="images/action_bg2.gif" border="0"></td>
						</tr>
						</table>
						</td>
					</tr>
					<tr>
						<td colspan="4" height="5"></td>
					</tr>
					<form name=form1 method=post action="<?=$_SERVER[PHP_SELF]?>">
					<input type=hidden name=mode>
					<input type=hidden name=block value="<?=$block?>">
					<input type=hidden name=gotopage value="<?=$gotopage?>">
					<input type=hidden name=sort value="<?=$sort?>">
					<input type=hidden name=type value="<?=$type?>">
					<input type=hidden name=seq value="<?=$seq?>">
					<input type=hidden name=start_date value="<?=$adata->start_date?>">
					<tr valign="top">
						<td align="center"><IMG SRC="images/auction_detail_icon2.gif" border="0" vspace="5"></td>
						<td>입찰금액</td>
						<td align="center">:</td>
						<td><input type=text name="bid_price" size="10" onKeyUp="return strnumkeyup(this);" style="BACKGROUND-COLOR:#F7F7F7;" class="input">원(콤마(&quot;,&quot;)없이 입력)<br><b>* 현재 <font color="#F02800"><?=number_format($accept_pay)?>원</font>부터 입찰가능</b></td>
					</tr>
					<tr>
						<td height="1" colspan="4" bgcolor="#E8E8E8"></td>
					</tr>
					<tr valign="top" height="22">
						<td align="center"><IMG SRC="images/auction_detail_icon2.gif" border="0" vspace="5"></td>
						<td>입찰내용</td>
						<td align="center">:</td>
						<td><input type=text name="bid_msg" size="30" maxlength="200" style="width:100%;BACKGROUND-COLOR:#F7F7F7;" class="input"><br>예)제가 꼭 사고싶어요.</td>
					</tr>
					<tr>
						<td height="1" colspan="4" bgcolor="#E8E8E8"></td>
					</tr>
					<tr>
						<td align="center" colspan="4"><A HREF="javascript:CheckForm()"><IMG SRC="images/auction_ok.gif" border="0" vspace="5"></A></td>
					</tr>
					</form>
					</table>
					</td>
				</tr>
				</table>
				</td>
				<td background="images/auction_detail_t04.gif"><img src="images/auction_detail_t04.gif" border="0"></td>
			</tr>
			<tr>
				<td><IMG SRC="images/auction_detail_t03_left.gif" border="0"></td>
				<td background="images/auction_detail_t03.gif"></td>
				<td><IMG SRC="images/auction_detail_t03_right.gif" border="0"></td>
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
				<td><img src="images/auction_title1.gif" border="0"></td>
				<td align="right"><img src="images/auction_title1end.gif" border="0"></td>
			</tr>
			</table>
			</td>
		</tr>
		<tr>
			<td>
			<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
			<col width="80"></col>
			<col width="120"></col>
			<col width="80"></col>
			<col></col>
			<tr>
				<td height="2" colspan="4" bgcolor="#000000"></td>
			</tr>
			<tr height="30" align="center" bgcolor="#F8F8F8" style="letter-spacing:-0.5pt;">
				<td><font color="#333333"><b>입찰자</b></font></td>
				<td><font color="#333333"><b>입찰일자</b></font></td>
				<td><font color="#333333"><b>입찰가격</b></font></td>
				<td><font color="#333333"><b>입찰내용</b></font></td>
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

		$sql = "SELECT COUNT(*) as t_count FROM tblauctionresult ";
		$sql.= "WHERE auction_seq='".$seq."' AND start_date='".$adata->start_date."' ";
		$result = mysql_query($sql,get_db_conn());
		$row = mysql_fetch_object($result);
		$t_count = $row->t_count;
		mysql_free_result($result);
		$pagecount = (($t_count - 1) / $setup[list_num]) + 1;

		$sql = "SELECT * FROM tblauctionresult ";
		$sql.= "WHERE auction_seq='".$seq."' AND start_date='".$adata->start_date."' ";
		$sql.= "ORDER BY price DESC ";
		$sql.= "LIMIT " . ($setup[list_num] * ($gotopage2 - 1)) . ", " . $setup[list_num];
		$result=mysql_query($sql,get_db_conn());
		$j=0;
		while($row = mysql_fetch_object($result)) {
			$rdate=mktime((substr($row->date,8,2)*1),(substr($row->date,10,2)*1),0,(substr($row->date,4,2)*1),(substr($row->date,6,2)*1),(substr($row->date,0,4)*1));

			if ($j % 2 == 0) $trbg = "#fcfcfc";
			else $trbg = "#f4f4f4";
			echo "<tr height=\"26\" align=\"center\">\n";
			echo "<td>";
			if ($row->price == $adata->last_price) {
				echo "<font color=\"#FF3300\"><B>".$row->id."</B></font>";
			} else {
				echo "<font color=\"#333333\">".$row->id."</font>";
			}
			echo "</a></td>\n";
			echo "<td><font color=\"#333333\">".date("Y-m-d H:i:s",$rdate)."</font></td>\n";
			echo "<td><font color=\"#333333\">".number_format($row->price)."</font></td>\n";
			echo "<td align=\"left\"><font color=\"#333333\">&nbsp;".$row->content."&nbsp;</font></td>\n";
			echo "</tr>\n";
			echo "<tr>\n";
			echo "	<td height=\"1\" colspan=\"4\" bgcolor=\"#DDDDDD\"></td>\n";
			echo "</tr>\n";
			$j++;
		}
		mysql_free_result($result);

		if($j==0) {
			echo "<tr height=35><td style=\"color:#333333;padding-bottom:3pt;padding-top:3pt;line-height:18px;\" colspan=4 align=center valign=middle>입찰에 참여한 회원이 없습니다.</td></tr>";
			echo "<tr>\n";
			echo "	<td height=\"1\" colspan=\"4\" bgcolor=\"#DDDDDD\"></td>\n";
			echo "</tr>\n";
		}
?>
				<tr>
				<td height="20" colspan="4"></td>
			</tr>
			<tr>
				<td colspan="4">
				<table cellpadding="0" cellspacing="0" width="100%">
<?
		$total_block = intval($pagecount / $setup[page_num]);
		if (($pagecount % $setup[page_num]) > 0) {
			$total_block = $total_block + 1;
		}
		$total_block = $total_block - 1;
		if (ceil($t_count/$setup[list_num]) > 0) {
			$a_first_block = "";
			if ($nowblock > 0) {
				$a_first_block .= "<a href='".$_SERVER[PHP_SELF]."?seq=".$seq."&start_date=".$adata->start_date."&block=".$block."&gotopage=".$gotopage."&block2=0&gotopage2=1&type=".$type."&sort=".$sort."' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='첫 페이지';return true\">[1...]</a>&nbsp;&nbsp;";
				$prev_page_exists = true;
			}
			$a_prev_page = "";
			if ($nowblock > 0) {
				$a_prev_page .= "<a href='".$_SERVER[PHP_SELF]."?seq=".$seq."&start_date=".$adata->start_date."&block=".$block."&gotopage=".$gotopage."&block2=".($nowblock-1)."&gotopage2=".($setup[page_num]*($block2-1)+$setup[page_num])."&type=".$type."&sort=".$sort."' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='이전 ".$setup[page_num]." 페이지';return true\">[prev]</a>&nbsp;&nbsp;";
				$a_prev_page = $a_first_block.$a_prev_page;
			}
			if (intval($total_block) <> intval($nowblock)) {
				$print_page = "";
				for ($gopage = 1; $gopage <= $setup[page_num]; $gopage++) {
					if ((intval($nowblock*$setup[page_num]) + $gopage) == intval($gotopage2)) {
						$print_page .= "<FONT color=red><B>".(intval($nowblock*$setup[page_num]) + $gopage)."</B></font> ";
					} else {
						$print_page .= "<a href='".$_SERVER[PHP_SELF]."?seq=".$seq."&start_date=".$adata->start_date."&block=".$block."&gotopage=".$gotopage."&block2=".$nowblock."&gotopage2=".(intval($nowblock*$setup[page_num]) + $gopage)."&type=".$type."&sort=".$sort."' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$setup[page_num]) + $gopage)."';return true\">[".(intval($nowblock*$setup[page_num]) + $gopage)."]</a> ";
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
						$print_page .= "<a href='".$_SERVER[PHP_SELF]."?seq=".$seq."&start_date=".$adata->start_date."&block=".$block."&gotopage=".$gotopage."&block2=".$nowblock."&gotopage2=".(intval($nowblock*$setup[page_num]) + $gopage)."&type=".$type."&sort=".$sort."' onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$setup[page_num]) + $gopage)."';return true\">[".(intval($nowblock*$setup[page_num]) + $gopage)."]</a> ";
					}
				}
			}
			$a_last_block = "";
			if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
				$last_block = ceil($t_count/($setup[list_num]*$setup[page_num])) - 1;
				$last_gotopage = ceil($t_count/$setup[list_num]);

				$a_last_block .= "&nbsp;&nbsp;<a href='".$_SERVER[PHP_SELF]."?seq=".$seq."&start_date=".$adata->start_date."&block=".$block."&gotopage=".$gotopage."&block2=".$last_block."&gotopage2=".$last_gotopage."&type=".$type."&sort=".$sort."' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='마지막 페이지';return true\">[...".$last_gotopage."]</a>";
				$next_page_exists = true;
			}
			$a_next_page = "";
			if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
				$a_next_page .= "&nbsp;&nbsp;<a href='".$_SERVER[PHP_SELF]."?seq=".$seq."&start_date=".$adata->start_date."&block=".$block."&gotopage=".$gotopage."&block2=".($nowblock+1)."&gotopage2=".($setup[page_num]*($nowblock+1)+1)."&type=".$type."&sort=".$sort."' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='다음 ".$setup[page_num]." 페이지';return true\">[next]</a>";

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
			</table>
			</td>
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
</table>

<? include ($Dir."lib/bottom.php") ?>

</BODY>
</HTML>