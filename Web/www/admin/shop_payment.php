<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
INCLUDE ("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "sh-3";
$MenuCode = "shop";
if (!$_usersession->isAllowedTask($PageCode)) {
	INCLUDE ("AccessDeny.inc.php");
	exit;
}
#########################################################

$type=$_POST["type"];
$up_newbank_account=$_POST["up_newbank_account"];
$up_oldbank_account=$_POST["up_oldbank_account"];
$up_payment_type=$_POST["up_payment_type"];
$up_card_payfee=$_POST["up_card_payfee"];
$up_bank_percent=$_POST["up_bank_percent"];
$up_card_splittype=$_POST["up_card_splittype"];
$up_card_splitmonth=$_POST["up_card_splitmonth"];
$up_card_splitprice=$_POST["up_card_splitprice"];
$up_auto_order_cancel=$_POST["up_auto_order_cancel"];
$up_bankmess=$_POST["up_bankmess"];
$up_cardmess=$_POST["up_cardmess"];
$up_saletype=$_POST["up_saletype"];
$up_bank_miniprice=$_POST["up_bank_miniprice"];
$up_card_miniprice=$_POST["up_card_miniprice"];


$chagepwd = isset($_POST['changepwd'])? trim($_POST['changepwd']):"";

// 현금할인율
if ($up_saletype=="-" && $up_bank_percent!=0) $up_bank_percent+=50;
if ($up_bank_percent!=0) $up_card_payfee = "-".$up_bank_percent;


#### 나이스페이 취소시 비밀번호 가져오기
$cancelpwdSQL = "SELECT value FROM extra_conf WHERE type = 'payment' AND name='cancel' ";
if(false !== $cancelpwdRes = mysql_query($cancelpwdSQL,get_db_conn())){
	$cancelNum = mysql_num_rows($cancelpwdRes);
	$cancelPWD = mysql_result($cancelpwdRes,0,0);
	if($cancelNum> 0){
		mysql_free_result($cancelpwdRes);
	}
}

#### PG정보 가져오기
$_ShopInfo->getPgdata();
$pg_info = GetEscrowType($_data->card_id);

$pgcorp = trim($pg_info['PG']);

if($pgcorp != "E"){
	$display = 'style="display:none"';
}

if ($type=="up") {
	$sql = "UPDATE tblshopinfo SET ";
	$sql.= "bank_miniprice			= '".$up_bank_miniprice."', ";
	$sql.= "card_miniprice			= '".$up_card_miniprice."', ";
	$sql.= "payment_type		= '".$up_payment_type."', ";
	$sql.= "card_payfee		= '".$up_card_payfee."', ";
	$sql.= "bank_account		= '".$up_oldbank_account."=".$up_bankmess."=".$up_cardmess."', ";
	$sql.= "card_splittype		= '".$up_card_splittype."', ";
	$sql.= "card_splitmonth		= '".$up_card_splitmonth."', ";
	$sql.= "card_splitprice		= '".$up_card_splitprice."', ";
	$sql.= "auto_order_cancel	= '".$up_auto_order_cancel."' ";
	$result = mysql_query($sql,get_db_conn());
	DeleteCache("tblshopinfo.cache");

	$log_content = "## 결제관련설정 ## - 여부:$up_card_splittype 개월수:$up_card_splitmonth 금액:$up_card_splitprice 카드수수료 : $up_card_payfee(마이너스이면 현금할인율임) $up_payment_type 최소주문가격: $bank_miniprice 카드취소주문가격 : $card_miniprice 자동결제취소 : $auto_order_cancel";
	ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);

	if($cancelNum > 0){
		if(strlen($chagepwd)>0 && $pgcorp == "E"){
			$cancelSQL = "UPDATE extra_conf SET value='".$chagepwd."' WHERE type='payment' AND name='cancel' ";
			@mysql_query($cancelSQL,get_db_conn());
		}
	}else{
		if(strlen($chagepwd)>0 && $pgcorp == "E"){
			$cancelSQL = "INSERT INTO extra_conf SET type='payment', name='cancel', value='".$chagepwd."' ";
			@mysql_query($cancelSQL,get_db_conn());
		}
	}
	$onload="<script> alert('결제관련 설정이 완료되었습니다.');location.replace('./shop_payment.php');</script>";
} else if ($type=="add") {
	if (strlen(trim($up_newbank_account))>0) {
		$sql = "SELECT bank_account FROM tblshopinfo ";
		$result = mysql_query($sql,get_db_conn());
		if ($row=mysql_fetch_object($result)) {
			if (strlen($row->bank_account)>0){
			   $arbank_account=explode("=",$row->bank_account);
			   $sql = "UPDATE tblshopinfo SET ";
			   $sql.= "bank_account='";
			   if(strlen($arbank_account[0])>0) {
				   $sql.=$arbank_account[0].",";
			   }
			   $sql.=$up_newbank_account."=".$arbank_account[1]."=".$arbank_account[2]."'";
			} else {
				$sql = "UPDATE tblshopinfo SET bank_account = '".$up_newbank_account."' ";
			}
			mysql_query($sql,get_db_conn());
			DeleteCache("tblshopinfo.cache");
			$onload = "<script> alert('무통장 입금 신규 계좌 추가가 완료되었습니다.'); </script>";
		}
		mysql_free_result($result);
	}
} else if ($type=="del") {
	if (strlen(trim($up_newbank_account))>0) {
		$sql = "SELECT bank_account FROM tblshopinfo ";
		$result = mysql_query($sql,get_db_conn());
		if ($row=mysql_fetch_object($result)) {
			if (strlen($row->bank_account)>0) {
				$bank_account=explode("=",$row->bank_account);
				$temp = $bank_account[0];
				$tok = strtok($temp,",");
				$count = 0; $temp2="";
				while ($tok) {
					if ($up_newbank_account!=$tok) {
						if ($count==0) $temp2=$tok;
						else $temp2=$temp2.",".$tok;
						$count++;
					}
					$tok = strtok(",");
				}
			}
			$sql = "UPDATE tblshopinfo SET ";
			$sql.= "bank_account = '".$temp2."=".$bank_account[1]."=".$bank_account[2]."' ";
			mysql_query($sql,get_db_conn());
			DeleteCache("tblshopinfo.cache");
			$onload="<script> alert('선택하신 무통장 입금계좌 삭제가 완료되었습니다.'); </script>";
		}
	}
}else if($type == "pwd"){
	if($cancelNum > 0){
		if(strlen($chagepwd)>0 && $pgcorp == "E"){
			$cancelSQL = "UPDATE extra_conf SET value='".$chagepwd."' WHERE type='payment' AND name='cancel' ";
			if(mysql_query($cancelSQL,get_db_conn())){
				echo "<script>alert(\"결제취소 비밀번호가 정상적으로 수정되었습니다..\");location.replace('./shop_payment.php');</script>";
			}else{
				echo "<script>alert(\"네트워크 오류로 처리되지 않았습니다.\\n잠시 후 다시 시도해 주세요.\");location.replace('./shop_payment.php');</script>";
			}
		}
	}else{
		if(strlen($chagepwd)>0 && $pgcorp == "E"){
			$cancelSQL = "INSERT INTO extra_conf SET type='payment', name='cancel', value='".$chagepwd."' ";
			if(mysql_query($cancelSQL,get_db_conn())){
				echo "<script>alert(\"결제취소 비밀번호가 정상적으로 등록되었습니다.\");location.replace('./shop_payment.php');</script>";
			}else{
				echo "<script>alert(\"네트워크 오류로 처리되지 않았습니다.\\n잠시 후 다시 시도해 주세요.\");location.replace('./shop_payment.php');</script>";
			}
		}
	}
}


