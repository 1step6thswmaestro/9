<?php
$Dir="../";
include_once($Dir."lib/nusoap.php");
include_once($Dir."lib/XML.class.php");
include_once($Dir."lib/Hiworks_Bill.class.php");


/* **************************************** */
/* define 정의                                */
/* **************************************** */
define( 'HB_DOCUMENTTYPE_TAX' , 'A' );    // 세금계산서
define( 'HB_DOCUMENTTYPE_BILL' , 'B' );   // 계산서
define( 'HB_DOCUMENTTYPE_DETAIL' , 'D' ); // 거래명세서

define( 'HB_TAXTYPE_TAX', 'A' );		// 과세
define( 'HB_TAXTYPE_NOTAX', 'B' );	// 영세
define( 'HB_TAXTYPE_MANUAL', 'D' );	// 수동

define( 'HB_SENDTYPE_SEND', 'S' );	// 매출
define( 'HB_SENDTYPE_RECV', 'R' );	// 매입

define( 'HB_PTYPE_RECEIPT', 'R' );	// 영수
define( 'HB_PTYPE_CALL', 'C' );		// 청구

define( 'HB_COMPANYPREFIX_SUPPLIER', 's' );	// 매출처 접두어
define( 'HB_COMPANYPREFIX_CONSUMER', 'r' );	// 매입처 접두어

define( 'HB_SOAPSERVER_URL', 'http://billapi.hiworks.co.kr/server.php?wsdl' );	// SOAP Server URL

/* **************************************** */
/* 타입 정의                                */
/* **************************************** */
$document_status = array();
$document_status['W'] = '승인요청전';
$document_status['T'] = '승인요청';
$document_status['R'] = '승인요청';
$document_status['S'] = '승인';
$document_status['B'] = '반려';
$document_status['C'] = '승인취소요청';
$document_status['A'] = '승인최소완료';
$document_status['E'] = '에러';

define( 'TAXRATE' , '10' );    // 세율

class Shop_Billinfo
{
	var $license_no = "";
	var $license_id = "";
	var $domain = "";
	var $partner_id = "";

	var $r_name = "";
	var $r_number = "";
	var $r_tnumber = "";
	var $r_master = "";
	var $r_address = "";
	var $r_condition = "";
	var $r_item = "";
	var $c_name ="";
	var $c_email ="";
	var $c_cell ="";

	var $s_name = "";
	var $s_number = "";
	var $s_tnumber = "";
	var $s_master = "";
	var $s_address = "";
	var $s_condition = "";
	var $s_item = "";

	var $taxrate = TAXRATE;

	var $supplyprice = "";		// supplyprice : 공급가액
	var $tax = "";				// tax : 세금

	var $guest_name ="";
	var $reserve = "";
	var $deli_price = "";
	var $dc_price = "";
	var $count = 0;
	var $productname = null;
	var $quantity = null;
	var $taxsum = null;
	var $taxsumquantity = null;
	var $taxsumsale = null;


	function Shop_Billinfo()
	{
		$sql = "SELECT * FROM tblshopbillinfo WHERE bill_state ='Y' limit 1";
		$result=@mysql_query($sql,get_db_conn());
		if($row=@mysql_fetch_object($result)) {
			$this->license_no = $row->license_no;
			$this->license_id = $row->license_id;
			$this->domain = $row->domain;
			$this->partner_id = $row->partner_id;
		}else{
			error_msg("전자세금계산서 발행 정보가 없습니다.","");
		}
	}

	function baseinfo($mem_id)
	{
		$sql = "SELECT * FROM tblmemcompany WHERE memid ='".$mem_id."' ";
		$result = mysql_query($sql,get_db_conn());
		if ($row=mysql_fetch_object($result)) {
			$this->r_name = $row->companyname;
			$this->r_number = $row->companynum;
			$this->r_tnumber = $row->companytnum;
			$this->r_master = $row->companyowner;
			$this->r_address = str_replace("||","",$row->companyaddr);
			$this->r_condition = $row->companybiz;
			$this->r_item = $row->companyitem;

			$this->c_name = $row->c_name;
			$this->c_email = $row->c_email;
			$this->c_cell = $row->c_cell;
		}else{
			error_msg("공급받는자 정보가 존재하지 않습니다.","");
		}
		mysql_free_result($result);

		$sql = "SELECT * FROM tblshopinfo ";
		$result = mysql_query($sql,get_db_conn());
		if ($row=mysql_fetch_object($result)) {
			$this->s_name = $row->companyname;
			$this->s_number = $row->companynum;
			$this->s_master = $row->companyowner;
			$this->s_address = str_replace("||","",$row->companyaddr);
			$this->s_condition = $row->companybiz;
			$this->s_item = $row->companyitem;
		}else{
			error_msg("공급자 정보가 존재하지 않습니다.","");
		}
		mysql_free_result($result);

	}

