<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
INCLUDE ("access.php");

####################### ������ ���ٱ��� check ###############
$PageCode = "pr-1";
$MenuCode = "nomenu";
if (!$_usersession->isAllowedTask($PageCode)) {
	INCLUDE ("AccessDeny.inc.php");
	exit;
}
#########################################################

$maxfilesize="512000";
$imagepath=$Dir.DataDir."shopimages/product/";

$mode=$_POST["mode"];
$mode_result=$_POST["mode_result"];

$code=$_POST["code"];
$parentcode=$_POST["parentcode"];

$up_code_name=$_POST["up_code_name"];
$up_type1=$_POST["up_type1"];
$up_type2=$_POST["up_type2"];
$up_group_code=$_POST["up_group_code"];
$up_sort=$_POST["up_sort"];

//������ Ÿ�� ���� �ʰ� �⺻������ ���� �ֵ���
$up_list_type = ($_POST["up_list_type"] == "")? 'AL001':$_POST["up_list_type"];
$up_detail_type = ($_POST["up_detail_type"] == "")? 'AD001':$_POST["up_detail_type"];
/*$up_list_type= isset($_POST["up_list_type"])? $_POST["up_list_type"]:'AL001';
$up_detail_type=isset($_POST["up_detail_type"])? $_POST["up_detail_type"]:'AD001';*/
$up_special=$_POST["up_special"];
$up_islist=$_POST["up_islist"];
$up_code_hide=$_POST["up_code_hide"];

$up_special_1_cols=(int)$_POST["up_special_1_cols"];
$up_special_1_rows=(int)$_POST["up_special_1_rows"];
$up_special_2_cols=(int)$_POST["up_special_2_cols"];
$up_special_2_rows=(int)$_POST["up_special_2_rows"];
$up_special_3_cols=(int)$_POST["up_special_3_cols"];
$up_special_3_rows=(int)$_POST["up_special_3_rows"];

$up_special_1_type=$_POST["up_special_1_type"];
$up_special_2_type=$_POST["up_special_2_type"];
$up_special_3_type=$_POST["up_special_3_type"];

$is_gcode=$_POST["is_gcode"];
$is_sort=$_POST["is_sort"];
$is_design=$_POST["is_design"];
$is_special=$_POST["is_special"];

$up_type=$_POST["up_type"];
//�Ҽȼ��� ��Ų����
if($up_type1 =="S" || $up_type =="S"){
	$up_list_type	= "SL001";
	$up_detail_type	= "SD001";
}


$dsameparent=$_POST["dsameparent"];

//���� ����ȸ�� ������ ���õǰ� ����
$arr_group_code=$_POST["arr_group_code"];
if($up_group_code=="ALL"){
	$up_group_code = "";
	for($i=0;$i<sizeof($arr_group_code);$i++){
		if($i!=sizeof($arr_group_code)-1){
			$up_group_code .= $arr_group_code[$i].",";
		}else{
			$up_group_code .= $arr_group_code[$i];
		}
	}
}else{
	$up_group_code = $up_group_code;
}

//ī�װ��� ����ǰ����Ұ�, ��������Ұ�, ��ȯ �� ȯ�ҺҰ� üũ  2012-04-13 �߰� ������ ���Ұ�
/*
$up_isgift = (!empty($_POST["up_isgift"])) ? $_POST["up_isgift"] : "N";
$up_iscoupon = (!empty($_POST["up_iscoupon"])) ? $_POST["up_iscoupon"] : "N";
$up_isrefund = (!empty($_POST["up_isrefund"])) ? $_POST["up_isrefund"] : "N";
$up_isreserve = (!empty($_POST["up_isreserve"])) ? $_POST["up_isreserve"] : "N";
*/
$up_isCheck = array('Y','N');
$up_isgift = (in_array($_POST["up_isgift"],$up_isCheck))?$_POST["up_isgift"]:"";
$up_iscoupon = (in_array($_POST["up_iscoupon"],$up_isCheck))?$_POST["up_iscoupon"]:"";
$up_isrefund = (in_array($_POST["up_isrefund"],$up_isCheck))?$_POST["up_isrefund"]:"";
$up_isreserve = (in_array($_POST["up_isreserve"],$up_isCheck))?$_POST["up_isreserve"]:"";

