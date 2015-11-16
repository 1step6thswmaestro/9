<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
INCLUDE ("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "pr-4";
$MenuCode = "nomenu";
if (!$_usersession->isAllowedTask($PageCode)) {
	INCLUDE ("AccessDeny.inc.php");
	exit;
}
#########################################################

//리스트 세팅
$setup[page_num] = 10;
if(!empty($_REQUEST['clist_num']) && preg_match('/^[1-9]{1}[0-9]{0,}$/',$_REQUEST['clist_num'])) $setup[list_num] = $_REQUEST['clist_num'];
else $setup[list_num] = 10;

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
////////////////////////

$mode=$_POST["mode"];
$code=$_POST["code"];
$search=$_POST["search"];
$s_check=(int)$_POST["s_check"];

$quantity=(array)$_POST["quantity"];
$alldisplay=$_POST["alldisplay"];
$allprcode=$_POST["allprcode"];

$productcode=(array)$_POST["productcode"];
$display2=(array)$_POST["display2"];

if ($mode=="update" && count($productcode)>0) {
	$prcodes="";
	$array_display=explode("|",$alldisplay);
	$size=count($productcode);
	$u_cnt=0;
	for($i=0;$i<$size;$i++) {
		if(strlen($quantity[$i])>0 || $array_display[$i]!=$display2[$i]) {
			$prcodes.=$productcode[$i].",";
			$sql = "UPDATE tblproduct SET display = '".$array_display[$i]."' ";
			if(strlen($quantity[$i])>0) {
				$sql.= ", quantity = '".$quantity[$i]."' ";
			}
			$sql.= "WHERE productcode = '".$productcode[$i]."' ";
			mysql_query($sql,get_db_conn());
			$u_cnt++;
		}
	}
	if(strlen($prcodes)>0) {
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

		for($yy=0;$yy<count($arrvender);$yy++) {
			$sql = "SELECT COUNT(*) as prdt_allcnt, COUNT(IF(display='Y',1,NULL)) as prdt_cnt FROM tblproduct ";
			$sql.= "WHERE vender='".$arrvender[$yy]."' ";
			$result=mysql_query($sql,get_db_conn());
			$row=mysql_fetch_object($result);
			$prdt_allcnt=(int)$row->prdt_allcnt;
			$prdt_cnt=(int)$row->prdt_cnt;
			mysql_free_result($result);

			setVenderCountUpdate($prdt_allcnt, $prdt_cnt, $arrvender[$yy]);
		}
	}
	$onload="<script>alert('총 ".$u_cnt."개의 해당 상품을 변경하였습니다.');</script>";
} else if ($mode=="delete" && strlen($allprcode)>0) {
	$prcodelist = ereg_replace("\|","','",$allprcode);

	$arrvender=array();
	$arrvenderid=array();
	$arrpridx=array();
	$arrassembleuse=array();
	$arrassembleproduct=array();
	$arrproductcode=array();
	$sql = "SELECT vender,pridx,assembleuse,assembleproduct,productcode FROM tblproduct ";
	$sql.= "WHERE productcode IN ('".$prcodelist."') ";
	$p_result=mysql_query($sql,get_db_conn());
	while($p_row=mysql_fetch_object($p_result)) {
		if($p_row->vender>0 && strlen($arrvenderid[$p_row->vender])==0) {
			$arrvender[]=$p_row->vender;
			$arrvenderid[$p_row->vender]=$p_row->vender;
		}
		$arrpridx[]=$p_row->pridx;
		$arrassembleuse[]=$p_row->assembleuse;
		$arrassembleproduct[]=$p_row->assembleproduct;
		$arrproductcode[]=$p_row->productcode;
	}
	mysql_free_result($p_result);

	$sql = "DELETE FROM tblproduct WHERE productcode IN ('".$prcodelist."')";
	mysql_query($sql,get_db_conn());

	$sql = "DELETE FROM tblproducttheme WHERE productcode IN ('".$prcodelist."')";
	$result = mysql_query($sql,get_db_conn());

	$sql = "DELETE FROM tblproductreview WHERE productcode IN ('".$prcodelist."')";
	mysql_query($sql,get_db_conn());

	$sql = "DELETE FROM tblproductgroupcode WHERE productcode IN ('".$prcodelist."')";
	mysql_query($sql,get_db_conn());

	#태그관련 지우기
	$sql = "DELETE FROM tbltagproduct WHERE productcode IN ('".$prcodelist."')";
	mysql_query($sql,get_db_conn());

	$sql = "DELETE FROM tblwishlist WHERE productcode IN ('".$prcodelist."')";
	mysql_query($sql,get_db_conn());

	$sql = "DELETE FROM tblcollection WHERE productcode IN ('".$prcodelist."')";
	mysql_query($sql,get_db_conn());

	// 멀티카테고리 삭제
	$sql = "DELETE FROM `tblcategorycode` WHERE `productcode` IN ('".$prcodelist."')";
	@mysql_query($sql,get_db_conn());

	//jdy 추가 내용 조회 지우기
	$sql = "DELETE FROM product_commission WHERE productcode = '".$prcodelist."'";
	mysql_query($sql,get_db_conn());

	for($vz=0; $vz<count($arrpridx); $vz++) { // 코디/조립 기본구성상품의 가격 처리
		if($arrassembleuse[$vz]=="Y") {
			$sql = "SELECT assemble_pridx FROM tblassembleproduct ";
			$sql.= "WHERE productcode = '".$arrproductcode[$vz]."' ";
			$result = mysql_query($sql,get_db_conn());
			if($row = @mysql_fetch_object($result)) {
				$sql = "DELETE FROM tblassembleproduct WHERE productcode = '".$arrproductcode[$vz]."' ";
				mysql_query($sql,get_db_conn());

				if(strlen(str_replace("","",$row->assemble_pridx))>0) {
					$sql = "UPDATE tblproduct SET ";
					$sql.= "assembleproduct = REPLACE(assembleproduct,',".$arrproductcode[$vz]."','') ";
					$sql.= "WHERE pridx IN ('".str_replace("","','",$row->assemble_pridx)."') ";
					$sql.= "AND assembleuse != 'Y' ";
					mysql_query($sql,get_db_conn());
				}
			}
			mysql_free_result($result);
		} else {
			if(strlen($arrassembleproduct[$vz])>0) {
				$sql = "SELECT productcode, assemble_pridx FROM tblassembleproduct ";
				$sql.= "WHERE productcode IN ('".str_replace(",","','",$arrassembleproduct[$vz])."') ";
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
			$sql.= "assemble_pridx=REPLACE(assemble_pridx,'".$arrpridx[$vz]."',''), ";
			$sql.= "assemble_list=REPLACE(assemble_list,',".$arrpridx[$vz]."','') ";
			mysql_query($sql,get_db_conn());
		}
	}

	for($yy=0;$yy<count($arrvender);$yy++) {
		//미니샵 테마코드에 등록된 상품 삭제
		setVenderThemeDelete($prcodelist, $arrvender[$yy]);

		//미니샵 상품수 업데이트 (진열된 상품만)
		$sql = "SELECT COUNT(*) as prdt_allcnt, COUNT(IF(display='Y',1,NULL)) as prdt_cnt FROM tblproduct ";
		$sql.= "WHERE vender='".$arrvender[$yy]."' ";
		$result=mysql_query($sql,get_db_conn());
		$row=mysql_fetch_object($result);
		$prdt_allcnt=(int)$row->prdt_allcnt;
		$prdt_cnt=(int)$row->prdt_cnt;
		mysql_free_result($result);

		setVenderCountUpdate($prdt_allcnt, $prdt_cnt, $arrvender[$yy]);

		//tblvendercodedesign => 해당 대분류 상품 확인 후 없으면 대분류 화면 삭제
		$tmpcodeA=array();
		$arrprcode=explode("|",$allprcode);
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

					$imagename = $Dir.DataDir."shopimages/vender/".$arrvender[$yy]."_CODE10_".$key.".gif";
					@unlink($imagename);
				}
				$str_codeA=substr($str_codeA,0,-1);
				$str_codeA=ereg_replace(',','\',\'',$str_codeA);
				setVenderDesignDelete($str_codeA, $arrvender[$yy]);
			}
		}
	}

	$log_content = "## 품절 상품삭제 ## - 상품코드 ".ereg_replace("\|",",",$allprcode)."";
	ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);

	$prcode = explode("|",$allprcode);
	$cnt = count($prcode);
	
	for($i=0;$i<$cnt;$i++){
		$delshopimage=$Dir.DataDir."shopimages/product/".$prcode[$i]."*";
		proc_matchfiledel($delshopimage);
		delProductMultiImg("prdelete","",$prcode[$i]);
	}
	$onload="<script>alert('해당 상품을 삭제하였습니다.');</script>";
}

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

