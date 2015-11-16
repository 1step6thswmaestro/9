// JavaScript Document



/*jQuery(function($){
	
})*/

//$(document).ready(init);
/////슬라이드
 /*function init()
  {
	  
	  $(".pg_num_area1").find(".pg_num").eq(0).addClass("pg_num_on");
	  
	$(".slideArea").each(function() {
		$slide_leng = $(this).find('.slideWrap .slideBox .slide').length;
		$wrapWidth = $slide_leng*100;
		$(this).find(".slideWrap .slideBox").css('width',$wrapWidth+'%');
		$(this).find(".slideWrap .slideBox .slide").css('width',100/$slide_leng+"%");
		$(this).parent("div").find(".slidePg").find(".pg_area span").html($slide_leng);
	});
	
    $(".slideArea").on("swipeleft", function (event)
    {
		$wrap = $(this);
		leftMove($(this));
    });
    
    $(".slideArea").on("swiperight", function (event)
    {
		$wrap = $(this);
		leftMove($(this));
    });
	
	$(".slidePg").find(".pg_btn_next").click(function(){
		$wrap = $(this).parent(".slidePg").parent("div").find(".slideArea");
		leftMove($wrap);
	});
	
	$(".slidePg").find(".pg_btn_prev").click(function(){
		$wrap = $(this).parent(".slidePg").parent("div").find(".slideArea");
		rightMove($wrap);
	});
	
	function leftMove(a){
		a.find(".slideWrap .slideBox").stop(true,true);
		$cur_left = parseInt(a.find('.slideWrap .slideBox').css('left').replace('%',''));
		$total_left = a.find('.slideWrap .slideBox .slide').length*-100+100;
		if($cur_left==$total_left){
			$wrap_left = 0;
		}else{
			$wrap_left = $cur_left-100;
		}
		$act_num = -($wrap_left/100);
		
		changNum(a,$act_num);
		
		a.find(".slideWrap .slideBox").animate({'left' : $wrap_left+'%'},700);
	}
	
	function rightMove(a){
		a.find(".slideWrap .slideBox").stop(true,true);
		$cur_left = parseInt(a.find('.slideWrap .slideBox').css('left').replace('%',''));
		$total_left = a.find('.slideWrap .slideBox .slide').length*-100+100;
		if($cur_left==0){
			$wrap_left = $total_left;
		}else{
			$wrap_left = $cur_left+100;
		}
		$act_num = -($wrap_left/100);
		
		changNum(a,$act_num);
		a.find(".slideWrap .slideBox").animate({'left' : $wrap_left+'%'},700);
	}
	
	function changNum(a,b){
		$act_pg = a.parent("div").find(".pg_num_area1").find(".pg_num").eq($act_num);
		$act_pg.addClass("pg_num_on");
		$act_pg.siblings(".pg_num").removeClass("pg_num_on");		
		$pg2 = a.parent("div").find(".slidePg").find(".pg_area");
		$pg2.find("em").html(b+1);
	}
	
   }
   */
   

//////////////////////////

//var quickview_path="../front/product.quickview.xml.php";
//var quickfun_path="../front/product.quickfun.xml.php";
/*
function sendmail() {
	window.open("../front/email.php","email_pop","height=100,width=100");
}

function estimate(type) {
	if(type=="Y") {
		window.open("../front/estimate_popup.php","estimate_pop","height=100,width=100,scrollbars=yes");
	} else if(type=="O") {
		if(typeof(top.main)=="object") {
			top.main.location.href="../front/estimate.php";
		} else {
			document.location.href="../front/estimate.php";
		}
	}
}
function privercy() {
	window.open("../front/privercy.php","privercy_pop","height=570,width=590,scrollbars=yes");
}
function order_privercy() {
	window.open("../front/privercy.php","privercy_pop","height=570,width=590,scrollbars=yes");
}

function sslinfo() {
	window.open("../front/sslinfo.php","sslinfo","width=100,height=100,scrollbars=no");
}
function memberout() {
	if(typeof(top.main)=="object") {
		top.main.location.href="../front/mypage_memberout.php";
	} else {
		document.location.href="../front/mypage_memberout.php";
	}
}
function notice_view(type,code) {
	if(type=="view") {	
		window.open("../front/notice.php?type="+type+"&code="+code,"notice_view","width=450,height=450,scrollbars=yes");
	} else {
		window.open("../front/notice.php?type="+type,"notice_view","width=450,height=450,scrollbars=yes");
	}
}
function information_view(type,code) {
	if(type=="view") {	
		window.open("../front/information.php?type="+type+"&code="+code,"information_view","width=600,height=500,scrollbars=yes");
	} else {
		window.open("../front/information.php?type="+type,"information_view","width=600,height=500,scrollbars=yes");
	}
}
function GoPrdtItem(prcode) {
	window.open("../front/productdetail.php?productcode="+prcode,"prdtItemPop","WIDTH=800,HEIGHT=700 left=0,top=0,toolbar=yes,location=yes,directories=yse,status=yes,menubar=yes,scrollbars=yes,resizable=yes");
}
*/

