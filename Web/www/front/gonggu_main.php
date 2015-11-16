<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$sleftMn = "NO";

$code=$_REQUEST["code"];

$imagepath=$Dir.DataDir."shopimages/etc/socail_logo.gif";
$flashpath=$Dir.DataDir."shopimages/etc/social_logo.swf";

$socialintro="";

if (strlen($_data->social_intro)==0) {
	$socialintro = "";
} else {
	if (file_exists($imagepath)) {
		$mainimg="<img src=\"".$imagepath."\" border=0 align=absmiddle>";
	} else {
		$mainimg="";
	}
	if (file_exists($flashpath)) {
		if (strpos($_data->shop_intro,"[MAINFLASH_")!==false) {
			$mainstart=strpos($_data->shop_intro,"[MAINFLASH_");
			$mainend=strpos($_data->shop_intro,"]",$mainstart);
			$swfsize=substr($_data->shop_intro,$mainstart+11,$mainend-$mainstart-11);
			$size=explode("X",$swfsize);
			$width=$size[0];
			$height=$size[1];
		}
		$mainflash="<script>flash_show('".$flashpath."','".$width."','".$height."');</script>";
	} else {
		$mainflash="";
	}
	$pattern=array("(\[DIR\])","(\[MAINIMG\])","/\[MAINFLASH_([0-9]{1,4})X([0-9]{1,4})\]/");
	$replace=array($Dir,$mainimg,$mainflash);
	$socialintro=preg_replace($pattern,$replace,$_data->shop_intro);

	if (strpos(strtolower($socialintro),"table")!=false || strlen($mainflash)>0)
		$socialintro = $socialintro;
	else
		$socialintro = ereg_replace("\n","<br>",$socialintro);
} //shopintro [SHOPINTRO]


if(strlen($code)==0) {
	//소셜코드 가져오기(카테고리 출력)
	$sql = "SELECT * FROM tblproductcode WHERE type like 'S%' Order by codeA,codeB,codeC,codeD limit 1 ";
	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		$code = $row->codeA.$row->codeB.$row->codeC.$row->codeD;
	}else{
		echo "<html></head><body onload=\"location.href='".$Dir.MainDir."main.php'\"></body></html>";exit;
	}
	mysql_free_result($result);
}

$codeA=substr($code,0,3);
$codeB=substr($code,3,3);
$codeC=substr($code,6,3);
$codeD=substr($code,9,3);
if(strlen($codeA)!=3) $codeA="000";
if(strlen($codeB)!=3) $codeB="000";
if(strlen($codeC)!=3) $codeC="000";
if(strlen($codeD)!=3) $codeD="000";
$code=$codeA.$codeB.$codeC.$codeD;

$likecode=$codeA;
if($codeB!="000") $likecode.=$codeB;
if($codeC!="000") $likecode.=$codeC;
if($codeD!="000") $likecode.=$codeD;


$_cdata="";
$sql = "SELECT * FROM tblproductcode WHERE codeA='".$codeA."' AND codeB='".$codeB."' ";
$sql.= "AND codeC='".$codeC."' AND codeD='".$codeD."' ";
$result=mysql_query($sql,get_db_conn());
if($row=mysql_fetch_object($result)) {
	//접근가능권한그룹 체크
	if($row->group_code=="NO") {
		echo "<html></head><body onload=\"location.href='".$Dir.MainDir."main.php'\"></body></html>";exit;
	}
	if(strlen($_ShopInfo->getMemid())==0) {
		if(strlen($row->group_code)>0) {
			echo "<html></head><body onload=\"location.href='".$Dir.FrontDir."login.php?chUrl=".getUrl()."'\"></body></html>";exit;
		}
	} else {
		if($row->group_code!="ALL" && strlen($row->group_code)>0 && $row->group_code!=$_ShopInfo->getMemgroup()) {
			echo "<html></head><body onload=\"alert('해당 카테고리 접근권한이 없습니다.');location.href='".$Dir.MainDir."main.php'\"></body></html>";exit;
		}
	}
	$_cdata=$row;
} else {
	echo "<html></head><body onload=\"location.href='".$Dir.MainDir."main.php'\"></body></html>";exit;
}
mysql_free_result($result);

$imagepath=$Dir.DataDir."shopimages/product/";
$listnum=(int)$_REQUEST["listnum"];
if($listnum<=0) $listnum=$_data->prlist_num;

