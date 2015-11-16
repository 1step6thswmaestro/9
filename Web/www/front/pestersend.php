<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$sender_name=ereg_replace(" ","",$_POST["sender_name"]);
$sender_email=ereg_replace("'","",$_POST["sender_email"]);
$sender_tel=ereg_replace("'","",$_POST["sender_tel"]);
$receiver_name=ereg_replace(" ","",$_POST["receiver_name"]);
$receiver_tel1=ereg_replace("'","",$_POST["receiver_tel1"]);
$receiver_tel2=ereg_replace("'","",$_POST["receiver_tel2"]);
$receiver_post=$_POST["rpost1"]."-".$_POST["rpost2"];
//$receiver_addr=ereg_replace("'","",$_POST["receiver_addr"])."=".ereg_replace("'","",$_POST["raddr1"])." ".ereg_replace("'","",$_POST["raddr2"]);
$receiver_addr=ereg_replace("'","",$_POST["receiver_addr"])."=".ereg_replace("'","",$_POST["raddr1"])."=".ereg_replace("'","",$_POST["raddr2"]);
$order_prmsg=ereg_replace("'","",$_POST["order_prmsg"]);
$pester_smstxt=ereg_replace("'","",$_POST["pester_smstxt"]);
$pester_emailtxt=ereg_replace("'","",$_POST["pester_emailtxt"]);

$cnt = 1;
while($cnt > 0){
	$tmpcode = rand(10000,99999);
	$sql = "SELECT count(1) cnt FROM tblpesterinfo WHERE code='".$tmpcode."'";
	$result = mysql_query($sql,get_db_conn());
	if($row = mysql_fetch_object($result)) {
		$cnt = (int)$row->cnt;
	}
	mysql_free_result($result);
}
if($_ShopInfo->getTempkey()){
	$sql = "INSERT tblpesterinfo SET ";
	$sql.= "code	= '".$tmpcode."', ";
	$sql.= "tempkey	= '".$_ShopInfo->getTempkey()."', ";
	$sql.= "id		= '".$_ShopInfo->getMemid()."', ";
	$sql.= "sender_name		= '".$sender_name."', ";
	$sql.= "sender_email	= '".$sender_email."', ";
	$sql.= "sender_tel		= '".$sender_tel."', ";
	$sql.= "receiver_name	= '".$receiver_name."', ";
	$sql.= "receiver_tel1	= '".$receiver_tel1."', ";
	$sql.= "receiver_tel2	= '".$receiver_tel2."', ";
	$sql.= "receiver_post	= '".$receiver_post."', ";
	$sql.= "receiver_addr	= '".$receiver_addr."', ";
	$sql.= "order_prmsg		= '".$order_prmsg."', ";
	$sql.= "pester_name		= '".$pester_name."', ";
	$sql.= "pester_email	= '".$pester_email."', ";
	$sql.= "pester_tel		= '".$pester_tel."', ";
	$sql.= "pester_smstxt	= '".$pester_smstxt."', ";
	$sql.= "pester_emailtxt	= '".$pester_emailtxt."', ";
	$sql.= "regdate	= '".time()."', ";
	$sql.= "state	= '0' ";
	$insert=mysql_query($sql,get_db_conn());
	if (mysql_errno()==0) {

		mysql_query("INSERT INTO tblbasket_pester_save SELECT * FROM tblbasket_pester WHERE tempkey='".$_ShopInfo->getTempkey()."'",get_db_conn());
		if(!mysql_errno()) mysql_query("DELETE FROM tblbasket_pester WHERE tempkey='".$_ShopInfo->getTempkey()."'",get_db_conn());
		$_ShopInfo->setTempkey($_data->ETCTYPE["BASKETTIME"], true);
		$sql="SELECT * FROM tblsmsinfo WHERE mem_pester ='Y' ";
		$result=mysql_query($sql,get_db_conn());
		if($rowsms=mysql_fetch_object($result)) {
			$sms_id=$rowsms->id;
			$sms_authkey=$rowsms->authkey;
			$msg_mem_pester=$rowsms->msg_mem_pester;

			if(strlen($msg_mem_pester)==0) $msg_mem_pester="[NAME]님의 조르기: [URL] (상세내용 메일확인)";
			$msg_mem_pester = "[NAME]님의 조르기: [URL] (상세내용 메일확인)\n";
			$patten=array("(\[NAME\])","(\[URL\])");
			$replace=array($sender_name,"http://".$_ShopInfo->getShopurl()."?pstr=".$tmpcode);

			$msg_pester=preg_replace($patten,$replace,$msg_mem_pester);
			$msg_pester=addslashes($msg_pester.$pester_smstxt);

			$date=0;
			$etcmsg="조르기메세지(회원)";
			if($rowsms->use_mms=='Y') $use_mms = 'Y';
			else $use_mms = '';
			$temp=SendSMS2($sms_id, $sms_authkey, $pester_tel, "", $sender_tel, $date, $msg_pester, $etcmsg, $use_mms);
		}
		mysql_free_result($result);
		//조르기 메일전송
		if(strlen($pester_email)>0 && strlen($pester_emailtxt)>0){
			SendPesterMail($_data->shopname, $_data->shopurl, $_data->design_mail, $pester_emailtxt, $sender_email, $sender_name, $pester_email, $pester_name, $tmpcode);
		}
		echo "<script>alert('조르기 상품이 전송되었습니다.');</script>";

	}
}
echo "<html><head><title></title></head><body onload=\"location.replace('/')\"></body></html>";exit;
?>
