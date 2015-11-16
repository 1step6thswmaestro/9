<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/venderlib.php");
INCLUDE ("access.php");
include_once($Dir."lib/admin_more.php");

$mode=$_POST["mode"];
$prcodes=$_POST["prcodes"];
$display=$_POST["display"];

// 정산 기준 조회 jdy
$shop_more_info = getShopMoreInfo();
$account_rule = $shop_more_info['account_rule'];

$savewideimage = $Dir.DataDir."shopimages/wideimage/";
$vender_more = getVenderMoreInfo($_VenderInfo->getVidx());
$commission_type = $vender_more['commission_type'];

// 정산 기준 조회 jdy

if($mode=="display" && strlen($prcodes)>0 && ($display=="Y" || $display=="N")) {
	if(substr($_venderdata->grant_product,1,1)!="Y") {
		echo "<html></head><body onload=\"alert('상품정보 수정 권한이 없습니다.\\n\\n쇼핑몰에 문의하시기 바랍니다.')\"></body></html>";exit;
	} else if(substr($_venderdata->grant_product,3,1)!="N") {
		echo "<html></head><body onload=\"alert('쇼핑몰 운영자만 상품진열 수정이 가능합니다.\\n\\n쇼핑몰에 문의하시기 바랍니다.')\"></body></html>";exit;
	}

	$prcodes=substr($prcodes,0,-1);
	$prcodelist=ereg_replace(',','\',\'',$prcodes);

	if ($display=="Y") {
		$prcodelist_s = explode( ",", $prcodes);

		$i=0;
		while ($prcodelist_s[$i]){

			//수수료 승인이 나지 않은 상태에서 노출 불가 jdy
			if ($account_rule=="1" || $commission_type=="1") {
				$p_sql = "select first_approval from product_commission where productcode='".$prcodelist_s[$i]."'" ;

				$result=mysql_query($p_sql,get_db_conn());
				$data=mysql_fetch_array($result);

				if ($data[0] != "1") {
					echo "<html></head><body onload=\"alert('아직 수수료가 결정되지 않아 진열할 수 없는상품이 있습니다..')\"></body></html>";exit;
				}
			}
			$i++;
		}
	}

	$sql = "UPDATE tblproduct SET display='".$display."' ";
	$sql.= "WHERE productcode IN ('".$prcodelist."') ";
	$sql.= "AND vender='".$_VenderInfo->getVidx()."' ";

	if(mysql_query($sql,get_db_conn())) {
		$sql = "SELECT COUNT(*) as prdt_allcnt, COUNT(IF(display='Y',1,NULL)) as prdt_cnt FROM tblproduct ";
		$sql.= "WHERE vender='".$_VenderInfo->getVidx()."' ";
		$result=mysql_query($sql,get_db_conn());
		$row=mysql_fetch_object($result);
		$prdt_allcnt=(int)$row->prdt_allcnt;
		$prdt_cnt=(int)$row->prdt_cnt;
		mysql_free_result($result);

		$sql = "UPDATE tblvenderstorecount SET prdt_allcnt='".$prdt_allcnt."', prdt_cnt='".$prdt_cnt."' ";
		$sql.= "WHERE vender='".$_VenderInfo->getVidx()."' ";
		mysql_query($sql,get_db_conn());

		echo "<html></head><body onload=\"alert('요청하신 작업이 성공하였습니다.');parent.pageForm.submit();\"></body></html>";exit;
	} else {
		echo "<html></head><body onload=\"alert('요청하신 작업중 오류가 발생하였습니다.')\"></body></html>";exit;
	}
} else if($mode=="delete" && strlen($prcodes)>0) {

	if(substr($_venderdata->grant_product,2,1)!="Y") {
		echo "<html></head><body onload=\"alert('상품 삭제권한이 없습니다.\\n\\n쇼핑몰에 문의하시기 바랍니다.')\"></body></html>";exit;
	}

	unset($_deldata);
	$prcodes=substr($prcodes,0,-1);
	$prcodelist=ereg_replace(',','\',\'',$prcodes);

	$prcodes="";
	$sql = "SELECT productcode, productname, maximage, minimage, tinyimage, display,pridx,assembleuse,assembleproduct,content FROM tblproduct ";
	$sql.= "WHERE productcode IN ('".$prcodelist."') ";
	$sql.= "AND vender='".$_VenderInfo->getVidx()."' ";
	$result=mysql_query($sql,get_db_conn());
	while($row=mysql_fetch_object($result)) {
		$_deldata[]=$row;
		$prcodes.=$row->productcode.",";
	}
	mysql_free_result($result);

	if(count($_deldata)>0) {
		$prcodes=substr($prcodes,0,-1);
		$prcodelist=ereg_replace(',','\',\'',$prcodes);

		$sql = "DELETE FROM tblproduct WHERE productcode IN ('".$prcodelist."') ";
		$sql.= "AND vender='".$_VenderInfo->getVidx()."' ";
		if(mysql_query($sql,get_db_conn())) {
			//상품 삭제로 인한 관련 데이터 삭제처리

			#태그관련 지우기
			$sql = "DELETE FROM tbltagproduct WHERE productcode IN ('".$prcodelist."') ";
			mysql_query($sql,get_db_conn());

			#리뷰 지우기
			$sql = "DELETE FROM tblproductreview WHERE productcode IN ('".$prcodelist."') ";
			mysql_query($sql,get_db_conn());

			#위시리스트 지우기
			$sql = "DELETE FROM tblwishlist WHERE productcode IN ('".$prcodelist."') ";
			mysql_query($sql,get_db_conn());

			#관련상품 지우기
			$sql = "DELETE FROM tblcollection WHERE productcode IN ('".$prcodelist."') ";
			mysql_query($sql,get_db_conn());

			#테마상품 지우기
			$sql = "DELETE FROM tblproducttheme WHERE productcode IN ('".$prcodelist."') ";
			mysql_query($sql,get_db_conn());

			#상품접근권한 지우기
			$sql = "DELETE FROM tblproductgroupcode WHERE productcode IN ('".$prcodelist."')";
			mysql_query($sql,get_db_conn());

			/* 추가 수수료 테이블 내용 삭제 jdy */
			$sql = "DELETE FROM product_commission WHERE productcode IN ('".$prcodelist."') ";
			mysql_query($sql,get_db_conn());
			/* 추가 수수료 테이블 내용 삭제 jdy */


			//미니샵 테마코드에 등록된 상품 삭제
			$sql = "DELETE FROM tblvenderthemeproduct WHERE vender='".$_VenderInfo->getVidx()."' ";
			$sql.= "AND productcode IN ('".$prcodelist."') ";
			mysql_query($sql,get_db_conn());

			//미니샵 상품수 업데이트 (진열된 상품만)
			$sql = "SELECT COUNT(*) as prdt_allcnt, COUNT(IF(display='Y',1,NULL)) as prdt_cnt FROM tblproduct ";
			$sql.= "WHERE vender='".$_VenderInfo->getVidx()."' ";
			$result=mysql_query($sql,get_db_conn());
			$row=mysql_fetch_object($result);
			$prdt_allcnt=(int)$row->prdt_allcnt;
			$prdt_cnt=(int)$row->prdt_cnt;
			mysql_free_result($result);

			$sql = "UPDATE tblvenderstorecount SET prdt_allcnt='".$prdt_allcnt."', prdt_cnt='".$prdt_cnt."' ";
			$sql.= "WHERE vender='".$_VenderInfo->getVidx()."' ";
			mysql_query($sql,get_db_conn());

			$tmpcodeA=array();
			$arrprcode=explode(",",$prcodes);
			for($j=0;$j<count($arrprcode);$j++) {
				$tmpcodeA[substr($arrprcode[$j],0,3)]=true;
			}

			if(count($tmpcodeA)>0) {
				$sql = "SELECT SUBSTRING(productcode,1,3) as codeA FROM tblproduct ";
				$sql.= "WHERE ( ";
				$arr_codeA=$tmpcodeA;
				$i=0;
				while(list($key,$val)=each($arr_codeA)) {
					if(strlen($key)==3) {
						if($i>0) $sql.= "OR ";
						$sql.= "productcode LIKE '".$key."%' ";
						$i++;
					}
				}
				$sql.= ") ";
				$sql.= "AND vender='".$_VenderInfo->getVidx()."' ";
				$sql.= "GROUP BY codeA ";
				$result=mysql_query($sql,get_db_conn());
				while($row=mysql_fetch_object($result)) {
					unset($tmpcodeA[$row->codeA]);
				}
				mysql_free_result($result);

				if(count($tmpcodeA)>0) {
					$str_codeA="";
					while(list($key,$val)=each($tmpcodeA)) {
						$str_codeA.=$key.",";

						$imagename=$Dir.DataDir."shopimages/vender/".$_VenderInfo->getVidx()."_CODE10_".$key.".gif";
						@unlink($imagename);
					}
					$str_codeA=substr($str_codeA,0,-1);
					$str_codeA=ereg_replace(',','\',\'',$str_codeA);
					$sql = "DELETE FROM tblvendercodedesign WHERE vender='".$_VenderInfo->getVidx()."' ";
					$sql.= "AND code IN ('".$str_codeA."') AND tgbn='10' ";
					mysql_query($sql,get_db_conn());
				}
			}

			#상품이미지 삭제
			$imagepath=$Dir.DataDir."shopimages/product/";
			$update_ymd = date("YmdH");
			$update_ymd2 = date("is");
			for($i=0;$i<count($_deldata);$i++) {
				/** 에디터 관련 파일 처리 추가 부분 */
				if(preg_match_all('/\/data\/editor\/([a-zA-Z0-9\.]+)/',$_deldata[$i]->content,$edimg)){
					foreach($edimg[1] as $timg){
						@unlink($_SERVER['DOCUMENT_ROOT'].'/data/editor/'.$timg);
					}
				}
				/** #에디터 관련 파일 처리 추가 부분 */

				if(strlen($_deldata[$i]->assembleproduct)>0) {
					$sql = "SELECT productcode, assemble_pridx FROM tblassembleproduct ";
					$sql.= "WHERE productcode IN ('".str_replace(",","','",$_deldata[$i]->assembleproduct)."') ";
					$result = mysql_query($sql,get_db_conn());
					while($row = @mysql_fetch_object($result)) {
						$sql = "SELECT SUM(sellprice) as sumprice FROM tblproduct ";
						$sql.= "WHERE pridx IN ('".str_replace("","','",$row->assemble_pridx)."') ";
						$sql.= "AND display ='Y' ";
						$sql.= "AND assembleuse!='Y' ";
						$result2 = mysql_query($sql,get_db_conn());
						if($row2 = @mysql_fetch_object($result2)) {
							$sql = "UPDATE tblproduct SET sellprice='".$row2->sumprice."' ";
							$sql.= "WHERE productcode = '".$row->productcode."' ";
							$sql.= "AND assembleuse='Y' ";
							mysql_query($sql,get_db_conn());
						}
						mysql_free_result($result2);
					}
				}

				$sql = "UPDATE tblassembleproduct SET ";
				$sql.= "assemble_pridx=REPLACE(assemble_pridx,'".$_deldata[$i]->pridx."',''), ";
				$sql.= "assemble_list=REPLACE(assemble_list,',".$_deldata[$i]->pridx."','') ";
				mysql_query($sql,get_db_conn());

				unset($vimagear);
				$vimagear=array(&$vimage,&$vimage2,&$vimage3);
				$vimage=$_deldata[$i]->maximage;
				$vimage2=$_deldata[$i]->minimage;
				$vimage3=$_deldata[$i]->tinyimage;

				for($y=0;$y<3;$y++){
					if(strlen($vimagear[$y])>0 && file_exists($imagepath.$vimagear[$y]))
						unlink($imagepath.$vimagear[$y]);
				}
				@delProductMultiImg("prdelete","",$_deldata[$i]->productcode);
				$wideimage="";
				$wideimage=$savewideimage.$_deldata[$i]->productcode."*";

				@proc_matchfiledel($wideimage);
				$log_content = "## 상품삭제 ## - 상품코드 ".$_deldata[$i]->productcode." - 상품명 : ".urldecode($_deldata[$i]->productname)." ".$_deldata[$i]->display."";
				$_VenderInfo->ShopVenderLog($_VenderInfo->getVidx(),$connect_ip,$log_content,$update_date);
				$update_ymd2++;
			}

			echo "<html></head><body onload=\"alert('요청하신 작업이 성공하였습니다.');parent.pageForm.submit();\"></body></html>";exit;
		} else {
			echo "<html></head><body onload=\"alert('요청하신 작업중 오류가 발생하였습니다.')\"></body></html>";exit;
		}
	} else {
		echo "<html></head><body onload=\"alert('삭제할 상품이 존재하지 않습니다.');parent.pageForm.submit();\"></body></html>";exit;
	}
}

