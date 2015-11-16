<?php
require_once dirname(__FILE__).'/WebParamGather.php';

/**
 * 
 * @author kblee
 *
 */
class CashReceiptWebParamGather implements WebParamGather{
	
	/**
	 * Default Constructor
	 */
	public function CashReceiptWebParamGather(){
		
	}
	
	/**
	 * 
	 * @param $request
	 */
	public function gather($request) {
		$webParam = new WebMessageDTO();
		// 주민 번호,휴대폰 번호 식별 값
		$webParam->setParameter(RECEIPT_TYPE_NO,$request["ReceiptTypeNo"]);
		// 소득공제 구분
		$webParam->setParameter(RECEIPT_TYPE,$request["ReceiptType"]);
		// 봉사료
		$webParam->setParameter(RECEIT_SERVICE_AMT,$request["ReceiptServiceAmt"]);
		//부가가치세
		$webParam->setParameter(RECEIT_VAT,$request["ReceiptVAT"]);
		
		//부가가치세
		$webParam->setParameter(RECEIT_SUPPLY_AMT,$request["ReceiptSupplyAmt"]);

		//현금 영수증 요청 금액
		$webParam->setParameter(RECEIPT_AMT,$request["ReceiptAmt"]);
				
		//현금 영수증 서브몰 사업자번호
		$webParam->setParameter(RECEIT_SUB_NUM,$request["ReceiptSubNum"]);
		
		return $webParam;
	}
	
}
?>