//리스트 세팅
$setup[page_num] = 10;
$setup[list_num] = $listnum;

$block=$_REQUEST["block"];
$gotopage=$_REQUEST["gotopage"];

if ($block != "") {
	$nowblock = $block;
	$curpage  = $block * $setup[page_num] + $gotopage;
} else {
	$nowblock = 0;
}

if (($gotopage == "") || ($gotopage == 0)) {
	$gotopage = 1;
}

$type=$_REQUEST["type"];
if(strlen($type)==0 || $type!="complete") $type="";

?>

<HTML>
<HEAD>
<TITLE><?=$_data->shoptitle?> - 진행중인 공동구매</TITLE>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META http-equiv="X-UA-Compatible" content="IE=5" />
<META name="description" content="<?=(strlen($_data->shopdescription)>0?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<?include($Dir."lib/style.php")?>
<SCRIPT LANGUAGE="JavaScript">
<!--
function ClipCopy(url) {
	var tmp;
	tmp = window.clipboardData.setData('Text', url);
	if(tmp) {
		alert('주소가 복사되었습니다.');
	}
}

function GoPage(block,gotopage) {
	document.form2.block.value=block;
	document.form2.gotopage.value=gotopage;
	document.form2.submit();
}
function ChangeType(val) {
	document.form2.block.value="";
	document.form2.gotopage.value="";
	document.form2.type.value=val;
	document.form2.submit();
}

function feedFunc(frm) {

    if(!frm.emailCheck.checked && !frm.smsCheck.checked) {
        alert('이메일과 핸드폰중 구독을 원하시는 항목을 선택하여주세요');
        return false;
    }

    if(frm.emailCheck.checked) {
        if(!frm.feedEmail.value) {
            alert('받으실 이메일을 입력해주시기 바랍니다.');
            frm.feedEmail.focus();
            return false;
        }
		if (!IsMailCheck(frm.feedEmail.value)) {
			alert('잘못된 이메일 입니다.');
			frm.feedEmail.focus();
			return false;
        }
    }

    if(frm.smsCheck.checked) {
        if(!frm.feedSms.value) {
            alert('받으실 핸드폰번호를 입력해주시기 바랍니다.');
            frm.feedSms.focus();
            return false;
        } else {

            if(frm.feedSms.value.length > 3) {
                head = frm.feedSms.value.substr(0,3);
                if(head != "010" && head != "011" && head != "016" && head != "017" && head != "018" && head != "019") {
                    alert('핸드폰구독은 010, 011, 016, 017, 018, 019만 서비스가 가능합니다.');
                    frm.feedSms.focus();
                    return false;
                }
            }
        }
    }

    if(!confirm('구독신청을 하시겠습니까?')) return false;

    return true;

}
//-->
</SCRIPT>
</HEAD>

