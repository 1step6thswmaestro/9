<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/venderlib.php");
INCLUDE ("access.php");

$curdate = date("Ymd");
$curdate_1 = date("Ymd",mktime(0,0,0,date("m"),date("d")-1,date("Y")));

$sql = "SELECT ";
//오늘 주문건수 및 주문금액
$sql.= "COUNT(DISTINCT(IF(a.ordercode LIKE '".$curdate."%',a.ordercode,NULL))) as totordcnt, ";
$sql.= "SUM(IF(a.ordercode LIKE '".$curdate."%',b.price*b.quantity,0)) as totordprice, ";
//오늘 미배송 건수 및 미배송건 금액
$sql.= "COUNT(DISTINCT(IF((a.ordercode LIKE '".$curdate."%') && (b.deli_gbn IN('N','X','S')),a.ordercode,NULL))) as totdelaycnt, ";
$sql.= "SUM(IF((a.ordercode LIKE '".$curdate."%') && (b.deli_gbn IN('N','X','S')),b.price*b.quantity,0)) as totdelayprice, ";

//1일전 주문건수 및 주문금액
$sql.= "COUNT(DISTINCT(IF(a.ordercode LIKE '".$curdate_1."%',a.ordercode,NULL))) as totordcnt1, ";
$sql.= "SUM(IF(a.ordercode LIKE '".$curdate_1."%',b.price*b.quantity,0)) as totordprice1, ";
//1일전 미배송 건수 및 미배송건 금액
$sql.= "COUNT(DISTINCT(IF((a.ordercode LIKE '".$curdate_1."%') && (b.deli_gbn IN('N','X','S')),a.ordercode,NULL))) as totdelaycnt1, ";
$sql.= "SUM(IF((a.ordercode LIKE '".$curdate_1."%') && (b.deli_gbn IN('N','X','S')),b.price*b.quantity,0)) as totdelayprice1, ";

//이달 주문건수 및 매출
$sql.= "COUNT(DISTINCT(IF(a.ordercode LIKE '".substr($curdate,0,6)."%',a.ordercode,NULL))) as totmonordcnt, ";
$sql.= "SUM(IF((a.ordercode LIKE '".substr($curdate,0,6)."%' AND a.deli_gbn='Y'),b.price*b.quantity,0)) as totmonordprice ";

$sql.= "FROM tblorderinfo a, tblorderproduct b WHERE b.vender='".$_VenderInfo->getVidx()."' AND a.ordercode=b.ordercode ";
if(substr($curdate,0,6)!=substr($curdate_1,0,6)) {
	$sql.="AND (a.ordercode LIKE '".substr($curdate,0,6)."%' OR a.ordercode LIKE '".$curdate_1."%') ";
} else {
	$sql.="AND a.ordercode LIKE '".substr($curdate,0,6)."%' ";
}
$sql.= "AND NOT (b.productcode LIKE 'COU%' OR b.productcode LIKE '999999%') ";
$filename=$_VenderInfo->getVidx().".admin.order.cache";
get_db_cache($sql, $resval, $filename, 30);
$row=$resval[0];
unset($resval);

$totordcnt=(int)$row->totordcnt;			//오늘 주문건수
$totordprice=(int)$row->totordprice;		//오늘 주문금액
$totdelaycnt=(int)$row->totdelaycnt;		//오늘 미배송건수
$totdelayprice=(int)$row->totdelayprice;	//오늘 미배송금액

$totordcnt1=(int)$row->totordcnt1;			//1일전 주문건수
$totordprice1=(int)$row->totordprice1;		//1일전 주문금액
$totdelaycnt1=(int)$row->totdelaycnt1;		//1일전 미배송건수
$totdelayprice1=(int)$row->totdelayprice1;	//1일전 미배송금액

$totmonordcnt=(int)$row->totmonordcnt;		//이달의 주문건수
$totmonordprice=(int)$row->totmonordprice;	//이달의 매출

$sql = "SELECT SUM(cnt) as totmonvstcnt FROM tblvenderstorevisit WHERE vender='".$_VenderInfo->getVidx()."' AND date LIKE '".substr($curdate,0,6)."%' ";
$result=mysql_query($sql,get_db_conn());
$row=mysql_fetch_object($result);
$totmonvstcnt=(int)$row->totmonvstcnt;

?>

