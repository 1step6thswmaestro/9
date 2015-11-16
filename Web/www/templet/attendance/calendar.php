<script language="javascript" type="text/javascript">
function chgAttendance(){
	var aidx = $j('#selAidx').val();
	document.location.replace='<?=$_SERVER['PHP_SELF']?>?aidx='+aidx;
}

$j(function(){
	$j('#checkStampForm').submit(function(e){
		if($j.trim($j('#checkStampForm').find('textarea[name=ment]').val()).length < 1){
			alert('한줄 메모를 남겨 주세요.');	
			$j('#checkStampForm').find('textarea[name=ment]').focus();
			e.preventDefault();
		}
	});
});
</script>
<style type="text/css">
	#attTitleArea {margin-bottom:5px; height:20px; overflow:hidden;}
	#attTitleArea #attTitle {float:left; font-size:15px; color:#333333; font-weight:bold;}
	#attTitleArea #attRange {float:right;}

	#eventArea {clear:both; width:100%;}
	#calendarArea {width:100%;}

	#calendarTbl {width:100%; border-left:1px solid #efefef; border-top:1px solid #efefef;}
	#calendarTbl thead th {background:#f9f9f9; height:60px; border-right:1px solid #efefef; border-bottom:1px solid #efefef;}
	.weekTitle_0 {color:red;}
	.weekTitle_1 {color:#333333;}
	.weekTitle_2 {color:#333333;}
	.weekTitle_3 {color:#333333;}
	.weekTitle_4 {color:#333333;}
	.weekTitle_5 {color:#333333;}
	.weekTitle_6 {color:blue;}

	#calendarTbl tbody td {height:60px; border-right:1px solid #efefef; border-bottom:1px solid #efefef; text-align:right; padding-right:5px;}
	.dayOfweek_0 {color:red;}
	.dayOfweek_1 {color:#333333;}
	.dayOfweek_2 {color:#333333;}
	.dayOfweek_3 {color:#333333;}
	.dayOfweek_4 {color:#333333;}
	.dayOfweek_5 {color:#333333;}
	.dayOfweek_6 {color:blue;}

	#calendarTbl tbody td.pastday {color:#ccc;}
	#calendarTbl tbody td.currentday {background:#FF6; font-weight:bold;}
	#calendarTbl tbody td.commingday {}
	#calendarTbl tbody td.stampOk {background:url('/images/design/stampok.png') no-repeat; background-position:center;}
	#calendarTbl tbody td.stampFail {background:url('/images/design/stampfail.png') no-repeat; background-position:center;}

	#rewardArea {margin-top:25px;}
	#rewardArea h4 {color:#333333; line-height:26px; padding-bottom:10px;}
	.rewardItem {font-size:13px;}
	#noRewardMsg {width:100%; padding:10px; text-align:center; border:1px solid #efefef}

	#stampTbl {width:100%; margin-top:25px;}
	#stampTbl caption {text-align:left; font-weight:bold; color:#333333; font-size:15px; padding-left:24px; padding-bottom:5px; background:url("/data/design/img/sub/staricon.gif") no-repeat;}
	
	#commentList { margin-top:25px;}
	#commentList h4 {color:#333333; line-height:26px; padding-bottom:10px;}
	#commentList .listTbl {border-top:1px solid #efefef;}
	#commentList .listTbl thead th {background:#f9f9f9; height:28px; border-right:1px solid #efefef; border-bottom:1px solid #efefef;}
	#commentList .listTbl tbody td {height:28px;border-bottom:1px solid #efefef; }
</style>

<? if($attendancelist['total'] < 1){ ?>
<div style="text-align:center;">진행중인 출석 이벤트가 없습니다.</div>
<? }else{ ?>

<?	 if($attendancelist['total'] >1){ ?>
<select id="selAidx" name="aidx" onchange="javascript:chgAttendance()">
	<? foreach($attendancelist['items'] as $attitem){ 
			$sel = ($_REQUEST['aidx'] == $attitem['aidx'])?' selected':'';
	?>
	<option value="<?=$attitem['aidx']?>" <?=$sel?>><?=$attitem['title']?></option>
	<? } ?>
</select>
<? } ?>
<?
$year = date('Y');
$month = date('m');
$currday = date('d');

$stampeddates = !_empty($_ShopInfo->getMemid())?$attendance->_getStamp(true):array();

$startStamp = strtotime($year.'-'.$month.'-01');
$weekTitles = array('일','월','화','수','목','금','토');

$chkfirst = date('w',$startStamp);
$lastday = date('t',$startStamp);
/*
$calendarstart = intval(date('z',$startStamp));

$eventstart = intval(date('z',strtotime($attendance->_get('stdate')))) - $calendarstart;
$eventend = date('z',strtotime($attendance->_get('enddate'))) - $calendarstart+2;
*/
$eventstart = strtotime($attendance->_get('stdate'))-$startStamp;
$eventstart = round($eventstart/(24*3600));
$eventend = strtotime($attendance->_get('enddate')) - $startStamp;
$eventend = round($eventend/(24*3600))+1;

$dispday = 1;

$aleadyStamp = (_array($stampeddates[$year.'-'.$month.'-'.$currday]));	
$totalcnt = count($stampeddates);
?>
<div id="wrapper">
	<div id="attTitleArea">
		<span id="attTitle"><?=$attendance->_get('title')?></span>
		<span id="attRange"><?=substr($attendance->_get('stdate'),0,10)?> ~ <?=substr($attendance->_get('enddate'),0,10)?> (총 :<?=number_format($totalcnt).'회 출석'?>)</span>
	</div>
	<div id="eventArea">
		<div id="calendarArea">
		
		<table border="0" cellpadding="0" cellspacing="0" id="calendarTbl">
			<thead>
				<? foreach($weekTitles as $idx=>$title){ ?>
				<th class="weekTitle_<?=$idx?>"><?=$title?></th>
				<? }// end foreach ?>
			</thead>
			<tbody>
				<?
				for($i=0;$i<7;$i++){
					$class = 'dayOfweek_'.$i;
					
					if(($dispday > 1 && $dispday <= $lastday) || $i >= $dispday+$chkfirst-1){
						
						if($currday == $dispday) $class .= ' currentday';
						else $class .= ($currday > $dispday)?' pastday':' commingday';
						
						$daychkstr = $year.'-'.$month.'-'.sprintf('%02d',$dispday);
						
						if($dispday > $eventstart && $dispday < $eventend && $dispday <= $currday){
							if(_array($stampeddates[$daychkstr])) $class .= ' stampOk';
							else $class.= ' stampFail';
						}
						$daystr = $dispday++;
					}else $daystr = '&nbsp;';

					if($i == 0) echo '<tr>';
					?>
					<td valign="top" class="<?=$class?>"><?=$daystr?></td>
					<?
					if($i == 6){
						echo '</tr>';
						if($dispday < $lastday) $i=-1;
					}
				}
				?>
			</tbody>
		</table>
		</div>
		<div id="rewardArea">
			<h4>출석 이벤트 참여 보상</h4>
			<? 
			$rewards = $attendance->_get('rewards');
			//_pr($rewards);
			if(!_array($rewards)){ ?>
				<span id="noRewardMsg">등록된 참여 보상 목록이 없습니다.</span>
		<?	}else{
				foreach($rewards as $key=>$val){
					$rewtext = '';
					$rewtext .= (($val['conse'] == 1)?'연속 ':'총 ').$val['ranges'].'일 방문';
					
					if(intval($val['rewmax']) > 0) $rewtext .='마다 ';
					
					switch($val['rewtype']){
						case 'reserve':
						$rewtext .= '적립금 <strong>'.number_format($val['rewval']).'원</strong> 지급';
						break;
					}
					
					if(intval($val['rewmax']) > 0) $rewtext .=' (최대 '.number_format($val['rewmax']).'원 까지)';
					else if(intval($val['rewmax']) == 0) $rewtext .='(반복지급)';
				?>
				<div class="rewardItem">- <?=$rewtext?></div>
		<?		} // end foreach
			}?>
		</div>

		<div id="stampArea">
		<? if(_empty($_ShopInfo->getMemid())){ ?>
			<div style="text-align:center; background:#f9f9f9; border:1px solid #f5f5f5; padding:5px 0px; margin-top:10px;">회원 로그인 후 참여하실 수 있습니다. <a href="<?=$Dir.FrontDir?>login.php?chUrl=<?=getUrl()?>">[ 로그인 ]</a></div>
		<? }else if(!$aleadyStamp){ ?>
			<form name="checkStampForm" id="checkStampForm" action="/front/attendance_process.php">
			<input type="hidden" name="aidx" value="<?=$attendance->_get('aidx')?>" />
			<table cellpadding="0" cellspacing="0" border="0" id="stampTbl">
				<caption>출석 이벤트 참여하기</caption>
				<tr>
					<td><textarea name="ment" style="width:100%; height:50px; padding:5px;"></textarea></td>
					<td width="55" align="right"><input type="image" src="/images/design/send_attendance.gif" /></td>
				</tr>
			</table>
			</form>
		<? }else{ ?>
			<div style="text-align:center; background:#f9f9f9; border:1px solid #f5f5f5; padding:5px 0px; margin-top:10px;">오늘은 이미 참여하셨습니다.</div>
		<? } ?>
		</div>
	</div>
	<div id="commentList">
	<form name="pageForm" action="<?=$_SERVER['PHP_SELF']?>" method="get">
		<input type="hidden" name="aidx" value="<?=$attendance->_get('aidx')?>">
		<input type="hidden" name="page" value="<?=$_REQUEST['page']?>">
	</form>
<script language="javascript" type="text/javascript">
function GoPage(page){
	document.pageForm.page.value = page;
	document.pageForm.submit();
}
</script>

		<h4>출석 이벤트 참여내역</h4>
	<?
		$list = $attendance->_getStampList($_REQUEST,true);	
	?>
		<table cellspacing="0" cellpadding="0" width="100%" border="0" class="listTbl">
			<thead>
				<tr>
					<th style="height:30px; width:100px;">No</th>
					<th>한줄멘트</th>
					<th style="width:120px;">회원</th>
					<th style="width:130px;">일자</th>
				</tr>
			</thead>
			<tbody>
				<? if($list['total'] < 1){ ?>
				<tr>
					<td colspan='4' style="height:50px; text-align:center">등록된 출석 내역이 없습니다.</td>
				</tr>
				<? }else{ 
			foreach($list['items'] as $item){
				if(_empty($item['ment'])) $item['ment'] = '&nbsp;';
			?>
				<tr>
					<td style="text-align:center; height:28px"><?=$item['vno']?></td>
					<td style=""><?=$item['ment']?></td>
					<td style="text-align:center"><?=$item['memid']?></td>
					<td style="text-align:center"><?=$item['date']?></td>
					
	
				</tr>
				<?
			}// end foreach 
	
			
		}?>
			</tbody>
		</table>
		<?
		$linkstr = "javascript:GoPage('%u','".$list['perpage']."')";
		$pageSet = array('page'=>$list['page'],'total_page'=>$list['total_page'],'links'=>$linkstr,'pageblocks'=>10,'pages'=>'%u', // 일반 페이지
			'page_sep'=>'&nbsp;');
		
		$Opage = new pages($pageSet);
		$Opage->_solv();
		?>
		<div style="margin-top:10px; text-align:center"><? echo $Opage->_result('fulltext'); ?></div>
	</div>
</div>
<? } ?>