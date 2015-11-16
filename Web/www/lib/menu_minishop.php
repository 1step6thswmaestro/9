<?
if(substr(getenv("SCRIPT_NAME"),-18)=="/menu_minishop.php"){
	header("HTTP/1.0 404 Not Found");
	exit;
}
/* 입점업체 정보노출 관련 jdy */
include_once($Dir."lib/admin_more.php");

$_dataShopMoreInfo = getShopMoreInfo();
/* 입점업체 정보노출 관련 jdy */


if ($_data->frame_type!="N") include($Dir.MainDir.$_data->onetop_type.".php");
else if($_data->align_type=="Y") echo "<center>";

$clipcopy="http://".$_ShopInfo->getShopurl().(MinishopType=="ON"?"minishop/":"minishop.php?storeid=").$_minidata->id;

$prdataA=$_MiniLib->prdataA;
$prdataB=$_MiniLib->prdataB;
$themeprdataA=$_MiniLib->themeprdataA;
$themeprdataB=$_MiniLib->themeprdataB;

?>
<SCRIPT LANGUAGE="JavaScript">
<!--
var quickview_path="<?=$Dir.FrontDir?>product.quickview.xml.php";
var quickfun_path="<?=$Dir.FrontDir?>product.quickfun.xml.php";
function sendmail() {
	window.open("<?=$Dir.FrontDir?>email.php","email_pop","height=100,width=100");
}
function estimate(type) {
	if(type=="Y") {
		window.open("<?=$Dir.FrontDir?>estimate_popup.php","estimate_pop","height=100,width=100,scrollbars=yes");
	} else if(type=="O") {
		if(typeof(top.main)=="object") {
			top.main.location.href="<?=$Dir.FrontDir?>estimate.php";
		} else {
			document.location.href="<?=$Dir.FrontDir?>estimate.php";
		}
	}
}
function privercy() {
	window.open("<?=$Dir.FrontDir?>privercy.php","privercy_pop","height=570,width=590,scrollbars=yes");
}
function order_privercy() {
	window.open("<?=$Dir.FrontDir?>privercy.php","privercy_pop","height=570,width=590,scrollbars=yes");
}
function logout() {
	location.href="<?=$Dir.MainDir?>main.php?type=logout";
}
function sslinfo() {
	window.open("<?=$Dir.FrontDir?>sslinfo.php","sslinfo","width=100,height=100,scrollbars=no");
}
function memberout() {
	if(typeof(top.main)=="object") {
		top.main.location.href="<?=$Dir.FrontDir?>mypage_memberout.php";
	} else {
		document.location.href="<?=$Dir.FrontDir?>mypage_memberout.php";
	}
}
function notice_view(type,code) {
	if(type=="view") {	
		window.open("<?=$Dir.FrontDir?>notice.php?type="+type+"&code="+code,"notice_view","width=450,height=450,scrollbars=yes");
	} else {
		window.open("<?=$Dir.FrontDir?>notice.php?type="+type,"notice_view","width=450,height=450,scrollbars=yes");
	}
}
function information_view(type,code) {
	if(type=="view") {	
		window.open("<?=$Dir.FrontDir?>information.php?type="+type+"&code="+code,"information_view","width=600,height=500,scrollbars=yes");
	} else {
		window.open("<?=$Dir.FrontDir?>information.php?type="+type,"information_view","width=600,height=500,scrollbars=yes");
	}
}
function GoPrdtItem(prcode) {
	window.open("<?=$Dir.FrontDir?>productdetail.php?productcode="+prcode,"prdtItemPop","WIDTH=800,HEIGHT=700 left=0,top=0,toolbar=yes,location=yes,directories=yse,status=yes,menubar=yes,scrollbars=yes,resizable=yes");
}
//-->
</SCRIPT>
<table border=0 width="<?=$_minidata->shop_width?>" cellpadding=0 cellspacing=0 style="table-layout:fixed" id="tableposition">
<tr><td height=3></td></tr>
<tr>
	<td style="padding-top:8px; padding-left:22px">
	<!-- 현재위치 들어가는부분 ($minishop_navi) -->
	<table border=0 cellpadding=0 cellspacing=0>
	<tr height=19>
		<td style="padding-right:5;padding-bottom:1px;"><?=$strlocation?></td>
		<td style="padding-right:3px;padding-bottom:3px"><A HREF="javascript:ClipCopy('<?=$clipcopy?>')"><img src="<?=$Dir?>images/minishop/btn_addr_copy.gif" border=0></A></td>
	</tr>
	</table>
	</td>
