<?
session_start();
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/venderlib.php");
INCLUDE ("access.php");

// �ֹ�����Ʈ
$ordercode = $_POST['ordercode'];
$ordercodeList = explode(",",$ordercode);



//��۾�ü ����Ʈ
$deliCompanyArray = array();
$deliCompanySQL="SELECT * FROM tbldelicompany ORDER BY code ";
$deliCompanyResult=mysql_query($deliCompanySQL,get_db_conn());
while( $deliCompanyRow=mysql_fetch_assoc($deliCompanyResult) ) {
	$deliCompanyArray[$deliCompanyRow['code']] = $deliCompanyRow;
}


// ��ۿϷ� ó��
$ordercodeEndList = $_POST['ordercodelist'];
if( strlen($ordercodeEndList) > 0 AND $_POST['mode'] == "set" ){
	$ordercodeEndLists = explode("|",$ordercodeEndList);
	foreach ($ordercodeEndLists as $orders){
		if( strlen($orders) > 0 ){
			$ordersSet = explode(",",$orders);
			//_pr($ordersSet);

			$delimailok = "Y";

			$ordercode=$ordersSet[0];
			$deli_com=$ordersSet[1];
			$deli_num=$ordersSet[2];
			$deli_name=$deliCompanyArray[$deli_com]['company_name'];

			$sql="SELECT * FROM tblorderinfo WHERE ordercode='".$ordercode."'";
			$result=mysql_query($sql,get_db_conn());
			$_ord=mysql_fetch_object($result);
			mysql_free_result($result);

			$pgid_info="";
			$pg_type="";
			switch (substr($_ord->paymethod,0,1)) {
				case "B":
					break;
				case "V":
					$pgid_info=GetEscrowType($_shopdata->trans_id);
					$pg_type=$pgid_info["PG"];
					break;
				case "O":
					$pgid_info=GetEscrowType($_shopdata->virtual_id);
					$pg_type=$pgid_info["PG"];
					break;
				case "Q":
					$pgid_info=GetEscrowType($_shopdata->escrow_id);
					$pg_type=$pgid_info["PG"];
					break;
				case "C":
					$pgid_info=GetEscrowType($_shopdata->card_id);
					$pg_type=$pgid_info["PG"];
					break;
				case "P":
					$pgid_info=GetEscrowType($_shopdata->card_id);
					$pg_type=$pgid_info["PG"];
					break;
				case "M":
					$pgid_info=GetEscrowType($_shopdata->mobile_id);
					$pg_type=$pgid_info["PG"];
					break;
			}
			$pg_type=trim($pg_type);

			$tax_type=$_shopdata->tax_type;

			if(preg_match("/^(N|X|S)$/",$_ord->deli_gbn)) {

				$patterns = array("( )","(_)","(-)");
				$replace = array("","","");
				$deli_num = preg_replace($patterns,$replace,$deli_num);

				###����ũ�� ������ ������� ���� - ����ũ�� ������ ��쿡��.....

				if(strlen($deli_name)==0) {
					$deli_name="�ڰ����";
				}
				if(preg_match("/^(Q|P){1}/", $_ord->paymethod)) {

					if($pg_type=="A") {	//KCP
						$query="sitecd=".$pgid_info["ID"]."&sitekey=".$pgid_info["KEY"]."&ordercode=".$ordercode."&deli_num=".$deli_num."&deli_name=".urlencode($deli_name);

						$delivery_data=SendSocketPost(getenv("HTTP_HOST"),str_replace(getenv("HTTP_HOST"),"",$_ShopInfo->getShopurl())."paygate/".substr($_ord->paymethod,1,1)."/delivery.php",$query);

						$delivery_data=substr($delivery_data,strpos($delivery_data,"RESULT=")+7);
						if (substr($delivery_data,0,2)!="OK") {
							$tempdata=explode("|",$delivery_data);
							$errmsg="��������� ����ũ�� ������ �������� ���߽��ϴ�.\\n\\n����� �ٽ� �����Ͻñ� �ٶ��ϴ�.";
							if(strlen($tempdata[1])>0) $errmsg=$tempdata[1];
							echo "<script> alert('".$errmsg."');history.go(-1);</script>";
							exit;
						} else {
							$tempdata=explode("|",$delivery_data);
							if(strlen($tempdata[1])>0) $errmsg=$tempdata[1];
							if(strlen($errmsg)>0) {
								echo "<script> alert('".$errmsg."');</script>";
							}
						}
					} else if($pg_type=="B") {	//LG������
						$delicom_code="";
						if(strlen($deli_com)>0) {
							$sql = "SELECT dacom_code FROM tbldelicompany WHERE code='".$deli_com."' ";
							$result=mysql_query($sql,get_db_conn());
							if($row=mysql_fetch_object($result)) {
								$delicom_code=$row->dacom_code;
							}
							mysql_free_result($result);
						}
						$query="mid=".$pgid_info["ID"]."&mertkey=".$pgid_info["KEY"]."&ordercode=".$ordercode."&deli_num=".$deli_num."&delicom_code=".$delicom_code;

						$delivery_data=SendSocketPost(getenv("HTTP_HOST"),str_replace(getenv("HTTP_HOST"),"",$_ShopInfo->getShopurl())."paygate/".substr($_ord->paymethod,1,1)."/delivery.php",$query);

						$delivery_data=substr($delivery_data,strpos($delivery_data,"RESULT=")+7);
						if (substr($delivery_data,0,2)!="OK") {
							$tempdata=explode("|",$delivery_data);
							$errmsg="��������� ����ũ�� ������ �������� ���߽��ϴ�.\\n\\n����� �ٽ� �����Ͻñ� �ٶ��ϴ�.";
							if(strlen($tempdata[1])>0) $errmsg=$tempdata[1];
							echo "<script> alert('".$errmsg."');history.go(-1);</script>";
							exit;
						} else {
							$tempdata=explode("|",$delivery_data);
							if(strlen($tempdata[1])>0) $errmsg=$tempdata[1];
							if(strlen($errmsg)>0) {
								echo "<script> alert('".$errmsg."');</script>";
							}
						}
					} else if($pg_type=="C") {	//�ô�����Ʈ
						$query="storeid=".$pgid_info["ID"]."&ordercode=".$ordercode;

						$delivery_data=SendSocketPost(getenv("HTTP_HOST"),str_replace(getenv("HTTP_HOST"),"",$_ShopInfo->getShopurl())."paygate/".substr($_ord->paymethod,1,1)."/delivery.php",$query);

						$delivery_data=substr($delivery_data,strpos($delivery_data,"RESULT=")+7);
						if (substr($delivery_data,0,2)!="OK") {
							$tempdata=explode("|",$delivery_data);
							$errmsg="��������� ����ũ�� ������ �������� ���߽��ϴ�.\\n\\n����� �ٽ� �����Ͻñ� �ٶ��ϴ�.";
							if(strlen($tempdata[1])>0) $errmsg=$tempdata[1];
							echo "<script> alert('".$errmsg."');history.go(-1);</script>";
							exit;
						} else {
							$tempdata=explode("|",$delivery_data);
							if(strlen($tempdata[1])>0) $errmsg=$tempdata[1];
							if(strlen($errmsg)>0) {
								echo "<script> alert('".$errmsg."');</script>";
							}
						}
					} else if($pg_type=="D") {	//INICIS
						$delicom_code="";
						if(strlen($deli_com)>0) {
							$sql = "SELECT inicis_code FROM tbldelicompany WHERE code='".$deli_com."' ";
							$result=mysql_query($sql,get_db_conn());
							if($row=mysql_fetch_object($result)) {
								$delicom_code=$row->inicis_code;
							}
							mysql_free_result($result);
						}
						$query="sitecd=".$pgid_info["EID"]."&ordercode=".$ordercode."&deli_num=".$deli_num."&delicom_code=".$delicom_code."&deli_name=".urlencode($deli_name);

						$delivery_data=SendSocketPost(getenv("HTTP_HOST"),str_replace(getenv("HTTP_HOST"),"",$_ShopInfo->getShopurl())."paygate/".substr($_ord->paymethod,1,1)."/delivery.php",$query);

						$delivery_data=substr($delivery_data,strpos($delivery_data,"RESULT=")+7);
						if (substr($delivery_data,0,2)!="OK") {
							$tempdata=explode("|",$delivery_data);
							$errmsg="��������� ����ũ�� ������ �������� ���߽��ϴ�.\\n\\n����� �ٽ� �����Ͻñ� �ٶ��ϴ�.";
							if(strlen($tempdata[1])>0) $errmsg=$tempdata[1];
							echo "<script> alert('".$errmsg."');history.go(-1);</script>";
							exit;
						} else {
							$tempdata=explode("|",$delivery_data);
							if(strlen($tempdata[1])>0) $errmsg=$tempdata[1];
							if(strlen($errmsg)>0) {
								echo "<script> alert('".$errmsg."');</script>";
							}
						}
					}
				}

				/*$sql = "UPDATE tblorderinfo SET deli_gbn='Y', deli_date='".date("YmdHis")."' ";
				$sql.= "WHERE ordercode='".$ordercode."' ";
				if(mysql_query($sql,get_db_conn())) {*/
					$sql = "UPDATE tblorderproduct SET";
					if( strlen($deli_com) > 0 ) $sql.= " deli_com='".$deli_com."', ";
					if( strlen($deli_num) > 0 ) $sql.= " deli_num='".$deli_num."', ";
					$sql.= " deli_gbn='Y',";
					$sql.= " deli_date='".date("YmdHis")."' ";
					$sql.= "WHERE ordercode='".$ordercode."' ";
					$sql.= "AND NOT (productcode LIKE '999%' OR productcode LIKE 'COU%') ";
					$sql.= "AND deli_gbn!='Y' ";
					$sql.= "AND vender='" .$_VenderInfo->vidx. "' ";
					mysql_query($sql,get_db_conn());
				//}
				$isupdate=true;
				$first_recomId ="";
				//��õ�� ù���Ž� ���� Ȯ��
				if($recom_ok =="Y" && $arRecomType[0] == "B"){
					$sql = "SELECT rec_id FROM tblmember WHERE id='".$_ord->id."' ";
					$result=mysql_query($sql,get_db_conn());
					if($row=mysql_fetch_object($result)) {
						if(trim($row->rec_id) !=""){
							//ù���ſ���üũ
							$sql2 = "SELECT COUNT(ordercode) as t_count FROM tblorderinfo where  deli_gbn='Y' AND id='".$_ord->id."'";
							$result2 = mysql_query($sql2,get_db_conn());
							if($row2=mysql_fetch_object($result2)) {
								$firstOrderCnt=$row2->t_count;
							}
							if($firstOrderCnt == 1 ){
								if($totalRecom >0){
									SetReserve($row->rec_id,$totalRecom,$_ord->id."���� ��õ�ϰ� ���ſϷ��ϼ̽��ϴ�.");
									//������ ���� �Է� - ��ҽ�..
									$sql = "INSERT tblreservefirst SET ";
									$sql.= "id			= '".$_ord->id."', ";
									$sql.= "reserve		= '".$totalRecom."', ";
									$sql.= "ordercode	= '".$ordercode."', ";
									$sql.= "rec_id		= '".$row->rec_id."', ";
									$sql.= "date		= '".date("YmdHis")."', ";
									$sql.= "cancelchk	= 'N' ";
									mysql_query($sql,get_db_conn());
									$first_recomId = $_ord->id;
								}
							}
							mysql_free_result($result2);
						}
					}
					mysql_free_result($result);
				}

				//sns ȫ��id
				if(sizeof($arSell_id)>0 && $arSnsType[0] != "N") {
					for($jj=0;$jj<sizeof($arSell_id);$jj++){
						//ù ���Ž� ����
						if($first_recomId != $arSell_id[$jj]) {
							SetReserve($arSell_id[$jj],$arSell_rsv[$jj],$_ord->id."���� snsȫ���� ���� ��ǰ�� �����ϼ̽��ϴ�.");
						}
					}
				}

				if($in_reserve>0) {
					$sql = "SELECT reserve FROM tblmember WHERE id='".$_ord->id."' ";
					$result=mysql_query($sql,get_db_conn());
					if($row=mysql_fetch_object($result)) {
						$reservemoney=$in_reserve + $row->reserve;
						$sql = "UPDATE tblmember SET reserve = ".abs($reservemoney)." ";
						$sql.= "WHERE id='".$_ord->id."' ";
						mysql_query($sql,get_db_conn());

						$sql = "INSERT tblreserve SET ";
						$sql.= "id			= '".$_ord->id."', ";
						$sql.= "reserve		= '".$in_reserve."', ";
						$sql.= "reserve_yn	= 'Y', ";
						$sql.= "content		= '��ǰ ���԰ǿ� ���� ������ ����', ";
						$sql.= "orderdata	= '".$ordercode."=".$_ord->price."', ";
						$sql.= "date		= '".date("YmdHis")."' ";
						mysql_query($sql,get_db_conn());
						$in_reserve=0;
					}
					mysql_free_result($result);
				}
			} else if(!preg_match("/^(N|X|S)$/",$_ord->deli_gbn)) {
				echo "<script>alert(\"�̹� ��ҵǰų� �߼۵� ��ǰ�Դϴ�. �ٽ� Ȯ���Ͻñ� �ٶ��ϴ�.\");</script>";
			}


		}
	}
	echo "<script>if(opener) {opener.history.go(0);} window.close(); </script>";
	exit;

}
?>

