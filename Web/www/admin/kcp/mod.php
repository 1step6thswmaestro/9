<?
/*
error_reporting(E_ALL);
ini_set("display_errors", 1);
*/
$Dir="../../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

$_ShopInfo->getPgdata();
INCLUDE ("../access.php");


####################### 페이지 접근권한 check ###############
$PageCode = "pr-1";
$MenuCode = "nomenu";


if(!$_usersession->isAllowedTask($PageCode)) {	
	INCLUDE ("../AccessDeny.inc.php");
	exit;
}

//if(KCPMOD_REAL){	
	if(empty($_REQUEST['ordercode'])) exit('주문 번호 전달 오류');
	$sql = "select * from tblorderinfo where ordercode='".$_REQUEST['ordercode']."' limit 1";
	if(false === $res = mysql_query($sql,get_db_conn())) exit(mysql_error());
	if(mysql_num_rows($res) < 1) exit('주문 정보를 확인 할수 없습니다.');
	$oinfo = mysql_fetch_assoc($res);
	if($oinfo['pay_admin_proc'] == 'C') exit('취소 처리된 주문 입니다.');
	if($oinfo['price'] < 1) exit('결제 금액이 올바르지 않습니다.');
	
	if(!in_array(substr($oinfo['paymethod'],0,1),array('C','V')) || substr($oinfo['paymethod'],1,1) != 'A') exit('결제 방식이 올바르지 않습니다.');
	if($oinfo['pay_flag'] != '0000') exit('결제 성공 주문이 아닙니다.');
	
	if(substr($oinfo['paymethod'],0,1 == 'V')){
		$sql = "select * from tblptranslog where ordercode='".$_REQUEST['ordercode']."' limit 1";
	}else{
		$sql = "select * from tblpcardlog where ordercode='".$_REQUEST['ordercode']."' limit 1";
	}
	
	if(false === $res = mysql_query($sql,get_db_conn())) exit(mysql_error());
	if(mysql_num_rows($res) < 1) exit('주문 정보를 확인 할수 없습니다.');
	$tmp = mysql_fetch_assoc($res);
	$oinfo['tno'] = $tmp['trans_code'];
	
//}

require_once 'config.php';

// 2013110410200997219A


function _pr($val){
	echo '<pre>';
	print_r($val);
	echo '</pre>';
}
?>

<!--
    /* ============================================================================== */
    /* =   PAGE : 취소 요청 PAGE                                                    = */
    /* = -------------------------------------------------------------------------- = */
    /* =   Copyright (c)  2008   KCP Inc.   All Rights Reserverd.                   = */
    /* ============================================================================== */
//-->
<html>
<head>
<title>*** KCP Online Payment System [PHP Version] ***</title>
<link href="css/sample.css" rel="stylesheet" type="text/css">
<script language='javascript'>
function  jsf__go_mod( form )
{
    if ( form.mod_type.value != 'mod_type_not_sel' )
    {
        if ( form.tno.value.length < 14 )
        {
            alert( "KCP 거래 번호를 입력하세요" );
            form.tno.focus();
            form.tno.select();
        }
        else
        {
			
			if(confirm('부분 취소를 진행 하시겠습니까?')){
	            form.submit();
			}
        }
    }
    else
    {
        alert( "거래 구분을 선택하여 주십시요." );
        form.mod_type.focus();
    }
}

function  jsf__chk_mod_type()
{
    if ( document.mod_form.mod_type.value == 'RN07' )
    {
        window.show_mod_mny[0].style.display = 'inline';
        window.show_mod_mny[1].style.display = 'inline';
    }
    else
    {
     //   window.show_mod_mny[0].style.display = 'none';
   //     window.show_mod_mny[1].style.display = 'none';
    }
}
</script>
<body onLoad="jsf__chk_mod_type()">

<form name="mod_form" action="pp_cli_hub.php" method="post">

<input type='hidden' name='req_tx' value='mod'>
<input type="hidden" name="ordercode" value="<?=$oinfo['ordercode']?>">
<input type="hidden" name="tempkey" value="<?=$oinfo['tempkey']?>">
<table border='0' cellpadding='0' cellspacing='1' width='500' align='center'>
    <tr>
        <td align="left" height="25"><img src="./img/KcpLogo.jpg" border="0" width="65" height="50"></td>
        <td align='right' class="txt_main">KCP Online Payment System [PHP Version]</td>
    </tr>
    <tr>
        <td bgcolor="CFCFCF" height='3' colspan='2'></td>
    </tr>
    <tr>
        <td colspan="2">
            <br>
            <table width="90%" align="center">
                <tr>
                    <td bgcolor="CFCFCF" height='2'></td>
                </tr>
                <tr>
                    <td align="center">취소 요청 </td>
                </tr>
                <tr>
                    <td bgcolor="CFCFCF" height='2'></td>
                </tr>
            </table>
            <table width="90%" align="center">
                <tr>
                    <td width="136">주문번호</td>
                    <td><?=$oinfo['ordercode']?></td>
				</tr>
				<tr>
					<td>승인번호</td><td><?=$oinfo['pay_auth_no']?></td>
                </tr>
				<tr>
                    <td width="136">주문자</td>
                    <td><?=$oinfo['id']?></td>
                </tr>
				<tr>
                    <td width="136">구분</td>
                    <td>
						<input type="hidden" name="mod_type" value="<?=$mod_type?>"><? echo ($mod_type == 'RN07')?'신용카드 부분 취소':'계좌이체 부분 취소'?>											
                    </td>
                </tr>
                <tr>
                    <td>KCP 거래번호</td>
                    <td>
                        <input type='text' name='tno' size='20' value="<?=$oinfo['tno']?>" maxlength='14'>
                    </td>
                </tr>
				 <tr id='show_mod_mny'>
                    <td width='120' class='text3'>취소 가능 잔액</td>
                    <td>
                        <input type='text' name='rem_mny' value='<?=$oinfo['price']?>' size='20' maxlength='14'>
                    </td>
                </tr>
                <tr>
                    <td>변경 사유</td>
                    <td>
                        <input type='text' name='mod_desc' value='<?=$mod_desc?>' size='42' maxlength='100'>
                    </td>
                </tr>
                <tr id='show_mod_mny'>
                    <td width='120' class='text3'>취소 요청 금액</td>
                    <td>
                        <input type='text' name='mod_mny' value='' size='20' maxlength='14'>
                    </td>
                </tr>               
                <tr>
                    <td colspan="2" align="center">
                        <input type="button" value="확 인" class="box" onclick='jsf__go_mod( this.form )'>
						 <input type="button" value="취소" class="box" onclick='window.close()'>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td bgcolor="CFCFCF" height='3' colspan='2'></td>
    </tr>
    <tr>
        <td colspan='2' align="center" height='25'>ⓒ Copyright 2008. KCP Inc.  All Rights Reserved.</td>
    </tr>
</table>

</form>
</body>
</html>
