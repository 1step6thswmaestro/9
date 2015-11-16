<?php

/**
 * 
 * @author kblee
 *
 */
class CancelWebParamGather implements WebParamGather {
	
	/**
	 * 
	 */
	public function CancelWebParamGather(){
		
	}
	
	
	/**
	 * 
	 * @param $request
	 */
	public function gather($request){
		
		$webParam = new WebMessageDTO();
		
		$tid = $request["TID"];
		
		$svcCd = "";
		
		if(strlen($tid)>=30){
			$svcCd = substr($tid,10, 2);
		}
		$payMethod = "";
		if(SVC_CD_CARD == $svcCd){
			$payMethod = CARD_PAY_METHOD;
		}else if(SVC_CD_BANK == $svcCd){
			$payMethod = BANK_PAY_METHOD;
		}else if(SVC_CD_CELLPHONE == $svcCd){
			$payMethod = CELLPHONE_PAY_METHOD;
		}
		$webParam->setParameter(PAY_METHOD, $payMethod);
		
		$cancelAmt = $request["CancelAmt"];
		$webParam->setParameter(CANCEL_AMT, $cancelAmt);
		
		$cancelPwd = $request["CancelPwd"];
		$webParam->setParameter(CANCEL_PWD, $cancelPwd);
		
		$cancelMsg = $request["CancelMsg"];
		$webParam->setParameter(CANCEL_MSG, $cancelMsg);
		
		$cancelIP = $request["CancelIP"];
		$webParam->setParameter(CANCEL_IP, $cancelIP);
		
		$partialCancelCode = $request["PartialCancelCode"];
		$webParam->setParameter(PARTIAL_CANCEL_CODE,$partialCancelCode);
		
		return $webParam;
	}
	
}
?>
