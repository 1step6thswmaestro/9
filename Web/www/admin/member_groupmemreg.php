<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
INCLUDE ("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "me-2";
$MenuCode = "member";
if (!$_usersession->isAllowedTask($PageCode)) {
	INCLUDE ("AccessDeny.inc.php");
	exit;
}
#########################################################

$regdate=$_shopdata->joindate;
if(strlen($regdate)==0) $regdate="20070401010101";

//리스트 세팅
$setup[page_num] = 10;
$setup[list_num] = 20;

$block=$_REQUEST["block"];
$gotopage=$_REQUEST["gotopage"];
$sort=$_POST["sort"];

if ($block != "") {
	$nowblock = $block;
	$curpage  = $block * $setup[page_num] + $gotopage;
} else {
	$nowblock = 0;
}

if (($gotopage == "") || ($gotopage == 0)) {
	$gotopage = 1;
}


$type=$_POST["type"];
$mode=$_POST["mode"];
$ids=$_POST["ids"];
$group_code=$_POST["group_code"];
$searchtype=$_POST["searchtype"];
$sex=$_POST["sex"];
$age=$_POST["age"];
$agemin=$_POST["agemin"];
$agemax=$_POST["agemax"];
$reserve=$_POST["reserve"];
$reservemin=$_POST["reservemin"];
$reservemax=$_POST["reservemax"];
$memregdate=$_POST["memregdate"];
$memregyear1=$_POST["memregyear1"];
$memregmonth1=$_POST["memregmonth1"];
$memregday1=$_POST["memregday1"];
$memregyear2=$_POST["memregyear2"];
$memregmonth2=$_POST["memregmonth2"];
$memregday2=$_POST["memregday2"];
$birth=$_POST["birth"];
$birthmonth=$_POST["birthmonth"];
$birthday=$_POST["birthday"];
$addr=$_POST["addr"];
$seladdr=$_POST["seladdr"];
$groupmember=$_POST["groupmember"];
$buydate=$_POST["buydate"];
$buyyear1=$_POST["buyyear1"];
$buymonth1=$_POST["buymonth1"];
$buyday1=$_POST["buyday1"];
$buyyear2=$_POST["buyyear2"];
$buymonth2=$_POST["buymonth2"];
$buyday2=$_POST["buyday2"];
$price=$_POST["price"];
$pricemin=$_POST["pricemin"];
$pricemax=$_POST["pricemax"];
$ordercnt=$_POST["ordercnt"];
$ordercntmin=$_POST["ordercntmin"];
$ordercntmax=$_POST["ordercntmax"];
$search=$_POST["search"];

$today=date("Ymd");
if(strlen($memregmonth1)>0){
	if (strlen($memregmonth1) != 2) $memregmonth1 = "0".$memregmonth1;
	if (strlen($memregday1) != 2) $memregday1 = "0".$memregday1;
	if (strlen($memregmonth2) != 2) $memregmonth2 = "0".$memregmonth2;
	if (strlen($memregday2) != 2) $memregday2 = "0".$memregday2;
}

// 가입자별
$memregdateDisabled = "disabled";
if($memregdate!="ALL") {
	$termday = (mktime(0,0,0,$memregmonth2,$memregday2,$memregyear2)-mktime(0,0,0,$memregmonth1,$memregday1,$memregyear1))/86400;
	if( strlen($_POST["type"]) > 0 ) {
		$memregdate = "SELDATE";
		$memregdateDisabled = "";
	}
} else {
	$termday=0;
}

if ($termday>92) {
	echo "<script>alert('가입일자별 조회시 3개월을 초과할 수 없습니다.\\n날짜를 재조정하신 후 다시 시도하세요.');history.go(-1);</script>";
	exit;
}


// 생년월일별
$birthDisabled = "disabled";
if( $birth != "ALL" AND strlen($_POST["type"]) > 0 ) {
	$birthDisabled = "";
	$birth ='SELDATE';
}



// 지역별
$addrDisabled = "disabled";
if( $addr != "NO" AND strlen($_POST["type"]) > 0 ) {
	$addrDisabled = "";
	$addr ='SELDATE';
}


// 구매내역 - 구매날짜별
if( $buydate != "ALL" AND strlen($_POST["type"]) > 0 ) {
	$buydate ='SELDATE';
}

// 구매내역 - 구매금액별
if( $price != "ALL" AND strlen($_POST["type"]) > 0 ) {
	$price ='SELDATE';
}

// 구매내역 - 구매건수별
if( $ordercnt != "ALL" AND strlen($_POST["type"]) > 0 ) {
	$ordercnt ='SELDATE';
}



if($mode=="insert") {
	$ids=substr($ids,0,-1);
	$inid=eregi_replace("\|","'",$ids);
	$sql = "UPDATE tblmember SET group_code = '".$group_code."' WHERE id IN (".$inid.") ";
	mysql_query($sql,get_db_conn());
	$onload="<script>alert('해당 회원의 등급이 변경 되었습니다.');</script>";
}
$max=10;
$len=21;

if (empty($searchtype)) $searchtype="M";
if (empty($page)) $page="1";
?>
<? INCLUDE "header.php"; ?>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">

function CheckForm() {
	/*
	if(document.form1.group_code.value.length==0){
		alert('회원등급을 먼저 선택하셔야 검색이 가능합니다.');
		document.form1.group_code.focus();
		return;
	}*/
	if(document.form1.searchtype[0].checked==true){
		if(document.form1.sex[0].checked==false && document.form1.sex[1].checked==false && document.form1.sex[2].checked==false){
			alert('성별을 선택하세요. ');
			document.form1.sex[0].focus();
			return;
		}
		if(document.form1.age.checked==false && document.form1.agemin.value.length==0 && document.form1.agemax.value.length==0){
			alert('나이를 선택하세요. ');
			document.form1.age.focus();
			return;
		}
		if((document.form1.agemin.value.length!=0 && isNaN(document.form1.agemin.value)) || (document.form1.agemax.value.length!=0 && isNaN(document.form1.agemax.value))){
			alert('나이는 숫자만 입력 가능 합니다.');
			document.form1.agemin.focus();
			return;
		}
		if(document.form1.reserve.checked==false && document.form1.reservemin.value.length==0 && document.form1.reservemax.value.length==0){
			alert('적립금 선택을 하세요. ');
			document.form1.reserve.focus();
			return;
		}
		if((document.form1.reservemin.value.length!=0 && isNaN(document.form1.reservemin.value)) || (document.form1.reservemax.value.length!=0 && isNaN(document.form1.reservemax.value))){
			alert('적립금은 숫자만 입력 가능합니다.');
			document.form1.reservemin.focus();
			return;
		}
	}else if(document.form1.searchtype[1].checked==true){
		if(document.form1.price.checked==false && document.form1.pricemin.value.length==0 && document.form1.pricemax.value.length==0){
			alert('구매금액을 선택하세요. ');
			document.form1.price.focus();
			return;
		}
		if((document.form1.pricemin.value.length!=0 && isNaN(document.form1.pricemin.value)) || (document.form1.pricemax.value.length!=0 && isNaN(document.form1.pricemax.value))){
			alert('구매금액은 숫자만 입력 가능합니다.');
			document.form1.pricemin.focus();
			return;
		}
		if(document.form1.ordercnt.checked==false && document.form1.ordercntmin.value.length==0 && document.form1.ordercntmax.value.length==0){
			alert('구매건수를 선택하세요. ');
			document.form1.ordercnt.focus();
			return;
		}
		if((document.form1.ordercntmin.value.length!=0 && isNaN(document.form1.ordercntmin.value)) || (document.form1.ordercntmax.value.length!=0 && isNaN(document.form1.ordercntmax.value))){
			alert('구매건수는 숫자만 입력 가능합니다.');
			document.form1.ordercntmin.focus();
			return;
		}
	}else if(document.form1.searchtype[2].checked==true){
		if(document.form1.search.value.length<2){
			alert('특정회원 검색어는 2자 이상 입력하셔야 합니다. ');
			document.form1.search.focus();
			return;
		}
	}
	document.form1.type.value="search";
	document.form1.mode.value="";
	document.form1.block.value=document.form3.block.value;
	document.form1.gotopage.value=document.form3.gotopage.value;
	document.form1.submit();
}

function ChangeSex(no){
	for(i=0;i<3;i++){
		if(no==i) document.form1.sex[i].checked=true;
		else document.form1.sex[i].checked=false;
	}
}

function ChangeGroup(no){
	for(i=0;i<3;i++){
		if(no==i) document.form1.groupmember[i].checked=true;
		else document.form1.groupmember[i].checked=false;
	}
}

function ChangeCheck(no){
	if(no==1) document.form1.age.checked=false;
	else if(no==2) document.form1.reserve.checked=false;
	else if(no==3) document.form1.price.checked=false;
	else if(no==4) document.form1.ordercnt.checked=false;
	else if(no==5) document.form1.buydate.checked=false;
}

var shop="layer1";
var ArrLayer = new Array ("layer1","layer2","layer3");
function ViewLayer(gbn){
	if(document.all){
		for(i=0;i<ArrLayer.length;i++) {
			if (ArrLayer[i] == gbn)
				document.all[ArrLayer[i]].style.display="";
			else
				document.all[ArrLayer[i]].style.display="none";
		}
	} else if(document.getElementById){
		for(i=0;i<2;i++) {
			if (ArrLayer[i] == gbn)
				document.getElementByld[ArrLayer[i]].style.display="";
			else
				document.getElementByld[ArrLayer[i]].style.display="none";
		}
	} else if(document.layers){
		for(i=0;i<2;i++) {
			if (ArrLayer[i] == gbn)
				document.layers[ArrLayer[i]].display="";
			else
				document.layers[ArrLayer[i]].display="none";
		}
	}
	shop=gbn;
}

function GoSort(sort){
	document.form1.type.value="search";
	document.form1.mode.value="";
	document.form1.sort.value=sort;
	document.form1.submit();
}

function GoPage(block,gotopage) {
	document.form1.type.value="search";
	document.form1.mode.value="";
	document.form1.block.value = block;
	document.form1.gotopage.value = gotopage;
	document.form1.submit();
}

function GoGroupCode( code ){
	<?
	// 그룹 정보
	echo "var groupInfo = new Array();\n";
	echo "groupInfoArray = new Array();\n";
	$grouptitle = array();
	$Gsql = "SELECT * FROM tblmembergroup ";
	$Gresult = mysql_query($Gsql,get_db_conn());
	$i=0;

	echo "groupInfo[".$i."] = new Array(2);\n";
	echo "groupInfo[".$i."][0] = '';\n";
	echo "groupInfo[".$i."][1] = '';\n";
	echo "groupInfo[".$i."][2] = '';\n";
	echo "groupInfo[".$i."][3] = '0';\n";
	$i++;

	while($Grow=mysql_fetch_object($Gresult)) {

		$Msql = "SELECT COUNT(*) as cnt FROM tblmember WHERE group_code = '".$Grow->group_code."'";
		$Mresult=mysql_query($Msql,get_db_conn());
		$Mrow=mysql_fetch_object($Mresult);

		$grouptitle[$Grow->group_code]['code']=$Grow->group_code;
		$grouptitle[$Grow->group_code]['name']=$Grow->group_name;

		echo "groupInfo[".$i."] = new Array(2);\n";
		echo "groupInfo[".$i."][0] = '".$Grow->group_code."';\n";
		echo "groupInfo[".$i."][1] = '".$Grow->group_name."';\n";
		echo "groupInfo[".$i."][2] = '".$Grow->group_description."';\n";
		echo "groupInfo[".$i."][3] = '".$Mrow->cnt."';\n";

		echo "groupInfoArray[".$i."] = '".$Grow->group_code."';\n";

		$i++;

	}
	mysql_free_result($Gresult);
	?>
	var key = '0';
	var temp = groupInfoArray.indexOf(code);
	if( temp > 0 ) key = temp;
	groupInfoCNT.innerHTML = groupInfo[key][3];
	groupInfoMemo.innerHTML = groupInfo[key][2];
}

function CheckAll(cnt){
	chkval=document.form1.allcheck.checked;
	for(i=1;i<=cnt;i++){
		document.form1.chkid[i].checked=chkval;
	}
}

function InsertGroup(cnt){
	chkval=false;

	if(document.form1.group_code.value.length==0){
		alert('회원등급을 먼저 선택하셔야 검색이 가능합니다.');
		document.form1.group_code.focus();
		return;
	}

	document.form1.ids.value="";

	for(i=1;i<=cnt;i++){
		if(document.form1.chkid[i].checked==true){
			chkval=true;
			document.form1.ids.value+="|"+document.form1.chkid[i].value+"|,";
		}
	}
	if(chkval==false){
		alert('변경할 회원을 선택하세요');
		document.form1.chkid[1].focus();
		return;
	}
	document.form1.type.value="search";
	document.form1.mode.value="insert";
	document.form1.submit();
}

function ReserveInfo(id) {
	window.open("about:blank","reserve_info","height=400,width=400,scrollbars=yes");
	document.form2.id.value=id;
	document.form2.submit();
}

function UserMemoView(obj,type) {
	try {
		obj.style.visibility = type;
	} catch (e) {}
}

</script>

<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
	<tr>
		<td valign="top">
			<table cellpadding="0" cellspacing="0" width=100% style="table-layout:fixed">
				<tr>
					<td>
						<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed"  background="images/con_bg.gif">
							<col width=198>

									</col>

							<col width=10>

									</col>

							<col width=>

									</col>

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
														<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 회원관리 &gt; 회원등급설정 &gt; <span class="2depth_select">등급별 회원변경/관리</span></td>
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
													<tr>
														<td height="8"></td>
													</tr>
													<tr>
														<td>
															<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
																<TR>
																	<TD><IMG SRC="images/member_groupmemreg_title.gif" ALT=""></TD>
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
																	<TD width="100%" class="notice_blue">회원검색을 통해 회원특성에 맞는 등급으로 변경 관리할 수 있습니다.</TD>
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
													<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post>
														<input type=hidden name=type>
														<input type=hidden name=mode>
														<input type=hidden name=block>
														<input type=hidden name=gotopage>
														<input type=hidden name=sort value="<?=$sort?>">
														<input type=hidden name=ids>
														<tr>
															<td height="10"></td>
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
																								<TD colspan="4" background="images/table_con_line.gif"></TD>
																							</TR>
																							<TR>
																								<TD height="35" align=center background="images/blueline_bg.gif"><b><font color="#555555">회원검색하기</font></b></TD>
																								<td></td>
																								<td></td>
																								<td></td>
																							</TR>
																							<TR>
																								<TD>
																									<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
																										<col width=138>

																												</col>

																										<col width=>

																												</col>

																										<col width=108>

																												</col>

																										<col width=206>

																												</col>

																										<TR>
																											<TD colspan="2" background="images/table_con_line.gif"></TD>
																										</TR>
																										<TR>
																											<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">검색기준</TD>
																											<TD class="td_con1">
																												<input type=radio id="idx_searchtype1" name=searchtype value="M" style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;" <?if($searchtype=="M") echo "checked";?> onclick="ViewLayer('layer1')">
																												<label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_searchtype1>회원 속성</label>
																												&nbsp;&nbsp;
																												<input type=radio id="idx_searchtype2" name=searchtype value="O" style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;" <?if($searchtype=="O") echo "checked";?> onclick="ViewLayer('layer2')">
																												<label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_searchtype2>구매 내역</label>
																												&nbsp;&nbsp;
																												<input type=radio id="idx_searchtype3" name=searchtype value="U" style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;" <?if($searchtype=="U") echo "checked";?> onclick="ViewLayer('layer3')">
																												<label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_searchtype3>특정 회원</label>
																											</TD>
																										</TR>
																										<TR>
																											<TD colspan="4" background="images/table_con_line.gif"></TD>
																										</TR>
																									</table>
																									<div id=layer1 style="margin-left:0;display:hide; display:<?=($searchtype=="M"?"block":"none")?> ;border-style:solid; border-width:0; border-color:black;background:#FFFFFF;padding:0;">
																										<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
																											<col width=138></col>
																											<col width=></col>
																											<col width=108></col>
																											<col width=206></col>
																											<TR>
																												<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">연령별</TD>
																												<TD class="td_con1">
																													<input type=checkbox id="idx_age1" name=age value="ALL" style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;" <? if(empty($agemin) && empty($agemax)) echo "checked"?>>
																													<label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_age1>전체</label>
																													<input type=text name=agemin value="<?=$agemin?>" size=3 maxlength=3 onfocus="ChangeCheck(1)" class="input">
																													세부터
																													<input type=text name=agemax value="<?=$agemax?>" size=3 maxlength=3 onfocus="ChangeCheck(1)" class="input">
																													세까지</TD>
																											</TR>
																											<TR>
																												<TD colspan="4" background="images/table_con_line.gif"></TD>
																											</TR>
																											<TR>
																												<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">성별</TD>
																												<TD class="td_con1">
																													<input type=checkbox id="idx_sex1" name=sex value="ALL" style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;" onclick="ChangeSex(0)" <? if($sex !="M" && $sex !='F' ) echo "checked"?>>
																													<label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_sex1>전체</label>
																													<input type=checkbox id="idx_sex2" name=sex value="M" style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;" onclick="ChangeSex(1)" <?if($sex=="M") echo "checked"?>>
																													<label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_sex2>남자</label>
																													<input type=checkbox id="idx_sex3" name=sex value="F" style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;" onclick="ChangeSex(2)" <?if($sex=="F") echo "checked"?>>
																													<label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_sex3>여자</label>
																												</TD>
																											</TR>
																											<TR>
																												<TD colspan="4" background="images/table_con_line.gif"></TD>
																											</TR>
																											<TR>
																												<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">적립금액별</TD>
																												<TD class="td_con1">
																													<input type=checkbox id="idx_reserve1" name=reserve value="ALL" style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;" <? if(empty($reservemin) && empty($reservemax)) echo "checked"?>>
																													<label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_reserve1>전체</label>
																													<input type=text name=reservemin value="<?=$reservemin?>" size=8 maxlength=8 onfocus="ChangeCheck(2)" class="input">
																													원부터
																													<input type=text name=reservemax value="<?=$reservemax?>" size=8 maxlength=8 onfocus="ChangeCheck(2)" class="input">
																													원까지</TD>
																											</TR>
																											<TR>
																												<TD colspan="4" background="images/table_con_line.gif"></TD>
																											</TR>
																											<TR>
																												<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">가입일자별</TD>
																												<TD class="td_con1">
																													<input type=checkbox id="idx_memregdate1" name=memregdate value="ALL" style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;" <?if($memregdate=="ALL" OR $memregdate == '') echo "checked"?> onclick="idx_memregdateSel.disabled=this.checked;">
																													<label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_memregdate1>전체</label>
																													<span id="idx_memregdateSel" <?=$memregdateDisabled?>>
																													<?
								if (empty($memregdate1)) $memregdate1 = $today;
								if (empty($memregdate2)) $memregdate2 = $today;
								// 초기값 설정 :오늘날짜
								if(empty($memregyear1))	$memregyear1 = substr($memregdate1,0,4);
								if(empty($memregmonth1))	$memregmonth1 = substr($memregdate1,4,2);
								if(empty($memregday1))		$memregday1 = substr($memregdate1,6,2);

								if(empty($memregyear2))	$memregyear2 = substr($memregdate2,0,4);
								if(empty($memregmonth2))	$memregmonth2 = substr($memregdate2,4,2);
								if(empty($memregday2))		$memregday2 = substr($memregdate2,6,2);

								echo "<select size=1 name=memregyear1 class=\"select\">\n";
								for ($i = substr($regdate,0,4);$i <=substr($today,0,4) ; $i++) {
									if($i == $memregyear1)  echo "<option selected value=\"$i\">$i</option>\n";
									else echo "<option value=\"$i\">$i</option>\n";
								}
								echo "</select>년";
								echo "<select size=1 name=memregmonth1 class=\"select\">\n";
								for ($i = 1;$i <= 12; $i++) {
									if($i == $memregmonth1)  echo "<option selected value=\"$i\">$i</option>\n";
									else echo "<option value=\"$i\">$i</option>\n";
								}
								echo "</select>월";
								echo "<select size=1 name=memregday1 class=\"select\">\n";
								for ($i = 1;$i <= 31; $i++) {
									if ($i == $memregday1)  echo "<option selected value=\"$i\">$i</option>\n";
									else echo "<option value=\"$i\">$i</option>\n";
								}
								echo "</select>일 ~ ";

								echo "<select size=1 name=memregyear2 class=\"select\">\n";
								for ($i = substr($regdate,0,4);$i <= substr($today,0,4); $i++) {
									if ($i == $memregyear2)  echo "<option selected value=\"$i\">$i</option>\n";
									else echo "<option value=\"$i\">$i</option>\n";
								}
								echo "</select>년";
								echo "<select size=1 name=memregmonth2 class=\"select\">\n";
								for ($i = 1;$i <= 12; $i++) {
									if ($i == $memregmonth2)  echo "<option selected value=\"$i\">$i</option>\n";
									else echo "<option value=\"$i\">$i</option>\n";
								}
								echo "</select>월";
								echo "<select size=1 name=memregday2 class=\"select\">\n";

								for ($i = 1;$i <= 31; $i++) {
									if($i == $memregday2)   echo "<option selected value=\"$i\">$i</option>\n";
									else  echo "<option value=\"$i\">$i</option>\n";
								}
								echo "</select>일";
?>
																													</span>
																												</TD>
																											</TR>
																											<TR>
																												<TD colspan="4" background="images/table_con_line.gif"></TD>
																											</TR>
																											<TR>
																												<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">생년월일별</TD>
																												<TD class="td_con1">
																													<input type=checkbox id="idx_birth1" name=birth value="ALL" style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;" <?if($birth=="ALL" OR $birth=="") echo "checked"?> onclick="idx_birthSel.disabled=this.checked;">
																													<label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_birth1>전체</label>
																													<span id="idx_birthSel" <?=$birthDisabled?>>
																													<?
								if(strlen($birthmonth)==0) $birthmonth = date("m");
								echo "<select size=1 name=birthmonth class=\"select\">\n";
								for ($i = 1;$i <= 12; $i++) {
									if ($i<10) $i2 = "0$i";
									else $i2=$i;
									if($i2 == $birthmonth)  echo "<option selected value=\"$i2\">$i</option>\n";
									else echo "<option value=\"$i2\">$i</option>\n";
								}
								echo "</select>월";
								if(strlen($birthday)==0) $birthday = date("d");
								echo "<select size=1 name=birthday class=\"select\">\n";
								echo "<option value=\"ALL\"";
								if($birthday=="ALL") echo " selected";
								echo ">전체";
								for ($i = 1;$i <= 31; $i++) {
									if ($i<10) $i2 = "0$i";
									else $i2=$i;
									if ($i2 == $birthday)  echo "<option selected value=\"$i2\">$i</option>\n";
									else echo "<option value=\"$i2\">$i</option>\n";
								}
								echo "</select>일";
?>
																													</span>
																												</TD>
																											</TR>
																											<TR>
																												<TD colspan="4" background="images/table_con_line.gif"></TD>
																											</TR>
																											<TR>
																												<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">지역별</TD>
																												<TD class="td_con1">
																													<input type=checkbox id="idx_addr1" name=addr value="NO" style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;" <?if($addr=="NO" OR $addr=="") echo "checked"?> onclick="idx_addrSel.disabled=this.checked;">
																													<label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_addr1>전체</label>
																													<span id="idx_addrSel" <?=$addrDisabled?>>
																													<select name=seladdr class="select">
																														<?
								$area= array ("서울","인천","부산","대전","광주","대구","울산","경기","강원","충북","충남","경북","경남","전북","전남","제주");
								$arnum = count($area);
								for($i=0;$i<$arnum;$i++){
									echo "<option value=\"".$area[$i]."\"";
									if($seladdr==$area[$i]) echo " selected";
									echo ">".$area[$i];
								}
?>
																													</select>
																													<span class="font_orange">*해당 검색은 집주소만 검색합니다.</span>
																													</span>
																												</TD>
																											</TR>
																											<TR>
																												<TD colspan="4" background="images/table_con_line.gif"></TD>
																											</TR>
																											<tr>
																												<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">기변경 회원</TD>
																												<TD class="td_con1" colspan="3">
																													<input type=checkbox id="idx_groupmember1" name=groupmember value="NO" style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;" <? if($groupmember !="YES" && $groupmember !="ONE") echo "checked"?> onclick="ChangeGroup(0)">
																													<label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_groupmember1>전체</label>
																													&nbsp;&nbsp;
																													<input type=checkbox id="idx_groupmember2" name=groupmember value="YES" style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;" <?if($groupmember=="YES") echo "checked"?> onclick="ChangeGroup(1)">
																													<label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_groupmember2>전체 기변경 회원 제외</label>
																													&nbsp;&nbsp;
																													<input type=checkbox id="idx_groupmember3" name=groupmember value="ONE" style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;" <?if($groupmember=="ONE") echo "checked"?> onclick="ChangeGroup(2)">
																													<label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_groupmember3>선택 등급 회원 제외</label>
																												</TD>
																											</tr>
																										</table>
																									</div>
																									<div id=layer2 style="margin-left:0;display:hide; display:<?=($searchtype=="O"?"block":"none")?> ;border-style:solid; border-width:0; border-color:black;background:#FFFFFF;padding:0;">
																										<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
																											<col width=138>

																													</col>

																											<col width=>

																													</col>

																											<col width=108>

																													</col>

																											<col width=206>

																													</col>

																											<tr>
																												<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">구매날짜별</TD>
																												<TD class="td_con1" colspan="3">
																													<input type=checkbox id="idx_buydate1" name=buydate value="ALL" style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;" <?if($buydate=="ALL" OR $buydate=="") echo "checked"?>>
																													<label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_buydate1>전체</label>
																													<select name=buyyear1 onchange="ChangeCheck(5)" class="select">
																														<?
								if(strlen($buyyear1)==0) $temp = date("Y");
								else $temp=$buyyear1;
								for ($i=substr($regdate,0,4);$i<=date("Y");$i++) {
									if ($i==$temp)
										echo "<option value=\"$i\" selected>$i\n";
									else
										echo "<option value=\"$i\">$i\n";
								}
?>
																													</select>
																													년
																													<select name=buymonth1 onchange="ChangeCheck(5)" class="select">
																														<?
								if(strlen($buymonth1)==0) $curmonth = date("m");
								else $curmonth=$buymonth1;
								for ($i=1;$i<=12;$i++) {
									if ($i<10) $i2 = "0$i";
									else $i2 = $i;
									if ($i2 == $curmonth) echo "<option value=\"$i2\" selected>$i2\n";
									else echo "<option value=\"$i2\">$i2\n";
								}
?>
																													</select>
																													월
																													<select name=buyday1 onchange="ChangeCheck(5)" class="select">
																														<?
								if(strlen($buyday1)==0) $curday = date("d");
								else $curday=$buyday1;
								for ($i=1;$i<=31;$i++) {
									if ($i<10) $i2 = "0$i";
									else $i2 = $i;
									if ($i2 == $curday) echo "<option value=\"$i2\" selected>$i2\n";
									else echo "<option value=\"$i2\">$i2\n";
								}
?>
																													</select>
																													일 ~
																													<select name=buyyear2 onchange="ChangeCheck(5)" class="select">
																														<?
								if(strlen($buyyear2)==0) $temp = date("Y");
								else $temp=$buyyear2;
								for ($i=substr($regdate,0,4);$i<=date("Y");$i++) {
									if ($i==$temp)
										echo "<option value=\"$i\" selected>$i\n";
									else
										echo "<option value=\"$i\">$i\n";
								}
?>
																													</select>
																													년
																													<select name=buymonth2 onchange="ChangeCheck(5)" class="select">
																														<?
								if(strlen($buymonth2)==0) $curmonth = date("m");
								else $curmonth=$buymonth2;
								for ($i=1;$i<=12;$i++) {
									if ($i<10) $i2 = "0$i";
									else $i2 = $i;
									if ($i2 == $curmonth) echo "<option value=\"$i2\" selected>$i2\n";
									else echo "<option value=\"$i2\">$i2\n";
								}
?>
																													</select>
																													월
																													<select name=buyday2 onchange="ChangeCheck(5)" class="select">
																														<?
								if(strlen($buyday2)==0) $curday = date("d");
								else $curday=$buyday2;
								for ($i=1;$i<=31;$i++) {
									if ($i<10) $i2 = "0$i";
									else $i2 = $i;
									if ($i2 == $curday) echo "<option value=\"$i2\" selected>$i2\n";
									else echo "<option value=\"$i2\">$i2\n";
								}
?>
																													</select>
																													일 </TD>
																											</tr>
																											<TR>
																												<TD colspan="4" background="images/table_con_line.gif"></TD>
																											</TR>
																											<tr>
																												<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">구매금액별</TD>
																												<TD class="td_con1" colspan="3">
																													<input type=checkbox id="idx_price1" name=price value="ALL" style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;" <?if($price=="ALL" OR $price=="") echo "checked"?>>
																													<label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_price1>전체</label>
																													<input type=text name=pricemin value="<?=$pricemin?>" size=8 maxlength=8 onclick="ChangeCheck(3)" class="input">
																													원 부터
																													<input type=text name=pricemax value="<?=$pricemax?>" size=8 maxlength=8 onclick="ChangeCheck(3)" class="input">
																													원 까지</TD>
																											</tr>
																											<TR>
																												<TD colspan="4" background="images/table_con_line.gif"></TD>
																											</TR>
																											<TR>
																												<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">구매건수별</TD>
																												<TD class="td_con1" colspan="3">
																													<input type=checkbox id="idx_ordercnt1" name=ordercnt value="ALL" style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;" <?if($ordercnt=="ALL" OR $ordercnt=="") echo "checked"?>>
																													<label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_ordercnt1>전체</label>
																													<input type=text name=ordercntmin value="<?=$ordercntmin?>" size=8 maxlength=8 onclick="ChangeCheck(4)" class="input">
																													건 부터 &nbsp;
																													<input type=text name=ordercntmax value="<?=$ordercntmax?>" size=8 maxlength=8 onclick="ChangeCheck(4)" class="input">
																													건 까지</TD>
																											</TR>
																										</table>
																									</div>
																									<div id=layer3 style="margin-left:0;display:hide; display:<?=($searchtype=="U"?"block":"none")?> ;border-style:solid; border-width:0; border-color:black;background:#FFFFFF;padding:0;">
																										<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
																											<col width=138>

																													</col>

																											<col width=>

																													</col>

																											<col width=108>

																													</col>

																											<col width=206>

																													</col>

																											<TR>
																												<TD class="table_cell" style="padding-bottom:10pt;"><img src="images/icon_point2.gif" width="8" height="11" border="0">특정회원 검색</TD>
																												<TD class="td_con1" colspan="3" style="padding-bottom:10pt;">
																													<input type=text name=search value="<?=$search?>" class="input">
																													&nbsp;<span class="font_orange">*특정회원의 이름, 아이디, 연락처, 이메일을 검색합니다!</span></TD>
																											</TR>
																										</TABLE>
																									</div>
																								</TD>
																								<td></td>
																								<td></td>
																								<td></td>
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
															<td height=5></td>
														</tr>
														<tr>
															<td>
																<p align="center"><a href="javascript:CheckForm();"><img src="images/botteon_search1.gif" width="145" height="38" border="0" vspace="3"></a>
															</td>
														</tr>
														<tr>
															<td height="20"></td>
														</tr>
														<tr>
															<td>
																<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
																	<TR>
																		<TD><IMG SRC="images/member_groupmem_stitle1.gif" WIDTH="192" HEIGHT=31 ALT=""></TD>
																		<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif">&nbsp;</TD>
																		<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></TD>
																	</TR>
																</TABLE>
															</td>
														</tr>
														<tr>
															<td>
																<p align="right">&nbsp;<FONT color=red><B>정렬방법 :</B></FONT> 적립금 <A href="javascript:GoSort('reserve_desc');"><B>▲</B></A> <A href="javascript:GoSort('reserve_asc');"><B>▼</B></A> &nbsp;|&nbsp; 이름 <A href="javascript:GoSort('name_asc');"><B>▲</B></A> <A href="javascript:GoSort('name_desc');"><B>▼</B></A>
															</td>
														</tr>
														<tr>
															<td>
																<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
																	<TR>
																		<TD background="images/table_top_line.gif" colspan="10" height=1></TD>
																	</TR>
																	<input type=hidden name=chkid>
																	<TR align=center>
																		<TD class="table_cell">선택</TD>
																		<TD class="table_cell1">아이디</TD>
																		<TD class="table_cell1">메모</TD>
																		<TD class="table_cell1">등급명</TD>
																		<TD class="table_cell1">지역</TD>
																		<TD class="table_cell1">성별</TD>
																		<TD class="table_cell1">나이</TD>
																		<TD class="table_cell1">구매금액</TD>
																		<TD class="table_cell1">구매건수</TD>
																		<TD class="table_cell1">적립금</TD>
																	</TR>
																	<TR>
																		<TD colspan="10" background="images/table_con_line.gif"></TD>
																	</TR>
																	<?
				if ($type=="search") {
					if ($searchtype=="M" || $searchtype=="U") {
						$qry.= "WHERE member_out = 'N' ";
						if ($searchtype=="M" && $groupmember=="YES")
							$qry.= "AND group_code = '' ";
						else if ($searchtype=="M" && $groupmember=="ONE")
							$qry.= "AND (group_code = '' OR group_code != '".$group_code."') ";
						if ($searchtype=="U") {
							$qry.= "
								AND
									(
										name LIKE '%".$search."%'
										OR
										id LIKE '%".$search."%'
										OR
										mobile LIKE '%".$search."%'
										OR
										home_tel LIKE '%".$search."%'
										OR
										email LIKE '%".$search."%'
									)
							";
						} else if ($searchtype=="M") {
							//if($sex!="ALL" && $sex=="M") $qry.= "AND  MID(resno,7,1)%2=1 ";
							//else if($sex!="ALL" && $sex=="F") $qry.= "AND  MID(resno,7,1)%2=0 ";
							if($sex!="ALL" && $sex=="M") $qry.= "AND gender='1' ";
							else if($sex!="ALL" && $sex=="F") $qry.= "AND gender='2' ";
							if ($age!="ALL"){
								$start_year = (int)date("Y") - (int)$agemax +1;
								$end_year = (int)date("Y") - (int)$agemin +1;
								$s_year = substr((string)$start_year,2,2);
								$e_year = substr((string)$end_year,2,2);
								if ($start_year < 2000 && $end_year < 2000) {
									$qry.= "AND (LEFT(resno,2) BETWEEN '".$s_year."' AND '".$e_year."') ";
									$qry.= "AND MID(resno,7,1) < '3' ";
								} else if ($start_year < 2000 && $end_year > 1999) {
									$qry.= "AND (((LEFT(resno,2) BETWEEN '".$s_year."' AND '99') ";
									$qry.= "AND MID(resno,7,1) < '3') OR ((LEFT(resno,2) BETWEEN '00' AND '".$e_year."') ";
									$qry.= "AND MID(resno,7,1) > '2')) ";
								} else if ($start_year > 1999 && $end_year > 1999) {
									$qry.= "AND (LEFT(resno,2) BETWEEN '".$s_year."' AND '".$e_year."') ";
									$qry.= "AND MID(resno,7,1) > '2') ";
								}
							}
							if($birth!="ALL"){
								if($birthday!="ALL") $qry.= "AND MID(resno,3,4) = '".$birthmonth.$birthday."' ";
								else $qry.= "AND MID(resno,3,2) = '".$birthmonth."' ";
							}
							if($memregdate!="ALL"){
								$memregdate1 = substr($memregyear1,0,4).substr($memregmonth1,0,2).substr($memregday1,0,2)."000000";
								$memregdate2 = substr($memregyear2,0,4).substr($memregmonth2,0,2).substr($memregday2,0,2)."999999";
								$qry.= "AND date >= '".$memregdate1."' AND date <= '".$memregdate2."' ";
							}
							if($reserve!="ALL"){
								if(strlen($reservemin)!=0) $reserveminvalue=$reservemin;
								else $reserveminvalue=0;
								if(strlen($reservemax)!=0) $reservemaxvalue=$reservemax;
								else $reservemaxvalue=10000000000;
								$qry.= "AND reserve >= '".$reserveminvalue."' AND reserve <= '".$reservemaxvalue."' ";
							}
							if($addr!="NO"){
								$qry.= "AND home_addr LIKE '".$seladdr."%' ";
							}
						}

						$sql = "SELECT COUNT(*) as t_count FROM tblmember ";
						$sql.= $qry;
						$result = mysql_query($sql,get_db_conn());
						$row = mysql_fetch_object($result);
						$t_count = $row->t_count;
						mysql_free_result($result);
						$pagecount = (($t_count - 1) / $setup[list_num]) + 1;

						$sql = "SELECT id, reserve, name, MID(resno,1,2) as age, MID(resno,7,1) as sex, ";
						$sql.= "group_code, MID(home_addr,1,4) as addr, memo, gender FROM tblmember ";
						$sql.= $qry." ";
						if($sort=="reserve_desc") $sql.= "ORDER BY reserve DESC ";
						else if($sort=="reserve_asc") $sql.= "ORDER BY reserve ";
						else if($sort=="name_asc") $sql.= "ORDER BY name ";
						else $sql.= "ORDER BY name DESC ";
						$sql.= "LIMIT " . ($setup[list_num] * ($gotopage - 1)) . ", " . $setup[list_num];

						$result=mysql_query($sql,get_db_conn());
						$count=0;
						while($row=mysql_fetch_object($result)){
							$row->id=strtolower($row->id);
							$arcount[$count]=$row->id;
							$arreserve[$row->id]=$row->reserve;
							$arname[$row->id]=$row->name;
							$arsex[$row->id]=$row->gender;
							$arage[$row->id]=$row->age;
							if(!$row->group_code) $artrue[$row->id]="Y";
							else $artrue[$row->id]="N";
							$groupcode[$row->id]=$row->group_code;
							$address[$row->id]=$row->addr;
							$memo[$row->id]=$row->memo;
							$mulid.="'$row->id',";
							$count++;
						}

						$maxcnt=$count;
						if($maxcnt>20) $count--;
						$mulid=substr($mulid,0,-1);
						mysql_free_result($result);
						if($count!=0){
							$sql = "SELECT COUNT(price) as cnt, SUM(price) as totalprice, id FROM tblorderinfo ";
							$sql.= "WHERE deli_gbn = 'Y' AND id IN (".$mulid.") GROUP BY id";
							$result=mysql_query($sql,get_db_conn());
							while($row=mysql_fetch_object($result)){
								$row->id=strtolower($row->id);
								$arprice[$row->id]=$row->totalprice;
								$arcnt[$row->id]=$row->cnt;
							}
							mysql_free_result($result);
						}
					} else if ($searchtype=="O") {
						
						$sql = "SELECT COUNT(price) as cnt, SUM(price) as totalprice, id FROM tblorderinfo ";
						$sql.= "WHERE 1=1 ";
						if($buydate!="ALL") {
							$sql.= "AND (ordercode > '".$buyyear1.$buymonth1.$buyday1."000000' ";
							$sql.= "AND ordercode < '".$buyyear2.$buymonth2.$buyday2."999999') ";
						}
						$sql.= "AND deli_gbn = 'Y' AND (MID(ordercode,15,2) != 'X' && MID(ordercode,15,1) != '|') ";
						$sql.= "GROUP BY id ";
						if($price!="ALL" || $ordercnt!="ALL") $sql.= "HAVING ";
						if($price!="ALL"){
							if(strlen($pricemin)!=0) $priceminvalue=$pricemin;
							else $priceminvalue=0;
							if(strlen($pricemax)!=0) $pricemaxvalue=$pricemax;
							else $pricemaxvalue=10000000000;
							$sql.= "totalprice >= '".$priceminvalue."' AND totalprice <= '".$pricemaxvalue."' ";
						}
						if($price!="ALL" && $ordercnt!="ALL") $sql.= "AND ";
						if($ordercnt!="ALL"){
							if(strlen($ordercntmin)!=0) $ordercntminvalue=$ordercntmin;
							else $ordercntminvalue=0;
							if(strlen($ordercntmax)!=0) $ordercntmaxvalue=$ordercntmax;
							else $ordercntmaxvalue=10000000000;
							$sql.= "cnt >= '".$ordercntminvalue."' AND cnt <= '".$ordercntmaxvalue."' ";
						}
						$result = mysql_query($sql,get_db_conn());
						$t_count = mysql_num_rows($result);
						mysql_free_result($result);
						$pagecount = (($t_count - 1) / $setup[list_num]) + 1;
						if($sort=="topcnt") $sql.= "ORDER BY cnt DESC ";
						else if($sort=="bottomcnt") $sql.= "ORDER BY cnt ";
						else if($sort=="bottomprice") $sql.= "ORDER BY totalprice ";
						else $sql.= "ORDER BY totalprice DESC ";
						$sql.= "LIMIT " . ($setup[list_num] * ($gotopage - 1)) . ", " . $setup[list_num];
						$result=mysql_query($sql,get_db_conn());
						while($row=mysql_fetch_object($result)){
							$row->id=strtolower($row->id);
							$arcount[$count]=$row->id;
							$arprice[$row->id]=$row->totalprice;
							$arcnt[$row->id]=$row->cnt;
							$mulid.="'$row->id',";
							$count++;
						}				$maxcnt=$count;
						if($maxcnt>20) $count--;
						$mulid=substr($mulid,0,-1);
						mysql_free_result($result);
						if ($count!=0) {
							$sql = "SELECT id, MID(resno,1,2) as age, MID(resno,7,1) as sex, reserve, name, ";
							$sql.= "group_code, MID(home_addr,1,4) as addr, memo, gender FROM tblmember ";
							$sql.= "WHERE id IN (".$mulid.") ";
							$result=mysql_query($sql,get_db_conn());
							while($row=mysql_fetch_object($result)){
								$row->id=strtolower($row->id);
								if(!$row->group_code) $artrue[$row->id]="Y";
								else $artrue[$row->id]="N";
								$arreserve[$row->id]=$row->reserve;
								$arname[$row->id]=$row->name;
								$arsex[$row->id]=$row->gender;
								$arage[$row->id]=$row->age;
								$groupcode[$row->id]=$row->group_code;
								$address[$row->id]=$row->addr;
								$memo[$row->id]=$row->memo;
							}
							mysql_free_result($result);
						}
					}
					$lineage=100+date("y");
					$totalcheck=0;
					// 회원 리스트 출력
					for($i=0;$i<$count;$i++) {
						$_gender="";
						$_groupname="";
						switch($arsex[$arcount[$i]]){
							case "1":
								$_gender="남자";
							break;
							case "2":
								$_gender="여자";
							break;
							default:
								$_gender="-";
							break;
						}

						if(($searchtype!="O" && $groupcode[$arcount[$i]]=="") || ($searchtype=="O" && $artrue[$arcount[$i]]=="Y")) {
							$_groupname = $arname[$arcount[$i]];
						} else if($artrue[$arcount[$i]]=="N" || $groupcode[$arcount[$i]]!="") {
							//echo "<font color=#AA0000 title='이름 : ".$arname[$arcount[$i]]."\n해당 등급명 : ".$grouptitle[$groupcode[$arcount[$i]]]['name']."'><u>".titleCut(15,$grouptitle[$groupcode[$arcount[$i]]]['name'])."</font>\n";
							$_groupname = (titleCut(15,$grouptitle[$groupcode[$arcount[$i]]]['name']) != "")?titleCut(15,$grouptitle[$groupcode[$arcount[$i]]]['name']):"-";
						} else {
							$_groupname = "삭제 회원";
						}
						$bgcolor="#FFFFFF";
						if($searchtype!="O" && strlen($arprice[$arcount[$i]])>0) {
							$bgcolor="#FEFAAB";
						}
						echo "<tr>\n";
						echo "	<TD align=center class=\"td_con2\">";
						if($artrue[$arcount[$i]]!=""){
							$totalcheck++;
							echo "<input type=checkbox name=chkid value=\"".$arcount[$i]."\" style=\"BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none\">";
						}
						echo "	</td>\n";
						echo "	<TD align=center class=\"td_con1\"><span class=\"font_orange\"><b>".$arcount[$i]."</b></span></TD>\n";
						echo "	<TD align=center class=\"td_con1\"><NOBR>";
						if (strlen(trim($memo[$arcount[$i]]))>0) {
							echo "<img src=\"images/btn_memo.gif\" width=\"35\" height=\"29\" border=\"0\" onMouseOver=\"UserMemoView(divmemo_".$i.",'visible')\" onMouseOut=\"UserMemoView(divmemo_".$i.",'hidden')\">";
						} else {
							echo "<img src=\"images/btn_memor.gif\" width=\"35\" height=\"29\" border=\"0\">";
						}
						echo "	<div id=\"divmemo_".$i."\" style=\"position:absolute; z-index:5; width:250; filter:revealTrans(duration=0.3); visibility:hidden;\">\n";
						echo "	<table border=0 cellspacing=0 cellpadding=1 bgcolor=#7F7F65>\n";
						echo "	<tr>\n";
						echo "		<td><font color=#ffffff>&nbsp;".$memo[$arcount[$i]]."&nbsp;</td>\n";
						echo "	</tr>\n";
						echo "	</table>\n";
						echo "	</div>\n";
						echo "	</td>\n";
						echo "	<TD align=center class=\"td_con1\">";
						echo $_groupname;
						echo "	</td>\n";
						echo "	<TD align=center class=\"td_con1\">".($address[$arcount[$i]])."&nbsp;</td>\n";
						echo "	<TD align=center class=\"td_con1\">";
						echo $_gender; 
						echo "</td>\n";
						echo "	<TD align=center class=\"td_con1\">".(strlen($arage[$arcount[$i]])==0?"&nbsp;":$lineage-$arage[$arcount[$i]])."</td>\n";
						echo "	<TD align=right class=\"td_con1\"><span class=\"font_orange\"><b>".number_format($arprice[$arcount[$i]])."원</b></span></td>\n";
						echo "	<TD align=right class=\"td_con1\">".number_format($arcnt[$arcount[$i]])."건</td>\n";
						echo "	<TD align=right class=\"td_con1\">".number_format($arreserve[$arcount[$i]])."원&nbsp;<a href=\"javascript:ReserveInfo('".$arcount[$i]."');\"><img src=\"images/btn_detail.gif\" width=\"35\" height=\"29\" border=\"0\" align=absMiddle></a></td>\n";
						echo "</tr>\n";
						echo "<tr>\n";
						echo "	<TD colspan=\"10\" background=\"images/table_con_line.gif\"></TD>\n";
						echo "</tr>\n";
					}
				}
				if($count==0) {
					echo "<tr><td class=\"td_con2\" colspan=10 align=center>검색된 회원이 없습니다.</td></tr>\n";
				}
?>
																	<TR>
																		<TD background="images/table_top_line.gif"colspan="10" height=1></TD>
																	</TR>
																</TABLE>
															</td>
														</tr>
														<?
			$total_block = intval($pagecount / $setup[page_num]);

			if (($pagecount % $setup[page_num]) > 0) {
				$total_block = $total_block + 1;
			}

			$total_block = $total_block - 1;

			if (ceil($t_count/$setup[list_num]) > 0) {
				// 이전	x개 출력하는 부분-시작
				$a_first_block = "";
				if ($nowblock > 0) {
					$a_first_block .= "<a href='javascript:GoPage(0,1);' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='첫 페이지';return true\"><IMG src=\"images/icon_first.gif\" border=0 align=\"absmiddle\"></a>&nbsp;&nbsp;";

					$prev_page_exists = true;
				}

				$a_prev_page = "";
				if ($nowblock > 0) {
					$a_prev_page .= "<a href='javascript:GoPage(".($nowblock-1).",".($setup[page_num]*($block-1)+$setup[page_num]).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='이전 ".$setup[page_num]." 페이지';return true\">[prev]</a>&nbsp;&nbsp;";

					$a_prev_page = $a_first_block.$a_prev_page;
				}

				// 일반 블럭에서의 페이지 표시부분-시작

				if (intval($total_block) <> intval($nowblock)) {
					$print_page = "";
					for ($gopage = 1; $gopage <= $setup[page_num]; $gopage++) {
						if ((intval($nowblock*$setup[page_num]) + $gopage) == intval($gotopage)) {
							$print_page .= "<span class=font_orange2><B>[".(intval($nowblock*$setup[page_num]) + $gopage)."]</B></span> ";
						} else {
							$print_page .= "<a href='javascript:GoPage(".$nowblock.",".(intval($nowblock*$setup[page_num]) + $gopage).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$setup[page_num]) + $gopage)."';return true\">[".(intval($nowblock*$setup[page_num]) + $gopage)."]</a> ";
						}
					}
				} else {
					if (($pagecount % $setup[page_num]) == 0) {
						$lastpage = $setup[page_num];
					} else {
						$lastpage = $pagecount % $setup[page_num];
					}

					for ($gopage = 1; $gopage <= $lastpage; $gopage++) {
						if (intval($nowblock*$setup[page_num]) + $gopage == intval($gotopage)) {
							$print_page .= "<span class=font_orange2><B>[".(intval($nowblock*$setup[page_num]) + $gopage)."]</B></span> ";
						} else {
							$print_page .= "<a href='javascript:GoPage(".$nowblock.",".(intval($nowblock*$setup[page_num]) + $gopage).");' onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$setup[page_num]) + $gopage)."';return true\">[".(intval($nowblock*$setup[page_num]) + $gopage)."]</a> ";
						}
					}
				}		// 마지막 블럭에서의 표시부분-끝


				$a_last_block = "";
				if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
					$last_block = ceil($t_count/($setup[list_num]*$setup[page_num])) - 1;
					$last_gotopage = ceil($t_count/$setup[list_num]);

					$a_last_block .= "&nbsp;&nbsp;<a href='javascript:GoPage(".$last_block.",".$last_gotopage.");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='마지막 페이지';return true\"><IMG src=\"images/icon_last.gif\" border=0 align=\"absmiddle\" width=\"17\" height=\"14\"></a>";

					$next_page_exists = true;
				}

				// 다음 10개 처리부분...

				$a_next_page = "";
				if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
					$a_next_page .= "&nbsp;&nbsp;<a href='javascript:GoPage(".($nowblock+1).",".($setup[page_num]*($nowblock+1)+1).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='다음 ".$setup[page_num]." 페이지';return true\">[next]</a>";

					$a_next_page = $a_next_page.$a_last_block;
				}
			} else {
				$print_page = "<B>[1]</B>";
			}
			echo "<tr>\n";
			echo "	<td height=\"24\">\n";
			echo "	<input type=checkbox name=allcheck value=\"".$totalcheck."\" style=\"BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;\" onclick=\"CheckAll('".$totalcheck."')\">&nbsp;<font color=#0054A6>page 전체 회원 선택</font>";
			echo "	</td>\n";
			echo "</tr>\n";
			echo "<tr>\n";
			echo "	<td height=\"10\"></td>\n";
			echo "</tr>\n";
			echo "<tr>\n";
			echo "	<td width=\"100%\" class=\"font_size\" align=center>\n";
			echo "		".$a_div_prev_page.$a_prev_page.$print_page.$a_next_page.$a_div_next_page;
			echo "	</td>\n";
			echo "</tr>\n";
