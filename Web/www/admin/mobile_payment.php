<?
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once($Dir."lib/shopdata.php");
	INCLUDE ("access.php");

	####################### 페이지 접근권한 check ###############
	$PageCode = "mo-1";
	$MenuCode = "mobile";
	if (!$_usersession->isAllowedTask($PageCode)) {
		INCLUDE ("AccessDeny.inc.php");
		exit;
	}
	#########################################################

	#### PG 데이타 세팅 ####
	$_ShopInfo->getPgdata();

	$pgid_info = GetEscrowType($_data->card_id);

	$mode = isset($_POST['mode'])? $_POST['mode'] : "";
	$interlock = isset($_POST['interlock'])? $_POST['interlock'] : "";
	$interlock_mode = isset($_POST['interlock_mode'])? $_POST['interlock_mode'] : "";
	$id = $pgid_info['ID'];
	$key = $pgid_info['KEY'];

	$isfile = $_SERVER[DOCUMENT_ROOT]."/authkey/pg";

	if(!is_file($isfile)){
		echo '<script>alert("전자결제 연동이 되어 있지 않습니다.\n해당 기능은 전자결제 연동 후 사용이 가능합니다.");history.go(-1);</script>';
		exit;
	}

	if(count($pgid_info) < 3){ //
		echo '<script>alert("전자결제 연동이 올바르지 않습니다.\n해당 기능은 전자결제 정상 연동 후 사용이 가능합니다.");history.go(-1);</script>';
		exit;
	}else{
		$pg_type = trim($pgid_info['PG']);
		$getSql = "SELECT * FROM tblmobilepg WHERE 1=1";
		$getResult = mysql_query($getSql, get_db_conn());
		$getRow = mysql_fetch_object($getResult);
		@mysql_free_result($get_result);

		unset($onInterlock);
		unset($offInterlock);
		unset($realMode);
		unset($testMode);

		switch($getRow->pg_use){ //모바일샵 전자결제 사용설정 Y:사용함, N:사용안함
			case "Y":
				$onInterlock = "checked";
				$offInterlock = "";
			break;
			case "N":
				$onInterlock = "";
				$offInterlock = "checked";
			break;
			default:
				$onInterlock = "";
				$offInterlock = "checked";
			break;
		}
		switch($getRow->pg_mode){ //연동모드 상태 R:실제연동, T:테스트연동
			case "R":
				$realMode = "checked";
				$testMode = "";
			break;
			case "T":
				$realMode = "";
				$testMode = "checked";
			break;
			default:
				$realMode = "";
				$testMode = "checked";
			break;
		}


		$addClass = "";
		switch($pg_type){
			case "A":
				$company = "KCP";
			break;
			case "B":
				$company = "LG U+(구 DACOM)";
			break;
			case "C":
				$company = "ALLTHEGATE : 모바일샵 전자결제가 현재 지원되지 않는 PG사입니다.";
				$addClass = "optionhide";
			break;
			case "D":
				$company = "INICIS : 모바일샵 전자결제가 현재 지원되지 않는 PG사입니다.";
				$addClass = "optionhide";
			break;
			case "E":
				$company = "NICE";
			break;
			case "G":
				$company = "All at";
			break;
			default:
				$company = "전자결제가 연동되지 않았거나 지원되지 않는 PG사입니다.";
				$addClass = "optionhide";
			break;
		}
	}
	// A -> KCP, B -> LG, C->allthegae, D->inicis

	if($mode == "update"){
		$setSql = "UPDATE tblmobilepg SET ";
		$setSql .= "pg_use= '".$interlock."', ";
		$setSql .= "pg_type= '".$pg_type."', ";
		$setSql .= "pg_id= '".$id."', ";
		$setSql .= "pg_key= '".$key."', ";
		$setSql .= "pg_mode= '".$interlock_mode."', ";
		$setSql .= "pg_date= '".date("YmdHi")."' ";
		$setSql .= "WHERE pg_section='mobile'";

		if(mysql_query($setSql,get_db_conn())){

			$filename = "mall.conf";
			$file_loc = $_SERVER[DOCUMENT_ROOT]."/m/paygate/B/lgdacom/conf/";
			$file = $file_loc.$filename;

			if($pg_type == "B" && $interlock == "Y"){ // PG사가 LGU+ 고 사용설정 될 경우 세팅


				if(is_file($file)){  //파일이 존재하면
					$confirm = fopen($file, "r");
					$str = "";

					if($confirm){
						while(!feof($confirm)){
							$str.= fgets($confirm, 1024);
						}
						$param = array();
						$param[] = "";
						$param[] = ";log _dir";
						$param[] = "log_dir = ".$_SERVER[DOCUMENT_ROOT]."/m/paygate/B/lgdacom/log";
						$param[] = ";상점 ID";
						$param[] = "t".$id." = ".$key;
						$param[] = $id." = ".$key;

						if(strpos($str, "add")){  //
							$param[0] = "add";
							$modify = fopen($file, "w+");
							$modify_str = strstr($str, "add");
							$loop = count($param);
							for($i=0;$i < $loop ; $i++){
								$setStr .= $param[$i]."\r\n";
								if($i == 2){
									$setStr .= "\r\n";
								}
							}
							$contents = str_replace($modify_str,$setStr,$str);
							fwrite($modify, $contents);
							fclose($modify);
						}else{

							$param[0] = ";add";
							$loop = count($param);
							$write =fopen($file, "a");

							for($i=0;$i < $loop ; $i++){

								if($i == 2){
									$line .= "\r\n\r\n";
								}else{
									$line .= "\r\n";
								}
								fwrite($write,"\r\n".$param[$i]);
							}
						}
					}
					fclose($confirm);
				}else{
					echo '<script>alert("설정가능한 파일이 존재하지 않습니다");</script>';
				}
			}else{

				if(is_file($file)){  //파일이 존재하면
					$confirm = fopen($file, "r");
					$str = "";
					while(!feof($confirm)){
							$str.= fgets($confirm, 1024);
					}
					fclose($confirm);
					if(strpos($str, "add")){  //
						$param[0] = "add";
						$del = fopen($file, "w+");
						$del_str = strstr($str, "add");
						$loop = count($param);
						for($i=0;$i < $loop ; $i++){
							$setStr .= $param[$i]."\r\n";
							if($i == 2){
								$setStr .= "\r\n";
							}
						}
						$contents = str_replace($del_str,"add",$str);
						fwrite($del, $contents);
						fclose($del);
					}

				}
			}
			echo '<script>alert("정상적으로 적용되었습니다.");location.href="./mobile_payment.php";</script>';
			exit;
		}else{
			echo '<script>alert("정상적으로 적용되지 않았습니다.");</script>';
		}
	}
