<?
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	INCLUDE ("access.php");

	####################### 페이지 접근권한 check ###############
	$PageCode = "mo-1";
	$MenuCode = "mobile";
	if (!$_usersession->isAllowedTask($PageCode)) {
		INCLUDE ("AccessDeny.inc.php");
		exit;
	}

	#########################################################

	$mode=$_POST["mode"];
	$mode_result=$_POST["mode_result"];

	$code=$_POST["code"];
	$parentcode=$_POST["parentcode"];

	$mobile_display=$_POST["up_mobile_display"];
	if($mobile_display!="N") {
		$mobile_display="Y";
	}

	if ($mode=="insert" && strlen($up_code_name)>0) {


	} else if($mode=="modify" && strlen($code)==12) {

		$codeA=substr($code,0,3);
		$codeb=substr($code,3,3);
		$codeC=substr($code,6,3);
		$codeD=substr($code,9,3);

		$sql = "SELECT * FROM tblproductcode WHERE codeA='".$codeA."' AND codeb='".$codeb."' ";
		$sql.= "AND codeC='".$codeC."' AND codeD='".$codeD."' ";
		$result = mysql_query($sql,get_db_conn());
		$row = mysql_fetch_object($result);

		mysql_free_result($result);

		if(!$row) {
			echo "<script>parent.HiddenFrame.alert('해당 상품카테고리 정보가 존재하지 않습니다.');parent.location.reload();</script>";
			exit;
		}

		$type=$row->type;

		if ($mode_result=="result") {	//수정내역 업데이트

			$up_code_name = ereg_replace(";","",$up_code_name);
			$sql = "UPDATE tblproductcode SET ";
			$sql.= "mobile_display		= '".$mobile_display."'";
			$sql.= "WHERE codeA = '".$codeA."' AND codeb = '".$codeb."' ";
			$sql.= "AND codeC = '".$codeC."' AND codeD = '".$codeD."' ";

			$update = mysql_query($sql,get_db_conn());

			if ($update) {

				$onload="<script>parent.HiddenFrame.alert(' 카테고리 숨김여부 설정이 완료되었습니다.');</script>";

			} else {

			$onload="<script>parent.HiddenFrame.alert('상품카테고리 정보 수정중 오류가 발생하였습니다.');</script>";

			}

			$sql = "SELECT * FROM tblproductcode WHERE codeA='".$codeA."' AND codeb='".$codeb."' ";
			$sql.= "AND codeC='".$codeC."' AND codeD='".$codeD."' ";
			$result = mysql_query($sql,get_db_conn());
			$row = mysql_fetch_object($result);
			mysql_free_result($result);
		}

		$type=$row->type;
		$code_name=$row->code_name;
		$list_type=$row->list_type;
		$detail_type=$row->detail_type;
		$group_code=$row->group_code;
		$sort=$row->sort;
		$special=$row->special;
		$special_cnt=$row->special_cnt;
		$islist=$row->islist;
		$arr_special=explode(",",$special);
		$old_special=$special;
		$mobile_display=$row->mobile_display;

		unset($special);

		for($i=0;$i<count($arr_special);$i++) {
			$special[$arr_special[$i]]="Y";
		}

		if(strlen($old_special)==0) {
			$old_special="1,2,3";
		} else {
			if(!eregi("1",$old_special)) {
				$old_special.=",1";
			}
			if(!eregi("2",$old_special)) {
				$old_special.=",2";
			}
			if(!eregi("3",$old_special)) {
			$old_special.=",3";
			}
		}

		$arrspecialcnt=explode(",",$special_cnt);

		for ($i=0;$i<count($arrspecialcnt);$i++) {
			if (substr($arrspecialcnt[$i],0,2)=="1:") {
				$tmpsp1=substr($arrspecialcnt[$i],2);
			} else if (substr($arrspecialcnt[$i],0,2)=="2:") {
				$tmpsp2=substr($arrspecialcnt[$i],2);
			} else if (substr($arrspecialcnt[$i],0,2)=="3:") {
				$tmpsp3=substr($arrspecialcnt[$i],2);
			}
		}
		if(strlen($tmpsp1)>0) {
			$special_1=explode("X",$tmpsp1);
			$special_1_cols=(int)$special_1[0];
			$special_1_rows=(int)$special_1[1];
			$special_1_type=$special_1[2];
		}
		if(strlen($tmpsp2)>0) {
			$special_2=explode("X",$tmpsp2);
			$special_2_cols=(int)$special_2[0];
			$special_2_rows=(int)$special_2[1];
			$special_2_type=$special_2[2];
		}
		if(strlen($tmpsp3)>0) {
			$special_3=explode("X",$tmpsp3);
			$special_3_cols=(int)$special_3[0];
			$special_3_rows=(int)$special_3[1];
			$special_3_type=$special_3[2];
		}

		if($special_1_cols<=0) $special_1_cols=5;
		if($special_1_rows<=0) $special_1_rows=1;
		if(strlen($special_1_type)==0) $special_1_type="I";
		if($special_2_cols<=0) $special_2_cols=5;
		if($special_2_rows<=0) $special_2_rows=1;
		if(strlen($special_2_type)==0) $special_2_type="I";
		if($special_3_cols<=0) $special_3_cols=5;
		if($special_3_rows<=0) $special_3_rows=1;
		if(strlen($special_3_type)==0) $special_3_type="I";

		$type1=substr($type,0,1);

		if (ereg("X",$type)) {
			$type2="1";	//하위카테고리 없음
		} else {
			$type2="0";	//하위카테고리 있음
		}

		$gong="N";
		if (substr($row->list_type,0,1)=="b") {
			$gong="Y";
		}

		$code_loc = "";
		$sql = "SELECT code_name,type FROM tblproductcode WHERE codeA='".substr($code,0,3)."' ";

		if(substr($code,3,3)!="000") {
			$sql.= "AND (codeb='".substr($code,3,3)."' OR codeb='000') ";
			if(substr($code,6,3)!="000") {
				$sql.= "AND (codeC='".substr($code,6,3)."' OR codeC='000') ";
					if(substr($code,9,3)!="000") {
						$sql.= "AND (codeD='".substr($code,9,3)."' OR codeD='000') ";
					} else {
						$sql.= "AND codeD='000' ";
					}
			} else {
				$sql.= "AND codeC='000' ";
			}
		} else {
			$sql.= "AND codeb='000' AND codeC='000' ";
		}

		$sql.= "ORDER bY codeA,codeb,codeC,codeD ASC ";

		$result=mysql_query($sql,get_db_conn());
		$i=0;

		while($row=mysql_fetch_object($result)) {
			if($i>0) $code_loc.= " > ";
			$code_loc.= $row->code_name;
			$i++;
		}
		mysql_free_result($result);

	} else {

		$mode="insert";

	}

