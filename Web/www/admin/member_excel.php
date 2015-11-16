<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
INCLUDE ("access.php");

Header("Content-Type: application/octet-stream"); 
Header("Content-Disposition: attachment; filename=member_excel_".date("Ymd").".csv"); 
Header("Pragma: no-cache"); 
Header("Expires: 0");

unset($arrgroup);
$sql = "SELECT group_code, group_name FROM tblmembergroup ";
$result=mysql_query($sql,get_db_conn());
while($row=mysql_fetch_object($result)) {
	$arrgroup[$row->group_code]=$row->group_name;
}
mysql_free_result($result);

$joindate = $_shopdata->joindate;
$CurrentTime = time();
$period[0] = substr($joindate,0,4)."-".substr($joindate,4,2)."-".substr($joindate,6,2);
$period[1] = date("Y-m-d",$CurrentTime);
$period[2] = date("Y-m-d",$CurrentTime-(60*60*24*7));
$period[3] = date("Y-m",$CurrentTime)."-01";
$period[4] = date("Y",$CurrentTime)."-01-01";

$sort=(int)$_POST["sort"];
$scheck=(int)$_POST["scheck"];
$group_code=$_POST["group_code"];
$search_start=$_POST["search_start"];
$search_end=$_POST["search_end"];
$vperiod=(int)$_POST["vperiod"];
$search=$_POST["search"];
$search_start=$search_start?$search_start:$period[0];
$search_end=$search_end?$search_end:date("Y-m-d",$CurrentTime);

$ArrSort = array("date","name","id","age","reserve");
$ArrScheck = array("id","name","email","resno","home_tel","mobile","rec_id","group_code","logindate");

$date_start = str_replace("-","",$search_start)."000000";
$date_end = str_replace("-","",$search_end)."235959";
if ($scheck=="6") {			//추천인 검색
	$searchsql = "AND a.date >= '".$date_start."' AND a.date <= '".$date_end."' ";
	$searchsql.= "AND b.member_out = 'N' ";
	if($search) {
		$searchsql.= "AND a.rec_id LIKE '".$search."%' ";
	}
	$sql = "SELECT COUNT(a.rec_id) as rec_cnt, b.* FROM tblrecomendlist a, tblmember b ";
	$sql.= "WHERE a.rec_id=b.id ".$searchsql;
	$sql.= "GROUP BY a.rec_id ORDER BY rec_cnt DESC ";
} else if ($scheck=="7") {	//등급회원 검색
	$searchsql = "AND date >= '".$date_start."' AND date <= '".$date_end."' ";
	if ($group_code) {
		$searchsql.= "AND group_code = '".$group_code."' ";	//해당 등급회원
	} else {
		$searchsql.= "AND group_code != '' ";				//모든 등급회원
	}
	if ($search) {
		$searchsql.= "AND id LIKE '".$search."%' ";
	}
	$sql = "SELECT * FROM tblmember WHERE 1=1 ";
	$sql.= $searchsql." ";
} else if ($scheck=="8") {	//오늘 로그인 회원 검색
	if ($search) {
		$searchsql = "AND id LIKE '".$search."%' ";
	}
	$sql = "SELECT * FROM tblmember WHERE logindate >= '".date("Ymd")."000000' ".$searchsql." ";
} else {
	$searchsql = "AND date >= '".$date_start."' AND date <= '".$date_end."' ";
	if ($search) {
		$searchsql.= "AND ".$ArrScheck[$scheck]." LIKE '".$search."%' ";
	}
	$sql = "SELECT * FROM tblmember WHERE 1=1 ".$searchsql." ";
}
if ($scheck!="6") {
	switch ($sort) {
		case "0":	//등록일
			$sql.= "ORDER BY date DESC ";
			break;
		case "1":	//회원명
			$sql.= "ORDER BY name ASC ";
			break;
		case "2":	//아이디
			$sql.= "ORDER BY id ASC ";
			break;
		case "3":	//나이순
			$sql.= "ORDER BY resno ASC ";
			break;
		case "4":	//적립금
			$sql.= "ORDER BY reserve DESC ";
			break;
		default :	//등록일
			$sql.= "ORDER BY date DESC ";
			break;
	}
}
#echo $sql; exit;
$result=mysql_query($sql,get_db_conn());

echo "가입일,아이디,비밀번호,이름,회원그룹,주민번호,이메일,휴대폰,이메일수신,SMS수신,집전화,집우편번호,집주소(읍/면/동 이상),집주소(번지 미만),회사전화,회사우편번호,회사주소(읍/면/동 이상),회사주소(번지 미만),적립금,추천인ID\n";
while($row=mysql_fetch_object($result)) {
	$reg_date=substr($row->date,0,4)."/".substr($row->date,4,2)."/".substr($row->date,6,2);
	$row->id=ereg_replace(",","",$row->id);
	$row->name=ereg_replace(",","",$row->name);
	$row->email=ereg_replace(",","",$row->email);
	$row->home_tel=ereg_replace(",","",$row->home_tel);
	$row->office_tel=ereg_replace(",","",$row->office_tel);
	$row->mobile=ereg_replace(",","",$row->mobile);
	$row->home_addr=ereg_replace(","," ",$row->home_addr);
	$row->office_addr=ereg_replace(","," ",$row->office_addr);
	$row->rec_id=ereg_replace(",","",$row->rec_id);
	$row->etcdata=ereg_replace(","," ",$row->etcdata);

	$home_addr=explode("=",$row->home_addr);
	$office_addr=explode("=",$row->office_addr);

	echo "$reg_date,";
	echo "=\"$row->id\",";
	echo "$row->passwd,";
	echo "$row->name,";
	echo $arrgroup[$row->group_code].",";
	echo substr($row->resno,0,6)."-".substr($row->resno,6,1)."[".md5(substr($row->resno,7,6))."],";
	echo "$row->email,";
	echo "=\"$row->mobile\",";
	if($row->news_yn=="Y") echo "Y,Y,";
	else if($row->news_yn=="N") echo "N,N,";
	else if($row->news_yn=="M") echo "Y,N,";
	else if($row->news_yn=="S") echo "N,Y,";
	else ",,";
	echo "=\"$row->home_tel\",";
	if(strlen($row->home_post)>0) echo substr($row->home_post,0,3)."-".substr($row->home_post,3,3).",";
	else echo ",";
	echo $home_addr[0].",";
	echo $home_addr[1].",";
	echo "=\"$row->office_tel\",";
	if(strlen($row->office_post)>0)	echo substr($row->office_post,0,3)."-".substr($row->office_post,3,3).",";
	else echo ",";
	echo $office_addr[0].",";
	echo $office_addr[1].",";
	echo "$row->reserve,";
	echo "$row->rec_id";
	echo "\n";
}
mysql_free_result($result);
?>