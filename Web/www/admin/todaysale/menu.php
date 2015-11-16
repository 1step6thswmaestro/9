<?

$leftMenus = array(
				array('name'=>'투데이세일',
					  'links'=>array(
					  			array("/admin/todaysale.php",'상품 목록'),
								array("/admin/todaysale.php?mode=new",'상품 등록'),
								array("/admin/todaysale.php?mode=orders",'주문 목록')								
							  )
				)			
			);				
$pidx = $sidx = 0;

switch($_SERVER['PHP_SELF']){
	case '/admin/todaysale.php':		
		switch($_REQUEST['mode']){
			case 'new': $sidx=1; break;
			case 'orders': $sidx=2; break;				
		}
		break;
}

?>
<SCRIPT LANGUAGE="JavaScript">
<!--
$(function(){
	$('.leftMenuItem>dl>dt').each(function(idx,el){
		$(this).css('cursor','pointer');
		$(this).click(function(){ toggleMenu(this);});		
	});
	toggleMenu($('.leftMenuItem>dl>dt.select'));
	
});

function toggleMenu(el){
	var midx = $('.leftMenuItem>dl>dt').index(el);	
	if(midx >= 0){		
		if($(el).hasClass('select')){
			if(!$(el).hasClass('selfix')){
				$(el).removeClass('select');
				$('.leftMenuItem>dl>dd:eq('+midx+')').css('display','none');
			}else{
				$('.leftMenuItem>dl>dd:eq('+midx+')').css('display','block');	
			}
		}else{
			$(el).addClass('select');
			$('.leftMenuItem>dl>dd:eq('+midx+')').css('display','');
		}
	}
}
//-->
</SCRIPT>
<style type="text/css">
.leftMenuItem{ background:url(/admin/images/leftmenu_line.gif) top left repeat-x; padding-top:3px;}
.leftMenuItem dl{ margin:0px; padding:0px;}
.leftMenuItem dt{ color:#fff; font-weight:bold; font-size:11px; letter-spacing:-0.5px; background:url(/admin/images/icon_leftmenu.gif) 20px 48% no-repeat; padding:5px 0px 5px 40px;}
.leftMenuItem dd{display:none; padding:0px;}
.leftMenuItem ul{ padding:0px; margin:0px; margin-bottom:20px; list-style:none}
.leftMenuItem ul li{ background:url(/admin/images/icon_leftmenu1.gif) left 50% no-repeat; padding:3px 0px 3px 13px;}


.leftMenuItem dd ul li a,.leftMenuItem dd ul li a:hover,.leftMenuItem dd ul li a:visited{ text-decoration:none; color:#bbbbbd}

.leftMenuItem dt.select{ background:url(/admin/images/icon_leftmenu_select.gif) 20px 48% no-repeat;}

.leftMenuItem dd ul li.select a,.leftMenuItem dd ul li.select a:hover,.leftMenuItem dd ul li.select a:visited{color:#fe8e4b; font-weight:bold}


</style>
<table width="100%" BORDER="0" CELLPADDING="0" CELLSPACING="0" >
	<TR>
		<TD height="68" align="right" valign="top" background="/admin/images/todaysale_leftmenu_title.gif" ><a href="javascript:scrollMove(0);"><img src="/admin/images/leftmenu_stop.gif" border="0" id="menu_pix"></a><a href="javascript:scrollMove(1);"><img src="/admin/images/leftmenu_trans.gif" border="0" hspace="2" id="menu_scroll"></a></TD>
	</TR>
	<TR>
		<TD  background="/admin/images/leftmenu_bg.gif">
			<div class="leftMenuItem">
				<dl>
				<? foreach($leftMenus as $idx=>$menuitem){ 
						$classstr = ($idx == $pidx)?' class="select selfix"':'';
				?>
					<dt <?=$classstr?>><?=$menuitem['name']?></dt>
					<dd>
						<ul>
						<? foreach($menuitem['links'] as $iidx=>$links){ 
								$sclassstr = (!_empty($classstr) && $sidx == $iidx)?'class="select"':'';
						?>
							<li <?=$sclassstr?>><a href="<?=$links[0]?>"><?=$links[1]?></a></li>
						<? } ?>
						</ul>
					</dd>
				<? } ?>
				</dl>
			</div>			
		</TD>
	</TR>
</table>