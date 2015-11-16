<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/ext/func.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/hiworks/bill.class.php");

$ordercode=$_POST["ordercode"];
$bill = new Bill();
$bill->setOrder($ordercode);

if($bill->billstatus == 'R'){
	//_pr($bill);
	$rinfo = $bill->companyinfo['receiver'];
}else if($bill->billstatus == 'S'){

}else{

	$rinfo = $bill->get_receiverinfo();
}

?>
<html>
<head>
<title>세금계산서 정보입력</title>
<meta http-equiv="CONTENT-TYPE" content="text/html;charset=EUC-KR">
<script type="text/javascript" src="../lib/lib.js.php"></script>
<style>
td {font-family:돋음;color:333333;font-size:9pt;}
tr {font-family:돋음;color:333333;font-size:9pt;}
BODY,TD,SELECT,DIV,form,TEXTAREA,center,option,pre,blockquote {font-family:돋음;color:333333;font-size:9pt;}

A:link    {color:333333;text-decoration:none;}
A:visited {color:333333;text-decoration:none;}
A:active  {color:333333;text-decoration:none;}
A:hover  {color:#CC0000;text-decoration:none;}
</style>
<script language="JavaScript">

function f_addr_search(form,post,addr,gbn) {
	window.open("<?=$Dir.FrontDir?>addr_search.php?form="+form+"&post="+post+"&addr="+addr+"&gbn="+gbn,"f_post","resizable=yes,scrollbars=yes,x=100,y=200,width=370,height=250");		
}

function CheckForm() {
	var form = document.form1;
	if (!form.r_name.value) {
		form.r_name.focus();
		alert("상호(회사명)을 입력하세요.");
		return;
	}
	if(CheckLength(form.r_name)>30) {
		form.company_name.focus();
		alert("상호(회사명)은 한글15자 영문30자 까지 입력 가능합니다");
		return;
	}
	if (!form.r_number.value) {
		form.r_number.focus();
		alert("사업자등록번호를 입력하세요.");
		return;
	}

	var bizno;
	var bb;
	bizno = form.r_number.value;
	bizno = bizno.replace(/-/g,'');

	bb = chkBizNo(bizno);
	if (!bb) {
		alert("인증되지 않은 사업자등록번호 입니다.\n사업자등록번호를 다시 입력하세요.");
		form.r_number.value = "";
		form.r_number.focus();
		return;
	}

	if (!form.r_master.value) {
		form.r_master.focus();
		alert("대표자 성명을 입력하세요.");
		return;
	}
	if(CheckLength(form.r_master)>12) {
		form.r_master.focus();
		alert("대표자 성명은 한글 6글자까지 가능합니다");
		return;
	}
	
	if (!form.r_address.value) {
		form.r_address.focus();
		alert("사업장 주소를 입력하세요.");
		return;
	}	
	
	if(CheckLength(form.r_condition)< 1) {
		form.r_condition.focus();
		alert("업태를 입력하세요");
		return;
	}else if(CheckLength(form.r_condition)>30) {
		form.r_condition.focus();
		alert("사업자 업태는 한글 15자까지 입력 가능합니다");
		return;
	}
	
	if(CheckLength(form.r_item)< 1) {
		form.r_condition.focus();
		alert("종목을 입력하세요");
		return;
	}else if(CheckLength(form.r_item)>30) {
		form.r_item.focus();
		alert("사업자 종목은 한글 15자까지 입력 가능합니다");
		return;
	}
	if (!form.c_name.value) {
		form.c_name.focus();
		alert("담당자 이름을 입력하세요.");
		return;
	}
	if (!form.c_email.value) {
		form.c_email.focus();
		alert("담당자 이메일을 입력하세요.");
		return;
	}
	if (!IsMailCheck(form.c_email.value)) {
		form.c_email.focus();
		alert("이메일 형식이 아닙니다.");
		return;
	}
	if (!form.c_cell.value) {
		form.c_cell.focus();
		alert("담당자 연락처를 입력하세요.");
		return;
	}

	//form.type.value="<?=($type)? 'mod':'up'?>";
	form.submit();
}
function billRequest(ordercode){
	var form = document.frmbill;
	form.submit();
}
</script>
</head>

<body topmargin=0 leftmargin=0 rightmargin=0 marginheight=0 marginwidth=0>
<center>
<form name=form1 action="/bill/process.php" method=post>
<input type=hidden name="act" value="<?=($bill->billstatus == 'R')?'mod':''?>">
<input type=hidden name="ordercode" value="<?=$_POST["ordercode"]?>">
<table border=0 cellpadding=0 cellspacing=0 width=100%>
<tr>
	<td align=center style="padding:10,10,10,10">
	<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
	<tr><td align=center height=30 bgcolor=#454545><FONT COLOR="#FFFFFF"><B>공급받는자 정보입력</B></FONT></td></tr>
	<tr><td height=10></td></tr>
	<tr>
		<td align=center>
		<table border=0 cellpadding=5 cellspacing=1 width=100% bgcolor=#E7E7E7 style="table-layout:fixed">
		<col width=110></col>
		<col width=></col>
				<tr>
			<td align=center bgcolor=#F5F5F5 style="padding:7,10">등록번호</td>
			<td bgcolor=#ffffff style="padding:7,10"><input type="text" name="r_number" style="WIDTH:100px;" value="<?=$rinfo['r_number']?>"></td>
		</tr>
		<tr>
			<td align=center bgcolor=#F5F5F5 style="padding:7,10">상호(법인명)</td>
			<td bgcolor=#ffffff style="padding:7,10"><input type="text" name="r_name" style="WIDTH:150px;" value="<?=$rinfo['r_name']?>"></td>
		</tr>
		<tr>
			<td align=center bgcolor=#F5F5F5 style="padding:7,10">성명(대표자)</td>
			<td bgcolor=#ffffff style="padding:7,10"><input type="text" name="r_master" style="WIDTH:100px;" value="<?=$rinfo['r_master']?>"></td>
		</tr>
		<tr>
			<td align=center bgcolor=#F5F5F5 style="padding:7,10">주소</td>
			<td bgcolor=#ffffff style="padding:7,10">
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td><INPUT type=text name="up_companypost1" value="<?=substr($companypost,0,3)?>" readOnly style="WIDTH:40px;"> - <INPUT type=text name="up_companypost2" value="<?=substr($companypost,3,3)?>" readOnly style="WIDTH:40px;" ><a href="javascript:f_addr_search('form1','up_companypost','r_address',2);"><img src="<?=$Dir?>images/common/mbjoin/001/memberjoin_skin1_btn2.gif" border="0" align="absmiddle" hspace="3"></a></td>
				</tr>
				<tr>
					<td><INPUT type=text name="r_address" value="<?=$rinfo['r_address']?>" maxLength="200" style="WIDTH:80%;"></td>
				</tr>
				
				</table>
			</td>
		</tr>
		<tr>
			<td align=center bgcolor=#F5F5F5 style="padding:7,10">업태</td>
			<td bgcolor=#ffffff style="padding:7,10"><input type="text" name="r_condition" value="<?=$rinfo['r_condition']?>"></td>
		</tr>
		<tr>
			<td align=center bgcolor=#F5F5F5 style="padding:7,10">종목</td>
			<td bgcolor=#ffffff style="padding:7,10"><input type="text" name="r_item" value="<?=$rinfo['r_item']?>"></td>
		</tr>
		<tr>
			<td align=center bgcolor=#F5F5F5 style="padding:7,10">담당자 이름</td>
			<td bgcolor=#ffffff style="padding:7,10"><input type="text" name="c_name" value="<?=pick($bill->basicinfo['c_name'],$_ShopInfo->getMemname())?>"></td>
		</tr>
		<tr>
			<td align=center bgcolor=#F5F5F5 style="padding:7,10">담당자 이메일</td>
			<td bgcolor=#ffffff style="padding:7,10"><input type="text" name="c_email" value="<?=pick($bill->basicinfo['c_email'],$_ShopInfo->getMememail())?>"></td>
		</tr>
		<tr>
			<td align=center bgcolor=#F5F5F5 style="padding:7,10">담당자 연락처</td>
			<td bgcolor=#ffffff style="padding:7,10"><input type="text" name="c_cell" value="<?=$bill->basicinfo['c_cell']?>"></td>
		</tr>
		</table>
		</td>
	</tr>
	<tr><td height=10></td></tr>
	<tr>
		<td align=center>
		<input type="button" value="<?=($bill->billstatus == 'R')? " 수 정 ":" 신 청 "?>" onClick="CheckForm()" style="cursor:pointer">
		<A HREF="javascript:window.close()"><img src="../images/common/orderdetailpop_close.gif" align=absmiddle border=0></A>
		</td>
	</tr>
	</table>
	</td>
</tr>
</table>
</form>
</body>
</html>
