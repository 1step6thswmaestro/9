<?
// 원 hiworks 라이브러리를 수정하지 않고 기능 확장을 위해서 사용


include_once dirname(__FILE__).'/nusoap.php';
include_once dirname(__FILE__).'/XML.class.php';
include_once dirname(__FILE__).'/Hiworks_Bill.class.php';


include_once dirname(__FILE__).'/../ext/func.php';
/* **************************************** */
/* define 정의                                */
/* **************************************** */
define( 'HB_DOCUMENTTYPE_TAX' , 'A' );    // 세금계산서
define( 'HB_DOCUMENTTYPE_BILL' , 'B' );   // 계산서
define( 'HB_DOCUMENTTYPE_DETAIL' , 'D' ); // 거래명세서

define( 'HB_TAXTYPE_TAX', 'A' );		// 과세
define( 'HB_TAXTYPE_NOTAX', 'B' );	// 영세
define( 'HB_TAXTYPE_MANUAL', 'D' );	// 수동

define( 'HB_SENDTYPE_SEND', 'S' );	// 매출
define( 'HB_SENDTYPE_RECV', 'R' );	// 매입

define( 'HB_PTYPE_RECEIPT', 'R' );	// 영수
define( 'HB_PTYPE_CALL', 'C' );		// 청구

define( 'HB_COMPANYPREFIX_SUPPLIER', 's' );	// 매출처 접두어
define( 'HB_COMPANYPREFIX_CONSUMER', 'r' );	// 매입처 접두어

define( 'HB_SOAPSERVER_URL', 'http://billapi.hiworks.co.kr/server.php?wsdl' );	// SOAP Server URL


//class Bill extends Hiworks_Bill_V2{
class Bill{
	var $document_status = array('W'=>'미발송','T'=>'미열람','R'=>'열람','S'=>'승인','B'=>'반려','C'=>'승인취소요청','A'=>'승인최소완료');
	var $reqStatus = array('R'=>'요청','S'=>'발송','E'=>'오류');
	var $billstatus = '';
	var $usable = NULL;
	var $taxrate = 10;
	var $config = array();	
	var $orderinfo = array();
	var $basicinfo = array();
	var $companyinfo = array();
	var $documentinfo = array();
	var $workinfo = array();


	var $errmsg = '';
	var $obj = NULL;
	
	function Bill($ordercode=''){
		if(true !== $this->_init_config() || !_array($this->config) || _empty($this->config['license_no'])){
			$this->errmsg = '전자세금계산서 설정이 활성화 되어 있지 않습니다.';
		}else{
			if(_empty($this->basicinfo['sc_name'])){
				$this->errmsg = '담당자명 정보가 올바르지 않습니다.';							
				$this->usable = false;
			}	
		}
	}
	
	function __construct($ordercode=''){
		$this->Bill($ordercode);
	}
	
	function _docStatus($status){
		if(isset($this->document_status[$status])) return $this->document_status[$status];
		else return 'Err';		
	}
	
	function _reqStatus($status){
		if(isset($this->reqStatus[$status])) return $this->reqStatus[$status];
		else return 'Err';		
	}
	
	function _init_config(){
		if(is_null($this->usable)){
			$this->config = array();
			$this->usable = false;

			$this->orderinfo = array();
			$this->basicinfo = array();
			$this->companyinfo = array();
			
			if(false !== $res = @mysql_query("SELECT * FROM tblshopbillinfo WHERE bill_state ='Y' limit 1",get_db_conn())){
				if(mysql_num_rows($res) == 1){
					$tmp = mysql_fetch_assoc($res);
					foreach($tmp as $key=>$val){
						if(substr($key,0,2) == 'sc') $this->basicinfo[$key] = $val;
						else $this->config[$key] = $val;
					}
					
					
					$this->usable = true;
				}				
			}		
		}
		return $this->usable;
	}
	