<? INCLUDE "header.php"; ?>
<style>td {line-height:18pt;}</style>
<script type="text/javascript" src="lib.js.php"></script>
<script>var LH = new LH_create();</script>
<script for=window event=onload>LH.exec();</script>
<script>LH.add("parent_resizeIframe('ListFrame')");</script>

<script language="JavaScript">
<?if($vendercnt>0){?>
function viewVenderInfo(vender) {
	window.open("about:blank","vender_infopop","width=100,height=100,scrollbars=yes");
	document.vForm.vender.value=vender;
	document.vForm.target="vender_infopop";
	document.vForm.submit();
}
<?}?>

function CheckUpdate(cnt) {
	if(cnt==0) return;
	if (cnt>1) {
		for(i=0;i<cnt;i++){
			if(isNaN(document.form1["quantity[]"][i].value)){
				alert('수량은 숫자만 입력이 가능합니다.');
				document.form1["quantity[]"][i].focus();
				return;
			}
			if(document.form1["display[]"][i].checked==true) document.form1.alldisplay.value+="N|";
			else document.form1.alldisplay.value+="Y|";
		}
	} else {
		if(isNaN(document.form1["quantity[]"].value)){
			alert('수량은 숫자만 입력이 가능합니다.');
			document.form1["quantity[]"].focus();
			return;
		}
		if(document.form1["display[]"].checked==true) document.form1.alldisplay.value+="N|";
		else document.form1.alldisplay.value+="Y|";
	}
	document.form1.mode.value="update";
	document.form1.search.value="OK";
	document.form1.block.value="";
	document.form1.gotopage.value="";
	document.form1.submit();
}