?>

<? INCLUDE "header.php"; ?>

<script type="text/javascript" src="lib.js.php"></script>
<script>var LH = new LH_create();</script>
<script for=window event=onload>LH.exec();</script>
<script>LH.add("parent_resizeIframe('PropertyFrame')");</script>

<script language="JavaScript">
	<!--
	function DesignList(idx) {
		document.form1.gong[idx].checked=true;
			if(document.form1.gong[0].checked==true) {
				gong="N";
			} else {
				gong="Y";
			}
		up_list_type=document.form1.up_list_type.value;
		window.open("design_productlist.php?code="+up_list_type+"&gong="+gong,"design","height=450,width=380,scrollbars=yes");
	}

	function DesignDetail(idx) {
		document.form1.gong[idx].checked=true;
			if(document.form1.gong[0].checked==true) {
				gong="N";
			} else {
				gong="Y";
			}
		up_detail_type=document.form1.up_detail_type.value;
		window.open("design_productdetail.php?code="+up_detail_type+"&gong="+gong,"design2","height=450,width=380,scrollbars=yes");
	}

	function ChangeSequence() {
		txt=document.form1.fcode.options[document.form1.fcode.selectedIndex].text;
		if((num=txt.indexOf("(가상대카테고리)"))>0) {
			document.form1.selectedfcodename.value=txt.substr(0,num);
		} else {
			document.form1.selectedfcodename.value = txt;
		}
	}

	function Save() {

		mode = document.form1.mode.value;
		document.form1.submit();
	}
