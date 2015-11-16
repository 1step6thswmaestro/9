<?
//member_groupnew_couponList.php

$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/ext/coupon_func.php");
INCLUDE ("access.php");


	header("Content-Type: text/plain");
	header("Content-Type: text/html; charset=euc-kr");

	$groupCode=$_REQUEST['groupCode'];

	//쿠폰을 사용할 경우
	$coupon_body= '<table cellpadding="0" cellspacing="0" border="0" width="100%" style="padding:3px;"><tr>';


	$coupon_sql = "
		SELECT
			*
		FROM
			group_coupon AS GC
			inner join tblcouponinfo AS CI ON GC.coupon_code = CI.coupon_code
		WHERE
			GC.group_code = '".$groupCode."'
	";
	$coupon_result = mysql_query($coupon_sql,get_db_conn());
	$i=0;
	$cperline = 5;
	$loop = ceil(count(mysql_num_rows ( $coupon_result ))/$cperline)*$cperline;
	while( $coupon_row = mysql_fetch_object ( $coupon_result ) ){
		$date2 = ($coupon_row->date_start>0)?substr($coupon_row->date_start,0,42)."/".substr($coupon_row->date_start,4,2)."/".substr($coupon_row->date_start,6,2)." ~ ".substr($coupon_row->date_end,0,4)."/".substr($coupon_row->date_end,4,2)."/".substr($coupon_row->date_end,6,2):"발급일로부터 ".abs($coupon_row->date_start)."일";
		//date("Y/m/d")." ~ ".date("Y/m/d",mktime(0,0,0,date("m"),date("d")+abs($coupon_row->date_start),date("Y")));

		if($i > 0 && $i%$cperline == 0) $coupon_body .= '</tr><tr>';
		$coupon_body .= '<td>';
		$coupon_body .= '	<div style="border:3px solid #ddd; width:150px; min-height:55px; height:55px;">';

		$coupon_name = addslashes($coupon_row->coupon_name);
		$coupon_desc = number_format($coupon_row->sale_money).($coupon_row->sale_type<=2?"%":"원").($coupon_row->sale_type%2==0?"할인":"적립")."쿠폰";

		if(file_exists($Dir.DataDir."shopimages/etc/COUPON".$coupon_row->coupon_code.".gif")) {
			$coupon_body .= '		<div style="text-align:center">';
			$coupon_body .= '			<img src="'.$Dir.DataDir.'shopimages/etc/COUPON'.$coupon_row->coupon_code.'.gif\" border=0>';
			$coupon_body .= '		</div>';
		}else{
			$coupon_body .= '		<ul style="list-style:none; margin:0px; padding:0px;">';
			$coupon_body .= '			<li style="width:100%; color:#bbb; font-family:verdana; font-size:10px; font-weight:bold; padding-left:5px;">COUPON</li>';
			//$coupon_body .= '			<li style="float:right; width:100%; color:#ff3300; text-align:center; line-height:30px; font-weight:bold;"><span style="font-family:verdana; font-size:20px; letter-spacing:-0.1em;">10</span>% 할인</li>';
			$coupon_body .= '			<li style="float:right; width:100%; color:#ff3300; text-align:center; line-height:30px; font-weight:bold;"><span style="font-family:verdana; font-size:20px; letter-spacing:-0.1em;">'.number_format($coupon_row->sale_money).'</span>'.($coupon_row->sale_type<=2?"%":"원").' '.($coupon_row->sale_type%2==0?"할인":"적립").'</li>';
			$coupon_body .= '		</ul>';
		}
		$coupon_body .= '	</div>';

		$coupon_body .= '
			<div style="width:150px; height:30px; margin-top:5px; text-align:center;">
				<a href="javascript:return false;" onMouseOver="showInfo'.$i.'.style.visibility=\'visible\';" onMouseOut="showInfo'.$i.'.style.visibility=\'hidden\';">쿠폰정보</a>
				|
				<a href="javascript:choiceCoupon ( \''.$groupCode.'\', \''.$coupon_row->coupon_code.'\', false ); " >삭제</a>
			</div>
		';

		$coupon_body .= '	<div id="showInfo'.$i.'" style="width:210px; margin:0px; margin-top:-12px; padding:10px; position:absolute; background:#ffffff; color:#666; font-size:11px; border:1 solid #ccc; visible; z-index:100; visibility:hidden;">';
		$coupon_body .= '		<span style="color:#444; font-size:12px; font-weight:bold;">쿠폰명 : '.$coupon_name.'</span><br />';
		$coupon_body .= $coupon_row->description.'<br />';
		$coupon_body .= '		사용기간 : '.$date2.'<br />';
		if($coupon_row->bank_only=="Y") $coupon_body.=" <font color=\"0000FF\">(현금결제만 가능)</font><br />";

		$productList = usableProductOnCoupon($coupon_row->productcode);
		if($coupon_row->use_con_type2=="N") $coupon_body .= '		적용대상 : '.'['.$productList.'] 제외';
		else $coupon_body .= '		적용대상 : '.$productList.'';
		$coupon_body .= '	</div>';
		$i++;
	}

	for(;$i<$loop;$i++){
		$coupon_body .= '<td width="25%"></td>';
	}
	$coupon_body .= '</tr></table>';


	echo $coupon_body;
?>

