<?
$sql = "SELECT recom_memreserve_type, sns_ok, sns_reserve_type ";
$sql.= "FROM tblshopinfo ";
$result=mysql_query($sql,get_db_conn());
if ($row=mysql_fetch_object($result)) {
	$recom_memreserve_type=$row->recom_memreserve_type;
	$sns_ok=$row->sns_ok;
	$sns_reserve_type=$row->sns_reserve_type;
	$arRecomType = explode("",$recom_memreserve_type);
	$arSnsType = explode("",$sns_reserve_type);
}
$imagepath=$Dir.DataDir."shopimages/product/";
$maxfilesize="512000";
$prcode=$_POST["prcode"];
$predit_type="Y";

if(substr($prcode,0,12)!=$code) {
	$prcode="";
	$maxq="";
}
if ($mode == "modify" && strlen($code)==12) {
	if (strlen($prcode)>0) {
		$sql = "SELECT * FROM tblproduct P Left OUTER join tblproduct_social S ON P.productcode = S.pcode ";
		$sql.= "WHERE 1=1  ";
		$sql.= "AND productcode = '".$prcode."' ";
		$result = mysql_query($sql,get_db_conn());
		if ($_data = mysql_fetch_object($result)) {
			$productname = $_data->productname;
			if(strlen($_data->option_quantity)>0) $searchtype=1;
			else if(ereg("^(\[OPTG)([0-9]{4})(\])$",$_data->option1)) $searchtype=3;
			
			// 특수옵션값을 체크한다.
			$dicker = $dicker_text="";
			if (strlen($_data->etctype)>0) {
				$etctemp = explode("",$_data->etctype); 
				$miniq = 1;          // 최소주문수량 기본값 넣는다.
				$maxq = "";
				for ($i=0;$i<count($etctemp);$i++) {
					if ($etctemp[$i]=="BANKONLY")                    $bankonly="Y";        // 현금전용
					else if (substr($etctemp[$i],0,11)=="DELIINFONO=")     $deliinfono=substr($etctemp[$i],11);  // 배송/교환/환불정보 노출안함 정보
					else if ($etctemp[$i]=="SETQUOTA")               $setquota="Y";        // 무이자상품
					else if (substr($etctemp[$i],0,6)=="MINIQ=")     $miniq=substr($etctemp[$i],6);  // 최소주문수량
					else if (substr($etctemp[$i],0,5)=="MAXQ=")      $maxq=substr($etctemp[$i],5);  // 최대주문수량
					else if (substr($etctemp[$i],0,5)=="ICON=")      $iconvalue=substr($etctemp[$i],5);  // 최대주문수량
					else if (substr($etctemp[$i],0,9)=="FREEDELI=")  $freedeli=substr($etctemp[$i],9);  // 무료배송상품
					else if (substr($etctemp[$i],0,7)=="DICKER=") {  
						$dicker="Y"; 
						$dicker_text=str_replace("DICKER=","",$etctemp[$i]); 
					}  // 가격대체문구
				}
			}
			if(strlen($iconvalue)>0) {
				for($i=0;$i<strlen($iconvalue);$i=$i+2) {
					$iconvalue2[substr($iconvalue,$i,2)]="Y";
					//echo "<br>>>>>".substr($iconvalue,$i,2);
				}
			}
			if($_data->brand>0) {
				$sql = "SELECT brandname FROM tblproductbrand WHERE bridx = '".$_data->brand."' ";
				$result2 = mysql_query($sql,get_db_conn());
				$_data2 = mysql_fetch_object($result2);
				$_data->brandname = $_data2->brandname;
				mysql_free_result($result2);
			}
			$start_date1=date("Y-m-d",$_data->sell_startdate);
			$start_date2=date("H",$_data->sell_startdate);
			$start_date3=date("i",$_data->sell_startdate);
			$end_date1=date("Y-m-d",$_data->sell_enddate);
			$end_date2=date("H",$_data->sell_enddate);
			$end_date3=date("i",$_data->sell_enddate);
			
		} else {
			echo "<script>alert('해당 상품이 존재하지 않습니다.');location='".$_SERVER[PHP_SELF]."';</script>";
			exit;
		}
		mysql_free_result($result);
	}

	if(ereg("^(\[OPTG)([0-9]{4})(\])$",$_data->option1)){
		$optcode = substr($_data->option1,5,4);
		$_data->option1="";
		$_data->option_price="";
	}
}

$CurrentTime = time();
$start_date1=$start_date1?$start_date1:date("Y-m-d",$CurrentTime);
$start_date2=$start_date2?$start_date2:date("H",$CurrentTime);
$start_date3=$start_date3?$start_date3:date("i",$CurrentTime);

$end_date1=$end_date1?$end_date1:date("Y-m-d",($CurrentTime+(60*60*24)));
$end_date2=$end_date2?$end_date2:date("H",$CurrentTime);
$end_date3=$end_date3?$end_date3:date("i",$CurrentTime);

$start_date=str_replace("-","",$start_date1).$start_date2.$start_date3."00";
$end_date=str_replace("-","",$end_date1).$end_date2.$end_date3."59";
?>
<script type="text/javascript" src="calendar.js.php"></script>
<script type="text/javascript">
<!--
function NewPrdtInsert(){
	document.cForm.prcode.value="";
	document.cForm.mode.value="write";
	document.cForm.submit();
}
function PrdtDelete() {
	if (confirm("해당 상품을 삭제하시겠습니까?")) {
		document.cForm.mode.value="delete";
		document.cForm.submit();
	}
}
function DeletePrdtImg(temp){
	if(confirm('해당 이미지를 삭제하시겠습니까?')){
		document.cForm.mode.value="delprdtimg";
		document.cForm.delprdtimg.value=temp-1;
		document.cForm.submit();
	}
}
var shop="layer0";
var ArrLayer = new Array ("layer0","layer1","layer2","layer3");
function ViewLayer(gbn){
	if(document.all){
		for(i=0;i<4;i++) {
			if (ArrLayer[i] == gbn)
				document.all[ArrLayer[i]].style.display="";
			else
				document.all[ArrLayer[i]].style.display="none";
		}
	} else if(document.getElementById){
		for(i=0;i<4;i++) {
			if (ArrLayer[i] == gbn)
				document.getElementByld[ArrLayer[i]].style.display="";
			else
				document.getElementByld[ArrLayer[i]].style.display="none";
		}
	} else if(document.layers){
		for(i=0;i<4;i++) {
			if (ArrLayer[i] == gbn)
				document.layers[ArrLayer[i]].display="";
			else
				document.layers[ArrLayer[i]].display="none";
		}
	}
	shop=gbn;
}
function ViewSnsLayer(display) {
	if(document.getElementById("sns_optionWrap"))
		document.getElementById("sns_optionWrap").style.display = display;
}

function BrandSelect() {
	window.open("product_brandselect.php","brandselect","height=400,width=420,scrollbars=no,resizable=no");
}

function FiledSelect(pagetype) {
	window.open("product_select.php?type="+pagetype,pagetype,"height=400,width=420,scrollbars=no,resizable=no");
}

function deli_helpshow() {
	if(document.getElementById('deli_helpshow_idx')) {
		if(document.getElementById('deli_helpshow_idx').style.display=="none") {
			document.getElementById('deli_helpshow_idx').style.display="";
		} else {
			document.getElementById('deli_helpshow_idx').style.display="none";
		}
	}
}
/* 적립금 입력 관련 */
function chkFieldMaxLenFunc(thisForm,reserveType) {
	if (reserveType=="Y") { max=5; addtext="/특수문자(소수점)";} else { max=6; }
	if (thisForm.reserve.value.bytes() > max) {
		alert("입력할 수 있는 허용 범위가 초과되었습니다.\n\n" + "숫자"+addtext+" " + max + "자 이내로 입력이 가능합니다.");
		thisForm.reserve.value = thisForm.reserve.value.cut(max);
		thisForm.reserve.focus();
	}
}

function getSplitCount(objValue,splitStr)
{
	var split_array = new Array();
	split_array = objValue.split(splitStr);
	return split_array.length;
}

function getPointCount(objValue,splitStr,falsecount)
{
	var split_array = new Array();
	split_array = objValue.split(splitStr);
	
	if(split_array.length!=2) {
		if(split_array.length==1) {
			return false;
		} else {
			return true;
		}
	} else {
		if(split_array[1].length>falsecount) {
			return true;
		} else {
			return false;
		}
	}
}

function isDigitSpecial(objValue,specialStr)
{
	if(specialStr.length>0) {
		var specialStr_code = parseInt(specialStr.charCodeAt(i));

		for(var i=0; i<objValue.length; i++) {
			var code = parseInt(objValue.charCodeAt(i));
			var ch = objValue.substr(i,1).toUpperCase();
			
			if((ch<"0" || ch>"9") && code!=specialStr_code) {
				return true;
				break;
			}
		}
	} else {
		for(var i=0; i<objValue.length; i++) {
			var ch = objValue.substr(i,1).toUpperCase();
			if(ch<"0" || ch>"9") {
				return true;
				break;
			}
		}
	}
}

//-->
</script>
<table cellpadding="0" cellspacing="0" width="100%">
<tr><td height="8"></td></tr>
<tr>
	<td>
	<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
	<TR>
		<TD><IMG SRC="images/social_shopping_title.gif" ALT="공동구매 상품관리"></TD>
		</tr><tr>
		<TD width="100%" background="images/title_bg.gif" height="21"></TD>
	</TR>
	</TABLE>
	</td>
</tr>
<tr><td height=20></td></tr>
<tr>
	<td>
	<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
	<TR>
		<TD><IMG SRC="images/social_shopping_stitle2.gif"  ALT="공동구매 상품 등록 및 수정"></TD>
		<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif">&nbsp;</TD>
		<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></TD>
	</TR>
	</TABLE>
	</td>
</tr>
<tr><td height=3></td></tr>
<tr><td>
	<table cellpadding="0" cellspacing="0" width="100%">
	<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post enctype="multipart/form-data">
	<input type=hidden name=mode>
	<input type=hidden name=code value="<?=$code?>">
	<input type=hidden name=prcode value="<?=$prcode?>">
	<input type=hidden name=htmlmode value='wysiwyg'>
	<input type=hidden name=delprdtimg>
	<input type=hidden name=option1>
	<input type=hidden name=option2>
	<input type=hidden name=option_price>
	<tr>
		<td align="center">
		<table cellpadding="0" cellspacing="0" width="100%">
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
				<TD width="100%" class="notice_blue"><img src="images/icon_point2.gif" width="8" height="11" border="0"> <span class="font_orange"><b>필수표시 항목</b></span></TD>
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
		<tr><td height=3></td></tr>
		<tr>
			<td>
			<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0 style="table-layout:fixed">
			<col width=140></col>
			<col width=50%></col>
			<col width=140></col>
			<col width=50%></col>
			<TR>
				<TD colspan="4" background="images/table_con_line.gif"></TD>
			</TR>
			<?if($_data->vender>0){?>
			<tr>
				<td class="table_cell">등록업체</td>
				<td class="td_con1" colspan="3">
				<?
				$sql = "SELECT vender,id,brand_name FROM tblvenderstore WHERE vender='".$_data->vender."' ";
				$result=mysql_query($sql,get_db_conn());
				if($row=mysql_fetch_object($result)) {
					echo "<A HREF=\"javascript:viewVenderInfo(".$row->vender.")\"><B>".$row->brand_name." (".$row->id.")</B></A>";
				}
				mysql_free_result($result);
				?>
				</td>
			</tr>
			<?}?>
			<TR>
				<TD class="table_cell"><img src="images/icon_point5.gif" width="8" height="11" border="0">현재 카테고리</TD>
				<TD class="td_con1" colspan="3" style="word-break:break-all;">
