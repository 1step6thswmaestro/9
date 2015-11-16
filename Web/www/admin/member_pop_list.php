<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
INCLUDE ("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "me-1";
$MenuCode = "member";
if (!$_usersession->isAllowedTask($PageCode)) {
INCLUDE ("AccessDeny.inc.php");
exit;
}
#########################################################

$setup[page_num] = 10;
$setup[list_num] = 5;

$type=$_POST["type"];
$id=$_POST["id"];
$ids=$_POST["ids"];
$email=$_POST["email"];
$mem_name =$_POST["mem_name"];

if ($type=="confirm_ok") {
	$sql = "UPDATE tblmember SET confirm_yn='Y' WHERE id='".$id."' ";
	mysql_query($sql,get_db_conn());
		if (strlen($email)>0) {
			SendAuthMail($_shopdata->shopname, $shopurl, $_shopdata->design_mail, $_shopdata->info_email, $email, $id, $mem_name);
		}

	$sql2= "SELECT * FROM tblsmsinfo WHERE mem_auth = 'Y' ";
	$result2 = mysql_query($sql2,get_db_conn());
	if($row2=mysql_fetch_object($result2)){
		$sql3 = "SELECT mobile, name FROM tblmember WHERE id='".$id."'";
		$result3 = mysql_query($sql3,get_db_conn());
		$row3 = mysql_fetch_object($result3);
		mysql_free_result($result3);
		$sms_id=$row2->id;
		$sms_authkey=$row2->authkey;

		$row3->mobile=ereg_replace(",","",$row3->mobile);
		$row3->mobile=ereg_replace("-","",$row3->mobile);
		$toname= $row3->name;
		$totel= $row3->mobile;

		$msg=$row2->msg_mem_auth;
		$patten=array("(\[NAME\])");
		$replace=array($toname);
		$msg=preg_replace($patten,$replace,$msg);
		$msg=AddSlashes($msg);
		$fromtel=$row2->return_tel;
		$etcmsg="회원인증 안내메세지";
		$date=0;
		$res=SendSMS($sms_id, $sms_authkey, $totel, "", $fromtel, $date, $msg, $etcmsg);
	}

} else if ($type=="confirm_cancel") {
	$sql = "UPDATE tblmember SET confirm_yn = 'N' WHERE id = '".$id."' ";
	mysql_query($sql,get_db_conn());
	//echo "<script>history.go(-1);</script>"; exit;
} else if ($type=="member_out" && strlen($id)>0) {	//선택 회원삭제
	$idval=substr($id,0,-3);
	$arr_id=explode("|=|",$idval);
	for($i=0;$i<count($arr_id);$i++) {
		$outid=$arr_id[$i];
		$sql = "SELECT COUNT(*) as cnt FROM tblorderinfo WHERE id='".$outid."'";
		$result= mysql_query($sql,get_db_conn());
		$row = mysql_fetch_object($result);
		mysql_free_result($result);
			if ($row->cnt==0) {
				$sql = "DELETE FROM tblmember WHERE id = '".$outid."'";
			} else {
				$sql = "UPDATE tblmember SET ";
				$sql.= "passwd			= '', ";
				$sql.= "resno			= '', ";
				$sql.= "email			= '', ";
				$sql.= "news_yn			= 'N', ";
				$sql.= "age				= '', ";
				$sql.= "gender			= '', ";
				$sql.= "job				= '', ";
				$sql.= "birth			= '', ";
				$sql.= "home_post		= '', ";
				$sql.= "home_addr		= '', ";
				$sql.= "home_tel		= '', ";
				$sql.= "mobile			= '', ";
				$sql.= "office_post		= '', ";
				$sql.= "office_addr		= '', ";
				$sql.= "office_tel		= '', ";
				$sql.= "memo			= '', ";
				$sql.= "reserve			= 0, ";
				$sql.= "joinip			= '', ";
				$sql.= "ip				= '', ";
				$sql.= "authidkey		= '', ";
				$sql.= "group_code		= '', ";
				$sql.= "member_out		= 'Y', ";
				$sql.= "etcdata			= '' ";
				$sql.= "WHERE id = '".$outid."'";
			}
		mysql_query($sql,get_db_conn());
		$sql = "DELETE FROM tblreserve WHERE id='".$outid."'";
		mysql_query($sql,get_db_conn());
		$sql = "DELETE FROM tblcouponissue WHERE id='".$outid."'";
		mysql_query($sql,get_db_conn());
		$sql = "DELETE FROM tblmemo WHERE id='".$outid."'";
		mysql_query($sql,get_db_conn());
		$sql = "DELETE FROM tblrecommendmanager WHERE rec_id='".$outid."'";
		mysql_query($sql,get_db_conn());
		$sql = "DELETE FROM tblrecomendlist WHERE id='".$outid."'";
		mysql_query($sql,get_db_conn());
		$sql = "DELETE FROM tblpersonal WHERE id='".$outid."'";
		mysql_query($sql,get_db_conn());
	}
	//로그 insert
	$log_content = "## 회원삭제 : ID:".str_replace("|=|",",",$idval)." ##";
	ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);

	$onload="<script>alert('선택하신 회원 ".count($arr_id)."명을 탈퇴처리 하였습니다.\\n\\n구매내역이 있는 경우에는 회원 기본정보만 삭제됩니다.');</script>";
}

