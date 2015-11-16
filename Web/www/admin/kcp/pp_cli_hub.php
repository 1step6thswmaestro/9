<?
    /* ============================================================================== */
    /* =   PAGE : 취소 요청 PAGE                                                    = */
    /* = -------------------------------------------------------------------------- = */
    /* =   Copyright (c)  2008   KCP Inc.   All Rights Reserverd.                   = */
    /* ============================================================================== */
?>
<?
    /* ============================================================================== */
    /* = 라이브러리 및 사이트 정보 include                                          = */
    /* = -------------------------------------------------------------------------- = */
    require "./pp_cli_hub_lib.php";
    
	if($_POST['mod_type'] == 'RN07') $oinfo['paymethod'] = 'C';
	else $oinfo['paymethod'] = 'V';
	
	include "./config.php";
	
    /* ============================================================================== */
	
	
    /* ============================================================================== */
    /* =   01. 취소 요청 정보 설정                                                  = */
    /* = -------------------------------------------------------------------------- = */
    $req_tx           = $_POST[ "req_tx"     ];                    // 요청 종류
    $cust_ip          = getenv( "REMOTE_ADDR" );                   // 요청 IP
    $mod_type         = $_POST[ "mod_type"   ];                    // 변경 요청 종류
    /* = -------------------------------------------------------------------------- = */
    $res_cd           = "";                                        // 결과코드
    $res_msg          = "";                                        // 결과메시지
    $tno              = "";                                        // 거래번호
    /* = -------------------------------------------------------------------------- = */
    $tx_cd            = "";                                        // 트랜잭션 코드
    /* = -------------------------------------------------------------------------- = */
    $amount           = "";                                        // 원 거래금액
    $mod_mny          = "";                                        // 취소요청금액
    $rem_mny          = "";                                        // 취소가능잔액
    /* ============================================================================== */


    /* ============================================================================== */
    /* =   02. 인스턴스 생성 및 초기화                                              = */
    /* = -------------------------------------------------------------------------- = */
    $c_PayPlus  = new C_PAYPLUS_CLI;
    $c_PayPlus->mf_clear();
    /* ============================================================================== */


    /* ============================================================================== */
    /* =   03. 처리 요청 정보 설정, 실행                                            = */
    /* = -------------------------------------------------------------------------- = */

    /* = -------------------------------------------------------------------------- = */
    /* =   03-1. 취소 요청                                                          = */
    /* = -------------------------------------------------------------------------- = */
        if ( $req_tx == "mod" )
        {
            $tx_cd = "00200000";

            $c_PayPlus->mf_set_modx_data( "tno",      $_POST[ "tno" ]      ); // KCP 원거래 거래번호
            $c_PayPlus->mf_set_modx_data( "mod_type", $mod_type            ); // 원거래 변경 요청 종류
            $c_PayPlus->mf_set_modx_data( "mod_ip",   $cust_ip             ); // 변경 요청자 IP
            $c_PayPlus->mf_set_modx_data( "mod_desc", $_POST[ "mod_desc" ] ); // 변경 사유

            if ( $mod_type == "RN07" || $mod_type == "STPA" ) // 부분취소의 경우
            {
                $c_PayPlus->mf_set_modx_data( "mod_mny", $_POST[ "mod_mny" ] ); // 취소요청금액
                $c_PayPlus->mf_set_modx_data( "rem_mny", $_POST[ "rem_mny" ] ); // 취소가능잔액
            }
        }

    /* ============================================================================== */
    /* =   03-2. 실행                                                               = */
    /* ------------------------------------------------------------------------------ */
        if ( strlen($tx_cd) > 0 )
        {
            $c_PayPlus->mf_do_tx( "",                $g_conf_home_dir, $g_conf_site_cd,
                                  $g_conf_site_key,  $tx_cd,           "",
                                  $g_conf_pa_url,    $g_conf_pa_port,  "payplus_cli_slib",
                                  "",                $cust_ip,         "3",
                                  "",                0 );
        }
        else
        {
            $c_PayPlus->m_res_cd  = "9562";
            $c_PayPlus->m_res_msg = "연동 오류";
        }
        $res_cd  = $c_PayPlus->m_res_cd;                      // 결과 코드
        $res_msg = $c_PayPlus->m_res_msg;                     // 결과 메시지
    /* ============================================================================== */


    /* ============================================================================== */
    /* =   04. 취소 결과 처리                                                       = */
    /* = -------------------------------------------------------------------------- = */
        if ( $req_tx == "mod" )
        {
            if ( $res_cd == "0000" )
            {
                $tno = $c_PayPlus->mf_get_res_data( "tno" );  // KCP 거래 고유 번호

    /* = -------------------------------------------------------------------------- = */
    /* =   04-1. 부분취소 결과 처리                                                 = */
    /* = -------------------------------------------------------------------------- = */
                if ( $mod_type == "RN07" ) // 부분취소의 경우
                {
                    $amount  = $c_PayPlus->mf_get_res_data( "amount"       ); // 원 거래금액
                    $mod_mny = $c_PayPlus->mf_get_res_data( "panc_mod_mny" ); // 취소요청된 금액
                    $rem_mny = $c_PayPlus->mf_get_res_data( "panc_rem_mny" ); // 취소요청후 잔액
					
				//	_pr($_
					$errorstr = '';
					$opt1_name = 0;
					
					$sql = "select ifnull(max(opt1_name),0) from tblorderproduct where ordercode='".$_POST['ordercode']."' and tempkey='".$_POST['tempkey']."' and productcode='99999999995X'";
					$res = mysql_query($sql,get_db_conn());
					if($res){
						$opt1_name = mysql_result($res,0,0);						
					}
					
					$opt1_name += 1;
					
					$sql = "insert into tblorderproduct set ordercode='".$_POST['ordercode']."',tempkey='".$_POST['tempkey']."',productcode='99999999995X',productname='결제부분취소' ,opt1_name='".$opt1_name."',opt2_name='',addcode='',quantity=1,price=".(-1*($mod_mny)).",date='".date('Ymd')."',selfcode='',etcapply_gift='',productbisiness='',order_prmsg='".$_POST[ "mod_desc" ]."<br>취소요청후 잔액:".$rem_mny."',assemble_info=''";					
					if(false === mysql_query($sql,get_db_conn())) $errorstr = mysql_error().'<br>['.$sql.']<br>';
					
					$sql = "update tblorderinfo set price = price - ".$mod_mny." where ordercode ='".$_POST['ordercode']."'";									
					if(false === mysql_query($sql,get_db_conn())) $errorstr .= mysql_error().'<br>['.$sql.']<br>';
					/*
					if(!empty($errorstr)){
						echo $errorstr;						
					}
					*/
                }
            } // End of [res_cd = "0000"]

    /* = -------------------------------------------------------------------------- = */
    /* =   04-2. 취소 실패 결과 처리                                                = */
    /* = -------------------------------------------------------------------------- = */
            else
            {
            }
        } // End of Process


    /* ============================================================================== */
    /* =   05. 폼 구성 및 결과페이지 호출                                           = */
    /* ============================================================================== */
