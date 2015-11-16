<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/venderlib.php");
INCLUDE ("access.php");

$maxRow=12;


$mode=$_POST["mode"];
if($mode=="update") {
	$hot_used_flag=(int)$_POST["hot_used_flag"];		//hot사용여부
	$hot_prdlinktype=(int)$_POST["hot_prdlinktype"];	//hot 링크방법
	$hot_templt_dispseq=(int)$_POST["hot_templt_dispseq"];
	$new_used_flag=(int)$_POST["new_used_flag"];
	$new_templt_dispseq=(int)$_POST["new_templt_dispseq"];
	$page_disptype=$_POST["page_disptype"];
	$page_disp_num=(int)$_POST["page_disp_num"];

	$sql = "SELECT * FROM tblvendersectdisplist WHERE seq IN ('".$hot_templt_dispseq."','".$new_templt_dispseq."') ";
	$result=mysql_query($sql,get_db_conn());
	$check_dispseq=array();
	while($row=mysql_fetch_object($result)) {
		$check_dispseq[$row->seq]=$row;
	}
	mysql_free_result($result);

	if(!preg_match("/^(0|1)$/",$hot_used_flag)) {
		$hot_used_flag=0;
	}
	if(!preg_match("/^(1|2)$/",$hot_prdlinktype)) {
		$hot_prdlinktype=1;
	}
	if(!is_object($check_dispseq[$hot_templt_dispseq])) {
		$hot_templt_dispseq=118;
	}
	if(!preg_match("/^(0|1)$/",$new_used_flag)) {
		$new_used_flag=0;
	}
	if(!is_object($check_dispseq[$new_templt_dispseq])) {
		$new_templt_dispseq=118;
	}
	if(!preg_match("/^(I|D|L)$/",$page_disptype)) {
		$page_disptype="I";
	}
	if($page_disp_num<=0) $page_disp_num=12;

	$sql = "UPDATE tblvenderstore SET ";
	$sql.= "hot_used		= '".$hot_used_flag."', ";
	$sql.= "hot_dispseq		= '".$hot_templt_dispseq."', ";
	$sql.= "hot_linktype	= '".$hot_prdlinktype."', ";
	$sql.= "new_used		= '".$new_used_flag."', ";
	$sql.= "new_dispseq		= '".$new_templt_dispseq."', ";
	$sql.= "prlist_display	= '".$page_disptype."', ";
	$sql.= "prlist_num		= '".$page_disp_num."' ";
	$sql.= "WHERE vender='".$_VenderInfo->getVidx()."' ";
	if(mysql_query($sql,get_db_conn())) {
		if($hot_prdlinktype=="1") {	//HOT 판매량순
			$sql = "DELETE FROM tblvenderspecialmain ";
			$sql.= "WHERE vender='".$_VenderInfo->getVidx()."' ";
			$sql.= "AND special='3' ";
			mysql_query($sql,get_db_conn());
		} else {					//HOT 상품별도지정
			$special_list="";
			$in_prlist="";
			for($i=0;$i<$maxRow;$i++) {
				${"hot_prdcode".$i}=$_POST["hot_prdcode".$i];
				if(strlen(${"hot_prdcode".$i})==18) {
					$special_list.=${"hot_prdcode".$i}.",";
				}
			}
			if(strlen($special_list)>0) {
				$special_list=ereg_replace(',','\',\'',$special_list);
				$sql = "SELECT productcode FROM tblproduct WHERE productcode IN ('".$special_list."') ";
				$sql.= "AND vender='".$_VenderInfo->getVidx()."' ";
				$result=mysql_query($sql,get_db_conn());
				while($row=mysql_fetch_object($result)) {
					$in_prlist.=$row->productcode.",";
				}
				mysql_free_result($result);
				$in_prlist=substr($in_prlist,0,-1);
			}
			$sql = "INSERT INTO tblvenderspecialmain VALUES ('".$_VenderInfo->getVidx()."','3','".$in_prlist."') ";
			mysql_query($sql,get_db_conn());
			if (mysql_errno()==1062) {
				$sql = "UPDATE tblvenderspecialmain SET special_list='".$in_prlist."' ";
				$sql.= "WHERE vender='".$_VenderInfo->getVidx()."' ";
				$sql.= "AND special='3' ";
				mysql_query($sql,get_db_conn());
			}
		}
	} else {
		echo "<html></head><body onload=\"alert('요청하신 작업중 오류가 발생하였습니다.')\"></body></html>";exit;
	}
	echo "<html></head><body onload=\"alert('요청하신 작업이 성공하였습니다.');parent.location.reload()\"></body></html>";exit;
}