$code=$_POST["code"];
$disptype=$_POST["disptype"];
$s_check=$_POST["s_check"];
if(strlen($s_check)==0) $s_check="name";
$search=ltrim($_POST["search"]);
$sort=$_POST["sort"];
if($sort!="order by productname asc" && $sort!="order by productname desc" && $sort!="order by productcode asc" && $sort!="order by productcode desc" && $sort!="order by sellprice asc" && $sort!="order by sellprice desc" && $sort!="order by regdate asc" && $sort!="order by regdate desc") {
	$sort="order by regdate desc";
}


$qry = "WHERE 1=1 ";
if(strlen($code)>=3) {
	$qry.= "AND p.productcode LIKE '".$code."%' ";
}
$qry.= "AND p.vender='".$_VenderInfo->getVidx()."' ";
if($disptype=="Y") $qry.= "AND p.display='Y' ";
else if($disptype=="N") $qry.= "AND p.display='N' ";
if(strlen($search)>0) {
	if($s_check=="name") $qry.= "AND p.productname LIKE '%".$search."%' ";
	else if($s_check=="code") $qry.= "AND p.productcode='".$search."' ";
}


$setup[page_num] = 10;
$setup[list_num] = 20;

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

$t_count=0;
$sql = "SELECT COUNT(*) as t_count FROM tblproduct as p ".$qry." ";
$result = mysql_query($sql,get_db_conn());
$row = mysql_fetch_object($result);
$t_count = $row->t_count;
mysql_free_result($result);
$pagecount = (($t_count - 1) / $setup[list_num]) + 1;