if ($mode=="insert" && strlen($up_code_name)>0) {
	if(strlen($parentcode)==12) {	//����ī�װ� �߰�
		$in_codeA=substr($parentcode,0,3);
		$in_codeB=substr($parentcode,3,3);
		$in_codeC=substr($parentcode,6,3);
		$in_codeD=substr($parentcode,9,3);

		$sql = "SELECT * FROM tblproductcode WHERE codeA='".$in_codeA."' AND codeB='".$in_codeB."' ";
		$sql.= "AND codeC='".$in_codeC."' AND codeD='".$in_codeD."' ";
		$result=mysql_query($sql,get_db_conn());
		$pobj = $row=mysql_fetch_object($result);
		mysql_free_result($result);
		if($row) {
			if(ereg("X",$row->type)) {
				echo "<script>parent.HiddenFrame.alert('����ī�װ� ������ �߸��Ǿ����ϴ�.');location.replace('".$_SERVER[PHP_SELF]."');</script>";
				exit;
			}
		} else {
			echo "<script>parent.HiddenFrame.alert('����ī�װ� ������ �߸��Ǿ����ϴ�.');location.replace('".$_SERVER[PHP_SELF]."');</script>";
			exit;
		}
		$type=$row->type;
		if(!ereg("M",$type)) $type.="M";

		$sql = "SELECT MAX(codeB) as maxcodeB, MAX(codeC) as maxcodeC, MAX(codeD) as maxcodeD ";
		$sql.= "FROM tblproductcode WHERE codeA='".$in_codeA."' ";
		if($in_codeB!="000") {
			$sql.= "AND codeB='".$in_codeB."' ";
		}
		if($in_codeC!="000") {
			$sql.= "AND codeC='".$in_codeC."' ";
		}
		$result = mysql_query($sql,get_db_conn());
		$row = mysql_fetch_object($result);
		mysql_free_result($result);

		if($in_codeB=="000" && $in_codeC=="000" && $in_codeD=="000") {
			$in_codeB=(int)$row->maxcodeB+1;
			$in_codeB="000".$in_codeB;
			$in_codeB=substr($in_codeB,-3);
		} else if($in_codeC=="000" && $in_codeD=="000") {
			$in_codeC=(int)$row->maxcodeC+1;
			$in_codeC="000".$in_codeC;
			$in_codeC=substr($in_codeC,-3);
		} else if($in_codeD=="000") {
			$in_codeD=(int)$row->maxcodeD+1;
			$in_codeD="000".$in_codeD;
			$in_codeD=substr($in_codeD,-3);
		}
		if (strlen($up_type2)==0 || $up_type2=="1" || $in_codeD!="000") {
			$type.="X";
		}

	} else {	//�ֻ��� ī�װ� �ű��߰�
		$sql = "SELECT MAX(codeA) as maxcode FROM tblproductcode WHERE type IN ('L','T','LX','TX','S','SX') ";
		$result = mysql_query($sql,get_db_conn());
		$row = mysql_fetch_object($result);
		mysql_free_result($result);
		$maxcode=(int)$row->maxcode+1;
		$maxcode="000".$maxcode;
		$maxcode=substr($maxcode,-3);
		$type=$up_type1;
		if ($up_type2=="1") {	//��ī�װ� ����
			$type.="X";
		}
		$in_codeA=$maxcode;
		$in_codeB="000";
		$in_codeC="000";
		$in_codeD="000";
	}
	if ($up_code_hide=="NO") {
		$up_group_code = "NO";
	}
	if(strlen($up_islist)==0) $up_islist="N";
	$in_special="";
	if(strlen($old_special)>0 && strlen($up_special)>0) {
		$arr_sp=explode(",",$old_special);
		for($i=0;$i<count($arr_sp);$i++) {
			if(eregi($arr_sp[$i],$up_special)) {
				$in_special.=$arr_sp[$i].",";
			}
		}
		$in_special=substr($in_special,0,-1);
	} else $in_special=$up_special;

	$in_special_cnt="";
	if(eregi("1",$in_special)) {
		if($up_special_1_cols<=0) $up_special_1_cols=5;
		if($up_special_1_rows<=0) $up_special_1_rows=1;
		if(strlen($up_special_1_type)==0) $up_special_1_type="I";
		$in_special_cnt.="1:".$up_special_1_cols."X".$up_special_1_rows."X".$up_special_1_type.",";
	}
	if(eregi("2",$in_special)) {
		if($up_special_2_cols<=0) $up_special_2_cols=5;
		if($up_special_2_rows<=0) $up_special_2_rows=1;
		if(strlen($up_special_2_type)==0) $up_special_2_type="I";
		$in_special_cnt.="2:".$up_special_2_cols."X".$up_special_2_rows."X".$up_special_2_type.",";
	}
	if(eregi("3",$in_special)) {
		if($up_special_3_cols<=0) $up_special_3_cols=5;
		if($up_special_3_rows<=0) $up_special_3_rows=1;
		if(strlen($up_special_3_type)==0) $up_special_3_type="I";
		$in_special_cnt.="3:".$up_special_3_cols."X".$up_special_3_rows."X".$up_special_3_type.",";
	}
	if(strlen($in_special_cnt)>0) $in_special_cnt=substr($in_special_cnt,0,-1);

	$sql = "INSERT tblproductcode SET ";
	$sql.= "codeA		= '".$in_codeA."', ";
	$sql.= "codeB		= '".$in_codeB."', ";
	$sql.= "codeC		= '".$in_codeC."', ";
	$sql.= "codeD		= '".$in_codeD."', ";
	$sql.= "type		= '".$type."', ";
	$sql.= "code_name	= '".$up_code_name."', ";

	if($dsameparent == '1'){
		$up_list_type = $pobj->list_type;
		$up_detail_type= $pobj->detail_type;
	}else{
		$dsameparent = "";
	}
	$sql.= "list_type	= '".$up_list_type."', ";
	$sql.= "detail_type	= '".$up_detail_type."', ";
	$sql.= "dsameparent	= '".$dsameparent."', ";


	$sql.= "sort		= '".$up_sort."', ";
	$sql.= "group_code	= '".$up_group_code."', ";
	$sql.= "special		= '".$in_special."', ";
	$sql.= "special_cnt	= '".$in_special_cnt."', ";
	$sql.= "islist		= '".$up_islist."', ";
	$sql.= "isgift		= '".$up_isgift."', ";
	$sql.= "iscoupon	= '".$up_iscoupon."', ";
	$sql.= "isrefund	= '".$up_isrefund."', ";


	$sql.= "syncNaverEp	= '".(($_POST['syncNaverEp'] == '0')?'0':'1')."', ";


	$sql.= "isreserve	= '".$up_reserve."' ";
	$insert = mysql_query($sql,get_db_conn());




	//////////////////////////////////////////////////
	//��� ���� �߰�
	if ($in_codeA!="000" && $in_codeB=="000" && $in_codeC=="000" && $in_codeD=="000") {
		$sql = "SELECT * FROM product_code_banner WHERE code='".$in_codeA."'";

		$result = mysql_query($sql,get_db_conn());
		$b_row = mysql_fetch_object($result);

		mysql_free_result($result);
	}

	$up_banner_file = $_FILES["up_banner_file"];
	$file_size = $up_banner_file[size];

	$up_banner_url = $_POST["up_banner_url"];
	$up_move_type = $_POST["up_move_type"];

	if($file_size > $maxfilesize) {
		echo "<script>alert(\"��ǰ�̹����� �� �뷮�� ".ceil($file_size/1024)."Kbyte�� 500K�� �ѽ��ϴ�.\\n\\n�ѹ��� �ø� �� �ִ� �ִ� �뷮�� 500K�Դϴ�.\\n\\n"."�̹����� gif�� �ƴϸ� �̹��� ������ �ٲپ� �ø��ø� �뷮�� �پ��ϴ�.\");history.go(-1);</script>\n";
		exit;
	}

	$filename = $up_banner_file[name];
	$file = $up_banner_file[tmp_name];

	if (strlen($filename)>0 && file_exists($file)) {
		$image_name = $in_codeA;

		$ext = strtolower(substr($filename,strlen($filename)-3,3));
		if ($ext=="gif" || $ext=="jpg") {
			$image = $image_name.".".$ext;
			move_uploaded_file($file,$imagepath.$image);
			chmod($imagepath.$image,0664);
		} else {
			$image="";
		}
	} else {
		$image = $b_row->banner_file;
	}

	$sql2 = "insert product_code_banner set ";
	$sql2 .= "code = '".$in_codeA."', ";
	$sql2 .= "banner_file = '".$image."', ";
	$sql2 .= "banner_url = '".$up_banner_url."', ";
	$sql2 .= "move_type = '".$up_move_type."' ";
	mysql_query($sql2,get_db_conn());
	//��� ���� �߰�





	if ($insert) {
		$log_content = "## ī�װ��Է� ## - �ڵ� ".$in_codeA.$in_codeB.$in_codeC.$in_codeD." - �ڵ�� : ".$up_code_name."";
		ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);

		$onload="<script>parent.NewCodeResult('".$in_codeA.$in_codeB.$in_codeC.$in_codeD."','".$type."','".$up_code_name."','".$up_list_type."','".$up_detail_type."','".$up_sort."','".$up_group_code."');parent.HiddenFrame.alert('��ǰī�װ� ����� �Ϸ�Ǿ����ϴ�.');</script>";
	} else {
		$onload="<script>parent.HiddenFrame.alert('��ǰī�װ� ����� ������ �߻��Ͽ����ϴ�.');</script>";
	}
} else if($mode=="modify" && strlen($code)==12) {
	$codeA=substr($code,0,3);
	$codeB=substr($code,3,3);
	$codeC=substr($code,6,3);
	$codeD=substr($code,9,3);

	$sql = "SELECT * FROM tblproductcode WHERE codeA='".$codeA."' AND codeB='".$codeB."' ";
	$sql.= "AND codeC='".$codeC."' AND codeD='".$codeD."' ";

	$result = mysql_query($sql,get_db_conn());
	$row = mysql_fetch_object($result);

	mysql_free_result($result);
	if(!$row) {
		echo "<script>parent.HiddenFrame.alert('�ش� ��ǰī�װ� ������ �������� �ʽ��ϴ�.');parent.location.reload();</script>";
		exit;
	}
	$type=$row->type;
	$osyncNaverEp = $row->syncNaverEp;

	// �θ� ī�װ� ȣ�� ����
	$parentInfo = array();
	if($codeB != '000'){
		$pwhere = array();
		for($i=0;$i<3;$i++){
			$key = 	'code'.chr(65+$i);
			$key2 = 'code'.chr(65+$i+1);
			if(${$key2} == '000') array_push($pwhere,$key."='000'");
			else array_push($pwhere,$key."='".${$key}."'");
		}
		array_push($pwhere,$key2."='000'");
		$psql = "select * from tblproductcode where ".implode(' and ',$pwhere)." limit 1";
		if(false !== $pres = mysql_query($psql,get_db_conn())){
			$parentInfo = mysql_fetch_assoc($pres);
		}
	}

	//��� ���� �߰� jdy
	if ($codeA!="000" && $codeB=="000" && $codeC=="000" && $codeD=="000") {
		$sql = "SELECT * FROM product_code_banner WHERE code='".$codeA."'";

		$result = mysql_query($sql,get_db_conn());
		$b_row = mysql_fetch_object($result);

		mysql_free_result($result);
	}
	//��� ���� �߰� jdy


	if ($mode_result=="result" && $up_code_name) {	//�������� ������Ʈ


		//��� ���� �߰� jdy
		$up_banner_file = $_FILES["up_banner_file"];
		$file_size = $up_banner_file[size];

		$up_banner_url = $_POST["up_banner_url"];
		$up_move_type = $_POST["up_move_type"];

		if($file_size > $maxfilesize) {
			echo "<script>alert(\"��ǰ�̹����� �� �뷮�� ".ceil($file_size/1024)."Kbyte�� 500K�� �ѽ��ϴ�.\\n\\n�ѹ��� �ø� �� �ִ� �ִ� �뷮�� 500K�Դϴ�.\\n\\n"."�̹����� gif�� �ƴϸ� �̹��� ������ �ٲپ� �ø��ø� �뷮�� �پ��ϴ�.\");history.go(-1);</script>\n";
			exit;
		}

		$filename = $up_banner_file[name];
		$file = $up_banner_file[tmp_name];

		if (strlen($filename)>0 && file_exists($file)) {
			$image_name = $codeA;

			$ext = strtolower(substr($filename,strlen($filename)-3,3));
			if ($ext=="gif" || $ext=="jpg") {
				$image = $image_name.".".$ext;
				move_uploaded_file($file,$imagepath.$image);
				chmod($imagepath.$image,0664);
			} else {
				$image="";
			}
		} else {
			$image = $b_row->banner_file;
		}
		//��� ���� �߰� jdy


		if ($up_code_hide=="NO") {
			$up_group_code = "NO";
		}
		if(strlen($up_islist)==0) $up_islist="N";
		$in_special="";
		if(strlen($old_special)>0 && strlen($up_special)>0) {
			$arr_sp=explode(",",$old_special);
			for($i=0;$i<count($arr_sp);$i++) {
				if(eregi($arr_sp[$i],$up_special)) {
					$in_special.=$arr_sp[$i].",";
				}
			}
			$in_special=substr($in_special,0,-1);
		} else $in_special=$up_special;

		$in_special_cnt="";
		if(eregi("1",$in_special)) {
			if($up_special_1_cols<=0) $up_special_1_cols=5;
			if($up_special_1_rows<=0) $up_special_1_rows=1;
			if(strlen($up_special_1_type)==0) $up_special_1_type="I";
			$in_special_cnt.="1:".$up_special_1_cols."X".$up_special_1_rows."X".$up_special_1_type.",";
		}
		if(eregi("2",$in_special)) {
			if($up_special_2_cols<=0) $up_special_2_cols=5;
			if($up_special_2_rows<=0) $up_special_2_rows=1;
			if(strlen($up_special_2_type)==0) $up_special_2_type="I";
			$in_special_cnt.="2:".$up_special_2_cols."X".$up_special_2_rows."X".$up_special_2_type.",";
		}
		if(eregi("3",$in_special)) {
			if($up_special_3_cols<=0) $up_special_3_cols=5;
			if($up_special_3_rows<=0) $up_special_3_rows=1;
			if(strlen($up_special_3_type)==0) $up_special_3_type="I";
			$in_special_cnt.="3:".$up_special_3_cols."X".$up_special_3_rows."X".$up_special_3_type.",";
		}
		if(strlen($in_special_cnt)>0) $in_special_cnt=substr($in_special_cnt,0,-1);

		$up_code_name = ereg_replace(";","",$up_code_name);
		$sql = "UPDATE tblproductcode SET ";
		$sql.= "code_name		= '".$up_code_name."', ";

		if($dsameparent == '1'){
			$up_list_type 	= $parentInfo['list_type'];
			$up_detail_type = $parentInfo['detail_type'];
		}else{
			$dsameparent = "";
		}

		$sql.= "list_type		= '".$up_list_type."', ";
		$sql.= "detail_type		= '".$up_detail_type."', ";
		$sql.= "dsameparent		= '".$dsameparent."', ";


		$sql.= "group_code		= '".$up_group_code."', ";
		$sql.= "sort			= '".$up_sort."', ";
		$sql.= "special			= '".$in_special."', ";
		$sql.= "special_cnt		= '".$in_special_cnt."', ";
		$sql.= "islist			= '".$up_islist."', ";
		$sql.= "isgift			= '".$up_isgift."', ";
		$sql.= "iscoupon		= '".$up_iscoupon."', ";
		$sql.= "isrefund		= '".$up_isrefund."', ";


		$sql.= "syncNaverEp	= '".(($_POST['syncNaverEp'] == '0')?'0':'1')."', ";

		$sql.= "isreserve	= '".$up_reserve."' ";
		$sql.= "WHERE codeA = '".$codeA."' AND codeB = '".$codeB."' ";
		$sql.= "AND codeC = '".$codeC."' AND codeD = '".$codeD."' ";
		$update = mysql_query($sql,get_db_conn());

		if ($update) {
			if(($is_gcode==1 || $is_sort==1 || $is_design==1 || $is_special==1) && !ereg("X",$type)) {
				$sql = "UPDATE tblproductcode SET ";
				if($is_gcode==1) $sql.= "group_code = '".$up_group_code."',";
				if($is_sort==1) $sql.= "sort = '".$up_sort."',";

				if($is_design==1) {
					$sql.= "list_type = '".$up_list_type."',";
					$sql.= "detail_type = '".$up_detail_type."',";
				}

				if($is_special==1) {
					$sql.= "special		= '".$in_special."',";
					$sql.= "special_cnt	= '".$in_special_cnt."',";
					$sql.= "islist		= '".$up_islist."',";
				}
				$sql = substr($sql,0,-1);
				$sql.= " WHERE codeA='".$codeA."' ";
				if($codeB!="000") {
					$sql.= "AND codeB='".$codeB."' ";
					if($codeC!="000") {
						$sql.= "AND codeC='".$codeC."' ";
					}
				}
				mysql_query($sql,get_db_conn());
			}

			// �����ΰ� ������ ���� ī�װ��� �θ� ������ ���� ������ �κп� ���� ó��
			if($codeA != '000'){
				$cwhere = array();
				$cdep = false;
				for($i=0;$i<3;$i++){
					$key = 	'code'.chr(65+$i);
					if(${$key} == '000'){
						array_push($cwhere,$key."!='000'");
						break;
					}else array_push($cwhere,$key."='".${$key}."'");
				}
				array_push($cwhere,"dsameparent='1'");
				$csql = "UPDATE  tblproductcode  SET list_type = '".$up_list_type."',detail_type = '".$up_detail_type."' where ".implode(' and ',$cwhere);
				@mysql_query($csql,get_db_conn());
			}


			if ($codeA!="000" && $codeB=="000" && $codeC=="000" && $codeD=="000") {

				if ($b_row->code) {
					$sql = "update product_code_banner set ";
					$sql .= "banner_file = '".$image."', ";
					$sql .= "banner_url = '".$up_banner_url."', ";
					$sql .= "move_type = '".$up_move_type."' ";

					$sql .= " where code = '".$codeA."' ";
				}else{
					$sql = "insert product_code_banner set ";
					$sql .= "code = '".$codeA."', ";
					$sql .= "banner_file = '".$image."', ";
					$sql .= "banner_url = '".$up_banner_url."', ";
					$sql .= "move_type = '".$up_move_type."' ";
				}

				mysql_query($sql,get_db_conn());
			}


			$onload="<script>parent.ModifyCodeResult('".$codeA.$codeB.$codeC.$codeD."','".$type."','".$up_code_name."','".$up_list_type."','".$up_detail_type."','".$up_sort."','".$up_group_code."','".$is_gcode."','".$is_sort."','".$is_design."');parent.HiddenFrame.alert('��ǰī�װ� ���� ������ �Ϸ�Ǿ����ϴ�.');</script>";
		} else {
			$onload="<script>parent.HiddenFrame.alert('��ǰī�װ� ���� ������ ������ �߻��Ͽ����ϴ�.');</script>";
		}





		//// ī�װ� �ɼ� ���濡 ���� ���� ��ǰ �� ī�װ� ���� ����
		$swhere = array();
		$likecode = '';

		for($i=0;$i<4;$i++){
			$key = 'code'.chr(65+$i);
			if(${$key} != '000'){
				array_push($swhere, $key."='".${$key}."'");
				$likecode .= ${$key};
			}else if(${$key} == '000'){
				array_push($swhere, $key."!='000'");
				break;
			}
		}
		$setCheckP = $setCheckC = array();

		if(!empty($up_iscoupon)){
			array_push($setCheckP,"etcapply_coupon='".(($up_iscoupon=='Y')?'N':'Y')."'");
			array_push($setCheckC,"iscoupon='".$up_iscoupon."'");
		}


		if(!empty($up_isreserve)){
			array_push($setCheckP,"etcapply_reserve='".(($up_isreserve=='Y')?'N':'Y')."'");
			array_push($setCheckC,"isreserve='".$up_isreserve."'");
		}

		if(!empty($up_isgift)){
			array_push($setCheckP,"etcapply_gift='".(($up_isgift=='Y')?'N':'Y')."'");
			array_push($setCheckC,"isgift='".$up_isgift."'");
		}


		if(!empty($up_isrefund)){
			array_push($setCheckP,"etcapply_return='".(($up_isrefund=='Y')?'N':'Y')."'");
			array_push($setCheckC,"isrefund='".$up_isrefund."'");
		}

		// ���̹� ���� ���� ���� ī�װ� update
		if($osyncNaverEp != $_POST['syncNaverEp']){
			array_push($setCheckP,"syncNaverEp='".(($_POST['syncNaverEp']=='0')?'0':'1')."'");
			array_push($setCheckC,"syncNaverEp='".(($_POST['syncNaverEp']=='0')?'0':'1')."'");
		}
		/*
		$up_isgift = (in_array($_POST["up_isgift"],$up_isCheck))?$_POST["up_isgift"]:"";
$up_iscoupon = (in_array($_POST["up_iscoupon"],$up_isCheck))?$_POST["up_iscoupon"]:"";
$up_isrefund = (in_array($_POST["up_isrefund"],$up_isCheck))?$_POST["up_isrefund"]:"";
$up_isreserve = (in_array($_POST["up_isreserve"],$up_isCheck))?$_POST["up_isreserve"]:"";
*/
		if(count($setCheckC) > 0){
			$sql = "update tblproductcode set ".implode(',',$setCheckC)." where ".implode(' and ',$swhere);
			mysql_query($sql,get_db_conn());
		}


		if(count($setCheckP) > 0){
			$sql = "update tblproduct set ".implode(',',$setCheckP)." where productcode like '".$likecode."%'";
			//echo $sql;
			mysql_query($sql,get_db_conn());
		}


		$sql = "SELECT * FROM tblproductcode WHERE codeA='".$codeA."' AND codeB='".$codeB."' ";
		$sql.= "AND codeC='".$codeC."' AND codeD='".$codeD."' ";
		$result = mysql_query($sql,get_db_conn());
		$row = mysql_fetch_object($result);
		mysql_free_result($result);

		//��� ���� �߰� jdy
		if ($codeA!="000" && $codeB=="000" && $codeC=="000" && $codeD=="000") {
			$sql = "SELECT * FROM product_code_banner WHERE code='".$codeA."'";

			$result = mysql_query($sql,get_db_conn());
			$b_row = mysql_fetch_object($result);

			mysql_free_result($result);
		}
		//��� ���� �߰� jdy

	}
	$type=$row->type;
	$code_name=$row->code_name;
	$list_type=$row->list_type;
	$detail_type=$row->detail_type;
	$group_code=$row->group_code;
	$sort=$row->sort;
	$special=$row->special;
	$special_cnt=$row->special_cnt;
	$islist=$row->islist;
	//ī�װ��ɼ�
	$isgift=$row->isgift;
	$iscoupon=$row->iscoupon;
	$isrefund=$row->isrefund;
	$isreserve=$row->isreserve;


	$syncNaverEp=$row->syncNaverEp;

	$dsameparent=$row->dsameparent;


	$arr_special=explode(",",$special);
	$old_special=$special;
	unset($special);
	for($i=0;$i<count($arr_special);$i++) {
		$special[$arr_special[$i]]="Y";
	}

	if(strlen($old_special)==0) {
		$old_special="1,2,3";
	} else {
		if(!eregi("1",$old_special)) {
			$old_special.=",1";
		}
		if(!eregi("2",$old_special)) {
			$old_special.=",2";
		}
		if(!eregi("3",$old_special)) {
			$old_special.=",3";
		}
	}

	$arrspecialcnt=explode(",",$special_cnt);
	for ($i=0;$i<count($arrspecialcnt);$i++) {
		if (substr($arrspecialcnt[$i],0,2)=="1:") {
			$tmpsp1=substr($arrspecialcnt[$i],2);
		} else if (substr($arrspecialcnt[$i],0,2)=="2:") {
			$tmpsp2=substr($arrspecialcnt[$i],2);
		} else if (substr($arrspecialcnt[$i],0,2)=="3:") {
			$tmpsp3=substr($arrspecialcnt[$i],2);
		}
	}
	if(strlen($tmpsp1)>0) {
		$special_1=explode("X",$tmpsp1);
		$special_1_cols=(int)$special_1[0];
		$special_1_rows=(int)$special_1[1];
		$special_1_type=$special_1[2];
	}
	if(strlen($tmpsp2)>0) {
		$special_2=explode("X",$tmpsp2);
		$special_2_cols=(int)$special_2[0];
		$special_2_rows=(int)$special_2[1];
		$special_2_type=$special_2[2];
	}
	if(strlen($tmpsp3)>0) {
		$special_3=explode("X",$tmpsp3);
		$special_3_cols=(int)$special_3[0];
		$special_3_rows=(int)$special_3[1];
		$special_3_type=$special_3[2];
	}

	if($special_1_cols<=0) $special_1_cols=5;
	if($special_1_rows<=0) $special_1_rows=1;
	if(strlen($special_1_type)==0) $special_1_type="I";
	if($special_2_cols<=0) $special_2_cols=5;
	if($special_2_rows<=0) $special_2_rows=1;
	if(strlen($special_2_type)==0) $special_2_type="I";
	if($special_3_cols<=0) $special_3_cols=5;
	if($special_3_rows<=0) $special_3_rows=1;
	if(strlen($special_3_type)==0) $special_3_type="I";

	$type1=substr($type,0,1);
	if (ereg("X",$type)) {
		$type2="1";	//����ī�װ� ����
	} else {
		$type2="0";	//����ī�װ� ����
	}

	$gong="N";
	if (substr($row->list_type,0,1)=="B") {
		$gong="Y";
	}

	$code_loc = "";
	$sql = "SELECT code_name,type FROM tblproductcode WHERE codeA='".substr($code,0,3)."' ";
	if(substr($code,3,3)!="000") {
		$sql.= "AND (codeB='".substr($code,3,3)."' OR codeB='000') ";
		if(substr($code,6,3)!="000") {
			$sql.= "AND (codeC='".substr($code,6,3)."' OR codeC='000') ";
			if(substr($code,9,3)!="000") {
				$sql.= "AND (codeD='".substr($code,9,3)."' OR codeD='000') ";
			} else {
				$sql.= "AND codeD='000' ";
			}
		} else {
			$sql.= "AND codeC='000' ";
		}
	} else {
		$sql.= "AND codeB='000' AND codeC='000' ";
	}
	$sql.= "ORDER BY codeA,codeB,codeC,codeD ASC ";
	//echo $sql; exit;
	$result=mysql_query($sql,get_db_conn());
	$i=0;
	while($row=mysql_fetch_object($result)) {
		if($i>0) $code_loc.= " > ";
		$code_loc.= $row->code_name;
		$i++;
	}
	mysql_free_result($result);

	/* ��� ���� �߰�jdy */
	$up_banner_file = $b_row->banner_file;
	$up_banner_url = $b_row->banner_url;
	$up_move_type = $b_row->move_type;
	$banner_img = "";

	if (!empty($up_banner_file) && file_exists($imagepath.$up_banner_file)) {
		$banner_img = "<img src=\"".$imagepath.$up_banner_file."\" width=\"200\" />";
	}

	/* ��� ���� �߰�jdy */

} else if ($mode=='banner_del') {

	$codeA=substr($code,0,3);
	$codeB=substr($code,3,3);
	$codeC=substr($code,6,3);
	$codeD=substr($code,9,3);

	//����̹��� ����
	$sql = "SELECT * FROM product_code_banner WHERE code='".$codeA."'";

	$result = mysql_query($sql,get_db_conn());
	$b_row = mysql_fetch_object($result);

	mysql_free_result($result);

	$up_banner_file = $b_row->banner_file;

	if (!empty($up_banner_file) && file_exists($imagepath.$up_banner_file)) {
		unlink($imagepath.$up_banner_file);

		$sql = "update product_code_banner set banner_file='' WHERE code='".$codeA."'";
		mysql_query($sql,get_db_conn());
	}

	echo "
		<form name=form1 action=product_code.property.php method=post>
		<input type=hidden name=mode value=modify>
		<input type=hidden name=code value=".$code.">
		</form>

		<script>alert('��ʰ� ���� �Ǿ����ϴ�.');document.form1.submit();</script>
	";
	exit();

} else {
	$mode="insert";
	$islist="Y";
	//ī�װ��ɼ�
	$isgift="Y";
	$iscoupon="Y";
	$isrefund="Y";
	$isreserve="Y";
	if(strlen($old_special)==0) $old_special="1,2,3";
	$special_cnt=4;

	$special_1_type="I";
	$special_1_cols=5;
	$special_1_rows=1;
	$special_2_type="I";
	$special_2_cols=5;
	$special_2_rows=1;
	$special_3_cols=5;
	$special_3_type="I";
	$special_3_rows=1;

	$syncNaverEp= '1';
}

