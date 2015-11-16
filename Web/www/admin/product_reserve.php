<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
INCLUDE ("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "pr-4";
$MenuCode = "product";
if (!$_usersession->isAllowedTask($PageCode)) {
	INCLUDE ("AccessDeny.inc.php");
	exit;
}
#########################################################

$mode=$_POST["mode"];
$code=$_POST["code"];
$money=$_POST["money"];
$gbn=$_POST["gbn"];
$reserve=$_POST["reserve"];
$reservetype=$_POST["reservetype"];
$danwi=$_POST["danwi"];
$cut=$_POST["cut"];

if(strlen($code)==12) {
	$sql = "SELECT type FROM tblproductcode WHERE codeA='".substr($code,0,3)."' ";
	$sql.= "AND codeB='".substr($code,3,3)."' ";
	$sql.= "AND codeC='".substr($code,6,3)."' AND codeD='".substr($code,9,3)."' ";
	$result=mysql_query($sql,get_db_conn());
	$row=mysql_fetch_object($result);
	mysql_free_result($result);
	if(!$row) {
		$code="";
	}
	$type = $row->type;
} else {
	$code="";
}

if ($mode=="update" && (strlen(trim($money))>0 || strlen(trim($reserve))>0) && strlen($code)==12) {
	if(ereg("X",$type)) {
		$likecode=$code;
	} else {
		$likecode=substr($code,0,3);
		if(substr($code,3,3)!="000") {
			$likecode.=substr($code,3,3);
			if(substr($code,6,3)!="000") {
				$likecode.=substr($code,6,3);
				if(substr($code,9,3)!="000") {
					$likecode.=substr($code,9,3);
				}
			}
		}
	}

	$won=array(1=>0.1,10=>0.01,100=>0.001,1000=>0.0001);

	if ($gbn=="W") {
		$reserve=$reserve*1;
		if($reservetype!="Y") {
			$reservetype="N";
		}
		$sql = "UPDATE tblproduct SET ";
		$sql.= "reserve			= '".$money."', ";
		$sql.= "reservetype		= '".$reservetype."' ";
		$sql.= "WHERE productcode LIKE '".$likecode."%' ";
		mysql_query($sql,get_db_conn());

		$log_content = "## 적립금 일괄수정 ## - 코드 $likecode - 변경 적립금 : $money";
		ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);
		$onload="<script>alert('적립금 일괄 변경이 완료되었습니다.');</script>";
	} else if ($gbn=="P") {
		if ($cut=="round") {
			$sql = "UPDATE tblproduct SET ";
			$sql.= "reserve=ROUND(sellprice*($reserve/100)*$won[$danwi]+$won[$danwi])*($danwi*10), reserveType='N' ";
			$sql.= "WHERE productcode LIKE '".$likecode."%' ";
		} else if ($cut=="ceil") {
			$sql = "UPDATE tblproduct SET ";
			$sql.= "reserve=CEILING(sellprice*($reserve/100)*$won[$danwi])*($danwi*10), reserveType='N' ";
			$sql.= "WHERE productcode LIKE '".$likecode."%' ";
		} else if ($cut=="floor") {
			$sql = "UPDATE tblproduct SET ";
			$sql.= "reserve=FLOOR(sellprice*($reserve/100)*$won[$danwi])*($danwi*10), reserveType='N' ";
			$sql.= "WHERE productcode LIKE '".$likecode."%' ";
		}
		$result = mysql_query($sql,get_db_conn());

		$log_content = "## 적립금 일괄수정 ## - 코드 $likecode - 변경 적립금 : $reserve% - $danwi단위로 $cut";
		ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);
		$onload="<script>alert('적립금을 판매 금액의 $reserve%로 변경하였습니다.');</script>";
	}
}

?>

<? INCLUDE "header.php"; ?>

