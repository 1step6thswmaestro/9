<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
@include_once($Dir."lib/venderlib.php");
include_once($Dir."lib/shopdata.php");

$engine_num = "shopbinder"; //엔진페이지 번호
function ReadEngine($file) {
	$filename = DirPath.DataDir."shopimages/etc/".$file;

	if(file_exists($filename)==true) {
		if($fp=@fopen($filename, "r")) {
			$szdata=fread($fp, filesize($filename));
			fclose($fp);
			$engine=unserialize($szdata);
		}
	}
	return $engine;
}
$engineval = ReadEngine("engineinfo.db");

if($engineval[$engine_num] == "checked") {
	if((int)$page==0)
		$page=1;
	
	$sql = "SELECT COUNT(*) AS allcount FROM tblproduct ";
	$sql.= "WHERE display='Y' ";
	$sql.= "AND (quantity IS NULL OR quantity > 0) ";
	$tresult=mysql_query($sql,get_db_conn());
	$rowcnt=mysql_fetch_object($tresult);
	$total = $rowcnt->allcount;
	mysql_free_result($tresult);
	
	if($total>500) {
		$list_num = 500;
		$limit_start = ($page-1)*$list_num;
		$limit_end = $list_num;
		
		$paging_count = ceil($total/$list_num);
		for($i=1; $i<=$paging_count; $i++) {
			$pagin_echo .= " <a href='http://".$shopurl."shopping/shopbinder.php?page=".$i."'>".$i."</a>";
		}
		$qry.= "LIMIT ".$limit_start.", ".$limit_end."";
	}

	$sql = "SELECT productcode, productname, sellprice, quantity, production, ";
	$sql.= "deli, deli_price, vender, tinyimage, minimage, maximage, madein, reserve, reservetype, regdate, opendate FROM tblproduct ";
	$sql.= "WHERE display='Y' ";
	$sql.= "AND (quantity IS NULL OR quantity > 0) ";
	$sql.= $qry;
	$presult=mysql_query($sql,get_db_conn());

	if($total>0)
	{
		echo "
<HTML>
<HEAD>
<TITLE>샵바인더용 상품 DB리스트</TITLE>
<META content=\"text/html; charset=ks_c_5601-1987\" http-equiv=Content-Type>
</HEAD>

<BODY>
<CENTER>
<br>
<H3><b>샵바인더용 상품 DB리스트</b></H3>

	<table border=\"1\" width=\"90%\" align=\"center\" cellspacing=\"2\" cellpadding=\"3\">
	<tr>
		<td width=\"30\">번호</td>
		<td width=\"65\">분류1</td>
		<td width=\"65\">분류2</td>
		<td width=\"45\">분류3</td>
		<td width=\"70\">분류4</td>
		<td width=\"70\">제조회사</td>
		<td width=\"100\">상품명</td>
		<td width=\"100\">상품코드</td>
		<td width=\"80\">가격</td>
		<td width=\"80\">이벤트</td>
		<td width=\"80\">이미지URL</td>
		<td width=\"80\">배송료</td>
		<td width=\"80\">할인쿠폰</td>
		<td width=\"80\">제조년월</td>
	</tr>\n";

		$sql = "SELECT codeA,codeB,codeC,codeD,code_name FROM tblproductcode ";
		$sql.= "WHERE type LIKE 'L%'";
		$result=mysql_query($sql,get_db_conn());
		while($row=mysql_fetch_object($result)) {
			$code_name[$row->codeA.$row->codeB.$row->codeC.$row->codeD] = $row->code_name;
		}
		mysql_free_result($result);

		if(getVenderUsed()==true) {
			$sql = "SELECT vender, deli_price, deli_mini FROM tblvenderinfo ";
			$result=mysql_query($sql,get_db_conn());
			while($row=mysql_fetch_object($result)) {
					$vender_deli_price[$row->vender] = $row->deli_price;
					$vender_deli_mini[$row->vender] = $row->deli_mini;
			}
			mysql_free_result($result);
		}
		
		//echo $_data->shoptitle."입니다.총(".(int)$total.")건 입니다.\n";
		//echo "<total>".(int)$total."\n<update>".date("YmdHis")."\n";
		//echo "<<<total>>>\n\t<<<총상품수>>>".(int)$total."\n\t<<<최종갱신일>>>".date("YmdHis")."\n\t<<<수정/추가상품수>>>".(int)$total."\n<<</total>>>\n\n";
		
		$i=1;
		while($row=mysql_fetch_object($presult)) {
			$codeA=substr($row->productcode,0,3); //1차 CODE
			$codeB=substr($row->productcode,3,3); //2차 CODE
			$codeC=substr($row->productcode,6,3); //3차 CODE
			$codeD=substr($row->productcode,9,3); //4차 CODE
			
			$part1="";
			$part2="";
			$part3="";
			$part4="";
			if($codeA>0) {
				$part1 	  	= preg_replace("(\\t|\\n|\\r|\^|\||%|※|☆|★|○|●|◎|◇|◆|□|■|△|▲|▽|▼|◁|◀|▷|▶|♤|♠|♡|♥|♧|♣|⊙|◈|▣|◐|◑|◐|◑|▒|▤|▥|▨|▧|▦|▩|♨|☏|☎|☜|☞|¶|†|‡|↕|↗|↙|↖|↘|♭|♩|♪|♬|㉿|＃)", "", strip_tags($code_name[$codeA."000000000"]));
				if($codeB>0) {
					$part2 	  	= preg_replace("(\\t|\\n|\\r|\^|\||%|※|☆|★|○|●|◎|◇|◆|□|■|△|▲|▽|▼|◁|◀|▷|▶|♤|♠|♡|♥|♧|♣|⊙|◈|▣|◐|◑|◐|◑|▒|▤|▥|▨|▧|▦|▩|♨|☏|☎|☜|☞|¶|†|‡|↕|↗|↙|↖|↘|♭|♩|♪|♬|㉿|＃)", "", strip_tags($code_name[$codeA.$codeB."000000"]));
					if($codeC>0) {
						$part3 	  	= preg_replace("(\\t|\\n|\\r|\^|\||%|※|☆|★|○|●|◎|◇|◆|□|■|△|▲|▽|▼|◁|◀|▷|▶|♤|♠|♡|♥|♧|♣|⊙|◈|▣|◐|◑|◐|◑|▒|▤|▥|▨|▧|▦|▩|♨|☏|☎|☜|☞|¶|†|‡|↕|↗|↙|↖|↘|♭|♩|♪|♬|㉿|＃)", "", strip_tags($code_name[$codeA.$codeB.$codeC."000"]));
						if($codeD>0) {
							$part4 	  	= preg_replace("(\\t|\\n|\\r|\^|\||%|※|☆|★|○|●|◎|◇|◆|□|■|△|▲|▽|▼|◁|◀|▷|▶|♤|♠|♡|♥|♧|♣|⊙|◈|▣|◐|◑|◐|◑|▒|▤|▥|▨|▧|▦|▩|♨|☏|☎|☜|☞|¶|†|‡|↕|↗|↙|↖|↘|♭|♩|♪|♬|㉿|＃)", "", strip_tags($code_name[$codeA.$codeB.$codeC.$codeD]));
						}
					}
				}
			}

			$itemcode 		= $row->productcode;
			$modelname		= strip_tags($row->productname);
			$modelname		= preg_replace("(\\t|\\n|\\r|\^|\||%|※|☆|★|○|●|◎|◇|◆|□|■|△|▲|▽|▼|◁|◀|▷|▶|♤|♠|♡|♥|♧|♣|⊙|◈|▣|◐|◑|◐|◑|▒|▤|▥|▨|▧|▦|▩|♨|☏|☎|☜|☞|¶|†|‡|↕|↗|↙|↖|↘|♭|♩|♪|♬|㉿|＃)", "", $modelname);
			$price			= $row->sellprice;
			$productcom		= strip_tags($row->production);
			$productcom		= preg_replace("(\\t|\\n|\\r|\^|\||%|※|☆|★|○|●|◎|◇|◆|□|■|△|▲|▽|▼|◁|◀|▷|▶|♤|♠|♡|♥|♧|♣|⊙|◈|▣|◐|◑|◐|◑|▒|▤|▥|▨|▧|▦|▩|♨|☏|☎|☜|☞|¶|†|‡|↕|↗|↙|↖|↘|♭|♩|♪|♬|㉿|＃)", "", $productcom);
			$url			= "http://".$shopurl."?productcode=".$row->productcode;
			$coupon			= "";
			$GoodsImg_Url	= "http://".$shopurl.DataDir."shopimages/product/".$row->tinyimage;
			$GoodsImg_Url1	= "http://".$shopurl.DataDir."shopimages/product/".$row->minimage;
			$GoodsImg_Url2	= "http://".$shopurl.DataDir."shopimages/product/".$row->maximage;
			$madein			= strip_tags($row->madein);
			$madein			= preg_replace("(\\t|\\n|\\r|\^|\||%|※|☆|★|○|●|◎|◇|◆|□|■|△|▲|▽|▼|◁|◀|▷|▶|♤|♠|♡|♥|♧|♣|⊙|◈|▣|◐|◑|◐|◑|▒|▤|▥|▨|▧|▦|▩|♨|☏|☎|☜|☞|¶|†|‡|↕|↗|↙|↖|↘|♭|♩|♪|♬|㉿|＃)", "", $madein);
			$reserve		= getReserveConversion($row->reserve,$row->reservetype,$row->sellprice,"Y");
			$regdate		= str_replace("-", "", substr($row->regdate,0,10));
			$opendate		= $row->opendate;
			
			$BesongBi		= "0";
			if($row->deli=="N" && $row->deli_price == "0")
			{
				if($row->vender>0) {
					if($vender_deli_price[$row->vender]>0 && $row->sellprice<$vender_deli_mini[$row->vender]) {
						$BesongBi	= $vender_deli_price[$row->vender];
					}
				} else {
					if($_data->deli_basefee>0 && $row->sellprice<$_data->deli_miniprice) {
						$BesongBi	= $_data->deli_basefee;
					}
				}
			} else if($row->deli_price>0) {
				$BesongBi	= $row->deli_price;
			}
			echo "\t<tr>
		<td width=\"30\">".$i."</td>
		<td width=\"65\">".trim($part1)."</td>
		<td width=\"65\">".trim($part2)."</td>
		<td width=\"45\">".trim($part3)."</td>
		<td width=\"70\">".trim($part4)."</td>
		<td width=\"70\">".trim($productcom)."</td>
		<td width=\"100\"><a href=\"".trim($url)."\">".trim($modelname)."</a></td>
		<td width=\"100\">".trim($itemcode)."</td>
		<td width=\"80\">".trim($price)."</td>
		<td width=\"80\"></td>
		<td width=\"80\">".trim($GoodsImg_Url2)."</td>
		<td width=\"80\">".trim($BesongBi)."</td>
		<td width=\"80\"></td>
		<td width=\"80\"></td>
	</tr>\n";
		$i++;
		}
		mysql_free_result($presult);
	echo "\t</table>
		
	<PAGES>
	<DIV>
	".$pagin_echo."
	</DIV>
	</PAGES>

</CENTER>
</BODY>
</HTML>
	";
	}
} else {
	echo "<html><head><title></title></head><body onload=\"alert('현재 페이지는 미사용 페이지 입니다.');window.close();\"></body></html>";exit;
}
?>