<?
$Dir="../../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

$_ShopInfo->getPgdata();


define('KCPMOD_REAL',false); // 테스트 여부 실 사용시 해당 값을 true 로 설정할것


$_ShopInfo->getPgdata();

$g_conf_home_dir	= dirname(__FILE__);	// BIN 절대경로 입력 bin 이전 까지
//$g_conf_log_level	= "3";											// 변경불가
$g_conf_pa_port		= "8090";										// 포트번호 , 변경불가

$pgid_info=GetEscrowType($_data->card_id);
$pg_type=$pgid_info["PG"];


if(KCPMOD_REAL){
	switch(substr($oinfo['paymethod'],0,1)){
		/*
		case "B":
			exit('무통장 입금은 부분 취소 대상이 아닙니다.');
			break;*/
		case "V":
			$pgid_info=GetEscrowType($_data->trans_id);			
			$pg_type=$pgid_info["PG"];
			$mod_type = 'STPA';
			break;
			/*
		case "O":
			$pgid_info=GetEscrowType($_data->virtual_id);
			$pg_type=$pgid_info["PG"];
			break;
			
		case "Q":
			$pgid_info=GetEscrowType($_data->escrow_id);
			$pg_type=$pgid_info["PG"];
			break;
			*/
		case "C":
		case "P":
			$pgid_info=GetEscrowType($_data->card_id);
			$pg_type=$pgid_info["PG"];		
			$mod_type = 'RN07';			
			break;
			/*
		case "M":
			$pgid_info=GetEscrowType($_data->mobile_id);
			$pg_type=$pgid_info["PG"];
			brak;*/
		default:
			exit('지원하지 않는 결제 타입 입니다.');
	}
	if($pgid_info["PG"] != 'A'){
		exit('부분 취소는 현재 KCP모듈만 지원 합니다.');
	}
	
	
	if(empty($pgid_info['ID']) || empty($pgid_info['KEY'])) exit('설정이 올바르지 않습니다.');
	
	$g_conf_pa_url		= "paygw.kcp.co.kr";							// real url : paygw.kcp.co.kr , test url : testpaygw.kcp.co.kr
	$g_conf_site_cd  = $pgid_info['ID'];
	$g_conf_site_key = $pgid_info['KEY'];
}else{
	$g_conf_pa_url		= "testpaygw.kcp.co.kr";							// real url : paygw.kcp.co.kr , test url : testpaygw.kcp.co.kr
	$g_conf_site_cd  = "T0000";                     // ※ 리얼 반영시 KCP에서 발급한 site_cd 사용
	$g_conf_site_key = "3grptw1.zW0GSo4PQdaGvsF__"; // ※ 리얼 반영시 KCP에서 발급한 site_key 사용
	$mod_type = 'RN07';
	$oinfo['tno'] = '20090610121212';
	$mod_desc= '테스트용 부분 취소';
	//$oinfo['mod_mny'] = '100';
	$oinfo['price'] = '1004';
}


?>