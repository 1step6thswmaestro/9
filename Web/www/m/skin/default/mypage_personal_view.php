<? 
//include_once('header.php'); 
?>

<div id="content">
	<div class="h_area2">
		<h2>1:1 ����</h2>
		<a href="main.php" class="btn_home" rel="external"><span class="vc">Ȩ</span></a>
		<a href="javascript:history.back()" class="btn_prev" rel="external"><span>����</span></a>
	</div>
	<!-- 1:1���ǳ��� -->
	<div class="mtom">
		<h2>1:1 ���Ǹ� ���� ���ǳ��� �� �亯�� �� �� �ֽ��ϴ�.</h2>
		<table border="0" cellpadding="0" cellspacing="0" width="100%" class="mtomView">
			<tr>
				<th>��������</th>
				<td><?=$_pdata->subject?></td>
			</tr>
			<tr>
				<th>��������</th>
				<td><em><?=$date = substr($row->date,0,4)."-".substr($row->date,4,2)."-".substr($row->date,6,2);?></em></td>
			</tr>
			<tr>
				<th>�亯����</th>
				<td>
					<span class="point3">
					<?
						if(strlen($row->re_date)==14) {
							echo "�亯�� �Ϸ�Ǿ����ϴ�.";
						} else {
							echo "�亯 ������Դϴ�.";
						}
					?>
					</span>
				</td>
			</tr>
		</table>

		<div class="mtomQnA">
			<div class="mtomQ">
				<div><?=$_pdata->subject?><br /><?=nl2br($_pdata->content)?></div>
			</div>
			<div class="mtomA">
				<span class="black small">�亯����</span>
				<div class="mtomAcontent"><?=nl2br($_pdata->re_content)?></div>
			</div>
		</div>

		<div class="mtomButton">
			<a href="mypage_personal_list.php" rel="external" class="button white bigrounded"><span>��Ϻ���</span></a>
		</div>
	</div>
	<!-- //1:1���ǳ��� -->

</div>

<hr>

<? 
//include_once('footer.php'); 
?>