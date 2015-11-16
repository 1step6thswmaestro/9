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

$imagepath = $Dir.DataDir."shopimages/etc/";

$type=$_POST["type"];
$up_sns_ok=$_POST["up_sns_ok"];
$up_sns_reserve_type		=$_POST["up_sns_reserve_type"];
$up_sns_recomreserve		=$_POST["up_sns_recomreserve"];
$up_sns_recomreserve_type	=$_POST["up_sns_recomreserve_type"];
$up_sns_recomreserve_give_type	=$_POST["up_sns_recomreserve_give_type"];
$up_sns_memreserve			=$_POST["up_sns_memreserve"];
$up_sns_memreserve_type		=$_POST["up_sns_memreserve_type"];
$up_sns_memreserve_give_type		=$_POST["up_sns_memreserve_give_type"];
$snsBoardReserve = ($_POST["snsBoardReserve"]>0?$_POST["snsBoardReserve"]:0);



if($up_sns_reserve_type != "A")
{
	$up_sns_recomreserve_type="";
	$up_sns_memreserve_type="";
	$up_sns_recomreserve=0;
	$up_sns_memreserve=0;
	$up_sns_recomreserve_give_type="";
	$up_sns_memreserve_give_type="";
}
$up_sns_reserve_type = $up_sns_reserve_type."".$up_sns_recomreserve_type."".$up_sns_memreserve_type."".$up_sns_recomreserve_give_type."".$up_sns_memreserve_give_type;

if ($type=="up") {
	$sql = "UPDATE tblshopinfo SET ";
	$sql.= "sns_ok				= '".$up_sns_ok."', ";
	$sql.= "sns_reserve_type	= '".$up_sns_reserve_type."', ";
	$sql.= "sns_recomreserve	= '".$up_sns_recomreserve."', ";
	$sql.= "sns_memreserve		= '".$up_sns_memreserve."' ";
	//echo $sql;

	mysql_query($sql,get_db_conn());
	DeleteCache("tblshopinfo.cache");

	// 게시판 홍보 적립금
	$extra_conf_sql = "SELECT `value` FROM `extra_conf` WHERE `type` = 'tblshopinfo' AND `name` = 'snsBoardReserve' ";
	$extra_conf_res = mysql_query($extra_conf_sql,get_db_conn());
	if( mysql_num_rows($extra_conf_res) ) {
		$extra_conf_up_sql = "UPDATE `extra_conf` SET `value`='".$snsBoardReserve."' WHERE `type` = 'tblshopinfo' AND `name` = 'snsBoardReserve' ";
	} else{
		$extra_conf_up_sql = "INSERT `extra_conf` SET `type` = 'tblshopinfo', `name` = 'snsBoardReserve', `value`='".$snsBoardReserve."' ";
	}
	$extra_conf_up_res = mysql_query($extra_conf_up_sql,get_db_conn());
	mysql_free_result($extra_conf_res);
	mysql_free_result($extra_conf_up_res);

	$onload="<script>alert('SNS 설정이 완료되었습니다.');</script>\n";
}else if($type =="snsinfo"){
	//$arSnsType = array("f","t","m","k");
	$arSnsType = array("f","t","k");
	for($i=0;$i<sizeof($arSnsType);$i++){
		$type = $arSnsType[$i];
		$state = $_POST[$type."_state"];
		$appid = $_POST[$type."_appid"];
		$secret = $_POST[$type."_secret"];
		$icon = $_FILES[$type."_icon"];

		$sql = "SELECT COUNT(*) as cnt, icon_img FROM tblshopsnsinfo ";
		$sql.= "WHERE type = '{$type}'";
		$result = mysql_query($sql,get_db_conn());
		$row = mysql_fetch_object($result);
		mysql_free_result($result);
		$cnt=$row->cnt;
		$old_icon = $row->icon_img;

		if ($icon[name] && (strtolower(substr($icon[name],strlen($icon[name])-3,3))=="gif" || strtolower(substr($icon[name],strlen($icon[name])-3,3))=="jpg")) {
			if ($icon[size]<153600) {
				$icon[name]="icon_".$type.substr($icon[name],-4);
				if(strlen($old_icon)>0 && file_exists($imagepath.$old_icon)) {
					unlink($imagepath.$old_icon);
				}
				move_uploaded_file($up_image[tmp_name],$imagepath.$up_image[name]);
				chmod($imagepath.$up_image[name],0606);
			} else {
				$up_image[name] = $old_icon;
			}
		}


		if ($cnt==1) {
			$sql="UPDATE tblshopsnsinfo SET ";
			$sql.= "appid		= '".$appid."', ";
			$sql.= "secret		= '".$secret."', ";
			$sql.= "icon_img	= '".$up_image[name]."', ";
			$sql.= "state		= '".$state."' ";
			$sql.= "WHERE type = '".$type."'";
			$onload="<script>alert('sns 채널정보 수정이 완료되었습니다.');</script>";
		} else {
			$sql="INSERT INTO tblshopsnsinfo (type,appid,secret,icon_img,state) VALUES ('".$type."','".$appid."','".$secret."','".$up_image[name]."','".$state."')";
			$onload="<script>alert('sns 채널정보 등록이 완료되었습니다.');</script>";
		}
		mysql_query($sql,get_db_conn());
	}
}

