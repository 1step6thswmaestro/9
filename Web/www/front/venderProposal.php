<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
?>

<HTML>
<HEAD>
<TITLE><?=$_data->shoptitle?></TITLE>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META http-equiv="X-UA-Compatible" content="IE=5" />
<META name="description" content="<?=(strlen($_data->shopdescription)>0?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<?include($Dir."lib/style.php")?>

<SCRIPT LANGUAGE="JavaScript">
	function sendForm( form ) {

		if(form.company.value.length==0) {
			alert("ȸ��� �Է��ϼ���.");
			form.company.focus(); return;
		}
		if(form.home_addr1.value.length==0) {
			alert("����� �ּҸ� �Է��ϼ���.");
			f_addr_search('proposalFrom','home_post','home_addr1',2); return;
		}
		if(form.home_addr2.value.length==0) {
			alert("����� �� �ּҸ� �Է��ϼ���.");
			form.home_addr2.focus(); return;
		}

		if(form.name.value.length==0) {
			alert("����� ������ �Է��ϼ���.");
			form.name.focus(); return;
		}

		if(form.tell1.value.length==0) {
			alert("����� ��ȭ��ȣ�� �Է��ϼ���.");
			form.tell1.focus(); return;
		}
		if(form.tell2.value.length==0) {
			alert("����� ��ȭ��ȣ�� �Է��ϼ���.");
			form.tell2.focus(); return;
		}
		if(form.tell3.value.length==0) {
			alert("����� ��ȭ��ȣ�� �Է��ϼ���.");
			form.tell3.focus(); return;
		}

		if(form.phone1.value=='X') {
			alert("����� �ڵ��� ���ڸ��� �����ϼ���.");
			form.phone1.focus(); return;
		}
		if(form.phone2.value.length==0) {
			alert("����� �ڵ����� �Է��ϼ���.");
			form.phone2.focus(); return;
		}
		if(form.phone3.value.length==0) {
			alert("����� �ڵ����� �Է��ϼ���.");
			form.phone3.focus(); return;
		}

		if(form.mail.value.length==0) {
			alert("�̸����� �Է��ϼ���.");
			form.mail.focus(); return;
		}
		if(!IsMailCheck(form.mail.value)) {
			alert("�̸��� ������ �����ʽ��ϴ�.\n\nȮ���Ͻ� �� �ٽ� �Է��ϼ���.");
			form.mail.focus(); return;
		}

		if(form.contents.value.length==0) {
			alert("�󼼹��ǳ��� �Է��ϼ���.");
			form.contents.focus(); return;
		}

		form.action = '/front/venderProposal.process.php';
		form.method = 'POST';
		form.submit();

	}

	function f_addr_search(form,post,addr,gbn) {
		window.open("/front/addr_search.php?form="+form+"&post="+post+"&addr="+addr+"&gbn="+gbn,"f_post","resizable=yes,scrollbars=yes,x=100,y=200,width=370,height=250");
	}
	//-->
</SCRIPT>

</HEAD>

<? include ($Dir.MainDir.$_data->menu_type.".php"); ?>

<style>
	.partnerinfoL { width:160px; padding-left:14px; }
	.partnerinfoL2 { width:160px; padding-left:27px; }
	.partnerinfoR { padding-left:10px; }
</style>

<div class="subpageTitle">���� �� ��������</div>

