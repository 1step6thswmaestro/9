<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

if(strlen($_ShopInfo->getId())==0){
	echo "<script>alert('정상적인 경로로 접근하시기 바랍니다.');window.close();</script>";
	exit;
}

$codetype=$_POST["codetype"];
$code=$_POST["code"];

if(strlen($code)!=12) $code="000000000000";

$codeA=substr($code,0,3);
$codeB=substr($code,3,3);
$codeC=substr($code,6,3);
$codeD=substr($code,9,3);

?>

<html>
<head>
<meta http-equiv='Content-Type' content='text/html;charset=euc-kr'>
<title>쿠폰 적용 상품군 선택</title>
<link rel="stylesheet" href="style.css" type="text/css">
<script type="text/javascript" src="codeinit.js.php"></script>
<SCRIPT LANGUAGE="JavaScript">
<!--
var code="<?=$code?>";
var cnt=0;
function CodeProcessFun(_code) {
	if(_code=="out" || _code.length==0 || _code=="000000000000") {
		seltype="";
		selcode="";
		document.all["code_top"].style.background="#dddddd";
		document.form1.codetype[0].checked=true;

		if(_code!="out") {
			BodyInit('');
		} else {
			_code="";
		}
	} else {
		document.all["code_top"].style.background="#ffffff";
		document.form1.codetype[0].checked=true;
		if(cnt>0) {
			if(seltype.indexOf("X")!=-1) {
				document.form1.code.value=selcode;
				document.form1.submit();
			}
		}
		BodyInit(_code);
	}
	cnt++;
}

function ChangeProduct() {
	document.form1.codetype[1].checked=true;
}

//document.onkeydown = CheckKeyPress;
//document.onkeyup = CheckKeyPress;
function CheckKeyPress() {
	ekey = event.keyCode;

	if(ekey == 38 || ekey == 40 || ekey == 112 || ekey ==17 || ekey == 18 || ekey == 25 || ekey == 122 || ekey == 116) {
		event.keyCode = 0;
		return false;
	}
}

function PageResize() {
	var oWidth = document.all.table_body.clientWidth + 10;
	var oHeight = document.all.table_body.clientHeight + 80;

	window.resizeTo(oWidth,oHeight);
}

function CheckForm(form) {
	codetype="";
	for(i=0;i<form.codetype.length;i++) {
		if(form.codetype[i].checked==true) {
			codetype=form.codetype[i].value;
			break;
		}
	}
	if (codetype=="CODE") {
		if(selcode.length!=12 || selcode=="000000000000"){
			alert('쿠폰 적용을 원하시는 카테고리를 선택하세요');
			return;
		}
		productcode=selcode;
		productname=selcode_name;
		if(CodeAdd(productcode,productname)==false) {
			return;
		}
	} else if (codetype=="PRODUCT") {
		if(form.prcode.value.length==0){
			alert('쿠폰 적용을 원하시는 상품을 선택하세요');
			form.prcode.focus();
			return;
		}
		productcode=form.prcode.value;
		productname=selcode_name+" > "+form.prcode.options[form.prcode.selectedIndex].text;
		if(CodeAdd(productcode,productname)==false) {
			return;
		}
	} else {
		alert("쿠폰 적용 상품군 선택이 안되었습니다.");
		return;
	}
	//window.close();
}

function CodeAdd(productcode,productname) {
	if(productcode.length==0 || productname.length==0) {
		alert("상품군 선택이 잘못되었습니다.");
		return false;
	}
	//alert(productcode);
	
	//codelist=opener.document.form1.codelist;
	if(opener.document.form1){
		//var f = opener.document.form1;
		codelist=opener.document.form1.codelist;
	}else if(opener.document.bulkMailForm){
		//var f = opener.document.bulkMailForm;
		codelist=opener.document.bulkMailForm.codelist;
	}
/*
	if(codelist.options.length>50) {
		alert("상품군 선택은 50개 까지 가능합니다.");
		return false;
	}
*/
	for(i=1;i<codelist.options.length;i++) {
		if(productcode==codelist.options[i].value) {
			alert("이미 추가된 상품군입니다.\n\n다시 확인하시기 바랍니다.");
			return false;
		}
	}

	new_option = opener.document.createElement("OPTION");
	new_option.text=productname;
	new_option.value=productcode;
	codelist.add(new_option);
	cnt=codelist.options.length - 1;
	codelist.options[0].text = "------------------------- 적용 상품군을 선택하세요. -------------------------";
	alert(productname+' 을 추가 했습니다.');

	return true;
}

