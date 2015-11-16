<?
$Dir="../../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/class/bulkmail.php");
$bulkmail = new bulkmail();
include ("../access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "st-1";
$MenuCode = "counter";

if (!$_usersession->isAllowedTask($PageCode)) {
	include ("../AccessDeny.inc.php");
	exit;
}
?>
<style type="text/css">
.formTbl{ border-top:1px solid #ccc; border-left:1px solid #ccc;}
.formTbl caption{ font-size:12px; padding:5px 0px 3px 0px;}
.formTbl th{ background:#efefef; border-bottom:1px solid #ccc; border-right:1px solid #ccc; font-weight:normal; font-size:12px;}
.formTbl td{background:#fff; border-bottom:1px solid #ccc; border-right:1px solid #ccc; padding:2px 0px 2px 5px; font-size:12px;}
</style>

<?
$result = $bulkmail->detail($_REQUEST);
if(!empty($result['errmsg'])){ ?>
<div style="padding:10 0px; text-align:center; color:red">
<?=$result['errmsg']?>
</div>
<?	}else{

?>

<table width="100%" border="0" cellpadding="0" cellspacing="0" class="formTbl">
	<tr>
		<th style="height:30px; width:120px;">보내는 사람 이름</th>
		<td><?=$result['sender_alias']?></td>
	</tr>
	<tr>
		<th style="height:30px;">보내는 사람 주소</th>
		<td><?=$result['sender']?></td>
	</tr>
	<tr>
		<th style="height:30px;">발송시작</th>
		<td><?=$result['send_time']?></td>
	</tr>
	<tr>
		<th style="height:30px;">발송완료</th>
		<td><?=$result['finish_time']?></td>
	</tr>
	<tr>
		<th style="height:30px;">제목</th>
		<td><?=$result['title']?></td>
	</tr>
	<tr>
		<th style="height:28xp;">내용</th>
		<td><div style="width:100%; height:450px; overflow:auto"><?=$result['content']?></div></td>
	</tr>		
</table>
<?	} ?>	
<div style="text-align:center; margin-top:10px;"		><input type="button" value="닫기" style="width:60px; height:30px;" onclick="javascript:window.close();" /></div>
