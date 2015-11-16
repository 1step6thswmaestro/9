<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

if( $_POST[mode] == "venderProposalInsert" ) {
	$type = $_POST[type];
	$company = $_POST[company];
	//$zip = $_POST[home_post1]."-".$_POST[home_post2];
	$zip = $_POST[home_post1];
	$addr1 = $_POST[home_addr1];
	$addr2 = $_POST[home_addr2];
	$site = $_POST[site];
	$preSell = $_POST[preSell];
	$memNo = $_POST[memNo];
	$mall = $_POST[mall];
	$name = $_POST[name];
	$tell = $_POST[tell1]."-".$_POST[tell2]."-".$_POST[tell3];
	$phone = $_POST[phone1]."-".$_POST[phone2]."-".$_POST[phone3];
	$mail = $_POST[mail];
	$contents = $_POST[contents];


	$SQL = "
		INSERT tblVenderProposal SET
			type = '".$type."',
			company = '".$company."',
			comp_zip = '".$zip."',
			comp_addr1 = '".$addr1."',
			comp_addr2 = '".$addr2."',
			comp_site = '".$site."',
			pre_sell = '".$preSell."',
			comp_mem_no = '".$memNo."',
			etc_mall = '".$mall."',
			mng_name = '".$name."',
			mng_tell = '".$tell."',
			mng_phone = '".$phone."',
			mng_mail = '".$mail."',
			contents = '".$contents."',
			mem_id = '".$_ShopInfo->getMemid()."',
			reg_date = NOW() ;
	";

	mysql_query($SQL,get_db_conn());

	echo "
		<script>
			alert('저장하였습니다.');
			location.href='venderProposal.php';
		</script>
	";
}

?>