$regdate = $_shopdata->regdate;
$CurrentTime = time();
$period[0] = substr($regdate,0,4)."-".substr($regdate,4,2)."-".substr($regdate,6,2);
$period[1] = date("Y-m-d",$CurrentTime);
$period[2] = date("Y-m-d",$CurrentTime-(60*60*24*7));
$period[3] = date("Y-m",$CurrentTime)."-01";
$period[4] = date("Y",$CurrentTime)."-01-01";

/*
$member_search=(int)$_POST["member_search"];
$scheck=(int)$_POST["scheck"];
$group_code=$_POST["group_code"];
$search_start=$_POST["search_start"];
$search_end=$_POST["search_end"];
$vperiod=(int)$_POST["vperiod"];
$search=$_POST["search"];
$search_start=$search_start?$search_start:$period[0];
$search_end=$search_end?$search_end:date("Y-m-d",$CurrentTime);

${"check_sort".$member_search} = "checked";
${"check_scheck".$scheck} = "checked";
${"check_vperiod".$vperiod} = "checked";

if ($scheck == "6") {
	$sort_disabled = "disabled";
}
*/

$block=$_REQUEST["block"];
$gotopage=$_REQUEST["gotopage"];

$display_group_code="none";
/*
if($scheck==7) $display_group_code="";
$display_todaylogin="";
if($scheck==8) $display_todaylogin="none";

$ArrSort = array("date","name","id","age","reserve");
$ArrScheck = array("id","name","email","resno","home_tel","mobile","rec_id","group_code","logindate");
*/

if ($block != "") {
	$nowblock = $block;
	$curpage  = $block * $setup[page_num] + $gotopage;
} else {
	$nowblock = 0;
}

if (($gotopage == "") || ($gotopage == 0)) {
	$gotopage = 1;
}
?>
<? INCLUDE "header.php"; ?>
<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="calendar.js.php"></script>
<SCRIPT LANGUAGE="JavaScript">
	<!--
	function member_baro(type,id,email,mem_name) {
		var msg = "";
		if (type=="ok") msg="["+id+"] 님을 인증 하시겠습니까?";
		else if (type=="cancel") msg="["+id+"] 님의 인증을 취소하시겠습니까?";
		if (confirm(msg)) {
			document.form1.type.value="confirm_"+type;
			document.form1.id.value=id;
			document.form1.email.value=email;
			document.form1.mem_name.value=mem_name;
			document.form1.submit();
		}
	}

	function check() {
		if (document.form1.search.value.length==0) {
			tmsg="";
			if(document.form1.scheck[8].checked==false) tmsg="검색기간 내에서 ";
			if(document.form1.scheck[7].checked==true && document.form1.group_code.value.length==0) {
				alert("조회하실 회원 등급을 선택하세요.");
				document.form1.group_code.focus();
				return;
			}
			if (confirm(tmsg+"전체 조회하시겠습니까?")) {
				document.form1.submit();
			}
		}else {
			document.form1.submit();
		}
	}

	function searchcheck() {
		key=event.keyCode;
		if (key==13) {
			check();
		}
	}