$sql = "SELECT sns_ok,sns_reserve_type,sns_recomreserve,sns_memreserve ";
$sql.= "FROM tblshopinfo ";
$result=mysql_query($sql,get_db_conn());
if ($row=mysql_fetch_object($result)) {
	$sns_ok=$row->sns_ok;
	$sns_reserve_type=$row->sns_reserve_type;
	$sns_recomreserve=$row->sns_recomreserve;
	$sns_memreserve=$row->sns_memreserve;
	$arSnsType = explode("",$sns_reserve_type);
}

mysql_free_result($result);

$extra_conf_sql = "SELECT `value` FROM `extra_conf` WHERE `type` = 'tblshopinfo' AND `name` = 'snsBoardReserve' ";
$extra_conf_res = mysql_query($extra_conf_sql,get_db_conn());
if ($extra_conf_row=mysql_fetch_object($extra_conf_res)) {
	$snsBoardReserve=$extra_conf_row->value;
}
mysql_free_result($extra_conf_res);
?>

<? INCLUDE "header.php"; ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm() {
	document.form1.type.value="up";
	document.form1.submit();
}
function CheckForm2() {
	document.form2.type.value="snsinfo";
	document.form2.submit();
}
function rsvType(val){
	if(val =="A"){
		document.getElementById("snsTypeWrap").style.display = "block";
	}else{
		document.getElementById("snsTypeWrap").style.display = "none";
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
				<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 상점관리 &gt; 쇼핑몰 운영 설정 &gt; <span class="2depth_select">SNS 및 홍보적립금 설정</span></td>
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
					<TD><IMG SRC="images/shop_sns_title.gif"></TD>
					</tr><tr>
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
							<TD width="100%" class="notice_blue"><p>SNS를 통해 상품 홍보시 각종 혜택을 부여할 수 있습니다. 타 쇼핑몰과 차별화되는 SNS 홍보제도를 활용해 보세요.</p></TD>
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
							<TD><IMG SRC="images/shop_sns_stitle1.gif" HEIGHT=31 ALT=""></TD>
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
							<TD width="100%" class="notice_blue">1) SNS 사용시 상품 상세페이지에
							SNS알리기 영역이 나타납니다.</TD>
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
			<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method="post">
			<input type="hidden" name="type" />
			<tr>
				<td>
					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
						<TR>
							<TD colspan=2 background="images/table_top_line.gif"></TD>
						</TR>
						<TR>
							<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">SNS 적용여부 선택</TD>
							<TD class="td_con1"><input type=radio id="idx_sns_ok1" name=up_sns_ok value="Y" <?=($sns_ok =="Y")? "checked":""?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_sns_ok1>SNS 사용</label> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=radio id="idx_sns_ok2" name=up_sns_ok value="N" <?=($sns_ok !="Y")? "checked":""?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_sns_ok2>SNS 사용불가</label></TD>
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
							<TD><IMG SRC="images/shop_sns_stitle2.gif" HEIGHT=31 ALT=""></TD>
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
								1) My page > 적립금에서 추가된 적립금 확인 가능합니다.
								<br>2) <font style="color:#f00"><b>이메일, sms, sns</b></font>를 통한 회원별 고유 상품홍보 url을 통해 쇼핑몰에서 상품 주문시 배송완료되면 적립금이 추가 적립됩니다.
								<br>3) 적립금을 사용 하지 않으시려면 0원으로 표기 하시면 됩니다.
								<br>4) 적립금은 구매 배송 처리시 적립 됩니다.
								<br>5) 게시판 홍보 적립금은 홍보URL을 통해 최초 접근 1회지급 됩니다.
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
						<TR>
							<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">상품홍보 적립금 설정</TD>
							<TD class="td_con1" >
							<input type="radio" name="up_sns_reserve_type" id="up_sns_reserve_typeN" value="N" onclick="rsvType('N');" <?=($arSnsType[0] =="" || $arSnsType[0] == "N")? "checked":""?>><label for="up_sns_reserve_typeN">적립금 미지급</label>
							<input type="radio" name="up_sns_reserve_type" id="up_sns_reserve_typeA" value="A" onclick="rsvType('A');" <?=($arSnsType[0] == "A")? "checked":""?>><label for="up_sns_reserve_typeA">전체상품 일괄적용</label>
							<!-- <input type="radio" name="up_sns_reserve_type" id="up_sns_reserve_typeB" value="B" onclick="rsvType('B');" <?=($arSnsType[0] == "B")? "checked":""?>><label for="up_sns_reserve_typeB">각 상품별 적립금설정기준을 따름</label> -->
							</TD>
						</TR>
						<TR id="snsTypeWrap">
							<TD colspan="2">
								<table WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
									<col width="139"></col>
									<col width=""></col>
									<TR>
										<TD colspan="2"  background="images/table_con_line.gif"></TD>
									</TR>
									<TR>
										<TD class="table_cell" width="139px"><img src="images/icon_point2.gif" width="8" height="11" border="0">상품 홍보인 적립금</TD>
										<TD class="td_con1" >
											<input name="up_sns_recomreserve" value="<?=$sns_recomreserve?>" size=10 maxlength=6 class="input">
											원 <input type="hidden" name="up_sns_recomreserve_type" value="N">
											<? /*
											<select name="up_sns_recomreserve_type" class="select">
												<option value="N"<?=($arSnsType[1]!="Y")?" selected":""?>>적립금(￦)</option>
												<option value="Y"<?=($arSnsType[1]!="Y")?"":" selected"?>>적립률(%)</option>
											</select>
											*/?>
											<select name="up_sns_recomreserve_give_type" class="select">
												<option value="O"<?=($arSnsType[3]!="A")?" selected":""?>>1회지급</option>
												<option value="A"<?=($arSnsType[3]!="A")?"":" selected"?>>지속지급</option>
											</select>
										</TD>
									</TR>
									<TR>
										<TD colspan="2"  background="images/table_con_line.gif"></TD>
									</TR>
									<TR>
										<TD class="table_cell" width="139px"><img src="images/icon_point2.gif" width="8" height="11" border="0">상품 구매인 적립금</TD>
										<TD class="td_con1">
											<input name="up_sns_memreserve" value="<?=$sns_memreserve?>" size=10 maxlength=6 class="input">
											원 <input type="hidden" name="up_sns_memreserve_type" value="N">
											<? /*
											<select name="up_sns_memreserve_type" class="select">
												<option value="N"<?=($arSnsType[2]!="Y")?" selected":""?>>적립금(￦)</option>
												<option value="Y"<?=($arSnsType[2]!="Y")?"":" selected"?>>적립률(%)</option>
											</select>
											*/ ?>
											<select name="up_sns_memreserve_give_type" class="select">
												<option value="O"<?=($arSnsType[4]!="A")?" selected":""?>>1회지급</option>
												<option value="A"<?=($arSnsType[4]!="A")?"":" selected"?>>지속지급</option>
											</select>
										</TD>
									</TR>
									<TR>
										<TD colspan=2 background="images/table_top_line.gif"></TD>
									</TR>
									<TR>
										<TD class="table_cell" width="139px"><img src="images/icon_point2.gif" width="8" height="11" border="0">게시판 홍보인 적립금</TD>
										<TD class="td_con1">
											<input name="snsBoardReserve" value="<?=$snsBoardReserve?>" size=10 maxlength=6 class="input"> 원 (최초 1회 지급)
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
			<tr><td height="20"></td></tr>
			<tr>
				<td align="center"><a href="javascript:CheckForm();"><img src="images/botteon_save.gif" width="113" height="38" border="0"></a></td>
			</tr>
			<tr><td height="30"></td></tr>
			</form>
			</table>

			<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td>
						<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
							<TR>
								<TD><IMG SRC="images/shop_bitly_stitle3.gif"  HEIGHT=31 ALT="단축 도메인 연동 서비스"></TD>
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
								<TD COLSPAN=1 background="images/distribute_02.gif"></TD>
								<TD><IMG SRC="images/distribute_03.gif"></TD>
							</TR>
							<TR>
								<TD background="images/distribute_04.gif"></TD>
								<TD class="notice_blue"><IMG SRC="images/distribute_img.gif" ></TD>
								<TD width="100%" class="notice_blue">
									1) https://bitly.com 서비스를 통해 http://bit.ly/xxxxxxx 형식의 숏URL을 제공합니다.<br />
									2) <a href="https://bitly.com/" target="_blank">https://bitly.com/</a>에 가입 후 <a href="https://bitly.com/a/oauth_apps" target="_blank">https://bitly.com/a/oauth_apps</a>의 Generic Access Token을 생성/입력해 주시면 됩니다.
								</TD>
								<TD background="images/distribute_07.gif"></TD>
							</TR>
							<TR>
								<TD><IMG SRC="images/distribute_08.gif"></TD>
								<TD COLSPAN=1 background="images/distribute_09.gif"></TD>
								<TD><IMG SRC="images/distribute_10.gif"></TD>
							</TR>
						</TABLE>
					</td>
				</tr>
				<tr><td height="5"></td></tr>
				<tr>
					<td>
						<?
							if( $type == "bitlyUpdate" ) {
								$extra_conf_sql = "SELECT `value` FROM `extra_conf` WHERE `type` = 'bitlyShortUrl' AND `name` = 'accessToken' ";
								$extra_conf_res = mysql_query($extra_conf_sql,get_db_conn());
								if( mysql_num_rows($extra_conf_res) ) {
									$extra_conf_up_sql = "UPDATE `extra_conf` SET `value`='".$accessToken."' WHERE `type` = 'bitlyShortUrl' AND `name` = 'accessToken' ";
								} else{
									$extra_conf_up_sql = "INSERT `extra_conf` SET `type` = 'bitlyShortUrl', `name` = 'accessToken', `value`='".$accessToken."' ";
								}
								$extra_conf_up_res = mysql_query($extra_conf_up_sql,get_db_conn());
								mysql_free_result($extra_conf_res);
								mysql_free_result($extra_conf_up_res);
								$onload="<script>alert('Bitly's API Access Token 설정이 완료되었습니다.');</script>\n";
							}
							$extra_conf_sql = "SELECT `value` FROM `extra_conf` WHERE `type` = 'bitlyShortUrl' AND `name` = 'accessToken' ";
							$extra_conf_res = mysql_query($extra_conf_sql,get_db_conn());
							if ($extra_conf_row=mysql_fetch_object($extra_conf_res)) {
								$accessToken=$extra_conf_row->value;
							}
							mysql_free_result($extra_conf_res);
						?>
						<form name="bitlyForm" action="<?=$_SERVER[PHP_SELF]?>" method="POST">
						<input type=hidden name=type value="bitlyUpdate">
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
							<TR>
								<TD colspan=3 background="images/table_top_line.gif"></TD>
							</TR>
							<TR>
								<TD class="table_cell" width="140"><img src="images/icon_point2.gif" width="8" height="11" border="0">Bitly's API</TD>
								<TD class="td_con1" width="200">Generic Access Token</TD>
								<TD class="td_con1">
									<input type="text" name="accessToken" value="<?=$accessToken?>" style="width:80%;">
								</TD>
							</TR>
							<TR>
								<TD colspan="3" background="images/table_top_line.gif"></TD>
							</TR>
						</TABLE>
						</form>
					</td>
				</tr>
				<tr>
					<td align="center"><img src="images/botteon_save.gif" width="113" height="38" border="0" onclick="bitlyForm.submit();" style="cursor:pointer;"></td>
				</tr>
			</table>


