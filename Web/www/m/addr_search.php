<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/ext/func.php");

$form=$_REQUEST["form"];
$post=$_REQUEST["post"];
$addr=$_REQUEST["addr"];
$gbn=$_REQUEST["gbn"];

$area=trim($_POST["area"]);
$mode=$_POST["mode"];

if (strlen($area)>2 && (strpos(getenv("HTTP_REFERER"),"addr_search.php")==false || strpos(getenv("HTTP_REFERER"),getenv("HTTP_HOST"))==false)) {
	exit;
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>우편번호 검색</title>
	<meta http-equiv="CONTENT-TYPE" content="text/html;charset=EUC-KR">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no" />
	<link rel="stylesheet" href="./css/common.css" />
	<script type="text/javascript" src="./js/jquery-1.10.2.min.js"></script>
	<script type="text/javascript" src="./js/common.js"></script>
	<script>
		var $j = jQuery.noConflict();
		var form="<?=$form?>";
		var post="<?=$post?>";
		var addr="<?=$addr?>";
		var gbn="<?=$gbn?>";
		var zipXhr = null;
		var searchMode =0;
		
		$j(function(){
			searchMenu(0);
			initSidoCode();
			$j("select[name='sidocode']").on('change',function(e){	getSigunguname($j(this).val());});
			/*$j('#apiForm').submit(function(e){
				e.preventDefault();
				initFunc=false;
				findZipAPI();
			});*/
		});
		
		function stopApi(){
			if(zipXhr != null) zipXhr.abort();
		}

		function do_submit2(zipcode,straddr,ext){
			try {
				if(gbn=="2") {
					opener.document[form][post+'1'].value=zipcode;
				} else {
					opener.document[form][post].value=zipcode;
				}
				opener.document[form][addr].value=straddr;

				if(addr.substr(addr.length-1,1) == '1'){
					var addr2 = addr.substr(0,addr.length-1)+'2';
					if(opener.document[form][addr2]){
						opener.document[form][addr2].value = ext;
					}
				}
				stopApi();
				window.close();
			} catch (e) {
				alert("오류가 발생하였습니다.");
			}
		}

		function findZipAPI(){
			var sidocode = $j("#apiForm").find("select[name='sidocode']").val();
			var sigunguEl = $j("#apiForm").find("select[name='sigunguname']");
			var sigunguname = $j(sigunguEl).val();
			var roadname = $j("#apiForm").find("input[name='roadname']").val();
			var dongname = $j("#apiForm").find("input[name='dongname']").val();
			var bldmainnum = $j("#apiForm").find("input[name='bldmainnum']").val();
			var apimode ='test';

			if($j.trim(sidocode).length < 1){
				alert('시/도 를 선택해주세요');
			}else if($j(sigunguEl).find('option:eq(0)').text() != '없음' &&  !$j(sigunguEl).attr('disabled') &&  $j.trim(sigunguname).length < 1){
				alert('시/군/구 를 선택해주세요');
			}else{
				if(searchMode == 0 && $j.trim(dongname) < 1){
					alert("읍/면/동을 입력해주세요.");
					return;
				}
				if(searchMode == 1 && $j.trim(roadname) < 1){
					alert("도로명을 입력해주세요.");
					return;
				}
				initResult();
				var obj = {'apiname':'roadzip','method':'search','sidocode':sidocode,'sigunguname':escape(sigunguname),'roadname':escape(roadname),'dongname':escape(dongname),'apimode':escape(apimode),'bldmainnum':escape(bldmainnum),'perpage':'20'};
				if(zipXhr && zipXhr.readystate != 4) zipXhr.abort(); // 실행중 쿼리 취소
				requestAPIsearch(obj);
			}
		}

		function requestAPIsearch(obj){
			var $page;
			var $totalpage;
			zipXhr = $j.post('/lib/api.php',obj,
				function(data){
					var $emsg = $j(data).find('msg:eq(0)').text();
					if($j.trim($emsg).length){
						alert($emsg);
					}else{
						var $rst = $j(data).find('result');
		
						$page = $j($rst).attr('page');
						$totalpage = $j($rst).attr('totalpage');
		
						var $cnt = $j($rst).attr('itemcount');
						var $itm = $j($rst).find('item');
						if(parseInt($cnt) <1){
							$j('#searchResultAPI').find('table:eq(0)').find('tbody').append('<tr><td class="noResult">검색 결과가 없습니다.</td></tr>');
						}else{
							dispAPIresult($itm);
						}
					}
				},"xml").done(function(){
					if(parseInt($page) < parseInt($totalpage)){
						obj.page = parseInt($page)+1;
						requestAPIsearch(obj);
					}
				}).fail(function(jqXHR, textStatus){ if(textStatus != 'abort') alert('api 연동 부분에 오류가 있습니다. (1)');});
		}

		function dispAPIresult($itm){
			$j($itm).each(function(idx,itm){
				var eclass = (idx > 0 && idx%2 == 1)?'evenItem':'oddItem';
				var makehtml="";
				var zipcode = $j(itm).find('basicareanum').text(); 
				var addr = $j(itm).find('sidoname').text()+' '+$j(itm).find('sigunguname').text()+' '+$j(itm).find('roadname').text();
				var bldmainnum = $j(itm).find('bldmainnum').text();
				var bldsubnum  = $j(itm).find('bldsubnum ').text();
				var bldname  = $j(itm).find('bldname').text();
				var dbldname  = $j(itm).find('dbldname').text();
				var ext = '';
				if($j.trim(bldmainnum).length) ext+= bldmainnum;
				if($j.trim(bldsubnum ).length) ext+= '-'+bldsubnum ;

				if($j.trim(bldname).length) ext+= ' '+bldname;
				if($j.trim(dbldname ).length) ext+= '-'+dbldname ;

				var addrold = $j(itm).find('sidoname').text()+' '+$j(itm).find('sigunguname').text()+' '+$j(itm).find('dongname').text();
				var jimain = $j(itm).find('jibunmain').text();
				var jisub = $j(itm).find('jibunsub').text();
				if($j.trim(jimain).length) addrold+= ' '+jimain;
				if($j.trim(jisub).length) addrold+= '-'+jisub;

				if(searchMode ==0){ // 지번
					makehtml = '<tr><td class="zipCodeStr '+eclass+'"><A HREF="javascript:do_submit2(\''+zipcode+'\',\''+addr+'\',\''+ext+'\');">'+zipcode+'</a></td><td class="zipAddrStr '+eclass+'"><A HREF="javascript:do_submit2(\''+zipcode+'\',\''+addr+'\',\''+ext+'\');"><span class="oldAddress">'+addrold+'</span><br/>'+addr+' '+ext+'</a></td></tr>'
				}else{//도로명
					makehtml = '<tr><td class="zipCodeStr '+eclass+'"><A HREF="javascript:do_submit2(\''+zipcode+'\',\''+addr+'\',\''+ext+'\');">'+zipcode+'</a></td><td class="zipAddrStr '+eclass+'"><A HREF="javascript:do_submit2(\''+zipcode+'\',\''+addr+'\',\''+ext+'\');">'+addr+' '+ext+'<br><span class="oldAddress">'+addrold+'</span></a></td></tr>'
				}
				$j('#searchResultAPI').find('table:eq(0)').find('tbody').append(makehtml);
			});
		}


		function getSigunguname(sidocode){
			initSigunguname();
			$j.post('/lib/api.php',{'apiname':'roadzip','method':'getgugun','sidocode':sidocode},
				function(data){
					var $emsg = $j(data).find('msg:eq(0)').text();
					if($j.trim($emsg).length){
						alert($emsg);
					}else{
						var $rst = $j(data).find('result');
						var $cnt = $j($rst).attr('itemcount');
						var $itm = $j($rst).find('item');
						var target = $j("select[name='sigunguname']");
						if($cnt == '1' && $j.trim($j($itm[0]).find('sigunguname').text()).length < 1){
							$j(target).find('option:eq(0)').text('없음');
							$j(target).attr('disabled',true);
						}else{				
							$j(target).attr('disabled',false);
							$j(target).find('option:eq(0)').text('선택해주세요');
							$j($itm).each(function(idx,opt){
								$j(target).append('<option value="'+$j(opt).find('sigunguname').text()+'">'+$j(opt).find('sigunguname').text()+'</option>');
							});
						}
						
						
					}
				},"xml").done(function(){}
			).fail(function(){ alert('api 연동 부분에 오류가 있습니다. (2)');});
		}

		function initSigunguname(){
			$j("select[name='sigunguname']").find('option:gt(0)').remove();
		}

		function initResult(){
			$j('#searchResultAPI').find('table:eq(0)').find('tbody').html('');
		}

		function initSidoCode(){
			$j("select[name='sidocode']").find('option:gt(0)').remove();
			initSigunguname();
			$j.post('/lib/api.php',{'apiname':'roadzip','method':'getsido'},
			function(data){
				var $emsg = $j(data).find('msg:eq(0)').text();
				if($j.trim($emsg).length){
					alert($emsg);
				}else{
					var $rst = $j(data).find('result');
					var $cnt = $j($rst).attr('itemcount');
					var $itm = $j($rst).find('item');
					var target = $j("select[name='sidocode']");			
					$j(target).find('option:eq(0)').html('선택해주세요');
					$j($itm).each(function(idx,opt){
						$j(target).append('<option value="'+$j(opt).find('code').text()+'">'+$j(opt).find('name').text()+'</option>');
					});
				}
			 }
			,"xml").done(function(){}
			).fail(function(){ alert('api 연동 부분에 오류가 있습니다. (3)');});
		}

		function searchMenu(idx){
			stopApi();
			initResult();
			if(idx.length>0) {alert("잘못된 접근입니다.");return;}
			var menuobj = $j('#menu_wrap > a');
			var menulength = menuobj.length;
			for(i = 0; i<menulength;i++){
				if(i==idx)menuobj.eq(i).attr('class','black');
				else menuobj.eq(i).attr('class','white');
			}
			var act="", ext="", sn="", st="", mc="", bm="";
			var mh = new Array; 
			if(idx == 0){searchMode=0,sn='<input type="text" name="dongname"  value="" style="width:120px;" /><input type="hidden" name="roadname"  value="" style="width:120px;" />',st='읍/면/동',mh[0] = "",mh[1] ="<input type=\"hidden\" name=\"bldmainnum\" value=\"\" style=\"width:120px;\">";}
			else{	searchMode=1,sn='<input type="text" name="roadname"  value="" style="width:120px;" /><input type="hidden" name="dongname" value="" style="width:120px;" />',st='도로명',mh[0] = "건물번호",mh[1] = "<input type=\"text\" name=\"bldmainnum\" value=\"\" style=\"width:120px;\">";}
			$j('#sendFiled').html(sn);
			$j('#sendTitle').text(st);
			mc = mh.length;
			for(m=0;m<mc;m++){$j('.bildnumber').eq(m).html(mh[m]);}
			
		}

		function callAPI(){
			stopApi();
			findZipAPI();
		}
	</script>
	<style>
		article h1 {height:35px;line-height:35px;background-color:#555555;color:#FFFFFF;text-align:center;font-size:1.2em;letter-spacing:4px;}
		section {width:96%; margin:0 auto;}
		input {box-sizing:border-box;}
		#menu_wrap {clear:both; width:100%; font-size:1.1em; margin:20px 0px 10px 0px; padding-bottom:8px; border-bottom:1px solid #222222;}
		#menu_wrap li {width:50%;float:left; text-align:center; cursor:pointer; height:25px; margin:10px 0px; line-height:25px}
		#menu_wrap a {padding:6px 12px;}
		#searchFormLocal {height:28px; padding:8px 0px; border-top:1px solid #dddddd; border-bottom:1px solid #dddddd;}
		#searchFormLocal input {height:26px; width:250px; border:1px solid #DDDDDD}
		#searchFormAPI {padding:8px 0px; border-top:1px solid #dddddd; border-bottom:1px solid #dddddd;}
		#searchFormAPI th {width:25%; text-align:left; padding-left:10px; background:#f2f2f2;}
		#searchFormAPI td {height:30px; padding-left:5px;}
		#searchFormAPI td select {height:26px; width:100%;}
		#addr2 .input {height:26px; width:100%; border:1px solid #DDDDDD}
		#roadBtn {text-align:center;}

		#addr1, #addr2 {margin-top:30px;}
		#searchResultLocal, #searchResultAPI {margin-top:30px;}
	</style>
</head>
<body>
	<article>
		<h1>우편번호 검색</h1>
		<section>
			<div id="searchWrap">
				<!-- <div id="menu_wrap"><a href="javascript:searchMenu(0);" class="searchMenu" class="black">지번주소 검색</a><a href="javascript:searchMenu(1);" class="white">도로명주소 검색</a></div> -->
				<div id="menu_wrap"><a href="javascript:searchMenu(0);" >지번주소 검색</a><a href="javascript:searchMenu(1);" >도로명주소 검색</a></div>
				<div style="margin:10px 8px; color:#888888;">
					시/도 및 시/군/구를 선택하신 후 "도로명" 혹은 "읍/면/동"을 입력하세요.
				</div>
				<div id="searchFormAPI">
					<form method="POST" name="apiForm" id="apiForm" action="">
						<table border="0" cellpadding="0" cellspacing="0" width="100%">
							<tr>
								<th>- 시/도</th>
								<td><select name="sidocode"><option value="">-- 목록 호출중 --</option></select></td>
							</tr>
							<tr>
								<th>- 군/구</th>
								<td><select name="sigunguname"><option value="">선택해주세요</option><option value="">선택해주세요</option><option value="">선택해주세요</option><option value="">선택해주세요</option></select></td>
							</tr>
							<tr>
								<th id="sendTitle"></th>
								<td id="sendFiled"></td>
							</tr>
							<tr>
								<th class="bildnumber"></th>
								<td class="bildnumber"></td>
							</tr>
						</table>
					</form>
					<div id="roadBtn" style="position:absolute;right:16px;top:220px;"><a href="javascript:callAPI();" class="button black">검색</a></div>
				</div>
				<div style="margin-top:10px; text-align:center;"><a href="javascript:close();" class="button white">닫기</a></div>

				<div id="searchResultAPI" style="display:;">
					<style type="text/css">
						#apiResultTbl{margin-top:10px;}
						#apiResultTbl td.noResult{ text-align:center;padding-top:10;color:#EE4900; font-weight:bold; }
						#apiResultTbl td.zipCodeStr{text-align:center; width:70px; color:#FF6C00; font-weight:bold; }
						#apiResultTbl td.zipCodeStr a:link {color:#FF6C00;}
						#apiResultTbl td.zipAddrStr{ font-weight:bold; padding:5px 0px; }
						#apiResultTbl td.oddItem{ background:#ffffff; }
						#apiResultTbl td.evenItem{ background:#F3F3F3; }
						#apiResultTbl .oldAddress{ font-weight:normal; }
					</style>
					<table cellpadding="0" cellspacing="0" width="100%" id="apiResultTbl">
						<tbody>
						</tbody>
					</table>
					<div id="APIpageStr"></div>
				</div>
			</div>
		</section>
	</article>
</body>
</html>