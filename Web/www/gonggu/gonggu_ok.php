<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

if(strlen($_ShopInfo->getMemid())==0) {
	Header("Location:".$Dir.FrontDir."login.php?chUrl=".getUrl());
	exit;
}

$gongguimagepath=$Dir.DataDir."shopimages/gonggu/";

$mode=$_POST["mode"];

$type=$_REQUEST["type"];
if(strlen($type)==0 || $type!="complete") $type="";

$block=$_REQUEST["block"];
$gotopage=$_REQUEST["gotopage"];
$sort=$_REQUEST["sort"];
$type=$_REQUEST["type"];
$seq=$_REQUEST["seq"];

$sql = "SELECT id FROM tblgongresult WHERE gong_seq='".$seq."' AND id='".$_ShopInfo->getMemid()."' ";
$result=mysql_query($sql,get_db_conn());
if($row=mysql_fetch_object($result)) {
	echo "<html><head><title></title></head><body onload=\"alert('이미 공동구매에 참여하셨습니다.');history.go(-1);\"></body></html>";exit;
}
mysql_free_result($result);

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

$nowcnt=$gdata->quantity - $gdata->bid_cnt;

$start_date=substr($gdata->start_date,0,4)."/".substr($gdata->start_date,4,2)."/".substr($gdata->start_date,6,2)." ".substr($gdata->start_date,8,2).":".substr($gdata->start_date,10,2);
$end_date=substr($gdata->end_date,0,4)."/".substr($gdata->end_date,4,2)."/".substr($gdata->end_date,6,2)." ".substr($gdata->end_date,8,2).":".substr($gdata->end_date,10,2);
$end_time=mktime((substr($gdata->end_date,8,2)*1),(substr($gdata->end_date,10,2)*1),0,(substr($gdata->end_date,4,2)*1),(substr($gdata->end_date,6,2)*1),(substr($gdata->end_date,0,4)*1));

if($end_time<=time()) {
	echo "<html><head><title></title></head><body onload=\"alert('공구가 마감되었습니다.');location.href='gonggu_detail.php?seq=".$seq."&type=".$type."&sort=".$sort."&block=".$block."&gotopage=".$gotopage."';\"></body></html>";exit;
}

$time=$end_time;
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

