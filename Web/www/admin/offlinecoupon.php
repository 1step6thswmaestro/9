<?

$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/class/coupon.php");
include_once($Dir."lib/class/pages.php");
$coupon = new coupon();
include ("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "st-1";
$MenuCode = "counter";

if (!$_usersession->isAllowedTask($PageCode)) {
	include ("AccessDeny.inc.php");
	exit;
}



function _back($msg){
echo '<script language="javascript" type="text/javascript">
		alert("'.str_replace('"',"'",$msg).'");
		history.back();
	</script>';
exit;
}


#########################################################

include "header.php"; 
?>
<script type="text/javascript" src="lib.js.php"></script>
<script language="javascript" type="text/javascript" src="/js/jquery-1.7.2.min.js"></script>
<script language="javascript" type="text/javascript" src="/admin/bulkmail/javascript.js"></script>
<script language="javascript" type="text/javascript" src="/admin/calendar.js.php"></script>
<script language="javascript" type="text/javascript">
function goMenu(no,url){
	parent.topframe.GoMenu(no,url);
}
</script>
<style type="text/css">
.formTbl {
	border-top:1px solid #ccc;
	border-left:1px solid #ccc;
}
.formTbl caption {
	font-size:12px;
	padding:5px 0px 3px 0px;
}
.formTbl th {
	background:#efefef;
	border-bottom:1px solid #ccc;
	border-right:1px solid #ccc;
	font-weight:normal;
	font-size:11px;
}
.formTbl td {
	background:#fff;
	border-bottom:1px solid #ccc;
	border-right:1px solid #ccc;
	padding:2px 0px 2px 5px;
}
</style>
<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed"  background="images/con_bg.gif">
	<tr>
		<td valign="top"  background="images/leftmenu_bg.gif" style="width:198px"><? include "menu_market.php"; ?></td>	
		<td style="width:10px"></td>
		<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td height="29" colspan="3" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 프로모션 &gt; 쿠폰발행서비스  &gt; <span class="2depth_select">오프라인 쿠폰 관리</span></td>
				</tr>
				<tr>
					<td style="width:16px;"><img src="images/con_t_01.gif" width="16" height="16" border="0"></td>
					<td background="images/con_t_01_bg.gif" style="width:100%"></td>
					<td style="width:16px;"><img src="images/con_t_02.gif" width="16" height="16" border="0"></td>
				</tr>
				<tr>
					<td background="images/con_t_04_bg1.gif"></td>
					<td bgcolor="#ffffff" style="padding:10px">
						<?			
									switch($_REQUEST['mode']){										
										case 'new':
											include_once './offcoupon/form.php';		
											break;
										case 'list':
										default:
											$_REQUEST['issue_type'] = 'OP';
											$result = $coupon->_couponList($_REQUEST);
											$issues = $coupon->_issueList(array('coupon_code'=>$_REQUEST['coupon_code'],'page'=>$_REQUEST['issue_page'],'search'=>$_REQUEST['issue_search']));
											include_once './offcoupon/list.php';		
											break;
									}
									?>
					</td>
					<td background="images/con_t_02_bg.gif"></td>
				</tr>
				<tr>
					<td><img src="images/con_t_04.gif" width="16" height="16" border="0"></td>
					<td background="images/con_t_04_bg.gif"></td>
					<td><img src="images/con_t_03.gif" width="16" height="16" border="0"></td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<?=$onload?>
<? include "copyright.php"; ?>