<?
				$code_loc = "";
				$sql = "SELECT code_name,type FROM tblproductcode WHERE codeA='".substr($code,0,3)."' ";
				if(substr($code,3,3)!="000") {
					$sql.= "AND (codeB='".substr($code,3,3)."' OR codeB='000') ";
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
					$sql.= "AND codeB='000' AND codeC='000' ";
				}
				$sql.= "ORDER BY codeA,codeB,codeC,codeD ASC ";
				$result=mysql_query($sql,get_db_conn());
				$i=0;
				while($row=mysql_fetch_object($result)) {
					if($i>0) $code_loc.= " > ";
					$code_loc.= $row->code_name;
					$i++;
				}
				mysql_free_result($result);

				if (strlen($prcode)>0) {
					echo $code_loc." > <B><span class=\"font_orange\">".$productname."</span></B>";
				} else {
					echo $code_loc." > <B><span class=\"font_orange\">신규입력</span></B>";
				}
?>
				</TD>
			</TR>
			<TR>
				<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0"><b><span class="font_orange">상품명</span></b></TD>
				<TD class="td_con1" colspan="3"><input name=productname value="<?=ereg_replace("\"","&quot",$_data->productname)?>" size=80 maxlength=250 onKeyDown="chkFieldMaxLen(250)" class="input" style=width:100%></TD>
			</TR>
			<TR>
				<TD colspan="4" background="images/table_con_line.gif"></TD>
			</TR>
<? 
	if($mode != "write"){
?>
			<TR>
				<TD class="table_cell"><img src="images/icon_point5.gif" width="8" height="11" border="0">상품코드정보</TD>
				<TD class="td_con1" colspan="3">(상품코드 : <span class="font_orange"><?=$_data->productcode?></span>)<a href="http://<?=$shopurl."?productcode=".$_data->productcode?>" target="_blank"><img src="images/productregister_goproduct.gif" align="absmiddle" border="0" alt="진열상품 바로가기"></font></a>
				</TD>
			</TR>
			<TR>
				<TD colspan="4" background="images/table_con_line.gif"></TD>
			</TR>
<? 
	}
?>
			<tr>
				<TD class="table_cell"><img src="images/icon_point5.gif" width="8" height="11" border="0">제조사</TD>
				<TD class="td_con1"><input name=production value="<?=$_data->production?>" size=23 maxlength=20 onKeyDown="chkFieldMaxLen(50)" class="input"><a href="javascript:FiledSelect('PR');"><img src="images/btn_select.gif" border="0" hspace="5" align="absmiddle"></a></TD>
				<TD class="table_cell" style="border-left-width:1pt; border-color:rgb(227,227,227); border-top-style:none; border-right-style:none; border-bottom-style:none; border-left-style:solid;"><img src="images/icon_point5.gif" width="8" height="11" border="0">원산지</TD>
				<TD class="td_con1"><input name=madein value="<?=$_data->madein?>" size=23 maxlength=20 onKeyDown="chkFieldMaxLen(30)" class="input"><a href="javascript:FiledSelect('MA');"><img src="images/btn_select.gif" border="0" hspace="5" align="absmiddle"></a></TD>
			</tr>
			<TR>
				<TD colspan="4" background="images/table_con_line.gif"></TD>
			</TR>
			<tr>
				<TD class="table_cell"><img src="images/icon_point5.gif" width="8" height="11" border="0">브랜드</TD>
				<TD class="td_con1"><input type=text name=brandname value="<?=$_data->brandname?>" size=23 maxlength=50 onKeyDown="chkFieldMaxLen(50)" class="input"><a href="javascript:BrandSelect();"><img src="images/btn_select.gif" border="0" hspace="5" align="absmiddle"></a><br>
				<span class="font_orange">* 브랜드를 직접 입력시에도 등록됩니다.</span></TD>
				<TD class="table_cell" style="border-left-width:1pt; border-color:rgb(227,227,227); border-top-style:none; border-right-style:none; border-bottom-style:none; border-left-style:solid;"><img src="images/icon_point5.gif" width="8" height="11" border="0">모델명</TD>
				<TD class="td_con1"><input name=model value="<?=$_data->model?>" size=23 maxlength=40 onKeyDown="chkFieldMaxLen(50)" class="input"><a href="javascript:FiledSelect('MO');"><img src="images/btn_select.gif" border="0" hspace="5" align="absmiddle"></a></TD>
			</tr>
			<TR>
				<TD colspan="4" background="images/table_con_line.gif"></TD>
			</TR>
			<tr>
				<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0"><b><span class="font_orange">공동구매 시작일</span></b></TD>
				<TD class="td_con1">
				<INPUT class="input_selected" style="text-align:center;" onfocus=this.blur(); onclick=Calendar(this) size=10 value="<?=$start_date1?>" name=start_date1> 
				<SELECT name=start_date2 class="select">
<?
				for($i=0;$i<=23;$i++) {
					$val=substr("0".$i,-2);
					if($i<=5) {
						echo "<option value=\"".$val."\"";
						if($val==$start_date2) {
							echo "selected";
						}
						echo " >새벽 ".$i."시</option>";
					} else if($i<=11) {
						echo "<option value=\"".$val."\"";
						if($val==$start_date2) {
							echo "selected";
						}
						echo " >오전 ".$i."시</option>";
					} else {
						echo "<option value=\"".$val."\"";
						if($val==$start_date2) {
							echo "selected";
						}
						echo " >오후 ".$i."시</option>";
					}
				}
?>
				</SELECT>
				 
				<SELECT name=start_date3 class="select">
<?
				for($i=0;$i<=59;$i++) {
					$val=substr("0".$i,-2);
					echo "<option value=\"".$val."\"";
					if($val==$start_date3) {
						echo "selected";
					}
					echo " >".$val."분</option>";
				}
?>
				</SELECT>
				</TD>
				<TD class="table_cell" style="border-left-width:1pt; border-color:rgb(227,227,227); border-top-style:none; border-right-style:none; border-bottom-style:none; border-left-style:solid;"><img src="images/icon_point2.gif" width="8" height="11" border="0"><b><span class="font_orange">공동구매 종료일</span></b></TD>
				<TD class="td_con1">
				<INPUT class="input_selected" style="text-align:center;" onfocus=this.blur(); onclick=Calendar(this) size=10 value="<?=$end_date1?>" name=end_date1> 
				<SELECT name=end_date2 class="select">
<?
				for($i=0;$i<=23;$i++) {
					$val=substr("0".$i,-2);
					if($i<=5) {
						echo "<option value=\"".$val."\"";
						if($val==$end_date2) {
							echo "selected";
						}
						echo " >새벽 ".$i."시</option>";
					} else if($i<=11) {
						echo "<option value=\"".$val."\"";
						if($val==$end_date2) {
							echo "selected";
						}
						echo " >오전 ".$i."시</option>";
					} else {
						echo "<option value=\"".$val."\"";
						if($val==$end_date2) {
							echo "selected";
						}
						echo " >오후 ".$i."시</option>";
					}
				}
?>
				</SELECT>
				 
				<SELECT name=end_date3 class="select">
<?
				for($i=0;$i<=59;$i++) {
					$val=substr("0".$i,-2);
					echo "<option value=\"".$val."\"";
					if($val==$end_date3) {
						echo "selected";
					}
					echo " >".$val."분</option>";
				}
?>
				</SELECT>
				</TD>
			</tr>
			<TR>
				<TD colspan="4" background="images/table_con_line.gif"></TD>
			</TR>

			<TR>
				<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0"><span class="font_orange"><b>판매가격(할인가)</b></span></TD>
				<TD class="td_con1"><input name="sellprice" value="<?=$_data->sellprice?>" size=16 maxlength=10 class="input" style=width:98%></TD>
				<TD class="table_cell" style="border-left-width:1pt; border-color:rgb(227,227,227); border-top-style:none; border-right-style:none; border-bottom-style:none; border-left-style:solid;"><img src="images/icon_point2.gif" width="8" height="11" border="0"><span class="font_orange"><b>원래가격(정상가)</b></span></TD>
				<TD class="td_con1"><input name="consumerprice" value="<?=(int)(strlen($_data->consumerprice)>0?$_data->consumerprice:"0")?>" size="16" maxlength="10" class="input" style=width:100%></TD>
			</tr>
			<TR>
				<TD colspan="4" background="images/table_con_line.gif"></TD>
			</TR>
			<tr>
				<TD class="table_cell"><img src="images/icon_point5.gif" width="8" height="11" border="0">적립금(률)</TD>
				<TD class="td_con1"><input name=reserve value="<?=$_data->reserve?>" size=16 maxlength=6 class="input" style="width:60%" onKeyUP="chkFieldMaxLenFunc(this.form,this.form.reservetype.value);"> <select name="reservetype" class="select" onchange="chkFieldMaxLenFunc(this.form,this.value);"><option value="N"<?=($_data->reservetype!="Y"?" selected":"")?>>적립금(￦)</option><option value="Y"<?=($_data->reservetype!="Y"?"":" selected")?>>적립률(%)</option></select><br><span class="font_orange" style="font-size:8pt;letter-spacing:-0.5pt">* 적립률은 소수점 둘째자리까지 입력 가능합니다.<br>* 적립률에 대한 적립 금액 소수점 자리는 반올림.</span>
				</TD>
				<TD class="table_cell" style="border-left-width:1pt; border-color:rgb(227,227,227); border-top-style:none; border-right-style:none; border-bottom-style:none; border-left-style:solid;"><img src="images/icon_point5.gif" width="8" height="11" border="0">구입원가</TD>
				<TD class="td_con1"><input name=buyprice value="<?=$_data->buyprice?>" size=16 maxlength=10 class="input" style=width:100%></TD>
			</tr>
			<TR>
				<TD colspan="4" background="images/table_con_line.gif"></TD>
			</TR>
			<tr>
				<TD class="table_cell"><img src="images/icon_point5.gif" width="8" height="11" border="0">최소구매수량</TD>
				<TD class="td_con1"><input type=text name=miniq value="<?=($miniq>0?$miniq:"1")?>" size=5 maxlength=5 class="input"> 개 이상</TD>
				<TD class="table_cell" style="border-left-width:1pt; border-color:rgb(227,227,227); border-top-style:none; border-right-style:none; border-bottom-style:none; border-left-style:solid;"><img src="images/icon_point5.gif" width="8" height="11" border="0">최대구매수량</TD>
				<TD class="td_con1"><input type=radio id="idx_checkmaxq1" name=checkmaxq value="A" <? if (strlen($maxq)==0 || $maxq=="?") echo "checked ";?> onclick="document.form1.maxq.disabled=true;document.form1.maxq.style.background='silver';"><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_checkmaxq1>무제한</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=radio id="idx_checkmaxq2" name=checkmaxq value="B" <? if ($maxq!="?" && $maxq>0) echo "checked"; ?> onclick="document.form1.maxq.disabled=false;document.form1.maxq.style.background='white';"><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_checkmaxq2>수량</label> : <input name=maxq size=5 maxlength=5 value="<?=$maxq?>" class="input"> 개 이하
				<script>
				if (document.form1.checkmaxq[0].checked==true) { document.form1.maxq.disabled=true;document.form1.maxq.style.background='silver'; }
				else if (document.form1.checkmaxq[1].checked==true) { document.form1.maxq.disabled=false;document.form1.maxq.style.background='white'; }
				</script>
				</TD>
			</tr>
			<TR>
				<TD colspan="4" background="images/table_con_line.gif"></TD>
			</TR>
			<tr>
				<TD class="table_cell"><img src="images/icon_point5.gif" width="8" height="11" border="0"><b><span class="font_orange">구매 달성 수량</span></b></TD>
				<TD class="td_con1" colspan="3"><input type=text name=complete_quantity size=5 maxlength=5 value="<?=$_data->complete_quantity ?>" class="input">개</TD>
			</tr>
			<TR>
				<TD colspan="4" background="images/table_con_line.gif"></TD>
			</TR>
			<tr>
				<TD class="table_cell"><img src="images/icon_point5.gif" width="8" height="11" border="0"><b><span class="font_orange">총 구매제한수량</span></b></TD>
				<TD class="td_con1" colspan="3">
