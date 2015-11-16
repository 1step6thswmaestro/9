<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/venderlib.php");
INCLUDE ("access.php");

$mode=$_POST["mode"];
if($mode=="update") {
	$cust_tel="";
	$cust_tel1=$_POST["cust_tel1"];
	$cust_tel2=$_POST["cust_tel2"];
	$cust_tel3=$_POST["cust_tel3"];
	if(strlen($cust_tel1)>=2 && strlen($cust_tel2)>=3 && strlen($cust_tel3)>=3) {
		$cust_tel=$cust_tel1."-".$cust_tel2."-".$cust_tel3;
	}
	$cust_fax="";
	$cust_fax1=$_POST["cust_fax1"];
	$cust_fax2=$_POST["cust_fax2"];
	$cust_fax3=$_POST["cust_fax3"];
	if(strlen($cust_fax1)>=2 && strlen($cust_fax2)>=3 && strlen($cust_fax3)>=3) {
		$cust_fax=$cust_fax1."-".$cust_fax2."-".$cust_fax3;
	}
	$cust_email=$_POST["cust_email"];

	$cust_time1_type=(int)$_POST["cust_time1_type"];
	$cust_time1_start=$_POST["cust_time1_start"];
	$cust_time1_end=$_POST["cust_time1_end"];
	$cust_time2_type=(int)$_POST["cust_time2_type"];
	$cust_time2_start=$_POST["cust_time2_start"];
	$cust_time2_end=$_POST["cust_time2_end"];
	$cust_time3_type=(int)$_POST["cust_time3_type"];
	$cust_time3_start=$_POST["cust_time3_start"];
	$cust_time3_end=$_POST["cust_time3_end"];

	$cust_time1="";
	$cust_time2="";
	$cust_time3="";
	if($cust_time1_type==0) {
		$cust_time1=0;
	} else {
		if(strlen($cust_time1_start)==0) $cust_time1_start="00";
		if(strlen($cust_time1_end)==0) $cust_time1_end="00";
		$cust_time1.=$cust_time1_start.":00 ~ ".$cust_time1_end.":00";
	}
	if($cust_time2_type==0) {
		$cust_time2=0;
	} else {
		if(strlen($cust_time2_start)==0) $cust_time2_start="00";
		if(strlen($cust_time2_end)==0) $cust_time2_end="00";
		$cust_time2.=$cust_time2_start.":00 ~ ".$cust_time2_end.":00";
	}
	if($cust_time3_type==0) {
		$cust_time3=0;
	} else {
		if(strlen($cust_time3_start)==0) $cust_time3_start="00";
		if(strlen($cust_time3_end)==0) $cust_time3_end="00";
		$cust_time3.=$cust_time3_start.":00 ~ ".$cust_time3_end.":00";
	}

	if(strlen($cust_tel)==0 || strlen($cust_fax)==0 || strlen($cust_email)==0 || strlen($cust_time1)==0 || strlen($cust_time2)==0 || strlen($cust_time3)==0) {
		echo "<html></head><body onload=\"alert('!!! 고객센터 정보를 정확히 입력하세요.')\"></body></html>";exit;
	} else {
		$cust_info="";	$cust_info.="TEL=".$cust_tel."=FAX=".$cust_fax."=EMAIL=".$cust_email."=TIME1=".$cust_time1."=TIME2=".$cust_time2."=TIME3=".$cust_time3;

		$sql = "UPDATE tblvenderstore SET cust_info='".$cust_info."' ";
		$sql.= "WHERE vender='".$_VenderInfo->getVidx()."' ";
		if(mysql_query($sql,get_db_conn())) {
			$_venderdata->cust_info=$cust_info;
			echo "<html></head><body onload=\"alert('고객센터 정보 설정이 완료되었습니다.');parent.location.reload()\"></body></html>";exit;
		} else {
			echo "<html></head><body onload=\"alert('고객센터 정보 등록 중 오류가 발생하였습니다.')\"></body></html>";exit;
		}
	}
	exit;
}

