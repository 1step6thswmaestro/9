<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

if(strlen($_ShopInfo->getMemid())==0) {
	Header("Location:".$Dir.FrontDir."login.php?chUrl=".getUrl());
	exit;
}

$sql = "SELECT * FROM tblmember WHERE id='".$_ShopInfo->getMemid()."' ";
$result=mysql_query($sql,get_db_conn());
if($row=mysql_fetch_object($result)) {
	if($row->member_out=="Y") {
		$_ShopInfo->SetMemNULL();
		$_ShopInfo->Save();
		echo "<html><head><title></title></head><body onload=\"alert('회원 아이디가 존재하지 않습니다.');location.href='".$Dir.FrontDir."login.php';\"></body></html>";exit;
	}

	if($row->authidkey!=$_ShopInfo->getAuthidkey()) {
		$_ShopInfo->SetMemNULL();
		$_ShopInfo->Save();
		echo "<html><head><title></title></head><body onload=\"alert('처음부터 다시 시작하시기 바랍니다.');location.href='".$Dir.FrontDir."login.php';\"></body></html>";exit;
	}
}
mysql_free_result($result);

function get_totaldays($year,$month) {
	$date = 1;
	while(checkdate($month,$date,$year)) {
		$date++;
	}

	$date--;

	return $date;
}

$s_year=(int)$_POST["s_year"];
$s_month=(int)$_POST["s_month"];
$s_day=(int)$_POST["s_day"];

$e_year=(int)$_POST["e_year"];
$e_month=(int)$_POST["e_month"];
$e_day=(int)$_POST["e_day"];

if($e_year==0) $e_year=(int)date("Y");
if($e_month==0) $e_month=(int)date("m");
if($e_day==0) $e_day=(int)date("d");

$stime=mktime(0,0,0,($e_month-1),$e_day,$e_year);
if($s_year==0) $s_year=(int)date("Y",$stime);
if($s_month==0) $s_month=(int)date("m",$stime);
if($s_day==0) $s_day=(int)date("d",$stime);

$ordgbn=$_POST["ordgbn"];
if(!preg_match("/^(A|S|C|R|P)$/",$ordgbn)) {
	$ordgbn="A";
}


//리스트 세팅
$setup[page_num] = 10;
$setup[list_num] = 10;

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

?>

<HTML>
<HEAD>
<TITLE><?=$_data->shoptitle?> - 조르기내역 조회</TITLE>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META http-equiv="X-UA-Compatible" content="IE=5" />
<META name="description" content="<?=(strlen($_data->shopdescription)>0?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<?include($Dir."lib/style.php")?>
<SCRIPT LANGUAGE="JavaScript">
<!--
var NowYear=parseInt(<?=date('Y')?>);
var NowMonth=parseInt(<?=date('m')?>);
var NowDay=parseInt(<?=date('d')?>);
function getMonthDays(sYear,sMonth) {
	var Months_day = new Array(0,31,28,31,30,31,30,31,31,30,31,30,31)
	var intThisYear = new Number(), intThisMonth = new Number();
	datToday = new Date();													// 현재 날자 설정

	intThisYear = parseInt(sYear);
	intThisMonth = parseInt(sMonth);

	if (intThisYear == 0) intThisYear = datToday.getFullYear();				// 값이 없을 경우
	if (intThisMonth == 0) intThisMonth = parseInt(datToday.getMonth())+1;	// 월 값은 실제값 보다 -1 한 값이 돼돌려 진다.


	if ((intThisYear % 4)==0) {													// 4년마다 1번이면 (사로나누어 떨어지면)
		if ((intThisYear % 100) == 0) {
			if ((intThisYear % 400) == 0) {
				Months_day[2] = 29;
			}
		} else {
			Months_day[2] = 29;
		}
	}
	intLastDay = Months_day[intThisMonth];										// 마지막 일자 구함
	return intLastDay;
}

