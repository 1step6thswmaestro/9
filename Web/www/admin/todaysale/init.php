<?
$sql_table = "CREATE TABLE IF NOT EXISTS `todaysale` (
  `pridx` int(11) NOT NULL,
  `start` datetime NOT NULL,
  `end` datetime default NULL,
  `addquantity` int(11) default NULL,
  `salecnt` int(11) default NULL,
  PRIMARY KEY  (`pridx`)
) ENGINE=MyISAM DEFAULT CHARSET=euckr;";

$sql_code = "INSERT INTO `tblproductcode` (`codeA`, `codeB`, `codeC`, `codeD`, `type`, `code_name`, `list_type`, `detail_type`, `sequence`, `sort`, `group_code`, `estimate_set`, `noreserve`, `special`, `special_cnt`, `islist`, `title_type`, `title_body`, `mobile_display`, `isgift`, `iscoupon`, `isrefund`, `isreserve`) VALUES
('899', '000', '000', '000', 'X', '투데이세일_프로그램관리', 'AL001', 'AD001', NULL, 'date', '', 999, 'Y', '', '', 'Y', NULL, NULL, 'Y', 'N', 'N', 'N', 'N');";

$chk_code = "select * from tblproductcode where codeA='899' and codeB='000'  and codeC='000'  and codeD='000'";
$chk_table = "SHOW TABLES LIKE  'todaysale'";

if(false !== $res = mysql_query($chk_code,get_db_conn())){
	if(mysql_num_rows($res) < 1){
		mysql_query($sql_code,get_db_conn());
	}
	
	if(false !== $res = mysql_query($chk_table,get_db_conn())){
		if(mysql_num_rows($res) < 1){
			mysql_query($sql_table,get_db_conn());
		}
	}
}

?>