<?
				if ($_data) {
					$quantity=$_data->quantity;
					if($_data->quantity==NULL) $checkquantity="F";
					else if($_data->quantity<=0) $checkquantity="E";
					else $checkquantity="C";
					if($quantity<0) $quantity="";
				} else {
					$checkquantity="F";
				}

				$arrayname= array("품절","무제한","수량");
				$arrayprice=array("E","F","C");
				$arraydisable=array("true","true","false");
				$arraybg=array("silver","silver","white");
				$arrayquantity=array("","","$quantity");
				$cnt = count($arrayprice);
				for($i=0;$i<$cnt;$i++){
					echo "<input type=radio id=\"idx_checkquantity".$i."\" name=checkquantity value=\"".$arrayprice[$i]."\" "; 
					if($checkquantity==$arrayprice[$i]) echo "checked "; echo "onClick=\"document.form1.quantity.disabled=".$arraydisable[$i].";document.form1.quantity.style.background='".$arraybg[$i]."';document.form1.quantity.value='".$arrayquantity[$i]."';\"><label style='cursor:hand;' onmouseover=\"style.textDecoration='underline'\" onmouseout=\"style.textDecoration='none'\" for=idx_checkquantity".$i.">".$arrayname[$i]."</label>&nbsp;&nbsp;";
				}
				echo ": <input type=text name=quantity size=5 maxlength=5 value=\"".($quantity==0?"":$quantity)."\" class=\"input\">개 구매시 자동판매종료";

/*
				$arrayname= array("마감","수량");
				$arrayprice=array("E","C");
				$arraydisable=array("true","false");
				$arraybg=array("silver","white");
				$arrayquantity=array("","$quantity");
				for($i=0;$i<2;$i++){
					echo "<input type=radio id=\"idx_checkquantity".$i."\" name=checkquantity value=\"".$arrayprice[$i]."\" ";
					if($checkquantity==$arrayprice[$i]) echo "checked "; echo "onClick=\"document.form1.quantity.disabled=".$arraydisable[$i].";document.form1.quantity.style.background='".$arraybg[$i]."';document.form1.quantity.value='".$arrayquantity[$i]."';\"><label style='cursor:hand;' onmouseover=\"style.textDecoration='underline'\" onmouseout=\"style.textDecoration='none'\" for=idx_checkquantity".$i.">".$arrayname[$i]."</label>&nbsp;";
				}
				echo ": <input type=text name=quantity size=5 maxlength=5 value=\"".($quantity==0?"":$quantity)."\" class=\"input\">개 구매시 자동판매종료";
*/
?>
				</TD>
			</tr>
			<TR>
				<TD colspan="4" background="images/table_con_line.gif"></TD>
			</TR>
			<tr>
				<TD class="table_cell"><img src="images/icon_point5.gif" width="8" height="11" border="0"><b><span class="font_orange">단일 회원</span></b></TD>
				<TD class="td_con1" colspan="3">
					<input type=radio id="idx_check_member1" name="member_check" value="Y" <?if($_data->member_check=="Y") echo "checked";?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for="idx_check_member1">중복구매 허용</label>&nbsp;
					<input type=text name=sellcount_member size=5 maxlength=5 value="<?=$_data->sellcount_member?>" class="input">개 까지 허용(0 일 경우 무제한 허용)
					<br>
					<span class="font_orange"><input type=radio id="idx_check_member2" name="member_check" value="N" <?if($_data->member_check=="N") echo "checked";?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for="idx_check_member2">중복구매 불가</label>
				</TD>
			</tr>
			<TR>
				<TD colspan="4" background="images/table_con_line.gif"></TD>
			</TR>
			<tr>
				<TD class="table_cell" style="border-left-width:1pt; border-color:rgb(227,227,227); border-top-style:none; border-right-style:none; border-bottom-style:none; border-left-style:solid;"><img src="images/icon_point5.gif" width="8" height="11" border="0"><b>노출 판매수량</b></TD>
				<TD class="td_con1" colspan="3">
					<input type="radio" name="sellcount_type" value="B" id="sellcount_type1" <?if($_data->sellcount_type=="B") echo "checked";?>><label for="sellcount_type1">실제판매수량</label><br>
					<input type="radio" name="sellcount_type" value="C" id="sellcount_type2" <?if($_data->sellcount_type=="C") echo "checked";?>><label for="sellcount_type2">실제판매수량</label> + <input type=text name=sellcount_add_C size=5 maxlength=5 value="<?=$_data->sellcount_add ?>" class="input">개<br>
					<input type="radio" name="sellcount_type" value="R" id="sellcount_type3" <?if($_data->sellcount_type=="R") echo "checked";?>><label for="sellcount_type3">실제판매수량</label> + 실제판매수량의 <input type=text name=sellcount_add_R size=5 maxlength=5 value="<?=$_data->sellcount_add ?>" class="input">%<br>
					<input type="radio" name="sellcount_type" value="A" id="sellcount_type3" <?if($_data->sellcount_type=="A") echo "checked";?>><label for="sellcount_type3">판매시작 후 1시간마다</label> + <input type=text name=sellcount_add_A size=5 maxlength=5 value="<?=$_data->sellcount_add ?>" class="input">개<br></TD>
			</tr>
			<TR>
				<TD colspan="4" background="images/table_con_line.gif"></TD>
			</TR>
			<tr>
				<td class="table_cell"><img src="images/icon_point5.gif" width="8" height="11" border="0">개별배송비</td>
				<td class="td_con1" colspan="3">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<tr>
					<td><input type=radio id="idx_deliprtype0" name=deli value="H" <?if($_data->deli_price<=0 && $_data->deli=="N") echo "checked";?> onclick="document.form1.deli_price_value1.disabled=true;document.form1.deli_price_value1.style.background='silver';document.form1.deli_price_value2.disabled=true;document.form1.deli_price_value2.style.background='silver';"><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_deliprtype0>기본 배송비 <b>유지</b></label>
						&nbsp;&nbsp;&nbsp;&nbsp;
						<input type=radio id="idx_deliprtype2" name=deli value="F" <?if($_data->deli_price<=0 && $_data->deli=="F") echo "checked";?> onclick="document.form1.deli_price_value1.disabled=true;document.form1.deli_price_value1.style.background='silver';document.form1.deli_price_value2.disabled=true;document.form1.deli_price_value2.style.background='silver';"><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_deliprtype2>개별 배송비 <b><font color="#0000FF">무료</font></b></label>
						&nbsp;&nbsp;&nbsp;&nbsp;
						<input type=radio id="idx_deliprtype1" name=deli value="G" <?if($_data->deli_price<=0 && $_data->deli=="G") echo "checked";?> onclick="document.form1.deli_price_value1.disabled=true;document.form1.deli_price_value1.style.background='silver';document.form1.deli_price_value2.disabled=true;document.form1.deli_price_value2.style.background='silver';"><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_deliprtype1>개별 배송비 <b><font color="#38A422">착불</font></b></label>
					</td>
				</tr>
				<tr>
					<td height="5"></td>
				</tr>
				<tr>
					<td><input type=radio id="idx_deliprtype3" name=deli value="N" <?if($_data->deli_price>0 && $_data->deli=="N") echo "checked";?> onclick="document.form1.deli_price_value1.disabled=false;document.form1.deli_price_value1.style.background='';document.form1.deli_price_value2.disabled=true;document.form1.deli_price_value2.style.background='silver';"><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_deliprtype3>개별 배송비 <b><font color="#FF0000">유료</font></b> <input type=text name=deli_price_value1 value="<?if($_data->deli_price>0 && $_data->deli=="N") echo $_data->deli_price;?>" size=6 maxlength=6 <?if($_data->deli_price<=0 || $_data->deli=="Y") echo "disabled style='background:silver'";?> class="input">원</label>&nbsp;<a href="javascript:deli_helpshow();"><img src="images/product_optionhelp3.gif" border="0" align="absmiddle"></a>
						<br>
						<input type=radio id="idx_deliprtype4" name=deli value="Y" <?if($_data->deli_price>0 && $_data->deli=="Y") echo "checked";?> onclick="document.form1.deli_price_value2.disabled=false;document.form1.deli_price_value2.style.background='';document.form1.deli_price_value1.disabled=true;document.form1.deli_price_value1.style.background='silver';"><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_deliprtype4>개별 배송비 <b><font color="#FF0000">유료</font></b> <input type=text name=deli_price_value2 value="<?if($_data->deli_price>0 && $_data->deli=="Y") echo $_data->deli_price;?>" size=6 maxlength=6 <?if($_data->deli_price<=0 || $_data->deli=="N") echo "disabled style='background:silver'";?> class="input">원 (구매수 대비 개별 배송비 증가 : <FONT COLOR="#FF0000"><B>상품구매수×개별 배송비</B></font>)</label>&nbsp;<a href="javascript:deli_helpshow();"><img src="images/product_optionhelp3.gif" border="0" align="absmiddle"></a>
					</td>
				</tr>
				<tr id="deli_helpshow_idx" style="display:none;">
					<td style="padding:5px;">
					<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
					<TR>
						<TD><IMG SRC="images/distribute_01.gif"></TD>
						<TD background="images/distribute_02.gif"></TD>
						<TD><IMG SRC="images/distribute_03.gif"></TD>
					</TR>
					<TR>
						<TD background="images/distribute_04.gif"></TD>
						<TD class="notice_blue" valign="top">
						<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
						<TR>
							<TD class="notice_blue" valign="top" width="745" colspan="2"><IMG SRC="images/distribute_img1.gif" width="110" height="35" ></TD>
						</TR>
						<TR>
							<TD class="notice_blue" valign="top">&nbsp;</TD>
							<TD width="100%" class="space"><span class=font_blue>&nbsp;&nbsp;&nbsp;&nbsp;<b>'개별배송비' 입력 후 '배송비 타입 상품수 대비 배송비 증가' <font color='#0000FF'>체크</font> 예)</b><br>
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;구매가격&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: 10,000원 × 2개구매 = 상품가격 20,000원<br>
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;개별배송비&nbsp;&nbsp;: 3,000원 일때 × 2개구매= 총배송비 6,000원<br>
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;총 결제금액 : 26,000원<br><br>
							&nbsp;&nbsp;&nbsp;&nbsp;<b>'개별배송비' 입력 후 '배송비 타입 상품수 대비 배송비 증가' <font color='#FF0000'>미체크</font> 예)</b><br>
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;구매가격&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: 10,000원 × 2개구매 = 상품가격 20,000원<br>
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;개별배송비&nbsp;&nbsp;: 3,000원(구매수가 2개라도 3,000원 한번만 적용)<br>
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;총 결제금액 : 23,000원</span></TD>
						</TR>
						</TABLE>
						</TD>
						<TD background="images/distribute_07.gif"></TD>
					</TR>
					<TR>
						<TD><IMG SRC="images/distribute_08.gif"></TD>
						<TD background="images/distribute_09.gif"></TD>
						<TD><IMG SRC="images/distribute_10.gif"></TD>
					</TR>
					</TABLE>
					</td>
				</tr>
				</table>
				</td>
			</tr>
			<TR>
				<TD colspan="4" background="images/table_con_line.gif"></TD>
			</TR>
			<tr>
				<td class="table_cell"><img src="images/icon_point5.gif" width="8" height="11" border="0">상품노출등급</td>
				<td class="td_con1" colspan="3">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<tr>
					<td><input type=radio id="idx_group_check1" name="group_check" value="N" onclick="GroupCode_Change('N');" <?if($_data->group_check!="Y") echo "checked";?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for="idx_group_check1">상품노출등급 미지정</label>&nbsp;&nbsp;<span class="font_orange">* 상품노출등급 미지정할 경우 모든 비회원, 회원에게 노출됩니다.</span><br><input type=radio id="idx_group_check2" name="group_check" value="Y" onclick="GroupCode_Change('Y');" <?if($_data->group_check=="Y") echo "checked";?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for="idx_group_check2">상품노출등급 지정</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="font_orange">* 회원등급은 <a href="javascript:parent.parent.topframe.GoMenu(3,'member_groupnew.php');"><span class="font_blue">회원관리 > 회원등급 설정 > 회원등급 등록/수정/삭제</span></a>에서 관리하세요.</span></td>
				</tr>
				<tr>
					<td height="5"></td>
				</tr>
				<tr id="group_checkidx" <?if($_data->group_check!="Y") echo "style=\"display:none;\"";?>>
					<td>
					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
					<tr>
						<td bgcolor="#FFF7F0" style="border:2px #FF7100 solid;">
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
						<tr>