<body<?=(substr($_data->layoutdata["MOUSEKEY"],0,1)=="Y"?" oncontextmenu=\"return false;\"":"")?><?=(substr($_data->layoutdata["MOUSEKEY"],1,1)=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=(substr($_data->layoutdata["MOUSEKEY"],2,1)=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>

<? include ($Dir.MainDir.$_data->menu_type.".php") ?>

<?//=$socialintro?>

<table align="center" cellpadding="0" cellspacing="0" width="100%">
    <tr>
        <td valign="top">
			<table align="center" cellpadding="0" cellspacing="0" width="100%" class="table_td">
				<tr>
					<td>
						<table cellpadding="0" cellspacing="0" width="100%">
							<tr>
								<td><div class="subpageTitle">공동구매</div><!--<IMG SRC="../images/design/gonggu_title01.gif" ALT=""><IMG SRC="../images/design/gonggu_title01_text.gif" ALT="">--></td>
								<td class="course" align="right"></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td height="5"></td>
				</tr>
				<tr>
					<td>
						<TABLE WIDTH=100% BORDER=0 CELLPADDING=0 CELLSPACING=0>
							<TR>
								<TD><a href="../front/gonggu_main.php"><IMG SRC="../images/design/gonggu_tap01r.gif" ALT="" border="0"></a></TD>
								<TD><a href="../front/gonggu_end.php"><IMG SRC="../images/design/gonggu_tap02.gif"  ALT="" border="0"></a></TD>
								<TD><a href="../front/gonggu_order.php"><IMG SRC="../images/design/gonggu_tap03.gif"  ALT="" border="0"></a></TD>
								<TD><a href="../front/gonggu_guide.php"><IMG SRC="../images/design/gonggu_tap04.gif"  ALT="" border="0"></a></TD>
								<TD width="100%" background="../images/design/gonggu_tap_bg.gif">&nbsp;</TD>
							</TR>
						</TABLE>
					</td>
				</tr>
				<tr><td height="20"></td></tr>
				<tr>
					<td>
						<table cellpadding="0" cellspacing="1" width="100%" bgcolor="#EDEDED">
							<tr>
								<td bgcolor="#F9F9F9" class="table_td" height="35">
									<form name="frmMailing" action="gonggu_mailing.php" method="post" target="feedFrmFrame" style="display:inline" onsubmit="return feedFunc(this)">
									<table cellpadding="0" cellspacing="0" width="90%" align="center">
										<tr>
											<td class="table_td"> 공동구매상품 소식 구독하기 : </td>
											<td class="table_td">이메일</td>
											<td><input type="checkbox" name="emailCheck" value='1' onclick="if(this.checked) this.form.feedEmail.disabled=false; else this.form.feedEmail.disabled=true;" ></td>
											<td><input type="text" name="feedEmail" maxlength="30" size="20" class="input" disabled style="width:190px"></td>
											<td class="table_td">휴대폰</td>
											<td><input type="checkbox" name="smsCheck" value='1' onclick="if(this.checked) this.form.feedSms.disabled=false; else this.form.feedSms.disabled=true;" ></td>
											<td><input type="text" name="feedSms" maxlength="30" size="20" class="input" disabled style="width:190px"></td>
											<td width="62"><input type='image' SRC="../images/design/gongguing_btn01.gif" WIDTH=62 HEIGHT=26 ALT="" ></td>
										</tr>
									</table>
									</form>
									<iframe name='feedFrmFrame' src='about:blank' width=0 height=0 style='display:none'></iframe>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr><td height="10"></td></tr>
				<tr>
					<td>
						<TABLE cellSpacing=0 cellPadding=0 width="100%">
							<TR>
								<TD style="PADDING:1px;" bgColor=#EDEDED width="100%">
									<TABLE cellSpacing=0 cellPadding=0 width="100%" bgColor=white>
										<TR>
											<TD>
												<?if($_data->ETCTYPE["CODEYES"]!="N") {?>
												<?
													// 1차 카테고리 출력
													$sql = "SELECT * FROM tblproductcode WHERE type like 'S%' AND codeB='000' AND codeC='000' AND codeD='000' Order by codeA ";
													$result=mysql_query($sql,get_db_conn());
													while($row=mysql_fetch_object($result)) {
														if($codeA == $row->codeA){
															$codeA_list .= "<span style=\"line-height:24px; font-weight:bold; color:black; padding-right:30px;\">".$row->code_name."</span>";
														}else{
															$codeA_list .= "<span style=\"line-height:24px; font-weight:normal; padding-right:30px;\"><a href=\"".$Dir.FrontDir."gonggu_main.php?code=".$row->codeA."\">".$row->code_name."</a></span>";
														}
													}
													mysql_free_result($result);
												?>
												<TABLE border=0 cellSpacing=0 cellPadding=0 width="100%">
													<!--
													<TR>
														<TD style="padding:0px 15px; border-bottom:1px solid #ededed;" height=30 bgcolor="#F8F8F8"><B><?//=$codeA_list?></B></TD>
													</tr>
													-->
													<tr>
														<TD style="PADDING:0px 5pt;">
															<table cellpadding="0" cellspacing="0" width="100%">
																<tr>
																	<td style="padding:4px 10px;">
																	<?
																		//2차카테고리출력
																		if($_cdata->type!="SX") {
																	?>
																	<?
																			$sql = "SELECT codeA,codeB,codeC,codeD,code_name,type FROM tblproductcode ";
																			$sql.= "WHERE codeA='".$codeA."' AND codeB!='000' AND codeC='000' AND codeD='000' AND group_code!='NO' ";
																			$sql.= "AND (type='SM' || type='SMX')";
																			$sql.= "ORDER BY sequence DESC ";
																			$result=mysql_query($sql,get_db_conn());
																			$i =0;
																			while($row=mysql_fetch_object($result)) {
																				if($i>0) echo " | ";
																				echo "<a href=\"".$Dir.FrontDir."gonggu_main.php?code=".$row->codeA.$row->codeB.$row->codeC.$row->codeD."\"><FONT class=upcodename>".(($codeB ==$row->codeB)?"<b>".$row->code_name."</b>":$row->code_name)."</font></a>";
																				$i++;
																			}
																			mysql_free_result($result);
																	?>
																	</td>
																</tr>
																<tr>
																	<td style="padding:10px;">

																	<?
																			$category_list="";
																			if($_cdata->type!="SMX" || ($_cdata->type=="SMX" && $codeC !="000")) {
																				$sql = "SELECT codeA,codeB,codeC,codeD,code_name,type FROM tblproductcode ";
																				$sql.= "WHERE codeA='".$codeA."' AND codeB='".$codeB."' AND codeC!='000' AND codeD='000' AND group_code!='NO' ";
																				$sql.= "AND (type='SM' || type='SMX')";
																				$sql.= "ORDER BY sequence DESC ";
																				$result=mysql_query($sql,get_db_conn());

																				$i =0;
																				while($row=mysql_fetch_object($result)) {
																					$category_list .="			<a href=\"".$Dir.FrontDir."gonggu_main.php?code=".$row->codeA.$row->codeB.$row->codeC.$row->codeD."\"><FONT class=upcodename>".(($codeC ==$row->codeC)?"<b>".$row->code_name."</b>":$row->code_name)."</font></a>,\n";
																					if(!eregi("X",$row->type)) {
																						$sql = "SELECT codeA,codeB,codeC,codeD,code_name,type FROM tblproductcode ";
																						$sql.= "WHERE codeA='".$row->codeA."' AND codeB='".$row->codeB."' AND codeC='".$row->codeC."' AND codeD!='000' AND group_code!='NO' ";
																						$sql.= "AND (type='SM' || type='SMX') ";
																						$sql.= "ORDER BY sequence DESC ";
																						$result2=mysql_query($sql,get_db_conn());
																						$j=0;
																						while($row2=mysql_fetch_object($result2)) {
																							if($j == 0) $category_list .="			<img src=\"../img/productlist_t_icon.gif\" style=\"vertical-align:middle;\">";
																							if($j>0) $category_list.="&nbsp;&nbsp; | &nbsp;&nbsp;";
																							$category_list.="<a href=\"".$Dir.FrontDir."gonggu_main.php?code=".$row2->codeA.$row2->codeB.$row2->codeC.$row2->codeD."\"><FONT class=subcodename>".(($codeC ==$row2->codeC && $codeD ==$row2->codeD)?"<b>".$row2->code_name."</b>":$row2->code_name)."</font></a>";
																							$j++;
																						}
																						mysql_free_result($result2);
																						if($j >0) $category_list .="			\n";
																					}
																					$i++;
																				}
																			}
																			echo $category_list;
																		}
																	}
																	?>

																	</td>
																</tr>
															</table>
														</TD>
													</TR>
												</TABLE>
											</TD>
										</TR>
									</TABLE>
								</TD>
							</TR>
						</TABLE>
					</td>
				</tr>
				<tr><td height="7"></td></tr>




<!-- list 시작 -->
<?
	$today=time();
	$sCondition = "AND P.productcode = S.pcode ";
	$sCondition .= "AND P.productcode LIKE '".$likecode."%' ";
	$sCondition.= "AND display='Y' ";
	if ($type=="complete")
		$sCondition.= "AND (sell_enddate < '".$today."' OR quantity = 0 )";
	else
		$sCondition.= "AND sell_startdate <= '".$today."' AND sell_enddate > '".$today."' AND (quantity is null OR quantity <> 0 ) ";

	$sql = "SELECT COUNT(*) as t_count FROM tblproduct P, tblproduct_social S WHERE 1=1 ".$sCondition;
	$result = mysql_query($sql,get_db_conn());
	$row = mysql_fetch_object($result);
	$t_count = $row->t_count;
	mysql_free_result($result);
	$pagecount = (($t_count - 1) / $setup[list_num]) + 1;

	$sql = "SELECT * FROM tblproduct P, tblproduct_social S ";
	$sql.= "WHERE 1=1 ".$sCondition;
	$sql.= "ORDER BY sell_enddate ASC ";
	$sql.= "LIMIT " . ($setup[list_num] * ($gotopage - 1)) . ", " . $setup[list_num];
	$result = mysql_query($sql,get_db_conn());

	$i=0;
	while($row=mysql_fetch_object($result)) {

		if (strlen($row->maximage)>0 && file_exists($imagepath.$row->maximage)==true){
			$maximage = "<img src='".$imagepath.$row->maximage."' alt=\"\" />";
		} else {
			$maximage = "<img src='".$Dir."images/no_img.gif' alt=\"\" />";
		}

		//구매자 수
		$sql = "SELECT IFNULL(sum(quantity), 0) as totcnt FROM tblorderproduct P, tblorderinfo O ";
		$sql.= "WHERE P.ordercode = O.ordercode ";
		$sql.= "AND O.deli_gbn IN ('S','Y','N','X') ";
		$sql.= "AND O.pay_admin_proc !='C' ";
		$sql.= "AND (del_gbn='N' OR del_gbn='A') ";
		$sql.= "AND P.productcode='".$row->productcode."' ";

		$result2=mysql_query($sql,get_db_conn());
		$row2=mysql_fetch_object($result2);
		$totcnt=$row2->totcnt;
		mysql_free_result($result2);
		switch($row->sellcount_type){
			case "B": $nowSailCnt = 0;break;
			case "C": $nowSailCnt = $row->sellcount_add;break;
			case "R": $nowSailCnt = intval($totcnt* $row->sellcount_add / 100);break;
			case "A":
				//if ($type!="complete") {
					//시간마다 +
					$limitTime = ($today > $row->sell_enddate)? $row->sell_enddate : $today ;
					$passTime = intval(($limitTime - $row->sell_startdate) / (60*60));
					$nowSailCnt = $row->sellcount_add * $passTime;
				//}
				break;
		}
		$nowSailCnt = $nowSailCnt + $totcnt;
		if($row->complete_quantity)
			$sellBar = intval($nowSailCnt * 100 / $row->complete_quantity);
		else
			$sellBar =1;

		if($type == "complete")
		{
			if($row->quantity == "0")
				$buyText = "<IMG SRC=\"../images/design/gonggu_soldout.gif\" ALT=\"\" border=0 id=\"buyImg_".$i."\">";//SOLD OUT
			else
				$buyText = "<IMG SRC=\"../images/design/gonggu_endbuy.gif\" ALT=\"\" border=0 id=\"buyImg_".$i."\">";//판매종료
			$strLeftTime = "000000";
		}else{
			$leftTime = $row->sell_enddate - $today;
			//$leftDate1 = date("Y-m-d H:i:s",$row->sell_enddate);
			//$leftDate2 = date("Y-m-d H:i:s",$today);
			$left_d = intval($leftTime / (24*60*60));
			$mod_d	= $leftTime % (24*60*60);
			$left_H = $mod_d / (60*60);
			$mod_H	= $mod_d % (60*60);
			$left_i = $mod_H / 60;
			$mod_i	= $mod_H % 60;
			$left_s = $mod_i;
			$strLeftTime = sprintf("%02d%02d%02d" ,$left_H,$left_i,$left_s);
			$strLeftDay = ($left_d>0)? "<span id=\"timeleft0_".$i."\">".$left_d."일</span>":"";
			$arLeftTime[] = $leftTime;
			$buyText = "<IMG SRC=\"../images/design/gongguing_info_view.gif\" WIDTH=\"197\" HEIGHT=\"30\" ALT=\"\" border=0 id=\"buyImg_".$i."\">";
		}

		$discount =""; $discountRate ="";
		if($row->discount_state == "Y" && $row->consumerprice >0 ){
			$discount = 100 - intval($row->sellprice/$row->consumerprice*100)."%";
			$discountRate = sprintf("(%s %s할인)"
										,($row->complete_quantity>0)? sprintf("%d명이상 구매시",$row->complete_quantity):""
										,$discount);
		}

		$buyBtn = "<A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode."\">".$buyText."</a>";

?>


				<tr>
					<td class="table_td" align="center">
						<table cellpadding="0" cellspacing="0">
							<tr>
								<td class="table_td" align="right">&nbsp;</td>
							</tr>
							<tr>
								<td style="position:relative;">
									<table cellpadding="0" cellspacing="0" border="0" style="border:1px solid #eeeeee; table-layout:fixed;">
										<tr>
											<td valign="top" style="width:100%;">
												<div style="height:380px; text-align:center; overflow:hidden;"><?=$maximage?></div>
											</td>
											<td width="235" valign="top" background="../images/design/gongguing_info.jpg" height="100%">
												<table cellpadding="0" cellspacing="0" height="100%" align="center">
													<tr>
														<td colspan="2" height="25"></td>
													</tr>
													<tr>
														<td colspan="2" class="gongguing_date" align="center"><?=date("Y.m.d D",$today)?></td>
													</tr>
													<tr>
														<td colspan="2" class="gongguing_dates" align="center">(<?=date("Y.m.d D",$row->sell_enddate)?> 완료예정)</td>
													</tr>
													<tr>
														<td colspan="2" height="30"></td>
													</tr>
													<tr>
														<td height="30"><IMG SRC="../images/design/gongguing_info_time.gif" WIDTH=45 HEIGHT=18 ALT=""></td>
														<td class="gongguing_date" align="right"><?=$strLeftDay?></td>
													</tr>
													<tr>
														<td colspan="2">
															<table cellpadding="0" cellspacing="0" width="100%">
																<tr>
																	<td width="32" height="30" background="../images/design/gongguing_info_timebg.gif" class="gongguing_time" align="center"><span id="timeleft1_<?=$i?>"><?=substr($strLeftTime,0,1)?></span></td>
																	<td width="32" height="30" background="../images/design/gongguing_info_timebg.gif" class="gongguing_time" align="center"><span id="timeleft2_<?=$i?>"><?=substr($strLeftTime,1,1)?></span></td>
																	<td width="6" height="30" align="center"><img src="../images/design/gongguing_info_line.gif" width="6" height="30" border="0"></td>
																	<td width="32" height="30" background="../images/design/gongguing_info_timebg.gif" class="gongguing_time" align="center"><span id="timeleft3_<?=$i?>"><?=substr($strLeftTime,2,1)?></span></td>
																	<td width="32" height="30" background="../images/design/gongguing_info_timebg.gif" class="gongguing_time" align="center"><span id="timeleft4_<?=$i?>"><?=substr($strLeftTime,3,1)?></span></td>
																	<td width="6" height="30" align="center"><img src="../images/design/gongguing_info_line.gif" width="6" height="30" border="0"></td>
																	<td width="32" height="30" background="../images/design/gongguing_info_timebg.gif" class="gongguing_time" align="center"><span id="timeleft5_<?=$i?>"><?=substr($strLeftTime,4,1)?></span></td>
																	<td width="32" height="30" background="../images/design/gongguing_info_timebg.gif" class="gongguing_time" align="center"><span id="timeleft6_<?=$i?>"><?=substr($strLeftTime,5,1)?></span></td>
																</tr>
															</table>
														</td>
													</tr>
													<tr>
														<td colspan="2" height="20"></td>
													</tr>
													<tr>
														<td width="56"><IMG SRC="../images/design/gongguing_info_price.gif" WIDTH=36 HEIGHT=18 ALT=""></td>
														<td width="146" class="gongguing_price" align="right"><?=number_format($row->consumerprice)?><img src="../images/design/gongguing_info_price1.gif" width="14" height="11" border="0" hspace="3"></td>
													</tr>
													<tr>
														<td width="56" valign="top" style="padding-top:7px;" height="52"><IMG SRC="../images/design/gongguing_info_oneday.gif" WIDTH=45 HEIGHT=18 ALT=""></td>
														<td width="146" height="52" align="right">
															<table cellpadding="0" cellspacing="0" width="100%">
																<tr>
																	<td class="gongguing_price1" align="right"><?=number_format($row->sellprice)?><img src="../images/design/gongguing_info_price2.gif" width="14" height="11" border="0" hspace="3"></td>
																</tr>
																<tr>
																	<td class="gongguing_text" align="right"><?=$discountRate?></td>
																</tr>
															</table>
														</td>
													</tr>
													<tr>
														<td valign="top" style="padding-top:7px;" height="10" colspan="2"></td>
													</tr>
													<tr>
														<td width="56"><IMG SRC="../images/design/gongguing_info_n.gif" WIDTH="56" HEIGHT=18 ALT=""></td>
														<td width="146" class="gongguing_price1" align="right"><?=$nowSailCnt ?><img src="../images/design/gongguing_info_n1.gif" width="12" height="14" border="0" hspace="3"></td>
													</tr>
													<tr>
														<td colspan="2" style="padding-top:5px">
															<table cellpadding="0" cellspacing="0" width="100%" height="5">
																<tr>
																	<td width="100%" colspan="2" bgcolor="#606060"><IMG SRC="../images/design/gongguing_info_nbar.gif" WIDTH="<?=$sellBar?>%" HEIGHT=5 ALT=""></td>
																</tr>
																<tr>
																	<td width="50%" class="gongguing_text1">0명</td>
																	<td width="50%" class="gongguing_text1" align="right"><?=$row->complete_quantity ?>명</td>
																</tr>
															</table>
														</td>
													</tr>
													<tr><td colspan="2" height="100%"></td></tr>
													<tr><td colspan="2"><?=$buyBtn?></td></tr>
													<tr><td colspan="2" height=15></td></tr>
												</table>
												<?=(strlen($discount)>0)? "<div class=\"discount_png_wrap\"><span class=\"discount_png\">".$discount."</span></div>":""?>
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr><td height="20"></td></tr>


<?
		$i++;
	}
	mysql_free_result($result);

	if($i==0) {
		if($type=="complete") $msg="마감된 공동구매가 없습니다.";
		else $msg="진행 중인 공동구매가 없습니다.";
		echo "<tr height=\"30\"><td align=\"center\">".$msg."</td></tr>\n";
	}
?>

				<tr>
					<td>
						<table align="center" cellpadding="0" cellspacing="0" width="100%">
							<tr>
								<td height="30"><img src="../img/cmn/con_line02.gif" width="100%" height="1" border="0"></td>
							</tr>
							<tr>
								<td>
									<table align="center" cellpadding="0" cellspacing="0" width="100%">
										<tr>
											<td class="table01_con2" align="right">

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
				$a_first_block .= "<a href='javascript:GoPage(0,1);' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='첫 페이지';return true\"><img src=\"../img/cmn/btn_first.gif\" height=\"22\" border=\"0\" align=\"absmiddle\"></a>&nbsp;&nbsp;";

				$prev_page_exists = true;
			}

			$a_prev_page = "";
			if ($nowblock > 0) {
				$a_prev_page .= "<a href='javascript:GoPage(".($nowblock-1).",".($setup[page_num]*($block-1)+$setup[page_num]).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='이전 ".$setup[page_num]." 페이지';return true\"><FONT class=\"table01_con2\"><img src=\"../img/cmn/btn_pre.gif\" height=\"22\" border=\"0\" align=\"absmiddle\"></FONT></a>&nbsp;&nbsp;";

				$a_prev_page = $a_first_block.$a_prev_page;
			}

			// 일반 블럭에서의 페이지 표시부분-시작

			if (intval($total_block) <> intval($nowblock)) {
				$print_page = "";
				for ($gopage = 1; $gopage <= $setup[page_num]; $gopage++) {
					if ((intval($nowblock*$setup[page_num]) + $gopage) == intval($gotopage)) {
						$print_page .= "<b><font color=\"#FF511B\">".(intval($nowblock*$setup[page_num]) + $gopage)."</font></b> ";
					} else {
						$print_page .= "<a href='javascript:GoPage(".$nowblock.",".(intval($nowblock*$setup[page_num]) + $gopage).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$setup[page_num]) + $gopage)."';return true\"><FONT class=\"table01_con2\">".(intval($nowblock*$setup[page_num]) + $gopage)."</FONT></a> ";
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
						$print_page .= "<b><font color=\"#FF511B\">".(intval($nowblock*$setup[page_num]) + $gopage)."</font></b> ";
					} else {
						$print_page .= "<a href='javascript:GoPage(".$nowblock.",".(intval($nowblock*$setup[page_num]) + $gopage).");' onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$setup[page_num]) + $gopage)."';return true\"><FONT class=\"table01_con2\">".(intval($nowblock*$setup[page_num]) + $gopage)."</FONT></a> ";
					}
				}
			}		// 마지막 블럭에서의 표시부분-끝


			$a_last_block = "";
			if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
				$last_block = ceil($t_count/($setup[list_num]*$setup[page_num])) - 1;
				$last_gotopage = ceil($t_count/$setup[list_num]);

				$a_last_block .= "&nbsp;&nbsp;<a href='javascript:GoPage(".$last_block.",".$last_gotopage.");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='마지막 페이지';return true\"><FONT class=\"prlist\"><img src=\"../img/cmn/btn_end.gif\" height=\"22\" border=\"0\" align=\"absmiddle\"></FONT></a>";

				$next_page_exists = true;
			}

			// 다음 10개 처리부분...

			$a_next_page = "";
			if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
				$a_next_page .= "&nbsp;&nbsp;<a href='javascript:GoPage(".($nowblock+1).",".($setup[page_num]*($nowblock+1)+1).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='다음 ".$setup[page_num]." 페이지';return true\"><FONT class=\"table01_con2\"><img src=\"../img/cmn/btn_next.gif\" height=\"22\" border=\"0\" align=\"absmiddle\"></FONT></a>";

				$a_next_page = $a_next_page.$a_last_block;
			}
		} else {
			$print_page = "<FONT class=\"table01_con2\">1</FONT>";
		}
		echo $a_div_prev_page.$a_prev_page.$print_page.$a_next_page.$a_div_next_page
