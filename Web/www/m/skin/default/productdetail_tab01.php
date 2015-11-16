<script>
function openBicImage()
{
	window.open("productdetail_image_popup.php?productcode=<?=$_GET[productcode]?>","","");
}
</script>

<!-- view탭 -->
<?
//상품평 수
$sql_cnt3 = "SELECT COUNT(*) as t_count FROM tblproductreview WHERE productcode='$_GET[productcode]'";
$result_cnt3=mysql_query($sql_cnt3,get_db_conn());
$row_cnt3=mysql_fetch_object($result_cnt3);
$t_cnt3 = (int)$row_cnt3->t_count;

//상품문의 수
$pridx=$_pdata->pridx;
$sql_cnt4 = "SELECT COUNT(*) as t_count FROM tblboard WHERE board='$prqnaboard' and pridx = '$pridx'";
$result_cnt4=mysql_query($sql_cnt4,get_db_conn());
$row_cnt4=mysql_fetch_object($result_cnt4);
$t_cnt4 = (int)$row_cnt4->t_count;
?>
<div class="pr_detail">

<section class="tab_area">
	<ul class="tab_type1 tab01">
		<li class="active"><a href="productdetail_tab01.php?productcode=<?=$productcode?>&sort=<?=$sort?>#tapTop" rel="external">기본정보</a></li>
		<!-- <li><a href="productdetail_tab02.php?productcode=<?=$productcode?>&sort=<?=$sort?>" rel="external">상세정보</a></li> -->
		<li><a href="productdetail_tab03.php?productcode=<?=$productcode?>&sort=<?=$sort?>#tapTop" rel="external">상품평(<?=$t_cnt3?>)</a></li>
		<li><a href="productdetail_tab04.php?productcode=<?=$productcode?>&sort=<?=$sort?>#tapTop" rel="external">상품문의(<?=$t_cnt4?>)</a></li>
	</ul>
</section>
<!-- //view탭 -->
<section style="margin:25px 20px; text-align:center">
	<p style="margin-bottom:8px;">핑거 줌으로 상품 상세정보를 확대해서 보고싶을 경우 아래 [상품정보 확대보기] 버튼을 클릭하세요.</p>
	<button class="button white medium" onClick="prdetailView();">상품정보 확대보기</button>
</section>
<section class="detail_02">
	<?
		if(strlen($detail_filter)>0) {
			$_pdata->content = preg_replace($filterpattern,$filterreplace,$_pdata->content);
		}

		if (strpos($_pdata->content,"table>")!=false || strpos($_pdata->content,"TABLE>")!=false)
			echo "<pre>".$_pdata->content."</pre>";
		else if(strpos($_pdata->content,"</")!=false)
			echo ereg_replace("\n","<br>",$_pdata->content);
		else if(strpos($_pdata->content,"img")!=false || strpos($_pdata->content,"IMG")!=false)
			echo ereg_replace("\n","<br>",$_pdata->content);
		else
			echo ereg_replace(" ","&nbsp;",ereg_replace("\n","<br>",$_pdata->content));
	?>
</section>

<?
	if(nameTechUse($_pdata->vender)){
		$venderinfoSQL = "SELECT COUNT(p.pridx) AS prcount, v.com_name, v.com_owner, v.com_image ";
		$venderinfoSQL .= "FROM tblproduct AS p LEFT OUTER JOIN tblvenderinfo AS v ON(p.vender = v.vender) ";
		$venderinfoSQL .= "WHERE v.vender = '".$_pdata->vender."' ";
		
		$venderimagedir = $Dir."data/shopimages/vender/";
		$venderprcount=$vendername=$ownername=$image=$src=$vendersize="";
		if(false !==$venderinfoRes = mysql_query($venderinfoSQL,get_db_conn())){
			$venderprcount=mysql_result($venderinfoRes,0,0);
			$vendername=mysql_result($venderinfoRes,0,1);
			$ownername=mysql_result($venderinfoRes,0,2);
			$image=mysql_result($venderinfoRes,0,3);
			$src = $venderimagedir.$image;

			$vendersize = _getImageRateSize($src,80);
?>
	<section>
		<div class="venderNametag">
			<?if(strlen($image)>0){?>
				<div style="float:left; width:100px; margin-top:2px; text-align:center;"><img src="<?=$src?>" <?=$vendersize?> /></div>
			<?}?>
			<div style="float:left; margin-left:5px;">
				<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<th>상호(대표)</th>
						<td> : <?=$vendername?> (<?=$ownername?>)</td>
					</tr>
					<tr>
						<th>등록상품</th>
						<td> : <?=$venderprcount?>개</td>
					</tr>
				</table>
				<div style="margin-top:4px;"><a href="./venderinfo.php?vidx=<?=$_pdata->vender?>" rel="external" class="button small black">전체상품보기</a></div>
			</div>
		</div>
	</section>
<?
		}
	}
?>

<section>
	<div class="information">
		<span>상품정보고시와 배송/AS/환불 안내 규정은 PC버전에서 확인할 수 있습니다.</span>
		<div><a href="../front/productdetail.php?productcode=<?=$_pdata->productcode?>#2" class="button white small">확인하기</a></div>
	</div>
