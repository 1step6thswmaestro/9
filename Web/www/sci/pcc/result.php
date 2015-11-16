<?
$Dir="../../";
header ("Cache-Control : no-cache");
header ("Cache-Control : post-check=0 pre-check=0");
header ("Pragma:no-cache");

include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

include_once './sci_pcc.class.php';
$sci_pcc = new sci_pcc();
$retInfo = $sci_pcc->_retInfo($_REQUEST["retInfo"]);
?>
<html>
<head>
<title>본인확인서비스 서비스 처리결과</title>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
<script language="javascript" type="text/javascript">
function end(){

	<?
	if(is_array($retInfo) && !empty($retInfo['reqNum']) && (!empty($retInfo['cellNo']) || !empty($retInfo['di']))){
		if(!empty($retInfo['di'])){
			$sql = "SELECT count(*) FROM tblmember WHERE uniqNo='H".$retInfo['di']."' ";
		}else{
			$sql = "SELECT count(*) FROM tblmember WHERE replace(mobile,'-','') ='".$retInfo['cellNo']."' ";
		}

		if(false !== $res = mysql_query($sql,get_db_conn())){
			if(mysql_result($res,0,0) < 1){
				$sql = 'insert into sci_pcc_log set ';
				foreach($retInfo as $k=>$v){
					if(in_array(substr($k,0,3),array('ext','err'))) continue;
					$sql .= $k."='".$v."',";
				}
				$sql = substr($sql,0,-1);
				@mysql_query($sql,get_db_conn());
				switch($retInfo['addVar']){
					case '/front/member_agree.php': //회원 가입 페이지
					case '/m/member_agree.php':
	?>

	opener.document.form1.scitype.value = 'pcc';
	opener.document.form1.sciReqNum.value = '<?=$retInfo['reqNum']?>';
	opener.document.form1.submit();
	//if(opener.document.getElementById('sciResult')) opener.document.getElementById('sciResult').innerHTML('<?=$retInfo['name']?> 님의 본인 확인이 처리 되었습니다.');
	<?
						break;
					default:
	?>
	alert('호출 페이지 정보구분이 정의 되지 않았습니다.');
	<?
						break;
				}
			}else{ ?>
		alert('이미 가입된 회원 입니다.');
	<? 		}
		}else{ ?>
		alert('DB검증 오류가 발생했습니다.');
	<?	}
	}else{ ?>
		alert('알수 없는 오류가 발생했습니다.');
	<? } ?>
	self.close();

}
</script>
</head>
<body onLoad="javascript:end()">
</body>
</html>