$sql = "SELECT bank_account,payment_type,deli_basefee,deli_miniprice,card_payfee,card_splittype, ";
$sql.= "card_splitmonth, card_splitprice,card_miniprice,bank_miniprice, auto_order_cancel FROM tblshopinfo ";
$result=mysql_query($sql,get_db_conn());
if ($row=mysql_fetch_object($result)) {
	$arbank_account=explode("=",$row->bank_account);
	$bank_account=$arbank_account[0];
	$bankmess=$arbank_account[1];
	$cardmess=$arbank_account[2];
	$payment_type = $row->payment_type;
	$deli_basefee = $row->deli_basefee;
	$deli_miniprice = $row->deli_miniprice;
	$card_payfee = $row->card_payfee;
	$bank_percent = 0;
	$card_splittype = $row->card_splittype;
	$card_splitmonth = $row->card_splitmonth;
	$card_splitprice = $row->card_splitprice;
	$card_miniprice = $row->card_miniprice;
	$bank_miniprice = $row->bank_miniprice;	
	$auto_order_cancel = $row->auto_order_cancel;	

	if ($card_payfee<=0) { 
		$saletype="+";
		$bank_percent = abs($row->card_payfee); 
		if($bank_percent>50){
			$bank_percent-=50;
			$saletype="-";
		}
		$card_payfee=0; 
	}
}
mysql_free_result($result);
if($card_payfee < 1) $card_payfee = 0;

${"check_payment_type".$payment_type} = "checked";
${"check_card_splittype".$card_splittype} = "checked";




?>

