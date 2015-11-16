<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

unset($row);
$sql = "SELECT * FROM tbldesign ";
$result=mysql_query($sql,get_db_conn());
if($crow=mysql_fetch_object($result)) {

} else {
	$crow->introtype="C";
}
mysql_free_result($result);

?>

<HTML>
<HEAD>
<TITLE><?=$_data->shoptitle?> - 회사소개</TITLE>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META http-equiv="X-UA-Compatible" content="IE=5" />
<META name="description" content="<?=(strlen($_data->shopdescription)>0?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<?include($Dir."lib/style.php")?>
</HEAD>

<body<?=(substr($_data->layoutdata["MOUSEKEY"],0,1)=="Y"?" oncontextmenu=\"return false;\"":"")?><?=(substr($_data->layoutdata["MOUSEKEY"],1,1)=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=(substr($_data->layoutdata["MOUSEKEY"],2,1)=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>

<? include ($Dir.MainDir.$_data->menu_type.".php") ?>

<table border="0" cellpadding="0" cellspacing="0" width="100%">
<?
if ($crow->introtype=="A") {
	echo "<tr>\n";
	if ($_data->title_type=="Y" && file_exists($Dir.DataDir."design/company_title.gif")) {
		echo "<td><img src=\"".$Dir.DataDir."design/company_title.gif\" border=\"0\" alt=\"회사소개\"></td>\n";
	} else {
		echo "<td>\n";
		/*
		echo "<TABLE WIDTH=100% BORDER=0 CELLPADDING=0 CELLSPACING=0>\n";
		echo "<TR>\n";
		echo "	<TD><IMG SRC=".$Dir."images/".$_data->icon_type."/company_title_head.gif ALT=></TD>\n";
		echo "	<TD width=100% valign=top background=".$Dir."images/".$_data->icon_type."/company_title_bg.gif></TD>\n";
		echo "	<TD width=40><IMG SRC=".$Dir."images/".$_data->icon_type."/company_title_tail.gif ALT=></TD>\n";
		echo "</TR>\n";
		echo "</TABLE>\n";
		*/
		echo "<div class=\"subpageTitle\">회사소개</div>";
		echo "</td>\n";
	}
	echo "</tr>\n";
	echo "<tr>\n";
	echo "	<td style=\"padding:20px;padding-top:0px;\"><pre>".$crow->content."</pre></td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "	<td valign=\"top\">\n";
	echo "	<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
	echo "	<tr>\n";
	echo "		<td width=\"50%\" valign=\"top\" style=\"padding-left:20px;\">";	//회사개요, 회사연혁, 소비자 센터
	echo "		<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
	echo "		<tr>\n";
	echo "			<td>\n";
	echo "			<TABLE cellSpacing=\"0\" cellPadding=\"0\" width=\"100%\">\n";
	echo "			<TR>\n";
	echo "				<TD><IMG src=\"".$Dir."images/common/design_useinfo_skin1_sticon.gif\" border=\"0\"></TD>\n";
	echo "				<TD width=\"100%\" background=\"".$Dir."images/common/design_useinfo_skin1_stbg.gif\"><font color=\"#333333\"><b>회사개요</b></font></TD>\n";
	echo "				<TD><IMG src=\"".$Dir."images/common/design_useinfo_skin1_stend.gif\" border=\"0\"></TD>\n";
	echo "			</TR>\n";
	echo "			</TABLE>\n";
	echo "			</td>\n";
	echo "		</tr>\n";
	echo "		<tr><td height=\"10\"></td></tr>\n";
	echo "		<tr>\n";
	echo "			<td style=\"padding-left:20px;\">\n";
	echo "			<table border=\"0\" cellpadding=\"1\" cellspacing=\"0\">\n";
	echo "			<tr>\n";
	echo "				<td><img src=\"".$Dir."images/common/text_icon01.gif\" border=\"0\"></td>\n";
	echo "				<td style=\"color:#333333\">회사명</td>\n";
	echo "				<td>&nbsp;:&nbsp;</td>\n";
	echo "				<td>".$crow->companyname."</td>\n";
	echo "			</tr>\n";
	echo "			<tr>\n";
	echo "				<td><img src=\"".$Dir."images/common/text_icon01.gif\" border=\"0\"></td>\n";
	echo "				<td  style=\"color:#333333\">상점명</td>\n";
	echo "				<td>&nbsp;:&nbsp;</td>\n";
	echo "				<td>".$crow->shopname."</td>\n";
	echo "			</tr>\n";
	echo "			<tr>\n";
	echo "				<td><img src=\"".$Dir."images/common/text_icon01.gif\" border=\"0\"></td>\n";
	echo "				<td style=\"color:#333333\">대표이사</td>\n";
	echo "				<td>&nbsp;:&nbsp;</td>\n";
	echo "				<td>".$crow->ownername."</td>\n";
	echo "			</tr>\n";
	echo "			<tr>\n";
	echo "				<td><img src=\"".$Dir."images/common/text_icon01.gif\" border=\"0\"></td>\n";
	echo "				<td style=\"color:#333333\">이메일</td>\n";
	echo "				<td>&nbsp;:&nbsp;</td>\n";
	echo "				<td>".$crow->owneremail."</td>\n";
	echo "			</tr>\n";
	echo "			</table>\n";
	echo "			</td>\n";
	echo "		</tr>\n";
	echo "		<tr><td height=\"20\"></td></tr>\n";
	echo "		<tr>\n";
	echo "			<td>\n";
	echo "			<TABLE cellSpacing=\"0\" cellPadding=\"0\" width=\"100%\">\n";
	echo "			<TR>\n";
	echo "				<TD><IMG src=\"".$Dir."images/common/design_useinfo_skin1_sticon.gif\" border=\"0\"></TD>\n";
	echo "				<TD width=\"100%\" background=\"".$Dir."images/common/design_useinfo_skin1_stbg.gif\"><font color=\"#333333\"><b>회사연혁</b></font></TD>\n";
	echo "				<TD><IMG src=\"".$Dir."images/common/design_useinfo_skin1_stend.gif\" border=\"0\"></TD>\n";
	echo "			</TR>\n";
	echo "			</TABLE>\n";
	echo "			</td>\n";
	echo "		</tr>\n";
	echo "		<tr><td height=\"10\"></td></tr>\n";
	echo "		<tr><td style=\"padding-left:20\"><pre>".$crow->history."</pre></td></tr>\n";
	echo "		<tr>\n";
	echo "			<td>\n";
	echo "			<TABLE cellSpacing=\"0\" cellPadding=\"0\" width=\"100%\">\n";
	echo "			<TR>\n";
	echo "				<TD><IMG src=\"".$Dir."images/common/design_useinfo_skin1_sticon.gif\" border=\"0\"></TD>\n";
	echo "				<TD width=\"100%\" background=\"".$Dir."images/common/design_useinfo_skin1_stbg.gif\"><font color=\"#333333\"><b>소비자 센터</b></font></TD>\n";
	echo "				<TD><IMG src=\"".$Dir."images/common/design_useinfo_skin1_stend.gif\" border=\"0\"></TD>\n";
	echo "			</TR>\n";
	echo "			</TABLE>\n";
	echo "			</td>\n";
	echo "		</tr>\n";
	echo "		<tr><td height=\"10\"></td></tr>\n";
	echo "		<tr>\n";
	echo "			<td style=\"padding-left:20\">\n";
	echo "			<table border=\"0\" cellpadding=\"1\" cellspacing=\"0\">\n";
	echo "			<tr>\n";
	echo "				<td><img src=\"".$Dir."images/common/text_icon01.gif\" border=\"0\"></td>\n";
	echo "				<td style=\"color:#333333\">전화번호</td>\n";
	echo "				<td>&nbsp;:&nbsp;</td>\n";
	echo "				<td>".$crow->info_tel."</td>\n";
	echo "			</tr>\n";
	echo "			<tr>\n";
	echo "				<td><img src=\"".$Dir."images/common/text_icon01.gif\" border=\"0\"></td>\n";
	echo "				<td style=\"color:#333333\">팩스</td>\n";
	echo "				<td>&nbsp;:&nbsp;</td>\n";
	echo "				<td>".$crow->info_fax."</td>\n";
	echo "			</tr>\n";
	echo "			<tr>\n";
	echo "				<td><img src=\"".$Dir."images/common/text_icon01.gif\" border=\"0\"></td>\n";
	echo "				<td style=\"color:#333333\">상담시간</td>\n";
	echo "				<td>&nbsp;:&nbsp;</td>\n";
	echo "				<td>".$crow->info_counsel."</td>\n";
	echo "			</tr>\n";
	echo "			<tr>\n";
	echo "				<td><img src=\"".$Dir."images/common/text_icon01.gif\" border=\"0\"></td>\n";
	echo "				<td style=\"color:#333333\">이메일</td>\n";
	echo "				<td>&nbsp;:&nbsp;</td>\n";
	echo "				<td><A HREF=\"mailto:".$crow->info_email."\">".$crow->info_email."</A></td>\n";
	echo "			</tr>\n";
	echo "			<tr>\n";
	echo "				<td><img src=\"".$Dir."images/common/text_icon01.gif\" border=\"0\"></td>\n";
	echo "				<td style=\"color:#333333\">정보담당</td>\n";
	echo "				<td>&nbsp;:&nbsp;</td>\n";
	echo "				<td>".$crow->privercyname." / <A HREF=\"mailto:".$crow->privercyemail."\">".$crow->privercyemail."</A></td>\n";
	echo "			</tr>\n";
	echo "			</table>\n";
	echo "			</td>\n";
	echo "		</tr>\n";
	echo "		</table>\n";
	echo "		</td>\n";
	echo "		<td width=\"10\" nowrap></td>\n";
	echo "		<td width=50% align=\"center\" valign=\"top\">";
	echo "		<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\n";
	echo "		<tr>\n";
	echo "			<td>\n";
	if (strlen($crow->mapimage)>0 && file_exists($Dir.DataDir."shopimages/etc/".$crow->mapimage)==true) {
		echo "<img src=\"".$Dir.DataDir."shopimages/etc/".$crow->mapimage."\"";
		$width = getimagesize($Dir.DataDir."shopimages/etc/".$crow->mapimage);
		if ($width[0]>340) echo " width=340 ";
		echo "></td>";
	} else {
		echo "&nbsp</td>";
	}
	echo "		</tr>\n";
	echo "		</table>\n";
	echo "		</td>\n";
	echo "	</tr>\n";
	echo "	</table>\n";
	echo "	</td>\n";
	echo "</tr>\n";
} else if ($crow->introtype=="B") {
	echo "<tr>\n";
	if ($_data->title_type=="Y" && file_exists($Dir.DataDir."design/company_title.gif")) {
		echo "<td><img src=\"".$Dir.DataDir."design/company_title.gif\" border=\"0\" alt=\"회사소개\"></td>\n";
	} else {
		echo "<td>\n";
		/*
		echo "<TABLE WIDTH=100% BORDER=0 CELLPADDING=0 CELLSPACING=0>\n";
		echo "<TR>\n";
		echo "	<TD><IMG SRC=".$Dir."images/".$_data->icon_type."/company_title_head.gif ALT=></TD>\n";
		echo "	<TD width=100% valign=top background=".$Dir."images/".$_data->icon_type."/company_title_bg.gif></TD>\n";
		echo "	<TD width=40><IMG SRC=".$Dir."images/".$_data->icon_type."/company_title_tail.gif ALT=></TD>\n";
		echo "</TR>\n";
		echo "</TABLE>\n";
		*/
		echo "<div class=\"subpageTitle\">회사소개</div>";
		echo "</td>\n";
	}
	echo "</tr>\n";
	if (strlen($crow->mapimage)>0 && $crow->mapalign=="top") {
		echo "<tr>\n";
		echo "	<td align=\"center\" style=\"padding-bottom:20px;\"><img src=\"".$Dir.DataDir."shopimages/etc/".$crow->mapimage."\" border=0></td>\n";
		echo "</tr>\n";
	}
	echo "<tr>\n";
	echo "	<td style=\"padding-left:20px;padding-right:20px;\">";
	echo "	<table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"table-layout:fixed\">\n";
	echo "	<tr valign=\"top\">\n";

	if (strlen($crow->mapimage)>0 && $crow->mapalign=="left") {
		echo "	<td align=\"".$crow->mapalign."\"><img src=\"".$Dir.DataDir."shopimages/etc/".$crow->mapimage."\" border=\"0\"></td>\n";
		echo "	<td style=\"padding-left:10px;\"><pre>".$crow->content."</pre></td>\n";
	} else if (strlen($crow->mapimage)>0 && $crow->mapalign=="right") {
		echo "	<td style=\"padding-right:10px;\"><pre>".$crow->content."</pre></td>\n";
		echo "	<td align=\"".$crow->mapalign."\"><img src=\"".$Dir.DataDir."shopimages/etc/".$crow->mapimage."\" border=\"0\"></td>\n";
	} else {
		echo "	<td><pre>".$crow->content."</pre></td>\n";
	}
	echo "	</tr>\n";
	echo "	</table>\n";
	echo "	</td>\n";
	echo "</tr>\n";
	if (strlen($crow->mapimage)>0 && $crow->mapalign=="bottom") {
		echo "<tr>\n";
		echo "	<td align=\"center\" style=\"padding-top:20px;\"><img src=\"".$Dir.DataDir."shopimages/etc/".$crow->mapimage."\" border=\"0\"></td>\n";
		echo "</tr>\n";
	}
} else if ($crow->introtype=="C") {
	if($crow->mapalign!="top") {
		echo "<tr>\n";
		if ($_data->title_type=="Y" && file_exists($Dir.DataDir."design/company_title.gif")) {
			echo "<td><img src=\"".$Dir.DataDir."design/company_title.gif\" border=\"0\" alt=\"회사소개\"></td>\n";
		} else {
			echo "<td>\n";
			/*
			echo "<TABLE WIDTH=100% BORDER=0 CELLPADDING=0 CELLSPACING=0>\n";
			echo "<TR>\n";
			echo "	<TD><IMG SRC=".$Dir."images/".$_data->icon_type."/company_title_head.gif ALT=></TD>\n";
			echo "	<TD width=100% valign=top background=".$Dir."images/".$_data->icon_type."/company_title_bg.gif></TD>\n";
			echo "	<TD width=40><IMG SRC=".$Dir."images/".$_data->icon_type."/company_title_tail.gif ALT=></TD>\n";
			echo "</TR>\n";
			echo "</TABLE>\n";
			*/
			echo "<div class=\"subpageTitle\">회사소개</div>";
			echo "</td>\n";
		}
		echo "</tr>\n";
	}
	echo "<tr>\n";
	echo "	<td style=\"padding-bottom:20px;\">".$crow->content."</td>\n";
	echo "</tr>\n";
}
?>
<tr><td height="20"></td></tr>
</table>

<? include ($Dir."lib/bottom.php") ?>

</BODY>
</HTML>