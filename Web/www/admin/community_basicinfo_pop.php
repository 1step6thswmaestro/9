<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
INCLUDE ("access.php");

$board=$_POST["board"];

if(strlen($_ShopInfo->getId())==0 || strlen($board)==0){
	echo "<script>alert('정상적인 경로로 접근하시기 바랍니다.');window.close();</script>";
	exit;
}

$sql = "SELECT * FROM tblboardadmin WHERE board='".$board."' ";
$result=mysql_query($sql,get_db_conn());
$data=mysql_fetch_object($result);
mysql_free_result($result);

if(!$data) {
	echo "<script>alert(\"해당 게시판이 존재하지 않습니다.\");window.close();</script>";
	exit;
}


$btype=substr($data->board_skin,0,1);
unset($btypename);
if($btype=="L") $btypename="일반형 게시판";
else if($btype=="W") $btypename="웹진형 게시판";
else if($btype=="I") $btypename="앨범형 게시판";
else if($btype=="B") $btypename="블로그형 게시판";

$prqnaboardname="";
if($btype=="L") {
	$sql = "SELECT etcfield FROM tblshopinfo ";
	$result=mysql_query($sql,get_db_conn());
	$row=mysql_fetch_object($result);
	$etcfield=$row->etcfield;
	mysql_free_result($result);

	$prqnaboard=getEtcfield($etcfield,"PRQNA");
	if(strlen($prqnaboard)>0) {
		$sql = "SELECT board_name FROM tblboardadmin ";
		$sql.= "WHERE board='".$prqnaboard."' ";
		$result=mysql_query($sql,get_db_conn());
		if($row=mysql_fetch_object($result)) {
			$prqnaboardname=str_replace("<script>","",str_replace("</script>","",$row->board_name));
		} else {
			$etcfield=setEtcfield($etcfield,"PRQNA","");
			$prqnaboard="";
		}
		mysql_free_result($result);
	}
}

$mode=$_POST["mode"];

$subCategory=$_POST["subCategory"];
$board_name=$_POST["board_name"];
$board_width=$_POST["board_width"];
$list_num=$_POST["list_num"];
$page_num=$_POST["page_num"];
$max_filesize=$_POST["max_filesize"];
$use_imgresize="N";
$img_maxwidth=$_POST["img_maxwidth"];
$img_align=$_POST["img_align"];
$passwd=$_POST["passwd"];
$grant_write=$_POST["grant_write"];
$grant_view=$_POST["grant_view"];
$group_code=$_POST["group_code"];
$use_reply=$_POST["use_reply"];
$grant_reply=$_POST["grant_reply"];
$use_comment=$_POST["use_comment"];
$grant_comment=$_POST["grant_comment"];
$use_lock=$_POST["use_lock"];
$writer_gbn=(int)$_POST["writer_gbn"];
$prqna_gbn=$_POST["prqna_gbn"];
$admin_icon = $_POST['admin_icon'];

$secuCmt = $_POST['secuCmt'];
$fileYN = $_POST['fileYN'];
$onlyCmt = $_POST['onlyCmt'];

$linkboard = $_POST['linkboard'];
/* 게시판 리스트 타입 */
$list_customer = $list_community= $list_etc = "N";
if(_array($_POST['listtype'])){
	foreach($_POST['listtype'] AS $val){
		switch($val){
			case "CUS":
				$list_customer = "Y";
			break;
			case "COM":
				$list_community = "Y";
			break;
			case "ETC":
				$list_etc = "Y";
			break;
		}
	}
}
/* 게시판 리스트 타입 */

