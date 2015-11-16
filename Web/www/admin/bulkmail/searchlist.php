<?
$Dir="../../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/class/bulkmail.php");
include_once($Dir."lib/class/pages.php");
$bulkmail = new bulkmail();
include ("../access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "st-1";
$MenuCode = "counter";

if (!$_usersession->isAllowedTask($PageCode)) {
	include ("../AccessDeny.inc.php");
	exit;
}

$_REQUEST['perline'] = 30;
if($_REQUEST['stype'] != 'member' || !empty($_REQUEST['skey'])){	
	$result = $bulkmail->_searchGroupTarget($_REQUEST);	
}
if(isset($result['total'])){
	$linkstr = "javascript:goSearchPage('%u')";
	$pageSet = array('page'=>$result['page'],'total_page'=>$result['total_page'],'links'=>$linkstr,'pageblocks'=>$cond['page_num'],'style_pages'=>'%u', // 일반 페이지 
		'style_page_sep'=>'&nbsp;.&nbsp;');

	$Opage = new pages($pageSet);
	$Opage->_solv();
}
?>
<form name="searchPageForm" method="get" action="<?=$_SERVER['PHP_SELF']?>">
<?
	foreach($_REQUEST as $key=>$val){ 
		if($key == 'page') continue;
		if(is_array($val)){
			for($i=0;$i<count($val);$i++){ ?>
	<input type="hidden" name="<?=$key?>[]" value="<?=$val[$i]?>" />			
<?			}
		}else{ ?>	
	<input type="hidden" name="<?=$key?>" value="<?=$val?>" />
	
<? 		}
	}
 ?>
<input type="hidden" name="page" value="1"  />
</form>
<script language="javascript" type="text/javascript">
function goSearchPage(p){
	document.searchPageForm.page.value = p;
	document.searchPageForm.submit();
}
</script>
<style type="text/css">
.formTbl{ border-top:1px solid #ccc; border-left:1px solid #ccc;}
.formTbl th{ background:#efefef; border-bottom:1px solid #ccc; border-right:1px solid #ccc; font-weight:normal; font-size:11px;}
.formTbl td{background:#fff; border-bottom:1px solid #ccc; border-right:1px solid #ccc; padding:2px 0px 2px 5px;}
</style>
<? if($_REQUEST['stype'] == 'member'){ ?>
<form name="searchForm2" method="get" action="<?=$_SERVER['PHP_SELF']?>">
<input type="hidden" name="stype" value="member" />
<table border="0" cellpadding="0" cellspacing="0" class="formTbl" style="width:100%">
	<tr>
		<th class="formTh">
			<select name="skey">
				<option value="">선택</option>
				<option value="id" <?=(($_REQUEST['skey'] == 'id')?'selected':'')?>>ID</option>
				<option value="name" <?=(($_REQUEST['skey'] == 'name')?'selected':'')?>>이름</option>
				<option value="email" <?=(($_REQUEST['skey'] == 'email')?'selected':'')?>>E-mail</option>
			</select>
		</th>
		<td class="formTd">
			<input type="text" name="sval" value="<?=$_REQUEST['sval']?>" style="width:200px" class="bulkmailInput" />
			<input type="submit" value="검색" />
		</td>
	</tr>
</table>
</form>
<? } 
?>
<? if(isset($result['total'])){ ?>

<table border="0" cellpadding="0" cellspacing="0" class="formTbl" style="width:100%">
	<tr>
		<th style="height:30px;">No</th>		
		<th>E-mail</th>
		<th>이름</th>
		<th>Mobile</th>
	</tr>
	<? if($result['total'] <1){ ?>
	<tr>
		<td colspan="4" style="text-align:center; font-size:12px; padding:5px 0px">검색된 대상 회원이 없습니다.</td>
	</tr>
	<? }else{
			foreach($result['items'] as $item){ ?>
	<tr>
		<td><?=$item['vno']?></td>	
		<td><?=$item['email']?></td>	
		<td><?=$item['name']?></td>	
		<td><?=$item['mobile']?></td>	
	</tr>
		<? }
	} ?>
</table>
<div style="text-align:center; font-size:12px; margin:5px 0px;"><?=$Opage->_result('fulltext')?></div>
<? } ?>
<div style="text-align:center"><input type="button" value="닫기" onclick="javascript:window.close()" style="width:50px; height:30px;" /></div>
