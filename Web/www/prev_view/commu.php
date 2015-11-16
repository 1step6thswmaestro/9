<?
if(strlen($Dir)==0) {
	$Dir="../";
}
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/cache_main.php");

Header("Pragma: no-cache");

include_once($Dir."lib/shopdata.php");



?>
<!-- ShoppingMall Version <?=_IncomuShopVersionNo?>(<?=_IncomuShopVersionDate?>) //-->
<HTML>
<HEAD>
<TITLE><?=$_data->shoptitle?></TITLE>
<link rel="P3Pv1" href="http://<?=$_ShopInfo->getShopurl()?>w3c/p3p.xml">
<link rel="shortcut icon" href="<?=$Dir?>2010/favicon1.ico" >
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META name="description" content="<?=(strlen($_data->shopdescription)>0?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<SCRIPT LANGUAGE="JavaScript">
<!--
<?=$onload?>
//-->
</SCRIPT>
<?include($Dir."lib/style.php")?>
</HEAD>

<body<?=(substr($_data->layoutdata["MOUSEKEY"],0,1)=="Y"?" oncontextmenu=\"return false;\"":"")?><?=(substr($_data->layoutdata["MOUSEKEY"],1,1)=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=(substr($_data->layoutdata["MOUSEKEY"],2,1)=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>

<center><script src="../Scripts/common.js" type="text/javascript"></script>
<script type="text/javascript" src="../Scripts/rolling.js"></script>
<link href="../css/in_style.css" rel="stylesheet" type="text/css" />
<link href="../css/new_style.css" rel="stylesheet" type="text/css" />
<link rel="shortcut icon" href="../2010/favicon1.ico" >

<style type="text/css">
<!--
.style1 {font-family: "돋움체", "돋움";font-size: 12px;}
a {selector-dummy : expression(this.hideFocus=true);}
a:link {color:#909090;text-decoration: none;}
a:visited {color:#909090;text-decoration: none;}	
a:hover {color:#ce0000;text-decoration: none;}
-->
</style>

<table border=0 cellpadding=0 cellspacing=0 width=100%>
<?
$sql = "SELECT * FROM tbldesignnewpage_prev WHERE type='commu' ";
$result=mysql_query($sql,get_db_conn());
if($row=mysql_fetch_object($result)) {
	$isnew=true;
	unset($newobj);
	$newobj->subject=$row->subject;
	$newobj->menu_type=$row->leftmenu;
	$filename=explode("",$row->filename);
	$newobj->member_type=$filename[0];
	$newobj->menu_code=$filename[1];
	$newobj->body=$row->body;
	$newobj->body=str_replace("[DIR]",$Dir,$newobj->body);
	if(strlen($newobj->member_type)>1) {
		$newobj->group_code=$newobj->member_type;
		$newobj->member_type="G";
	}
}
mysql_free_result($result);

for($i=1;$i<=9;$i++) {
	if($num=strpos($newobj->body,"[BOARD".$i)) {
		$boardval[$i]->board_type="Y";
		$boardval[$i]->board_datetype=substr($newobj->body,$num+7,1);
		$boardval[$i]->board_num=(int)substr($newobj->body,$num+8,1);
		$boardval[$i]->board_gan=(int)substr($newobj->body,$num+9,1);
		$boardval[$i]->board_reply=substr($newobj->body,$num+10,1);

		$board_tmp=explode("_",substr($newobj->body,$num+1,strpos($newobj->body,"]",$num)-$num-1));

		$boardval[$i]->board_titlelen=$board_tmp[1];
		$boardval[$i]->board_code=substr($newobj->body,$num+13+strlen($boardval[$i]->board_titlelen),strpos($newobj->body,"]",$num)-$num-13-strlen($boardval[$i]->board_titlelen));

		$boardval[$i]->board_titlelen=(int)$boardval[$i]->board_titlelen;
		if($boardval[$i]->board_num==0) $boardval[$i]->board_num=5;
		if(strlen($boardval[$i]->board_code)==0) $boardval[$i]->board_type="";
	}
}

################## 게시판 #################
$board1=""; $board2=""; $board3=""; $board4=""; $board5=""; $board6=""; $board7=""; $board8=""; $board9="";
for($i=1;$i<=9;$i++) {
	if($boardval[$i]->board_type=="Y") {
		${"board".$i}.="<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
		${"board".$i}.="<tr>\n";
		${"board".$i}.="	<td>\n";

		$sql = "SELECT num, title, writetime, depth FROM tblboard WHERE board='".$boardval[$i]->board_code."' ";
		$sql.= "AND deleted!='1' ";
		if($boardval[$i]->board_reply=="N") $sql.= "AND pos=0 ";
		$sql.= "ORDER BY thread ASC LIMIT ".$boardval[$i]->board_num;
		$result=@mysql_query($sql,get_db_conn());
		$j=0;
		while($row=mysql_fetch_object($result)) {
			$j++;
			$space="";
			if($row->depth>0)
				$space = "<img src=\"/board/images/skin/L03/re_mark.gif\" border=\"0\" align=\"absmiddle\"> ";
			$date="";
			if($boardval[$i]->board_datetype=="1") {
				$date="[".date("m.d",$row->writetime)."] ";
				${"board".$i}.="<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
				${"board".$i}.="<col width=\"32\">\n";
				${"board".$i}.="<col width=\"171\">\n";
				${"board".$i}.="<col width=\"71\">\n";
				${"board".$i}.="<tr>\n";
				${"board".$i}.="	<td height=\"28\" align=\"center\"><img src=\"".$Dir."images/noticedot.gif\" border=\"0\" align=\"absmiddle\"></td>\n";
				${"board".$i}.="	<td><a href=\"".$Dir.BoardDir."board.php?pagetype=view&view=1&board=".$boardval[$i]->board_code."&num=".$row->num."\" onmouseover=\"window.status='게시글항조회';return true;\" onmouseout=\"window.status='';return true;\">".$space.($boardval[$i]->board_titlelen>0?titleCut($boardval[$i]->board_titlelen,$row->title):$row->title)."</a></td>\n";
				${"board".$i}.="	<td align=\"center\">".$date."</td>\n";
				${"board".$i}.="</tr>\n";
				${"board".$i}.="<tr>\n";
				${"board".$i}.="	<td height=\"1\" align=\"center\" background=\"".$Dir."images/newcustom/line_02.gif\"></td>\n";
				${"board".$i}.="	<td background=\"".$Dir."images/newcustom/line_02.gif\"></td>\n";
				${"board".$i}.="	<td align=\"center\" background=\"".$Dir."images/newcustom/line_02.gif\"></td>\n";
				${"board".$i}.="</tr>\n";
				${"board".$i}.="</table>\n";
			} else if($boardval[$i]->board_datetype=="2") {
				$date="".date("Y.m.d",$row->writetime)."";
				${"board".$i}.="<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
				${"board".$i}.="<col width=\"32\">\n";
				${"board".$i}.="<col width=\"171\">\n";
				${"board".$i}.="<col width=\"71\">\n";
				${"board".$i}.="<tr>\n";
				${"board".$i}.="	<td height=\"28\" align=\"center\"><img src=\"".$Dir."images/noticedot.gif\" border=\"0\" align=\"absmiddle\"></td>\n";
				${"board".$i}.="	<td><a href=\"".$Dir.BoardDir."board.php?pagetype=view&view=1&board=".$boardval[$i]->board_code."&num=".$row->num."\" onmouseover=\"window.status='게시글항조회';return true;\" onmouseout=\"window.status='';return true;\">".$space.($boardval[$i]->board_titlelen>0?titleCut($boardval[$i]->board_titlelen,$row->title):$row->title)."</a></td>\n";
				${"board".$i}.="	<td align=\"center\">".$date."</td>\n";
				${"board".$i}.="</tr>\n";
				${"board".$i}.="<tr>\n";
				${"board".$i}.="	<td height=\"1\" align=\"center\" background=\"".$Dir."images/newcustom/line_02.gif\"></td>\n";
				${"board".$i}.="	<td background=\"".$Dir."images/newcustom/line_02.gif\"></td>\n";
				${"board".$i}.="	<td align=\"center\" background=\"".$Dir."images/newcustom/line_02.gif\"></td>\n";
				${"board".$i}.="</tr>\n";
				${"board".$i}.="</table>\n";
			} else {
				${"board".$i}.="<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
				${"board".$i}.="<col width=\"10\">\n";
				${"board".$i}.="<col width=\"\">\n";
				${"board".$i}.="<tr>\n";
				${"board".$i}.="	<td height=\"18\" align=\"center\"><img src=\"".$Dir."images/newcustom/img_bl_01.gif\" width=\"1\" height=\"2\" /></td>\n";
				${"board".$i}.="	<td><span class=\"style2\"><a href=\"".$Dir.BoardDir."board.php?pagetype=view&view=1&board=".$boardval[$i]->board_code."&num=".$row->num."\" onmouseover=\"window.status='게시글항조회';return true;\" onmouseout=\"window.status='';return true;\">".$space.($boardval[$i]->board_titlelen>0?titleCut($boardval[$i]->board_titlelen,$row->title):$row->title)."</a></span></td>\n";
				${"board".$i}.="</tr>\n";
				${"board".$i}.="</table>\n";
			}
		}
		mysql_free_result($result);
		if($j==0) {
			${"board".$i}.="<table border=0 cellpadding=0 cellspacing=0>\n";
			${"board".$i}.="<tr><td align=center class=\"mainboard\">등록된 게시글이 없습니다.</td></tr>";
			${"board".$i}.="</table>";
		}
		${"board".$i}.="	</td>\n";
		${"board".$i}.="</tr>\n";
		${"board".$i}.="</table>\n";
	}
}

$pattern=array(
			"(\[BOARD1([0-2]{1})([1-9]{1})([0-9]{1})([YN]{1})_([0-9]{0,3})_([_a-zA-Z0-9-]{0,})\])",
			"(\[BOARD2([0-2]{1})([1-9]{1})([0-9]{1})([YN]{1})_([0-9]{0,3})_([_a-zA-Z0-9-]{0,})\])",
			"(\[BOARD3([0-2]{1})([1-9]{1})([0-9]{1})([YN]{1})_([0-9]{0,3})_([_a-zA-Z0-9-]{0,})\])",
			"(\[BOARD4([0-2]{1})([1-9]{1})([0-9]{1})([YN]{1})_([0-9]{0,3})_([_a-zA-Z0-9-]{0,})\])",
			"(\[BOARD5([0-2]{1})([1-9]{1})([0-9]{1})([YN]{1})_([0-9]{0,3})_([_a-zA-Z0-9-]{0,})\])",
			"(\[BOARD6([0-2]{1})([1-9]{1})([0-9]{1})([YN]{1})_([0-9]{0,3})_([_a-zA-Z0-9-]{0,})\])",
			"(\[BOARD7([0-2]{1})([1-9]{1})([0-9]{1})([YN]{1})_([0-9]{0,3})_([_a-zA-Z0-9-]{0,})\])",
			"(\[BOARD8([0-2]{1})([1-9]{1})([0-9]{1})([YN]{1})_([0-9]{0,3})_([_a-zA-Z0-9-]{0,})\])",
			"(\[BOARD9([0-2]{1})([1-9]{1})([0-9]{1})([YN]{1})_([0-9]{0,3})_([_a-zA-Z0-9-]{0,})\])"
			);
$replace=array($board1,$board2,$board3,$board4,$board5,$board6,$board7,$board8,$board9);
$newobj->body=preg_replace($pattern,$replace,$newobj->body);

echo $newobj->body;

?>
</table>
	
</BODY>
</HTML>