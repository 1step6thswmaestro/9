<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

if(strlen($_ShopInfo->getMemid())==0) {
	echo "<script>window.close();</script>"; exit;
}

$idx=$_POST["idx"];

$sql = "SELECT * FROM tblpersonal WHERE id='".$_ShopInfo->getMemid()."' AND idx='".$idx."' ";
$result=mysql_query($sql,get_db_conn());
if($row=mysql_fetch_object($result)) {
	$_pdata=$row;
} else {
	echo "<html></head><body onload=\"alert('해당 문의내역이 없습니다.');window.close();\"></body></html>";exit;
}
mysql_free_result($result);
?>

<html>
<head>
<title>1:1고객문의 확인</title>
<meta http-equiv="CONTENT-TYPE" content="text/html;charset=EUC-KR">
<META http-equiv="X-UA-Compatible" content="IE=5" >
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<style>
td	{font-family:"굴림,돋움";color:#4B4B4B;font-size:12px;line-height:17px;}
BODY,DIV,form,TEXTAREA,center,option,pre,blockquote {font-family:Tahoma;color:000000;font-size:9pt;}

A:link    {color:#635C5A;text-decoration:none;}
A:visited {color:#545454;text-decoration:none;}
A:active  {color:#5A595A;text-decoration:none;}
A:hover  {color:#545454;text-decoration:underline;}
.input{font-size:12px;BORDER-RIGHT: #DCDCDC 1px solid; BORDER-TOP: #C7C1C1 1px solid; BORDER-LEFT: #C7C1C1 1px solid; BORDER-BOTTOM: #DCDCDC 1px solid; HEIGHT: 18px; BACKGROUND-COLOR: #ffffff;padding-top:2pt; padding-bottom:1pt; height:19px}
.select{color:#444444;font-size:12px;}
.textarea {border:solid 1;border-color:#e3e3e3;font-family:돋음;font-size:9pt;color:333333;overflow:auto; background-color:transparent}
</style>
<SCRIPT LANGUAGE="JavaScript">
<!--
window.moveTo(10,10);
window.resizeTo(577,480);
//-->
</SCRIPT>
</head>

<body topmargin="0" leftmargin="0" rightmargin="0" marginheight="0" marginwidth="0">
<TABLE WIDTH="100%" BORDER="0" CELLPADDING="0" CELLSPACING="0">
<TR>
	<TD><img src="<?=$Dir?>images/common/mypersonal_popup_title.gif" border="0"></TD>
</TR>
<TR>
	<TD style="padding-left:15px;padding-right:15px;">
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td>
		<TABLE width="100%" cellSpacing="0" cellPadding="0" border="0">
		<col width="100" bgcolor="#F8F8F8" style="letter-spacing:-0.5pt;"></col>
		<col></col>
		<TR>
			<TD height="1" colspan="2" bgcolor="#B9B9B9"></TD>
		</TR>
		<TR>
			<TD style="padding:6px;"><img src="<?=$Dir?>images/common/mypersonal_popup_point2.gif" border="0"><b>문의제목</b></TD>
			<TD style="padding:6px;BORDER-LEFT:#E3E3E3 1px solid;"><font color="#FF4C00"><B><?=$_pdata->subject?></B></font></TD>
		</TR>
		<TR>
			<TD height="1" colspan="2" bgcolor="#EDEDED"></TD>
		</TR>
		<TR>
			<TD style="padding:6px;"><img src="<?=$Dir?>images/common/mypersonal_popup_point2.gif" border="0">이메일</TD>
			<TD style="padding:6px;BORDER-LEFT:#E3E3E3 1px solid;"><?=$_pdata->email?></TD>
		</TR>
		<TR>
			<TD height="1" colspan="2" bgcolor="#EDEDED"></TD>
		</TR>
		<TR>
			<TD style="padding:6px;"><img src="<?=$Dir?>images/common/mypersonal_popup_point2.gif" border="0">답변여부</TD>
			<TD style="padding:6px;BORDER-LEFT:#E3E3E3 1px solid;">
<?
			$re_date="";
			if(strlen($_pdata->re_date)==14) {
				$re_date = substr($_pdata->re_date,0,4)."/".substr($_pdata->re_date,4,2)."/".substr($_pdata->re_date,6,2);
			}
			if(strlen($row->re_date)==14) {
				echo "<font color=\"#0000FF\"><B>답변이 완료되었습니다.</B></font> <font color=\"#000000\">(".$re_date.")</font>";
			} else {
				echo "<font color=\"#FF0000\"><B>답변 대기중입니다.</B></font>";
			}
?>
			</TD>
		</TR>
		<TR>
			<TD height="1" colspan="2" bgcolor="#EDEDED"></TD>
		</TR>
		<TR>
			<TD colspan="2" style="padding:8px"><img src="<?=$Dir?>images/common/mypersonal_popup_qicon.gif" border="0"><br><?=nl2br($_pdata->content)?></TD>
		</TR>
		<TR>
			<TD height="1" colspan="2" bgcolor="#EDEDED"></TD>
		</TR>
		<TR>
			<TD style="padding:8px" colspan="2" bgcolor="#FFFAEC"><img src="<?=$Dir?>images/common/mypersonal_popup_aicon.gif" border="0"><br><font color="#A37736"><?=nl2br($_pdata->re_content)?></font></TD>
		</TR>
		<TR>
			<TD height="1" colspan="2" bgcolor="#B9B9B9"></TD>
		</TR>
		</TABLE>
		</td>
	</tr>
	<tr>
		<td height="20"></td>
	</tr>
	<tr>
		<td align="center"><a href="javascript:window.close()"><img src="<?=$Dir?>images/common/mypersonal_popup_close.gif" border="0"></a></td>
	</tr>
	<tr>
		<td height="20"></td>
	</tr>
	</table>
	</TD>
</TR>
</TABLE>
</body>
</html>