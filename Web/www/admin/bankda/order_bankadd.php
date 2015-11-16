
<script language="JavaScript">

//입금계좌 등록
function addAccount(){
	<? if($svinfo['ableaccount'] <= count($svinfo['accounts'])){ ?>
		alert("사용가능한 계좌를 모두 등록 하셨습니다.");
		return;
	<? }else{ ?>
	/*
		window.open("about:blank","addAccount_pop","height=520,width=650,scrollbars=yes");
		document.userinfo.action = "/admin/bankda/addAccount.php";
		document.userinfo.target = "addAccount_pop";
		document.userinfo.method = "post";
		document.userinfo.submit();*/
		document.modaccount.job_type.value = "add_account";
document.modaccount.account_num.value = '';
	window.open("about:blank","modAccount","scrollbars=yes,width=700,height=400");
	document.modaccount.submit();
	<? } ?>
}

function modAccount(account_num) {
	document.modaccount.job_type.value = "mod_account";
	document.modaccount.account_num.value = account_num;
	window.open("about:blank","modAccount","scrollbars=yes,width=700,height=600");
	document.modaccount.submit();
}
//계좌 삭제
function accountDel(account_num){
	document.modaccount.job_type.value = "del_account";
	document.modaccount.account_num.value = account_num;
	window.open("about:blank","modAccount","scrollbars=yes,width=700,height=600");
	document.modaccount.submit();
	
}
</script>
<form name="userinfo">
<?
	foreach($info as $key=>$val){ 
?>
	<input type="hidden" id="<?=$key?>" name="<?=$key?>" value="<?=$val?>" />
<? 
	}
 ?>