if(strlen($code)==0 && strlen($parentcode)==0) {
	$code_loc = "�ֻ��� ī�װ�";
} else if(strlen($parentcode)==12) {
	if(substr($parentcode,9,3)!="000") {
		echo "<script>parent.HiddenFrame.alert('����ī�װ� ������ �߸��Ǿ����ϴ�.');location.replace('".$_SERVER[PHP_SELF]."');</script>";
		exit;
	} else {
		$sql = "SELECT type,syncNaverEp FROM tblproductcode ";
		$sql.= "WHERE codeA='".substr($parentcode,0,3)."' ";
		$sql.= "AND codeB='".substr($parentcode,3,3)."' ";
		$sql.= "AND codeC='".substr($parentcode,6,3)."' ";
		$sql.= "AND codeD='".substr($parentcode,9,3)."' ";
		$result=mysql_query($sql,get_db_conn());
		if($row=mysql_fetch_object($result)) {
			if(ereg("X",$row->type)) {
				echo "<script>parent.HiddenFrame.alert('����ī�װ� ������ �߸��Ǿ����ϴ�.');location.replace('".$_SERVER[PHP_SELF]."');</script>";
				exit;
			}
		} else {
			echo "<script>parent.HiddenFrame.alert('����ī�װ� ������ �߸��Ǿ����ϴ�.');location.replace('".$_SERVER[PHP_SELF]."');</script>";
			exit;
		}

		$syncNaverEp= $row->syncNaverEp; // ���̹� ���� ���� ���� ����



		mysql_free_result($result);
	}
	$code_loc = "";
	$sql = "SELECT code_name,type FROM tblproductcode WHERE codeA='".substr($parentcode,0,3)."' ";
	if(substr($parentcode,3,3)!="000") {
		$sql.= "AND (codeB='".substr($parentcode,3,3)."' OR codeB='000') ";
		if(substr($parentcode,6,3)!="000") {
			$sql.= "AND (codeC='".substr($parentcode,6,3)."' OR codeC='000') ";
		} else {
			$sql.= "AND codeC='000' ";
		}
	} else {
		$sql.= "AND codeB='000' AND codeC='000' ";
	}
	$sql.= "AND codeD='000' ";
	$sql.= "ORDER BY codeA,codeB,codeC,codeD ASC ";
	//echo $sql; exit;
	$result=mysql_query($sql,get_db_conn());
	$i=0;
	while($row=mysql_fetch_object($result)) {
		if($i>0) $code_loc.= " >> ";
		$code_loc.= $row->code_name;
		$type1=substr($row->type,0,1);
		$i++;
	}
	mysql_free_result($result);

	if(substr($parentcode,6,3)!="000") {
		$type2=1;
	}


	$dsameparent = '1';
}
?>

