<script language="javascript" type="text/javascript">
function CheckForm(form) {
	if(form.running.value=="1"){
		alert('코드 생성중 입니다. 잠시만 더 기다려 주세요.');
		return;
	}
	if(form.coupon_name.value.length==0) {
		alert("쿠폰 이름을 입력하세요.");
		form.coupon_name.focus();
		return;
	}
	if(CheckLength(form.coupon_name)>100) {
		alert("입력할 수 있는 허용 범위가 초과되었습니다.\n\n" + "한글 50자 이내 혹은 영문/숫자/기호 100자 이내로 입력이 가능합니다.");
		form.coupon_name.focus();
		return;
	}
	content ="아래의 사항을 확인하시고, 등록하시면 됩니다.\n\n"
			 +"--------------------------------------------\n\n"
			 +"* 쿠폰 이름 : "+form.coupon_name.value+"\n\n";
	
	if (form.time[0].checked==true) {
		date = "<?=date("Y-m-d");?>";
		if (form.date_start.value<date || form.date_end.value<date || form.date_start.value>form.date_end.value) {
			alert("쿠폰 유효기간 설정이 잘못되었습니다.\n\n다시 확인하시기 바랍니다.");
			form.date_start.focus();
			return;
		}
		content+="* 쿠폰 유효기간 : "+form.date_start.value+" ~ "+form.date_end.value+" 까지\n\n";
	} else {
		if (form.peorid.value.length==0) {
			alert("쿠폰 사용기간을 입력하세요.");
			form.peorid.focus();
			return;
		} else if (!IsNumeric(document.form1.peorid.value)) {
			alert("쿠폰 사용기간은 숫자만 입력 가능합니다.");
			form.peorid.focus();
			return;
		}
		content+="* 쿠폰 사용기간 : "+form.peorid.value+"일 동안\n\n";
	}
	if (form.sale_money.value.length==0) {
		alert("쿠폰 할인 금액/할인률을 입력하세요.");
		form.sale_money.focus();
		return;
	} else if (!IsNumeric(form.sale_money.value)) {
		alert("쿠폰 할인 금액/할인률은 숫자만 입력 가능합니다.(소숫점 입력 안됨)");
		form.sale_money.focus();
		return;
	}
	if(form.sale2.selectedIndex==1 && form.sale_money.value>=100){
		alert("쿠폰 할인률은 100보다 작아야 합니다.");
		form.sale_money.focus();
		return;
	}
	content+="* 쿠폰종류 : "+form.sale_type.options[form.sale_type.selectedIndex].text+"\n\n";
	content+="* 쿠폰 금액/할인률 : "+form.sale_money.value+form.sale2.options[form.sale2.selectedIndex].value+"\n\n";
	if(form.bank_only[0].checked==true) content+="* 쿠폰 사용가능 결제방법 : 제한 없음\n\n";
	else content+="* 쿠폰 사용가능 결제방법 : 현금 결제만 가능(실시간 계좌이체 포함)\n\n";

	if(form.order_limit[0].checked==true) content+="* 단일 주문 중복사용 : 제한 없음\n\n";
	else content+="* 단일 주문 중복사용 : 사용불가\n\n";

	document.form1.productcode.value="";
	if(document.form1.codegbn[0].checked==true) {
		document.form1.productcode.value="ALL";
	} else {
		cnt=document.form1.codelist.options.length - 1;
		if(cnt<=0) {
			alert("쿠폰 적용 상품군을 선택하세요.");
			return;
		}
		for(i=1;i<=cnt;i++) {
			document.form1.productcode.value+=document.form1.codelist.options[i].value+",";
			/*
			if(i==1) {
				document.form1.productcode.value+=document.form1.codelist.options[i].value;
			} else {
				document.form1.productcode.value+=","+document.form1.codelist.options[i].value;
			}
			*/
		}
	}

	if(form.productcode.value.length==18 && form.checksale[1].checked==true && form.use_con_type2.checked!=true) {
		alert("쿠폰이 한상품에 적용될경우 구매금액에 제한이 없습니다.");
		nomoney(1);
	}
	if(form.checksale[1].checked==true){
		if(form.mini_price.value.length==0){
			alert("쿠폰 결제 금액을 입력하세요.");
			document.form1.mini_price.focus();
			return;
		}else if(!IsNumeric(form.mini_price.value)){
			alert("쿠폰 결제 금액은 숫자만 입력 가능합니다.");
			form.mini_price.focus();
			return;
		}
		content+="* 쿠폰 결제 금액 : "+form.mini_price.value+"원 이상 구매시\n\n";
	} else {
		content+="* 쿠폰 결제 금액 : 제한없음\n\n";
	}
	//content+="* 적용상품군 : "+form.productname.value+"\n\n";
	if(form.etcapply_gift.checked==true) {
		content+="* 사은품제외여부 : 본 쿠폰을 사용할 경우 사은품을 지급하지 않음\n\n";
	}
	if(form.issue_tot_no.value.length==0){
		alert("쿠폰 발행수를 입력하세요.");
		form.issue_tot_no.focus();
		return;
	}else if(!IsNumeric(form.issue_tot_no.value)){
		alert("쿠폰 발행수는 숫자만 입력 가능합니다.(소숫점 입력 안됨)");
		form.issue_tot_no.focus();
		return;
	}else if(form.issue_tot_no.value<=0) {
		alert("쿠폰 발행 매수를 입력하세요.");
		form.issue_tot_no.focus();
		return;
	}
	content+="* 발행 쿠폰수 : "+form.issue_tot_no.value+"개\n\n";
	

	
	content+="* 발급조건 : 오프라인페이퍼쿠폰\n\n";
	content+="* 제한사항 : 등급할인혜택과 동시사용 "+form.use_point.options[form.use_point.selectedIndex].text+"\n\n";
	
	content+="--------------------------------------------";
	if(confirm(content)){
		alert('발급수가 많을 경우 시간이 오래 소요될수 있습니다. 페이지를 닫거나 새로고치지 마시고 완료 메시지가 나올때 까지 기다려 주세요');
		form.running.value="1";
		form.type.value="insert";
		form.submit();
	}
}
function changerate(rate){  
	document.form1.rate.value=rate;
	if(rate=="%") {
		document.form1.amount_floor.disabled=false;
	} else {
		document.form1.amount_floor.disabled=true;
	}
}
function nomoney(temp){  
	if(temp==1){
		document.form1.mini_price.value="";
		document.form1.mini_price.disabled=true;
		document.form1.mini_price.style.background='#F0F0F0';
		document.form1.checksale[0].checked=true;
	} else {
		document.form1.mini_price.value="0";
		document.form1.mini_price.disabled=false;
		document.form1.mini_price.style.background='white';
		document.form1.checksale[1].checked=true;
	}
}


