<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata2.php");

?>

<html>
<head>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<link rel="stylesheet" href="<?=$Dir?>lib/style.css">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<script language="JavaScript">
function CheckLogin() {
	if (!document.loginform.mem_id.value) {
		alert("아이디를 입력하세요.");
		document.loginform.mem_id.focus();
		return false;
	}
	if (!document.loginform.mem_pw.value) {
		alert("패스워드를 입력하세요.");
		document.loginform.mem_pw.focus();
		return false;
	}
	<?if($_data->ssl_type=="Y" && strlen($_data->ssl_domain)>0 && strlen($_data->ssl_port)>0 && $_data->ssl_pagelist["ADMIN"]=="Y") {?>
	if(typeof document.loginform.ssllogin!="undefined") {
		if(document.loginform.ssllogin.checked==true) {
			document.loginform.action='https://<?=$_data->ssl_domain?><?=($_data->ssl_port!="443"?":".$_data->ssl_port:"")?>/<?=RootPath.SecureDir?>adminlogin.php';
		}
	}
	<?}?>
}

function sslinfo() {
	window.open("<?=$Dir.FrontDir?>sslinfo.php","sslinfo","width=100,height=100,scrollbars=no");
}

</script>
</head>
<body onload="document.loginform.mem_id.focus()" BGCOLOR=#FFFFFF LEFTMARGIN=0 TOPMARGIN=0 MARGINWIDTH=0 MARGINHEIGHT=0 background="images/login_bg.jpg">

<table cellpadding="0" cellspacing="0" width="840" align="center">
<form action="loginproc.php" method=post name=loginform onsubmit="return CheckLogin();">
<?if($_data->ssl_type=="Y" && strlen($_data->ssl_domain)>0 && strlen($_data->ssl_port)>0 && $_data->ssl_pagelist["ADMIN"]=="Y") {?>
<input type=hidden name=shopurl value="<?=getenv("HTTP_HOST")?>">
<?}?>
<tr>
	<td colspan="3"  height="146"></td>
