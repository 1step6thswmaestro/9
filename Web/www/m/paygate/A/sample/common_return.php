<?
    /* ============================================================================== */
    /* =   PAGE : ���� �뺸 PAGE                                                    = */
    /* = -------------------------------------------------------------------------- = */
    /* =   ������ ������ �߻��ϴ� ��� �Ʒ��� �ּҷ� �����ϼż� Ȯ���Ͻñ� �ٶ��ϴ�.= */
    /* =   ���� �ּ� : http://kcp.co.kr/technique.requestcode.do                    = */
    /* = -------------------------------------------------------------------------- = */
    /* =   Copyright (c)  2013   KCP Inc.   All Rights Reserverd.                   = */
    /* ============================================================================== */
?>
<?
    /* ============================================================================== */
    /* =   01. ���� �뺸 ������ ����(�ʵ�!!)                                        = */
    /* = -------------------------------------------------------------------------- = */
    /* =   ���� �뺸 ������������,                                                  = */
    /* =   ������� �Ա� �뺸 �����Ϳ� ����ϾȽɰ��� �뺸 ������ ���� KCP�� ����   = */
    /* =   �ǽð����� �뺸 ���� �� �ֽ��ϴ�.                                        = */
    /* =                                                                            = */
    /* =   common_return �������� �̷��� �뺸 �����͸� �ޱ� ���� ���� ������        = */
    /* =   �Դϴ�. ������ �������� ��ü�� �°� �����Ͻ� ��, �Ʒ� ������ �����ϼż�  = */
    /* =   KCP ������ �������� ����� �ֽñ� �ٶ��ϴ�.                              = */
    /* =                                                                            = */
    /* =   ��� ����� ������ �����ϴ�.                                             = */
    /* =  - KCP ������������(admin.kcp.co.kr)�� �α��� �մϴ�.                      = */
    /* =  - [���θ� ����] -> [��������] -> [���� URL ����] -> [���� URL ���� ��]��  = */
    /* =    ������� ���۹��� ������ URL�� �Է��մϴ�.                              = */
    /* ============================================================================== */


    /* ============================================================================== */
    /* =   02. ���� �뺸 ������ �ޱ�                                                = */
    /* = -------------------------------------------------------------------------- = */
    $site_cd      = $_POST [ "site_cd"  ];                 // ����Ʈ �ڵ�
    $tno          = $_POST [ "tno"      ];                 // KCP �ŷ���ȣ
    $order_no     = $_POST [ "order_no" ];                 // �ֹ���ȣ
    $tx_cd        = $_POST [ "tx_cd"    ];                 // ����ó�� ���� �ڵ�
    $tx_tm        = $_POST [ "tx_tm"    ];                 // ����ó�� �Ϸ� �ð�
    /* = -------------------------------------------------------------------------- = */
    $ipgm_name    = "";                                    // �ֹ��ڸ�
    $remitter     = "";                                    // �Ա��ڸ�
    $ipgm_mnyx    = "";                                    // �Ա� �ݾ�
    $bank_code    = "";                                    // �����ڵ�
    $account      = "";                                    // ������� �Աݰ��¹�ȣ
    $op_cd        = "";                                    // ó������ �ڵ�
    $noti_id      = "";                                    // �뺸 ���̵�
    /* = -------------------------------------------------------------------------- = */
	$refund_nm    = "";                                    // ȯ�Ұ����ָ�
    $refund_mny   = "";                                    // ȯ�ұݾ�
    $bank_code    = "";                                    // �����ڵ�
    /* = -------------------------------------------------------------------------- = */
    $st_cd        = "";                                    // ����Ȯ�� �ڵ�
    $can_msg      = "";                                    // ������� ����
    /* = -------------------------------------------------------------------------- = */
    $waybill_no   = "";                                    // ����� ��ȣ
    $waybill_corp = "";                                    // �ù� ��ü��
    /* = -------------------------------------------------------------------------- = */
    $cash_a_no    = "";                                    // ���ݿ����� ���ι�ȣ

    /* = -------------------------------------------------------------------------- = */
    /* =   02-1. ������� �Ա� �뺸 ������ �ޱ�                                     = */
    /* = -------------------------------------------------------------------------- = */
    if ( $tx_cd == "TX00" )
    {
        $ipgm_name = $_POST[ "ipgm_name" ];                // �ֹ��ڸ�
        $remitter  = $_POST[ "remitter"  ];                // �Ա��ڸ�
        $ipgm_mnyx = $_POST[ "ipgm_mnyx" ];                // �Ա� �ݾ�
        $bank_code = $_POST[ "bank_code" ];                // �����ڵ�
        $account   = $_POST[ "account"   ];                // ������� �Աݰ��¹�ȣ
        $op_cd     = $_POST[ "op_cd"     ];                // ó������ �ڵ�
        $noti_id   = $_POST[ "noti_id"   ];                // �뺸 ���̵�
        $cash_a_no = $_POST[ "cash_a_no" ];                // ���ݿ����� ���ι�ȣ
    }

	/* = -------------------------------------------------------------------------- = */
    /* =   02-2. ������� ȯ�� �뺸 ������ �ޱ�                                     = */
    /* = -------------------------------------------------------------------------- = */
    else if ( $tx_cd == "TX01" )
	{
        $refund_nm  = $_POST[ "refund_nm"  ];               // ȯ�Ұ����ָ�
        $refund_mny = $_POST[ "refund_mny" ];               // ȯ�ұݾ�
        $bank_code  = $_POST[ "bank_code"  ];               // �����ڵ�
	}
    /* = -------------------------------------------------------------------------- = */
    /* =   02-3. ����Ȯ��/������� �뺸 ������ �ޱ�                                  = */
    /* = -------------------------------------------------------------------------- = */
    else if ( $tx_cd == "TX02" )

        $st_cd = $_POST "st_cd" 						    // ����Ȯ�� �ڵ�

        if ( $st_cd = "N"  )								// ����Ȯ�� ���°� ��������� ���
		{
            $can_msg = $_POST[ "can_msg"   ];               // ������� ����
		}
        
    /* = -------------------------------------------------------------------------- = */
    /* =   02-4. ��۽��� �뺸 ������ �ޱ�                                           = */
    /* = -------------------------------------------------------------------------- = */
    else if ( $tx_cd == "TX03" )
	{

        $waybill_no   = $_POST[ "waybill_no"   ];           // ����� ��ȣ
        $waybill_corp = $_POST[ "waybill_corp" ];           // �ù� ��ü��
	}

    /* ============================================================================== */
    /* =   03. ���� �뺸 ����� ��ü ��ü������ DB ó�� �۾��Ͻô� �κ��Դϴ�.      = */
    /* = -------------------------------------------------------------------------- = */
    /* =   �뺸 ����� DB �۾� �ϴ� �������� ���������� �뺸�� �ǿ� ���� DB �۾���  = */
    /* =   �����Ͽ� DB update �� �Ϸ���� ���� ���, ����� ���뺸 ���� �� �ִ�     = */
    /* =   ���μ����� �����Ǿ� �ֽ��ϴ�.                                            = */
    /* =                                                                            = */
    /* =   * DB update�� ���������� �Ϸ�� ���                                     = */
    /* =   �ϴ��� [04. result �� ���� �ϱ�] ���� result ���� value���� 0000����     = */
    /* =   ������ �ֽñ� �ٶ��ϴ�.                                                  = */
    /* =                                                                            = */
    /* =   * DB update�� ������ ���                                                = */
    /* =   �ϴ��� [04. result �� ���� �ϱ�] ���� result ���� value���� 0000�̿���   = */
    /* =   ������ ������ �ֽñ� �ٶ��ϴ�.                                           = */
    /* = -------------------------------------------------------------------------- = */

    /* = -------------------------------------------------------------------------- = */
    /* =   03-1. ������� �Ա� �뺸 ������ DB ó�� �۾� �κ�                        = */
    /* = -------------------------------------------------------------------------- = */
    if ( $tx_cd == "TX00" )
    {
    }
	/* = -------------------------------------------------------------------------- = */
    /* =   03-2. ������� ȯ�� �뺸 ������ DB ó�� �۾� �κ�                        = */
    /* = -------------------------------------------------------------------------- = */
    else if ( $tx_cd == "TX01" )
    {
    }
    /* = -------------------------------------------------------------------------- = */
    /* =   03-3. ����Ȯ��/������� �뺸 ������ DB ó�� �۾� �κ�                    = */
    /* = -------------------------------------------------------------------------- = */
    else if ( $tx_cd == "TX02" )
    {
    }
    /* = -------------------------------------------------------------------------- = */
    /* =   03-4. ��۽��� �뺸 ������ DB ó�� �۾� �κ�                             = */
    /* = -------------------------------------------------------------------------- = */
    else if ( $tx_cd == "TX03" )
    {
    }
    /* = -------------------------------------------------------------------------- = */
    /* =   03-5. ���꺸�� �뺸 ������ DB ó�� �۾� �κ�                             = */
    /* = -------------------------------------------------------------------------- = */
    else if ( $tx_cd == "TX04" )
    {
    }
    /* = -------------------------------------------------------------------------- = */
    /* =   03-6. ������ �뺸 ������ DB ó�� �۾� �κ�                             = */
    /* = -------------------------------------------------------------------------- = */
    else if ( $tx_cd == "TX05" )
    {
    }
    /* = -------------------------------------------------------------------------- = */
    /* =   03-7. ��� �뺸 ������ DB ó�� �۾� �κ�                                 = */
    /* = -------------------------------------------------------------------------- = */
    else if ( $tx_cd == "TX06" )
    {
    }
    /* = -------------------------------------------------------------------------- = */
    /* =   03-8. �߱ް������� �뺸 ������ DB ó�� �۾� �κ�                         = */
    /* = -------------------------------------------------------------------------- = */
    else if ( $tx_cd == "TX07" )
    {
    }
    /* = -------------------------------------------------------------------------- = */
    /* =   03-9. ����ϾȽɰ��� �뺸 ������ DB ó�� �۾� �κ�                       = */
    /* = -------------------------------------------------------------------------- = */
    else if ( $tx_cd == "TX08" )
    {
    }
    /* ============================================================================== */


    /* ============================================================================== */
    /* =   04. result �� ���� �ϱ�                                                  = */
    /* ============================================================================== */
?>
<html><body><form><input type="hidden" name="result" value="0000"></form></body></html>