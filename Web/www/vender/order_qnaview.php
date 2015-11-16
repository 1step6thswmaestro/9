<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/venderlib.php");
INCLUDE ("access.php");

include ($Dir.BoardDir."file.inc.php");

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

if(strlen($qnasetup->board)<=0) {
	echo "<html></head><body onload=\"alert('쇼핑몰 Q&A게시판 오픈이 안되었습니다.\\n\\n쇼핑몰에 문의하시기 바랍니다.');location.href='order_qna.php'\"></body></html>";exit;
}

$CurrentTime = time();
$period[0] = date("Y-m-d",$CurrentTime);
$period[1] = date("Y-m-d",$CurrentTime-(60*60*24*7));
$period[2] = date("Y-m-d",$CurrentTime-(60*60*24*14));
$period[3] = date("Y-m-d",mktime(0,0,0,date("m")-1,date("d"),date("Y")));

$num=$_POST["num"];
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

$qry2="";
if(strlen($code)>=3) {
	$qry2.= "AND b.productcode LIKE '".$code."%' ";
}
if(date("Ymd",$search_s)==date("Ymd",$search_e)) {
	$qry2.= "AND FROM_UNIXTIME(a.writetime) LIKE '".date("Y-m-d",$search_s)."%' ";
} else {
	$qry2.= "AND a.writetime>='".$search_s."' AND a.writetime <='".$search_e."' ";
}
if(strlen($search)>0) {
	if($s_check=="t") $qry2.= "AND (a.title LIKE '%".$search."%' OR a.content LIKE '%".$search."%') ";
	else if($s_check=="n") $qry2.= "AND a.name='".$search."' ";
}

$sql = "SELECT a.*, b.productcode,b.productname,b.tinyimage,b.sellprice,b.selfcode ";
$sql.= "FROM tblboard a, tblproduct b ".$qry." ";
$sql.= "AND a.num='".$num."' ";
$result=mysql_query($sql,get_db_conn());
if(!$qnadata=mysql_fetch_object($result)) {
	echo "<html></head><body onload=\"alert('해당 게시글이 존재하지 않습니다.');location.href='order_qna.php'\"></body></html>";exit;
}
mysql_free_result($result);

if(strlen($qnadata->filename)>0) {
	unset($file_name1);	//다운로드 링크
	unset($upload_file1);	//이미지 태그

	$attachfileurl=$filepath."/".$qnadata->filename;
	if(remote_file_exists($attachfileurl)) {
		$file_name1=FileDownload($qnasetup->board,$qnadata->filename)." (".ProcessBoardFileSize($qnasetup->board,$qnadata->filename).")";

		$ext = strtolower(substr(strrchr($qnadata->filename,"."),1));
		if($ext=="gif" || $ext=="jpg" || $ext=="png") {
			$imgmaxwidth=ProcessBoardFileWidth($qnasetup->board,$qnadata->filename);
			if($imgmaxwidth>600) {
				$imgmaxwidth=600;
			}
			$upload_file1="<img src=\"".ImageAttachUrl($qnasetup->board,$qnadata->filename)."\" border=0 width=\"".$imgmaxwidth."\">";
		}
	}
}


//이전글
unset($prevdata);
$sql = "SELECT a.num,a.name,a.title,a.writetime FROM tblboard a, tblproduct b ".$qry." ".$qry2." ";
$sql.= "AND a.pos = 0 AND a.thread < '".$qnadata->thread."' AND a.deleted != '1' ";
$sql.= "ORDER BY a.thread DESC LIMIT 1 ";
$result=mysql_query($sql,get_db_conn());
$prevdata=mysql_fetch_object($result);
mysql_free_result($result);

//다음글
unset($nextdata);
$sql = "SELECT a.num,a.name,a.title,a.writetime FROM tblboard a, tblproduct b ".$qry." ".$qry2." ";
$sql.= "AND a.pos = 0 AND a.thread > '".$qnadata->thread."' AND a.deleted != '1' ";
$sql.= "ORDER BY a.thread LIMIT 1 ";
$result=mysql_query($sql,get_db_conn());
$nextdata=mysql_fetch_object($result);
mysql_free_result($result);
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

function listArticle() {
	document.procForm.exec.value="";
	document.procForm.num.value="";
	document.procForm.target="";
	document.procForm.action="order_qna.php";
	document.procForm.submit();
}

function viewArticle(num) {
	document.procForm.exec.value="";
	document.procForm.num.value=num;
	document.procForm.target="";
	document.procForm.action="order_qnaview.php";
	document.procForm.submit();
}