?>

<? INCLUDE "header.php"; ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function ACodeSendIt(code) {
	document.sForm.code.value=code;
	murl = "product_myprd.ctgr.php?code="+code+"&depth=2";
	surl = "product_myprd.ctgr.php?depth=3";
	durl = "product_myprd.ctgr.php?depth=4";
	BCodeCtgr.location.href = murl;
	CCodeCtgr.location.href = surl;
	DCodeCtgr.location.href = durl;
}

//엑셀파일 다운로드
function excelDown() {
	document.etcform.prcodes.value="";
	for(i=1;i<document.form2.chkprcode.length;i++) {
		if(document.form2.chkprcode[i].checked==true) {
			document.etcform.prcodes.value+=document.form2.chkprcode[i].value+",";
		}
	}
	if(document.etcform.prcodes.value.length==0) {
		alert("선택하신 상품이 없습니다.");
		return;
	}
	if(confirm("선택하신 상품의 정보를 엑셀다운로드 하시겠습니까?")) {
		document.etcform.mode.value="excel";
		document.etcform.display.value="";
		document.etcform.action="product_myprd.exceldown.php";
		document.etcform.target="processFrame";
		document.etcform.submit();
	}
}

<?if(substr($_venderdata->grant_product,1,1)=="Y" && substr($_venderdata->grant_product,3,1)=="N") {?>
function setPrdDisplaytype(prcode,display) {
	if(display!="Y" && display!="N") {
		alert("ON/OFF 설정이 잘못되었습니다.");
		return;
	}
	document.etcform.prcodes.value="";
	if(prcode.length==18) {
		document.etcform.prcodes.value+=prcode+",";
	} else {
		for(i=1;i<document.form2.chkprcode.length;i++) {
			if(document.form2.chkprcode[i].checked==true) {
				document.etcform.prcodes.value+=document.form2.chkprcode[i].value+",";
			}
		}
	}
	if(document.etcform.prcodes.value.length==0) {
		alert("선택하신 상품이 없습니다.");
		return;
	}
	if(confirm("선택하신 상품의 상품진열을 ["+(display=="Y"?"ON":"OFF")+"] 하시겠습니까?")) {
		document.etcform.mode.value="display";
		document.etcform.display.value=display;
		document.etcform.action="<?=$_SERVER[PHP_SELF]?>";
		document.etcform.target="processFrame";
		document.etcform.submit();
	}
}
<?}?>