</tr>
<tr>
    <tr>
        <td colspan="3"  height="23" background="images/login_o_t_topbg.jpg"></td>
    </tr>
    <tr>
        <td width="22" background="images/login_o_t_leftbg.jpg"></td>
		<td>
			<TABLE  BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD width="429" background="images/login_table_bg.jpg" valign="top">
                        <table align="center" cellpadding="0" cellspacing="0" width="80%">
                            <tr>
                                <td  height="30"></td>
                            </tr>
                            <tr>
                                <td ><img src="images/admin_img01.gif" width="136" height="63" border="0"></td>
                            </tr>
                            <tr>
                                <td  height="13"></td>
                            </tr>
                            <tr>
                                <td >
                                    <table cellpadding="0" cellspacing="0" width="100%">
                                        <tr>
                                            <td width="236" style="padding-bottom:4px"><input type="text" name="mem_id" value="" size="20" maxlength="20" style="color:#333333; background-color:rgb(242,242,242); border-width:1px; border-color:#EBEBEB; border-style:solid; height:32px;width:96%;font-size:16px;font-weight:bold;padding-top:6px; padding-left:6px;" tabindex="1"></td>
                                            <td width="107" rowspan="2"><input type=image src="images/admin_btn01.gif" width="70" height="70" border="0"  tabindex="3"></td>
                                        </tr>
                                        <tr>
                                            <td width="236"><input type="password" name="mem_pw" value="" size="20" maxlength="20" style="color:#333333; background-color:rgb(242,242,242); border-width:1px; border-color:#EBEBEB; border-style:solid; height:32px;width:96%;font-size:16px;font-weight:bold;padding-top:6px; padding-left:6px;" tabindex="2"></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td  height="27">
					<?if($_data->ssl_type=="Y" && strlen($_data->ssl_domain)>0 && strlen($_data->ssl_port)>0 && $_data->ssl_pagelist["ADMIN"]=="Y") {?>
					<input type=checkbox name=ssllogin value="Y" checked> <A HREF="javascript:sslinfo()">보안 접속</A>
					<?}?>
								</td>
                            </tr>
                            <tr>
                                <td ><span style="color:#818289;font-family:돋움;font-size:11px;letter-spacing:0pt;line-height:16px"><?=getenv("HTTP_HOST")?>에서 부여받은 <b>운영자/부운영자</b>의<br>ID와 Password를 입력하세요.<br><img width=0 height=3><br><b>비밀번호관리에 주의하세요!</b></span></td>
                            </tr>
                        </table>
					</TD>
					<TD width="366" background="images/login_banner_bg.jpg" valign="top">
					<table  cellpadding="0" cellspacing="0" width="345" style="margin-top:8px;margin-left:10px;">
						<tr>
							<td><iframe src="http://www.getmall.co.kr/frames/login_banner.php"  WIDTH="345px" height="190px" frameborder="0" scrolling="no" marginwidth="0" marginheight="0" name="loginbanner" ></iframe></td>
						</tr>
						<!-- <tr>
							<td>
							<iframe src="http://www.getmall.co.kr/frames/login_banner.php"  WIDTH="345px" height="190px" frameborder="0" scrolling="no" name="loginbanner" ></iframe>
							<a href="http://getmallnew.objet.co.kr/front/cevent_view.php?seq=9&etc=B" target="_blank"><img src="http://getmallnew.objet.co.kr/getmallbanner/login_banner02.gif" border="0" width="345" height="190"></a> <iframe height="190px" width="345px" frameborder="0" scrolling="no"  name="loginbanner" src="http://www.getmall.co.kr/frames/login_banner.php"></iframe>
							</td>
						</tr> -->
						<tr>
							<td height="5"></td>
						</tr>
						<tr>
							<td align="center">
								<table cellpadding="0" cellspacing="0" width="90%">
									<tr>
										<td colspan="2" style="padding-top:5px;padding-bottom:10px;"><img src="images/login_notice.gif" border="0"></td>
									</tr>
									<tr>
										<td>
											<table cellpadding="0" cellspacing="0" width="100%"><iframe src="http://www.getmall.co.kr/frames/login_notice.php"  WIDTH="100%" height="100px" frameborder="0" scrolling="no" marginwidth="0" marginheight="0" name="loginnotice" ></iframe>	</table>
										</td>
									</tr>
								 </table>
							</td>
						</tr>
						<!-- <tr>
							<td align="center">
								<table cellpadding="0" cellspacing="0" width="90%">
									<tr>
										<td colspan="2" style="padding-top:5px;padding-bottom:10px;"><img src="images/login_notice.gif" border="0"></td>
									</tr>
									<tr>
										<td height="20"><img src="images/login_icon_point.gif" border="0"></td>
										<td><a href="http://getmallnew.objet.co.kr/board/board.php?pagetype=view&num=4&board=notice" target="_blank">[2/16] 뉴스 기사 내 북마크 기능 적용 .. new</a><img src="images/login_new_icon.gif" align="absmiddle" hspace="3"></td>
									</tr>
									<tr>
										<td height="20"><img src="images/login_icon_point.gif" border="0"></td>
										<td><a href="http://getmallnew.objet.co.kr/board/board.php?pagetype=view&num=4&board=notice" target="_blank">[2/16] 미투데이 채팅 오픈 안내 new</a><img src="images/login_new_icon.gif" align="absmiddle" hspace="3"></td>
									</tr>
									<tr>
										<td height="20"><img src="images/login_icon_point.gif" border="0"></td>
										<td><a href="http://getmallnew.objet.co.kr/board/board.php?pagetype=view&num=4&board=notice" target="_blank">[이벤트] 앱플레이어 출시 기념 이벤..</a></td>
									</tr>
									<tr>
										<td height="20"><img src="images/login_icon_point.gif" border="0"></td>
										<td><a href="http://getmallnew.objet.co.kr/board/board.php?pagetype=view&num=4&board=notice" target="_blank">[1/31] 뉴스캐스트 구독 추가 안내</a></td>
									</tr>
								 </table>
							</td>
						</tr> -->
						<tr><td height="18"></td></tr>
					 </table>
					</TD>
				</TR>
			</TABLE>
        </td>
        <td width="23" background="images/login_o_t_rightbg.jpg"></td>
    </tr>
    <tr>
        <td colspan="3" width="840" height="57" background="images/login_o_t_downbg.jpg"></td>
    </tr>
    <tr>
        <td colspan="3" align="center"><a href="http://getmall.co.kr" target="_blank"><img src="images/login_getmall_logo.gif" border="0" align="absmiddle"><span style="font-family:Verdana; font-size:9px;"><font color="#999999">Copyright&copy;</font><font color="#FF6600"><b>getmall.co.kr</b></font><font color="#999999">.
All Rights reserved.</font></span></a></td>
    </tr>
</form>
</table>
</body>
</html>