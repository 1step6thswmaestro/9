<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
INCLUDE ("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "de-7";
$MenuCode = "design";
if (!$_usersession->isAllowedTask($PageCode)) {
	INCLUDE ("AccessDeny.inc.php");
	exit;
}
#########################################################

$imagepath=$Dir.DataDir."shopimages/etc/";

$type=$_POST["type"];
$selimage=$_POST["selimage"];
$okdesign=$_POST["okdesign"];

if($type=="delete_bgimage") {	//백그라운드 삭제
	$bgimage = $imagepath."easymenubg.gif";
	if (file_exists($bgimage)) {
		unlink($bgimage);
		$onload="<script>alert('왼쪽메뉴 배경 이미지 삭제가 완료되었습니다.');</script>";
	} else {
		$onload="<script>alert('등록된 왼쪽메뉴 배경 이미지가 존재하지 않습니다.');</script>";
	}
} else if($type=="delete_menuimage") {	//해당 메뉴 이미지 삭제
	$menuimage=$imagepath.$selimage.".gif";
	if(file_exists($menuimage)) {
		unlink($menuimage);
		$onload="<script>alert('해당 메뉴 이미지 삭제가 완료되었습니다.');</script>";
	} else {
		$onload="<script>alert('해당 메뉴 이미지가 존재하지 않습니다.');</script>";
	}
} else if($type=="update") {	//정보 업데이트
	while (list($key, $vals) = each($_POST)) {
		${$key}=$_POST[$key];
	}
	while (list($key, $vals) = each($_FILES)) {
		${$key}=$_FILES[$key];
	}
	$number=array(&$num0,&$num1,&$num2,&$num3,&$num4);

	$iserror=false;
	if (file_exists($background["tmp_name"])) {
		$imgname=$background["name"];
		$ext = strtolower(substr($imgname,strlen($imgname)-3,3));
		$rimage="easymenubg.gif";
		if($ext=="gif" || $ext=="jpg"){
			move_uploaded_file($background["tmp_name"],$imagepath.$rimage);
			chmod($imagepath.$rimage,0664);
		} else {
			$iserror=true;
		}
	}

	$cnt=5;
	for($i=0;$i<$cnt;$i++) {
		for($j=0;$j<$number[$i];$j++) {
			$rimage=${"easymenucode".$i.$j}.".gif";
			$img=${"file".$i.$j};
			$imgname=$img["name"];

			if (strlen($imgname)>0 && file_exists($img["tmp_name"])) {
				$ext = strtolower(substr($imgname,strlen($imgname)-3,3));
				if($ext=="gif" || $ext=="jpg") {
					move_uploaded_file($img["tmp_name"],$imagepath.$rimage);
					chmod($imagepath.$rimage,0664);
				} else {
					$iserror=true;
				}
			}
		}
	}

	if($iserror) {
		echo "<html></head><body onload=\"alert('이미지 등록이 잘못되었습니다.');history.go(-1);\"></body></html>";exit;
	}

	$menulist=substr($menulist,1);

	$sql = "SELECT COUNT(*) as cnt FROM tbldesign ";
	$result=mysql_query($sql,get_db_conn());
	$row=mysql_fetch_object($result);
	$cnt=(int)$row->cnt;
	mysql_free_result($result);
	$qry="";
	if($cnt==0) {
		$sql = "INSERT tbldesign SET ";
	} else {
		$sql = "UPDATE tbldesign SET ";
	}
	$sql.= "left_set	= 'Y', ";
	$sql.= "left_xsize	= '".$left_xsize."', ";
	$sql.= "left_image	= '".$menulist."' ";
	$sql.= $qry;
	mysql_query($sql,get_db_conn());
	DeleteCache("tbldesign.cache");

	if($okdesign=="Y") {
		$sql = "UPDATE tblshopinfo SET menu_type='menue' ";
		mysql_query($sql,get_db_conn());
		DeleteCache("tblshopinfo.cache");

		$_shopdata->menu_type="menue";
	}
	$onload = "<script> alert('정보 수정이 완료되었습니다.'); </script>";
}

$sql = "SELECT * FROM tbldesign ";
$result=mysql_query($sql,get_db_conn());
$row=mysql_fetch_object($result);
if($row->left_set=="Y") {
	$left_xsize=$row->left_xsize;
	$imgtype=$row->left_image;
} else {
	$imgtype="1,2,3,4";
}
mysql_free_result($result);

$allimgname=array("로그인","상품 검색","상품 카테고리","커뮤니티","고객센터","배너","이벤트/고객알림");
$allimgtype=",0,1,2,3,4,5,6,";

$cnt = count($allimgname);

$ar_imgtype = explode(",",$imgtype);
$cnt2 = count($ar_imgtype);

?>

<? INCLUDE "header.php"; ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm() {
	if(document.form1.left_xsize.value.length==0) {
		alert("왼쪽메뉴 사이즈를 입력하세요.");
		document.form1.left_xsize.focus();
		return;
	}
	if(!IsNumeric(document.form1.left_xsize.value)) {
		alert("왼쪽메뉴 사이즈는 숫자만 입력하세요.");
		document.form1.left_xsize.focus();
		return;
	}
	document.form1.menulist.value="";
	for(i=0;i<document.form1.inmenu.options.length;i++) {
		document.form1.menulist.value+=","+document.form1.inmenu.options[i].value;
	}
	if(document.form1.menulist.value.length==0) {
		alert("왼쪽메뉴를 하나 이상 노출하셔야 합니다.");
		return;
	}
	document.form1.type.value="update";
	document.form1.submit();
}

function SendMode(mode) {
	if (document.form1.outmenu.selectedIndex==-1 && mode=="insert") {
		alert("왼쪽메뉴에 추가할 메뉴를 선택하세요.");
		return;
	} else if(document.form1.inmenu.selectedIndex==-1 && mode=="delete") {
		alert("왼쪽메뉴에서 삭제할 메뉴를 선택하세요.");
		return;
	}
	if (mode=="insert") {
		text=document.form1.outmenu.options[document.form1.outmenu.selectedIndex].text;
		value=document.form1.outmenu.options[document.form1.outmenu.selectedIndex].value;
		document.form1.inmenu.options[document.form1.inmenu.options.length]=new Option(text,value);
		document.form1.outmenu.options[document.form1.outmenu.selectedIndex]=null;
	} else if (mode=="delete"){
		text=document.form1.inmenu.options[document.form1.inmenu.selectedIndex].text;
		value=document.form1.inmenu.options[document.form1.inmenu.selectedIndex].value;
		document.form1.outmenu.options[document.form1.outmenu.options.length]=new Option(text,value);
		document.form1.inmenu.options[document.form1.inmenu.selectedIndex]=null;
	}
}

function move(gbn) {
	change_idx = document.form1.inmenu.selectedIndex;
	if (change_idx<0) {
		alert("순서를 변경할 메뉴를 선택하세요.");
		return;
	}
	if (gbn=="up" && change_idx==0) {
		alert("선택하신 메뉴는 더이상 위로 이동되지 않습니다.");
		return;
	}
	if (gbn=="down" && change_idx==(document.form1.inmenu.length-1)) {
		alert("선택하신 메뉴는 더이상 아래로 이동되지 않습니다.");
		return;
	}
	if (gbn=="up") idx = change_idx-1;
	else idx = change_idx+1;

	idx_value = document.form1.inmenu.options[idx].value;
	idx_text = document.form1.inmenu.options[idx].text;

	document.form1.inmenu.options[idx].value = document.form1.inmenu.options[change_idx].value;
	document.form1.inmenu.options[idx].text = document.form1.inmenu.options[change_idx].text;

	document.form1.inmenu.options[change_idx].value = idx_value;
	document.form1.inmenu.options[change_idx].text = idx_text;

	document.form1.inmenu.selectedIndex = idx;
}

var layer=new Array("layer0","layer1","layer2","layer3","layer4","layer5","layer6");
function change_layer(val) {
	if(document.all){
		for(i=0;i<layer.length;i++) {
			document.all[layer[i]].style.display="none";
		}
		document.all["layer"+val].style.display="";
	} else if(document.getElementById){
		for(i=0;i<layer.length;i++) {
			document.getElementByld[layer[i]].style.display="none";
		}
		document.getElementByld["layer"+val].style.display="";
	} else if(document.layers){
		for(i=0;i<layer.length;i++) {
			document.layers[layer[i]].display="none";
		}
		document.layers["layer"+val].display="";
	}
}

function delet_bgimage() {
	if(confirm("왼쪽메뉴 배경 이미지를 정말 삭제하시겠습니까?")) {
		document.form1.type.value="delete_bgimage";
		document.form1.submit();
	}
}

function delete_menuimage(val) {
	if(confirm("해당 완쪽메뉴 이미지를 정말 삭제하시겠습니까?")) {
		document.form1.type.value="delete_menuimage";
		document.form1.selimage.value=val;
		document.form1.submit();
	}
}

</script>
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
			<? include ("menu_design.php"); ?>
			</td>

			<td></td>
			<td valign="top">




<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td height="29" colspan="3">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 디자인관리 &gt; Easy 디자인 관리 &gt; <span class="2depth_select">Easy 왼쪽 메뉴 관리</span></td>
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
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/design_easyleft_img.gif" ALT=""></TD>
					</tr>
<tr>
<TD width="100%" background="images/title_bg.gif" height="21"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height="3"></td></tr>
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
					<TD width="100%" class="notice_blue"><p>쇼핑몰 왼쪽메뉴 디자인을 직접 제작하신 이미지파일을 이용하여, 간단하게 디자인을 하실 수 있습니다.</p></TD>
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
			<tr><td height=20></td></tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/distribute_01.gif" WIDTH=7 HEIGHT=7 ALT=""></TD>
					<TD COLSPAN=2 background="images/distribute_02.gif"></TD>
					<TD><IMG SRC="images/distribute_03.gif" WIDTH=8 HEIGHT=7 ALT=""></TD>
				</TR>
				<TR>
					<TD background="images/distribute_04.gif"></TD>
					<TD class="notice_blue"><IMG SRC="images/distribute_img.gif"></TD>
					<TD width="100%" class="notice_blue">
						<table cellpadding="0" cellspacing="0" width="100%">
							<tr>
								<td width="100%" class="notice_blue">1) HTML을 몰라도 왼쪽메뉴의 디자인과 순서를 쉽게 변경 가능합니다.<br>2) Easy 디자인 해제 : 메인 템플릿 선택 또는 개별디자인 선택하면 됩니다.</td>
								<td width="145"><p align="center"><img src="images/design_eachleftmenu_img.gif" width="159" height="100" border="0"></p></td>
							</tr>
						</table></TD>
					<TD background="images/distribute_07.gif"></TD>
				</TR>
				<TR>
					<TD><IMG SRC="images/distribute_08.gif" WIDTH=7 HEIGHT=8 ALT=""></TD>
					<TD COLSPAN=2 background="images/distribute_09.gif"></TD>
					<TD><IMG SRC="images/distribute_10.gif" WIDTH=8 HEIGHT=8 ALT=""></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td>&nbsp;</td></tr>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="200" valign="top" height="100%">
					<table cellpadding="0" cellspacing="0" width="100%" height="100%">
					<tr>
						<td width="100%" height="100%" valign="top" background="images/category_boxbga.gif">
						<table cellpadding="0" cellspacing="0" width="100%" height="100%">
						<tr>
							<td><IMG SRC="images/category_box1a.gif" WIDTH="200" HEIGHT=4 ALT=""></td>
						</tr>
						<tr>
							<td bgcolor="#0F8FCB" style="padding-top:4pt; padding-bottom:6pt;"><p align="center">&nbsp;<B><font color="white">쇼핑몰 실제 왼쪽이미지</font></B></p></td>
						</tr>
						<tr>
							<td width="234"><IMG SRC="images/category_box2a.gif" WIDTH="200" HEIGHT=5 ALT=""></td>
						</tr>
						<tr>
							<td width="100%" height="100%">
							<TABLE cellSpacing=0 cellPadding="10" width="100%" border="0" height="100%">
							<TR>
								<TD height="100%" valign="top" width="100%"><iframe name=contents src="/<?=RootPath.MainDir?>menue.php?preview=OK" frameborder=no scrolling=auto style="WIDTH:100%;HEIGHT:100%;border;border-width:1pt; border-color:#D3E2F5; border-style:solid;"></iframe></td>
							</TR>
							</TABLE>
							</td>
						</tr>
						</table>
						</td>
					</tr>
					<tr>
						<td width="232" height="13"><IMG SRC="images/category_boxdowna.gif" WIDTH="200" HEIGHT=13 ALT=""></td>
					</tr>
					</table>
					</TD>
					<td width="5" valign="top"></TD>
					<td  valign="top">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="100%">
						<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
						<TR>
							<TD><IMG SRC="images/design_easyleft_stitle1.gif"  ALT=""></TD>
							<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif"></TD>
							<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></TD>
						</TR>
						</TABLE>
						</td>
					</tr>
					<tr>
						<td height=3></td>
					</tr>
					<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post enctype="multipart/form-data">
					<input type=hidden name=type>
					<input type=hidden name=menulist>
					<input type=hidden name=selimage>
					<tr>
						<td width="100%">
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
						<TR>
							<TD colspan=2 background="images/table_top_line.gif"></TD>
						</TR>
						<TR>
							<TD class="table_cell" width="153"><img src="images/icon_point2.gif" width="8" height="11" border="0"><FONT color=#3d3d3d>왼쪽 사이즈 설정</FONT></TD>
							<TD class="td_con1" width="382"><img src="images/icon_width1.gif" width="37" height="14" border="0"> : <input type=text name="left_xsize" value="<?=$left_xsize?>" size=7 maxlength=3 onkeyup="strnumkeyup(this)" class="input">픽셀&nbsp;&nbsp;(권장 : 180픽셀)</TD>
						</TR>
						<TR>
							<TD colspan="2" background="images/table_con_line.gif"></TD>
						</TR>
						<TR>
							<TD class="table_cell" width="153"><img src="images/icon_point2.gif" width="8" height="11" border="0"><FONT color=#3d3d3d>왼쪽 배경이미지<br><font color="#0099BF">(JPG/GIF 파일_150K 이하)</font></FONT></TD>
							<TD class="td_con1" width="382"><input type=file name=background size=15 class="input" style="WIDTH: 88%"><? if(file_exists($imagepath."easymenubg.gif")) { ?> <input type="button" onClick="delet_bgimage()" value="삭제" class="submit1"><? }?><br><span class="font_orange" style="font-size:11px;letter-spacing:-0.5pt;"> * 미등록시 흰색 배경, 배경이미지 사이즈가 작을 경우 반복되는 현상 발생됩니다.</span></TD>
						</TR>
						<TR>
							<TD colspan=2 background="images/table_top_line.gif"></TD>
						</TR>
						</TABLE>
						</td>
					</tr>
					<tr><td height="25"></td></tr>
					<tr>
						<td width="100%">
						<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
						<TR>
							<TD><IMG SRC="images/design_easyleft_stitle2.gif"  ALT=""></TD>
							<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif"></TD>
							<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></TD>
						</TR>
						</TABLE>
						</td>
					</tr>
					<tr><td height=3></td></tr>
					<tr>
						<td width="100%">
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
						<TR>
							<TD background="images/table_top_line.gif" colspan="3"></TD>
						</TR>
						<TR>
							<TD class="table_cell" align="center">미사용중인 메뉴명</p></TD>
							<TD class="table_cell1" align="center" width="50">&nbsp;</TD>
							<TD class="table_cell1" align="center" background="images/blueline_bg.gif"><p><span class="font_blue">현재 사용중인 왼쪽메뉴명&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></p></TD>
						</TR>
						<TR>
							<TD colspan="3" background="images/table_con_line.gif"></TD>
						</TR>
						<TR>
							<TD width="40%" align="center" valign="top" style="padding:5pt;"><select name=outmenu size=13 style="width:100%;" class="select">
