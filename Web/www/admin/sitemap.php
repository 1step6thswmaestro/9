<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
INCLUDE ("access.php");

$curdate = date("Ymd");
?>

<? INCLUDE ("header.php"); ?>
<script>try {parent.topframe.ChangeMenuImg(0);}catch(e){}</script>
<style>td	{font-family:"굴림,돋움";color:#4B4B4B;font-size:12px;line-height:17px;}</style>
<script type="text/javascript" src="lib.js.php"></script>
<SCRIPT LANGUAGE="JavaScript">
<!--
//-->
</SCRIPT>

<table cellpadding="0" cellspacing="0" width="980" style="table-layout:fixed">
<col width=10></col>
<col width=></col>
<tr>
	<td valign="top"></td>
	<td valign="top">
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td height="29">
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td height="28" class="link" align="right"><img src="images/top_link_house.gif" border="0" valign=absmiddle>현재위치 : <a href="http://">사이트맵</a></td>
		</tr>
		<tr>
			<td><img src="images/top_link_line.gif" width="100%" height="1" border="0"></td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td height="10"></td>
		</tr>
		<tr>
			<td>
			<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
			<TR>
				<TD><IMG SRC="images/sitemap_title.gif" border="0"></TD>
				</tr><tr>
				<TD width="100%" background="images/title_bg.gif" height="20"></TD>
			</TR>
			</TABLE>
			</td>
		</tr>
		<tr>
			<td height="20"></td>
		</tr>
		<tr>
			<td>
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td valign="top"><a href="javascript:parent.topframe.GoMenu(1,'shop_index.php');"><img src="images/sitemap_img01.gif" border="0" hspace="5"></a></td>
				<td width="100%" valign="top">
				<table cellpadding="0" cellspacing="0" width="100%">
				<col width="20%"></col>
				<col width="20%"></col>
				<col width="20%"></col>
				<col width="20%"></col>
				<col width="20%"></col>
				<tr>
					<td valign="top">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td class="font_blue1a"><img src="images/icon_point.gif" border="0">상점 기본정보 설정</td>
					</tr>
					<tr>
						<td style="padding-top:5px; padding-left:15px;" class="font_size1">
						· <A href="javascript:parent.topframe.GoMenu(1,'shop_basicinfo.php');">상점 기본정보 관리</A><br>
						· <A href="javascript:parent.topframe.GoMenu(1,'shop_keyword.php');">브라우저 타이틀/키워드</A><br>
						· <A href="javascript:parent.topframe.GoMenu(1,'shop_mainintro.php');">메인 타이틀이미지 디자인</A><br>
						· <A href="javascript:parent.topframe.GoMenu(1,'shop_companyintro.php');">회사 소개/약도</A><br>
						· <A href="javascript:parent.topframe.GoMenu(1,'shop_agreement.php');">쇼핑몰 이용약관</A><br>
						· <A href="javascript:parent.topframe.GoMenu(1,'shop_privercyinfo.php');">쇼핑몰 개인정보취급방침</a>
						</td>
					</tr>
					</table>
					</td>
					<td valign="top">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td class="font_blue1a"><img src="images/icon_point.gif" border="0">쇼핑몰 환경 설정</td>
					</tr>
					<tr>
						<td style="padding-top:5px; padding-left:15px;" class="font_size1">
						· <A href="javascript:parent.topframe.GoMenu(1,'shop_openmethod.php');">업종별 운영방식 설정</A><br>
						· <A href="javascript:parent.topframe.GoMenu(1,'shop_displaytype.php');">프레임/정렬 설정</A><br>
						· <A href="javascript:parent.topframe.GoMenu(1,'shop_layout.php');">쇼핑몰 레이아웃 설정</A><br>
						· <A href="javascript:parent.topframe.GoMenu(1,'shop_mainproduct.php');">상품 진열수/화면설정</A><br>
						· <A href="javascript:parent.topframe.GoMenu(1,'shop_productshow.php');">상품 진열 기타 설정</A><br>
						· <A href="javascript:parent.topframe.GoMenu(1,'shop_mainleftinform.php');">왼쪽 고객 알림 디자인</A><br>
						· <A href="javascript:parent.topframe.GoMenu(1,'shop_logobanner.php');">로고/배너 관리</A><br>
						· <A href="javascript:parent.topframe.GoMenu(1,'shop_orderform.php');">회원가입/주문 안내문구</A><br>
						· <A href="javascript:parent.topframe.GoMenu(1,'shop_ssl.php');">SSL(보안서버) 기능 설정</A><br>
						· <A href="javascript:parent.topframe.GoMenu(1,'shop_bizsiren.php');">실명인증 정보 설정</A>
						</td>
					</tr>
					</table>
					</td>
					<td valign="top">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td class="font_blue1a"><img src="images/icon_point.gif" border="0">쇼핑몰 운영 설정</td>
					</tr>
					<tr>
						<td style="padding-top:5px; padding-left:15px;" class="font_size1">
						· <A href="javascript:parent.topframe.GoMenu(1,'shop_tag.php');">상품태그 관련 기능설정</A><br>
						· <A href="javascript:parent.topframe.GoMenu(1,'shop_search.php');">상품검색 관련 기능설정</A><br>
						· <A href="javascript:parent.topframe.GoMenu(1,'shop_reserve.php');">적립금/쿠폰 기능설정</A><br>
						· <A href="javascript:parent.topframe.GoMenu(1,'shop_recommand.php');">추천인 제도 설정</A><br>
						· <A href="javascript:parent.topframe.GoMenu(1,'shop_member.php');">회원가입 관련 설정</A><br>
						· <A href="javascript:parent.topframe.GoMenu(1,'shop_deli.php');">상품 배송관련 기능설정</A><br>
						· <A href="javascript:parent.topframe.GoMenu(1,'shop_payment.php');">상품 결제관련 기능설정</A><br>
						· <A href="javascript:parent.topframe.GoMenu(1,'shop_escrow.php');">에스크로 결제관련 설정</A><br>
						· <A href="javascript:parent.topframe.GoMenu(1,'shop_return.php');">상품 반품/환불 기능설정</A><br>
						· <A href="javascript:parent.topframe.GoMenu(1,'shop_basket.php');">장바구니 관련 기능설정</A><br>
						</td>
					</tr>
					</table>
					</td>
					<td valign="top">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td style="padding-top:19px; padding-left:15px;" class="font_size1">
						· <A href="javascript:parent.topframe.GoMenu(1,'shop_review.php');">상품리뷰(후기) 설정</A><br>
						· <A href="javascript:parent.topframe.GoMenu(1,'shop_estimate.php');">상품 견적서 기능설정</A><br>
						· <A href="javascript:parent.topframe.GoMenu(1,'shop_snsinfo.php');">SNS 및 홍보적립금 설정</A>
						</td>
					</tr>
					</table>
					</td>
					<td valign="top">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td class="font_blue1a"><img src="images/icon_point.gif" border="0">보안설정</td>
					</tr>
					<tr>
						<td style="padding-top:5px; padding-left:15px;" class="font_size1">
						· <A href="javascript:parent.topframe.GoMenu(1,'shop_rolelist.php');">그룹 및 권한 설정</A><br>
						· <A href="javascript:parent.topframe.GoMenu(1,'shop_adminlist.php');">운영자/부운영자 설정</A><br>
						· <A href="javascript:parent.topframe.GoMenu(1,'shop_iplist.php');">접근IP 설정</A><br>
						· <A href="javascript:parent.topframe.GoMenu(1,'shop_changeadminpasswd.php');">패스워드 변경</A>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td height="50" colspan="2"><hr noshade size="1" color="#DEDEDE"></td>
			</tr>
			<tr>
				<td valign="top"><A href="javascript:parent.topframe.GoMenu(2,'design_index.php');"><img src="images/sitemap_img02.gif" border="0" hspace="5"></a></td>
				<td width="100%" valign="top">
				<table cellpadding="0" cellspacing="0" width="100%">
				<col width="20%"></col>
				<col width="20%"></col>
				<col width="20%"></col>
				<col width="20%"></col>
				<col width="20%"></col>
				<tr>
					<td valign="top">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td class="font_blue1a"><img src="images/icon_point.gif" border="0">웹FTP, 개별적용 선택</td>
					</tr>
					<tr>
						<td style="padding-top:5px; padding-left:15px;" class="font_size1">
						· <A href="javascript:parent.topframe.GoMenu(2,'design_webftp.php');">웹FTP/웹FTP팝업</A><br>
						· <A href="javascript:parent.topframe.GoMenu(2,'design_option.php');">개별디자인 적용선택</A>
						</td>
					</tr>
					</table>
					</td>
					<td valign="top">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td class="font_blue1a"><img src="images/icon_point.gif" border="0">템플릿-메인 및 카테고리</td>
					</tr>
					<tr>
						<td style="padding-top:5px; padding-left:15px;" class="font_size1">
						· <A href="javascript:parent.topframe.GoMenu(2,'design_adultintro.php');">성인몰 인트로 템플릿</A><br>
						· <A href="javascript:parent.topframe.GoMenu(2,'design_main.php');">메인화면 템플릿</A><br>
						· <A href="javascript:parent.topframe.GoMenu(2,'design_bottom.php');">쇼핑몰 하단 템플릿</A><br>
						· <A href="javascript:parent.topframe.GoMenu(2,'design_plist.php');">상품 카테고리 템플릿</A><br>
						· <A href="javascript:parent.topframe.GoMenu(2,'design_pdetail.php');">상품 상세화면 템플릿</A>
						</td>
					</tr>
					</table>
					</td>
					<td valign="top">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td class="font_blue1a"><img src="images/icon_point.gif" border="0"><span style="letter-spacing:-1.5pt;">개별디자인-메인 및 카테고리</span></td>
					</tr>
					<tr>
						<td style="padding-top:5px; padding-left:15px;" class="font_size1">
						· <A href="javascript:parent.topframe.GoMenu(2,'design_eachintropage.php');">인트로 화면 꾸미기</A><br>
						· <A href="javascript:parent.topframe.GoMenu(2,'design_eachtitleimage.php');">타이틀 이미지 관리</A><br>
						· <A href="javascript:parent.topframe.GoMenu(2,'design_eachtopmenu.php');">상단메뉴 꾸미기</A><br>
						· <A href="javascript:parent.topframe.GoMenu(2,'design_eachleftmenu.php');">왼쪽메뉴 꾸미기</A><br>
						· <A href="javascript:parent.topframe.GoMenu(2,'design_eachmain.php');">메인 본문 꾸미기</A><br>
						· <A href="javascript:parent.topframe.GoMenu(2,'design_eachbottom.php');">하단화면 꾸미기</A><br>
						· <A href="javascript:parent.topframe.GoMenu(2,'design_eachloginform.php');">로그인폼 꾸미기</A><br>
						· <A href="javascript:parent.topframe.GoMenu(2,'design_eachplist.php');">상품 카테고리 꾸미기</A><br>
						· <A href="javascript:parent.topframe.GoMenu(2,'design_eachpdetail.php');">상품상세 화면 꾸미기</A><br>
						· <A href="javascript:parent.topframe.GoMenu(2,'design_tag.php');">태그 화면 템플릿</A><br>
						</td>
					</tr>
					</table>
					</td>
					<td valign="top">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td class="font_blue1a"><img src="images/icon_point.gif" border="0">템플릿-페이지 본문</td>
					</tr>
					<tr>
						<td style="padding-top:5px; padding-left:15px;" class="font_size1">
						· <A href="javascript:parent.topframe.GoMenu(2,'design_tag.php');">태그 화면 템플릿</A><br>
						· <A href="javascript:parent.topframe.GoMenu(2,'design_section.php');">섹션상품 화면 템플릿</A><br>
						· <A href="javascript:parent.topframe.GoMenu(2,'design_search.php');">상품검색 결과화면 템플릿</A><br>
						· <A href="javascript:parent.topframe.GoMenu(2,'design_useinfo.php');">이용안내 화면 템플릿</A><br>
						· <A href="javascript:parent.topframe.GoMenu(2,'design_memberjoin.php');">회원가입 화면 템플릿</A><br>
						· <A href="javascript:parent.topframe.GoMenu(2,'design_usermodify.php');">회원수정 화면 템플릿</A><br>
						· <A href="javascript:parent.topframe.GoMenu(2,'design_login.php');">로그인 관련 화면 템플릿</A><br>
						· <A href="javascript:parent.topframe.GoMenu(2,'design_basket.php');">장바구니 화면 템플릿</A><br>
						· <A href="javascript:parent.topframe.GoMenu(2,'design_order.php');">주문서 화면 템플릿</A><br>
						· <A href="javascript:parent.topframe.GoMenu(2,'design_mypage.php');">MYPAGE 화면 템플릿</A><br>
						· <A href="javascript:parent.topframe.GoMenu(2,'design_orderlist.php');">주문리스트 화면 템플릿</A><br>
						· <A href="javascript:parent.topframe.GoMenu(2,'design_wishlist.php');">WishList 화면 템플릿</A>
						</td>
					</tr>
					</table>
					</td>
					<td valign="top">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td style="padding-top:19px; padding-left:15px;" class="font_size1">
						
						· <A href="javascript:parent.topframe.GoMenu(2,'design_mycoupon.php');">쿠폰 화면 템플릿</A><br>
						· <A href="javascript:parent.topframe.GoMenu(2,'design_myreserve.php');">적립금 화면 템플릿</A><br>
						· <A href="javascript:parent.topframe.GoMenu(2,'design_mypersonal.php');">1:1고객문의 화면 템플릿</A><br>
						<? if(getVenderUsed()==true) { ?>· <A href="javascript:parent.topframe.GoMenu(2,'design_mypersonal.php');">단골매장 화면 템플릿</A><br> <? } ?>
						· <A href="javascript:parent.topframe.GoMenu(2,'design_sendmail.php');">메일관련 화면 템플릿</A><br>
						· <A href="javascript:parent.topframe.GoMenu(2,'design_popupnotice.php');">공지사항 팝업창 템플릿</A><br>
						· <A href="javascript:parent.topframe.GoMenu(2,'design_popupinfo.php');">정보 팝업창 템플릿</A><br>
						· <A href="javascript:parent.topframe.GoMenu(2,'design_formmail.php');">폼메일 템플릿</A><br>
						· <A href="javascript:parent.topframe.GoMenu(2,'design_cardtopimg.php');">카드결제창 로고</A><br>
						· <A href="javascript:parent.topframe.GoMenu(2,'design_blist.php');">상품브랜드 화면 템플릿</A><br>
						· <A href="javascript:parent.topframe.GoMenu(2,'design_bmap.php');">브랜드맵 화면 템플릿</A>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				<tr>
					<td height="17" colspan="5"></td>
				</tr>
				<tr>
					<td height="15" colspan="5" background="images/sitemap_line_bg.gif"></td>
				</tr>
				<tr>
					<td height="10" colspan="5"></td>
				</tr>
				<tr>
					<td valign="top">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td class="font_blue1a"><img src="images/icon_point.gif" border="0">개별디자인-페이지 본문</td>
					</tr>
					<tr>
						<td style="padding-top:5px; padding-left:15px;" class="font_size1">
						· <A href="javascript:parent.topframe.GoMenu(2,'design_eachbottomtools.php');">하단 폴로메뉴 화면 꾸미기</A><br>
						· <A href="javascript:parent.topframe.GoMenu(2,'design_eachtag.php');">태그 화면 꾸미기</A><br>
						· <A href="javascript:parent.topframe.GoMenu(2,'design_eachsection.php');">상품섹션 화면 꾸미기</A><br>
						· <A href="javascript:parent.topframe.GoMenu(2,'design_eachsearch.php');">상품검색 결과화면 꾸미기</A><br>
						· <A href="javascript:parent.topframe.GoMenu(2,'design_eachbasket.php');">장바구니 화면 꾸미기</A><br>
						· <A href="javascript:parent.topframe.GoMenu(2,'design_eachprimageview.php');">상품이미지 확대창 꾸미기</A><br>
						· <A href="javascript:parent.topframe.GoMenu(2,'design_eachpopupnotice.php');">공지사항 팝업창 꾸미기</A><br>
						· <A href="javascript:parent.topframe.GoMenu(2,'design_eachpopupinfo.php');">정보 팝업창 꾸미기</A><br>
						· <A href="javascript:parent.topframe.GoMenu(2,'design_eachsendmail.php');">메일 화면 꾸미기</A><br>
						· <A href="javascript:parent.topframe.GoMenu(2,'design_eachformmail.php');">폼메일 화면 꾸미기</A><br>
						· <A href="javascript:parent.topframe.GoMenu(2,'design_eachboardtop.php');">게시판 상단 화면 꾸미기</A><br>
						· <A href="javascript:parent.topframe.GoMenu(2,'design_eachuseinfo.php');">이용안내 화면 꾸미기</A>
						</td>
					</tr>
					</table>
					</td>
					<td valign="top">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td style="padding-top:19px; padding-left:15px;" class="font_size1">
						· <A href="javascript:parent.topframe.GoMenu(2,'design_eachagreement.php');">이용약관 화면 꾸미기</A><br>
						· <A href="javascript:parent.topframe.GoMenu(2,'design_eachjoinagree.php');">회원가입 약관화면 꾸미기</A><br>
						· <A href="javascript:parent.topframe.GoMenu(2,'design_eachmemberjoin.php');">회원가입 입력폼 꾸미기</A><br>
						· <A href="javascript:parent.topframe.GoMenu(2,'design_eachusermodify.php');">회원수정화면 꾸미기</A><br>
						· <A href="javascript:parent.topframe.GoMenu(2,'design_eachiddup.php');">회원ID체크 화면 꾸미기</A><br>
						· <A href="javascript:parent.topframe.GoMenu(2,'design_eachfindpwd.php');">패스워드 분실 화면 꾸미기</A><br>
						· <A href="javascript:parent.topframe.GoMenu(2,'design_eachlogin.php');">로그인 화면 꾸미기</A><br>
						· <A href="javascript:parent.topframe.GoMenu(2,'design_eachmemberout.php');">회원탈퇴 화면 꾸미기</A><br>
						· <A href="javascript:parent.topframe.GoMenu(2,'design_eachmypage.php');">MYPAGE 화면 꾸미기</A><br>
						· <A href="javascript:parent.topframe.GoMenu(2,'design_eachorderlist.php');">주문리스트 화면 꾸미기</A><br>
						· <A href="javascript:parent.topframe.GoMenu(2,'design_eachwishlist.php');">WishList 화면 꾸미기</A><br>
						</td>
					</tr>
					</table>
					</td>
					<td valign="top">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td style="padding-top:19px; padding-left:15px;" class="font_size1">
						· <A href="javascript:parent.topframe.GoMenu(2,'design_eachmycoupon.php');">쿠폰 화면 꾸미기</A><br>
						· <A href="javascript:parent.topframe.GoMenu(2,'design_eachmyreserve.php');">적립금 화면 꾸미기</A><br>
						· <A href="javascript:parent.topframe.GoMenu(2,'design_eachmypersonal.php');">1:1고객문의 화면 꾸미기</A><br>
						<? if(getVenderUsed()==true) { ?>· <A href="javascript:parent.topframe.GoMenu(2,'design_eachmycustsect.php');">단골매장 화면 꾸미기</A><br> <? } ?>
						· <A href="javascript:parent.topframe.GoMenu(2,'design_eachsurveylist.php');">투표리스트 화면 꾸미기</A><br>
						· <A href="javascript:parent.topframe.GoMenu(2,'design_eachsurveyview.php');">투표결과 화면 꾸미기</A><br>
						· <A href="javascript:parent.topframe.GoMenu(2,'design_eachreviewpopup.php');">상품리뷰 보기창 꾸미기</A><br>
						· <A href="javascript:parent.topframe.GoMenu(2,'design_eachreviewall.php');">리뷰모음 화면 꾸미기</A><br>
						· <A href="javascript:parent.topframe.GoMenu(2,'design_eachrssinfo.php');">RSS 화면 꾸미기</A><br>
						· <A href="javascript:parent.topframe.GoMenu(2,'design_eachblist.php');">상품브랜드 화면 꾸미기</A><br>
						· <A href="javascript:parent.topframe.GoMenu(2,'design_eachbmap.php');">브랜드맵 화면 꾸미기</A>
						</td>
					</tr>
					</table>
					</td>
					<td valign="top">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td class="font_blue1a"><img src="images/icon_point.gif" border="0">개별 추가페이지 관리</td>
					</tr>
					<tr>
						<td style="padding-top:5px; padding-left:15px;" class="font_size1">
						· <A href="javascript:parent.topframe.GoMenu(2,'design_newpage.php');">일반페이지 꾸미기</A><br>
						· <A href="javascript:parent.topframe.GoMenu(2,'design_community.php');">커뮤니티 페이지 꾸미기</A>
						</td>
					</tr>
					</table>
					</td>
					<td valign="top">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td class="font_blue1a"><img src="images/icon_point.gif" border="0">Easy디자인 관리</td>
					</tr>
					<tr>
						<td style="padding-top:5px; padding-left:15px;" class="font_size1">
						· <A href="javascript:parent.topframe.GoMenu(2,'design_easytop.php');">Easy 상단 메뉴 관리</A><br>
						· <A href="javascript:parent.topframe.GoMenu(2,'design_easyleft.php');">Easy 왼쪽 메뉴 관리</A><br>
						· <A href="javascript:parent.topframe.GoMenu(2,'design_easycss.php');">Easy 텍스트 속성 변경</A>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td height="50" colspan="2"><hr noshade size="1" color="#DEDEDE"></td>
			</tr>
			<tr>
				<td valign="top"><A href="javascript:parent.topframe.GoMenu(3,'member_index.php');"><img src="images/sitemap_img03.gif" border="0" hspace="5"></a></td>
				<td width="100%" valign="top">
				<table cellpadding="0" cellspacing="0" width="100%">
				<col width="20%"></col>
				<col width="20%"></col>
				<col width="20%"></col>
				<col width="20%"></col>
				<col width="20%"></col>
				<tr>
					<td valign="top">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td class="font_blue1a"><img src="images/icon_point.gif" border="0">회원정보 관리</td>
					</tr>
					<tr>
						<td style="padding-top:5px; padding-left:15px;" class="font_size1">
						· <A href="javascript:parent.topframe.GoMenu(3,'member_list.php');">회원정보 관리</A><br>
						· <A href="javascript:parent.topframe.GoMenu(3,'member_outlist.php');">회원 탈퇴요청 관리</A><br>
						· <A href="javascript:parent.topframe.GoMenu(3,'member_excelupload.php');">회원정보 일괄 등록</A>
						</td>
					</tr>
					</table>
					</td>
					<td valign="top">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td class="font_blue1a"><img src="images/icon_point.gif" border="0">회원등급 설정</td>
					</tr>
					<tr>
						<td style="padding-top:5px; padding-left:15px;" class="font_size1">
						· <A href="javascript:parent.topframe.GoMenu(3,'member_groupnew.php');">회원등급 등록/수정/삭제</A><br>
						· <A href="javascript:parent.topframe.GoMenu(3,'member_groupmemreg.php');">회원등급 변경 관리</A><br>
						· <A href="javascript:parent.topframe.GoMenu(3,'member_groupmemberview.php');">등급별 회원 관리</A>
						</td>
					</tr>
					</table>
					</td>
					<td valign="top">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td class="font_blue1a"><img src="images/icon_point.gif" border="0">회원관리 부가기능</td>
					</tr>
					<tr>
						<td style="padding-top:5px; padding-left:15px;" class="font_size1">
						· <A href="javascript:parent.topframe.GoMenu(3,'member_mailsend.php');">개별메일 발송</A><br>
						· <A href="javascript:parent.topframe.GoMenu(3,'member_mailallsend.php');">단체메일 발송</A><br>
						· <A href="javascript:parent.topframe.GoMenu(3,'member_mailallsendinfo.php');">단체메일 발송내역 관리</A><br>
						· <A href="javascript:parent.topframe.GoMenu(7,'market_smssinglesend.php');">개별 SMS 발송</A><br>
						· <A href="javascript:parent.topframe.GoMenu(7,'market_smsgroupsend.php');">단체 SMS 발송</A>
						</td>
					</tr>
					</table>
					</td>
					<td valign="top"></td>
					<td valign="top"></td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td height="50" colspan="2"><hr noshade size="1" color="#DEDEDE"></td>
			</tr>
			<tr>
				<td valign="top"><A href="javascript:parent.topframe.GoMenu(4,'product_index.php');"><img src="images/sitemap_img04.gif" border="0" hspace="5"></a></td>
				<td width="100%" valign="top">
				<table cellpadding="0" cellspacing="0" width="100%">
				<col width="20%"></col>
				<col width="20%"></col>
				<col width="20%"></col>
				<col width="20%"></col>
				<col width="20%"></col>
				<tr>
					<td valign="top">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td class="font_blue1a"><img src="images/icon_point.gif" border="0">카테고리/상품관리</td>
					</tr>
					<tr>
						<td style="padding-top:5px; padding-left:15px;" class="font_size1">
						· <A href="javascript:parent.topframe.GoMenu(4,'product_code.php');">카테고리 관리</A><br>
						· <A href="javascript:parent.topframe.GoMenu(4,'product_register.php');">상품 등록/수정/삭제</A><br>
						· <A href="javascript:parent.topframe.GoMenu(4,'product_assemble.php');">코디/조립 상품 관리</A><br>
						· <A href="javascript:parent.topframe.GoMenu(4,'product_mainlist.php');">메인상품 진열관리</A><br>
						· <A href="javascript:parent.topframe.GoMenu(4,'product_codelist.php');">카테고리 상품 진열관리</A><br>
						· <A href="javascript:parent.topframe.GoMenu(4,'product_sort.php');">상품 진열순서 설정</A><br>
						· <A href="javascript:parent.topframe.GoMenu(4,'product_copy.php');">상품 이동/복사/삭제</A><br>
						· <A href="javascript:parent.topframe.GoMenu(4,'product_theme.php');">가상 카테고리 상품관리</A><br>
						· <A href="javascript:parent.topframe.GoMenu(4,'product_detaillist.php');">상품 스펙 노출관리</A><br>
						· <A href="javascript:parent.topframe.GoMenu(4,'product_deliinfo.php');">교환/배송/환불정보 노출</A><br>
						· <A href="javascript:parent.topframe.GoMenu(4,'product_brand.php');">상품 브랜드 설정 관리</A><br>
						· <A href="javascript:parent.topframe.GoMenu(4,'product_business.php');">상품 거래처 관리</A><br>
						· <A href="javascript:parent.topframe.GoMenu(4,'product2_register.php');">상품권 등록/수정/관리</A><br>
						· <A href="javascript:parent.topframe.GoMenu(4,'product_latestup.php');">최근등록상품</A><br>
						· <A href="javascript:parent.topframe.GoMenu(4,'product_latestsell.php');">최근판매상품</A>
						</td>
					</tr>
					</table>
					</td>
					<td valign="top">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td class="font_blue1a"><img src="images/icon_point.gif" border="0">다중이미지 관리</td>
					</tr>
					<tr>
						<td style="padding-top:5px; padding-left:15px;" class="font_size1">
						· <A href="javascript:parent.topframe.GoMenu(4,'product_imgmulticonfig.php');">상품 다중이미지 설정</A><br>
						· <A href="javascript:parent.topframe.GoMenu(4,'product_imgmultiset.php');">상품 다중이미지 등록/관리</A>
						</td>
					</tr>
					</table><br>
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td class="font_blue1a"><img src="images/icon_point.gif" border="0">관련상품 관리</td>
					</tr>
					<tr>
						<td style="padding-top:5px; padding-left:15px;" class="font_size1">
						· <A href="javascript:parent.topframe.GoMenu(4,'product_collectionconfig.php');">관련상품 진열방식 설정</A><br>
						· <A href="javascript:parent.topframe.GoMenu(4,'product_collectionlist.php');">관련상품 검색/등록</A>
						</td>
					</tr>
					</table>
					</td>
					<td valign="top">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td class="font_blue1a"><img src="images/icon_point.gif" border="0">상품 일괄관리</td>
					</tr>
					<tr>
						<td style="padding-top:5px; padding-left:15px;" class="font_size1">
						· <A href="javascript:parent.topframe.GoMenu(4,'product_allupdate.php');">상품 일괄 간편수정</A><br>
						· <A href="javascript:parent.topframe.GoMenu(4,'product_reserve.php');">상품 적립금 일괄수정</A><br>
						· <A href="javascript:parent.topframe.GoMenu(4,'product_price.php');">판매상품 가격 일괄수정</A><br>
						· <A href="javascript:parent.topframe.GoMenu(4,'product_allsoldout.php');">품절상품 일괄 삭제/관리</A><br>
						· <A href="javascript:parent.topframe.GoMenu(4,'product_allquantity.php');">재고상품 일괄관리</A><br>
						· <A href="javascript:parent.topframe.GoMenu(4,'product_excelupload.php');">상품정보 일괄 등록</A><br>
						· <A href="javascript:parent.topframe.GoMenu(4,'product_exceldownload.php');">상품 엑셀 다운로드</A>
						</td>
					</tr>
					</table>
					</td>
					<td valign="top">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td class="font_blue1a"><img src="images/icon_point.gif" border="0">사은품/견적/기타관리</td>
					</tr>
					<tr>
						<td style="padding-top:5px; padding-left:15px;" class="font_size1">
						· <A href="javascript:parent.topframe.GoMenu(4,'product_giftlist.php');">사은품 제도 관리</A><br>
						· <A href="javascript:parent.topframe.GoMenu(4,'product_estimate.php');">견적서 상품 등록/관리</A><br>
						· <A href="javascript:parent.topframe.GoMenu(4,'product_review.php');">상품 리뷰 관리</A><br>
						· <A href="javascript:parent.topframe.GoMenu(4,'product_wishlist.php');">Wishlist 상품 관리</A><br>
						· <A href="javascript:parent.topframe.GoMenu(4,'product_keywordsearch.php');">상품 키워드 검색</A><br>
						· <A href="javascript:parent.topframe.GoMenu(4,'product_detailfilter.php');">상품상세내역 단어 필터링</A>
						</td>
					</tr>
					</table><br>
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td class="font_blue1a"><img src="images/icon_point.gif" border="0">옵션그룹 등록 관리</td>
					</tr>
					<tr>
						<td style="padding-top:5px; padding-left:15px;" class="font_size1">
						· <A href="javascript:parent.topframe.GoMenu(4,'product_option.php');">옵션그룹 등록 관리</A>
						</td>
					</tr>
					</table><br>
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td class="font_blue1a"><img src="images/icon_point.gif" border="0">옵션그룹 등록 관리</td>
					</tr>
					<tr>
						<td style="padding-top:5px; padding-left:15px;" class="font_size1">
						· <A href="javascript:parent.topframe.GoMenu(4,'product_option.php');">옵션그룹 등록 관리</A>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td height="50" colspan="2"><hr noshade size="1" color="#DEDEDE"></td>
			</tr>
			<tr>
				<td valign="top"><A href="javascript:parent.topframe.GoMenu(5,'order_index.php');"><img src="images/sitemap_img05.gif" border="0" hspace="5"></a></td>
				<td width="100%" valign="top">
				<table cellpadding="0" cellspacing="0" width="100%">
				<col width="20%"></col>
				<col width="20%"></col>
				<col width="20%"></col>
				<col width="20%"></col>
				<col width="20%"></col>
				<tr>
					<td valign="top">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td class="font_blue1a"><img src="images/icon_point.gif" border="0">주문조회 및 배송관리</td>
					</tr>
					<tr>
						<td style="padding-top:5px; padding-left:15px;" class="font_size1">
						· <A href="javascript:parent.topframe.GoMenu(5,'order_list.php');">일자별 주문조회/배송</A><br>
						· <A href="javascript:parent.topframe.GoMenu(5,'order_delay.php');">미배송/미입금 주문관리</A><br>
						· <A href="javascript:parent.topframe.GoMenu(5,'order_delisearch.php');">배송/입금일별 주문조회</A><br>
						· <A href="javascript:parent.topframe.GoMenu(5,'order_namesearch.php');">이름/가격별 외 주문조회</A><br>
						· <A href="javascript:parent.topframe.GoMenu(5,'order_monthsearch.php');">개월별 상품명 주문조회</A><br>
						· <A href="javascript:parent.topframe.GoMenu(5,'order_tempinfo.php');">결제시도 주문서 관리</A><br>
						· <A href="javascript:parent.topframe.GoMenu(5,'order_excelinfo.php');">주문리스트 엑셀파일 관리</A><br>
						· <A href="javascript:parent.topframe.GoMenu(5,'order_csvdelivery.php');">주문리스트 일괄배송 관리</A>
						</td>
					</tr>
					</table>
					</td>
					<td valign="top">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td class="font_blue1a"><img src="images/icon_point.gif" border="0">장바구니 및 매출 분석</td>
					</tr>
					<tr>
						<td style="padding-top:5px; padding-left:15px;" class="font_size1">
						· <A href="javascript:parent.topframe.GoMenu(5,'order_basket.php');">장바구니 상품분석</A><br>
						· <A href="javascript:parent.topframe.GoMenu(5,'order_allsale.php');">전체상품 매출분석</A><br>
						· <A href="javascript:parent.topframe.GoMenu(5,'order_eachsale.php');">개별상품 매출분석</A>
						</td>
					</tr>
					</table>
					</td>
					<td valign="top">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td class="font_blue1a"><img src="images/icon_point.gif" border="0">현금영수증 관리</td>
					</tr>
					<tr>
						<td style="padding-top:5px; padding-left:15px;" class="font_size1">
						· <A href="javascript:parent.topframe.GoMenu(5,'order_taxsaveabout.php');">현금영수증 제도란?</A><br>
						· <A href="javascript:parent.topframe.GoMenu(5,'order_taxsaveconfig.php');">현금영수증 환경설정</A><br>
						· <A href="javascript:parent.topframe.GoMenu(5,'order_taxsavelist.php');">현금영수증 발급/조회</A><br>
						· <A href="javascript:parent.topframe.GoMenu(5,'order_taxsaveissue.php');">현금영수증 개별발급</A>
						</td>
					</tr>
					</table>
					</td>
					<td valign="top"></td>
					<td valign="top"></td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td height="50" colspan="2"><hr noshade size="1" color="#DEDEDE"></td>
			</tr>
			<tr>
				<td valign="top"><A href="javascript:parent.topframe.GoMenu(6,'gong_index.php');"><img src="images/sitemap_img06.gif" border="0" hspace="5"></a></td>
				<td width="100%" valign="top">
				<table cellpadding="0" cellspacing="0" width="100%">
				<col width="20%"></col>
				<col width="20%"></col>
				<col width="20%"></col>
				<col width="20%"></col>
				<col width="20%"></col>
				<tr>
					<td valign="top">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td class="font_blue1a"><img src="images/icon_point.gif" border="0">소셜쇼핑</td>
					</tr>
					<tr>
						<td style="padding-top:5px; padding-left:15px;" class="font_size1">
						· <A href="javascript:parent.topframe.GoMenu(6,'social_shopping.php');">소셜쇼핑상품관리</A><br>
						· <A href="javascript:parent.topframe.GoMenu(6,'social_sell_result.php');">판매종료 소셜상품</A><br>
						· <A href="javascript:parent.topframe.GoMenu(6,'social_request.php');">공동구매신청관리</A><br>
						· <A href="javascript:parent.topframe.GoMenu(6,'social_request.php');">공동구매구독관리</A><br>
						· <A href="javascript:parent.topframe.GoMenu(6,'social_mailing_result.php');">구독메일전송목록</A>
						</td>
					</tr>
					</table>
					</td>
					<td valign="top">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td class="font_blue1a"><img src="images/icon_point.gif" border="0">경매/공동구매 화면설정</td>
					</tr>
					<tr>
						<td style="padding-top:5px; padding-left:15px;" class="font_size1">
						· <A href="javascript:parent.topframe.GoMenu(6,'gong_displayset.php');">경매/공동구매 화면설정</A>
						</td>
					</tr>
					</table>
					</td>
					<td valign="top">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td class="font_blue1a"><img src="images/icon_point.gif" border="0">쇼핑몰 경매 관리</td>
					</tr>
					<tr>
						<td style="padding-top:5px; padding-left:15px;" class="font_size1">
						· <A href="javascript:parent.topframe.GoMenu(6,'gong_auctionreg.php');">경매상품 등록/수정</A><br>
						· <A href="javascript:parent.topframe.GoMenu(6,'gong_auctionlist.php');">등록 경매 관리</A>
						</td>
					</tr>
					</table>
					</td>
					<td valign="top">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td class="font_blue1a"><img src="images/icon_point.gif" border="0">공동구매 관리</td>
					</tr>
					<tr>
						<td style="padding-top:5px; padding-left:15px;" class="font_size1">
						· <A href="javascript:parent.topframe.GoMenu(6,'gong_gongchangereg.php');">가격변동형 공구 등록/수정</A><br>
						· <A href="javascript:parent.topframe.GoMenu(6,'gong_gongchangelist.php');">가격변동형 등록공구 관리</A><br>
						· <A href="javascript:parent.topframe.GoMenu(6,'gong_gongfixset.php');">가격고정형 공동구매 설정</A><br>
						· <A href="javascript:parent.topframe.GoMenu(6,'gong_gongfixreg.php');">가격고정형 공구구매 등록</A>
						</td>
					</tr>
					</table>
					</td>
					<td valign="top"></td>
					<td valign="top"></td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td height="50" colspan="2"><hr noshade size="1" color="#DEDEDE"></td>
			</tr>
			<tr>
				<td valign="top"><A href="javascript:parent.topframe.GoMenu(7,'market_index.php');"><img src="images/sitemap_img07.gif" border="0" hspace="5"></a></td>
				<td width="100%" valign="top">
				<table cellpadding="0" cellspacing="0" width="100%">
				<col width="20%"></col>
				<col width="20%"></col>
				<col width="20%"></col>
				<col width="20%"></col>
				<col width="20%"></col>
				<tr>
					<td valign="top">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td class="font_blue1a"><img src="images/icon_point.gif" border="0">마케팅 지원</td>
					</tr>
					<tr>
						<td style="padding-top:5px; padding-left:15px;" class="font_size1">
						· <A href="javascript:parent.topframe.GoMenu(7,'market_notice.php');">공지사항 관리</A><br>
						· <A href="javascript:parent.topframe.GoMenu(7,'market_contentinfo.php');">정보(information)관리</A><br>
						· <A href="javascript:parent.topframe.GoMenu(7,'market_survey.php');">온라인투표 관리</A><br>
						· <A href="javascript:parent.topframe.GoMenu(7,'market_partner.php');">제휴마케팅 관리</A><br>
						· <A href="javascript:parent.topframe.GoMenu(7,'market_affiliatebanner.php');">Affiliate 배너관리</A><br>
						· <A href="javascript:parent.topframe.GoMenu(7,'market_enginepage.php');">가격비교페이지 관리</A><br>
						· <A href="javascript:parent.topframe.GoMenu(7,'market_cash_reserve.php');">적립금 현금전환리스트</A>
						</td>
					</tr>
					</table>
					</td>
					<td valign="top">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td class="font_blue1a"><img src="images/icon_point.gif" border="0">이벤트/사은품 기능 설정</td>
					</tr>
					<tr>
						<td style="padding-top:5px; padding-left:15px;" class="font_size1">
						· <A href="javascript:parent.topframe.GoMenu(7,'market_eventpopup.php');">팝업 이벤트 관리</A><br>
						· <A href="javascript:parent.topframe.GoMenu(7,'market_quickmenu.php');">Quick메뉴 관리</A><br>
						· <A href="javascript:parent.topframe.GoMenu(7,'market_newproductview.php');">최근 본 상품 관리</A><br>
						· <A href="javascript:parent.topframe.GoMenu(7,'market_eventcode.php');">카테고리별 이벤트 관리</A><br>
						· <A href="javascript:parent.topframe.GoMenu(7,'market_eventprdetail.php');">상품상세 공통이벤트 관리</A><br>
						· <A href="javascript:parent.topframe.GoMenu(7,'product_giftlist.php');">고객 사은품 등록/관리</A>
						</td>
					</tr>
					</table>
					</td>
					<td valign="top">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td class="font_blue1a"><img src="images/icon_point.gif" border="0">쿠폰발생 서비스 설정</td>
					</tr>
					<tr>
						<td style="padding-top:5px; padding-left:15px;" class="font_size1">
						· <A href="javascript:parent.topframe.GoMenu(7,'market_couponnew.php');">새로운 쿠폰 생성하기</A><br>
						· <A href="javascript:parent.topframe.GoMenu(7,'market_couponsupply.php');">생성된 쿠폰 즉시발급</A><br>
						· <A href="javascript:parent.topframe.GoMenu(7,'market_couponlist.php');">발급된 쿠폰 내역관리</A>
						</td>
					</tr>
					</table>
					</td>
					<td valign="top">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td class="font_blue1a"><img src="images/icon_point.gif" border="0">SMS 발송/관리</td>
					</tr>
					<tr>
						<td style="padding-top:5px; padding-left:15px;" class="font_size1">
						· <A href="javascript:parent.topframe.GoMenu(7,'market_smsconfig.php');">SMS 기본환경 설정</A><br>
						· <A href="javascript:parent.topframe.GoMenu(7,'market_smssendlist.php');">SMS 발송내역 관리</A><br>
						· <A href="javascript:parent.topframe.GoMenu(7,'market_smssinglesend.php');">SMS 개별 발송</A><br>
						· <A href="javascript:parent.topframe.GoMenu(7,'market_smsgroupsend.php');">SMS 등급/단체 발송</A><br>
						· <A href="javascript:parent.topframe.GoMenu(7,'market_smsaddressbook.php');">SMS 주소록 관리</A><br>
						· <A href="javascript:parent.topframe.GoMenu(7,'market_smsfill.php');">SMS 충전하기</A>
						</td>
					</tr>
					</table>
					</td>
					<td valign="top"></td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td height="50" colspan="2"><hr noshade size="1" color="#DEDEDE"></td>
			</tr>
			<tr>
				<td valign="top"><A href="javascript:parent.topframe.GoMenu(8,'community_index.php');"><img src="images/sitemap_img08.gif" border="0" hspace="5"></a></td>
				<td width="100%" valign="top">
				<table cellpadding="0" cellspacing="0" width="100%">
				<col width="20%"></col>
				<col width="20%"></col>
				<col width="20%"></col>
				<col width="20%"></col>
				<col width="20%"></col>
				<tr>
					<td valign="top">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td class="font_blue1a"><img src="images/icon_point.gif" border="0">커뮤니티 관리</td>
					</tr>
					<tr>
						<td style="padding-top:5px; padding-left:15px;" class="font_size1">
						· <A href="javascript:parent.topframe.GoMenu(8,'community_list.php');">게시판 리스트 관리</A><br>
						· <A href="javascript:parent.topframe.GoMenu(8,'community_register.php');">게시판 신규 생성</A><br>
						· <A href="javascript:parent.topframe.GoMenu(8,'community_article.php');">게시판 게시물 관리</A><br>
						· <A href="javascript:parent.topframe.GoMenu(8,'community_notice.php');">게시판 공지사항 관리</A><br>
						· <A href="javascript:parent.topframe.GoMenu(8,'community_personal.php');">1:1 고객 게시판 관리</A><br>
						· <A href="javascript:parent.topframe.GoMenu(8,'community_schedule_year.php');">쇼핑몰 일정관리</A>
						</td>
					</tr>
					</table>
					</td>
					<td valign="top"></td>
					<td valign="top"></td>
					<td valign="top"></td>
					<td valign="top"></td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td height="50" colspan="2"><hr noshade size="1" color="#DEDEDE"></td>
			</tr>
			<tr>
				<td valign="top"><A href="javascript:parent.topframe.GoMenu(9,'counter_index.php');"><img src="images/sitemap_img09.gif" border="0" hspace="5"></a></td>
				<td width="100%" valign="top">
				<table cellpadding="0" cellspacing="0" width="100%">
				<col width="20%"></col>
				<col width="20%"></col>
				<col width="20%"></col>
				<col width="20%"></col>
				<col width="20%"></col>
				<tr>
					<td valign="top">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td class="font_blue1a"><img src="images/icon_point.gif" border="0">접속통계 HOME</td>
					</tr>
					<tr>
						<td style="padding-top:5px; padding-left:15px;" class="font_size1">
						· <a href="javascript:parent.topframe.GoMenu(9,'counter_index.php');">접속통계 HOME</a>
						</td>
					</tr>
					</table>
					</td>
					<td valign="top">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td class="font_blue1a"><img src="images/icon_point.gif" border="0">트래픽 분석</td>
					</tr>
					<tr>
						<td style="padding-top:5px; padding-left:15px;" class="font_size1">
						· <a href="javascript:parent.topframe.GoMenu(9,'counter_timevisit.php');">시간별 순 방문자</a><br>
						· <a href="javascript:parent.topframe.GoMenu(9,'counter_dayvisit.php');">일자별 순 방문자</a><br>
						· <a href="javascript:parent.topframe.GoMenu(9,'counter_timepageview.php');">시간별 페이지뷰</a><br>
						· <a href="javascript:parent.topframe.GoMenu(9,'counter_daypageview.php');">일자별 페이지뷰</a><br>
						· <a href="javascript:parent.topframe.GoMenu(9,'counter_timeorder.php');">시간별 주문시도건수</a><br>
						· <a href="javascript:parent.topframe.GoMenu(9,'counter_dayorder.php');">일자별 주문시도건수</a>
						</td>
					</tr>
					</table>
					</td>
					<td valign="top">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td class="font_blue1a"><img src="images/icon_point.gif" border="0">고객 선호도 분석</td>
					</tr>
					<tr>
						<td style="padding-top:5px; padding-left:15px;" class="font_size1">
						· <a href="javascript:parent.topframe.GoMenu(9,'counter_prcodeprefer.php');">분류별 선호도</a><br>
						· <a href="javascript:parent.topframe.GoMenu(9,'counter_productprefer.php');">상품 선호도</a><br>
						· <a href="javascript:parent.topframe.GoMenu(9,'counter_prsearchprefer.php');">상품 검색 선호도</a><br>
						· <a href="javascript:parent.topframe.GoMenu(9,'counter_sitepageprefer.php');">Site 구성요소 선호도</a>
						</td>
					</tr>
					</table>
					</td>
					<td valign="top">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td class="font_blue1a"><img src="images/icon_point.gif" border="0">외부 접근 경로 분석</td>
					</tr>
					<tr>
						<td style="padding-top:5px; padding-left:15px;" class="font_size1">
						· <a href="javascript:parent.topframe.GoMenu(9,'counter_domainrank.php');">도메인별 접근순위</a><br>
						· <a href="javascript:parent.topframe.GoMenu(9,'counter_searchenginerank.php');">검색엔진별 접근순위</a><br>
						· <a href="javascript:parent.topframe.GoMenu(9,'counter_searchkeywordrank.php');">검색엔진 검색어 순위</a>
						</td>
					</tr>
					</table>
					</td>
					<td valign=top>
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td class="font_blue1a"><img src="images/icon_point.gif" border="0">그래프로 보는 통계분석</td>
					</tr>
					<tr>
						<td style="padding-top:5px; padding-left:15px;" class="font_size1">
						· <a href="javascript:parent.topframe.GoMenu(9,'counter_timetotal.php');">시간별 전체 접속통계</a><br>
						· <a href="javascript:parent.topframe.GoMenu(9,'counter_daytotal.php');">일자별 전체 접속통계</a>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td height="50" colspan="2"><hr noshade size="1" color="#DEDEDE"></td>
			</tr>
			<tr>
				<td valign="top">
				<A href="javascript:parent.topframe.GoMenu(1,'vender_index.php');"><img src="images/sitemap_img01_1.gif" border="0" hspace="5"></a></td>
				<td width="100%" valign="top">
				<? if(setUseVender()==true) { ?>
				<table cellpadding="0" cellspacing="0" width="100%">
				<col width="20%"></col>
				<col width="20%"></col>
				<col width="20%"></col>
				<col width="20%"></col>
				<col width="20%"></col>
				<tr>
					<td valign="top">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td class="font_blue1a"><img src="images/icon_point.gif" border="0">입점업체 관리</td>
					</tr>
					<tr>
						<td style="padding-top:5px; padding-left:15px;" class="font_size1">
						· <a href="javascript:parent.topframe.GoMenu(1,'vender_new.php');">입점업체 신규등록</a><br>
						· <a href="javascript:parent.topframe.GoMenu(1,'vender_management.php');">입점업체 정보관리</a><br>
						· <a href="javascript:parent.topframe.GoMenu(1,'vender_notice.php');">입점업체 공지사항</a><br>
						· <a href="javascript:parent.topframe.GoMenu(1,'vender_counsel.php');">입점업체 상담게시판</a><br>
						· <a href="javascript:parent.topframe.GoMenu(1,'vender_mailsend.php');">E-mail 발송</a><br>
						· <a href="javascript:parent.topframe.GoMenu(1,'vender_smssend.php');">SMS 문자전송</a>
						</td>
					</tr>
					</table>
					</td>
					<td valign="top">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td class="font_blue1a"><img src="images/icon_point.gif" border="0">입점상품 관리</td>
					</tr>
					<tr>
						<td style="padding-top:5px; padding-left:15px;" class="font_size1">
						· <a href="javascript:parent.topframe.GoMenu(1,'vender_prdtlist.php');">입점업체 상품목록</a><br>
						· <a href="javascript:parent.topframe.GoMenu(1,'vender_prdtallupdate.php');">상품 일괄 간편수정</a><br>
						· <a href="javascript:parent.topframe.GoMenu(1,'vender_prdtallsoldout.php');">품절상품 일괄 삭제/관리</a>
						</td>
					</tr>
					</table>
					</td>
					<td valign="top">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td class="font_blue1a"><img src="images/icon_point.gif" border="0">주문/정산 관리</td>
					</tr>
					<tr>
						<td style="padding-top:5px; padding-left:15px;" class="font_size1">
						· <a href="javascript:parent.topframe.GoMenu(1,'vender_orderlist.php');">입점업체 주문조회</a><br>
						· <a href="javascript:parent.topframe.GoMenu(1,'vender_orderadjust.php');">입점업체 정산관리</a><br>
						· <a href="javascript:parent.topframe.GoMenu(1,'vender_calendar.php');">입점업체 정산 캘린더</a>
						</td>
					</tr>
					</table>
					</td>
					<td valign="top"></td>
					<td valign="top"></td>
				</tr>
				</table>
				<? } ?>
				</td>
			</tr>
			</table>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td height="20"></td>
	</tr>
	</table>
	</td>
</tr>
</table>

<? INCLUDE ("copyright.php"); ?>
