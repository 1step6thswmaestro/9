<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");



	$sql = "SELECT * FROM ".$designnewpageTables." WHERE type='joinmail' ";
	$result=mysql_query($sql,get_db_conn());
	$row=mysql_fetch_object($result);
		$curdate = date("Y³â m¿ù dÀÏ");




		$pattern = array (
			"(\[AUTHCODE\])",
			"(\[BANKDATE\])",
			"(\[CONTENT\])",
			"(\[CURDATE\])",
			"(\[DATE\])",
			"(\[GONGGULINK\])",
			"(\[ID\])",
			"(\[MAILDATA\])",
			"(\[MESSAGE\])",
			"(\[MSG\])",
			"(\[NAME\])",
			"(\[OKDATE\])",
			"(\[OKDATE\])",
			"(\[ORDERCODE\])",
			"(\[ORDERDATE\])",
			"(\[PASSWORD\])",
			"(\[PESTER_CONTENT\])",
			"(\[PESTER_URL\])",
			"(\[PRICE\])",
			"(\[RECEIVE_MAIL\])",
			"(\[REQUSETDATE\])",
			"(\[RE_NAME\])",
			"(\[SHOP\])",
			"(\[TONAME\])",
			"(\[URL\])",
			"(\[URL_ID\])",
			"(\[URL_LINK\])"
		);

		$replace = array (
			$authcode,$bankdate,$content,$curDate,
		);



		$replace = array ($shopname,$name."[".$id."]",$url_link,$message,$shopurl,$email,$curDate);
		$replace = array ($shopname,$sendmsg,$shopurl,$curdate,$strBestprdt);
		$replace = array ($shopname,$name,$sendmsg,$shopurl,$curdate,$requsetdate,$gonggulink);
		$replace = array ($shopname,$send_name,$re_name,$message,$pester_url,$shopurl,$pester_content,$curdate);
		$replace = array ($shopname,$send_name,$re_name,$message,$ordercode,$shopurl,$curdate);
		$replace = array ($shopname,$send_name,$re_name);
		$replace = array ($shopname,$name."[".$id."]",$url_id,$message,$shopurl,$curdate);
		$replace = array ($shopname,$name,$url_id);
		$replace = array ($shopname,$shopurl,$okdate,$id,$msg,$name,$toname,number_format($price),$curdate);
		$replace = array ($shopname,$shopurl,$orderdate,$curdate);
		$replace = array ($shopname,$shopurl,$okdate,$id,$name,$curdate);
		$replace = array ($shopname,$shopurl,$bankdate,$orderdate,$curdate);
		$replace = array ($shopname,$name,$id,$passwd,$shopurl,$curdate);
		$replace = array ($shopname,$_ord->sender_name,$curdate,$maildata[0],$thankmsg,$shopurl,$c_ordercode);
		$replace = array ($shopname,$_ord->sender_name,$curdate);
		$replace = array ($shopname,$name,$join_msg,$shopurl,$curdate);


		$subject = preg_replace($pattern,$replace,$row->subject);
		$body	 = $row->body;
?>