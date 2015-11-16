<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/ext/func.php");
include_once($Dir."lib/ext/order_func.php");

if(_empty($_ShopInfo->getMemid())){
	_alert('접근 권한이 없습니다.','0');
	exit;
}

if(!_isInt($_REQUEST['soidx'])){
	_alert('조회 정보가 올바르지 않습니다.','0');
	exit;
}

$sql = "select * from scheduled_delivery_order where soidx='".$_REQUEST['soidx']."'";
if(false === $res = mysql_query($sql,get_db_conn())){
	_alert('DB 연동 오류','0');
	exit;
}else if(mysql_num_rows($res) < 1){
	_alert('정보를 찾을수 없습니다.','0');
	exit;
}

$order = mysql_fetch_assoc($res);


$sql = "select * from scheduled_delivery_schedule s left join scheduled_delivery_order_items i using(soidx,itemseq) where soidx='".$order['soidx']."' and pridx >0 order by drseq asc,itemseq asc";
if(false === $res = mysql_query($sql,get_db_conn())){
	_alert('DB 연동 오류','0');
	exit;
}else if(mysql_num_rows($res) < 1){
	_alert('정보를 찾을수 없습니다.','0');
	exit;
}

$scheduled = array();
$pridxs = array();
while($row = mysql_fetch_assoc($res)){
	if(!isset($scheduled[$row['drseq']]) || !_array($scheduled[$row['drseq']])) $scheduled[$row['drseq']] = array();
	if(!in_array($row['pridx'],$pridxs)) array_push($pridxs,$row['pridx']);
	array_push($scheduled[$row['drseq']],$row);
}


$sql = "select pridx,tinyimage from tblproduct where pridx in('".implode("','",$pridxs)."')";
$pridxs = array();
if(false === $res = mysql_query($sql,get_db_conn())){
	
}else if(mysql_num_rows($res) >0){
	while($item = mysql_fetch_assoc($res)){
		if(!_empty($item['tinyimage']) && file_exists($Dir.DataDir."shopimages/product/".$item['tinyimage'])){
			$item['tinyimage'] = array('ori'=>$item['tinyimage'],'src'=>$Dir.DataDir."shopimages/product/".$item['tinyimage'],'width'=>'','height'=>'');
		}else{
			$item['tinyimage'] = array('ori'=>$item['tinyimage'],'src'=>$Dir."images/no_img.gif",'width'=>'','height'=>'');
		}
		list($item['tinyimage']['width'],$item['tinyimage']['height']) = getImageSize($item['tinyimage']['src']);
		
		$item['tinyimage']['big'] = ($item['tinyimage']['width'] > $item['tinyimage']['height'])?'width':'height';
		$item['tinyimage']['bigsize'] = $item['tinyimage'][$item['tinyimage']['big']];
		$pridxs[$item['pridx']] = $item;
	}
}



?>

<html>
<head>
<title>정기배송</title>
<meta http-equiv="CONTENT-TYPE" content="text/html;charset=EUC-KR">
<META http-equiv="X-UA-Compatible" content="IE=5" >
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<style>
#refundAccount {display:none}
td {font-family:돋음;color:333333;font-size:9pt;}

tr {font-family:돋음;color:333333;font-size:9pt;}
BODY,TD,SELECT,DIV,form,TEXTAREA,center,option,pre,blockquote {font-family:돋음;color:333333;font-size:9pt;}

A:link    {color:333333;text-decoration:none;}

A:visited {color:333333;text-decoration:none;}

A:active  {color:333333;text-decoration:none;}

A:hover  {color:#CC0000;text-decoration:none;}
</style>
<SCRIPT LANGUAGE="JavaScript">
<!--
window.moveTo(10,10);
window.resizeTo(800,650);
window.name="orderpop";
//-->
</SCRIPT>
</head>

<body topmargin=0 leftmargin=0 rightmargin=0 marginheight=0 marginwidth=0>
<center>
<table border=0 cellpadding=0 cellspacing=0 width=100%>
<tr>
	<td align=center style="padding:10,10,10,10">
	<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
	<tr><td align=center height=30 bgcolor=#454545><FONT COLOR="#FFFFFF"><B>정기배송조회</B></FONT></td></tr>
	<tr><td height=10></td></tr>
	<tr>
		<td><span style="float:left"><img src=<?=$Dir?>images/icon_dot.gif border=0 align=absmiddle> <B>배송일정 정보</B></span></td>
	</tr>
	<tr>
		<td>
		<table border=0 cellpadding=0 cellspacing=1 bgcolor=E7E7E7 width=100% style="table-layout:fixed">
		<col width=45></col>
		<col width=50></col>
		<col width=></col>		
		<col width=50></col>
		<col width=120></col>
		<col width=80></col>
		<tr height=28 bgcolor=#F5F5F5>
			<td align=center>회차</td>
			<td align=center colspan="2">상품</td>
			<td align=center>수량</td>
			<td align=center>배송(예정)일</td>
			<td align=center>상태</td>
		</tr>
<?
	foreach($scheduled as $seqidx=>$scitem){ 
		$vno = $seqidx.' 회';
		if($vno > $order['period']) $vno.'(보너스)';
		$rowspan = count($scitem);
		
	?>
		<tr style="background:#fff">
			<td rowspan="<?=$rowspan?>" style="text-align:center"><?=$vno?></td>
<?		for($i=0;$i<$rowspan;$i++){			
			if($i > 0) echo '<tr  style="background:#fff">';
			$pr = $scitem[$i];
			
			$delistat = ($pr['deliverystatus'] == '1')?'배송완료':'배송예정';
			$dedata = !_empty($pr['deliveryeddate'] && $pr['deliverystatus'] == '1')?$pr['deliveryeddate']:$pr['deliverydate'];
			?>			
			<td><img src="<?=$pridxs[$pr['pridx']]['tinyimage']['src']?>" width="50"></td>
			<td><?=$pr['productname']?></td>
			<td style="text-align:center"><?=number_format($pr['quantity'])?>개</td>
			<td align=center><?=$dedata?></td>
			<td align=center><?=$delistat?></td>
		</tr>
<?		}
	}?>
		</table>
		</td>
	</tr>
</table>
<div style="text-align:center">
<a href="javascript:window.close()">[창닫기]</a>
</div>
</body>
</html>
