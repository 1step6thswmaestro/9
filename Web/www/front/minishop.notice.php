<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/venderlib.php");

$sellvidx=$_REQUEST["sellvidx"];

$_MiniLib=new _MiniLib($sellvidx);
$_MiniLib->_MiniInit();

if(!$_MiniLib->isVender) {
	Header("Location:".$Dir.MainDir."main.php");
	exit;
}
$_minidata=$_MiniLib->getMiniData();

$_MiniLib->getCode();
$_MiniLib->getThemecode();

$strlocation="<A HREF=\"http://".$_ShopInfo->getShopurl()."\">홈</A> > <A HREF=\"http://".$_ShopInfo->getShopurl().FrontDir."minishop.php?sellvidx=".$_minidata->vender."\"><B>".$_minidata->brand_name."</B></A>";


$setup[page_num] = 10;
$setup[list_num] = 10;

$type=$_REQUEST["type"];	//list, view
$artid=$_REQUEST["artid"];
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

if($type!="list" && $type!="view") $type="list";

if($type=="view") {
	$sql = "SELECT * FROM tblvendernotice WHERE vender='".$_minidata->vender."' AND date='".$artid."' ";
	$result=mysql_query($sql,get_db_conn());
	$noticedata=mysql_fetch_object($result);
	mysql_free_result($result);

	$sql = "UPDATE tblvendernotice SET access=access+1 WHERE vender='".$_minidata->vender."' AND date='".$artid."' ";
	mysql_query($sql,get_db_conn());

}

?>

<HTML>
<HEAD>
<TITLE><?=$_data->shoptitle?></TITLE>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META http-equiv="X-UA-Compatible" content="IE=5" />
<META name="description" content="<?=(strlen($_data->shopdescription)>0?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<script type="text/javascript" src="<?=$Dir?>lib/DropDown.js.php"></script>
<script type="text/javascript" src="<?=$Dir?>lib/minishop.js.php"></script>
<?include($Dir."lib/style.php")?>
<SCRIPT LANGUAGE="JavaScript">
<!--
function GoPage(block,gotopage) {
	document.location.href="<?=$_SERVER[PHP_SELF]?>?sellvidx=<?=$_minidata->vender?>&block="+block+"&gotopage="+gotopage;
}
//-->
</SCRIPT>
</HEAD>