$hot_used=$_venderdata->hot_used;
$hot_dispseq=$_venderdata->hot_dispseq;
$hot_linktype=$_venderdata->hot_linktype;

$new_used=$_venderdata->new_used;
$new_dispseq=$_venderdata->new_dispseq;

$prlist_display=$_venderdata->prlist_display;
$prlist_num=$_venderdata->prlist_num;


if($hot_dispseq<=0) $hot_dispseq=118;
if($new_dispseq<=0) $new_dispseq=118;

$sql = "SELECT * FROM tblvendersectdisplist ";
$result=mysql_query($sql,get_db_conn());
$SectArr=array();
while($row=mysql_fetch_object($result)) {
	$SectArr[$row->seq]=$row;
	if($row->dispcnt>$maxRow) $maxRow=$row->dispcnt;
}
mysql_free_result($result);
?>

<? INCLUDE "header.php"; ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language=javascript src="PrdtDispInfoFucn.js.php" type="text/javascript"></script>

<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
<col width=190></col>
<col width=20></col>
<col width=></col>
<col width=20></col>
<tr>
	<td width=190 valign=top nowrap background="images/minishop_leftbg.gif"><? include ("menu.php"); ?></td>
	<td width=20 nowrap></td>
	<td valign=top style="padding-top:20px">

	<table width="100%"  border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td>
		<table width="100%"  border="0" cellpadding="0" cellspacing="0" >
		<tr>
			<td>
				<table border=0 cellpadding=0 cellspacing=0 width=100% >
				<tr>
					<td><img src="images/main_design_title.gif"></td>
				</tr>
				<tr>
					<td height=5 background="images/minishop_titlebg.gif">
				</tr>
				</table>
			</td>
		</tr>
		<tr><td height=10></td></tr>
		<tr>
			<td>
				<table border=0 cellpadding=0 cellspacing=0 width=100% >
				<tr>
					<td colspan=3 >


						<table cellpadding="10" cellspacing="1" width="100%" bgcolor="#EFEFF2">
							<tr>
								<td  bgcolor="#F5F5F9" style="padding:20px">
									<table border=0 cellpadding=0 cellspacing=0 width=100%>
										<tr>
											<td class="notice_gray" height="20"><img src="images/icon_dot02.gif" border=0 hspace="4">미니샵 메인 화면에 진열되는 상품을 판매자 의도대로 자유롭게 배치하실 수 있습니다.</td>
										</tr>
										<tr>
											<td class="notice_gray" height="20"><img src="images/icon_dot02.gif" border=0 hspace="4">[HOT 추천상품]의 경우 별도로 상품 진열 지정을 하지 않을 경우 판매량 순으로 진열됩니다.</td>
										</tr>
										<tr>
											<td class="notice_gray" height="20"><img src="images/icon_dot02.gif" border=0 hspace="4">[NEW 신상품]은 최근 등록한 상품 순서로 진열됩니다.</td>
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

			<!-- 처리할 본문 위치 시작 -->
			<tr><td height=40></td></tr>
			<tr>
				<td>
				



<script language=Javascript>
maxRow = <?=$maxRow?>;

<?
$tmpsectval=$SectArr;
while(list($key,$val)=each($tmpsectval)) {
	echo "hot_templt_img[".$key."]=\"images/sample/display/section/".$val->disptype.".gif\";\n";
	echo "hot_prd_count[".$key."]=\"".$val->dispcnt."\";\n";

	echo "new_templt_img[".$key."]=\"images/sample/display/section/".$val->disptype.".gif\";\n";
	echo "new_prd_count[".$key."]=\"".$val->dispcnt."\";\n";
}
?>

function  changePrdTemplt() {
	iForm.prd_templt_img.src = 'images/sample/display/item_' + iForm.page_disptype.value + '.gif';
}

