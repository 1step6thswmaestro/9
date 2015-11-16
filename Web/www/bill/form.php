<?php
	include 'cfg.php';//기본 include파일   
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="EUC-KR" xml:lang="EUC-KR" xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
<title>매입 세금계산서</title>
<style>
table {font-size:12px;}
input,textarea {font-size:12px; font-family: "gulim";border:solid 1px #D7D7D7;}
input.right {text-align:right;}

.hidden_line {border:dotted 1px #D7D7D7;}
.show_line {border:solid 1px #D7D7D7;}
.background {background-color:#e7e7e7;text-align:right;}
</style>
<script>
    function chkLogin(f)
    {
        if (!f.domain.value) {
            alert('하이웍스 개설 그룹주소를 입력해 주세요');
            f.domain.focus();
            return false;
        }

        if (!f.license_id.value) {
            alert('사용자 아이디를 입력해 주세요');
            f.license_id.focus();
            return false;
        }

        if (!f.license_no.value) {
            alert('세금계산서 연동정보를 입력해 주세요');
            f.license_no.focus();
            return false;
        }

        return true;
    }

    function init()
    {
        document.getElementById('domain').focus();
    }

</script>
</head>
<body onload="init();">
<form method="post" name="frmLogin" action="http://billapi.hiworks.co.kr/auto_login.php" onsubmit="return chkLogin(this)">
<table width="400" cellpadding="0" cellspacing="1" bgcolor="#c8c8c8">
<tr bgcolor="#ffffff" height="30">
<td width="40%" align="center">하이웍스 개설 그룹주소</td>
<td>&nbsp;<input type="text" name="domain" id="domain" value="<?=$cfg['domain']?>"></td>
</tr>
<tr bgcolor="#ffffff" height="30">
<td align="center">사용자 아이디</td>
<td>&nbsp;<input type="text" name="license_id" value="<?=$cfg['license_id']?>"></td>
</tr>
<tr bgcolor="#ffffff" height="30">
<td align="center">세금계산서 연동정보</td>
<td>&nbsp;<input type="text" name="license_no" value="<?=$cfg['license_no']?>"></td>
</tr>
<tr bgcolor="#ffffff" height="30">
<td align="center">로그인 방법</td>
<td>&nbsp;<input type="radio" name="pType" id="pType_BILL" value="BILL" checked="checked"><label for="pType_BILL">세금계산서</label>
<input type="radio" name="pType" id="pType_SMS" value="SMS"><label for="pType_SMS">문자충전</label>
</td>
</tr>

<tr bgcolor="#ffffff" height="30">
<td colspan="2" align="center"><input type="submit" value="로그인" style="width:100px"></td>
</tr>
</table>
</form>
</body>
</html>