//-->
</script>
<?
if($mode=="insert") {
?>
<!-- 최초로딩시 sks -->
<table border="0" cellpadding="0" cellspacing="0" width="520" height="400">
	<tr>
		<td width="100%" bgcolor="#FFFFFF"><img src="images/product_cate_function_title.gif" width="91" height="24" alt=""></td>
	</tr>
	<tr>
		<td width="100%" height="100%" valign="top" style="border:#FF8730 2px solid;padding-left:5px;padding-right:5px;">
			<div style="text-align:center;padding-top:50px">모바일 페이지에 적용할 카테고리를 선택후 사용하세요~</div>
		</td>
	</tr>
</table>
<?
} else{
?>
<table border="0" cellpadding="0" cellspacing="0" width="520" height="100%">
	<tr>
		<td width="100%" bgcolor="#FFFFFF"><img src="images/product_cate_function_title.gif" width="91" height="24" alt=""></td>
	</tr>
	<tr>
		<td width="100%" height="100%" valign="top" style="bORDER:#FF8730 2px solid;padding-left:5px;padding-right:5px;">
			<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post onsubmit="return false">
				<table cellspacing=0 cellpadding=0 width="100%" border=0>
					<col width=141></col>
					<col width=""></col>
					<tr>
						<td colspan="2" height="10"></td>
					</tr>
					<tr>
						<td colspan="2" background="images/table_con_line.gif"></td>
					</tr>
					<tr>
						<td class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0"><b>카테고리 코드</b></td>
						<td class="td_con1"><b><?=$code?></b></td>
					</tr>
					<tr>
						<td colspan="2" background="images/table_con_line.gif"></td>
					</tr>
					<tr>
						<td class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0"><b>카테고리명</b></td>
						<td class="td_con1"><?=htmlspecialchars($code_name)?></td>
					</tr>
					<tr>
						<td colspan="2" background="images/table_con_line.gif"></td>
					</tr>
					<tr>
						<td class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">카테고리위치</td>
						<td class="td_con1"><?=$code_loc?></td>
					</tr>
					<tr>
						<td colspan="2" background="images/table_con_line.gif"></td>
					</tr>
					<tr>
						<td class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">카테고리타입</td>
						<td class="td_con1">
							<?
								if ($mode=="modify" || (strlen($parentcode)==12 && strlen($type1)>0)) {
									if ($type1=="L") {
										echo "기본 카테고리";
									} else if ($type1=="T") {
										echo "가상 카테고리";
									}
								} else {
									echo "<input type=radio id=\"idx_type1_1\" name=up_type1 value=\"L\" checked style=\"bORDER-RIGHT: medium none; bORDER-TOP: medium none; bORDER-LEFT: medium none; bORDER-bOTTOM: medium none;\"><label style='cursor:hand;' onmouseover=\"style.textDecoration='underline'\" onmouseout=\"style.textDecoration='none'\" for=idx_type1_1>기본 카테고리</label> <input type=radio id=\"idx_type1_2\" name=up_type1 value=\"T\" style=\"bORDER-RIGHT: medium none; bORDER-TOP: medium none; bORDER-LEFT: medium none; bORDER-bOTTOM: medium none;\"><label style='cursor:hand;' onmouseover=\"style.textDecoration='underline'\" onmouseout=\"style.textDecoration='none'\" for=idx_type1_2>가상 카테고리</label>";
								}
							?>
						</td>
					</tr>
					<tr>
						<td colspan="2" background="images/table_con_line.gif"></td>
					</tr>
					<tr>
						<td class="table_cell">
							<img src="images/icon_point2.gif" width="8" height="11" border="0">하위카테고리유무
						</td>
						<td class="td_con1">
							<?
								if ($mode=="modify" || (strlen($parentcode)==12 && $type2==1)) {
									if ($type2=="0") {
										echo "하위카테고리 있음";
									} else {
										echo "하위카테고리 없음";
									}
								} else {
									echo "<input type=radio id=\"idx_type2_1\" name=up_type2 value=\"0\" checked style=\"bORDER-RIGHT: medium none; bORDER-TOP: medium none; bORDER-LEFT: medium none; bORDER-bOTTOM: medium none;\"><label style='cursor:hand;' onmouseover=\"style.textDecoration='underline'\" onmouseout=\"style.textDecoration='none'\" onclick=\"javascript:alert('카테고리 등록시 한번 설정한 하위카테고리유무는 변경이 불가능 하므로 신중히 선택해 주세요.');\" for=idx_type2_1>하위카테고리 있음</label> <input id=\"idx_type2_2\" type=radio name=up_type2 value=\"1\" style=\"border-right: medium none; border-top: medium none; border-left: medium none; border-bottom: medium none;\"><label style='cursor:hand;' onmouseover=\"style.textDecoration='underline'\" onmouseout=\"style.textDecoration='none'\" onclick=\"javascript:alert('카테고리 등록시 한번 설정한 하위카테고리유무는 변경이 불가능 하므로 신중히 선택해 주세요.');\" for=idx_type2_2>하위카테고리 없음</labal>";
								}
							?>
						</td>
					</tr>
					<tr>
						<td colspan="2" background="images/table_con_line.gif"></td>
					</tr>
					<tr>
						<td class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">접근가능 회원등급</td>
						<td class="td_con1">
							<?
								if($group_code=="NO") {
									echo "disabled";
								}
							?>
							<?
								$gcode_array = array("","ALL");
								$gname_array = array("모든사람 접근가능","쇼핑몰 회원만 접근가능");
								$sql = "SELECT group_code,group_name FROM tblmembergroup ";
								$result = mysql_query($sql,get_db_conn());
								$num=2;

								while($row = mysql_fetch_object($result)) {
									$gcode_array[$num]=$row->group_code;
									$gname_array[$num++]=$row->group_name;
								}
								mysql_free_result($result);

								for($i=0;$i<$num;$i++){
									if($group_code==$gcode_array[$i]){
										echo $gname_array[$i];
									}
								}
							?>

						</td>
					</tr>
					<tr>
						<td colspan="2" background="images/table_con_line.gif"></td>
					</tr>
					<tr>
						<td class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">상품정렬</td>
						<td class="td_con1">
							<?
								if ($sort=="date") {
									$str = "상품 등록/수정날짜 순서";
								} else if ($sort=="date2"){
									$str = "상품 등록/수정날짜 순서 + 품절상품 뒤로";
								} else if ($sort=="productname") {
									$str = "상품명 가나다 순서";
								} else if ($sort=="production") {
									$str = "제조사 가나다 순서";
								} else if ($sort=="price") {
									$str = "상품 판매가격 순서";
								}

								echo $str;
							?>
						</td>
					</tr>
					<tr>
						<td colspan="2" background="images/table_con_line.gif"></td>
					</tr>
					<tr>
						<td class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">카테고리 숨김여부</td>
						<td class="td_con1"><? if($group_code=="NO") { echo "숨기기"; } else {	echo "오픈";}?> </td>
					</tr>
					<tr>
						<td colspan="2" background="images/table_con_line.gif"></td>
					</tr>
					<tr>
						<td class="table_cell" width="140"><img src="images/icon_point2.gif" width="8" height="11" border="0">모바일샵에서<br />&nbsp;&nbsp;카테고리 숨김여부</td>
						<td class="td_con1">
							<input type=radio  name=up_mobile_display value="N" <? if($mobile_display=="N") echo "checked";?>> <span style="color:#ff6600"><strong>숨기기</strong></span>&nbsp;
							<input type=radio  name=up_mobile_display value="Y" <? if($mobile_display=="Y") echo "checked";?>> <span style="color:#ff6600"><strong>보이기</strong></span>
						</td>
					</tr>
					<tr>
						<td colspan="2" background="images/table_con_line.gif"></td>
					</tr>
					<tr>
						<td colspan="2" height="10"></td>
					</tr>
					<tr>
						<td colspan="2" align="center">
							<a href="javascript:Save();"><img src="images/botteon_catemodify.gif" width="118" height="38" border="0" hspace="0"></a>&nbsp;
						</td>
					</tr>
					<tr>
						<td colspan="2" height="10"></td>
					</tr>
				</table>
				<input type=hidden name=mode value="<?=$mode?>">
				<input type=hidden name=code value="<?=$code?>">
				<input type=hidden name=parentcode value="<?=$parentcode?>">
				<input type=hidden name=mode_result value="result">
				<input type=hidden name=up_list_type value="<?=$list_type?>">
				<input type=hidden name=up_detail_type value="<?=$detail_type?>">
				<input type=hidden name=old_special value="<?=$old_special?>">
				<input type=hidden name=up_special>
			</form>
		</td>
	</tr>
</table>
</form>
<form name=form2 action="<?=$_SERVER[PHP_SELF]?>" method=post>
<input type=hidden name=mode>
<input type=hidden name=code>
</form>
<?
}
?>
<?=$onload?>
</body>
</html>