</script>

				<table border=0 cellpadding=0 cellspacing=0 width=100%>

				<form name=iForm action="" method=post>
				<input type=hidden name=mode>
				<input type=hidden name=hot_prddispcnt value="<?=$SectArr[$hot_dispseq]->dispcnt?>">
				<input type=hidden name=hot_used_flag>
				<input type=hidden name=hot_prdlinktype>
				<input type=hidden name=new_used_flag>

				<tr>
					<td><img src="images/main_design_stitle01.gif" border=0 align=absmiddle alt="HOT 추천상품 관리"></td>
				</tr>
				<tr><td height=5></td></tr>
				<tr><td height=1 bgcolor=#cccccc></td></tr>
				<tr>
					<td align=center bgcolor=F8F8F8 style=padding:25>
					<img name=hot_templt_img id=hot_templt_img src=images/sample/display/section/<?=$SectArr[$hot_dispseq]->disptype?>.gif border=0>
					</td>
				</tr>
				<tr><td height=5></td></tr>
				<tr><td height=1 bgcolor=#CDCDCD></td></tr>
				<tr>
					<td valign=top>
					<table width=100% border=0 cellspacing=0 cellpadding=0>
					<tr>
						<td width=140 bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B>사용여부</B></td>
						<td  style=padding:7,10><input type=radio name="hot_used_flag_radio" <?if($hot_used=="1")echo"checked";?>>예 &nbsp; &nbsp; <input type=radio name="hot_used_flag_radio" <?if($hot_used=="0")echo"checked";?>>아니오</td>
					</tr>
					<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
					<tr>
						<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B>상품진열 선택</B></td>
						<td valign=top style=padding:7,10>
						<select name="hot_templt_dispseq" style=width:200 onChange="changeHotTempltIdx();">
<?
						$tmpsectval=$SectArr;
						while(list($key,$val)=each($tmpsectval)) {
							echo "<option value=\"".$key."\"";
							if($key==$hot_dispseq) echo " selected";
							echo ">".$val->dispname."</option>\n";
						}
?>
						</select>
						</td>
					</tr>
					<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
					<tr valign=top>
						<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B>링크설정</B></td>
						<td style=padding:7,10>
						<table width=100% border=0 cellspacing=0 cellpadding=0>
						<tr>
							<td><input type=radio name="hot_sect_prdlink" value="10" onClick="resetHotPrdList();" <?if($hot_linktype=="1")echo"checked";?>>판매량순 &nbsp; &nbsp; <input type=radio name="hot_sect_prdlink" value="20" onClick="resetHotPrdList();" <?if($hot_linktype=="2")echo"checked";?>>상품별도지정 : 상품선택 &nbsp; <a href="javascript:openPrdList();"><img src=images/btn_select02.gif border=0 align=absmiddle></a> <a href="javascript:deletePrdList();"><img src=images/btn_delete02.gif border=0 align=absmiddle></a></td>
						</tr>
						<tr>
							<td height=6></td>
						</tr>
						<tr>
							<td style=color:2A97A7>&nbsp;* 판매량순으로 선택하실 경우 HOT 추천상품에 진열되는 상품은 판매량 순으로 진열됩니다.</td>
						</tr>
						<tr>
							<td height=6></td>
						</tr>
						<tr>
							<td valign=top bgcolor=E7E7E7>
