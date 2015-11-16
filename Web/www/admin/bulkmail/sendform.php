<?
$sql = "SELECT id, authkey, return_tel FROM tblsmsinfo ";
$res=mysql_query($sql,get_db_conn());
$smsInfo = array();
$smsInfo=mysql_fetch_assoc($res);
$smsInfo['reutrn_tel'] = explode('-',$smsInfo['reutrn_tel']);
mysql_free_result($res);
$smsInfo['errMsg'] = '';

if(empty($smsInfo['id'])|| empty($smsInfo['authkey'])) $smsInfo['errMsg'] = "SMS 설정이 되어 있지않습니다.";
else{
	$smsInfo['ablecount'] = getSmscount($smsInfo['id'], $smsInfo['authkey']);
	switch(substr($smsInfo['ablecount'],0,2)){
		case 'OK': $smsInfo['ablecount']=substr($smsInfo['ablecount'],3); break;
		case 'NO': case 'AK':
			$smsInfo['ablecount']= '0';$smsInfo['errMsg'] = "SMS 설정이 올바르지 않습니다."; break;
		default:
			$smsInfo['ablecount']= '0';$smsInfo['errMsg'] = "서버 통신장애"; break;
	}
}
?>

<!-- 에디터용 파일 호출 -->
<!-- <script type="text/javascript" src="/gmeditor/js/jquery.js"></script> -->
<script type="text/javascript" src="/gmeditor/js/jquery.event.drag-2.0.min.js"></script>
<script type="text/javascript" src="/gmeditor/js/jquery.resizable.js"></script>
<script type="text/javascript" src="/gmeditor/js/ajax_upload.3.6.js"></script>
<script type="text/javascript" src="/gmeditor/js/ej.h2xhtml.js"></script>
<script type="text/javascript" src="/gmeditor/editor.js"></script>
<script language="javascript" type="text/javascript">
$(document).ready(function() {
	ejEditor();
});
</script>
<style type="text/css">
  @import url("/gmeditor/common.css");

  .formTh {text-align:left; padding-left:12px; height:30; font-size:12px; font-weight:bold; color:#4b4b4b; background-color:#f8f8f8; border-right:1px solid #e3e3e3; border-bottom:1px solid #ededed;}
  .formTd {height:30; background-color:#ffffff; color:#777777; padding:4px 0px 4px 8px; border-bottom:1px solid #ededed;}
  .td_con1 {height:30; background-color:#ffffff; color:#949494; padding:4px 0px 4px 8px; border-bottom:1px solid #ededed;}
  .bulkmailInput {font-family:돋움; border:1px solid #d5d5d5; padding:2px;}

  .tpSel{ border:3px solid #F90;}
  .tpItem{border:3px solid; padding:0px; margin-right:2px; cursor:hand; cursor:pointer; display:block; padding:2px 2px 2px 10px; margin-left:5%; float:left}
  .tpTitle{ float:left; display:block; margin-right:20px;}
  .delTpl{background:#ff0000; cursor:pointer; float:left;}
</style>
<!-- # 에디터용 파일 호출 -->
<script type="text/javascript" src="/js/jquery-ui-1.9.2.custom.min.js"></script>
<style type="text/css">
  @import url("/css/ui-lightness/jquery-ui-1.9.2.custom.min.css");
</style>

<script type="text/javascript" src="calendar.js.php"></script>
<script language="JavaScript">
function checkBulkForm(form){
	if(form.onAction.value == '1'){
		alert('발송 처리 중입니다');
		return false;
	}
	if($.trim(form.senderName.value) == ''){
		alert('발송자 이름을 입력하세요.');
		form.senderName.focus();
		return false;
	}
	if($.trim(form.senderEmail.value) == ''){
		alert('발송자 이메일을 입력하세요.');
		form.senderEmail.focus();
		return false;
	}
	if($.trim(form.subject.value) == ''){
		alert('메일제목을 입력하세요.');
		form.subject.focus();
		return false;
	}
	if(form.setCoupon[1].checked){
		if(!CheckForm(form)) return false;
	}

	if(form.sendSMS && form.sendSMS[1] && form.sendSMS[1].checked){
		if($.trim(form.smsmsg.value) == ''){
			alert('문자메시지 내용을 입력하세요.');
			form.smsmsg.focus();
			return false;
		}
		var frmtel = true;
		$('input[name^=smsfrom]').each(function(idx,el){
			if($(el).val().length < 1){
				alert('발신자 전화번호를 입력하세요');
				$(el).focus();
				frmtel = false;
				return;
			}
		});
		if(!frmtel) return false;
	}
	if($('input[name=targetType]:checked').val() == 'input' && $('input[name=new_email]').val() != '' && !confirm('직접입력후 추가 처리 되지 않은 정보가 있습니다. 그래도 발송하시겠습니까?')){
			return false;
	}
	ejEdtSetMode('mailContents','0');
	form.onAction.value = '1';
	return true;
}

function CheckForm(form) {
	if (form.time[0].checked==true) {
		date = "<?=date("Y-m-d");?>";
		if (form.date_start.value<date || form.date_end.value<date || form.date_start.value>form.date_end.value) {
			alert("쿠폰 유효기간 설정이 잘못되었습니다.\n\n다시 확인하시기 바랍니다.");
			form.date_start.focus();
			return false;
		}
		//content+="* 쿠폰 유효기간 : "+form.date_start.value+" ~ "+form.date_end.value+" 까지\n\n";
	}else{
		if (form.peorid.value.length==0) {
			alert("쿠폰 사용기간을 입력하세요.");
			form.peorid.focus();
			return false;
		} else if (!IsNumeric(document.bulkMailForm.peorid.value)) {
			alert("쿠폰 사용기간은 숫자만 입력 가능합니다.");
			form.peorid.focus();
			return false;
		}
	}
	if (form.sale_money.value.length==0) {
		alert("쿠폰 할인 금액/할인률을 입력하세요.");
		form.sale_money.focus();
		return false;
	} else if (!IsNumeric(form.sale_money.value)) {
		alert("쿠폰 할인 금액/할인률은 숫자만 입력 가능합니다.(소숫점 입력 안됨)");
		form.sale_money.focus();
		return false;
	}
	if(form.sale2.selectedIndex==1 && form.sale_money.value>=100){
		alert("쿠폰 할인률은 100보다 작아야 합니다.");
		form.sale_money.focus();
		return false;
	}

	if(form.productcode.value.length==18 && form.checksale[1].checked==true && form.use_con_type2.checked!=true) {
		alert("쿠폰이 한상품에 적용될경우 구매금액에 제한이 없습니다.");
		nomoney(1);
	}
	if(form.checksale[1].checked==true){
		if(form.mini_price.value.length==0){
			alert("쿠폰 결제 금액을 입력하세요.");
			document.bulkMailForm.mini_price.focus();
			return false;
		}else if(!IsNumeric(form.mini_price.value)){
			alert("쿠폰 결제 금액은 숫자만 입력 가능합니다.");
			form.mini_price.focus();
			return false;
		}
	}
	return true;
}
function changerate(rate){
	document.bulkMailForm.rate.value=rate;
	if(rate=="%") {
		document.bulkMailForm.amount_floor.disabled=false;
	} else {
		document.bulkMailForm.amount_floor.disabled=true;
	}
}
function nomoney(temp){
	if(temp==1){
		document.bulkMailForm.mini_price.value="";
		document.bulkMailForm.mini_price.disabled=true;
		document.bulkMailForm.mini_price.style.background='#F0F0F0';
		document.bulkMailForm.checksale[0].checked=true;
	} else {
		document.bulkMailForm.mini_price.value="0";
		document.bulkMailForm.mini_price.disabled=false;
		document.bulkMailForm.mini_price.style.background='white';
		document.bulkMailForm.checksale[1].checked=true;
	}
}
function nonum(temp){
	if(temp==1){
		document.bulkMailForm.issue_tot_no.value="";
		document.bulkMailForm.issue_tot_no.disabled=true;
		document.bulkMailForm.issue_tot_no.style.background='#F0F0F0';
		document.form1.checknum[0].checked=true;
	} else {
		document.form1.issue_tot_no.value="0";
		document.bulkMailForm.issue_tot_no.disabled=false;
		document.bulkMailForm.issue_tot_no.style.background='white';
		document.bulkMailForm.checknum[1].checked=true;
	}
}
function ViewLayer(layer,display){
	if(document.all && document.all[layer]){
		document.all[layer].style.display=display;
	} else if(document.getElementById && document.getElementByld(layer)){
		document.getElementByld[layer].style.display=display;
	} else if(document.layers && document.layers[layer]){
		document.layers[layer].display=display;
	}
}
function ChoiceProduct(){
	window.open("about:blank","coupon_product","width=245,height=140,scrollbars=no");
	document.form2.submit();
}

</script>

<? if(!empty($smsInfo['errMsg'])){ ?>
<? $sms_host=getSmshost(&$sms_path); ?>
<form name=joinform method=post action="http://<?=$sms_host.$sms_path?>/member/member_join.html" target="smsjoin">
<input type=hidden name=shopurl value="<?=$shopurl?>">
</form>
<? } ?>
<form name=form2 action="coupon_productchoice.php" method=post target=coupon_product></form>
<table cellpadding="0" cellspacing="0" width="100%" border="0">
	<tr>
		<td height="8"></td>
	</tr>
	<tr>
		<td>
			<table width="100%" border=0 cellpadding=0 cellspacing=0>
				<tr>
					<td><IMG src="/admin/images/market_bulkmail_title.gif" ALT="대용량메일 발송관리"></td>
				</tr>
				<tr>
					<td width="100%" background="images/title_bg.gif" height="21"></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td height="20">
			<table border="0" cellpadding="0" cellspacing="0">
				<tr>
					<TD background="images/distribute_04.gif"></TD>
					<TD class="notice_blue"><IMG SRC="images/distribute_img.gif" ></TD>
					<TD width="100%" class="notice_blue"><!--대용량 메일 서비스--></TD>
					<TD background="images/distribute_07.gif"></TD>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>
			<!--대용량 메일 관련 설명-->
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td height="40"><img src="images/market_bulkmail_title_s5.gif"></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>
		<?
		$sql = "SELECT shopname,info_email FROM tblshopinfo limit 1";
		$result = mysql_query($sql,get_db_conn());
		if($result){
			$senderName = mysql_result($result,0,0);
			$senderEmail = mysql_result($result,0,1);
		}

		$sql = "select group_code,group_name from tblmembergroup";
		$result = mysql_query($sql,get_db_conn());
		$memberGroupLists = array();
		if($result){
			while($row = mysql_fetch_assoc($result)){
				array_push($memberGroupLists,$row);
			}
		}


		$sql = "select gidx,gname from bulkmail_group";
		$result = mysql_query($sql,get_db_conn());
		$mailGroupLists = array();
		if($result){
			while($row = mysql_fetch_assoc($result)){
				array_push($mailGroupLists,$row);
			}
		}

		?>
			<form action="/admin/bulkmail/preview.php" id="previewForm" name="previewForm" target="mailPreview" method="post">
				<input type="hidden" name="contents" value="" />
				<input type="hidden" name="sender" value="" />
				<input type="hidden" name="receiver" value="" />
				<input type="hidden" name="subject" value="" />
				<input type="hidden" name="rejectMsg" value="" />
			</form>

			<form action="/admin/bulkmail/process.php" name="bulkMailForm" id="bulkMailform" method="post" enctype="multipart/form-data" onsubmit="javascript:return checkBulkForm(this);">
				<input type="hidden" name="onAction" value="" />
				<input type="hidden" name="act" value="send" />
				<input type="hidden" name="campaignType" value="1" />
				<input type="hidden" name="receiverName" value="[$name]">
				<!--<table width="100%" border="0" cellpadding="0" cellspacing="0" class="formTbl">-->
				<table width="100%" border="0" cellpadding="0" cellspacing="0">
					<tr><td height="1" bgcolor="#b9b9b9" colspan="3"></tr>
					<tr>
						<th colspan="2" class="formTh"><img src="images/icon_point2.gif" width="8" height="11" border="0">잔여수량</th>
						<td class="formTd"><input type="text" value="<?=number_format($bulkmail->ableCount)?> 건" readOnly="readOnly" class="bulkmailInput"> <A href="/admin/bulkmail.php?isorder=order"><img src="images/market_bulkmail_recharge.gif"></A></span><br /><font color="#ff4c00">* 발송 대상 수가 잔여수량 보다 클 경우 잔여수량 초과 분에 대해서는 메일이 발송되지 않습니다.</font></td>
					</tr>
					<tr>
						<th colspan="2" class="formTh"><img src="images/icon_point2.gif" width="8" height="11" border="0">보내는 사람</th>
						<td class="formTd">
							이름 : <input type="text" name="senderName" value="<?=$senderName?>" class="bulkmailInput" style="margin-right:10px;" />
							이메일 주소 : <input type="text" name="senderEmail" value="<?=$senderEmail?>" class="bulkmailInput" style="width:200px;" />
						</td>
					</tr>
					<tr>
						<th rowspan="2" class="formTh" style="width:80px;"><img src="images/icon_point2.gif" width="8" height="11" border="0">받는사람</th>
						<th class="formTh" style="width:80px;">대상 구분</th>
						<td class="formTd">
							<input type="radio" name="targetType" value="member" onclick="javascript:chgTarget(this)" checked="checked" />회원
							<input type="radio" name="targetType" value="csv" onclick="javascript:chgTarget(this)" />엑셀(CSV) 파일 등록
							<input type="radio" name="targetType" value="input" onclick="javascript:chgTarget(this)" />직접주소입력
						</td>
					</tr>
					<tr>
						<th class="formTh">선택</th>
						<td>
						<div id="memberSet" style="width:100%;">
							<table border="0" cellpadding="0" cellspacing="0" width="100%">
								<tr>
									<td class="formTd" width="220">
										<input type="radio" name="memberType" value="membergroup" onclick="javascript:refMemberType(this);" checked="checked" />회원그룹 선택
										<select name="membergroup" id="membergroup_sel">
											<option value="all">전체그룹</option>
											<? for($i=0;$i<count($memberGroupLists);$i++){ ?>
											<option value="<?=$memberGroupLists[$i]['group_code']?>"><?=$memberGroupLists[$i]['group_name']?></option>
											<? } ?>
										</select>
									</td>
									<td class="formTd"><!--<input type="button" value="회원그룹관리" onclick="javascript:goMenu('3','/admin/member_groupnew.php')" />--><a href="javascript:goMenu('3','/admin/member_groupnew.php')"><img src="images/market_bulkmail_mgroup.gif" border="0"></a></td>
								</tr>
								<tr>
									<td class="formTd">
										<input type="radio" name="memberType" value="mailgroup" onclick="javascript:refMemberType(this);"/>발송그룹 선택
										<select name="mailgroup" id="mailgroup_sel">
											<option value="">그룹선택</option>
											<? for($i=0;$i<count($mailGroupLists);$i++){ ?>
											<option value="<?=$mailGroupLists[$i]['gidx']?>"><?=$mailGroupLists[$i]['gname']?></option>
											<? } ?>
										</select>
									</td>
									<td class="formTd"><a href="bulkmail.php?act=group"><!--<input type="button" value="발송그룹관리" />--><img src="images/market_bulkmail_sgroup.gif" border="0"></a></td>
								</tr>
								<tr>
									<td colspan="2" class="formTd">수신거부 포함 여부 ( <input type="radio" name="addreject" value="0" checked="checked" />포함하지 않음 / <input type="radio" name="addreject" value="1" /> 포함)
							</table>
						</div>
						<div id="csvSet" style="width:100%; display:none">
							<input type="file" name="csvFile" value="" />
							<?
							$filepath=$_SERVER['DOCUMENT_ROOT']."/data/groupmail/";
							$filename="bulkmail.csv";
							$csvfile = $filepath.$filename;
							$filestr = '';
							if(file_exists($csvfile) && is_file($csvfile) && filesize($csvfile)){
								$filestr = '<br>'.date("Y/m/d h:i:s",filemtime($csvfile)).' 에 생성된 파일이 있습니다.<br> 파일을 등록하지 않으면 마지막 저장 파일로 발송됩니다.';
							}
							echo $filestr;
							?>
						</div>
						<div id="inputSet" style="width:100%; display:none">
							<table border="0" cellpadding="0" cellspacing="0"  class="formTbl">
								<caption style="color:red; text-align:left"> * 입력후 "<span style="font-weight:bold">추가</span>" 버튼을 누르셔야만 발송 목록에 추가 됩니다. <input type="button" value="회원검색" onclick="javascript:memberSearch();" /></caption>
								<tr>
									<th style="height:28px;">Email</th>
									<th>이름</th>
									<th>Mobile</th>
									<th>추가</th>
								</tr>
								<tr>
									<td><input type="text" name="new_email" id="new_email" value="" /></td>
									<td><input type="text" name="new_name" id="new_name" value="" /></td>
									<td><input type="text" name="new_mobile" id="new_mobile" value="" /></td>
									<td><input type="button" name="inputAdd" onclick="javascript:addInputRow(this)" value="추가" /></td>
								</tr>
							</table>
						</div>
						<script language="javascript" type="text/javascript">
						function memberSearch(){
							window.open('/admin/bulkmail/searchlist.php?stype=member', 'SearchResult', 'width=450,height=300,status=yes,resizable=yes,scrollbars=yes');
						}
						</script>
						</td>
					</tr>
					<tr>
						<th colspan="2" class="formTh"><img src="images/icon_point2.gif" width="8" height="11" border="0">메일구분</th>
						<td class="formTd">
							<select name="mailType">
								<option value="뉴스레터">뉴스레터</option>
								<option value="상품홍보">상품홍보</option>
								<option value="기타">기타</option>
								<option value="긴급공지">긴급공지</option>
							</select>
						</td>
					</tr>
					<tr>
						<th colspan="2" class="formTh"><img src="images/icon_point2.gif" width="8" height="11" border="0">메일제목</th>
						<td class="formTd"><input type="text" name="subject" value="" class="bulkmailInput" style="width:80%;" /><br />* 제목에 받는 분의 이름을 사용 할 경우 이름 부분에 [$name] 를 넣으면 됩니다.<br />ex: [$name] 님 안녕하세요.</td>
					</tr>
					<tr>
						<th colspan="2"  class="formTh">SMS 안내기능</th>
						<td style="padding-left:5px;"  class="formTd">
						<? if(empty($smsInfo['errMsg'])){ ?>
						<input type="radio" name="sendSMS" value="0" checked="checked" onclick="javascript:toggleSendSms(this)" />보내지않음&nbsp;<input type="radio" name="sendSMS" value="1" onclick="javascript:toggleSendSms(this)" />SMS 메시지 발송
						<span style="margin-left:5px;">  잔여 : <?=number_format($smsInfo['ablecount']).'건'?>&nbsp;&nbsp; <A href="javascript:parent.topframe.GoMenu(7,'/admin/market_smsfill.php')"><img src="images/market_bulkmail_sms_recharge.gif"></A></span>
						<div id="smsmsgInputDiv" style="clear:both; display:none; padding-left:5px;">
						발신자번호 : <input type="text" id="smsfrom1" name="smsfrom[]" value=""  class="input" style=" width:30px;" />-<input type="text" id="smsfrom2" name="smsfrom[]" value=""  class="input" style=" width:30px;" />-<input type="text"  id="smsfrom3" name="smsfrom[]" value=""  class="input" style=" width:30px;" />
						<br />메시지: <input type="text" name="smsmsg" value=""  class="input" style=" width:340px;" /><br /><span style="color:orange" >* 각회원별 이름은 [$name] 를 메일주소는 [$email] 을 입력하시면 자동 변환됩니다.</span></div>
						<script language="javascript" type="text/javascript">
							function toggleSendSms(el){
								if($(el).val() == '1'){
									$('#smsmsgInputDiv').css('display','');
								}else{
									$('#smsmsgInputDiv').css('display','none');
								}
							}
						</script>
						<? }else{ ?>
							<script language="javascript" type="text/javascript">
								function sms_join() {
									window.open("about:blank","smsjoin","width=450,height=460,scrollbars=no,status=yes");
									document.joinform.submit();
								}
							</script>

							<input type="hidden" name="sendSMS" value="0" />
							사용불가 : <?=$smsInfo['errMsg']?> <A style="cursor:hand" onclick="sms_join();"><img src="images/market_bulkmail_sms_order.gif"></A></span>
						<? } ?>
						</td>

					</tr>
					<tr>
						<th colspan="2" class="formTh"><img src="images/icon_point2.gif" width="8" height="11" border="0">할인쿠폰기능</th>
						<td class="formTd">
							<input type="radio" name="setCoupon" value="0" checked="checked" onclick="javascript:toggleCoupon(this)" />미사용&nbsp;<input type="radio" name="setCoupon" value="1" onclick="javascript:toggleCoupon(this)" />사용
							<script language="javascript" type="text/javascript">
								function toggleCoupon(el){
									if($(el).val() == '1'){
										$('#couponFormDiv').css('display','');
									}else{
										$('#couponFormDiv').css('display','none');
									}
								}
							</script>
							&nbsp;&nbsp;<span class="font_orange">* 쿠폰을 설정하신 경우 컨텐츠에 [$couponcode] 를 넣으셔야 해당 부분에 쿠폰코드가 표시 됩니다.</span>
						</td>
					</tr>

					<tr><td height="1" bgcolor="#b9b9b9" colspan="3"></tr>
				</table>
				<div id="couponFormDiv" style="display:none">
				<input type="hidden" name="coupon_name" value="[대량메일발송용쿠폰]<?=date('Y-m-d H')?>" />
				<input type="hidden" name="productcode" value="ALL" />
				<input type="hidden" name="issue_type" value="O" />
				<input type="hidden" name="detail_auto" value="N" />
				<input type="hidden" name="use_point" value="Y" />
				<input type="hidden" name="use_con_type1" value="Y">
				<input type="hidden" name="description" value="대량 메일 발송시 설정된 쿠폰" />
				<table width="100%" border="0" cellpadding="0" cellspacing="0">
					<tr><td height="30"></td></tr>
					<tr>
						<td height="40" colspan="2"><img src="images/market_bulkmail_title_s4.gif"></td>
					</tr>
					<!--<caption> 쿠폰 설정</caption>-->
					<tr><td height="1" bgcolor="#b9b9b9" colspan="2"></tr>
					<tr>
						<th rowspan="2" class="formTh" style="width:173px;"><img src="images/icon_point2.gif" width="8" height="11" border="0">유효기간</th>
						<td class="formTd">
							<input type="radio" value="D" name="time">기간설정 : <input onfocus="this.blur();" onclick="Calendar(this)" size="12" name="date_start" value="<?=date('Y-m-d')?>" class="input_selected"> 부터 <input  onfocus="this.blur();" onclick="Calendar(this)" size="12" name="date_end" value="<?=date('Y-m-d',strtotime('+1 week'))?>" class="input_selected"> 까지 사용가능<span class="font_orange">(유효기간 마지막일 23시59분59초 까지)</span> </td>
						</tr>
						<tr>
							<td class="formTd"><input type="radio" CHECKED value="P" name="time">발행 후 <input onkeyup="strnumkeyup(this);" style="PADDING-RIGHT: 3px; TEXT-ALIGN: right" maxLength=3 size=4 name=peorid class="input">일 동안 사용가능<span class="font_orange">(유효기간 마지막일 23시59분59초 까지)</span> </td>
					</tr>
					<tr>
						<th class="formTh"><img src="images/icon_point2.gif" width="8" height="11" border="0">쿠폰종류 선택</th>
						<td class="formTd">
							<SELECT style="WIDTH:100px" name="sale_type" class="select">
								<OPTION value="-" selected>할인 쿠폰</OPTION>
								<OPTION value="+">적립 쿠폰</OPTION>
							</SELECT>
						<span class="font_orange"> * 할인쿠폰은 구매시 즉시 할인되며, 적립쿠폰은 구매시 추가 적립금이 지급됩니다.</span>
						</td>
					</tr>
					<tr>
						<th class="formTh"><img src="images/icon_point2.gif" width="8" height="11" border="0">금액/할인율 선택</th>
						<td class="formTd">
							<SELECT style="WIDTH: 100px" onchange=changerate(options.value); name=sale2 class="select">
								<OPTION value=원 selected>금액</OPTION> <OPTION value=%>할인(적립)율</OPTION>
							</SELECT>
							→
							<input onkeyup="strnumkeyup(this);" style="PADDING-RIGHT: 5px; TEXT-ALIGN: right" maxLength=10 size=10 name=sale_money class="input"> <input class="input_hide1" readOnly size=1 value=원 name=rate></td>
					</tr>
					<tr>
						<th class="formTh"><img src="images/icon_point2.gif" width="8" height="11" border="0">금액절사</th>
						<td class="td_con1">
						<SELECT disabled name=amount_floor class="select">
<?
					$arfloor = array(1=>"일원단위, 예)12344 → 12340","십원단위, 예)12344 → 12300","백원단위, 예)12344 → 12000","천원단위, 예)12344 → 10000");
					$arcnt = count($arfloor);
					for($i=1;$i<$arcnt;$i++){
						echo "<option value=\"".$i."\"";
						if($amount_floor==$i) echo " selected";
						echo ">".$arfloor[$i]."</option>";
					}
?>
					</SELECT>
						</td>
					</tr>
					<tr>
						<th class="formTh"><img src="images/icon_point2.gif" width="8" height="11" border="0">쿠폰 결제 금액</th>
						<td class="formTd">
							<input onclick=nomoney(1) type="radio" CHECKED name=checksale>제한 없음  &nbsp;
							<input onclick=nomoney(0) type="radio" name=checksale><input onkeyup=strnumkeyup(this); disabled maxLength=10 size=10 name=mini_price class="input_disabled">원 이상 주문시 가능		<SCRIPT>nomoney(1);</SCRIPT>
						</td>
					</tr>
					<tr>
						<th class="formTh"><img src="images/icon_point2.gif" width="8" height="11" border="0">쿠폰사용가능 결제방법</th>
						<td class="formTd">
							<input type="radio" CHECKED value=N name=bank_only>제한 없음  &nbsp;
							<input type="radio" value=Y name=bank_only><B>현금 결제</B>만 가능(실시간 계좌이체 포함)
						</td>
					</tr>
					<tr>
						<th class="formTh"><img src="images/icon_point2.gif" width="8" height="11" border="0">쿠폰 적용 상품군</th>
						<td class="formTd"><input style="width:400px" onclick="alert('변경은 [선택하기]버튼을 클릭하시면 됩니다.')" readOnly size="64" value="전체상품" name="productname" class="input"><a href="javascript:ChoiceProduct();"><img src="images/btn_select2.gif" width="76" height="28" border="0" hspace="2"></a></td>
					</tr>
					<tr><td height="1" bgcolor="#b9b9b9" colspan="2"></tr>
				</table>
				</div>
				<div style="width:100%">
				<TABLE border=0 cellSpacing=0 cellPadding=0 width="100%">
					<TR>
						<TD height=30 background=images/blueline_bg.gif align=center><B><a href="javascript:toggleTemplet()"><FONT color=#555555>템플릿 선택<span id="toggleTplStr"></span></FONT></a></B></TD>
					</TR>
					<tr>
						<td style="padding-top:5px; padding-bottom:5px;">
							<div id="templateArea" style="display:none">
							<span style="text-align:center; padding-right:10px;" class="tpItem" idx="0">Empty</span>
							<div style="clear:both; margin-top:5px;"><img src="/admin/images/market_bulkmail_templet_save.gif" border="0" class="saveTpl" alt="템플릿 저장" /></div>
							</div>
						</td>
					</tr>
				</TABLE>
				</div>
				<?
				$logoimg = $Dir.DataDir."shopimages/etc/logo.gif";
				$logoexist = (file_exists($logoimg) && filesize($logoimg) > 0);
				?>
				<table width="100%" border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td colspan="2" bgcolor="#fffaed" height="30">
							<div id="logoImgDiv" style="display:none; padding:2px 0px 2px 5px;">
							<table border="0" cellpadding="0" cellspacing="0">
								<tr>
									<th style="font-size:12px; width:120px;">로고 이미지 : </th>
									<td style="width:400px;"><input type="checkbox" name="useDefLogoimg" value="1"  <?=(!$logoexist)?'disabled="disabled"':''?> /> 기존이미지 사용 (<?=(!$logoexist)?'등록된 로고가 없습니다. 쇼핑몰 환경설정메뉴를 통해 등록 하실수 있습니다.':'쇼핑몰 환경설정에 <a href="'.$logoimg.'" target="_blank" style="color:blue">등록된 로고</a> 이미지사용'?>)  <br />업로드 이미지<input type="file" name="logoImg" value="" /></td>
									<td style="color:#ff4c00; padding-left:5px">* 내용의 [$LOGO] 를 업로드 이미지로 변경합니다.</td>
								</tr>
							</table>
							</div>
							<span style="color:#ff4c00">* 내용에 [$name] 를 넣으시면 해당 부분은 받는 사람의 이름으로 변환 됩니다.</span>
						</td>
					</tr>
					<tr>
						<td colspan="2"><textarea name="mailContents" style="width:98%; height:380px;" lang="ej-editor1"></textarea></td>
					</tr>
					<tr>
						<th class="formTh"><img src="images/icon_point2.gif" width="8" height="11" border="0">수신거부안내</th>
						<td class="formTd">
						<textarea name="rejectMsg" style="width:98%; height:50px;">본 메일은 정보통신망률 등 관련규정에 의거하여 수신동의하신 회원에게 발송되었습니다.
메일 수신을 원치 않으시면 <?=$bulkmail->shopurl?> 에 로그인후 마이페이지에서 설정을 변경해주세요.</textarea>
						</td>
					</tr>
					<tr><td height="1" bgcolor="#b9b9b9" colspan="2"></tr>
				</table>
				<div style="width:100%; text-align:center; padding-top:10px;">
				<!--<input type="button" value="불법 스팸방지 안내" onclick="javascript:sapmguide();" style="margin-right:5px;" /><input type="button" value="미리보기" onclick="javascript:preview()" style="margin-right:5px;" /><input type="submit" value="발송" />-->
					<table border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td><a href="javascript:sapmguide();"><img src="images/market_bulkmail_spam.gif" border="0"></a></td>
							<td style="padding-left:12px;"><a href="javascript:preview();"><img src="images/market_bulkmail_view.gif" border="0"></a></td>
							<td style="padding-left:12px;"><input type="image" src="images/market_bulkmail_send.gif"></td>
						</tr>
					</table>
				</div>
			</form>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td>
			<table width="100%" border=0 cellpadding=0 cellspacing=0>
				<tr>
					<td><IMG src="/admin/images/manual_top1.gif" width=15 height="45" ALT=""></td>
					<td><IMG src="/admin/images/manual_title.gif" width=113 height="45" ALT=""></td>
					<td width="100%" background="images/manual_bg.gif" height="35"></td>
					<td background="images/manual_bg.gif"></td>
					<td background="images/manual_bg.gif"><IMG src="/admin/images/manual_top2.gif" width=18 height="45" ALT=""></td>
				</tr>
				<tr>
					<td background="images/manual_left1.gif"></td>
					<td COLSPAN=3 width="100%" valign="top" bgcolor="white" style="padding-top:8pt; padding-bottom:8pt; padding-left:4pt;" class="menual_bg"> </td>
					<td background="images/manual_right1.gif"></td>
				</tr>
				<tr>
					<td><IMG src="/admin/images/manual_left2.gif" width=15 HEIGHT=8 ALT=""></td>
					<td COLSPAN=3 background="images/manual_down.gif"></td>
					<td><IMG src="/admin/images/manual_right2.gif" width=18 HEIGHT=8 ALT=""></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td height="50"></td>
	</tr>
</table>

<div id="templetFormDiv" style="display:none">
<form name="templetForm" id="templetForm">
<input type="hidden" name="tpidx" value="" />
<table border="0" cellpadding="0" cellspacing="0" class="formTbl" style="width:100%">
	<tr>
		<th style="width:100px;">템플릿 이름</th>
		<td><input type="text" name="title" value="" style="width:98%" /></td>
	</tr>
</table>
</form>
</div>
<script language="javascript" type="text/javascript">
function preview(){
	alert('실제 메일 수신시 운영 사이트 정책 및 특성에 따라 다소 차이가 있을수 있습니다.');
	var sf = $('#bulkMailform');
	var tf = $('#previewForm');

	$(tf).find('input[name=subject]').val($(sf).find('input[name=subject]').val()); // 제목
	$(tf).find('input[name=sender]').val($(sf).find('input[name=senderName]').val()+'('+$(sf).find('input[name=senderEmail]').val()+')'); // 제목
	$(tf).find('input[name=contents]').val($(sf).find('textarea[name=mailContents]').val()); // 제목
	$(tf).find('input[name=rejectMsg]').val($(sf).find('textarea[name=rejectMsg]').val()); // 제목

	var targetType = $(sf).find('input:radio[name=targetType]:checked').val();
	if(targetType == 'member'){
		if($(sf).find('input:radio[name=memberType]:checked').val() == 'membergroup'){
			$(tf).find('input[name=receiver]').val($('#membergroup_sel option:selected').html());
		}else{
			$(tf).find('input[name=receiver]').val($('#membergroup_sel option:selected').html());
		}
	}else if(targetType=='csv'){
		$(tf).find('input[name=receiver]').val('CSV업로드목록');
	}else if(targetType =='input'){
		$(tf).find('input[name=receiver]').val('직접 입력 발송 목록');
	}

	window.open("about:blank","mailPreview","width=800,height=700,scrollbars=no");
	$(tf).submit();
}

function sapmguide(){
	var guideWin = window.open("http://www.getmall.co.kr/front/mail/spamguide.php","spamguide","width=400,height=500");
}

function chgTarget(el){
	var trgarr = ['member','csv','input'];
	var tgname = $(el).val();
	for(i=0;i<trgarr.length;i++){
		name = trgarr[i];
		if(tgname == name){
			$('#'+name+'Set').css('display','');
		}else{
			$('#'+name+'Set').css('display','none');
		}
	}
}

function refMemberType(el){
	var trgarr = ['membergroup','mailgroup'];
	var tgname = $(el).val();
	for(i=0;i<trgarr.length;i++){
		name = trgarr[i];
		if(tgname == name){
			$('#'+name+'_sel').removeAttr('disabled');
		}else{
			$('#'+name+'_sel').attr('disabled','disabled');
		}
	}
}

function addInputRow(el){
	var email	= $('#new_email').val();
	var name	= $('#new_name').val();
	var mobile	= $('#new_mobile').val();
	var str = '';
	//if(email.match(/^(\w+)@(\w+)[.](\w+)$/ig) == null && email.match(/^(\w+)@(\w+)[.](\w+)[.](\w+)$/ig) == null){
	if(email.match(/^([a-zA-Z0-9])+([\.a-zA-Z0-9_-])*@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-]+)*\.([a-zA-Z]{2,6})$/ig) == null){
		alert('메일 주소가 올바르지 않습니다.');
		$('#new_email').focus();
	}else{
		str = '<tr><td><input type="text" name="email[]" value="'+email+'" style="border:0px" readonly /></td><td><input type="text" name="name[]"  value="'+name+'" style="border:0px" readonly /></td><td><input type="text" name="mobile[]"  value="'+mobile+'" style="border:0px" readonly /></td><td><input type="button"  onclick="javascript:removeInputRow(this)" value="삭제" /></td></tr>';
		$('#inputSet>table').append(str);
		$('#new_email').val('');
		$('#new_name').val('');
		$('#new_mobile').val('');
	}
}

function removeInputRow(el){
	$(el).parent().parent().remove();

}


var selectTpl=null;

function tplSelect(el){
	if(selectTpl && selectTpl != el){
		$(selectTpl).removeClass('tpSel');
		$(el).addClass('tpSel');
		var idx = parseInt($(el).attr('idx'));
		readTemplet(idx);
	}else{
		$(el).addClass('tpSel');
	}
	selectTpl = el;
}

function readTemplet(idx){
	idx = parseInt(idx);
	if(isNaN(idx) || idx < 1){
		parseTpHtml('mailContents','');
		$('#templetForm').find('input[name=tpidx]').val('');
		$('#templetForm').find('input[name=title]').val('');
	}else{
		$('#templetForm').find('input[name=tpidx]').val(idx);
		$.get('/admin/bulkmail/templet.process.php',{'act':'read','idx':idx},function(data){
			if(data.err != 'ok'){
				alert(data.err);
			}else{
				setTemplet(data.items);
			}
		},'json');
	}
}

function saveTemplet(isnew){
	//ejEdt2Html();
	ejEdtSetMode('mailContents','0');
	ejEdtSetMode('mailContents','1');
	if(isnew){
		var tpidx = '';
	}else{
		var tpidx = $('#templetForm').find('input[name=tpidx]').val();
	}
	var title = $('#templetForm').find('input[name=title]').val();
	var mailContents = $('textarea[name=mailContents]').html();

	$.post('/admin/bulkmail/templet.process.php',{'act':'modify','tpidx':tpidx,'title':title,'mailContents':mailContents},
		function(data){
			if(data.err != 'ok'){
				alert(data.err);
			}else{
				if($.trim(tpidx).length < 1) addTplItem(data.items);
				var sel = $('#templateArea').find('span[idx='+data.items.tpidx+']:eq(0)');
				$(sel).find('.tpTitle:eq(0)').text(data.items.title);
				tplSelect(sel);
				//setTemplet(data.items);
			}
			$( "#templetFormDiv").dialog('close');
		},'json');
}


function setTemplet(itm){
	if($.trim(itm.title).length) $('#templetForm').find('input[name=title]').val(itm.title);
	if($.trim(itm.mailContents).length) parseTpHtml('mailContents',itm.mailContents);
	//tplSelect($('#templateArea').find('span[idx='+itm.tpidx+']:eq(0)'));
}

function parseTpHtml(name,htmlstr){
	if($('textarea[name='+name+']')){
		$('textarea[name='+name+']').html(htmlstr);
		var iFrame =  document.getElementById('ejEdt_'+name);
		var iFrameBody;
		if ( iFrame.contentDocument) iFrameBody = iFrame.contentDocument.getElementsByTagName('body')[0];
		else if ( iFrame.contentWindow) iFrameBody = iFrame.contentWindow.document.getElementsByTagName('body')[0];
		$(iFrameBody).html(htmlstr);
	}
}

function reloadTplList(items){
	$('#templateArea').find('span.tpItem:gt(0)').remove();
	$.each(items,function(idx,itm){
		//var str = '<span style="text-align:center" class="tpItem" idx="'+itm.tpidx+'">'+itm.title+'</span>	';
		//$('#templateArea').find('span:last').after(str);
		addTplItem(itm);
	});
}

function addTplItem(itm){
	var str = '<span style="text-align:center" class="tpItem" idx="'+itm.tpidx+'"><span class="tpTitle">'+itm.title+'</span><img src="/admin/images/market_bulkmail_templet_delete.gif" class="delTpl" /></span>';
	$('#templateArea').find('span.tpItem:last').after(str);
}

function toggleTemplet(){
	if($('#templateArea').css('display') != 'none'){
		$('#templateArea').css('display','none');
		$('#toggleTplStr').html('[+ 펼치기]');
	}else{
		$('#templateArea').css('display','block');
		$('#toggleTplStr').html('[- 닫&nbsp;기]');
	}
}

function deleteTemplet(el){
	var tpidx = parseInt($(el).attr('idx'));
	if(isNaN(tpidx)){
		alert('고유 식별 번호가 올바르지 않습니다.');
	}else if(tpidx > 0 && confirm('정말 삭제하시겠습니까?')){
		$.post('/admin/bulkmail/templet.process.php',{'act':'delete','tpidx':tpidx},
			function(data){
				if(data.err != 'ok'){
					alert(data.err);
				}else{
					tplSelect($('.tpItem:eq(0)'));
					$(el).remove();
				}
			},'json');
	}
}

$(function(){
	$( "#templetFormDiv").dialog({autoOpen: false, height: 200,width: 350,modal: true,
	buttons:[
		{text:'저장',click:function(){saveTemplet();}},
		{text:'닫기',click:function(){$(this).dialog('close');}}
	]});

	toggleTemplet();
	$('input:radio[name="targetType"]:checked').each(function(index,el) {
		chgTarget(el);
	});

	$('input:radio[name="memberType"]:checked').each(function(index,el) {
		refMemberType(el)
	});

	$(document).on('click','.tpItem',function(){
		tplSelect(this);
	});

	$(document).on('click','.saveTpl',function(){
		var btns = [];
		var tpidx = parseInt($('#templetForm').find('input[name=tpidx]').val());

		if(isNaN(tpidx) || tpidx  < 1){
			btns.push({text:'등록',click:function(){saveTemplet();}});
		}else{
			btns.push({text:'변경',click:function(){saveTemplet();}});
			btns.push({text:'신규등록',click:function(){saveTemplet(true);}});
		}
		btns.push({text:'닫기',click:function(){$(this).dialog('close');}});

		$( "#templetFormDiv").dialog('option',"buttons",btns);
		$( "#templetFormDiv").dialog('open');
	});

	$.get('/admin/bulkmail/templet.process.php',{'act':'list'},function(data){
		$('#templateArea').find('span:gt(0)').remove();
		if(data.err != 'ok'){
			alert(data.err);
		}else{
			reloadTplList(data.items);
		}
	},'json');
	$(document).on('click','.delTpl',function(){
		deleteTemplet($(this).parent());
	});
	tplSelect($('.tpItem:eq(0)'));
});
</script>