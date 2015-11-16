<?php
require_once dirname(__FILE__).'/../core/Constants.php';
require_once dirname(__FILE__).'/NiceLog.php';

/**
 * 
 * @author kblee
 *
 */
class NicePayLogJournal{
	
	/**
	 * 
	 * @var $instance
	 */
	private static $instance;
	
	/** The event log path. */
	private $logPath;
	
	
	/** The event logger. */
	private $eventLogger;

	private $appLogger;
	
	/**
	 * Create a MnBankLogJournal instance.
	 */
	private function NicePayLogJournal(){
		
	}
	
	/**
	 * get a Single MnBankLogJournal instance.
	 */
	public static function getInstance(){
		if(!isset(NicePayLogJournal::$instance)){
			NicePayLogJournal::$instance = new NicePayLogJournal();
		}
		return NicePayLogJournal::$instance;
	}
	
	/**
	 * 
	 * @param  $eventLogPath
	 */
	public function setLogDirectoryPath($logPath){
		$this->logPath = $logPath;
	}
	
	/**
	 * 
	 */
	public function configureNicePayLog4PHP(){
		if(!isset($this->appLogger) || !isset($this->eventLogger)){
			try {
				
				
				$this->appLogger = new NICELog("DEBUG","application");
				if($this->appLogger->StartLog($this->logPath)){
					
					
				}else{
					echo "로그 경로 설정 실패";
				}

				$this->eventLogger = new NICELog("INFO","event");
				if($this->eventLogger->StartLog($this->logPath)){
					
					
				}else{
					echo "로그 경로 설정 실패";
				}
				
			} catch (Exception $e) {
				echo "Exception  : Log Configuration Error";
			}
			
		}
		
	}
	
	/**
	 * Write journal.
	 * 
	 * @param dto the dto
	 */
	public function writeEventLog($dto){

		$serviceMode  = $dto->getParameter(SERVICE_MODE);
		$logString = "";
		
		//StringBuffer logBuffer = new StringBuffer(); 
		$resultCode = $dto->getParameter("ResultCode");
		$reqDate = date("Ymd");
		$reqTime = date("His");
		
		if(PAY_SERVICE_CODE == $serviceMode){ //결제승인
			/**
			 *  승인
	         *  P|결제성공여부|결제요청일|결제요청시간|결제수단|결제상품명|결제금액|USER_ID|응답코드|응답메시지
			 */
			$logString.="P|";
			if( ("3001" == $resultCode) || ("4000" == $resultCode) || ("4001" == $resultCode) || ("A000" == $resultCode)){ //결제성공
				$logString.="TE|";
			}else{ //결제실패
				$logString.="TF|";
			}
			$logString.=$reqDate."|";
			$logString.=$reqTime."|";
			$logString.= ($dto->getParameter(PAY_METHOD)==null?"":trim($dto->getParameter(PAY_METHOD)))."|";
			$logString.= ($dto->getParameter(GOODS_NAME)==null?"":trim($dto->getParameter(GOODS_NAME)))."|";
			$logString.= ($dto->getParameter(GOODS_AMT)==null?"0":trim($dto->getParameter(GOODS_AMT)))."|";
			$logString.= ($dto->getParameter(MALL_USER_ID)==null?"":trim($dto->getParameter(MALL_USER_ID)))."|";
			$logString.=trim($resultCode)."|";
			$logString.=($dto->getParameter("ResultMsg")==null?"":trim($dto->getParameter("ResultMsg")));
		}else if(CANCEL_SERVICE_CODE == $serviceMode){                                                          // 결제취소
			/**
			 *  취소
	         *  C|취소성공여부|취소요청일|취소요청시간|결제수단|취소금액|USER_ID|응답코드|응답메시지
			 */
			$logString.="C|";
			if(("2001" == $resultCode) || ("2005" == $resultCode)){ //취소성공
				$logString.="TE|";
			}else{ //결제실패
				$logString.="TF|";
			}
			$logString.=$reqDate."|";
			$logString.=$reqTime."|";
			
			$logString.= ($dto->getParameter(PAY_METHOD)==null?"":trim($dto->getParameter(PAY_METHOD)))."|";
			$logString.= ($dto->getParameter(GOODS_NAME)==null?"":trim($dto->getParameter(GOODS_NAME)))."|";
			$logString.= ($dto->getParameter(GOODS_AMT)==null?"0":trim($dto->getParameter(GOODS_AMT)))."|";
			$logString.= ($dto->getParameter(MALL_USER_ID)==null?"":trim($dto->getParameter(MALL_USER_ID)))."|";
			$logString.=trim($resultCode)."|";
			$logString.=($dto->getParameter("ResultMsg")==null?"":trim($dto->getParameter("ResultMsg")));
		}
		if(isset($logString) && strlen($logString) > 0){	
			$this->eventLogger->WriteLog($logString);
			$this->eventLogger->CloseLog("");
		}
	}

	public function writeAppLog($string){
		$this->appLogger->WriteLog($string);
	}

	public function errorAppLog($string){
		$this->appLogger->WriteLog($string);
	}

	public function warnAppLog($string){
		$this->appLogger->WriteLog($string);
	}
	
	
}
?>
