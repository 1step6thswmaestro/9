<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
INCLUDE ("access.php");

####################### 페이지 접근권한 check ###############
//$PageCode = "st-1";
//$MenuCode = "counter";
//if (!$_usersession->isAllowedTask($PageCode)) {
//	INCLUDE ("AccessDeny.inc.php");
//	exit;
//}
#########################################################

$isupdate=0;
$date=date("Ym",mktime(0,0,0,date("m")-1,1,date("Y")));

$sql = "SELECT date FROM tblcounterupdate ";
$result=mysql_query($sql,get_db_conn());
if($row=mysql_fetch_object($result)) {
	if($row->date<$date) $isupdate=1;
} else {
	$isupdate=2;
}
mysql_free_result($result);

if($isupdate>0) {
	unset($qry);
	$sql = "SELECT MID(date,7,2) as day, SUM(cnt) as cnt, SUM(pagecnt) as pagecnt FROM tblcounter ";
	$sql.= "WHERE date LIKE '".$date."%' GROUP BY day ";
	$result2=mysql_query($sql,get_db_conn());
	$i=0;
	while($data=mysql_fetch_object($result2)) {
		if($i==0) $qry = "INSERT INTO tblcountermonth VALUES ";
		$qry.= "('".$date.$data->day."','".$data->cnt."','".$data->pagecnt."'),";
		$i++;
	}
	mysql_free_result($result2);
	if(strlen($qry)>0) {
		$qry=substr($qry,0,-1);
		mysql_query($qry,get_db_conn());
	}

	unset($qry);
	$sql ="SELECT SUM(cnt) as cnt, productcode FROM tblcounterproduct ";
	$sql.="WHERE date LIKE '".$date."%' GROUP BY productcode ";
	$result2=mysql_query($sql,get_db_conn());
	$i=0;
	while($data=mysql_fetch_object($result2)) {
		if($i==0) $qry = "INSERT INTO tblcounterproductmonth VALUES ";
		$qry.= "('".$date."','".$data->productcode."','".$data->cnt."'),";
		$i++;
	}
	mysql_free_result($result2);
	if(strlen($qry)>0) {
		$qry=substr($qry,0,-1);
		mysql_query($qry,get_db_conn());
	}

	unset($qry);
	$sql ="SELECT SUM(cnt) as cnt,code FROM tblcountercode WHERE date LIKE '".$date."%' GROUP BY code ";
	$result2=mysql_query($sql,get_db_conn());
	$i=0;
	while($data=mysql_fetch_object($result2)) {
		if($i==0) $qry = "INSERT INTO tblcountercodemonth VALUES ";
		$qry.= "('".$date."','".$data->code."','".$data->cnt."'),";
		$i++;
	}
	mysql_free_result($result2);
	if(strlen($qry)>0) {
		$qry=substr($qry,0,-1);
		mysql_query($qry,get_db_conn());
	}

	unset($qry);
	$sql ="SELECT SUM(cnt) as cnt,search FROM tblcounterkeyword ";
	$sql.="WHERE date LIKE '".$date."%' GROUP BY search ";
	$result2=mysql_query($sql,get_db_conn());
	$i=0;
	while($data=mysql_fetch_object($result2)) {
		if($i==0) $qry = "INSERT INTO tblcounterkeywordmonth VALUES ";
		$qry.= "('".$date."','".$data->search."','".$data->cnt."'),";
		$i++;
	}
	mysql_free_result($result2);
	if(strlen($qry)>0) {
		$qry=substr($qry,0,-1);
		mysql_query($qry,get_db_conn());
	}

	unset($qry);
	$sql ="SELECT SUM(cnt) as cnt,domain FROM tblcounterdomain ";
	$sql.="WHERE date LIKE '".$date."%' GROUP BY domain ";
	$result2=mysql_query($sql,get_db_conn());
	$i=0;
	while($data=mysql_fetch_object($result2)) {
		if($i==0) $qry = "INSERT INTO tblcounterdomainmonth VALUES ";
		$qry.= "('".$date."','".$data->domain."','".$data->cnt."'),";
		$i++;
	}
	mysql_free_result($result2);
	if(strlen($qry)>0) {
		$qry=substr($qry,0,-1);
		mysql_query($qry,get_db_conn());
	}

	unset($qry);
	$sql = "SELECT MID(date,7,2) as day, SUM(cnt) as cnt FROM tblcounterorder ";
	$sql.= "WHERE date LIKE '".$date."%' GROUP BY day ";
	$result2=mysql_query($sql,get_db_conn());
	$i=0;
	while($data=mysql_fetch_object($result2)) {
		if($i==0) $qry = "INSERT INTO tblcounterordermonth VALUES ";
		$qry.= "('".$date.$data->day."','".$data->cnt."'),";
		$i++;
	}
	mysql_free_result($result2);
	if(strlen($qry)>0) {
		$qry=substr($qry,0,-1);
		mysql_query($qry,get_db_conn());
	}

	unset($qry);
	$sql ="SELECT SUM(cnt) as cnt,page FROM tblcounterpageview ";
	$sql.="WHERE date LIKE '".$date."%' GROUP BY page ";
	$result2=mysql_query($sql,get_db_conn());
	$i=0;
	while($data=mysql_fetch_object($result2)) {
		if($i==0) $qry = "INSERT INTO tblcounterpageviewmonth VALUES ";
		$qry.= "('".$date."','".$data->page."','".$data->cnt."'),";
		$i++;
	}
	mysql_free_result($result2);
	if(strlen($qry)>0) {
		$qry=substr($qry,0,-1);
		mysql_query($qry,get_db_conn());
	}

	unset($qry);
	$sql ="SELECT SUM(cnt) as cnt,domain FROM tblcountersearchengine ";
	$sql.="WHERE date LIKE '".$date."%' GROUP BY domain ";
	$result2=mysql_query($sql,get_db_conn());
	$i=0;
	while($data=mysql_fetch_object($result2)) {
		if($i==0) $qry = "INSERT INTO tblcountersearchenginemonth VALUES ";
		$qry.= "('".$date."','".$data->domain."','".$data->cnt."'),";
		$i++;
	}
	mysql_free_result($result2);
	if(strlen($qry)>0) {
		$qry=substr($qry,0,-1);
		mysql_query($qry,get_db_conn());
	}


	if($isupdate==1) {
		$sql = "UPDATE tblcounterupdate SET date='".$date."' ";
		mysql_query($sql,get_db_conn());
	} else if($isupdate==2) {
		$sql = "INSERT INTO tblcounterupdate VALUES ('".$date."') ";
		mysql_query($sql,get_db_conn());
	}
}
?>