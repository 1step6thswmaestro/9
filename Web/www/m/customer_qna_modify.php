<?
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once($Dir."lib/shopdata.php");
	include_once("header.php");

	$board_name = isset($_REQUEST[board])? $_REQUEST[board]:"";
	$board_num = isset($_REQUEST[num])? $_REQUEST[num]:"";
	$board_pass = isset($_REQUEST[pass])? $_REQUEST[pass]:"";
	$mode = isset($_REQUEST[mode])? $_REQUEST[mode]:"";

	$get_qna_sql = "SELECT * FROM tblboardadmin WHERE board = '".$board_name."' ";
	$get_qna_result = mysql_query($get_qna_sql, get_db_conn());
	$get_qna_row = mysql_fetch_array($get_qna_result);

	$set_qna_list_view =$get_qna_row[grant_view]; // �Խ��� ��ȸ ���� N: ȸ����ȸ�� ���,�ۺ��� ��� ����, U: ��ȸ���� ��Ϻ��⸸ ����, Y: ȸ��������
	$set_qna_list_write = $get_qna_row[grant_write]; // �Խ��� ���� ����
	$set_qna_category = $get_qna_row[subCategory]; // ī�װ� ����Ʈ  
	$set_qna_lock = $get_qna_row[use_lock]; // ��ݻ��� ���� ���� 
	
	if(empty($board_name) || empty($board_num)){
		echo '<script>alert("�߸��� ��η� �����Ͽ����ϴ�.");history.go(-1);</script>';
		exit;
	}
	
	if($set_qna_list_view == "U" || $set_qna_list_view == "Y"){
		if($_ShopInfo->getMemid() == "" || $_ShopInfo->getMemid() == null){ 
			echo '<script>alert("���θ� ȸ���� �̿� �����մϴ�.\n�α��� �Ͻñ� �ٶ��ϴ�.");history.go(-1);</script>';
			exit;
		}
	}

	$content_sql= "SELECT * FROM tblboard WHERE board = '".$board_name."' AND num = ".$board_num;
	$content_result= mysql_query($content_sql, get_db_conn());
	$content_row = mysql_fetch_object($content_result);

	if($mode != "modify"){
		if($content_row->passwd != $board_pass){
			echo '<script>alert("��й�ȣ�� ���� �ʽ��ϴ�.");history.go(-1);</script>';
			exit;
		}
	}else{
		$secret = isset($_POST[secret])? $_POST[secret]:"0";
		$name = isset($_POST[name])? $_POST[name]:"";
		$passwd = isset($_POST[passwd])? $_POST[passwd]:"";
		$email = isset($_POST[email])? $_POST[email]:"";
		$cate = isset($_POST[cate])? $_POST[cate]:"";
		$title = isset($_POST[title])? $_POST[title]:"";
		$content = isset($_POST[content])? $_POST[content]:"";
		$thread = isset($_POST[thread])? $_POST[thread]:"";

		$modify_sql = "UPDATE tblboard SET ";
		$modify_sql .= "subCategory = '".$cate."', ";
		$modify_sql .= "name = '".$name."', ";
		$modify_sql .= "passwd = '".$passwd."', ";
		$modify_sql .= "email = '".$email."', ";
		$modify_sql .= "is_secret = '".$secret."', ";
		$modify_sql .= "title = '".$title."', ";
		$modify_sql .= "content = '".$content."' ";

		$modify_sql .= "WHERE num = ".$board_num." AND board = '".$board_name."' AND thread = ".$thread;

		if(($secret >= 0) && !empty($name) && !empty($passwd) && !empty($email) && !empty($cate) && !empty($title) && !empty($content) && !empty($thread)){
			
			if(mysql_query($modify_sql, get_db_conn())){
				echo '<script>alert("���������� �����Ǿ����ϴ�.");location.href="./customer_qna_list.php"</script>';
				exit;
			}else{
				echo '<script>alert("���������� �������� �ʾҽ��ϴ�.");"</script>';
			}
		}else{
			echo '<script>alert("�ʼ����� �����Ǿ� �������� �ʾҽ��ϴ�.");</script>';
		}
	}

	$modify_name = $content_row->name;
	$modify_email = $content_row->email;
	$modify_title = $content_row->title;
	$modify_content = $content_row->content;
	$modify_secret = $content_row->is_secret;
	$modify_category = trim($content_row->subCategory);
	$modify_thread = $content_row->thread;
	$modify_pridx = $content_row->pridx;
	
	switch($modify_secret){
		case 0:
			$no_lock = "selected";
			$lock = "";
		break;
		case 1:
			$no_lock = "";
			$lock = "selected";
		break;
		default:
			$no_lock = "";
			$lock = "selected";
		break;
	}

	if(!empty($modify_pridx)){
		$filepath = "../data/shopimages/product/";
		$pridx_sql = "SELECT * FROM tblproduct WHERE pridx = ".$modify_pridx;
		$pridx_result = mysql_query($pridx_sql, get_db_conn());
		$pridx_row = mysql_fetch_object($pridx_result);
		
		
		$img_state = $filepath.$pridx_row->tinyimage;
		if(file_exists($img_state)){
			$img = $img_state;
			$size = _getImageSize($img); // �̹��� ������ 
			if($size[width] >= $size[height]){ //�̹��� ũ�⿡ ���� stylesheet Ŭ���� �ο�
				$class_name = "img_width";
			}else{
				$class_name = "img_height";
			}

		}else{
			$img ="../images/no_img.gif";
		}

		if(strlen($pridx_row->productname) > 28){
			$modify_productname = substr($pridx_row->productname, 0, 28)."...";
		}else{
			$modify_productname = $pridx_row->productname;
		}
		if($pridx_row->productcode){
			$return_url="./productdetail_tab04.php?productcode=".$pridx_row->productcode;
		}
	}
	
	if(!empty($set_qna_category)){
		$catetory = explode(",",$set_qna_category);
	}
