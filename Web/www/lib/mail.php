<?
if(substr(getenv("SCRIPT_NAME"),-9)=="/mail.php"){
	header("HTTP/1.0 404 Not Found");
	exit;
}

//가입축하메일
function SendJoinMail($shopname, $shopurl, $mail_type, $join_msg, $info_email, $email, $name) {
	$sql = "SELECT * FROM ".$designnewpageTables." WHERE type='joinmail' ";
	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		//개별디자인
		$pattern = array ("(\[SHOP\])","(\[NAME\])");
		$replace = array ($shopname,$name);
		$subject = preg_replace($pattern,$replace,$row->subject);
		$body	 = $row->body;
	} else {
		//템플릿
		$subject = $shopname." 가입 축하 메일입니다.";
		$buffer="";
		if(file_exists(DirPath.TempletDir."mail/joinmail".$mail_type.".php")) {
			$fp=fopen(DirPath.TempletDir."mail/joinmail".$mail_type.".php","r");
			if($fp) {
				while (!feof($fp)) {$buffer.= fgets($fp, 1024);}
			}
			fclose($fp);
			$body=$buffer;
		}
	}
	mysql_free_result($result);
	if(strlen($body)>0) {
		$curdate = date("Y년 m월 d일");
		$pattern = array ("(\[SHOP\])","(\[NAME\])","(\[MESSAGE\])","(\[URL\])","(\[CURDATE\])");
		$replace = array ($shopname,$name,$join_msg,$shopurl,$curdate);
		$body	 = preg_replace($pattern,$replace,$body);
		if (strlen($shopname)>0) $mailshopname = "=?ks_c_5601-1987?B?".base64_encode($shopname)."?=";
		$subject = '=?ks_c_5601-1987?B?'.base64_encode(strtr($subject,"\r\n",'  ')).'?='; // 아웃룩 등에서 메일 제목 깨지는 것 관련 해서 처리
		$header=getMailHeader($mailshopname,$info_email);
		if(ismail($email)) {
			sendmail($email, $subject, $body, $header);
		}
	}
}

