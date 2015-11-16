<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
INCLUDE ("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "pr-1";
$MenuCode = "nomenu";
if (!$_usersession->isAllowedTask($PageCode)) {
	INCLUDE ("AccessDeny.inc.php");
	exit;
}
#########################################################

//리스트 세팅
$setup[page_num] = 10;
$setup[list_num] = 10;

$sort=$_REQUEST["sort"];
$block=$_REQUEST["block"];
$gotopage=$_REQUEST["gotopage"];

if ($block != "") {
	$nowblock = $block;
	$curpage  = $block * $setup[page_num] + $gotopage;
} else {
	$nowblock = 0;
}

if (($gotopage == "") || ($gotopage == 0)) {
	$gotopage = 1;
}
////////////////////////


$mode=$_POST["mode"];
$code=$_POST["code"];

$keyword=$_POST["search"];
$searchtype="1";

if(strlen($searchtype)==0) $searchtype=0;

$cproductcodes=$_POST["cproductcodes"];

if ($mode=="copy" || $mode=="move") {
	$cproductcodes=substr($cproductcodes,0,-1);
	$cproductcode=explode("|",$cproductcodes);
	$size = sizeof($cproductcode);

	if ($size>100) {
		echo "<script>alert('한번에 100개씩만 바꾸실 수 있습니다.');history.go(-1);</script>";
		exit;
	}
	if ($size==0) {
		echo "<script>alert('카테고리 이동/복사할 상품을 선택하세요.');history.go(-1);</script>";
		exit;
	}

	$sql = "SELECT type FROM tblproductcode WHERE codeA='".substr($copycode,0,3)."' ";
	$sql.= "AND codeB='".substr($copycode,3,3)."' ";
	$sql.= "AND codeC='".substr($copycode,6,3)."' AND codeD='".substr($copycode,9,3)."' ";
	$result=mysql_query($sql,get_db_conn());
	$row=mysql_fetch_object($result);
	mysql_free_result($result);
	if(!$row || !ereg("X",$row->type)) {
		echo "<script>alert('상품카테고리 선택이 잘못되었습니다.');history.go(-1);</script>";
		exit;
	}

	$copycount=0;
	$vender_prcodelist=array();
	for ($i=0;$i<=$size;$i++) {
		if (strlen($cproductcode[$i])==18) {
			$sql = "SELECT * FROM tblproduct WHERE productcode = '".$cproductcode[$i]."'";
			$result = mysql_query($sql,get_db_conn());
			if ($row=mysql_fetch_object($result)) {
				$sql = "SELECT productcode FROM tblproduct WHERE productcode LIKE '".$copycode."%' ";
				$sql.= "ORDER BY productcode DESC LIMIT 1 ";
				$result = mysql_query($sql,get_db_conn());
				if ($rows = mysql_fetch_object($result)) {
					$newproductcode = substr($rows->productcode,12)+1;
					$newproductcode = substr("000000".$newproductcode,strlen($newproductcode));
				} else {
					$newproductcode = "000001";
				}
				mysql_free_result($result);

				$path = $Dir.DataDir."shopimages/product/";
				if (strlen($row->maximage)>0) {
					$maximage=$copycode.$newproductcode.".".strtolower(substr($row->maximage,strlen($row->maximage)-3,3));
					if (file_exists("$path$row->maximage")==true) {
						if ($mode=="move") rename("$path$row->maximage","$path$maximage");
						else copy("$path$row->maximage","$path$maximage");
					}
				} else $maximage="";
				if (strlen($row->minimage)>0) {
					$minimage=$copycode.$newproductcode."2.".strtolower(substr($row->minimage,strlen($row->minimage)-3,3));
					if (file_exists("$path$row->minimage")==true) {
						if ($mode=="move") rename("$path$row->minimage","$path$minimage");
						else copy("$path$row->minimage","$path$minimage");
					}
				} else $minimage="";
				if (strlen($row->tinyimage)>0) {
					$tinyimage=$copycode.$newproductcode."3.".strtolower(substr($row->tinyimage,strlen($row->tinyimage)-3,3));
					if (file_exists("$path$row->tinyimage")==true) {
						if ($mode=="move") rename("$path$row->tinyimage","$path$tinyimage");
						else copy("$path$row->tinyimage","$path$tinyimage");
					}
				} else $tinyimage="";
				if (strlen($row->quantity)==0) $quantity="NULL";
				else $quantity=$row->quantity;

				$productname = mysql_escape_string($row->productname);
				$production = mysql_escape_string($row->production);
				$madein = mysql_escape_string($row->madein);
				$model = mysql_escape_string($row->model);
				$tempkeyword = mysql_escape_string($row->keyword);
				$addcode = mysql_escape_string($row->addcode);
				$userspec = mysql_escape_string($row->userspec);
				$option1 = mysql_escape_string($row->option1);
				$option2 = mysql_escape_string($row->option2);
				$content = mysql_escape_string($row->content);
				$selfcode = mysql_escape_string($row->selfcode);
				$assembleproduct = mysql_escape_string($row->assembleproduct);

				$sql = "INSERT tblproduct SET ";
				$sql.= "productcode		= '".$copycode.$newproductcode."', ";
				$sql.= "productname		= '".$productname."', ";
				$sql.= "assembleuse		= '".$row->assembleuse."', ";
				$sql.= "assembleproduct	= '".$row->assembleproduct."', ";
				$sql.= "sellprice		= ".$row->sellprice.", ";
				$sql.= "consumerprice	= ".$row->consumerprice.", ";
				$sql.= "buyprice		= ".$row->buyprice.", ";
				$sql.= "reserve			= '".$row->reserve."', ";
				$sql.= "reservetype		= '".$row->reservetype."', ";
				$sql.= "production		= '".$production."', ";
				$sql.= "madein			= '".$madein."', ";
				$sql.= "model			= '".$model."', ";
				$sql.= "brand			= '".$row->brand."', ";
				$sql.= "opendate		= '".$row->opendate."', ";
				$sql.= "selfcode		= '".$row->selfcode."', ";
				$sql.= "bisinesscode	= '".$row->bisinesscode."', ";
				$sql.= "quantity		= ".$quantity.", ";
				$sql.= "group_check		= '".$row->group_check."', ";
				$sql.= "keyword			= '".$tempkeyword."', ";
				$sql.= "addcode			= '".$addcode."', ";
				$sql.= "userspec		= '".$userspec."', ";
				$sql.= "maximage		= '".$maximage."', ";
				$sql.= "minimage		= '".$minimage."', ";
				$sql.= "tinyimage		= '".$tinyimage."', ";
				$sql.= "option_price	= '".$row->option_price."', ";
				$sql.= "option_quantity	= '".$row->option_quantity."', ";
				$sql.= "option1			= '".$option1."', ";
				$sql.= "option2			= '".$option2."', ";
				$sql.= "etctype			= '".$row->etctype."', ";
				$sql.= "deli			= '".$row->deli."', ";
				$sql.= "package_num		= '".(int)$row->package_num."', ";
				$sql.= "display			= '".$row->display."', ";
				if ($newtime=="Y")
					$sql.= "date		= '".date("YmdHis")."', ";
				else
					$sql.= "date		= '".$row->date."', ";
				$sql.= "vender			= '".$row->vender."', ";
				$sql.= "regdate			= now(), ";
				$sql.= "modifydate		= now(), ";
				$sql.= "content			= '".$content."' ";
				$insert = mysql_query($sql,get_db_conn());
				$insert_pridx = mysql_insert_id();
				$fromproductcodes.="|".$cproductcode[$i];
				$copyproductcodes.="|".$copycode.$newproductcode;

				if($row->vender>0) {
					$vender_prcodelist[$row->vender]["IN"][]=$copycode.$newproductcode;
				}

				if ($mode=="move") {
					if($row->vender>0) {
						$vender_prcodelist[$row->vender]["OUT"][]=$row->productcode;
					}

					$sql = "DELETE FROM tblproduct WHERE productcode = '".$cproductcode[$i]."' ";
					mysql_query($sql,get_db_conn());

					#태그관련 지우기
					$sql = "DELETE FROM tbltagproduct WHERE productcode = '".$cproductcode[$i]."'";
					mysql_query($sql,get_db_conn());

					$sql = "UPDATE tblproductgroupcode SET productcode = '".$copycode.$newproductcode."' ";
					$sql.= "WHERE productcode='".$cproductcode[$i]."'";
					mysql_query($sql,get_db_conn());

					$sql = "UPDATE tblproductreview SET productcode = '".$copycode.$newproductcode."' ";
					$sql.= "WHERE productcode='".$cproductcode[$i]."'";
					mysql_query($sql,get_db_conn());

					$sql = "UPDATE tblproducttheme SET productcode = '".$copycode.$newproductcode."' ";
					$sql.= "WHERE productcode='".$cproductcode[$i]."'";
					mysql_query($sql,get_db_conn());

					$sql = "UPDATE tblcollection SET productcode = '".$copycode.$newproductcode."' ";
					$sql.= "WHERE productcode='".$cproductcode[$i]."'";
					mysql_query($sql,get_db_conn());

					$sql = "UPDATE tblwishlist SET productcode = '".$copycode.$newproductcode."' ";
					$sql.= "WHERE productcode='".$cproductcode[$i]."'";
					mysql_query($sql,get_db_conn());

					$sql = "UPDATE tblcollection SET ";
					$sql.= "collection_list = replace(collection_list,'".$cproductcode[$i]."','".$copycode.$newproductcode."') ";
					mysql_query($sql,get_db_conn());

					$sql = "UPDATE tblspecialcode SET ";
					$sql.= "special_list = replace(special_list,'".$cproductcode[$i]."','".$copycode.$newproductcode."') ";
					mysql_query($sql,get_db_conn());

					$sql = "UPDATE tblspecialmain SET ";
					$sql.= "special_list = replace(special_list,'".$cproductcode[$i]."','".$copycode.$newproductcode."') ";
					mysql_query($sql,get_db_conn());

					if($row->assembleuse=="Y") { //코디/조립 상품일 경우
						$sql = "UPDATE tblassembleproduct SET productcode = '".$copycode.$newproductcode."' ";
						$sql.= "WHERE productcode='".$cproductcode[$i]."'";
						mysql_query($sql,get_db_conn());

						$sql = "SELECT assemble_pridx FROM tblassembleproduct ";
						$sql.= "WHERE productcode = '".$copycode.$newproductcode."' ";
						$result = mysql_query($sql,get_db_conn());
						if($row = @mysql_fetch_object($result)) {
							if(strlen(str_replace("","",$row->assemble_pridx))>0) {
								$sql = "UPDATE tblproduct SET ";
								$sql.= "assembleproduct = REPLACE(assembleproduct,',".$cproductcode[$i]."',',".$copycode.$newproductcode."') ";
								$sql.= "WHERE pridx IN ('".str_replace("","','",$row->assemble_pridx)."') ";
								$sql.= "AND assembleuse != 'Y' ";
								mysql_query($sql,get_db_conn());
							}
						}
						mysql_free_result($result);
					} else {
						$sql = "UPDATE tblassembleproduct SET ";
						$sql.= "assemble_pridx=REPLACE(assemble_pridx,'".$row->pridx."','".$insert_pridx."'), ";
						$sql.= "assemble_list=REPLACE(assemble_list,',".$row->pridx."',',".$insert_pridx."') ";
						mysql_query($sql,get_db_conn());
					}

					$log_content = "## 상품이동입력 ## - 상품코드 ".$cproductcode[$i]." => ".$copycode.$newproductcode." - 상품명 : ".$productname;
					ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);
				} else {
					if($row->group_check=="Y") {
						$sql = "INSERT INTO tblproductgroupcode SELECT '".$copycode.$newproductcode."', group_code FROM tblproductgroupcode WHERE productcode = '".$cproductcode[$i]."' ";
						mysql_query($sql,get_db_conn());
					}
					if($row->assembleuse=="Y") { //코디/조립 상품일 경우
						$sql = "INSERT INTO tblassembleproduct ";
						$sql.= "SELECT '".$copycode.$newproductcode."', assemble_type, assemble_title, assemble_pridx, assemble_list FROM tblassembleproduct ";
						$sql.= "WHERE productcode='".$cproductcode[$i]."' ";
						mysql_query($sql,get_db_conn());

						$sql = "SELECT assemble_pridx FROM tblassembleproduct ";
						$sql.= "WHERE productcode = '".$cproductcode[$i]."' ";
						$result = mysql_query($sql,get_db_conn());
						if($row = @mysql_fetch_object($result)) {
							if(strlen(str_replace("","",$row->assemble_pridx))>0) {
								$sql = "UPDATE tblproduct SET ";
								$sql.= "assembleproduct = CONCAT(assembleproduct,',".$copycode.$newproductcode."') ";
								$sql.= "WHERE pridx IN ('".str_replace("","','",$row->assemble_pridx)."') ";
								$sql.= "AND assembleuse != 'Y' ";
								mysql_query($sql,get_db_conn());
							}
						}
						mysql_free_result($result);
					} else {
						$sql = "UPDATE tblproduct SET assembleproduct = '' ";
						$sql.= "WHERE productcode='".$copycode.$newproductcode."'";
						mysql_query($sql,get_db_conn());
					}

					$log_content = "## 상품복사입력 ## - 상품코드 ".$cproductcode[$i]." => ".$copycode.$newproductcode." - 상품명 : ".$productname;
					ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);
				}
				$copycount++;
			}
		}
	}
	if ($copycount!=0) {
		//입점업체 상품 관련 처리
		if(count($vender_prcodelist)>0) {
			$tmpvender=$vender_prcodelist;
			while(list($vender,$prarr)=each($tmpvender)) {
				unset($tmpcodeA);
				for($kk=0;$kk<count($prarr["IN"]);$kk++) {
					//insert 처리
					setVenderDesignInsert($vender, $prarr["IN"][$kk]);

					if(strlen($prarr["OUT"][$kk])==18) {
						//move 처리
						$tmpcodeA[substr($prarr["OUT"][$kk],0,3)]=true;
						setVenderThemeSpecialUpdate($vender, $prarr["IN"][$kk], $prarr["OUT"][$kk]);
					}
				}
				//미니샵 상품수 업데이트 (진열된 상품만)
				$sql="SELECT COUNT(*) as prdt_allcnt,COUNT(IF(display='Y',1,NULL)) as prdt_cnt FROM tblproduct ";
				$sql.="WHERE vender='".$vender."' ";
				$result=mysql_query($sql,get_db_conn());
				$row=mysql_fetch_object($result);
				$prdt_allcnt=(int)$row->prdt_allcnt;
				$prdt_cnt=(int)$row->prdt_cnt;
				mysql_free_result($result);

				setVenderCountUpdate($prdt_allcnt, $prdt_cnt, $vender);

				if(count($tmpcodeA)>0) {
					$sql = "SELECT SUBSTRING(productcode,1,3) as codeA FROM tblproduct ";
					$sql.= "WHERE ( ";
					$arr_codeA=$tmpcodeA;
					$i=0;
					while(list($key,$val)=each($arr_codeA)) {
						if(strlen($key)==3) {
							if($i>0) $sql.= "OR ";
							$sql.= "productcode LIKE '".$key."%' ";
							$i++;
						}
					}
					$sql.= ") ";
					$sql.= "AND vender='".$vender."' ";
					$sql.= "GROUP BY codeA ";
					$result=mysql_query($sql,get_db_conn());
					while($row=mysql_fetch_object($result)) {
						unset($tmpcodeA[$row->codeA]);
					}
					mysql_free_result($result);

					if(count($tmpcodeA)>0) {
						$str_codeA="";
						while(list($key,$val)=each($tmpcodeA)) {
							$str_codeA.=$key.",";

							$imagename = $Dir.DataDir."shopimages/vender/".$vender."_CODE10_".$key.".gif";
							@unlink($imagename);
						}
						$str_codeA=substr($str_codeA,0,-1);
						$str_codeA=ereg_replace(',','\',\'',$str_codeA);
						setVenderDesignDelete($str_codeA, $vender);
					}
				}
			}
		}

		delProductMultiImg($mode,substr($fromproductcodes,1),substr($copyproductcodes,1));

		if ($mode=="move") $onload="<script>alert('$copycount 건의 데이터가 [".ereg_replace("\"","",$copycode_name)."]으로 이동되었습니다.');</script>";
		else $onload="<script>alert('".$copycount." 건의 데이터가 [".ereg_replace("\"","",$copycode_name)."]으로 복사되었습니다.');</script>";
	}

} else if ($mode=="delete") {
	$cproductcodes=substr($cproductcodes,0,-1);
	$allprcode=$cproductcodes;

	$cproductcode=explode("|",$cproductcodes);
	$size = sizeof($cproductcode);

	if ($size>100) {
		echo "<script>alert('한번에 100개씩만 삭제하실 수 있습니다.');history.go(-1);</script>";
		exit;
	}
	if ($size==0) {
		echo "<script>alert('삭제할 상품을 선택하세요.');history.go(-1);</script>";
		exit;
	}

	$prcodelist = ereg_replace("\|","','",$allprcode);
	$arrvender=array();
	$arrvenderid=array();
	$arrpridx=array();
	$arrassembleuse=array();
	$arrassembleproduct=array();
	$arrproductcode=array();
	$sql = "SELECT vender,pridx,assembleuse,assembleproduct,productcode FROM tblproduct ";
	$sql.= "WHERE productcode IN ('".$prcodelist."') ";
	$p_result=mysql_query($sql,get_db_conn());
	while($p_row=mysql_fetch_object($p_result)) {
		if($p_row->vender>0 && strlen($arrvenderid[$p_row->vender])==0) {
			$arrvender[]=$p_row->vender;
			$arrvenderid[$p_row->vender]=$p_row->vender;
		}
		$arrpridx[]=$p_row->pridx;
		$arrassembleuse[]=$p_row->assembleuse;
		$arrassembleproduct[]=$p_row->assembleproduct;
		$arrproductcode[]=$p_row->productcode;
	}
	mysql_free_result($p_result);

	$sql = "DELETE FROM tblproduct WHERE productcode IN ('".$prcodelist."')";
	mysql_query($sql,get_db_conn());
	
	$sql = "DELETE FROM tblproductgroupcode WHERE productcode IN ('".$prcodelist."')";
	$result = mysql_query($sql,get_db_conn());

	$sql = "DELETE FROM tblproducttheme WHERE productcode IN ('".$prcodelist."')";
	$result = mysql_query($sql,get_db_conn());

	$sql = "DELETE FROM tblproductreview WHERE productcode IN ('".$prcodelist."')";
	mysql_query($sql,get_db_conn());

	#태그관련 지우기
	$sql = "DELETE FROM tbltagproduct WHERE productcode IN ('".$prcodelist."')";
	mysql_query($sql,get_db_conn());

	$sql = "DELETE FROM tblwishlist WHERE productcode IN ('".$prcodelist."')";
	mysql_query($sql,get_db_conn());

	$sql = "DELETE FROM tblcollection WHERE productcode IN ('".$prcodelist."')";
	mysql_query($sql,get_db_conn());

	for($vz=0; $vz<count($arrpridx); $vz++) { // 코디/조립 기본구성상품의 가격 처리		
		if($arrassembleuse[$vz]=="Y") {
			$sql = "SELECT assemble_pridx FROM tblassembleproduct ";
			$sql.= "WHERE productcode = '".$arrproductcode[$vz]."' ";
			$result = mysql_query($sql,get_db_conn());
			if($row = @mysql_fetch_object($result)) {
				$sql = "DELETE FROM tblassembleproduct WHERE productcode = '".$arrproductcode[$vz]."' ";
				mysql_query($sql,get_db_conn());
				
				if(strlen(str_replace("","",$row->assemble_pridx))>0) {
					$sql = "UPDATE tblproduct SET ";
					$sql.= "assembleproduct = REPLACE(assembleproduct,',".$arrproductcode[$vz]."','') ";
					$sql.= "WHERE pridx IN ('".str_replace("","','",$row->assemble_pridx)."') ";
					$sql.= "AND assembleuse != 'Y' ";
					mysql_query($sql,get_db_conn());
				}
			}
			mysql_free_result($result);
		} else {
			if(strlen($arrassembleproduct[$vz])>0) {
				$sql = "SELECT productcode, assemble_pridx FROM tblassembleproduct ";
				$sql.= "WHERE productcode IN ('".str_replace(",","','",$arrassembleproduct[$vz])."') ";
				$result = mysql_query($sql,get_db_conn());
				while($row = @mysql_fetch_object($result)) {
					$sql = "SELECT SUM(sellprice) as sumprice FROM tblproduct ";
					$sql.= "WHERE pridx IN ('".str_replace("","','",$row->assemble_pridx)."') ";
					$sql.= "AND display ='Y' ";
					$sql.= "AND assembleuse!='Y' ";
					$result2 = mysql_query($sql,get_db_conn());
					if($row2 = @mysql_fetch_object($result2)) {
						$sql = "UPDATE tblproduct SET sellprice='".$row2->sumprice."' ";
						$sql.= "WHERE productcode = '".$row->productcode."' ";
						$sql.= "AND assembleuse='Y' ";
						mysql_query($sql,get_db_conn());
					}
					mysql_free_result($result2);
				}
			}

			$sql = "UPDATE tblassembleproduct SET ";
			$sql.= "assemble_pridx=REPLACE(assemble_pridx,'".$arrpridx[$vz]."',''), ";
			$sql.= "assemble_list=REPLACE(assemble_list,',".$arrpridx[$vz]."','') ";
			mysql_query($sql,get_db_conn());
		}
	}

	for($yy=0;$yy<count($arrvender);$yy++) {
		//미니샵 테마코드에 등록된 상품 삭제
		setVenderThemeDelete($prcodelist, $arrvender[$yy]);

		//미니샵 상품수 업데이트 (진열된 상품만)
		$sql = "SELECT COUNT(*) as prdt_allcnt, COUNT(IF(display='Y',1,NULL)) as prdt_cnt FROM tblproduct ";
		$sql.= "WHERE vender='".$arrvender[$yy]."' ";
		$result=mysql_query($sql,get_db_conn());
		$row=mysql_fetch_object($result);
		$prdt_allcnt=(int)$row->prdt_allcnt;
		$prdt_cnt=(int)$row->prdt_cnt;
		mysql_free_result($result);

		setVenderCountUpdate($prdt_allcnt, $prdt_cnt, $arrvender[$yy]);

		//tblvendercodedesign => 해당 대분류 상품 확인 후 없으면 대분류 화면 삭제
		$tmpcodeA=array();
		$arrprcode=explode("|",$allprcode);
		for($j=0;$j<count($arrprcode);$j++) {
			$tmpcodeA[substr($arrprcode[$j],0,3)]=true;
		}

		if(count($tmpcodeA)>0) {
			$sql = "SELECT SUBSTRING(productcode,1,3) as codeA FROM tblproduct ";
			$sql.= "WHERE ( ";
			$arr_codeA=$tmpcodeA;
			$i=0;
			while(list($key,$val)=each($arr_codeA)) {
				if(strlen($key)==3) {
					if($i>0) $sql.= "OR ";
					$sql.= "productcode LIKE '".$key."%' ";
					$i++;
				}
			}
			$sql.= ") ";
			$sql.= "AND vender='".$arrvender[$yy]."' ";
			$sql.= "GROUP BY codeA ";
			$result=mysql_query($sql,get_db_conn());
			while($row=mysql_fetch_object($result)) {
				unset($tmpcodeA[$row->codeA]);
			}
			mysql_free_result($result);

			if(count($tmpcodeA)>0) {
				$str_codeA="";
				while(list($key,$val)=each($tmpcodeA)) {
					$str_codeA.=$key.",";

					$imagename = $Dir.DataDir."shopimages/vender/".$arrvender[$yy]."_CODE10_".$key.".gif";
					@unlink($imagename);
				}
				$str_codeA=substr($str_codeA,0,-1);
				$str_codeA=ereg_replace(',','\',\'',$str_codeA);
				setVenderDesignDelete($str_codeA, $arrvender[$yy]);
			}
		}
	}

	$prcode = explode("|",$allprcode);
	$cnt = count($prcode);

	$log_content = "## 등록상품 이동/복사/삭제에서 ".$cnt."건의 상품삭제 ##";
	ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);

	for($i=0;$i<$cnt;$i++){
		$delshopimage=$Dir.DataDir."shopimages/product/".$prcode[$i]."*";
		proc_matchfiledel($delshopimage);
		delProductMultiImg("prdelete","",$prcode[$i]);
	}

	$onload="<script>alert(\"".$cnt."건의 상품이 정상적으로 삭제되었습니다.\");</script>";
}

