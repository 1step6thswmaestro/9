<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
INCLUDE ("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "de-5";
$MenuCode = "nomenu";
if (!$_usersession->isAllowedTask($PageCode)) {
	INCLUDE ("AccessDeny.inc.php");
	exit;
}
#########################################################

$mode=$_POST["mode"];
$code=$_POST["code"];
$body=$_POST["body"];
$added=$_POST["added"];

if((int)$code>0) {
	$sql = "SELECT * FROM tblproductbrand WHERE bridx='".$code."' ";
	$result=mysql_query($sql,get_db_conn());
	$row=mysql_fetch_object($result);
	mysql_free_result($result);
	if(!$row) {
		$onload="<script>alert(\"브랜드 선택이 잘못 되었습니다.\");</script>";
	}
}

if(strlen($onload)==0) {
	if(strlen($mode)>0) {
		if(strlen($code)) {
			if(($code == "전체" || $code == "ALL")) {
				$code = "ALL";
			}
		} else {
			$onload="<script>alert(\"브랜드 선택이 잘못 되었습니다.\");</script>";
		}
	}

	if(strlen($onload)==0) {

		if($added=="Y") {
			$leftmenu="Y";
		} else {
			$leftmenu="N";
		}

		$subject = '상품 브랜드';
		$insertKey = 'brlist';

		// 백업 / 복구
		if ( $mode=="store" OR $mode=="restore" ) {
			$MSG = adminDesingBackup ( $mode, $insertKey, $body, $subject, $code, '', $leftmenu );
			$onload="<script>alert(\"".$MSG."\");</script>";
		}


		if($mode == "update" && strlen($body)>0) {


			$sql = "SELECT COUNT(*) as cnt FROM tbldesignnewpage WHERE type='brlist' AND code='".$code."' ";
			$result=mysql_query($sql,get_db_conn());
			$row=mysql_fetch_object($result);
			if($row->cnt==0) {
				$sql = "INSERT tbldesignnewpage SET ";
				$sql.= "type		= 'brlist', ";
				$sql.= "subject		= '상품 브랜드', ";
				$sql.= "leftmenu	= '".$leftmenu."', ";
				$sql.= "body		= '".$body."', ";
				$sql.= "code		= '".$code."' ";
				mysql_query($sql,get_db_conn());
			} else {
				$sql = "UPDATE tbldesignnewpage SET ";
				$sql.= "leftmenu	= '".$leftmenu."', ";
				$sql.= "body		= '".$body."' ";
				$sql.= "WHERE type='brlist' AND code='".$code."' ";
				mysql_query($sql,get_db_conn());
			}
			mysql_free_result($result);

			$sql="";
			if($leftmenu=="Y") {	//브랜드 화면 개별디자인 적용
				$sql = "UPDATE tblproductbrand SET list_type=CONCAT(SUBSTRING(list_type,1,4),'U') ";
			} else if($leftmenu=="N") {	//브랜드화면 템플릿 적용
				$sql = "UPDATE tblproductbrand SET list_type=SUBSTRING(list_type,1,4) ";
			}
			if(strlen($sql)>0) {
				if((int)$code>0) {
					$sql.= "WHERE bridx='".$code."' ";
					$msg="해당 상품 브랜드 디자인이 ";
				} else {
					$msg="모든 상품 브랜드 디자인이 ";
				}
				mysql_query($sql,get_db_conn());

				if($leftmenu=="Y") {
					$msg.="개별디자인으로 변경되었습니다.";
				} else if($leftmenu=="N") {
					$msg.="기본으로 제공되는 템플릿 디자인으로 변경되었습니다.";
				}
			}
			if((int)$code>0) {
				$onload="<script>alert(\"".$msg."\");</script>";
			} else {
				echo "<script>alert(\"".$msg."\");parent.location.reload();</script>";
				exit;
			}
		} else if($mode=="delete") {
			$sql = "DELETE FROM tbldesignnewpage WHERE type='brlist' AND code='".$code."' ";
			mysql_query($sql,get_db_conn());

			$sql = "UPDATE tblproductbrand SET list_type=SUBSTRING(list_type,1,4) ";
			if((int)$code>0) {
				$sql.= "WHERE bridx='".$code."' ";
				$msg="해당 상품 브랜드 디자인이 기본으로 제공되는 템플릿 디자인으로 변경되었습니다.";
			} else {
				$msg="모든 상품 브랜드 개별디자인이 삭제되었습니다.\\n해당 상품 브랜드의 개별디자인은 삭제되지 않았습니다.";
			}
			mysql_query($sql,get_db_conn());

			if((int)$code>0) {
				$onload="<script>alert(\"".$msg."\");</script>";
			} else {
				echo "<script>alert(\"".$msg."\");parent.location.reload();</script>";
				exit;
			}
		} else if($mode=="clear") {
			$body="";
			$sql = "SELECT body FROM tbldesigndefault WHERE type='brlist' ";
			$result=mysql_query($sql,get_db_conn());
			if($row=mysql_fetch_object($result)) {
				$body=$row->body;
			}
			mysql_free_result($result);
		}
	}
}

if($mode!="clear") {
	$body="";
	$added="";
	if(strlen($code)>0) {
		if(($code == "전체" || $code == "ALL")) {
			$code = "ALL";
		}
	}
	$sql = "SELECT leftmenu, body FROM tbldesignnewpage WHERE type='brlist' AND code='".$code."' ";
	$result = mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		$body=$row->body;
		$added=$row->leftmenu;

		if($added=="Y" && $code == "ALL") {
			$design_default = "LU";
		}
	} else {
		$added="Y";
	}
	mysql_free_result($result);
}

