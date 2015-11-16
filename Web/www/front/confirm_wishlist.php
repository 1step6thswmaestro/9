<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

if(strlen($_ShopInfo->getMemid())==0) {
	echo "<html></head><body onload=\"window.close()\"></body></html>";exit;
}


$productcode=$_POST["productcode"];
$opts=$_POST["opts"];
$option1=$_POST["option1"];
$option2=$_POST["option2"];

if (empty($opts))  $opts="0";
if (empty($option1))  $option1=0;
if (empty($option2))  $option2=0;

if(strlen($productcode)==18) {
	$codeA=substr($productcode,0,3);
	$codeB=substr($productcode,3,3);
	$codeC=substr($productcode,6,3);
	$codeD=substr($productcode,9,3);

	$sql = "SELECT * FROM tblproductcode WHERE codeA='".$codeA."' AND codeB='".$codeB."' ";
	$sql.= "AND codeC='".$codeC."' AND codeD='".$codeD."' ";
	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		if($row->group_code=="NO") {	//숨김 분류
			echo "<html></head><body onload=\"alert('판매가 종료된 상품입니다.');window.close()\"></body></html>";exit;
		} else if(strlen($row->group_code)>0 && $row->group_code!="ALL" && $row->group_code!=$_ShopInfo->getMemgroup()) {	//그룹회원만 접근
			echo "<html></head><body onload=\"alert('해당 분류의 접근 권한이 없습니다.');window.close();\"></body></html>";exit;
		}
	} else {
		echo "<html></head><body onload=\"alert('해당 분류가 존재하지 않습니다.');window.close();\"></body></html>";exit;
	}
	mysql_free_result($result);

	$sql = "SELECT productname,quantity,display,option1,option2,option_quantity,etctype,group_check FROM tblproduct ";
	$sql.= "WHERE productcode='".$productcode."' ";
	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		if($row->display!="Y") {
			$errmsg="해당 상품은 판매가 되지 않는 상품입니다.\\n";
		}
		if($row->group_check!="N") {
			if(strlen($_ShopInfo->getMemid())>0) {
				$sqlgc = "SELECT COUNT(productcode) AS groupcheck_count FROM tblproductgroupcode ";
				$sqlgc.= "WHERE productcode='".$productcode."' ";
				$sqlgc.= "AND group_code='".$_ShopInfo->getMemgroup()."' ";
				$resultgc=mysql_query($sqlgc,get_db_conn());
				if($rowgc=@mysql_fetch_object($resultgc)) {
					if($rowgc->groupcheck_count<1) {
						$errmsg="해당 상품은 지정 등급 전용 상품입니다.\\n";
					}
					@mysql_free_result($resultgc);
				} else {
					$errmsg="해당 상품은 지정 등급 전용 상품입니다.\\n";
				}
			} else {
				$errmsg="해당 상품은 회원 전용 상품입니다.\\n";
			}
		}
		if(strlen($errmsg)==0) {
			if(strlen(dickerview($row->etctype,0,1))>0) {
				$errmsg="해당 상품은 판매가 되지 않습니다.\\n";
			}
		}
		if(empty($option1) && strlen($row->option1)>0)  $option1=1;
		if(empty($option2) && strlen($row->option2)>0)  $option2=1;
	} else {
		$errmsg="해당 상품이 존재하지 않습니다.\\n";
	}
	mysql_free_result($result);

	if(strlen($errmsg)>0) {
		echo "<html></head><body onload=\"alert('".$errmsg."');window.close()\"></body></html>";
		exit;
	}

	$sql = "SELECT COUNT(*) as totcnt FROM tblwishlist WHERE id='".$_ShopInfo->getMemid()."' ";
	$result2=mysql_query($sql,get_db_conn());
	$row2=mysql_fetch_object($result2);
	$totcnt=$row2->totcnt;
	mysql_free_result($result2);
	$maxcnt=100;
	if($totcnt>=$maxcnt) {
		$sql = "SELECT b.productcode FROM tblwishlist a, tblproduct b ";
		$sql.= "LEFT OUTER JOIN tblproductgroupcode c ON b.productcode=c.productcode ";
		$sql.= "WHERE a.id='".$_ShopInfo->getMemid()."' AND a.productcode=b.productcode ";
		$sql.= "AND b.display='Y' ";
		$sql.= "AND (b.group_check='N' OR c.group_code='".$_ShopInfo->getMemgroup()."') ";
		$sql.= "GROUP BY b.productcode ";
		$result2=mysql_query($sql,get_db_conn());
		$i=0;
		$wishprcode="";
		while($row2=mysql_fetch_object($result2)) {
			$wishprcode.="'".$row2->productcode."',";
			$i++;
		}
		mysql_free_result($result2);
		$totcnt=$i;
		$wishprcode=substr($wishprcode,0,-1);
		if(strlen($wishprcode)>0) {
			$sql = "DELETE FROM tblwishlist WHERE id='".$_ShopInfo->getMemid()."' ";
			$sql.= "AND productcode NOT IN (".$wishprcode.") ";
			mysql_query($sql,get_db_conn());
			echo "<html></head><body onload=\"if(typeof(opener.setFollowFunc)!='undefined') { opener.setFollowFunc('Wishlist','selectmenu'); }\"></body></html>";
		}
	}
	if($totcnt<$maxcnt) {
		$sql = "SELECT COUNT(*) as cnt FROM tblwishlist WHERE id='".$_ShopInfo->getMemid()."' ";
		$sql.= "AND productcode='".$productcode."' AND opt1_idx='".$option1."' ";
		$sql.= "AND opt2_idx='".$option2."' AND optidxs='".$opts."' ";
		$result2=mysql_query($sql,get_db_conn());
		$row2=mysql_fetch_object($result2);
		$cnt=$row2->cnt;
		mysql_free_result($result2);
		if($cnt<=0) {
			$sql = "INSERT tblwishlist SET ";
			$sql.= "id			= '".$_ShopInfo->getMemid()."', ";
			$sql.= "productcode	= '".$productcode."', ";
			$sql.= "opt1_idx	= '".$option1."', ";
			$sql.= "opt2_idx	= '".$option2."', ";
			$sql.= "optidxs		= '".$opts."', ";
			$sql.= "date		= '".date("YmdHis")."' ";
			mysql_query($sql,get_db_conn());
		} else {
			$sql = "UPDATE tblwishlist SET date='".date("YmdHis")."' ";
			$sql.= "WHERE id='".$_ShopInfo->getMemid()."' ";
			$sql.= "AND productcode='".$productcode."' ";
			$sql.= "AND opt1_idx='".$option1."' AND opt2_idx='".$option2."' AND optidxs='".$opts."' ";
			mysql_query($sql,get_db_conn());
		}
		echo "<html></head><body onload=\"if(typeof(opener.setFollowFunc)!='undefined') { opener.setFollowFunc('Wishlist','selectmenu'); }\"></body></html>";
	} else {
		echo "<html></head><body onload=\"alert('WishList에는 ".$maxcnt."개 까지만 등록이 가능합니다.\\n\\nWishList에서 다른 상품을 삭제하신 후 등록하시기 바랍니다.');window.close();\"></body></html>";exit;
	}
} else {
	echo "<html></head><body onload=\"window.close()\"></body></html>";exit;
}
?>

