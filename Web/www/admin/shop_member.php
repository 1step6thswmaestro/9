<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/ext/func.php");
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
$text1=$_POST["text1"];
$text2=$_POST["text2"];
$text3=$_POST["text3"];
$up_memberout_type=$_POST["up_memberout_type"];
$up_resno_type=$_POST["up_resno_type"];
$up_resno_type2=$_POST["up_resno_type2"];

if($type=="up"){
	if($up_resno_type=="Y" && $up_resno_type2=="N") $up_resno_type="Y";
	else if($up_resno_type=="Y" && $up_resno_type2=="Y") $up_resno_type="M";
	else if($up_resno_type=="N") $up_resno_type="N";

	$count=0;
	$temparray1 = explode("",$text1);
	$temparray2 = explode("",$text2);
	$temparray3 = explode("",$text3);
	$cnt=count($temparray1);
	for($i=1;$i<=$cnt;$i++){
		$temp1=trim($temparray1[$i]); $temp2=trim($temparray2[$i]); $temp3=trim($temparray3[$i]);
		if(strlen($temp1)>0 && strlen($temp2)>0 && strlen($temp3)>0){
			if($count!=0) $temp.="=";
			$count++;
			$temp.=$temp1."=".$temp2."=".$temp3;
		}
	}
	
	$sql = "UPDATE tblshopinfo SET ";
	$sql.= "memberout_type		= '".$up_memberout_type."', ";
	$sql.= "resno_type			= '".$up_resno_type."', ";
	$sql.= "member_addform		= '".$temp."' ";
	
	// 도매회원 관련 추가
	$sql.= ",wholesalemember='".(($_POST['up_wholesalemember']=='Y')?'Y':'N')."'";
	// # 도매회원 관련 추가
	$update = mysql_query($sql,get_db_conn());
	
	if(!_empty($_POST['ext_memconf_reqgender'])){
		@mysql_query("insert into extra_conf (type,name,value) VALUES ('memconf','reqgender','".($_POST['ext_memconf_reqgender'])."') ON DUPLICATE KEY UPDATE value=VALUES(value)",get_db_conn());
	}
	
	if(!_empty($_POST['ext_memconf_reqbirth'])){
		@mysql_query("insert into extra_conf (type,name,value) VALUES ('memconf','reqbirth','".($_POST['ext_memconf_reqbirth'])."') ON DUPLICATE KEY UPDATE value=VALUES(value)",get_db_conn());
	}
	
	
	DeleteCache("tblshopinfo.cache");
	
	
	$onload="<script>alert('회원가입 관련 설정이 완료되었습니다.');</script>";
}

//$sql = "SELECT memberout_type,resno_type,member_addform FROM tblshopinfo ";
$sql = "SELECT memberout_type,resno_type,member_addform,wholesalemember FROM tblshopinfo ";
$result = mysql_query($sql,get_db_conn());
$row = mysql_fetch_object($result);
$memberout_type = $row->memberout_type;
$resno_type = $row->resno_type;
if ($resno_type=="Y") $resno_type2="N";
else if ($resno_type=="M") {
	$resno_type="Y";
	$resno_type2="Y";
} else {
	$resno_type2="N";
}
if (strlen($row->member_addform)!=0) {
	$fieldarray=explode("=",$row->member_addform);
	$num=sizeof($fieldarray)/3;
	for($i=0;$i<$num;$i++){
		$field_length1[$i]=$fieldarray[$i*3+1];
		$max_length1[$i]=$fieldarray[$i*3+2];
		if (substr($fieldarray[$i*3],-1,1)=="^") {
			$field_name1[$i] = substr($fieldarray[$i*3],0,strlen($fieldarray[$i*3])-1);
			$field_check[$i] = "Y";
		} else {
			$field_name1[$i] = $fieldarray[$i*3];
			$field_check[$i] = "N";
		}
	}
}
mysql_free_result($result);

${"check_resno_type".$resno_type} = "checked";
${"check_resno_type2".$resno_type2} = "checked";
${"check_memberout_type".$memberout_type} = "checked";
?>