<?if(substr($_venderdata->grant_product,2,1)=="Y") {?>
function DeletePrd(prcode) {
	document.etcform.prcodes.value="";
	if(prcode.length==18) {
		document.etcform.prcodes.value+=prcode+",";
	} else {
		for(i=1;i<document.form2.chkprcode.length;i++) {
			if(document.form2.chkprcode[i].checked==true) {
				document.etcform.prcodes.value+=document.form2.chkprcode[i].value+",";
			}
		}
	}
	if(document.etcform.prcodes.value.length==0) {
		alert("선택하신 상품이 없습니다.");
		return;
	}
	if(confirm("선택하신 상품을 삭제할 경우 복구가 불가능합니다.\n\선택하신 상품을 완전히 삭제하시겠습니까?")) {
		document.etcform.mode.value="delete";
		document.etcform.display.value="";
		document.etcform.action="<?=$_SERVER[PHP_SELF]?>";
		document.etcform.target="processFrame";
		document.etcform.submit();
	}
}
<?}?>

function SearchPrd() {
	document.sForm.submit();
}

function GoPage(block,gotopage) {
	document.pageForm.block.value=block;
	document.pageForm.gotopage.value=gotopage;
	document.pageForm.submit();
}

function OrderSort(sort) {
	document.pageForm.block.value="";
	document.pageForm.gotopage.value="";
	document.pageForm.sort.value=sort;
	document.pageForm.submit();
}

