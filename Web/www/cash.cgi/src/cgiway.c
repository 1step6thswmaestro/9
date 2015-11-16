#include <stdio.h>
#include <string.h>

#include "../inc/cgi.h"
#include "../inc/msg_data.h"

void INITCGI() 
{
    printf("Content-Type: text/html; charset=EUC-KR\n\n");

    if (!cgi_init()) 
    {
        printf("INITCGI Error");
        exit(1);
    }
}

char *Request(char *varname) 
{
    char * rtval = NULL;
    rtval = (char *)cgi_param(varname);
    if( rtval == NULL || strlen(rtval) == 0)
        return NULL;
    return rtval;
}

void ENDCGI() 
{
    cgi_done();
    exit(0);
}

int main()
{
    CashDataVal val;
    CashDataBuf buf;
    char * cashtype = NULL;
        
    INITCGI();
    
    InitializeCashData(&val, &buf);
    
    cashtype = Request("cashtype");
    
    val.midbykcp = Request("midbykcp");
    val.termid = Request("termid");
    val.cashipaddress1 = Request("cashipaddress1");
    val.cashportno1 = Request("cashportno1");
    val.cashipaddress2 = Request("cashipaddress2");
    val.cashportno2 = Request("cashportno2");    
    val.tax_no = Request("tax_no");
    
    val.tsdtime = Request("tsdtime");
    val.id_info = Request("id_info");
    val.extend1 = Request("extend1");
    val.extend2 = Request("extend2");
    val.extend3 = Request("extend3");
    val.extend4 = Request("extend4");
    
    val.sell_type = Request("sell_type");
    val.sell_name = Request("sell_name");
    val.sell_addr = Request("sell_addr");
    val.sell_tel = Request("sell_tel");
    val.opt_val = Request("opt_val");

    if( strcmp(cashtype, "AUTH") == 0 || strcmp(cashtype, "VOID") == 0 )
    {
        val.tr_code = Request("tr_code");    
        val.amt1 = Request("amt1");
        val.amt2 = Request("amt2");
        val.amt3 = Request("amt3");
        val.amt4 = Request("amt4");
        val.orderid = Request("orderid");
        val.mcht_name = Request("mcht_name");
        val.prod_name = Request("prod_name");
        val.cons_name = Request("cons_name");
        val.cons_tel = Request("cons_tel");
        val.cons_email = Request("cons_email");

        if( strcmp(cashtype, "AUTH") == 0 )
        {
            RequestCashDataAuth(&val, &buf);
        }
        else if( strcmp(cashtype, "VOID") == 0 )
        {
            RequestCashDataVoid(&val, &buf);
        }
    }
    else if( strcmp(cashtype, "QURY") == 0 )
    {
        val.authno = Request("authno");
        val.mtrsno = Request("mtrsno");
        
        RequestCashDataQury(&val, &buf);
    }
    else
    {
        printf("invalid request cashtype");
        ENDCGI();
    }

    if( val.error_msg != NULL )
    {
        printf("error_msg=%s<br>\n", val.error_msg);
    }
    else
    {
        if( memcmp(val.mrspc, "00", 2) == 0 )
        {
            printf("tsdtime=%s<br>\n", val.tsdtime);
            printf("msg_type=%s<br>\n", val.msg_type);
            printf("tr_code=%s<br>\n", val.tr_code);
            printf("tax_no=%s<br>\n", val.tax_no);
            printf("term_id=%s<br>\n", val.term_id);
            printf("id_info=%s<br>\n", val.id_info);
            printf("amt1=%s<br>\n", val.amt1);
            printf("amt2=%s<br>\n", val.amt2);
            printf("amt3=%s<br>\n", val.amt3);
            printf("amt4=%s<br>\n", val.amt4);
            printf("authno=%s<br>\n", val.authno);
            printf("mrspc=%s<br>\n", val.mrspc);
            printf("hrspc=%s<br>\n", val.hrspc);
            printf("pem=%s<br>\n", val.pem);
            printf("sd_flag=%s<br>\n", val.sd_flag);
            printf("orderid=%s<br>\n", val.orderid);
            printf("mcht_name=%s<br>\n", val.mcht_name);
            printf("prod_name=%s<br>\n", val.prod_name);
            printf("cons_name=%s<br>\n", val.cons_name);
            printf("cons_tel=%s<br>\n", val.cons_tel);
            printf("cons_email=%s<br>\n", val.cons_email);
            printf("entdtime=%s<br>\n", val.entdtime);
            printf("snddtime=%s<br>\n", val.snddtime);
            printf("reqdtime=%s<br>\n", val.reqdtime);
            printf("mtrsno=%s<br>\n", val.mtrsno);
            printf("resp_msg=%s<br>\n", val.resp_msg);
            printf("extend1=%s<br>\n", val.extend1);
            printf("extend2=%s<br>\n", val.extend2);
            printf("extend3=%s<br>\n", val.extend3);
            printf("extend4=%s<br>\n", val.extend4);
            
            printf("sell_type=%s<br>\n", val.sell_type);
            printf("sell_name=%s<br>\n", val.sell_name);
            printf("sell_addr=%s<br>\n", val.sell_addr);
            printf("sell_tel=%s<br>\n", val.sell_tel);
            printf("opt_val=%s<br>\n", val.opt_val);
        }
        else
        {
            printf("mrspc=%s<br>\n", val.mrspc);
            printf("resp_msg=%s<br>\n", val.resp_msg);
        }
    }
        
    ENDCGI();
    return 0;
}

