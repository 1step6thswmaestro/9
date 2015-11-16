<?
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	INCLUDE ("access.php");
?>

<? INCLUDE "header.php"; ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
	function CheckForm() {
	}
</script>

<style>
	.wrap_hiworks {width:736px; margin-left:24px;}
	.wrap_hiworks a:link {font-size:11px;}
	.wrap_hiworks a:hover {font-size:11px; color:#0099cc;}

	.wrap_hiworks div {margin-bottom:40px;}
	.wrap_hiworks span,dd {margin:0px; padding:0px; padding-left:35px; font-family:돋움; font-size:11px; color:#929292; letter-spacing:-1px; line-height:16px; padding-bottom:20px;}
</style>

<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
<tr>
	<td valign="top">
	<table cellpadding="0" cellspacing="0" width=100% style="table-layout:fixed">
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed"  background="images/con_bg.gif">
		<col width=198></col>
		<col width=10></col>
		<col width=></col>
		<tr>
			<td valign="top"  background="images/leftmenu_bg.gif">
			<? include ("menu_order.php"); ?>
			</td>

			<td></td>
			<td valign="top">




<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td height="29" colspan="3">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" width="16" height="12" border="0" valign=absmiddle>현재위치 : 주문/매출 &gt; 전자세금계산서 관리 &gt; <span class="2depth_select">서비스 신청방법</span></td>
			</tr>
			</table>
		</td>
	</tr>   
	<tr>
        <td width="16"><img src="images/con_t_01.gif" width="16" height="16" border="0"></td>
        <td background="images/con_t_01_bg.gif"></td>
        <td width="16"><img src="images/con_t_02.gif" width="16" height="16" border="0"></td>
    </tr>
    <tr>
        <td width="16" background="images/con_t_04_bg1.gif"></td>
        <td bgcolor="#ffffff" style="padding:10px">

			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td height="8"></td>
			</tr>
			<tr>
				<td>
					<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
						<TR>
							<TD><IMG SRC="images/billuse_title.gif"  ALT=""></TD>
						</tr>
						<tr>
							<TD width="100%" background="images/title_bg.gif" height="21"></TD>
						</TR>
					</TABLE>
				</td>
			</tr>
			<tr>
				<td height="3"></td>
			</tr>
			<tr>
				<td style="padding-bottom:3pt;">
					<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
						<TR>
							<TD><IMG SRC="images/distribute_01.gif"></TD>
							<TD COLSPAN=2 background="images/distribute_02.gif"></TD>
							<TD><IMG SRC="images/distribute_03.gif"></TD>
						</TR>
						<TR>
							<TD background="images/distribute_04.gif"><IMG SRC="images/distribute_04.gif" ></TD>
							<TD class="notice_blue"><IMG SRC="images/distribute_img.gif" ></TD>
							<TD width="100%" class="notice_blue"><p>전자세금계산서 서비스 신청 절차 안내입니다.</p></TD>
							<TD background="images/distribute_07.gif"><IMG SRC="images/distribute_07.gif" ></TD>
						</TR>
						<TR>
							<TD><IMG SRC="images/distribute_08.gif"></TD>
							<TD COLSPAN=2 background="images/distribute_09.gif"></TD>
							<TD><IMG SRC="images/distribute_10.gif"></TD>
						</TR>
					</TABLE>
				</td>
			</tr>
			<tr><td height="20"></td></tr>
			<tr>
				<td><img src="images/billuse_sstitle.gif" alt="하이웍스 서비스 신청 절차" /></td>
			</tr>
			<tr><td height="15"></td></tr>
			<tr>
				<td>

					<div class="wrap_hiworks">
						<div>
							<dl>
								<dt><img src="images/billuse_stitle01.gif" alt="01. 하이웍스 회원가입" /></dt>
								<dd>
									하이웍스 홈페이지를 방문하셔서 회원가입을 진행합니다. <a href="https://www.hiworks.co.kr/about/bill" target="_blank"><span style="padding:0px; font-size:11px; color:#0099cc; letter-spacing:0px;">(https://www.hiworks.co.kr/about/bill)</span></a><br />
									- 하이웍스에 이미 가입되어 있거나 가비아 회원이라면 [로그인] 버튼을 눌러 바로 로그인하시면 됩니다.<br />
									- 가비아 회원은 보유한 아이디로 회원가입 없이 이용하실 수 있습니다.<br /><br /><br />
									<img src="http://www.getmall.co.kr/manual/data/hiworks_img01.gif" alt="" />
								</dd>
							</dl>
						</div>

						<div>
							<dl>
								<dt><img src="images/billuse_stitle02.gif" alt="02. 하이웍스 시작하기" /></dt>
								<dd>
									회원가입 완료 후 [하이웍스 시작하기 계속] 메뉴를 통해서 [하이웍스 오피스 만들기]를 진행합니다.<br /><br /><br />
									<img src="http://www.getmall.co.kr/manual/data/hiworks_img02.gif" alt="" /><br /><br /><br />
									<img src="http://www.getmall.co.kr/manual/data/hiworks_img03.gif" alt="" />
								</dd>
							</dl>
						</div>

						<div>
							<dl>
								<dt><img src="images/billuse_stitle03.gif" alt="03. 하이웍스 오피스 만들기" /></dt>
								<dd>
									오피스 만들기에서 담당자 및 사업자 정보를 입력합니다.<br />
									오피스 주소는 수정이 불가하니 정확하게 입력해 주시기 바라며, 오피스 이름 및 담당자는 오피스 생성 후 하이웍스 관리자<br />페이지에서 수정 가능합니다.<br /><br />
									<div style="margin-top:10px;"><img src="http://www.getmall.co.kr/manual/data/hiworks_img04.gif" alt="" /></div>
								</dd>
							</dl>
						</div>

						<div>
							<dl>
								<dt><img src="images/billuse_stitle04.gif" alt="04. 전자세금계산서 서비스 신청" /></dt>
								<dd>
									① 오피스 생성 완료 후 로그인을 하면 좌측 상단 메뉴 중 [전자세금계산서] 세금계산서 메뉴를 확인할 수 있습니다.<br /><br /><br />
									<img src="http://www.getmall.co.kr/manual/data/hiworks_img05.gif" alt="" /><br /><br /><br />
								</dd>
								<dd>
									② [전자세금계산서] 클릭시 일반회원은 해당 기능을 사용할 수 없다는 안내문구가 출력되며, 메시지 확인 후 이동되는 페이지에서<br />좌측 상단의 [관리자] 메뉴로 들어갑니다.<br /><br /><br />
									<img src="http://www.getmall.co.kr/manual/data/hiworks_img06.gif" alt="" /><br /><br /><br />
								</dd>
								<dd>
									③ [관리자] 메뉴 좌측 상단의 [서비스 연장] 버튼을 클릭해서 사용중인 서비스 관리 페이지로 이동합니다.<br /><br /><br />
									<img src="http://www.getmall.co.kr/manual/data/hiworks_img07.gif" alt="" /><br /><br /><br />
								</dd>
								<dd>
									④ 서비스 관리 페이지에서 [부가서비스 관리] 의 전자세금계산서 [서비스 신청] 버튼을 누르시면 신청 페이지로 이동됩니다.<br />
									서비스 신청시 최초 설치비 5,500원(VAT포함)이 부과되며, 결제타입 선택 후 결제 진행을 하시면 서비스 신청이 완료됩니다.<br /><br /><br />
									<img src="http://www.getmall.co.kr/manual/data/hiworks_img08.gif" alt="" />
								</dd>
							</dl>
						</div>
					</div>

				</td>
			</tr>
			<tr>
				<td height="20"></td>
			</tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/manual_top1.gif" WIDTH=15 height="45" ALT=""></TD>
					<TD><IMG SRC="images/manual_title.gif" WIDTH=113 height="45" ALT=""></TD>
					<TD width="100%" background="images/manual_bg.gif" height="35"></TD>
					<TD background="images/manual_bg.gif"></TD>
					<td background="images/manual_bg.gif"><IMG SRC="images/manual_top2.gif" WIDTH=18 height="45" ALT=""></td>
				</TR>
				<TR>
					<TD background="images/manual_left1.gif"><IMG SRC="images/manual_left1.gif" WIDTH=15 HEIGHT="5" ALT=""></TD>
					<TD COLSPAN=3 width="100%" valign="top" bgcolor="white" style="padding-top:8pt; padding-bottom:8pt; padding-left:4pt;" class="menual_bg">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td ><span class="font_dotline">전자세금계산서 연동</span></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td  class="space_top" style="letter-spacing:-0.5pt;"><p>- 최초 설치비 처리완료 후 하이웍스 관리자 페이지에서 연동정보를 확인할 수 있습니다.</p></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td  class="space_top" style="letter-spacing:-0.5pt;"><p>- [하이웍스 관리자 > 계산서 > 세금계산서 연동정보]에서 개설 오피스주소/ID/KEY 정보를 그대로 [전자세금계산서 설정]에 등록하시면 됩니다.</p></td>
					</tr>
					</table>
					</TD>
					<TD background="images/manual_right1.gif"><IMG SRC="images/manual_right1.gif" WIDTH=18 HEIGHT="2" ALT=""></TD>
				</TR>
				<TR>
					<TD><IMG SRC="images/manual_left2.gif" WIDTH=15 HEIGHT=8 ALT=""></TD>
					<TD COLSPAN=3 background="images/manual_down.gif"><IMG SRC="images/manual_down.gif" WIDTH="4" HEIGHT=8 ALT=""></TD>
					<TD><IMG SRC="images/manual_right2.gif" WIDTH=18 HEIGHT=8 ALT=""></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height="50"></td>
			</tr>
			</table>
</td>
        <td width="16" background="images/con_t_02_bg.gif"></td>
    </tr>
    <tr>
        <td width="16"><img src="images/con_t_04.gif" width="16" height="16" border="0"></td>
        <td background="images/con_t_04_bg.gif"></td>
        <td width="16"><img src="images/con_t_03.gif" width="16" height="16" border="0"></td>
    </tr>
    <tr><td height="20"></td></tr>
</table>


			</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>
</table>

<?=$onload?>

<? INCLUDE "copyright.php"; ?>