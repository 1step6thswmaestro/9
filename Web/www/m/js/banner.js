if(typeof(pops)=='undefined') {

	BaramangSwipe.mainBanner = function(obj, pagination, autoslidetime,options) {

		var _banner = $j(obj).baramangSwipe(options && options.childTag || "div", $j.extend({	
			elementCountPerGroup: 1,
			isLoop: true,
			isAutoScroll: true,
			autoScrollTime: autoslidetime,
		}, options));

		_banner.bannerNavigator = function() {				
			_banner.success();
		};
		
		_banner.success = function() {
			$j(".naviImg"+_banner.currentPageNo).attr("src","/m/images/pon.png");
		};
		return _banner;
	};
}