<?
						$sqlgrp = "SELECT group_code,group_name FROM tblmembergroup ";
						$resultgrp = mysql_query($sqlgrp,get_db_conn());
						$grpcnt=0;
						while($rowgrp = mysql_fetch_object($resultgrp)){
							if($grpcnt!=0 && $grpcnt%4==0) {
								echo "</tr>\n<tr>\n";
							}
							echo "<td width=\"25%\" style=\"padding:3px;\"><input type=checkbox id=\"group_code_idx".$grpcnt."\" name=\"group_code[]\" value=\"".$rowgrp->group_code."\" ".(strlen($group_code[$rowgrp->group_code])>0?"checked":"")."> <label style='cursor:hand;' onmouseover=\"style.textDecoration='underline'\" onmouseout=\"style.textDecoration='none'\" for=\"group_code_idx".$grpcnt."\">".$rowgrp->group_name."</label></td>\n";
							$grpcnt++;
						}
						mysql_free_result($resultgrp);

						if($grpcnt==0) {
							echo "<td style=\"padding:3px;\">* 회원등급이 존재하지 않습니다.<br>* 회원등급은 <a href=\"javascript:parent.parent.topframe.GoMenu(3,'member_groupnew.php');\"><span class=\"font_blue\">상품관리 > 카테고리/상품관리 > 상품 거래처 관리</span></a>에서 등록하세요.</span></td>\n";
						}
?>
						</tr>
						</table>
						</td>
					</tr>
<?
					if($grpcnt!=0) {
						echo "<tr><td align=\"right\"><input type=checkbox id=\"group_codeall_idx\" onclick=\"GroupCodeAll(this.checked,$grpcnt);\"> <label style='cursor:hand;' onmouseover=\"style.textDecoration='underline'\" onmouseout=\"style.textDecoration='none'\" for=\"group_codeall_idx\">일괄선택/해제</label></td></tr>\n";
					}
?>
					</table>
					</td>
				</tr>
				</table>
				</td>
			</tr>
			<TR>
				<TD colspan="4" background="images/table_con_line.gif"></TD>
			</TR>
			<tr>
				<TD class="table_cell"><img src="images/icon_point5.gif" width="8" height="11" border="0">검색어</TD>
				<TD class="td_con1" colspan="3"><input name=keyword value="<? if ($_data) echo $_data->keyword; ?>" size=80 maxlength=100 onKeyDown="chkFieldMaxLen(100)" class="input" style=width:100%></TD>
			</tr>
			<TR>
				<TD colspan="4" background="images/table_con_line.gif"></TD>
			</TR>
			<TR>
				<TD class="table_cell"><img src="images/icon_point5.gif" width="8" height="11" border="0">특이사항</TD>
				<TD class="td_con1" colspan="3"><input name=addcode value="<? if ($_data) echo ereg_replace("\"","&quot;",$_data->addcode); ?>" size=80 maxlength=200 class="input"><!-- &nbsp;<span class="font_orange">(예: 한정판매 : 50개, 판매수량 : 100개)</span> --></TD>
			</TR>
			<TR>
				<TD colspan="4" background="images/table_con_line.gif"></TD>
			</TR>
			<TR>
				<TD class="table_cell"><img src="images/icon_point5.gif" width="8" height="11" border="0">대 이미지</TD>
				<TD class="td_con1" colspan="3">
				<input type=file name="userfile" onchange="document.getElementById('size_checker').src=this.value;" style="WIDTH: 400px" class="input">
				<input type=text name="userfile_url" value="<?=$userfile_url?>" style="WIDTH: 400px; display:none" class="input">
				<span class="font_orange">(권장이미지 : 665X382)</span>
				<input type=hidden name="vimage" value="<?=$_data->maximage?>">
<?
			if ($_data) {
				if (strlen($_data->maximage)>0 && file_exists($imagepath.$_data->maximage)==true) {
					echo "<br><img src='".$imagepath.$_data->maximage."' height=100 width=200 border=1 alt='URL : http://".$_ShopInfo->getShopurl().DataDir."shopimages/product/".$_data->maximage."'>";
					echo "&nbsp;<a href=\"JavaScript:DeletePrdtImg('1')\"><img src=\"images/icon_del1.gif\" align=bottom border=0></a>";
				} else {
					echo "<br><img src=\"images/space01.gif\">";
				}
			}
?>
				</TD>
			</TR>
			<TR>
				<TD colspan="4" background="images/table_con_line.gif"></TD>
			</TR>
			<TR>
				<TD class="table_cell"><img src="images/icon_point5.gif" width="8" height="11" border="0">중 이미지</TD>
				<TD class="td_con1" colspan="3">
				<input type=file name="userfile2" style="WIDTH: 400px" onchange="document.getElementById('size_checker2').src = this.value;" class="input">
				<input type=text name="userfile2_url" value="<?=$userfile2_url?>" style="WIDTH: 400px; display:none" class="input">
				<span class="font_orange">(권장이미지 : 500X383)</font>
				<input type=hidden name="vimage2" value="<?=$_data->minimage?>">
<?
			if ($_data) {
				if (strlen($_data->minimage)>0 && file_exists($imagepath.$_data->minimage)==true){
					echo "<br><img src='".$imagepath.$_data->minimage."' height=80 width=150 border=1 alt='URL : http://".$_ShopInfo->getShopurl().DataDir."product/".$_data->minimage."'>";
					echo "&nbsp;<a href=\"JavaScript:DeletePrdtImg('2')\"><img src=\"images/icon_del1.gif\" align=bottom border=0></a>";
				} else {
					echo "<br><img src=images/space01.gif>";
				}
			}
?>
				</TD>
			</TR>
			<TR>
				<TD colspan="4" background="images/table_con_line.gif"></TD>
			</TR>
			<TR>
				<TD class="table_cell" style="border-bottom-width:1pt; border-bottom-color:rgb(255,153,51); border-bottom-style:solid;"><img src="images/icon_point5.gif" width="8" height="11" border="0">소 이미지</TD>
				<TD class="td_con1" colspan="3" style="border-bottom-width:1pt; border-bottom-color:rgb(255,153,51); border-bottom-style:solid;">
				<input type=file name="userfile3" style="WIDTH: 400px" onchange="document.getElementById('size_checker3').src = this.value;" class="input">
				<input type=text name="userfile3_url" value="<?=$userfile3_url?>" style="WIDTH: 400px; display:none" class="input">
				<span class="font_orange">(권장이미지 : 296X227)</font>
				<input type=hidden name=setcolor value="<?=$setcolor?>">
				<input type=hidden name="vimage3" value="<?=$_data->tinyimage?>">
<?
			if ($_data) {
				if (strlen($_data->tinyimage)>0 && file_exists($imagepath.$_data->tinyimage)==true){
					echo "<br><img src='".$imagepath.$_data->tinyimage."' height=70 width=120 border=1 alt='URL : http://".$_ShopInfo->getShopurl().DataDir."product/".$_data->tinyimage."'>";
					echo "&nbsp;<a href=\"JavaScript:DeletePrdtImg('3')\"><img src=\"images/icon_del1.gif\" align=bottom border=0></a>";
				} else {
					echo "<br><img src=images/space01.gif>";
				}
			}
