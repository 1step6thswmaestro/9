<?
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once("header.php");
	
	$vidx = isset($_GET['vidx'])?trim($_GET['vidx']):"";
	
	$curpage = isset($_GET['page'])?trim($_GET['page']):"1";

	if((strlen($vidx) <= 0) || $vidx == "0"){
		echo '<script>alert(\"�ʼ����� �����Ǿ� �������� �����Ͻ� �� �����ϴ�.\");history.go(-1);</script>';exit;
	}
	
	$infoSQL = "SELECT COUNT(p.pridx) AS prcount, v.com_name, v.com_owner, v.com_image ";
	$infoSQL .= "FROM tblproduct AS p LEFT OUTER JOIN tblvenderinfo AS v ON(p.vender = v.vender) ";
	$infoSQL .= "WHERE v.vender = '".$vidx."' ";
	
	$imgsrc = $Dir."data/shopimages/vender/";
	
	if(false !== $infoRes = mysql_query($infoSQL,get_db_conn())){
		$infoNumRows = mysql_num_rows($infoRes);
		if($infoNumRows > 0){
			$prcount = mysql_result($infoRes,0,0);
			$corpname = mysql_result($infoRes,0,1);
			$corprep = mysql_result($infoRes,0,2);
			$imagerep = mysql_result($infoRes,0,3);

			$src = $imgsrc.$imagerep;
		}else{
			echo '<script>alert(\"��ϵ� �����簡 �ƴմϴ�.\");history.go(-1);</script>';exit;
		}
		
		mysql_free_result($infoRes);
	}else{
		echo '<script>alert(\"������ �����Ǿ����ϴ�.\n ��� �� �ٽ� �õ� �� �ֽñ� �ٶ��ϴ�.\");history.go(-1);</script>';exit;
	}
	
	$listnum = 5; // ����Ʈ��
	$blocknum = 3; // ��ϼ�
	$pagetype = "product";
	$variable = "vidx=".$vidx."&";
?>
<div id="content">
	<div class="h_area2">
		<h2>�̴ϼ�</h2>
		<a href="main.php" class="btn_home" rel="external"><span class="vc">Ȩ</span></a>
		<a href="javascript:history.back()" class="btn_prev" rel="external"><span>����</span></a>
	</div>

	<section>
		<div style="margin:0px; padding:0px; height:80px; background:#f5f5f5; border-bottom:1px solid #eeeeee;">
			<div style="float:left; width:60px; margin:10px; text-align:center;"><img src="<?=$src?>" width="60" style="border:1px solid #dddddd;" alt="" /></div>
			<div style="float:left; width:70%; margin-top:10px;">
				<ul>
					<li style="padding-top:3px;"><span style="font-size:1.3em; font-weight:bold; line-height:20px;"><?=$corpname?></span><li>
					<li style="padding-top:3px;"><span style="font-size:0.9em; color:#888888;">��ǥ : <?=$corprep?></span><li>
					<li><span style="font-size:0.9em; color:#888888;">��ϵ� ��ǰ�� : <b><?=$prcount?>��</b></span><li>
				</ul>
			</div>
		</div>
	</section>

	<section>
		<h3 style="display:none;">������ǰ ����Ʈ</h3>
		<ul>
		<?
			$origloc = $_SERVER['DOCUMENT_ROOT']."/data/shopimages/product/"; // �������� ���
			$saveloc = $_SERVER['DOCUMENT_ROOT']."/data/shopimages/mobile/"; // �泻�� ���� ���
			$quality = 100;

			$vPrSQL = "SELECT productcode, productname, sellprice, consumerprice, maximage ";
			$vPrSQL .= "FROM tblproduct ";
			$vPrSQL .= "WHERE vender = '".$vidx."' ";
			$vPrSQL .= "ORDER BY regdate DESC ";
			$vPrSQL .= "LIMIT ".($listnum * ($curpage - 1)) . ", " . $listnum;;

			if(false !== $vPrRes = mysql_query($vPrSQL)){
				$vPrNumRows = mysql_num_rows($vPrRes);

				if($vPrNumRows){
					while($vPrRow = mysql_fetch_assoc($vPrRes)){
		?>
			<li class="pr_type_list_wrap">
				<a href="productdetail_tab01.php?productcode=<?=$vPrRow['productcode']?>" rel="external">
				<table cellpadding="0" cellspacing="0" width="100%" class="pr_type_list_table">
					<tr>
						<td class="typelist_image_wrap">
							<div class="typelist_image_div">
								<img src="<?=_getMobileThumbnail($origloc,$saveloc,$vPrRow['maximage'],140,140,$quality)?>" alt="��ǰ�� �̹���" class="pr_pt">
							</div>
						</td>
						<td class="typelist_text_wrap">
							<div class="pr_txt" style="width:96%;">
								<strong class="pr_name"><?=$vPrRow['productname']?></span></strong><br/>
								<?if($vPrRow['consumerprice'] > 0){?>
									<em class="pr_consumer_price"><?=number_format($vPrRow['consumerprice'])?>��</em><br/>
								<?}?>
								<em class="pr_price"><?=number_format($vPrRow['sellprice'])?>��</em>
							</div>
						</td>
					</tr>
				</table>
				</a>
			<li>
		<?
					}
				}else{
				}
				mysql_free_result($vPrRes);
			}
		?>
		</ul>
	</section>

	<div id="paging_container">
		<div id="paging_box">
			<ul>
				<?
					_getPage($prcount,$listnum,$blocknum,$curpage,$pagetype,$variable); 
				?>
			</ul>
		</div>
	</div>
</div>

<?
	include_once("footer.php");
?>