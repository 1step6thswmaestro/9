<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
INCLUDE ("access.php");

$regdate = $_shopdata->regdate;

$mode=$_POST["mode"];
$code=$_POST["code"];
$prcode=$_POST["prcode"];
$date_year=$_POST["date_year"];
$date_month=$_POST["date_month"];
$age1=0;//($_POST["age1"]);
$age2=0;//$_POST["age2"];
$loc=$_POST["loc"];
$sex=$_POST["sex"];
$member=$_POST["member"];
$paymethod=$_POST["paymethod"];

if(strlen($date_year)==0) $date_year=date("Y");
if(strlen($date_month)==0) $date_month=date("m");

?>

<? INCLUDE "header.php"; ?>
<script type="text/javascript" src="lib.js.php"></script>
<script>var LH = new LH_create();</script>
<script for=window event=onload>LH.exec();</script>
<script>LH.add("parent_resizeIframe('AddFrame')");</script>

<SCRIPT LANGUAGE="JavaScript">
<!--
function CheckForm() {
	if(parent.selcode.length!=12 || parent.selcode=="000000000000") {
		alert("상품카테고리를 선택하세요.");
		return;
	}
	/*
	if(!IsNumeric(document.form1.age1.value) || !IsNumeric(document.form1.age2.value)) {
		alert("연령 입력은 숫자만 입력하셔야 합니다.");
		return;
	}
	age1=0;
	age2=0;
	if(document.form1.age1.value.length>0 && document.form1.age2.value.length>0) {
		age1=document.form1.age1.value;
		age2=document.form1.age2.value;
		if(age1==0 || age2==0 || age1>age2) {
			age1=0;
			age2=0;
		}
	}
	if((age1>0 || document.form1.sex.value!="ALL") && document.form1.member.value!="Y") {
		document.form1.member.options[1].selected=true;
	}
	*/
	document.form1.code.value=parent.selcode;
	document.form1.prcode.value=parent.prcode;
	//document.form1.age1.value=age1;
	//document.form1.age2.value=age2;
	document.form1.submit();
}
//-->
</SCRIPT>
<table cellpadding="0" cellspacing="0" width="100%">
	<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post>
	<input type=hidden name=mode value="search">
	<input type=hidden name=code>
	<input type=hidden name=prcode>
	<tr>
		<td>
			<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/distribute_01.gif" WIDTH=7 HEIGHT=7 ALT=""></TD>
					<TD COLSPAN=2 background="images/distribute_02.gif"></TD>
					<TD><IMG SRC="images/distribute_03.gif" WIDTH=8 HEIGHT=7 ALT=""></TD>
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
			<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="100%" bgcolor="#ededed" style="padding:4pt;">
						<table cellpadding="0" cellspacing="0" width="100%" bgcolor="white">
							<tr>
								<td width="100%">
									<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
										<TR>
											<TD class="table_cell" width="138"><img src="images/icon_point2.gif" width="8" height="11" border="0">기간 선택</TD>
											<TD class="td_con1" width="191" colspan="3"><select name=date_year class="select" style="width:70px;">
											<?
														for($i=substr($regdate,0,4);$i<=date("Y");$i++) {
															echo "<option value=\"".$i."\" ";
															if($i==$date_year) echo "selected";
															echo ">".$i."</option>\n";
														}
											?>
											</select>년 <select name=date_month class="select" style="width:70px;">
												<option value="ALL" <?if($date_month=="ALL")echo"selected";?>>전체</option>
											<?
														for($i=1;$i<=12;$i++) {
															$ii=substr("0".$i,-2);
															echo "<option value=\"".$ii."\" ";
															if($i==$date_month) echo "selected";
															echo ">".$ii."</option>\n";
														}
											?>
											</select>월</TD>
										</TR>
										<TR>
											<TD colspan="4" width="760" background="images/table_con_line.gif"><img src="images/table_con_line.gif" width="4" height="1" border="0"></TD>
										</TR>
										<TR>
											<TD class="table_cell" width="138"><img src="images/icon_point2.gif" width="8" height="11" border="0">지역별</TD>
											<TD class="td_con1" width="191"><select name=loc class="select" style="width:70px;">
												<option value="ALL" <?if($loc=="ALL")echo"selected";?>>전체</option>
											<?
														$loclist=array("서울","부산","대구","인천","광주","대전","울산","강원","경기","경남","경북","충남","충북","전남","전북","제주","기타");
														for($i=0;$i<count($loclist);$i++) {
															echo "<option value=\"".$loclist[$i]."\" ";
															if($loc==$loclist[$i]) echo "selected";
															echo ">".$loclist[$i]."</option>\n";
														}
											?>
											</select></TD>
											<TD class="table_cell1" width="126"><img src="images/icon_point2.gif" width="8" height="11" border="0">성별</TD>
											<TD class="td_con1" width="256"><select name=sex class="select" style="width:70px;">
												<option value="ALL" <?if($sex=="ALL")echo"selected";?>>전체</option>
												<option value="M" <?if($sex=="M")echo"selected";?>>남자</option>
												<option value="F" <?if($sex=="F")echo"selected";?>>여자</option>
											</select></TD>
										</TR>
										<TR>
											<TD colspan="4" width="760" background="images/table_con_line.gif"><img src="images/table_con_line.gif" width="4" height="1" border="0"></TD>
										</TR>
										<TR>
											<TD class="table_cell" width="138"><img src="images/icon_point2.gif" width="8" height="11" border="0">회원구분</TD>
											<TD class="td_con1" width="191"><select name=member class="select" style="width:70px;">
												<option value="ALL" <?if($member=="ALL")echo"selected";?>>전체</option>
												<option value="Y" <?if($member=="Y")echo"selected";?>>회원</option>
												<option value="N" <?if($member=="N")echo"selected";?>>비회원</option>
											</select></TD>
											<TD class="table_cell1" width="126"><img src="images/icon_point2.gif" width="8" height="11" border="0">결제방법</TD>
											<TD class="td_con1" width="256"><select name=paymethod style="WIDTH: 95%" class="select">
												<option value="ALL" <?if($paymethod=="ALL")echo"selected";?>>전체</option>
												<option value="B" <?if($paymethod=="B")echo"selected";?>>무통장</option>
												<option value="V" <?if($paymethod=="V")echo"selected";?>>실시간계좌이체</option>
												<option value="O" <?if($paymethod=="O")echo"selected";?>>가상계좌</option>
												<option value="C" <?if($paymethod=="C")echo"selected";?>>신용카드</option>
												<!--option value="P" <?if($paymethod=="P")echo"selected";?>>매매보호 신용카드</option-->
												<option value="M" <?if($paymethod=="M")echo"selected";?>>휴대폰</option>
												<option value="Q" <?if($paymethod=="Q")echo"selected";?>>매매보호 가상계좌</option>
											</select></TD>
										</TR>
									</TABLE>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td align="center" height=6></td>
	</tr>
	<tr>
		<td align="center"><p><a href="javascript:CheckForm();"><img src="images/botteon_search.gif" width="113" height="38" border="0"></a></p></td>
	</tr>
	</form>
	<tr>
		<td align="center"><p>&nbsp;</p></td>
	</tr>
	<tr>
		<td align="center">
<?
		if($mode=="search") {
			$codeA = substr($code,0,3);
			$codeB = substr($code,3,3);
			$codeC = substr($code,6,3);
			$codeD = substr($code,9,3);
			$likecode=$codeA;
			if($codeB!="000") {
				$likecode.=$codeB;
				if($codeC!="000") {
					$likecode.=$codeC;
					if($codeD!="000") {
						$likecode.=$codeD;
					}
				}
			}
			unset($codeA);unset($codeB);unset($codeC);unset($codeD);

			if($date_month=="ALL") {
				include "order_eachsale.year.php";
			} else {
				include "order_eachsale.month.php";
			}
		}
?>
		</td>
	</tr>
</table>
</body>
</html>