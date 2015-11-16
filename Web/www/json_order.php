<?
/*
$Dir="./";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
require_once($Dir."lib/ext/func.php");
require_once($Dir."lib/ext/coupon_func.php");

$imagepath=$Dir.DataDir."shopimages/etc/";

$result = array();
$result['items'] = array();
try{
	switch($_REQUEST['act']){
		case 'getGife':	// 상품 조회
			array_walk($_REQUEST,'_iconvFromUtf8');
			if(_isInt($_REQUEST['gift_price'])){
				$sql = "SELECT * FROM tblgiftinfo WHERE gift_startprice<=".$_REQUEST['gift_price']." AND gift_endprice>".$_REQUEST['gift_price']." AND (gift_quantity is NULL OR gift_quantity>0) ORDER BY gift_regdate ";
				if(false === $res = mysql_query($sql,get_db_conn())) throw new ErrorException(mysql_error());
				if(mysql_num_rows($res)){
					//$result['items'] = array();
					$tmp = array();
					while($item = mysql_fetch_assoc($res)){
						$lastr = '<div class="st13_2" style="display:none" id="gift_'.$item['gift_regdate'].'">';
						for($k=1;$k<5;$k++){
							if (strlen($item['gift_option'.$k])>0) {
								$gift_option = explode(",",$item['gift_option'.$k]);
								$lastr .= '<div style="clear:both;"><div class="st13_2_1" style="float:left;width:80px;" >'.$gift_option[0].'</div><div style="float:left"><select name="option'.$k.'_'.$item['gift_regdate'].'" class="st13_2_2">';
								for ($j=1;$j<count($gift_option);$j++) {
									$gift_vls=explode(":",$gift_option[$j]);
									if($gift_vls[1]==0 && strlen($gift_vls[1])==1) {
										$adds1 = "(품절)";
										$adds2 = "disabled";
									}
									else $adds1 = $adds2 = '';
									$lastr .= "<option value=\"".$gift_option[0].",".$gift_vls[0]."\" {$adds2}>".$gift_vls[0].$adds1."</option>\n";
								}
								$lastr .= '</select></div></div>';
							}
						}
						if (strlen($item['gift_image'])>0 && file_exists($imagepath.$item['gift_image'])) {
							$g_img = "/".$imagepath.$item['gift_image'];
						} else {
							$g_img = "/images/no_img.gif";
						}

						$lastr .= '<input type="hidden" name="img_'.$item['gift_regdate'].'" value="'.$g_img.'"></div>';
						$item['divstr'] = $lastr;
						array_push($tmp,$item);
					}
					$result['items'] = $tmp;
				}
			}
			break;
		default:
			throw new ErrorException('정의되지 않은 실행 입니다.');
			break;
	}
	$result['err']='ok';
}catch(Exception $e){
	$result['err'] = $e->getMessage();
}

// php  5.2.0 이상은 추가
$phpVer = str_replace(".","",phpversion());
if( $phpVer >= 520 ) array_walk($result,'_encode');

echo json_encode($result);
exit;
?>*/

$Dir="./";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
require_once($Dir."lib/ext/func.php");
require_once($Dir."lib/ext/coupon_func.php");

$imagepath=$Dir.DataDir."shopimages/etc/";
$result = array();
$result['items'] = array();

try{
	switch($_REQUEST['act']){
		case 'getGife':	// 상품 조회			
			//if(!_isInt($_REQUEST['gift_price'])) throw new InvalidArgumentException('구매 가격 오류');
			if(_isInt($_REQUEST['gift_price'],true)){
			
				$sql = "SELECT * FROM tblgiftinfo WHERE gift_startprice<=".$_REQUEST['gift_price']." AND gift_endprice>".$_REQUEST['gift_price']." AND (gift_quantity is NULL OR gift_quantity>0) ORDER BY gift_regdate ";
				if(false === $res = mysql_query($sql,get_db_conn())) throw new ErrorException(mysql_error());

				if(mysql_num_rows($res)){					
					while($item = mysql_fetch_assoc($res)){
						$optemp = array();
						$existOption = false;
						
						for($k=1;$k<5;$k++){
							if(!_empty($item['gift_option'.$k])){
								$gift_option = explode(",",$item['gift_option'.$k]);
								$optemp[$k] = array('name'=>$gift_option[0],'items'=>array());							
								
								for($j=1;$j<count($gift_option);$j++){
									if(!$existOption)  $existOption = true;
									$gift_vls=explode(":",$gift_option[$j]);								
									if($gift_vls[1] == '0') continue; // 품절 항목은 건너뜀
									array_push($optemp[$k]['items'],$gift_vls);
								}
								if(count($gift_option) > 1 && count($optemp[$k]['items']) < 1) unset($optemp[$k]);
							}						
						}
						if($existOption && count($optemp) < 1) continue;					
						$item['options'] = $optemp;
						$g_img = (!_empty($item['gift_image']) && file_exists($imagepath.$item['gift_image']))?"/".$imagepath.$item['gift_image']:"/images/no_img.gif";																					
						array_push($result['items'],$item);
					}
				}
			}
			break;
		case 'couponProduct':		
			if(_empty($_REQUEST['coupon_code']) || _empty($_REQUEST['id']) || _empty($_REQUEST['key']) ) throw new InvalidArgumentException('전달값 오류'); 	
			$items = getAbleProductOnBasket($_REQUEST['coupon_code'],$_REQUEST['id'],$_REQUEST['key']);
			$result['cnt'] = count($items);
			$result['items'] = $items;
			break;
		default:
			throw new ErrorException('정의되지 않은 실행 입니다.');
			break;
	}
	$result['err']='ok';
	$result['itemcnt']=count($result['items']);
}catch(Exception $e){
	$result['err'] = $e->getMessage();
	$result['itemcnt'] = 0;
}

// php  5.2.0 이상은 추가
$phpVer = str_replace(".","",phpversion());
if( $phpVer >= 520 ) array_walk($result,'_encode');

echo json_encode($result);
exit;
?>