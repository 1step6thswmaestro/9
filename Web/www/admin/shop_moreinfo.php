<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
INCLUDE ("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "sh-1";
$MenuCode = "shop";
if (!$_usersession->isAllowedTask($PageCode)) {
	INCLUDE ("AccessDeny.inc.php");
	exit;
}
#########################################################

$type=$_POST["type"];
$up_function_use=$_POST["up_function_use"];
$up_nametech_use=$_POST["up_nametech_use"];
$up_account_rule=$_POST["up_account_rule"];
$up_reserve_use=$_POST["up_reserve_use"];
$up_coupon_use=$_POST["up_coupon_use"];
$up_info_view=$_POST["up_info_view"];
$up_relay=$_POST["up_relay"];

if ($type == "up") {
	########################### TEST 쇼핑몰 확인 ##########################
	DemoShopCheck("데모버전에서는 테스트가 불가능 합니다.", $_SERVER[PHP_SELF]);
	#######################################################################

	$sql = "SELECT * FROM shop_more_info ";
	$result = mysql_query($sql,get_db_conn());
	$data_lows = mysql_num_rows($result);

	if (!$data_lows) {
		$sql = "insert into shop_more_info values(";
		$sql.= "'".$up_function_use."', ";
		$sql.= "'".$up_nametech_use."', ";
		$sql.= "'".$up_account_rule."', ";
		$sql.= "'".$up_reserve_use."', ";
		$sql.= "'".$up_coupon_use."', ";
		$sql.= "'".$up_info_view."', ";
		$sql.= "'".$up_relay."') ";

	}else{

		$sql = "UPDATE shop_more_info  SET ";
		$sql.= "function_use		= '".$up_function_use."', ";
		$sql.= "nametech_use	= '".$up_nametech_use."', ";
		$sql.= "account_rule		= '".$up_account_rule."', ";
		$sql.= "reserve_use		= '".$up_reserve_use."', ";
		$sql.= "coupon_use		= '".$up_coupon_use."', ";
		$sql.= "info_view		= '".$up_info_view."', ";
		$sql.= "relay		= '".$up_relay."' ";
	}
	$result = mysql_query($sql,get_db_conn());

	DeleteCache("shop_more_info.cache");
	$onload = "<script> alert('정보 수정이 완료되었습니다.'); </script>";
}

$sql = "SELECT * FROM shop_more_info ";
$result = mysql_query($sql,get_db_conn());
if ($row=mysql_fetch_object($result)) {
	$function_use = $row->function_use;
	$nametech_use = $row->nametech_use;
	$account_rule = $row->account_rule;
	$reserve_use = $row->reserve_use;
	$coupon_use = $row->coupon_use;
	$info_view = $row->info_view;
	$relay = $row->relay;
}
mysql_free_result($result);

?>

<? INCLUDE ("header.php"); ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">

function CheckForm() {
	var form = document.form1;
	form.type.value="up";
	form.submit();
}

function alertFunctionUser() {
	if(!confirm("사용안함설정 시 입점업체 관리자접속만 불가능하며 구매자화면에서는 입점상품이 노출되니 만약 실제 입점운영을 중지한다면 반드시 전체 입점사상품을 미노출로 변경하셔야 합니다.\n입점사상품 미노출설정 : 입점관리 > 입점상품관리 > 입점업체 상품목록에서 [on][off] 가능\n지금 입점기능 사용안함으로 변경하시겠습니까?")) {
		up_function_use1 = document.getElementById('up_function_use1');
		up_function_use1.checked = true;
	}
}

function alertAccountRule() {
	if (!confirm("상품별 공급가로 운영 시 각 상품별 공급가와 판매가를 반드시 입력해야 합니다.\n지금 변경하시겠습니까?")) {
		up_account_rule0 = document.getElementById('up_account_rule0');
		up_account_rule0.checked = true;
	}
}

</script>
<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
<tr>
	<td valign="top">
	<table cellpadding="0" cellspacing="0" width=100% style="table-layout:fixed">
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed" background="images/con_bg.gif">
		<col width=198></col>
		<col width=10></col>
		<col width=></col>
		<tr>
			<td  valign="top" background="images/leftmenu_bg.gif">
			<? include ("menu_vender.php"); ?>
			</td>

			<td></td>
			<td valign="top">

<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td height="29" colspan="3">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 상점관리 &gt; 상점 기본정보 설정 &gt; <span class="2depth_select">상점 기본정보 관리</span></td>
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





			<table width="100%" cellpadding="0" cellspacing="0">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
				<table width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td><img src="images/shop_basicinfo.gif" border="0"></td>
				</tr>
				<tr>
					<td width="100%" background="images/title_bg.gif" height="21"></td>
				</tr>
				</table>
				</td>
			</tr>
			<tr><td height="20"></td></tr>
			<tr>
				<td>
				<table width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td><img src="images/shop_basicinfo_stitle2.gif" border="0"></td>
					<td width="100%" background="images/shop_basicinfo_stitle_bg.gif"></td>
					<td><img src="images/shop_basicinfo_stitle_end.gif" border="0"></td>
				</tr>
				</table>
				</td>
			</tr>
			<tr><td height="3"></td></tr>
			<tr>
				<td>
				<table width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td><img src="images/distribute_01.gif"></td>
					<td COLSPAN="2" background="images/distribute_02.gif"></td>
					<td><img src="images/distribute_03.gif"></td>
				</tr>
				<tr>
					<td background="images/distribute_04.gif"></td>
					<td class="notice_blue"><img src="images/distribute_img.gif" ></td>
					<td width="100%" class="notice_blue">쇼핑몰 정보를 바탕으로 각종 내용이 표기됩니다. <b>정확히 입력해 주세요!</b></td>
					<td background="images/distribute_07.gif"></td>
				</tr>
				<tr>
					<td><img src="images/distribute_08.gif"></td>
					<td COLSPAN="2" background="images/distribute_09.gif"></td>
					<td><img src="images/distribute_10.gif"></td>
				</tr>
				</table>
				</td>
			</tr>
			<tr><td height="3"></td></tr>
			<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post>
			<input type=hidden name=type>
			<tr>
				<td>
				<table width="100%" cellpadding="0" cellspacing="0">
				<col width="140"></col>
				<col></col>
				<tr>
					<td height="2" colspan="2" bgcolor="#808080"></td>
				</tr>
				<tr>
					<td class="table_cell"><img src="images/icon_point2.gif" border="0">입점기능사용여부</td>
					<td class="td_con1">
						<input type=radio name=up_function_use id=up_function_use1 value="1" <?if($function_use=="1" || strlen($function_use)==0)echo"checked";?>> <label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=up_function_use1>사용</label>
						<img width=20 height=0>
						<input type=radio name=up_function_use id=up_function_use0 value="0" <?if($function_use=="0")echo"checked";?> onclick="alertFunctionUser();"> <label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=up_function_use0>사용안함</label>
					</td>
				</tr>
				<tr>
					<td height="1" colspan="2" bgcolor="#EDEDED"></td>
				</tr>
				<tr>
					<td class="table_cell"><img src="images/icon_point2.gif" border="0">입점사 네임텍 사용여부</td>
					<td class="td_con1">
						<input type=radio name=up_nametech_use id=up_nametech_use1 value="1" <?if($nametech_use=="1" || strlen($nametech_use)==0)echo"checked";?>> <label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=up_nametech_use1>사용</label>
						<img width=20 height=0>
						<input type=radio name=up_nametech_use id=up_nametech_use0 value="0" <?if($nametech_use=="0")echo"checked";?>> <label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=up_nametech_use0>사용안함</label>
					</td>
				</tr>
				<tr>
					<td height="1" colspan="2" bgcolor="#EDEDED"></td>
				</tr>
				<tr>
					<td class="table_cell"><img src="images/icon_point2.gif" border="0">정산기준설정</td>
					<td class="td_con1">
						<input type=radio name=up_account_rule id=up_account_rule0 value="0" <?if($account_rule=="0" || strlen($account_rule)==0)echo"checked";?>> <label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=up_account_rule0>판매 수수료로 운영</label>
						<img width=20 height=0>
						<input type=radio name=up_account_rule id=up_account_rule1 value="1" <?if($account_rule=="1")echo"checked";?> onclick="alertAccountRule();"> <label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=up_account_rule1>상품별 공급가로 운영</label>
					</td>
				</tr>
				<tr>
					<td height="1" colspan="2" bgcolor="#EDEDED"></td>
				</tr>
				<tr>
					<td class="table_cell"><img src="images/icon_point2.gif" border="0">혜택 사용 여부</td>
					<td class="td_con1">
						<b>적립금 : </b>
						<input type=radio name=up_reserve_use id=up_reserve_use1 value="1" <?if($reserve_use=="1")echo"checked";?>> <label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=up_reserve_use1>사용</label>
						<img width=20 height=0>
						<input type=radio name=up_reserve_use id=up_reserve_use0 value="0" <?if($reserve_use=="0" || strlen($reserve_use)==0)echo"checked";?>> <label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=up_reserve_use0>사용 안함</label>
						<br/>
						<b>쿠폰 : </b>
						<input type=radio name=up_coupon_use id=up_coupon_use1 value="1" <?if($coupon_use=="1")echo"checked";?>> <label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=up_coupon_use1>사용</label>
						<img width=20 height=0>
						<input type=radio name=up_coupon_use id=up_coupon_use0 value="0" <?if($coupon_use=="0" || strlen($coupon_use)==0)echo"checked";?>> <label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=up_coupon_use0>사용 안함</label>
						<br/>
						<span class="font_blue">
						* 사용불가 체크 시 입점사는 혜택을 사용할 수 없으며 해당메뉴가 입점사 관리모드에 노출되지 않습니다.
						</span>
					</td>
				</tr>
				<tr>
					<td height="1" colspan="2" bgcolor="#EDEDED"></td>
				</tr>
				<tr>
					<td class="table_cell"><img src="images/icon_point2.gif" border="0">입점업체정보노출여부</td>
					<td class="td_con1">
						<input type=radio name=up_info_view id=up_disabled1 value="1" <?if($info_view=="1" || strlen($info_view)==0)echo"checked";?>> <label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=up_disabled1>노출</label>
						<img width=20 height=0>
						<input type=radio name=up_info_view id=up_info_view0 value="0" <?if($info_view=="0")echo"checked";?>> <label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=up_info_view0>노출안함</label>
						<br/>
						<span class="font_blue">
							* 노출 안함 체크 시 전화번호 및 메일주소 등 업체정보가 보이지 않습니다. 고객이 구매 전 직접 입점사와 구매전 흥정행위를 방지하기 위함
						</span>
					</td>
				</tr>
				<tr>
					<td height="1" colspan="2" bgcolor="#EDEDED"></td>
				</tr>
				<tr>
					<td class="table_cell"><img src="images/icon_point2.gif" border="0">판매운영사(본사)<br/>상품판매 중계업<br/>등록여부</td>
					<td class="td_con1">
						<input type=radio name=up_relay id=up_relay0 value="0" <?if($relay=="0" || strlen($relay)==0)echo"checked";?>> <label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=up_relay0>상품판매 중계업체 아님</label>
						<img width=20 height=0>
						<input type=radio name=up_relay id=up_relay1 value="1" <?if($relay=="1")echo"checked";?>> <label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=up_relay1>상품판매 중계업체</label>
						<br/>
						<br/>
							<table width="100%" cellpadding="0" cellspacing="0">
							<tr>
								<td class="menual_con"><img src="images/icon_8.gif" border="0" align="absmiddle"><span class="font_dotline">정산계산식</span></td>
							</tr>
							<tr>
								<td style="padding-left:13px;" class="menual_con">
								<b>1. 수수료 기준 운영</b>
									<div class="font_blue" style="padding-left:13px">
									1)상품판매 중계업체가 아닌 ① 경우 정산금액=상품판매금액-수수료금액+배송비-적립금-쿠폰할인/적립 <br/>
									2)상품판매 중개업체인 ② 경우 정산금액=상품판매금액-수수료금액-수수료의 부가세+배송비-적립금-쿠폰할인/적립
									</div>
								<b>2. 상품공급가 기준 운영</b>
									<div class="font_blue" style="padding-left:13px">
									1) 상품판매 중계업체가 아닌 ① 의 경우 정산금액= 판매상품 전체 공급가격+배송비-적립금-쿠폰할인/적립 <br/>
									2) 상품판매 중계업체인 ② 의 경우 정산금액=판매상품 전체 공급가격-(상품판매금액-상품공급가격)*0.1+배송비-적립금-쿠폰할인/적립 <br/>
										<span style="padding-left:13px">* (상품판매금액-상품공급가격)*0.1은 수수료의 부가세입니다.</span>
									</div>
									*수수료 금액 = 판매금액x수수료율 <br/>
									*적립금 및 쿠폰의 경우 발행 주체가 부담하는것을 원칭으로 합니다. <br/>
									*회원등급별 혜택 등 기타 혜택은 판매운영사(본사)가 부담하는것을 원칙으로 합니다. <br/>
									*배송료의 경우 입점사 설정한 배송정책을 따릅니다.
								</td>
							</tr>
							<tr><td height="20"></td></tr>
							<tr>
								<td class="menual_con"><img src="images/icon_8.gif" border="0" align="absmiddle"><span class="font_dotline">세금계산서 처리</span></td>
							</tr>
							<tr>
								<td style="padding-left:13px;" class="menual_con">
								①의 경우 전체 정산금액 기준 매입세금계산서를 입점사로부터 받음 <br/>
								②의 경우 상품판매수수료에 대한 부가세를 공제 후 정산하고 판매수수료에 대한 매출세금계산서를 입점사에게 발송, 입점사는 전체 판매금액에 대해 구매자에게 세금계산서 발송
								</td>
							</tr>
							<tr><td height="20"></td></tr>
							</table>
					</td>
				</tr>
				<tr>
					<td height="1" colspan="2" bgcolor="#EDEDED"></td>
				</tr>
				</table>
				</td>
			</tr>
			<tr><td height="10"></td></tr>
			<tr>
				<td align="center"><a href="javascript:CheckForm();"><img src="images/botteon_save.gif" border="0"></a></td>
			</tr>
			<tr><td height="20"></td></tr>
			</form>
			<!--
			<tr>
				<td>
				<table width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td><img src="images/manual_top1.gif" border="0"></td>
					<td width="100%" background="images/manual_bg.gif"><img src="images/manual_title.gif" border="0"></td>
					<td><img src="images/manual_top2.gif" border="0"></td>
				</tr>
				<tr>
					<td background="images/manual_left1.gif"></td>
					<td style="padding-top:5px;" class="menual_bg">
					<table width="100%" cellpadding="0" cellspacing="0">
					<tr>
						<td class="menual_con"><img src="images/icon_8.gif" border="0" align="absmiddle"><span class="font_dotline">정산계산식</span></td>
					</tr>
					<tr>
						<td style="padding-left:13px;" class="menual_con">
						<b>1. 수수료 기준 운영</b>
							<div class="font_blue" style="padding-left:13px">
							1)상품판매 중계업체가 아닌 ① 경우 정산금액=상품판매금액-수수료금액+배송비-적립금-쿠폰할인/적립 <br/>
							2)상품판매 중개업체인 ② 경우 정산금액=상품판매금액-수수료금액-수수료의 부가세+배송비-적립금-쿠폰할인/적립
							</div>
						<b>2. 상품공급가 기준 운영</b>
							<div class="font_blue" style="padding-left:13px">
							1) 상품판매 중계업체가 아닌 ① 의 경우 정산금액= 판매상품 전체 공급가격+배송비-적립금-쿠폰할인/적립 <br/>
							2) 상품판매 중계업체인 ② 의 경우 정산금액=판매상품 전체 공급가격-(상품판매금액-상품공급가격)*0.1+배송비-적립금-쿠폰할인/적립 <br/>
								<span style="padding-left:13px">* (상품판매금액-상품공급가격)*0.1은 수수료의 부가세입니다.</span>
							</div>
							*수수료 금액 = 판매금액x수수료율 <br/>
							*적립금 및 쿠폰의 경우 발행 주체가 부담하는것을 원칭으로 합니다. <br/>
							*회원등급별 혜택 등 기타 혜택은 판매운영사(본사)가 부담하는것을 원칙으로 합니다. <br/>
							*배송료의 경우 입점사 설정한 배송정책을 따릅니다.
						</td>
					</tr>
					<tr><td height="20"></td></tr>
					<tr>
						<td class="menual_con"><img src="images/icon_8.gif" border="0" align="absmiddle"><span class="font_dotline">세금계산서 처리</span></td>
					</tr>
					<tr>
						<td style="padding-left:13px;" class="menual_con">
						①의 경우 전체 정산금액 기준 매입세금계산서를 입점사로부터 받음 <br/>
						②의 경우 상품판매수수료에 대한 부가세를 공제 후 정산하고 판매수수료에 대한 매출세금계산서를 입점사에게 발송, 입점사는 전체 판매금액에 대해 구매자에게 세금계산서 발송
						</td>
					</tr>
					<tr><td height="20"></td></tr>
					</table>
					</td>
					<td background="images/manual_right1.gif"></td>
				</tr>
				<tr>
					<td><img src="images/manual_left2.gif" border="0"></td>
					<td background="images/manual_down.gif"></td>
					<td><img src="images/manual_right2.gif" border="0"></td>
				</tr>
				</table>
				</td>
			</tr>
			-->
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

<? INCLUDE ("copyright.php"); ?>