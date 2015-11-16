<?
$Dir="../../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

if(strlen($_ShopInfo->getId())==0) exit;

$date=$_REQUEST["date"];

$year=substr($date,0,4);
$mon=substr($date,4,2);

$lastdays = array("0","31","28","31","30","31","30","31","31","30","31","30","31");
if(date("L",mktime(0,0,0,$mon,1,$year))) $lastdays[2]="29";

Header("Content-type: image/gif");
$im=ImagecreateFromGif("img/dayvisit_graph.gif");
$im2=ImagecreateFromGif("img/graph_dot.gif");
$white = ImageColorAllocate($im,255,255,255);
$dot = ImageColorAllocate($im,255,60,0);
$dot2 = ImageColorAllocate($im,127,127,127);
$poly = ImageColorAllocate($im,252,212,166);
$black = ImageColorAllocate($im,0,0,0);
$blue = ImageColorAllocate($im,0,0,255);
$red = ImageColorAllocate($im,255,0,0);

$topvalue=0;

if($date>=date("Ym",mktime(0,0,0,date("m")-1,date("d"),date("Y")))) {
	$sql ="SELECT MID(date,7,2) as day,sum(cnt) as cnt FROM tblcounter ";
	$sql.="WHERE date LIKE '".$date."%' GROUP BY day ";
} else {	//1달 후 데이터는 월 데이타 테이블에서 찾는다.
	$sql ="SELECT MID(date,7,2) as day, cnt FROM tblcountermonth ";
	$sql.="WHERE date LIKE '".$date."%'";
}
$result = mysql_query($sql,get_db_conn());
while($row = mysql_fetch_object($result)){
	$time[$row->day]=$row->cnt;
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

//Y좌표 숫자 만들기
for($i=0;$i<10;$i++) {
	$num=$value*(10-$i);
	$ynumber=60+((($i/2)-1)*36);
	$xnumber=35-strlen(number_format($num))*3;
	//imageString($im,2,$xnumber,$ynumber,number_format($num),$black);
	imagettftext($im,6,0,$xnumber,$ynumber,$black,"font/kroeger.ttf",number_format($num));
}

$count=0;
if($date==date("Ym")) $curday=date("d");
else $curday=$lastdays[(int)$mon];
$year=substr($viewdate,0,4);
$month=substr($viewdate,4,2);
for($i=1;$i<=$curday;$i++){
	$curdate=date("d",mktime(0,0,0,$month,$i,$year));
	$x=69+((($i-1)/2)*38);
	$y=201-($time[$curdate]/$top)*200;
	if($i<>1) imageline($im,$prevx+1,$prevy,$x+1,$y,$dot);
	if($i<=$lastdays[(int)$mon] && $i<31) ImageCopyResized($im,$im2,$x-1,$y-1,0,0,5,5,5,5);
	$prevx=$x;$prevy=$y;
}
ImageGif($im);
ImageDestroy($im);
imageDestroy($im2);
?>