function ChangeDate(gbn) {
	year=document.form1[gbn+"_year"].value;
	month=document.form1[gbn+"_month"].value;
	totdays=getMonthDays(year,month);

	MakeDaySelect(gbn,1,totdays);
}

function MakeDaySelect(gbn,intday,totdays) {
	document.form1[gbn+"_day"].options.length=totdays;
	for(i=1;i<=totdays;i++) {
		var d = new Option(i);
		document.form1[gbn+"_day"].options[i] = d;
		document.form1[gbn+"_day"].options[i].value = i;
	}
	document.form1[gbn+"_day"].selectedIndex=intday;
}

function GoSearch(gbn) {
	switch(gbn) {
		case "TODAY":
			s_date = new Date(parseInt(NowYear), parseInt(NowMonth), parseInt(NowDay));
			break;
		case "15DAY":
			s_date = new Date(parseInt(NowYear), parseInt(NowMonth), parseInt(NowDay)-15);
			break;
		case "1MONTH":
			s_date = new Date(parseInt(NowYear), parseInt(NowMonth)-1, parseInt(NowDay));
			break;
		case "3MONTH":
			s_date = new Date(parseInt(NowYear), parseInt(NowMonth)-3, parseInt(NowDay));
			break;
		case "6MONTH":
			s_date = new Date(parseInt(NowYear), parseInt(NowMonth)-6, parseInt(NowDay));
			break;
		default :
			s_date = new Date(parseInt(NowYear), parseInt(NowMonth), parseInt(NowDay));
			break;
	}
	e_date = new Date(parseInt(NowYear), parseInt(NowMonth), parseInt(NowDay));
	document.form1.s_year.value=parseInt(s_date.getFullYear());
	document.form1.s_month.value=parseInt(s_date.getMonth());
	document.form1.e_year.value=NowYear;
	document.form1.e_month.value=NowMonth;
	totdays=getMonthDays(parseInt(s_date.getFullYear()),parseInt(s_date.getMonth()));
	MakeDaySelect("s",parseInt(s_date.getDate()),totdays);
	totdays=getMonthDays(NowYear,NowMonth);
	MakeDaySelect("e",NowDay,totdays);
	document.form1.submit();
}

function CheckForm() {
	s_year=document.form1.s_year.value;
	s_month=document.form1.s_month.value;
	s_day=document.form1.s_day.value;
	s_date = new Date(parseInt(s_year), parseInt(s_month), parseInt(s_day));

	e_year=document.form1.e_year.value;
	e_month=document.form1.e_month.value;
	e_day=document.form1.e_day.value;
	e_date = new Date(parseInt(e_year), parseInt(e_month), parseInt(e_day));
	tmp_e_date = new Date(parseInt(e_year), parseInt(e_month)-6, parseInt(e_day));

	if(s_date>e_date) {
		alert("조회 기간이 잘못 설정되었습니다. 기간을 다시 설정해서 조회하시기 바랍니다.");
		return;
	}
	if(s_date<tmp_e_date) {
		alert("조회 기간이 6개월을 넘었습니다. 6개월 이내로 설정해서 조회하시기 바랍니다.");
		return;
	}
	document.form1.submit();
}

function GoOrdGbn(temp) {
	document.form1.ordgbn.value=temp;
	document.form1.submit();
}

function OrderDetailPop(ordercode) {
	document.detailform.ordercode.value=ordercode;
	window.open("about:blank","orderpop","width=610,height=500,scrollbars=yes");
	document.detailform.submit();
}
function DeliSearch(deli_url){
	window.open(deli_url,"배송추적","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizeble=yes,copyhistory=no,width=600,height=550");
}
function DeliveryPop(ordercode) {
	document.deliform.ordercode.value=ordercode;
	window.open("about:blank","delipop","width=600,height=370,scrollbars=no");
	document.deliform.submit();
}

