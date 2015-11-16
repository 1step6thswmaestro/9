<? //
//include_once('header.php'); 
?>

<div id="content">
	<div class="h_area2">
		<h2>공지사항</h2>
		<a href="main.php" class="btn_home" rel="external"><span class="vc">홈</span></a>
		<a href="javascript:history.back()" class="btn_prev" rel="external"><span>이전</span></a>
	</div>
	
	<!-- 공지사항 -->
	<div class="notice">
		<table class="notice_list">
			<thead>
				<tr>
					<th scope="col"><span>날짜</span></th>
					<th scope="col"><span>제목</span></th>	
				</tr>
			</thead>
			<tbody>

<?

$setup[page_num] = 3;
$setup[list_num] = 3;
$code=$_REQUEST["code"];
$block=$_REQUEST["block"];
$gotopage=$_REQUEST["gotopage"];

if ($block != "") {
	$nowblock = $block;
	$curpage  = $block * $setup[page_num] + $gotopage;
} else {
	$nowblock = 0;
}

if (($gotopage == "") || ($gotopage == 0)) {
	$gotopage = 1;
}

	$sql = "SELECT COUNT(*) as t_count FROM tblnotice ";
	$result = mysql_query($sql,get_db_conn());
	$row = mysql_fetch_object($result);
	$t_count = $row->t_count;
	mysql_free_result($result);
	$pagecount = (($t_count - 1) / $setup[list_num]) + 1;

	$sql = "SELECT date,subject FROM tblnotice ";
	$sql.= "ORDER BY date DESC ";
	$sql.= "LIMIT " . ($setup[list_num] * ($gotopage - 1)) . ", " . $setup[list_num];
	$result=mysql_query($sql,get_db_conn());

	$i=0;
	while($row=mysql_fetch_object($result)) {
		
		$i++;
		$date=substr($row->date,0,4)."/".substr($row->date,4,2)."/".substr($row->date,6,2);
		
		if(strlen($row->subject) >= 36){

			$subject = substr($row->subject,0,36)."...";

		}else{
			$subject = $row->subject;
		}
		
		?>
			<tr>
				<td class="date"><span><?=$date?></span></td>
				<td><a href="notice_view.php?code=<?=$row->date?>" rel="external"><?=$subject?></a></td>
			</tr>			
		<?

	}
	mysql_free_result($result);
?>

				
			</tbody>
		</table>
		
		<!-- 페이지네비 -->
	<?	
		$total_block = intval($pagecount / $setup[page_num]);		

		if (($pagecount % $setup[page_num]) > 0) {
			$total_block = $total_block + 1;
		}

		$total_block = $total_block - 1;

		if (ceil($t_count/$setup[list_num]) > 0) {
			// 이전	x개 출력하는 부분-시작
			$a_first_block = "";
			if ($nowblock > 0) {
			//	$a_first_block .= "<a href='javascript:GoPage(0,1);' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='첫 페이지';return true\"><FONT class=\"prlist\">[1...]</FONT></a>&nbsp;&nbsp;";

				$prev_page_exists = true;
			}

			$a_prev_page = "";
			if ($nowblock > 0) {			
			
				$a_prev_page = "<button type=\"button\" onClick='javascript:GoPage(".($nowblock-1).",".($setup[page_num]*($block-1)+$setup[page_num]).");' class=\"pg_btn pg_btn_prev\"><span></span></button>";
				//	$a_prev_page = $a_first_block.$a_prev_page;
				$a_prev_page = $a_prev_page;

			}
			else
			{
				$a_prev_page = "<button type=\"button\" onClick=\"\" class=\"pg_btn pg_btn_prev\"><span></span></button>";
				
			}


			// 일반 블럭에서의 페이지 표시부분-시작			
			if (intval($total_block) <> intval($nowblock)) {
				$print_page .= "";
				for ($gopage = 1; $gopage <= $setup[page_num]; $gopage++) {
					if ((intval($nowblock*$setup[page_num]) + $gopage) == intval($gotopage)) {
						$print_page .= (intval($nowblock*$setup[page_num]) + $gopage)." ";
					} else {
						$print_page .= " <a href='javascript:GoPage(".$nowblock.",".(intval($nowblock*$setup[page_num]) + $gopage).");' class=\"pg_num\">".(intval($nowblock*$setup[page_num]) + $gopage)."</a>";
					}
				}
			} else {
				if (($pagecount % $setup[page_num]) == 0) {
					$lastpage = $setup[page_num];
				} else {
					$lastpage = $pagecount % $setup[page_num];
				}

				for ($gopage = 1; $gopage <= $lastpage; $gopage++) {
					if (intval($nowblock*$setup[page_num]) + $gopage == intval($gotopage)) {
						$print_page .= " <span class=\"pg_num pg_num_on\">".(intval($nowblock*$setup[page_num]) + $gopage)."</span>&nbsp;";
					} else {
						$print_page .= " <a href='javascript:GoPage(".$nowblock.",".(intval($nowblock*$setup[page_num]) + $gopage).");' class=\"pg_num\">".(intval($nowblock*$setup[page_num]) + $gopage)."</a>&nbsp;";
					}
				}
			}		// 마지막 블럭에서의 표시부분-끝
			
			$a_last_block = "";
			if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
				$last_block = ceil($t_count/($setup[list_num]*$setup[page_num])) - 1;
				$last_gotopage = ceil($t_count/$setup[list_num]);

			//	$a_last_block .= "&nbsp;&nbsp;<a href='javascript:GoPage(".$last_block.",".$last_gotopage.");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='마지막 페이지';return true\"><FONT class=\"prlist\">[...".$last_gotopage."]</FONT></a>";

				$next_page_exists = true;
			}
			// 다음 10개 처리부분...

			$a_next_page = "";
			if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
				
				//$a_next_page .= "&nbsp;&nbsp;<a href='javascript:GoPage(".($nowblock+1).",".($setup[page_num]*($nowblock+1)+1).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='다음 ".$setup[page_num]." 페이지';return true\"><FONT class=\"prlist\">[next]</FONT></a>";

				$a_next_page = "<button type=\"button\" onClick='javascript:GoPage(".($nowblock+1).",".($setup[page_num]*($nowblock+1)+1).");' class=\"pg_btn pg_btn_next\"><span></span></button>";

				//$a_next_page = $a_next_page.$a_last_block;
				$a_next_page = $a_next_page;
			}
			else
			{
				$a_next_page = "<button type=\"button\" onClick=\"\" class=\"pg_btn pg_btn_next\"><span></span></button>";
			}
		} else {
			$a_prev_page = "<button type=\"button\" onClick=\"\" class=\"pg_btn pg_btn_prev\"><span></span></button>";				
			$print_page = "<span class=\"pg_num pg_num_on\">1</span>";
			$a_next_page = "<button type=\"button\" onClick=\"\" class=\"pg_btn pg_btn_next\"><span></span></button>";
		}
?>

		<div class="pg pg_num_area3">
		<?=$a_prev_page?>
		<span class="pg_area"><?=$print_page?></span>
		<?=$a_next_page?>
		</div>

		<!-- //페이지네비 -->
	</div>
	<!-- //공지사항 -->
	
</div>

<hr>

<? 
//include_once('footer.php'); 
?>


