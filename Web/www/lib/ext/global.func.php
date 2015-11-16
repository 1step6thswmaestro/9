<?

$includeFiles = array (
	"lib/ext/product_func.php",		// 상품
	"lib/ext/order_func.php",			// 주문
	"lib/ext/member_func.php",		// 회원
	"lib/ext/coupon_func.php",		// 쿠폰
	"lib/ext/promote.php"				// 홍보
);

// 신규 함수
if(strlen(trim($Dir))){
	foreach ( $includeFiles as $f ){ include_once($Dir.$f); }
}else{
	foreach ( $includeFiles as $f ){ include_once($_SERVER['DOCUMENT_ROOT']."/".$f); }
}



function editorImsgeUrlSolv($content,$original=''){
	/** 에디터 관련 파일 처리 추가 부분 */
	if(!_empty($original) && preg_match_all('/\/data\/editor\/([a-zA-Z0-9\.]+)/',$original,$edtimg)){
		if(!preg_match_all('/\/data\/editor\/([a-zA-Z0-9\.]+)/',$content,$edimg)) $edimg[1] =array();
		foreach($edtimg[1] as $cimg){
			if(!in_array($cimg,$edimg[1])) @unlink($_SERVER['DOCUMENT_ROOT'].'/data/editor/'.$cimg);
		}
	}
	/** #에디터 관련 파일 처리 추가 부분 */

	/** 에디터 관련 파일 처리 추가 부분 */
	if(preg_match_all('/\/data\/editor_temp\/([a-zA-Z0-9\.]+)/',$content,$edimg)){
		foreach($edimg[1] as $timg){
			@rename($_SERVER['DOCUMENT_ROOT'].'/data/editor_temp/'.$timg,$_SERVER['DOCUMENT_ROOT'].'/data/editor/'.$timg);
		}
		$content = str_replace('/data/editor_temp/','/data/editor/',$content);
	}
	/** #에디터 관련 파일 처리 추가 부분 */
	return $content;
}






// 2013-10-11 IP차단 x2chi
// (IP,{block,test,print})
//test:접근가능검사 (default)
//block:차단
//print:출력
function ipAuth ( $ip , $test = "test" ) {

	$ipOrg = $ip;
	$ip = ip2long($ip);

	$ipSQL = "SELECT * FROM `tblConnIP_block` WHERE `IP` = '".$ip."' ";
	$ipResult = mysql_query( $ipSQL , get_db_conn() );
	$auth = ( @mysql_num_rows ( $ipResult ) > 0 )?"block":"access";

	$returnArray = array("ip"=>$ipOrg, "auth"=>$auth);

	if( $auth == "block" AND $test == "block" ) {
		exit(
			"
				<center>
					<img src='/images/blockingImages.jpg' alt='접근 하신 IP [ ".$ipOrg." ] 해당 사이트에서 차단된 IP입니다.'>
				</center>
			"
		);
	}
	if( $test == "print" ) {
		_pr($returnArray);
		//exit;
	}
	return $returnArray;

}
ipAuth($_SERVER['REMOTE_ADDR'], "block");


