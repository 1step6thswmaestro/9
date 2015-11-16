<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/venderlib.php");
INCLUDE ("access.php");

//상품QNA 게시판 존재여부 확인 및 설정정보 확인
$prqnaboard=getEtcfield($_venderdata->etcfield,"PRQNA");
if(strlen($prqnaboard)>0) {
	$sql = "SELECT * FROM tblboardadmin WHERE board='".$prqnaboard."' ";
	$result=mysql_query($sql,get_db_conn());
	$qnasetup=mysql_fetch_object($result);
	mysql_free_result($result);

	$qnasetup->btype=substr($qnasetup->board_skin,0,1);
	$qnasetup->max_filesize=$qnasetup->max_filesize*(1024*100);
	if($qnasetup->use_hidden=="Y") unset($qnasetup);
}

$CurrentTime = time();
$period[0] = date("Y-m-d",$CurrentTime);
$period[1] = date("Y-m-d",$CurrentTime-(60*60*24*7));
$period[2] = date("Y-m-d",$CurrentTime-(60*60*24*14));
$period[3] = date("Y-m-d",mktime(0,0,0,date("m")-1,date("d"),date("Y")));

$code=$_POST["code"];
$s_check=$_POST["s_check"];
$search=$_POST["search"];
$search_start=$_POST["search_start"];
$search_end=$_POST["search_end"];
$vperiod=(int)$_POST["vperiod"];

$search_start=$search_start?$search_start:$period[0];
$search_end=$search_end?$search_end:date("Y-m-d",$CurrentTime);
$search_s=$search_start?$search_start." 00:00:00":$period[0]." 00:00:00";
$search_e=$search_end?$search_end." 23:59:59":date("Ymd",$CurrentTime)." 23:59:59";

$search_s=mktime(0,0,0,(int)substr($search_s,5,2),(int)substr($search_s,8,2),substr($search_s,0,4));
$search_e=mktime(23,59,59,(int)substr($search_e,5,2),(int)substr($search_e,8,2),substr($search_e,0,4));

${"check_vperiod".$vperiod} = "checked";

$tempstart = explode("-",$search_start);
$tempend = explode("-",$search_end);
$termday = (mktime(0,0,0,$tempend[1],$tempend[2],$tempend[0])-mktime(0,0,0,$tempstart[1],$tempstart[2],$tempstart[0]))/86400;
if ($termday>366) {
	echo "<script>alert('검색기간은 1년을 초과할 수 없습니다.');location='".$_SERVER[PHP_SELF]."';</script>";
	exit;
}

$qry = "WHERE a.board='".$qnasetup->board."' ";
$qry.= "AND a.pridx=b.pridx AND b.vender='".$_VenderInfo->getVidx()."' ";
if(strlen($code)>=3) {
	$qry.= "AND b.productcode LIKE '".$code."%' ";
}
if(date("Ymd",$search_s)==date("Ymd",$search_e)) {
	$qry.= "AND FROM_UNIXTIME(a.writetime) LIKE '".date("Y-m-d",$search_s)."%' ";
} else {
	$qry.= "AND a.writetime>='".$search_s."' AND a.writetime <='".$search_e."' ";
}
if(strlen($search)>0) {
	if($s_check=="t") $qry.= "AND (a.title LIKE '%".$search."%' OR a.content LIKE '%".$search."%') ";
	else if($s_check=="n") $qry.= "AND a.name='".$search."' ";
}

$setup[page_num] = 10;
$setup[list_num] = 10;

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

?>

<? INCLUDE "header.php"; ?>

<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="calendar.js.php"></script>
<script language="JavaScript">
function ACodeSendIt(code) {
	document.sForm.code.value=code;
	murl = "order.ctgr.php?code="+code;
	BCodeCtgr.location.href = murl;
}

function OnChangePeriod(val) {
	var pForm = document.sForm;
	var period = new Array(7);
	period[0] = "<?=$period[0]?>";
	period[1] = "<?=$period[1]?>";
	period[2] = "<?=$period[2]?>";
	period[3] = "<?=$period[3]?>";

	pForm.search_start.value = period[val];
	pForm.search_end.value = period[0];
}

function searchForm() {
	document.sForm.submit();
}

function GoPage(block,gotopage) {
	document.pageForm.block.value=block;
	document.pageForm.gotopage.value=gotopage;
	document.pageForm.submit();
}