function GoPage(block,gotopage) {
	document.form2.block.value=block;
	document.form2.gotopage.value=gotopage;
	document.form2.submit();
}

//-->
</SCRIPT>
</HEAD>

<body<?=(substr($_data->layoutdata["MOUSEKEY"],0,1)=="Y"?" oncontextmenu=\"return false;\"":"")?><?=(substr($_data->layoutdata["MOUSEKEY"],1,1)=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=(substr($_data->layoutdata["MOUSEKEY"],2,1)=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>

<? include ($Dir.MainDir.$_data->menu_type.".php") ?>

<table border="0" cellpadding="0" cellspacing="0" width="100%">
<?
$leftmenu="Y";
if($_data->design_orderlist=="U") {
	$sql="SELECT body,leftmenu FROM ".$designnewpageTables." WHERE type='orderlist'";
	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		$body=$row->body;
		$body=str_replace("[DIR]",$Dir,$body);
		$leftmenu=$row->leftmenu;
		$newdesign="Y";
	}
	mysql_free_result($result);
}

if ($leftmenu!="N") {
	echo "<tr>\n";
	if ($_data->title_type=="Y" && file_exists($Dir.DataDir."design/orderlist_title.gif")) {
		echo "<td><img src=\"".$Dir.DataDir."design/orderlist_title.gif\" border=\"0\" alt=\"주문내역\"></td>\n";
	} else {
		echo "<td>\n";
		echo "<TABLE WIDTH=100% BORDER=0 CELLPADDING=0 CELLSPACING=0>\n";
		echo "<TR>\n";
		echo "	<TD><IMG SRC=".$Dir."images/".$_data->icon_type."/orderlist_title_head.gif ALT=></TD>\n";
		echo "	<TD width=100% valign=top background=".$Dir."images/".$_data->icon_type."/orderlist_title_bg.gif></TD>\n";
		echo "	<TD width=40><IMG SRC=".$Dir."images/".$_data->icon_type."/orderlist_title_tail.gif ALT=></TD>\n";
		echo "</TR>\n";
		echo "</TABLE>\n";
		echo "</td>\n";
	}
	echo "</tr>\n";
}
//개별 디자인 미적용
$_data->design_orderlist = ($_data->design_orderlist =="U")? "001":$_data->design_orderlist;
echo "<form name=form1 method=post action=\"".$_SERVER[PHP_SELF]."\">\n";
echo "<input type=hidden name=ordgbn value=\"".$ordgbn."\">\n";
echo "<input type=hidden name=type value=\"".$type."\">\n";
echo "<tr>\n";
echo "	<td align=\"center\">\n";
include ($Dir.TempletDir."orderlist/pesterlist".$_data->design_orderlist.".php");
echo "	</td>\n";
echo "</tr>\n";
echo "</form>\n";
?>
<form name=form2 method=post action="<?=$_SERVER[PHP_SELF]?>">
<input type=hidden name=block value="<?=$block?>">
<input type=hidden name=gotopage value="<?=$gotopage?>">
<input type=hidden name=ordgbn value="<?=$ordgbn?>">
<input type=hidden name=s_year value="<?=$s_year?>">
<input type=hidden name=s_month value="<?=$s_month?>">
<input type=hidden name=s_day value="<?=$s_day?>">
<input type=hidden name=e_year value="<?=$e_year?>">
<input type=hidden name=e_month value="<?=$e_month?>">
<input type=hidden name=e_day value="<?=$e_day?>">
</form>

<form name=detailform method=post action="<?=$Dir.FrontDir?>orderdetailpop.php" target="orderpop">
<input type=hidden name=ordercode>
</form>
<form name=deliform method=post action="<?=$Dir.FrontDir?>deliverypop.php" target="delipop">
<input type=hidden name=ordercode>
</form>

</table>

<? include ($Dir."lib/bottom.php") ?>

<?=$onload?>

</BODY>
</HTML>