<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
INCLUDE ("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "go-3";
$MenuCode = "gong";
if (!$_usersession->isAllowedTask($PageCode)) {
	INCLUDE ("AccessDeny.inc.php");
	exit;
}
#########################################################

$storeimagepath=DocumentRoot."/gonggu/upfile/";

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

$mode=$_POST["mode"];
$gong_seq=$_POST["gong_seq"];
$allid=$_POST["allid"];

if($mode=="delete" && strlen($gong_seq)>0) {
	$sql = "SELECT COUNT(*) as cnt FROM tblgongresult WHERE gong_seq='".$gong_seq."' ";
	$result=mysql_query($sql,get_db_conn());
	$row=mysql_fetch_object($result);
	mysql_free_result($result);
	if($row->cnt>0) {
		$onload="<script>alert(\"해당 공동구매에 참여자가 있어 삭제가 불가능합니다.\\n\\n참여자를 먼저 삭제 후 삭제하시기 바랍니다.\");</script>";
	} else {
		$sql = "SELECT image1,image2,image3 FROM tblgonginfo WHERE gong_seq='".$gong_seq."' ";
		$result=mysql_query($sql,get_db_conn());
		$row=mysql_fetch_object($result);
		mysql_free_result($result);
		$oldfiles=array(&$row->image1,&$row->image2,&$row->image3);

		$sql = "DELETE FROM tblgonginfo WHERE gong_seq='".$gong_seq."' ";
		$delete=mysql_query($sql,get_db_conn());
		if($delete) {
			$sql = "DELETE FROM tblgongresult WHERE gong_seq='".$gong_seq."' ";
			mysql_query($sql,get_db_conn());
			for($i=0;$i<3;$i++) {
				if(strlen($oldfiles[$i])>0 && file_exists($storeimagepath.$oldfiles[$i])) {
					unlink($storeimagepath.$oldfiles[$i]);
				}
			}
			$onload="<script>alert(\"해당 공동구매를 삭제하였습니다.\");</script>";
		} else {
			$onload="<script>alert(\"해당 공동구매중 오류가 발생하였습니다.\");</script>";
		}
	}
	$gong_seq="";
} else if($mode=="process_receipt" && strlen($gong_seq)>0 && strlen($allid)>0) {
	$allid=ereg_replace("\\\\","",$allid);
	$sql = "UPDATE tblgongresult SET process_gbn='B' ";
	$sql.= "WHERE gong_seq='".$gong_seq."' AND id IN (".$allid.")";
	mysql_query($sql,get_db_conn());
	$onload="<script>alert(\"선택하신 공동구매 참여자를 입금확인 하였습니다.\");</script>";
} else if($mode=="process_deli" && strlen($gong_seq)>0 && strlen($allid)>0) {
	$allid=ereg_replace("\\\\","",$allid);
	$sql = "UPDATE tblgongresult SET process_gbn='E' ";
	$sql.= "WHERE gong_seq='".$gong_seq."' AND id IN (".$allid.")";
	mysql_query($sql,get_db_conn());
	$onload="<script>alert(\"선택하신 공동구매 참여자를 배송확인 하였습니다.\");</script>";
} else if($mode=="process_del" && strlen($gong_seq)>0 && strlen($allid)>0) {
	$allid=ereg_replace("\\\\","",$allid);
	$sql = "DELETE FROM tblgongresult WHERE gong_seq='".$gong_seq."' AND id IN (".$allid.")";
	mysql_query($sql,get_db_conn());

	$sql = "SELECT SUM(buy_cnt) as buy_cnt FROM tblgongresult WHERE gong_seq='".$gong_seq."' ";
	$result=mysql_query($sql,get_db_conn());
	$row=mysql_fetch_object($result);
	mysql_free_result($result);
	if($row->buy_cnt==NULL || strlen($row->buy_cnt)==0) $buy_cnt=0;
	else $buy_cnt=$row->buy_cnt;

	$sql = "UPDATE tblgonginfo SET bid_cnt='".$buy_cnt."' WHERE gong_seq='".$gong_seq."' ";
	mysql_query($sql,get_db_conn());
	$onload="<script>alert(\"선택하신 공동구매 참여자를 삭제 하였습니다.\");</script>";
}
?>

<? INCLUDE "header.php"; ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm() {

}

function GongModify(gong_seq) {
	document.modifyform.gong_seq.value=gong_seq;
	document.modifyform.submit();
}

function GongDelete(gong_seq) {
	if(confirm("해당 공동구매를 삭제하시겠습니까?")) {
		document.form1.mode.value="delete";
		document.form1.gong_seq.value=gong_seq;
		document.form1.submit();
	}
}

function GongMail(gong_seq) {
	window.open('gong_gongmail_pop.php?gong_seq='+gong_seq,"gong_mail","width=430,height=430");
}

function BidsView(gong_seq) {
	document.form1.gong_seq.value=gong_seq;
	document.form1.submit();
}

function AllExcel(gong_seq) {
	document.excelform.submit();
}

function MemberView(id){
	document.memberform.search.value=id;
	document.memberform.submit();
}

function MemberMail(mail){
	document.mailform.rmail.value=mail;
	document.mailform.submit();
}

function GoPage(block,gotopage) {
	document.form1.gong_seq.value="";
	document.form1.block.value = block;
	document.form1.gotopage.value = gotopage;
	document.form1.submit();
}

function CheckAllReceipt(checked) {
	try {
		for(i=1;i<document.form2.ckreceipt.length;i++) {
			document.form2.ckreceipt[i].checked=checked;
		}
	} catch(e) {}
}
function CheckAllDeli(checked) {
	try {
		for(i=1;i<document.form2.ckdeli.length;i++) {
			document.form2.ckdeli[i].checked=checked;
		}
	} catch(e) {}
}
function CheckAllDel(checked) {
	try {
		for(i=1;i<document.form2.ckdel.length;i++) {
			document.form2.ckdel[i].checked=checked;
		}
	} catch(e) {}
}

function ResultReceipt() {
	allid="";
	try {
		for(i=1;i<document.form2.ckreceipt.length;i++) {
			if(document.form2.ckreceipt[i].checked==true) allid+=",'"+document.form2.ckreceipt[i].value+"'";
		}
		if(allid.length==0) {
			alert("입금확인할 공동구매 참여자를 선택하세요.");
			return;
		} else {
			if(!confirm("선택하신 공구 참여자를 입금확인 하시겠습니까?")) return;
			allid=allid.substring(1,allid.length);
			document.prform.mode.value="process_receipt";
			document.prform.allid.value=allid;
			document.prform.submit();
		}
	} catch(e) {}
}
function ResultDeli() {
	allid="";
	try {
		for(i=1;i<document.form2.ckdeli.length;i++) {
			if(document.form2.ckdeli[i].checked==true) allid+=",'"+document.form2.ckdeli[i].value+"'";
		}
		if(allid.length==0) {
			alert("배송확인할 공동구매 참여자를 선택하세요.");
			return;
		} else {
			if(!confirm("선택하신 공구 참여자를 배송확인 하시겠습니까?")) return;
			allid=allid.substring(1,allid.length);
			document.prform.mode.value="process_deli";
			document.prform.allid.value=allid;
			document.prform.submit();
		}
	} catch(e) {}
}
function ResultDel() {
	allid="";
	try {
		for(i=1;i<document.form2.ckdel.length;i++) {
			if(document.form2.ckdel[i].checked==true) allid+=",'"+document.form2.ckdel[i].value+"'";
		}
		if(allid.length==0) {
			alert("삭제할 공동구매 참여자를 선택하세요.");
			return;
		} else {
			if(!confirm("선택하신 공구 참여자를 삭제 하시겠습니까?")) return;
			allid=allid.substring(1,allid.length);
			document.prform.mode.value="process_del";
			document.prform.allid.value=allid;
			document.prform.submit();
		}
	} catch(e) {}
}


function MemoMouseOver(cnt) {
	obj = event.srcElement;
	WinObj=eval("document.all.memo"+cnt);
	obj._tid = setTimeout("MemoView(WinObj)",200);
}
function MemoView(WinObj) {
	WinObj.style.visibility = "visible";
}
function MemoMouseOut(cnt) {
	obj = event.srcElement;
	WinObj=eval("document.all.memo"+cnt);
	WinObj.style.visibility = "hidden";
	clearTimeout(obj._tid);
}

</script>
<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
<tr>
	<td valign="top">
	<table cellpadding="0" cellspacing="0" width=100% style="table-layout:fixed">
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed"  background="images/con_bg.gif">
		<col width=198></col>
		<col width=10></col>
		<col width=></col>
		<tr>
			<td valign="top"  background="images/leftmenu_bg.gif">
			<? include ("menu_gong.php"); ?>
			</td>

			<td></td>
			<td valign="top">




<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td height="29" colspan="3">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 공구/경매 &gt; 공동구매관리 &gt; <span class="2depth_select">가격변동형 등록공구 관리</span></td>
			</tr>
			</table>
		</td>
	</tr>   
	<tr>
        <td width="16"><img src="images/con_t_01.gif" width="16" height="16" border="0"></td>
        <td background="images/con_t_01_bg.gif"></td>
        <td width="16"><img src="images/con_t_02.gif" width="16" height="16" border="0"></td>
    </tr>
    <tr>
        <td width="16" background="images/con_t_04_bg1.gif"></td>
        <td bgcolor="#ffffff" style="padding:10px">









			<table cellpadding="0" cellspacing="0" width="100%">
			<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post>
			<input type=hidden name=mode>
			<input type=hidden name=gong_seq>
			<input type=hidden name=block value="<?=$block?>">
			<input type=hidden name=gotopage value="<?=$gotopage?>">

			<tr><td height="8"></td></tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/gong_gongchangelist_title.gif" ALT=""></TD>
					</tr><tr>
<TD width="100%" background="images/title_bg.gif" height="21"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height="3"></td></tr>
			<tr>
				<td style="padding-bottom:3pt;">
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/distribute_01.gif"></TD>
					<TD COLSPAN=2 background="images/distribute_02.gif"></TD>
					<TD><IMG SRC="images/distribute_03.gif"></TD>
				</TR>
				<TR>
					<TD background="images/distribute_04.gif"></TD>
					<TD class="notice_blue"><IMG SRC="images/distribute_img.gif" ></TD>
					<TD width="100%" class="notice_blue">등록된 공동구매를 관리할 수 있습니다.</TD>
					<TD background="images/distribute_07.gif"></TD>
				</TR>
				<TR>
					<TD><IMG SRC="images/distribute_08.gif"></TD>
					<TD COLSPAN=2 background="images/distribute_09.gif"></TD>
					<TD><IMG SRC="images/distribute_10.gif"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height=20></td></tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/gong_gongchangelist_stitle1.gif" WIDTH="187" HEIGHT=31 ALT=""></TD>
					<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif">&nbsp;</TD>
					<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height=3></td></tr>
			<tr>
				<td>
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<col width=90></col>
				<col width=></col>
				<col width=75></col>
				<col width=60></col>
				<col width=50></col>
				<col width=50></col>
				<TR>
					<TD colspan=6 background="images/table_top_line.gif"></TD>
				</TR>
				<TR align=center>
					<TD class="table_cell">공구 마감일</TD>
					<TD class="table_cell1">공구 상품명</TD>
					<TD class="table_cell1">참여자</TD>
					<TD class="table_cell1">현재가</TD>
					<TD class="table_cell1">참여수</TD>
					<TD class="table_cell1">삭제</TD>
				</TR>
				<TR>
					<TD colspan="6" background="images/table_con_line.gif"></TD>
				</TR>
<?
				$colspan=6;
				$sql = "SELECT COUNT(*) as t_count FROM tblgonginfo "; 
				$result = mysql_query($sql,get_db_conn());
				$row = mysql_fetch_object($result);
				mysql_free_result($result);
				$t_count = $row->t_count;
				$pagecount = (($t_count - 1) / $setup[list_num]) + 1;

				$sql = "SELECT * FROM tblgonginfo ORDER BY end_date DESC ";
				$sql.= "LIMIT " . ($setup[list_num] * ($gotopage - 1)) . ", " . $setup[list_num];
				$result = mysql_query($sql,get_db_conn());
				$cnt=0;
				while($row=mysql_fetch_object($result)) {
					$number = ($t_count-($setup[list_num] * ($gotopage-1))-$cnt);
					$end_date=substr($row->end_date,0,4)."/".substr($row->end_date,4,2)."/".substr($row->end_date,6,2)."(".substr($row->end_date,8,2).":".substr($row->end_date,10,2).")";

					$gong_no=intval($row->bid_cnt/$row->count);
					$now_price=$row->start_price-($gong_no*$row->down_price);
					if($now_price<$row->mini_price) $now_price=$row->mini_price;

					echo "<TR>\n";
					echo "	<TD align=center class=\"td_con2\">";
					if($row->end_date<date("YmdHis") || $row->quantity<=$row->bid_cnt) {
						echo "<img src=\"images/gong_auctionlist_endicon2.gif\" width=\"55\" height=\"15\" border=\"0\">";
					} else {
						echo $end_date;
					}
					echo "	</TD>\n";
					if($row->bid_cnt==0) {	//참여자 없음
						echo "	<TD class=\"td_con1\"><A HREF=\"javascript:GongModify('".$row->gong_seq."');\">".$row->gong_name."</A></TD>\n";
						echo "	<TD align=center class=\"td_con1\">참여자 없음</td>\n";
					} else {	//입찰자 있음
						echo "	<TD class=\"td_con1\">".$row->gong_name."&nbsp;</TD>\n";
						echo "	<TD align=center class=\"td_con1\"><a href=\"javascript:BidsView('".$row->gong_seq."')\"><img src=\"images/gong_gongchangelist_cham.gif\" width=\"66\" height=\"15\" border=\"0\"></a></TD>\n";
					}
					echo "	<TD align=center class=\"td_con1\"><b><font color=\"#220F03\">".number_format($now_price)."원</font></b></TD>\n";
					echo "	<TD align=center class=\"td_con3\">".(int)$row->bid_cnt."</TD>\n";
					if($row->bid_cnt==0 || $row->gbn=="E") {
						echo "	<TD align=center class=\"td_con2\"><a href=\"javascript:GongDelete('".$row->gong_seq."')\"><img src=\"images/btn_del.gif\" width=\"50\" height=\"22\" border=\"0\"></a></TD>\n";
					} else if($row->end_date<date("YmdHis") || $row->quantity==$row->bid_cnt) {
						echo "	<TD align=center class=\"td_con2\"><a href=\"javascript:GongMail('".$row->gong_seq."')\"><img src=\"images/btn_mail.gif\" width=\"50\" height=\"22\" border=\"0\"></a></TD>\n";
					} else {
						echo "	<TD class=\"td_con2\">&nbsp;</TD>\n";
					}
					echo "</TR>\n";
					echo "<TR>\n";
					echo "	<TD colspan=".$colspan." background=\"images/table_con_line.gif\"></TD>\n";
					echo "</TR>\n";

					$cnt++;
				}
				mysql_free_result($result);
				if ($cnt==0) {
					echo "<tr><td class=td_con2 colspan=".$colspan." align=center>검색된 리뷰 정보가 존재하지 않습니다.</td></tr>";
				}
?>
				<TR>
					<TD colspan=6 background="images/table_top_line.gif"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height=10></td></tr>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="100%" align=center class="font_size">
<?
					$total_block = intval($pagecount / $setup[page_num]);

					if (($pagecount % $setup[page_num]) > 0) {
						$total_block = $total_block + 1;
					}

					$total_block = $total_block - 1;

					if (ceil($t_count/$setup[list_num]) > 0) {
						// 이전	x개 출력하는 부분-시작
						$a_first_block = "";
						if ($nowblock > 0) {
							$a_first_block .= "<a href='javascript:GoPage(0,1);' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='첫 페이지';return true\"><IMG src=\"images/icon_first.gif\" border=0 align=\"absmiddle\"></a>&nbsp;&nbsp;";

							$prev_page_exists = true;
						}

						$a_prev_page = "";
						if ($nowblock > 0) {
							$a_prev_page .= "<a href='javascript:GoPage(".($nowblock-1).",".($setup[page_num]*($block-1)+$setup[page_num]).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='이전 ".$setup[page_num]." 페이지';return true\">[prev]</a>&nbsp;&nbsp;";

							$a_prev_page = $a_first_block.$a_prev_page;
						}

						// 일반 블럭에서의 페이지 표시부분-시작

						if (intval($total_block) <> intval($nowblock)) {
							$print_page = "";
							for ($gopage = 1; $gopage <= $setup[page_num]; $gopage++) {
								if ((intval($nowblock*$setup[page_num]) + $gopage) == intval($gotopage)) {
									$print_page .= "<span class=font_orange2><B>[".(intval($nowblock*$setup[page_num]) + $gopage)."]</B></span> ";
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
									$print_page .= "<span class=font_orange2><B>[".(intval($nowblock*$setup[page_num]) + $gopage)."]</B></span> ";
								} else {
									$print_page .= "<a href='javascript:GoPage(".$nowblock.",".(intval($nowblock*$setup[page_num]) + $gopage).");' onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$setup[page_num]) + $gopage)."';return true\">[".(intval($nowblock*$setup[page_num]) + $gopage)."]</a> ";
								}
							}
						}		// 마지막 블럭에서의 표시부분-끝


						$a_last_block = "";
						if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
							$last_block = ceil($t_count/($setup[list_num]*$setup[page_num])) - 1;
							$last_gotopage = ceil($t_count/$setup[list_num]);

							$a_last_block .= "&nbsp;&nbsp;<a href='javascript:GoPage(".$last_block.",".$last_gotopage.");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='마지막 페이지';return true\"><IMG src=\"images/icon_last.gif\" border=0 align=\"absmiddle\" width=\"17\" height=\"14\"></a>";

							$next_page_exists = true;
						}

						// 다음 10개 처리부분...

						$a_next_page = "";
						if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
							$a_next_page .= "&nbsp;&nbsp;<a href='javascript:GoPage(".($nowblock+1).",".($setup[page_num]*($nowblock+1)+1).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='다음 ".$setup[page_num]." 페이지';return true\">[next]</a>";

							$a_next_page = $a_next_page.$a_last_block;
						}
					} else {
						$print_page = "<B>[1]</B>";
					}
