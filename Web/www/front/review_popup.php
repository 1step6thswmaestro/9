<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$reviewlist=$_data->ETCTYPE["REVIEWLIST"];
$reviewdate=$_data->ETCTYPE["REVIEWDATE"];
if(strlen($reviewlist)==0) $reviewlist="N";

$num=$_REQUEST["num"];
$prcode=$_REQUEST["prcode"];

$sql = "SELECT * FROM tblproductreview WHERE num='".$num."' AND productcode='".$prcode."' ";
$result=mysql_query($sql,get_db_conn());

if($row=mysql_fetch_object($result)) {
	if ($reviewdate!="N") {
		$date=substr($row->date,0,4)."/".substr($row->date,4,2)."/".substr($row->date,6,2);
	} else {
		$date="";
	}
	$tmp_content=explode("=",$row->content);
	$writer=$row->name;
	for($i=0;$i<$row->marks;$i++) $mark.="<FONT color=\"#000000\" style=\"font-family:'µ¸¿ò,±¼¸²';font-size:11px;letter-spacing:-0.5pt;\">¡Ú</FONT>";

	$content="<table border=0 cellpadding=0 cellspacing=0>\n";
	$content.="<tr><td>".ereg_replace("\n","<br>",$tmp_content[0])."</td></tr>\n";

	$reviewimgwidth = 0;
	if(!empty($row->img) && file_exists($Dir.DataDir."shopimages/productreview/".$row->img)){
		$content.="<tr><td><img src=\"".$Dir.DataDir."shopimages/productreview/".$row->img."\" border='0' style='margin-bottom:5px;' width='__reviewImgWidth__' /></td></tr>\n";
		$temp = getimageSize($Dir.DataDir."shopimages/productreview/".$row->img);
		$reviewimgwidth =  $temp[0];
	}

	if(strlen($tmp_content[1])>0) {
		$content.="<tr><td height=5></td></tr>\n";
		$content.="<tr><td><img src='".$Dir."images/common/review/review_replyicn2.gif' align=absmiddle border=0> ".$tmp_content[1]."</td></tr>\n";
	}
	$content.="</table>\n";
} else {
	echo "<html><head><title></title></head><body onload=\"alert('ÇØ´ç »óÇ°¸®ºä°¡ ¾ø½À´Ï´Ù.');window.close();\"></body></html>";exit;
}
mysql_free_result($result);


$newdesign="";
$sql="SELECT filename,body FROM ".$designnewpageTables." WHERE type='reviewopen'";
$result=mysql_query($sql,get_db_conn());


if($row=mysql_fetch_object($result)) {
	$body=$row->body;
	$body=str_replace("[DIR]",$Dir,$body);
	$size=explode("",$row->filename);
	$xsize=(int)$size[0];
	$ysize=(int)$size[1];
	if($xsize==0) $xsize=450;
	if($ysize==0) $ysize=400;
	$body="<script>resizeTo('$xsize','$ysize');</script>\n".$body;
} else {
	if(file_exists($Dir.TempletDir."review/review_popup.php")) {
		$fp=fopen($Dir.TempletDir."review/review_popup.php","r");
		if($fp) {
			while (!feof($fp)) {$buffer.= fgets($fp, 1024);}
		}
		fclose($fp);
		$body=$buffer;
	}

	$xsize=450;
}

if($reviewimgwidth >0 && 450 < $reviewimgwidth) $reviewimgwidth = '450px';
else $reviewimgwidth = '100%';

mysql_free_result($result);

$close="javascript:window.close()";
$content = str_replace('__reviewImgWidth__',$reviewimgwidth,$content);

$pattern=array("(\[DIR\])","(\[DATE\])","(\[WRITER\])","(\[MARK\])","(\[CONTENT\])","(\[CLOSE\])");
$replace=array($Dir,$date,$writer,$mark,$content,$close);
$reviewbody=preg_replace($pattern,$replace,$body);
?>

<html>
<head>
<title>»ç¿ëÈÄ±â</title>
<meta http-equiv="CONTENT-TYPE" content="text/html;charset=EUC-KR">
<META http-equiv="X-UA-Compatible" content="IE=5" >
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<style>
td	{font-family:"±¼¸²,µ¸¿ò";color:#4B4B4B;font-size:12px;line-height:17px;}
BODY,DIV,form,TEXTAREA,center,option,pre,blockquote {font-family:Tahoma;color:000000;font-size:9pt;}

A:link    {color:#635C5A;text-decoration:none;}
A:visited {color:#545454;text-decoration:none;}
A:active  {color:#5A595A;text-decoration:none;}
A:hover  {color:#545454;text-decoration:underline;}
.input{font-size:12px;BORDER-RIGHT: #DCDCDC 1px solid; BORDER-TOP: #C7C1C1 1px solid; BORDER-LEFT: #C7C1C1 1px solid; BORDER-BOTTOM: #DCDCDC 1px solid; HEIGHT: 18px; BACKGROUND-COLOR: #ffffff;padding-top:2pt; padding-bottom:1pt; height:19px}
.select{color:#444444;font-size:12px;}
.textarea {border:solid 1;border-color:#e3e3e3;font-family:µ¸À½;font-size:9pt;color:333333;overflow:auto; background-color:transparent}
</style>
</head>

<body topmargin="0" leftmargin="0" rightmargin="0" marginheight="0" marginwidth="0">
<?=$reviewbody?>
</body>
</html>