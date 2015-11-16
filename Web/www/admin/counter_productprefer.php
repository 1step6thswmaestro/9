<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
INCLUDE ("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "st-1";
$MenuCode = "counter";
if (!$_usersession->isAllowedTask($PageCode)) {
	INCLUDE ("AccessDeny.inc.php");
	exit;
}
#########################################################

$type=$_POST["type"];
$searchdate=$_POST["searchdate"];
$print=$_POST["print"];
$limit = $_POST['limit'];
$codeA=$_REQUEST["codeA"];
$codeB=$_REQUEST["codeB"];
$codeC=$_REQUEST["codeC"];
$codeD=$_REQUEST["codeD"];

if($codeA!="000") $likecode.=$codeA;
if($codeB!="000") $likecode.=$codeB;
if($codeC!="000") $likecode.=$codeC;
if($codeD!="000") $likecode.=$codeD;

if(strlen($likecode)>0) {
	$qry = "AND productcode LIKE '".$likecode."%' ";
}
if(strlen($limit)>0) {
	$lmt = "LIMIT {$limit}";
}

if(strlen($type)==0) $type="d";
if(strlen($searchdate)==0) $searchdate=date("Ymd");
if($type!="m" && strlen($searchdate)!=8) $searchdate=date("Ymd");
if($type=="d" && $searchdate==date("Ymd")) $timeview="NO";

$month= date("m");
if($type=="d") {
	$sql ="SELECT * FROM tblcounterproduct WHERE date='".$searchdate."' && cnt>0 {$qry} ORDER BY cnt DESC {$lmt}";
} else if($type=="w") {
	$prevdate=date("Ymd",mktime(0,0,0,date("m"),date("d")-7,date("Y")));
	$sql ="SELECT SUM(cnt) as cnt,productcode FROM tblcounterproduct ";
	$sql.="WHERE (date<='".$searchdate."' AND date>='".$prevdate."') && cnt>0 {$qry} ";
	$sql.="GROUP BY productcode ORDER BY cnt DESC {$lmt}";
} else if($type=="m") {
	$date=substr($searchdate,0,6);  
	if ($date==date("Ym")) {
		$sql ="SELECT SUM(cnt) as cnt,productcode FROM tblcounterproduct ";
		$sql.="WHERE date LIKE '".$date."%' && cnt>0 {$qry} GROUP BY productcode ORDER BY cnt DESC {$lmt}";
	} else {
		$sql ="SELECT cnt,productcode FROM tblcounterproductmonth ";
		$sql.="WHERE date='".$date."' && cnt>0 {$qry}  ORDER BY cnt DESC {$lmt}";
	}
}

$sum=0;
$result = mysql_query($sql,get_db_conn());
$count=0;
while($row = mysql_fetch_object($result)) {
	$time[$count]=$row->cnt;
	$productcode[$count]=$row->productcode;
	if($max<$row->cnt) $max=$row->cnt;
	$sum+=$row->cnt;

	$count++;
}
mysql_free_result($result);

unset($productinfo);
if($count<>0) {
	$prlist="";
	for($i=0;$i<count($productcode);$i++) {
		$prlist.=$productcode[$i].",";
	}
	$prlist=substr($prlist,0,-1);

	if(strlen($prlist)>0) {
		$prlist=ereg_replace(',','\',\'',$prlist);
		$sql ="SELECT productname,productcode,tinyimage FROM tblproduct ";
		$sql.="WHERE productcode IN ('".$prlist."') ORDER BY FIELD(productcode,'".$prlist."') ";
		$result=mysql_query($sql,get_db_conn());
		$i=0;
		while($row=mysql_fetch_object($result)) {
			if($i!=0) $productinfo.="=";
			$productinfo.=$row->productname."||".$row->tinyimage;
			$i++;
		}
		mysql_free_result($result);
	}

	$arrayproduct = explode("=",$productinfo);
	$countproduct=count($arrayproduct)-$count;
}

?>


<? INCLUDE "header.php"; ?>

<script type="text/javascript" src="../lib/DropDown.js.php"></script>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function search_date(type) {
	document.form1.type.value=type;
	document.form1.submit();
}

function view_printpage(){
	window.open("about:blank","popviewprint","height=550,width=700,scrollbars=yes");
	document.form2.print.value="Y";
	document.form2.submit();
}

</script>

<?

$sql = "SELECT * FROM tblproductcode WHERE group_code!='NO' ";
$sql.= "AND (type!='T' AND type!='TX' AND type!='TM' AND type!='TMX') ORDER BY sequence DESC ";
$i=0;
$ii=0;
$iii=0;
$iiii=0;
$strcodelist = "";
$strcodelist.= "<script>\n";
$result = mysql_query($sql,get_db_conn());
$selcode_name="";
while($row=mysql_fetch_object($result)) {
	$strcodelist.= "var clist=new CodeList();\n";
	$strcodelist.= "clist.codeA='".$row->codeA."';\n";
	$strcodelist.= "clist.codeB='".$row->codeB."';\n";
	$strcodelist.= "clist.codeC='".$row->codeC."';\n";
	$strcodelist.= "clist.codeD='".$row->codeD."';\n";
	$strcodelist.= "clist.type='".$row->type."';\n";
	$strcodelist.= "clist.code_name='".$row->code_name."';\n";
	if($row->type=="L" || $row->type=="T" || $row->type=="LX" || $row->type=="TX") {
		$strcodelist.= "lista[".$i."]=clist;\n";
		$i++;
	}
	if($row->type=="LM" || $row->type=="TM" || $row->type=="LMX" || $row->type=="TMX") {
		if ($row->codeC=="000" && $row->codeD=="000") {
			$strcodelist.= "listb[".$ii."]=clist;\n";
			$ii++;
		} else if ($row->codeD=="000") {
			$strcodelist.= "listc[".$iii."]=clist;\n";
			$iii++;
		} else if ($row->codeD!="000") {
			$strcodelist.= "listd[".$iiii."]=clist;\n";
			$iiii++;
		}
	}
	$strcodelist.= "clist=null;\n\n";
}
mysql_free_result($result);
$strcodelist.= "CodeInit();\n";
$strcodelist.= "</script>\n";

echo $strcodelist;

?>
<?if($print!="Y"){?>

<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
<tr>
	<td valign="top">
	<table cellpadding="0" cellspacing="0" width=100% style="table-layout:fixed">
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed"  background="images/con_bg.gif">
		<col width=198></col>
		<col width=10></col>
		<col width=></col>
		<tr>
			<td valign="top"  background="images/leftmenu_bg.gif">
			<? include ("menu_counter.php"); ?>
			</td>

			<td></td>
			<td valign="top">




<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td height="29" colspan="3">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 통계분석 &gt; 고객 선호도 분석 &gt; <span class="2depth_select">상품 선호도</span></td>
			</tr>
			</table>
		</td>
	</tr>   
	<tr>
        <td width="16"><img src="images/con_t_01.gif" width="16" height="16" border="0"></td>
        <td background="images/con_t_01_bg.gif"></td>
        <td width="16"><img src="images/con_t_02.gif" width="16" height="16" border="0"></td>
    </tr>
    <tr>
        <td width="16" background="images/con_t_04_bg1.gif"></td>
        <td bgcolor="#ffffff" style="padding:10px">



			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
<?} else {?>
			<table cellpadding="5" cellspacing="0" width="680" style="table-layout:fixed">
<?}?>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/counter_prorderprefer_title.gif" ALT=""></TD>
					</tr>
<tr>
<TD width="100%" background="images/title_bg.gif" height="21"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height="40"></td></tr>
			<tr>
				<td align=center>

				<table cellpadding="0" cellspacing="0" width="100%">
				<form name=form1 method=post action="<?=$_SERVER[PHP_SELF]?>">
				<input type=hidden name=type>
				<input type=hidden name=print value="<?=$print?>">
				
				<tr>
					<td style="font-size:11px;">
					<table border=0 cellpadding=0 cellspacing=0 width=100%>	
					<tr>
						<td >
							<select name=codeA style="width:183px" onchange="SearchChangeCate(this,1)" style="font-size:11px;">
							<option value="">--- 1차 카테고리 선택 ---</option>
							</select>
							<select name=codeB style="width:183px" onchange="SearchChangeCate(this,2)" style="font-size:11px;">
							<option value="">--- 2차 카테고리 선택 ---</option>
							</select>
							<select name=codeC style="width:183px;" onchange="SearchChangeCate(this,3)" style="font-size:11px;">
							<option value="">--- 3차 카테고리 선택 ---</option>
							</select>
							<select name=codeD style="width:183px" style="font-size:11px;">
							<option value="">--- 4차 카테고리 선택 ---</option>
							</select>
						</td>
					</tr>
					<tr><td height="10"></td></tr>
					</table>
					<script>SearchCodeInit("<?=$codeA?>","<?=$codeB?>","<?=$codeC?>","<?=$codeD?>");</script>
					</td>
				</tr>
				<tr>
					<td style="font-size:11px;">
					<table border=0 cellpadding=0 cellspacing=0 width=100%>
					<tr>
						<td>
<?
						if($timeview=="NO") {
							echo "* <b><font color=\"#FF6633\">".date("Y년 m월 d일 H시 i분")."</font></b> 현재";
						} else {
							echo "* <b><font color=\"#FF6633\">".substr($searchdate,0,4)."년 ".substr($searchdate,4,2)."월 ".($type!="m"?substr($searchdate,6,2)."일":"")."</font></b> 전체";
						}
						echo " 상품 선호도 입니다.";
?>
						</td>
						<td align=right>
						<select name=limit>
							<option value=''>전체상품</option>
							<option value='10'>10위</option>
							<option value='20'>20위</option>
							<option value='50'>50위</option>
							<option value='100'>100위</option>
							<option value='200'>200위</option>
							<option value='500'>500위</option>
						</select>
						<script>document.form1.limit.value='<?=$limit;?>';</script>

						<A HREF="javascript:search_date('d')"><img src="images/counter_tab_day_<?=($type=="d"?"on":"off")?>.gif" width="74" height="20" border="0"></A>
						<A HREF="javascript:search_date('w')"><img src="images/counter_tab_week_<?=($type=="w"?"on":"off")?>.gif" width="74" height="20" border="0"></A>
						<A HREF="javascript:search_date('m')"><img src="images/counter_tab_month_<?=($type=="m"?"on":"off")?>.gif" width="74" height="20" border="0"></A>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				<tr>
					<td>
					<table border=0 cellpadding=0 cellspacing=0 width=100%>
					<col width=50></col>
					<col width=40></col>
					<col width=></col>
					<col width=100></col>
					<col width=100></col>
					<tr><td colspan=5 height=2 bgcolor=#000000></td></tr>
					<TR>
						<TD class="table_cell" align=center><FONT color=#3d3d3d>NO</FONT></TD>
						<TD class="table_cell" align=center></TD>
						<TD class="table_cell" align=center><FONT color=#3d3d3d>상품명</FONT></TD>
						<TD class="table_cell" align=center><FONT color=#3d3d3d>방문자수</FONT></TD>
						<TD class="table_cell" align=center><FONT color=#3d3d3d>퍼센트</FONT></TD>
					</TR>
					<tr><td colspan=5 height=1 bgcolor=#EDEDED></td></tr>
<?
					$len=count($time); 
					for($i=0;$i<$len;$i++){
						$percent[$i]=$time[$i]/$sum*100;
						if($pos=strpos($percent[$i],".")) {
							$percent[$i]=substr($percent[$i],0,$pos+3);
						}
						if(strlen($arrayproduct[$i])==0) $arrayproduct[$i]="삭제된 상품";
						$prinfo=explode("||",$arrayproduct[$i]);
						$prname=$prinfo[0];
						$primage=$prinfo[1];
						$prname="<A HREF=\"http://".$shopurl."?productcode=".$productcode[$i]."\" target=\"_blank\">".$prname."</A>";
						if(strlen($primage)>0) $primage="<img src=\"http://".$shopurl.DataDir."shopimages/product/".$primage."\" width=40 border=0>";
						echo "<tr>\n";
						echo "	<TD class=\"td_con2a\" align=center>".($i+1)."</td>\n";
						echo "	<TD class=\"td_con2a\" align=center>".$primage."</td>\n";
						echo "	<TD class=\"td_con2a\" style=\"padding-left:5\">".$prname."</td>\n";
						echo "	<TD class=\"td_con2a\" align=center><FONT color=\"#00769D\">".number_format($time[$i])."명</FONT></td>\n";
						echo "	<TD class=\"td_con2a\" align=center>".$percent[$i]."%</td>\n";
						echo "</tr>\n";
						echo "<tr><td colspan=5 height=1 bgcolor=#EDEDED></td></tr>\n";

					}
					if($len==0){
						echo "<tr bgcolor=#FFFFFF><td colspan=5 height=30 class=\"td_con2a\" align=center><font color=#3D3D3D>해당 자료가 없습니다.</font></td></tr>\n";
						echo "<tr><td colspan=5 height=1 bgcolor=#EDEDED></td></tr>\n";
					}
?>
					</table>
					</td>
				</tr>
				<?if($print!="Y"){?>
				<TR>
					<TD width="100%" background="images/counter_blackline_bg.gif" height="30" align=right>
					<table cellpadding="0" cellspacing="0">
					<tr>
						<td class="font_white" align=right>
						<?if($type=="d") {?>
						지난 접속통계 
						<select name=searchdate onchange="search_date('d')">
<?
						for($i=59;$i>=0;$i--) {
							$date=date("Ymd",mktime(0,0,0,date("m"),date("d")-$i,date("Y")));
							echo "<option value=\"".$date."\"";
							if($date==$searchdate) echo " selected";
							echo ">".substr($date,0,4)."년 ".substr($date,4,2)."월 ".substr($date,6,2)."일</option>\n";
						}
?>
						</select>
						<?}?>
						<?if($type=="m") {?>
						지난 접속통계 
						<select name=searchdate onchange="search_date('m')">
<?
						$cnt=11;  
						for($i=0;$i<=$cnt;$i++) {
							$date=date("Ym",mktime(0,0,0,date("m")-$i,1,date("Y")));
							echo "<option value=\"".$date."\"";
							if($date==$searchdate) echo " selected";
							echo ">".substr($date,0,4)."년 ".substr($date,4,2)."월</option>\n";
						}
?>
						</select>
						<?}?>
						</td>
						<td align=right style="padding:0,5,0,5"><A HREF="javascript:view_printpage()"><img src="images/counter_btn_print.gif" width="90" height="20" border="0"></A></td>
					</tr>
					</table>
					</TD>
				</TR>
				<?} else {?>
				<TR>
					<td align=right style="padding:20,20,0,5"><A HREF="javascript:print()"><img src="images/counter_btn_print.gif" width="90" height="20" border="0"></A></td>
				</TR>
				<?}?>
				</form>
				</table>

				</td>
			</tr>
			<tr><td height="30"></td></tr>
<?if($print!="Y"){?>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/manual_top1.gif" WIDTH=15 HEIGHT=45 ALT=""></TD>
					<TD><IMG SRC="images/manual_title.gif" WIDTH=113 HEIGHT=45 ALT=""></TD>
					<TD width="100%" background="images/manual_bg.gif"></TD>
					<TD background="images/manual_bg.gif"></TD>
					<TD><IMG SRC="images/manual_top2.gif" WIDTH=18 HEIGHT=45 ALT=""></TD>
				</TR>
				<TR>
					<TD background="images/manual_left1.gif"></TD>
					<TD COLSPAN=3 width="100%" valign="top" bgcolor="white" style="padding-top:8pt; padding-bottom:8pt; padding-left:4pt;" 
 class="menual_bg">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td><FONT color=#3d3d3d>쇼핑몰을 방문한 고객이 어떤 특정 상품에 많은 관심을 보였는 지 알 수 있습니다.</FONT></td>
					</tr>
					<tr><td colspan=2 height=5></td></tr>
					<tr>
						<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td>
							<FONT color=#3d3d3d>쇼핑몰에 등록되어 있는 많은 상품중에서 고객이 선호하는 상품을 개별적으로 확인할 수 있습니다.<br>
							쇼핑몰을 방문한 고객의 개별 상품 선호도 등 고객속성을 파악할 수 있습니다.</FONT>
						</td>
					</tr>
					<tr><td colspan=2 height=5></td></tr>
					<tr>
						<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td>
							<FONT color=#3d3d3d>당사의 쇼핑몰의 많은 상품들 중에서 고객들이 가장 많은 관심을 보인 상품을 파악하여, <br>
							이 제품들에 대한 재고관리 및 판매 프로모션에 접목할 수 있습니다.</FONT>
						</td>
					</tr>
					</table>
					</TD>
					<TD background="images/manual_right1.gif"></TD>
				</TR>
				<TR>
					<TD><IMG SRC="images/manual_left2.gif" WIDTH=15 HEIGHT=8 ALT=""></TD>
					<TD COLSPAN=3 background="images/manual_down.gif"></TD>
					<TD><IMG SRC="images/manual_right2.gif" WIDTH=18 HEIGHT=8 ALT=""></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height="30"></td></tr>
<?}?>
			</table>
<?if($print!="Y"){?>
</td>
        <td width="16" background="images/con_t_02_bg.gif"></td>
    </tr>
    <tr>
        <td width="16"><img src="images/con_t_04.gif" width="16" height="16" border="0"></td>
        <td background="images/con_t_04_bg.gif"></td>
        <td width="16"><img src="images/con_t_03.gif" width="16" height="16" border="0"></td>
    </tr>
    <tr><td height="20"></td></tr>
</table>

			</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>

<form name=form2 method=post action="<?=$_SERVER[PHP_SELF]?>"  target=popviewprint>
<input type=hidden name=print>
<input type=hidden name=codeA value="<?=$codeA?>">
<input type=hidden name=codeB value="<?=$codeB?>">
<input type=hidden name=codeC value="<?=$codeC?>">
<input type=hidden name=codeD value="<?=$codeD?>">
<input type=hidden name=type value=<?=$type?>>
<input type=hidden name=searchdate value=<?=$searchdate?>>
</form>
</table>
<?=$onload?>
<? INCLUDE "copyright.php"; ?>
<?}?>