function ChoiceProduct(){
	window.open("about:blank","coupon_product","width=245,height=140,scrollbars=no");
	document.form2.submit();
}

function ChangeCodegbn(gbn) {
	if(gbn=="A") {
		if(document.all){
			document.all["layer_codelist"].style.display="none";
		} else if(document.getElementById){
			document.getElementByld["layer_codelist"].style.display="none";
		} else if(document.layers){
			document.layers["layer_codelist"].display="none";
		}
		ViewLayer('layer1','none');
	} else if(gbn=="N") {
		if(document.all){
			document.all["layer_codelist"].style.display="";
		} else if(document.getElementById){
			document.getElementByld["layer_codelist"].style.display="";
		} else if(document.layers){
			document.layers["layer_codelist"].display="";
		}
		ViewLayer('layer1','block');
	}
}

function CodeDelete() {
	codelist=document.form1.codelist;
	for(i=1;i<codelist.options.length;i++) {
		if(codelist.options[i].selected==true){
			codelist.options[i]=null;
			cnt=codelist.options.length - 1;
			codelist.options[0].text = "------------------------- 적용 상품군을 선택하세요. -------------------------";
			return;
		}
	}
	alert("삭제할 상품군을 선택하세요.");
	codelist.focus();
}

function ViewLayer(layer,display){
	if(document.all){
		document.all[layer].style.display=display;
	} else if(document.getElementById){
		document.getElementByld[layer].style.display=display;
	} else if(document.layers){
		document.layers[layer].display=display;
	}
}


</script>

