<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
$sleftMn = "NO";
unset($row);
$sql = "SELECT * FROM tbldesign ";
$result=mysql_query($sql,get_db_conn());
if($crow=mysql_fetch_object($result)) {

} else {
	$crow->introtype="C";
}
mysql_free_result($result);

?>

<HTML>
<HEAD>
<TITLE><?=$_data->shoptitle?> 디자인샵 > 공동구매 > 공동구매 이용안내</TITLE>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META http-equiv="X-UA-Compatible" content="IE=5" />
<META name="description" content="<?=(strlen($_data->shopdescription)>0?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<?include($Dir."lib/style.php")?>
</HEAD>

<body<?=(substr($_data->layoutdata["MOUSEKEY"],0,1)=="Y"?" oncontextmenu=\"return false;\"":"")?><?=(substr($_data->layoutdata["MOUSEKEY"],1,1)=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=(substr($_data->layoutdata["MOUSEKEY"],2,1)=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>

<? include ($Dir.MainDir.$_data->menu_type.".php") ?>

<table align="center" cellpadding="0" cellspacing="0" width="100%">
    <tr>
        <td valign="top">
			<table align="center" cellpadding="0" cellspacing="0" width="100%" class="table_td">
				<tr>
					<td>
						<table cellpadding="0" cellspacing="0" width="100%">
							<tr>
								<td><IMG SRC="../images/design/gonggu_title01.gif" ALT=""></td>
								<td class="course" align="right"></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td>
						<TABLE WIDTH=100% BORDER=0 CELLPADDING=0 CELLSPACING=0>
							<TR>
								<TD><a href="../front/gonggu_main.php"><IMG SRC="../images/design/gonggu_tap01.gif" ALT="" border="0"></a></TD>
								<TD><a href="../front/gonggu_end.php"><IMG SRC="../images/design/gonggu_tap02.gif"  ALT="" border="0"></a></TD>
								<TD><a href="../front/gonggu_order.php"><IMG SRC="../images/design/gonggu_tap03.gif"  ALT="" border="0"></a></TD>
								<TD><a href="../front/gonggu_guide.php"><IMG SRC="../images/design/gonggu_tap04r.gif"  ALT="" border="0"></a></TD>
								<TD width="100%" background="../images/design/gonggu_tap_bg.gif"></TD>
							</TR>
						</TABLE>
					</td>
				</tr>
				<tr>
					<td height="40"></td>
				</tr>
				<tr>
					<td><IMG SRC="../images/design/gonggu_stitle01.gif" WIDTH=179 HEIGHT=50 ALT=""></td>
				</tr>
				<tr>
					<td><IMG SRC="../images/design/gonggu_sstitle01.gif" WIDTH=311 HEIGHT=25 ALT=""></td>
				</tr>
				<tr>
					<td class="table_td1">아래의 3가지 방법으로 원하는 SNS채널에 로그인 하세요.<br>- 마이페이지&gt;나의 정보&gt;나의 SNS 채널 등록 메뉴에서 공유를 원하는 SNS채널에 로그인<br>- 상품 상세피이지 하단 공동구매 신청 탭에서 글작성 전 아래 참고그림 1번영역의 SNS채널을 선택하여 로그인<br>- 공동구매 메뉴 내 공동구매 신청 탭에서 글작성 전 아래 참고그림 1번 영역의 SNS채널을 선택하여 로그인</td>
				</tr>
				<tr>
					<td height="40"></td>
				</tr>
                <tr>
					<td><IMG SRC="../images/design/gonggu_sstitle02.gif" WIDTH=132 HEIGHT=25 ALT=""></td>
                </tr>
                <tr>
					<td class="table_td1">- 아래 참고그림 2번의 상품검색하기 버튼을 클릭<br>- 상품 검색 팝업에서 나의 관심상품 리스트에서 또는 직접 검색하여 상품을 선택<br>- 각 SNS채널에 공유될 문구를 아래 참고그림 3번영역에 입력 후 공동구매 제안을 완료하세요!</td>
                </tr>
                <tr>
					<td height=20></td>
                </tr>
                <tr>
					<td><IMG SRC="../images/design/gonggu_img01.gif" WIDTH=930 HEIGHT=156 ALT=""></td>
                </tr>
                <tr>
					<td height="60"></td>
                </tr>
                <tr>
					<td><IMG SRC="../images/design/gonggu_stitle02.gif" WIDTH=218 HEIGHT=50 ALT=""></td>
                </tr>
                <tr>
					<td><IMG SRC="../images/design/gonggu_sstitle03.gif" WIDTH=282 HEIGHT=25 ALT=""></td>
                </tr>
				<tr>
					<td class="table_td1">- 공구를 제안한 상품 중 나도 동일한 상품에 대해 공구를 희망한다면 아래 참고그림 1번의 함께 신청하기 버튼을 클릭<br>- 클릭시 희망 문구를 입력할 수 있고 해당 상품이 공동구매가 진행될 시 이메일과 SMS로 알람을 받을지 여부도 선택 할 수 있습니다.<br>- 희망글을 입력할 경우 아래참고그림 2번영역에 자신이 쓴글과 SNS사진이 보여집니다.</td>
				</tr>
				<tr>
					<td height=20></td>
				</tr>
				<tr>
					<td>
                        <table cellpadding="15" cellspacing="1" width="100%" bgcolor="#EEEEEE">
                            <tr>
								<td width="922" bgcolor="#F9F9F9" class="table_td">공구 진행중일 경우 함께 신청하기 버튼대신 공구진행중 버튼으로 바뀜<br>공구 제안 후 공구진행되지 않은 상태로 일주일이 지날경우 공구 미진행 버튼으로 바뀜<br>공구가 진행된 상품인 경우 공구완료 버튼으로 바뀜</td>
                            </tr>
                        </table>
					</td>
				</tr>
				<tr>
					<td height="30"></td>
				</tr>
				<tr>
					<td><IMG SRC="../images/design/gonggu_img02.gif" WIDTH=930 HEIGHT=215 ALT=""></td>
				</tr>
				<tr>
					<td height="60"></td>
				</tr>
				<tr>
					<td><IMG SRC="../images/design/gonggu_stitle03.gif" WIDTH=225 HEIGHT=50 ALT=""></td>
				</tr>
				<tr>
					<td><IMG SRC="../images/design/gonggu_sstitle04.gif" WIDTH=205 HEIGHT=25 ALT=""></td>
				</tr>
				<tr>
					<td class="table_td1">공동구매 &gt; 진행중인 공동구매 탭에서 진행중인 공동구매가 보여지며 상품상세보기 버튼을 클릭하면 상품에 대한 상세 설명을 확인 하실 수 있습니다.</td>
				</tr>
				<tr>
					<td height="30"></td>
				</tr>
                <tr>
					<td><IMG SRC="../images/design/gonggu_sstitle05.gif" WIDTH=166 HEIGHT=25 ALT=""></td>
                </tr>
                <tr>
					<td class="table_td1">상품 상세 내용 확인 후 옵션과 수량을 선택하여 공구가격에 구매예약 하실 수 있습니다.<br>(구매예약 시 선결제가 이루어 지며 공동구매가 성립되지 않을 경우 일괄 환불 처리 됩니다.)</td>
				</tr>
                <tr>
					<td height="30"></td>
                </tr>
                <tr>
					<td><IMG SRC="../images/design/gonggu_sstitle06.gif" WIDTH=101 HEIGHT=25 ALT=""></td>
                </tr>
                <tr>
					<td class="table_td1">공구희망자가 최소인원을 초과할 경우 공구가 성사되며 결제가 진행됩니다.</td>
                </tr>
                <tr>
					<td height=20></td>
                </tr>
                <tr>
					<td height="20"><IMG SRC="../images/design/gonggu_img03.gif" WIDTH=930 HEIGHT=517 ALT=""></td>
                </tr>
                <tr>
					<td height="60"></td>
                </tr>
                <tr>
					<td><IMG SRC="../images/design/gonggu_stitle04.gif" WIDTH=179 HEIGHT=50 ALT=""></td>
                </tr>
                <tr>
					<td><IMG SRC="../images/design/gonggu_sstitle07.gif" WIDTH=208 HEIGHT=25 ALT=""></td>
                </tr>
                <tr>
					<td class="table_td1">마이페이지&gt;공동구매 정보에서 공구 신청/구매 내역을 확인하실 수 있습니다.<br>- 공구 제안상품 내역<br>- 공구 신청상품 내역<br>- 공구 구매 내역</td>
                </tr>
                <tr>
					<td height="30"></td>
                </tr>
				<tr>
					<td><IMG SRC="../images/design/gonggu_sstitle08.gif" WIDTH=184 HEIGHT=25 ALT=""></td>
				</tr>
				<tr>
					<td class="table_td1">고객님께서 서비스 받고 계신 계정에 겟몰솔루션 설치 후(오픈소스 특성상) 환불은 불가능합니다.</td>
				</tr>
				<tr>
					<td height="100"></td>
				</tr>
			</table>
        </td>
    </tr>
</table>





<? include ($Dir."lib/bottom.php") ?>

</BODY>
</HTML>