<?
include "header.php";
include_once($Dir."lib/ext/product_func.php");
include_once($Dir."lib/ext/member_func.php");
include_once($Dir."lib/ext/order_func.php");
include_once($Dir."lib/ext/coupon_func.php");

$ordertype=$_GET["ordertype"];

//ȸ�������� ��� �α���������...
if($_data->member_buygrant=="Y" && strlen($_ShopInfo->getMemid())==0) {
	Header("Location:./login.php?chUrl=".getUrl());
	exit;
}

$origloc = $_SERVER['DOCUMENT_ROOT']."/data/shopimages/product/"; // �������� ���
$saveloc = $_SERVER['DOCUMENT_ROOT']."/data/shopimages/mobile/"; // �泻�� ���� ���
$quality = 100;

//��ٱ��� ����Ű Ȯ��
if(strlen($_ShopInfo->getTempkey())==0 || $_ShopInfo->getTempkey()=="deleted") {
	$_ShopInfo->setTempkey($_data->ETCTYPE["BASKETTIME"]);
}
if(strlen($_ShopInfo->getMemid()) > 0){
	$sql ="UPDATE tblbasket SET sell_memid ='' WHERE tempkey='".$_ShopInfo->getTempkey()."' AND sell_memid='".$_ShopInfo->getMemid()."'";
	mysql_query($sql,get_db_conn());
}

// ��ٱ��� ������ (Array) ==================================================
$basketItems = getBasketByArray();


/*
echo "<div style=\" height:500px; overflow:scroll;  border:2px solid #ff0000 ;  text-align:left;\">";
_pr($basketItems);
echo "</div>";
*/

/*
ȸ�� ��� ���� �޼��� ============
	RW : �ݾ� �߰� ����
	RP  : % �߰� ����
	SW : �ݾ� �߰� ����
	SP  : % �߰� ����
*/

$groupMemberSale = "";
if( $basketItems['groupMemberSale'] ) {
	$groupMemberSale .= "
		<font style=\"letter-spacing:0px;\"><b>".$basketItems['groupMemberSale']['name']."</b></font>��(".$basketItems['groupMemberSale']['group'].")�� ȸ�� ��� ����
		<font color=\"#ee0a02\" style=\"letter-spacing:0px;\">".number_format($basketItems['groupMemberSale']['useMoney'])."</font>�� �̻�
		<font  color=\"#ee0a02\">".$basketItems['groupMemberSale']['payType']."</font> ������
	";
	if($basketItems['groupMemberSale']['groupCode']=="RW") {
		$groupMemberSale .= "<font color=#ee0a02 style=letter-spacing:0px;><b>".number_format($basketItems['groupMemberSale']['addMoney'])."</b>��</font>�� �������� �߰��� ������ �帳�ϴ�.";
	} else if($basketItems['groupMemberSale']['groupCode']=="RP") {
		$groupMemberSale .= "<font color=#ee0a02 style=letter-spacing:0px;><b>���űݾ��� ".number_format($basketItems['groupMemberSale']['addMoney'])."</b>%</font>�� ������ �帳�ϴ�.";
	} else if($basketItems['groupMemberSale']['groupCode']=="SW") {
		$groupMemberSale .= "<font color=#ee0a02 style=letter-spacing:0px;><b>���űݾ� ".number_format($basketItems['groupMemberSale']['addMoney'])."</b>��</font>�� �߰��� ���� �˴ϴ�.";
	} else if($basketItems['groupMemberSale']['groupCode']=="SP") {
		$groupMemberSale .= "<font color=#ee0a02 style=letter-spacing:0px;><b>���űݾ��� ".number_format($basketItems['groupMemberSale']['addMoney'])."</b>%</font>�� �߰��� ���� �˴ϴ�.";
	}
	$groupMemberSale .= "<span id=\"couponEventMsg\"></span>";
}
#### PG ����Ÿ ���� ####
$_ShopInfo->getPgdata();
########################

//////  ���� ���� ���� start  ////////////////////////////////////////////////

// ���� ���ݰ��� ���� ���� üũ
$bankonlyCHK = "N";
foreach ( $basketItems['vender'] as $venderKey => $venderValue ) {
	foreach ( $venderValue['products'] as $productsKey=> $productsValue ){
		if( $productsValue['bankonly'] ) {
			$bankonlyCHK = "Y";
		}
	}
}

$escrow_info = GetEscrowType($_data->escrow_info);

$payType = "";

//������
/*if( preg_match("/^(Y|N)$/", $_data->payment_type) && $escrow_info["onlycard"]!="Y" ) {
	$payType .= "<input type='radio' onclick=\"change_paymethod(1);\" name='sel_paymethod' value='B' id=\"sel_paymethod1\"><label for=\"sel_paymethod1\" style=\"cursor:pointer;\">������ �Ա�</label>&nbsp;&nbsp;";
}

//2:�ſ�ī��: ���ݰ����� ��Ȱ��
if(preg_match("/^(Y|C)$/", $_data->payment_type) && strlen($_data->card_id)>0 AND $bankonlyCHK == "N" ) {
	$payType .= "<input type='radio' onclick=\"change_paymethod(2);\" name='sel_paymethod' value='C' id=\"sel_paymethod2\"><label for=\"sel_paymethod2\" style=\"cursor:pointer;\">�ſ�ī��</label>&nbsp;&nbsp;";
}

//2:�ǽð�������ü
if($escrow_info["onlycard"]!="Y" ) {
	if(strlen($_data->trans_id)>0) {
		$payType .= "<input type='radio' onclick=\"change_paymethod(3);\" name='sel_paymethod' value='V' id=\"sel_paymethod3\"><label for=\"sel_paymethod3\" style=\"cursor:pointer;\">�ǽð�������ü</label>&nbsp;&nbsp;";
	}
}

//3:�������
if($escrow_info["onlycard"]!="Y" ) {
	if(strlen($_data->virtual_id)>0) {
		//$payType .= "<input type='radio' onclick=\"change_paymethod(4);\" name='sel_paymethod' value='O' id=\"sel_paymethod4\"><label for=\"sel_paymethod4\" style=\"cursor:pointer;\">�������</label>&nbsp;&nbsp;";
	}
}

//4:����ũ��
if(($escrow_info["escrowcash"]=="A" || $escrow_info["escrowcash"]=="Y") && strlen($_data->escrow_id)>0) {
	$pgid_info="";
	$pg_type="";
	$pgid_info=GetEscrowType($_data->escrow_id);
	$pg_type=trim($pgid_info["PG"]);

	if(preg_match("/^(A|B|C|D)$/",$pg_type)) {
		//KCP/������/�ô�����Ʈ/�̴Ͻý� ������� ����ũ�� �ڵ�
		$payType .= "<input type='radio' onclick=\"change_paymethod(5);\" name='sel_paymethod' value='Q' id=\"sel_paymethod5\"><label for=\"sel_paymethod5\" style=\"cursor:pointer;\">������ݿ�ġ��(����ũ��)</label>&nbsp;&nbsp;";
	}
}

//5:�ڵ��� : ���ݰ����� ��Ȱ��
if(strlen($_data->mobile_id)>0 AND $bankonlyCHK == "N" ) {
	//$payType .= "<input type='radio' onclick=\"change_paymethod(6);\" name='sel_paymethod' value='M' id=\"sel_paymethod6\"><label for=\"sel_paymethod6\" style=\"cursor:pointer;\">�ڵ��� ����</label>";
}
*/
//���ݰ��� ���� ��ǰ ���Խ� �޼���
if( $bankonlyCHK == "Y" ) {
	$payType .= "&nbsp;&nbsp;&nbsp;<font color='#FF0000'>(*�ֹ� ��ǰ�� [���ݰ���] ��ǰ�� ���ԵǾ� �ſ�ī������� �Ұ����մϴ�.)</font>";
}

//////  ���� ���� ���� end  ////////////////////////////////////////////////




// �������� ���� ��ũ
$offlineCouponInputButton = "<img src='/images/common/order/T01/offlineCouponInputButton.gif' align='absmiddle' style='cursor:pointer;' alt='�������� ���� ���' onclick=\" coupon_check( 'offlinecoupon' );\">";