<? INCLUDE "header.php"; ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function GoNoticeView(artid) {
	url="shop_notice.php?type=view&artid="+artid;
	document.location.href=url;
}
function GoCounselView(artid) {
	url="shop_counsel.php?type=view&artid="+artid;
	document.location.href=url;
}
</script>

<table border=0 cellpadding=0 cellspacing=0 width=100% height="100%" style="table-layout:fixed">
<col width=190></col>
<col width=20></col>
<col width=></col>
<col width=20></col>
<tr>
	<td width=190 valign=top nowrap background="images/minishop_leftbg.gif"><? include ("menu.php"); ?></td>
	<td width=20 nowrap></td>
	<td valign=top>

	<table width="100%"  border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td>
		<table width="100%"  border="0" cellpadding="0" cellspacing="0"  bgcolor="#ffffff">
		<tr>
			<td style="padding-top:30px">

			<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
			<col width=></col>
			<col width=10></col>
			<col width=220></col>
			<tr>
				<td valign=top>
				<!-- 중앙 내용 시작 -->
				<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
				<tr>
					<td>



					<table border=0 cellpadding=0 cellspacing=0 width=100%   style="table-layout:fixed">
					<tr>
						<td valign=top>



						<table border=0 cellpadding=0 cellspacing=0 width=100% height=100% style="table-layout:fixed">
						<col width=></col>
						<col width=></col>
						<col width=></col>
						<tr>
							<td valign=top style="padding:2">
							<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
							<tr height=25>
								<td bgcolor=#F2F2F6 style="padding:7,5"><img src=images/icon_dot07.gif border=0 width=5 height=13 align=absmiddle> <b>오늘 현황</b> <img src=images/icon_today.gif border=0 align=absmiddle></td>
							</tr>
							<tr><td height=1 bgcolor=#FFFFFF></td></tr>
							<tr>
								<td height=80 valign=top style="padding:9;border:1px solid #E7E7E7">
								<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
								<col width=7></col>
								<col width=50></col>
								<col width=20></col>
								<col width=></col>
								<tr>
									<td><img src="images/icon_dot01.gif"></td>
									<td style="padding-left:5" class="font_gray"><A HREF="order_list.php">주문수</A></td>
									<td>:</td>
									<td><font class=verdana style="font-size:8pt"><?=$totordcnt?></font>건</td>
								</tr>
								<tr>
									<td><img src="images/icon_dot01.gif"></td>
									<td style="padding-left:5" class="font_gray"><A HREF="order_list.php">주문액</A></td>
									<td>:</td>
									<td><font class=verdana style="font-size:8pt"><?=number_format($totordprice)?></font>원</td>
								</tr>
								<tr>
									<td><img src="images/icon_dot01.gif"></td>
									<td style="padding-left:5" class="font_gray"><A HREF="order_list.php">미배송</A></td>
									<td>:</td>
									<td><font class=verdana style="font-size:8pt"><?=$totdelaycnt?></font>건</td>
								</tr>
								</table>
								</td>
							</tr>
							</table>
							</td>

							<td valign=top style="padding:2">
							<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
							<tr height=25>
								<td bgcolor=#F2F2F6 style="padding:7,5"><img src=images/icon_dot07.gif border=0 width=5 height=13 align=absmiddle> <b>어제 현황</b></td>
							</tr>
							<tr><td height=1 bgcolor=#FFFFFF></td></tr>
							<tr>
								<td height=80 valign=top style="padding:9;border:1px solid #E7E7E7">
								<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
								<col width=7></col>
								<col width=50></col>
								<col width=20></col>
								<col width=></col>
								<tr>
									<td><img src="images/icon_dot01.gif"></td>
									<td style="padding-left:5" class="font_gray"><A HREF="order_list.php">주문수</A></td>
									<td>:</td>
									<td><font class=verdana style="font-size:8pt"><?=$totordcnt1?></font>건</td>
								</tr>
								<tr>
									<td><img src="images/icon_dot01.gif"></td>
									<td style="padding-left:5" class="font_gray"><A HREF="order_list.php">주문액</A></td>
									<td>:</td>
									<td><font class=verdana style="font-size:8pt"><?=number_format($totordprice1)?></font>원</td>
								</tr>
								<tr>
									<td><img src="images/icon_dot01.gif"></td>
									<td style="padding-left:5" class="font_gray"><A HREF="order_list.php">미배송</A></td>
									<td>:</td>
									<td><font class=verdana style="font-size:8pt"><?=$totdelaycnt1?></font>건</td>
								</tr>
								</table>
								</td>
							</tr>
							</table>
							</td>

							<td valign=top style="padding:2">
							<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
							<tr height=25>
								<td bgcolor=#F2F2F6 style="padding:7,5"><img src=images/icon_dot07.gif border=0 width=5 height=13 align=absmiddle> <b>이달 현황</b></td>
							</tr>
							<tr><td height=1 bgcolor=#FFFFFF></td></tr>
							<tr>
								<td height=80 valign=top style="padding:9;border:1px solid #E7E7E7">
								<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
								<col width=7></col>
								<col width=50></col>
								<col width=20></col>
								<col width=></col>
								<tr>
									<td><img src="images/icon_dot01.gif"></td>
									<td style="padding-left:5" class="font_gray"><A HREF="sellstat_list.php">주문수</A></td>
									<td>:</td>
									<td><font class=verdana style="font-size:8pt"><?=$totmonordcnt?></font>건</td>
								</tr>
								<tr>
									<td><img src="images/icon_dot01.gif"></td>
									<td style="padding-left:5" class="font_gray"><A HREF="sellstat_list.php">매출액</A></td>
									<td>:</td>
									<td><font class=verdana style="font-size:8pt"><?=number_format($totmonordprice)?></font>원</td>
								</tr>
								<tr>
									<td><img src="images/icon_dot01.gif"></td>
									<td style="padding-left:5" class="font_gray">방문수</td>
									<td>:</td>
									<td><font class=verdana style="font-size:8pt"><?=number_format($totmonvstcnt)?></font>명</td>
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
				</tr>
				<tr><td height=20></td></tr>
				<tr>
					<td valign=top>
					<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
					<col width=></col>
					<col width=200></col>
					<tr>
						<td><img src=images/main_noticetitle.gif border=0></td>
						<td align=right>
						<A HREF="shop_notice.php"><img src=images/btn_readall.gif border=0></A>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				<tr><td height=3></td></tr>
				<tr><td height=2 bgcolor=E7E7E7></td></tr>
				<tr><td height=5></td></tr>
				<tr>
					<td>
					<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
