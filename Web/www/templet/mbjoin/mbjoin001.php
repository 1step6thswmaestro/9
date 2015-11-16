<table border="0" cellpadding="0" cellspacing="0" width="100%">
<tr>
	<td style="padding-left:10;padding-right:10">
	<table border="0" cellpadding="0" cellspacing="0" width="100%">
	<?if($_data->resno_type!="N" && strlen($adultauthid)>0){###### 서신평 아이디가 존재하면 실명인증 안내멘트######?>
	<tr>
		<td>&nbsp;-&nbsp;&nbsp;입력하신 이름과 주민번호의 <font color="#F02800"><b>실명확인</b></font>이 되어야 회원가입을 완료하실 수 있습니다.</td>
	</tr>
	<?}?>
	<tr>
		<td>&nbsp;-&nbsp;&nbsp;<font color="#F02800"><b>(＊)는 필수입력 항목입니다.</b></font></td>
	</tr>
	<tr>
		<td>
		<table cellpadding="0" cellspacing="6"  width="100%">
		<tr>
			<td width="100%" bgcolor="FFFFFF" style="padding:8pt;">
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
			<col width="150" align="right"></col>
			<col width="130" style="padding-left:5px;"></col>
			<col width="100" align="right"></col>
			<col style="padding-left:5px;"></col>
			<tr>
				<td colspan="4" height="2" bgcolor="#E6E6E6"></td>
			</tr>
			<tr>
				<td colspan="4" height="10"></td>
			</tr>
			<tr>
				<td align="left" style="padding-left:14px"><font color="#F02800"><b>＊</b></font><font color="#000000"><b>아이디</b></font></td>
				<td colspan="3"><INPUT type=text name="id" value="<?=$id?>" maxLength="12" style="WIDTH:120px;BACKGROUND-COLOR:#F7F7F7;" class="input"><A href="javascript:idcheck();"><img src="<?=$Dir?>images/common/mbjoin/<?=$_data->design_mbjoin?>/memberjoin_skin1_btn1.gif" border="0" align="absmiddle" hspace="3"></a></td>
			</tr>
			<tr>
				<td height="10" colspan="4" background="<?=$Dir?>images/common/mbjoin/memberjoin_p_skin_line.gif"></td>
			</tr>
			<tr>
				<td align="left" style="padding-left:14px"><font color="#F02800"><b>＊</b></font><font color="#000000"><b>비밀번호</b></font></td>
				<td><INPUT type=password name="passwd1" value="<?=$passwd1?>" maxLength="20" style="WIDTH:120px;BACKGROUND-COLOR:#F7F7F7;" class="input"></td>
				<td><font color="#F02800"><b>＊</b></font><font color="#000000"><b>비밀번호확인</b></font></td>
				<td><INPUT type=password name="passwd2" value="<?=$passwd2?>" maxLength="20" style="WIDTH:120px;BACKGROUND-COLOR:#F7F7F7;" class="input"></td>
			</tr>
			<tr>
				<td HEIGHT="10" colspan="4" background="<?=$Dir?>images/common/mbjoin/memberjoin_p_skin_line.gif"></td>
			</tr>
			<tr>
				<td align="left" style="padding-left:14px"><font color="#F02800"><b>＊</b></font><font color="#000000"><b>이름</b></font></td>
				<td colspan="3"><INPUT type=text name="name" value="<?=$name?>" maxLength="15" style="WIDTH:120px;BACKGROUND-COLOR:#F7F7F7;" class="input"></td>
			</tr>
			<tr>
				<td height="10" colspan="4" background="<?=$Dir?>images/common/mbjoin/memberjoin_p_skin_line.gif"></td>
			</tr>
			<? if($_data->resno_type!="N"){?>
			<tr>
				<td align="left" style="padding-left:14px"><font color="#F02800"><b>＊</b></font><font color="#000000"><b>주민등록번호</b></font></td>
				<td colspan="3"><INPUT type=text name="resno1" value="<?=$resno1?>" maxLength="6" onkeyup="return strnumkeyup2(this);" style="WIDTH:50px;BACKGROUND-COLOR:#F7F7F7;" class="input"> - <INPUT type=password name="resno2" value="<?=$resno2?>" maxLength="7" onkeyup="return strnumkeyup2(this);" style="WIDTH:58px;BACKGROUND-COLOR:#F7F7F7;" class="input"></td>
			</tr>
			<tr>
				<td height="10" colspan="4" background="<?=$Dir?>images/common/mbjoin/memberjoin_p_skin_line.gif"></td>
			</tr>
			<? }?>
			<? if($extconf['reqgender'] != 'H'){?>
			<tr>
				<td align="left" style="padding-left:14px"><? if($extconf['reqgender'] == 'Y'){?><font color="#F02800"><b>＊</b></font><?}?><font color="#000000"><b>성별</b></font></td>
				<td colspan="3"><INPUT type="radio" name="gender" value="1">남자 / <INPUT type="radio" name="gender" value="2">여자</td>
			</tr>
			<tr>
				<td height="10" colspan="4" background="<?=$Dir?>images/common/mbjoin/memberjoin_p_skin_line.gif"></td>
			</tr>
			<? }?>
			<? if($extconf['reqbirth'] != 'H'){?>
			<tr>
				<td align="left" style="padding-left:14px"><? if($extconf['reqbirth'] == 'Y'){?><font color="#F02800"><b>＊</b></font><?}?><font color="#000000"><b>생년월일</b></font></td>
				<td colspan="3"><INPUT type="text" name="birth" value="" maxLength="10" style="WIDTH:100px; BACKGROUND-COLOR:#F7F7F7;" class="input"> ( ex : <?=date('Y-m-d')?> )</td>
			</tr>
			<tr>
				<td height="10" colspan="4" background="<?=$Dir?>images/common/mbjoin/memberjoin_p_skin_line.gif"></td>
			</tr>
			<? }?>
			<tr>
				<td align="left" style="padding-left:14px"><font color="#F02800"><b>＊</b></font><font color="#000000"><b>이메일</b></font></td>
				<td colspan="3"><INPUT type=text name="email" value="<?=$email?>" maxLength="100" style="WIDTH:30%;BACKGROUND-COLOR:#F7F7F7;" class="input"><A href="javascript:mailcheck();"><img src="<?=$Dir?>images/common/mbjoin/<?=$_data->design_mbjoin?>/memberjoin_skin1_btnMail.gif" border="0" align="absmiddle" hspace="3" alt="메일 중복 검색"></a></td>
			</tr>
			<tr>
				<td height="10" colspan="4" background="<?=$Dir?>images/common/mbjoin/memberjoin_p_skin_line.gif"></td>
			</tr>
			<tr>
				<td align="left" style="padding-left:14px"><font color="#F02800"><b>＊</b></font><font color="#000000"><b>메일정보 수신여부</b></font></td>
				<td colspan="3"><INPUT type=radio name="news_mail_yn" value="Y" id="idx_news_mail_yn0" <?if($news_mail_yn=="Y")echo"checked";?> style="BORDER:none;"><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand" onmouseout="style.textDecoration='none'" for="idx_news_mail_yn0">받습니다.</LABEL> <INPUT type=radio name="news_mail_yn" value="N" id="idx_news_mail_yn1" <?if($news_mail_yn=="N")echo"checked";?> style="BORDER:none;"><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand" onmouseout="style.textDecoration='none'" for="idx_news_mail_yn1">받지 않습니다.</LABEL></td>
			</tr>
			<tr>
				<td height="10" colspan="4" background="<?=$Dir?>images/common/mbjoin/memberjoin_p_skin_line.gif"></td>
			</tr>
			<tr>
				<td align="left" style="padding-left:14px"><font color="#F02800"><b>＊</b></font><font color="#000000"><b>SMS정보 수신여부</b></font></td>
				<td colspan="3"><INPUT type=radio name="news_sms_yn" value="Y" id="idx_news_sms_yn0" <?if($news_sms_yn=="Y")echo"checked";?> style="BORDER:none;"><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand" onmouseout="style.textDecoration='none'" for="idx_news_sms_yn0">받습니다.</LABEL> <INPUT type=radio name="news_sms_yn" value="N" id="idx_news_sms_yn1" <?if($news_sms_yn=="N")echo"checked";?> style="BORDER:none;"><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand" onmouseout="style.textDecoration='none'" for="idx_news_sms_yn1">받지 않습니다.</LABEL></td>
			</tr>
			<tr>
				<td height="10" colspan="4" background="<?=$Dir?>images/common/mbjoin/memberjoin_p_skin_line.gif"></td>
			</tr>
			<tr>
				<td align="left" style="padding-left:14px"><font color="#F02800"><b>＊</b></font><font color="#000000"><b>집전화</b></font></td>
				<td colspan="3"><INPUT type=text name="home_tel" value="<?=$home_tel?>" maxLength="15" style="WIDTH:120px;BACKGROUND-COLOR:#F7F7F7;" class="input"></td>
			</tr>
			<tr>
				<td height="10" colspan="4" background="<?=$Dir?>images/common/mbjoin/memberjoin_p_skin_line.gif"></td>
			</tr>
			<tr>
				<td align="left" style="padding-left:14px"><font color="#F02800"><b>＊</b></font><font color="#000000"><b>집주소</b></font></td>
				<td colspan="3">
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td><INPUT type=text name="home_post1" value="<?=$home_post1?>" readOnly style="WIDTH:50px;BACKGROUND-COLOR:#F7F7F7;" class="input"><a href="javascript:f_addr_search('form1','home_post','home_addr1',2);"><img src="<?=$Dir?>images/common/mbjoin/<?=$_data->design_mbjoin?>/memberjoin_skin1_btn2.gif" border="0" align="absmiddle" hspace="3"></a></td>
				</tr>
				<tr>
					<td><INPUT type=text name="home_addr1" value="<?=$home_addr1?>" maxLength="100" readOnly style="WIDTH:80%;BACKGROUND-COLOR:#F7F7F7;" class="input"></td>
				</tr>
				<tr>
					<td><INPUT type=text name="home_addr2" value="<?=$home_addr2?>" maxLength="100" style="WIDTH:80%;BACKGROUND-COLOR:#F7F7F7;" class="input"></td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td height="10" colspan="4" background="<?=$Dir?>images/common/mbjoin/memberjoin_p_skin_line.gif"></td>
			</tr>
			<tr>
				<td align="left" style="padding-left:14px"><font color="#F02800"><b>＊</b></font><font color="#000000"><b>비상전화(휴대폰)</b></font></td>
				<td colspan="3"><INPUT type=text maxLength="15" name="mobile" value="<?=$mobile?>" style="WIDTH:120px;BACKGROUND-COLOR:#F7F7F7;" class="input"></td>
			</tr>
			<tr>
				<td height="10" colspan="4" background="<?=$Dir?>images/common/mbjoin/memberjoin_p_skin_line.gif"></td>
			</tr>
			<tr>
				<td align="left" style="padding-left:27px"><font color="#000000"><b>회사주소</b></font></td>
				<td colspan="3">
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td><INPUT type=text name="office_post1" value="<?=$office_post1?>" readOnly style="WIDTH:50px;BACKGROUND-COLOR:#F7F7F7;" class="input"><a href="javascript:f_addr_search('form1','office_post','office_addr1',2);"><img src="<?=$Dir?>images/common/mbjoin/<?=$_data->design_mbjoin?>/memberjoin_skin1_btn2.gif" border="0" align="absmiddle" hspace="3"></a></td>
				</tr>
				<tr>
					<td><INPUT type=text name="office_addr1" value="<?=$office_addr1?>" maxLength="100" readOnly style="WIDTH:80%;BACKGROUND-COLOR:#F7F7F7;" class="input"></td>
				</tr>
				<tr>
					<td><INPUT type=text name="office_addr2" value="<?=$office_addr2?>" maxLength="100" style="WIDTH:80%;BACKGROUND-COLOR:#F7F7F7;" class="input"></td>
				</tr>
				</table>
				</td>
			</tr>