$sql = "SELECT vendercnt FROM tblshopcount ";
$result=mysql_query($sql,get_db_conn());
$row=mysql_fetch_object($result);
$vendercnt=$row->vendercnt;
mysql_free_result($result);

if($vendercnt>0){
	$venderlist=array();
	$sql = "SELECT vender,id,com_name,delflag FROM tblvenderinfo ORDER BY id ASC ";
	$result=mysql_query($sql,get_db_conn());
	while($row=mysql_fetch_object($result)) {
		$venderlist[$row->vender]=$row;
	}
	mysql_free_result($result);
}

$imagepath=$Dir.DataDir."shopimages/product/";
?>

<? INCLUDE "header.php"; ?>
<style>td {line-height:18pt;}</style>
<script type="text/javascript" src="lib.js.php"></script>
<script>var LH = new LH_create();</script>
<script for=window event=onload>LH.exec();</script>
<script>LH.add("parent_resizeIframe('ListFrame')");</script>

<script language="JavaScript">
function CheckKeyPress(){
	ekey=event.keyCode;
	if (ekey==13) {
		CheckSearch();
	}
}

function CheckSearch() {
	document.form1.mode.value = "";
	document.form1.code.value = "";
	if (document.form1.search.value.length<2) {
		if(document.form1.search.value.length==0) alert("검색어를 입력하세요.");
		else alert("검색어는 2글자 이상 입력하셔야 합니다."); 
		document.form1.search.focus();
		return;
	} else {
		document.form1.target="";
		document.form1.action="<?=$_SERVER[PHP_SELF]?>";
		document.form1.submit();
	}
}