$cust_data=array();
$temp=explode("=",$_venderdata->cust_info);
for ($i=0;$i<count($temp);$i++) {
	if (substr($temp[$i],0,4)=="TEL=")			$cust_data["TEL"]=substr($temp[$i],4);
	else if (substr($temp[$i],0,4)=="FAX=")		$cust_data["FAX"]=substr($temp[$i],4);
	else if (substr($temp[$i],0,6)=="EMAIL=")	$cust_data["EMAIL"]=substr($temp[$i],6);
	else if (substr($temp[$i],0,6)=="TIME1=")	$cust_data["TIME1"]=substr($temp[$i],6);
	else if (substr($temp[$i],0,6)=="TIME2=")	$cust_data["TIME2"]=substr($temp[$i],6);
	else if (substr($temp[$i],0,6)=="TIME3=")	$cust_data["TIME3"]=substr($temp[$i],6);
}

$cust_tel=explode("-",trim($cust_data["TEL"]));
$cust_fax=explode("-",trim($cust_data["FAX"]));
$cust_email=trim($cust_data["EMAIL"]);
$cust_time1=explode("~",trim($cust_data["TIME1"]));
$cust_time2=explode("~",trim($cust_data["TIME2"]));
$cust_time3=explode("~",trim($cust_data["TIME3"]));

$cust_time1[0]=substr(trim($cust_time1[0]),0,2);
$cust_time2[0]=substr(trim($cust_time2[0]),0,2);
$cust_time3[0]=substr(trim($cust_time3[0]),0,2);
$cust_time1[1]=substr(trim($cust_time1[1]),0,2);
$cust_time2[1]=substr(trim($cust_time2[1]),0,2);
$cust_time3[1]=substr(trim($cust_time3[1]),0,2);

$cust_time1_type=1;
$cust_time2_type=1;
$cust_time3_type=1;
if(strlen($cust_time1[0])==1 && $cust_time1[0]==0) {
	$cust_time1_type=0;
	unset($cust_time1);
}
if(strlen($cust_time2[0])==1 && $cust_time2[0]==0) {
	$cust_time2_type=0;
	unset($cust_time2);
}
if(strlen($cust_time3[0])==1 && $cust_time3[0]==0) {
	$cust_time3_type=0;
	unset($cust_time3);
}
?>

<? INCLUDE "header.php"; ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckPreview() {

}

