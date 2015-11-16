<?
if(substr(getenv("SCRIPT_NAME"),-11)=="/bottom.php"){
	header("HTTP/1.0 404 Not Found");
	exit;
}

$companynum="";

$sql = "SELECT * FROM ".$designnewpageTables." WHERE type='bottom' ";
$result = mysql_query($sql,get_db_conn());
if($row = mysql_fetch_object($result)) {
	$type=$row->code;
	$bottom_body=$row->body;
	if($type==3) {	//½Å±Ô ÀÌ¹ÌÁöÇü ±âº» µðÀÚÀÎ
		$bottom_body ="<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
		$bottom_body.="<tr><td height=\"10\"></td></tr>\n";
		$bottom_body.="<tr>\n";
		$bottom_body.="	<td colspan=\"3\" background=\"".$Dir."images/".$_data->icon_type."/main_skin_copyrightbg.gif\">\n";
		$bottom_body.="	<TABLE cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
		$bottom_body.="	<tr>\n";
		$bottom_body.="		<td width=\"100%\"><IMG SRC=\"".$Dir."images/".$_data->icon_type."/main_skin_copyrightimg.gif\" border=\"0\"></td>\n";
		$bottom_body.="		<td>\n";
		$bottom_body.="		<table BORDER=\"0\" cellpadding=\"0\" cellspacing=\"0\">\n";
		$bottom_body.="		<tr>\n";
		$bottom_body.="			<td><a href=[COMPANY]><IMG SRC=\"".$Dir."images/".$_data->icon_type."/main_skin_copyrightm1.gif\" border=\"0\"></a></td>\n";
		$bottom_body.="			<td><a href=[USEINFO]><IMG SRC=\"".$Dir."images/".$_data->icon_type."/main_skin_copyrightm2.gif\" border=\"0\"></a></td>\n";
		$bottom_body.="			<td><a href=[CONTRACT]><IMG SRC=\"".$Dir."images/".$_data->icon_type."/main_skin_copyrightm3.gif\" border=\"0\"></a></td>\n";
		$bottom_body.="			<td><a href=[PRIVERCYVIEW]><IMG SRC=\"".$Dir."images/".$_data->icon_type."/main_skin_copyrightm4.gif\" border=\"0\"></a></td>\n";
		$bottom_body.="			<td><A HREF=[EMAIL]><IMG SRC=\"".$Dir."images/".$_data->icon_type."/main_skin_copyrightm5.gif\" border=\"0\"></a></td>\n";

		if (setUseVender()==true) {
			$bottom_body.="			<td><A HREF='/front/venderProposal.php'><IMG SRC=\"".$Dir."images/".$_data->icon_type."/main_skin_copyrightm6.gif\" border=\"0\"></a></td>\n";
			$bottom_body.="			<td><A HREF=\"/vender/\" target=\"_blank\"><IMG SRC=\"".$Dir."images/".$_data->icon_type."/main_skin_copyrightm7.gif\" border=\"0\" style=\"margin-right:10px;\"></a></td>\n";
		}

		$bottom_body.="			<TD><a href=[HOME]><IMG SRC=\"".$Dir."images/".$_data->icon_type."/main_skin_copyrighthome.gif\" border=\"0\"></a></TD>\n";
		$bottom_body.="			<TD><a href=\"#top\"><IMG SRC=\"".$Dir."images/".$_data->icon_type."/main_skin_copyrighttop.gif\" border=\"0\"></a></TD>\n";
		$bottom_body.="			<TD><a href=\"javascript:history.go(-1);\"><IMG SRC=\"".$Dir."images/".$_data->icon_type."/main_skin_copyrightbac.gif\" border=\"0\"></a></TD>\n";
		$bottom_body.="		</TR>\n";
		$bottom_body.="		</TABLE>\n";
		$bottom_body.="		</td>\n";
		$bottom_body.="		<td><IMG SRC=\"".$Dir."images/".$_data->icon_type."/main_skin_copyrightimg1.gif\" border=\"0\"></td>\n";
		$bottom_body.="	</tr>\n";
		$bottom_body.="	</table>\n";
		$bottom_body.="	</td>\n";
		$bottom_body.="</tr>\n";
		$bottom_body.="<tr><td height=\"10\"></td></tr>\n";
		$bottom_body.="<tr>\n";
		$bottom_body.="	<td width=\"200\" align=\"center\"><a href=\"".$Dir.FrontDir."agreement.php\"><img src=\"".$Dir."images/".$_data->icon_type."/main_skin_copyrightimg2.gif\" align=\"absmiddle\" border=\"0\"></a></td>\n";
		$bottom_body.="	<td width=\"700\" style=\"padding-left:10px;padding-right:10px;\" style=\"font-size:11px;letter-spacing:-0.5pt;line-height:15px;\">\n";
		$bottom_body.="	»óÈ£¸í : [COMPANYNAME] &nbsp; ´ëÇ¥ : [OWNER] &nbsp; »ç¾÷ÀÚµî·Ï¹øÈ£ : [BIZNUM] &nbsp; Åë½ÅÆÇ¸Å¾÷¹øÈ£ : [SALENUM]<br>";
		$bottom_body.="	»ç¾÷Àå¼ÒÀçÁö : [ADDRESS] &nbsp; °í°´¼¾ÅÍ : <img src=\"".$Dir."images/".$_data->icon_type."/main_skin_copyrighttel.gif\" align=\"absmiddle\"> [TEL]<br>\n";
		$bottom_body.="	E-mail : <img src=\"".$Dir."images/".$_data->icon_type."/main_skin_copyrightemail.gif\" align=\"absmiddle\"> <a href=[EMAIL]>[INFOMAIL]</a> &nbsp; [°³ÀÎÁ¤º¸ Ã¥ÀÓÀÚ [PRIVERCY]] &nbsp; [<a href=[CONTRACT]>¾à°ü</a>] &nbsp; [<a href=[PRIVERCYVIEW]>°³ÀÎÁ¤º¸Ãë±Þ¹æÄ§</a>] &nbsp; <a href=[RSS]><img src=\"".$Dir."images/".$_data->icon_type."/main_skin_copyrightrss.gif\" align=\"absmiddle\"><b><font color=\"#FF8730\">RSS</font></b></a><br><b>Copyright ¨Ï <a href=[URL]>[NAME]</a> All Rights Reserved.</b>\n";
		$bottom_body.="	</td>\n";
		$bottom_body.="</tr>\n";
		$bottom_body.="<tr><td colspan=\"3\" height=\"10\"></td></tr>\n";
		$bottom_body.="</table>\n";
		$type=2;
	}else if($type==4){	//¼îÇÎ¸ô ·Î°íÇü ÀÀ¿ë µðÀÚÀÎ
		$bottom_body ="<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
		$bottom_body.="<tr><td height=\"10\"></td></tr>\n";
		$bottom_body.="<tr>\n";
		$bottom_body.="	<td colspan=\"3\" background=\"".$Dir."images/".$_data->icon_type."/main_skin_copyrightbg.gif\">\n";
		$bottom_body.="	<TABLE cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
		$bottom_body.="	<tr>\n";
		$bottom_body.="		<td><IMG SRC=\"".$Dir."images/".$_data->icon_type."/main_skin_copyrightimg.gif\" border=\"0\"></td>\n";
		$bottom_body.="		<td width=\"100%\" align=left>\n";
		$bottom_body.="		<table BORDER=\"0\" cellpadding=\"0\" cellspacing=\"0\">\n";
		$bottom_body.="		<tr>\n";
		$bottom_body.="			<td><a href=[COMPANY]><IMG SRC=\"".$Dir."images/".$_data->icon_type."/main_skin_copyrightm1.gif\" border=\"0\"></a></td>\n";
		$bottom_body.="			<td><a href=[USEINFO]><IMG SRC=\"".$Dir."images/".$_data->icon_type."/main_skin_copyrightm2.gif\" border=\"0\"></a></td>\n";
		$bottom_body.="			<td><a href=[CONTRACT]><IMG SRC=\"".$Dir."images/".$_data->icon_type."/main_skin_copyrightm3.gif\" border=\"0\"></a></td>\n";
		$bottom_body.="			<td><a href=[PRIVERCYVIEW]><IMG SRC=\"".$Dir."images/".$_data->icon_type."/main_skin_copyrightm4.gif\" border=\"0\"></a></td>\n";
		$bottom_body.="			<td><A HREF=[EMAIL]><IMG SRC=\"".$Dir."images/".$_data->icon_type."/main_skin_copyrightm5.gif\" border=\"0\"></a></td>\n";

		if (setUseVender()==true) {
			$bottom_body.="			<td><A HREF='/front/venderProposal.php'><IMG SRC=\"".$Dir."images/".$_data->icon_type."/main_skin_copyrightm6.gif\" border=\"0\"></a></td>\n";
			$bottom_body.="			<td><A HREF=\"/vender/\" target=\"_blank\"><IMG SRC=\"".$Dir."images/".$_data->icon_type."/main_skin_copyrightm7.gif\" border=\"0\" style=\"margin-right:10px;\"></a></td>\n";
		}
		$bottom_body.="		</TR>\n";
		$bottom_body.="		</TABLE>\n";
		$bottom_body.="		</td>\n";
		$bottom_body.="		<TD><a href=[HOME]><IMG SRC=\"".$Dir."images/".$_data->icon_type."/main_skin_copyrighthome.gif\" border=\"0\"></a></TD>\n";
		$bottom_body.="		<TD><a href=\"#top\"><IMG SRC=\"".$Dir."images/".$_data->icon_type."/main_skin_copyrighttop.gif\" border=\"0\"></a></TD>\n";
		$bottom_body.="		<TD><a href=\"javascript:history.go(-1);\"><IMG SRC=\"".$Dir."images/".$_data->icon_type."/main_skin_copyrightbac.gif\" border=\"0\"></a></TD>\n";
		$bottom_body.="		<td><IMG SRC=\"".$Dir."images/".$_data->icon_type."/main_skin_copyrightimg1.gif\" border=\"0\"></td>\n";
		$bottom_body.="	</tr>\n";
		$bottom_body.="	</table>\n";
		$bottom_body.="	</td>\n";
		$bottom_body.="</tr>\n";
		$bottom_body.="<tr><td height=\"10\"></td></tr>\n";
		$bottom_body .="<tr>\n";
		$bottom_body .="	<td>\n";
		$bottom_body .="	<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
		$bottom_body .="	<tr>\n";
		$bottom_body .="		<td width=\"200\">";
		if(file_exists($Dir.DataDir."shopimages/etc/bottom_logo.gif"))
			$bottom_body.="<img src=\"".$Dir.DataDir."shopimages/etc/bottom_logo.gif\" width=\"200\" height=\"74\" align=absmiddle>";
		else
			$bottom_body.="<img src=\"".$Dir."images/common/bottom_nologo.gif\" width=\"200\" height=\"74\" align=absmiddle>";
		$bottom_body .="	</td>\n";
		$bottom_body .="		<td width=\"700\" style=\"padding-left:10px;padding-right:10px;font-size:11px;letter-spacing:-0.5pt;line-height:15px;\"><p>»óÈ£¸í : [COMPANYNAME] &nbsp; ´ëÇ¥ : [OWNER] &nbsp; »ç¾÷ÀÚµî·Ï¹øÈ£ : [BIZNUM] &nbsp; Åë½ÅÆÇ¸Å¹øÈ£ : [SALENUM]<br>";
		$bottom_body .="		»ç¾÷Àå ¼ÒÀçÁö : [ADDRESS] &nbsp; °í°´¼¾ÅÍ : <img src=\"".$Dir."images/".$_data->icon_type."/main_skin_copyrighttel.gif\" align=\"absmiddle\"> [TEL]<br>";
		$bottom_body .="		E-MAIL : <img src=\"".$Dir."images/".$_data->icon_type."/main_skin_copyrightemail.gif\" align=\"absmiddle\"> <a href=[EMAIL]>[INFOMAIL]</a> &nbsp; [°³ÀÎÁ¤º¸Ã¥ÀÓÀÚ : [PRIVERCY]] &nbsp; <a href=[CONTRACT]>[¾à°ü]</a> &nbsp; <a href=[PRIVERCYVIEW]>[°³ÀÎÁ¤º¸Ãë±Þ¹æÄ§]</a> &nbsp; <a href=[RSS]><img src=\"".$Dir."images/".$_data->icon_type."/main_skin_copyrightrss.gif\" align=\"absmiddle\"><b><font color=\"#FF8730\">RSS</font></b></a><br>";
		$bottom_body .="		<b>COPYRIGHT <a href=[URL]>[NAME]</a> ALL RIGHTS RESERVED.</b></td>\n";
		$bottom_body .="	</tr>\n";
		$bottom_body .="	</table>\n";
		$bottom_body .="	</td>\n";
		$bottom_body .="</tr>\n";
		$bottom_body .="<tr>\n";
		$bottom_body .="	<td height=\"10\"></td>\n";
		$bottom_body .="</tr>\n";
		$bottom_body .="</table>\n";
		$type=2;
	}
} else {
	$bottom_body ="<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\n";
	$bottom_body.="<tr>\n";
	$bottom_body.="	<td background=\"".$Dir."images/".$_data->icon_type."/main_skin_copyrightbg.gif\">\n";
	$bottom_body.="	<TABLE border=0 cellpadding=\"0\" cellspacing=\"0\" class=\"menuAndCopyright\">\n";
	$bottom_body.="	<tr>\n";
	$bottom_body.="		<td><IMG SRC=\"".$Dir."images/".$_data->icon_type."/main_skin_copyrightimg.gif\" border=\"0\"></td>\n";
	$bottom_body.="		<td align=\"right\">\n";
	$bottom_body.="			<table BORDER=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"menuLine\">\n";
	$bottom_body.="			<tr>\n";
	$bottom_body.="				<td><a href=[COMPANY]>È¸»ç¼Ò°³</a></td>\n";
	$bottom_body.="				<td><a href=[USEINFO]>ÀÌ¿ë¾È³»</a></td>\n";
	$bottom_body.="				<td><a href=[CONTRACT]>ÀÌ¿ë¾à°ü</a></td>\n";
	$bottom_body.="				<td><a href=[PRIVERCYVIEW]>°³ÀÎÁ¤º¸Ãë±Þ¹æÄ§</a></td>\n";
	$bottom_body.="				<td><A HREF=\"".$Dir.FrontDir."community.php?code=1\">°í°´¼¾ÅÍ</a></td>\n";

	if (setUseVender()==true) {
		$bottom_body.="			<td><A HREF=\"".$Dir.FrontDir."venderProposal.php\">Á¦ÈÞ ¹× ÀÔÁ¡¹®ÀÇ</a></td>\n";
		$bottom_body.="			<td><A HREF=\"/vender/\" target=\"_blank\">ÆÇ¸ÅÀÚ·Î±×ÀÎ</a></td>\n";
	}


	$bottom_body.="				<TD bgcolor=\"#e0e0e0\" style=\"padding-left:15px;\"><a href=[HOME]><IMG SRC=\"".$Dir."images/".$_data->icon_type."/main_skin_copyrighthome.gif\" align=\"absmiddle\" border=\"0\" /> È¨</a></TD>\n";
	$bottom_body.="				<TD bgcolor=\"#e0e0e0\"><a href=\"#top\"><IMG SRC=\"".$Dir."images/".$_data->icon_type."/main_skin_copyrighttop.gif\" align=\"absmiddle\" border=\"0\" /> À§·Î</a></TD>\n";
	$bottom_body.="				<TD bgcolor=\"#e0e0e0\"><a href=\"javascript:history.go(-1);\"><IMG SRC=\"".$Dir."images/".$_data->icon_type."/main_skin_copyrightbac.gif\" align=\"absmiddle\" border=\"0\" /> ÀÌÀü</a></TD>\n";
	$bottom_body.="			</TR>\n";
	$bottom_body.="		</TABLE>\n";
	$bottom_body.="		</td>\n";
	//$bottom_body.="		<td><IMG SRC=\"".$Dir."images/".$_data->icon_type."/main_skin_copyrightimg1.gif\" border=\"0\"></td>\n";
	$bottom_body.="	</tr>\n";
	$bottom_body.="	</table>\n";
	$bottom_body.="	</td>\n";
	$bottom_body.="</tr>\n";
	$bottom_body.="<tr><td height=\"35\"></td></tr>\n";
	$bottom_body.="<tr>\n";
	$bottom_body.="	<td align=\"center\" style=\"font-size:11px; letter-spacing:-0.5pt; line-height:15px;\">\n";
	$bottom_body.="	»óÈ£¸í : [NAME]&nbsp;&nbsp;";
	$bottom_body.="	´ëÇ¥ : [OWNER]&nbsp;&nbsp;";
	$bottom_body.="	»ç¾÷ÀÚµî·Ï¹øÈ£ : [BIZNUM]&nbsp;&nbsp;";
	if (strlen($_data->reportnum)>0) {
		$bottom_body.="	Åë½ÅÆÇ¸Å¾÷½Å°í¹øÈ£ : [SALENUM]";
	}
	$bottom_body.="	<br>»ç¾÷Àå¼ÒÀçÁö : [ADDRESS]";
	$bottom_body.="	<br>°í°´¼¾ÅÍ : [TEL]&nbsp;&nbsp;";
	$bottom_body.="	E-MAIL : <A HREF=[EMAIL]>[INFOMAIL]</A>&nbsp;&nbsp;";
	if (strlen($_data->privercyname)>0) {
		$bottom_body.="	[°³ÀÎÁ¤º¸ Ã¥ÀÓÀÚ : <a href=\"mailto:".$_data->privercyemail."\">[PRIVERCY]</a>]&nbsp;&nbsp;";
		$bottom_body.="	<a href=[CONTRACT]>[¾à°ü]</a>&nbsp;&nbsp;";
		$bottom_body.=" <a href=[PRIVERCYVIEW]>[°³ÀÎÁ¤º¸Ãë±Þ¹æÄ§]</a>&nbsp;&nbsp;";
	} else {
		$bottom_body.="	<a href=[CONTRACT]>[¾à°ü]</a>&nbsp;&nbsp;";
	}
	$bottom_body.="	<a href=[RSS]><b><font color=\"#FF8730\">RSS</font></b></a>&nbsp;&nbsp;";
	$bottom_body.="	<a href=http://www.getmall.co.kr target=_blank>Hosting by <font color=\"#FF8730\"><b>GETMALL</b></font></a>&nbsp;&nbsp;";
	$bottom_body.="	<br><b>Copyright ¨Ï <a href=[URL]>[NAME]</a> All Rights Reserved.</b>";
	$bottom_body.="	</td>\n";
	$bottom_body.="</tr>\n";
	$bottom_body.="</table>\n";
	$type="2";
}



