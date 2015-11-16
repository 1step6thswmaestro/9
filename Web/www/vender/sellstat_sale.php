<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/venderlib.php");
INCLUDE ("access.php");

?>

<? INCLUDE "header.php"; ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language=javascript src="sellstatCtgrPrdt.js.php"></script>



<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed"  height="100%" >
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
					<td><img src="images/sellstat_sale_title.gif"></td>
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
											<td class="notice_gray" height="20"><img src="images/icon_dot02.gif" border=0 hspace="4">입점사는 각각의 상품별 매출분석표를 이용하여 매출향상 여부를 확인할 수 있습니다.</td>
										</tr>
										<tr>
											<td class="notice_gray" height="20"><img src="images/icon_dot02.gif" border=0 hspace="4">상품에 대한 분석은 성별/회원/비회원 구분하여 분석자료를 수집할 수 있습니다.</td>
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
				



				
				<table border=0 cellpadding=0 cellspacing=0 width=100%>
				<tr>
					<td valign=top bgcolor=D4D4D4 style=padding:1>
					<table border=0 cellpadding=0 cellspacing=0 width=100%>
					<tr>
						<td valign=top bgcolor=F0F0F0 style=padding:10>
						<table border=0 cellpadding=0 cellspacing=0 width=100%>
						<form name="sForm" method="post">
						<input type="hidden" name="code" value="">
						<input type="hidden" name="prcode" value="">
						<tr>
							<td>
							<table border=0 cellpadding=0 cellspacing=5 align="center">
							<tr>
								<td>
								<select name="code1" style=width:155 onchange="ACodeSendIt(this.options[this.selectedIndex].value)">
								<option value="">------ 대 분 류 ------</option>
<?
								$sql = "SELECT SUBSTRING(productcode,1,3) as prcode FROM tblproduct ";
								$sql.= "WHERE vender='".$_VenderInfo->getVidx()."' ";
								$sql.= "AND display='Y' GROUP BY prcode ";
								$result=mysql_query($sql,get_db_conn());
								$codes="";
								while($row=mysql_fetch_object($result)) {
									$codes.=$row->prcode.",";
								}
								mysql_free_result($result);
								if(strlen($codes)>0) {
									$codes=substr($codes,0,-1);
									$prcodelist=ereg_replace(',','\',\'',$codes);
								}
								if(strlen($prcodelist)>0) {
									$sql = "SELECT codeA,codeB,codeC,codeD,code_name FROM tblproductcode ";
									$sql.= "WHERE codeA IN ('".$prcodelist."') AND codeB='000' AND codeC='000' ";
									$sql.= "AND codeD='000' AND type LIKE 'L%' ORDER BY sequence DESC ";
									$result=mysql_query($sql,get_db_conn());
									while($row=mysql_fetch_object($result)) {
										echo "<option value=\"".$row->codeA."\"";
										if($row->codeA==substr($code,0,3)) echo " selected";
										echo ">".$row->code_name."</option>\n";
									}
									mysql_free_result($result);
								}
?>
								</select>
								</td>
								<td></td>
								<td>
								<iframe name="BCodeCtgr" src="sellstat_sale.ctgr.php?depth=2" width="155" height="21" scrolling=no frameborder=no></iframe>
								</td>
								<td></td>
								<td><iframe name="CCodeCtgr" src="sellstat_sale.ctgr.php?depth=3" width="155" height="21" scrolling=no frameborder=no></iframe></td>
								<td></td>
								<td><iframe name="DCodeCtgr" src="sellstat_sale.ctgr.php?depth=4" width="155" height="21" scrolling=no frameborder=no></iframe></td>
							</tr>
							</table>
							</td>
						</tr>

						</form>

						</table>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				<tr><td height=5></td></tr>
				<tr>
					<td>
					<iframe name="PrdtListIfrm" src="sellstat_sale.prlist.php" width="100%" height="190" scrolling=no frameborder=no style="background:FFFFFF"></iframe>
					</td>
				</tr>
				<tr><td height=15></td></tr>
				<tr>
					<td>

					<table border=0 cellpadding=3 cellspacing=1 width=100% bgcolor=#D4D4D4>
					<form name=form1 method=post>
					<input type=hidden name=code>
					<input type=hidden name=prcode>
					<tr>
						<td width=70 bgcolor=#F0F0F0 align=right style="padding-right:10" nowrap>기간 선택</td>
						<td width=30% bgcolor=#FFFFFF style="padding-left:5">
						<select name=date_year>
