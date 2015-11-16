<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$code=$_GET["code"];

$sql = "SELECT ordercode FROM tblpresentcode WHERE code='".$code."'";
$result = mysql_query($sql,get_db_conn());
if($row = mysql_fetch_object($result)) {
	$ordercode = $row->ordercode;
}
mysql_free_result($result);
if(!$ordercode) {
	echo "<html></head><body onload=\"alert('잘못된 경로로 접근하셨습니다.'); window.close();\"></body></html>";
	exit;
}
?>
<html>
<head>
<title>주문내역 조회</title>
<meta http-equiv="CONTENT-TYPE" content="text/html;charset=EUC-KR">
<META http-equiv="X-UA-Compatible" content="IE=5" >
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<style>
td {font-family:돋음;color:333333;font-size:9pt;}

tr {font-family:돋음;color:333333;font-size:9pt;}
BODY,TD,SELECT,DIV,form,TEXTAREA,center,option,pre,blockquote {font-family:돋음;color:333333;font-size:9pt;}

A:link    {color:333333;text-decoration:none;}

A:visited {color:333333;text-decoration:none;}

A:active  {color:333333;text-decoration:none;}

A:hover  {color:#CC0000;text-decoration:none;}
</style>
<SCRIPT LANGUAGE="JavaScript">
<!--

function MemoMouseOver(cnt) {
	obj = event.srcElement;
	WinObj=eval("document.all.memo"+cnt);
	obj._tid = setTimeout("MemoView(WinObj)",200);
}
function MemoView(WinObj) {
	WinObj.style.visibility = "visible";
}
function MemoMouseOut(cnt) {
	obj = event.srcElement;
	WinObj=eval("document.all.memo"+cnt);
	WinObj.style.visibility = "hidden";
	clearTimeout(obj._tid);
}

function DeliSearch(url){
	window.open(url,'배송조회','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=550,height=500');
}

function view_product(productcode) {
	window.open("http://<?=$_data->shopurl.FrontDir?>productdetail.php?productcode="+productcode,'orderProduct');
}

function ProductMouseOver(cnt) {
	obj = event.srcElement;
	WinObj=eval("document.all.primage"+cnt);
	obj._tid = setTimeout("ProductViewImage(WinObj)",200);
}
function ProductViewImage(WinObj) {
	WinObj.style.visibility = "visible";
}
function ProductMouseOut(Obj) {
	obj = event.srcElement;
	Obj = document.getElementById(Obj);
	Obj.style.visibility = "hidden";
	clearTimeout(obj._tid);
}

//-->
</SCRIPT>
</head>

<body topmargin=0 leftmargin=0 rightmargin=0 marginheight=0 marginwidth=0>
<center>
<table border=0 cellpadding=0 cellspacing=0 width=100%>
<tr>
	<td align=center style="padding:10,10,10,10">
	<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
	<tr><td align=center height=30 bgcolor=#454545><FONT COLOR="#FFFFFF"><B>주문조회</B></FONT></td></tr>
<?

	$sql = "SELECT * FROM tblorderinfo WHERE ordercode='".$ordercode."' ";
	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		$_ord=$row;
		$gift_price=$row->price-$row->deli_price;
	} else {
		echo "<tr height=200><td align=center>조회하신 주문내역이 없습니다.</td></tr>\n";
		echo "<tr><td align=center><input type=button value='닫 기' style=\"cursor:hand;color:#FFFFFF;border-color:#666666;background-color:#666666;font-size:8pt;font-family:Tahoma;height:20px;width:70\" onclick=\"window.close()\"></td></tr>\n";
		echo "</table>";
		exit;
	}
	mysql_free_result($result);
?>
	<tr><td height=10></td></tr>
	<tr>
		<td style="padding-left:20">
		<img src="<?=$Dir?>images/common/orderdetailpop_img.gif" border=0 align=absmiddle>
		&nbsp;&nbsp;&nbsp;
		<img src="<?=$Dir?>images/common/orderdetailpop_arrow.gif" border=0 align=absmiddle>
		<FONT COLOR="#EE1A02"><B><?=$_ord->sender_name?></B></FONT>님께서 <FONT COLOR="#111682"><?=substr($_ord->ordercode,0,4)?>년 <?=substr($_ord->ordercode,4,2)?>월 <?=substr($_ord->ordercode,6,2)?>일</FONT> 선물하신 내역입니다.
		</td>
	</tr>
	<tr><td height=10></td></tr>
	<tr>
		<td><img src=<?=$Dir?>images/icon_dot.gif border=0 align=absmiddle> <B>주문상품 정보</B></td>
	</tr>
	<tr>
		<td>
		<table border=0 cellpadding=0 cellspacing=1 bgcolor=E7E7E7 width=100% style="table-layout:fixed">
		<col width=45></col>
		<col width=></col>
		<col width=75></col>
		<col width=75></col>
		<col width=30></col>
		<col width=70></col>
		<col width=30></col>
		<col width=70></col>
		<col width=100></col>
		<tr height=28 bgcolor=#F5F5F5>
			<td align=center>이미지</td>
			<td align=center>상품명</td>
			<td align=center>선택사항1</td>
			<td align=center>선택사항2</td>
			<td align=center>수량</td>
			<td align=center>가격</td>
			<td align=center>메모</td>
			<td align=center>처리상태</td>
			<td align=center>배송조회</td>
		</tr>
