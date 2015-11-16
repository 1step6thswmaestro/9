<?
$Dir="../../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

if(strlen($_ShopInfo->getId())==0) exit;

$date=date("YmdH");
$prevdate=date("YmdH",mktime(date("H")-9,0,0,date("m"),date("d"),date("Y")));

Header("Content-type: image/gif");
$im=ImagecreateFromGif("img/main_graph1.gif");
$im2=ImagecreateFromGif("img/graph_dot.gif");
$white = ImageColorAllocate($im,255,255,255);
$dot = ImageColorAllocate($im,255,60,0);
$poly = ImageColorAllocate($im,252,212,166);
$black = ImageColorAllocate($im,0,0,0);
$blue = ImageColorAllocate($im,0,0,255);
$red = ImageColorAllocate($im,255,0,0);

$topvalue=0;

$sql ="SELECT cnt,MID(date,9,2) as hour FROM tblcounter WHERE (date>='".$prevdate."' AND date<='".$date."') ";
$result = mysql_query($sql,get_db_conn());
while($row = mysql_fetch_object($result)){
	$time[$row->hour]=$row->cnt;
	if($topvalue<$row->cnt) $topvalue=$row->cnt;
}
mysql_free_result($result);

if($topvalue<4)         $max=4;
else if($topvalue<8)    $max=8;
else if($topvalue<20)   $max=20;
else if($topvalue<40)   $max=40;
else if($topvalue<80)   $max=80;
else if($topvalue<100)  $max=100;
else if($topvalue<200)  $max=200;
else if($topvalue<400)  $max=400;
else if($topvalue<800)  $max=800;
else if($topvalue<1000) $max=1000;
else if($topvalue<2000) $max=2000;
else if($topvalue<4000) $max=4000;
else                    $max=8000;

$value=($max/4);
$top=$max+$value;

for($i=0;$i<=3;$i++) {
	$num=$value*(4-$i);
	$ynumber=66+((($i/2)-1)*36);
	$xnumber=35-strlen(number_format($num))*3;
	//imageString($im,2,$xnumber,$ynumber,number_format($num),$black);
	imagettftext($im,6,0,$xnumber,$ynumber,$black,"font/kroeger.ttf",number_format($num));
}

$date=$prevdate;
$count=0;
for($i=0;$i<10;$i++){
	$curdate=date("H",mktime(date("H")-9+$i,0,0,date("m"),date("d"),date("Y")));
	$xnumber=40+(($i/2)*52);
	if($i%2==1) {
		//imageString($im,2,$xnumber,100,$curdate,$black);
		imagettftext($im,6,0,$xnumber,108,$black,"font/kroeger.ttf",$curdate);
	}
	$x=$xnumber+6;
	$y=97-($time[$curdate]/$top)*82;
	if($i<>0) imageline($im,$prevx+1,$prevy,$x+1,$y,$dot);
	if(strcmp($topvalue,$time[$curdate])==0){
		//imageString($im,2,$x-5,$y-15,$topvalue,$red);
		imagettftext($im,6,0,$x-5,$y-5,$red,"font/kroeger.ttf",$topvalue);
	}
	if($i<>0 && $i<>9) ImageCopyResized($im,$im2,$x-1,$y-1,0,0,5,5,5,5);
	$prevx=$x;$prevy=$y;
}
ImageGif($im);
ImageDestroy($im);
imageDestroy($im2);
?>