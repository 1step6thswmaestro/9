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

	$logoimagepath = "../m/upload/";
	$bannerimagepath = "../m/upload/";

	$limit_bannerimg = 512000; // 배너이미지 업로드 사이즈 제한
	$limit_etcSize = 153600; //로고 및 카피라이트 사이즈 제한
	$type=trim($_POST["type"]);

	function _getSizeConverter($size) {
		$unit_list = array("Bytes", "KB", "MB", "GB", "TB", "PB", "EB", "ZB", "YB");
		$set_sizeUnit = round($size/pow(1024, ($i = floor(log($size, 1024)))), 2) . $unit_list[$i];
		return $set_sizeUnit;
	}
	$msg_size =  _getSizeConverter($limit_bannerimg);  //배너이미지 용량
	$msg_etc_size = _getSizeConverter($limit_etcSize);
	
	//기본설정
	$use_mobile_site = $_POST["use_mobile_site"];
	$use_auto_redirection = $_POST["use_auto_redirection"];
	$use_cross_link = $_POST["use_cross_link"];
	$use_bank = $_POST["use_bank"];
	$use_creditcard = $_POST["use_creditcard"];
	$use_mobilephone = $_POST["use_mobilephone"];
	$use_mobile_qna = $_POST["use_mobile_qna"];
	$use_mobile_qna_write = $_POST["use_mobile_qna_write"];
	$skin = $_POST["skin"];
	$skin_css = $_POST["skin_css"];
	$color_css = $_POST["color_css"];
	$logo=$_FILES["logo"];
	$icon=$_FILES["icon"];
	$copyright_text = strip_tags($_POST["copyright_text"], '<a><br><br/>');
	$copyright_image = $_FILES["copyright_image"];
	//SNS 추가
	$sns_kakaotalk = isset($_POST["kakaotalk"])? $_POST["kakaotalk"]:'N';
	$sns_kakaostory = isset($_POST["kakaostory"])? $_POST["kakaostory"]:'N';
	$sns_facebook = isset($_POST["facebook"])? $_POST["facebook"]:'N';
	$sns_twitter = isset($_POST["twitter"])? $_POST["twitter"]:'N';
	
	$set_sns = $sns_kakaotalk.'|'.$sns_kakaostory.'|'.$sns_facebook.'|'.$sns_twitter;
	
	//배너
	$up_image=$_FILES["up_image"];
	$up_border=$_POST["up_border"];
	$up_url_type=$_POST["up_url_type"];
	$up_url=$_POST["up_url"];
	$up_target=$_POST["up_target"];
	$up_banner_loc=$_POST["up_banner_loc"];
	$place=$_POST["place"];
	$CurrentTime = date("YmdHis");


	if ($type=="up") {
		if ($logo[name]) {
			$logo_ext = strtolower(substr($logo[name],strlen($logo[name])-3,3));
			$fix_logo_name = "logo.".$logo_ext;
			if ($logo[size]>$limit_etcSize) { //1024 * 150
				echo '<script>alert("올리실 이미지 용량은 '.$msg_etc_size.' 이하의 파일만 가능합니다.");history.go(-1);</script>';exit;
			} else {
				move_uploaded_file($logo[tmp_name],$logoimagepath.$fix_logo_name);
				chmod($logoimagepath.$fix_logo_name,777);
				$sql_logo = "logo = '$fix_logo_name',";
			}
		}

		if ($icon[name]) {
			$icon_ext = strtolower(substr($icon[name],strlen($icon[name])-3,3));
			$fix_icon_name = "icon.".$icon_ext;

			if ($icon[size]>$limit_etcSize) {
				$onload = "<script>alert (\"올리실 이미지 용량은 150KB 이하의 파일만 가능합니다.\");</script>";
			}else{
				move_uploaded_file($icon[tmp_name],$logoimagepath.$fix_icon_name);
				@chmod($logoimagepath.$fix_icon_name,777);
				$sql_icon = "icon = '$fix_icon_name',";
			}
		}

		if ($copyright_image[name]) {
			$copyright_image_ext = strtolower(substr($copyright_image[name],strlen($copyright_image[name])-3,3));
			$fix_copyright_image_name = "copyright_image.".$copyright_image_ext;

			if ($copyright_image[size]>$limit_etcSize) {
				echo '<script>alert("올리실 이미지 용량은 '.$msg_etc_size.'이하의 파일만 가능합니다.");history.go(-1);</script>';exit;
			} else {
				move_uploaded_file($copyright_image[tmp_name],$logoimagepath.$fix_copyright_image_name);
				@chmod($logoimagepath.$fix_copyright_image_name,777);

				$sql_copyright_image = "copyright_image = '$fix_copyright_image_name',";
			}
		}
		$skin_css = $skin.".css";
		$sql = "UPDATE tblmobileconfig SET
				use_mobile_site = '$use_mobile_site',
				use_auto_redirection = '$use_auto_redirection',
				use_cross_link = '$use_cross_link',
				use_bank = '$use_bank',
				use_creditcard = '$use_creditcard',
				use_mobilephone = '$use_mobilephone',
				use_mobile_qna = '$use_mobile_qna',
				use_mobile_qna_write = '$use_mobile_qna_write',
				use_mobile_sns = '$set_sns',
				skin = '$skin',
				skin_css = '$skin_css',
				color_css =  '$color_css',
				$sql_logo 	$sql_icon  $sql_copyright_image
				copyright_text = '$copyright_text'
		";

		mysql_query($sql,get_db_conn());

		$onload = "<script>alert('정보 수정이 완료되었습니다.');</script>";

	} else if ($type=="logodel") {

		$query = "select logo from tblmobileconfig";
		$result = mysql_query($query);
		$row = mysql_fetch_array($result);

		@unlink($logoimagepath.$row['logo']);
		$onload="<script>alert ('쇼핑몰 로고 삭제가 완료되었습니다.');</script>";
		mysql_query("update tblmobileconfig set logo = ''");

	} else if ($type=="icondel") {

		$query = "select icon from tblmobileconfig";
		$result = mysql_query($query);
		$row = mysql_fetch_array($result);

		@unlink($logoimagepath.$row[icon]);
		$onload="<script>alert ('쇼핑몰 아이콘이 삭제가 완료되었습니다.');</script>";

		mysql_query("update tblmobileconfig set icon = ''");

	} else if ($type=="copyrightimagedel") {

		$query = "select copyright_image from tblmobileconfig";
		$result = mysql_query($query);
		$row = mysql_fetch_array($result);

		@unlink($logoimagepath.$row[icon]);
		$onload="<script>alert ('쇼핑몰 카피라이트 이미지 삭제가 완료되었습니다.');</script>";

		mysql_query("update tblmobileconfig set copyright_image = ''");

	} else if ($type=="bannerdel") {
		if ($up_url) {
			$sql = "SELECT image FROM tblmobilebanner ";
			$sql.= "WHERE date = '".$up_url."'";
			$result = mysql_query($sql,get_db_conn());
			if($row=mysql_fetch_object($result)) {
				if($row->image && file_exists($bannerimagepath.$row->image)) {
					@unlink($bannerimagepath.$row->image);
				}
			}
			mysql_free_result($result);
			$sql = "DELETE FROM tblmobilebanner WHERE date = '".$up_url."'";
			mysql_query($sql,get_db_conn());
			$onload = "<script>alert('배너 삭제가 완료되었습니다.');</script>";
		}
	} else if ($type=="banneradd") {

		if ($up_image[size]<=$limit_bannerimg){
			if($up_image[name] && $up_url) {
				$sql = "SELECT COUNT(*) as cnt FROM tblmobilebanner ";
				$result = mysql_query($sql,get_db_conn());
				$row = mysql_fetch_object($result);
				mysql_free_result($result);
				$cnt=(int)$row->cnt;
				if ($cnt<10) {
					$banner_name = "banner_".date(His)."_".$up_image[name];
					move_uploaded_file($up_image[tmp_name],$bannerimagepath.$banner_name);
					chmod($bannerimagepath.$banner_name,777);
					$sql = "INSERT tblmobilebanner SET ";
					$sql.= "date		= '".$CurrentTime."', ";
					$sql.= "image		= '".$banner_name."', ";
					$sql.= "border		= '".$up_border."', ";
					$sql.= "url_type	= '".$up_url_type."', ";
					$sql.= "url			= '".$up_url."', ";
					$sql.= "target		= '".$up_target."' ";


					mysql_query($sql,get_db_conn());
					$onload="<script>alert('배너 등록이 완료되었습니다.');</script>";
				} else {
					$onload="<script>alert('배너 등록은 최대 10개까지만 등록이 가능합니다.');</script>";
				}
			}
		}else{
			$onload="<script>alert('배너이미지 용량은 최대 ".$msg_size."까지 등록 가능합니다.');</script>";
		}
	} else if ($type=="bannersort") {
		$banner=explode(",",$place);
		$date1=date("Ym");
		$date=date("dHis");
		for($i=0;$i<count($banner);$i++){
			$date--;
			if (strlen($date)==7) {
				$date="0".$date;
			} else if (strlen($date)==6) {
				$date="00".$date;
			}
			$sql = "UPDATE tblmobilebanner SET date='$date1$date' ";
			$sql.= "WHERE date = '".$banner[$i]."'";
			mysql_query($sql,get_db_conn());
		}
	}

	$result = mysql_query("select * from tblmobileconfig");
	$row = mysql_fetch_array($result);

	//SNS 노출 설정 정보 
	if($row['use_mobile_sns'] != ''){
		$get_sns = explode('|',$row['use_mobile_sns']);
		$kakaotalk = isset($get_sns[0])? $get_sns[0]:'';
		$kakaostory = isset($get_sns[1])? $get_sns[1]:'';
		$facebook = isset($get_sns[2])? $get_sns[2]:'';
		$twitter = isset($get_sns[3])? $get_sns[3]:'';
	}
