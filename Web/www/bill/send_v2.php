<?php
	require 'cfg.php';

	/*
	세금계산서/계산서 전송 예제입니다.
	*/

	//  hiworks bill 객체 생성
	$HB = new Hiworks_Bill_V2( $cfg['domain'], $cfg['license_id'], $cfg['license_no'], $cfg['partner_id'] );

	// 기본정보 입력
	$HB->set_basic_info('d_type', HB_DOCUMENTTYPE_DETAIL); // d_type : 세금계산서(HB_DOCUMENTTYPE_TAX), 계산서(HB_DOCUMENTTYPE_BILL), 거래명세서(HB_DOCUMENTTYPE_DETAIL)
	$HB->set_basic_info('kind', HB_TAXTYPE_TAX);        // kind : 과세(HB_TAXTYPE_TAX), 영세(HB_TAXTYPE_NOTAX), 수동(HB_TAXTYPE_MANUAL)
	$HB->set_basic_info('sendtype', HB_SENDTYPE_SEND);  // sendtype : 매출(HB_SENDTYPE_SEND), 매입(HB_SENDTYPE_RECV)

	$HB->set_basic_info('detail_together_tax', '1'); // 거래명세서 발송 시 세금계산서 동시 발송 여부(거래명세서만 발송할 경우는 주석처리하세요.)

	$HB->set_basic_info('c_name', '받는담당자');             // c_name : 담당자명
	$HB->set_basic_info('c_email', 'billapi@hiworks.co.kr'); // c_email : 이메일주소
	$HB->set_basic_info('c_cell', '010-000-0000');           // c_cell : 휴대폰
	$HB->set_basic_info('c_phone', '02-000-0000');           // c_phone : 일반전화

	$HB->set_basic_info('c_name2', '받는담당자');             // c_name2 : 담당자명2
	$HB->set_basic_info('c_email2', 'billapi@hiworks.co.kr'); // c_email2 : 이메일주소2
	$HB->set_basic_info('c_cell2', '010-000-0000');           // c_cell2 : 휴대폰2
	$HB->set_basic_info('c_phone2', '02-000-0000');           // c_phone2 : 일반전화2

	$HB->set_basic_info('sc_name', '보내는담당자');           // sc_name : 담당자명
	$HB->set_basic_info('sc_email', 'billapi@hiworks.co.kr'); // sc_email : 이메일주소
	$HB->set_basic_info('sc_cell', '010-000-0000');           // sc_cell : 휴대폰
	$HB->set_basic_info('sc_phone', '02-000-0000');           // sc_phone : 일반전화

	$HB->set_basic_info('memo', '메모');                     // memo : 메모
	$HB->set_basic_info('book_no', '111-111');               // book_no : 책번호 X권 X호
	$HB->set_basic_info('serial', '222-222');                // serial : 일련번호

	$HB->set_document_info('issue_date', date('Y-m-d')); // issue_date : 작성일
	$HB->set_document_info('supplyprice', '500000');     // supplyprice : 공급가액
	$HB->set_document_info('tax', '50000');              // tax : 세금
	$HB->set_document_info('p_type', HB_PTYPE_RECEIPT);  // ptype : 영수(HB_PTYPE_RECEIPT), 청구(HB_PTYPE_CALL)
	$HB->set_document_info('remark', '');                // remark : 비고
	$HB->set_document_info('money', '');                 // money : 현금
	$HB->set_document_info('moneycheck', '');            // moneycheck : 수표
	$HB->set_document_info('bill', '');                  // bill : 어음
	$HB->set_document_info('uncollect', '');             // uncollect : 외상미수금

	// 공급자 정보
	$HB->set_company_info('s_number', '111-11-11111'); // s_number : 등록번호
	$HB->set_company_info('s_tnumber', '1111');        // s_tnumber : 종사업장번호
	$HB->set_company_info('s_name', '상호');           // s_name : 상호(법인명)
	$HB->set_company_info('s_master', '대표자성명');   // s_master : 성명(대표자)
	$HB->set_company_info('s_address', '주소');        // s_address : 주소
	$HB->set_company_info('s_condition', '업태');      // s_condition : 업태
	$HB->set_company_info('s_item', '종목');           // s_item : 종목

	// 공급받는자 정보
	$HB->set_company_info('r_number', '222-22-22222'); // r_number : 등록번호
	$HB->set_company_info('r_tnumber', '2222');        // r_tnumber : 종사업장번호
	$HB->set_company_info('r_name', '상호');           // r_name : 상호(법인명)
	$HB->set_company_info('r_master', '대표자성명');   // r_master : 성명(대표자)
	$HB->set_company_info('r_address', '주소');        // r_address : 주소
	$HB->set_company_info('r_condition', '업태');      // r_condition : 업태
	$HB->set_company_info('r_item', '종목');           // r_item : 종목

	// 계산정보입력
	// 세금계산서, 계산서는 최대 4개, 거래명세서는 최대 20개까지 입력가능함
	$HB->set_work_info( '10', '30', '웹메일 euc-kr', 'EA', '1', '100000', '100000', '10000', '', '110000' );
	$HB->set_work_info( '10', '30', '웹하드', 'EA', '1', '200000', '200000', '20000', '', '220000' );
	$HB->set_work_info( '10', '30', '웹하드', 'EA', '1', '200000', '200000', '20000', '', '220000' );

	$rs = $HB->send_document( HB_SOAPSERVER_URL );

	if (!$rs) {
		$HB->showError();
		exit;
	}

	$HB->view('Result :', $HB->get_document_id());
	unset($HB, $rs);
?>