// shopinfo ����ǰ Ȱ��ȭ ���� ȣ��
$giftInfoRow = @mysql_fetch_object( mysql_query("SELECT `gift_type` FROM `tblshopinfo` LIMIT 1;",get_db_conn()) );
$giftInfoSetArray = explode("|",$giftInfoRow->gift_type);


#��������ľ�
$errmsg="";
$sql = "SELECT a.quantity as sumquantity,b.productcode,b.productname,b.display,b.quantity, ";
$sql.= "b.option_quantity,b.etctype,b.group_check,b.assembleuse,a.assemble_list AS basketassemble_list ";
$sql.= ", c.assemble_list,a.package_idx ";
$sql.= "FROM tblbasket a, tblproduct b ";
$sql.= "LEFT OUTER JOIN tblassembleproduct c ON b.productcode=c.productcode ";
$sql.= "WHERE a.tempkey='".$_ShopInfo->getTempkey()."' ";
$sql.= "AND a.productcode=b.productcode ";
$result=mysql_query($sql,get_db_conn());
$assemble_proquantity_cnt=0;
while($row=mysql_fetch_object($result)) {
	if($row->display!="Y") {
		$errmsg="[".ereg_replace("'","",$row->productname)."]��ǰ�� �ǸŰ� ���� �ʴ� ��ǰ�Դϴ�.\\n";
	}

	// today sale �Ǹ� �ð� ���� check
	if(preg_match('/^899[0-9]{15}$/',$row->productcode)){
		$tsql = "select unix_timestamp(t.end) -unix_timestamp() as remain, t.salecnt+t.addquantity as sellcnt from tblproduct a inner join todaysale t using(pridx) WHERE a.productcode='".$row->productcode."' limit 1";
		if(false === $tres = mysql_query($tsql,get_db_conn())){
			$errmsg="[".ereg_replace("'","",$row->productname)."]�� ������ DB ���� Ȯ�� �ϴ��� ������ �߻��߽��ϴ�..\\n";
		}else{
			if(mysql_num_rows($tres) < 1){
				$errmsg="[".ereg_replace("'","",$row->productname)."]�� ������ ã���� �����ϴ�.\\n";
			}else{
				$trow = mysql_fetch_assoc($tres);
				if($trow['remain'] < 1){
					$errmsg="[".ereg_replace("'","",$row->productname)."]�� �Ǹ� ������ ��ǰ �Դϴ�.\\n";
					mysql_query("delete from tblbasket where a.tempkey='".$_ShopInfo->getTempkey()."' and productcode='".$row->productcode."'",get_db_conn()); // ���� ó��
				}
			}
		}
	}

	if($row->group_check!="N") {
		if(strlen($_ShopInfo->getMemid())>0) {
			$sqlgc = "SELECT COUNT(productcode) AS groupcheck_count FROM tblproductgroupcode ";
			$sqlgc.= "WHERE productcode='".$row->productcode."' ";
			$sqlgc.= "AND group_code='".$_ShopInfo->getMemgroup()."' ";
			$resultgc=mysql_query($sqlgc,get_db_conn());
			if($rowgc=@mysql_fetch_object($resultgc)) {
				if($rowgc->groupcheck_count<1) {
					$errmsg="[".ereg_replace("'","",$row->productname)."]��ǰ�� ���� ��� ���� ��ǰ�Դϴ�.\\n";
				}
				@mysql_free_result($resultgc);
			} else {
				$errmsg="[".ereg_replace("'","",$row->productname)."]��ǰ�� ���� ��� ���� ��ǰ�Դϴ�.\\n";
			}
		} else {
			$errmsg="[".ereg_replace("'","",$row->productname)."]��ǰ�� ȸ�� ���� ��ǰ�Դϴ�.\\n";
		}
	}


	if(strlen($errmsg)==0) {
		$miniq=1;
		$maxq="?";
		if(strlen($row->etctype)>0) {
			$etctemp = explode("",$row->etctype);
			for($i=0;$i<count($etctemp);$i++) {
				if(substr($etctemp[$i],0,6)=="MINIQ=")     $miniq=substr($etctemp[$i],6);
				if(substr($etctemp[$i],0,5)=="MAXQ=")      $maxq=substr($etctemp[$i],5);
			}
		}

		if(strlen(dickerview($row->etctype,0,1))>0) {
			$errmsg="[".ereg_replace("'","",$row->productname)."]��ǰ�� �ǸŰ� ���� �ʽ��ϴ�. �ٸ� ��ǰ�� �ֹ��� �ּ���.\\n";
		}
	}

	if(strlen($errmsg)==0) {
		if ($miniq!=1 && $miniq>1 && $row->sumquantity<$miniq)
			$errmsg.="[".ereg_replace("'","",$row->productname)."]��ǰ�� �ּ� ".$miniq."�� �̻� �ֹ��ϼž� �մϴ�.\\n";

		if ($maxq!="?" && $maxq>0 && $row->sumquantity>$maxq)
			$errmsg.="[".ereg_replace("'","",$row->productname)."]��ǰ�� �ִ� ".$maxq."�� ���Ϸ� �ֹ��ϼž� �մϴ�.\\n";

		if(strlen($row->quantity)>0) {
			if ($row->sumquantity>$row->quantity) {
				if ($row->quantity>0)
					$errmsg.="[".ereg_replace("'","",$row->productname)."]��ǰ�� ��� ".($_data->ETCTYPE["STOCK"]=="N"?"�����մϴ�.":"���� ".$row->quantity." �� �Դϴ�.")."\\n";
				else
					$errmsg.= "[".ereg_replace("'","",$row->productname)."]��ǰ�� ��� �ٸ��� �ֹ����� ������ ��ٱ��� �������� �۽��ϴ�.\\n";
			}
		}
		if($assemble_proquantity_cnt==0) { //�Ϲ� �� ������ǰ���� ��� ��������
			///////////////////////////////// �ڵ�/���� ������� ���� ��� üũ ///////////////////////////////////////////////
			$basketsql = "SELECT productcode,assemble_list,quantity,assemble_idx FROM tblbasket WHERE tempkey='".$_ShopInfo->getTempkey()."' ";
			$basketresult = mysql_query($basketsql,get_db_conn());
			while($basketrow=@mysql_fetch_object($basketresult)) {
				if($basketrow->assemble_idx>0) {
					if(strlen($basketrow->assemble_list)>0) {
						$assembleprolistexp = explode("",$basketrow->assemble_list);
						for($i=0; $i<count($assembleprolistexp); $i++) {
							if(strlen($assembleprolistexp[$i])>0) {
								$assemble_proquantity[$assembleprolistexp[$i]]+=$basketrow->quantity;
							}
						}
					}
				} else {
					$assemble_proquantity[$basketrow->productcode]+=$basketrow->quantity;
				}
			}
			@mysql_free_result($basketresult);
			$assemble_proquantity_cnt++;
		}
		if(count($assemble_list_exp)>0) { // ������ǰ�� ��� üũ
			$assemprosql = "SELECT productcode,quantity,productname FROM tblproduct ";
			$assemprosql.= "WHERE productcode IN ('".implode("','",$assemble_list_exp)."') ";
			$assemprosql.= "AND display = 'Y' ";
			$assemproresult=mysql_query($assemprosql,get_db_conn());
			while($assemprorow=@mysql_fetch_object($assemproresult)) {
				if(strlen($assemprorow->quantity)>0) {
					if($assemble_proquantity[$assemprorow->productcode]>$assemprorow->quantity) {
						if($assemprorow->quantity>0) {
							$errmsg.="[".ereg_replace("'","",$row->productname)."]��ǰ�� ������ǰ [".ereg_replace("'","",$assemprorow->productname)."] ��� ".($_data->ETCTYPE["STOCK"]=="N"?"�����մϴ�.":"���� ".$assemprorow->quantity." �� �Դϴ�.")."\\n";
						} else {
							$errmsg.="[".ereg_replace("'","",$row->productname)."]��ǰ�� ������ǰ [".ereg_replace("'","",$assemprorow->productname)."] �ٸ� ���� �ֹ����� ǰ���Ǿ����ϴ�.\\n";
						}
					}
				}
			}
			@mysql_free_result($assemproresult);
		} else if(strlen($package_productcode_tmp)>0) {
			$package_productcode_tmpexp = explode("",$package_productcode_tmp);
			$package_quantity_tmpexp = explode("",$package_quantity_tmp);
			$package_productname_tmpexp = explode("",$package_productname_tmp);
			for($i=0; $i<count($package_productcode_tmpexp); $i++) {
				if(strlen($package_productcode_tmpexp[$i])>0) {
					if(strlen($package_quantity_tmpexp[$i])>0) {
						if($assemble_proquantity[$package_productcode_tmpexp[$i]] > $package_quantity_tmpexp[$i]) {
							if($package_quantity_tmpexp[$i]>0) {
								$errmsg.="�ش� ��ǰ�� ��Ű�� [".ereg_replace("'","",$package_productname_tmpexp[$i])."] ��� ".($_data->ETCTYPE["STOCK"]=="N"?"�����մϴ�.":"���� ".$package_quantity_tmpexp[$i]." �� �Դϴ�.")."\\n";
							} else {
								$errmsg.="�ش� ��ǰ�� ��Ű�� [".ereg_replace("'","",$package_productname_tmpexp[$i])."] �ٸ� ���� �ֹ����� ǰ���Ǿ����ϴ�.\\n";
							}
						}
					}
				}
			}
		} else { // �Ϲݻ�ǰ�� ��� üũ
			if(strlen($row->quantity)>0) {
				if($assemble_proquantity[$assemprorow->productcode]>$row->quantity) {
					if ($row->quantity>0) {
						$errmsg.="[".ereg_replace("'","",$row->productname)."]��ǰ�� ��� ".($_data->ETCTYPE["STOCK"]=="N"?"�����մϴ�.":"���� ".$row->quantity." �� �Դϴ�.")."\\n";
					} else {
						$errmsg.= "[".ereg_replace("'","",$row->productname)."]��ǰ�� ��� �ٸ��� �ֹ����� ������ ��ٱ��� �������� �۽��ϴ�.\\n";
					}
				}
			}
		}
		if(strlen($row->option_quantity)>0) {
			$sql = "SELECT opt1_idx, opt2_idx, quantity FROM tblbasket ";
			$sql.= "WHERE tempkey='".$_ShopInfo->getTempkey()."' ";
			$sql.= "AND productcode='".$row->productcode."' ";
			$result2=mysql_query($sql,get_db_conn());
			while($row2=mysql_fetch_object($result2)) {
				$optioncnt = explode(",",substr($row->option_quantity,1));
				$optionvalue=$optioncnt[($row2->opt2_idx==0?0:($row2->opt2_idx-1))*10+($row2->opt1_idx-1)];

				if($optionvalue<=0 && $optionvalue!="") {
					$errmsg.="[".ereg_replace("'","",$row->productname)."]��ǰ�� �ɼ��� �ٸ� ���� �ֹ����� ǰ���Ǿ����ϴ�.\\n";
				} else if($optionvalue<$row2->quantity && $optionvalue!="") {
					$errmsg.="[".ereg_replace("'","",$row->productname)."]��ǰ�� ���õ� �ɼ��� ��� ".($_data->ETCTYPE["STOCK"]=="N"?"�����մϴ�.":"$optionvalue �� �Դϴ�.")."\\n";
				}
			}
			@mysql_free_result($result2);
		}
	}
}
@mysql_free_result($result);