</tr>
<tr><td height=3></td></tr>
<tr>
	<td>
	<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
	<col width=187></col>
	<col width=></col>
	<!-- 상단 타이틀부분 들어가는 곳 -->
	<tr>
		<td background="<?=$_minidata->top_backimg?>" style="background-repeat:no-repeat;background-position:left top">
		<table border=0 cellpadding=0 cellspacing=0 width=100%>
		<tr>
			<td style="padding:5,0,0,5">

			<table border=0 cellpadding=0 cellspacing=0 width=187 style="table-layout:fixed">
			<tr>
				<td width=187 bgcolor=#ffffff  style="padding-top:5px">
				
				<table width=100% border=0 cellspacing=0 cellpadding=0 >
				<tr>
					<td align=center valign=middle><a href='<?=$Dir.FrontDir?>minishop.php?sellvidx=<?=$_minidata->vender?>'><img src="<?=$_minidata->logo?>" width=165 height=80 border=0></a></td>
				</tr>
				<form name=custregminiform method=post>
				<input type=hidden name=sellvidx value="<?=$_minidata->vender?>">
				<input type=hidden name=memberlogin value="<?=(strlen($_ShopInfo->getMemid())>0?"Y":"N")?>">
				</form>
				<tr>
					<td align=center valign=top style="padding-top:3px"><A HREF="javascript:custRegistMinishop()"><img src="<?=$Dir?>images/minishop/dangol.gif" border=0></A></td>
				</tr>
				</table>

				</td>
			</tr>
			</table>

			</td>
		</tr>
		</table>
		</td>

		<td valign=top background="<?=$_minidata->top_backimg?>" style="background-repeat:no-repeat;background-position:right top">
		<table border=0 cellpadding=0 cellspacing=0 width=100%>
		<tr>
			<td valign=top>
			<table border=0 cellpadding=0 cellspacing=0 width=88%>
			<tr>
				<td style="color:#<?=$_minidata->top_fontcolor?>;padding:20,0,0,30">
				<FONT style="font-size:13"><B><?=$_minidata->brand_name?></B></font>( <?=$_minidata->prdt_cnt?>개 상품 / <?=$_minidata->id?></FONT>)
				</td>
			</tr>
			<tr><td height=5></td></tr>
			<tr>
				<td style="color:#<?=$_minidata->top_fontcolor?>;padding:0,0,0,30">
				<FONT style="font-size:11px;word-spacing:-2px;"><?=$_minidata->brand_description?></font>
				</td>
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
<tr><td height=10></td></tr>
<tr>
	<td width=100% valign=top>
	<table border=0 cellpadding=0 cellspacing=0 width=100% height=100%>
	<tr>
		<td width=187 valign=top height=100%>


		
		<table cellpadding="0" cellspacing="0" width="100%"  height="100%" style="table-layout" border=0>
		<tr>
			<td height=10 valign=top background="<?=$Dir?>images/minishop/bg/<?=$_minidata->color?>_bg_title.gif" style="padding:0,7;"></td>
		</tr>
		<tr>
			<td valign=top background="<?=$Dir?>images/minishop/bg/<?=$_minidata->color?>_bg_bg.gif" style="padding-top:5"  height="100%">



		<table cellpadding="0" cellspacing="0" width="100%" style="table-layout" border=0>
		<tr>
			<td style="padding-left:10"><img src="<?=$Dir?>images/minishop/search_title.gif" border=0></td>
		</tr>
		<tr>
			<td align=center>

				<table cellpadding="0" cellspacing="1" width="90%" bgcolor="#E5E5E5">
					<tr>
						<td bgcolor="#F5F5F5" style="padding:6px;">
						
							<table width=100% border=0 cellspacing=2 cellpadding=0 style="table-layout:fixed">
							<form name="MinishopSearchForm">
							<input type="hidden" name="sellvidx" value="">
							<tr>
								<td>
								<select name="search_gbn" style=width:100%>
								<option value="store">이 미니샵 상품</option>
								<option value="all">쇼핑몰 전체 상품</option>
								</select>
								</td>
							</tr>
							<tr>
								<td><input type=text name="search" size=16 value="" onkeydown="if (event.keyCode == 13) return SearchMinishop();" style="width:100px;height:18px"> <img src="<?=$Dir?>images/minishop/btn_search.gif" border=0 align=absmiddle style="cursor:hand" onClick="SearchMinishop()"></td>
							</tr>
							</form>
							</table>


						</td>
					</tr>
				</table>




			<script>
			function SearchMinishop() {
				if(document.MinishopSearchForm.search.value.length<=0) {
					alert("검색어를 입력하세요.");
					document.MinishopSearchForm.search.focus();
					return;
				} else {
					if(document.MinishopSearchForm.search_gbn.value=="all") {
						document.MinishopSearchForm.action="<?=$Dir.FrontDir?>productsearch.php";
						document.MinishopSearchForm.submit();
					} else {
						document.MinishopSearchForm.sellvidx.value="<?=$_minidata->vender?>";
						document.MinishopSearchForm.action="<?=$Dir.FrontDir?>minishop.productsearch.php";
						document.MinishopSearchForm.submit();
					}
				}
			}
			</script>

			</td>
		</tr>
		<tr><td height=30></td></tr>