//주문확인메일
function SendOrderMail($shopname, $shopurl, $mail_type, $info_email, $ordercode, $okadminmail, $oksendmail, $thankmsg) {
	global $smsproductname;
	$smsproductname="";
	global $vender_smsproductname;
	$vender_smsproductname= array(array());

	$maildata=array();

	$mailtop ="<table cellpadding=0 cellspacing=0 border=0 width=656>\n";
	$mailtop.="<tr>\n";
	$mailtop.="	<td>\n";
	$mailtop.="	<table cellpadding=0 cellspacing=0 width=100%>\n";
	$mailtop.="	<TR>\n";
	$mailtop.="		<TD><strong style=\"padding-left:4px; color:#333333;\">주문내역</strong></TD>\n";
	$mailtop.="	</TR>\n";
	$mailtop.="	<tr>\n";
	$mailtop.="		<td height=5></td>\n";
	$mailtop.="	</tr>\n";
	$mailtop.="	<tr>\n";
	$mailtop.="		<td>\n";
	$mailtop.="		<table cellpadding=0 cellspacing=0 width=100% style='table-layout:fixed'>\n";
	$mailtop.="		<col width=80></col>\n";
	$mailtop.="		<col></col>\n";
	$mailtop.="		<col width=70></col>\n";
	$mailtop.="		<col width=130></col>\n";
	$mailtop.="		<col width=130></col>\n";
	$mailtop.="		<tr>\n";
	$mailtop.="			<td height=2 colspan=5 bgcolor=#969696></td>\n";
	$mailtop.="		</tr>\n";
	$mailtop.="		<tr height=30 align=center bgcolor=#f9f9f9>\n";
	$mailtop.="			<td colspan=2><font color=#474747><b>상품명</b></font></td>\n";
	$mailtop.="			<td><font color=#474747><b>수량</b></font></td>\n";
	$mailtop.="			<td><font color=#474747><b>적립금</b></font></td>\n";
	$mailtop.="			<td><font color=#474747><b>주문금액</b></font></td>\n";
	$mailtop.="		</tr>\n";
	$mailtop.="		<tr>\n";
	$mailtop.="			<td height=1 colspan=5 bgcolor=#e4e4e4></td>\n";
	$mailtop.="		</tr>\n";

	$mailmid ="	<tr>\n";
	$mailmid.="		<td height=25></td>\n";
	$mailmid.="	</tr>\n";
	$mailmid.="	<TR>\n";
	$mailmid.="		<TD><strong style=\"padding-left:4px; color:#333333;\">추가비용 / 할인 / 적립내역</strong></TD>\n";
	$mailmid.="	</TR>\n";
	$mailmid.="	<tr>\n";
	$mailmid.="		<td height=7></td>\n";
	$mailmid.="	</tr>\n";
	$mailmid.="	<tr>\n";
	$mailmid.="		<td>\n";
	$mailmid.="		<table cellpadding=0 cellspacing=0 width=100% style='table-layout:fixed'>\n";
	$mailmid.="		<col width=99></col>\n";
	$mailmid.="		<col width=></col>\n";
	$mailmid.="		<col width=75></col>\n";
	$mailmid.="		<col width=75></col>\n";
	$mailmid.="		<col width=185></col>\n";
	$mailmid.="		<tr>\n";
	$mailmid.="			<td height=2 colspan=5 bgcolor=#969696></td>\n";
	$mailmid.="		</tr>\n";
	$mailmid.="		<tr height=30 align=center bgcolor=#f9f9f9>\n";
	$mailmid.="			<td><font color=#474747><b>항목</b></font></td>\n";
	$mailmid.="			<td><font color=#474747><b>내용</b></font></td>\n";
	$mailmid.="			<td><font color=#474747><b>금액</b></font></td>\n";
	$mailmid.="			<td><font color=#474747><b>적립금</b></font></td>\n";
	$mailmid.="			<td><font color=#474747><b>해당상품명</b></font></td>\n";
	$mailmid.="		</tr>\n";
	$mailmid.="		<tr>\n";
	$mailmid.="			<td height=1 colspan=5 bgcolor=#e4e4e4></td>\n";
	$mailmid.="		</tr>\n";

	$mailbottom.="		</table>\n";
	$mailbottom.="		</td>\n";
	$mailbottom.="	</tr>\n";

	$sql = "SELECT * FROM tblorderinfo WHERE ordercode='".$ordercode."' ";
	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		$_ord=$row;
		$email=$_ord->sender_email;
		if(substr($row->id,0,1)=='X') $guest_type="guest";
	} else {
		$ordercode="";
	}
	mysql_free_result($result);

	if(strlen($ordercode)>0) {
		$sql ="SELECT *, price*quantity as sumprice, (select tinyimage from tblproduct A where A.productcode=B.productcode) tinyimage FROM tblorderproduct B ";
		$sql.="WHERE ordercode='".$ordercode."' ";
		$sql.="ORDER BY productcode ASC ";
		$result=mysql_query($sql,get_db_conn());
		$etcdata=array();
		$arrvender=array();
		$prdata=array();
		$cnt=0;

		while($row = mysql_fetch_object($result)) {

			if (substr($row->productcode,0,3)=="999" || substr($row->productcode,0,3)=="COU") {
			//if (substr($row->productcode,0,3)=="COU") {
				$etcdata[]=$row;
				continue;
			}
			if($row->reserve>0) $reserve+=$row->reserve*$row->quantity;

			$prdata[]=$row;
			if($row->vender>0) {
				$arrvender[$row->vender]=$row->vender;
			}

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

			unset($tempmaildata);
			$tempmaildata.="<tr>\n";
			$tempmaildata.="	<td style='padding:10px 0 10px 10px; vertical-align:text-top;'>";
			if(strlen($row->tinyimage)!=0 && file_exists(DirPath.DataDir."shopimages/product/".$row->tinyimage)){
				$tempmaildata.="<img src=\"http://".$shopurl.DataDir."shopimages/product/".$row->tinyimage."\" ";
			} else {
				$tempmaildata.="<img src=\"http://".$shopurl."images/no_img.gif\" ";
			}
			$tempmaildata.=" width=\"54\" height=\"54\" style='border:1px solid #d0d0d0;'></td>";
			$tempmaildata.="	<td style='padding-left:10px;word-break:break-all;color:#ff6c00;'>";
			$tempmaildata.="<B>".$row->productname."</B>";
			$tempmaildata.="	</td>\n";
			$tempmaildata.="	<td align=center>".$row->quantity."</td>\n";
			$tempmaildata.="	<td align=center>".number_format($row->reserve*$row->quantity)."원"."</td>\n";
			$tempmaildata.="	<td align=center><font color=#ff6c00><b>".number_format($row->price*$row->quantity)."원</b></font></td>\n";
			$tempmaildata.="</tr>\n";
			if(strlen($row->opt1_name)>0 || strlen($row->opt2_name)>0 || strlen($optvalue)>0 || strlen(str_replace("","",str_replace(":","",str_replace("=","",$row->assemble_info))))>0) {
				if(strlen($row->opt1_name)>0 || strlen($row->opt2_name)>0 || strlen($optvalue)>0) {
					$tempmaildata.="<tr>\n";
					$tempmaildata.="	<td colspan=5 style='padding-left:10px;word-break:break-all;'>";
					if(strlen($row->addcode)>0) $tempmaildata.="특징 : ".$row->addcode."&nbsp;&nbsp;";
					if(strlen($row->opt1_name)>0) $tempmaildata.=" ".$row->opt1_name." ";
					if(strlen($row->opt2_name)>0) $tempmaildata.=", ".$row->opt2_name." ";
					if(strlen($optvalue)>0) $tempmaildata.= $optvalue;
					$tempmaildata.="	</td>\n";
					$tempmaildata.="</tr>\n";
					$row->addcode="";
				}
				if(strlen(str_replace("","",str_replace(":","",str_replace("=","",$row->assemble_info))))>0) {
					$assemble_infoall_exp = explode("=",$row->assemble_info);

					if($row->package_idx>0 && strlen(str_replace("","",str_replace(":","",$assemble_infoall_exp[0])))>0) {
						$tempmaildata.="<tr>\n";
						$tempmaildata.="	<td colspan=\"5\" style=\"padding:5px 0 5px 10px;word-break:break-all;\">";
						if(strlen($row->addcode)>0) $tempmaildata.="특징 : ".$row->addcode."&nbsp;&nbsp;";
						$package_info_exp = explode(":", $assemble_infoall_exp[0]);
						if(strlen($package_info_exp[3])>0) $tempmaildata.="패키지선택 : ".$package_info_exp[3]."(<font color=#FF3C00>+".number_format($package_info_exp[2])."원</font>)";

						$productname_package_list_exp = explode("",$package_info_exp[1]);
						$tempmaildata.="	<table border=0 width=\"100%\" cellpadding=\"0\" cellspacing=\"0\">\n";
						$tempmaildata.="	<tr>\n";
						if(count($productname_package_list_exp)>0 && strlen($productname_package_list_exp[0])>0) {
							$tempmaildata.="		<td width=\"50\" valign=\"top\" style=\"padding-left:12px;\" nowrap><font color=\"#FF7100\" style=\"line-height:10px;\">┃<br>┗━<b>▶</b></font></td>\n";
							$tempmaildata.="		<td width=\"100%\">\n";
							$tempmaildata.="		<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-left:1px #e4e4e4 solid;border-top:1px #e4e4e4 solid;border-right:1px #e4e4e4 solid;\">\n";

							for($i=0; $i<count($productname_package_list_exp); $i++) {
								$tempmaildata.="		<tr>\n";
								$tempmaildata.="			<td bgcolor=\"#FFFFFF\" style=\"border-bottom:1px #e4e4e4 solid;\">\n";
								$tempmaildata.="			<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
								$tempmaildata.="			<col width=\"\"></col>\n";
								$tempmaildata.="			<col width=\"120\"></col>\n";
								$tempmaildata.="			<tr>\n";
								$tempmaildata.="				<td style=\"padding:4px;word-break:break-all;\"><font color=\"#000000\">".$productname_package_list_exp[$i]."</font>&nbsp;</td>\n";
								$tempmaildata.="				<td align=\"center\" style=\"padding:4px;border-left:1px #e4e4e4 solid;\">본 상품 1개당 수량1개</td>\n";
								$tempmaildata.="			</tr>\n";
								$tempmaildata.="			</table>\n";
								$tempmaildata.="			</td>\n";
								$tempmaildata.="		</tr>\n";
							}
							$tempmaildata.="		</table>\n";
							$tempmaildata.="		</td>\n";
						} else {
							$tempmaildata.="		<td width=\"50\" valign=\"top\" style=\"padding-left:12px;\" nowrap><font color=\"#FF7100\" style=\"line-height:10px;\">┃<br>┗━<b>▶</b></font></td>\n";
							$tempmaildata.="		<td width=\"100%\">\n";
							$tempmaildata.="		<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-left:1px #e4e4e4 solid;border-top:1px #e4e4e4 solid;border-right:1px #e4e4e4 solid;\">\n";
							$tempmaildata.="		<tr>\n";
							$tempmaildata.="			<td bgcolor=\"#FFFFFF\" style=\"border-bottom:1px #e4e4e4 solid;padding:4px;word-break:break-all;\"><font color=\"#000000\">구성상품이 존재하지 않는 패키지</font></td>\n";
							$tempmaildata.="		</tr>\n";
							$tempmaildata.="		</table>\n";
							$tempmaildata.="		</td>\n";
						}
						$tempmaildata.="	</tr>\n";
						$tempmaildata.="	</table>\n";
						$tempmaildata.="	</td>\n";
						$tempmaildata.="</tr>\n";

						$row->addcode="";
					}

					if($row->assemble_idx>0 && strlen(str_replace("","",str_replace(":","",$assemble_infoall_exp[1])))>0) {
						$tempmaildata.="<tr>\n";
						$tempmaildata.="	<td colspan=\"5\" style=\"padding:5px 10px;\">";
						if(strlen($row->addcode)>0) $tempmaildata.="특징 : ".$row->addcode."<br>";
						$tempmaildata.="	<table border=0 width=\"100%\" cellpadding=\"0\" cellspacing=\"0\">\n";
						$tempmaildata.="	<tr>\n";
						$tempmaildata.="		<td width=\"50\" valign=\"top\" style=\"padding-left:5px;\" nowrap><font color=\"#FF7100\" style=\"line-height:10px;\">┃<br>┗━<b>▶</b></font></td>\n";
						$tempmaildata.="		<td width=\"100%\">\n";
						$tempmaildata.="		<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-left:1px #e4e4e4 solid;border-top:1px #e4e4e4 solid;border-right:1px #e4e4e4 solid;\">\n";

						$assemble_info_exp = explode(":", $assemble_infoall_exp[1]);

						if(count($assemble_info_exp)>2) {
							$assemble_productname_exp = explode("", $assemble_info_exp[1]);
							$assemble_sellprice_exp = explode("", $assemble_info_exp[2]);

							for($k=0; $k<count($assemble_productname_exp); $k++) {
								$tempmaildata.="		<tr>\n";
								$tempmaildata.="			<td bgcolor=\"#FFFFFF\" style=\"border-bottom:1px #e4e4e4 solid;\">\n";
								$tempmaildata.="			<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
								$tempmaildata.="			<col width=\"\"></col>\n";
								$tempmaildata.="			<col width=\"80\"></col>\n";
								$tempmaildata.="			<col width=\"120\"></col>\n";
								$tempmaildata.="			<tr>\n";
								$tempmaildata.="				<td style=\"padding:4px;word-break:break-all;\"><font color=\"#000000\">".$assemble_productname_exp[$k]."</font>&nbsp;</td>\n";
								$tempmaildata.="				<td align=\"right\" style=\"padding:4px;border-left:1px #e4e4e4 solid;border-right:1px #e4e4e4 solid;\"><font color=\"#000000\">".number_format((int)$assemble_sellprice_exp[$k])."원</font></td>\n";
								$tempmaildata.="				<td align=\"center\" style=\"padding:4px;\">본 상품 1개당 수량1개</td>\n";
								$tempmaildata.="			</tr>\n";
								$tempmaildata.="			</table>\n";
								$tempmaildata.="			</td>\n";
								$tempmaildata.="		</tr>\n";
							}
						}
						$tempmaildata.="		</table>\n";
						$tempmaildata.="		</td>\n";
						$tempmaildata.="	</tr>\n";
						$tempmaildata.="	</table>\n";
						$tempmaildata.="	</td>\n";
						$tempmaildata.="</tr>\n";
						$row->addcode="";
					}
				}
			} else if(strlen($row->addcode)>0) {
				$tempmaildata.="<tr>\n";
				$tempmaildata.="	<td colspan=\"5\" style=\"padding:5px 10px ;word-break:break-all;\">";
				if(strlen($row->addcode)>0) $tempmaildata.="특징 : ".$row->addcode;
				$tempmaildata.="	</td>\n";
				$tempmaildata.="</tr>\n";
			}
			$tempmaildata.="<tr><td colspan=5 height=1 bgcolor=#e4e4e4></td></tr>\n";

			$maildata[0].=$tempmaildata;
			if($row->vender>0) {
				$maildata[$row->vender].=$tempmaildata;
			}
			if($row->price>0){
				$smsproductname.=",".$row->productname;
				$vender_smsproductname[$row->vender][] = $row->productname;
			}
			$cnt++;
		}
		mysql_free_result($result);
		$smsproductname=titleCut(40,strip_tags($smsproductname));

		$maildata[0]=$mailtop.$maildata[0];
		$maildata[0].=$mailbottom.$mailmid;

		$tmpvender=$arrvender;
		while(list($key,$val)=each($tmpvender)) {
			$maildata[$val]=$mailtop.$maildata[$val];
			$maildata[$val].=$mailbottom.$mailmid;
		}

		for($i=0;$i<count($etcdata);$i++) {
			unset($tempmaildata);
			if(ereg("^(COU)([0-9]{8})(X)$",$etcdata[$i]->productcode)) {				#쿠폰
				$tempmaildata.="<tr >\n";
				$tempmaildata.="	<td align=center><b>쿠폰 사용</b></td>\n";
				$tempmaildata.="	<td style='padding:5px;'>".$etcdata[$i]->productname."</td>\n";
				$tempmaildata.="	<td align=center>".($etcdata[$i]->price!=0?number_format($etcdata[$i]->price)."원":"&nbsp;")."</td>\n";
				$tempmaildata.="	<td align=center>".($etcdata[$i]->reserve!=0?number_format($etcdata[$i]->reserve)."원":"&nbsp;")."</td>\n";
				$tempmaildata.="	<td align=center>".$etcdata[$i]->order_prmsg."</td>\n";
				$tempmaildata.="</tr>\n";
				$tempmaildata.="<tr><td height=1 colspan=5 bgcolor=#e4e4e4></td></tr>\n";
			} else if(ereg("^(9999999999)([0-9]{1})(X)$",$etcdata[$i]->productcode)) {
				if($etcdata[$i]->productcode=="99999999999X") {
					$tempmaildata.="<tr>\n";
					$tempmaildata.="	<td align=center><b>결제 할인</b></td>\n";
					$tempmaildata.="	<td style='padding:5px;'>".$etcdata[$i]->productname."</td>\n";
					$tempmaildata.="	<td align=center>".($etcdata[$i]->price!=0?number_format($etcdata[$i]->price)."원":"&nbsp;")."</td>\n";
					$tempmaildata.="	<td align=center>".($etcdata[$i]->reserve!=0?number_format($etcdata[$i]->reserve)."원":"&nbsp;")."</td>\n";
					$tempmaildata.="	<td align=center>주문서 전체적용</td>\n";
					$tempmaildata.="</tr>\n";
					$tempmaildata.="<tr><td height=1 colspan=5 bgcolor=#e4e4e4></td></tr>\n";
				} else if($etcdata[$i]->productcode=="99999999998X") {
					$tempmaildata.="<tr>\n";
					$tempmaildata.="	<td align=center><b>결제 수수료</b></td>\n";
					$tempmaildata.="	<td style='padding:5px;'>".$etcdata[$i]->productname."</td>\n";
					$tempmaildata.="	<td align=center>".($etcdata[$i]->price!=0?number_format($etcdata[$i]->price)."원":"&nbsp;")."</td>\n";
					$tempmaildata.="	<td align=center>".($etcdata[$i]->reserve!=0?number_format($etcdata[$i]->reserve)."원":"&nbsp;")."</td>\n";
					$tempmaildata.="	<td align=center>주문서 전체적용</td>\n";
					$tempmaildata.="</tr>\n";
					$tempmaildata.="<tr><td height=1 colspan=5 bgcolor=#e4e4e4></td></tr>\n";
				} else if($etcdata[$i]->productcode=="99999999990X") {
					$tempmaildata.="<tr>\n";
					$tempmaildata.="	<td align=center><b>배송료</b></td>\n";
					$tempmaildata.="	<td style='padding:5px;'>".$etcdata[$i]->productname."</td>\n";
					$tempmaildata.="	<td align=center>".($etcdata[$i]->price!=0?number_format($etcdata[$i]->price)."원":"&nbsp;")."</td>\n";
					$tempmaildata.="	<td align=center>".($etcdata[$i]->reserve!=0?number_format($etcdata[$i]->reserve)."원":"&nbsp;")."</td>\n";
					$tempmaildata.="	<td align=center>".$etcdata[$i]->order_prmsg."</td>\n";
					$tempmaildata.="</tr>\n";
					$tempmaildata.="<tr><td height=1 colspan=5 bgcolor=#e4e4e4></td></tr>\n";
				} else if($etcdata[$i]->productcode=="99999999997X") {
					$tempmaildata.="<tr>\n";
					$tempmaildata.="	<td align=center><b>부가세(VAT)</b></td>\n";
					$tempmaildata.="	<td style='padding:5px;'>".$etcdata[$i]->productname."</td>\n";
					$tempmaildata.="	<td align=center>".($etcdata[$i]->price!=0?number_format($etcdata[$i]->price)."원":"&nbsp;")."</td>\n";
					$tempmaildata.="	<td align=center></td>\n";
					$tempmaildata.="	<td align=center>주문서 전체적용</td>\n";
					$tempmaildata.="</tr>\n";
					$tempmaildata.="<tr><td height=1 colspan=5 bgcolor=#e4e4e4></td></tr>\n";
				}
			}
			if(strlen($tempmaildata)>0) {
				$maildata[0].=$tempmaildata;
				if($etcdata[$i]->vender>0) {
					$maildata[$etcdata[$i]->vender].=$tempmaildata;
				}
			}
		}
		unset($tempmaildata);
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
			$tempmaildata.="<tr>\n";
			$tempmaildata.="	<td align=center><b>그룹적립/할인</b></td>\n";
			$tempmaildata.="	<td style='padding:5px;'>그룹회원 적립/할인 : ".$group_name."</td>\n";
			$tempmaildata.="	<td align=center>".($salemoney>0?"-".number_format($salemoney)."원":"&nbsp;")."</td>\n";
			$tempmaildata.="	<td align=center>".($salereserve>0?"+ ".number_format($salereserve)."원":"&nbsp;")."</td>\n";
			$tempmaildata.="	<td align=center>주문서 전체 적용</td>\n";
			$tempmaildata.="</tr>\n";
			$tempmaildata.="<tr><td colspan=5 height=1 bgcolor=#e4e4e4></td></tr>\n";
		}

		if($_ord->reserve>0) {
			$tempmaildata.="<tr>\n";
			$tempmaildata.="	<td align=center><b>적립금 사용</b></td>\n";
			$tempmaildata.="	<td style='padding:5px;'>결제시 적립금 ".number_format($_ord->reserve)."원 사용</td>\n";
			$tempmaildata.="	<td align=center>-".number_format($_ord->reserve)."원</td>\n";
			$tempmaildata.="	<td align=center>&nbsp;</td>\n";
			$tempmaildata.="	<td align=center>주문서 전체 적용</td>\n";
			$tempmaildata.="</tr>\n";
			$tempmaildata.="<tr><td colspan=5 height=1 bgcolor=#e4e4e4></td></tr>\n";
		}

		if(strlen($tempmaildata)>0) {
			$maildata[0].=$tempmaildata;
		}

		$strvender="";
		$maildata[0].=$mailbottom;

		$kk=0;
		$tmpvender=$arrvender;
		while(list($key,$val)=each($tmpvender)) {
			$maildata[$val].=$mailbottom;
			if($kk>0) $strvender.=",";
			$strvender.=$val;
			$kk++;
		}

		$maildata[0].="<tr>\n";
		$maildata[0].="	<td height=45></td>\n";
		$maildata[0].="</tr>\n";
		$maildata[0].="<tr>\n";
		$maildata[0].="	<td>\n";
		$maildata[0].="	<table cellpadding=0 cellspacing=0 width=100%>\n";
		$maildata[0].="	<tr>\n";
		$maildata[0].="		<td height=2 bgcolor=#969696></td>\n";
		$maildata[0].="	</tr>\n";
		$maildata[0].="	<tr height=30 bgcolor=#f9f9f9>\n";
		$maildata[0].="		<td align=right style='padding-right:10px;'><font color=#474747><B>총 결제금액&nbsp;:&nbsp;</b></font><font color=#ff6c00 style='font-size:12pt;'><b>".number_format($_ord->price)."원</b></font></td>\n";
		$maildata[0].="	</tr>\n";
		$maildata[0].="	<tr>\n";
		$maildata[0].="		<td height=1 bgcolor=#e4e4e4></td>\n";
		$maildata[0].="	</tr>\n";
		$maildata[0].="	</table>\n";
		$maildata[0].="	</td>\n";
		$maildata[0].="</tr>\n";

		$tempmaildata1.="<tr>\n";
		$tempmaildata1.="	<td height=45></td>\n";
		$tempmaildata1.="</tr>\n";
		$tempmaildata1.="<tr>\n";
		$tempmaildata1.="	<td>\n";
		$tempmaildata1.="	<table cellpadding=0 cellspacing=0 width=100%>\n";
		$tempmaildata1.="	<TR>\n";
		$tempmaildata1.="		<TD><strong style='padding-left:4px; color:#333333;'>결제방법</strong></TD>\n";
		$tempmaildata1.="	</TR>\n";
		$tempmaildata1.="	<tr>\n";
		$tempmaildata1.="		<td height=7></td>\n";
		$tempmaildata1.="	</tr>\n";
		$tempmaildata1.="	<TR>\n";
		$tempmaildata1.="		<TD>\n";
		$tempmaildata1.="		<table cellpadding=10 cellspacing=2 width=100% bgcolor=#c4c4c4>\n";
		$tempmaildata1.="		<tr>\n";
		$tempmaildata1.="			<td bgcolor=#ffffff>\n";

		if(preg_match("/^(V|C|P|M){1}/", $_ord->paymethod)) {
			$arpm=array("V"=>"실시간계좌이체","C"=>"신용카드","P"=>"매매보호 - 신용카드", "M"=>"핸드폰");
			$tempmaildata1.= $arpm[substr($_ord->paymethod,0,1)];

			if ($_ord->pay_flag=="0000") {
				if(preg_match("/^(C|P){1}/", $_ord->paymethod)) {
					$tempmaildata1.="&nbsp;결제성공 - 승인번호 : ".$_ord->pay_auth_no." ";
				} else {
					$tempmaildata1.="&nbsp;결제가 <font color=blue>정상처리</font> 되었습니다.";
				}
			} else if(strlen($_ord->pay_flag)>0)
				$tempmaildata1.="&nbsp;거래결과 : <font color=red><b><u>".$_ord->pay_data."</u></b></font>\n";
			else
				$tempmaildata1.="&nbsp;\n<font color=red>(지불실패)</font>";

			if (preg_match("/^(C|P|M){1}/", $_ord->paymethod) && $_data->card_payfee>0) $tempmaildata1.="<br>&nbsp\n".$arpm[substr($_ord->paymethod,0,1)]." 결제시 현금 할인가 적용이 안됩니다.";

		} else if (preg_match("/^(B|O|Q){1}/", $_ord->paymethod)) {
			if(preg_match("/^(B){1}/", $_ord->paymethod)) $tempmaildata1.="무통장 입금 : <font color=#0054A6>".$_ord->pay_data."</font> <br>\n(입금확인후 배송이 됩니다.)";
			else {
				if($_ord->pay_flag=="0000") $msg = "&nbsp\n(입금확인후 배송이 됩니다.)";
				if(preg_match("/^(O){1}/", $_ord->paymethod)) $tempmaildata1.="가상계좌 : <font color=#0054A6>".$_ord->pay_data."</font> <br>".$msg;
				else if(preg_match("/^(Q){1}/", $_ord->paymethod)) $tempmaildata1.="매매보호 - 가상계좌 : <font color=#0054A6>".$_ord->pay_data."</font> <br>".$msg;
			}
		}

		$tempmaildata1.="			</td>\n";
		$tempmaildata1.="		</tr>\n";
		$tempmaildata1.="		</table>\n";
		$tempmaildata1.="		</TD>\n";
		$tempmaildata1.="	</TR>\n";
		$tempmaildata1.="	</table>\n";
		$tempmaildata1.="	</td>\n";
		$tempmaildata1.="</tr>\n";
		$tempmaildata2.="<tr>\n";
		$tempmaildata2.="	<td height=45></td>\n";
		$tempmaildata2.="</tr>\n";
		$tempmaildata2.="<tr>\n";
		$tempmaildata2.="	<td>\n";
		$tempmaildata2.="	<table cellpadding=0 cellspacing=0 width=100%>\n";
		$tempmaildata2.="	<TR>\n";
		$tempmaildata2.="		<TD><strong style='padding-left:4px; color:#333333;'>결제상태</strong></TD>\n";
		$tempmaildata2.="	</TR>\n";
		$tempmaildata2.="	<tr>\n";
		$tempmaildata2.="		<td height=7></td>\n";
		$tempmaildata2.="	</tr>\n";
		$tempmaildata2.="	<TR>\n";
		$tempmaildata2.="		<TD>\n";
		$tempmaildata2.="		<table cellpadding=10 cellspacing=2 width=100% bgcolor=#c4c4c4>\n";
		$tempmaildata2.="		<tr>\n";
		$tempmaildata2.="			<td bgcolor=#ffffff>\n";

		if(preg_match("/^(V|C|P|M){1}/", $_ord->paymethod)) {
			$arpm=array("V"=>"실시간계좌이체","C"=>"신용카드","P"=>"매매보호 - 신용카드", "M"=>"핸드폰");
			$tempmaildata2.= $arpm[substr($_ord->paymethod,0,1)];
			if ($_ord->pay_flag=="0000") {
				if(preg_match("/^(C|P){1}/", $_ord->paymethod)) {
					$tempmaildata2.="&nbsp;결제성공 ";
				} else {
					$tempmaildata2.="&nbsp;결제가 <font color=blue>정상처리</font> 되었습니다.";
				}
			} else if(strlen($_ord->pay_flag)>0)
				$tempmaildata2.="&nbsp;거래결과 : <font color=red><b><u>".$_ord->pay_data."</u></b></font>\n";
			else
				$tempmaildata2.="&nbsp;\n<font color=red>(지불실패)</font>";
		} else if (preg_match("/^(B|O|Q){1}/", $_ord->paymethod)) {
			if(preg_match("/^(B){1}/", $_ord->paymethod)) $tempmaildata2.="무통장 결제";
			else {
				if(preg_match("/^(O){1}/", $_ord->paymethod)) $tempmaildata2.="가상계좌 결제<br>".$msg;
				else if(preg_match("/^(Q){1}/", $_ord->paymethod)) $tempmaildata2.="매매보호 - 가상계좌 결제<br>".$msg;
			}
		}

		$tempmaildata2.="			</td>\n";
		$tempmaildata2.="		</tr>\n";
		$tempmaildata2.="		</table>\n";
		$tempmaildata2.="		</TD>\n";
		$tempmaildata2.="	</TR>\n";
		$tempmaildata2.="	</table>\n";
		$tempmaildata2.="	</td>\n";
		$tempmaildata2.="</tr>\n";
		$tempmaildata3.="<tr>\n";
		$tempmaildata3.="	<td height=45></td>\n";
		$tempmaildata3.="</tr>\n";
		$tempmaildata3.="<tr>\n";
		$tempmaildata3.="	<td>\n";
		$tempmaildata3.="	<table cellpadding=0 cellspacing=0 width=100%>\n";
		$tempmaildata3.="	<TR>\n";
		$tempmaildata3.="		<TD><strong style='padding-left:4px; color:#333333;'>주문자정보</strong></TD>\n";
		$tempmaildata3.="	</TR>\n";
		$tempmaildata3.="	<tr>\n";
		$tempmaildata3.="		<td height=7></td>\n";
		$tempmaildata3.="	</tr>\n";
		$tempmaildata3.="	<TR>\n";
		$tempmaildata3.="		<TD>\n";
		$tempmaildata3.="		<table cellpadding=0 cellspacing=0 width=100% style='border-top:2px solid #969696'>\n";
		$tempmaildata3.="		<tr>\n";
		$tempmaildata3.="			<td>\n";
		$tempmaildata3.="			<table cellpadding=0 cellspacing=0 width=100%>\n";
		$tempmaildata3.="			<col width=100></col>\n";
		$tempmaildata3.="			<col></col>\n";
		$tempmaildata3.="			<tr>\n";
		$tempmaildata3.="				<td style='padding-left:15px; background-color:#f9f9f9; text-align:left;height:32px'><font color=#474747><b>주문일자</b></font></td>\n";
		$tempmaildata3.="				<td style='padding-left:10px;'><b>".substr($_ord->ordercode,0,4).".".substr($_ord->ordercode,4,2).".".substr($_ord->ordercode,6,2)."</b></td>\n";
		$tempmaildata3.="			</tr>\n";
		$tempmaildata3.="			<tr><td colspan=2 height=1 bgcolor=#e4e4e4></td></tr>\n";
		$tempmaildata3.="			<tr>\n";
		$tempmaildata3.="				<td style='padding-left:15px; background-color:#f9f9f9; text-align:left;height:32px'><font color=#474747><b>이름</b></font></td>\n";
		$tempmaildata3.="				<td style='padding-left:10px;'><b>".$_ord->sender_name."</b></td>\n";
		$tempmaildata3.="			</tr>\n";
		$tempmaildata3.="			<tr><td colspan=2 height=1 bgcolor=#e4e4e4></td></tr>\n";
		$tempmaildata3.="			<tr>\n";
		$tempmaildata3.="				<td style='padding-left:15px; background-color:#f9f9f9; text-align:left;height:32px'><font color=#474747><b>전화번호</b></font></td>\n";
		$tempmaildata3.="				<td style='padding-left:10px;'>".$_ord->sender_tel."</td>\n";
		$tempmaildata3.="			</tr>\n";
		$tempmaildata3.="			<tr><td colspan=2 height=1 bgcolor=#e4e4e4></td></tr>\n";
		$tempmaildata3.="			<tr>\n";
		$tempmaildata3.="				<td style='padding-left:15px; background-color:#f9f9f9; text-align:left;height:32px'><font color=#474747><b>이메일</b></font></td>\n";
		$tempmaildata3.="				<td style='padding-left:10px;'>".$_ord->sender_email."</td>\n";
		$tempmaildata3.="			</tr>\n";
		$tempmaildata3.="			<tr><td colspan=2 height=2 bgcolor=#e4e4e4></td></tr>\n";
		$tempmaildata3.="			</table>\n";
		$tempmaildata3.="			</td>\n";
		$tempmaildata3.="		</tr>\n";
		$tempmaildata3.="		</table>\n";
		$tempmaildata3.="		</TD>\n";
		$tempmaildata3.="	</TR>\n";
		$tempmaildata3.="	</table>\n";
		$tempmaildata3.="	</td>\n";
		$tempmaildata3.="</tr>\n";
		$tempmaildata4.="<tr>\n";
		$tempmaildata4.="	<td height=45></td>\n";
		$tempmaildata4.="</tr>\n";
		$tempmaildata4.="<tr>\n";
		$tempmaildata4.="	<td>\n";
		$tempmaildata4.="	<table cellpadding=0 cellspacing=0 width=100%>\n";
		$tempmaildata4.="	<TR>\n";
		$tempmaildata4.="		<TD><strong style='padding-left:4px; color:#333333;'>배송정보</strong</TD>\n";
		$tempmaildata4.="	</TR>\n";
		$tempmaildata4.="	<tr>\n";
		$tempmaildata4.="		<td height=7></td>\n";
		$tempmaildata4.="	</tr>\n";
		$tempmaildata4.="	<TR>\n";
		$tempmaildata4.="		<TD>\n";
		$tempmaildata4.="		<table cellpadding=0 cellspacing=0 width=100% style='border-top:2px solid #969696'>\n";
		$tempmaildata4.="		<tr>\n";
		$tempmaildata4.="			<td>\n";
		$tempmaildata4.="			<table cellpadding=0 cellspacing=0 width=100%>\n";
		$tempmaildata4.="			<col width=100></col>\n";
		$tempmaildata4.="			<col></col>\n";
		$tempmaildata4.="			<tr>\n";
		$tempmaildata4.="				<td style='padding-left:15px; background-color:#f9f9f9; text-align:left;height:32px'><font color=#474747><b>주문일자</b></font></td>\n";
		$tempmaildata4.="				<td style='padding-left:10px;'><b>".substr($_ord->ordercode,0,4).".".substr($_ord->ordercode,4,2).".".substr($_ord->ordercode,6,2)."</b></td>\n";
		$tempmaildata4.="			</tr>\n";
		$tempmaildata4.="			<tr><td colspan=2 height=1 bgcolor=#e4e4e4></td></tr>\n";
		$tempmaildata4.="			<tr>\n";
		$tempmaildata4.="				<td style='padding-left:15px; background-color:#f9f9f9; text-align:left;height:32px'><font color=#474747><b>이름</b></font></td>\n";
		$tempmaildata4.="				<td style='padding-left:10px;'><b>".$_ord->receiver_name."</b></td>\n";
		$tempmaildata4.="			</tr>\n";
		$tempmaildata4.="			<tr><td colspan=2 height=1 bgcolor=#e4e4e4></td></tr>\n";
		$tempmaildata4.="			<tr>\n";
		$tempmaildata4.="				<td style='padding-left:15px; background-color:#f9f9f9; text-align:left;height:32px'><font color=#474747><b>전화번호</b></font></td>\n";
		$tempmaildata4.="				<td style='padding-left:10px;'>".$_ord->receiver_tel1."</td>\n";
		$tempmaildata4.="			</tr>\n";
		$tempmaildata4.="			<tr><td colspan=2 height=1 bgcolor=#e4e4e4></td></tr>\n";
		$tempmaildata4.="			<tr>\n";
		$tempmaildata4.="				<td style='padding-left:15px; background-color:#f9f9f9; text-align:left;height:32px'><font color=#474747><b>비상전화</b></font></td>\n";
		$tempmaildata4.="				<td style='padding-left:10px;'>".$_ord->receiver_tel2."</td>\n";
		$tempmaildata4.="			</tr>\n";
		$tempmaildata4.="			<tr><td colspan=2 height=1 bgcolor=#e4e4e4></td></tr>\n";
		$tempmaildata4.="			<tr>\n";
		$tempmaildata4.="				<td style='padding-left:15px; background-color:#f9f9f9; text-align:left;height:32px'><font color=#474747><b>주소</b></font></td>\n";
		$tempmaildata4.="				<td style='padding-left:10px;'>".$_ord->receiver_addr."</td>\n";
		$tempmaildata4.="			</tr>\n";
		$tempmaildata4.="			<tr><td colspan=2 height=1 bgcolor=#e4e4e4></td></tr>\n";
		$tempmaildata4.="			<tr>\n";
		$tempmaildata4.="				<td style='padding-left:15px; background-color:#f9f9f9; text-align:left;height:32px'><font color=#474747><b>주문요청사항</b></font></td>\n";
		$tempmaildata4.="				<td style='padding-left:10px;' valign=top>".ereg_replace("\r\n","<br>",$_ord->order_msg)."</td>\n";
		$tempmaildata4.="			</tr>\n";
		$tempmaildata4.="			<tr><td colspan=2 height=1 bgcolor=#e4e4e4></td></tr>\n";

		for($i=0;$i<count($prdata);$i++) {
			if(strlen($prdata[$i]->order_prmsg)>0) {
				$tempmaildata5.="<tr><td colspan=2 height=1 bgcolor=#e4e4e4></td></tr>\n";
				$tempmaildata5.="<tr>\n";
				$tempmaildata5.="	<td style='padding-left:15px; background-color:#f9f9f9; text-align:left;height:32px'><font color=#474747><b>주문메세지</b></font></td>\n";
				$tempmaildata5.="	<td style='padding-left:10px;'>\n";
				$tempmaildata5.="	<font color=#474747><B>상품명 :</B></FONT> ".$prdata[$i]->productname."<BR>\n";
				$tempmaildata5.="<textarea style='width:95%;height:40;overflow-x:hidden;overflow-y:auto;' readonly>".$prdata[$i]->order_prmsg."</textarea>\n";
				$tempmaildata5.="	</td>\n";
				$tempmaildata5.="</tr>\n";
				$tempmaildata5.="<tr><td colspan=2 height=1 bgcolor=#e4e4e4></td></tr>\n";
			}
		}

		$tempmaildata7.="			</table>\n";
		$tempmaildata7.="			</td>\n";
		$tempmaildata7.="		</tr>\n";
		$tempmaildata7.="		</table>\n";
		$tempmaildata7.="		</TD>\n";
		$tempmaildata7.="	</TR>\n";
		$tempmaildata7.="	</table>\n";
		$tempmaildata7.="	</td>\n";
		$tempmaildata7.="</tr>\n";
		$tempmaildata8.="<tr>\n";
		$tempmaildata8.="	<td height=45></td>\n";
		$tempmaildata8.="</tr>\n";
		$tempmaildata8.="<tr>\n";
		$tempmaildata8.="	<td>\n";
		$tempmaildata8.="	<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0 width=100%>\n";
		$tempmaildata8.="	<tr>\n";
		$tempmaildata8.="		<td>\n";
		$tempmaildata8.="		<table cellpadding=10 cellspacing=2 border=0 width=100% bgcolor=#c4c4c4>\n";
		$tempmaildata8.="		<tr>\n";
		$tempmaildata8.="			<td bgcolor=#FFFFFF>\n";

		if(preg_match("/^(B){1}/", $_ord->paymethod) || (preg_match("/^(V|O|Q|C|P|M){1}/", $_ord->paymethod) && strcmp($_ord->pay_flag,"0000")==0)) {
			if(strlen($_ord->ordercode)==20 && substr($_ord->ordercode,-1)!="X") {
				$tempmaildata8.="<font color=#FF6600><b>".$_ord->sender_name."님의 주문이 완료되었습니다.</b></font><br><br>\n";
				if ($totreserve>0) $tempmaildata8.="귀하의 제품 구입에 따른 적립금 <font color=#FF6600><b>".number_format($totreserve)."원</b></font>은 배송과 함께 바로 적립됩니다.<br>\n";
			} else {
				$tempmaildata8.="주문이 완료되었습니다.<br>\n";
				$tempmaildata8.="귀하의 주문확인 번호는 <font color=0000a0><b>".substr($_ord->id,1,6)."</b></font>입니다.<br>\n";
			}
		} else if (preg_match("/^(V|O|Q|C|P|M)$/", $_ord->paymethod) && strcmp($_ord->pay_flag,"0000")!=0 ) {
			$tempmaildata8.="<font color=red size=3><b>주문이 실패되었습니다.</b></font><br>\n";
		}

		if(preg_match("/^(B){1}/", $_ord->paymethod) || (preg_match("/^(O|Q){1}/", $_ord->paymethod) && $_ord->pay_flag=="0000")) {
			$tempmaildata8.="입금방법이 무통장입금의 경우 계좌번호를 메모하세요.<br>저희가 입금확인 후 바로 보내드립니다.<br><br>\n";
		} else if(preg_match("/^(C|P|M){1}/", $_ord->paymethod) && strcmp($_ord->pay_flag,"0000")==0) {
			$tempmaildata8.="저희가 확인 후 바로 보내드립니다.<br><br>\n";
		}

		if ((preg_match("/^(B){1}/", $_ord->paymethod) || (preg_match("/^(V|O|Q|C|P|M){1}/", $_ord->paymethod) && strcmp($_ord->pay_flag,"0000")==0)) && strlen($_data->orderend_msg)>0) {
			$tempmaildata8.=ereg_replace("\n","<br>",$_data->orderend_msg);
			$tempmaildata8.="<br>\n";
		}

		$tempmaildata8.="			</td>\n";
		$tempmaildata8.="		</tr>\n";
		$tempmaildata8.="		</table>\n";
		$tempmaildata8.="		</td>\n";
		$tempmaildata8.="	</tr>\n";
		$tempmaildata8.="	</table>\n";
		$tempmaildata8.="	</td>\n";
		$tempmaildata8.="</tr>\n";

		$tempmaildata9.="	</table>\n";
		$tempmaildata9.="	</td>\n";
		$tempmaildata9.="</tr>\n";
		$tempmaildata9.="</table>\n";

		$maildata[0].=$tempmaildata1.$tempmaildata3.$tempmaildata4.$tempmaildata5.$tempmaildata7.$tempmaildata8.$tempmaildata9;

		$tmpvender=$arrvender;
		while(list($key,$val)=each($tmpvender)) {
			unset($tempmaildata6);
			for($i=0;$i<count($prdata);$i++) {
				if($val==$prdata[$i]->vender) {
					if(strlen($prdata[$i]->order_prmsg)>0) {
						$tempmaildata6.="<tr><td colspan=2 height=1 bgcolor=#e4e4e4></td></tr>\n";
						$tempmaildata6.="<tr>\n";
						$tempmaildata6.="	<td style='padding-left:15px; background-color:#f9f9f9; text-align:left;height:32px'><font color=#474747><b>주문메세지</b></font></td>\n";
						$tempmaildata6.="	<td style='padding-left:10px;'>";
						$tempmaildata6.="	<FONT COLOR=\"#474747\"><B>상품명 :</B></FONT> ".$prdata[$i]->productname."<BR>\n";
						$tempmaildata6.="<textarea style=\"width:95%;height:40;overflow-x:hidden;overflow-y:auto;\" readonly>".$prdata[$i]->order_prmsg."</textarea>\n";
						$tempmaildata6.="	</td>\n";
						$tempmaildata6.="</tr>\n";
						$tempmaildata6.="<tr><td colspan=2 height=1 bgcolor=#e4e4e4></td></tr>\n";
					}
				}
			}
			$maildata[$val].=$tempmaildata2.$tempmaildata4.$tempmaildata6.$tempmaildata7.$tempmaildata9;
		}

		if($okadminmail=="Y") {
			$tempmaildata="<table cellpadding=0 cellspacing=0 border=0 width=600><tr><td>".$maildata[0].$thankmsg."</td></tr></table>";
			$curdate=date("Y/m/d (H:i)");

			if (strlen($shopname)>0) $mailshopname = "=?ks_c_5601-1987?B?".base64_encode($shopname)."?=";
			$header=getMailHeader($mailshopname,$info_email);
			if(ismail($info_email)) {
				sendmail($info_email, "[$shopname] $curdate 주문내역서", $tempmaildata, $header);
			}
		}
		if(strlen($strvender)>0) {
			$strvender=ereg_replace(',','\',\'',$strvender);
			$sql = "SELECT vender,p_email FROM tblvenderinfo ";
			$sql.= "WHERE vender IN ('".$strvender."') ";
			$result=mysql_query($sql,get_db_conn());
			while($row=mysql_fetch_object($result)) {
				if(strlen($row->p_email)>0 && ismail($row->p_email)) {
					$tempmaildata="<table cellpadding=0 cellspacing=0 border=0 width=600><tr><td>".$maildata[$row->vender]."</td></tr></table>";
					$curdate=date("Y/m/d (H:i)");

					if (strlen($shopname)>0) $mailshopname = "=?ks_c_5601-1987?B?".base64_encode($shopname)."?=";
					$header=getMailHeader($mailshopname,$row->p_email);
					sendmail($row->p_email, "[$shopname] $curdate 주문내역서", $tempmaildata, $header);
				}
			}
			mysql_free_result($result);
		}

		$c_ordercode = ($guest_type =="guest")? "<td style=\"color:#fff; background-image:url(http://".$shopurl."images/mail/solution/icon_1.gif); background-position:10px 45%; background-repeat:no-repeat; padding-left:20px;\">주문번호 : ".substr($_ord->id,1,6)."</td>":"";

		if($oksendmail=="Y") {
			$curdate = date("Y년 m월 d일");
			$sql = "SELECT * FROM ".$designnewpageTables." WHERE type='ordermail' ";
			$result=mysql_query($sql,get_db_conn());
			if($row=mysql_fetch_object($result)) {
				//개별디자인
				$pattern = array ("(\[SHOP\])","(\[NAME\])","(\[DATE\])");
				$replace = array ($shopname,$_ord->sender_name,$curdate);
				$subject = preg_replace($pattern,$replace,$row->subject);
				$body	 = $row->body;
			} else {
				//템플릿
				$subject = $shopname." 주문내역서 확인 메일입니다.";
				$buffer="";
				if(file_exists(DirPath.TempletDir."mail/ordermail".$mail_type.".php")) {
					$fp=fopen(DirPath.TempletDir."mail/ordermail".$mail_type.".php","r");
					if($fp) {
						while (!feof($fp)) {$buffer.= fgets($fp, 1024);}
					}
					fclose($fp);
					$body=$buffer;
				}
			}
			mysql_free_result($result);
			if(strlen($body)>0) {
				$pattern = array ("(\[SHOP\])","(\[NAME\])","(\[CURDATE\])","(\[MAILDATA\])","(\[MESSAGE\])","(\[URL\])","(\[ORDERCODE\])");
				$replace = array ($shopname,$_ord->sender_name,$curdate,$maildata[0],$thankmsg,$shopurl,$c_ordercode);
				$body	 = preg_replace($pattern,$replace,$body);
				if (strlen($shopname)>0) $mailshopname = "=?ks_c_5601-1987?B?".base64_encode($shopname)."?=";
				$header=getMailHeader($mailshopname,$info_email);
				if(ismail($email)) {
					sendmail($email, $subject, $body, $header);
				}
			}
		}
	}
}