?>
					<?=$a_div_prev_page.$a_prev_page.$print_page.$a_next_page.$a_div_next_page?>
					</td>
				</tr>
				</table>
				</td>
			</tr>
			</form>
<?
			unset($row);
			if(strlen($gong_seq)>0) {
				$sql = "SELECT * FROM tblgonginfo WHERE gong_seq='".$gong_seq."' ";
				$result=mysql_query($sql,get_db_conn());
				$row=mysql_fetch_object($result);
				mysql_free_result($result);
			}
?>
			<?if($row){?>
			<tr>
				<td height="30">&nbsp;</td>
			</tr>
			<tr>
				<td><span class="font_blue" style="font-size:9pt;"><b><img align=absmiddle src="images/btn_sound01.gif" width="14" height="14" border="0">[</span><span class="font_blue1" style="font-size:9pt;"><?=$row->gong_name?></span><span class="font_blue" style="font-size:9pt;">]</b><B>공구 참여자 관리</B><b> </span></b></td>
			</tr>
			<tr>
				<td height=3></td>
			</tr>
			<form name=form2 action="<?=$_SERVER[PHP_SELF]?>" method=post>
			<input type=hidden name=mode>
			<tr>
				<td align="center">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<col width=60></col>
				<col width=60></col>
				<col width=></col>
				<col width=60></col>
				<col width=35></col>
				<col width=35></col>
				<col width=80></col>
				<col width=80></col>
				<col width=80></col>
				<TR>
					<TD background="images/table_top_line.gif" colspan="9"></TD>
				</TR>
				<TR align=center>
					<TD class="table_cell">아이디</TD>
					<TD class="table_cell1">이름</TD>
					<TD class="table_cell1">이메일</TD>
					<TD class="table_cell1">전화/주소</TD>
					<TD class="table_cell1">메모</TD>
					<TD class="table_cell1">수량</TD>
					<TD class="table_cell1"><INPUT id=idx_receipt onclick=CheckAllReceipt(this.checked) type=checkbox><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand; TEXT-DECORATION: none" onmouseout="style.textDecoration='none'" for=idx_receipt>입금확인</LABEL></TD>
					<TD class="table_cell1"><INPUT id=idx_deli onclick=CheckAllDeli(this.checked) type=checkbox><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand; TEXT-DECORATION: none" onmouseout="style.textDecoration='none'" for=idx_deli>배송확인</LABEL></TD>
					<TD class="table_cell1"><INPUT id=idx_del onclick=CheckAllDel(this.checked) type=checkbox><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand; TEXT-DECORATION: none" onmouseout="style.textDecoration='none'" for=idx_del>삭제하기</LABEL></TD>
				</TR>
				<TR>
					<TD colspan="9" background="images/table_con_line.gif"></TD>
				</TR>
				<input type=hidden name=ckreceipt>
				<input type=hidden name=ckdeli>
				<input type=hidden name=ckdel>
<?
				$sql = "SELECT * FROM tblgongresult WHERE gong_seq='".$gong_seq."' ";
				$result=mysql_query($sql,get_db_conn());
				$i=0;
				while($row=mysql_fetch_object($result)) {
					$i++;
					echo "<TR>\n";
					echo "	<TD align=center class=\"td_con2\"><A HREF=\"javascript:MemberView('".$row->id."')\">".$row->id."</A></TD>\n";
					echo "	<TD align=center class=\"td_con1\">".$row->name."</TD>\n";
					echo "	<TD align=center class=\"td_con1\"><A HREF=\"javascript:MemberMail('".$row->email."');\">".$row->email."</A></TD>\n";
					$tel_disabled="";
					$addr_disabled="";
					if(strlen($row->tel)==0) $tel_disabled="disabled";
					if(strlen($row->address)==0) $addr_disabled="disabled";
					echo "	<TD align=center class=\"td_con1\"><a href=\"javascript:alert('".$row->tel."');\" ".$tel_disabled."><IMG SRC=\"images/member_tel.gif\" border=\"0\"></a>&nbsp;<a href=\"javascript:alert('".$row->address."');\" ".$addr_disabled."><IMG SRC=\"images/addr_home.gif\" border=\"0\"></a></TD>\n";
					echo "	<TD align=center class=\"td_con1\">";
					if(strlen($row->memo)>0) {
						echo "	<a href=\"javascript:alert('".str_replace("\r\n","",$row->memo)."');\" onMouseOver='MemoMouseOver($i)' onMouseOut=\"MemoMouseOut($i);\"><IMG SRC=\"images/btn_memo.gif\" border=\"0\"></a>";
						echo "	<div id=memo".$i." style=\"position:absolute; z-index:100; visibility:hidden;\">\n";
						echo "	<table width=250 border=0 cellspacing=0 cellpadding=5 bgcolor=#A47917>\n";
						echo "	<tr><td style=\"color:#ffffff\">".$row->memo."</td></tr>\n";
						echo "	</table>\n";
						echo "	</div>\n";
					} else {
						echo "	<IMG SRC=\"images/btn_memor.gif\" border=\"0\">";
					}
					echo "	</TD>\n";
					echo "	<TD align=center class=\"td_con1\">".$row->buy_cnt."</TD>\n";
					echo "	<TD align=center class=\"td_con1\">";
					if($row->process_gbn=="I") {
						echo "<input type=checkbox name=ckreceipt value=\"".$row->id."\">";
					} else {
						echo "완료";
					}
					echo "	</TD>\n";
					echo "	<TD align=center class=\"td_con1\">";
					if($row->process_gbn=="B") {
						echo "<input type=checkbox name=ckdeli value=\"".$row->id."\">";
					} else if($row->process_gbn=="I") {
						echo "&nbsp;";
					} else {
						echo "완료";
					}
					echo "	</TD>\n";
					echo "	<TD align=center class=\"td_con1\"><input type=checkbox name=ckdel value=\"".$row->id."\"></TD>\n";
					echo "</TR>\n";
					echo "<TR>\n";
					echo "	<TD colspan=\"9\" background=\"images/table_con_line.gif\"></TD>\n";
					echo "</TR>\n";
				}
				mysql_free_result($result);
?>
				<TR>
					<TD background="images/grayline_bg.gif" colspan="6" align=right style="padding-right:10"><a href="javascript:AllExcel();"><IMG height=26 src="images/icon_exceldown.gif" width="105" vspace=3 border=0></a></TD>
					<TD background="images/grayline_bg.gif" align=center><a href="javascript:ResultReceipt();"><IMG height=26 src="images/icon_ip.gif" width="77" vspace=3 border=0></a></TD>
					<TD background="images/grayline_bg.gif" align=center><a href="javascript:ResultDeli();"><IMG height=26 src="images/icon_trans1.gif" width="77" vspace=3 border=0></a></TD>
					<TD background="images/grayline_bg.gif" align=center><a href="javascript:ResultDel();"><IMG height=26 src="images/icon_del.gif" width="77" vspace=3 border=0></a></TD>
				</TR>
				<TR>
					<TD background="images/table_top_line.gif" colspan="9"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			</form>
			<?}?>
			<tr><td height=20></td></tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/manual_top1.gif" WIDTH=15 height="45" ALT=""></TD>
					<TD><IMG SRC="images/manual_title.gif" WIDTH=113 height="45" ALT=""></TD>
					<TD width="100%" background="images/manual_bg.gif" height="35"></TD>
					<TD background="images/manual_bg.gif">&nbsp;</TD>
					<td background="images/manual_bg.gif"><IMG SRC="images/manual_top2.gif" WIDTH=18 height="45" ALT=""></td>
				</TR>
				<TR>
					<TD background="images/manual_left1.gif"></TD>
					<TD COLSPAN=3 width="100%" valign="top" bgcolor="white" style="padding-top:8pt; padding-bottom:8pt; padding-left:4pt;" class="menual_bg">
					<table cellpadding="0" cellspacing="0" width="100%">
					<col width=20></col>
					<col width=></col>
					<tr>
						<td align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td><span class="font_dotline">가격변동형 등록공구 관리</span></td>
					</tr>
					<tr>
						<td align="right">&nbsp;</td>
						<td class="space_top" style="letter-spacing:-0.5pt;">- 참여자보기를 클릭하면 해당 공동구매에 참여한 구매자 리스트가 출력됩니다.</td>
					</tr>
					<tr>
						<td align="right">&nbsp;</td>
						<td class="space_top" style="letter-spacing:-0.5pt;">- 메일 버튼을 이용하여 참여한 구매자에게 전체 메일 발송이 가능하며, 참여자 리스트에서 입금, 배송, 삭제가 가능합니다.</td>
					</tr>
					<tr>
						<td align="right">&nbsp;</td>
						<td class="space_top" style="letter-spacing:-0.5pt;">- 참여자가 없는 공동구매건에 한해 상품명 클릭후 수정이 가능합니다.</td>
					</tr>
					<tr>
						<td align="right">&nbsp;</td>
						<td class="space_top" style="letter-spacing:-0.5pt;">- 공동구매 목록 누적시 마감된 공동구매는 삭제 처리해 주시면 됩니다.</td>
					</tr>
					</table>
					</TD>
					<TD background="images/manual_right1.gif"><IMG SRC="images/manual_right1.gif" WIDTH=18 HEIGHT="2" ALT=""></TD>
				</TR>
				<TR>
					<TD><IMG SRC="images/manual_left2.gif" WIDTH=15 HEIGHT=8 ALT=""></TD>
					<TD COLSPAN=3 background="images/manual_down.gif"><IMG SRC="images/manual_down.gif" WIDTH="4" HEIGHT=8 ALT=""></TD>
					<TD><IMG SRC="images/manual_right2.gif" WIDTH=18 HEIGHT=8 ALT=""></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height="50"></td>
			</tr>
			<form name=prform action="<?=$_SERVER[PHP_SELF]?>" method=post>
			<input type=hidden name=mode>
			<input type=hidden name=allid>
			<input type=hidden name=gong_seq value="<?=$gong_seq?>">
			<input type=hidden name=block value="<?=$block?>">
			<input type=hidden name=gotopage value="<?=$gotopage?>">
			</form>

			<form name=modifyform action="gong_gongchangereg.php" method=post>
			<input type=hidden name=gong_seq>
			</form>

			<form name=excelform action="gong_gongexcel.php" method=post>
			<input type=hidden name=gong_seq value="<?=$gong_seq?>">
			</form>

			<form name=memberform action="member_list.php" method=post>
			<input type=hidden name=search>
			</form>

			<form name=mailform action="member_mailsend.php" method=post>
			<input type=hidden name=rmail>
			</form>
			</table>
			</td>
		</tr>
		</table>
</td>
        <td width="16" background="images/con_t_02_bg.gif"></td>
    </tr>
    <tr>
        <td width="16"><img src="images/con_t_04.gif" width="16" height="16" border="0"></td>
        <td background="images/con_t_04_bg.gif"></td>
        <td width="16"><img src="images/con_t_03.gif" width="16" height="16" border="0"></td>
    </tr>
    <tr><td height="20"></td></tr>
</table>


		</td>
	</tr>
	</table>
	</td>
</tr>
</table>
<?=$onload?>
<? INCLUDE "copyright.php"; ?>