if((int)$code>0) {
	$sql = "SELECT * FROM tblproductbrand WHERE bridx='".$code."' ";
	$result=mysql_query($sql,get_db_conn());
	$row=mysql_fetch_object($result);
	mysql_free_result($result);

	if(strlen($row->list_type)==5) {
		$design_default = "LU";
	} else {
		$design_default = $row->list_type;
	}
}
?>

<? INCLUDE "header.php"; ?>
<style>td {line-height:18pt;}</style>
<script type="text/javascript" src="lib.js.php"></script>
<script>var LH = new LH_create();</script>
<script for=window event=onload>LH.exec();</script>
<script>LH.add("parent_resizeIframe('MainPrdtFrame')");</script>
<SCRIPT LANGUAGE="JavaScript">
<!--
function CheckForm(mode) {
	if(document.form1.code.value.length==0) {
		alert("상품 브랜드를 선택해 주세요.");
		parent.document.form1.up_brandlist.focus();
		return;
	}

	if(mode=="update") {
		if(document.form1.body.value.length==0) {
			alert("상품 브랜드 디자인 내용을 입력하세요.");
			document.form1.body.focus();
			return;
		}
		if(document.form1.code.value=="ALL") {
			msg="모든 ";
		} else {
			msg="해당 ";
		}
		if(document.form1.added.checked==true) {
			if(!confirm(msg+"상품 브랜드 디자인을 개별디자인으로 변경하시겠습니까?")) {
				return;
			}
		} else {
			if(!confirm(msg+"상품 브랜드 디자인을 기본으로 제공되는 템플릿 디자인으로 변경하시겠습니까?\n\n입력하신 개별디자인 소스는 저장됩니다.")) {
				return;
			}
		}
		document.form1.mode.value=mode;
		document.form1.submit();
	} else if(mode=="delete") {
		if(confirm("상품 브랜드 개별디자인을 삭제하시겠습니까?")) {
			document.form1.mode.value=mode;
			document.form1.submit();
		}
	} else if(mode=="clear") {
		alert("기본값 복원 후 [적용하기]를 클릭하세요. 클릭 후 페이지에 적용됩니다.");
		document.form1.mode.value=mode;
		document.form1.submit();
	}


	// 백업
	if(mode=="store") {
		if(confirm("<?=$subject?> 디자인을 백업하시겠습니까?\n\n적용하지 않으셨다면 \"적용하기\"로 적용 하신후 백업하시기 바랍니다.\n기존 저장된 백업소스를 대체합니다.")) {
			document.form1.mode.value=mode;
			document.form1.submit();
		}
	}
	// 복구
	if(mode=="restore") {
		if(confirm("<?=$subject?> 디자인을 백업복구 하시겠습니까?\n\n복구 하게 되면 바로 디자인 적용 됩니다.")) {
			document.form1.mode.value=mode;
			document.form1.submit();
		}
	}

	// 미리보기
	if(mode=="preview") {
		if(document.form1.body.value.length==0) {
			alert("상품 브랜드 디자인 내용을 입력하세요.");
			document.form1.body.focus();
			return;
		}
		document.form1.mode.value='<?=$insertKey?>';
		document.form1.target="preview";
		document.form1.action="designPreview.php";
		document.form1.submit();
		document.form1.target="";
		document.form1.action="<?=$_SERVER[PHP_SELF]?>";
	}

}