<?
		$sql="SELECT * FROM tbldelicompany ORDER BY company_name ";
		$result=mysql_query($sql,get_db_conn());
		$delicomlist=array();
		while($row=mysql_fetch_object($result)) {
			$delicomlist[$row->code]=$row;
		}
		mysql_free_result($result);

		$sql = "SELECT productcode,productname,opt1_name,opt2_name,tempkey,addcode,quantity,price,reserve, ";
		$sql.= "quantity*price as sumprice, deli_gbn, deli_com, deli_num, deli_date, order_prmsg, package_idx, assemble_idx, assemble_info ";
		$sql.= "FROM tblorderproduct WHERE ordercode='".$ordercode."' ";
		$result=mysql_query($sql,get_db_conn());
		$cnt=0;
		$gift_check="N";
		$taxsaveprname="";
		$etcdata=array();
		$in_reserve=0;
		while($row=mysql_fetch_object($result)) {
			if (substr($row->productcode,0,3)=="999" || substr($row->productcode,0,3)=="COU") {
				if ($gift_check=="N" && strpos($row->productcode,"GIFT")!==false) $gift_check="Y";
				$etcdata[]=$row;
				continue;
			}
			$gift_tempkey=$row->tempkey;
			$taxsaveprname.=$row->productname.",";

			$optvalue="";
			if(ereg("^(\[OPTG)([0-9]{3})(\])$",$row->opt1_name)) {
				$optioncode=$row->opt1_name;
				$row->opt1_name="";
				$sql = "SELECT opt_name FROM tblorderoption WHERE ordercode='".$ordercode."' AND productcode='".$row->productcode."' ";
				$sql.= "AND opt_idx='".$optioncode."' ";
				$result2=mysql_query($sql,get_db_conn());
				if($row2=mysql_fetch_object($result2)) {
					$optvalue=$row2->opt_name;
				}
				mysql_free_result($result2);
			}

			$in_reserve+=$row->quantity*$row->reserve;

			$file="";
			if(file_exists($Dir.DataDir."shopimages/product/".$row->productcode."3.gif"))
				$file=$row->productcode."3.gif";
			else if(file_exists($Dir.DataDir."shopimages/product/".$row->productcode."3.jpg"))
				$file=$row->productcode."3.jpg";

			$packagestr = "";
			$packageliststr = "";
			$assemblestr = "";
			$rowspanstr = 1;
			if(strlen(str_replace("","",str_replace(":","",str_replace("=","",$row->assemble_info))))>0) {
				$assemble_infoall_exp = explode("=",$row->assemble_info);

				if($row->package_idx>0 && strlen(str_replace("","",str_replace(":","",$assemble_infoall_exp[0])))>0) {
					$rowspanstr++;
					$package_info_exp = explode(":", $assemble_infoall_exp[0]);
					$packagestr.="<br><img src=\"".$Dir."images/common/icn_package.gif\" border=0 align=absmiddle> ".$package_info_exp[3]."(<font color=#FF3C00>+".number_format($package_info_exp[2])."원</font>)";
					$productname_package_list_exp = explode("",$package_info_exp[1]);
					$packageliststr.="<tr bgcolor=\"#FFFFFF\">\n";
					$packageliststr.="	<td colspan=\"6\" style=\"padding-left:5px;\">\n";
					$packageliststr.= "	<table border=0 width=\"100%\" cellpadding=\"0\" cellspacing=\"0\">\n";
					$packageliststr.= "	<tr>\n";
					if(count($productname_package_list_exp)>0 && strlen($productname_package_list_exp[0])>0) {
						$packageliststr.= "		<td width=\"50\" valign=\"top\" style=\"padding-left:12px;\" nowrap><font color=\"#FF7100\" style=\"line-height:10px;\">┃<br>┗━<b>▶</b></font></td>\n";
						$packageliststr.= "		<td width=\"100%\">\n";
						$packageliststr.= "		<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-left:1px #DDDDDD solid;\">\n";

						for($k=0; $k<count($productname_package_list_exp); $k++) {
							$packageliststr.= "		<tr>\n";
							$packageliststr.= "			<td bgcolor=\"#FFFFFF\"".($k>0?"style=\"border-top:1px #DDDDDD solid;\"":"").">\n";
							$packageliststr.= "			<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
							$packageliststr.= "			<col width=\"\"></col>\n";
							$packageliststr.= "			<col width=\"124\"></col>\n";
							$packageliststr.= "			<tr>\n";
							$packageliststr.= "				<td style=\"padding:4px;word-break:break-all;font-size:8pt;\"><font color=\"#000000\">".$productname_package_list_exp[$k]."</font>&nbsp;</td>\n";
							$packageliststr.= "				<td align=\"center\" style=\"padding:4px;border-left:1px #DDDDDD solid;font-size:8pt;\">본 상품 1개당 수량1개</td>\n";
							$packageliststr.= "			</tr>\n";
							$packageliststr.= "			</table>\n";
							$packageliststr.= "			</td>\n";
							$packageliststr.= "		</tr>\n";
						}
						$packageliststr.= "		</table>\n";
						$packageliststr.= "		</td>\n";
					} else {
						$packageliststr.= "		<td width=\"50\" valign=\"top\" style=\"padding-left:12px;\" nowrap><font color=\"#FF7100\" style=\"line-height:10px;\">┃<br>┗━<b>▶</b></font></td>\n";
						$packageliststr.= "		<td width=\"100%\">\n";
						$packageliststr.= "		<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-left:1px #DDDDDD solid;\">\n";
						$packageliststr.= "		<tr>\n";
						$packageliststr.= "			<td bgcolor=\"#FFFFFF\" style=\"padding:4px;word-break:break-all;font-size:8pt;\"><font color=\"#000000\">구성상품이 존재하지 않는 패키지</font></td>\n";
						$packageliststr.= "		</tr>\n";
						$packageliststr.= "		</table>\n";
						$packageliststr.= "		</td>\n";
					}
					$packageliststr.= "	</tr>\n";
					$packageliststr.= "	</table>\n";
					$packageliststr.="	</td>\n";
					$packageliststr.="</tr>\n";
				}

				if($row->assemble_idx>0 && strlen(str_replace("","",str_replace(":","",$assemble_infoall_exp[1])))>0) {
					$rowspanstr++;
					$assemblestr.="<tr bgcolor=\"#FFFFFF\">\n";
					$assemblestr.="	<td colspan=\"6\" style=\"padding-left:5px;\">\n";
					$assemblestr.="	<table border=0 width=\"100%\" cellpadding=\"0\" cellspacing=\"0\">\n";
					$assemblestr.="	<tr>\n";
					$assemblestr.="		<td width=\"50\" valign=\"top\" style=\"padding-left:5px;\" nowrap><font color=\"#FF7100\" style=\"line-height:10px;\">┃<br>┗━<b>▶</b></font></td>\n";
					$assemblestr.="		<td width=\"100%\">\n";
					$assemblestr.="		<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-left:1px #DDDDDD solid;\">\n";

					$assemble_info_exp = explode(":", $assemble_infoall_exp[1]);

					if(count($assemble_info_exp)>2) {
						$assemble_productname_exp = explode("", $assemble_info_exp[1]);
						$assemble_sellprice_exp = explode("", $assemble_info_exp[2]);

						for($k=0; $k<count($assemble_productname_exp); $k++) {
							$assemblestr.="		<tr>\n";
							$assemblestr.="			<td bgcolor=\"#FFFFFF\"".($k>0?"style=\"border-top:1px #DDDDDD solid;\"":"").">\n";
							$assemblestr.="			<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
							$assemblestr.="			<col width=\"\"></col>\n";
							$assemblestr.="			<col width=\"67\"></col>\n";
							$assemblestr.="			<col width=\"124\"></col>\n";
							$assemblestr.="			<tr>\n";
							$assemblestr.="				<td style=\"padding:4px;word-break:break-all;font-size:8pt;\">".$assemble_productname_exp[$k]."&nbsp;</td>\n";
							$assemblestr.="				<td align=\"right\" style=\"padding:4px;border-left:1px #DDDDDD solid;border-right:1px #DDDDDD solid;font-size:8pt\">".number_format((int)$assemble_sellprice_exp[$k])."</td>\n";
							$assemblestr.="				<td align=\"center\" style=\"padding:4px;font-size:8pt\">본 상품 1개당 수량1개</td>\n";
							$assemblestr.="			</tr>\n";
							$assemblestr.="			</table>\n";
							$assemblestr.="			</td>\n";
							$assemblestr.="		</tr>\n";
						}
					}
					@mysql_free_result($alproresult);
					$assemblestr.="		</table>\n";
					$assemblestr.="		</td>\n";
					$assemblestr.="	</tr>\n";
					$assemblestr.="	</table>\n";
					$assemblestr.="	</td>\n";
					$assemblestr.="</tr>\n";
				}
			}

			echo "<tr bgcolor=#FFFFFF>\n";
			echo "	<td align=center rowspan=\"".$rowspanstr."\" style=\"padding:2px;\">\n";
			if(strlen($file)>0) {
				echo "		<span onMouseOver='ProductMouseOver($cnt)' onMouseOut=\"ProductMouseOut('primage".$cnt."');\">";
				echo "		<img src=".$Dir.DataDir."shopimages/product/".$file." border=0 width=40 height=40>";
				echo "		</span>\n";
				echo "		<div id=primage".$cnt." style=\"position:absolute; z-index:100; visibility:hidden;\">\n";
				echo "		<table border=0 cellspacing=0 cellpadding=0 width=170>\n";
				echo "		<tr bgcolor=#FFFFFF>\n";
				echo "			<td align=center width=100% height=150 style=\"border:#000000 solid 1px\"><img src=".$Dir.DataDir."shopimages/product/".$file."></td>\n";
				echo "		</tr>\n";
				echo "		</table>\n";
				echo "		</div>\n";
			} else {
				echo "&nbsp;";
			}
			echo "	</td>\n";
			echo "	<td style=\"font-size:8pt;padding:5,5,5,5\">";
			if (substr($row->productcode,0,3)!="999" && substr($row->productcode,0,3)!="COU") {
				echo "<a href=\"javascript:view_product('".$row->productcode."')\">";
			}
			echo ($row->sumprice<0?"<font color=#0000FF>":"").$row->productname.(strlen($row->addcode)>0?" - $row->addcode":"")."</a>";
			if(strlen($optvalue)>0) {
				echo "<br><img src=\"".$Dir."images/common/icn_option.gif\" border=0 align=absmiddle> ".$optvalue."";
			}
			if(strlen($packagestr)>0) {
				echo $packagestr;
			}
			echo "	</td>\n";
			echo "	<td align=center style=\"font-size:8pt\">".$row->opt1_name."</td>\n";
			echo "	<td align=center style=\"font-size:8pt\">".$row->opt2_name."</td>\n";
			echo "	<td align=center style=\"font-size:8pt\">".$row->quantity."</td>\n";
			echo "	<td align=right style=\"font-size:8pt;padding-right:5\"><FONT COLOR=\"#EE1A02\"><B>".number_format($row->sumprice)."</B></FONT></td>\n";
			if(strlen($row->order_prmsg)>0) {
				echo "	<td align=center style=\"font-size:8pt;color:red\"><a style=\"cursor:hand;\" onMouseOver='MemoMouseOver($cnt)' onMouseOut=\"MemoMouseOut($cnt);\">메모</a>";
				echo "	<div id=memo".$cnt." style=\"left:160px;position:absolute; z-index:100; visibility:hidden;\">\n";
				echo "	<table width=400 border=0 cellspacing=0 cellpadding=0 bgcolor=#A47917>\n";
				echo "	<tr>\n";
				echo "		<td style=\"padding:5;line-height:12pt\"><font color=#FFFFFF>".nl2br(strip_tags($row->order_prmsg))."</td>\n";
				echo "	</tr>";
				echo "	</table>\n";
				echo "	</div>\n";
				echo "	</td>\n";
			} else {
				echo "	<td align=center style=\"font-size:8pt\">-</td>\n";
			}
			echo "	<td align=center style=\"font-size:8pt\" rowspan=\"".$rowspanstr."\">";
			if ($row->deli_gbn=="C") echo "주문취소";
			else if ($row->deli_gbn=="D") echo "취소요청";
			else if ($row->deli_gbn=="E") echo "환불대기";
			else if ($row->deli_gbn=="X") echo "발송준비";
			else if ($row->deli_gbn=="Y") echo "발송완료";
			else if ($row->deli_gbn=="N") {
				if (strlen($_ord->bank_date)<12 && preg_match("/^(B|O|Q){1}/", $_ord->paymethod)) echo "입금확인중";
				else if ($_ord->pay_admin_proc=="C" && $_ord->pay_flag=="0000") echo "결제취소";
				else if (strlen($_ord->bank_date)>=12 || $_ord->pay_flag=="0000") echo "발송준비";
				else echo "결제확인중";
			} else if ($row->deli_gbn=="S") {
				echo "발송준비";
			} else if ($row->deli_gbn=="R") {
				echo "반송처리";
			} else if ($row->deli_gbn=="H") {
				echo "발송완료 [정산보류]";
			}
			echo "	</td>\n";
			echo "	<td align=center style=\"font-size:8pt\" rowspan=\"".$rowspanstr."\">";
			$deli_url="";
			$trans_num="";
			$company_name="";
			if($row->deli_gbn=="Y") {
				if($row->deli_com>0 && $delicomlist[$row->deli_com]) {
					$deli_url=$delicomlist[$row->deli_com]->deli_url;
					$trans_num=$delicomlist[$row->deli_com]->trans_num;
					$company_name=$delicomlist[$row->deli_com]->company_name;
					echo $company_name."<br>";
					if(strlen($row->deli_num)>0 && strlen($deli_url)>0) {
						if(strlen($trans_num)>0) {
							$arrtransnum=explode(",",$trans_num);
							$pattern=array("(\[1\])","(\[2\])","(\[3\])","(\[4\])");
							$replace=array(substr($row->deli_num,0,$arrtransnum[0]),substr($row->deli_num,$arrtransnum[0],$arrtransnum[1]),substr($row->deli_num,$arrtransnum[0]+$arrtransnum[1],$arrtransnum[2]),substr($row->deli_num,$arrtransnum[0]+$arrtransnum[1]+$arrtransnum[2],$arrtransnum[3]));
							$deli_url=preg_replace($pattern,$replace,$deli_url);
						} else {
							$deli_url.=$row->deli_num;
						}
						echo "<A HREF=\"javascript:DeliSearch('".$deli_url."')\"><img src=".$Dir."images/common/btn_mypagedeliview.gif border=0></A>";
					}
				} else {
					echo "-";
				}
			} else {
				echo "-";
			}
			echo "	</td>\n";
			echo "</tr>\n";
			echo $assemblestr;
			echo $packageliststr;
			$cnt++;
		}
		mysql_free_result($result);