?>

<script type="text/javascript" src="./gmeditor/js/jquery.js"></script>
<script type="text/javascript" src="./gmeditor/js/jquery.event.drag-2.0.min.js"></script>
<script type="text/javascript" src="./gmeditor/js/jquery.resizable.js"></script>
<script type="text/javascript" src="./gmeditor/js/ajax_upload.3.6.js"></script>
<script type="text/javascript" src="./gmeditor/js/ej.h2xhtml.js"></script>
<script type="text/javascript" src="./gmeditor/editor.js"></script>
<style type="text/css">
  /*@import url("./gmeditor/common.css");*/
</style>
<script language="javascript" type="text/javascript">
$(document).ready(function() {
	ejEditor();
});
</script>
<div id="content">
	<div class="h_area2">
		<h2>��ǰ Q&A</h2>
		<a href="main.php" class="btn_home" rel="external"><span class="vc">Ȩ</span></a>
		<a href="javascript:history.back()" class="btn_prev" rel="external"><span>����</span></a>
	</div>

	<form name="qnaForm" id="qnaForm" action="<?=$PHP_SELF?>" method="post">

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
							<b><?=$modify_productname?></b><br />
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
						<select name="secret" class='cate' id="lock">
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
				<td><input type="text" name="name" value="<?=$modify_name?>" class="m_input" /></td>
			</tr>
			<tr>
				<th>��й�ȣ</th>
				<td><input type="password" name="passwd" value="" class="m_input" /></td>
			</tr>
			<tr>
				<th>�̸���</th>
				<td><input type="text" name="email" value="<?=$modify_email?>" class="m_input" /></td>
			</tr>
			<tr>
				<th>���Ӹ�</th>
				<td>
					<select name="cate" class="cate">
						<option value="">���Ӹ� ����</option>
						<option value=".">--- ���� ---</option>
						<? foreach($catetory as $key){ ?>
							<option value="<?=$key?>" <? if($modify_category == $key){echo 'selected';}?>><?=$key?></option>
						<? } ?>
					</select>
				</td>
			</tr>
			<tr>
				<th>������</th>
				<td><input type="text" name="title" value="<?=$modify_title?>" class="m_input" /></td>
			</tr>
			<tr>
				<th>�۳���</th>
				<td><textarea name="content" id="content" lang="ej-editor4"><?=$content_row->content?></textarea></td>
			</tr>
		</table>
		<div style="text-align:center; margin:10px 0px 30px 0px;"><a class="button black bigrounded" id="btn_submit">�����Ϸ�</a> <a class="button white bigrounded" id="btn_reset">�ٽ��ۼ�</a></div>

		<input type="hidden" name="board" value="<?=$board_name?>"/>
		<input type="hidden" name="num" value="<?=$board_num?>"/>
		<input type="hidden" name="thread" value="<?=$modify_thread?>"/>
		<input type="hidden" name="mode" value="modify"/>
	</form>
</div>

<script>
	var form = document.qnaForm;
	$("#btn_submit").click(function(){ // ��üũ �� �����
		
		if($("select[name=secret]").val() == "" || $("select[name=secret]").val() == null){
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
		}else if($("select[name=cate]").val() == "" || $("select[name=cate]").val() == null){
			alert("���Ӹ��� �����ϼ���.");
			$("select[name=cate]").focus();
			return;
		}else if($("input[name=title]").val() == "" || $("input[name=title]").val() == null){
			alert("��й�ȣ�� �Է��ϼ���");
			$("input[name=title]").focus();
			return;
		}else if($("#ejEdt_content").contents().find("body").text() == "" || $("#ejEdt_content").contents().find("body").text() == null){
			alert("������ �Է��ϼ���.");
			$("#ejEdt_content").contents().find("body").focus();
			return;
		}else{
			$(".modify_btn").css("display","none");
			form.submit();
		}

	});
	
	$("#btn_reset").click(function(){ //�ʱ�ȭ
		$(".m_input").each(function(){
			this.value= "";
		});

		$("#ejEdt_content").contents().find("body").text("");
		$(".cate").find('option:first').attr('selected', 'selected');
		$("#content").value="";
		
	});
</script>

<? include_once('footer.php'); ?>
