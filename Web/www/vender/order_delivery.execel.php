<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/venderlib.php");
INCLUDE ("access.php");

$ordercodes=substr($_POST["ordercodes"],0,-1);
$paystate=$_POST["paystate"];
$deli_gbn=$_POST["deli_gbn"];
$s_check=$_POST["s_check"];
$search=$_POST["search"];

$CurrentTime = time();

$search_start=$_POST["search_start"];
$search_end=$_POST["search_end"];
$search_s=$search_start?str_replace("-","",$search_start."000000"):str_replace("-","",$period[0]."000000");
$search_e=$search_end?str_replace("-","",$search_end."235959"):date("Ymd",$CurrentTime)."235959";

$tempstart = explode("-",$search_start);
$tempend = explode("-",$search_end);
$termday = (mktime(0,0,0,$tempend[1],$tempend[2],$tempend[0])-mktime(0,0,0,$tempstart[1],$tempstart[2],$tempstart[0]))/86400;
if ($termday>31) {
	echo "<script>alert('주문서 EXCEL 다운로드 기간은 1달을 초과할 수 없습니다.');</script>";
	exit;
}

Header("Content-Type: application/octet-stream"); 
Header("Content-Disposition: attachment; filename=order_excel_".date("Ymd",$CurrentTime).".csv"); 
Header("Pragma: no-cache"); 
Header("Expires: 0"); 

$excelnum="0,1,2";

$excelval=array(
	array("주문번호"									,&$ordercode),				#0		-
	array("주문자"									,&$sender_name),		#1		-
	array("송장번호"				,'')
);

$arr_excel = explode(",",$excelnum);
$cnt = count($arr_excel);

if(strlen($ordercodes)>0) $ordercodes="'".str_replace(",","','",$ordercodes)."'";

$sql = "SELECT * FROM tblorderoption ";
if(strlen($ordercodes)>0) 
	$sql.= "WHERE ordercode IN (".$ordercodes.") ";
else
	$sql.= "WHERE ordercode >= '".$search_s."' AND ordercode <= '".$search_e."' ";
$result = mysql_query($sql,get_db_conn());
while($row = mysql_fetch_object($result)) {
	$optionkey=$row->ordercode.$row->productcode.$row->opt_idx;
	$addoption[$optionkey]=$row->opt_name;
}
mysql_free_result($result);

$sql = "SELECT a.ordercode,a.id,a.paymethod, ";
$sql.= "a.pay_data,a.bank_date,a.pay_flag,a.pay_admin_proc, ";
$sql.= "a.sender_name,a.sender_email,a.sender_tel,a.receiver_name,a.receiver_tel1,a.receiver_tel2, ";
$sql.= "a.receiver_addr,a.order_msg,a.del_gbn, b.deli_gbn as prdt_deli_gbn,b.deli_date as prdt_deli_date, ";
$sql.= "b.deli_num,b.productcode,b.productname,b.opt1_name,b.opt2_name, ";
$sql.= "b.addcode,b.quantity,b.price,b.reserve,b.date,b.order_prmsg ";
$sql.= "FROM tblorderinfo a, tblorderproduct b ";
$sql.= "WHERE a.ordercode=b.ordercode ";

if(strlen($ordercodes)>0)
	$sql.= "AND a.ordercode IN (".$ordercodes.") ";
else
	$sql.= "AND a.ordercode >= '".$search_s."' AND a.ordercode <= '".$search_e."' ";

$sql.= "AND b.vender='".$_VenderInfo->getVidx()."' ";
$sql.= "AND NOT (b.productcode LIKE 'COU%' OR b.productcode LIKE '999999%') ";
if (strlen($deli_gbn)>0) $sql.= "AND b.deli_gbn = '".$deli_gbn."' ";