<?if($vendercnt>0){?>
function viewVenderInfo(vender) {
	window.open("about:blank","vender_infopop","width=100,height=100,scrollbars=yes");
	document.vForm.vender.value=vender;
	document.vForm.target="vender_infopop";
	document.vForm.submit();
}
<?}?>

function ProductMouseOver(Obj) {
	obj = event.srcElement;
	WinObj=document.getElementById(Obj);
	obj._tid = setTimeout("ProductViewImage(WinObj)",200);
}
function ProductViewImage(WinObj) {
	WinObj.style.display = "";
	
	if(!WinObj.height)
		WinObj.height = WinObj.offsetTop;

	WinObjPY = WinObj.offsetParent.offsetHeight;
	WinObjST = WinObj.height-WinObj.offsetParent.scrollTop;
	WinObjSY = WinObjST+WinObj.offsetHeight;

	if(WinObjPY < WinObjSY)
		WinObj.style.top = WinObj.offsetParent.scrollTop-WinObj.offsetHeight+WinObjPY;
	else if(WinObjST < 0)
		WinObj.style.top = WinObj.offsetParent.scrollTop;
	else
		WinObj.style.top = WinObj.height;
}
function ProductMouseOut(Obj) {
	obj = event.srcElement;
	WinObj = document.getElementById(Obj);
	WinObj.style.display = "none";
	clearTimeout(obj._tid);
}

