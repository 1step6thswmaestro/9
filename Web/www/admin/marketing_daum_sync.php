<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
INCLUDE ("access.php");

unset($row);
$sql = "SELECT * FROM tbldesign ";
$result=mysql_query($sql,get_db_conn());
if($crow=mysql_fetch_object($result)) {
	
} else {
	$crow->introtype="C";
}
mysql_free_result($result);

####################### 페이지 접근권한 check ###############
$PageCode = "st-1";
$MenuCode = "counter";
if (!$_usersession->isAllowedTask($PageCode)) {
	INCLUDE ("AccessDeny.inc.php");
	exit;
}
#########################################################
/*
$regdate=$_shopdata->regdate;

$today = date("Ymd");
$year=date("Y");
$month=date("m");
$day=date("d");
*/

$filename = DirPath.DataDir."shopimages/etc/daum.db";
$daumConf = array();

if(file_exists($filename)){
	if($fp=@fopen($filename, "r")){
		$szdata=fread($fp, filesize($filename));			
		$daumConf=unserialize($szdata);
		fclose($fp);
	}
}

if($_POST['act'] =='updateShopping'){		
	if($fp=@fopen($filename, "w")){
		$daumConf['shopping'] = ($_POST['useDaumSync'] == '1')?'active':'';		
		$daumConf['syncPname'] = trim($_POST['syncPname']);
		fputs($fp, serialize($daumConf));
		fclose($fp);
		echo '<script type="text/javascript">alert("변경되었습니다.");</script>';		
	}	
}

?>

<HTML>
<HEAD>
<TITLE>마케팅 > 네이버지지식쇼핑 > 네이버 지식쇼핑이란?</TITLE>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<script type="text/javascript" src="<?=$Dir?>lib/jquery-1.4.2.min.js"></script>
<?include($Dir."lib/style.php")?>
<script type="text/javascript" src="lib.js.php"></script>
<style type="text/css">
.deTable1 {
	border-top:1px solid #ccc;
	border-left:1px solid #ccc
}
.deTable1 caption {
	padding:5px;
	text-align:left;
	background:#e6e6e6;
}
.deTable1 tbody th {
	font-size:12px;
	background:#eeeeee;
	border-bottom:1px solid #ccc;
	border-right:1px solid #ccc
}
.deTable1 tbody td {
	font-size:12px;
	border-bottom:1px solid #ccc;
	border-right:1px solid #ccc
}
</style>
<script language="javascript"> 
<!--
function winopen_fun(pv)
  {
    if(pv == 1)
      na_open_window('win1', '/front/marketing/mar_daum_02_1_pop1.php', 100, 100, 745, 830, 0, 0, 0, 1, 0);
    else if(pv == 2)
      na_open_window('win1', '/front/marketing/mar_daum_02_1_pop2.php', 100, 100, 745, 830, 0, 0, 0, 1, 0);
    else if(pv == 3)
      na_open_window('win1', '/front/marketing/mar_daum_02_1_pop3.php', 100, 100, 745, 830, 0, 0, 0, 1, 0);
	else if(pv == 4)
      na_open_window('win1', '/front/marketing/mar_daum_02_1_pop4.php', 100, 100, 745, 830, 0, 0, 0, 1, 0);
	else if(pv == 5)
      na_open_window('win1', '/front/marketing/mar_daum_02_1_pop5.php', 100, 100, 745, 830, 0, 0, 0, 1, 0);
	else if(pv == 6)
      na_open_window('win1', '/front/marketing/mar_daum_02_1_pop6.php', 100, 100, 745, 830, 0, 0, 0, 1, 0);
	else if(pv == 7)
      na_open_window('win1', '/front/marketing/mar_daum_02_1_pop7.php', 100, 100, 745, 830, 0, 0, 0, 1, 0);
	else if(pv == 8)
      na_open_window('win1', '/front/marketing/mar_daum_02_1_pop8.php', 100, 100, 745, 830, 0, 0, 0, 1, 0);
	else if(pv == 9)
      na_open_window('win1', '/front/marketing/mar_daum_02_1_pop9.php', 100, 100, 745, 830, 0, 0, 0, 1, 0);
	else if(pv == 10)
      na_open_window('win1', '/front/marketing/mar_daum_02_1_pop10.php', 100, 100, 745, 830, 0, 0, 0, 1, 0);
	else if(pv == 11)
      na_open_window('win1', '/front/marketing/mar_daum_02_1_pop11.php', 100, 100, 745, 830, 0, 0, 0, 1, 0);
  }
  
