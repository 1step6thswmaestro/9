<?
$bankda->_authMatch();

####################### 페이지 접근권한 check ###############
$PageCode = "or-4";
$MenuCode = "order";
if (!$_usersession->isAllowedTask($PageCode)) {
	INCLUDE ("../AccessDeny.inc.php");
	exit;
}
/*
if($_POST['mode'] == "delete") {
	$d = $bankda->_delete($_POST['Bkid'],$info['user_id']);
}

if($orderby == "") {
	$orderby = "DESC";
}
$arr_status = array("N"=>"확인전","T"=>"입금확인(자동)","B"=>"입금확인(수동)","S" => "동명이인","F" => "실패(불일치)","A" => "관리자입금확인");
*/
#########################################################
$CurrentTime = time();
$period[0] = date("Y-m-d",$CurrentTime);
$period[1] = date("Y-m-d",$CurrentTime-(60*60*24*7));
$period[2] = date("Y-m-d",$CurrentTime-(60*60*24*15));
$period[3] = date("Y-m-d",mktime(0,0,0,date("m")-1,date("d"),date("Y")));
$period[4] = date("Y-m-d",mktime(0,0,0,date("m")-2,date("d"),date("Y")));
$period[5] = date("Y-m-d",mktime(0,0,0,date("m"),date("d"),date("Y")-10));//전체

if(_empty($_REQUEST['search_start'])) $_REQUEST['search_start']=$period[0];
if(_empty($_REQUEST['search_end'])) $_REQUEST['search_end']=date("Y-m-d",$CurrentTime);

$matching_period[0] = date("Y-m-d",$CurrentTime);
$matching_period[1] = date("Y-m-d",$CurrentTime-(60*60*24*7));
$matching_period[2] = date("Y-m-d",$CurrentTime-(60*60*24*15));
$matching_period[3] = date("Y-m-d",mktime(0,0,0,date("m")-1,date("d"),date("Y")));
$matching_period[4] = date("Y-m-d",mktime(0,0,0,date("m")-2,date("d"),date("Y")));
$period[5] = date("Y-m-d",mktime(0,0,0,date("m"),date("d"),date("Y")-10));//전체
$search_matching_start=$search_matching_start?$search_matching_start:$matching_period[0];
$search_matching_end=$search_matching_end?$search_matching_end:date("Y-m-d",$CurrentTime);

if($_POST['mode'] == "delete") {
	$d = $bankda->_deleteList($_POST['Bkid']);	
	if(!_empty($d['err']) && $d['err'] != 'ok' ) _alert($d['err']);
	unset($_POST['mode'],$_REQUEST['mode']);
}

$listinfo = $bankda->_getList($_REQUEST);
//_pr($res);
?>

<script type="text/javascript" src="/admin/lib.js.php"></script>
<script type="text/javascript" src="/admin/calendar.js.php"></script>
<script language="JavaScript">
function OnChangePeriod(val) {
	var pForm = document.form1;
	var period = new Array(7);
	period[0] = "<?=$period[0]?>";
	period[1] = "<?=$period[1]?>";
	period[2] = "<?=$period[2]?>";
	period[3] = "<?=$period[3]?>";
	period[4] = "<?=$period[4]?>";
	period[5] = "<?=$period[5]?>";

	pForm.search_start.value = period[val];
	pForm.search_end.value = period[0];
}

function OnChangematchingPeriod(val) {
	var pForm = document.form1;
	var matching_period = new Array(7);
	matching_period[0] = "<?=$period[0]?>";
	matching_period[1] = "<?=$period[1]?>";
	matching_period[2] = "<?=$period[2]?>";
	matching_period[3] = "<?=$period[3]?>";
	matching_period[4] = "<?=$period[4]?>";
	matching_period[5] = "<?=$period[5]?>";

	pForm.search_matching_start.value = matching_period[val];
	pForm.search_matching_end.value = matching_period[0];
}

function GoPage(page,listnum) {
	document.form1.page.value = page;
	document.form1.list_num.value = listnum;
	document.form1.submit();
}


function GoOrderby(orderby) {
	document.form1.orderby.value = orderby;
	document.form1.submit();
}

function OrderDetailView(ordercode) {
	document.detailform.ordercode.value = ordercode;
	window.open("","orderdetail","scrollbars=yes,width=700,height=600");
	document.detailform.submit();
}

function CheckForm(){
	document.form1.submit();
}

function CheckDelete(){
	option = confirm("선택한 정보를 삭제 하시겠습니까?"); 
	if(option == true ){ 
		document.form1.mode.value = "delete";		
		document.form1.submit();
	}
}