?>

<? INCLUDE "header.php"; ?>
<style>
	.optionhide{display:none;}
</style>
<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="<?=$Dir?>js/jquery-1.8.3.min.js"></script>
<script>  var $j = jQuery.noConflict(); </script>
<script language="JavaScript">


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
														<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 모바일샵 &gt; <span class="2depth_select">전자결제연동</span></td>
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
															<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
																<TR>
																	<TD>
																	<IMG SRC="images/mobile_payment_title.gif" border="0"></TD>
																</tr>
																<tr>
																	<TD width="100%" background="images/title_bg.gif" height="21"></TD>
																</TR>
															</TABLE>
														</td>
													</tr>
													<tr>
														<td height="3"></td>
													</tr>
													<tr>
														<td style="padding-bottom:3pt;">
															<TABLE WIDTH="100%" BORDER="0" CELLPADDING="0" CELLSPACING="0">
																<TR>
																	<TD><IMG SRC="images/distribute_01.gif"></TD>
																	<TD COLSPAN="2" background="images/distribute_02.gif"></TD>
																	<TD><IMG SRC="images/distribute_03.gif"></TD>
																</TR>
																<TR>
																	<TD background="images/distribute_04.gif"></TD>
																	<TD class="notice_blue"><IMG SRC="images/distribute_img.gif" ></TD>
																	<TD width="100%" class="notice_blue">모바일쇼핑몰에 PG전자결제를 간편하게 연동하실수 있습니다.</TD>
																	<TD background="images/distribute_07.gif"></TD>
																</TR>
																<TR>
																	<TD><IMG SRC="images/distribute_08.gif"></TD>
																	<TD COLSPAN="2" background="images/distribute_09.gif"></TD>
																	<TD><IMG SRC="images/distribute_10.gif"></TD>
																</TR>
															</TABLE>
														</td>
													</tr>
													<tr>
														<td height="20"></td>
													</tr>
													<tr>
														<td>
															<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
																<TR>
																	<TD><IMG SRC="images/mobile_payment_stitle.gif" border="0"></TD>
																	<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif"></TD>
																	<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></TD>
																</TR>
															</TABLE>
														</td>
													</tr>
													<tr>
														<td height=3></td>
													</tr>
													<tr>
														<td>
															<form name=pgForm action="<?=$_SERVER[PHP_SELF]?>" method=post enctype="multipart/form-data">
																<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0 class="container_line">
																	<tr>
																		<td class="table_cell" width=180>
																			연동된 전자 결제
																		</td>
																		<td class="td_con1">
																			<table cellpadding=0 cellspacing=0>
																				<tr>
																					<td>
																						<span class="inner_text"><?=$company?></span>
																					</td>
																				</tr>
																			</table>
																		</td>
																	</tr>
																	<tr class="<?=$addClass?>">
																		<td class="table_cell inner_line" width=180>
																			모바일샵 전자결제 사용 설정
																		</td>
																		<td class="td_con1 inner_line">
																			<table cellpadding=0 cellspacing=0 >
																				<tr>
																					<td>
																						<input type="radio" class="interlock" name="interlock" value="Y" <?=$onInterlock?>><label>전자결제 사용함</label>
																					</td>
																				</tr>
																				<tr>
																					<td>
																						<input type="radio" class="interlock" name="interlock" value="N" <?=$offInterlock?>><label>전자결제 사용안함</label>
																					</td>
																				</tr>
																			</table>
																		</td>
																	</tr>
																	<!-- <tr class="state_interlock <?=$addClass?>">
																		<td class="table_cell inner_line" width="180">
																			모바일샵 전자결제 사용 모드
																		</td>
																		<td class="td_con1 inner_line">
																			<table cellpadding=0 cellspacing=0>
																				<tr>
																					<td>
																						<input type="radio" name="interlock_mode" value="R" <?=$realMode?>><label>실제연동</label>&nbsp;
																						<input type="radio" name="interlock_mode" value="T" <?=$testMode?>><label>테스트연동</label>
																					</td>
																				</tr>
																			</table>
																		</td>
																	</tr> -->
																</TABLE>
																<input type="hidden" name="mode" value="update">
															</form>
														</td>
													</tr>
													<tr>
														<td height=20></td>
													</tr>
													<tr>
														<td align="center"><a href="#" id="btn_submit"><img src="./images/botteon_save.gif"></a></td>
													</tr>
													<tr>
														<td height=20></td>
													</tr>
													<tr>
														<td>
															<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
																<TR>
																	<TD><IMG SRC="images/manual_top1.gif" WIDTH=15 height="45" ALT=""></TD>
																	<TD><IMG SRC="images/manual_title.gif" WIDTH=113 height="45" ALT=""></TD>
																	<TD width="100%" background="images/manual_bg.gif" height="35"></TD>
																	<TD background="images/manual_bg.gif"></TD>
																	<td background="images/manual_bg.gif"><IMG SRC="images/manual_top2.gif" WIDTH=18 height="45" ALT=""></td>
																</TR>
																<TR>
																	<TD background="images/manual_left1.gif"></TD>
																	<TD COLSPAN=3 width="100%" valign="top" class="menual_bg" style="padding-top:8pt; padding-bottom:8pt; padding-left:4pt;">
																		<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
																			<col width=20></col>
																			<col width=></col>
																			<tr>
																				<td align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
																				<td><span class="font_dotline">모바일 전자결제 연동</span></td>
																			</tr>
																			<tr>
																				<td align="right">&nbsp;</td>
																				<td class="space_top" style="letter-spacing:-0.5pt;">
																				- 모바일샵 전자결제 연동의 경우 쇼핑몰 PG연동 상태를 기본으로 하므로 먼저 쇼핑몰 PG연동을 먼저 설정하여야 합니다.<br/>
																				- 모바일샵 전자결제의 경우 현재 신용카드만 지원합니다<br/>
																				- 연동된 전자 결제의 경우 쇼핑몰에 연동된 PG사 정보를 가져옵니다.<br/>
																				- 모바일샵 전자결제의 경우 Android, iOS만 지원됩니다. <br/>
																				- 최신 모바일 OS및 브라우저 버전에서 결제가 진행되지 않을 수 있습니다<br/>
																				- KCP 모바일샵 전자결제의 경우 PHP환경에서 SOAP 모듈이 연동되어있어야 정상동작합니다 <br/>
																				- LGU+ 모바일샵 전자결제의 경우  결제창2.0 방식에서만 정상 동작 합니다<br/>
																				- 현재 모바일샵의 지원가능한 PG사는 KCP, LGU+만 지원 이외 PG사는 지원되지 않습니다.
																				</td>
																			</tr>
																			<tr>
																				<td colspan="2" height="20"></td>
																			</tr>
																		</table>
																	</TD>
																	<TD background="images/manual_right1.gif"><IMG SRC="images/manual_right1.gif" WIDTH=18 HEIGHT="2" ALT=""></TD>
																</TR>
																<TR>
																	<TD><IMG SRC="images/manual_left2.gif" WIDTH=15 HEIGHT=8 ALT=""></TD>
																	<TD COLSPAN=3 background="images/manual_down.gif"></TD>
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