if(strlen($errmsg)>0) {
	echo "<html></head><body onload=\"alert('".$errmsg."');location.href='/m/basket.php';\"></body></html>";
	exit;
}







//���� ������ ���� ���
if($_REQUEST['mode']=="coupon" && strlen($_REQUEST['coupon_code'])==8){
	$onload = '';
	if(strlen($_ShopInfo->getMemid())==0) {	//��ȸ��
		echo "<html></head><body onload=\"alert('�α��� �� ���� �ٿ�ε尡 �����մϴ�.');location.href='./login.php?chUrl=".getUrl()."';\"></body></html>";exit;
	}else{
		$sql = "SELECT * FROM tblcouponinfo where coupon_code = '".$_REQUEST['coupon_code']."'";


		$result=mysql_query($sql,get_db_conn());
		if($row=mysql_fetch_object($result)) {
			if($row->issue_tot_no>0 && $row->issue_tot_no<$row->issue_no+1) {
				$onload="<script>alert(\"��� ������ �߱޵Ǿ����ϴ�.\");</script>";
			} else {
				$date=date("YmdHis");
				if($row->date_start>0) {
					$date_start=$row->date_start;
					$date_end=$row->date_end;
				} else {
					$date_start = substr($date,0,10);
					$date_end = date("Ymd",mktime(0,0,0,substr($date,4,2),substr($date,6,2)+abs($row->date_start),substr($date,0,4)))."23";
				}
				$sql = "INSERT tblcouponissue SET ";
				$sql.= "coupon_code	= '".$_REQUEST['coupon_code']."', ";
				$sql.= "id			= '".$_ShopInfo->getMemid()."', ";
				$sql.= "date_start	= '".$date_start."', ";
				$sql.= "date_end	= '".$date_end."', ";
				$sql.= "date		= '".$date."' ";

				mysql_query($sql,get_db_conn());
				if(!mysql_errno()) {
					$sql = "UPDATE tblcouponinfo SET issue_no = issue_no+1 ";
					$sql.= "WHERE coupon_code = '".$_REQUEST['coupon_code']."'";
					mysql_query($sql,get_db_conn());

					$onload="<script>alert(\"�ش� ���� �߱��� �Ϸ�Ǿ����ϴ�.\\n\\n��ǰ �ֹ��� �ش� ������ ����Ͻ� �� �ֽ��ϴ�.\");</script>";
				} else {
					if($row->repeat_id=="Y") {	//������ ��߱��� �����ϴٸ�,,,,
						$sql = "UPDATE tblcouponissue SET ";
						if($row->date_start<=0) {
							$sql.= "date_start	= '".$date_start."', ";
							$sql.= "date_end	= '".$date_end."', ";
						}
						$sql.= "used		= 'N' ";
						$sql.= "WHERE coupon_code='".$_REQUEST['coupon_code']."' ";
						$sql.= "AND id='".$_ShopInfo->getMemid()."' ";
						mysql_query($sql,get_db_conn());
						$onload="<script>alert(\"�ش� ���� �߱��� �Ϸ�Ǿ����ϴ�.\\n\\n��ǰ �ֹ��� �ش� ������ ����Ͻ� �� �ֽ��ϴ�.\");</script>";
					} else {
						$onload="<script>alert(\"�̹� ������ �߱޹����̽��ϴ�.\\n\\n�ش� ������ ��߱��� �Ұ����մϴ�.\");</script>";
					}
				}
			}
		}
		mysql_free_result($result);

	}

	if(_empty($onload)){
		echo $onload;
	}
	?>
	<script language="javascript" type="text/javascript">
		document.location.replace('./order.php');
	</script>
	<?
	exit;

}


// ���� ���� ����Ʈ
$mycoupon_codes = getMyCouponList('',true);







$card_miniprice=$_data->card_miniprice;
$deli_area=$_data->deli_area;
$admin_message = $_data->order_msg;
$reserve_limit = $_data->reserve_limit;
$reserve_maxprice = $_data->reserve_maxprice;
if($reserve_limit==0) $reserve_limit=1000000000000;

if($reserve_limit<0) $reserve_limit = round($basketItems['sumprice']*(-1*$reserve_limit/100), -1);	//2015-06-02 ������ ������ %�϶� -�� ������ �κ� %�� ����ؼ� �������� ����

if($_data->rcall_type=="Y") {
	$rcall_type = $_data->rcall_type;
	$bankreserve="Y";
} else if($_data->rcall_type=="N") {
	$rcall_type = $_data->rcall_type;
	$bankreserve="Y";
} else if($_data->rcall_type=="M") {
	$rcall_type="Y";
	$bankreserve="N";
} else {
	$rcall_type="N";
	$bankreserve="N";
}
$etcmessage=explode("=",$admin_message);