function marketing_naver(menu) {
	if(menu==1) {
		document.all.marketing_naver1.style.display = 'block';
		document.all.marketing_naver2.style.display = 'none';
	} else if(menu==2) {
		document.all.marketing_naver1.style.display = 'none';
		document.all.marketing_naver2.style.display = 'block';
	} else {
		document.all.marketing_naver1.style.display = 'none';
		document.all.marketing_naver2.style.display = 'none';
	}
}
// -->
<!--
function marketing_1_naver(menu) {
	if(menu==1) {
		document.all.marketing_1_naver1.style.display = 'block';
		document.all.marketing_1_naver2.style.display = 'none';
	} else if(menu==2) {
		document.all.marketing_1_naver1.style.display = 'none';
		document.all.marketing_1_naver2.style.display = 'block';
	} else {
		document.all.marketing_1_naver1.style.display = 'none';
		document.all.marketing_1_naver2.style.display = 'none';
	}
}
// -->



<!--
function na_restore_img_src(name, nsdoc)
{
  var img = eval((navigator.appName.indexOf('Netscape', 0) != -1) ? nsdoc+'.'+name : 'document.all.'+name);
  if (name == '')
    return;
  if (img && img.altsrc) {
    img.src    = img.altsrc;
    img.altsrc = null;
  } 
}
 
function na_preload_img()
{ 
  var img_list = na_preload_img.arguments;
  if (document.preloadlist == null) 
    document.preloadlist = new Array();
  var top = document.preloadlist.length;
  for (var i=0; i < img_list.length; i++) {
    document.preloadlist[top+i]     = new Image;
    document.preloadlist[top+i].src = img_list[i+1];
  } 
}
 
function na_change_img_src(name, nsdoc, rpath, preload)
{ 
  var img = eval((navigator.appName.indexOf('Netscape', 0) != -1) ? nsdoc+'.'+name : 'document.all.'+name);
  if (name == '')
    return;
  if (img) {
    img.altsrc = img.src;
    img.src    = rpath;
  } 
}
 
// -->

<!--
  function winopen_fun(pv)
  {
    if(pv == 1)
      na_open_window('win1', 'http://join.shopping.naver.com/entrance/pop_old_cpc_commission.nhn', 100, 100, 745, 830, 0, 0, 0, 1, 0);
  }
//-->
<!--
 
function na_restore_img_src(name, nsdoc)
{
  var img = eval((navigator.appName == 'Netscape') ? nsdoc+'.'+name : 'document.all.'+name);
  if (name == '')
    return;
  if (img && img.altsrc) {
    img.src    = img.altsrc;
    img.altsrc = null;
  } 
}
 
function na_preload_img()
{ 
  var img_list = na_preload_img.arguments;
  if (document.preloadlist == null) 
    document.preloadlist = new Array();
  var top = document.preloadlist.length;
  for (var i=0; i < img_list.length; i++) {
    document.preloadlist[top+i]     = new Image;
    document.preloadlist[top+i].src = img_list[i+1];
  } 
}
 
function na_change_img_src(name, nsdoc, rpath, preload)
{ 
  var img = eval((navigator.appName == 'Netscape') ? nsdoc+'.'+name : 'document.all.'+name);
  if (name == '')
    return;
  if (img) {
    img.altsrc = img.src;
    img.src    = rpath;
  } 
}
 
