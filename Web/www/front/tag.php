<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

if($_data->ETCTYPE["TAGTYPE"]=="N") {
	echo "<html></head><body onload=\"alert('현재 페이지는 미사용 중 입니다.');history.go(-1);\"></body></html>";exit;
}
$tagname=$_GET["tagname"];
?>

<HTML>
<HEAD>
<TITLE><?=$_data->shoptitle?> - 태그</TITLE>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META http-equiv="X-UA-Compatible" content="IE=5" />
<META name="description" content="<?=(strlen($_data->shopdescription)>0?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<?include($Dir."lib/style.php")?>
<SCRIPT LANGUAGE="JavaScript">
<!--
var IE = false ;
if (window.navigator.appName.indexOf("Explorer") !=-1) {
	IE = true;
}

//tag 금칙 문자 (%, &, +, <, >, ?, /, \, ', ", =,  \n)
var restrictedTagChars = /[\x25\x26\x2b\x3c\x3e\x3f\x2f\x5c\x27\x22\x3d\x2c\x20]|(\x5c\x6e)/g;
function check_tagvalidate(aEvent, input) {
	var keynum;
	if(typeof aEvent=="undefined") aEvent=window.event;
	if(IE) {
		keynum = aEvent.keyCode;
	} else {
		keynum = aEvent.which;
	}
	//  %, &, +, -, ., /, <, >, ?, \n, \ |
	var ret = input.value;
	if(ret.match(restrictedTagChars) != null ) {
		 ret = ret.replace(restrictedTagChars, "");
		 input.value=ret;
	}
}

var memorytag="";
var memorysort="";
var tagCls = {
		tagList : function(){
			var path="<?=$Dir.FrontDir?>tag.xml.php?sort="+memorysort;
			this.searchGo(path);
		},
		tagSearch : function (tagname){
			memorytag=tagname;
			var path = "<?=$Dir.FrontDir?>tag.xml.php?mode=taglink&tagname="+tagname;
			this.searchGo(path);
		},
		searchProc : function(){
			var tagname = document.all["searchtagname"].value;
			if(tagname.length == 0){
				alert('태그를 입력해 주세요!');
				document.all["searchtagname"].focus();
				return;
			}
			memorytag=tagname;
			var path = "<?=$Dir.FrontDir?>tag.xml.php?mode=search&tagname="+tagname;
			this.searchGo(path);
			return;
		},
		GoPage : function (tagname,sort,block,gotopage){
			var path = "<?=$Dir.FrontDir?>tag.xml.php?mode=search&tagname="+tagname+"&sort="+sort+"&block="+block+"&gotopage="+gotopage;
			this.searchGo(path);
		},
		searchGo : function(path){
			this.openwinClose();
			//로딩중
			//$('tagsearchresult').effect('opacity',{duration:800,transition: fx.sinoidal}).custom(1,0);
			//$('tagsearchresult').setStyle('display','none');
			//move_layer_center(document.all["waiting"], 250,25);

			$('waiting').setStyle('display','block');
			if(!document.all["waiting"].width)
				document.all["waiting"].width = document.all["waiting"].offsetLeft-Math.ceil(document.all["waiting"].offsetWidth/2);

			if(!document.all["waiting"].height)
				document.all["waiting"].height = document.all["waiting"].offsetTop+200;

			document.all["waiting"].style.left = document.all["waiting"].width;
			document.all["waiting"].style.top = document.all["waiting"].height+document.body.scrollTop;

			var myajax = new Ajax(path,
					           {
					 				onComplete: function(text){
					 					var searchTag = new Element('div').setHTML(text);
					 					$('tagsearchresult').setHTML(searchTag.innerHTML);
										$('tagsearchresult').setStyle('display','block');
										//$('tagsearchresult').effect('opacity',{duration:800,transition: fx.sinoidal}).custom(0,1);
										$('waiting').setStyle('display','none');
										document.location.replace("#");
					 				},
					 				evalScripts : true
								}
						).request();
						return;
		},
		changeSort : function(sort){
			this.openwinClose();
			if(memorysort.length>0 && memorysort==sort) {
				return;
			}
			memorysort=sort;
			var path="<?=$Dir.FrontDir?>tag.xml.php?mode=sort&sort="+sort;
			this.searchGo(path);
		},
		splitFunc : function(data){
			if(data.length>0) {
				var splitString = data.split("|");
				if(splitString[1].length>0) {
					alert(splitString[1]);
				}
			}
		},
		openwinClose : function(){
			$('create_openwin').setStyle('display','none');
			$('create_openwin').setHTML("");
		}
}

function GoPage(block,gotopage) {
	tagCls.GoPage(memorytag,"",block,gotopage);
}

function CheckKeyTagSearch() {
	key=event.keyCode;
	if (key==13) {
		tagCls.searchProc();
	}
}

var plusimagepath="<?=$Dir.DataDir?>shopimages/multi/";

//-->
</SCRIPT>
</HEAD>

<body<?=(substr($_data->layoutdata["MOUSEKEY"],0,1)=="Y"?" oncontextmenu=\"return false;\"":"")?><?=(substr($_data->layoutdata["MOUSEKEY"],1,1)=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=(substr($_data->layoutdata["MOUSEKEY"],2,1)=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>

<?
	include ($Dir.MainDir.$_data->menu_type.".php");
?>

<table border=0 cellpadding=0 cellspacing=0 width=100% id="tagTop">
<tr>
	<td align=center>

	<DIV id="waiting" style="position:absolute;display:none">
	<TABLE border=0 cellpadding=0 cellspacing=0 width=250 height=25 bgcolor="#A0A0A0">
	<TR>
		<TD align=center style="color:white; font-size:11px; font-family:Tahoma;"><B>PROCESSING ..... </B></TD>
	</TR>
	</TABLE>
	</DIV>
	<div id="tagsearchresult"></div>
	</td>
</tr>
<tr><td width=100%><img width=100% height=0></td></tr>
</table>

<? include ($Dir."lib/bottom.php") ?>

<div id="create_openwin" style="display:none"></div>

<?if(strlen($tagname)>0){?>
<script>tagCls.tagSearch('<?=$tagname?>');</script>
<?}else{?>
<script>tagCls.tagList();</script>
<?}?>

</BODY>
</HTML>
