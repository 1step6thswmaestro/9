<?
switch(substr(strrchr(getenv("SCRIPT_NAME"),"/"),1)) {
	case "marketing_step_01.php":
		$menuidx = "shop1"; $idx[0][0] = 'YES'; break;
	case "marketing_step_02.php":
		$menuidx = "shop1"; $idx[0][1] = 'YES'; break;
	case "marketing_step_03.php":
		$menuidx = "shop1"; $idx[0][2] = 'YES'; break;
	case "marketing_step_04.php":
		$menuidx = "shop1"; $idx[0][3] = 'YES'; break;
	case "marketing_step_05.php":
		$menuidx = "shop1"; $idx[0][4] = 'YES'; break;
	case "marketing_naver.php":
		$menuidx = "shop2"; 
		$selstep = -1;
		for($kk=0;$kk<4;$kk++){
			if($kk == $_REQUEST['step'] -1){
				$idx[1][$kk] = 'YES';
				$selstep = $kk;
				break;
			}
		}
		if($selstep < 0) $idx[1][0] = 'YES';
		
		break;
/*
	case "marketing_naver.php":
		$menuidx = "shop2"; $idx[1][0] = 'YES'; break;
	case "marketing_naver_01.php":
		$menuidx = "shop2"; $idx[1][1] = 'YES'; break;
	case "marketing_naver_02.php":
		$menuidx = "shop2"; $idx[1][2] = 'YES'; break;
	case "marketing_naver_ep.php":
		$menuidx = "shop2"; $idx[1][3] = 'YES'; break;
*/	
	case "marketing_daum.php":
		$menuidx = "shop3"; 
		$selstep = -1;
		for($kk=0;$kk<4;$kk++){
			if($kk == $_REQUEST['step'] -1){
				$idx[2][$kk] = 'YES';
				$selstep = $kk;
				break;
			}
		}
		if($selstep < 0) $idx[2][0] = 'YES';
		
		break;
/*
	case "marketing_daum.php":
		$menuidx = "shop3"; $idx[2][0] = 'YES'; break;
	case "marketing_daum_01.php":
		$menuidx = "shop3"; $idx[2][1] = 'YES'; break;
	case "marketing_daum_02.php":
		$menuidx = "shop3"; $idx[2][2] = 'YES'; break;
		*/
	
	case "marketing_nate.php":
		$menuidx = "shop4"; $idx[3][0] = 'YES'; break;
	case "marketing_nate_01.php":
		$menuidx = "shop4"; $idx[3][1] = 'YES'; break;
	case "marketing_nate_02.php":
		$menuidx = "shop4"; $idx[3][2] = 'YES'; break;

	case "marketing_about.php":
		$menuidx = "shop5"; $idx[4][0] = 'YES'; break;
	case "marketing_about_01.php":
		$menuidx = "shop5"; $idx[4][1] = 'YES'; break;
	case "marketing_about_02.php":
		$menuidx = "shop5"; $idx[4][2] = 'YES'; break;

	case "marketing_keyword.php":
		$menuidx = "shop6"; $idx[5][0] = 'YES'; break;
	case "marketing_keyword1.php":
		$menuidx = "shop6"; $idx[5][1] = 'YES'; break;
	case "marketing_keyword2.php":
		$menuidx = "shop6"; $idx[5][2] = 'YES'; break;
	case "marketing_keyword3.php":
		$menuidx = "shop6"; $idx[5][3] = 'YES'; break;
	case "marketing_keyword4.php":
		$menuidx = "shop6"; $idx[5][4] = 'YES'; break;
	case "marketing_keyword5.php":
		$menuidx = "shop6"; $idx[5][5] = 'YES'; break;

	case "marketing_blog.php":
		$menuidx = "shop7"; $idx[6][0] = 'YES'; break;
	case "marketing_blog2.php":
		$menuidx = "shop7"; $idx[6][1] = 'YES'; break;
	case "marketing_sns.php":
		$menuidx = "shop7"; $idx[6][2] = 'YES'; break;
	case "marketing_cafe.php":
		$menuidx = "shop7"; $idx[6][3] = 'YES'; break;
	case "marketing_comm.php":
		$menuidx = "shop7"; $idx[6][4] = 'YES'; break;
	case "marketing_etc.php":
		$menuidx = "shop7"; $idx[6][5] = 'YES'; break;

	case "marketing_ip.php":
		$menuidx = "shop8"; $idx[7][0] = 'YES'; break;

	case "marketing_mobile.php":
		$menuidx = "shop9"; $idx[8][0] = 'YES'; break;
}

