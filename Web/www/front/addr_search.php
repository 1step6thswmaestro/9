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
$displaymode= (!_empty($_POST["mode"]))?trim(!_empty($_POST["mode"])):"2";
if (strlen($area)>2 && (strpos(getenv("HTTP_REFERER"),"addr_search.php")==false || strpos(getenv("HTTP_REFERER"),getenv("HTTP_HOST"))==false)) {
	exit;
}
?>

<html>
<head>
<title>우편번호 검색</title>
<meta http-equiv="CONTENT-TYPE" content="text/html;charset=EUC-KR">
<META http-equiv="X-UA-Compatible" content="IE=5" >
<style>
td	{font-family:"굴림,돋움";color:#4B4B4B;font-size:12px;line-height:17px;}
BODY,DIV,form,TEXTAREA,center,option,pre,blockquote {font-family:Tahoma;color:000000;font-size:9pt;}

A:link    {color:#635C5A;text-decoration:none;}
A:visited {color:#545454;text-decoration:none;}
A:active  {color:#5A595A;text-decoration:none;}
A:hover  {color:#545454;text-decoration:underline;}

.input{font-size:12px;BORDER-RIGHT: #DCDCDC 1px solid; BORDER-TOP: #C7C1C1 1px solid; BORDER-LEFT: #C7C1C1 1px solid; BORDER-BOTTOM: #DCDCDC 1px solid; HEIGHT: 18px; BACKGROUND-COLOR: #ffffff;padding-top:2pt; padding-bottom:1pt; height:19px}
.select{color:#444444;font-size:12px;}
.textarea {border:solid 1;border-color:#e3e3e3;font-family:돋음;font-size:9pt;color:333333;overflow:auto; background-color:transparent}

#searchFormLocal form, #searchFormAPI form {margin:0px; padding:0px;}
#searchResultAPI {margin-top:10px; border-top:1px solid #f3f3f3; border-bottom:1px solid #f3f3f3;}
</style>
<script language="javascript" type="text/javascript" src="/js/jquery-1.8.3.min.js"></script>
<SCRIPT LANGUAGE="JavaScript">
<!--

var $j = jQuery.noConflict();

function EnterCheck() {
	if (document.form.area.value.length==0) {
		alert("동(읍/면/리) 이름을 입력하세요.");
		document.form.area.focus();
		return;
	} else {
		if (document.form.area.value.length<2) {
			alert("동(읍/면/리) 이름을 2자 이상 입력하세요.");
			document.form.area.focus();
			return;
		}
		document.form.submit();
	}
}

var form="<?=$form?>";
var post="<?=$post?>";
var addr="<?=$addr?>";
var gbn="<?=$gbn?>";
var displaymode = "<?=$displaymode?>"
function do_submit(post1,straddr) {
	try {
		if(gbn=="2") {
			opener.document[form][post+"1"].value=post1;
			//opener.document[form][post+"2"].value=post2;
		} else {
			opener.document[form][post].value=post1;
		}
		opener.document[form][addr].value=straddr;
		//opener.document[form][addr].focus();
		
		if(addr.substr(addr.length-1,1) == '1'){
			var addr2 = addr.substr(0,addr.length-1)+'2';
			if(opener.document[form][addr2]) opener.document[form][addr2].value = '';
		}
		window.close();
	} catch (e) {
		alert("오류가 발생하였습니다.");
	}
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
			//stopApi();
			window.close();
		} catch (e) {
			alert("오류가 발생하였습니다.");
		}
	}
//탭 처리
function addrTab(index) {
	displaymode=index;
	for (i=1; i<=2; i++)
		if (index == i) {
			thisMenu = eval("addr" + index + ".style");
			thisMenu.display = "";
		} else {
			otherMenu = eval("addr" + i + ".style");
			otherMenu.display = "none";
		}
}

function checkTab(){
	addrTab(displaymode);
}
//-->
</SCRIPT>
<script language="javascript" type="text/javascript">
$j(function(){
	initSidoCode();
	$j("select[name='sidocode']").on('change',function(e){	getSigunguname($j(this).val());});

	$j('#apiForm').submit(function(e){
		e.preventDefault();
		findZipAPI();
	});
});

function findZipAPI(){
	var sidocode = $j("#apiForm").find("select[name='sidocode']").val();
	var sigunguEl = $j("#apiForm").find("select[name='sigunguname']");
	var sigunguname = $j(sigunguEl).val();
	var roadname = $j("#apiForm").find("input[name='roadname']").val();
	var bldmainnum = $j("#apiForm").find("input[name='bldmainnum']").val();
	if($j.trim(sidocode).length < 1){
		alert('시/도 를 선택해주세요');
	}else if($j(sigunguEl).find('option:eq(0)').text() != '없음' &&  !$j(sigunguEl).attr('disabled') &&  $j.trim(sigunguname).length < 1){
		alert('시/군/구 를 선택해주세요');
	}else if($j.trim(roadname).length < 1){
		alert('도로명을 입력해주세요');
	}else{
		initResult();
		//$j.post('/lib/api.php',{'apiname':'roadzip','method':'search','sidocode':sidocode,'sigunguname':escape(sigunguname),'roadname':escape(roadname),'bldmainnum':escape(bldmainnum),'perpage':'-1'},
		var obj = {'apiname':'roadzip','method':'search','sidocode':sidocode,'sigunguname':escape(sigunguname),'roadname':escape(roadname),'bldmainnum':escape(bldmainnum),'perpage':'20'};
		requestAPIsearch(obj);
	}
}

function requestAPIsearch(obj){
	
	var $page;
	var $totalpage;
	$j.post('/lib/api.php',obj,
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
				}).fail(function(){ alert('api 연동 부분에 오류가 있습니다. (1)');});
}

function dispAPIresult($itm){
	$j($itm).each(function(idx,itm){
		var eclass = (idx > 0 && idx%2 == 1)?'evenItem':'oddItem';

		/*var zipcode = $j(itm).find('zipnum').text();
		zipcode = zipcode.replace(/([0-9]{3})([0-9]{3})/g, '$1-$2');*/
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

		makehtml = '<tr><td class="zipCodeStr '+eclass+'"><A HREF="javascript:do_submit2(\''+zipcode+'\',\''+addr+'\',\''+ext+'\');">'+zipcode+'</a></td><td class="zipAddrStr '+eclass+'"><A HREF="javascript:do_submit2(\''+zipcode+'\',\''+addr+'\',\''+ext+'\');">'+addr+' '+ext+'<br><span class="oldAddress">'+addrold+'</span></a></td></tr>'

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
			$j(target).find('option:eq(0)').text('선택해주세요');
			$j($itm).each(function(idx,opt){
				$j(target).append('<option value="'+$j(opt).find('code').text()+'">'+$j(opt).find('name').text()+'</option>');
			});
		}
	 }
	,"xml").done(function(){}
	).fail(function(){ alert('api 연동 부분에 오류가 있습니다. (3)');});
}
</script>
</head>
<body topmargin="0" leftmargin="0" marginheight="0" marginwidth="0" onLoad="window.resizeTo(467,350);checkTab();">

<TABLE WIDTH="100%" BORDER="0" CELLPADDING="0" CELLSPACING="0">
	<TR>
		<TD><img src="<?=$Dir?>images/search_zipcode_title.gif" border="0"></TD>
	</TR>
	<TR>
		<TD style="padding:10px;">
			<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="100%">
						<div id="addr1" style="display:none;">
							<table border="0" cellpadding="0" cellspacing="0" width="100%" background="<?=$Dir?>images/common/tab_bg.gif">
								<tr>
									<td onClick="addrTab(2)" style="cursor:pointer;"><img src="<?=$Dir?>images/common/tab_addr2.gif" alt="도로명주소 검색" /></td>
									<td style="cursor:pointer;"><img src="<?=$Dir?>images/common/tab_addr1_on.gif" border="0" alt="지번주소 검색" /></td>
									<td width="100%"></td>
								</tr>
							</table>
							<div id="searchFormLocal">
								<form method="POST" name="form" action="<?= $_SERVER[PHP_SELF] ?>?form=<?=$form?>&post=<?=$post?>&addr=<?=$addr?>&gbn=<?=$gbn?>">
									<input type="hidden" name="displaymode" value="1"/>
									<input type="hidden" name=mode value="srch">
									<table border="0" cellpadding="0" cellspacing="0" width="100%">
										<tr>
											<td colspan="2" style="padding:10px 0px;">
												<!--<img src="<?=$Dir?>images/search_zipcode_text1.gif" border="0"><br />-->
												<span style="font-size:11px; color:#ff6600; letter-spacing:-1px; font-family:돋움; line-height:14px;">찾고자하는 주소의 동 (읍/면/리) 를 입력하세요.<br />예) 압구정, 태평로2가, 양촌리</span>
											</td>
										</tr>
										<tr>
											<td width="100%" valign="top"><input type="text" name="area" value="<?=$area?>" class="input" style="WIDTH:100%;"></td>
											<td><a href="javascript:EnterCheck();"><img src="<?=$Dir?>images/search_zipcode_btn.gif" border="0" hspace="5"></a></td>
										</tr>
									</table>
								</form>
							</div>

							<div id="searchResultLocal" style="display:;">
								<table cellpadding="0" cellspacing="0" width="100%">
<?
		if($mode=="srch" && strlen($area)>0) {
			$sql = "SELECT * FROM tblpostalcode ";
			$sql.= "WHERE addr_dong LIKE '%".$area."%' ";
			if(false === $result=mysql_query($sql,get_db_conn())){ ?>
						<tr><td style="padding:10px 0px; text-align:center; font-weight:bold">DB 연결상 오류가 있습니다.</td></tr>
		<?	}else{
				if(mysql_num_rows($result) < 1){ ?>
						<tr><td style="padding:10px 0px; text-align:center; font-weight:bold">검색된 결과가 없습니다.</td></tr>
		<?		}else{
					$odd = false;
					while($row=mysql_fetch_object($result)) {
						$trbg = ($odd)?"#F3F3F3":"#FFFFFF";
						$odd = !$odd;
						$temp = substr($row->post,0,3)."-".substr($row->post,3,3);
						$temp2 = $row->addr_do." ".$row->addr_si." ".$row->addr_dong." ".$row->addr_bunji;
						$temp3 = $row->addr_do." ".$row->addr_si." ".$row->addr_dong;  ?>

						<tr>
							<td style="text-align:center; width:80px; background:<?=$trbg?>;"><A HREF="javascript:do_submit('<?=$row->post?>','<?=$temp3?>');"><img src="<?=$Dir?>images/search_zipcode_point3.gif" border="0"><span style="color:#FF6C00; font-weight:bold"><?=$temp?></span></A></td>
							<td style="padding:4px 0px; background:<?=$trbg?>;"><A HREF="javascript:do_submit('<?=$row->post?>','<?=$temp3?>');"><span style="text-decoration:underline;"><?=$temp2?></span></a></td>
						</tr>
		<?			} // end while?>
						<tr><td><hr size="1" color="#F3F3F3"></td></tr>
						<tr>
							<td colspan="2" style="height:30px; text-align:center; color:#EE4900; font-weight:bold">해당 주소를 선택 후 나머지 주소를 입력하세요.</td>
						</tr>
						<tr><td><hr size="1" color="#F3F3F3"></td></tr>
		<?		} // end if
			} // end if 2
			@mysql_free_result($result);
		} else { ?>
						<tr><td><hr size="1" color="#F3F3F3"></td></tr>
						<tr>
							<td style="height:30px; text-align:center; color:#EE4900; font-weight:bold">해외 주소의 경우 입력란에 "해외"를 입력하세요.</td>
						</tr>
						<tr><td><hr size="1" color="#F3F3F3"></td></tr>
<?		} ?>
					</table>
				</div>
			</div>

			<div id="addr2" style="display:;">
				<table border="0" cellpadding="0" cellspacing="0" width="100%" background="<?=$Dir?>images/common/tab_bg.gif">
					<tr>
						<td style="cursor:pointer;"><img src="<?=$Dir?>images/common/tab_addr2_on.gif" border="0" alt="지번주소 검색" /></td>
						<td onClick="addrTab(1)" style="cursor:pointer;"><img src="<?=$Dir?>images/common/tab_addr1.gif" border="0" alt="지번주소 검색" /></td>
						
						<td width="100%"></td>
					</tr>
				</table>
				<div id="searchFormAPI">
					<form method="POST" name="apiForm" id="apiForm" action="">
					<table border="0" cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td colspan="5" style="padding:14px 0px; border-bottom:1px solid #f3f3f3;"><span style="font-size:11px; color:#ff6600; letter-spacing:-1px; font-family:돋움; line-height:14px;">시/도 및 시/군/구를 선택하신후 도로명과 건물 번호를 입력하세요.</span></td>
						</tr>
						<tr><td colspan="5" height="10"></td></tr>
						<tr>
							<td width="45">시/도</td>
							<td><select name="sidocode" style="width:120px;"><option value="">-- 목록 호출중 --</option></select></td>
							<td>시/군/구</td>
							<td><select name="sigunguname" style="width:120px;"><option value="">선택해주세요</option><option value="">선택해주세요</option><option value="">선택해주세요</option><option value="">선택해주세요</option></select></td>
							<td></td>
						</tr>
						<tr>
							<td>도로명</td>
							<td><input type="text" name="roadname" value="" style="width:120px;" /></td>
							<td>건물번호</td>
							<td><input type="text" name="bldmainnum" value="" style="width:120px;"></td>
							<td><input type="image" src="<?=$Dir?>images/search_zipcode_btn.gif" border="0" /></td>
						</tr>
					</table>
					</form>
				</div>
				<div id="searchResultAPI" style="display:;">
					<style type="text/css">
						#apiResultTbl{}
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
		</td>
	</tr>
	<tr><td height="10"></td></tr>
	<tr>
		<td align="center"><a href="javascript:window.close()"><img src="<?=$Dir?>images/search_zipcode_btn_close.gif" border="0"></a></td>
	</tr>
	</table>
	</TD>
</TR>
</TABLE>
</body>
</html>