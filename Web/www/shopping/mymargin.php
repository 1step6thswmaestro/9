<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
@include_once($Dir."lib/venderlib.php");
include_once($Dir."lib/shopdata.php");

$engine_num = "mymargin"; //엔진페이지 번호
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
	$sql = "SELECT COUNT(*) AS allcount FROM tblproduct ";
	$sql.= "WHERE display='Y' ";
	$sql.= "AND (quantity IS NULL OR quantity > 0) ";
	$tresult=mysql_query($sql,get_db_conn());
	$rowcnt=mysql_fetch_object($tresult);
	$total = $rowcnt->allcount;
	mysql_free_result($tresult);

	$sql = "SELECT productcode, productname, sellprice, quantity, production, ";
	$sql.= "deli, deli_price, vender, tinyimage, minimage, maximage, madein, reserve, reservetype, regdate, opendate FROM tblproduct ";
	$sql.= "WHERE display='Y' ";
	$sql.= "AND (quantity IS NULL OR quantity > 0) ";
	$presult=mysql_query($sql,get_db_conn());
	
	if($total>0)
	{
		//echo "<html>\n<head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=euc-kr\">\n<meta http-equiv=\"Cache-Control\" content=\"no-cache\">\n<meta http-equiv=\"Expires\" content=\"0\">\n<meta http-equiv=\"Pragma\" content=\"no-cache\">\n<title>엔진페이지</title></head>\n<body topmargin=\"0\" leftmargin=\"0\">\n<pre>\n";
		
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
		
		echo "<p>TOTAL:".(int)$total."\n";

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
			
			$part1 	  	= trim($part1);
			if(strlen($part2)>0) {
				$part2 	  	= "/".trim($part2);
				if(strlen($part3)>0) {
					$part3 	  	= "/".trim($part3);
					if(strlen($part4)>0) {
						$part4 	  	= "/".trim($part4);
					}
				}
			}
			$part			= $part1.$part2.$part3.$part4;
			$BesongBi		= "무료";
			if($row->deli=="N" && $row->deli_price == "0") {
				if($row->vender>0) {
					if($vender_deli_price[$row->vender]>0) {
						if($vender_deli_mini[$row->vender]>0) {
							$BesongBi	= $vender_deli_mini[$row->vender]."원이상무료";
						} else {
							$BesongBi	= $vender_deli_price[$row->vender];
						}
					} else if($vender_deli_price[$row->vender]=="-9") {
						$BesongBi	= "착불";
					}
				} else {
					if($_data->deli_basefee>0) {
						if($_data->deli_miniprice>0) {
							$BesongBi	= $_data->deli_miniprice."원이상무료";
						} else {
							$BesongBi	= $_data->deli_basefee;
						}
					} else if($_data->deli_basefee == "-9") {
						$BesongBi	= "착불";
					}
				}
			} else if($row->deli_price>0) {
				$BesongBi	= $row->deli_price;
			} else if($row->deli=="G" && $row->deli_price == "0") {
				$BesongBi	= "착불";
			}
			$itemcode = str_replace(".","",str_replace("www.","",getenv("HTTP_HOST"))).trim($itemcode);
			echo "<p>".trim($itemcode)."[^]".trim($modelname)."[^]".trim($url)."[^]".trim($price)."[^]".trim($part)."[^]".($productcom?trim($productcom):"없음")."[^]".trim($GoodsImg_Url1)."[^]".trim($BesongBi)."[^][^][^][^]\n";
		}
		mysql_free_result($presult);
		//echo "</pre>\n</body>\n</html>";
	}
} else {
	echo "<html><head><title></title></head><body onload=\"alert('현재 페이지는 미사용 페이지 입니다.');window.close();\"></body></html>";exit;
}
?>