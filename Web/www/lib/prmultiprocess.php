<?
if(substr(getenv("SCRIPT_NAME"),-19)=="/prmultiprocess.php") {
	header("HTTP/1.0 404 Not Found");
	exit;
}

$imagepath=$Dir.DataDir."shopimages/multi/";

if($type=="codedel") {				//분류삭제
	if(strlen($code)==12) {
		$codeA=substr($code,0,3);
		$codeB=substr($code,3,3);
		$codeC=substr($code,6,3);
		$codeD=substr($code,9,3);

		$likecode=$codeA;
		if($codeB!="000") {
			$likecode.=$codeB;
			if($codeC!="000") {
				$likecode.=$codeC;
				if($codeD!="000") {
					$likecode.=$codeD;
				}
			}
		}
		$sql = "DELETE FROM tblmultiimages WHERE productcode LIKE '".$likecode."%' ";
		mysql_query($sql,get_db_conn());
		if(!mysql_errno()) {
			proc_matchfiledel($imagepath."*_".$likecode."*");
		}
	}
} else if($type=="prdelete") {		//상품삭제
	if(strlen($productcode)==18) {
		$sql = "DELETE FROM tblmultiimages WHERE productcode='".$productcode."' ";
		mysql_query($sql,get_db_conn());
		if(!mysql_errno()) {
			proc_matchfiledel($imagepath."*_".$productcode."*");
		}
	}
} else if($type=="copy" || $type=="move") {			//상품복사 / 상품이동
	$fromprlist=explode("|",$code);
	$copyprlist=explode("|",$productcode);

	for($i=0;$i<count($fromprlist);$i++) {
		$sql = "SELECT * FROM tblmultiimages WHERE productcode='".$fromprlist[$i]."' ";
		$result=mysql_query($sql,get_db_conn());
		if($row=mysql_fetch_object($result)) {
		
			unset($plusfiles);
			$plusfiles=array($row->primg01,$row->primg02,$row->primg03,$row->primg04,$row->primg05,$row->primg06,$row->primg07,$row->primg08,$row->primg09,$row->primg10);

			$productcode=$copyprlist[$i];
			$sql = "INSERT INTO tblmultiimages ";
			$sql.= "(productcode,primg01,primg02,primg03,primg04,primg05,primg06,primg07,primg08,primg09,primg10,size) ";
			$sql.= "VALUES ('".$productcode."', ";
			for($y=0;$y<count($plusfiles);$y++) {
				$imgfile=ereg_replace($fromprlist[$i],$productcode,$plusfiles[$y]);
				$sql.= "'".$imgfile."', ";

				if(strlen($plusfiles[$y])>0) {
					copy($imagepath.$plusfiles[$y], $imagepath.$imgfile);
					copy($imagepath."s".$plusfiles[$y], $imagepath."s".$imgfile);
					if($type=="move" && file_exists($imagepath.$plusfiles[$y])==true) {
						unlink($imagepath.$plusfiles[$y]);
					}
					if($type=="move" && file_exists($imagepath."s".$plusfiles[$y])==true) {
						unlink($imagepath."s".$plusfiles[$y]);
					}
				}
			}
			$sql.= "'".$row->size."') ";
			mysql_query($sql,get_db_conn());
			if(!mysql_errno()) {
				if($type=="move") {
					$sql = "DELETE FROM tblmultiimages WHERE productcode='".$fromprlist[$i]."' ";
					mysql_query($sql,get_db_conn());
				}
			}
		}
		mysql_free_result($result);
	}
}
?>