################## °Ô½ÃÆÇ #################
unset($boardval);
for($i=1;$i<=6;$i++) {
	if($num=strpos($bottom_body,"[BOARD".$i)) {
		$boardval[$i]->board_type="Y";
		$boardval[$i]->board_datetype=substr($bottom_body,$num+7,1);
		$boardval[$i]->board_num=(int)substr($bottom_body,$num+8,1);
		$boardval[$i]->board_gan=(int)substr($bottom_body,$num+9,1);
		$boardval[$i]->board_reply=substr($bottom_body,$num+10,1);

		$board_tmp=explode("_",substr($bottom_body,$num+1,strpos($bottom_body,"]",$num)-$num-1));

		$boardval[$i]->board_titlelen=$board_tmp[1];
		$boardval[$i]->board_code=substr($bottom_body,$num+13+strlen($boardval[$i]->board_titlelen),strpos($bottom_body,"]",$num)-$num-13-strlen($boardval[$i]->board_titlelen));
/*
		if($boardval[$i]->board_code!=$_ShopInfo->getDb() && (substr($boardval[$i]->board_code,0,strlen($_ShopInfo->getDb()))!=$_ShopInfo->getDb() || substr($boardval[$i]->board_code,strlen($_ShopInfo->getDb()),1)!="_")) {
			$boardval[$i]->board_code="";
			$boardval[$i]->board_type="";
		}
*/
		$boardval[$i]->board_titlelen=(int)$boardval[$i]->board_titlelen;
		if($boardval[$i]->board_num==0) $boardval[$i]->board_num=5;
		if(strlen($boardval[$i]->board_code)==0) $boardval[$i]->board_type="";
	}
}