function noselectmenu($name,$url,$idx,$end){
	if($end==0 || $end==3){
		echo "<tr><td  height=\"8\"></td></tr>";
	}
	$str_style_class="depth2_default";
	if ($idx == "YES") {
		$str_style_class = "depth2_select";
	}
	echo "<tr>\n";
	echo "	<td height=\"19\"  style=\"padding-left:33px;\" class=\"".$str_style_class."\"><img src=\"images/icon_leftmenu1.gif\" border=\"0\"><a href=\"".$url."\">".$name."</a></td>\n";
	echo "</tr>\n";
	if($end==2 || $end==3){
		echo "<tr><td height=\"25\" ></td></tr>";
	}
}
?>
<SCRIPT LANGUAGE="JavaScript">
<!--
layerlist = new Array ('shop1','shop2','shop3','shop4','shop5','shop6','shop7','shop8','shop9');
var thisshop="<?=$menuidx?>";
ino=9;

function Change(){
	if(document.all){
		for(i=0;i<ino;i++) {
			document.all(layerlist[i]).style.display="none";
		}
		stobj="document.all(shop).style";
	} else if(document.getElementById){
		for(i=0;i<ino;i++) {
			document.getElementById(layerlist[i]).style.display="none";
		}
		stobj="document.getElementById(shop).style";
	} else if(document.layers){
		for(i=0;i<ino;i++) {
			document.layers[layerlist[i]].display=none;
		}
		stobj="document.layers[shop]";
	}
}

function ChangeMenu(shop){
	if ( thisshop !== shop){
		Change();
		eval(stobj).display="block";
		thisshop=shop;
	} else{
		Change();
		//eval(stobj).display="block";
		thisshop=stobj;
	}
}

function InitMenu(shop) {
	try {
		tblashop = "tbla".concat(shop);
		tblbshop = "tblb".concat(shop);
		document.all(shop).style.display="block";
		document.all(tblashop).style.display="none";
		document.all(tblbshop).style.display="block";
		num=shop.substring(4,5)-1;
	} catch (e) {
/*
		shop = "shop1";
		tblashop = "tblashop1";
		tblbshop = "tblbshop1";
*/
		shop = "";
		tblashop = "";
		tblbshop = "";
		document.all(shop).style.display="block";
		document.all(tblashop).style.display="none";
		document.all(tblbshop).style.display="block";
		num=shop.substring(4,5)-1;
	}
}
//-->
</SCRIPT>