function GoPage(block,gotopage,sort) {
	document.form1.mode.value = "";
	document.form1.sort.value = sort;
	document.form1.block.value = block;
	document.form1.gotopage.value = gotopage;
	document.form1.submit();
}

function GoSort(sort) {
	document.form1.mode.value = "";
	document.form1.sort.value = sort;
	document.form1.block.value = "";
	document.form1.gotopage.value = "";
	document.form1.submit();
}

function CheckAll(cnt){
	checkvalue=document.form1.allcheck.checked;
	for(i=1;i<=cnt;i++){
		document.form1.cproductcode[i].checked=checkvalue;
		checkActive(document.form1.cproductcode[i],document.form1.cproductcode[i].value);
	}
}

function CopyCodeSelect() {
	window.open("product_copycodeselect.php","","height=300,width=420,scrollbars=no,resizable=no");
}

function Copy(gbn) {
	var gbn_name = "복사";
	if (gbn=="move") gbn_name = "이동";
	if (document.form1.copycode.value.length==0) {
		alert(gbn_name+"할 카테고리를 선택하세요.");
		CopyCodeSelect();
		return;
	}
	if (document.form1.copycode.value==document.form1.oldcode.value) {
		alert(gbn_name+"할 카테고리가 이전카테고리와 같습니다.");
		CopyCodeSelect();
		return;
	}
	if (confirm("선택된 카테고리를 "+gbn_name+"하시겠습니까?")) {
		var checkvalue=false;
		for(i=1;i<document.form1.cproductcode.length;i++){
			if(document.form1.cproductcode[i].checked==true){
				checkvalue=true;
				document.form1.cproductcodes.value+=document.form1.cproductcode[i].value+"|";
			}
		}

		if(checkvalue!=true){
			alert(gbn_name+"할 상품을 선택하세요");
			return;
		}
		document.form1.mode.value=gbn;
		document.form1.block.value="";
		document.form1.gotopage.value="";
		document.form1.submit();
	}
}

