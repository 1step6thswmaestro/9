<?
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once($Dir."lib/shopdata.php");
	include_once("header.php");

	$board_name = isset($_REQUEST[board])? trim($_REQUEST[board]):"";
	$board_num = isset($_REQUEST[num])? trim($_REQUEST[num]):"";
	$board_type = isset($_REQUEST[type])? trim($_REQUEST[type]):"";
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

	if($board_type == "view"){
		$location = "./customer_qna_view.php";
	} else if ($board_type == "modify"){
		$location = "./customer_qna_modify.php";
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
				<form name="passwd_form" method="post" action="<?=$location?>">
					<label>��й�ȣ &nbsp;:&nbsp;</label>
					<input style="border:1px solid #BBBBBB; width:100px; height:22px;" type="password" name="pass" value=""/>
					<a href="#" class="button black" onClick="passForm();"/>Ȯ��</a>

					<input type="hidden" name="num" value="<?=$_REQUEST[num]?>">
					<input type="hidden" name="board" value="<?=$_REQUEST[board]?>">
				</form>
			</div>
		</div>
	</div>
</div>

<script>
	function passForm(){
		var form = document.passwd_form;
		
		if(form.pass.value.length <= 0 || form.pass.value == null){
			alert("��й�ȣ�� �Է��ϼ���.");
			form.pass.focus();
			return;
		}else{
			form.submit();
		}
	}
</script>

<? include_once('footer.php'); ?>