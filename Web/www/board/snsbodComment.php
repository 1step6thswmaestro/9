<?php
header('Content-Type: text/html; charset=euc-kr');
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

include "head.php";

$board = $_POST["board"];
$this_num = $_POST["num"];

$arIconImage = array("t"=>"twitter","f"=>"facebook");

$com_query = "SELECT A.* ";
$com_query .= ", (SELECT profile_img FROM tblmembersnsinfo B WHERE A.id=B.id ORDER BY B.regidate DESC limit 1) profile_img ";
$com_query .= " FROM tblboardcomment A WHERE board='".$board."' ";
$com_query .= " AND parent = '".$this_num."' ORDER BY num DESC ";
$com_result = @mysql_query($com_query,get_db_conn());
$com_rows = @mysql_num_rows($com_result);
?>
<table cellpadding="0" cellspacing="0" width="100%">
<?
if ($com_rows <= 0) {
	@mysql_query("UPDATE tblboard SET total_comment='0' WHERE board='$board' AND num='$this_num'");
}
else
{
	while($com_row = mysql_fetch_array($com_result)) {
		$icon="";
		$artype = explode(",",$com_row["sns_type"]);
		for($i=0;$i<sizeof($artype)-1;$i++){

			$link_query = "SELECT link FROM tblmembersnsinfo WHERE id='".$com_row["id"]."' AND type = '".$artype[$i]."' LIMIT 1 ";
			$link_result = @mysql_query($link_query,get_db_conn());
			$link_row = mysql_fetch_array($link_result);

			$link = $link_row["link"];

			if( strlen($link) > 0 ) {
				$icon .= "<a href='".$link."' target='_blank'>";
			}
			$icon .= "<img src=\"../images/design/icon_".$arIconImage[$artype[$i]]."_on.gif\" align=\"absmiddle\" WIDTH=\"17\" HEIGHT=\"17\"> ";
			if( strlen($link) > 0 ) {
				$icon .= "</a>";
			}
		}
		$profile_img = $com_row["profile_img"];
		if(strlen($profile_img) == 0){
			$profile_img="../images/design/sns_default.jpg";
		}
		$sns_date = date("Y-m-d H:i:s", $com_row["writetime"]);
		$name = $com_row["name"];
		$c_num = $com_row["num"];
		$c_id = $com_row["id"];
		$del_btn = "";
		if($c_id == $_ShopInfo->getMemid() || $member[admin]=="SU"){
			$del_btn ="<IMG SRC=\"../images/design/board_comment_deletion.gif\" WIDTH=11 HEIGHT=11 ALT=\"\" style=\"CURSOR:pointer;\" onclick=\"delbodComment(".$c_num.")\">";
		}
?>
<tr>
	<td width="48" valign="top"><IMG SRC="<?=$profile_img?>" WIDTH="48" HEIGHT="48" ALT="" class="img"></td>
	<td width="13" valign="top"><img src="../images/design/space_line.gif" width="10" height="1" border="0"></td>
	<td width="100%" valign="top">
		<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td>
					<table cellpadding="0" cellspacing="0">
						<tr>
							<td style="padding-right:10px;"><?=$icon?></td>
							<td style="padding-right:10px;" class="gongguing_order_id"><?=$name?></td>
							<td style="padding-right:10px;" class="gongguing_order_date"><?=$sns_date?></td>
							<td style="text-align:right" class="gongguing_order_date"><?=$del_btn?></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td class="table_td"><?=nl2br(stripslashes($com_row["comment"]))?></td>
			</tr>
		</table>
	</td>
</tr>
<tr>
	<td colspan="3" height="25"><img src="../images/design/con_line02.gif" width="100%" height="1" border="0"></td>
</tr>
<?
	}
	mysql_free_result($com_result);
}
?>
</table>
<script type="text/javascript">
$j("#snsCmtTot").html("<?=$com_rows?>");
</script>