<? INCLUDE "header.php"; ?>

<script type="text/javascript" src="lib.js.php"></script>
<script>var LH = new LH_create();</script>
<script for=window event=onload>LH.exec();</script>
<script>//LH.add("parent_resizeIframe('PropertyFrame')");</script>

<SCRIPT LANGUAGE="JavaScript">
<!--
function DesignList(idx) {
	document.form1.gong[idx].checked=true;
	checkDesignSame(null);

	if(document.form1.gong[0].checked==true) gong="N";
	else gong="Y";
	up_list_type=document.form1.up_list_type.value;
	window.open("design_productlist.php?code="+up_list_type+"&gong="+gong,"design","height=450,width=380,scrollbars=yes");
}

function DesignDetail(idx) {
	document.form1.gong[idx].checked=true;
	checkDesignSame(null);

	if(document.form1.gong[0].checked==true) gong="N";
	else gong="Y";
	up_detail_type=document.form1.up_detail_type.value;
	window.open("design_productdetail.php?code="+up_detail_type+"&gong="+gong,"design2","height=450,width=380,scrollbars=yes");
}

function ChangeSequence() {
	txt=document.form1.fcode.options[document.form1.fcode.selectedIndex].text;
	if((num=txt.indexOf("(�����ī�װ�)"))>0) document.form1.selectedfcodename.value=txt.substr(0,num);
	else document.form1.selectedfcodename.value = txt;
}