if($mode=="modify" && strlen($board)>0) {
	if(strlen($board_width)==0) $board_width=690;
	if(strlen($list_num)==0) $list_num=20;
	if(strlen($page_num)==0) $page_num=10;
	if(strlen($max_filesize)==0) $max_filesize=2;
	if(strlen($img_maxwidth)==0) $img_maxwidth=650;
	if(strlen($use_lock)==0) $use_lock="N";
	if(strlen($use_reply)==0) $use_reply="Y";
	if(strlen($use_comment)==0) $use_comment="Y";
	if(strlen($use_imgresize)==0) $use_imgresize="N";
	if(strlen($img_align)==0) $img_align="center";
	if(strlen($grant_reply)==0) $grant_reply="N";
	if(strlen($admin_icon) == 0) $admin_icon = "Y";
	if(strlen($secuCmt) == 0) $secuCmt = "N";
	if(strlen($fileYN) == 0) $fileYN = "N";
	if(strlen($onlyCmt) == 0) $onlyCmt = "N";
	if(strlen($linkboard) == 0) $linkboard = 0;

	$sql = "UPDATE tblboardadmin SET ";
	$sql.= "subCategory		= '".$subCategory."', ";
	$sql.= "board_name		= '".$board_name."', ";
	$sql.= "passwd			= '".$passwd."', ";
	$sql.= "board_width		= '".$board_width."', ";
	$sql.= "list_num		= '".$list_num."', ";
	$sql.= "page_num		= '".$page_num."', ";
	$sql.= "writer_gbn		= '".$writer_gbn."', ";
	$sql.= "max_filesize	= '".$max_filesize."', ";
	$sql.= "img_maxwidth	= '".$img_maxwidth."', ";
	$sql.= "img_align		= '".$img_align."', ";
	$sql.= "use_lock		= '".$use_lock."', ";
	$sql.= "use_reply		= '".$use_reply."', ";
	$sql.= "use_comment		= '".$use_comment."', ";
	$sql.= "use_imgresize	= '".$use_imgresize."', ";
	$sql.= "group_code		= '".$group_code."', ";
	$sql.= "grant_write		= '".$grant_write."', ";
	$sql.= "grant_view		= '".$grant_view."', ";
	$sql.= "grant_reply		= '".$grant_reply."', ";
	$sql.= "admin_icon		= '".$admin_icon."', ";
	$sql.= "grant_comment	= '".$grant_comment."', ";
	/* 게시판 리스트 타입 */
	$sql.= "list_customer	= '".$list_customer."', ";
	$sql.= "list_community	= '".$list_community."', ";
	$sql.= "list_etc	= '".$list_etc."', ";
	/* 게시판 리스트 타입*/
	$sql.= "secuCmt = '".$secuCmt."', ";
	$sql.= "fileYN = '".$fileYN."', ";
	$sql.= "onlyCmt = '".$onlyCmt."', ";
	$sql.= "linkboard = '".$linkboard."' ";
	$sql.= "WHERE board='".$board."' ";
	$update=mysql_query($sql,get_db_conn());
	if($update) {
		if($use_comment=="N") {
			mysql_query("DELETE FROM tblboardcomment WHERE board='".$board."'",get_db_conn());
		}
		if($btype=="L") {
			if($prqna_gbn=="Y") {
				if($prqnaboard!=$board) {
					$etcfield=setEtcfield($etcfield,"PRQNA",$board);
				}
			} else {
				if($prqnaboard==$board) {
					$etcfield=setEtcfield($etcfield,"PRQNA","");
				}
			}
		}
		echo "<script>alert(\"게시판 기본기능 설정이 완료되었습니다.\");opener.location.reload();window.close();</script>";
		exit;
	} else {
		$onload="<script>alert(\"게시판 기본기능 설정중 오류가 발생하였습니다.\");</script>";
		$data->subCategory=$subCategory;
		$data->board_name=$board_name; $data->passwd=$passwd; $data->board_width=$board_width;
		$data->list_num=$list_num; $data->page_num=$page_num; $data->writer_gbn=$writer_gbn;
		$data->max_filesize=$max_filesize; $data->img_maxwidth=$img_maxwidth; $data->img_align=$img_align;
		$data->use_lock=$use_lock; $data->use_reply=$use_reply; $data->use_comment=$use_comment;
		$data->use_imgresize=$use_imgresize; $data->group_code=$group_code; $data->grant_write=$grant_write;
		$data->grant_view=$grant_view; $data->grant_reply=$grant_reply; $data->grant_comment=$grant_comment;
	}
}

if($prqnaboard==$board) $prqna_gbn="Y";





// 말머리 추가
if ( strlen ( $board ) > 0 AND $mode == "subCategoryInput" ) {
	$subCateSQL = "INSERT `tblboardSubCategory` SET `board` = '".$board."' , `title` = '".$_POST['subCategoryTitle']."' ;";
	$subCateRes = mysql_query($subCateSQL,get_db_conn());
}