function viewArticle(num) {
	//view, modify, write
	document.procForm.num.value=num;
	document.procForm.action="order_qnaview.php";
	document.procForm.submit();
}
</script>
<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed"  height="100%" >
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
					<td><img src="images/order_qna_title.gif"></td>
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
											<td class="notice_gray" height="20"><img src="images/icon_dot02.gif" border=0 hspace="4">입점사에서 등록한 상품에 대해서만 Q&A 게시물을 확인할 수 있습니다.</td>
										</tr>
										<tr>
											<td class="notice_gray" height="20"><img src="images/icon_dot02.gif" border=0 hspace="4">입점사는 등록된 Q&A 게시물의 관리[답변/수정/삭제]를 할 수 있습니다.</td>
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
				


				






				<table border=0 cellpadding=0 cellspacing=0 width=100%>
				<form name=sForm action="<?=$_SERVER[PHP_SELF]?>" method=post>
				<input type=hidden name=code value="<?=$code?>">
				<tr>
					<td valign=top bgcolor=D4D4D4 style=padding:1>
					<table border=0 cellpadding=0 cellspacing=0 width=100%>
					<tr>
						<td valign=top bgcolor=F0F0F0 style=padding:10>
						<table border=0 cellpadding=0 cellspacing=3 >
						<col width=></col>
						<col width=130></col>
						<tr>
							<td>
							<U>접수일</U>&nbsp; <input class=input type=text name=search_start value="<?=$search_start?>" size=10 onfocus="this.blur();" OnClick="Calendar(this)" style="text-align:center;"> ~ <input type=text name=search_end value="<?=$search_end?>" size=10 onfocus="this.blur();" OnClick="Calendar(this)" style="text-align:center;" class=input>
							&nbsp;
							<img src=images/btn_today01.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(0)">
							<img src=images/btn_day07.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(1)">
							<img src=images/btn_day14.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(2)">
							<img src=images/btn_day30.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(3)">
							&nbsp;&nbsp;&nbsp;&nbsp;
							<U>분류</U>&nbsp;
							<select name="code1" style=width:130; onchange="ACodeSendIt(this.options[this.selectedIndex].value)">
							<option value="">--- 선택하세요 ---</option>
<?
							$sql = "SELECT SUBSTRING(productcode,1,3) as prcode FROM tblproduct ";
							$sql.= "WHERE vender='".$_VenderInfo->getVidx()."' ";
							$sql.= "GROUP BY prcode ";
							$result=mysql_query($sql,get_db_conn());
							$codes="";
							while($row=mysql_fetch_object($result)) {
								$codes.=$row->prcode.",";
							}
							mysql_free_result($result);
							if(strlen($codes)>0) {
								$codes=substr($codes,0,-1);
								$prcodelist=ereg_replace(',','\',\'',$codes);
							}
							if(strlen($prcodelist)>0) {
								$sql = "SELECT codeA,codeB,codeC,codeD,code_name FROM tblproductcode ";
								$sql.= "WHERE codeA IN ('".$prcodelist."') AND codeB='000' AND codeC='000' ";
								$sql.= "AND codeD='000' AND type LIKE 'L%' ORDER BY sequence DESC ";
								$result=mysql_query($sql,get_db_conn());
								while($row=mysql_fetch_object($result)) {
									echo "<option value=\"".$row->codeA."\"";
									if($row->codeA==substr($code,0,3)) echo " selected";
									echo ">".$row->code_name."</option>\n";
								}
								mysql_free_result($result);
							}
?>
							</select>
							</td>
							<td><iframe name="BCodeCtgr" src="order.ctgr.php?code=<?=substr($code,0,3)?>&select_code=<?=$code?>" width="130" height="21" scrolling=no frameborder=no></iframe></td>
						</tr>
						<tr><td colspan=2 height=8></td></tr>
						<tr>
							<td colspan=2>
							<U>검색어</U>&nbsp;
							<select name=s_check style="">
							<option value="n" <?if($s_check=="n")echo"selected";?>>작성자</option>
							<option value="t" <?if($s_check=="t")echo"selected";?>>제목+내용</option>
							</select>
							<input type=text name=search value="<?=$search?>" size=30 class=input>
							<A HREF="javascript:searchForm()"><img src=images/btn_inquery03.gif border=0 align=absmiddle></A>
							</td>
						</tr>
						</table>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				</form>
				</table>

				<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
				<tr><td height=20></td></tr>
				<tr><td height=1 bgcolor=#cccccc></td></tr>
				</table>

				<table border=0 cellpadding=0 cellspacing=1 width=100% bgcolor=E7E7E7 style="table-layout:fixed">
				<col width=35></col>
				<col width=></col>
				<col width=200></col>
				<col width=75></col>
				<col width=75></col>
				<tr height=28 align=center bgcolor=F5F5F5>
					<td><B>번호</B></td>
					<td><B>제목</B></td>
					<td><B>상품명</B></td>
					<td><B>글쓴이</B></td>
					<td><B>등록일</B></td>
				</tr>