<table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-top:10px;">
	<!--
	<tr>
		<td background="/images/003/venderproposal_title_bg.gif"><img src="/images/003/venderproposal_title_head.gif" alt="" /></td>
	</tr>
	-->
	<tr><td>&nbsp;-&nbsp;&nbsp;<font color="#F02800"><b>(��)�� �ʼ��Է� �׸��Դϴ�.</b></font></td></tr>
	<tr><td height="10"></tr>
	<tr>
		<td style="padding:0px 10px;">

			<table cellpadding="0" cellspacing="6"  width="100%">
				<FORM name="proposalFrom">
				<tr><td colspan="2" height="2" bgcolor="#E6E6E6"></td></tr>
				<tr><td colspan="2" height="2"></td></tr>
				<tr>
					<td class="partnerinfoL"><font color="#F02800"><b>��</b></font><font color="#000000"><b>���ǳ���</b></font></td>
					<td class="partnerinfoR">
						<?
							$sql = "SELECT * FROM `tblVenderProposalType` ";
							$result=mysql_query($sql,get_db_conn());
							while($row=mysql_fetch_object($result)) {
								$sel = ( $sel_i == 0 ) ? "checked":"";
								$sel_i++;
								echo "<input type=\"radio\" name=\"type\" id='name".$row->idx."' value=\"".$row->name."\" ".$sel."><label style='cursor:hand;' onMouseOver=\"style.textDecoration='underline';\" onMouseOut=\"style.textDecoration='none';\" for='name".$row->idx."'>".$row->name."</label>&nbsp;&nbsp;";
							}
						?>
					</td>
				</tr>
				<tr><td colspan="2" height="1" bgcolor="#E9E9E9"></td></tr>

				<tr>
					<td class="partnerinfoL"><font color="#F02800"><b>��</b></font><font color="#000000"><b>ȸ���</b></font></td>
					<td class="partnerinfoR"><input type="text" name="company" maxlength="20" style="width:360px; background-color: rgb(247, 247, 247);" class="input"></td>
				</tr>
				<tr><td colspan="2" height="1" bgcolor="#E9E9E9"></td></tr>

				<tr>
					<td class="partnerinfoL"><font color="#F02800"><b>��</b></font><font color="#000000"><b>����� ������ �ּ�</b></font></td>
					<td class="partnerinfoR">
						<table border="0" cellpadding="0" cellspacing="0">
							<tr>
								<td>
									<input type="text" name="home_post1" value="" style="width:50px; background-color: rgb(247, 247, 247);" class="input" readonly>
									<a href="javascript:f_addr_search('proposalFrom','home_post','home_addr1',2);"><img src="/images/common/mbjoin/001/memberjoin_skin1_btn2.gif" border="0" align="absmiddle" hspace="3"></a>
								</td>
							</tr>
							<tr>
								<td><input type=text name=home_addr1 value="" maxlength=100 readonly style="width:360px; background-color: rgb(247, 247, 247);" class="input"></td>
							</tr>
							<tr>
								<td><input type=text name=home_addr2 value="" maxlength=100 style="width:360px; background-color: rgb(247, 247, 247);" class="input"></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr><td colspan="2" height="1" bgcolor="#E9E9E9"></td></tr>

				<tr>
					<td class="partnerinfoL"><font color="#F02800"><b>��</b></font><font color="#000000"><b>����� ��</b></font></td>
					<td class="partnerinfoR"><input type="text" name="name" maxlength="20" style="width:100px; background-color: rgb(247, 247, 247);" class="input"></td>
				</tr>
				<tr><td colspan="2" height="1" bgcolor="#E9E9E9"></td></tr>

				<tr>
					<td class="partnerinfoL"><font color="#F02800"><b>��</b></font><font color="#000000"><b>��ȭ��ȣ</b></font></td>
					<td class="partnerinfoR">
						<input type="text" name="tell1" maxlength="4" style="width:50px; background-color: rgb(247, 247, 247);" class="input">
						-
						<input type="text" name="tell2" maxlength="4" style="width:60px; background-color: rgb(247, 247, 247);" class="input">
						-
						<input type="text" name="tell3" maxlength="4" style="width:60px; background-color: rgb(247, 247, 247);" class="input">
					</td>
				</tr>
				<tr><td colspan="2" height="1" bgcolor="#E9E9E9"></td></tr>

				<tr>
					<td class="partnerinfoL"><font color="#F02800"><b>��</b></font><font color="#000000"><b>�޴���</b></font></td>
					<td class="partnerinfoR">
						<select name="phone1">
							<option value="X" selected="selected">����</option>
							<option value="010">010</option>
							<option value="011">011</option>
							<option value="016">016</option>
							<option value="017">017</option>
							<option value="018">018</option>
							<option value="019">019</option>
						</select>
						-
						<input type="text" name="phone2" maxlength="4" style="width:60px; background-color: rgb(247, 247, 247);" class="input"> - <input type="text" name="phone3" maxlength="4" style="width:60px; background-color: rgb(247, 247, 247);" class="input">
					</td>
				</tr>
				<tr><td colspan="2" height="1" bgcolor="#E9E9E9"></td></tr>

				<tr>
					<td class="partnerinfoL"><font color="#F02800"><b>��</b></font><font color="#000000"><b>�̸���</b></font></td>
					<td class="partnerinfoR"><input type="text" name="mail" maxlength="40" style="width:360px; background-color: rgb(247, 247, 247);" class="input"></td>
				</tr>
				<tr><td colspan="2" height="1" bgcolor="#E9E9E9"></td></tr>

				<tr>
					<td class="partnerinfoL2"><font color="#000000"><b>������Ʈ �ּ�</b></font></td>
					<td class="partnerinfoR"><input type="text" name="site" style="width:360px; background-color: rgb(247, 247, 247);" class="input"></td>
				</tr>
				<tr><td colspan="2" height="1" bgcolor="#E9E9E9"></td></tr>

				<tr>
					<td class="partnerinfoL2"><font color="#000000"><b>���⵵ �����</b></font></td>
					<td class="partnerinfoR"><input type="text" name="preSell" maxlength="20" style="width:100px; background-color: rgb(247, 247, 247);" class="input"></td>
				</tr>
				<tr><td colspan="2" height="1" bgcolor="#E9E9E9"></td></tr>

				<tr>
					<td class="partnerinfoL2"><font color="#000000"><b>������</b></font></td>
					<td class="partnerinfoR"><input type="text" name="memNo" maxlength="10" style="width:100px; background-color: rgb(247, 247, 247);" class="input"></td>
				</tr>
				<tr><td colspan="2" height="1" bgcolor="#E9E9E9"></td></tr>

				<tr>
					<td class="partnerinfoL2"><font color="#000000"><b>���ո�, ���¸��� ��<br />�� �� ������</b></font></td>
					<td class="partnerinfoR"><textarea name="mall" style="width:100%; height:80px;"></textarea></td>
				</tr>
				<tr><td colspan="2" height="1" bgcolor="#E9E9E9"></td></tr>

				<tr>
					<td class="partnerinfoL"><font color="#F02800"><b>��</b></font><font color="#000000"><b>�� ���ǳ���</b></font></td>
					<td class="partnerinfoR"><textarea name="contents" style="width:100%; height:160px;"></textarea></td>
				</tr>
				<input type="hidden" name="mode" value="venderProposalInsert">
				</FORM>

				<tr><td colspan="2" height="10"></tr>
				<tr><td colspan="2" height="2" bgcolor="#d5d5d5"></td></tr>
				<tr><td colspan="2" height="5"></tr>
				<tr><td colspan="2" align="center"><img src="/images/common/partner/partner_ok.gif" border="0" hspace="10" alt="Ȯ��" onclick="sendForm(proposalFrom);" style="cursor:pointer;" /><a href="javascript:history.back(-1);"><img src="/images/common/partner/partner_cancel.gif" border="0" alt="���" /></a></td></tr>
			</table>

		</td>
	</tr>
	<tr><td height="40"></td></tr>
</table>

<? include ($Dir."lib/bottom.php"); ?>

</BODY>
</HTML>