function GroupCheck(checked){
	if (checked==true) {
		alert('ī�װ��� ������ ���ο� ǥ�õ� ��ǰ�� �״�� ǥ�õ˴ϴ�.\nȮ���� ���λ�ǰ�� ���� ���� ���ο��� ������ ���ּž� �մϴ�.');
		document.form1.up_group_code.disabled=true;
	} else {
		document.form1.up_group_code.disabled=false;
	}
}

function Save() {
	mode = document.form1.mode.value;
	if (document.form1.up_code_name.value.length==0) {
		document.form1.up_code_name.focus();
		alert("ī�װ����� �Է��ϼ���.");
		return;
	}
	if (CheckLength(document.form1.up_code_name)>100) {
		alert('�� �Է°����� ���̰� �ѱ� 50�ڱ����Դϴ�. �ٽ��ѹ� Ȯ���Ͻñ� �ٶ��ϴ�.');
		document.form1.up_code_name.focus();
		return;
	}
	if (mode=="insert") {
		if(typeof(document.form1.up_type1)=="object") {
			if (document.form1.up_type1[0].checked==false && document.form1.up_type1[1].checked==false && document.form1.up_type1[2].checked==false) {
				alert("ī�װ� Ÿ���� �����ϼ���.");
				return;
			}
		}
		if(typeof(document.form1.up_type2)=="object") {
			if (document.form1.up_type2[0].checked==false && document.form1.up_type2[1].checked==false) {
				alert("����ī�װ� ������ �����ϼ���.");
				return;
			}
		}
	}
<?if ($type1!="S") {?>
	social_chk = false;
	//�ҼȽ�Ų��������
	/*
	if(typeof(document.form1.up_type1)=="object" && document.form1.up_type1[2].checked==true){
		social_chk = true;
    }
	*/
	if((typeof(document.form1.up_type1)=="object" && document.form1.up_type1[2].checked==true) || (document.form1.dsameparent && document.form1.dsameparent.checked)){
		social_chk = true;
    }

	/*if(!social_chk ){
		if (document.form1.up_list_type.value.length==0) {
			alert("��ǰ���� �������� �����ϼ���.");
			if(document.form1.gong[0].checked==true) DesignList(0);
			else DesignList(1);
			return;
		} else {
			list_type=document.form1.up_list_type.value.substring(0,1);
			if(document.form1.gong[0].checked==true) {
				if(list_type!="A") {
					alert("��ǰ���� �������� �����ϼ���.");
					DesignList(0);
					return;
				}
			} else {
				if(list_type!="B") {
					alert("��ǰ���� �������� �����ϼ���.");
					DesignList(1);
					return;
				}
			}
		}
		if (document.form1.up_detail_type.value.length==0) {
			alert("��ǰ�� �������� �����ϼ���.");
			if(document.form1.gong[0].checked==true) DesignDetail(0);
			else DesignDetail(1);
			return;
		} else {
			detail_type=document.form1.up_detail_type.value.substring(0,1);
			if(document.form1.gong[0].checked==true) {
				if(detail_type!="A") {
					alert("��ǰ�� �������� �����ϼ���.");
					DesignDetail(0);
					return;
				}
			} else {
				if(detail_type!="B") {
					alert("��ǰ�� �������� �����ϼ���.");
					DesignDetail(1);
					return;
				}
			}
		}
	}*/
<? }?>

	/*
	if (document.form1.up_sort.selectedIndex<=0) {
		alert("��ǰ ���� ����� �����ϼ���.");
		return;
	}
	*/
	up_special="";
	for(i=0;i<document.form1.tmp_special.length;i++) {
		if(document.form1.tmp_special[i].checked==true) {
			up_special+=","+document.form1.tmp_special[i].value;
		}
	}
	if(up_special.length>0) {
		up_special=up_special.substring(1,up_special.length);
	}
	document.form1.up_special.value=up_special;
	document.form1.submit();
}

function DesignMsg(type){
	if (type==0 && confirm("�Ϲݼ��θ�Ÿ������ ��ǰ�� �����Ǵ� ����Դϴ�!\n��ǰ�������ð� ��ǰ�󼼼����� ������ �ּ���!")) {
		document.form1.gong[0].checked=true;
	} else if(type==0) {
		document.form1.gong[1].checked=true;
	} else if (type==1 && confirm("��������Ÿ������ ��ǰ�� �����Ǵ� ����Դϴ�!\n������ǰ�������ð� ������ǰ�󼼼����� ������ �ּ���!")) {
		document.form1.gong[1].checked=true;
	} else if(type==1) {
		document.form1.gong[0].checked=true;
	}
	checkDesignSame(null);

}

function CodeDelete() {
	submit=true;
	con = "�����Ͻðڽ��ϱ�?\n����ī�װ� �� ��ǰ�� ��� �������ϴ�.";
	con2= "ī�װ������� ����ī�װ� �� ��ǰ�� �����ǿ��� ������ �Ͻñ� �ٶ��ϴ�.\n\n����Ȯ���� �մϴ�."
	if (confirm(con)) {
		if (!confirm(con2)) submit=false;
	} else submit=false;
	if (submit) {
		parent.CodeDelete2(document.form1.code.value);
	}
}

var clickgbn=false;
function ChildCodeClick() {
	WinObj=eval("document.all.child_layer");
	if(clickgbn==false) {
		WinObj.style.visibility = "visible";
		clickgbn=true;
	} else if (clickgbn==true) {
		WinObj.style.visibility = "hidden";
		clickgbn=false;
	}
}

function chGroupCode(val) {
	WinObj=eval("document.all.gcode_layer");

	if(val=="ALL") {
		WinObj.style.display = "";
	} else{
		WinObj.style.display = "none";
	}
}

function checkDesignSame(el){
	if(document.form1.dsameparent){
		if(el == document.form1.dsameparent){
			if(document.form1.dsameparent.checked){
				document.form1.gong[0].checked = false;
				document.form1.gong[1].checked = false;
			}
		}else{
			if(el == document.form1.dsameparent){
				document.form1.gong[0].checked = false;
				document.form1.gong[1].checked = false;
			}else{
				document.form1.dsameparent.checked = false;
			}
		}
	}
}

function deleteBannerImg() {

	if(confirm("��� �̹����� �����Ͻðڽ��ϱ�?")) {
		document.form1.mode.value="banner_del";
		document.form1.submit();
	}
}
//-->
</SCRIPT>
<table cellpadding="0" cellspacing="0" width="100%" height="100%" bgcolor="#ffffff">
<tr>
	<td width="100%" bgcolor="#FFFFFF"><IMG SRC="images/product_cate_function_title.gif" ALT="" /></td>
</tr>
<tr>
	<td width="100%" height="100%" valign="top" style="BORDER:#FF8730 2px solid;padding-left:5px;padding-right:5px;">
	<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
	<col width=141></col>
	<col width=""></col>
	<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post onsubmit="return false" enctype="multipart/form-data">
	<input type=hidden name=mode value="<?=$mode?>">
	<input type=hidden name=code value="<?=$code?>">
	<input type=hidden name=parentcode value="<?=$parentcode?>">
	<input type=hidden name=mode_result value="result">
	<input type=hidden name=up_list_type value="<?=$list_type?>">
	<input type=hidden name=up_detail_type value="<?=$detail_type?>">
	<input type=hidden name=old_special value="<?=$old_special?>">
	<input type=hidden name=up_special>
	<tr>
		<td colspan="2" height="10"></td>
	</tr>
	<TR>
		<TD colspan="2" background="images/table_con_line.gif"></TD>
	</TR>
	<?if($mode=="modify"){?>
	<TR>
		<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0"><b>ī�װ� �ڵ�</b></TD>
		<TD class="td_con1"><B><?=$code?></B></TD>
	</TR>
	<TR>
		<TD colspan="2" background="images/table_con_line.gif"></TD>
	</TR>
	<?}?>

	<TR>
		<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0"><b>ī�װ���</b></TD>
		<TD class="td_con1"><input type=text name=up_code_name size=38 maxlength=100 value="<?=htmlspecialchars($code_name)?>" class="input_selected" style=width:100%></TD>
	</TR>
	<TR>
		<TD colspan="2" background="images/table_con_line.gif"></TD>
	</TR>
	<TR>
		<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">ī�װ���ġ</TD>
		<TD class="td_con1"><?=$code_loc?></TD>
	</TR>
	<TR>
		<TD colspan="2" background="images/table_con_line.gif"></TD>
	</TR>
	<TR>
		<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">ī�װ�Ÿ��</TD>
		<TD class="td_con1">