function Delete() {
	if (confirm("선택한 상품 삭제시 복구가 불가능합니다.\n\n선택된 상품을 삭제하시겠습니까?")) {
		var checkvalue=false;
		for(i=1;i<document.form1.cproductcode.length;i++){
			if(document.form1.cproductcode[i].checked==true){
				checkvalue=true;
				document.form1.cproductcodes.value+=document.form1.cproductcode[i].value+"|";
			}
		}
		if(checkvalue!=true){
			alert('삭제할 상품이 선택되지 않았습니다.');
			return;
		}
		document.form1.mode.value="delete";
		document.form1.block.value="";
		document.form1.gotopage.value="";
		document.form1.submit();
	}
}

function checkActive(checkObj,checkId)
{
	if(document.getElementById("pidx_"+checkId))
	{
		if(checkObj.checked)
			document.getElementById("pidx_"+checkId).style.backgroundColor = "#EFEFEF";
		else
			document.getElementById("pidx_"+checkId).style.backgroundColor = "#FFFFFF";
	}
}

function ProductInfo(prcode) {
	code=prcode.substring(0,12);
	popup="YES";
	document.form_reg.code.value=code;
	document.form_reg.prcode.value=prcode;
	document.form_reg.popup.value=popup;
	if (popup=="YES") {
		document.form_reg.action="product_register.add.php";
		document.form_reg.target="register";
		window.open("about:blank","register","width=820,height=700,scrollbars=yes,status=no");
	} else {
		document.form_reg.action="product_register.php";
		document.form_reg.target="";
	}
	document.form_reg.submit();
}
/*
function DivDefaultReset()
{
	if(!self.id)
	{
		self.id = self.name;
		parent.document.getElementById(self.id).style.height = parent.document.getElementById(self.id).height;
	}
}
DivDefaultReset();*/
</script>

