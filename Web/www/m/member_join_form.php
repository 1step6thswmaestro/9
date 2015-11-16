<table cellpadding="0" cellspacing="0"  width="100%">
	<?if($_data->resno_type!="N" && strlen($adultauthid)>0){###### 서신평 아이디가 존재하면 실명인증 안내멘트######?>
		<tr>
			<td>- 입력하신 이름과 주민번호의 <font color="#F02800"><b>실명확인</b></font>이 되어야 회원가입을 완료하실 수 있습니다.</td>
		</tr>
	<?}?>
	<tr><td valign="bottom" style="height:30px; padding-bottom:5px; padding-left:10px;"><font color="#F02800"><b>＊는 필수입력 항목입니다.</b></font></td></tr>
	<tr>
		<td valign="bottom" style="height:30px; padding-bottom:5px; padding-left:10px;">
			<p>＊모바일에서 회원가입을 할 경우 간편가입으로</p>
			<p style="margin-left:13px;">필수 입력요소 외 주소, 연락처 등의 정보는</p>
			<p style="margin-left:13px;">최초주문시 주문자 정보로 자동 입력 됩니다.</p>
		</td>
	</tr>
	<tr>
		<td>
			<table border="0" cellpadding="0" cellspacing="0" width="100%" class="memJoinForm">
				<col width="110"></col>
				<col width=""></col>
				<tr>
					<th><font color="#F02800"><b>＊</b></font><font color="#000000"><b>아이디</b></font></th>
					<td><INPUT type=text name="id" value="<?=$id?>" maxLength="12" style="WIDTH:90px; BACKGROUND-COLOR:#F7F7F7;" class="input"> <A class="button black small btn_memjoin" href="javascript:idcheck();">중복확인</a></td>
				</tr>
				<tr>
					<th><font color="#F02800"><b>＊</b></font><font color="#000000"><b>비밀번호</b></font></th>
					<td><INPUT type=password name="passwd1" value="<?=$passwd1?>" maxLength="20" style="WIDTH:90px; BACKGROUND-COLOR:#F7F7F7;" class="input"></td>
				</tr>
				<tr>
					<th><font color="#F02800"><b>＊</b></font><font color="#000000"><b>비밀번호확인</b></font></th>
					<td><INPUT type=password name="passwd2" value="<?=$passwd2?>" maxLength="20" style="WIDTH:90px; BACKGROUND-COLOR:#F7F7F7;" class="input"></td>
				</tr>
				<tr>
					<th><font color="#F02800"><b>＊</b></font><font color="#000000"><b>이름</b></font></th>
					<td><INPUT type=text name="name" value="<?=$name?>" maxLength="15" style="WIDTH:90px; BACKGROUND-COLOR:#F7F7F7;" class="input"></td>
				</tr>
