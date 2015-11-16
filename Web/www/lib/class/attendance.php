<?
require_once dirname(__FILE__).'/../ext/func.php';

class attendance{
	var $db=NULL;
	var $session = NULL;
	var $status = false;
	var $statusmsg = '';
	var $pattern = array();
	var $info = array();
	var $stampinfo = array();
	
	function attendance(){
		$this->_init();
	}
	
	// 초기화
	function _init($aidx=NULL){
		global $_ShopInfo;
		$this->db = &get_db_conn();
		$this->session = array();
		if(false !== $admid = $this->_checkAdmin()){
			$this->session['id'] = $admid;
			$this->session['type'] = 'admin';
			$this->status = true;
			
			if(_isInt($aidx)){
				if(!$this->_set($aidx)){
				//	$this->status = false;
					$this->statusmsg = $this->msg;
				}
			}
		}else if(!_empty($_ShopInfo->getMemid())){
			$this->session['id'] = $_ShopInfo->getMemid();
			$this->session['type'] = 'user';
			$this->status = true;
			
			if(_isInt($aidx)){
				if(!$this->_set($aidx)){
			//		$this->status = false;
					$this->statusmsg = $this->msg;
				}
			}
		}else{
			$this->status = false;
			$this->statusmsg = '로그인 되어 있지 않습니다.';
		}
		
		$this->pattern['date'] = '/^([0-9]{4}-[0-9]{1,2}-[0-9]{1,2})\s?([0-9]{1,2}:[0-9]{1,2}:[0-9]{1,2}|)$/';		
		
	}
	
	// 이벤트 가져 오기
	function _set($aidx=NULL){
		$this->info = array();
	//	if($this->status === false) return false;				
		if(false === $this->info = $this->_getItem($aidx)) return false;
		if(false === $this->info['rewards'] = $this->_getRewards()) return false;
	}
	
	function _get($key){
		if(_array($this->info) && isset($this->info[$key])) return $this->info[$key];
		else return NULL;
	}
	
	function _getItem($aidx){		
		$result = array();
		if(_isInt($aidx)){
			if(false === $res = mysql_query("select * from attendance_items where aidx='".$aidx."' limit 1",$this->db)) return $this->_return(false,'DB 에러gi');
			if(mysql_num_rows($res) < 1) return $this->_return(false,'해당 식별 번호의 출석 이벤트를 찾을 수 없습니다.');
			$result = mysql_fetch_assoc($res);
			
			$curr = date('Y-m-d H:i:s');
			
			$result['status'] = true;
			$result['statusmsg'] = '정상';
				
			// 이벤트 가용 상태 관련 설정 부분 - Todo : 조건별 회원 제한 사항 선택 등
			if($result['stdate'] > $curr){			
				$result['status'] = 0;
				$result['statusmsg'] = '대기';
			}else if($result['enddate'] < $curr){
				$result['status'] = -1;
				$result['statusmsg'] = '마감';
			}else{
				$result['status'] = 1;
				$result['statusmsg'] = '진행중';
			}
			mysql_free_result($res);		
		}
		return $result;
	}
	