	function get_senderinfo(){
		$this->companyinfo['sender'] = array();		
		if(false !== $res = mysql_query("SELECT * FROM tblshopinfo limit 1",get_db_conn())){
			if(mysql_num_rows($res)){
				$tmp = mysql_fetch_assoc($res);
				
				$this->companyinfo['sender']['s_number'] = preg_replace('/([0-9]{3})[^0-9]*([0-9]{2})[^0-9]*([0-9]{5})/','$1-$2-$3',$tmp['companynum']);								
				$this->companyinfo['sender']['s_tnumber'] = ''; // 종사업장 번호
				$this->companyinfo['sender']['s_name'] = $tmp['companyname'];
				$this->companyinfo['sender']['s_master'] = $tmp['companyowner'];
				$this->companyinfo['sender']['s_address'] = $tmp['companyaddr'];
				$this->companyinfo['sender']['s_condition'] = $tmp['companybiz'];
				$this->companyinfo['sender']['s_item'] = $tmp['companyitem'];
				
				if(true !== $result = $this->validateCinfo($this->companyinfo['sender'],'s')){
					$this->errmsg = $result;
					return false;
				}				
			}else{
				$this->errmsg = '공급자 정보를 찾을수 없습니다.';
				return false;
			}
		}else{
			$this->errmsg = '공급자 정보정보 확인 중 DB 오류가 발생했습니다.';
			return false;
		}
		return true;
	}
	
	
	function get_receiverinfo(){

		if(!_array($this->companyinfo['receiver']) && !_empty($this->basicinfo['memid'])){
			
			if(!_empty($id) && false !== $res = mysql_query("SELECT * FROM bill_basic b left join bill_company using(bill_idx) where b.memid='".$this->basicinfo['memid']." order by regdate desc limit 1",get_db_conn())){

				if(mysql_num_rows($res)){
					$tmp = mysql_fetch_assoc($res);
					foreach($tmp as $k=>$v){
						switch(substr($k,0,2)){
							case 'r_':
								$this->companyinfo['receiver'][$k] = $v;	
								break;
							case 'c_':
								$this->basicinfo['receiver'][$k] = $v;	
								break;
							default:
								continue;
								break;							
						}
					}
					$this->companyinfo['receiver'] = mysql_fetch_assoc($res);
					unset($this->companyinfo['receiver']['bill_idx']);
					
					if(true !== $result = $this->validateCinfo($$this->companyinfo['receiver'],'r')){
						$this->companyinfo['receiver'] = array();
					}
				}			
			}
		}
		return $this->companyinfo['receiver'];
	}
	
	
	function validateCinfo($info=array(),$prefix='s'){		
		if(!_array($info)) return '전달된 정보가 없습니다.';
		if($prefix == 's') $premsg = '공급자';
		else if($prefix == 'r') $premsg = '공급받는자';
		else return '구분 정보가 올바르지 않습니다.';
	
		if(!preg_match('/^[0-9]{3}-[0-9]{2}-[0-9]{5}$/',$info[$prefix.'_number'])) return $premsg.' 사업자 정보가 올바르지 않습니다.';	
		
		$chkkey = array('name'=>'상호','master'=>'대표자명','address'=>'주소','condition'=>'업태','item'=>'종목');		
		foreach($chkkey as $key=>$msg){
			if(_empty($info[$prefix.'_'.$key])) return $premsg.' '.$msg.' 정보가 올바르지 않습니다.';
		}
		return true;
	}
	
	function setBillByIdx($bill_idx){
		if(!_isInt($bill_idx)){
			$this->errmsg = "식별 번호가 올바르지 않습니다.";
			return false;
		}
		if(false !== $chkr = mysql_query("select * from bill_basic where bill_idx='".$bill_idx."' limit 1",get_db_conn())){				
			if(mysql_num_rows($chkr)){
				$this->basicinfo = array_merge($this->basicinfo,mysql_fetch_assoc($chkr));
				foreach(array('company','document') as $tbl){
					if(false !== $res = mysql_query("select * from bill_".$tbl." where bill_idx='".$this->basicinfo['bill_idx']."' limit 1",get_db_conn())){
						if(mysql_num_rows($res)){
							if($tbl == 'document'){
								$this->documentinfo = mysql_fetch_assoc($res);
								unset($this->documentinfo['bill_idx']);
							}else if($tbl == 'company'){
								$this->companyinfo['receiver'] = mysql_fetch_assoc($res);
								unset($this->companyinfo['receiver']['bill_idx']);
							}								
						}
					}
				}
				$this->workinfo = array();
				if(false !== $res = mysql_query("select * from bill_document_items where bill_idx = '".$bill_idx."' order by itm_seq asc",get_db_conn())){
					while($work= mysql_fetch_assoc($res)){
						unset($work['bill_idx'],$work['itm_seq']);
						array_push($this->workinfo,$work);
					}
				}				
				
				$this->billstatus = $this->basicinfo['status'];
				return true;
			}
		}else{
			$this->errmsg = mysql_error();
			return false;
		}
	}
	