if($paystate=="Y") {		//입금
	$sql.= "AND ((MID(a.paymethod,1,1) IN ('B','V','O','Q') AND LENGTH(a.bank_date)=14) OR (MID(a.paymethod,1,1) IN ('C','P','M') AND a.pay_admin_proc!='C' AND a.pay_flag='0000')) ";
} else if($paystate=="B") {	//미입금
	$sql.= "AND ((MID(a.paymethod,1,1) IN ('B','V','O','Q') AND (a.bank_date IS NULL OR a.bank_date='')) OR (MID(a.paymethod,1,1) IN ('C','P','M') AND a.pay_flag!='0000' AND a.pay_admin_proc='C')) ";
} else if($paystate=="C") {	//환불
	$sql.= "AND ((MID(a.paymethod,1,1) IN ('B','V','O','Q') AND LENGTH(a.bank_date)=9) OR (MID(a.paymethod,1,1) IN ('C','P','M') AND a.pay_flag='0000' AND a.pay_admin_proc='C')) ";
}
if(strlen($search)>0) {
	if($s_check=="cd") $qry.= "AND a.ordercode='".$search."' ";
	else if($s_check=="pn") $qry.= "AND b.productname LIKE '".$search."%' ";
	else if($s_check=="mn") $qry.= "AND a.sender_name='".$search."' ";
	else if($s_check=="mi") $qry.= "AND a.id='".$search."' ";
	else if($s_check=="cn") $qry.= "AND a.id='".$search."X' ";
}
$sql.= "ORDER BY a.ordercode DESC ";
$result = mysql_query($sql,get_db_conn());

for($i=0;$i<$cnt;$i++) {
	if($i!=0) echo ",";
	echo $excelval[$arr_excel[$i]][0];
}
echo "\n";

$pattern = array("(\r\n)","(\")","(,)","(;)");
$replacement = array(" ","",".","");

