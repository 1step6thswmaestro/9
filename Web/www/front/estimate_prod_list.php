<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

if($_data->estimate_ok!="Y" && $_data->estimate_ok!="O") {
	echo "<html></head><body onload=\"alert('견적서 기능 선택이 안되었습니다.');history.go(-1);\"></body></html>";exit;
}
?>
<HTML>
<HEAD>
<TITLE><?=$_data->shoptitle?> - 온라인견적서</TITLE>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META http-equiv="X-UA-Compatible" content="IE=5" />
<META name="description" content="<?=(strlen($_data->shopdescription)>0?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<?include($Dir."lib/style.php")?>
<?
$box_img_width= "40";
$box_img_height= "50";
?>
<script language="javascript">
<!--
function AddEstimate(code,pcode,fst){
	var cmd = "";
	var f = document.estimate_prod;
	var row_id = 'TR_'+pcode;
	var save_obj = eval("f.TS_" + pcode);
	var price_obj = eval("f.price_" + pcode);
	var pname_obj	= eval("f.pname_" + pcode);
	var accnt_obj = eval("f.accnt_" + pcode);
	var info = new Array();
	var img_id = 'IMG_'+pcode;
	var img_obj = document.getElementById(img_id);

	if(typeof(save_obj) != 'undefined')
	{
		if(fst == 'N') save_obj.value = 'Y';
		if(fst == 'D') save_obj.value = 'N';

		if(save_obj.value == 'Y')
		{
			if(parent.product_all[eval(pcode)].quantity==0)
			{
				alert("품절된 상품입니다.");
				return;
			}
			document.getElementById(row_id).setAttribute('bgColor','#F3F3F3');
			cmd = 'ADD';
			info["price"] = price_obj.value;
			info["pname"] = pname_obj.value;
			info["accnt"] = accnt_obj.value;

			save_obj.value = 'N';
			img_obj.src = '<?=$Dir?>images/estimate/btn_revocation.gif';
		}
		else
		{
			document.getElementById(row_id).setAttribute('bgColor','');
			cmd = 'DEL';
			save_obj.value = 'Y';
			img_obj.src = '<?=$Dir?>images/estimate/btn_choice.gif';
		}
	}
	else
	{
		cmd = 'DEL';
	}

	if(fst != 'N')
	{
		parent.Prod_proc(code,pcode,info,cmd);
	}
}

function SelList_Remark()
{
	var frm = document.FRM_Search;
	var p_id_TBL = 'TBL_' + frm.code.value;
	var p_obj_TBL = parent.document.getElementById(p_id_TBL);
	var p_len_row = p_obj_TBL.rows.length;
	for(var i=0; i<p_len_row; i++)
	{
		var pcode = p_obj_TBL.rows[i].id.replace(/TR_/,'');
		AddEstimate(frm.code.value,pcode,'N')
	}
}
//-->
</script>
<table border="0" cellpadding="0" cellspacing="0" width="100%">
<form name="FRM_Search" method="GET">
<input type="hidden" name="code" value="<?=$code?>">
</form>
<form name="estimate_prod">
<tr>
	<td>
	<table border="0" cellpadding="3" cellspacing="0" width="100%">
	<col width="<?=$box_img_width?>">
	<col width="">
	<col width="53">
	<?
	if($code) {
		$codeA = substr($code,0,3);
		$codeB = substr($code,3,6);
		$codeC = substr($code,6,3);
		$codeD = substr($code,9,3);

		$likecode=$codeA;
		if($codeB!="000") $likecode.=$codeB;
		if($codeC!="000") $likecode.=$codeC;
		if($codeD!="000") $likecode.=$codeD;

		$sql = "SELECT a.productcode,a.productname,a.sellprice,a.production,a.quantity,a.tinyimage,a.etctype,a.selfcode ";
		$sql.= "FROM tblproduct AS a ";
		$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
		$sql.= "WHERE a.productcode LIKE '".$likecode."%' AND a.display='Y' ";
		$sql.= "AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";
		$sql.= "ORDER BY a.productcode ";
		$result=mysql_query($sql,get_db_conn());

		$ccount = 0;
		while($row = mysql_fetch_object($result))
		{
			if (strlen(dickerview($row->etctype,$row->sellprice,1))==0) {
				echo "<tr id=\"TR_".$row->productcode."\" bgcolor=\"#FFFFFF\">\n";
				echo "	<td>".(file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage) && strlen($row->tinyimage)>0?"<img src=\"".$Dir.DataDir."shopimages/product/".$row->tinyimage."\" width=\"".$box_img_width."\" height=\"".$box_img_height."\">":"<img src=\"".$Dir."images/no_img.gif\" width=\"".$box_img_width."\" height=\"".$box_img_height."\">")."</td>\n";
				echo "	<td valign=\"top\">".$row->productname."<br><img src=\"".$Dir."images/common/won_icon.gif\" border=0 align=absmiddle> ".number_format($row->sellprice)."원</td>\n";
				echo "	<td>\n";
				echo "		<a href=\"javascript:AddEstimate('".$code."','".$row->productcode."','Y');\"><img id=\"IMG_".$row->productcode."\" src=\"".$Dir."images/estimate/btn_choice.gif\"></a>\n";
				echo "		<input type=\"hidden\" name=\"TS_".$row->productcode."\" value=\"Y\">\n";
				echo "		<input type=\"hidden\" name=\"price_".$row->productcode."\" value=\"".$row->sellprice."\">\n";
				echo "		<input type=\"hidden\" name=\"pname_".$row->productcode."\" value=\"".$row->productname."\">\n";
				echo "		<input type=\"hidden\" name=\"accnt_".$row->productcode."\" value=\"1\">\n";
				echo "	</td>\n";
				echo "</tr>\n";
				echo "<tr>\n";
				echo "	<td height=\"1\" colspan=\"3\" bgcolor=\"#CCCCCC\"></td>\n";
				echo "<tr>\n";
				$ccount++;
			}
		}
		if($ccount==0)
		{
			echo "<tr bgcolor=\"#FFFFFF\">\n";
			echo "	<td height=\"100\" align=\"center\">상품이 존재하지 않습니다.</td>\n";
			echo "</tr>\n";
		}
		mysql_free_result($result);
	} else {
		echo "<tr bgcolor=\"#FFFFFF\">\n";
		echo "	<td height=\"100\" align=\"center\">카테고리 정보가 부족합니다.</td>\n";
		echo "</tr>\n";
	}
	?>
	</table>
	</td>
</tr>
</form>
</table>
<script language='javascript'>
SelList_Remark();
</script>