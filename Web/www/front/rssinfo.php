<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$leftmenu="Y";
$sql="SELECT body,leftmenu FROM ".$designnewpageTables." WHERE type='rssinfo'";
$result=mysql_query($sql,get_db_conn());
if($row=mysql_fetch_object($result)) {
	$body=$row->body;
	$body=str_replace("[DIR]",$Dir,$body);
	$leftmenu=$row->leftmenu;
	$newdesign="Y";
}
mysql_free_result($result);

?>

<HTML>
<HEAD>
<TITLE><?=$_data->shoptitle?> - RSS</TITLE>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META http-equiv="X-UA-Compatible" content="IE=5" />
<META name="description" content="<?=(strlen($_data->shopdescription)>0?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<script type="text/javascript" src="<?=$Dir?>lib/DropDown.js.php"></script>
<?include($Dir."lib/style.php")?>
<SCRIPT LANGUAGE="JavaScript">
<!--
function FeedCreate() {
	tmpcode="";
	if(typeof(document.form1.codeA)!="undefined" && document.form1.codeA.options[document.form1.codeA.selectedIndex].value.length==3) {
		tmpcode+=document.form1.codeA.options[document.form1.codeA.selectedIndex].value;
	}
	if(typeof(document.form1.codeB)!="undefined" && document.form1.codeB.options[document.form1.codeB.selectedIndex].value.length==3) {
		tmpcode+=document.form1.codeB.options[document.form1.codeB.selectedIndex].value;
	}
	if(typeof(document.form1.codeC)!="undefined" && document.form1.codeC.options[document.form1.codeC.selectedIndex].value.length==3) {
		tmpcode+=document.form1.codeC.options[document.form1.codeC.selectedIndex].value;
	}
	if(typeof(document.form1.codeD)!="undefined" && document.form1.codeD.options[document.form1.codeD.selectedIndex].value.length==3) {
		tmpcode+=document.form1.codeD.options[document.form1.codeD.selectedIndex].value;
	}

	if(tmpcode.length==0 && document.form1.search.value.length==0) {
		alert("분류 선택 또는 검색어를 입력하세요.");
		return;
	}
	rssfeed="http://<?=$_ShopInfo->getShopurl().RssDir?>rss.php?code="+tmpcode+"&sprice="+document.form1.sprice.options[document.form1.sprice.selectedIndex].value+"&search="+document.form1.search.value;
	document.form1.rssfeed.value=rssfeed;
}

function FeedCopy() {
	document.form1.rssfeed.focus();
	document.form1.rssfeed.select();
	document.execCommand("Copy");
	alert("RSS 주소가 클립보드에 복사되었습니다.");
}
//-->
</SCRIPT>
</HEAD>