<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%" style="table-layout:fixed">
<tr>
	<td width="100%" bgcolor="#FFFFFF"><img src="images/icon_point2.gif" width="8" height="11" border="0"><b>조회할 상품명 입력</b></td>
</tr>
<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post>
<input type=hidden name=mode>
<input type=hidden name=code value="<?=$code?>">
<input type=hidden name=cproductcodes>
<input type=hidden name=block value="<?=$block?>">
<input type=hidden name=gotopage value="<?=$gotopage?>">
<input type=hidden name=searchtype value="1">
<input type=hidden name=sort value="<?=$sort?>">
<tr>
	<TD class="">
		<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td width="99%"><input type=text name=search size=50 value="<?=$keyword?>" onKeyDown="CheckKeyPress()" class="input" style=width:100%></td>
				<td width="1%"><p align="right"><a href="javascript:CheckSearch();"><img src="images/btn_search2.gif" width="50" height="25" border="0" align=absmiddle hspace="2"></a></td>
			</tr>
		</table>
	</TD>
</tr>

<tr>
	<td width="100%" bgcolor="#FFFFFF"><IMG SRC="images/product_mainlist_text.gif" border="0"></td>
</tr>
<tr>
	<td width="100%" height="100%" valign="top" style="BORDER:#FF8730 2px solid;padding-left:5px;padding-right:5px;">
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td width="100%" style="padding-top:2pt; padding-bottom:2pt;" height="30"><B><span class="font_orange">* 정렬방법 :</span></B> <A HREF="javascript:GoSort('date');">진열순</a> | <A HREF="javascript:GoSort('productname');">상품명순</a> | <A HREF="javascript:GoSort('price');">가격순</a></td>
	</tr>
	<tr>
		<td width="100%" valign="top">
		<DIV style="width:100%;height:100%;overflow:hidden;">
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td width="100%">
			<TABLE border="0" cellSpacing="0" cellPadding="0" width="100%" style="table-layout:fixed">
<?
			$colspan=7;
			if($vendercnt>0) $colspan++;
?>
			<col width=45></col>
			<?if($vendercnt>0){?>
			<col width=70></col>
			<?}?>
			<col width=50></col>
			<col width=></col>
			<col width=70></col>
			<col width=45></col>
			<col width=45></col>
			<col width=45></col>
			<TR>
				<TD colspan="<?=$colspan?>" background="images/table_top_line.gif"></TD>
			</TR>
			<TR align="center">
				<TD class="table_cell">선택</TD>
				<?if($vendercnt>0){?>
				<TD class="table_cell1">입점업체</TD>
				<?}?>
				<TD class="table_cell1" colspan="2">상품명/진열코드/특이사항</TD>
				<TD class="table_cell1">판매가격</TD>
				<TD class="table_cell1">수량</TD>
				<TD class="table_cell1">상태</TD>
				<TD class="table_cell1">수정</TD>
			</TR>
			<input type=hidden name=cproductcode>
