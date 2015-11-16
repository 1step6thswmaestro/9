#include <stdio.h>
#include <string.h>

#include "../inc/msg_data.h"

void test1()
{
    CashDataVal val;
    CashDataBuf buf;

    InitializeCashData(&val, &buf);
    
    val.midbykcp = "MT31";
    val.termid = "T00000";
    val.cashipaddress1 = "203.238.36.152";
    val.cashportno1 = "9981";
    val.cashipaddress2 = "203.238.36.152";
    val.cashportno2 = "9981";    
    val.tsdtime = "041023123159";
    val.tr_code = "0";
    val.tax_no = "0000000000";
    val.id_info = "000011112222";
    val.amt1 = "5000";
    val.amt2 = "";
    val.amt3 = "";
    val.amt4 = "";
    val.orderid = "";
    val.mcht_name = "테스트상점";
    val.prod_name = "상품명";
    val.cons_name = "고객명";
    val.cons_tel = "0000-1111-2222";
    val.cons_email = "test@test.com";
    val.extend1 = "";
    val.extend2 = "127.0.0.1";
    val.extend3 = "";
    val.extend4 = "";
    /*-- 
    입점몰 정보를 넘기는 경우에 아래 정보를 채워줍니다.
    직접 판매시에는 넘기지 않아도 됩니다.
    --*/
    val.sell_type = "1";
    val.sell_name = "대표자명";
    val.sell_addr = "판매처주소";
    val.sell_tel = "0000-1111-2222";
    val.opt_val = "";

    printf("현금영수증승인\n");
    RequestCashDataAuth(&val, &buf);
    
    if( val.error_msg != NULL )
    {
        printf("error_msg=[%s]\n", val.error_msg);
    }
    else
    {
        if( memcmp(val.mrspc, "00", 2) == 0 )
        {
            printf("tsdtime=[%s]\n", val.tsdtime);
            printf("msg_type=[%s]\n", val.msg_type);
            printf("tr_code=[%s]\n", val.tr_code);
            printf("tax_no=[%s]\n", val.tax_no);
            printf("term_id=[%s]\n", val.term_id);
            printf("id_info=[%s]\n", val.id_info);
            printf("amt1=[%s]\n", val.amt1);
            printf("amt2=[%s]\n", val.amt2);
            printf("amt3=[%s]\n", val.amt3);
            printf("amt4=[%s]\n", val.amt4);
            printf("authno=[%s]\n", val.authno);
            printf("mrspc=[%s]\n", val.mrspc);
            printf("hrspc=[%s]\n", val.hrspc);
            printf("pem=[%s]\n", val.pem);
            printf("sd_flag=[%s]\n", val.sd_flag);
            printf("orderid=[%s]\n", val.orderid);
            printf("mcht_name=[%s]\n", val.mcht_name);
            printf("prod_name=[%s]\n", val.prod_name);
            printf("cons_name=[%s]\n", val.cons_name);
            printf("cons_tel=[%s]\n", val.cons_tel);
            printf("cons_email=[%s]\n", val.cons_email);
            printf("entdtime=[%s]\n", val.entdtime);
            printf("snddtime=[%s]\n", val.snddtime);
            printf("reqdtime=[%s]\n", val.reqdtime);
            printf("mtrsno=[%s]\n", val.mtrsno);
            printf("resp_msg=[%s]\n", val.resp_msg);
            printf("extend1=[%s]\n", val.extend1);
            printf("extend2=[%s]\n", val.extend2);
            printf("extend3=[%s]\n", val.extend3);
            printf("extend4=[%s]\n", val.extend4);
            
            printf("sell_type=[%s]\n", val.sell_type);
            printf("sell_name=[%s]\n", val.sell_name);
            printf("sell_addr=[%s]\n", val.sell_addr);
            printf("sell_tel=[%s]\n", val.sell_tel);
            printf("opt_val=[%s]\n", val.opt_val);
        }
        else
        {
            printf("mrspc=[%s]\n", val.mrspc);
            printf("resp_msg=[%s]\n", val.resp_msg);
        }
    }
}