//상품발송완료메일
function SendDeliMail($shopname, $shopurl, $mail_type, $info_email, $ordercode, $deli_com, $deli_num, $delimailtype) {
	$sql = "SELECT * FROM tblorderinfo WHERE ordercode='".$ordercode."' ";
	$result=mysql_query($sql,get_db_conn());
	$_ord=mysql_fetch_object($result);
	mysql_free_result($result);
	if($_ord) {
		$email=$_ord->sender_email;
		$patterns = array("( )","(_)","(-)");
		$replace = array("","","");
		$deli_num = preg_replace($patterns,$replace,$deli_num);

		if(strlen($deli_com)>0 && strlen($deli_num)>0) {
			$sql="SELECT * FROM tbldelicompany WHERE code='".$deli_com."' ";
			$result=mysql_query($sql,get_db_conn());
			if($row=mysql_fetch_object($result)) {
				$deliurl=$row->deli_url;
				$delicom=$row->company_name;
				$transnum=$row->trans_num;
			}
			mysql_free_result($result);
		}

		$sql = "SELECT * FROM ".$designnewpageTables." WHERE type='delimail' ";
		$result=mysql_query($sql,get_db_conn());
		if($row=mysql_fetch_object($result)) {
			//개별디자인
			$pattern = array ("(\[SHOP\])");
			$replace = array ($shopname);
			$subject = preg_replace($pattern,$replace,$row->subject).($delimailtype=="Y"?" [송장이 변경되었습니다.]":"");
			$body	 = $row->body;
		} else {
			//템플릿
			$subject = $shopname." 발송 메일입니다.".($delimailtype=="Y"?" [송장이 변경되었습니다.]":"");
			$buffer="";
			if(file_exists(DirPath.TempletDir."mail/delimail".$mail_type.".php")) {
				$fp=fopen(DirPath.TempletDir."mail/delimail".$mail_type.".php","r");
				if($fp) {
					while (!feof($fp)) {$buffer.= fgets($fp, 1024);}
				}
				fclose($fp);
				$body=$buffer;
			}
		}
		mysql_free_result($result);
		if(strlen($body)>0) {
			$orderdate = substr($ordercode,0,4)."/".substr($ordercode,4,2)."/".substr($ordercode,6,2);
			$delidate = date("Y/m/d");
			if(strpos($body,"[IFDELICHANGE]")!=0) {
				$ifdelichange=strpos($body,"[IFDELICHANGE]");
				$elsedelichange=strpos($body,"[ELSEDELICHANGE]");
				$enddelichange=strpos($body,"[ENDDELICHANGE]");

				$yesdelichange=substr($body,$ifdelichange+14,$elsedelichange-$ifdelichange-14);
				$nodelichange =substr($body,$elsedelichange+17,$enddelichange-$elsedelichange-17);

				if($delimailtype=="Y") {
					$changemsg=$yesdelichange;
				} else {
					$changemsg=$nodelichange;
				}
				$body=substr($body,0,$ifdelichange-1).$changemsg.substr($body,$enddelichange+15);
			}
			if(strpos($body,"[IFDELINUM]")!=0) {
				$ifdelinum=strpos($body,"[IFDELINUM]");
				$enddelinum=strpos($body,"[ENDDELINUM]");
				$yesdelinum=substr($body,$ifdelinum+11,$enddelinum-$ifdelinum-11);

				if(strpos($body,"[IFDELIURL]")!=0) {
					$ifurl=strpos($yesdelinum,"[IFDELIURL]");
					$elseurl=strpos($yesdelinum,"[ELSEDELIURL]");
					$endurl=strpos($yesdelinum,"[ENDDELIURL]");

					$yesdeliurl=substr($yesdelinum,$ifurl+11,$elseurl-$ifurl-11);
					$nodeliurl =substr($yesdelinum,$elseurl+14,$endurl-$elseurl-14);

					if(strlen($deli_com)>0 && $deli_num>0) {
						if(strlen($deliurl)>0) {
							if(strlen($transnum)>0) {
								$artransnum=explode(",",$transnum);
								$trpatten=array("(\[1\])","(\[2\])","(\[3\])","(\[4\])");
								$trreplace=array(substr($deli_num,0,$artransnum[0]),substr($deli_num,$artransnum[0],$artransnum[1]),substr($deli_num,$artransnum[0]+$artransnum[1],$artransnum[2]),substr($deli_num,$artransnum[0]+$artransnum[1]+$artransnum[2],$artransnum[3]));
								$deliurl=preg_replace($trpatten,$trreplace,$deliurl);
								$yesdeliurl=str_replace('[DELIVERYURL][DELIVERYNUM]','[DELIVERYURL]',$yesdeliurl);
							} else {
								$deliurl=$deliurl.$deli_num;
							}
							$delivery =  substr($yesdelinum,0,$ifurl-1).$yesdeliurl.substr($yesdelinum,$endurl+12);
						} else {
							$delivery =  substr($yesdelinum,0,$ifurl-1).$nodeliurl.substr($yesdelinum,$endurl+12);
						}
					}
				} else {
					$delivery=$yesdelinum;
				}
				$body=substr($body,0,$ifdelinum-1).$delivery.substr($body,$enddelinum+12);
			}
			$curdate = date("Y년 m월 d일");
			$patten = array ("(\[SHOP\])","(\[DELIVERYURL\])","(\[DELIVERYNUM\])","(\[DELIVERYCOMPANY\])","(\[URL\])","(\[DELIVERYDATE\])","(\[ORDERDATE\])","(\[CURDATE\])");
			$replace = array ($shopname,$deliurl,$deli_num,$delicom,$shopurl,$delidate,$orderdate,$curdate);
			$body = preg_replace($patten,$replace,$body);

			if (strlen($shopname)>0) $mailshopname = "=?ks_c_5601-1987?B?".base64_encode($shopname)."?=";
			$header=getMailHeader($mailshopname,$info_email);
			if(ismail($email)) {
				sendmail($email, $subject, $body, $header);
			}
		}
	}
}