<?
				$colspan=5;
				if(strlen($qnasetup->board)>0) {
					$sql = "SELECT COUNT(*) as t_count FROM tblboard a, tblproduct b ".$qry." ";
					$result = mysql_query($sql,get_db_conn());
					$row = mysql_fetch_object($result);
					$t_count = $row->t_count;
					mysql_free_result($result);
					$pagecount = (($t_count - 1) / $setup[list_num]) + 1;

					$sql = "SELECT a.*, b.productcode,b.productname,b.selfcode FROM tblboard a, tblproduct b ".$qry." ";
					$sql.= "ORDER BY a.thread, a.pos ";
					$sql.= "LIMIT " . ($setup[list_num] * ($gotopage - 1)) . ", " . $setup[list_num];
					$result=mysql_query($sql,get_db_conn());
					$i=0;
					while($row=mysql_fetch_object($result)) {
						$number = ($t_count-($setup[list_num] * ($gotopage-1))-$i);

						unset($subject);
						$depth=$row->depth;
						$wid=1;
						if ($depth > 0) {
							if ($depth == 1) {
								$wid = 6;
							} else {
								$wid = (6 * $depth) + (4 * ($depth-1));
							}
							$subject .= "<img src=images/x.gif width=".$wid." height=2 border=0>";
							$subject .= "<img src=images/re_mark.gif border=0>";
						}
						$subject .= strip_tags($row->title);

						echo "<tr height=28 bgcolor=#FFFFFF>\n";
						echo "	<td align=center>".$number."</td>\n";
						echo "	<td width=100% nowrap style=padding-top:3;padding-left:3 align=left>";
						echo "	<span style='width:97%;overflow:hidden;text-overflow:ellipsis;'>\n";
						echo "	<A HREF=\"javascript:viewArticle(".$row->num.")\">".$subject."</A>\n";
						echo "	</span>\n";
						echo "	</td>\n";
						echo "	<td width=100% nowrap style=padding-top:3;padding-left:3 align=left>";
						echo "	<span style='width:97%;overflow:hidden;text-overflow:ellipsis;'>\n";
						echo "	<a href=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode."\" target=_blank>".$row->productname.($row->selfcode?"-".$row->selfcode:"")."</a>\n";
						echo "	</span>\n";
						echo "	</td>\n";
						echo "	<td align=center>".$row->name."</td>\n";
						echo "	<td align=center>".date("Y-m-d",$row->writetime)."</td>\n";
						echo "</tr>\n";
						$i++;
					}
					mysql_free_result($result);
					if($i==0) {
						echo "<tr height=28 bgcolor=#FFFFFF><td colspan=".$colspan." align=center>조회된 내용이 없습니다.</td></tr>\n";
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
				} else {
					echo "<tr height=28 bgcolor=#FFFFFF><td colspan=".$colspan." align=center>조회된 내용이 없습니다.</td></tr>\n";
				}
?>
				</table>

				<table border=0 cellpadding=0 cellspacing=0 width=100%>
				<tr>
					<td align=center style="padding-top:10"><?=$pageing?></td>
				</tr>
				</table>

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

<form name=pageForm method=post action="<?=$_SERVER[PHP_SELF]?>">
<input type=hidden name=code value="<?=$code?>">
<input type=hidden name=search_start value="<?=$search_start?>">
<input type=hidden name=search_end value="<?=$search_end?>">
<input type=hidden name=s_check value="<?=$s_check?>">
<input type=hidden name=search value="<?=$search?>">
<input type=hidden name=block value="<?=$block?>">
<input type=hidden name=gotopage value="<?=$gotopage?>">
</form>

<form name=procForm method=post>
<input type=hidden name=code value="<?=$code?>">
<input type=hidden name=search_start value="<?=$search_start?>">
<input type=hidden name=search_end value="<?=$search_end?>">
<input type=hidden name=s_check value="<?=$s_check?>">
<input type=hidden name=search value="<?=$search?>">
<input type=hidden name=block value="<?=$block?>">
<input type=hidden name=gotopage value="<?=$gotopage?>">
<input type=hidden name=num>
</form>

</table>

<iframe name="processFrame" src="about:blank" width="0" height="0" scrolling=no frameborder=no></iframe>

<?=$onload?>

<? INCLUDE "copyright.php"; ?>