<?
							$sql = "SELECT special_list FROM tblvenderspecialmain ";
							$sql.= "WHERE vender='".$_VenderInfo->getVidx()."' ";
							$sql.= "AND special='3' ";
							$result=mysql_query($sql,get_db_conn());
							$sp_prcode="";
							if($row=mysql_fetch_object($result)) {
								$sp_prcode=ereg_replace(',','\',\'',$row->special_list);
							}
							mysql_free_result($result);

							$hotprobj20=array();
							$hotprobj10=array();
							if(strlen($sp_prcode)>0) {
								$sql = "SELECT productcode,productname,sellprice,display FROM tblproduct ";
								$sql.= "WHERE vender='".$_VenderInfo->getVidx()."' ";
								$sql.= "AND productcode IN ('".$sp_prcode."') ";
								$sql.= "ORDER BY FIELD(productcode,'".$sp_prcode."') LIMIT ".$maxRow." ";
								$result=mysql_query($sql,get_db_conn());
								while($row=mysql_fetch_object($result)) {
									$hotprobj20[]=$row;
								}
								mysql_free_result($result);
							}

							$sql = "SELECT productcode,productname,sellprice FROM tblproduct ";
							$sql.= "WHERE vender='".$_VenderInfo->getVidx()."' AND display='Y' ";
							$sql.= "ORDER BY sellcount DESC LIMIT ".$maxRow." ";
							$result=mysql_query($sql,get_db_conn());
							while($row=mysql_fetch_object($result)) {
								$hotprobj10[]=$row;
							}
							mysql_free_result($result);

							$hotprlist10="";
							$hotprlist20="";
							for($i=0;$i<$maxRow;$i++) {
								$display="";
								if($SectArr[$hot_dispseq]->dispcnt>=$i) $display="style='display:none'";
								if($hot_linktype=="1") $display="style='display:none'";
								$icon_num=substr("0".($i+1),-2);

								$hotprlist20.="<tr height=28 align=center bgcolor=FFFFFF id=hotPrd".$i." ".$display.">\n";
								$hotprlist20.="	<td><input type=checkbox name='selectBoxPrd'></td>\n";
								$hotprlist20.="	<td style=padding-bottom:4><img src=images/icon_num".$icon_num.".gif border=0><input type=hidden name='hot_prdcode".$i."' value='".$hotprobj20[$i]->productcode."'></td>\n";
								$hotprlist20.="	<td id='hot_prdname".$i."' >".$hotprobj20[$i]->productname."</td>\n";
								$hotprlist20.="	<td style='padding-right:5'><input type=text readonly name='hot_prdprice".$i."' value='".(strlen($hotprobj20[$i]->sellprice)>0?number_format($hotprobj20[$i]->sellprice):"")."' style='text-align:right;border:0' size=10></td>\n";
								$hotprlist20.="	<td><input type=text readonly name='hot_prddpflag".$i."' value='".$hotprobj20[$i]->display."' style='border:0' size=1></td>\n";
								$hotprlist20.="	<td><a href='javascript:goDown(".$i.")'><img src=images/btn_down02.gif border=0 align=absmiddle></a> <a href='javascript:goUp(".$i.")'><img src=images/btn_up01.gif border=0 align=absmiddle></a></td>\n";
								$hotprlist20.="</tr>\n";

								$display="";
								if($SectArr[$hot_dispseq]->dispcnt>=$i) $display="style='display:none'";
								if($hot_linktype=="2") $display="style='display:none'";
								$icon_num=substr("0".($i+1),-2);

								$hotprlist10.="<tr height=28 align=center bgcolor=FFFFFF id=autoPrd".$i." ".$display.">\n";
								$hotprlist10.="	<td style=padding-bottom:4><img src=images/icon_num".$icon_num.".gif border=0></td>\n";
								$hotprlist10.="	<td><input type=text readonly name='auto_prdname".$i."' value='".$hotprobj10[$i]->productname."' size=60 style='text-align:left;border:0'><input type=hidden name='auto_prdcode".$i."' value='".$hotprobj10[$i]->productcode."'></td>\n";
								$hotprlist10.="	<td style='padding-right:5'><input type=text readonly name='auto_prdprice".$i."' value='".(strlen($hotprobj10[$i]->sellprice)>0?number_format($hotprobj10[$i]->sellprice):"")."' style='text-align:right;border:0' size=13></td>\n";
								$hotprlist10.="</tr>\n";
							}
