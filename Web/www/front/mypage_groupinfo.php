<?
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once($Dir."lib/shopdata.php");

	$groupcode = isset($_ShopInfo->memgroup)?trim($_ShopInfo->memgroup):"";

	$groupname = "";
	if(strlen($groupcode)>0){
		$grnameSQL ="SELECT group_name FROM tblmembergroup WHERE group_code = '".$groupcode."' ";
		if(false !== $grnameRes = mysql_query($grnameSQL)){
			
			$grnameNumRow = mysql_num_rows($grnameRes);
			if($grnameNumRow > 0){
				$groupname = trim(mysql_result($grnameRes, 0,0));
			}else{
				$groupname = '일반회원';
			}
			mysql_free_result($grnameRes);
		}
	}
?>