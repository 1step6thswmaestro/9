<?php

    require 'cfg.php';

    /*
    세금계산서/계산서 삭제 예제입니다.
    */

    //  hiworks bill 객체 생성
    $HB = new Hiworks_Bill( $cfg['domain'], $cfg['license_id'], $cfg['license_no'], $cfg['partner_id'] );
    $HB->set_delete_id('');

    $documet_result_array = $HB->delete_document( HB_SOAPSERVER_URL );

    if (!$documet_result_array) {
        $HB->showError();
        exit;
    }

    $HB->view('Result :', $documet_result_array);
    unset($HB, $rs);

?>