// 말머리 삭제
$idx = $_POST['idx'];
if ( strlen ( $board ) > 0 AND $mode == "subCategoryDelete" ) {
	$subCateSQL = "DELETE FROM `tblboardSubCategory` WHERE `idx` = '".$idx."' LIMIT 1 ;";
	$subCateRes = mysql_query($subCateSQL,get_db_conn());
}

?>

<html>
<head>
<meta http-equiv='Content-Type' content='text/html;charset=euc-kr'>
<title>게시판 기본기능 설정</title>
<link rel="stylesheet" href="style.css" type="text/css">
<style>td {line-height:14pt}</style>
<script type="text/javascript" src="lib.js.php"></script>
<SCRIPT LANGUAGE="JavaScript">
<!--
document.onkeydown = CheckKeyPress;
document.onkeyup = CheckKeyPress;
function CheckKeyPress() {
	ekey = event.keyCode;
	if(ekey==38 || ekey==40 || ekey==112 || ekey==17 || ekey==18 || ekey==25 || ekey==122 || ekey==116) {
		try {
			event.keyCode = 0;
			return false;
		} catch(e) {}
	}
}

function PageResize() {
	var oWidth = 630;
	var oHeight = 600;

	window.resizeTo(oWidth,oHeight);
}

<?if($btype=="L"){?>
function check_qnaboard(gbn) {
<?if(strlen($prqnaboard)>0 && $prqnaboard!=$board){?>
	if(confirm("현재 \"<?=$prqnaboardname?>\" 게시판을 상품QNA로 사용중입니다.\n\n현재 게시판을 상품QNA로 설정하시겠습니까?")) {
		document.form1.prqna_gbn[0].checked=true;
	} else {
		document.form1.prqna_gbn[1].checked=true;
	}
<?}?>
}
<?}?>

function CheckForm(form) {
	if(form.board_name.value.length==0) {
		alert("게시판 제목을 입력하세요.");
		form.board_name.focus();
		return;
	}


	var listcount = false;
	for(var i=0; i<form['listtype[]'].length;i++){
		if(form['listtype[]'][i].checked){
			listcount= true;
			break;
		}
	}
	
	if(listcount===false){
		if(confirm("노출영역이 선택되지 않아 기본적으로 노출되지 않습니다.\n계속진행 하시겠습니까?")){
	
		}else{
			return;
		}
	}
	if(form.board_width.value.length==0) {
		alert("게시판 넓이를 입력하세요.");
		form.board_width.focus();
		return;
	}
	if(!IsNumeric(form.board_width.value)) {
		alert("게시판 넓이는 숫자만 입력 가능합니다.");
		form.board_width.focus();
		return;
	}
	if(form.list_num.value.length==0) {
		alert("게시글 목록수를 입력하세요.");
		form.list_num.focus();
		return;
	}
	if(!IsNumeric(form.list_num.value)) {
		alert("게시판 목록수는 숫자만 입력 가능합니다.");
		form.list_num.focus();
		return;
	}
	if(form.page_num.value==0) {
		alert("페이지 목록수를 입력하세요.");
		form.page_num.focus();
		return;
	}
	if(!IsNumeric(form.page_num.value)) {
		alert("페이지 목록수는 숫자만 입력 가능합니다.");
		form.page_num.focus();
		return;
	}
	if(form.passwd.value.length==0) {
		alert("게시판 관리 비밀번호를 입력하세요.");
		form.passwd.focus();
		return;
	}
	form.mode.value="modify";
	form.submit();
}

function putBoardname(boardname) {
	document.form1.board_name.value = boardname;
}

//-->
</SCRIPT>
</head>
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 oncontextmenu="return false" style="overflow-x:hidden;" ondragstart="return false" onselectstart="return false" oncontextmenu="return false" onLoad="PageResize();">

<TABLE WIDTH="630" BORDER=0 CELLPADDING=0 CELLSPACING=0 style="table-layout:fixed;" id=table_body>
<TR>
	<TD>
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td><IMG SRC="images/community_list_sort_fun1.gif" ALT=""></td>
		<td width="100%" background="images/member_mailallsend_imgbg.gif">&nbsp;</td>
	</tr>
	</table>
	</TD>
