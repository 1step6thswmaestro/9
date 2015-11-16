<?
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include ("access.php");

	@set_time_limit(300);

	#########################################################


	$max=50;

	$Scrolltype=$_GET["Scrolltype"];

	$mode=$_GET["mode"];
	$selcodes=$_GET["selcodes"];
	$change=$_GET["change"];
	$prcode=$_GET["prcode"];
	$selcode=$_GET["selcode"];

	if ($mode=="sequence" && $change=="Y" && strlen($selcodes)>0 && strlen($pm_idx)>0) {
		$sql = "UPDATE tblmobileplanningmain SET product_list = '".$selcodes."' WHERE pm_idx='".$pm_idx."' ";
		mysql_query($sql,get_db_conn());
		$onload="<script>alert('메인화면 상품 순서 조정이 완료되었습니다.');</script>\n";
	} else if ($mode=="modify" && strlen($selcodes)>0 && strlen($pm_idx)>0) {
		$sql = "UPDATE tblmobileplanningmain SET product_list = '".$selcodes."' WHERE pm_idx='".$pm_idx."' ";
		mysql_query($sql,get_db_conn());
		$onload="<script>alert('해당 상품을 메인화면 진열상품 카테고리에 추가하였습니다.');</script>\n";
	} else if ($mode=="insert" && strlen($selcodes)>0 && strlen($pm_idx)>0) {
		$sql = "INSERT tblmobileplanningmain SET ";
		$sql.= "pm_idx			= '".$pm_idx."', ";
		$sql.= "product_list	= '".$selcodes."' ";
		mysql_query($sql,get_db_conn());
		$onload="<script>alert('해당 상품을 메인화면 진열상품 카테고리에 추가하였습니다.');</script>\n";
	} else if ($mode=="delete" && strlen($pm_idx)>0) {
		if(strlen($selcodes)==0) {
			$sql = "UPDATE tblmobileplanningmain SET product_list = '".$selcodes."' WHERE pm_idx='".$pm_idx."' ";
		} else {
			$sql = "UPDATE tblmobileplanningmain SET product_list = '".$selcodes."' WHERE pm_idx='".$pm_idx."' ";
		}
		mysql_query($sql,get_db_conn());
		$onload="<script>alert('해당 상품을 메인상품에서 삭제하였습니다.');</script>\n";
	}

	$sql = "SELECT vendercnt FROM tblshopcount ";
	$result=mysql_query($sql,get_db_conn());
	$row=mysql_fetch_object($result);
	$vendercnt=$row->vendercnt;
	mysql_free_result($result);

	if($vendercnt>0){
		$venderlist=array();
		$sql = "SELECT vender,id,com_name,delflag FROM tblvenderinfo ORDER BY id ASC ";
		$result=mysql_query($sql,get_db_conn());
		
		while($row=mysql_fetch_object($result)) {
			$venderlist[$row->vender]=$row;
		}
		mysql_free_result($result);
	}

	$imagepath=$Dir.DataDir."shopimages/product/";
?>

<? include "header.php"; ?>

