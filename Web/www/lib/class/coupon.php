<?

/**
* 쿠폰 클레스
*/
class coupon{
	var $attr = array();
	var $code = '';
	var $imgpath = '';
	var $issueTypes = array("D"=>"삭제 쿠폰","M"=>"회원 가입시 발급","N"=>"즉시 발급용 쿠폰","Y"=>"쿠폰 클릭시 발급","O"=>"대량메일용오프라인쿠폰","P"=>"오프라인페이퍼쿠폰");
	//var $imagepath= $Dir.DataDir."shopimages/etc/";
	function coupon($code=NULL){
		$this->__construct($code);
	}

	function __construct($code=NULL){
		$this->imgpath = $GLOBALS['Dir'].DataDir."shopimages/etc/";
		if(!empty($code)){
			$sql = "select * from tblcouponinfo where coupon_code='".$code."' limit 1";
			$res = mysql_query($sql,get_db_conn());
			if($res){
				$this->attr = mysql_fetch_assoc($res);
				$this->code = $this->attr['coupon_code'];
			}else{
				// 예외 처리...
			}
		}else{
			$this->code = '';
			$this->attr = array();
		}
	}

	// 쿠폰 코드 패턴 확인
	function _checkCodePattern($coupon_code=NULL){
		return (!empty($coupon_code) && preg_match('/[0-9]+$/',$coupon_code));
	}

	//신규 쿠폰 코드 생성
	function _genCouponcode(){
		$chk = 0;
		do{
			$coupon_code = substr(ceil(date("sHi").date("ds")/10*8)."000",0,8);
			$result = mysql_query("select count(*) from tblcouponinfo where coupon_code='".$coupon_code."'",get_db_conn());
		}while($result && mysql_result($result,0,0) > 1);
		return $coupon_code;
	}

	// 오프라인 쿠폰 코드 생성
	function _genOffCode($couponLength, $couponString=""){
		$defaultString = "ABCDEFGHIJKLMNOPQRSTUVXYZ0123456789"; // 렌덤 문자 생성 범위
		srand((double)microtime()*1000000);

		if(empty($couponString)) $couponString = $defaultString;  //$couponString의 값이 정해지지 않았다면 $defaultString 값으로 사용
	 	$length = strlen($couponString);
	   	for($i=0;$i<$couponLength;$i++){
			 $couponStr = rand(0,$length-1); //0에서 $defaultString또는 $couponString의 길이사이의 난수를 구한다
			 $resultStr .= substr( $couponString, $couponStr, 1 );
		}
		return $resultStr;
	}

	// 오프라인쿠폰 코드 발급
	function _setOffCode($couponcode,$totalcnt){
		$result = array('result'=>false,'msg'=>'알수 없는 오류','codes'=>array());
		if(!empty($couponcode) || intval($totalcnt) < 1){
			$conn = get_db_conn();
			if($conn){
				for($i=0;$i<intval($totalcnt);$i++){
					do{
						$regen = false;
						$tcode = $this->_genOffCode(16);
						if(count($result['codes']) > 0 && in_array($tcode,$result['codes'])) $regen = true;
						else{
							$res = mysql_query("select count(*) from tblcouponissue_off where check_code='".$tcode."'",$conn);
							if($res){
								$regen = (mysql_result($res,0,0) > 0);
							}else{
								echo mysql_error();
								exit;
							}
						}

					}while($regen);

					$res = mysql_query("insert into tblcouponissue_off set check_code='".$tcode."',coupon_code='".$couponcode."',reg_date=now()",$conn);
					if($res){
						array_push($result['codes'],$tcode);
					}else{
						echo mysql_error();
						exit;
					}
				}
				$result['result'] = count($result['codes']) > 0;
			}else{
				$result['msg'] = 'DB 접속 오류';
			}
		}else{
			$result['msg'] = '쿠폰 정보 전달 오류';
		}
		return $result;
	}

