<?
if($_data->sns_ok == "Y" ){ //&& $setup[sns_state] == "Y"
	// 링크
	$sql = "SELECT code FROM tblsnsboard ";
	$sql.= "WHERE board='".$board."' AND num='".$num."' and id='".$_ShopInfo->getMemid()."' ";
	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		$bodUrl = "http://".$_ShopInfo->getShopurl()."?bcmt=".$row->code;
	}else{
		$cnt = 1;
		while($cnt > 0){
			$tmpid = rand(10000,999999);
			$sql = "SELECT count(1) cnt FROM tblsnsboard WHERE code='".$tmpid."'";
			$result = mysql_query($sql,get_db_conn());
			if($row = mysql_fetch_object($result)) {
				$cnt = (int)$row->cnt;
			}
			mysql_free_result($result);
		}
		$sql = "INSERT tblsnsboard SET ";
		$sql.= "code	= '".$tmpid."', ";
		$sql.= "board	= '".$board."', ";
		$sql.= "num	= '".$num."', ";
		$sql.= "id	= '".$_ShopInfo->getMemid()."' ";
		$result=mysql_query($sql,get_db_conn());
		if($result) {
			$bodUrl = "http://".$_ShopInfo->getShopurl()."?bcmt=".$tmpid;
		}
	}
	$bodsubject = $strSubject;
?>
	<script type="text/javascript" src="../lib/jquery-1.4.2.min.js"></script>
	<script type="text/javascript" src="../lib/sns.js"></script>