<script type="text/javascript" src="lib.js.php"></script> 
<script language="JavaScript">
	<!--
	function openForm(pm_idx) {
		window.open("mobile_main_planning_write.php?pm_idx="+pm_idx,"","width=680,height=600");	
	}

	function del(pm_idx){
		if(confirm("선택하신 구성요소를 삭제할까요?")){	
			ifrm_ctrl.location.href="mobile_main_planning_ctrl.php?mode=del&pm_idx="+pm_idx;
		}
	}

	document.onkeydown = CheckKeyPress;
	var all_list_i = new Array(); //num에 대한 리스트값 셋팅
	var preselectnum = ""; //num에 대한 기존리스트값 셋팅
	var all_list = new Array();
	var selnum="";
	var ProductInfoStop="";

	function CheckKeyPress(updownValue) {
		prevobj=null;
		selobj=null;

		if(updownValue){
			ekey = updownValue;
		}else{
			ekey = event.keyCode;
		}
		
		if(selnum!="" && (ekey==38 || ekey==40 || ekey=="up" || ekey=="down")) {
			var h=0;

			h=all_list_i[selnum];
			if(ekey==38 || ekey == "up") {			//위로 이동
				kk=h-1;
			} else {	//아래로 이동
				kk=h+1;
			}

			prevobj=all_list[kk];

			if(prevobj!=null) {
				selobj=all_list[h];

				t1=prevobj.sort;
				prevobj.sort=selobj.sort;
				selobj.sort=t1;

				o1=prevobj.no;
				prevobj.no=selobj.no;
				selobj.no=o1;

				all_list[h]=prevobj;
				all_list[kk]=selobj;

				all_list_i[prevobj.num]=h; //prevobj.num에 대한 리스트값 셋팅
				all_list_i[selobj.num]=kk; //selobj.num에 대한 리스트값 셋팅
				preselectnum=prevobj.num; //prevobj.num에 대한 기존리스트값 셋팅

				takeChange(prevobj);
				takeChange(selobj);

				all_list[kk].selected=false;
				selnum="";
				document.form1.change.value="Y";
				ChangeList(all_list[kk].num);
			}
		}
	}

	function takeChange(argObj){
		var innerHtmlStr = "";

		innerHtmlStr="<td>"+argObj.num+"</td>";
		innerHtmlStr+="<td><a href=\"javascript:updown_click('"+argObj.num+"','up')\"><img src=\"images/btn_plus.gif\" border=\"0\" style=\"margin-bottom:3px;\"></a><br><a href=\"javascript:updown_click('"+argObj.num+"','down')\"><img src=\"images/btn_minus.gif\" border=\"0\" style=\"margin-top:3px;\"></a></td>";
		<?if($vendercnt>0) {echo "innerHtmlStr+=argObj.venderidx;\n";}?>
		innerHtmlStr+=argObj.imgidx;
		innerHtmlStr+=argObj.nameidx;
		innerHtmlStr+=argObj.sellidx;
		innerHtmlStr+=argObj.quantityidx;
		innerHtmlStr+=argObj.displayidx;
		innerHtmlStr+=argObj.editidx;
		innerHtmlStr+=argObj.deleteidx;
		document.all["idx_inner_"+argObj.sort].innerHTML="<table onclick=\"ChangeList('"+argObj.num+"');\" border=\"0\" cellSpacing=\"0\" cellPadding=\"0\" width=\"100%\" style=\"table-layout:fixed\"><col width=40></col><col width=12></col><?=($vendercnt>0?"<col width=70></col>":"")?><col width=50></col><col width=></col><col width=70></col><col width=45></col><col width=45></col><col width=45></col><col width=45></col><tr align=\"center\">"+innerHtmlStr+"</tr></table>";
	}

	function updown_click(num,updownValue){
		if(selnum != num){
			ChangeList(num);
		}
		CheckKeyPress(updownValue);
	}

	function ChangeList(num) {
		if(ProductInfoStop){
			ProductInfoStop = "";
		}else{
			if(all_list[all_list_i[num]].selected==true) {
				preselectnum="";  //기존num 값 셋팅
				selnum="";
				all_list[all_list_i[num]].selected=false;
				document.all["idx_inner_"+all_list[all_list_i[num]].sort].style.backgroundColor="#FFFFFF";
				
			} else {
				if(preselectnum>0) { //기존 선택되어 있는 값 비우기
					all_list[all_list_i[preselectnum]].selected=false;
					document.all["idx_inner_"+all_list[all_list_i[preselectnum]].sort].style.backgroundColor="#FFFFFF";
				}
				preselectnum=num;  //기존num 값 셋팅
				selnum=num;
				all_list[all_list_i[num]].selected=true;
				document.all["idx_inner_"+all_list[all_list_i[num]].sort].style.backgroundColor="#EFEFEF";
			}
			jumpdivshow(num,all_list[all_list_i[num]].selected);
		}
	}

	function jumpdivshow(num,selectValue) {
		if(document.getElementById("idx_inner_"+all_list[all_list_i[num]].sort) && document.getElementById("jumpdiv")) {
			var inneridxObj = document.getElementById("idx_inner_"+all_list[all_list_i[num]].sort);
			var jumpdivObj = document.getElementById("jumpdiv");

			jumpdivObj.style.display="none";
			
			if(selectValue==true) {
				jumpdivObj.style.display="";
				
				if(inneridxObj.offsetHeight>jumpdivObj.offsetHeight) {
					jumpdivObj.style.top = inneridxObj.offsetTop+((inneridxObj.offsetHeight-jumpdivObj.offsetHeight)/2);
				} else {
					jumpdivObj.style.top = inneridxObj.offsetTop-(jumpdivObj.offsetHeight-inneridxObj.offsetHeight-1);
				}
				jumpdivObj.style.left = (inneridxObj.offsetWidth-jumpdivObj.offsetWidth)/2;
			}
		}
	}

	function CheckJump(updownValue) {
		prevobj=null;
		selobj=null;

		h=all_list_i[selnum];
		if(updownValue == "up") {			//위로 이동
			kk=h-1;
		} else {	//아래로 이동
			kk=h+1;
		}

		if(all_list[kk]!=null) {
			prevobj=all_list[kk];
			selobj=all_list[h];

			t1=prevobj.sort;
			prevobj.sort=selobj.sort;
			selobj.sort=t1;

			o1=prevobj.no;
			prevobj.no=selobj.no;
			selobj.no=o1;

			all_list[h]=prevobj;
			all_list[kk]=selobj;

			all_list_i[prevobj.num]=h; //prevobj.num에 대한 리스트값 셋팅
			all_list_i[selobj.num]=kk; //selobj.num에 대한 리스트값 셋팅
			preselectnum=prevobj.num; //prevobj.num에 대한 기존리스트값 셋팅

			takeChange(prevobj);

			selnum=all_list[kk].num;
			all_list[kk].selected=true;
		}
	}

	function jumpgo() {
	
		form = document.form1;

		if(selnum.length) {
			if(form.jumpnumber.value.length>0 && all_list_i[form.jumpnumber.value]>-1 && all_list[all_list_i[form.jumpnumber.value]] && all_list[all_list_i[form.jumpnumber.value]].sort>-1) {
				if(form.jumpnumber.value!=selnum) {
					var updowntype = "down";
					var selnum_Obj = all_list[all_list_i[selnum]];
					var jumpnumber_Obj = all_list[all_list_i[form.jumpnumber.value]];

					var selnum_sort = selnum_Obj.sort;
					var jumpnumber_sort = jumpnumber_Obj.sort;
					var num_subtract = selnum_sort-jumpnumber_sort;
					var preselectnum_num="";

					preselectnum = selnum_Obj.num;
					if(num_subtract>0) {
						updowntype = "up";
					}

					num_subtract = Math.abs(num_subtract);

					for(var i=0; i<num_subtract; i++) {
						CheckJump(updowntype);
						if(i==0) {
							preselectnum_num = preselectnum;
						}
					}
					takeChange(selnum_Obj);
					preselectnum = preselectnum_num;

					form.jumpnumber.value="";
					document.form1.change.value="Y";
					selnum="";
					selnum_Obj.selected=false;
					ChangeList(selnum_Obj.num);
				}
			} else {
				if(form.jumpnumber.value.length==0) {
					alert("이동위치 No를 입력해 주세요.");
				} else {
					alert("이동위치 No는 존재하지 않는 번호 입니다.");
				}
			}
		}
	}

	function ObjList() {
		var argv = ObjList.arguments;   
		var argc = ObjList.arguments.length;

		//Property 선언
		this.classname		= "ObjList";
		this.debug			= false;
		this.num			= new String((argc > 0) ? argv[0] : "0");
		this.productcode	= new String((argc > 1) ? argv[1] : "");
		this.imgidx			= new String((argc > 2) ? argv[2] : "");
		this.nameidx		= new String((argc > 3) ? argv[3] : "");
		this.sellidx		= new String((argc > 4) ? argv[4] : "");
		this.quantityidx	= new String((argc > 5) ? argv[5] : "");
		this.displayidx		= new String((argc > 6) ? argv[6] : "");
		this.editidx		= new String((argc > 7) ? argv[7] : "");
		this.deleteidx		= new String((argc > 8) ? argv[8] : "");
		this.no				= new String((argc > 9) ? argv[9] : "");
		this.sort			= new String((argc > 10) ? argv[10] : "");
		this.selected		= new Boolean((argc > 11) ? argv[11] : false );
		<?if($vendercnt>0) {echo "this.venderidx		= new String((argc > 12) ? argv[12] : \"\");\n";}?>
	}

	function move_save(){
		if (document.form1.change.value!="Y") {
			alert("순서 변경을 하지 않았습니다.");
			return;
		}
		if (!confirm("현재의 순서대로 저장하시겠습니까?")){
			return;
		}

		val="";

		for(i=0;i<all_list.length;i++){
			val+=","+all_list[i].productcode;
		}


		if(val.length>0){

			val=val.substring(1);
			document.form1.mode.value = "sequence";
			document.form1.selcodes.value=val;
			document.form1.submit();
		}
	}

	<?if($vendercnt>0){?>
		function viewVenderInfo(vender) {
		ProductInfoStop = "1";
		window.open("about:blank","vender_infopop","width=100,height=100,scrollbars=yes");
		document.vForm.vender.value=vender;
		document.vForm.target="vender_infopop";
		document.vForm.submit();
		}
	<?}?>

	function ProductMouseOver(Obj) {
		obj = event.srcElement;
		WinObj=document.getElementById(Obj);
		obj._tid = setTimeout("ProductViewImage(WinObj)",200);
	}
	function ProductViewImage(WinObj) {
		WinObj.style.display = "";

		if(!WinObj.height){
			WinObj.height = WinObj.offsetTop;
		}

		WinObjPY = WinObj.offsetParent.offsetHeight;
		WinObjST = WinObj.height-WinObj.offsetParent.scrollTop;
		WinObjSY = WinObjST+WinObj.offsetHeight;

		if(WinObjPY < WinObjSY){
			WinObj.style.top = WinObj.offsetParent.scrollTop-WinObj.offsetHeight+WinObjPY;
		}else if(WinObjST < 0){
			WinObj.style.top = WinObj.offsetParent.scrollTop;
		}else{
			WinObj.style.top = WinObj.height;
		}
	}

	function ProductMouseOut(Obj) {
		obj = event.srcElement;
		WinObj = document.getElementById(Obj);
		WinObj.style.display = "none";
		clearTimeout(obj._tid);
	}

	function InsertSpecial() {
		if (document.form1.prcode.value.length==0) {
			alert("메인 진열상품에 추가할 상품을 선택하세요.");
			document.form1.prcode.focus();
			return;
		}
		num = all_list.length-1;
		if(num+1>=50){
			alert('메인 진열상품은 최대 50개까지 등록가능합니다.');
			return;
		}
		if (confirm("해당 상품을 메인 진열상품으로 포함하시겠습니까?")){
			temp = "";
			for (i=0;i<=num;i++) {
				if(all_list[i].productcode == document.form1.prcode.value){
					alert('이미 등록된 상품입니다.');
					return;
				} 
				if (i==0) {
					temp = all_list[i].productcode;
				}else {
					temp+=","+all_list[i].productcode;
				}
			}
			if(num==-1) {
				temp=document.form1.prcode.value;
			}else {
				temp+=","+document.form1.prcode.value;
			}
			document.form1.selcodes.value = temp;
			document.form1.submit();
		}
	}

	function ChangeSpecial(val) {
	
		document.form1.submit();
	}

	function Delete(delcode) {	
		ProductInfoStop = "1";
		
		if(!confirm("해당 상품을 메인상품에서 삭제하시겠습니까?")){
			return;
		}
		val="";
		for(i=0;i<all_list.length;i++){
			if(delcode!=all_list[i].productcode){
				val+=","+all_list[i].productcode;
			}
		}

		if(val.length>0){
			val=val.substring(1);
		}
		document.form1.mode.value="delete";
		document.form1.selcodes.value=val;

		document.form1.submit();
	}

	function ProductInfo(prcode) {
		ProductInfoStop = "1";
		code=prcode.substring(0,12);
		popup="YES";
		document.form_reg.code.value=code;
		document.form_reg.prcode.value=prcode;
		document.form_reg.popup.value=popup;
		if (popup=="YES") {
			document.form_reg.action="product_register.add.php";
			document.form_reg.target="register";
			window.open("about:blank","register","width=820,height=700,scrollbars=yes,status=no");
		} else {
			document.form_reg.action="product_register.php";
			document.form_reg.target="";
		}
		document.form_reg.submit();
	}

	function DivScrollActive(arg1){
		if(!self.id){
			self.id = self.name;
		}

		if(document.getElementById("divscroll") && document.getElementById("ListTTableId") && document.getElementById("ListLTableId") && parent.document.getElementById(self.id)){
			if(!document.getElementById("divscroll").height){
				document.getElementById("divscroll").height=document.getElementById("divscroll").offsetHeight;
			}

			if(arg1>0){
				if(document.getElementById("ListLTableId").offsetHeight > document.getElementById("divscroll").offsetHeight){
					document.getElementById("divscroll").style.height="100%";
					parent.document.getElementById(self.id).style.height=document.getElementById("ListTTableId").offsetHeight;
				}
			}else{
				document.getElementById("divscroll").style.height=document.getElementById("divscroll").height;
				parent.document.getElementById(self.id).style.height="100%";
			}
		}

		document.form1.Scrolltype.value = arg1;
	}
	function openSel(page_mode,pm_idx) {
		window.open("mobile_product_search.php?page_mode="+page_mode+"&pm_idx="+pm_idx+"&","collection","width=900,height=800,scrollbars=yes");
	}
