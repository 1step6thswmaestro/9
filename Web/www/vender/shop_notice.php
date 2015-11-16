<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/venderlib.php");
INCLUDE ("access.php");

$setup[page_num] = 10;
$setup[list_num] = 10;

$type=$_REQUEST["type"];
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
	$sql = "SELECT * FROM tblvenderadminnotice ";
	$sql.= "WHERE (vender='".$_VenderInfo->getVidx()."' || vender='0') AND date='".$artid."' ";
	$result=mysql_query($sql,get_db_conn());
	if(!$noticedata=mysql_fetch_object($result)) {
		echo "<html></head><body onload=\"alert('해당 공지사항이 존재하지 않습니다.')\"></body></html>";exit;
	}
	mysql_free_result($result);

	$sql = "UPDATE tblvenderadminnotice SET access=access+1 ";
	$sql.= "WHERE (vender='".$_VenderInfo->getVidx()."' || vender='0') AND date='".$artid."' ";
	mysql_query($sql,get_db_conn());

	//이전글
	unset($prevdata);
	$sql = "SELECT date,subject FROM tblvenderadminnotice ";
	$sql.= "WHERE (vender='".$_VenderInfo->getVidx()."' || vender='0') ";
	$sql.= "AND date>'".$artid."' ORDER BY date ASC LIMIT 1 ";
	$result=mysql_query($sql,get_db_conn());
	$prevdata=mysql_fetch_object($result);
	mysql_free_result($result);

	//다음글
	unset($nextdata);
	$sql = "SELECT date,subject FROM tblvenderadminnotice ";
	$sql.= "WHERE (vender='".$_VenderInfo->getVidx()."' || vender='0') ";
	$sql.= "AND date<'".$artid."' ORDER BY date DESC LIMIT 1 ";
	$result=mysql_query($sql,get_db_conn());
	$nextdata=mysql_fetch_object($result);
	mysql_free_result($result);
}

?>

<? INCLUDE "header.php"; ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function GoPage(block,gotopage) {
	document.location.href="<?=$_SERVER[PHP_SELF]?>?block="+block+"&gotopage="+gotopage;
}
function GoNoticeList(block,gotopage) {
	url="<?=$_SERVER[PHP_SELF]?>?block="+block+"&gotopage="+gotopage;
	document.location.href=url;
}
function GoNoticeView(artid,block,gotopage) {
	url="<?=$_SERVER[PHP_SELF]?>?type=view&artid="+artid;
	if(typeof block!="undefined") url+="&block="+block;
	if(typeof gotopage!="undefined") url+="&gotopage="+gotopage;
	document.location.href=url;
}

