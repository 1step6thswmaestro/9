/*==========================================================================*/
/*  Module Name : msg_data.h                                                */
/*  Description : define message format and constant                        */
/*==========================================================================*/
#ifndef _MSG_DATA_H_
#define _MSG_DATA_H_

#define LEN_MSG_LEN     ( 4)
#define LEN_MIDBYKCP    ( 4)
#define LEN_TERMID      ( 6)
#define LEN_TSDTIME     (12)
#define LEN_MSG_TYPE    ( 4)
#define LEN_TR_CODE     ( 1)
#define LEN_TAX_NO      (10)
#define LEN_TERM_ID     (16)
#define LEN_ID_INFO     (20)
#define LEN_AMT1        (12)
#define LEN_AMT2        (12)
#define LEN_AMT3        (12)
#define LEN_AMT4        (12)
#define LEN_AUTHNO	    ( 9)
#define LEN_MRSPC       ( 2)
#define LEN_HRSPC       ( 2)
#define LEN_PEM         ( 2)
#define LEN_SD_FLAG     ( 1)
#define LEN_ORDERID     (50)
#define LEN_MCHT_NAME   (30)
#define LEN_PROD_NAME   (30)
#define LEN_CONS_NAME   (20)
#define LEN_CONS_TEL    (14)
#define LEN_CONS_EMAIL  (30)
#define LEN_ENTDTIME    (14)
#define LEN_SNDDTIME    (14)
#define LEN_REQDTIME    (14)
#define LEN_MTRSNO      (12)
#define LEN_RESP_MSG    (37)
#define LEN_EXTEND1     ( 8)
#define LEN_EXTEND2     (16)
#define LEN_EXTEND3     (24)
#define LEN_EXTEND4     (32)
/*-- INTER DEF --*/
#define LEN_SEQNO       ( 7)
/*-- version 2.0 --*/
#define LEN_SELL_TYPE   ( 1)
#define LEN_SELL_NAME   (15)
#define LEN_SELL_ADDR   (50)
#define LEN_SELL_TEL    (14)
#define LEN_OPT_VAL     (19)

typedef struct _tagInitCashData
{
	char 	msg_len		[LEN_MSG_LEN];	/* message length */
	char 	midbykcp	[LEN_MIDBYKCP];	/* mid */
	char 	termid		[LEN_TERMID];	/* termid */
	/*-- DataBase --*/
	char    tsdtime     [LEN_TSDTIME];  /*-- 원거래일시  --*/
	char 	msg_type	[LEN_MSG_TYPE];	/* message type */
	char 	tr_code     [LEN_TR_CODE];  /* 거래자 구분 */
	char    tax_no      [LEN_TAX_NO];   /*-- 가맹점 사업자번호 --*/
	char    term_id     [LEN_TERM_ID];  /*-- 가맹점 단말기번호 --*/
	char    id_info     [LEN_ID_INFO];  /*-- 신분확인ID --*/
	char    amt1        [LEN_AMT1];     /*-- 거래금액 총합계 --*/
	char    amt2        [LEN_AMT2];     /*-- 공급가액 --*/
	char    amt3        [LEN_AMT3];     /*-- 봉사료 --*/
	char    amt4        [LEN_AMT4];     /*-- 부가가치세 --*/
	char 	authno		[LEN_AUTHNO];   /*-- 승인번호 --*/
	char    mrspc       [LEN_MRSPC];    /*-- KCP응답코드 --*/
	char    hrspc       [LEN_HRSPC];    /*-- 국세청응답코드 --*/
	char    pem         [LEN_PEM];      /*-- 수기입력여부 --*/
	char    sd_flag     [LEN_SD_FLAG];  /*-- Batch 전송 flag --*/
	char    orderid     [LEN_ORDERID];
	char    mcht_name   [LEN_MCHT_NAME];
	char    prod_name   [LEN_PROD_NAME];
	char    cons_name   [LEN_CONS_NAME];
	char    cons_tel    [LEN_CONS_TEL];
	char    cons_email  [LEN_CONS_EMAIL];
	char    entdtime    [LEN_ENTDTIME];
	char    snddtime    [LEN_SNDDTIME];
	char    reqdtime    [LEN_REQDTIME];
	char    mtrsno      [LEN_MTRSNO];
	/*-- DataBase --*/
	char    resp_msg    [LEN_RESP_MSG];
	char    extend1     [LEN_EXTEND1];
	char    extend2     [LEN_EXTEND2];
	char    extend3     [LEN_EXTEND3];
	char    extend4     [LEN_EXTEND4];
} InitCashData, *lpInitCashData;

typedef struct _tagCashDataSel
{
	/*-- version 2.0 --*/
	char    sell_type   [LEN_SELL_TYPE];
	char    sell_name   [LEN_SELL_NAME];
	char    sell_addr   [LEN_SELL_ADDR];
	char    sell_tel    [LEN_SELL_TEL];
	char    opt_val     [LEN_OPT_VAL];
} CashDataSel, *lpCashDataSel;

