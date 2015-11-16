<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
INCLUDE ("access.php");

if(strlen($_ShopInfo->getId())==0) {
	echo "<script>alert('정상적인 경로로 접근하시기 바랍니다.');window.close();</script>";
	exit;
}

$ordercode=substr($_POST["ordercodes"],0,-1);
$gbn=$_POST["gbn"];

$CurrentTime = time();

if(strlen($ordercode)==0) {
	echo "<script>alert('정상적인 경로로 접근하시기 바랍니다.');window.close();</script>";
	exit;
}

$card_payfee=$_shopdata->card_payfee;

$sql = "SELECT vendercnt FROM tblshopcount ";
$result=mysql_query($sql,get_db_conn());
$row=mysql_fetch_object($result);
$vendercnt=$row->vendercnt;
mysql_free_result($result);

if($vendercnt>0){
	$venderlist=array();
	$sql = "SELECT vender,id,com_name,delflag FROM tblvenderinfo ORDER BY id ASC ";
	$result=mysql_query($sql,get_db_conn());
	while($row=mysql_fetch_object($result)) {
		$venderlist[$row->vender]=$row;
	}
	mysql_free_result($result);
}

$delicomlist=array();
//$sql="SELECT * FROM tbldelicompany ORDER BY company_name ";
$sql="SELECT * FROM tbldelicompany ORDER BY code ";

$result=mysql_query($sql,get_db_conn());
while($row=mysql_fetch_object($result)) {
	$delicomlist[]=$row;
}
mysql_free_result($result);

?>