	function order_info($ordercode)
	{
		$sql = "SELECT * FROM tblorderinfo WHERE ordercode='".$ordercode."' "; //발송완료 :  AND deli_gbn='Y'
		$result = mysql_query($sql,get_db_conn());
		if ($row=mysql_fetch_object($result)) {
			$this->guest_name=$row->sender_name;
			$totalprice=$row->price;
			$reserve=$row->reserve;
			$deli_price=$row->deli_price;
			$dc_price=$row->dc_price;
			$paymethod=$row->paymethod;

			//$this->supplyprice = round($totalprice/(1+$this->taxrate/100));
			//$this->tax = $totalprice-$this->supplyprice;
			//$totalsumprice = $totalprice;

			$this->product_info($ordercode);
			//기타 정보
			$count = $this->count;
			if($deli_price>0) {
				$this->productname[$count]="배송료";
				$this->quantity[$count]=1;
				$this->taxsum[$count]=round($deli_price/(1+$this->taxrate/100));  //단가
				$this->taxsumquantity[$count]=$this->taxsum[$count]; //공급가액
				$this->taxsumsale[$count]=$deli_price-$this->taxsumquantity[$count]; //세액

				$this->supplyprice = $this->supplyprice + $this->taxsumquantity[$count];
				$this->tax = $this->tax + $this->taxsumsale[$count];
				$count++;
			}
			if($reserve>0) {
				$this->productname[$count]="적립금";
				$this->quantity[$count]=1;
				$this->taxsum[$count]=-round($reserve/(1+$this->taxrate/100));  //단가
				$this->taxsumquantity[$count]=$this->taxsum[$count]; //공급가액
				$this->taxsumsale[$count]=-$reserve-$this->taxsumquantity[$count]; //세액

				$this->supplyprice = $this->supplyprice + $this->taxsumquantity[$count];
				$this->tax = $this->tax + $this->taxsumsale[$count];
				$count++;
			}
			if($dc_price<0) {
				$this->productname[$count]="우수회원 할인";
				$this->quantity[$count]=1;
				$this->taxsum[$count]=round($dc_price/(1+$this->taxrate/100));  //단가
				$this->taxsumquantity[$count]=$this->taxsum[$count]; //공급가액
				$this->taxsumsale[$count]=$dc_price-$this->taxsumquantity[$count]; //세액

				$this->supplyprice = $this->supplyprice + $this->taxsumquantity[$count];
				$this->tax = $this->tax + $this->taxsumsale[$count];
				$count++;
			}
			$this->count = $count;
		}
		mysql_free_result($result);
	}

	function product_info($ordercode){
		$sql="SELECT * FROM tblorderproduct WHERE ordercode='".$ordercode."' ";
		$result=mysql_query($sql,get_db_conn());
		$count=0;
		unset($etcdata);
		while($row=mysql_fetch_object($result)){
			if($row->productcode!="99999999997X") {
				if(ereg("^(COU)([0-9]{8})(X)$",$row->productcode)) {				#쿠폰
					if($row->price!=0 && $row->price!=NULL) {
						$etcdata[]=$row;
						continue;
					}
				} else if(ereg("^(9999999999)([0-9]{1})(X|R)$",$row->productcode)) {
					if($row->productcode=="99999999990X") {
						continue;
					} else {
						$etcdata[]=$row;
						continue;
					}
				}
				$this->productname[$count]=strip_tags($row->productname);
				$this->quantity[$count]=$row->quantity;

				$this->taxsum[$count]=round($row->price/(1+$this->taxrate/100));  //단가
				$this->taxsumquantity[$count]=round($row->price*$row->quantity/(1+$this->taxrate/100)); //공급가액
				$this->taxsumsale[$count]=$row->price*$row->quantity-$this->taxsumquantity[$count]; //세액

				$this->supplyprice = $this->supplyprice + $this->taxsumquantity[$count];
				$this->tax = $this->tax + $this->taxsumsale[$count];
				$count++;
			}
		}
		for($k=0;$k<count($etcdata);$k++){
			$this->productname[$count]=strip_tags($etcdata[$k]->productname);
			$this->quantity[$count]=1;
			$this->taxsum[$count]=round($etcdata[$k]->price/(1+$this->taxrate/100));  //단가
			$this->taxsumquantity[$count]=$this->taxsum[$count]; //공급가액
			$this->taxsumsale[$count]=$etcdata[$k]->price-$this->taxsumquantity[$count]; //세액
			$this->supplyprice = $this->supplyprice + $this->taxsumquantity[$count];
			$this->tax = $this->tax + $this->taxsumsale[$count];
			$count++;
		}
		$this->count =$count;
	}

	function set_taxrate ($rate){
		$this->taxrate = $rate;
	}

	function get_serial($ordercode)
	{
		$sql = "SELECT ifnull(max(b_idx),0)+1 as maxidx FROM tblorderbill ";
		$result=@mysql_query($sql,get_db_conn());
		if($row=@mysql_fetch_object($result)) {
			$maxidx = $row->maxidx;
		}
		return substr($ordercode, 0, 8)."-".sprintf("%08d", $maxidx);

	}

}
?>