<TABLE WIDTH="100%" BORDER="0" CELLPADDING="0" CELLSPACING="0">
	<TR>
		<TD height="68" align="right" valign="top" background="images/marketing_leftmenu_title.gif"><a href="javascript:scrollMove(0);"><img src="images/leftmenu_stop.gif" border="0" id="menu_pix"></a><a href="javascript:scrollMove(1);"><img src="images/leftmenu_trans.gif" border="0" id="menu_scroll"></a></TD>
	</TR>
	<TR>
		<TD  background="images/leftmenu_bg.gif">
			<TABLE WIDTH="100%" BORDER="0" CELLPADDING="0" CELLSPACING="0">
				<col width="16">
				
						</col>
				
				<col>
				
						</col>
				
				<col width="16">
				
						</col>
				
				<TR>
					<TD valign="top">
						<table width="100%" cellpadding="0" cellspacing="0" id="tblashop1">
							<tr>
								<td height="34" style="padding-left:20px;cursor:hand;" onClick="ChangeMenu('shop1');"  class="depth1_noselect"><img src="images/icon_leftmenu.gif" border="0" align="absmiddle"  style="margin-right:4px;margin-bottom:2px">단계별 마케팅전략</td>
							</tr>
						</table>
						<table width="100%" cellpadding="0" cellspacing="0" id="tblbshop1" style="display:none">
							<tr>
								<td height="34" style="padding-left:20px;cursor:hand;" class="depth1_select" onClick="ChangeMenu('shop1');"><img src="images/icon_leftmenu_select.gif"  align="absmiddle" style="margin-right:4px;margin-bottom:2px">단계별 마케팅전략</td>
							</tr>
						</table>
						<table width="100%" cellpadding="0" cellspacing="0">
							<tr>
								<td>
									<div id="shop1" style="display:none;">
										<table cellpadding="0" cellspacing="0" width="100%">
											<?
			if($menuidx && $menuidx != "shop1") {
				echo "<tr><td height=\"1\" ></td></tr>";
			}
			noselectmenu('1단계 검색엔진 등록','marketing_step_01.php?step=1',$idx[0][0],0);
			noselectmenu('2단계 입점마케팅','marketing_step_01.php?step=2',$idx[0][1],1);
			noselectmenu('3단계 키워드 광고','marketing_step_01.php?step=3',$idx[0][2],1);
			noselectmenu('4단계 포털 쇼핑박스','marketing_step_01.php?step=4',$idx[0][3],1);
			noselectmenu('5단계 브랜드 마케팅','marketing_step_01.php?step=5',$idx[0][4],1);
?>
											<tr>
												<td height="10"></td>
											</tr>
										</table>
									</div>
								</td>
							</tr>
						</table>
						<table width="100%" cellpadding="0" cellspacing="0" id="tblashop2">
							<tr>
								<td height="3" background="images/leftmenu_line.gif"></td>
							</tr>
							<tr>
								<td height="34" style="padding-left:20px;cursor:hand;" onClick="ChangeMenu('shop2');"  class="depth1_noselect"><img src="images/icon_leftmenu.gif" border="0" align="absmiddle"  style="margin-right:4px;margin-bottom:2px">네이버지식쇼핑</td>
							</tr>
						</table>
						<table width="100%" cellpadding="0" cellspacing="0" id="tblbshop2" style="display:none">
							<tr>
								<td height="3" background="images/leftmenu_line.gif"></td>
							<tr>
								<td height="34" style="padding-left:20px;cursor:hand;" class="depth1_select" onClick="ChangeMenu('shop2');"><img src="images/icon_leftmenu_select.gif"  align="absmiddle" style="margin-right:4px;margin-bottom:2px">네이버지식쇼핑</td>
							</tr>
						</table>
						<table width="100%" cellpadding="0" cellspacing="0">
							<tr>
								<td>
									<div id="shop2" style="display:none;">
										<table width="100%" cellpadding="0" cellspacing="0">
											<?
			if($menuidx != "shop2") {
				echo "<tr><td height=\"1\" ></td></tr>";
			}
			noselectmenu('네이버 지식쇼핑이란?','marketing_naver.php?step=1',$idx[1][0],0);
			noselectmenu('입점 안내','marketing_naver.php?step=2',$idx[1][1],1);
			noselectmenu('광고 안내','marketing_naver.php?step=3',$idx[1][2],1);
			noselectmenu('연동 설정 안내','marketing_naver.php?step=4',$idx[1][3],1);