// 접근 아이피 로그
function connIP ( $ip ) {
	@session_start();
	global $_ShopInfo;

	if( substr($ip,0,4) != "127." ) {

		$ip = ip2long($ip);
		$id = $_ShopInfo->getMemid();
		$key = $ip."_".$id; //date("Ymd")."_".

		if( $_SESSION['connIP'] != $key ) {

			$serverInfo = "";
			foreach ( $_SERVER as $k => $v ){
				$serverInfo .= "|".$k."=".$v;
			}
			$ipInSQL = "INSERT `tblConnIP_list` SET `IP` = '".$ip."' , `conn`=NOW(), `serverInfo` = '".$serverInfo."' ; ";
			@mysql_query( $ipInSQL , get_db_conn() );
			if(strlen($id)>0){
				$ipMemInSQL = "INSERT `tblConnIP_memid` SET `IP` = '".$ip."' , `memid`='".$id."', `conn`=NOW() ; ";
				@mysql_query( $ipMemInSQL , get_db_conn() );
			}
			$ipStatisticsSQL = "SELECT * FROM `tblConnIP_statistics` WHERE `IP` = '".$ip."' ";
			$ipStatisticsResult = mysql_query( $ipStatisticsSQL , get_db_conn() );
			if ( @mysql_num_rows ( $ipStatisticsResult ) == 0 ) {
				$ipStatisticsInputSQL = "INSERT `tblConnIP_statistics` SET `IP` = '".$ip."' ;";
			} else {
				$ipStatisticsInputSQL = "UPDATE `tblConnIP_statistics` SET `count`=`count`+1 WHERE `IP` = '".$ip."' LIMIT 1; ";
			}
			@mysql_query( $ipStatisticsInputSQL , get_db_conn() );

			$_SESSION['connIP'] = $key;
		}
	}
}
connIP ( $_SERVER['REMOTE_ADDR'] );




// 도매회원 사용 여부
function isWholesale () {
	$ROW = mysql_fetch_assoc ( mysql_query( "SELECT `wholesalemember` FROM `tblshopinfo` LIMIT 1 ;" ) );
	return $ROW['wholesalemember'];
}



// 테이블 비우기 !! 주의 데이터 다 날라감
function truncateTables ( $tableName ) {
	$sql = "TRUNCATE TABLE `".$tableName."`";
	return mysql_query( $sql, get_db_conn() );
}





// SMS key
function smsCountValue () {
	$sql = "SELECT id, authkey, return_tel FROM tblsmsinfo ";
	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)){
		$return_tel = explode("-",$row->return_tel);
		$sms_id=$row->id;
		$sms_authkey=$row->authkey;
	}
	mysql_free_result($result);

	$smscountdata=getSmscount($sms_id, $sms_authkey);

	$smscount = explode("|",$smscountdata);

	return $smscount[1];
}


