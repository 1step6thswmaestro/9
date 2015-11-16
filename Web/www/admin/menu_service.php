
<TABLE WIDTH="100%" BORDER="0" CELLPADDING="0" CELLSPACING="0">
<TR>
	<TD height="68" align="right" valign="top" background="images/service_leftmenu_title.gif"><a href="javascript:scrollMove(0);"><img src="images/leftmenu_stop.gif" border="0" id="menu_pix"></a><a href="javascript:scrollMove(1);"><img src="images/leftmenu_trans.gif" border="0" id="menu_scroll"></a></TD>
</TR>
<TR>
	<TD  background="images/leftmenu_bg.gif">
	<TABLE WIDTH="100%" BORDER="0" CELLPADDING="0" CELLSPACING="0">
	<col width="16"></col>
	<col></col>
	<col width="16"></col>
	<TR>
		<TD valign="top">
		<table width="100%" cellpadding="0" cellspacing="0" >
		<tr>
			<td height="34" style="padding-left:20px;cursor:hand;"  class="depth1_noselect"><img src="images/icon_leftmenu.gif" border="0" align="absmiddle"  style="margin-right:4px;margin-bottom:2px"><a href="service_domain.php"><span class="depth1_noselect">도메인</span></a></td>
		</tr>
		</table>
		<table width="100%" cellpadding="0" cellspacing="0" id="tblashop2">
		<tr><td height="3" background="images/leftmenu_line.gif"></td>
		</tr>
		<tr>
			<td height="34" style="padding-left:20px;cursor:hand;" onClick="ChangeMenu('shop2');"  class="depth1_noselect"><img src="images/icon_leftmenu.gif" border="0" align="absmiddle"  style="margin-right:4px;margin-bottom:2px"><a href="service_hosting.php"><span class="depth1_noselect">호스팅</span></a></td>
		</tr>
		</table>
		<table width="100%" cellpadding="0" cellspacing="0" id="tblashop3">
		<tr><td height="3" background="images/leftmenu_line.gif"></td>
		<tr>
			<td height="34" style="padding-left:20px;cursor:hand;" onClick="ChangeMenu('shop3');"  class="depth1_noselect"><img src="images/icon_leftmenu.gif" border="0" align="absmiddle"  style="margin-right:4px;margin-bottom:2px"><a href="service_payment.php"><span class="depth1_noselect">통합전자결제서비스</span></a></td>
		</tr>
		</table>
		<table width="100%" cellpadding="0" cellspacing="0" id="tblashop4">
		<tr><td height="3" background="images/leftmenu_line.gif"></td></tr>
		<tr>
			<td height="34" style="padding-left:20px;cursor:hand;"   class="depth1_noselect"><img src="images/icon_leftmenu.gif" border="0" align="absmiddle"  style="margin-right:4px;margin-bottom:2px"><a href="service_sms.php"><span class="depth1_noselect">SMS서비스</span></a></td>
		</tr>
		</table>
		<table width="100%" cellpadding="0" cellspacing="0" id="tblashop4">
		<tr><td height="3" background="images/leftmenu_line.gif"></td></tr>
		<tr>
			<td height="34" style="padding-left:20px;cursor:hand;"   class="depth1_noselect"><img src="images/icon_leftmenu.gif" border="0" align="absmiddle"  style="margin-right:4px;margin-bottom:2px"><a href="service_ssl.php"><span class="depth1_noselect">보안인증</span></a></td>
		</tr>
		</table>
		<table width="100%" cellpadding="0" cellspacing="0" id="tblashop4">
		<tr><td height="3" background="images/leftmenu_line.gif"></td></tr>
		<tr>
			<td height="34" style="padding-left:20px;cursor:hand;"   class="depth1_noselect"><img src="images/icon_leftmenu.gif" border="0" align="absmiddle"  style="margin-right:4px;margin-bottom:2px"><a href="service_ipin.php"><span class="depth1_noselect">IPIN</span></a></td>
		</tr>
		</table>
		<table width="100%" cellpadding="0" cellspacing="0" id="tblashop4">
		<tr><td height="3" background="images/leftmenu_line.gif"></td></tr>
		<tr>
			<td height="34" style="padding-left:20px;cursor:hand;"   class="depth1_noselect"><img src="images/icon_leftmenu.gif" border="0" align="absmiddle"  style="margin-right:4px;margin-bottom:2px"><a href="service_log.php"><span class="depth1_noselect">로그분석서비스</span></a></td>
		</tr>
		</table>
		<table width="100%" cellpadding="0" cellspacing="0" id="tblashop4">
		<tr><td height="3" background="images/leftmenu_line.gif"></td></tr>
		<tr>
			<td height="34" style="padding-left:20px;cursor:hand;"   class="depth1_noselect"><img src="images/icon_leftmenu.gif" border="0" align="absmiddle"  style="margin-right:4px;margin-bottom:2px"><a href="service_parcelservice.php"><span class="depth1_noselect">택배연동서비스</span></a></td>
		</tr>
		</table>