function replyArticle(num) {
	qnaWin = windowOpenScroll("", "qnaOpenwin", 100, 100);
	qnaWin.focus();
	document.procForm.exec.value="reply";
	document.procForm.num.value=num;
	document.procForm.target="qnaOpenwin";
	document.procForm.action="order_qnawriteopen.php";
	document.procForm.submit();
}

function modifyArticle(num) {
	qnaWin = windowOpenScroll("", "qnaOpenwin", 100, 100);
	qnaWin.focus();
	document.procForm.exec.value="modify";
	document.procForm.num.value=num;
	document.procForm.target="qnaOpenwin";
	document.procForm.action="order_qnapassconfirm.php";
	document.procForm.submit();
}

function deleteArticle(num) {
	qnaWin = windowOpenScroll("", "qnaOpenwin", 100, 100);
	qnaWin.focus();
	document.procForm.exec.value="delete";
	document.procForm.num.value=num;
	document.procForm.target="qnaOpenwin";
	document.procForm.action="order_qnapassconfirm.php";
	document.procForm.submit();
}

</script>

<table border=0 cellpadding=0 cellspacing=0 width=1000 style="table-layout:fixed">
<col width=175></col>
<col width=5></col>
<col width=740></col>
<col width=80></col>
<tr>
	<td width=175 valign=top nowrap><? include ("menu.php"); ?></td>
	<td width=5 nowrap></td>
	<td valign=top>

	<table width="100%"  border="0" cellpadding="1" cellspacing="0" bgcolor="#D0D1D0">
	<tr>
		<td>
		<table width="100%"  border="0" cellpadding="0" cellspacing="0" style="border:3px solid #EEEEEE" bgcolor="#ffffff">
		<tr>
			<td style="padding:10">
			<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
			<tr>
				<td>
				<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
				<col width=165></col>
				<col width=></col>
				<tr>
					<td height=29 align=center background="images/tab_menubg.gif">
					<FONT COLOR="#ffffff"><B>상품 Q&A 관리</B></FONT>
					</td>
					<td></td>
				</tr>
				</table>
				</td>
			</tr>
			<tr><td height=2 bgcolor=red></td></tr>
			<tr>
				<td bgcolor=#FBF5F7>
				<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
				<col width=10></col>
				<col width=></col>
				<col width=10></col>
				<tr>
					<td colspan=3 style="padding:15,15,5,15">
					<table border=0 cellpadding=0 cellspacing=0 width=100%>
					<tr>
						<td style="padding-bottom:5"><img src="images/icon_boxdot.gif" border=0 align=absmiddle> <B>상품 Q&A 관리</B></td>
					</tr>
					<tr>
						<td style="padding-left:5;color:#7F7F7F"><img src="images/icon_dot02.gif" border=0> 설명1</td>
					</tr>
					<tr>
						<td style="padding-left:5;color:#7F7F7F"><img src="images/icon_dot02.gif" border=0> 설명2</td>
					</tr>
					<tr>
						<td style="padding-left:5;color:#7F7F7F"><img src="images/icon_dot02.gif" border=0> 설명3</td>
					</tr>
					</table>
					</td>
				</tr>
				<tr>
					<td><img src="images/tab_boxleft.gif" border=0></td>
					<td></td>
					<td><img src="images/tab_boxright.gif" border=0></td>
				</tr>
				</table>
				</td>
			</tr>

			<!-- 처리할 본문 위치 시작 -->
			<tr><td height=0></td></tr>
			<tr>
				<td style="padding:15">

				<table border=0 cellpadding=0 cellspacing=0 width=100%>
				<form name=sForm action="order_qna.php" method=post>
				<input type=hidden name=code value="<?=$code?>">
				<tr>
					<td valign=top bgcolor=D4D4D4 style=padding:1>
					<table border=0 cellpadding=0 cellspacing=0 width=100%>
					<tr>
						<td valign=top bgcolor=F0F0F0 style=padding:10>
						<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
						<col width=></col>
						<col width=130></col>
						<tr>
							<td>
							<U>접수일</U>&nbsp; <input type=text name=search_start value="<?=$search_start?>" size=10 onfocus="this.blur();" OnClick="Calendar(this)" style="text-align:center;font-size:8pt"> ~ <input type=text name=search_end value="<?=$search_end?>" size=10 onfocus="this.blur();" OnClick="Calendar(this)" style="text-align:center;font-size:8pt">
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
							<select name=s_check style="font-size:8pt">
							<option value="n" <?if($s_check=="n")echo"selected";?>>작성자</option>
							<option value="t" <?if($s_check=="t")echo"selected";?>>제목+내용</option>
							</select>
							<input type=text name=search value="<?=$search?>" size=30>
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

				<table cellpadding="0" cellspacing="0" width="100%">
				<tr><td height="10"></td></tr>
				<tr>
					<td>
					<table cellpadding="0" cellspacing="8" width="100%" bgcolor="#E8E8E8">
					<tr>
						<td bgcolor="#FFFFFF" style="padding:8px;">
						<table cellpadding="0" cellspacing="0" width="100%" align="center" style="table-layout:fixed">
						<col width="70"></col>
						<col width="15"></col>
						<col></col>
						<tr>
							<td>