?>

											</td>
											<!-- <td width="3" class="table01_con" align="right"></td>
											<td width="25" class="table01_con" align="right"><img src="../img/cmn/btn_next.gif" width="51" height="22" border="0" hspace="0"></td> -->
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td height="30"><img src="../img/cmn/con_line02.gif" width="100%" height="1" border="0"></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr><td height="20"></td></tr>
				<tr>
					<td>
						<table cellpadding="10" cellspacing="1" width="100%" bgcolor="#EDEDED">
							<tr>
								<td bgcolor="#F9F9F9" class="table_td" height="35">
									<form name="frmMailing2" action="gonggu_mailing.php" method="post" target="feedFrmFrame" style="display:inline" onsubmit="return feedFunc(this)">
									<table cellpadding="0" cellspacing="0" width="90%" align="center">
										<tr>
											<td class="table_td">공동구매상품 소식 구독하기 : </td>
											<td class="table_td">이메일</td>
											<td><input type="checkbox" name="emailCheck" value='1' onclick="if(this.checked) this.form.feedEmail.disabled=false; else this.form.feedEmail.disabled=true;" ></td>
											<td><input type="text" name="feedEmail" maxlength="40" size="17" class="input" disabled style="width:190px"></td>
											<td class="table_td">휴대폰</td>
											<td><input type="checkbox" name="smsCheck" value='1' onclick="if(this.checked) this.form.feedSms.disabled=false; else this.form.feedSms.disabled=true;" ></td>
											<td><input type="text" name="feedSms" maxlength="40" size="17" class="input" disabled  style="width:190px"></td>
											<td width="62"><input type='image' SRC="../images/design/gongguing_btn01.gif" WIDTH=62 HEIGHT=26 ALT=""></td>
										</tr>
									</table>
									</form>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr><td height="50"></td></tr>
			</table>
        </td>
    </tr>
