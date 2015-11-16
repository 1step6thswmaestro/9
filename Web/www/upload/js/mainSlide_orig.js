// JavaScript Document
var $jj =jQuery.noConflict();

jQuery(function($j){   
	var totalCount = $j('.slideBanner').find('.tab_list').find('li').length;
	var tabWidth = conWidth/totalCount;
	$j('.tab_list').find('li').css('width',tabWidth); //하단 탭부분 자동 넓이조정
	var count = 0;
	
	function slideBn(){
		$jtopVal = conHeight*count;
		$jbn_list = $j('.slideBanner').find('.con_list').find('ul');
		$jbn_list.animate({top:-$jtopVal},aniTimer);
		$j('.slideBanner').find('.tab_list').find('li').find('a').eq(count).mouseover();
		if(count==totalCount-1){
			count = 0;
		}else{
			count++;
		}
		
		myTimer = setTimeout(slideBn,slideTimer,'easeOutQuad');
	}
	
	$j('.slideBanner').find('.tab_list').find('li').find('a').hover(function(){
		$jsel_tab = $j(this).parent('li');
		$jsel_tab.addClass('active');
		$jsel_tab.siblings('li').removeClass('active');
		$jcur_count = $j(this).parents('.tab_list').find('li').find('a').index(this);
		count = $jcur_count;
		$jbn_list.stop(true,false).animate({top:-conHeight*$jcur_count},aniTimer,'easeOutQuad');
		clearTimeout(myTimer);
			
	},function(){
		slideBn();
	});
	
	var myTimer = setTimeout(slideBn,slideTimer,'easeOutQuad');
	
	slideBn();
})