	// 신규 오프라인 쿠폰 등록
	function _new($param=array()){
		$result = array('result'=>false,'msg'=>'알수 없는 오류');
		$input = array();
		$input['coupon_code']= (empty($param['coupon_code']))?$this->_genCouponcode():$param['coupon_code'];
		$input['coupon_name']=trim($param['coupon_name']);
		$input['issue_type'] = $param['issue_type'];

		if($param['time']=="D"){
			$input['date_start'] = (!empty($param['date_start']) && preg_match('/^2[0-9]{3}-[0-9]{2}-[0-9]{2}$/',$param['date_start']))?$param['date_start']:date('Y-m-d');
			$input['date_end'] = (!empty($param['date_end']) && preg_match('/^2[0-9]{3}-[0-9]{2}-[0-9]{2}$/',$param['date_end']))?$param['date_end']:date('Y-m-d');

			$input['date_start'] = str_replace("-","",$param['date_start'])."00";
			$input['date_end'] = str_replace("-","",$param['date_end'])."23";
		}else if(intval($param['peorid']) > 0){
			$input['date_start'] = "-".intval($param['peorid']);
			$input['date_end'] = "";
		}else{
			$result = array('result'=>false,'msg'=>'사용 기간 지정 오류');
			return $result;
		}

		if(in_array($input['issue_type'],array('O','P'))){
			if(intval($param['issue_tot_no']) < 1){
				$result = array('result'=>false,'msg'=>'발행 쿠폰수 오류');
				return $result;
			}
			$input['issue_tot_no'] = $param['issue_tot_no'];
		}


		if($param['sale_type'] =="+") $input['sale_type'] = ($param['sale2']=='%')?1:3;
		else $input['sale_type'] = ($param['sale2']=='%')?2:4;

		$input['sale_money'] 	= max(intval($param['sale_money']),0);
		$input['mini_price'] 	= max(intval($param['mini_price']),0);
		$input['issue_tot_no'] 	= max(intval($param['issue_tot_no']),0);
		$input['sale_money'] 	= max(intval($param['sale_money']),0);
		$input['sale_money'] 	= max(intval($param['sale_money']),0);
		$input['repeat_id'] 	= empty($param['repeat_id'])?"N":$param['repeat_id'];
		$input['productcode'] 	= trim($param['productcode']);
		$input['use_con_type1'] = ($input['productcode'] == 'ALL' || empty($param['use_con_type1']))?'N':$param['use_con_type1'];
		$input['use_con_type2'] = ($input['productcode'] == 'ALL' || empty($param['use_con_type2']))?'Y':$param['use_con_type2'];

		$input['amount_floor']	= trim($param['amount_floor']);
		$input['bank_only'] 	= trim($param['bank_only']);
		$input['order_limit']	= ($param['order_limit'] != 'Y')?'N':'Y';
		$input['detail_auto'] 	= trim($param['detail_auto']);
		$input['description'] 	= trim($param['description']);
		$input['use_point'] 	= trim($param['use_point']);

		$input['etcapply_gift'] = ($param['etcapply_gift'] == 'A')?'A':'';
		$input['repeat_ok']		= ($param['repeat_ok'] == 'Y')?'Y':'N';

		if($input['issue_type'] != 'N'){
			$input['member'] = 'ALL';
			$input['display'] = 'Y';
		}else{
			$input['member'] = '';
			$input['display'] = 'N';
		}
		//$input['date'] = date("YmdHis");
		$sql = "INSERT tblcouponinfo SET ";
		foreach($input as $cul=>$val){
			$sql .= $cul." = '".$val."',";
		}
		$sql.= "date			= '".date("YmdHis")."' ";
		if(mysql_query($sql,get_db_conn())){
			$result = array('result'=>true,'msg'=>'정상 등록','coupon_code'=>$input['coupon_code'],'issue_type'=>$input['issue_type']);
			if(in_array($input['issue_type'],array('O','P')) && $input['issue_tot_no'] > 0){ // 오프라인 쿠폰
				$offresult = $this->_setOffCode($input['coupon_code'],$input['issue_tot_no']);
				if($offresult['result']){
					$result['couponcodes'] = $offresult['codes'];
				}else{
					mysql_query("delete from tblcouponinfo where coupon_code='".$input['coupon_code']."'",get_db_conn());
					if(!empty($offresult['msg'])) $result['msg'] = $offresult['msg'];
				}
			}
		}else{
			$result = array('result'=>false,'msg'=>mysql_error());
		}
		return $result;
	}


