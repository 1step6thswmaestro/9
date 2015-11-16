<?
$Dir="../../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

if(strlen($_ShopInfo->getId())==0) exit;

$type=$_REQUEST["type"];
$date=$_REQUEST["date"];

$year=substr($date,0,4);
$mon=substr($date,4,2);
$day=substr($date,6,2);

Header("Content-type: image/gif");
$im=ImagecreateFromGif("img/timeorder_graph.gif");
$im2=ImagecreateFromGif("img/graph_dot.gif");
$im3=ImagecreateFromGif("img/graph_dot2.gif");
$white = ImageColorAllocate($im,255,255,255);
$dot = ImageColorAllocate($im,255,60,0);
$dot2 = ImageColorAllocate($im,127,127,127);
$poly = ImageColorAllocate($im,252,212,166);
$black = ImageColorAllocate($im,0,0,0);
$blue = ImageColorAllocate($im,0,0,255);
$red = ImageColorAllocate($im,255,0,0);

$topvalue=0;

if($type=="d"){
	$prevdate=date("YmdH",mktime(0,0,0,$mon,$day-1,$year));
	if($date==date("Ymd")) $date=date("YmdH",mktime(date("H"),0,0,$mon,$day,$year));
	else $date=$date."99";
	$sql ="SELECT cnt,MID(date,7,4) as hour FROM tblcounterorder ";
	$sql.="WHERE (date>='".$prevdate."' && date<='".$date."') ORDER BY date ";
}else if($type=="w"){
	$prevdate=date("Ymd00",mktime(0,0,0,date("m"),date("d")-7,date("Y")));
	$nextdate=date("Ymd99");
	$sql ="SELECT SUM(cnt) as cnt,MID(date,9,2) as hour FROM tblcounterorder ";
	$sql.="WHERE (date<='".$nextdate."' AND date>='".$prevdate."') GROUP BY hour ";
}else if($type=="m"){
	$date=date("Ym");
	$sql ="SELECT SUM(cnt) as cnt,MID(date,9,2) as hour FROM tblcounterorder ";
	$sql.="WHERE date LIKE '".$date."%' GROUP BY hour ";
}

$result = mysql_query($sql,get_db_conn());
while($row = mysql_fetch_object($result)){
	$time[$row->hour]=$row->cnt;
	if($topvalue<$row->cnt) $topvalue=$row->cnt;
}
mysql_free_result($result);

if($topvalue<10) $max=10;
else if($topvalue<20) $max=20;
else if($topvalue<30) $max=30;
else if($topvalue<40) $max=40;
else if($topvalue<50) $max=50;
else if($topvalue<60) $max=60;
else if($topvalue<70) $max=70;
else if($topvalue<80) $max=80;
else if($topvalue<90) $max=90;
else if($topvalue<100) $max=100;
else {
	$max = ceil(($topvalue*1.1)/10)*10;
}
$value=($max/10);
$top=$max+$value;

//YÁÂÇ¥ ¼ýÀÚ ¸¸µé±â
for($i=0;$i<10;$i++) {
	$num=$value*(10-$i);
	$ynumber=60+((($i/2)-1)*36);
	$xnumber=35-strlen(number_format($num))*3;
	//imageString($im,2,$xnumber,$ynumber,number_format($num),$black);
	imagettftext($im,6,0,$xnumber,$ynumber,$black,"font/kroeger.ttf",number_format($num));
}

$date=$prevdate;
$count=0;
$curhour=date("dH");
if($day==date("d")) $end="YES";
for($i=0;$i<24;$i++){
	if($type=="d"){
		$predate=date("dH",mktime($i,0,0,$mon,$day-1,$year));
		$curdate=date("dH",mktime($i,0,0,$mon,$day,$year));
	}
	$x=45+(($i/2)*52)+5;	//¿À´Ã XÁÂÇ¥
	$x2=45+(($i/2)*52)+5;	//Àü³¯ XÁÂÇ¥
	if($type=="d"){
		$y=201-($time[$curdate]/$top)*200;	//¿À´Ã YÁÂÇ¥
		$y2=201-($time[$predate]/$top)*200;	//Àü³¯ YÁÂÇ¥
	}else{
		$curdate=substr("0".$i,-2);
		$y=201-($time[$curdate]/$top)*200;
	}
	if($i<>0){
		if($type=="d") imageline($im,$prevx2+1,$prevy2,$x2+1,$y2,$dot2);
		if($type<>"d" || ($type=="d" && ($curhour>=$curdate || $end!="YES"))) imageline($im,$prevx+1,$prevy,$x+1,$y,$dot);
	}
	if($i<>0 && $i<>23){
		if($type=="d") ImageCopyResized($im,$im3,$x2-1,$y2-1,0,0,5,5,5,5);
		if($type<>"d" || ($type=="d" && ($curhour>=$curdate || $end!="YES"))) ImageCopyResized($im,$im2,$x-1,$y-1,0,0,5,5,5,5);
	}
	$prevx=$x;$prevy=$y;
	$prevx2=$x2;$prevy2=$y2;
}
ImageGif($im);
ImageDestroy($im);
imageDestroy($im2);
imageDestroy($im3);
?>