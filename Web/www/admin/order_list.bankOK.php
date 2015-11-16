<?
session_start();
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");


$ordercode = $_GET['ordercode'];
$smsnum = $_GET['smsnum'];

$ordercodeList = explode(",",$ordercode);

if( strlen( $ordercode ) > 0 ) {
	$setDate = date("YmdHis");
	$i=0;
	foreach ( $ordercodeList as $var ) {

		$chkSQL = "SELECT * FROM tblorderinfo WHERE bank_date IS NULL AND paymethod = 'B' AND ordercode = '".$var."' LIMIT 1 ;";
		if( mysql_num_rows(mysql_query($chkSQL,get_db_conn())) ) {

			$sql = "UPDATE tblorderinfo SET bank_date = '".$setDate."' WHERE bank_date IS NULL AND paymethod = 'B' AND ordercode = '".$var."' LIMIT 1 ;";
			mysql_query($sql,get_db_conn());

			$i++;

			// SMS 발송
			$smssql="SELECT * FROM tblsmsinfo ";
			$smsresult=mysql_query($smssql,get_db_conn());
			if($smsrow=mysql_fetch_object($smsresult)) {
				$sms_id=$smsrow->id;
				$sms_authkey=$smsrow->authkey;
				$mem_bankok=$smsrow->mem_bankok;
				$mem_bankokvender=$smsrow->mem_bankokvender;
				$msg_mem_bankok=$smsrow->msg_mem_bankok;
				$fromtel=$smsrow->return_tel;
				$mem_present=$smsrow->mem_present;
				$msg_mem_present=$smsrow->msg_mem_present;
				$use_mms =($smsrow->use_mms=='Y')? $smsrow->use_mms:"";
			}
			mysql_free_result($result);
			if($mem_bankok == "Y"){

				// 주문정보
				$oSql = "SELECT * FROM tblorderinfo WHERE ordercode = '".$var."' LIMIT 1 ;";
				$oResult = mysql_query($oSql,get_db_conn());
				$oRow = mysql_fetch_object ( $oResult );
					$bankprice=$oRow->price;
					$bankname=$oRow->sender_name;
					$senderTel=$oRow->sender_tel;


				// 주문 회원에게 SMS 발송
				if( strlen($senderTel) > 0 ) {

					if(strlen($msg_mem_bankok)==0) $msg_mem_bankok="[".strip_tags($_shopdata->shopname)."] [DATE]의 주문이 입금확인 되었습니다. 빨리 발송해 드리겠습니다.";
					$patten=array("(\[DATE\])","(\[NAME\])","(\[PRICE\])");
					$replace=array(substr($ordercode,0,4)."/".substr($ordercode,4,2)."/".substr($ordercode,6,2),$bankname,$bankprice);

					$msg_mem_bankok=preg_replace($patten,$replace,$msg_mem_bankok);
					$msg_mem_bankok=addslashes($msg_mem_bankok);


					$date=0;
					$etcmsg="입금확인메세지(회원)";
					$temp=SendSMS($sms_id, $sms_authkey, $senderTel , "", $fromtel, $date, $msg_mem_bankok, $etcmsg);
				}

				// 입점사 상품이 있을경우 입점사에도 입금확인 메세지 전송
				if( $mem_bankokvender == "Y" ) {
					// 주문 상품 정보
					$senderTel = "";
					$opSql = "SELECT * FROM tblorderproduct WHERE ordercode = '".$var."' AND vender > 0 ;";
					$opResult = mysql_query($opSql,get_db_conn());
					while ( $opRow = mysql_fetch_object ( $opResult ) ) {

						// 벤더 관리자 연락처
						$vSql = "SELECT p_mobile FROM tblvenderinfo WHERE vender = ".$opRow->vender." ;";
						$vResult = @mysql_query($vSql,get_db_conn());
						if( $vRow = @mysql_fetch_object ( $vResult ) ) {
							if( strlen($vRow->p_mobile) > 10 ) $senderTel .= $vRow->p_mobile.",";
						}
					}

					$date=0;
					$etcmsg="입금확인메세지(벤더)";
					$temp=SendSMS($sms_id, $sms_authkey, $senderTel , "", $fromtel, $date, "v)".$msg_mem_bankok, $etcmsg);
				}
			}

			$scriptAdd = "";
			$productRoopSQL = "SELECT `productcode` FROM `tblorderproduct` WHERE `ordercode` = '".$var."' ; ";
			$productRoopResult = mysql_query($productRoopSQL,get_db_conn());
			while( $productRoopRow = mysql_fetch_assoc ( $productRoopResult ) ){
				if( strlen($productRoopRow['productcode']) == 18 ) {
					$scriptAdd .= "parent.document.getElementById('chkOrderProductCode_".$var.$productRoopRow['productcode']."').style.display='inline';\n";
				}
			}
			echo "
				<script type=\"text/javascript\">
				<!--
					parent.document.getElementById('orderBankOKobj_".$var."').style.display='none';
					parent.document.getElementById('orderBankOKOKobj_".$var."').style.display='block';
					".$scriptAdd ."
				//-->
				</script>
			";
		} else {
			echo "
				<script type=\"text/javascript\">
				<!--
					parent.document.getElementById('orderBankOKobj_".$var."').style.display='block';
					parent.document.getElementById('orderBankOKOKobj_".$var."').style.display='none';
				//-->
				</script>
			";
		}
	}
	if( $i > 1 ) {
		echo "
			<script type=\"text/javascript\">
			<!--
				alert(\"선택하신 주문건 중 미입금건만 입금완료처리했습니다.\");
			//-->
			</script>
		";
	}

	if( $i == 0 ) {
		echo "
			<script type=\"text/javascript\">
			<!--
				alert(\"일괄처리가능한 주문건이 없습니다.\");
			//-->
			</script>
		";
	}
}

?>