</script>
<table border=0 cellpadding=0 cellspacing=0 width=100% height=100% style="table-layout:fixed">
<col width=190></col>
<col width=20></col>
<col width=></col>
<col width=20></col>
<tr>
	<td width=190 valign=top nowrap background="images/minishop_leftbg.gif"><? include ("menu.php"); ?></td>
	<td width=20 nowrap></td>
	<td valign=top style="padding-top:20px">

	<table width="100%"  border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td>
		<table width="100%"  border="0" cellpadding="0" cellspacing="0" >
		<tr>
			<td>
				<table border=0 cellpadding=0 cellspacing=0 width=100% >
				<tr>
					<td><img src="images/shop_notice_title.gif"></td>
				</tr>
				<tr>
					<td height=5 background="images/minishop_titlebg.gif">
				</tr>
				</table>
			</td>
		</tr>
		<tr><td height=10></td></tr>
		<tr>
			<td>
				<table border=0 cellpadding=0 cellspacing=0 width=100% >
				<tr>
					<td colspan=3 >


						<table cellpadding="10" cellspacing="1" width="100%" bgcolor="#EFEFF2">
							<tr>
								<td  bgcolor="#F5F5F9" style="padding:20px">
									<table border=0 cellpadding=0 cellspacing=0 width=100%>
										<tr>
											<td class="notice_gray" height="20"><img src="images/icon_dot02.gif" border=0 hspace="4">본사 쇼핑몰 [입점관리 공지사항] 게시판에 등록된 게시물은 각각의 입점사 관리페이지에서 확인할 수 있습니다.</td>
										</tr>
										<tr>
											<td class="notice_gray" height="20"><img src="images/icon_dot02.gif" border=0 hspace="4">본사 공지사항에 등록된 글은 입점사는 열람만 가능하며 수정/삭제는 본사 관리자만 관리할 수 있습니다.</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>



					</td>
				</tr>
				</table>
				</td>
			</tr>

			<!-- 처리할 본문 위치 시작 -->
			<tr><td height=40></td></tr>
			<tr>
				<td>
				

				<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
				<tr><td height=1 bgcolor=#cccccc></td></tr>
				</table>

				<?if($type=="list"){?>

				<table border=0 cellpadding=0 cellspacing=1 width=100% bgcolor=E7E7E7 style="table-layout:fixed">
				<col width=10%></col>
				<col width=></col>
				<col width=14%></col>
				<tr height=28 align=center bgcolor=F5F5F5>
					<td><B>번호</B></td>
					<td><B>제목</B></td>
					<td><B>게시일</B></td>
				</tr>
<?
				$sql = "SELECT COUNT(*) as t_count FROM tblvenderadminnotice ";
				$sql.= "WHERE (vender='".$_VenderInfo->getVidx()."' OR vender='0') ";
				$result = mysql_query($sql,get_db_conn());
				$row = mysql_fetch_object($result);
				$t_count = $row->t_count;
				mysql_free_result($result);
				$pagecount = (($t_count - 1) / $setup[list_num]) + 1;

				$sql = "SELECT date,subject,access FROM tblvenderadminnotice ";
				$sql.= "WHERE (vender='".$_VenderInfo->getVidx()."' OR vender='0') ";
				$sql.= "ORDER BY date DESC ";
				$sql.= "LIMIT " . ($setup[list_num] * ($gotopage - 1)) . ", " . $setup[list_num];
				$result=mysql_query($sql,get_db_conn());
				$i=0;
				while($row=mysql_fetch_object($result)) {
					$number = ($t_count-($setup[list_num] * ($gotopage-1))-$i);
					$date=substr($row->date,0,4)."/".substr($row->date,4,2)."/".substr($row->date,6,2);
					echo "<tr height=28 bgcolor=#FFFFFF>\n";
					echo "	<td align=center>".$number."</td>\n";
					echo "	<td style=\"padding:7,10\"><A HREF=\"javascript:GoNoticeView('".$row->date."','".$block."','".$gotopage."')\">".strip_tags($row->subject)."</A></td>\n";
					echo "	<td align=center>".$date."</td>\n";
					echo "</tr>\n";
					$i++;
				}
				mysql_free_result($result);
				if($i==0) {
					echo "<tr height=28 bgcolor=#FFFFFF><td colspan=3 align=center>등록된 공지사항이 없습니다.</td></tr>\n";
				} else if($i>0) {
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
				}
?>
				</table>
				
				<table border=0 cellpadding=0 cellspacing=0 width=100%>
				<tr>
					<td align=center style="padding-top:10"><?=$pageing?></td>
				</tr>
				</table>

				<?}else if($type=="view"){?>

				<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
				<tr>
					<td valign=top style=background-repeat:repeat-x bgcolor="e7e7e7">
					<table width=100% border=0 cellspacing=0 cellpadding=0>
					<tr>
						<td bgcolor=F5F5F5>
						<table width="100%" border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td style=background-repeat:repeat-y;background-position:right;padding:9 width="88%">
							<B>제 목 : <?=$noticedata->subject?></B>
							</td>
							<td align="left"><?=substr($noticedata->date,0,4)."/".substr($noticedata->date,4,2)."/".substr($noticedata->date,6,2)?></td>
						</tr>
						</table>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				<tr><td height=1 bgcolor=#E7E7E7></td></tr>
				<tr>
					<td bgcolor=ffffff style=background-repeat:repeat-y;background-position:right;padding:9>
					<?=nl2br($noticedata->content)?>
					</td>
				</tr>
				<tr><td height=1 bgcolor=#E7E7E7></td></tr>
				<tr><td height=12></td></tr>
				<tr>
					<td align=center>
					<?if(is_object($prevdata)){?>
					<A HREF="javascript:GoNoticeView('<?=$prevdata->date?>','<?=$block?>','<?=$gotopage?>')"><img src="images/btn_prev01.gif" border=0></A>&nbsp;
					<?}?>
					<A HREF="javascript:GoNoticeList('<?=$block?>','<?=$gotopage?>')"><img src="images/btn_list.gif" border=0></A>
					<?if(is_object($nextdata)){?>
					&nbsp;<A HREF="javascript:GoNoticeView('<?=$nextdata->date?>','<?=$block?>','<?=$gotopage?>')"><img src="images/btn_next01.gif" border=0></A>
					<?}?>
					</td>
				</tr>
				
				<?if(is_object($prevdata) || is_object($nextdata)){?>

				<tr><td height=25></td></tr>
				<tr>
					<td>
					<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
					<tr><td height=1 bgcolor=#cccccc></td></tr>
					</table>

					<table border=0 cellpadding=0 cellspacing=1 width=100% bgcolor=E7E7E7 style="table-layout:fixed">
					<col width=10%></col>
					<col width=></col>
					<col width=14%></col>
					<tr height=28 align=center bgcolor=F5F5F5>
						<td><B>번호</B></td>
						<td><B>제목</B></td>
						<td><B>게시일</B></td>
					</tr>
					<?if(is_object($prevdata)){?>
					<tr height=28 bgcolor=#FFFFFF>
						<td align=center>이전글</td>
						<td style="padding:7,10"><A HREF="javascript:GoNoticeView('<?=$prevdata->date?>','<?=$block?>','<?=$gotopage?>')"><?=strip_tags($prevdata->subject)?></A></td>
						<td align=center><?=substr($prevdata->date,0,4)."/".substr($prevdata->date,4,2)."/".substr($prevdata->date,6,2)?></td>
					</tr>
					<?}?>
					<?if(is_object($nextdata)){?>
					<tr height=28 bgcolor=#FFFFFF>
						<td align=center>다음글</td>
						<td style="padding:7,10"><A HREF="javascript:GoNoticeView('<?=$nextdata->date?>','<?=$block?>','<?=$gotopage?>')"><?=strip_tags($nextdata->subject)?></A></td>
						<td align=center><?=substr($nextdata->date,0,4)."/".substr($nextdata->date,4,2)."/".substr($nextdata->date,6,2)?></td>
					</tr>
					<?}?>
					</table>
					</td>
				</tr>

				<?}?>

				</table>

				<?}?>

				</td>
			</tr>
			<!-- 처리할 본문 위치 끝 -->

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

<?=$onload?>

<? INCLUDE "copyright.php"; ?>