<?
echo "parent.document.all[\"preview_img\"].style.display=\"none\";";
if(strlen($design_default)>0) {
	echo "parent.document.all[\"preview_img\"].src=\"images/sample/brand".$design_default.".gif\";\n";
	echo "parent.document.all[\"preview_img\"].style.display=\"\";";
}
?>

//매크로 보기(팝업)
function macroview(){
	window.open("http://www.getmall.co.kr/macro/pages/productbrand_macro.html","productbrand_macro","height=800,width=680,scrollbars=no");
}

//-->
</SCRIPT>

<table cellpadding="0" cellspacing="0" width="100%">
<form name=form1 method=post action="<?=$_SERVER[PHP_SELF]?>">
<input type=hidden name=mode>
<input type=hidden name=code value="<?=$code?>">
<tr>
	<td>
	<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
	<TR>
		<TD><IMG SRC="images/design_productbrand_stitle1.gif" border="0"></TD>
		<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif">
			&nbsp;&nbsp;<a href="javascript:macroview();"><img src="images/btn_macroview.gif" border="0" align="absmiddle" alt="" /></a>&nbsp;&nbsp;<a href="http://www.getmall.co.kr/macro/data/productbrand_design.zip"><img src="images/btn_defaultcode.gif" border="0" align="absmiddle" alt="" />
		</TD>
		<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></TD>
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
		<TD background="images/distribute_04.gif"></TD>
		<TD class="notice_blue"><IMG SRC="images/distribute_img.gif" ></TD>
		<TD width="100%" class="notice_blue" style="line-height:12pt;"><p>1) 매뉴얼의 <b>매크로명령어</b>를 참조하여 디자인 하세요.</span><br>2) [기본값복원]+[적용하기], [삭제하기]하면 기본템플릿으로 변경(개별디자인 소스 삭제)됨 -> 템플릿 메뉴에서 원하는 템플릿 선택.<br>3) 기본값 복원이나 삭제하기 없이도 템플릿 선택하면 개별디자인은 해제됩니다.(개별디자인 소스는 보관됨)<br>
		&nbsp;<b>&nbsp;&nbsp;</b>※ 상품 브랜드 페이지 노출 설정은 <a href="javascript:parent.topframe.GoMenu(4,'product_brand.php');"><span class="font_blue"><b>상품관리 > 카테고리/상품관리 > 상품 브랜드 설정 관리</span></b></a> 에서 설정해 주세요.</p></TD>
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
	<td height="3"></td>
</tr>
<tr>
	<td><textarea name=body style="WIDTH: 100%; HEIGHT: 300px" class="textarea"><?=htmlspecialchars($body)?></textarea><br><input type=checkbox name=added value="Y" <?if($added=="Y")echo"checked";?>> <b><span class="font_orange">적용하기 체크</span>(체크해야만 디자인이 적용됩니다. 미체크시 소스만 보관되고 적용은 되지 않습니다.)</b></td>
</tr>
<tr>
	<td height="20"></td>
</tr>
<tr>
	<td align="center"><a href="javascript:CheckForm('update');"><img src="images/botteon_save.gif" width="113" height="38" border="0"></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:CheckForm('clear');"><img src="images/botteon_bok.gif" width="124" height="38" border="0" hspace="2"></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:CheckForm('delete');"><img src="images/botteon_del.gif" width="113" height="38" border="0" hspace="0"></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:CheckForm('preview');"><img src="images/botteon_prev.gif" width="113" height="38" border="0" hspace="2"></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:CheckForm('store');"><img src="images/botteon_store.gif" border="0" hspace="2" alt="백업하기"></a>&nbsp;&nbsp;&nbsp;<a href="javascript:CheckForm('restore');"><img src="images/botteon_restore.gif" border="0" hspace="2" alt="백업복원하기"></a></td>
</tr>
<tr>
	<td height=10></td>
</tr>
</form>
</table>
<?=$onload?>

</body>
</html>