<?
		if(substr($_minidata->code_distype,0,1)=="Y") {
?>
		<tr>
			<td align=center>
			<table width=100% cellspacing=0 cellpadding=0 border=0>
			<tr>
				<!--<td>&nbsp;&nbsp;<FONT COLOR="<?=$_minidata->fontcolor?>"><B><?=$_minidata->brand_name?> 카테고리</B></FONT></td>-->
				<td style="padding-left:10"><img src="<?=$Dir?>images/minishop/category_title.gif" border=0></td>
			</tr>
			<tr>
				<td align=center>
				<table width=187 cellspacing=0 cellpadding=0 border=0>
				<tr>
					<td style='padding-left:16'>
					<table border=0 cellpadding=0 cellspacing=0 width=100%>
<?
					for($i=0;$i<count($prdataA);$i++) {
						$tmpcode=$prdataA[$i]->codeA."000";
						if($i>0) echo "<tr><td height=10></td></tr>\n";
						echo "<tr>\n";
						echo "	<td><img src=\"".$Dir."images/minishop/icon_catedot.gif\" border=0> ";
						if($tgbn!="10" || $code!=$tmpcode) {
							echo "<A HREF=\"javascript:GoSection('".$_minidata->vender."','10','".$tmpcode."')\"><B>".$prdataA[$i]->code_name."</B></A>";
						} else {
							echo "<FONT style=\"text-decoration: underline;\"><B>".$prdataA[$i]->code_name."</B></font>";
						}
						echo "	</td>\n";
						echo "</tr>\n";
						unset($strprdata);
						for($j=0;$j<count($prdataB[$prdataA[$i]->codeA]);$j++) {
							$tmpcode=$prdataB[$prdataA[$i]->codeA][$j]->codeA.$prdataB[$prdataA[$i]->codeA][$j]->codeB;
							if($j>0) $strprdata.=" | ";
							if($tgbn!="10" || $code!=$tmpcode) {
								$strprdata.="<A HREF=\"javascript:GoSection('".$_minidata->vender."','10','".$tmpcode."')\">".$prdataB[$prdataA[$i]->codeA][$j]->code_name."</A>";
							} else {
								$strprdata.="<FONT style=\"text-decoration: underline;\">".$prdataB[$prdataA[$i]->codeA][$j]->code_name."</FONT>";
							}
						}
						if(strlen($strprdata)>0) {
							echo "<tr>\n";
							echo "	<td style=\"padding:5,0,0,15\">".$strprdata."</td>\n";
							echo "</tr>\n";
						}
					}
?>
					</table>
					</td>
				</tr>
				</table>
				</td>
			</tr>
			</table>
			</td>
		</tr>
		<tr><td height=20></td></tr>
<?
		}

		if(substr($_minidata->code_distype,1,1)=="Y" && count($themeprdataA)>0) {
?>
		<tr>
			<td align=center>
			<table width=100% cellspacing=0 cellpadding=0 border=0>
			<tr>
				<!--<td>&nbsp;&nbsp;<FONT COLOR="<?=$_minidata->fontcolor?>"><B>테마 카테고리</B></FONT></td>-->
				<td style="padding-left:10"><img src="<?=$Dir?>images/minishop/tmcategory_title.gif" border=0></td>
			</tr>
			<tr>
				<td align=center>
				<table width=187 cellspacing=0 cellpadding=0 border=0 >
				<tr>
					<td style='padding-left:16'>
					<table border=0 cellpadding=0 cellspacing=0 width=100%>
<?
					for($i=0;$i<count($themeprdataA);$i++) {
						$tmpcode=$themeprdataA[$i]->codeA."000";
						if($i>0) echo "<tr><td height=10></td></tr>\n";
						echo "<tr>\n";
						echo "	<td><img src=\"".$Dir."images/minishop/icon_catedot.gif\" border=0> ";
						if($tgbn!="20" || $code!=$tmpcode) {
							echo "<A HREF=\"javascript:GoSection('".$_minidata->vender."','20','".$tmpcode."')\"><B>".$themeprdataA[$i]->code_name."</B></A>";
						} else {
							echo "<FONT style=\"text-decoration: underline;\"><B>".$themeprdataA[$i]->code_name."</B></font>";
						}
						echo "	</td>\n";
						echo "</tr>\n";
						unset($strprdata);
						for($j=0;$j<count($themeprdataB[$themeprdataA[$i]->codeA]);$j++) {
							$tmpcode=$themeprdataB[$themeprdataA[$i]->codeA][$j]->codeA.$themeprdataB[$themeprdataA[$i]->codeA][$j]->codeB;
							if($j>0) $strprdata.=" | ";
							if($tgbn!="20" || $code!=$tmpcode) {
								$strprdata.="<A HREF=\"javascript:GoSection('".$_minidata->vender."','20','".$tmpcode."')\">".$themeprdataB[$themeprdataA[$i]->codeA][$j]->code_name."</A>";
							} else {
								$strprdata.="<FONT style=\"text-decoration: underline;\">".$themeprdataB[$themeprdataA[$i]->codeA][$j]->code_name."</FONT>";
							}
						}
						if(strlen($strprdata)>0) {
							echo "<tr>\n";
							echo "	<td style=\"padding:5,0,0,15\">".$strprdata."</td>\n";
							echo "</tr>\n";
						}
					}
?>
					</table>
					</td>
				</tr>
				</table>
				</td>
			</tr>
			</table>
			</td>
		</tr>
		<tr><td height=20></td></tr>

		<?}?>

		<tr>
			<td align=center>
			<table width=100% cellspacing=0 cellpadding=0 border=0>
			<tr>
				<td align=center>
				<table width=187 cellspacing=0 cellpadding=0 border=0 >
				<tr>
					<td style='padding-top:5'>
					<table border=0 cellpadding=0 cellspacing=0 width=100% >
					<tr height=30>
						<td style="padding-left:10">
						<img src="<?=$Dir?>images/minishop/menu_notice.gif" border=0>
						</td>
						<td align=right style="padding-right:10">
						<A HREF="javascript:GoNoticeList('<?=$_minidata->vender?>')"><img src="<?=$Dir?>images/minishop/btn_more.gif" border=0></A>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				<tr>
					<td style='padding-left:16'>
					<table border=0 cellpadding=0 cellspacing=0 width=100%>
