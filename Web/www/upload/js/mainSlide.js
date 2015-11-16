// JavaScript Document
var $jj =jQuery.noConflict();
if(typeof slideDirH == "undefined") var slideDirH = false;

jQuery(function($j){   
	var totalCount = $j('.slideBanner').find('.tab_list').find('li').length;
	var tabWidth = conWidth/totalCount;	
	$j('.tab_list').find('li').css('width',tabWidth); //하단 탭부분 자동 넓이조정
	
	
	if(slideDirH == true){	// 좌우 이동일 경우
		$j('.slideBanner').find('.con_list').find('ul:eq(0)').css({width:conWidth*totalCount,height:conHeight});
		$j('.slideBanner').find('.con_list').css({width:conWidth,height:conHeight});
	}
	
	
	var count = 0;
	
	function slideBn(){
		$jtopVal = conHeight*count;
		$jleftVal = conWidth*count; // 추가 부분
		
		$jbn_list = $j('.slideBanner').find('.con_list').find('ul');
		if(slideDirH == true){	 // 좌우 이동일 경우
			$jbn_list.animate({left:-$jleftVal},aniTimer);
		}else{			
			$jbn_list.animate({top:-$jtopVal},aniTimer);
		}
		
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
		if(slideDirH == true){	 // 좌우 이동일 경우
			$jbn_list.stop(true,false).animate({left:-conWidth*$jcur_count},aniTimer,'easeOutQuad');
		}else{
			$jbn_list.stop(true,false).animate({top:-conHeight*$jcur_count},aniTimer,'easeOutQuad');
		}		
		clearTimeout(myTimer);
			
	},function(){
		slideBn();
	});
	
	var myTimer = setTimeout(slideBn,slideTimer,'easeOutQuad');
	
	slideBn();
})