$user_reserve=0;
if(strlen($_ShopInfo->getMemid())>0) {
	$sql = "SELECT * FROM tblmember WHERE id='".$_ShopInfo->getMemid()."' ";
	$result = mysql_query($sql);
	if($row = mysql_fetch_object($result)) {
		$user_reserve = $row->reserve;
		if($user_reserve>$reserve_limit) {
			$okreserve=$reserve_limit;
			$remainreserve=$user_reserve-$reserve_limit;
		} else {
			$okreserve=$user_reserve;
			$remainreserve=0;
		}
		$home_addr="";
		/*if(strlen($row->home_post)==6) {
			$home_post1=substr($row->home_post,0,3);
			$home_post2=substr($row->home_post,3,3);
		}*/
		$home_post1=$row->home_post;

		$row->home_addr = ereg_replace("\"","",$row->home_addr);
		$home_addr = explode("=",$row->home_addr);
		$home_addr1 = $home_addr[0];
		$home_addr2 = $home_addr[1];

		$office_addr="";
		/*if(strlen($row->office_post)==6) {
			$office_post1=substr($row->office_post,0,3);
			$office_post2=substr($row->office_post,3,3);
		}*/
		$office_post1=$row->office_post;
		
		$row->office_addr = ereg_replace("\"","",$row->office_addr);
		$office_addr = explode("=",$row->office_addr);
		$office_addr1 = $office_addr[0];
		$office_addr2 = $office_addr[1];

		$name = $row->name;
		$email = $row->email;
		if (strlen($row->mobile)>0) $mobile = $row->mobile;
		else if (strlen($row->home_tel)>0) $home_tel = $row->home_tel;
		else if (strlen($row->office_tel)>0) $office_tel = $row->office_tel;
		$mobile=explode("-",replace_tel(check_num($mobile)));
		$home_tel=explode("-",replace_tel(check_num($row->home_tel)));

		$group_code=$row->group_code;
		@mysql_free_result($result);
		if(strlen($group_code)>0 && $group_code!=NULL) {
			$sql = "SELECT * FROM tblmembergroup WHERE group_code='".$group_code."' AND MID(group_code,1,1)!='M' ";
			$result=mysql_query($sql);
			if($row=mysql_fetch_object($result)){

				//�׷� �̹��� ���ó�� 20131025 J.Bum
				if(file_exists($Dir.DataDir."shopimages/etc/groupimg_".$row->group_code.".gif")) {
					$royal_img="<img src=\"".$Dir.DataDir."shopimages/etc/groupimg_".$row->group_code.".gif\" border=0>";
				} else {
					$royal_img="<img src=\"".$Dir."images/common/group_img.gif\" border=0>\n";
				}

				$group_code = $row->group_code;
				$org_group_name=$row->group_name;  //�׷������� ���� �߰�
				$group_name=$row->group_name;
				$group_type=substr($row->group_code,0,2); // �׷� Ÿ�� 					RW : �ݾ� �߰� ���� / RP  : % �߰� ���� / SW : �ݾ� �߰� ���� / SP  : % �߰� ����
				$group_usemoney=$row->group_usemoney; // �׷����� ���� �ݾ�
				$group_addmoney=$row->group_addmoney; // �׷����αݾ�
				$group_payment=$row->group_payment; // ���� ���					"B"=>"����","C"=>"ī��","N"=>"����/ī��"
					if($group_payment=="B") {
						$group_name.=" (���ݰ�����)";
					} else if($group_payment=="C") {
						$group_name.=" (ī�������)";
					}
			}
			@mysql_free_result($result);
		}
	} else {
		$_ShopInfo->setMemid("");
	}
}




// ��ȸ�� ����
if( strlen($_ShopInfo->getMemid()) == 0 ){
	$sql = "SELECT privercy FROM tbldesign ";
	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		$privercy_exp = @explode("=", $row->privercy);
		$privercybody=$privercy_exp[1];
	}
	@mysql_free_result($result);

	if(strlen($privercybody)==0) {
		$buffer="";
		$fp=fopen($Dir.AdminDir."privercy2.txt","r");
		if($fp) {
			while (!feof($fp)) {
				$buffer.= fgets($fp, 1024);
			}
		}
		fclose($fp);
		$privercybody=$buffer;
	}

	$pattern=array("(\[SHOP\])","(\[NAME\])","(\[EMAIL\])","(\[TEL\])");
	$replace=array($_data->shopname,$_data->privercyname,"<a href=\"mailto:".$_data->privercyemail."\">".$_data->privercyemail."</a>",$_data->info_tel);
	$privercybody = preg_replace($pattern,$replace,$privercybody);
}



$sumprice = $basketItems['sumprice'];


?>
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<script>  var $j = jQuery.noConflict(); </script>
<SCRIPT LANGUAGE="JavaScript">
<!--
var coupon_limit = "<?=$_data->coupon_limit_ok?>";
function change_message(gbn) {
	if(gbn==1) {
		document.all["msg_idx2"].style.display="none";
		document.all["msg_idx1"].style.display="";
		document.form1.msg_type.value=gbn;
	} else if(gbn==2) {
		document.all["msg_idx2"].style.display="";
		document.all["msg_idx1"].style.display="none";
		document.form1.msg_type.value=gbn;
	}
}
function SameCheck() {
	document.form1.receiver_name.value=document.form1.sender_name.value;
	document.form1.receiver_tel11.value=document.form1.sender_tel1.value;
	document.form1.receiver_tel12.value=document.form1.sender_tel2.value;
	document.form1.receiver_tel13.value=document.form1.sender_tel3.value;
	document.form1.receiver_tel21.value=document.form1.sender_hp1.value;
	document.form1.receiver_tel22.value=document.form1.sender_hp2.value;
	document.form1.receiver_tel23.value=document.form1.sender_hp3.value;
	document.form1.rpost1.value="<?=$home_post1?>";
	//document.form1.rpost2.value="<?=$home_post2?>";
	document.form1.raddr1.value="<?=$home_addr1?>";
	document.form1.raddr2.value="<?=$home_addr2?>";
}
<?if(strlen($_ShopInfo->getMemid())>0){?>
/*function addrchoice() {
	if(document.form1.addrtype[0].checked==true) {
		document.form1.rpost1.value="<?=$home_post1?>";
		document.form1.rpost2.value="<?=$home_post2?>";
		document.form1.raddr1.value="<?=$home_addr1?>";
		document.form1.raddr2.value="<?=$home_addr2?>";
	} else if(document.form1.addrtype[1].checked==true) {
		document.form1.rpost1.value="<?=$office_post1?>";
		document.form1.rpost2.value="<?=$office_post2?>";
		document.form1.raddr1.value="<?=$office_addr1?>";
		document.form1.raddr2.value="<?=$office_addr2?>";
	} else if(document.form1.addrtype[2].checked==true) {
		window.open("./addrbygone.php","addrbygone","width=100,height=100,toolbar=no,menubar=no,scrollbars=yes,status=no");
	}
}*/
function reserve_check(temp) {
	temp=parseInt(temp);
	if(isNaN(document.form1.usereserve.value)) {
		document.form1.usereserve.value=0;
		document.form1.okreserve.value=temp;
		document.form1.usereserve.focus();
		alert('���ڸ� �Է��ϼž� �մϴ�.');
		return;
	}
	if(parseInt(document.form1.usereserve.value)>temp) {
		document.form1.usereserve.value=0;
		document.form1.okreserve.value=temp;
		document.form1.usereserve.focus();
		alert('��밡�� ������ ���� ���ų� �Ȱ��� �Է��ϼž� �մϴ�.');
		return;
	}
	document.form1.okreserve.value=parseInt(temp-document.form1.usereserve.value);
	document.form1.usereserve.value=temp-document.form1.okreserve.value;
}
<?}?>
function get_post() {
	window.open("./addr_search.php?form=form1&post=rpost&addr=raddr1&gbn=2","f_post","resizable=yes,scrollbars=yes,x=100,y=200,width=370,height=250");		
}

// �����ٿ�ε�
function issue_coupon(coupon_code,productcode){
	location.href="?mode=coupon&coupon_code="+coupon_code+"&productcode="+productcode;
}


