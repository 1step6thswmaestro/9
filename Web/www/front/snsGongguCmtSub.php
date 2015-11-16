<?php
header('Content-Type: text/html; charset=euc-kr'); 
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$c_seq = $_POST["c_seq"];
?>
<table cellpadding="0" cellspacing="0" width="100%" bgcolor="#FAFAFA" style="margin-top:10px; margin-bottom:10px;">
<?
$sql = "SELECT A.* ,";
$sql .="(SELECT profile_img FROM tblmembersnsinfo B	WHERE A.id=B.id ORDER BY B.regidate DESC limit 1) profile_img ";
$sql .="FROM tblsnsGongguCmt A ";
$sql .="WHERE 1=1 AND c_seq='".$c_seq."' AND seq <> '".$c_seq."' ";
$sql .="ORDER BY c_order ";
$result=mysql_query($sql,get_db_conn());
while($row=mysql_fetch_object($result)) {
	$id = $row->id;
	$comment = $row->comment;
	$sns_date = date("Y-m-d H:i:s", $row->regidate);
	$profile_img = $row->profile_img;
	if(strlen($profile_img) == 0){
		$profile_img="/images/design/sns_default.jpg";
	}
	$delBtn=($_ShopInfo->getMemid() == $row->id)? "<a href=\"javascript:;\" onclick=\"delGongguCmt('".$row->seq."')\"><IMG SRC=\"../images/design/gonggu_order_del.gif\" ALT=\"\" style=\"cursor:pointer;\"></a>":"";

?>									
<tr>
	<td width="40" style="padding-top:15px; padding-bottom:15px;" align="center"><img src="../images/design/icon_reply.gif" width="10" height="12" border="0"></td>
	<td width="110" style="padding-top:15px; padding-bottom:15px;" align="center"><IMG SRC="<?=$profile_img?>" WIDTH="48" HEIGHT="48" ALT="" class="img"></td>
	<td style="padding-top:15px; padding-bottom:15px;">
		<table cellpadding="0" cellspacing="0" width="95%">
			<tr>
				<td class="table_td">
					<table cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td><span class="gongguing_order_id"><?=$id?></span><span style="padding-left:10px;"><?=$sns_date?></span></td>
							<td align="right"><?=$delBtn ?></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td class="table_td"><?=$comment?></td>
			</tr>
		</table>
	</td>
</tr>
<?}?>
</table>