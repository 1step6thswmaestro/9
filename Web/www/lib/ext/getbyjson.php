<?
// ajax json 을 통한 백그라운드 실행 처리용 파일
error_reporting(0);
$Dir="../../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/ext/product_func.php");

$result = array();
array_walk($_REQUEST,'_iconvFromUtf8');
switch($_REQUEST['act']){
	case 'getProductGosiTitles':
		$items = _productGosiInfo();
		if(count($items) < 1){
			$result['err'] = '등록된 상품정보고시 항목이 없습니다.';
		}else{
			$result['err'] = 'ok';
			$result['items'] = $items;
		}
		break;
	case 'getProductGosiItems':
		if(!_isInt($_REQUEST['idx'],true)) $result['err'] = '식별 고유 번호가 전달되지 않았습니다.';
		else{
			$items = _productGosiInfo($_REQUEST['idx']);
			if(count($items) < 1){
				$result['err'] = '등록된 상품정보고시 세부 항목이 없습니다.';
			}else{
				$result['err'] = 'ok';
				$result['items'] = $items;
			}
		}
		break;
	default:
		$result['err'] = '정의되지 않은 메서드 입니다.';
		break;
}
if(!isset($result['err']) || empty($result['err'])) $result['err'] = 'ok';
if(PHP_VERSION > '5.2') array_walk($result,'_encode');
exit(json_encode($result));
?>