?>

    <html>
    <head>
    <script>
        function goResult()
        {
			<? if(empty($errorstr)){ ?>
            document.pay_info.submit();
			<? } ?>
        }
    </script>
    </head>
    <body onLoad="goResult()">
    <form name="pay_info" method="post" action="./result.php">
        <input type="hidden" name="req_tx"            value="<?=$req_tx?>">            <!-- 요청 구분 -->
        <input type="hidden" name="mod_type"          value="<?=$mod_type?>">          <!-- 변경 요청 구분 -->

        <input type="hidden" name="res_cd"            value="<?=$res_cd?>">            <!-- 결과 코드 -->
        <input type="hidden" name="res_msg"           value="<?=$res_msg?>">           <!-- 결과 메세지 -->
        <input type="hidden" name="tno"               value="<?=$tno?>">               <!-- KCP 거래번호 -->

        <input type="hidden" name="amount"            value="<?=$amount?>">            <!-- 원 거래금액 -->
        <input type="hidden" name="mod_mny"           value="<?=$mod_mny?>">           <!-- 취소요청된 금액 -->
        <input type="hidden" name="rem_mny"           value="<?=$rem_mny?>">           <!-- 취소요청후 잔액 -->
		<? if(!empty($errorstr)){ ?>
			<span style="color:red">연동 정보 내부 DB 반영시 오류가 발생했습니다. 아래의 코드를 jhj@objet.co.kr 로 발생해 주시기 바랍니다.<br><? echo $errorstr; ?></span>
			KCP 연동 결과 확인을 위해 아래 "결과 확인"을 클릭해 주시기 바랍니다.		
		<input type="submit" value="결과 확인">
		<? } ?>
    </form>
    </body>
    </html>
