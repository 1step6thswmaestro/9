<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/ext/func.php");
include_once($Dir."lib/class/pages.php");
include_once($Dir."lib/class/attendance.php");


$attendance = new attendance();
$attendancelist = $attendance->_getList('ables');
$saidx = '';
if($attendancelist['total'] ==1) $saidx = $attendancelist['items'][0]['aidx'];
else if(_isInt($_REQUEST['aidx'])) $saidx =$_REQUEST['aidx'];
else $saidx = $attendancelist['items'][0]['aidx'];

if(_empty($saidx)){
	_alert('출석 체크 이벤트가 할당 되지 않았습니다.','/main/main.php');
	exit;
}elseif(false === $attendance->_set($saidx)){
	_alert($attendance->_msg(),'/main/main.php');
	exit;
}
?>
<HTML>
<HEAD>
	<TITLE>출석 체크 이벤트</TITLE>
	<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
	<META http-equiv="X-UA-Compatible" content="IE=5" />
	<META name="description" content="<?=(strlen($_data->shopdescription)>0?$_data->shopdescription:$_data->shoptitle)?>">
	<META name="keywords" content="<?=$_data->shopkeyword?>">
	<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
	<script type="text/javascript" src="<?=$Dir?>lib/drag.js.php"></script>
	<script language="javascript" type="text/javascript" src="/js/jquery-1.10.2.min.js"></script>
	<script language="javascript" type="text/javascript">
	var $j = jQuery.noConflict();
	</script>
	<? include($Dir."lib/style.php")?>
</HEAD>
<body<?=(substr($_data->layoutdata["MOUSEKEY"],0,1)=="Y"?" oncontextmenu=\"return false;\"":"")?><?=(substr($_data->layoutdata["MOUSEKEY"],1,1)=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=(substr($_data->layoutdata["MOUSEKEY"],2,1)=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>

<? include ($Dir.MainDir.$_data->menu_type.".php"); ?>

<table border=0 cellpadding=0 cellspacing=0 width=100%>
	<tr>
		<td>

			<div class="memberbenefit">
				<h2>MUST HAVE! 쇼핑혜택</h2>
				<div><img src="/images/003/benefit_top.jpg" alt="" /></div>
				<div class="benefitmenu">
					<table border="0" cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td><a href="/front/newpage.php?code=1">회원혜택</a></td>
							<td><a href="/front/newpage.php?code=2">상품평혜택</a></td>
							<td><a href="/front/couponlist.php">쿠폰모음</a></td>
							<td><a href="/front/productgift.php">전용이용권</a></td>
							<td class="nowon"><a href="/front/attendance.php">출석체크</a></td>
							<td><a href="/front/member_urlhongbo.php">홍보적립금혜택</a></td>
							<td><a href="/board/board.php?board=storytalk">스토리톡</a></td>
						</tr>
					</table>
				</div>

				<div class="attendance">
					<h4>출석체크</h4>
					<p>
						- 단일출석 적립금 및 쿠폰과 연속출석 추가적립,쿠폰,사은품 등 다양한 혜택을 누려보세요.<br />
						- 출석부의 등록된 해당날짜의 혜택을 확인하시고 로그인 후 출석체크하세요.<br />
						- 매일 출석하면 하루 50원 적립 최대 1,000원 적립가능, 연속출석시 추가혜택 및 사은품 등의 혜택이 주어집니다.
					</p>
				</div>
			</div>

		</td>
	</tr>
	<tr><td height="20"></td></tr>
	<tr>
		<td style="border:1px solid #ecefed; padding:30px 40px;">
<?
		include $Dir.'templet/attendance/calendar.php';
?>
		</td>
	</tr>
	<tr><td height="40"></td></tr>
</table>

<? include ($Dir."lib/bottom.php") ?>
</BODY>
</HTML>