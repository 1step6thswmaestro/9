<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
INCLUDE ("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "pr-4";
$MenuCode = "product";
if (!$_usersession->isAllowedTask($PageCode)) {
	INCLUDE ("AccessDeny.inc.php");
	exit;
}
#########################################################

include_once($Dir."lib/ext/product_func.php");

function getcsvdata($fields = array(), $delimiter = ',', $enclosure = '"') {
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
	return $str;
}

@set_time_limit(300);

###################################### 입점기능 사용권한 체크 #######################################
$usevender=setUseVender();

unset($venderlist);
if($usevender==true) {
	$sql = "SELECT vender,id,com_name FROM tblvenderinfo WHERE disabled=0 AND delflag='N' ";
	$result=mysql_query($sql,get_db_conn());
	while($row=mysql_fetch_object($result)) {
		$venderlist[$row->vender]=$row;
	}
	mysql_free_result($result);
}
#####################################################################################################

$mode=$_POST["mode"];
$vender=$_POST["vender"];
$code=$_POST["code"];

if($mode=="download") {
	
	if($usevender==true) {
		//입점업체 확인
		if(strlen($vender)>0 && $vender != "0") {
			if($vender == "shop") {
				$qry .= "AND p.vender = '0' ";
			} else if(strlen($venderlist[$vender]->vender)>0){
			 	$qry .= "AND p.vender = '".$vender."' ";
			}
		}
	}

	if($code>0) {
		$codeABCD = str_pad($code, 12, "0", STR_PAD_RIGHT);

		//분류 확인
		$sql = "SELECT type FROM tblproductcode ";
		$sql.= "WHERE codeA='".substr($codeABCD,0,3)."' AND codeB='".substr($codeABCD,3,3)."' ";
		$sql.= "AND codeC='".substr($codeABCD,6,3)."' AND codeD='".substr($codeABCD,9,3)."' ";
		$result=mysql_query($sql,get_db_conn());
		if(@mysql_num_rows($result)!=1) {
			echo "<html><head></head><body onload=\"alert('상품을 다운로드할 분류 선택이 잘못되었습니다.');location='".$_SERVER["PHP_SELF"]."'\"></body></html>";exit;
		}
		mysql_free_result($result);
		$qry .= "AND p.productcode LIKE '".$code."%' ";
	}

	if(strlen($qry)>0) {
		$qry = "WHERE".substr($qry,3);
	}

	$connect_ip = getenv("REMOTE_ADDR");
	$curdate = date("YmdHis");

	$sql = "SELECT COUNT(*) as cnt FROM tblproduct p ";
	$sql.= $qry;
	$result = mysql_query($sql,get_db_conn());
	$row = mysql_fetch_object($result);
	if ($row->cnt>=5000) {
		$temp = "삼일";
		$okdate = date("Ymd",mktime(0,0,0,date("m"),date("d")+3,date("Y")));
	} else {
		$temp = "하루";
		$okdate=date("Ymd");
	}
	mysql_free_result($result);

	$log_content = "## 상품 엑셀 다운로드 ## - 다운로드 ".$_ShopInfo->getId()." - 시간 : ".$curdate;
	ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);

	Header("Content-Disposition: attachment; filename=product_".date("Ymd").".csv");
	Header("Content-type: application/x-msexcel");

	$sql = "SELECT CONCAT(codeA,codeB,codeC,codeD) as code, type,code_name FROM tblproductcode ";
	$result = mysql_query($sql,get_db_conn());
	while ($row=mysql_fetch_object($result)) {
		$code_name[$row->code] = $row->code_name;
	}
	mysql_free_result($result);

	$sql = "SELECT bridx, brandname FROM tblproductbrand ";
	$result = mysql_query($sql,get_db_conn());
	while ($row=mysql_fetch_object($result)) {
		$brandname[$row->bridx] = $row->brandname;
	}
	mysql_free_result($result);

	$patten = array ("\r");
	$replace = array ("");

	

	//$field=array("1차카테고리","2차카테고리","3차카테고리","4차카테고리","상품코드","상품명","시중가격","판매가","구매가","도매가","제조사","원산지","브랜드","모델명","출시일","진열코드","적립금(률)","할인쿠폰불가","적립금적용불가","사은품불가","교환/환불불가","재고","큰이미지","보통이미지","작은이미지","선택사항1","선택1의가격","선택사항2","구분","등록일","상품진열여부","설명","");
	
	$field = array("1차카테고리","2차카테고리","3차카테고리","4차카테고리","상품코드","상품명","시중가격","판매가","구매가","도매가","제조사","원산지","브랜드","모델명","출시일","진열코드","적립금","재고","선택사항1","선택1의가격","선택사항2","상품진열여부","큰이미지","보통이미지","작은이미지","특이사항","설명","등록일","사용자스팩명1","사용자스팩1내용","사용자스팩명2","사용자스팩2내용","사용자스팩명3","사용자스팩3내용","사용자스팩명4","사용자스팩4내용","사용자스팩명5","사용자스팩5내용","할인쿠폰적용불가","적립금적용불가","사은품적용불가","반품/교환불가");
	
	$gosicnt = 0;
	$gositsql = "SELECT MAX( r.dcnt ) FROM ( SELECT COUNT( d.didx ) AS dcnt FROM  `tblproduct_detail` d INNER JOIN tblproduct p ON p.pridx = d.pridx ".$qry." GROUP BY d.pridx) r";
	if(false !== $gcntr = mysql_query($gositsql,get_db_conn())){
		$gosicnt = mysql_result($gcntr,0,0);
	}
	for($gc=0;$gc < $gosicnt;$gc++){
		array_push($field,'정보고시'.($gc+1).'번항목명','정보고시'.($gc+1).'번항목내용');
	}
	
	echo getcsvdata($field);
	
	$sql = "SELECT * FROM tblproduct p ";
	$sql.= $qry;
	$sql.= "ORDER BY p.productcode ";	
	$result = mysql_query($sql,get_db_conn());

	while ($row=mysql_fetch_object($result)) {
		unset($field);

		$codeA = substr($row->productcode,0,3);
		$codeB = substr($row->productcode,3,3);
		$codeC = substr($row->productcode,6,3);
		$codeD = substr($row->productcode,9,3);
		$code = substr($row->productcode,0,12);
		if($codeB=="000") $codeB="";
		if($codeC=="000") $codeC="";
		if($codeD=="000") $codeD="";
		$field[]=$code_name[$codeA."000000000"];
		if(strlen($code_name[$codeA.$codeB."000000"])==0) $field[]="2차카테고리없음";
		else $field[]=$code_name[$codeA.$codeB."000000"];
		if(strlen($code_name[$codeA.$codeB.$codeC."000"])==0) $field[]="3차카테고리없음";
		else $field[]=$code_name[$codeA.$codeB.$codeC."000"];
		if(strlen($code_name[$codeA.$codeB.$codeC.$codeD])==0) $field[]="4차카테고리없음";
		else $field[]=$code_name[$codeA.$codeB.$codeC.$codeD];

		$field[]='"'.$row->productcode.'"';
		$field[]=$row->productname;
		$field[]=$row->consumerprice;
		$field[]=$row->sellprice;
		$field[]=$row->buyprice;
		$field[]=$row->productdisprice;
		$field[]=$row->production;
		$field[]=$row->madein;
		$field[]=$brandname[$row->brand];
		$field[]=$row->model;
		$field[]=$row->opendate;
		$field[]=$row->selfcode;
		$field[]=($row->reservetype!="Y"?$row->reserve:$row->reserve."%");
		$field[] = (strlen($row->quantity)==0)?"무제한":$row->quantity;
		$field[]=str_replace(",","|",$row->option1);
		$field[]=str_replace(",","^",$row->option_price);
		$field[]=str_replace(",","|",$row->option2);
		$field[]=$row->display;
		$field[]=$row->maximage;
		$field[]=$row->minimage;
		$field[]=$row->tinyimage;
		$field[]=str_replace(",","",$row->addcode);
		$field[]=str_replace($patten,$replace,$row->content);
		$field[]=substr($row->date,0,8);
		
		$tmpspec = explode("=",$row->userspec);
		/*
		if(strlen(trim($field[$jj++])) >0 && strlen(trim($field[$jj])) > 0) array_push($userspecarr,$field[$jj-1]."".$field[$jj]);
		}	
		$userspec = (count($userspecarr) > 0)?implode("=",$userspecarr):'';
		*/
		for($sp=0;$sp<5;$sp++){
			$spname = $spval = '';
			if(isset($tmpspec[$sp]) && strlen(trim($tmpspec[$sp])) > 3){
				$tspec = explode("",$tmpspec[$sp]);
				if(strlen(trim($tspec[0])) > 0 &&  strlen(trim($tspec[1])) > 0){
					$spname = $tspec[0];
					$spval = $tspec[1];
				}
			}
			$field[]=$spname;
			$field[]=$spval;
		}
		
		$field[]=$row->etcapply_coupon;//할인쿠폰적용불가
		$field[]=$row->etcapply_reserve;//적립금적용불가
		$field[]=$row->etcapply_gift;//사은품적용불가
		$field[]=$row->etcapply_return;//교환/환불불가
		
		$gositmp = _getProductDetails($row->pridx);
		for($gc=0;$gc < $gosicnt;$gc++){
			if(is_array($gositmp[$gc]) && !_empty($gositmp[$gc]['dtitle'])){
				$field[]= $gositmp[$gc]['dtitle'];
				$field[]= $gositmp[$gc]['dcontent'];
			}else{
				$field[]= "";
				$field[]= "";
			}
		}
		echo getcsvdata($field);
		flush();
	}
	mysql_free_result($result);
	exit;
}