</section>

</div>
<!-- TAB1-기본정보 -->

<script language="JavaScript">
var miniq=<?=($miniq>1?$miniq:1)?>;
var ardollar=new Array(3);
ardollar[0]="<?=$ardollar[0]?>";
ardollar[1]="<?=$ardollar[1]?>";
ardollar[2]="<?=$ardollar[2]?>";
<?
if(strlen($optcode)==0) {
	$maxnum=($count2-1)*10;
	if($optioncnt>0) {
		echo "num = new Array(";
		for($i=0;$i<$maxnum;$i++) { 
			if ($i!=0) echo ",";
			if(strlen($optioncnt[$i])==0) echo "100000";
			else echo $optioncnt[$i]; 
		} 
		echo ");\n";
	}
?>

function change_price(temp,temp2,temp3) {

<?=(strlen($dicker)>0)?"return;\n":"";?>
	if(temp3=="") temp3=1;
	price = new Array(<?if($priceindex>0) echo "'".number_format($_pdata->sellprice)."','".number_format($_pdata->sellprice)."',"; for($i=0;$i<$priceindex;$i++) { if ($i!=0) { echo ",";} echo "'".$pricetok[$i]."'"; } ?>);
	doprice = new Array(<?if($priceindex>0) echo "'".number_format($_pdata->sellprice/$ardollar[1],2)."','".number_format($_pdata->sellprice/$ardollar[1],2)."',"; for($i=0;$i<$priceindex;$i++) { if ($i!=0) { echo ",";} echo "'".$pricetokdo[$i]."'"; } ?>);
	if(temp==1) {
		if (document.form1.option1.selectedIndex><? echo $priceindex+2 ?>) 
			temp = <?=$priceindex?>;
		else temp = document.form1.option1.selectedIndex;
		document.form1.price.value = price[temp];
		document.all["idx_price"].innerHTML = document.form1.price.value+"원";
<?if($_pdata->reservetype=="Y" && $_pdata->reserve>0) { ?>
		if(document.getElementById("idx_reserve")) {
			var reserveInnerValue="0";
			if(document.form1.price.value.length>0) {
				var ReservePer=<?=$_pdata->reserve?>;
				var ReservePriceValue=Number(document.form1.price.value.replace(/,/gi,""));
				if(ReservePriceValue>0) {
					reserveInnerValue = Math.round(ReservePer*ReservePriceValue*0.01)+"";
					var result = "";
					for(var i=0; i<reserveInnerValue.length; i++) {
						var tmp = reserveInnerValue.length-(i+1);
						if(i%3==0 && i!=0) result = "," + result;
						result = reserveInnerValue.charAt(tmp) + result;
					}
					reserveInnerValue = result;
				}
			}
			document.getElementById("idx_reserve").innerHTML = reserveInnerValue+"원";
		}
<? } ?>
		if(typeof(document.form1.dollarprice)=="object") {
			document.form1.dollarprice.value = doprice[temp];
			document.all["idx_dollarprice"].innerHTML=ardollar[0]+" "+document.form1.dollarprice.value+" "+ardollar[2];
		}
	}
	packagecal(); //패키지 상품 적용
	if(temp2>0 && temp3>0) {
		if(num[(temp3-1)*10+(temp2-1)]==0){
			alert('해당 상품의 옵션은 품절되었습니다. 다른 상품을 선택하세요');
			if(document.form1.option1.type!="hidden") document.form1.option1.focus();
			return;
		}
	} else {
		if(temp2<=0 && document.form1.option1.type!="hidden") document.form1.option1.focus();
		else document.form1.option2.focus();
		return;
	}
}

<? } else if(strlen($optcode)>0) { ?>

function chopprice(temp){
<?=(strlen($dicker)>0)?"return;\n":"";?>
	ind = document.form1.mulopt[temp];
	price = ind.options[ind.selectedIndex].value;
	originalprice = document.form1.price.value.replace(/,/g, "");
	document.form1.price.value=Number(originalprice)-Number(document.form1.opttype[temp].value);
	if(price.indexOf(",")>0) {
		optprice = price.substring(price.indexOf(",")+1);
	} else {
		optprice=0;
	}
	document.form1.price.value=Number(document.form1.price.value)+Number(optprice);
	if(typeof(document.form1.dollarprice)=="object") {
		document.form1.dollarprice.value=(Math.round(((Number(document.form1.price.value))/ardollar[1])*100)/100);
		document.all["idx_dollarprice"].innerHTML=ardollar[0]+" "+document.form1.dollarprice.value+" "+ardollar[2];
	}
	document.form1.opttype[temp].value=optprice;
	var num_str = document.form1.price.value.toString()
	var result = ''

	for(var i=0; i<num_str.length; i++) {
		var tmp = num_str.length-(i+1)
		if(i%3==0 && i!=0) result = ',' + result
		result = num_str.charAt(tmp) + result
	}
	document.form1.price.value = result;
	document.all["idx_price"].innerHTML=document.form1.price.value+"원";
	packagecal(); //패키지 상품 적용
}

<?}?>
<? if($_pdata->assembleuse=="Y") { ?>
function setTotalPrice(tmp) {
<?=(strlen($dicker)>0)?"return;\n":"";?>
	var i=true;
	var j=1;
	var totalprice=0;
	while(i) {
		if(document.getElementById("acassemble"+j)) {
			if(document.getElementById("acassemble"+j).value) {
				arracassemble = document.getElementById("acassemble"+j).value.split("|");
				if(arracassemble[2].length) {
					totalprice += arracassemble[2]*1;
				}
			}
		} else {
			i=false;
		}
		j++;
	}
	totalprice = totalprice*tmp;
	var num_str = totalprice.toString();
	var result = '';
	for(var i=0; i<num_str.length; i++) {
		var tmp = num_str.length-(i+1);
		if(i%3==0 && i!=0) result = ',' + result;
		result = num_str.charAt(tmp) + result;
	}
	if(typeof(document.form1.price)=="object") { document.form1.price.value=totalprice; }
	if(typeof(document.form1.dollarprice)=="object") {
		document.form1.dollarprice.value=(Math.round(((Number(document.form1.price.value))/ardollar[1])*100)/100);
		document.all["idx_dollarprice"].innerHTML=ardollar[0]+" "+document.form1.dollarprice.value+" "+ardollar[2];
	}
	if(document.getElementById("idx_assembleprice")) { document.getElementById("idx_assembleprice").value = result; }
	if(document.getElementById("idx_price")) { document.getElementById("idx_price").innerHTML = result+"원"; }
	if(document.getElementById("idx_price_graph")) { document.getElementById("idx_price_graph").innerHTML = result+"원"; }
	<?if($_pdata->reservetype=="Y" && $_pdata->reserve>0) { ?>
		if(document.getElementById("idx_reserve")) {
			var reserveInnerValue="0";
			if(document.form1.price.value.length>0) {
				var ReservePer=<?=$_pdata->reserve?>;
				var ReservePriceValue=Number(document.form1.price.value.replace(/,/gi,""));
				if(ReservePriceValue>0) {
					reserveInnerValue = Math.round(ReservePer*ReservePriceValue*0.01)+"";
					var result = "";
					for(var i=0; i<reserveInnerValue.length; i++) {
						var tmp = reserveInnerValue.length-(i+1);
						if(i%3==0 && i!=0) result = "," + result;
						result = reserveInnerValue.charAt(tmp) + result;
					}
					reserveInnerValue = result;
				}
			}
			document.getElementById("idx_reserve").innerHTML = reserveInnerValue+"원";
		}
	<? } ?>
}
<? } ?>