<?
							echo "<A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$qnadata->productcode."\" target='_blank' onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\">";
							if (strlen($qnadata->tinyimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$qnadata->tinyimage)==true) {
								echo "<img src=\"".$Dir.DataDir."shopimages/product/".urlencode($qnadata->tinyimage)."\" border=\"0\" width=\"70\">";
							} else {
								echo "<img src=\"".$Dir."images/no_img.gif\" border=\"0\" align=\"center\" width=\"70\">";
							}
							echo "</A></td>";
?>
							<td></td>
							<td>
							<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
							<col width="60">
							<col width="10">
							<tr>
								<td>상품명</td>
								<td align="center">:</td>
								<td><A HREF="<?=$Dir.FrontDir?>productdetail.php?productcode=<?=$qnadata->productcode?>" target="_blank" onmouseover="window.status='상품상세조회';return true;" onmouseout="window.status='';return true;"><FONT class="prname"><?=viewproductname($qnadata->productname,$qnadata->etctype,"").(strlen($qnadata->selfcode)>0?" - ".$qnadata->selfcode:"")?></FONT></A></td>
							</tr>
							<tr>
								<td>상품가격</td>
								<td align="center">:</td>
								<td><font class="prprice">
<?
							if($dicker=dickerview($qnadata->etctype,number_format($qnadata->sellprice)."원",1)) {
								echo $dicker;
							} else if(strlen($_data->optiontitle)==0) {
								echo "<img src=\"".$Dir."images/common/won_icon.gif\" border=\"0\" align=\"absmiddle\">".number_format($qnadata->sellprice)."원";
								if (strlen($qnadata->option_price)!=0) echo "(기본가)";
							} else {
								if (strlen($qnadata->optionprice)==0) echo number_format($qnadata->sellprice)."원";
								else echo ereg_replace("\[PRICE\]",number_format($qnadata->sellprice),$_data->optiontitle);
							}
							if ($qnadata->quantity=="0") echo soldout();
?>
								</font></td>
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
				</table>

				<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
				<tr><td height=20></td></tr>
				<tr><td height=1 bgcolor=red></td></tr>
				</table>

				<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
				<tr>
					<td valign=top style=background-repeat:repeat-x bgcolor="e7e7e7">
					<table width=100% border=0 cellspacing=0 cellpadding=0>
					<tr>
						<td bgcolor=F5F5F5>
						<table width="100%" border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td style=background-repeat:repeat-y;background-position:right;padding:9 width="88%">
							<B>제 목 : <?=$qnadata->title?></B>
							</td>
							<td align="left"><?=date("Y/m/d",$qnadata->writetime)?></td>
						</tr>
						</table>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				<tr><td height=1 bgcolor=#E7E7E7></td></tr>
				<?if(strlen($file_name1)>0) {?>
				<TR>
					<TD align="right" style="padding:3;"><font color="#FF6600">첨부파일 : <?=$file_name1?></font></TD>
				</TR>
				<?}?>
				<tr>
					<td bgcolor=ffffff style=background-repeat:repeat-y;background-position:right;padding:9>
					<table border=0 cellpadding=0 cellspacing=0 width=100%>
					<TR>
						<TD style="word-break:break-all;" valign="top">
						<span style="width:100%;line-height:160%;"> 
						<?=nl2br($qnadata->content)?>
						</span>
						</TD>
					</TR>
					<TR>
						<TD style="word-break:break-all;" valign="top">
						<?if ($upload_file1) {?>
						<span style="width:100%;line-height:160%;text-align:center"> 
						<?=$upload_file1?>
						</span>
						<?}?>
						</td>
					</tr>
