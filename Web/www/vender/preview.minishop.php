<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/venderlib.php");

$isaccesspass=true;
INCLUDE ("access.php");

######################## 미리보기 ########################
#1. 왼쪽/상단 디자인 (preview_type => design)
#2. 메인 상단/이벤트 관리 (preview_type => main_topevent)
#3. 분류 상단/이벤트 관리 (preview_type => code_topevent)
##########################################################

$sellvidx=$_VenderInfo->getVidx();

$_MiniLib=new _MiniLib($sellvidx);
$_MiniLib->_MiniInit();

if(!$_MiniLib->isVender) {
	Header("Location:".$Dir.MainDir."main.php");
	exit;
}
$_minidata=$_MiniLib->getMiniData();

$tgbn="10";
$code="000000";

$top_eventimagepath=$Dir.DataDir."shopimages/vender/MAIN_".$_minidata->vender.".gif";
$top_eventimageurl=$Dir.DataDir."shopimages/vender/MAIN_".$_minidata->vender.".gif";

$preview_type=$_POST["preview_type"];
if($preview_type=="info") {						#미니샵 기본정보
	$up_brand_name=$_POST["up_brand_name"];
	$up_description=$_POST["up_description"];
	$image_path=$_POST["image_path"];
	$upfile=$_FILES["upfile"];
	if(strlen($upfile["name"])>0 && $upfile["size"]>0) {
		$_minidata->logo=$image_path;
	}
	$_minidata->brand_name=$up_brand_name;
	$_minidata->brand_description=$up_description;
} else if($preview_type=="design") {				#왼쪽/상단 디자인
	$top_skin_seq=$_POST["top_skin_seq"];
	$top_skin_rgb=$_POST["top_skin_rgb"];
	$top_skin_image=$_POST["top_skin_image"];
	$top_font_color=$_POST["top_font_color"];
	$left_color_seq=$_POST["left_color_seq"];
	$left_color_rgb=$_POST["left_color_rgb"];
	$left_font_color=$_POST["left_font_color"];
	$skin_upload_img=$_FILES["skin_upload_img"];
	$image_path=$_POST["image_path"];
	$skin_upload_flag=$_POST["skin_upload_flag"];

	$_minidata->top_fontcolor=$top_font_color;
	$_minidata->color=$left_color_rgb;
	$_minidata->fontcolor=$left_font_color;


	if($skin_upload_flag=="N") {
		$_minidata->top_backimg=$Dir."images/minishop/title_skin/".$top_skin_rgb."_".$top_skin_image;
	} else if($skin_upload_flag=="Y") {
		$_minidata->top_backimg=$image_path;
	}
} else if($preview_type=="main_topevent") {	#메인 상단/이벤트 관리
	$toptype=$_POST["toptype"];
	$topdesign=$_POST["topdesign"];
	$upfileimage=$_FILES["upfileimage"];
	$image_path=$_POST["image_path"];

	$_minidata->main_toptype=$toptype;
	$_minidata->main_topdesign=$topdesign;
	if($_minidata->main_toptype=="image") {
		$top_eventimagepath=$upfileimage[tmp_name];
		$top_eventimageurl=$image_path;
	}
} else if($preview_type=="code_topevent") {	#분류 상단/이벤트 관리
	$toptype=$_POST["toptype"];
	$topdesign=$_POST["topdesign"];
	$upfileimage=$_FILES["upfileimage"];
	$image_path=$_POST["image_path"];

	$select_code=$_POST["select_code"];
	$select_tgbn=$_POST["select_tgbn"];

	$tgbn=$select_tgbn;
	$code=$select_code;
	$codeA=substr($code,0,3);
	$codeB=substr($code,3,3);
	if(strlen($codeB)!=3) $codeB="000";
	$code=$codeA.$codeB;

	$_minidata->main_toptype=$toptype;
	$_minidata->main_topdesign=$topdesign;
	if($_minidata->main_toptype=="image") {
		$top_eventimagepath=$upfileimage[tmp_name];
		$top_eventimageurl=$image_path;
	}

	$_minidata->new_used="0";
	$_minidata->new_dispseq="";

	$sql = "SELECT hot_used,hot_dispseq,hot_linktype FROM tblvendercodedesign ";
	$sql.= "WHERE vender='".$_minidata->vender."' ";
	$sql.= "AND code='".substr($code,0,3)."' AND tgbn='".$tgbn."' ";
	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		$_minidata->hot_used=$row->hot_used;
		$_minidata->hot_dispseq=$row->hot_dispseq;
		$_minidata->hot_linktype=$row->hot_linktype;
	} else {
		$_minidata->hot_used="0";
	}
	mysql_free_result($result);
}

