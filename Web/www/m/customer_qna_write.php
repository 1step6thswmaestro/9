<?
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once($Dir."lib/shopdata.php");
	include_once("header.php");
	include_once($Dir."board/head.php");


	$board_name = isset($_REQUEST[board])? $_REQUEST[board]:"";
	$board_num = isset($_REQUEST[num])? $_REQUEST[num]:"";
	$board_pass = isset($_REQUEST[pass])? $_REQUEST[pass]:"";

	$board_pridx = isset($_REQUEST[pridx])? $_REQUEST[pridx]:"";
	$mode = isset($_REQUEST[mode])? $_REQUEST[mode]:"";


	if(empty($board_name)){
		echo '<script>alert("������ ���� �Ǿ����ϴ�.");</script>';
		exit;
	}

	$get_qna_sql = "SELECT * FROM tblboardadmin WHERE board = '".$board_name."' ";
	$get_qna_result = mysql_query($get_qna_sql, get_db_conn());
	$get_qna_row = mysql_fetch_array($get_qna_result);

	$set_qna_list_view =$get_qna_row[grant_view]; // �Խ��� ��ȸ ���� N: ȸ����ȸ�� ���,�ۺ��� ��� ����, U: ��ȸ���� ��Ϻ��⸸ ����, Y: ȸ��������
	$set_qna_list_write = $get_qna_row[grant_write]; // �Խ��� ���� ����
	$set_qna_category = $get_qna_row[subCategory]; // ī�װ� ����Ʈ
	$set_qna_lock = $get_qna_row[use_lock]; // ��ݻ��� ���� ����
	$set_qna_max_num = $get_qna_row[max_num]; // �ֻ��� �� ��ȣ

	mysql_free_result($get_qna_result);

	if($set_qna_list_write == "Y" || $set_qna_list_write == "A"){//ȸ�� ����
		if($_ShopInfo->getMemid() == "" || $_ShopInfo->getMemid() == null){
			echo '<script>alert("ȸ���� �۾��Ⱑ �����մϴ�.\n�α����� �̿��ϼ���.");history.go(-1);</script>';
			exit;
		}
	}

	if($_ShopInfo->getMemid() != "" ){
		$member_sql = "SELECT * FROM tblmember WHERE id = '".$_ShopInfo->getMemid()."' ";
		$member_result = mysql_query($member_sql, get_db_conn());
		$member_row = mysql_fetch_object($member_result);
	}

	if($member_row){
		$name = $member_row->name;
		$email = $member_row->email;
		$id = $member_row->id;
		$name = $member_row->name;
		$lock = "readonly";
	}


	if($mode == "upload"){

		$thread = $setup[thread_no] - 1;
		if ($thread<=0) {
			$que2 = "SELECT MIN(thread) FROM tblboard ";
			$result = mysql_query($que2,get_db_conn());
			$row = mysql_fetch_array($result);
			if ($row[0]<=0) {
				$thread = 999999999;
			} else {
				$thread = $row[0] - 1;
			}
			mysql_free_result($result);
		}


		$thread_no = $thread;
		$secret = isset($_POST[secret])? $_POST[secret]:"";
		$name = isset($_POST[name])? $_POST[name]:"";
		$passwd = isset($_POST[passwd])? $_POST[passwd]:"";
		$email = isset($_POST[email])? $_POST[email]:"";
		$cate = isset($_POST[cate])? $_POST[cate]:"";
		$title = isset($_POST[title])? $_POST[title]:"";
		$content = isset($_POST[content])? $_POST[content]:"";
		$pridx = isset($_POST[pridx])? $_POST[pridx]:"";
		//$username = isset($_POST[userid])? $_POST[userid]:"";
		
		if($set_qna_lock == "A"){
			$secret=1;
		}

		$name = addslashes($name);
		$title = str_replace("<!","&lt;!",$title);
		$title = addslashes($title);
		$content = str_replace("<!","&lt;!",$content);
		$content = addslashes($content);
		$userid = $_ShopInfo->getMemid();
		//$up_usercel = $up_cel1."-".$up_cel2."-".$up_cel3;



		if($setup[use_html]=="N") $up_html="";
		if (!$up_html) {
			$send_memo = nl2br(stripslashes($up_memo));
		}

		$next_no = $setup[max_num];

		$up_sql  = "INSERT tblboard SET ";
		$up_sql .= "board				= '".$board_name."', ";
		$up_sql .= "subCategory		= '".$cate."', ";
		$up_sql .= "num				= '', ";
		$up_sql .= "thread				= '".$thread_no."', ";
		$up_sql .= "pos				= '0', ";
		$up_sql .= "depth				= '0', ";
		$up_sql .= "prev_no			= '0', ";
		if(strlen($pridx)>0) {
			$up_sql.= "pridx			= '".$pridx."', ";
		}
		$up_sql .= "next_no			= '".$set_qna_max_num."', ";
		$up_sql .= "name				= '".$name."', ";
		$up_sql .= "passwd				= '".$passwd."', ";
		$up_sql .= "email				= '".$email."', ";
		$up_sql .= "userid				= '".$userid."', ";
		//$up_sql .= "usercel			= '".$up_usercel."', ";
		$up_sql .= "is_secret			= '".$secret."', ";
		$up_sql .= "use_html			= '".$up_html."', ";
		$up_sql .= "title				= '".$title."', ";
		//$up_sql .= "filename			= '".$up_filename."', ";
		$up_sql .= "writetime			= '".time()."', ";
		$up_sql .= "ip					= '".getenv("REMOTE_ADDR")."', ";
		$up_sql .= "access				= '0', ";
		$up_sql .= "total_comment		= '0', ";
		$up_sql .= "content			= '".$content."', ";
		$up_sql .= "notice				= '0', ";
		$up_sql .= "deleted			= '0' ";


		$insert = mysql_query($up_sql,get_db_conn());


		if($insert) {

			$qry = "SELECT LAST_INSERT_ID() ";
			$res = mysql_fetch_row(mysql_query($qry,get_db_conn()));
			$thisNum = $res[0];

			if ($next_no) {
				$qry9 = "SELECT thread FROM tblboard WHERE board='$board' AND num='$next_no' ";
				$res9 = mysql_query($qry9,get_db_conn());
				$next_thread = mysql_fetch_row($res9);
				@mysql_free_result($res9);

				mysql_query("UPDATE tblboard SET prev_no='".$thisNum."' WHERE board='".$board."' AND thread = '".$next_thread[0]."'",get_db_conn());

				mysql_query("UPDATE tblboard SET prev_no='$thisNum' WHERE board='$board' AND num = '$next_no'",get_db_conn());

				mysql_query("UPDATE tblboardadmin SET thread_no = '".$thread_no."'",get_db_conn());
			}

			// ===== �������̺��� �Խñۼ� update =====
			$sql3 = "UPDATE tblboardadmin SET total_article=total_article+1, max_num='$thisNum' ";
			$sql3.= "WHERE board='$board' ";
			$update = mysql_query($sql3,get_db_conn());


			/*
			if (($setup[use_admin_mail]=="Y") && $setup[admin_mail]) {
				INCLUDE "SendForm.inc.php";

				$title = $send_subject;
				$message = GetHeader() . GetContent($send_name, $send_email, $send_subject, $send_memo,$send_date,$send_filename,$setup[board_name]) . GetFooter();

				$tmp_admin_mail_list = split(",",$setup[admin_mail]);

				sendMailForm($send_name,$send_email,$message,$bodytext,$mailheaders);

				for($jj=0;$jj<count($tmp_admin_mail_list);$jj++) {
					if (ismail($tmp_admin_mail_list[$jj])) {
						mail($tmp_admin_mail_list[$jj], $title, $bodytext, $mailheaders);
					}
				}
			}
			*/

			//�Խ��� �۵�� SMS�߼�
			$sqlsms = "SELECT * FROM tblsmsinfo WHERE admin_board='Y' ";
			$resultsms= mysql_query($sqlsms,get_db_conn());
			if($rowsms=mysql_fetch_object($resultsms)){
				function getStringCut($strValue,$lenValue)
				{
					preg_match('/^([\x00-\x7e]|.{2})*/', substr($strValue,0,$lenValue), $retrunValue);
					return $retrunValue[0];
				}

				$sms_id=$rowsms->id;
				$sms_authkey=$rowsms->authkey;

				if (($setup[use_admin_sms]=="Y") && $setup[admin_sms]) {
					$totellist = $setup[admin_sms];

					$fromtel=$rowsms->return_tel;
					$smsboardname=str_replace("\\n"," ",str_replace("\\r","",strip_tags($setup[board_name])));
					$smsboardsubject=str_replace("\\n"," ",str_replace("\\r","",strip_tags(str_replace("&lt;!","<!",stripslashes($up_subject)))));

					/*$smsmsg="]�űԱ��� ".getStringCut($smsboardsubject,20)."���� ��ϵǾ����ϴ�.";
					$smsmsg=getStringCut($setup[board_name],80-strlen($smsmsg)).$smsmsg;*/

					$new_post_msg = $rowsms->new_post_msg;
					$pattern = array("(\[BOARD\])","(\[TITLE\])");
					$replace = array($setup[board_name], getStringCut($smsboardsubject,20));
					$new_post_msg=preg_replace($pattern, $replace, $new_post_msg);
					$new_post_msg=addslashes($new_post_msg);

					$etcmsg="�Խ��� �۵�� �޼���(������)";
					if($rowsms->sleep_time1!=$rowsms->sleep_time2){
						$date="0";
						$time = date("Hi");
						if($rowsms->sleep_time2<"12" && $time<=substr("0".$rowsms->sleep_time2,-2)."59") $time+=2400;
						if($rowsms->sleep_time2<"12" && $rowsms->sleep_time1>$rowsms->sleep_time2) $rowsms->sleep_time2+=24;

						if($time<substr("0".$rowsms->sleep_time1,-2)."00" || $time>=substr("0".$rowsms->sleep_time2,-2)."59"){
							if($time<substr("0".$rowsms->sleep_time1,-2)."00") $day = date("d");
							else $day=date("d")+1;
							$date = date("Y-m-d H:i:s",mktime($rowsms->sleep_time1,0,0,date("m"),$day,date("Y")));
						}
					}
					if(strlen($new_post_msg)>80)
						for($i=0; $i<ceil(strlen($new_post_msg)/80); $i++)
							$temp=SendSMS($sms_id, $sms_authkey, $totellist, "", $fromtel, $date, substr($new_post_msg, $i*81, ($i*81)+80), $etcmsg);
					else
						$temp=SendSMS($sms_id, $sms_authkey, $totellist, "", $fromtel, $date, $new_post_msg, $etcmsg);
					mysql_free_result($resultsms);

					//echo $sms_id."===".$sms_authkey."===".$totellist."===".$fromtel."===".$date."===".$smsmsg."===".$etcmsg;
				}
			}

			echo '<script>alert("���������� ��ϵǾ����ϴ�.");location.href="./customer_qna_list.php";</script>';
			echo("<meta http-equiv='Refresh' content='0; URL=\"./customer_qna_list.php\">");
			exit;
		} else {
			echo "
				<script>
				window.alert('�۾��� �Է��� ������ �߻��Ͽ����ϴ�.');
				</script>
			";
			//reWriteForm();
			exit;
		}
	}

	if(!empty($set_qna_category)){
		$catetory = explode(",",$set_qna_category);
	}

	if(!empty($board_pridx)){
		$filepath = "../data/shopimages/product/";
		$pridx_sql = "SELECT * FROM tblproduct WHERE pridx = ".$board_pridx;
		$pridx_result = mysql_query($pridx_sql, get_db_conn());
		$pridx_row = mysql_fetch_object($pridx_result);


		//$img_state = $filepath.$pridx_row->productcode.".jpg";
		$img_state = $filepath.$pridx_row->tinyimage;
		if(file_exists($img_state)){
			$img = $img_state;
			$size = _getImageSize($img); // �̹��� ������
			$img_class = "";
			if($size[width] >= $size[height]){ //�̹��� ũ�⿡ ���� stylesheet Ŭ���� �ο�
				$class_name = "img_width";
			}else{
				$class_name = "img_height";
			}

		}else{
			$img ="../images/no_img.gif";
		}

		if(strlen($pridx_row->productname) > 28){
			$write_productname = substr($pridx_row->productname, 0, 28)."...";
		}else{
			$write_productname = $pridx_row->productname;
		}
		if($pridx_row->productcode){
			$return_url="./productdetail_tab04.php?productcode=".$pridx_row->productcode;
		}
	}