if($mode=="insert") {
	$name=$_POST["name"];
	$email=$_POST["email"];
	$tel=$_POST["tel"];
	$address=$_POST["address"];
	$memo=$_POST["memo"];
	$buy_cnt=$_POST["buy_cnt"];

	$onload="";
	if(strlen($name)==0) {
		$onload="이름을 입력하셔야 합니다.";
	} else if(strlen($email)==0) {
		$onload="이메일을 입력하셔야 합니다.";
	} else if(!ismail($email)) {
		$onload="이메일 입력이 잘못되었습니다.\\n\\n확인 후 다시 입력하시기 바랍니다.";
	} else if(strlen($tel)==0) {
		$onload="전화번호를 입력하셔야 합니다.";
	} else if(strlen($address)==0) {
		$onload="주소를 입력하셔야 합니다.";
	} else if(strlen($buy_cnt)==0 || $buy_cnt==0) {
		$onload="구매수량을 입력하셔야 합니다.";
	} else if($nowcnt<$buy_cnt) {
		$onload="구매수량이 초과되었습니다.\\n\\n현재 남아있는 수량은 ".$nowcnt."개 입니다.";
	}

	if(strlen($onload)>0) {
		echo "<html><head><title></title></head><body onload=\"alert('".$onload."');history.go(-1);\"></body></html>";exit;
	}

	$sql = "INSERT tblgongresult SET ";
	$sql.= "gong_seq	= '".$gdata->gong_seq."', ";
	$sql.= "id			= '".$_ShopInfo->getMemid()."', ";
	$sql.= "name		= '".$name."', ";
	$sql.= "email		= '".$email."', ";
	$sql.= "tel			= '".$tel."', ";
	$sql.= "address		= '".$address."', ";
	$sql.= "process_gbn	= 'I', ";
	$sql.= "buy_cnt		= '".$buy_cnt."', ";
	$sql.= "date		= '".date("YmdHis")."', ";
	$sql.= "memo		= '".$memo."' ";
	$insert=mysql_query($sql,get_db_conn());
	if($insert) {
		$sql = "SELECT SUM(buy_cnt) as bid_cnt FROM tblgongresult ";
		$sql.= "WHERE gong_seq='".$gdata->gong_seq."' ";
		$result=mysql_query($sql,get_db_conn());
		$row=mysql_fetch_object($result);
		$bid_cnt=(int)$row->bid_cnt;
		mysql_free_result($result);

		$sql = "UPDATE tblgonginfo SET ";
		$sql.= "bid_cnt		= ".$bid_cnt." ";
		$sql.= "WHERE gong_seq='".$gdata->gong_seq."' ";
		mysql_query($sql,get_db_conn());
	}
	echo "<html><head><title></title></head><body onload=\"alert('공동구매 참여 처리가 완료되었습니다.');location.href='gonggu.php?sort=".$sort."&block=".$block."&gotopage=".$gotopage."';\"></body></html>";exit;
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
var nowcnt=<?=$nowcnt?>;
function CheckForm() {
	if(document.form1.name.value.length==0) {
		alert("이름을 입력하세요.");
		document.form1.name.focus();
		return;
	}
	if(document.form1.email.value.length==0) {
		alert("사용하고 계신 이메일을 입력하세요.");
		document.form1.email.focus();
		return;
	}
	isMailChk = /^[^@ ]+@([a-zA-Z0-9\-]+\.)+([a-zA-Z0-9\-]{2}|net|com|gov|mil|org|edu|int)$/;
	if(!isMailChk.test(document.form1.email.value)) {
		alert("이메일 형식이 맞지않습니다.\n\n확인하신 후 다시 입력하세요.");
		document.form1.email.focus();
		return;
	}
	if(document.form1.tel.value.length==0) {
		alert("전화번호를 입력하세요.");
		document.form1.tel.focus();
		return;
	}
	if(document.form1.address.value.length==0) {
		alert("주소를 입력하세요.");
		document.form1.address.focus();
		return;
	}
	if(document.form1.buy_cnt.value.length==0 || document.form1.buy_cnt.value==0) {
		alert("주문수량을 입력하세요.");
		document.form1.buy_cnt.focus();
		return;
	}
	if(!IsNumeric(document.form1.buy_cnt.value)) {
		alert("주문수량은 숫자만 입력 가능합니다.");
		document.form1.buy_cnt.focus();
		return;
	}

	if(nowcnt<document.form1.buy_cnt.value) {
		alert("구매수량이 초과되었습니다.\n\n현재 남아있는 수량은 "+nowcnt+"개 입니다.");
		document.form1.buy_cnt.focus();
		return;
	}
	document.form1.mode.value="insert";
	document.form1.submit();
}

function change_cnt(gbn) {
	var cnt=document.form1.buy_cnt.value;
	if(gbn=="up") {
		if(nowcnt>cnt) {
			cnt++;
		}
	} else if(gbn=="dn") {
		if(cnt> 1) cnt--;
	}
	document.form1.buy_cnt.value=cnt;
}

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
else $delivery="배송료 ".number_format($row->deli_money)."원";

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
		<td height="2" bgcolor="#000000"></td>
	</tr>
	<tr>
		<td height="20"></td>
	</tr>
	<form name=form1 method=post action="<?=$_SERVER[PHP_SELF]?>">
	<input type=hidden name=mode>
	<input type=hidden name=block value="<?=$block?>">
	<input type=hidden name=gotopage value="<?=$gotopage?>">
	<input type=hidden name=sort value="<?=$sort?>">
	<input type=hidden name=type value="<?=$type?>">
	<input type=hidden name=seq value="<?=$seq?>">
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
				echo "<img src=\"".$gongguimagepath.$gdata->image2."\" border=\"0\" ";
				$size=GetImageSize($gongguimagepath.$gdata->image2);
				if(($size[0]>320 || $size[1]>320) && $size[0]>$size[1]) {
					echo " width=\"320\"";
				} else if($size[0]>320 || $size[1]>320) {
					echo " height=\"320\"";
				}
				echo "></td>";
			} else {
				echo "<img src=\"images/product_no_img.gif\" width=\"90\" height=\"90\" border=\"0\"></td>";
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
					<td>
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td><INPUT type=text name="buy_cnt" value="1" size="2" maxLength="8" onKeyUp="return strnumkeyup(this);" style="text-align:right;font-size:11px;BORDER:#DFDFDF 1px solid;BACKGROUND-COLOR:#F7F7F7;"></td>
						<td>
						<table cellpadding="0" cellspacing="0" border="0">
						<tr>
							<td><A HREF="javascript:change_cnt('up')"><IMG SRC="images/neroup.gif" border="0" hspace="3" vspace="3"></a></td>
						</tr>
						<tr>
							<td><A HREF="javascript:change_cnt('dn')"><IMG SRC="images/nerodown.gif" border="0" hspace="3" vspace="3"></a></td>
						</tr>
						</table>
						</td>
						<td width="100%">현재 남은 수량 : <b><?=$nowcnt?></b>개</td>
					</tr>
					</table>
					</td>
				</tr>
				<tr>
					<td><IMG SRC="images/detail_pointa.gif" border="0"></td>
					<td>남은시간</td>
					<td align="center">:</td>
					<td><input type=text name="finaltime" value="<?=$txtTime?>" size="25" style="background-color:#FFFFFF;border:none;color:#F02800;font-weight:bold;height:16;" readonly></td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td height="4" background="images/pdetail_skin1_titleline1.gif"></td>
			</tr>
			</table>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
		<tr>
			<td><img src="images/gongdetail_ordertitle.gif" border="0"></td>
		</tr>
<?
		$sql = "SELECT name,email,home_addr,home_tel FROM tblmember ";
		$sql.= "WHERE id='".$_ShopInfo->getMemid()."' ";
		$result=mysql_query($sql,get_db_conn());
		$row=mysql_fetch_object($result);
		mysql_free_result($result);

		$home_addr_temp=explode("=",$row->home_addr);
		$home_addr1=$home_addr_temp[0];
		$home_addr2=$home_addr_temp[1];
?>
		<tr>
			<td>
			<table cellpadding="0" cellspacing="0" width="100%">
			<col align="right" width="150" style="padding-right:5px;background-color:#F8F8F8;letter-spacing:-0.5pt;"></col>
			<col style="padding:5px;"></col>
			<tr>
				<td height="2" bgcolor="#000000" colspan="2"></td>
			</tr>
			<tr height="30">
				<td><font color="#333333"><b>회원아이디</b></font></td>
				<td style="BORDER-LEFT:#E3E3E3 1pt solid;"><b><font color="#FF3300"><?=$_ShopInfo->getMemid()?></font></b></td>
			</tr>
			<tr>
				<td height="1" colspan="2" bgcolor="#DDDDDD"></td>
			</tr>
			<tr>
				<td><font color="#333333"><b>이름</b></font></td>
				<td style="BORDER-LEFT:#E3E3E3 1pt solid;"><input type=text name="name" value="<?=$row->name?>" size="20" maxlength="20" class="input"></td>
			</tr>
			<tr>
				<td height="1" colspan="2" bgcolor="#DDDDDD"></td>
			</tr>
			<tr>
				<td><font color="#333333"><b>E-mail</b></font></td>
				<td style="BORDER-LEFT:#E3E3E3 1pt solid;"><input type=text name="email" value="<?=$row->email?>" size="40" maxlength="50" class="input"><BR><FONT color="#FF3300" style="font-size:8pt;letter-spacing:-0.5pt;">* 공구 일정이 끝나면 공구내용을 메일로 발송합니다. 메일기입에 주의하세요.</FONT></td>
			</tr>
			<tr>
				<td height="1" colspan="2" bgcolor="#DDDDDD"></td>
			</tr>
			<tr>
				<td><font color="#333333"><b>전화번호</b></font></td>
				<td style="BORDER-LEFT:#E3E3E3 1pt solid;"><input type=text name="tel" value="<?=$row->home_tel?>" size="20" maxlength="15" class="input"></td>
			</tr>
			<tr>
				<td height="1" colspan="2" bgcolor="#DDDDDD"></td>
			</tr>
			<tr>
				<td><font color="#333333"><b>주소</b></font></td>
				<td style="BORDER-LEFT:#E3E3E3 1pt solid;"><input type=text name="address" value="<?=$home_addr1." ".$home_addr2?>" size="70" maxlength="150" class="input" style="width=100%"></td>
			</tr>
			<tr>
				<td height="1" colspan="2" bgcolor="#DDDDDD"></td>
			</tr>
			<tr>
				<td><font color="#333333"><b>입금계좌정보</b></font></td>
				<td style="BORDER-LEFT:#E3E3E3 1pt solid;">
<?
				$tmp=explode("=",$_data->bank_account);
				$bank_account=$tmp[0];
				if (strlen($bank_account)>0) {
					$tok = explode(",",$bank_account);
					$count = count($tok);
					for($i=0;$i<$count;$i++) if(strlen($tok[$i])>0) echo $tok[$i]."<br>\n";
				}
?>
				</td>
			</tr>
			<tr>
				<td height="1" colspan="2" bgcolor="#DDDDDD"></td>
			</tr>
			<tr>
				<td><font color="#333333"><b>메모</b></font></td>
				<td style="BORDER-LEFT:#E3E3E3 1pt solid;"><textarea name="memo" maxlength="200" style="WIDTH:100%;HEIGHT:100px;padding:2px;font-size:9pt;color:333333;line-height:17px;border:#BDBDBD solid 1;"></TEXTAREA></td>
			</tr>
			<tr>
				<td height="1" colspan="2" bgcolor="#DDDDDD"></td>
			</tr>
			</table>
			</td>
		</tr>
		<tr>
			<td height="20"></td>
		</tr>
		<tr>
			<td align="center"><A HREF="javascript:CheckForm()"><IMG SRC="images/gonggu_ok.gif" border="0"></a><A HREF="gonggu_detail.php?seq=<?=$gdata->gong_seq?>&sort=<?=$sort?>&block=<?=$block?>&gotopage=<?=$gotopage?>"><IMG SRC="images/gonggu_cancel.gif" border="0" hspace="3"></a></td>
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
</form>
</table>
<?
if ($isEnd == 0) {
	echo "<script>window.onload = finaltimer;</script>";
}
?>

<? include ($Dir."lib/bottom.php") ?>

</BODY>
</HTML>