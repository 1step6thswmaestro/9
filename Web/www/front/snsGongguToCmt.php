<?
$email ="";$mobile ="";
if(strlen($_ShopInfo->getMemid())>0) {
	$sql = "SELECT * FROM tblmember WHERE id='".$_ShopInfo->getMemid()."' ";
	$result = mysql_query($sql);
	if($row = mysql_fetch_object($result)) {
		$email = $row->email;
		if (strlen($row->mobile)>0) $mobile = $row->mobile;
	}
}
?>
<div id="GongguWish" style="postion:absolute;display:none;background:#fff;">
<table cellpadding="0" cellspacing="0" width="420" align="center">
<tr>
	<td colspan="3">
	<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td width="17" align="left"><IMG SRC="../images/design/pop_view_head.gif" WIDTH=17 HEIGHT=44 ALT=""></td>
			<td background="../images/design/pop_view_headbg.gif"><IMG SRC="../images/design/popgonggu_title.gif" WIDTH=151 HEIGHT=43 ALT=""></td>
			<td width="47" align="right"><IMG SRC="../images/design/pop_view_exit.gif" WIDTH=47 HEIGHT=44 ALT="" class="LayerHide" style="cursor:pointer;"></td>
		</tr>
	</table>
	</td>
</tr>
<tr>
	<td background="../images/design/pop_view_leftbg.gif" width="17" height="100%" align="center"></td>
	<td width="100%"  style="padding-top:13px">
		<form name="GongguWishFrm" method="post" >
		<input type="hidden" name="method" value="">
		<input type="hidden" name="c_seq" value="">
		<input type="hidden" name="pcode" value="">
		<input type="hidden" name="etc" value="">
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td height="25"><IMG SRC="../images/design/popgonggu_text1.gif" WIDTH=67 HEIGHT=19 ALT=""></td>
		</tr>
		<tr>
			<td><TEXTAREA rows=6 cols="40" name="comment" class="textarea1" onClick="txtchk(this)">저도 이 제품 공동구매를 희망합니다.</TEXTAREA></td>
		</tr>
		<tr>
			<td height="15"></td>
		</tr>
		<tr>
			<td height="25"><IMG SRC="../images/design/popgonggu_text2.gif" WIDTH=220 HEIGHT=19 ALT=""><a href="../front/mypage_usermodify.php"><IMG SRC="../images/design/btn_infoedit.gif" WIDTH=75 HEIGHT=19 ALT=""></a></td>
		</tr>
		<tr>
			<td>
				<table cellpadding="10" cellspacing="0" width="100%" bgcolor="#F3F3F3">
					<tr>
						<td>
							<table cellpadding="0" cellspacing="0" width="100%">
								<tr>
									<td width="50%" class="table01_con"><input type="checkbox" name="hpno">문자메세지로 알림받기</td>
									<td width="50%" class="table01_con"><input type="checkbox" name="email">이메일로 알림받기</td>
								</tr>
								<tr>
									<td width="50%" style="padding-left:20px;" class="table01_con2e"><b><font color="#FF855F"><?=$mobile ?></font></b></td>
									<td width="50%" style="padding-left:20px;" class="table01_con2e"><b><font color="#FF855F"><?=$email ?></font></b></td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td align="center"><a href="javascript:;" onclick="regTogetherGonggu()"><IMG SRC="../images/design/btn_application.gif" ALT="" vspace="5"></a></td>
		</tr>
		</table>
		</form>
	</td>
	<td background="../images/design/pop_view_rightbg.gif" width="17" height="100%"></td>
</tr>
<tr>
	<td height="9" width="10"><IMG SRC="../images/design/pop_view_bottomleft.gif" width="17" height="16" border="0"></td>
	<td background="../images/design/pop_view_bottombg.gif" height="9" width="729">&nbsp;</td>
	<td height="9" width="11"><IMG SRC="../images/design/pop_view_bottomright.gif" width="17" height="16" border="0"></td>
</tr>
</table>
</div>
<iframe src="" name="ifrmHidden" frameborder="0" width="0" height="0" marginwidth="0" marginheight="0" topmargin="0" scrolling="no"></iframe>