<? /*
		<table width="100%" cellpadding="0" cellspacing="0" id="tblashop4">
		<tr><td height="3" background="images/leftmenu_line.gif"></td></tr>

		<tr>
			<td height="34" style="padding-left:20px;cursor:hand;"   class="depth1_noselect"><img src="images/icon_leftmenu.gif" border="0" align="absmiddle"  style="margin-right:4px;margin-bottom:2px"><a href="service_mail.php"><span class="depth1_noselect">대량메일</span></a></td>
		</tr>
		</table>
		*/ ?>
		<?
		$pfile = substr(strrchr(getenv("SCRIPT_NAME"),"/"),1);
		if(!function_exists('noselectmenu')){
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
		}
		?>
		<script language="javascript" type="text/javascript">
			function toggleMailSub(){
				var tel = document.getElementById('mailSubDiv');
				if(tel.style.display == 'none'){
					document.getElementById('mailIcon').src='images/icon_leftmenu_select.gif';
					tel.style.display = '';
				}else{
					document.getElementById('mailIcon').src='images/icon_leftmenu.gif';
					tel.style.display = 'none';
				}
			}
		</script>
		<table width="100%" cellpadding="0" cellspacing="0" id="tblbshop10">
			<tr>
				<td height="3" background="images/leftmenu_line.gif"></td>
			<tr>
				<? if($pfile == 'bulkmail.php'){ ?>
				<td height="34" style="padding-left:20px;cursor:hand;" class="depth1_select"><img id="mailIcon" src="images/icon_leftmenu_select.gif"  align="absmiddle" style="margin-right:4px;margin-bottom:2px">대량메일발송</td>
				<? }else{ ?>
				<td height="34" style="padding-left:20px;cursor:hand;" class="depth1_select" onClick="toggleMailSub();"><img id="mailIcon" src="images/icon_leftmenu.gif"  align="absmiddle" style="margin-right:4px;margin-bottom:2px">대량메일발송</td>
				<? } ?>
			</tr>
		</table>
		<table width="100%" cellpadding="0" cellspacing="0">
			<tr>
				<td>
					<div id="mailSubDiv" style="display:<?=(($pfile == 'bulkmail.php')?'':'none')?>;">
						<table width="100%" cellpadding="0" cellspacing="0">