	// 쿠폰 이미지 등록
	function _couponImg($coupon_code,$param=array()){ // $param 에는 $_FILE 에서 쿠폰 이미지 정보 배열을 넣어야함.
		if(!empty($param['tmp_name']) && is_uploaded_file($param['tmp_name'])){
			if($param['size'] < 153600){
				if(strlen($param['name'])>0){
					$ext = strtolower(substr($param['name'],strlen($param['name'])-3,3));
					if($ext=="gif"){
						$imagename = "COUPON".$coupon_code.".gif";
						move_uploaded_file($param['tmp_name'],$this->imgpath.$imagename);
						chmod($imagepath.$imagename,0666);
						return 'ok';
					}else{
						return 'notgif';
					}
				}
			}else{
				return 'sizeover';
			}
		}
		return '';
	}

	// 쿠폰 인증 (발급)
	function _authIssue($couponcode){
		global $_ShopInfo;
		$return = array('result'=>false,'msg'=>'알수 없는 오류');
		if(strlen($_ShopInfo->getMemid())==0){
			$return['msg'] = '로그인되어 있지 않습니다.';
		}else if(empty($couponcode)){
			$return['msg']= '입력 정보 오류';
		}else{
			$couponcode = preg_replace('/[^0-9a-zA-Z]/','',$couponcode);
			$sql = "select * from tblcouponinfo c left join tblcouponissue_off o using(coupon_code) where o.check_code='".$couponcode."' and (id is null || length(id) < 1) limit 1";
			$cres = mysql_query($sql,get_db_conn());
			if($cres) $cinfo = mysql_fetch_assoc($cres);
			if(!is_array($cinfo) || empty($cinfo['check_code']))  $return['msg'] = '등록된 쿠폰 번호가 아닙니다.';
			else{
				if(!empty($cinfo['date_end']) && $cinfo['date_end'] < date('Ymd')) $return['msg'] = '사용 기간 초과';
				else{
					$sql = "select count(*) from tblcouponissue where coupon_code='".$cinfo['coupon_code']."' and id='".$_ShopInfo->getMemid()."'";
					$cres = mysql_query($sql,get_db_conn());
					if(mysql_result($cres,0,0) > 0){
						$return['msg'] = '이미 등록된 쿠폰이 있습니다.';
					}else{
						$date = date("YmdHis");
						if(intval($cinfo['date_start']) < 0) {
							//$date_start = substr($date,0,10);
							//$date_end = date("Ymd",mktime(0,0,0,substr($date,4,2),substr($date,6,2)+abs(intval($cinfo['date_start'])),substr($date,0,4)))."23";
							$date_start = date('YmdH');
							$date_end = date('Ymd23',strtotime('+'.abs(intval($cinfo['date_start'])).' day'));
						} else {
							$date_start=$cinfo['date_start'];
							$date_end=$cinfo['date_end'];
						}
						$sql = "insert  into tblcouponissue set coupon_code='".$cinfo['coupon_code']."',id='".$_ShopInfo->getMemid()."',date_start='".$date_start."',date_end='".$date_end."',used='N',date='".$date."'";

						$res= mysql_query($sql,get_db_conn());

						$sql = "UPDATE tblcouponinfo SET issue_no = issue_no+1 where coupon_code='".$cinfo['coupon_code']."'";
						mysql_query($sql,get_db_conn());

						mysql_query("update tblcouponissue_off set id='".$_ShopInfo->getMemid()."',auth_date=NOW() where check_code='".$couponcode."'",get_db_conn());
						$return = array('result'=>true,'msg'=>'인증 성공');
					}
				}
			}
		}
		return $return;
	}

	// 발행
	function _issue(){
		if(!empty($this->attr['code']) && count($this->attr) > 0){

		}else{

		}
	}

	// 지정된 쿠폰 사용
	function _use(){
		if(!empty($this->attr['code']) && count($this->attr) > 0){

		}else{

		}
	}