<?
	// 링크
	if(strlen($_ShopInfo->getMemid())==0) {
?>
		<script type="text/javascript">
		var bodUrl = "<?=$bodUrl?>";
		var bodsubject = "<?=strip_tags($bodsubject)?>";
		function goFaceBook()
		{
			var href = "http://www.facebook.com/sharer/sharer.php?u=" + encodeURIComponent(bodUrl) + "&t=" + encodeURIComponent(bodsubject);
			var a = window.open(href, 'Facebook', '');
			if (a) {
				a.focus();
			}
		}

		function goTwitter()
		{
			var href = "http://twitter.com/share?text=" + encodeURIComponent(bodsubject) + " " + encodeURIComponent(bodUrl);
			var a = window.open(href, 'Twitter', '');
			if (a) {
				a.focus();
			}
		}
		</script>
<?
		echo "<table cellpadding=\"0\" cellspacing=\"0\" style=\"position:relative;\" width=\"".$setup[board_width]."\">\n";
		echo "<tr>\n";
		echo "	<td><a href=\"javascript:goTwitter();\"><IMG SRC=\"../images/design/icon_twitter_on.gif\" width=\"17\" border=\"0\" id=\"tLoginBtn0\"></a>&nbsp;</td>\n";
		echo "	<td><a href=\"javascript:goFaceBook();\"><IMG SRC=\"../images/design/icon_facebook_on.gif\" width=\"17\"  border=\"0\" id=\"fLoginBtn0\"></a>&nbsp;</td>\n";
		echo "	<td width='100%'></td>";
		echo "</tr>\n";
		echo "</table>\n";
	}else{

		$snsButton ="";
		if(TWITTER_ID !="TWITTER_ID") {
			echo "<input type=\"hidden\" name=\"tLoginBtnChk\" id=\"tLoginBtnChk\">";
			$snsButton .= "<td><INPUT type=\"checkbox\" name=\"send_chk\" id=\"send_chk_t\" value=\"t\" disabled><IMG SRC=\"../images/design/icon_twitter_off.gif\" width=\"17\"  border=\"0\" id=\"tLoginBtn0\" style=\"cursor:pointer\"></td>";
		}

		if(FACEBOOK_ID !="FACEBOOK_ID") {
			echo "<input type=\"hidden\" name=\"fLoginBtnChk\" id=\"fLoginBtnChk\">";
			$snsButton .= "<td><INPUT type=\"checkbox\" name=\"send_chk\" id=\"send_chk_f\" value=\"f\" disabled><IMG SRC=\"../images/design/icon_facebook_off.gif\" width=\"17\"  border=\"0\" id=\"fLoginBtn0\" style=\"cursor:pointer\"></td>";
		}
?>
		<script type="text/javascript">
		var bodUrl = "<?=$bodUrl?>";
		var bodbase_txt = "[<?=$_data->shopname?>-<?=strip_tags($bodsubject)?>]";
		var fbPicture = "<?=$fbPicture?>";
		</script>

		<table cellpadding="0" cellspacing="0" style="position:relative;" width="<?=$setup[board_width]?>">
			<tr>
				<?=$snsButton?>
				<td>&nbsp;</td>
				<td><a href="#" onclick="showDiv_bod('snsSepup');"><img src="../images/design/icon_setup.gif" alt="sns자동연결설정" border="0" align="absmiddle"></a>
					<!--sns 자동연결 설정-->
					<div id="snsSepup" style="position:absolute;z-index:1000;background:#fff;left:35px;top:20px;visibility:hidden;">
					<table cellpadding="0" cellspacing="0" width="150">
					<tr>
						<td colspan="3"><IMG src="../images/design/speech_bubble_top.gif" width="150" height="7"></td>
					</tr>
					<tr>
						<td width="5" background="../images/design/speech_bubble_leftbg.gif"></td>
						<td width="140" class="table01_con">
							<table cellpadding="0" cellspacing="0" width="100%">
							<tr>
								<td class="speechbubble_title"><b>sns자동연결 설정</b></td>
								<td align="right" class="speechbubble_close"><a href="#" onclick="showDiv_bod('snsSepup');"><img src="../images/design/speech_bubble_close.gif"></a></td>
							</tr>
							<tr>
								<td colspan="2" height="10"><img src="../images/design/con_line02.gif" width="140" height="1"></td>
							</tr>
							<tr>
								<td colspan="2" class="speechbubble_con">
									<table cellpadding="0" cellspacing="0" width="100%">
										<tr>
											<td height=23><img src="../images/design/icon_sb_facebook_off.gif" id="fLoginBtn1"></td>
											<td><a href="javascript:changeSnsInfo('f');"><img src="../images/design/btn_connection_off.gif" alt="" id="fLoginBtn2"></td>
										</tr>
										<tr>
											<td height=23><img src="../images/design/icon_sb_twitter_off.gif"  id="tLoginBtn1"></td>
											<td><a href="javascript:changeSnsInfo('t');"><img src="../images/design/btn_connection_off.gif" alt="" id="tLoginBtn2"></td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td colspan="2" height="10"><img src="../images/design/con_line02.gif" width="140" height="1"></td>
							</tr>
							<tr>
								<td colspan="2" class="speechbubble_con">버튼을 클릭하면 연결해제를 할수 있습니다.</td>
							</tr>
							</table>

						</td>
						<td width="5" background="../images/design/speech_bubble_rightbg.gif"></td>
					</tr>
					<tr>
						<td colspan="3"><IMG src="../images/design/speech_bubble_bottom.gif" width="150" height="7"></td>
					</tr>
					</table>
					</div>
					<!--sns 자동연결 설정-->
				</td>
				<td><a href="#" onclick="showDiv_bod('snsHelp');"><img src="../images/design/icon_help.gif" hspace="2" alt="도움말" border="0" align="absmiddle"></a>
					<!--sns보내기(도움말)-->
					<div id="snsHelp" style="position:absolute;z-index:1000;background:#fff;left:55px;top:20px;visibility:hidden;">
					<table cellpadding="0" cellspacing="0" width="150">
						<tr>
							<td colspan="3"><IMG src="../images/design/speech_bubble_top.gif" width="150" height="7"></td>
						</tr>
						<tr>
							<td width="5" background="../images/design/speech_bubble_leftbg.gif"></td>
							<td width="140">
								<table cellpadding="0" cellspacing="0" width="100%">
									<tr>
										<td class="speechbubble_title"><b>도움말</b></td>
										<td align="right" class="speechbubble_close"><a href="#" onclick="showDiv_bod('snsHelp');"><img src="../images/design/speech_bubble_close.gif"></a></td>
									</tr>
									<tr>
										<td colspan="2" class="speechbubble_con">해당 컨텐츠를 내 SNS로 보내 친구들과 공유해보세요.<br><font color="#F8752F">SNS 자동연결 설정 시 한번에 여러개의 SNS로 글을 올릴수 있습니다.</font></td>
									</tr>
								</table>
							</td>
							<td width="5" background="../images/design/speech_bubble_rightbg.gif"></td>
						</tr>
						<tr>
							<td colspan="3"><IMG src="../images/design/speech_bubble_bottom.gif" width="150" height="7"></td>
						</tr>
					</table>
					</div>
					<!--sns보내기(도움말)-->
				</td>
				<td><a href="#" onclick="showDiv_bod('snsSend');"><img src="../images/design/icon_snssend.gif" border="0" align="absmiddle"></a>
					<!--sns 보내기-->
					<div id="snsSend" style="position:absolute;z-index:1000;background:#fff;left:0;top:-130px;visibility:hidden;">
					<table cellpadding="0" cellspacing="0" width="350">
						<tr>
							<td colspan="3"><IMG src="../images/design/speech_bubble_topa.gif" width="352" height="7"></td>
						</tr>
						<tr>
							<td width="5" background="../images/design/speech_bubble_leftbg.gif"></td>
							<td width="342" class="table01_con">


								<table cellpadding="0" cellspacing="0" width="100%">
									<tr>
										<td class="speechbubble_count"><b><font color="#F8752F" id="cmtByte">0</font></b>/100자</td>
										<td align="right" class="speechbubble_close"><a href="#" onclick="showDiv_bod('snsSend');"><img src="../images/design/speech_bubble_close.gif"></a></td>
									</tr>
									<tr>
										<td class="speechbubble_con" colspan="2"><TEXTAREA rows="3" cols="50" name="comment0" id="comment0" class="textarea1" onChange="CheckStrLen('100',this,'top');" onKeyUp="CheckStrLen('100',this,'top');" ></TEXTAREA></td>
									</tr>
									<tr>
										<td  align="center" colspan="2" style="padding-bottom:10px"><a href="#" onclick="snsbodCopy();"><img src="../images/design/icon_snssend.gif"></a><a href="#" onclick="showDiv_bod('snsSend');"><img src="../images/design/btn_cancel01.gif" hspace="4"></a></td>
									</tr>
								</table>

							</td>
							<td width="5" background="../images/design/speech_bubble_rightbg.gif"></td>
						</tr>
						<tr>
							<td colspan="3"><IMG src="../images/design/speech_bubble_bottoma.gif" width="352" height="7"></td>
						</tr>
					</table>
					</div>
					<!--sns 보내기-->
				</td>
				<td width="100%"></td>
			</tr>
		</table>
<?
	}
}
?>