<?
						noselectmenu('서비스 안내 및 신청','bulkmail.php',($pfile == 'bulkmail.php' && !in_array($_REQUEST['act'],array('send','group','result'))?'YES':''),0);
						noselectmenu('메일 보내기','bulkmail.php?act=send',(($pfile == 'bulkmail.php' &&  $_REQUEST['act'] =='send')?'YES':''),1);
						noselectmenu('발송그룹관리','bulkmail.php?act=group',(($pfile == 'bulkmail.php' &&  $_REQUEST['act'] =='group')?'YES':''),1);
						noselectmenu('발송결과분석','bulkmail.php?act=result',(($pfile == 'bulkmail.php' &&  $_REQUEST['act'] =='result')?'YES':''),2);
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
		<tr><td height="3" background="images/leftmenu_line.gif"></td></tr>
		<tr>
			<td height="34" style="padding-left:20px;cursor:hand;"   class="depth1_noselect"><img src="images/icon_leftmenu.gif" border="0" align="absmiddle"  style="margin-right:4px;margin-bottom:2px"><a href="service_deposit.php"><span class="depth1_noselect">무통장입금확인서비스</span></a></td>
		</tr>
		</table>
		<table width="100%" cellpadding="0" cellspacing="0" id="tblashop4">
		<tr><td height="3" background="images/leftmenu_line.gif"></td></tr>
		<tr>
			<td height="34" style="padding-left:20px;cursor:hand;"   class="depth1_noselect"><img src="images/icon_leftmenu.gif" border="0" align="absmiddle"  style="margin-right:4px;margin-bottom:2px"><a href="service_openmarket.php"><span class="depth1_noselect">오픈마켓/종합몰연동</span></a></td>
		</tr>
		</table>
		<table width="100%" cellpadding="0" cellspacing="0" id="tblashop4">
		<tr><td height="3" background="images/leftmenu_line.gif"></td></tr>
		<tr>
			<td height="34" style="padding-left:20px;cursor:hand;"   class="depth1_noselect"><img src="images/icon_leftmenu.gif" border="0" align="absmiddle"  style="margin-right:4px;margin-bottom:2px"><a href="service_sales_admin.php"><span class="depth1_noselect">온.오프라인통합관리</span></a></td>
		</tr>
		</table>
		<table width="100%" cellpadding="0" cellspacing="0" id="tblashop4">
		<tr><td height="3" background="images/leftmenu_line.gif"></td></tr>
		<tr>
			<td height="34" style="padding-left:20px;cursor:hand;"   class="depth1_noselect"><img src="images/icon_leftmenu.gif" border="0" align="absmiddle"  style="margin-right:4px;margin-bottom:2px"><a href="service_buyingservice.php"><span class="depth1_noselect">구매대행상품수집연동</span></a></td>
		</tr>
		</table>
		<table width="100%" cellpadding="0" cellspacing="0" id="tblashop4">
		<tr><td height="3" background="images/leftmenu_line.gif"></td></tr>
		<tr>
			<td height="34" style="padding-left:20px;cursor:hand;"   class="depth1_noselect"><img src="images/icon_leftmenu.gif" border="0" align="absmiddle"  style="margin-right:4px;margin-bottom:2px"><a href="service_relocation.php"><span class="depth1_noselect">타사이전서비스</span></a></td>
		</tr>
		</table>
		<table width="100%" cellpadding="0" cellspacing="0" id="tblashop4">
		<tr><td height="3" background="images/leftmenu_line.gif"></td></tr>
		<tr>
			<td height="34" style="padding-left:20px;cursor:hand;"   class="depth1_noselect"><img src="images/icon_leftmenu.gif" border="0" align="absmiddle"  style="margin-right:4px;margin-bottom:2px"><a href="service_groupware.php"><span class="depth1_noselect">그룹웨어</span></a></td>
		</tr>
		</table>
		<table width="100%" cellpadding="0" cellspacing="0" id="tblashop4">
		<tr><td height="3" background="images/leftmenu_line.gif"></td></tr>
		<tr>
			<td height="34" style="padding-left:20px;cursor:hand;"   class="depth1_noselect"><img src="images/icon_leftmenu.gif" border="0" align="absmiddle"  style="margin-right:4px;margin-bottom:2px"><a href="service_070.php"><span class="depth1_noselect">070인터넷전화</span></a></td>
		</tr>
		</table>
		<table width="100%" cellpadding="0" cellspacing="0" id="tblashop4">
		<tr><td height="3" background="images/leftmenu_line.gif"></td></tr>
		<tr>
			<td height="34" style="padding-left:20px;cursor:hand;"   class="depth1_noselect"><img src="images/icon_leftmenu.gif" border="0" align="absmiddle"  style="margin-right:4px;margin-bottom:2px"><a href="service_foundation.php"><span class="depth1_noselect">창업패키지서비스</span></a></td>
		</tr>
		</table>

		</TD>
	</TR>
	</TABLE>
	</TD>
</TR>
</TABLE>