<?
			if (($searchtype=="0" && strlen($code)==12) || ($searchtype=="1" && strlen($keyword)>2)) {
				$page_numberic_type = 1;
				if ($searchtype=="0" && strlen($code)==12) {
					$qry = "AND productcode LIKE '".$code."%' ";
				} else {
					$qry = "AND productname LIKE '%".$keyword."%' ";
				}
				$sql0 = "SELECT COUNT(*) as t_count FROM tblproduct WHERE 1=1 ";
				$sql0.= $qry;
				$result = mysql_query($sql0,get_db_conn());
				$row = mysql_fetch_object($result);
				mysql_free_result($result);
				$t_count = $row->t_count;
				$pagecount = (($t_count - 1) / $setup[list_num]) + 1;

				$sql = "SELECT option_price, productcode,productname,production,sellprice,consumerprice, ";
				$sql.= "buyprice,quantity,reserve,reservetype,addcode,display,vender,tinyimage,selfcode,assembleuse ";
				$sql.= "FROM tblproduct WHERE 1=1 ";
				$sql.= $qry." ";
				if ($sort=="price")				$sql.= "ORDER BY sellprice ";
				else if ($sort=="productname")	$sql.= "ORDER BY productname ";
				else							$sql.= "ORDER BY date DESC ";
				$sql.= " LIMIT " . ($setup[list_num] * ($gotopage - 1)) . ", " . $setup[list_num];
				$result = mysql_query($sql,get_db_conn());
				$cnt=0;
				while($row=mysql_fetch_object($result)) {
					echo "<tr>\n";
					echo "	<TD colspan=\"".$colspan."\" background=\"images/table_con_line.gif\"></TD>\n";
					echo "</tr>\n";
					echo "<tr align=\"center\" id=\"pidx_".$row->productcode."\">\n";
					echo "	<TD class=\"td_con2\"><input type=checkbox name=cproductcode value=\"".$row->productcode."\" onclick=\"checkActive(this,'".$row->productcode."')\"></td>\n";
					if($vendercnt>0) {
						echo "	<TD class=\"td_con1\"><B>".(strlen($venderlist[$row->vender]->vender)>0?"<a href=\"javascript:viewVenderInfo(".$row->vender.")\">".$venderlist[$row->vender]->id."</a>":"-")."</B></td>\n";
					}
					echo "<TD class=\"td_con1\">";
					if (strlen($row->tinyimage)>0 && file_exists($imagepath.$row->tinyimage)==true){
						echo "<img src='".$imagepath.$row->tinyimage."' height=40 width=40 border=1 onMouseOver=\"ProductMouseOver('primage".$cnt."')\" onMouseOut=\"ProductMouseOut('primage".$cnt."');\">";
					} else {
						echo "<img src=images/space01.gif onMouseOver=\"ProductMouseOver('primage".$cnt."')\" onMouseOut=\"ProductMouseOut('primage".$cnt."');\">";
					}
					echo "<div id=\"primage".$cnt."\" style=\"position:absolute; z-index:100; display:none;\"><table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"170\">\n";
					echo "		<tr bgcolor=\"#FFFFFF\">\n";
					if (strlen($row->tinyimage)>0 && file_exists($imagepath.$row->tinyimage)==true){
						echo "		<td align=\"center\" width=\"100%\" height=\"150\" style=\"border:#000000 solid 1px;\"><img src=\"".$imagepath.$row->tinyimage."\" border=\"0\"></td>\n";
					} else {
						echo "		<td align=\"center\" width=\"100%\" height=\"150\" style=\"border:#000000 solid 1px;\"><img src=\"".$Dir."images/product_noimg.gif\" border=\"0\"></td>\n";
					}
					echo "		</tr>\n";
					echo "		</table>\n";
					echo "		</div>\n";
					echo "	</td>\n";
					echo "	<TD class=\"td_con1\" align=\"left\" style=\"word-break:break-all;\"><img src=\"images/producttype".($row->assembleuse=="Y"?"y":"n").".gif\" border=\"0\" align=\"absmiddle\" hspace=\"2\">".$row->productname.($row->selfcode?"-".$row->selfcode:"").($row->addcode?"-".$row->addcode:"")."&nbsp;</td>\n";
					echo "	<TD align=right class=\"td_con1\"><img src=\"images/won_icon.gif\" border=\"0\" style=\"margin-right:2px;\"><span class=\"font_orange\">".number_format($row->sellprice)."</span><br><img src=\"images/reserve_icon.gif\" border=\"0\" style=\"margin-right:2px;\">".($row->reservetype!="Y"?number_format($row->reserve):$row->reserve."%")."</TD>\n";
					echo "	<TD class=\"td_con1\">";
					if (strlen($row->quantity)==0) echo "무제한";
					else if ($row->quantity<=0) echo "<span class=\"font_orange\"><b>품절</b></span>";
					else echo $row->quantity;
					echo "	</TD>\n";
					echo "	<TD class=\"td_con1\">".($row->display=="Y"?"<font color=\"#0000FF\">판매중</font>":"<font color=\"#FF4C00\">보류중</font>")."</td>";
					echo "	<TD class=\"td_con1\"><a href=\"javascript:ProductInfo('".$row->productcode."');\"><img src=\"images/icon_newwin1.gif\" border=\"0\"></a></td>\n";
					echo "</tr>\n";
					$cnt++;
				}
				mysql_free_result($result);
				if ($cnt==0) {
					$page_numberic_type="";
					echo "<tr><TD colspan=\"".$colspan."\" background=\"images/table_con_line.gif\"></TD></tr><tr><td class=\"td_con2\" colspan=\"".$colspan."\" align=\"center\">검색된 상품이 존재하지 않습니다.</td></tr>";
				}
			} else {
				$page_numberic_type="";
				echo "<tr><TD colspan=\"".$colspan."\" background=\"images/table_con_line.gif\"></TD></tr><tr><td class=\"td_con2\" colspan=\"".$colspan."\" align=\"center\">상품카테고리를 선택하거나 검색을 하세요.</td></tr>";
			}
?>
			<TR>
				<TD height="1" colspan="<?=$colspan?>" background="images/table_top_line.gif"></TD>
			</TR>
			</TABLE>
			</td>
		</tr>
		<tr>
			<td width="100%" background="images/blueline_bg.gif">
			<table cellpadding="0" cellspacing="0" width="100%">