// 관리자 페이지용 시작 ===============================================================================================================



	// 관리자 > 개별 디자인 백업/복구, 미리보기 저장 여부 확인
	function isAdminDesingCHK ( $tables, $type, $code='', $field='' ) {
		$sql = "SELECT * FROM ".$tables." WHERE ";
		$sql .= "type='".$type."' ";
		if( strlen($code) > 0 AND $code != "codeChange" ) $sql .= " AND code='".$code."' ";
		if( strlen($field) > 0 ) $sql .= " AND orgField='".$field."' ";
		$sql .= "LIMIT 1 ;";

		$result=mysql_query( $sql,get_db_conn());
		$row = mysql_fetch_assoc ($result);
		$return = (mysql_num_rows($result) > 0) ? true : false;
		mysql_free_result($result);

		return array( "row"=>$row, "check"=>$return );
	}


	// 관리자 > 개별 디자인 백업 / 복구
	/* adminDesingBackup (
			$mode, => 백업 / 복구 구분 (store:restore)
			$type,  => 구분 키 tbldesignnewpage 의 type
			$body,  => 내용 tbldesignnewpage 의 body
			$subject='',  => 제목 tbldesignnewpage 의 subject
			$code='',  => 추가기능 tbldesignnewpage 의 code
			$filename='',  => 추가기능 tbldesignnewpage 의 filename
			$leftmenu='',  => 메뉴사용 tbldesignnewpage 의 leftmenu
			$table = 'tbldesignnewpage', => 다른 테이블 디자인 소스일경우 테이블명 ( 기본:tbldesignnewpage )
			$field = '' => 다른 테이블 디자인 소스일경우 필드명
		)
	*/
	function adminDesingBackup ( $mode, $type, $body, $subject='', $code='', $filename='', $leftmenu='', $table = 'tbldesignnewpage', $field = '' ) {

		$returnError = "ERROR";

		// 백업
		if($mode=="store" && strlen(trim($body))>0){

			$returnMSG = $subject."디자인이";

			$returnError = "store";

			$temp = isAdminDesingCHK("tbldesignnewpage_temp",$type, $code, $field );
			
			if( $temp['check'] === true ) {
				
				$sql = "UPDATE tbldesignnewpage_temp SET ";
				if( strlen($subject) > 0 ) $sql.= "subject = '" .mysql_real_escape_string($subject). "', ";
				if( strlen($filename) > 0 ) $sql.= "filename = '" .mysql_real_escape_string($filename). "', ";
				if( strlen($leftmenu) > 0 ) $sql.= "leftmenu = '" .mysql_real_escape_string($leftmenu). "', ";
				$sql.= "body = '" .mysql_real_escape_string($body). "', ";
				$sql.= "orgTable = '" .mysql_real_escape_string($table). "' ";
				$sql.= "WHERE type = '" .mysql_real_escape_string($type). "' ";
				if( strlen($code) > 0 ) $sql.= " AND code = '" .mysql_real_escape_string($code). "' ";
				if( strlen($field) > 0 ) $sql.= " AND orgField = '" .mysql_real_escape_string($field). "' ";

				$returnError .= " update";
				$returnMSG = "";

			} else {

				$sql = "INSERT tbldesignnewpage_temp SET ";
				$sql.= "type = '".$type."', ";
				if( strlen($subject) > 0 ) $sql.= "subject = '" .mysql_real_escape_string($subject). "', ";
				if( strlen($code) > 0 ) $sql.= "code = '" .mysql_real_escape_string($code). "', ";
				if( strlen($filename) > 0 ) $sql.= "filename = '" .mysql_real_escape_string($filename). "', ";
				if( strlen($leftmenu) > 0 ) $sql.= "leftmenu = '" .mysql_real_escape_string($leftmenu). "', ";
				if( strlen($field) > 0 ) $sql.= "orgField = '" .mysql_real_escape_string($field). "', ";
				$sql.= "body = '" .mysql_real_escape_string($body). "', ";
				$sql.= "orgTable = '" .mysql_real_escape_string($table). "' ";

				$returnError .= " insert";

			}

			if ( $result = mysql_query($sql,get_db_conn()) ) {
				$returnError .= " OK";
				$returnMSG .= " 백업";
			} else {
				$returnError .= " ERROR";
				$returnMSG .= " 백업 실패";
			}
			@mysql_free_result($result);

			$returnMSG .= " 되었습니다.";
		}

		// 복구
		if($mode=="restore"){

			$returnMSG = $subject."디자인이";

			$returnError = "restore";

			if( $type == "bottom") $code='codeChange'; // bottom 은 code를 변경 함

			$temp = isAdminDesingCHK("tbldesignnewpage_temp",$type, $code, $field );

			if( $temp['check'] === true ) {
				$row = $temp['row'];
				if( $row['orgTable'] == "tbldesignnewpage" ) {
					
					$org = isAdminDesingCHK ("tbldesignnewpage", $type, $row['code'] );
					
					if( $org['check'] === true ) {
						$sql = "UPDATE tbldesignnewpage SET ";
						if( strlen($row['subject']) > 0 ) $sql.= "subject = '".$row['subject']."', ";
						if( strlen($row['filename']) > 0 ) $sql.= "filename = '".$row['filename']."', ";
						if( strlen($row['leftmenu']) > 0 ) $sql.= "leftmenu = '".$row['leftmenu']."', ";
						if( strlen($row['code']) > 0 AND $code == "codeChange" ) $sql.= "code = '".$row['code']."', ";
						$sql.= "body = '".$row['body']."' ";
						$sql.= "WHERE type = '".$row['type']."' ";
						if( strlen($row['code']) > 0 AND $code != "codeChange" ) $sql.= " AND code = '".$row['code']."' ";

						$returnError .= " update";
					} else {
						$sql = "INSERT tbldesignnewpage SET ";
						if( strlen($row['type']) > 0 ) $sql.= " type = '".$row['type']."', ";
						if( strlen($row['code']) > 0 ) $sql.= " code = '".$row['code']."', ";
						if( strlen($row['subject']) > 0 ) $sql.= "subject = '".$row['subject']."', ";
						if( strlen($row['filename']) > 0 ) $sql.= "filename = '".$row['filename']."', ";
						if( strlen($row['leftmenu']) > 0 ) $sql.= "leftmenu = '".$row['leftmenu']."', ";
						$sql.= "body = '".$row['body']."' ";

						$returnError .= " insert";
					}
				} else {
					$sql = "UPDATE ".$row['orgTable']." SET ";
					$sql.= "`".$row['orgField']."` = '".$row['body']."' ";

					$returnError .= " ".$row['orgTable']." update";
				}
			} else {
				$returnMSG .= " 백업된 정보가 없어";
			}

			if ( $result = mysql_query($sql,get_db_conn()) ) {
				$returnError .= " OK";
				$returnMSG .= " 복원";
			} else {
				$returnError .= " ERROR";
				$returnMSG .= " 복원 실패";
			}
			@mysql_free_result($result);

			$returnMSG .= " 되었습니다.";

		}

		//echo $sql;
		$returnMSG .= "(".$returnError.")";
		$returnMSG .= "\\n";
		return $returnMSG;

	}






	// 미리보기 저장
	function adminPreview ( $type, $body, $subject='', $code='', $filename='', $leftmenu='' ) {

		$returnError = "ERROR";

		if( strlen(trim($body)) > 0 ) {

			$returnMSG = $subject."미리보기";

			$returnError = "preview";

			// 미리보기 테이블 비우기
			//truncateTables('tbldesignnewpage_prev');

			$temp = isAdminDesingCHK("tbldesignnewpage_prev",$type, $code );

			if( $temp['check'] === true ) {

				$sql = "UPDATE tbldesignnewpage_prev SET ";
				if( strlen($subject) > 0 ) $sql.= "subject = '".$subject."', ";
				if( strlen($filename) > 0 ) $sql.= "filename = '".$filename."', ";
				if( strlen($leftmenu) > 0 ) $sql.= "leftmenu = '".$leftmenu."', ";
				$sql.= "body = '".$body."' ";
				$sql.= "WHERE type = '".$type."' ";
				if( strlen($code) > 0 ) $sql.= " AND code = '".$code."' ";

				$returnError .= " update";
				$returnMSG = "";

			} else {

				$sql = "INSERT tbldesignnewpage_prev SET ";
				$sql.= "type = '".$type."', ";
				if( strlen($subject) > 0 ) $sql.= "subject = '".$subject."', ";
				if( strlen($code) > 0 ) $sql.= "code = '".$code."', ";
				if( strlen($filename) > 0 ) $sql.= "filename = '".$filename."', ";
				if( strlen($leftmenu) > 0 ) $sql.= "leftmenu = '".$leftmenu."', ";
				$sql.= "body = '".$body."' ";

				$returnError .= " insert";

			}

			if ( $result = mysql_query($sql,get_db_conn()) ) {
				$returnError .= " OK";
				$returnMSG .= " 저장 성공";
			} else {
				$returnError .= " ERROR";
				$returnMSG .= " 저장 실패";
			}
			@mysql_free_result($result);

		}

		//echo $sql;
		$returnMSG .= "(".$returnError.")";
		$returnMSG .= "\\n";
		return $returnMSG;

	}

	function _getDeviceInfo(){
		$_devicelist = '/(iPod|iPhone|Android|BlackBerry|SymbianOS|SCH-M\d+|Opera Mini|Windows CE|Nokia|SonyEricsson|webOS|PalmOS)/';
		if ( $_GET['pc'] == "ON" ){
			$_deviceinfo = "P";
		}else{
			if(preg_match($_devicelist, $_SERVER['HTTP_USER_AGENT'])) {
				$_deviceinfo = "M";
			} else {
				$_deviceinfo = "P";
			}
		}
		return $_deviceinfo;
	}



