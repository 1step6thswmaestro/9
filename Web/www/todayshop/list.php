<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

include_once($Dir."lib/ext/func.php");
include_once($Dir."lib/class/pages.php");
$where = array();

array_push($where,"p.productcode like '899%'");
array_push($where,"p.display = 'Y'");

if(!_empty($_ShopInfo->getMemid())){
	array_push($where,"(p.group_check='N' OR b.group_code LIKE '%".$_ShopInfo->getMemgroup()."%')");
}else{
	array_push($where,"p.group_check='N'");
}


switch($_REQUEST['ordby']){
	case 'new':
		$ordby = " order by pridx desc";
		break;
	case 'emd':
		$ordby = " order by end asc, pridx desc";
		break;
	case 'end':
	//	$where = array();
		$ordby = " order by pridx desc";
		break;
	case 'best':
	default:
		$_REQUEST['ordby'] = 'best';
		$ordby = " order by sellcnt desc";
		break;
}

array_push($where,"start <= '".date('Y-m-d H:i')."'");


if($_REQUEST['ordby'] == 'end'){
	array_push($where,"end < '".date('Y-m-d H:i')."'");
}else{
	array_push($where,"end >= '".date('Y-m-d H:i')."'");	
}



$where = _array($where)?' where '.implode(' and ',$where):'';

$sql = "select count(p.pridx) from tblproduct p inner join todaysale t using(pridx)  LEFT OUTER JOIN tblproductgroupcode b ON p.productcode=b.productcode ".$where;


if(false === $res = mysql_query($sql,get_db_conn())) _alert('DB 에러','-1');
$total = mysql_result($res,0,0);


$page = intval($_REQUEST['page'])>0?intval($_REQUEST['page']):1;
$perline = 2;
$totline = 2;
$perpage = $perline * $totline;

$total_page = ceil($total/$perpage);
$page = max(min($page,$total_page),1);

$limit = ' limit '.($page-1)*$perpage.','.$perpage;

//$sql = $tsql= "select p.*,t.start,t.end,t.addquantity,t.salecnt,t.dispmain,unix_timestamp(end) -unix_timestamp() as remain, salecnt+addquantity as sellcnt from tblproduct p inner join todaysale t using(pridx) LEFT OUTER JOIN tblproductgroupcode b ON p.productcode=b.productcode ".$where.$ordby.$limit;
$sql = $tsql= "select p.*,t.start,t.end,t.addquantity,t.salecnt,unix_timestamp(end) -unix_timestamp() as remain, p.sellcount+addquantity as sellcnt from tblproduct p inner join todaysale t using(pridx) LEFT OUTER JOIN tblproductgroupcode b ON p.productcode=b.productcode ".$where.$ordby.$limit;		//2015-04-16 salecnt->p.sellcount


if(false === $res = mysql_query($sql,get_db_conn())) _alert('DB Error1:'.mysql_error(),'-1');
$items = array();
while($row = mysql_fetch_assoc($res)){
	array_push($items,$row);
}

$pages = new pages(array('total_page'=>$total_page,'page'=>$page,'pageblocks'=>10,'links'=>"/todayshop/list.php?ordby=".$_REQUEST['ordby']."&page=%u"));
$pagestr = $pages->_solv()->_result('fulltext'); 
?>
<HTML>
<HEAD>
<TITLE><?=$_data->shopname." [투데이세일]"?></TITLE>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META http-equiv="X-UA-Compatible" content="IE=5" />
<META name="description" content="<?=(strlen($_data->shopdescription)>0?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<script type="text/javascript" src="/js/jquery-1.8.3.min.js"></script>
<script language="javascript" type="text/javascript">
$j = jQuery.noConflict();
</script>
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<script type="text/javascript" src="<?=$Dir?>lib/drag.js.php"></script>
<?include($Dir."lib/style.php")?>
<SCRIPT LANGUAGE="JavaScript">
<!--
function ClipCopy(url) {
	var tmp;
	tmp = window.clipboardData.setData('Text', url);
	if(tmp) {
		alert('주소가 복사되었습니다.');
	}
}

function ChangeSort(val) {
	document.form2.block.value="";
	document.form2.gotopage.value="";
	document.form2.sort.value=val;
	document.form2.submit();
}

function ChangeListnum(val) {
	document.form2.block.value="";
	document.form2.gotopage.value="";
	document.form2.listnum.value=val;
	document.form2.submit();
}