//아이디/패스워드안내메일
function SendPassMail($shopname, $shopurl, $mail_type, $info_email, $email, $name, $id, $passwd) {
	$sql = "SELECT * FROM ".$designnewpageTables." WHERE type='passmail' ";
	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		//개별디자인
		$pattern = array ("(\[SHOP\])","(\[NAME\])");
		$replace = array ($shopname,$name);
		$subject = preg_replace($pattern,$replace,$row->subject);
		$body	 = $row->body;
	} else {
		//템플릿
		$subject = $shopname." 패스워드 안내메일입니다.";
		$buffer="";
		if(file_exists(DirPath.TempletDir."mail/passmail".$mail_type.".php")) {
			$fp=fopen(DirPath.TempletDir."mail/passmail".$mail_type.".php","r");
			if($fp) {
				while (!feof($fp)) {$buffer.= fgets($fp, 1024);}
			}
			fclose($fp);
			$body=$buffer;
		}
	}
	mysql_free_result($result);
	if(strlen($body)>0) {
		$curdate = date("Y년 m월 d일");
		$pattern = array ("(\[SHOP\])","(\[NAME\])","(\[ID\])","(\[PASSWORD\])","(\[URL\])","(\[CURDATE\])");
		$replace = array ($shopname,$name,$id,$passwd,$shopurl,$curdate);
		$body	 = preg_replace($pattern,$replace,$body);
		if (strlen($shopname)>0) $mailshopname = "=?ks_c_5601-1987?B?".base64_encode($shopname)."?=";
		$header=getMailHeader($mailshopname,$info_email);
		if(ismail($email)) {
			sendmail($email, $subject, $body, $header);
		}
	}
}

//입금확인메일
function SendBankMail($shopname, $shopurl, $mail_type, $info_email, $email, $ordercode) {
	$sql = "SELECT * FROM ".$designnewpageTables." WHERE type='bankmail' ";
	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		//개별디자인
		$pattern = array ("(\[SHOP\])");
		$replace = array ($shopname);
		$subject = preg_replace($pattern,$replace,$row->subject);
		$body	 = $row->body;
	} else {
		//템플릿
		$subject = $shopname." 입금 확인 메일입니다.";
		$buffer="";
		if(file_exists(DirPath.TempletDir."mail/bankmail".$mail_type.".php")) {
			$fp=fopen(DirPath.TempletDir."mail/bankmail".$mail_type.".php","r");
			if($fp) {
				while (!feof($fp)) {$buffer.= fgets($fp, 1024);}
			}
			fclose($fp);
			$body=$buffer;
		}
	}
	mysql_free_result($result);
	if(strlen($body)>0) {
		$orderdate = substr($ordercode,0,4)."/".substr($ordercode,4,2)."/".substr($ordercode,6,2);
		$bankdate = date("Y/m/d");
		$curdate = date("Y년 m월 d일");
		$pattern = array ("(\[SHOP\])","(\[URL\])","(\[BANKDATE\])","(\[ORDERDATE\])","(\[CURDATE\])");
		$replace = array ($shopname,$shopurl,$bankdate,$orderdate,$curdate);
		$body	 = preg_replace($pattern,$replace,$body);
		if (strlen($shopname)>0) $mailshopname = "=?ks_c_5601-1987?B?".base64_encode($shopname)."?=";
		$header=getMailHeader($mailshopname,$info_email);
		if(ismail($email)) {
			sendmail($email, $subject, $body, $header);
		}
	}
}

//회원인증메일
function SendAuthMail($shopname, $shopurl, $mail_type, $info_email, $email, $id, $name) {
	$sql = "SELECT * FROM ".$designnewpageTables." WHERE type='authmail' ";
	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		//개별디자인
		$pattern = array ("(\[SHOP\])","(\[ID\])");
		$replace = array ($shopname,$id);
		$subject = preg_replace($pattern,$replace,$row->subject);
		$body	 = $row->body;
	} else {
		//템플릿
		$subject = $shopname." 회원 인증 메일입니다.";
		$buffer="";
		if(file_exists(DirPath.TempletDir."mail/authmail".$mail_type.".php")) {
			$fp=fopen(DirPath.TempletDir."mail/authmail".$mail_type.".php","r");
			if($fp) {
				while (!feof($fp)) {$buffer.= fgets($fp, 1024);}
			}
			fclose($fp);
			$body=$buffer;
		}
	}
	mysql_free_result($result);
	if(strlen($body)>0) {
		$okdate=date("Y/m/d");
		$curdate = date("Y년 m월 d일");
		$pattern = array ("(\[SHOP\])","(\[URL\])","(\[OKDATE\])","(\[ID\])","(\[NAME\])","(\[CURDATE\])");
		$replace = array ($shopname,$shopurl,$okdate,$id,$name,$curdate);
		$body	 = preg_replace($pattern,$replace,$body);
		if (strlen($shopname)>0) $mailshopname = "=?ks_c_5601-1987?B?".base64_encode($shopname)."?=";
		$header=getMailHeader($mailshopname,$info_email);
		if(ismail($email)) {
			sendmail($email, $subject, $body, $header);
		}
	}
}