<?
			echo "<tr>\n";
			echo "	<td class=\"font_blue\" style=\"padding-bottom:2px;\"><input type=checkbox id=\"idx_allcheck\" name=allcheck value=\"".$cnt."\" style=\"BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none\" onclick=\"CheckAll('".$cnt."')\"><label style=\"cursor:hand; TEXT-DECORATION: none\" onmouseover=\"style.textDecoration='underline'\" onmouseout=\"style.textDecoration='none'\" for=idx_allcheck><span style=\"font-size:8pt;\">전체상품 선택</span></label></td>";
			echo "</tr>\n";
			if($page_numberic_type) {
				

				$total_block = intval($pagecount / $setup[page_num]);

				if (($pagecount % $setup[page_num]) > 0) {
					$total_block = $total_block + 1;
				}

				$total_block = $total_block - 1;

				if (ceil($t_count/$setup[list_num]) > 0) {
					// 이전	x개 출력하는 부분-시작
					$a_first_block = "";
					if ($nowblock > 0) {
						$a_first_block .= "<a href=\"javascript:GoPage(0,1,'".$sort."');\" onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='첫 페이지';return true\"><IMG src=\"images/icon_first.gif\" border=0 align=\"absmiddle\"></a>&nbsp;&nbsp;";

						$prev_page_exists = true;
					}

					$a_prev_page = "";
					if ($nowblock > 0) {
						$a_prev_page .= "<a href=\"javascript:GoPage(".($nowblock-1).",".($setup[page_num]*($block-1)+$setup[page_num]).",'".$sort."');\" onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='이전 ".$setup[page_num]." 페이지';return true\">[prev]</a>&nbsp;&nbsp;";

						$a_prev_page = $a_first_block.$a_prev_page;
					}

					// 일반 블럭에서의 페이지 표시부분-시작

					if (intval($total_block) <> intval($nowblock)) {
						$print_page = "";
						for ($gopage = 1; $gopage <= $setup[page_num]; $gopage++) {
							if ((intval($nowblock*$setup[page_num]) + $gopage) == intval($gotopage)) {
								$print_page .= "<span class=font_orange2><B>[".(intval($nowblock*$setup[page_num]) + $gopage)."]</B></span> ";
							} else {
								$print_page .= "<a href=\"javascript:GoPage(".$nowblock.",".(intval($nowblock*$setup[page_num]) + $gopage).",'".$sort."');\" onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$setup[page_num]) + $gopage)."';return true\">[".(intval($nowblock*$setup[page_num]) + $gopage)."]</a> ";
							}
						}
					} else {
						if (($pagecount % $setup[page_num]) == 0) {
							$lastpage = $setup[page_num];
						} else {
							$lastpage = $pagecount % $setup[page_num];
						}

						for ($gopage = 1; $gopage <= $lastpage; $gopage++) {
							if (intval($nowblock*$setup[page_num]) + $gopage == intval($gotopage)) {
								$print_page .= "<span class=font_orange2><B>[".(intval($nowblock*$setup[page_num]) + $gopage)."]</B></span> ";
							} else {
								$print_page .= "<a href=\"javascript:GoPage(".$nowblock.",".(intval($nowblock*$setup[page_num]) + $gopage).",'".$sort."');\" onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$setup[page_num]) + $gopage)."';return true\">[".(intval($nowblock*$setup[page_num]) + $gopage)."]</a> ";
							}
						}
					}		// 마지막 블럭에서의 표시부분-끝


					$a_last_block = "";
					if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
						$last_block = ceil($t_count/($setup[list_num]*$setup[page_num])) - 1;
						$last_gotopage = ceil($t_count/$setup[list_num]);

						$a_last_block .= "&nbsp;&nbsp;<a href=\"javascript:GoPage(".$last_block.",".$last_gotopage.",'".$sort."');\" onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='마지막 페이지';return true\"><IMG src=\"images/icon_last.gif\" border=0 align=\"absmiddle\" width=\"17\" height=\"14\"></a>";

						$next_page_exists = true;
					}

					// 다음 10개 처리부분...

					$a_next_page = "";
					if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
						$a_next_page .= "&nbsp;&nbsp;<a href=\"javascript:GoPage(".($nowblock+1).",".($setup[page_num]*($nowblock+1)+1).",'".$sort."');\" onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='다음 ".$setup[page_num]." 페이지';return true\">[next]</a>";

						$a_next_page = $a_next_page.$a_last_block;
					}
				} else {
					$print_page = "<B>[1]</B>";
				}
				echo "<tr>\n";
				echo "	<td height=\"30\" align=center>\n";
				echo "	".$a_div_prev_page.$a_prev_page.$print_page.$a_next_page.$a_div_next_page."\n";
				echo "	</td>\n";
				echo "</tr>\n";
			}
?>
			</table>
			</td>
		</tr>
<?
		if ($t_count>0) {
?>
		<input type=hidden name=copycode value="<?=$copycode?>">
		<input type=hidden name=oldcode value="<?=$code?>">
		<tr>
			<td width="100%" bgcolor="#0099CC" style="padding-top:3pt; padding-bottom:3pt;">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td width="100%" class="font_white1">
				<table cellpadding="0" cellspacing="0" width="100%">
				<col width="110"></col>
				<col width=""></col>
				<col width="95"></col>
				<tr>
					<td class="font_white1">&nbsp;&nbsp;이동/복사할 카테고리 : </td>
					<td><input type=text name=copycode_name size=43 style="width:100%;" onfocus="this.blur();alert('[카테고리 선택] 버튼을 이용하셔서 이동/복사시킬 위치의 카테고리를 선택하시기 바랍니다.');" value="<?=htmlspecialchars(stripslashes($copycode_name))?>" class="input" style="width:100%;"></td>
					<td align=center><a href="javascript:CopyCodeSelect();"><img src="images/btn_cateselect.gif" border="0"></a></td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td width="100%" class="font_white1">&nbsp;<input type=checkbox id="idx_newtime" name=newtime value="Y" style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none"> <label style='cursor:hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_newtime>이동/복사된 상품의 등록날짜를 현재시간으로 재설정합니다.</label></td>
			</tr>
			</table>
			</td>
		</tr>
		</table>
		</div>
		</td>
	</tr>
	<tr>
		<td width="100%" align=center style="padding-top:6pt; padding-bottom:6pt;"><span style="font-size:8pt; letter-spacing:-0.5pt;" class="font_orange">상품 이동/복사는 <b>최하위 또는 마지막 카테고리에서만 적용</b>됩니다.</span><br>
		<a href="javascript:Copy('copy');"><img src="images/btn_copy.gif" width="136" height="38" border="0" vspace="3"></a>&nbsp;
		<a href="javascript:Copy('move');"><img src="images/btn_trans.gif" width="136" height="38" border="0" vspace="3"></a>&nbsp;
		<a href="javascript:Delete();"><img src="images/btn_del4.gif" width="136" height="38" border="0" vspace="3"></a></td>
	</tr>
	</table>
	</td>
</tr>

<?
		}
?>
</form>
<form name=form_reg action="product_register.php" method=post>
<input type=hidden name=code>
<input type=hidden name=prcode>
<input type=hidden name=popup>
</form>
<?if($vendercnt>0){?>
<form name=vForm action="vender_infopop.php" method=post>
<input type=hidden name=vender>
</form>
<?}?>
</table>
<?=$onload?>

</body>
</html>