<? 
//include_once('header.php'); 
?>
<?
$query_notice = "SELECT * FROM tblnotice where date = '$_GET[code]'";
$result_notice = mysql_query($query_notice,get_db_conn());
$row_notice = mysql_fetch_array($result_notice);

			$y = substr($row_notice[date],0,4);
			 $m = substr($row_notice[date],4,2);
			 $d = substr($row_notice[date],6,2);
?>
<div id="content">
	<div class="h_area2">
		<h2>��������</h2>
		<a href="main.php" class="btn_home" rel="external"><span class="vc">Ȩ</span></a>
		<a href="javascript:history.back()" class="btn_prev" rel="external"><span>����</span></a>
	</div>
	
	<!-- �������� -->
	<div class="notice">
		<table class="notice_list view">
			<thead>
				<tr>
					<th scope="col"><span>��¥</span></th>
					<th scope="col"><span>����</span></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="date"><span><?=$y?>.<?=$m?>.<?=$d?></span></td>
					<td><a href="notice_view.php" rel="external"><?=$row_notice[subject]?></a></td>
				</tr>
				<tr>
					<td colspan="2" class="view_con"><span><?=nl2br($row_notice[content])?></span></td>
				</tr>
			</tbody>
		</table>
		
		<div class="notice_list_bt">
			<a class="button white bigrounded" href="notice_list.php" rel="external"><span>��Ϻ���</span></a>
		</div>
	</div>
	<!-- //�������� -->
	
</div>

<hr>

<? 
//include_once('footer.php'); 
?>