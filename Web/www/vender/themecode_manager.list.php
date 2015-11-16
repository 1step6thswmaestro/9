<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/venderlib.php");
INCLUDE ("access.php");

$codeA=$_REQUEST["codeA"];
if(strlen($codeA)!=3) $codeA="";
$codeA_name="";

$mode=$_POST["mode"];
if($mode=="disptypeupdate" && strlen($_POST["code_disptype"])==2) {
	$code_disptype=$_POST["code_disptype"];
	if($code_disptype!="YY" && $code_disptype!="YN" && $code_disptype!="NY") {
		exit;
	}
	$sql = "UPDATE tblvenderstore SET code_distype='".$code_disptype."' ";
	$sql.= "WHERE vender='".$_VenderInfo->getVidx()."' ";
	mysql_query($sql,get_db_conn());
	echo "<html></head><body onload=\"alert('카테고리 노출 설정이 완료되었습니다.')\"></body></html>";exit;
} else if($mode=="update") {
	if(strlen($codeA)==3) {
		$sql = "SELECT code_name FROM tblvenderthemecode ";
		$sql.= "WHERE vender='".$_VenderInfo->getVidx()."' ";
		$sql.= "AND codeA='".$codeA."' AND codeB='000' ";
		$result=mysql_query($sql,get_db_conn());
		if($row=mysql_fetch_object($result)) {
			$codeA_name=$row->code_name;
		} else $codeA="";
		mysql_free_result($result);
	}

	$codeCnt=$_POST["codeCnt"];
	$delCnt=$_POST["delCnt"];
	$savecodes=explode("=",$_POST["savecodes"]);
	$delcodes=explode("=",$_POST["delcodes"]);

	$delsql="";
	$delprdt="";
	$delthemedesign="";
	if(count($delcodes)>0) {
		$j=0;
		for($i=0;$i<count($delcodes);$i++) {
			if(strlen($delcodes[$i])==6) {
				if($j>0) {
					$delsql.=" OR ";
					$delprdt.=" OR ";
				}
				if(strlen($codeA)==3) {
					$delsql.="(codeA='".substr($delcodes[$i],0,3)."' AND codeB='".substr($delcodes[$i],3,3)."') ";
					$delprdt.="(themecode='".$delcodes[$i]."') ";
				} else {
					$delsql.="(codeA='".substr($delcodes[$i],0,3)."') ";
					$delprdt.="(themecode LIKE '".substr($delcodes[$i],0,3)."%') ";
				}
				if(substr($delcodes[$i],3,3)=="000") {
					$delthemedesign.=substr($delcodes[$i],0,3).",";
				}
				$j++;
			}
		}
	}
	if(strlen($delsql)>0) {
		$sql = "DELETE FROM tblvenderthemecode WHERE vender='".$_VenderInfo->getVidx()."' ";
		$sql.= "AND (".$delsql.") ";
		if(mysql_query($sql,get_db_conn())) {
			$sql = "DELETE FROM tblvenderthemeproduct WHERE vender='".$_VenderInfo->getVidx()."' ";
			$sql.= "AND (".$delprdt.") ";
			mysql_query($sql,get_db_conn());

			if(strlen($delthemedesign)>0) {
				//대분류 화면관리 delete (tblvendercodedesign)
				$delthemedesign=substr($delthemedesign,0,-1);
				$delthemedesign=ereg_replace(',','\',\'',$delthemedesign);
				$sql = "DELETE FROM tblvendercodedesign WHERE vender='".$_VenderInfo->getVidx()."' AND code IN ('".$delthemedesign."') AND tgbn='20' ";
				mysql_query($sql,get_db_conn());
			}
		}
	}

	$codes_in=array();
	if(count($savecodes)>0) {
		$j=0;
		for($i=0;$i<count($savecodes);$i++) {
			$sequence=9999-$i;
			$temp=explode("",$savecodes[$i]);
			if(strlen($temp[0])==0) {
				if(strlen($temp[1])>0) {
					$codes_in[$j]["sequence"]=$sequence;
					$codes_in[$j]["code_name"]=$temp[1];
					$j++;
				}
			} else {
				$sql = "UPDATE tblvenderthemecode SET sequence='".$sequence."', ";
				$sql.= "code_name='".$temp[1]."' ";
				$sql.= "WHERE vender='".$_VenderInfo->getVidx()."' ";
				$sql.= "AND codeA='".substr($temp[0],0,3)."' AND codeB='".substr($temp[0],3,3)."' ";
				mysql_query($sql,get_db_conn());
			}
		}
	}
	if(count($codes_in)>0) {
		if(strlen($codeA)==3) {	//중분류 생성
			$sql = "DELETE FROM tblvenderthemeproduct WHERE vender='".$_VenderInfo->getVidx()."' ";
			$sql.= "AND themecode='".$codeA."000' ";
			mysql_query($sql,get_db_conn());

			$sql = "SELECT MAX(codeB) as maxcodeB FROM tblvenderthemecode ";
			$sql.= "WHERE vender='".$_VenderInfo->getVidx()."' ";
			$sql.= "AND codeA='".$codeA."' ";
			$result=mysql_query($sql,get_db_conn());
			$row=mysql_fetch_object($result);
			mysql_free_result($result);
			$in_codeB=(int)$row->maxcodeB+1;
			$in_codeB="000".$in_codeB;
			$in_codeB=substr($in_codeB,-3);
			for($i=0;$i<count($codes_in);$i++) {
				$sql = "INSERT tblvenderthemecode SET ";
				$sql.= "vender		= '".$_VenderInfo->getVidx()."', ";
				$sql.= "codeA		= '".$codeA."', ";
				$sql.= "codeB		= '".$in_codeB."', ";
				$sql.= "code_name	= '".$codes_in[$i]["code_name"]."', ";
				$sql.= "sequence	= '".$codes_in[$i]["sequence"]."' ";
				if(mysql_query($sql,get_db_conn())) {
					$in_codeB=(int)$in_codeB+1;
					$in_codeB="000".$in_codeB;
					$in_codeB=substr($in_codeB,-3);

					//기존 대분류에 등록된 상품 해제
					$sql = "DELETE FROM tblvenderthemeproduct WHERE vender='".$_VenderInfo->getVidx()."' ";
					$sql.= "AND themecode='".$codeA."000' ";
					mysql_query($sql,get_db_conn());
				}
			}
		} else {				//대분류 생성
			$sql = "SELECT MAX(codeA) as maxcodeA FROM tblvenderthemecode ";
			$sql.= "WHERE vender='".$_VenderInfo->getVidx()."' ";
			$result=mysql_query($sql,get_db_conn());
			$row=mysql_fetch_object($result);
			mysql_free_result($result);
			$in_codeA=(int)$row->maxcodeA+1;
			$in_codeA="000".$in_codeA;
			$in_codeA=substr($in_codeA,-3);
			$in_codeB="000";
			for($i=0;$i<count($codes_in);$i++) {
				$sql = "INSERT tblvenderthemecode SET ";
				$sql.= "vender		= '".$_VenderInfo->getVidx()."', ";
				$sql.= "codeA		= '".$in_codeA."', ";
				$sql.= "codeB		= '".$in_codeB."', ";
				$sql.= "code_name	= '".$codes_in[$i]["code_name"]."', ";
				$sql.= "sequence	= '".$codes_in[$i]["sequence"]."' ";
				if(mysql_query($sql,get_db_conn())) {
					$sql = "INSERT tblvendercodedesign SET ";
					$sql.= "vender		= '".$_VenderInfo->getVidx()."', ";
					$sql.= "code		= '".$in_codeA."', ";
					$sql.= "tgbn		= '20', ";
					$sql.= "hot_used	= '1', ";
					$sql.= "hot_dispseq	= '118', ";
					$sql.= "hot_linktype= '1' ";
					mysql_query($sql,get_db_conn());

					$in_codeA=(int)$in_codeA+1;
					$in_codeA="000".$in_codeA;
					$in_codeA=substr($in_codeA,-3);
				}
			}
		}
	}

	echo "<html></head><body onload=\"alert('요청하신 작업이 성공하였습니다.');parent.location.reload()\"></body></html>";exit;
}