function GoPrdinfo(prcode,target) {
	document.form3.target="";
	document.form3.prcode.value=prcode;
	if(target.length>0) {
		document.form3.target=target;
	}
	document.form3.submit();
}

function CheckAll(){
   chkval=document.form2.allcheck.checked;
   cnt=document.form2.tot.value;
   for(i=1;i<=cnt;i++){
      document.form2.chkprcode[i].checked=chkval;
   }
}

function viewHistory(productcode) {
	window.open("vender_prdtcom_histoy_pop.php?productcode="+productcode,"history","height=400,width=550,toolbar=no,menubar=no,scrollbars=yes,status=no");
}

</script>
<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed"  height="100%" >
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
					<td><img src="images/product_myprd_title.gif"></td>
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
											<td class="notice_gray" height="20"><img src="images/icon_dot02.gif" border=0 hspace="4">카테고리 분류/상품명 검색으로 등록된 상품을 관리합니다.</td>
										</tr>
										<tr>
											<td class="notice_gray" height="20"><img src="images/icon_dot02.gif" border=0 hspace="4">상품명 클릭시 해당 상품 열람/수정이 가능합니다.</td>
										</tr>
										<tr>
											<td class="notice_gray" height="20"><img src="images/icon_dot02.gif" border=0 hspace="4">상품 체크 후 ON/OFF 상태를 일괄 변경할 수 있습니다.</td>
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
				<td>






				<table border=0 cellpadding=0 cellspacing=0 width=100%>
				<tr>
					<td valign=top bgcolor=D4D4D4 style=padding:1>
					<table border=0 cellpadding=0 cellspacing=0 width=100%>
					<tr>
						<td valign=top bgcolor=F0F0F0 style=padding:10>
						<table border=0 cellpadding=0 cellspacing=0 width=100%>
						<form name="sForm" method="post">
						<input type="hidden" name="code" value="<?=$code?>">
						<tr>
							<td>
							<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
							<col width=155></col>
							<col width=></col>
							<col width=155></col>
							<col width=></col>
							<col width=155></col>
							<col width=></col>
							<col width=155></col>
							<tr>
								<td>
								<select name="code1" style=width:155 onchange="ACodeSendIt(this.options[this.selectedIndex].value)">
								<option value="">------ 대 분 류 ------</option>
<?
								$sql = "SELECT SUBSTRING(productcode,1,3) as prcode FROM tblproduct ";
								$sql.= "WHERE vender='".$_VenderInfo->getVidx()."' ";
								$sql.= "GROUP BY prcode ";
								$result=mysql_query($sql,get_db_conn());
								$codes="";
								while($row=mysql_fetch_object($result)) {
									$codes.=$row->prcode.",";
								}
								mysql_free_result($result);
								if(strlen($codes)>0) {
									$codes=substr($codes,0,-1);
									$prcodelist=ereg_replace(',','\',\'',$codes);
								}
								if(strlen($prcodelist)>0) {
									$sql = "SELECT codeA,codeB,codeC,codeD,code_name FROM tblproductcode ";
									$sql.= "WHERE codeA IN ('".$prcodelist."') AND codeB='000' AND codeC='000' ";
									$sql.= "AND codeD='000' AND type LIKE 'L%' ORDER BY sequence DESC ";
									$result=mysql_query($sql,get_db_conn());
									while($row=mysql_fetch_object($result)) {
										echo "<option value=\"".$row->codeA."\"";
										if($row->codeA==substr($code,0,3)) echo " selected";
										echo ">".$row->code_name."</option>\n";
									}
									mysql_free_result($result);
								}