?>
<script type="text/javascript" src="./gmeditor/js/jquery.js"></script>
<script type="text/javascript" src="./gmeditor/js/jquery.event.drag-2.0.min.js"></script>
<script type="text/javascript" src="./gmeditor/js/jquery.resizable.js"></script>
<script type="text/javascript" src="./gmeditor/js/ajax_upload.3.6.js"></script>
<script type="text/javascript" src="./gmeditor/js/ej.h2xhtml.js"></script>
<script type="text/javascript" src="./gmeditor/editor.js"></script>
<style type="text/css">
/*  @import url("./gmeditor/common.css");*/
</style>
<script language="javascript" type="text/javascript">
$(document).ready(function() {
	ejEditor();
});
</script>
<div id="content">
	<div class="h_area2">
		<h2>������</h2>
		<a href="main.php" class="btn_home" rel="external"><span class="vc">Ȩ</span></a>
		<a href="javascript:history.back()" class="btn_prev" rel="external"><span>����</span></a>
	</div>


	<form name="qnaForm" id="qnaForm" action="<?=$PHP_SELF?>" method="post">
	<input type="hidden" name="board" value="<?=$board_name?>"/>
	<input type="hidden" name="userid" value="<?=$id?>" />
	<input type="hidden" name="mode" value="upload"/>

	<table border="0" cellpadding="0" cellspacing="0" class="writeForm">
		<caption>��ǰ����</caption>
		<col width="70"></col>
		<col width=""></col>

		<? if($pridx_row){ ?>
		<tr>
			<td colspan="2">
				<a href="<?=$return_url?>">
				<div class="img_container">
					<div class="img_box"><img class= "<?=$class_name?>" src="<?=$img?>"></div>
					<div class="img_contents">
						<b><?=$write_productname?></b><br />
						�ǸŰ� : <span class="sellprice"><?=number_format($pridx_row->sellprice);?>��</span><br />
						���߰� : <strike><?=number_format($pridx_row->consumerprice);?>��</strike>
					</div>
					<input type="hidden" name="pridx" value="<?=$pridx_row->pridx?>">
				</div>
				</a>
			</td>
		</tr>
		<? } ?>

		<tr>
			<th>��ݱ��</th>
			<td>
				<?if($set_qna_lock == "Y"){?>
					<select name="secret" class="cate" id="lock">
						<option value="">--����--</option>
						<option value="0" <?=$no_lock?>>������</option>
						<option value="1" <?=$lock?>>��ݻ��</option>
					</select>
				<?} else if($set_qna_lock == "A"){?>
					<font color="#FF0000">*�ڵ����� ��б۷� ��ȯ�˴ϴ�</font>
				<?}?>
			</td>
		</tr>
		<tr>
			<th>�ۼ���</th>
			<td><input type="text" name="name" value="<?=$name?>" <?=$lock?> /></td>
		</tr>
		<tr>
			<th>��й�ȣ</th>
			<td><input type="password" name="passwd" value="" /></td>
		</tr>
		<tr>
			<th>�̸���</th>
			<td><input type="text" name="email" value="<?=$email?>" <?=$lock?> /></td>
		</tr>
		<tr>
			<th>���Ӹ�</th>
			<td>
				<select name="cate" class="cate">
					<option value="">���Ӹ�����</option>
					<? foreach($catetory as $key){ ?>
					<option value="<?=$key?>" <? if($write_category == $key){echo 'selected';}?>><?=$key?></option>
					<? } ?>
				</select>
			</td>
		</tr>
		<tr>
			<th>������</th>
			<td><input type="text" name="title" value="" /></td>
		</tr>
		<tr>
			<th>�۳���</th>
			<td><textarea name="content" id="content"><?=$content_row->content?></textarea></td><!-- lang="ej-editor4" -->
		</tr>
	</table>
	<div style="text-align:center; margin:10px 0px 30px 0px;"><a class="button black bigrounded" id="btn_submit">���ǵ��</a> <a class="button white bigrounded" id="btn_reset">�ٽ��ۼ�</a></div>
	</form>
