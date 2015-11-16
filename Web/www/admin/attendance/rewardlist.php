<form name="pageForm" action="<?=$_SERVER['PHP_SELF']?>" method="get">
<input type="hidden" name="aidx" value="<?=$_REQUEST['aidx']?>">
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
<table cellspacing="0" cellpadding="0" width="100%" border="0" class="formTbl">
		<thead>
			<tr>
				<th style="height:30px; width:100px;">No</th>
				<th style="width:120px;">회원</th>
				<th style="width:80px;">보상구분</th>
				<th style="width:120px;">보상내용</th>
				<th>조건</th>								
				<th style="width:120px;">일자</th>
			</tr>
		</thead>
		<tbody>
			<? if($result['total'] < 1){ ?>
			<tr>
				<td colspan='7' style="height:50px; text-align:center">등록된 보상 내역이 없습니다.</td>
			</tr>
			<? }else{ 
					$rewards = $attendance->_get('rewards');
				//	_pr($result);				
		foreach($result['items'] as $item){
				if(_empty($rewards[$item['ridx']]['condstr'])){
					if(!_array($rewards[$item['ridx']])){
						$rewards[$item['ridx']]['condstr'] = '관리자에 의한 정보 변경으로 보상기준이 변경되었습니다.';				}else{
						$rewards[$item['ridx']]['condstr'] = ($rewards[$item['ridx']]['conse']=='1')?'연속':'총';
						$rewards[$item['ridx']]['condstr'] .= $rewards[$item['ridx']]['ranges'].'일 방문시';
						
						switch($rewards[$item['ridx']]['rewtype']){
							case 'reserve':
								$rewards[$item['ridx']]['condstr'] .= number_format($rewards[$item['ridx']]['rewval']).' 원';
								break;
						}
						
						if($rewards[$item['ridx']]['rewmax'] < 0){
							$rewards[$item['ridx']]['condstr'] .= '[ 1회 ]';
						}else if($rewards[$item['ridx']]['rewmax'] >0){
							$rewards[$item['ridx']]['condstr'] .= '[최대 '.number_format($rewards[$item['ridx']]['rewmax']).' 원 ]';
						}else{
							$rewards[$item['ridx']]['condstr'] .= '[ 무제한 반복 ]';
						}					
					}
				}
				$condstr = $rewards[$item['ridx']]['condstr'];
				$typestr = '';
				switch($item['rewtype']){
					case 'reserve':
						$typestr = '적립금';
						$rewstr = number_format($item['rewval']).'원';
						break;						
				}
		?>
			<tr>
				<td style="text-align:center; height:28px"><?=$item['vno']?></td>
				<td style="text-align:center"><?=$item['memid']?></td>
				<td style="text-align:center"><?=$typestr?></td>
				<td style="text-align:center"><?=$rewstr?></td>
				<td style="text-align:center"><?=$condstr?></td>

				<td align='center'><?=$item['rewdate']?></td>
			</tr>
			<?
		}// end foreach 

		
	}?>
		</tbody>
	</table>	
	<?
	$linkstr = "javascript:GoPage('%u','".$result['perpage']."')";
	$pageSet = array('page'=>$result['page'],'total_page'=>$result['total_page'],'links'=>$linkstr,'pageblocks'=>10,'style_pages'=>'%u', // 일반 페이지 
		'style_page_sep'=>'&nbsp;.&nbsp;');
	
	$Opage = new pages($pageSet);
	$Opage->_solv();		
	?>
	<div style="margin-top:10px;">
		<div style="width:10%; float:left"></div>	
		<div style="text-align:center; width:80%; float:left"><? echo $Opage->_result('fulltext'); ?></div>
		<div style="width:10%; float:right; text-align:right;"><button class="listBtn">목록</button></div>
	</div>