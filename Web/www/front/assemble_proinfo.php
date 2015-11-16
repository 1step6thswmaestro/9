<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$origin_productcode=$_REQUEST["op"];	// 조합상품아이디

if(strlen($code)==0) {
	$code=substr($origin_productcode,0,12);
}
$codeA=substr($code,0,3);
$codeB=substr($code,3,3);
$codeC=substr($code,6,3);
$codeD=substr($code,9,3);
if(strlen($codeA)!=3) $codeA="000";
if(strlen($codeB)!=3) $codeB="000";
if(strlen($codeC)!=3) $codeC="000";
if(strlen($codeD)!=3) $codeD="000";
$likecode=$codeA;
if($codeB!="000") $likecode.=$codeB;
if($codeC!="000") $likecode.=$codeC;
if($codeD!="000") $likecode.=$codeD;

// 조합상품 권한 체크(조합상품 권한이 없을 경우 개별 상품 정보도 열람 불가)
if(strlen($origin_productcode)==18) {
	$sql = "SELECT * FROM tblproductcode WHERE codeA='".$codeA."' AND codeB='".$codeB."' AND codeC='".$codeC."' AND codeD='".$codeD."' ";
	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		if($row->group_code=="NO") {	//숨김 분류
			echo "<html></head><body onload=\"alert('판매가 종료된 상품입니다.');self.close();\"></body></html>";exit;
		} else if($row->group_code=="ALL" && strlen($_ShopInfo->getMemid())==0) {	//회원만 접근가능
			echo "<html></head><body onload=\"alert('해당 분류의 접근 권한이 없습니다.');self.close();\"></body></html>";exit;
		} else if(strlen($row->group_code)>0 && $row->group_code!="ALL" && $row->group_code!=$_ShopInfo->getMemgroup()) {	//그룹회원만 접근
			echo "<html></head><body onload=\"alert('해당 분류의 접근 권한이 없습니다.');self.close();\"></body></html>";exit;
		}
	} else {
		echo "<html></head><body onload=\"alert('해당 분류가 존재하지 않습니다.');self.close();\"></body></html>";exit;
	}
	mysql_free_result($result);
} else {
	echo "<html></head><body onload=\"alert('해당 상품 정보가 존재하지 않습니다.');self.close();\"></body></html>";exit;
}

$productcode=$_REQUEST["np"];	// 개별상품아이디

$selfcodefont_start = "<font class=\"prselfcode\">"; //진열코드 폰트 시작
$selfcodefont_end = "</font>"; //진열코드 폰트 끝

if(strlen($productcode)==18) {
	$sql = "SELECT a.* ";
	$sql.= "FROM tblproduct AS a ";
	$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
	$sql.= "WHERE a.productcode='".$productcode."' AND a.display='Y' ";
	$sql.= "AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";
	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		$_pdata=$row;

		$sql = "SELECT * FROM tblproductbrand ";
		$sql.= "WHERE bridx='".$_pdata->brand."' ";
		$bresult=mysql_query($sql,get_db_conn());
		$brow=mysql_fetch_object($bresult);
		$_pdata->brandcode = $_pdata->brand;
		$_pdata->brand = $brow->brandname;

		mysql_free_result($result);

	} else {
		echo "<html></head><body onload=\"alert('해당 상품 정보가 존재하지 않습니다.');self.close();\"></body></html>";exit;
	}
} else {
	echo "<html></head><body onload=\"alert('해당 상품 정보가 존재하지 않습니다.');self.close();\"></body></html>";exit;
}

//상품단어 필터링
if(strlen($_data->filter)>0) {
	$arr_filter=explode("#",$_data->filter);
	$detail_filter=$arr_filter[0];
	$filters=explode("=",$detail_filter);
	$filtercnt=count($filters)/2;

	for($i=0;$i<$filtercnt;$i++){
		$filterpattern[$i]="/".str_replace("\0","\\0",preg_quote($filters[$i*2]))."/";
		$filterreplace[$i]=$filters[$i*2+1];
		if(strlen($filterreplace[$i])==0) $filterreplace[$i]="***";
	}
}

