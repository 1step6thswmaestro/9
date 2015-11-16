<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
INCLUDE ("access.php");
include_once($Dir."lib/admin_more.php");

####################### 페이지 접근권한 check ###############
$PageCode = "vd-1";
$MenuCode = "vender";
if (!$_usersession->isAllowedTask($PageCode)) {
	INCLUDE ("AccessDeny.inc.php");
	exit;
}
#########################################################


// 정산 기준 조회 jdy
$shop_more_info = getShopMoreInfo();
$account_rule = $shop_more_info['account_rule'];
// 정산 기준 조회 jdy

$mode=$_POST["mode"];
$prcodes=$_POST["prcodes"];
$display=$_POST["display"];
if($mode=="display" && strlen($prcodes)>0 && ($display=="Y" || $display=="N")) {
	$prcodes=substr($prcodes,0,-1);
	
	if ($display=="Y") {
		$prcodelist_s = explode( ",", $prcodes);
		
		$i=0;
		while ($prcodelist_s[$i]){
			
			$p_sql = "SELECT vender FROM tblproduct WHERE productcode='".$prcodelist_s[$i]."' AND vender>0 ";

			$result=mysql_query($p_sql,get_db_conn());
			$data=mysql_fetch_array($result);		

			$vender_more = getVenderMoreInfo($data[0]);
			$commission_type = $vender_more['commission_type'];
			
			//수수료 승인이 나지 않은 상태에서 노출 불가 jdy
			if ($account_rule=="1" || $commission_type=="1") { 
				$p_sql = "select first_approval from product_commission where productcode='".$prcodelist_s[$i]."'" ;

				$result=mysql_query($p_sql,get_db_conn());
				$data=mysql_fetch_array($result);
				
				if ($data[0] != "1") {
					echo "<html></head><body onload=\"alert('아직 수수료가 결정되지 않아 진열할 수 없는상품이 있습니다.')\"></body></html>";exit;
				}
			}
			$i++;
		}
	}

	$prcodelist=ereg_replace(',','\',\'',$prcodes);
	$sql = "SELECT vender FROM tblproduct WHERE productcode IN ('".$prcodelist."') ";
	$sql.= "AND vender>0 ";
	if($display=="Y") {
		$sql.= "AND display='N' ";
	} else {
		$sql.= "AND display='Y' ";
	}
	$sql.= "GROUP BY vender ";
	$p_result=mysql_query($sql,get_db_conn());
	while($p_row=mysql_fetch_object($p_result)) {
		$sql = "UPDATE tblproduct SET display='".$display."' ";
		$sql.= "WHERE productcode IN ('".$prcodelist."') ";
		$sql.= "AND vender='".$p_row->vender."' ";
		if(mysql_query($sql,get_db_conn())) {
			$sql = "SELECT COUNT(*) as prdt_allcnt, COUNT(IF(display='Y',1,NULL)) as prdt_cnt FROM tblproduct ";
			$sql.= "WHERE vender='".$p_row->vender."' ";
			$result=mysql_query($sql,get_db_conn());
			$row=mysql_fetch_object($result);
			$prdt_allcnt=(int)$row->prdt_allcnt;
			$prdt_cnt=(int)$row->prdt_cnt;
			mysql_free_result($result);

			$sql = "UPDATE tblvenderstorecount SET prdt_allcnt='".$prdt_allcnt."', prdt_cnt='".$prdt_cnt."' ";
			$sql.= "WHERE vender='".$p_row->vender."' ";
			mysql_query($sql,get_db_conn());
		}
	}
	mysql_free_result($p_result);

	echo "<html></head><body onload=\"alert('요청하신 작업이 성공하였습니다.');parent.pageForm.submit();\"></body></html>";exit;

} else if($mode=="delete" && strlen($prcodes)>0) {
	unset($_deldata);
	$prcodes=substr($prcodes,0,-1);
	$prcodelist=ereg_replace(',','\',\'',$prcodes);

	$prcodes="";
	$sql = "SELECT productcode, productname, maximage, minimage, tinyimage, display FROM tblproduct ";
	$sql.= "WHERE productcode IN ('".$prcodelist."') AND vender>0 ";
	$result=mysql_query($sql,get_db_conn());
	while($row=mysql_fetch_object($result)) {
		$_deldata[]=$row;
		$prcodes.=$row->productcode.",";
	}
	mysql_free_result($result);

	if(count($_deldata)>0) {
		$prcodes=substr($prcodes,0,-1);
		$prcodelist=ereg_replace(',','\',\'',$prcodes);

		$arrvender=array();
		$sql = "SELECT vender FROM tblproduct WHERE productcode IN ('".$prcodelist."') AND vender>0 ";
		$sql.= "GROUP BY vender ";
		$p_result=mysql_query($sql,get_db_conn());
		while($p_row=mysql_fetch_object($p_result)) {
			$arrvender[]=$p_row->vender;
		}
		mysql_free_result($p_result);
		$sql = "DELETE FROM tblproduct WHERE productcode IN ('".$prcodelist."') AND vender>0 ";
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

			$sql = "DELETE FROM tblproducttheme WHERE productcode IN ('".$prcodelist."') ";
			mysql_query($sql,get_db_conn());

			$sql = "DELETE FROM tblproductgroupcode WHERE productcode IN ('".$prcodelist."') ";
			mysql_query($sql,get_db_conn());

			/* 추가 수수료 테이블 내용 삭제 jdy */
			$sql = "DELETE FROM product_commission WHERE productcode IN ('".$prcodelist."') ";
			mysql_query($sql,get_db_conn());
			/* 추가 수수료 테이블 내용 삭제 jdy */

			for($yy=0;$yy<count($arrvender);$yy++) {
				//미니샵 테마코드에 등록된 상품 삭제
				$sql = "DELETE FROM tblvenderthemeproduct WHERE vender='".$arrvender[$yy]."' ";
				$sql.= "AND productcode IN ('".$prcodelist."') ";
				mysql_query($sql,get_db_conn());

				//미니샵 상품수 업데이트 (진열된 상품만)
				$sql = "SELECT COUNT(*) as prdt_allcnt, COUNT(IF(display='Y',1,NULL)) as prdt_cnt FROM tblproduct ";
				$sql.= "WHERE vender='".$arrvender[$yy]."' ";
				$result=mysql_query($sql,get_db_conn());
				$row=mysql_fetch_object($result);
				$prdt_allcnt=(int)$row->prdt_allcnt;
				$prdt_cnt=(int)$row->prdt_cnt;
				mysql_free_result($result);

				$sql ="UPDATE tblvenderstorecount SET prdt_allcnt='".$prdt_allcnt."', prdt_cnt='".$prdt_cnt."' ";
				$sql.="WHERE vender='".$arrvender[$yy]."' ";
				mysql_query($sql,get_db_conn());

				//tblvendercodedesign => 해당 대분류 상품 확인 후 없으면 대분류 화면 삭제
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
					$sql.= "AND vender='".$arrvender[$yy]."' ";
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

							$imagename=$Dir.DataDir."shopimages/vender/".$arrvender[$yy]."_CODE10_".$key.".gif";
							@unlink($imagename);
						}
						$str_codeA=substr($str_codeA,0,-1);
						$str_codeA=ereg_replace(',','\',\'',$str_codeA);
						$sql = "DELETE FROM tblvendercodedesign WHERE vender='".$arrvender[$yy]."' ";
						$sql.= "AND code IN ('".$str_codeA."') AND tgbn='10' ";
						mysql_query($sql,get_db_conn());
					}
				}
			}

			#상품이미지 삭제
			$storeimagepath=$Dir.DataDir."shopimages/product/";
			$update_ymd = date("YmdH");
			$update_ymd2 = date("is");
			for($i=0;$i<count($_deldata);$i++) {
				unset($vimagear);
				$vimagear=array(&$vimage,&$vimage2,&$vimage3);
				$vimage=$_deldata[$i]->maximage;
				$vimage2=$_deldata[$i]->minimage;
				$vimage3=$_deldata[$i]->tinyimage;

				for($y=0;$y<3;$y++){
					if(strlen($vimagear[$y])>0 && file_exists($storeimagepath.$vimagear[$y]))
						unlink($storeimagepath.$vimagear[$y]);
				}
				@delProductMultiImg("prdelete","",$_deldata[$i]->productcode);

				$update_date = $update_ymd.$update_ymd2;
				$log_content = "## 상품삭제 ## - 상품코드 ".$_deldata[$i]->productcode." - 상품명 : ".urldecode($_deldata[$i]->productname)." ".$_deldata[$i]->display."";
				ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content,$update_date);
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
$vender=$_POST["vender"];
$disptype=$_POST["disptype"];
$s_check=$_POST["s_check"];
if(strlen($s_check)==0) $s_check="name";
$search=ltrim($_POST["search"]);
$sort=$_POST["sort"];
if($sort!="order by productname asc" && $sort!="order by productname desc" && $sort!="order by productcode asc" && $sort!="order by productcode desc" && $sort!="order by vender asc" && $sort!="order by vender desc" && $sort!="order by sellprice asc" && $sort!="order by sellprice desc" && $sort!="order by regdate asc" && $sort!="order by regdate desc") {
	$sort="order by regdate desc";
}