	// 출석 등록
	function _setStamp($ment=''){		
		if($this->status === false) return $this->_return(false,$this->statusmsg);
		if(!_isInt($this->info['aidx'])) return $this->_return(false,'출석이벤트가 선택 되지 않았습니다.');
		if($this->info['status'] === false) return $this->_return(false,$this->info['statusmsg']);
		if(_empty($this->session['id'])) return $this->_return(false,'비정상적인 접근 에러 #1');
		
	
		if(false === $res =mysql_query("select date,continuity from attendance_stamp where aidx='".$this->info['aidx']."' and memid="._escape($this->session['id'])." order by date desc limit 1",$this->db)) return $this->_return(false,'DB Error Stamp');
		
		
		$stampinfo = array();
		$stampinfo['aidx'] = $this->info['aidx'];
		$stampinfo['memid'] = $this->session['id'];
		
		if(mysql_num_rows($res) > 0){
			$chk = mysql_fetch_assoc($res);
			if($chk['date'] == date('Y-m-d')) return $this->_return(false,'이미 출석 체크 등록 되었습니다.');
			else if(date('Y-m-d',strtotime("-1 day")) == $chk['date']) $stampinfo['continuity'] = intval($chk['continuity']);
			else $stampinfo['continuity'] = intval($chk['continuity'])+1;
		}else{
			$stampinfo['continuity'] = 1;			
		}
		
		if(!_empty($ment)) $stampinfo['ment'] = $ment;
		$stampinfo['ip'] = ip2long($_SERVER['REMOTE_ADDR']);				
		
		$sql = "insert into attendance_stamp set ";
		foreach($stampinfo as $key=>$val){
			$sql .= $key.'='._escape($val).', ';
		}
		$sql .= ' date=NOW(),time=NOW()';	
		if(false === mysql_query($sql,$this->db)) return $this->_return(false,'DB Error Stamp2');		
		return $this->_checkReward($stampinfo['continuity']);
	}
	
	// 보상 해당 여부 파악
	function _checkReward($continuity=NULL){
		if(!_isInt($continuity)) return $this->_return(false,'연속출석 체크용 변수 전달 오류');		
		
		$totalsum = 0;		
		$historys = array();
		if(!_array($this->info['rewards'])) return true;		// 보상 항목을 못불러온 경우
	
		// 보상 이력 가져 오기
		$sql = "select ridx,count(seq) as cnt,sum(rewval) as totalrew,max(rewdate) as date from attendance_reward_history where memid ='".$this->session['id']."' and aidx='".$this->info['aidx']."' group by ridx,rewtype";
		if(false === $hres = mysql_query($sql,$this->db)) $this->_return(false,'History read Error');
		if(mysql_num_rows($hres)){
			while($hrow = mysql_fetch_assoc($hres)) $historys[$hrow['ridx']] = $hrow;
		}
		
		
		foreach($this->info['rewards'] as $ridx=>$reward){
			if(isset($historys[$ridx]) && ($reward['rewmax']< 0 || ($reward['rewmax'] > 0 && $historys[$ridx]['totalrew'] >= $reward['rewmax']))) continue;			
			
			$sql = "select count(*) as cnt from attendance_stamp where aidx='".$this->info['aidx']."' and memid='".$this->session['id']."'";
			
			if(isset($historys[$ridx]) && !_empty($historys[$ridx]['date'])) $sql .= " and date > '".$historys[$ridx]['date']."'";
			
			if($reward['conse'] == '1')	$sql .= " and continuity='".$continuity."' group by continuity";
			

			if(false === $res = mysql_query($sql,$this->db)) return $this->_return(false,mysql_error());
			$checkranges = mysql_result($res,0,0);
			if($checkranges >= $reward['ranges']){
				if(false === $this->_giveReward($ridx)) return false; // 보상 지급
			}
		}
	
		return true;
	}
	
	function _giveReward($ridx=NULL){
		if(!_isInt($ridx)) return $this->_return(false,'보상 항목 고유 번호 전달 오류');		
		$rewtype = $this->info['rewards'][$ridx]['rewtype'];
		$rewval = $this->info['rewards'][$ridx]['rewval'];
		
		$sql = "insert into attendance_reward_history set memid='".$this->session['id']."',aidx='".$this->info['aidx']."',ridx='".$ridx."',rewtype='".$rewtype."',rewval='".$rewval."',rewdate=NOW()";

		if(false === mysql_query($sql,$this->db)) return $this->_return(false,mysql_error());
		// 회원 보상 처리 부분
		switch($rewtype){
			case 'reserve':
				SetReserve($this->session['id'], $rewval,'출석이벤트보상');
				break;
			default:
				return $this->_return(false,'보상 형태 오류');
				break;
		}
		return;
	}
	
