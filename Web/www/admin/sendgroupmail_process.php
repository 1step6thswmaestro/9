<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

if(strlen($_ShopInfo->getId())==0){
	echo "<script>alert('정상적인 경로로 접근하시기 바랍니다.');</script>";
	exit;
}

set_time_limit(7200);

$date=$_POST["date"];

$mailfilepath=$Dir.DataDir."groupmail/";

$sql = "SELECT * FROM tblgroupmail WHERE issend='N' AND procok='N' ";
if(strlen($date)>0) $sql.= "AND date='".$date."' ";
$result=mysql_query($sql,get_db_conn());
while($row=mysql_fetch_object($result)) {
	mysql_query("UPDATE tblgroupmail SET procok='Y' WHERE date='".$row->date."'",get_db_conn());
	$data[]=$row;
}
mysql_free_result($result);

for($i=0;$i<count($data);$i++) {
	if(strlen($data[$i]->filename)>0 && strlen($data[$i]->body)>0) {
		$fp = fopen($mailfilepath.$data[$i]->filename, "r");
		$tolist = fread($fp,filesize($mailfilepath.$data[$i]->filename));
		fclose($fp);

		$shopname=$data[$i]->shopname;
		$shopname="Return-Path: ".$data[$i]->fromemail."\r\n".stripslashes("From: ".$shopname."<".$data[$i]->fromemail.">")."\r\n";
		$shopname=$shopname."X-Mailer: SendMail\r\n";
	    
		if($data[$i]->html=="Y") $content_type="text/html";
		else $content_type="text/plain";

		$count=0;
		$body=stripslashes($data[$i]->body);

		$tok=strtok($tolist,"\n");

		mail($data[$i]->fromemail,"단체메일 발송이 완료되었습니다.",$body, "Content-Type: ".$content_type."; charset=euc-kr\r\n".$shopname."\r\n");
		while($tok) {
			$toarray=explode(",",$tok);
			$to=str_replace("<?","",$toarray[0]);
			$date=$toarray[2];
			$date=substr($date,0,4)."년".substr($date,4,2)."월".substr($date,6,2)."일 (".substr($date,8,2).":".substr($date,10,2).")";
			$id=str_replace("?>","",$toarray[3]);

			$subject=$data[$i]->subject;
			$pattern=array("(\[NAME\])");
			$replace=array($toarray[1]);
			$subject=preg_replace($pattern,$replace,$subject);

			$body=$data[$i]->body;
			$pattern=array("(\[NAME\])","(\[DATE\])","(\[NOMAIL\])");
			$replace=array($toarray[1],$date,FrontDir."mypage_usermodify.php");
			$body=preg_replace($pattern,$replace,$body);

			mail($to,$subject,$body,"Content-Type: ".$content_type."; charset=euc-kr\r\n".$shopname."\r\n");
			$tok=strtok("\n");
			$count++;
		}
		$curdate=date("YmdHis");
		$sql ="UPDATE tblgroupmail SET issend='Y', okcnt='".$count."', enddate='".$curdate."' ";
		$sql.="WHERE date='".$data[$i]->date."' ";
		mysql_query($sql,get_db_conn());
		unlink($mailfilepath.$data[$i]->filename);
	}
	sleep(15);
}

?>