?>
								</select>
								</td>
								<td></td>
								<td>
								<iframe name="BCodeCtgr" src="product_myprd.ctgr.php?code=<?=substr($code,0,3)?>&select_code=<?=$code?>&depth=2" width="155" height="21" scrolling=no frameborder=no></iframe>
								</td>
								<td></td>
								<td><iframe name="CCodeCtgr" src="product_myprd.ctgr.php?code=<?=substr($code,0,6)?>&select_code=<?=$code?>&depth=3" width="155" height="21" scrolling=no frameborder=no></iframe></td>
								<td></td>
								<td><iframe name="DCodeCtgr" src="product_myprd.ctgr.php?code=<?=substr($code,0,9)?>&select_code=<?=$code?>&depth=4" width="155" height="21" scrolling=no frameborder=no></iframe></td>
							</tr>
							</table>
							</td>
						</tr>
						<tr><td height=5></td></tr>
						<tr>
							<td>
							<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
							<col width=155></col>
							<col width=></col>
							<col width=155></col>
							<col width=></col>
							<col width=155></col>
							<col width=></col>
							<col width=155></col>
							<tr>
								<td>
								<select name=disptype style="width:100%">
								<option value="">진열/대기상품 전체</option>
								<option value="Y" <?if($disptype=="Y")echo"selected";?>>진열상품만 검색</option>
								<option value="N" <?if($disptype=="N")echo"selected";?>>대기상품만 검색</option>
								</select>
								</td>

								<td></td>

								<td>
								<select name="s_check" style="width:100%">
								<option value="name" <?if($s_check=="name")echo"selected";?>>상품명으로 검색</option>
								<option value="code" <?if($s_check=="code")echo"selected";?>>상품코드로 검색</option>
								</select>
								</td>

								<td></td>

								<td><input type=text name=search value="<?=$search?>" style="width:100%"></td>

								<td></td>

								<td><A HREF="javascript:SearchPrd()"><img src=images/btn_inquery03.gif border=0></A></td>
							</tr>
							</table>
							</td>
						</tr>

						</form>

						</table>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				</table>

				<table border=0 cellpadding=0 cellspacing=0 width=100%>
				<tr><td height=20></td></tr>
				<tr>
					<td>
					<table border=0 cellpadding=0 cellspacing=0 width=100%>
					<col width=150></col>
					<col width=></col>
					<tr>
						<td><img src=images/btn_exceldown.gif border=0 style="cursor:hand" onclick="excelDown()"></td>
						<td align=right>
						<?if(substr($_venderdata->grant_product,1,1)=="Y" && substr($_venderdata->grant_product,3,1)=="N") {?>
						<img src=images/btn_prddispon.gif border=0 style="cursor:hand" onclick="setPrdDisplaytype('','Y')">
						<img src=images/btn_prddispoff.gif border=0 style="cursor:hand" onclick="setPrdDisplaytype('','N')">
						<?}?>
						<?if(substr($_venderdata->grant_product,2,1)=="Y") {?>
						<img src=images/btn_prddel.gif border=0 style="cursor:hand" onclick="DeletePrd('')">
						<?}?>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				<tr><td height=3></td></tr>
				<tr><td height=1 bgcolor=#cccccc></td></tr>
				<tr>
					<td bgcolor=E7E7E7>
					<table width=100% border=0 cellspacing=1 cellpadding=0 style="table-layout:fixed">
					<col width=30></col>
					<col width=40></col>
					<col width=120></col>
					<col width=></col>
					<? /** 수수료 관련 jdy **/ ?>
					<? if ($account_rule==1 || $commission_type==1) { ?>
					<col width=120></col>
					<? } ?>
					<? /** 수수료 관련 jdy **/ ?>
					<col width=60></col>
					<col width=70></col>
					<col width=60></col>

					<form name=form2 method=post>
					<input type=hidden name=chkprcode>

					<tr height=35 align=center bgcolor=F5F5F5>
						<td align=center><input type=checkbox name=allcheck onclick="CheckAll()"></td>
						<td align=center><B>번호</B></td>
						<td align=center><a href="javascript:OrderSort('<?=($sort=="order by productcode asc"?"order by productcode desc":"order by productcode asc")?>')"; onMouseover="self.status=''; return true; "><B>상품코드</B></a></td>
						<td align=center><a href="javascript:OrderSort('<?=($sort=="order by productname asc"?"order by productname desc":"order by productname asc")?>')"; onMouseover="self.status=''; return true; "><B>상품명</B></a></td>

						<? /** 수수료 관련 jdy **/ ?>
						<? if ($account_rule==1 || $commission_type==1) {

							if ($account_rule==1) { ?>
							<td align=center><B>공급가</B></a></td>
						<?
							}else { ?>
							<td align=center><B>수수료</B></a></td>
						<?	}
						}?>
						<? /** 수수료 관련 jdy **/ ?>

						<td align=center><a href="javascript:OrderSort('<?=($sort=="order by sellprice asc"?"order by sellprice desc":"order by sellprice asc")?>')"; onMouseover="self.status=''; return true; "><B>가격</B></a></td>
						<td align=center><a href="javascript:OrderSort('<?=($sort=="order by regdate asc"?"order by regdate desc":"order by regdate asc")?>')"; onMouseover="self.status=''; return true; "><B>등록일</B></a></td>
						<td align=center><B>상품진열</B></td>
					</tr>