</div>

<script>
	var form = document.qnaForm;
	$("#btn_submit").click(function(){ // ��üũ �� �����
		var issecurity = "<?=$set_qna_lock?>";
		if(issecurity != "A" && ($("select[name=secret]").val() == "" || $("select[name=secret]").val() == null)){
			alert("��ݱ���� �����ϼ���.");
			$("select[name=secret]").focus();
			return;
		}else if($("input[name=name]").val() == "" || $("input[name=name]").val() == null){
			alert("�ۼ��ڸ� �Է��ϼ���.");
			$("input[name=name]").focus();
			return;
		}else if($("input[name=passwd]").val() == "" || $("input[name=passwd]").val() == null){
			alert("��й�ȣ�� �Է��ϼ���.");
			$("input[name=passwd]").focus();
			return;
		}else if($("input[name=email]").val() == "" || $("input[name=email]").val() == null){
			alert("�̸����� �Է��ϼ���.");
			$("input[name=email]").focus();
			return;
		/*}else if($("select[name=cate]").val() == "" || $("select[name=cate]").val() == null){
			alert("���Ӹ��� �����ϼ���.");
			$("select[name=cate]").focus();
			return;
		*/
		}else if($("input[name=title]").val() == "" || $("input[name=title]").val() == null){
			alert("������ �Է��ϼ���");
			$("input[name=title]").focus();
			return;
		/*
		}else if($("#ejEdt_content").contents().find("body").text() == "" || $("#ejEdt_content").contents().find("body").text() == null){
			alert("������ �Է��ϼ���.");
			$("#ejEdt_content").contents().find("body").focus();
			return;
		*/
		}else if($("textarea#content").val() == "" || $("textarea#content").val() == null){
			alert("������ �Է��ϼ���");
			$("textarea#content").focus();
			return;
		}else{
			$(".write_btn").css("display","none");
			form.submit();
		}

	});

	$("#btn_reset").click(function(){ //�ʱ�ȭ
		$(".m_input").each(function(){
			this.value= "";
		});

		//$("#ejEdt_content").contents().find("body").text("");
		$(".cate").find('option:first').attr('selected', 'selected');
		//$("#content").value="";

	});
</script>

<? include_once('footer.php'); ?>