<?
					//관련답변
					$sql = "SELECT a.num, a.thread, a.pos, a.depth, a.name, a.deleted, a.title, a.writetime ";
					$sql.= "FROM tblboard a, tblproduct b ".$qry." ";
					$sql.= "AND a.thread=".$qnadata->thread." ORDER BY a.pos ";
					$result=mysql_query($sql, get_db_conn());
					$rows=mysql_num_rows($result);
					if($rows>0) {
						echo "<tr><td height=5></td></tr>\n";
						echo "<tr>\n";
						echo "	<td bgcolor=#E7E7E7>\n";
						echo "	<table border=0 cellpadding=5 cellspacing=4 width=100%>\n";
						echo "	<tr>\n";
						echo "		<td bgcolor=#FFFFFF>\n";
						echo "		<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
						echo "		<tr><td style=\"padding-bottom:5\"><img src=images/qna_article_reply.gif border=0></td></tr>\n";
						while($row=mysql_fetch_object($result)) {
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
							if($qnadata->num==$row->num) $subject="<B>".$subject."</B>";
							echo "	<tr bgcolor=#FFFFFF>\n";
							echo "		<td width=100% nowrap style=padding-top:3;padding-left:3 align=left>";
							echo "		<span style='width:97%;overflow:hidden;text-overflow:ellipsis;'>\n";
							echo "		<A HREF=\"javascript:viewArticle(".$row->num.")\">".$subject."</A>\n";
							echo "		</span>\n";
							echo "		</td>\n";
							echo "	</tr>\n";
						}
						echo "		</table>\n";
						echo "		</td>\n";
						echo "	</tr>\n";
						echo "	</table>\n";
						echo "	</td>\n";
						echo "</tr>\n";
					}
					mysql_free_result($result);
?>
					</table>
					</td>
				</tr>
				<tr><td height=1 bgcolor=#E7E7E7></td></tr>
				<tr><td height=12></td></tr>
				<tr>
					<td align=center>
					<A HREF="javascript:listArticle()"><img src="images/btn_list.gif" border=0></A>
					<A HREF="javascript:replyArticle(<?=$qnadata->num?>)"><img src="images/btn_reply.gif" border=0></A>
					<A HREF="javascript:modifyArticle(<?=$qnadata->num?>)"><img src="images/btn_modify03.gif" border=0></A>
					<A HREF="javascript:deleteArticle(<?=$qnadata->num?>)"><img src="images/btn_delete.gif" border=0></A>
					</td>
				</tr>
				
				<?if(is_object($prevdata) || is_object($nextdata)){?>

				<tr><td height=25></td></tr>
				<tr>
					<td>
					<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
					<tr><td height=1 bgcolor=red></td></tr>
					</table>

					<table border=0 cellpadding=0 cellspacing=1 width=100% bgcolor=E7E7E7 style="table-layout:fixed">
					<col width=60></col>
					<col width=></col>
					<col width=80></col>
					<col width=80></col>
					<tr height=28 align=center bgcolor=F5F5F5>
						<td><B>번호</B></td>
						<td><B>제목</B></td>
						<td><B>글쓴이</B></td>
						<td><B>등록일</B></td>
					</tr>
					<?if(is_object($prevdata)){?>
					<tr height=28 bgcolor=#FFFFFF>
						<td align=center>이전글</td>
						<td width=100% nowrap style=padding-top:3;padding-left:3 align=left>
						<span style='width:97%;overflow:hidden;text-overflow:ellipsis;'>
						<A HREF="javascript:viewArticle(<?=$prevdata->num?>)"><?=strip_tags($prevdata->title)?></A>
						</span>
						</td>
						<td align=center><?=$prevdata->name?></td>
						<td align=center><?=date("Y-m-d",$prevdata->writetime)?></td>
					</tr>
					<?}?>
					<?if(is_object($nextdata)){?>
					<tr height=28 bgcolor=#FFFFFF>
						<td align=center>다음글</td>
						<td width=100% nowrap style=padding-top:3;padding-left:3 align=left>
						<span style='width:97%;overflow:hidden;text-overflow:ellipsis;'>
						<A HREF="javascript:viewArticle(<?=$nextdata->num?>)"><?=strip_tags($nextdata->title)?></A>
						</span>
						</td>
						<td align=center><?=$nextdata->name?></td>
						<td align=center><?=date("Y-m-d",$nextdata->writetime)?></td>
					</tr>
					<?}?>
					</table>
					</td>
				</tr>

				<?}?>

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

<form name=procForm method=post>
<input type=hidden name=code value="<?=$code?>">
<input type=hidden name=search_start value="<?=$search_start?>">
<input type=hidden name=search_end value="<?=$search_end?>">
<input type=hidden name=s_check value="<?=$s_check?>">
<input type=hidden name=search value="<?=$search?>">
<input type=hidden name=block value="<?=$block?>">
<input type=hidden name=gotopage value="<?=$gotopage?>">
<input type=hidden name=exec>
<input type=hidden name=num>
</form>

</table>

<iframe name="processFrame" src="about:blank" width="0" height="0" scrolling=no frameborder=no></iframe>

<?=$onload?>

<? INCLUDE "copyright.php"; ?>