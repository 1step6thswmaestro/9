<? 
include_once("./inc/function.php");
$setup[list_num] = 3;
?>

<div id="content">
	<div class="h_area2">
		<h2>������</h2>
		<a href="main.php" class="btn_home" rel="external"><span class="vc">Ȩ</span></a>
		<a href="javascript:history.back()" class="btn_prev" rel="external"><span>����</span></a>
	</div>
	
	<!-- ������ -->
	<div class="reserve">
		<div class="reserve_list">
			<div class="reserve_list_top">��밡�� ������</div>
			<div class="reserve_list_value"><?=number_format($reserve)?>��</div>
		</div>

		<div class="reserve_prwrap">
			<h2>���� ������ ����</h2>
			<div class="reserve_pr_list">

<?
		$currentPage = $_REQUEST["page"];
		if(!$currentPage) $currentPage = 1; 
		$pagetype="board";
		$sql = "SELECT COUNT(*) as t_count FROM tblreserve ";
		$sql.= "WHERE id='".$_ShopInfo->getMemid()."' ";
		$sql.= "AND date >= '".$s_curdate."' AND date <= '".$e_curdate."' ";
		$result = mysql_query($sql,get_db_conn());
		$row = mysql_fetch_object($result);
		$totalRecord = $row->t_count;

		mysql_free_result($result);
		
		$pagecount = (($totalRecord - 1) / $setup[list_num]) + 1;
		
		$recordPerPage = 3; // �������� �Խñ� ����Ʈ ��
		$pagePerBlock = 5; // ��� ����

		$sql = "SELECT * FROM tblreserve WHERE id='".$_ShopInfo->getMemid()."' ";
		$sql.= "AND date >= '".$s_curdate."' AND date <= '".$e_curdate."' ";
		$sql.= "ORDER BY date DESC LIMIT " . ($recordPerPage * ($currentPage - 1)) . ", " . $recordPerPage;
		$result=mysql_query($sql,get_db_conn());
		$cnt=0;
		while($row=mysql_fetch_object($result)) {
			$number = ($t_count-($setup[list_num] * ($gotopage-1))-$i);
			$date=substr($row->date,0,4)."-".substr($row->date,4,2)."-".substr($row->date,6,2);

			if($cnt>0) {
				echo "<tr>\n";
				echo "	<td height=\"1\" colspan=\"4\" bgcolor=\"#DDDDDD\"></td>\n";
				echo "</tr>\n";
			}

			$ordercode="";
			$orderprice="";
			$orderdata=$row->orderdata;
			if(strlen($orderdata)>0) {
				$tmpstr=explode("=",$orderdata);
				$ordercode=$tmpstr[0];
				$orderprice=$tmpstr[1];
			}
?>
			<table border="0" cellpadding="0" cellspacing="0" width="100%" class="reserve_pr_table">
				<thead>
				<tr>
					<td colspan="2" class="reserve_pr_date"><?=$date?></td>
				</tr>
				</thead>
				<tbody>
				<tr>
					<th>��������</th>
					<td><!--<a href="#" rel="external">-->
						<em><?=$row->content?></em>
					</td>
				</tr>
				<tr>
					<th>�����ݾ�</th>
					<td>
					<?
						if(strlen($orderprice)>0 && $orderprice>0) {
							echo number_format($orderprice);
						} else {
							echo "-";
						}
					?>��
					</td>
				</tr>
				<tr>
					<th class="lastTH">��������</th>
					<td class="lastTD"><span class="point3"><?=number_format($row->reserve)?>��</span></td>
				</tr>
				<!--</a>-->
				</tbody>
			</table>

			<?
			$cnt++;
		}
		mysql_free_result($result);
		if ($cnt==0) {
			echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" class=\"reserve_pr_table\"><tr height=\"28\"><th align=\"center\">�ش系���� �����ϴ�.</th></tr></table>";
		}
?>
		</ul>
		</div>
		</div>


		<div id="paging_container">
			<div id="paging_box">
				<ul>
					<?
						_getPage($totalRecord,$recordPerPage,$pagePerBlock,$currentPage,$pagetype); 
					?>
				</ul>
			</div>
		</div>


	</div>
	<!-- //������ -->
	
</div>

<hr>

<? 
//include_once('footer.php'); 
?>