//-->
</SCRIPT>
</head>
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 onLoad="PageResize();">

<STYLE type=text/css>
	#menuBar {}
	#contentDiv {WIDTH: 245;HEIGHT: 230;}
</STYLE>

<TABLE WIDTH="250" BORDER=0 CELLPADDING=0 CELLSPACING=0 style="table-layout:fixed;" id=table_body>
<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post>
<input type=hidden name=code value="<?=$code?>">
<TR>
	<TD>
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td><p><img src="images/product_copy_dt_title1.gif" border="0" width="143" height="31"></p></td>
		<td width="100%" background="images/member_mailallsend_imgbg.gif"><p>&nbsp;</p></td>
		<td><p align="right"><img src="images/member_mailallsend_img2.gif" width="20" height="31" border="0"></p></td>
	</tr>
	</table>
	</TD>
</TR>
<TR>
	<TD>
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td width="240"><p><input type=radio id="idx_codetype1" name=codetype value="CODE" <?=($codetype=="CODE"?"checked":"")?>> <label style='cursor:hand;' onMouseOver="style.textDecoration='underline'" onMouseOut="style.textDecoration='none'" for=idx_codetype1>일부 카테고리의 모든상품에만 적용.</label></p></td>
	</tr>
	<tr>
		<td width="240"><p><input type=radio id="idx_codetype2" name=codetype value="PRODUCT" <?=($codetype=="PRODUCT"?"checked":"")?>> <label style='cursor:hand;' onMouseOver="style.textDecoration='underline'" onMouseOut="style.textDecoration='none'" for=idx_codetype2>일부 상품에만 적용.</label></p></td>
	</tr>
	</table>
	</TD>
</TR>
<tr>
	<TD>
	<table border=1 cellpadding=0 cellspacing=0 width=100%>
	<tr>
		<td style="padding-top:1" nowrap>
		<DIV class=MsgrScroller id=contentDiv style="OVERFLOW-x: auto; OVERFLOW-y: auto;" oncontextmenu="return false" style="overflow-x:hidden;overflow-y:hidden;" ondragstart="return false" onselectstart="return false" oncontextmenu="return false">
		<DIV id=bodyList>
		<table border=0 cellpadding=0 cellspacing=0>
		<tr>
			<td height=18 style="padding-left:5">
			<IMG SRC="images/directory_root.gif" border=0 align=absmiddle> <span id="code_top" style="cursor:default;background-color:<?=($code=="000000000000"?"#dddddd":"#ffffff")?>" onMouseOver="this.className='link_over'" onMouseOut="this.className='link_out'" onClick="ChangeSelect('out');">쿠폰 상품군 선택</span>
			</td>
		</tr>
		<tr>
			<!-- 상품카테고리 목록 -->
			<td id="code_list" style="padding-right:5" nowrap>

			</td>
			<!-- 상품카테고리 목록 끝 -->
		</tr>
		</table>
		</DIV>
		</DIV>
		</td>
	</tr>
	</table>
	</TD>
</tr>
<TR>
	<TD width="100%">
	<p align="center"><a href="javascript:window.close()">
	<select name=prcode size=5 onChange="ChangeProduct();" style="width:98%;">
<?
	if (strlen($code)==12) {
		$sql = "SELECT productcode,productname FROM tblproduct ";
		$sql.= "WHERE productcode LIKE '".$code."%' ORDER BY date DESC";
		$result = mysql_query($sql,get_db_conn());
		while ($row = mysql_fetch_object($result)) {
			echo "<option value=\"".$row->productcode."\">".$row->productname.$sale;
		}
		echo "</option>\n";
	}
	mysql_free_result($result);
?>
	</select></a></p>
	</TD>
</TR>
<TR>
	<TD height="25" style="padding-top:4pt;"><p align="center"><a href="javascript:CheckForm(document.form1);"><img src="images/btn_select1.gif" width="56" height="18" border="0" vspace="0" border=0></a><a href="javascript:window.close();"><img src="images/btn_close.gif" width="36" height="18" border="0" vspace="0" border=0 hspace="2"></a></p></TD>
</TR>
</form>
</TABLE>

<?
$sql = "SELECT * FROM tblproductcode WHERE type!='T' AND type!='TX' AND type!='TM' AND type!='TMX' ";
$sql.= "ORDER BY sequence DESC ";
include ("codeinit.php");
?>

</body>
</html>