?>

<? INCLUDE "header.php"; ?>
<?=$onload?>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
	function CheckForm(type) {
		if (type=="logodel") {
			if (!confirm("쇼핑몰 로고를 삭제하시겠습니까?")) {
				return;
			}
		} else if (type=="icondel") {
			if (!confirm("쇼핑몰 아이콘을 삭제하시겠습니까?")) {
				return;
			}
		} else {
			if(form1.use_mobile_site[0].checked=="" && form1.use_mobile_site[1].checked=="") {
				alert("모바일용 쇼핑몰 사용여부를 선택해 주세요.");
				return;
			}

			if(form1.skin.value=="") {
				alert("skin 을 선택해 주세요.");
				return;
			}
		}
		form1.type.value=type;
		form1.submit();
	}
	function BannerDel(date) {
		if(confirm("배너를 삭제하시겠습니까?")) {

			form2.type.value="bannerdel";
			form2.up_url.value = date;
			form2.submit();
		}
	}

	function BannerAdd() {
		if(!form2.up_image.value){
			alert('배너 이미지를 등록하세요');
			form2.up_image.focus();
			return;
		}
		if(!form2.up_url.value){
			alert('배너에 연결할 URL를 입력하세요. \n(예: www.abc.com)');
			form2.up_url.focus();
			return;
		}
		form2.type.value="banneradd";
		form2.submit();
	}

	function BannerSort(cnt){
		arr_sort = new Array();
		var _val;
		for(i=1;i<=cnt;i++){
			_val=form2.bannerplace[i].options[form2.bannerplace[i].selectedIndex].value;
			if (arr_sort[_val]) {
				alert("배너 순서가 중복되거나 잘못되었습니다.");
				return;
			} else {
				arr_sort[_val] = form2.bannerdate[i].value;
			}
		}
		var result = arr_sort.join(",").substring(1);

		document.form2.place.value=result;
		document.form2.type.value="bannersort";
		document.form2.submit();
	}

	function setSkinCss(skin_css) {
		document.form1.skin_css.value = skin_css;
		document.getElementById("id_skin_css").style.display='none';
	}

	function setColorCss(skin_css) {
		document.form1.color_css.value = skin_css;
		document.getElementById("id_color_css").style.display='none';
	}

	function showPicker(str) {
		if(str=="skin") {
			document.getElementById("id_skin_css").style.display='';
		} else if (str=="color") {
			document.getElementById("id_color_css").style.display='';
		}
	}

	function BannerModify(tdate){
		var _form = document.bannerModify;
		var popurl = "./mobile_banner_modify.php";
		var popname = "modifypop";

		window.open(popurl,popname,"width=600,height=600");
		_form.bdate.value = tdate;
		_form.target = popname;
		_form.action = popurl;
		_form.submit();
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
									<? include ("menu_mobile.php"); ?>
								</td>
								<td></td>
								<td valign="top">
									<table cellpadding="0" cellspacing="0" width="100%">
										<tr>
											<td height="29" colspan="3">
												<table cellpadding="0" cellspacing="0" width="100%">
													<tr>
														<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 모바일샵 &gt; <span class="2depth_select">기본설정</span></td>
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
													<tr>
														<td height="8"></td>
													</tr>
													<tr>
														<td>
															<table width="100%" border=0 cellpadding=0 cellspacing=0>
																<tr>
																	<td><img src="images/mobile_stitle.gif" border="0" alt="모바일기본설정"></td>
																</tr>
																<tr>
																	<td width="100%" background="images/title_bg.gif" height="21"></TD>
																</tr>
															</table>
														</td>
													</tr>
													<tr>
														<td height="3"></td>
													</tr>
													<tr>
														<td style="padding-bottom:3pt;">
															<table width="100%" border="0" cellpadding="0" cellspacing="0">
																<tr>
																	<td><img src="images/distribute_01.gif"></td>
																	<td colspan="2" background="images/distribute_02.gif"></td>
																	<td><img src="images/distribute_03.gif"></td>
																</tr>
																<tr>
																	<td background="images/distribute_04.gif"></td>
																	<td class="notice_blue"><img src="images/distribute_img.gif" ></td>
																	<td width="100%" class="notice_blue">모바일사이트의 기본환경을 설정하실수 있습니다.</td>
																	<td background="images/distribute_07.gif"></td>
																</tr>
																<tr>
																	<td><img src="images/distribute_08.gif"></td>
																	<td colspan="2" background="images/distribute_09.gif"></td>
																	<td><img src="images/distribute_10.gif"></td>
																</tr>
															</table>
														</td>
													</tr>
													<tr>
														<td height="20"></td>
													</tr>
													<tr>
														<td>
															<table width="100%" border=0 cellpadding=0 cellspacing=0>
																<tr>
																	<td><img src="images/mobile_sstitle01.gif" border="0" alt="모바일사이트 기본설정"></td>
																	<td width="100%" background="images/shop_basicinfo_stitle_bg.gif"></td>
																	<td><img src="images/shop_basicinfo_stitle_end.gif" width=10 height=31 alt=""></td>
																</tr>
															</table>
														</td>
													</tr>
													<tr>
														<td height=3></td>
													</tr>
													<tr>
														<td>
															<!-- 환경설정 시작 -->
															<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post enctype="multipart/form-data">
																<input type="hidden" name="type" value="up">
																<table cellspacing=0 cellpadding=0 width="100%" border=0 style="border-top:1px solid #BBBBBB;">
																	<tr>
																		<td background="images/table_top_line.gif" colspan=2></td>
																	</tr>
																	<tr>
																		<td class="table_cell" width="180"><img src="images/icon_point2.gif" width="8" height="11" border="0">모바일용 쇼핑몰 사용 설정</td>
																		<td class="td_con1" >
																			<table  border="0" cellpadding="0" cellspacing="0">
																				<tr>
																					<td class="td_con1b">
																						<input type="radio" name="use_mobile_site" value="Y" <? if($row[use_mobile_site]=="Y") echo "checked";?>>사용<span class="font_orange2">('m.기존주소'로 접속)</span>
																					</td>
																				</tr>
																				<tr>
																					<td class="td_con1b">
																						<input type="radio" name="use_mobile_site"  value="N" <? if($row[use_mobile_site]=="N") echo "checked";?>>사용안함<span class="font_orange2">(모바일용 주소 접속시 pc용 화면으로 접속됨</span>)
																					</td>
																				</tr>
																			</table>
																		</td>
																	</tr>
																	<tr>
																		<td colspan="2" background="images/table_con_line.gif"></td>
																	</tr>
																	<tr>
																		<td class="table_cell" width="180"><img src="images/icon_point2.gif" width="8" height="11" border="0">모바일 접속시 자동 연결 설정</td>
																		<td class="td_con1">
																			<table  border="0" cellpadding="0" cellspacing="0">
																				<tr>
																					<td class="td_con1b">
																						<input type="radio" name="use_auto_redirection"  value="Y" <? if($row[use_auto_redirection]=="Y") echo "checked";?>>사용<span class="font_orange2">('www.기존주소' 입력시에도 모바일로 접속할 경우 모바일용 화면으로 자동으로 이동함)</span>
																					</td>
																				</tr>
																				<tr>
																					<td class="td_con1b">
																						<input type="radio" name="use_auto_redirection"  value="N" <? if($row[use_auto_redirection]=="N") echo "checked";?>>사용안함<span class="font_orange2">(모바일에서 'www.기존주소' 입력시 PC용 화면(기존 쇼핑몰 화면) 그대로 보여줌)</span>
																					</td>
																				</tr>
																			</table>
																		</td>
																	</tr>
																	<tr>
																		<td colspan="2" background="images/table_con_line.gif"></td>
																	</tr>
																	<tr>
																		<td class="table_cell" width="180"><img src="images/icon_point2.gif" width="8" height="11" border="0">PC화면 바로가기 설정</span></td>
																		<td class="td_con1">
																			<table  border="0" cellpadding="0" cellspacing="0">
																				<tr>
																					<td class="td_con1b">
																						<input type="radio" name="use_cross_link" value="Y" <? if($row[use_cross_link]=="Y") echo "checked";?>>사용<span class="font_orange2">(모바일샵 하단에 "PC버전" 버튼으로 노출됨)</span>
																					</td>
																				</tr>
																				<tr>
																					<td class="td_con1b">
																						<input type="radio" name="use_cross_link" value="N" <? if($row[use_cross_link]=="N") echo "checked";?>>사용안함<span class="font_orange2">(모바일샵 하단에 "PC버전" 버튼이 노출안됨)</span>
																					</td>
																				</tr>
																			</table>
																		</td>
																	</tr>
																	<tr>
																		<td colspan="2" background="images/table_con_line.gif"></td>
																	</tr>
																	<tr>
																		<td class="table_cell" width="180"><img src="images/icon_point2.gif" width="8" height="11" border="0">결제수단 선택</td>
																		<td class="td_con1" >
																			<table  border="0" cellpadding="0" cellspacing="0" width="100%">
																				<tr>
																					<td width="90" class="td_con1b" >
																						<input type="checkbox" name="use_bank" value="Y" <? if($row[use_bank]=="Y") echo "checked";?>>무통장입금
																					</td>
																				</tr>
																				<tr>
																					<td class="td_con1b" >
																						<input type="checkbox" name="use_creditcard" value="Y" <? if($row[use_creditcard]=="Y") echo "checked";?>>신용카드(<span class="font_orange2">스마트폰 전용결제 서비스를 신청하셔야 합니다.</span>)
																					</td>
																				</tr>
																			</table>
																		</td>
																	</tr>
																	<tr>
																		<td colspan="2" background="images/table_con_line.gif"></td>
																	</tr>
																	<tr>
																		<td class="table_cell" width="180"><img src="images/icon_point2.gif" width="8" height="11" border="0">Q&A 노출 설정</td>
																		<td class="td_con1" >
																			<table  border="0" cellpadding="0" cellspacing="0">
																				<tr>
																					<td class="td_con1b">
																						<input type="radio" name="use_mobile_qna" value="Y" <? if($row[use_mobile_qna]=="Y") echo "checked";?>>사용<span class="font_orange2">('모바일샵에 상품Q&A 게시판 노출함)</span>
																					</td>
																				</tr>
																				<tr>
																					<td class="td_con1b">
																						<input type="radio" name="use_mobile_qna"  value="N" <? if($row[use_mobile_qna]=="N") echo "checked";?>>사용안함<span class="font_orange2">('모바일 샵에 상품Q&A 게시판 노출 하지 않음')</span>
																					</td>
																				</tr>
																			</table>
																		</td>
																	</tr>
																	<tr>
																		<td colspan="2" background="images/table_con_line.gif"></td>
																	</tr>
																	<tr>
																		<td class="table_cell" width="180"><img src="images/icon_point2.gif" width="8" height="11" border="0">Q&A 쓰기 설정</td>
																		<td class="td_con1" >
																			<table  border="0" cellpadding="0" cellspacing="0">
																				<tr>
																					<td class="td_con1b">
																						<input type="radio" name="use_mobile_qna_write" value="Y" <? if($row[use_mobile_qna_write]=="Y") echo "checked";?>>사용<span class="font_orange2">('모바일샵에 상품Q&A 쓰기 사용함)</span>
																					</td>
																				</tr>
																				<tr>
																					<td class="td_con1b">
																						<input type="radio" name="use_mobile_qna_write"  value="N" <? if($row[use_mobile_qna_write]=="N") echo "checked";?>>사용안함<span class="font_orange2">('모바일 샵에 상품Q&A 게시판 노출 하지 않음')</span>
																					</td>
																				</tr>
																			</table>
																		</td>
																	</tr>
																	<tr>
																		<td colspan="2" background="images/table_con_line.gif"></td>
																	</tr>
																	<tr>
																		<td class="table_cell" width="180"><img src="images/icon_point2.gif" width="8" height="11" border="0">SNS 노출 설정</td>
																		<td class="td_con1" >
																			<table  border="0" cellpadding="0" cellspacing="0">
																				<tr>
																					<td class="td_con1b">
																						<input type="checkbox" name="kakaotalk" value="Y" <? if($kakaotalk=="Y") echo "checked";?>/><span>카카오톡</span>
																						<input type="checkbox" name="kakaostory" value="Y" <? if($kakaostory=="Y") echo "checked";?>/><span>카카오스토리</span>
																						<input type="checkbox" name="facebook" value="Y" <? if($facebook=="Y") echo "checked";?>/><span>페이스북</span>
																						<input type="checkbox" name="twitter" value="Y" <? if($twitter=="Y") echo "checked";?>/><span>트위터</span>
																						<span class="font_orange2">(모바일샵 상품상세보기 화면에 노출됩니다.)</span>
																					</td>
																				</tr>
																			</table>
																		</td>
																	</tr>
																	<tr>
																		<td colspan="2" background="images/table_con_line.gif"></td>
																	</tr>
																	<tr>
																		<td class="table_cell" width="180"><img src="images/icon_point2.gif" width="8" height="11" border="0">스킨설정</td>
																		<td class="td_con1" >
																			<select name="skin">
																				<option value="default" <?if($row[skin] == "default"){?> selected <?}?>>Default</option>
																			</select>
																		</td>
																	</tr>
																	<tr>
																		<td colspan="2" background="images/table_con_line.gif"></td>
																	</tr>
																	<tr>
																		<td class="table_cell" width="180"><img src="images/icon_point2.gif" width="8" height="11" border="0">로고등록</td>
																		<td class="td_con1" >
																			<table width="100%">
																				<tr>
																					<td><input type=file name="logo" style="width:480px; font-size:12px; border:1px solid #DCDCDC; background-color:#ffffff;"></td>
																				</tr>
																				<tr>
																					<td class=linebottomleft style="PADDING-RIGHT: 5px; PADDING-BOTTOM: 5px; PADDING-TOP: 5px" align=left width="100%" bgColor=#ffffff>
																						<p>
																							<? if ($row[logo]) {?>
																								<img src="<?=$logoimagepath?><?=$row[logo]?>" border=0 style="border-width:1pt; border-color:rgb(235,235,235); border-style:solid;"> <a href="javascript:CheckForm('logodel');"><img src="images/btn_del.gif" width="50" height="22" border="0" hspace="3"></a>
																							<? } else { ?>
																								등록된 로고가 없습니다.
																							<? } ?>
																						</p>
																					</td>
																				</tr>
																			</table>
																		</td>
																	</tr>
																	<tr>
																		<td colspan="2" background="images/table_con_line.gif"></td>
																	</tr>
																	<tr>
																		<td class="table_cell" width="180"><img src="images/icon_point2.gif" width="8" height="11" border="0">카피라이트</td>
																		<td class="td_con1" >
																			<table width="100%">
																				<tr>
																					<td>
																						<input type=file name="copyright_image" style="width:480px; font-size:12px;border:1px solid #DCDCDC;background-color:#ffffff;">
																					</td>
																				</tr>
																				<tr>
																					<td class="td_con1b" style="PADDING-RIGHT: 5px; PADDING-BOTTOM: 5px; PADDING-TOP: 5px" align=left width="100%" bgColor=#ffffff>
																						<p>
																							<? if ($row[copyright_image]) { ?>
																							<img src="<?=$logoimagepath?><?=$row[copyright_image]?>" border=0 style="border-width:1pt; border-color:rgb(235,235,235); border-style:solid;"> <a href="javascript:CheckForm('copyrightimagedel');"><img src="images/btn_del.gif" width="50" height="22" border="0" hspace="3"></a>
																							<? } else { ?>
																							등록된 카피라이트 이미지가 없습니다.
																							<? } ?>
																						</p>
																					</td>
																				</tr>
																				<tr>
																					<td class="td_con1b" style="PADDING-RIGHT: 5px; PADDING-LEFT: 0; PADDING-BOTTOM: 5px; PADDING-TOP: 5px" align=left width="100%" bgColor=#ffffff>
																						<!-- <input type=text name="copyright_text" style="WIDTH: 100%" class="input" value="<?=$row[copyright_text]?>"> -->
																						<textarea name="copyright_text" style="width:90%; height:65px;" class="input"><?=$row[copyright_text]?></textarea>
																						<div><span class="font_orange2">(텍스트로 copyright를 대체합니다. 카피라이트 이미지가 있을 경우, 이미지 출력을 우선합니다)</span></div>
																					</td>
																				</tr>
																			</table>
																		</td>
																	</tr>
																	<tr>
																		<td colspan="2" background="images/table_con_line.gif"></td>
																	</tr>
																	<tr>
																		<td background="images/table_top_line.gif" colspan=2></td>
																	</tr>
																</table>
															<!-- 환경설정 끝 -->
															</form>
														</td>
													</tr>
													<tr>
														<td align="center">
															<a href="javascript:CheckForm('up')"><img src="images/botteon_save.gif" id="uploadButton" width="113" height="38" border="0"></a>
														</td>
													</tr>
													<tr><td height="30"></td></tr>
													<tr>
														<td>
															<!-- 배너 등록 시작-->
															<form name="form2" action="<?=$_SERVER[PHP_SELF]?>" method="post" enctype="multipart/form-data">
																<input type="hidden" name="type">
																<input type="hidden" name="place">
																<input type="hidden" name="bannerplace">
																<input type="hidden" name="bannerdate">
																<table cellpadding=0 cellspacing=0 border=0 width=100%>
																	<tr>
																		<td>
																			<table width="100%" border=0 cellpadding=0 cellspacing=0>
																				<tr>
																					<td><img src="images/shop_logobanner_stitle2.gif" width="192" height=31 alt=""></td>
																					<td width="100%" background="images/shop_basicinfo_stitle_bg.gif"></td>
																					<td><img src="images/shop_basicinfo_stitle_end.gif" width=10 height=31 alt=""></td>
																				</tr>
																			</table>
																		</td>
																	</tr>
																	<tr><td height=3></td></tr>
																	<tr>
																		<td>
																			<table border="0" cellpadding="0" cellspacing="0">
																				<tr>
																					<td background="images/distribute_04.gif"></td>
																					<td class="notice_blue" valign="top"><img src="images/distribute_img.gif" ></td>
																					<td width="100%" class="notice_blue">
																						배너순서 변경시 위치가 변경되는 두 배너의 순서 값을 모두 변경하셔야 합니다.<br />
																						(예: "순서1"의 배너를 "순서3" 으로 변경할 경우 - 1번 배너의 설정값 3, 3번 배너의 설정값 1)
																					</td>
																					<td background="images/distribute_07.gif"></td>
																				</tr>
																			</table>
																		</td>
																	</tr>
																	<tr><td height=5></td></tr>
																	<tr>
																		<td>
																			<table cellspacing=0 cellpadding=0 width="100%" border=0>
																				<tr>
																					<td background="images/table_top_line.gif" width="760" colspan="4"></td>
																				</tr>
																				<tr>
																					<td class="table_cell" width="33"><p align="center">순서</td>
																					<td class="table_cell1"><p align="center">배너이미지</td>
																					<td class="table_cell1" width="439"><p align="center">링크주소</td>
																					<td class="table_cell1" width="80"><p align="center">수정/삭제</td>
																				</tr>
																				<tr>
																					<td colspan="4" width="760" background="images/table_con_line.gif"></td>
																				</tr>
																				<?
																					$sql0 = "SELECT COUNT(*) as cnt FROM tblmobilebanner ";
																					$result = mysql_query($sql0,get_db_conn());
																					$row = mysql_fetch_object($result);
																					mysql_free_result($result);
																					$cnt = $row->cnt;

																					$sql = "SELECT * FROM tblmobilebanner ORDER BY date DESC";
																					$result = mysql_query($sql,get_db_conn());
																					$count=1;
																					while($row=mysql_fetch_object($result)){
																					$image = $row->image;
																					$url = $row->url;
																				?>
																				<tr>
																					<td class="td_con" noWrap align=middle width=60>
																						<p align="center">
																							<select name=bannerplace class="select">
																							<? for($i=1;$i<=$cnt;$i++){
																								echo "<option value=\"".$i."\"";
																								if($i==$count) {
																									echo " selected";
																								}
																								echo ">".($i);
																							   }
																							?>
																							</select>
																							<input type="hidden" name=bannerdate value="<?=$row->date?>">
																						</p>
																					</td>
																					<td class="td_con1" width="151"><img src="<?=$bannerimagepath.$image?>" border="<?=$row->border?>" width=320 height="100"class="imgline"></td>
																					<td class="td_con1" width="447">
																						<a href=http<?=($row->url_type=="S"?"s":"")?>://<?=$url?> target=<?=$row->target?>><font color=#0000a0>http<?=($row->url_type=="S"?"s":"")?>://<?=$url?></font></a>
																					</td>
																					<td class="td_con1" width="88">
																						<p align="center">
																							<a href="javascript:BannerModify('<?=$row->date?>');"><img src="images/btn_edit.gif" border="0" alt="" /></a>
																							<a href="javascript:BannerDel('<?=$row->date?>');"><img src="images/btn_del.gif" border="0" alt="" /></a>
																						</p>
																					</td>
																				</tr>
																				<tr>
																					<td colspan="4" width="760" background="images/table_con_line.gif"></td>
																				</tr>
																				<?
																					$count++;
																					}
																					mysql_free_result($result);
																					if($cnt==0) {
																				?>
																				<tr>
																					<td class=lineleft colspan=4 align=center height=30>
																						<font color=#383838>등록된 배너가 없습니다.</font>
																					</td>
																				</tr>
																				<tr>
																					<td colspan="4" width="760" background="images/table_con_line.gif"></td>
																				</tr>
																				<? } ?>
																			</table>
																		</td>
																	</tr>
																	<tr>
																		<td background="images/table_top_line.gif"></td>
																	</tr>
																	<tr><td height="20"></td></tr>
																	<tr>
																		<td align="center">
																			<?
																				if ($cnt > 0) {
																					echo "<a href=\"javascript:BannerSort('$cnt');\"><img src=\"images/botteon_save.gif\" width=\"113\" height=\"38\" border=\"0\" alt=\"\" /></a>\n";
																				}
																			?>
																		</td>
																	</tr>
																	<tr><td height="30"></td></tr>
																	<tr>
																		<td>
																			<table width="100%" border=0 cellpadding=0 cellspacing=0>
																				<tr>
																					<td><img src="images/mobile_sstitle03.gif" alt="" /></td>
																					<td width="100%" background="images/shop_basicinfo_stitle_bg.gif"></td>
																					<td><img src="images/shop_basicinfo_stitle_end.gif" width=10 height=31 alt=""></td>
																				</tr>
																			</table>
																		</td>
																	</tr>
																	<tr>
																		<td height=3></td>
																	</tr>
																	<tr>
																		<td style="padding-top:3pt; padding-bottom:3pt;">
																			<table width="100%" border=0 cellpadding=0 cellspacing=0>
																				<tr>
																					<td><img src="images/distribute_01.gif"></td>
																					<td colspan=2 background="images/distribute_02.gif"></td>
																					<td><img src="images/distribute_03.gif"></td>
																				</tr>
																				<tr>
																					<td background="images/distribute_04.gif"></td>
																					<td class="notice_blue" valign="top"><img src="images/distribute_img.gif" ></td>
																					<td width="100%" class="notice_blue">
																						<p>1) <b>GIF(gif), JPG(jpg), PNG(png)파일만</b> 등록 가능합니다.<br>
																							2) 이미지 사이즈의 경우 <b>가로기준으로 100% 처리됩니다.</b><br>3) 이미지 용량 <b><?=$msg_size?> 이하</b> 만 업로드 가능합니다.
																						</p>
																					</td>
																					<td background="images/distribute_07.gif"></td>
																				</tr>
																				<tr>
																					<td><img src="images/distribute_08.gif"></td>
																					<td colspan=2 background="images/distribute_09.gif"></td>
																					<td><IMG SRC="images/distribute_10.gif"></td>
																				</tr>
																			</table>
																		</td>
																	</tr>
																	<tr>
																		<td>
																			<table cellpadding="0" cellspacing="0" width="100%">
																				<tr>
																					<td bgcolor="#ededed" style="padding:4pt;">
																						<table cellpadding="0" cellspacing="0" width="100%" bgcolor="white">
																							<tr>
																								<td width="100%">
																									<table cellspacing=0 cellpadding=0 width="100%" border=0>
																										<tr>
																											<td  colspan="2" height="35" background="images/blueline_bg.gif">
																												<p align="center">
																													<b><font color="#333333">배너등록하기</font></b>
																												</p>
																											</td>
																										</tr>
																										<tr>
																											<td colspan="2" width="760" background="images/table_con_line.gif"></td>
																										</tr>
																										<tr>
																											<td width="148" class="table_cell"><b><img src="images/icon_point2.gif" width="8" height="11" border="0"></b>배너 이미지</td>
																											<td  class="td_con1"> <input type=file name=up_image style="width:480px; font-size:12px;border:1px solid #DCDCDC;background-color:#ffffff;"></td>
																										</tr>
																										<tr>
																											<td colspan="2" width="760" background="images/table_con_line.gif"></td>
																										</tr>
																										<tr>
																											<td width="148" class="table_cell"><b><img src="images/icon_point2.gif" width="8" height="11" border="0"></b>연결 URL</td>
																											<td  class="td_con1">
																												http://
																												<input type=text name=up_url size="72"  onKeyUp="chkFieldMaxLen(200)" class="input" >
																											</td>
																										</tr>
																										<tr>
																											<td colspan="2" width="760" background="images/table_con_line.gif"></td>
																										</tr>
																											<input type="hidden" name="up_type" value="H" />
																											<input type="hidden" name="up_target" value="_self" />
																											<input type="hidden" name="up_border" value="0" />
																									</table>
																								</td>
																							</tr>
																						</table>
																					</td>
																				</tr>
																			</table>
																		</td>
																	</tr>
																	<tr>
																		<td style="padding-top:15pt;">
																			<p align="center">
																			<a href="javascript:BannerAdd();"><img src="images/botteon_insert.gif" border="0" alt="" /></a>
																		</td>
																	</tr>
																	<tr>
																		<td height="20"></td>
																	</tr>
																</table>

																<!-- 배너 등록 끝 -->
															</form>
														</td>
													</tr>
													<tr>
														<td height="50"></td>
													</tr>
													<tr>
														<td>
														<!-- 메뉴얼 시작 -->
															<table cellpadding=0 cellspacing=0 width=100%>
																<tr>
																	<td><img src="images/manual_top1.gif" width=15 height=45 alt=""></td>
																	<td><img src="images/manual_title.gif" width=113 height=45 alt=""></td>
																	<td width="100%" background="images/manual_bg.gif"></td>
																	<td background="images/manual_bg.gif"></td>
																	<td><img src="images/manual_top2.gif" width=18 height=45 alt=""></td>
																</tr>
																<tr>
																	<td background="images/manual_left1.gif"></td>
																	<td colspan=3 width="100%" valign="top"  style="padding-top:8pt; padding-bottom:8pt; padding-left:4pt;" class="menual_bg">
																			<table cellpadding="0" cellspacing="0" width="100%">
																				<tr>
																					<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
																					<td ><span class="font_dotline">모바일쇼핑몰 기본 환경설정</span></td>
																				</tr>
																				<tr>
																					<td width="20" align="right">&nbsp;</td>
																					<td  class="space_top">
																						- 모바일샵 Q&A 설정의 경우 노출설정만 가능하며 그외 권한 설정의 경우 PC버전과 동일합니다.<br/>
																						- 결제수단 선택에서 모바일샵에서 사용할 결제타입을 선택하시면 됩니다.<br/>
																						- 전자결제 사용이 결제수단 선택에서 선택되어있더라도 쇼핑몰 PG연동및, 모바일 전자결제 연동에서 "전자결제 사용함"으로 설정되지 않은 경우 주문서에서 전자결제 결제타입을 사용하실 수 없습니다.</br>
																						- 모바일샵 전자결제 연동의 경우 쇼핑몰 PG 연동 후 좌측 모바일샵 메뉴에서 "전자결제연동" 메뉴에서 설정하실 수 있습니다<br/>
																						- 전자결제란 신용카드, 가상계좌, 계좌이체 등을 말하며 현재 모바일샵에서는 신용카드만 지원합니다.<br/>
																						- 모바일샵 로고 및 카피라이트 이미지의 파일사이즈는 <?=$msg_etc_size?>로 제한됩니다.<br/>
																						- 로고이미지의 권장사이즈는 가로100px, 세로40px 이며 이와 다를경우 세로기준 40px로 고정됩니다.<br/>
																						- 모바일샵 copyright를 이미지로 등록하실 경우 사용자의 디바이스별 해상도에 따라 레이아웃 및 정렬 상태가 상이 할수 있으며 해당 부분으로 인한 문제의 경우 A/S지원 대상이 아니므로 텍스트 사용을 권장합니다.<br/>
																						- 모바일샵 copyright를 텍스트로 등록시 기본 가운데 정렬 되며, &ltbr&gt,&lta&gt 를 사용하실 수 있으며 이외 다른 태그는 지원 되지 않습니다.
																					
																					</td>
																					<tr>
																						<td width="20" align="right">&nbsp;</td>
																						<td  class="space_top">&nbsp; </td>
																					</tr>
																				</tr>
																			</table>
																		</td>
																	<td background="images/manual_right1.gif"><img src="images/manual_right1.gif" width=18 height="2" alt=""></td>
																</tr>
																<tr>
																	<td><img src="images/manual_left2.gif" width=15 height=8 alt=""></td>
																	<td colspan=3 background="images/manual_down.gif"><img src="images/manual_down.gif" width="4" height=8 alt=""></td>
																	<td><img src="images/manual_right2.gif" width=18 height=8 alt=""></td>
																</tr>
															</table>
														<!-- 메뉴얼 끝 -->
														</td>
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
										<tr>
											<td height="20"></td>
										</tr>
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
<style>
	form{margin:0px;padding:0px;border:0px;}
</style>
<form name="bannerModify" method="post">
	<input type="hidden" name="bdate" value=""/> 
</form>
<? INCLUDE "copyright.php"; ?>
