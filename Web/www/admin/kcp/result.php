<?
    /* ============================================================================== */
    /* =   PAGE : 결과 표시 PAGE                                                    = */
    /* = -------------------------------------------------------------------------- = */
    /* =   Copyright (c)  2008   KCP Inc.   All Rights Reserverd.                   = */
    /* ============================================================================== */
?>
<?
    /* ============================================================================== */
    /* =   요청 결과                                                                = */
    /* = -------------------------------------------------------------------------- = */
    $req_tx           = $_POST[ "req_tx"     ];                    // 요청 종류
    $mod_type         = $_POST[ "mod_type"   ];                    // 변경 요청 종류
    /* = -------------------------------------------------------------------------- = */
    $res_cd           = $_POST[ "res_cd"     ];                    // 결과코드
    $res_msg          = $_POST[ "res_msg"    ];                    // 결과메시지
    $tno              = $_POST[ "tno"        ];                    // 거래번호
    /* = -------------------------------------------------------------------------- = */
    $amount           = $_POST[ "amount"     ];                    // 원 거래금액
    $mod_mny          = $_POST[ "mod_mny"    ];                    // 취소요청된 금액
    $rem_mny          = $_POST[ "rem_mny"    ];                    // 취소요청후 잔액
    /* ============================================================================== */

    $req_tx_name = "";

    if( $mod_type == "STSC" )
    {
        $req_tx_name = "취소";
    }
    else if( $mod_type == "RN07" )
    {
        $req_tx_name = "부분취소";
    }
?>
    <html>
    <head>
    <link href="css/sample.css" rel="stylesheet" type="text/css">
    <script language="javascript">
        <!-- 신용카드 영수증 연동 스크립트 -->
        function receiptView(tno)
        {
            receiptWin = "http://admin.kcp.co.kr/Modules/Sale/Card/ADSA_CARD_BILL_Receipt.jsp?c_trade_no=" + tno;
            window.open(receiptWin , "" , "width=420, height=670");
        }
    </script>
    </head>
    <body>
    <center>
    <table border='0' cellpadding='0' cellspacing='1' width='500' align='center'>
        <tr>
            <td align="left" height="25"><img src="./img/KcpLogo.jpg" border="0" width="65" height="50"></td>
            <td align='right' class="txt_main">KCP Online Payment System [MOD HUB PHP Version]</td>
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
                        <td align="center">결과 페이지(<?=$req_tx_name?>)</td>
                    </tr>
                    <tr>
                        <td bgcolor="CFCFCF" height='2'></td>
                    </tr>
                </table>
<?
    if ( $req_tx == "mod" )                     // 거래 구분 : 취소
    {
?>
                <table width="85%" align="center" border='0' cellpadding='0' cellspacing='1'>
                    <tr>
                        <td>결과코드</td>
                        <td><?=$res_cd?></td>
                    </tr>
                    <tr><td colspan="2"><IMG SRC="./img/dot_line.gif" width="100%"></td></tr>
                    <tr>
                        <td>결과 메세지</td>
                        <td><?=$res_msg?></td>
                    </tr>
<?
        if ( $res_cd == "0000" )                    // 정상 취소
        {
            if ( $mod_type == "RN07" )              // 부분취소
            {
?>
                    <tr><td colspan="2"><IMG SRC="./img/dot_line.gif" width="100%"></td></tr>
                    <tr>
                        <td>원 거래금액</td>
                        <td><?=$amount?></td>
                    </tr>
                    <tr><td colspan="2"><IMG SRC="./img/dot_line.gif" width="100%"></td></tr>
                    <tr>
                        <td>취소요청된 금액</td>
                        <td><?=$mod_mny?></td>
                    </tr>
                    <tr><td colspan="2"><IMG SRC="./img/dot_line.gif" width="100%"></td></tr>
                    <tr>
                        <td>취소가능 잔액</td>
                        <td><?=$rem_mny?></td>
                    </tr>
<?
            }
?>
                    <tr><td colspan="2"><IMG SRC="./img/dot_line.gif" width="100%"></td></tr>
                    <tr>
                        <td>신용카드 영수증</td>
                        <td><input type="button" name="receiptView" value="영수증 확인" class="box" onClick="javascript:receiptView('<?=$tno?>')"></td>
                    </tr>
<?
        }
    }
?>
                </table>
                <table width="90%" align="center">
                    <tr>
                        <td bgcolor="CFCFCF" height='2'></td>
                    </tr>
                    <tr>
                        <td height='2'>&nbsp;</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td bgcolor="CFCFCF" height='3' colspan='2'></td>
        </tr>
    </table>
	<div style="text-align:center"><button onClick="javascript:cwin()">확인</button>
    </center>
	<script language="javascript" type="text/javascript">
	function cwin(){
		opener.document.location.reload();
		window.close();
	}
	</script>
    </body>
    </html>