<?
					$sql = "SELECT date,subject,access FROM tblvenderadminnotice ";
					$sql.= "WHERE (vender='".$_VenderInfo->getVidx()."' OR vender='0') ";
					$sql.= "ORDER BY date DESC LIMIT 5";
					$result=mysql_query($sql,get_db_conn());
					$i=0;
					while($row=mysql_fetch_object($result)) {
						$date=substr($row->date,4,2)."/".substr($row->date,6,2);
						echo "<tr height=20>\n";
						echo "	<td style=\"padding:3,10\"><img src=images/icon_dot04.gif border=0 align=absmiddle hspace=3> <A HREF=\"javascript:GoNoticeView('".$row->date."')\"><font class='verdana' style='font-size:8pt'>[".$date."]</font> ".strip_tags($row->subject)."</A></td>\n";
						echo "</tr>\n";
						$i++;
					}
					mysql_free_result($result);
?>
					</table>
					</td>
				</tr>
				<tr><td height=20></td></tr>
				<tr>
					<td valign=top>
					<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
					<col width=></col>
					<col width=200></col>
					<tr>
						<td><img src=images/main_qnatitle.gif border=0></td>
						<td align=right>
						<A HREF="shop_counsel.php?type=write"><img src=images/btn_qnawrite.gif border=0></A>
						<A HREF="shop_counsel.php"><img src=images/btn_readall.gif border=0></A>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				<tr><td height=3></td></tr>
				<tr><td height=2 bgcolor=E7E7E7></td></tr>
				<tr><td height=5></td></tr>
				<tr>
					<td>
					<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
					<col width=></col>
					<col width=70></col>
