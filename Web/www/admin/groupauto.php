<?
@set_time_limit(0);
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/ext/func.php");


// 자동 등급 변경 설정 정보
$asql = "select * from extra_conf where type='autogroup'";
if(false !== $ares = mysql_query($asql,get_db_conn())){
	$auto = array();
	while($trow = mysql_fetch_assoc($ares)){
		$auto[$trow['name']] = $trow['value'];
	}
}
if(!_array($auto)) exit;

$endDate = strtotime(date('Y-m-'.$auto['rangestart']));  // 합산 기준 일자

if($auto['rangestart'] >= date('d')) $endDate = strtotime('-1 month',$endDate); // 합산종료일
$startDate = strtotime('-'.$auto['rangemonth'].' month',$endDate); // 합산시작일

//echo "합산시작일:".date("Y-m-d",$startDate)."<br>";
//echo "합산종료일:".date("Y-m-d",$endDate)."<br>";

$where = array();
array_push($where,"o.ordercode>='".date('Ymd',$startDate)."000000'"); // 합산시작일
array_push($where,"o.ordercode<='".date('Ymd',$endDate)."235959'"); // 합산종료일
array_push($where,"deli_gbn = 'Y'"); // 합산조건 - 배송완료


// 그룹 리스트 ===========================================
$groupList = array();
$excpgroupList = array();
$excpgroupCodes = array();
$groupOrderType_SQL = "SELECT `value` FROM `extra_conf` WHERE `type` = 'groupconf' AND `name` = 'group_order_type' LIMIT 1 ; ";
$groupOrderType_RES =  mysql_query($groupOrderType_SQL,get_db_conn());
$groupOrderType_ROW = mysql_fetch_assoc($groupOrderType_RES);
$groupOrderType = $groupOrderType_ROW['value'];

switch( $groupOrderType ){
	case '1' : // 금액만 충족
		$groupList_SQL_WHERE = "WHERE group_order_price > 0 or group_excp_auto='Y'";
		$groupList_SQL_ORDERBY = "ORDER BY group_order_price DESC";
		//echo "구매금액 기준";
		break;
	case '2' : // 건수만 충족
		$groupList_SQL_WHERE = "WHERE group_order_cnt > 0  or group_excp_auto='Y'";
		$groupList_SQL_ORDERBY = "ORDER BY group_order_cnt DESC";
		//echo "구매건수 기준";
		break;
	case '3' : // 금액 && 건수 충족
		$groupList_SQL_WHERE = "WHERE (group_order_price > 0 AND group_order_cnt > 0)  or group_excp_auto='Y' ";
		$groupList_SQL_ORDERBY = "ORDER BY group_order_price DESC, group_order_cnt DESC";
		//echo "금액 AND 건수 기준 (금액 우선)";
		break;
	case '4' : // 금액 || 건수 충족
		$groupList_SQL_WHERE = " where group_order_price > 0 or group_order_cnt > 0  or group_excp_auto='Y'";
		$groupList_SQL_ORDERBY = "ORDER BY group_order_price DESC, group_order_cnt DESC";
		//echo "금액 OR 건수 기준 (금액 우선)";
		break;
	default :
		echo "[ERROR] 누적 금액/구매건수 적용 조건 누락";
		exit;
}
//echo "<br />";

$groupList_SQL = "SELECT group_code, group_order_price, group_order_cnt, group_order_type, use_auto_coupon, groupCouponSendType, group_iossreserve, groupCouponSendType_M, groupCouponSendType_D,group_excp_auto FROM tblmembergroup ".$groupList_SQL_WHERE." ".$groupList_SQL_ORDERBY." ";
$groupList_RESULT = mysql_query($groupList_SQL,get_db_conn());

while ( $groupList_ROW = mysql_fetch_assoc($groupList_RESULT) ){
	if($groupList_ROW['group_excp_auto'] == 'Y'){
		array_push($excpgroupList,$groupList_ROW);
		array_push($excpgroupCodes,$groupList_ROW['group_code']);
	}else{
		array_push($groupList,$groupList_ROW);
	}
}


// 자동 등급 변경 ====================================================
$runAutoGroupSchedule = true;