?>
				</TD>
			</TR>
			<tr>
				<TD class="td_con_orange" colspan="4">
				<table cellpadding="0" cellspacing="0" width="100%">
				<col width=160></col>
				<col width=></col>
				<tr>
					<td><B><span class="font_orange">상품 상세내역 입력</span></B></td>
					<td><? if($predit_type=="Y"){?><input type=radio id="idx_checkedit1" name=checkedit checked onclick="JavaScript:htmlsetmode('wysiwyg',this)"><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_checkedit1>웹편집기로 입력하기(권장)</label> &nbsp;&nbsp; <input type=radio id="idx_checkedit2" name=checkedit onclick="JavaScript:htmlsetmode('textedit',this);"><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_checkedit2>직접 HTML로 입력하기</label><? }?></td>
				</tr>
				</table>
				</TD>
			</tr>
			<tr>
				<TD colspan="4">
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td><textarea wrap=off style="WIDTH: 100%; HEIGHT: 300px" name=content><?=htmlspecialchars($_data->content)?></textarea></td>
				</tr>
				</table>
				</TD>
			</tr>
			<tr>
				<td colspan="4"><img id="size_checker" style="display:none;"><img id="size_checker2" style="display:none;"><img id="size_checker3" style="display:none;"></td>
			</tr>
			<TR>
				<TD colspan=4 background="images/table_top_line.gif"></TD>
			</TR>
			<tr>
				<TD class="table_cell"><img src="images/icon_point5.gif" width="8" height="11" border="0">옵션 타입 선택</TD>
				<TD class="td_con1" colspan=3>
				<input type=radio id="idx_searchtype0" name=searchtype style="border:none" onclick="ViewLayer('layer0')" value="0" <?if($searchtype=="0") echo "checked";?><?=($_data->assembleuse=="Y"?" disabled":"")?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_searchtype0>옵션정보 없음</label>
				<img width=10 height=0>
				<input type=radio id="idx_searchtype1" name=searchtype style="border:none" onclick="ViewLayer('layer1');alert('옵션1과 옵션2는 각각 최대 10개로\n각 옵션별 수량조절이 가능하게 됩니다.\n수정시 기존의 그이상의 옵션들은 삭제됩니다.');" value="1" <?if($searchtype=="1") echo "checked";?><?=($_data->assembleuse=="Y"?" disabled":"")?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_searchtype1>상품 옵션 + <font color=#FF0000>재고관리</font></label> 
				<img width=10 height=0>
				<input type=radio id="idx_searchtype2" name=searchtype style="border:none" onclick="ViewLayer('layer2')" value="2" <?if($searchtype=="2") echo "checked";?><?=($_data->assembleuse=="Y"?" disabled":"")?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_searchtype2>상품 옵션 무제한 등록</label>
				<?//if($gongtype=="N" && (int)$_data->vender==0){
				if($gongtype=="N"){?>
				<img width=10 height=0>
				<input type=radio id="idx_searchtype3" name=searchtype style="border:none" onclick="ViewLayer('layer3')" value="3" <?if($searchtype=="3") echo "checked";?><?=($_data->assembleuse=="Y"?" disabled":"")?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_searchtype3>옵션그룹</label>
				<?}?>
				</td>
			</tr>
			<tr>
				<td colspan=4>
				<div id=layer0 style="margin-left:0;display:hide; display:block ;border-style:solid; border-width:0; border-color:black;background:#FFFFFF;padding:0;">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<TD colspan=2 background="images/table_top_line.gif"></TD>
				</TR>
				</TABLE>
				</div>		
				<div id=layer1 style="margin-left:0;display:hide; display:none ;border-style:solid; border-width:0; border-color:black;background:#FFFFFF;padding:0;">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<col width=160></col>
				<col width=></col>
				<TR>
					<TD colspan=2 background="images/table_top_line.gif"></TD>
				</TR>
	<?
				$optionarray1=explode(",",$_data->option1);
				$option_price=explode(",",$_data->option_price);
				$optionarray2=explode(",",$_data->option2);
				$option_quantity_array=explode(",",$_data->option_quantity);
				$optnum1=count($optionarray1)-1; 
				$optnum2=count($optionarray2)-1;

				$optionover="NO";
				if($optnum1>10){
					$optnum1=10;
					$optionover="YES";
				}
				if($optnum2>10){
					$optnum2=10;
					$optionover="YES";
				}
				if($optnum1>0 && strlen($_data->option_quantity)==0) $optionover="YES";
				if($optnum2<=1) $optnum2=1;
	?>
				<tr>
					<TD class="table_cell"><img src="images/icon_point5.gif" width="8" height="11" border="0">상품옵션 속성명</TD>
					<TD class="td_con1"><b>옵션1 속성명</b><B> :<FONT color=#ff6000> </B></FONT><input name=option1_name value="<? if (strlen($_data->option1)>0) echo htmlspecialchars($optionarray1[0]); ?>" size=20 maxlength=20 class="input">&nbsp;&nbsp;&nbsp;&nbsp;<b>옵션2 속성명</b><B> :<FONT color=#128c02> </B></FONT><input name=option2_name value="<? if (strlen($_data->option2)>0) echo htmlspecialchars($optionarray2[0]); ?>" size=20 maxlength=20 class="input"></TD>
				</tr>
				<TR>
					<TD colspan=2 background="images/table_top_line.gif"></TD>
				</TR>
				<TR>
					<TD colspan="2" style="padding-top:3pt; padding-bottom:3pt;">
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
						1) 옵션가격 입력시 판매가격은 무시되고 옵션가격으로 구매가 진행됩니다.<br>
						2) 판매상품 품절일 경우 옵션 재고수량이 남아 있더라도 상품구매는 진행되지 않습니다.<br>
						&nbsp;<b>&nbsp;&nbsp;</b>옵션 재고수량으로만 상품 관리를 할 경우 판매상품 재고수량을 무제한으로 설정해 주세요.<br>
						3) 옵션 재고수량 미입력시 옵션 재고수량은 무제한 상태가 되며 "0" 입력시 옵션 재고수량은 품절 상태가 됩니다.</TD>
						<TD background="images/distribute_07.gif"></TD>
					</TR>
					<TR>
						<TD><IMG SRC="images/distribute_08.gif"></TD>
						<TD COLSPAN=2 background="images/distribute_09.gif"></TD>
						<TD><IMG SRC="images/distribute_10.gif"></TD>
					</TR>
					</TABLE>
					</TD>
				</TR>
				<TR>
					<TD colspan="2">
					<TABLE cellSpacing=0 cellPadding=0 width="754px" bgColor=#ffffff border=0>
					<TR>
						<TD width="80px" bgColor="#F9F9F9">
						<TABLE cellSpacing=0 cellPadding=0 border=0>
						<TR bgColor=#FF7100 height=2>
							<TD noWrap width=2></TD>
							<TD noWrap width=2></TD>
							<TD width="100%"></TD>
							<TD noWrap width=2></TD>
							<TD noWrap width=2></TD>
						</TR>
						<TR height=50>
							<TD bgColor=#FF7100 rowSpan=25></TD>
							<TD rowSpan=25></TD>
							<TD align=middle><B>옵션1 속성</B></TD>
							<TD rowSpan=25></TD>
							<TD bgColor=#FF7100 rowSpan=25></TD>
						</TR>
						<TR bgColor=#dadada height=1>
							<TD></TD>
						</TR>
						<TR height=1>
							<TD></TD>
						</TR>
	<?
					for($i=1;$i<=10;$i++){
						if($i==6) echo "<tr height=5><td></td></tr>";
						echo "<tr height=7><td></td></tr>";
						echo "<tr height=19><TD align=middle><input type=text name=optname1 value=\"".trim(htmlspecialchars($optionarray1[$i]))."\" size=8 class=\"input\"></td></tr>";
					}
					echo "<tr height=2><td></td></tr>";
					echo "<tr height=2><td colspan=5 bgcolor=#FF7100></td></tr>";
	?>
						</TABLE>
						</TD>
						<TD width="80px" bgColor="#F9F9F9">
						<TABLE cellSpacing=0 cellPadding=0 border=0>
						<TR bgColor=#0071C3 height=2>
							<TD noWrap width=2></TD>
							<TD noWrap width=2></TD>
							<TD width="100%"></TD>
							<TD noWrap width=2></TD>
							<TD noWrap width=2></TD>
						</TR>
						<TR height=50>
							<TD bgColor=#0071C3 rowSpan=25></TD>
							<TD rowSpan=25></TD>
							<TD align=middle><B>가격</B></TD>
							<TD rowSpan=25></TD>
							<TD bgColor=#0071C3 rowSpan=25></TD>
						</TR>
						<TR bgColor=#dadada height=1>
							<TD></TD>
						</TR>
						<TR height=1>
							<TD></TD>
						</TR>
	<?
					for($i=0;$i<10;$i++){
						if($i==5) echo "<tr height=5><td></td></tr>";
						echo "<tr height=7><td></td></tr>";
						echo "<tr height=21><td align=center><input type=text name=optprice size=8 ";
						echo " value=\"".$option_price[$i]."\" ";
						echo "onkeyup=\"strnumkeyup(this)\" class=\"input\"></td></tr>";
					}
					echo "<tr height=2><td></td></tr>";
					echo "<tr height=2><td colspan=5 bgcolor=#0071C3></td></tr>";
	?>
						</TABLE>
						</TD>
						<TD vAlign=top width="585px" bgColor=#ffffff>
						<TABLE cellSpacing=0 cellPadding=0 border=0>
						<TR bgColor=#57B54A height=2>
							<TD width=2 rowSpan=4></TD>
							<TD width=2></TD>
							<TD width=80></TD>
							<TD width=80></TD>
							<TD width=80></TD>
							<TD width=80></TD>
							<TD width=80></TD>
							<TD width=80></TD>
							<TD width=80></TD>
							<TD width=80></TD>
							<TD width=80></TD>
							<TD width=80></TD>
							<TD width=2></TD>
							<TD width=2 rowSpan=4></TD>
						</TR>
						<TR bgColor=#f1ffef height=27>
							<TD width=2 rowspan="2"></TD>
							<TD align=middle colSpan=10 bgcolor="#F9F9F9"><b>옵션2 속성</b></TD>
							<TD width=2 rowspan="2"></TD>
						</TR>
						<TR bgColor=#f1ffef height=19>
	<? 
						for($i=1;$i<=10;$i++){
							echo "<TD align=middle width=\"20%\" bgcolor=\"#F9F9F9\"><input type=text name=optname2 value=\"".htmlspecialchars($optionarray2[$i])."\" size=8 class=\"input\"></td>";
						}
	?>
						</TR>
						<TR bgColor=#F9F9F9 height=4>
							<TD colSpan=12></TD>
						</TR>
						<TR bgColor=#57B54A height=2>
							<TD colSpan=14></TD>
						</TR>
						<TR height=6>
							<TD colSpan=2 rowSpan="22"></TD>
							<TD colSpan=10></TD>
							<TD colSpan=2 rowSpan="22"></TD>
						</TR>
	<?
					for($i=0;$i<10;$i++){
						if($i!=0 && $i!=5) echo "<tr><td colspan=10 height=7></td></tr>";
						else if($i==5) echo "<tr><td colspan=10 height=6></td></tr>
											<tr><td colspan=10 height=1 bgcolor=#DADADA></td></tr>
											<tr><td colspan=10 height=5></td></tr>";
						echo "<tr height=19>";
						for($j=0;$j<10;$j++){
							echo "<TD align=middle><input type=text name=optnumvalue[".$j."][".$i."] value=\"".$option_quantity_array[$j*10+$i+1]."\" size=8 maxlength=3 onkeyup=\"strnumkeyup(this)\" class=\"input\"></TD>\n";
						}
						echo "</tr>";
					}
	?>
						</TABLE>
						</TD>
					</TR>
					</TABLE>
					</TD>
				</TR>
				<tr><td colspan=2 height=5></td></tr>
				<TR>
					<TD colspan=2 background="images/table_top_line.gif"></TD>
				</TR>
				</table>
				</div>
				
				<div id=layer2 style="margin-left:0;display:hide; display:none ;border-style:solid; border-width:0; border-color:black;background:#FFFFFF;padding:0;">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<col width=100></col>
				<col width=></col>
				<TR>
					<TD colspan=2 background="images/table_top_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell"><img src="images/icon_point5.gif" width="8" height="11" border="0">옵션1</TD>
					<TD class="td_con1">
	<?
					$option1="";
					$optname1="";
					if ($_data) {
						if (strlen($_data->option1)>0) {
							$tok = strtok($_data->option1,",");
							$optname1=$tok;
							$tok = strtok("");
							$option1=$tok;
						}
					}
	?>
					<TABLE cellSpacing=0 cellPadding=0 border=0 width="100%">
					<col width=76></col>
					<col width=></col>
					<TR>
						<TD>1)속성명</TD>
						<TD style="PADDING-LEFT: 5px"><input name=toptname1 value="<? if ($_data && strlen($_data->option1)>0) echo $optname1; ?>" size=50 maxlength=20 class="input"></TD>
					</TR>
					<TR>
						<TD>2)속성</TD>
						<TD style="PADDING-LEFT: 5px"><input name=toption1 value="<? if ($_data && strlen($_data->option1)>0) echo htmlspecialchars($option1); ?>" size=90  class="input"></TD>
					</TR>
					<TR>
						<TD style="PADDING-LEFT: 3px" colSpan=2>* 옵션의 속성명으로 색상 또는 사이즈 또는 용량 등을 입력해서 사용하세요.<br>* 속성은 속성명에 대한 세부내용을 입력합니다.<br>&nbsp;&nbsp;&nbsp;예)빨강,파랑,노랑 또는 95,100,105 와 같이 컴마(,)로 구분하여 공백없이 입력합니다.</TD>
					</TR>
					</TABLE>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell"><img src="images/icon_point5.gif" width="8" height="11" border="0">옵션1 가격</TD>
					<TD class="td_con1">
						<input name=toption_price value="<? if ($_data) echo $_data->option_price; ?>" size=100  class="input"><br><span class="font_orange"><b>예) 1000,2000,3000</b></span><br>
						* 옵션1 가격 입력시 판매가격은 무시됩니다.<br>
						* 옵션1 가격 입력시 판매가격 대신 첫번째 가격이 판매가격으로 사용됩니다.<br>
						* 카테고리내 상품 출력시 "판매가격 (기본가)"로 표기 됩니다.<br>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<tr>
					<TD class="table_cell"><img src="images/icon_point5.gif" width="8" height="11" border="0">옵션2</TD>
					<TD class="td_con1">
	<?
				$option2="";
				$optname2="";
				if ($_data) {
					if (strlen($_data->option2)>0) {
						$tok = strtok($_data->option2,",");
						$optname2=$tok;
						$tok = strtok("");
						$option2=$tok;
					}
				}
	?>
					<TABLE cellSpacing=0 cellPadding=0 border=0 width="100%">
					<col width=76></col>
					<col width=></col>
					<TR>
						<TD>1)속성명</TD>
						<TD style="PADDING-LEFT: 5px"><input name=toptname2 value="<? if ($_data && strlen($_data->option2)>0) echo $optname2; ?>" size=50 maxlength=20 class="input"></TD>
					</TR>
					<TR>
						<TD>2)속성</TD>
						<TD style="PADDING-LEFT: 5px"><input name=toption2 value="<? if ($_data && strlen($_data->option2)>0) echo htmlspecialchars($option2); ?>" size=90  class="input"></TD>
					</TR>
					<TR>
						<TD style="PADDING-LEFT: 3px" colSpan=2>* 옵션1 등록 방법과 같으나 "<B>옵션1 가격</B>"과는 무관합니다.</TD>
					</TR>
					</TABLE>
					</TD>
				</tr>
				<TR>
					<TD colspan=2 background="images/table_top_line.gif"></TD>
				</TR>
				</table>
				</div>

				<div id=layer3 style="margin-left:0;display:hide; display:none ;border-style:solid; border-width:0; border-color:black;background:#FFFFFF;padding:0;">
				<?//if($gongtype=="N" && (int)$_data->vender==0){
				if($gongtype=="N"){?>
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
					<col width=100></col>
					<col width=></col>
				<TR>
					<TD colspan=2 background="images/table_top_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell"><img src="images/icon_point5.gif" width="8" height="11" border="0">옵션그룹 선택</TD>
					<TD class="td_con1">
					<select name=optiongroup style="width: 70%" class="select">
	<?
				$sqlopt = "SELECT option_code,description FROM tblproductoption ";
				$resultopt = mysql_query($sqlopt,get_db_conn());
				$optcnt=0;
				while($rowopt = mysql_fetch_object($resultopt)){
					if($optcnt++==0) echo "<option value=0>옵션그룹을 선택하세요.";
					echo "<option value=\"".$rowopt->option_code."\"";
					if($optcode==$rowopt->option_code) echo " selected";
					echo ">".$rowopt->description."</option>";
				}
				mysql_free_result($resultopt);
				if($optcnt==0) echo "<option value=0>등록하신 옵션그룹이 없습니다.</option>";
	?>
					</select>
					<?if($popup!="YES"){?><A HREF="javascript:parent.location='product_option.php';"><B><img src="images/btn_option.gif" width="105" height="18" border="0" hspace="2" align=absmiddle></B></A><?}?>
					<?if($optcnt==0) echo "<script>document.form1.optiongroup.disabled=true;</script>";?>
					
					<br>* (상품가격+옵션) 변경가격 사용시 옵션그룹을 이용해 주세요.
					<br>* 옵션그룹 사용시 옵션1과 옵션2는 자동 삭제됩니다.
					<br>* 옵션그룹 선택시 해당 옵션그룹에 등록된 상품옵션을 확인할 수 있습니다.
					</TD>
				</TR>
				<TR>
					<TD colspan=2 background="images/table_top_line.gif"></TD>
				</TR>
				</TABLE>
				<?}?>
				</div>
				</td>
			</tr>
			<TR>
				<TD class="table_cell"><img src="images/icon_point5.gif" width="8" height="11" border="0">상품진열여부</TD>
				<TD class="td_con1" colspan="3"><input type=radio id="idx_display1" name=display value="Y" <? if ($_data) { if ($_data->display=="Y") echo "checked"; } else echo "checked";  ?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_display1>진열함</label> &nbsp; <input type=radio id="idx_display2" name=display value="N" <? if ($_data) { if ($_data->display=="N") echo "checked"; } ?> onclick="JavaScript:alert('메인 화면의 상품 특징이 없음으로 변경됩니다.')"><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_display2>진열안함</label></TD>
			</TR>
			<TR>
				<TD colspan="4" background="images/table_con_line.gif"></TD>
			</TR>
