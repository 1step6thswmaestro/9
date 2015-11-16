<?
$shopname=$_data->shopname;
function GetHeader() {
	global $_ShopInfo, $shopname;
	$get_result = "
<html xmlns=\"http://www.w3.org/1999/xhtml\">
<head>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=euc-kr\" />
<title>Untitled Document</title>
<style type=\"text/css\">
body, td {font-family:굴림;font-size:12px;color:#666666}
img {margin:0; border:0;}
.table_1 th{border-bottom:1px solid #e4e4e4; border-left:1px solid #e4e4e4; border-top:2px solid #969696; color:#474747; background-color:#f9f9f9;}
.table_1 td{border-bottom:1px solid #e4e4e4; border-left:1px solid #e4e4e4;}
.table_2 {border:2px solid #c4c4c4}
.table_2 th{padding-left:20px; text-align:left; border-bottom:1px dotted #e4e4e4; color:#474747; background-color:#f9f9f9;}
.table_2 td{padding-left:10px; text-align:left; border-bottom:1px dotted #e4e4e4}
table a:link,
table a:active,
table a:visited {color:#8f8f8f; text-decoration:none;}
table a:hover {text-decoration:none;}
</style>
</head>
<body>
   <table width=\"690\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">
	<tr>
	  <td height=\"50\">&nbsp;</td>
	</tr>
	<tr>
	  <td>
		<table width=\"690\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" style=\"margin-bottom:5px;\">
		  <tr>
			<td height=\"35\" valign=\"bottom\" style=\"padding-bottom:5px; padding-left:50px; background-image:url(http://".$_ShopInfo->getShopurl()."images/mail/solution/logo_1.gif); background-position:0 bottom; background-repeat:no-repeat;\"><strong>".$shopname."</strong></td>
			<td align=\"right\" style=\"font-weight:bold; vertical-align:bottom; padding-bottom:5px; padding-right:15px\">".date("Y년 m월 d일")."</td>
		  </tr>
		</table>
	  </td>
	</tr>
	</table>
	";

	return $get_result;
}

function GetFooter() {
	global $_ShopInfo, $shopname;
	$get_result = "
	<table width=\"690\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">
    <tr>
      <td height=\"40\">&nbsp;</td>
    </tr>
    <tr>
      <td>
        <table border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
          <tr>
            <td width=\"20\">&nbsp;</td>
            <td><a href=\"http://".$_ShopInfo->getShopurl()."\" target=\"_blank\"><img src=\"http://".$_ShopInfo->getShopurl()."images/mail/solution/copy_logo.gif\" /></a></td> 
            <td style=\"padding-left:30px; font-size:11px; color:#8f8f8f; line-height:18px;\">본 메일은 정보통신망률 등 관련규정에 의거하여 수신동의하신 회원에게 발송되었습니다. <br />본 메일은 발신전용메일입니다. 메일 수신을 원치 않으시면 <b><a href=\"http://".$_ShopInfo->getShopurl()."front/mypage_usermodify.php\">[수신거부]</a></b> 클릭하십시오. <br />COPYRIGHT (C) <b>".$shopname."</b> ALL RESERVED.
            </td>
          </tr>
        </table>
      </td>
    </tr>
    </tr>
    <tr>
      <td height=\"40\">&nbsp;</td>
    </tr>
  </table>
</body>
</html>
	";

	return $get_result;
}


function GetContent($b_name, $b_email, $b_subject, $b_memo, $b_date, $b_filename, $b_title,$type="",$re_b_mame="") {
	global $_ShopInfo, $board, $view_divider,$view_left_header_color,$view_body_color;
	if ($b_email) {
		$b_name = "<a href='mailto:".$b_email."'>".$b_name."</a>";
	}
	if($b_filename) {
		$b_file="<img src=\"http://".$_ShopInfo->getShopurl().DataDir."shopimages/board/".$board."/".$b_filename."\" border=0><br>";
	}

	$get_result = "
	<table width=\"690\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">
    <tr>
      <td>
        <table width=\"690\" height=\"232\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" style=\"background:url(http://".$_ShopInfo->getShopurl()."images/mail/solution/top_bg.jpg) 0 0 no-repeat;\">
          <tr>
            <td height=\"4\" style=\"padding-top:31px; padding-left:20px\">".(($type=="re")?"<img src=\"http://".$_ShopInfo->getShopurl()."images/mail/solution/top_img_1_4.gif\" />":"<img src=\"http://".$_ShopInfo->getShopurl()."images/mail/solution/top_img_1_3.gif\" />")."</td>
          </tr>
          <tr>
            <td height=\"23\" style=\"padding-left:25px\"><img src=\"http://".$_ShopInfo->getShopurl()."images/mail/solution/top_img_2.gif\" /></td>
          </tr>
          <tr>
            <td valign=\"top\" style=\"padding-left:20px; padding-top:30px; line-height:18px;\"><span style=\"color:#40a2c7;font-weight:bold;\">".(($type=="re")? $re_b_mame."</span> 고객님! 안녕하세요?<br />
            고객님이 작성하신 글에 아래와 같이 답변이 등록되었습니다.<br />":$b_name."님께서 ".$b_title."에 게시글을 등록하셨습니다.")."</td>
          </tr>
        </table>
      </td>
    </tr>
    <tr>
      <td height=\"20\">&nbsp;</td>
    </tr>
    <tr>
      <td>
        <table width=\"688\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" style=\"border-bottom:1px solid #ebebeb; border-left:1px solid #ebebeb; border-right:1px solid #ebebeb;\">
          <tr>
            <td width=\"690\" height=\"50\" bgcolor=\"#5f5f5f\">
              <table height=\"21\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
                  <tr>
                    <td style=\"color:#fff; background-image:url(http://".$_ShopInfo->getShopurl()."images/mail/solution/icon_1.gif); background-position:10px 45%; background-repeat:no-repeat; padding-left:20px;\">작성일자 : ".substr($b_date,0, 10)."</td>
                    <td width=\"5\">&nbsp;</td>
                    <td style=\"color:#fff; background-image:url(http://".$_ShopInfo->getShopurl()."images/mail/solution/icon_1.gif); background-position:10px 45%; background-repeat:no-repeat; padding-left:20px;\">".$b_title."</td>
                  </tr>
              </table>
            </td>
          </tr>
          <tr>
            <td height=\"31\">&nbsp;</td>
          </tr>
          <tr>
            <td align=\"center\" valign=\"top\">
              <table width=\"656\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
                <tr>
                  <td align=\"left\" style=\"padding-bottom:7px;\"><strong style=\"padding-left:4px; color:#333333;\">내용</strong></td>
                </tr>
                <tr>
                  <td>
                    <table width=\"656\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
                      <tr>
                        <td align=\"left\" style=\"padding:10px 0 30px 10px; border-bottom:1px solid #e4e4e4; border-top:2px solid #969696; color:#474747;\">".$b_file.$b_memo."
						</td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr>
            <td height=\"15\">&nbsp;</td>
          </tr>
          <tr>
            <td align=\"center\"><a href=\"http://".$_ShopInfo->getShopurl()."\" target=\"_blank\"><img src=\"http://".$_ShopInfo->getShopurl()."images/mail/solution/btn_1.gif\" /></a></td>
          </tr>
          <tr>
            <td height=\"45\">&nbsp;</td>
          </tr>
        </table> 
      </td>
    </tr>
	</table>
	";

	return $get_result;
}

?>