function logout() {
	//location.href="../main/main.php?type=logout";
	location.href="logout.php";
}

function TopSearchCheck() {
	try {
		if(document.search_tform.search.value.length==0) {
			alert("상품 검색어를 입력하세요.");
			document.search_tform.search.focus();
			return;
		}
		document.search_tform.submit();
	} catch (e) {}
}



function sendsns(type,title,shop_url,site_name) 
{
	//var shop_url = shop_url;
	
	switch(type)
	{

		case "twitter" :
			var link = 'http://twitter.com/home?status=' + encodeURIComponent(title) + ' : ' + encodeURIComponent(shop_url);
			var w = window.open("http://twitter.com/home?status=" + encodeURIComponent(title) + " " + encodeURIComponent(shop_url), 'twitter', 'menubar=yes,toolbar=yes,status=yes,resizable=yes,location=yes,scrollbars=yes');
			if(w)  {	w.focus();	}
		break;


		
		case "facebook" :
			var link = 'http://www.facebook.com/share.php?t=' + encodeURIComponent(title) + '&u=' + encodeURIComponent(shop_url);
			var w = window.open(link,'facebook', 'menubar=yes,toolbar=yes,status=yes,resizable=yes,location=yes,scrollbars=yes');
			if(w)  {	w.focus();	}

		break;

		case "me2day" :
			var tag = site_name;
			var link = 'http://me2day.net/posts/new?new_post[body]="' + encodeURIComponent(title) + '" : ' + encodeURIComponent(shop_url) + '&new_post[tags]=' + encodeURIComponent(tag) ;
			var w = window.open(link,'me2day', 'menubar=yes,toolbar=yes,status=yes,resizable=yes,location=yes,scrollbars=yes');
			if(w)  {	w.focus();	}
		break;


		case "yozm" :			
			//parameter = "ggg";
			var href = "http://yozm.daum.net/api/popup/prePost?link=" + encodeURIComponent(shop_url) + "&prefix=" + encodeURIComponent(title) + "&parameter=" + encodeURIComponent(site_name);
			var w = window.open(href, 'yozm', 'width=466, height=356');
			if(w)  {	w.focus();	}

		break;


		case "cyworld" :
		
			var href = "http://api.cyworld.com/openscrap/post/v1/?xu=/cyworldApi.php?code=" + code +"&sid=코드입력";
			var w = window.open(href, 'cyworld', 'width=450,height=410');
			if(w)  {	w.focus();	}

		break;



		default :

		break;

	
	}

}

function quantityControl(mode, idx){
	var _form = document['form_'+idx];

	if(mode != null || mode != 'undifined'){
		if(mode == 'plus'){
			_form.quantity.value = parseInt(_form.quantity.value) + 1;
		}

		if(mode == 'minus'){
			if(_form.quantity.value > 1){
				_form.quantity.value = parseInt(_form.quantity.value) - 1;
			}else{
				alert("최소 구매가능한 수량은 1개 입니다.");
			}
		}
	}
}


function openSubCate(idx){
	var open = document.getElementById('btn_plus_'+idx);
	var close = document.getElementById('btn_minus_'+idx);
	var viewbox = document.getElementById('subCatelist_'+idx);
	if(idx != "undifined" && idx != null){
		open.style.display = "none";
		close.style.display = "inline-block";
		viewbox.style.display = "block";
	}

}

function closeSubCate(idx){
	var open = document.getElementById('btn_plus_'+idx);
	var close = document.getElementById('btn_minus_'+idx);
	var viewbox = document.getElementById('subCatelist_'+idx);
	if(idx != "undifined" && idx != null){
		close.style.display = "none";
		open.style.display = "inline-block";
		viewbox.style.display = "none";
	}
}

function _toggle(idx){
	var open = document.getElementById('btn_plus_'+idx);
	var close = document.getElementById('btn_minus_'+idx);
	var viewbox = document.getElementById('subCatelist_'+idx);
	if(viewbox.style.display == 'block'){
		viewbox.style.display = 'none';
		close.style.display = 'none';
		open.style.display = 'inline-block';	
	}else{
		viewbox.style.display = 'block';
		open.style.display = 'none';
		close.style.display = 'inline-block';
	}
	return;
}
