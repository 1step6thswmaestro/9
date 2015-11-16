<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/class/XMLParser.php");
include_once($Dir."lib/class/pages.php");

include_once($Dir."lib/class/bankda.php");


include ("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "or-4";
$MenuCode = "order";
if (!$_usersession->isAllowedTask($PageCode)) {
	include ("AccessDeny.inc.php");
	exit;
}

$bankda = new bankda();
$svinfo = $bankda->_checkSolutionAuth();
if(_empty($svinfo['err']) && $svinfo['err'] != 'ok'){
	_alert($svinfo['err'],'-1');
}
if(!_isInt($svinfo['uid'])){
	_alert("솔루션 서비스 인증에 문제가 있습니다. 관리자에게 문의 바랍니다.".$svinfo['err'],'-1');
}

if(_empty($svinfo['user_id']) || _empty($svinfo['enddate']) || $svinfo['enddate'] < date('Y-m-d')){
	_alert("무통장 입금확인 서비스를 신청하시겠습니까?","/admin/service_deposit.php");
	exit;
}

include "header.php";
?>
<script language="javascript" type="text/javascript" src="/js/jquery-1.7.2.min.js"></script>
<script language="javascript" type="text/javascript">
function goMenu(no,url){
	parent.topframe.GoMenu(no,url);
}
</script>
<style type="text/css">
.formTbl{ border-top:1px solid #ccc; border-left:1px solid #ccc;}
.formTbl caption{ font-size:12px; padding:5px 0px 3px 0px;}
.formTbl th{ background:#efefef; border-bottom:1px solid #ccc; border-right:1px solid #ccc; font-weight:normal; font-size:11px;}
.formTbl td{background:#fff; border-bottom:1px solid #ccc; border-right:1px solid #ccc; padding:2px 0px 2px 5px;}
</style>
<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
	<tr>
		<td valign="top">
			<table cellpadding="0" cellspacing="0" width=100% style="table-layout:fixed">
				<tr>
					<td>
						<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed"  background="images/con_bg.gif">
							<tr>
								<td valign="top"  background="images/leftmenu_bg.gif" style="width:198px">
									<? include "menu_order.php"; ?>
								</td>
								<td style="width:10px"></td>
								<td valign="top">
									<table cellpadding="0" cellspacing="0" width="100%">
										<tr>
											<td height="29" colspan="3" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 주문/매출 &gt; 무통장 입금확인 &gt; <span class="2depth_select">무통장 입금확인</span></td>
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

												switch($_REQUEST['act']){
													case 'bankadd':
														include_once './bankda/order_bankadd.php';
														break;
													case 'bankm':
													default:
														include_once './bankda/order_bankm.php';
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
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<?=$onload?>
<? include "copyright.php"; ?>