<?
			for($i=0;$i<$cnt;$i++){
				if(!ereg($i,$imgtype)){
					echo "<option value=\"".$i."\">".$allimgname[$i]."\n";
				}
			}
?>
							</select></TD>
							<TD class="td_con1" align="center" width="50"><a href="JavaScript:SendMode('insert')"><img src="images/btn_next.gif" border="0"><br><span style="color:#000000;font-size:11px;letter-spacing:-0.5pt;">보이기</span></a><br><br><a href="JavaScript:SendMode('delete')"><img src="images/btn_back.gif" border="0"><br><span style="color:#000000;font-size:11px;letter-spacing:-0.5pt;">숨기기</span></a></TD>
							<TD width="60%" class="td_con1" align="center" valign="top" style="padding:5pt;">
							<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
							<TR>
								<TD noWrap align=middle width="100%"><select name=inmenu size=13 style="width:100%" onchange="change_layer(this.value)" class="select">
<?
			for($i=0;$i<$cnt2;$i++){
				echo "<option value=\"".$ar_imgtype[$i]."\">".$allimgname[$ar_imgtype[$i]]."\n";
			}
?>
								</select></TD>
								<TD style="padding-left:5px;">
								<table cellpadding="0" cellspacing="0" width="34">
								<TR>
									<TD align=middle><a href="JavaScript:move('up')"><IMG src="images/code_up.gif" align=absMiddle border=0 width="40" height="30" vspace="2"></A></td>
								</tr>
								<TR>
									<TD align=middle><IMG src="images/code_sort.gif" width="40" height="30"></td>
								</tr>
								<TR>
									<TD align=middle><a href="JavaScript:move('down')"><IMG src="images/code_down.gif" align=absMiddle border=0 width="40" height="30" vspace="2"></A></td>
								</tr>
								</table>
								</TD>
							</TR>
							</TABLE><span class="font_orange" style="font-size:11px;letter-spacing:-0.5pt;">* 메뉴명을 클릭하면 해당 메뉴의 이미지를 등록할 수 있습니다.</span>
							</TD>
						</TR>
						<TR>
							<TD background="images/table_top_line.gif" width="760" colspan="3"></TD>
						</TR>
						</TABLE>
						</td>
					</tr>
					<tr>
						<td width="100%" height="25"><p>&nbsp;</p></td>
					</tr>
					<tr>
						<td width="100%">