function CheckDelete(cnt) {
	if(cnt==0) return;
	ischeck=false;
	prcode="";
	if (cnt>1) {
		for (i=0;i<cnt;i++) {
			if (document.form1["delcheck[]"][i].checked==true) {
				ischeck=true;
				if(prcode=="") prcode=document.form1["delcheck[]"][i].value;
				else prcode=prcode+"|"+document.form1["delcheck[]"][i].value;
			}
		}
	} else {
		if (document.form1["delcheck[]"].checked==true) {
			ischeck=true;
			if(prcode=="") prcode=document.form1["delcheck[]"].value;
			else prcode=prcode+"|"+document.form1["delcheck[]"].value;
		}
	}
	if (ischeck==true && confirm("선택하신 상품을 정말로 삭제하시겠습니까?")) {
		document.form1.mode.value="delete";
		document.form1.allprcode.value=prcode;
		document.form1.search.value="OK";
		document.form1.block.value="";
		document.form1.gotopage.value="";
		document.form1.submit();
	} else if (ischeck==false) {
		alert('삭제하시려는 상품을 선택하세요.');
		return;
	}
}

function ProductInfo(prcode) {
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
	document.form1.search.value = "OK";
	document.form1.block.value = block;
	document.form1.gotopage.value = gotopage;
	document.form1.sort.value = sort;
	document.form1.submit();
}

function GoSort(sort) {
	document.form1.search.value = "OK";
	document.form1.sort.value = sort;
	document.form1.block.value = "";
	document.form1.gotopage.value = "";
	document.form1.submit();
}

function CheckAll(){	//숨김
	try {
		checkvalue=document.form1.allcheck.checked;
		if (typeof(document.form1["display[]"].length)=="number") {
			cnt=document.form1["display[]"].length;
			for(i=0;i<cnt;i++){
				document.form1["display[]"][i].checked=checkvalue;
			}
		} else {
			document.form1["display[]"].checked=checkvalue;
		}
	} catch(e) {}
}