/*
	function OnChangePeriod(val) {
		var pForm = document.form1;
		var period = new Array(7);
		period[0] = "<?=$period[0]?>";
		period[1] = "<?=$period[1]?>";
		period[2] = "<?=$period[2]?>";
		period[3] = "<?=$period[3]?>";
		period[4] = "<?=$period[4]?>";

		pForm.search_start.value = period[val];
		pForm.search_end.value = period[1];
	}
	function OnChangeSearchType(val) {
		if (val == 6) {
			for(var i=0;i<document.form1.member_search.length;i++) {
				document.form1.member_search[i].disabled = true;
			}
		}else {
			for(var i=0;i<document.form1.member_search.length;i++) {
			document.form1.member_search[i].disabled = false;
		}
	}

	if (val == 7) {
		document.all.div_todaylogin.style.display = "";
		document.all.div_todaylogin2.style.display = "";
		document.all.div_group_code.style.display = "";
		document.all.div_group_code2.style.display = "";
	}else {
		document.all.div_group_code.style.display = "none";
		document.all.div_group_code2.style.display = "none";

		if (val == 8) {
			document.all.div_todaylogin.style.display = "none";
			document.all.div_todaylogin2.style.display = "none";
		}	else {
			document.all.div_todaylogin.style.display = "";
			document.all.div_todaylogin2.style.display = "";
		}
	}
}
*/
	function CheckAll(){
		chkval=document.form1.allcheck.checked;
		cnt=document.form1.tot.value;
		for(i=1;i<=cnt;i++){
			document.form1.ids_chk[i].checked=chkval;
		}
	}

	function check_del() {
		document.form1.id.value="";
		for(i=1;i<document.form1.ids_chk.length;i++) {
			if(document.form1.ids_chk[i].checked==true) {
				document.form1.id.value+=document.form1.ids_chk[i].value+"|=|";
			}
		}
		if(document.form1.id.value.length==0) {
			alert("선택하신 회원아이디가 없습니다.");
			return;
		}
		if(confirm("선택하신 회원아이디를 탈퇴처리 하시겠습니까?")) {
			document.form1.type.value="member_out";
			document.form1.submit();
		}
	}

	function MemberInfo(id) {
		window.open("about:blank","infopop","width=567,height=600,scrollbars=yes");
		document.form3.target="infopop";
		document.form3.id.value=id;
		document.form3.action="member_infopop.php";
		document.form3.submit();
	}

	function LostPass(id) {
		window.open("about:blank","lostpasspop","width=350,height=200,scrollbars=no");
		document.form3.target="lostpasspop";
		document.form3.id.value=id;
		document.form3.action="member_lostpasspop.php";
		document.form3.submit();
	}

	function ReserveInOut(id){
		window.open("about:blank","reserve_set","width=245,height=140,scrollbars=no");
		document.reserveform.target="reserve_set";
		document.reserveform.id.value=id;
		document.reserveform.type.value="reserve";
		document.reserveform.submit();
	}


	function ReserveInfo(id) {
		window.open("about:blank","reserve_info","height=400,width=400,scrollbars=yes");
		document.form2.id.value=id;
		document.form2.submit();
	}

	function OrderInfo(id) {
		window.open("about:blank","orderinfo","width=414,height=320,scrollbars=yes");
		document.form3.target="orderinfo";
		document.form3.id.value=id;
		document.form3.action="orderinfopop.php";
		document.form3.submit();
	}

	function CouponInfo(id) {
		window.open("about:blank","couponinfo","width=600,height=400,scrollbars=yes");
		document.form3.target="couponinfo";
		document.form3.id.value=id;
		document.form3.action="coupon_listpop.php";
		document.form3.submit();
	}