<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="codeinit.js.php"></script>
<script language="JavaScript">
var code="<?=$code?>";
function CodeProcessFun(_code) {
	if(_code=="out" || _code.length==0 || _code=="000000000000") {
		document.all["code_top"].style.background="#dddddd";
		selcode="";
		seltype="";

		if(_code!="out") {
			BodyInit('');
		} else {
			_code="";
		}
	} else {
		document.all["code_top"].style.background="#ffffff";
		BodyInit(_code);
	}
}

var allopen=false;
function AllOpen() {
	display="show";
	open1="open";
	if(allopen) {
		display="none";
		open1="close";
		allopen=false;
	} else {
		allopen=true;
	}
	for(i=0;i<all_list.length;i++) {
		if(display=="none" && all_list[i].codeA==selcode.substring(0,3)) {
			all_list[i].selected=true;
			selcode=all_list[i].codeA+all_list[i].codeB+all_list[i].codeC+all_list[i].codeD;
			seltype=all_list[i].type;
		}
		all_list[i].display=display;
		all_list[i].open=open1;
		for(ii=0;ii<all_list[i].ArrCodeB.length;ii++) {
			if(display=="none") {
				all_list[i].ArrCodeB[ii].selected=false;
			}
			all_list[i].ArrCodeB[ii].display=display;
			all_list[i].ArrCodeB[ii].open=open1;
			for(iii=0;iii<all_list[i].ArrCodeB[ii].ArrCodeC.length;iii++) {
				if(display=="none") {
					all_list[i].ArrCodeB[ii].ArrCodeC[iii].selected=false;
				}
				all_list[i].ArrCodeB[ii].ArrCodeC[iii].display=display;
				all_list[i].ArrCodeB[ii].ArrCodeC[iii].open=open1;
				for(iiii=0;iiii<all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD.length;iiii++) {
					if(display=="none") {
						all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD[iiii].selected=false;
					}
					all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD[iiii].display=display;
					all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD[iiii].open=open1;
				}
			}
		}
	}
	BodyInit('');
}

function CheckForm() {
	if(selcode.length!=12 || selcode=="000000000000") {
		alert ("변경하실 카테고리를 선택하세요.");
		return;
	}
	
	if(document.form1.gbn[0].checked==true) {
		if(document.form1.reservetype.value=="Y") {
			if(document.form1.money.value.length==0){
				alert('일괄 적립금의 금액을 입력하세요');
				document.form1.money.focus();
				return;
			}
			if(isDigitSpecial(document.form1.money.value,".")) {
				alert("적립률은 숫자와 특수문자 소수점\(.\)으로만 입력하세요.");
				document.form1.money.focus();
				return;
			}
			
			if(getSplitCount(document.form1.money.value,".")>2) {
				alert("적립률 소수점\(.\)은 한번만 사용가능합니다.");
				document.form1.money.focus();
				return;
			}

			if(getPointCount(document.form1.money.value,".",2)==true) {
				alert("적립률은 소수점 이하 둘째자리까지만 입력 가능합니다.");
				document.form1.money.focus();
				return;
			}

			if(Number(document.form1.money.value)>100 || Number(document.form1.money.value)<0) {
				alert("적립률은 0 보다 크고 100 보다 작은 수를 입력해 주세요.");
				document.form1.money.focus();
				return;
			}
		} else {
			if(document.form1.money.value.length==0){
				alert('일괄 적립금의 금액을 입력하세요');
				document.form1.money.focus();
				return;
			}
			if(isDigitSpecial(document.form1.money.value,"")) {
				alert("적립금은 숫자로만 입력하세요.");
				document.form1.money.focus();
				return;
			}
		}
	}

	if (!confirm("해당 카테고리의 적립금을 변경하시겠습니까?")) {
		return;
	}
	document.form1.mode.value="update";
	document.form1.code.value=selcode;
	document.form1.submit();
}

function GoAllUpdate(){
	if(selcode.length!=12 || selcode=="000000000000") {
		alert('먼저 카테고리를 선택하세요');
		return;
	}
	document.form2.code.value=selcode;
	document.form2.submit();
}

