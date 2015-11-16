<?php

    require 'cfg.php';

    /*
    세금계산서/계산서 전송 예제입니다.
    */

    //  hiworks bill 객체 생성
    $HB = new Hiworks_Bill( $cfg['domain'], $cfg['license_id'], $cfg['license_no'], $cfg['partner_id'] );


    //  Type 입력
    $HB->set_type( HB_DOCUMENTTYPE_TAX , HB_TAXTYPE_TAX, HB_SENDTYPE_SEND );

    //  기본정보 입력
    $HB->set_basic_info( '홍길동', 'billapi@hiworks.co.kr', '010-111-1111', '메모', '111-111', '222-222' );

    $HB->set_document_info(date('Y-m-d'), '300000', '30000', HB_PTYPE_RECEIPT, '', '', '', '', '');

    //  공급자 정보와 공급받는자 정보
    $HB->set_company_info( '214-86-39239', '가비아', '담당자', '서울', '서비스업', '솔루션', HB_COMPANYPREFIX_SUPPLIER );
    $HB->set_company_info( '214-86-39239', '업체명', '담당자', '서울', '서비스업', '계산서', HB_COMPANYPREFIX_CONSUMER );

    //  계산정보입력
    $HB->set_work_info( '10', '30', '웹메일', 'EA', '1', '100000', '100000', '10000', '', '110000' );
    $HB->set_work_info( '10', '30', '웹하드', 'EA', '1', '200000', '200000', '20000', '', '220000' );

    $rs = $HB->send_document( HB_SOAPSERVER_URL );

    if (!$rs) {
        $HB->showError();
        exit;
    }

    $HB->view('Result :', $HB->get_document_id());
    unset($HB, $rs);
?>