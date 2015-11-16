<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
INCLUDE ("access.php");

if($_GET['vender']) $vender = $_GET['vender'];
?>

<html>
	<head>
		<meta http-equiv='Content-Type' content='text/html;charset=euc-kr'>
		<title>웹FTP</title>
		<link rel="stylesheet" href="style.css" type="text/css">
		<script type="text/javascript" src="../lib/jquery-1.4.2.min.js"></script>
		<script type="text/javascript" src="./uploadify/swfobject.js"></script>
		<script type="text/javascript" src="./uploadify/jquery.uploadify.js"></script>
		<script type="text/javascript">
			<!--
			function PageResize() {
				var oWidth = document.all.table_body.clientWidth + 10;
				var oHeight = document.all.table_body.clientHeight + 200;

				window.resizeTo(oWidth,oHeight);
				setPopup("popup");
			}
			//-->
		</SCRIPT>
		<style type="text/css">
		</style>
	</head>
	<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 style="overflow-x:hidden;overflow-y:hidden;" onLoad="PageResize();">
		<TABLE WIDTH="800" BORDER=0 CELLPADDING=0 CELLSPACING=0 id=table_body>
			<tr>
				<td>		
					<table cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td><img src="images/design_webftp_wintitle.gif" border="0"></td>
							<td background="images/member_mailallsend_imgbg.gif" width=100%></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td style="padding:20">
					<?include("design_webftp_display.php");?>
				</td>
			</tr>
			<TR>
				<TD align=center>
					<a href="javascript:window.close()"><img src="images/btn_close.gif" border="0" border=0></a>	
				</TD>
			</TR>
			<tr><td height=10></td></tr>
		</TABLE>

		<form name=form3 method=post target="webftpetcpop">
			<input type=hidden name=val>
			<input type=hidden name=vender value="<?=$vender?>">
		</form>

	</body>
</html>