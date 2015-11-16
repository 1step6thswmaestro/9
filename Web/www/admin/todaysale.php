<?
//error_reporting(E_ALL);
//ini_set("display_errors", 1);

$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/ext/func.php");
include ("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "gong-4";
$MenuCode = "gong";
if (!$_usersession->isAllowedTask($PageCode)) {
	include ("AccessDeny.inc.php");
	exit;
}
#########################################################

$rpath = dirname(__FILE__).'/todaysale/';

switch($_REQUEST['mode']){
	case "topdesign":
		$nvaitit = '상단디자인';
		$file = 'topdesign.php';
		break;
	case "new":
		$nvaitit = '상품 등록';
	case "modify":
	case "update":
		if(_empty($navitit)) $navitit = '상품 수정';
		$file = 'modify.php';
		break;
		/*
	case "delete":
		include("social_shopping_proc.php");
		break;*/
	case "orders":
		$navitit = '주문 관리';
		$file = 'orderlist.php';
		break;
	case 'list':
	default:
		$navitit = '상품 목록';
		$file = 'list.php';
		break;
}

include "header.php"; ?>
<? /* <script type="text/javascript" src="lib.js.php"></script> */ ?>
<script language="javascript" type="text/javascript" src="/js/jquery-1.8.3.min.js"></script>
<script language="javascript" type="text/javascript" src="/js/jquery-ui-1.9.2.custom.min.js"></script>
<style type="text/css">
@import url("/css/ui-lightness/jquery-ui-1.9.2.custom.min.css");
</style>

<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed"  background="images/con_bg.gif">
	<tr>
		<td valign="top"  background="images/leftmenu_bg.gif" style="width:192px;">
			<? // include_once  $rpath."menu.php";
			 include ("menu_gong.php");
			?>
		</td>
		<td style="width:10px;"></td>
		<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%" style="margin-bottom:20px;">
				<caption style="height:28px; font-size:11px; color:6c6c6c; padding:5px 0px; text-align:left;background:url(images/con_link_bg.gif)"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 투데이세일 &gt; <span class="2depth_select"><?=$navitit?></span></caption>
				<tr>
					<td width="16"><img src="images/con_t_01.gif" width="16" height="16" border="0"></td>
					<td background="images/con_t_01_bg.gif"></td>
					<td width="16"><img src="images/con_t_02.gif" width="16" height="16" border="0"></td>
				</tr>
				<tr>
					<td width="16" background="images/con_t_04_bg1.gif"></td>
					<td bgcolor="#ffffff" style="padding:10px">
						<table width="100%" cellpadding="0" cellspacing="0">
							<tr>
								<td>
									<?
										include_once($rpath."init.php");
										if(!file_exists($rpath.$file)) echo '파일을 찾을수 없습니다.';
										else require($rpath.$file);
									?>
								</td>
							</tr>
						</table>
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
<? include "copyright.php"; ?>