$board1=""; $board2=""; $board3=""; $board4=""; $board5=""; $board6="";
for($i=1;$i<=6;$i++) {
	if($boardval[$i]->board_type=="Y") {
		${"board".$i}.="<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
		${"board".$i}.="<tr>\n";
		${"board".$i}.="	<td style=\"padding:5px 0px;\">\n";

		$sql = "SELECT num, title, writetime FROM tblboard WHERE board='".$boardval[$i]->board_code."' ";
		$sql.= "AND deleted!='1' ";
		if($boardval[$i]->board_reply=="N") $sql.= "AND pos=0 ";
		$sql.= "ORDER BY thread ASC LIMIT ".$boardval[$i]->board_num;
		$result=@mysql_query($sql,get_db_conn());
		$j=0;
		while($row=mysql_fetch_object($result)) {
			$j++;
			$date="";
			if($boardval[$i]->board_datetype=="1") {
				$date=date("m-d",$row->writetime);
			} else if($boardval[$i]->board_datetype=="2") {
				$date=date("Y-m-d",$row->writetime);
			}
			${"board".$i}.="<table border=0 cellpadding=0 cellspacing=0 width=\"100%\">\n";
			${"board".$i}.="<tr><td style=\"word-break:break-all;\">";
			${"board".$i}.="<A HREF=\"".$Dir.BoardDir."board.php?pagetype=view&view=1&board=".$boardval[$i]->board_code."&num=".$row->num."\" onmouseover=\"window.status='°Ô½Ã±ÛÇ×Á¶È¸';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"mainboard\">".($boardval[$i]->board_titlelen>0?titleCut($boardval[$i]->board_titlelen,$row->title):$row->title)."</FONT></A>";
			${"board".$i}.="</td><td class=\"mainboard\" align=\"right\">".$date."</td></tr>\n";
			${"board".$i}.="<tr><td height=".$boardval[$i]->board_gan."></td></tr>\n";
			${"board".$i}.="</table>\n";
		}
		mysql_free_result($result);
		if($j==0) {
			${"board".$i}.="<table border=0 cellpadding=0 cellspacing=0>\n";
			${"board".$i}.="<tr><td align=center class=\"mainboard\">µî·ÏµÈ °Ô½Ã±ÛÀÌ ¾ø½À´Ï´Ù.</td></tr>";
			${"board".$i}.="</table>";
		}
		${"board".$i}.="	</td>\n";
		${"board".$i}.="</tr>\n";
		${"board".$i}.="</table>\n";
	}
}

