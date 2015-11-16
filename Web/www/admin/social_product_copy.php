<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
INCLUDE ("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "go-1";
$MenuCode = "gong";
if (!$_usersession->isAllowedTask($PageCode)) {
	INCLUDE ("AccessDeny.inc.php");
	exit;
}
#########################################################

$mode=$_POST["mode"];
$cproductcode=$_POST["cproductcode"];
$gong_seq=$_POST["gong_seq"];

if (sizeof($cproductcode)==0) {
	echo "<script>alert('잘못된 접근입니다.');window.close();</script>";
	exit;
}
$code=substr($cproductcode, 0,12);

if ($mode=="copy") {

	$sql = "SELECT type FROM tblproductcode WHERE codeA='".substr($copycode,0,3)."' ";
	$sql.= "AND codeB='".substr($copycode,3,3)."' ";
	$sql.= "AND codeC='".substr($copycode,6,3)."' AND codeD='".substr($copycode,9,3)."' ";
	$result=mysql_query($sql,get_db_conn());
	$row=mysql_fetch_object($result);
	mysql_free_result($result);
	if(!$row || !ereg("X",$row->type)) {
		echo "<script>alert('상품카테고리 선택이 잘못되었습니다.');history.go(-1);</script>";
		exit;
	}

	$social_code = false;
	if(substr($row->type,0,1) =="S"){
		$social_code = true;
	}

	$vender_prcodelist=array();
	if (strlen($cproductcode)==18) {
		$sql = "SELECT * FROM tblproduct WHERE productcode = '".$cproductcode."'";
		$result = mysql_query($sql,get_db_conn());
		if ($row=mysql_fetch_object($result)) {
			$sql = "SELECT productcode FROM tblproduct WHERE productcode LIKE '".$copycode."%' ";
			$sql.= "ORDER BY productcode DESC LIMIT 1 ";
			$result = mysql_query($sql,get_db_conn());
			if ($rows = mysql_fetch_object($result)) {
				$newproductcode = substr($rows->productcode,12)+1;
				$newproductcode = substr("000000".$newproductcode,strlen($newproductcode));
			} else {
				$newproductcode = "000001";
			}
			mysql_free_result($result);

			$path = $Dir.DataDir."shopimages/product/";
			if (strlen($row->maximage)>0) {
				$maximage=$copycode.$newproductcode.".".strtolower(substr($row->maximage,strlen($row->maximage)-3,3));
				if (file_exists("$path$row->maximage")==true) {
					if ($mode=="move") rename("$path$row->maximage","$path$maximage");
					else copy("$path$row->maximage","$path$maximage");
				}
			} else $maximage="";
			if (strlen($row->minimage)>0) {
				$minimage=$copycode.$newproductcode."2.".strtolower(substr($row->minimage,strlen($row->minimage)-3,3));
				if (file_exists("$path$row->minimage")==true) {
					if ($mode=="move") rename("$path$row->minimage","$path$minimage");
					else copy("$path$row->minimage","$path$minimage");
				}
			} else $minimage="";
			if (strlen($row->tinyimage)>0) {
				$tinyimage=$copycode.$newproductcode."3.".strtolower(substr($row->tinyimage,strlen($row->tinyimage)-3,3));
				if (file_exists("$path$row->tinyimage")==true) {
					if ($mode=="move") rename("$path$row->tinyimage","$path$tinyimage");
					else copy("$path$row->tinyimage","$path$tinyimage");
				}
			} else $tinyimage="";
			if (strlen($row->quantity)==0) $quantity="NULL";
			else $quantity=$row->quantity;

			$productname = mysql_escape_string($row->productname);
			$production = mysql_escape_string($row->production);
			$madein = mysql_escape_string($row->madein);
			$model = mysql_escape_string($row->model);
			$tempkeyword = mysql_escape_string($row->keyword);
			$addcode = mysql_escape_string($row->addcode);
			$userspec = mysql_escape_string($row->userspec);
			$option1 = mysql_escape_string($row->option1);
			$option2 = mysql_escape_string($row->option2);
			$content = mysql_escape_string($row->content);
			$selfcode = mysql_escape_string($row->selfcode);
			$assembleproduct = mysql_escape_string($row->assembleproduct);

			$sql = "INSERT tblproduct SET ";
			$sql.= "productcode		= '".$copycode.$newproductcode."', ";
			$sql.= "productname		= '".$productname."', ";
			$sql.= "assembleuse		= '".$row->assembleuse."', ";
			$sql.= "assembleproduct	= '".$row->assembleproduct."', ";
			$sql.= "sellprice		= ".$row->sellprice.", ";
			$sql.= "consumerprice	= ".$row->consumerprice.", ";
			$sql.= "buyprice		= ".$row->buyprice.", ";
			$sql.= "reserve			= '".$row->reserve."', ";
			$sql.= "reservetype		= '".$row->reservetype."', ";
			$sql.= "production		= '".$production."', ";
			$sql.= "madein			= '".$madein."', ";
			$sql.= "model			= '".$model."', ";
			$sql.= "brand			= '".$row->brand."', ";
			$sql.= "opendate		= '".$row->opendate."', ";
			$sql.= "selfcode		= '".$row->selfcode."', ";
			$sql.= "bisinesscode	= '".$row->bisinesscode."', ";
			$sql.= "quantity		= ".$quantity.", ";
			$sql.= "group_check		= '".$row->group_check."', ";
			$sql.= "keyword			= '".$tempkeyword."', ";
			$sql.= "addcode			= '".$addcode."', ";
			$sql.= "userspec		= '".$userspec."', ";
			$sql.= "maximage		= '".$maximage."', ";
			$sql.= "minimage		= '".$minimage."', ";
			$sql.= "tinyimage		= '".$tinyimage."', ";
			$sql.= "option_price	= '".$row->option_price."', ";
			$sql.= "option_quantity	= '".$row->option_quantity."', ";
			$sql.= "option1			= '".$option1."', ";
			$sql.= "option2			= '".$option2."', ";
			$sql.= "etctype			= '".$row->etctype."', ";
			$sql.= "deli			= '".$row->deli."', ";
			$sql.= "package_num		= '".(int)$row->package_num."', ";
			$sql.= "display			= '".$row->display."', ";
			if ($newtime=="Y")
				$sql.= "date		= '".date("YmdHis")."', ";
			else
				$sql.= "date		= '".$row->date."', ";
			$sql.= "vender			= '".$row->vender."', ";
			$sql.= "regdate			= now(), ";
			$sql.= "modifydate		= now(), ";
			$sql.= "content			= '".$content."', ";
			$sql.= "sns_state		= '".$row->sns_state."', ";
			$sql.= "present_state	= '".$row->present_state."', ";
			$sql.= "pester_state	= '".$row->pester_state."', ";
			$sql.= "sns_reserve1		= '".$row->sns_reserve1."', ";
			$sql.= "sns_reserve1_type	= '".$row->sns_reserve1_type."', ";
			$sql.= "sns_reserve2		= '".$row->sns_reserve2."', ";
			$sql.= "sns_reserve2_type	= '".$row->sns_reserve2_type."', ";
			$sql.= "first_reserve		= '".$row->first_reserve."', ";
			$sql.= "first_reserve_type	= '".$row->first_reserve_type."', ";
			$sql.= "social_chk		= '".(($social_code)? "Y":$row->social_chk)."', ";
			$sql.= "img_type		= '".$row->img_type."', ";
			$sql.= "gonggu_product	= '".(($social_code)? "Y":$row->gonggu_product)."'";
			/*
			$sql.= "gonggu_product	= '".(($social_code)? "Y":$row->gonggu_product)."', ";
			$sql.= "prdt_type		= '".$row->prdt_type."', ";
			$sql.= "prdt_style		= '".$row->prdt_style."', ";
			$sql.= "prdt_color		= '".$row->prdt_color."', ";
			$sql.= "prdt_layout		= '".$row->prdt_layout."', ";
			$sql.= "sample_url		= '".$row->sample_url."' ";
			*/
			$insert = mysql_query($sql,get_db_conn());
			$insert_pridx = mysql_insert_id();
			$fromproductcodes.="|".$cproductcode;
			$copyproductcodes.="|".$copycode.$newproductcode;

			if($row->vender>0) {
				$vender_prcodelist[$row->vender]["IN"][]=$copycode.$newproductcode;
			}


			if($row->group_check=="Y") {
				$sql = "INSERT INTO tblproductgroupcode SELECT '".$copycode.$newproductcode."', group_code FROM tblproductgroupcode WHERE productcode = '".$cproductcode."' ";
				mysql_query($sql,get_db_conn());
			}
			if($row->assembleuse=="Y") { //코디/조립 상품일 경우
				$sql = "INSERT INTO tblassembleproduct ";
				$sql.= "SELECT '".$copycode.$newproductcode."', assemble_type, assemble_title, assemble_pridx, assemble_list FROM tblassembleproduct ";
				$sql.= "WHERE productcode='".$cproductcode."' ";
				mysql_query($sql,get_db_conn());

				$sql = "SELECT assemble_pridx FROM tblassembleproduct ";
				$sql.= "WHERE productcode = '".$cproductcode."' ";
				$result = mysql_query($sql,get_db_conn());
				if($row = @mysql_fetch_object($result)) {
					if(strlen(str_replace("","",$row->assemble_pridx))>0) {
						$sql = "UPDATE tblproduct SET ";
						$sql.= "assembleproduct = CONCAT(assembleproduct,',".$copycode.$newproductcode."') ";
						$sql.= "WHERE pridx IN ('".str_replace("","','",$row->assemble_pridx)."') ";
						$sql.= "AND assembleuse != 'Y' ";
						mysql_query($sql,get_db_conn());
					}
				}
				mysql_free_result($result);
			} else {
				$sql = "UPDATE tblproduct SET assembleproduct = '' ";
				$sql.= "WHERE productcode='".$copycode.$newproductcode."'";
				mysql_query($sql,get_db_conn());
			}

			$log_content = "## 상품복사입력 ## - 상품코드 ".$cproductcode." => ".$copycode.$newproductcode." - 상품명 : ".$productname;
			ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);
		}

		$sql = "UPDATE tblsnsGongguCmt SET reg_prdt = '".$copycode.$newproductcode."' WHERE seq='".$gong_seq."'";
		mysql_query($sql,get_db_conn());

		//입점업체 상품 관련 처리
		if(count($vender_prcodelist)>0) {
			$tmpvender=$vender_prcodelist;
			while(list($vender,$prarr)=each($tmpvender)) {
				unset($tmpcodeA);
				for($kk=0;$kk<count($prarr["IN"]);$kk++) {
					//insert 처리
					setVenderDesignInsert($vender, $prarr["IN"][$kk]);

					if(strlen($prarr["OUT"][$kk])==18) {
						//move 처리
						$tmpcodeA[substr($prarr["OUT"][$kk],0,3)]=true;
						setVenderThemeSpecialUpdate($vender, $prarr["IN"][$kk], $prarr["OUT"][$kk]);
					}
				}
				//미니샵 상품수 업데이트 (진열된 상품만)
				$sql="SELECT COUNT(*) as prdt_allcnt,COUNT(IF(display='Y',1,NULL)) as prdt_cnt FROM tblproduct ";
				$sql.="WHERE vender='".$vender."' ";
				$result=mysql_query($sql,get_db_conn());
				$row=mysql_fetch_object($result);
				$prdt_allcnt=(int)$row->prdt_allcnt;
				$prdt_cnt=(int)$row->prdt_cnt;
				mysql_free_result($result);

				setVenderCountUpdate($prdt_allcnt, $prdt_cnt, $vender);

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
					$sql.= "AND vender='".$vender."' ";
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

							$imagename = $Dir.DataDir."shopimages/vender/".$vender."_CODE10_".$key.".gif";
							@unlink($imagename);
						}
						$str_codeA=substr($str_codeA,0,-1);
						$str_codeA=ereg_replace(',','\',\'',$str_codeA);
						setVenderDesignDelete($str_codeA, $vender);
					}
				}
			}
		}

		delProductMultiImg($mode,substr($fromproductcodes,1),substr($copyproductcodes,1));

		$onload="<script>alert('[".ereg_replace("\"","",$copycode_name)."]으로 복사되었습니다.');</script>";
	}

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