	//쿠폰 정보 출력
	function _couponList($param=array()){
		$cond['page_num'] = 10;
		$cond['list_num'] = 20;


		$where = array();
		array_push($where,"vender='0'"); // 벤더 관련 ( 향후 조건 선별 필요)

		if(!empty($param['issue_type'])){ // issue type 별 검색 조건 추가
			$tmp = explode(',',$param['issue_type']);
			$twhere = array();
			foreach($tmp as $itype){
				if(substr($itype,0,1) == '!'){
					for($jj=1;$jj<strlen($itype);$jj++){
						$itypeval = substr($itype,$jj,1);
						if(!empty($this->issueTypes[$itypeval])) array_push($twhere," issue_type !='".$itypeval."'");
					}
					array_push($where,'('.implode(' and ',$twhere).')');
				}else{
					for($jj=0;$jj<strlen($itype);$jj++){
						$itypeval = substr($itype,$jj,1);
						if(!empty($this->issueTypes[$itypeval])) array_push($twhere," issue_type ='".$itypeval."'");
					}
					array_push($where,'('.implode(' or ',$twhere).')');
				}
			}
		}
		$where = (is_array($where) && count($where))?" where ".implode(' and ',$where).' ':'';

		$sql = "SELECT COUNT(*) FROM tblcouponinfo ".$where;

		$result = mysql_query($sql,get_db_conn());
		$return['total'] = mysql_result($result,0,0);
		$return['total_page'] = ceil($return['total']/$cond['list_num']);
		$return['page'] = min(((empty($param['page']) || intval($param['page']) < 1)?1:intval($param['page'])),$return['total_page']);
		$return['items'] = array();
		if($return['total'] > 0){
			$limit = ' limit '.(($return['page']-1)*$cond['list_num']).','.$cond['list_num'];
			$orderby = ' order by date desc';
			$sql = "select * from tblcouponinfo ".$where.$orderby.$limit;
			$result = mysql_query($sql,get_db_conn());
			if($result){
				$vno = $return['total'] - ($return['page']-1)*$cond['list_num'];
				while($item=mysql_fetch_assoc($result)){
					$item['vno'] = $vno--;
					$item['dan'] = ($item['sale_type']<=2)?'%':'원';
					$item['sale'] = ($item['sale_type']%2==0)?'할인':'적립';

				//	$item['range'] = ($item['date_start'] > 0)?str_replace('-','.',substr($item['date_start'],2,8)." ~ ".substr($item['date_end'],2,8)):abs($item['date_start'])."일동안";
					if($item['date_start'] > 0){
						$item['range'] = substr($item['date_start'],0,4).'.'.substr($item['date_start'],4,2).'.'.substr($item['date_start'],6,2)." ~ ";
						if(!_empty($item['date_end'])) $item['range'] .= substr($item['date_end'],0,4).'.'.substr($item['date_end'],4,2).'.'.substr($item['date_end'],6,2);
					}else{
						$item['range'] = abs($item['date_start'])."일동안";
					}

					array_push($return['items'],$item);
				}
			}
		}
		return $return;
	}

	// 발급 리스트
	function _issueList($param=array()){
		$cond['page_num'] = 10;
		$cond['list_num'] = 20;
		$result = NULL;
		if($this->_checkCodePattern($param['coupon_code'])){
			$result = array('coupon_code'=>$param['coupon_code']);
			$result['search'] = trim($param['search']);
			if(preg_match('/[^0-9a-zA-Z_]/',$result['search'])) $result['search']='';

			$sql = "SELECT a.coupon_name,COUNT(b.coupon_code) as issuetotal, COUNT(IF(b.used='Y',1,NULL)) as usenum, COUNT(IF(b.id like '%".$result['search']."%',1,NULL)) as searchcnt FROM tblcouponinfo a left join tblcouponissue b on a.coupon_code=b.coupon_code WHERE a.coupon_code = '".$result['coupon_code']."' AND a.vender=0 group by b.coupon_code";

			$res=mysql_query($sql,get_db_conn());
			$result = array_merge($result,@mysql_fetch_assoc($res));

			mysql_free_result($res);
			$result['total'] = (strlen($result['search']) > 0)?$result['searchcnt']:$result['issuetotal'];

			$result['total_page'] = ceil($result['total']/$cond['list_num']);
			$result['page'] = min(((empty($param['page']) || intval($param['page']) < 1)?1:intval($param['page'])),$result['total_page']);
			$result['items'] = array();

			if($result['total'] > 0){
				$sql = "SELECT * FROM tblcouponissue WHERE coupon_code = '".$result['coupon_code']."' ";
				if(strlen($result['search']) > 0) $sql.= "AND id LIKE '%".$result['search']."%' ";
				$orderby = "ORDER BY date DESC";
				$limit = ' limit '.(($result['page']-1)*$cond['list_num']).','.$cond['list_num'];
				$sql .=$orderby.$limit;

				$res = mysql_query($sql,get_db_conn());
				if($res){
					$vno = $result['total'] - ($result['page']-1)*$cond['list_num'];
					while($item=mysql_fetch_assoc($res)){
						$item['vno'] = $vno--;
						array_push($result['items'],$item);
					}
				}
			}
		}
		return $result;
	}