function chkFieldMaxLenFunc(thisForm,reserveType) {
	if (reserveType=="Y") { max=5; addtext="/특수문자(소수점)";} else { max=6; }
	if (thisForm.money.value.bytes() > max) {
		alert("입력할 수 있는 허용 범위가 초과되었습니다.\n\n" + "숫자"+addtext+" " + max + "자 이내로 입력이 가능합니다.");
		thisForm.money.value = thisForm.money.value.cut(max);
		thisForm.money.focus();
	}
}

function getSplitCount(objValue,splitStr)
{
	var split_array = new Array();
	split_array = objValue.split(splitStr);
	return split_array.length;
}

function getPointCount(objValue,splitStr,falsecount)
{
	var split_array = new Array();
	split_array = objValue.split(splitStr);
	
	if(split_array.length!=2) {
		if(split_array.length==1) {
			return false;
		} else {
			return true;
		}
	} else {
		if(split_array[1].length>falsecount) {
			return true;
		} else {
			return false;
		}
	}
}

function isDigitSpecial(objValue,specialStr)
{
	if(specialStr.length>0) {
		var specialStr_code = parseInt(specialStr.charCodeAt(i));

		for(var i=0; i<objValue.length; i++) {
			var code = parseInt(objValue.charCodeAt(i));
			var ch = objValue.substr(i,1).toUpperCase();
			
			if((ch<"0" || ch>"9") && code!=specialStr_code) {
				return true;
				break;
			}
		}
	} else {
		for(var i=0; i<objValue.length; i++) {
			var ch = objValue.substr(i,1).toUpperCase();
			if(ch<"0" || ch>"9") {
				return true;
				break;
			}
		}
	}
}
</script>

<STYLE type=text/css>
	#menuBar {}
	#contentDiv {WIDTH: 200;HEIGHT: 315;}
</STYLE>
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
			<? include ("menu_product.php"); ?>
			</td>

			<td></td>
			<td valign="top">