<? INCLUDE "header.php"; ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm() {
	var temp;
	temp = parseInt(document.form1.up_card_payfee.value);
	temp2 = parseInt(document.form1.up_bank_percent.value);
	if (document.form1.up_card_splitprice.value<50000) {
		alert("무이자 가능금액은 50,000원 이상입니다.(할부는 5만원이상일때 가능)");
		document.form1.up_card_splitprice.focus();
		return;
	}
	if(!(-1 < temp && temp < 100)) {
		alert("카드수수료란에는 0부터 99까지의 숫자만 가능합니다.");
		document.form1.up_card_payfee.focus();
		return;
	}
	if(!(-1 < temp2 && temp2 < 51)) {
		alert("현금결제 수수료 할인/적립율에는 0부터 50까지의 숫자만 가능합니다.");
		document.form1.up_bank_percent.focus();
		return;
	}
	if (temp!=0 && temp2!=0) {
		alert("카드결제 수수료 추가와 현금결제 할인/적립 정책은 동시사용 불가합니다.");
		return;
	}
	document.form1.type.value="up";
	document.form1.submit();
}

function AccountDel(account) {
	document.form1.up_newbank_account.value=account;
	document.form1.type.value="del";
	document.form1.submit();
}

function AccountAdd(){
	document.form1.up_newbank_account.value = document.form1.up_newbank_account1.value;
	if (document.form1.up_newbank_account2.value.length>0) {
		document.form1.up_newbank_account.value += " (예금주:" + document.form1.up_newbank_account2.value + ")";
	}
	if (document.form1.up_newbank_account.value.length==0) {
		alert("은행계좌번호를 입력하세요.");
		return;
	}
	document.form1.type.value="add";
	document.form1.submit();
}