<!-- <link rel="stylesheet" href="/admin/style.css" type="text/css"> -->
<html>
	<head>
	<style>
body div form table {margin:0px; padding:0px; border:0px;}
.listOrderEnd {width:96%; background-color:#d8d8d8; margin-top:25px;}
.listOrderEnd th {height:22px; background-color:#efefef; font-family:����; font-size:11px;}
.listOrderEnd td {background-color:#ffffff; font-size:11px; text-align:center;}
.listOrderEnd select {font-size:11px; font-family:����;}
#deliCompnay_change {font-size:11px; font-family:����;}

</style>
	</head>
	<body topmargin="0" leftmargin="0">
<?
if( strlen( $ordercode ) > 0 ) {
	$print = "";
	$print .= "<form name=\"OrderEndForm\">";
	$print .= "<div><img src=\"images/tit_listorderend.gif\" alt=\"\" /></div>";
	$print .= "<div style=\"margin:10px 25px; color:#999999; font-size:11px; font-family:����;\"><b>��</b> �߼۴�� ������ �ֹ��ǿ� ���� �ϰ����ó���� �����մϴ�.<br /><b>��</b> ��۾�ü�� ��� ������ ��� �ϴ��� [��۾�ü �ϰ�����]�� ����Ͻø� �ѹ��� ��۾�ü ������ �����մϴ�.<br /><b>��</b> ������ȣ ������ �� �ֹ����� �󼼳��� ���⿡�� ������ �����մϴ�.</div>";
	$print .= "<table border=0 cellpadding=2 cellspacing=1 align=center class=\"listOrderEnd\">";
	$print .= "	<thead>";
	$print .= "		<tr>";
	$print .= "			<th width=22></th>";
	$print .= "			<th>�ֹ��ڵ�</th>";
	$print .= "			<th>�ֹ���</th>";
	$print .= "			<th>�����ݾ�</th>";
	$print .= "			<th>��ۻ���</th>";
	$print .= "			<th>��������</th>";
	$print .= "			<th>��۾�ü</th>";
	$print .= "			<th>������ȣ</th>";
	$print .= "		</tr>";
	$print .= "	</thead>";
	$print .= "	<tbody>";


	//����Ʈ
	$cnt=0;
	foreach ( $ordercodeList as $var ) {
		if( strlen($var) > 0 ) {
			$sel_sql = "SELECT * FROM tblorderinfo WHERE ordercode = '".$var."' and NOT (select count(vender) from tblorderproduct where NOT deli_gbn='Y' AND vender='".$_VenderInfo->vidx."' AND ordercode = '".$var."') = 0;";
			$sel_result = mysql_query($sel_sql,get_db_conn());
			$sel_row = mysql_fetch_assoc ($sel_result);
			if(empty($sel_row))
				continue;

			// �ֹ��ڸ�(���̵�)
			$senderName = $sel_row['sender_name']."<br /><span style=\"font-size:11px;\">".( strlen($sel_row['id'])>0 ? "(".$sel_row['id'].")" : "" )."</span>";

			//�Աݻ���
			$arpm=array("B"=>"������","V"=>"������ü","O"=>"�������","Q"=>"�������<br />(�Ÿź�ȣ)","C"=>"�ſ�ī��","P"=>"�ſ�ī��<br />(�Ÿź�ȣ)","M"=>"�ڵ���");
			$paymethod = substr($sel_row['paymethod'],0,1);
			$paymethodBankOk = "";
			$paymethodCHK = true;
			if( $paymethod == "B" ) {
				if( strlen($sel_row['bank_date']) == 0 ) {
					$paymethodBankOk = "<font color='blue'><strong>[���Ա�]</strong></font>";
					$paymethodCHK = false;
				} else {
					$paymethodBankOk = "[�ԱݿϷ�]";
				}
			}

			// ��ۻ���
			$ardg=array("N"=>"��ó��","S"=>"��۴��<br />(�߼��غ�)","Y"=>"���","C"=>"�ֹ����","R"=>"�ݼ�","D"=>"��ҿ�û","E"=>"ȯ�Ҵ��","H"=>"���<br />(���꺸��)");
			$deliveryState = $ardg[$sel_row['deli_gbn']];


			if( $sel_row['deli_gbn'] == "Y" ) {
				$paymethodCHK = false;
			}

			$paymethod = $arpm[$paymethod]."<br />".$paymethodBankOk;

			// �ʱ�ȭ
			$deliCompnay = "";
			$deliCode = "";
			$ordercodeChkBox = "";

			// ���ó�� ���� ��ǰ��
			if( $paymethodCHK == true ) {

				// ��۾�ü
				$deliCompnay = "<select name=\"deliCompnay_".$var."\" id=\"deliCompnay\">";
				foreach( $deliCompanyArray as $deliComRow ) {
					$deliCompnay.="<option value=\"".$deliComRow['code']."\">".$deliComRow['company_name']."</option>\n";
				}
				$deliCompnay .= "</select>";

				// �����ȣ
				$deliCode = "<input type=\"text\" name=\"deliCode_".$var."\" style=\"width:110px; font-size:11px;\">";

				// ���� ����
				$ordercodeChkBox = "<input type=\"checkbox\" name=\"ordercodes\" id=\"ordercodes_".$var."\" value=\"".$sel_row['ordercode']."\" checked>";
			$cnt++;
			}

			//����Ʈ
			$print .= "<tr>";
			$print .= "		<td>".$ordercodeChkBox."</td>";
			$print .= "		<td>".$var."</td>";
			$print .= "		<td>".$senderName."</td>";
			$print .= "		<td>".number_format($sel_row['price'])."</td>";
			$print .= "		<td>".$deliveryState."</td>";
			$print .= "		<td>".$paymethod."</td>";
			$print .= "		<td>".$deliCompnay."</td>";
			$print .= "		<td>".$deliCode."</td>";
			$print .= "</tr>";
		}

		
	}

	$print .= "	</tbody>";
	$print .= "</table>";
	$print .= "</form>";

	$print .= "<form name=\"OrderEndSendForm\">";
	$print .= "	<input type='hidden' name='ordercodelist' value=''>";
	$print .= "	<input type='hidden' name='mode' value='set'>";
	$print .= "</form>";
}
?>
<script type="text/javascript">
<!--
	//��۾�ü �ϰ� ����
	function chgAll( v ) {
		var K = v.selectedIndex;
		var F = document.OrderEndForm;
		if( K > 0 ) {
			var ordercode = "";
			for(i=0;i<=F.ordercodes.length;i++) {
				ordercode = F.ordercodes[i].value;
				eval( "F.deliCompnay_"+ordercode+"["+(K-1)+"].selected = true;" );
			}
		}
	}

	// ��ۿϷ� ó�� üũ
	function sendCHK () {
		var F = document.OrderEndForm;
		var SF = document.OrderEndSendForm;
		var O = F.ordercodes;
		var ordercode = "";
		var ordercodeList = "";
		if( O.length > 0 ) {
			for(i=0;i<O.length;i++) {
				if( O[i].checked == true ) {
					ordercode = O[i].value;
					var deliCode = eval("F.deliCode_"+ordercode);
					var deliCompnay = eval("F.deliCompnay_"+ordercode);
					if( deliCode.value== "") {
						alert("����ڵ尡 ���� �Ǿ����ϴ�.");
						deliCode.focus();
						return false;
					}
					ordercodeList += ordercode+","+deliCompnay.value+","+deliCode.value+"|";
				}
			}
		} else {
			ordercode = O.value;
			var deliCode = eval("F.deliCode_"+ordercode);
			var deliCompnay = eval("F.deliCompnay_"+ordercode);
			ordercodeList = ordercode+","+deliCompnay.value+","+deliCode.value+"|";
		}
		SF.ordercodelist.value=ordercodeList;
		SF.method = "POST";
		SF.action = '<?=$_SERVER['PHP_SELF']?>';
		SF.submit();
	}
//-->
</script>



<!-- ����Ʈ -->
<?=$print?>





<div style="float:left; margin-left:14px;">
<!-- ��۾�ü �ϰ� ���� -->
<?if($cnt>1){?>
<select name="deliCompnay_change" id="deliCompnay_change" onchange="chgAll(this);">
	<option value="">��۾�ü �ϰ� ����</option>
<?
	foreach( $deliCompanyArray as $deliComRow ) {
		echo "<option value=\"".$deliComRow['code']."\">".$deliComRow['company_name']."</option>\n";
	}
?>
</select>
<?}?>
</div>

<!-- ���ó�� �Ϸ� ��ư -->
<div style="float:left; margin-left:5px;"><img src="images/btn_deliEndOkAll.gif" alt="�����ֹ� ��ۿϷ� ó��" style="cursor:pointer;" onclick="sendCHK();"></div>
<div style="clear:both; margin-top:20px; text-align:center;"><a href="javascript:window.close();"><img src="/images/common/bigview_btnclose.gif" border="0"/></a></div>


<?
	//_pr($_POST);
?>
	</body>
</html>