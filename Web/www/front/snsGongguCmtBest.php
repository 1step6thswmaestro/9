<?php
header('Content-Type: text/html; charset=euc-kr'); 
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
$sql = "SELECT A.count,A.pcode ";
$sql .=", (SELECT productname FROM tblproduct B WHERE B.productcode=A.pcode ) productname ";
$sql .=", (SELECT tinyimage FROM tblproduct C WHERE C.productcode=A.pcode ) tinyimage ";
$sql .="FROM tblsnsGongguCmt A ";
$sql .="WHERE c_order=1 AND rqt_state=1 ";
$sql .="ORDER BY count DESC LIMIT 4";
$result=mysql_query($sql,get_db_conn());
?>
 <ul>
<?
while($row=mysql_fetch_object($result)) {
	if(strlen($row->tinyimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)) {
		$width=GetImageSize($Dir.DataDir."shopimages/product/".$row->tinyimage);
		if($width[0]>=130) $width[0]=130;
		else if (strlen($width[0])==0) $width[0]=130;
		$sPdtThumb = "<img src=\"".$Dir.DataDir."shopimages/product/".$row->tinyimage."\" border=\"0\" width=\"".$width[0]."\" class=\"img\">";
	} else {
		$sPdtThumb = "<img src=\"".$Dir."images/no_img.gif\" border=\"0\" WIDTH=130 HEIGHT=100 class=\"img\">";
	}
?>

 <li><a href="../front/productdetail.php?productcode=<?=$row->pcode?>"><?=$sPdtThumb?></a>
 <p class="table_td"><b><?=$row->productname?></b><br><IMG SRC="../images/design/gonggu_order_icon01.gif" WIDTH=36 HEIGHT=18 ALT="" align="absmiddle"><?=$row->count?>°Ç</p>
<?}?>
 </ul>