<?
						for($i=(int)substr($_venderdata->regdate,0,4);$i<=date("Y");$i++) {
							echo "<option value=\"".$i."\" ";
							if($i==date("Y")) echo "selected";
							echo ">".$i."</option>\n";
						}
?>
						</select>년
						<select name=date_month>
						<option value="ALL">전체</option>
<?
						for($i=1;$i<=12;$i++) {
							$ii=substr("0".$i,-2);
							echo "<option value=\"".$ii."\" ";
							if($ii==date("m")) echo "selected";
							echo ">".$ii."</option>\n";
						}
?>
						</select>월
						</td>
						<td width=70 bgcolor=#F0F0F0 align=right style="padding-right:10" nowrap>연령별</td>
						<td width=30% bgcolor=#FFFFFF style="padding-left:5">
						<input type=text name=age1 value="0" maxlength=3 style="width:35;padding-left:5" onkeyup="strnumkeyup(this);">살부터
						<input type=text name=age2 value="0" maxlength=3 style="width:35;padding-left:5" onkeyup="strnumkeyup(this);">까지
						</td>
						<td width=70 bgcolor=#F0F0F0 align=right style="padding-right:10" nowrap>지역별</td>
						<td width=30% bgcolor=#FFFFFF style="padding-left:5">
						<select name=loc>
						<option value="ALL">전체</option>
<?
						$loclist=array("서울","부산","대구","인천","광주","대전","울산","강원","경기","경남","경북","충남","충북","전남","전북","제주","기타");
						for($i=0;$i<count($loclist);$i++) {
							echo "<option value=\"".$loclist[$i]."\">".$loclist[$i]."</option>\n";
						}
?>
						</select>
						</td>
					</tr>
					<tr>
						<td width=70 bgcolor=#F0F0F0 align=right style="padding-right:10" nowrap>성별</td>
						<td width=30% bgcolor=#FFFFFF style="padding-left:5">
						<select name=sex>
						<option value="ALL">전체</option>
						<option value="M">남자</option>
						<option value="F">여자</option>
						</select>
						</td>
						<td width=70 bgcolor=#F0F0F0 align=right style="padding-right:10" nowrap>회원구분</td>
						<td width=30% bgcolor=#FFFFFF style="padding-left:5">
						<select name=member>
						<option value="ALL">전체</option>
						<option value="Y">회원</option>
						<option value="N">비회원</option>
						</select>
						</td>
						<td colspan=2 bgcolor=#FFFFFF align=center><A HREF="javascript:SellStat()"><img src=images/btn_confirm03.gif border=0></A></td>
					</tr>
					</form>
					</table>

					</td>
				</tr>
				<tr><td height=5></td></tr>
				<tr>
					<td>
					<iframe name="StatIfrm" src="blank.php" width="100%" height="0" scrolling=no frameborder=no style="background:FFFFFF"></iframe>
					</td>
				</tr>
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

<SCRIPT FOR=BCodeCtgr EVENT=onload LANGUAGE="JScript">
  loadedNum++;
  if(bodyOnLoad == 1 && loadedNum == 3) f_getData();
</SCRIPT>
<SCRIPT FOR=CCodeCtgr EVENT=onload LANGUAGE="JScript">
  loadedNum++;
  if(bodyOnLoad == 1 && loadedNum == 3) f_getData();
</SCRIPT>
<SCRIPT FOR=DCodeCtgr EVENT=onload LANGUAGE="JScript">
  loadedNum++;
  if(bodyOnLoad == 1 && loadedNum == 3) f_getData();
</SCRIPT>

<iframe name="processFrame" src="about:blank" width="0" height="0" scrolling=no frameborder=no></iframe>

<?=$onload?>

<? INCLUDE "copyright.php"; ?>