?>

<? INCLUDE "header.php"; ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function ACodeSendIt(f,obj) {
	if(obj.ctype=="X") {
		f.code.value = obj.value+"000000000";
	} else {
		f.code.value = obj.value;
	}

	burl = "product_exceldownload.ctgr.php?depth=2&code=" + obj.value;
	curl = "product_exceldownload.ctgr.php?depth=3";
	durl = "product_exceldownload.ctgr.php?depth=4";
	BCodeCtgr.location.href = burl;
	CCodeCtgr.location.href = curl;
	DCodeCtgr.location.href = durl;
}
function CheckForm() {
	document.form1.mode.value="download";
	document.form1.submit();
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
			<? include ("menu_product.php"); ?>
			</td>

			<td></td>
			<td valign="top">




<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td height="29" colspan="3">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 상품관리 &gt; 상품 일괄관리 &gt; <span class="2depth_select">상품 엑셀 다운로드</span></td>
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
					<TD><IMG SRC="images/product_exceldownload_title.gif" border="0"></TD>
					</tr><tr>
					<TD width="100%" background="images/title_bg.gif" height=21></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height="3"></td>
			</tr>
			<tr>
				<td style="padding-bottom:3pt;">
				<TABLE WIDTH="100%" BORDER="0" CELLPADDING="0" CELLSPACING="0">
				<TR>
					<TD><IMG SRC="images/distribute_01.gif"></TD>
					<TD COLSPAN="2" background="images/distribute_02.gif"></TD>
					<TD><IMG SRC="images/distribute_03.gif"></TD>
				</TR>
				<TR>
					<TD background="images/distribute_04.gif"></TD>
					<TD class="notice_blue"><IMG SRC="images/distribute_img.gif" ></TD>
					<TD width="100%" class="notice_blue">상품정보 Excel(.csv) 형식으로 다운로드할 수 있습니다.</TD>
					<TD background="images/distribute_07.gif"></TD>
				</TR>
				<TR>
					<TD><IMG SRC="images/distribute_08.gif"></TD>
					<TD COLSPAN="2" background="images/distribute_09.gif"></TD>
					<TD><IMG SRC="images/distribute_10.gif"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height="20"></td>
			</tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/product_exceldownload_stitle1.gif" border="0"></TD>
					<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif"></TD>
					<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height=3></td>
			</tr>

			<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post>
			<input type=hidden name=mode>
			<input type="hidden" name="code" value="">
			<tr>
				<td>
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<TD background="images/table_top_line.gif" colspan=2></TD>
				</TR>
				<?if($usevender==true) {?>

				<TR>
					<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">등록상품 입점사 선택</TD>
					<TD class="td_con1" >
					<select name=vender>
						<option value="0">쇼핑몰 전체</option>
						<option value="shop">쇼핑몰 본사</option>
						<?
						while(list($key,$val)=each($venderlist)) {
							echo "<option value=\"".$val->vender."\">".$val->id." (".$val->com_name.")</option>\n";
						}
						?>
					</select>
					<span class="font_orange">＊다운로드할 상품 입점사를 선택하세요.</span>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				
				<?}?>

				<TR>
					<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">상품 카테고리 선택</TD>
					<TD class="td_con1" >
					<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
					<col width=145></col>
					<col width=3></col>
					<col width=145></col>
					<col width=3></col>
					<col width=145></col>
					<col width=3></col>
					<col width=></col>
					<tr>
						<td>
						<select name="code1" style=width:145 onchange="ACodeSendIt(document.form1,this.options[this.selectedIndex])">
						<option value="">--- 대 분 류 전 체 ---</option>
<?
						$sql = "SELECT codeA,codeB,codeC,codeD,code_name,type FROM tblproductcode ";
						$sql.= "WHERE codeB='000' AND codeC='000' ";
						$sql.= "AND codeD='000' AND type LIKE 'L%' ORDER BY sequence DESC ";
						$result=mysql_query($sql,get_db_conn());
						while($row=mysql_fetch_object($result)) {
							$ctype=substr($row->type,-1);
							if($ctype!="X") $ctype="";
							echo "<option value=\"".$row->codeA."\" ctype='".$ctype."'>".$row->code_name."";
							if($ctype=="X") echo " (단일분류)";
							echo "</option>\n";
						}
						mysql_free_result($result);
?>
						</select>
						</td>
						<td></td>
						<td>
						<iframe name="BCodeCtgr" src="product_exceldownload.ctgr.php?depth=2" width="145" height="21" scrolling=no frameborder=no></iframe>
						</td>
						<td></td>
						<td><iframe name="CCodeCtgr" src="product_exceldownload.ctgr.php?depth=3" width="145" height="21" scrolling=no frameborder=no></iframe></td>
						<td></td>
						<td><iframe name="DCodeCtgr" src="product_exceldownload.ctgr.php?depth=4" width="145" height="21" scrolling=no frameborder=no></iframe></td>
					</tr>
					</table>
					</td>
				</TR>
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
				<td align="center"><img src="images/btn_filedown.gif" id="downloadButton" border="0" style="cursor:hand" onclick="CheckForm(document.form1);"></td>
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
						<td><span class="font_dotline">상품정보 엑셀 다운로드</span></td>
					</tr>
					<tr>
						<td align="right">&nbsp;</td>
						<td class="space_top" style="letter-spacing:-0.5pt;">
						- 상품정보 엑셀 다운로드 파일은 확장자 CSV 로 저장됩니다.
						</td>
					</tr>
					<tr>
						<td align="right">&nbsp;</td>
						<td class="space_top" style="letter-spacing:-0.5pt;">
						- 상품정보 엑셀 다운로드할 경우 등록상품 입점사 및 상품 카테고리 별로 선택하여 다운로드 가능합니다.
						</td>
					</tr>
					<tr>
						<td colspan="2" height="20"></td>
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