<?
					$sql = "SELECT date,subject FROM tblvendernotice WHERE vender='".$_minidata->vender."' ";
					$sql.= "ORDER BY date DESC LIMIT 5 ";
					$result=mysql_query($sql,get_db_conn());
					while($row=mysql_fetch_object($result)) {
						echo "<tr><td><span style=word-break:break-all;height:16;overflow:hidden;><A HREF=\"javascript:GoNoticeView('".$_minidata->vender."','".$row->date."')\"><B>·</B> ".titleCut(23,strip_tags($row->subject))."</A></span></td></tr>\n";
						echo "<tr><td height=3></td></tr>\n";
					}
					mysql_free_result($result);
?>
					</table>
					</td>
				</tr>
				</table>
				</td>
			</tr>
			</table>
			</td>
		</tr>
		<tr><td height=25></td></tr>

		<?//if(strlen($_minidata->cust_info)>0){?>
		<? if ($_dataShopMoreInfo['info_view']==1 || strlen($_dataShopMoreInfo['info_view']) ==0) { ?>
		<tr>
			<td align=center>
			<table width=100% cellspacing=0 cellpadding=0 border=0>
			<tr>
				<td align=center>
				<table width=187 cellspacing=0 cellpadding=0 border=0>
				<tr>
					<td>
					<table border=0 cellpadding=0 cellspacing=0 width=100% >
					<tr>
						<td style="padding-left:10"><img src="<?=$Dir?>images/minishop/menu_cust.gif" border=0></td>
					</tr>
					</table>
					</td>
				</tr>
				<tr>
					<td style='padding-left:16'>

					<table width=100% border=0 cellspacing=0 cellpadding=0>
					<tr> 
						<td height=18><img src="<?=$Dir?>images/minishop/menu_cust_text01.gif" border=0 alt="문의전화"></td>
					</tr>
					<tr><td height=3></td></tr>
					<tr> 
						<td height=17><img src="<?=$Dir?>images/minishop/icon_phone01.gif" border=0 align=absmiddle> <?=$_minidata->custdata["TEL"]?></td>
					</tr>
					<tr> 
						<td height=17><img src="<?=$Dir?>images/minishop/icon_fax01.gif" border=0 align=absmiddle> <?=$_minidata->custdata["FAX"]?></td>
					</tr>
					<tr> 
						<td height=17 style="word-break:break-all"><img src="<?=$Dir?>images/minishop/icon_email01.gif" border=0 align=absmiddle> <?=$_minidata->custdata["EMAIL"]?></td>
					</tr>
					</table>
					<table width=100% border=0 cellspacing=0 cellpadding=0>
					<tr> 
						<td height=10></td>
					</tr>
					<tr> 
						<td><img src="<?=$Dir?>images/minishop/menu_cust_text02.gif" border=0 alt="고객상담시간"></td>
					</tr>
					<tr><td height=3></td></tr>
					<tr> 
						<td>평일 : <?=$_minidata->custdata["TIME1"]?></td>
					</tr>
					<tr> 
						<td>토요일 : <?=$_minidata->custdata["TIME2"]?></td>
					</tr>
					<tr> 
						<td>일ㆍ공휴일 : <?=$_minidata->custdata["TIME3"]?></td>
					</tr>
					</table>

					</td>
				</tr>
				</table>
				</td>
			</tr>
			<tr><td height=5></td></tr>
			</table>
			</td>
		</tr>
		<tr><td height=5></td></tr>
		<? } ?>
		<?//}?>


		</table>
		<tr>
			<td height=10 valign=top background="<?=$Dir?>images/minishop/bg/<?=$_minidata->color?>_bg_bottom.gif" style="padding:0,7;"></td>
		</tr>
		</table>


		</td>
		<td width="<?=($_minidata->shop_width-200)?>" align=center valign=top nowrap>