	// 발급 내역 삭제
	function _issueDelete($coupon_code,$uid=NULL){
		$return = array('result'=>false,'msg'=>'알수없는 오류');
		if($this->_checkCodePattern($coupon_code) && strlen($uid)>0){
			$sql = "DELETE FROM tblcouponissue WHERE coupon_code = '".$coupon_code."' AND id = '".$uid."' ";
			mysql_query($sql,get_db_conn());
			$return = (!mysql_errno())?array('result'=>true,'msg'=>"정상 처리 되었습니다."):array('result'=>false,'msg'=>mysql_error());
		}else{
			$return = array('result'=>false,'msg'=>'쿠폰 코드 또는 유저구분 오류');
		}
		return $reuturn;
	}


	// 사용 상태 초기화
	function _issueRe($coupon_code,$uid=NULL){
		$return = array('result'=>false,'msg'=>'알수없는 오류');
		if($this->_checkCodePattern($coupon_code) && strlen($uid)>0){
			$sql = "UPDATE tblcouponissue SET used='N' WHERE coupon_code = '".$coupon_code."' AND id = '".$uid."' ";
			mysql_query($sql,get_db_conn());
			$return = (!mysql_errno())?array('result'=>true,'msg'=>"정상 처리 되었습니다."):array('result'=>false,'msg'=>mysql_error());
		}else{
			$return = array('result'=>false,'msg'=>'쿠폰 코드 또는 유저구분 오류');
		}
		return $reuturn;
	}

	// 정지
	function _stop($coupon_code){
		$return = array('result'=>false,'msg'=>'알수없는 오류');
		if($this->_checkCodePattern($coupon_code)){
			$sql = "UPDATE tblcouponinfo SET display='N',issue_type='D' WHERE coupon_code = '".$coupon_code."' ";
			if(mysql_query($sql,get_db_conn())) $return = array('result'=>true,'msg'=>"정상 처리 되었습니다.");
			else $return = array('result'=>false,'msg'=>mysql_error());
		}else{
			$return = array('result'=>false,'msg'=>'쿠폰 코드 오류');
		}
		return $return;
	}


	// 삭제
	function _delete($coupon_code){
		$return = array('result'=>false,'msg'=>'알수없는 오류');
		if($this->_checkCodePattern($coupon_code)){
			$sql = "DELETE FROM tblcouponinfo WHERE coupon_code = '".$coupon_code."' ";
			if(mysql_query($sql,get_db_conn())){
				$return = array('result'=>true,'msg'=>'삭제 성공');
				$sql = "DELETE FROM tblcouponissue WHERE coupon_code = '".$coupon_code."' ";
				if(mysql_query($sql,get_db_conn())){
					if(file_exists($GLOBALS['Dir'].DataDir."shopimages/etc/COUPON".$coupon_code.".gif")){
						@unlink($GLOBALS['Dir'].DataDir."shopimages/etc/COUPON".$coupon_code.".gif");
					}
				}
				$sql = "DELETE FROM tblcouponissue_off WHERE coupon_code = '".$coupon_code."' ";
				mysql_query($sql,get_db_conn());
			}else{
				$return = array('result'=>false,'msg'=>mysql_error());
			}
		}else{
			$return = array('result'=>false,'msg'=>'쿠폰 코드 오류');
		}
		return $return;
	}
}
?>