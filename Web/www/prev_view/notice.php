<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$setup[page_num] = 10;
$setup[list_num] = 10;

$type=$_REQUEST["type"];	//list, view
$code=$_REQUEST["code"];
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

$list=$_SERVER[PHP_SELF]."?block=".$block."&gotopage=".$gotopage;
$close="javascript:window.close()";
if($type=="view") {
	$sql = "UPDATE tblnotice SET access=access+1 WHERE date='".$code."' ";
	mysql_query($sql,get_db_conn());

	$sql = "SELECT * FROM tblnotice WHERE date='".$code."' ";
	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		$date=substr($row->date,0,4)."/".substr($row->date,4,2)."/".substr($row->date,6,2);
		$access=$row->access;
		$subject=$row->subject;
		$content="<pre>".$row->content."</pre>";
	} else {
		echo "<html><head><title></title></head><body onload=\"alert('해당 공지사항이 없습니다.');location.href='".$list."'\"></body></html>";exit;
	}
	mysql_free_result($result);

	//이전글
	$sql = "SELECT * FROM tblnotice WHERE date>'".$code."' ";
	$sql.= "ORDER BY date ASC LIMIT 1 ";
	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		$prev="<a href=\"".$_SERVER[PHP_SELF]."?type=view&code=".$row->date."\">".$row->subject."</a>";
	} else {
		$prev="<a href=\"javascript:alert('이전글이 존재하지 않습니다.')\">이전글이 존재하지 않습니다.</a>";
	}
	mysql_free_result($result);

	//다음글
	$sql = "SELECT * FROM tblnotice WHERE date<'".$code."' ";
	$sql.= "ORDER BY date DESC LIMIT 1 ";
	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		$next="<a href=\"".$_SERVER[PHP_SELF]."?type=view&code=".$row->date."\">".$row->subject."</a>";
	} else {
		$next="<a href=\"javascript:alert('다음글이 존재하지 않습니다.')\">다음글이 존재하지 않습니다.</a>";
	}
	mysql_free_result($result);

	$sql = "SELECT filename,body FROM tbldesignnewpage WHERE type='noticeview' ";
	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		$size=explode("",$row->filename);
		$xsize=(int)$size[0];
		$ysize=(int)$size[1];
		$body=$row->body;
		$body=str_replace("[DIR]",$Dir,$body);

		$body="<script>window.resizeTo(".$xsize.",".$ysize.");</script>\n".$body;
		$newdesign="Y";
	}
	mysql_free_result($result);

	if($newdesign!="Y") {
		if(file_exists($Dir.TempletDir."notice/noticeview".$_data->design_notice.".php")) {
			$fp=fopen($Dir.TempletDir."notice/noticeview".$_data->design_notice.".php","r");
			if($fp) {
				while (!feof($fp)) {$buffer.= fgets($fp, 1024);}
			}
			fclose($fp);
			$body=$buffer;
		}
	}

	$pattern=array ("(\[DIR\])","(\[SUBJECT\])","(\[CONTENT\])","(\[DATE\])","(\[ACCESS\])","(\[PREV\])","(\[NEXT\])","(\[LIST\])","(\[CLOSE\])");
	$replace=array ($Dir,$subject,$content,$date,$access,$prev,$next,$list,$close);
	$body=preg_replace($pattern,$replace,$body);
} else {
	$listing="";
	$pageing="";

	$listing.="<table border=0 cellpadding=0 cellspacing=0 width=100% style=\"table-layout:fixed\">\n";
	$listing.="<col width=3></col><col width=></col><col width=85></col><col width=3></col>\n";
	$listing.="<tr height=30 bgcolor=#f2f2f2>\n";
	$listing.="	<td>&nbsp;</td>\n";
	$listing.="	<td align=center>제 목</td>\n";
	$listing.="	<td align=center>게시일</td>\n";
	$listing.="	<td>&nbsp;</td>\n";
	$listing.="</tr>\n";
	$listing.="<tr><td colspan=4 height=1 bgcolor=#dadada></td></tr>\n";

	$sql = "SELECT COUNT(*) as t_count FROM tblnotice ";
	$result = mysql_query($sql,get_db_conn());
	$row = mysql_fetch_object($result);
	$t_count = $row->t_count;
	mysql_free_result($result);
	$pagecount = (($t_count - 1) / $setup[list_num]) + 1;

	$sql = "SELECT date,subject FROM tblnotice ";
	$sql.= "ORDER BY date DESC ";
	$sql.= "LIMIT " . ($setup[list_num] * ($gotopage - 1)) . ", " . $setup[list_num];
	$result=mysql_query($sql,get_db_conn());
	$i=0;
	while($row=mysql_fetch_object($result)) {
		$i++;
		$date=substr($row->date,0,4)."/".substr($row->date,4,2)."/".substr($row->date,6,2);
		$n_bgcolor = "#FFFFFF";
		if ($i % 2 == 0) $n_bgcolor = "#f4f4f4";

		$listing.="<tr>\n";
		$listing.="	<td></td>\n";
		$listing.="	<td colspan=2>\n";
		$listing.="	<table border=0 cellpadding=0 cellspacing=0 width=100% style=\"table-layout:fixed\">\n";
		$listing.="	<col width=></col><col width=85></col>\n";
		$listing.="	<tr height=25 bgcolor=".$n_bgcolor.">\n";
		$listing.="		<td style=\"padding-left:10\"><A HREF=\"".$_SERVER[PHP_SELF]."?type=view&code=".$row->date."\">".$row->subject."</A></td>\n";
		$listing.="		<td align=center>".$date."</td>\n";
		$listing.="	</tr>\n";
		$listing.="	</table>\n";
		$listing.="	</td>\n";
		$listing.="	<td></td>\n";
		$listing.="</tr>\n";
	}
	mysql_free_result($result);

	$listing.="<tr><td colspan=4 height=1 bgcolor=#dadada></td></tr>\n";
	$listing.="</table>\n";

	$total_block = intval($pagecount / $setup[page_num]);

	if (($pagecount % $setup[page_num]) > 0) {
		$total_block = $total_block + 1;
	}

	$total_block = $total_block - 1;

	if (ceil($t_count/$setup[list_num]) > 0) {
		// 이전	x개 출력하는 부분-시작
		$a_first_block = "";
		if ($nowblock > 0) {
			$a_first_block .= "<a href='javascript:GoPage(0,1);' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='첫 페이지';return true\">[1...]</a>&nbsp;&nbsp;";

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
		}		// 마지막 블럭에서의 표시부분-끝


		$a_last_block = "";
		if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
			$last_block = ceil($t_count/($setup[list_num]*$setup[page_num])) - 1;
			$last_gotopage = ceil($t_count/$setup[list_num]);

			$a_last_block .= "&nbsp;&nbsp;<a href='javascript:GoPage(".$last_block.",".$last_gotopage.");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='마지막 페이지';return true\">[...".$last_gotopage."]</a>";

			$next_page_exists = true;
		}

		// 다음 10개 처리부분...

		$a_next_page = "";
		if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
			$a_next_page .= "&nbsp;&nbsp;<a href='javascript:GoPage(".($nowblock+1).",".($setup[page_num]*($nowblock+1)+1).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='다음 ".$setup[page_num]." 페이지';return true\">[next]</a>";

			$a_next_page = $a_next_page.$a_last_block;
		}
	} else {
		$print_page = "<B>1</B>";
	}

	$pageing=$a_div_prev_page.$a_prev_page.$print_page.$a_next_page.$a_div_next_page;

	$sql = "SELECT filename,body FROM tbldesignnewpage_prev WHERE type='noticelist' ";
	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		$size=explode("",$row->filename);
		$xsize=1024;
		$ysize=768;
		$body=$row->body;
		$body=str_replace("[DIR]",$Dir,$body);

		$body="<script>window.resizeTo(".$xsize.",".$ysize.");</script>\n".$body;
		$newdesign="Y";
	}
	mysql_free_result($result);

	if($newdesign!="Y") {
		if(file_exists($Dir.TempletDir."notice/noticelist".$_data->design_notice.".php")) {
			$fp=fopen($Dir.TempletDir."notice/noticelist".$_data->design_notice.".php","r");
			if($fp) {
				while (!feof($fp)) {$buffer.= fgets($fp, 1024);}
			}
			fclose($fp);
			$body=$buffer;
		}
	}

	$pattern=array ("(\[DIR\])","(\[LISTING\])","(\[PAGEING\])","(\[CLOSE\])");
	$replace=array ($Dir,$listing,$pageing,$close);
	$body=preg_replace($pattern,$replace,$body);
}
?>

<html>
<head>
<title>공지사항</title>
<meta http-equiv="CONTENT-TYPE" content="text/html;charset=EUC-KR">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<style>
td {font-family:Tahoma;color:666666;font-size:9pt;}

tr {font-family:Tahoma;color:666666;font-size:9pt;}
BODY,TD,SELECT,DIV,form,TEXTAREA,center,option,pre,blockquote {font-family:Tahoma;color:000000;font-size:9pt;}

A:link    {color:333333;text-decoration:none;}

A:visited {color:333333;text-decoration:none;}

A:active  {color:333333;text-decoration:none;}

A:hover  {color:#CC0000;text-decoration:none;}
</style>
<SCRIPT LANGUAGE="JavaScript">
<!--
function GoPage(block,gotopage) {
	document.location.href="<?=$_SERVER[PHP_SELF]?>?block="+block+"&gotopage="+gotopage;
}
//-->
</SCRIPT>
</head>

<body topmargin=0 leftmargin=0 rightmargin=0 marginheight=0 marginwidth=0>
<?=$body?>
</body>
</html>