// 제조회사, 모델명, 브랜드, 출시일, 진열코드, 특이사항 사용자 정의 스펙
$arproduct=array(&$prproduction,&$prmodel,&$prbrand,&$propendate,&$prselfcode,&$praddcode,&$pruserspec0,&$pruserspec1,&$pruserspec2,&$pruserspec3,&$pruserspec4);
?>
<HTML>
<HEAD>
<TITLE><?=$_data->shopname." [".$_pdata->productname."]"?></TITLE>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META http-equiv="X-UA-Compatible" content="IE=5" />
<META name="description" content="<?=(strlen($_data->shopdescription)>0?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<?include($Dir."lib/style.php")?>
</HEAD>

<body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0">
<table border="0" cellpadding="0" cellspacing="0" width="100%">
<tr>
	<td>
	<table border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td style="padding-left:5px;padding-right:5px;">
		<table border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
		<tr>
			<td height="20"></td>
		</tr>
		<tr>
			<td style="padding-left:5px;padding-right:5px;">
			<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">
				<col width="46%"></col>
				<col width="1%"></col>
				<col width=></col>
				<tr>
					<td valign="top">
					<table cellpadding="0" cellspacing="0" width="100%">
<?
					echo "<tr><td align=\"center\">";
					if(strlen($_pdata->maximage)>0 && file_exists($Dir.DataDir."shopimages/product/".$_pdata->maximage)) {
						$imgsize=GetImageSize($Dir.DataDir."shopimages/product/".$_pdata->maximage);
						if(($imgsize[1]>550 || $imgsize[0]>750) && $multi_img!="I") $imagetype=1;
						else $imagetype=0;
					}
					if(strlen($_pdata->minimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$_pdata->minimage)) {
						$width=GetImageSize($Dir.DataDir."shopimages/product/".$_pdata->minimage);
						if($width[0]>=300) $width[0]=300;
						else if (strlen($width[0])==0) $width[0]=300;
						echo "<img src=\"".$Dir.DataDir."shopimages/product/".$_pdata->minimage."\" border=\"0\" width=\"".$width[0]."\"></td>\n";
					} else {
						echo "<img src=\"".$Dir."images/no_img.gif\" border=\"0\"></td>\n";
					}
					echo "</tr>\n";
					echo "<tr><td height=\"10\"></td></tr><tr><td align=\"center\">";
					echo "</tr><tr><td height=\"5\"></td></tr>\n";
?>
					</table>
					</td>
					<td></td>
					<td valign="top">
					<table cellpadding="0" cellspacing="8" width="100%" bgcolor="#E8E8E8">
					<tr>
						<td style="padding:8px;" bgcolor="FFFFFF">
						<table cellpadding="0" cellspacing="0" width="100%" bgcolor="white">
						<tr>
							<td><font color="#FF4C00" style="font-size:15px;letter-spacing:-0.5pt;word-break:break-all;"><b><?=viewproductname($_pdata->productname,$_pdata->etctype,"")?></b></font></td>
						</tr>
						<tr>
							<td height="5"></td>
						</tr>
						<tr>
							<td background="<?=$Dir?>images/common/assemble_proinfo/assemble_proinfo_titleline.gif" HEIGHT="3"></td>
						</tr>
						<tr>
							<td height="5"></td>
						</tr>
						<tr>
							<td width="100%">
							<table cellpadding="0" cellspacing="0" width="100%">
							<col width="14" align="center"></col>
							<col width="64"></col>
							<col width="13"></col>
							<col width=></col>
