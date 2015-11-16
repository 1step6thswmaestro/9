<?
include_once($Dir."lib/ext/product_func.php");
include_once($Dir."lib/class/pages.php");
$where = array();
$where = _array($where)?' where '.implode(' and ',$where):'';
$sql = "select count(p.pridx) from tblproduct p inner join todaysale t using(pridx) ".$where;
if(false === $res = mysql_query($sql,get_db_conn())) exit(mysql_error());
$total = mysql_result($res,0,0);
$page = _isInt($_REQUEST['page'])?intval($_REQUEST['page']):1;
$perpage = _isInt($_REQUEST['perpage'])?intval($_REQUEST['perpage']):10;
$total_page = max(1,@ceil($total/$perpage));
$page = min($page,$total_page);

$limit = " limit ".($page-1)*$perpage.','.$perpage;
$ordby = " order by pridx desc";

$sql = "select * from tblproduct p inner join todaysale t using(pridx) ".$where.$ordby.$limit;
if(false === $res = mysql_query($sql,get_db_conn())) exit(mysql_error());

$items = array();
$vno = $total - ($page-1)*$perpage;
while($row = mysql_fetch_assoc($res)){
	$row['vno'] = $vno--;
	array_push($items,$row);
}
?>
<style type="text/css">

.todayListTbl{border-top:2px solid #ccc; margin-bottom:10px;}
.todayListTbl thead th{background:#efefef; border-bottom:1px solid #ccc; border-right:1px solid #ccc;  font-size:12px;}
.todayListTbl thead td{background:#efefef; font-weight:bold; text-align:center; border-bottom:1px solid #ccc;  border-right:1px solid #ccc;  font-size:12px;}


.todayListTbl tbody th{background:#fff; border-bottom:1px solid #efefef; border-right:1px solid #efefef; font-size:12px;}
.todayListTbl tbody td{background:#fff; border-bottom:1px solid #efefef; border-right:1px solid #efefef; font-size:12px; text-align:center}


.todayListTbl .noBR{border-right:0px;}

</style>

<div style="background:url(images/title_bg.gif) repeat-x left bottom; padding-bottom:25px;"><IMG SRC="images/todaysale_title01.gif" ALT="투데이세일 상품목록" /></div>
<div style="padding:0px 0px 20px 20px;" class="notice_blue">투데이세일에 등록된 상품목록을 확인할 수 있습니다.</div>

<table width="100%" border="0" cellpadding="0" cellspacing="0" class="todayListTbl">
	<thead>
		<th style="height:30px;">No</th>
		<td colspan="2">상품</td>
		<td>가격</td>
		<td>기간</td>
		<td>판매량</td>
		<td class="noBR">관리</td>
	</thead>
	<tbody>
	<? if(!_array($items)){ ?>
		<tr>
			<td colspan="7" style="height:30px; text-align:center">등록된 상품이 없습니다.</td>
		</tr>
	<? }else{
			foreach($items as $item){ 
				$productinfo = $item['productname'];
				$range = '시작 : '.$item['start'];
				$range .= '<br>종료 : '.$item['end'];
	?>	
		<tr>
			<th style="width:30px;"><?=$item['vno']?></th>
			<td style="width:120px;"><img src="<?=$Dir.'data/shopimages/product/'.$item['tinyimage']?>"></td>
			<td><?=$productinfo?></td>
			<td style="width:150px;"><span style="color:#666"><?=number_format($item['consumerprice'])?></span> -> <span style="color:blue"><?=number_format($item['sellprice'])?></span></td>
			<td style="width:200px;"><?=$range?></td>
			<td style="width:80px;"><?=number_format($item['sellcnt'])?></td>
			<td style=" width:120px;">
				<a href="javascript:editItem('<?=$item['productcode']?>');"><img src="images/btn_edit.gif" border="0" alt="수정" /></a>
				<a href="javascript:deleteItem('<?=$item['productcode']?>');"><img src="images/btn_del.gif" border="0" alt="삭제" /></a>
			</td>
		</tr>
	<? 		} // end foreach
		} ?>
	</tbody>
</table>
<script language="javascript" type="text/javascript">
function editItem(productcode){
	document.location.href= '/admin/todaysale.php?mode=modify&productcode='+productcode;
}
function deleteItem(productcode){
	if(confirm('해당 상품을 정말 삭제 하시겠습니까?')){
		document.deleteform.prcode.value = productcode;
		document.deleteform.submit();
	}
}
</script>
<form name="deleteform" method="post" action="/admin/todaysale/product.process.php">
<input type="hidden" name="prcode" value="" />
<input type="hidden" name="mode" value="delete" />
</form>
<div style="text-align:center; margin-top:10px; margin-bottom:30px;" class="font_size">
<?
$pages = new pages(array('total_page'=>$total_page,'page'=>$page,'pageblocks'=>10,'links'=>"/admin/todaysale.php?page=%u"));
echo $pages->_solv()->_result('fulltext'); ?>
</div>