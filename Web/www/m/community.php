<?
	include_once("./header.php");
	//��ǰ Q&A ���� ����
	$qna_state_sql = "SELECT use_mobile_qna FROM tblmobileconfig WHERE use_mobile_site = 'Y' ";
	$qna_state_reuslt = mysql_query($qna_state_sql, get_db_conn());
	$qna_state_row = mysql_fetch_object($qna_state_reuslt);

	$get_qna_name = _getQnaName($_data->etcfield);
	$get_qna_sql = "SELECT * FROM tblboardadmin WHERE board = '".$get_qna_name."' ";
	$get_qna_result = mysql_query($get_qna_sql, get_db_conn());
	$get_qna_row = mysql_fetch_array($get_qna_result);
	$set_qna_list_view =$get_qna_row[grant_view]; // �Խ��� ��ȸ ���� N: ȸ����ȸ�� ���,�ۺ��� ��� ����, U: ��ȸ���� ��Ϻ��⸸ ����, Y: ȸ��������
?>
<div id="content">
	<div class="h_area2">
		<h2>Ŀ�´�Ƽ</h2>
		<a href="main.php" class="btn_home" rel="external"><span class="vc">Ȩ</span></a>
		<a href="javascript:history.back()" class="btn_prev" rel="external"><span>����</span></a>
	</div>
	<!-- ī�װ� ����Ʈ -->
	<div class="category_list">
	<ul class="list_type02">
		<?
				$boardListSQL = "SELECT board_name, board, grant_view, grant_mobile FROM tblboardadmin WHERE board IN('notice','qna','faq','storytalk','event') ORDER BY FIELD(board,'notice','qna','faq','storytalk','event') ASC";
				if(false !== $boardListRes = mysql_query($boardListSQL,get_db_conn())){
					$boardListrowcount = mysql_num_rows($boardListRes);

					if($boardListrowcount>0){
						$grant_view = $boardname = $section="";
						while($boardListRow = mysql_fetch_assoc($boardListRes)){
							$grant_view = $boardListRow['grant_view'];
							$grant_mobile = $boardListRow['grant_mobile'];
							$boardname = $boardListRow['board_name'];
							$section = $boardListRow['board'];
							$href="";
							if($section != "share"){
								$href = "board_list.php?board=".$section;
							}else{
								$href = "board_share_list.php";
							}
							if($grant_mobile =="Y"){
								if($grant_view == "Y"){
									if(strlen($_ShopInfo->getMemid())>0){
				?>
					<li><a href="<?=$href?>"><?=$boardname?></a></li>		
				<?
									}
								}else{
				?>
					<li><a href="<?=$href?>"><?=$boardname?></a></li>		
				<?
								}
							}
						}
					}
				}
			?>
			<li><a href="member_urlhongbo.php" rel="external">ȫ������������</a></li>
		</ul>
	</div>
	<!-- //ī�װ� ����Ʈ -->

</div>
<?
include_once("./footer.php");
?>