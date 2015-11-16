<?
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include ("access.php");

	//어느페이지에서 호출한건지 category, planning 가 있다
	//$page_mode = $_REQUEST["page_mode"];
	$page_mode = isset($_GET['page_mode'])?trim($_GET['page_mode']):"";
	//리스트 세팅
	$setup[page_num] = 10;
	$setup[list_num] = 5;

	$sort=$_REQUEST["sort"];
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
	

	$mode=$_GET["mode"];
	$code=$_GET["code"];
	$keyword=$_GET["keyword"];
	$sellprice_min = $_GET["sellprice_min"];
	$sellprice_max = $_GET["sellprice_max"];
	$brand = $_GET["brand"];
	//sks
	$regdate_begin = $_GET["regdate_begin"];
	$regdate_end = $_GET["regdate_end"];
	$mobile_display = $_GET["mobile_display"];
	$display = $_GET["display"];

	$search_field = $_GET["search_field"];
	$search_world = $_GET["search_word"];

	$prcode=$_REQUEST["prcode"];
	
	$sql = "SELECT vendercnt FROM tblshopcount ";
	$result=mysql_query($sql,get_db_conn());
	$row=mysql_fetch_object($result);
	$vendercnt=$row->vendercnt;
	mysql_free_result($result);

	if($vendercnt>0){
		$venderlist=array();
		$sql = "SELECT vender,id,com_name,delflag FROM tblvenderinfo ORDER BY id ASC ";
		$result=mysql_query($sql,get_db_conn());
		
		while($row=mysql_fetch_object($result)) {
			$venderlist[$row->vender]=$row;
		}
		mysql_free_result($result);
	}

	$imagepath=$Dir.DataDir."shopimages/product/";
?>

<? include "header.php"; ?>
<style>td {line-height:18pt;}</style>
<script type="text/javascript" src="lib.js.php"></script>
<script>var LH = new LH_create();</script>
<script for=window event=onload>LH.exec();</script>
<script>LH.add("parent_resizeIframe('ListFrame')");</script>
<script language="JavaScript">
	<!--
	var ProductInfoStop="";

	<?if($vendercnt>0){?>
		function viewVenderInfo(vender) {
			ProductInfoStop = "1";
			window.open("about:blank","vender_infopop","width=100,height=100,scrollbars=yes");
			document.vForm.vender.value=vender;
			document.vForm.target="vender_infopop";
			document.vForm.submit();
		}
	<?}?>

	function ProductMouseOver(Obj) {
		obj = event.srcElement;
		WinObj=document.getElementById(Obj);
		obj._tid = setTimeout("ProductViewImage(WinObj)",200);
	}
	function ProductViewImage(WinObj) {
		WinObj.style.display = "";

		if(!WinObj.height){
			WinObj.height = WinObj.offsetTop;
		}

		WinObjPY = WinObj.offsetParent.offsetHeight;
		WinObjST = WinObj.height-WinObj.offsetParent.scrollTop;
		WinObjSY = WinObjST+WinObj.offsetHeight;

		if(WinObjPY < WinObjSY){
			WinObj.style.top = WinObj.offsetParent.scrollTop-WinObj.offsetHeight+WinObjPY;
		}else if(WinObjST < 0){
			WinObj.style.top = WinObj.offsetParent.scrollTop;
		}else{
			WinObj.style.top = WinObj.height;
		}
	}

	function ProductMouseOut(Obj) {
		obj = event.srcElement;
		WinObj = document.getElementById(Obj);
		WinObj.style.display = "none";
		clearTimeout(obj._tid);
	}

	function GoPage(block,gotopage,sort) {
		document.form1.mode.value = "";
		document.form1.sort.value = sort;
		document.form1.block.value = block;
		document.form1.gotopage.value = gotopage;

		document.form1.page_mode.value = "<?=$_GET[page_mode]?>";
		document.form1.pm_idx.value = "<?=$_GET[pm_idx]?>";

		document.form1.submit();
	}

	function GoSort(sort) {
		document.form1.mode.value = "";
		document.form1.sort.value = sort;
		document.form1.block.value = "";
		document.form1.gotopage.value = "";
		document.form1.submit();
	}


	function SelectList(page_mode,idx){

		if(page_mode=="category") {		
			ifrm_ctrl.location.href="mobile_product_ctrl.php?mode=mobile_display_y&productcode="+idx;	
		} else if(page_mode=="planning") {	
			ifrm_ctrl.location.href="mobile_product_ctrl.php?mode=planning_write&pm_idx=<?=$_GET[pm_idx]?>&productcode="+idx;		
		}
	}

	function onMouseColor(argValue){
		if(document.form1.prcode.value != argValue){
			return true;
		}else{
			return false;
		}
	}

	function ProductInfo(prcode) {
		ProductInfoStop = "1";
		code=prcode.substring(0,12);
		popup="YES";
		document.form_reg.code.value=code;
		document.form_reg.prcode.value=prcode;
		document.form_reg.popup.value=popup;
		if (popup=="YES") {
			document.form_reg.action="product_register.add.php";
			document.form_reg.target="register";
			window.open("about:blank","register","width=820,height=700,scrollbars=yes,status=no");
		} else {
			document.form_reg.action="product_register.php";
			document.form_reg.target="";
		}
		document.form_reg.submit();
	}

	function DivDefaultReset(){
		if(!self.id){
			self.id = self.name;
			top.document.getElementById(self.id).style.height = top.document.getElementById(self.id).height;
		}
	}
	DivDefaultReset();