<body<?=(substr($_data->layoutdata["MOUSEKEY"],0,1)=="Y"?" oncontextmenu=\"return false;\"":"")?><?=(substr($_data->layoutdata["MOUSEKEY"],1,1)=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=(substr($_data->layoutdata["MOUSEKEY"],2,1)=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>

<? include ($Dir."lib/menu_minishop.php") ?>

<table border=0 cellpadding=0 cellspacing=0 width=100%>
<tr>
	<td align=center style="padding:5">
	<table border=0 cellpadding=0 cellspacing=0 width=100%>
	<tr><td height=5></td></tr>
	<tr>
		<td style="padding-left:5"><img src="<?=$Dir?>images/minishop/title_notice.gif" border=0></td>
	</tr>
	<tr><td height=20></td></tr>
	<tr>
		<td style="padding-left:5">

		<?if($type=="list"){?>

		<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
		<col width=60></col>
		<col width=1></col>
		<col width=120></col>
		<col width=1></col>
		<col width=></col>
		<col width=1></col>
		<col width=80></col>
		<tr height=21>
			<td align=center><img src="<?=$Dir?>images/minishop/notice_table0.gif" border=0></td>
			<td bgcolor="#BDBABD"></td>
			<td align=center><img src="<?=$Dir?>images/minishop/notice_table1.gif" border=0></td>
			<td bgcolor="#BDBABD"></td>
			<td align=center><img src="<?=$Dir?>images/minishop/notice_table2.gif" border=0></td>
			<td bgcolor="#BDBABD"></td>
			<td align=center><img src="<?=$Dir?>images/minishop/notice_table3.gif" border=0></td>
		</tr>
		<tr>
			<td colspan=7 height=12 background="<?=$Dir?>images/minishop/notice_tabletoplinebg.gif"></td>
		</tr>
<?
		$sql = "SELECT COUNT(*) as t_count FROM tblvendernotice WHERE vender='".$_minidata->vender."' ";
		$result = mysql_query($sql,get_db_conn());
		$row = mysql_fetch_object($result);
		$t_count = $row->t_count;
		mysql_free_result($result);
		$pagecount = (($t_count - 1) / $setup[list_num]) + 1;

		$sql = "SELECT date,subject,access FROM tblvendernotice ";
		$sql.= "WHERE vender='".$_minidata->vender."' ";
		$sql.= "ORDER BY date DESC ";
		$sql.= "LIMIT " . ($setup[list_num] * ($gotopage - 1)) . ", " . $setup[list_num];
		$result=mysql_query($sql,get_db_conn());
		$i=0;
		while($row=mysql_fetch_object($result)) {
			$number = ($t_count-($setup[list_num] * ($gotopage-1))-$i);
			$date=substr($row->date,0,4)."/".substr($row->date,4,2)."/".substr($row->date,6,2);
			echo "<tr height=25>\n";
			echo "	<td align=center>".$number."</td>\n";
			echo "	<td colspan=2 align=center>".$date."</td>\n";
			echo "	<td colspan=2 style=\"padding-left:15\"><A HREF=\"javascript:GoNoticeView('".$_minidata->vender."','".$row->date."','".$block."','".$gotopage."')\">".strip_tags($row->subject)."</A></td>\n";
			echo "	<td colspan=2 align=center>".$row->access."</td>\n";
			echo "</tr>\n";
			echo "<tr><td colspan=\"7\" height=\"1\" bgcolor=\"#EFEBEF\"></td></tr>\n";
			$i++;
		}
		mysql_free_result($result);
		echo "<tr><td colspan=\"7\" height=\"2\" bgcolor=\"#000000\"></td></tr>\n";
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

			echo "<tr><td colspan=7 align=center style=\"padding-top:10\">".$pageing."</td></tr>\n";
		}
?>
		</table>

		<?}else if($type=="view"){?>

		<table width=100% border=0 cellspacing=0 cellpadding=0>
		<tr>
			<td>
			<table width="100%" border=0 cellspacing=0 cellpadding=0 style="table-layout:fixed">
			<col width=80></col>
			<col width=230></col>
			<col width=55></col>
			<col width=></col>
			<tr>
				<td width="60" height="25" style="padding-left:25">제&nbsp;&nbsp;&nbsp;목 :</td>
				<td width="671" colspan="3" align="left"><font COLOR="#FF3300"><B><?=$noticedata->subject?></B></font></td>
			</tr>
			<tr height=25>
				<td style="padding-left:25">등록일 :</td>
				<td><?=substr($noticedata->date,0,4)."/".substr($noticedata->date,4,2)."/".substr($noticedata->date,6,2)?></td>
				<td>조회수 :</td>
				<td><?=$noticedata->access?></td>
			</tr>
			<tr>
				<td colspan=4 height=12 background="<?=$Dir?>images/minishop/notice_tabletoplinebg.gif"></td>
			</tr>
			<tr>
				<td style="padding:10,25" colspan="4">

				<?=nl2br($noticedata->content)?>

				</td>
			</tr>
			<tr>
				<td height=8></td>
			</tr>
			<tr>
				<td colspan="4" height="2" bgcolor="#000000"></td>
			</tr>
			</table>
			</td>
		</tr>
		<tr>
			<td height="30" align="right"><a href="javascript:GoNoticeList('<?=$_minidata->vender?>','<?=$block?>','<?=$gotopage?>')"><img src="<?=$Dir?>images/minishop/btn_notice_list.gif" border=0></a></td>
		</tr>
		</table>

		<?}?>

		</td>
	</tr>
	</table>
	</td>
</tr>
</table>

<? include ($Dir."lib/bottom.php") ?>

</BODY>
</HTML>