//입금확인메일 2011-09 추가
function SendBankMail2($shopname, $shopurl, $mail_type, $info_email, $email, $ordercode) {
	//템플릿
	$subject = $shopname." 입금 확인 메일입니다.";
	$buffer="";
	if(file_exists(DirPath.TempletDir."mail/bankmail2001.php")) {
		$fp=fopen(DirPath.TempletDir."mail/bankmail2001.php","r");
		if($fp) {
			while (!feof($fp)) {$buffer.= fgets($fp, 1024);}
		}
		fclose($fp);
		$body=$buffer;
	}

	if(strlen($body)>0) {
		$orderdate = substr($ordercode,0,4)."/".substr($ordercode,4,2)."/".substr($ordercode,6,2);
		$bankdate = date("Y/m/d");
		$curdate = date("Y년 m월 d일");
		$pattern = array ("(\[SHOP\])","(\[URL\])","(\[BANKDATE\])","(\[ORDERDATE\])","(\[CURDATE\])");
		$replace = array ($shopname,$shopurl,$bankdate,$orderdate,$curdate);
		$body	 = preg_replace($pattern,$replace,$body);
		if (strlen($shopname)>0) $mailshopname = "=?ks_c_5601-1987?B?".base64_encode($shopname)."?=";
		$header=getMailHeader($mailshopname,$info_email);
		if(ismail($email)) {
			sendmail($email, $subject, $body, $header);
		}
	}
}

//선물권인증메일
function SendGiftAuthMail($shopname, $shopurl, $mail_type, $info_email, $ordercode, $authcode, $price) {
	$subject = $shopname." 선물권 인증 메일입니다.";
	$buffer="";
	if(file_exists(DirPath.TempletDir."mail/giftauthmail001.php")) {
		$fp=fopen(DirPath.TempletDir."mail/giftauthmail001.php","r");
		if($fp) {
			while (!feof($fp)) {$buffer.= fgets($fp, 1024);}
		}
		fclose($fp);
		$body=$buffer;
	}

	if(strlen($body)>0) {
		$sql = "SELECT * FROM tblorderinfo WHERE ordercode='{$ordercode}' ";
		$result=mysql_query($sql,get_db_conn());
		$row = mysql_fetch_array($result);
		mysql_free_result($result);

		$id = $row['id'];
		$name=$row['sender_name'];
		$toname=$row['receiver_name'];
		$email = $row['receiver_addr'];

		$sql = "SELECT * FROM tblorderproduct WHERE ordercode='{$ordercode}' ";
		$result2=mysql_query($sql,get_db_conn());
		$row2 = mysql_fetch_array($result2);
		mysql_free_result($result2);

		$msg = ereg_replace("\r\n","<br>",$row2['order_prmsg']);

		$okdate=date("Y/m/d");
		$curdate = date("Y년 m월 d일");
		$pattern = array ("(\[SHOP\])","(\[URL\])","(\[OKDATE\])","(\[ID\])","(\[MSG\])","(\[AUTHCODE\])","(\[NAME\])","(\[TONAME\])","(\[PRICE\])","(\[CURDATE\])");
		$replace = array ($shopname,$shopurl,$okdate,$id,$msg,$authcode,$name,$toname,number_format($price),$curdate);
		$body	 = preg_replace($pattern,$replace,$body);
		if (strlen($shopname)>0) $mailshopname = "=?ks_c_5601-1987?B?".base64_encode($shopname)."?=";
		$header=getMailHeader($mailshopname,$info_email);
		if(ismail($email)) {
			sendmail($email, $subject, $body, $header);
		}
	}
}

//가입홍보메일 2011-08-08 추가
function SendUrlMail($shopname, $shopurl, $mail_type, $message, $info_email, $email, $name, $url_id, $id, $reserve_join) {
	$sql = "SELECT * FROM ".$designnewpageTables." WHERE type='hongbomail' ";
	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		//개별디자인
		$pattern = array ("(\[SHOP\])","(\[NAME\])","(\[URL_ID\])");
		$replace = array ($shopname,$name,$url_id);
		$subject = preg_replace($pattern,$replace,$row->subject);
		$body	 = $row->body;
	} else {
		//템플릿
		$subject = $name."[".$id."]님이 ".$shopname."에 초대합니다.";
		$buffer="";
		if(file_exists(DirPath.TempletDir."mail/hongbomail".$mail_type.".php")) {
			$fp=fopen(DirPath.TempletDir."mail/hongbomail".$mail_type.".php","r");
			if($fp) {
				while (!feof($fp)) {$buffer.= fgets($fp, 1024);}
			}
			fclose($fp);
			$body=$buffer;
		}
	}
	mysql_free_result($result);
	if(strlen($body)>0) {
		$curdate = date("Y년 m월 d일");
		$pattern = array ("(\[SHOP\])","(\[NAME\])","(\[URL_ID\])","(\[MESSAGE\])","(\[URL\])","(\[CURDATE\])","(\[RESERVE\])");
		$replace = array ($shopname,$name."[".$id."]",$url_id,$message,$shopurl,$curdate,$reserve_join);
		$body	 = preg_replace($pattern,$replace,$body);
		if (strlen($shopname)>0) $mailshopname = "=?ks_c_5601-1987?B?".base64_encode($shopname)."?=";
		$header=getMailHeader($mailshopname,$info_email);
		if(ismail($email)) {
			sendmail($email, $subject, $body, $header);
		}
	}
}

//선물하기 메일
function SendPresentMail($shopname, $shopurl, $mail_type, $message, $send_email, $send_name, $re_email, $re_name, $ordercode) {
	$sql = "SELECT * FROM ".$designnewpageTables." WHERE type='presentmail' ";
	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		//개별디자인
		$pattern = array ("(\[SHOP\])","(\[NAME\])","(\[RE_NAME\])");
		$replace = array ($shopname,$send_name,$re_name);
		$subject = preg_replace($pattern,$replace,$row->subject);
		$body	 = $row->body;
	} else {
		//템플릿
		$subject = $send_name."님이 ".$re_name."님께 선물을 보내셨습니다.";
		$buffer="";
		if(file_exists(DirPath.TempletDir."mail/presentmail".$mail_type.".php")) {
			$fp=fopen(DirPath.TempletDir."mail/presentmail".$mail_type.".php","r");
			if($fp) {
				while (!feof($fp)) {$buffer.= fgets($fp, 1024);}
			}
			fclose($fp);
			$body=$buffer;
		}
	}
	mysql_free_result($result);
	if(strlen($body)>0) {
		$curdate = date("Y년 m월 d일");
		$pattern = array ("(\[SHOP\])","(\[NAME\])","(\[RE_NAME\])","(\[MESSAGE\])","(\[ORDERCODE\])","(\[URL\])","(\[CURDATE\])");
		$replace = array ($shopname,$send_name,$re_name,$message,$ordercode,$shopurl,$curdate);
		$body	 = preg_replace($pattern,$replace,$body);
		if (strlen($shopname)>0) $mailshopname = "=?ks_c_5601-1987?B?".base64_encode($shopname)."?=";
		$header=getMailHeader($mailshopname,$send_email);
		if(ismail($re_email)) {
			sendmail($re_email, $subject, $body, $header);
		}
	}
}