<?
if($sns_ok == "Y"){
?>
			<TR>
				<TD colspan="4" background="images/table_con_line.gif"></TD>
			</TR>
			<TR>
				<TD class="table_cell"><img src="images/icon_point5.gif" width="8" height="11" border="0">SNS 사용여부</TD>
				<TD class="td_con1"><input type=radio id="sns_state1" name=sns_state value="Y" <? if ($_data) { if ($_data->sns_state=="Y") echo "checked"; }  ?> onclick="ViewSnsLayer('block')" ><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=sns_state1>사용함</label> &nbsp; <input type=radio id="sns_state2" name=sns_state value="N" <? if ($_data) { if ($_data->sns_state !="Y") echo "checked"; } else echo "checked"; ?> onclick="ViewSnsLayer('none')" ><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=sns_state2>사용안함</label></TD>
			</TR>
<?
	if($arSnsType[0] =="B"){
?>
			<tr id ="sns_optionWrap" style="display:none;">
				<td colspan="4">
					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
					<col width=140></col>
					<col></col>
					<col width=140></col>
					<col></col>
					<tr>
					<TD class="table_cell"><img src="images/icon_point5.gif" width="8" height="11" border="0">추천인 적립금(률)</TD>
					<TD class="td_con1"><input name=sns_reserve1 value="<?=$_data->sns_reserve1?>" size=10 maxlength=6 class="input" style="width:45%" onKeyUP="chkFieldMaxLenFunc(this.form,this.form.sns_reserve1_type.value);"> <select name="sns_reserve1_type" class="select" onchange="chkFieldMaxLenFunc(this.form,this.value);"><option value="N"<?=($_data->sns_reserve1_type!="Y"?" selected":"")?>>적립금(￦)</option><option value="Y"<?=($_data->sns_reserve1_type!="Y"?"":" selected")?>>적립률(%)</option></select>
					</TD>
					<TD class="table_cell" style="border-left-width:1pt; border-color:rgb(227,227,227); border-top-style:none; border-right-style:none; border-bottom-style:none; border-left-style:solid;"><img src="images/icon_point5.gif" width="8" height="11" border="0">비추천인 적립금(률)</TD>
					<TD class="td_con1"><input name=sns_reserve2 value="<?=$_data->sns_reserve2?>" size=10 maxlength=6 class="input" style="width:45%" onKeyUP="chkFieldMaxLenFunc(this.form,this.form.sns_reserve2_type.value);"> <select name="sns_reserve2_type" class="select" onchange="chkFieldMaxLenFunc(this.form,this.value);"><option value="N"<?=($_data->sns_reserve2_type!="Y"?" selected":"")?>>적립금(￦)</option><option value="Y"<?=($_data->sns_reserve2_type!="Y"?"":" selected")?>>적립률(%)</option></select>
					</TD>
					</tr>
					</table>
				</TD>
			</tr>
<?	}
}?>
			<TR>
				<TD colspan="4" background="images/table_con_line.gif"></TD>
			</TR>
			<TR>
				<TD class="table_cell"><img src="images/icon_point5.gif" width="8" height="11" border="0">선물하기 사용여부</TD>
				<TD class="td_con1" colspan="3"><input type=radio id="present_state1" name=present_state value="Y" <? if ($_data) { if ($_data->present_state=="Y") echo "checked"; } ?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=present_state1>사용함</label> &nbsp; <input type=radio id="present_state2" name=present_state value="N" <? if ($_data) { if ($_data->present_state!="Y") echo "checked"; } else echo "checked"; ?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=present_state2>사용안함</label></TD>
			</TR>
			<TR>
				<TD colspan="4" background="images/table_con_line.gif"></TD>
			</TR>
			<TR>
				<TD class="table_cell"><img src="images/icon_point5.gif" width="8" height="11" border="0">조르기 사용여부</TD>
				<TD class="td_con1" colspan="3"><input type=radio id="pester_state1" name=pester_state value="Y" <? if ($_data) { if ($_data->pester_state=="Y") echo "checked"; } ?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=pester_state1>사용함</label> &nbsp; <input type=radio id="pester_state2" name=pester_state value="N" <? if ($_data) { if ($_data->pester_state!="Y") echo "checked"; } else echo "checked";  ?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=pester_state2>사용안함</label></TD>
			</TR>
