<?
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once($Dir."lib/shopdata.php");
	include_once("header.php");

	$board_name = isset($_REQUEST[board])? trim($_REQUEST[board]):"";
	$board_num = isset($_REQUEST[num])? trim($_REQUEST[num]):"";
	$c_num = isset($_REQUEST[c_num])? trim($_REQUEST[c_num]):"";
	if(empty($board_name) || empty($board_num)){
		echo '<script>alert("�߸��� ��η� �����Ͽ����ϴ�.");history.go(-1);</script>';
		exit;
	}
	
	$get_qna_sql = "SELECT * FROM tblboardadmin WHERE board = '".$board_name."' ";
	$get_qna_result = mysql_query($get_qna_sql, get_db_conn());
	$get_qna_row = mysql_fetch_array($get_qna_result);

	$set_qna_list_view =$get_qna_row[grant_view]; // �Խ��� ��ȸ ���� N: ȸ����ȸ�� ���,�ۺ��� ��� ����, U: ��ȸ���� ��Ϻ��⸸ ����, Y: ȸ��������
	
	$set_qna_list_write = $get_qna_row[grant_write]; // �Խ��� ���� ����

	if($set_qna_list_view == "U" || $set_qna_list_view == "Y"){
		if($_ShopInfo->getMemid() == "" || $_ShopInfo->getMemid() == null){ 
			echo '<script>alert("���θ� ȸ���� �̿� �����մϴ�.\n�α��� �Ͻñ� �ٶ��ϴ�.");history.go(-1);</script>';
			exit;
		}
	}

?>
<div id="content">
	<div class="h_area2">
		<h2>��й�ȣ Ȯ��</h2>
		<a href="main.php" class="btn_home" rel="external"><span class="vc">Ȩ</span></a>
		<a href="javascript:history.back()" class="btn_prev" rel="external"><span>����</span></a>
	</div>

	<div id="passwd_container">
		<div class="passwd_box">
			<div class="passwd_top">
				��� ����� ����Ͽ� ����� �Խù��Դϴ�.<br />
				������ ��й�ȣ�� �ۼ��� ��й�ȣ�� �Է��ϼ���.
			</div>
			<div class="passwd_bottom">
				<form name="passwd_form" method="post" action="comment_delete.php">
					<label>��й�ȣ &nbsp;:&nbsp;</label>
					<input style="border:1px solid #BBBBBB; width:100px; height:22px;" type="password" name="up_passwd" value=""/>
					<a href="#" class="button black" onClick="passForm();"/>Ȯ��</a>

					<input type="hidden" name="num" value="<?=$_REQUEST[num]?>">
					<input type="hidden" name="board" value="<?=$_REQUEST[board]?>">
					<input type="hidden" name="c_num" value="<?=$c_num?>">
					<input type="hidden" name="mode" value="delete">
				</form>
			</div>
		</div>
	</div>
</div>

<script>
	function passForm(){
		var form = document.passwd_form;
		
		if(form.up_passwd.value.length <= 0 || form.up_passwd.value == null){
			alert("��й�ȣ�� �Է��ϼ���.");
			form.up_passwd.focus();
			return;
		}else{
			form.submit();
		}
	}
</script>

<? include_once('footer.php'); ?>