<html>
<head>
<title>주문서 출력</title>
<meta http-equiv="CONTENT-TYPE" content="text/html;charset=EUC-KR">
<STYLE TYPE="text/css"> 
<!--
body { font-size: 9pt}
td { font-size: 9pt}
tr { font-size: 9pt}
.break {page-break-before: always;}
--> 
</STYLE> 
<SCRIPT LANGUAGE="JavaScript">
<!--
document.onkeydown = CheckKeyPress;
document.onkeyup = CheckKeyPress;
function CheckKeyPress() {
	ekey = event.keyCode;

	if(ekey == 38 || ekey == 40 || ekey == 112 || ekey ==17 || ekey == 18 || ekey == 25 || ekey == 122 || ekey == 116) {
	   event.keyCode = 0;
	   return false;
	 }
}
//-->
</SCRIPT>
</head>
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 oncontextmenu="return false" style="overflow-x:hidden;overflow-y:hidden;" oncontextmenu="return false" onload="this.focus();print();">
<center>
<?
$arrordercode = explode(",",$ordercode);
$cnt = count($arrordercode);
for($i=0;$i<$cnt;$i++) {
	if($i<>0) echo "<H1 CLASS=\"break\"><br style=\"height=0;line-height:0;\">\n";
?>

<table border=0 cellpadding=0 cellspacing=0 width=96%>
<tr><td align=center><B>주문해주셔서 감사합니다.</B></td></tr>
<tr><td height=10></td></tr>
</table>

<table border=1 cellpadding=3 cellspacing=0 width=645 bgcolor=#FFFFFF style="table-layout:fixed">
<col width=20></col>
<?if($vendercnt>0 && $gbn=="Y"){?>
<col width=60></col>
<?}?>
<col width=></col>
<col width=95></col>
<col width=30></col>
<col width=50></col>
<col width=60></col>
<tr bgcolor=#f4f4f4>
	<td align=center>No</td>
	<?if($vendercnt>0 && $gbn=="Y"){?>
	<td align=center>입점업체</td>
	<?}?>
	<td align=center>상품명</td>
	<td align=center>선택사항</td>
	<td align=center>수량</td>
	<td align=center>적립금</td>
	<td align=center>가격</td>
</tr>
<?
$colspan=6;
if($vendercnt>0 && $gbn=="Y") $colspan++;

$sql = "SELECT * FROM tblorderinfo WHERE ordercode='".$arrordercode[$i]."' ";
$result=mysql_query($sql,get_db_conn());
$_ord=mysql_fetch_object($result);
mysql_free_result($result);

$sql = "SELECT * FROM tblorderproduct WHERE ordercode='".$arrordercode[$i]."' ";
$result=mysql_query($sql,get_db_conn());
$no=0;
$sumprice=0;
$sumreserve=0;
$totprice=0;
$totreserve=0;
$totquantity=0;
unset($etcdata);
unset($prdata);
while($row=mysql_fetch_object($result)) {
	if(ereg("^(COU)([0-9]{8})(X)$",$row->productcode)) {				#쿠폰
		if($row->price!=0 && $row->price!=NULL) {
			$etcdata[]=$row;
			continue;
		}
	} else if(ereg("^(9999999999)([0-9]{1})(X|R)$",$row->productcode)) {
		#99999999999X : 현금결제시 결제금액에서 추가적립/할인
		#99999999998X : 에스크로 결제시 수수료
		#99999999997X : 부가세(VAT)
		#99999999990X : 상품배송비
		#99999999999R : 카드수수료
		$etcdata[]=$row;
		continue;
	} else {															#진짜상품
		$prdata[]=$row;
	}

	$no++;
	$optvalue="";
	if(ereg("^(\[OPTG)([0-9]{3})(\])$",$row->opt1_name)) {
		$optioncode=$row->opt1_name;
		$row->opt1_name="";
		$sql = "SELECT opt_name FROM tblorderoption WHERE ordercode='".$arrordercode[$i]."' ";
		$sql.= "AND productcode='".$row->productcode."' AND opt_idx='".$optioncode."' ";
		$result2=mysql_query($sql,get_db_conn());
		if($row2=mysql_fetch_object($result2)) {
			$optvalue=$row2->opt_name;
		}
		mysql_free_result($result2);
	}

	$sumprice=$row->price*$row->quantity;
	$totprice+=$sumprice;
	$isnot=false;
	if ($row->productcode!="99999999999X" && substr($row->productcode,0,3)!="COU" && $row->productcode!="99999999999R") {
		$totquantity+=$row->quantity;
		$isnot=true;
	}
	$sumreserve=$row->reserve*$row->quantity;
	$totreserve+=$sumreserve;

	$assemblestr = "";
	$packagestr = "";
	if(($_ord->paymethod!="B" || $mode!="update") && $row->assemble_idx>0 && strlen(str_replace("","",str_replace(":","",$row->assemble_info)))>0) {
		$assemble_infoall_exp = explode("=",$row->assemble_info);

		if($row->package_idx>0 && strlen(str_replace("","",str_replace(":","",$assemble_infoall_exp[0])))>0) {
			$package_info_exp = explode(":", $assemble_infoall_exp[0]);

			$package_productcode_exp = explode("", $package_info_exp[0]);
			$package_productname_exp = explode("", $package_info_exp[1]);
			$package_sellprice = $package_info_exp[2];
			$package_packagename = $package_info_exp[3];
			
			if(count($package_info_exp)>2 && strlen($package_packagename)>0) {
				$packagestr.="	<table border=0 width=\"100%\" cellpadding=\"0\" cellspacing=\"0\">\n";
				$packagestr.="	<tr>\n";
				$packagestr.="		<td colspan=\"2\" style=\"word-break:break-all;font-size:8pt;\"><font color=green><b>[</b>패키지선택 : ".$package_packagename."<b>]</b></font></td>\n";
				$packagestr.="	</tr>\n";
				if(strlen(str_replace("","",$package_info_exp[1]))>0) {
					$packagestr.="	<tr>\n";
					$packagestr.="		<td width=\"30\" valign=\"top\" nowrap><font color=\"#008000\" style=\"line-height:10px;\">│<br>└▶</font></td>\n";
					$packagestr.="		<td width=\"100%\" bgcolor=\"#DDDDDD\">\n";
					$packagestr.="		<table width=\"100%\" cellpadding=\"0\" cellspacing=\"1\">\n";
					$packagestr.="		<col width=\"\"></col>\n";
					$packagestr.="		<col width=\"55\"></col>\n";
					for($k=0; $k<count($package_productname_exp); $k++) {
						if($k==0) {
							$packagestr.="		<tr bgcolor=\"#FFFFFF\">\n";
							$packagestr.="				<td style=\"padding-left:4px;padding-right:4px;word-break:break-all;font-size:8pt;\">".$package_productname_exp[$k]."&nbsp;</td>\n";
							$packagestr.="				<td rowspan=\"".count($package_productname_exp)."\" align=\"right\" style=\"padding-left:4px;padding-right:4px;font-size:8pt;\">".number_format((int)$package_sellprice)."</td>\n";
							$packagestr.="		</tr>\n";
						} else {
							$packagestr.="		<tr bgcolor=\"#FFFFFF\">\n";
							$packagestr.="				<td style=\"padding-left:4px;padding-right:4px;word-break:break-all;font-size:8pt;\">".$package_productname_exp[$k]."&nbsp;</td>\n";
							$packagestr.="		</tr>\n";
						}
					}

					$packagestr.="		</table>\n";
					$packagestr.="		</td>\n";
					$packagestr.="	</tr>\n";
				}
				$packagestr.="	</table>\n";
			}
		}

		if($row->assemble_idx>0 && strlen(str_replace("","",str_replace(":","",$assemble_infoall_exp[1])))>0) {
			$assemblestr.="	<table border=0 width=\"100%\" cellpadding=\"0\" cellspacing=\"0\">\n";
			$assemblestr.="	<tr height=\"2\"><td></td></tr>\n";
			$assemblestr.="	<tr>\n";
			$assemblestr.="		<td width=\"30\" valign=\"top\" nowrap><font color=\"#FF7100\" style=\"line-height:10px;\">│<br>└▶</font></td>\n";
			$assemblestr.="		<td width=\"100%\" bgcolor=\"#DDDDDD\">\n";
			$assemblestr.="		<table width=\"100%\" cellpadding=\"0\" cellspacing=\"1\">\n";
			
			$assemble_info_exp = explode(":", $assemble_infoall_exp[1]);

			if(count($assemble_info_exp)>2) {
				$assemble_productcode_exp = explode("", $assemble_info_exp[0]);
				$assemble_productname_exp = explode("", $assemble_info_exp[1]);
				$assemble_sellprice_exp = explode("", $assemble_info_exp[2]);

				for($k=0; $k<count($assemble_productname_exp); $k++) {
					$assemblestr.="		<col width=\"\"></col>\n";
					$assemblestr.="		<col width=\"55\"></col>\n";
					$assemblestr.="		<tr bgcolor=\"#FFFFFF\">\n";
					$assemblestr.="				<td style=\"padding-left:4px;padding-right:4px;word-break:break-all;font-size:8pt;\">".$assemble_productname_exp[$k]."&nbsp;</td>\n";
					$assemblestr.="				<td align=\"right\" style=\"padding-left:4px;padding-right:4px;font-size:8pt;\">".number_format((int)$assemble_sellprice_exp[$k])."</td>\n";
					$assemblestr.="		</tr>\n";
				}
			}
			$assemblestr.="		</table>\n";
			$assemblestr.="		</td>\n";
			$assemblestr.="	</tr>\n";
			$assemblestr.="	</table>\n";
		}
	}

	echo "<tr bgcolor=#FFFFFF>\n";
	echo "	<td align=center style=\"font-size:8pt\">".$no."</td>\n";
	if($vendercnt>0 && $gbn=="Y") {
		if($row->vender>0) {
			echo "	<td align=center style=\"font-size:8pt\">".$venderlist[$row->vender]->id."</td>\n";
		} else {
			echo "	<td align=center>&nbsp;</td>\n";
		}
	}
	echo "	<td style=\"font-size:8pt;padding:2,5;line-height:10pt\">";
	echo $row->productname;
	if(strlen($row->addcode)>0) echo "<br><font color=blue><b>[</b>특수표시 : ".$row->addcode."<b>]</b></font>";
	if(strlen($optvalue)>0) echo "<br><font color=red><b>[</b>옵션사항 : ".$optvalue."<b>]</b></font>";
	echo $packagestr;
	echo $assemblestr;
	echo "	</td>\n";
	echo "	<td style=\"font-size:8pt;padding:2,5;line-height:11pt\">";
	echo (strlen($row->opt1_name)>0?$row->opt1_name."<br>":"&nbsp;");
	echo (strlen($row->opt2_name)>0?$row->opt2_name."<br>":"&nbsp;");
	echo (strlen($row->opt3_name)>0?$row->opt3_name."<br>":"&nbsp;");
	if(strlen($row->opt4_name)>0) echo $row->opt4_name;
	echo "	</td>\n";
	echo "	<td align=center style=\"font-size:8pt\">".($isnot==true?$row->quantity:"&nbsp;")."</td>\n";
	echo "	<td align=right style=\"font-size:8pt;padding-right:3\">".number_format($sumreserve)."</td>\n";
	echo "	<td align=right style=\"font-size:8pt;padding-right:3\">".number_format($sumprice)."</td>\n";
	echo "</tr>\n";
	echo "<tr bgcolor=#FFFFFF style=\"font-size:8pt\">\n";
	echo "	<td style=\"padding:2,5\" colspan=".$colspan.">\n";
	echo "	<table border=0 cellpadding=0 cellspacing=0 width=100% style=\"table-layout:fixed\">\n";
	echo "	<col width=180></col><col width=200></col><col width=></col>\n";
	echo "	<tr>\n";
	echo "		<td>배송업체 : \n";
	echo "		".(strlen($company_name)>0?$company_name:"없음")."\n";
	echo "		</td>\n";
	echo "		<td>송장번호 : \n";
	echo "		".(strlen($row->deli_num)>0?$row->deli_num:"없음")."\n";
	echo "		</td>\n";
	echo "		<td align=right>\n";
	echo "	배송상태 : <B>";
	switch($row->deli_gbn) {
		case 'S': echo "발송준비";  break;
		case 'X': echo "배송요청";  break;
		case 'Y': echo "배송";  break;
		case 'D': echo "<font color=blue>취소요청</font>";  break;
		case 'N': echo "미처리";  break;
		case 'E': echo "<font color=red>환불대기</font>";  break;
		case 'C': echo "<font color=red>주문취소</font>";  break;
		case 'R': echo "반송";  break;
		case 'H': echo "배송(<font color=red>정산보류</font>)";  break;
	}
	if($row->deli_gbn=="D" && strlen($row->deli_date)==14) echo " (배송)";
	echo "	</B>";
	echo "		</td>\n";
	echo "	</tr>\n";
	echo "	</table>\n";
	echo "	</td>\n";
	echo "</tr>\n";
}
mysql_free_result($result);

if(count($etcdata)>0) {
	echo "<tr height=30 bgcolor=#efefef>\n";
	echo "	<td align=center colspan=".$colspan."><B>추가비용/할인/적립내역</B></td>";
	echo "</tr>\n";

	for($j=0;$j<count($etcdata);$j++) {
		$sumprice=$etcdata[$j]->price;
		$totprice+=$sumprice;
		$reserve=$etcdata[$j]->reserve;
		$totreserve+=$reserve;
		echo "<tr bgcolor=#FFFFFF>\n";
		echo "	<td>&nbsp;</td>\n";
		if($vendercnt>0 && $gbn=="Y") {
			if($etcdata[$j]->vender>0) {
				echo "	<td align=center style=\"font-size:8pt\">".$venderlist[$etcdata[$j]->vender]->id."</td>\n";
			} else {
				echo "	<td align=center>&nbsp;</td>\n";
			}
		}
		echo "	<td style=\"font-size:8pt;padding:2,5;line-height:10pt\">".$etcdata[$j]->productname."</td>\n";
		echo "	<td>&nbsp;</td>\n";
		if ($etcdata[$j]->productcode=="99999999999X" || $etcdata[$j]->productcode=="99999999990X" || $etcdata[$j]->productcode=="99999999997X" || substr($etcdata[$j]->productcode,0,3)=="COU" || $etcdata[$j]->productcode=="99999999999R") { // 현금결제면 수량표시안함
			echo "	<td>&nbsp;</td>\n";
		} else {
			echo "	<td align=center".($etcdata[$j]->quantity>1?" bgcolor=#FDE9D5 style=\"font-size:8pt\"><font color=#000000><b>":">").$etcdata[$j]->quantity."</td>\n";
		}
		echo "	<td align=right style=\"font-size:8pt\">";
		if($etcdata[$j]->vender>0) {
			if(strlen($_ord->ordercode)==20 && substr($_ord->ordercode,-1)!="X" && $etcdata[$j]->productcode!="99999999990X" && $etcdata[$j]->productcode!="99999999997X") {
				if($_ord->paymethod!="B" || $mode!="update") {
					echo ($reserve>0?number_format($reserve):"")."&nbsp;";
				} else {
					echo "&nbsp;";
				}
			} else {
				echo "&nbsp;";
			}
		} else {
			if(strlen($_ord->ordercode)==20 && substr($_ord->ordercode,-1)!="X" && $etcdata[$j]->productcode!="99999999990X" && $etcdata[$j]->productcode!="99999999997X") {
				if($_ord->paymethod!="B" || $mode!="update") {
					echo ($reserve>0?number_format($reserve):"")."&nbsp;";
				} else {
					echo "&nbsp;";
				}
			} else {
				echo "&nbsp;";
			}
		}
		echo "	</td>\n";

		echo "	<td align=right style=\"font-size:8pt\">".(substr($etcdata[$j]->productcode,-4)!="GIFT"?number_format($sumprice):"&nbsp;")."</td>\n";
		echo "</tr>\n";
	}
}

if($_ord) {
	$dc_price=(int)$_ord->dc_price;
	$salemoney=0;
	$salereserve=0;
	if($dc_price<>0) {
		if($dc_price>0) $salereserve=$dc_price;
		else $salemoney=-$dc_price;
		if(strlen($_ord->ordercode)==20 && substr($_ord->ordercode,-1)!="X") {
			$sql = "SELECT b.group_name FROM tblmember a, tblmembergroup b ";
			$sql.= "WHERE a.id='".$_ord->id."' AND b.group_code=a.group_code AND MID(b.group_code,1,1)!='M' ";
			$result=mysql_query($sql,get_db_conn());
			if($row=mysql_fetch_object($result)) {
				$group_name=$row->group_name;
			}
			mysql_free_result($result);
		}
		echo "<tr bgcolor=#FFFFE6>\n";
		echo "	<td>&nbsp;</td>\n";
		if($vendercnt>0 && $gbn=="Y") {
			echo "	<td>&nbsp;</td>\n";
		}
		echo "	<td style=\"font-size:8pt;padding:2,5\"><font color=red>그룹회원 적립/할인 : ".$group_name."</font></td>\n";
		echo "	<td>&nbsp;</td>\n";
		echo "	<td>&nbsp;</td>\n";
		echo "	<td align=right style=\"font-size:8pt\">".($salereserve>0?number_format($salereserve):"&nbsp;")."</td>\n";
		echo "	<td align=right style=\"font-size:8pt\">".($salemoney>0?"-".number_format($salemoney):"&nbsp;")."</td>\n";
		echo "</tr>\n";
		$totreserve+=$salereserve;
	}
	if($_ord->reserve>0) {
		echo "<tr bgcolor=#FFFFFF>\n";
		echo "	<td>&nbsp;</td>\n";
		if($vendercnt>0 && $gbn=="Y") {
			echo "	<td>&nbsp;</td>\n";
		}
		echo "	<td style=\"font-size:8pt;padding:2,5\"><font color=#0000FF>적립금 사용액</font></td>\n";
		echo "	<td>&nbsp;</td>\n";
		echo "	<td>&nbsp;</td>\n";
		echo "	<td align=right style=\"font-size:8pt;padding-right:3\">&nbsp;</td>\n";
		echo "	<td align=right style=\"font-size:8pt;padding-right:3\">-".number_format($_ord->reserve)."</td>\n";
		echo "</tr>\n";
	}
	$totprice=$totprice-$salemoney-$_ord->reserve;
	if (preg_match("/^(C|P|M){1}/", $_ord->paymethod) && $card_payfee>0 && $_ord->price<>$totprice) {
		echo "<tr bgcolor=#FFFFFF>\n";
		echo "	<td>&nbsp;</td>\n";
		if($vendercnt>0 && $gbn=="Y") {
			echo "	<td>&nbsp;</td>\n";
		}
		echo "	<td style=\"font-size:8pt;padding:2,5\"><font color=#0000FF>신용카드 수수료</font></td>\n";
		echo "	<td>&nbsp;</td>\n";
		echo "	<td>&nbsp;</td>\n";
		echo "	<td align=right style=\"padding-right:3\">&nbsp;</td>\n";
		echo "	<td align=right style=\"font-size:8pt;padding-right:3\">".number_format($_ord->price-$totprice)."</td>\n";
		echo "</tr>\n";
	}
	echo "<tr bgcolor=#FFFFFF>\n";
	echo "	<td colspan=".($colspan-4)." style=\"font-size:8pt;padding:5,27\"><B>총 합계</B> </td>\n";
	echo "	<td>&nbsp;</td>\n";
	echo "	<td align=center style=\"font-size:8pt\">".$totquantity."</td>\n";
	echo "	<td align=right style=\"font-size:8pt\">".(strlen($_ord->ordercode)==20 && substr($_ord->ordercode,-1)!="X"?number_format($totreserve)."&nbsp;":"&nbsp")."</td>\n";

	echo "	<td align=right style=\"font-size:8pt;padding-right:3\">".number_format($_ord->price)."</td>\n";
	echo "</tr>\n";

	echo "<tr bgcolor=#FFFFFF>\n";
	echo "	<td colspan=".$colspan." style=\"padding:10\">\n";
	echo "	<table border=0 cellpadding=2 cellspacing=0 width=100%>\n";
	echo "	<col width=100></col><col width=></col>\n";
	$date=substr($_ord->ordercode,0,4)."/".substr($_ord->ordercode,4,2)."/".substr($_ord->ordercode,6,2)." ".substr($_ord->ordercode,8,2).":".substr($_ord->ordercode,10,2).":".substr($_ord->ordercode,12,2);
	echo "	<tr>\n";
	echo "		<td>주문 일자</td>\n";
	echo "		<td>: ".$date."</td>\n";
	echo "	</tr>\n";
	if(strlen($_ord->ordercode)==20 && substr($_ord->ordercode,-1)!="X") $idname=$_ord->id;
	else $idname="비회원";
	echo "	<tr>\n";
	echo "		<td>주 &nbsp;문&nbsp; 자</td>\n";
	echo "		<td>: ".$_ord->sender_name."(".$idname.")님</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td>받 &nbsp;는&nbsp; 분</td>\n";
	echo "		<td>: ".$_ord->receiver_name."님</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td>받는 주소</td>\n";
	echo "		<td>: ".$_ord->receiver_addr."</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td>연 &nbsp;락&nbsp; 처</td>\n";
	echo "		<td>: ".$_ord->receiver_tel1.(strlen($_ord->receiver_tel2)>0?" , ".$_ord->receiver_tel2:"")."</td>\n";
	echo "	</tr>\n";
	if($gbn=="Y") {
		$pgdate = date("YmdHi",mktime(date("H")-2,date("i"),0,date("m"),date("d"),date("Y")));
		$arpm=array("B"=>"무통장","V"=>"계좌이체","O"=>"가상계좌","Q"=>"가상계좌(매매보호)","C"=>"신용카드","P"=>"신용카드(매매보호)","M"=>"핸드폰");

		if(($_ord->pay_data=="신용카드결제 - 카드작성중" || $_ord->pay_data=="핸드폰결제 - 작성중") && substr($_ord->ordercode,0,12)<=$pgdate) {
			$_ord->pay_data=$arpm[substr($_ord->paymethod,0,1)]." 에러";
		}

		echo "	<tr><td colspan=2 height=10></td></tr>\n";
		echo "	<tr>\n";
		echo "		<td>결제 방법</td>\n";
		echo "		<td>: ";
		if (preg_match("/^(B|O|Q){1}/",$_ord->paymethod)) {	//무통장, 가상계좌, 가상계좌 에스크로
			if($_ord->paymethod=="B") echo "<font color=#FF5D00>무통장입금</font>\n";
			else if(substr($_ord->paymethod,0,1)=="O") echo "<font color=#FF5D00>가상계좌</font>\n";
			else echo "매매보호 - 가상계좌";

			if(!preg_match("/^(C|D)$/",$_ord->deli_gbn) || $_ord->paymethod=="B") echo "【 ".$_ord->pay_data." 】";
			else echo "【 계좌 취소 】";

			if (strlen($_ord->bank_date)>=12) {
				echo "</td>\n</tr>\n";
				echo "<tr>\n";
				echo "	<td align=center bgcolor=#efefef>입금확인</td>\n";
				echo "	<td bgcolor=#ffffff style=\"padding-left:10\"><font color=red>".substr($_ord->bank_date,0,4)."/".substr($_ord->bank_date,4,2)."/".substr($_ord->bank_date,6,2)." (".substr($_ord->bank_date,8,2).":".substr($_ord->bank_date,10,2).")</font>";
			} else if(strlen($_ord->bank_date)==9) {
				echo "</td>\n</tr>\n";
				echo "<tr>\n";
				echo "	<td align=center bgcolor=#efefef>입금확인</td>\n";
				echo "	<td bgcolor=#ffffff style=\"padding-left:10\">환불";
			}
		} else if(substr($_ord->paymethod,0,1)=="M") {	//핸드폰 결제
			echo "핸드폰 결제【 ";
			if ($_ord->pay_flag=="0000") {
				if($_ord->pay_admin_proc=="C") echo "【 <font color=red>결제취소 완료</font> 】";
				else echo "<font color=red>결제가 성공적으로 이루어졌습니다.</font>";
			}
			else echo "결제가 실패되었습니다.";
			echo " 】";
		} else if(substr($_ord->paymethod,0,1)=="P") {	//매매보호 신용카드
			echo "매매보호 - 신용카드";
			if($_ord->pay_flag=="0000") {
				if($_ord->pay_admin_proc=="C") echo "【 <font color=red>카드결제 취소완료</font> 】";
				else if($_ord->pay_admin_proc=="Y") echo "【 카드 결제 완료 * 감사합니다. : 승인번호 ".$_ord->pay_auth_no." 】";
			}
			else echo "【 ".$_ord->pay_data." 】";
		} else if (substr($_ord->paymethod,0,1)=="C") {	//일반신용카드
			echo "<font color=#FF5D00>신용카드</font>\n";
			if($_ord->pay_flag=="0000") {
				if($_ord->pay_admin_proc=="C") echo "【 <font color=red>카드결제 취소완료</font> 】";
				else if($_ord->pay_admin_proc=="Y") echo "【 카드 결제 완료 * 감사합니다. : 승인번호 ".$_ord->pay_auth_no." 】";
			}
			else echo "【 ".$_ord->pay_data." 】";
		} else if (substr($_ord->paymethod,0,1)=="V") {
			echo "실시간 계좌이체 : ";
			if ($_ord->pay_flag=="0000") {
				if($_ord->pay_admin_proc=="C") echo "【 <font color=005000> [환불]</font> 】";
				else echo "<font color=red>".$_ord->pay_data."</font>";
			}
			else echo "결제가 실패되었습니다.";
		}
		echo "		</td>\n";
		echo "	</tr>\n";
		if(strlen($_ord->bank_date)==14) {
			$bank_date=substr($_ord->bank_date,0,4)."/".substr($_ord->bank_date,4,2)."/".substr($_ord->bank_date,6,2)." (".substr($_ord->bank_date,8,2).":".substr($_ord->bank_date,10,2).")";
			echo "	<tr>\n";
			echo "		<td>입금 확인</td>\n";
			echo "		<td>: ".$bank_date."</td>\n";
			echo "	</tr>\n";
		}
		$deli_array=array("S"=>"발송준비","X"=>"배송요청","Y"=>"배송","D"=>"취소요청","N"=>"미처리","E"=>"환불대기","C"=>"주문취소","R"=>"반송","H"=>"배송(정산보류)");
		echo "	<tr>\n";
		echo "		<td>발송 여부</td>\n";
		echo "		<td>: ".$deli_array[$_ord->deli_gbn]."";
		if(strlen($_ord->deli_date)==14) {
			$deli_date=substr($_ord->deli_date,0,4)."/".substr($_ord->deli_date,4,2)."/".substr($_ord->deli_date,6,2)." (".substr($_ord->deli_date,8,2).":".substr($_ord->deli_date,10,2).")";
			echo "&nbsp;&nbsp; 【발송세팅일 : ".$deli_date."】";
		}
		echo "		</td>\n";
		echo "	</tr>\n";
		if($totreserve>0 && $_ord->deli_gbn=="N" && substr($_ord->ordercode,-1)!="X") {
			echo "<tr>\n";
			echo "	<td></td>\n";
			echo "	<td> <FONT color=#0000FF>* 배송완료 버튼을 누르면 회원에게 <FONT color=red>적립금 ".number_format($totreserve)."원</FONT>이 적립됩니다.</FONT></td>\n";
			echo "</tr>\n";
		}
	}

	echo "	<tr><td colspan=2 height=10></td></tr>\n";

	$order_msg=explode("[MEMO]",$_ord->order_msg);
	$order_msg[0]=str_replace("\"","",$order_msg[0]);
	$order_msg[0]=preg_replace("/^(\r\n){0,}/","&nbsp;&nbsp;",$order_msg[0]);
	$order_msg[0]=ereg_replace("\r\n","<br>&nbsp;&nbsp;",$order_msg[0]);

	if(strlen($order_msg[0])>0) {
		echo "	<tr>\n";
		echo "		<td valign=top>주문요청사항</td>\n";
		echo "		<td>: ".$order_msg[0]."</td>\n";
		echo "	</tr>\n";
	}
	for($j=0;$j<count($prdata);$j++) {
		if(strlen($prdata[$j]->order_prmsg)>0) {
			echo "	<tr>\n";
			echo "		<td valign=middle>주문메세지</td>\n";
			echo "		<td style=\"padding-left:7\">";
			echo "		<FONT COLOR=\"#000000\"><B>상품명 :</B></FONT> ".$prdata[$j]->productname."<BR>\n";
			echo "		".$prdata[$j]->order_prmsg."";
			echo "		</td>\n";
			echo "	</tr>\n";
			echo "<tr><td colspan=2 height=3></td></tr>\n";
		}
	}
	if(strlen($order_msg[1])>0) {
		echo "	<tr>\n";
		echo "		<td>주문관련메모</td>\n";
		echo "		<td>: ".$order_msg[1]."</td>\n";
		echo "	</tr>\n";
	}
	if(strlen($order_msg[2])>0) {
		echo "	<tr>\n";
		echo "		<td>고객알리미</td>\n";
		echo "		<td>: ".$order_msg[2]."</td>\n";
		echo "	</tr>\n";
	}

	echo "	</table>\n";
	echo "	</td>\n";
	echo "</tr>\n";
}
?>
</table>

<?
	if($i<>0) echo "</H1>\n";
}
?>

</center>
</body>
</html>