// 관리자 페이지용 끝 ================================================================================================================
/* jyh 함수 모음 시작 */
function prt($data=null, $size=0) {
	//if (!isdev()) return;
	ob_start();
	print_r($data);
	$content_text = ob_get_contents();
	ob_end_clean();
	$content_list = explode("\n", $content_text);
	$rows = count($content_list);
	$cols = 0;
	foreach ($content_list as $line) {
		if (($col = mb_strlen($line)) > $cols) {
			$cols = $col;
			if ($cols > 128) $rows++;
		}
	}
	$rows += 2;
	$cols += 2;
	if (!headers_sent()) {
		header('Content-type: text/html; charset=euc-kr');
	}
	echo '<textarea cols="' . $cols . '" rows="' . $rows . '"';
	echo ' style="display:block; margin:0; padding:5px; border:1px solid #999;';
	if ($size) echo ' width:' . $size . 'px;';
	echo ' font-family:Hwsr_9pt, Gulimche; font-size:9pt;">';
	echo $content_text;
	echo '</textarea>';
}


//인젝션방지 함수
function getInjection($str) {
	if(!$str) return false;
	return addslashes(stripslashes($str));
}

//인젝션방지 함수2
function getInjection2($str) {
	if(!$str) return false;
	//$str = stripslashes($str);
	//$str = mysql_real_escape_string($str);

	$str = htmlspecialchars(stripslashes($str));
	$str = str_ireplace("script", "blocked", $str);
	$str = mysql_escape_string($str);


	return $str;
}