<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td height="29" colspan="3">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 상품관리 &gt; 상품 일괄관리 &gt; <span class="2depth_select">적립금 일괄수정</span></td>
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
					<TD><IMG SRC="images/product_reserve_title.gif" ALT=""></TD>
					</tr><tr>
					<TD width="100%" background="images/title_bg.gif" height=21></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height="3"></td></tr>
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
					<TD width="100%" class="notice_blue">쇼핑몰에 등록된 모든 상품의 적립금을 일괄 수정할 수 있습니다.</TD>
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
				<td height=20></td>
			</tr>
			<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post>
			<input type=hidden name=mode>
			<input type=hidden name=code>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td valign="top">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="232" height="100%" valign="top" background="images/category_boxbg.gif">
						<table cellpadding="0" cellspacing="0" width="242">
						<tr>
							<td bgcolor="white"><IMG SRC="images/product_totoacategory_title.gif" WIDTH=85 HEIGHT=24 ALT=""></td>
						</tr>
						<tr>
							<td><IMG SRC="images/category_box1.gif" border="0"></td>
						</tr>
						<tr>
							<td bgcolor="#0F8FCB" style="padding:2;padding-left:4">
							<button title="전체 트리확장" id="btn_treeall" class="btn" onmouseover="if(this.className=='btn'){this.className='btnOver'}" onmouseout="if(this.className=='btnOver'){this.className='btn'}" unselectable="on" onclick="AllOpen();"><IMG SRC="images/category_btn1.gif" WIDTH=22 HEIGHT=23 border="0"></button>
							</td>
						</tr>
						<tr>
							<td bgcolor="#0F8FCB" style="padding-top:4pt; padding-bottom:6pt;"></td>
						</tr>
						<tr>
							<td><IMG SRC="images/category_box2.gif" border="0"></td>
						</tr>
						<tr>
							<td width="100%" height="100%" align=center valign=top style="padding-left:5px;padding-right:5px;">
							<DIV class=MsgrScroller id=contentDiv style="width=99%;height:350;OVERFLOW-x: auto; OVERFLOW-y: auto;" oncontextmenu="return false" style="overflow-x:hidden;overflow-y:hidden;" ondragstart="return false" onselectstart="return false" oncontextmenu="return false">
							<DIV id=bodyList>
							<table border=0 cellpadding=0 cellspacing=0 width="100%" height="100%" bgcolor=FFFFFF>
							<tr>
								<td height=18><IMG SRC="images/directory_root.gif" border=0 align=absmiddle> <span id="code_top" style="cursor:default;" onmouseover="this.className='link_over'" onmouseout="this.className='link_out'" onclick="ChangeSelect('out');">최상위 카테고리</span></td>
							</tr>
							<tr>
								<!-- 상품카테고리 목록 -->
								<td id="code_list" nowrap valign=top></td>
								<!-- 상품카테고리 목록 끝 -->
							</tr>
							</table>
							</DIV>
							</DIV>
							</td>
						</tr>
						</table>
						</td>
					</tr>
					<tr>
						<td><IMG SRC="images/category_boxdown.gif" border="0"></td>
					</tr>
					</table>
					</td>
					<td width="15"><img src="images/btn_next1.gif" width="25" height="25" border="0" hspace="5"><br></td>
					<td width="100%" valign="top" height="100%">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td>
						<table cellpadding="0" cellspacing="0" width="100%" height="100%">
						<tr>
							<td width="100%" bgcolor="#FFFFFF"><IMG SRC="images/product_mainlist_text3.gif" border="0"></td>
						</tr>
						<tr>
							<td width="100%" height="100%" valign="top" style="BORDER-bottom:#eeeeee 2px solid;BORDER-top:#eeeeee 2px solid;">
							<table cellpadding="0" cellspacing="0" width="100%">
							<tr>
								<td width="100%" style="padding-top:2pt; padding-bottom:2pt;"><input type=radio name=gbn value="W" checked style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none">적립금(률)을 일괄 <input type=text name=money size=10 maxlength=6 style="text-align:right" class="input" onKeyUP="chkFieldMaxLenFunc(this.form,this.form.reservetype.value);"><select name="reservetype" style="font-size:8pt;margin-left:1px;" onchange="chkFieldMaxLenFunc(this.form,this.value);"><option value="N" selected>￦</option><option value="Y">％</option></select>으로 변경합니다.</td>
							</tr>
							<tr>
								<td width="100%" style="padding-top:2pt; padding-bottom:2pt;" height="22"><input type=radio name=gbn value="P" style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none">적립금을 판매가격의 <select name=reserve class="select">
									<?for($i=0.1;$i<=0.9;$i+=0.1) echo "<option value='$i'>$i</option>\n";  ?>
									<?for($i=1;$i<=100;$i++) echo "<option value='$i'>$i</option>\n";  ?>
									</select> %를 <select name=danwi class="select">
										<option value="1">1</option>
										<option value="10">10</option>
										<option value="100">100</option>
										<option value="1000">1000</option>
									</select>원 단위로 <select name=cut class="select">
									<option value="floor">내림</option>
									<option value="round">반올림</option>
									<option value="ceil">올림</option>
								</select>하여 변경합니다.</td>
							</tr>
							<tr>
								<td width="100%">&nbsp;</td>
							</tr>
							<tr>
								<td width="100%" style="padding-top:5pt; padding-bottom:5pt;">
								<table cellpadding="0" cellspacing="0" width="100%">
								<tr>
									<td width="100%" class="font_orange" style="padding-top:5pt; padding-bottom:5pt; border-width:1pt; border-color:rgb(0,153,204); border-style:solid;">&nbsp;&nbsp;* 코디/조립 상품의 판매가격은 기본 구성 상품 판매가격의 총합 가격입니다.<br>
									&nbsp;&nbsp;* 상품 적립금 변경에서는 콤마(,)를 제외한 숫자만 입력하세요.<br>
									&nbsp;&nbsp;* 상품 적립률 변경에서는 숫자 와 소수점(.)만 입력하세요.<br>
									&nbsp;&nbsp;* 상품 적립률은 소수점 둘째자리까지 입력이 가능합니다.<br>
									&nbsp;&nbsp;* 상품 적립률에 대한 적립 금액 소수점 이하 자리는 반올림 처리되어 지급됩니다.<br>
										&nbsp;&nbsp;* 선택된 카테고리내 모든 하위 카테고리의 상품 적립금(률)이 변경됩니다.<br>
										&nbsp;&nbsp;* 변경된 적립금(률)은 복원되지 않으므로 신중히 처리하시기 바랍니다.<br>
										&nbsp;&nbsp;* <b>[일괄 적립금 보기]</b> 버튼을 누르면 해당 카테고리의 상품정보를 보실 수 있습니다.</td>
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
					<tr>
						<td height=10></td>
					</tr>
					<tr>
						<td align=center><a href="javascript:CheckForm();"><img src="images/botteon_save.gif" border="0" hspace="0" vspace="4"></a>&nbsp;&nbsp;<a href="javascript:GoAllUpdate();"><img src="images/btn_allpoint.gif" border="0" hspace="2" vspace="4"></a></td>
					</tr>
					</table>
					</td>
				</tr>
				</table>
				</td>
			</tr>
			</form>
			<form name=form2 action="product_allupdate.php" method=post>
			<input type=hidden name=code>
			<tr>
				<td height="30"></td>
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
					<TD background="images/manual_left1.gif"></td>
					<TD COLSPAN=3 width="100%" valign="top" bgcolor="white" style="padding-top:8pt; padding-bottom:8pt; padding-left:4pt;" class="menual_bg">
					<table cellpadding="0" cellspacing="0" width="100%">
					<col width=20></col>
					<col width=></col>
					<tr>
						<td align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td><span class="font_dotline">적립금 일괄수정</span></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td  class="space_top"><p>- 코디/조립 상품의 판매가격은 기본 구성 상품 판매가격의 총합 가격입니다.</p></td>
					</tr>
					<tr>
						<td align="right">&nbsp;</td>
						<td class="space_top">- 개별 상품 적립금 수정은 [상품 등록/수정/삭제] 메뉴에서 처리하시기 바랍니다.<br>
						<b>&nbsp;&nbsp;</b><a href="javascript:parent.topframe.GoMenu(4,'product_register.php');"><span class="font_blue">상품관리 > 카테고리/상품관리 > 상품 등록/수정/삭제</span></a></td>
					</tr>
					<tr>
						<td align="right">&nbsp;</td>
						<td class="space_top">- 선택된 카테고리내 모든 하위카테고리에 등록된 모든 상품의 적립금이 일괄변경되므로 카테고리 선택에 주의하세요.</td>
					</tr>
					<tr>
						<td align="right">&nbsp;</td>
						<td class="space_top">- 변경된 적립금은 복원되지 않으므로 신중히 처리하시기 바랍니다.</td>
					</tr>
					<tr>
						<td colspan="2" height="20"></td>
					</tr>
					<tr>
						<td align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td><span class="font_dotline">적립금 일괄수정 방법</span></td>
					</tr>
					<tr>
						<td align="right">&nbsp;</td>
						<td class="space_top">① 일괄 변경을 원하는 카테고리를 선택합니다.</td>
					</tr>
					<tr>
						<td align="right">&nbsp;</td>
						<td class="space_top">② 직접 적립금액을 입력하여 변경하는 방식과 상품 판매가격에 대한 비율로 변경하는 두가지 방식 중 하나를 선택하세요.</td>
					</tr>
					<tr>
						<td align="right">&nbsp;</td>
						<td class="space_top">③ 원하는 적립금 변경을 입력하신 후 적용하기 버튼을 누르세요.</td>
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
<?
$sql = "SELECT * FROM tblproductcode WHERE type!='T' AND type!='TX' AND type!='TM' AND type!='TMX' ";
$sql.= "ORDER BY sequence DESC ";
include ("codeinit.php");
?>

<?=$onload?>

<? INCLUDE "copyright.php"; ?>