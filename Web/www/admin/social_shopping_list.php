<?
$code = $_REQUEST['code'];
if(strlen($code)==0) {
	//소셜코드 가져오기(카테고리 출력)
	$sql = "SELECT * FROM tblproductcode WHERE type like 'S%' Order by codeA,codeB,codeC,codeD limit 1 ";
	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		$code = $row->codeA.$row->codeB.$row->codeC.$row->codeD;
	}
	mysql_free_result($result);
}

if(strlen($code) == 0){
	echo "<script>alert('상품카테고리를 먼저 등록하세요');";
	echo "history.go(-1);</script>";exit;
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

$btnWrite="";
//상품등록버튼
$sql = "SELECT type, list_type FROM tblproductcode WHERE codeA='".substr($code,0,3)."' ";
$sql.= "AND codeB='".substr($code,3,3)."' ";
$sql.= "AND codeC='".substr($code,6,3)."' AND codeD='".substr($code,9,3)."' ";
$result=mysql_query($sql,get_db_conn());
$row=mysql_fetch_object($result);
mysql_free_result($result);
if(ereg("X",$row->type)){
	$btnWrite = "<a href=\"javascript:ProductSend('write','$code');\"><img src=images/product_newregicn.gif style=margin-bottom:10px;></a>";
}
//리스트 세팅
$setup[page_num] = 10;
$setup[list_num] = 10;

$sort=$_POST["sort"];
$block=$_POST["block"];
$gotopage=$_POST["gotopage"];

if ($block != "") {
	$nowblock = $block;
	$curpage  = $block * $setup[page_num] + $gotopage;
} else {
	$nowblock = 0;
}

if (($gotopage == "") || ($gotopage == 0)) {
	$gotopage = 1;
}

////////////////////////
$imagepath=$Dir.DataDir."shopimages/product/";

?>
<SCRIPT LANGUAGE="JavaScript">
<!--
function ACodeSendIt(f,obj) {
	if(obj.ctype=="X") {
		f.code.value = obj.value+"000000000";
	} else {
		f.code.value = obj.value;
	}

	burl = "social_shopping.ctgr.php?depth=2&code=" + obj.value;
	curl = "social_shopping.ctgr.php?depth=3";
	durl = "social_shopping.ctgr.php?depth=4";
	BCodeCtgr.location.href = burl;
	CCodeCtgr.location.href = curl;
	DCodeCtgr.location.href = durl;
	f.submit();
}
function ProductMouseOver(Obj) {
	obj = event.srcElement;
	WinObj=document.getElementById(Obj);
	obj._tid = setTimeout("ProductViewImage(WinObj)",200);
}
function ProductViewImage(WinObj) {
	WinObj.style.display = "";
	
	if(!WinObj.height)
		WinObj.height = WinObj.offsetTop;

	WinObjPY = WinObj.offsetParent.offsetHeight;
	WinObjST = WinObj.height-WinObj.offsetParent.scrollTop;
	WinObjSY = WinObjST+WinObj.offsetHeight;

	if(WinObjPY < WinObjSY)
		WinObj.style.top = WinObj.offsetParent.scrollTop-WinObj.offsetHeight+WinObjPY;
	else if(WinObjST < 0)
		WinObj.style.top = WinObj.offsetParent.scrollTop;
	else
		WinObj.style.top = WinObj.height;
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
	document.form1.submit();
}

function GoSort(sort) {
	document.form1.mode.value = "";
	document.form1.sort.value = sort;
	document.form1.block.value = "";
	document.form1.gotopage.value = "";
	document.form1.submit();
}

function ProductSend(mode,prcode) {
	document.form1.mode.value = mode;
	document.form1.code.value = prcode.substr(0,12);
	if(mode != "write"){
		document.form1.prcode.value = prcode;
	}
	document.form1.submit();
}

function sendMail(pcode){
	document.sendMailFrm.pcode.value=pcode;
	document.sendMailFrm.target="sendMail";
	window.open("about:blank","sendMail","width=760,height=600,scrollbars=yes,status=no");
	document.sendMailFrm.submit();
}

function sendSms(pcode){
	document.sendSmsFrm.pcode.value=pcode;
	document.sendSmsFrm.target="sendSms";
	window.open("about:blank","sendSms","width=500,height=400,scrollbars=yes,status=no");
	document.sendSmsFrm.submit();
}
//-->
</SCRIPT>
<table cellpadding="0" cellspacing="0" width="100%">
<tr><td height="8"></td></tr>
<tr>
	<td>
	<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
	<TR>
		<TD><IMG SRC="images/social_shopping_title.gif" ALT="공동구매 상품관리"></TD>
		</tr><tr>
		<TD width="100%" background="images/title_bg.gif" height="21"></TD>
	</TR>
	</TABLE>
	</td>
</tr>
<tr><td height=20></td></tr>
<tr>
	<td>
	<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
	<TR>
		<TD><IMG SRC="images/social_shopping_stitle1.gif"  ALT="공동구매 상품목록"></TD>
		<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif">&nbsp;</TD>
		<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></TD>
	</TR>
	</TABLE>
	</td>
</tr>
<tr><td height=3></td></tr>
<tr>
	<td>



<TABLE cellSpacing=0 cellPadding=0 width='100%'>
	<TR>
		<TD style='PADDING-BOTTOM: 4pt; PADDING-LEFT: 4pt; PADDING-RIGHT: 4pt; PADDING-TOP: 4pt' bgColor=#ededed width='100%'>
			<TABLE cellSpacing=0 cellPadding=0 width='100%' bgColor=white>
				<TR>
					<TD width='100%'>
						<TABLE border=0 cellSpacing=0 cellPadding=0 width='100%'>
<?
	// 1차 카테고리 출력
	$sql = "SELECT * FROM tblproductcode WHERE type like 'S%' AND codeB='000' AND codeC='000' AND codeD='000' Order by codeA ";
	$result=mysql_query($sql,get_db_conn());
	while($row=mysql_fetch_object($result)) {
		$ctype=substr($row->type,-1);
		$slastCate = ($ctype =="X")? "(단일카테고리)":"";
		if($codeA == $row->codeA)
			$codeA_list .= "<span style=\"line-height:24px;font-weight:bold;font-size:12px;color:black;padding-right:30px;\">".$row->code_name.$slastCate."</span> ";
		else
			$codeA_list .= "<span style=\"line-height:24px;font-weight:normal;font-size:12px;padding-right:30px;\"><a href=\"".$_SERVER[PHP_SELF]."?code=".$row->codeA."\">".$row->code_name.$slastCate."</a></span>";
	}
	mysql_free_result($result);
?>	
							<TR>
								<TD height=30 background='images/blueline_bg.gif' width='100%' align='center'><B><?=$codeA_list?></B></TD>
							</TR>
							<TR>
								<TD background='images/table_con_line.gif' width='100%'><IMG border=0 src='images/table_con_line.gif' width=4 height=1></TD>
							</TR>
							<TR>
								<TD style='PADDING-BOTTOM: 10pt; PADDING-LEFT: 10pt; PADDING-RIGHT: 10pt; PADDING-TOP: 10pt' width='100%'>
									<TABLE cellSpacing=0 cellPadding=0 width='100%'>
<?
	//2차카테고리출력
	if($_cdata->type!="SX") {
?>										
										<TR>
											<TD width='100%'>
<?
		$sql = "SELECT codeA,codeB,codeC,codeD,code_name,type FROM tblproductcode ";
		$sql.= "WHERE codeA='".$codeA."' AND codeB!='000' AND codeC='000' AND codeD='000' AND group_code!='NO' ";
		$sql.= "AND (type='SM' || type='SMX')";
		$sql.= "ORDER BY sequence DESC ";
		$result=mysql_query($sql,get_db_conn());
		$i =0;
		while($row=mysql_fetch_object($result)) {
			$ctype=substr($row->type,-1);
			$slastCate = ($ctype =="X")? "(단일카테고리)":"";
			if($i>0) echo " &nbsp; ";
			echo "<img src=/images/icon_point2.gif><a href=\"".$_SERVER[PHP_SELF]."?code=".$row->codeA.$row->codeB.$row->codeC.$row->codeD."\">".(($codeB == $row->codeB)?"<b>".$row->code_name."</b>":$row->code_name).$slastCate."</a>";
			$i++;
		}
		mysql_free_result($result);
?>
											
											
											</TD>
										</TR>
										<TR>
											<TD height=10 width='100%'></TD>
										</TR>
										<TR>
											<TD width='100%'>
											
<?
		$category_list="";
		if($_cdata->type!="SMX" || ($_cdata->type=="SMX" && $codeC !="000")) {
			$sql = "SELECT codeA,codeB,codeC,codeD,code_name,type FROM tblproductcode ";
			$sql.= "WHERE codeA='".$codeA."' AND codeB='".$codeB."' AND codeC!='000' AND codeD='000' AND group_code!='NO' ";
			$sql.= "AND (type='SM' || type='SMX')";
			$sql.= "ORDER BY sequence DESC ";
			$result=mysql_query($sql,get_db_conn());
			$category_list .="	<tr>\n";
			$category_list.="		<td style=\"border-top-width:1px; border-bottom-width:1px; border-top-color:rgb(238,238,238); border-bottom-color:rgb(238,238,238); border-top-style:solid; border-bottom-style:solid;\">\n";
			$category_list.="		<table cellSpacing=\"1\" bgcolor=\"#ffffff\" width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";		
			$i =0;
			while($row=mysql_fetch_object($result)) {
				$ctype=substr($row->type,-1);
				$slastCate = ($ctype =="X")? "(단일카테고리)":"";
				$category_list .="		<tr>\n";
				$category_list .="			<td style=\"padding:10px;text-align:left;\" bgcolor=\"#F8F8F8\" width=\"15%\"><a href=\"".$_SERVER[PHP_SELF]."?code=".$row->codeA.$row->codeB.$row->codeC.$row->codeD."\"><FONT class=upcodename>".(($codeC ==$row->codeC)?"<b>".$row->code_name."</b>":$row->code_name).$slastCate."</font></a></td>\n";
				if(!eregi("X",$row->type)) {
					$sql = "SELECT codeA,codeB,codeC,codeD,code_name,type FROM tblproductcode ";
					$sql.= "WHERE codeA='".$row->codeA."' AND codeB='".$row->codeB."' AND codeC='".$row->codeC."' AND codeD!='000' AND group_code!='NO' ";
					$sql.= "AND (type='SM' || type='SMX') ";
					$sql.= "ORDER BY sequence DESC ";
					$result2=mysql_query($sql,get_db_conn());
					$j=0;
					while($row2=mysql_fetch_object($result2)) {
						if($j == 0) $category_list .="			<td width=\"85%\"><img src=\"../img/productlist_t_icon.gif\" style=\"vertical-align:middle;\">";
						if($j>0) $category_list.="&nbsp;&nbsp;  &nbsp;&nbsp;";
						$category_list.="<a href=\"".$_SERVER[PHP_SELF]."?code=".$row2->codeA.$row2->codeB.$row2->codeC.$row2->codeD."\"><FONT class=subcodename>".(($codeC ==$row2->codeC && $codeD ==$row2->codeD)?"<b>".$row2->code_name."</b>":$row2->code_name)."</font></a>";
						$j++;
					}
					mysql_free_result($result2);
					if($j >0) $category_list .="			</td>\n";
				}
				$category_list .="		</tr>\n";
				$i++;
			}
			mysql_free_result($result);
			$category_list.="		</table>\n";
			$category_list.="		</td>\n";
			$category_list .="	</tr>\n";
		}
		echo $category_list;
	}
?>											

											</TD>
										</TR>
									</TABLE>
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

<!-- <tr>
	<td>
	<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post>
	<input type=hidden name=mode>
	<input type=hidden name=prcode>
	<input type=hidden name=code value="<?=$code?>">
	<input type=hidden name=sort value="<?=$sort?>">
	<input type=hidden name=block value="<?=$block?>">
	<input type=hidden name=gotopage value="<?=$gotopage?>">
	<input type=hidden name=keyword value="<?=$keyword?>">
	<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
	<col width=130></col>
	<col width=140></col>
	<col width=3></col>
	<col width=140></col>
	<col width=3></col>
	<col width=140></col>
	<col width=3></col>
	<col width=140></col>
	<col width=50></col>
	<tr>
		<td><b>상품카테고리 선택 : </b></td>
		<td>
		<select name="code1" style="width:140" onchange="ACodeSendIt(document.form1,this.options[this.selectedIndex])">
		<option value="">---- 대 분 류 ----</option>
<?
		$sql = "SELECT codeA,codeB,codeC,codeD,code_name,type FROM tblproductcode ";
		$sql.= "WHERE codeB='000' AND codeC='000' ";
		$sql.= "AND codeD='000' AND type LIKE 'S%' ORDER BY sequence DESC ";
		$result=mysql_query($sql,get_db_conn());
		while($row=mysql_fetch_object($result)) {
			$ctype=substr($row->type,-1);
			$selstr = ($row->codeA == $codeA)? "selected":"";
			if($ctype!="X") $ctype="";
			echo "<option value=\"".$row->codeA."\" ctype='".$ctype."' ".$selstr.">".$row->code_name."";
			if($ctype=="X") echo " (단일분류)";
			echo "</option>\n";
		}
		mysql_free_result($result);
?>
		</select>
		</td>
		<td></td>
		<td>
		<iframe name="BCodeCtgr" src="social_shopping.ctgr.php?depth=2&selcode=<?=$codeA.$codeB?>" width="140" height="21" scrolling=no frameborder=no></iframe>
		</td>
		<td></td>
		<td><iframe name="CCodeCtgr" src="social_shopping.ctgr.php?depth=3&selcode=<?=$codeA.$codeB.$codeC?>" width="140" height="21" scrolling=no frameborder=no></iframe></td>
		<td></td>
		<td><iframe name="DCodeCtgr" src="social_shopping.ctgr.php?depth=4&selcode=<?=$codeA.$codeB.$codeC.$codeD?>" width="140" height="21" scrolling=no frameborder=no></iframe></td>
		<td><input type="submit" value="검색"></td>
	</tr>
	</table>
	</td>
</tr> -->
<tr><td height=20></td></tr>
<tr><td>
	<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post>
	<input type=hidden name=mode>
	<input type=hidden name=prcode>
	<input type=hidden name=code value="<?=$code?>">
	<input type=hidden name=sort value="<?=$sort?>">
	<input type=hidden name=block value="<?=$block?>">
	<input type=hidden name=gotopage value="<?=$gotopage?>">
	<input type=hidden name=keyword value="<?=$keyword?>">
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td width="100%" style="text-align:center">
		 <div style="border:1px solid #DBDBDB;width:100%;padding:10px 0;margin-top:10px;margin-bottom:10px;">상품명 <input type="text" class=input name="keyword" value="<?=$keyword?>" style="vertical-align:middle;"> <input type="image" src="images/icon_search.gif" alt="검색" style="vertical-align:middle;"></div>
		</td>
	</tr>
	<tr>
		<td width="100%" style="text-align:right;font-size:15px;font-weight:bold;"><?=$btnWrite?>111</td>
	</tr>
	<tr>
		<td width="100%">
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td width="100%">
			<TABLE border="0" cellSpacing="0" cellPadding="0" width="100%" style="table-layout:fixed">
			<col width=40></col>
			<col width=50></col>
			<col width=></col>
			<col width=70></col>
			<col width=70></col>
			<col width=45></col>
			<col width=45></col>
			<col width=45></col>
			<col width=50></col>
			<TR>
				<TD colspan="9" background="images/table_top_line.gif"></TD>
			</TR>
			<TR align="center">
				<TD class="table_cell">No</TD>
				<TD class="table_cell1" colspan="2">상품명/진열코드/특이사항</TD>
				<TD class="table_cell1">판매기간</TD>
				<TD class="table_cell1">판매가격</TD>
				<TD class="table_cell1">수량</TD>
				<TD class="table_cell1">상태</TD>
				<TD class="table_cell1">수정</TD>
				<TD class="table_cell1">관리</TD>
			</TR>
			<TR>
				<TD height="1" colspan="9" background="images/table_top_line.gif"></TD>
			</TR>
<?
if($code){
	$sCondition = "AND productcode LIKE '".$likecode."%' ";
	if(strlen($keyword)>2) {
		$sCondition.= "AND productname LIKE '%".$keyword."%' ";
	}
	$sql0 = "SELECT COUNT(*) as t_count FROM tblproduct WHERE 1=1 ";
	$sql0.= $sCondition;
	$result = mysql_query($sql0,get_db_conn());
	$row = mysql_fetch_object($result);
	mysql_free_result($result);
	$t_count = $row->t_count;
	$pagecount = (($t_count - 1) / $setup[list_num]) + 1;

	$sql = "SELECT option_price, productcode,productname,production,sellprice,consumerprice, ";
	$sql.= "buyprice,quantity,reserve,reservetype,addcode,display,vender,tinyimage,selfcode,assembleuse, ";
	$sql.= "sell_startdate,sell_enddate ";
	$sql.= "FROM tblproduct P LEFT OUTER JOIN tblproduct_social S ON P.productcode = S.pcode ";
	$sql.= "WHERE 1=1  ";
	$sql.= $sCondition;
	/* 
	//정렬
	if ($sort=="price")				$sql.= "ORDER BY sellprice ";
	else if ($sort=="productname")	$sql.= "ORDER BY productname ";
	else							$sql.= "ORDER BY date DESC ";
	*/
	$sql.= "ORDER BY date DESC ";
	$sql.= "LIMIT " . ($setup[list_num] * ($gotopage - 1)) . ", " . $setup[list_num];
	$list_rs = mysql_query($sql,get_db_conn());

	$cnt=0;
	while($row=mysql_fetch_object($list_rs)) {
		$number = ($t_count-($setup[list_num] * ($gotopage-1))-$cnt);
		$cnt++;
		$start_date=date("Y-m-d H:i:s",$row->sell_startdate);
		$end_date=date("Y-m-d H:i:s",$row->sell_enddate);
		if($row->display=="Y")
		{
			
			if($row->sell_startdate <=time() && time() <= $row->sell_enddate){
				$sell_state = "<font color=\"#0000FF\">판매중</font>";
			}else{
				$sell_state = "<font color=\"#FF0000\">판매종료</font>";
			}
		}else{
			$sell_state = "<font color=\"#FF4C00\">보류중</font>";
		}
		
		echo "<tr>\n";
		echo "	<TD colspan=\"8\" background=\"images/table_con_line.gif\"></TD>\n";
		echo "</tr>\n";
		echo "<tr align=\"center\">\n";
		echo "	<TD class=\"td_con2\">".$number."</td>\n";
		echo "	<TD class=\"td_con1\">";
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
		echo "	<TD class=\"td_con1\" align=\"left\" style=\"word-break:break-all;\"><A HREF=\"javascript:ProductSend('modify','".$row->productcode."');\">".$row->productname.($row->selfcode?"-".$row->selfcode:"").($row->addcode?"-".$row->addcode:"")."</A>&nbsp;</td>\n";
		echo "	<TD class=\"td_con1\">".$start_date."<br/>".$end_date."</td>";
		echo "	<TD align=right class=\"td_con1\"><img src=\"images/won_icon.gif\" border=\"0\" style=\"margin-right:2px;\"><span class=\"font_orange\">".number_format($row->sellprice)."</span>".(($row->consumerprice >0)? "<br>".(100-intval($row->sellprice/$row->consumerprice*100))."%":"")."</TD>\n";
		echo "	<TD class=\"td_con1\">";
		if (strlen($row->quantity)==0) echo "무제한";
		else if ($row->quantity<=0) echo "<span class=\"font_orange\"><b>품절</b></span>";
		else echo $row->quantity;
		echo "	</TD>\n";
		echo "	<TD class=\"td_con1\">".$sell_state."</td>";
		echo "	<TD class=\"td_con1\"><a href=\"javascript:ProductSend('modify','".$row->productcode."');\"><img src=\"images/icon_edit2.gif\" border=\"0\"></a></td>\n";
		echo "	<TD class=\"td_con1\">";
		echo "<input type=\"button\" value=\"메일\" class=\"btnstyle\"  onclick=\"sendMail('".$row->productcode."')\"><br>";
		echo "<input type=\"button\" value=\"문자\" class=\"btnstyle\"  onclick=\"sendSms('".$row->productcode."')\">";
		echo "</td>";
		echo "</tr>\n";
	}
	mysql_free_result($list_rs);
}
if ($cnt==0) {
	echo "<tr><td class=\"td_con2\" colspan=\"9\" align=\"center\">등록된 상품이 없습니다.</td></tr>";
}
?>
			<TR>
				<TD height="1" colspan="9" background="images/table_top_line.gif"></TD>
			</TR>
			</TABLE>
			</td>
		</tr>
<?
		if($t_count > 0) {
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
	</table>
	</form>
</td>
</tr>
</table>
<form name=sendMailFrm action="social_product_mailsend.php" method=post>
<input type=hidden name=pcode>
</form>
<form name=sendSmsFrm action="social_product_smssend.php" method=post>
<input type=hidden name=pcode>
</form>