<form name="pageForm" action="<?=$_SERVER['PHP_SELF']?>" method="get">
<input type="hidden" name="aidx" value="<?=$_REQUEST['aidx']?>">
<input type="hidden" name="act" value="<?=$_REQUEST['act']?>">
<input type="hidden" name="page" value="<?=$_REQUEST['page']?>">
</form>
<script language="javascript" type="text/javascript">
function GoPage(page){
	document.pageForm.page.value = page;
	document.pageForm.submit();
}
</script>
<div style="padding:10px; background:#efefef; border:1px solid #ccc">
	<span style="font-weight:bold; margin-right:15px;"><?=$attendance->_get('title')?></span>
	<span style="">기간 : <?=substr($attendance->_get('stdate'),0,10)?> ~ <?=substr($attendance->_get('enddate'),0,10)?></span>
</div>
<script language="javascript" type="text/javascript">
	
	
$(function(){
	$(document).on('click','.selAllCheck',function(){		
		if($(this).is(':checked')){
			$('input[name^=delseq]').attr('checked',true);
		}else{
			$('input[name^=delseq]').attr('checked',false);
		}
	});
	
	
	$(document).on('click','input[name^=delseq]',function(){
		if($(this).is(':checked') && $('input[name^=delseq]').length == $('input[name^=delseq]:checked').length){
			$('.selAllCheck').attr('checked',true);
		}else{
			$('.selAllCheck').attr('checked',false);
		}
	});
	
	
	$('.delSTBtn').click(function(e){
		$('#deleteStamp').submit();
	});
	
	$('#deleteStamp').submit(
		function(e){		
			var items = $(this).find('input[name^=delseq]:checked');
			if(items.length <1){
				alert('선택된 항목이 없습니다.');
			}else{
				return;
			}
			e.preventDefault();	
		}
	);
	
});
</script>
<form name="deleteStamp" id="deleteStamp" method="post" action="/admin/attendance/process.php">
<input type="hidden" name="aidx" value="<?=$_REQUEST['aidx']?>" />
<input type="hidden" name="act" value="deleteStamp" />
<table cellspacing="0" cellpadding="0" width="100%" border="0" class="formTbl">
		<thead>
			<tr>
				<th style="height:30px; width:30px; text-align:center"><input type="checkbox" class="selAllCheck" name="selAll" value="" /></th>
				<th style="height:30px; width:100px;">No</th>
				<th style="width:120px;">회원</th>
				<th style="width:130px;">일자</th>
				<th>한줄멘트</th>				
				<th style="width:120px; text-align:center">ip</th>				
			</tr>
		</thead>
		<tbody>
			<? if($result['total'] < 1){ ?>
			<tr>
				<td colspan='5' style="height:50px; text-align:center">등록된 출석 내역이 없습니다.</td>
			</tr>
			<? }else{ 
		foreach($result['items'] as $item){ 
			if(_empty($item['ment'])) $item['ment'] = '&nbsp;';
		?>
			<tr>
				<td style="text-align:center; height:28px"><input type="checkbox" name="delseq[]" value="<?=$item['seq']?>" /></td>
				<td style="text-align:center; height:28px"><?=$item['vno']?></td>
				<td style="text-align:center"><?=$item['memid']?></td>
				<td style="text-align:center"><?=$item['date'].' '.$item['time']?></td>
				<td style="text-align:center"><?=$item['ment']?></td>
				<td style="text-align:center"><?=long2ip($item['ip'])?></td>

			</tr>
			<?
		}// end foreach 

		
	}?>
		</tbody>
	</table>	
</form>
	<?
	$linkstr = "javascript:GoPage('%u','".$result['perpage']."')";
	$pageSet = array('page'=>$result['page'],'total_page'=>$result['total_page'],'links'=>$linkstr,'pageblocks'=>10,'style_pages'=>'%u', // 일반 페이지 
		'style_page_sep'=>'&nbsp;.&nbsp;');
	
	$Opage = new pages($pageSet);
	$Opage->_solv();		
	?>
	<div style="margin-top:10px;">
		<div style="width:10%; float:left"><button class="delSTBtn">선택항목삭제</button></div>	
		<div style="text-align:center; width:80%; float:left"><? echo $Opage->_result('fulltext'); ?></div>
		<div style="width:10%; float:right; text-align:right;"><button class="listBtn">목록</button></div>
	</div>