<?
$Dir="../../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/class/bulkmail.php");
include_once($Dir."lib/class/coupon.php");
$bulkmail = new bulkmail();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
<title>대용량 메일 미리보기</title>
</head>
<body>
<style type="text/css">
.formTbl{ border-top:1px solid #ccc; border-left:1px solid #ccc;}
.formTbl th{ background:#efefef; border-bottom:1px solid #ccc; border-right:1px solid #ccc; font-weight:normal; font-size:11px;}
.formTbl td{background:#fff; border-bottom:1px solid #ccc; border-right:1px solid #ccc; padding:2px 0px 2px 5px; font-size:11px;}
</style>

<table border="0" cellpadding="0" cellspacing="0" class="formTbl" style="width:100%">
	<tr>
		<th>보낸 사람</th>
		<td><?=$_REQUEST['sender']?></td>
	</tr>
	<tr>
		<th>받는 사람</th>
		<td><?=$_REQUEST['receiver']?></td>
	</tr>
	<tr>
		<th>제목</th>
		<td><?=$_REQUEST['subject']?></td>
	</tr>
</table>
<div style="width:100%; height:400px; overflow: scroll; border:2px solid #ddd">
<?
$text = $bulkmail->_content($_REQUEST['contents'],$_REQUEST['rejectMsg']);
$text = preg_replace('/<!doctype[^>]+>/i','',$text);
if(preg_match_all('/^<head>([\s.]{1,})<\/head>$/mi',$text,$mat)){
	echo '<pre>';
	echo 'ddddddd';
	print_r($mat);
	echo '</pre>';
}


$sp = stristr($text,'<body');
if(false !== $sp){
	$text = preg_replace('/(<body[^>]*>|<\/(body|html)>)/i','',$sp);
	unset($sp);
}
echo $text;
?>
</div>
<ul style="list-style-type:disc; font-size:11px">
<li>한글 파일명(이미지 등) 은 사용자의 인터넷브라우저 환경에 따라 정상 처리 되지 않을수 있으니 가급적 영문명을 사용하시기 바랍니다.</li>
<li>미리보기에 보이는 내용은 실제 수신 사이트의 정책에 따라 다르게  보여질수 있습니다.<br />
  반드시 테스트 메일 발송 후 정상 유무 확인한 다음에 전체 발송해주세요. </li>
</ul>

<div style="text-align:center">
	<input type="button" value="닫기" onclick="javascript:window.close()" />
</div>
</body>
</html>