<?
$i=0;
$ii=0;
$iii=0;
$iiii=0;
$strcodelist = "";
$strcodelist.= "<script>\n";
$result = mysql_query($sql,get_db_conn());
$selcode_name="";
while($row=mysql_fetch_object($result)) {
	$tmpcode=$row->codeA.$row->codeB.$row->codeC.$row->codeD;

	$strcodelist.= "var clist=new CodeList();\n";
	$strcodelist.= "clist.code='".$tmpcode."';\n";
	$strcodelist.= "clist.codeA='".$row->codeA."';\n";
	$strcodelist.= "clist.codeB='".$row->codeB."';\n";
	$strcodelist.= "clist.codeC='".$row->codeC."';\n";
	$strcodelist.= "clist.codeD='".$row->codeD."';\n";
	$strcodelist.= "clist.type='".$row->type."';\n";
	$strcodelist.= "clist.code_name='".str_replace("'","`",$row->code_name)."';\n";
	$strcodelist.= "clist.list_type='".$row->list_type."';\n";
	$strcodelist.= "clist.detail_type='".$row->detail_type."';\n";
	$strcodelist.= "clist.sort='".$row->sort."';\n";
	$strcodelist.= "clist.group_code='".$row->group_code."';\n";
	$selected="false";
	$display="none";
	$open="close";
	if($row->type=="L" || $row->type=="T" || $row->type=="LX" || $row->type=="TX" || $row->type=="S" || $row->type=="SX") {
		if($codeA==$row->codeA && $codeB==$row->codeB && $codeC==$row->codeC && $codeD==$row->codeD) {
			$selected="true";
			$strcodelist.= "seltype='".$row->type."';\n";
			$strcodelist.= "selcode='".$code."';\n";
		}
		if($codeA==$row->codeA) {
			$display="show";
			$selcode_name.=$row->code_name;
		}
		if($codeA==$row->codeA && $codeB!="000") {
			$open="open";
		}
		$strcodelist.= "clist.selected=".$selected.";\n";
		$strcodelist.= "clist.display='".$display."';\n";
		$strcodelist.= "clist.open='".$open."';\n";
		//$strcodelist.= "lista[".$i."]=clist;\n";
		$strcodelist.= "listaloop[listacount.toString()]=clist;\n";
		$strcodelist.= "listacount=Number(listacount)+1;\n";
		$i++;
	}
	if($row->type=="LM" || $row->type=="TM" || $row->type=="LMX" || $row->type=="TMX" || $row->type=="SM" || $row->type=="SMX") {
		if ($row->codeC=="000" && $row->codeD=="000") {
			if($codeA==$row->codeA && $codeB==$row->codeB && $codeC==$row->codeC && $codeD==$row->codeD) {
				$selected="true";
				$strcodelist.= "seltype='".$row->type."';\n";
				$strcodelist.= "selcode='".$code."';\n";
			}
			if($codeA==$row->codeA && $codeB!="000") {
				$display="show";
			}
			if($codeA==$row->codeA && $codeB==$row->codeB) {
				$selcode_name.=" > ".$row->code_name;
			}
			if($codeA==$row->codeA && $codeB==$row->codeB && $codeC!="000") {
				$open="open";
			}
			$strcodelist.= "clist.selected=".$selected.";\n";
			$strcodelist.= "clist.display='".$display."';\n";
			$strcodelist.= "clist.open='".$open."';\n";
			//$strcodelist.= "listb[".$ii."]=clist;\n";
			$strcodelist.= "if(!listbcount[clist.codeA]) { listbcount[clist.codeA]=0; }\n";
			$strcodelist.= "listbloop[clist.codeA+listbcount[clist.codeA].toString()]=clist;\n";
			$strcodelist.= "listbcount[clist.codeA]=Number(listbcount[clist.codeA])+1;\n";
			$ii++;
		} else if ($row->codeD=="000") {
			if($codeA==$row->codeA && $codeB==$row->codeB && $codeC==$row->codeC && $codeD==$row->codeD) {
				$selected="true";
				$strcodelist.= "seltype='".$row->type."';\n";
				$strcodelist.= "selcode='".$code."';\n";
			}
			if($codeA==$row->codeA && $codeB==$row->codeB && $codeC!="000") {
				$display="show";
			}
			if($codeA==$row->codeA && $codeB==$row->codeB && $codeC==$row->codeC) {
				$selcode_name.=" > ".$row->code_name;
			}
			if($codeA==$row->codeA && $codeB==$row->codeB && $codeC==$row->codeC && $codeD!="000") {
				$open="open";
			}
			$strcodelist.= "clist.selected=".$selected.";\n";
			$strcodelist.= "clist.display='".$display."';\n";
			$strcodelist.= "clist.open='".$open."';\n";
			//$strcodelist.= "listc[".$iii."]=clist;\n";
			$strcodelist.= "if(!listccount[clist.codeA+clist.codeB]) { listccount[clist.codeA+clist.codeB]=0; }\n";
			$strcodelist.= "listcloop[clist.codeA+clist.codeB+listccount[clist.codeA+clist.codeB].toString()]=clist;\n";
			$strcodelist.= "listccount[clist.codeA+clist.codeB]=Number(listccount[clist.codeA+clist.codeB])+1;\n";
			$iii++;
		} else if ($row->codeD!="000") {
			if($codeA==$row->codeA && $codeB==$row->codeB && $codeC==$row->codeC && $codeD==$row->codeD) {
				$strcodelist.= "seltype='".$row->type."';\n";
				$strcodelist.= "selcode='".$code."';\n";
				$selected="true";
				$display="show";
				$open="open";
				$selcode_name.=" > ".$row->code_name;
			}
			$strcodelist.= "clist.selected=".$selected.";\n";
			$strcodelist.= "clist.display='".$display."';\n";
			$strcodelist.= "clist.open='".$open."';\n";
			//$strcodelist.= "listd[".$iiii."]=clist;\n";
			$strcodelist.= "if(!listdcount[clist.codeA+clist.codeB+clist.codeC]) { listdcount[clist.codeA+clist.codeB+clist.codeC]=0; }\n";
			$strcodelist.= "listdloop[clist.codeA+clist.codeB+clist.codeC+listdcount[clist.codeA+clist.codeB+clist.codeC].toString()]=clist;\n";
			$strcodelist.= "listdcount[clist.codeA+clist.codeB+clist.codeC]=Number(listdcount[clist.codeA+clist.codeB+clist.codeC])+1;\n";
			$iiii++;
		}
	}
	$strcodelist.= "clist=null;\n\n";
	$strcodelist.= "selcode_name='".str_replace("'","`",$selcode_name)."';\n";
}
mysql_free_result($result);
$strcodelist.= "CodeInit();\n";
$strcodelist.= "</script>\n";

echo $strcodelist;
?>