// �������� ( offlinecoupon : ��������������� )
function coupon_check( offlinecoupon ){
	resetCoupon();

	var offlinecouponURL = "";
	if( offlinecoupon == "offlinecoupon" ) {
		offlinecouponURL = "?offlinecoupon=popup";
	}
	window.open("/m/couponpop.php"+offlinecouponURL,"couponpopup","width=720,height=750,toolbar=no,menubar=no,scrollbars=yes,status=no");
}
/*<?if(strlen($_ShopInfo->getMemid())>0 && $_data->coupon_ok=="Y"){?>
	var isreserveinit=false;
function coupon_cancel() {
	if(document.form1.coupon_code.value.length>0) {
		if(confirm("���õ� ������ ����Ͻðڽ��ϱ�?")==true) {
			document.form1.coupon_code.value="";
		}
	}
	if(isreserveinit==true) {
		if(typeof(document.form1.usereserve)=="object") {
			document.form1.usereserve.readOnly=false;
		}
	}
}
function issue_coupon(coupon_code,productcode){
	location.href="?mode=coupon&coupon_code="+coupon_code+"&productcode="+productcode;
}

function coupon_check( offlinecoupon ){
	resetCoupon();

	var offlinecouponURL = "";
	if( offlinecoupon == "offlinecoupon" ) {
		offlinecouponURL = "?offlinecoupon=popup";
	}
	window.open("/m/couponpop.php"+offlinecouponURL,"couponpopup","toolbar=no,menubar=no,scrollbars=yes,status=no");
}

function coupon_default(){
	resetCoupon();
}
<?}?>*/
function number_format(input){
	var input = String(input);
    var reg = /(\-?\d+)(\d{3})($|\.\d+)/;

    if(reg.test(input)){
        return input.replace(reg, function(str, p1,p2,p3){
                return number_format(p1) + "," + p2 + "" + p3;
            }
        );
    }else{
        return input;
    }
}

/*function ordercancel(gbn) {
	if(gbn=="cancel" && document.form1.process.value=="N") {
		document.location.href="basket.php";
	} else {
		if (PROCESS_IFRAME.chargepop) {
			if (gbn=="cancel") alert("����â�� �������Դϴ�. ����Ͻ÷��� ����â���� ����ϱ⸦ ��������.");
		} else {
			PROCESS_IFRAME.PaymentOpen();
		}
	}
}*/

/*function ProcessWait(display) {
	var PAYWAIT_IFRAME = document.all.PAYWAIT_IFRAME;

	document.paywait.src = "<?=$Dir?>images/paywait.gif";
	var _x = document.body.clientWidth/2 + document.body.scrollLeft - 250;
	var _y = document.body.clientHeight/2 + document.body.scrollTop - 120;

	PAYWAIT_IFRAME.style.visibility=display;
	PAYWAIT_IFRAME.style.posLeft=_x;
	PAYWAIT_IFRAME.style.posTop=_y;

	PAYWAIT_LAYER.style.posLeft=_x;
	PAYWAIT_LAYER.style.posTop=_y;
	PAYWAIT_LAYER.style.visibility=display;
}

function ProcessWaitPayment() {
	var PAYWAIT_IFRAME = document.all.PAYWAIT_IFRAME;

	document.paywait.src = "<?=$Dir?>images/paywait2.gif";
	var _x = document.body.clientWidth/2 + document.body.scrollLeft - 250;
	var _y = document.body.clientHeight/2 + document.body.scrollTop - 120;

	PAYWAIT_IFRAME.style.visibility='visible';
	PAYWAIT_IFRAME.style.posLeft=_x;
	PAYWAIT_IFRAME.style.posTop=_y;

	PAYWAIT_LAYER.style.visibility='visible';
	PAYWAIT_LAYER.style.posLeft=_x;
	PAYWAIT_LAYER.style.posTop=_y;
}

function PaymentOpen() {
	PROCESS_IFRAME.PaymentOpen();
	ProcessWait('visible');
}*/

//-->
</SCRIPT>
<?
$mingiftprice = 0;
if(false !== $gres = mysql_query("select min(gift_startprice) from tblgiftinfo",get_db_conn())){
	if(mysql_num_rows($gres)) $mingiftprice = mysql_result($gres,0,0);
}
?>

<script>
	var deli_basefee	= parseInt(<?=$_data->deli_basefee?>); //���θ� ���� ��۷�
	var deli_miniprice	= parseInt(<?=$_data->deli_miniprice?>); //���θ� ���� ��۹��� �ּ� ��ǰ��
	var deli_price = parseInt(<?=$basketItems['deli_price']?>);
	var excp_group_discount = parseInt(<?=$basketItems['excp_group_discount']?>);
	var mingiftprice = parseInt(<?=$giftprice?>);

	var setprice;

	//������� ����
	var groupDiscMoney = parseInt("<?=$basketItems['groupMemberSale']['addMoney']?>"); // ����/���αݾ� �Ǵ� %
	var groupDiscUseMoney = parseInt("<?=$basketItems['groupMemberSale']['useMoney']?>"); // ���� �ݾ�
	var groupDiscPayTypeCode = "<?=$basketItems['groupMemberSale']['payTypeCode']?>"; // ���� ���� ���
	var groupCode = "<?=$basketItems['groupMemberSale']['groupCode']?>"; // �׷��ڵ�

	if(isNaN(mingiftprice) || mingiftprice <1) mingiftprice = 0;

	$j(document).ready(function() {

		// ������
		$j("#usereserve").keyup(function(){
			var possibleMileage = parseInt($j("#okreserve").val());//�ش� �ֹ����� ��밡���� ������
			var defaultprice	= parseInt($j("#sumprice").val());	//�⺻ �� �����ݾ�

			repstr = $j(this).val().replace(/[^0-9]/g,'');
			userMileage = parseInt(repstr);
			if(isNaN(userMileage)) userMileage = 0;
			$j(this).val(userMileage.toString());

			if(userMileage > possibleMileage){
				alert("�ش� �ֹ��� ������ ���� ������ �ݾ��� "+possibleMileage + "�� �Դϴ�.");
				$j("#usereserve").val(possibleMileage.toString());
			}else{

			}
			//resetCoupon();

			solvPrice();
		});

		$j("input[name=saddr2],input[name=raddr2]").focus(function(){
			if($j(this).val() == '������ �ּ�') $j(this).val('');
		});

		$j("input[name=saddr2],input[name=raddr2]").blur(function(){
			if($j.trim($j(this).val()).length < 1) $j(this).val('������ �ּ�');
		});

		solvPrice();
	});

	function reserdeli(total_price){
		if(total_price > 0 && deli_miniprice > total_price) {
			alert("���� �����ݾ��� " + number_format(deli_miniprice) + " �� ������ ��� �⺻ ��۷� " +number_format(deli_basefee)+ "���� �߰��˴ϴ�");
			$j("#disp_last_price").text(number_format(total_price+deli_basefee));	// ���������ݾ� UI ǥ��
		}
	}

	function resetCoupon(){
		var coupon = parseInt($j("#coupon_price").val()); //���� ���ξ�
		if(!isNaN(coupon) && coupon > 0){
			alert('���� ��� ������ �ʱ�ȭ �˴ϴ�.');
		}
		$j('#couponlist').val('');
		$j('#dcpricelist').val('');
		$j('#couponproduct').val('');
		$j('#coupon_price').val('0');
		$j('#bank_only').val('N');
		$j("#possible_gift_price_used").val("Y");
		$j("#possible_group_dis_used").val("Y");

		solvPrice();
	}

	function change_paymethod(val){
		solvPrice();
	}


	// �� ���� ******************************************************************************************
	function solvPrice(){

		var possibleMileage = parseInt($j("#okreserve").val());//�ش� �ֹ����� ��밡���� ������
		var userMileage = parseInt($j("#usereserve").val()); // ����� ������
		var gift = parseInt($j("#possible_gift_price").val()); // ����ǰ ���ް��� ���űݾ�
		var coupon = parseInt($j("#coupon_price").val()); //���� ����� ��
		var defaultprice = parseInt($j("#sumprice").val()); //�� �����ݾ�
		var deli_price = parseInt($j("#deliprice").val()); // ��ۺ�

		if(isNaN(possibleMileage)) possibleMileage = 0;
		if(isNaN(userMileage)) userMileage = 0;
		if(isNaN(gift)) gift = 0;
		if(isNaN(coupon)) coupon = 0;
		if(isNaN(defaultprice)) defaultprice = 0;
		if(isNaN(deli_price)) deli_price = 0;
		var gdiscount = 0;

		setprice = parseInt(defaultprice-userMileage-coupon); // ���� �ݾ�

		// ������ ���
		if( setprice < 0 ) {
			userMileage = parseInt( userMileage - ( 0 - setprice ) );
			alert("�����ݻ���� "+userMileage+"���� ��밡���մϴ�.\n\n* ���� ��� �� ������å�� ���Ͽ� ���� �Ǵ� ���� ������ ���Ѱ��Դϴ�.");
			setprice = 0;
		}
		$j("#usereserve").val(userMileage);


		//��� ����
		var gdiscount = 0;
		var ispaymentcheck=false;
		for(i=0;i<document.form1.sel_paymethod.length;i++) {
			if(document.form1.sel_paymethod[i].checked==true) {
				document.form1.paymethod.value=document.form1.sel_paymethod[i].value;
				ispaymentcheck=true;
				break;
			}
		}
		if( isNaN(groupCode) && ispaymentcheck==true && $j("#possible_group_dis_used").val() == "Y" && setprice >= groupDiscMoney && setprice >= groupDiscUseMoney ) {
			if ( groupCode == 'SW' ) {
				gdiscount=groupDiscMoney;
			} else {
				gdiscount= Math.floor((setprice*(groupDiscMoney/100))/100)*100;
			}
			// ���� ��Ŀ� ���� ó��
			// "B"=>"����","C"=>"ī��","N"=>"����/ī��"
			if( groupDiscPayTypeCode != "N" ) {
				var paymethodList = ( groupDiscPayTypeCode == "B" ) ? "B|V|O" : "C|M";
				var paymethod = $j("#paymethod").val();
				if( paymethodList.indexOf(paymethod) < 0 ) {
					gdiscount = 0;
				}
			}
		}

		// ������� ���� �ȵ� ���� ��� �޼���
		if ($j("#possible_group_dis_used").val() == "N") {
			$j("#couponEventMsg").html("<br><font color='blue'>����Ͻ� ���� �� ������� ������ ���� �� ���� ������ ���ԵǾ����ϴ�.</font>");
		} else {
			$j("#couponEventMsg").html("");
		}

		setprice -= gdiscount;

		gdiscount = 0-gdiscount;
		$j("#groupdiscount").val(gdiscount);

		// ����ǰ ����
		if(setprice < gift) gift = setprice; // ����ǰ ��밡�� �ݾ�
		giftchoices(gift);

		// �Ѱ����ݾ�
		var total_price =  parseInt( setprice + deli_price );

		// ���÷��� ( UI ǥ�� )
		$j("#disp_coupon").text(number_format(0-coupon)); // �������� ���ݾ�
		$j("#disp_reserve").text(number_format(0-userMileage)); // ������ ����
		$j("#disp_groupdiscount").text(number_format(gdiscount));	// �������
		$j("#disp_deliprice").text(number_format(deli_price));	// ��۱ݾ�
		$j("#disp_last_price").text(number_format(total_price));	// ���������ݾ�

	}
	// �� ���� �� **************************************************************************************