<?if($_data->resno_type!="N"){?>
				<tr>
					<th><font color="#F02800"><b>＊</b></font><font color="#000000"><b>주민등록번호</b></font></th>
					<td><INPUT type=text name="resno1" value="<?=$resno1?>" maxLength="6" onkeyup="return strnumkeyup2(this);" style="WIDTH:50px; BACKGROUND-COLOR:#F7F7F7;" class="input"> - <INPUT type=password name="resno2" value="<?=$resno2?>" maxLength="7" onkeyup="return strnumkeyup2(this);" style="WIDTH:58px;BACKGROUND-COLOR:#F7F7F7;" class="input"></td>
				</tr>
<?}?>
			<? if($ext_cont['reqgender'] != 'H'){?>
				<tr>
					<th><? if($ext_cont['reqgender'] == 'Y'){?><font color="#F02800"><b>＊</b></font><?}?><font color="#000000"><b>성별</b></font></th>
					<td><INPUT type="radio" name="gender" id="gender_m" value="1"><label for="gender_m">남자</label> / <INPUT type="radio" name="gender" id="gender_w" value="2"><label for="gender_w">여자</label></td>
				</tr>
			<? }?>
			<? if($ext_cont['reqbirth'] != 'H'){?>
			<tr>
				<th><? if($ext_cont['reqbirth'] == 'Y'){?><font color="#F02800"><b>＊</b></font><?}?><font color="#000000"><b>생년월일</b></font></th>
				<td colspan="3"><INPUT type="text" name="birth" value="" maxLength="10" style="WIDTH:60%;BACKGROUND-COLOR:#F7F7F7;" class="input"><p style="font-size:0.9em">( ex : <?=date('Y-m-d')?> )</p></td>
			</tr>
			<? }?>
				<tr>
					<th><font color="#F02800"><b>＊</b></font><font color="#000000"><b>이메일</b></font></th>
					<td><INPUT type=text name="email" value="<?=$email?>" maxLength="100" style="WIDTH:90%; BACKGROUND-COLOR:#F7F7F7;" class="input">
					<p style="margin-top:3px;"><A href="javascript:mailcheck();" class="button black small btn_memjoin" style="margin-left:4px;">이메일중복확인</a></p></td>
				</tr>
				<tr>
					<th><font color="#F02800"><b>＊</b></font><font color="#000000"><b>메일정보<br />수신여부</b></font></th>
					<td><INPUT type=radio name="news_mail_yn" value="Y" id="idx_news_mail_yn0" <?if($news_mail_yn=="Y")echo"checked";?> style="BORDER:none;"><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand" onmouseout="style.textDecoration='none'" for="idx_news_mail_yn0">받습니다.</LABEL> <INPUT type=radio name="news_mail_yn" value="N" id="idx_news_mail_yn1" <?if($news_mail_yn=="N")echo"checked";?> style="BORDER:none;"><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand" onmouseout="style.textDecoration='none'" for="idx_news_mail_yn1">받지 않습니다.</LABEL></td>
				</tr>
				<tr>
					<th><font color="#F02800"><b>＊</b></font><font color="#000000"><b>SMS정보<br />수신여부</b></font></th>
					<td><INPUT type=radio name="news_sms_yn" value="Y" id="idx_news_sms_yn0" <?if($news_sms_yn=="Y")echo"checked";?> style="BORDER:none;"><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand" onmouseout="style.textDecoration='none'" for="idx_news_sms_yn0">받습니다.</LABEL> <INPUT type=radio name="news_sms_yn" value="N" id="idx_news_sms_yn1" <?if($news_sms_yn=="N")echo"checked";?> style="BORDER:none;"><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand" onmouseout="style.textDecoration='none'" for="idx_news_sms_yn1">받지 않습니다.</LABEL></td>
				</tr>
<?	
if($recom_ok=="Y") {
	if($recom_url_ok=="Y" && $_COOKIE['url_id'] != ""){
		if($_data->recom_addreserve >0){
?>
				<tr>
					<th style="padding-left:27px"><font color="#000000"><b>추가적립금</b></font></th>
					<td><b><?=$_COOKIE['url_name']?>(<?=$_COOKIE['url_id']?>)</b>님의 초대로 <b><font color="#FD9999"> 적립금 <?=$_data->recom_addreserve?>원</font></b>을 추가 적립해 드립니다.<input type="hidden" name="rec_id" value="<?=$_COOKIE['url_id']?>"></td>
				</tr>
<?
		}else{
?>
				<tr>
					<th style="padding-left:27px"><font color="#000000"><b>추천인</b></font></th>
					<td><b><?=$_COOKIE['url_name']?>(<?=$_COOKIE['url_id']?>)</b>님의 초대를 받았습니다.<input type="hidden" name="rec_id" value="<?=$_COOKIE['url_id']?>" style="WIDTH:120px;BACKGROUND-COLOR:#F7F7F7;"></td>
				</tr>
<?
		}
	}else{
?>
				<tr>
					<th style="padding-left:27px"><font color="#000000"><b>추천ID</b></font></th>
					<td><INPUT type="text" name="rec_id" maxLength="12"  value="<?=$rec_id?>" style="WIDTH:120px;BACKGROUND-COLOR:#F7F7F7;" class="input"></td>
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
</table>

<div class="memJoinButton">
	<a class="button blue bigrounded" href="javascript:CheckForm();">회원가입</a><!--<img src="<?=$Dir?>images/common/mbjoin/<?=$_data->design_mbjoin?>/memberjoin_skin1_btn3.gif" border="0">-->
	<a class="button white bigrounded" href="javascript:history.go(-1);";>이전으로</a><!--<img src="<?=$Dir?>images/common/mbjoin/<?=$_data->design_mbjoin?>/memberjoin_skin1_btn4.gif" border="0" hspace="3">-->
</div>