function na_open_window(name, url, left, top, width, height, toolbar, menubar, statusbar, scrollbar, resizable)
{
  toolbar_str = toolbar ? 'yes' : 'no';
  menubar_str = menubar ? 'yes' : 'no';
  statusbar_str = statusbar ? 'yes' : 'no';
  scrollbar_str = scrollbar ? 'yes' : 'no';
  resizable_str = resizable ? 'yes' : 'no';
  window.open(url, name, 'left='+left+',top='+top+',width='+width+',height='+height+',toolbar='+toolbar_str+',menubar='+menubar_str+',status='+statusbar_str+',scrollbars='+scrollbar_str+',resizable='+resizable_str);
}
 
//-->
</script>
<script language="javascript" type="text/javascript">
<!--
var $j =jQuery.noConflict();

jQuery(function($j){
	$j(".naver").click(function () {  
		window.open('http://getmall.co.kr/front/marketing_forms/marketing_form.php?code=navershop','popup1','width=820,height=600,scrollbars=yes');
	});
	
	$j('.tab_list2').find('.tab2').click(function(){
		$j(this).parent('td').addClass('active');
		$j(this).parent('td').siblings('td').removeClass('active');
		$jact_num = $j('.tab_list2').find('.tab2').index(this);
		$jact_con = $j('.con_list2').find('.con2').eq($jact_num);
		$jact_con.show();
		$jact_con.siblings('.con2').hide();
		//parent.doResize();
		return false;	
	});
	$j('.tab_list2').find('.tab2').eq(0).click();
});
	//-->
</script>
<style type="text/css">
.tab_list td .on{display:none}
.tab_list td .off{display:block}
.tab_list td.active .on{display:block}
.tab_list td.active .off{display:none}
.con_list .con{display:none}
.tab_list2 td .on{display:none}
.tab_list2 td .off{display:block}
.tab_list2 td.active .on{display:block}
.tab_list2 td.active .off{display:none}
.con_list2 .con2{display:none}
</style>
</HEAD>
<?
if(empty($_REQUEST['step']) || $_REQUEST['step'] < 1 || $_REQUEST['step'] > 4) $_REQUEST['step'] = '1';
?>
<body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0">
<table cellpadding="0" cellspacing="0" width="690">
    <tr>
        <td><IMG src="http://getmall.co.kr/img/marketing/marketing_daum_timg01.jpg" WIDTH=690 HEIGHT=236 ALT=""></td>
    </tr>
    <tr>
        <td height="19"></td>
    </tr>
    <tr>
        <td>
			<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG src="http://getmall.co.kr/img/marketing/marketing_naver_text01.gif" WIDTH=504 HEIGHT=77 ALT=""></TD>
					<TD><IMG src="http://getmall.co.kr/img/marketing/marketing_daum_btn01.gif" WIDTH=186 HEIGHT=77 ALT="" border="0" class="naver" style="cursor:hand"></TD>
				</TR>
			</TABLE>
        </td>
    </tr>
    <tr>
        <td height="60"></td>
    </tr>
    <tr>
        <td>
			<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0 class="tab_list">
				<TR>
				<?
					for($i=1;$i<4;$i++){ ?>					
					<TD><a href="http://getmall.co.kr/front/marketing/marketing_daum.php?step=<?=$i?>&adminUrl=<?=$shopurl?>">
						<IMG src="http://getmall.co.kr/img/marketing/marketing_daum_tap0<?=$i?>.gif" ALT="" border="0"></a></TD>
					<? } ?>					
					<TD id="shopSettingBtn"><a href="/admin/marketing_daum.php?step=4&ext=1" ><IMG src="http://getmall.co.kr/img/marketing/marketing_naver_tap04r.gif" ALT="" border="0"></a></TD>
				</TR>
			</TABLE>
        </td>
    </tr>
    <tr>
        <td height="2" bgcolor="#333333"></td>
    </tr>
    <tr>
        <td height="20"></td>
    </tr>
