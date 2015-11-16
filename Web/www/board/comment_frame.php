<?
if(substr(getenv("SCRIPT_NAME"),-19)=="/comment_frame.php"){
	header("HTTP/1.0 404 Not Found");
	exit;
}

include ("head.php");

$this_num=$_REQUEST["num"];

?>

<HTML>
<HEAD>
<TITLE><?=$_data->shoptitle?> - <?=$setup[board_name]?></TITLE>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META http-equiv="X-UA-Compatible" content="IE=5" />
<META name="description" content="<?=(strlen($_data->shopdescription)>0?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<?include($Dir."lib/style.php")?>
<script>var LH = new LH_create();</script>
<script for=window event=onload>f_onLoad();</script>
<script>LH.add("parent_resizeIframe('list_comment<?=$this_num?>')");</script>
<SCRIPT LANGUAGE="JavaScript">
	<!--
	function zoomImage(img,board) {
		if (img.length==0) {
			alert("확대보기 이미지가 없습니다.");
			return;
		}
		var tmp = "toolbar=no,menubar=no,resizable=no,status=no,scrollbars=yes";
		url = "/board/zoomimage.php?board="+board+"&image="+img;

		window.open(url,"zoomimage",tmp);
	}
	//-->
</script>
<SCRIPT LANGUAGE="JavaScript">
<!--
var isOnload = false;
var setCnt = 0;
function f_onLoad() {
	isOnload = true;
	LH.exec();
}
function chk_time() {
	if (!isOnload && setCnt < 20) {
		setCnt = setCnt + 1;
		LH.exec();
		setTimeout("chk_time()", 500);
	}
}
chk_time();

function chkCommentDel(userId,url) {
	parent.location.href = url;
}
//-->
</SCRIPT>
</HEAD>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<?@include ($dir."/comment_head.php");?>

<?

if ($setup[use_comment] == "Y" && $member[grant_comment]=="Y") {
	if( $secuCmtViewCnt == 0 OR strlen($_ShopInfo->id) > 0 ){
		$cmtFile = ($setup[fileYN] == "Y") ? "<input type=\"file\" name=\"img\" class=\"input\">" : ""; // 파일첨부
		@include ($dir."/comment_write.php");
	}
}



$frametype="Y";
$sql = "SELECT * FROM tblboardcomment WHERE board='".$board."' ";
$sql.= "AND parent = $this_num ORDER BY num ASC ";
$result = @mysql_query($sql,get_db_conn());
while($row=mysql_fetch_object($result)) {
	$c_num = $row->num;
	$c_name = $row->name;

	if($setup[use_comip]=="Y") {
		$c_uip=$row->ip;
	}

	$c_writetime = getTimeFormat($row->writetime);
	$c_comment = nl2br(stripslashes($row->comment));
	$c_ip = $row->ip;
	$c_comment = getStripHide($c_comment);



		unset($comUserId);

		// 비밀댓글
		$secuCmtView = true;
		if( $setup["secuCmt"] == "Y" ){
			$secuCmtView = false;
			if( $_ShopInfo->getMemid() == $row->id OR strlen($_ShopInfo->id) > 0 ){
				$secuCmtView = true;
			}
		}


		// 관리자 댓글의 댓글
		$adminComment = "";
		$adminCommSQL = "SELECT * FROM `tblboardcomment_admin` WHERE `board` = '".$board."' AND `board_no`= '".$this_num."' AND `comm_no`= '".$c_num."' ORDER BY `idx` ASC";
		$adminCommResult = mysql_query( $adminCommSQL );
		$adminCommNums = mysql_num_rows($adminCommResult);

		if($adminCommNums > 0) {
			$adminComment .= "<div style=\" background:#f9f9f9; border:1px solid #f5f5f5; padding:5px 10px 3px 10px;\">";
			while( $adminCommRow = mysql_fetch_assoc ( $adminCommResult ) ) {
				$adminComment .= "
					<p style=\"font-size:11px; padding:3px 0px;\">
						<img src=\"".$imgdir."/icon_reply.gif\" alt=\"\" /> <strong>관리자</strong> : ".$adminCommRow['comment']."
					</p>
				"; //(".$adminCommRow['reg_date'].")
			}
			$adminComment .= "</div>";
		}

		// 파일
		$filesname = DirPath.DataDir."shopimages/board/".$board."/".$row->file;
		$filessize = @getimagesize($filesname);
		$c_comment_file_max_width = $setup[comment_width];
		$c_comment_file_width = ( $c_comment_file_max_width < $filessize[0] ) ? ($c_comment_file_max_width) : $filessize[0];
		$c_comment_file = ( strlen($row->file) > 0 ) ? "<div style='float:left; margin-right:20px;'><a href=\"javascript:zoomImage('".$row->file."','".$board."');\"><img src='".$filesname."' width='".$c_comment_file_width."'></a></div>" : "";


	@include ($dir."/comment_list.php");
}
mysql_free_result($result);


?>
</body>
</html>