</script>
<?
#������ ��ǰ�� �Ϲ� ��ǰ�� �ֹ��� ���
if($basketItems['productcnt']!=$basketItems['productcnt'] && $basketItems['productcnt']>0 && $_data->card_splittype=="O") {
	echo "<script> alert('[�ȳ�] �����������ǰ�� �Ϲݻ�ǰ�� ���� �ֹ��� �������Һ������� �ȵ˴ϴ�.');</script>";
}

if($basketItems['sumprice']<$_data->bank_miniprice) {
	echo "<script>alert('�ֹ� ������ �ּ� �ݾ��� ".number_format($_data->bank_miniprice)."�� �Դϴ�.');location.href='./basket.php';</script>";
	exit;
} else if($basketItems['sumprice']<=0) {
	echo "
		<script>
			alert('��ǰ �� ������ 0���� ��� ��ǰ �ֹ��� ���� �ʽ��ϴ�.');
			location.href='./basket.php';
		</script>
	";
	exit;
}

?>

<form name=form1 action="ordersend.php" method=post>
<input type="hidden" name="sumprice" id="sumprice" value="<?=$basketItems['sumprice']?>" />

<!-- �������� �� ( ���б�ȣ : | )) -->
<!-- �����������Ʈ -->
<input type="hidden" name="couponlist" id="couponlist" value="" />
<!-- ������� ���ξ� ����Ʈ -->
<input type="hidden" name="dcpricelist" id="dcpricelist" value="" />
<!-- ������� ������ ����Ʈ -->
<input type="hidden" name="drpricelist" id="drpricelist" value="" /><!-- -->
<!-- ���������ǰ����Ʈ --><!--  (�����ڵ�_��ǰ�ڵ�_�ɼ�1idx_�ɼ�2idx) -->
<input type="hidden" name="couponproduct" id="couponproduct" value="" />
<!-- ���� ���� ������ ������ ���õ� ��� --><!-- if (���� ���� ������ ������ ���õ� ��� ) Y else N -->
<input type="hidden" name="bank_only" id="couponBankOnly" value="N" />
<!-- ��ۺ� -->
<input type='hidden' name='deliprice' id='deliprice' value='<?=$basketItems['deli_price']?>'>
<!-- ���������Ѿ� -->
<input type="hidden" name="coupon_reserve" id="coupon_reserve" value="0" />
<!-- ������� -->
<input type="hidden" name="paymethod" id="paymethod" value="0" />
<!-- ������ ���� �Ұ� ��ǰ ������ ���밡���� ������ �ݾ� , ��� �������� okreserve ���� �۾ƾ� �� -->
<input type="hidden" name="okreserve" id="okreserve" value="<?=$okreserve?>" />
<!-- ���� Ÿ��(?) �����ϱ� �ϰ�� (?) -->
<input type=hidden name=ordertype value="<?=$ordertype?>" />
<!-- �鼼? �̰� ��� Ȱ��?? -->
<input type="hidden" name="tax_free" value="<?=$basketItems['tax_free']?>" />
<!-- ����ǰ ��밡�� �ݾ� -->
<input type="hidden" name="possible_gift_price" id="possible_gift_price" value="<?=$basketItems['gift_price']?>" />
<!-- ����ǰ ��밡�� ���� (Y/N) -->
<input type="hidden" name="possible_gift_price_used" id="possible_gift_price_used" value="Y" />
<!-- ȸ�� ��� ���� ���� ���� (Y/N) -->
<input type="hidden" name="possible_group_dis_used" id="possible_group_dis_used" value="Y" />

<!-- �ֹ��޼��� Ÿ�� -->
<input type="hidden" name="msg_type" value="1" />
<!-- ������ �߰� ��۷�..???? -->
<input type="hidden" name="addorder_msg" value="" />

<!-- ���� ���� ���� -->
<input type="hidden" name="basketTempList" id="basketTempList" value="" />

<!-- ȸ���׷�(�߰�)���� -->
<input type="hidden" name="groupdiscount" id="groupdiscount" value="0" />


<input type="hidden" name="process" value="N" />
<!-- <input type=hidden name=paymethod> --><!-- ���������������������ϱ����� �ּ�ó�� -->
<!-- <input type=hidden name=pay_data1> --><!-- ����������â���������ϱ����� �ּ�ó�� �˾����� opener�� �ѱ�� ���̾��� -->
<input type="hidden" name="pay_data2" />
<input type="hidden" name="sender_resno" />
<input type="hidden" name="sender_tel" />
<input type="hidden" name="receiver_tel1" />
<input type="hidden" name="receiver_tel2" />
<input type="hidden" name="receiver_addr" />
<input type="hidden" name="order_msg" />
<!-- <input type=hidden name=gift_price value="<?//=$basketItems['gift_price']?>"> -->
<?
	if($_data->ssl_type=="Y" && strlen($_data->ssl_domain)>0 && strlen($_data->ssl_port)>0 && $_data->ssl_pagelist["ORDER"]=="Y") {
?>
	<input type="hidden" name="shopurl" value="<?=getenv("HTTP_HOST")?>" />
<?
	}
?>
<? include $skinPATH."order.php"; ?>
</form>


<!-- <form name="couponform" action="<?=$Dir.FrontDir?>couponpop_new.php" method=post target=couponpopup>
<input type="hidden" name="sumprice" value="<?=$basketItems['sumprice']?>">
<input type="hidden" name="giftprice" value="<?=$basketItems['gift_price']?>">
<input type="hidden" name="usereserve" value="0">
<input type="hidden" name="total_sumprice" id="ctotal_sumprice" value="" />
</form> -->