//디자인관리 구문 php소스만 막기
function getReplacePhp($str) {
	if(!$str) return false;

	$str = str_replace("<?","&lt?",$str);
	$str = str_replace("?>","?&gt",$str);
	return $str;
}

//파일삭제함수
function LIB_removeAllData( $URL ) {
	if(is_dir($URL)) {
		if( $dh = @opendir($URL)) {
			while( ( $file = @readdir( $dh ) ) !== false ) {
				if( $file == '.' || $file == ".." )	continue;

				if( @filetype( $URL.$file ) == "dir" )	LIB_removeAllData( $URL.$file.'/' );
				else	@unlink( $URL.$file );                    // 파일 삭제
			}
			//@rmdir( $URL );        // 폴더 삭제
			closedir( $dh );
		}
	}
}

//최상단폴더생성하고 퍼미션
function LIB_removeAfter_Dir($URL) {
	if(is_dir($URL)) {
		LIB_removeAllData($URL);
		@mkdir($URL);
		@chmod($URL,0707);
	}
}

function isdev() {
		//return isget($_SERVER, 'SERVER_ADDR') == '127.0.0.1';
		return true;
}

//페이지별 타이틀 이미지 컨트롤
function page_top_title() {
	global $_data, $config;
	$args = func_get_args();

	if (is_array($args) && empty($args)) return false;
	//prt($args);

	$Dir		= $args[0];
	$leftmenu	= $args[1];	//타이틀 이미지 노출할지 여부
	$page_type  = $args[2];	//페이지 구분값

	//$date_img	= $Dir.DataDir."design/".$config['page_top_title'][$page_type]['data'];	//사용자가 등록한 타이틀 이미지
	//$alt		= $config['page_top_title'][$page_type]['alt'];							//alt
	//$def_img	= $Dir."images/".$_data->icon_use_type."/".$config['page_top_title'][$page_type]['def'];	//default 타이틀 이미지

	$date_img	= $Dir.DataDir."design/".$page_type."_title.gif";	//사용자가 등록한 타이틀 이미지
	$alt		= $config['page_top_title'][$page_type]['alt'];							//alt
	$def_img	= $Dir.DataDir."design/skin/".$_data->icon_use_type."/etc/".$page_type."_title_head.gif";	//default 타이틀 이미지


	if ($leftmenu!="N") {
	//if ($leftmenu=="N") {
		if ($_data->title_type=="Y" && file_exists($date_img)) {
			$text  = "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
			$text .= "<tr>\n";
			//$text .= "<td><img src=\"".$date_img."\" border=\"0\" alt=\"".$alt."\"></td>\n";
			$text .= "<td><img src=\"".$date_img."\" border=\"0\" ></td>\n";
			$text .= "</tr>\n";
			$text .= "</table>\n";
		} elseif(file_exists($def_img)) {	//파일이 존재할경우에만 나타나게 수정 by.jyh
			$text  = "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
			$text .= "<tr>\n";
			$text .= "<td><img src=\"".$def_img."\" border=\"0\" alt=\"".$alt."\"></td>\n";
			$text .= "</tr>\n";
			$text .= "</table>\n";
		}
	}
	return $text;
}