$qry = "WHERE 1=1 ";

if(strlen($code)>=3) {
	$qry.= "AND p.productcode LIKE '".$code."%' ";
}
if(strlen($vender)>0) {
	$qry.= "AND p.vender='".$vender."' ";
} else {
	$qry.= "AND p.vender>0 ";
}
if($disptype=="Y") $qry.= "AND p.display='Y' ";
else if($disptype=="N") $qry.= "AND p.display='N' ";
if(strlen($search)>0) {
	if($s_check=="name") $qry.= "AND p.productname LIKE '%".$search."%' ";
	else if($s_check=="code") $qry.= "AND p.productcode='".$search."' ";
}
/*
if(strlen($code)>=3) {
	$qry.= "AND productcode LIKE '".$code."%' ";
}
if(strlen($vender)>0) {
	$qry.= "AND vender='".$vender."' ";
} else {
	$qry.= "AND vender>0 ";
}
if($disptype=="Y") $qry.= "AND display='Y' ";
else if($disptype=="N") $qry.= "AND display='N' ";
if(strlen($search)>0) {
	if($s_check=="name") $qry.= "AND productname LIKE '%".$search."%' ";
	else if($s_check=="code") $qry.= "AND productcode='".$search."' ";
}
*/

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
$sql = "SELECT COUNT(*) as t_count FROM tblproduct p ".$qry." ";
$result = mysql_query($sql,get_db_conn());
$row = mysql_fetch_object($result);
$t_count = $row->t_count;
mysql_free_result($result);
$pagecount = (($t_count - 1) / $setup[list_num]) + 1;