<?
	if ($mode=="modify" || (strlen($parentcode)==12 && strlen($type1)>0)) {
		if ($type1=="L") echo "�⺻ ī�װ�";
		else if ($type1=="T") echo "���� ī�װ�";
		else if ($type1=="S") echo "�Ҽ� ī�װ�<input type=\"hidden\" name=\"up_type\" value=\"S\">";
	} else {
		echo "<input type=radio id=\"idx_type1_1\" name=up_type1 value=\"L\" checked style=\"BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;\"><label style='cursor:hand;' onmouseover=\"style.textDecoration='underline'\" onmouseout=\"style.textDecoration='none'\" for=idx_type1_1>�⺻ ī�װ�</label> <input type=radio id=\"idx_type1_2\" name=up_type1 value=\"T\" style=\"BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;\"><label style='cursor:hand;' onmouseover=\"style.textDecoration='underline'\" onmouseout=\"style.textDecoration='none'\" for=idx_type1_2>���� ī�װ�</label> <input type=radio id=\"idx_type1_3\" name=up_type1 value=\"S\" style=\"BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;\"><label style='cursor:hand;' onmouseover=\"style.textDecoration='underline'\" onmouseout=\"style.textDecoration='none'\" for=idx_type1_3>�Ҽ� ī�װ�</label>";
	}
?>
		</TD>
	</TR>
	<TR>
		<TD colspan="2" background="images/table_con_line.gif"></TD>
	</TR>
	<TR>
		<TD class="table_cell">
			<img src="images/icon_point2.gif" width="8" height="11" border="0">����ī�װ�����
		</TD>
		<TD class="td_con1">
<?
		if ($mode=="modify" || (strlen($parentcode)==12 && $type2==1)) {
			if ($type2=="0") echo "����ī�װ� ����";
			else echo "����ī�װ� ����";
		} else {
			echo "<input type=radio id=\"idx_type2_1\" name=up_type2 value=\"0\" checked style=\"BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;\"><label style='cursor:hand;' onmouseover=\"style.textDecoration='underline'\" onmouseout=\"style.textDecoration='none'\" onclick=\"javascript:alert('ī�װ� ��Ͻ� �ѹ� ������ ����ī�װ������� ������ �Ұ��� �ϹǷ� ������ ������ �ּ���.');\" for=idx_type2_1>����ī�װ� ����</label> <input id=\"idx_type2_2\" type=radio name=up_type2 value=\"1\" style=\"BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;\"><label style='cursor:hand;' onmouseover=\"style.textDecoration='underline'\" onmouseout=\"style.textDecoration='none'\" onclick=\"javascript:alert('ī�װ� ��Ͻ� �ѹ� ������ ����ī�װ������� ������ �Ұ��� �ϹǷ� ������ ������ �ּ���.');\" for=idx_type2_2>����ī�װ� ����</labal>";
		}
?>
		</TD>
	</TR>
	<TR>
		<TD colspan="2" background="images/table_con_line.gif"></TD>
	</TR>



	<? //���������� ��� ���� jdy ( remake:x2chi ) ?>
	<?// if ($mode=="modify" && $codeA !='000' && $codeB=='000' && $codeC=='000' && $codeD=='000') { ?>
	<? if ($mode=="insert" && !$parentcode || $mode=="modify" && $codeA !='000' && $codeB=='000' && $codeC=='000' && $codeD=='000') { ?>
		<TR>
			<TD class="table_cell">
				<img src="images/icon_point2.gif" width="8" height="11" border="0">�޴� ���̾� ���
			</TD>
			<TD class="td_con1" style="padding:0px;">
				<table cellpadding="0" cellspacing="0" width="100%" height="100%" bgcolor="#ffffff">
					<tr>
						<td class="table_cell">��� �̹���</td>
						<td class="td_con1">
							<?
								if ($banner_img) {
									echo $banner_img;
									echo "&nbsp;&nbsp;<img src=images/btn_del.gif style=\"cursor:pointer;\" alt=\"����\" onclick=\"deleteBannerImg()\">";
								}else{
									echo "��ϵ� ��ʰ� �����ϴ�.";
								}
							?>
						</td>
					</tr>
					<TR>
						<TD colspan="2" background="images/table_con_line.gif"></TD>
					</TR>
					<tr>
						<td class="table_cell"><strong>���� ÷��</strong></td>
						<td class="td_con1">
							<input type=file name="up_banner_file" style="WIDTH: 400px" class="input">
							<span class="font_orange">(�����̹��� : 200X85)</span><br />
						</td>
					</tr>
					<TR>
						<TD colspan="2" background="images/table_con_line.gif"></TD>
					</TR>
					<tr>
						<td class="table_cell"><strong>��ũ URL</strong></td>
						<td class="td_con1">http:// <input type=text name=up_banner_url maxlength=100 value="<?=$up_banner_url?>" class="input" style=width:80%;></td>
					</tr>
					<TR>
						<TD colspan="2" background="images/table_con_line.gif"></TD>
					</TR>
					<tr>
						<td class="table_cell"><strong>��ũ ���</strong></td>
						<td class="td_con1">
							<input type=radio id="up_move_type1" name=up_move_type value="0" <? if(($up_move_type=="0" || strlen($up_move_type)==0)) echo " checked"?>> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=up_move_type1>����â���� ����</label>
							<input type=radio id="up_move_type2" name=up_move_type value="1" <? if($up_move_type=="1") echo " checked"?>> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=up_move_type2>��â���� ����</label>
						</td>
					</tr>
					<TR>
						<TD colspan="2" background="images/table_con_line.gif"></TD>
					</TR>
				</table>

			</TD>
		</TR>

		<TR>
			<TD colspan="2" background="images/table_con_line.gif"></TD>
		</TR>
	<? } ?>
	<? //���������� ��� ���� jdy ?>





	<? // ���� ���� ���� ���� ���� �߰�
	if(false !== $naverep = checkNaverEp()){
		if(!_empty($naverep['shopping'])){ ?>
		<TR>
			<TD class="table_cell">
				<img src="images/icon_point2.gif" width="8" height="11" border="0">���̹� ���ļ��� ����
			</TD>
			<TD class="td_con1">
			<input type="checkbox" name="syncNaverEp" value="0" <?=(($syncNaverEp=='0')?'checked':'')?> /> ���̹� ���� ���� ������ �ش� ī�װ��� ���� �մϴ�.<br /><span style="color:red">���� ���� ī�װ��� ��ǰ ��ü�� ���¸� ���� �ϰ� �˴ϴ�.</span>
			</TD>
		</TR>
		<TR>
			<TD colspan="2" background="images/table_con_line.gif"></TD>
		</TR>
	<? }
	}
	?>




	<TR>
		<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">���ٰ��� ȸ�����</TD>
		<TD class="td_con1">
			<select name=up_group_code style="width:100%" <?if($group_code=="NO") echo "disabled";?> class="select" onChange="javascript:chGroupCode(this.options[this.selectedIndex].value)">
<?
		$gcode_array = array("","ALL");
		$gname_array = array("����� ���ٰ���","���θ� ȸ���� ���ٰ���");
		$num=2;
		if($group_code!="") $group_code1 = "ALL";
		else $group_code1 = "";
		for($i=0;$i<$num;$i++){
			echo "<option value=\"".$gcode_array[$i]."\"";
			if($group_code1==$gcode_array[$i]) echo " selected";
			echo ">".$gname_array[$i]."</option>\n";
		}
?>
			</select>

			<span id=gcode_layer <?=($group_code)?"":"style=\"display:none;\"";?>>
<?
		//���� ����ȸ�� ������ ���õǰ� ����
		$gcode_array = array();
		$gname_array = array();
		$sql = "SELECT group_code,group_name FROM tblmembergroup ";
		$result = mysql_query($sql,get_db_conn());
		$num=0;
		while($row = mysql_fetch_object($result)){
			$gcode_array[$num]=$row->group_code;
			$gname_array[$num++]=$row->group_name;
		}
		mysql_free_result($result);
		for($i=0;$i<$num;$i++){
			echo "<input type=\"checkbox\" name=\"arr_group_code[]\" id=\"up_group_code2\" value=\"".$gcode_array[$i]."\"";
			if(strpos($group_code,$gcode_array[$i])!==false) echo " checked";
			if( $group_code1 == "" ) echo " checked";
			echo ">".$gname_array[$i]."\n";
		}
