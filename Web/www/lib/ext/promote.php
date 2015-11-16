<?


	/**
		SNS 홍보 적립금
	*/


	// 게시판 SNS 홍보 URL 접근 적립금 처리 ======================================================
	function snsPromoteBoard( $bcmt ){

		$reteun = array();

		$sql = "SELECT board, num, id, chk FROM tblsnsboard WHERE code='".$bcmt."' ";
		$result=mysql_query($sql,get_db_conn());
		if($row=mysql_fetch_object($result)) {
			$board=$row->board;
			$num=$row->num;
			$id=$row->id;
			$chk=$row->chk;
			$sql = "SELECT title, content FROM tblboard WHERE num='".$num."' ";
			$result2=mysql_query($sql,get_db_conn());
			if($row2=mysql_fetch_object($result2)) {
				$return['fbName'] = strip_tags($row2->title);
				$return['fbDesc'] = substr(strip_tags($row2->content),0, 300);
				$return['mainurl'] = BoardDir."board.php?pagetype=view&num=".$num."&board=".$board;

				// 적립금 처리
				if( ( $id != NULL OR $id != "" ) AND $chk == 0 ) {
					$extra_conf_sql = "SELECT `value` FROM `extra_conf` WHERE `type` = 'tblshopinfo' AND `name` = 'snsBoardReserve' ";
					$extra_conf_res = mysql_query($extra_conf_sql,get_db_conn());
					if ($extra_conf_row=mysql_fetch_object($extra_conf_res)) {
						$snsBoardReserve=$extra_conf_row->value;
						SetReserve($id,$snsBoardReserve,"게시판 SNS 홍보적립금 [".$bcmt."]");
						mysql_query("UPDATE `tblsnsboard` SET `chk`=1 WHERE code='".$bcmt."' ",get_db_conn());
					}
					mysql_free_result($extra_conf_res);
				}

				$return['err'] = "no";

			}else{
				$return['err'] = "noBoard";
			}
		}else{
			$return['err'] = "noHonbo";
		}

		return $return;

	}



	// 상품 SNS 홍보 URL 로 접근시 처리 =========================================================


	// 홍보 적립금 접근 기록
	function snsPromoteAccess ( $pk ) {

		global $_ShopInfo;

		$return['access'] = false;
		$return['pcode'] = "";
		$pkInfo = snsPromote_pkInfo($pk);

		if( $pkInfo['cnt'] > 0 ) { // 홍보정보가 있다면

			$return['access'] = true;
			$return['pcode'] = $pkInfo['pcode'];

			// 접근키 (기존 접근해있던 사용자는 기존키 사용 )
			if( strlen($_ShopInfo->authkey) == 0 ){
				$_ShopInfo->authkey = md5(uniqid(""));
				$_ShopInfo->Save();
			}

			$acsInfo = snsPromote_acsInfo( $_ShopInfo->authkey );

			$logSQL = "";
			if( empty($acsInfo['idx']) ){
				$logSQL = "INSERT `tblsnsproductLog` SET `code` = '".$pkInfo['code']."', `authkey` = '".$_ShopInfo->authkey."', `accessTime` = NOW() ; ";
			} else{
				if( strlen($_ShopInfo->getMemid()) > 0 ){
					$logSQL = "UPDATE `tblsnsproductLog` SET `memid` = '".$_ShopInfo->getMemid()."' WHERE `code` = '".$pkInfo['code']."' AND `authkey` = '".$_ShopInfo->authkey."' LIMIT 1 ; ";
				}
			}
			mysql_query($logSQL, get_db_conn());

			// 로그인된 회원 정보 업데이트
			snsPromoteAccessLogin( $_ShopInfo->authkey, $_ShopInfo->getMemid() );

			//접근 카운트 (홍보URL클릭수)
			mysql_query( "UPDATE tblsnsproduct SET count=count+1 WHERE code='".$pk."' LIMIT 1;" ,get_db_conn());
		}

		return $return;
	}





	// PK키 정보
	function snsPromote_pkInfo ( $pk ) {
		$sql = "SELECT *, count(*) as cnt FROM `tblsnsproduct` WHERE `code` = '".$pk."' LIMIT 1 ; ";
		$result=mysql_query($sql,get_db_conn());
		$row=mysql_fetch_assoc($result);
		return $row;
	}





	// 홍보 URL 접근 정보
	function snsPromote_acsInfo ( $authkey ) {
		$sql = "SELECT *, count(*) as cnt FROM `tblsnsproductLog` WHERE `authkey` = '".$authkey."' ; ";
		$result=mysql_query($sql,get_db_conn());
		$row=mysql_fetch_assoc($result);
		return $row;
	}






	// 홍보 URL 접근 후 로그인
	function snsPromoteAccessLogin( $authkey, $id = '' ){
		if( strlen($authkey) > 0 AND strlen($id) > 0 ) {
			$acsInfo = snsPromote_acsInfo( $authkey );
			$pkInfo = snsPromote_pkInfo($acsInfo['code']);
			$logSQL = "";
			if( $acsInfo['cnt'] > 0 AND $pkInfo['cnt'] > 0 AND strlen($id) > 0 ){
				if( $id == $pkInfo['id'] ){
					$logSQL = "DELETE FROM `tblsnsproductLog` WHERE `authkey` = '".$authkey."' ; ";
				} else{
					$logSQL = "UPDATE `tblsnsproductLog` SET `memid` = '".$id."' WHERE `authkey` = '".$authkey."' LIMIT 1 ; ";
				}
			}
			mysql_query($logSQL, get_db_conn());
		}
	}





	// 홍보 URL 접근 주문
	function snsPromoteAccessOrder( $authkey, $ordercode ){
		$acsInfo = snsPromote_acsInfo( $authkey );
		if( !empty($acsInfo['idx']) AND strlen($ordercode) > 0 ){
			$logSQL = "UPDATE `tblsnsproductLog` SET `ordercode` = '".$ordercode."', `orderTime` = NOW() WHERE `authkey` = '".$authkey."' AND `orderTime` = '0000-00-00 00:00:00' LIMIT 1 ; ";
			mysql_query($logSQL, get_db_conn());
		}
	}




	// 홍보 URL 접근 주문 정보 로 확인
	function snsPromoteOrderInfo( $ordercode ){
		$sql = "SELECT *, count(*) as cnt FROM `tblsnsproductLog` WHERE `ordercode` = '".$ordercode."' ; ";
		$result=mysql_query($sql,get_db_conn());
		$row=mysql_fetch_assoc($result);
		return $row;
	}





	// 홍보 URL 접근 주문 완료 (회원적립처리 정보)
	function snsPromoteAccessOrderOK( $ordercode ){

		$orderInfo = snsPromoteOrderOkInfo ( $ordercode );

		// 홍보자 적립
		if( $orderInfo['rsvA'] ){
			SetReserve($orderInfo['pkId'],$orderInfo['pkRsv'],$orderInfo['memId']."님이 SNS홍보를 통한 상품구매적립.");
		}

		// 구매자 적립
		if( $orderInfo['rsvB'] ){
			SetReserve($orderInfo['memId'],$orderInfo['memRsv'],$orderInfo['pkId']."님의 SNS홍보를 통한 구매적립금.");
		}

		// 완료 처리
		$logSQL = "UPDATE `tblsnsproductLog` SET `orderOkTime` = NOW() WHERE `ordercode` = '".$ordercode."' AND `orderOkTime` = '0000-00-00 00:00:00' LIMIT 1 ; ";
		mysql_query($logSQL, get_db_conn());

	}






	// 홍보 URL 적립 가능 정보
	function snsPromoteOrderOkInfo ( $ordercode ){

		$return = array();
		$return['rsvA'] = false;
		$return['rsvB'] = false;

		if( strlen($ordercode) > 0 ){

			$arSns = snsReserveInfo();

			// 홍보 상품 주문 정보
			$promot = snsPromoteOrderInfo( $ordercode );
			$pkInfo = snsPromote_pkInfo($promot['code']);
			if( $arSns[7] == "Y" AND $arSns[0] != "N" AND $pkInfo['cnt'] > 0 ) {

				$return['pkId'] = $pkInfo['id']; //  상품 홍보인
				$return['memId'] = $promot['memid']; // 상품 구매인

				$return['pkRsv'] = $arSns[5]; // 상품 홍보인 적립금
				$return['memRsv'] = $arSns[6]; // 상품 구매인 적립금

				// 홍보자 적립
				if( strlen($pkInfo['id']) > 0 AND $arSns[5] > 0 ) {
					if( $arSns[3] == "O" ){ // 1회 적립
						$return['rsvA'] = ( $promot['cnt'] == 1 ? true : false ) ;
					} elseif($arSns[3] == "A"){ // 지속적립
						$return['rsvA'] = true;
					}
				}

				// 구매자 적립
				if( strlen($promot['memid']) > 0 AND $arSns[6] > 0 ) {
					if( $arSns[4] == "O" ){ // 1회 적립
						$return['rsvB'] = ( $promot['cnt'] == 1 ? true : false ) ;
					} elseif($arSns[4] == "A"){ // 지속적립
						$return['rsvB'] = true;
					}
				}
			}
		}

		return $return;

	}



	// 적립금 관련 정보
	function snsReserveInfo () {
		global $_data;
		/*
			sns_reserve_type
			$arSnsType[n]
			n : 설명
			0 : [N]적립금 미지급 [A]전체상품 일괄적용 [B]각 상품별 적립금설정기준을 따름
			1 : 상품 홍보인 적립 타입 [N]적립금(￦) [Y]적립률(%)
			2 : 상품 구매인 적립 타입 [N]적립금(￦) [Y]적립률(%)
			3 : 상품 홍보인 적립 지급 타입 [O]1회지급 [A]지속지급
			4 : 상품 구매인 적립 지급 타입 [O]1회지급 [A]지속지급
			5 : 상품 홍보인 적립금
			6 : 상품 구매인 적립금
		*/
		$arSnsType = explode("",$_data->sns_reserve_type);
		$arSnsType[5]=$_data->sns_recomreserve; // 상품 홍보인 적립금
		$arSnsType[6]=$_data->sns_memreserve; // 상품 구매인 적립금
		$arSnsType[7]=$_data->sns_ok; // SNS 사용 유무

		return $arSnsType;
	}





























	/**
		추천인 적립금
	*/

	// 관리자 - 추천인 제도 설정 저장
	// $arr 배열일 경우 저장 빈값이면 출력 리턴
	function recommandSetting( $arr = '' ){

		if( is_array($arr) ) {

			// 저장
			$orgMemRecommandReserve=trim($arr["orgMemRecommandReserve"]);
				$orgMemRecommandReserve=( strlen($orgMemRecommandReserve) > 0 ) ? $orgMemRecommandReserve : 0;
			$orgMemRecommandType = ( $arr["orgMemRecommandReserveType1"] == "join" ) ? $arr["orgMemRecommandReserveType1"] : $arr["orgMemRecommandReserveType2"];
			$newMemRecommandReserve=trim($arr["newMemRecommandReserve"]);
				$newMemRecommandReserve=( strlen($newMemRecommandReserve) > 0 ) ? $newMemRecommandReserve : 0;

			// 가입 추천한 회원 적립금
			recommandSettingDb( 'orgMemRecommandReserve', $orgMemRecommandReserve );

			// 가입 추천한 회원 적립 지급 타입
			recommandSettingDb( 'orgMemRecommandType', $orgMemRecommandType );

			// 추천받아 가입한 회원 적립금
			recommandSettingDb( '', $newMemRecommandReserve );

		} else{

			// 출력
			$reteun = array();
			$extra_conf_res = mysql_query("SELECT `value`, `name` FROM `extra_conf` WHERE `type` = 'recommandReserve' ",get_db_conn());
			while ($extra_conf_row=mysql_fetch_object($extra_conf_res)) {
				$reteun[$extra_conf_row->name] = $extra_conf_row->value;
			}
			mysql_free_result($extra_conf_res);
			return $reteun;

		}

	}

	// DB에 설정값 저장 - extra_conf
	function recommandSettingDb ( $k, $v ){
		if( !empty($k) AND !empty($v) ) {
			${$k} = $v;
			$extra_conf_res = mysql_query("SELECT `value` FROM `extra_conf` WHERE `type` = 'recommandReserve' AND `name` = '".$k."' ",get_db_conn());
			if( mysql_num_rows($extra_conf_res) ) {
				$extra_conf_up_sql = "UPDATE `extra_conf` SET `value`='".${$k}."' WHERE `type` = 'recommandReserve' AND `name` = '".$k."' ";
			} else{
				$extra_conf_up_sql = "INSERT `extra_conf` SET `type` = 'recommandReserve', `name` = '".$k."', `value`='".${$k}."' ";
			}
			$extra_conf_up_res = mysql_query($extra_conf_up_sql,get_db_conn());
			mysql_free_result($extra_conf_res);
			mysql_free_result($extra_conf_up_res);
		}
	}



	// 회원 가입시 처리 내용
	function recommandJoin( $arr = "" ){

		if( is_array($arr) ) {

			$date=date("YmdHis");

			$get = recommandSetting(); // 정보호출


			// 추천한 회원 적립
			$orgMemRecommandReserveResult = 0;
			if( $get['orgMemRecommandType'] == "join" AND $get['orgMemRecommandReserve'] > 0 ) {

				SetReserve($arr['recomMem'],$get['orgMemRecommandReserve'],"홍보URL을 통한 ".$arr['newMeme']."님의 가입 홍보적립금.");

				$orgMemRecommandReserveResult = $get['orgMemRecommandReserve'];

				// 로그
				recomenLog( $arr['recomMem'], "recomMem", $orgMemRecommandReserveResult );
			}


			// 추천 받아 가입한 회원 즉시 적립
			$newMemRecommandReserveResult = 0;
			if( $get['newMemRecommandReserve'] > 0 ) {

				SetReserve($arr['newMeme'],$get['newMemRecommandReserve'],$arr['recomMem']."님의 추천으로 가입 추가적립금.");

				$newMemRecommandReserveResult = $get['newMemRecommandReserve'];

				// 추천인 추천으로 가입한 카운트
				$rec_cnt=0;
				$rec_result = mysql_query( "SELECT rec_cnt FROM tblrecommendmanager WHERE rec_id='".$arr['recomMem']."'" ,get_db_conn());
				if($rec_row = mysql_fetch_object($rec_result)) {
					$rec_cnt = (int)$rec_row->rec_cnt;
				}
				mysql_free_result($rec_result);
				if($rec_cnt>0) {	//update
					$sql2 = "UPDATE tblrecommendmanager SET rec_cnt=rec_cnt+1 WHERE rec_id='".$arr['recomMem']."' ";
				} else {			//insert
					$sql2 = "INSERT tblrecommendmanager SET rec_id		= '".$arr['recomMem']."', rec_cnt	= '1', date		= '".$date."' ";
				}
				mysql_query($sql2,get_db_conn());


				// (기존)발행기록 카운트 이걸로 하는듯...없으면 안될듯...
				$sql2 = "INSERT tblrecomendlist SET ";
				$sql2.= "rec_id			= '".$arr['recomMem']."', ";
				$sql2.= "id				= '".$arr['newMeme']."', ";
				$sql2.= "rec_id_reserve	= '".$orgMemRecommandReserveResult."', ";
				$sql2.= "id_reserve		= '".$newMemRecommandReserveResult."', ";
				$sql2.= "date			= '".$date."' ";
				mysql_query($sql2,get_db_conn());

				// 로그
				recomenLog( $arr['newMeme'], "joinMem", $newMemRecommandReserveResul );

			}

		}

	}


	// 추천한 회원 적립 (상품 구매 완료시)
	function recommandMemReserve ( $memid ){

		$get = recommandSetting(); // 정보호출

		// 회원의 추천인
		$getMemResult = mysql_query( "SELECT rec_id FROM tblmember WHERE id = '".$memid."' ; ",get_db_conn());
		$getMemRow = mysql_fetch_assoc($getMemResult);

		//_pr($getMemRow);
		if( strlen($getMemRow['rec_id']) ) {

			$log = recomenLog($getMemRow['rec_id']);

			//_pr($get);
			//_pr($log);

			$reserveOK = false;

			if ( $get[orgMemRecommandType] == "orderA" ) { // 최초 1회만
				if( $log['result']['cnt'] == 0  ) { $reserveOK = true; }
			} else if ( $get[orgMemRecommandType] == "orderB" ) { // 구매시 마다 (지속적립)
				$reserveOK = true;
			}
			if( $reserveOK ) {
				// 지급
				SetReserve( $getMemRow['rec_id'], $get['orgMemRecommandReserve'], "[홍보적립]".$memid."님이 상품구매완료." );
				// 로그
				$chk = recomenLog( $getMemRow['rec_id'], "orderEnd", $get['orgMemRecommandReserve'] );
				//_pr($chk);
			}

		}
		//exit;
	}


	// 추천 적립 로그 작성
	function recomenLog ( $memId, $type = '', $reserve = 0 ) {

		$return = array();

		if ( empty($memId) ){
			$return['error'] = "회원아이디 누락";
		} else {
			if( strlen($type) > 0 ) {
				// 로그 저장
				if ( $reserve > 0 ) {
					mysql_query("INSERT tblrecomenLog SET memId = '".$memId."', reserve = '".$reserve."', type = '".$type."'; ", get_db_conn());
					$return['result'] = "저장완료";
				} else {
					$return['error'] = "적립금 에러(0원)";
				}
			} else {
				// 로그 호출
				$getLogSQL = "SELECT * FROM  tblrecomenLog WHERE memId = '".$memId."' ; ";
				$getLogResult = mysql_query($getLogSQL,get_db_conn());
				$return['result']['cnt'] = mysql_num_rows($getLogResult);
				while ( $getLogRow = mysql_fetch_assoc( $getLogResult ) ) {
					$return['result'][$getLogRow['idx']]['id'] = $getLogRow['memId'];
					$return['result'][$getLogRow['idx']]['reserve'] = $getLogRow['reserve'];
					$return['result'][$getLogRow['idx']]['type'] = $getLogRow['type'];
				}
			}
		}
		return $return;
	}

?>