$venderlist=array();
$sql = "SELECT vender,id,com_name,delflag, passwd FROM tblvenderinfo ORDER BY id ASC ";
$result=mysql_query($sql,get_db_conn());
while($row=mysql_fetch_object($result)) {
	$venderlist[$row->vender]=$row;
}
mysql_free_result($result);

?>

<? INCLUDE "header.php"; ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function ACodeSendIt(code) {
	document.sForm.code.value=code;
	murl = "vender_prdtlist.ctgr.php?code="+code+"&depth=2";
	surl = "vender_prdtlist.ctgr.php?depth=3";
	durl = "vender_prdtlist.ctgr.php?depth=4";
	BCodeCtgr.location.href = murl;
	CCodeCtgr.location.href = surl;
	DCodeCtgr.location.href = durl;
}

function ProductInfo(code,prcode,popup) {
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

function viewVenderInfo(vender) {
	window.open("about:blank","vender_infopop","width=100,height=100,scrollbars=yes");
	document.vForm.vender.value=vender;
	document.vForm.target="vender_infopop";
	document.vForm.submit();
}

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

function CheckAll(){
   chkval=document.form2.allcheck.checked;
   cnt=document.form2.tot.value;
   for(i=1;i<=cnt;i++){
      document.form2.chkprcode[i].checked=chkval;
   }
}

function viewHistory(productcode) {
	window.open("vender_prdtcom_histoy_pop.php?productcode="+productcode,"history","height=400,width=580,toolbar=no,menubar=no,scrollbars=yes,status=no");
}

function manageCom(productcode) {
	window.open("vender_prdtcom_modify_pop.php?productcode="+productcode,"modify","height=400,width=500,toolbar=no,menubar=no,scrollbars=yes,status=no");
}

function loginVender(vender, pd) {

	window.open("","loginVender","");

	document.lForm.id.value=vender;
	document.lForm.passwd.value=pd;
	document.lForm.action="/vender/loginproc.php";
	document.lForm.target="loginVender";
	document.lForm.submit();
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
			<? include ("menu_vender.php"); ?>
			</td>

			<td></td>
			<td valign="top">




<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td height="29" colspan="3">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 입점관리 &gt; 입점상품 관리 &gt; <span class="2depth_select">입점업체 상품목록</span></td>
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
			<tr><td height="8"></td></tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/vender_prdtlist_title.gif" ALT=""></TD>
					</tr><tr>
					<TD width="100%" background="images/title_bg.gif" height="21"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height="3"></td></tr>
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
					<TD width="100%" class="notice_blue">입점업체의 상품정보 및 관리를 하실 수 있습니다.</TD>
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
			<form name="sForm" method="post">
			<input type="hidden" name="code" value="<?=$code?>">
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td bgcolor="#ededed" style="padding:4pt;">
					<table cellpadding="0" cellspacing="0" width="100%" bgcolor="FFFFFF">
					<tr>
						<td width="100%">
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
						<TR>
							<TD height="1" background="images/table_con_line.gif"></TD>
						</TR>
						<TR>
							<TD height="35" align="center" background="images/blueline_bg.gif"><b><font color="#333333">입점업체 상품 검색 선택</font></b></TD>
						</TR>
						<TR>
							<TD>
							<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
							<TR>
								<TD height="1" background="images/table_con_line.gif"></TD>
							</TR>
							<TR>
								<TD class="td_con1" style="padding-top:10pt;padding-left:10px;" align="center">
								<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
								<col width=155></col>
								<col width=5></col>
								<col width=155></col>
								<col width=5></col>
								<col width=155></col>
								<col width=5></col>
								<col width=></col>
								<tr>
									<td>
									<select name="code1" style=width:155 onchange="ACodeSendIt(this.options[this.selectedIndex].value)">
									<option value="">------ 대 분 류 ------</option>
<?
									$sql = "SELECT codeA,codeB,codeC,codeD,code_name FROM tblproductcode ";
									$sql.= "WHERE codeB='000' AND codeC='000' ";
									$sql.= "AND codeD='000' AND type LIKE 'L%' ORDER BY sequence DESC ";
									$result=mysql_query($sql,get_db_conn());
									while($row=mysql_fetch_object($result)) {
										echo "<option value=\"".$row->codeA."\"";
										if($row->codeA==substr($code,0,3)) echo " selected";
										echo ">".$row->code_name."</option>\n";
									}
									mysql_free_result($result);
?>
									</select>
									</td>
									<td></td>
									<td>
									<iframe name="BCodeCtgr" src="vender_prdtlist.ctgr.php?code=<?=substr($code,0,3)?>&select_code=<?=$code?>&depth=2" width="155" height="21" scrolling=no frameborder=no></iframe>
									</td>
									<td></td>
									<td><iframe name="CCodeCtgr" src="vender_prdtlist.ctgr.php?code=<?=substr($code,0,6)?>&select_code=<?=$code?>&depth=3" width="155" height="21" scrolling=no frameborder=no></iframe></td>
									<td></td>
									<td><iframe name="DCodeCtgr" src="vender_prdtlist.ctgr.php?code=<?=substr($code,0,9)?>&select_code=<?=$code?>&depth=4" width="155" height="21" scrolling=no frameborder=no></iframe></td>
								</tr>
								</table>
								</TD>
							</TR>
							<TR>
								<TD class="td_con1" style="padding-top:3px;padding-left:10px;" align="center">
								<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
								<col width=155></col>
								<col width=5></col>
								<col width=155></col>
								<col width=5></col>
								<col width=155></col>
								<col width=5></col>
								<col width=></col>
								<tr>
									<td>
									<select name=vender style="font-size:8pt;width:155">
									<option value="">모든 입점업체</option>
<?
									$tmplist=$venderlist;
									while(list($key,$val)=each($tmplist)) {
										if($val->delflag=="N") {
											echo "<option value=\"".$val->vender."\"";
											if($vender==$val->vender) echo " selected";
											echo ">".$val->id." - ".$val->com_name."</option>\n";
										}
									}
?>
									</select>
									</td>

									<td></td>

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

									<td>
									<input type=text name=search value="<?=$search?>" style="width:155">
									<A HREF="javascript:SearchPrd()"><img src=images/btn_inquery03.gif border=0 align=absmiddle></A>
									</td>
								</tr>
								</table>
								</td>
							</tr>
							</TABLE>
							</TD>
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
			</form>
			<tr>
				<td height="20"></td>
			</tr>
			<tr>
				<td align="right"><img src=images/btn_prddispon.gif border=0 style="cursor:hand" onclick="setPrdDisplaytype('','Y')"><img src=images/btn_prddispoff.gif border=0 style="cursor:hand" onclick="setPrdDisplaytype('','N')"><img src=images/btn_prddel.gif border=0 style="cursor:hand" onclick="DeletePrd('')"></td>
			</tr>
			<tr>
				<td height="5"></td>
			</tr>
			<form name=form2 method=post>
			<input type=hidden name=chkprcode>
			<tr>
				<td>
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<col width=30></col>
				<col width=40></col>
				<col width=70></col>
				<col width=120></col>
				<col width=></col>
				<col width=60></col>
				<col width=150></col>
				<col width=70></col>
				<col width=60></col>
				<TR>
					<TD height=1 background="images/table_top_line.gif" colspan="9"></TD>
				</TR>
				<TR>
					<TD class="table_cell" align="center"><input type=checkbox name=allcheck onclick="CheckAll()"></TD>
					<TD class="table_cell1" align="center">번호</TD>
					<TD class="table_cell1" align="center"><a href="javascript:OrderSort('<?=($sort=="order by vender asc"?"order by vender desc":"order by vender asc")?>')"; onMouseover="self.status=''; return true; "><B>입점업체</B></a></TD>
					<TD class="table_cell1" align="center"><a href="javascript:OrderSort('<?=($sort=="order by productcode asc"?"order by productcode desc":"order by productcode asc")?>')"; onMouseover="self.status=''; return true; "><B>상품코드</B></a></TD>
					<TD class="table_cell1" align="center"><a href="javascript:OrderSort('<?=($sort=="order by productname asc"?"order by productname desc":"order by productname asc")?>')"; onMouseover="self.status=''; return true; "><B>상품명</B></a></TD>
					<TD class="table_cell1" align="center"><a href="javascript:OrderSort('<?=($sort=="order by sellprice asc"?"order by sellprice desc":"order by sellprice asc")?>')"; onMouseover="self.status=''; return true; "><B>가격</B></a></TD>
					<? if ($account_rule==1) { ?>
					<TD class="table_cell1" align="center"><B>공급가</B></TD>
					<? }else {?>
					<TD class="table_cell1" align="center"><B>수수료</B></TD>
					<? } ?>

					<TD class="table_cell1" align="center"><a href="javascript:OrderSort('<?=($sort=="order by regdate asc"?"order by regdate desc":"order by regdate asc")?>')"; onMouseover="self.status=''; return true; "><B>등록일</B></a></TD>
					<TD class="table_cell1" align="center"><B>상품진열</B></TD>
				</TR>
				<TR>
					<TD height=1 background="images/table_con_line.gif" colspan="9"></TD>
				</TR>

<?
				$colspan=9;
				$cnt=0;
				if($t_count>0) {
					/*
					$sql = "SELECT productcode,productname,sellprice,regdate,display,vender FROM tblproduct ".$qry." ".$sort." ";
					*/
					/* 개별 수수료 관련 jdy */
					$sql = "SELECT p.productcode,productname,sellprice,regdate,display,vender, 
							(select commission_type from vender_more_info where vender=p.vender) as commission_type,
							(select rate from tblvenderinfo where vender=p.vender) as v_rate,
							c.rq_com, c.cf_com, c.rq_cost, c.cf_cost, c.status, c.first_approval FROM tblproduct p left join product_commission c on p.productcode=c.productcode ".$qry." ".$sort." ";
					/* 개별 수수료 관련 jdy */
					$sql.= "LIMIT " . ($setup[list_num] * ($gotopage - 1)) . ", " . $setup[list_num];

					$result=mysql_query($sql,get_db_conn());
					$i=0;
					while($row=mysql_fetch_object($result)) {

						$number = ($t_count-($setup[list_num] * ($gotopage-1))-$i);
						
						/* 개별 수수료 관련 jdy */
						$history_html = "<img src=\"images/icon_history.gif\" style='cursor:pointer; border:0;' onclick=\"viewHistory('".$row->productcode."')\">";
						if ($account_rule==1) {

							if ($row->status == "") {
								$com_value = "공급가를 지정해주세요.";
							}else if ($row->status == "1") { 		
								if ($row->first_approval == "1") {
									$com_value = "<b>".$row->cf_cost."원</b> <a href=\"javascript:manageCom('".$row->productcode."')\">[".$row->rq_cost."원 요청]</a><br />".$history_html;
								}else{
									$com_value = "<a href=\"javascript:manageCom('".$row->productcode."')\">[".$row->rq_cost."원 요청]</a><br />".$history_html;
								}

							}else if ($row->status == "2") {
								$com_value = "<b>".$row->cf_cost."원</b> <a href=\"javascript:manageCom('".$row->productcode."')\">[승인]</a><br />".$history_html;
							}else if ($row->status == "3") {

								if ($row->first_approval == "1") {
									$com_value = "<b>".$row->cf_cost."원</b> <a href=\"javascript:manageCom('".$row->productcode."')\">[".$row->rq_cost."원 요청거부]</a><br />".$history_html;
								}else{
									$com_value = "<a href=\"javascript:manageCom('".$row->productcode."')\">[".$row->rq_cost."원 요청거부]</a><br />".$history_html;
								}

							}

						}else{
							
							if ($row->commission_type =="1") {
								
								if ($row->status == "") {
									$com_value = "수수료를 지정해주세요.";
								}else if ($row->status == "1") { 			
									
									if ($row->first_approval == "1") {
										$com_value = "<b>".$row->cf_com."%</b> <a href=\"javascript:manageCom('".$row->productcode."')\">[".$row->rq_com."% 요청]</a><br />".$history_html;
									}else{
										$com_value = "<a href=\"javascript:manageCom('".$row->productcode."')\">[".$row->rq_com."% 요청]</a><br />".$history_html;
									}
								}else if ($row->status == "2") {
									$com_value = "<b>".$row->cf_com."%</b> <a href=\"javascript:manageCom('".$row->productcode."')\">[승인]</a><br />".$history_html;
								}else if ($row->status == "3") {
									if ($row->first_approval == "1") {
										$com_value = "<b>".$row->cf_com."%</b> <a href=\"javascript:manageCom('".$row->productcode."')\">[".$row->rq_com."% 요청거부]</a><br />".$history_html;
									}else{
										$com_value = "<a href=\"javascript:manageCom('".$row->productcode."')\">[".$row->rq_com."% 요청거부]</a><br />".$history_html;
									}
								}
							}else{
								$com_value = "전체수수료 ".$row->v_rate."%";
							}
			
						}

						/* 개별 수수료 관련 jdy */

						echo "<tr bgcolor=#FFFFFF onmouseover=\"this.style.background='#FEFBD1'\" onmouseout=\"this.style.background='#FFFFFF'\">\n";
						echo "	<td class=\"td_con2\" align=center><input type=checkbox name=chkprcode value=\"".$row->productcode."\"></td>\n";
						echo "	<td class=\"td_con1\" align=center>".$number."</td>\n";
						echo "	<td class=\"td_con1\" align=center><B>".(strlen($venderlist[$row->vender]->vender)>0?"<a href=\"javascript:viewVenderInfo(".$row->vender.")\">".$venderlist[$row->vender]->id."</a>":"-")."</B><br/><a href=\"javascript:loginVender('".$venderlist[$row->vender]->id."','".$venderlist[$row->vender]->tblvenderinfo."');\"><span style='padding:3px 0px;'><img src=\"images/icon_venderlogin.gif\" alt=\"관리자\" /></span></a></td>\n";
						echo "	<td class=\"td_con1\" align=center><a href=\"/front/productdetail.php?productcode=".$row->productcode."\" target=\"_blank\">".$row->productcode."</a></td>\n";
						echo "	<td class=\"td_con1\" align=center style=\"word-break:break-all;\">&nbsp;<A HREF=\"javascript:ProductInfo('".substr($row->productcode,0,12)."','".$row->productcode."','')\">".titleCut(48,$row->productname)." <A HREF=\"javascript:ProductInfo('".substr($row->productcode,0,12)."','".$row->productcode."','YES')\"><img src=images/newwindow.gif border=0 align=absmiddle></A>&nbsp;</td>\n";
						echo "	<td class=\"td_con1\" align=center>&nbsp;".number_format($row->sellprice)."&nbsp;</td>\n";

						echo "  <td class=\"td_con1\" align=center>&nbsp;".$com_value."&nbsp;</td>\n";

						echo "	<td class=\"td_con1\" align=center>&nbsp;".substr($row->regdate,0,10)."&nbsp;</td>\n";
						echo "	<td class=\"td_con1\" align=center>\n";
						if($row->display=="Y") {
							echo "<img src=images/icon_on.gif border=0 style=\"cursor:hand\" onclick=\"setPrdDisplaytype('".$row->productcode."','N')\">";
						} else {
							echo "<img src=images/icon_off.gif border=0 style=\"cursor:hand\" onclick=\"setPrdDisplaytype('".$row->productcode."','Y')\">";
						}
						echo "	</td>\n";
						echo "</tr>\n";
						echo "<tr>\n";
						echo "	<TD height=1 background=\"images/table_con_line.gif\" colspan=\"9\"></TD>\n";
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
								$a_first_block .= "<a href='javascript:GoPage(0,1);' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='첫 페이지';return true\"><img src=/images/minishop/btn_miniprev_end.gif border=0 align=absmiddle></a> ";
								$prev_page_exists = true;
							}
							$a_prev_page = "";
							if ($nowblock > 0) {
								$a_prev_page .= "<a href='javascript:GoPage(".($nowblock-1).",".($setup[page_num]*($block-1)+$setup[page_num]).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='이전 ".$setup[page_num]." 페이지';return true\"><img src=/images/minishop/btn_miniprev.gif border=0 align=absmiddle></a> ";

								$a_prev_page = $a_first_block.$a_prev_page;
							}
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
							}
							$a_last_block = "";
							if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
								$last_block = ceil($t_count/($setup[list_num]*$setup[page_num])) - 1;
								$last_gotopage = ceil($t_count/$setup[list_num]);
								$a_last_block .= " <a href='javascript:GoPage(".$last_block.",".$last_gotopage.");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='마지막 페이지';return true\"><img src=/images/minishop/btn_mininext_end.gif border=0 align=absmiddle></a>";
								$next_page_exists = true;
							}
							$a_next_page = "";
							if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
								$a_next_page .= " <a href='javascript:GoPage(".($nowblock+1).",".($setup[page_num]*($nowblock+1)+1).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='다음 ".$setup[page_num]." 페이지';return true\"><img src=/images/minishop/btn_mininext.gif border=0 align=absmiddle></a>";
								$a_next_page = $a_next_page.$a_last_block;
							}
						} else {
							$print_page = "<B>[1]</B>";
						}
						$pageing=$a_div_prev_page.$a_prev_page.$print_page.$a_next_page.$a_div_next_page;
					}
				} else {
					echo "<tr height=28 bgcolor=#FFFFFF><td colspan=".$colspan." align=center>조회된 내용이 없습니다.</td></tr>\n";
				}