?>
														<tr><td height="20"></td></tr>
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
																								<TD colspan="4" background="images/table_con_line.gif"></TD>
																							</TR>
																							<TR>
																								<TD height="35" align=center background="images/blueline_bg.gif"><b><font color="#555555">변경 등급 선택</font></b></TD>
																								<td></td>
																								<td></td>
																								<td></td>
																							</TR>
																							<TR>
																								<TD>
																									<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
																										<col width=138>

																												</col>

																										<col width=>

																												</col>

																										<col width=108>

																												</col>

																										<col width=206>

																												</col>

																										<TR>
																											<TD colspan="4" background="images/table_con_line.gif"></TD>
																										</TR>
																										<TR>
																											<TD class="table_cell" style="padding-top:10pt;"><img src="images/icon_point2.gif" width="8" height="11" border="0">회원등급 선택</TD>
																											<TD class="td_con1" style="padding-top:10pt;">
																												<select name=group_code onchange="GoGroupCode( this.value );" style="width:90%" class="select">
																													<option value="">해당 등급을 선택하세요.
																													<?
																														foreach($grouptitle as $gcode=>$gname){
																															$sel = ($group_code==$gcode) ? " selected" : "" ;
																															echo "<option value=\"".$gname['code']."\">".$gname['name']."</option>\n";
																														}
																													?>
																												</select>
																											</TD>
																											<TD class="table_cell1" style="padding-top:10pt;"><img src="images/icon_point2.gif" width="8" height="11" border="0">등급 회원수</TD>
																											<TD class="td_con1" style="padding-top:10pt;">
																												<span id="groupInfoCNT">0</span>명 </TD>
																										</TR>
																										<TR>
																											<TD colspan="4" background="images/table_con_line.gif"></TD>
																										</TR>
																										<TR>
																											<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">회원등급 설명</TD>
																											<TD class="td_con1" colspan="3">&nbsp;
																												<span id="groupInfoMemo"></span>
																											</TD>
																										</TR>
																									</TABLE>
																								</TD>
																								<td></td>
																								<td></td>
																								<td></td>
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
															<td>
																<p align="center"><a href="javascript:InsertGroup('<?=$totalcheck?>');"><img src="images/botteon_register.gif" border="0" vspace="3"></a>
															</td>
														</tr>
													</form>
													<form name=form2 action="member_reservelist.php" method=post target=reserve_info>
														<input type=hidden name=id>
														<input type=hidden name=type>
													</form>
													<form name=form3 method=post>
														<input type=hidden name=block value="<?=$block?>">
														<input type=hidden name=gotopage value="<?=$gotopage?>">
													</form>
													<tr>
														<td>&nbsp;</td>
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
																	<TD COLSPAN=3 width="100%" valign="top" class=menual_bg style="padding-top:8pt; padding-bottom:8pt; padding-left:4pt;">
																		<table cellpadding="0" cellspacing="0" width="100%">
																			<tr>
																				<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
																				<td >
																					<p><span class="font_dotline">회원등급 변경/검색 방법</span>
																				</td>
																			</tr>
																			<tr>
																				<td width="20" align="right">&nbsp;</td>
																				<td  class="space_top">
																					<p>- 이동될 등급을 선택 후 검색기준별로 검색조건들을 선택해야만 검색이 가능합니다.
																				</td>
																			</tr>
																			<tr>
																				<td width="20" align="right">&nbsp;</td>
																				<td  class="space_top">
																					<p>- 검색된 후 해당 회원들의 등급을 이동할 수 있습니다.
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
													<tr>
														<td height="50"></td>
													</tr>
												</table>
											</td>
											<td width="16" background="images/con_t_02_bg.gif"></td>
										</tr>
										<tr>
											<td width="16"><img src="images/con_t_04.gif" width="16" height="16" border="0"></td>
											<td background="images/con_t_04_bg.gif"></td>
											<td width="16"><img src="images/con_t_03.gif" width="16" height="16" border="0"></td>
										</tr>
										<tr>
											<td height="20"></td>
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
<? INCLUDE "copyright.php"; ?>