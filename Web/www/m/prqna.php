<?
$pridx=$_pdata->pridx;

$curpage = !_empty($_GET['page'])?trim($_GET['page']):1;

$targetname = $qnasetup->board;
$listnum = 3;

$rowcountSQL ="SELECT thread FROM tblboard WHERE board = '".$targetname."' AND pridx = '".$pridx."' AND depth = '0' ";

if(false !== $rowcountRes = mysql_query($rowcountSQL, get_db_conn())){
	$boardrowcount = mysql_num_rows($rowcountRes);
	mysql_free_result($rowcountRes);
}

$qna_sql = "SELECT * FROM tblboard WHERE board='$prqnaboard' and pridx = '".$pridx."' ORDER BY thread ASC, pos LIMIT ". ($recordPerPage * ($currentPage - 1)) . ", " . $recordPerPage;
$qna_result=mysql_query($qna_sql,get_db_conn());
$qna_num_rows = mysql_num_rows($qna_result);

	//$get_qna_name = _getQnaName($_data->etcfield);
	
	$get_qna_sql = "SELECT * FROM tblboardadmin WHERE board = '".$targetname."' ";


	$get_qna_result = mysql_query($get_qna_sql, get_db_conn());
	$get_qna_row = mysql_fetch_array($get_qna_result);
	
	$set_qna_list_view =$get_qna_row[grant_view]; // �Խ��� ��ȸ ���� N: ȸ����ȸ�� ���,�ۺ��� ��� ����, U: ��ȸ���� ��Ϻ��⸸ ����, Y: ȸ��������
	$set_qna_list_write = $get_qna_row[grant_write]; // �Խ��� ���� ����

	if($set_qna_list_view == "Y"){
		if($_ShopInfo->getMemid() == "" || $_ShopInfo->getMemid() == null){
			echo '<script>alert("��Ϻ��� ������ �����ϴ�.");history.go(-1);</script>';
			exit;
		}
	}
	
	// ����ϼ� Q&A ���� ���� ����
	$qna_state_sql = "SELECT use_mobile_qna FROM tblmobileconfig WHERE use_mobile_site = 'Y' ";
	$qna_state_reuslt = mysql_query($qna_state_sql, get_db_conn());
	$qna_state_row = mysql_fetch_object($qna_state_reuslt);


?>
	<div class="qna">
		<span><strong><?=$boardrowcount?>��</strong>�� ��ǰ���ǰ� �ֽ��ϴ�.</span>
		<?
		if($set_qna_list_write == "Y" || $set_qna_list_write == "A"){
			if($_ShopInfo->getMemid() != "" || $_ShopInfo->getMemid() != null){
		?>
			<div style="float:right; margin-right:10px;"><a class="button blue small" href="./customer_qna_write.php?board=qna&pridx=<?=$pridx?>" rel="external">�����ϱ�</a></div>
		<?
			}
		}else if($set_qna_list_write == "N"){
		?>
			<div style="float:right; margin-right:10px;"><a class="button blue small" href="./customer_qna_write.php?board=qna&pridx=<?=$pridx?>" rel="external">�����ϱ�</a></div>
		<?}?>
		
		<table class="qna_list">
			<thead>
				<tr>
					<th scope="col" class="head_date">��¥</th>
					<th scope="col" class="head_title">����</th>
					<th scope="col" class="head_writer">�ۼ���</th>
				</tr>
			</thead>
			<tbody>
				<?
				if($qna_state_row->use_mobile_qna == "Y"){
						$boardListSQL = "SELECT * FROM tblboard WHERE board = '".$targetname."' AND pridx = '".$pridx."' ORDER BY thread ASC ";

						if(false !== $boardListRes = mysql_query($boardListSQL,get_db_conn())){
							while($boardListRow = mysql_fetch_assoc($boardListRes)){
								$subject = _strCut($boardListRow['title'],16,5,$charset);
								if($boardListRow['pos'] >= "1"){
									$printsubject = '<img src="./images/re_mark.gif"/> '.$subject;
								}else{
									$printsubject = $subject;
								}
								$printsubject = $subject;
								$writer = $boardListRow['name'];
								$regdate = date("Y.m.d",$boardListRow['writetime']);
								if($boardListRow['is_secret'] == "1"){
									$link='javascript:isSecret();';
								}else{
									
									$link='productdetail_tab04_view.php?productcode='.$productcode.'&sort='.$sort.'&num='.$boardListRow['num'].'#tapTop';
								}
				?>
				<tr>
					<td class="date"><div class="cell_td"><?=$regdate?></div></td>
					<td class="title"><a class="page_block" href="<?=$link?>" rel="external"><?=$printsubject?></a></td>
					<td class="writer"><div class="cell_td"><?=$writer?></div></td>

				</tr>

				<?
							}
						}else{
				?>

					<tr>
						<td colspan="3" class="err_td">��ϵ� ���� �����ϴ�.</td>
					</tr>
				<?
						}
					}else{
		?>
				<tr>
					<td colspan="3" class="err_td"><span>����ϼ� ��ǰQ&A ���� ������ �Ǿ����� �ʽ��ϴ�.</span></td>
				</tr>
		<?
					}
		?>
			</tbody>
		</table>
	</div>
	<div id="page_wrap">
			<?
				$pageLink = $_SERVER['PHP_SELF']."?productcode=".$productcode."&page=%u"; // ��ũ
				$pagePerBlock = ceil($boardrowcount/$listnum);
				$paging = new pages($pageparam);
				$paging->_init(array('page'=>$curpage,'total_page'=>$pagePerBlock,'links'=>$pageLink,'pageblocks'=>3))->_solv();
				echo $paging->_result('fulltext');
			?>
	</div>
	<script>
		function isSecret(){
			alert("�ش� ���� ���� ��ݱ���� ������ �Խñ۷�\���� �Խ��ǿ� ���ż� Ȯ���ϼž� �մϴ�.");
		}
	</script>