<? INCLUDE "header.php"; ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm() {
	var form = document.form1;
	var isMax=false;
	form.text1.value="";
	form.text2.value="";
	form.text3.value="";
	for(i=0;i<form.field_name.length;i++){
		if(isNaN(form.field_length[i].value)){
			alert("필드 길이는 숫자만 입력 가능합니다.");
			form.field_length[i].focus();
			return;
		}
		if(isNaN(form.max_length[i].value)){
			alert("필드 최대길이는 숫자만 입력 가능합니다.");
			form.max_length[i].focus();
			return;
		}
		if(form.field_name[i].value.indexOf('')>=0){
			alert("'' 문자는 입력하실 수 없습니다.");
			form.field_name[i].focus();
			return;
		}
		if(form.field_name[i].value.indexOf('^')>=0){
			alert("'^' 문자는 입력하실 수 없습니다.");
			form.field_name[i].focus();
			return;
		}
		if(form.field_length[i].value>40){
			alert("필드길이는 최대 40까지 가능합니다.");
			form.field_length[i].focus();
			return;
		}
		if((form.field_name[i].value.length!=0 && form.field_length[i].value.length==0) || (form.field_name[i].value.length==0 && form.field_length[i].value.length!=0)){
			alert("추가입력폼 입력이 잘못되었습니다.\n\n다시 확인하시기 바랍니다.");
			if(form.field_length[i].value.length==0) form.field_length[i].focus();
			else form.field_name[i].focus();
			return;
		}
		if(form.field_name[i].value.length!=0 && form.field_length[i].value.length!=0 && form.max_length[i].value.length==0){
			isMax=true;
			form.max_length[i].value=form.field_length[i].value;
		}
		if(form.field_name[i].value.length!=0 && form.field_length[i].value.length!=0 && form.max_length[
		i].value.length!=0){
			if (form.field_check[i].checked==true) {
				chk_val = '^';
			} else {
				chk_val = '';
			}
			form.text1.value=form.text1.value+""+form.field_name[i].value+chk_val;
			form.text2.value=form.text2.value+""+form.field_length[i].value;
			form.text3.value=form.text3.value+""+form.max_length[i].value;
		}
	}
	if (isMax==true) {
		if (!confirm("최대 길이를 입력하지 않으시면 필드길이와 같은 값으로 입력됩니다.")) {
			return;
		}
	}
	form.type.value="up";
	form.submit();
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
				<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 상점관리 &gt; 쇼핑몰 운영 설정 &gt; <span class="2depth_select">회원관련 설정</span></td>
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
					<TD><IMG SRC="images/shop_member_title.gif"  ALT=""></TD>
					</tr>
<tr>
<TD width="100%" background="images/title_bg.gif" height="21"></TD>
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
					<TD width="100%" class="notice_blue">기본 회원가입 입력폼 + 추가 입력폼 , 주민번호 사용 및 탈퇴설정을 할 수 있습니다.</TD>
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
			<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post>
			<input type=hidden name=type>
			<input type=hidden name=text1>
			<input type=hidden name=text2>
			<input type=hidden name=text3>
			<!-- 도메인 회원 관련 추가 부분  // 상단의 form 및 hidden input 위치도 변경됨 -->
			<tr><td height="30"></td></tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/shop_member_stitle_new1.gif" ALT="도매회원 기능 사용 여부"></TD>
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
					<TD width="100%" class="notice_blue">1) 도매회원은 상품 정보에 도매가가 있을 경우 해당 가격으로 표시 및 주문 됩니다.<br>2) 도매회원은 가입후 관리자의 승인을 필요로 합니다. <br />3) 도매회원은 적립금 및 할인 적용이 불가 합니다.</TD>
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
					<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">도매회원 허용여부</TD>
					<TD class="td_con1"><input type=radio name='up_wholesalemember' id="idx_wholesalemember1" value="Y"  <? if($row->wholesalemember == 'Y') echo 'checked'; ?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for='idx_wholesalemember1'>도매회원 허용</label>  &nbsp;&nbsp;&nbsp;&nbsp;<input type=radio id="idx_wholesalemember2" name='up_wholesalemember' value="N"  <? if($row->wholesalemember != 'Y') echo 'checked'; ?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for='idx_wholesalemember2'>도매회원 허용하지 않음</label></TD>
				</TR>
				<TR>
					<TD colspan=2 background="images/table_top_line.gif"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<!-- #도매인 회원 허용 관련 끝 -->
			<tr><td height="20"></td></tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/shop_member_stitle1.gif" WIDTH="188" HEIGHT=31 ALT=""></TD>
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
					<TD width="100%" class="notice_blue">1) 최대길이를 입력하지 않으면 필드길이와 동일하게 등록됩니다.<br>2) 필드의 최대 길이는 250입니다.<br>3) 추가입력폼은 기본입력폼 하단에 표기됩니다.</TD>
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
					<TD background="images/table_top_line.gif" colspan=5></TD>
				</TR>
				<TR bgColor=#f0f0f0 height=26>
					<TD class="table_cell" align=middle width="28">순번</TD>
					<TD class="table_cell1" align=middle >필드명</TD>
					<TD class="table_cell1" align=middle width="156">필드길이</TD>
					<TD class="table_cell1" align=middle width="152">입력최대길이</TD>
					<TD class="table_cell1" align=middle width="38">필수</TD>
				</TR>
				<TR>
					<TD colspan="5" background="images/table_con_line.gif"></TD>
				</TR>