<script>
	//console.log($j('input[name="interlock"]:checked').val());

	if($j('input[name="interlock"]:checked').val() == "N"){
		$j('.state_interlock').hide();
	}
	$j('input[name="interlock"]').click(function(){
		if(this.value == "Y"){
			$j('.state_interlock').show();
		}else{
			$j('.state_interlock').hide();
		}
	});

	$j('input[name="interlock_mode"]').click(function(){
		if(this.value == "R"){
			alert("거래가 정상적으로 이루어집니다.");
		}else{
			alert("거래가 이루어지나 결제되지 않으며,\n테스트모드로 주문취소 처리가 되지 않습니다.")
		}
	});

	$j('#btn_submit').click(function(){
		//console.log($j('input[name="interlock"]:checked').length);

		if($j('input[name="interlock"]:checked').length <= 0){
			alert("전자결제 사용 설정 상태를 확인 하세요.");
			return false;
		}

		/*if($j('input[name="interlock"]:checked').val() == "Y"){
			if($j('input[name="interlock_mode"]:checked').length <= 0){
				alert("전자결제 사용 모드 상태를 확인 하세요.");
				return false;
			}
		}*/

		if(confirm("모바일 샵 PG 설정 상태가 변경됩니다.")){
			var _form = document.pgForm;
			_form.submit();
		}

	});

</script>
<? INCLUDE "copyright.php"; ?>