mysql_free_result($result);



$arcompa=array("-"," ",".","_",",");
$arcomre=array("", "", "", "", "");
$companynum=str_replace($arcompa,$arcomre,$_data->companynum);

if(strlen($companynum)==13) {
	$companynum=substr($companynum,0,6)."-*******";
} else {
	$companynum=substr($companynum,0,3)."-".substr($companynum,3,2)."-".substr($companynum,5);
}
$bottom_body=str_replace("[DIR]",$Dir,$bottom_body);

$pattern=array("(\[URL\])","(\[NAME\])","(\[TEL\])","(\[INFOMAIL\])","(\[COMPANYNAME\])","(\[BIZNUM\])","(\[SALENUM\])","(\[OWNER\])","(\[PRIVERCY\])","(\[ADDRESS\])","(\[HOME\])","(\[USEINFO\])","(\[BASKET\])","(\[COMPANY\])","(\[ESTIMATE\])","(\[BOARD\])","(\[AUCTION\])","(\[GONGGU\])","(\[EMAIL\])","(\[RESERVEVIEW\])","(\[LOGIN\])","(\[LOGOUT\])","(\[PRIVERCYVIEW\])","(\[CONTRACT\])","(\[MEMBER\])","(\[MYPAGE\])","(\[ORDER\])","(\[RSS\])","(\[PRODUCTNEW\])","(\[PRODUCTBEST\])","(\[PRODUCTHOT\])","(\[PRODUCTSPECIAL\])","(\[VENDERPROPOSAL\])",
"(\[BOARD1([0-2]{1})([1-9]{1})([0-9]{1})([YN]{1})_([0-9]{0,3})_([_a-zA-Z0-9-]{0,})\])",
"(\[BOARD2([0-2]{1})([1-9]{1})([0-9]{1})([YN]{1})_([0-9]{0,3})_([_a-zA-Z0-9-]{0,})\])",
"(\[BOARD3([0-2]{1})([1-9]{1})([0-9]{1})([YN]{1})_([0-9]{0,3})_([_a-zA-Z0-9-]{0,})\])",
"(\[BOARD4([0-2]{1})([1-9]{1})([0-9]{1})([YN]{1})_([0-9]{0,3})_([_a-zA-Z0-9-]{0,})\])",
"(\[BOARD5([0-2]{1})([1-9]{1})([0-9]{1})([YN]{1})_([0-9]{0,3})_([_a-zA-Z0-9-]{0,})\])",
"(\[BOARD6([0-2]{1})([1-9]{1})([0-9]{1})([YN]{1})_([0-9]{0,3})_([_a-zA-Z0-9-]{0,})\])");

$replacelogin=array("http://".$_ShopInfo->getShopurl()." target=_top",$_data->shopname,$_data->info_tel,$_data->info_email,$_data->companyname,$companynum,$_data->reportnum,$_data->companyowner,"<a href=\"mailto:".$_data->privercyemail."\">".$_data->privercyname."</a>",$_data->info_addr,$Dir.MainDir."main.php",$Dir.FrontDir."useinfo.php",$Dir.FrontDir."basket.php",$Dir.FrontDir."company.php","\"JavaScript:estimate()\"",$Dir.BoardDir."board.php?board=qna",$Dir.AuctionDir."auction.php",$Dir.GongguDir."gonggu.php","\"JavaScript:sendmail()\"",$Dir.FrontDir."mypage_reserve.php","\"JavaScript:alert('·Î±×ÀÎÁßÀÔ´Ï´Ù.');\"",$Dir.MainDir."top.php?type=logout","\"JavaScript:privercy()\"",$Dir.FrontDir."agreement.php",$Dir.FrontDir."mypage_usermodify.php",$Dir.FrontDir."mypage.php",$Dir.FrontDir."mypage_orderlist.php",$Dir.FrontDir."rssinfo.php",$Dir.FrontDir."productnew.php",$Dir.FrontDir."productbest.php",$Dir.FrontDir."producthot.php",$Dir.FrontDir."productspecial.php",$Dir.FrontDir."venderProposal.php",$board1,$board2,$board3,$board4,$board5,$board6,);

$replacelogout=array("http://".$_ShopInfo->getShopurl()." target=_top",$_data->shopname,$_data->info_tel,$_data->info_email,$_data->companyname,$companynum,$_data->reportnum,$_data->companyowner,"<a href=\"mailto:".$_data->privercyemail."\">".$_data->privercyname."</a>",$_data->info_addr,$Dir.MainDir."main.php",$Dir.FrontDir."useinfo.php",$Dir.FrontDir."basket.php",$Dir.FrontDir."company.php","\"JavaScript:estimate()\"",$Dir.BoardDir."board.php?board=qna",$Dir.AuctionDir."auction.php",$Dir.GongguDir."gonggu.php","\"JavaScript:sendmail()\"",$Dir.FrontDir."mypage_reserve.php",$Dir.FrontDir."login.php?chUrl=".(isset($_REQUEST["chUrl"])?$_REQUEST["chUrl"]:""),"\"JavaScript:alert('¸ÕÀú ·Î±×ÀÎÇÏ¼¼¿ä.');\"","\"JavaScript:privercy()\"",$Dir.FrontDir."agreement.php",$Dir.FrontDir."member_agree.php",$Dir.FrontDir."mypage.php",$Dir.FrontDir."mypage_orderlist.php",$Dir.FrontDir."rssinfo.php",$Dir.FrontDir."productnew.php",$Dir.FrontDir."productbest.php",$Dir.FrontDir."producthot.php",$Dir.FrontDir."productspecial.php",$Dir.FrontDir."venderProposal.php",$board1,$board2,$board3,$board4,$board5,$board6,);

array_push($pattern,"(\[TODAYSALE\])");
array_push($replacelogin,$Dir.TodaySaleDir);
array_push($replacelogout,$Dir.TodaySaleDir);

if (strlen($_ShopInfo->getMemid())>0) {
	$bottom_body = preg_replace($pattern,$replacelogin,$bottom_body);
} else {
	$bottom_body = preg_replace($pattern,$replacelogout,$bottom_body);
}

$bottom_body = preg_replace($pattern,$replacelogout,$bottom_body);
?>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>

<?if($type=="1"){?>
	<table border="0" cellpadding="0" cellspacing="0" width="100%">
		<tr><td height="40"></td></tr>
		<tr>
			<td style="background:url('<?=$Dir?>images/<?=$_data->icon_type?>/main_skin_copyrightbg.gif') repeat-x;"><?=$bottom_body?></td>
		</tr>
		<tr><td height="40"></td></tr>
	</table>
<?}?>

<? if($type=="2"){ ?>
	<table border="0" cellpadding="0" cellspacing="0" width="100%" class="bottomMenu">
		<tr><td height="40"></td></tr>
		<tr>
			<td align="center"><?=$bottom_body?></td>
		</tr>
		<tr><td height="40"></td></tr>
	</table>
<? } ?>






<!--
<script src="/upload/js/jquery_mini.js" type="text/javascript"></script>
<script src="/upload/js/jquery.dimensions.js" type="text/javascript"></script>