?>
				<TR>
					<TD colspan="<?=$colspan?>" background="images/table_top_line.gif"></TD>
				</TR>

				<tr><td colspan=<?=$colspan?> height=20></td></tr>
				<tr><td colspan=<?=$colspan?> align=center class="font_size"><?=$pageing?></td></tr>
				<tr><td colspan=<?=$colspan?> height=10></td></tr>
				<!--
				<tr><td colspan=<?=$colspan?> align=center class="font_size"><button style="font-size:12pt;color:#ffffff;border:1px solid #000000;background-color:red;padding:5px 10px" onclick="ProductInfo('','','YES')">입점업체 상품등록</button></td></tr>
				-->
				<tr><td colspan=<?=$colspan?> height=10></td></tr>
				<input type=hidden name=tot value="<?=$cnt?>">
				</form>
				</table>
				</td>
			</tr>
			<form name="pageForm" method="post">
			<input type=hidden name='code' value='<?=$code?>'>
			<input type=hidden name='vender' value='<?=$vender?>'>
			<input type=hidden name='disptype' value='<?=$disptype?>'>
			<input type=hidden name='s_check' value='<?=$s_check?>'>
			<input type=hidden name='search' value='<?=$search?>'>
			<input type=hidden name='sort' value='<?=$sort?>'>
			<input type=hidden name='block' value='<?=$block?>'>
			<input type=hidden name='gotopage' value='<?=$gotopage?>'>
			</form>
			<form name=vForm action="vender_infopop.php" method=post>
			<input type=hidden name=vender>
			</form>

			<form name=form_reg action="product_register.php" method=post>
			<input type=hidden name=code>
			<input type=hidden name=prcode>
			<input type=hidden name=popup>
			</form>

			<form name=etcform method=post action="<?=$_SERVER[PHP_SELF]?>">
			<input type=hidden name=mode>
			<input type=hidden name=prcodes>
			<input type=hidden name=display>
			</form>
			
			<? /* 로그인 관련 추가 jdy */?>
			<form name=lForm method=post>
			<input type=hidden name="id">
			<input type=hidden name="passwd">
			<input type=hidden name="admin_chk" value="1">
			</form>
			<? /* 로그인 관련 추가 jdy */?>
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
					<TD COLSPAN=3 width="100%" valign="top" class="menual_bg" style="padding-top:8pt; padding-bottom:8pt; padding-left:4pt;">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td ><span class="font_dotline">입점업체 상품목록</span></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td  class="space_top">- 입점업체가 등록한 상품리스트를 분류별 검색 및 입점사 아이디로 검색할 수 있습니다.</td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td  class="space_top">- 입점사별 등록한 상품명을 클릭시 상품정보를 확인할 수 있습니다.</td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td  class="space_top">- 등록여부가 잘못되었을 경우 본사 관리자가 수정/삭제/상품진열 진행할 수 있습니다.</td>
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
<iframe name="processFrame" src="about:blank" width="0" height="0" scrolling=no frameborder=no></iframe>
<?=$onload?>
<? INCLUDE "copyright.php"; ?>