<?
				$menuname=array("로그인","상품검색","상품카테고리","커뮤니티","고객센터");
				$commonmenu = array("타이틀 이미지 등록","하단 이미지 등록","배경 이미지 등록");
				$menucode=array(
							array("easylogintitle","easyloginbottom","easyloginbg","easyidimage","easypwimage","easyloginbutton","easylogoutbutton"),
							array("easysearchtitle","easysearchbottom","easysearchbg","easysearchbutton"),
							array("easyproducttitle","easyproductbottom","easyproductbg"),
							array("easyboardtitle","easyboardbottom","easyboardbg"),
							array("easycustomertitle","easycustomerbottom","easycustomerbg"));

				$button=array(
							array("아이디","비밀번호","로그인 버튼","로그아웃 버튼"),
							array("검색버튼"));

				$sql = "SELECT codeA as code, type, code_name FROM tblproductcode ";
				$sql.= "WHERE group_code!='NO' ";
				$sql.= "AND (type='L' OR type='T' OR type='LX' OR type='TX') ORDER BY sequence DESC ";
				$result=mysql_query($sql,get_db_conn());
				$cnt=0;$cnt2=3;
				while($row=mysql_fetch_object($result)) {
					$button[2][$cnt++]=$row->code_name;
					$menucode[2][$cnt2++]="easy".$row->code;
				}
				if($_shopdata->estimate_ok=="Y" || $_shopdata->estimate_ok=="O") {
					$button[2][$cnt++]="온라인 견적서";
					$menucode[2][$cnt2++]="easyestimate";
				}

				$sql = "SELECT board,board_name FROM tblboardadmin ORDER BY date DESC ";
				$result=mysql_query($sql,get_db_conn());
				$cnt=0;$cnt2=3;
				while ($row=mysql_fetch_object($result)) {
					$button[3][$cnt++]=$row->board_name;
					$menucode[3][$cnt2++]="easy".$row->board;
				}
				if ($_shopdata->ETCTYPE["REVIEW"]=="Y") {
					$button[3][$cnt++]="사용후기 모음";
					$menucode[3][$cnt2++]="easyreviewall";
				}
				
				$mess=array("<img src=\"images/design_easyleft_img1.gif\" border=\"0\" width=\"536\">", 
				"<img src=\"images/design_easyleft_img4.gif\" border=\"0\" width=\"536\">", 
				"<img src=\"images/design_easyleft_img2.gif\" border=\"0\" width=\"536\">", 
				"<img src=\"images/design_easyleft_img5.gif\" border=\"0\" width=\"536\">", 
				"<img src=\"images/design_easyleft_img3.gif\" border=\"0\" width=\"536\">");

				for($i=0;$i<=4;$i++) {
?>
						<div id=layer<?=$i?> style="margin-left:0;display:hide; display:none;border-style:solid; border-width:0; border-color:black;background:#FFFFFF;padding:0;">
						<table cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td width="100%">
							<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
							<TR>
								<TD><IMG SRC="images/sub4_stitle_img1.gif" WIDTH=20 HEIGHT=24 ALT=""></TD>
								<TD width="100%" background="images/sub4_stitle_bg.gif" class="font_white"><B><FONT color=#ffffff>[<?=$menuname[$i]?>] 메뉴 이미지 등록</FONT></B></TD>
								<TD><IMG SRC="images/sub4_stitle_img2.gif" WIDTH="20" HEIGHT=24 ALT=""></TD>
							</TR>
							</TABLE>
							</td>
						</tr>
						<tr>
							<td width="100%">
							<TABLE cellSpacing="1" cellPadding=0 width="100%" border=0 bgcolor="#DEDEDE">
							<TR bgColor=#f0f0f0>
								<TD class="table_cell" style="PADDING-RIGHT: 5px; PADDING-LEFT: 5px; PADDING-BOTTOM: 5px; PADDING-TOP: 5px" align=middle width="81" bgcolor="#F9F9F9">왼쪽 메뉴명</TD>
								<TD class="table_cell" style="PADDING-RIGHT: 5px; PADDING-LEFT: 5px; PADDING-BOTTOM: 5px; PADDING-TOP: 5px" align=middle width="140" bgcolor="#F9F9F9">해당 이미지명</TD>
								<TD class="table_cell" style="PADDING-RIGHT: 5px; PADDING-LEFT: 5px; PADDING-BOTTOM: 5px; PADDING-TOP: 5px" align=middle width="274" bgcolor="#F9F9F9">해당 이미지 등록<span class="font_blue">(JPG/GIF 파일_150K)</span></TD>
							</TR>
							<TR>
								<TD class=lineleft style="PADDING-RIGHT: 5px; PADDING-LEFT: 5px; PADDING-BOTTOM: 5px; LINE-HEIGHT: 125%; PADDING-TOP: 5px" align=middle bgcolor="white" rowspan="100"><B><?=$menuname[$i]?></B></TD>
<?
						for($j=0;$j<=2;$j++) {
							if($j!=0) echo "<tr>";
?>
								<TD class="td_con2" style="line-height:125%;letter-spacing:-0.5pt;" align=middle bgcolor="white"><p align="left"><?=$commonmenu[$j]?></p></td>
								<? if(file_exists($imagepath.$menucode[$i][$j].".gif")) { ?>
								<TD class=line style="PADDING-RIGHT: 5px; PADDING-LEFT: 5px; PADDING-BOTTOM: 5px; LINE-HEIGHT: 125%; PADDING-TOP: 5px" align=middle bgcolor="white">
								<table cellpadding="0" cellspacing="0" width="100%">
								<tr>
									<td width="100%"><p align="left"><input type=file name=file<?=$i.$j?> style="WIDTH: 100%" class="input"></td>
									<td width="125" style="padding-left:2px"><input type="button" value="삭제" class="submit1" onClick="delete_menuimage('<?=$menucode[$i][$j]?>')"></td>
								</tr>
								</table>
								<? } else { ?>
								<TD class=line style="PADDING-RIGHT: 5px; PADDING-LEFT: 5px; PADDING-BOTTOM: 5px; LINE-HEIGHT: 125%; PADDING-TOP: 5px" align=middle width="274" bgcolor="white">
								<table cellpadding="0" cellspacing="0" width="100%">
								<tr>
									<td width="100%"><p align="left"><input type=file name=file<?=$i.$j?> style="WIDTH: 100%" class="input"></td>
									<td width="125"></td>
								</tr>
								</table>
								<? } ?>
								<input type=hidden name=easymenucode<?=$i.$j?> value="<?=$menucode[$i][$j]?>">
								</td></tr>
<?
						}
						$cnt = count($button[$i]);
						for($k=0;$k<$cnt;$k++){
?>
							<tr>
								<TD class="td_con2" style="line-height:125%;letter-spacing:-0.5pt;" align=middle bgcolor="white" title='[<?=str_replace("'","",$button[$i][$k])?>]'><p align="left">[<?=titleCut(13,strip_tags($button[$i][$k]))?>] 이미지 등록</p></td>
								<? if(file_exists($imagepath.$menucode[$i][$k+$j].".gif")) { ?>
								<TD class=line style="PADDING-RIGHT: 5px; PADDING-LEFT: 5px; PADDING-BOTTOM: 5px; LINE-HEIGHT: 125%; PADDING-TOP: 5px" align=middle bgcolor="white">
								<table cellpadding="0" cellspacing="0" width="100%">
								<tr>
									<td width="100%"><p align="left"><input type=file name=file<?=$i.($k+$j)?> style="WIDTH: 100%" class="input"></td>
									<td width="125" style="padding-left:2px"><input type="button" value="삭제" class="submit1" onClick="delete_menuimage('<?=$menucode[$i][$k+$j]?>')"></td>
								</tr>
								</table>
								<? } else { ?>
								<TD class=line style="PADDING-RIGHT: 5px; PADDING-LEFT: 5px; PADDING-BOTTOM: 5px; LINE-HEIGHT: 125%; PADDING-TOP: 5px" align=middle width="274" bgcolor="white">
								<table cellpadding="0" cellspacing="0" width="100%">
								<tr>
									<td width="100%"><p align="left"><input type=file name=file<?=$i.($k+$j)?> style="WIDTH: 100%" class="input"></td>
									<td width="125"></td>
								</tr>
								</table>
								<? } ?>
								<input type=hidden name=easymenucode<?=$i.($k+$j)?> value="<?=$menucode[$i][$k+$j]?>">
								</td>
							</tr>
<?
						}
?>
							<input type=hidden name=num<?=$i?> value="<?=$k+$j?>">
							</TABLE>
							</td>
						</tr>
						<tr>
							<td width="100%">
							<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
							<TR>
								<TD class="table_cell" align="center" width="518"><p>도움말</p></TD>
							</TR>
							<TR>
								<TD background="images/table_con_line.gif"></TD>
							</TR>
							<TR>
								<TD align="center" valign="top" style="padding:5pt;" width="524"><?=$mess[$i]?></TD>
							</TR>
							<TR>
								<TD background="images/table_top_line.gif"></TD>
							</TR>
							</TABLE>
							</td>
						</tr>
						</table>
						</div>
<?
				}