function CheckAll2(){	//삭제
	try {
		checkvalue=document.form1.allcheck2.checked;
		if (typeof(document.form1["delcheck[]"].length)=="number") {
			cnt=document.form1["delcheck[]"].length;
			for(i=0;i<cnt;i++){
				document.form1["delcheck[]"][i].checked=checkvalue;
			}
		} else {
			document.form1["delcheck[]"].checked=checkvalue;
		}
	} catch(e) {}
}

function DivDefaultReset()
{
	if(!self.id)
	{
		self.id = self.name;
		parent.document.getElementById(self.id).style.height = parent.document.getElementById(self.id).height;
	}
}
DivDefaultReset();
</script>

<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%" bgcolor="#FFFFFF">
<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post>
<input type=hidden name=mode>
<input type=hidden name=search value="<?=$search?>">
<input type=hidden name=code value="<?=$code?>">
<input type=hidden name=block>
<input type=hidden name=gotopage>
<input type=hidden name=alldisplay>
<input type=hidden name=allprcode>
<input type=hidden name=sort value="<?=$sort?>">
<tr>
	<td width="100%" bgcolor="#FFFFFF"><IMG SRC="images/product_mainlist_text.gif" border="0"></td>
</tr>
<tr>
	<td width="100%" height="100%" valign="top" style="BORDER:#FF8730 2px solid;padding-left:5px;padding-right:5px;">
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td width="100%" style="padding-top:2pt; padding-bottom:2pt;" height="30"><div style="float:left; width:320px;"><B><span class="font_orange">* 정렬방법 :</span></B> <A HREF="javascript:GoSort('date');"><B>진열순</B></a> | <A HREF="javascript:GoSort('price');"><B>가격순</B></a> | <A HREF="javascript:GoSort('productname');"><B>상품명순</B></a> | <A HREF="javascript:GoSort('regdate');"><b>등록일순</b></a></div>
		<div style="width:200px; float:right; text-align:right">출력갯수
			<select name="clist_num" onChange="javascript:document.form1.submit();">
				<?
					$perRowsArr = array('10','20','30','50','100');
					for($i=0;$i<count($perRowsArr);$i++){
						$sel = ($_REQUEST['clist_num'] == $perRowsArr[$i])?'selected="selected"':''; ?>
				<option value="<?=$perRowsArr[$i]?>" <?=$sel?>><?=$perRowsArr[$i]?></option>
				<?	} ?>
			</select>
			</div></td>
	</tr>
	<tr>
		<td width="100%" valign="top">
		<DIV style="width:100%;height:100%;overflow:hidden;">
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td width="100%">
			<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0 style="table-layout:fixed">
<?
			$colspan=8;
			if($vendercnt>0) $colspan++;
?>
			<col width=40></col>
			<?if($vendercnt>0){?>
			<col width=70></col>
			<?}?>
			<col width=50></col>
			<col width=></col>
			<col width=70></col>
			<col width=40></col>
			<col width=40></col>
			<col width=40></col>
			<col width=50></col>
			<TR>
				<TD background="images/table_top_line.gif" colspan="<?=$colspan?>"></TD>
			</TR>
			<TR align=center>
				<TD class="table_cell3" style="font-size:11px;"><b>No</b></TD>
				<?if($vendercnt>0){?>
				<TD class="table_cell1" style="font-size:11px;">입점업체</TD>
				<?}?>
				<TD class="table_cell1" style="font-size:11px;" colspan="2">품절된 상품명/진열코드/특이사항</TD>
				<TD class="table_cell1" style="font-size:11px;">판매가격</TD>
				<TD class="table_cell1" style="font-size:11px;">수량</TD>
				<TD class="table_cell1" style="font-size:11px;">진열<br>안함</TD>
				<TD class="table_cell1" style="font-size:11px;">삭제</TD>
				<TD class="table_cell1" style="font-size:11px;">수정</TD>
			</TR>