<!-- //sns 기본설정 -->
			<form name=form2 action="<?=$_SERVER[PHP_SELF]?>" method=post>
			<input type=hidden name=type>
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td>
					<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
						<TR>
							<TD><IMG SRC="images/shop_sns_stitle3.gif"  HEIGHT=31 ALT=""></TD>
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
								<p style="font-weight:700;color:#FF0000;margin:0px;padding:0px;">
									* SNS API의 경우 각 SNS사에서 배포하는 API를 사용하며, 각 SNS사에서 API가 변경될 경우 사용하지 못할 수 있습니다.<br>
									&nbsp;&nbsp;이경우 해당서비스에 대한 A/S가 지연되거나 제외될 수 있으며, 해당 서비스를 사용하지 못할 수 있습니다.
								</p>
								1) sns 로그인 연동을 위해 각 SNS에서 앱ID와 앱 시크릿코드를 발급받아 등록해주세요.<br />
								2) Facebook : <a href="https://www.facebook.com/developers/createapp.php" target="_blank">https://www.facebook.com/developers/createapp.php</a><br />
								3) Twitter : <a href="https://dev.twitter.com/apps/new" target="_blank">https://dev.twitter.com/apps/new</a><br />
								4) Kakao : <a href="https://developers.kakao.com/" target="_blank">https://developers.kakao.com/</a><br />
								&nbsp;&nbsp;&nbsp;&nbsp;- 카카오톡의 경우 javascript key를 받아 적용하시면 됩니다.
								<!--<br>4) Me2day : <a href="http://me2day.net/me2/app" target="_blank">http://me2day.net/me2/app</a> 로그인 후 앱 키 발급받기-->
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
				<?
					$sql = "SELECT * FROM tblshopsnsinfo ";
					$result=@mysql_query($sql,get_db_conn());
					while($row=@mysql_fetch_object($result)) {
						$type=$row->type;
						$appid=$row->appid;
						$secret=$row->secret;
						$icon_img=$row->icon_img;
						$state=$row->state;
						$arSnsinfo["{$type}"][]=$appid;
						$arSnsinfo["{$type}"][]=$secret;
						$arSnsinfo["{$type}"][]=$icon_img;
						$arSnsinfo["{$type}"][]=$state;
					}
					mysql_free_result($result);
				?>
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
					<TR>
						<TD colspan=3 background="images/table_top_line.gif"></TD>
					</TR>
					<TR>
						<TD class="table_cell" width="140" rowspan="3"><img src="images/icon_point2.gif" width="8" height="11" border="0">페이스북</TD>
						<TD class="td_con1" width="100"style="border-bottom-width:1px; border-bottom-color:#E3E3E3; border-bottom-style:solid;">사용설정</TD>
						<TD class="td_con1" style="border-bottom-width:1px; border-bottom-color:#E3E3E3; border-bottom-style:solid;">
							<input type=radio id="f_state1" name="f_state" value="Y" <?=($arSnsinfo['f'][3] =="Y")? "checked":""?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=f_state1>사용</label><input type=radio id="f_state2" name=f_state value="N" <?=($arSnsinfo['f'][3] !="Y")? "checked":""?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=f_state2>사용안함</label>
						</TD>
					</TR>
					<TR>
						<TD class="td_con1" width="100"style="border-bottom-width:1px; border-bottom-color:#E3E3E3; border-bottom-style:solid;">앱 ID</TD>
						<TD class="td_con1" style="border-bottom-width:1px; border-bottom-color:#E3E3E3; border-bottom-style:solid;"><input type="text" name="f_appid" value="<?=$arSnsinfo['f'][0]?>" size=50 maxlength=40 class="input"></TD>
					</TR>
					<TR>
						<TD class="td_con1" width="100">앱 Secret</TD>
						<TD class="td_con1" ><input type="text" name="f_secret" value="<?=$arSnsinfo['f'][1]?>" size=50 maxlength=80 class="input"></TD>
					</TR>
					<!--
					<TR>
						<TD class="td_con1" width="100">앱 아이콘</TD>
						<TD class="td_con1" ><?=($arSnsinfo['f'][2])? "<img src=\"".$imagepath.$arSnsinfo['f'][2]."\" align=\"absmiddle\" width=\"23\">":"<img src=\"/images/design/icon_facebook_on.gif\" align=\"absmiddle\" width=\"23\">"?> <input type="file" name="f_icon"  size=50  class="input"></TD>
					</TR>
					-->
					<TR>
						<TD colspan="3"  background="images/table_con_line.gif"></TD>
					</TR>
				</TABLE>
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<TD class="table_cell" width="140" rowspan="3"><img src="images/icon_point2.gif" width="8" height="11" border="0">트위터</TD>
					<TD class="td_con1" width="100"style="border-bottom-width:1px; border-bottom-color:#E3E3E3; border-bottom-style:solid;">사용설정</TD>
					<TD class="td_con1" style="border-bottom-width:1px; border-bottom-color:#E3E3E3; border-bottom-style:solid;">
						<input type=radio id="t_state1" name="t_state" value="Y" <?=($arSnsinfo['t'][3] =="Y")? "checked":""?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=t_state1>사용</label><input type=radio id="t_state2" name=t_state value="N" <?=($arSnsinfo['t'][3] !="Y")? "checked":""?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=t_state2>사용안함</label>
					</TD>
				</TR>
				<TR>
					<TD class="td_con1" width="100"style="border-bottom-width:1px; border-bottom-color:#E3E3E3; border-bottom-style:solid;">앱 ID</TD>
					<TD class="td_con1" style="border-bottom-width:1px; border-bottom-color:#E3E3E3; border-bottom-style:solid;"><input type="text" name="t_appid" value="<?=$arSnsinfo['t'][0]?>" size=50 maxlength=40 class="input"></TD>
				</TR>
				<TR>
					<TD class="td_con1" width="100">앱 Secret</TD>
					<TD class="td_con1" ><input type="text" name="t_secret" value="<?=$arSnsinfo['t'][1]?>" size=50 maxlength=80 class="input"></TD>
				</TR>
				<!-- <TR>
					<TD class="td_con1" width="100">앱 아이콘</TD>
					<TD class="td_con1" ><?=($arSnsinfo['t'][2])? "<img src=\"".$imagepath.$arSnsinfo['t'][2]."\" align=\"absmiddle\" width=\"23\">":"<img src=\"/images/design/icon_twitter_on.gif\" align=\"absmiddle\" width=\"23\">"?> <input type="file" name="t_icon"  size=50  class="input"></TD>
				</TR> -->
				<TR>
					<TD colspan="3"  background="images/table_con_line.gif"></TD>
				</TR>
				</TABLE>
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
					<TR>
						<TD class="table_cell" width="140" rowspan="2"><img src="images/icon_point2.gif" width="8" height="11" border="0">카카오</TD>
						<TD class="td_con1" width="100" style="border-bottom-width:1px; border-bottom-color:#E3E3E3; border-bottom-style:solid;">사용설정</TD>
						<TD class="td_con1" style="border-bottom-width:1px; border-bottom-color:#E3E3E3; border-bottom-style:solid;">
							<input type=radio id="k_state1" name="k_state" value="Y" <?=($arSnsinfo['k'][3] =="Y")? "checked":""?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=k_state1>사용</label><input type=radio id="k_state2" name=k_state value="N" <?=($arSnsinfo['k'][3] !="Y")? "checked":""?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=k_state2>사용안함</label>
						</TD>
					</TR>
					<TR>
						<TD class="td_con1" width="100">Javascript Key</TD>
						<TD class="td_con1"><input type="text" name="k_secret" value="<?=$arSnsinfo['k'][1]?>" size=50 maxlength=80 class="input"></TD>
					</TR>
					<TR>
						<TD colspan="3"  background="images/table_top_line.gif"></TD>
					</TR>
				</TABLE>

				<!--
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<TD class="table_cell" width="140" rowspan="3"><img src="images/icon_point2.gif" width="8" height="11" border="0">미투데이</TD>
					<TD class="td_con1" width="100" style="border-bottom-width:1px; border-bottom-color:#E3E3E3; border-bottom-style:solid;">사용설정</TD>
					<TD class="td_con1" style="border-bottom-width:1px; border-bottom-color:#E3E3E3; border-bottom-style:solid;">
						<input type=radio id="m_state1" name="m_state" value="Y" <?=($arSnsinfo['m'][3] =="Y")? "checked":""?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=m_state1>사용</label><input type=radio id="m_state2" name=m_state value="N" <?=($arSnsinfo['m'][3] !="Y")? "checked":""?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=m_state2>사용안함</label>
					</TD>
				</TR>
				<TR>
					<TD class="td_con1" width="100"style="border-bottom-width:1px; border-bottom-color:#E3E3E3; border-bottom-style:solid;">앱 ID</TD>
					<TD class="td_con1" style="border-bottom-width:1px; border-bottom-color:#E3E3E3; border-bottom-style:solid;"><input type="text" name="m_appid" value="<?=$arSnsinfo['m'][0]?>" size=50 maxlength=40 class="input"></TD>
				</TR>
				<TR>
					<TD class="td_con1" width="100">앱 Secret</TD>
					<TD class="td_con1" style="border-bottom-width:1px; border-bottom-color:#E3E3E3; border-bottom-style:solid;"><input type="text" name="m_secret" value="<?=$arSnsinfo['m'][1]?>" size=50 maxlength=80 class="input"></TD>
				</TR>
				<!-- <TR>
					<TD class="td_con1" width="100">앱 아이콘</TD>
					<TD class="td_con1" ><?=($arSnsinfo['m'][2])? "<img src=\"".$imagepath.$arSnsinfo['m'][2]."\" align=\"absmiddle\" width=\"23\">":"<img src=\"/images/design/icon_me2day_on.gif\" align=\"absmiddle\" width=\"23\">"?> <input type="file" name="m_icon"  size=50  class="input"></TD>
				</TR> --//>

				<TR>
					<TD colspan=3 background="images/table_top_line.gif"></TD>
				</TR>
				</TABLE>
				-->

				</td>
			</tr>
			<tr><td height="30"></td></tr>
			<tr>
				<td align="center"><a href="javascript:CheckForm2();"><img src="images/botteon_save.gif" width="113" height="38" border="0"></a></td>
			</tr>
			</form>
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
rsvType('<?=$arSnsType[0]?>');
</script>

<? INCLUDE "copyright.php"; ?>