?>
											<tr>
												<td height="10"></td>
											</tr>
										</table>
									</div>
								</td>
							</tr>
						</table>
						<table width="100%" cellpadding="0" cellspacing="0" id="tblashop3">
							<tr>
								<td height="3" background="images/leftmenu_line.gif"></td>
							<tr>
								<td height="34" style="padding-left:20px;cursor:hand;" onClick="ChangeMenu('shop3');"  class="depth1_noselect"><img src="images/icon_leftmenu.gif" border="0" align="absmiddle"  style="margin-right:4px;margin-bottom:2px">다음쇼핑하우</td>
							</tr>
						</table>
						<table width="100%" cellpadding="0" cellspacing="0" id="tblbshop3" style="display:none">
							<tr>
								<td height="3" background="images/leftmenu_line.gif"></td>
							<tr>
								<td height="34" style="padding-left:20px;cursor:hand;" class="depth1_select" onClick="ChangeMenu('shop3');"><img src="images/icon_leftmenu_select.gif"  align="absmiddle" style="margin-right:4px;margin-bottom:2px">다음쇼핑하우</td>
							</tr>
						</table>
						<table width="100%" cellpadding="0" cellspacing="0">
							<tr>
								<td>
									<div id="shop3" style="display:none;">
										<table width="100%" cellpadding="0" cellspacing="0">
											<?
			if($menuidx != "shop3") {
				echo "<tr><td height=\"1\" ></td></tr>";
			}
			noselectmenu('다음쇼핑하우란?','marketing_daum.php?step=1',$idx[2][0],0);
			noselectmenu('입점 안내','marketing_daum.php?step=2',$idx[2][1],1);
			noselectmenu('광고 안내','marketing_daum.php?step=3',$idx[2][2],1);
			noselectmenu('연동 설정안내','marketing_daum.php?step=4',$idx[2][3],1);
?>
											<tr>
												<td height="10"></td>
											</tr>
										</table>
									</div>
								</td>
							</tr>
						</table>
						<table width="100%" cellpadding="0" cellspacing="0" id="tblashop4">
							<tr>
								<td height="3" background="images/leftmenu_line.gif"></td>
							</tr>
							<tr>
								<td height="34" style="padding-left:20px;cursor:hand;" onClick="ChangeMenu('shop4');"  class="depth1_noselect"><img src="images/icon_leftmenu.gif" border="0" align="absmiddle"  style="margin-right:4px;margin-bottom:2px">네이트쇼핑</td>
							</tr>
						</table>
						<table width="100%" cellpadding="0" cellspacing="0" id="tblbshop4" style="display:none">
							<tr>
								<td height="3" background="images/leftmenu_line.gif"></td>
							<tr>
								<td height="34" style="padding-left:20px;cursor:hand;" class="depth1_select" onClick="ChangeMenu('shop4');"><img src="images/icon_leftmenu_select.gif"  align="absmiddle" style="margin-right:4px;margin-bottom:2px">네이트쇼핑</td>
							</tr>
						</table>
						<table width="100%" cellpadding="0" cellspacing="0">
							<tr>
								<td>
									<div id="shop4" style="display:none;">
										<table width="100%" cellpadding="0" cellspacing="0">
											<?
			if($menuidx != "shop4") {
				echo "<tr><td height=\"1\" ></td></tr>";
			}
			noselectmenu('네이트 쇼핑이란?','marketing_nate.php?step=1',$idx[3][0],0);
			noselectmenu('입점 안내','marketing_nate.php?step=2',$idx[3][1],1);
			noselectmenu('네이트 광고안내','marketing_nate.php?step=3',$idx[3][2],1);
?>
											<tr>
												<td height="10"></td>
											</tr>
										</table>
									</div>
								</td>
							</tr>
						</table>
						<table width="100%" cellpadding="0" cellspacing="0" id="tblashop5">
							<tr>
								<td height="3" background="images/leftmenu_line.gif"></td>
							</tr>
							<tr>
								<td height="34" style="padding-left:20px;cursor:hand;" onClick="ChangeMenu('shop5');"  class="depth1_noselect"><img src="images/icon_leftmenu.gif" border="0" align="absmiddle"  style="margin-right:4px;margin-bottom:2px">어바웃(지마켓+옥션)</td>
							</tr>
						</table>
						<table width="100%" cellpadding="0" cellspacing="0" id="tblbshop5" style="display:none">
							<tr>
								<td height="3" background="images/leftmenu_line.gif"></td>
							<tr>
								<td height="34" style="padding-left:20px;cursor:hand;" class="depth1_select" onClick="ChangeMenu('shop5');"><img src="images/icon_leftmenu_select.gif"  align="absmiddle" style="margin-right:4px;margin-bottom:2px">어바웃(지마켓+옥션)</td>
							</tr>
						</table>
						<table width="100%" cellpadding="0" cellspacing="0">
							<tr>
								<td>
									<div id="shop5" style="display:none;">
										<table width="100%" cellpadding="0" cellspacing="0">
											<?
			if($menuidx != "shop5") {
				echo "<tr><td height=\"1\" ></td></tr>";
			}
			noselectmenu('어바웃이란?','marketing_about.php?step=1',$idx[4][0],0);
			noselectmenu('입점 안내','marketing_about.php?step=2',$idx[4][1],1);
			noselectmenu('광고 안내','marketing_about.php?step=3',$idx[4][2],1);
