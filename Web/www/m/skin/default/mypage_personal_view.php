<? 
//include_once('header.php'); 
?>

<div id="content">
	<div class="h_area2">
		<h2>1:1 문의</h2>
		<a href="main.php" class="btn_home" rel="external"><span class="vc">홈</span></a>
		<a href="javascript:history.back()" class="btn_prev" rel="external"><span>이전</span></a>
	</div>
	<!-- 1:1문의내역 -->
	<div class="mtom">
		<h2>1:1 문의를 통한 문의내역 및 답변을 볼 수 있습니다.</h2>
		<table border="0" cellpadding="0" cellspacing="0" width="100%" class="mtomView">
			<tr>
				<th>문의제목</th>
				<td><?=$_pdata->subject?></td>
			</tr>
			<tr>
				<th>문의일자</th>
				<td><em><?=$date = substr($row->date,0,4)."-".substr($row->date,4,2)."-".substr($row->date,6,2);?></em></td>
			</tr>
			<tr>
				<th>답변여부</th>
				<td>
					<span class="point3">
					<?
						if(strlen($row->re_date)==14) {
							echo "답변이 완료되었습니다.";
						} else {
							echo "답변 대기중입니다.";
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
				<span class="black small">답변내용</span>
				<div class="mtomAcontent"><?=nl2br($_pdata->re_content)?></div>
			</div>
		</div>

		<div class="mtomButton">
			<a href="mypage_personal_list.php" rel="external" class="button white bigrounded"><span>목록보기</span></a>
		</div>
	</div>
	<!-- //1:1문의내역 -->

</div>

<hr>

<? 
//include_once('footer.php'); 
?>