<table cellpadding="0" cellspacing="0"  width="100%">
	<?if($_data->resno_type!="N" && strlen($adultauthid)>0){###### ������ ���̵� �����ϸ� �Ǹ����� �ȳ���Ʈ######?>
		<tr>
			<td>- �Է��Ͻ� �̸��� �ֹι�ȣ�� <font color="#F02800"><b>�Ǹ�Ȯ��</b></font>�� �Ǿ�� ȸ�������� �Ϸ��Ͻ� �� �ֽ��ϴ�.</td>
		</tr>
	<?}?>
	<tr><td valign="bottom" style="height:30px; padding-bottom:5px; padding-left:10px;"><font color="#F02800"><b>���� �ʼ��Է� �׸��Դϴ�.</b></font></td></tr>
	<tr>
		<td valign="bottom" style="height:30px; padding-bottom:5px; padding-left:10px;">
			<p>������Ͽ��� ȸ�������� �� ��� ����������</p>
			<p style="margin-left:13px;">�ʼ� �Է¿�� �� �ּ�, ����ó ���� ������</p>
			<p style="margin-left:13px;">�����ֹ��� �ֹ��� ������ �ڵ� �Է� �˴ϴ�.</p>
		</td>
	</tr>
	<tr>
		<td>
			<table border="0" cellpadding="0" cellspacing="0" width="100%" class="memJoinForm">
				<col width="110"></col>
				<col width=""></col>
				<tr>
					<th><font color="#F02800"><b>��</b></font><font color="#000000"><b>���̵�</b></font></th>
					<td><INPUT type=text name="id" value="<?=$id?>" maxLength="12" style="WIDTH:90px; BACKGROUND-COLOR:#F7F7F7;" class="input"> <A class="button black small btn_memjoin" href="javascript:idcheck();">�ߺ�Ȯ��</a></td>
				</tr>
				<tr>
					<th><font color="#F02800"><b>��</b></font><font color="#000000"><b>��й�ȣ</b></font></th>
					<td><INPUT type=password name="passwd1" value="<?=$passwd1?>" maxLength="20" style="WIDTH:90px; BACKGROUND-COLOR:#F7F7F7;" class="input"></td>
				</tr>
				<tr>
					<th><font color="#F02800"><b>��</b></font><font color="#000000"><b>��й�ȣȮ��</b></font></th>
					<td><INPUT type=password name="passwd2" value="<?=$passwd2?>" maxLength="20" style="WIDTH:90px; BACKGROUND-COLOR:#F7F7F7;" class="input"></td>
				</tr>
				<tr>
					<th><font color="#F02800"><b>��</b></font><font color="#000000"><b>�̸�</b></font></th>
					<td><INPUT type=text name="name" value="<?=$name?>" maxLength="15" style="WIDTH:90px; BACKGROUND-COLOR:#F7F7F7;" class="input"></td>
				</tr>