void test2()
{
    CashDataVal val;
    CashDataBuf buf;

    InitializeCashData(&val, &buf);
    
    val.midbykcp = "MT31";
    val.termid = "T00000";
    val.cashipaddress1 = "203.238.36.152";
    val.cashportno1 = "9981";
    val.cashipaddress2 = "203.238.36.152";
    val.cashportno2 = "9981";    
    val.tsdtime = "041023123159";
    val.tr_code = "0";
    val.tax_no = "0000000000";
    val.id_info = "000011112222";
    val.amt1 = "5000";
    val.amt2 = "";
    val.amt3 = "";
    val.amt4 = "";
    val.orderid = "";
    val.mcht_name = "테스트상점";
    val.prod_name = "상품명";
    val.cons_name = "고객명";
    val.cons_tel = "0000-1111-2222";
    val.cons_email = "test@test.com";
    val.extend1 = "";
    val.extend2 = "127.0.0.1";
    val.extend3 = "";
    val.extend4 = "";
    /*-- 
    입점몰 정보를 넘기는 경우에 아래 정보를 채워줍니다.
    직접 판매시에는 넘기지 않아도 됩니다.
    --*/
    val.sell_type = "1";
    val.sell_name = "대표자명";
    val.sell_addr = "판매처주소";
    val.sell_tel = "0000-1111-2222";
    val.opt_val = "";
        
    printf("현금영수증취소\n");
    RequestCashDataVoid(&val, &buf);
    
    if( val.error_msg != NULL )
    {
        printf("error_msg=[%s]\n", val.error_msg);
    }
    else
    {
        if( memcmp(val.mrspc, "00", 2) == 0 )
        {
            printf("tsdtime=[%s]\n", val.tsdtime);
            printf("msg_type=[%s]\n", val.msg_type);
            printf("tr_code=[%s]\n", val.tr_code);
            printf("tax_no=[%s]\n", val.tax_no);
            printf("term_id=[%s]\n", val.term_id);
            printf("id_info=[%s]\n", val.id_info);
            printf("amt1=[%s]\n", val.amt1);
            printf("amt2=[%s]\n", val.amt2);
            printf("amt3=[%s]\n", val.amt3);
            printf("amt4=[%s]\n", val.amt4);
            printf("authno=[%s]\n", val.authno);
            printf("mrspc=[%s]\n", val.mrspc);
            printf("hrspc=[%s]\n", val.hrspc);
            printf("pem=[%s]\n", val.pem);
            printf("sd_flag=[%s]\n", val.sd_flag);
            printf("orderid=[%s]\n", val.orderid);
            printf("mcht_name=[%s]\n", val.mcht_name);
            printf("prod_name=[%s]\n", val.prod_name);
            printf("cons_name=[%s]\n", val.cons_name);
            printf("cons_tel=[%s]\n", val.cons_tel);
            printf("cons_email=[%s]\n", val.cons_email);
            printf("entdtime=[%s]\n", val.entdtime);
            printf("snddtime=[%s]\n", val.snddtime);
            printf("reqdtime=[%s]\n", val.reqdtime);
            printf("mtrsno=[%s]\n", val.mtrsno);
            printf("resp_msg=[%s]\n", val.resp_msg);
            printf("extend1=[%s]\n", val.extend1);
            printf("extend2=[%s]\n", val.extend2);
            printf("extend3=[%s]\n", val.extend3);
            printf("extend4=[%s]\n", val.extend4);
            
            printf("sell_type=[%s]\n", val.sell_type);
            printf("sell_name=[%s]\n", val.sell_name);
            printf("sell_addr=[%s]\n", val.sell_addr);
            printf("sell_tel=[%s]\n", val.sell_tel);
            printf("opt_val=[%s]\n", val.opt_val);
        }
        else
        {
            printf("mrspc=[%s]\n", val.mrspc);
            printf("resp_msg=[%s]\n", val.resp_msg);
        }
    }
}

void test3()
{
    CashDataVal val;
    CashDataBuf buf;

    InitializeCashData(&val, &buf);
    
    val.midbykcp = "MT31";
    val.termid = "T00000";
    val.cashipaddress1 = "203.238.36.152";
    val.cashportno1 = "9981";
    val.cashipaddress2 = "203.238.36.152";
    val.cashportno2 = "9981";    
    val.tsdtime = "041023123159";
    val.tax_no = "0000000000";
    val.id_info = "000011112222";
    val.authno = "590000121";
    val.mtrsno = "0412cT80J7Co";
    
    printf("현금영수증조회\n");
    RequestCashDataQury(&val, &buf);
    
    if( val.error_msg != NULL )
    {
        printf("error_msg=[%s]\n", val.error_msg);
    }
    else
    {
        if( memcmp(val.mrspc, "00", 2) == 0 )
        {
            printf("tsdtime=[%s]\n", val.tsdtime);
            printf("msg_type=[%s]\n", val.msg_type);
            printf("tr_code=[%s]\n", val.tr_code);
            printf("tax_no=[%s]\n", val.tax_no);
            printf("term_id=[%s]\n", val.term_id);
            printf("id_info=[%s]\n", val.id_info);
            printf("amt1=[%s]\n", val.amt1);
            printf("amt2=[%s]\n", val.amt2);
            printf("amt3=[%s]\n", val.amt3);
            printf("amt4=[%s]\n", val.amt4);
            printf("authno=[%s]\n", val.authno);
            printf("mrspc=[%s]\n", val.mrspc);
            printf("hrspc=[%s]\n", val.hrspc);
            printf("pem=[%s]\n", val.pem);
            printf("sd_flag=[%s]\n", val.sd_flag);
            printf("orderid=[%s]\n", val.orderid);
            printf("mcht_name=[%s]\n", val.mcht_name);
            printf("prod_name=[%s]\n", val.prod_name);
            printf("cons_name=[%s]\n", val.cons_name);
            printf("cons_tel=[%s]\n", val.cons_tel);
            printf("cons_email=[%s]\n", val.cons_email);
            printf("entdtime=[%s]\n", val.entdtime);
            printf("snddtime=[%s]\n", val.snddtime);
            printf("reqdtime=[%s]\n", val.reqdtime);
            printf("mtrsno=[%s]\n", val.mtrsno);
            printf("resp_msg=[%s]\n", val.resp_msg);
            printf("extend1=[%s]\n", val.extend1);
            printf("extend2=[%s]\n", val.extend2);
            printf("extend3=[%s]\n", val.extend3);
            printf("extend4=[%s]\n", val.extend4);
            
            printf("sell_type=[%s]\n", val.sell_type);
            printf("sell_name=[%s]\n", val.sell_name);
            printf("sell_addr=[%s]\n", val.sell_addr);
            printf("sell_tel=[%s]\n", val.sell_tel);
            printf("opt_val=[%s]\n", val.opt_val);            
        }
        else
        {
            printf("mrspc=[%s]\n", val.mrspc);
            printf("resp_msg=[%s]\n", val.resp_msg);
        }
    }
}

int main()
{
    test1();
    test2();
    test3();
    
    return 0;
}

