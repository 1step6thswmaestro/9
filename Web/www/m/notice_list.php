<?
	include_once("header.php");
	include_once($Dir."m/inc/paging_inc.php");
	$curpage=isset($_GET['page'])?trim($_GET['page']):1;
	
	$boardName = "notice";
	$listnum = 10; //�Խñ� ����Ʈ ��

	$boardSQL = "SELECT grant_view tblboardadmin WHERE board = '".$boardName."' ";
	if(false !== $boardRes = mysql_query($boardSQL,get_db_conn())){
		$boardNumRow = mysql_num_rows($boardRes);

		mysql_free_result($boardRes);
		
		if($boardNumRow>0){
			$view_grant = mysql_result($boardRes,0,0); // �Խ��� ��ȸ ���� N: ȸ����ȸ�� ���,�ۺ��� ��� ����, U: ��ȸ���� ��Ϻ��⸸ ����, Y: ȸ��������
		}else{
			echo '<script>alert("������������ ������ �Խ����� �����ϴ�.");history.go(-1)</script>';exit;
		}
	}
	if($view_grant == "Y"){
		if($_ShopInfo->getMemid() == "" || $_ShopInfo->getMemid() == null){
			echo '<script>alert("��Ϻ��� ������ �����ϴ�.");history.go(-1);</script>';
			exit;
		}
	}
	$countSQL = "SELECT COUNT(board) AS rowcount FROM tblboard WHERE board = '".$boardName."' ";
	if(false !== $countRes = mysql_query($countSQL,get_db_conn())){ 
		$rowcount = mysql_result($countRes,0,0);
		mysql_free_result($countRes);
	}
?>
<div id="content">
	<div class="h_area2">
		<h2>��������</h2>
		<a href="main.php" class="btn_home" rel="external"><span class="vc">Ȩ</span></a>
		<a href="javascript:history.back()" class="btn_prev" rel="external"><span>����</span></a>
	</div>
	
	<section id="notice_wrap">
		<div class="rowcount">��ü <?=$rowcount?>���� ���������� �ֽ��ϴ�.</div>
		<div class="board_list">
<?
	$listSQL = "SELECT * FROM tblboard WHERE board = '".$boardName."' ORDER BY writetime DESC ";
	$listSQL .= "LIMIT ". ($listnum * ($curpage - 1)).", ".$listnum;

	if(false !== $listRes = mysql_query($listSQL,get_db_conn())){
		$listNumRow = mysql_num_rows($listRes);
		$writetime="";
		$subject="";
		$num="";
		$thread="";
		if($listNumRow > 0){
			while($listRow = mysql_fetch_assoc($listRes)){
				$writetime = date('Y-m-d',$listRow['writetime']);
				$subject = _strCut($listRow['title'],25,6,$charset);
				$num = $listRow['num'];
				$thread = $listRow['thread'];
?>
			<a href="javascript:noticeView('<?=$num?>','<?=$thread?>','<?=$boardName?>');">
				<p class="title"><?=$subject?></p>
				<p class="writer"><?=$writetime?> <span class="hline">|</span> <?=$listRow['name']?> <span class="hline">|</span> <?=$listRow['access']?></p>
			</a>
<?		
		}
		mysql_free_result($listRes);
	}else{
?>
			<p class="li_notice">��ϵ� ���������� �����ϴ�.</p>
<?
		}
	}else{
?>
			<p class="li_notice">������������ ������ �Խ����� ���ų�,<br/>���� ���� �ʾҽ��ϴ�.</p>
<?
	}
?>
		</div>
	</section>

	<div id="page_wrap">
			<?
				$pageLink = $_SERVER['PHP_SELF']."?page=%u"; // ��ũ
				$pagePerBlock = ceil($rowcount/$listnum);
				$paging = new pages($pageparam);
				$paging->_init(array('page'=>$curpage,'total_page'=>$pagePerBlock,'links'=>$pageLink,'pageblocks'=>3))->_solv();
				echo $paging->_result('fulltext');
			?>
	</div>
</div>

<?
	include_once("footer.php");
?>