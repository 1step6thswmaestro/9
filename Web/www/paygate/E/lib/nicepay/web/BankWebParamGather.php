<?php
require_once dirname(__FILE__).'/WebParamGather.php';

/**
 * 
 * @author kblee
 *
 */
class BankWebParamGather implements WebParamGather{
	
	public function BankWebParamGather(){
		
	}
	
	/**
	 * 
	 * @param $request
	 */
	public function gather($request){
		$webParam = new WebMessageDTO();
		
		
		$receitType = $request["CashReceiptType"];
		$hd_pi = $request["hd_pi"];
		$bankCode = $request["BankCode"];
		$receitTypeNo = $request["ReceiptTypeNo"];
		
		$webParam->setParameter(RECEIPT_TYPE,$receitType);
		$webParam->setParameter(RECEIPT_TYPE_NO,$receitTypeNo);
		$webParam->setParameter(BANK_ENC_DATA, $hd_pi);
		$webParam->setParameter(BANK_CODE, $bankCode);
		
		$transType = $request["TransType"] == null ? "0" : $request["TransType"];
		$webParam->setParameter(TRANS_TYPE,$transType);
		
		$trKey = $request["TrKey"] == null ? "0" : $request["TrKey"];
		$webParam->setParameter(TR_KEY,$trKey);
		
		return $webParam;
	}
}
?>