//조르기 메일
//보낸사람 email, name , 받는 사람 email, name
function SendPesterMail($shopname, $shopurl, $mail_type, $message, $send_email, $send_name, $re_email, $re_name, $pester_code) {
	$sql = "SELECT * FROM ".$designnewpageTables." WHERE type='pestermail' ";
	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		//개별디자인
		$pattern = array ("(\[SHOP\])","(\[NAME\])","(\[RE_NAME\])");
		$replace = array ($shopname,$send_name,$re_name);
		$subject = preg_replace($pattern,$replace,$row->subject);
		$body	 = $row->body;
	} else {
		//템플릿
		$subject = $send_name."님의 조르기";
		$buffer="";
		if(file_exists(DirPath.TempletDir."mail/pestermail".$mail_type.".php")) {
			$fp=fopen(DirPath.TempletDir."mail/pestermail".$mail_type.".php","r");
			if($fp) {
				while (!feof($fp)) {$buffer.= fgets($fp, 1024);}
			}
			fclose($fp);
			$body=$buffer;
		}
	}
	mysql_free_result($result);

	$sql = "SELECT * FROM tblpesterinfo WHERE code='".$pester_code."' limit 1";
	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		$pester_tempkey = $row->tempkey;
	}
	mysql_free_result($result);

	$sql = "SELECT a.productcode,a.package_idx,a.quantity,c.package_list,c.package_title,c.package_price ";
	$sql.= "FROM tblbasket_pester_save AS a, tblproduct AS b, tblproductpackage AS c ";
	$sql.= "WHERE a.productcode=b.productcode ";
	$sql.= "AND b.package_num=c.num ";
	$sql.= "AND a.tempkey='".$pester_tempkey."' ";
	$sql.= "AND a.package_idx>0 ";
	$sql.= "AND b.display = 'Y' ";

	$pesterrs = mysql_query($sql,get_db_conn());
	while($pesterrow=@mysql_fetch_object($pesterrs)) {
		if(strlen($pesterrow->package_title)>0 && strlen($pesterrow->package_idx)>0 && $pesterrow->package_idx>0) {
			$package_title_exp = explode("",$pesterrow->package_title);
			$package_price_exp = explode("",$pesterrow->package_price);
			$package_list_exp = explode("", $pesterrow->package_list);

			$title_package_listtmp[$pesterrow->productcode][$pesterrow->package_idx] = $package_title_exp[$pesterrow->package_idx];

			if(strlen($package_list_exp[$pesterrow->package_idx])>1) {
				$basketsql3 = "SELECT productcode,quantity,productname,tinyimage,sellprice FROM tblproduct ";
				$basketsql3.= "WHERE pridx IN ('".str_replace(",","','",$package_list_exp[$pesterrow->package_idx])."') ";
				$basketsql3.= "AND display = 'Y' ";

				$basketresult3 = mysql_query($basketsql3,get_db_conn());
				$sellprice_package_listtmp=0;
				while($basketrow3=@mysql_fetch_object($basketresult3)) {
					$assemble_proquantity[$basketrow3->productcode]+=$pesterrow->quantity;
					$productcode_package_listtmp[] = $basketrow3->productcode;
					$quantity_package_listtmp[] = $basketrow3->quantity;
					$productname_package_listtmp[] = $basketrow3->productname;
					$tinyimage_package_listtmp[] = $basketrow3->tinyimage;
					$sellprice_package_listtmp+= $basketrow3->sellprice;
				}
				@mysql_free_result($basketresult3);

				if(count($productcode_package_listtmp)>0) {  //장바구니 패키지 상품 정보 출력시 필요한 정보
					$price_package_listtmp[$pesterrow->productcode][$pesterrow->package_idx]=0;
					if((int)$sellprice_package_listtmp>0) {
						$price_package_listtmp[$pesterrow->productcode][$pesterrow->package_idx]=(int)$sellprice_package_listtmp;
						if(strlen($package_price_exp[$pesterrow->package_idx])>0) {
							$package_price_expexp = explode(",",$package_price_exp[$pesterrow->package_idx]);
							if(strlen($package_price_expexp[0])>0 && $package_price_expexp[0]>0) {
								$sumsellpricecal=0;
								if($package_price_expexp[1]=="Y") {
									$sumsellpricecal = ((int)$sellprice_package_listtmp*$package_price_expexp[0])/100;
								} else {
									$sumsellpricecal = $package_price_expexp[0];
								}
								if($sumsellpricecal>0) {
									if($package_price_expexp[2]=="Y") {
										$sumsellpricecal = $sellprice_package_listtmp-$sumsellpricecal;
									} else {
										$sumsellpricecal = $sellprice_package_listtmp+$sumsellpricecal;
									}
									if($sumsellpricecal>0) {
										if($package_price_expexp[4]=="F") {
											$sumsellpricecal = floor($sumsellpricecal/($package_price_expexp[3]*10))*($package_price_expexp[3]*10);
										} else if($package_price_expexp[4]=="R") {
											$sumsellpricecal = round($sumsellpricecal/($package_price_expexp[3]*10))*($package_price_expexp[3]*10);
										} else {
											$sumsellpricecal = ceil($sumsellpricecal/($package_price_expexp[3]*10))*($package_price_expexp[3]*10);
										}
										$price_package_listtmp[$pesterrow->productcode][$pesterrow->package_idx]=$sumsellpricecal;
									}
								}
							}
						}
					}

					$productcode_package_list[$pesterrow->productcode][$pesterrow->package_idx] = $productcode_package_listtmp;
					$quantity_package_list[$pesterrow->productcode][$pesterrow->package_idx] = $quantity_package_listtmp;
					$productname_package_list[$pesterrow->productcode][$pesterrow->package_idx] = $productname_package_listtmp;
					$tinyimage_package_list[$pesterrow->productcode][$pesterrow->package_idx] = $tinyimage_package_listtmp;
				}
				unset($productcode_package_listtmp);
				unset($quantity_package_listtmp);
				unset($productname_package_listtmp);
			}
		}
	}
	@mysql_free_result($pesterrs);

	$sql = "SELECT b.vender FROM tblbasket_pester_save a, tblproduct b WHERE a.tempkey='".$pester_tempkey."' ";
	$sql.= "AND a.productcode=b.productcode GROUP BY b.vender ";
	$res=mysql_query($sql,get_db_conn());

	$cnt=0;
	$sumprice = 0;
	$deli_price = 0;
	$reserve = 0;
	$arr_prlist=array();
	while($vgrp=mysql_fetch_object($res)) {
		unset($_vender);
		if($vgrp->vender>0) {
			$sql = "SELECT deli_price, deli_pricetype, deli_mini, deli_limit FROM tblvenderinfo WHERE vender='".$vgrp->vender."' ";
			$res2=mysql_query($sql,get_db_conn());
			if($_vender=mysql_fetch_object($res2)) {
				if($_vender->deli_price==-9) {
					$_vender->deli_price=0;
					$_vender->deli_after="Y";
				}
				if ($_vender->deli_mini==0) $_vender->deli_mini=1000000000;
			}
			mysql_free_result($res2);

		}
		$sql = "SELECT a.opt1_idx,a.opt2_idx,a.optidxs,a.quantity,b.productcode,b.productname,b.sellprice, ";
		$sql.= "b.reserve,b.reservetype,b.addcode,b.tinyimage,b.option_price,b.option_quantity,b.option1,b.option2, ";
		$sql.= "b.etctype,b.deli_price,b.deli,b.sellprice*a.quantity as realprice, b.selfcode,a.assemble_list,a.assemble_idx,a.package_idx ";
		$sql.= ", a.basketidx, b.sns_state,b.present_state,b.pester_state,b.sns_reserve2,b.sns_reserve2_type, a.sell_memid "; //sns 및 기타 추가기능 정보
		$sql.= "FROM tblbasket_pester_save a, tblproduct b WHERE b.vender='".$vgrp->vender."' ";
		$sql.= "AND a.tempkey='".$pester_tempkey."' ";
		$sql.= "AND a.productcode=b.productcode ";
		$sql.= "ORDER BY a.date DESC ";
		$result=mysql_query($sql,get_db_conn());

		$vender_sumprice = 0;
		$vender_delisumprice = 0;//해당 입점업체의 기본배송비 총 구매액
		$vender_deliprice = 0;
		$deli_productprice=0;
		$deli_init = false;

		while($row = mysql_fetch_object($result)) {

			if(ereg("^(\[OPTG)([0-9]{4})(\])$",$row->option1)){
				$optioncode = substr($row->option1,5,4);
				$row->option1="";
				$row->option_price="";
				if($row->optidxs!="") {
					$tempoptcode = substr($row->optidxs,0,-1);
					$exoptcode = explode(",",$tempoptcode);

					$sqlopt = "SELECT * FROM tblproductoption WHERE option_code='".$optioncode."' ";
					$resultopt = mysql_query($sqlopt,get_db_conn());
					if($rowopt = mysql_fetch_object($resultopt)){
						$optionadd = array (&$rowopt->option_value01,&$rowopt->option_value02,&$rowopt->option_value03,&$rowopt->option_value04,&$rowopt->option_value05,&$rowopt->option_value06,&$rowopt->option_value07,&$rowopt->option_value08,&$rowopt->option_value09,&$rowopt->option_value10);
						$opti=0;
						$optvalue="";
						$option_choice = $rowopt->option_choice;
						$exoption_choice = explode("",$option_choice);
						while(strlen($optionadd[$opti])>0){
							if($exoptcode[$opti]>0){
								$opval = explode("",str_replace('"','',$optionadd[$opti]));
								$optvalue.= ", ".$opval[0]." : ";
								$exop = explode(",",str_replace('"','',$opval[$exoptcode[$opti]]));
								if ($exop[1]>0) $optvalue.=$exop[0]."(<font color=#FF3C00>+".number_format($exop[1])."원</font>)";
								else if($exop[1]==0) $optvalue.=$exop[0];
								else $optvalue.=$exop[0]."(<font color=#FF3C00>".number_format($exop[1])."원</font>)";
								$row->sellprice+=($row->quantity*$exop[1]);
							}
							$opti++;
						}
						$optvalue = substr($optvalue,1);
					}
				}
			} else {
				$optvalue="";
			}

			$cnt++;

			$assemble_str="";
			$package_str="";
			if($row->assemble_idx>0 && strlen(str_replace("","",$row->assemble_list))>0) {
				$assemble_list_proexp = explode("",$row->assemble_list);
				$alprosql = "SELECT productcode,productname,sellprice FROM tblproduct ";
				$alprosql.= "WHERE productcode IN ('".implode("','",$assemble_list_proexp)."') ";
				$alprosql.= "AND display = 'Y' ";
				$alprosql.= "ORDER BY FIELD(productcode,'".implode("','",$assemble_list_proexp)."') ";
				$alproresult=mysql_query($alprosql,get_db_conn());

				$assemble_str ="		<td width=\"50\" valign=\"top\" style=\"padding-left:12px;\" nowrap><font color=\"#FF7100\" style=\"line-height:10px;\">┃<br>┗━<b>▶</b></font></td>\n";
				$assemble_str.="		<td width=\"100%\">\n";
				$assemble_str.="		<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-left:1px #e4e4e4 solid;border-top:1px #e4e4e4 solid;border-right:1px #e4e4e4 solid;\">\n";

				$assemble_sellerprice=0;
				while($alprorow=@mysql_fetch_object($alproresult)) {
					$assemble_str.="		<tr>\n";
					$assemble_str.="			<td bgcolor=\"#FFFFFF\" style=\"border-bottom:1px #e4e4e4 solid;\">\n";
					$assemble_str.="			<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
					$assemble_str.="			<col width=\"\"></col>\n";
					$assemble_str.="			<col width=\"80\"></col>\n";
					$assemble_str.="			<col width=\"120\"></col>\n";
					$assemble_str.="			<tr>\n";
					$assemble_str.="				<td style=\"padding:4px;word-break:break-all;\"><font color=\"#000000\">".$alprorow->productname."</font>&nbsp;</td>\n";
					$assemble_str.="				<td align=\"right\" style=\"padding:4px;border-left:1px #e4e4e4 solid;border-right:1px #e4e4e4 solid;\"><font color=\"#000000\">".number_format((int)$alprorow->sellprice)."원</font></td>\n";
					$assemble_str.="				<td align=\"center\" style=\"padding:4px;\">본 상품 1개당 수량1개</td>\n";
					$assemble_str.="			</tr>\n";
					$assemble_str.="			</table>\n";
					$assemble_str.="			</td>\n";
					$assemble_str.="		</tr>\n";
					$assemble_sellerprice+=$alprorow->sellprice;
				}
				@mysql_free_result($alproresult);
				$assemble_str.="		</table>\n";
				$assemble_str.="		</td>\n";

				//######### 코디/조립에 따른 가격 변동 체크 ###############
				$price = $assemble_sellerprice*$row->quantity;
				$tempreserve = getReserveConversion($row->reserve,$row->reservetype,$assemble_sellerprice,"N");
				//sns홍보일 경우 적립금
				if($_data->sns_ok == "Y" && $row->sns_state == "Y" && $row->sell_memid !=""){
					$tempreserve = getReserveConversionSNS($tempreserve,$row->sns_reserve2,$row->sns_reserve2_type,$assemble_sellerprice,"N");
				}
				$sellprice=$assemble_sellerprice;
			} else if($row->package_idx>0 && strlen($row->package_idx)>0) {
				$package_str = $title_package_listtmp[$row->productcode][$row->package_idx]."(<font color=#FF3C00>+".number_format($price_package_listtmp[$row->productcode][$row->package_idx])."원</font>)";

				$productname_package_list_exp = $productname_package_list[$row->productcode][$row->package_idx];
				if(count($productname_package_list_exp)>0) {
					$packagelist_str ="		<td width=\"50\" valign=\"top\" style=\"padding-left:12px;\" nowrap><font color=\"#FF7100\" style=\"line-height:10px;\">┃<br>┗━<b>▶</b></font></td>\n";
					$packagelist_str.="		<td width=\"100%\">\n";
					$packagelist_str.="		<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-left:1px #e4e4e4 solid;border-top:1px #e4e4e4 solid;border-right:1px #e4e4e4 solid;\">\n";

					for($i=0; $i<count($productname_package_list_exp); $i++) {
						$packagelist_str.="		<tr>\n";
						$packagelist_str.="			<td bgcolor=\"#FFFFFF\" style=\"border-bottom:1px #e4e4e4 solid;\">\n";
						$packagelist_str.="			<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
						$packagelist_str.="			<col width=\"\"></col>\n";
						$packagelist_str.="			<col width=\"120\"></col>\n";
						$packagelist_str.="			<tr>\n";
						$packagelist_str.="				<td style=\"padding:4px;word-break:break-all;\"><font color=\"#000000\">".$productname_package_list_exp[$i]."</font>&nbsp;</td>\n";
						$packagelist_str.="				<td align=\"center\" style=\"padding:4px;border-left:1px #e4e4e4 solid;\">본 상품 1개당 수량1개</td>\n";
						$packagelist_str.="			</tr>\n";
						$packagelist_str.="			</table>\n";
						$packagelist_str.="			</td>\n";
						$packagelist_str.="		</tr>\n";
					}
					$packagelist_str.="		</table>\n";
					$packagelist_str.="		</td>\n";
				} else {
					$packagelist_str ="		<td width=\"50\" valign=\"top\" style=\"padding-left:12px;\" nowrap><font color=\"#FF7100\" style=\"line-height:10px;\">┃<br>┗━<b>▶</b></font></td>\n";
					$packagelist_str.="		<td width=\"100%\">\n";
					$packagelist_str.="		<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-left:1px #e4e4e4 solid;border-top:1px #e4e4e4 solid;border-right:1px #e4e4e4 solid;\">\n";
					$packagelist_str.="		<tr>\n";
					$packagelist_str.="			<td bgcolor=\"#FFFFFF\" style=\"border-bottom:1px #e4e4e4 solid;padding:4px;word-break:break-all;\"><font color=\"#000000\">구성상품이 존재하지 않는 패키지</font></td>\n";
					$packagelist_str.="		</tr>\n";
					$packagelist_str.="		</table>\n";
					$packagelist_str.="		</td>\n";
				}
				//######### 옵션에 따른 가격 변동 체크 ###############
				if (strlen($row->option_price)==0) {
					$sellprice=$row->sellprice+$price_package_listtmp[$row->productcode][$row->package_idx];
					$price = $sellprice*$row->quantity;
					$tempreserve = getReserveConversion($row->reserve,$row->reservetype,$sellprice,"N");
					//sns홍보일 경우 적립금
					if($_data->sns_ok == "Y" && $row->sns_state == "Y" && $row->sell_memid !=""){
						$tempreserve = getReserveConversionSNS($tempreserve,$row->sns_reserve2,$row->sns_reserve2_type,$sellprice,"N");
					}
				} else if (strlen($row->opt1_idx)>0) {
					$option_price = $row->option_price;
					$pricetok=explode(",",$option_price);
					$priceindex = count($pricetok);
					$sellprice=$pricetok[$row->opt1_idx-1]+$price_package_listtmp[$row->productcode][$row->package_idx];
					$price = $sellprice*$row->quantity;
					$tempreserve = getReserveConversion($row->reserve,$row->reservetype,$sellprice,"N");
					//sns홍보일 경우 적립금
					if($_data->sns_ok == "Y" && $row->sns_state == "Y" && $row->sell_memid !=""){
						$tempreserve = getReserveConversionSNS($tempreserve,$row->sns_reserve2,$row->sns_reserve2_type,$sellprice,"N");
					}
				}
			} else {
				//######### 옵션에 따른 가격 변동 체크 ###############
				if (strlen($row->option_price)==0) {
					$price = $row->realprice;
					$tempreserve = getReserveConversion($row->reserve,$row->reservetype,$row->sellprice,"N");
					//sns홍보일 경우 적립금
					if($_data->sns_ok == "Y" && $row->sns_state == "Y" && $row->sell_memid !=""){
						$tempreserve = getReserveConversionSNS($tempreserve,$row->sns_reserve2,$row->sns_reserve2_type,$row->sellprice,"N");
					}
					$sellprice=$row->sellprice;
				} else if (strlen($row->opt1_idx)>0) {
					$option_price = $row->option_price;
					$pricetok=explode(",",$option_price);
					$priceindex = count($pricetok);
					$price = $pricetok[$row->opt1_idx-1]*$row->quantity;
					$tempreserve = getReserveConversion($row->reserve,$row->reservetype,$pricetok[$row->opt1_idx-1],"N");
					//sns홍보일 경우 적립금
					if($_data->sns_ok == "Y" && $row->sns_state == "Y" && $row->sell_memid !=""){
						$tempreserve = getReserveConversionSNS($tempreserve,$row->sns_reserve2,$row->sns_reserve2_type,$pricetok[$row->opt1_idx-1],"N");
					}
					$sellprice=$pricetok[$row->opt1_idx-1];
				}
			}

			$sumprice += $price;
			$vender_sumprice += $price;

			$deli_str = "";
			if (($row->deli=="Y" || $row->deli=="N") && $row->deli_price>0) {
				if($row->deli=="Y") {
					$deli_productprice += $row->deli_price*$row->quantity;
					$deli_str = "&nbsp;<font color=a00000>- 개별배송비<font color=#FF3C00>(구매수 대비 증가:".number_format($row->deli_price*$row->quantity)."원)</font></font>";
				} else {
					$deli_productprice += $row->deli_price;
					$deli_str = "&nbsp;<font color=a00000>- 개별배송비<font color=#FF3C00>(".number_format($row->deli_price)."원)</font></font>";
				}
			} else if($row->deli=="F" || $row->deli=="G") {
				$deli_productprice += 0;
				if($row->deli=="F") {
					$deli_str = "&nbsp;<font color=a00000>- 개별배송비<font color=#0000FF>(무료)</font></font>";
				} else {
					$deli_str = "&nbsp;<font color=a00000>- 개별배송비<font color=#38A422>(착불)</font></font>";
				}
			} else {
				$deli_init=true;
				$vender_delisumprice += $price;
			}

			$productname=$row->productname;

			$arr_prlist[$row->productcode]=$row->productname;

			$reserve += $tempreserve*$row->quantity;

			$bankonly_html = ""; $setquota_html = "";
			if (strlen($row->etctype)>0) {
				$etctemp = explode("",$row->etctype);
				for ($i=0;$i<count($etctemp);$i++) {
					switch ($etctemp[$i]) {
						case "BANKONLY": $bankonly = "Y";
							$bankonly_html = " <img src=http://".$shopurl." src=http://".$shopurl."images/common/bankonly.gif border=0 align=absmiddle> ";
							break;
						case "SETQUOTA":
							if ($_data->card_splittype=="O" && $price>=$_data->card_splitprice) {
								$setquotacnt++;
								$setquota_html = " <img src=http://".$shopurl."images/common/setquota.gif border=0 align=absmiddle>";
								$setquota_html.= "</b><font color=black size=1>(";
								//$setquota_html.="3~";
								$setquota_html.= $_data->card_splitmonth.")</font>";
							}
							break;
					}
				}
			}
			$pester_content .="<tr>
				<td align=\"center\" valign=\"middle\" style=\"padding:10px 0 10px 10px;\">";
			if(strlen($row->tinyimage)!=0 && file_exists(DirPath.DataDir."shopimages/product/".$row->tinyimage)){
				$pester_content .="<img src=\"http://".$shopurl.DataDir."shopimages/product/".$row->tinyimage."\" ";
			} else {
				$pester_content .="<img src=\"http://".$shopurl."images/no_img.gif\" ";
			}
			$pester_content .=" width=\"54\" height=\"54\" style='border:1px solid #d0d0d0;'></td>
				<td style='padding-left:10px;word-break:break-all;'>
				<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">
				<tr>
					<td style=\"font-size:12px;padding-left:2px;word-break:break-all;\"><font color=\"#ff6c00\"><b>".$productname."</font></td>
				</tr>";
			if (strlen($row->option1)>0 || strlen($row->option2)>0 || strlen($optvalue)>0) {
				$pester_content .="<tr>
					<td style=\"padding:1,0,1,0;font-size:11px;letter-spacing:-0.5pt;word-break:break-all;\">
					<img src=\"http://".$shopurl."images/common/icn_option.gif\" border=\"0\" align=\"absmiddle\">";

				if (strlen($row->option1)>0 && $row->opt1_idx>0) {
					$temp = $row->option1;
					$tok = explode(",",$temp);
					$count=count($tok);
					$pester_content .= $tok[0]." : ".$tok[$row->opt1_idx]."\n";
				}
				if (strlen($row->option2)>0 && $row->opt2_idx>0) {
					$temp = $row->option2;
					$tok = explode(",",$temp);
					$count=count($tok);
					$pester_content .=",&nbsp; ".$tok[0]." : ".$tok[$row->opt2_idx]."\n";
				}
				if(strlen($optvalue)>0) {
					$pester_content .= $optvalue."\n";
				}
				$pester_content .="	</td>
				</tr>";
			}
			if (strlen($package_str)>0) { // 패키지 정보
				$pester_content .="<tr>
					<td width=\"100%\" style=\"padding-top:2px;font-size:11px;letter-spacing:-0.5pt;line-height:15px;word-break:break-all;\"><img src=\"http://".$shopurl."images/common/icn_package.gif\" border=\"0\" align=\"absmiddle\"> ".(strlen($package_str)>0?$package_str:"")."</td>
				</tr>";
			}

			$pester_content .="	</table>
				</td>";
			if ($_data->reserve_maxuse>=0) {
				$pester_content .="	<td align=\"right\" style=\"padding-right:5px;\"><font color=\"#333333\">". number_format($tempreserve) ."원</font></td>";
			} else {
				$pester_content .="	<td align=\"center\"><font color=\"#333333\">없음</font></td>";
			}
			$pester_content .="	<td align=\"center\"><font color=\"#333333\"><B>".number_format($sellprice)."원</B></font></td>
				<td align=\"center\"><font color=\"#333333\">".$row->quantity."개</font></td>
				<td align=\"center\"><b><font color=\"#ff6c00\">". number_format($price) ."원</font></b></td>
			</tr>";
			if (strlen($assemble_str)>0) { // 코디/조립 정보
				$pester_content .="			<tr>
				<td colspan=\"6\" style=\"padding:5px;padding-top:0px;padding-left:20px;\">
				<table border=0 width=\"100%\" cellpadding=\"0\" cellspacing=\"0\">
				<tr>".$assemble_str."
				</tr>
				</table>
				</td>
			</tR>";
			}

			if (strlen($packagelist_str)>0) { // 패키지 정보
				$pester_content .="			<tr id=\"packageidx".$cnt."\" style=\"display:none;\">
				<td colspan=\"6\" style=\"padding:5px;padding-top:0px;padding-left:60px;\">
				<table border=0 width=\"100%\" cellpadding=\"0\" cellspacing=\"0\">
				<tr>".$packagelist_str."</tr>
				</table>
				<td>
			</tr>";
			}
			$pester_content .="			<tr><td colspan=\"6\" height=\"1\" bgcolor=\"#e4e4e4\"></td></tr>";
		}
		mysql_free_result($result);

		$vender_deliprice=$deli_productprice;

		if($_vender) {
			if($_vender->deli_price>0) {
				if($_vender->deli_pricetype=="Y") {
					$vender_delisumprice = $vender_sumprice;
				}

				if ($vender_delisumprice<$_vender->deli_mini && $deli_init==true) {
					$vender_deliprice+=$_vender->deli_price;
				}
			} else if(strlen($_vender->deli_limit)>0) {
				if($_vender->deli_pricetype=="Y") {
					$vender_delisumprice = $vender_sumprice;
				}
				if($deli_init==true) {
					$delilmitprice = setDeliLimit($vender_delisumprice,$_vender->deli_limit);
					$vender_deliprice+=$delilmitprice;
				}
			}
		} else {
			if($_data->deli_basefee>0) {
				if($_data->deli_basefeetype=="Y") {
					$vender_delisumprice = $vender_sumprice;
				}

				if ($vender_delisumprice<$_data->deli_miniprice && $deli_init==true) {
					$vender_deliprice+=$_data->deli_basefee;
				}
			} else if(strlen($_data->deli_limit)>0) {
				if($_data->deli_basefeetype=="Y") {
					$vender_delisumprice = $vender_sumprice;
				}

				if($deli_init==true) {
					$delilmitprice = setDeliLimit($vender_delisumprice,$_data->deli_limit);
					$vender_deliprice+=$delilmitprice;
				}
			}
		}
		$deli_price+=$vender_deliprice;

		$pester_content .="<tr>\n";
		$pester_content .="	<td colspan=6 style=\"padding:3\">\n";
		$pester_content .="	<table border=0 cellpadding=5 cellspacing=0 bgcolor=#efefef width=100% style=\"table-layout:fixed\">\n";
		$pester_content .="	<col width=></col>\n";
		$pester_content .="	<col width=100></col>\n";
		$pester_content .="	<col width=120></col>\n";
		$pester_content .="	<col width=100></col>\n";
		$pester_content .="	<col width=130></col>\n";
		$pester_content .="	<tr>\n";
		$pester_content .="		<td bgcolor=#ffffff colspan=5 align=right style=\"font-size:12px;\"><FONT COLOR=#000000>배송비</FONT> : <FONT COLOR=#000000>".number_format($vender_deliprice)."원</FONT> / <FONT COLOR=#000000>합계</FONT> : <FONT COLOR=#000000><B>".number_format($vender_sumprice)."원</B></FONT></td>\n";
		$pester_content .="	</tr>\n";
		$pester_content .="	</table>\n";
		$pester_content .="	</td>\n";
		$pester_content .="</tr>\n";
		$pester_content .="<tr><td colspan=6 height=1 bgcolor=\"#969696\"></td></tr>\n";
	}
	mysql_free_result($res);

	if($sumprice>0) {
		if(strlen($group_type)>0 && $group_type!=NULL && $sumprice>=$group_usemoney) {
			$salemoney=0;
			$salereserve=0;
			if($group_type=="SW" || $group_type=="SP") {
				if($group_type=="SW") {
					$salemoney=$group_addmoney;
				} else if($group_type=="SP") {
					$salemoney=substr(((int)($sumprice*($group_addmoney/100))),0,-2)."00";
				}
			}
			if($group_type=="RW" || $group_type=="RP" || $group_type=="RQ") {
				if($group_type=="RW") {
					$salereserve=$group_addmoney;
				} else if($group_type=="RP") {
					$salereserve=$reserve*($group_addmoney-1);
				} else if($group_type=="RQ") {
					$salereserve=substr(((int)($sumprice*($group_addmoney/100))),0,-2)."00";
				}
			}
		}

		$pester_content .="<tr>\n";
		$pester_content .="	<td colspan=6 bgcolor=#ffffff align=right>\n";
		$pester_content .="	<table border=0 cellpadding=5 cellspacing=0 bgcolor=#FAFAFA width=100%>\n";
		$pester_content .="	<col></col>\n";
		$pester_content .="	<col width=120></col>\n";
		$pester_content .="	<tr>\n";
		$pester_content .="		<td align=right bgcolor=#FAFAFA style=\"padding-right:15;font-size:14px;\"><FONT COLOR=\"#000000\"><B>상품 합계금액</B></FONT></td>\n";
		$pester_content .="		<td align=right bgcolor=#ffffff style=\"padding-right:15;font-size:14px;\"><FONT COLOR=\"#000000\"><B>".number_format($sumprice)."원</B></FONT></td>\n";
		$pester_content .="	</tr>\n";
		if($_data->ETCTYPE["VATUSE"]=="Y") {
			$sumpricevat = return_vat($sumprice);
			$pester_content .="	<tr>\n";
			$pester_content .="		<td align=right bgcolor=#FAFAFA style=\"padding-right:15;font-size:14px;\"><FONT COLOR=\"#000000\"><B>부가세(VAT) 합계금액</B></FONT></td>\n";
			$pester_content .="		<td align=right bgcolor=#ffffff style=\"padding-right:15;font-size:14px;\"><FONT COLOR=\"#000000\"><B>+ ".number_format($sumpricevat)."원</B></FONT></td>\n";
			$pester_content .="	</tr>\n";
		}
		if($deli_price>0) {
			$pester_content .="	<tr>\n";
			$pester_content .="		<td align=right bgcolor=#FAFAFA style=\"padding-right:15;font-size:14px;\"><FONT COLOR=\"#000000\"><B>배송비 합계금액</B></FONT></td>\n";
			$pester_content .="		<td align=right bgcolor=#ffffff style=\"padding-right:15;font-size:14px;\"><FONT COLOR=\"#000000\"><B>+ ".number_format($deli_price)."원</B></FONT></td>\n";
			$pester_content .="	</tr>\n";
		}
		if($salemoney>0) {
			$pester_content .="	<tr>\n";
			$pester_content .="		<td align=right bgcolor=#ffffff style=\"padding-right:15;font-size:14px;\"><img src=\"http://".$shopurl."images/common/group_orderimg.gif\" align=absmiddle>&nbsp;&nbsp;<b><font color=#FF3C00>".$group_name." 추가 할인</FONT></b></td>\n";
			$pester_content .="		<td align=right bgcolor=#ffffff style=\"padding-right:15;font-size:14px;\"><FONT COLOR=\"#FF3C00\"><B>- ".number_format($salemoney)."원</B></FONT></td>\n";
			$pester_content .="	</tr>\n";
		}
		$pester_content .="	<tr>\n";
		$pester_content .="		<td align=right bgcolor=#FAFAFA style=\"padding-right:15;font-size:14px;\"><FONT COLOR=\"#000000\"><B>총 결제금액</B></FONT></td>\n";
		$pester_content .="		<td align=right bgcolor=#ffffff style=\"padding-right:15;font-size:14px;\"><FONT COLOR=\"#EE1A02\"><B>".number_format($sumprice+$deli_price+$sumpricevat-$salemoney)."원</B></FONT></td>\n";
		$pester_content .="	</tr>\n";
		if($reserve>0 && $_data->reserve_maxuse>=0 ) {
			$pester_content .="<tr>\n";
			$pester_content .="	<td align=right bgcolor=#FAFAFA style=\"padding-right:15;font-size:14px;\"><FONT COLOR=#0099CC><B>적립금</B></FONT></td>\n";
			$pester_content .="	<td align=right bgcolor=#ffffff style=\"padding-right:15;font-size:14px;\"><FONT COLOR=#0099CC><B>".number_format($reserve)."원</B></FONT></td>\n";
			$pester_content .="</tr>\n";
		}

		if($salereserve>0) {
			$pester_content .="	<tr>\n";
			$pester_content .="		<td align=right bgcolor=#ffffff style=\"padding-right:15;font-size:14px;\"><img src=\"http://".$shopurl."images/common/group_orderimg.gif\" align=absmiddle>&nbsp;&nbsp;<b><font color=#0000FF>".$group_name." 추가 적립</FONT></b></td>\n";
			$pester_content .="		<td align=right bgcolor=#ffffff style=\"padding-right:15;font-size:14px;\"><FONT COLOR=\"#0000FF\"><B>".number_format($salereserve)."원</B></FONT></td>\n";
			$pester_content .="	</tr>\n";
		}
		$pester_content .="	</table>\n";
		$pester_content .="	</td>\n";
		$pester_content .="</tr>\n";
		$pester_content .="<tr><td colspan=6 height=1 bgcolor=\"#969696\"></td></tr>\n";

	} else {
		$pester_content .="<tr height=25><td colspan=6 align=center>쇼핑하신 상품이 없습니다.</td></tr>\n";
		$pester_content .="<tr><td colspan=6 height=1 bgcolor=\"#e4e4e4\"></td></tr>\n";
	}

	$pester_url = "http://".$shopurl."?pstr=".$pester_code;
	if(strlen($body)>0) {
		$curdate = date("Y년 m월 d일");
		$pattern = array ("(\[SHOP\])","(\[NAME\])","(\[RE_NAME\])","(\[MESSAGE\])","(\[PESTER_URL\])","(\[URL\])","(\[PESTER_CONTENT\])","(\[CURDATE\])");
		$replace = array ($shopname,$send_name,$re_name,$message,$pester_url,$shopurl,$pester_content,$curdate);
		$body	 = preg_replace($pattern,$replace,$body);
		if (strlen($shopname)>0) $mailshopname = "=?ks_c_5601-1987?B?".base64_encode($shopname)."?=";
		$header=getMailHeader($mailshopname,$send_email);
		if(ismail($re_email)) {
			sendmail($re_email, $subject, $body, $header);
		}
	}
}