<body<?=(substr($_data->layoutdata["MOUSEKEY"],0,1)=="Y"?" oncontextmenu=\"return false;\"":"")?><?=(substr($_data->layoutdata["MOUSEKEY"],1,1)=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=(substr($_data->layoutdata["MOUSEKEY"],2,1)=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>

<? include ($Dir.MainDir."nomenu.php") ?>

<table border=0 cellpadding=0 cellspacing=0 width=100%>
<form name=form1 method=post>
<tr>
	<td>
<?
if($newdesign=="Y") {	//개별디자인
	if($num=strpos($body,"[CODEA_")) {
		$s_tmp=explode("_",substr($body,$num+1,strpos($body,"]",$num)-$num-1));
		$codeA_style=$s_tmp[1];
	}
	if($num=strpos($body,"[CODEB_")) {
		$s_tmp=explode("_",substr($body,$num+1,strpos($body,"]",$num)-$num-1));
		$codeB_style=$s_tmp[1];
	}
	if($num=strpos($body,"[CODEC_")) {
		$s_tmp=explode("_",substr($body,$num+1,strpos($body,"]",$num)-$num-1));
		$codeC_style=$s_tmp[1];
	}
	if($num=strpos($body,"[CODED_")) {
		$s_tmp=explode("_",substr($body,$num+1,strpos($body,"]",$num)-$num-1));
		$codeD_style=$s_tmp[1];
	}
	$sprice_style="";
	if($num=strpos($body,"[SPRICE_")) {
		$s_tmp=explode("_",substr($body,$num+1,strpos($body,"]",$num)-$num-1));
		$sprice_style=$s_tmp[1];
	}

	if(strlen($codeA_style)==0) $codeA_style="width:200px";
	if(strlen($codeB_style)==0) $codeB_style="width:200px";
	if(strlen($codeC_style)==0) $codeC_style="width:200px";
	if(strlen($codeD_style)==0) $codeD_style="width:200px";

	if($num=strpos($body,"[KEYWORD_")) {
		$s_tmp=explode("_",substr($body,$num+1,strpos($body,"]",$num)-$num-1));
		$keyword_style=$s_tmp[1];
	}
	if($num=strpos($body,"[RSSFEED_")) {
		$s_tmp=explode("_",substr($body,$num+1,strpos($body,"]",$num)-$num-1));
		$rssfeed_style=$s_tmp[1];
	}
	if(strlen($keyword_style)==0) $keyword_style="width:300px";
	if(strlen($rssfeed_style)==0) $rssfeed_style="width:300px";

	$codeA_select ="<select name=codeA style=\"".$codeA_style."\" onchange=\"SearchChangeCate(this,1)\">\n";
	$codeA_select.="<option value=\"\">--- 1차 카테고리 선택 ---</option>\n";
	$codeA_select.="</select>\n";

	$codeB_select ="<select name=codeB style=\"".$codeB_style."\" onchange=\"SearchChangeCate(this,2)\">\n";
	$codeB_select.="<option value=\"\">--- 2차 카테고리 선택 ---</option>\n";
	$codeB_select.="</select>\n";

	$codeC_select ="<select name=codeC style=\"".$codeC_style."\" onchange=\"SearchChangeCate(this,3)\">\n";
	$codeC_select.="<option value=\"\">--- 3차 카테고리 선택 ---</option>\n";
	$codeC_select.="</select>\n";

	$codeD_select ="<select name=codeD style=\"".$codeD_style."\">\n";
	$codeD_select.="<option value=\"\">--- 4차 카테고리 선택 ---</option>\n";
	$codeD_select.="</select>\n";

	$txt_keyword = "<input type=text name=search style=\"".$keyword_style."\">";

	$sprice_select = "<select name=sprice";
	if(strlen($sprice_style)>0) $sprice_select.= " style=\"".$sprice_style."\"";
	$sprice_select.= ">\n";
	$sprice_select.= "<option value=\"\">전체</option>\n";
	$sprice_select.= "<option value=\"20000\">2만원 이하</option>\n";
	$sprice_select.= "<option value=\"50000\">2~5만원</option>\n";
	$sprice_select.= "<option value=\"100000\">5~10만원</option>\n";
	$sprice_select.= "<option value=\"300000\">10~30만원</option>\n";
	$sprice_select.= "<option value=\"300001\">30만원 이상</option>\n";
	$sprice_select.= "</select>\n";

	$txt_rssfeed = "<input type=text name=rssfeed style=\"".$rssfeed_style."\">";

	$pattern=array(
		"(\[CODEA((\_){0,1})([0-9a-zA-Z\.\-\:\;\%\#\ ]){0,}\])",
		"(\[CODEB((\_){0,1})([0-9a-zA-Z\.\-\:\;\%\#\ ]){0,}\])",
		"(\[CODEC((\_){0,1})([0-9a-zA-Z\.\-\:\;\%\#\ ]){0,}\])",
		"(\[CODED((\_){0,1})([0-9a-zA-Z\.\-\:\;\%\#\ ]){0,}\])",
		"(\[KEYWORD((\_){0,1})([0-9a-zA-Z\.\-\:\;\%\#\ ]){0,}\])",
		"(\[SPRICE((\_){0,1})([0-9a-zA-Z\.\-\:\;\%\#\ ]){0,}\])",
		"(\[RSSFEED((\_){0,1})([0-9a-zA-Z\.\-\:\;\%\#\ ]){0,}\])",
		"(\[FEEDCREATE\])",
		"(\[FEEDCOPY\])"
	);

	$replace=array($codeA_select,$codeB_select,$codeC_select,$codeD_select,$txt_keyword,$sprice_select,$txt_rssfeed,"javascript:FeedCreate()","javascript:FeedCopy()");
	$body = preg_replace($pattern,$replace,$body);
	echo $body;

} else {
?>
<table align="center" cellpadding="0" cellspacing="0" width="100%">
<tr>
	<td><img src="<?=$Dir?>images/common/rss_text01.gif" border="0"></td>
</tr>
<tr>
	<td height="10"></td>
</tr>
<tr>
	<td><img src="<?=$Dir?>images/common/rss_text01_a.gif" border="0"><!--<b>본 쇼핑몰에서 제공하는 <font color="#0000FF" size="3">RSS</font> 는 <font color="#FF7A00" size="3">XML기반의 컨텐츠 교환 프로토콜로서</font></b><br>
	<font color="#33B1CC"><b>업데이트된 쇼핑컨텐츠 정보를 사용자들에게 보다 쉽게 제공하기 위한 XML 형식의 데이터 입니다.</b></font>--></td>
</tr>
<tr>
	<td height="20"></td>
</tr>
<tr>
	<td>
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td><img src="<?=$Dir?>images/common/rss_table01.gif" border="0"></td>
		<td width="100%" background="<?=$Dir?>images/common/rss_table01bg.gif"></td>
		<td><img src="<?=$Dir?>images/common/rss_table02.gif" border="0"></td>
	</tr>
	<tr>
		<td background="<?=$Dir?>images/common/rss_table04bg.gif"></td>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%">

		<tr>
			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0">
				<tr>
					<td><img src="<?=$Dir?>images/common/rss_table01a.gif" border="0"></td>
					<td width="100%" background="<?=$Dir?>images/common/rss_table01abg.gif"></td>
					<td><img src="<?=$Dir?>images/common/rss_table02a.gif"" border="0"></td>
				</tr>
				<tr>
					<td background="<?=$Dir?>images/common/rss_table04abg.gif"></td>
					<td  bgcolor="#FAFAFA" align="center">





					<TABLE cellSpacing="0" cellPadding="0" border="0">
					<TR>
						<TD style="padding:7px,10px,0,0" vAlign="top" align="right"><B>분류선택</B></TD>
						<TD style="PADDING-TOP: 7px">
						<TABLE cellSpacing="0" cellPadding="0" border="0">
						<TR>
							<TD><select name="codeA" style="width:300px" onchange="SearchChangeCate(this,1);">
							<option value="">--- 1차 카테고리 선택 ---</option>
							</SELECT></TD>
						</TR>
						<TR>
							<TD height="5"></TD>
						</tr>
						<TR>
							<TD><select name="codeB" style="width:300px" onchange="SearchChangeCate(this,2);">
							<option value="">--- 2차 카테고리 선택 ---</option>
							</SELECT></TD>
						</TR>
						<TR>
							<TD height="5"></TD>
						</tr>
						<TR>
							<TD><select name="codeC" style="width:300px" onchange="SearchChangeCate(this,3);">
							<option value="">--- 3차 카테고리 선택 ---</option>
							</SELECT></TD>
						</TR>
						<TR>
							<TD height="5"></TD>
						</tr>
						<TR>
							<TD><select name="codeD" style="width:300px">
							<option value="">--- 4차 카테고리 선택 ---</option>
							</SELECT></TD>
						</TR>
						<TR>
							<TD height="20"></TD>
						</TR>
						</TABLE>

						</TD>
					</TR>
					<TR>
						<TD style="PADDING-RIGHT:10px" align="right"><B>검색어</B></TD>
						<TD><INPUT type=text name="search" size="60"></TD>
					</TR>
					<TR>
						<TD height="20" colspan="2"></TD>
					</TR>
					<TR>
						<TD style="PADDING-RIGHT:10px" align="right"><B>가격대</B></TD>
						<TD><select name="sprice">
						<option value="">전체</option>
						<option value="20000">2만원 이하</option>
						<option value="50000">2~5만원</option>
						<option value="100000">5~10만원</option>
						<option value="300000">10~30만원</option>
						<option value="300001">30만원 이상</option>
						</SELECT></TD>
					</TR>
					<TR>
						<TD></TD>
						<TD style="padding-top:2px"><img src="<?=$Dir?>images/common/rss_btn02.gif" border="0" style="cursor:hand" onclick="FeedCreate();"></TD>
					</TR>
					<TR>
						<TD height="20" colspan="2"></TD>
					</TR>
					<TR>
						<TD style="PADDING-RIGHT:10px" align="right"><B>RSS 주소</B></TD>
						<TD><INPUT type=text name="rssfeed" size="60"></TD>
					</TR>
					<TR>
						<TD></TD>
						<TD style="padding-top:2px"><img src="<?=$Dir?>images/common/rss_btn03.gif" border="0" style="cursor:hand" onclick="FeedCopy();"></TD>
					</TR>
					</TABLE>
					</td>
					<td background="<?=$Dir?>images/common/rss_table02abg.gif"></td>
				</tr>
				<tr>
					<td><img src="<?=$Dir?>images/common/rss_table04a.gif" border="0"></td>
					<td width="100%" background="<?=$Dir?>images/common/rss_table03abg.gif"></td>
					<td><img src="<?=$Dir?>images/common/rss_table03a.gif" border="0"></td>
				</tr>
				</table>













				</td>
			</tr>
			<tr>
				<td height="20"></td>
			</tr>
			<tr>
				<td style="padding-left:20px;">
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td><img src="<?=$Dir?>images/common/rss_text04.gif" border="0" vspace="4"></td>
				</tr>
				<tr>
					<td style="padding-left:35px;letter-spacing:-0.5pt;">RSS는 'RDF Site Summary' 또는 'Really Simple Syndication', 'Rich Site Summary' 등의 약자입니다.<br>
					뉴스나 블로그와 같이 컨텐츠 업데이트가 자주 일어나는 웹사이트에서, <b><u>업데이트된 정보를 자동적으로 쉽게<br>
					사용자들에게 제공하기 위한 서비스</u></b>입니다.</td>
				</tr>
				<tr>
					<td height="20"></td>
				</tr>
				<tr>
					<td><img src="<?=$Dir?>images/common/rss_text05.gif" border="0" vspace="4"></td>
				</tr>
				<tr>
					<td style="padding-left:35px;letter-spacing:-0.5pt;">쇼핑몰에서 제공하는 RSS 주소를 리더기 등록하기만 하면, 관심있는 카테고리 및 상품의 검색결과를<br>
					<b>쇼핑몰에 매번 방문할 필요 없이실시간으로 받아 보실 수 있습니다.</td></td>
				</tr>
				<tr>
					<td height="20"></td>
				</tr>
				<tr>
					<td><img src="<?=$Dir?>images/common/rss_text06.gif" border="0" vspace="4"></td>
				</tr>
				<tr>
					<td style="padding-left:35px;letter-spacing:-0.5pt;">1) 자신에게 맞는 RSS Reader 프로그램을 설치합니다.<br>
					2) 업데이트된 정보를 얻고 싶은 상품분류/키워드를 입력 후 "RSS 주소생성"을 합니다.<br>
					&nbsp;&nbsp;&nbsp;&nbsp;생성된 RSS주소는 "RSS 주소복사"를 클릭하시면 복사됩니다.<br>
					3) RSS Reader 프로그램에 복사된 주소를 입력합니다.<br>
					4) 이제부터 RSS Reader 프로그램을 통해 새로 들어오는 실시간 상품정보를 자동적으로 받아보실 수 있습니다.</td>
				</tr>
				<tr>
					<td style="padding-left:35px;"><img src="<?=$Dir?>images/common/rss_img04.gif" border="0" vspace="10"></td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td height="20"></td>
			</tr>
			</table>
			</td>
			<td></td>
			<td valign="top" style="padding-left:10px">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td><img src="<?=$Dir?>images/common/rss_text02.gif" border="0"></td>
			</tr>
			<tr>
				<td><a href="http://www.hanrss.com" target="_blank"><img src="<?=$Dir?>images/common/rss_btn05.gif" border="0" vspace="3"></a></td>
			</tr>
			<tr>
				<td height="5"></td>
			</tr>
			<tr>
				<td><span style="font-size:9pt;"><b><font color="#999900">한RSS공식 사이트</font></b><br>
				<a href="http://www.hanrss.com" target="_blank">http://www.hanrss.com</a></span></td>
			</tr>
			<tr>
				<td height="10"></td>
			</tr>
			<tr>
				<td><span style="font-size:9pt;"><b><font color="black">그 외 리더기</font></b><br>
				- KlipFolio<br>
				- Xpyder<br>
				- SAGE(Mozilla 브라우저용)</span></td>
			</tr>
			<tr>
				<td height="10"></td>
			</tr>
			<tr>
				<td><img src="<?=$Dir?>images/common/rss_text03.gif" border="0"></td>
			</tr>
			</table>
			</td>
		</tr>
		</table>
		</td>
		<td background="<?=$Dir?>images/common/rss_table02bg.gif"></td>
	</tr>
	<tr>
		<td><img src="<?=$Dir?>images/common/rss_table04.gif" border="0"></td>
		<td width="100%" background="<?=$Dir?>images/common/rss_table03bg.gif"></td>
		<td><img src="<?=$Dir?>images/common/rss_table03.gif" border="0"></td>
	</tr>
	</table>
	</td>