?>
		</table>
		</td>
	</tr>
	<tr><td height=20></td></tr>

<?
	if($_ord->price>0) {
?>
	<tr>
		<td align=center>
		<table border=0 cellpadding=5 cellspacing=1 width=100% bgcolor=#E7E7E7 style="table-layout:fixed">
		<col width=110></col>
		<col width=></col>
		<?if(strlen($ordercode)==21 && substr($ordercode,-1)=="X"){?>
		<tr>
			<td align=center bgcolor=#F5F5F5 style="padding:7,10">주문확인번호</td>
			<td bgcolor=#ffffff style="padding:7,10"><b><?=substr($_ord->id,1,6)?></td>
		</tr>
		<?}?>
		<tr>
			<td align=center bgcolor=#F5F5F5 style="padding:7,10">주문일자</td>
			<td bgcolor=#ffffff style="padding:7,10"><?=substr($ordercode,0,4).".".substr($ordercode,4,2).".".substr($ordercode,6,2)?></td>
		</tr>
		<tr>
			<td align=center bgcolor=#F5F5F5 style="padding:7,10">받는사람</td>
			<td bgcolor=#ffffff style="padding:7,10"><?=$_ord->receiver_name?></td>
		</tr>
		<tr>
			<td align=center bgcolor=#F5F5F5 style="padding:7,10">배달주소</td>
			<td bgcolor=#ffffff style="padding:7,10"><?=ereg_replace("주소 :","<br>주소 :",$_ord->receiver_addr)?></td>
		</tr>
<?
		$order_msg=explode("[MEMO]",$_ord->order_msg);
		if(strlen($order_msg[0])>0) {
?>
		<tr>
			<td align=center bgcolor=#F5F5F5 style="padding:7,10">고객메모</td>
			<td bgcolor=#ffffff style="padding:7,10">
			<?=nl2br($order_msg[0])?>
			</td>
		</tr>
		<?}?>

		<?if(strlen($order_msg[2])>0) {?>
		<tr>
			<td align=center bgcolor=#F5F5F5 style="padding:7,10">상점메모</td>
			<td bgcolor=#ffffff style="padding:7,10">
			<?=nl2br($order_msg[2])?>
			</td>
		</tr>
		<?}?>
		</table>
		</td>
	</tr>
<?
	}
?>
	<tr><td height=10></td></tr>
	</table>
	</td>
</tr>
</table>
</center>
</body>
</html>