<?
if($arRecomType[0] =="B" && $arRecomType[1] == "B"){
?>
			<TR>
				<TD colspan="4" background="images/table_con_line.gif"></TD>
			</TR>
			<TR>
				<TD class="table_cell"><img src="images/icon_point5.gif" width="8" height="11" border="0">첫구매시 추천인 적립</TD>
				<TD class="td_con1" colspan="3"><input name=first_reserve value="<?=$_data->first_reserve?>" size=10 maxlength=6 class="input" style="width:20%" onKeyUP="chkFieldMaxLenFunc(this.form,this.form.first_reserve_type.value);"> <select name="first_reserve_type" class="select" onchange="chkFieldMaxLenFunc(this.form,this.value);"><option value="N"<?=($_data->first_reserve_type!="Y"?" selected":"")?>>적립금(￦)</option><option value="Y"<?=($_data->first_reserve_type!="Y"?"":" selected")?>>적립률(%)</option></select><font color="#ff0000">*sns홍보를 통해 첫구매에도 적용</span>
				</TD>
			</TR>
<?}?>
			<TR>
				<TD colspan="4" background="images/table_con_line.gif"></TD>
			</TR>
			<TR>
				<TD class="table_cell"><img src="images/icon_point5.gif" width="8" height="11" border="0">재고노출여부</TD>
				<TD class="td_con1" colspan="3"><input type=radio id="stock_state1" name=stock_state value="Y" <? if ($_data) { if ($_data->stock_state=="Y") echo "checked"; } ?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=stock_state1>사용함</label> &nbsp; <input type=radio id="stock_state2" name=stock_state value="N" <? if ($_data) { if ($_data->stock_state=="N") echo "checked"; }else echo "checked";   ?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=stock_state2>사용안함</label></TD>
			</TR>
			<TR>
				<TD colspan="4" background="images/table_con_line.gif"></TD>
			</TR>
			<TR>
				<TD class="table_cell"><img src="images/icon_point5.gif" width="8" height="11" border="0">할인율 노출여부</TD>
				<TD class="td_con1" colspan="3"><input type=radio id="discount_state1" name=discount_state value="Y" <? if ($_data) { if ($_data->discount_state=="Y") echo "checked"; }?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=discount_state1>사용함</label> &nbsp; <input type=radio id="discount_state2" name=discount_state value="N" <? if ($_data) { if ($_data->discount_state=="N") echo "checked"; }  else echo "checked";  ?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=discount_state2>사용안함</label></TD>
			</TR>
			<TR>
				<TD colspan="4" background="images/table_top_line.gif"></TD>
			</TR>
			<TR>
				<TD class="table_cell"><img src="images/icon_point5.gif" width="8" height="11" border="0">공구신청상품</TD>
				<TD class="td_con1" colspan="3"><input type=radio id="gonggu_product1" name="gonggu_product" value="Y" <? if ($_data) { if ($_data->gonggu_product=="Y") echo "checked"; } else echo "checked";  ?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=gonggu_product1>진열함</label> &nbsp; <input type=radio id="gonggu_product2" name="gonggu_product" value="N" <? if ($_data) { if ($_data->gonggu_product=="N") echo "checked"; } ?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=gonggu_product2>진열안함</label></TD>
			</TR>
			<TR>
				<TD colspan="4" background="images/table_top_line.gif"></TD>
			</TR>
			<TR>
				<TD colspan="4" height="30">&nbsp;</TD>
			</TR>
			<tr>
				<td colspan="3" align="center">
					<? if (strlen($prcode)==0) { ?>
							<a href="javascript:CheckForm('insert');"><img src="images/btn_new.gif" align=absmiddle width="144" height="38" border="0" vspace="5"></a>
					<? } else {?>
							<a href="javascript:CheckForm('update');"><B><img src="images/btn_infoedit.gif" align=absmiddle width="162" height="38" border="0" vspace="5"></B></a>
							&nbsp;
							<a href="javascript:PrdtDelete();"><B><img src="images/btn_infodelete.gif" align=absmiddle width="113" height="38" border="0" vspace="5"></B></a>
					<? }?>
				</td>
				<td align="right">
					<? if (strlen($prcode)>0) { ?>
							<a href="JavaScript:NewPrdtInsert()"  onMouseOver="window.status='신규입력';return true;"><img src="images/product_newregicn.gif" align=absmiddle border="0" width="142" height="38" vspace="5"></a>
					<? } ?>
				</td>
			</tr>
			</TABLE>
			</td>
		</tr>
		</form>
		</table>

		</td>
	</tr>
	<tr>
		<td style="text-align:right">
		<form name=cForm action="<?=$_SERVER[PHP_SELF]?>" method=post>
		<input type=hidden name=mode>
		<input type=hidden name=code value=<?=$code?>>
		<input type=hidden name=prcode value=<?=$prcode?>>
		<input type=hidden name=delprdtimg>
		<input type=hidden name="vimage" value="<? if ($_data) echo $_data->maximage; ?>">
		<input type=hidden name="vimage2" value="<? if ($_data) echo $_data->minimage; ?>">
		<input type=hidden name="vimage3" value="<? if ($_data) echo $_data->tinyimage; ?>">
		</form>
		<form name=form_list action="<?=$_SERVER[PHP_SELF]?>" method=post>
		<input type=hidden name=mode value="list">
		<input type=hidden name=prcode>
		<input type=hidden name=code value="<?=$code?>">
		<input type=hidden name=sort value="<?=$sort?>">
		<input type=hidden name=block value="<?=$block?>">
		<input type=hidden name=gotopage value="<?=$gotopage?>">
		<input type=hidden name=keyword value="<?=$keyword?>">
		<input type="submit" value="상품목록" >
		</form>			
		</td>
	</tr>
	</table>
	</td>
</tr>
</table>

<?
if (strlen($code)==12 && $predit_type=="Y") {
?>
<script language="Javascript1.2" src="htmlarea/editor.js"></script>
<script language="JavaScript">
function htmlsetmode(mode,i){
	if(mode==document.form1.htmlmode.value) {
		return;
	} else {
		i.checked=true;
		editor_setmode('content',mode);
	}
	document.form1.htmlmode.value=mode;
} 
_editor_url = "htmlarea/";
editor_generate('content');
</script>
<?
}
?>