<form name=form1 action="/admin/offcoupon/process.php" method=post>
	<input type=hidden name=type>
	<input type="hidden" name="running" value="0" />
	<input type="hidden" name="act" value="new">
	<input type=hidden name=productcode value="ALL">
	<input type="hidden" name="issue_type" value="P">
	<input type="hidden" name="repeat_id" value="N">
	<input type="hidden" name="repeat_ok" value="N" />
	<input type="hidden" name="description" value="오프라인페이퍼쿠폰">
	<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
		<tr>
			<td height="8"></td>
		</tr>
		<tr>
			<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
					<TR>
						<TD><IMG SRC="images/market_couponnew_title.gif" ALT=""></TD>
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
						<TD width="100%" class="notice_blue">쿠폰 번호를 통한 인증 방식의 쿠폰 발행을 통해 인쇄 매체 등을 이용한 오프라인 쿠폰을 발행합니다.</TD>
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
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
					<TR>
						<TD><IMG SRC="images/market_couponnew_stitle1.gif" WIDTH="192" HEIGHT=31 ALT=""></TD>
						<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif"></TD>
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
						<TD><IMG SRC="images/distribute_01.gif" WIDTH=7 HEIGHT=7 ALT=""></TD>
						<TD COLSPAN=2 background="images/distribute_02.gif"></TD>
						<TD><IMG SRC="images/distribute_03.gif" WIDTH=8 HEIGHT=7 ALT=""></TD>
					</TR>
					<TR>
						<TD background="images/distribute_04.gif"></TD>
						<TD class="notice_blue"><IMG SRC="images/distribute_img.gif"></TD>
						<TD width="100%" class="notice_blue">쿠폰 사용은 한 주문건에 대해서 한개의 쿠폰만 사용이 가능합니다.</TD>
						<TD background="images/distribute_07.gif"></TD>
					</TR>
					<TR>
						<TD><IMG SRC="images/distribute_08.gif" WIDTH=7 HEIGHT=8 ALT=""></TD>
						<TD COLSPAN=2 background="images/distribute_09.gif"></TD>
						<TD><IMG SRC="images/distribute_10.gif" WIDTH=8 HEIGHT=8 ALT=""></TD>
					</TR>
				</TABLE>
			</td>
		</tr>
		<tr>
			<td height=3></td>
		</tr>
		<tr>
			<td>
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0 style="table-layout:fixed">
					<col width=160>
					
							</col>
					
					<col width=>
					
							</col>
					
					<TR>
						<TD colspan=2 background="images/table_top_line.gif"></TD>
					</TR>
					<TR>
						<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">쿠폰 이름</TD>
						<TD class="td_con1">
							<INPUT maxLength=100 size=70 name=coupon_name class="input">
							<br>
							<span class="font_orange"><b>예)새 봄맞이10% 할인쿠폰이벤트~</b></span></TD>
					</TR>
					<TR>
						<TD colspan="2" background="images/table_con_line.gif"></TD>
					</TR>
					<TR>
						<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">유효기간</TD>
						<TD class="td_con1">
							<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
								<TR>
									<TD >
										<INPUT type=radio value=D name=time>
										기간설정 :
										<INPUT onfocus=this.blur(); onclick=Calendar(this) size=12 name=date_start value="<?=date('Y-m-d')?>" class="input_selected">
										부터
										<INPUT  onfocus=this.blur(); onclick=Calendar(this) size=12 name=date_end value="<?=date('Y-m-d',strtotime('+1 week'))?>" class="input_selected">
										까지 사용가능<span class="font_orange">(유효기간 마지막일 23시59분59초 까지)</span> </TD>
								</TR>
								<TR>
									<TD >
										<INPUT type=radio CHECKED value=P name=time>
										발행 후
										<INPUT onkeyup=strnumkeyup(this); style="PADDING-RIGHT: 3px; TEXT-ALIGN: right" maxLength=3 size=4 name=peorid class="input">
										일 동안 사용가능<span class="font_orange">(유효기간 마지막일 23시59분59초 까지)</span> </TD>
								</TR>
							</TABLE>
						</TD>
					</TR>
					<TR>
						<TD colspan="2" background="images/table_con_line.gif"></TD>
					</TR>
					<TR>
						<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">쿠폰종류 선택</TD>
						<TD class="td_con1">
							<SELECT style="WIDTH: 100px" name=sale_type class="select">
								<OPTION value=- selected>할인 쿠폰</OPTION>
								<OPTION value=+>적립 쿠폰</OPTION>
							</SELECT>
							<span class="font_orange"> * 할인쿠폰은 구매시 즉시 할인되며, 적립쿠폰은 구매시 추가 적립금이 지급됩니다.</span> </TD>
					</TR>
					<TR>
						<TD colspan="2" background="images/table_con_line.gif"></TD>
					</TR>
					<TR>
						<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">금액/할인율 선택</TD>
						<TD class="td_con1">
							<SELECT style="WIDTH: 100px" onchange=changerate(options.value); name=sale2 class="select">
								<OPTION value=원 selected>금액</OPTION>
								<OPTION value=%>할인(적립)율</OPTION>
							</SELECT>
							→
							<INPUT onkeyup=strnumkeyup(this); style="PADDING-RIGHT: 5px; TEXT-ALIGN: right" maxLength=10 size=10 name=sale_money class="input">
							<INPUT class="input_hide1" readOnly size=1 value=원 name=rate>
						</TD>
					</TR>
					<TR>
						<TD colspan="2" background="images/table_con_line.gif"></TD>
					</TR>
					<TR>
						<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">금액절사</TD>
						<TD class="td_con1">
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
						</TD>
					</TR>
					<TR>
						<TD colspan="2" background="images/table_con_line.gif"></TD>
					</TR>
					<tr>
						<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">쿠폰 결제 금액</TD>
						<TD class="td_con1">
							<INPUT onclick=nomoney(1) type=radio CHECKED name=checksale>
							제한 없음  &nbsp;
							<INPUT onclick=nomoney(0) type=radio name=checksale>
							<INPUT onkeyup=strnumkeyup(this); disabled maxLength=10 size=10 name=mini_price class="input_disabled">
							원 이상 주문시 가능 
							<SCRIPT>nomoney(1);</SCRIPT> 
						</TD>
					</tr>
					<TR>
						<TD colspan="2" background="images/table_con_line.gif"></TD>
					</TR>
					<tr>
						<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">쿠폰사용가능 결제방법</TD>
						<TD class="td_con1">
							<INPUT type=radio CHECKED value=N name=bank_only>
							제한 없음  &nbsp;
							<INPUT type=radio value=Y name=bank_only>
							<B>현금 결제</B>만 가능(실시간 계좌이체 포함) </TD>
					</tr>
					<TR>
						<TD colspan=2 background="images/table_top_line.gif"></TD>
					</TR>
					<tr>
						<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">단일 주문 중복 사용</TD>
						<TD class="td_con1">
							<INPUT type=radio CHECKED value=N name=order_limit>
							제한 없음  &nbsp;
							<INPUT type=radio value=Y name=order_limit>
							사용불가 
					</tr>
					<TR>
						<TD colspan=2 background="images/table_top_line.gif"></TD>
					</TR>
					<tr>
						<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">사은품 제외여부</TD>
						<TD class="td_con1">
							<input type=checkbox name=etcapply_gift value=A>
							본 쿠폰을 사용할 경우 사은품을 지급하지 않습니다. </TD>
					</tr>
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
						<TD><IMG SRC="images/market_couponnew_stitle2.gif" WIDTH="192" HEIGHT=31 ALT=""></TD>
						<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif"></TD>
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
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0 style="table-layout:fixed">
					<col width=160>
					
							</col>
					
					<col width=>
					
							</col>
					
					<TR>
						<TD colspan=2 background="images/table_top_line.gif"></TD>
					</TR>
					<TR>
						<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">쿠폰 적용 상품군 선택</TD>
						<TD class="td_con1">
							<table cellpadding="0" cellspacing="0" width="100%">
								<tr>
									<td>
										<input type=radio name=codegbn value="A" checked onclick="ChangeCodegbn('A')">
										전체상품
										&nbsp;&nbsp;
										<input type=radio name=codegbn value="N" onclick="ChangeCodegbn('N')">
										일부 카테고리/상품 </td>
								</tr>
								<tr>
									<td height=5></td>
								</tr>
								<tr>
									<td id=layer_codelist style="display:none">
										<table border=0 cellpadding=0 cellspacing=0 width=100%>
											<tr>
												<td>
													<select name=codelist size=10 style="WIDTH:470px" class="select">
														<option value="" style="BACKGROUND-COLOR: #ffff00">------------------------- 적용 상품군을 선택하세요. -------------------------</option>
													</select>
												</td>
												<td valign=top nowrap> <a href="javascript:ChoiceProduct();"><img src="images/btn_add1.gif" hspace="2"></a> &nbsp; <a href="javascript:CodeDelete();"><img src="images/btn_del.gif" hspace="2"></a> </td>
											</tr>
										</table>
									</td>
								</tr>
							</table>
						</TD>
					</TR>
					<TR>
						<TD colspan="2" background="images/table_con_line.gif"><img src="images/table_con_line.gif" width="4" height="1" border="0"></TD>
					</TR>
					<TR>
						<TD colspan="2">
							<div id=layer1 style="margin-left:0;display:hide; display:none;border-style:solid; border-width:0; border-color:black;background:#FFFFFF;padding:0;">
								<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0 style="table-layout:fixed">
									<col width=160>
									
											</col>
									
									<col width=>
									
											</col>
									
									<TR>
										<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">쿠폰 사용조건</TD>
										<TD class="td_con1">
											<INPUT type=checkbox CHECKED value=Y name=use_con_type1>
											다른 상품과 함께 구매시에도, 해당 쿠폰을 사용합니다.<BR>
											<INPUT type=checkbox value=N name=use_con_type2>
											선택된 카테고리(상품)을 제외하고 적용합니다. </TD>
									</TR>
									<TR>
										<TD colspan="2" background="images/table_con_line.gif"><img src="images/table_con_line.gif" width="4" height="1" border="0"></TD>
									</TR>
									</TABLE>									
							</div>
						</td>
					</tr>
					<tr>
						<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">발행 쿠폰 수</TD>
						<TD class="td_con1">
							<INPUT onkeyup="strnumkeyup(this);" maxLength="10" size="10" name="issue_tot_no" value="" class="input">
							매 </TD>
					</tr>
					<TR>
						<TD colspan="2" background="images/table_con_line.gif"></TD>
					</TR>
					<TR>
						<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">쿠폰 적용 제한&nbsp;사항</TD>
						<TD class="td_con1"> 쿠폰과 등급회원 할인/적립 혜택 동시
							<SELECT name=use_point class="select">
								<OPTION value=Y selected>적용함</OPTION>
								<OPTION value=A>적용안함</OPTION>
							</SELECT>
						</TD>
					</TR>
					<TR>
						<TD colspan="2" background="images/table_con_line.gif"></TD>
					</TR>
				</TABLE>
			</td>
		</tr>
		<tr>
			<td height=10></td>
		</tr>
		<tr>
			<td align=center><a href="javascript:CheckForm(document.form1);"><img src="images/btn_cupon.gif" width="139" height="38" border="0"></a> <a href="javascript:document.location.replace='/admin/offlinecoupon.php';"><img src="images/btn_list.gif" border="0" alt="목록으로"></a></td>
		</tr>
		<tr>
			<td height="25">&nbsp;</td>
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
								<col width=20>
								
										</col>
								
								<col width=>
								
										</col>
								
								<tr>
									<td align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
									<td>쿠폰 사용은 한번의 주문건에서만 사용할 수 있습니다.</td>
								</tr>
								<tr>
									<td align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
									<td>쿠폰사용 선택 : ① 할인쿠폰은 구매시 즉시 할인됩니다.</td>
								</tr>
								<tr>
									<td align="right" valign="top">&nbsp;</td>
									<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;② 적립쿠폰은 구매시 추가 적립금이 지급됩니다.</td>
								</tr>
								<tr>
									<td align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
									<td>쿠폰상품 선택 :모든상품,일부카테고리,일부상품 으로 구분 됩니다.</td>
								</tr>
								<tr>
									<td align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
									<td>쿠폰사용은 로그인 후 마이페이지 쿠폰관리에서 쿠폰 번호 인증 후 정보 확인 및 사용 할 수 있습니다.</td>
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
</form>
<form name=form2 action="coupon_productchoice.php" method=post target=coupon_product>
</form>
