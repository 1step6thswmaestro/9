<?
//��ǰ�� ��
$sql_cnt3 = "SELECT COUNT(*) as t_count FROM tblproductreview WHERE productcode='$_GET[productcode]'";
$result_cnt3=mysql_query($sql_cnt3,get_db_conn());
$row_cnt3=mysql_fetch_object($result_cnt3);
$t_cnt3 = (int)$row_cnt3->t_count;

//��ǰ���� ��
$pridx=$_pdata->pridx;
$sql_cnt4 = "SELECT COUNT(*) as t_count FROM tblboard WHERE board='$prqnaboard' and pridx = '$pridx'";
$result_cnt4=mysql_query($sql_cnt4,get_db_conn());
$row_cnt4=mysql_fetch_object($result_cnt4);
$t_cnt4 = (int)$row_cnt4->t_count;
?>

		<div class="pr_detail">
			<section class="tab_area">
				<ul class="tab_type1 tab01">
					<li><a href="productdetail_tab01.php?productcode=<?=$productcode?>&sort=<?=$sort?>#tapTop" rel="external">�⺻����</a></li>
					<!-- <li><a href="productdetail_tab02.php?productcode=<?=$productcode?>&sort=<?=$sort?>" rel="external">������</a></li> -->
					<li class="active"><a href="productdetail_tab03.php?productcode=<?=$productcode?>&sort=<?=$sort?>#tapTop" rel="external">��ǰ��(<?=$t_cnt3?>)</a></li>
					<li><a href="productdetail_tab04.php?productcode=<?=$productcode?>&sort=<?=$sort?>#tapTop" rel="external">��ǰ����(<?=$t_cnt4?>)</a></li>
				</ul>
			</section>
			<!-- //view�� -->

			<!-- TAB3-��ǰ�� -->
			<section class="detail_03">
				<ul class="list_type03">
					<? include "prreview.php"; ?>
				</ul>
			</section>
			<!-- //TAB3-��ǰ�� -->
			
			<!-- ��ư -->
			<!-- <section class="basic_btn_area btn_w1">
				<button type="button" class="basic_btn c1"><span>�ٷα���</span></button>
				<button type="button" class="basic_btn"><span>��ٱ���</span></button>
				<button type="button" class="basic_btn"><span>���ø���Ʈ</span></button>
			</section> -->
			<!-- //��ư -->
		</div>
	</div>
	<!-- //��ǰ DETAIL -->
</div>

<?
	//include_once('footer.php'); 
?>