<?
$Dir="../";

include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

include ("access.php");
include_once $Dir.'lib/ext/func.php';
include_once $Dir.'lib/class/attendance.php';
include_once $Dir.'lib/class/pages.php';
####################### 페이지 접근권한 check ###############
$PageCode = "ma-3";
$MenuCode = "market";
if (!$_usersession->isAllowedTask($PageCode)) {
	include ("AccessDeny.inc.php");
	exit;
}
#########################################################
$attendance = new attendance();

?>
<? include "header.php"; ?>
<style type="text/css">
.formTbl{ border-top:1px solid #ccc; border-left:1px solid #ccc;}
.formTbl caption{ font-size:12px; padding:5px 0px 3px 0px; text-align:left; font-weight:bold; font-size:13px; border-top:2px solid #000}
.formTbl th{ background:#efefef; border-bottom:1px solid #ccc; border-right:1px solid #ccc; font-weight:normal; font-size:12px;}
.formTbl td{background:#fff; border-bottom:1px solid #ccc; border-right:1px solid #ccc; padding:2px 0px 2px 5px; font-size:12px;}

.formTbl thead th{ background:#efefef; border-bottom:1px solid #ccc; border-right:1px solid #ccc; font-weight:normal; font-size:12px;}
.formTbl thead td{background:#fff; border-bottom:1px solid #ccc; border-right:1px solid #ccc; padding:2px 0px 2px 5px; font-size:12px;}


.formTbl tbody th{ background:#efefef; border-bottom:1px solid #ccc; border-right:1px solid #ccc; font-weight:normal; font-size:12px;}
.formTbl tbody td{background:#fff; border-bottom:1px solid #ccc; border-right:1px solid #ccc; padding:2px 0px 2px 5px; font-size:12px;}

.formTbl tbody td.msgRow{ height:30px; text-align:center}

</style>
<script language="javascript" type="text/javascript" src="/js/jquery-1.10.2.min.js"></script>
<script language="javascript" type="text/javascript" src="/js/jquery-ui-1.10.4.custom.min.js"></script>
<link href="/css/ui-lightness/jquery-ui-1.10.4.custom.min.css" rel="stylesheet">
<script language="javascript" type="text/javascript">
$(function() {
	$('.modifyBtn').click(function(e){
		if($(this).attr('aidx')){
			document.location.href = '?act=edit&aidx='+$(this).attr('aidx');
		}else{
			document.location.href = '?act=new';
		}
	});
	
	$('.listBtn').click(function(e){
		document.location.href = '?act=list';
	});
	
	
	$('.viewStampBtn').click(function(e){
		if($(this).attr('aidx')){
			document.location.href = '?act=viewstamp&aidx='+$(this).attr('aidx');
		}
	});
	
	$('.viewRewardBtn').click(function(e){
		if($(this).attr('aidx')){
			document.location.href = '?act=viewraward&aidx='+$(this).attr('aidx');
		}
	});	
	
});
</script>
<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed"  background="images/con_bg.gif">	
	<tr>
		<td valign="top" style="width:198px; background:url(images/leftmenu_bg.gif)">	
			<? include ("menu_market.php"); ?>	
		</td>
		<td style="width:10px;"></td>
		<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td height="29" colspan="3" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 프로모션 &gt;  <span class="2depth_select">출석체크 이벤트 설정</span></td>
				</tr>
				<tr>
					<td width="16"><img src="images/con_t_01.gif" width="16" height="16" border="0"></td>
					<td background="images/con_t_01_bg.gif"></td>
					<td width="16"><img src="images/con_t_02.gif" width="16" height="16" border="0"></td>
				</tr>
				<tr>
					<td width="16" background="images/con_t_04_bg1.gif"></td>
					<td bgcolor="#ffffff" style="padding:10px;">
						<div>
							<div style="width:100%; background:url(images/title_bg.gif) left bottom repeat-x; margin-top:8px; margin-bottom:3px; padding-bottom:21px;"> <img src="images/market_attendance_title.gif" alt="출석 이벤트 관리"> </div>
							<span class="notice_blue" style=" display:block; padding-left:22px;">현재 진행중인 출석 체크 이벤트 정보를 확인할 수 있는 메뉴 입니다.<!-- <a href="javascript:document.location.reload()">[새로고침]</a>--></span>
						<?
						$result = array();
						switch($_REQUEST['act']){
							case 'modify':
							case 'edit':
								/*if($attendance->_checkAdmin()){
								}else{
								}*/					
								if(false === $result['item'] = $attendance->_getItem($_REQUEST['aidx'])) $result['msg'] = $attendance->_msg();
							case 'new':								
								include_once dirname(__FILE__).'/attendance/form.php';
								break;
							case 'viewraward':
								if(false === $attendance->_set($_REQUEST['aidx'])) $result['msg'] = $attendance->_msg();
								$result = $attendance->_giveRewardList($_REQUEST);
								include_once dirname(__FILE__).'/attendance/rewardlist.php';
								break;
							case 'viewstamp':
								if(false === $attendance->_set($_REQUEST['aidx'])) $result['msg'] = $attendance->_msg();
								$result = $attendance->_getStampList($_REQUEST);
								include_once dirname(__FILE__).'/attendance/stamplist.php';
								break;
							case 'list':
							default:
								if(false === $attendance->_set($_REQUEST['aidx'])) $result['msg'] = $attendance->_msg();
								
								if(false === $result = $attendance->_getList($_REQUEST)){
									echo $attendance->_msg();
								}else{									
									include_once dirname(__FILE__).'/attendance/list.php';
								}
								break;
						}
						?>
						</div>
					</td>
					<td width="16" background="images/con_t_02_bg.gif"></td>
				</tr>
				<tr>
					<td width="16"><img src="images/con_t_04.gif" width="16" height="16" border="0"></td>
					<td background="images/con_t_04_bg.gif"></td>
					<td width="16"><img src="images/con_t_03.gif" width="16" height="16" border="0"></td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<?=$onload?>
<? include "copyright.php"; ?>