?>
											<tr>
												<td height="10"></td>
											</tr>
										</table>
									</div>
								</td>
							</tr>
						</table>
						<table width="100%" cellpadding="0" cellspacing="0" id="tblashop6">
							<tr>
								<td height="3" background="images/leftmenu_line.gif"></td>
							</tr>
							<tr>
								<td height="34" style="padding-left:20px;cursor:hand;" onClick="ChangeMenu('shop6');"  class="depth1_noselect"><img src="images/icon_leftmenu.gif" border="0" align="absmiddle"  style="margin-right:4px;margin-bottom:2px">키워드광고</td>
							</tr>
						</table>
						<table width="100%" cellpadding="0" cellspacing="0" id="tblbshop6" style="display:none">
							<tr>
								<td height="3" background="images/leftmenu_line.gif"></td>
							<tr>
								<td height="34" style="padding-left:20px;cursor:hand;" class="depth1_select" onClick="ChangeMenu('shop6');"><img src="images/icon_leftmenu_select.gif"  align="absmiddle" style="margin-right:4px;margin-bottom:2px">키워드광고</td>
							</tr>
						</table>
						<table width="100%" cellpadding="0" cellspacing="0">
							<tr>
								<td>
									<div id="shop6" style="display:none;">
										<table width="100%" cellpadding="0" cellspacing="0">
											<?
			if($menuidx != "shop6") {
				echo "<tr><td height=\"1\" ></td></tr>";
			}
			noselectmenu('네이버 키워드광고','marketing_keyword.php',$idx[5][0],0);
			noselectmenu('오버추어 키워드광고','marketing_keyword1.php',$idx[5][1],1);
			noselectmenu('다음 키워드광고','marketing_keyword2.php',$idx[5][2],1);
			noselectmenu('구글 키워드광고','marketing_keyword3.php',$idx[5][3],1);
			noselectmenu('리얼클릭 키워드광고','marketing_keyword4.php',$idx[5][4],1);
			noselectmenu('키워드광고 통합대행사패키지','marketing_keyword5.php',$idx[5][5],1);
?>
											<tr>
												<td height="10"></td>
											</tr>
										</table>
									</div>
								</td>
							</tr>
						</table>
						<table width="100%" cellpadding="0" cellspacing="0" id="tblashop7">
							<tr>
								<td height="3" background="images/leftmenu_line.gif"></td>
							</tr>
							<tr>
								<td height="34" style="padding-left:20px;cursor:hand;" onClick="ChangeMenu('shop7');"  class="depth1_noselect"><img src="images/icon_leftmenu.gif" border="0" align="absmiddle"  style="margin-right:4px;margin-bottom:2px">바이럴마케팅</td>
							</tr>
						</table>
						<table width="100%" cellpadding="0" cellspacing="0" id="tblbshop7" style="display:none">
							<tr>
								<td height="3" background="images/leftmenu_line.gif"></td>
							</tr>
							<tr>
								<td height="34" style="padding-left:20px;cursor:hand;" class="depth1_select" onClick="ChangeMenu('shop7');"><img src="images/icon_leftmenu_select.gif"  align="absmiddle" style="margin-right:4px;margin-bottom:2px">바이럴마케팅</td>
							</tr>
						</table>
						<table width="100%" cellpadding="0" cellspacing="0">
							<tr>
								<td>
									<div id="shop7" style="display:none;">
										<table width="100%" cellpadding="0" cellspacing="0">
											<?
			if($menuidx != "shop7") {
				echo "<tr><td height=\"1\" ></td></tr>";
			}
			//noselectmenu('바이럴마케팅','marketing_viral.php',$idx[6][0],0);
			noselectmenu('블로그마케팅','marketing_blog.php',$idx[6][0],0);
			noselectmenu('체험단마케팅','marketing_blog2.php',$idx[6][1],1);
			noselectmenu('SNS마케팅','marketing_sns.php',$idx[6][2],1);
			noselectmenu('카페마케팅','marketing_cafe.php',$idx[6][3],1);
			noselectmenu('커뮤니티마케팅','marketing_comm.php',$idx[6][4],1);