</TR>
<TR>
	<TD style="padding:6pt;">


	<table cellpadding="0" cellspacing="0" width="100%">
	<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post>
	<input type=hidden name=mode>
	<input type=hidden name=board value="<?=$board?>">
	<table border=0 cellpadding=0 cellspacing=0 width=100%>
	<tr>
		<td width="100%">
		<TABLE cellSpacing=0 cellPadding=0 width="584" border=0>
		<col width=120></col>
		<col width=></col>
		<TR>
			<TD colspan=2 background="images/table_top_line.gif"></TD>
		</TR>
		<TR>
			<TD colspan="2" background="images/table_con_line.gif"></TD>
		</TR>
		<TR>
			<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">게시판 형태<br></TD>
			<TD class="td_con1">&nbsp;<?=$btypename?><span class="font_orange">(수정불가)</span>
				<?
					if( ereg( "I|L|W",$btype) ) {
				?>
				<br />&nbsp;링크형 게시판 사용 <input type="checkbox" name="linkboard" value="1" <?=($data->linkboard?"checked":"")?>>
				<br />&nbsp;* 링크형 게시판은 내용없이 바로 URL로 이동 할 수 있습니다.
				<br />&nbsp;* URL입력이 활성화 됩니다.
				<?
					}
				?>
			</TD>
		</TR>
		<TR>
			<TD colspan="2" background="images/table_con_line.gif"></TD>
		</TR>
		<TR>
			<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">게시판 제목<br></TD>
			<TD class="td_con1">&nbsp;<INPUT maxLength="40" size=40 value="" name=board_name class="select_selected" style=width:98%><br><span class="font_blue" style="font-size:11px;letter-spacing:-0.5pt;">&nbsp;* 게시판 제목은 HTML로 작성이 가능합니다.&nbsp;</span></TD>
		</TR>
		<script>putBoardname("<?=addslashes($data->board_name)?>");</script>
		<TR>
			<TD colspan="2" background="images/table_con_line.gif"></TD>
		</TR>
		
		<!-- 게시판 리스트 타입 시작-->
		<TR>
			<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">노출영역<br></TD>
			<TD class="td_con1">&nbsp;
				<input type="checkbox" id="list_customer" name="listtype[]" value="CUS" <?=($data->list_customer == "Y")?"checked":""?>/><label for="list_customer">고객센터</label>
				<input type="checkbox" id="list_community" name="listtype[]" value="COM" <?=($data->list_community == "Y")?"checked":""?>/><label for="list_community">커뮤니티</label>
				<input type="checkbox" id="list_etc" name="listtype[]" value="ETC" <?=($data->list_etc == "Y")?"checked":""?>/><label for="list_etc">개별페이지</label>
				<p class="font_blue" style="margin:0px;padding:0px;font-size:11px;letter-spacing:-1px;">&nbsp;* 체크사항이 없을시 좌측 디자인이 출력되지 않습니다.</p>
			</TD>
		</TR>
		<TR>
			<TD colspan="2" background="images/table_con_line.gif"></TD>
		</TR>
		<!-- 게시판 리스트 타입 끝 -->

		<TR>
			<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">말머리<br></TD>
			<TD class="td_con1">&nbsp;<textarea name="subCategory" class="select_selected" style="height:100px;width:98%;"><?=$data->subCategory?></textarea><br><span class="font_blue" style="font-size:11px;letter-spacing:-0.5pt;">&nbsp;* 게시판 말머리 옵션 ,(쉼표) 구분하여 각 20자 미만으로 작성 합니다.&nbsp;</span>
			</TD>
		</TR>
		<TR>
			<TD colspan="2" background="images/table_con_line.gif"></TD>
		</TR>




		<tr>
			<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">게시판 넓이<br></TD>
			<TD class="td_con1">&nbsp;<INPUT onkeyup="return strnumkeyup(this);" maxLength="5" size=5 value="<?=$data->board_width?>" name=board_width class="input"> <span class="font_blue" style="font-size:11px;letter-spacing:-0.5pt;">&nbsp;* 권장 : 690픽셀(100이하이면 %로 설정됩니다.)</span></TD>
		</tr>
		<TR>
			<TD colspan="2" background="images/table_con_line.gif"></TD>
		</TR>
		<tr>
			<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">게시글 목록수<br></TD>
			<TD class="td_con1">&nbsp;<INPUT onkeyup="return strnumkeyup(this);" maxLength="5" size=5 value="<?=$data->list_num?>" name=list_num class="input"> <span class="font_blue" style="font-size:11px;letter-spacing:-0.5pt;">&nbsp;* 권장 : 10</span></TD>
		</tr>
		<TR>
			<TD colspan="2" background="images/table_con_line.gif"></TD>
		</TR>
		<tr>
			<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">페이지 목록수<br></TD>
			<TD class="td_con1">&nbsp;<INPUT onkeyup="return strnumkeyup(this);" maxLength="5" size=5 value="<?=$data->page_num?>" name=page_num class="input"> <span class="font_blue" style="font-size:11px;letter-spacing:-0.5pt;">&nbsp;* 권장 : 10</span></TD>
		</tr>
		<TR>
			<TD colspan="2" background="images/table_con_line.gif"></TD>
		</TR>
		<tr>
			<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">게시판 첨부파일<br></TD>
			<TD class="td_con1">&nbsp;<select name=max_filesize class="select">
			<option value="1" <?if($data->max_filesize=="1")echo"selected";?>>100KB</option>
			<option value="2" <?if($data->max_filesize=="2")echo"selected";?>>200KB</option>
			<option value="3" <?if($data->max_filesize=="3")echo"selected";?>>300KB</option>
			<option value="4" <?if($data->max_filesize=="4")echo"selected";?>>400KB</option>
			<option value="5" <?if($data->max_filesize=="5")echo"selected";?>>500KB</option>
			<option value="6" <?if($data->max_filesize=="6")echo"selected";?>>600KB</option>
			<option value="7" <?if($data->max_filesize=="7")echo"selected";?>>700KB</option>
			<option value="8" <?if($data->max_filesize=="8")echo"selected";?>>800KB</option>
			<option value="9" <?if($data->max_filesize=="9")echo"selected";?>>900KB</option>
			<option value="10" <?if($data->max_filesize=="10")echo"selected";?>>&nbsp;&nbsp;1MB</option>
			<option value="20" <?if($data->max_filesize=="20")echo"selected";?>>&nbsp;&nbsp;2MB</option>
			<option value="50" <?if($data->max_filesize=="50")echo"selected";?>>&nbsp;&nbsp;5MB</option>
			</select>&nbsp;* 권장 : 200KB<br>
			</TD>
		</tr>
		<TR>
			<TD colspan="2" background="images/table_con_line.gif"></TD>
		</TR>
		<TR>
			<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">게시판 이미지 설정<br></TD>
			<TD class="td_con1">
				&nbsp;이미지 최대 사이즈: <INPUT onkeyup="return strnumkeyup(this);" maxLength="5" size=5 value="<?=$data->img_maxwidth?>" name=img_maxwidth class="input">픽셀
				/ 이미지 정렬 :
				<INPUT type=radio id="idx_img_align0" name=img_align value="center" <?if($data->img_align=="center")echo"checked";?> ><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_img_align0>가운데 정렬</label>
				<INPUT type=radio id="idx_img_align1" name=img_align value="left" <?if($data->img_align=="left")echo"checked";?> ><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_img_align1>왼쪽정렬</label>
				<br>
				<span class="font_blue" style="font-size:11px;letter-spacing:-0.5pt;">&nbsp;* 이미지 최대 사이즈는 게시판의 넓이보다 작은 사이즈로 설정하시기 바랍니다.&nbsp;</span>
			</TD>
		</TR>
		<TR>
			<TD colspan="2" background="images/table_con_line.gif"></TD>
		</TR>
		<tr>
			<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">게시판 비밀번호<br></TD>
			<TD class="td_con1">&nbsp;<INPUT onkeyup="return strnumkeyup(this);" maxLength="20" size="20" value="<?=$data->passwd?>" name=passwd class="select_selected"><span class="font_blue" style="letter-spacing:-0.5pt;">&nbsp;* 비밀번호가 유출되지 않도록 주의하시기 바랍니다.</span></TD>
		</tr>
		<TR>
			<TD colspan="2" background="images/table_con_line.gif"></TD>
		</TR>
		<tr>
			<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">게시판 접근권한</TD>
			<TD class="td_con1">
			&nbsp;게시물 쓰기 :
			<select name=grant_write class="select" size="1">
			<option value="N" <?if($data->grant_write=="N")echo"selected";?>>회원/비회원</option>
			<option value="Y" <?if($data->grant_write=="Y")echo"selected";?>>회원전용</option>
			<option value="A" <?if($data->grant_write=="A")echo"selected";?>>관리자전용</option>
			</select><br />

			&nbsp;게시물 보기 :
 			<select name=grant_view class="select" size="1">
			<option value="N" <?if($data->grant_view=="N")echo"selected";?>>회원/비회원</option>
			<option value="U" <?if($data->grant_view=="U")echo"selected";?>>비회원목록조회</option>
			<option value="Y" <?if($data->grant_view=="Y")echo"selected";?>>비회원조회불가</option>
			</select>
			<br />

			&nbsp;특정등급만 읽고 쓰기 :
			<select name=group_code style="width:180px" class="select">
			<option value="">해당 등급을 선택하세요.</option>
