<?
	include_once("header.php");
	include_once($Dir."m/inc/paging_inc.php");

	$curpage=isset($_GET['page'])?trim($_GET['page']):1;
	$boardname = "share";
	$listnum = 5; // �������� �Խñ� ����Ʈ ��
	
	$boardsettingSQL = "SELECT grant_view, grant_write FROM tblboardadmin WHERE board = '".$boardname."' ";
	$boardsettingGrantView=$boardsettingGrantWrite="";
	if(false !== $boardsettingRes = mysql_query($boardsettingSQL,get_db_conn())){
		$boardsettingGrantView=$boardsettingGrantWrite="";
		$boardsettingGrantView = mysql_result($boardsettingRes,0,0);// �Խ��� ��ȸ ���� N: ȸ����ȸ�� ���,�ۺ��� ��� ����, U: ��ȸ���� ��Ϻ��⸸ ����, Y: ȸ��������
		$boardsettingGrantWrite = mysql_result($boardsettingRes,0,0);// �Խ��� ���� ����
	}

	if($boardsettingGrantView== "" || $boardsettingGrantView == "Y"){
		if($_ShopInfo->getMemid() == "" || $_ShopInfo->getMemid() == null){
			echo '<script>alert("��Ϻ��� ������ �����ϴ�.");history.go(-1);</script>';
			exit;
		}
	}
	
	$totallistSQL = "SELECT * FROM tblboard WHERE board = '".$boardname."' ";
	
	if(false !== $totallistRes = mysql_query($totallistSQL,get_db_conn())){
		$totallistrowcount = mysql_num_rows($totallistRes);
		mysql_free_result($totallistRes);
	}else{
		echo '<script>alert("�Խ����� �������� �ʾҽ��ϴ�.");history.go(-1)</script>';exit;
	}

	$listSQL = "SELECT * FROM tblboard WHERE board ='".$boardname."' ORDER BY thread, pos ASC LIMIT ".($listnum * ($curpage - 1)) . ", " . $listnum;
?>
<style>
	.boardInfo{padding:15px;letter-spacing:-1px;}
	.boardInfo li{padding:2px 0px;}
	.linkTypeList{width:100%;padding-top:10px;background:#e9e9e9;overflow:hidden;}
	.contentsBox{width:94%;margin:0 auto;margin-bottom:10px;padding-bottom:10px;background:#ffffff;border:1px solid #e0e0e0;}
	.addFile{margin-bottom:10px;text-align:center;}
	.contentsBox p{padding:0px 10px;}
	.title{margin-bottom:5px;font-size:1.2em;font-weight:700;letter-spacing:-1px;}
	.writer{color:#666666;font-weight:700;padding-right:5px;letter-spacing:-1px;}
	.line{color:#aaaaaa;font-size:10px;text-align:center;}
	.writetime{color:#888888;font-size:11px;padding-left:5px;}
</style>

<div id="content">
	<div class="h_area2">
		<h2>������</h2>
		<a href="main.php" class="btn_home" rel="external"><span class="vc">Ȩ</span></a>
		<a href="javascript:history.back()" class="btn_prev" rel="external"><span>����</span></a>
	</div>

	<ul class="boardInfo">
		<li>- <strong>������</strong>�� ������ SNS�� ��α�, ī�� ���� ���� ������ ���θ� �������� �Խ����Դϴ�.</li>
		<li>- ��ǰ���� �ı⳪ ���θ� ���� ���丮�� ����Ͻ� �� �ּ�(URL)�� �����ּ���.</li>
		<li>- �������� ����Ͻ� ȸ���е� �߿��� <strong>�ſ� 3��</strong>�� �����Ͽ� <strong>������ 5,000��</strong>��, <strong>�ſ� 1��</strong>�� ����Ʈ �������� �����Ͽ� <strong>������ 1����</strong>�� ������ �帳�ϴ�.</li>
	</ul>
	<div class="linkTypeList">
	<?
		if(false !== $listRes = mysql_query($listSQL,get_db_conn())){
			$listrowcount = mysql_num_rows($listRes);
			$imgsrc = $Dir."data/shopimages/board/share/";
			if($listrowcount>0){
				while($listRow = mysql_fetch_assoc($listRes)){
					if(mb_strlen($listRow['title']) > 21){
						$title = _strCut($listRow['title'],21,5,$charset);
					}else{
						$title = $listRow['title'];
					}
;
					$url = $listRow['url'];
					$attech = $listRow['filename'];
					$src = $imgsrc.$attech;
					$writer=$listRow['name'];
					$writetime =date("Y/m/d",$listRow['writetime']);

	?>
		<div class="contentsBox">
			<div class="addFile"><a href="<?=$url?>" target="_blank"><img src="<?=$src?>" width="100%" alt="" /></a></div>
			<p class="title"><a href="<?=$url?>" target="_blank"><?=$title?></a></p>
			<p class="etcInfo"><span class="writer"><?=$writer?></span><span class="line">|</span><span class="writetime"><?=$writetime?></span></p>
		</div>
	<?
				}
			}
		}
	?>
	</div>
	<div id="page_wrap">
			<?
				$pageLink = $_SERVER['PHP_SELF']."?page=%u&board=".$boardname; // ��ũ
				$pagePerBlock = ceil($totallistrowcount/$listnum);
				$paging = new pages($pageparam);
				$paging->_init(array('page'=>$curpage,'total_page'=>$pagePerBlock,'links'=>$pageLink,'pageblocks'=>3))->_solv();
				echo $paging->_result('fulltext');
			?>
	</div>
</div>

<? include_once('footer.php'); ?>