<?
					$sql = "SELECT date,subject,access,re_date FROM tblvenderadminqna ";
					$sql.= "WHERE vender='".$_VenderInfo->getVidx()."' ";
					$sql.= "ORDER BY date DESC LIMIT 5";
					$result=mysql_query($sql,get_db_conn());
					$i=0;
					while($row=mysql_fetch_object($result)) {
						$date=substr($row->date,4,2)."/".substr($row->date,6,2);
						$re_icn="";
						if(strlen($row->re_date)==14) {
							$re_icn="<img src=images/icn_counsel_ok.gif border=0>";
						} else {
							$re_icn="<img src=images/icn_counsel_no.gif border=0>";
						}
						echo "<tr height=20 bgcolor=#FFFFFF onmouseover=\"this.style.background='#ffffff'\" onmouseout=\"this.style.background='#FFFFFF'\">\n";
						echo "	<td style=\"padding:3,10\"><img src=images/icon_dot04.gif border=0 align=absmiddle hspace=3> <A HREF=\"javascript:GoCounselView('".$row->date."')\"><font class='verdana' style='font-size:8pt'>[".$date."]</font> ".strip_tags($row->subject)."</A></td>\n";
						echo "	<td align=center>".$re_icn."</td>\n";
						echo "</tr>\n";
						$i++;
					}
					mysql_free_result($result);
?>
					</table>
					</td>
				</tr>
				</table>
				<!-- 중앙 내용 끝 -->
				</td>

				<td></td>

				<td valign=top>
				<table width="100%" border="0" cellpadding="0" cellspacing="0" style="table-layout:fixed;border:1px solid #EEEEEE" bgcolor="#ffffff">
				<tr>
					<td style="padding:3" valign=top>
					<!-- 오른쪽 내용 시작 -->
<?
					$sql = "SELECT * FROM tblvenderstorecount WHERE vender='".$_VenderInfo->getVidx()."' ";
					$result=mysql_query($sql,get_db_conn());
					$row=mysql_fetch_object($result);
					mysql_free_result($result);
					$prdt_allcnt=$row->prdt_allcnt;
					$prdt_cnt=$row->prdt_cnt;
					$cust_cnt=$row->cust_cnt;
					$count_total=$row->count_total;
					$count_today=0;

					$period_0 = date("Ymd");
					$period_1 = date("Ymd",time()-(60*60*24*1));
					$period_2 = date("Ymd",time()-(60*60*24*2));
					$period_3 = date("Ymd",time()-(60*60*24*3));
					$period_4 = date("Ymd",time()-(60*60*24*4));
					$period_5 = date("Ymd",time()-(60*60*24*5));
					$period_6 = date("Ymd",time()-(60*60*24*6));
					$period_7 = date("Ymd",time()-(60*60*24*7));
					$visit[$period_1]=0;
					$visit[$period_2]=0;
					$visit[$period_3]=0;
					$visit[$period_4]=0;
					$visit[$period_5]=0;
					$visit[$period_6]=0;
					$visit[$period_7]=0;
					$sql = "SELECT date,cnt FROM tblvenderstorevisit ";
					$sql.= "WHERE vender='".$_VenderInfo->getVidx()."' ";
					$sql.= "AND date<='".$period_0."' AND date >='".$period_7."' ";
					$result=mysql_query($sql,get_db_conn());
					$sumvisit=0;
					while($row=mysql_fetch_object($result)) {
						if($row->date==$period_0) {
							$count_today=$row->cnt;
						} else {
							$sumvisit=$sumvisit+$row->cnt;
							$visit[$row->date]=$row->cnt;
						}
					}
					mysql_free_result($result);
?>
					<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
					<tr>
						<td bgcolor=#F2F2F6 style="padding:7">
						<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
						<tr><td height=7></td></tr>
						<tr>
							<td><img src=images/icon_dot07.gif border=0 width=5 height=13 align=absmiddle> <b>내 판매상품 현황</b></td>
						</tr>
						<tr><td height=5></td></tr>
						<tr>
							<td bgcolor=#FFFFFF style="padding:10,15;border:1px solid #E7E7E7">
							<img src=images/icon_dot06.gif border=0 align=absmiddle> 상품등록 제한<?=($_venderdata->product_max>0?"<font class=verdana style=\"font-size:8pt\"><B>".$_venderdata->product_max."</B></font> 개":"<B>무제한</B>")?>
							<br><img width=0 height=3></br>
							<img src=images/icon_dot06.gif border=0 align=absmiddle> 등록 상품(판매중)<font class=verdana style="font-size:8pt"><B><?=$prdt_allcnt?></B></font> 개
							<br><img width=0 height=3></br>
							<img src=images/icon_dot06.gif border=0 align=absmiddle> <font color=#737373>진열중/진열안함</font><font class=verdana style="font-size:8pt"><B><?=$prdt_cnt?></B>개/<font class=verdana style="font-size:8pt"><B><?=$prdt_allcnt?></B>개
							</td>
						</tr>
						<tr><td height=20></td></tr>
						<tr>
							<td><img src=images/icon_dot07.gif border=0 width=5 height=13 align=absmiddle> <b>내 미니샵 방문현황</b></td>
						</tr>
						<tr><td height=5></td></tr>
						<tr>
							<td bgcolor=#FFFFFF style="padding:10,15;border:1px solid #E7E7E7">
							<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
							<col width=35></col>
							<col width=></col>
							<col width=40></col>