<?
if($recom_ok=="Y") {
	if($recom_url_ok=="Y" && $_COOKIE['url_id'] != ""){
		if($_data->recom_addreserve >0){
?>
			<tr>
				<td height="10" colspan="4" background="<?=$Dir?>images/common/mbjoin/memberjoin_p_skin_line.gif"></td>
			</tr>
			<tr>
				<td align="left" style="padding-left:27px"><font color="#000000"><b>추가적립금</b></font></td>
				<td colspan="3"><b><?=$_COOKIE['url_name']?>(<?=$_COOKIE['url_id']?>)</b>님의 초대로 <b><font color="#FD9999"> 적립금 <?=$_data->recom_addreserve?>원</font></b>을 추가 적립해 드립니다.<input type="hidden" name="rec_id" value="<?=$_COOKIE['url_id']?>"></td>
			</tr>
<?
		}else{
?>
			<tr>
				<td height="10" colspan="4" background="<?=$Dir?>images/common/mbjoin/memberjoin_p_skin_line.gif"></td>
			</tr>
			<tr>
				<td align="left" style="padding-left:27px"><font color="#000000"><b>추천인</b></font></td>
				<td colspan="3"><b><?=$_COOKIE['url_name']?>(<?=$_COOKIE['url_id']?>)</b>님의 초대를 받았습니다.<input type="hidden" name="rec_id" value="<?=$_COOKIE['url_id']?>" style="WIDTH:120px;BACKGROUND-COLOR:#F7F7F7;"></td>
			</tr>
<?
		}
	}else{
?>
			<tr>
				<td height="10" colspan="4" background="<?=$Dir?>images/common/mbjoin/memberjoin_p_skin_line.gif"></td>
			</tr>
			<tr>
				<td align="left" style="padding-left:27px"><font color="#000000"><b>추천ID</b></font></td>
				<td colspan="3"><INPUT type="text" name="rec_id" maxLength="12"  value="<?=$rec_id?>" style="WIDTH:120px;BACKGROUND-COLOR:#F7F7F7;" class="input"></td>
			</tr>
<?
	}
}
?>

	<?
				if(strlen($straddform)>0) {
					echo $straddform;
				}
	?>
			</table>
			</td>
		</tr>
			<tr>
				<td colspan="4" height="10"></td>
			</tr>
			<tr>
				<td colspan="4" height="2" bgcolor="#E6E6E6"></td>
			</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td height="5"></td>
	</tr>
	<tr>
		<td align="center"><a href="javascript:CheckForm();"><img src="<?=$Dir?>images/common/mbjoin/<?=$_data->design_mbjoin?>/memberjoin_skin1_btn3.gif" border="0"></a><a href="javascript:history.go(-1);";><img src="<?=$Dir?>images/common/mbjoin/<?=$_data->design_mbjoin?>/memberjoin_skin1_btn4.gif" border="0" hspace="3"></a></td>
	</tr>
	<tr>
		<td height="40"></td>
	</tr>
	</table>
	</td>
</tr>
</table>