<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
INCLUDE ("access.php");

include_once($Dir.'lib/class/pages.php');
####################### 페이지 접근권한 check ###############
$PageCode = "pr-1";
$MenuCode = "nomenu";
if (!$_usersession->isAllowedTask($PageCode)) {
	INCLUDE ("AccessDeny.inc.php");
	exit;
}
#########################################################
function _getCategoryList($categorycode,$type=1){
	$fabcatenameSQL = "SELECT code_name FROM tblproductcode WHERE ";
	$realcatenameSQL ="";
	for($i=0;$i<4;$i++){
		$code = substr($categorycode,$i*3,3);
		if(strlen($code) >=3 && $code !='000'){
			$realcode .= "code".chr(65+$i)."='".$code."'";
			$loop = 3 - $i;
			for($j=0;$j<$loop;$j++){
				$fabrication .= " AND code".chr(65+$i+1)."='000'";
			}
			$realcatenameSQL = $fabcatenameSQL.$realcode.$fabrication;
			if(false !== $realcatenameRes = mysql_query($realcatenameSQL,get_db_conn())){
				$checkp++;
				$returndata .= mysql_result($realcatenameRes,0,0); 
				mysql_free_result($realcatenameRes);
				$returndata .= ">";
			}
			if($i<3){
				$realcode.=" AND ";
			}
			$fabrication="";
		}
	}
	if(substr($returndata,-1) == ">"){
		$returndata = substr($returndata,0,-1);
	}
	return $returndata;
}
$regmalldate = substr($_shopdata->regdate,0,4)."-".substr($_shopdata->regdate,4,2)."-".substr($_shopdata->regdate,6,2);
$today = date('Y-m-d');
$inmode = array('search','sort','page');
$mode = isset($_POST['mode'])? trim($_POST['mode']):"";
$productname = isset($_POST['prname'])? trim($_POST['prname']):"";
$sellstart = isset($_POST['sellstart'])? trim($_POST['sellstart']):"";
$sellend = isset($_POST['sellend'])? trim($_POST['sellend']):"";
$startmoney = isset($_POST['startmoney'])? trim($_POST['startmoney']):"";
$endmoney = isset($_POST['endmoney'])? trim($_POST['endmoney']):"";
$sorttype = isset($_POST['sorttype'])? trim($_POST['sorttype']):"";
$page = isset($_POST['page'])? trim($_POST['page']):"";
if(strlen($page)==""){
	$page="1";
}
$condition = array();
$query_prname="";
$query_sellstart="";
$query_sellend="";
$query_startmoney="";
$query_endmoney="";
if(strlen($mode)>0 && in_array($mode, $inmode)){
	if(strlen($productname)>0){
		$query_prname = "UPPER(productname) LIKE UPPER('%".$productname."%')";
		array_push($condition,$query_prname);
	}
	if(strlen($sellstart)>0){
		$query_sellstart = $sellstart." 00:00:00";
		$query_sellstart = "selldate>='".$query_sellstart."'";
		array_push($condition,$query_sellstart);
	}
	if(strlen($sellend)>0){
		$query_sellend = $sellend." 23:59:59";
		$query_sellend = "selldate<='".$query_sellend."'";
		array_push($condition,$query_sellend);
	}
	if(strlen($startmoney) >0){
		$query_startmoney = "sellprice>='".$startmoney."'";
		array_push($condition,$query_startmoney);
	}
	if(strlen($endmoney) >0){
		$query_endmoney = "sellprice<='".$endmoney."'";
		array_push($condition,$query_endmoney);
	}
}

$classname="";
$classprice="";
$classselldate="";
$classdate="";
switch($sorttype){
	case "name":
		$orderby = " ORDER BY productname DESC ";
		$classname="target";
	break;
	case "price":
		$orderby = " ORDER BY sellprice DESC ";
		$classprice="target";
	break;
	case "selldate":
		$orderby = " ORDER BY selldate DESC ";
		$classselldate="target";
	break;
	case "date":
	default:
		$classdate="target";
		$date = " ORDER BY regdate DESC ";
	break;
}