typedef struct _tagCashDataVal
{
    char * midbykcp;    /*-- in --*/
    char * termid;      /*-- in --*/
    char * cashipaddress1;  /*-- in --*/
    char * cashportno1;     /*-- in --*/
    char * cashipaddress2;  /*-- in --*/
    char * cashportno2; /*-- in --*/
    char * tsdtime;     /*-- in --*/
    char * msg_type;    /*-- in/out --*/ 
    char * tr_code;     /*-- in/out--*/
    char * tax_no;      /*-- in/out--*/
    char * term_id;     /*-- out --*/
    char * id_info;     /*-- in --*/
    char * amt1;        /*-- in/out--*/
    char * amt2;        /*-- in/out--*/
    char * amt3;        /*-- in/out--*/
    char * amt4;        /*-- in/out--*/
    char * authno;      /*-- out --*/
    char * mrspc;       /*-- out --*/
    char * hrspc;       /*-- out --*/
    char * pem;         /*-- out --*/
    char * sd_flag;     /*-- out --*/
	char * orderid;     /*-- in/out --*/
	char * mcht_name;   /*-- in/out --*/
	char * prod_name;   /*-- in/out --*/
	char * cons_name;   /*-- in/out --*/
	char * cons_tel;    /*-- in/out --*/
	char * cons_email;  /*-- in/out --*/
	char * entdtime;    /*-- out --*/
	char * snddtime;    /*-- out --*/
	char * reqdtime;    /*-- in/out --*/
	char * mtrsno;      /*-- in/out --*/
	char * resp_msg;    /*-- out --*/
	char * error_msg;   /*-- out --*/
	char * extend1;     /*-- in/out --*/
	char * extend2;     /*-- in/out --*/
	char * extend3;     /*-- in/out --*/
	char * extend4;     /*-- in/out --*/
	/*-- version 2.0 --*/
	char * sell_type;   /*-- in/out --*/
	char * sell_name;   /*-- in/out --*/
	char * sell_addr;   /*-- in/out --*/
	char * sell_tel;    /*-- in/out --*/
	char * opt_val;     /*-- in/out --*/
} CashDataVal, *lpCashDataVal;

typedef struct _tagCashDataBuf
{
    char    tsdtime     [LEN_TSDTIME+1];
	char 	msg_type	[LEN_MSG_TYPE+1];
	char 	tr_code     [LEN_TR_CODE+1];
	char    tax_no      [LEN_TAX_NO+1];
	char    term_id     [LEN_TERM_ID+1];
	char    id_info     [LEN_ID_INFO+1];
	char    amt1        [LEN_AMT1+1];
	char    amt2        [LEN_AMT2+1];
	char    amt3        [LEN_AMT3+1];
	char    amt4        [LEN_AMT4+1];
	char 	authno		[LEN_AUTHNO+1];
	char    mrspc       [LEN_MRSPC+1];
	char    hrspc       [LEN_HRSPC+1];
	char    pem         [LEN_PEM+1];
	char    sd_flag     [LEN_SD_FLAG+1];
	char    orderid     [LEN_ORDERID+1];
	char    mcht_name   [LEN_MCHT_NAME+1];
	char    prod_name   [LEN_PROD_NAME+1];
	char    cons_name   [LEN_CONS_NAME+1];
	char    cons_tel    [LEN_CONS_TEL+1];
	char    cons_email  [LEN_CONS_EMAIL+1];
	char    entdtime    [LEN_ENTDTIME+1];
	char    snddtime    [LEN_SNDDTIME+1];
	char    reqdtime    [LEN_REQDTIME+1];
	char    mtrsno      [LEN_MTRSNO+1];
	char    resp_msg    [LEN_RESP_MSG+1];
	char    extend1     [LEN_EXTEND1+1];
	char    extend2     [LEN_EXTEND2+1];
	char    extend3     [LEN_EXTEND3+1];
	char    extend4     [LEN_EXTEND4+1];
	/*-- version 2.0 --*/
	char   sell_type    [LEN_SELL_TYPE+1];
	char   sell_name    [LEN_SELL_NAME+1];
	char   sell_addr    [LEN_SELL_ADDR+1];
	char   sell_tel     [LEN_SELL_TEL+1];
	char   opt_val      [LEN_OPT_VAL+1];	
} CashDataBuf, *lpCashDataBuf;

void InitializeCashData(lpCashDataVal val, lpCashDataBuf buf);
int RequestCashDataAuth(lpCashDataVal val, lpCashDataBuf buf);
int RequestCashDataVoid(lpCashDataVal val, lpCashDataBuf buf);
int RequestCashDataQury(lpCashDataVal val, lpCashDataBuf buf);

#endif