<!-- <form name=couponissueform method=get action="<?=$_SERVER[PHP_SELF]?>">
<input type=hidden name=mode value="">
<input type=hidden name=coupon_code value="">
<input type=hidden name=productcode value="">
</form> -->

<!-- <form name=couponform2 action="<?=$Dir.FrontDir?>coupon.php" method=post target=couponpopup>
<input type=hidden name=sumprice value="<?=$basketItems['sumprice']?>">
<input type="hidden" name="giftprice" value="<?=$basketItems['gift_price']?>">
<input type="hidden" name="usereserve">
<input type="hidden" name="total_sumprice" id="ctotal_sumprice" value="" />
</form> -->

<!-- <form name=orderpayform method=post action="<?=$Dir.FrontDir?>orderpay.php" target=orderpaypop>
<?if($_data->ssl_type=="Y" && strlen($_data->ssl_domain)>0 && strlen($_data->ssl_port)>0 && $_data->ssl_pagelist["ORDER"]=="Y") {?>
<input type=hidden name=shopurl value="<?=getenv("HTTP_HOST")?>">
<?}?>
<input type=hidden name=coupon_code>
<input type=hidden name=couponlist>
<input type=hidden name=coupon_price>
<input type=hidden name=couponproduct>
<input type=hidden name=usereserve>
<input type=hidden name=email>
<input type=hidden name=mobile_num1>
<input type=hidden name=mobile_num>
<input type=hidden name=address>
</form> -->

<!-- <form name="reserve_check_form">
<input type="hidden" name="possible_total_price" id="possible_total_price" value="<?=$basketItems['sumprice']//$sumprice+$sumpricevat-$salemoney?>" />

<input type="hidden" name="possible_reserve_price" id="possible_reserve_price" value="<?=$okreserve?>" />