	// 출석 체크 현황 가져 오기
	function _getStamp($param=NULL){
		if($this->status === false) return $this->_return(false,$this->statusmsg);
		if(!_isInt($this->info['aidx'])) return $this->_return(false,'출석이벤트가 선택 되지 않았습니다.');
		if($this->info['status'] === false) return $this->_return(false,$this->info['statusmsg']);		
		
		$where = array("aidx='".$this->info['aidx']."'");
		if($this->session['type'] == 'admin'){ // 관리자는 다른 용도
			
		}else{
			array_push($where,'memid='._escape($this->session['id']));
			if($param!== true && !_array($param)){
				array_push($where,"date >='".date('Y-m-01')."'");
				array_push($where,"date <'".date('Y-m-01',strtotime('+1 month'))."'");
			}
		}
		$where = _array($where)?' where '.implode(' and ',$where):'';		
		$ordby = " order by date desc";		
		if(false === $res = mysql_query("select * from attendance_stamp ".$where.$ordby,$this->db)) return $this->_return(false,'DB Error Stamp');
		$return = array();
		if(mysql_num_rows($res)){
			while($row = mysql_fetch_assoc($res)){
				$return[$row['date']] = $row;
			}
		}
		return $return;
	}
	
	function _getStampList($param=array(),$viewlist=false){
		if(!$viewlist && $this->status === false) return $this->_return(false,$this->statusmsg);
		if(!_isInt($this->info['aidx'])) return $this->_return(false,'출석이벤트가 선택 되지 않았습니다.');
		if($this->info['status'] === false) return $this->_return(false,$this->info['statusmsg']);		
		
		$where = array("aidx='".$this->info['aidx']."'");		
		if(!$viewlist && $this->session['type'] != 'admin'){
			array_push($where,'memid='._escape($this->session['id']));
		}else{
			
		}
		
		$where = (is_array($where) && count($where))?" where ".implode(' and ',$where).' ':'';

		$sql = "SELECT COUNT(*) FROM attendance_stamp ".$where;
		$result = mysql_query($sql,get_db_conn());
		$return['total'] = mysql_result($result,0,0);
		$return['perpage'] = (_isInt($param['perpage']))?$param['perpage']:10;		
		$return['total_page'] = ceil($return['total']/$return['perpage']);
		
		$return['page'] = min(((!_isInt($param['page']) || intval($param['page']) < 1)?1:intval($param['page'])),$return['total_page']);
		$return['items'] = array();
		
		if($return['total'] > 0){
			$limit = ' limit '.(($return['page']-1)*$return['perpage']).','.$return['perpage'];
			$orderby = ' order by date desc,time desc';
			
			$sql = "select * from attendance_stamp ".$where.$orderby.$limit;
			$result = mysql_query($sql,get_db_conn());
			if($result){
				$vno = $return['total'] - ($return['page']-1)*$return['perpage'];				
				while($item=mysql_fetch_assoc($result)){
					$item['vno'] = $vno--;
					if($viewlist){
						$idlen = strlen($item['memid']);
						$item['memid'] = substr($item['memid'],0,3);
						$item['memid'] =  str_pad($item['memid'],$idlen,'*');
						$item['time'] = '';
						$item['ip'] = '***.***.***.***';
					}
					array_push($return['items'],$item);
				}
			}
		}
		return $return;
	}
	
	function _deleteStamp($param=array()){
		if($this->status === false) return $this->_return(false,$this->statusmsg);
		if(!_isInt($this->info['aidx'])) return $this->_return(false,'출석이벤트가 선택 되지 않았습니다.');
		if($this->info['status'] === false) return $this->_return(false,$this->info['statusmsg']);		
		
		$where = array();		
		
		if($this->session['type'] != 'admin'){
			return $this->_return(false,'접근 권한 없음');
		}
		
		if(!_array($param['delseq'])) return $this->_return(false,'삭제 대상 미지정');
		$delseqs = array();
		foreach($param['delseq'] as $delseq){
			if(_isInt($delseq)) array_push($delseqs,$delseq);
		}
		
		if(!_array($delseqs)) return $this->_return(false,'삭제 대상 미지정2');
		
		array_push($where,"seq in ('".implode("','",$delseqs)."')");
		array_push($where,"aidx='".$this->info['aidx']."'");
		
		$sql = "delete FROM attendance_stamp where ".implode(' and ',$where);		
		if(false === $result = mysql_query($sql,get_db_conn())){
			return $this->_return(false,'삭제중 DB 연동 오류');
		}
		return mysql_affected_rows(get_db_conn());		
	}
	