	function setOrder($ordercode){
		if(_empty($ordercode)){
			$this->errmsg = '주문 번호가 올바르지 않습니다.';
		}else{ //발송완료 :  AND deli_gbn='Y' 
			if(false !== $chkr = mysql_query("select bill_idx from bill_basic where ordercode='".$ordercode."' limit 1",get_db_conn())){				
				if(mysql_num_rows($chkr)){
					return $this->setBillByIdx(mysql_result($chkr,0,0));
				}
			}else{
				$this->errmsg = 'DB 통신 오류';
				return false;
			}
			
			if(_empty($this->billstatus)){
				if(false === $res = mysql_query("SELECT * FROM tblorderinfo WHERE ordercode='".$ordercode."' limit 1 ",get_db_conn())){
					$this->errmsg = '정보 조회중 DB 오류가 발생했습니다.';	
				}else{
					if(mysql_num_rows($res) < 1) $this->errmsg = '해당 주문 번호의 정보를 찾을수 없습니다.';
					else{
						$this->orderinfo = mysql_fetch_assoc($res);
						
						if(substr($ordercode,-1,1) == 'X'){
							$this->basicinfo['memid'] = NULL;
						}else{
							$this->basicinfo['memid'] = $this->orderinfo['id'];
						}
						
						$this->documentinfo['supplyprice'] = round($this->orderinfo['price']/(1+$this->taxrate/100));  
						$this->documentinfo['tax'] = $this->orderinfo['price'] - $this->documentinfo['supplyprice'];
						$this->documentinfo['p_type'] = HB_PTYPE_RECEIPT;
						
						
						$this->workinfo = array();					
						
						$items = $this->getOrderItem($ordercode);					
					//	if((count($items['product']) + count($items['ext'])) >4){
						if(true || (count($items['product']) + count($items['ext'])) >4){
							$tmp = array();					
							$tmp['subject'] = $items['product'][0]['subject'].' 외'.(count($items['product'])-1).'건';					
							$tmp['count'] = 1;					
							$tmp['oneprice'] = $tmp['price'] = $this->documentinfo['supplyprice'];
							$tmp['tax_row'] = $this->documentinfo['tax'];
							$tmp['etc'] = '';
							$tmp['sum'] = $tmp['tax_row'] + $tmp['price'];
							
							array_push($this->workinfo,$tmp);
						}else{
							$this->workinfo = array_merge($items['product'],$items['ext']);
						}
					}
					return true;				
				}
			}
		}
		mysql_free_result($result);
		if(_empty($this->errmsg)) $this->errmsg ='주문 정보 확인 오류';
		return false;
	}
	
	
	function getOrderItem($ordercode){ // 항목 수는 4 개까지?
		if(_empty($ordercode)){
			$this->errmsg = '주문 번호가 올바르지 않습니다.';
		}else if(false === $res = mysql_query("SELECT * FROM tblorderproduct WHERE ordercode='".$ordercode."'",get_db_conn())){
			$this->errmsg = '정보 조회중 DB 오류가 발생했습니다.';	
		}else{ //발송완료 :  AND deli_gbn='Y' 
			if(mysql_num_rows($res) < 1) $this->errmsg = '해당 주문 번호의 정보를 찾을수 없습니다.';			
			$this->orderitems = array();
			
			$result = array('product'=>array(),'ext'=>array());
			$extdata = array();
						
			while($row=mysql_fetch_assoc($res)){
				$tmp = array();				
				if($row['productcode'] == "99999999997X") continue; // 부가세??
				if(preg_match('/GIFT/',$row['productcode'])) continue;
				if($row['price'] == 0 || is_null($row['price'])) continue;
				
				$tmp['subject']=strip_tags($row['productname']);
				$tmp['count'] = $row['quantity'];
				$tmp['oneprice'] = round($row['price']/(1+$this->taxrate/100));  
				$tmp['tax_row'] = $row['price'] - $tmp['oneprice'];
//				$tmp['oneprice'] = $row['price'] - $tmp['tax_row'];				
				$tmp['price'] = $tmp['oneprice']*$tmp['count'];
				$tmp['tax_row'] *= $tmp['count'];  
				
				$tmp['etc'] = '';
				$tmp['sum'] = $tmp['tax_row'] + $tmp['price'];
				
								
				if(preg_match("/^COU/",$row['productcode'])) array_push($result['ext'],$tmp);
				//else if(preg_match("/^9999999999[0-9]{1}(X|R)/$",$row['productcode']) && $row['productcode'] !="99999999990X") array_push($result['ext'],$tmp);
				else if(preg_match("/^9999999999[1-9]{1}(X|R)$/",$row['productcode'])) array_push($result['ext'],$tmp);
				else array_push($result['product'],$tmp);
			}
			return $result;
		}
		return false;
	}
	
	
	function _request($param){
		if(!$this->setOrder($param['ordercode'])) return false;
		if(!_empty($this->billstatus)){
			$this->errmsg = '이미 신청 상태가 존재 합니다.';
			return false;
		}
		
		$this->companyinfo['receiver'] = array();	
		foreach($param as $k=>$v){
			switch(substr($k,0,2)){
				case 'r_':
					$this->companyinfo['receiver'][$k] = $v;
					break;
				case 'c_':
					$this->basicinfo[$k] = $v;
					break;
				default:
					break;
			}
		}
		
		if(preg_match('/^([0-9]{3})[*0-9]*([0-9]{2})[*0-9]*([0-9]{5})$/',$this->companyinfo['receiver']['r_number'],$mat)){
			$this->companyinfo['receiver']['r_number'] = $mat[1].'-'.$mat[2].'-'.$mat[3];
		}
		
		if(!_empty($this->basicinfo['c_cell']) && substr($this->basicinfo['c_cell'],0,2) != '01'){
			$this->basicinfo['c_phone']  = $this->basicinfo['c_cell'];
			$this->basicinfo['c_cell'] = '';
		}
		
		if(!_empty($this->basicinfo['c_phone']) && substr($this->basicinfo['c_phone'],0,2) == '01'){
			$this->basicinfo['c_cell']  = $this->basicinfo['c_phone'];
			$this->basicinfo['c_phone'] = '';
		}
		
		
		if(true !== $result = $this->validateCinfo($this->companyinfo['receiver'],'r')){
			$this->errmsg = $result;
			return false;
		}
		
		if(true !== $this->get_senderinfo()) return false;
		
		$this->basicinfo['d_type'] = HB_DOCUMENTTYPE_TAX;
		$this->basicinfo['kind'] = HB_TAXTYPE_TAX;
		$this->basicinfo['sendtype'] = HB_SENDTYPE_SEND;
		$this->basicinfo['detail_together_tax'] = '1';
		
		
		
		$dinfo['year'] = substr($this->orderinfo['ordercode'], 0, 4);
		$dinfo['mm'] = substr($this->orderinfo['ordercode'], 4, 2);
		$dinfo['dd'] = substr($this->orderinfo['ordercode'], 6, 2);
		
		// book_no && serial 산출
		$sql = "select max(book_no) from bill_basic";
		$res = mysql_query($sql,get_db_conn());		
		$bookno = mysql_result($res,0,0);		
		if(_empty($bookno)){
			$bookno = 1;
			$serial = 1;
		}else{			
			$sql = "select max(serial) from bill_basic whre book_no='".$bookno."'";
			$res = mysql_query($sql,get_db_conn());
			$serial = mysql_result($res,0,0);
			if(_empty($serial)) $serial = 1;
			else $serial = intval(str_replace('-',$serial));
			$bookno = intval(str_replace('-',$bookno));
			$serial++;
		}
		
		if($serial > 999999){
			$bookno++;
			$serial = 1;
		}
		
		$this->basicinfo['book_no'] = sprintf('%03d-%03d',@floor($bookno/1000),$bookno%1000);
		$this->basicinfo['serial'] = sprintf('%03d-%03d',@floor($serial/1000),$serial%1000);
		
		$sql = "insert into bill_basic set ordercode='".$this->orderinfo['ordercode']."',memid='".$this->basicinfo['memid']."',d_type='".$this->basicinfo['d_type']."',kind='".$this->basicinfo['kind']."',sendtype='".$this->basicinfo['sendtype']."',detail_together_tax='".$this->basicinfo['detail_together_tax']."',c_name='".$this->basicinfo['c_name']."',c_email='".$this->basicinfo['c_email']."',c_cell='".$this->basicinfo['c_cell']."',c_phone='".$this->basicinfo['c_phone']."',book_no='".$this->basicinfo['book_no']."',serial='".$this->basicinfo['serial']."',regdate=now(),status='R'";
		
		if(false === mysql_query($sql,get_db_conn())){ // 기본 정보
			$this->errmsg = mysql_error();
			return false;
		}
		$bill_idx = mysql_insert_id(get_db_conn());	
		
		$chkRollback = true;
		
		$sql = "insert into bill_company set bill_idx='".$bill_idx."'"; // 공급받는자 정보
		foreach($this->companyinfo['receiver'] as $cul=>$val){
			$sql .= ','.$cul."='".$val."'";
		}		
		$chkRollback = mysql_query($sql,get_db_conn());
		
		if($chkRollback){ // 문서 정보
			$sql = "insert into bill_document set bill_idx='".$bill_idx."',supplyprice='".$this->documentinfo['supplyprice']."',tax='".$this->documentinfo['tax']."',p_type='".$this->documentinfo['p_type']."'";
			$chkRollback = mysql_query($sql,get_db_conn());
		}
		
		if($chkRollback){ // 아이템 정보
			foreach($this->workinfo as $k=>$vals){
				$sql = "insert into bill_document_items set bill_idx='".$bill_idx."',mm='".$dinfo['mm']."',dd='".$dinfo['dd']."',subject='".$vals['subject']."',count='".$vals['count']."',oneprice='".$vals['oneprice']."',price='".$vals['price']."',tax_row='".$vals['tax_row']."',etc='".$vals['etc']."',sum='".$vals['sum']."'";
				if(false === $chkRollback = mysql_query($sql,get_db_conn())) break;
			}
		}
		
		if(!$chkRollback){ // 오류가 있을 경우
			$sql = "delect from bill_basic where bill_idx='".$bill_idx."'";
			@mysql_query($sql,get_db_conn());
			$sql = "delect from bill_company where bill_idx='".$bill_idx."'";
			@mysql_query($sql,get_db_conn());
			$sql = "delect from bill_document where bill_idx='".$bill_idx."'";
			@mysql_query($sql,get_db_conn());
			$sql = "delect from bill_document_items where bill_idx='".$bill_idx."'";
			@mysql_query($sql,get_db_conn());
			$this->errmsg = mysql_error();			
		}
		return $chkRollback;
	}
	