<?
							if(strlen($_pdata->production)>0) {
								$prproduction ="<td><IMG SRC=\"".$Dir."images/common/assemble_proinfo/assemble_proinfo_point.gif\" border=\"0\"></td>\n";
								$prproduction.="<td>제조회사</td>\n";
								$prproduction.="<td></td>";
								$prproduction.="<td>".$_pdata->production."</td>\n";
							}
							if(strlen($_pdata->model)>0) {
								$prmodel ="<td><IMG SRC=\"".$Dir."images/common/assemble_proinfo/assemble_proinfo_point.gif\" border=\"0\"></td>\n";
								$prmodel.="<td>모델명</td>\n";
								$prmodel.="<td></td>";
								$prmodel.="<td>".$_pdata->model."</td>\n";
							}
							if(strlen($_pdata->brand)>0) {
								$prbrand ="<td><IMG SRC=\"".$Dir."images/common/assemble_proinfo/assemble_proinfo_point.gif\" border=\"0\"></td>\n";
								$prbrand.="<td>브랜드</td>\n";
								$prbrand.="<td></td>";
								if($_data->ETCTYPE["BRANDPRO"]=="Y") {
									$prbrand.="<td>".$_pdata->brand."</td>\n";
								} else {
									$prbrand.="<td>".$_pdata->brand."</td>\n";
								}
							}
							if(strlen($_pdata->userspec)>0) {
								$specarray= explode("=",$_pdata->userspec);
								for($i=0; $i<count($specarray); $i++) {
									$specarray_exp = explode("", $specarray[$i]);
									if(strlen($specarray_exp[0])>0 || strlen($specarray_exp[1])>0) {
										${"pruserspec".$i} ="<td><IMG SRC=\"".$Dir."images/common/assemble_proinfo/assemble_proinfo_point.gif\" border=\"0\"></td>\n";
										${"pruserspec".$i}.="<td>".$specarray_exp[0]."</td>\n";
										${"pruserspec".$i}.="<td></td>";
										${"pruserspec".$i}.="<td>".$specarray_exp[1]."</td>\n";
									} else {
										${"pruserspec".$i} = "";
									}
								}
							}
							if(strlen($_pdata->selfcode)>0) {
								$prselfcode ="<td><IMG SRC=\"".$Dir."images/common/assemble_proinfo/assemble_proinfo_point.gif\" border=\"0\"></td>\n";
								$prselfcode.="<td>진열코드</td>\n";
								$prselfcode.="<td></td>";
								$prselfcode.="<td>".$selfcodefont_start.$_pdata->selfcode.$selfcodefont_end."</td>\n";
							}
							if(strlen($_pdata->opendate)>0) {
								$propendate ="<td><IMG SRC=\"".$Dir."images/common/assemble_proinfo/assemble_proinfo_point.gif\" border=\"0\"></td>\n";
								$propendate.="<td>출시일</td>\n";
								$propendate.="<td></td>";
								$propendate.="<td>".@substr($_pdata->opendate,0,4).(@substr($_pdata->opendate,4,2)?"-".@substr($_pdata->opendate,4,2):"").(@substr($_pdata->opendate,6,2)?"-".@substr($_pdata->opendate,6,2):"")."</td>\n";
							}
							if(strlen($_pdata->addcode)>0) {
								$praddcode ="<td><IMG SRC=\"".$Dir."images/common/assemble_proinfo/assemble_proinfo_point.gif\" border=\"0\"></td>\n";
								$praddcode.="<td>특이사항</td>\n";
								$praddcode.="<td></td>";
								$praddcode.="<td>".$_pdata->addcode."</td>\n";
							}

							for($i=0;$i<count($arproduct);$i++) {
								if(strlen($arproduct[$i])>0)
									echo "<tr height=\"22\">".$arproduct[$i]."</tr>\n";
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
				</table>
				</td>
			</tr>
			<tr>
				<td height="20"></td>
			</tr>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td><IMG SRC="<?=$Dir?>images/common/assemble_proinfo/assemble_proinfo_title_top.gif" border="0"></td>
					<td width="100%" background="<?=$Dir?>images/common/assemble_proinfo/assemble_proinfo_title_bg.gif"></td>
					<td><IMG SRC="<?=$Dir?>images/common/assemble_proinfo/assemble_proinfo_title_bottom.gif" border="0"></td>
				</tr>
				</table>
				<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
				<tr>
					<td valign="top">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td style="padding:5px;">
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
						</td>
					</tr>
					</table>
					</td>
				</tr>
				</table>
				</td>
			</tr>
			<tr><td height="20"></td></tr>
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
	<td height="50" align="center"><a href="javascript:self.close();"><IMG SRC="<?=$Dir?>images/common/assemble_proinfo/assemble_proinfo_close.gif" border="0"></a></td>
</tr>
</table>
<?=$onload?>
</BODY>
</HTML>