/*
	function MemberMail(mail,news_yn){
		if(news_yn!="Y" && news_yn!="M" && !confirm("해당 회원은 메일수신을 거부하였습니다.\n\n메일을 발송하시려면 확인 버튼을 클릭하시기 바랍니다.")) {
			return;
		}
		document.mailform.rmail.value=mail;
		document.mailform.submit();
	}
*/
	function MemberSMS(news_yn,tel1,tel2) {
		if(news_yn!="Y" && news_yn!="S") {
			if(!confirm("SMS수신거부 회원입니다.\n\nSMS를 발송하시려면 \"확인\"을 눌러주세요.")) {
				return;
			}
		}
		number=tel1+"|"+tel2;
		document.smsform.number.value=number;
		window.open("about:blank","sendsmspop","width=220,height=350,scrollbars=no");
		document.smsform.submit();
	}

	function MemberMemo2(id) {
		window.open("about:blank","memopop","width=350,height=350,scrollbars=no");
		document.form3.target="memopop";
		document.form3.id.value=id;
		document.form3.action="member_memopop.php";
		document.form3.submit();
	}
	function MemberEtcView(id) {
		window.open("about:blank","etcpop","width=350,height=350,scrollbars=no");
		document.form3.target="etcpop";
		document.form3.id.value=id;
		document.form3.action="member_etcpop.php";
		document.form3.submit();
	}
/*
	function excel_download() {
		if(confirm("검색된 모든 회원정보를 다운로드 하시겠습니까?")) {
			document.excelform.submit();
		}
	}
*/

	function GoPage(block,gotopage) {
	document.idxform.block.value = block;
	document.idxform.gotopage.value = gotopage;
	document.idxform.submit();
	}

	//-->
	</SCRIPT>