// 갱신 대상일 설정
$updateStamp = strtotime(date('Y-m-').$auto['upday']);

if(date('d') < $auto['upday']) $updateStamp = strtotime('-1 month'.$updateStamp); // 승급 처리 일 자 보정	

// 실행 여부 판별
if($auto['runtype'] == 'user'){	
	$runAutoGroupSchedule = true;
	if(!_empty($auto['lastrun']) && preg_match('/^(2[0-9]{3})-([0-9]{2})-([0-9]{2})$/',$auto['lastrun'],$mat)){
		if(checkdate($mat[2],$mat[3],$mat[1]) && $auto['lastrun'] <= date('Y-m-d') && strtotime($auto['lastrun']) >= $endDate){
			$runAutoGroupSchedule = false;
		}
	}
}else if(date('d') != $auto['upday']){
	$runAutoGroupSchedule = false;
}

if($runAutoGroupSchedule === true){
	// 해당 그룹 적용대상 회원 리스트 추가
	foreach($groupList as $gKey => $group){
		// 그룹이 최상위 레벨 그룹 일경우 true
		$endChk = ( empty($groupList[$gKey-1]) )?true:false;

		// 등급 검색 조건
		$having = "";
		switch( $groupOrderType ){
			case '1' : // 금액만 충족
				$having .= " having sum(o.price) >= ".($group['group_order_price']).( $endChk ? "" : " AND sum(o.price) < ".($groupList[$gKey-1]['group_order_price']) );
				break;
			case '2' : // 건수만 충족
				$having .= " having count(o.ordercode) >= ".($group['group_order_cnt']).($endChk ? "" : " AND count(o.ordercode) < ".($groupList[$gKey-1]['group_order_cnt']) );
				break;
			case '3' : // 금액 && 건수 충족
				$having .= " having ( sum(o.price) >= ".($group['group_order_price']).($endChk ? "" : " AND sum(o.price) < ".($groupList[$gKey-1]['group_order_price']))." ) AND ( count(o.ordercode) >= ".($group['group_order_cnt']).($endChk ? "" : " AND count(o.ordercode) < ".($groupList[$gKey-1]['group_order_cnt']))." ) ";
				break;
			case '4' : // 금액 || 건수 충족
				$having .= " having ( sum(o.price) >= ".($group['group_order_price']).($endChk ? "" : " AND sum(o.price) < ".($groupList[$gKey-1]['group_order_price']))." ) OR ( count(o.ordercode) >= ".($group['group_order_cnt']).($endChk ? "" : " AND count(o.ordercode) < ".($groupList[$gKey-1]['group_order_cnt']))." ) ";
				break;
		}


	//	$memSql = "SELECT m.id, m.group_code as memGroup, '".$group['group_code']."' as setGroup, ( SELECT l.changedate FROM autogroup_log as l WHERE l.currentclass = m.group_code AND l.id = m.id ORDER BY l.changedate DESC LIMIT 1 ) as updates FROM tblmember as m, ( SELECT o.id FROM tblorderinfo o WHERE ".implode(' and ',$where)." GROUP BY o.id ".$having." ) as s WHERE m.group_code not in ('".implode("','",array_merge(array($group['group_code'])))."') AND s.id=m.id AND m.member_out != 'Y'";
		$memSql = "SELECT m.id, m.group_code as memGroup, '".$group['group_code']."' as setGroup, ( SELECT l.changedate FROM autogroup_log as l WHERE l.currentclass = m.group_code AND l.id = m.id and l.classstate !='C' ORDER BY l.changedate DESC LIMIT 1 ) as updates FROM tblmember as m, ( SELECT o.id FROM tblorderinfo o WHERE ".implode(' and ',$where)." GROUP BY o.id ".$having." ) as s WHERE s.id=m.id AND m.member_out != 'Y' ";
		
		if(_array($excpgroupCodes)) $memSql .=  " and m.group_code not in ('".implode("','",$excpgroupCodes)."')";			
		$memRes = mysql_query($memSql,get_db_conn());
		if(mysql_num_rows($memRes) < 1) continue;
		
		while($mem = mysql_fetch_assoc($memRes)){			
			// 변경등급 유지기간
			$saveDate = 0;
			if(!_empty($mem['updates'])){
				$saveDate = strtotime('+'.$auto['keepclass'].' month',strtotime($mem['updates']));
			}
			
			// 현재 그룹 레벨 코드 ( -1 : error, 0부터 ~ )
			$memGroupCodeKey=-1;

			foreach($groupList as $gKeyTemp => $groupTemp){				
				if(array_search($mem['memGroup'],$groupList[$gKeyTemp]) ) $memGroupCodeKey = $gKeyTemp;
			}			
			$chg = false;
			/*
			// 등급 업
			if( $memGroupCodeKey > $gKey) { // 상위레벨로만 등급업
				$chg = true;
				$classstate = "U";
			}

			// 등급 유지
			if( $saveDate < strtotime(date("Ymd")) AND $memGroupCodeKey == $gKey ){
				// 회원등급 변경
				$memberUpSQL = "UPDATE tblmember SET changedate = now() WHERE currentclass = '".$group['group_code']."' AND id = '".$mem['id']."' ";
				mysql_query($memberUpSQL,get_db_conn());
			}

			// 등급 다운
			if( $saveDate < strtotime(date("Ymd")) AND $memGroupCodeKey < $gKey ){ // 등급유지기간 - 등급 다운 기준
				$chg = true;
				$classstate = "D";
			}
			*/
			if($memGroupCodeKey == -1){
				$chg = true;
				$classstate = "F";
			}else if( $memGroupCodeKey > $gKey) { // 상위레벨로만 등급업
				$chg = true;
				$classstate = "U";
			}else if($memGroupCodeKey == $gKey){ // 등급 변동 없을 경우
				echo 'same';
				/*
				$logSQL = "INSERT INTO autogroup_log SET  beforeclass = '".$mem['memGroup']."', currentclass = '".$group['group_code']."', id = '".$mem['id']."', classstate = 'S', changedate = '".date('Y-m-d H:i:s',$updateStamp)."' ";				
				//$memberUpSQL = "UPDATE autogroup_log SET changedate = '".date('Y-m-d H:i:s',$updateStamp)."' WHERE currentclass = '".$group['group_code']."' AND id = '".$mem['id']."' ";
				mysql_query($memberUpSQL,get_db_conn());*/
			}else if($saveDate >= $updateStamp){ // 등급 유지기간 적용
				echo 'alive';
			}else{ // 작을 경우	$memGroupCodeKey < $gKey			
				$chg = true;
				$classstate = "D";
			}			
			
			if($chg){
				// 해당 등급 적립금/쿠폰 발행 - 로그 참조 지급
				$memLogSQL = "SELECT count(*) FROM autogroup_log WHERE currentclass = '".$group['group_code']."' AND id = '".$mem['id']."' ";
				$logCnt = '0';
				if(false !== $logRes = mysql_query($memLogSQL,get_db_conn())){
					$logCnt = mysql_result($logRes,0,0);
				}

				// 등급 로그
				//$logSQL = "INSERT INTO autogroup_log SET  beforeclass = '".$mem['memGroup']."', currentclass = '".$group['group_code']."', id = '".$mem['id']."', classstate = '".$classstate."', changedate = now() ";
				$logSQL = "INSERT INTO autogroup_log SET  beforeclass = '".$mem['memGroup']."', currentclass = '".$group['group_code']."', id = '".$mem['id']."', classstate = '".$classstate."', changedate = '".date('Y-m-d H:i:s',$updateStamp)."' ";
				
				mysql_query($logSQL,get_db_conn());

				// 회원등급 변경
				$memberUpSQL = "UPDATE tblmember SET group_code = '".$group['group_code']."' WHERE id = '".$mem['id']."' ";				
				$memberUpOK = mysql_query($memberUpSQL,get_db_conn());

				//echo "<br />[".$mem['group_code']."=>".$group['group_code']."] 등급 ".($classstate=="U"?"업":"다운")." 적용 회원 : ".$mem['id'];


				// 적립금 / 쿠폰 발급
				if( false !== $memberUpOK AND $logCnt == 0 ){ // on gift

					// 적립금지급
					if(intval($group['group_iossreserve']) > 0 ){
						$reserveSQL = "UPDATE tblmember SET reserve = reserve+".intval($group['group_iossreserve'])." WHERE id ='".$mem['id']."' ";
						$reserveOK = mysql_query($reserveSQL,get_db_conn());
						if($reserveOK){
							// 적립금 지급 히스토리
						//	$reserveLogSQL = "INSERT INTO tblreserve SET id = '".$mem['id']."', reserve = '".$group['group_iossreserve']."', reserve_yn = 'Y', content = '승급(".$group['group_code'].")에 따른 지급', date = '".date('YmdHis')."' ";
							$reserveLogSQL = "INSERT INTO tblreserve SET id = '".$mem['id']."', reserve = '".intval($group['group_iossreserve'])."', reserve_yn = 'Y', content = '승급(".$group['group_code'].")에 따른 지급', date = '".date('YmdHis',$updateStamp)."' ";
							mysql_query($reserveLogSQL,get_db_conn());
							//echo " (적립금 지급)";
						}
					}

					// 쿠폰지급 - 최초 1회 지급일 경우에만
					if( $group['groupCouponSendType'] == 1 AND $group['use_auto_coupon'] == 1 ){

						$couponSQL = "SELECT * FROM group_coupon AS GC inner join tblcouponinfo AS CI ON GC.coupon_code = CI.coupon_code WHERE GC.group_code = '".$group['group_code']."' ";
						$couponResult = mysql_query($couponSQL,get_db_conn());
						while($couponRow = mysql_fetch_assoc($couponResult)){
							/*
							$date = date("YmdHis");
							if( $couponRow['date_start'] > 0 ) {
								$date_start = $couponRow['date_start'];
								$date_end = $couponRow['date_end'];
							} else {
								$date_start = substr($date,0,10);
								$date_end = date("Ymd",mktime(0,0,0,substr($date,4,2),substr($date,6,2)+abs($couponRow['date_start']),substr($date,0,4)))."23";
							}*/
							if( $couponRow['date_start'] > 0 ) {
								$date_start = $couponRow['date_start'];
								$date_end = $couponRow['date_end'];
							} else {
								$date_start = substr($date,0,10);
								$date_end = date("Ymd23",strtotime('+'.abs($couponRow['date_start']).' day',$updateStamp));
							}
							

							// 선 발급 여부
							$isCouponSQL = "SELECT * FROM `tblcouponissue` WHERE `coupon_code` = '".$couponRow['coupon_code']."' AND `id` = '".$mem['id']."' ;";
							$isCouponResult = mysql_query($isCouponSQL,get_db_conn());
							$isCouponRow = mysql_fetch_assoc ( $isCouponResult );

							if( mysql_num_rows( $isCouponResult ) ){
								$saveCouponSQL = "UPDATE `tblcouponissue` SET `used` = 'N', `date_start` = '".$date_start."', `date_end` = '".$date_end."' WHERE `coupon_code` = '".$couponRow['coupon_code']."' AND `id` = '".$mem['id']."' ; ";
							} else{
							//	$saveCouponSQL = "INSERT `tblcouponissue` SET `used` = 'N', `date_start` = '".$date_start."', `date_end` = '".$date_end."', `date` = '".$date."', `coupon_code` = '".$couponRow['coupon_code']."', `id` = '".$mem['id']."' ;";
								$saveCouponSQL = "INSERT `tblcouponissue` SET `used` = 'N', `date_start` = '".$date_start."', `date_end` = '".$date_end."', `date` = '".date("YmdHis",$updateStamp)."', `coupon_code` = '".$couponRow['coupon_code']."', `id` = '".$mem['id']."' ;";
							
							}
							mysql_query($saveCouponSQL,get_db_conn());

							// 회원 쿠폰지급 기록
							//$logSQL = "INSERT INTO autogroup_log SET  beforeclass = '', currentclass = '".$group['group_code']."', id = '".$mem['id']."', classstate = 'C', changedate = now() ";
							$logSQL = "INSERT INTO autogroup_log SET  beforeclass = '', currentclass = '".$group['group_code']."', id = '".$mem['id']."', classstate = 'C', changedate = '".date('Y-m-d H:i:s',$updateStamp)."'  ";
							mysql_query($logSQL,get_db_conn());

						} // end while
					} // end if
				} // on Change
				
			} 

		} // end mem while loop		
		//_pr($groupList);
	} // end group foreach loop
	

	// 자동 쿠폰 발급 ====================================================
	// 해당 그룹 적용대상 회원 리스트
	// 해당 그룹 적용대상 회원 리스트 추가
	//_pr($groupList);

	foreach($groupList as $gKey => $group){
		if($group['groupCouponSendType'] == 2 AND $group['use_auto_coupon'] == 1){			
			$memSql = "SELECT m.id, m.group_code, ( SELECT l.changedate FROM autogroup_log as l WHERE l.currentclass = m.group_code AND l.id = m.id and l.classstate='C' ORDER BY l.changedate DESC LIMIT 1 ) as updates FROM tblmember as m WHERE m.group_code = '".$group['group_code']."' AND m.member_out != 'Y'";
			$memRes = mysql_query($memSql,get_db_conn());
			while($mem = mysql_fetch_assoc($memRes)){				
				if(_empty($mem['updates']) || substr($mem['updates'],0,10) < date('Y-m-d',$updateStamp)){
					// 회원 쿠폰지급 기록
					$logSQL = "INSERT INTO autogroup_log SET  beforeclass = '', currentclass = '".$group['group_code']."', id = '".$mem['id']."', classstate = 'C', changedate = '".date('Y-m-d H:i:s',$updateStamp)."'";
					mysql_query($logSQL,get_db_conn());

					//쿠폰 지급
					$couponSQL = "SELECT * FROM group_coupon AS GC inner join tblcouponinfo AS CI ON GC.coupon_code = CI.coupon_code WHERE GC.group_code = '".$group['group_code']."' ";
					$couponResult = mysql_query($couponSQL,get_db_conn());
					while($couponRow = mysql_fetch_assoc($couponResult)){
						if($couponRow['date_start'] > 0){
							$date_start = $couponRow['date_start'];
							$date_end = $couponRow['date_end'];
						} else {
							$date_start = substr($date,0,10);
						//	$date_end = date("Ymd",mktime(0,0,0,substr($date,4,2),substr($date,6,2)+abs($couponRow['date_start']),substr($date,0,4)))."23";
							$date_end = date("Ymd23",strtotime('+'.abs($couponRow['date_start']).' day',$updateStamp));
						}

						// 선 발급 여부
						$isCouponSQL = "SELECT * FROM `tblcouponissue` WHERE `coupon_code` = '".$couponRow['coupon_code']."' AND `id` = '".$mem['id']."' ;";
						$isCouponResult = mysql_query($isCouponSQL,get_db_conn());
						$isCouponRow = mysql_fetch_assoc ( $isCouponResult );

						if( mysql_num_rows( $isCouponResult ) ){
							$saveCouponSQL = "UPDATE `tblcouponissue` SET `used` = 'N', `date_start` = '".$date_start."', `date_end` = '".$date_end."' WHERE `coupon_code` = '".$couponRow['coupon_code']."' AND `id` = '".$mem['id']."' ; ";
						} else{
							$saveCouponSQL = "INSERT `tblcouponissue` SET `used` = 'N', `date_start` = '".$date_start."', `date_end` = '".$date_end."', `date` = '".date("YmdHis",$updateStamp)."', `coupon_code` = '".$couponRow['coupon_code']."', `id` = '".$mem['id']."' ;";
						}
						mysql_query($saveCouponSQL,get_db_conn());
					}
				}
			}
		}
	}

	if(mysql_result(mysql_query("select count(*) from extra_conf where type='autogroup' and name='lastrun'",get_db_conn()),0,0) > 0){
		@mysql_query("update extra_conf set value='".date('Y-m-d')."' where type='autogroup' and name='lastrun'",get_db_conn());
	}else{
		@mysql_query("insert into extra_conf set type='autogroup',name='lastrun',value='".date('Y-m-d')."'",get_db_conn());
	}
} // end 변경등급 합산반영일

echo "작업완료!";
?>