$_MiniLib->getCode($tgbn,$code);
$_MiniLib->getThemecode($tgbn,$code);

?>

<HTML>
<HEAD>
<TITLE>미니샵 미리보기</TITLE>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META http-equiv="X-UA-Compatible" content="IE=5" />
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<script type="text/javascript" src="<?=$Dir?>lib/DropDown.js.php"></script>
<?include($Dir."lib/style.php")?>
<SCRIPT LANGUAGE="JavaScript">
<!--
function GoItem(a) {}
function GoSection(a,b,c) {}
function GoNoticeList(a) {}
function GoNoticeView(a,b) {}
//-->
</SCRIPT>
</HEAD>

<body topmargin=0 leftmargin=0 rightmargin=0 marginheight=0 marginwidth=0>

<?
$prdataA=$_MiniLib->prdataA;
$prdataB=$_MiniLib->prdataB;
$themeprdataA=$_MiniLib->themeprdataA;
$themeprdataB=$_MiniLib->themeprdataB;
?>
<table border=0 width="<?=$_minidata->shop_width?>" cellpadding=0 cellspacing=0 style="table-layout:fixed" >
	<tr>
		<td>
			<table border=0 cellpadding=0 cellspacing=0 width=100%>
			<col width=187></col>
			<col width=></col>

			<!-- 상단 타이틀부분 들어가는 곳 -->
				<tr>
					<td background="<?=$_minidata->top_backimg?>" style="background-repeat:no-repeat;background-position:left top">
						<table border=0 cellpadding=0 cellspacing=0 width=100%>
							<tr>
								<td style="padding:5,0,0,5">
									<table border=0 cellpadding=0 cellspacing=0 width=187 style="table-layout:fixed">
										<tr>
											<td width=187 bgcolor=#ffffff  style="padding-top:5px">
												<table width=100% border=0 cellspacing=0 cellpadding=0 >
													<tr>
														<td align=center valign=middle><a href='<?=$Dir.FrontDir?>minishop.php?sellvidx=<?=$_minidata->vender?>'><img src="<?=$_minidata->logo?>" width=165 height=80 border=0></a></td>
													</tr>
														<form name=custregminiform method=post>
														<input type=hidden name=sellvidx value="<?=$_minidata->vender?>">
														<input type=hidden name=memberlogin value="<?=(strlen($_ShopInfo->getMemid())>0?"Y":"N")?>">
														</form>
													<tr>
														<td align=center valign=top style="padding-top:3px"><img src="<?=$Dir?>images/minishop/dangol.gif" border=0></td>
													</tr>
												</table>
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</td>
					<td valign=top background="<?=$_minidata->top_backimg?>" style="background-repeat:no-repeat;background-position:right top">
						<table border=0 cellpadding=0 cellspacing=0 width=100%>
							<tr>
								<td valign=top>
									<table border=0 cellpadding=0 cellspacing=0 width=88%>
										<tr>
											<td style="color:#<?=$_minidata->top_fontcolor?>;padding:20,0,0,30"><FONT style="font-size:13"><B><?=$_minidata->brand_name?></B></font>( <?=$_minidata->prdt_cnt?>개 상품 / <?=$_minidata->id?></FONT>)</td>
										</tr>
										<tr>
											<td height=5></td>
										</tr>
										<tr>
											<td style="color:#<?=$_minidata->top_fontcolor?>;padding:0,0,0,30"><FONT style="font-size:11px;word-spacing:-2px;"><?=$_minidata->brand_description?></font></td>
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
	<tr>
		<td height=10></td>
	</tr>
	<tr>
		<td width=100% valign=top>
			<table border=0 cellpadding=0 cellspacing=0 width=100% height=100%>
				<tr>
					<td width=187 valign=top height=100%>
						<table cellpadding="0" cellspacing="0" width="100%"  height="100%" style="table-layout" border=0>
							<tr>
								<td height=10 valign=top background="<?=$Dir?>images/minishop/bg/<?=$_minidata->color?>_bg_title.gif" style="padding:0,7;"></td>
							</tr>
							<tr>
								<td valign=top background="<?=$Dir?>images/minishop/bg/<?=$_minidata->color?>_bg_bg.gif" style="padding-top:5"  height="100%">
									<table cellpadding="0" cellspacing="0" width="100%" style="table-layout" border=0>
										<tr>
											<td style="padding-left:10"><img src="<?=$Dir?>images/minishop/search_title.gif" border=0></td>
										</tr>
										<tr>
											<td align=center>
												<table cellpadding="0" cellspacing="1" width="90%" bgcolor="#E5E5E5">
													<tr>
														<td bgcolor="#F5F5F5" style="padding:6px;">
															<table width=100% border=0 cellspacing=2 cellpadding=0 style="table-layout:fixed">
																<form name="MinishopSearchForm">
																<input type="hidden" name="sellvidx" value="">
																<tr>
																	<td><select name="search_gbn" style="width:100%;#d4d4d4 1px solid; BORDER-LEFT: #d4d4d4 1px solid; PADDING-BOTTOM: 1pt; BACKGROUND-COLOR: #ffffff; HEIGHT: 16px; FONT-SIZE: 11px; BORDER-TOP: #d4d4d4 1px solid; BORDER-RIGHT: #d4d4d4 1px solid; PADDING-TOP: 2pt;font-family:돋움;"><option value="store">이 미니샵 상품</option><option value="all">쇼핑몰 전체 상품</option></select>
																	</td>
																</tr>
																<tr>
																	<td><input type=text name="search" size=16 value="" onkeydown="if (event.keyCode == 13) return SearchMinishop();" style="BORDER-BOTTOM: #d4d4d4 1px solid; BORDER-LEFT: #d4d4d4 1px solid; PADDING-BOTTOM: 1pt; BACKGROUND-COLOR: #ffffff; WIDTH: 70px; HEIGHT: 16px; FONT-SIZE: 11px; BORDER-TOP: #d4d4d4 1px solid; BORDER-RIGHT: #d4d4d4 1px solid; PADDING-TOP: 2pt"> <img src="<?=$Dir?>images/minishop/btn_search.gif" border=0 align=absmiddle style="cursor:hand" onClick="SearchMinishop()"></td>
																</tr>
																</form>
															</table>
														</td>
													</tr>
												</table>
											</td>
										</tr>
										<tr>
											<td height=30></td>
										</tr>
										<?
												if(count($prdataA)>0) {
										?>
										<tr>
											<td align=center>
												<table width=100% cellspacing=0 cellpadding=0 border=0>
													<tr><!--<td>&nbsp;&nbsp;<FONT COLOR="<?=$_minidata->fontcolor?>"><B><?=$_minidata->brand_name?> 카테고리</B></FONT></td>-->
														<td style="padding-left:10"><img src="<?=$Dir?>images/minishop/category_title.gif" border=0></td>
													</tr>
													<tr>
														<td align=center>
															<table width=187 cellspacing=0 cellpadding=0 border=0>
																<tr>
																	<td style='padding-left:16'>
																		<table border=0 cellpadding=0 cellspacing=0 width=100%>
																			<?
																			for($i=0;$i<count($prdataA);$i++) {
																				$tmpcode=$prdataA[$i]->codeA."000";
																				if($i>0) echo "<tr><td height=10></td></tr>\n";
																				echo "<tr>\n";
																				echo "	<td><img src=\"".$Dir."images/minishop/icon_catedot.gif\" border=0> ";
																				if($tgbn!="10" || $code!=$tmpcode) {
																					echo "<A HREF=\"#\"><B>".$prdataA[$i]->code_name."</B></A>";
																				} else {
																					echo "<FONT style=\"text-decoration: underline;\"><B>".$prdataA[$i]->code_name."</B></font>";
																				}
																				echo "	</td>\n";
																				echo "</tr>\n";
																				unset($strprdata);
																				for($j=0;$j<count($prdataB[$prdataA[$i]->codeA]);$j++) {
																					$tmpcode=$prdataB[$prdataA[$i]->codeA][$j]->codeA.$prdataB[$prdataA[$i]->codeA][$j]->codeB;
																					if($j>0) $strprdata.=" | ";
																					if($tgbn!="10" || $code!=$tmpcode) {
																						$strprdata.="<A HREF=\"#\">".$prdataB[$prdataA[$i]->codeA][$j]->code_name."</A>";
																					} else {
																						$strprdata.="<FONT style=\"text-decoration: underline;\">".$prdataB[$prdataA[$i]->codeA][$j]->code_name."</FONT>";
																					}
																				}
																				if(strlen($strprdata)>0) {
																					echo "<tr>\n";
																					echo "	<td style=\"padding:5,0,0,15\">".$strprdata."</td>\n";
																					echo "</tr>\n";
																				}
																			}
																			?>
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
											<td height=20></td>
										</tr>
										<?
										}

										if(count($themeprdataA)>0) {
										?>
										<tr>
											<td align=center>
												<table width=100% cellspacing=0 cellpadding=0 border=0>
													<tr>
														<!--<td>&nbsp;&nbsp;<FONT COLOR="<?=$_minidata->fontcolor?>"><B>테마 카테고리</B></FONT></td>-->
														<td style="padding-left:10"><img src="<?=$Dir?>images/minishop/tmcategory_title.gif" border=0></td>
													</tr>
													<tr>
														<td align=center>
															<table width=187 cellspacing=0 cellpadding=0 border=0 >
																<tr>
																	<td style='padding-left:16'>
																		<table border=0 cellpadding=0 cellspacing=0 width=100%>
																			<?
																			for($i=0;$i<count($themeprdataA);$i++) {
																				$tmpcode=$themeprdataA[$i]->codeA."000";
																				if($i>0) echo "<tr><td height=10></td></tr>\n";
																				echo "<tr>\n";
																				echo "	<td><img src=\"".$Dir."images/minishop/icon_catedot.gif\" border=0> ";
																				if($tgbn!="20" || $code!=$tmpcode) {
																					echo "<A HREF=\"#\"><B>".$themeprdataA[$i]->code_name."</B></A>";
																				} else {
																					echo "<FONT style=\"text-decoration: underline;\"><B>".$themeprdataA[$i]->code_name."</B></font>";
																				}
																				echo "	</td>\n";
																				echo "</tr>\n";
																				unset($strprdata);
																				for($j=0;$j<count($themeprdataB[$themeprdataA[$i]->codeA]);$j++) {
																					$tmpcode=$themeprdataB[$themeprdataA[$i]->codeA][$j]->codeA.$themeprdataB[$themeprdataA[$i]->codeA][$j]->codeB;
																					if($j>0) $strprdata.=" | ";
																					if($tgbn!="20" || $code!=$tmpcode) {
																						$strprdata.="<A HREF=\"#\">".$themeprdataB[$themeprdataA[$i]->codeA][$j]->code_name."</A>";
																					} else {
																						$strprdata.="<FONT style=\"text-decoration: underline;\">".$themeprdataB[$themeprdataA[$i]->codeA][$j]->code_name."</FONT>";
																					}
																				}
																				if(strlen($strprdata)>0) {
																					echo "<tr>\n";
																					echo "	<td style=\"padding:5,0,0,15\">".$strprdata."</td>\n";
																					echo "</tr>\n";
																				}
																			}
																			?>
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
											<td height=20></td>
										</tr>
										<?}?>
										<tr>
											<td align=center>
												<table width=100% cellspacing=0 cellpadding=0 border=0>
													<tr>
														<td align=center>
															<table width=187 cellspacing=0 cellpadding=0 border=0 >
																<tr>
																	<td style='padding-top:5'>
																		<table border=0 cellpadding=0 cellspacing=0 width=100% >
																			<tr height=30>
																				<td style="padding-left:10"><img src="<?=$Dir?>images/minishop/menu_notice.gif" border=0></td>
																				<td align=right style="padding-right:10"><A HREF="#"><img src="<?=$Dir?>images/minishop/btn_more.gif" border=0></A></td>
																			</tr>
																		</table>
																	</td>
																</tr>
																<tr>
																	<td style='padding-left:16'>
																		<table border=0 cellpadding=0 cellspacing=0 width=100%>
																			<?
																			$sql = "SELECT date,subject FROM tblvendernotice WHERE vender='".$_minidata->vender."' ";
																			$sql.= "ORDER BY date DESC LIMIT 5 ";
																			$result=mysql_query($sql,get_db_conn());
																			while($row=mysql_fetch_object($result)) {
																				echo "<tr><td><span style=word-break:break-all;height:16;overflow:hidden;><A HREF=\"#\"><B>·</B> ".titleCut(23,strip_tags($row->subject))."</A></span></td></tr>\n";
																				echo "<tr><td height=3></td></tr>\n";
																			}
																			mysql_free_result($result);
																			?>
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
											<td height=25></td>
										</tr>
										<tr>
											<td align=center>
												<table width=100% cellspacing=0 cellpadding=0 border=0>
													<tr>
														<td align=center>
															<table width=187 cellspacing=0 cellpadding=0 border=0>
																<tr>
																	<td>
																		<table border=0 cellpadding=0 cellspacing=0 width=100% >
																			<tr>
																				<td style="padding-left:10"><img src="<?=$Dir?>images/minishop/menu_cust.gif" border=0></td>
																			</tr>
																		</table>
																	</td>
																</tr>
																<tr>
																	<td style='padding-left:16'>
																		<table width=100% border=0 cellspacing=0 cellpadding=0>
																			<tr>
																				<td height=18><img src="<?=$Dir?>images/minishop/menu_cust_text01.gif" border=0 alt="문의전화"></td>
																			</tr>
																			<tr>
																				<td height=3></td>
																			</tr>
																			<tr>
																				<td height=17><img src="<?=$Dir?>images/minishop/icon_phone01.gif" border=0 align=absmiddle> <?=$_minidata->custdata["TEL"]?></td>
																			</tr>
																			<tr>
																				<td height=17><img src="<?=$Dir?>images/minishop/icon_fax01.gif" border=0 align=absmiddle> <?=$_minidata->custdata["FAX"]?></td>
																			</tr>
																			<tr>
																				<td height=17 style="word-break:break-all"><img src="<?=$Dir?>images/minishop/icon_email01.gif" border=0 align=absmiddle> <?=$_minidata->custdata["EMAIL"]?></td>
																			</tr>
																		</table>
																		<table width=100% border=0 cellspacing=0 cellpadding=0>
																			<tr>
																				<td height=10></td>
																			</tr>
																			<tr>
																				<td><img src="<?=$Dir?>images/minishop/menu_cust_text02.gif" border=0 alt="고객상담시간"></td>
																			</tr>
																			<tr><td height=3></td></tr>
																			<tr>
																				<td>평일 : <?=$_minidata->custdata["TIME1"]?></td>
																			</tr>
																			<tr>
																				<td>토요일 : <?=$_minidata->custdata["TIME2"]?></td>
																			</tr>
																			<tr>
																				<td>일ㆍ공휴일 : <?=$_minidata->custdata["TIME3"]?></td>
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
								</td>

								<td width="<?=($_minidata->shop_width-180)?>" align=center valign=top nowrap>
									<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
										<tr>
											<td align=center style="padding:5">

												<!-- 메인화면 상단 자유디자인 -->
												<?
															if($_minidata->main_toptype=="image") {
																if(file_exists($top_eventimagepath)) {
																	echo "<table width=100% border=0 cellpadding=0 cellspacing=0>\n";
																	echo "<tr>\n";
																	echo "	<td align=center><img src=\"".$top_eventimageurl."\" border=0 align=absmiddle></td>\n";
																	echo "</tr>\n";
																	echo "<tr><td height=5></td></tr>\n";
																	echo "</table>\n";
																}
															} else if($_minidata->main_toptype=="html") {
																if(strlen($_minidata->main_topdesign)>0) {
																	echo "<table width=100% border=0 cellpadding=0 cellspacing=0>\n";
																	echo "<tr>\n";
																	echo "	<td align=center>";
																	if (strpos(strtolower($_minidata->main_topdesign),"<table")!=false)
																		echo $_minidata->main_topdesign;
																	else
																		echo ereg_replace("\n","<br>",$_minidata->main_topdesign);
																	echo "	</td>\n";
																	echo "</tr>\n";
																	echo "<tr><td height=5></td></tr>\n";
																	echo "</table>\n";
																}
															}
												?>

															<!-- HOT 추천상품 -->
												<?
															if($_minidata->hot_used=="1") {
																unset($hot_disptype);
																unset($hot_dispcnt);
																unset($hot_prcode);
																unset($specialprlist);
																$sql = "SELECT disptype, dispcnt FROM tblvendersectdisplist WHERE seq='".$_minidata->hot_dispseq."' ";
																$result=mysql_query($sql,get_db_conn());
																if($row=mysql_fetch_object($result)) {
																	$hot_disptype=$row->disptype;
																	$hot_dispcnt=$row->dispcnt;
																}
																mysql_free_result($result);
																if(strlen($hot_disptype)>0 && $hot_dispcnt>0) {
																	$sql = "SELECT productcode,productname,sellprice,quantity,consumerprice,reserve,reservetype,production, ";
																	$sql.= "option_price, tag, minimage, tinyimage, etctype, option_price FROM tblproduct WHERE 1=1 ";
																	if($_minidata->hot_linktype=="2") {
																		$sql2 = "SELECT special_list FROM ";
																		if($preview_type=="code_topevent") {
																			$sql2.= "tblvenderspecialcode ";
																		} else {
																			$sql2.= "tblvenderspecialmain ";
																		}
																		$sql2.= "WHERE vender='".$_minidata->vender."' ";
																		if($preview_type=="code_topevent") {
																			$sql2.= "AND code='".substr($code,0,3)."' AND tgbn='".$tgbn."' ";
																		}
																		$sql2.= "AND special='3' ";
																		$result2=mysql_query($sql2,get_db_conn());
																		if($row2=mysql_fetch_object($result2)) {
																			$hot_prcode=ereg_replace(',','\',\'',$row2->special_list);
																		}
																		mysql_free_result($result2);
																		if(strlen($hot_prcode)>0) {
																			$sql.= "AND productcode IN ('".$hot_prcode."') ";
																		} else {
																			$isnot_hotspecial=true;
																		}
																	} else if($preview_type=="code_topevent") {
																		$sql.= "AND productcode LIKE '".substr($code,0,3)."%' ";
																	}
																	$sql.= "AND vender='".$_minidata->vender."' AND display='Y' ";
																	if($_minidata->hot_linktype=="1" || $isnot_hotspecial==true) {
																		$sql.= "ORDER BY sellcount DESC ";
																	} else if($_minidata->hot_linktype=="2") {
																		$sql.= "ORDER BY FIELD(productcode,'".$hot_prcode."') ";
																	}
																	$sql.= "LIMIT ".$hot_dispcnt." ";
																	$result=mysql_query($sql,get_db_conn());
																	$yy=1;
																	while($row=mysql_fetch_object($result)) {
																		$specialprlist[$yy]=$row;
																		$yy++;
																	}
																	mysql_free_result($result);
																}
																if(count($specialprlist)>0) {
																	echo "<table width=100% border=0 cellspacing=0 cellpadding=0>\n";
																	echo "<tr>\n";
																	echo "	<td bgcolor=\"#ffffff\" style=\"padding-left:10\" height=\"25\"><img src=\"".$Dir."images/minishop/title_hot.gif\" border=0></td>\n";
																	echo "</tr>\n";
																	echo "<tr>\n";
																	echo "	<td height=10></td>\n";
																	echo "</tr>\n";
																	echo "<tr>\n";
																	echo "	<td valign=top>\n";
																	include ($Dir.TempletDir."minisect/".$hot_disptype.".php");
																	echo "	</td>\n";
																	echo "</tr>\n";
																	echo "<tr>\n";
																	echo "	<td height=15></td>\n";
																	echo "</tr>\n";
																	echo "</table>\n";
																}
															}
												?>

															<!-- NEW 신상품 -->
												<?
															if($_minidata->new_used=="1") {
																unset($new_disptype);
																unset($new_dispcnt);
																unset($specialprlist);
																$sql = "SELECT disptype, dispcnt FROM tblvendersectdisplist WHERE seq='".$_minidata->new_dispseq."' ";
																$result=mysql_query($sql,get_db_conn());
																if($row=mysql_fetch_object($result)) {
																	$new_disptype=$row->disptype;
																	$new_dispcnt=$row->dispcnt;
																}
																mysql_free_result($result);
																if(strlen($new_disptype)>0 && $new_dispcnt>0) {
																	$sql = "SELECT productcode,productname,sellprice,quantity,consumerprice,reserve,reservetype,production, ";
																	$sql.= "option_price, tag, minimage, tinyimage, etctype, option_price FROM tblproduct ";
																	$sql.= "WHERE 1=1 ";
																	if($preview_type=="code_topevent") {
																		$sql.= "AND productcode LIKE '".substr($code,0,3)."%' ";
																	}
																	$sql.= "AND vender='".$_minidata->vender."' AND display='Y' ";
																	$sql.= "ORDER BY regdate DESC ";
																	$sql.= "LIMIT ".$new_dispcnt." ";
																	$result=mysql_query($sql,get_db_conn());
																	$yy=1;
																	while($row=mysql_fetch_object($result)) {
																		$specialprlist[$yy]=$row;
																		$yy++;
																	}
																	mysql_free_result($result);
																}
																if(count($specialprlist)>0) {
																	echo "<table width=100% border=0 cellspacing=0 cellpadding=0>\n";
																	echo "<tr>\n";
																	echo "	<td bgcolor=\"#ffffff\" style=\"padding-left:10\" height=\"25\"><img src=\"".$Dir."images/minishop/title_new.gif\" border=0></td>\n";
																	echo "</tr>\n";
																	echo "<tr>\n";
																	echo "	<td height=10></td>\n";
																	echo "</tr>\n";
																	echo "<tr>\n";
																	echo "	<td valign=top>\n";
																	include ($Dir.TempletDir."minisect/".$new_disptype.".php");
																	echo "	</td>\n";
																	echo "</tr>\n";
																	echo "<tr>\n";
																	echo "	<td height=15></td>\n";
																	echo "</tr>\n";
																	echo "</table>\n";
																}
															}
												?>
											</td>
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
</BODY>
</HTML>