<table cellpadding="0" cellspacing="0" width="770" style="table-layout:fixed">
	<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method="post">
	<input type=hidden name="type">
	<input type=hidden name="id">
	<input type=hidden name="email">
	<input type=hidden name="mem_name">
	<tr>
		<td>
			<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/member_list_stitle2.gif" WIDTH="192" HEIGHT=31 ALT=""></TD>
					<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif"></TD>
					<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></TD>
				</TR>
			</TABLE>
		</td>
	</tr>
	<tr><td height=3></td></tr>
	<?
	$member_search				=	(!empty($_POST['member_search']))				?	$_POST['member_search'] : "I";	
	$search							=	(!empty($_POST['search']))								?	$_POST['search'] : "";	

	if($member_search != "" && $search != "") {
		switch($member_search) {	
			case	"I"	:	$searchsql	.=	" and id like '%".$search."%'";							break;		//	아이디
			case	"N"	:	$searchsql	.=	" and name like '%".$search."%'";					break;		//	이름
		}
	}
	$row = sql_fetch_obj("tblmember", " WHERE 1=1 and member_out='N' ".$searchsql, "COUNT(*) as cnt");
	$t_count = $row->cnt;

	$sql = "SELECT * FROM tblmember WHERE 1=1 and member_out='N'  ".$searchsql."";
	$sql.= "ORDER BY date DESC  LIMIT " . ($setup[list_num] * ($gotopage - 1)) . ", " . $setup[list_num];
	
	
	$pagecount = (($t_count - 1) / $setup[list_num]) + 1;
	?>
	<tr>
		<td style="padding-bottom:3pt;">
			<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/distribute_01.gif"></TD>
					<TD COLSPAN=2 background="images/distribute_02.gif"></TD>
					<TD><IMG SRC="images/distribute_03.gif"></TD>
				</TR>
				<TR>
					<TD background="images/distribute_04.gif"></TD>
					<TD class="notice_blue"><IMG SRC="images/distribute_img.gif" ></TD>
					<TD width="100%" class="notice_blue">
						<table border=0 cellpadding=0 cellspacing=0 width=100%>
							<tr>
								<td class="notice_blue"><p>전체 <span class="font_orange"><B><?=$t_count?></B></span>건 조회, 현재 <span class="font_orange"><B><?=$gotopage?>/<?=ceil($t_count/$setup[list_num])?></B></span> 페이지</p></td>
								<td align=right><!-- <a href="javascript:excel_download()"><img src="images/btn_excel1.gif" border="0"></a> --></td>
							</tr>
						</table>
					</TD>
					<TD background="images/distribute_07.gif"></TD>
				</TR>
				<TR>
					<TD><IMG SRC="images/distribute_08.gif"></TD>
					<TD COLSPAN=2 background="images/distribute_09.gif"></TD>
					<TD><IMG SRC="images/distribute_10.gif"></TD>
				</TR>
			</TABLE>
		</td>
	</tr>
	<tr><td height="3"></td></tr>
	<tr>
		<td>
			<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<?$_shopdata->member_baro=="Y"?$member_list_colspan="15":$member_list_colspan="14";?>
				<TR>
					<TD background="images/table_top_line.gif" colspan="<?=$member_list_colspan?>" height=1></TD>
				</TR>
				<TR align=center>
					<TD class="table_cell"><input type=checkbox name=allcheck onclick="CheckAll()" style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;"></TD>
					<TD class="table_cell1">번호</TD>
					<TD class="table_cell1">아이디</TD>
					<TD class="table_cell1">성명</TD>
					<TD class="table_cell1">비번</TD>
					<TD class="table_cell1">가입일</TD>
					<TD class="table_cell1">메일</TD>
					<TD class="table_cell1">메모</TD>
					<TD class="table_cell1">주소</TD>
					<TD class="table_cell1">전화</TD>
					<TD class="table_cell1">적립금</TD>
					<TD class="table_cell1">적립금관리</TD>
					<TD class="table_cell1">내역</TD>
					<TD class="table_cell1">추천인</TD>
					<?if ($_shopdata->member_baro=="Y") {?>
					<TD class="table_cell1">인증</TD>
					<? }?>
				</TR>
				<input type=hidden name=ids_chk>
				<TR>
					<TD colspan="<?=$member_list_colspan?>" background="images/table_con_line.gif"></TD>
				</TR>
				<?
				$cnt=0;
				$result = mysql_query($sql,get_db_conn());
				while($row=mysql_fetch_object($result)) {
					$number = ($t_count-($setup[list_num] * ($gotopage-1))-$cnt);
					$reg_date=substr($row->date,0,4)."/".substr($row->date,4,2)."/".substr($row->date,6,2)." (".substr($row->date,8,2).":".substr($row->date,10,2).")";
					$haddress="우편번호 : ".substr($row->home_post,0,3)."-".substr($row->home_post,3,3)." ".str_replace("="," ",$row->home_addr);
					$oaddress="우편번호 : ".substr($row->office_post,0,3)."-".substr($row->office_post,3,3)." ".str_replace("="," ",$row->office_addr);

					echo "<tr>\n";
					echo "	<TD class=\"td_con2\"><p align=\"center\"><input type=checkbox name=ids_chk value=\"".$row->id."\" style=\"BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;\"></td>\n";
					echo "	<TD class=\"td_con1\" align=center>";
					if($row->member_out!="Y") {
						echo "	<b><span class=\"font_orange\"><A HREF=\"javascript:MemberEtcView('".$row->id."')\">".$number."</A></span></b>";
					} else echo $number;
					echo "	</td>\n";
					echo "	<TD align=center class=\"td_con1\">";
					if($row->member_out!="Y") {
						echo "<b><span class=\"font_orange\"><A HREF=\"javascript:MemberInfo('".$row->id."')\">".$row->id."</A></span></b>";
					} else {
						echo $row->id;
					}
					echo "	</td>\n";
					echo "	<TD align=center class=\"td_con1\">".$row->name."</td>\n";
					echo "	<TD align=center class=\"td_con1\">";
					if($row->member_out!="Y") {
						echo "	<a href=\"javascript:LostPass('".$row->id."');\"><img src=\"images/btn_edit4.gif\" width=\"35\" height=\"29\" border=\"0\"></a>";
					} else {
						//echo "	<button class=button2 disabled>변경</button>";
					}
					echo "	</td>\n";
					echo "	<TD align=center class=\"td_con1\" title='".$reg_date."'>".substr($reg_date,0,10)."</td>\n";
					echo "	<TD align=center class=\"td_con1\">".$row->email."</td>\n";
					//echo "	<a href=\"javascript:MemberMail('".$row->email."','".$row->news_yn."');\"><img src=\"images/btn_send.gif\" width=\"35\" height=\"29\" border=\"0\"></a>";
					//echo "	</td>\n";
					echo "	<TD align=center class=\"td_con1\">";
					echo "	<a href=\"javascript:MemberMemo2('".$row->id."');\">".(strlen($row->memo)>0?"<img src=\"images/btn_memo.gif\" width=\"35\" height=\"29\" border=\"0\">":"<img src=\"images/btn_memor.gif\" width=\"35\" height=\"29\" border=\"0\">")."</a>";
					echo "	</td>\n";
					echo "	<TD class=\"td_con1\">\n";
					echo "	<table cellpadding=\"0\" cellspacing=\"0\" align=\"center\">\n";
					echo "	<tr>\n";
					echo "		<td>\n";
					echo "		<A HREF=\"javascript:alert('".$haddress."')\"><IMG src=\"images/addr_home.gif\" align=absMiddle border=0 width=\"19\" height=\"19\"></A>";
					echo "		</td>\n";
					echo "		<td style=\"padding-left:1pt;\">\n";
					echo "		<A HREF=\"javascript:alert('".$oaddress."')\"><IMG src=\"images/addr_office.gif\" align=absMiddle border=0 width=\"19\" height=\"19\"></A>";
					echo "		</td>\n";
					echo "	</tr>\n";
					echo "	</table>\n";
					echo "	</TD>\n";
					echo "	<TD class=\"td_con1\">\n";
					echo "	<table cellpadding=\"0\" cellspacing=\"0\" align=\"center\">\n";
					echo "	<tr>\n";
					echo "		<td>\n";
					if(strlen($row->home_tel)>0 || strlen($row->mobile)>0) {
						//sendsms.php
						$mem_tel ="\\n자택전화 : ".$row->home_tel."";
						$mem_tel.="\\n\\n휴대전화 : ".$row->mobile."\\n";
					echo "		<A HREF=\"javascript:alert('".$mem_tel."')\"><IMG src=\"images/member_tel.gif\" align=absMiddle border=0 width=\"19\" height=\"19\"></A>";
					echo "		</td>\n";
					echo "		<td style=\"padding-left:1pt;\">\n";
					echo "		<A HREF=\"javascript:MemberSMS('".$row->news_yn."','".$row->home_tel."','".$row->mobile."')\"><IMG src=\"images/member_mobile.gif\" align=absMiddle border=0 width=\"19\" height=\"19\"></A>";

						//news_yn : Y/S
					} else {
						echo "- -";
					}
					echo "		</td>\n";
					echo "	</tr>\n";
					echo "	</table>\n";
					echo "	</TD>\n";
					echo "	<TD align=center class=\"td_con1\">".number_format($row->reserve)."</td>\n";
					echo "	<TD class=\"td_con1\">\n";
					echo "	<table cellpadding=\"0\" cellspacing=\"0\" align=\"center\">\n";
					echo "	<tr>\n";
					echo "		<td>\n";
					echo "		<A HREF=\"javascript:ReserveInOut('".$row->id."');\"><img src=\"images/btn_pm.gif\" width=\"35\" height=\"29\" border=\"0\"></A>";
					echo "		</td>\n";
					echo "		<td style=\"padding-left:1pt;\">\n";
					echo "		<A HREF=\"javascript:ReserveInfo('".$row->id."');\"><img src=\"images/btn_detail.gif\" width=\"35\" height=\"29\" border=\"0\"></A>";
					echo "		</td>\n";
					echo "	</tr>\n";
					echo "	</table>\n";
					echo "	</TD>\n";
					echo "	<TD class=\"td_con1\">\n";
					echo "	<table cellpadding=\"0\" cellspacing=\"0\" align=\"center\">\n";
					echo "	<tr>\n";
					echo "		<td>\n";
					echo "		<A HREF=\"javascript:OrderInfo('".$row->id."');\"><img src=\"images/btn_purchus.gif\" width=\"35\" height=\"29\" border=\"0\"></A>";
					echo "		</td>\n";
					echo "		<td style=\"padding-left:1pt;\">\n";
					echo "		<A HREF=\"javascript:CouponInfo('".$row->id."');\"><img src=\"images/btn_coupon.gif\" width=\"35\" height=\"29\" border=\"0\"></A>";
					echo "		</td>\n";
					echo "	</tr>\n";
					echo "	</table>\n";
					echo "	</TD>\n";
					if ($scheck!="6") {
						echo "	<TD align=center class=\"td_con1\">".($row->rec_id?$row->rec_id:"&nbsp;")."<br>(".$recom_cnt.")</td>\n";
					} else {
						echo "	<TD align=right class=\"td_con1\" style=\"padding-right:1pt;\">".$row->rec_cnt."명</td>\n";
					}					
					if ($_shopdata->member_baro=="Y") {
						if ($row->confirm_yn == "Y") {
							echo "<TD align=center class=\"td_con1\"><a href=\"javascript:member_baro('cancel','".$row->id."','".$row->email."','".$row->name."');\"><b><span class=\"font_orange\">OK</span></b></td>\n";
						} else {
							echo "<TD align=center class=\"td_con1\"><a href=\"javascript:member_baro('ok','".$row->id."','".$row->email."','".$row->name."');\"><img src=\"images/btn_ok2.gif\" width=\"35\" height=\"29\" border=\"0\"></td>\n";
						}
					}
					echo "</tr>\n";
					echo "<tr>\n";
					echo "	<TD colspan=\"".$member_list_colspan."\" width=\"762\" background=\"images/table_con_line.gif\"><img src=\"images/table_con_line.gif\" width=\"4\" height=\"1\" border=\"0\"></TD>";
					echo "</tr>\n";
					$cnt++;
				}
				mysql_free_result($result);
				?>
				<?
				if($cnt == 0){
				?>
				<TR>
					<TD colspan="<?=$member_list_colspan?>" height="200" align="center">검색조건에 맞는 회원이 없습니다.</TD>
				</TR>
				<?}?>
				<TR>
					<TD background="images/table_top_line.gif" colspan="<?=$member_list_colspan?>" height="1"></TD>
				</TR>
			</TABLE>
		</td>
	</tr>
	<tr><td height=6></td></tr>
	<!--	S :페이징 -->
	<?
				if($cnt > 0){
	?>
	<tr>
			<td><p align="left"><a href="javascript:check_del()"><img src="images/icon_tal.gif" border="0"></a></p><p align="center">
<?
			$total_block = intval($pagecount / $setup[page_num]);

			if (($pagecount % $setup[page_num]) > 0) {
				$total_block = $total_block + 1;
			}

			$total_block = $total_block - 1;

			if (ceil($t_count/$setup[list_num]) > 0) {
				// 이전	x개 출력하는 부분-시작
				$a_first_block = "";
				if ($nowblock > 0) {
					$a_first_block .= "<a href='javascript:GoPage(0,1);' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='첫 페이지';return true\"><IMG src=\"images/icon_first.gif\" border=0 align=\"absmiddle\"></a>&nbsp;&nbsp;";

					$prev_page_exists = true;
				}

				$a_prev_page = "";
				if ($nowblock > 0) {
					$a_prev_page .= "<a href='javascript:GoPage(".($nowblock-1).",".($setup[page_num]*($block-1)+$setup[page_num]).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='이전 ".$setup[page_num]." 페이지';return true\">[prev]</a>&nbsp;&nbsp;";

					$a_prev_page = $a_first_block.$a_prev_page;
				}

				// 일반 블럭에서의 페이지 표시부분-시작

				if (intval($total_block) <> intval($nowblock)) {
					$print_page = "";
					for ($gopage = 1; $gopage <= $setup[page_num]; $gopage++) {
						if ((intval($nowblock*$setup[page_num]) + $gopage) == intval($gotopage)) {
							$print_page .= "<span class=font_orange2><B>[".(intval($nowblock*$setup[page_num]) + $gopage)."]</B></span> ";
						} else {
							$print_page .= "<a href='javascript:GoPage(".$nowblock.",".(intval($nowblock*$setup[page_num]) + $gopage).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$setup[page_num]) + $gopage)."';return true\">[".(intval($nowblock*$setup[page_num]) + $gopage)."]</a> ";
						}
					}
				} else {
					if (($pagecount % $setup[page_num]) == 0) {
						$lastpage = $setup[page_num];
					} else {
						$lastpage = $pagecount % $setup[page_num];
					}

					for ($gopage = 1; $gopage <= $lastpage; $gopage++) {
						if (intval($nowblock*$setup[page_num]) + $gopage == intval($gotopage)) {
							$print_page .= "<span class=font_orange2><B>[".(intval($nowblock*$setup[page_num]) + $gopage)."]</B></span> ";
						} else {
							$print_page .= "<a href='javascript:GoPage(".$nowblock.",".(intval($nowblock*$setup[page_num]) + $gopage).");' onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$setup[page_num]) + $gopage)."';return true\">[".(intval($nowblock*$setup[page_num]) + $gopage)."]</a> ";
						}
					}
				}		// 마지막 블럭에서의 표시부분-끝


				$a_last_block = "";
				if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
					$last_block = ceil($t_count/($setup[list_num]*$setup[page_num])) - 1;
					$last_gotopage = ceil($t_count/$setup[list_num]);

					$a_last_block .= "&nbsp;&nbsp;<a href='javascript:GoPage(".$last_block.",".$last_gotopage.");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='마지막 페이지';return true\"><IMG src=\"images/icon_last.gif\" border=0 align=\"absmiddle\" width=\"17\" height=\"14\"></a>";

					$next_page_exists = true;
				}

				// 다음 10개 처리부분...

				$a_next_page = "";
				if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
					$a_next_page .= "&nbsp;&nbsp;<a href='javascript:GoPage(".($nowblock+1).",".($setup[page_num]*($nowblock+1)+1).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='다음 ".$setup[page_num]." 페이지';return true\">[next]</a>";

					$a_next_page = $a_next_page.$a_last_block;
				}
			} else {
				$print_page = "<B>[1]</B>";
			}