<?
					$colspan=7;
					$cnt=0;
					if($t_count>0) {
						/*
						$sql = "SELECT productcode,productname,sellprice,regdate,display,selfcode FROM tblproduct ".$qry." ".$sort." ";
						*/

						/* 개별 수수료 관련 jdy */
						$sql = "SELECT p.productcode,productname,sellprice,regdate,display,selfcode, c.rq_com, c.cf_com, c.rq_cost, c.cf_cost, c.status, c.first_approval, reservation FROM tblproduct p left join product_commission c on p.productcode=c.productcode ".$qry." ".$sort." ";
						/* 개별 수수료 관련 jdy */

						$sql.= "LIMIT " . ($setup[list_num] * ($gotopage - 1)) . ", " . $setup[list_num];
						$result=mysql_query($sql,get_db_conn());
						$i=0;
						while($row=mysql_fetch_object($result)) {
							$number = ($t_count-($setup[list_num] * ($gotopage-1))-$i);
							echo "<tr height=30 bgcolor=#FFFFFF>\n";
							echo "	<td align=center><input type=checkbox name=chkprcode value=\"".$row->productcode."\"></td>\n";
							echo "	<td align=center style=\"font-size:8pt\">".$number."</td>\n";
							echo "	<td align=center style=\"font-size:8pt\">".$row->productcode."</td>\n";

							$reservation = "";
							if( $row->reservation != "0000-00-00" ) {
								$reservation = "<font color=red>[예약배송:".$row->reservation."]</font><br>";
							}
							echo "	<td style='font-size:8pt;line-height:11pt;padding-left:5;padding-right:5'>".$reservation."<A HREF=\"javascript:GoPrdinfo('".$row->productcode."','')\">".titleCut(45,$row->productname.($row->selfcode?"-".$row->selfcode:""))."</A> <A HREF=\"javascript:GoPrdinfo('".$row->productcode."','_blank')\"><img src=images/newwindow.gif border=0 align=absmiddle></A></td>\n";

							 /** 수수료 관련 jdy **/
							if ($account_rule==1 || $commission_type==1) {
								/* 개별 수수료 관련 jdy */
								$history_html = "<button style='color:ffffff;background-color:1F497D;padding:0px 4px;border:0;' onclick=\"viewHistory('".$row->productcode."')\">H</button>";
								if ($account_rule==1) {

									if ($row->status == "") {
										$com_value = "공급가를 지정해주세요.";
									}else if ($row->status == "1") {

										if ($row->first_approval == "1") {
											$com_value = $history_html." ".$row->cf_cost."원 [".$row->rq_cost."원 요청]";
										}else{
											$com_value = $history_html." [".$row->rq_cost."원 요청]";
										}
									}else if ($row->status == "2") {
										$com_value = $history_html." ".$row->cf_cost."원";
									}else if ($row->status == "3") {

										if ($row->first_approval == "1") {
											$com_value = $history_html." ".$row->cf_cost."원 [".$row->rq_cost."원 요청거부]";
										}else{
											$com_value = $history_html." [".$row->rq_cost."원 요청거부]";
										}

									}
								}else{

									if ($commission_type =="1") {

										if ($row->status == "") {
											$com_value = "수수료를 지정해주세요.";
										}else if ($row->status == "1") {
											if ($row->first_approval == "1") {
												$com_value = $history_html." ".$row->cf_com."% [".$row->rq_com."% 요청]";
											}else{
												$com_value = $history_html." [".$row->rq_com."% 요청]";
											}


										}else if ($row->status == "2") {
											$com_value = $history_html." ".$row->cf_com."%";
										}else if ($row->status == "3") {
											if ($row->first_approval == "1") {
												$com_value = $history_html." ".$row->cf_com."% [".$row->rq_com."% 요청거부]";
											}else{
												$com_value = $history_html." [".$row->rq_com."% 요청거부]";
											}

										}
									}
								}

								/* 개별 수수료 관련 jdy */

								echo "	<td align=center style=font-size:8pt;padding-right:5>".$com_value."</td>\n";
							}

							echo "	<td align=right style=font-size:8pt;padding-right:5>".number_format($row->sellprice)."</td>\n";
							echo "	<td align=center style=\"font-size:8pt\">".substr($row->regdate,0,10)."</td>\n";
							echo "	<td align=center>";
							if(substr($_venderdata->grant_product,1,1)=="Y" && substr($_venderdata->grant_product,3,1)=="N") {
								if($row->display=="Y") {
									echo "<img src=images/icon_on.gif border=0 style=\"cursor:hand\" onclick=\"setPrdDisplaytype('".$row->productcode."','N')\">";
								} else {
									echo "<img src=images/icon_off.gif border=0 style=\"cursor:hand\" onclick=\"setPrdDisplaytype('".$row->productcode."','Y')\">";
								}
							} else {
								if($row->display=="Y") {
									echo "<img src=images/icon_on.gif border=0>";
								} else {
									echo "<img src=images/icon_off.gif border=0>";
								}
							}
							echo "	</td>\n";
							echo "</tr>\n";
							$i++;
						}
						mysql_free_result($result);
						$cnt=$i;

						if($i>0) {
							$total_block = intval($pagecount / $setup[page_num]);
							if (($pagecount % $setup[page_num]) > 0) {
								$total_block = $total_block + 1;
							}
							$total_block = $total_block - 1;
							if (ceil($t_count/$setup[list_num]) > 0) {
								// 이전	x개 출력하는 부분-시작
								$a_first_block = "";
								if ($nowblock > 0) {
									$a_first_block .= "<a href='javascript:GoPage(0,1);' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='첫 페이지';return true\"><img src=".$Dir."images/minishop/btn_miniprev_end.gif border=0 align=absmiddle></a> ";
									$prev_page_exists = true;
								}
								$a_prev_page = "";
								if ($nowblock > 0) {
									$a_prev_page .= "<a href='javascript:GoPage(".($nowblock-1).",".($setup[page_num]*($block-1)+$setup[page_num]).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='이전 ".$setup[page_num]." 페이지';return true\"><img src=".$Dir."images/minishop/btn_miniprev.gif border=0 align=absmiddle></a> ";

									$a_prev_page = $a_first_block.$a_prev_page;
								}
								if (intval($total_block) <> intval($nowblock)) {
									$print_page = "";
									for ($gopage = 1; $gopage <= $setup[page_num]; $gopage++) {
										if ((intval($nowblock*$setup[page_num]) + $gopage) == intval($gotopage)) {
											$print_page .= "<FONT color=red><B>".(intval($nowblock*$setup[page_num]) + $gopage)."</B></font> ";
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
											$print_page .= "<FONT color=red><B>".(intval($nowblock*$setup[page_num]) + $gopage)."</B></FONT> ";
										} else {
											$print_page .= "<a href='javascript:GoPage(".$nowblock.",".(intval($nowblock*$setup[page_num]) + $gopage).");' onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$setup[page_num]) + $gopage)."';return true\">[".(intval($nowblock*$setup[page_num]) + $gopage)."]</a> ";
										}
									}
								}
								$a_last_block = "";
								if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
									$last_block = ceil($t_count/($setup[list_num]*$setup[page_num])) - 1;
									$last_gotopage = ceil($t_count/$setup[list_num]);
									$a_last_block .= " <a href='javascript:GoPage(".$last_block.",".$last_gotopage.");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='마지막 페이지';return true\"><img src=".$Dir."images/minishop/btn_mininext_end.gif border=0 align=absmiddle></a>";
									$next_page_exists = true;
								}
								$a_next_page = "";
								if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
									$a_next_page .= " <a href='javascript:GoPage(".($nowblock+1).",".($setup[page_num]*($nowblock+1)+1).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='다음 ".$setup[page_num]." 페이지';return true\"><img src=".$Dir."images/minishop/btn_mininext.gif border=0 align=absmiddle></a>";
									$a_next_page = $a_next_page.$a_last_block;
								}
							} else {
								$print_page = "<B>1</B>";
							}
							$pageing=$a_div_prev_page.$a_prev_page.$print_page.$a_next_page.$a_div_next_page;
						}
					} else {
						echo "<tr height=28 bgcolor=#FFFFFF><td colspan=".$colspan." align=center>조회된 내용이 없습니다.</td></tr>\n";
					}
?>
					<input type=hidden name=tot value="<?=$cnt?>">
					</form>

					</table>
					</td>
				</tr>
				<tr><td height=10></td></tr>
				<tr>
					<td align=center>
					<form name="pageForm" method="post">
					<input type=hidden name='code' value='<?=$code?>'>
					<input type=hidden name='disptype' value='<?=$disptype?>'>
					<input type=hidden name='s_check' value='<?=$s_check?>'>
					<input type=hidden name='search' value='<?=$search?>'>
					<input type=hidden name='sort' value='<?=$sort?>'>
					<input type=hidden name='block' value='<?=$block?>'>
					<input type=hidden name='gotopage' value='<?=$gotopage?>'>
					</form>

					<?=$pageing?>

					</td>
				</tr>
				</table>

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

<form name=etcform method=post action="<?=$_SERVER[PHP_SELF]?>">
<input type=hidden name=mode>
<input type=hidden name=prcodes>
<input type=hidden name=display>
</form>

<form name=form3 method=post action="product_prdmodify.php">
<input type=hidden name=prcode>
</form>

</table>

<iframe name="processFrame" src="about:blank" width="0" height="0" scrolling=no frameborder=no></iframe>

<?=$onload?>

<? INCLUDE "copyright.php"; ?>