$temp = "";
while ($row=mysql_fetch_object($result)) {
	if(strlen($temp)!=0) {
		for($i=0;$i<$cnt;$i++) {
			if($i!=0) echo ",";
			echo '"' . doubleQuote($excelval[$arr_excel[$i]][1]) . '"';
		}
		echo "\n";
	}
	$ordercode=$row->ordercode;
	$temp=$row->ordercode;
	$date = substr($row->ordercode, 0, 12);
	$date = substr($date,0,4)."/".substr($date,4,2)."/".substr($date,6,2);   //날짜 형식 수정  
	$orderdate = str_replace("/","-",$date)." (".substr($row->ordercode,8,2).":".substr($row->ordercode,10,2).":".substr($row->ordercode,12,2).")";
	$sender_name=$row->sender_name;
	$pay_data=$row->pay_data;
	$sender_email=$row->sender_email;

	if(substr($row->ordercode,20)=="X") {	//비회원
		$idnum = substr($row->id,1,6);
	} else {	//회원
		$idnum = $row->id;
	}

	if(preg_match("/^(B){1}/", $row->paymethod)) {	//무통장
		if (strlen($row->bank_date)==9 && substr($row->bank_date,8,1)=="X") $pay="환불";
		else if (strlen($row->bank_date)>0) $pay="입금완료";
		else $pay="미입금";
	} else if(preg_match("/^(V){1}/", $row->paymethod)) {	//계좌이체
		if (strcmp($row->pay_flag,"0000")!=0) $pay="결제실패";
		else if ($row->pay_flag=="0000" && $row->pay_admin_proc=="C") $pay="환불";
		else if ($row->pay_flag=="0000") $pay="결제완료";
	} else if(preg_match("/^(M){1}/", $row->paymethod)) {	//핸드폰
		if (strcmp($row->pay_flag,"0000")!=0) $pay="결제실패";
		else if ($row->pay_flag=="0000" && $row->pay_admin_proc=="C") $pay="취소완료";
		else if ($row->pay_flag=="0000") $pay="결제완료";
	} else if(preg_match("/^(O|Q){1}/", $row->paymethod)) {	//가상계좌
		if (strcmp($row->pay_flag,"0000")!=0) $pay="주문실패";
		else if ($row->pay_flag=="0000" && $row->pay_admin_proc=="C") $pay="환불";
		else if ($row->pay_flag=="0000" && strlen($row->bank_date)==0) $pay="미입금";
		else if ($row->pay_flag=="0000" && strlen($row->bank_date)>0) $pay="입금완료";
	} else {
		if (strcmp($row->pay_flag,"0000")!=0) $pay="카드실패";
		else if ($row->pay_flag=="0000" && $row->pay_admin_proc=="N") $pay="카드승인";
		else if ($row->pay_flag=="0000" && $row->pay_admin_proc=="Y") $pay="결제완료";
		else if ($row->pay_flag=="0000" && $row->pay_admin_proc=="C") $pay="취소완료";
	}

	switch($row->prdt_deli_gbn) {
		case 'S': $prdt_deli_gbn="발송준비";  break;
		case 'X': $prdt_deli_gbn="배송요청";  break;
		case 'Y': $prdt_deli_gbn="배송";  break;
		case 'D': $prdt_deli_gbn="취소요청";  break;
		case 'N': $prdt_deli_gbn="미처리";  break;
		case 'E': $prdt_deli_gbn="환불대기";  break;
		case 'C': $prdt_deli_gbn="주문취소";  break;
		case 'R': $prdt_deli_gbn="반송";  break;
		case 'H': $prdt_deli_gbn="배송(정산보류)";  break;
	}
	$sender_telnum=check_num($row->sender_tel);
	$receiver_tel1num=check_num($row->receiver_tel1);
	$receiver_tel2num=check_num($row->receiver_tel2);
	$receiver_tel = $receiver_tel1num." ".$receiver_tel2num;
	$sender_tel = replace_tel($sender_telnum);
	$receiver_tel1 = replace_tel($receiver_tel1num);
	$receiver_tel2 = replace_tel($receiver_tel2num);
	$sender_telnum="=\"".$sender_telnum."\""; 
	$receiver_tel1num="=\"".$receiver_tel1num."\""; 
	$receiver_tel2num="=\"".$receiver_tel2num."\""; 
	$receiver_name=$row->receiver_name;
	$bank_date="=\"".($row->paymethod=="B"?$row->bank_date:substr($row->ordercode,0,14))."\"";
	$row->receiver_addr=str_replace("\r\n","",$row->receiver_addr);
	$row->receiver_addr=str_replace("\n","",$row->receiver_addr);
	$receiver_addr=explode("주소 : ",$row->receiver_addr);
	$post1 = substr($receiver_addr[0],11,3).substr($receiver_addr[0],15,3);
	$post2 = substr($receiver_addr[0],11,7);
	$addr = $receiver_addr[1]; 
	$mess=explode("[MEMO]",$row->order_msg);
	$message=preg_replace($pattern,$replacement,strip_tags($mess[0]));
	$messnotag=preg_replace($pattern,$replacement,$mess[0]);
	$adminmemo=preg_replace($pattern,$replacement,$mess[1]);
	$usermemo=preg_replace($pattern,$replacement,$mess[2]);
	$quantity=$row->quantity;
	$product1=str_replace(",","",$row->productname);
	$product=strip_tags($product1);
	$productcode="=\"".$row->productcode."\"";
	$price=$row->price;
	$reserve=$row->reserve;
	$option=$option2=$addcode="";
	if (strlen($row->addcode)>0) $addcode=$row->addcode;
	if (strlen($row->opt1_name)>0) {
		if(substr($row->opt1_name,0,5)=="[OPTG") {
			$key=$row->ordercode.$row->productcode.$row->opt1_name;
			$option.=$addoption[$key];
		} else {
			$option.=(strlen($option)==0?"":"-").$row->opt1_name;
		}
	}
	if (strlen($row->opt2_name)>0) $option.=(strlen($option)==0?"":"-").$row->opt2_name;
	$option2=$addcode.$option;
	$productname=$product."-".$quantity.(strlen($option2)==0?"":"-".$option2);
	$productname2=$productname;
	$deli_num=$row->deli_num;
	$prdt_message=preg_replace($pattern,$replacement,strip_tags($row->order_prmsg));
	$prdt_deli_date="=\"".$row->prdt_deli_date."\"";
}
mysql_free_result($result);

for($i=0;$i<$cnt;$i++){
	if($i!=0) echo ",";
	echo '"' . doubleQuote($excelval[$arr_excel[$i]][1]) . '"';
}
echo "\n";

function &doubleQuote($str) {
	return str_replace('"', '""', $str);
}

?>