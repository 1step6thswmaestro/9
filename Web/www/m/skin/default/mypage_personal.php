<? 
include_once("./inc/function.php");

$currentPage = $_REQUEST["page"];
if(!$currentPage) $currentPage = 1;


$recordPerPage = 5; // �������� �Խñ� ����Ʈ �� 
$pagePerBlock = 3; // ��� ���� 

$pagetype="board";
?>

<div id="content">
	<div class="h_area2">
		<h2>1:1 ����</h2>
		<a href="main.php" class="btn_home" rel="external"><span class="vc">Ȩ</span></a>
		<a href="javascript:history.back()" class="btn_prev" rel="external"><span>����</span></a>
	</div>
	<!-- 1:1���ǳ��� -->
	<div class="mtom">
		<h2>1:1 ���ǳ��� �� �亯�� �� �� �ֽ��ϴ�.</h2>
		<table class="mtom_list">
			<col width="30"></col>
			<col width=""></col>
			<col width="60"></col>
			<col width="80"></col>
			<thead>
				<tr>
					<th scope="col"><span>NO</span></th>
					<th scope="col"><span>����</span></th>
					<th scope="col"><span>�亯</span></th>
					<th scope="col"><span>��¥</span></th>
					<!--<th scope="col"><span>�ۼ���</span></th>-->
				</tr>
			</thead>
			<tbody>
<?
	$setup[list_num] = 5;
	$sql = "SELECT COUNT(*) as t_count FROM tblpersonal ";
	$sql.= "WHERE id='".$_ShopInfo->getMemid()."' ";
	$result = mysql_query($sql,get_db_conn());
	$row = mysql_fetch_object($result);
	$totalRecord = $row->t_count;
	mysql_free_result($result);
	$pagecount = (($t_count - 1) / $setup[list_num]) + 1;

	$sql = "SELECT idx,subject,date,re_date FROM tblpersonal ";
	$sql.= "WHERE id='".$_ShopInfo->getMemid()."' ";
	$sql.= "ORDER BY idx DESC LIMIT " . ($recordPerPage * ($currentPage - 1)) . ", " . $recordPerPage;
	$result = mysql_query($sql,get_db_conn());
	$cnt=0;
	while($row=mysql_fetch_object($result)) {
		$number = ($totalRecord-($setup[list_num] * ($currentPage-1))-$cnt);

		$date = substr($row->date,0,4)."-".substr($row->date,4,2)."-".substr($row->date,6,2);
		$re_date="-";
		if(strlen($row->re_date)==14) {
			$re_date = substr($row->re_date,0,4)."-".substr($row->re_date,4,2)."-".substr($row->re_date,6,2)."(".substr($row->re_date,8,2).":".substr($row->re_date,10,2).")";
		}
		
		if(strlen($row->re_date)==14) {
			$str_reply =  "<a class=\"black smallSE\">�Ϸ�</a>";
		} else {
			$str_reply =  "<a class=\"white smallSE\">���</a>";
		}

?>
			<tr>
				<td align="center"><?=$number?></td>
				<td class="mtomSubject"><a href="mypage_personal_view.php?idx=<?=$row->idx?>" rel="external"><?=strip_tags($row->subject)?></a></td>
				<td align="center"><?=$str_reply?></td>
				<td align="center"><em><?=$date?></em></td>
				<!--<td><em class="point1"><?=$_ShopInfo->getMemid()?> </em></td>-->
			</tr>
<?
	$cnt++;
}
	mysql_free_result($result);
	if ($cnt==0) {
		echo "<tr><td colspan=4 align=center>���ǳ����� �����ϴ�.</td></tr>";
	}
?>
		</table>
	</div>
	<!-- //1:1���ǳ��� -->
	<div class="mtomButton">
		<a href="./mypage_personal_write.php" rel="external" class="button blue bigrounded"><span>�����ϱ�</span></a>
	</div>

	<div id="paging_container">
		<div id="paging_box">
			<ul>
				<?
					_getPage($totalRecord,$recordPerPage,$pagePerBlock,$currentPage,$pagetype, $variable); 
				?>
			</ul>
		</div>
	</div>

</div>




<? 
//include_once('footer.php'); 
?>