</form>
<form name="modaccount"method="post" action="https://ssl.bankda.com/partnership/user/user_login.php" target="modAccount">
<input type="hidden" name="job_type" value="">
<input type="hidden" name="account_num" value="">
<input type="hidden" name="partner_id" value="getmall">
<input type="hidden" name="service_type" value="standard">
<input type="hidden" name="user_id" value="<?=$svinfo['user_id']?>">
<input type="hidden" name="user_pw" value="<?=$svinfo['user_pw']?>">
</form>
<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
	<tr>
		<td>
			<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/order_bankm_title.gif" ALT="무통장 입금확인"></TD>
				</tr>
				<tr>
					<TD width="100%" background="images/title_bg.gif" height="21"></TD>
				</TR>
			</TABLE>
		</td>
	</tr>
	<tr>
		<td height="3"></td>
	</tr>
	<tr>
		<td style="padding-bottom:3pt;">
			<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/distribute_01.gif"></TD>
					<TD COLSPAN=2 background="images/distribute_02.gif"></TD>
					<TD><IMG SRC="images/distribute_03.gif"></TD>
				</TR>
				<TR>
					<TD background="images/distribute_04.gif"><IMG SRC="images/distribute_04.gif" ></TD>
					<TD class="notice_blue"><IMG SRC="images/distribute_img.gif" ></TD>
					<TD width="100%" class="notice_blue"><p>무통장 입금계좌를 등록 및 수정하실 수 있습니다.</p></TD>
					<TD background="images/distribute_07.gif"><IMG SRC="images/distribute_07.gif" ></TD>
				</TR>
				<TR>
					<TD><IMG SRC="images/distribute_08.gif"></TD>
					<TD COLSPAN=2 background="images/distribute_09.gif"></TD>
					<TD><IMG SRC="images/distribute_10.gif"></TD>
				</TR>
			</TABLE>
		</td>
	</tr>
	<tr>
		<td height=20></td>
	</tr>
	<tr>
		<td>
			<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="bankda/images/order_bankm_stitle2.gif" ALT="입금계좌 등록 및 서비스 이용정보"></TD>
					<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif"></TD>
					<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></TD>
				</TR>
			</TABLE>
		</td>
	</tr>
	<tr>
		<td height=3></td>
	</tr>
	<tr>
		<td>
			<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<col class=cellC><col class=cellL><col class=cellC><col class=cellL width=35%>
				<TR>
					<TD background="images/table_top_line.gif" width="153"><img src="images/table_top_line.gif"></TD>
					<TD colspan="3" background="images/table_top_line.gif" width="607" ></TD>
				</TR>

				<tr>
					<td class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">서비스 유형 / 기간</td>
					<td colspan="3" class="td_con1" >Standard / <span id="period"><?=substr($svinfo['stdate'],0,10)?>~<?=substr($svinfo['enddate'],0,10)?></span></td>
				</tr>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"><img src="images/table_con_line.gif" width="4" height="1" border="0"></TD>
				</TR>
				<tr>
					<td class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">계좌수( 등록 / 신청 )</td>
					<td colspan="3" class="td_con1" ><span id="use_account"><?=count($svinfo['accounts'])?></span> 개 / <span id="able_account"><?=$svinfo['ableaccount']?></span> 개</td>
				</tr>
				<TR>
					<TD background="images/table_top_line.gif" width="153"><img src="images/table_top_line.gif"></TD>
					<TD  colspan="3"  background="images/table_top_line.gif" width="607"></TD>
				</TR>
			</table>
		</td>
	</tr>
	<tr><td height="30"></td></tr>
	<tr>
		<td align="right"><? if($svinfo['ableaccount'] > count($svinfo['accounts'])){ ?><a href="javascript:addAccount();"><img src="bankda/images/botteon_add.gif" border="0"></a><? } ?></td>
	</tr>
	<tr><td height="10"></td></tr>
	<tr>
		<td>
			
			<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0 >
				<TR>
					<TD colspan="7" background="images/table_top_line.gif"></TD>
				</TR>
				<TR height=32>									
										
					<TD class="table_cell5" style="width:100px" align="center">은행명</TD>
					<TD class="table_cell6" style="width:100px" align="center">구분</TD>
					<TD class="table_cell6" style="width:140px" align="center">계좌번호</TD>
					<TD class="table_cell6" align="center">계좌상태</TD>
					<TD class="table_cell6" style="width:140px" align="center">최종조회</TD>
					<TD class="table_cell6"  style="width:140px" align="center">등록일</TD>
					<TD class="table_cell6" style="width:180px;" align="center">관리</TD>
				</TR>
				<TR>
					<TD colspan="7" background="images/table_con_line.gif"></TD>
				</TR>
				<?
				for($i=0;$i<count($svinfo['accounts']);$i++){
					$bkinfo = &$svinfo['accounts'][$i];
				?>				
				<tr>											
					<td class="td_con1b" style="text-align:center"><?=$bkinfo['bkname']?></td>
					<td class="td_con1" style="text-align:center"><?=$bkinfo['accounttype']?></td>
					<td class="td_con1" style="text-align:center"><?=$bkinfo['bkacctno']?>&nbsp;</td>
					<td class="td_con1" >[<?=$bkinfo['acttag']?>] <?=$bkinfo['act_status']?></td>											
					<td class="td_con1" style="text-align:center"><?=$bkinfo['last_scraping_dtm']?></td>
					<td class="td_con1" style="text-align:center"><?=$bkinfo['regdate']?></td>
					<TD class="td_con1" align="center"><a href="javascript:modAccount('<?=$bkinfo['bkacctno']?>');"><img src="images/btn_edit.gif" border="0" hspace="2" alt="수정"></a><a href="javascript:accountDel('<?=$bkinfo['bkacctno']?>');"><img src="images/btn_del.gif" border="0" alt="삭제"></a></TD>
				</tr>										
				<TR>
					<TD colspan="7" background="images/table_top_line.gif"></TD>
				</TR>
				<?
				}	
				?>
				
			</TABLE>
		</td>
	</tr>
	
	<tr>
		<td height=20></td>
	</tr>
	<tr>
		<td>
			<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/manual_top1.gif" WIDTH=15 HEIGHT=45 ALT=""></TD>
					<TD><IMG SRC="images/manual_title.gif" WIDTH=113 HEIGHT=45 ALT=""></TD>
					<TD width="100%" background="images/manual_bg.gif"></TD>
					<TD background="images/manual_bg.gif"></TD>
					<TD><IMG SRC="images/manual_top2.gif" WIDTH=18 HEIGHT=45 ALT=""></TD>
				</TR>
				<TR>
					<TD background="images/manual_left1.gif"></TD>
					<TD COLSPAN=3 width="100%" valign="top" bgcolor="white" style="padding-top:8pt; padding-bottom:8pt; padding-left:4pt;">
						<table cellpadding="0" cellspacing="0" width="100%">
							<tr>
								<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
								<td><span class="font_dotline">입금계좌 추가</span></td>
							</tr>
							<tr>
								<td width="20" align="right">&nbsp;</td>
								<td width="701" class="space_top">- 입금계좌 추가시 계좌번호는 "-" 를 빼고 입력하시면 됩니다.</td>
							</tr>
							<tr>
								<td width="20" align="right">&nbsp;</td>
								<td width="701" class="space_top">- 은행정보 목록에 표기된 은행들만 등록가능 합니다.</td>
							</tr>
						</table>
					</TD>
					<TD background="images/manual_right1.gif"></TD>
				</TR>
				<TR>
					<TD><IMG SRC="images/manual_left2.gif" WIDTH=15 HEIGHT=8 ALT=""></TD>
					<TD COLSPAN=3 background="images/manual_down.gif"></TD>
					<TD><IMG SRC="images/manual_right2.gif" WIDTH=18 HEIGHT=8 ALT=""></TD>
				</TR>
			</TABLE>
		</td>
	</tr>
	<tr>
		<td height="50"></td>
	</tr>
</table>
<SCRIPT type="text/javascript">
<!--
// document.getElementById("user_name").value = "<?=$info['user_name']?>";
//-->
</SCRIPT>
<?=$onload?>