/* jyh 함수 모음 끝 */



function checkNaverEp(){
	//$engine_num = "naver"; //엔진페이지 번호
	$filename = DirPath.DataDir."shopimages/etc/naver.db";
	$checkValue = false;
	if(file_exists($filename)==true){
		if($fp=@fopen($filename, "r")){
			$szdata=fread($fp, filesize($filename));
			fclose($fp);
			$checkValue=unserialize($szdata);
		}
	}
	return $checkValue;
}

function _basketCount($table="tblbasket",$tempkey=null){
	$tablename = trim($table);
	$basketkey = trim($tempkey);
	$rowcount= 0;

	if(strlen($basketkey)>0){
		$countSQL = "SELECT COUNT(tempkey) AS rowcount FROM ".$tablename." WHERE tempkey = '".$basketkey."' AND (tempkey IS NOT NULL OR tempkey != '')";
		$countRes = @mysql_query($countSQL,get_db_conn());
		$rowcount = @mysql_result($countRes,0,0);
		mysql_free_result($countRes);
	}
	return $rowcount;
}








/***********************
/* 네임텍 사용 유무
/*
************************/
function nameTechUse( $vender ){

	$use = false;

	$shopRow = mysql_fetch_assoc (mysql_query("SELECT `nametech_use` FROM `shop_more_info` ",get_db_conn()));
	if( $shopRow[nametech_use] == 1 ) {
		$use = true;
		if( strlen( $vender ) > 0 ) {
			$venderRow = mysql_fetch_assoc (mysql_query("SELECT `com_nametech` FROM  `tblvenderinfo` WHERE `vender` =".$vender,get_db_conn()));
			if( $venderRow[com_nametech] == 1 ) {
				$use = true;
			} else{
				$use = false;
			}
		} else{
			$use = false;
		}
	}

	return $use;
}








// 예약상품 아이콘 추가
// 예약상품의 경우 상품명 아이콘 리스트 맨 앞에 예약상품 아이톤을 추가 함
function reservationEtcType($reservation,$etctype){
	if( strlen($reservation) > 0 AND $reservation != '0000-00-00' AND strtotime($reservation) > strtotime(date("Ymd")) ) {
		if( eregi("ICON=", $etctype) ) {
			if( !eregi("ICON=29", $etctype) ) {
				$etctype = str_replace("ICON=","ICON=29",$etctype);
			}
		} else{
			$etctype = "ICON=29".$etctype;
		}
	}
	return $etctype;
}