?>
							<table width=100% border=0 cellspacing=1 cellpadding=0>
							<tr height=28 align=center bgcolor=FEFCE2 id=hotHeader>
								<td width=15% colspan=2><B>위치</B></td>
								<td width=58%><B>상품명</B></td>
								<td width=10%><B>가격</B></td>
								<td width=7%><B>진열</B></td>
								<td width=10%><B>순서</B></td>
							</tr>
							<?=$hotprlist20?>
							</table>

							<table width=100% border=0 cellspacing=1 cellpadding=0>
							<tr height=28 align=center bgcolor=FEFCE2 id=autoHeader>
								<td width=15%><B>위치</B></td>
								<td width=75%><B>상품명</B></td>
								<td width=10%><B>가격</B></td>
							</tr>
							<?=$hotprlist10?>
							</table>

							</td>
						</tr>
						</table>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				<tr><td height=1 bgcolor=#CDCDCD></td></tr>


				<tr><td height=40></td></tr>


				<tr>
				<td><img src="images/main_design_stitle02.gif" border=0 align=absmiddle alt=" NEW 신상품 관리"></td>
				</tr>
				<tr><td height=5></td></tr>
				<tr><td height=1 bgcolor=#cccccc></td></tr>
				<tr>
					<td align=center bgcolor=F8F8F8 style=padding:25>
					<img name=new_templt_img id=new_templt_img src=images/sample/display/section/<?=$SectArr[$new_dispseq]->disptype?>.gif border=0>
					</td>
				</tr>
				<tr><td height=5></td></tr>
				<tr><td height=1 bgcolor=#CDCDCD></td></tr>
				<tr>
					<td valign=top>
					<table width=100% border=0 cellspacing=0 cellpadding=0>
					<tr>
						<td width=140 bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B>사용여부</B></td>
						<td  style=padding:7,10><input type=radio name="new_used_flag_radio" <?if($new_used=="1")echo"checked";?>>예 &nbsp; &nbsp; <input type=radio name="new_used_flag_radio" <?if($new_used=="0")echo"checked";?>>아니오</td>
					</tr>
					<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
					<tr>
						<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B>상품진열 선택</B></td>
						<td valign=top style=padding:7,10>
						<select name="new_templt_dispseq" style=width:200 onChange="changeNewTempltIdx();">
<?
						$tmpsectval=$SectArr;
						while(list($key,$val)=each($tmpsectval)) {
							echo "<option value=\"".$key."\"";
							if($key==$new_dispseq) echo " selected";
							echo ">".$val->dispname."</option>\n";
						}
?>
						</select>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				<tr><td height=1 bgcolor=#CDCDCD></td></tr>

				<tr><td height=40></td></tr>

				<tr>
<td><img src="images/main_design_stitle03.gif" border=0 align=absmiddle alt="상품 전체 리스트 관리"></td>
				</tr>
				<tr><td height=5></td></tr>
				<tr><td height=1 bgcolor=#cccccc></td></tr>
				<tr>
					<td align=center bgcolor=F8F8F8 style=padding:25>
					<img name=prd_templt_img id=prd_templt_img src=images/sample/display/item_<?=$prlist_display?>.gif border=0>
					</td>
				</tr>
				<tr><td height=5></td></tr>
				<tr><td height=1 bgcolor=#CDCDCD></td></tr>
				<tr>
					<td valign=top>
					<table width=100% border=0 cellspacing=0 cellpadding=0>
					<tr>
						<td width=140 bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B>상품진열 방법</B></td>
						<td  style=padding:7,10>
						<select name=page_disptype style=width:160 onChange="javascript:changePrdTemplt();">
						<option value="I" <?if($prlist_display=="I")echo"selected";?>>이미지로 보기</option>
						<option value="D" <?if($prlist_display=="D")echo"selected";?>>더블형으로 보기</option>
						<option value="L" <?if($prlist_display=="L")echo"selected";?>>리스트형으로 보기</option>
						</select>
						</td>
					</tr>
					<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
					<tr>
						<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B>상품진열 개수</B></td>
						<td valign=top style=padding:7,10>
						<select name=page_disp_num style='width:160'>
						<option value="12" <?if($prlist_num==12)echo"selected";?>>12개 정렬</option>
						<option value="24" <?if($prlist_num==24)echo"selected";?>>24개 정렬</option>
						<option value="36" <?if($prlist_num==36)echo"selected";?>>36개 정렬</option>
						<option value="48" <?if($prlist_num==48)echo"selected";?>>48개 정렬</option>
						<option value="60" <?if($prlist_num==60)echo"selected";?>>60개 정렬</option>
						</select>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				<tr><td height=1 bgcolor=#CDCDCD></td></tr>
				<tr><td height=20></td></tr>
				<tr>
					<td align=center>
					<A HREF="javascript:formSubmit()"><img src="images/btn_save01.gif" border=0></A>
					</td>
				</tr>

				</form>

				</table>

				</td>
			</tr>
			<!-- 처리할 본문 위치 끝 -->

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

<iframe name="processFrame" src="about:blank" width="0" height="0" scrolling=no frameborder=no></iframe>

<script language=javascript>
	resetHotPrdList();
	changePrdTemplt;
</script>

<?=$onload?>

<? INCLUDE "copyright.php"; ?>