$searchSQL = "SELECT * FROM tblproduct WHERE productcode != '' AND productcode IS NOT NULL ";
if(sizeof($condition)){
	$searchSQL .= "AND ".implode(" AND ",$condition);
}
$searchSQL .= $orderby;

if(false !== $searchRes = mysql_query($searchSQL,get_db_conn())){
	$rowcount = mysql_num_rows($searchRes);
	mysql_free_result($searchRes);
}
$listnum = 10;
?>
<? INCLUDE "header.php"; ?>
<link rel="stylesheet" href="<?=$Dir?>css/ui-lightness/jquery-ui-1.10.4.custom.min.css">
<style>
	form,table,img,tbody,thead,tfoot{margin:0px;padding:0px;border:0px;}
	a:link{text-decoration:none}
	a:hover{text-decoration:none}
	input{border:1px solid #888;text-align:center;height:22px;line-height:22px;}
	.tb_sarchform{border:1px solid #999;border-right:2px solid #999;margin-bottom:5px;}
	.tb_sarchform th{width:100px;font-size:13px;border-bottom:1px solid #888;border-right:1px solid #888}
	.tb_sarchform td{padding:2px 5px;border-bottom:1px solid #888}
	.searchbtn_wrap{width:100%; margin:15px 0px 25px 0px; text-align:center;}
	.searchbtn_wrap a span{display:inline-block; border:2px solid #777;background-color:#aeaeae;height:22px;width:65px;line-height:22px;text-align:center;text-decoration:none;cursor:pointer;color:#FFF;font-weight:bold}
	.tb_orderbytype{margin:5px 0px;}
	.tb_orderbytype th{width:56px; font-size:11px; text-align:left; color:#FF2424}
	.tb_orderbytype td{font-size:11px; text-align:left;}
	.tb_orderbytype td a{margin:0px 2px; border:1px solid #E3E3E3; display:inline-block; height:18px; line-height:18px; width:55px; text-align:center; cursor:pointer; font-size:11px;}
	.tb_orderbytype td a:hover{border:1px solid #999; font-weight:bold;}
	.tb_list {margin-top:5px; border:1px solid #888;}
	.tb_list th {height:30px; border-bottom:1px solid #888; border-right:1px solid #888; font-size:13px;}
	.tb_list td {padding:2px 0px; text-align:center; border-bottom:1px solid #888; border-right:1px solid #888;}
	.page_wrap {text-align:center; margin-top:5px;}
	.target {font-weight:bold}
</style>
<script src="<?=$Dir?>js/jquery-1.10.2.js"></script>
<script src="<?=$Dir?>js/jquery-ui-1.10.4.custom.min.js"></script>
<script type="text/javascript">
	$(function(){
		$("#sellstart").datepicker({
			yearRange: 'c-0:c+2',
			dateFormat:'yy-mm-dd',
			monthNamesShort:['01','02','03','04','05','06','07','08','09','10','11','12']
		});
		$("#sellend").datepicker({
			yearRange: 'c-0:c+2',
			dateFormat:'yy-mm-dd',
			monthNamesShort:['01','02','03','04','05','06','07','08','09','10','11','12']
		});
	});
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
								<? include ("menu_product.php"); ?>
								</td>
								<td></td>
								<td valign="top">
									<table cellpadding="0" cellspacing="0" width="100%">
										<tr>
											<td height="29" colspan="3">
												<table cellpadding="0" cellspacing="0" width="100%">
												<tr>
													<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 상점관리 &gt; 카테고리/상품관리 &gt; <span class="2depth_select">등록된 상품 수정</span></td>
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
												<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%" style="table-layout:fixed">
													<tr><td height="8"></td></tr>
													<tr>
														<td>
															<table width="100%" border=0 cellpadding=0 cellspacing=0>
																<tr>
																	<td><img src="images/regproduct_search.gif" alt="등록된 상품 조회" /></td>
																</tr>
																<tr>
																	<td width="100%" background="images/title_bg.gif" height=21></td>
																</tr>
															</table>
														</td>
													</tr>
													<tr><td height="3"></td></tr>
													<tr>
														<td style="padding-bottom:3pt;">
															<table width="100%" border=0 cellpadding=0 cellspacing=0>
																<tr>
																	<td><img src="images/distribute_01.gif"></td>
																	<td COLSPAN=2 background="images/distribute_02.gif"></td>
																	<td><img src="images/distribute_03.gif"></td>
																</tr>
																<tr>
																	<td background="images/distribute_04.gif"><img src="images/distribute_04.gif" ></td>
																	<td width="100%" class="notice_blue" colspan="2" style="padding-left:10px;">등록된 상품 정보 조회 및 수정을 하실 수 있습니다.</td>
																	<td background="images/distribute_07.gif"><img src="images/distribute_07.gif" ></td>
																</tr>
																<tr>
																	<td><img src="images/distribute_08.gif"></td>
																	<td COLSPAN=2 background="images/distribute_09.gif"></td>
																	<td><img src="images/distribute_10.gif"></td>
																</tr>
															</table>
														</td>
													</tr>
													<tr>
														<td height="20"></td>
													</tr>
													<tr>
														<td><img src="images/regproduct_search_stitle1.gif" alt="" /></td>
													</tr>
													<tr>
														<td class="notice_blue" style="padding:5px 25px;">
															1) 판매일 입력폼을 클릭하시면 달력이 오픈됩니다.<br />
															2) 원하는 검색 결과값을 위해 검색 정보는 가급적 정확하게 입력해 주시기 바랍니다.
														</td>
													</td>
													<tr>
														<td valign="top">
															<form name="searchForm" action="<?=$_SERVER['PHP_SELF']?>" method="post">
																<table cellpadding="0" cellspacing="0" width="100%">
																	<col width="140"></col>
																	<col width=""></col>
																	<tr><td background="images/table_top_line.gif" colSpan="2"></td></tr>
																	<tr>
																		<td class="table_cell"><img width="8" height="11" src="images/icon_point2.gif" border="0"/> 상품명</td>
																		<td class="td_con1"><input type="text" name="prname" value="<?=$productname?>" class="input" style="width:400px; text-align:left; padding:0px;" /></td>
																	</tr>
																	<tr><td background="images/table_con_line.gif" colSpan="2"></td></tr>
																	<tr>
																		<td class="table_cell"><img width="8" height="11" src="images/icon_point2.gif" border="0"/> 판매일</td>
																		<td class="td_con1"><input type="text" name="sellstart" id="sellstart" class="input_selected" style="width:100px; text-align:left; padding:0px;" value="<?=$sellstart?>" readonly/> ~ <input type="text" name="sellend" id ="sellend" class="input_selected" style="width:100px; text-align:left; padding:0px;" value="<?=$sellend?>" readonly/></td>
																	</tr>
																	<tr><td background="images/table_con_line.gif" colSpan="2"></td></tr>
																	<tr>
																		<td class="table_cell"><img width="8" height="11" src="images/icon_point2.gif" border="0"/> 판매가</td>
																		<td class="td_con1"><input type="text"  name="startmoney" value="<?=$startmoney?>" class="input" style="width:100px; text-align:left; padding:0px;" onkeyup="numCheck(this.value);"/> ~ <input type="text" name="endmoney" value="<?=$endmoney?>" class="input" style="width:100px; text-align:left; padding:0px;" onkeyup="numCheck(this.value)"/></td>
																	</tr>
																	<tr><td background="images/table_top_line.gif" colSpan="2"></td></tr>
																</table>
																<div class="searchbtn_wrap">
																	<!-- <a href="javascript:sendForm('search');" ><img src="images/botteon_search.gif" alt="검색"/></a> -->
																	<a href="javascript:sendForm('search');"><img src="images/botteon_search.gif" border="0" /></a>
																</div>

																<table cellpadding="0" cellspacing="0" border="0" class="tb_orderbytype">
																	<tr>
																		<td colspan="2"><img src="images/regproduct_search_stitle2.gif" alt="" /></td>
																	</tr>
																	<tr><td height="10"></td></tr>
																	<tr>
																		<th>정렬방법</th>
																		<td><a href="javascript:orderType('name');"><span class="<?=$classname?>">상품명</span></a><a href="javascript:orderType('price');"><span class="<?=$classprice?>">판매가</span></a><a href="javascript:orderType('date');"><span class="<?=$classdate?>">등록일</span></a><a href="javascript:orderType('selldate');"><span class="<?=$classselldate?>">판매일</span></a></td>
																	</tr>
																</table>

																<table cellpadding="0" cellspacing="0" width="100%" border="0">
																	<col width="20%"/>
																	<col width=""/>
																	<col width="80"/>
																	<col width="80"/>
																	<col width="80"/>
																	<col width="70"/>
																	<thead>
																		<tr><td height="1" background="images/table_top_line.gif" colSpan="6"></td></tr>
																		<tr>
																			<td class="table_cell" align="center">카테고리</td>
																			<td class="table_cell1" align="center">상품명</td>
																			<td class="table_cell1" align="center">판매가격</td>
																			<td class="table_cell1" align="center">등록일</td>
																			<td class="table_cell1" align="center">판매일</td>
																			<td class="table_cell1" align="center">비고</td>
																		</tr>
																		<tr><td background="images/table_con_line.gif" colSpan="6"></td></tr>
																	</thead>
																	<tbody>
																<?
																	$searchSQL .="LIMIT ".($listnum * ($page -1)).", ".$listnum;
																	if(false !== $searchListRes = mysql_query($searchSQL,get_db_conn())){
																		$categoryname="";
																		$productname="";
																		$salemoney="";
																		$regdate="";
																		$saledate="";
																		$productcode="";
																		if($rowcount >0){
																			while($searchRow = mysql_fetch_assoc($searchListRes)){
																				$categoryname=_getCategoryList(substr($searchRow['productcode'],0,12));
																				$productname= $searchRow['productname'];
																				$salemoney=number_format($searchRow['sellprice']);
																				$regdate=substr($searchRow['regdate'],0,10);
																				$saledate=substr($searchRow['selldate'],0,10);
																				$productcode=$searchRow['productcode'];
																?>
																		<tr>
																			<td class="td_con2"><?=$categoryname?></td>
																			<td class="td_con1"><?=$productname?></td>
																			<td class="td_con1" align="right"><img style="margin-right: 2px;" src="images/won_icon.gif" border="0"/><span class="font_orange"><?=$salemoney?></span></td>
																			<td class="td_con1" align="center"><?=$regdate?></td>
																			<td class="td_con1" align="center"><?=$saledate?></td>
																			<td class="td_con1" align="center"><a href="javascript:modifyProduct('<?=$productcode?>')"><img src="images/icon_edit2.gif" border="0" alt="수정" /></a></td>
																		</tr>
																		<tr><td background="images/table_con_line.gif" colSpan="6"></td></tr>
																<?
																			}
																		}else{
																?>
																		<tr>
																			<td colspan="6" align="center" height="40">검색된 상품이 없습니다.</td>
																		</tr>
																		<tr><td background="images/table_con_line.gif" colSpan="6"></td></tr>
																<?
																		}

																	}
																?>
																	</tbody>
																</table>
																<input type="hidden" name="sorttype" value="<?=$sorttype?>"/>
																<input type="hidden" name="mode" value=""/>
																<input type="hidden" name="page" value=""/>
															</form>
														</td>
													</tr>
													<tr>
														<td height="52" align="center" background="images/blueline_bg.gif" style="border-bottom:2px solid ##ededed;">
															<div class="page_wrap">
																<?		
																	$pagePerBlock = ceil($rowcount / $listnum);
																	$pages = new pages(array('total_page'=>$pagePerBlock,'page'=>$page,'pageblocks'=>5,'links'=>"javascript:goPage('%u')"));
																	echo $pages->_solv()->_result('fulltext');
																?>
															</div>
														</td>
													</tr>
													<tr><td height="50"></td></tr>
													<tr>
														<td>
															<table width="100%" border="0" cellpadding="0" cellspacing="0">
																<tr>
																	<td><img src="images/manual_top1.gif" width="15" height="45" alt=""></td>
																	<td><img src="images/manual_title.gif" width="113" height="45" alt=""></td>
																	<td width="100%" background="images/manual_bg.gif"></td>
																	<td background="images/manual_bg.gif"></td>
																	<td><img src="images/manual_top2.gif" width="18" height="45" alt=""></td>
																</tr>
																<tr>
																	<td background="images/manual_left1.gif"></td>
																	<td colspan=3 width="100%" valign="top" bgcolor="white" style="padding-top:8pt; padding-bottom:8pt; padding-left:4pt;" class="menual_bg">
																		<table cellpadding="0" cellspacing="0" width="100%">
																			<col width=20></col>
																			<col width=></col>
																			<tr>
																				<td align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
																				<td><span class="font_dotline">등록된 상품 조회</span></td>
																			</tr>
																			<tr>
																				<td align="right">&nbsp;</td>
																				<td class="space_top">- 기본적으로 정렬은 등록일을 기준으로 정렬됩니다.</td>
																			</tr>
																			<tr>
																				<td align="right">&nbsp;</td>
																				<td class="space_top">- 상품명 검색시 영문의 경우 대소문자를 구분하지 않습니다.</td>
																			</tr>
																			<tr>
																				<td align="right">&nbsp;</td>
																				<td class="space_top">- 판매가 입력시 숫자만 입력하시기 바랍니다.</td>
																			</tr>
																			<tr>
																				<td align="right">&nbsp;</td>
																				<td class="space_top">- 판매일이 0000-00-00 형식으로 출력되는 상품은 판매가 되지 않은 상품입니다.</td>
																			</tr>
																			<tr>
																				<td align="right">&nbsp;</td>
																				<td class="space_top">- 모든 정렬은 내림차순으로 정렬됩니다.(예: 판매가 기준일 경우 큰 금액이 우선순위로 정렬됩니다)</td>
																			</tr>
																			<tr>
																				<td align="right">&nbsp;</td>
																				<td class="space_top">- 새창 버튼을 클릭할 경우 해당 상품을 수정 하실 수 있습니다.</td>
																			</tr>
																		</table>
																	</td>
																	<td background="images/manual_right1.gif"></td>
																</tr>
																<tr>
																	<td><img src="images/manual_left2.gif" width="15" height="8" alt=""></td>
																	<td colspan=3 background="images/manual_down.gif"></td>
																	<td><img src="images/manual_right2.gif" width="18" height="8" alt=""></td>
																</tr>
															</table>
														</td>
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
<form name="modifyForm" method="post">
	<input type="hidden" name="code" value=""/>
	<input type="hidden" name="prcode" value=""/>
	<input type="hidden" name="popup" value=""/>
</form>
<script type="text/javascript">
	function numCheck(_param){
		var regexp = /^[0-9]*$/;
		if(!regexp.test(_param)){
			return true;
		}else{
			return false;
		}
	}
	function sendForm(param){
		var _form = document.searchForm;
		_form.mode.value=param;
		if(_form.startmoney.value != ""){
			if(numCheck(_form.startmoney.value)){
				alert("판매가는 숫자만 입력가능합니다.");
				_form.startmoney.focus();
				return;
			}
		}
		if(_form.endmoney.value != ""){
			if(numCheck(_form.endmoney.value)){
				alert("판매가는 숫자만 입력가능합니다.");
				_form.endmoney.focus();
				return;
			}
		}
		
		if(_form.mode.value == "" || _form.mode.value == null){
			alert("정상적인 경로로 접근하시기 바랍니다.");
			return false;
		}

		
		_form.submit();
		return;
	}
	function orderType(param){
		var _form = document.searchForm;
		_form.mode.value = "sort";
		_form.sorttype.value= param;
		_form.submit();
		return;
	}

	function goPage(page){
		var _form = document.searchForm;
		_form.mode.value="page";
		_form.page.value=page;
		_form.submit();
		return;
	}

	function modifyProduct(prcode){
		var _form = document.modifyForm;
		code=prcode.substring(0,12);
		popup="YES";
		_form.code.value=code;
		_form.prcode.value=prcode;
		_form.popup.value=popup;
		if (popup=="YES") {
			_form.action="product_register.add.php";
			_form.target="register";
			window.open("about:blank","register","width=820,height=700,scrollbars=yes,status=no");
		} else {
			_form.action="product_register.php";
			_form.target="";
		}
		_form.submit();
	}
</script>
<? INCLUDE "copyright.php"; ?>