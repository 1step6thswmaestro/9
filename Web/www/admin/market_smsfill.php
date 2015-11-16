<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
INCLUDE ("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "ma-4";
$MenuCode = "market";
if (!$_usersession->isAllowedTask($PageCode)) {
	INCLUDE ("AccessDeny.inc.php");
	exit;
}
#########################################################

$sql = "SELECT id, authkey, return_tel FROM tblsmsinfo ";
$result=mysql_query($sql,get_db_conn());
if($row=mysql_fetch_object($result)){
	$return_tel = explode("-",$row->return_tel);
	$sms_id=$row->id;
	$sms_authkey=$row->authkey;
}
mysql_free_result($result);

$isdisabled="1";
$totcnt=0;
if(strlen($sms_id)==0 || strlen($sms_authkey)==0) {
	$onload="<script>alert('SMS 회원가입 및 충전 후 SMS 기본환경 설정에서\\n\\nSMS 아이디 및 인증키를 입력하시기 바랍니다.');</script>";
	$isdisabled="0";
} else {
	$smscountdata=getSmscount($sms_id, $sms_authkey);
	if(substr($smscountdata,0,2)=="OK") {
		$totcnt=substr($smscountdata,3);
	} else if(substr($smscountdata,0,2)=="NO") {
		$onload="<script>alert('SMS 회원 아이디가 존재하지 않습니다.\\n\\nSMS 기본환경 설정에서 SMS 아이디 및 인증키를 정확히 입력하시기 바랍니다.');</script>";
		$isdisabled="2";
	} else if(substr($smscountdata,0,2)=="AK") {
		$onload="<script>alert('SMS 회원 인증키가 일치하지 않습니다.\\n\\nSMS 기본환경 설정에서 인증키를 정확히 입력하시기 바랍니다.');</script>";
		$isdisabled="3";
	} else {
		$onload="<script>alert('SMS 서버와 통신이 불가능합니다.\\n\\n잠시 후 이용하시기 바랍니다.');</script>";
		$isdisabled="4";
	}
}

$smspayment=array();
$resdata=getSmspaylist();
if(substr($resdata,0,2)=="OK") {
	$tempdata=explode("=",$resdata);
	$smspayment=unserialize($tempdata[1]);
} else {
	echo "<html></head><body onload=\"alert('SMS 서버와 통신이 불가능합니다.\\n\\n잠시 후 이용하시기 바랍니다.');history.go(-1)\"></body></html>";exit;
}

?>

<? INCLUDE "header.php"; ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
<?if(preg_match("/^(0|2|3)$/",$isdisabled)){?>
function sms_join() {
	window.open("about:blank","smsjoin","width=450,height=460,scrollbars=no,status=yes");
	document.joinform.submit();
}
<?}else if($isdisabled=="1"){?>
function sms_login() {
	window.open("about:blank","smslogin","width=450,height=460,scrollbars=no,status=yes");
	document.loginform.submit();
}
<?}?>

function CheckForm() {
<?if($isdisabled=="1"){?>
	if(document.smsform.price.value.length==0) {
		alert("SMS머니 충전 사용료를 선택하세요.");
		return;
	}
	window.open("about:blank","smspayment","width=450,height=460,scrollbars=no,status=yes");
	document.smsform.submit();
<?}else if($isdisabled=="0"){?>
	alert("SMS 회원가입 후 SMS 머니 충전이 가능합니다.");
<?}else if($isdisabled=="2"){?>
	alert("SMS 회원 아이디가 존재하지 않습니다.\n\nSMS 기본환경 설정에서 SMS 아이디 및 인증키를 정확히 입력하시기 바랍니다.");
<?}else if($isdisabled=="3"){?>
	alert("SMS 회원 인증키가 일치하지 않습니다.\n\nSMS 기본환경 설정에서 인증키를 정확히 입력하시기 바랍니다.");
<?}else if($isdisabled=="4"){?>
	alert("SMS 서버와 통신이 불가능합니다.\n\n잠시 후 이용하시기 바랍니다.");
<?}?>
}

function change_money(price) {
<?if($isdisabled=="1"){?>
	document.smsform.price.value=price;
<?}?>
}

function smsfillinfo() {
<?if($isdisabled=="1"){?>
	window.open("market_smsfillinfopop.php","smsfillinfo","width=450,height=460,scrollbars=no,status=yes");
<?}else if($isdisabled=="0"){?>
	alert("SMS 회원가입 및 충전 후 SMS 기본환경 설정에서\n\nSMS 아이디 및 인증키를 입력하시기 바랍니다.");
<?}else if($isdisabled=="2"){?>
	alert("SMS 회원 아이디가 존재하지 않습니다.\n\nSMS 기본환경 설정에서 SMS 아이디 및 인증키를 정확히 입력하시기 바랍니다.");
<?}else if($isdisabled=="3"){?>
	alert("SMS 회원 인증키가 일치하지 않습니다.\n\nSMS 기본환경 설정에서 인증키를 정확히 입력하시기 바랍니다.");
<?}else if($isdisabled=="4"){?>
	alert("SMS 서버와 통신이 불가능합니다.\n\n잠시 후 이용하시기 바랍니다.");
<?}?>
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
			<? include ("menu_market.php"); ?>
			</td>

			<td></td>
			<td valign="top">




<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td height="29" colspan="3">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 마케팅지원 &gt; SMS 발송/관리  &gt; <span class="2depth_select">SMS 충전하기</span></td>
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
			<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post>
			<input type=hidden name=type>
			<tr>
				<td height="8"></td>
			</tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/market_smsfill_title.gif" ALT=""></TD>
					</tr><tr>
<TD width="100%" background="images/title_bg.gif" height="21"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height="3"></td>
			</tr>
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
					<TD width="100%" class="notice_blue">SMS 발송시 필요한 사용료를 충전합니다.</TD>
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
			<tr>
				<td height="20"></td>
			</tr>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="208" valign="top">
					<TABLE WIDTH=200 BORDER=0 CELLPADDING=0 CELLSPACING=0 align="center">
					<TR>
						<TD><IMG SRC="images/sms_top_01.gif" WIDTH=200 HEIGHT="30" ALT=""></TD>
					</TR>
					<TR>
						<TD height="90" background="images/sms_bg.gif">
						<table cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td width="190" align="center"><FONT color="white"><b><span style="font-size:12pt; letter-spacing:-1pt;">SMS</span><span style="font-size:12pt; letter-spacing:-2pt;"> 발송가능건수<br>  </span></FONT></b></td>
						</tr>
						<tr>
							<td width="190" height="45" align="center"><b><FONT style="FONT-SIZE: 19pt; LINE-HEIGHT: 30pt; FONT-FAMILY: 굴림" color="#FFFFCC" face="돋움"><span style="font-size:30pt; letter-spacing:-2pt;"><?=number_format($totcnt)?></span></b></FONT></td>
						</tr>
						</table>
						</TD>
					</TR>
					<TR>
						<TD height="26" background="images/sms_down_01.gif" align="center">&nbsp;</TD>
					</TR>
					<tr><td height=20></td></tr>
					<tr>
						<td align=center>
						<img src="images/btn_smsfill_view.gif" border="0" onclick="smsfillinfo();" style="cursor:hand;">
						</td>
					</tr>
					</TABLE>
					</td>
					<td width="20" valign="top">&nbsp;</td>
					<td  valign="top">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td  class="font_size">
						<img src="images/icon_point.gif" width="15" height="11" border="0">핸드폰 문자메세지 서비스는 선불제 서비스입니다. 
						<br><img src="images/icon_point.gif" width="15" height="11" border="0">충전금액은 발송건수에 따라 국내 <b><span class="font_orange" style="font-size:8pt;">최저 19원/건당(부과세 포함금액)</span></b>입니다.
						<br><img src="images/icon_point.gif" width="15" height="11" border="0">충전하신 SMS머니는 환불되지 않습니다.<br><img src="images/icon_point.gif" width="15" height="11" border="0">성공한 건수만 과금되며 실패한 건수에 대해서는 과금하지 않습니다.&nbsp;<br>
						</td>
					</tr>
					<tr>
						<td >&nbsp;</td>
					</tr>
					<tr>
						<td  style="padding-bottom:2pt;">
						<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
						<TR>
							<TD><IMG SRC="images/market_smsfill_stitle.gif" WIDTH="192" HEIGHT=31 ALT=""></TD>
							<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif">&nbsp;</TD>
							<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></TD>
						</TR>
						</TABLE>
						</td>
					</tr>
					<tr>
						<td>
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
						<TR>
							<TD background="images/table_top_line.gif" colspan="4" ></TD>
						</TR>
						<TR>
							<TD class="table_cell" align="center"  align="center">선택하기</TD>
							<TD class="table_cell1" align="center"  align="center"><FONT color=#3d3d3d><B>SMS발송건수</FONT></B></TD>
							<TD class="table_cell1" align="center"  align="center"><FONT color=#3d3d3d><B>사용금액</FONT></B></TD>
							<TD class="table_cell1" align="center"  align="center"><FONT color=#3d3d3d><B>사용단가</FONT></B></TD>
						</TR>
						<TR>
							<TD colspan="4" background="images/table_con_line.gif" width="532"></TD>
						</TR>
<?
						$default_money=0;
						$ii=0;
						for($i=0;$i<count($smspayment);$i++) {
							if($smspayment[$i]["used"]=="Y") {
								if($ii==0) {
									$default_money=$smspayment[$i]["money"];
								}
								echo "<TR>\n";
								echo "	<TD class=\"td_con2\" align=\"center\"  align=\"center\"><input type=radio name=smspayment value=\"".$smspayment[$i]["money"]."\" ".($ii==0?"checked":"")." onclick=\"change_money(".$smspayment[$i]["money"].")\" ".($isdisabled=="1"?"":"disabled")."></TD>\n";
								echo "	<TD class=\"td_con1\" align=\"center\"  align=\"center\">".number_format($smspayment[$i]["val"])."건</TD>\n";
								echo "	<TD class=\"td_con1\" align=\"center\"  align=\"center\"><FONT color=#FF8040><B>".number_format($smspayment[$i]["money"]*1.1)."원</B></FONT></TD>\n";
								echo "	<TD class=\"td_con1\" align=\"center\"  align=\"center\">".$smspayment[$i]["unit"]."원/1건</TD>\n";
								echo "</TR>\n";
								echo "<TR>\n";
								echo "	<TD colspan=\"4\" background=\"images/table_con_line.gif\" ></TD>\n";
								echo "</TR>\n";
								$ii++;
							}
						}
?>						
						<TR>
							<TD background="images/table_top_line.gif" colspan="4" width="532"></TD>
						</TR>
						</TABLE>
						</td>
					</tr>
					<tr>
						<td  align="center">

						<?if($isdisabled=="1"){?>

						<a href="javascript:CheckForm();"><img src="images/btn_smssave.gif" width="156" height="38" border="0" vspace="10" alt="SMS머니 충전하기"></a>
						<a href="javascript:sms_login();"><img src="images/btn_smsmembermodify.gif" width="156" height="38" border="0" vspace="10" alt="SMS 회원정보 수정"></a>

						<?}else if(preg_match("/^(0|2|3)$/",$isdisabled)){?>

						<a href="javascript:sms_join();"><img src="images/btn_smsmemberjoin.gif" width="156" height="38" border="0" vspace="10" alt="SMS 회원가입"></a>

						<?}?>

						</td>
					</tr>
					</table>
					</td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td height="20">&nbsp;</td>
			</tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/manual_top1.gif" WIDTH=15 height="45" ALT=""></TD>
					<TD><IMG SRC="images/manual_title.gif" WIDTH=113 height="45" ALT=""></TD>
					<TD width="100%" background="images/manual_bg.gif" height="35"></TD>
					<TD background="images/manual_bg.gif"></TD>
					<td background="images/manual_bg.gif"><IMG SRC="images/manual_top2.gif" WIDTH=18 height="45" ALT=""></td>
				</TR>
				<TR>
					<TD background="images/manual_left1.gif"></TD>
					<TD COLSPAN=3 width="100%" valign="top" bgcolor="white" style="padding-top:8pt; padding-bottom:8pt; padding-left:4pt;" class="menual_bg">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td ><span class="font_dotline">SMS 충전하기</span></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td  class="space_top">- SMS 서비스를 도입하시면 저렴한 비용으로 강력한 고객관리를 하실 수 있습니다.</td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td  class="space_top">- SMS 서비스는 고객관리 기능 확대로 기업매출 증가, 무상 솔루션 제공으로 시스템의 확장성 및 호환성을 확보하실 수 있습니다.</td>
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
			<tr>
				<td height="50"></td>
			</tr>
			</form>

<?
			$sms_host=getSmshost(&$sms_path);
?>
			<?if($isdisabled=="1"){?>
			<form name=smsform method=post action="http://<?=$sms_host.$sms_path?>/charge/charge.html" target="smspayment">
			<input type=hidden name=shopid value="<?=$sms_id?>">
			<input type=hidden name=enckey value="<?=getEncKey($sms_id)?>">
			<input type=hidden name=shopurl value=<?=getenv("HTTP_HOST")?>>
			<input type=hidden name=price value=<?=$default_money?>>
			</form>
			<form name=loginform method=get action="http://<?=$sms_host.$sms_path?>/member/login.html" target="smslogin">
			</form>
			<?}else if(preg_match("/^(0|2|3)$/",$isdisabled)){?>
			<form name=joinform method=post action="http://<?=$sms_host.$sms_path?>/member/member_join.html" target="smsjoin">
			<input type=hidden name=shopurl value="<?=$shopurl?>">
			</form>
			<?}?>

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