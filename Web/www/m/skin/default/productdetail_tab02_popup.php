<!DOCTYPE HTML>
<html>
<head>
<!-- ���θ��±� -->
<meta name="description" content="">
<meta name="keywords" content="">
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">

<!-- �ٷΰ��� ������ -->
<link rel="apple-touch-icon-precomposed" href="" />
<meta name="apple-mobile-web-app-status-bar-style" content="black" />

<title><?=$row_shop[shopname]?> ���θ� - �����</title>
<link rel="stylesheet" href="../../css/common.css" />
<link rel="stylesheet" href="../../css/skin/default.css" />
<link rel="stylesheet" href="../../css/user.css" />

</head>


<div id="content">
	
	
	<!-- ��ǰ DETAIL -->
	<div class="pr_detail">
		
				
		
<!-- view�� -->


		
		<!-- TAB2-������ -->
		<section class="detail_02">
			<!-- <a href="#" target="_blank"><img src="img/@detail_sample01.png"></a>
			<a href="#" target="_blank"><img src="img/@detail_sample02.png"></a>	 -->
			<?
				if(strlen($detail_filter)>0) {
					$_pdata->content = preg_replace($filterpattern,$filterreplace,$_pdata->content);
				}

				if (strpos($_pdata->content,"table>")!=false || strpos($_pdata->content,"TABLE>")!=false)
					echo "<pre>".$_pdata->content."</pre>";
				else if(strpos($_pdata->content,"</")!=false)
					echo ereg_replace("\n","<br>",$_pdata->content);
				else if(strpos($_pdata->content,"img")!=false || strpos($_pdata->content,"IMG")!=false)
					echo ereg_replace("\n","<br>",$_pdata->content);
				else
					echo ereg_replace(" ","&nbsp;",ereg_replace("\n","<br>",$_pdata->content));
			?>

		</section>
		<!-- //TAB2-������ -->
	</div>
	<!-- //��ǰ DETAIL -->
</div>

<hr>

</body>
</html>