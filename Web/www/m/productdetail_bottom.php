
<?if($_data->sns_ok == "Y" && ($_pdata->sns_state == "Y" || $_pdata->gonggu_product == "Y")){?>

<script>var $j = jQuery.noConflict();</script>
<script type="text/javascript" src="./js/sns.js"></script>
<script type="text/javascript">
<!--
var pcode = "<?=$productcode ?>";
var memId = "<?=$_ShopInfo->getMemid() ?>";
var fbPicture ="<?=$fbThumb?>";
var preShowID ="";
var snsCmt = "";
var snsLink = "";
var snsType = "";
var gRegFrm = "";

$j(document).ready( function () {
	if(memId != ""){
		snsImg();
		snsInfo();
	}
	showSnsComment();
	showGongguCmt();
});
//-->
</script>
<? include ($Dir.FrontDir."snsGongguToCmt.php") ?>
<?}?>

<form name=couponform method=get action="<?=$_SERVER[PHP_SELF]?>">
<input type=hidden name=mode value="">
<input type=hidden name=coupon_code value="">
<input type=hidden name=productcode value="<?=$productcode?>">
<?=($brandcode>0?"<input type=hidden name=brandcode value=\"".$brandcode."\">\n":"")?>
</form>
<form name=idxform method=get action="<?=$_SERVER[PHP_SELF]?>">
<input type=hidden name=productcode value="<?=$productcode?>">
<input type=hidden name=sort value="<?=$sort?>">
<input type=hidden name=block value="<?=$block?>">
<input type=hidden name=gotopage value="<?=$gotopage?>">
<input type=hidden name=qnablock value="<?=$qnablock?>">
<input type=hidden name=qnagotopage value="<?=$qnagotopage?>">
<?=($brandcode>0?"<input type=hidden name=brandcode value=\"".$brandcode."\">\n":"")?>
</form>

<form name=wishform method=post action="confirm_wishlist.php" >
<input type=hidden name=productcode value="<?=$productcode?>">
<input type=hidden name=opts>
<input type=hidden name=option1>
<input type=hidden name=option2>
</form>

<?if($_pdata->vender>0){?>
<form name=custregminiform method=post>
<input type=hidden name=sellvidx value="<?=$_vdata->vender?>">
<input type=hidden name=memberlogin value="<?=(strlen($_ShopInfo->getMemid())>0?"Y":"N")?>">
</form>
<?}?>


<div id="create_openwin" style="display:none"></div>


<? include "footer.php";?>