<SCRIPT LANGUAGE="JavaScript">
	<!--
	function chkCommentForm() {
		if (!comment_form.up_name.value) {
			alert('이름을 입력 하세요.');
			comment_form.up_name.focus();
			return false;
		}

		if (!comment_form.up_passwd.value) {
			alert('패스워드를 입력 하세요.');
			comment_form.up_passwd.focus();
			return false;
		}

		if (!comment_form.up_comment.value) {
			alert('내용을 입력 하세요.');
			comment_form.up_comment.focus();
			return false;
		}
	}
	
	function nologin() {
		if ( confirm("댓글 쓰기는 로그인이 필요 합니다! 로그인 하시겠습니까?") ) {
			location.href = "./login.php?chUrl=<?=getUrl()?>";
		}
	}
	//-->
</SCRIPT>
<?
	$usercheck="";
	
	$checkform = 'javascript:document.comment_form.submit();';
	if($_ShopInfo->getMemid() ==""){
		$usercheck = 'onFocus="nologin();"';
		$checkform='nologin();';
	}
?>
<!-- 간단한 답변글 쓰기 -->
<form method=post name=comment_form action="comment_result.php" onSubmit="return chkCommentForm();" enctype="multipart/form-data">
<input type=hidden name=pagetype value="comment_result">
<input type=hidden name=board value="<?=$board_name?>">
<input type=hidden name=num value="<?=$view_num?>">
<input type=hidden name=block value="<?=$block?>">
<input type=hidden name=gotopage value="<?=$gotopage?>">
<input type=hidden name=search value="<?=$search?>">
<input type=hidden name=subCategory value="<?=$subCategory?>">
<input type=hidden name=s_check value="<?=$s_check?>">
<input type=hidden name=frametype value="<?=$frametype?>">
<input type=hidden name=mode value="up">

<style>
	.commentwrite {margin:25px 7px 10px 7px; padding-bottom:7px;}
	.commentwrite h4 {padding-left:7px; padding-bottom:5px;}
	.commentwrite .input {border:1px solid #d1d1d1; width:50%; height:24px; line-height:24px;}
	.commentwrite .writerinfo {border-top:2px solid #333333;}
	.commentwrite .writerinfo {margin-bottom:7px;}
	.commentwrite .writerinfo th {width:25%; text-align:left; padding:5px; border-bottom:1px solid #efefef; background:#f5f5f5;}
	.commentwrite .writerinfo td {padding:7px 10px 7px 5px; border-bottom:1px solid #efefef;}
</style>

<div class="commentwrite">
	<h4>댓글작성</h4>
	<table border="0" cellpadding="0" cellspacing="0" width="100%" class="writerinfo">
		<? if (strlen($member[name])>0) { ?>
		<tr>
			<th>· 작성자</th>
			<td><?= $member[name] ?><input type="hidden" name="up_name" value="<?=$member[name]?>" <?=$usercheck?> class="input" /></td>
		</tr>
		<? }else{ ?>
		<tr>
			<th>· 작성자</th>
			<td><input type="text" name="up_name" size="12" maxlength="10" value="" <?=$usercheck?> class="input" /></td>
		</tr>
		<tr>
			<th>· 비밀번호</th>
			<td><INPUT type="password" name="up_passwd" value="" maxLength="20" size="12" <?=$usercheck?> class="input" /></td>
		</tr>
		<? } ?>
		<tr>
			<th>· 내용</th>
			<td><textarea name="up_comment" <?=$usercheck?> style="width:98%; height:50px; line-height:17px; border:solid 1px #BDBDBD; font-size:9pt; color:333333;"></textarea></td>
		</tr>
		<? if($setup[fileYN] == "Y"){ ?>
		<tr>
			<th>· 파일첨부</th>
			<td><?=$cmtFile?></td>
		</tr>
		<? } ?>
		<tr>
			<th></th>
			<td><input type="button" class="button white medium" onclick="<?=$checkform?>" value="등록하기" /></td>
		</tr>
	</table>
</div>

</FORM>