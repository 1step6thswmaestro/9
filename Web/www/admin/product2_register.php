<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
INCLUDE ("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "pr-1";
$MenuCode = "product";
if (!$_usersession->isAllowedTask($PageCode)) {
	INCLUDE ("AccessDeny.inc.php");
	exit;
}
#########################################################

$prcode=$_POST["prcode"];
if(strlen($prcode)==18) {
	$code=substr($prcode,0,12);
	$codeA=substr($code,0,3);
	$codeB=substr($code,3,3);
	$codeC=substr($code,6,3);
	$codeD=substr($code,9,3);
}

$imagepath=$Dir.DataDir."shopimages/giftbg/";

if($_POST['mode']=='add') {	

	for($i=1;$i<4;$i++){			

		if(!eregi("none",$_FILES["userfile".$i]['tmp_name']) && $_FILES["userfile".$i]['tmp_name']) {
			
			$ext = strtolower(substr($_FILES["userfile".$i]['name'],strlen($_FILES["userfile".$i]['name'])-3,3));
			$bg_name = "gift_bg_0{$i}.{$ext}";
			if (file_exists($imagepath.$bg_name)) {
				unlink($imagepath.$bg_name);
			}

			if ($ext=="gif" || $ext=="jpg") {
				move_uploaded_file($_FILES["userfile".$i]['tmp_name'],$imagepath.$bg_name);
				chmod($imagepath.$bg_name,0664);
			} 
		}
	}			
}

?>

<? INCLUDE "header.php"; ?>

<script type="text/javascript" src="lib.js.php"></script>
<SCRIPT LANGUAGE="JavaScript">
<!--

function ProductListReload(_code) {
	document.form2.mode.value="";
	document.form2.code.value=_code;
	document.form2.target="ListFrame";
	document.form2.action="product_register.list.php";
	document.form2.submit();
}

function ProductModify(prdtcode) {
	document.form2.mode.value="";
	document.form2.code.value=prdtcode.substring(0,12);
	document.form2.prcode.value=prdtcode;
	document.form2.target="AddFrame";
	document.form2.action="product2_register.add.php";
	document.form2.submit();

	document.form2.prcode.value="";
}

//-->
</SCRIPT>
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
			<? include ("menu_product.php"); ?>
			</td>

			<td></td>
			<td valign="top">


<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td height="29" colspan="3">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 상품관리 &gt;카테고리/상품관리 &gt; <span class="2depth_select">상품권 등록/수정/삭제</span></td>
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


			<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/product_register_title_r.gif" ALT=""></TD>
					</tr><tr>
					<TD width="100%" background="images/title_bg.gif" height=21></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height="3"></td></tr>
			<tr>
				<td>
					<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
						<TR>
							<TD style="padding-left:10px;" class="notice_blue">
								- 상품권을 등록/수정/삭제할 수 있습니다.<br />
								- 상품권 <span class="font_orange" style="font-size:11px;">배경이미지를 등록하지 않을경우 <b>기본 배경이미지</b>가 적용</span>됩니다.
							</TD>
						</TR>
					</TABLE>
				</td>
			</tr>
			<tr><td height="10"></td></tr>
			<tr>
				<td>
					<form name="form1" action="<?=$_SERVER[PHP_SELF]?>" method="post" enctype="multipart/form-data">
					<input type="hidden" name="mode" value="add" />
					<table cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td width="100%" bgcolor="#EDEDED" style="padding:4pt;">
								<table cellpadding="0" cellspacing="0" width="100%" bgcolor="white">
									<tr>
										<td width="100%">
											<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
												<TR>
													<TD width="100%" height="30" background="images/blueline_bg.gif" align="center"><b><font color="#555555">상품권 배경이미지 등록</font></b></TD>
												</TR>
												<TR>
													<TD width="100%" background="images/table_con_line.gif"><img src="images/table_con_line.gif" width="4" height="1" border="0"></TD>
												</TR>
												<tr>
													<td>
														<table cellpadding="0" cellspacing="0" width="100%">
															<colgroup>
																<col width="180"></col>
																<col width=""></col>
															</colgroup>
															<tr>
																<td class="table_cell"><img src="images/icon_point5.gif" width="8" height="11" border="0" />기본 배경이미지</td>
																<td class="td_con1" colspan="3" style="padding:10px;"><img src="/images/design/giftcard_bg.gif" width="150" alt="" /></td>
															</tr>
															<TR>
																<TD colspan="4" background="images/table_con_line.gif"></TD>
															</TR>
															<TR>
																<TD class="table_cell"><img src="images/icon_point5.gif" width="8" height="11" border="0">대 이미지</TD>
																<TD class="td_con1" colspan="3">
																<input type=file name="userfile1" style="WIDTH: 400px" class="input">
																<span class="font_orange">(권장 이미지 : 222 X 105)</span>&nbsp;&nbsp;	
																<? 
																	if(file_exists($imagepath."gift_bg_01.jpg")) $files1 = $imagepath."gift_bg_01.jpg";
																	else if(file_exists($imagepath."gift_bg_01.gif")) $files1 = $imagepath."gift_bg_01.gif";
																	if($files1) {
																		echo "<img src='{$files1}' width=40 height=40>";
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
																<input type=file name="userfile2" style="WIDTH: 400px" class="input">
																<span class="font_orange">(권장 이미지 : 222 X 105)</span>	&nbsp;&nbsp;	
																<? 
																	if(file_exists($imagepath."gift_bg_02.jpg")) $files2 = $imagepath."gift_bg_02.jpg";
																	else if(file_exists($imagepath."gift_bg_02.gif")) $files2 = $imagepath."gift_bg_02.gif";
																	if($files2) {
																		echo "<img src='{$files2}' width=40 height=40>";
																	}
																?>
																</TD>
															</TR>
															<TR>
																<TD colspan="4" background="images/table_con_line.gif"></TD>
															</TR>
															<TR>
																<TD class="table_cell"><img src="images/icon_point5.gif" width="8" height="11" border="0">소 이미지</TD>
																<TD class="td_con1" colspan="3">
																<input type=file name="userfile3" style="WIDTH: 400px" class="input">
																<span class="font_orange">(권장 이미지 : 222 X 105)</span>	&nbsp;&nbsp;	
																<? 
																	if(file_exists($imagepath."gift_bg_03.jpg")) $files3 = $imagepath."gift_bg_03.jpg";
																	else if(file_exists($imagepath."gift_bg_03.gif")) $files3 = $imagepath."gift_bg_03.gif";
																	if($files3) {
																		echo "<img src='{$files3}' width=40 height=40>";
																	}
																?>		
																</TD>
															</TR>
															<TR>
																<TD colspan="4" background="images/table_con_line.gif"></TD>
															</TR>
														</table>
													</td>
												</tr>
											</TABLE>
										</td>
									</tr>
								</table>
							</TD>
						</tr>
						<TR>
							<TD colspan="4" align="center" style="padding-top:20px;"><a href="#" onclick="document.form1.submit();"><img src="images/botteon_save.gif"></a></TD>
						</TR>
					</table>
					</form>
				</td>
			</tr>
			<tr><td height="20"></td></tr>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%" height="200">
				<tr>
					<td width="100%" valign="top" height="100%"><DIV style="position:relative;z-index:1;width:100%;height:100%;bgcolor:#FFFFFF;"><IFRAME name="ListFrame" src="product2_register.list.php" width=100% height=200 frameborder=0 align=TOP scrolling="no" marginheight="0" marginwidth="0"></IFRAME></div></td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td height="10"></td>
			</tr>
			
			<tr>
				<td align="center"><IFRAME name="AddFrame" src="product2_register.add.php" width=100% height=0 frameborder=0 align=TOP scrolling="no" marginheight="0" marginwidth="0"></IFRAME></td>
			</tr>	
			<IFRAME name="HiddenFrame" src="<?=$Dir?>blank.php" width=0 height=0 frameborder=0 align=TOP scrolling="no" marginheight="0" marginwidth="0"></IFRAME>
			</form>
			<form name=form2 action="" method=post>
			<input type=hidden name=mode>
			<input type=hidden name=code>
			<input type=hidden name=prcode>
			</form>
			<tr>
				<td height="20"></td>
			</tr>
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
					<TD background="images/manual_left1.gif"><IMG SRC="images/manual_left1.gif" WIDTH=15 HEIGHT="5" ALT=""></TD>
					<TD COLSPAN=3 width="100%" valign="top" class=menual_bg style="padding-top:8pt; padding-bottom:8pt; padding-left:4pt;">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td width="701" class="space_top">
						"상품권 선물 시 자동발송되는 인증번호는 MMS사용여부를 설정해주셔야 인증번호가 
지인의 핸드폰으로 자동발송됩니다"<br>
    설정바로가기 :<a href="javascript:parent.topframe.GoMenu(4,'market_smsconfig.php');"><span class="font_blue"> SMS(MMS)발송/관리 > "MMS사용유무"설정</span></a>에서 확인해주세요

						
						</td>
					</tr>
					</table>
					</TD>
					<TD background="images/manual_right1.gif"><IMG SRC="images/manual_right1.gif" WIDTH=18 HEIGHT="2" ALT=""></TD>
				</TR>
				<TR>
					<TD><IMG SRC="images/manual_left2.gif" WIDTH=15 HEIGHT=8 ALT=""></TD>
					<TD COLSPAN=3 background="images/manual_down.gif"><IMG SRC="images/manual_down.gif" WIDTH="4" HEIGHT=8 ALT=""></TD>
					<TD><IMG SRC="images/manual_right2.gif" WIDTH=18 HEIGHT=8 ALT=""></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height="50"></td>
			</tr>
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

<? INCLUDE "copyright.php"; ?>