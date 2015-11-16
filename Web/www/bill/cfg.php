<?php

$doc = '.';

include $doc . '/lib/nusoap.php';
include $doc . '/lib/XML.class.php';
include $doc . '/lib/Hiworks_Bill.class.php';

/*
회사 정보로 수정해주세요
*/
$cfg = array();

$cfg['license_no'] = '';    //  발급받으신 번호를 입력해주세요
$cfg['license_id'] = '';    //  하이웍스 아이디를 입력해주세요
$cfg['domain'] = '';    //  하이웍스 도메인을 입력해주세요
$cfg['partner_id'] = '';    //  하이웍스 회사 코드를 입력해주세요


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
$document_status['W'] = '미발송';
$document_status['T'] = '미열람';
$document_status['R'] = '열람';
$document_status['S'] = '승인';
$document_status['B'] = '반려';
$document_status['C'] = '승인취소요청';
$document_status['A'] = '승인최소완료';

?>