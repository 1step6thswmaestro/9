<?php

    require 'cfg.php';

    /*
    세금계산서/계산서 상태체크 예제입니다.
    */

    $document_id = '';

    //  hiworks bill 객체 생성
    $HB = new Hiworks_Bill( $cfg['domain'], $cfg['license_id'], $cfg['license_no'], $cfg['partner_id'] );
    $HB->set_document_id($document_id);


    $documet_result_array = $HB->check_document( HB_SOAPSERVER_URL );

    if (!$documet_result_array) {
        $HB->showError();
        exit;
    }

    $HB->view('Result :', $documet_result_array);
    unset($HB, $rs);

?>