function CheckForm() {
	if(document.form1.cust_tel1.value.length==0 || document.form1.cust_tel2.value.length==0 || document.form1.cust_tel3.value.length==0) {
		alert("문의 전화번호를 정확히 입력하세요.");
		document.form1.cust_tel1.focus();
		return;
	}
	if(!isNumber(document.form1.cust_tel1.value)) {
		alert("문의 전화번호는 숫자만 입력하세요.");
		document.form1.cust_tel1.focus();
		return;
	}
	if(!isNumber(document.form1.cust_tel2.value)) {
		alert("문의 전화번호는 숫자만 입력하세요.");
		document.form1.cust_tel2.focus();
		return;
	}
	if(!isNumber(document.form1.cust_tel3.value)) {
		alert("문의 전화번호는 숫자만 입력하세요.");
		document.form1.cust_tel3.focus();
		return;
	}
	if(document.form1.cust_fax1.value.length==0 || document.form1.cust_fax2.value.length==0 || document.form1.cust_fax3.value.length==0) {
		alert("문의 팩스번호를 정확히 입력하세요.");
		document.form1.cust_tel1.focus();
		return;
	}
	if(!isNumber(document.form1.cust_fax1.value)) {
		alert("문의 팩스번호는 숫자만 입력하세요.");
		document.form1.cust_fax1.focus();
		return;
	}
	if(!isNumber(document.form1.cust_fax2.value)) {
		alert("문의 팩스번호는 숫자만 입력하세요.");
		document.form1.cust_fax2.focus();
		return;
	}
	if(!isNumber(document.form1.cust_fax3.value)) {
		alert("문의 팩스번호는 숫자만 입력하세요.");
		document.form1.cust_fax3.focus();
		return;
	}
	if(document.form1.cust_email.value.length==0) {
		alert("문의 이메일을 입력하세요.");
		document.form1.cust_email.focus();
		return;
	}
	if(!IsMailCheck(document.form1.cust_email.value)) {
		alert("이메일을 형식에 맞게 정확히 입력하세요.");
		document.form1.cust_email.focus();
		return;
	}
	if(document.form1.cust_time1_type[1].checked==true) {
		if(document.form1.cust_time1_start.value.length==0) {
			alert("평일 상담시간을 선택하세요.");
			document.form1.cust_time1_start.focus();
			return;
		}
		if(document.form1.cust_time1_end.value.length==0) {
			alert("평일 상담시간을 선택하세요.");
			document.form1.cust_time1_end.focus();
			return;
		}
	}
	if(document.form1.cust_time2_type[1].checked==true) {
		if(document.form1.cust_time2_start.value.length==0) {
			alert("토요일 상담시간을 선택하세요.");
			document.form1.cust_time2_start.focus();
			return;
		}
		if(document.form1.cust_time2_end.value.length==0) {
			alert("토요일 상담시간을 선택하세요.");
			document.form1.cust_time2_end.focus();
			return;
		}
	}
	if(document.form1.cust_time3_type[1].checked==true) {
		if(document.form1.cust_time3_start.value.length==0) {
			alert("일.공휴일 상담시간을 선택하세요.");
			document.form1.cust_time3_start.focus();
			return;
		}
		if(document.form1.cust_time3_end.value.length==0) {
			alert("일.공휴일 상담시간을 선택하세요.");
			document.form1.cust_time3_end.focus();
			return;
		}
	}
	if(confirm("고객센터 정보 설정을 하시겠습니까?")) {
		document.form1.mode.value="update";
		document.form1.target="processFrame";
		document.form1.submit();
	}
}