<?
			if ($search=="OK" && strlen($code)==12) {
				$page_numberic_type=1;
				$sql = "SELECT CONCAT(codeA,codeB,codeC,codeD) as code, type,code_name FROM tblproductcode ";
				$result = mysql_query($sql,get_db_conn());
				while ($row=mysql_fetch_object($result)) {
					$code_name[$row->code] = $row->code_name;
				}
				mysql_free_result($result);

				$codeA = substr($code,0,3);
				$codeB = substr($code,3,3);
				$codeC = substr($code,6,3);
				$codeD = substr($code,9,3);
				$likecode=$codeA;
				if($codeB!="000") {
					$likecode.=$codeB;
					if($codeC!="000") {
						$likecode.=$codeC;
						if($codeD!="000") {
							$likecode.=$codeD;
						}
					}
				}
				unset($codeA);unset($codeB);unset($codeC);unset($codeD);

				$qry ="WHERE productcode LIKE '".$likecode."%' ";
				$qry.= "AND quantity <= 0 ";
				if($s_check==1)		$qry.="AND display = 'N' ";
				else if($s_check==2)$qry.="AND display = 'Y' ";

				$sql = "SELECT COUNT(*) as t_count FROM tblproduct ".$qry;
				$result = mysql_query($sql,get_db_conn());
				$row = mysql_fetch_object($result);
				mysql_free_result($result);
				$t_count = $row->t_count;
				$pagecount = (($t_count - 1) / $setup[list_num]) + 1;

				$sql = "SELECT productcode,display,addcode,productname,quantity,tinyimage,vender,sellprice,reserve,reservetype,selfcode,assembleuse FROM tblproduct ";
				$sql.= $qry;
				if ($sort=="price")				$sql.= "ORDER BY sellprice ";
				else if ($sort=="productname")	$sql.= "ORDER BY productname ";
				else if ($sort=="regdate")	$sql.= "ORDER BY regdate desc ";
				else							$sql.= "ORDER BY date DESC ";
				$sql.= "LIMIT " . ($setup[list_num] * ($gotopage - 1)) . ", " . $setup[list_num];
				$result = mysql_query($sql,get_db_conn());
				$cnt=0;
				while($row=mysql_fetch_object($result)) {
					$number = ($t_count-($setup[list_num] * ($gotopage-1))-$cnt);

					if (strlen($row->quantity) == 0) $quantity = "무제한";
					elseif ($row->quantity > 0) $quantity = "$row->quantity";
					elseif ($row->quantity < 1) $quantity = "<font color=red>품절</font>";

					$codename="";
					$codeA = substr($row->productcode,0,3);
					$codeB = substr($row->productcode,3,3);
					$codeC = substr($row->productcode,6,3);
					$codeD = substr($row->productcode,9,3);
					if($codeB=="000") $codeB="";
					if($codeC=="000") $codeC="";
					if($codeD=="000") $codeD="";
					$codename.=$code_name[$codeA."000000000"];
					if(strlen($code_name[$codeA.$codeB."000000"])>0) {
						$codename.=" > ".$code_name[$codeA.$codeB."000000"];
					}
					if(strlen($code_name[$codeA.$codeB.$codeC."000"])>0) {
						$codename.=" > ".$code_name[$codeA.$codeB.$codeC."000"];
					}

					if(strlen($code_name[$codeA.$codeB.$codeC.$codeD])>0) {
						$codename.=" > ".$code_name[$codeA.$codeB.$codeC.$codeD];
					}

					echo "<tr>\n";
					echo "	<TD colspan=\"".$colspan."\" background=\"images/table_con_line.gif\"></TD>\n";
					echo "</tr>\n";
					echo "<tr align=center>";
					echo "	<TD class=\"td_con2\">".$number."</td>\n";
					if($vendercnt>0) {
						echo "	<TD class=\"td_con1\"><B>".(strlen($venderlist[$row->vender]->vender)>0?"<a href=\"javascript:viewVenderInfo(".$row->vender.")\">".$venderlist[$row->vender]->id."</a>":"-")."</B></td>\n";
					}
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
					echo "	<TD align=left class=\"td_con1\" style=\"word-break:break-all;\"><span style=\"font-size:8pt; letter-spacing:-0.5pt;\"><span class=\"font_orange\"><b>카테고리 : </b></span>".$codename."</span>";
					echo "		<br><img src=\"images/producttype".($row->assembleuse=="Y"?"y":"n").".gif\" border=\"0\" align=\"absmiddle\" hspace=\"2\"><font color=#3D3D3D>".$row->productname.($row->selfcode?"-".$row->selfcode:"").($row->addcode?"-".$row->addcode:"")."</font>";
					echo "	</td>\n";
					echo "	<TD align=right class=\"td_con1\"><img src=\"images/won_icon.gif\" border=\"0\" style=\"margin-right:2px;\"><span class=\"font_orange\">".number_format($row->sellprice)."</span><br><img src=\"images/reserve_icon.gif\" border=\"0\" style=\"margin-right:2px;\">".($row->reservetype!="Y"?number_format($row->reserve):$row->reserve."%")."</TD>\n";
					echo "	<TD class=\"td_con1\"><input type=text name=quantity[] size=3 maxlength=8 class=\"input\"></td>";
					echo "	<TD class=\"td_con1\"><input type=checkbox name=display[] value=\"Y\"";
					if($row->display=="N") echo " checked";
					echo "	style=\"BORDER:none\"></td>\n";
					echo "	<TD class=\"td_con1\"><input type=checkbox name=delcheck[] value=\"".$row->productcode."\" style=\"BORDER:none\"></td>\n";
					echo "	<TD class=\"td_con1\"><a href=\"javascript:ProductInfo('".$row->productcode."');\"><img src=\"images/icon_newwin1.gif\" border=\"0\"></a></td>\n";
					echo "<input type=hidden name=productcode[] value=\"".$row->productcode."\">";
					echo "<input type=hidden name=display2[] value=\"".$row->display."\">";
					echo "</tr>\n";
					$cnt++;
				}
				mysql_free_result($result);

				if ($cnt==0) {
					$page_numberic_type="";
					echo "<tr><TD colspan=\"".$colspan."\" background=\"images/table_con_line.gif\"></TD></tr><tr><td class=\"td_con2\" colspan=".$colspan." align=center>검색된 상품이 존재하지 않습니다.</td></tr>";
				}
			} else {
				$page_numberic_type="";
				echo "<tr><TD colspan=\"".$colspan."\" background=\"images/table_con_line.gif\"></TD></tr><tr><td class=\"td_con2\" colspan=".$colspan." align=center>검색된 상품이 없습니다.</td></tr>";
			}