<?
	for($i=0;$i<10;$i++){
		if ($i == 9) {
			$line_bottom = "bottom";
		}
?>
				<tr bgcolor=#ffffff height=25>
					<TD class="table_cell" align=middle bgColor=#f0f0f0 width="28"><?=$i+1?></TD>
					<TD class="td_con1" style="PADDING-LEFT: 10px" align=left ><input type=text name=field_name value="<?=$field_name1[$i]?>" maxlength=45 style="width:97%" class="input"></TD>
					<TD class="td_con1" align=middle width="156"><input type=text name=field_length value="<?=$field_length1[$i]?>" maxlength=3 style="width:95%" class="input"></TD>
					<TD class="td_con1" align=middle width="152"><input type=text name=max_length value="<?=$max_length1[$i]?>" maxlength=3 style="width:95%" class="input"></TD>
					<TD class="td_con1" align=middle width="38"><input type=checkbox name=field_check value="Y" <? if ($field_check[$i]=="Y") echo "checked"; ?>></TD>
				</TR>
				<TR>
					<TD colspan="5" background="images/table_con_line.gif"></TD>
				</TR>
<?
	}
?>
				<TR>
					<TD background="images/table_top_line.gif" colspan=5></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height="30"></td></tr>
			<?
			$ext_cont = array();
			$esql = "select * from extra_conf where type='memconf'";
			if(false !== $eres = mysql_query($esql,get_db_conn())){
				$erowcount = mysql_num_rows($eres);
				if($erowcount>0){
					while($erow = mysql_fetch_assoc($eres)){
						$ext_cont[$erow['name']] = $erow['value'];
					}
				}else{
					$ext_cont['reqgender']=$ext_cont['reqbirth']="H";
				}
			}
			?>
			
			<tr>
				<td style="padding-bottom:3px;"><IMG SRC="images/shop_member_stitle_req.gif" HEIGHT=31 ALT="회원 가입 필수 입력 설정"></td>
			</tr>
			<tr>
				<td>
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0 style="margin-bottom:30px;">
				<TR>
					<TD colspan=2 background="images/table_top_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">성별</TD>
					<TD class="td_con1">
						<input type="radio" name="ext_memconf_reqgender" id="ext_memconf_reqgender1" value="Y" <?=($ext_cont['reqgender']=='Y')?'checked="checked"':''?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for="ext_memconf_reqgender1">필수 입력</label>  
						<input type="radio" name="ext_memconf_reqgender" id="ext_memconf_reqgender2" value="N" <?=($ext_cont['reqgender']=='N')?'checked="checked"':''?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for="ext_memconf_reqgender2">선택적 입력</label>
						<input type="radio" name="ext_memconf_reqgender" id="ext_memconf_reqgender3" value="H" <?=($ext_cont['reqgender']=='H')?'checked="checked"':''?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for="ext_memconf_reqgender3">받지 않음</label>
						</TD>
				</TR>
				<TR>
					<TD colspan=2 background="images/table_top_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">생년월일</TD>
					<TD class="td_con1">
						<input type="radio" name="ext_memconf_reqbirth" id="ext_memconf_reqbirth1" value="Y" <?=($ext_cont['reqbirth']=='Y')?'checked="checked"':''?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for="ext_memconf_reqbirth1">필수 입력</label>  
						<input type="radio" name="ext_memconf_reqbirth" id="ext_memconf_reqbirth2" value="N" <?=($ext_cont['reqbirth']=='N')?'checked="checked"':''?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for="ext_memconf_reqbirth2">선택적 입력</label>
						<input type="radio" name="ext_memconf_reqbirth" id="ext_memconf_reqbirth3" value="H" <?=($ext_cont['reqbirth']=='H')?'checked="checked"':''?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for="ext_memconf_reqbirth3">받지 않음</label>
						</TD>
				</TR>
				<TR>
					<TD colspan=2 background="images/table_top_line.gif"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			
			
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/shop_member_stitle2.gif" WIDTH="188" HEIGHT=31 ALT=""></TD>
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
					<TD width="100%" class="notice_blue">1) <b>14세 미만</b>의 경우는 보호자의 동의가 필요합니다.<br>2) <b>주민번호 미입력</b>으로 인한 모든 법적 책임은 쇼핑몰에 있습니다.</TD>
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
					<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">주민번호 입력여부 선택</TD>
					<TD class="td_con1"><input type=radio id="idx_resno_type1" name=up_resno_type value="Y" <?=$check_resno_typeY?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_resno_type1>회원 가입시 주민등록번호 입력</label>  &nbsp;&nbsp;&nbsp;&nbsp;<input type=radio id="idx_resno_type2" name=up_resno_type value="N" <?=$check_resno_typeN?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_resno_type2>회원 가입시 주민등록번호 미입력</label></TD>
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
					<TD><IMG SRC="images/shop_member_stitle3.gif" WIDTH="188" HEIGHT=31 ALT=""></TD>
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
					<TD width="100%" class="notice_blue">회원가입 후 회원정보 수정에서 주민번호의 변경가능 유무를 설정할 수 있습니다.</TD>
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
					<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">수정여부 선택</TD>
					<TD class="td_con1"><input type=radio id="idx_resno_type21" name=up_resno_type2 value="Y" <?=$check_resno_type2Y?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_resno_type21>회원이 주민등록번호 수정가능</label>  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=radio id="idx_resno_type22" name=up_resno_type2 value="N" <?=$check_resno_type2N?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_resno_type22>입력된 주민등록번호 수정불가능</label></TD>
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
					<TD><IMG SRC="images/shop_member_stitle4.gif" WIDTH="188" HEIGHT=31 ALT=""></TD>
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
					<TD width="100%" class="notice_blue">1) 회원이 스스로 회원탈퇴를 할 수 있는 메뉴를 <B>[회원정보수정]</B> 화면에 추가할 수 있습니다.<br>
					2) 탈퇴기능을 사용할 경우 [회원정보수정] 화면에 표시됩니다.<br>
					3) 개별디자인시에는 왼쪽, 상단, 로그인폼 관리에서 해당 링크 추가가 가능합니다.</TD>
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
					<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">회원탈퇴 기능 선택</TD>
					<TD class="td_con1"><input type=radio id="idx_memberout_type1" name=up_memberout_type value="Y" <?=$check_memberout_typeY?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_memberout_type1>관리자인증후 회원탈퇴</label>  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=radio id="idx_memberout_type2" name=up_memberout_type value="O" <?=$check_memberout_typeO?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_memberout_type2>자동 회원 탈퇴</label>  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=radio id="idx_memberout_type3" name=up_memberout_type value="N" <?=$check_memberout_typeN?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_memberout_type3>회원 탈퇴메뉴 사용안함</label></TD>
				</TR>
				<TR>
					<TD colspan=2 background="images/table_top_line.gif"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
					
			<tr><td height=10></td></tr>
			<tr>
				<td align="center"><a href="javascript:CheckForm();"><img src="images/botteon_save.gif" width="113" height="38" border="0"></a></td>
			</tr>
			</form>
			<tr><td height=20></td></tr>
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
						<td ><span class="font_dotline">주민번호 미사용 설정 특성</span></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td  class="space_top">- 주민번호 미사용시 <b>[아이디/비빌번호찾기]</b> 는 이름과 가입메일주소로 잧을 수 있습니다.</td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td  class="space_top">- 실명인증서비스를 받고 있는 경우라도 실명인증이 연결되지 않습니다.</td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td  class="space_top">- 실명인증서비스를 받는 도중 잠시 실명인증을 하지 않거나 실명인증 사용 월정액이 초과되어 추가비용 발생시<br><b>&nbsp;&nbsp;</b>주민번호 미사용으로 설정하면 실명인증서비스 받지 않을 수 있습니다. </td>
					</tr>
					<tr>
						<td height="20" colspan="2"></td>
					</tr>
					<tr>
						<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td ><span class="font_dotline">회원탈퇴 처리 특성</span></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td  class="space_top">- 관리자인증 후 탈퇴인 경우 관리자가 탈퇴 인증을 하면 회원리스트에서 즉시 삭제됩니다.</td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td  class="space_top">- 자동 탈퇴일 경우는 회원리스트에서 즉시 삭제됩니다.</td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td  class="space_top">- 삭제된 회원정보는 복구되지 않습니다.</td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td  class="space_top">- 탈퇴된 회원의 주문리스트는 별도 삭제해주세요.</td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td  class="space_top">- 거래정보 확인을 위해 회원탈퇴 후 거래내역정보 등의 보관 기간을 회원가입 또는 개인정보보호정책에 표명을 권장합니다.<br>
						<b>&nbsp;&nbsp;</b>예) 상법 등 관련법령의 규정에 의하여 다음과 같이 거래 관련 권리 의무 관계의 확인 등을 이유로 일정기간 보유합니다.<br>
						<b>&nbsp;&nbsp;</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;* 계약 또는 청약철회 등에 관한 기록 : 5년<br>
						<b>&nbsp;&nbsp;</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;* 대금결제 및 재화등의 공급에 관한 기록 : 5년<br>
						<b>&nbsp;&nbsp;</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;* 소비자의 불만 또는 분쟁처리에 관한 기록 : 3년
						</td>
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
<?=$onload?>

<? INCLUDE "copyright.php"; ?>