</script>
<style type="text/css">
.tblStyle{font-size:11px; line-height:200%; width:100%; margin-top:15px;}
.tblStyle caption{ text-align:left}
.tblStyle thead th{ background:##f8f8f8;  color:#4b4b4b;  font-weight:bold; border-bottom:1px solid #ddd;}
.tblStyle thead td{ border-bottom:1px solid #ddd; border-left:#e3e3e3}

.tblStyle tbody th{ background:##f8f8f8;  color:#4b4b4b;  font-weight:bold; border-bottom:1px solid #ddd;}
.tblStyle tbody td{ border-bottom:1px solid #ddd; border-left:#e3e3e3}

</style>
<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post>
<input type=hidden name=mode>
<input type=hidden name=orderby value="<?=$orderby?>">
<input type=hidden name=list_num value="<?=$_REQUEST['list_num']?>">
<input type=hidden name=page value="<?=$_REQUEST['page']?>">
<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
	<tr>
		<td>
			<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/order_bankm_title.gif" ALT="무통장 입금확인"></TD>
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
					<TD background="images/distribute_04.gif"><IMG SRC="images/distribute_04.gif" ></TD>
					<TD class="notice_blue"><IMG SRC="images/distribute_img.gif" ></TD>
					<TD width="100%" class="notice_blue"><p>무통장 입금확인을 하실 수 있습니다.</p></TD>
					<TD background="images/distribute_07.gif"><IMG SRC="images/distribute_07.gif" ></TD>
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
	<tr>
		<td>
			<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/order_bankm_stitle1.gif" ALT="무통장 입금조회"></TD>
					<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif"></TD>
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
			<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<col class=cellC><col class=cellL><col class=cellC><col class=cellL width=35%>
				<TR>
					<TD background="images/table_top_line.gif" width="153"><img src="images/table_top_line.gif"></TD>
					<TD colspan="3" background="images/table_top_line.gif" width="607" ></TD>
				</TR>
				<tr>
					<td class="table_cell">키워드검색</td>
					<td class="td_con1" >
						<select name="skey">
						<option value="" > 선택하기 </option>
						<option value="bkjukyo" <?if($skey == "bkjukyo") echo "selected";?>> 입금자명 </option>
						<option value="bkinput" <?if($skey == "bkinput") echo "selected";?>> 입금예정금액 </option>
						<option value="ordercode" <?if($skey == "ordercode") echo "selected";?>> 주문번호 </option>
						</select>
						<input type="text" NAME="sword" value="<?=$sword?>" class="line">
					</td>
					<td class="table_cell">현재상태<font class=small color=444444>/</font>은행명</td>
					<td class="td_con1" >
						<select name="Bkstatus">
						<option value=""> 전체 </option>
						<?
						foreach($bankda->statusArr as $stkey=>$stval){ 
							$sel = ($_REQUEST['Bkstatus'] == $stkey)?'selected':'';
						?>						
						<option value="<?=$stkey?>" <?=$sel?>><?=$stval?></option>
						<? } ?>
						</select>

						<select name="bkname">
						<option value="">↓은행검색</option>
						<option value="기업은행"  <?if($_REQUEST['bkname'] == "기업은행") echo "selected";?>>기업은행		
						<option value="국민은행"  <?if($_REQUEST['bkname'] == "국민은행") echo "selected";?>>국민은행		
						<option value="외환은행"  <?if($_REQUEST['bkname'] == "외환은행") echo "selected";?>>외환은행		
						<option value="주택은행"  <?if($_REQUEST['bkname'] == "주택은행") echo "selected";?>>주택은행		
						<option value="농협중앙회"  <?if($_REQUEST['bkname'] == "농협중앙회") echo "selected";?>>농협중앙회		
						<option value="농협개인"  <?if($_REQUEST['bkname'] == "농협개인") echo "selected";?>>농협개인		
						<option value="우리은행"  <?if($_REQUEST['bkname'] == "우리은행") echo "selected";?>>우리은행		
						<option value="조흥은행"  <?if($_REQUEST['bkname'] == "조흥은행") echo "selected";?>>조흥은행		
						<option value="제일은행"  <?if($_REQUEST['bkname'] == "제일은행") echo "selected";?>>제일은행		
						<option value="서울은행"  <?if($_REQUEST['bkname'] == "서울은행") echo "selected";?>>서울은행		
						<option value="신한은행"  <?if($_REQUEST['bkname'] == "신한은행") echo "selected";?>>신한은행		
						<option value="한미은행"  <?if($_REQUEST['bkname'] == "한미은행") echo "selected";?>>한미은행		
						<option value="대구은행"  <?if($_REQUEST['bkname'] == "대구은행") echo "selected";?>>대구은행		
						<option value="부산은행"  <?if($_REQUEST['bkname'] == "부산은행") echo "selected";?>>부산은행		
						<option value="광주은행"  <?if($_REQUEST['bkname'] == "광주은행") echo "selected";?>>광주은행		
						<option value="제주은행"  <?if($_REQUEST['bkname'] == "제주은행") echo "selected";?>>제주은행		<option value="전북은행"  <?if($_REQUEST['bkname'] == "전북은행") echo "selected";?>>전북은행		<option value="경남은행"  <?if($_REQUEST['bkname'] == "경남은행") echo "selected";?>>경남은행		<option value="새마을금고"  <?if($_REQUEST['bkname'] == "새마을금고") echo "selected";?>>새마을금고		<option value="우체국"  <?if($_REQUEST['bkname'] == "우체국") echo "selected";?>>우체국		<option value="하나은행"  <?if($_REQUEST['bkname'] == "하나은행") echo "selected";?>>하나은행
						</select>
					</td>
				</tr>
				<TR>
					<TD colspan="4" background="images/table_con_line.gif"><img src="images/table_con_line.gif" width="4" height="1" border="0"></TD>
				</TR>
				<tr>
					<td class="table_cell">입금일</td>
					<td colspan="3" class="td_con1" >
						<input type=text name="search_start" value="<?=$_REQUEST['search_start']?>" size=13 onfocus="this.blur();" OnClick="Calendar(this)"  class="input_selected"> ~ <input type=text name=search_end value="<?=$_REQUEST['search_end']?>" size=13 onfocus="this.blur();" OnClick="Calendar(this)"  class="input_selected">
						<img src="images/btn_today01.gif" border=0 align="absmiddle" style="cursor:hand" onclick="OnChangePeriod(0)" alt="오늘">
						<img src="images/btn_day07.gif" border=0 align="absmiddle" style="cursor:hand" onclick="OnChangePeriod(1)" alt="7일">
						<img src="images/btn_day15.gif" border=0 align="absmiddle" style="cursor:hand" onclick="OnChangePeriod(2)" alt="15일">
						<img src="images/btn_day30.gif" border=0 align="absmiddle" style="cursor:hand" onclick="OnChangePeriod(3)" alt="1개월"></a>
						<img src="images/btn_day60.gif" border=0 align="absmiddle" style="cursor:hand" onclick="OnChangePeriod(4)" alt="두달">
						<img src="images/btn_day.gif" border=0 align="absmiddle" style="cursor:hand" onclick="OnChangePeriod(5)" alt="전체">
					</td>
				</tr>
				<TR>
					<TD colspan="4" background="images/table_con_line.gif"><img src="images/table_con_line.gif" width="4" height="1" border="0"></TD>
				</TR>				
			</table>
		</td>
	</tr>
	<tr>
		<td height=10></td>
	</tr>
	<tr>
		<td align="center"><p><a href="javascript:CheckForm();"><img src="bankda/images/botteon_search.gif" border="0"></a><!--  <a href="./bankda/bank_match.php?ad=admin"><img src="bankda/images/botteon_matching.gif" border="0"></a> --></p></td>
	</tr>

	<tr>
		<td height="50"></td>
	</tr>
	<tr>
		<td>
			<table>
				<tr>
					<td>						
						
						<tr>
							<td style="padding-bottom:3pt;">
								<table cellpadding="0" cellspacing="0" width="100%">
									<tr>
										<td >
											<img src="images/icon_8a.gif" width="13" height="13" border="0"><B>정렬 :
											<A HREF="javascript:GoOrderby('<?if($orderby == "DESC") echo "ASC"; else echo "DESC";?>');"><B><FONT class=font_orange>입금일정<?if($orderby == "DESC") echo "↑"; else echo "↓";?></FONT></B></A>
										</td>
										<td  align="right"><img src="images/icon_8a.gif" width="13" height="13" border="0">총 : <B><?=number_format($listinfo['total'])?></B>건, &nbsp;&nbsp;<img src="images/icon_8a.gif" width="13" height="13" border="0">현재 <b><?=$listinfo['page']?>/<?=$listinfo['totalpage']?></b> 페이지</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td>
								<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0 style="table-layout:fixed">
									<col width=4%></col>
									<col width=5%></col>
									<col width=12%></col>
									<col width=12%></col>
									<col width=10%></col>
									<col width=11%></col>
									<col width=11%></col>
									<col width=11%></col>
									<col width=12%></col>
									<col width=11%></col>
									<input type=hidden name=chkordercode>
									<TR>
										<TD background="images/table_top_line.gif" width="761" colspan="10"></TD>
									</TR>
									<TR height=32>
										<TD class="table_cell5" align="center"><input type=checkbox id=allcheck name=allcheck onclick="selectAll();"></TD>
										<TD class="table_cell6" align="center">번호</TD>
										<TD class="table_cell6" align="center">입금완료일</TD>
										<TD class="table_cell6" align="center">계좌번호</TD>
										<TD class="table_cell6" align="center">은행명</TD>
										<TD class="table_cell6" align="center">입금금액</TD>
										<TD class="table_cell6" align="center">입금자명</TD>
										<TD class="table_cell6" align="center">현재상태</TD>
										<TD class="table_cell6" align="center">최종 매칭일</TD>
										<TD class="table_cell6" align="center">주문번호</TD>
									</TR>
									<TR>
										<TD colspan="10" background="images/table_con_line.gif"></TD>
									</TR>
									<?
									$linkstr = "javascript:GoPage('%u','".$_REQUEST['list_num']."')";
									$pageSet = array('page'=>$listinfo['page'],'total_page'=>$listinfo['totalpage'],'links'=>$linkstr,'pageblocks'=>$listinfo['list_num'],'style_pages'=>'%u', // 일반 페이지 
										'style_page_sep'=>'&nbsp;.&nbsp;');
									
									$Opage = new pages($pageSet);
									$Opage->_solv();
									
									if($listinfo['total'] < 1){ 
									?>
									<tr>
										<td colspan="10" style="padding:10; text-align:center">입금 확인 내역이 없습니다.</td>
									</tr>
									<? }else{
											$vno = $listinfo['total'] - ($listinfo['page']-1)*$listinfo['list_num'];
											foreach($listinfo['items'] as $item) {											
												$ordercode = !_empty($item['ordercode']) ? "<a href=\"javascript:OrderDetailView('$item[ordercode]')\"><font color=blue>".$item['ordercode']."</font></a>": "-";
									?>
									<TR height=32>
										<TD class="td_con1b" align="center"><input type="checkbox" name="Bkid[]" value="<?=$item['Bkid']?>"></TD>
										<TD class="td_con1" align="center"><?=$vno--?></TD>
										<TD class="td_con1" align="center"><?=$item['Bkdate']?></TD>
										<TD class="td_con1" align="center"><?=$item['Bkacctno']?></TD>
										<TD class="td_con1" align="center"><?=$item['Bkname']?></TD>
										<TD class="td_con1" align="center"><?=number_format($item['Bkinput'])?></TD>
										<TD class="td_con1" align="center"><?=$item['Bkjukyo']?></TD>
										<TD class="td_con1" align="center"><?=$bankda->_statusTxt($item['status'])?></TD>
										<TD class="td_con1" align="center"><?=$item['matchdate']?></TD>
										<TD class="td_con1" align="center"><?=$ordercode?></TD>
									</TR>
									<!-- <tr height=28 bgcolor=#FFFFFF><td colspan=10 align=center>조회된 내용이 없습니다.</td></tr> -->
									<TR>
										<TD background="images/table_top_line.gif" colspan="10"></TD>
									</TR>
									<?
											}
									} ?>
								</TABLE>
								<div style="text-align:center; margin-top:10px"><?=$Opage->_result('fulltext')?></div>
							</td>
						</tr>
						<tr>
							<td style="padding-top:4pt;"><a href="javascript:CheckDelete();"><img src="bankda/images/botteon_delete.gif" border="0"></a></td>
						</tr>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td height="40"></td>
	</tr>
	<tr>
		<td>
		<? /*
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
					<TD COLSPAN=3 width="100%" valign="top" bgcolor="white" style="padding-top:8pt; padding-bottom:8pt; padding-left:4pt;">
						<table cellpadding="0" cellspacing="0" width="100%">
							<tr>
								<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
								<td><span class="font_dotline">10분 주기로 자동 입금확인합니다.</span></td>
							</tr>
							<tr>
								<td width="20" align="right">&nbsp;</td>
								<td width="701" class="space_top">수동 입금확인의 경우 14일 이전 데이터를 검색해서 매칭합니다.</td>
							</tr>
							<tr>
								<td width="20" align="right">&nbsp;</td>
								<td width="701" class="space_top"><!-- - 은행정보 목록에 표기된 은행들만 등록가능 합니다.--></td>
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
			</TABLE>*/ ?>
		</td>
	</tr>
	<tr>
		<td height="50"></td>
	</tr>
</table>
</form>
<form name=detailform method="post" action="order_detail.php" target="orderdetail">
<input type="hidden" name="ordercode" value="" />
</form> 
<script type="text/javascript">
var chkbox = document.getElementsByName("Bkid[]");
function selectAll(){
	if(document.getElementById("allcheck").checked == true) {
		for(i = 0 ; i < chkbox.length ; i++)
		{ 
			chkbox[i].checked = true;
		}
	}else{
		cancelAll();
	}
}
function cancelAll(){
	for(i = 0 ; i < chkbox.length ; i++)
	{ 
		chkbox[i].checked = false;
	}
}
</script>
<?=$onload?>