?>
											<tr>
												<td height="10"></td>
											</tr>
										</table>
									</div>
								</td>
							</tr>
						</table>
						<table width="100%" cellpadding="0" cellspacing="0" id="tblashop8">
							<tr>
								<td height="3" background="images/leftmenu_line.gif"></td>
							</tr>
							<tr>
								<td height="34" style="padding-left:20px;cursor:hand;" onClick="ChangeMenu('shop8');"  class="depth1_noselect"><img src="images/icon_leftmenu.gif" border="0" align="absmiddle"  style="margin-right:4px;margin-bottom:2px">디스플레이광고</td>
							</tr>
						</table>
						<table width="100%" cellpadding="0" cellspacing="0" id="tblbshop8" style="display:none">
							<tr>
								<td height="3" background="images/leftmenu_line.gif"></td>
							<tr>
								<td height="34" style="padding-left:20px;cursor:hand;" class="depth1_select" onClick="ChangeMenu('shop8');"><img src="images/icon_leftmenu_select.gif"  align="absmiddle" style="margin-right:4px;margin-bottom:2px">디스플레이광고</td>
							</tr>
						</table>
						<table width="100%" cellpadding="0" cellspacing="0">
							<tr>
								<td>
									<div id="shop8" style="display:none;">
										<table width="100%" cellpadding="0" cellspacing="0">
											<?
			if($menuidx != "shop8") {
				echo "<tr><td height=\"1\" ></td></tr>";
			}
			noselectmenu('디스플레이광고','marketing_ip.php',$idx[7][0],0);
?>
											<tr>
												<td height="10"></td>
											</tr>
										</table>
									</div>
								</td>
							</tr>
						</table>
						<table width="100%" cellpadding="0" cellspacing="0" id="tblashop9">
							<tr>
								<td height="3" background="images/leftmenu_line.gif"></td>
							</tr>
							<tr>
								<td height="34" style="padding-left:20px;cursor:hand;" onClick="ChangeMenu('shop9');"  class="depth1_noselect"><img src="images/icon_leftmenu.gif" border="0" align="absmiddle"  style="margin-right:4px;margin-bottom:2px">모바일광고</td>
							</tr>
						</table>
						<table width="100%" cellpadding="0" cellspacing="0" id="tblbshop9" style="display:none">
							<tr>
								<td height="3" background="images/leftmenu_line.gif"></td>
							<tr>
								<td height="34" style="padding-left:20px;cursor:hand;" class="depth1_select" onClick="ChangeMenu('shop9');"><img src="images/icon_leftmenu_select.gif"  align="absmiddle" style="margin-right:4px;margin-bottom:2px">모바일광고</td>
							</tr>
						</table>
						<table width="100%" cellpadding="0" cellspacing="0">
							<tr>
								<td>
									<div id="shop9" style="display:none;">
										<table width="100%" cellpadding="0" cellspacing="0">
											<?
			if($menuidx != "shop9") {
				echo "<tr><td height=\"1\" ></td></tr>";
			}
			noselectmenu('모바일광고','marketing_mobile.php',$idx[8][0],0);
?>
											<tr>
												<td height="10"></td>
											</tr>
										</table>
									</div>
								</td>
							</tr>
						</table>						
					</TD>
				</TR>
			</TABLE>
		</TD>
	</TR>
</TABLE>
<script>
InitMenu('<?=$menuidx?>');
</script> 
<script type="text/javascript" src="move_menu.js.php"></script>