<?if($_data->resno_type!="N"){?>
				<tr>
					<th><font color="#F02800"><b>��</b></font><font color="#000000"><b>�ֹε�Ϲ�ȣ</b></font></th>
					<td><INPUT type=text name="resno1" value="<?=$resno1?>" maxLength="6" onkeyup="return strnumkeyup2(this);" style="WIDTH:50px; BACKGROUND-COLOR:#F7F7F7;" class="input"> - <INPUT type=password name="resno2" value="<?=$resno2?>" maxLength="7" onkeyup="return strnumkeyup2(this);" style="WIDTH:58px;BACKGROUND-COLOR:#F7F7F7;" class="input"></td>
				</tr>
<?}?>
			<? if($ext_cont['reqgender'] != 'H'){?>
				<tr>
					<th><? if($ext_cont['reqgender'] == 'Y'){?><font color="#F02800"><b>��</b></font><?}?><font color="#000000"><b>����</b></font></th>
					<td><INPUT type="radio" name="gender" id="gender_m" value="1"><label for="gender_m">����</label> / <INPUT type="radio" name="gender" id="gender_w" value="2"><label for="gender_w">����</label></td>
				</tr>
			<? }?>
			<? if($ext_cont['reqbirth'] != 'H'){?>
			<tr>
				<th><? if($ext_cont['reqbirth'] == 'Y'){?><font color="#F02800"><b>��</b></font><?}?><font color="#000000"><b>�������</b></font></th>
				<td colspan="3"><INPUT type="text" name="birth" value="" maxLength="10" style="WIDTH:60%;BACKGROUND-COLOR:#F7F7F7;" class="input"><p style="font-size:0.9em">( ex : <?=date('Y-m-d')?> )</p></td>
			</tr>
			<? }?>
				<tr>
					<th><font color="#F02800"><b>��</b></font><font color="#000000"><b>�̸���</b></font></th>
					<td><INPUT type=text name="email" value="<?=$email?>" maxLength="100" style="WIDTH:90%; BACKGROUND-COLOR:#F7F7F7;" class="input">
					<p style="margin-top:3px;"><A href="javascript:mailcheck();" class="button black small btn_memjoin" style="margin-left:4px;">�̸����ߺ�Ȯ��</a></p></td>
				</tr>
				<tr>
					<th><font color="#F02800"><b>��</b></font><font color="#000000"><b>��������<br />���ſ���</b></font></th>
					<td><INPUT type=radio name="news_mail_yn" value="Y" id="idx_news_mail_yn0" <?if($news_mail_yn=="Y")echo"checked";?> style="BORDER:none;"><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand" onmouseout="style.textDecoration='none'" for="idx_news_mail_yn0">�޽��ϴ�.</LABEL> <INPUT type=radio name="news_mail_yn" value="N" id="idx_news_mail_yn1" <?if($news_mail_yn=="N")echo"checked";?> style="BORDER:none;"><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand" onmouseout="style.textDecoration='none'" for="idx_news_mail_yn1">���� �ʽ��ϴ�.</LABEL></td>
				</tr>
				<tr>
					<th><font color="#F02800"><b>��</b></font><font color="#000000"><b>SMS����<br />���ſ���</b></font></th>
					<td><INPUT type=radio name="news_sms_yn" value="Y" id="idx_news_sms_yn0" <?if($news_sms_yn=="Y")echo"checked";?> style="BORDER:none;"><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand" onmouseout="style.textDecoration='none'" for="idx_news_sms_yn0">�޽��ϴ�.</LABEL> <INPUT type=radio name="news_sms_yn" value="N" id="idx_news_sms_yn1" <?if($news_sms_yn=="N")echo"checked";?> style="BORDER:none;"><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand" onmouseout="style.textDecoration='none'" for="idx_news_sms_yn1">���� �ʽ��ϴ�.</LABEL></td>
				</tr>
<?	
if($recom_ok=="Y") {
	if($recom_url_ok=="Y" && $_COOKIE['url_id'] != ""){
		if($_data->recom_addreserve >0){
?>
				<tr>
					<th style="padding-left:27px"><font color="#000000"><b>�߰�������</b></font></th>
					<td><b><?=$_COOKIE['url_name']?>(<?=$_COOKIE['url_id']?>)</b>���� �ʴ�� <b><font color="#FD9999"> ������ <?=$_data->recom_addreserve?>��</font></b>�� �߰� ������ �帳�ϴ�.<input type="hidden" name="rec_id" value="<?=$_COOKIE['url_id']?>"></td>
				</tr>
<?
		}else{
?>
				<tr>
					<th style="padding-left:27px"><font color="#000000"><b>��õ��</b></font></th>
					<td><b><?=$_COOKIE['url_name']?>(<?=$_COOKIE['url_id']?>)</b>���� �ʴ븦 �޾ҽ��ϴ�.<input type="hidden" name="rec_id" value="<?=$_COOKIE['url_id']?>" style="WIDTH:120px;BACKGROUND-COLOR:#F7F7F7;"></td>
				</tr>
<?
		}
	}else{
?>
				<tr>
					<th style="padding-left:27px"><font color="#000000"><b>��õID</b></font></th>
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
	<a class="button blue bigrounded" href="javascript:CheckForm();">ȸ������</a><!--<img src="<?=$Dir?>images/common/mbjoin/<?=$_data->design_mbjoin?>/memberjoin_skin1_btn3.gif" border="0">-->
	<a class="button white bigrounded" href="javascript:history.go(-1);";>��������</a><!--<img src="<?=$Dir?>images/common/mbjoin/<?=$_data->design_mbjoin?>/memberjoin_skin1_btn4.gif" border="0" hspace="3">-->
</div>