function change_timetype(gbn,val) {
	if(val=="0") {
		document.form1["cust_time"+gbn+"_start"].disabled=true;
		document.form1["cust_time"+gbn+"_end"].disabled=true;
	} else if(val=="1") {
		document.form1["cust_time"+gbn+"_start"].disabled=false;
		document.form1["cust_time"+gbn+"_end"].disabled=false;
	}
}
</script>
<table border=0 cellpadding=0 cellspacing=0 width=100% height="100%" style="table-layout:fixed">
<col width=190></col>
<col width=20></col>
<col width=></col>
<col width=20></col>
<tr>
	<td width=190 valign=top nowrap background="images/minishop_leftbg.gif"><? include ("menu.php"); ?></td>
	<td width=20 nowrap></td>
	<td valign=top style="padding-top:20px">

	<table width="100%"  border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td>
		<table width="100%"  border="0" cellpadding="0" cellspacing="0" >
		<tr>
			<td>
				<table border=0 cellpadding=0 cellspacing=0 width=100% >
				<tr>
					<td><img src="images/cust_info_title.gif"></td>
				</tr>
				<tr>
					<td height=5 background="images/minishop_titlebg.gif">
				</tr>
				</table>
			</td>
		</tr>
		<tr><td height=10></td></tr>
		<tr>
			<td>
				<table border=0 cellpadding=0 cellspacing=0 width=100% >
				<tr>
					<td colspan=3 >


						<table cellpadding="10" cellspacing="1" width="100%" bgcolor="#EFEFF2">
							<tr>
								<td  bgcolor="#F5F5F9" style="padding:20px">
									<table border=0 cellpadding=0 cellspacing=0 width=100%>
										<tr>
											<td class="notice_gray" height="20"><img src="images/icon_dot02.gif" border=0 hspace="4">고객센터 기본정보 입력 메뉴이며, 입력한 정보는 미니샵 왼쪽하단에 노출됩니다.</td>
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

			<!-- 처리할 본문 위치 시작 -->
			<tr><td height=40></td></tr>
			<tr>
				<td >






				<table border=0 cellpadding=0 cellspacing=0 width=100%>

				<form name=form1 method=post action="<?=$_SERVER[PHP_SELF]?>">
				<input type=hidden name=mode>

				<tr>
					<td><img src="images/cust_info_stitle01.gif" border=0 align=absmiddle alt="문의전화/이메일"><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="notice_blue">(고객센터 정보 설정을 하시면 미니샵의 왼쪽하단 <B>[고객센터]</B> 정보가 설정됩니다)</font></td>
				</tr>
				<tr><td height=5></td></tr>
				<tr><td height=1 bgcolor=#cccccc></td></tr>
				</table>

				<table border=0 cellpadding=7 cellspacing=1 bgcolor=#dddddd width=100%>
				<col width=140></col>
				<col width=></col>
				<tr>
					<td bgcolor=#f0f0f0 style="padding-left:10"><B>문의 전화번호</B></td>
					<td bgcolor=#ffffff style="padding-left:10">
					<input type=text class=input  name="cust_tel1" value="<?=$cust_tel[0]?>" size=5 maxlength=3 onkeyup="strnumkeyup(this)"> - <input type=text class=input  name="cust_tel2" value="<?=$cust_tel[1]?>" size=5 maxlength=4 onkeyup="strnumkeyup(this)"> - <input type=text class=input  name="cust_tel3" value="<?=$cust_tel[2]?>" size=5 maxlength=4 onkeyup="strnumkeyup(this)"> * 고객센터 전화번호를 입력하세요
					</td>
				</tr>
				<tr>
					<td bgcolor=#f0f0f0 style="padding-left:10"><B>문의 팩스번호</B></td>
					<td bgcolor=#ffffff style="padding-left:10">
					<input type=text class=input  name="cust_fax1" value="<?=$cust_fax[0]?>" size=5 maxlength=3 onkeyup="strnumkeyup(this)"> - <input type=text class=input  name="cust_fax2" value="<?=$cust_fax[1]?>" size=5 maxlength=4 onkeyup="strnumkeyup(this)"> - <input type=text class=input  name="cust_fax3" value="<?=$cust_fax[2]?>" size=5 maxlength=4 onkeyup="strnumkeyup(this)"> * 고객센터 팩스번호를 입력하세요
					</td>
				</tr>
				<tr>
					<td bgcolor=#f0f0f0 style="padding-left:10"><B>문의 이메일</B></td>
					<td bgcolor=#ffffff style="padding-left:10">
					<input type=text class=input  name=cust_email value="<?=$cust_email?>" size=35> * 고객센터 이메일을 입력하세요
					</td>
				</tr>
				<tr>
					<td bgcolor=#f0f0f0 style="padding-left:10"><B>상담시간 [평일]</B></td>
					<td bgcolor=#ffffff style="padding-left:10">
					<input type=radio name=cust_time1_type value=0 <?if($cust_time1_type==0)echo "checked";?> onclick="change_timetype('1',this.value)">휴무
					&nbsp;
					<input type=radio name=cust_time1_type value=1 <?if($cust_time1_type==1)echo "checked";?> onclick="change_timetype('1',this.value)">영업
					&nbsp;
					<select name=cust_time1_start <?if($cust_time1_type==0)echo "disabled";?>>
						<option value="">선택하세요</option>
						<?
						for($i=0;$i<=23;$i++) {
							$time1=substr("0".$i,-2);
							$tmpsel="";
							$tmpsel="";
							if($time1==$cust_time1[0]) $tmpsel="selected";
							echo "<option value=\"".$time1."\" ".$tmpsel.">".$time1."시 부터</option>\n";
						}
						?>
					</select>
					~
					<select name=cust_time1_end <?if($cust_time1_type==0)echo "disabled";?>>
						<option value="">선택하세요</option>
						<?
						for($i=0;$i<=23;$i++) {
							$time1=substr("0".$i,-2);
							$tmpsel="";
							if($time1==$cust_time1[1]) $tmpsel="selected";
							echo "<option value=\"".$time1."\" ".$tmpsel.">".$time1."시 까지</option>\n";
						}
						?>
					</select>
					</td>
				</tr>
				<tr>
					<td bgcolor=#f0f0f0 style="padding-left:10"><B>상담시간 [토요일]</B></td>
					<td bgcolor=#ffffff style="padding-left:10">
					<input type=radio name=cust_time2_type value=0 <?if($cust_time2_type==0)echo "checked";?> onclick="change_timetype('2',this.value)">휴무
					&nbsp;
					<input type=radio name=cust_time2_type value=1 <?if($cust_time2_type==1)echo "checked";?> onclick="change_timetype('2',this.value)">영업
					&nbsp;
					<select name=cust_time2_start <?if($cust_time2_type==0)echo "disabled";?>>
						<option value="">선택하세요</option>
						<?
						for($i=0;$i<=23;$i++) {
							$time1=substr("0".$i,-2);
							$tmpsel="";
							if($time1==$cust_time2[0]) $tmpsel="selected";
							echo "<option value=\"".$time1."\" ".$tmpsel.">".$time1."시 부터</option>\n";
						}
						?>
					</select>
					~
					<select name=cust_time2_end <?if($cust_time2_type==0)echo "disabled";?>>
						<option value="">선택하세요</option>
						<?
						for($i=0;$i<=23;$i++) {
							$time1=substr("0".$i,-2);
							$tmpsel="";
							if($time1==$cust_time2[1]) $tmpsel="selected";
							echo "<option value=\"".$time1."\" ".$tmpsel.">".$time1."시 까지</option>\n";
						}
						?>
					</select>
					</td>
				</tr>
				<tr>
					<td bgcolor=#f0f0f0 style="padding-left:10"><B>상담시간 [일ㆍ공휴일]</B></td>
					<td bgcolor=#ffffff style="padding-left:10">
					<input type=radio name=cust_time3_type value=0 <?if($cust_time3_type==0)echo "checked";?> onclick="change_timetype('3',this.value)">휴무
					&nbsp;
					<input type=radio name=cust_time3_type value=1 <?if($cust_time3_type==1)echo "checked";?> onclick="change_timetype('3',this.value)">영업
					&nbsp;
					<select name=cust_time3_start <?if($cust_time3_type==0)echo "disabled";?>>
						<option value="">선택하세요</option>
						<?
						for($i=0;$i<=23;$i++) {
							$time1=substr("0".$i,-2);
							$tmpsel="";
							if($time1==$cust_time3[0]) $tmpsel="selected";
							echo "<option value=\"".$time1."\" ".$tmpsel.">".$time1."시 부터</option>\n";
						}
						?>
					</select>
					~
					<select name=cust_time3_end <?if($cust_time3_type==0)echo "disabled";?>>
						<option value="">선택하세요</option>
						<?
						for($i=0;$i<=23;$i++) {
							$time1=substr("0".$i,-2);
							$tmpsel="";
							if($time1==$cust_time3[1]) $tmpsel="selected";
							echo "<option value=\"".$time1."\" ".$tmpsel.">".$time1."시 까지</option>\n";
						}
						?>
					</select>
					</td>
				</tr>
				</table>
				
				<table border=0 cellpadding=0 cellspacing=0 width=100%>
				<tr><td height=20></td></tr>
				<tr>
					<td align=center>
					<A HREF="javascript:CheckForm()"><img src="images/btn_save01.gif" border=0></A>
					</td>
				</tr>

				</form>

				</table>

				<iframe name="processFrame" src="about:blank" width="0" height="0" scrolling=no frameborder=no></iframe>

				</td>
			</tr>
			<!-- 처리할 본문 위치 끝 -->

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