if(strlen($codeA)==3 && strlen($codeA_name)==0) {
	$sql = "SELECT code_name FROM tblvenderthemecode ";
	$sql.= "WHERE vender='".$_VenderInfo->getVidx()."' ";
	$sql.= "AND codeA='".$codeA."' AND codeB='000' ";
	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		$codeA_name=$row->code_name;
	} else $codeA="";
	mysql_free_result($result);
}

?>

<html>
<head>
<title></title>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META http-equiv="X-UA-Compatible" content="IE=5" />
<link rel=stylesheet href="style.css" type=text/css>
<script language=javascript src="themecodemgr.js.php"></script>

</head>
<body marginwidth=0 marginheight=0 leftmargin=0 topmargin=0>
<form name=form1 method=post action="<?=$_SERVER[PHP_SELF]?>">
<input type="hidden" name="mode">
<input type="hidden" name="codeA" value="<?=$codeA?>">
<input type="hidden" name="codeCnt">
<input type="hidden" name="delCnt">

<span id="oData"></span>

<table width=100% border=0 cellspacing=0 cellpadding=0 bgcolor=FFFFFF>
<tr>
	<td bgcolor=FEFCE2 style=padding:5,10>
	<B>대분류명</B>
<?
	if(strlen($codeA_name)>0) {
		echo " <B>: ".$codeA_name."</B>";
		echo "<br><img width=0 height=1><br><B>중분류명</B>";
	}
?>
	</td>
</tr>
<tr>
	<td>
	<select name=code size=13 style=width:435 onClick="f_setEdit()" >
<?
	$sql = "SELECT codeA,codeB,code_name FROM tblvenderthemecode ";
	$sql.= "WHERE vender='".$_VenderInfo->getVidx()."' ";
	if(strlen($codeA)==3) {
		$sql.= "AND codeA='".$codeA."' AND codeB!='000' ";
	} else {
		$sql.= "AND codeB='000' ";
	}
	$sql.= "ORDER BY sequence DESC ";
	$result=mysql_query($sql,get_db_conn());
	while($row=mysql_fetch_object($result)) {
		echo "<option value=\"".$row->codeA.$row->codeB."\">".$row->code_name."</option>\n";
	}
	mysql_free_result($result);
?>
	</select>
	</td>
</tr>
</table>
</form>
</body>
</html>