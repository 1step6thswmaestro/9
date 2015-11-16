<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/ext/func.php");
include_once($Dir.'lib/class/service.php');
$service = new service();
$svstate = $service->_usedList();
//_pr($svstate);
?>
<script type="text/javascript">

function showMenu(max,index,target){
	var view_t	=	document.getElementById(target+index);	
	for(var i=1;i<Number(max)+1;i++){
		document.getElementById(target+i).style.display="none";
	}
	view_t.style.display="block";
}

function content_arrow(max,index,target,way){
	var lt_cnt	=	document.getElementById("lt_cnt");
	var listNum	=	document.getElementById("topNum");

	if(way=="right"){
		if(lt_cnt.value == max){
			index = 1;
			lt_cnt.value	 =	1;
			listNum.innerHTML = lt_cnt.value;
		}else {
			index	=	Number(index) + 1;
			lt_cnt.value	 =	Number(lt_cnt.value) + 1
			listNum.innerHTML = lt_cnt.value;
		}
	}else if(way=="left"){
		if(lt_cnt.value == 1){
			index	=	max;
			lt_cnt.value	 =	max;
			listNum.innerHTML = lt_cnt.value;
		}else{
			index	=	Number(index) - 1;
			lt_cnt.value	 =	Number(lt_cnt.value) - 1
			listNum.innerHTML = lt_cnt.value;
		}
	}
	showMenu(max,lt_cnt.value,target);			
}

</script>
<style type="text/css">
ol, ul, li {list-style:none; width:100%}

#left-service {overflow:hidden; width:100%; height:211px; position:relative; overflow:hidden; padding:0; margin:0; background:#45464c;}
#left-service h2{text-align:left; padding:0; margin:0; }
#left-service h2 span.m-title{color:#ee4dab; padding:0; margin:0; height:27px;}	

#left-service div.list {margin:13px 0 20px 0; padding:0; height:135px; width:170px; overflow:hidden;}
#left-service div.list div.service_list {margin:0; padding:0; width:100%; height:135px; overflow:hidden;}
#left-service div.list div.service_list ol {float:left; margin:0; padding:0;}
#left-service div.list div.service_list ol li.con_li_list {float:left; margin:0; padding:0;}
.wid_name{float:left; margin:0; padding:0; width:110px; }	

#left-service p {float:left;}
#left-service p.topNum {float:left; width:30px; padding:0; margin:0 0 0 ; }
#left-service p.le_btn {overflow:hidden; cursor:pointer;  padding:0; margin:0;}
#left-service p.re_btn {overflow:hidden; cursor:pointer;  padding:0; margin:0;}

#bottom {float:right;}

.font_gray00{color:#818289;font-family:"Verdana";font-size:10px;letter-spacing:0pt;line-height:19px}
.font_gray00 a:link{color:#818289;font-family:"Verdana";font-size:10px;letter-spacing:0pt;line-height:19px}
.font_gray00 a:hover{color:#818289;font-family:"Verdana";font-size:10px;letter-spacing:0pt;line-height:19px}
.font_gray0 a:visited{color:#818289;font-family:"Verdana";font-size:10px;letter-spacing:0pt;line-height:19px}
.font_gray0{color:#D2D2D2;font-family:"Verdana";font-size:10px;letter-spacing:0pt;line-height:19px}
.font_gray0 a:link{color:#D2D2D2;font-family:"Verdana";font-size:10px;letter-spacing:0pt;line-height:19px}
.font_gray0 a:hover{color:#D2D2D2;font-family:"Verdana";font-size:10px;letter-spacing:0pt;line-height:19px}
.font_gray0 a:visited{color:#D2D2D2;font-family:"Verdana";font-size:10px;letter-spacing:0pt;line-height:19px}
.font_gray{color:#D2D2D2;font-family:"돋움";font-size:12px;letter-spacing:-0.5pt;line-height:19px}

</style>
<script language="javascript" type="text/javascript">
function orderService(code){
	switch(code){
		case 'secure':
			parent.topframe.GoMenu(10,'/admin/service_ssl.php?isorder=order');
			break;
		case 'log':
			parent.topframe.GoMenu(10,'/admin/service_log.php?isorder=order');
			break;
		case 'realname':
			parent.topframe.GoMenu(10,'/admin/service_ipin.php?isorder=order');
			break;
		default:
			alert('정의 되지 않은 호출 입니다.');
			break;
	}

}

</script>
<div id="left-service">
	<h2><span class="m-title"><img src="images/main_left_oper_t.gif"  alt="운영서비스 사용 현황" /></span></h2>
	<? 
		$sepTab = 9;
		$loopcnt = 0;
		$divopen = false;
		foreach($svstate as $svcode=>$svval){ 
			if($i++ == 0){ ?>
	<div id="list1" class="list">
		<div class="service_list">
			<ol class="con_li_list">
		<? 
				$divopen = true;	
			}else if(($i+1)%$sepTab ==0){ ?>
	<div id="list2"  class="list" style="display: none;">
		<div class="service_list">				
			<ol class="con_li_list">
		<? 
				$divopen = true;
			} 
			$statusmsg = '';
			if(!empty($svval['state']) && !empty($svval['state_txt'])){
				switch($svcode){
					case 'secure':
						
						break;
					case 'autobank':
						if(strtotime($svval['service_end']) > time()){
							$statusmsg = '<font style="color:#cece00;">~'.substr($svval['service_end'],0,10).'</font>';
						}
						break;							
				}
				if(empty($statusmsg)) $statusmsg = '<font style="color:#cece00; margin-left:7px; letter-spacing:-0.1em;">'.$svval['state_txt'].'</font>';
			}
			if(empty($statusmsg)){
				if(in_array($svcode,array("secure","log","realname","autobank"))) $statusmsg = '<a href="javascript:orderService(\''.$svcode.'\')"><img src="/admin/images/main_icon_order.gif" border="0" alt="신청" style="vertical-align:middle; margin-left:5px; "></a>';
				else $statusmsg = '<a href="http://www.getmall.co.kr/front/service_forms/service_'.$svcode.'.php?code='.$svcode.'" target="_blank"><img src="/admin/images/main_icon_order.gif" border="0" alt="신청" style="vertical-align:middle; margin-left:5px; "></a>';
			}
				
	?>
				<li class="font_gray"><img src="images/main_left_icon01.gif"><?=$svval['service_name']?><?=$statusmsg?></li>
		<? if(($i+2)%$sepTab ==0 && $divopen){ ?>
			</ol>
		</div>		
	</div>
	<? 		
				$divopen = false;;
			}			 
		} // end foreach
		if($divopen){  ?>
			</ol>
		</div>		
	</div>
	<? 		
		}
	?>
	
	<div  id="bottom">
		<p class="topNum"><span id="topNum" class="font_gray0">1</span><span class="font_gray00">/2</span></p>
		<p class="le_btn"><a href="javascript:content_arrow('2','1','list','left');"><img src="images/main_icon_pre.gif" border="0" width="17px" width="17" alt="이전"></a></p>
		<p class="re_btn"><a href="javascript:content_arrow('2','1','list','right');"><img src="images/main_icon_next.gif" border="0" width="16px" width="17" alt="다음"></a></p>
	</div>
	<input type="hidden" id="lt_cnt" name="lt_cnt" value="1" />
</div>