<SCRIPT LANGUAGE="JavaScript">
<!--
function CheckForm(mode) {
	if (document.form1.productname.value.length==0) {
		alert("상품명을 입력하세요.");
		document.form1.productname.focus();
		return;
	}
	if (CheckLength(document.form1.productname)>100) {
		alert('총 입력가능한 길이가 한글 50자까지입니다. 다시한번 확인하시기 바랍니다.');
		document.form1.productname.focus();
		return;
	}
	if (document.form1.sellprice.value.length==0) {
		alert("판매가격을 입력하세요.");
		document.form1.sellprice.focus();
		return;
	}
	if (isNaN(document.form1.sellprice.value)) {
		alert("판매가격을 숫자로만 입력하세요.(콤마제외)");
		document.form1.sellprice.focus();
		return;
	}
	if (document.form1.consumerprice.value.length==0) {
		alert("원래가격을 입력하세요.");
		document.form1.consumerprice.focus();
		return;
	}
	if (isNaN(document.form1.consumerprice.value)) {
		alert("원래가격을 숫자로만 입력하세요.(콤마제외)");
		document.form1.consumerprice.focus();
		return;
	}
	if (Number(document.form1.consumerprice.value) <=0) {
		alert("원래가격을 0보다 크게 입력하세요");
		document.form1.consumerprice.focus();
		return;
	}

	if (document.form1.reserve.value.length>0) {
		if(document.form1.reservetype.value=="Y") {
			if(isDigitSpecial(document.form1.reserve.value,".")) {
				alert("적립률은 숫자와 특수문자 소수점\(.\)으로만 입력하세요.");
				document.form1.reserve.focus();
				return;
			}
			
			if(getSplitCount(document.form1.reserve.value,".")>2) {
				alert("적립률 소수점\(.\)은 한번만 사용가능합니다.");
				document.form1.reserve.focus();
				return;
			}

			if(getPointCount(document.form1.reserve.value,".",2)==true) {
				alert("적립률은 소수점 이하 둘째자리까지만 입력 가능합니다.");
				document.form1.reserve.focus();
				return;
			}

			if(Number(document.form1.reserve.value)>100 || Number(document.form1.reserve.value)<0) {
				alert("적립률은 0 보다 크고 100 보다 작은 수를 입력해 주세요.");
				document.form1.reserve.focus();
				return;
			}
		} else {
			if(isDigitSpecial(document.form1.reserve.value,"")) {
				alert("적립금은 숫자로만 입력하세요.");
				document.form1.reserve.focus();
				return;
			}
		}
	}

	if (document.form1.checkquantity[2].checked==true) {
		if (document.form1.quantity.value.length==0) {
			alert("수량을 입력하세요.");
			document.form1.quantity.focus();
			return;
		} else if (isNaN(document.form1.quantity.value)) {
			alert("수량을 숫자로만 입력하세요.");
			document.form1.quantity.focus();
			return;
		} else if (parseInt(document.form1.quantity.value)<=0) {
			alert("수량은 0개이상이여야 합니다.");
			document.form1.quantity.focus();
			return;
		}
	}
	miniq_obj=document.form1.miniq;
	maxq_obj=document.form1.maxq;
	if (miniq_obj.value.length>0) {
		if (isNaN(miniq_obj.value)) {
			alert ("최소주문한도는 숫자로만 입력해 주세요.");
			miniq_obj.focus();
			return;
		}
	}
	if (document.form1.checkmaxq[1].checked==true) {
		if (maxq_obj.value.length==0) {
			alert ("최대주문한도의 수량을 입력해 주세요.");
			maxq_obj.focus();
			return;
		} else if (isNaN(maxq_obj.value)) {
			alert ("최대주문한도의 수량을 숫자로만 입력해 주세요.");
			maxq_obj.focus();
			return;
		}
	}
	if (miniq_obj.value.length>0 && document.form1.checkmaxq[1].checked==true && maxq_obj.value.length>0) {
		if (parseInt(miniq_obj.value) > parseInt(maxq_obj.value)) {
			alert ("최소주문한도는 최대주문한도 보다 작아야 합니다.");
			miniq_obj.focus();
			return;
		}
	}

	if (document.form1.complete_quantity.value.length==0) {
		alert("구매달성수량을 입력하세요.");
		document.form1.complete_quantity.focus();
		return;
	}
	if (isNaN(document.form1.complete_quantity.value)) {
		alert("구매달성수량을 숫자로만 입력하세요.(콤마제외)");
		document.form1.complete_quantity.focus();
		return;
	}

	if(document.form1.deli[3].checked==true || document.form1.deli[4].checked==true) {
		if(document.form1.deli[3].checked==true)
		{
			if (document.form1.deli_price_value1.value.length==0) {
				alert("개별배송비를 입력하세요.");
				document.form1.deli_price_value1.focus();
				return;
			} else if (isNaN(document.form1.deli_price_value1.value)) {
				alert("개별배송비는 숫자로만 입력하세요.");
				document.form1.deli_price_value1.focus();
				return;
			} else if (parseInt(document.form1.deli_price_value1.value)<=0) {
				alert("개별배송비는 0원 이상 입력하셔야 합니다.");
				document.form1.deli_price_value1.focus();
				return;
			}
		}
		else
		{
			if (document.form1.deli_price_value2.value.length==0) {
				alert("개별배송비를 입력하세요.");
				document.form1.deli_price_value2.focus();
				return;
			} else if (isNaN(document.form1.deli_price_value2.value)) {
				alert("개별배송비는 숫자로만 입력하세요.");
				document.form1.deli_price_value2.focus();
				return;
			} else if (parseInt(document.form1.deli_price_value2.value)<=0) {
				alert("개별배송비는 0원 이상 입력하셔야 합니다.");
				document.form1.deli_price_value2.focus();
				return;
			}
		}
	}

	//옵션체크 ==============================================================================
	if(shop=="layer0") {

	} else if(shop=="layer1"){
		optnum1=0;
		optnum2=0;

		//옵션1 항목
		document.form1.option1.value="";
		for(i=0;i<10;i++){
			if(document.form1.optname1[i].value.length>0) {
				document.form1.option1.value+=document.form1.optname1[i].value+",";
				optnum1++;
			}
		}

		//옵션1 제목 검사 (옵션1 항목이 NULL이 아니면)
		if((document.form1.option1.value.length!=0 && document.form1.option1_name.value.length==0)
		|| (document.form1.option1.value.length==0 && document.form1.option1_name.value.length!=0)){
			alert('각 옵션별 조건입력과 [옵션제목]을 확인해주세요!');
			if(document.form1.option1_name.value.length==0) {
				document.form1.option1_name.focus();
			} else {
				document.form1.optname1[0].focus();
			}
			return;
		}

		//옵션2 항목
		document.form1.option2.value="";
		for(i=0;i<10;i++){
			if(document.form1.optname2[i].value.length>0) {
				document.form1.option2.value+=document.form1.optname2[i].value+",";
				optnum2++;
			}
		}

		//옵션2 제목 검사 (옵션2 항목이 NULL이 아니면)
		if((document.form1.option2.value.length!=0 && document.form1.option2_name.value.length==0)
		|| (document.form1.option2.value.length==0 && document.form1.option2_name.value.length!=0)){
			alert('각 옵션별 조건입력과 [옵션제목]을 확인해주세요!');
			if(document.form1.option2_name.value.length==0) {
				document.form1.option2_name.focus();
			} else {
				document.form1.optname2[0].focus();
			}
			return;
		}

		//옵션2만 입력했는지 검사
		if(document.form1.option1.value.length==0 && document.form1.option2.value.length>0) {
			alert('옵션2는 옵션1 입력후 입력가능합니다.');
			document.form1.option1_name.focus();
			return;
		}

		//옵션1에 따른 가격 검사
		document.form1.option_price.value="";
		pricecnt=0;
		for(i=0;i<optnum1;i++){
			if(document.form1.optprice[i].value.length==0){
				pricecnt++;
			}else{
				document.form1.option_price.value+=document.form1.optprice[i].value+",";
			}
		}
		if(optnum1>0 && pricecnt!=0 && pricecnt!=optnum1){
			alert('옵션별 가격은 모두 입력하거나 모두 입력하지 않아야 합니다.');
			document.form1.optprice[0].focus();
			return;
		}

		if(document.form1.option_price.value.length!=0) temp=0;
		else temp=-1;
		temp2=document.form1.option_price.value;
		while(temp!=-1){
			temp=temp2.indexOf(",");
			if(temp!=-1) temp3=(temp2.substring(0,temp));
			else temp3=temp2;
			if(isNaN(temp3)){
				alert("옵션 가격은 숫자만 입력을 하셔야 합니다.");
				document.form1.option_price.focus();
				return;
			}
			temp2=temp2.substring(temp+1);
		}

		//재고수량 및 숫자검사
		isquan=false;
		quanobj="";
		for(i=0;i<10;i++) {
			isgbn1=false;
			if(i<optnum1) isgbn1=true;

			for(j=0;j<10;j++) {
				isgbn2=false;
				if(optnum2>0) {
					if(j<optnum2 && isgbn1==true) isgbn2=true;
				} else {
					if(j==0 && isgbn1==true) isgbn2=true;
				}

				if(isgbn2==true) {
					if(isquan==false && document.form1["optnumvalue["+j+"]["+i+"]"].value.length==0) {
						isquan=true;
						quanobj=document.form1["optnumvalue["+j+"]["+i+"]"];
					}
				} else {
					if(document.form1["optnumvalue["+j+"]["+i+"]"].value.length>0) {
						alert("입력하신 수량이 옵션정보의 범위를 넘었습니다. ("+(i+1)+" 째줄 "+(j+1)+" 째칸)");
						document.form1["optnumvalue["+j+"]["+i+"]"]. focus();
						return;
					}
				}
			}
		}
		if(isquan==true) {
			if(!confirm("수량 입력이 안된 옵션정보는 무제한 수량으로 등록됩니다.\n\n계속 하시겠습니까?")) {
				quanobj.focus();
				return;
			}
		}

	} else if(shop=="layer2"){
		if (document.form1.toption_price.value.length!=0 && document.form1.toption1.value.length==0) {
			alert("특수코드별가격을 입력하면 반드시 특수코드입력1에도 내용을 입력해야 합니다.");
			document.form1.toption1.focus();
			return;
		}
		if(document.form1.toption_price.value.length!=0) temp=0;
		else temp=-1;
		temp2=document.form1.toption_price.value;
		while(temp!=-1){
			temp=temp2.indexOf(",");
			if(temp!=-1) temp3=(temp2.substring(0,temp));
			else temp3=temp2;
			temp4=" "+temp3;
			if(isNaN(temp3) || temp4.indexOf('.')>0){
				alert("옵션 가격은 숫자만 입력을 하셔야 합니다.");
				document.form1.toption_price.focus();
				return;
			}
			temp2=temp2.substring(temp+1);
		}
		document.form1.option_price.value=document.form1.toption_price.value+",";
		document.form1.option1_name.value=document.form1.toptname1.value;
		document.form1.option1.value=document.form1.toption1.value+",";
		document.form1.option2_name.value=document.form1.toptname2.value;
		document.form1.option2.value=document.form1.toption2.value+",";
<?	//if($gongtype=="N" && (int)$_data->vender==0){
	if($gongtype=="N"){ ?>
	} else if(shop=="layer3") {
		if(document.form1.optiongroup.selectedIndex==0) {
			alert("옵션그룹을 선택하세요.");
			document.form1.optiongroup.focus();
			return;
		}
<? } ?>
	}
	//옵션체크 끝 ==============================================================================
	
<?
	if($sns_ok =="Y" && $arSnsType[0] =="B"){
?>
	if (document.form1.sns_reserve1.value.length>0) {
		if(document.form1.sns_reserve1_type.value=="Y") {
			if(isDigitSpecial(document.form1.reserve.value,".")) {
				alert("적립률은 숫자와 특수문자 소수점\(.\)으로만 입력하세요.");
				document.form1.sns_reserve1.focus();
				return;
			}
			
			if(getSplitCount(document.form1.sns_reserve1.value,".")>2) {
				alert("적립률 소수점\(.\)은 한번만 사용가능합니다.");
				document.form1.sns_reserve1.focus();
				return;
			}

			if(getPointCount(document.form1.sns_reserve1.value,".",2)==true) {
				alert("적립률은 소수점 이하 둘째자리까지만 입력 가능합니다.");
				document.form1.sns_reserve1.focus();
				return;
			}

			if(Number(document.form1.sns_reserve1.value)>100 || Number(document.form1.sns_reserve1.value)<0) {
				alert("적립률은 0 보다 크고 100 보다 작은 수를 입력해 주세요.");
				document.form1.sns_reserve1.focus();
				return;
			}
		} else {
			if(isDigitSpecial(document.form1.sns_reserve1.value,"")) {
				alert("적립금은 숫자로만 입력하세요.");
				document.form1.sns_reserve1.focus();
				return;
			}
		}
	}
	if (document.form1.sns_reserve2.value.length>0) {
		if(document.form1.sns_reserve2_type.value=="Y") {
			if(isDigitSpecial(document.form1.sns_reserve2.value,".")) {
				alert("적립률은 숫자와 특수문자 소수점\(.\)으로만 입력하세요.");
				document.form1.sns_reserve2.focus();
				return;
			}
			
			if(getSplitCount(document.form1.sns_reserve2.value,".")>2) {
				alert("적립률 소수점\(.\)은 한번만 사용가능합니다.");
				document.form1.sns_reserve2.focus();
				return;
			}

			if(getPointCount(document.form1.sns_reserve2.value,".",2)==true) {
				alert("적립률은 소수점 이하 둘째자리까지만 입력 가능합니다.");
				document.form1.sns_reserve2.focus();
				return;
			}

			if(Number(document.form1.sns_reserve2.value)>100 || Number(document.form1.sns_reserve2.value)<0) {
				alert("적립률은 0 보다 크고 100 보다 작은 수를 입력해 주세요.");
				document.form1.sns_reserve2.focus();
				return;
			}
		} else {
			if(isDigitSpecial(document.form1.sns_reserve2.value,"")) {
				alert("적립금은 숫자로만 입력하세요.");
				document.form1.sns_reserve2.focus();
				return;
			}
		}
	}
<?
}

if($arRecomType[0] =="B" && $arRecomType[1] == "B"){
?>
	if (document.form1.first_reserve.value.length>0) {
		if(document.form1.first_reserve_type.value=="Y") {
			if(isDigitSpecial(document.form1.reserve.value,".")) {
				alert("적립률은 숫자와 특수문자 소수점\(.\)으로만 입력하세요.");
				document.form1.first_reserve.focus();
				return;
			}
			
			if(getSplitCount(document.form1.first_reserve.value,".")>2) {
				alert("적립률 소수점\(.\)은 한번만 사용가능합니다.");
				document.form1.first_reserve.focus();
				return;
			}

			if(getPointCount(document.form1.first_reserve.value,".",2)==true) {
				alert("적립률은 소수점 이하 둘째자리까지만 입력 가능합니다.");
				document.form1.first_reserve.focus();
				return;
			}

			if(Number(document.form1.first_reserve.value)>100 || Number(document.form1.first_reserve.value)<0) {
				alert("적립률은 0 보다 크고 100 보다 작은 수를 입력해 주세요.");
				document.form1.first_reserve.focus();
				return;
			}
		} else {
			if(isDigitSpecial(document.form1.first_reserve.value,"")) {
				alert("적립금은 숫자로만 입력하세요.");
				document.form1.first_reserve.focus();
				return;
			}
		}
	}
<?
}
?>
	filesize = Number(document.form1.size_checker.fileSize) + Number(document.form1.size_checker2.fileSize) + Number(document.form1.size_checker3.fileSize) ;
	if(filesize><?=$maxfilesize?>) { 
		alert('올리시려고 하는 파일용량이 500K이상입니다.\n파일용량을 체크하신후에 다시 이미지를 올려주세요');
		return;
	}
	tempcontent = document.form1.content.value;
<?if ($predit_type=="Y"){ ?>
	if(mode=="modify" && tempcontent.length>0 && tempcontent.indexOf("<")==-1 && tempcontent.indexOf(">")==-1 && !confirm("웹편집기 기능추가로 텍스트로만 입력하신 상세설명은\n줄바꾸기가 해제되어 쇼핑몰에서 다르게 보여질 수 있습니다.\n\n재입력하시거나 현재 쇼핑몰에서 해당 상품의 상세설명을\n그대로 마우스로 드래그하여 붙여넣기를 해서 재입력하셔야 합니다.\n\n위와 같이 수정하지 않고 저장하시려면 [확인]을 누르세요.")){
		return;
	}
<?}?>
	document.form1.mode.value=mode;
	document.form1.submit();
}

//-->
</SCRIPT>

<?
if($searchtype==2 || $optionover=="YES") {
	echo "<script>document.form1.searchtype[2].checked=true;\nViewLayer('layer2');</script>";
} else if($searchtype==1) {
	echo "<script>document.form1.searchtype[1].checked=true;\nViewLayer('layer1');</script>";
//} else if($searchtype==3 && $gongtype=="N" && (int)$_data->vender==0) {
} else if($searchtype==3 && $gongtype=="N" ) {
	echo "<script>document.form1.searchtype[3].checked=true;\nViewLayer('layer3');</script>";
}
if ($_data->sns_state=="Y") {
	echo "<script>ViewSnsLayer('block');</script>";
}
?>

