<?
	if(substr(getenv("SCRIPT_NAME"),-10)=="/style.php"){
		header("HTTP/1.0 404 Not Found");
		exit;
	}
?>

<style type="text/css">
* html {background:url(http://) fixed;}
img {border:none}

body {
	scrollbar-face-color: #dddddd;
	scrollbar-shadow-color: #aaaaaa;
	scrollbar-highlight-color: #ffffff;
	scrollbar-3dlight-color: #dadada;
	scrollbar-darkshadow-color: #dadada;
	scrollbar-track-color: #eeeeee;
	scrollbar-arrow-color: #ffffff;
	overflow-x:auto;
	overflow-y:scroll;
}

A:link    {color:#666666;font-size:13px;text-decoration:none;font-family:"맑은고딕","Nanum Gothic","나눔고딕","돋움";}
A:visited {color:#666666;font-size:13px;text-decoration:none;font-family:"맑은고딕","Nanum Gothic","나눔고딕","돋움";}
A:active  {color:#666666;font-size:13px;text-decoration:none;font-family:"맑은고딕","Nanum Gothic","나눔고딕","돋움";}
A:hover  {color:#666666;font-size:13px;text-decoration:underline;font-family:"맑은고딕","Nanum Gothic","나눔고딕","돋움";}

.textarea {border:solid 1;border-color:#e3e3e3;font-family:돋움;font-size:9pt;color:333333;overflow:auto; background-color:transparent}
.textarea1 {border:solid 1;border-color:#e3e3e3;font-family:돋움;font-size:8.5pt;color:#929292;background-color:#ffffff;width:100%;padding:10px;letter-spacing:-0.5px;}
.textarea_gonggu {border:solid 1;border-color:#e3e3e3;font-family:돋움;font-size:9pt;color:333333;overflow:auto; background-color:#ffffff;height:80px}

.input {font-size:12px; padding-left:4px; BORDER-RIGHT:#DCDCDC 1px solid; BORDER-TOP:#C7C1C1 1px solid; BORDER-LEFT:#C7C1C1 1px solid; BORDER-BOTTOM:#DCDCDC 1px solid; HEIGHT:19px; BACKGROUND-COLOR: #ffffff;}

.verdana {FONT-SIZE: 9px; FONT-FAMILY:"verdana", "arial";}
.verdana2 {FONT-WEIGHT: bold; FONT-SIZE: 11px; FONT-FAMILY:"verdana", "arial";}

.ndv {background-color:#DDDDDD}
.bdv {background-color:#DDDDDD}
td.blover { background: #F7F7F7; FONT-WEIGHT: bold; FONT-SIZE: 11px; FONT-FAMILY:"verdana", "arial";} /* black:#4A4A4A, gray:#444444, silver:#E0E0E0, white:#F7F7F7 */
td.blout  {FONT-WEIGHT: bold; FONT-SIZE: 11px; FONT-FAMILY:"verdana", "arial";}


.wrap {width:100%; margin:0px; margin-bottom:20px; padding:0px;}


/* 상단 메뉴 */
.topLineMenu {height:30px; text-align:center; border-bottom:1px solid #dddddd;}
.topFavorite {float:left; margin-top:5px; padding-left:15px; background:url('/images/common/icon_favorite.gif') no-repeat; background-position:0% 2px;}
.topFavorite .welcome {color:#FF6600; font-weight:bold;}
.topMemberMenu {float:right; margin-top:5px;}
.topMemberMenu li {float:left; padding-left:20px; letter-spacing:-1px; background:url('/images/common/line_gray.gif') no-repeat; background-position:10px 4px;}
.topMemberMenu .firstLi {padding:0px; background:none;}
.topMemberMenu .basketText {letter-spacing:0px; color:#0082f0;}

.topLogoAndSearch {margin-top:10px; height:90px;}
.topLogo {clear:both; float:left; margin-top:12px;}

.topTagAndSearch {float:left; margin-top:18px; text-align:left;}
.topSearch {position:absolute; left:50%; margin-left:-200px; width:430px; border:0px solid #dddddd;}
.topSearch input {float:left; display:inline-block; margin:0; padding:0 10 0 0; width:364px; height:32px; line-height:32px; padding-left:5px; font-size:13px; border:1px solid #2465ec; text-align:left;}
.topSearch .txt {float:left; display:inline-block; margin:0; padding:0 0 0 0; width:53px; height:32px; background-color:#2465ec; border:none; color:#fff; font-size:13px; line-height:32px; font-weight:bold; text-align:center; font-family:NanumGothic,"나눔고딕";}
.topSearch .txt:link {color:#fff; font-size:13px; font-weight:bold; text-align:center; font-family:NanumGothic,"나눔고딕";}
.topSearch .txt:visited {color:#fff;}
.topSearch .txt:hover {color:#fff;}

.topCommunity {float:right; margin-top:30px;}

.topTagRss {float:left; font-size:0px; margin-top:8px;}
.topTagRss div {float:left; margin-right:5px;}

.topPrMenu {clear:both; background:#0082f0; height:36px;}
.topPrMenuLeft {float:left; height:36px;}
.topPrMenuLeft ul {list-style:none; margin:0px; padding:0px;}
.topPrMenuLeft li {float:left; height:36px; line-height:36px; padding-right:30px;}
.topPrMenuLeft li a:link {font-weight:600; color:#ffffff; font-size:15px;}
.topPrMenuLeft li a:visited {font-weight:600; color:#ffffff; font-size:15px;}
.topPrMenuLeft li a:active {font-weight:600; color:#ffffff; font-size:15px;}
.topPrMenuLeft li a:hover {font-weight:600; color:#ffffff; font-size:15px;}

.topPrMenuRight {float:right; height:36px; font-size:0px; margin-right:2px;}
.topPrMenuRight ul {list-style:none; margin:0px; padding:0px;}
.topPrMenuRight li {float:left; height:36px; line-height:36px; padding-left:15px;}
.topPrMenuRight li a:link {font-weight:600; color:#ffffff; font-size:11px; font-family:NanumGothic,"나눔고딕",arial;}
.topPrMenuRight li a:visited {font-weight:600; color:#ffffff; font-size:11px; font-family:NanumGothic,"나눔고딕",arial;}
.topPrMenuRight li a:active {font-weight:600; color:#ffffff; font-size:11px; font-family:NanumGothic,"나눔고딕",arial;}
.topPrMenuRight li a:hover {font-weight:600; color:#ffffff; font-size:11px; font-family:NanumGothic,"나눔고딕",arial;}


/*본문 타이틀 */
.headline.{color:#333333;font-size:35px;line-height:50px;}
.headline_title.{color:#2c3031;font-size:26px;line-height:35px;}
.headline_stitle.{color:#53595a;font-size:17px;line-height:20px;FONT-WEIGHT: bold;}
.con_text.{color:#868e90;font-size:13px;line-height:25px;FONT-WEIGHT: bold;}


/* 메인 하단 게시판 & 고객센터 */
.mainNoticeQna {float:left; width:68%; border-top:2px solid #444444; padding-top:5px;}
.mainCustomerCenter {float:right; width:240px; border-top:2px solid #0082f0;}
.mainCustomerCenter .telNum {color:#222222; font-size:26px; letter-spacing:-1px; line-height:34px; font-family:Georgia,Times,serif;}


/* 하단메뉴 + 카피라이트 */
.bottomMenu {width:100%; height:29px; margin:0px; padding:0px; text-align:center;}
.bottomMenu .menuAndCopyright {border:0px solid #dddddd; margin:0 auto; padding:0px; width:<?=($_data->layoutdata["SHOPWIDTH"]>0?$_data->layoutdata["SHOPWIDTH"]:"980")?>px;}
.bottomMenu .menuAndCopyright .menuLine td {height:29px; line-height:29px; padding-right:15px;}
.bottomMenu .menuAndCopyright .menuLine td a:link {color:#888888; font-weight:600; font-size:11px; font-family:NanumGothic,"나눔고딕",arial;}
.bottomMenu .menuAndCopyright .menuLine td a:visited {color:#888888; font-weight:600; font-size:11px; font-family:NanumGothic,"나눔고딕",arial;}
.bottomMenu .menuAndCopyright .menuLine td a:active {color:#888888; font-weight:600; font-size:11px; font-family:NanumGothic,"나눔고딕",arial;}
.bottomMenu .menuAndCopyright .menuLine td a:hover {color:#888888; font-weight:600; font-size:11px; font-family:NanumGothic,"나눔고딕",arial;}

/* 서브 페이지 타이틀 */
.subpageTitle {margin-top:15px; margin-bottom:20px; padding-left:18px; text-align:left; color:#444444; font-size:24px; font-weight:500; line-height:30px; border-left:1px solid #dddddd;}

/*소셜공동구매 스타일*/
.gongguing_date{font-family:"verdana", "돋움"; font-size:18px; color:#ffffff; letter-spacing:-1px;FONT-WEIGHT: bold;}
.gongguing_dates{font-family:"verdana", "돋움"; font-size:11px; color:#8E9399; letter-spacing:-1px;FONT-WEIGHT: bold;}
.gongguing_time{font-family:"verdana", "돋움"; font-size:22px; color:#ffffff; letter-spacing:-1px;FONT-WEIGHT: bold;}
.gongguing_price{font-family:"verdana", "돋움"; font-size:20px; color:#4166A0; letter-spacing:-1px;FONT-WEIGHT: bold;}
.gongguing_price1{font-family:"verdana", "돋움"; font-size:20px; color:#30AFFE; letter-spacing:-1px;FONT-WEIGHT: bold;}
.gongguing_text{font-family:"돋움"; font-size:11px; color:#FFCC00; letter-spacing:-1px;FONT-WEIGHT: bold;}
.gongguing_text1{font-family:"돋움"; font-size:11px; color:#8C9299; letter-spacing:-1px;}
.gongguing_end_text1{font-family:"돋움"; font-size:14px; color:#868e90; letter-spacing:-1px;FONT-WEIGHT: bold;line-height:20px;}
.gongguing_end_text2{font-family:"돋움"; font-size:12px; color:#868e90; letter-spacing:-1px;FONT-WEIGHT: bold;line-height:20px;}
.gongguing_end_text3{font-family:"돋움"; font-size:11px; color:#2FAFC3; letter-spacing:-1px;line-height:20px;}
.gongguing_end_price1{font-family:"verdana","돋움"; font-size:12px; color:#A7A7A7; letter-spacing:-1px;FONT-WEIGHT: bold;line-height:20px;}
.gongguing_end_price2{font-family:"verdana","돋움"; font-size:12px; color:#2FAFC3; letter-spacing:-1px;FONT-WEIGHT: bold;line-height:20px;}
.gongguing_end_date{font-family:"verdana", "돋움"; font-size:18px; color:#AAAAAA; letter-spacing:-1px;FONT-WEIGHT: bold;}
.gongguing_order_id{font-family:"verdana","돋움"; font-size:12px; color:#484848; letter-spacing:-1px;FONT-WEIGHT: bold;line-height:20px;}
.gongguing_order_date{font-family:"verdana", "돋움"; font-size:10px; color:#AAAAAA; letter-spacing:-1px;line-height:20px;}
.gongguing_order_order{font-family:"verdana", "돋움"; font-size:30px; color:#AAAAAA; letter-spacing:-1px;FONT-WEIGHT: bold;line-height:20px;}
.discount_png_wrap {position:absolute; left:477px; top:230px; z-index:999; width:113px; height:112px;background:url('../images/design/gonggu_sale.png') no-repeat;text-align:center;padding:45px 0;}
.discount_png {font-family:"verdana", "돋움"; font-size:27px; color:#ffffff; letter-spacing:-1px;FONT-WEIGHT: bold;}

.table_td {font-family:돋움; font-size:11px; color:#929292; letter-spacing:-1px; line-height:20px;}
.gongguBest ul {margin:0;}
.gongguBest ul li {float:left;width:135px;text-align:center;}


/* sns보내기 팝업창 */
.speechbubble_title{font-family:돋움; font-size:11px; color:#929292; letter-spacing:-1px; line-height:17px;padding-top:7px;padding-left:7px;}
.speechbubble_con{font-family:돋움; font-size:11px; color:#929292; letter-spacing:-1px; line-height:17px;padding:7px;}
.speechbubble_count{font-family:돋움; font-size:11px; color:#929292; letter-spacing:0px; line-height:17px;padding-top:7px;padding-left:7px;}
.speechbubble_close{font-family:돋움; font-size:11px; color:#929292; letter-spacing:0px; line-height:17px;padding-top:7px;padding-right:7px;}


/* 장바구니 */
.basket_speed_title {font-family:돋움; font-size:11px; color:#999999; letter-spacing:-1px; padding-top:3px; padding-left:15px;}
.basket_use_info {color:#555555; font-family:돋움; font-size:11px; letter-spacing:-0.5px; word-break:break-all; font-weight:bold; line-height:130%; padding:5px 15px 0px 0px;}
.basket_pro_option {font-size:11px; letter-spacing:-0.5px; word-break:break-all;}
.basket_list_title {font-family:돋움; font-size:12px; color:#444444; letter-spacing:-0.5px; font-weight:bold; height:30px; text-align:center; background-color:#F8F8F8;}
.basket_total_price {color:ee0a02; font-family:verdana; font-size:20px; line-height:22px; font-weight:bold;}
.basket_etc_price {font-family:tahoma,verdana; font-size:18px; line-height:22px; font-weight:bold;}
.basket_etc_price2 {font-family:tahoma,verdana; color:#; font-size:18px; line-height:22px; font-weight:bold;}
.basket_etc_price3 {font-family:tahoma,verdana; color:#ff3300; font-size:18px; line-height:22px; font-weight:bold;}

.itemListTbl {margin:0px; padding:0px;}
.itemListTbl .thstyle {background:#f5f5f5; height:36px; color:#868e90; border-top:2px solid #6b6b6b; border-bottom:1px solid #d1d1d1;}
.itemListTbl .tdstyle {border-right:1px solid #e5e5e5; border-bottom:1px solid #e5e5e5; padding:10px 0px;}
.itemListTbl .tdstyle2 { border-bottom:1px solid #e5e5e5;}


/* 마이페이지 */
.mypagetmenu {width:100%;}
.mypagetmenu td {width:10%;text-align:center;border-top:1px solid #dddddd;border-bottom:1px solid #dddddd;background:url('/images/common/line_gray.gif') repeat-y;background-position:100% 0px;}
.mypagetmenu .leftline {background:url('/images/common/line_gray.gif') repeat-y;}
.mypagetmenu a{display:block; padding:6px 0px;}
.mypagetmenu .nowMyage{background:#0082f0;}
.mypagetmenu .nowMyage a{color:#ffffff;font-weight:600;}
.mypagemembergroup {height:36px; text-align:left; border:1px solid #dddddd; border-bottom:none; background:#ffffff url('/images/common/mypage/001/mypage_meminfo_bg.gif') no-repeat; background-position:100% 0px;}
.mypagemembergroup .groupinfotext {float:left; margin:9px 15px;}
.mypagemembergroup .groupinfotext .st1 {font-weight:bold; color:#666666;}
.mypagemembergroup .groupinfotext .st2 {font-weight:bold; color:#ff4400;}
.mypagemembergroup .gruopinfogo {float:left;}
.mypagemembergroup .gruopinfogo a{ font-size:11px; height:36px; line-height:36px; font-weight:700;}

.pointfaq h3 {padding-bottom:15px; padding-left:36px; line-height:29px; color:#53585b; font-size:18px; background:url('/images/003/no3.gif') no-repeat;}
.pointfaq h4 {font-size:16px; color:#51595b; padding-bottom:5px;}
.pointfaq p {padding-bottom:25px;}

.snshongboinfo h4 {padding-left:36px; line-height:29px; color:#53585b; font-size:18px; background:url('/images/003/no2.gif') no-repeat;}
.snshongboinfo .addpoint {background:#f5f7f6; margin:10px 0px 20px 0px; padding:25px 50px; font-size:20px; line-height:28px;}
.snschannel h4 {font-size:16px; padding-bottom:5px;}
.urlhongboinfo h4 {padding-bottom:10px; padding-left:36px; line-height:29px; color:#53585b; font-size:18px; background:url('/images/003/no3.gif') no-repeat;}

.mypage_mem_info {color:#ff4c00; font-weight:bold;}
.mypage_list_title {color:#444444; letter-spacing:-0.5px; font-weight:bold; margin-bottom:5px;}
.mypage_list_cont {color:#888888; letter-spacing:-0.5px; padding-left:15px;}
.mypage_list_cont2 {color:#888888; letter-spacing:-0.5px;}
.mypage_order_line {border-right:1px solid #e5e5e5; padding-bottom:10px;}
.mypage_order_line2 {border-bottom:1px solid #e5e5e5; color:#888888; font-size:12px; letter-spacing:-0.5px; padding-left:15px;}

/* 마이페이지 주문 현황/반품환불 현황 */
.myOrderTbl {width:100%; height:50px; background:#f5f5f5; color:#ffffff; border:1px solid #cccccc;}
.myOrderTbl th {width:110px; padding-left:10px; background:#aaaaaa; color:#ffffff; text-align:left; font-family:NanumGothic,나눔고딕;}
.myOrderTbl td {width:14%; padding:8px 0px; text-align:center; border-right:1px solid #e5e5e5;}
.myOrderTbl td strong {font-size:18px; font-family:arial; color:#444444; font-weight:bold; line-height:24px;}

/* 마이페이지 정보수신현황(SMS/EMAIL) */
.recInfoDivLeft {float:left; width:49%; border:1px solid #e2e2e2; border-top:1px solid #333333;}
.recInfoDivRight {float:right; width:49%; border:1px solid #e2e2e2; border-top:1px solid #333333;}


/* 주문서 */
.st02_1{font-size:12px; BORDER-RIGHT: #DCDCDC 1px solid; BORDER-TOP: #C7C1C1 1px solid; BORDER-LEFT: #C7C1C1 1px solid; BORDER-BOTTOM: #DCDCDC 1px solid; HEIGHT:18px; BACKGROUND-COLOR: #F7F7F7;padding-top:2px; padding-bottom:1px;}


/* 갤러리형 상품 하나당 정보 출력 박스(메인+상품목록 페이지) */
.prInfoBox {border:0px solid #eeeeee;}
.prInfoBox .prImage {height:200px; padding:0px;}
.prInfoBox .discount {color:#fd0000; font-family:tahoma,arial;}
.prInfoBox .prmsgArea {height:26px; padding-top:5px; color:#6699cc; font-size:12px; line-height:125%;}

/* 리스트형 상품 하나당 정보 출력 박스(메인+상품목록 페이지) */
.prInfoBox2 {width:100%;}
.prinfoBox2 .prImage {border:1px solid #eeeeee;}
.prInfoBox2 .discount {color:#ff5500; font-family:tahoma,arial; font-size:15px; padding-left:12px;}
.prInfoBox2 .prmsgArea {color:#999999;}

/* 상품목록 네임텍 박스(메인+상품목록 페이지) */
.nameTagBox {padding:5px 7px; border-top:1px solid #eeeeee; background:#f9f9f9;}
.nameTagBox .name {font-size:11px; font-weight:bold;}
.nameTagBox .owner {font-size:11px; color:#999999;}

/* 상품목록 리스트형 네임텍 박스(메인+상품목록 페이지) */
.nameTagBox2 {margin-top:5px;}
.nameTagBox2 .name {font-size:11px; font-weight:bold;}
.nameTagBox2 .owner {font-size:11px; color:#999999;}

/* 상품목록 상품 정렬방식 선택 */
.prSortType {float:left; list-style:none; margin-top:3px; padding:px;}
.prSortType li {float:left; padding:0px 10px; background:url('/images/common/line_gray.gif') no-repeat; background-position:100% 3px;}
.prSortType .last {float:left; padding:0px 10px; background:none;}
.prSortType li a {display:block;}
.prSortType li .sortOn {font-weight:bold;}

/* 카테고리그룹 출력 */
.cateName {padding-bottom:10px; color:#222222; font-size:15px; font-weight:600;}
.subCategoryBox {width:100%; background:#dddddd; margin-bottom:15px;}
.subCategoryBox caption {display:none;}
.subCategoryBox td {width:20%; background:#ffffff;}
.subCategoryBox td a{display:block; padding:8px 12px;}
.subCategoryBox td.selCategory{font-weight:bold; color:#fff; background:#0082f0;}
.subCategoryBox td.selCategory a{color:#fff;}
.subCategoryBox td .citemproductcnt{ font-weight:normal;}

/* 상품 상세페이지 템플릿(detail_AD001) */
.prdetailname {color:#333333; font-family:돋움; font-size:16px; line-height:22px; font-weight:bold; word-break:break-all;}
.prdetailmsg {margin-top:8px; color:#999999; font-size:11px; letter-spacing:-1px;}

/* 상품 상세페이지 탭부분(상세정보/관련상품/Q&A 등) */
.prDetailTab {width:100%; height:35px;}
.prDetailTab a {letter-spacing:-1px;}
.prDetailTab .prDetailTabOn {width:10%; border:2px solid #222222; border-bottom:none; text-align:center; font-weight:700;}
.prDetailTab .prDetailTabOn a {color:#222222;}
.prDetailTab .prDetailTabOff {width:10%; border:1px solid #dddddd; border-bottom:2px solid #222222; border-left:none; text-align:center; background:#f9f9f9;}
.prDetailTab .prDetailTabOff2 {width:10%; border:1px solid #dddddd; border-bottom:2px solid #222222; border-right:none; text-align:center; background:#f9f9f9;}
.prDetailTab .prDetailTabOff, .prDetailTab .prDetailTabOff2 a {color:#888888;}
.prDetailTab .prDetailTabNull {width:5%; border-bottom:2px solid #222222;}


/* 상품 상세페이지 상품평 작성 */
.reviewMarkTbl {margin:0px; padding:0px; width:100%; border-top:1px solid #444444;}
.reviewMarkTbl th {width:100px; padding:8px 0px 6px 15px; text-align:left; background:#f9f9f9; border-bottom:1px solid #e9e9e9; color:#444444; font-size:11px;}
.reviewMarkTbl td {padding:4px 5px; border-bottom:1px solid #e9e9e9;}

.reviewWriteTbl {margin:0px; padding:0px; width:100%;}
.reviewWriteTbl th {width:100px; padding:8px 0px 6px 15px; text-align:left; font-weight:500; background:#f9f9f9; border-bottom:1px solid #e9e9e9; font-size:11px;}
.reviewWriteTbl td {padding:4px 5px; border-bottom:1px solid #e9e9e9; font-size:11px; letter-spacing:-0.5pt; line-height:15px;}
.reviewWriteTbl input {width:40%;}

.reviewInfoDiv {padding:10px 15px; color:#888888; font-size:11px; letter-spacing:-1px; border-bottom:1px solid #444444;}

/* 상품 상세페이지 입점사 정보 출력 */
.venderInfo {border:1px solid #eeeeee; margin-top:20px;}
.venderInfoTbl {width:100%; margin-top:7px;}
.venderInfoTbl caption {display:none;}
.venderInfoTbl th {text-align:left; font-weight:600; font-size:11px;}
.venderInfoTbl td {color:#999999; font-size:11px;  padding:4px 0px;}


/* 쇼핑혜택 */
.memberbenefit {text-align:left;}
.memberbenefit h2 {margin-top:23px; margin-bottom:20px; padding-left:18px; color:#444444; font-size:24px; font-weight:500; line-height:24px; border-left:1px solid #dddddd;}
.memberbenefit .benefitmenu table {border:1px solid #eeeeee; border-right:none; margin-bottom:40px;}
.memberbenefit .benefitmenu td {text-align:center;border-right:1px solid #eeeeee;}
.memberbenefit .benefitmenu a:link {display:block; padding:15px 0px; color:#656e70; font-weight:700; font-size:14px;}
.memberbenefit .benefitmenu a:hover {display:block; padding:15px 0px; font-weight:700; font-size:14px;}
.memberbenefit .benefitmenu .nowon {background:#fef5cc;}

.memberbenefit .allcouponlist h3 {line-height:30px; color:#333333; font-size:22px; letter-spacing:-1px;}
.memberbenefit .productgift h4 {padding-bottom:15px; line-height:30px; color:#333333; font-size:22px; letter-spacing:-1px;}
.memberbenefit .productgift p {padding-bottom:10px;}
.memberbenefit .attendance {margin-bottom:15px;}
.memberbenefit .attendance h4 {padding-bottom:15px; line-height:30px; color:#333333; font-size:22px; letter-spacing:-1px;}
.memberbenefit .urlhongbo {margin-bottom:10px;}
.memberbenefit .urlhongbo h3 {padding-bottom:25px; color:#333333; font-size:22px; letter-spacing:-1px;}
.memberbenefit .urlhongbo h4 {padding-left:36px; line-height:29px; color:#53585b; font-size:18px; background:url('/images/003/no1.gif') no-repeat;}
.memberbenefit .storytalk {margin-bottom:20px;}
.memberbenefit .storytalk h3 {padding-bottom:25px; color:#333333; font-size:22px; letter-spacing:-1px;}


.button {
	float:left;
	width:95px;
	height:23px;
	line-height:18px;
	cursor: pointer;
	text-align: center;
	font-size:11px;
	letter-spacing:-1px;
	padding-top:4px;
	margin-right:3px;
}

.tabOff {
	background:url('/images/common/product/AD001/pdetail_skin_reviewbt.gif') no-repeat;
}
.tabOff a:link {display:block; color:#4d4d4d; text-decoration: none;}
.tabOff a:active {display:block; color:#4d4d4d; text-decoration: none;}
.tabOff a:hover {display:block; color:#4d4d4d; text-decoration: none;}
.tabOff a:visited {display:block; color:#4d4d4d; text-decoration: none;}

.tabOn {
	font-weight:bold;
	background:url('/images/common/product/AD001/pdetail_skin_reviewbt_on.gif') no-repeat;
}
.tabOn a:link {display:block; color:#ff541c; text-decoration: none;}
.tabOn a:active {display:block; color:#ff541c; text-decoration: none;}
.tabOn a:hover {display:block; color:#ff541c; text-decoration: none;}
.tabOn a:visited {display:block; color:#ff541c; text-decoration: none;}

/* 현재위치 */
	.currentTitle {clear:both;}
	.currentTitle .titleimage {float:left; margin-top:25px; font-size:18px; font-weight:600; color:#333333;}
	.currentTitle .current {float:right; font-size:11px; margin-top:25px;}
	.currentTitle .current .nowCurrent {font-weight:700;}

<?
	$array_menu[0]=array("leftprname","leftcommunity","leftcustomer");
	$array_menu[1]=array("mainprname","mainprprice","mainspname","mainspprice","mainnotice","maininfo","mainpoll","mainboard","mainconprice","mainreserve","maintag","mainproduction","mainselfcode");
	$array_menu[2]=array("choicecodename","upcodename","subcodename","prname","prprice","prmadein","prproduction","prconsumerprice","prreserve","prtag","praddcode","prsort","choiceprsort","prlist","choiceprlist","prselfcode");

	if(strlen($_data->css)==0) {
		$sql = "SELECT * FROM tbltempletinfo WHERE icon_type='".$_shopdata->icon_type."' ";
		$styleresult=mysql_query($sql,get_db_conn());
		$stylerow=mysql_fetch_object($styleresult);
		
		$_data->css=$stylerow->default_css;
		mysql_free_result($styleresult);
	}

	if(strlen($_data->css)==0) {
		for($i=0;$i<count($array_menu[0]);$i++) {
			$_data->css.="돋움,";
			$_data->css.="11px,";
			$_data->css.="normal,";
			$_data->css.=",";
			$_data->css.=",";
		}
		$_data->css=substr($_data->css,0,-1)."";
		for($i=0;$i<count($array_menu[1]);$i++) {
			$_data->css.="돋움,";
			$_data->css.="11px,";
			$_data->css.="normal,";
			$_data->css.=",";
			$_data->css.=",";
		}
		$_data->css=substr($_data->css,0,-1)."";
		for($i=0;$i<count($array_menu[2]);$i++) {
			$_data->css.="돋움,";
			$_data->css.="11px,";
			$_data->css.="normal,";
			$_data->css.=",";
			$_data->css.=",";
		}
		$_data->css=substr($_data->css,0,-1);
	}
	$array_val=explode("",$_data->css);

	$z=0;
	$k=0;
	$value=explode(",",$array_val[$z]);
	for($i=0;$i<count($array_menu[$z]);$i++) {
		echo ".".$array_menu[$z][$i]." {font-family:".$value[$k++]."; font-size:".$value[$k++]."; font-weight:".$value[$k++]."; ";
		if(strlen($value[$k])>0) echo "text-decoration:".$value[$k]."; ";
		$k++;
		if(strlen($value[$k])>0) echo "color:".$value[$k]."; ";
		$k++;
		echo "}\n";
	}

	$z=1;
	$k=0;
	$value=explode(",",$array_val[$z]);
	for($i=0;$i<count($array_menu[$z]);$i++) {
		echo ".".$array_menu[$z][$i]." {font-family:".$value[$k++]."; font-size:".$value[$k++]."; font-weight:".$value[$k++]."; ";
		if(strlen($value[$k])>0) echo "text-decoration:".$value[$k]."; ";
		$k++;
		if(strlen($value[$k])>0) echo "color:".$value[$k]."; ";
		$k++;
		echo "}\n";
	}

	$z=2;
	$k=0;
	$value=explode(",",$array_val[$z]);
	for($i=0;$i<count($array_menu[$z]);$i++) {
		echo ".".$array_menu[$z][$i]." {font-family:".$value[$k++]."; font-size:".$value[$k++]."; font-weight:".$value[$k++]."; ";
		if(strlen($value[$k])>0) echo "text-decoration:".$value[$k]."; ";
		$k++;
		if(strlen($value[$k])>0) echo "color:".$value[$k]."; ";
		$k++;
		echo "}\n";
	}
?>


/*추가 페이징 */
	.addpaging {border:0px solid black;}
	.addpaging a {display:inline-block; border:1px solid #999999; width:26px; height:26px; line-height:26px; margin:0px 2px;}
	.addpaging .addpaging_jump {width:30px;}

/* 공통 pageing */
	.pageingarea{} /* 페이지 ui 컨테이너 */	
	.pageingarea a{padding:0px; margin:0px;}	
	.pageingarea .blockPageBtn{ border:0px;  width:26px; height:26px; display:inline-block;vertical-align: top;} /* 처음 페이지등 특수 버튼 */
	.pageingarea .pageitem{border:1px solid #e4e2e3; background:#ffffff; color :#9a9a9a; width:26px; height:26px; padding-top:5px; text-align:center; cursor:pointer; display:inline-block;vertical-align: top;} /* 일반 페이지 */
	.pageingarea .currpageitem{margin-left:2px; margin-right:2px; border:1px solid #ec4024;  background:#fff; color : #ec4024;width:26px; height:25px; padding-top:3px; text-align:center; display:inline-block;vertical-align: top;} /* 현페이지 */
</style>

<link rel="stylesheet" type="text/css" href="/css/common.css">