	function _chgRequest($param){
		if(!$this->setOrder($param['ordercode'])) return false;
		if($this->billstatus != 'R'){
			$this->errmsg = '수정 가능 상태가 아닙니다.';
			return false;
		}
		
		$receiver = array();
		$basic = array();
		foreach($param as $k=>$v){
			switch(substr($k,0,2)){
				case 'r_':
					if($this->companyinfo['receiver'][$k] != $v){
						$receiver[$k] = $v;
					}
					break;
				case 'c_':
					if($this->basicinfo['receiver'][$k] != $v){
						$basic[$k] = $v;
					}
					break;
				default:
					break;
			}
		}
		
		if(isset($receiver['r_number']) && preg_match('/^([0-9]{3})[*0-9]*([0-9]{2})[*0-9]*([0-9]{5})$/',$receiver['r_number'],$mat)){
			$receiver['r_number'] = $mat[1].'-'.$mat[2].'-'.$mat[3];
			if($this->companyinfo['receiver'] == $receiver['r_number']) unset($receiver['r_number']);
		}
		
		if(!_empty($basic['c_cell']) && substr($basic['c_cell'],0,2) != '01'){
			$basic['c_phone']  = $basic['c_cell'];
			unset($basic['c_cell']);
			//$this->basicinfo['c_cell'] = '';
		}
		
		if(!_empty($basic['c_phone']) && substr($basic['c_phone'],0,2) == '01'){
			$basic['c_cell']  = $basic['c_phone'];
			unset($basic['c_phone']);			
		}
		
		$this->companyinfo['receiver'] = array_merge($this->companyinfo['receiver'],$receiver);		
		if(true !== $result = $this->validateCinfo($this->companyinfo['receiver'],'r')){
			$this->errmsg = $result;
			return false;
		}
		
		$chkerr = true;
		if(_array($basic)){			
			$tmp = array();
			foreach($basic as $cul=>$val){
				$tmp[] = $cul."='".$val."'";
			}
			$sql = "update bill_basic set ".implode(',',$tmp)." where bill_idx = '".$this->basicinfo['bill_idx']."'";
			$chkerr = mysql_query($sql,get_db_conn());
		}
		
		if(_array($receiver)){
			$tmp = array();
			foreach($receiver as $cul=>$val){
				$tmp[] = $cul."='".$val."'";
			}
			$sql = "update bill_company set ".implode(',',$tmp)." where bill_idx = '".$this->basicinfo['bill_idx']."'";
			if(!mysql_query($sql,get_db_conn())) $chkerr = false;
		}
		if(!$chkerr){
			$this->errmsg = mysql_error();
			return false;
		}
		return true;
		
	}
	
