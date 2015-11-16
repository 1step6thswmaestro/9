			<li>
				<div class="pr_info_area">
					<table cellpadding="0" cellspacing="0" width="100%" border="0" class="pr_infobox">
						<tr>
							<td valign="top">
								<div style="float:left; width:80px; margin-right:10px; border:1px solid #eeeeee;">
								<?
									if(strlen($row->tinyimage)!=0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)){
									?>
										<img src="<?=_getMobileThumbnail($origloc,$saveloc,$row->tinyimage,80,80,$quality)?>?>" />
									<?
										
									} else {
										echo "<img src=\"".$Dir."images/no_img.gif\" width=\"80\">";
									}
								?>
								</div>
								<div style="padding-top:5px;"><a href="./productdetail_tab01.php?productcode=<?=$row->productcode?>" rel="external"><?=cutStr($productname,34)?></a></div>
							</td>
						</tr>
						<tr>
							<td>
								<table cellpadding="0" cellspacing="0" width="100%" border="0" class="pr_info">
								<?if (strlen($row->option1)>0 || strlen($row->option2)>0 || strlen($optvalue)>0) {?>
									<?
										if (strlen($row->option1)>0) {
											$temp = $row->option1;
											$tok = explode(",",$temp);
											$count=count($tok);	
									?>
									<tr>
										<th class="pr_info_classfy">·&nbsp;&nbsp;<?=$tok[0]?></th>
										<td class="pt_info_contents">
											<select name="option1" size="1" onchange="CheckForm('upd',$formcount-1);">
										<?
												for($i=1;$i<$count;$i++){
													if(strlen($tok[$i])>0){
										?>
												<option value="<?=$i?>" <?if($i==$row->opt1_idx){?>selected<?}?>><?=$tok[$i]?></option>	
										<?
													}
												}
										?>
											</select>
										</td>
									</tr>
									<?}?>
									<?
										if (strlen($row->option2)>0) {
											$temp = $row->option2;
											$tok = explode(",",$temp);
											$count=count($tok);
									?>
									<tr>
										<th class="pr_info_classfy">·&nbsp;&nbsp;<?=$tok[0]?></th>
										<td class="pt_info_contents">
											<select name="option2" size="1" onchange="CheckForm('upd',$formcount-1);">
											<?
													for($i=1;$i<$count;$i++){
														if(strlen($tok[$i])>0){
											?>
														<option value="<?=$i?>" <?if($i==$row->opt2_idx){?>selected<?}?>><?=$tok[$i]?></option>
											<?
														}
													}
											?>
											</select>
										</td>
									</tr>
									<?}?>
									<?if(strlen($optvalue)>0) {?>
									<tr>
										<td colspan="3">
											<?=$optvalue?>
										</td>
									</tr>
								<?
										}
									} // 옵션있을 경우 끝
								?>
									<?if ($_data->reserve_maxuse>=0){?>
									<tr>
										<th class="pr_info_classfy">·&nbsp;&nbsp;적립금</th>
										<td class="pt_info_contents"><?=number_format($tempreserve)?>원</td>
									</tr>
									<?}?>
									<tr>
										<th class="pr_info_classfy">·&nbsp;&nbsp;주문금액</th>
										<td class="pt_info_contents"><span class="point4"><?=number_format($sellprice)?>원</span></td>
									</tr>
									<tr>
										<th class="pr_info_classfy">·&nbsp;&nbsp;수량</th>
										<td class="pt_info_contents">
											<span class="button white small" onClick="quantityControl('minus',<?=$formcount-1?>);">-</span>
											<input type="text" name="quantity" value="<? echo $row->quantity ?>" class="input_text quantity" />
											<span class="button white small" onClick="quantityControl('plus',<?=$formcount-1?>);">+</span>
											<span class="button black small" onClick="CheckForm('upd',<?=$formcount-1?>);">적용</span>
										</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td class="basket_prbtn_area"">
								<?
									if (strlen($_ShopInfo->getMemid())>0 && $_ShopInfo->getMemid()!="deleted") {
								?>
									<button type="button" class="btn_type3 wish" onClick="go_wishlist('<?=$formcount-1?>')"><span>찜하기</span></button>
								<?
									} else {
								?>
									<button type="button" class="btn_type3 wish" onClick=":check_login()"><span>찜하기</span></button>
								<?
									}
								?>
								<button type="button" class="btn_type3 delete" onClick="CheckForm('del',<?=$formcount-1?>)"><span>상품삭제</span></button>
							</td>
						</tr>
					</table>
				</div>
			</li>