?>
						<div id=layer5 style="margin-left:0;display:hide; display:none;border-style:solid; border-width:0; border-color:black;background:#FFFFFF;padding:0;">
						<table cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td width="100%">
							<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
							<TR>
								<TD><IMG SRC="images/sub4_stitle_img1.gif" WIDTH=20 HEIGHT=24 ALT=""></TD>
								<TD width="100%" background="images/sub4_stitle_bg.gif" class="font_white"><B><FONT color=#ffffff>[배너] 메뉴 이미지 등록</FONT></B></TD>
								<TD><IMG SRC="images/sub4_stitle_img2.gif" WIDTH="20" HEIGHT=24 ALT=""></TD>
							</TR>
							</TABLE>
							</td>
						</tr>
						<tr>
							<td width="100%">
							<TABLE cellSpacing="1" cellPadding=0 width="100%" border=0 bgcolor="#DEDEDE">
							<TR bgColor=#f0f0f0>
								<TD class="table_cell" style="PADDING-RIGHT: 5px; PADDING-LEFT: 5px; PADDING-BOTTOM: 5px; PADDING-TOP: 5px" align=middle width="96" bgcolor="#F9F9F9">왼쪽 메뉴명</TD>
								<TD class="table_cell" style="PADDING-RIGHT: 5px; PADDING-LEFT: 5px; PADDING-BOTTOM: 5px; PADDING-TOP: 5px" align=middle width="133" bgcolor="#F9F9F9">해당 이미지명</TD>
								<TD class="table_cell" style="PADDING-RIGHT: 5px; PADDING-LEFT: 5px; PADDING-BOTTOM: 5px; PADDING-TOP: 5px" align=middle width="274" bgcolor="#F9F9F9">해당 이미지 등록<span class="font_blue">(JPG/GIF 파일_150K)</span></TD>
							</TR>
							<TR>
								<TD class=lineleft style="PADDING-RIGHT: 5px; PADDING-LEFT: 5px; PADDING-BOTTOM: 5px; LINE-HEIGHT: 125%; PADDING-TOP: 5px" align=middle width="96" bgcolor="white"><B>배너</B></TD>
								<TD class="td_con2" style="line-height:125%;" align=middle width="434" bgcolor="white" colspan="2"><p align="left">배너 등록 및 관리는<br><span class="font_orange" style="letter-spacing:-0.5pt;">&quot;<a href="shop_logobanner.php">상점관리 &gt; 쇼핑몰환경설정 &gt; 로고/배너관리</a>&quot;</span>에서 하시면 됩니다.<br>Easy디자인에서는 [배너영역]의 노출 유무와 위치 순서만을 조정할 수 있습니다.</p></TD>
							</TR>
							</TABLE>
							</td>
						</tr>
						</table>
						</div>
						<div id=layer6 style="margin-left:0;display:hide; display:none;border-style:solid; border-width:0; border-color:black;background:#FFFFFF;padding:0;">
						<table cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td width="100%">
							<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
							<TR>
								<TD><IMG SRC="images/sub4_stitle_img1.gif" WIDTH=20 HEIGHT=24 ALT=""></TD>
								<TD width="100%" background="images/sub4_stitle_bg.gif" class="font_white"><B><FONT color=#ffffff>[이벤트/고객알림] 메뉴 이미지 등록</FONT></B></TD>
								<TD><IMG SRC="images/sub4_stitle_img2.gif" WIDTH="20" HEIGHT=24 ALT=""></TD>
							</TR>
							</TABLE>
							</td>
						</tr>
						<tr>
							<td width="100%">
							<TABLE cellSpacing="1" cellPadding=0 width="100%" border=0 bgcolor="#DEDEDE">
							<TR bgColor=#f0f0f0>
								<TD class="table_cell" style="PADDING-RIGHT: 5px; PADDING-LEFT: 5px; PADDING-BOTTOM: 5px; PADDING-TOP: 5px" align=middle width="96" bgcolor="#F9F9F9">왼쪽 메뉴명</TD>
								<TD class="table_cell" style="PADDING-RIGHT: 5px; PADDING-LEFT: 5px; PADDING-BOTTOM: 5px; PADDING-TOP: 5px" align=middle width="133" bgcolor="#F9F9F9">해당 이미지명</TD>
								<TD class="table_cell" style="PADDING-RIGHT: 5px; PADDING-LEFT: 5px; PADDING-BOTTOM: 5px; PADDING-TOP: 5px" align=middle width="274" bgcolor="#F9F9F9">해당 이미지 등록<span class="font_blue">(JPG/GIF 파일_150K)</span></TD>
							</TR>
							<TR>
								<TD class=lineleft style="PADDING-RIGHT: 5px; PADDING-LEFT: 5px; PADDING-BOTTOM: 5px; LINE-HEIGHT: 125%; PADDING-TOP: 5px;letter-spacing:-0.5pt;" align=middle width="96" bgcolor="white"><B>이벤트/고객알림</B></TD>
								<TD class="td_con2" style="line-height:125%;letter-spacing:-0.5pt;" align=middle width="434" bgcolor="white" colspan="2"><p align="left">자유 디자인의 등록&amp;관리는<br><span class="font_orange">&quot;<a href="shop_mainleftinform.php">상점관리 &gt; 쇼핑몰환경설정 &gt; 왼쪽고객알림</a>&quot;</span>에서 하시면 됩니다.<br>Easy디자인에서는[자유 디자인]의 노출 유무와 위치 순서만을 조정할 수 있습니다.</p></TD>
							</TR>
							</TABLE>
							</td>
						</tr>
						</table>
						</div>
						</td>
					</tr>
					</table>
					</TD>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td align="center">
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="195"><p>&nbsp;</p></TD>
					<td width="553" style="letter-spacing:-0.5pt;"><p><?if($_shopdata->menu_type!="menue") {?><input type=checkbox name=okdesign value="Y" style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none"><span class="font_orange"><b>Easy 디자인을 쇼핑몰에 곧바로 반영합니다.(템플릿, 개별디자인 모두 해제됨)</b><br>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;이미 적용중일 때는 체크박스 생성되지 않습니다.<br>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;* 사용중일 때 디자인 수정 - [적용하기] 클릭하면 쇼핑몰에 곧바로 반영됩니다.<br>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;* 미사용일 때 디자인 수정 - 체크박스를 체크하지 않은 상태에서 [적용하기] 클릭하면 저장만 됩니다.</span><br><? }?></TD>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td><p>&nbsp;</p></td>
			</tr>
			<tr>
				<td align="center">
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="195"><p>&nbsp;</p></TD>
					<td width="553"><p align=center><a href="javascript:CheckForm('update');"><img src="images/botteon_save.gif" width="113" height="38" border="0" vspace="5"></a></TD>
				</tr>
				</table>
				</td>
			</tr>
			</form>
			<tr>
				<td align="center" height="30"><p>&nbsp;</p></td>
			</tr>
			<tr>
				<td align="center">
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/manual_top1.gif" WIDTH=15 HEIGHT=45 ALT=""></TD>
					<TD><IMG SRC="images/manual_title.gif" WIDTH=113 HEIGHT=45 ALT=""></TD>
					<TD width="100%" background="images/manual_bg.gif"></TD>
					<TD background="images/manual_bg.gif"></TD>
					<TD><IMG SRC="images/manual_top2.gif" WIDTH=18 HEIGHT=45 ALT=""></TD>
				</TR>
				<TR>
					<TD background="images/manual_left1.gif"><IMG SRC="images/manual_left1.gif" WIDTH=15 HEIGHT="5" ALT=""></TD>
					<TD COLSPAN=3 width="100%" valign="top" style="padding-top:8pt; padding-bottom:8pt; padding-left:4pt;"  class="menual_bg">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td width="100%"><span class="font_dotline">Easy 디자인 해제 방법</span></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td width="100%" class="space_top" style="letter-spacing:-0.5pt;">- 템플릿으로 변경 : 메인템플릿 선택(Easy 디자인, 개별디자인이 모두 해제되고 선택된 템플릿의 상단으로 변경)</td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td width="100%" class="space_top" style="letter-spacing:-0.5pt;">- 개별디자인으로 변경 : <a href="javascript:parent.topframe.GoMenu(2,'design_option.php');"><span class="font_blue">디자인관리 > 웹FTP 및  개별적용 선택 > 개별디자인 적용선택</span></a> 메뉴에서<br>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>&nbsp;&nbsp;&nbsp;</b>[상단+왼쪽 동시 적용][상단만 적용] 선택 - 개별디자인 내용으로 변경됩니다.<br>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[적용안함] 선택 - 사용중인 템플릿으로 변경됩니다.
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
			<tr><td height="50"></td></tr>
			</table>
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
</table>
<script>
document.form1.inmenu.selectedIndex=0;
change_layer(document.form1.inmenu.options[0].value);
</script>

<?=$onload?>

<? INCLUDE "copyright.php"; ?>