function GoPage(block,gotopage) {
	document.form2.block.value=block;
	document.form2.gotopage.value=gotopage;
	document.form2.submit();
}
//-->
</SCRIPT>
<script language="javascript" type="text/javascript">

function solvCountdown(timestamp){
	timestamp = parseInt(timestamp);
	var d = new Object;
	if(!isNaN(timestamp) && timestamp ){
		d.day = Math.floor(timestamp / (3600 * 24));
		mod = timestamp % (24 * 3600);
		d.hour = Math.floor(mod / 3600);
		mod = mod % 3600;
		d.min = Math.floor(mod / 60);
		d.sec = mod % 60;		
		/*
		if (leftTime == 0){
			//document.getElementById("buyImg_"+k).src =""; //구매종료이미지
		}
		leftTime = leftTime-1;*/
		return d;
	}else{
		return false;
	}
}

function refCountdown(el){
	if($j(el) && $j(el).attr('endstamp')){
		var end = parseInt($j(el).attr('endstamp'));
		var curr = Math.round(new Date().getTime() / 1000);
		if(isNaN(end) || end < curr) remain = 0;
		else remain = end - curr;					
		if(remain < 1){
			$j(el).find('.remainDay').html('0');
			$j(el).find('.remainHour1').html('0');
			$j(el).find('.remainHour2').html('0');
			$j(el).find('.remainMin1').html('0');
			$j(el).find('.remainMin2').html('0');
			$j(el).find('.remainSec1').html('0');
			$j(el).find('.remainSec2').html('0');
		}else{
			d = solvCountdown(remain);
			//$j(el).find('.remainDay').html(d.day+'일');
			$j(el).find('.remainDay').html(d.day);
			$j(el).find('.remainHour1').html(Math.floor(d.hour/10));
			$j(el).find('.remainHour2').html(Math.floor(d.hour %10));
			$j(el).find('.remainMin1').html(Math.floor(d.min/10));
			$j(el).find('.remainMin2').html(Math.floor(d.min%10));
			$j(el).find('.remainSec1').html(Math.floor(d.sec/10));
			$j(el).find('.remainSec2').html(Math.floor(d.sec%10));
		}
	}
}

function intCountdown(){
	$j('td.remainTimeBox').each(function(idx,el){
		refCountdown(el);
	});
	setTimeout("intCountdown()", 1000);
}

$j(function(){
	intCountdown();
});
	
	
</script>
</HEAD>

<body<?=(substr($_data->layoutdata["MOUSEKEY"],0,1)=="Y"?" oncontextmenu=\"return false;\"":"")?><?=(substr($_data->layoutdata["MOUSEKEY"],1,1)=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=(substr($_data->layoutdata["MOUSEKEY"],2,1)=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>

<? 
	//$_data->menu_type = 'nomenu';
	include ($Dir.MainDir.$_data->menu_type.".php");
?>
<!-- skin -->
<table border=0 cellpadding=0 cellspacing=0 width=100%>
	<tr><td><!--<IMG SRC="../images/design/todaysale_title01.gif" ALT=""><IMG SRC="../images/design/gonggu_title01_text.gif" ALT="">--><div class="subpageTitle">투데이세일</div></td></tr>
	<tr><td height="5"></td></tr>
	<tr>
		<td>
<?
	$sql = "select * from tbldesigndefault where type='todaysale' limit 1";
	if(false !== $res = mysql_query($sql,get_db_conn())){
		if(mysql_num_rows($res)){
			$tmp = mysql_fetch_assoc($res);
			//$body = $tmp['body'];
			echo $tmp['body'];
		}
	}

	include dirname(__FILE__).'/skin/list.php';
?>
		</td>
	</tr>
</table>

<form name=form2 method=get action="<?=$_SERVER[PHP_SELF]?>">
	<input type=hidden name=code value="<?=$code?>">
	<input type=hidden name=listnum value="<?=$listnum?>">
	<input type=hidden name=sort value="<?=$sort?>">
	<input type=hidden name=block value="<?=$block?>">
	<input type=hidden name=gotopage value="<?=$gotopage?>">
</form>


<? include ($Dir."lib/bottom.php") ?>

<div id="create_openwin" style="display:none"></div>

</BODY>
</HTML>

<? if($HTML_CACHE_EVENT=="OK") ob_end_flush(); ?>