function checkmess(){
	if(!confirm('결제업체와 무이자할부서비스 계약을 먼저 하셔야 합니다.\n계약이 안된 상태에서 무이자 적용시 결제에러가 날 수 있습니다.\n무이자 할부 계약이 되어계시면 [확인]버튼을 눌러주셔야 적용됩니다.\n[취소]버튼을 누르시면 할부 무이자 안함으로 변경됩니다.')){
		document.form1.up_card_splittype[0].checked=true; 
	}
}
function changePasswd(val){
	var payForm = document.form1;
	if(payForm.changepwd.value == "" || payForm.changepwd.value == null){
		alert("취소 비밀번호를 입력하세요");
		payForm.changepwd.focus();
		return;
	}else{
		if(val != "" && val != 'undefined' && val != null && val=="pwd"){
			if(confirm("비밀번호를 수정하시겠습니까?")){
				payForm.type.value = val;
				payForm.submit();
				return;
			}else{
				return;
			}
		}else{
			alert("필수값이 전달되지 않았습니다.\n다시 시도해 주시기 바랍니다.");
			location.replace("./shop_payment.php");
			return;
		}
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
							<col width="198"></col>
							<col width="10"></col>
							<col width=></col>
							<tr>
								<td valign="top"  background="images/leftmenu_bg.gif"><? include ("menu_shop.php"); ?></td>
								<td></td>
								<td valign="top">
									<table cellpadding="0" cellspacing="0" width="100%">
										<tr>
											<td height="29" colspan="3">
												<table cellpadding="0" cellspacing="0" width="100%">
													<tr>
														<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 상점관리 &gt; 쇼핑몰 운영 설정 &gt; <span class="2depth_select">상품 결제관련 기능설정</span></td>
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
													<tr>
														<td height="8"></td>
													</tr>
													<tr>
														<td>
															<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
																<TR>
																	<TD><IMG SRC="images/shop_payment_title.gif"  ALT=""></TD>
																</tr>
																<tr>
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
																	<TD width="100%" class="notice_blue"><p>현금/신용카드 결제시 할인율, 최소구매액, 무이자할부, 신용카드 수수료등을 설정할 수 있습니다.</p></TD>
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
														<td>
															<form name="form1" action="<?=$_SERVER[PHP_SELF]?>" method="post">
																<input type="hidden" name="type">
																<input type="hidden" name="up_newbank_account">
																<input type="hidden" name="up_oldbank_account" value="<?=$bank_account?>">
																<table cellpadding="0" cellspacing="0" width="100%" border="0">
																	<tr>
																		<td height="10"></td>
																	</tr>
																	<tr>
																		<td><IMG SRC="images/shop_payment_stitle1.gif" border="0"></td>
																	</tr>
																	<tr>
																		<td height="5"></td>
																	</tr>
																	<tr>
																		<td>
																			<table cellpadding="0" cellspacing="0" width="100%">
																				<TR>
																					<TD bgcolor="#B9B9B9" height="1"></TD>
																				</TR>
																				<tr>
																					<td>
																						<table cellpadding="0" cellspacing="0" width="100%">
																							<col width="150"></col>
																							<col width=""></col>
																							<TR>
																								<TD class="table_cell"><b><img src="images/icon_point2.gif" border="0"></b>결제방법 선택</TD>
																								<TD class="td_con1">
																									<input type=radio id="idx_payment_type1" name=up_payment_type value="Y" <?=$check_payment_typeY?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_payment_type1>신용카드+온라인결제</label>
																									<input type=radio id="idx_payment_type2" name=up_payment_type value="C" <?=$check_payment_typeC?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_payment_type2>신용카드결제만 가능</label>
																									<input type=radio id="idx_payment_type3" name=up_payment_type value="N" <?=$check_payment_typeN?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_payment_type3>온라인결제만 가능</label>
																								</TD>
																							</TR>
																						</table>
																					</td>
																				</tr>
																				<TR>
																					<TD bgcolor="#B9B9B9" height="1"></TD>
																				</TR>
																			</table>
																		</td>
																	</tr>
																	<tr>
																		<td height="10"></td>
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
																					<TD width="100%" class="notice_blue">
																						<p>
																						1) 카드결제는 지불중회사와 계약 후 지불아이디(사이트코드)를 회사에 알려주셔야 사용 가능합니다.<br />
																						2) 지불중계회사는 적용 가능한 회사만 지원됩니다.(회사 홈페이지 참조)
																						</p>
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
																	<tr>
																		<td height="30"></td>
																	</tr>
																	<!-- 비밀번호 박스-->
																	<tr <?=$display?>>
																		<td>
																			<table cellpadding="0" cellspacing="0" width="100%">
																				<tr>
																					<td><IMG SRC="images/paycancel_title.gif" border="0" alt="나이스페이 결제취소 비밀번호" /></td>
																				</tr>
																				<tr>
																					<td height="5"></td>
																				</tr>
																				<tr>
																					<td>
																						<table cellpadding="0" cellspacing="0" width="100%">
																							<TR>
																								<TD class="notice_blue" colspan="2">
																									<div style="margin-left:24px; margin-bottom:10px;">
																										<p style="color:red">
																											1) 해당 취소 비밀번호는 나이스페이 신용카드 결제취소 연동처리시 반드시 필요합니다.<br />
																											2) 해당 취소 비밀번호는 나이스페이와 자동연동되지 않으므로, 나이스페이 정산관리자에서 취소비밀번호 변경시 반드시 동일한 정보로 수정되어야 합니다.<br />
																											&nbsp;&nbsp;&nbsp;&nbsp;(나이스페이 결제취소 비밀번호 설정 : 정산관리자 페이지 > 가맹점 정보 > 기본정보)<br />
																											3) 나이스페이 취소비밀번호와 일치하지 않을 경우 쇼핑몰 상점관리자에서 카드취소가 되지 않습니다.<br />
																											4) 취소비밀번호 입력 후 [비밀번호 저장] 버튼을 반드시 눌러주셔야 설정값이 저장됩니다.
																										</p>
																									</div>
																								</TD>
																							</TR>
																							<TR>
																								<TD bgcolor="#B9B9B9" height="1"></TD>
																							</TR>
																							<tr>
																								<td>
																									<table cellpadding="0" cellspacing="0" width="100%">
																										<col width="150"></col>
																										<col width=""></col>
																										<TR>
																											<TD class="table_cell"><b><img src="images/icon_point2.gif" border="0" title=""></b>결제취소 비밀번호</TD>
																											<TD class="td_con1">
																												<input type="password" name="changepwd" value="<?=$cancelPWD?>"/> <a href="javascript:changePasswd('pwd');"><img src="images/btn_savepass.gif" align="absmiddle" border="0" alt="비밀번호 저장" /></a>
																											</TD>
																										</TR>
																									</table>
																								</td>
																							</tr>
																							<TR>
																								<TD bgcolor="#B9B9B9" height="1"></TD>
																							</TR>
																						</table>
																					</td>
																				</tr>
																				<tr>
																					<td height="30"></td>
																				</tr>
																			</table>
																		</td>
																	</tr>
																	<!--비밀번호-->
																	
																	<tr>
																		<td>
																			<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
																				<TR>
																					<TD><IMG SRC="images/shop_payment_stitle2.gif" WIDTH="192" HEIGHT=31 ALT=""></TD>
																					<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif">&nbsp;</TD>
																					<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></TD>
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
																					<TD width="100%" class="notice_blue"><p>1) 무이자 할부에 대한 추가수수료는 상점 부담입니다.(기본 카드결제수수료+무이자할부 수수료)<br>
																					2) 무이자 할부는 지불중계회사와 별도의 계약 후 적용됩니다.(지불중계회사에서 제공하는 관리메뉴에서 신청 및 설정)<br>
																					3) 무이자 할부 가능 카드, 무이자 개월수에 대한 추가수수료 차이등은 해당 지불중계회사의 안내를 받으시면 됩니다.<br>
																					4) 카드사마다 자체 무이자 지원행사를 하는 경우에는 지불중계회사의 관리메뉴에서 무이자설정 종료 무이자 할부 안함으로 선택.
																					</p></TD>
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
																		<td height=3></td>
																	</tr>
																	<tr>
																		<td>
																			<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
																				<TR>
																					<TD colspan=2 background="images/table_top_line.gif"></TD>
																				</TR>
																				<TR>
																					<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">무이자 할부 여부</TD>
																					<TD class="td_con1" ><input type=radio id="idx_card_splittype1" name=up_card_splittype value="N" <?=$check_card_splittypeN?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_card_splittype1>무이자 할부 사용안함</label><BR>
																					<input type=radio id="idx_card_splittype2" name=up_card_splittype value="Y" <?=$check_card_splittypeY?> onclick="checkmess()"><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_card_splittype2>무이자 할부 적용(상점부담,<font color=red>전체상품적용</font>)</label><BR>
																					<!--<input type=radio id="idx_card_splittype3" name=up_card_splittype value="O" <?=$check_card_splittypeO?> onclick="checkmess()"><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_card_splittype3>무이자 할부 적용(상점부담,<font color=red>개별상품적용</font> → <B>[판매상품신규등록/관리]</B>에서 체크)</label>--></TD>
																				</TR>
																				<TR>
																					<TD colspan="2" background="images/table_con_line.gif"></TD>
																				</TR>
																					<TR>
																						<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">무이자 가능 개월수</TD>
																						<TD class="td_con1" >
																							<select name=up_card_splitmonth class="select">
																								<?
																									for ($i=3;$i<=12;$i++) {
																									echo "<option value='$i'";
																									if ($i==$card_splitmonth) echo " selected";
																									echo ">".$i."개월\n";
																									}
																								?>
																							</select> 이내<FONT color=#0054a6><BR> </FONT><span class="font_blue">* 3개월로 선택시 3개월만 무이자, 나머지 일반할부<BR> * 지불중계회사 관리메뉴에서 카드마다 할부개월수 선택가능, 단, 선택한 할부개월수 외엔 모두 일반할부<BR>* 지불중계회사의 관리메뉴와 상점의 무이자 가능 개월수를 동일하게 설정</span>
																						</TD>
																					</TR>
																				<TR>
																					<TD colspan="2" background="images/table_con_line.gif"></TD>
																				</TR>
																				<TR>
																					<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">무이자 가능금액</TD>
																					<TD class="td_con1" ><input type=text name=up_card_splitprice value="<?=$card_splitprice?>" size=7 maxlength=7 class="input_selected"> 이상(콤마제외)</TD>
																				</TR>
																				<TR>
																					<TD colspan=2 background="images/table_top_line.gif"></TD>
																				</TR>
																			</TABLE>
																		</td>
																	</tr>
																	<tr>
																		<td height="30"></td>
																	</tr>
																	<tr>
																		<td>
																			<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
																				<TR>
																					<TD><IMG SRC="images/shop_payment_stitle3.gif" WIDTH="192" HEIGHT=31 ALT=""></TD>
																					<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif">&nbsp;</TD>
																					<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></TD>
																				</TR>
																			</TABLE>
																		</td>
																	</tr>
																	<tr>
																		<td height=3></td>
																	</tr>
																	<tr>
																		<td>
																			<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
																				<TR>
																					<TD background="images/table_top_line.gif" colspan="2"></TD>
																				</TR>
																				<TR>
																					<TD class="table_cell" align="center">은행 및 계좌번호</TD>
																					<TD class="table_cell1" align="center">선택계좌삭제</TD>
																				</TR>
																				<TR>
																					<TD colspan="2" background="images/table_con_line.gif"></TD>
																				</TR>
																					<?
																						if (strlen($bank_account)>0) {
																							$temp = $bank_account;
																							$tok = strtok($temp,",");
																							while ($tok) {
																								echo "<TR><TD class=\"td_con2\">$tok</td>\n";
																								echo "<TD class=\"td_con1\" align=\"center\"><a href=\"javascript:AccountDel('$tok');\"><img src=\"images/btn_delet.gif\" width=\"108\" height=\"25\" border=\"0\"></a></td></tr>\n";
																								echo "<TR><TD colspan=\"2\" background=\"images/table_con_line.gif\"></TD></TR>";
																								$tok = strtok(",");
																							}
																						} else {
																							echo "<TR><TD class=\"td_con2\" colspan=2 align=center>등록된 계좌정보가 없습니다.</td></tr>\n";
																							echo "<TR><TD colspan=\"2\" background=\"images/table_con_line.gif\"></TD></TR>";
																						}
																					?>
																				<TR>
																					<TD background="images/table_top_line.gif" colspan="2"></TD>
																				</TR>
																			</TABLE>
																		</td>
																	</tr>
																	<tr>
																		<td height=2></td>
																	</tr>
																	<tr>
																		<td>
																			<table cellpadding="0" cellspacing="0" width="100%">
																				<tr>
																					<td  bgcolor="#EDEDED" style="padding:4pt;">
																						<table cellpadding="0" cellspacing="0" width="100%" bgcolor="white">
																							<tr>
																								<td width="100%">
																									<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
																										<TR>
																											<TD  colspan="2" height="35" background="images/blueline_bg.gif"><p align="center"><b><font color="#0099CC">계좌번호 입력하기</font></b></TD>
																										</TR>
																										<TR>
																											<TD colspan="2" background="images/table_con_line.gif"></TD>
																										</TR>
																										<TR>
																											<TD width="148" class="table_cell"><b><img src="images/icon_point2.gif" width="8" height="11" border="0">은행 및 계좌번호</b></TD>
																											<TD  class="td_con1"><input type=text name=up_newbank_account1 size=27 maxlength=50 class="input">&nbsp;&nbsp;<span class="font_orange">* 예) 00은행 123-4567-8910 방식으로 입력</span></TD>
																										</TR>
																										<TR>
																											<TD colspan="2" background="images/table_con_line.gif"></TD>
																										</TR>
																										<TR>
																											<TD width="148" class="table_cell"><b><img src="images/icon_point2.gif" width="8" height="11" border="0">예금주</b></TD>
																											<TD  class="td_con1">
																												<table cellpadding="0" cellspacing="0" width="100%">
																													<tr>
																														<td width="569"><input type=text name=up_newbank_account2 size=12 maxlength=30 class="input"> <a href="javascript:AccountAdd();"><img src="images/btn_bank.gif" width="105" height="26" border="0" align=absmiddle></a></td>
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
																			</table>
																		</td>
																	</tr>
																	<tr>
																		<td height="30"></td>
																	</tr>
																	<tr>
																		<td>
																			<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
																				<TR>
																					<TD><IMG SRC="images/shop_payment_stitle4.gif" WIDTH="192" HEIGHT=31 ALT=""></TD>
																					<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif">&nbsp;</TD>
																					<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></TD>
																				</TR>
																			</TABLE>
																		</td>
																	</tr>
																	<tr>
																		<td style="padding-top:3pt; padding-bottom:3pt;">
																			<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
																				<TR>
																					<TD><IMG SRC="images/distribute_01.gif"></TD>
																					<TD COLSPAN=2 background="images/distribute_02.gif"></TD>
																					<TD><IMG SRC="images/distribute_03.gif"></TD>
																				</TR>
																				<TR>
																					<TD background="images/distribute_04.gif"></TD>
																					<TD class="notice_blue"><IMG SRC="images/distribute_img.gif" ></TD>
																					<TD width="100%" class="notice_blue"><p>1) 결제창의 무통장 입금 기본안내 문구 <b>(반드시 주문자 성함으로 입금)</b>를 원하는 문구로 변경할 수 있습니다.<br>2) 결제창의 신용카드 결제 기본안내 문구 <b>[비할인판매가]</b>를 원하는 문구로 변경할 수 있습니다.</p></TD>
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
																		<td>
																			<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
																				<TR>
																					<TD colspan=2 background="images/table_top_line.gif"></TD>
																				</TR>
																				<TR>
																					<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">무통장 입금<br>&nbsp;&nbsp;안내문구 변경</TD>
																					<TD class="td_con1" ><input type=text name=up_bankmess value="<?=trim($bankmess)?>" size=80 maxlength=80 class="input" style=width:100%></TD>
																				</TR>
																				<TR>
																					<TD colspan="2" background="images/table_con_line.gif"></TD>
																				</TR>
																				<TR>
																					<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">신용카드 결제<br>&nbsp;&nbsp;안내문구 변경</TD>
																					<TD class="td_con1" ><input type=text name=up_cardmess value="<?=trim($cardmess)?>" size=50 maxlength=50 class="input" style=width:100%></TD>
																				</TR>
																				<TR>
																					<TD colspan=2 background="images/table_top_line.gif"></TD>
																				</TR>
																			</TABLE>
																		</td>
																	</tr>
																	<tr>
																		<td height="30"></td>
																	</tr>
																	<tr>
																		<td>
																			<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
																				<TR>
																				<TD><IMG SRC="images/shop_payment_stitle5.gif" WIDTH="192" HEIGHT=31 ALT=""></TD>
																				<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif">&nbsp;</TD>
																				<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></TD>
																				</TR>
																			</TABLE>
																		</td>
																	</tr>
																	<tr>
																		<td style="padding-top:3pt; padding-bottom:3pt;">
																			<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
																				<TR>
																					<TD><IMG SRC="images/distribute_01.gif"></TD>
																					<TD COLSPAN=2 background="images/distribute_02.gif"></TD>
																					<TD><IMG SRC="images/distribute_03.gif"></TD>
																				</TR>
																				<TR>	
																					<TD background="images/distribute_04.gif"></TD>
																					<TD class="notice_blue"><IMG SRC="images/distribute_img.gif" ></TD>
																					<TD width="100%" class="notice_blue"><p>1) 현금구매가격 + 카드결제 수수료 방식입니다.<br>2) 현금구매와 카드결제에 대한 차별은 금지되어 있으며 법적 책임이 발생할 수 있습니다.</p></TD>
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
																		<td>
																			<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
																				<TR>
																					<TD colspan=2 background="images/table_top_line.gif"></TD>
																				</TR>
																				<TR>
																					<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">카드결제 수수료 추가</TD>
																					<TD class="td_con1"><input type=text name=up_card_payfee value="<?=$card_payfee?>" size=5 maxlength=2 style="font-size:9pt" class="input">% 의 수수료를 <span class="font_orange">추가</span> (10원 단위 절사)</TD>
																				</TR>
																				<TR>
																					<TD colspan=2 background="images/table_top_line.gif"></TD>
																				</TR>
																			</TABLE>
																		</td>
																	</tr>
																	<tr>
																		<td height="30"></td>
																	</tr>
																	<tr>
																		<td>
																			<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
																				<TR>
																				<TD><IMG SRC="images/shop_payment_stitle6.gif" WIDTH="192" HEIGHT=31 ALT=""></TD>
																				<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif">&nbsp;</TD>
																				<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></TD>
																				</TR>
																			</TABLE>
																		</td>
																	</tr>
																	<tr>
																		<td style="padding-top:3pt; padding-bottom:3pt;">
																			<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
																				<TR>
																					<TD><IMG SRC="images/distribute_01.gif"></TD>
																					<TD COLSPAN=2 background="images/distribute_02.gif"></TD>
																					<TD><IMG SRC="images/distribute_03.gif"></TD>
																				</TR>
																				<TR>
																					<TD background="images/distribute_04.gif"></TD>
																					<TD class="notice_blue"><IMG SRC="images/distribute_img.gif" ></TD>
																					<TD width="100%" class="notice_blue"><p>1) 현금결제시 할인/추가 적립금을 주는 방식 ( 카드결제수수료 추가와 동시 사용불가)<br>2) 추가 적립금은 로그인한 회원에게만 적용됩니다.(추가 할인은 일반회원에게도 적용)<br>3) 현금구매와 카드결제에 대한 차별은 금지되어 있으며 법적 책임이 발생할 수 있습니다.</p></TD>
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
																		<td>
																			<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
																				<TR>
																					<TD colspan=2 background="images/table_top_line.gif"></TD>
																				</TR>
																				<TR>
																					<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">현금결제 혜택</TD>
																					<TD class="td_con1"><input type=text name=up_bank_percent value="<?=$bank_percent?>" size=5 maxlength=2 style="font-size:9pt" class="input">%		<select name=up_saletype class="input">
																							<option value="+" <?=($saletype=="+"?"selected":"")?>>할인
																							<option value="-" <?=($saletype=="-"?"selected":"")?>>적립
																						</select> 제공 (10원단위 절사, 배송비는 제외)
																					</TD>
																				</TR>
																				<TR>
																					<TD colspan=2 background="images/table_top_line.gif"></TD>
																				</TR>
																			</TABLE>
																		</td>
																	</tr>
																	<tr>
																		<td height="30"></td>
																	</tr>
																	<tr>
																		<td>
																			<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
																				<TR>
																					<TD><IMG SRC="images/shop_deli_stitle7.gif" WIDTH="180" HEIGHT=31 ALT=""></TD>
																					<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif">&nbsp;</TD>
																					<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></TD>
																				</TR>
																			</TABLE>
																		</td>
																	</tr>
																	<tr>
																		<td height=3></td>
																	</tr>
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
																					<TD width="100%" class="notice_blue">1) 주문 가능금액 보다 작은 금액은 주문이 되지 않습니다.(순수 상품금액 기준 - 배송료 및 카드수수료 제외)<br>2) 0으로 입력하면 모든 금액이 주문됩니다.<br>3) 신용카드 주문 가능금액 보다 작은 금액은 무통장 입금이 가능합니다.<br>&nbsp;&nbsp;&nbsp;&nbsp;신용카드 주문 가능금액은 무통장 결제보다 크거나 같아야 합니다.<br>4) 무통장 결제와 신용카드 결제의 차별은 금지되어 있으며 법적 책임이 발생할 수 있습니다.</TD>
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
																		<td height=3></td>
																	</tr>
																	<tr>
																		<td>
																			<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
																				<TR>
																					<TD colspan=2 background="images/table_top_line.gif"></TD>
																				</TR>
																				<TR>
																					<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">무통장 결제</TD>
																					<TD class="td_con1"> 주문 가능 금액 : <input type=text name=up_bank_miniprice size=15 maxlength=7 value="<?=$bank_miniprice?>" class="input">원</TD>
																				</TR>
																				<TR>
																					<TD colspan="2" background="images/table_con_line.gif"></TD>
																				</TR>
																				<TR>
																					<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">신용카드 결제</TD>
																					<TD class="td_con1" > 주문 가능 금액 : <input type=text name=up_card_miniprice size=15 maxlength=7 value="<?=$card_miniprice?>" class="input">원</TD>
																				</TR>
																				<TR>
																					<TD colspan=2 background="images/table_top_line.gif"></TD>
																				</TR>
																			</TABLE>
																		</td>
																	</tr>
																	<tr>
																		<td height="30"></td>
																	</tr>
																	<tr>
																		<td>
																			<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
																				<TR>
																					<TD width="20%"><IMG SRC="images/shop_payment_stitle7.gif"  HEIGHT=31 ALT="소셜쇼핑 주문취소설정"></TD>
																					<TD width="80%" background="images/shop_basicinfo_stitle_bg.gif">&nbsp;</TD>
																					<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></TD>
																				</TR>
																			</TABLE>
																		</td>
																	</tr>
																	<tr>
																		<td height=3></td>
																	</tr>
																	<!-- 웹호스팅 cron 안돌아감. -->
																	<!-- <tr>
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
																					<TD width="100%" class="notice_blue">1) 소셜쇼핑 구매성사 인원 미달성시 결제취소 기능 자동/수동 관리 제어.</TD>
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
																		<td height=3></td>
																	</tr> -->
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
																					<TD width="100%" class="notice_blue">1) 소셜쇼핑 구매성사 인원 미달성시 결제취소 기능 자동/수동 관리 제어.</TD>
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
																		<td height=3></td>
																	</tr>
																	<tr>
																		<td>
																			<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
																				<tr>
																					<td height=3></td>
																				</tr>
																				<TR>
																					<TD colspan=2 background="images/table_top_line.gif"></TD>
																				</TR>
																				<TR>
																					<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">결제취소</TD>
																					<TD class="td_con1" > <input type=radio id="up_auto_order_cancel1" name=up_auto_order_cancel value="Y" <?=($auto_order_cancel =="Y")? "checked":""?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=up_auto_order_cancel1>자동결제취소</label> <input type=radio id="up_auto_order_cancel2" name=up_auto_order_cancel value="N" <?=($auto_order_cancel =="N")? "checked":""?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=up_auto_order_cancel2>관리자 수동취소</label></TD>
																				</TR>
																				<TR>
																					<TD colspan=2 background="images/table_top_line.gif"></TD>
																				</TR>
																			</TABLE>
																		</td>
																	</tr>
																	<tr>
																		<td>&nbsp;</td>
																	</tr>
																	<tr>
																		<td align="center"><a href="javascript:CheckForm();"><img src="images/botteon_save.gif" width="113" height="38" border="0"></a></td>
																	</tr>
																</table>
															</form>
														</td>
													</tr>
													<tr>
														<td height=20></td>
													</tr>
													<tr>
														<td>
															<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
																<TR>
																	<TD><IMG SRC="images/manual_top1.gif" WIDTH=15 HEIGHT=45 ALT=""></TD>
																	<TD><IMG SRC="images/manual_title.gif" WIDTH=113 HEIGHT=45 ALT=""></TD>
																	<TD width="100%" background="images/manual_bg.gif"></TD>
																	<TD background="images/manual_bg.gif"></TD>
																	<TD><IMG SRC="images/manual_top2.gif" WIDTH=18 HEIGHT=45 ALT=""></TD>
																</TR>
																<TR>
																	<TD background="images/manual_left1.gif"></TD>
																	<TD COLSPAN=3 width="100%" valign="top" bgcolor="white" style="padding-top:8pt; padding-bottom:8pt; padding-left:4pt;" class="menual_bg">
																		<table cellpadding="0" cellspacing="0" width="100%">
																			<tr>
																				<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
																				<td ><span class="font_dotline">결제수단 종류 및 설정</span></td>
																			</tr>
																			<tr>
																				<td width="20" align="right">&nbsp;</td>
																				<td  class="space_top">- 실시간계좌이체, 가상계좌, 핸드폰결제, 에스크로결제는 지불중계회사와 계약 후 회사에 알려주시면 됩니다.</td>
																			</tr>
																			<tr>
																				<td width="20" align="right">&nbsp;</td>
																				<td  class="space_top">- 이외 추가적인 결제 수단은 프로그램과 연동이 가능한 결제수단만 가능합니다.</td>
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
												</table>
											</td>
											<td width="16" background="images/con_t_02_bg.gif"></td>
										</tr>
										<tr>
											<td width="16"><img src="images/con_t_04.gif" width="16" height="16" border="0"></td>
											<td background="images/con_t_04_bg.gif"></td>
											<td width="16"><img src="images/con_t_03.gif" width="16" height="16" border="0"></td>
										</tr>
										<tr>
											<td height="20"></td>
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
<?=$onload?>
<? INCLUDE "copyright.php"; ?>