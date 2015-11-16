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
		echo '<script>alert("잘못된 경로로 접근하였습니다.");history.go(-1);</script>';
		exit;
	}
	
	$get_qna_sql = "SELECT * FROM tblboardadmin WHERE board = '".$board_name."' ";
	$get_qna_result = mysql_query($get_qna_sql, get_db_conn());
	$get_qna_row = mysql_fetch_array($get_qna_result);

	$set_qna_list_view =$get_qna_row[grant_view]; // 게시판 조회 권한 N: 회원비회원 목록,글보기 모두 가능, U: 비회원은 목록보기만 가능, Y: 회원만가능
	
	$set_qna_list_write = $get_qna_row[grant_write]; // 게시판 쓰기 권한

	if($set_qna_list_view == "U" || $set_qna_list_view == "Y"){
		if($_ShopInfo->getMemid() == "" || $_ShopInfo->getMemid() == null){ 
			echo '<script>alert("쇼핑몰 회원만 이용 가능합니다.\n로그인 하시기 바랍니다.");history.go(-1);</script>';
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
		<h2>비밀번호 확인</h2>
		<a href="main.php" class="btn_home" rel="external"><span class="vc">홈</span></a>
		<a href="javascript:history.back()" class="btn_prev" rel="external"><span>이전</span></a>
	</div>

	<div id="passwd_container">
		<div class="passwd_box">
			<div class="passwd_top">
				잠금 기능을 사용하여 등록한 게시물입니다.<br />
				관리자 비밀번호나 작성자 비밀번호를 입력하세요.
			</div>
			<div class="passwd_bottom">
				<form name="passwd_form" method="post" action="<?=$location?>">
					<label>비밀번호 &nbsp;:&nbsp;</label>
					<input style="border:1px solid #BBBBBB; width:100px; height:22px;" type="password" name="pass" value=""/>
					<a href="#" class="button black" onClick="passForm();"/>확인</a>

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
			alert("비밀번호를 입력하세요.");
			form.pass.focus();
			return;
		}else{
			form.submit();
		}
	}
</script>

<? include_once('footer.php'); ?>