	function _issueSend($param){
		
		$this->obj = new Hiworks_Bill_V2($this->config['domain'], $this->config['license_id'], $this->config['license_no'], $this->config['partner_id']);
		
		if(!$this->setBillByIdx($param['bill_idx'])) return false;
		if(!$this->get_senderinfo()) return false;
		
		foreach($this->basicinfo as $key=>$val){
			if(in_array($key,array('bill_idx','ordercode','memid','regdate','senddate','statue'))) continue;
			$this->obj->set_basic_info($key,$val);
		}
		
		foreach($this->documentinfo as $key=>$val){
			if(in_array($key,array('document_id'))) continue;
			else if($key == 'issue_date'){
				$val = date('Y-m-d');
			}
			$this->obj->set_document_info($key,$val);
		}
		
		foreach($this->companyinfo['sender'] as $key=>$val){
			$this->obj->set_company_info($key,$val);
		}
		
		foreach($this->companyinfo['receiver'] as $key=>$val){
			$this->obj->set_company_info($key,$val);
		}

		foreach($this->workinfo as $work){
			$this->obj->set_work_info($work['mm'],$work['dd'],$work['subject'],'EA',$work['count'],$work['oneprice'],$work['price'],$work['tax_row'],$work['etc'],$work['sum']);
		}
		
		$rs = $this->obj->send_document( HB_SOAPSERVER_URL );
		if (!$rs) {
		
			$sql = "update bill_basic set senddate=now(),status='E' where bill_idx='".$this->basicinfo['bill_idx']."'";
			
			@mysql_query($sql,get_db_conn());
			$sql = "insert into bill_log set bill_idx='".$this->basicinfo['bill_idx']."',msg='".$this->obj->_getError()."',rdate=now()";
			
			$this->errmsg = $this->obj->_getError();
			@mysql_query($sql,get_db_conn());
			return false;			
		}
		
		
		$sql = "update bill_basic set senddate=now(),status='S' where bill_idx='".$this->basicinfo['bill_idx']."'";
		@mysql_query($sql,get_db_conn());
		$sql = "update bill_document set document_id='".$this->obj->get_document_id['bill_idx']."',issue_date='".$this->basicinfo['issue_date']."' where bill_idx='".$this->basicinfo['bill_idx']."'";
		@mysql_query($sql,get_db_conn());
			
		
		return true;
		
	}
	
	function _checkDocumentStatus($docids=''){
		$ids = array();
		if(_array($docids)){
			foreach($docids as $tmp){
				if(is_string($tmp) && !_empty($tmp)) array_push($ids,$tmp);
			}
		}else if(is_string($docids) && !_empty($docids)){
			array_push($ids,$docids);
		}
		
		if(_array($ids)){
			$this->obj = new Hiworks_Bill_V2($this->config['domain'], $this->config['license_id'], $this->config['license_no'], $this->config['partner_id']);
			foreach($ids as $document_id){
				$this->obj->set_document_id($document_id);
			}
			$result = $this->obj->check_document(HB_SOAPSERVER_URL);
			
			$return = array();
			foreach($result as $stat){
				$return[$stat['document_id']] = explode('|',$stat['now_state']);
			}
			return $return;
		}
	}
}

?>