<!-- ½ºÅ©·Ñ --//>
<script type="text/javascript">
// <![CDATA[
	var name = "#floatMenu";
	var menuYloc = null;

		$(document).ready(function(){
			menuYloc = parseInt($(name).css("top").substring(0,$(name).css("top").indexOf("px")))
			$(window).scroll(function () {
				offset = menuYloc+$(document).scrollTop()+"px";
				$(name).animate({top:offset},{duration:500,queue:false});
				//$(name).css('top',offset);
			});
		});
// ]]>
</script>

<style>
	/* #floatMenu */
	#floatMenu{
		text-align:left;
		position:absolute;
		width:80px;
		top:110px;
		left:50%;
		padding:0;
		margin:0;
		margin-left:470px;
		z-index:1000;
	}

	#floatMenu a{
		text-decoration:none;
		display:block;
	}
</style>

<div id="floatMenu">
	<script language="javascript" src="<?=$Dir.FrontDir?>right_newproduct.php"></script>
</div>
-->

<SCRIPT LANGUAGE="JavaScript">
	<!--
	function RightNewprdtClose() {
		if (isNS4) {
		RightB=document['RightNewprdt'];
		RightB.visibility='hidden';
		} else if (isDOM) {
		RightB = getRightObj('RightNewprdt');
		RightB.style.visibility='hidden';
		}
	}

	var RightAreaAll=new Array();
	function RightArea() {
		var argv = RightArea.arguments;
		var argc = RightArea.arguments.length;

		this.classname		= "RightArea"
		this.debug			= false;
		this.id				= new String((argc > 0) ? argv[0] : "");
		this.x_to			= new String((argc > 1) ? argv[1] : "");
		this.y_to			= new String((argc > 2) ? argv[2] : "");
		this.scroll			= new String((argc > 3) ? argv[3] : "Y");
	}
	//-->
</SCRIPT>

<?
//¿À¸¥ÂÊ ÃÖ±Ù º» »óÇ° ¹× Quick¸Þ´º ½ÃÀÛ
$right_body="";
$isRightBanner=false;
if($_data->quick_type==0) {
	//ÃÖ±Ù º» »óÇ° ÄíÅ°Á¤º¸°¡ ÀÖ´ÂÁö °Ë»ç ÈÄ ÀÖÀ¸¸é ¾Æ·¡ Ã³¸® ($_COOKIE[ViewProduct])
	//if(strlen($_COOKIE[ViewProduct])>0) {
		$isRightBanner=true;
		$sql = "SELECT * FROM ".$designnewpageTables." WHERE type='r_banner' ";
		$result=mysql_query($sql,get_db_conn());
		if($row=mysql_fetch_object($result)) {
			$tmp=explode("",$row->subject);
			$x_to=$tmp[0];	//¿ÞÂÊÀ§Ä¡
			$y_to=$tmp[1];	//À§ÂÊÀ§Ä¡

			$scroll_auto=$row->leftmenu;	//½ºÅ©·Ñ Å¸ÀÔ

			$harf_xto = ($x_to/2)-5;

			//$right_body.="<div id=RightNewprdtBase style=\"position:resolution;\">";
			$right_body.="<div id=RightNewprdtBase style=\"position:absolute;top:0px;left:50%;margin-left:-".$harf_xto."px;\">";
			$right_body.="<div id=RightNewprdt style=\"position:absolute;top:". $y_to."px;left:".$x_to."px;display:hidden;\">\n";
			$right_body.="<script>var right_area=new RightArea();right_area.id='RightNewprdt';right_area.x_to='".$x_to."';right_area.y_to='".$y_to."';right_area.scroll='".$scroll_auto."';RightAreaAll[RightAreaAll.length]=right_area;right_area=null;</script>\n";
			$right_body.="<script language=\"javascript\" src=\"".$Dir.FrontDir."right_newproduct.php\"></script>\n";
			$right_body.="</div>\n";
			$right_body.="</div>\n";
		}
		mysql_free_result($result);
	//}
}

$sql="SELECT * FROM tblquickmenu WHERE used='Y' ";
$result=mysql_query($sql,get_db_conn());
if($row=mysql_fetch_object($result)) {
	$isRightBanner=true;
	$harf_xto = ($row->x_to/2)-5;

	//$right_body.="<div id=RightBanner style=\"position:absolute;display:hidden;\">\n";
	$right_body.="<div id=RightQuickBase style=\"position:absolute;top:0px;left:50%;margin-left:-".$harf_xto."px;\">";
	$right_body.="<div id=\"RightBanner\" style=\"position:absolute;top:".$row->y_to."px;left:".$row->x_to.";\">";
	$right_body.="<script>var right_area=new RightArea(); right_area.id='RightBanner'; right_area.x_to='".$row->x_to."'; right_area.y_to='".$row->y_to."'; right_area.scroll='".$row->scroll_auto."'; RightAreaAll[RightAreaAll.length]=right_area; right_area=null;</script>\n";
	$right_body.="<script language=\"javascript\" src=\"".$Dir.FrontDir."right_quickmenu.php\"></script>\n";
	$right_body.="</div>\n";
	$right_body.="</div>\n";
}
mysql_free_result($result);