</table>
<?
if($type != "complete" && $i > 0 ){
?>
<script type="text/javascript">
<!--
var pCnt =<?=$i?>;
var leftTime= [<?=implode(",",$arLeftTime)?>];
var CountText ='';
function showCountdown(){
	for(k=0;k<pCnt;k++){
		tempTime = leftTime[k];
		if(tempTime>=0){
			day = Math.floor(tempTime / (3600 * 24));
			mod = tempTime % (24 * 3600);

			hour = Math.floor(mod / 3600);
			mod = mod % 3600;

			min = Math.floor(mod / 60);
			sec = mod % 60;
			if(day >0){
				document.getElementById("timeleft0_"+k).innerText = day+"일";
			}
			document.getElementById("timeleft1_"+k).innerText = Math.floor(hour / 10);
			document.getElementById("timeleft2_"+k).innerText = Math.floor(hour % 10);
			document.getElementById("timeleft3_"+k).innerText = Math.floor(min / 10);
			document.getElementById("timeleft4_"+k).innerText = Math.floor(min % 10);
			document.getElementById("timeleft5_"+k).innerText = Math.floor(sec / 10);
			document.getElementById("timeleft6_"+k).innerText = Math.floor(sec % 10);
			if (tempTime == 0){
				document.getElementById("buyImg_"+k).src =""; //구매종료이미지
			}
			leftTime[k] = tempTime-1;
		}
	}
	setTimeout("showCountdown()", 1000);
}
showCountdown();
//-->
</script>
<?
}
?>
<form name=form2 method=get action="<?=$_SERVER[PHP_SELF]?>">
<input type=hidden name=code value="<?=$code?>">
<input type=hidden name=listnum value="<?=$listnum?>">
<input type=hidden name=sort value="<?=$sort?>">
<input type=hidden name=block value="<?=$block?>">
<input type=hidden name=gotopage value="<?=$gotopage?>">
<input type=hidden name=type value="<?=$type?>">
</form>



<? include ($Dir."lib/bottom.php") ?>

</BODY>
</HTML>