//-->
</script>
<!-- 선택/취소 시 사용되는 iframe  -->
<iframe name="ifrm_ctrl" width=0 height=0 frameborder=0 align=top scrolling="no" marginheight="0" marginwidth="0"></iframe>
<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%" style="table-layout:fixed">
	<tr>
		<td width="100%" bgcolor="#FFFFFF"><img src="images/product_mainlist_text.gif" border="0"></td>
	</tr>
	
	<tr>
		<td width="100%" height="100%" valign="top" style="BORDER:#FF8730 2px solid;padding-left:5px;padding-right:5px;">
			<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=get>
				<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="100%" style="padding-top:2pt; padding-bottom:2pt;" height="30">
							<b><span class="font_orange">* 정렬방법 :</span></b> <a href="javascript:GoSort('date');">진열순</a> | <a href="javascript:GoSort('productname');">상품명순</a> | <a href="javascript:GoSort('price');">가격순</a>
						</td>
					</tr>
					<tr>
						<td width="100%">
							<div style="width:100%;height:100%;overflow:hidden;">
								<table cellpadding="0" cellspacing="0" width="100%">
									<tr>
										<td width="100%">
											<table border="0" cellSpacing="0" cellPadding="0" width="100%" style="table-layout:fixed">
												<?
													$colspan=7;
													if($vendercnt>0) $colspan++;
												?>
												<col width=50></col>
												<?if($vendercnt>0){?>
													<col width=70></col>
												<?}?>
												<col width=50></col>
												<col width=></col>
												<col width=70></col>
												<col width=45></col>
												<col width=45></col>
												<col width=45></col>
												<tr>
													<td colspan="<?=$colspan?>" background="images/table_top_line.gif"></td>
												</tr>
												<tr align="center">
													<td class="table_cell">No</td>
												<?if($vendercnt>0){?>
													<td class="table_cell1">입점업체</td>
												<?}?>
													<td class="table_cell1" colspan="2">상품명/진열코드/특이사항</td>
													<td class="table_cell1">판매가격</td>
													<td class="table_cell1">수량</td>
													<td class="table_cell1">상태</td>
													<td class="table_cell1">수정</td>
												</tr>
												<?				
													if(strlen($code) || strlen($keyword) || strlen($search_world) || strlen($sellprice_min) || strlen($sellprice_max) || strlen($brand) || strlen($regdate_begin) || strlen($regdate_end)) {
														$page_numberic_type=1;
														$likecode.=substr($code,0,3);
														if(substr($code,3,3)!="000") {
															$likecode.=substr($code,3,3);
															if(substr($code,6,3)!="000") {
																$likecode.=substr($code,6,3);
																if(substr($code,9,3)!="000") {
																	$likecode.=substr($code,9,3);
																}
															}
														}
														$codeA=substr($code,0,3);
														$codeB=substr($code,3,3);
														$codeC=substr($code,6,3);
														$codeD=substr($code,9,3);
														$sql = "SELECT * FROM tblproductcode WHERE codeA='".$codeA."' AND codeB='".$codeB."' ";
														$sql.= "AND codeC='".$codeC."' AND codeD='".$codeD."' ";

														$result=mysql_query($sql,get_db_conn());
														$row=mysql_fetch_object($result);
														mysql_free_result($result);

														$qry = "WHERE a.productcode LIKE '".$likecode."%' ";
														
														if($search_field != "" && $search_world != ""){
															$qry .= "AND a.".$search_field." LIKE '%".$search_world."%' ";
														}
														
														//가격대
														if(strlen(trim($sellprice_min)) && strlen(trim($sellprice_max))){				
															$qry.= "AND (a.sellprice >= $sellprice_min AND a.sellprice <= $sellprice_max) ";	
														}else{
															if(strlen(trim($sellprice_min))){
																$qry.= "AND a.sellprice >= $sellprice_min";
															}else if(strlen(trim($sellprice_max))){
																$qry.= "AND a.sellprice <= $sellprice_max";
															}
															
														}

														//브랜드
														if(strlen($brand)){
															$qry.= "AND a.brand = '$brand'";
														}

														//등록일
														if(strlen($regdate_begin) && strlen($regdate_end)){				
															$qry.= "AND (substring(a.regdate,'1','10') >= '$regdate_begin' AND substring(a.regdate,'1','10') <= '$regdate_end') ";	
														}else{
															if(strlen($regdate_begin)) {
																$qry.= "AND substring(a.regdate,'1','10') >= '$regdate_begin'";
															}else if(strlen($regdate_end)) {
																$qry.= "AND substring(a.regdate,'1','10') <= '$regdate_end'";
															}					
														}				
														//모바일상세설명

														//상품출력설정에 따른
														if(strlen($display)) {
															$qry.= "AND a.display = '$display'";
															}else{

																$sql.= "AND a.display = 'Y' ";

															}


															//모바일에 상품출력 설정에 따른
														/*if(strlen($mobile_display))	{
															$qry.= "AND a.mobile_display = '$mobile_display'";
														}*/
														if($page_mode=="planning")	{
															$qry.= "AND a.mobile_display = 'Y' ";
														}
														
														if($row && ereg("X",$row->type)){
															//카테고리 중복선택
															$qry .= "AND b.categorycode='".$code."'";
															if(strlen($keyword)>2) {
																$qry.= "AND a.productname LIKE '%".$keyword."%' ";
															}
															$sql0 = "SELECT COUNT(*) as t_count ";
															$sql0.= "FROM tblproduct AS a LEFT OUTER JOIN tblcategorycode AS b ON a.productcode=b.productcode ";
															$sql0.= $qry;
															$result = mysql_query($sql0,get_db_conn());
															$row = mysql_fetch_object($result);
															mysql_free_result($result);
															$t_count = $row->t_count;
															$pagecount = (($t_count - 1) / $setup[list_num]) + 1;
														
														
														/*$sql = "SELECT option_price, productcode,productname,production,sellprice,consumerprice, ";
														$sql.= "buyprice,quantity,reserve,reservetype,addcode,display,vender,tinyimage,selfcode,assembleuse,mobile_display ";
														$sql.= "FROM tblproduct ";*/
														
														$sql = "SELECT a.option_price, a.productcode,a.productname,a.production,a.sellprice,a.consumerprice, ";
														$sql.= "a.buyprice,a.quantity,a.reserve,a.reservetype,a.addcode,a.display,a.vender,a.tinyimage,a.selfcode,a.assembleuse,a.mobile_display ";
														$sql.= "FROM tblproduct AS a LEFT OUTER JOIN tblcategorycode AS b ON a.productcode=b.productcode ";
														$sql.= $qry." ";
														
														if ($sort=="price")	{
															$sql.= "ORDER BY a.sellprice ";
														} else if ($sort=="productname") {
															$sql.= "ORDER BY a.productname ";
														} else {
															$sql.= "ORDER BY a.date DESC ";
														}
														
														$sql.= "LIMIT " . ($setup[list_num] * ($gotopage - 1)) . ", " . $setup[list_num];
														$result = mysql_query($sql,get_db_conn());
														$cnt=0;
														
														while($row=mysql_fetch_object($result)) {
															$number = ($t_count-($setup[list_num] * ($gotopage-1))-$cnt);
															$cnt++;
															
															if($page_mode=="category" && $row->mobile_display=="Y"){	
																$str_m_d = "[<font color='#ff6600'>모바일샵에 출력중</font>]<br />";	
																$color_m_d = "bgcolor='#d5d5d5'";
															}else {	
																$str_m_d = "";
																$color_m_d = "";
															}

															echo "<tr>\n";
															echo "	<td colspan=\"".$colspan."\" background=\"images/table_con_line.gif\"></td>\n";
															echo "</tr>\n";
															echo "<tr align=\"center\"> \n";
															echo "	<td class=\"td_con2\">".$number."</td>\n";
															
															if($vendercnt>0) {
																echo "	<td class=\"td_con1\"><B>".(strlen($venderlist[$row->vender]->vender)>0?"<span onclick=\"viewVenderInfo(".$row->vender.")\">".$venderlist[$row->vender]->id."</span>":"-")."</B></td>\n";
															}

															echo "	<td class=\"td_con1\">";
															
															if (strlen($row->tinyimage)>0 && file_exists($imagepath.$row->tinyimage)==true){
																echo "<img src='".$imagepath.$row->tinyimage."' height=40 width=40 border=1 onMouseOver=\"ProductMouseOver('primage".$cnt."')\" onMouseOut=\"ProductMouseOut('primage".$cnt."');\">";
															} else {
																echo "<img src=images/space01.gif onMouseOver=\"ProductMouseOver('primage".$cnt."')\" onMouseOut=\"ProductMouseOut('primage".$cnt."');\">";
															}
															
															echo "<div id=\"primage".$cnt."\" style=\"position:absolute; z-index:100; display:none;\"><table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"170\">\n";
															echo "		<tr bgcolor=\"#FFFFFF\">\n";
															
															if (strlen($row->tinyimage)>0 && file_exists($imagepath.$row->tinyimage)==true){
																echo "		<td align=\"center\" width=\"100%\" height=\"150\" style=\"border:#000000 solid 1px;\"><img src=\"".$imagepath.$row->tinyimage."\" border=\"0\"></td>\n";
															} else {
																echo "		<td align=\"center\" width=\"100%\" height=\"150\" style=\"border:#000000 solid 1px;\"><img src=\"".$Dir."images/product_noimg.gif\" border=\"0\"></td>\n";
															}

															echo "		</tr>\n";
															echo "		</table>\n";
															echo "		</div>\n";
															echo "	</td>\n";
															echo "	<td class=\"td_con1\" id=\"pidx_".$row->productcode."\" onclick=\"SelectList('$page_mode','".$row->productcode."')\" onmouseover=\"if(onMouseColor('".$row->productcode."'))this.style.backgroundColor='#F4F7FC';\" onmouseout=\"if(onMouseColor('".$row->productcode."'))this.style.backgroundColor='';\" style=\"cursor:hand;\" align=\"left\" style=\"word-break:break-all;\">$str_m_d
															<img src=\"images/producttype".($row->assembleuse=="Y"?"y":"n").".gif\" border=\"0\" align=\"absmiddle\" hspace=\"2\">".$row->productname.($row->selfcode?"-".$row->selfcode:"").($row->addcode?"-".$row->addcode:"")."&nbsp;</td>\n";
															echo "	<td align=right class=\"td_con1\"><img src=\"images/won_icon.gif\" border=\"0\" style=\"margin-right:2px;\"><span class=\"font_orange\">".number_format($row->sellprice)."</span><br><img src=\"images/reserve_icon.gif\" border=\"0\" style=\"margin-right:2px;\">".($row->reservetype!="Y"?number_format($row->reserve):$row->reserve."%")."</td>\n";
															echo "	<td class=\"td_con1\">";
															
															if (strlen($row->quantity)==0) {
																echo "무제한";
															} else if ($row->quantity<=0) {
																echo "<span class=\"font_orange\"><b>품절</b></span>";
															} else {
																echo $row->quantity;
															}
															echo "	</td>\n";
															echo "	<td class=\"td_con1\">".($row->display=="Y"?"<font color=\"#0000FF\">판매중</font>":"<font color=\"#FF4C00\">보류중</font>")."</td>";
															echo "	<td class=\"td_con1\"><a href=\"javascript:ProductInfo('".$row->productcode."');\"><img src=\"images/icon_newwin1.gif\" border=\"0\"></a></td>\n";
															echo "</tr>\n";
														}

														mysql_free_result($result);
															}
														if ($cnt==0) {
															$page_numberic_type="";
															echo "<tr><td colspan=\"".$colspan."\" background=\"images/table_con_line.gif\"></td></tr><tr><td class=\"td_con2\" colspan=\"".$colspan."\" align=\"center\">검색된 상품이 없습니다.</td></tr>";
														}
													} else {
														$page_numberic_type="";
														echo "<tr><td colspan=\"".$colspan."\" background=\"images/table_con_line.gif\"></td></tr><tr><td class=\"td_con2\" colspan=\"".$colspan."\" align=\"center\">검색된 상품이 없습니다.</td></tr>";
													}
												?>
												<tr>
													<td height="1" colspan="<?=$colspan?>" background="images/table_top_line.gif"></td>
												</tr>
											</table>
										</td>
									</tr>
									<?
										if($page_numberic_type) {
											$total_block = intval($pagecount / $setup[page_num]);

											if (($pagecount % $setup[page_num]) > 0) {
												$total_block = $total_block + 1;
											}

											$total_block = $total_block - 1;

											if (ceil($t_count/$setup[list_num]) > 0) {
												
												// 이전	x개 출력하는 부분-시작
												$a_first_block = "";
												if ($nowblock > 0) {
													$a_first_block .= "<a href=\"javascript:GoPage(0,1,'".$sort."');\" onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='첫 페이지';return true\"><IMG src=\"images/icon_first.gif\" border=0 align=\"absmiddle\"></a>&nbsp;&nbsp;";

													$prev_page_exists = true;
												}

												$a_prev_page = "";
												if ($nowblock > 0) {
													$a_prev_page .= "<a href=\"javascript:GoPage(".($nowblock-1).",".($setup[page_num]*($block-1)+$setup[page_num]).",'".$sort."');\" onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='이전 ".$setup[page_num]." 페이지';return true\">[prev]</a>&nbsp;&nbsp;";

													$a_prev_page = $a_first_block.$a_prev_page;
												}

												// 일반 블럭에서의 페이지 표시부분-시작

												if (intval($total_block) <> intval($nowblock)) {
													$print_page = "";
													for ($gopage = 1; $gopage <= $setup[page_num]; $gopage++) {
														if ((intval($nowblock*$setup[page_num]) + $gopage) == intval($gotopage)) {
															$print_page .= "<span class=font_orange2><B>[".(intval($nowblock*$setup[page_num]) + $gopage)."]</B></span> ";
														} else {
															$print_page .= "<a href=\"javascript:GoPage(".$nowblock.",".(intval($nowblock*$setup[page_num]) + $gopage).",'".$sort."');\" onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$setup[page_num]) + $gopage)."';return true\">[".(intval($nowblock*$setup[page_num]) + $gopage)."]</a> ";
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
															$print_page .= "<a href=\"javascript:GoPage(".$nowblock.",".(intval($nowblock*$setup[page_num]) + $gopage).",'".$sort."');\" onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$setup[page_num]) + $gopage)."';return true\">[".(intval($nowblock*$setup[page_num]) + $gopage)."]</a> ";
														}
													}
												}		// 마지막 블럭에서의 표시부분-끝

												$a_last_block = "";
												if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
													$last_block = ceil($t_count/($setup[list_num]*$setup[page_num])) - 1;
													$last_gotopage = ceil($t_count/$setup[list_num]);

													$a_last_block .= "&nbsp;&nbsp;<a href=\"javascript:GoPage(".$last_block.",".$last_gotopage.",'".$sort."');\" onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='마지막 페이지';return true\"><IMG src=\"images/icon_last.gif\" border=0 align=\"absmiddle\" width=\"17\" height=\"14\"></a>";

													$next_page_exists = true;
												}

												// 다음 10개 처리부분...

												$a_next_page = "";
												if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
													$a_next_page .= "&nbsp;&nbsp;<a href=\"javascript:GoPage(".($nowblock+1).",".($setup[page_num]*($nowblock+1)+1).",'".$sort."');\" onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='다음 ".$setup[page_num]." 페이지';return true\">[next]</a>";

													$a_next_page = $a_next_page.$a_last_block;
												}
											} else {
												$print_page = "<B>[1]</B>";
											}

											echo "<tr>\n";
											echo "	<td height=\"52\" align=center background=\"images/blueline_bg.gif\">\n";
											echo "	".$a_div_prev_page.$a_prev_page.$print_page.$a_next_page.$a_div_next_page."\n";
											echo "	</td>\n";
											echo "</tr>\n";
										}
									?>
									<tr>
										<td style="padding-top:12px;BORDER-top:#eeeeee 2px solid;"><img width="0" height="0"></td>
									</tr>
								</table>
							</div>
						</td>
					</tr>
				</table>
				<input type=hidden name=mode>
				<input type=hidden name=code value="<?=$code?>">
				<input type=hidden name=sort value="<?=$sort?>">
				<input type=hidden name=block value="<?=$block?>">
				<input type=hidden name=gotopage value="<?=$gotopage?>">
				<input type=hidden name=keyword value="<?=$keyword?>">
				<input type=hidden name=prcode value="">
				<!-- sks -->
				<input type=hidden name=search_field value="<?=$search_field?>">
				<input type=hidden name=search_word value="<?=$search_word?>">
				<input type=hidden name=price_max value="<?=$price_max?>">
				<input type=hidden name=price_min value="<?=$price_min?>">
				<input type=hidden name=price_max value="<?=$price_max?>">
				<input type=hidden name=brand value="<?=$brand?>">
				<input type=hidden name=regdate_begin value="<?=$regdate_begin?>">
				<input type=hidden name=regdate_end value="<?=$regdate_end?>">
				<input type=hidden name=mobile_content value="<?=$mobile_content?>">
				<input type=hidden name=display value="<?=$display?>">
				<input type=hidden name=mobile_display value="<?=$mobile_display?>">
				<input type=hidden name=page_mode value="<?=$_GET[page_mode]?>">
				<input type=hidden name=pm_idx value="<?=$_GET[pm_idx]?>">
			</form>
		</td>
	</tr>
</table>

<?if($vendercnt>0){?>
	<form name=vForm action="vender_infopop.php" method=post>
		<input type=hidden name=vender>
	</form>
<?}?>

<form name=form_reg action="product_register.php" method=post>
	<input type=hidden name=code>
	<input type=hidden name=prcode>
	<input type=hidden name=popup>
</form>
</body>
</html>