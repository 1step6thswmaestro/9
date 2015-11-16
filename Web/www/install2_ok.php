<?
$Dir="./";
include ($Dir."lib/init.php");
$install_state=true;
include ($Dir."lib/lib.php");

if(!file_exists($Dir.DataDir."config.php")) {
	echo "<html></head><body onload=\"location.replace('install.php')\"></body></html>";exit;
}

if(fileperms($Dir.DataDir)!=16839 && fileperms($Dir.DataDir)!=16895) {
	error_msg("<font color=red>현재 707로 퍼미션이 되어 있지 않습니다.</font><br><font color=red><B>(/".RootPath.DataDir.")</B> 텔넷이나 FTP에서 퍼미션을 조정하세요.<font>");
}

if(fileperms($Dir.CashcgiDir."bin")!=16839 && fileperms($Dir.CashcgiDir."bin")!=16895) {
	error_msg("<font color=red>현재 707로 퍼미션이 되어 있지 않습니다.</font><br><font color=red><B>(/".RootPath.CashcgiDir."bin/)</B> 텔넷이나 FTP에서 퍼미션을 조정하세요.<font>");
}

$admin_passwd=$_POST["admin_passwd"];
$shop_name=$_POST["shop_name"];

if(is_blank($admin_passwd))	error_msg("관리자 패스워드를 입력하세요");
if(is_blank($shop_name))	error_msg("쇼핑몰 이름을 입력하세요");

//최종 data insert
$sql = "SELECT COUNT(*) as cnt FROM tblshopinfo ";
$result=mysql_query($sql,get_db_conn());
$row=mysql_fetch_object($result);
mysql_free_result($result);
if($row->cnt<=0) {
	$sql = "INSERT INTO tblshopinfo (shopname,regdate,shop_intro) VALUES ('".$shop_name."','".date("YmdHis")."','<style type=\"text/css\">
A:link    {color:#AAAAAA;text-decoration:none;}
A:visited {color:#AAAAAA;text-decoration:none;}
A:hover  {color:#AAAAAA;text-decoration:none;}
</style>
<table cellpadding=\"0\" cellspacing=\"0\">
	<tr>
		<td>
		<script type=\"text/javascript\">
		var conWidth = 780; //배너이미지 가로크기
		var conHeight = 217; //배너이미지 세로크기
		var aniTimer = 1000; // 움직이는 속도
		var slideTimer = 3000; // 슬라이드 속도
		</script>
		<style type=\"text/css\">
		.slideBanner img{border:0 none}
		.slideBanner ul,li{list-style:none; padding:0; margin:0}		
		.slideBanner .con_list{display:block; height:217px; overflow:hidden}
		.slideBanner .con_list ul{position:relative}
		.slideBanner .con_list ul img{vertical-align:top}
		.slideBanner .tab_list{*zoom:1}
		.slideBanner .tab_list:after{content:\"\"; display:block; clear:both}
		.slideBanner .tab_list li{float:left; text-align:center; background:#FFFFFF;}
		.slideBanner .tab_list li a{display:block; padding:5px 0}
		.slideBanner .tab_list li a img{vertical-align:top}
		.slideBanner .tab_list li.active{background:#ECECEC}
		.slideBanner .tab_list li.active a{color:#999999}
		</style>
		<script type=\"text/javascript\" src=\"../upload/js/jquery-1.7.1.min.js\"></script>
		<script type=\"text/javascript\" src=\"../upload/js/jquery.easing.1.3.js\"></script>
		<script type=\"text/javascript\" src=\"../upload/js/mainSlide.js\"></script>
		<div class=\"slideBanner\">
			<div class=\"con_list\">
				<ul>
					<li><a href=\"#\"><img src=\"../upload/img/main/img1.gif\" /></a></li>
					<li><a href=\"#\"><img src=\"../upload/img/main/img2.gif\" /></a></li>
					<li><a href=\"#\"><img src=\"../upload/img/main/img3.gif\" /></a></li>
					<li><a href=\"#\"><img src=\"../upload/img/main/img4.gif\" /></a></li>
				</ul>
			</div>
			<ul class=\"tab_list\">
				<li><a href=\"#\">첫번째 배너</a></li>
				<li><a href=\"#\">두번째 배너</a></li>
				<li><a href=\"#\">세번째 배너</a></li>
				<li><a href=\"#\">네번째 배너</a></li>
			</ul>
		</div>
		</td>
	</tr>
          <tr>
                     <td height=\"20\"><td>
          </tr>
</table>') ";
	mysql_query($sql,get_db_conn());
}

$sql = "SELECT COUNT(*) as cnt FROM tblshopcount ";
$result=mysql_query($sql,get_db_conn());
$row=mysql_fetch_object($result);
mysql_free_result($result);
if($row->cnt<=0) {
	$sql = "INSERT INTO tblshopcount VALUES ('0','0') ";
	mysql_query($sql,get_db_conn());
}

$sql = "SELECT COUNT(*) as cnt FROM tblboardadmin WHERE board='qna' ";
$result=mysql_query($sql,get_db_conn());
$row=mysql_fetch_object($result);
mysql_free_result($result);
if($row->cnt<=0) {
	/*$sql = "INSERT INTO tblboardadmin (board,board_name,passwd) VALUES ('qna','상품Q&A','".$admin_passwd."') ";
	mysql_query($sql,get_db_conn());

	############# qna 게시판 디렉토리 생성 ################
	include($Dir.BoardDir."file.inc.php");
	ProcessBoardDir("qna","create");*/
}

$sql = "SELECT COUNT(*) as cnt FROM tblsecurityadmin WHERE id='admin' ";
$result=mysql_query($sql,get_db_conn());
$row=mysql_fetch_object($result);
mysql_free_result($result);
if($row->cnt<=0) {
	$sql = "INSERT INTO tblsecurityadmin VALUES ('admin','".md5($admin_passwd)."','1','운영자','','','0','0','0','','0') ";
	mysql_query($sql,get_db_conn());
}

$sql = "SELECT COUNT(*) as cnt FROM tblsecurityadminip WHERE idx=1 ";
$result=mysql_query($sql,get_db_conn());
$row=mysql_fetch_object($result);
mysql_free_result($result);
if($row->cnt<=0) {
	$sql = "INSERT INTO tblsecurityadminip VALUES (1,'admin','0') ";
	mysql_query($sql,get_db_conn());
}

$sql = "SELECT COUNT(*) as cnt FROM tblsecurityrole WHERE idx=1 ";
$result=mysql_query($sql,get_db_conn());
$row=mysql_fetch_object($result);
mysql_free_result($result);
if($row->cnt<=0) {
	$sql = "INSERT INTO tblsecurityrole VALUES (1,'Administrator',0) ";
	mysql_query($sql,get_db_conn());
}

$sql = "SELECT COUNT(*) as cnt FROM tblsecurityroletask WHERE idx=1 ";
$result=mysql_query($sql,get_db_conn());
$row=mysql_fetch_object($result);
mysql_free_result($result);
if($row->cnt<=0) {
	$sql = "INSERT INTO tblsecurityroletask VALUES (1,1,0) ";
	mysql_query($sql,get_db_conn());
}

$sql = "SELECT COUNT(*) as cnt FROM tblsecurityadminrole WHERE idx=1 ";
$result=mysql_query($sql,get_db_conn());
$row=mysql_fetch_object($result);
mysql_free_result($result);
if($row->cnt<=0) {
	$sql = "INSERT INTO tblsecurityadminrole VALUES (1,'admin',1) ";
	mysql_query($sql,get_db_conn());
}


header("location:".$Dir.AdminDir);
?>