	function _giveRewardList($param=array()){
		if($this->status === false) return $this->_return(false,$this->statusmsg);
		if(!_isInt($this->info['aidx'])) return $this->_return(false,'출석이벤트가 선택 되지 않았습니다.');
		if($this->info['status'] === false) return $this->_return(false,$this->info['statusmsg']);		
		
		$where = array("aidx='".$this->info['aidx']."'");		
		if($this->session['type'] != 'admin'){
			array_push($where,'memid='._escape($this->session['id']));
		}else{
			
		}
		
		$where = (is_array($where) && count($where))?" where ".implode(' and ',$where).' ':'';

		$sql = "SELECT COUNT(*) FROM attendance_reward_history ".$where;
		$result = mysql_query($sql,get_db_conn());
		$return['total'] = mysql_result($result,0,0);
		$return['perpage'] = (_isInt($param['perpage']))?$param['perpage']:10;		
		$return['total_page'] = ceil($return['total']/$return['perpage']);
		
		$return['page'] = min(((!_isInt($param['page']) || intval($param['page']) < 1)?1:intval($param['page'])),$return['total_page']);
		$return['items'] = array();
		
		if($return['total'] > 0){
			$limit = ' limit '.(($return['page']-1)*$return['perpage']).','.$return['perpage'];
			$orderby = ' order by rewdate desc';
			
			$sql = "select * from attendance_reward_history ".$where.$orderby.$limit;
			$result = mysql_query($sql,get_db_conn());
			if($result){
				$vno = $return['total'] - ($return['page']-1)*$return['perpage'];				
				while($item=mysql_fetch_assoc($result)){
					$item['vno'] = $vno--;
					array_push($return['items'],$item);
				}
			}
		}
		return $return;
	}
	
	
	// 배열 정보 -1 : 경과 / 0: 아직 대상아님 / 1: 출석 체크 했음
	function _calenderArr(){
			
	}
	
	function _getList($param=array()){
		$return = array();
		$where = array();
		$curr = date('Y-m-d H:i:s');
		
		if($param == 'ables'){
			array_push($where," a.stdate <= '".$curr."'");
			array_push($where," a.enddate > '".$curr."'");
		}else{
			
		}
		
		$where = _array($where)?' where '.implode(' and ',$where):'';
		
		if(false === $res = mysql_query("select count(*) from attendance_items a ".$where,$this->db)){
			return $this->_return(false,'DB 연동 오류');
		}
		
		$return['total'] = mysql_result($res,0,0);
		$return['items'] = array();
		$return['perpage'] = _isInt($param['perpage'])?$param['perpage']:10;
		if(!_isInt($return['perpage'])) $return['perpage'] = 10;
		$return['page'] = _isInt($param['page'])?$param['page']:1;
		$return['total_page'] = ($return['total']<1)?1:ceil($return['total']/$return['perpage']);
		$return['page'] = min($return['page'],$return['total_page']);	
		
		if($return['total']>0){
			$limit = ' limit '.($return['page']-1)*$return['perpage'].','.$return['perpage'];					
			if(false === $res = mysql_query(" SELECT a. * , SUM( s.totalcnt ) AS totalcnt, COUNT( s.memid ) as usercnt FROM attendance_items a LEFT JOIN (SELECT aidx, COUNT( * ) AS totalcnt, memid FROM attendance_stamp AS q GROUP BY q.aidx, q.memid )s ON s.aidx = a.aidx ".$where." GROUP BY a.aidx  ".$limit,$this->db)) return $this->_return(false,'DB 연동 오류');
			
			$vno = $return['total']-(($return['page']-1)*$return['perpage']);
			
			while($row = mysql_fetch_assoc($res)){
				$row['vno'] = $vno--;
				
			// 이벤트 가용 상태 관련 설정 부분 - Todo : 조건별 회원 제한 사항 선택 등
				if($row['stdate'] > $curr){			
					$row['status'] = 0;
					$row['statusmsg'] = '대기중';
				}else if($row['enddate'] < $curr){
					$row['status'] = -1;
					$row['statusmsg'] = '마감';
				}else{
					$row['status'] = 1;
					$row['statusmsg'] = '진행중';
				}
				array_push($return['items'],$row);
			}
		}
		
		return $return;
	}
	
	
	function _deleteItems($aidxs){
		$idxs = array();
		if(_array($aidxs)){
			foreach($aidxs as $aidx){
				if(_isInt($aidx)) array_push($idxs,$aidx);
			}
		}else if(_isInt($aidxs)) array_push($idxs,$aidxs);
		
		if(!_array($idxs)){
			return $this->_return(false,'삭제대상 전달 오류');	
		}
		$sql = "delete from attendance_items where aidx in ('".implode("','",$idxs)."')";
		mysql_query($sql,$this->db);
		$sql = "delete from attendance_reward where aidx in ('".implode("','",$idxs)."')";
		mysql_query($sql,$this->db);
		$sql = "delete from attendance_reward_history where aidx in ('".implode("','",$idxs)."')";
		mysql_query($sql,$this->db);
		$sql = "delete from attendance_stamp where aidx in ('".implode("','",$idxs)."')";
		mysql_query($sql,$this->db);
		return true;
	}
	
