<? 
	include_once('header.php');
	
	$agreement="";
	$agreementSQL ="SELECT agreement FROM tbldesign";
	if(false !== $agreementRes = mysql_query($agreementSQL,get_db_conn())){
		$rowcount = mysql_num_rows();
		if($rowcount>0){
			$agreement = mysql_result($agreementRes);
		}else{
			$fa = fopen($Dir.AdminDir."agreement.txt","r");
			if($fa){
				while(!feof($fa)){
					$fbuffer .=fgets($fa,1024);
				}
			}
			fclose($fa);
			$agreement = $fbuffer;
		}
		mysql_free_result($agreementRes);
	}

	if(strlen($agreement) <= 0){
		echo '<script>alert("이용약관 설정이 되어 있지 않습니다.");history.go(-1);</script>';exit;
	}

	$pattern=array("(\[SHOP\])","(\[COMPANY\])");
	$replace=array($_data->shopname, $_data->companyname);
	$agreement = preg_replace($pattern,$replace,$agreement);
?>
<div id="content">
	<div class="h_area2">
		<h2>이용약관</h2>
		<a href="main.php" class="btn_home" rel="external"><span class="vc">홈</span></a>
		<a href="javascript:history.back()" class="btn_prev" rel="external"><span>이전</span></a>
	</div>
	
	<section id="sec_agreement_wrap">
		<?=$agreement?>
	</section>
</div>
<? 
	include_once('footer.php'); 
?>