//-->
</script>

<iframe name="ifrm_ctrl" width=0 height=0 frameborder=0 align=top scrolling="no" marginheight="0" marginwidth="0"></iframe>
<form name="form_reg" action="product_register.php" method="post">
	<input type="hidden" name="code">
	<input type="hidden" name="prcode">
	<input type="hidden" name="popup">
</form>
<form name="vForm" action="vender_infopop.php" method="post">
	<input type="hidden" name="vender">
</form>
<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
	<tr>
		<td valign="top">
			<table cellpadding="0" cellspacing="0" width=100% style="table-layout:fixed">
				<tr>
					<td>
						<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed"  background="images/con_bg.gif">
							<col width=198></col>
							<col width=10></col>
							<col width=></col>
							<tr>
								<td valign="top"  background="images/leftmenu_bg.gif">
									<? include ("menu_mobile.php"); ?>
								</td>
								<td></td>
								<td valign="top">
									<table cellpadding="0" cellspacing="0" width="100%">
										<tr>
											<td height="29" colspan="3">
												<table cellpadding="0" cellspacing="0" width="100%">
													<tr>
														<td height="28" class="link" align="left" background="images/con_link_bg.gif">
															<img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 모바일샵 &gt; <span class="2depth_select">메인 상품 진열설정</span>
														</td>
													</tr>
												</table>
											</td>
										</tr>   
										<tr>
											<td width="16"><img src="images/con_t_01.gif" width="16" height="16" border="0"></td>
											<td background="images/con_t_01_bg.gif"></td>
											<td width="16"><img src="images/con_t_02.gif" width="16" height="16" border="0"></td>
										</tr>
										<tr>
											<td width="16" background="images/con_t_04_bg1.gif"></td>
											<td bgcolor="#ffffff" style="padding:10px">
												<table cellpadding="0" cellspacing="0" width="100%">
													<tr>
														<td>
															<table WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
																<tr>
																	<td><img src="images/mobile_main_planning_title.gif" border="0"></td>
																</tr>
																<tr>
																	<td width="100%" background="images/title_bg.gif" height="21"></td>
																</tr>
															</table>
														</td>
													</tr>
													<tr>
														<td height="3"></td>
													</tr>
													<tr>
														<td style="padding-bottom:3pt;">
															<table WIDTH="100%" BORDER="0" CELLPADDING="0" CELLSPACING="0">
																<tr>
																	<td><img src="images/distribute_01.gif"></td>
																	<td colspan="2" background="images/distribute_02.gif"></td>
																	<td><img src="images/distribute_03.gif"></td>
																</tr>
																<tr>
																	<td background="images/distribute_04.gif"></td>
																	<td class="notice_blue"><img src="images/distribute_img.gif" ></td>
																	<td width="100%" class="notice_blue">모바일 쇼핑몰 메인화면의 진열상품을 관리하실 수 있습니다.</td>
																	<td background="images/distribute_07.gif"></td>
																</tr>
																<tr>
																	<td><img src="images/distribute_08.gif"></td>
																	<td colspan="2" background="images/distribute_09.gif"></td>
																	<td><img src="images/distribute_10.gif"></td>
																</tr>
															</table>
														</td>
													</tr>
													<tr>
														<td height="20"></td>
													</tr>
													<tr>
														<td>
															<table width="100%" border=0 cellpadding=0 cellspacing=0>
																<tr>
																	<td><img src="images/mobile_main_planning_stitle.gif" border="0" alt="메인상품 진열설정"></td>
																	<td width="100%" background="images/shop_basicinfo_stitle_bg.gif"></td>
																	<td><img src="images/shop_basicinfo_stitle_end.gif" width=10 height=31 alt=""></td>
																</tr>
															</table>
														</td>
													</tr>
													<tr>
														<td height=3></td>
													</tr>
													<tr>
														<td>
															<form name=frm action="<?=$_SERVER[PHP_SELF]?>" method=post>
																<input type=hidden name=>
																<input type=hidden name=>

																<table cellspacing=0 cellpadding=0 width="100%" border=0>
																	<colgroup>
																		<col width="20"></col>
																		<col width=""></col>
																		<col width="80"></col>
																		<col width="50"></col>
																		<col width="50"></col>
																		<col width="50"></col>
																		<col width="50"></col>
																	</colgroup>
																	<tr>
																		<td background="images/table_top_line.gif" width="761" colspan="8"></td>
																	</tr>
																	<tr>
																		<td class="table_cell"><p align="center">no</td>
																		<td class="table_cell1"><p align="center">상품구성명</td>
																		<td class="table_cell1"><p align="center">진열방식</td>
																		<td class="table_cell1"><p align="center">출력수</td>
																		<td class="table_cell1"><p align="center">사용유무</td>
																		<td class="table_cell1"><p align="center">수정</td>
																		<td class="table_cell1"><p align="center">삭제</td>
																	</tr>
																	<tr>
																		<td colspan="8" width="760" background="images/table_con_line.gif"><img src="images/table_con_line.gif" width="4" height="1" border="0"></td>
																	</tr>
																	<?
																		$query = "select * from tblmobileplanningmain order by pm_idx ASC";
																		$result = mysql_query($query,get_db_conn());
																		$i = 1;
																		while($row = mysql_fetch_array($result)){
																			$arr_pm_idx[$i] = $row[pm_idx];
																			$arr_title[$i] = $row[title];
																	?>

																	<tr align="center">
																	<td class="td_con2"><?=$i?></td>
																		<td class="td_con2"><?=$row[title]?></td>
																		<td class="td_con2"><?=$row[display_type]?></td>
																		<td class="td_con2"><?=$row[product_cnt]?></td>
																		<td class="td_con2"><?=$row[display]?></td>
																		<td class="td_con2"><a href="javascript:openForm('<?=$row[pm_idx]?>')"><img src="images/btn_edit.gif" border="0" alt="" /></a></td>
																		<td class="td_con2"><a href="javascript:del('<?=$row[pm_idx]?>')"><img src="images/btn_del.gif" /></a></td>
																	</tr>
																	<tr>
																		<td colspan="8" width="760" background="images/table_con_line.gif"><img src="images/table_con_line.gif" width="4" height="1" border="0"></td>
																	</tr>
																	<?
																			$i++;
																		}
																	?>
																	<tr>
																		<td colspan="8" width="760" background="images/table_con_line.gif"><img src="images/table_con_line.gif" width="4" height="1" border="0"></td>
																	</tr>
																</table>
															</form>
														</td>
													</tr>
													<tr>
														<td align="right"style="padding-top:10px">
															<img src="images/mobile_main_planning_btn.gif" id="uploadButton" border="0" style="cursor:hand" onclick="openForm('');" alt="상품구성 추가">
														</td>
													</tr>
													<tr>
														<td align="center" height=10></td>
													</tr>
													<tr>
														<td height=20></td>
													</tr>
													<tr>
														<td>
															<table id="listttableid" cellspacing=0 cellpadding=0 width="100%" border=0>
																<tr>
																	<td style="border:#ededed 4px solid;">
																		<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=get>
																			<table cellspacing=0 cellpadding=0 width="100%" border=0>
																				<!-- html-->
																				<col width="70"></col>
																				<col width=""></col>
																				<tr>
																					<td colspan=2 background="images/table_top_line.gif"></td>
																				</tr>
																				<tr>
																					<td class="table_cell" width="170"><img src="images/icon_point2.gif" border="0">메인 상품진열 섹션 선택</td>
																					<td class="td_con1">
																						<table cellspacing=0 cellpadding=0 border=0 width="100%">
																							<tr>
																								<td>
																									<?
																									
																										for($i=1; $i<= sizeof($arr_pm_idx);$i++){

																											if($pm_idx=="" && $i==1){
																												$checked = "checked";
																											}else{		
																												$checked = "";		
																												if($arr_pm_idx[$i]==$pm_idx){
																													$checked = "checked";
																												}				
																											}
																									?>					
																										<input type="radio" name="pm_idx" value="<?=$arr_pm_idx[$i]?>" <? echo $checked; ?> onClick="ChangeSpecial(this.value);" /><?=$arr_title[$i]?> 
																									<?
																										}	
																									?>
																								</td>
																							</tr>
																						</table>
																					</td>
																				</tr>
																				<tr>
																					<td colspan="2" background="images/table_con_line.gif"></td>
																				</tr>
																				<tr>
																					<td colspan="2">
																						<table cellpadding="0" cellspacing="0" width="100%">
																							<tr>
																								<td background="images/table_top_line.gif"></td>
																							</tr>
																							<tr>
																								<td>
																									<table id="listltableid" border="0" cellspacing="0" cellpadding="0" width="100%" style="table-layout:fixed">
																										<?
																										$colspan=9;
																										if($vendercnt>0) $colspan++;
																										?>
																										<col width=40></col>
																										<col width=12></col>
																										<col width=70></col>
																										<col width=50></col>
																										<col width=></col>
																										<col width=70></col>
																										<col width=45></col>
																										<col width=45></col>
																										<col width=45></col>
																										<col width=45></col>
																										<tr align="center">
																											<td class="table_cell" colspan="2">no</td>
																											<td class="table_cell1">입점업체</td>
																											<td class="table_cell1" colspan="2">상품명</td>
																											<td class="table_cell1">판매가격</td>
																											<td class="table_cell1">수량</td>
																											<td class="table_cell1">상태</td>
																											<td class="table_cell1">수정</td>
																											<td class="table_cell1">삭제</td>
																										</tr>
																										<?
																											$pm_idx=$_REQUEST["pm_idx"];
																											if($pm_idx==""){ 
																												$pm_idx=$arr_pm_idx[1];
																											}

																											$image_i=0;
																											if(strlen($pm_idx)>0) {
																												$mode="insert";
																												$sp_prcode="";
																												$sql = "SELECT product_list FROM tblmobileplanningmain WHERE pm_idx='".$pm_idx."' ";
																												$result = mysql_query($sql,get_db_conn());
																												if($row = mysql_fetch_object($result)){
																													$cnt_prcode=$row->product_list;
																													$sp_prcode=ereg_replace(',','\',\'',$cnt_prcode);
																													$mode="modify";
																												}
																												mysql_free_result($result);
																												
																												if(strlen($sp_prcode)>0) {
																												$sql = "SELECT option_price, productcode,productname,production,sellprice,consumerprice, ";
																												$sql.= "buyprice,quantity,reserve,reservetype,addcode,display,vender,tinyimage,selfcode,assembleuse ";
																												$sql.= "FROM tblproduct ";
																												$sql.= "WHERE productcode IN ('".$sp_prcode."')";

																												$result = mysql_query($sql,get_db_conn());

																												while($row=mysql_fetch_object($result)) {
																													$arraycode[$row->productcode]=$row->productcode;
																													$arrayquantity[$row->productcode]=$row->quantity;
																													$arraydisplay[$row->productcode]=$row->display;
																													$arrayoption_price[$row->productcode]=$row->option_price;
																													$arrayproductname[$row->productcode]=$row->productname;
																													$arrayproduction[$row->productcode]=$row->production;
																													$arraysellprice[$row->productcode]=$row->sellprice;
																													$arrayconsumerprice[$row->productcode]=$row->consumerprice;
																													$arraybuyprice[$row->productcode]=$row->buyprice;
																													$arrayreserve[$row->productcode]=$row->reserve;
																													$arrayreservetype[$row->productcode]=$row->reservetype;
																													$arrayaddcode[$row->productcode]=$row->addcode;
																													$arrayvender[$row->productcode]=$row->vender;
																													$arraytinyimage[$row->productcode]=$row->tinyimage;
																													$arrayselfcode[$row->productcode]=$row->selfcode;
																													$arrayassembleuse[$row->productcode]=$row->assembleuse;
																												}

																												$viewproduct = explode(",",$cnt_prcode);
																												$cnt =count($viewproduct);
																												$j=0;
																												$strlist="<script>\n";
																												$jj=$cnt;
																												$ii=0;
																												for($i=0;$i<$cnt;$i++){
																													if(strlen($arraycode[$viewproduct[$i]])>0){
																														$j++;
																														$strlist.= "var objlist=new ObjList();\n";
																														$strlist.= "objlist.num=\"".$j."\";\n";
																														$strlist.= "all_list_i[objlist.num]=".$ii.";\n";
																														$strlist.= "objlist.productcode=\"".$arraycode[$viewproduct[$i]]."\";\n";
																														if($vendercnt>0) {
																															$strlist.= "objlist.venderidx=\"<td class=\\\"td_con1\\\"><B>".(strlen($venderlist[$arrayvender[$viewproduct[$i]]]->vender)>0?"<span onclick=\\\"viewVenderInfo(".$arrayvender[$viewproduct[$i]].");\\\">".$venderlist[$arrayvender[$viewproduct[$i]]]->id."</span>":"-")."</B></td>\";\n";
																														}
																														if (strlen($arraytinyimage[$viewproduct[$i]])>0 && file_exists($imagepath.$arraytinyimage[$viewproduct[$i]])==true){
																															$strlist.= "objlist.imgidx=\"<td class=\\\"td_con1\\\"><img src=\\\"".$imagepath.$arraytinyimage[$viewproduct[$i]]."\\\" height=\\\"40\\\" width=\\\"40\\\" border=\\\"1\\\" onMouseOver=\\\"ProductMouseOver('primage".$image_i."')\\\" onMouseOut=\\\"ProductMouseOut('primage".$image_i."');\\\"><div id=\\\"primage".$image_i."\\\" style=\\\"position:absolute; z-index:100; display:none;\\\"><table border=\\\"0\\\" cellspacing=\\\"0\\\" cellpadding=\\\"0\\\" width=\\\"170\\\"><tr bgcolor=\\\"#FFFFFF\\\"><td align=\\\"center\\\" width=\\\"100%\\\" height=\\\"150\\\" style=\\\"border:#000000 solid 1px;\\\"><img src=\\\"".$imagepath.$arraytinyimage[$viewproduct[$i]]."\\\" border=\\\"0\\\"></td></tr></table></div></td>\";\n";
																														} else {
																															$strlist.= "objlist.imgidx=\"<td class=\\\"td_con1\\\"><img src=images/space01.gif onMouseOver=\\\"ProductMouseOver('primage".$image_i."')\\\" onMouseOut=\\\"ProductMouseOut('primage".$image_i."');\\\"><div id=\\\"primage".$image_i."\\\" style=\\\"position:absolute; z-index:100; display:none;\\\"><table border=\\\"0\\\" cellspacing=\\\"0\\\" cellpadding=\\\"0\\\" width=\\\"170\\\"><tr bgcolor=\\\"#FFFFFF\\\"><td align=\\\"center\\\" width=\\\"100%\\\" height=\\\"150\\\" style=\\\"border:#000000 solid 1px;\\\"><img src=\\\"".$Dir."images/product_noimg.gif\\\" border=\\\"0\\\"></td></tr></table></div></td>\";\n";
																														}

																														$strlist.= "objlist.nameidx=\"<td class=\\\"td_con1\\\" align=\\\"left\\\" style=\\\"word-break:break-all;\\\"><img src=\\\"images/producttype".($arrayassembleuse[$viewproduct[$i]]=="Y"?"y":"n").".gif\\\" border=\\\"0\\\" align=\\\"absmiddle\\\" hspace=\\\"2\\\">".addslashes($arrayproductname[$viewproduct[$i]].($arrayselfcode[$viewproduct[$i]]?"-".$arrayselfcode[$viewproduct[$i]]:"").($arrayaddcode[$viewproduct[$i]]?"-".$arrayaddcode[$viewproduct[$i]]:""))."&nbsp;</td>\";\n";
																														$strlist.= "objlist.sellidx=\"<td align=\\\"right\\\" class=\\\"td_con1\\\"><img src=\\\"images/won_icon.gif\\\" border=\\\"0\\\" style=\\\"margin-right:2px;\\\"><span class=\\\"font_orange\\\">".number_format($arraysellprice[$viewproduct[$i]])."</span><br><img src=\\\"images/reserve_icon.gif\\\" border=\\\"0\\\" style=\\\"margin-right:2px;\\\">".($arrayreservetype[$viewproduct[$i]]!="Y"?number_format($arrayreserve[$viewproduct[$i]]):$arrayreserve[$viewproduct[$i]]."%")."</td>\";\n";
																														if (strlen($arrayquantity[$viewproduct[$i]])==0) {
																															$strlist.= "objlist.quantityidx=\"<td class=\\\"td_con1\\\">무제한</td>\";\n";
																														}else if ($arrayquantity[$viewproduct[$i]]<=0) {
																															$strlist.= "objlist.quantityidx=\"<td class=\\\"td_con1\\\"><span class=\\\"font_orange\\\"><b>품절</b></span></td>\";\n";
																														}else{
																															$strlist.= "objlist.quantityidx=\"<td class=\\\"td_con1\\\">".$arrayquantity[$viewproduct[$i]]."</td>\";\n";
																														}
																														$strlist.= "objlist.displayidx=\"<td class=\\\"td_con1\\\">".($arraydisplay[$viewproduct[$i]]=="Y"?"판매중</font>":"<font color=\\\"#FF4C00\\\">보류중</font>")."</td>\";\n";

																														$strlist.= "objlist.editidx=\"<td class=\\\"td_con1\\\"><img src=\\\"images/icon_newwin1.gif\\\" border=\\\"0\\\" onclick=\\\"ProductInfo('".$arraycode[$viewproduct[$i]]."');\\\" style=\\\"cursor:hand;\\\"></td>\";\n";
																														$strlist.= "objlist.deleteidx=\"<td class=\\\"td_con1\\\"><img src=\\\"images/icon_del1.gif\\\" border=\\\"0\\\" onclick=\\\"Delete('".$arraycode[$viewproduct[$i]]."');\\\" style=\\\"cursor:hand;\\\"></td>\";\n";
																														$strlist.= "objlist.no=\"".$jj--."\";\n";
																														$strlist.= "objlist.sort=\"".$ii."\";\n";
																														$strlist.= "objlist.selected=false;\n";
																														$strlist.= "all_list[".$ii."]=objlist;\n";
																														$strlist.= "objlist=null;\n";

																														echo "<tr>\n";
																														echo "	<td colspan=\"".$colspan."\" background=\"images/table_con_line.gif\"></td>\n";
																														echo "</tr>\n";
																														echo "<tr>\n";
																														echo "	<td id=\"idx_inner_".$ii."\" colspan=\"".$colspan."\" style=\"background-color:'#FFFFFF';\" onmouseover=\"if(this.style.backgroundColor != '#efefef')this.style.backgroundColor='#F4F7FC';\" onmouseout=\"if(this.style.backgroundColor != '#efefef')this.style.backgroundColor='#FFFFFF';\">\n";
																														echo "	<table border=\"0\" cellSpacing=\"0\" cellPadding=\"0\" width=\"100%\" style=\"table-layout:fixed;\" onclick=\"ChangeList('".$j."');\">\n";
																														echo "	<col width=40></col><col width=12></col>".($vendercnt>0?"<col width=70></col>":"")."<col width=50></col><col width=></col><col width=70></col><col width=45></col><col width=45></col><col width=45></col><col width=45></col>\n";
																														echo "	<tr align=\"center\">\n";
																														echo "		<td>".$j."</td>\n";
																														echo "		<td><a href=\"javascript:updown_click('".$j."','up')\"><img src=\"images/btn_plus.gif\" border=\"0\" style=\"margin-bottom:3px;\"></a><br><a href=\"javascript:updown_click('".$j."','down')\"><img src=\"images/btn_minus.gif\" border=\"0\" style=\"margin-top:3px;\"></a></td>\n";
																														
																														if($vendercnt>0) {
																															echo "		<td class=\"td_con1\"><B>".(strlen($venderlist[$arrayvender[$viewproduct[$i]]]->vender)>0?"<span onclick=\"viewVenderInfo(".$arrayvender[$viewproduct[$i]].");\">".$venderlist[$arrayvender[$viewproduct[$i]]]->id."</span>":"-")."</B></td>\n";
																														}

																														echo "		<td class=\"td_con1\">";
																														if (strlen($arraytinyimage[$viewproduct[$i]])>0 && file_exists($imagepath.$arraytinyimage[$viewproduct[$i]])==true){
																															echo "<img src=\"".$imagepath.$arraytinyimage[$viewproduct[$i]]."\" height=\"40\" width=\"40\" border=\"1\" onMouseOver=\"ProductMouseOver('primage".$image_i."')\" onMouseOut=\"ProductMouseOut('primage".$image_i."');\">";
																														} else {
																															echo "<img src=images/space01.gif onMouseOver=\"ProductMouseOver('primage".$image_i."')\" onMouseOut=\"ProductMouseOut('primage".$image_i."');\">";
																														}


																														echo "<div id=\"primage".$image_i."\" style=\"position:absolute; z-index:100; display:none;\"><table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"170\">\n";
																														echo "		<tr bgcolor=\"#FFFFFF\">\n";
																														if (strlen($arraytinyimage[$viewproduct[$i]])>0 && file_exists($imagepath.$arraytinyimage[$viewproduct[$i]])==true){
																															echo "		<td align=\"center\" width=\"100%\" height=\"150\" style=\"border:#000000 solid 1px;\"><img src=\"".$imagepath.$arraytinyimage[$viewproduct[$i]]."\" border=\"0\"></td>\n";
																														} else {
																															echo "		<td align=\"center\" width=\"100%\" height=\"150\" style=\"border:#000000 solid 1px;\"><img src=\"".$Dir."images/product_noimg.gif\" border=\"0\"></td>\n";
																														}
																														echo "		</tr>\n";
																														echo "		</table>\n";
																														echo "		</div>\n";
																														echo "		</td>\n";
																														echo "		<td class=\"td_con1\" align=\"left\" style=\"word-break:break-all;\"><img src=\"images/producttype".($arrayassembleuse[$viewproduct[$i]]=="Y"?"y":"n").".gif\" border=\"0\" align=\"absmiddle\" hspace=\"2\">".$arrayproductname[$viewproduct[$i]].($arrayselfcode[$viewproduct[$i]]?"-".$arrayselfcode[$viewproduct[$i]]:"").($arrayaddcode[$viewproduct[$i]]?"-".$arrayaddcode[$viewproduct[$i]]:"")."&nbsp;</td>\n";
																														echo "		<td align=right class=\"td_con1\"><img src=\"images/won_icon.gif\" border=\"0\" style=\"margin-right:2px;\"><span class=\"font_orange\">".number_format($arraysellprice[$viewproduct[$i]])."</span><br><img src=\"images/reserve_icon.gif\" border=\"0\" style=\"margin-right:2px;\">".($arrayreservetype[$viewproduct[$i]]!="Y"?number_format($arrayreserve[$viewproduct[$i]]):$arrayreserve[$viewproduct[$i]]."%")."</td>\n";
																														echo "		<td class=\"td_con1\">";
																														if (strlen($arrayquantity[$viewproduct[$i]])==0) {
																															echo "무제한";
																														}else if ($arrayquantity[$viewproduct[$i]]<=0) {
																															echo "<span class=\"font_orange\"><b>품절</b></span>";
																														}else {
																															echo $arrayquantity[$viewproduct[$i]];
																														}
																														echo "		</td>\n";
																														echo "		<td class=\"td_con1\">".($arraydisplay[$viewproduct[$i]]=="Y"?"<font color=\"#0000FF\">판매중</font>":"<font color=\"#FF4C00\">보류중</font>")."</td>";
																														echo "		<td class=\"td_con1\"><img src=\"images/icon_newwin1.gif\" border=\"0\" onclick=\"ProductInfo('".$arraycode[$viewproduct[$i]]."');\" style=\"cursor:hand;\"></td>\n";
																														echo "		<td class=\"td_con1\"><img src=\"images/icon_del1.gif\" border=\"0\" onclick=\"Delete('".$arraycode[$viewproduct[$i]]."');\" style=\"cursor:hand;\"></td>\n";
																														echo "	</tr>\n";
																														echo "	</table>\n";
																														echo "	</td>\n";
																														echo "</tr>\n";
																														$ii++;
																														$image_i++;	
																													}
																												}
																												
																												mysql_free_result($result);
																												$strlist.="</script>\n";
																												echo $strlist;
																												if ($j==0) {
																													echo "<tr><td colspan=\"".$colspan."\" background=\"images/table_con_line.gif\"></td></tr><tr><td class=\"td_con2\" colspan=\"".$colspan."\" align=\"center\">등록된 상품이 없습니다.</td></tr>";
																												}
																											} else {
																												echo "<tr><td colspan=\"".$colspan."\" background=\"images/table_con_line.gif\"></td></tr><tr><td class=\"td_con2\" colspan=\"".$colspan."\" align=\"center\">등록된 상품이 없습니다.</td></tr>";
																											}

																											
																											} else {
																												echo "<tr><td colspan=\"".$colspan."\" background=\"images/table_con_line.gif\"></td></tr><tr><td class=\"td_con2\"			colspan=\"".$colspan."\" align=\"center\">등록된 상품이 없습니다.</td></tr>";
																											}
																										?>
																										<tr>
																											<td height="1" colspan="<?=$colspan?>" background="images/table_con_line.gif"></td>
																										</tr>
																									</table>
																								</td>
																							</tr>
																							<tr>
																								<td background="images/table_top_line.gif"></td>
																							</tr>
																						</table>
																					</td>
																				</tr>
																				<input type=hidden name=mode value="modify">
																			</table>
																		<input type=hidden name=prcode>
																		<input type=hidden name=selcode>
																		<input type=hidden name=scrolltype value="0">
																		<input type=hidden name=change>
																		<input type=hidden name=selcodes>
																		<input type=hidden name=num value="">
																	</form>
																	</td>
																</tr>
																<tr>
																	<td height="15"></td>
																</tr>
																<tr>
																	<td colspan="2" align=center>
																		<a href="javascript:openSel('planning','<?=$pm_idx?>');"><img src="images/botteon_prinsert.gif" border="0"></a>
																		<a href="javascript:move_save();"><img src="images/btn_mainarray.gif" border="0"></a>
																	</td>
																</tr>
															</table>
														</td>
													</tr>
													<tr>
														<td height="50"></td>
													</tr>
													<tr>
														<td>
															<table cellpadding=0 cellspacing=0 width=100%>
																<tr>
																	<td><img src="images/manual_top1.gif" width=15 height=45 alt=""></td>
																	<td><img src="images/manual_title.gif" width=113 height=45 alt=""></td>
																	<td width="100%" background="images/manual_bg.gif"></td>
																	<td background="images/manual_bg.gif"></td>
																	<td><img src="images/manual_top2.gif" width=18 height=45 alt=""></td>
																</tr>
																<tr>
																	<td background="images/manual_left1.gif"></td>
																	<td colspan=3 width="100%" valign="top"  style="padding-top:8pt; padding-bottom:8pt; padding-left:4pt;" class="menual_bg">
																			<table cellpadding="0" cellspacing="0" width="100%">
																				<tr>
																					<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
																					<td ><span class="font_dotline">메인 상품 진열 설정</span></td>
																				</tr>
																				<tr>
																					<td width="20" align="right">&nbsp;</td>
																					<td  class="space_top">
																						- 메인 상품구성(이하 섹션)의 기본 섹션은 수정가능하나 등록가능한 수는 최대 4개로 제한됩니다.<br/>
																					</td>
																					<tr>
																						<td width="20" align="right">&nbsp;</td>
																						<td  class="space_top">&nbsp; </td>
																					</tr>
																				</tr>
																				<tr>
																					<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
																					<td ><span class="font_dotline">메인 상품 노출 등록</span></td>
																				</tr>
																				<tr>
																					<td width="20" align="right">&nbsp;</td>
																					<td  class="space_top">
																						- 메인 상품진열 섹션 선택에서 원하는 섹션 선택 후 "상품등록하기" 버튼을 클릭하시면 등록할 수 있는 팝업창(이하 모바일 쇼핑몰 상품관리 창)이 나타납니다.<br/>
																						- 모바일쇼핑몰 상품관리 창에서 원하는 카테고리 및 조건으로 검색 후 아래 등록된 상품 목록에서 원하는 상품을 클릭 하시면 됩니다.<br/>
																						- 동일한 메인진열 섹션에서 동일 상품은 한나만 진열됩니다<br/>
																						- 한 섹션에 등록가능한 상품수는 제한되지 않으나 모바일샵에서 노출되는 상품의 수는 최대 해당 섹션의 출력수를 초과하지 않습니다.<br/>
																						- 모바일샵 메인에 노출한 상품이 상품삭제등을 통해 제거 될 경우 노출되지 않습니다.<br/>
																					</td>
																					<tr>
																						<td width="20" align="right">&nbsp;</td>
																						<td  class="space_top">&nbsp; </td>
																					</tr>
																				</tr>
																				<tr>
																					<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
																					<td ><span class="font_dotline">메인 상품 노출 삭제</span></td>
																				</tr>
																				<tr>
																					<td width="20" align="right">&nbsp;</td>
																					<td  class="space_top">
																						- 원하는 섹션 선택후 우측 "삭제" 버튼을 클릭 하시면 진열된 상품을 삭제 하실 수 있습니다.<br/>
																					<tr>
																						<td width="20" align="right">&nbsp;</td>
																						<td  class="space_top">&nbsp; </td>
																					</tr>
																				</tr>
																				<tr>
																					<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
																					<td ><span class="font_dotline">상품 정보수정 및 삭제</span></td>
																				</tr>
																				<tr>
																					<td width="20" align="right">&nbsp;</td>
																					<td  class="space_top">
																						- 원하는 섹션 선택후 우측 "새창" 버튼을 클릭 하시면 해당 상품정보를 수정할수 있는 팝업창(이하 상품 등록/수정/삭제 창)이 나타납니다.<br/>
																						- 상품 등록/수정/삭제 창에서 해당 상품 정보 수정 후 아래 "상품정보 수정하기" 버튼을 클릭하시면 수정하실 수 있습니다.<br/>
																						- 상품 등록/수정/삭제 창에서 수정하실 경우 쇼핑몰 전체에서 해당 상품 정보가 변경되므로 수정시 유의 하시기 바랍니다<br/>
																						- 상품 등록/수정/삭제 창에서 하단 "삭제하기" 버튼을 클릭시 해당 상품을 삭제 하실 수 있으며 쇼핑몰 전체에서 삭제되므로 삭제시 유의 하시기 바랍니다<br/>
																					<tr>
																						<td width="20" align="right">&nbsp;</td>
																						<td  class="space_top">&nbsp; </td>
																					</tr>
																				</tr>
																			</table>
																		</td>
																	<td background="images/manual_right1.gif"><img src="images/manual_right1.gif" width=18 height="2" alt=""></td>
																</tr>
																<tr>
																	<td><img src="images/manual_left2.gif" width=15 height=8 alt=""></td>
																	<td colspan=3 background="images/manual_down.gif"><img src="images/manual_down.gif" width="4" height=8 alt=""></td>
																	<td><img src="images/manual_right2.gif" width=18 height=8 alt=""></td>
																</tr>
															</table>
														</td>
													</tr>
												</table>
											</td>
											<td width="16" background="images/con_t_02_bg.gif"></td>
										</tr>
										<tr>
											<td width="16"><img src="images/con_t_04.gif" width="16" height="16" border="0"></td>
											<td background="images/con_t_04_bg.gif"></td>
											<td width="16"><img src="images/con_t_03.gif" width="16" height="16" border="0"></td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<? include "copyright.php"; ?>