?>
			</span>
		</TD>
	</TR>
	<TR>
		<TD colspan="2" background="images/table_con_line.gif"></TD>
	</TR>
	<tr>
		<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">��ǰ����</TD>
		<TD class="td_con1"><select name=up_sort style="width:100%" class="select">
			<option <? if ($sort=="date" OR $sort=="" ) echo "selected "; ?> value="date">��ǰ ���/������¥ ����</option>
			<option <? if ($sort=="date2") echo "selected "; ?> value="date2">��ǰ ���/������¥ ���� + ǰ����ǰ �ڷ�</option>
			<option <? if ($sort=="productname") echo "selected "; ?> value="productname">��ǰ�� ������ ����</option>
			<option <? if ($sort=="production") echo "selected "; ?> value="production">������ ������ ����</option>
			<option <? if ($sort=="price") echo "selected "; ?> value="price">��ǰ �ǸŰ��� ����</option>
			</select>
		</TD>
	</tr>
	<TR>
		<TD colspan="2" background="images/table_con_line.gif"></TD>
	</TR>
	<tr>
		<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">ī�װ� ��ǰ����</TD>
		<TD class="td_con1"><input type=checkbox id="idx_special1" name=tmp_special value="1" <?if($special["1"]=="Y")echo"checked";?> style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none"> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_special1>�űԻ�ǰ</label>
		- <FONT COLOR="red">����Ÿ�� ���� :</FONT>
		<select name=up_special_1_type class="select">
			<option value="I" <?if($special_1_type=="I")echo"selected";?>>�̹��� A��</option>
			<option value="D" <?if($special_1_type=="D")echo"selected";?>>�̹��� B��</option>
			<option value="L" <?if($special_1_type=="L")echo"selected";?>>����Ʈ��</option>
		</select>
		<br><img width=0 height=2><br>
		<img width=87 height=0><FONT COLOR="red">���κ� ��ǰ�� :</FONT>
		<select name=up_special_1_cols class="select">
			<option value="1" <?if($special_1_cols==1)echo"selected";?>>1</option>
			<option value="2" <?if($special_1_cols==2)echo"selected";?>>2</option>
			<option value="3" <?if($special_1_cols==3)echo"selected";?>>3</option>
			<option value="4" <?if($special_1_cols==4)echo"selected";?>>4</option>
			<option value="5" <?if($special_1_cols==5)echo"selected";?>>5</option>
			<option value="6" <?if($special_1_cols==6)echo"selected";?>>6</option>
			<option value="7" <?if($special_1_cols==7)echo"selected";?>>7</option>
			<option value="8" <?if($special_1_cols==8)echo"selected";?>>8</option>
		</select>&nbsp;
		<FONT COLOR="red">�ټ� :</FONT>
		<select name=up_special_1_rows class="select">
			<option value="1" <?if($special_1_rows==1)echo"selected";?>>1</option>
			<option value="2" <?if($special_1_rows==2)echo"selected";?>>2</option>
			<option value="3" <?if($special_1_rows==3)echo"selected";?>>3</option>
			<option value="4" <?if($special_1_rows==4)echo"selected";?>>4</option>
			<option value="5" <?if($special_1_rows==5)echo"selected";?>>5</option>
		</select>
		<br><img width=0 height=7><br>
		<input type=checkbox id="idx_special0" name=tmp_special value="2" <?if($special["2"]=="Y")echo"checked";?> style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none"> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_special0>�α��ǰ</label>
		- <FONT COLOR="red">����Ÿ�� ���� :</FONT>
		<select name=up_special_2_type class="select">
			<option value="I" <?if($special_2_type=="I")echo"selected";?>>�̹��� A��</option>
			<option value="D" <?if($special_2_type=="D")echo"selected";?>>�̹��� B��</option>
			<option value="L" <?if($special_2_type=="L")echo"selected";?>>����Ʈ��</option>
		</select>
		<br><img width=0 height=2><br>
		<img width=87 height=0><FONT COLOR="red">���κ� ��ǰ�� :</FONT>
		<select name=up_special_2_cols class="select">
			<option value="1" <?if($special_2_cols==1)echo"selected";?>>1</option>
			<option value="2" <?if($special_2_cols==2)echo"selected";?>>2</option>
			<option value="3" <?if($special_2_cols==3)echo"selected";?>>3</option>
			<option value="4" <?if($special_2_cols==4)echo"selected";?>>4</option>
			<option value="5" <?if($special_2_cols==5)echo"selected";?>>5</option>
			<option value="6" <?if($special_2_cols==6)echo"selected";?>>6</option>
			<option value="7" <?if($special_2_cols==7)echo"selected";?>>7</option>
			<option value="8" <?if($special_2_cols==8)echo"selected";?>>8</option>
		</select>&nbsp;
		<FONT COLOR="red">�ټ� :</FONT>
		<select name=up_special_2_rows class="select">
			<option value="1" <?if($special_2_rows==1)echo"selected";?>>1</option>
			<option value="2" <?if($special_2_rows==2)echo"selected";?>>2</option>
			<option value="3" <?if($special_2_rows==3)echo"selected";?>>3</option>
			<option value="4" <?if($special_2_rows==4)echo"selected";?>>4</option>
			<option value="5" <?if($special_2_rows==5)echo"selected";?>>5</option>
		</select>
		<br><img width=0 height=2><br>
		<input type=checkbox id="idx_special2" name=tmp_special value="3" <?if($special["3"]=="Y")echo"checked";?> style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none"> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_special2>��õ��ǰ</label>
		- <FONT COLOR="red">����Ÿ�� ���� :</FONT>
		<select name=up_special_3_type class="select">
			<option value="I" <?if($special_3_type=="I")echo"selected";?>>�̹��� A��</option>
			<option value="D" <?if($special_3_type=="D")echo"selected";?>>�̹��� B��</option>
			<option value="L" <?if($special_3_type=="L")echo"selected";?>>����Ʈ��</option>
		</select>
		<br><img width=0 height=2><br>
		<img width=87 height=0><FONT COLOR="red">���κ� ��ǰ�� :</FONT>
		<select name=up_special_3_cols class="select">
			<option value="1" <?if($special_3_cols==1)echo"selected";?>>1</option>
			<option value="2" <?if($special_3_cols==2)echo"selected";?>>2</option>
			<option value="3" <?if($special_3_cols==3)echo"selected";?>>3</option>
			<option value="4" <?if($special_3_cols==4)echo"selected";?>>4</option>
			<option value="5" <?if($special_3_cols==5)echo"selected";?>>5</option>
			<option value="6" <?if($special_3_cols==6)echo"selected";?>>6</option>
			<option value="7" <?if($special_3_cols==7)echo"selected";?>>7</option>
			<option value="8" <?if($special_3_cols==8)echo"selected";?>>8</option>
		</select>&nbsp;
		<FONT COLOR="red">�ټ� :</FONT>
		<select name=up_special_3_rows class="select">
			<option value="1" <?if($special_3_rows==1)echo"selected";?>>1</option>
			<option value="2" <?if($special_3_rows==2)echo"selected";?>>2</option>
			<option value="3" <?if($special_3_rows==3)echo"selected";?>>3</option>
			<option value="4" <?if($special_3_rows==4)echo"selected";?>>4</option>
			<option value="5" <?if($special_3_rows==5)echo"selected";?>>5</option>
		</select>
		<br>
		<input type=checkbox id="idx_islist" name=up_islist value="Y" <?if($islist=="Y")echo"checked";?> style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none"> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_islist>ī�װ���ǰ���</label><br>
		<span class="font_orange" style="letter-spacing:-0.5pt;FONT-SIZE:11px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;* ������ : <a href="shop_mainproduct.php" target="_parent"><span class="font_blue" style="letter-spacing:-0.5pt;FONT-SIZE:11px;">�������� > ���θ� ȯ�� ���� > ��ǰ ������/ȭ�鼳��</span></a>.</span>
		</TD>
	</tr>
	<TR>
		<TD colspan="2" background="images/table_con_line.gif"></TD>
	</TR>
	<tr>
		<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">ī�װ� ���迩��</TD>
		<TD class="td_con1"><input type=checkbox id="idx_code_hide1" name=up_code_hide value="NO" <? if($group_code=="NO") echo "checked";?> onclick="GroupCheck(this.checked)"> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_code_hide1>�� ��ǰī�װ�(ī�װ�) �����</label></TD>
	</tr>
	<TR>
		<TD colspan="2" background="images/table_con_line.gif"></TD>
	</TR>

	<tr>
		<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">ī�װ� �ɼ�</TD>
		<TD class="td_con1">

			����ǰ <select name="up_isgift" style="margin-right:10px;"><option value="">---</option><option value="N" <? if($isgift=="N") echo "selected";?>>����Ұ�</option><option value="Y" <? if($isgift=="Y") echo "selected";?>>���밡��</option></select>
			���� <select name="up_iscoupon" style="margin-right:10px;"><option value="">---</option><option value="N" <? if($iscoupon=="N") echo "selected";?>>����Ұ�</option><option value="Y" <? if($iscoupon=="Y") echo "selected";?>>���밡��</option></select>
			��ȯ �� ȯ�� <select name="up_isrefund" style="margin-right:10px;"><option value="">---</option><option value="N" <? if($isrefund=="N") echo "selected";?>>�Ұ�</option><option value="Y" <? if($isrefund=="Y") echo "selected";?>>����</option></select>
			������ <select name="up_reserve" style="margin-right:10px;"><option value="">---</option><option value="N" <? if($isreserve=="N") echo "selected";?>>���Ұ�</option><option value="Y" <? if($isreserve=="Y") echo "selected";?>>��밡��</option></select>
			<? /*
			����ǰ <select name="up_isgift" style="margin-right:10px;"><option value="">---</option><option value="Y">����Ұ�</option><option value="N" >���밡��</option></select>
			���� <select name="up_iscoupon" style="margin-right:10px;"><option value="">---</option><option value="Y">����Ұ�</option><option value="N">���밡��</option></select>
			��ȯ �� ȯ�� <select name="up_isrefund" style="margin-right:10px;"><option value="">---</option><option value="Y">�Ұ�</option><option value="N" >����</option></select>
			������ <select name="up_isreserve" style="margin-right:10px;"><option value="">---</option><option value="Y">���Ұ�</option><option value="N">��밡��</option></select>
			*/ ?>
		</TD>
	</tr>
	<TR>
		<TD colspan="2" background="images/table_con_line.gif"></TD>
	</TR>