function packagecal() {
<?=(count($arrpackage_pricevalue)==0?"return;\n":"")?>
	pakageprice = new Array(<? for($i=0;$i<count($arrpackage_pricevalue);$i++) { if ($i!=0) { echo ",";} echo "'".$arrpackage_pricevalue[$i]."'"; }?>);
	var result = "";
	var intgetValue = document.form1.price.value.replace(/,/g, "");
	var temppricevalue = "0";
	for(var j=1; j<pakageprice.length; j++) {
		if(document.getElementById("idx_price"+j)) {
			temppricevalue = (Number(intgetValue)+Number(pakageprice[j])).toString();
			result="";
			for(var i=0; i<temppricevalue.length; i++) { 
				var tmp = temppricevalue.length-(i+1);
				if(i%3==0 && i!=0) result = "," + result;
				result = temppricevalue.charAt(tmp) + result;
			}
			document.getElementById("idx_price"+j).innerHTML=result+"원";
		}
	}

	if(typeof(document.form1.package_idx)=="object") {
		var packagePriceValue = Number(intgetValue)+Number(pakageprice[Number(document.form1.package_idx.value)]);
	
		if(packagePriceValue>0) {
			result = "";
			packagePriceValue = packagePriceValue.toString();
			for(var i=0; i<packagePriceValue.length; i++) { 
				var tmp = packagePriceValue.length-(i+1);
				if(i%3==0 && i!=0) result = "," + result;
				result = packagePriceValue.charAt(tmp) + result;
			}
			returnValue = result;
		} else {
			returnValue = "0";
		}
		if(document.getElementById("idx_price")) {
			document.getElementById("idx_price").innerHTML=returnValue+"원";
		}
		if(document.getElementById("idx_price_graph")) {
			document.getElementById("idx_price_graph").innerHTML=returnValue+"원";
		}
		if(typeof(document.form1.dollarprice)=="object") {
			document.form1.dollarprice.value=Math.round((packagePriceValue/ardollar[1])*100)/100;
			if(document.getElementById("idx_price_graph")) {
				document.getElementById("idx_price_graph").innerHTML=ardollar[0]+" "+document.form1.dollarprice.value+" "+ardollar[2];
			}
		}
	}
}

function prdetailView(){
	var openurl = "./productdetail_view.php?prcode=<?=$productcode?>";
	window.open(openurl,"prdetailview","");
	return;
}
</script>

<? 
//include_once('footer.php'); 
?>