<style type="text/css">
/*
	.tabUL{ margin:0px; padding:0px; list-style:none; margin-top:10px; display:block}
	.tabUL li{ width:23%; display:inline-block; float:left; padding:5px 0px; text-align:center; background:#efefef; border:1px solid #ccc; margin-right:4px; height:20px;}
	.tabUL .tabOn{ background:#64A7DD;}
	.tabUL .tabOff{ cursor:pointer}
*/

	.todaySaleBanner {height:350px; position:relative; border:1px solid #eeeeee;}

	.saleIcon {
		position:absolute;
		width:64px;
		height:62px;
		line-height:20px;
		margin:0px 10px;
		padding-top:26px;
		background-image:url(/images/common/todaysale/sale_ribbon.png);
		text-align:center;
		color:#ffffff;
		font-size:20px;
		font-family:Tahoma;
		font-weight:bold;
		z-index:100
	}
	
	.saleBanner {width:484px; overflow:hidden; z-index:1}
	
	/*
	.todayItemBox {
		width:235px;
		height:350px;
		float:right;
		background-image:url(/images/common/todaysale/todaysale_info.jpg);
	}
	*/

	.todayItemBox {width:49%; float:left;}
	.rightBox {width:49%; float:right;}
	.todayItemBox .sellPrice{font-family:Tahoma; font-size:20px; color:#30affe; font-weight:bold}
	.todayItemBox .productname{ font-size:13px; font-weight:bold; padding:5px;}
	.todayItemBox .price {padding:5px;}
	.todayItemBox .discount{ font-size:14px; color:red; font-weight:bold ; text-align:right; padding-right:10px;}
	.todayItemBox .sellCnt{ font-size:13px; color:#0CF; font-weight:bold}

	.todayItemBox ul {list-style:none; margin:0px; padding:0px;}
	.prName {padding:10px 0px; line-height:22px;}
</style>

<TABLE WIDTH=100% BORDER=0 CELLPADDING=0 CELLSPACING=0>
	<TR>
		<TD><a href="/todayshop/?ordby=best"><IMG SRC="../images/design/todaysale_tab01<?=((_empty($_REQUEST['ordby']) || $_REQUEST['ordby'] == 'best')?'r':'')?>.gif" ALT="" border="0"></a></TD>
		<TD><a href="/todayshop/?ordby=new"><IMG SRC="../images/design/todaysale_tab02<?=(($_REQUEST['ordby'] == 'new')?'r':'')?>.gif" ALT="" border="0"></a></TD>
		<TD><a href="/todayshop/?ordby=emd"><IMG SRC="../images/design/todaysale_tab03<?=(($_REQUEST['ordby'] == 'emd')?'r':'')?>.gif" ALT="" border="0"></a></TD>
		<TD><a href="/todayshop/?ordby=end"><IMG SRC="../images/design/todaysale_tab04<?=(($_REQUEST['ordby'] == 'end')?'r':'')?>.gif" ALT="" border="0"></a></TD>
		<TD width="100%" background="../images/design/gonggu_tap_bg.gif"></TD>
	</TR>
</TABLE>

<script language="javascript" type="text/javascript">
$j(function(){
	$j('.tabUL').find('li').each(function(idx,el){
		
		if($j(el).attr('ordby') == '<?=$_REQUEST['ordby']?>'){
			$j(el).addClass('tabOn');
		}else{
			$j(el).addClass('tabOff');
			$j(el).click(function(){ window.location.replace('/todayshop/?ordby='+$j(this).attr('ordby'));	});
		}
	});
});

</script>

<div style="width:100%; margin-top:20px;">

<?
$perline = 2;
$imgsize = 350; // 이미지 크기 제한
if(_array($items)){
	$i=0;
	foreach($items as $item){ 
		$datediff = solvTimestamp($item['remain']);

		if( fmod($i,$perline)==0 ){  // 줄바뀔 경우 처리
			if( $perline <= $i ) {
				?><div style="clear:both; height:20px;"></div><?
			}
			$addclass = '';
		} else {
			$addclass = 'rightBox';
		}
		$i++;

		$imgsrc = $imgstr = '';
	
		if(!_empty($item['maximage']) && file_exists($Dir.DataDir."shopimages/product/".$item['maximage'])) {
			$imgsrc = $Dir.DataDir."shopimages/product/".$item['maximage'];
			$size = getimagesize($imgsrc);
			
			if($size[1]>$size[0]){
				$imgstr =  ' height="'.(($size[1] > $imgsize)?$imgsize:$size[1]).'" ';
			}else{
				$imgstr =  ' width="'.(($size[0] > $imgsize)?$imgsize:$size[0]).'" ';
			}
		}else{
			$imgsrc.= $Dir."images/no_img.gif";
		}
		$discount = round(($item['consumerprice']-$item['sellprice'])/$item['consumerprice']*100);

		$icons = array();
		if(preg_match('/ICON=([0-9]+)/',$item['etctype'],$itmp)){
			for($j=0;$j<strlen($itmp[1]);$j+=2){
				$num = substr($itmp[1],$j,2);
				$file = $Dir.'images/common/icon'.$num.'.gif';
				if(file_exists($file)) array_push($icons,$file);
			}
		}

		$linkstr = '/todayshop/detail.php?productcode='.$item['productcode'].'&page='.$page; // 링크 주소
	?>

	<!-- 체험상품목록 반복 START -->
	<div class="todayItemBox <?=$addclass?>">
		<table border="0" cellpadding="0" cellspacing="0" width="100%" style="border:1px solid #dddddd;">
			<tr>
				<td style="border-bottom:1px solid #dddddd;" class="remainTimeBox" remain="<?=$item['remain']?>" endstamp='<?=strtotime($item['end'])?>'>

					<? if($item['remain'] < 1){ ?>
						<div style="float:left; width:73px; height:73px; background:#2db400 url('/images/common/todaysale/daybox_bg3.gif');">
							<div style="margin-top:50px; color:#ffffff; font-weight:bold; text-align:center;">마감</div>
						</div>
					<? }else{ ?>

						<? if(($item['deli_price'] == 0 && $item['deli'] == 'F') || ($item['deli_price'] == 0 && $item['deli'] == 'N' && $item['sellprice'] >= $_data->deli_miniprice)){ ?>
							<!--[무료배송]-->
							<div style="float:left; width:73px; height:73px; background:#2db400 url('/images/common/todaysale/daybox_bg1.gif');">
								<div style="margin-top:50px; color:#ffffff; font-weight:bold; text-align:center;">무료배송</div>
							</div>
						<? } ?>

						<? if($item['remain'] < 7*24*3600){ ?>
							<!--[마감임박]-->
							<div style="float:left; width:73px; height:73px; background:#e82900 url('/images/common/todaysale/daybox_bg2.gif');">
								<div style="margin-top:50px; color:#ffffff; font-weight:bold; text-align:center;">임박 D-<span style="font-weight:bold;" class="remainDay">0</span></div>
							</div>
						<? }else{ ?>
							<div style="float:left; width:73px; height:73px; background:#2db400 url('/images/common/todaysale/daybox_bg1.gif');">
								<div style="margin-top:50px; color:#ffffff; font-weight:bold; text-align:center;">D-<span style="font-weight:bold;" class="remainDay">0</span></div>
							</div>
						<? } ?>

					<? } ?>

					<div style="float:left; margin-left:20px;">
						<div><span style="color:#000; height:30px; line-height:30px; font-size:11px;">마감 : <b><?=substr($item['end'],0,10)?> <?=date('D',strtotime($item['end']))?></b> &nbsp;|&nbsp; <span style="color:#fd003b; font-weight:bold;"><?=$item['sellcnt']?></span> 명 구매</span></div>
						<div style="float:left; width:30px; height:30px; line-height:29px; font-family:verdana; color:#ffffff; font-size:24px; font-weight:bold; text-align:center; background:url('/images/common/todaysale/timebox_bg.gif') no-repeat; margin-right:3px; " class="remainHour1">0</div>
						<div style="float:left; width:30px; height:30px; line-height:29px; font-family:verdana; color:#ffffff; font-size:24px; font-weight:bold; text-align:center; background:url('/images/common/todaysale/timebox_bg.gif') no-repeat;" class="remainHour2">0</div>

						<div style="float:left;"><img src="/images/common/todaysale/timebox_dot_line.gif" alt="" /></div>

						<div style="float:left; width:30px; height:30px; line-height:29px; font-family:verdana; color:#ffffff; font-size:24px; font-weight:bold; text-align:center; background:url('/images/common/todaysale/timebox_bg.gif') no-repeat; margin-right:3px;" class="remainMin1">0</div>
						<div style="float:left; width:30px; height:30px; line-height:29px; font-family:verdana; color:#ffffff; font-size:24px; font-weight:bold; text-align:center; background:url('/images/common/todaysale/timebox_bg.gif') no-repeat;" class="remainMin2">0</div>

						<div style="float:left;"><img src="/images/common/todaysale/timebox_dot_line.gif" alt="" /></div>

						<div style="float:left; width:30px; height:30px; line-height:29px; font-family:verdana; color:#ffffff; font-size:24px; font-weight:bold; text-align:center; background:url('/images/common/todaysale/timebox_bg.gif') no-repeat; margin-right:3px;" class="remainSec1">0</div>
						<div style="float:left; width:30px; height:30px; line-height:29px; font-family:verdana; color:#ffffff; font-size:24px; font-weight:bold; text-align:center; background:url('/images/common/todaysale/timebox_bg.gif') no-repeat;" class="remainSec2">0</div>
					</div>
				</td>
			</tr>
			<tr>
				<td align="center" style="padding:10px 0px;">
					<table border="0" cellpadding="0" cellspacing="0" width="90%">
						<tr>
							<td align="center" height="300"><a href="<?=$linkstr?>"><img src="<?=$imgsrc?>" <?=$imgstr?> border="0" /></a></td>
						</tr>
						<tr><td height="10"></td></tr>
						<tr>
							<td>
								<a href="<?=$linkstr?>"><span style="color:#000; font-size:15px;"><?=$item['productname']?></span></a> 
								<? for($j=0;$j<count($icons);$j++){ ?>
									<div style="float:left;"><img src="<?=$icons[$j]?>" border="0" style="margin-right:2px;" /></div>
								<? } ?>
							</td>
						</tr>
						<tr><td height="10"></td></tr>
						<tr>
							<td style="padding:10px 0px; border-top:1px solid #e9e9e9; word-break:break-all;">

								<div style="float:left; width:35px; height:30px; background:url('/images/common/todaysale/icon_catebestprice.gif') no-repeat;"></div>
								<div style="float:left; margin-top:7px;">
									<span class="prconsumerprice"><s><?=number_format($item['consumerprice'])?>원</s></span>
									<span class="prconsumerprice">&gt;</span>
									<span style="color:#444444; font-size:20px; font-weight:bold; line-height:22px; font-family:verdana;"><?=number_format($item['sellprice'])?>원</span>
								</div>

								<div class="mainprlistsale" style="color:#fd003b; float:right; margin-top:8px; padding-left:10px; font-size:17px; font-weight:bold; font-family:verdana; border-left:1px solid #e9e9e9; text-align:right;"><?=$discount?>%</span></div>

							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</div>
	<!-- 체험상품목록 반복 END -->

<?
	} // end foreach
}// end if
?>
	<div style="text-align:center; clear:both; padding:20px 0px;" class="pageingarea"><?=$pagestr?></div>
</div>

</div>