?>
			<TR>
				<TD background="images/table_top_line.gif" colspan="<?=$colspan?>"></TD>
			</TR>
			</TABLE>
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
			echo "	<td width=\"100%\" height=\"30\" background=\"images/blueline_bg.gif\" align=right>\n";
			echo "	상품일괄 선택/해제 (<input type=checkbox name=allcheck onclick=\"CheckAll('".$cnt."');\"><B>진열안함</B> / <input type=checkbox name=allcheck2 onclick=\"CheckAll2('".$cnt."');\"><B>삭제</B>)";
			echo "	</td>\n";
			echo "</tr>\n";
			echo "<tr>\n";
			echo "	<td width=\"100%\" height=\"30\" background=\"images/blueline_bg.gif\" align=center>\n";
			echo "		".$a_div_prev_page.$a_prev_page.$print_page.$a_next_page.$a_div_next_page;
			echo "	</td>\n";
			echo "</tr>\n";
		}
?>
		<tr>
			<td style="padding:10px;BORDER-top:#eeeeee 2px solid;" align="center"><a href="javascript:CheckUpdate('<?=$cnt?>');"><img src="images/btn_edit2.gif" border="0"></a>&nbsp;&nbsp;<a href="javascript:CheckDelete('<?=$cnt?>');"><img src="images/btn_del3.gif" border="0"></a></td>
		</tr>
		</table>
		</div>
		</td>
	</tr>
	</table>
	</td>
</tr>
</form>
<form name=form_reg action="product_register.php" method=post>
<input type=hidden name=code>
<input type=hidden name=prcode>
<input type=hidden name=popup>
</form>
<?if($vendercnt>0){?>
<form name=vForm action="vender_infopop.php" method=post>
<input type=hidden name=vender>
</form>
<?}?>
</table>
<?=$onload?>

</body>
</html>