<?
							$MAX_barsize=88;
							while(list($key,$val)=each($visit)) {
								echo "<tr height=17>\n";
								echo "	<td style=\"font-size:8pt;color:737373\">".substr($key,4,2)."/".substr($key,6,2)."</td>\n";
								echo "	<td style=\"font-size:8pt;color:737373\">";
								if($val>0) {
									echo "<img src=\"images/icon_dot07.gif\" width=".@round(($val / $sumvisit)*$MAX_barsize)." height=3 align=absmiddle>";
								}
								echo "	</td>\n";
								echo "	<td align=right style=\"font-size:8pt;color:737373\">".number_format($val)."명</td>\n";
								echo "</tr>\n";
							}
?>
							<tr><td colspan=3 height=5></td></tr>
							<tr><td colspan=3 height=1 background=images/bg_storeInfo.gif></td></tr>
							<tr><td colspan=3 height=5></td></tr>
							<tr>
								<td colspan=3>
								오늘/전체<font class=verdana style="font-size:8pt"><B><?=$count_today?></B>명/<font class=verdana style="font-size:8pt"><B><?=$count_total?></B>명
								</td>
							</tr>
							</table>
							</td>
						</tr>
						</table>
						</td>
					</tr>
					<tr><td height=5></td></tr>

					<tr>
						<td valign=top bgcolor=#FEFCDA style="padding:7">
						<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
						<tr><td height=7></td></tr>
						<tr>
							<td><img src=images/icon_dot07.gif border=0 width=5 height=13 align=absmiddle> <b>주요기능 바로가기</b></td>
						</tr>
						<tr><td height=5></td></tr>
						<tr>
							<td bgcolor=#FFFFFF >
							<table border=0 cellpadding=0 cellspacing=0 width=100%>
								<tr>
									<td bgcolor=#FFFFFF style="padding:10,15;border:1px solid #E7E7E7">
									<img src=images/icon_dot06.gif border=0 align=absmiddle> <A HREF="delivery_info.php">배송관련기능설정</A>
									<br><img width=0 height=3></br>
									<img src=images/icon_dot06.gif border=0 align=absmiddle> <A HREF="minishop_design.php">디자인관리</A>
									<br><img width=0 height=3></br>
									<img src=images/icon_dot06.gif border=0 align=absmiddle> <A HREF="main_design.php">메인화면관리</A>
									<br><img width=0 height=3></br>
									<img src=images/icon_dot06.gif border=0 align=absmiddle> <A HREF="minishop_notice.php">미니샵공지사항</A>
									<br><img width=0 height=3></br>
									<img src=images/icon_dot06.gif border=0 align=absmiddle> <A HREF="product_register.php"><b>상품등록</b></A>
									<br><img width=0 height=3></br>
									<img src=images/icon_dot06.gif border=0 align=absmiddle> <A HREF="product_myprd.php"><b>상품관리</b></A>
									<br><img width=0 height=3></br>
									<img src=images/icon_dot06.gif border=0 align=absmiddle> <A HREF="order_list.php"><b>주문조회</b></A
									<br><img width=0 height=3></br>
									<img src=images/icon_dot06.gif border=0 align=absmiddle> <A HREF="sellstat_list.php"><b>정산조회</b></A>
									<br><img width=0 height=3></br>
									<img src=images/icon_dot06.gif border=0 align=absmiddle> <A HREF="sellstat_sale.php"><b>매출분석</b></A>
									<br><img width=0 height=3></br>
									<img src=images/icon_dot06.gif border=0 align=absmiddle> <A HREF="coupon_list.php">쿠폰관리</A>
									</td>
							</tr>

							</table>
							</td>
						</tr>
						</table>
						</td>
						</tr>












					</table>
					<!-- 오른쪽 내용 끝 -->
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

	</td>
</tr>
</table>

<?=$onload?>

<? INCLUDE "copyright.php"; ?>