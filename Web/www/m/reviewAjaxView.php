	<!-- ¸®ºä ³»¿ë¹Ù·Îº¸±â -->
	<DIV id="reviewContents" style="position:absolute; display:none; background-color:#FFFFFF; z-index:1000;" >
		<DIV id="reviewContentsView"></DIV>
		<div><button class="button white bigrounded" style="width:100%; border-top:0px;" onclick="reviewContentsClose();">X ´Ý±â</button></div>
	</DIV>

	<script type="text/javascript">
	<!--
		var $p = jQuery.noConflict();
		function abspos(e){
			return e.clientY + (document.documentElement.scrollTop?document.documentElement.scrollTop:document.body.scrollTop);
		}

		function reviewOpen ( productcode, num, e ) {
			$p.post( "reviewContents.php", { "productcode":productcode, "num": num }).done(function( data ) {
				$p( "#reviewContentsView" ).html( data );
				pos = abspos(e);
				$p( "#reviewContents" ).css( "left", 9 );
				$p( "#reviewContents" ).css( "top", pos );
				$p( "#reviewContents" ).css( "width", "95%" );
				$p( "#reviewContents" ).css( "display","block" );
			});
		}

		function reviewContentsClose() {
			$p( "#reviewContents" ).css( "display","none" );
		}
	//-->
	</script>