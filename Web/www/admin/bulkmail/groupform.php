<style>
	.formTh {text-align:left; padding-left:12px; height:30; font-size:12px; font-weight:bold; color:#4b4b4b; background-color:#f8f8f8; border-right:1px solid #e3e3e3; border-bottom:1px solid #ededed;}
	.formTd {height:30; background-color:#ffffff; color:#777777; padding:4px 0px 4px 8px; border-bottom:1px solid #ededed;}
	.td_con1 {height:30; background-color:#ffffff; color:#949494; padding:4px 0px 4px 8px; border-bottom:1px solid #ededed;}
	.bulkmailInput {font-family:돋움; height:22px; border:1px solid #d5d5d5; padding:2px;}
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
					<TD width="100%" class="notice_blue"><p>대용량 메일 발송 상세그룹을 추가할 수 있습니다.</p></TD>
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
		<td height="40"><img src="images/market_bulkmail_title_s6.gif"></td>
	</tr>
	<tr>
		<td>
			<?
			if(!empty($_REQUEST['gidx']) && intval($_REQUEST['gidx']) >0){
				$group = $bulkmail->_getGroup($_REQUEST['gidx']);
			}else{
				$group = array();
			}
			?>
			<form name="groupForm" method="post" action="">
				<input type="hidden" name="act" value="group" />
				<input type="hidden" name="md" value="" />
				<input type="hidden" name="gidx" value="<?=$group['gidx']?>" />
				<table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom:10px;">
					<tr><td height="1" bgcolor="#b9b9b9" colspan="2"></tr>
					<tr>
						<th class="formTh" style="width:180px;"><img src="images/icon_point2.gif" width="8" height="11" border="0">그룹명</th>
						<td class="formTd">
							<input type="text" name="gname" value="<?=$group['gname']?>" style="width:200px" class="bulkmailInput" />
						</td>
					</tr>
					<tr>
						<th class="formTh"><img src="images/icon_point2.gif" width="8" height="11" border="0">그룹설명</th>
						<td class="formTd">
							<input type="text" name="memo" value="<?=$group['memo']?>" style="width:95%;" class="bulkmailInput" />
						</td>
					</tr>
					<tr><td height="1" bgcolor="#b9b9b9" colspan="2"></tr>
				</table>
			</td>
		</tr>
		<tr>
			<td height="15"></td>
		</tr>
		<tr>
			<td height="40"><img src="images/market_bulkmail_title_s7.gif"></td>
		</tr>
		<tr>
			<td>
				<table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom:10px;">
					<tr><td height="1" bgcolor="#b9b9b9" colspan="2"></tr>
					<tr>
						<th class="formTh" style="width:180px;"><img src="images/icon_point2.gif" width="8" height="11" border="0">회원등급</th>
						<td class="formTd">
							<input type="hidden" name="member_group_name" value="" />
							<select name="member_group">
								<option value="">전체회원</option>
								<?
								$gquery = "select * from tblmembergroup";
								$gres= mysql_query($gquery,get_db_conn());
								$member_groups = array();
								if($gres){
									while($trow = mysql_fetch_assoc($gres)){										
										$sel = (!empty($group['member_group']) && $group['member_group'] == $trow['group_code'])?'selected':'';
										?>
								<option value="<?=$trow['group_code']?>" <?=$sel?>><?=$trow['group_name']?></option>								
										<?											
									}
								}
								
								?>
							</select>
						</td>
					</tr>
					<!--
					<tr>
						<th class="formTh">
							<select name="skey">
								<option value="">선택</option>
								<option value="id" <?=(($group['skey'] == 'id')?'selected':'')?>>ID</option>
								<option value="name" <?=(($group['skey'] == 'name')?'selected':'')?>>이름</option>
								<option value="email" <?=(($group['skey'] == 'email')?'selected':'')?>>E-mail</option>
							</select>
						</th>
						<td class="formTd">
							<input type="text" name="sval" value="<?=$group['sval']?>" style="width:200px" class="bulkmailInput" />
						</td>
					</tr> -->
					<tr>
						<th class="formTh"><img src="images/icon_point2.gif" width="8" height="11" border="0">가용적립금</th>
						<td class="formTd">
							<input type="text" name="reserve[]" value="<?=$group['reserve'][0]?>" style="width:83px" class="bulkmailInput" /> 원
							~
							<input type="text" name="reserve[]" value="<?=$group['reserve'][1]?>" style="width:84px" class="bulkmailInput" /> 원
						</td>
					</tr>
					<tr>
						<th class="formTh"><img src="images/icon_point2.gif" width="8" height="11" border="0">나이</th>
						<td class="formTd">
							<input type="text" name="age[]" value="<?=$group['age'][0]?>" style="width:83px" class="bulkmailInput" /> 세
							~
							<input type="text" name="age[]" value="<?=$group['age'][1]?>" style="width:84px" class="bulkmailInput" /> 세
						</td>
					</tr>
					<tr>
						<th class="formTh"><img src="images/icon_point2.gif" width="8" height="11" border="0">성별</th>
						<td class="formTd">
							<input type="radio" name="gender" value="" <?=(empty($group['gender'])?'checked':'')?> />
							모든회원
							<input type="radio" name="gender" value="1" <?=(($group['gender'] == '1')?'checked':'')?> />
							남
							<input type="radio" name="gender" value="2" <?=(($group['gender'] == '2')?'checked':'')?> />
							여</td>
					</tr>
					<tr>
						<th class="formTh"><img src="images/icon_point2.gif" width="8" height="11" border="0">가입일</th>
						<td class="formTd">
							<input type="text" name="joindate[]" value="<?=$group['joindate'][0]?>" OnClick="Calendar(this)" style="width:200px" class="bulkmailInput" />
							~
							<input type="text" name="joindate[]" value="<?=$group['joindate'][1]?>" OnClick="Calendar(this)" style="width:200px" class="bulkmailInput" />
						</td>
					</tr>
					<tr>
						<th class="formTh"><img src="images/icon_point2.gif" width="8" height="11" border="0">E-mail 수신</th>
						<td class="formTd">
							<input type="radio" name="news_yn" value=""  <?=(empty($group['news_yn'])?'checked':'')?> />
							모든회원
							<input type="radio" name="news_yn" value="Y" <?=(($group['news_yn'] == 'Y')?'checked':'')?> />
							수신허용회원
							<input type="radio" name="news_yn" value="N" <?=(($group['news_yn'] == 'N')?'checked':'')?> />
							수신안함회원</td>
					</tr>
					<tr>
						<th class="formTh"><img src="images/icon_point2.gif" width="8" height="11" border="0">결혼여부</th>
						<td class="formTd">
							<input type="radio" name="married_yn" value="" <?=(empty($group['married_yn'])?'checked':'')?>/>
							모든회원
							<input type="radio" name="married_yn" value="Y" <?=(($group['married_yn'] == 'Y')?'checked':'')?>/>
							기혼
							<input type="radio" name="married_yn" value="N" <?=(($group['married_yn'] == 'N')?'checked':'')?>/>
							미혼</td>
					</tr>
					<tr>
						<th class="formTh"><img src="images/icon_point2.gif" width="8" height="11" border="0">전화</th>
						<td class="formTd">
							<input type="text" name="home_tel" value="<?=$group['home_tel']?>" style="width:200px" class="bulkmailInput" />
						</td>
					</tr>
					<tr>
						<th class="formTh"><img src="images/icon_point2.gif" width="8" height="11" border="0">휴대전화</th>
						<td class="formTd">
							<input type="text" name="mobile" value="<?=$group['mobile']?>" style="width:200px" class="bulkmailInput" />
						</td>
					</tr>
					<tr><td height="1" bgcolor="#b9b9b9" colspan="2"></tr>
				</table>
				<div style="width:100%;margin-top:5px; text-align:center">
					<a href="javascript:checkForm();"><img src="images/btn_badd2.gif" border="0" /></a><!--<input type="button" value="등록" style="margin-right:5px;" onclick="javascript:checkForm()" />-->
					<a href="javascript:searchList();"><img src="images/market_bulkmail_search.gif" border="0" /></a><!--<input type="button" value="검색" style="margin-right:5px;" onclick="javascript:searchList()" />-->
					<a href="javascript:goList();"><img src="images/market_bulkmail_list.gif" border="0" /></a><!--<input type="button" value="목록" onclick="javascript:goList();" />-->
				</div>
			</form>
			<script language="javascript" type="text/javascript">
			function checkForm(){
				document.groupForm.action = '/admin/bulkmail/process.php';
				document.groupForm.submit();
			}
			
			function searchList(){
				document.groupForm.action = '/admin/bulkmail/searchlist.php';
				document.groupForm.act.value = 'search';
				document.groupForm.target="SearchResult";
				document.groupForm.method="get";
				window.open('', 'SearchResult', 'width=450,height=300,status=yes,resizable=yes,scrollbars=yes')
				document.groupForm.submit();
				
				document.groupForm.action = '/admin/bulkmail.php';
				document.groupForm.target="";
				document.groupForm.method="post";
			}
			
			function goList(){
				history.back();
			}
			</script>
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
