<?php
header("Content-type:text/html; charset=KS_C_5601-1987");
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$method = $_POST["method"];
$sns_type = $_POST["sns_type"];
$sns_state = $_POST["sns_state"];
$comment = $_POST["comment"];
$pcode = $_POST["pcode"];
$c_seq = $_POST["c_seq"];
$etc = $_POST["etc"];

$board = $_POST["board"];
$bod_uid = $_POST["bod_uid"];

$arr_Snstype=array("f"=>"facebook","t"=>"twitter");
$return_data = null;
if($_ShopInfo->getMemid()){
	if($method == "snsLoginCheck"){

		$sql = "SELECT * FROM tblmembersnsinfo ";
		$sql.= "WHERE id='".$_ShopInfo->getMemid()."'";

		$result=mysql_query($sql,get_db_conn());
		while($row=mysql_fetch_object($result)) {
			$return_data[$arr_Snstype[$row->type]] = $row->state;
		}
		if(sizeof($return_data) >0){
			$return_data["result"] = "true";
		}else{
			$return_data["result"] = "nodata";
			$return_data["message"] = "nodata";
		}

	}else if($method == "snsChange"){

		$sql = "UPDATE tblmembersnsinfo SET ";
		$sql.= "state	= '".$sns_state."' ";
		$sql.= "WHERE id='".$_ShopInfo->getMemid()."' AND type ='".$sns_type."' ";

		$result=mysql_query($sql,get_db_conn());
		if($result) {
			$return_data["result"] = "true";
		}else{
			$return_data["result"] = "false";
		}
	}else if($method == "snsImage"){

		$sql = "SELECT profile_img FROM tblmembersnsinfo ";
		$sql.= "WHERE id='".$_ShopInfo->getMemid()."' AND state='Y' ORDER by regidate desc limit 1 ";//AND state='Y'
		$result=mysql_query($sql,get_db_conn());
		if($row=mysql_fetch_object($result)) {
			$return_data["result"] = "true";
			$return_data["sns_image"] = $row->profile_img;
		}else{
			$return_data["result"] = "nodata";
		}
	}else if($method == "regPcode"){

		$sql = "SELECT code FROM tblsnsproduct ";
		$sql.= "WHERE id='".$_ShopInfo->getMemid()."' AND pcode='".$pcode."' ";
		$result=mysql_query($sql,get_db_conn());
		if($row=mysql_fetch_object($result)) {
			$return_data["result"] = "true";
			$return_data["sns_url"] = "http://".$_ShopInfo->getShopurl()."?pk=".$row->code;
		}else{
			$cnt = 1;
			while($cnt > 0){
				$tmpid = rand(10000,999999);
				$sql = "SELECT count(1) cnt FROM tblsnsproduct WHERE code='".$tmpid."'";
				$result = mysql_query($sql,get_db_conn());
				if($row = mysql_fetch_object($result)) {
					$cnt = (int)$row->cnt;
				}
				mysql_free_result($result);
			}
			$sql = "INSERT tblsnsproduct SET ";
			$sql.= "code	= '".$tmpid."', ";
			$sql.= "id		= '".$_ShopInfo->getMemid()."', ";
			$sql.= "pcode	= '".$pcode."' ";
			$result=mysql_query($sql,get_db_conn());
			echo $sql;
			if($result) {
				$return_data["result"] = "true";
				$return_data["sns_url"] = "http://".$_ShopInfo->getShopurl()."?pk=".$tmpid;
			}
		}
	}else if($method == "regSns"){
		$sql = "SELECT seq FROM tblsnscomment ORDER BY seq desc";
		$result=mysql_query($sql,get_db_conn());
		if($row = mysql_fetch_object($result)) {
			$seq = (int)$row->seq;
		}
		$sql = "INSERT tblsnscomment SET ";
		$sql.= "seq	= '".($seq+1)."', ";
		$sql.= "id		= '".$_ShopInfo->getMemid()."', ";
		$sql.= "pcode	= '".$pcode."', ";
		$sql.= "comment	= '".iconv("UTF-8","EUC-KR", $comment)."', ";
		$sql.= "sns_type	= '".$sns_type."', ";
		$sql.= "regidate	= '".time()."' ";

		$result=mysql_query($sql,get_db_conn());
		if($result) {
			$return_data["result"] = "true";
			$return_data["snsType"] = $sns_type;
			$return_data["seq"] = $seq+1 ;
		}
	}else if($method == "regSns_0"){
		$arSnsTxt = array('t'=>"트위터",'f'=>"페이스북으");
		$sql = "SELECT seq FROM tblsnscomment ORDER BY seq desc";
		$result=mysql_query($sql,get_db_conn());
		if($row = mysql_fetch_object($result)) {
			$seq = (int)$row->seq;
		}
		$sql = "INSERT tblsnscomment SET ";
		$sql.= "seq	= '".($seq+1)."', ";
		$sql.= "id		= '".$_ShopInfo->getMemid()."', ";
		$sql.= "pcode	= '".$pcode."', ";
		$sql.= "comment	= '".$_ShopInfo->getMemid()."님이 ".$arSnsTxt[$sns_type]."로 상품을 추천하셨습니다.', ";
		$sql.= "sns_type	= '".$sns_type.",', ";
		$sql.= "regidate	= '".time()."' ";

		$result=mysql_query($sql,get_db_conn());
		if($result) {
			$return_data["result"] = "true";
		}
	}else if($method == "regSnsUrl"){
		$sql = "SELECT seq FROM tblsnscomment ORDER BY seq desc";
		$result=mysql_query($sql,get_db_conn());
		if($row = mysql_fetch_object($result)) {
			$seq = (int)$row->seq;
		}
		$sql = "INSERT tblsnscomment SET ";
		$sql.= "seq	= '".($seq+1)."', ";
		$sql.= "id		= '".$_ShopInfo->getMemid()."', ";
		$sql.= "pcode	= '".$pcode."', ";
		$sql.= "comment	= '".$_ShopInfo->getMemid()."님이 URL복사를 통해 상품을 추천하셨습니다.', ";
		$sql.= "sns_type	= '', ";
		$sql.= "regidate	= '".time()."' ";

		$result=mysql_query($sql,get_db_conn());
		if($result) {
			$return_data["result"] = "true";
		}
	}else if($method == "regGonggu"){
		/*
		$return_data["result"] = "true";
		$return_data["sns_url"] = "http://".$_ShopInfo->getShopurl()."?prdt=".$pcode;
		*/
		$sql = "SELECT code FROM tblsnsGonggu ";
		$sql.= "WHERE id='".$_ShopInfo->getMemid()."' AND pcode='".$pcode."' ";
		$result=mysql_query($sql,get_db_conn());
		if($row=mysql_fetch_object($result)) {
			$return_data["result"] = "true";
			$return_data["sns_url"] = "http://".$_ShopInfo->getShopurl()."?gong=".$row->code;
		}else{
			$cnt = 1;
			while($cnt > 0){
				$tmpid = rand(10000,999999);
				$sql = "SELECT count(1) cnt FROM tblsnsGonggu WHERE code='".$tmpid."'";
				$result = mysql_query($sql,get_db_conn());
				if($row = mysql_fetch_object($result)) {
					$cnt = (int)$row->cnt;
				}
				mysql_free_result($result);
			}
			$sql = "INSERT tblsnsGonggu SET ";
			$sql.= "code	= '".$tmpid."', ";
			$sql.= "id		= '".$_ShopInfo->getMemid()."', ";
			$sql.= "pcode	= '".$pcode."' ";
			$result=mysql_query($sql,get_db_conn());
			if($result) {
				$return_data["result"] = "true";
				$return_data["sns_url"] = "http://".$_ShopInfo->getShopurl()."?gong=".$tmpid;
			}
		}
	}else if($method == "regGongguUrl"){
		$sql = "SELECT seq FROM tblsnsGongguCmt ORDER BY seq desc";
		$result=mysql_query($sql,get_db_conn());
		if($row = mysql_fetch_object($result)) {
			$seq = (int)$row->seq +1;
		}
		mysql_free_result($result);
		$c_seq = $seq;

		$sql = "INSERT tblsnsGongguCmt SET ";
		$sql.= "seq	= '".$seq."', ";
		$sql.= "id		= '".$_ShopInfo->getMemid()."', ";
		$sql.= "c_seq	= '".$c_seq."', ";
		$sql.= "c_order	= '1', ";
		$sql.= "pcode	= '".$pcode."', ";
		$sql.= "comment	= '".$_ShopInfo->getMemid()."님이 URL복사를 통해 상품을 공동구매 신청하셨습니다.', ";
		$sql.= "sns_type= '".$sns_type."', ";
		$sql.= "count	= '1', ";
		$sql.= "etc		= '".$etc."', ";
		$sql.= "regidate	= '".time()."' ";
		$result=mysql_query($sql,get_db_conn());
		if($result) {
			$return_data["result"] = "true";
		}
	}else if($method == "regGongguCmt"){
		$sql = "SELECT seq FROM tblsnsGongguCmt ORDER BY seq desc";
		$result=mysql_query($sql,get_db_conn());
		if($row = mysql_fetch_object($result)) {
			$seq = (int)$row->seq +1;
		}
		mysql_free_result($result);
		$c_seq = $seq;

		$sql = "INSERT tblsnsGongguCmt SET ";
		$sql.= "seq	= '".$seq."', ";
		$sql.= "id		= '".$_ShopInfo->getMemid()."', ";
		$sql.= "c_seq	= '".$c_seq."', ";
		$sql.= "c_order	= '1', ";
		$sql.= "pcode	= '".$pcode."', ";
		$sql.= "comment	= '".iconv("UTF-8","EUC-KR", $comment)."', ";
		$sql.= "sns_type= '".$sns_type."', ";
		$sql.= "count	= '1', ";
		$sql.= "etc		= '".$etc."', ";
		$sql.= "regidate	= '".time()."' ";
		$result=mysql_query($sql,get_db_conn());

		if($result) {
			$return_data["result"] = "true";
		}
	}else if($method == "regGongguCmtsub"){
		$sql = "SELECT seq FROM tblsnsGongguCmt ORDER BY seq desc";
		$result=mysql_query($sql,get_db_conn());
		if($row = mysql_fetch_object($result)) {
			$seq = (int)$row->seq +1;
		}
		mysql_free_result($result);

		$sql = "INSERT tblsnsGongguCmt SET ";
		$sql.= "seq	= '".$seq."', ";
		$sql.= "id		= '".$_ShopInfo->getMemid()."', ";
		$sql.= "c_seq	= '".$c_seq."', ";
		$sql.= "c_order	= '2', ";
		$sql.= "pcode	= '".$pcode."', ";
		$sql.= "comment	= '".$comment."', ";
		$sql.= "sns_type= '".$sns_type."', ";
		$sql.= "count	= '1', ";
		$sql.= "etc		= '".$etc."', ";
		$sql.= "regidate	= '".time()."' ";
		$result=mysql_query($sql,get_db_conn());
		@mysql_query("UPDATE tblsnsGongguCmt SET count=count+1 WHERE seq ='$c_seq'",get_db_conn());

		if($result) {
			$return_data["result"] = "true";
		}
	}else if($method == "regGongguChk"){
		$sql = "SELECT count(1) cnt  FROM tblsnsGongguCmt WHERE c_seq ='".$c_seq."' AND pcode='".$pcode."' AND id='".$_ShopInfo->getMemid()."'";
		$result=mysql_query($sql,get_db_conn());
		if($row = mysql_fetch_object($result)) {
			$cnt = (int)$row->cnt;
		}
		mysql_free_result($result);
		if($cnt == 0) {
			$return_data["check"] = "ok";
		}else{
			$return_data["check"] = "duplicated";
		}
	}else if($method == "delGongguCmt"){
		$sql = "SELECT count, c_seq FROM tblsnsGongguCmt WHERE seq ='".$seq."' AND id='".$_ShopInfo->getMemid()."'";
		$result=mysql_query($sql,get_db_conn());
		if($row = mysql_fetch_object($result)) {
			$count = (int)$row->count;
			$c_seq = (int)$row->c_seq;
		}
		mysql_free_result($result);
		if($count == 1) {
			if(mysql_query("DELETE FROM tblsnsGongguCmt WHERE seq ='".$seq."' AND id='".$_ShopInfo->getMemid()."' ", get_db_conn())){
				if(@mysql_query("UPDATE tblsnsGongguCmt SET count=count-1 WHERE seq ='$c_seq'",get_db_conn()))
					$return_data["result"] = "true";
			}
		}else{
			$return_data["result"] = "false";
		}
	}else if($method == "encore"){
		$sql = "SELECT count(1) cnt FROM tblgongguencore WHERE productcode ='".$pcode."' AND id='".$_ShopInfo->getMemid()."'";
		$result=mysql_query($sql,get_db_conn());
		if($row = mysql_fetch_array($result)) {
			$cnt = (int)$row["cnt"];
		}
		mysql_free_result($result);
		if($cnt > 0) {
			$return_data["message"] = iconv("EUC-KR", "UTF-8", "이미 신청하셨습니다.");
		}else{
			$sql = "insert tblgongguencore SET productcode ='".$pcode."', id='".$_ShopInfo->getMemid()."', regidate=".time();
			if(mysql_query($sql,get_db_conn())){
				$return_data["message"] = iconv("EUC-KR", "UTF-8", "신청되었습니다.");
			}else{
				$return_data["message"] = "fail";
			}
		}
		$return_data["result"] = "true";
	}else if($method == "regBodLink"){

		$sql = "SELECT code FROM tblsnsboard ";
		$sql.= "WHERE board='".$board."' AND num='".$bod_uid."' ";
		$result=mysql_query($sql,get_db_conn());
		if($row=mysql_fetch_object($result)) {
			$return_data["result"] = "true";
			$return_data["sns_url"] = "http://".$_ShopInfo->getShopurl()."?bcmt=".$row->code;
		}else{
			$cnt = 1;
			while($cnt > 0){
				$tmpid = rand(10000,999999);
				$sql = "SELECT count(1) cnt FROM tblsnsboard WHERE code='".$tmpid."'";
				$result = mysql_query($sql,get_db_conn());
				if($row = mysql_fetch_object($result)) {
					$cnt = (int)$row->cnt;
				}
				mysql_free_result($result);
			}
			$sql = "INSERT tblsnsboard SET ";
			$sql.= "code	= '".$tmpid."', ";
			$sql.= "board	= '".$board."', ";
			$sql.= "num	= '".$bod_uid."' ";
			$result=mysql_query($sql,get_db_conn());
			if($result) {
				$return_data["result"] = "true";
				$return_data["sns_url"] = "http://".$_ShopInfo->getShopurl()."?bcmt=".$tmpid;
			}
		}
	}else if($method == "regBod"){
		$sql = "INSERT tblboardcomment SET ";
		$sql.= "board	= '".$board."', ";
		$sql.= "parent	= '".$bod_uid."', ";
		$sql.= "name	= '".$_ShopInfo->getMemname()."', ";
		$sql.= "passwd	= '".$_ShopInfo->getMemid()."', ";
		$sql.= "ip		= '".$_SERVER[REMOTE_ADDR]."', ";
		$sql.= "writetime	= '".time()."', ";
		$sql.= "comment	= '".iconv("UTF-8","EUC-KR", $comment)."', ";
		$sql.= "id		= '".$_ShopInfo->getMemid()."', ";
		$sql.= "sns_type= '".$sns_type."' ";
		$result=mysql_query($sql,get_db_conn());
		if($result) {
			$sql2 = "SELECT Max(num) num FROM tblboardcomment";
			$result2=mysql_query($sql2,get_db_conn());
			if($row = mysql_fetch_array($result2)) {
				$num = $row["num"];
			}
			mysql_free_result($result2);
			// 코멘트 갯수를 구해서 정리
			$total=mysql_fetch_array(mysql_query("SELECT COUNT(*) FROM tblboardcomment WHERE board='".$board."' AND parent='".$bod_uid."'",get_db_conn()));
			mysql_query("UPDATE tblboard SET total_comment='".$total[0]."' WHERE board='".$board."' AND num='".$bod_uid."'",get_db_conn());

			$return_data["result"] = "true";
			$return_data["snsType"] = $sns_type;
			$return_data["num"] = $num ;
		}
	}else{
		$return_data = array("result"=>"false","message"=>"잘못된 접근입니다.");
	}
}else{
	$return_data = array("result"=>"false","message"=>"Not Login");
}
echo json_encode($return_data);
?>