<?
			$sql = "SELECT group_code,group_name FROM tblmembergroup ";
			$result=mysql_query($sql,get_db_conn());
			while($row=mysql_fetch_object($result)) {
				if($data->group_code==$row->group_code) {
					echo "<option value=\"".$row->group_code."\" selected>".$row->group_name."</option>";
				} else {
					echo "<option value=\"".$row->group_code."\">".$row->group_name."</option>";
				}
			}
			mysql_free_result($result);
?>
			</select><br><span class="font_blue" style="font-size:11px;letter-spacing:-0.5pt;">&nbsp;* 관리자전용 선택시 해당 회원등급은 조회 및 내용보기만 가능.&nbsp;</span>
			</TD>
		</tr>
		<TR>
			<TD colspan="2" background="images/table_con_line.gif"></TD>
		</TR>
		<?if($btype=="L"){?>
		<tr>
			<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">게시판 답변기능<br></TD>
			<TD class="td_con1">
			<input type=radio id="idx_use_reply0" name=use_reply value="Y" <?if($data->use_reply=="Y")echo"checked";?> onclick="this.form.grant_reply.disabled=false;"> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_use_reply0>사용함</label>
			(답변쓰기 :
			<select name=grant_reply class="select" <?if($data->use_reply=="N")echo"disabled";?>>
			<option value="N" <?if($data->grant_reply=="N")echo"selected";?>>회원/비회원</option>
			<option value="Y" <?if($data->grant_reply=="Y")echo"selected";?>>회원전용</option>
			<option value="A" <?if($data->grant_reply=="A")echo"selected";?>>관리자전용</option>
			</select>
			)
			<INPUT id="idx_use_reply1"  onclick="this.form.grant_reply.disabled=true;" type=radio value=N name=use_reply <?if($data->use_reply=="N")echo"checked";?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_use_reply1>사용안함</label>
			</TD>
		</tr>
		<?}else{?>
		<input type=hidden name=use_replay value="N">
		<input type=hidden name=grant_reply value="N">
		<?}?>
		<TR>
			<TD colspan="2" background="images/table_con_line.gif"></TD>
		</TR>
		<tr>
			<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">게시판 댓글기능<br></TD>
			<TD class="td_con1">
			<input type=radio id="idx_use_comment0" name=use_comment value="Y" <?if($data->use_comment=="Y")echo"checked";?> onclick="this.form.grant_comment.disabled=false;"> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_use_comment0>사용함</label>
			(댓글쓰기 :
			<select name=grant_comment  class="select" <?if($data->use_comment=="N")echo"disabled";?>>
			<option value="N" <?if($data->grant_comment=="N")echo"selected";?>>회원/비회원</option>
			<option value="Y" <?if($data->grant_comment=="Y")echo"selected";?>>회원전용</option>
			<option value="A" <?if($data->grant_comment=="A")echo"selected";?>>관리자전용</option>
			</select>
			)
			<INPUT id="idx_use_comment1"  onclick="this.form.grant_comment.disabled=true;alert('댓글기능 사용안함으로 설정할 경우 기존 등록된 댓글 데이타는 일괄 삭제되며 복구 불가능하게 됩니다.');" type=radio value=N name=use_comment <?if($data->use_comment=="N")echo"checked";?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_use_comment1>사용안함</label>
			<br><span class="font_blue" style="font-size:11px;letter-spacing:-0.5pt;">&nbsp;* 사용안함으로 설정할 경우 기존 등록된 댓글 데이타는 일괄 삭제되며 복구 불가능하게 됩니다.</span>


			<br><span class="font_orange" style="font-size:11px;letter-spacing:-0.5pt;">&nbsp;* 비밀댓글/단일댓글/댓글 이미지 업로더 는 SNS댓글 사용시 적용되지 않습니다.</span>

			<br>
			<strong>비밀댓글 </strong>:
			<INPUT id="secuCmtY" type="radio" value="Y" name="secuCmt" <?if($data->secuCmt=="Y")echo"checked";?> onclick="this.form.grant_comment.disabled=false; idx_use_comment0.checked=true; grant_comment.options[1].selected=true;"><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for="secuCmtY">사용함</label>
			<INPUT id="secuCmtN" type="radio" value="N" name="secuCmt" <?if($data->secuCmt=="N")echo"checked";?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for="secuCmtN">사용안함</label>
			<br><span class="font_blue" style="font-size:11px;letter-spacing:-0.5pt;">&nbsp;* 비밀댓글사용시 댓글은 작성자와 관리자만 확인가능합니다.</span>
			<br><span class="font_blue" style="font-size:11px;letter-spacing:-0.5pt;">&nbsp;* 비밀댓글사용은 회원전용 댓글쓰기가 가능해야 합니다.</span>


			<br>
			<strong>단일댓글 </strong>:
			<INPUT id="onlyCmtY" type="radio" value="Y" name="onlyCmt" <?if($data->onlyCmt=="Y")echo"checked";?> onclick="this.form.grant_comment.disabled=false; idx_use_comment0.checked=true; grant_comment.options[1].selected=true;"><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for="onlyCmtY">사용함</label>
			<INPUT id="onlyCmtN" type="radio" value="N" name="onlyCmt" <?if($data->onlyCmt=="N")echo"checked";?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for="onlyCmtN">사용안함</label>
			<br><span class="font_blue" style="font-size:11px;letter-spacing:-0.5pt;">&nbsp;* 단일댓글은 회원전용 댓글쓰기가 가능해야 합니다.</span>
			<br><span class="font_blue" style="font-size:11px;letter-spacing:-0.5pt;">&nbsp;* 단일댓글은 회원당 1회만 댓글이 가능하며 삭제가 불가 합니다.</span>



			<br>
			<strong>댓글 이미지 업로더 </strong>:
			<INPUT id="fileY" type="radio" value="Y" name="fileYN" <?if($data->fileYN=="Y")echo"checked";?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for="fileY">사용함</label>
			<INPUT id="fileN" type="radio" value="N" name="fileYN" <?if($data->fileYN=="N")echo"checked";?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for="fileN">사용안함</label>
			<br><span class="font_blue" style="font-size:11px;letter-spacing:-0.5pt;">&nbsp;* 댓글에 이미지 업로드가 가능합니다.</span>

			</TD>
		</tr>
		<TR>
			<TD colspan="2" background="images/table_con_line.gif"></TD>
		</TR>
		<tr>
			<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">게시판 비밀글 기능<br></TD>
			<TD class="td_con1">
			<INPUT id="idx_use_lock0"  type=radio value=A name="use_lock" <?if($data->use_lock=="A")echo"checked";?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_use_lock0>고객 의무사용</label>
			<INPUT id="idx_use_lock1"  type=radio value=Y name="use_lock" <?if($data->use_lock=="Y")echo"checked";?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_use_lock1>고객 선택사용</label>
			<INPUT  id="idx_use_lock2"  type=radio value=N name="use_lock" <?if($data->use_lock=="N")echo"checked";?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_use_lock2>사용하지 않음</label>
			<br>
			<span class="font_blue" style="font-size:11px;letter-spacing:-0.5pt;">&nbsp;* 잠금기능이 설정된 게시물은 관리자 비밀번호를 입력하시면 조회가 가능합니다.</span>
			</TD>
		</tr>
		<TR>
			<TD colspan="2" background="images/table_con_line.gif"></TD>
		</TR>
		<tr>
			<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">작성자 표시구분<br></TD>
			<TD class="td_con1">
			<INPUT id="idx_writer_gbn0"  type=radio value=0 name="writer_gbn" <?if($data->writer_gbn=="0")echo"checked";?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_writer_gbn0>회원 이름</label>
			<INPUT id="idx_writer_gbn1"  type=radio value=1 name="writer_gbn" <?if($data->writer_gbn=="1")echo"checked";?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_writer_gbn1>회원아이디</label>
			<br>
			<span class="font_blue" style="font-size:11px;letter-spacing:-0.5pt;">&nbsp;* 게시판에 출력되는 회원의 작성자 표기 방식을 선택할 수 있습니다.</span></TD>
		</tr>
		<TR>
			<TD colspan="2" background="images/table_con_line.gif"></TD>
		</TR>
		<tr>
			<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">관리자 아이콘<br></TD>
			<TD class="td_con1">
			<INPUT id="idx_admin_icon0"  type=radio value=Y name="admin_icon" <?if($data->admin_icon=="Y")echo"checked";?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_admin_icon0>출력</label>
			<INPUT id="idx_admin_icon1"  type=radio value=N name="admin_icon" <?if($data->admin_icon=="N")echo"checked";?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_admin_icon1>미출력</label>
			<br>
			<span class="font_blue" style="font-size:11px;letter-spacing:-0.5pt;">&nbsp;* 게시판에 출력되는 관리자 접속 아이콘 출력 여부를 선택할 수 있습니다.</span></TD>
		</tr>
		<TR>
			<TD colspan="2" background="images/table_con_line.gif"></TD>
		</TR>
		<?if($btype=="L"){?>
		<tr>
			<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">상품 Q&A 기능</TD>
			<TD class="td_con1">
			<INPUT id=idx_prqna_gbn0 type=radio value="Y" name=prqna_gbn <?if($prqna_gbn=="Y")echo"checked";?> onclick="check_qnaboard('Y')"><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand" onmouseout="style.textDecoration='none'" for=idx_prqna_gbn0>현재 게시판을 상품Q&A로 사용함</LABEL>

			&nbsp;&nbsp;&nbsp;<INPUT id=idx_prqna_gbn1 type=radio name=prqna_gbn value="" <?if($prqna_gbn!="Y")echo"checked";?> onclick="check_qnaboard('')"><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand" onmouseout="style.textDecoration='none'" for=idx_prqna_gbn1>사용안함</LABEL>
			<br>
			<span class="font_blue" style="font-size:11px;letter-spacing:-0.5pt;">&nbsp;* 상품상세페이지의 상품Q&A 게시판으로 사용될 게시판을 선택할 수 있습니다.</span></TD>
		</tr>
		<TR>
			<TD colspan="2" background="images/table_con_line.gif"></TD>
		</TR>
		<?}?>
		<tr>
			<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">게시판 URL</TD>
			<TD class="td_con1"><b><span class="font_orange">/<?=RootPath.BoardDir?>board.php?board=<?=$board?></span></b></TD>
		</tr>
		<TR>
			<TD colspan=2 background="images/table_top_line.gif"></TD>
		</TR>
		</TABLE>
		</td>
	</tr>
	<tr><td height=10></td></tr>
	<tr>
		<td width="100%" align="center"><input type="image" src="images/bnt_apply.gif" width="76" height="28" border="0" vspace="10" border=0 onclick="CheckForm(this.form)"><a href="javascript:window.close()"><img src="images/btn_cancel.gif" width="76" height="28" border="0" vspace="10" border=0 hspace="2"></a></td>
	</tr>
	</form>
	</table>
	</TD>
</TR>
</TABLE>
</body>
</html>

<?=$onload?>