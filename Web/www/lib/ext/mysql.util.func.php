<?
include_once dirname(__FILE__).'/func.php';
function fetchResult($sql,$row=0,$cul=0){
	$return = false;
	if(false !== $result = mysql_query($sql,get_db_conn())){
		if(mysql_num_rows($result) > 0) $return = mysql_result($result,$row,$cul);
	}
	return $return;
}


function fetchAssoc($sql){
	$return = false;
	if(false !== $result = mysql_query($sql,get_db_conn())){
		$return = array();
		if(mysql_num_rows($result) > 0){
			while($row = mysql_fetch_assoc($result)) array_push($return,$row);
		}
	}
	return $return;
}

?>