</tr>
</table>
<?
}

$sql = "SELECT * FROM tblproductcode ";
if(strlen($_ShopInfo->getMemid())==0) {
	$sql.= "WHERE group_code='' ";
} else {
	$sql.= "WHERE (group_code='' OR group_code='ALL' OR group_code='".$_ShopInfo->getMemgroup()."') ";
}
$sql.= "AND (type!='T' AND type!='TX' AND type!='TM' AND type!='TMX') ORDER BY sequence DESC ";
$i=0;
$ii=0;
$iii=0;
$iiii=0;
$strcodelist = "";
$strcodelist.= "<script>\n";
$result = mysql_query($sql,get_db_conn());
$selcode_name="";
while($row=mysql_fetch_object($result)) {
	$strcodelist.= "var clist=new CodeList();\n";
	$strcodelist.= "clist.codeA='".$row->codeA."';\n";
	$strcodelist.= "clist.codeB='".$row->codeB."';\n";
	$strcodelist.= "clist.codeC='".$row->codeC."';\n";
	$strcodelist.= "clist.codeD='".$row->codeD."';\n";
	$strcodelist.= "clist.type='".$row->type."';\n";
	$strcodelist.= "clist.code_name='".$row->code_name."';\n";
	if($row->type=="L" || $row->type=="T" || $row->type=="LX" || $row->type=="TX") {
		$strcodelist.= "lista[".$i."]=clist;\n";
		$i++;
	}
	if($row->type=="LM" || $row->type=="TM" || $row->type=="LMX" || $row->type=="TMX") {
		if ($row->codeC=="000" && $row->codeD=="000") {
			$strcodelist.= "listb[".$ii."]=clist;\n";
			$ii++;
		} else if ($row->codeD=="000") {
			$strcodelist.= "listc[".$iii."]=clist;\n";
			$iii++;
		} else if ($row->codeD!="000") {
			$strcodelist.= "listd[".$iiii."]=clist;\n";
			$iiii++;
		}
	}
	$strcodelist.= "clist=null;\n\n";
}
mysql_free_result($result);
$strcodelist.= "CodeInit();\n";
$strcodelist.= "</script>\n";

echo $strcodelist;

echo $prlistscript;

echo "<script>SearchCodeInit('".$codeA."','".$codeB."','".$codeC."','".$codeD."');</script>";
?>
	</td>
</tr>
</form>
</table>

<? include ($Dir."lib/bottom.php") ?>

</BODY>
</HTML>