if($isRightBanner) {
	$right_body.= "<SCRIPT LANGUAGE=\"JavaScript\">\n";
	$right_body.= "<!--\n";
	$right_body.= "var isDOM = (document.getElementById ? true : false);\n";
	$right_body.= "var isIE4 = ((document.all && !isDOM) ? true : false);\n";
	$right_body.= "var isNS4 = (document.layers ? true : false);\n";
	$right_body.= "var isNS = navigator.appName == 'Netscape';\n";
	$right_body.= "window.onresize = WindowResize;\n";

	$right_body.= "function WindowResize(){\n";
	$right_body.= "	if (isNS4) {\n";
	$right_body.= "		for(i=0;i<RightAreaAll.length;i++) {\n";
	$right_body.= "			RightB = document[RightAreaAll[i].id];\n";
	$right_body.= "			RightB.top = top.pageYOffset + parseInt(RightAreaAll[i].y_to);\n";
	$right_body.= "			RightB.visibility = 'visible';\n";
	$right_body.= "			if(RightAreaAll[i].scroll=='Y') {\n";
	$right_body.= "				MoveRightBanner(i);\n";
	$right_body.= "			}\n";
	$right_body.= "		}\n";
	$right_body.= "	} else if (isDOM) {\n";
	$right_body.= "		for(i=0;i<RightAreaAll.length;i++) {\n";
	$right_body.= "			RightB = getRightObj(RightAreaAll[i].id);\n";
	$right_body.= "			RightB.style.top = (isNS ? window.pageYOffset : document.body.scrollTop) + parseInt(RightAreaAll[i].y_to);\n";
	$right_body.= "			RightB.style.left = (isNS ? window.pageXOffset+100 : document.all.tableposition.offsetLeft) + parseInt(RightAreaAll[i].x_to);\n";
	$right_body.= "			RightB.style.visibility = 'visible';\n";
	$right_body.= "			if(RightAreaAll[i].scroll=='Y') {\n";
	$right_body.= "				MoveRightBanner(i);\n";
	$right_body.= "			}\n";
	$right_body.= "		}\n";
	$right_body.= "	}\n";
	$right_body.= "}\n";

	$right_body.= "function getRightObj(id) {\n";
	$right_body.= "	if (isDOM) return document.getElementById(id);\n";
	$right_body.= "	if (isIE4) return document.all[id];\n";
	$right_body.= "	if (isNS4) return document.layers[id];\n";
	$right_body.= "}\n";

	$right_body.= "function MoveRightBanner(idx) {\n";
	$right_body.= "	var yMenuFrom, yMenuTo, yOffset, timeoutNextCheck;\n";
	$right_body.= "	if (isNS4) {\n";
	$right_body.= "		RightB = document[RightAreaAll[idx].id];\n";
	$right_body.= "		yMenuFrom   = RightB.top;\n";
	$right_body.= "		yMenuTo     = windows.pageYOffset + parseInt(RightAreaAll[idx].y_to);\n";
	$right_body.= "	} else if (isDOM) {\n";
	$right_body.= "		RightB = getRightObj(RightAreaAll[idx].id);\n";
	$right_body.= "		yMenuFrom   = parseInt (RightB.style.top, 10);\n";
	$right_body.= "		yMenuTo     = (isNS ? window.pageYOffset : document.body.scrollTop) + parseInt(RightAreaAll[idx].y_to);\n";
	$right_body.= "	}\n";
	$right_body.= "	timeoutNextCheck = 300;\n";
	$right_body.= "	if (yMenuFrom != yMenuTo) {\n";
	$right_body.= "		yOffset = Math.ceil(Math.abs(yMenuTo - yMenuFrom) / 20);\n";
	$right_body.= "		if (yMenuTo < yMenuFrom) yOffset = -yOffset;\n";
	$right_body.= "		if (isNS4) RightB.top += yOffset;\n";
	$right_body.= "		else if (isDOM) RightB.style.top = parseInt (RightB.style.top, 10) + yOffset;\n";
	$right_body.= "		timeoutNextCheck = 10;\n";
	$right_body.= "	}\n";
	$right_body.= "	setTimeout (\"MoveRightBanner(\"+idx+\")\", timeoutNextCheck);\n";
	$right_body.= "}\n";

	$right_body.= "if (isNS4) {\n";
	$right_body.= "	for(i=0;i<RightAreaAll.length;i++) {\n";
	$right_body.= "		RightB = document[RightAreaAll[i].id];\n";
	$right_body.= "		RightB.top = top.pageYOffset + parseInt(RightAreaAll[i].y_to);\n";
	$right_body.= "		RightB.visibility = 'visible';\n";
	$right_body.= "		if(RightAreaAll[i].scroll=='Y') {\n";
	$right_body.= "			MoveRightBanner(i);\n";
	$right_body.= "		}\n";
	$right_body.= "	}\n";
	$right_body.= "} else if (isDOM) {\n";
	$right_body.= "	for(i=0;i<RightAreaAll.length;i++) {\n";
	$right_body.= "		RightB = getRightObj(RightAreaAll[i].id);\n";
	$right_body.= "		RightB.style.top = (isNS ? window.pageYOffset : document.body.scrollTop) + parseInt(RightAreaAll[i].y_to);\n";
	//$right_body.= "		RightB.style.left = (isNS ? window.pageXOffset+100 : document.all.tableposition.offsetLeft) + parseInt(RightAreaAll[i].x_to);\n";
	$right_body.= "		RightB.style.visibility = 'visible';\n";
	$right_body.= "		if(RightAreaAll[i].scroll=='Y') {\n";
	$right_body.= "			MoveRightBanner(i);\n";
	$right_body.= "		}\n";
	$right_body.= "	}\n";
	$right_body.= "}\n";

	$right_body.= "function RightNewprdtClose() {\n";
	$right_body.= "	if (isNS4) {\n";
	$right_body.= "		RightB=document['RightNewprdt'];\n";
	$right_body.= "		RightB.visibility='hidden';\n";
	$right_body.= "	} else if (isDOM) {\n";
	$right_body.= "		RightB = getRightObj('RightNewprdt');\n";
	$right_body.= "		RightB.style.visibility='hidden';\n";
	$right_body.= "	}\n";
	$right_body.= "}\n";
	$right_body.= "//-->\n";
	$right_body.= "</SCRIPT>\n";
}

echo $right_body;
?>