</table>
<div class="con_list">
<!-- //연동 설정 안내 -->
<table cellpadding="0" cellspacing="0" width="690">
    <tr>
        <td><img src="http://getmall.co.kr/img/marketing/marketing_daum_04_stitle01.gif" width="147" height="50" border="0"></td>
    </tr>
    <tr>
        <td>	
            <form name="setdaumSync" method="post" action="<?=$_SERVER['PHP_SELF']?>" style="margin:0px; padding:0px;">
			<input type="hidden" name="act" value="updateShopping" />
			<input type="hidden" name="step" value="<?=$_REQUEST['step']?>" />
			<input type="hidden" name="ext" value="<?=$_REQUEST['ext']?>">
			<table cellpadding="2" cellspacing="0" width="100%">
                <tr>
                    <td height="40" colspan="2" bgcolor="#F3F5F0" style="font-weight:600; border-top-width:1px; border-bottom-width:1px; border-top-color:rgb(204,205,201); border-bottom-color:rgb(204,205,201); border-top-style:solid; border-bottom-style:solid;" align="center">다음 쇼핑하우 상품DB URL 페이지</td>
                </tr>
				 <tr>
                    <td width="140" height="30" bgcolor="#FBFBF9" style="border-right-width:1px; border-bottom-width:1px; border-right-color:rgb(223,225,222); border-bottom-color:rgb(223,225,222); border-right-style:solid; border-bottom-style:solid;" align="center"><img src="http://getmall.co.kr/img/marketing/marketing_naver_04_t02_a.gif" width="59" height="18" border="0" alt="연동 사용"></td>
                    <td style="padding-left:10px; border-bottom-width:1px; border-bottom-color:rgb(223,225,222); border-bottom-style:solid;">
						<input type="radio" name="useDaumSync" value="1" id="daumSyncUse" <? if(!empty($daumConf['shopping'])) echo 'checked'; ?>/>
						<label for="daumSyncUse">연동 사용함</label>
						<input type="radio" name="useDaumSync" value="0" id="daumSyncNUse" <? if(empty($daumConf['shopping'])) echo 'checked'; ?> />
						<label for="daumSyncNUse">연동 사용하지 않음</label>
						<input type="submit" value="적용" />
					</td>
                </tr>
                <tr>
                    <td width="140" height="30" bgcolor="#FBFBF9" style="border-right-width:1px; border-bottom-width:1px; border-right-color:rgb(223,225,222); border-bottom-color:rgb(223,225,222); border-right-style:solid; border-bottom-style:solid;" align="center"><img src="http://getmall.co.kr/img/marketing/marketing_naver_04_t02.gif" width="59" height="18" border="0"></td>
                    <td style="padding-left:10px; border-bottom-width:1px; border-bottom-color:rgb(223,225,222); border-bottom-style:solid;"> <?=$shopurl?>shopping/daum.shoppinghow.all.php <? if(!empty($daumConf['shopping'])){ ?>
				<a href="/shopping/daum.shoppinghow.all.php" target="_blank" style="margin-left:10px;">[미리보기]</a>
				<? } ?></td>
                </tr>
                <tr>
                    <td width="140" height="30" bgcolor="#FBFBF9" style="border-right-width:1px; border-bottom-width:1px; border-right-color:rgb(223,225,222); border-bottom-color:rgb(223,225,222); border-right-style:solid; border-bottom-style:solid;" align="center"><img src="http://getmall.co.kr/img/marketing/marketing_naver_04_t03.gif" width="59" height="19" border="0"></td>
                    <td style="padding-left:10px; border-bottom-width:1px; border-bottom-color:rgb(223,225,222); border-bottom-style:solid;"><?=$shopurl?>shopping/daum.shoppinghow.simple.php <? if(!empty($daumConf['shopping'])){ ?>
				<a href="/shopping/daum.shoppinghow.simple.php?isdemo=1" target="_blank" style="margin-left:10px;">[미리보기]</a>
				<? } ?></td>
                </tr>
				<tr>
                    <td width="140" height="30" bgcolor="#FBFBF9" style="border-right-width:1px; border-bottom-width:1px; border-right-color:rgb(223,225,222); border-bottom-color:rgb(223,225,222); border-right-style:solid; border-bottom-style:solid;" align="center"><img src="http://getmall.co.kr/img/marketing/marketing_naver_04_t04.gif" border="0" alt="연동상품명"></td>
                    <td style="padding:4px 10px; border-bottom-width:1px; border-bottom-color:rgb(223,225,222); border-bottom-style:solid;">
						<input type="text" name="syncPname" style="width:300px;" class="input" value="<?=$daumConf['syncPname']?>" ><br />
						<ul style="list-style:none; margin-top:10px;">
							<li style="width:100%; font-size:11px;">
								- 쇼핑하우 연동시 검색 최적화 등을 위해 전달되는 상품명을 확장합니다.<br />
								- 비워두시면 쇼핑몰 상품명 그대로 전달됩니다.
							</li>
							<li style="width:100%; font-size:11px;">
								- <span style="color:blue">[BRAND]</span> : 브랜드명<br />
								- <span style="color:blue">[SHOPNAME]</span> : 상점관리에 설정된 상호(회사명)<br />
								- <span style="color:blue">[PNAME]</span> : 상품명
							</li>
							<li style="width:100%; font-size:11px; padding:5px 0px 5px 10px;">
								예) 브랜드가 "겟몰" 이고 상품명이 "넥스트 프라임" 이며 상호가 "오브제" 일 경우<br />
								[<span style="color:blue">[BRAND]</span>] (<span style="color:blue">[SHOPNAME]</span>) - <span style="color:blue">[PNAME]</span> 의 형식으로 입력하면 상품 연동시 전달되는 상품명은<br />
								[<span style="color:blue">겟몰</span>] (<span style="color:blue">오브제</span>) - <span style="color:blue">넥스트프라임</span> 이 됩니다.
							</li>
						</ul>
					</td>
				</td>
                </tr>
            </table>
			</form>
		</td>
	</tr>
	<tr>
		<td height="20"></td>
	</tr>
	<tr>
		<td class="m_table_td1">
			위의 전체/요약상품 URL 정보를 다음 쇼핑하우 커머스원(<a href="http://commerceone.biz.daum.net" target="_blank"><b><font color="#000000">http://commerceone.biz.daum.net</font></b></a>)에 등록하시면 됩니다.<br />
			자세한 등록 방법은 아래의 <b>&ldquo;쇼핑몰 상품 DB URL 등록방법(다음 쇼핑하우)&rdquo;</b>을 참고하세요.</td>
	</tr>

	<tr>
		<td align="right"><a href="http://commerceone.biz.daum.net" target="_blank"><img src="http://getmall.co.kr/img/marketing/marketing_daum_04_btn01.gif" border=0></a></td>
	</tr>

	<tr>
		<td height="40"><IMG border=0 src="http://getmall.co.kr/img/cmn/con_line02.gif" width="100%" height=1></td>
	</tr>

	<tr>
		<td><img src="http://getmall.co.kr/img/marketing/marketing_daum_04_stitle02.gif" border="0" /></td>
	</tr>
	<tr>
		<td><img src="http://getmall.co.kr/img/marketing/marketing_daum_04_img01.jpg" border="0"></td>
	</tr>
	<tr>
		<td><img src="http://getmall.co.kr/img/marketing/marketing_daum_04_img02.jpg" border="0"></td>
	</tr>
	<tr>
		<td height="20"></td>
	</tr>
	<tr>
		<td align="center"><a href="http://commerceone.biz.daum.net" target="_blank"><img src="http://getmall.co.kr/img/marketing/marketing_daum_04_btn01.gif" border=0></a></td>
	</tr>
	<tr>
		<td height="60"></td>
	</tr>

</table>
	<!-- //연동 설정 안내 -->
</div>
</BODY>
</HTML>