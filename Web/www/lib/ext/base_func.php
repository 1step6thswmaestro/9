<?php
include_once dirname(__FILE__).'/func.php';
// 택배사 정보를 코드 를 키로 가지는 연관 배열로 반환
// 전달 파라메터 $itemisArray 가 true 일 경우 각 배열 요소는 배열로 반환되고 그이외의 경우는 객체로 반환
function getDeliCompany($itemisArray=false){	
	$sql="SELECT * FROM tbldelicompany ORDER BY company_name ";
	$result=mysql_query($sql,get_db_conn());
	$delicomlist=array();
	if(mysql_num_rows($result) > 0){
		if($itemisArray === true){
			while($row=mysql_fetch_array($result)) $delicomlist[$row->code]=$row;
		}else{
			while($row=mysql_fetch_object($result)) $delicomlist[$row->code]=$row;
		}
	}
	return $delicomlist;
}
?>