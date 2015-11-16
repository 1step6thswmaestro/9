<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$sleftMn = "NO";

unset($row);
$sql = "SELECT * FROM tbldesign ";
$result=mysql_query($sql,get_db_conn());
if($crow=mysql_fetch_object($result)) {

} else {
	$crow->introtype="C";
}
mysql_free_result($result);


$code=$_REQUEST["code"];

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

$type="complete";

$s_year=$_REQUEST["s_year"];
$s_month=$_REQUEST["s_month"];
$s_year = (!$s_year)? date("Y"):$s_year;
$s_month = (!$s_month)? date("m"):$s_month;
?>

<HTML>
<HEAD>
<TITLE><?=$_data->shoptitle?> 디자인샵 > 공동구매 > 진행중인 공동구매</TITLE>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META http-equiv="X-UA-Compatible" content="IE=5" />
<META name="description" content="<?=(strlen($_data->shopdescription)>0?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<script type="text/javascript" src="<?=$Dir?>lib/jquery-1.4.2.min.js"></script>
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

//-->
</SCRIPT>
<style>
.gonglist_wrap{width:440px;height:210px;float:left;overflow:hidden;}
.mar_r10{margin-right:10px;}
</style>
</HEAD>

<body<?=(substr($_data->layoutdata["MOUSEKEY"],0,1)=="Y"?" oncontextmenu=\"return false;\"":"")?><?=(substr($_data->layoutdata["MOUSEKEY"],1,1)=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=(substr($_data->layoutdata["MOUSEKEY"],2,1)=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>

<? include ($Dir.MainDir.$_data->menu_type.".php") ?>

<table align="center" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td valign="top">
			<table align="center" cellpadding="0" cellspacing="0" width="100%" class="table_td">
				<tr>
					<td>
						<table cellpadding="0" cellspacing="0" width="100%">
							<tr>
								<td><IMG SRC="../images/design/gonggu_title01.gif" ALT=""><IMG SRC="../images/design/gonggu_title01_text.gif" ALT=""></td>
								<td class="course" align="right"></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td height=20></td>
				</tr>
				<tr>
					<td>
						<TABLE WIDTH=100% BORDER=0 CELLPADDING=0 CELLSPACING=0>
							<TR>
								<TD><a href="../front/gonggu_main.php"><IMG SRC="../images/design/gonggu_tap01.gif" ALT="" border="0"></a></TD>
								<TD><a href="../front/gonggu_end.php"><IMG SRC="../images/design/gonggu_tap02r.gif" ALT="" border="0"></a></TD>
								<TD><a href="../front/gonggu_order.php"><IMG SRC="../images/design/gonggu_tap03.gif"  ALT="" border="0"></a></TD>
								<TD><a href="../front/gonggu_guide.php"><IMG SRC="../images/design/gonggu_tap04.gif"  ALT="" border="0"></a></TD>
								<TD width="100%" background="../images/design/gonggu_tap_bg.gif">&nbsp;</TD>
							</TR>
						</TABLE>
					</td>
				</tr>
				<tr>
					<td>
						<table cellpadding="10" cellspacing="1" width="100%" bgcolor="#EDEDED">
							<tr>
								<td width="922" bgcolor="#F9F9F9" height="70">
									<table cellpadding="0" cellspacing="0" width="100%">
										<tr>
											<td width="81"><IMG SRC="../images/design/gonggu_end_date.gif" WIDTH=81 HEIGHT=24 ALT=""></td>
											<td width="36" align="left"><a href="?s_year=<?=$s_year-1?>&s_month=12"><IMG SRC="../images/design/gonggu_end_prev.gif" WIDTH=36 HEIGHT=24 ALT=""></a></td>
											<td width="50" class="gongguing_end_date"><?=$s_year ?></td>
											<td width="1"><IMG SRC="../images/design/gonggu_end_year.gif" WIDTH=16 HEIGHT=24 ALT=""></td>
									<?
									for($i=1;$i<=12;$i++){
										echo "<td align=\"center\">";
										if($s_year > date("Y") || ($s_year == date("Y") && $i > date("m") )){
											echo sprintf("<IMG SRC=\"../images/design/gonggu_end_%dth_n.gif\" HEIGHT=24 ALT=\"\">", $i);
										}else{
											echo sprintf("<a href=\"?s_year=%d&s_month=%d\"><IMG SRC=\"../images/design/gonggu_end_%dth%s.gif\" HEIGHT=24 ALT=\"\"></a>"
											, $s_year
											, $i
											, $i
											, ($s_month ==$i)? "_s":"");
										}
										echo "</td>";
									}
									?>
											<td width="36" align="right"><?=($s_year >= date("Y"))? "":"<a href=\"?s_year=".($s_year+1)."&s_month=1\">"?><IMG SRC="../images/design/gonggu_end_next.gif" WIDTH=36 HEIGHT=24 ALT=""><?=($s_year == date("Y"))? "":"</a>"?></td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td height="30"></td>
				</tr>
				<tr>
					<td>
<!-- list 시작 -->
<?
	$today=time();
	$sch_date = sprintf("%04d-%02d", $s_year, $s_month);
	$sCondition = "AND P.productcode = S.pcode ";
	//$sCondition .= "AND P.productcode LIKE '".$likecode."%' ";
	$sCondition.= "AND display='Y' ";
	if ($type=="complete"){
		$sCondition.= "AND (sell_enddate < '".$today."' OR quantity = 0 )";
		$sCondition.= "AND (from_unixtime( sell_startdate, '%Y-%m' ) = '".$sch_date."' or from_unixtime( sell_enddate, '%Y-%m' ) = '".$sch_date."' ) ";
	}

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
			$maximage = "<img src='".$imagepath.$row->maximage."' width=231 height=\"127\" class=\"img\" >";
		} else {
			$maximage = "<img src='".$Dir."images/no_img.gif'  width=231 height=\"127\" class=\"img\" >";
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

		if($row->quantity == "0")
			$buyText = "<IMG SRC=\"../images/common/product/".$_cdata->list_type."/social_shopping_soldout.gif\" WIDTH=\"190\" HEIGHT=\"30\" ALT=\"\" border=0 id=\"buyImg_".$i."\">";//SOLD OUT
		else
			$buyText = "<IMG SRC=\"../images/common/product/".$_cdata->list_type."/social_shopping_endbuy.gif\" WIDTH=\"190\" HEIGHT=\"30\" ALT=\"\" border=0 id=\"buyImg_".$i."\">";//판매종료
		$strLeftTime = "000000";

		$discountRate ="";
		if($row->discount_state == "Y"){
			$discountRate = sprintf("(%d명이상 구매시 %s할인)"
										,$row->complete_quantity
										,100-intval($row->sellprice/$row->consumerprice*100)."%");
		}

		$buyBtn = "<A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode."\">".$buyText."</a>";

?>

						<div class="gonglist_wrap <?=($i%2 ==0)? "mar_r10":""?>">

									<table cellpadding="0" cellspacing="0" width="440">
										<tr>
											<td width="6"><IMG SRC="../images/design/gonggu_end_t01.gif" WIDTH=6 HEIGHT=6 ALT=""></td>
											<td background="../images/design/gonggu_end_tbg1.gif"></td>
											<td width="6"><p align="right"><IMG SRC="../images/design/gonggu_end_t02.gif" WIDTH=6 HEIGHT=6 ALT=""></td>
										</tr>
										<tr>
											<td width="6" background="../images/design/gonggu_end_tbg2.gif"></td>
											<td style="padding:10px;">
												<table cellpadding="0" cellspacing="0" width="100%">
													<tr>
														<td colspan="3" height="30" class="gongguing_end_text1"><?=$row->productname?></td>
													</tr>
													<tr>
														<td width="231" valign="top"><A HREF="<?=$Dir.FrontDir."productdetail.php?productcode=".$row->productcode?>"><?=$maximage?></a></td>
														<td width="10" valign="top"></td>
														<td valign="top" height="100%">
															<table cellpadding="0" cellspacing="0" width="100%" height="100%">
																<tr>
																	<td width="170" colspan="2" class="gongguing_end_text2" height="25"><?=$nowSailCnt?>명 구매<IMG SRC="../images/design/gonggu_end_btn_<?=($nowSailCnt>=$row->complete_quantity)? "ok":"cancel"?>.gif" WIDTH=46 HEIGHT=16 ALT="" align="absmiddle" hspace="8"></td>
																</tr>
																<tr>
																	<td width="34"><IMG SRC="../images/design/gonggu_end_price.gif" WIDTH=34 HEIGHT=17 ALT=""></td>
																	<td class="gongguing_end_price1"><?=number_format($row->consumerprice)?>원</td>
																</tr>
																<tr>
																	<td width="34"><IMG SRC="../images/design/gonggu_end_gonggu.gif" WIDTH=34 HEIGHT=17 ALT=""></td>
																	<td class="gongguing_end_price2"><?=number_format($row->sellprice)?>원</td>
																</tr>
																<tr>
																	<td width="34"></td>
																	<td class="gongguing_end_text3"><?=$discountRate?></td>
																</tr>
																<tr>
																	<td width="170" colspan="2" height="100%"></td>
																</tr>
																<tr>
																	<td width="170" colspan="2"><IMG SRC="../images/design/gonggu_end_btn_order.gif" WIDTH=130 HEIGHT=31 ALT="" style="cursor:pointer" onclick="gongguEncore('<?=$row->productcode?>')"></td>
																</tr>
															</table>
														</td>
													</tr>
												</table>
											</td>
											<td width="6" background="../images/design/gonggu_end_tbg3.gif"></td>
										</tr>
										<tr>
											<td width="6"><IMG SRC="../images/design/gonggu_end_t03.gif" WIDTH=6 HEIGHT=7 ALT=""></td>
											<td background="../images/design/gonggu_end_tbg4.gif"></td>
											<td width="6" align="right"><IMG SRC="../images/design/gonggu_end_t04.gif" WIDTH=6 HEIGHT=7 ALT=""></td>
										</tr>
									</table>

							</div>
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

					</td>
				</tr>
				<tr>
					<td height="20"></td>
				</tr>
				<tr>
					<td>
						<table align="center" cellpadding="0" cellspacing="0" width="100%">
							<tr>
								<td height="30">
									<img src="../img/cmn/con_line02.gif" width="100%" height="1" border="0">
								</td>
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
				$a_first_block .= "<a href='javascript:GoPage(0,1);' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='첫 페이지';return true\"><FONT class=\"prlist\">[1...]</FONT></a>&nbsp;&nbsp;";

				$prev_page_exists = true;
			}

			$a_prev_page = "";
			if ($nowblock > 0) {
				$a_prev_page .= "<a href='javascript:GoPage(".($nowblock-1).",".($setup[page_num]*($block-1)+$setup[page_num]).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='이전 ".$setup[page_num]." 페이지';return true\"><FONT class=\"table01_con2\">[prev]</FONT></a>&nbsp;&nbsp;";

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

				$a_last_block .= "&nbsp;&nbsp;<a href='javascript:GoPage(".$last_block.",".$last_gotopage.");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='마지막 페이지';return true\"><FONT class=\"prlist\">[...".$last_gotopage."]</FONT></a>";

				$next_page_exists = true;
			}

			// 다음 10개 처리부분...

			$a_next_page = "";
			if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
				$a_next_page .= "&nbsp;&nbsp;<a href='javascript:GoPage(".($nowblock+1).",".($setup[page_num]*($nowblock+1)+1).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='다음 ".$setup[page_num]." 페이지';return true\"><FONT class=\"table01_con2\">[next]</FONT></a>";

				$a_next_page = $a_next_page.$a_last_block;
			}
		} else {
			$print_page = "<FONT class=\"table01_con2\">1</FONT>";
		}
		echo $a_div_prev_page.$a_prev_page.$print_page.$a_next_page.$a_div_next_page
?>

											</td>
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
				<tr>
					<td height="100"></td>
				</tr>
			</table>
        </td>
    </tr>
</table>
<p>&nbsp;</p>
<script type="text/javascript">
<!--
function gongguEncore(pcode){
	var memId = "<?=$_ShopInfo->getMemid() ?>";
	if(memId == ""){
		alert("로그인이 필요한 서비스입니다.");
		return false;
	}
	$j.post(
		"snsAction.php",
		{ method: "encore", pcode:pcode},
		  function(data){
			if ( data.result == 'true' ) {
				alert(data.message);
				return false;
			}
		},
		"json"
	);
}
//-->
</script>





<? include ($Dir."lib/bottom.php") ?>

</BODY>
</HTML>