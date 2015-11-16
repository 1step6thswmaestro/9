<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
INCLUDE ("access.php");
INCLUDE "header.php";
?>
<script type="text/javascript" src="../lib/lib.js.php"></script>
<script type="text/javascript">
<!--
function board_open(board,num) {
	window.open("<?=$Dir?>board/board.php?pagetype=view&board="+board+"&num="+num,"","width=1080,height=800,scrollbars=yes");
}
//-->
</script>
<?
//리스트 세팅
$setup[page_num] = 10;
$setup[list_num] = 15;

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

$colspan=5;
$qry = "WHERE C.id='".$_GET['memberID']."' ";
$sql = "SELECT COUNT(B.*) as t_count FROM tblboard B INNER LEFT JOIN tblboardcomment C ON B.userid = C.id ";
$sql.= $qry;
$result=mysql_query($sql,get_db_conn());
$row=mysql_fetch_object($result);
$t_count = (int)$row->t_count;
mysql_free_result($result);
$pagecount = (($t_count - 1) / $setup[list_num]) + 1;

?>

<style>
	.warp {width:100%; margin:0px; padding:0px;}
	.titleStyle {height:32px; padding-left:28px; line-height:32px; color:#fff; font-size:12px; font-family:돋움; font-weight:bold; background:url('images/member_mailallsend_imgbg.gif') repeat-x;}
	.infoText {width:100%; margin:4px 0px; padding:0px; padding-left:28px; color:#888; font-size:11px; font-family:돋움; letter-spacing:-1px;}

	.tblStyle {width:100%; border-top:1px solid #ddd;}
	.tblStyle caption {display:none;}
	.tblStyle th {height:30px; background-color:#f8f8f8; color:#444; font-size:12px; letter-spacing:-1px; border-left:1px solid eee; border-bottom:1px solid #eee;}
	.tblStyle td {border-left:1px solid eee; border-bottom:1px solid #eee;}
</style>

<div class="titleStyle"><?=$_GET['memberName']?>(<font color="#fe8e4b"><?=$_GET['memberID']?></font>)회원님의 게시물 댓글</div>

<table cellpadding="0" cellspacing="0" width="100%">
	<tr><td height="20"></td></tr>
	<tr>
		<td>
			<table cellpadding="0" cellspacing="0" width="100%" class="tblStyle">
				<caption>회원활동관리-게시판 댓글</caption>
					<col width="80"></col>
					<col width=></col>
					<col width="80"></col>
					<col width="60"></col>
					<col width="70"></col>
				<tr><td height="1" background="<?=$Dir?>images/common/product/<?=$_cdata->detail_type?>/pdetail_skin_line2.gif" colspan="<?=$colspan?>"></td></tr>
				<tr>
					<th>게시판</th>
					<th>제목</th>
					<th>작성자</th>
					<th>작성일</th>
				</tr>
				<tr>
					<td height="1" colspan="<?=$colspan?>" background="<?=$Dir?>images/common/product/<?=$_cdata->detail_type?>/pdetail_skin_line4.gif"></td>
				</tr>
				<?
	$sql = "SELECT B.* FROM tblboard AS B INNER JOIN tblboardcomment AS C ON B.num = C.parent ".$qry." ";
	$sql.= "ORDER BY B.num DESC ";
	$sql.= "LIMIT " . ($setup[list_num] * ($gotopage - 1)) . ", " . $setup[list_num];
	$result=mysql_query($sql,get_db_conn());
	$j=0;
	while($row=mysql_fetch_object($result)){
		$number = ($t_count-($setup[list_num] * ($gotopage-1))-$j);

		$date=date("Y/m/d",$row->writetime);
		echo "<tr align=\"center\" style=\"color:#333333;padding-bottom:3pt;padding-top:3pt;line-height:18px;\">\n";
		echo "	<td>".$row->board."</td>\n";
		echo "	<td align=\"left\" style=\"padding:4px 10px;\">";
		echo "<A HREF=\"javascript:board_open('".$row->board."','".$row->num."')\">".titleCut(80,$row->title)."</A>";
		echo "	</td>\n";
		echo "	<td>".$row->name."</td>\n";
		echo "	<td>".$date."</td>\n";
		echo "</tr>\n";
		echo "<tr><td height=\"1\" colspan=\"".$colspan."\" background=\"".$Dir."images/common/product/".$_cdata->detail_type."/pdetail_skin_line4.gif\"></td></tr>\n";
		$j++;
	}
	mysql_free_result($result);
	if($j==0) {
		echo "<tr><td colspan=\"".$colspan."\" height=\"25\" align=\"center\">등록된 댓글이 없습니다.</td></tr>\n";
		echo "<tr><td height=\"1\" colspan=\"".$colspan."\" background=\"".$Dir."images/common/product/".$_cdata->detail_type."/pdetail_skin_line4.gif\"></td></tr>\n";
	}
?>
			</table>
		</td>
	</tr>
	<tr>
		<td height="10"></td>
	</tr>
	<tr>
		<td>
			<table cellpadding="0" cellspacing="0" width="100%">
				<?
	 if($j != 0) {
		$total_block = intval($pagecount / $setup[page_num]);

		if (($pagecount % $setup[page_num]) > 0) {
			$total_block = $total_block + 1;
		}

		$total_block = $total_block - 1;

		if (ceil($t_count/$setup[list_num]) > 0) {
			// 이전	x개 출력하는 부분-시작
			$a_first_block = "";
			if ($nowblock > 0) {
				$a_first_block .= "<a href='javascript:GoPage(\"review\",0,1);' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='첫 페이지';return true\"><FONT class=\"prlist\">[1...]</FONT></a>&nbsp;&nbsp;";

				$prev_page_exists = true;
			}

			$a_prev_page = "";
			if ($nowblock > 0) {
				$a_prev_page .= "<a href='javascript:GoPage(\"review\",".($nowblock-1).",".($setup[page_num]*($block-1)+$setup[page_num]).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='이전 ".$setup[page_num]." 페이지';return true\"><FONT class=\"prlist\">[prev]</FONT></a>&nbsp;&nbsp;";

				$a_prev_page = $a_first_block.$a_prev_page;
			}

			// 일반 블럭에서의 페이지 표시부분-시작

			if (intval($total_block) <> intval($nowblock)) {
				$print_page = "";
				for ($gopage = 1; $gopage <= $setup[page_num]; $gopage++) {
					if ((intval($nowblock*$setup[page_num]) + $gopage) == intval($gotopage)) {
						$print_page .= "<FONT class=\"choiceprlist\">".(intval($nowblock*$setup[page_num]) + $gopage)."</font> ";
					} else {
						$print_page .= "<a href='javascript:GoPage(\"review\",".$nowblock.",".(intval($nowblock*$setup[page_num]) + $gopage).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$setup[page_num]) + $gopage)."';return true\"><FONT class=\"prlist\">[".(intval($nowblock*$setup[page_num]) + $gopage)."]</FONT></a> ";
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
						$print_page .= "<FONT class=\"choiceprlist\">".(intval($nowblock*$setup[page_num]) + $gopage)."</font> ";
					} else {
						$print_page .= "<a href='javascript:GoPage(\"review\",".$nowblock.",".(intval($nowblock*$setup[page_num]) + $gopage).");' onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$setup[page_num]) + $gopage)."';return true\"><FONT class=\"prlist\">[".(intval($nowblock*$setup[page_num]) + $gopage)."]</FONT></a> ";
					}
				}
			}		// 마지막 블럭에서의 표시부분-끝


			$a_last_block = "";
			if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
				$last_block = ceil($t_count/($setup[list_num]*$setup[page_num])) - 1;
				$last_gotopage = ceil($t_count/$setup[list_num]);

				$a_last_block .= "&nbsp;&nbsp;<a href='javascript:GoPage(\"review\",".$last_block.",".$last_gotopage.");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='마지막 페이지';return true\"><FONT class=\"prlist\">[...".$last_gotopage."]</FONT></a>";

				$next_page_exists = true;
			}

			// 다음 10개 처리부분...

			$a_next_page = "";
			if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
				$a_next_page .= "&nbsp;&nbsp;<a href='javascript:GoPage(\"review\",".($nowblock+1).",".($setup[page_num]*($nowblock+1)+1).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='다음 ".$setup[page_num]." 페이지';return true\"><FONT class=\"prlist\">[next]</FONT></a>";

				$a_next_page = $a_next_page.$a_last_block;
			}
		} else {
			$print_page = "<FONT class=\"prlist\">1</FONT>";
		}
	}
?>
				<tr>
					<td align="center" style="font-size:11px;">
						<?=$a_div_prev_page.$a_prev_page.$print_page.$a_next_page.$a_div_next_page?>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr><td align="center"><a href="javascript:window.close();"><img src="images/btn_close.gif" vspace="10" border="0" alt="" /></a></td></tr>
</table>
