<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once '../lib/class/coupon.php';
INCLUDE ("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "ma-3";
$MenuCode = "market";
if (!$_usersession->isAllowedTask($PageCode)) {
	INCLUDE ("AccessDeny.inc.php");
	exit;
}
#########################################################

$popup=$_REQUEST["popup"];

$gubun=$_REQUEST["gubun"];

$memberID=$_REQUEST["memberID"];

$groupCode=$_REQUEST["grp"];


/*
$CurrentTime = time();
$date_start=$_POST["date_start"];
$date_end=$_POST["date_end"];
$date_start=$date_start?$date_start:date("Y-m-d",$CurrentTime);
$date_end=$date_end?$date_end:date("Y-m-d",$CurrentTime);

$type=$_POST["type"];
$productcode=$_POST["productcode"];
$coupon_name=$_POST["coupon_name"];
$time=$_POST["time"];
$peorid=$_POST["peorid"];
$sale_type=$_POST["sale_type"];
$sale2=$_POST["sale2"];
$sale_money=$_POST["sale_money"];
$amount_floor=$_POST["amount_floor"];
$mini_price=$_POST["mini_price"];
$bank_only=$_POST["bank_only"];
$order_limit=$_POST["order_limit"];
$use_con_type1=$_POST["use_con_type1"];
$issue_type=$_POST["issue_type"];
$detail_auto=$_POST["detail_auto"];
$issue_tot_no=$_POST["issue_tot_no"];
$repeat_id=$_POST["repeat_id"];
$repeat_ok=$_POST["repeat_ok"];
$description=$_POST["description"];
$use_point=$_POST["use_point"];
$etcapply_gift=$_POST["etcapply_gift"];
$couponimg=$_FILES["couponimg"];

$imagepath=$Dir.DataDir."shopimages/etc/";
if ($type=="insert") {
	$coupon_code=substr(ceil(date("sHi").date("ds")/10*8)."000",0,8);
	if($couponimg[size] < 153600) {
		if (strlen($couponimg[name])>0 && file_exists($couponimg[tmp_name])) {
			$ext = strtolower(substr($couponimg[name],strlen($couponimg[name])-3,3));
			if ($ext=="gif") {
				$imagename = "COUPON".$coupon_code.".gif";
				move_uploaded_file($couponimg[tmp_name],$imagepath.$imagename);
				chmod($imagepath.$imagename,0666);
			} else {
				echo "<script>alert('쿠폰 이미지 파일은 GIF 파일만 등록 가능합니다.');history.go(-1);</script>";
			}
		}
	} else {
		echo "<script>alert('쿠폰 이미지 파일 용량이 초과되었습니다.\\n\\nGIF 파일 150KB 이하로 올려주시기 바랍니다.');history.go(-1);</script>";
	}

	if(strlen($mini_price)==0) $mini_price=0;
	if(strlen($use_con_type1)==0 || $productcode=="ALL") $use_con_type1="N";
	if(strlen($use_con_type2)==0 || $productcode=="ALL") $use_con_type2="Y";
	if(strlen($repeat_id)==0) $repeat_id="N";
	if(strlen($issue_tot_no)==0) $issue_tot_no=0;
	if(strlen($sale_money)==0) $sale_money=0;
	if($sale_type=="+" && $sale2=="%") $realsale=1;
	else if($sale_type=="-" && $sale2=="%") $realsale=2;
	else if($sale_type=="+" && $sale2=="원") $realsale=3;
	else if($sale_type=="-" && $sale2=="원") $realsale=4;
	if ($time=="D") {
		$date_start = str_replace("-","",$date_start)."00";
		$date_end = str_replace("-","",$date_end)."23";
	} else {
		$date_start = "-".$peorid;
		$date_end = "";
	}

	if($etcapply_gift!="A") $etcapply_gift="";

	$sql = "INSERT tblcouponinfo SET ";
	$sql.= "coupon_code		= '".$coupon_code."', ";
	$sql.= "coupon_name		= '".$coupon_name."', ";
	$sql.= "date_start		= '".$date_start."', ";
	$sql.= "date_end		= '".$date_end."', ";
	$sql.= "sale_type		= '".$realsale."', ";
	$sql.= "sale_money		= ".$sale_money.", ";
	$sql.= "amount_floor	= '".$amount_floor."', ";
	$sql.= "mini_price		= ".$mini_price.", ";
	$sql.= "bank_only		= '".$bank_only."', ";
	$sql.= "order_limit		= '".$order_limit."', ";
	$sql.= "productcode		= '".$productcode."', ";
	$sql.= "use_con_type1	= '".$use_con_type1."', ";
	$sql.= "use_con_type2	= '".$use_con_type2."', ";
	$sql.= "issue_type		= '".$issue_type."', ";
	$sql.= "detail_auto		= '".$detail_auto."', ";
	$sql.= "issue_tot_no	= ".$issue_tot_no.", ";
	$sql.= "repeat_id		= '".$repeat_id."', ";
	$sql.= "description		= '".$description."', ";
	$sql.= "use_point		= '".$use_point."', ";
	$sql.= "member			= '".($issue_type!="N"?"ALL":"")."', ";
	$sql.= "etcapply_gift	= '".$etcapply_gift."', ";
	$sql.= "display			= '".($issue_type!="N"?"Y":"N")."', ";
	$sql.= "date			= '".date("YmdHis")."' ";

	mysql_query($sql,get_db_conn());

	if($issue_type!="N") $url = "market_couponlist.php";
	else $url = "market_couponsupply.php";

	echo "<body onload=\"location.href='$url';\"></body>";
	exit;
}*/

$coupon = new coupon();
if($_POST['type'] == 'insert'){
	$_POST['coupon_code'] = $coupon->_genCouponcode();
	$imgresult = $coupon->_couponImg($result['coupon_code'],$_FILES["couponimg"]);
	if($imgresult == 'notgif'){
		echo "<script>alert('쿠폰 이미지 파일은 GIF 파일만 등록 가능합니다.');history.go(-1);</script>";
		exit;
	}else if($imgresult == 'sizeover'){
		echo "<script>alert('쿠폰 이미지 파일 용량이 초과되었습니다.\\n\\nGIF 파일 150KB 이하로 올려주시기 바랍니다.');history.go(-1);</script>";
		exit;
	}
	$result = $coupon->_new($_POST);

	if($result['result']){

		//if( $popup == "OK" ) {
			switch( $gubun ){
				case "MEMBER" :
					$url = "market_couponsupply.php?popup=".$popup."&memberID=".$memberID."&gubun=".$gubun."&coupon_code=".$result['coupon_code'];
					break;
				case "GROUP" :
					$url = "member_groupnew_couponAddPop.php?popup=".$popup."&grp=".$groupCode;
					break;
				default :
					$url=($result['issue_type']!="N")? "market_couponlist.php" : "market_couponsupply.php" ;
				break;
			}
		//}
		echo "<body onload=\"location.href='$url';\"></body>";
		exit;
	}
}

include "header.php"; ?>
<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="calendar.js.php"></script>
<script type="text/javascript" src="/js/jquery-1.7.2.min.js"></script>
<script language="javascript" type="text/javascript">
$j = jQuery.noConflict();
</script>
<script language="JavaScript">
function CheckForm(form) {
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

	if(form.detail_auto[0].checked==true && form.issue_type[1].checked!=true) {
		content+="* 상품 상세페이지 자동노출 : 노출함\n\n";
	} else if(form.issue_type[1].checked!=true) {
		content+="* 상품 상세페이지 자동노출 : 노출안함\n\n";
	}

	if(form.description.value.length==0) {
		alert("쿠폰 설명을 입력하세요.");
		form.description.focus();
		return;
	}
	if(CheckLength(form.description)>100) {
		alert("입력할 수 있는 허용 범위가 초과되었습니다.\n\n" + "한글 50자 이내 혹은 영문/숫자/기호 100자 이내로 입력이 가능합니다.");
		form.description.focus();
		return;
	}
	if((form.issue_type[0].checked==true || form.issue_type[2].checked==true) && form.checknum[1].checked==true){
		alert("즉시 발급시,회원 가입시 쿠폰 발행의 경우 발행 쿠폰수에 제한이 없습니다.");
		nonum(1);
	}
	if(form.checknum[1].checked==true){
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
	} else {
		content+="* 발행 쿠폰수 : 무제한\n\n";
	}

	if(form.repeat_id[0].checked==true){
		 content +="* 중복 다운로드 : 가능\n\n";
	} else {
		 content +="* 중복 다운로드 : 불가능\n\n";
	}


	/*
	if(form.repeat_ok[0].checked==true){
		 content +="* 쿠폰 사용 후 자동 재발급 : 사용\n\n";
	} else {
		 content +="* 쿠폰 사용 후 자동 재발급 : 사용안함\n\n";
	}
	*/

	//content+="* 적용상품군 : "+form.productname.value+"\n\n";
	if(form.etcapply_gift.checked==true) {
		content+="* 사은품제외여부 : 본 쿠폰을 사용할 경우 사은품을 지급하지 않음\n\n";
	}


	if(form.issue_type[0].checked==true) tempmsg ="즉시 발급용 쿠폰";
	else if(form.issue_type[1].checked==true) tempmsg ="쿠폰 클릭시 발급";
	else if(form.issue_type[2].checked==true) tempmsg ="회원 가입시 발급";
	content+="* 발급조건 : "+tempmsg+"\n\n";
	//content+="* 제한사항 : 등급할인혜택과 동시사용 "+form.use_point[form.use_point.selectedIndex].text+"\n\n";
	if(form.use_point[1].checked==true) content +="* 등급별 혜택: 회원등급할인과 동시 적용 불가\n\n";

	if(form.useimg[0].checked==true){
		form.couponimg.value="";
		content+="* 쿠폰이미지 : 기본이미지\n\n";
	} else if(form.useimg[1].checked==true && form.couponimg.value.length==0){
		alert("쿠폰 이미지를 등록하세요.");
		form.couponimg.focus();
		return;
	} else {
		content+="* 쿠폰이미지 : 선택 이미지 등록\n\n";
	}
	content+="--------------------------------------------";
	if(confirm(content)){
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
function nonum(temp){
	if(temp==1){
		document.form1.issue_tot_no.value="";
		document.form1.issue_tot_no.disabled=true;
		document.form1.issue_tot_no.style.background='#F0F0F0';
		document.form1.checknum[0].checked=true;
	} else {
		document.form1.issue_tot_no.value="0";
		document.form1.issue_tot_no.disabled=false;
		document.form1.issue_tot_no.style.background='white';
		document.form1.checknum[1].checked=true;
	}
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

function toggleDownType(bool){
	if(bool){
		$j('.downOnly').css('display','');
	}else{
		$j('.downOnly').css('display','none');
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


</script>

<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
	<tr>
		<td valign="top">
			<table cellpadding="0" cellspacing="0" width=100% style="table-layout:fixed">
				<tr>
					<td>
						<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed"  background="images/con_bg.gif">
							<?
								if( $popup != "OK" ) {
							?>
							<col width=198></col>
							<col width=10></col>
							<?
								}
							?>
							<col width=></col>
							<tr>
								<?
									if( $popup != "OK" ) {
								?>
								<td valign="top"  background="images/leftmenu_bg.gif"><? include ("menu_market.php"); ?></td>
								<td></td>
								<?
									}
								?>
								<td valign="top">
									<table cellpadding="0" cellspacing="0" width="100%">
										<?
											if( $popup != "OK" ) {
										?>
										<tr>
											<td height="29" colspan="3">
												<table cellpadding="0" cellspacing="0" width="100%">
													<tr>
														<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 마케팅지원 &gt; 쿠폰발행 서비스 설정 &gt; <span class="2depth_select">새로운 쿠폰 생성하기</span></td>
													</tr>
												</table>
											</td>
										</tr>
										<?
											}
										?>
										<tr>
											<td width="16"><img src="images/con_t_01.gif" width="16" height="16" border="0"></td>
											<td background="images/con_t_01_bg.gif"></td>
											<td width="16"><img src="images/con_t_02.gif" width="16" height="16" border="0"></td>
										</tr>
										<tr>
											<td width="16" background="images/con_t_04_bg1.gif"></td>
											<td bgcolor="#ffffff" style="padding:10px">
												<a href="javascript:document.location.reload()">새로고침</a>
												<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post enctype="multipart/form-data">
													<input type=hidden name=popup value="<?=$popup?>">
													<input type=hidden name=memberID value="<?=$memberID?>">
													<input type=hidden name=gubun value="<?=$gubun?>">
													<input type=hidden name=type>
													<input type=hidden name=productcode value="ALL">
													<div style="padding-bottom:21px; background:url(images/title_bg.gif) repeat-x bottom left; margin-top:8px; margin-bottom:3px;"> <IMG SRC="images/market_couponnew_title.gif" ALT=""> </div>
													<div style="padding-top:3px; margin-bottom:20px; padding-left:25px;" class="notice_blue">회원들에게 자유롭게 쿠폰발행 서비스를 진행할 수 있습니다.
													<br><span class="font_orange">입점몰의 경우 쿠폰발행 주체에서 할인에 대한 비용을 부담합니다.(쇼핑몰 운영사에서 입점사 상품에 발행 시 쿠폰할인금액이 정산금액에서 공제되지 않음)</span></div>
													<style type="text/css">
													.cinputTbl{ border-top:1px solid #ccc;  border-bottom:1px solid #ccc; margin-bottom:15px;}
													.cinputTbl th{ background:#f8f8f8 url(images/icon_point2.gif) no-repeat 15px 50%; padding:3px 0px 3px 25px; border-bottom:1px solid #efefef; border-right:1px solid #efefef; text-align:left; font-size:12px;}
													.cinputTbl td{ border-bottom:1px solid #efefef; padding:3px; empty-cells:show}
													</style>
													<div><IMG SRC="images/market_couponnew_stitle1.gif" WIDTH="192" HEIGHT=31 ALT="">
														<p class="notice_blue" style="padding-left:25px;">쿠폰 사용은 한 주문건에 대해서 한개의 쿠폰만 사용이 가능합니다.</p>
													</div>
													<table class="cinputTbl" width="100%" border="0"  cellpadding="0" cellspacing="0">
														<tr>
															<th style="width:160px">쿠폰 이름</th>
															<td>
																<INPUT maxLength=100 size=70 name=coupon_name class="input">
																<br>
																<span class="font_orange"><b>예)새 봄맞이10% 할인쿠폰이벤트~</b></span></td>
														</tr>
														<tr>
															<th>쿠폰 설명</th>
															<td>
																<INPUT maxLength=200 size=91 name=description style=width:99% class="input">
																<span class="font_orange"> * 입력한 쿠폰설명은 쿠폰이미지 상단에 출력됩니다.</span></td>
														</tr>
														<tr>
															<th>유효기간</th>
															<td>
																<div>
																	<INPUT type=radio value=D name=time>
																	기간설정 :
																	<INPUT onfocus=this.blur(); onclick=Calendar(this) size=10 name=date_start value="<?=$date_start?>" class="input_selected">
																	부터
																	<INPUT  onfocus=this.blur(); onclick=Calendar(this) size=10 name=date_end value="<?=$date_end?>" class="input_selected">
																	까지 사용가능<span class="font_orange">(유효기간 마지막일 23시59분59초 까지)</span> </div>
																<div>
																	<INPUT type=radio CHECKED value=P name=time>
																	발행 후
																	<INPUT onkeyup=strnumkeyup(this); style="PADDING-RIGHT: 3px; TEXT-ALIGN: right" maxLength=3 size=4 name=peorid class="input">
																	일 동안 사용가능<span class="font_orange">(유효기간 마지막일 23시59분59초 까지)</span> </div>
															</td>
														</tr>
														<tr>
															<th>쿠폰종류 선택</th>
															<td>
																<SELECT style="WIDTH: 100px" name=sale_type class="select">
																	<OPTION value="-" selected>할인 쿠폰</OPTION>
																	<OPTION value="+">적립 쿠폰 (배송후 적립)</OPTION>
																</SELECT>
																<span class="font_orange"> * 할인쿠폰은 구매시 즉시 할인되며, 적립쿠폰은 구매시 추가 적립금이 지급됩니다.</span> </td>
														</tr>
														<tr>
															<th>금액/할인율 선택</th>
															<td>
																<SELECT style="WIDTH: 100px" onchange=changerate(options.value); name=sale2 class="select">
																	<OPTION value="원" selected>금액</OPTION>
																	<OPTION value="%">할인(적립)율</OPTION>
																</SELECT>
																→
																<INPUT onkeyup=strnumkeyup(this); style="PADDING-RIGHT: 5px; TEXT-ALIGN: right" maxLength=10 size=10 name=sale_money class="input">
																<INPUT class="input_hide1" readOnly size=1 value=원 name=rate>
															</td>
														</tr>
														<tr>
															<th>금액절사</th>
															<td>
																<SELECT disabled name=amount_floor class="select">
																	<?
																		$arfloor = array(1=>"일원단위, 예)12344 → 12340","십원단위, 예)12344 → 12300","백원단위, 예)12344 → 12000","천원단위, 예)12344 → 10000");
																		$arcnt = count($arfloor);
																		for($i=1;$i<$arcnt;$i++){
																			$sel = ($amount_floor==$i)?" selected":'';
																	?>
																	<option value="<?=$i?>" <?=$sel?>>
																	<?=$arfloor[$i]?>
																	</option>
																	<?											} ?>
																</SELECT>
															</td>
														</tr>
														<tr>
															<th>쿠폰 결제 금액</th>
															<td>
																<INPUT onclick=nomoney(1) type=radio CHECKED name=checksale>
																제한 없음  &nbsp;
																<INPUT onclick=nomoney(0) type=radio name=checksale>
																<INPUT onkeyup=strnumkeyup(this); disabled maxLength=10 size=10 name=mini_price class="input_disabled">
																원 이상 주문시 가능
																<SCRIPT>nomoney(1);</SCRIPT>
															</td>
														</tr>
														<tr>
															<th>쿠폰사용가능 결제방법</th>
															<td>
																<INPUT type=radio CHECKED value=N name=bank_only>
																제한 없음  &nbsp;
																<INPUT type=radio value=Y name=bank_only>
																<B>현금 결제</B>만 가능(실시간 계좌이체 포함) </td>
														</tr>
														<tr>
															<th>쿠폰 발급조건</th>
															<td>
																<INPUT onclick="toggleDownType(false)" type=radio CHECKED value=N name=issue_type>
																운영자발급&nbsp;&nbsp;&nbsp;&nbsp;<span class="font_orange">* 쿠폰등록 후 [생성된 쿠폰 즉시 발급](쿠폰발급대기리스트) 에서 운영자가 특정회원에게 발급.</span><BR>
																<INPUT onclick="toggleDownType(true)" type=radio value=Y name=issue_type>
																회원직접 다운로드&nbsp;&nbsp;&nbsp;&nbsp;<span class="font_orange">* 로그인 한 회원이 직접 다운로드 클릭시 발급 </span><BR>
																<INPUT onclick="toggleDownType(false)" type=radio value=M name=issue_type>
																회원 가입시 자동발급</td>
														</tr>
														<!--
														<tr>
															<th>발급대상회원등급</th>
															<td>&nbsp;   </td>
														</tr> -->
														<tr class="downOnly" style="display:none">
															<th>발행 쿠폰 수</th>
															<td class="td_con1">
																<INPUT onclick=nonum(1) type=radio CHECKED name=checknum>
																무제한 &nbsp;
																<INPUT onclick=nonum(0) type=radio name=checknum>
																<INPUT onkeyup=strnumkeyup(this); disabled maxLength=10 size=10 name=issue_tot_no class="input">
																매 한정
																<SCRIPT>nonum(1);</SCRIPT>
															</td>
														</tr>
														<tr class="downOnly" style="display:none">
															<th>쿠폰 자동노출 여부</th>
															<td> 상품 상세페이지 상세설명 상단에 쿠폰을 자동
																<SELECT name=detail_auto class="select">
																	<OPTION value=Y selected>노출함</OPTION>
																	<OPTION value=N>노출안함</OPTION>
																</SELECT>
																<IMG height=5 width=0><BR>
																<span class="font_orange"> * 회원이 직접 쿠폰을 클릭함으로서 발급받을 수 있는 서비스입니다.</span> </td>
														</tr>
														<tr class="downOnly" style="display:none">
															<th>재 다운로드 </th>
															<td>
																<INPUT type=radio value=N name=repeat_id checked>
																불가능 &nbsp;
																<INPUT type=radio value=Y name=repeat_id>
																가능 </td>
														</tr>
														<?
															if( false ) {
														?>
														<tr class="downOnly" style="display:none">
															<th>쿠폰 사용 후 자동 재발급</th>
															<td>
																<INPUT type=radio value=Y name=repeat_ok>
																사용  &nbsp;
																<INPUT type=radio value=N name=repeat_ok checked>
																사용안함 </td>
														</tr>
														<?
															}
														?>
														<tr>
															<th style="width:160px;">적용상품 또는<br />카테고리 선택</th>
															<td>
																<input type=radio name=codegbn value="A" checked onclick="ChangeCodegbn('A')">
																전체상품&nbsp;&nbsp;
																<input type=radio name=codegbn value="N" onclick="ChangeCodegbn('N')">
																일부 카테고리/상품
																<div id=layer_codelist style="display:none; width:680px;">
																	<select name=codelist size=10 style="WIDTH:470px; float:left" class="select">
																		<option value="" style="BACKGROUND-COLOR: #ffff00">------------------------- 적용 상품군을 선택하세요. -------------------------</option>
																	</select>
																	<div style="width:200px;"> <a href="javascript:ChoiceProduct();"><img src="images/btn_add1.gif" hspace="2"></a> &nbsp; <a href="javascript:CodeDelete();"><img src="images/btn_del.gif" hspace="2"></a> </div>
																</div>
															</td>
														</tr>
														<tr id="layer1" style="display:none;">
															<th style="width:">쿠폰 사용조건</th>
															<td>
																<INPUT type=checkbox CHECKED value=Y name=use_con_type1>
																다른 상품과 함께 구매시에도, 해당 쿠폰을 사용합니다.<BR>
																<INPUT type=checkbox value=N name=use_con_type2>
																선택된 카테고리(상품)을 제외하고 적용합니다. </td>
														</tr>
													</table>

													<div><IMG SRC="images/market_couponnew_stitle2.gif" WIDTH="192" HEIGHT=31 ALT="쿠폰 부가정보 입력"></div>
													<table  cellSpacing=0 cellPadding=0 width="100%" border=0  class="cinputTbl">
														<tr>
															<th style="width:160px;">회원등급별 혜택<br />(할인/적립)과 동시<br /> 적용 여부</th>
															<td><input type="radio" name="use_point" value="Y" checked="checked" />동시적용&nbsp;&nbsp;<input type="radio" name="use_point" value="A" />쿠폰만 적용</td>
														</tr>
														<tr>
															<th>사은품 과 <br />동시적용여부</th>
															<td>
																<input type=checkbox name=etcapply_gift value=A> 본 쿠폰을 사용할 경우 사은품을 지급하지 않습니다. </td>
														</tr>

														<tr>
															<th>하나의 주문에<br /> 중복사용가능 여부</th>
															<td>
																<INPUT type=radio value=Y name=order_limit CHECKED>중복 사용불가
																<INPUT type=radio value=N name=order_limit>제한없이 중복사용가능  &nbsp;
															</td>
														</tr>
														<tr>
															<th>쿠폰 이미지 설정</th>
															<td>
																<INPUT type=radio CHECKED name=useimg>
																기본 이미지 사용<br>
																<IMG src="images/sample/market_couponsampleimg.gif" width="352" height="122" style="margin-bottom:5px;"><br />
																<INPUT type=radio name=useimg>
																자유제작 이미지 등록<span class="font_orange">(*GIF 파일 150KB 이하로 올려주시고, 권장 사이즈는 350*150 입니다.)</span><br />
																<INPUT type=file size=65 name=couponimg class="input">
															</td>
														</tr>
													</table>
												</form>
												<form name=form2 action="coupon_productchoice.php" method=post target=coupon_product>
												</form>
												<div style=" margin-top:10px; margin-bottom:25px; text-align:center"><a href="javascript:CheckForm(document.form1);"><img src="images/btn_cupon.gif" width="139" height="38" border="0"></a></div>
												<div style="margin-bottom:50px;">
													<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
														<tr>
															<td><IMG SRC="images/manual_top1.gif" WIDTH=15 height="45" ALT=""></td>
															<td><IMG SRC="images/manual_title.gif" WIDTH=113 height="45" ALT=""></td>
															<td width="100%" background="images/manual_bg.gif" height="35"></td>
															<td background="images/manual_bg.gif"></td>
															<td background="images/manual_bg.gif"><IMG SRC="images/manual_top2.gif" WIDTH=18 height="45" ALT=""></td>
														</tr>
														<tr>
															<td background="images/manual_left1.gif"></td>
															<td COLSPAN=3 width="100%" valign="top" bgcolor="white" style="padding-top:8pt; padding-bottom:8pt; padding-left:4pt;" class="menual_bg">
																<table cellpadding="0" cellspacing="0" width="100%">
																	<col width=20></col>
																	<col width=></col>
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
																		<td>발생한 쿠폰은 로그인 후 마이페이지 정보에서 확인 할 수 있습니다.</td>
																	</tr>
																</table>
															</td>
															<td background="images/manual_right1.gif"></td>
														</tr>
														<tr>
															<td><IMG SRC="images/manual_left2.gif" WIDTH=15 HEIGHT=8 ALT=""></td>
															<td COLSPAN=3 background="images/manual_down.gif"></td>
															<td><IMG SRC="images/manual_right2.gif" WIDTH=18 HEIGHT=8 ALT=""></td>
														</tr>
													</TABLE>
												</div>
											</td>
											<td width="16" background="images/con_t_02_bg.gif"></td>
										</tr>
										<tr>
											<td width="16"><img src="images/con_t_04.gif" width="16" height="16" border="0"></td>
											<td background="images/con_t_04_bg.gif"></td>
											<td width="16"><img src="images/con_t_03.gif" width="16" height="16" border="0"></td>
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

<?
	if( $popup != "OK" ) {
		INCLUDE "copyright.php";
	}
?>