function CopyCodeSelect() {
	window.open("product_copycodeselect.php","","height=300,width=420,scrollbars=no,resizable=no");
}

function Copy(gbn) {
	var gbn_name = "복사";
	if (gbn=="move") gbn_name = "이동";
	if (document.form1.copycode.value.length==0) {
		alert(gbn_name+"할 카테고리를 선택하세요.");
		CopyCodeSelect();
		return;
	}
	if (document.form1.copycode.value==document.form1.oldcode.value) {
		alert(gbn_name+"할 카테고리가 이전카테고리와 같습니다.");
		CopyCodeSelect();
		return;
	}
	if (confirm("선택된 카테고리를 "+gbn_name+"하시겠습니까?")) {
		document.form1.mode.value=gbn;
		document.form1.submit();
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

</script>

<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%" style="table-layout:fixed">
<tr>
	<td width="100%" bgcolor="#FFFFFF" style="color:#0099CC;font-weight:bold;font-size:12px;">공동구매 상품 복사</td>
</tr>
<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post>
<input type=hidden name=mode>
<input type=hidden name=code value="<?=$code?>">
<input type=hidden name=cproductcode value="<?=$cproductcode?>">
<input type=hidden name=gong_seq value="<?=$gong_seq?>">
<tr>
	<td width="100%" height="100%" valign="top">
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td width="100%" valign="top">
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td width="100%">
			<TABLE border="0" cellSpacing="0" cellPadding="0" width="100%" style="table-layout:fixed">
<?
			$colspan=6;
			if($vendercnt>0) $colspan++;
?>
			<?if($vendercnt>0){?>
			<col width=70></col>
			<?}?>
			<col width=50></col>
			<col width=></col>
			<col width=70></col>
			<col width=45></col>
			<col width=45></col>
			<col width=45></col>
			<TR>
				<TD colspan="<?=$colspan?>" background="images/table_top_line.gif"></TD>
			</TR>
			<TR align="center">
				<?if($vendercnt>0){?>
				<TD class="table_cell">입점업체</TD>
				<?}?>
				<TD class="table_cell1" colspan="2">상품명/진열코드/특이사항</TD>
				<TD class="table_cell1">판매가격</TD>
				<TD class="table_cell1">수량</TD>
				<TD class="table_cell1">상태</TD>
				<TD class="table_cell1">수정</TD>
			</TR>
<?
			if (strlen($cproductcode)==18) {
				$sql = "SELECT option_price, productcode,productname,production,sellprice,consumerprice, ";
				$sql.= "buyprice,quantity,reserve,reservetype,addcode,display,vender,tinyimage,selfcode,assembleuse ";
				$sql.= "FROM tblproduct WHERE productcode ='".$cproductcode."' ";
				$result = mysql_query($sql,get_db_conn());
				$t_count = mysql_num_rows($result);
				if($row=mysql_fetch_object($result)) {
					echo "<tr>\n";
					echo "	<TD colspan=\"".$colspan."\" background=\"images/table_con_line.gif\"></TD>\n";
					echo "</tr>\n";
					echo "<tr align=\"center\">\n";
					if($vendercnt>0) {
						echo "	<TD class=\"td_con\"><B>".(strlen($venderlist[$row->vender]->vender)>0?"<a href=\"javascript:viewVenderInfo(".$row->vender.")\">".$venderlist[$row->vender]->id."</a>":"-")."</B></td>\n";
					}
					echo "<TD class=\"td_con1\">";
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
					echo "	<TD class=\"td_con1\" align=\"left\" style=\"word-break:break-all;\"><img src=\"images/producttype".($row->assembleuse=="Y"?"y":"n").".gif\" border=\"0\" align=\"absmiddle\" hspace=\"2\">".$row->productname.($row->selfcode?"-".$row->selfcode:"").($row->addcode?"-".$row->addcode:"")."&nbsp;</td>\n";
					echo "	<TD align=right class=\"td_con1\"><img src=\"images/won_icon.gif\" border=\"0\" style=\"margin-right:2px;\"><span class=\"font_orange\">".number_format($row->sellprice)."</span><br><img src=\"images/reserve_icon.gif\" border=\"0\" style=\"margin-right:2px;\">".($row->reservetype!="Y"?number_format($row->reserve):$row->reserve."%")."</TD>\n";
					echo "	<TD class=\"td_con1\">";
					if (strlen($row->quantity)==0) echo "무제한";
					else if ($row->quantity<=0) echo "<span class=\"font_orange\"><b>품절</b></span>";
					else echo $row->quantity;
					echo "	</TD>\n";
					echo "	<TD class=\"td_con1\">".($row->display=="Y"?"<font color=\"#0000FF\">판매중</font>":"<font color=\"#FF4C00\">보류중</font>")."</td>";
					echo "	<TD class=\"td_con1\"><a href=\"javascript:ProductInfo('".$row->productcode."');\"><img src=\"images/icon_newwin1.gif\" border=\"0\"></a></td>\n";
					echo "</tr>\n";
				}
				mysql_free_result($result);
			}
?>
			<TR>
				<TD height="1" colspan="<?=$colspan?>" background="images/table_top_line.gif"></TD>
			</TR>
			</TABLE>
			</td>
		</tr>
		<tr>
<?
		if ($t_count>0) {
?>
		<input type=hidden name=copycode value="<?=$copycode?>">
		<input type=hidden name=oldcode value="<?=$code?>">
		<tr>
			<td width="100%" bgcolor="#0099CC" style="padding-top:3pt; padding-bottom:3pt;">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td width="100%" class="font_white1">
				<table cellpadding="0" cellspacing="0" width="100%">
				<col width="110"></col>
				<col width=""></col>
				<col width="95"></col>
				<tr>
					<td class="font_white1">&nbsp;&nbsp;이동/복사할 카테고리 : </td>
					<td><input type=text name=copycode_name size=43 style="width:100%;" onfocus="this.blur();alert('[카테고리 선택] 버튼을 이용하셔서 이동/복사시킬 위치의 카테고리를 선택하시기 바랍니다.');" value="<?=htmlspecialchars(stripslashes($copycode_name))?>" class="input" style="width:100%;"></td>
					<td align=center><a href="javascript:CopyCodeSelect();"><img src="images/btn_cateselect.gif" border="0"></a></td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td width="100%" class="font_white1">&nbsp;<input type=checkbox id="idx_newtime" name=newtime value="Y" style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none"> <label style='cursor:hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_newtime>이동/복사된 상품의 등록날짜를 현재시간으로 재설정합니다.</label></td>
			</tr>
			</table>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td width="100%" align=center style="padding-top:6pt; padding-bottom:6pt;"><span style="font-size:8pt; letter-spacing:-0.5pt;" class="font_orange">상품 이동/복사는 <b>최하위 또는 마지막 카테고리에서만 적용</b>됩니다.</span><br>
		<a href="javascript:Copy('copy');"><img src="images/btn_copy.gif" width="136" height="38" border="0" vspace="3"></a>&nbsp;
		</td>
	</tr>
	</table>
	</td>
</tr>

<?
		}
?>
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