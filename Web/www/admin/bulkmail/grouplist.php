<style>
	.formTh {height:30; font-size:12px; font-weight:bold; color:#4b4b4b; background-color:#f8f8f8; border-right:1px solid #e3e3e3; border-bottom:1px solid #ededed;}
	.formTd {height:30; background-color:#ffffff; color:#777777; padding:4px 0px 4px 8px; border-bottom:1px solid #ededed; border-right:1px solid #ededed;}
	.td_con1 {height:30; background-color:#ffffff; color:#949494; padding:4px 0px 4px 8px; border-bottom:1px solid #ededed;}
	.bulkmailInput {font-family:돋움; border:1px solid #d5d5d5; padding:2px;}
</style>

<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td height="8"></td>
	</tr>
	<tr>
		<td>
			<table width="100%" border=0 cellpadding=0 cellspacing=0>
				<tr>
					<td><IMG SRC="images/market_bulkmail_title.gif"  ALT="대용량메일 발송관리"></td>
				</tr>
				<tr>
					<td width="100%" background="images/title_bg.gif" height="21"></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td height="3"></td>
	</tr>
	<tr>
		<td style="padding-bottom:3pt;">
			<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/distribute_01.gif"></TD>
					<TD COLSPAN=2 background="images/distribute_02.gif"></TD>
					<TD><IMG SRC="images/distribute_03.gif"></TD>
				</TR>
				<TR>
					<TD background="images/distribute_04.gif"></TD>
					<TD class="notice_blue"><IMG SRC="images/distribute_img.gif" ></TD>
					<TD width="100%" class="notice_blue"><p>대용량 메일 발송 상세그룹을 설정할 수 있습니다.</p></TD>
					<TD background="images/distribute_07.gif"></TD>
				</TR>
				<TR>
					<TD><IMG SRC="images/distribute_08.gif"></TD>
					<TD COLSPAN=2 background="images/distribute_09.gif"></TD>
					<TD><IMG SRC="images/distribute_10.gif"></TD>
				</TR>
			</TABLE>
		</td>
	</tr>
	<tr>
		<td height="15"></td>
	</tr>
	<tr>
		<td height="40"><img src="images/market_bulkmail_title_s2.gif"></td>
	</tr>
	<tr>
		<td>
			<form name="groupListForm" id="groupListForm" method="post" action="/admin/bulkmail/process.php">
			<input type="hidden" name="act" value="deleteGroups" />
			<input type="hidden" name="dgidx" value="" />
			<table border="0" cellpadding="0" cellspacing="0" style="width:100%">
				<tr><td height="1" bgcolor="#b9b9b9" colspan="6"></tr>
				<tr>
					<th class="formTh" style="width:40px"><input type="checkbox" name="selAll" value="" /></th>
					<th class="formTh" width="84" align="center">그룹명</th>
					<!-- <th>회원수</th> -->
					<th class="formTh" width="84" align="center">설명</th>
					<th class="formTh" width="84" align="center">상세정보</th>
					<th class="formTh" width="84" align="center">수정</th>
					<th class="formTh" width="84" align="center">삭제</th>
				</tr>
				<?
				$groupList = $bulkmail->_groupList();
				if(count($groupList) < 1){ ?>
				<tr>
					<td colspan="5" style="text-align:center">등록된 그룹이 없습니다.</td>
				</tr>
			<?  }else{
					foreach($groupList as $group){ 
						$detail = unserialize($group['detail']);
						$detailstr = array();
						if(!empty($detail['member_group'])) $detailstr[] = '회원 그룹 : '.$detail['member_group_name'];
						if(!empty($detail['skey']) && !empty($detail['sval'])) $detailstr[] = '검색 조건 : '.(($detail['skey'] != 'id')?($detail['skey'] == 'name')?'이름':'이메일':'ID').' > '.$detail['sval'];		
						if(!empty($detail['gender'])) $detailstr[] = '성 별 : '.(($detail['gender'] == '1')?'남자':'여자');
						if(!empty($detail['reserve'][1])){
							if(!empty($detail['reserve'][0])) $detailstr[] = '가용적립금 : '.number_format($detail['reserve'][0]).' ~ '.number_format($detail['reserve'][1]);
							else $detailstr[] = '가용적립금 : '.number_format($detail['reserve'][1]).' 이하';
						}else if(!empty($detail['reserve'][0])) $detailstr[] = '가용적립금 : '.number_format($detail['reserve'][0]).' 이상';
						
						if(!empty($detail['age'][1])){
							if(!empty($detail['age'][0])) $detailstr[] = '나 이 : '.number_format($detail['age'][0]).' ~ '.number_format($detail['age'][1]);
							else $detailstr[] = '나 이 : '.number_format($detail['age'][1]).' 이하';
						}else if(!empty($detail['age'][0])) $detailstr[] = '나 이 : '.number_format($detail['age'][0]).' 이상';
						
						if(!empty($detail['date'][1])){
							if(!empty($detail['date'][0])) $detailstr[] = '가입일 : '.number_format($detail['date'][0]).' ~ '.number_format($detail['date'][1]);
							else $detailstr[] = '가입일 : '.number_format($detail['date'][1]).' 이전';
						}else if(!empty($detail['date'][0])) $detailstr[] = '가입일 : '.number_format($detail['date'][0]).' 이후';
						
						if(!empty($detail['news_yn'])) $detailstr[] = '메일 수신 : '.(($detail['news_yn'] == 'Y')?'허용':'거부');
						if(!empty($detail['married_yn'])) $detailstr[] = '결혼 여부 : '.(($detail['married_yn'] == 'Y')?'기혼':'미혼');
						
						if(!empty($detail['home_tel'])) $detailstr[] = '집전화 : '.$detail['home_tel'];
						if(!empty($detail['mobile'])) $detailstr[] = '핸드폰 : '.$detail['mobile'];
						
						if(count($detailstr) < 1) $detailstr[] = '전체 회원';
				?>
				<tr>
					<td class="formTd"><input type="checkbox" name="selGroup[]" value="<?=$group['gidx']?>" /></td>
					<td class="formTd" width="150"><span class=font_orange><B><?=$group['gname']?></b></span></td>
					<!-- <td><?=$group['gmembers']?></td> -->
					<td class="formTd" width="250"><?=$group['memo']?></td>
					<td class="formTd"><?=implode('<br>',$detailstr)?>	</td>
					<td class="formTd" width="150"><a href="javascript:editGroup('<?=$group['gidx']?>');"><img src="images/btn_edit.gif" width="50" height="22" border="0"></a><!--<input type="button" value="수정" onclick="javascript:editGroup('<?=$group['gidx']?>');" />--></td>
					<td class="formTd" width="150"><a href="javascript:deleteGroup('<?=$group['gidx']?>');"><img src="images/btn_del.gif" width="50" height="22" border="0"></a><!--<input type="button" value="삭제" onclick="javascript:deleteGroup('<?=$group['gidx']?>');" />--></td>
				</tr>
			<?	}
				}
				?>
				<tr><td height="1" bgcolor="#b9b9b9" colspan="6"></tr>
				<tr>
					<td colspan="6" height="52" align="center" valign="bottom">
				<!--<caption style="text-align:left">-->
					<img src="images/btn_badd2.gif" border="0" class="btn_groupadd" /><!--<input type="button" value="그룹추가" style="margin-right:5px" class="btn_groupadd" />-->
					<img src="images/btn_del3.gif" border="0" class="btn_groupdelete" /><!--<input type="button" value="그룹삭제" style="margin-right:5px" class="btn_groupdelete" />-->
				<!--</caption>-->
					</td>
				</tr>
			</table>
			</form>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td>
			<table width="100%" border=0 cellpadding=0 cellspacing=0>
				<tr>
					<td><IMG SRC="images/manual_top1.gif" width=15 height="45" ALT=""></td>
					<td><IMG SRC="images/manual_title.gif" width=113 height="45" ALT=""></td>
					<td width="100%" background="images/manual_bg.gif" height="35"></td>
					<td background="images/manual_bg.gif"></td>
					<td background="images/manual_bg.gif"><IMG SRC="images/manual_top2.gif" width=18 height="45" ALT=""></td>
				</tr>
				<tr>
					<td background="images/manual_left1.gif"></td>
					<td COLSPAN=3 width="100%" valign="top" bgcolor="white" style="padding-top:8pt; padding-bottom:8pt; padding-left:4pt;" class="menual_bg"> </td>
					<td background="images/manual_right1.gif"></td>
				</tr>
				<tr>
					<td><IMG SRC="images/manual_left2.gif" width=15 HEIGHT=8 ALT=""></td>
					<td COLSPAN=3 background="images/manual_down.gif"></td>
					<td><IMG SRC="images/manual_right2.gif" width=18 HEIGHT=8 ALT=""></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td height="50"></td>
	</tr>
</table>
<script language="javascript" type="text/javascript">
function deleteGroup(target){
	if(target == 'sel'){		
		if($('input:checkbox[name^=selGroup]:checked').length < 1){
			alert('선택된 그룹이 없습니다.');
		}else{
			$('#groupListForm>input:hidden[name=act]').val('deletegroups');
			$('#groupListForm>input:hidden[name=dgidx]').val('');
		}
		if(confirm('정말 삭제하시겠습니까?')){
			$('#groupListForm').submit();
		}
	}else if(parseInt(target) > 0){
		$('#groupListForm>input:hidden[name=act]').val('deletegroup');
		$('#groupListForm>input:hidden[name=dgidx]').val('target');
		$('input:checkbox[name^=selGroup]').each(function(index, el) {
			$(el).removeAttr('checked');
		});
		if(confirm('정말 삭제하시겠습니까?')){
			$('#groupListForm').submit();
		}
	}else{
		alert('잘못된 호출 입니다.');
	}
}


function editGroup(gidx){
	document.location.href='/admin/bulkmail.php?act=group&md=edit&gidx='+gidx;	
}
$(function(){
	$('.btn_groupadd').click(function(e) {
		document.location.href="/admin/bulkmail.php?act=group&md=edit";
	});
	
	$('.btn_groupdelete').click(function(e) {
		if($('input:checkbox[name^=selGroup]:checked').length < 1){
			alert('선택된 그룹이 없습니다.');
		}else{
			
		}
	});
	
});
</script>