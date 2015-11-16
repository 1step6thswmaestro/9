<?php
/***************************************************************************
본인 인증 확인용 처리 클레스 (서울신용평가정보)

주의 : 해당 기능 사용전 반드시 bizsiren.com 에 로그인해서 본인확인의 도메인 및 요청 페이지 등의 값을 설정후 해당 값의 정보를 srvList 에 설정해주어야만 합니다.
**************************************************************************/

class sci_ipin{
	var $seedFileName = '/SciSecuX'; // 라이브러리 파일 경로를 기준으로 컴파일된 암호화 파일의 위치 기술
	var $seedFile = ''; // 상대 경로 산출용으로 클레스 인스턴스화시 자동 경로 보정 해서 사용 됨 ( 수정하지 말것)
	var $id = 'OBJ001'; // bizsiren.com 에 등록된 본인인증 확인 업체 id
	// [필수&주의] bizsiren.com 의 도메인 관리에 등록된 요청 경로 정보를 등록 도메인 별로 배열 화하여 등록하여서 확인하도록 함.
	var $srvList = array(
						'yeanchon.objet.co.kr'=>array(
											'/sci/ipin/request.php'=>'004001'
											),
						'yeanchon.com'=>array(
											'/sci/ipin/request.php'=>'005001'
											),
						'www.yeanchon.com'=>array(
											'/sci/ipin/request.php'=>'006001'
											)
						);

	function sci_ipin(){
		$this->__construct();
	}

	function __construct(){
		$this->seedFile = dirname(__FILE__).$this->seedFileName;
	}


	function _srvNo(){
		$srvNo = '';
		$listarr = $this->srvList[$_SERVER['HTTP_HOST']];
		if(is_array($listarr) && count($listarr) >0 && isset($listarr[$_SERVER['PHP_SELF']]) && !empty($listarr[$_SERVER['PHP_SELF']])) $srvNo = $listarr[$_SERVER['PHP_SELF']];

		return $srvNo;
	}

	// 본인확인 요청용 정보값 암호화 후 반환
	// 추가 파라메터가 있을 경우 addVar 을 입력값으로 받음
	function _reqInfo(){
		$CurTime = date('YmdHis');  //현재 시각 구하기
		//6자리 랜덤값 생성
		$RandNo = rand(100000, 999999);

		$id = $this->id;
		$srvNo = $this->_srvNo();
		if(!preg_match('/^[0-9]{6}$/',$srvNo)){ // 서비스 번호가 형식에 맞지 않ㅇ르 경우 오류 처리
			//echo '본인 확인용 요청 서비스 번호가 올바르지 않습니다. 프로그램상의 설정을 참조 하시기 바랍니다.'.$srvNo;
			return '';
		}

		$reqNum = $CurTime.$RandNo;

		/************************************************************************************/
		/* reqNum 값은 최종 결과값 복호화를 위한 SecuKey로 활용 되므로 중요합니다.			*/
		/* reqNum 은 본인 확인 요청시 항상 새로운 값으로 중복 되지 않게 생성 해야 합니다.	*/
		/* 쿠키 또는 Session및 기타 방법을 사용해서 reqNum 값을								*/
		/* ipin_result_seed.php에서 가져 올 수 있도록 해야 함.								*/
		/* 샘플을 위해서 쿠키를 사용한 것이므로 참고 하시길 바랍니다.						*/
		/************************************************************************************/
		//01. reqNum 쿠키 생성
		setcookie("REQNUM", $reqNum, time()+600);

		$exVar       = "0000000000000000";        // 확장임시 필드입니다. 수정하지 마세요..

		//02. 암호화 파라미터 생성
		$enc_reqInfo = $reqNum . "/" . $id . "/" . $srvNo . "/" . $exVar;

		//03. 본인확인 요청정보 1차암호화
		$enc_reqInfo = exec($this->seedFile." SEED 1 1 $enc_reqInfo ");

		//04. 요청정보 위변조검증값 생성
		$hash_reqInfo = exec($this->seedFile." HMAC 1 1 $enc_reqInfo ");    // 요청정보 위변조검증값 생성

		//05. 요청정보 2차암호화
		//데이터 생성 규칙 : "요청정보 1차 암호화/위변조검증값/암복화 확장 변수"
		$enc_reqInfo = $enc_reqInfo. "/" .$hash_reqInfo. "/" ."00000000";
		$enc_reqInfo = exec($this->seedFile." SEED 1 1 $enc_reqInfo ");

		return $enc_reqInfo;
	}