//모바일 간편 회원 가입할 경우 첫주문에서 주소, 연락처, 자동입력처리
function setAutoAddMemInfo($param){
	if(is_array($param)){
		$info_id = !_empty($param['id'])?trim($param['id']):"";
		$info_tel = !_empty($param['tel'])?trim($param['tel']):"";
		$info_phone = !_empty($param['phone'])?trim($param['phone']):"";
		$info_post = !_empty($param['post'])?trim($param['post']):"";
		$info_address = !_empty($param['addr'])?trim($param['addr']):"";
		
		$checkIdSQL = "SELECT id, mobile, home_post, home_tel , home_addr FROM tblmember WHERE devices='M' AND id = '".$info_id."' ";

		if(false !== $checkIdRes = mysql_query($checkIdSQL,get_db_conn())){
			$removestring =array("=","\n","\r");
			$checkID = trim(mysql_result($checkIdRes,0,0));
			$checkMobile = trim(mysql_result($checkIdRes,0,1));
			$checkPost = trim(mysql_result($checkIdRes,0,2));
			$checkTel = trim(mysql_result($checkIdRes,0,3));
			$checkAddress = preg_replace($removestring,"",mysql_result($checkIdRes,0,4));

			if(strlen($checkID)>0 && strlen($checkMobile) <=0 && strlen($checkPost) <=0 && strlen($checkTel) <=0 && strlen($checkAddress)<=0){

				$updateInfoSQL = "UPDATE tblmember SET ";
				$updateInfoSQL .= "mobile = '".$info_phone."', ";
				$updateInfoSQL .= "home_tel = '".$info_tel."', ";
				$updateInfoSQL .= "home_post = '".$info_post."', ";
				$updateInfoSQL .= "home_addr = '".$info_address."' ";
				$updateInfoSQL .= "WHERE id = '".$checkID."' ";
				@mysql_query($updateInfoSQL,get_db_conn());
			}

			@mysql_free_result($checkIdRes);
		}
	}
}

function calcSetBankinfo(){
	$bankinfoArray = array(
		"001"=>"한국은행",
		"002"=>"산업은행",
		"003"=>"기업은행",
		"004"=>"국민은행",
		"005"=>"외환은행",
		"007"=>"수협중앙회",
		"008"=>"수출입은행",
		"011"=>"농협중앙회",
		"012"=>"농협회원조합",
		"020"=>"우리은행",
		"023"=>"SC제일은행",
		"027"=>"한국씨티은행",
		"031"=>"대구은행",
		"032"=>"부산은행",
		"034"=>"광주은행",
		"035"=>"제주은행",
		"037"=>"전북은행",
		"039"=>"경남은행",
		"045"=>"새마을금고연합회",
		"048"=>"신협중앙회",
		"050"=>"상호저축은행",
		"052"=>"모건스탠리은행",
		"054"=>"HSBC은행",
		"055"=>"도이치은행",
		"056"=>"에이비엔암로은행",
		"057"=>"제이피모간체이스은행",
		"058"=>"미즈호코퍼레이트은행",
		"059"=>"미쓰비시도쿄UFJ은행",
		"060"=>"BOA",
		"071"=>"정보통신부 우체국",
		"076"=>"신용보증기금",
		"077"=>"기술신용보증기금",
		"081"=>"하나은행",
		"088"=>"신한은행",
		"093"=>"한국주택금융공사",
		"094"=>"서울보증보험",
		"095"=>"경찰청",
		"099"=>"금융결제원",
		"209"=>"동양종합금융증권",
		"218"=>"현대증권",
		"230"=>"미래에셋증권",
		"238"=>"대우증권",
		"240"=>"삼성증권",
		"243"=>"한국투자증권",
		"247"=>"우리투자증권",
		"261"=>"교보증권",
		"262"=>"하이투자증권",
		"263"=>"에이치엠씨투자증권",
		"264"=>"키움증권",
		"265"=>"이트레이드증권",
		"266"=>"에스케이증권",
		"267"=>"대신증권",
		"268"=>"솔로몬투자증권",
		"269"=>"한화증권",
		"270"=>"하나대투증권",
		"278"=>"굿모닝신한증권",
		"279"=>"동부증권",
		"280"=>"유진투자증권",
		"287"=>"메리츠증권",
		"289"=>"엔에이치투자증권",
		"290"=>"부국증권"
	);
	return $bankinfoArray;
}
?>