?>
			<!-- 페이지 출력 ---------------------->
			<?=$a_div_prev_page?>
			<?=$a_prev_page?>
			<?=$print_page?>
			<?=$a_next_page?>
			<?=$a_div_next_page?>
			<!-- 페이지 출력 끝 -->
			</p>
			</td>
		</tr>
		<?}?>
	<!--	E :페이징 -->
	<tr>
		<td height="30">&nbsp;</td>
	</tr>
	<input type=hidden name=tot value="<?=$cnt?>">
	</form>
	<form name=form2 action="member_reservelist.php" method=post target=reserve_info>
	<input type=hidden name=id>
	<input type=hidden name=type>
	</form>
	<form name=form3 method=post>
	<input type=hidden name=id>
	</form>
	<form name=reserveform action="reserve_money.php" method=post>
	<input type=hidden name=type>
	<input type=hidden name=id>
	</form>
	<!-- <form name=mailform action="member_mailsend.php" method=post>
	<input type=hidden name=rmail> -->
	</form>
	<form name=smsform action="sendsms.php" method=post target="sendsmspop">
	<input type=hidden name=number>
	</form>
	<form name=idxform action="<?=$_SERVER[PHP_SELF]?>" method=post>
	<input type=hidden name=block value="">
	<input type=hidden name=gotopage value="">
	<input type=hidden name=member_search value="<?=$member_search?>">
	<input type=hidden name=scheck value="<?=$scheck?>">
	<input type=hidden name=group_code value="<?=$group_code?>">
	<input type=hidden name=vperiod value="<?=$vperiod?>">
	<input type=hidden name=search_start value="<?=$search_start?>">
	<input type=hidden name=search_end value="<?=$search_end?>">
	<input type=hidden name=search value="<?=$search?>">

	<input type=hidden name=search_start value="<?=$search_start?>">
	<input type=hidden name=search_end value="<?=$search_end?>">
	</form>

	<!-- <form name=excelform action="member_excel.php" method=post>
	<input type=hidden name=member_search value="<?=$member_search?>">
	<input type=hidden name=scheck value="<?=$scheck?>">
	<input type=hidden name=group_code value="<?=$group_code?>">
	<input type=hidden name=search_start value="<?=$search_start?>">
	<input type=hidden name=search_end value="<?=$search_end?>">
	<input type=hidden name=search value="<?=$search?>">
	</form> -->
</table>
<?=$onload?>