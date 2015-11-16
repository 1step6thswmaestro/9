<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

if(strlen($_ShopInfo->getMemid())==0) {
	exit;
}
?>

<html>
<head>
<title>과거 배송지 검색</title>
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
window.resizeTo(476,350);

function choice_addr(post,addr1,addr2) {
	if(post.length != 5){
		alert('해당 주소지는 우편번호가 갱신되지 않아 사용할 수 없습니다.');
		return;
	}
	opener.document.form1.rpost1.value=post;
	opener.document.form1.raddr1.value=addr1;
	opener.document.form1.raddr2.value=addr2;
	window.close();
}
//-->
</SCRIPT>
</head>

<body topmargin="0" leftmargin="0" rightmargin="0" marginheight="0" marginwidth="0" oncontextmenu="return false;">
<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
<tr>
	<td><IMG  src="<?=$Dir?>images/common/lastadress_title.gif" border="0"></td>
</tr>
<tr>
	<td style="padding-left:5px;padding-right:5px;">
	<table cellpadding="0" cellspacing="0" width="100%" bordercolordark="black" bordercolorlight="black" style="table-layout:fixed;">
	<tr>
		<td class="padding-top:5pt;font-size:11px;letter-spacing:-0.5pt;">* 고객님의 지난 배송지 리스트입니다.</td>
	</tr>
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%">
		<col width=60></col>
		<col width=></col>
		<col width=50></col>
		<tr>
			<td height="2" colspan="3" bgcolor="#000000"></td>
		</tr>
		<tr bgcolor="#F8F8F8" align="center" height="30">
			<td><font color="#333333"><b>우편번호</b></font></td>
			<td><font color="#333333"><b>배송지 주소</b></font></td>
			<td><font color="#333333"><b>선택</b></font></td>
		</tr>
		<tr>
			<td height="1" colspan="3" bgcolor="#DDDDDD"></td>
		</tr>
<?
		$sql = "SELECT receiver_addr FROM tblorderinfo WHERE id='".$_ShopInfo->getMemid()."' ";
		$sql.= "GROUP BY receiver_addr ";
		$result=mysql_query($sql,get_db_conn());
		$cnt=0;
		while($row=mysql_fetch_object($result)) {
			$cnt++;
			$receiver_addr = eregi_replace("\n"," ",trim($row->receiver_addr));
			$receiver_addr = eregi_replace("\r"," ",$receiver_addr);
			$pos=strpos($receiver_addr,"주소");
			if($pos>0) {
				$post = trim(substr($receiver_addr,0,$pos));
				$receiver_addr = substr($receiver_addr,$pos+7);
				$arrayaddr = explode("  ",$receiver_addr);
				$count=count($arrayaddr);
				if($count>1) {
					$addr1=$arrayaddr[0];
					$addr2=" ";
					for($i=1;$i<$count;$i++) $addr2.=$arrayaddr[$i]." ";
					if(strlen($addr2)>1) $addr2=substr($addr2,0,-1);
				} else {
					$arrayaddr = explode(" ",$receiver_addr);
					$count=count($arrayaddr);
					if($count>3) {
						$addr1=$arrayaddr[0]." ".$arrayaddr[1]." ".$arrayaddr[2];
						$addr2=" ";
						for($i=3;$i<$count;$i++) $addr2.=$arrayaddr[$i]." ";
						if(strlen($addr2)>1) $addr2=substr($addr2,0,-1);
					} else {
						$addr1=$receiver_addr;
						$addr2=" ";
					}
				}
			}
			$post = ereg_replace("우편번호 : ","",$post);
			$addr1=trim($addr1);
			$addr2=trim($addr2);

			echo "<tr align=\"center\" height=\"25\">\n";
			echo "	<td><A HREF=\"javascript:choice_addr('".$post."','".$addr1."','".$addr2."')\"><U><font color=\"#333333\">".$post."</font></U></A></td>\n";
			echo "	<td align=\"left\"><A HREF=\"javascript:choice_addr('".$post."','".$addr1."','".$addr2."')\"><font color=\"#333333\">".$addr1." ".$addr2."</font></a></td>\n";
			echo "	<td><a href=\"javascript:choice_addr('".$post."','".$addr1."','".$addr2."');\"><img src=\"".$Dir."images/common/lastadress_btn1.gif\" border=\"0\"></a></td>\n";
			echo "</tr>\n";
			echo "<tr><td height=\"1\" colspan=\"3\" bgcolor=\"#DDDDDD\"></td></tr>\n";
		}
		mysql_free_result($result);
		if($cnt==0) {
			echo "<tr height=\"30\">\n";
			echo "	<td colspan=\"3\" align=\"center\">검색된 과거 배송지가 없습니다.</td>\n";
			echo "</tr>\n";
			echo "<tr><td height=\"1\" colspan=\"3\" bgcolor=\"#DDDDDD\"></td></tr>\n";
		}
?>
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>
<tr>
	<td height="10"></td>
</tr>
<tr>
	<td align="center"><a href="javascript:window.close();"><img src="<?=$Dir?>images/common/bigview_btnclose.gif" border="0"></a></td>
</tr>
<tr>
	<td height="10"></td>
</tr>
</table>
</body>
</html>