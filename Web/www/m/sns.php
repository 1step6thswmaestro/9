<div class="sns_wrap">
	<?
		$appid = $_SERVER['HTTP_HOST'];
		$appname = $_data->shopname;		
		if(empty($appname)) $appname = $_data->companyname;
		
		$mobilesns_sql = "SELECT use_mobile_sns FROM tblmobileconfig";
		$mobilesns_result = mysql_query($mobilesns_sql,get_db_conn());
		$mobilesns_row = mysql_fetch_object($mobilesns_result);

		$sns_set = explode('|',$mobilesns_row->use_mobile_sns);

		$set_kakaotalk = $sns_set[0];
		$set_kakaostory = $sns_set[1];
		$set_facebook = $sns_set[2];
		$set_twitter = $sns_set[3];
		

		$imagesrc =$Dir."/data/shopimages/product/".$_pdata->maximage;
		$imgsize = array();
		$imgsize = getimagesize($imagesrc);
		$imagecapacity = filesize($imagesrc);
		$sendmaxcapacity ="512000";

		$shareWidth=!_empty($imgsize[0])?trim($imgsize[0]):"";
		$shareHeight = !_empty($imgsize[1])?trim($imgsize[1]):"";
		$protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"],0,5))=='https'?'https':'http';
		$kakao_primagesrc = $protocol.'://'.$_SERVER['HTTP_HOST']."/data/shopimages/product/".$_pdata->maximage;
		$kakao_returnurl = $protocol."://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']."?productcode=".$_pdata->productcode;
		$kakao_prname = $_pdata->productname;
		$kakao_prcontents = trim(strip_tags($_pdata->content));

		$kakaoinfoSQL = "SELECT state, secret FROM tblshopsnsinfo WHERE type ='k' ";

		$kakaousestate = $kakaousekey = "";
		if(false !== $kakaoinfoRes = mysql_query($kakaoinfoSQL,get_db_conn())){
			$kakaoinfocount = mysql_num_rows($kakaoinfoRes);
			if($kakaoinfocount>0){
				$kakaousestate = trim(mysql_result($kakaoinfoRes,0,0));
				$kakaousekey = trim(mysql_result($kakaoinfoRes,0,1));
			}
		}
		
	?>
	<form name="snsreseveForm" action="promotion_payreserve_proc.php" method="post" >
		<input type="hidden" name="prcode" value="<?=$_pdata->productcode?>"/>
		<input type="hidden" name="promotiontype" value=""/>
	</form>
	<script>
		function kakaoLink(type){
			var userid = "<?=$_ShopInfo->getMemid()?>";
			var _reserveForm = document.snsreseveForm;
			_reserveForm.promotiontype.value = type;
			if(userid.length <= 0 || userid == ""){
				if(confirm("로그인 되어있지 않아 적립금을 받을 수 없습니다.\n로그인 하시겠습니까?")){
					window.location='/m/login.php?chUrl='+"<?=getUrl()?>";
					return;
				}
			}else{
				alert("최종 홍보 등록 완료후 적립금이 지급 됩니다.");
				if(_reserveForm.promotiontype.value.length > 0 && _reserveForm.promotiontype.value == type ){
					_reserveForm.target="PROMOTION";
					_reserveForm.submit();
				}
			}
		}
		function kakaocall(type){
			var productname = "<?=$kakao_prname?>";
			var returnurl = "<?=$kakao_returnurl?>";
			var appid = "<?=$appid?>";
			var appname = "<?=$appname?>";
			var contents = "<?=$kakao_prcontents?>";
			var imagesrc = "<?=$kakao_primagesrc?>";
			var imagewidth = "<?=$shareWidth?>";
			var imageheight = "<?=$shareHeight?>";
			var imagecapacity = "<?=$imagecapacity?>";
			var sendmaxcapacity = "<?=$sendmaxcapacity?>";
			var kakaousestate = "<?=$kakaousestate?>";
			var kakaokey = "<?=$kakaousekey?>";
			switch(type){
				case "KT":
				if(kakaousestate == "Y" && kakaokey.length > 0){
					if(imagecapacity>sendmaxcapacity){
						if(confirm("첨부가능 용량을 초과하였습니다.\n첨부가능한 용량은 500KB로\n그대로 진행할 경우\n이미지가 손상될수 있습니다.\n계속하시겠습니까?")){
							sendLink(kakaokey,imagesrc,productname,returnurl,imagewidth,imageheight);
						}else{
							return;
						}
					}else{
						sendLink(kakaokey,imagesrc,productname,returnurl,imagewidth,imageheight);
						return;
					}
				}else{
					alert("카카오 키가 발급이 되어있지 않거나\n사용설정이 되어있지 않습니다.");
					return;
				}
				break;
				case "KS":
					executeKakaoStoryLink(returnurl,appid,appname,productname,contents,imagesrc);
				break;
				case "FB":
				break;
				case "TW":
				break;
			}
		}
		
		function sendLink(kakaokey,imagesrc,productname,returnurl,imagewidth,imageheight){
			Kakao.init(kakaokey);
			Kakao.Link.sendTalkLink({
				label: productname,
				image : {
					src : imagesrc,
					width : imagewidth,
					height : imageheight
				},
				webButton :{
					text : '방문하기',
					url : returnurl
				}
			});
		}
		function snsSendProc(type){
			var _form = document.snsprocForm;
			_form.snstype.value=type;
			_form.submit();
		}
	</script>

	<form name="snsprocForm" action="prsns_proc.php" method="post" target="SNSPROC">
		<input type="hidden" name="prcode" value="<?=$productcode?>"/>
		<input type="hidden" name="snstype" value=""/>
	</form>
	<iframe id="PROMOTION" name="PROMOTION" style="display:none"></iframe>
	<iframe id="SNSPROC" name="SNSPROC" style="display:none"></iframe>
	<?if($set_kakaotalk == 'Y'){?>
		<div class="snskakaotalk" onclick="kakaocall('KT')" /></div>
	<?}?>
	
	<?if($set_kakaostory == 'Y'){?>
	<div class="snskakaostory" onclick="kakaocall('KS')" /></div>
	<?}?>

	<?
		//Twitter, Facebook
		if($_data->sns_ok == "Y"){
			if(TWITTER_ID !="TWITTER_ID")
				echo "<input type=\"hidden\" name=\"tLoginBtnChk\" id=\"tLoginBtnChk\">";
			if(FACEBOOK_ID !="FACEBOOK_ID")
				echo "<input type=\"hidden\" name=\"fLoginBtnChk\" id=\"fLoginBtnChk\">";
		}
	?>
		<?if($set_twitter == 'Y'){?>
			<div class="snstwitter" onclick="snsSendProc('TW');" id="tLoginBtn0" /></div>
		<?}?>
		<?if($set_facebook == 'Y'){?>
			<div class="snsfacebook" onclick="snsSendProc('FB');" id="tLoginBtn0" /></div>
		<?}?>
	
</div>
