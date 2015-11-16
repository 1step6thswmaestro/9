<?
	$access_token = "df0078c40e37667d0021261d341fa66612df2d7f";
	$curPageURL = "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
?>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script type="text/javascript">
<!--
	function shortUrl ( uri ) {
		$.ajax({url:"https://api-ssl.bitly.com/v3/shorten?format=txt&uri="+encodeURIComponent(uri)+"&access_token=<?=$access_token?>",success:function(result){
			$('#print').html("<?=$curPageURL?> => "+result);
		}});
	}
//-->
</script>
<input type="button" id="a" value="shortUrl" onclick="shortUrl('<?=$curPageURL?>');"><br />
<span id="print"></span>