	// 관리자 로그인 확인
	function _checkAdmin(){
		global $_usersession;
	
		if(is_object($_usersession) && !_empty($_usersession->id)){
			return $_usersession->id;
		}else{
			return false;
		}
	}
	
	//	출석 체크 이벤트 등록
	function _setItem($param=array()){
		$iteminfo = array();
		$where = array();
		
		$isupdate = false;
		if($this->_checkAdmin() === false) return $this->_return(false,'권한이 없습니다.');
		if(_isInt($param['aidx'])){
			if(false === $res = mysql_query("select  * from attendance_items where aidx='".$param['aidx']."' limit 1",$this->db)) return $this->_return(false,'DB 에러si');
			if(mysql_num_rows($res) < 1) return $this->_return(false,'출석 체크 이벤트를 찾을 수 없습니다.');
			$oiteminfo= mysql_fetch_assoc($res);			
			array_push($where,"aidx='".$param['aidx']."'");
			$aidx = $param['aidx'];
			$isupdate = true;
			mysql_free_result($res);
		}
		
		$iteminfo['title'] = _escape($param['title']);
		if(_empty($param['stdate'])){
			$iteminfo['stdate'] = date('Y-m-d H:i:s');
		}else{
			if(!preg_match($this->pattern['date'],$param['stdate'],$mat)) return $this->_return(false,'시작일 패턴이 일치 하지 않습니다.');
			if(_empty($mat[2])) $mat[2] = '00:00:00';
			$iteminfo['stdate'] = date('Y-m-d H:i:s',strtotime($mat[1].' '.$mat[2]));
		}
		
		if(_empty($param['enddate'])){
			$iteminfo['enddate'] = date('Y-m-d H:i:s');
		}else{
			if(!preg_match($this->pattern['date'],$param['enddate'],$mat)) return $this->_return(false,'종료일 패턴이 일치 하지 않습니다.');
			if(_empty($mat[2])) $mat[2] = '23:59:59';
			$iteminfo['enddate'] = date('Y-m-d H:i:s',strtotime($mat[1].' '.$mat[2]));
		}
		
		$iteminfo['stdate'] = _escape($iteminfo['stdate']);
		$iteminfo['enddate'] = _escape($iteminfo['enddate']);
		
		
		
		if(!_empty($param['design'])) $iteminfo['design'] = _escape($param['design']);
		if(!_empty($param['memo'])) $iteminfo['memo'] = _escape($param['memo']);
		
		$where = (_array($where))?' where '.implode(' and ',$where):'';
		
		$indata = array();
		foreach($iteminfo as $key=>$val){
			if($isupdate === true && $oiteminfo[$key] == $val) continue;
			array_push($indata, $key.'='.$val);
		}		
		if(_array($indata)){
			$sql = (($isupdate)?'update ':'insert into ').' attendance_items set ';
			$sql .= implode(',',$indata).$where;		
			if(false === mysql_query($sql,$this->db)) return $this->_return(false,'정보 갱신 실패');
			if(!$isupdate) $aidx = mysql_insert_id($this->db);			
		}				
		$this->_set($aidx);		
		if(_array($param['rewardItem'])) return $this->_setReward($param['rewardItem']);
	}
	
