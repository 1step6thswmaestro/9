<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");


$basketItems = getBasketByArray();

//_pr($basketItems);

echo $chkprice = $_REQUEST['chkprice'];
echo "<br>";

					if( strlen($_ShopInfo->getMemgroup()) > 0 ) {

						// 그룹 회원 배송비 무료 정책 ( 타입 - 1:기본, 2:무료, 3:금액이상 )
						$groupDeli = memberGroupDelivery ( $_ShopInfo->getMemgroup() );
						//// 타입 "1" 기본 배송 정책 패스~
						//// 타입 "2" 무료
						if( $groupDeli['type'] == "2" ) {
							$basketItems['deli_price'] =0;
						}
						//// 타입 "3"금액이상 무료
						if( $groupDeli['type'] == "3" AND $chkprice >= $groupDeli['money'] ) {
							$basketItems['deli_price'] = 0;
						}
					}
					echo "<hr>";
		echo "총 배송비 : ".$basketItems['deli_price']."<br>";
?>