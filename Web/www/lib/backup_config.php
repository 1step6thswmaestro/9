<?php

#tbldesignnewpage
$backuppage = array(
	'topmenu'		=> array(
							'name'		=> 'type_code',				//각상품대불류별메뉴
							'subject'	=> ''
						),
	'leftmenu'		=> array(
							'name'		=> 'type_code',				//상단메뉴들
							'subject'	=> ''
						),
	'mainpage'		=> array(
							'name'		=> 'type',				//메인본문꾸미기
							'subject'	=> '메인페이지'				
						),
	'bottom'		=> array(
							'name'		=> 'type_code',				//하단화면꾸미기
							'subject'	=> '쇼핑몰 하단'				
						),
	'loginform'		=> array(
							'name'		=> 'type',				//로그인디자인관리
							'subject'	=> '로그인폼'				
						),
	'logoutform'	=> array(
							'name'		=> 'type',				//로그아웃디자인관리
							'subject'	=> '로그아웃폼'				
						),
	'prlist'		=> array(
							'name'		=> 'type_code_leftmenu',	//모든카테고리
							'subject'	=> '상품 카테고리',
							'info'		=> 'ulist_type'				//tblproductcode all : Y , where codeA+B+C+D
						),
	'prdetail'		=> array(
							'name'		=> 'type_code_leftmenu',				//모든카테고리상세
							'subject'	=> '상품상세 화면',
							'info'		=> 'udetail_type'			//tblproductcode all : Y , where codeA+B+C+D
						),
	'bttoolsetc'	=> array(
							'name'		=> 'type',				//기본메인설정
							'subject'	=> '기본메인설정'				
						),
	'bttools'		=> array(
							'name'		=> 'type',				//플로bar디자인
							'subject'	=> '기본메인화면'				
						),
	'bttoolstdy'	=> array(
						'name'		=> 'type',				//최근 본 상품본문
						'subject'	=> '최근 본 상품 본문'				
					),
	'bttoolswlt'	=> array(
						'name'		=> 'type',				//wishlist 본문
						'subject'	=> 'wishlist 본문'
					),
	'bttoolsbkt'	=> array(
						'name'		=> 'type',				//장바구니 본문
						'subject'	=> '장바구니 본문'
					),
	'bttoolsmbr'	=> array(
							'name'		=> 'type',				//회원정보 본문
							'subject'	=> '회원정보 본문'
						),
	'tag'			=> array(
							'name'		=> 'type_leftmenu',				//인기태그
							'subject'	=> '인기태그 화면',
							'info'		=> 'design_tag'					//tblshopinfo 적용여부에 따라 Y : 'U' / N : DesignDeNum
						),
	'tagsearch'		=> array(
							'name'		=> 'type_leftmenu',				//태그검색
							'subject'	=> '태그검색 화면',
							'info'		=> 'design_tagsearch'			//tblshopinfo 적용여부에 따라 Y : 'U' / N : DesignDeNum
						),
	'prnew'			=> array(
							'name'		=> 'type_leftmenu',				//신상품
							'subject'	=> '섹션 신상품 화면',
							'info'		=> 'design_prnew'				//tblshopinfo 적용여부에 따라 Y : 'U' / N : DesignDeNum
						),
	'prbest'		=> array(
							'name'		=> 'type_leftmenu',				//인기상품
							'subject'	=> '섹션 인기상품 화면',
							'info'		=> 'design_prbest'				//tblshopinfo 적용여부에 따라 Y : 'U' / N : DesignDeNum
						),
	'prhot'			=> array(
							'name'		=> 'type_leftmenu',				//추천상품
							'subject'	=> '섹션 추천상품 화면',
							'info'		=> 'design_prhot'				//tblshopinfo 적용여부에 따라 Y : 'U' / N : DesignDeNum
						),
	'prspecial'		=> array(
							'name'		=> 'type_leftmenu',				//특별상품
							'subject'	=> '섹션 특별상품 화면',
							'info'		=> 'design_prspecial'			//tblshopinfo 적용여부에 따라 Y : 'U' / N : DesignDeNum
						),
	'search'		=> array(
							'name'		=> 'type_leftmenu',				//상품검색
							'subject'	=> '상품검색 결과화면',
							'info'		=> 'design_search'				//tblshopinfo 적용여부에 따라 Y : 'U' / N : DesignDeNum
						),
	'basket'		=> array(
							'name'		=> 'type_leftmenu',				//장바구니
							'subject'	=> '장바구니 화면',
							'info'		=> 'design_basket'				//tblshopinfo 적용여부에 따라 Y : 'U' / N : DesignDeNum

						),
	'primgview'		=> array(
							'name'		=> 'type_leftmenu_filename',			//상품이미지 확대창
							'subject'	=> '상품이미지 확대창 디자인'
						),
	'noticelist'		=> array(
							'name'		=> 'type_filename',			//공지사항 팝업목록
							'subject'	=> '공지사항 팝업 목록창'
						),
	'noticeview'		=> array(
							'name'		=> 'type_filename',			//공지사항 팝업상세페이지 
							'subject'	=> '공지사항 팝업 상세 페이지'
						),
	'infolist'		=> array(
							'name'		=> 'type_filename',			//정보팝업 목록
							'subject'	=> '정보 팝업 목록창'
						),
	'infoview'		=> array(
							'name'		=> 'type_filename',			//정보팝업 상세페이지
							'subject'	=> '정보 팝업 상세 페이지'
						),
	'joinmail'		=> array(
							'name'		=> 'type',			//신규 회원가입메일
							'subject'	=> 'Y'	//데이터값 불러와서 저장
						),
	'ordermail'		=> array(
							'name'		=> 'type',			//주문신청메일
							'subject'	=> 'Y'	//데이터값 불러와서 저장
						),
	'delimail'		=> array(
							'name'		=> 'type',			//주문발송메일
							'subject'	=> 'Y'	//데이터값 불러와서 저장
						),
	'bankmail'		=> array(
							'name'		=> 'type',			//주문입금메일
							'subject'	=> 'Y'	//데이터값 불러와서 저장
						),
	'passmail'		=> array(
							'name'		=> 'type',			//아이디/패스워드메일
							'subject'	=> 'Y'	//데이터값 불러와서 저장
						),
	'authmail'		=> array(
							'name'		=> 'type',			//회원인증메일
							'subject'	=> 'Y'	//데이터값 불러와서 저장
						),
	'email'		=> array(
							'name'		=> 'type_code_filename',			//폼메일화면
							'subject'	=> '폼메일 화면 디자인'
						),
	'board'		=> array(
							'name'		=> 'type_code_leftmenu_filename',			//회원인증메일
							'subject'	=> '게시판 상단화면 디자인'
						),
	'joinagree'		=> array(
							'name'		=> 'type_leftmenu',			//회원가입약관
							'subject'	=> ''
						),
	'mbjoin'		=> array(
							'name'		=> 'type_leftmenu',			//회원가입입력폼
							'subject'	=> '회원가입 입력폼 디자인',
							'info'		=> 'design_mbjoin'			//tblshopinfo 적용여부에 따라 Y : 'U' / N : DesignDeNum
						),
	'mbmodify'		=> array(
							'name'		=> 'type_leftmenu',			//회원수정화면
							'subject'	=> '회원정보수정 화면 디자인',
							'info'		=> 'design_mbmodify'		//tblshopinfo 적용여부에 따라 Y : 'U' / N : DesignDeNum
						),
	'iddup'		=> array(
							'name'		=> 'type',			//회원ID체크
							'subject'	=> '회원ID 중복체크 화면 디자인'
						),
	'findpwd'		=> array(
							'name'		=> 'type_leftmenu',			//패스워드분실화면
							'subject'	=> '패스워드 분실화면 디자인'
						),
	'login'		=> array(
							'name'		=> 'type_leftmenu',			//로그인화면
							'subject'	=> '로그인 화면 디자인'
						),
	'memberout'		=> array(
							'name'		=> 'type_leftmenu',			//회원탈퇴화면
							'subject'	=> '회원탈퇴 화면 디자인'
						),
	'mypage'		=> array(
							'name'		=> 'type_leftmenu',			//마이페이지
							'subject'	=> 'MyPage 화면',
							'info'		=> 'design_mypage'			//tblshopinfo 적용여부에 따라 Y : 'U' / N : DesignDeNum

						),
	'orderlist'		=> array(
							'name'		=> 'type_leftmenu',			//주문리스트
							'subject'	=> '주문리스트 화면',
							'info'		=> 'design_orderlist'		//tblshopinfo 적용여부에 따라 Y : 'U' / N : DesignDeNum
						),
	'wishlist'		=> array(
							'name'		=> 'type_leftmenu',			//wishlist
							'subject'	=> 'WishList 화면',
							'info'		=> 'design_wishlist'		//tblshopinfo 적용여부에 따라 Y : 'U' / N : DesignDeNum
						),
	'mycoupon'		=> array(
							'name'		=> 'type_leftmenu',			//쿠폰화면
							'subject'	=> '마이페이지 쿠폰 화면',
							'info'		=> 'design_mycoupon'		//tblshopinfo 적용여부에 따라 Y : 'U' / N : DesignDeNum
						),
	'myreserve'		=> array(
							'name'		=> 'type_leftmenu',			//적립금화면
							'subject'	=> '마이페이지 적립금 화면',
							'info'		=> 'design_myreserve'		//tblshopinfo 적용여부에 따라 Y : 'U' / N : DesignDeNum
						),
	'mypersonal'		=> array(
							'name'		=> 'type_leftmenu',			//1:1고객문의
							'subject'	=> '마이페이지 1:1고객문의 화면',
							'info'		=> 'design_mypersonal'		//tblshopinfo 적용여부에 따라 Y : 'U' / N : DesignDeNum
						),
	'mycustsect'		=> array(
							'name'		=> 'type_leftmenu',			//단골매장
							'subject'	=> '마이페이지 단골매장 화면',
							'info'		=> 'design_mycustsect'		//tblshopinfo 적용여부에 따라 Y : 'U' / N : DesignDeNum
						),
	'surveylist'		=> array(
							'name'		=> 'type',			//투표리스트
							'subject'	=> '투표리스트 화면 디자인'
						),
	'surveyview'		=> array(
							'name'		=> 'type',			//투표결과
							'subject'	=> '투표결과 화면 디자인'
						),
	'reviewopen'		=> array(
							'name'		=> 'type_filename',			//상품리뷰
							'subject'	=> '상품리뷰 팝업화면 디자인'
						),
	'reviewall'			=> array(
							'name'		=> 'type',			//리뷰모음 화면 디자인
							'subject'	=> '리뷰모음 화면 디자인'
						),
	'rssinfo'			=> array(
							'name'		=> 'type',			//RSS
							'subject'	=> 'RSS 페이지'
						),
	'brlist'			=> array(
							'name'		=> 'type_code_leftmenu',	//상품 브랜드
							'subject'	=> '상품 브랜드',
							'info'		=> 'u_type'					//tblproductbrand all : Y , where bridx
						),
	'bmap'				=> array(
							'name'		=> 'type_leftmenu',			//브랜드맵 화면
							'subject'	=> '브랜드맵 화면',
							'info'		=> 'design_bmap'			//tblshopinfo 적용여부에 따라 Y : 'U' / N : DesignDeNum
						),
	'newpage'			=> array(
							'name'		=> 'type_leftmenu_code_filename',			//개별추가 일반페이지
							'subject'	=> 'Y'	//데이터값 불러와서 저장
						),
	'community'			=> array(
							'name'		=> 'type_leftmenu_code_filename',			//개별추가 커뮤니티
							'subject'	=> 'Y'	//데이터값 불러와서 저장
						),
	'rbanner'			=> array(
							'name'		=> 'type_leftmenu_code_filename',			//최근본상품관리
							'subject'	=> 'Y'	//데이터값 불러와서 저장
						),

//별도 tbldesign => 2011-09-16 top_height만 저장
	'topmenuall'		=> array(
							'name'		=> 'designtype_height',			
							'subject'	=> ''	
						),

//2011-09-16 추가사항
	'adultintro'		=> array(
							'name'		=> 'type_leftmenu',			//성인몰인트로
							'subject'	=> ''	
						),
	'adultlogin'		=> array(
							'name'		=> 'type_leftmenu',			//성인몰로그인
							'subject'	=> ''	
						),
	'agreement'		=> array(
							'name'		=> 'type_leftmenu',			//이용약관
							'subject'	=> ''	
						),
	'useinfo'		=> array(
							'name'		=> 'type_leftmenu',			//이용안내
							'subject'	=> '이용안내'	
						),
//2011-11-07 분리되어 있던 quickmenu를 newpage로 이동시킴 by.jyh 
	'quickmenu'			=> array(
							'name'		=> 'type_leftmenu_code_filename',			//최근본상품관리
							'subject'	=> 'Y'	//데이터값 불러와서 저장
						)


);