	// 보상 등록
	function _setReward($param=array()){			
		if(!_isInt($this->info['aidx'])) return $this->_return(false,'출석 체크 이벤트 찾기 오류sr');
		if(false === $rlist = $this->_getRewards()) return $this->_return(false,'보상 목록 호출 오류');
		if($param === false){
			$sql = "delete * from attendance_reward where aidx='".$this->info['aidx']."'";
			if(false === mysql_query($sql,$this->db)) return $this->_return(false,mysql_error());
			return true;
		}else if(!_array($param)){
			return $this->_return(false,'세팅 파라메터 오류sr');
		}else{
			foreach($param as $itm){
				$item = $this->_parseRewData($itm);
				if($item['ranges'] > 0){
					if(_isInt($item['ridx'])){						
						$sql = "update attendance_reward set conse='".$item['conse']."',ranges='".$item['ranges']."',rewtype='".$item['rewtype']."',rewval='".$item['rewval']."',rewmax='".$item['rewmax']."' where aidx='".$this->info['aidx']."' and ridx='".$item['ridx']."'";
						unset($rlist[$item['ridx']]);
					}else{
						$sql = "insert into attendance_reward set aidx='".$this->info['aidx']."',conse='".$item['conse']."',ranges='".$item['ranges']."',rewtype='".$item['rewtype']."',rewval='".$item['rewval']."',rewmax='".$item['rewmax']."'";						
					}			
					// Todo : 오류 상화에 따른 대처 및 리턴 처리
					@mysql_query($sql,$this->db);
				}else{
					
				}
			}			
			if(_array($rlist)){
				$excpridxs = array_keys($rlist);				
				$sql = "delete from attendance_reward where aidx='".$this->info['aidx']."' and ridx in ('".implode("','",$excpridxs)."')";			
				@mysql_query($sql,$this->db);
			}
		}
		return true;
	}
	
	function _getRewards(){
		$return = array();
		if(!_isInt($this->info['aidx'])) return $this->_return(false,'출석 체크 이벤트 찾기 오류gr');			
		$sql = "select * from attendance_reward where aidx=".$this->info['aidx']." order by `ranges` asc,rewval asc";
		
		if(false === $res = mysql_query($sql,$this->db)){
			return $this->_return(false,mysql_error());
		}else if(mysql_num_rows($res) < 1){
			
		}else{
			while($row = mysql_fetch_assoc($res)){
				//array_push($return,$row);
				$return[$row['ridx']] = $row;
			}
		}
		return $return;
	}
	
	function _parseRewData($data){
		$return = array();
		if(!_empty($data)){
			$tmp = explode('_',$data);
			$return['ridx']= _isInt($tmp[0])?$tmp[0]:0;
			$return['conse']= ($tmp[1]== '1')?1:0;			
			$return['ranges']= _isInt($tmp[2])?$tmp[2]:0;
			//$return['conse']= ($tmp[3]== 'reserve')?'reserve':'gift';			
			$return['rewtype']= 'reserve';			
			$return['rewval']= _isInt($tmp[4])?$tmp[4]:0;
			$return['rewmax']= _isInt($tmp[5],true)?$tmp[5]:-1;
		}else{
			$return =false;		
		}
		return $return;
	
	}
	
		
	function _return($bool=true,$msg=''){		
		if(!_empty($msg)){
			$this->statusmsg = $msg;
		}
		return $bool;
	}
	
	function _msg(){
		return $this->statusmsg;
	}
	
}
?>