<?
if ($type1!="S"){
?>
	<tr>
		<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">��ǰ���� ���ø� ����</TD>
		<TD class="td_con1">
		<?
		if(strlen(str_replace('0','',substr(trim($parentcode),0,3))) >= 1 || strlen(str_replace('0','',substr(trim($code),0,6))) >=2){ ?>
		<div style="padding:3px; background:#efefef">
		<input type="checkbox" name="dsameparent" value="1" <? if($dsameparent == 1){ ?> checked="checked" <? } ?> onclick="checkDesignSame(this)" /> �θ� �����ΰ� ����<?=$dsameparent?>
		</div>
		<? } ?>
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td width="397"><input type=radio id="idx_gong1" name=gong value="N" <? if(($gong=="N" || strlen($gong)==0) && $dsameparent != '1') echo " checked"?> onclick="DesignMsg(0)"> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_gong1>��ǰ ���� �� ��ǰ�� ������(�Ϲ���)</label></td>
		</tr>
		<tr>
			<td width="100%" style="padding-left:13pt;">
			<table cellpadding="0" cellspacing="0" width="97%">
			<col width=50%></col>
			<col width=50%></col>
			<tr>
				<td align=center><a href="javascript:DesignList(0);"><img src="images/product_displaylist1.gif" width="158" height="16" border="0"></a></td>
				<td align=center><a href="javascript:DesignDetail(0);"><img src="images/product_displaydetail1.gif" width="158" height="16" border="0"></a></td>
			</tr>
			<?if($gong == "N" && $list_type!="" && $detail_type!="") {?>
			<tr>
				<td align=center valign="top" style="padding-top:3pt;"><a href="javascript:DesignList(0);"><img src="images/product/<?=$list_type?>.gif" width="150" height="160" border="0" style="border-width:1pt; border-color:rgb(222,222,222); border-style:solid;"></a></td>
				<td align=center valign="top" style="padding-top:3pt;"><a href="javascript:DesignDetail(0);"><img src="images/product/<?=$detail_type?>.gif" width="150" height="160" border="0" style="border-width:1pt; border-color:rgb(222,222,222); border-style:solid;"></a></td>
			</tr>
			<? } else { ?>
			<tr>
				<td align=center valign="top" style="padding-top:3pt;"><a href="javascript:DesignList(0);"><img src="images/ex1.gif" width="150" height="160" border="0" style="border-width:1pt; border-color:rgb(222,222,222); border-style:solid;"></a></td>
				<td align=center valign="top" style="padding-top:3pt;"><a href="javascript:DesignDetail(0);"><img src="images/ex2.gif" width="150" height="160" border="0" style="border-width:1pt; border-color:rgb(222,222,222); border-style:solid;"></a></td>
			</tr>
			<? } ?>
			</table>
			</td>
		</tr>
		<tr>
			<td height=10></td>
		</tr>
		<tr>
			<td width="100%"><input type=radio id="idx_gong2" name=gong value="Y" <? if($gong=="Y" && $dsameparent != '1') echo " checked"?> onclick="DesignMsg(1)"> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_gong2>���ݰ����� �������� ������(������)</label></td>
		</tr>
		<tr>
			<td width="100%" style="padding-left:13pt;">
			<table cellpadding="0" cellspacing="0" width="97%">
			<col width=50%></col>
			<col width=50%></col>
			<tr>
				<td align=center><a href="javascript:DesignList(1);"><img src="images/product_displaylist2.gif" width="158" height="16" border="0"></a></td>
				<td align=center><a href="javascript:DesignDetail(1);"><img src="images/product_displaydetail2.gif" width="158" height="16" border="0"></a></td>
			</tr>
			<?if($gong == "Y" && $list_type!="" && $detail_type!="") {?>
			<tr>
				<td align=center valign="top" style="padding-top:3pt;"><a href="javascript:DesignList(1);"><img src="images/product/<?=$list_type?>.gif" width="150" height="160" border="0" style="border-width:1pt; border-color:rgb(222,222,222); border-style:solid;"></a></td>
				<td align=center valign="top" style="padding-top:3pt;"><a href="javascript:DesignDetail(1);"><img src="images/product/<?=$detail_type?>.gif" width="150" height="160" border="0" style="border-width:1pt; border-color:rgb(222,222,222); border-style:solid;"></a></td>
			</tr>
			<? } else { ?>
			<tr>
				<td align=center valign="top" style="padding-top:3pt;"><a href="javascript:DesignList(1);"><img src="images/ex3.gif" width="150" height="160" border="0" style="border-width:1pt; border-color:rgb(222,222,222); border-style:solid;"></a></td>
				<td align=center valign="top" style="padding-top:3pt;"><a href="javascript:DesignDetail(1);"><img src="images/ex4.gif" width="150" height="160" border="0" style="border-width:1pt; border-color:rgb(222,222,222); border-style:solid;"></a></td>
			</tr>
			<? } ?>
			</table>
			</td>
		</tr>
		</table>
		</TD>
	</tr>
<? }?>
	<? if($mode=="modify"){?>
	<tr>
		<TD align="center" colspan="2">
		<div id=child_layer style="position:absolute;z-index:100;left:0;bottom:45;width:270px;visibility:hidden;">
		<table border=0 cellspacing=1 cellpadding=0 width=270 bgcolor=#000000>
		<tr>
			<td bgcolor=#FFFFFF>
			<table border=0 cellpadding=3 width=100%>
			<col width=50%></col>
			<col width=50%></col>
			<tr>
				<td valign="top"><input type=checkbox id="idx_isgcode" name="is_gcode" value="1" style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none"> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_isgcode>���ٰ��� ȸ�����</label><br><input type=checkbox id="idx_issort" name="is_sort" value="1" style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none"> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_issort>��ǰ����</label></td>
				<td valign="top"><input type=checkbox id="idx_isdesign" name="is_design" value="1" style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none"> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_isdesign>��ǰ���� ������</label><br><input type=checkbox id="idx_isspecial" name="is_special" value="1" style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none"> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_isspecial>ī�װ� ������ǰ</label></td>
			</tr>
			</table>
			</td>
		</tr>
		</table>
		</div>
		</TD>
	</tr>
	<?}?>
	<TR>
		<TD colspan="2" background="images/table_con_line.gif"></TD>
	</TR>
	<tr>
		<td colspan="2" height="10"></td>
	</tr>
	<?if($mode=="insert"){?>
	<TR>
		<TD colspan="2" align="center"><a href="javascript:Save()"><img src="images/botteon_add.gif" width="137" height="38" border="0" hspace="0"></a></TD>
	</TR>
	<?}else if($mode=="modify"){?>
	<TR>
		<TD colspan="2" align="center">
		<?if(!ereg("X",$type)){?>
		<a href="javascript:ChildCodeClick();"><img src="images/botteon_downallapply.gif" width="118" height="38" border="0" hspace="0"></a>&nbsp;
		<?}?>
		<a href="javascript:parent.NewCode();"><img src="images/botteon_newadd.gif" width="118" height="38" border="0" hspace="0"></a>&nbsp;
		<a href="javascript:Save();"><img src="images/botteon_catemodify.gif" width="118" height="38" border="0" hspace="0"></a>&nbsp;
		<a href="javascript:CodeDelete();"><img src="images/botteon_catedelete.gif" width="118" height="38" border="0" hspace="0"></a>&nbsp;
		</TD>
	</TR>
	<?}?>
	<tr>
		<td colspan="2" height="10"></td>
	</tr>
	</TABLE>
	</td>
</tr>
</form>
<form name=form2 action="<?=$_SERVER[PHP_SELF]?>" method=post>
<input type=hidden name=mode>
<input type=hidden name=code>
</form>
</table>
<?=$onload?>
<script type="text/javascript">
<!--
	parent.autoResize('PropertyFrame');
//-->
</script>
</body>
</html>