//공동구매 진행
function SendGongguMail($shopname, $shopurl, $sendmsg, $info_email, $email, $name,$requsetdate,$gonggulink) {
	$sql = "SELECT * FROM ".$designnewpageTables." WHERE type='gongguReg' ";
	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		//개별디자인
		$pattern = array ("(\[SHOP\])","(\[NAME\])");
		$replace = array ($shopname,$name);
		$subject = preg_replace($pattern,$replace,$row->subject);
		$body	 = $row->body;
	} else {
		//템플릿
		$subject = $shopname." 공동구매 진행 알림 메일입니다.";
		$buffer="";
		if(file_exists(DirPath.TempletDir."mail/gonggumail.php")) {
			$fp=fopen(DirPath.TempletDir."mail/gonggumail.php","r");
			if($fp) {
				while (!feof($fp)) {$buffer.= fgets($fp, 1024);}
			}
			fclose($fp);
			$body=$buffer;
		}
	}
	mysql_free_result($result);
	if(strlen($body)>0) {
		$curdate = date("Y년 m월 d일");
		$pattern = array ("(\[SHOP\])","(\[NAME\])","(\[MESSAGE\])","(\[URL\])","(\[CURDATE\])","(\[REQUSETDATE\])","(\[GONGGULINK\])");
		$replace = array ($shopname,$name,$sendmsg,$shopurl,$curdate,$requsetdate,$gonggulink);
		$body	 = preg_replace($pattern,$replace,$body);
		if (strlen($shopname)>0) $mailshopname = "=?ks_c_5601-1987?B?".base64_encode($shopname)."?=";
		$header=getMailHeader($mailshopname,$info_email);
		if(ismail($email)) {
			sendmail($email, $subject, $body, $header);
		}
	}
}