	// 본인확인 결과 값 디코딩 해서 연관 배열로 반환
	function _retInfo($retInfo){
		$return = array(); // 결과 반환용 연관 배열 처리 - 오류 있을 경우는 err 컬럼에 메시지 넣어서 반환
		/************************************************************************************/
		/* - sample 페이지에서 요청 시 쿠키에 저장한 Reqnum값을 가져와서 IV값에 셋팅   	    */
		/* - 쿠키 만료시간 경과 후 결과처리 못함										    */
		/************************************************************************************/
		//01. 쿠키값 확인
		$iv = "";
		if(strlen(trim($retInfo)) <1){
			$return['err'] = "결과 값이 없습니다.";
			return $return;
		}

		if (isset($_COOKIE["REQNUM"])) {
			$iv = $_COOKIE["REQNUM"];
			setcookie("REQNUM", "", time()-600); //쿠키 삭제
		}else{
			$return['err'] = "세션이 만료되었습니다.!!";
			return $return;
		}
		//02. 요청결과 복호화
		$dec_retInfo = exec($this->seedFile." SEED 2 0 $iv $retInfo ");

		//데이터 조합 : "본인확인1차암호화값/위변조검증값/암복화확장변수"
		$totInfo = split("/", $dec_retInfo);
		$encPara  = $totInfo[0];		//암호화된 통합 파라미터
		$encMsg   = $totInfo[1];		//암호화된 통합 파라미터의 Hash값

		//03. 위변조검증값 생성
		$hmac_str = exec($this->seedFile." HMAC 1 0 $encPara ");
		if($hmac_str != $encMsg){
			$return['err'] = "비정상적인 접근입니다.!!";
			return $return;
		}
		//04. 본인확인1차암호화값 복호화
		$decPara = exec($this->seedFile." SEED 2 0 $iv $encPara ");

		//05. 파라미터 분리
		$split_dec_retInfo = split("/", $decPara);
		$return['reqNum'] = $split_dec_retInfo[0];   //요청번호
		$return['vDiscrNo'] = $split_dec_retInfo[1];   //아이핀번호
		$return['name'] = $split_dec_retInfo[2];   //성명
		$return['result'] = $split_dec_retInfo[3];   //인증결과
		$return['age'] = $split_dec_retInfo[4];   //연령대
		$return['sex'] = $split_dec_retInfo[5];   //성별
		$return['ip'] = $split_dec_retInfo[6];   //Client IP
		$return['authInfo'] = $split_dec_retInfo[7];   //발급수단정보
		$return['birth'] = $split_dec_retInfo[8];   //생년월일
		$return['fgn'] = $split_dec_retInfo[9];   //내/외국인구분
		$return['discrHash'] = $split_dec_retInfo[10];  //중복가입확인정보
		$return['ciVersion'] = $split_dec_retInfo[11];  //연계정보 버젼
		$return['ciscrHash'] = $split_dec_retInfo[12];  //연계정보


		$return['discrHash'] = exec($this->seedFile." SEED 2 0 $iv ".$return['discrHash']);    //중복가입확인정보는 구분자인 "/"가 나올수 있으므로 한번더 복호화
		$return['ciscrHash'] = exec($this->seedFile." SEED 2 0 $iv ".$return['ciscrHash']);    //연계정보는 구분자인 "/"가 나올수 있으므로 한번더 복호화

		return $return;
	}

	function _getSave($reqNum){
		$return = array('err'=>'');
		if(!preg_match('/^[0-9a-zA-Z]{10,}$/',$reqNum)){
			$return['err'] = '식별 번호 오류';
		}else{
			$sql = "select * from sci_ipin_log where reqNum='".$reqNum."' limit 1";

			if(false === $res = mysql_query($sql,get_db_conn())) $return['err'] = mysql_error();
			else{
				if(mysql_num_rows($res) < 1) $return['err'] = '요청 정보에 해당하는 결과 값이 없습니다.';
				else{
					$return = mysql_fetch_assoc($res);
				}
			}
		}
		return $return;
	}
}

?>