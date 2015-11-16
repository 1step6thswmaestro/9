<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
INCLUDE ("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "me-1";
$MenuCode = "member";
if (!$_usersession->isAllowedTask($PageCode)) {
	INCLUDE ("AccessDeny.inc.php");
	exit;
}
#########################################################

@set_time_limit(300);

setlocale(LC_CTYPE, 'ko_KR.eucKR');

if(!function_exists('fputcsv')) {
	function fputcsv(&$handle, $fields = array(), $delimiter = ',', $enclosure = '"') {
		$str = '';
		$escape_char = '\\';
		foreach ($fields as $value) {
			if (strpos($value, $delimiter) !== false ||
			strpos($value, $enclosure) !== false ||
			strpos($value, "\n") !== false ||
			strpos($value, "\r") !== false ||
			strpos($value, "\t") !== false ||
			strpos($value, ' ') !== false) {
				$str2 = $enclosure;
				$escaped = 0;
				$len = strlen($value);
				for ($i=0;$i<$len;$i++) {
					if ($value[$i] == $escape_char) {
						$escaped = 1;
					} else if (!$escaped && $value[$i] == $enclosure) {
						$str2 .= $enclosure;
					} else {
						$escaped = 0;
					}
					$str2 .= $value[$i];
				}
				$str2 .= $enclosure;
				$str .= $str2.$delimiter;
			} else {
				$str .= $value.$delimiter;
			}
		}
		$str = substr($str,0,-1);
		$str .= "\n";
		return fwrite($handle, $str);
	}
}

$imagepath=$Dir.DataDir."shopimages/etc/";
$filename="memexcelupfile.csv";
$filepath2=$imagepath."member_error.csv";
@unlink($imagepath.$filename);

$mode=$_POST["mode"];
$group_code=$_POST["group_code"];
$upfile=$_FILES["upfile"];

$reg_group=$_shopdata->group_code;

$group_list=array();
$sql = "SELECT group_code,group_name FROM tblmembergroup ";
$result = mysql_query($sql,get_db_conn());
while($row=mysql_fetch_object($result)){
	if(strlen($group_code)>0) {
		if($row->group_code==$group_code) {
			$reg_group=$row->group_code;
		}
	}
	$group_list[]=$row;
}


if($mode=="upload" && strlen($upfile[name])>0 && $upfile[size]>0) {
	########################### TEST 쇼핑몰 확인 ##########################
	DemoShopCheck("데모버전에서는 테스트가 불가능 합니다.", $_SERVER[PHP_SELF]);
	#######################################################################

	$ext = strtolower(substr($upfile[name],strlen($upfile[name])-3,3));
	if($ext=="csv") {
		copy($upfile[tmp_name],$imagepath.$filename);
		chmod($imagepath.$filename,0664);
	} else {
		echo "<html><head></head><body onload=\"alert('파일형식이 잘못되어 업로드가 실패하였습니다.\\n\\n등록 가능한 파일은 엑셀(CSV) 파일만 등록 가능합니다.');location='".$_SERVER["PHP_SELF"]."'\"></body></html>";exit;
	}

	########################################################################################################
	# 0=>아이디, 1=>비밀번호, 2=>이름, 3=>주민번호, 4=>이메일, 5=>휴대폰, 6=>이메일수신여부, 7=>SMS수신여부
	# 8=>집전화, 9=>집우편번호, 10=>집주소(동/읍/면 이상), 11=>집주소(번지 미만), 12=>회사전화
	# 13=>회사우편번호, 14=>회사주소(동/읍/면 이상), 15=>회사주소(번지 미만), 16=>적립금, 17=>가입일
	########################################################################################################

	$query="INSERT INTO tblmember (id,passwd,name,resno,email,mobile,news_yn,gender,home_post,home_addr,home_tel,office_post,office_addr,office_tel,reserve,joinip,date,group_code) VALUES ";

	$error_list=array();
	$memcnt=0;
	$filepath=$imagepath.$filename;
	$fp=fopen($filepath,"r");
	$yy=0;
	while($field=@fgetcsv($fp, 4096, ",")) {
		if($yy++==0) continue;

		$id=trim($field[0]);
		$passwd=trim($field[1]);
		$name=trim($field[2]);
		$resno=trim($field[3]);
		$email=trim($field[4]);
		$mobile=trim($field[5]);
		$news_mail_yn=trim($field[6]);
		$news_sms_yn=trim($field[7]);
		$home_tel=trim($field[8]);
		$home_post=trim($field[9]);
		$home_post=@str_replace("-","",$home_post);
		$home_addr1=trim($field[10]);
		$home_addr2=trim($field[11]);
		$office_tel=trim($field[12]);
		$office_post=trim($field[13]);
		$office_post=@str_replace("-","",$office_post);
		$office_addr1=trim($field[14]);
		$office_addr2=trim($field[15]);
		$reserve=(int)trim($field[16]);

		$date=trim(@str_replace("/","",$field[17]));
		$date=@str_replace("-","",$date);
		if(strlen($date)!=8) $date=date("Ymd");
		$date.="000000";

		if(!preg_match("/^(Y|N)$/",$news_mail_yn)) {
			$news_mail_yn="Y";
		}
		if(!preg_match("/^(Y|N)$/",$news_sms_yn)) {
			$news_sms_yn="Y";
		}
		if($news_mail_yn=="Y" && $news_sms_yn=="Y") {
			$news_yn="Y";
		} else if($news_mail_yn=="Y") {
			$news_yn="M";
		} else if($news_sms_yn=="Y") {
			$news_yn="S";
		} else {
			$news_yn="N";
		}

		$home_addr="";
		if(strlen($home_post)==6) $home_addr=$home_addr1."=".$home_addr2;
		$home_addr = str_replace("'","\'",$home_addr);

		$office_addr="";
		if(strlen($office_post)==6) $office_addr=$office_addr1."=".$office_addr2;
		$office_addr = str_replace("'","\'",$office_addr);

		$resno=str_replace("-","",$resno);
		if(strlen($resno)==13) {
			if(!chkResNo($resno)) $resno="";
		} else if(strlen($resno)==41) {

			//7011031[670b14728ad9902aecba32e22fa4f6bd]
		} else {
			$resno="";
		}

		$joinip="127.0.0.1";

		if(strlen($id)==0 || strlen($passwd)==0 || strlen($name)==0 || strlen($email)==0) {
			$error_list[]=$field;
			continue;
		} else if($_shopdata->resno_type!="N" && strlen($resno)==0) {
			$error_list[]=$field;
			continue;
		} else if(!IsAlphaNumeric($id)) {
			$error_list[]=$field;
			continue;
		} else if(!ismail($email)) {
			$error_list[]=$field;
			continue;
		} else if($passwd=="삭제회원" || substr($resno,0,7)=="9999999") {
			$error_list[]=$field;
			continue;
		}

		//아이디 중복 체크
		$sql = "SELECT COUNT(*) as cnt FROM tblmember WHERE id='".$id."' ";
		$result=mysql_query($sql,get_db_conn());
		$row=mysql_fetch_object($result);
		mysql_free_result($result);
		if($row->cnt>=1) {
			$error_list[]=$field;
			continue;
		}

		$gender="";
		if(strlen($resno)>0) {		//주민번호 중복체크
			$sql = "SELECT COUNT(*) as cnt FROM tblmember WHERE resno='".$resno."' ";
			$result=mysql_query($sql,get_db_conn());
			$row=mysql_fetch_object($result);
			mysql_free_result($result);
			if($row->cnt>=1) {
				$error_list[]=$field;
				continue;
			}
			$gender=substr($resno,6,1);
		}

		//이메일 중복 체크
		$sql = "SELECT COUNT(*) as cnt FROM tblmember WHERE email='".$email."' ";
		$result=mysql_query($sql,get_db_conn());
		$row=mysql_fetch_object($result);
		mysql_free_result($result);
		if($row->cnt>=1) {
			$error_list[]=$field;
			continue;
		}

		//비밀번호 재조정
		if(strlen($passwd)<16) {
			$passwd=md5($passwd);
		}

		$memcnt++;
		$query.= "('".$id."','".$passwd."','".$name."','".$resno."','".$email."','".$mobile."','".$news_yn."','".$gender."','".$home_post."','".$home_addr."','".$home_tel."','".$office_post."','".$office_addr."','".$office_tel."','".$reserve."','".$joinip."','".$date."','".$reg_group."'),";

		if($memcnt==1000) {
			$query=substr($query,0,-1);
			mysql_query($query,get_db_conn());
			$memcnt=0;
			$query="INSERT INTO tblmember (id,passwd,name,resno,email,mobile,news_yn,gender,home_post,home_addr,home_tel,office_post,office_addr,office_tel,reserve,joinip,date,group_code) VALUES ";
		}
	}
	@fclose($fp);
	@unlink($filepath);

	if($memcnt>0) {
		$query=substr($query,0,-1);
		mysql_query($query,get_db_conn());
	}

	@unlink($filepath2);
	if(count($error_list)>0) {
		$fp2=fopen($filepath2,"a");
		for($i=0;$i<count($error_list);$i++) {
			if(strlen($error_list[$i])>0) {
				fputcsv($fp2,$error_list[$i]);
			}
		}
		@fclose($fp2);
	}

	echo "<html><head></head><body onload=\"alert('회원정보 등록이 완료되었습니다.');location.href='".$_SERVER["PHP_SELF"]."';\"></body></html>";exit;
} else if($mode=="error_del") {
	@unlink($filepath2);
}

?>

<? INCLUDE "header.php"; ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
var isupload=false;
function CheckForm() {
	if(isupload==true) {
		alert("######### 현재 회원정보를 등록중입니다. #########");
		return;
	}

	if(document.form1.group_code.value=="") {
		if(!confirm("회원등급을 설정하지않고 등록하시겠습니까?")) {
			return;
		}
	} else {
		temp=document.form1.group_code.options[document.form1.group_code.selectedIndex].text;
		if(!confirm("\""+temp+"\" 회원등급으로 등록하시겠습니까?")) {
			return;
		}
	}

	isupload=true;
	document.all.uploadButton.style.filter = "Alpha(Opacity=60) Gray";
	document.form1.mode.value="upload";
	document.form1.submit();
}

function delete_errfile() {
	if(isupload==true) {
		alert("######### 현재 회원정보를 등록중입니다. #########");
		return;
	}
	if(confirm("등록 실패한 회원정보 엑셀파일을 서버에서 삭제하시겠습니까?")) {
		document.form1.mode.value="error_del";
		document.form1.submit();
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
			<? include ("menu_member.php"); ?>
			</td>

			<td></td>
			<td valign="top">




<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td height="29" colspan="3">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 회원관리 &gt; 회원정보관리 &gt; <span class="2depth_select">회원정보 일괄 등록</span></td>
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
					<TD><IMG SRC="images/member_excelupload_title.gif" border=0></TD>
					</tr>
<tr>
<TD width="100%" background="images/title_bg.gif" height="21"></TD>
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
					<TD width="100%" class="notice_blue">다수의 회원정보를 엑셀파일로 만들어 일괄 등록을 하는 기능입니다.</TD>
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
			<tr><td height=20></td></tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/member_excelupload_stitle1.gif" board=0></TD>
					<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif"></TD>
					<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height=3></td></tr>
			<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post enctype="multipart/form-data">
			<input type=hidden name=mode>
			<tr>
				<td>
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<TD background="images/table_top_line.gif" colspan=2></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">엑셀 등록양식 다운로드</TD>
					<TD class="td_con1" ><A HREF="images/sample/member.csv"><img src="images/btn_down1.gif" border=0 align=absmiddle></A> <span class="font_orange">＊엑셀(CSV)파일을 내려받은 후 예제와 같이 작성합니다.</span></TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">회원등급 선택</TD>
					<TD class="td_con1" >
					<select name=group_code>
						<option value="">회원등급을 선택하세요.</option>
<?
						for($i=0;$i<count($group_list);$i++) {
							echo "<option value=\"".$group_list[$i]->group_code."\">".$group_list[$i]->group_name."</option>\n";
						}
?>
					</select>
					<span class="font_orange">＊등급설정은 <B>"회원관리 -> 회원등급 설정"</B>에서 하시면 됩니다.</span>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">엑셀파일(CSV) 등록</TD>
					<TD class="td_con1" ><input type=file name=upfile style="width:54%" class="input"> <span class="font_orange">＊엑셀(CSV) 파일만 등록 가능합니다.</span></TD>
				</TR>

				<?if(file_exists($filepath2)==true){?>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0"><font color=red>등록실패 엑셀 관리</font></TD>
					<TD class="td_con1" ><A HREF="<?=$filepath2?>"><B>[다운로드]</B></A> <img width=10 height=0> <A HREF="javascript:delete_errfile()"><B>[삭제하기]</B></A> &nbsp;&nbsp;&nbsp; <span class="font_orange">＊등록 실패한 데이터만 엑셀(CSV)파일로 다운/삭제 하실 수 있습니다.</span></TD>
				</TR>
				<?}?>

				<TR>
					<TD background="images/table_top_line.gif" colspan=2></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td align="center" height=10></td>
			</tr>
			<tr>
				<td align="center"><img src="images/btn_fileup.gif" id="uploadButton" width="113" height="38" border="0" style="cursor:hand" onclick="CheckForm(document.form1);"></td>
			</tr>
			</form>
			<tr>
				<td height=20></td>
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
					<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
					<col width=20></col>
					<col width=></col>
					<tr>
						<td align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td><span class="font_dotline">회원정보 일괄 등록</span></td>
					</tr>
					<tr>
						<td align="right">&nbsp;</td>
						<td class="space_top" style="letter-spacing:-0.5pt;">
						- 회원정보를 일괄 등록 하거나, 타 쇼핑몰 이용 고객 정보를 이전하는데 유용하게 사용됩니다.
						<br>
						<span class="font_orange" style="padding-left:0px"><B>- 회원데이터를 이전할 경우 회원의 동의가 꼭 필요하오니 회원 동의 후 이전하시기 바랍니다.</B></span>
						</td>
					</tr>
					<tr>
						<td colspan="2" height="20"></td>
					</tr>
					<tr>
						<td align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td><span class="font_dotline">엑셀(CSV)파일 작성 순서</span></td>
					</tr>
					<tr>
						<td align="right">&nbsp;</td>
						<td class="space_top" style="letter-spacing:-0.5pt;">
						- 엑셀파일 작성시 두번 째 라인부터 데이터를 입력하시기 바랍니다. (첫 라인은 필드 설명부분)<br>
						- 아래 형식대로 <FONT class=font_orange><B>엑셀파일 작성 -> 다른이름으로 저장 -> CSV(쉼표로 분리)</B></font> 순으로 저장하시면 됩니다.</td>
					</tr>
					<tr>
						<td colspan="2" height="20"></td>
					</tr>
					<tr>
						<td align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td><span class="font_dotline">회원정보 일괄등록 방법</span></td>
					</tr>
					<tr>
						<td align="right">&nbsp;</td>
						<td class="space_top" style="letter-spacing:-0.5pt;">- ① 아래의 형식을 참고로 회원정보 엑셀파일을 작성합니다.<br>
						<span class="font_orange" style="padding-left:10px">----------------------------------------------------- 상품정보 엑셀 형식 -----------------------------------------------------</span><br>
						<span class="font_blue" style="padding-left:25px">아이디, 비밀번호, 이름, 주민번호, 이메일, 휴대폰, 이메일수신, SMS수신, 집전화, 집우편번호, 집주소(동/읍/면 이상), </span>
						<br>
						<span class="font_blue" style="padding-left:25px">집주소(번지 미만), 회사전화, 회사우편번호, 회사주소(동/읍/면 이상), 회사주소(번지 미만), 적립금, 가입일<span><br>
						<span class="font_orange" style="padding-left:10px">------------------------------------------------------------------------------------------------------------------------------</span><br>

						<div style="padding-left:30">
						<table border=0 cellpadding=0 cellspacing=0 width=600>
						<col width=145></col>
						<col width=></col>
						<tr>
							<td colspan=2 align=center style="padding-bottom:5">
							<B>회원정보 엑셀 작성 예)</B>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">아이디<FONT class=font_orange>(*)</font></td>
							<td class=td_con1 style="padding-left:5;">
							getmall <img width=20 height=0><FONT class=font_orange>(영문/숫자 4~12자) <B>- 아이디 중복시 등록이 않됩니다</B></font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">비밀번호<FONT class=font_orange>(*)</font></td>
							<td class=td_con1 style="padding-left:5;">
							1234 <img width=20 height=0><FONT class=font_orange>(암호화된 비밀번호도 등록 가능)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">이름<FONT class=font_orange>(*)</font></td>
							<td class=td_con1 style="padding-left:5;">
							홍길동
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">주민번호

							<?if($_shopdata->resno_type!="N") {?>
							<FONT class=font_orange>(*)</font>
							<?}?>

							</td>
							<td class=td_con1 style="padding-left:5;">
							701103-1000000 <FONT class=font_orange>=>일반적인 주민번호</font>
							<br>701103-1[670b14728ad9902aecba32e22fa4f6bd] <FONT class=font_orange>=> 주민번호 뒤6자리 암호화</font>
							<br>
							<FONT class=font_orange><B>(주민번호 중복시 등록이 않됩니다.)</B></font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">이메일<FONT class=font_orange>(*)</font></td>
							<td class=td_con1 style="padding-left:5;">
							master@getmall.co.kr <img width=20 height=0><FONT class=font_orange><B>(이메일 중복시 등록이 않됩니다.)</B></font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">휴대폰</td>
							<td class=td_con1 style="padding-left:5;">
							010-000-0000
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">이메일 수신여부<FONT class=font_orange>(*)</font></td>
							<td class=td_con1 style="padding-left:5;">
							Y
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">SMS 수신여부<FONT class=font_orange>(*)</font></td>
							<td class=td_con1 style="padding-left:5;">
							Y
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">집전화<FONT class=font_orange>(*)</font></td>
							<td class=td_con1 style="padding-left:5;">
							02-00-0000
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">집 우편번호<FONT class=font_orange>(*)</font></td>
							<td class=td_con1 style="padding-left:5;">
							137-070
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">집주소 (동/읍/면 이상)<FONT class=font_orange>(*)</font></td>
							<td class=td_con1 style="padding-left:5;">
							서울시 서초구 서초동
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">집주소 (번지 미만)<FONT class=font_orange>(*)</font></td>
							<td class=td_con1 style="padding-left:5;">
							1358-18번지 XX빌딩 8층
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">회사전화</td>
							<td class=td_con1 style="padding-left:5;">
							02-111-1111
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">회사 우편번호</td>
							<td class=td_con1 style="padding-left:5;">
							137-073
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">회사주소 (동/읍/면 이상)</td>
							<td class=td_con1 style="padding-left:5;">
							서울시 서초구 방배동
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">회사주소 (번지 미만)</td>
							<td class=td_con1 style="padding-left:5;">
							18-18번지 XX빌딩 3층
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">적립금</td>
							<td class=td_con1 style="padding-left:5;">
							0
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">가입일</td>
							<td class=td_con1 style="padding-left:5;">
							2007/05/10 <img width=20 height=0><FONT class=font_orange>(현재 날짜로 등록시 공란)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						</table>
						</div>

						<span class="font_orange" style="padding-left:10px">------------------------------------------------------------------------------------------------------------------------------</span>
						</td>
					</tr>
					<tr>
						<td align="right">&nbsp;</td>
						<td class="space_top" style="letter-spacing:-0.5pt;">- ② 엑셀(CSV)파일을 선택합니다.</td>
					</tr>
					<tr>
						<td align="right">&nbsp;</td>
						<td class="space_top" style="letter-spacing:-0.5pt;">- ③ [파일등록] 버튼을 이용하여 업로드 완료 하면 회원정보가 등록됩니다.</td>
					</tr>
					</table>
					</TD>
					<TD background="images/manual_right1.gif"><IMG SRC="images/manual_right1.gif" WIDTH=18 HEIGHT="2" ALT=""></TD>
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