<?
if($_data->ETCTYPE["BOTTOMTOOLS"]!="Y" && strlen($_vscriptname)>0 && $_vscriptname!=FrontDir."order.php" && $_vscriptname!=FrontDir."orderend.php") {
	$bottomtools_width="100%";
	$bottomtools_height="280";
	$bottomtools_heightclose="29";
	$bottomtools_widthmain=($_data->layoutdata["SHOPWIDTH"]>0?$_data->layoutdata["SHOPWIDTH"]:"980");
	$bottomtools_background = "background:transparent url('".$Dir."images/common/btbackground.gif') repeat-x scroll 0 0;";

	$sql = "SELECT body FROM ".$designnewpageTables." WHERE type='bttoolsetc' ";
	$result = mysql_query($sql,get_db_conn());
	if($row = @mysql_fetch_object($result)) {	// ÇÏ´Ü Æú·Î¸Þ´º ÀüÃ¼ °³º°µðÀÚÀÎ ¼³Á¤ Àû¿ë
		mysql_free_result($result);
		unset($followetcdata);
		if(strlen($row->body)>0) {
			$followetctemp=explode("",$row->body);
			$followetccnt=count($followetctemp);
			if($followetccnt>1) {
				for ($followetci=0;$followetci<$followetccnt;$followetci++) {
					$followetctemp2=explode("=",$followetctemp[$followetci]);
					if(isset($followetctemp2[1])) {
						$followetcdata[$followetctemp2[0]]=$followetctemp2[1];
					} else {
						$followetcdata[$followetctemp2[0]]="";
					}
				}

				if(strlen($followetcdata["BTWIDTH"])>0 && strlen($followetcdata["BTWIDTH"])>0) {
					if(substr($followetcdata["BTWIDTH"],-1)=="%") {
						$bottomtools_width=((int)substr($followetcdata["BTWIDTH"],0,-1)).substr($followetcdata["BTWIDTH"],-1);
					} else {
						$bottomtools_width=(int)$followetcdata["BTWIDTH"];
					}
					$bottomtools_widthmain=(int)$followetcdata["BTWIDTHM"];
					$bottomtools_height=(int)$followetcdata["BTHEIGHT"];
					$bottomtools_heightclose=(int)(int)$followetcdata["BTHEIGHTC"];

					if($followetcdata["BTBGTYPE"]=="B") {
						if(strlen($followetcdata["BTBGCOLOR"])>0) {
							if($followetcdata["BTBGCLEAR"]=="Y") {
								$bottomtools_background = "background-color:transparent;";
							} else {
								$bottomtools_background = "background-color:".(strlen($followetcdata["BTBGCOLOR"])>0?$followetcdata["BTBGCOLOR"]:"#FFFFFF").";";
							}
						} else {
							$bottomtools_background = "background-color:#FFFFFF;";
						}
					} else if($followetcdata["BTBGTYPE"]=="I") {
						if(strlen($followetcdata["BTBGIMAGEREPET"])>0 && strlen($followetcdata["BTBGIMAGELOCAT"])>0 && file_exists($btbackgroundIMG)) {
							$btbackground_repeatarr=array("A"=>"repeat","B"=>"repeat-x","C"=>"repeat-y","D"=>"no-repeat");
							$btbackground_positionarr=array("A"=>"top left","B"=>"top center","C"=>"top right","D"=>"center left","E"=>"center center","F"=>"center right","G"=>"bottom left","H"=>"bottom center","I"=>"bottom right");
							$bottomtools_background = "background:transparent url('".$btbackgroundIMG."') ".$btbackground_repeatarr[$followetcdata["BTBGIMAGEREPET"]]." scroll ".$btbackground_positionarr[$followetcdata["BTBGIMAGELOCAT"]].";";
						} else {
							$bottomtools_background = "background-color:#FFFFFF;";
						}
					} else {
						$bottomtools_background = "";
					}
				}
			}
		}
	}

	function setFontStyle($strtemp) {
		$s_tmpstyle="";
		if(strlen($strtemp)>0) {
			$strtemp_exp = explode("|",$strtemp);
			if(count($strtemp_exp)>0) {
				if(strlen($strtemp_exp[0])>0) {
					$s_tmpstyle="font-size:".$strtemp_exp[0].";";
				}
				if(strlen($strtemp_exp[1])>0) {
					$s_tmpstyle="color:".$strtemp_exp[1].";";
				}
				if(strlen($strtemp_exp[2])>0) {
					if($strtemp_exp[2]=="Y") {
						$s_tmpstyle="font-weight:bold;";
					} else {
						$s_tmpstyle="font-weight:normal;";
					}
				}
				if(strlen($strtemp_exp[3])>0) {
					if($strtemp_exp[3]=="Y") {
						$s_tmpstyle="text-decoration:underline;";
					} else {
						$s_tmpstyle="text-decoration:none;";
					}
				}
			}
		}
		return $s_tmpstyle;
	}
	$followgstyletoday="";
	$followsstyletoday="|#FF3C00|N|N;";
	$followgstylewishlist="";
	$followsstylewishlist="|#FF3C00|N|N";
	$followgstylebasket="";
	$followsstylebasket="|#FF3C00|N|N";
	$followgstylemember="";
	$followsstylemember="|#FF3C00|N|N";
	$followopenimg=$Dir."images/common/btopen.gif";
	$followcloseimg=$Dir."images/common/btclose.gif";
	$sql = "SELECT body FROM ".$designnewpageTables." WHERE type='bttools' ";
	$result = mysql_query($sql,get_db_conn());
	if($row = mysql_fetch_object($result)) {
		mysql_free_result($result);
		if(strlen($row->body)>0) {
			$bttoolsbarok = "Y";
			$bttools_body=str_replace("[DIR]",$Dir,$row->body);
			$num=strpos($bttools_body,"[TODAYCHANGE_");
			if($num!==false) {
				$s_tmp=explode("_",substr($bttools_body,$num+1,strpos($bttools_body,"]",$num)-$num-1));
				$followtodaylink="\"javascript:setFollowSelect('Today');\" id=\"TitleIdToday\" style=\"".setFontStyle($s_tmp[1])."\"";
				$followgstyletoday=$s_tmp[1];
				$followsstyletoday=$s_tmp[2];
			}
			$num=strpos($bttools_body,"[WISHLISTCHANGE_");
			if($num!==false) {
				$s_tmp=explode("_",substr($bttools_body,$num+1,strpos($bttools_body,"]",$num)-$num-1));
				$followwishlistlink="\"javascript:setFollowSelect('Wishlist');\" id=\"TitleIdWishlist\" style=\"".setFontStyle($s_tmp[1])."\"";
				$followgstylewishlist=$s_tmp[1];
				$followsstylewishlist=$s_tmp[2];
			}
			$num=strpos($bttools_body,"[BASKETCHANGE_");
			if($num!==false) {
				$s_tmp=explode("_",substr($bttools_body,$num+1,strpos($bttools_body,"]",$num)-$num-1));
				$followbasketlink="\"javascript:setFollowSelect('Basket');\" id=\"TitleIdBasket\" style=\"".setFontStyle($s_tmp[1])."\"";
				$followgstylebasket=$s_tmp[1];
				$followsstylebasket=$s_tmp[2];
			}
			$num=strpos($bttools_body,"[MEMBERCHANGE_");
			if($num!==false) {
				$s_tmp=explode("_",substr($bttools_body,$num+1,strpos($bttools_body,"]",$num)-$num-1));
				$followmemberlink="\"javascript:setFollowSelect('Member');\" id=\"TitleIdMember\" style=\"".setFontStyle($s_tmp[1])."\"";
				$followgstylemember=$s_tmp[1];
				$followsstylemember=$s_tmp[2];
			}
			$num=strpos($bttools_body,"[OPENCLOSEIMG_");
			if($num!==false) {
				$s_tmp=explode("_",substr($bttools_body,$num+1,strpos($bttools_body,"]",$num)-$num-1));
				$followopenlink="\"javascript:setFollowDivAction();\" id=\"FollowOpenCloseImg\"";
				$followopenimg=$s_tmp[1];
				$followcloseimg=$s_tmp[2];
			}
			$pattern=array("(\[TODAYCHANGE((\_){0,1})([0-9a-zA-Z\|\_\#]){0,}\])","(\[WISHLISTCHANGE((\_){0,1})([0-9a-zA-Z\|\_\#]){0,}\])","(\[BASKETCHANGE((\_){0,1})([0-9a-zA-Z\|\_\#]){0,}\])","(\[MEMBERCHANGE((\_){0,1})([0-9a-zA-Z\|\_\#]){0,}\])","(\[OPENCLOSEIMG([a-zA-Z0-9_?\/\-.]+)\])","(\[OPENCLOSECHANGE\])","(\[TODAYCNT\])","(\[WISHLISTCNT\])","(\[BASKETCNT\])");

			$replace=array($followtodaylink,$followwishlistlink,$followbasketlink,$followmemberlink,"\"".$followopenimg."\"",$followopenlink,"<span id=\"CountIdToday\"> </span>","<span id=\"CountIdWishlist\"> </span>","<span id=\"CountIdBasket\"> </span>");
			$bttools_body = preg_replace($pattern,$replace,$bttools_body);
		}
	}
?>


<?
	//echo $_SERVER['HTTP_USER_AGENT'];
	if( eregi("Trident|MSIE",$_SERVER['HTTP_USER_AGENT']) ) {  // IE¸¸ °¡´É
?>
<div id="DefaultFollowLocat"></div>
<div id="FollowControlBar" style="position:absolute; z-index:10000; top:expression(document.body.scrollTop+document.body.clientHeight-this.clientHeight); left:0px; width:<?=$bottomtools_width?>;<?=$bottomtools_background?>overflow-x:visible;overflow-y:hidden;">
	<div style="left:0px; width:100%; overflow-x:visible; overflow-y:hidden;">
	<div id="FollowControlDiv" style="<?=($_data->align_type=="Y"?"":"left:0px;")?>width:<?=$bottomtools_widthmain?>;height:<?=$bottomtools_height?>;">
	<table border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td id="FollowControlBarTd" height="<?=$bottomtools_heightclose?>">
<?
	if($bttoolsbarok=="Y") {
		echo $bttools_body;
	} else {
?>
		<table border="0" cellpadding="0" cellspacing="0" width="100%">
			<col width="10"></col>
			<col width="120"></col>
			<col width="20"></col>
			<col width=""></col>
			<col width="20"></col>
			<col width="120"></col>
			<tr>
				<td style="background:url('<?=$Dir?>images/common/tab_left.gif') 0 0 no-repeat;"></td>
				<td width="110" style="background:transparent url('<?=$Dir?>images/common/tab_title_bg.gif') repeat-x scroll 0 0;"><img src="<?=$Dir?>images/common/tab_title.gif" border="0" alt="" /></td>
				<td style="background:url('<?=$Dir?>images/common/tab_sort1.gif') 0 0 no-repeat;"></td>
				<td style="background:transparent url('<?=$Dir?>images/common/tab_sort_bg.gif') repeat-x scroll 0 0;" height="100%">
					<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%">
						<col width=""></col>
						<col width="15"></col>
						<col width=""></col>
						<col width="15"></col>
						<col width=""></col>
						<col width="15"></col>
						<col width=""></col>
						<col width="15"></col>
						<col width="84"></col>
						<tr>
							<td style="text-align:center; font-size:11px;"><span id="TitleIdToday" style="cursor:hand;" onclick="setFollowSelect('Today');">ÃÖ±Ù º» »óÇ°<span style="font-size:5px;padding-left:10px;padding-right:11px;">|</span><span id="CountIdToday" style="font-weight:bold;"> </span></span></td>
							<td style="background:url('<?=$Dir?>images/common/tab_sort2.gif') 0 0 no-repeat;"></td>
							<td style="text-align:center; font-size:11px;"><span id="TitleIdWishlist" style="cursor:hand;" onclick="setFollowSelect('Wishlist');">Wishlist<span style="font-size:5px;padding-left:10px;padding-right:11px;">|</span><span id="CountIdWishlist" style="font-weight:bold;"> </span></span></td>
							<td style="background:url('<?=$Dir?>images/common/tab_sort2.gif') 0 0 no-repeat;"></td>
							<td style="text-align:center; font-size:11px;"><span id="TitleIdBasket" style="cursor:hand;" onclick="setFollowSelect('Basket');">Àå¹Ù±¸´Ï<span style="font-size:5px;padding-left:10px;padding-right:11px;">|</span><span id="CountIdBasket" style="font-weight:bold;"> </span></span></td>
							<td style="background:url('<?=$Dir?>images/common/tab_sort2.gif') 0 0 no-repeat;"></td>
							<td style="text-align:center; font-size:11px;"><span id="TitleIdMember" style="cursor:hand;" onclick="setFollowSelect('Member');">È¸¿øÁ¤º¸</span></td>
							<td style="background:url('<?=$Dir?>images/common/tab_sort2.gif') 0 0 no-repeat;"></td>
							<td style="text-align:right; vertical-align:bottom; font-size:11px; background:transparent url('<?=$Dir?>images/common/tab_title_bg.gif') repeat-x scroll 0 0;"><img src="<?=$Dir?>images/common/btopen.gif" id="FollowOpenCloseImg" border="0" style="cursor:hand;" onclick="setFollowDivAction();"></td>
						</tr>
					</table>
				</td>
				<td colspan="2" style="background:url('<?=$Dir?>images/common/tab_sort3.gif') 0 0 no-repeat;"></td>
				<!-- <td style="text-align:center;vertical-align:bottom;padding-bottom:8px;font-size:11px;"><a href="http://www.getmall.co.kr" target="_blank"><img src="<?=$Dir?>images/common/hosting_getmall.gif"></a></td> -->
			</tr>
		</table>
<? } ?>
		</td>
	</tr>
	<tr>
		<td><div id="FollowDivBasket" style="display:none;position:absolute;width:900px;"></div>
		<div id="FollowDivToday" style="display:none;position:absolute;width:900px;"></div>
		<div id="FollowDivWishlist" style="display:none;position:absolute;width:900px;"></div>
		<div id="FollowDivMember" style="display:none;position:absolute;width:900px;"></div></td>
	</tr>
	</table>
	</div>
	</div>
</div>

<script type="text/javascript">
<!--
// ÇÏ´Ü µû¶ó´Ù´Ï´Â ¸Þ´º º¯¼ö ¼ÂÆÃ
var FollowCurrentDiv = "";			// ÇöÀç ¼±ÅÃ ¸Þ´º
var FollowDivArr = new Array("Member","Today","Wishlist","Basket"); // ¸Þ´º, ¸¶Áö¸· ¹è¿­°ªÀº ÃÖÃÊ ¼±ÅÃµÈ °ªÀ¸·Î
var FollowFuncPath="<?=$Dir.FrontDir."follow.func.xml.php"?>"; // Ajax È£­Œ ÆÄÀÏ
var FollowCloseHeight=0;			// Close »óÅÂÀÇ ³ôÀÌ
var FollowOpenHeight=0;				// Open »óÅÂÀÇ ³ôÀÌ
var FollowScrollHeightDefault=0;	// ½ºÅ©·Ñ Ã³¸® ÇÊ¿ä º¯¼ö
var FollowDivTop=0;					// Open, Close ÇÊ¿ä º¯¼ö
var FollowDivOffset=0;				// Open, Close ÇÊ¿ä º¯¼ö
var FollowDivSetTObj;				// setTimeout ¼¼¼Ç º¯¼ö
var FollowSStyleToday="<?=$followsstyletoday?>";			// ÇöÀç ¼±ÅÃ ¸Þ´º ±Û »ö»ó
var FollowGStyleToday="<?=$followgstyletoday?>";			// ¼±ÅÃ ¸Þ´º¸¦ Á¦¿ÜÇÑ ±Û »ö»ó
var FollowSStyleWishlist="<?=$followsstylewishlist?>";		// ÇöÀç ¼±ÅÃ ¸Þ´º ±Û »ö»ó
var FollowGStyleWishlist="<?=$followgstylewishlist?>";		// ¼±ÅÃ ¸Þ´º¸¦ Á¦¿ÜÇÑ ±Û »ö»ó
var FollowSStyleBasket="<?=$followsstylebasket?>";			// ÇöÀç ¼±ÅÃ ¸Þ´º ±Û »ö»ó
var FollowGStyleBasket="<?=$followgstylebasket?>";			// ¼±ÅÃ ¸Þ´º¸¦ Á¦¿ÜÇÑ ±Û »ö»ó
var FollowSStyleMember="<?=$followsstylemember?>";			// ÇöÀç ¼±ÅÃ ¸Þ´º ±Û »ö»ó
var FollowGStyleMember="<?=$followgstylemember?>";			// ¼±ÅÃ ¸Þ´º¸¦ Á¦¿ÜÇÑ ±Û »ö»ó
var FollowOpenImg="<?=$followopenimg?>";	//¿­¸²¹öÆ°ÀÌ¹ÌÁö
var FollowCloseImg="<?=$followcloseimg?>";	//´ÝÈû¹öÆ°ÀÌ¹ÌÁö
var FollowSelectID="";				// ÇöÀç ¼±ÅÃµÈ ¸Þ´º ID
if(typeof(setFollowInit)!="undefined") {
	setFollowInit(FollowDivArr);	// ±âº» ¼ÂÆÃ È£Ãâ
}
//-->
</script>
<?
}

} // IE¸¸ °¡´É
?>

<!-- ¹Ù·Î±¸¸Å ¿É¼Ç -->
<form name="quickfun_setform" id="quickfun_setform">
	<input type=hidden id=quickfun_miniq name=quickfun_miniq value="<?=($miniq>1?$miniq:1)?>">
	<input type=hidden id=quickfun_num name=quickfun_num value="<?=$quickfun_num?>">
	<input type=hidden id=quickfun_dicker name=quickfun_dicker value="<?=(int)@strlen($dicker)?>">
	<input type=hidden id=quickfun_price name=quickfun_price value="<?=$quickfun_price?>">
	<input type=hidden id=quickfun_priceindex name=quickfun_priceindex value="<?=$priceindex?>">
	<input type=hidden id=quickfun_login name=quickfun_login value="<?=$Dir.FrontDir?>login.php?chUrl=">
	<input type=hidden id=quickfun_login2 name=quickfun_login2 value="<?=urlencode("?".getenv("QUERY_STRING"))?>">
</form>