<html>
<head>
<title>위시리스트 상품추가</title>
<meta http-equiv="CONTENT-TYPE" content="text/html;charset=EUC-KR">
<META http-equiv="X-UA-Compatible" content="IE=5" >
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<style>
td	{font-family:"굴림,돋움";color:#4B4B4B;font-size:12px;line-height:17px;}
BODY,DIV,form,TEXTAREA,center,option,pre,blockquote {font-family:Tahoma;color:000000;font-size:9pt;}

A:link    {color:#635C5A;text-decoration:none;}
A:visited {color:#545454;text-decoration:none;}
A:active  {color:#5A595A;text-decoration:none;}
A:hover  {color:#545454;text-decoration:underline;}
.input{font-size:12px;BORDER-RIGHT: #DCDCDC 1px solid; BORDER-TOP: #C7C1C1 1px solid; BORDER-LEFT: #C7C1C1 1px solid; BORDER-BOTTOM: #DCDCDC 1px solid; HEIGHT: 18px; BACKGROUND-COLOR: #ffffff;padding-top:2pt; padding-bottom:1pt; height:19px}
.select{color:#444444;font-size:12px;}
.textarea {border:solid 1;border-color:#e3e3e3;font-family:돋음;font-size:9pt;color:333333;overflow:auto; background-color:transparent}
</style>
<script type="text/javascript">
<!--
window.moveTo(10,10);
window.resizeTo(392,265);

function go_wishlist() {
	if(typeof(opener)=="object") {
		opener.location.href="<?=$Dir.FrontDir?>wishlist.php";
		window.close();
	} else {
		window.open("<?=$Dir.FrontDir?>wishlist.php");
		window.close();
	}
}
//-->
</SCRIPT>
</head>

<body topmargin="0" leftmargin="0" rightmargin="0" marginheight="0" marginwidth="0">
<table cellpadding="0" cellspacing="0" width="100%">
<tr>
	<td><IMG src="<?=$Dir?>images/common/confirm_wishlist_title.gif" border="0"></td>
</tr>
<tr>
	<td align="center" style="padding-top:10px;padding-bottom:10px;"><img src="<?=$Dir?>images/common/confirm_wishlist_text01.gif" border="0"></td>
</tr>
<tr>
	<td><hr size="1" noshade color="#F3F3F3"></td>
</tr>
<tr>
	<td align="center"><A HREF="javascript:go_wishlist()"><img src="<?=$Dir?>images/common/confirm_wishlist_btn1.gif" border="0" vspace="5"></a><A HREF="javascript:window.close();"><img src="<?=$Dir?>images/common/confirm_wishlist_btn2.gif" border="0" hspace="5" vspace="5"></a></td>
</tr>
</table>
</body>
</html>