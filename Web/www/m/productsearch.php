<?
	$Dir="../";
	include_once("./header.php");
	include_once($Dir."m/inc/paging_inc.php");

	$currentPage = $_REQUEST["page"];
	if(!$currentPage) $currentPage = 1; 
	$origloc = $_SERVER['DOCUMENT_ROOT']."/data/shopimages/product/"; // 원본파일 경로
	$saveloc = $_SERVER['DOCUMENT_ROOT']."/data/shopimages/mobile/"; // 썸내일 저장 경로
	$quality = 100;
	
	$recordPerPage = 12; // 페이지당 게시글 리스트 수 
	$pagePerBlock = 2; // 블록 갯수 

	$terms = isset($_REQUEST[terms])? $_REQUEST[terms]:"";
	$sc_text = isset($_REQUEST[sc_text])? $_REQUEST[sc_text]:"";
	$mode = isset($_REQUEST[mode])? $_REQUEST[mode]:"";

	if(!empty($terms) && !empty($sc_text) && !empty($mode)){
		
		$sql = "SELECT  sellprice, consumerprice, reserve, productname, tinyimage, maximage, quantity, productcode FROM tblproduct WHERE ";
		
		switch($terms){
			case "productname":
				$sql.= "UPPER(productname) LIKE UPPER('%".$sc_text."%') ";
			break;
			case "keyword":
				$sql.= "UPPER(keyword) LIKE UPPER('%".$sc_text."%') ";
			break;
			case "production":
				$sql.= "UPPER(production) LIKE UPPER('%".$sc_text."%') ";
			break;
			default:
				$sql.= "1=1 ";
			break;
		}
		$sql.="ORDER BY date DESC ";
		
		$cnt_result = mysql_query($sql,get_db_conn());
		$cnt = mysql_num_rows($cnt_result);	
		mysql_free_result($cnt_result);

		$sql.="LIMIT ".($recordPerPage * ($currentPage - 1)) . ", " . $recordPerPage;
		$result = mysql_query($sql, get_db_conn());
	}
	
	$pagetype = "product";
	$variable = "mode=".$mode."&terms=".$terms."&sc_text=".$sc_text."&";
	
?>

<div id="content">
	<div class="h_area2">
		<h2>상품검색</h2>
		<a href="main.php" class="btn_home" rel="external"><span class="vc">홈</span></a>
		<a href="javascript:history.back()" class="btn_prev" rel="external"><span>이전</span></a>
	</div>
	<div class="sc_container">
		<div class="sc_wrap">
			<div class="sc_terms">
				<form name="searchForm" method="post" action="<?=$_SERVER[PHP_SELF]?>">
					<div class="sc_type_box">
						<select name="terms" class="terms">
							<option value="productname">상품명</option>
							<option value="keyword">키워드</option>
							<option value="production">제조사</option>
						</select>
					</div>
					<div class="sc_text_box">
						<input type="text" name="sc_text" value="" class="m_input sc_text" />
					</div>
					<div class="sc_btn_box">
						<input type="button" name="btn_submit" id="btn_submit" class="btn_search" value="검색" />
					</div>
					<input type="hidden" name="mode" value="search">
				</form>
			</div>
			<div class="sc_result">
				<ul class="sc_list">
					<?
						if($cnt >= 1){
							while($row = mysql_fetch_object($result)){
								
								$img = "../data/shopimages/product/".urlencode($row->tinyimage);
					?>
							<li>
								<div class="sc_img_con">
									<div class="sc_img_wrap">
										<div class="sc_img_box">
											<div class="img_view" style="display:table-cell;vertical-align:middle;">
												<a href="productdetail_tab01.php?productcode=<?=$row->productcode?>" >
												<img src="<?=_getMobileThumbnail($origloc,$saveloc,$row->maximage,90,90,$quality)?>">
												</a>
											</div>
										</div>
									</div>
									<div class="sc_text_con">
									<strong class="pr_name"><?=cutStr($row->productname,13)?></strong><br>
									<? if($row->consumerprice != "0"){?>
									<em class="sc_pr_consumer"><?=number_format($row->consumerprice)?>원</em><br>
									<?}?>
									<em class="pr_price"><?=number_format($row->sellprice)?></em>원<br>
									<?
										if ($row->quantity=="0") echo soldout();
										$reserveconv=getReserveConversion($row->reserve,$row->reservetype,$row->sellprice,"Y");
										if($reserveconv>0) {
									?>
											<img src="<?=$Dir?>images/common/reserve_icon.gif" border="0" style="margin-right:2px;"><?=number_format($reserveconv)?>원
									<?
										}
									?>
									</div>
								</div>
							</li>
					<?
							}
						}else{
							if($cnt === null){
					?>
								<li style="width:100%;height:30px;line-height:30px;">검색어를 입력해 주세요</li>
					<?
							}else{
					?>
								<li style="width:100%;height:30px;line-height:30px;">검색된 상품이 존재하지 않습니다</li>
					<?
							}
						}
					?>
				</ul>
			</div>
		</div>
	</div>
</div>
<div id="paging_container">
	<div id="paging_box">
		<ul>
			<?
				_getPage($cnt,$recordPerPage,$pagePerBlock,$currentPage,$pagetype, $variable); 
			?>
		</ul>
	</div>
</div>

<script>
$("#btn_submit").click(function(){
	var _form = document.searchForm;

	if($("input[name=sc_text]").val() == "" || $("input[name=sc_text]").val() == ""){
		alert("검색어를 입력하세요.");
		$("input[name=sc_text]").focus();
		return false;
	}else{
		$("input[name=sc_text]").hide();
		_form.submit();
		return;
	}
});
</script>

<? include "footer.php"; ?>