<input type="hidden" name="possible_gift_price" id="possible_gift_price" value="<?=$basketItems['gift_price']?>" />
<input type="hidden" name="possible_gift_price_used" id="possible_gift_price_used" value="Y" />
</form> -->
<SCRIPT LANGUAGE="JavaScript">
<!--
function CheckForm() {
	if(document.form1.sender_name.type=="text") {
		if(document.form1.sender_name.value.length==0) {
			alert("�ֹ��� ������ �Է��ϼ���.");
			document.form1.sender_name.focus();
			return;
		}
		if(!chkNoChar(document.form1.sender_name.value)) {
			alert("�ֹ��� ���Կ� \\(��������) ,  '(��������ǥ) , \"(ū����ǥ)�� �Է��Ͻ� �� �����ϴ�.");
			document.form1.sender_name.focus();
			return;
		}
	}

	if(document.form1.sender_tel1.value.length==0) {
		alert("�ֹ��� ��ȭ��ȣ�� �Է��ϼ���.");
		document.form1.sender_tel1.focus();
		return;
	}
	if(document.form1.sender_tel2.value.length==0) {
		alert("�ֹ��� ��ȭ��ȣ�� �Է��ϼ���.");
		document.form1.sender_tel2.focus();
		return;
	}
	if(document.form1.sender_tel3.value.length==0) {
		alert("�ֹ��� ��ȭ��ȣ�� �Է��ϼ���.");
		document.form1.sender_tel3.focus();
		return;
	}
	if(!IsNumeric(document.form1.sender_tel1.value)) {
		alert("�ֹ��� ��ȭ��ȣ �Է��� ���ڸ� �Է��ϼ���.");
		document.form1.sender_tel1.focus();
		return;
	}
	if(!IsNumeric(document.form1.sender_tel2.value)) {
		alert("�ֹ��� ��ȭ��ȣ �Է��� ���ڸ� �Է��ϼ���.");
		document.form2.sender_tel2.focus();
		return;
	}
	if(!IsNumeric(document.form1.sender_tel3.value)) {
		alert("�ֹ��� ��ȭ��ȣ �Է��� ���ڸ� �Է��ϼ���.");
		document.form3.sender_tel3.focus();
		return;
	}
	document.form1.sender_tel.value=document.form1.sender_tel1.value+"-"+document.form1.sender_tel2.value+"-"+document.form1.sender_tel3.value;

	if(document.form1.sender_email.value.length>0) {
		if(!IsMailCheck(document.form1.sender_email.value)) {
			alert("�ֹ��� �̸��� ������ �߸��Ǿ����ϴ�.");
			document.form1.sender_email.focus();
			return;
		}
	}

	if(document.form1.receiver_name.value.length==0) {
		alert("�޴º� ������ �Է��ϼ���.");
		document.form1.receiver_name.focus();
		return;
	}
	if(!chkNoChar(document.form1.receiver_name.value)) {
		alert("�޴º� ���Կ� \\(��������) ,  '(��������ǥ) , \"(ū����ǥ)�� �Է��Ͻ� �� �����ϴ�.");
		document.form1.receiver_name.focus();
		return;
	}
	if(document.form1.receiver_tel11.value.length==0) {
		alert("�޴º� ��ȭ��ȣ�� �Է��ϼ���.");
		document.form1.receiver_tel11.focus();
		return;
	}
	if(document.form1.receiver_tel12.value.length==0) {
		alert("�޴º� ��ȭ��ȣ�� �Է��ϼ���.");
		document.form1.receiver_tel12.focus();
		return;
	}
	if(document.form1.receiver_tel13.value.length==0) {
		alert("�޴º� ��ȭ��ȣ�� �Է��ϼ���.");
		document.form1.receiver_tel13.focus();
		return;
	}
	if(!IsNumeric(document.form1.receiver_tel11.value)) {
		alert("�޴º� ��ȭ��ȣ �Է��� ���ڸ� �Է��ϼ���.");
		document.form1.receiver_tel11.focus();
		return;
	}
	if(!IsNumeric(document.form1.receiver_tel12.value)) {
		alert("�޴º� ��ȭ��ȣ �Է��� ���ڸ� �Է��ϼ���.");
		document.form1.receiver_tel12.focus();
		return;
	}
	if(!IsNumeric(document.form1.receiver_tel13.value)) {
		alert("�޴º� ��ȭ��ȣ �Է��� ���ڸ� �Է��ϼ���.");
		document.form1.receiver_tel13.focus();
		return;
	}
	document.form1.receiver_tel1.value=document.form1.receiver_tel11.value+"-"+document.form1.receiver_tel12.value+"-"+document.form1.receiver_tel13.value;

	if(document.form1.receiver_tel21.value.length==0) {
		alert("�޴º� �����ȭ��ȣ�� �Է��ϼ���.");
		document.form1.receiver_tel21.focus();
		return;
	}
	if(document.form1.receiver_tel22.value.length==0) {
		alert("�޴º� �����ȭ��ȣ�� �Է��ϼ���.");
		document.form1.receiver_tel22.focus();
		return;
	}
	if(document.form1.receiver_tel23.value.length==0) {
		alert("�޴º� �����ȭ��ȣ�� �Է��ϼ���.");
		document.form1.receiver_tel23.focus();
		return;
	}
	if(!IsNumeric(document.form1.receiver_tel21.value)) {
		alert("�޴º� �����ȭ��ȣ �Է��� ���ڸ� �Է��ϼ���.");
		document.form1.receiver_tel21.focus();
		return;
	}
	if(!IsNumeric(document.form1.receiver_tel22.value)) {
		alert("�޴º� �����ȭ��ȣ �Է��� ���ڸ� �Է��ϼ���.");
		document.form1.receiver_tel22.focus();
		return;
	}
	if(!IsNumeric(document.form1.receiver_tel23.value)) {
		alert("�޴º� �����ȭ��ȣ �Է��� ���ڸ� �Է��ϼ���.");
		document.form1.receiver_tel23.focus();
		return;
	}
	document.form1.receiver_tel2.value=document.form1.receiver_tel21.value+"-"+document.form1.receiver_tel22.value+"-"+document.form1.receiver_tel23.value;

	/*if(document.form1.rpost1.value.length==0 || document.form1.rpost2.value.length==0) {
		alert("�����ȣ�� �����ϼ���.");
		get_post('r');
		return;
	}*/
	if(document.form1.rpost1.value.length <=0 || document.form1.rpost1.value.length>=6) {
		alert("�����ȣ �Է��� �ǹٸ��� �ʽ��ϴ�.");
		get_post('r');
		return;
	}
	if(document.form1.raddr1.value.length==0) {
		alert("�ּҸ� �Է��ϼ���.");
		document.form1.raddr1.focus();
		return;
	}
	if(document.form1.raddr2.value.length==0) {
		alert("���ּҸ� �Է��ϼ���.");
		document.form1.raddr2.focus();
		return;
	}
	if(!chkNoChar(document.form1.raddr2.value)) {
		alert("���ּҿ� \\(��������) ,  '(��������ǥ) , \"(ū����ǥ)�� �Է��Ͻ� �� �����ϴ�.");
		document.form1.raddr2.focus();
		return;
	}
	<? if(strlen($_ShopInfo->getMemid())==0) { ?>
	if(document.form1.dongi[0].checked!=true) {
		alert("����������ȣ��å�� �����ϼž� ��ȸ�� �ֹ��� �����մϴ�.");
		document.form1.dongi[0].focus();
		return;
	}
	<?}?>
	<? if(strlen($_ShopInfo->getMemid())>0) { ?>
		<? if($_data->reserve_maxuse>=0 && strlen($okreserve)>0 && $okreserve>0) { ?>
		if(document.form1.usereserve.value > <?=$okreserve?>) {
			alert("������ ��밡�ɱݾ׺��� Ů�ϴ�.");
			document.form1.usereserve.focus();
			return;
		} else if(document.form1.usereserve.value < 0) {
			alert("�������� 0������ ũ�� ����ϼž� �մϴ�.");
			document.form1.usereserve.focus();
			return;
		}
		<? } ?>
		
		<? if($_data->reserve_maxuse>=0 && strlen($okreserve)>0 && $okreserve>0 && $_data->coupon_ok=="Y" && $rcall_type=="N") { ?>
		//if(document.form1.usereserve.value>0 && document.form1.coupon_code.value.length==8){
			if(document.form1.usereserve.value>0 && document.form1.couponlist.value.length>8){
			alert('�����ݰ� ������ ���ÿ� ����� �Ұ����մϴ�.\n���߿� �ϳ��� ����Ͻñ� �ٶ��ϴ�.');
			document.form1.usereserve.focus();
			return;
		}
		<? } ?>

		<? if($_data->reserve_maxuse>=0 && $bankreserve=="N") { ?>
		if (document.form1.usereserve.value>0) {
			if(paymethod!="B" && paymethod!="V" && paymethod!="O" && paymethod!="Q") {
				alert('�������� ���ݰ����ÿ��� ����� �����մϴ�.\n���ݰ����� ������ �ּ���');
				document.form1.paymethod.value="";
				return;
			}
		}
		<? } ?>
	<? } ?>

	//sks �߰� 	

	//������� ���ÿ��� ������, �ſ�ī��...
	var is_paymethod;
	var selected_paymethod;

	if(!document.form1.sel_paymethod.length) {//���� �ڽ��� 1�����
		if(document.form1.sel_paymethod.checked != true){
			alert("��������� ���� ���� �ʾҽ��ϴ�.");
			return;
		}else{
			document.form1.paymethod.value = document.form1.sel_paymethod.value;
			is_paymethod = true;
		}
		document.form1.paymethod.checked = true;
		//selected_paymethod = document.form1.paymethod.value;
		//is_paymethod = true;

	}else{//1�� �̻�
		var is_paymethod = false;
		var selected_paymethod = "";
		for (i=0;i<document.form1.sel_paymethod.length;i++) {//��������� �����ߴ��� ����
			if(document.form1.sel_paymethod[i].checked==true){	
				is_paymethod = true;	
				selected_paymethod = document.form1.sel_paymethod[i].value;	
			}
		}
		if(is_paymethod==false) {	
			alert("��������� �����ϼ���");	return;
		}		
	}

	//������ �Ա��� �����ߴٸ�
	var is_pay_data1;
	var selected_pay_data1;
	if(selected_paymethod=="B"){
		var _account = document.getElementById("pay_data1");
		var option_value = _account.options[_account.selectedIndex].value;
		if(option_value == 'dont'){
			alert("�Աݰ��¸� �����ϼ���");
			//_account.focus();
			return;
		}
	}
	
//	document.form1.receiver_addr.value = "�����ȣ : " + document.form1.rpost1.value + "-" + document.form1.rpost2.value + "\n�ּ� : " + document.form1.raddr1.value + "  " + document.form1.raddr2.value;
	document.form1.receiver_addr.value = "�����ȣ : " + document.form1.rpost1.value + "\n�ּ� : " + document.form1.raddr1.value + "  " + document.form1.raddr2.value;

	<? if($_data->coupon_ok=="Y" && strlen($_ShopInfo->getMemid())>0) { ?>
		if (document.form1.bank_only.value=="Y") {
			if(paymethod!="B" && paymethod!="V" && paymethod!="O" && paymethod!="Q") {
				alert("�����Ͻ� ������ ���ݰ����� �����մϴ�.\n���ݰ����� ������ �ּ���");
				document.form1.paymethod.value="";
				return;
			}
		}
	<? } ?>
		document.form1.order_msg.value="";
		if(document.form1.process.value=="N") {
		<? if(strlen($etcmessage[1])>0) {?>
			if(document.form1.nowdelivery.checked==true) {
				document.form1.order_msg.value+="<font color=red>�������� : ������ �������</font>";
			} else {
				document.form1.order_msg.value+="<font color=red>�������� : "+document.form1.year.value+"�� "+document.form1.mon.value+"�� "+document.form1.day.value+"��";
				<? if(strlen($etcmessage[1])==6) { ?>
				document.form1.order_msg.value+=" "+document.form1.time.value;
				<? } ?>
				document.form1.order_msg.value+="</font>";
			}
		<? } ?>
			/*

		<? if($etcmessage[2]=="Y") { ?>
			if(document.form1.bankname.value.length>1 && (document.form1.paymethod.length==null && paymethod=="B")) {
				if(document.form1.order_msg.value.length>0) document.form1.order_msg.value+="\n";
				document.form1.order_msg.value+="�Ա��� : "+document.form1.bankname.value;
			}
		<? } ?>
	*/
			//������ �߰���۷� Ȯ��
		<?
	/*
			echo "address = \" \"+document.form1.raddr1.value;\n";
			$array_deli = explode("|",$_data->deli_area);
			$cnt= floor(count($array_deli)/2);
			for($i=0;$i<$cnt;$i++){
				$subdeli=explode(",",$array_deli[$i*2]);
				$subcnt=count($subdeli);
				echo "if(";
				for($j=0;$j<$subcnt;$j++){
					if($j!=0) echo " || ";
					echo "address.indexOf(\"".$subdeli[$j]."\")>0";
				}
				echo "){ if(!confirm('";
				if($array_deli[$i*2+1]>0) echo "�ش� ������ ��۷� ".number_format($array_deli[$i*2+1])."���� �߰��˴ϴ�.";
				else echo "�ش� ������ ��۷� ".number_format(abs($array_deli[$i*2+1]))."���� ���ε˴ϴ�.";
				echo "')) return;}\n";
			}
	*/
		?>
		if(document.form1.addorder_msg=="[object]") {
			if(document.form1.order_msg.value.length>0) document.form1.order_msg.value+="\n";
			document.form1.order_msg.value+=document.form1.addorder_msg.value;
		}
		//document.form1.process.value="Y";
		// document.form1.target = "PROCESS_IFRAME"; //sks

<?if($_data->ssl_type=="Y" && strlen($_data->ssl_domain)>0 && strlen($_data->ssl_port)>0 && $_data->ssl_pagelist["ORDER"]=="Y") {?>
		//document.form1.action='https://<?=$_data->ssl_domain?><?=($_data->ssl_port!="443"?":".$_data->ssl_port:"")?>/<?=RootPath.SecureDir?>order.php';
<?}?>

	document.form1.submit();
		//document.all.paybuttonlayer.style.display="none";
		//document.all.payinglayer.style.display="block";

		//if(paymethod!="B") ProcessWait("visible");

	} else {
		ordercancel();
	}
}

//-->
</SCRIPT>

<script>
function showBankAccount(str)
{
	if(str=="show")
	{
		document.getElementById('pay_account_list').style.display = '';
	}
	else
	{
		document.getElementById('pay_account_list').style.display = 'none';
	}

}
</script>
<? include "footer.php"; ?>

