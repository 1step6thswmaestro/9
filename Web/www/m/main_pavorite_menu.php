<!-- 자주가는 서비스 -->
<?
	$upload_path = "../m/upload/";
	$query_t = "SELECT * FROM tblmobiledirectmenu ORDER BY date DESC";
	$result_t = mysql_query($query_t,get_db_conn());
	$row_num = mysql_num_rows($result_t);
	$i = 0;
	if($row_num > 0){
	?>
<section class="main_service">
	<div class="h_area"><h3>자주가는 서비스</h3></div>

	<ul class="svc_list">

<?
	while($row_t=mysql_fetch_array($result_t))
	{
		?>
		<li><a href="http://<?=$row_t[url]?>" rel="external"><div class="icon_area"><img src="<?=$upload_path.$row_t[image]?>" class="img_large"></div><div class="txt_area"><?=substr($row_t[title],0,12)?></div></a></li>
		<?	
	}
	
?>
	</ul>
</section>
<?}?>
<!-- //자주가는 서비스 -->