function SendGongguMail2($shopname, $shopurl, $arSendprdt, $info_email, $email) {

	$sql = "SELECT * FROM tblproduct A, tblproduct_social B ";
	$sql .="WHERE A.productcode=B.pcode ";
	$sql .="AND productcode in (".implode(",", $arSendprdt).") ";
	$result=mysql_query($sql,get_db_conn());
	$sendmsg = "        <table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">";
	$i=0;
	while($row=mysql_fetch_array($result)) {
		$productname= $row["productname"];
		$maximage= $row["maximage"];
		$productcode= $row["productcode"];
		if(strlen($maximage)>0 && file_exists(DirPath.DataDir."shopimages/product/".$maximage)) {
			$maximage = "<img src='http://".$shopurl.DataDir."shopimages/product/".$maximage."' width=488 height=\"294\" style=\"border-bottom:1px solid #e5e5e5; border-left:1px solid #e5e5e5; border-top:1px solid #e5e5e5;\">";
		} else {
			$maximage = "<img src=\"http://".$shopurl."images/no_img.gif\" width=488 height=\"294\"  style=\"border-bottom:1px solid #e5e5e5; border-left:1px solid #e5e5e5; border-top:1px solid #e5e5e5;\" >";
		}
		$discountRate = ($row["consumerprice"] >0 )? 100-intval($row["sellprice"]/$row["consumerprice"]*100)."%":"";

		if($i>0){
			$sendmsg .= "
		  <tr>
			<td height=\"30\">&nbsp;</td>
		  </tr>
		  <tr>
		   <td height=\"1\" align=\"center\"><img src=\"http://".$shopurl."images/mail/solution/bar_02.gif\" /></td>
		  </tr>
		  <tr>
		   <td height=\"30\">&nbsp;</td>
		  </tr>";
		}

		$sendmsg .= "
		  <tr>
			<td>".gongguPrdt($maximage,$row["sell_enddate"],$row["consumerprice"],$row["sellprice"],$discountRate,$productcode, $shopurl)."</td>
		  </tr>";
		$i++;
	}
	$sendmsg .= "        </table>";

	//인기상품
	$sql = "SELECT special_list FROM tblspecialmain WHERE special='2' ";
	$result=mysql_query($sql,get_db_conn());
	$sp_prcode="";
	if($row=mysql_fetch_object($result)) {
		$sp_prcode=ereg_replace(',','\',\'',$row->special_list);
	}
	mysql_free_result($result);

	if(strlen($sp_prcode)>0) {
		$sql = "SELECT a.productcode, a.productname, a.sellprice, a.quantity, ";
		$sql.= "a.tinyimage, a.date, a.etctype, a.consumerprice, a.reserve, a.reservetype, a.tag, a.selfcode FROM tblproduct AS a ";
		$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
		$sql.= "WHERE a.productcode IN ('".$sp_prcode."') AND a.display='Y' ";
		$sql.= "AND a.group_check='N' ";
		$sql.= "ORDER BY FIELD(a.productcode,'".$sp_prcode."') ";
		$sql.= "LIMIT 8 ";
		$result=mysql_query($sql,get_db_conn());
		$i=0;
		$strBestprdt.="<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" align=\"center\">\n";
		$strBestprdt.="<tr>\n";
		while($row=mysql_fetch_object($result)) {
			if ($i!=0 && $i%4==0) {
				$strBestprdt.="</tr><tr><td colspan=\"8\" height=\"45\"></td></tr><tr>\n";
			}
			$strBestprdt.="<td width=\"10\">&nbsp;</td>\n";
			$strBestprdt.="<td width=\"20%\" align=\"center\" valign=\"top\">";
			if (strlen($row->tinyimage)>0 && file_exists(DirPath.DataDir."shopimages/product/".$row->tinyimage)==true) {
				$strBestprdt.="<A HREF=\"http://".$shopurl."?productcode=".$row->productcode."\"  target=\"_blank\"><img src=\"http://".$shopurl.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" ";
				$width = getimagesize(DirPath.DataDir."shopimages/product/".$row->tinyimage);
				if($_data->ETCTYPE["IMGSERO"]=="Y") {
					if ($width[1]>$width[0] && $width[1]>127) $strBestprdt.="height=\"127\" ";
					else if (($width[1]>=$width[0] && $width[0]>=130) || $width[0]>=130) $strBestprdt.="width=\"130\" ";
				} else {
					if ($width[0]>=$width[1] && $width[0]>152) $strBestprdt.="width=\"152\" ";
					else if ($width[1]>=127) $strBestprdt.="height=\"127\" ";
				}
			} else {
				$strBestprdt.="<img src=\"".$Dir."images/no_img.gif\" align=\"center\" width=\"152\" height=\"127\" ";
			}
			$strBestprdt.="	style=\"border:1px solid #e6e6e6;\"></A><div style='height:20px;text-align:center;padding-top:5px;'><A HREF=\"http://".$shopurl."?productcode=".$row->productcode."\" target=\"_blank\">".$row->productname."</A></div></td>\n";
			$i++;

			if ($i==8) break;
		}
		if($i>0 && $i%4 != 0) {
			for($k=0; $k<(4 - $i%4); $k++) {
				$strBestprdt.="<td></td><td></td>\n";
			}
		}
		$strBestprdt.="</tr>\n";
		$strBestprdt.="</table>\n";
		mysql_free_result($result);
	}



	$sql = "SELECT * FROM ".$designnewpageTables." WHERE type='gongguSend' ";
	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		//개별디자인
		$pattern = array ("(\[SHOP\])","(\[NAME\])");
		$replace = array ($shopname,$name);
		$subject = preg_replace($pattern,$replace,$row->subject);
		$body	 = $row->body;
	} else {
		//템플릿
		$subject = $shopname." 공동구매 진행합니다.";
		$buffer="";
		if(file_exists(DirPath.TempletDir."mail/gonggumail2.php")) {
			$fp=fopen(DirPath.TempletDir."mail/gonggumail2.php","r");
			if($fp) {
				while (!feof($fp)) {$buffer.= fgets($fp, 1024);}
			}
			fclose($fp);
			$body=$buffer;
		}
	}
	mysql_free_result($result);
	if(strlen($body)>0) {
		$curdate = date("Y년 m월 d일");
		$pattern = array ("(\[SHOP\])","(\[MESSAGE\])","(\[URL\])","(\[CURDATE\])","(\[BESTPRODUCT\])");
		$replace = array ($shopname,$sendmsg,$shopurl,$curdate,$strBestprdt);
		$body	 = preg_replace($pattern,$replace,$body);
		if (strlen($shopname)>0) $mailshopname = "=?ks_c_5601-1987?B?".base64_encode($shopname)."?=";
		$header=getMailHeader($mailshopname,$info_email);
		if(ismail($email)) {
			sendmail($email, $subject, $body, $header);
		}
	}
	return $body;
}

//공구메일 발송관련
function gongguPrdt($maximage,$sell_enddate,$consumerprice,$sellprice,$discountRate,$productcode, $shopurl){
	$contents ="
			<table width=\"690\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
			  <tr>
				<td width=\"488\">".$maximage."</td>
				<td bgcolor=\"#ff901b\" valign=\"top\" style=\"text-align:center\">
				  <table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" style=\"text-align:center\">
					<tr>
					  <td height=\"30\">&nbsp;</td>
					</tr>
					<tr>
					  <td style=\"font-family:Verdana, Geneva, sans-serif; font-size:14pt; font-weight:bold;color:#666666\">".date("Y.m.d D",$sell_enddate)."</td>
					</tr>
					<tr>
					  <td style=\"color:#fff; font-family:돋음; font-size:14px; font-weight:bold; padding-top:5px;\">(완료예정)</td>
					</tr>
					<tr>
					  <td height=\"25\">&nbsp;</td>
					</tr>
					<tr>
					  <td style=\"font-family:Verdana, Geneva, sans-serif; font-size:14pt; font-weight:bold; text-decoration:line-through; color:#fff;\">￦ ".number_format($consumerprice)."</td>
					</tr>
					<tr>
					  <td height=\"60\" style=\"background:url(http://".$shopurl."images/mail/solution/bg_gonggu1.gif) center center no-repeat; font-family:돋음; font-size:18px; font-weight:bold; color:#fff;\">".$discountRate."</td>
					</tr>
					<tr>
					  <td style=\"font-family:Verdana, Geneva, sans-serif; font-size:14pt; font-weight:bold;color:#000;\">￦ ".number_format($sellprice)."</td>
					</tr>
					<tr>
					  <td style=\"text-align:left\">
					  <div style=\"position:relative;\">
						<div style=\"position:absolute; left: -6px; top: 15px;\"><a href=\"http://".$shopurl."?productcode=".$productcode."\" target=\"_blank\"><img src=\"http://".$shopurl."images/mail/solution/btn_detail.png\" border='0'/></a></div>
					  </div>
					  </td>
					</tr>
				  </table>
				</td>
			  </tr>
			  </table>";
	return $contents;
}

//상품 홍보메일
function SendProductMail($shopname, $shopurl, $mail_type, $message, $info_email, $email, $name, $url_link, $id, $pcode) {
	$sql = "SELECT * FROM ".$designnewpageTables." WHERE type='productmail' ";
	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		$body	 = $row->body;
	} else {
		//템플릿
		$subject = $name."[".$id."]님이 추천합니다.";
		$buffer="";
		if(file_exists(DirPath.TempletDir."mail/productmail".$mail_type.".php")) {
			$fp=fopen(DirPath.TempletDir."mail/productmail".$mail_type.".php","r");
			if($fp) {
				while (!feof($fp)) {$buffer.= fgets($fp, 1024);}
			}
			fclose($fp);
			$body=$buffer;
		}
	}
	mysql_free_result($result);

	$content ="                    <table width=\"656\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" class=\"table_1\">
                    <col width=\"\" />
                      <tr>
                        <th height=\"30\" colspan=\"2\" style=\"border-left:none;\">상 품 명</th>
                        <th style=\"border-left:none;\"></th>
   						<th width=\"130\">판 매 가</th>
						<th width=\"100\">기 타</th>
                      </tr>";
	$sql = "SELECT * FROM tblproduct A  LEFT OUTER JOIN tblproduct_social B ON A.productcode=B.pcode WHERE productcode = '".$pcode."' ";
	$result=mysql_query($sql,get_db_conn());
	if($row=@mysql_fetch_array($result)){
		$productname= $row["productname"];
		$tinyimage= $row["tinyimage"];
		$productcode= $row["productcode"];
		$discountRate = ($row["consumerprice"] >0 )? 100-intval($row["sellprice"]/$row["consumerprice"]*100)."%":"";
		if(strlen($tinyimage)>0 && file_exists(DirPath.DataDir."shopimages/product/".$tinyimage)) {
			$tinyimage = "<img src='http://".$shopurl.DataDir."shopimages/product/".$tinyimage."' width=54 height=\"54\">";
		} else {
			$tinyimage = "<img src=\"http://".$shopurl."images/no_img.gif\" width=54 height=\"54\">";
		}
		$content .="
						  <tr>
							<td width=\"55\" height=\"77\" align=\"left\" style=\"border-left:none; padding-left:10px; padding-top:15px; vertical-align:text-top;\"><a href=\"".$url_link."\">".$tinyimage."</a></td>
							<td align=\"left\" style=\"border-left:none; padding-left:10px;\"><a href=\"".$url_link."\"><strong style=\"color:#000;\">".$productname."</strong></a></td>
							<td style=\"border-left:none;\"></td>
							<td align=\"center\">".number_format($row["sellprice"])."원";
		if($row["consumerprice"]>0) $content .="<br /><span style=\"color:#8f8f8f;\">(상품가격 ".number_format($row["consumerprice"])." 원)</span>";
		$content .="</td>\n";
		if($row["social_chk"]=="Y"){
			$content .="                        <td align=\"center\">".date("Y.m.d D",$row["sell_enddate"])."<br>(완료예정)</td>\n";
		}else{
			$content .="                        <td align=\"center\">-</td>";
		}
		$content .="                      <tr>\n";
	}
	$content .="                    </table>";
	$curDate = date("Y년 m월 d일",time());
	if(strlen($body)>0) {
		$pattern = array ("(\[SHOP\])","(\[NAME\])","(\[URL_LINK\])","(\[MESSAGE\])","(\[CONTENT\])","(\[URL\])","(\[DATE\])","(\[RECEIVE_MAIL\])","(\[CURDATE\])");
		$replace = array ($shopname,$name."[".$id."]",$url_link,$message,$content,$shopurl,$curDate,$email,$curDate);
		$body	 = preg_replace($pattern,$replace,$body);
		if (strlen($shopname)>0) $mailshopname = "=?ks_c_5601-1987?B?".base64_encode($shopname)."?=";
		$header=getMailHeader($mailshopname,$info_email);
		if(ismail($email)) {
			sendmail($email, $subject, $body, $header);
		}
	}
}

//공구실패
function SendGongguFailMail($shopname, $shopurl, $mail_type, $info_email, $name, $email, $ordercode) {
	$sql = "SELECT * FROM ".$designnewpageTables." WHERE type='gonggufailmail' ";
	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		$body	 = $row->body;
	} else {
		//템플릿
		$subject = $name."님 공동구매가 성사되지않았습니다.";
		$buffer="";
		if(file_exists(DirPath.TempletDir."mail/gonggufailmail.php")) {//".$mail_type."
			$fp=fopen(DirPath.TempletDir."mail/gonggufailmail.php","r");
			if($fp) {
				while (!feof($fp)) {$buffer.= fgets($fp, 1024);}
			}
			fclose($fp);
			$body=$buffer;
		}
	}
	mysql_free_result($result);

	$sql = "SELECT * FROM tblorderinfo WHERE ordercode='".$ordercode."' ";
	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		$_ord=$row;
		$email=$_ord->sender_email;
		if(substr($row->id,0,1)=='X') $guest_type="guest";
	} else {
		$ordercode="";
	}
	mysql_free_result($result);

	$content ="                    <table width=\"656\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" style='border-top:2px solid #969696'>
                    <col width=\"\" />
                      <tr  align=center style='background:#f9f9f9;color:#474747;font-weigth:bold;'>
                        <th height='30' colspan='2'>상 품 명</th>
                        <th></th>
   						<th width=\"70\">수량</th>
						<th width=\"130\">적립금</th>
						<th width=\"100\">주문금액</th>
                      </tr>
					  <tr>
					    <td height=1 colspan=6 bgcolor=#e4e4e4></td>
					  </tr>\n";
	$sql ="SELECT A.*, price*quantity as sumprice, (select tinyimage from tblproduct C where C.productcode=A.productcode) tinyimage ";
	$sql.="FROM tblorderproduct A ";
	$sql.="WHERE A.ordercode='".$ordercode."' ";
	$sql.="ORDER BY productcode ASC ";

	$result=mysql_query($sql,get_db_conn());
	while($row=@mysql_fetch_array($result)){
		$productname= $row["productname"];
		$tinyimage= $row["tinyimage"];
		$productcode= $row["productcode"];
		if(strlen($tinyimage)>0 && file_exists(DirPath.DataDir."shopimages/product/".$tinyimage)) {
			$tinyimage = "<img src='http://".$shopurl.DataDir."shopimages/product/".$tinyimage."' width=54 height=\"54\" style=\"border:1px solid #d0d0d0;\">";
		} else {
			$tinyimage = "<img src=\"http://".$shopurl."images/no_img.gif\" width=54 height=\"54\" style=\"border:1px solid #d0d0d0;\">";
		}
		$content .="
						  <tr>
							<td width=\"55\" align=\"left\" style=\"border-left:none; padding:15px 0 15px 10px; vertical-align:text-top;\"><a href=\"http://".$shopurl."?prdt=".$productcode."\">".$tinyimage."</a></td>
							<td align=\"left\" style=\"border-left:none; padding-left:10px;\"><a href=\"http://".$shopurl."?prdt=".$productcode."\"><strong style=\"color:#000;\">".$productname."</strong></a></td>
							<td style=\"border-left:none;\"></td>
							<td align=\"center\">".$row["quantity"]."</td>
							<td align=\"center\">".number_format($row["reserve"])."원</td>
							<td align=\"center\">".number_format($row["price"])."원</td>
						</tr>
						<tr>
							<td height=1 colspan=6 bgcolor=#e4e4e4></td>
						</tr>\n";
	}
	$content .="
						  <tr>
							<td height='55' colspan='4'></td>
							<td align=\"center\"><font color='#000'><b>결제총액</b></font></td>
							<td align=\"center\"><font color='#ff6c00'><b>".number_format($_ord->price)."원</b></font></td>
						  <tr>
						</table>";

	$curDate = date("Y년 m월 d일",time());
	$orderdate = substr($_ord->ordercode,0,4)."년 ".substr($_ord->ordercode,4,2)."월 ".substr($_ord->ordercode,6,2)."일 ";
	if(strlen($body)>0) {
		$pattern = array ("(\[SHOP\])","(\[NAME\])","(\[CONTENT\])","(\[URL\])","(\[CURDATE\])","(\[ORDERDATE\])");
		$replace = array ($shopname,$name,$content,$shopurl,$curDate,$orderdate);
		$body	 = preg_replace($pattern,$replace,$body);
		if (strlen($shopname)>0) $mailshopname = "=?ks_c_5601-1987?B?".base64_encode($shopname)."?=";
		$header=getMailHeader($mailshopname,$info_email);
		if(ismail($email)) {
			sendmail($email, $subject, $body, $header);
		}
	}
}
?>