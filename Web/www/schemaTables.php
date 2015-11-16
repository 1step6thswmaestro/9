<?
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `attendance_items` (
	  `aidx` int(11) NOT NULL AUTO_INCREMENT COMMENT '������ȣ',
	  `title` varchar(255) NOT NULL COMMENT '�⼮�̺�Ʈ��',
	  `stdate` datetime NOT NULL COMMENT '������',
	  `enddate` datetime NOT NULL COMMENT '������',
	  `design` text COMMENT '������ ������',
	  `memo` varchar(255) DEFAULT NULL COMMENT '�����ڸ޸�',
	  PRIMARY KEY (`aidx`)
	) ENGINE=MyISAM  DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `attendance_reward` (
	  `aidx` int(11) NOT NULL,
	  `ridx` int(11) NOT NULL AUTO_INCREMENT,
	  `conse` enum('0','1') NOT NULL DEFAULT '0' COMMENT '���ӿ��� 1�̸� ����',
	  `ranges` tinyint(4) NOT NULL COMMENT '�Ⱓ ��',
	  `rewtype` varchar(10) NOT NULL COMMENT '���󱸺�',
	  `rewval` varchar(30) NOT NULL COMMENT '����',
	  `rewmax` int(11) NOT NULL DEFAULT '-1',
	  PRIMARY KEY (`aidx`,`ridx`),
	  KEY `aidx` (`aidx`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `attendance_reward_history` (
	  `memid` varbinary(20) NOT NULL,
	  `aidx` int(11) NOT NULL,
	  `ridx` int(11) NOT NULL,
	  `seq` int(11) NOT NULL AUTO_INCREMENT,
	  `rewtype` enum('reserve','gift','coupon') NOT NULL,
	  `rewval` int(11) NOT NULL,
	  `rewdate` datetime NOT NULL,
	  PRIMARY KEY (`memid`,`aidx`,`ridx`,`seq`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `attendance_stamp` (
	  `aidx` int(11) NOT NULL,
	  `seq` int(11) NOT NULL AUTO_INCREMENT,
	  `date` date NOT NULL,
	  `time` time NOT NULL,
	  `memid` varchar(50) NOT NULL,
	  `continuity` int(11) NOT NULL DEFAULT '1' COMMENT '���Ӽ�',
	  `ment` varchar(255) DEFAULT NULL,
	  `ip` int(11) NOT NULL,
	  PRIMARY KEY (`seq`),
	  UNIQUE KEY `aidx` (`aidx`,`memid`,`date`,`continuity`)
	) ENGINE=MyISAM  DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `autogroup_log` (
	  `idx` int(10) unsigned NOT NULL AUTO_INCREMENT,
	  `beforeclass` varchar(6) NOT NULL,
	  `currentclass` varchar(6) NOT NULL,
	  `id` varchar(25) NOT NULL COMMENT '�����̵�',
	  `classstate` char(1) NOT NULL COMMENT '��޺������ up : U, down : D',
	  `changedate` datetime NOT NULL,
	  PRIMARY KEY (`idx`),
	  KEY `currentclass` (`currentclass`,`id`,`changedate`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `bill_basic` (
	  `bill_idx` int(11) NOT NULL AUTO_INCREMENT,
	  `ordercode` varchar(25) NOT NULL,
	  `memid` varchar(30) DEFAULT NULL COMMENT 'ȸ��ID',
	  `d_type` char(1) NOT NULL DEFAULT 'A' COMMENT 'Ÿ��',
	  `kind` char(1) NOT NULL DEFAULT 'A' COMMENT '���� ����',
	  `sendtype` char(1) NOT NULL DEFAULT 'S' COMMENT '���Ը��ⱸ��',
	  `detail_together_tax` char(1) DEFAULT '1' COMMENT '�ŷ����� �߼� �� ���ݰ�꼭 ���� �߼� ����(�ŷ������� �߼��� ���� �ּ�ó���ϼ���.)',
	  `c_name` varchar(30) NOT NULL COMMENT '����ڸ�',
	  `c_email` varchar(255) NOT NULL COMMENT '����� ����',
	  `c_cell` varchar(20) DEFAULT NULL COMMENT '����� �����',
	  `c_phone` varchar(20) DEFAULT NULL COMMENT '����� ��ȭ',
	  `memo` text,
	  `book_no` varchar(7) NOT NULL COMMENT 'å��ȣ X�� Xȣ(xxx-xxx)',
	  `serial` varchar(7) NOT NULL COMMENT '�Ϸù�ȣ (xxx-xxx)',
	  `regdate` datetime NOT NULL,
	  `senddate` datetime DEFAULT NULL,
	  `status` char(1) NOT NULL DEFAULT 'R',
	  PRIMARY KEY (`bill_idx`),
	  UNIQUE KEY `ordercode` (`ordercode`),
	  KEY `book_no` (`book_no`,`serial`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr COMMENT='���ڼ��ݰ�꼭 �߱ް��� ����';
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `bill_company` (
	  `bill_idx` int(11) NOT NULL,
	  `r_number` varchar(12) NOT NULL COMMENT '����� ��� ��ȣ',
	  `r_tnumber` varchar(10) DEFAULT NULL COMMENT '��������ȣ',
	  `r_name` varchar(100) NOT NULL COMMENT '��ȣ',
	  `r_master` varchar(50) NOT NULL COMMENT '��ǥ��',
	  `r_address` varchar(255) NOT NULL COMMENT '�ּ�',
	  `r_condition` varchar(30) NOT NULL COMMENT '����',
	  `r_item` varchar(30) NOT NULL COMMENT '����',
	  PRIMARY KEY (`bill_idx`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr COMMENT='���ڼ��ݰ�꼭 �߱ް��� ����';
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `bill_document` (
	  `bill_idx` int(11) NOT NULL,
	  `document_id` varchar(40) DEFAULT NULL,
	  `issue_date` date DEFAULT NULL COMMENT '������',
	  `supplyprice` int(11) NOT NULL COMMENT '���ް���',
	  `tax` int(11) NOT NULL,
	  `p_type` enum('R','S') NOT NULL DEFAULT 'R' COMMENT 'R:����, S: û��',
	  `remark` varchar(255) DEFAULT NULL COMMENT '���',
	  `money` int(11) DEFAULT NULL COMMENT '���� ( ����Ѱ�)',
	  `moneycheck` int(11) DEFAULT NULL COMMENT '��ǥ(����Ѱ�)',
	  `bill` int(11) DEFAULT NULL COMMENT '����(����Ѱ�)',
	  `uncollect` int(11) DEFAULT NULL COMMENT '�ܻ�̼���(����Ѱ�)',
	  PRIMARY KEY (`bill_idx`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `bill_document_items` (
	  `bill_idx` int(11) NOT NULL,
	  `itm_seq` int(11) NOT NULL AUTO_INCREMENT,
	  `mm` tinyint(4) NOT NULL,
	  `dd` tinyint(4) NOT NULL,
	  `subject` varchar(50) NOT NULL,
	  `count` int(11) NOT NULL,
	  `oneprice` int(11) NOT NULL,
	  `price` int(11) NOT NULL,
	  `tax_row` int(11) NOT NULL,
	  `etc` varchar(255) DEFAULT NULL,
	  `sum` int(11) NOT NULL,
	  PRIMARY KEY (`bill_idx`,`itm_seq`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `bill_log` (
	  `bill_idx` int(11) NOT NULL,
	  `code` varchar(10) DEFAULT NULL,
	  `msg` varchar(255) DEFAULT NULL,
	  `rdate` datetime DEFAULT NULL,
	  KEY `bill_idx` (`bill_idx`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `bulkmail_group` (
	  `gidx` int(11) NOT NULL AUTO_INCREMENT,
	  `gname` varchar(100) NOT NULL,
	  `gmembers` int(11) NOT NULL DEFAULT '0',
	  `memo` varchar(255) DEFAULT NULL,
	  `detail` text,
	  PRIMARY KEY (`gidx`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `bulkmail_templet` (
	  `tpidx` int(11) NOT NULL AUTO_INCREMENT,
	  `title` varchar(100) NOT NULL,
	  `mailContents` text NOT NULL,
	  `regdate` datetime NOT NULL,
	  PRIMARY KEY (`tpidx`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `card_orderinfo_tax` (
	  `ordercode` varchar(25) NOT NULL COMMENT '�ֹ��ڵ�',
	  `amount` int(11) NOT NULL COMMENT '�����ݾ�',
	  `tax_mny` int(11) NOT NULL COMMENT '���ް�',
	  `vat_mny` int(11) NOT NULL COMMENT '�ΰ���',
	  `free_mny` int(11) NOT NULL COMMENT '�����',
	  `reg_date` datetime NOT NULL COMMENT '�����'
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `card_part_cancel_log` (
	  `ordercode` varchar(25) NOT NULL COMMENT '�ֹ��ڵ�',
	  `uid` int(11) NOT NULL COMMENT '�κ���� ��ǰ�ڵ�',
	  `org_price` int(11) NOT NULL COMMENT '�����ݾ�',
	  `cancel_price` int(11) NOT NULL COMMENT '��ұݾ�',
	  `cancel_tax` int(11) NOT NULL DEFAULT '0' COMMENT '��� ���ް�',
	  `cancel_vat` int(11) NOT NULL DEFAULT '0' COMMENT '��� �ΰ���',
	  `cancel_free` int(11) NOT NULL DEFAULT '0' COMMENT '��� �����',
	  `remain_price` int(11) NOT NULL COMMENT '�����ݾ�',
	  `remain_tax` int(11) NOT NULL DEFAULT '0' COMMENT '���� ���ް�',
	  `remain_vat` int(11) NOT NULL DEFAULT '0' COMMENT '���� �ΰ���',
	  `remain_free` int(11) NOT NULL DEFAULT '0' COMMENT '���� �����',
	  `pay_data` varchar(50) NOT NULL COMMENT '���ι�ȣ',
	  `msg` varchar(200) NOT NULL COMMENT '�����޽���',
	  `ip` varchar(15) NOT NULL COMMENT 'ip',
	  `reg_date` datetime NOT NULL COMMENT 'ó����¥',
	  PRIMARY KEY (`uid`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `card_part_cancel_tax_free` (
	  `ordercode` varchar(25) NOT NULL COMMENT '�ֹ��ڵ�',
	  `cancel_uid` int(11) NOT NULL COMMENT '��Ұ��� ������',
	  `amount` int(11) NOT NULL DEFAULT '0' COMMENT '��ü��',
	  `tax_mny` int(11) NOT NULL DEFAULT '0' COMMENT '���ް�',
	  `vat_mny` int(11) NOT NULL DEFAULT '0' COMMENT '�ΰ���',
	  `free_mny` int(11) NOT NULL DEFAULT '0' COMMENT '�����',
	  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'ó������ 0 ��û 1 ó��',
	  `reg_date` datetime NOT NULL COMMENT '�����'
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `commission_history` (
	  `seq` int(11) NOT NULL AUTO_INCREMENT COMMENT '������',
	  `vender` int(11) NOT NULL COMMENT '������ü ������',
	  `productcode` varchar(18) DEFAULT NULL COMMENT '��ǰ�ڵ�',
	  `reg_date` datetime NOT NULL COMMENT '�����',
	  `memo` varchar(500) DEFAULT NULL COMMENT '��������',
	  `rq_name` varchar(20) DEFAULT NULL COMMENT '��û�� �̸�',
	  `type` tinyint(4) NOT NULL COMMENT '���� 1:��ü, 2:��ǰ',
	  `admin_id` varchar(20) NOT NULL COMMENT '������ ���̵�',
	  PRIMARY KEY (`seq`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `extra_conf` (
	  `type` varchar(50) NOT NULL,
	  `name` varchar(50) NOT NULL,
	  `value` varchar(255) DEFAULT NULL,
	  UNIQUE KEY `type` (`type`,`name`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `group_coupon` (
	  `group_code` varchar(4) NOT NULL,
	  `coupon_code` varchar(10) NOT NULL,
	  PRIMARY KEY (`group_code`,`coupon_code`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `order_account_new` (
	  `vender` int(11) NOT NULL COMMENT '������ü ������',
	  `date` varchar(8) NOT NULL COMMENT '����',
	  `price` int(11) NOT NULL COMMENT '�ݾ�',
	  `confirm` char(1) NOT NULL DEFAULT 'N' COMMENT '���꿩��',
	  `bank_account` varchar(100) DEFAULT NULL COMMENT '��������',
	  `memo` text COMMENT '�޸�',
	  `reg_date` datetime DEFAULT NULL COMMENT '�����',
	  `start_date` varchar(8) DEFAULT NULL COMMENT '���������',
	  `end_date` varchar(8) DEFAULT NULL COMMENT '���� ����',
	  PRIMARY KEY (`vender`,`date`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `order_adjust_detail` (
	  `ordercode` varchar(25) NOT NULL COMMENT '�ֹ��ڵ�',
	  `productcode` varchar(18) NOT NULL COMMENT ' ��ǰ�ڵ�',
	  `vender` int(11) NOT NULL COMMENT '������ü ������',
	  `deli_date` varchar(14) NOT NULL COMMENT '���԰�����',
	  `price` int(11) NOT NULL COMMENT '����',
	  `deli_price` int(11) DEFAULT '0' COMMENT '��ۺ�',
	  `reserve` int(11) DEFAULT '0' COMMENT '������',
	  `cou_price` int(11) DEFAULT '0' COMMENT '����',
	  `account_rule` tinyint(4) NOT NULL DEFAULT '0' COMMENT '������� 0:�Ǹ� ������, 1:��ǰ�� ���ް�',
	  `commission_type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '������ ����� 0:��ü 1:��ǰ��',
	  `relay` tinyint(4) NOT NULL DEFAULT '0' COMMENT '�߰��ü ���� 0:�ƴ� 1:�߰��ü',
	  `rate` int(11) DEFAULT '0' COMMENT '������',
	  `cost` int(11) DEFAULT '0' COMMENT '���ް�',
	  `rate_price` int(11) NOT NULL DEFAULT '0' COMMENT '������(ȯ�갪)',
	  `surtax` int(11) NOT NULL DEFAULT '0' COMMENT '�������Ǻΰ���(�߰��ü�Ͻ�)',
	  `adjust` int(11) NOT NULL DEFAULT '0' COMMENT '�����',
	  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '���� 1:�������� 2:���������� 3:����Ϸ� 4���',
	  `com_date` varchar(8) DEFAULT NULL COMMENT 'ó�� ��¥',
	  `uid` int(11) NOT NULL COMMENT 'tblorderproduct�� uid',
	  KEY `vender` (`vender`),
	  KEY `ordercode` (`ordercode`),
	  KEY `deli_date` (`deli_date`),
	  KEY `status` (`status`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `order_adjust_update_history` (
	  `seq` int(11) NOT NULL AUTO_INCREMENT COMMENT '������',
	  `ordercode` varchar(25) NOT NULL COMMENT '�ֹ��ڵ�',
	  `vender` int(11) NOT NULL COMMENT '������ü ������',
	  `move_adjust` int(11) NOT NULL COMMENT '���氡��',
	  `old_adjust` int(11) NOT NULL COMMENT '���Ű�',
	  `result_adjust` int(11) NOT NULL COMMENT '������',
	  `memo` text NOT NULL COMMENT '�޸�',
	  `reg_date` datetime NOT NULL COMMENT '��α뤩',
	  PRIMARY KEY (`seq`)
	) ENGINE=MyISAM  DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `order_refund_account` (
	  `ordercode` varchar(25) NOT NULL COMMENT '�ֹ���ȣ',
	  `bank` varchar(20) NOT NULL COMMENT '����',
	  `account_name` varchar(20) NOT NULL COMMENT '���� ��',
	  `account_num` varchar(50) NOT NULL COMMENT '���¹�ȣ',
	  `reg_date` datetime NOT NULL COMMENT '�����',
	  PRIMARY KEY (`ordercode`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `part_cancel_reserve` (
	  `ordercode` varchar(25) NOT NULL COMMENT '�ֹ��ڵ�',
	  `cancel_reserve` int(11) NOT NULL COMMENT '��� ������',
	  `org_reserve` int(11) NOT NULL COMMENT '����� ������',
	  `remain_reserve` int(11) NOT NULL COMMENT '����� ������',
	  `memo` varchar(500) NOT NULL COMMENT '�޸�',
	  `reg_date` datetime NOT NULL COMMENT '�����'
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `part_cancel_want` (
	  `uid` int(11) NOT NULL COMMENT '�� �ֹ� �ڵ�',
	  `requestor` tinyint(4) NOT NULL DEFAULT '0' COMMENT '��û�� 0:������ 1:��',
	  `reg_date` datetime NOT NULL COMMENT '�����',
	  PRIMARY KEY (`uid`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `personalboard_admin` (
	  `type` varchar(50) NOT NULL,
	  `smsused` char(1) NOT NULL DEFAULT 'N',
	  `leavenumber` text
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `product_code_banner` (
	  `code` char(3) NOT NULL COMMENT 'ī�װ��ڵ�(1��)',
	  `banner_file` varchar(50) DEFAULT NULL COMMENT '��� ���ϸ�',
	  `banner_url` varchar(200) DEFAULT NULL COMMENT '��ũ �ּ�',
	  `move_type` tinyint(4) DEFAULT '0' COMMENT '�̵�Ÿ�� 0 ��â 1 ��â',
	  UNIQUE KEY `code` (`code`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `product_commission` (
	  `productcode` varchar(18) NOT NULL COMMENT '��ǰ�ڵ�',
	  `rq_com` tinyint(4) DEFAULT '0' COMMENT '��û ������',
	  `cf_com` tinyint(4) DEFAULT '0' COMMENT 'Ȯ�� ������',
	  `rq_cost` int(11) DEFAULT '0' COMMENT '��û ���ް�',
	  `cf_cost` int(11) DEFAULT '0' COMMENT 'Ȯ�� ���ް�',
	  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '����. 1:��û 2:Ȯ��',
	  `first_approval` tinyint(4) NOT NULL DEFAULT '0' COMMENT '���� ���ο��� 0:����(�ǸźҰ�) 1:����',
	  `rq_date` datetime DEFAULT NULL COMMENT '������û��',
	  `update` datetime DEFAULT NULL,
	  KEY `productcode` (`productcode`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `product_commission_history` (
	  `seq` int(11) NOT NULL AUTO_INCREMENT COMMENT '������',
	  `productcode` varchar(18) NOT NULL COMMENT '��ǰ�ڵ�',
	  `reg_date` datetime NOT NULL COMMENT '�����',
	  `memo` varchar(500) DEFAULT NULL COMMENT '��������',
	  `type` tinyint(4) NOT NULL COMMENT '���� 1:��ü 2:������',
	  `admin_id` varchar(20) DEFAULT NULL COMMENT '������ ���̵�',
	  `vender` int(11) DEFAULT NULL COMMENT '������ü',
	  PRIMARY KEY (`seq`)
	) ENGINE=MyISAM  DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `promotion_reserve_log` (
	  `idx` int(11) NOT NULL AUTO_INCREMENT,
	  `productcode` varchar(20) NOT NULL,
	  `promotionid` varchar(100) NOT NULL,
	  `promotiontype` char(2) NOT NULL,
	  `saveamount` int(11) NOT NULL,
	  `regdate` datetime NOT NULL,
	  PRIMARY KEY (`idx`)
	) ENGINE=MyISAM  DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `scheduled_delivery_address` (
	  `id` varchar(20) NOT NULL,
	  `seq` int(11) NOT NULL AUTO_INCREMENT,
	  `addressname` varchar(100) NOT NULL,
	  `zip` varchar(7) NOT NULL,
	  `addr1` varchar(200) NOT NULL,
	  `addr2` varchar(200) NOT NULL,
	  PRIMARY KEY (`id`,`seq`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `scheduled_delivery_order` (
	  `soidx` int(11) NOT NULL AUTO_INCREMENT,
	  `id` varchar(20) NOT NULL,
	  `period` tinyint(4) NOT NULL COMMENT '��� �Ⱓ',
	  `periodtype` varchar(10) NOT NULL DEFAULT 'month',
	  `bperiod` tinyint(4) NOT NULL DEFAULT '0' COMMENT '���ʽ� �߼� �Ⱓ',
	  `start` date NOT NULL,
	  `dseq` int(11) NOT NULL COMMENT '����� ������ȣ',
	  `zip` varchar(7) NOT NULL,
	  `addr1` varchar(255) NOT NULL,
	  `addr2` varchar(255) NOT NULL,
	  `sumprice` int(11) NOT NULL,
	  `price` int(11) NOT NULL,
	  `dc_coupon` int(11) NOT NULL,
	  `usereserve` int(11) NOT NULL DEFAULT '0',
	  `reserve` int(11) NOT NULL,
	  `paymethod` char(2) DEFAULT NULL,
	  `paystatus` enum('0','1','-1','-2') NOT NULL DEFAULT '0' COMMENT '0:�������,1:��������,-1:���� ���� ����,-2 �������� ��Ұ�',
	  `paydate` datetime DEFAULT NULL,
	  `payinfo` varchar(255) DEFAULT NULL,
	  `payauth` varchar(20) DEFAULT NULL COMMENT '���� ���� �Ǵ� �Ա���',
	  `payflag` varchar(10) DEFAULT NULL,
	  `orderstatus` enum('B','N','S','D','C','E') NOT NULL DEFAULT 'B' COMMENT 'B:�屸��,N: ��ó��,S:����غ�,D:�����,C:���,E:�Ϸ�',
	  `extrastatus` varchar(20) DEFAULT NULL COMMENT '��ҵ� �߰� ���� ����',
	  `deliveryCnt` tinyint(4) NOT NULL DEFAULT '-1' COMMENT '��۵� Ƚ��( �����Ͽ� ��ϵǸ� 0 �ƴϸ� -1',
	  `deliprice` int(11) NOT NULL DEFAULT '0' COMMENT '��ۺ�',
	  `receiver_name` varchar(20) DEFAULT NULL,
	  `receiver_mobile` varchar(20) DEFAULT NULL,
	  `receiver_tel` varchar(20) DEFAULT NULL,
	  `order_msg` varchar(255) DEFAULT NULL,
	  `baskettime` datetime NOT NULL,
	  `ordertime` datetime DEFAULT NULL,
	  PRIMARY KEY (`soidx`),
	  KEY `id` (`id`,`orderstatus`,`baskettime`),
	  KEY `orderstatus` (`orderstatus`,`baskettime`)
	) ENGINE=MyISAM  DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `scheduled_delivery_order_items` (
	  `soidx` int(11) NOT NULL,
	  `itemseq` int(11) NOT NULL AUTO_INCREMENT,
	  `pridx` int(11) NOT NULL,
	  `productname` varchar(255) NOT NULL,
	  `sellprice` int(11) NOT NULL,
	  `quantity` mediumint(9) NOT NULL,
	  `sumprice` int(11) NOT NULL,
	  `reserve` int(11) NOT NULL DEFAULT '0',
	  `deli_price` int(11) NOT NULL DEFAULT '0',
	  `dc_coupon` int(11) NOT NULL DEFAULT '0',
	  `couponinfo` varchar(255) DEFAULT NULL,
	  PRIMARY KEY (`soidx`,`itemseq`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `scheduled_delivery_schedule` (
	  `soidx` int(11) NOT NULL,
	  `itemseq` int(11) NOT NULL,
	  `drseq` tinyint(4) NOT NULL AUTO_INCREMENT,
	  `deliverydate` date NOT NULL COMMENT '��ۿ�����',
	  `deliveryeddate` date DEFAULT NULL COMMENT '�����',
	  `deliverystatus` enum('0','1') NOT NULL DEFAULT '0' COMMENT '��ۻ���:0>���,1>��ۿϷ�',
	  PRIMARY KEY (`soidx`,`itemseq`,`drseq`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `sci_ipin_log` (
	  `reqNum` varchar(30) NOT NULL,
	  `vDiscrNo` varchar(13) NOT NULL,
	  `name` varchar(20) NOT NULL,
	  `result` varchar(1) NOT NULL,
	  `age` varchar(1) DEFAULT NULL,
	  `sex` enum('M','F') DEFAULT NULL,
	  `ip` varchar(15) DEFAULT NULL,
	  `authInfo` varchar(2) DEFAULT NULL,
	  `birth` varchar(8) DEFAULT NULL,
	  `fgn` varchar(1) DEFAULT NULL,
	  `discrHash` varchar(64) NOT NULL,
	  `ciVersion` varchar(1) DEFAULT NULL,
	  `ciscrHash` varchar(88) DEFAULT NULL,
	  PRIMARY KEY (`reqNum`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr COMMENT='Ipin ���� �α�_�÷� ������ ���� ���� ����';
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `sci_pcc_log` (
	  `name` varchar(40) NOT NULL,
	  `birYMD` varchar(8) NOT NULL,
	  `sex` enum('M','F') NOT NULL,
	  `fgnGbn` enum('1','2') NOT NULL,
	  `di` varchar(64) DEFAULT NULL,
	  `ci1` varchar(88) DEFAULT NULL,
	  `ci2` varchar(88) DEFAULT NULL,
	  `civersion` varchar(1) DEFAULT NULL,
	  `reqNum` varchar(40) NOT NULL,
	  `result` enum('Y','N','F') NOT NULL,
	  `certGb` char(1) NOT NULL,
	  `cellNo` varchar(11) DEFAULT NULL,
	  `cellCorp` varchar(3) DEFAULT NULL,
	  `certDate` varchar(14) NOT NULL,
	  `addVar` varchar(255) DEFAULT NULL,
	  PRIMARY KEY (`reqNum`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr COMMENT='���������α�_�ʵ�°��߹��� ����';
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `shop_more_info` (
	  `function_use` tinyint(4) DEFAULT '1' COMMENT '������� ��� ����',
	  `nametech_use` tinyint(4) NOT NULL DEFAULT '1' COMMENT '������ ���',
	  `account_rule` tinyint(4) DEFAULT '0' COMMENT '������ؼ��� 0:�Ǹż�����, 1:��ǰ�� ���ް�',
	  `reserve_use` tinyint(4) DEFAULT '0' COMMENT '������ ��뿩��',
	  `coupon_use` tinyint(4) DEFAULT '0' COMMENT '���� ��뿩��',
	  `info_view` tinyint(4) DEFAULT '1' COMMENT '������ü ���� ���� ����',
	  `relay` tinyint(4) DEFAULT '0' COMMENT '�߰� ��ü ����'
	) ENGINE=MyISAM DEFAULT CHARSET=euckr COMMENT='������⺻����';
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblaffiliatebanner` (
	  `num` int(11) unsigned NOT NULL AUTO_INCREMENT,
	  `used` char(1) NOT NULL DEFAULT 'N',
	  `reg_date` varchar(14) NOT NULL DEFAULT '',
	  `title` varchar(100) NOT NULL DEFAULT '',
	  `content` text NOT NULL,
	  PRIMARY KEY (`num`),
	  KEY `idx_tblaffiliatebanner_1` (`used`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblassembleproduct` (
	  `productcode` varchar(18) NOT NULL DEFAULT '',
	  `assemble_type` varchar(100) NOT NULL DEFAULT '',
	  `assemble_title` text NOT NULL,
	  `assemble_pridx` text NOT NULL,
	  `assemble_list` text,
	  PRIMARY KEY (`productcode`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblauctioninfo` (
	  `auction_seq` int(11) unsigned NOT NULL DEFAULT '1',
	  `start_date` varchar(14) NOT NULL DEFAULT '',
	  `end_date` varchar(14) NOT NULL DEFAULT '',
	  `auction_name` varchar(100) NOT NULL DEFAULT '',
	  `start_price` int(11) unsigned NOT NULL DEFAULT '0',
	  `last_price` int(11) unsigned NOT NULL DEFAULT '0',
	  `mini_unit` int(11) unsigned NOT NULL DEFAULT '0',
	  `quantity` tinyint(4) unsigned NOT NULL DEFAULT '1',
	  `deli_area` varchar(30) NOT NULL DEFAULT '',
	  `used_period` varchar(30) NOT NULL DEFAULT '',
	  `bid_cnt` smallint(5) unsigned NOT NULL DEFAULT '0',
	  `access` int(11) unsigned NOT NULL DEFAULT '0',
	  `product_image` varchar(30) NOT NULL DEFAULT '',
	  `content` mediumtext NOT NULL,
	  PRIMARY KEY (`auction_seq`,`start_date`),
	  KEY `idx_tblauctioninfo_1` (`end_date`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblauctionresult` (
	  `auction_seq` int(11) unsigned NOT NULL DEFAULT '1',
	  `start_date` varchar(14) NOT NULL DEFAULT '',
	  `id` varchar(20) NOT NULL DEFAULT '',
	  `quantity` tinyint(4) unsigned NOT NULL DEFAULT '1',
	  `price` int(11) unsigned NOT NULL DEFAULT '0',
	  `date` varchar(14) NOT NULL DEFAULT '',
	  `content` varchar(250) NOT NULL DEFAULT '',
	  PRIMARY KEY (`auction_seq`,`start_date`,`id`,`date`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblbankinfo` (
	  `uid` int(11) unsigned NOT NULL AUTO_INCREMENT,
	  `id` varchar(80) NOT NULL DEFAULT '',
	  `name` varchar(80) NOT NULL DEFAULT '',
	  `bank_name` varchar(80) NOT NULL DEFAULT '',
	  `bank_num` varchar(80) NOT NULL DEFAULT '',
	  `signdate` int(10) unsigned NOT NULL DEFAULT '0',
	  PRIMARY KEY (`uid`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblbanner` (
	  `date` varchar(14) NOT NULL DEFAULT '',
	  `image` varchar(100) DEFAULT NULL,
	  `border` char(1) NOT NULL DEFAULT '1',
	  `url_type` char(1) NOT NULL DEFAULT 'H',
	  `url` varchar(200) NOT NULL DEFAULT '',
	  `target` varchar(8) NOT NULL DEFAULT '_blank',
	  PRIMARY KEY (`date`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblbasket` (
	  `tempkey` varchar(32) NOT NULL DEFAULT '',
	  `productcode` varchar(18) NOT NULL DEFAULT '',
	  `opt1_idx` tinyint(3) NOT NULL DEFAULT '0',
	  `opt2_idx` tinyint(4) NOT NULL DEFAULT '0',
	  `optidxs` varchar(32) NOT NULL DEFAULT '0',
	  `assemble_idx` tinyint(3) unsigned NOT NULL DEFAULT '0',
	  `package_idx` smallint(5) unsigned NOT NULL DEFAULT '0',
	  `assemble_list` text NOT NULL,
	  `quantity` smallint(5) NOT NULL DEFAULT '1',
	  `date` varchar(14) NOT NULL DEFAULT '',
	  `basketidx` int(11) unsigned NOT NULL AUTO_INCREMENT,
	  `sell_memid` varchar(20) NOT NULL DEFAULT '',
	  `ordertype` varchar(20) NOT NULL,
	  PRIMARY KEY (`tempkey`,`productcode`,`opt1_idx`,`opt2_idx`,`optidxs`,`package_idx`,`assemble_idx`),
	  UNIQUE KEY `idx_tblbasket_3` (`basketidx`),
	  KEY `idx_tblbasket_1` (`productcode`),
	  KEY `idx_tblbasket_2` (`date`)
	) ENGINE=MyISAM  DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblbasket2` (
	  `tempkey` varchar(32) NOT NULL DEFAULT '',
	  `productcode` varchar(18) NOT NULL DEFAULT '',
	  `opt1_idx` tinyint(3) NOT NULL DEFAULT '0',
	  `opt2_idx` tinyint(4) NOT NULL DEFAULT '0',
	  `optidxs` varchar(32) NOT NULL DEFAULT '0',
	  `assemble_idx` tinyint(3) unsigned NOT NULL DEFAULT '0',
	  `package_idx` smallint(5) unsigned NOT NULL DEFAULT '0',
	  `assemble_list` text NOT NULL,
	  `quantity` smallint(5) NOT NULL DEFAULT '1',
	  `date` varchar(14) NOT NULL DEFAULT '',
	  `basketidx` int(11) unsigned NOT NULL AUTO_INCREMENT,
	  `gift` int(1) NOT NULL DEFAULT '0',
	  PRIMARY KEY (`tempkey`,`productcode`,`opt1_idx`,`opt2_idx`,`optidxs`,`package_idx`,`assemble_idx`),
	  UNIQUE KEY `idx_tblbasket_3` (`basketidx`),
	  KEY `idx_tblbasket_1` (`productcode`),
	  KEY `idx_tblbasket_2` (`date`)
	) ENGINE=MyISAM  DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblbasket3` (
	  `tempkey` varchar(32) NOT NULL DEFAULT '',
	  `productcode` varchar(18) NOT NULL DEFAULT '',
	  `opt1_idx` tinyint(3) NOT NULL DEFAULT '0',
	  `opt2_idx` tinyint(4) NOT NULL DEFAULT '0',
	  `optidxs` varchar(32) NOT NULL DEFAULT '0',
	  `assemble_idx` tinyint(3) unsigned NOT NULL DEFAULT '0',
	  `package_idx` smallint(5) unsigned NOT NULL DEFAULT '0',
	  `assemble_list` text NOT NULL,
	  `quantity` smallint(5) NOT NULL DEFAULT '1',
	  `date` varchar(14) NOT NULL DEFAULT '',
	  `basketidx` int(11) unsigned NOT NULL AUTO_INCREMENT,
	  `sell_memid` varchar(20) NOT NULL DEFAULT '',
	  PRIMARY KEY (`tempkey`,`productcode`,`opt1_idx`,`opt2_idx`,`optidxs`,`package_idx`,`assemble_idx`),
	  UNIQUE KEY `idx_tblbasket_3` (`basketidx`),
	  KEY `idx_tblbasket_1` (`productcode`),
	  KEY `idx_tblbasket_2` (`date`)
	) ENGINE=MyISAM  DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblbasket4` (
	  `tempkey` varchar(32) NOT NULL DEFAULT '',
	  `productcode` varchar(18) NOT NULL DEFAULT '',
	  `opt1_idx` tinyint(3) NOT NULL DEFAULT '0',
	  `opt2_idx` tinyint(4) NOT NULL DEFAULT '0',
	  `optidxs` varchar(32) NOT NULL DEFAULT '0',
	  `assemble_idx` tinyint(3) unsigned NOT NULL DEFAULT '0',
	  `package_idx` smallint(5) unsigned NOT NULL DEFAULT '0',
	  `assemble_list` text NOT NULL,
	  `quantity` smallint(5) NOT NULL DEFAULT '1',
	  `date` varchar(14) NOT NULL DEFAULT '',
	  `basketidx` int(11) unsigned NOT NULL AUTO_INCREMENT,
	  `sell_memid` varchar(20) NOT NULL DEFAULT '',
	  PRIMARY KEY (`tempkey`,`productcode`,`opt1_idx`,`opt2_idx`,`optidxs`,`package_idx`,`assemble_idx`),
	  UNIQUE KEY `idx_tblbasket_3` (`basketidx`),
	  KEY `idx_tblbasket_1` (`productcode`),
	  KEY `idx_tblbasket_2` (`date`)
	) ENGINE=MyISAM  DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblbasket_ordernow` (
	  `tempkey` varchar(32) NOT NULL DEFAULT '',
	  `productcode` varchar(18) NOT NULL DEFAULT '',
	  `opt1_idx` tinyint(3) NOT NULL DEFAULT '0',
	  `opt2_idx` tinyint(4) NOT NULL DEFAULT '0',
	  `optidxs` varchar(32) NOT NULL DEFAULT '0',
	  `assemble_idx` tinyint(3) unsigned NOT NULL DEFAULT '0',
	  `package_idx` smallint(5) unsigned NOT NULL DEFAULT '0',
	  `assemble_list` text NOT NULL,
	  `quantity` smallint(5) NOT NULL DEFAULT '1',
	  `date` varchar(14) NOT NULL DEFAULT '',
	  `basketidx` int(11) unsigned NOT NULL AUTO_INCREMENT,
	  `sell_memid` varchar(20) NOT NULL DEFAULT '',
	  `ordertype` varchar(20) NOT NULL,
	  PRIMARY KEY (`tempkey`,`productcode`,`opt1_idx`,`opt2_idx`,`optidxs`,`package_idx`,`assemble_idx`),
	  UNIQUE KEY `idx_tblbasket_3` (`basketidx`),
	  KEY `idx_tblbasket_1` (`productcode`),
	  KEY `idx_tblbasket_2` (`date`)
	) ENGINE=MyISAM  DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblbasket_pester` (
	  `tempkey` varchar(32) NOT NULL DEFAULT '',
	  `productcode` varchar(18) NOT NULL DEFAULT '',
	  `opt1_idx` tinyint(3) NOT NULL DEFAULT '0',
	  `opt2_idx` tinyint(4) NOT NULL DEFAULT '0',
	  `optidxs` varchar(32) NOT NULL DEFAULT '0',
	  `assemble_idx` tinyint(3) unsigned NOT NULL DEFAULT '0',
	  `package_idx` smallint(5) unsigned NOT NULL DEFAULT '0',
	  `assemble_list` text NOT NULL,
	  `quantity` smallint(5) NOT NULL DEFAULT '1',
	  `date` varchar(14) NOT NULL DEFAULT '',
	  `basketidx` int(11) unsigned NOT NULL AUTO_INCREMENT,
	  `sell_memid` varchar(20) NOT NULL DEFAULT '',
	  `ordertype` varchar(20) NOT NULL,
	  PRIMARY KEY (`tempkey`,`productcode`,`opt1_idx`,`opt2_idx`,`optidxs`,`package_idx`,`assemble_idx`),
	  UNIQUE KEY `idx_tblbasket_3` (`basketidx`),
	  KEY `idx_tblbasket_1` (`productcode`),
	  KEY `idx_tblbasket_2` (`date`)
	) ENGINE=MyISAM  DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblbasket_pester_order` (
	  `tempkey` varchar(32) NOT NULL DEFAULT '',
	  `productcode` varchar(18) NOT NULL DEFAULT '',
	  `opt1_idx` tinyint(3) NOT NULL DEFAULT '0',
	  `opt2_idx` tinyint(4) NOT NULL DEFAULT '0',
	  `optidxs` varchar(32) NOT NULL DEFAULT '0',
	  `assemble_idx` tinyint(3) unsigned NOT NULL DEFAULT '0',
	  `package_idx` smallint(5) unsigned NOT NULL DEFAULT '0',
	  `assemble_list` text NOT NULL,
	  `quantity` smallint(5) NOT NULL DEFAULT '1',
	  `date` varchar(14) NOT NULL DEFAULT '',
	  `basketidx` int(11) unsigned NOT NULL AUTO_INCREMENT,
	  `sell_memid` varchar(20) NOT NULL DEFAULT '',
	  `ordertype` varchar(20) NOT NULL,
	  PRIMARY KEY (`tempkey`,`productcode`,`opt1_idx`,`opt2_idx`,`optidxs`,`package_idx`,`assemble_idx`),
	  UNIQUE KEY `idx_tblbasket_3` (`basketidx`),
	  KEY `idx_tblbasket_1` (`productcode`),
	  KEY `idx_tblbasket_2` (`date`)
	) ENGINE=MyISAM  DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblbasket_pester_save` (
	  `tempkey` varchar(32) NOT NULL DEFAULT '',
	  `productcode` varchar(18) NOT NULL DEFAULT '',
	  `opt1_idx` tinyint(3) NOT NULL DEFAULT '0',
	  `opt2_idx` tinyint(4) NOT NULL DEFAULT '0',
	  `optidxs` varchar(32) NOT NULL DEFAULT '0',
	  `assemble_idx` tinyint(3) unsigned NOT NULL DEFAULT '0',
	  `package_idx` smallint(5) unsigned NOT NULL DEFAULT '0',
	  `assemble_list` text NOT NULL,
	  `quantity` smallint(5) NOT NULL DEFAULT '1',
	  `date` varchar(14) NOT NULL DEFAULT '',
	  `basketidx` int(11) unsigned NOT NULL AUTO_INCREMENT,
	  `sell_memid` varchar(20) NOT NULL DEFAULT '',
	  `ordertype` varchar(20) NOT NULL,
	  PRIMARY KEY (`tempkey`,`productcode`,`opt1_idx`,`opt2_idx`,`optidxs`,`package_idx`,`assemble_idx`),
	  UNIQUE KEY `idx_tblbasket_3` (`basketidx`),
	  KEY `idx_tblbasket_1` (`productcode`),
	  KEY `idx_tblbasket_2` (`date`)
	) ENGINE=MyISAM  DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblbasket_present` (
	  `tempkey` varchar(32) NOT NULL DEFAULT '',
	  `productcode` varchar(18) NOT NULL DEFAULT '',
	  `opt1_idx` tinyint(3) NOT NULL DEFAULT '0',
	  `opt2_idx` tinyint(4) NOT NULL DEFAULT '0',
	  `optidxs` varchar(32) NOT NULL DEFAULT '0',
	  `assemble_idx` tinyint(3) unsigned NOT NULL DEFAULT '0',
	  `package_idx` smallint(5) unsigned NOT NULL DEFAULT '0',
	  `assemble_list` text NOT NULL,
	  `quantity` smallint(5) NOT NULL DEFAULT '1',
	  `date` varchar(14) NOT NULL DEFAULT '',
	  `basketidx` int(11) unsigned NOT NULL AUTO_INCREMENT,
	  `sell_memid` varchar(20) NOT NULL DEFAULT '',
	  `ordertype` varchar(20) NOT NULL,
	  PRIMARY KEY (`tempkey`,`productcode`,`opt1_idx`,`opt2_idx`,`optidxs`,`package_idx`,`assemble_idx`),
	  UNIQUE KEY `idx_tblbasket_3` (`basketidx`),
	  KEY `idx_tblbasket_1` (`productcode`),
	  KEY `idx_tblbasket_2` (`date`)
	) ENGINE=MyISAM  DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblboard` (
	  `board` varchar(30) NOT NULL DEFAULT '',
	  `subCategory` varchar(30) NOT NULL,
	  `num` int(11) unsigned NOT NULL AUTO_INCREMENT,
	  `thread` int(11) unsigned NOT NULL DEFAULT '0',
	  `pos` int(11) unsigned NOT NULL DEFAULT '0',
	  `depth` int(11) unsigned NOT NULL DEFAULT '0',
	  `prev_no` int(11) unsigned NOT NULL DEFAULT '0',
	  `next_no` int(11) unsigned NOT NULL DEFAULT '0',
	  `pridx` int(11) unsigned DEFAULT NULL,
	  `name` varchar(20) NOT NULL DEFAULT '',
	  `passwd` varchar(60) NOT NULL DEFAULT '',
	  `email` varchar(50) NOT NULL DEFAULT '',
	  `is_secret` char(1) NOT NULL DEFAULT '0',
	  `use_html` char(1) NOT NULL DEFAULT '0',
	  `title` varchar(200) NOT NULL DEFAULT '',
	  `filename` varchar(50) NOT NULL DEFAULT '',
	  `writetime` int(11) unsigned NOT NULL DEFAULT '0',
	  `ip` varchar(15) NOT NULL DEFAULT '',
	  `access` smallint(6) unsigned NOT NULL DEFAULT '0',
	  `total_comment` smallint(6) unsigned NOT NULL DEFAULT '0',
	  `content` mediumtext NOT NULL,
	  `notice` char(1) NOT NULL DEFAULT '0',
	  `deleted` char(1) NOT NULL DEFAULT '0',
	  `userid` varchar(20) NOT NULL DEFAULT '',
	  `usercel` varchar(20) NOT NULL DEFAULT '--',
	  `vote` int(11) NOT NULL,
	  `url` text NOT NULL,
	  PRIMARY KEY (`num`),
	  KEY `idx_tblboard_1` (`board`,`thread`,`pos`),
	  KEY `idx_tblboard_2` (`board`,`depth`),
	  KEY `idx_tblboard_3` (`board`,`prev_no`),
	  KEY `idx_tblboard_4` (`board`,`next_no`),
	  KEY `idx_tblboard_5` (`board`,`pridx`),
	  KEY `idx_tblboard_6` (`board`,`writetime`),
	  KEY `idx_tblboard_7` (`board`,`notice`),
	  KEY `idx_tblboard_8` (`board`,`deleted`)
	) ENGINE=MyISAM  DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblboardadmin` (
	  `board` varchar(50) NOT NULL DEFAULT '',
	  `subCategory` text NOT NULL,
	  `board_name` varchar(200) NOT NULL DEFAULT '',
	  `passwd` varchar(20) NOT NULL DEFAULT '',
	  `total_article` int(11) unsigned NOT NULL DEFAULT '0',
	  `thread_no` int(11) NOT NULL DEFAULT '1000000000',
	  `max_num` int(11) NOT NULL DEFAULT '0',
	  `board_skin` char(3) NOT NULL DEFAULT 'L01',
	  `back_image` varchar(100) NOT NULL DEFAULT '',
	  `title_color` varchar(6) NOT NULL DEFAULT '000000',
	  `board_width` smallint(5) unsigned NOT NULL DEFAULT '0',
	  `comment_width` smallint(5) unsigned NOT NULL DEFAULT '0',
	  `list_num` tinyint(3) unsigned NOT NULL DEFAULT '10',
	  `page_num` tinyint(3) unsigned NOT NULL DEFAULT '10',
	  `writer_gbn` char(1) NOT NULL DEFAULT '0',
	  `max_filesize` tinyint(3) unsigned NOT NULL DEFAULT '2',
	  `img_maxwidth` smallint(5) DEFAULT NULL,
	  `img_align` varchar(10) NOT NULL DEFAULT 'center',
	  `date` varchar(14) NOT NULL DEFAULT '',
	  `use_hidden` char(1) NOT NULL DEFAULT 'N',
	  `use_lock` char(1) NOT NULL DEFAULT 'N',
	  `use_reply` char(1) NOT NULL DEFAULT 'Y',
	  `use_comment` char(1) NOT NULL DEFAULT 'Y',
	  `use_comip` char(1) NOT NULL DEFAULT 'Y',
	  `use_hide_ip` char(1) NOT NULL DEFAULT 'Y',
	  `use_hide_email` char(1) NOT NULL DEFAULT 'Y',
	  `use_html` char(1) NOT NULL DEFAULT 'Y',
	  `use_imgresize` char(1) NOT NULL DEFAULT 'Y',
	  `use_wrap` char(1) NOT NULL DEFAULT 'N',
	  `use_hide_button` char(1) NOT NULL DEFAULT 'N',
	  `use_article_care` char(1) NOT NULL DEFAULT 'N',
	  `use_admin_mail` char(1) NOT NULL DEFAULT 'N',
	  `group_code` varchar(4) NOT NULL DEFAULT '',
	  `grant_write` char(1) NOT NULL DEFAULT 'N',
	  `grant_view` char(1) NOT NULL DEFAULT 'N',
	  `grant_reply` char(1) NOT NULL DEFAULT 'N',
	  `grant_comment` char(1) NOT NULL DEFAULT 'N',
	  `admin_name` varchar(20) DEFAULT NULL,
	  `datedisplay` char(1) DEFAULT 'Y',
	  `hitdisplay` char(1) DEFAULT 'Y',
	  `hitplus` char(1) DEFAULT 'N',
	  `newimg` char(1) DEFAULT '0',
	  `notice` varchar(250) DEFAULT NULL,
	  `admin_mail` varchar(250) DEFAULT NULL,
	  `filter` varchar(250) DEFAULT NULL,
	  `avoid_ip` text,
	  `reply_sms` char(1) NOT NULL DEFAULT 'N',
	  `use_admin_sms` char(1) NOT NULL DEFAULT 'N',
	  `admin_sms` varchar(255) DEFAULT NULL,
	  `sns_state` char(1) NOT NULL DEFAULT 'N',
	  `admin_icon` char(1) NOT NULL DEFAULT 'Y',
	  `secuCmt` char(1) NOT NULL DEFAULT 'N' COMMENT '��д��',
	  `fileYN` char(1) NOT NULL DEFAULT 'N' COMMENT '���� ���',
	  `onlyCmt` char(1) NOT NULL DEFAULT 'N' COMMENT '���� ���',
	  `linkboard` int(1) NOT NULL,
	  `grant_mobile` char(1) DEFAULT 'Y',
	  `list_customer` char(1) DEFAULT 'N',
	  `list_community` char(1) DEFAULT 'N',
	  `list_etc` char(1) DEFAULT 'N',
	  PRIMARY KEY (`board`),
	  KEY `idx_tblboardadmin_1` (`board`,`date`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblboardcomment` (
	  `board` varchar(30) NOT NULL DEFAULT '',
	  `parent` int(11) unsigned NOT NULL DEFAULT '0',
	  `num` int(11) unsigned NOT NULL AUTO_INCREMENT,
	  `name` varchar(20) NOT NULL DEFAULT '',
	  `passwd` varchar(20) NOT NULL DEFAULT '',
	  `ip` varchar(15) NOT NULL DEFAULT '',
	  `writetime` int(11) unsigned NOT NULL DEFAULT '0',
	  `comment` text,
	  `id` varchar(20) NOT NULL,
	  `sns_type` varchar(10) NOT NULL DEFAULT '',
	  `file` varchar(200) NOT NULL,
	  PRIMARY KEY (`board`,`parent`,`num`),
	  KEY `idx_tblboardcomment_1` (`board`,`parent`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblboardcomment_admin` (
	  `idx` int(11) NOT NULL AUTO_INCREMENT COMMENT '�ε���',
	  `board` varchar(30) NOT NULL COMMENT '�Խ���',
	  `board_no` int(11) NOT NULL COMMENT '�Խù�',
	  `comm_no` int(11) NOT NULL COMMENT '�ڸ�Ʈ',
	  `comment` text NOT NULL COMMENT '����',
	  `reg_date` datetime NOT NULL COMMENT '�ð�',
	  PRIMARY KEY (`idx`)
	) ENGINE=MyISAM  DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblboardskin` (
	  `board_skin` char(3) NOT NULL DEFAULT '',
	  `board_skinname` varchar(50) NOT NULL DEFAULT '',
	  PRIMARY KEY (`board_skin`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblboardSubCategory` (
	  `idx` int(11) NOT NULL AUTO_INCREMENT,
	  `board` varchar(30) NOT NULL,
	  `title` varchar(50) NOT NULL,
	  KEY `idx` (`idx`)
	) ENGINE=MyISAM  DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblcategorycode` (
	  `productcode` varchar(18) NOT NULL DEFAULT '',
	  `categorycode` varchar(12) NOT NULL DEFAULT '',
	  PRIMARY KEY (`productcode`,`categorycode`),
	  KEY `categorycode` (`categorycode`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblcollection` (
	  `productcode` varchar(18) NOT NULL DEFAULT '',
	  `collection_list` text,
	  PRIMARY KEY (`productcode`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblConnIP_block` (
	  `idx` int(11) NOT NULL AUTO_INCREMENT,
	  `IP` bigint(20) NOT NULL,
	  `msg` text NOT NULL,
	  PRIMARY KEY (`idx`),
	  KEY `idx` (`idx`)
	) ENGINE=MyISAM  DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblConnIP_list` (
	  `idx` bigint(20) NOT NULL AUTO_INCREMENT,
	  `IP` bigint(20) NOT NULL,
	  `conn` datetime NOT NULL,
	  `serverInfo` text NOT NULL,
	  PRIMARY KEY (`idx`),
	  KEY `idx` (`idx`)
	) ENGINE=MyISAM  DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblConnIP_memid` (
	  `idx` bigint(20) NOT NULL AUTO_INCREMENT,
	  `IP` bigint(20) NOT NULL,
	  `memid` varchar(50) NOT NULL,
	  `conn` datetime NOT NULL,
	  PRIMARY KEY (`idx`)
	) ENGINE=MyISAM  DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblConnIP_statistics` (
	  `idx` bigint(20) NOT NULL AUTO_INCREMENT,
	  `IP` bigint(20) NOT NULL,
	  `count` int(11) NOT NULL DEFAULT '1',
	  PRIMARY KEY (`idx`),
	  UNIQUE KEY `IP` (`IP`)
	) ENGINE=MyISAM  DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblcontentinfo` (
	  `date` varchar(14) NOT NULL DEFAULT '',
	  `subject` varchar(200) NOT NULL DEFAULT '',
	  `image_name` varchar(50) DEFAULT NULL,
	  `image_align` varchar(10) DEFAULT NULL,
	  `access` int(11) NOT NULL DEFAULT '0',
	  `content` mediumtext NOT NULL,
	  PRIMARY KEY (`date`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblcounter` (
	  `date` varchar(10) NOT NULL DEFAULT '',
	  `cnt` int(11) unsigned NOT NULL DEFAULT '0',
	  `pagecnt` int(11) unsigned NOT NULL DEFAULT '0',
	  PRIMARY KEY (`date`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblcountercode` (
	  `date` varchar(8) NOT NULL DEFAULT '',
	  `code` varchar(12) NOT NULL DEFAULT '',
	  `cnt` smallint(5) unsigned NOT NULL DEFAULT '1',
	  PRIMARY KEY (`date`,`code`),
	  KEY `idx_tblcountercode_1` (`date`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblcountercodemonth` (
	  `date` varchar(6) NOT NULL DEFAULT '',
	  `code` char(12) NOT NULL DEFAULT '',
	  `cnt` mediumint(7) unsigned NOT NULL DEFAULT '0',
	  PRIMARY KEY (`date`,`code`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblcounterdomain` (
	  `date` varchar(8) NOT NULL DEFAULT '',
	  `domain` varchar(100) NOT NULL DEFAULT '',
	  `cnt` smallint(5) unsigned NOT NULL DEFAULT '1',
	  PRIMARY KEY (`date`,`domain`),
	  KEY `idx_tblcounterdomain_1` (`date`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblcounterdomainmonth` (
	  `date` varchar(6) NOT NULL DEFAULT '',
	  `domain` varchar(100) NOT NULL DEFAULT '',
	  `cnt` mediumint(7) unsigned NOT NULL DEFAULT '0',
	  PRIMARY KEY (`date`,`domain`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblcounterkeyword` (
	  `date` varchar(8) NOT NULL DEFAULT '',
	  `search` varchar(30) NOT NULL DEFAULT '',
	  `cnt` smallint(5) unsigned NOT NULL DEFAULT '1',
	  PRIMARY KEY (`date`,`search`),
	  KEY `idx_tblcounterkeyword_1` (`date`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblcounterkeywordmonth` (
	  `date` varchar(6) NOT NULL DEFAULT '',
	  `search` varchar(30) NOT NULL DEFAULT '',
	  `cnt` mediumint(7) unsigned NOT NULL DEFAULT '0',
	  PRIMARY KEY (`date`,`search`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblcountermonth` (
	  `date` varchar(8) NOT NULL DEFAULT '',
	  `cnt` int(11) unsigned NOT NULL DEFAULT '0',
	  `pagecnt` int(11) unsigned NOT NULL DEFAULT '0',
	  PRIMARY KEY (`date`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblcounterorder` (
	  `date` varchar(10) NOT NULL DEFAULT '',
	  `cnt` smallint(5) unsigned NOT NULL DEFAULT '1',
	  PRIMARY KEY (`date`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblcounterordermonth` (
	  `date` varchar(8) NOT NULL DEFAULT '',
	  `cnt` smallint(5) unsigned NOT NULL DEFAULT '0',
	  PRIMARY KEY (`date`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblcounterpageview` (
	  `date` varchar(8) NOT NULL DEFAULT '',
	  `page` varchar(50) NOT NULL DEFAULT '',
	  `cnt` smallint(5) unsigned NOT NULL DEFAULT '1',
	  PRIMARY KEY (`date`,`page`),
	  KEY `idx_tblcounterpageview_1` (`date`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblcounterpageviewmonth` (
	  `date` varchar(6) NOT NULL DEFAULT '',
	  `page` varchar(50) NOT NULL DEFAULT '',
	  `cnt` mediumint(7) unsigned NOT NULL DEFAULT '0',
	  PRIMARY KEY (`date`,`page`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblcounterproduct` (
	  `date` varchar(8) NOT NULL DEFAULT '',
	  `productcode` varchar(18) NOT NULL DEFAULT '',
	  `cnt` int(11) unsigned NOT NULL DEFAULT '1',
	  PRIMARY KEY (`date`,`productcode`),
	  KEY `idx_tblcounterproduct_1` (`date`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblcounterproductmonth` (
	  `date` varchar(6) NOT NULL DEFAULT '',
	  `productcode` varchar(18) NOT NULL DEFAULT '',
	  `cnt` int(11) unsigned NOT NULL DEFAULT '0',
	  PRIMARY KEY (`date`,`productcode`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblcountersearchdomain` (
	  `domain` varchar(50) NOT NULL DEFAULT '',
	  `enginename` varchar(50) NOT NULL DEFAULT '',
	  PRIMARY KEY (`domain`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblcountersearchengine` (
	  `date` varchar(8) NOT NULL DEFAULT '',
	  `domain` varchar(100) NOT NULL DEFAULT '',
	  `cnt` smallint(5) unsigned NOT NULL DEFAULT '1',
	  PRIMARY KEY (`date`,`domain`),
	  KEY `idx_tblcountersearchengine_1` (`date`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblcountersearchenginemonth` (
	  `date` varchar(6) NOT NULL DEFAULT '',
	  `domain` varchar(100) NOT NULL DEFAULT '',
	  `cnt` mediumint(7) unsigned NOT NULL DEFAULT '0',
	  PRIMARY KEY (`date`,`domain`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblcountersearchword` (
	  `date` varchar(6) NOT NULL DEFAULT '',
	  `domain` varchar(50) NOT NULL DEFAULT '',
	  `search` varchar(30) NOT NULL DEFAULT '',
	  `cnt` smallint(5) unsigned NOT NULL DEFAULT '1',
	  PRIMARY KEY (`date`,`domain`,`search`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblcounterupdate` (
	  `date` varchar(6) NOT NULL DEFAULT ''
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblcouponDesign` (
	  `idx` int(11) NOT NULL AUTO_INCREMENT COMMENT '�ε���',
	  `data` text NOT NULL,
	  PRIMARY KEY (`idx`)
	) ENGINE=MyISAM  DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblcouponinfo` (
	  `coupon_code` varchar(10) NOT NULL DEFAULT '',
	  `coupon_name` varchar(100) NOT NULL DEFAULT '',
	  `date_start` varchar(10) NOT NULL DEFAULT '',
	  `date_end` varchar(10) NOT NULL DEFAULT '',
	  `sale_type` char(1) NOT NULL COMMENT '2���� ũ�� ���� ������ ����',
	  `sale_money` int(11) unsigned NOT NULL DEFAULT '0',
	  `amount_floor` char(1) NOT NULL DEFAULT '1',
	  `mini_price` int(11) unsigned NOT NULL DEFAULT '0',
	  `bank_only` char(1) NOT NULL DEFAULT 'N',
	  `order_limit` char(1) NOT NULL DEFAULT 'N',
	  `productcode` text NOT NULL COMMENT 'ALL�� ��ü��ǰ',
	  `use_con_type1` char(1) NOT NULL DEFAULT 'N',
	  `use_con_type2` char(1) NOT NULL DEFAULT 'Y',
	  `issue_type` char(1) NOT NULL DEFAULT 'N',
	  `detail_auto` char(1) NOT NULL DEFAULT 'Y',
	  `issue_tot_no` int(11) unsigned NOT NULL DEFAULT '0',
	  `repeat_id` char(1) NOT NULL DEFAULT 'N',
	  `repeat_ok` char(1) NOT NULL DEFAULT 'N',
	  `description` varchar(100) NOT NULL DEFAULT '',
	  `use_point` char(1) NOT NULL DEFAULT 'N',
	  `member` varchar(4) NOT NULL DEFAULT '',
	  `etcapply_gift` char(1) NOT NULL DEFAULT 'N',
	  `display` char(1) NOT NULL DEFAULT 'Y',
	  `issue_no` int(11) unsigned NOT NULL DEFAULT '0',
	  `date` varchar(14) NOT NULL DEFAULT '',
	  `vender` int(10) unsigned NOT NULL DEFAULT '0',
	  PRIMARY KEY (`coupon_code`),
	  KEY `idx_tblcouponinfo_1` (`date`),
	  KEY `idx_tblcouponinfo_2` (`vender`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblcouponissue` (
	  `coupon_code` varchar(10) NOT NULL DEFAULT '',
	  `id` varchar(20) NOT NULL DEFAULT '',
	  `date_start` varchar(10) NOT NULL DEFAULT '',
	  `date_end` varchar(10) NOT NULL DEFAULT '',
	  `used` char(1) NOT NULL DEFAULT 'N',
	  `date` varchar(14) NOT NULL DEFAULT '',
	  PRIMARY KEY (`coupon_code`,`id`),
	  KEY `idx_tblcouponissue_1` (`coupon_code`,`date`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblcouponissue_off` (
	  `check_code` varchar(32) NOT NULL,
	  `coupon_code` varchar(10) NOT NULL,
	  `id` varchar(20) DEFAULT NULL,
	  `reg_date` datetime NOT NULL,
	  `auth_date` datetime DEFAULT NULL,
	  PRIMARY KEY (`check_code`),
	  KEY `coupon_code` (`coupon_code`,`id`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblcrinfo` (
	  `uid` int(11) unsigned NOT NULL AUTO_INCREMENT,
	  `id` varchar(80) NOT NULL DEFAULT '',
	  `name` varchar(80) NOT NULL DEFAULT '',
	  `bank_name` varchar(80) NOT NULL DEFAULT '',
	  `bank_num` varchar(80) NOT NULL DEFAULT '',
	  `price` int(8) unsigned NOT NULL DEFAULT '0',
	  `memo` text NOT NULL,
	  `status` enum('A','B','C') NOT NULL DEFAULT 'A',
	  `signdate` int(10) unsigned NOT NULL DEFAULT '0',
	  PRIMARY KEY (`uid`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tbldelicompany` (
	  `code` char(3) NOT NULL DEFAULT '',
	  `dacom_code` char(2) NOT NULL DEFAULT '',
	  `inicis_code` varchar(10) NOT NULL DEFAULT '',
	  `company_name` varchar(50) NOT NULL DEFAULT '',
	  `deli_url` varchar(200) NOT NULL DEFAULT '',
	  `trans_num` varchar(10) NOT NULL DEFAULT '',
	  PRIMARY KEY (`code`),
	  KEY `idx_tbldelicompany_1` (`dacom_code`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tbldesign` (
	  `body_top` mediumtext,
	  `body_left` mediumtext,
	  `title_ok` char(1) NOT NULL DEFAULT 'N',
	  `main_title_color` varchar(14) NOT NULL DEFAULT 'E6E6E6',
	  `top_height` smallint(5) unsigned NOT NULL DEFAULT '152',
	  `left_set` char(1) NOT NULL DEFAULT 'N',
	  `left_xsize` smallint(5) unsigned NOT NULL DEFAULT '150',
	  `left_image` varchar(255) NOT NULL DEFAULT '1,2,3,4,5',
	  `top_set` char(1) NOT NULL DEFAULT 'N',
	  `top_xsize` smallint(5) unsigned NOT NULL DEFAULT '900',
	  `top_ysize` smallint(5) unsigned NOT NULL DEFAULT '55',
	  `menu_align` char(1) NOT NULL DEFAULT 'C',
	  `background` char(1) NOT NULL DEFAULT 'N',
	  `logo_loc` char(1) NOT NULL DEFAULT 'Y',
	  `menu_list` varchar(255) NOT NULL DEFAULT '1,2,3,4,5,6',
	  `link1` varchar(255) DEFAULT NULL,
	  `link2` varchar(255) DEFAULT NULL,
	  `link3` varchar(255) DEFAULT NULL,
	  `link4` varchar(255) DEFAULT NULL,
	  `link5` varchar(255) DEFAULT NULL,
	  `introtype` char(1) NOT NULL DEFAULT 'A',
	  `mapimage` varchar(30) DEFAULT NULL,
	  `mapalign` varchar(10) DEFAULT NULL,
	  `companyname` varchar(30) DEFAULT NULL,
	  `shopname` varchar(30) DEFAULT NULL,
	  `ownername` varchar(10) DEFAULT NULL,
	  `owneremail` varchar(50) DEFAULT NULL,
	  `info_tel` varchar(100) DEFAULT NULL,
	  `info_fax` varchar(20) DEFAULT NULL,
	  `info_counsel` varchar(50) DEFAULT NULL,
	  `info_email` varchar(50) DEFAULT NULL,
	  `privercyname` varchar(10) DEFAULT NULL,
	  `privercyemail` varchar(30) DEFAULT NULL,
	  `content` text,
	  `history` text,
	  `agreement` mediumtext,
	  `agreement2` mediumtext,
	  `privercy` mediumtext,
	  `useinfo` text
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tbldesign_top_left_backup` (
	  `body_top` mediumtext,
	  `body_left` mediumtext,
	  `title_ok` char(1) NOT NULL DEFAULT 'N',
	  `main_title_color` varchar(14) NOT NULL DEFAULT 'E6E6E6',
	  `top_height` smallint(5) unsigned NOT NULL DEFAULT '152',
	  `left_set` char(1) NOT NULL DEFAULT 'N',
	  `left_xsize` smallint(5) unsigned NOT NULL DEFAULT '150',
	  `left_image` varchar(255) NOT NULL DEFAULT '1,2,3,4,5',
	  `top_set` char(1) NOT NULL DEFAULT 'N',
	  `top_xsize` smallint(5) unsigned NOT NULL DEFAULT '900',
	  `top_ysize` smallint(5) unsigned NOT NULL DEFAULT '55',
	  `menu_align` char(1) NOT NULL DEFAULT 'C',
	  `background` char(1) NOT NULL DEFAULT 'N',
	  `logo_loc` char(1) NOT NULL DEFAULT 'Y',
	  `menu_list` varchar(255) NOT NULL DEFAULT '1,2,3,4,5,6',
	  `link1` varchar(255) DEFAULT NULL,
	  `link2` varchar(255) DEFAULT NULL,
	  `link3` varchar(255) DEFAULT NULL,
	  `link4` varchar(255) DEFAULT NULL,
	  `link5` varchar(255) DEFAULT NULL,
	  `introtype` char(1) NOT NULL DEFAULT 'A',
	  `mapimage` varchar(30) DEFAULT NULL,
	  `mapalign` varchar(10) DEFAULT NULL,
	  `companyname` varchar(30) DEFAULT NULL,
	  `shopname` varchar(30) DEFAULT NULL,
	  `ownername` varchar(10) DEFAULT NULL,
	  `owneremail` varchar(50) DEFAULT NULL,
	  `info_tel` varchar(100) DEFAULT NULL,
	  `info_fax` varchar(20) DEFAULT NULL,
	  `info_counsel` varchar(50) DEFAULT NULL,
	  `info_email` varchar(50) DEFAULT NULL,
	  `privercyname` varchar(10) DEFAULT NULL,
	  `privercyemail` varchar(30) DEFAULT NULL,
	  `content` text,
	  `history` text,
	  `agreement` mediumtext,
	  `agreement2` mediumtext,
	  `privercy` mediumtext,
	  `useinfo` text,
	  `dbl_no` int(11)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tbldesigndefault` (
	  `type` varchar(10) NOT NULL DEFAULT '',
	  `body` mediumtext,
	  PRIMARY KEY (`type`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tbldesignnewpage` (
	  `type` varchar(10) NOT NULL DEFAULT '',
	  `code` varchar(12) NOT NULL DEFAULT '0',
	  `subject` varchar(100) NOT NULL DEFAULT '',
	  `filename` varchar(100) NOT NULL DEFAULT '',
	  `leftmenu` char(1) NOT NULL DEFAULT 'Y',
	  `body` mediumtext
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tbldesignnewpage_prev` (
	  `type` varchar(10) NOT NULL DEFAULT '',
	  `code` varchar(12) NOT NULL DEFAULT '0',
	  `subject` varchar(100) NOT NULL DEFAULT '',
	  `filename` varchar(100) NOT NULL DEFAULT '',
	  `leftmenu` char(1) NOT NULL DEFAULT 'Y',
	  `body` mediumtext
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tbldesignnewpage_temp` (
	  `type` varchar(20) NOT NULL,
	  `code` varchar(12) NOT NULL DEFAULT '0',
	  `subject` varchar(100) NOT NULL DEFAULT '',
	  `filename` varchar(100) NOT NULL DEFAULT '',
	  `leftmenu` char(1) NOT NULL DEFAULT 'Y',
	  `body` mediumtext,
	  `orgTable` varchar(40) NOT NULL,
	  `orgField` varchar(40) NOT NULL
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tbldesign_backup_history` (
	  `dbh_no` int(11) NOT NULL AUTO_INCREMENT,
	  `dbh_type` varchar(20) DEFAULT NULL,
	  `dbh_mode` varchar(10) DEFAULT NULL,
	  `dbh_content` varchar(200) DEFAULT NULL,
	  `dbh_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
	  `dbl_no` int(11) DEFAULT NULL,
	  `id` varchar(20) DEFAULT NULL,
	  PRIMARY KEY (`dbh_no`),
	  KEY `R_2` (`dbl_no`),
	  KEY `R_3` (`id`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tbldesign_backup_list` (
	  `dbl_no` int(11) NOT NULL AUTO_INCREMENT,
	  `dbl_type` enum('backup','skin') NOT NULL,
	  `dbl_subject` varchar(20) DEFAULT NULL,
	  `dbl_ftpnm_ftp` varchar(200) DEFAULT NULL,
	  `dbl_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
	  `dbl_downuse` int(11) DEFAULT '0',
	  `dbl_use` enum('y','n') NOT NULL DEFAULT 'y',
	  `dbl_del_data` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
	  `id` varchar(20) DEFAULT NULL,
	  PRIMARY KEY (`dbl_no`),
	  KEY `R_1` (`id`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tbldesign_backup_page` (
	  `dbp_no` int(11) NOT NULL AUTO_INCREMENT,
	  `dbp_type` varchar(10) DEFAULT NULL,
	  `dbp_code` varchar(12) DEFAULT '0',
	  `dbp_subject` varchar(100) DEFAULT NULL,
	  `dbp_filename` varchar(100) DEFAULT NULL,
	  `dbp_body` text,
	  `dbp_leftmenu` char(1) DEFAULT 'Y',
	  `dbl_no` int(11) DEFAULT NULL,
	  PRIMARY KEY (`dbp_no`),
	  KEY `R_4` (`dbl_no`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tbldesign_backup_quick` (
	  `dbq_no` int(11) NOT NULL AUTO_INCREMENT,
	  `dbq_num` int(11) NOT NULL,
	  `dbq_used` char(1) DEFAULT 'N',
	  `dbq_reg_date` varchar(14) DEFAULT NULL,
	  `dbq_design` varchar(50) DEFAULT '004',
	  `dbq_x_size` smallint(3) DEFAULT '420',
	  `dbq_y_size` smallint(3) DEFAULT '400',
	  `dbq_x_to` smallint(3) DEFAULT '0',
	  `dbq_y_to` smallint(3) DEFAULT '0',
	  `dbq_scroll_auto` char(1) DEFAULT 'Y',
	  `dbq_title` varchar(100) DEFAULT NULL,
	  `dbq_content` text,
	  `dbl_no` int(11) DEFAULT NULL,
	  PRIMARY KEY (`dbq_no`),
	  KEY `R_7` (`dbl_no`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tbldesign_backup_template` (
	  `dbt_no` int(11) NOT NULL AUTO_INCREMENT,
	  `dbt_frame_type` char(1) DEFAULT 'N',
	  `dbt_top_type` varchar(50) DEFAULT 'top004',
	  `dbt_main_type` varchar(50) DEFAULT 'main004',
	  `dbt_title_type` varchar(10) DEFAULT 'N',
	  `dbt_icon_use_type` varchar(50) DEFAULT NULL,
	  `dbt_top_height` smallint(5) DEFAULT '152',
	  `dbt_css` text,
	  `dbt_quick_type` char(1) NOT NULL DEFAULT '0',
	  `dbl_no` int(11) DEFAULT NULL,
	  PRIMARY KEY (`dbt_no`),
	  KEY `R_6` (`dbl_no`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tbleventpopup` (
	  `num` int(11) unsigned NOT NULL AUTO_INCREMENT,
	  `start_date` varchar(10) NOT NULL DEFAULT '',
	  `end_date` varchar(10) NOT NULL DEFAULT '',
	  `reg_date` varchar(14) NOT NULL DEFAULT '',
	  `design` char(3) NOT NULL DEFAULT '001',
	  `x_size` smallint(3) unsigned NOT NULL DEFAULT '420',
	  `y_size` smallint(3) unsigned NOT NULL DEFAULT '400',
	  `x_to` smallint(3) NOT NULL DEFAULT '0',
	  `y_to` smallint(3) NOT NULL DEFAULT '0',
	  `scroll_yn` char(1) NOT NULL DEFAULT 'Y',
	  `frame_type` char(1) NOT NULL DEFAULT '0',
	  `cookietime` char(1) NOT NULL DEFAULT '2',
	  `title` varchar(100) NOT NULL DEFAULT '',
	  `content` text NOT NULL,
	  PRIMARY KEY (`num`),
	  KEY `idx_tbleventpopup_1` (`start_date`,`end_date`)
	) ENGINE=MyISAM  DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblgiftinfo` (
	  `gift_regdate` varchar(14) NOT NULL DEFAULT '',
	  `gift_startprice` int(11) unsigned NOT NULL DEFAULT '0',
	  `gift_endprice` int(11) unsigned NOT NULL DEFAULT '0',
	  `gift_quantity` smallint(5) unsigned DEFAULT NULL,
	  `gift_limit` smallint(5) unsigned NOT NULL DEFAULT '0',
	  `gift_name` varchar(200) NOT NULL DEFAULT '',
	  `gift_image` varchar(30) NOT NULL DEFAULT '',
	  `gift_option1` varchar(250) NOT NULL DEFAULT '',
	  `gift_option2` varchar(250) NOT NULL DEFAULT '',
	  `gift_option3` varchar(250) NOT NULL DEFAULT '',
	  `gift_option4` varchar(250) NOT NULL DEFAULT '',
	  PRIMARY KEY (`gift_regdate`),
	  KEY `idx_tblgiftinfo_1` (`gift_name`),
	  KEY `idx_tblgiftinfo_2` (`gift_quantity`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblgift_info` (
	  `uid` int(11) unsigned NOT NULL AUTO_INCREMENT,
	  `ordercode` varchar(25) NOT NULL DEFAULT '',
	  `send_id` varchar(80) NOT NULL DEFAULT '',
	  `name` varchar(80) NOT NULL DEFAULT '',
	  `productcode` varchar(18) NOT NULL DEFAULT '',
	  `price` int(8) unsigned NOT NULL DEFAULT '0',
	  `authcode1` varchar(10) NOT NULL DEFAULT '',
	  `authcode2` varchar(10) NOT NULL DEFAULT '',
	  `use_id` varchar(80) NOT NULL DEFAULT '',
	  `use_date` int(10) unsigned NOT NULL DEFAULT '0',
	  `memo` text NOT NULL,
	  `status` enum('A','B','C') NOT NULL DEFAULT 'A',
	  `signdate` int(10) unsigned NOT NULL DEFAULT '0',
	  PRIMARY KEY (`uid`),
	  KEY `ordercode` (`ordercode`),
	  KEY `send_id` (`send_id`),
	  KEY `use_id` (`use_id`)
	) ENGINE=MyISAM  DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblgongguencore` (
	  `productcode` varchar(18) NOT NULL DEFAULT '',
	  `id` varchar(20) NOT NULL DEFAULT '',
	  `regidate` int(11) NOT NULL DEFAULT '0',
	  PRIMARY KEY (`productcode`,`id`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblgonggumail` (
	  `date` varchar(14) NOT NULL DEFAULT '',
	  `sendcnt` int(11) NOT NULL DEFAULT '0',
	  `toemail` mediumtext,
	  `body` mediumtext,
	  PRIMARY KEY (`date`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblgonginfo` (
	  `gong_seq` int(11) unsigned NOT NULL DEFAULT '1',
	  `start_date` varchar(14) NOT NULL DEFAULT '',
	  `end_date` varchar(14) NOT NULL DEFAULT '',
	  `gong_name` varchar(100) NOT NULL DEFAULT '',
	  `production` varchar(20) DEFAULT NULL,
	  `specialadd` varchar(100) NOT NULL DEFAULT '',
	  `receipt_end` tinyint(3) unsigned NOT NULL DEFAULT '3',
	  `origin_price` int(11) unsigned NOT NULL DEFAULT '0',
	  `start_price` int(11) unsigned NOT NULL DEFAULT '0',
	  `quantity` smallint(5) unsigned NOT NULL DEFAULT '0',
	  `down_price` smallint(5) unsigned NOT NULL DEFAULT '0',
	  `mini_price` int(11) unsigned NOT NULL DEFAULT '0',
	  `count` smallint(5) unsigned NOT NULL DEFAULT '0',
	  `deli_money` smallint(5) DEFAULT NULL,
	  `gbn` char(1) NOT NULL DEFAULT 'O',
	  `bid_cnt` smallint(5) unsigned NOT NULL DEFAULT '0',
	  `image1` varchar(30) NOT NULL DEFAULT '',
	  `image2` varchar(30) NOT NULL DEFAULT '',
	  `image3` varchar(30) NOT NULL DEFAULT '',
	  `content` mediumtext NOT NULL,
	  PRIMARY KEY (`gong_seq`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblgongresult` (
	  `gong_seq` int(11) unsigned NOT NULL DEFAULT '1',
	  `id` varchar(20) NOT NULL DEFAULT '',
	  `name` varchar(20) NOT NULL DEFAULT '',
	  `email` varchar(50) NOT NULL DEFAULT '',
	  `tel` varchar(15) NOT NULL DEFAULT '',
	  `address` varchar(150) NOT NULL DEFAULT '',
	  `process_gbn` char(1) NOT NULL DEFAULT '',
	  `buy_cnt` smallint(3) NOT NULL DEFAULT '1',
	  `date` varchar(14) NOT NULL DEFAULT '',
	  `memo` tinytext NOT NULL,
	  PRIMARY KEY (`gong_seq`,`id`),
	  KEY `idx_tblgongresult_1` (`date`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblgroupck` (
	  `uid` int(11) NOT NULL AUTO_INCREMENT,
	  `date` varchar(10) NOT NULL DEFAULT '',
	  PRIMARY KEY (`uid`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblgroupmail` (
	  `date` varchar(14) NOT NULL DEFAULT '',
	  `issend` char(1) NOT NULL DEFAULT 'N',
	  `html` char(1) NOT NULL DEFAULT 'N',
	  `fromemail` varchar(50) DEFAULT NULL,
	  `shopname` varchar(50) DEFAULT NULL,
	  `filename` varchar(100) DEFAULT NULL,
	  `subject` varchar(250) DEFAULT NULL,
	  `body` mediumtext,
	  `procok` char(1) NOT NULL DEFAULT 'N',
	  `okcnt` int(7) DEFAULT NULL,
	  `enddate` varchar(14) DEFAULT NULL,
	  PRIMARY KEY (`date`,`issend`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblmember` (
	  `id` varchar(20) NOT NULL DEFAULT '',
	  `passwd` varchar(60) NOT NULL DEFAULT '',
	  `name` varchar(50) NOT NULL DEFAULT '',
	  `resno` varchar(50) NOT NULL DEFAULT '',
	  `email` varchar(50) NOT NULL DEFAULT '',
	  `mobile` varchar(20) NOT NULL DEFAULT '',
	  `news_yn` char(1) NOT NULL DEFAULT 'Y',
	  `age` char(3) NOT NULL DEFAULT '',
	  `gender` char(1) NOT NULL DEFAULT '',
	  `married_yn` char(1) NOT NULL DEFAULT 'N',
	  `job` varchar(20) NOT NULL DEFAULT '',
	  `birth` varchar(10) NOT NULL DEFAULT '',
	  `lunar` char(1) NOT NULL DEFAULT '1',
	  `home_post` varchar(6) NOT NULL DEFAULT '',
	  `home_addr` varchar(120) NOT NULL DEFAULT '',
	  `home_tel` varchar(20) NOT NULL DEFAULT '',
	  `office_post` varchar(6) NOT NULL DEFAULT '',
	  `office_addr` varchar(120) NOT NULL DEFAULT '',
	  `office_tel` varchar(20) NOT NULL DEFAULT '',
	  `memo` varchar(200) NOT NULL DEFAULT '',
	  `reserve` int(11) unsigned NOT NULL DEFAULT '0',
	  `joinip` varchar(15) NOT NULL DEFAULT '',
	  `ip` varchar(15) NOT NULL DEFAULT '',
	  `logindate` varchar(14) NOT NULL DEFAULT '',
	  `logincnt` int(11) unsigned NOT NULL DEFAULT '0',
	  `date` varchar(14) NOT NULL DEFAULT '',
	  `confirm_yn` char(1) NOT NULL DEFAULT 'Y',
	  `rec_id` varchar(20) NOT NULL DEFAULT '',
	  `authidkey` varchar(32) NOT NULL DEFAULT '',
	  `group_code` varchar(4) NOT NULL DEFAULT '',
	  `member_out` char(1) NOT NULL DEFAULT 'N',
	  `etcdata` text NOT NULL,
	  `url_id` varchar(20) NOT NULL DEFAULT '',
	  `total_cnt` int(11) NOT NULL DEFAULT '0',
	  `comp_num` varchar(20) NOT NULL DEFAULT '',
	  `comp_owner` varchar(20) NOT NULL DEFAULT '',
	  `comp_type1` varchar(100) NOT NULL DEFAULT '',
	  `comp_type2` varchar(100) NOT NULL DEFAULT '',
	  `wholesaletype` char(1) DEFAULT NULL COMMENT '����ȸ�� ���� ''R-����ȸ�� ��û,Y-����ȸ��, �׿ܴ� �Ϲ�ȸ��''',
	  `devices` varchar(1) NOT NULL,
	  `vDiscrNo` varchar(13) DEFAULT NULL,
	  `uniqNo` varchar(67) DEFAULT NULL,
	  PRIMARY KEY (`id`),
	  KEY `idx_tblmember_1` (`name`),
	  KEY `idx_tblmember_2` (`resno`),
	  KEY `idx_tblmember_3` (`email`),
	  KEY `idx_tblmember_4` (`logindate`),
	  KEY `idx_tblmember_5` (`rec_id`),
	  KEY `idx_tblmember_6` (`date`),
	  KEY `idx_tblmember_7` (`group_code`),
	  KEY `idx_tblmember_8` (`member_out`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblmemberdiscount` (
	  `dsidx` int(5) unsigned NOT NULL AUTO_INCREMENT,
	  `group_code` varchar(4) NOT NULL DEFAULT '',
	  `productcode` varchar(25) NOT NULL,
	  `discountYN` varchar(1) NOT NULL DEFAULT 'N',
	  `discountrates` float NOT NULL DEFAULT '0',
	  `discountprices` int(8) NOT NULL DEFAULT '0',
	  `over_discount` enum('Y','N') NOT NULL DEFAULT 'Y',
	  PRIMARY KEY (`dsidx`)
	) ENGINE=MyISAM  DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblmembergroup` (
	  `group_code` varchar(4) NOT NULL DEFAULT '',
	  `group_excp_auto` varchar(2) DEFAULT NULL,
	  `group_name` varchar(50) NOT NULL DEFAULT '',
	  `group_description` varchar(250) NOT NULL DEFAULT '',
	  `group_payment` char(1) NOT NULL DEFAULT 'N',
	  `group_usemoney` int(8) unsigned NOT NULL DEFAULT '0',
	  `group_addmoney` int(8) unsigned NOT NULL DEFAULT '0',
	  `group_apply_gift` char(1) NOT NULL DEFAULT 'Y',
	  `group_apply_coupon` char(1) NOT NULL DEFAULT 'Y',
	  `group_apply_reserve` char(1) NOT NULL DEFAULT 'Y',
	  `group_apply_use_reserve` char(1) NOT NULL DEFAULT 'Y',
	  `group_card_commi` int(11) NOT NULL DEFAULT '0',
	  `group_carr_free` varchar(10) NOT NULL DEFAULT '',
	  `group_order_price` int(11) NOT NULL DEFAULT '0',
	  `group_seller` char(1) NOT NULL DEFAULT 'N',
	  `group_order_cnt` int(11) NOT NULL DEFAULT '0',
	  `group_order_type` int(2) NOT NULL DEFAULT '1' COMMENT '�׷�±�����Ÿ��',
	  `use_auto_coupon` int(1) NOT NULL DEFAULT '0' COMMENT '�ڵ��߱����� ��� ��� ����',
	  `group_iossreserve` int(10) unsigned DEFAULT '0' COMMENT '�±޽�����������',
	  `groupCouponSendType` int(2) NOT NULL DEFAULT '1' COMMENT '�±� �ڵ� ���� ���� Ÿ��',
	  `groupCouponSendType_M` int(2) NOT NULL DEFAULT '1' COMMENT '�±� �ڵ� ���� ���� ��',
	  `groupCouponSendType_D` int(2) NOT NULL DEFAULT '1' COMMENT '�±� �ڵ� ���� ���� ��',
	  PRIMARY KEY (`group_code`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblmemberout` (
	  `id` varchar(20) NOT NULL DEFAULT '',
	  `name` varchar(20) NOT NULL DEFAULT '',
	  `email` varchar(50) NOT NULL DEFAULT '',
	  `tel` varchar(15) NOT NULL DEFAULT '',
	  `etc` char(2) NOT NULL,
	  `txt` text NOT NULL,
	  `ip` varchar(15) NOT NULL DEFAULT '',
	  `state` char(1) NOT NULL DEFAULT 'N',
	  `date` varchar(14) NOT NULL DEFAULT '',
	  PRIMARY KEY (`id`),
	  KEY `idx_tblmemberout_1` (`date`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblmembersnsinfo` (
	  `id` varchar(20) NOT NULL DEFAULT '',
	  `type` char(1) NOT NULL DEFAULT '',
	  `user_id` varchar(20) NOT NULL DEFAULT '',
	  `oauth_token` varchar(300) NOT NULL,
	  `oauth_token2` varchar(300) NOT NULL,
	  `screen_name` varchar(100) NOT NULL DEFAULT '',
	  `profile_img` varchar(100) NOT NULL DEFAULT '',
	  `state` char(1) NOT NULL DEFAULT 'Y',
	  `regidate` int(11) NOT NULL DEFAULT '0',
	  `extval` text COMMENT '���̽��� ���� �ڵ� �� ������',
	  `link` text NOT NULL COMMENT '����� ������ ��ũ',
	  PRIMARY KEY (`id`,`type`),
	  KEY `idx_tblmembersnsinfo_1` (`id`),
	  KEY `idx_tblmembersnsinfo_2` (`type`),
	  KEY `idx_tblmembersnsinfo_3` (`state`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblmemcompany` (
	  `memid` varchar(25) NOT NULL DEFAULT '',
	  `companyname` varchar(40) NOT NULL DEFAULT '',
	  `companynum` varchar(20) NOT NULL DEFAULT '',
	  `companytnum` varchar(4) NOT NULL DEFAULT '',
	  `companyowner` varchar(20) NOT NULL DEFAULT '',
	  `companypost` varchar(6) NOT NULL DEFAULT '',
	  `companyaddr` varchar(150) NOT NULL DEFAULT '',
	  `companybiz` varchar(40) NOT NULL DEFAULT '',
	  `companyitem` varchar(40) NOT NULL DEFAULT '',
	  `c_name` varchar(40) NOT NULL DEFAULT '',
	  `c_email` varchar(100) NOT NULL DEFAULT '',
	  `c_cell` varchar(20) NOT NULL DEFAULT '',
	  `regidate` int(11) DEFAULT NULL,
	  PRIMARY KEY (`memid`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblmemo` (
	  `id` varchar(20) NOT NULL DEFAULT '',
	  `date` varchar(14) NOT NULL DEFAULT '',
	  `memo` varchar(200) NOT NULL DEFAULT '',
	  PRIMARY KEY (`id`,`date`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblmobilebanner` (
	  `date` varchar(14) NOT NULL DEFAULT '',
	  `image` varchar(100) DEFAULT NULL,
	  `border` char(1) NOT NULL DEFAULT '1',
	  `url_type` char(1) NOT NULL DEFAULT 'H',
	  `url` varchar(200) NOT NULL DEFAULT '',
	  `target` varchar(8) NOT NULL DEFAULT '_blank',
	  PRIMARY KEY (`date`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblmobileconfig` (
	  `use_mobile_site` char(1) DEFAULT 'Y',
	  `use_auto_redirection` varchar(10) DEFAULT 'Y',
	  `use_cross_link` varchar(100) DEFAULT NULL,
	  `use_bank` varchar(100) DEFAULT NULL,
	  `use_creditcard` varchar(10) DEFAULT NULL,
	  `use_transferaccount` char(1) DEFAULT 'N' COMMENT '������ü',
	  `use_virtualaccount` char(1) DEFAULT 'N',
	  `use_mobilephone` char(1) DEFAULT NULL,
	  `use_mobile_qna` char(1) DEFAULT 'Y',
	  `use_mobile_qna_write` char(1) DEFAULT NULL,
	  `use_mobile_sns` varchar(10) DEFAULT 'N|N|N|N' COMMENT 'SNS ���� ��������',
	  `skin` varchar(50) DEFAULT NULL,
	  `skin_css` varchar(50) DEFAULT NULL,
	  `color_css` varchar(50) DEFAULT NULL,
	  `logo` varchar(100) DEFAULT NULL,
	  `icon` varchar(100) DEFAULT NULL,
	  `copyright_text` text,
	  `copyright_image` varchar(255) DEFAULT NULL,
	  `main_item_sort` char(2) DEFAULT NULL,
	  `use_same_product_code` char(1) DEFAULT 'Y',
	  `use_same_product_image` char(1) DEFAULT 'Y'
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblmobiledirectmenu` (
	  `date` varchar(14) NOT NULL DEFAULT '',
	  `image` varchar(100) DEFAULT NULL,
	  `title` varchar(50) DEFAULT NULL,
	  `url_type` char(1) NOT NULL DEFAULT 'H',
	  `url` varchar(200) NOT NULL DEFAULT '',
	  `target` varchar(8) NOT NULL DEFAULT '_blank',
	  PRIMARY KEY (`date`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblmobilepg` (
	  `pg_use` char(1) NOT NULL DEFAULT 'N',
	  `pg_type` varchar(100) DEFAULT NULL,
	  `pg_id` varchar(100) DEFAULT NULL,
	  `pg_key` varchar(100) DEFAULT NULL,
	  `pg_mode` char(1) DEFAULT 'T',
	  `pg_date` varchar(14) DEFAULT NULL,
	  `pg_section` varchar(6) DEFAULT NULL,
	  PRIMARY KEY (`pg_use`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblmobileplanningmain` (
	  `pm_idx` int(11) NOT NULL AUTO_INCREMENT,
	  `title` varchar(50) NOT NULL DEFAULT '',
	  `display` char(1) DEFAULT NULL,
	  `display_type` varchar(10) DEFAULT NULL,
	  `product_cnt` tinyint(4) DEFAULT NULL,
	  `product_list` text,
	  PRIMARY KEY (`pm_idx`)
	) ENGINE=MyISAM  DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblmultiimages` (
	  `productcode` varchar(18) NOT NULL DEFAULT '',
	  `primg01` varchar(30) NOT NULL DEFAULT '',
	  `primg02` varchar(30) NOT NULL DEFAULT '',
	  `primg03` varchar(30) NOT NULL DEFAULT '',
	  `primg04` varchar(30) NOT NULL DEFAULT '',
	  `primg05` varchar(30) NOT NULL DEFAULT '',
	  `primg06` varchar(30) NOT NULL DEFAULT '',
	  `primg07` varchar(30) NOT NULL DEFAULT '',
	  `primg08` varchar(30) NOT NULL DEFAULT '',
	  `primg09` varchar(30) NOT NULL DEFAULT '',
	  `primg10` varchar(30) NOT NULL DEFAULT '',
	  `size` varchar(200) NOT NULL DEFAULT '',
	  PRIMARY KEY (`productcode`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblnotice` (
	  `date` varchar(14) NOT NULL DEFAULT '',
	  `access` int(11) unsigned NOT NULL DEFAULT '0',
	  `subject` varchar(150) NOT NULL DEFAULT '',
	  `content` mediumtext NOT NULL,
	  PRIMARY KEY (`date`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblorderbill` (
	  `b_idx` int(11) NOT NULL AUTO_INCREMENT,
	  `serial` varchar(30) NOT NULL DEFAULT '',
	  `memid` varchar(25) NOT NULL,
	  `memname` varchar(20) NOT NULL DEFAULT '',
	  `ordercode` varchar(25) NOT NULL DEFAULT '',
	  `document_id` varchar(40) NOT NULL DEFAULT '',
	  `bill_price` varchar(255) NOT NULL DEFAULT '',
	  `companyname` varchar(40) NOT NULL DEFAULT '',
	  `companynum` varchar(20) NOT NULL DEFAULT '',
	  `companytnum` varchar(4) NOT NULL DEFAULT '',
	  `companyowner` varchar(20) NOT NULL DEFAULT '',
	  `companyaddr` varchar(150) NOT NULL DEFAULT '',
	  `companybiz` varchar(40) NOT NULL DEFAULT '',
	  `companyitem` varchar(40) NOT NULL DEFAULT '',
	  `c_name` varchar(40) NOT NULL DEFAULT '',
	  `c_email` varchar(100) NOT NULL DEFAULT '',
	  `c_cell` varchar(20) NOT NULL DEFAULT '',
	  `regidate` int(11) DEFAULT NULL,
	  PRIMARY KEY (`b_idx`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblorderbillerror` (
	  `ordercode` varchar(25) NOT NULL DEFAULT '',
	  `error` varchar(255) NOT NULL DEFAULT '',
	  `regidate` int(11) DEFAULT NULL
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblordercoupon` (
	  `idx` int(11) NOT NULL AUTO_INCREMENT,
	  `ordercode` varchar(25) NOT NULL,
	  `orderPuid` int(11) NOT NULL,
	  `couponcode` varchar(10) NOT NULL,
	  `dcPrice` int(11) NOT NULL,
	  `reserve` int(11) NOT NULL,
	  KEY `idx` (`idx`)
	) ENGINE=MyISAM  DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblorderinfo` (
	  `ordercode` varchar(25) NOT NULL DEFAULT '',
	  `tempkey` varchar(32) NOT NULL DEFAULT '',
	  `id` varchar(20) NOT NULL DEFAULT '',
	  `price` int(11) DEFAULT NULL,
	  `deli_price` int(7) NOT NULL DEFAULT '0',
	  `dc_price` varchar(9) NOT NULL DEFAULT '0',
	  `reserve` int(11) DEFAULT NULL,
	  `paymethod` char(2) NOT NULL DEFAULT '',
	  `bank_date` varchar(14) DEFAULT NULL,
	  `pay_flag` varchar(8) NOT NULL DEFAULT 'N',
	  `pay_auth_no` varchar(50) DEFAULT NULL,
	  `pay_admin_proc` char(1) NOT NULL DEFAULT 'N',
	  `pay_data` varchar(100) NOT NULL DEFAULT '',
	  `escrow_result` char(1) DEFAULT NULL,
	  `deli_gbn` char(1) NOT NULL DEFAULT 'N',
	  `deli_date` varchar(14) DEFAULT NULL,
	  `sender_name` varchar(30) NOT NULL DEFAULT '',
	  `sender_email` varchar(50) NOT NULL DEFAULT '',
	  `sender_tel` varchar(30) NOT NULL DEFAULT '',
	  `receiver_name` varchar(30) NOT NULL DEFAULT '',
	  `receiver_tel1` varchar(30) NOT NULL DEFAULT '',
	  `receiver_tel2` varchar(30) NOT NULL DEFAULT '',
	  `receiver_addr` varchar(150) NOT NULL DEFAULT '',
	  `order_msg` text,
	  `ip` varchar(15) NOT NULL DEFAULT '',
	  `del_gbn` char(1) NOT NULL DEFAULT 'N',
	  `partner_id` varchar(20) DEFAULT NULL,
	  `loc` varchar(10) DEFAULT NULL,
	  `order_type` char(1) NOT NULL DEFAULT '',
	  `receiver_email` varchar(50) NOT NULL DEFAULT '',
	  `receiver_message` text,
	  `gift` enum('0','1','2','3') NOT NULL DEFAULT '0',
	  `status` varchar(2) NOT NULL DEFAULT '',
	  `device` char(1) DEFAULT NULL COMMENT '�ֹ���û ����̽� ����',
	  `bankname` varchar(60) DEFAULT NULL,
	  PRIMARY KEY (`ordercode`,`tempkey`,`id`),
	  KEY `idx_tblorderinfo_1` (`ordercode`),
	  KEY `idx_tblorderinfo_2` (`id`),
	  KEY `idx_tblorderinfo_3` (`paymethod`),
	  KEY `idx_tblorderinfo_4` (`sender_name`),
	  KEY `idx_tblorderinfo_5` (`receiver_name`),
	  KEY `idx_tblorderinfo_6` (`bank_date`),
	  KEY `idx_tblorderinfo_7` (`deli_gbn`),
	  KEY `idx_tblorderinfo_8` (`deli_date`),
	  KEY `idx_tblorderinfo_9` (`del_gbn`),
	  KEY `idx_tblorderinfo_10` (`partner_id`),
	  KEY `idx_tblorderinfo_11` (`loc`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblorderinfotemp` (
	  `ordercode` varchar(25) NOT NULL DEFAULT '',
	  `tempkey` varchar(32) NOT NULL DEFAULT '',
	  `id` varchar(20) NOT NULL DEFAULT '',
	  `price` int(11) DEFAULT NULL,
	  `deli_price` int(7) NOT NULL DEFAULT '0',
	  `dc_price` varchar(9) NOT NULL DEFAULT '0',
	  `reserve` int(11) DEFAULT NULL,
	  `paymethod` char(2) NOT NULL DEFAULT '',
	  `bank_date` varchar(14) DEFAULT NULL,
	  `pay_flag` varchar(8) NOT NULL DEFAULT 'N',
	  `pay_auth_no` varchar(50) DEFAULT NULL,
	  `pay_admin_proc` char(1) NOT NULL DEFAULT 'N',
	  `pay_data` varchar(100) NOT NULL DEFAULT '',
	  `escrow_result` char(1) DEFAULT NULL,
	  `deli_gbn` char(1) NOT NULL DEFAULT 'N',
	  `deli_date` varchar(14) DEFAULT NULL,
	  `sender_name` varchar(30) NOT NULL DEFAULT '',
	  `sender_email` varchar(50) NOT NULL DEFAULT '',
	  `sender_tel` varchar(30) NOT NULL DEFAULT '',
	  `receiver_name` varchar(30) NOT NULL DEFAULT '',
	  `receiver_tel1` varchar(30) NOT NULL DEFAULT '',
	  `receiver_tel2` varchar(30) NOT NULL DEFAULT '',
	  `receiver_addr` varchar(150) NOT NULL DEFAULT '',
	  `order_msg` text,
	  `ip` varchar(15) NOT NULL DEFAULT '',
	  `del_gbn` char(1) NOT NULL DEFAULT 'N',
	  `partner_id` varchar(20) DEFAULT NULL,
	  `loc` varchar(10) DEFAULT NULL,
	  `order_type` char(1) NOT NULL DEFAULT '',
	  `receiver_email` varchar(50) NOT NULL DEFAULT '',
	  `receiver_message` text,
	  `gift` enum('0','1','2','3') NOT NULL DEFAULT '0',
	  `status` varchar(2) NOT NULL DEFAULT '',
	  `device` char(1) DEFAULT NULL COMMENT '�ֹ���û ����̽� ����',
	  `bankname` varchar(60) DEFAULT NULL,
	  PRIMARY KEY (`ordercode`,`tempkey`,`id`),
	  KEY `idx_tblorderinfotemp_1` (`ordercode`),
	  KEY `idx_tblorderinfotemp_2` (`id`),
	  KEY `idx_tblorderinfotemp_3` (`paymethod`),
	  KEY `idx_tblorderinfotemp_4` (`sender_name`),
	  KEY `idx_tblorderinfotemp_5` (`receiver_name`),
	  KEY `idx_tblorderinfotemp_6` (`bank_date`),
	  KEY `idx_tblorderinfotemp_7` (`deli_gbn`),
	  KEY `idx_tblorderinfotemp_8` (`deli_date`),
	  KEY `idx_tblorderinfotemp_9` (`del_gbn`),
	  KEY `idx_tblorderinfotemp_10` (`partner_id`),
	  KEY `idx_tblorderinfotemp_11` (`loc`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblorderoption` (
	  `ordercode` varchar(25) NOT NULL DEFAULT '',
	  `productcode` varchar(18) NOT NULL DEFAULT '',
	  `opt_idx` varchar(10) NOT NULL DEFAULT '0',
	  `opt_name` text,
	  PRIMARY KEY (`ordercode`,`productcode`,`opt_idx`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblorderoptiontemp` (
	  `ordercode` varchar(25) NOT NULL DEFAULT '',
	  `productcode` varchar(18) NOT NULL DEFAULT '',
	  `opt_idx` varchar(10) NOT NULL DEFAULT '0',
	  `opt_name` text,
	  PRIMARY KEY (`ordercode`,`productcode`,`opt_idx`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblorderproduct` (
	  `vender` int(10) unsigned NOT NULL DEFAULT '0',
	  `ordercode` varchar(25) NOT NULL DEFAULT 'X',
	  `tempkey` varchar(32) NOT NULL DEFAULT '',
	  `productcode` varchar(18) NOT NULL DEFAULT '',
	  `productname` varchar(100) NOT NULL DEFAULT '',
	  `opt1_name` varchar(50) NOT NULL DEFAULT '',
	  `opt2_name` varchar(50) NOT NULL DEFAULT '',
	  `opt3_name` varchar(50) NOT NULL DEFAULT '',
	  `opt4_name` varchar(50) NOT NULL DEFAULT '',
	  `package_idx` smallint(5) unsigned NOT NULL DEFAULT '0',
	  `assemble_idx` tinyint(3) unsigned NOT NULL DEFAULT '0',
	  `addcode` varchar(200) NOT NULL DEFAULT '',
	  `quantity` smallint(5) DEFAULT NULL,
	  `price` int(11) DEFAULT NULL,
	  `reserve` int(11) NOT NULL DEFAULT '0',
	  `date` varchar(8) NOT NULL DEFAULT '',
	  `selfcode` varchar(20) NOT NULL DEFAULT '',
	  `productbisiness` varchar(200) NOT NULL DEFAULT '',
	  `deli_gbn` char(1) NOT NULL DEFAULT 'N',
	  `deli_com` char(3) DEFAULT NULL,
	  `deli_num` varchar(32) DEFAULT NULL,
	  `deli_date` varchar(14) DEFAULT NULL,
	  `order_prmsg` text,
	  `assemble_info` text NOT NULL,
	  `sell_memid` varchar(20) NOT NULL DEFAULT '',
	  `uid` int(11) unsigned NOT NULL AUTO_INCREMENT,
	  `status` varchar(2) NOT NULL DEFAULT '',
	  PRIMARY KEY (`vender`,`ordercode`,`tempkey`,`productcode`,`opt1_name`,`opt2_name`,`package_idx`,`assemble_idx`),
	  KEY `idx_tblorderproduct_1` (`ordercode`),
	  KEY `idx_tblorderproduct_2` (`date`),
	  KEY `idx_tblorderproduct_3` (`selfcode`),
	  KEY `uid` (`uid`)
	) ENGINE=MyISAM  DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblorderproducttemp` (
	  `vender` int(10) unsigned NOT NULL DEFAULT '0',
	  `ordercode` varchar(25) NOT NULL DEFAULT 'X',
	  `tempkey` varchar(32) NOT NULL DEFAULT '',
	  `productcode` varchar(18) NOT NULL DEFAULT '',
	  `productname` varchar(100) NOT NULL DEFAULT '',
	  `opt1_name` varchar(50) NOT NULL DEFAULT '',
	  `opt2_name` varchar(50) NOT NULL DEFAULT '',
	  `opt3_name` varchar(50) NOT NULL DEFAULT '',
	  `opt4_name` varchar(50) NOT NULL DEFAULT '',
	  `package_idx` smallint(5) unsigned NOT NULL DEFAULT '0',
	  `assemble_idx` tinyint(3) unsigned NOT NULL DEFAULT '0',
	  `addcode` varchar(200) NOT NULL DEFAULT '',
	  `quantity` smallint(5) DEFAULT NULL,
	  `price` int(11) DEFAULT NULL,
	  `reserve` int(11) NOT NULL DEFAULT '0',
	  `date` varchar(8) NOT NULL DEFAULT '',
	  `selfcode` varchar(20) NOT NULL DEFAULT '',
	  `productbisiness` varchar(200) NOT NULL DEFAULT '',
	  `deli_gbn` char(1) NOT NULL DEFAULT 'N',
	  `deli_com` char(3) DEFAULT NULL,
	  `deli_num` varchar(32) DEFAULT NULL,
	  `deli_date` varchar(14) DEFAULT NULL,
	  `order_prmsg` text,
	  `assemble_info` text NOT NULL,
	  `sell_memid` varchar(20) NOT NULL DEFAULT '',
	  `uid` int(11) unsigned NOT NULL AUTO_INCREMENT,
	  `status` varchar(2) NOT NULL DEFAULT '',
	  PRIMARY KEY (`vender`,`ordercode`,`tempkey`,`productcode`,`opt1_name`,`opt2_name`,`package_idx`,`assemble_idx`),
	  KEY `idx_tblorderproducttemp_1` (`ordercode`),
	  KEY `idx_tblorderproducttemp_2` (`date`),
	  KEY `idx_tblorderproducttemp_3` (`selfcode`),
	  KEY `uid` (`uid`)
	) ENGINE=MyISAM  DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblorder_social` (
	  `uid` int(11) unsigned NOT NULL AUTO_INCREMENT,
	  `pcode` varchar(18) NOT NULL DEFAULT '',
	  `rs_title` varchar(80) NOT NULL DEFAULT '',
	  `rs_content` text NOT NULL,
	  `regidate` int(11) NOT NULL DEFAULT '0',
	  PRIMARY KEY (`uid`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblpapercoupon` (
	  `coupon_code` varchar(10) NOT NULL DEFAULT '',
	  `coupon_name` varchar(100) NOT NULL DEFAULT '',
	  `description` varchar(255) NOT NULL DEFAULT '',
	  `date_start` varchar(10) NOT NULL DEFAULT '',
	  `date_end` varchar(10) NOT NULL DEFAULT '',
	  `sale_type` char(1) NOT NULL DEFAULT '',
	  `sale_money` int(11) unsigned NOT NULL DEFAULT '0',
	  `mini_price` int(11) unsigned NOT NULL DEFAULT '0',
	  `number_type` char(1) NOT NULL DEFAULT '',
	  `publish_limit` int(11) unsigned NOT NULL DEFAULT '0',
	  `display` char(1) NOT NULL DEFAULT 'Y',
	  `date` varchar(14) NOT NULL DEFAULT '',
	  PRIMARY KEY (`coupon_code`),
	  KEY `idx_tblcouponinfo_1` (`date`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblpapercoupon_code` (
	  `idx` int(11) NOT NULL AUTO_INCREMENT,
	  `coupon_code` varchar(10) NOT NULL,
	  `coupon_number` varchar(100) NOT NULL,
	  `state` char(1) NOT NULL DEFAULT 'N',
	  PRIMARY KEY (`idx`),
	  KEY `idx_tblpapercoupon_code_1` (`coupon_code`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblpartner` (
	  `id` varchar(20) NOT NULL DEFAULT '',
	  `passwd` varchar(20) NOT NULL DEFAULT '',
	  `url` varchar(100) NOT NULL DEFAULT '',
	  `hit_cnt` int(11) NOT NULL DEFAULT '0',
	  `authkey` varchar(32) NOT NULL DEFAULT '',
	  PRIMARY KEY (`id`),
	  UNIQUE KEY `idx_tblpartner_1` (`url`),
	  KEY `idx_tblpartner_2` (`id`,`authkey`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblpbankcode` (
	  `code` char(2) NOT NULL DEFAULT '',
	  `bank_name` varchar(20) NOT NULL DEFAULT '',
	  PRIMARY KEY (`code`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblpcardcode` (
	  `code` varchar(5) NOT NULL DEFAULT '',
	  `card_name` varchar(30) NOT NULL DEFAULT '',
	  PRIMARY KEY (`code`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblpcardlog` (
	  `ordercode` varchar(25) NOT NULL DEFAULT '',
	  `trans_code` varchar(50) NOT NULL DEFAULT '',
	  `pgtype` char(2) NOT NULL DEFAULT '',
	  `paymethod` char(1) NOT NULL DEFAULT '',
	  `pay_data` varchar(50) NOT NULL DEFAULT '',
	  `ok` char(1) NOT NULL DEFAULT '',
	  `status` char(1) NOT NULL DEFAULT 'N',
	  `okdate` varchar(14) NOT NULL DEFAULT '',
	  `edidate` varchar(14) NOT NULL DEFAULT '',
	  `canceldate` varchar(14) NOT NULL DEFAULT '',
	  `price` int(11) DEFAULT NULL,
	  `cardname` varchar(30) NOT NULL DEFAULT '',
	  `noinf` char(1) NOT NULL DEFAULT '',
	  `quota` char(2) NOT NULL DEFAULT '',
	  `ip` varchar(15) NOT NULL DEFAULT '',
	  `goodname` varchar(50) NOT NULL DEFAULT '',
	  `msg` varchar(200) NOT NULL DEFAULT '',
	  PRIMARY KEY (`ordercode`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblpersonal` (
	  `idx` int(11) unsigned NOT NULL AUTO_INCREMENT,
	  `id` varchar(20) NOT NULL DEFAULT '',
	  `name` varchar(20) NOT NULL DEFAULT '',
	  `email` varchar(50) DEFAULT NULL,
	  `ip` varchar(15) NOT NULL DEFAULT '',
	  `subject` varchar(200) NOT NULL DEFAULT '',
	  `date` varchar(14) NOT NULL DEFAULT '',
	  `content` text NOT NULL,
	  `re_date` varchar(14) DEFAULT NULL,
	  `re_content` text,
	  PRIMARY KEY (`idx`),
	  KEY `idx_tblpersonal_1` (`id`)
	) ENGINE=MyISAM  DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblpesterinfo` (
	  `code` int(11) NOT NULL DEFAULT '0',
	  `tempkey` varchar(32) NOT NULL DEFAULT '',
	  `id` varchar(20) NOT NULL DEFAULT '',
	  `sender_name` varchar(30) NOT NULL DEFAULT '',
	  `sender_email` varchar(50) NOT NULL DEFAULT '',
	  `sender_tel` varchar(30) NOT NULL DEFAULT '',
	  `receiver_name` varchar(30) NOT NULL DEFAULT '',
	  `receiver_tel1` varchar(30) NOT NULL DEFAULT '',
	  `receiver_tel2` varchar(30) NOT NULL DEFAULT '',
	  `receiver_post` varchar(7) NOT NULL,
	  `receiver_addr` varchar(150) NOT NULL,
	  `order_prmsg` text,
	  `pester_name` varchar(30) NOT NULL DEFAULT '',
	  `pester_email` varchar(50) NOT NULL DEFAULT '',
	  `pester_tel` varchar(30) NOT NULL DEFAULT '',
	  `pester_smstxt` text NOT NULL,
	  `pester_emailtxt` text NOT NULL,
	  `regdate` int(11) NOT NULL DEFAULT '0',
	  `state` int(11) NOT NULL DEFAULT '0',
	  `ordercode` varchar(25) NOT NULL,
	  PRIMARY KEY (`code`),
	  KEY `idx_tblpesterinfo2` (`regdate`),
	  KEY `idx_tblpesterinfo1` (`id`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblpmobilelog` (
	  `ordercode` varchar(25) NOT NULL DEFAULT '',
	  `trans_code` varchar(50) NOT NULL DEFAULT '',
	  `pgtype` char(2) NOT NULL DEFAULT '',
	  `pay_data` varchar(50) NOT NULL DEFAULT '',
	  `ok` char(1) NOT NULL DEFAULT '',
	  `okdate` varchar(14) NOT NULL DEFAULT '',
	  `canceldate` varchar(14) NOT NULL DEFAULT '',
	  `price` int(11) DEFAULT NULL,
	  `ip` varchar(15) NOT NULL DEFAULT '',
	  `goodname` varchar(50) NOT NULL DEFAULT '',
	  `msg` varchar(200) NOT NULL DEFAULT '',
	  PRIMARY KEY (`ordercode`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblpordercode` (
	  `ordercode` varchar(25) NOT NULL DEFAULT '',
	  `paymethod` char(1) NOT NULL DEFAULT '',
	  PRIMARY KEY (`ordercode`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblpostalcode` (
	  `seq` varchar(5) NOT NULL DEFAULT '',
	  `post` varchar(6) NOT NULL DEFAULT '',
	  `addr_do` varchar(10) NOT NULL DEFAULT '',
	  `addr_si` varchar(20) NOT NULL DEFAULT '',
	  `addr_dong` varchar(60) NOT NULL DEFAULT '',
	  `addr_bunji` varchar(50) DEFAULT NULL,
	  KEY `idx_tblpostalcode_1` (`seq`),
	  KEY `idx_tblpostalcode_2` (`addr_do`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblpresentcode` (
	  `code` varchar(20) NOT NULL DEFAULT '',
	  `ordercode` varchar(25) NOT NULL DEFAULT '',
	  PRIMARY KEY (`code`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblproduct` (
	  `pridx` int(11) unsigned NOT NULL AUTO_INCREMENT,
	  `productcode` varchar(18) NOT NULL DEFAULT '',
	  `productname` varchar(100) CHARACTER SET euckr COLLATE euckr_bin NOT NULL DEFAULT '',
	  `sellprice` int(11) DEFAULT NULL,
	  `assembleuse` char(1) NOT NULL DEFAULT 'N',
	  `consumerprice` int(11) DEFAULT NULL,
	  `discountRate` int(11) NOT NULL COMMENT '������',
	  `searchPrice` int(11) DEFAULT NULL,
	  `buyprice` int(11) DEFAULT NULL,
	  `reserve` varchar(7) NOT NULL DEFAULT '',
	  `reservetype` char(1) NOT NULL DEFAULT 'N',
	  `production` varchar(50) CHARACTER SET euckr COLLATE euckr_bin DEFAULT NULL,
	  `madein` varchar(30) CHARACTER SET euckr COLLATE euckr_bin DEFAULT NULL,
	  `model` varchar(50) CHARACTER SET euckr COLLATE euckr_bin DEFAULT NULL,
	  `brand` smallint(5) unsigned DEFAULT NULL,
	  `opendate` varchar(8) DEFAULT NULL,
	  `selfcode` varchar(20) NOT NULL DEFAULT '',
	  `bisinesscode` smallint(5) unsigned NOT NULL DEFAULT '0',
	  `quantity` int(11) DEFAULT NULL,
	  `group_check` char(1) NOT NULL DEFAULT 'N',
	  `option_quantity` varchar(250) DEFAULT NULL,
	  `option_price` varchar(250) DEFAULT NULL,
	  `option1` varchar(250) DEFAULT NULL,
	  `option2` varchar(250) DEFAULT NULL,
	  `keyword` varchar(100) CHARACTER SET euckr COLLATE euckr_bin NOT NULL DEFAULT '',
	  `userspec` text NOT NULL,
	  `tag` text NOT NULL,
	  `assembleproduct` text NOT NULL,
	  `addcode` varchar(200) DEFAULT NULL,
	  `maximage` varchar(30) DEFAULT NULL,
	  `minimage` varchar(30) DEFAULT NULL,
	  `tinyimage` varchar(30) DEFAULT NULL,
	  `wideimage` varchar(255) DEFAULT NULL,
	  `etctype` varchar(255) DEFAULT NULL,
	  `deli_price` int(11) unsigned NOT NULL DEFAULT '0',
	  `package_num` smallint(5) unsigned NOT NULL DEFAULT '0',
	  `etcapply_coupon` char(1) NOT NULL DEFAULT 'N',
	  `etcapply_reserve` char(1) NOT NULL DEFAULT 'N',
	  `etcapply_gift` char(1) NOT NULL DEFAULT 'N',
	  `etcapply_return` char(1) NOT NULL DEFAULT 'N' COMMENT '��ȯȯ�ҺҰ�',
	  `deli` char(1) NOT NULL DEFAULT 'N',
	  `display` char(1) NOT NULL DEFAULT 'Y',
	  `date` varchar(14) DEFAULT NULL,
	  `vender` int(10) unsigned NOT NULL DEFAULT '0',
	  `tagcount` smallint(5) unsigned NOT NULL DEFAULT '0',
	  `sellcount` smallint(5) unsigned NOT NULL DEFAULT '0',
	  `selldate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
	  `regdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
	  `modifydate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
	  `content` mediumtext,
	  `clicknum` int(11) NOT NULL DEFAULT '0',
	  `mobile_display` char(1) NOT NULL DEFAULT 'N',
	  `social_chk` char(1) NOT NULL DEFAULT 'N',
	  `gonggu_product` char(1) NOT NULL DEFAULT 'N',
	  `present_state` char(1) NOT NULL DEFAULT 'N',
	  `pester_state` char(1) NOT NULL DEFAULT 'N',
	  `sns_state` char(1) NOT NULL DEFAULT 'Y',
	  `sns_reserve1` varchar(7) NOT NULL DEFAULT '0',
	  `sns_reserve1_type` char(1) NOT NULL DEFAULT 'N',
	  `sns_reserve2` varchar(7) NOT NULL DEFAULT '0',
	  `sns_reserve2_type` char(1) NOT NULL DEFAULT 'N',
	  `first_reserve` varchar(7) NOT NULL DEFAULT '0',
	  `first_reserve_type` char(1) NOT NULL DEFAULT 'N',
	  `img_type` enum('0','1') NOT NULL DEFAULT '0',
	  `productdisprice` int(11) DEFAULT NULL,
	  `prmsg` varchar(255) DEFAULT NULL COMMENT '��ǰȫ������',
	  `syncNaverEp` enum('0','1') NOT NULL DEFAULT '1' COMMENT '���ļ��ο��� ���ϸ� 0',
	  `tax_yn` tinyint(4) NOT NULL DEFAULT '0' COMMENT '�������� 0:�Ϲ� 1:�����',
	  `reservation` date NOT NULL DEFAULT '0000-00-00' COMMENT '�����ǰ(�ǸŽ�����)',
	  PRIMARY KEY (`pridx`),
	  UNIQUE KEY `idx_product_1` (`productcode`),
	  KEY `idx_tblproduct_2` (`date`),
	  KEY `idx_tblproduct_3` (`vender`),
	  KEY `idx_tblproduct_4` (`tagcount`),
	  KEY `idx_tblproduct_5` (`brand`),
	  KEY `idx_tblproduct_6` (`selfcode`),
	  KEY `idx_tblproduct_7` (`group_check`),
	  KEY `idx_tblproduct_8` (`assembleuse`),
	  KEY `package_num` (`package_num`)
	) ENGINE=MyISAM  DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblproductbisiness` (
	  `companycode` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
	  `companyname` varchar(50) NOT NULL,
	  `companynum` varchar(40) DEFAULT NULL,
	  `companyowner` varchar(20) DEFAULT NULL,
	  `companypost` varchar(6) DEFAULT NULL,
	  `companyaddr` varchar(150) DEFAULT NULL,
	  `companybiz` varchar(40) DEFAULT NULL,
	  `companyitem` varchar(40) DEFAULT NULL,
	  `companytype` varchar(40) DEFAULT NULL,
	  `companycharge` varchar(20) NOT NULL,
	  `companychargeposition` varchar(20) DEFAULT NULL,
	  `companyemail` varchar(100) NOT NULL,
	  `companytel` varchar(40) DEFAULT NULL,
	  `companyhp` varchar(40) DEFAULT NULL,
	  `companyfax` varchar(40) DEFAULT NULL,
	  `companybank` varchar(40) DEFAULT NULL,
	  `companybanknum` varchar(40) DEFAULT NULL,
	  `companyurl` varchar(100) DEFAULT NULL,
	  `companymemo` text,
	  `companyview` char(4) DEFAULT NULL,
	  `companyviewval` varchar(200) DEFAULT NULL,
	  PRIMARY KEY (`companycode`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblproductbrand` (
	  `bridx` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
	  `brandname` varchar(50) CHARACTER SET euckr COLLATE euckr_bin NOT NULL DEFAULT '',
	  `list_type` varchar(5) NOT NULL DEFAULT 'L001',
	  `title_type` varchar(5) DEFAULT NULL,
	  `title_body` text,
	  PRIMARY KEY (`bridx`),
	  UNIQUE KEY `brandname` (`brandname`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblproductcode` (
	  `codeA` char(3) NOT NULL DEFAULT '',
	  `codeB` char(3) NOT NULL DEFAULT '',
	  `codeC` char(3) NOT NULL DEFAULT '',
	  `codeD` char(3) NOT NULL DEFAULT '',
	  `type` char(3) NOT NULL DEFAULT '',
	  `code_name` varchar(100) NOT NULL DEFAULT '',
	  `list_type` varchar(6) NOT NULL DEFAULT 'AL001',
	  `detail_type` varchar(6) NOT NULL DEFAULT 'AD001',
	  `sequence` varchar(4) DEFAULT NULL,
	  `sort` varchar(12) NOT NULL DEFAULT 'date',
	  `group_code` varchar(100) NOT NULL DEFAULT '',
	  `estimate_set` int(3) NOT NULL DEFAULT '999',
	  `noreserve` char(1) NOT NULL DEFAULT 'Y',
	  `special` varchar(10) NOT NULL DEFAULT '',
	  `special_cnt` varchar(30) NOT NULL DEFAULT '',
	  `islist` char(1) NOT NULL DEFAULT 'Y',
	  `title_type` varchar(5) DEFAULT NULL,
	  `title_body` text,
	  `mobile_display` char(1) NOT NULL DEFAULT 'Y',
	  `isgift` char(1) DEFAULT 'N',
	  `iscoupon` char(1) DEFAULT 'N',
	  `isrefund` char(1) DEFAULT 'N',
	  `isreserve` char(1) DEFAULT 'N',
	  `dsameparent` char(1) DEFAULT NULL COMMENT '�θ�� ���� ������ ����',
	  `syncNaverEp` enum('0','1') NOT NULL DEFAULT '1' COMMENT '���ļ��ο���',
	  PRIMARY KEY (`codeA`,`codeB`,`codeC`,`codeD`,`type`),
	  KEY `idx_tblproductcode_1` (`sequence`),
	  KEY `idx_tblproductcode_2` (`group_code`),
	  KEY `idx_tblproductcode_3` (`estimate_set`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblproductdesigntype` (
	  `code` varchar(5) NOT NULL DEFAULT '',
	  PRIMARY KEY (`code`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblproductgroupcode` (
	  `productcode` varchar(18) NOT NULL,
	  `group_code` varchar(4) NOT NULL,
	  PRIMARY KEY (`productcode`,`group_code`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblproductoption` (
	  `option_code` int(5) unsigned NOT NULL DEFAULT '0',
	  `description` varchar(250) NOT NULL DEFAULT '',
	  `option_choice` varchar(30) NOT NULL DEFAULT '',
	  `option_value01` text,
	  `option_value02` text,
	  `option_value03` text,
	  `option_value04` text,
	  `option_value05` text,
	  `option_value06` text,
	  `option_value07` text,
	  `option_value08` text,
	  `option_value09` text,
	  `option_value10` text,
	  PRIMARY KEY (`option_code`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblproductpackage` (
	  `num` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
	  `package_name` varchar(100) CHARACTER SET euckr COLLATE euckr_bin NOT NULL DEFAULT '',
	  `package_type` char(1) NOT NULL DEFAULT 'N',
	  `package_title` text NOT NULL,
	  `package_price` text NOT NULL,
	  `package_list` text,
	  PRIMARY KEY (`num`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblproductreview` (
	  `productcode` varchar(18) NOT NULL DEFAULT '',
	  `num` int(11) unsigned NOT NULL AUTO_INCREMENT,
	  `id` varchar(20) NOT NULL DEFAULT '',
	  `name` varchar(20) NOT NULL DEFAULT '',
	  `display` char(1) NOT NULL DEFAULT 'N',
	  `marks` tinyint(1) NOT NULL DEFAULT '5',
	  `quality` tinyint(2) DEFAULT NULL,
	  `price` tinyint(2) DEFAULT NULL,
	  `delitime` tinyint(2) DEFAULT NULL,
	  `recommend` varchar(2) DEFAULT NULL,
	  `best` char(1) DEFAULT 'N',
	  `reserve` smallint(5) NOT NULL DEFAULT '0',
	  `date` varchar(14) NOT NULL DEFAULT '',
	  `content` text NOT NULL,
	  `img` varchar(255) DEFAULT NULL,
	  `device` char(1) DEFAULT NULL,
	  PRIMARY KEY (`productcode`,`num`),
	  KEY `idx_tblproductreview_1` (`productcode`),
	  KEY `idx_tblproductreview_2` (`display`),
	  KEY `idx_tblproductreview_3` (`marks`),
	  KEY `idx_tblproductreview_4` (`date`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblproductselect` (
	  `type` char(2) NOT NULL DEFAULT '',
	  `num` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
	  `selectname` varchar(50) NOT NULL,
	  PRIMARY KEY (`type`,`num`),
	  KEY `idx_tblproductselect_1` (`selectname`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblproducttheme` (
	  `productcode` varchar(18) NOT NULL DEFAULT '',
	  `code` varchar(12) NOT NULL DEFAULT '',
	  `date` varchar(14) NOT NULL DEFAULT '',
	  PRIMARY KEY (`productcode`,`code`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblproduct_detail` (
	  `pridx` int(11) NOT NULL,
	  `didx` int(11) NOT NULL AUTO_INCREMENT,
	  `dtitle` varchar(255) NOT NULL,
	  `dcontent` text,
	  PRIMARY KEY (`pridx`,`didx`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblproduct_social` (
	  `pcode` varchar(18) NOT NULL DEFAULT '',
	  `sell_startdate` int(11) NOT NULL DEFAULT '0',
	  `sell_enddate` int(11) NOT NULL DEFAULT '0',
	  `complete_quantity` int(11) NOT NULL DEFAULT '0',
	  `sellcount_type` char(1) NOT NULL DEFAULT 'B',
	  `sellcount_add` int(11) NOT NULL DEFAULT '0',
	  `stock_state` char(1) NOT NULL DEFAULT 'N',
	  `discount_state` char(1) NOT NULL DEFAULT 'N',
	  `member_check` char(1) NOT NULL DEFAULT 'N',
	  `sellcount_member` tinyint(4) NOT NULL DEFAULT '0',
	  UNIQUE KEY `idx_pcode` (`pcode`),
	  KEY `sell_startdate` (`sell_startdate`),
	  KEY `sell_enddate` (`sell_enddate`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblptranslog` (
	  `ordercode` varchar(25) NOT NULL DEFAULT '',
	  `trans_code` varchar(50) NOT NULL DEFAULT '',
	  `pgtype` char(2) NOT NULL DEFAULT '',
	  `pay_data` varchar(50) NOT NULL DEFAULT '',
	  `ok` char(1) NOT NULL DEFAULT '',
	  `okdate` varchar(14) NOT NULL DEFAULT '',
	  `canceldate` varchar(14) NOT NULL DEFAULT '',
	  `price` int(11) DEFAULT NULL,
	  `bank_name` varchar(20) NOT NULL DEFAULT '',
	  `ip` varchar(15) NOT NULL DEFAULT '',
	  `goodname` varchar(50) NOT NULL DEFAULT '',
	  `msg` varchar(200) NOT NULL DEFAULT '',
	  PRIMARY KEY (`ordercode`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblpvirtuallog` (
	  `ordercode` varchar(25) NOT NULL DEFAULT '',
	  `trans_code` varchar(50) NOT NULL DEFAULT '',
	  `pgtype` char(2) NOT NULL DEFAULT '',
	  `paymethod` char(1) NOT NULL DEFAULT '',
	  `pay_data` varchar(50) NOT NULL DEFAULT '',
	  `ok` char(1) NOT NULL DEFAULT '',
	  `status` char(1) NOT NULL DEFAULT 'N',
	  `price` int(11) DEFAULT NULL,
	  `bank_price` int(11) DEFAULT NULL,
	  `sender_name` varchar(30) NOT NULL DEFAULT '',
	  `remitter` varchar(20) NOT NULL DEFAULT '',
	  `bank_code` char(2) NOT NULL DEFAULT '',
	  `account` varchar(30) NOT NULL DEFAULT '',
	  `okdate` varchar(14) NOT NULL DEFAULT '',
	  `bank_date` varchar(14) NOT NULL DEFAULT '',
	  `receive_date` varchar(14) NOT NULL DEFAULT '',
	  `noti_id` varchar(30) NOT NULL DEFAULT '',
	  `refund_account` varchar(30) NOT NULL DEFAULT '',
	  `refund_name` varchar(30) NOT NULL DEFAULT '',
	  `refund_bank_code` char(2) NOT NULL DEFAULT '',
	  `refund_price` int(11) DEFAULT NULL,
	  `refund_date` varchar(14) NOT NULL DEFAULT '',
	  `refund_receive_date` varchar(14) NOT NULL DEFAULT '',
	  `ip` varchar(15) NOT NULL DEFAULT '',
	  `goodname` varchar(50) NOT NULL DEFAULT '',
	  `msg` varchar(200) NOT NULL DEFAULT '',
	  PRIMARY KEY (`ordercode`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblquickmenu` (
	  `num` int(11) unsigned NOT NULL AUTO_INCREMENT,
	  `used` char(1) NOT NULL DEFAULT 'N',
	  `reg_date` varchar(14) NOT NULL DEFAULT '',
	  `design` char(3) NOT NULL DEFAULT '001',
	  `x_size` smallint(3) unsigned NOT NULL DEFAULT '420',
	  `y_size` smallint(3) unsigned NOT NULL DEFAULT '400',
	  `x_to` smallint(3) NOT NULL DEFAULT '0',
	  `y_to` smallint(3) NOT NULL DEFAULT '0',
	  `scroll_auto` char(1) NOT NULL DEFAULT 'Y',
	  `title` varchar(100) NOT NULL DEFAULT '',
	  `content` text NOT NULL,
	  PRIMARY KEY (`num`),
	  KEY `idx_tblquickmenu_1` (`used`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblrecomendlist` (
	  `rec_id` varchar(20) NOT NULL DEFAULT '',
	  `id` varchar(20) NOT NULL DEFAULT '',
	  `rec_id_reserve` int(11) unsigned NOT NULL DEFAULT '0',
	  `id_reserve` int(11) unsigned NOT NULL DEFAULT '0',
	  `date` varchar(14) NOT NULL DEFAULT '',
	  PRIMARY KEY (`rec_id`,`id`),
	  KEY `idx_tblrecomendlist_1` (`date`),
	  KEY `idx_tblrecomendlist_2` (`rec_id`),
	  KEY `idx_tblrecomendlist_3` (`id`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblrecomenLog` (
	  `idx` int(11) NOT NULL AUTO_INCREMENT,
	  `memId` varchar(50) NOT NULL,
	  `reserve` int(11) NOT NULL,
	  `type` varchar(20) NOT NULL,
	  PRIMARY KEY (`idx`)
	) ENGINE=MyISAM  DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblrecommendmanager` (
	  `rec_id` varchar(20) NOT NULL DEFAULT '',
	  `rec_cnt` int(11) unsigned NOT NULL DEFAULT '0',
	  `date` varchar(14) NOT NULL DEFAULT '',
	  PRIMARY KEY (`rec_id`),
	  KEY `idx_tblrecommendmanager_1` (`rec_cnt`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblregiststore` (
	  `id` varchar(20) NOT NULL DEFAULT '',
	  `vender` int(10) unsigned NOT NULL DEFAULT '0',
	  `email_yn` char(1) NOT NULL DEFAULT 'Y',
	  PRIMARY KEY (`id`,`vender`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblreserve` (
	  `ridx` int(11) NOT NULL AUTO_INCREMENT,
	  `id` varchar(20) NOT NULL DEFAULT '',
	  `reserve` int(11) NOT NULL DEFAULT '0',
	  `reserve_yn` char(1) NOT NULL DEFAULT 'Y',
	  `content` varchar(50) NOT NULL DEFAULT '',
	  `orderdata` varchar(40) NOT NULL DEFAULT '',
	  `date` varchar(14) NOT NULL DEFAULT '',
	  PRIMARY KEY (`id`,`date`,`ridx`),
	  KEY `idx_tblreserve_1` (`date`(8)),
	  KEY `idx_tblreserve_2` (`reserve_yn`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblreservefirst` (
	  `id` varchar(20) NOT NULL DEFAULT '',
	  `reserve` int(11) NOT NULL DEFAULT '0',
	  `ordercode` varchar(25) NOT NULL DEFAULT '',
	  `rec_id` varchar(20) NOT NULL DEFAULT '',
	  `date` varchar(14) NOT NULL DEFAULT '',
	  `cancelchk` enum('Y','N') NOT NULL DEFAULT 'N',
	  PRIMARY KEY (`id`,`ordercode`,`cancelchk`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblreturndata` (
	  `ordercode` varchar(25) NOT NULL DEFAULT '',
	  `date` varchar(14) NOT NULL DEFAULT '',
	  `return_data` text,
	  PRIMARY KEY (`ordercode`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblschedule` (
	  `idx` int(11) unsigned NOT NULL AUTO_INCREMENT,
	  `import` enum('N','Y') NOT NULL DEFAULT 'N',
	  `rest` enum('N','Y') NOT NULL DEFAULT 'N',
	  `subject` varchar(50) NOT NULL DEFAULT '',
	  `comment` varchar(200) NOT NULL DEFAULT '',
	  `duedate` varchar(8) NOT NULL DEFAULT '',
	  `duetime` char(2) NOT NULL DEFAULT '',
	  `date` varchar(14) NOT NULL DEFAULT '',
	  PRIMARY KEY (`idx`),
	  KEY `idx_tblschedule_1` (`duedate`,`duetime`),
	  KEY `idx_tblschedule_2` (`duedate`),
	  KEY `idx_tblschedule_3` (`rest`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblsecurityadmin` (
	  `id` varchar(20) NOT NULL DEFAULT '',
	  `passwd` varchar(60) NOT NULL DEFAULT '',
	  `admintype` tinyint(1) NOT NULL DEFAULT '0',
	  `adminname` varchar(20) NOT NULL DEFAULT '',
	  `adminemail` varchar(50) NOT NULL DEFAULT '',
	  `adminmobile` varchar(30) NOT NULL DEFAULT '',
	  `expirydate` int(11) unsigned NOT NULL DEFAULT '0',
	  `lastlogintime` int(11) unsigned NOT NULL DEFAULT '0',
	  `registerdate` int(11) unsigned NOT NULL DEFAULT '0',
	  `authkey` varchar(32) NOT NULL DEFAULT '',
	  `disabled` tinyint(1) unsigned NOT NULL DEFAULT '0',
	  PRIMARY KEY (`id`),
	  KEY `IX_tblsecurityadmin_1` (`authkey`),
	  KEY `IX_tblsecurityadmin_2` (`disabled`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblsecurityadminip` (
	  `idx` int(11) NOT NULL AUTO_INCREMENT,
	  `id` varchar(20) NOT NULL DEFAULT '',
	  `ipidx` int(11) NOT NULL DEFAULT '0',
	  PRIMARY KEY (`idx`),
	  KEY `idx_tblsecurityadminip_1` (`id`),
	  KEY `idx_tblsecurityadminip_2` (`ipidx`)
	) ENGINE=MyISAM  DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblsecurityadminlog` (
	  `idx` int(11) NOT NULL AUTO_INCREMENT,
	  `id` varchar(20) NOT NULL DEFAULT '',
	  `date` varchar(14) NOT NULL DEFAULT '',
	  `ip` varchar(15) NOT NULL DEFAULT '',
	  `content` varchar(250) NOT NULL DEFAULT '',
	  PRIMARY KEY (`idx`),
	  KEY `idx_tblsecurityadminlog_1` (`id`),
	  KEY `idx_tblsecurityadminlog_2` (`date`)
	) ENGINE=MyISAM  DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblsecurityadminrole` (
	  `idx` int(11) unsigned NOT NULL AUTO_INCREMENT,
	  `id` varchar(20) NOT NULL DEFAULT '',
	  `roleidx` int(11) unsigned NOT NULL DEFAULT '0',
	  PRIMARY KEY (`idx`),
	  KEY `idx_tblsecurityadminrole_1` (`id`),
	  KEY `idx_tblsecurityadminrole_2` (`roleidx`)
	) ENGINE=MyISAM  DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblsecurityiplist` (
	  `idx` int(11) NOT NULL AUTO_INCREMENT,
	  `ipaddress` varchar(20) NOT NULL DEFAULT '',
	  `description` varchar(100) NOT NULL DEFAULT '',
	  `disabled` tinyint(1) NOT NULL DEFAULT '0',
	  PRIMARY KEY (`idx`),
	  KEY `IX_tblsecurityiplist_1` (`disabled`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblsecurityrole` (
	  `idx` int(11) unsigned NOT NULL AUTO_INCREMENT,
	  `description` varchar(100) NOT NULL DEFAULT '',
	  `disabled` tinyint(1) unsigned NOT NULL DEFAULT '0',
	  PRIMARY KEY (`idx`),
	  KEY `idx_tblsecurityrole_1` (`disabled`)
	) ENGINE=MyISAM  DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblsecurityroletask` (
	  `idx` int(11) unsigned NOT NULL AUTO_INCREMENT,
	  `roleidx` int(11) unsigned NOT NULL DEFAULT '0',
	  `taskidx` int(11) unsigned NOT NULL DEFAULT '0',
	  PRIMARY KEY (`idx`),
	  KEY `idx_tblsecurityroletask_1` (`roleidx`),
	  KEY `idx_tblsecurityroletask_2` (`taskidx`)
	) ENGINE=MyISAM  DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblsecuritytask` (
	  `idx` int(11) unsigned NOT NULL AUTO_INCREMENT,
	  `taskcode` varchar(15) NOT NULL DEFAULT '',
	  `taskurl` varchar(50) NOT NULL DEFAULT '',
	  `description` varchar(100) NOT NULL DEFAULT '',
	  `taskgroupidx` int(11) NOT NULL DEFAULT '0',
	  `taskorder` int(11) NOT NULL DEFAULT '1',
	  `showmenu` tinyint(1) unsigned NOT NULL DEFAULT '1',
	  PRIMARY KEY (`idx`),
	  KEY `idx_tblsecuritytask_1` (`taskgroupidx`),
	  KEY `idx_tblsecuritytask_2` (`taskorder`)
	) ENGINE=MyISAM  DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblsecuritytaskgroup` (
	  `idx` int(11) unsigned NOT NULL AUTO_INCREMENT,
	  `taskgroupcode` char(2) NOT NULL DEFAULT '',
	  `taskgroupname` varchar(30) NOT NULL DEFAULT '',
	  PRIMARY KEY (`idx`),
	  UNIQUE KEY `idx_tblsecuritytaskgroup_1` (`taskgroupcode`)
	) ENGINE=MyISAM  DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblshopbillinfo` (
	  `license_no` varchar(50) NOT NULL,
	  `license_id` varchar(50) NOT NULL,
	  `domain` varchar(150) NOT NULL,
	  `partner_id` varchar(5) NOT NULL DEFAULT 'C0465',
	  `bill_state` char(1) NOT NULL DEFAULT 'N',
	  `sc_name` varchar(40) NOT NULL COMMENT '����� �̸�',
	  `sc_email` varchar(100) NOT NULL COMMENT '����� �̸���',
	  `sc_cell` varchar(20) NOT NULL COMMENT '������ڵ���',
	  `sc_phone` varchar(20) NOT NULL COMMENT '����� ��ȭ',
	  KEY `bill_state` (`bill_state`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblshopcount` (
	  `count` int(11) unsigned NOT NULL DEFAULT '0',
	  `vendercnt` smallint(5) unsigned NOT NULL DEFAULT '0'
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblshopcountday` (
	  `date` varchar(8) NOT NULL DEFAULT '',
	  `count` int(11) unsigned NOT NULL DEFAULT '0',
	  `login_cnt` smallint(5) unsigned NOT NULL DEFAULT '0',
	  `id_list` text NOT NULL,
	  PRIMARY KEY (`date`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblshopinfo` (
	  `shopname` varchar(50) NOT NULL DEFAULT '',
	  `shopurl` varchar(50) NOT NULL DEFAULT '',
	  `shoptitle` varchar(100) NOT NULL DEFAULT '',
	  `shopkeyword` varchar(100) NOT NULL DEFAULT '',
	  `shopdescription` varchar(100) NOT NULL DEFAULT '',
	  `companyname` varchar(40) NOT NULL DEFAULT '',
	  `companynum` varchar(20) NOT NULL DEFAULT '',
	  `companyowner` varchar(20) NOT NULL DEFAULT '',
	  `companypost` varchar(6) NOT NULL DEFAULT '',
	  `companyaddr` varchar(150) NOT NULL DEFAULT '',
	  `companybiz` varchar(40) NOT NULL DEFAULT '',
	  `companyitem` varchar(40) NOT NULL DEFAULT '',
	  `reportnum` varchar(50) NOT NULL DEFAULT '',
	  `info_email` varchar(50) NOT NULL DEFAULT '',
	  `info_tel` varchar(100) NOT NULL DEFAULT '',
	  `info_addr` varchar(150) NOT NULL DEFAULT '',
	  `privercyname` varchar(10) NOT NULL DEFAULT '',
	  `privercyemail` varchar(50) NOT NULL DEFAULT '',
	  `adult_type` char(1) NOT NULL DEFAULT 'N',
	  `frame_type` char(1) NOT NULL DEFAULT 'Y',
	  `align_type` char(1) NOT NULL DEFAULT 'Y',
	  `design_type` char(1) NOT NULL DEFAULT 'Y',
	  `top_type` varchar(10) NOT NULL DEFAULT 'top003',
	  `menu_type` varchar(10) NOT NULL DEFAULT 'menu003',
	  `main_type` varchar(10) NOT NULL DEFAULT 'main003',
	  `title_type` varchar(10) NOT NULL DEFAULT 'N',
	  `icon_type` varchar(10) NOT NULL DEFAULT '003',
	  `layoutdata` varchar(255) NOT NULL DEFAULT '',
	  `member_baro` char(1) NOT NULL DEFAULT 'N',
	  `member_buygrant` char(1) NOT NULL DEFAULT 'U',
	  `design_intro` char(3) NOT NULL DEFAULT '001',
	  `design_basket` char(3) NOT NULL DEFAULT '001',
	  `design_wishlist` char(3) NOT NULL DEFAULT '001',
	  `design_useinfo` char(3) NOT NULL DEFAULT '001',
	  `design_member` char(3) NOT NULL DEFAULT '001',
	  `design_mbjoin` char(3) NOT NULL DEFAULT '001',
	  `design_mbmodify` char(3) NOT NULL DEFAULT '001',
	  `design_order` char(3) NOT NULL DEFAULT 'T01',
	  `design_mypage` char(3) NOT NULL DEFAULT '001',
	  `design_orderlist` char(3) NOT NULL DEFAULT '001',
	  `design_mycoupon` char(3) NOT NULL DEFAULT '001',
	  `design_myreserve` char(3) NOT NULL DEFAULT '001',
	  `design_mypersonal` char(3) NOT NULL DEFAULT '001',
	  `design_mycustsect` char(3) NOT NULL DEFAULT '001',
	  `design_search` char(3) NOT NULL DEFAULT '001',
	  `design_mail` char(3) NOT NULL DEFAULT '001',
	  `design_notice` char(3) NOT NULL DEFAULT '001',
	  `design_information` char(3) NOT NULL DEFAULT '001',
	  `design_prnew` char(3) NOT NULL DEFAULT '001',
	  `design_prbest` char(3) NOT NULL DEFAULT '001',
	  `design_prhot` char(3) NOT NULL DEFAULT '001',
	  `design_prspecial` char(3) NOT NULL DEFAULT '001',
	  `design_tag` char(3) NOT NULL DEFAULT '001',
	  `design_tagsearch` char(3) NOT NULL DEFAULT '001',
	  `design_bmap` char(3) NOT NULL DEFAULT '001',
	  `payment_type` char(1) NOT NULL DEFAULT 'N',
	  `card_miniprice` int(7) NOT NULL DEFAULT '0',
	  `bank_miniprice` int(7) NOT NULL DEFAULT '0',
	  `card_payfee` tinyint(4) NOT NULL DEFAULT '0',
	  `card_splittype` char(1) NOT NULL DEFAULT 'N',
	  `card_splitmonth` char(2) NOT NULL DEFAULT '3',
	  `card_splitprice` int(7) unsigned NOT NULL DEFAULT '50000',
	  `rcall_type` char(1) NOT NULL DEFAULT 'Y',
	  `reserve_limit` int(7) NOT NULL DEFAULT '0',
	  `reserve_maxprice` int(10) unsigned NOT NULL DEFAULT '0',
	  `reserve_useadd` int(11) NOT NULL DEFAULT '-1',
	  `reserve_maxuse` int(7) NOT NULL DEFAULT '0',
	  `reserve_join` smallint(5) unsigned NOT NULL DEFAULT '0',
	  `coupon_ok` char(1) NOT NULL DEFAULT 'Y',
	  `coupon_limit_ok` char(1) NOT NULL DEFAULT 'N',
	  `deli_type` char(1) NOT NULL DEFAULT 'T',
	  `deli_basefee` int(5) NOT NULL DEFAULT '0',
	  `deli_basefeetype` char(1) NOT NULL DEFAULT 'Y',
	  `deli_miniprice` int(6) unsigned NOT NULL DEFAULT '0',
	  `deli_oneprprice` char(1) NOT NULL DEFAULT 'N',
	  `deli_setperiod` int(11) unsigned NOT NULL DEFAULT '1',
	  `deli_limit` varchar(200) NOT NULL DEFAULT '',
	  `main_hotprdt` varchar(10) NOT NULL DEFAULT '8|4|I',
	  `main_bestprdt` varchar(10) NOT NULL DEFAULT '8|4|I',
	  `main_newprdt` varchar(10) NOT NULL DEFAULT '8|4|I',
	  `main_specialprdt` varchar(10) DEFAULT '8|4|I',
	  `main_notice_num` tinyint(3) unsigned DEFAULT '5',
	  `main_special_num` tinyint(3) unsigned NOT NULL DEFAULT '3',
	  `main_info_num` tinyint(3) unsigned NOT NULL DEFAULT '4',
	  `main_code_display` char(1) NOT NULL DEFAULT 'N',
	  `main_special_type` char(1) NOT NULL DEFAULT 'N',
	  `recom_ok` char(1) NOT NULL DEFAULT 'N',
	  `recom_memreserve` int(5) NOT NULL DEFAULT '0',
	  `recom_addreserve` int(5) NOT NULL DEFAULT '0',
	  `recom_limit` smallint(5) DEFAULT NULL,
	  `prlist_num` tinyint(3) NOT NULL DEFAULT '10',
	  `proption_price` varchar(100) DEFAULT NULL,
	  `primg_minisize` char(3) NOT NULL DEFAULT '180',
	  `predit_type` char(1) NOT NULL DEFAULT 'Y',
	  `proption_size` char(3) NOT NULL DEFAULT '0',
	  `etctype` varchar(255) NOT NULL DEFAULT '',
	  `group_code` varchar(5) DEFAULT NULL,
	  `memberout_type` char(1) NOT NULL DEFAULT 'N',
	  `resno_type` char(1) NOT NULL DEFAULT 'N',
	  `ordercancel` char(1) NOT NULL DEFAULT '0',
	  `banner_loc` char(1) NOT NULL DEFAULT 'R',
	  `personal_ok` char(1) NOT NULL DEFAULT 'N',
	  `gift_type` varchar(10) NOT NULL DEFAULT 'N|N|A|N',
	  `quick_type` char(1) NOT NULL DEFAULT '0',
	  `review_type` char(1) NOT NULL DEFAULT 'Y',
	  `review_memtype` char(1) NOT NULL DEFAULT 'N',
	  `estimate_ok` char(1) NOT NULL DEFAULT 'N',
	  `oneshot_ok` char(1) NOT NULL DEFAULT 'N',
	  `coll_loc` char(1) NOT NULL DEFAULT '0',
	  `coll_num` char(1) NOT NULL DEFAULT '6',
	  `excel_ok` char(1) NOT NULL DEFAULT 'N',
	  `excel_info` varchar(100) NOT NULL DEFAULT ',0,1,2,4,5,8,9,10,11,12,18,19,20,21,22,23,',
	  `gong_num` tinyint(3) unsigned NOT NULL DEFAULT '10',
	  `auct_num` tinyint(3) unsigned NOT NULL DEFAULT '10',
	  `auct_sort` tinyint(1) unsigned NOT NULL DEFAULT '0',
	  `auct_moveday` tinyint(3) unsigned NOT NULL DEFAULT '0',
	  `shop_intro` text NOT NULL,
	  `social_intro` text NOT NULL,
	  `member_addform` text NOT NULL,
	  `order_msg` text NOT NULL,
	  `orderend_msg` text NOT NULL,
	  `estimate_msg` text NOT NULL,
	  `join_msg` text NOT NULL,
	  `deli_info` text NOT NULL,
	  `search_info` text NOT NULL,
	  `exposed_list` tinytext NOT NULL,
	  `deli_area` text NOT NULL,
	  `deli_area_limit` text NOT NULL,
	  `detail_info` text NOT NULL,
	  `bank_account` text NOT NULL,
	  `filter` text NOT NULL,
	  `css` text NOT NULL,
	  `etcfield` varchar(255) DEFAULT NULL,
	  `return1_type` char(1) NOT NULL DEFAULT '1',
	  `return2_type` char(1) NOT NULL DEFAULT '1',
	  `okcancel_msg` tinytext NOT NULL,
	  `nocancel_msg` tinytext NOT NULL,
	  `escrow_id` varchar(100) NOT NULL DEFAULT '',
	  `escrow_info` varchar(100) NOT NULL DEFAULT 'escrowcash=Y',
	  `trans_id` varchar(100) NOT NULL DEFAULT '',
	  `virtual_id` varchar(100) NOT NULL DEFAULT '',
	  `card_id` varchar(100) NOT NULL DEFAULT '',
	  `mobile_id` varchar(100) NOT NULL DEFAULT '',
	  `adultauth` varchar(40) NOT NULL DEFAULT '',
	  `multi_distype` char(1) NOT NULL DEFAULT '0',
	  `multi_dispos` char(1) NOT NULL DEFAULT '0',
	  `multi_changetype` char(1) NOT NULL DEFAULT '0',
	  `multi_bgcolor` varchar(7) NOT NULL DEFAULT '#FFFFFF',
	  `ssl_type` char(1) NOT NULL DEFAULT 'N',
	  `ssl_domain` varchar(50) NOT NULL DEFAULT '',
	  `ssl_port` varchar(5) NOT NULL DEFAULT '',
	  `ssl_page` varchar(100) NOT NULL DEFAULT '',
	  `tax_cnum` varchar(10) NOT NULL DEFAULT '',
	  `tax_cname` varchar(30) NOT NULL DEFAULT '',
	  `tax_cowner` varchar(15) NOT NULL DEFAULT '',
	  `tax_caddr` varchar(50) NOT NULL DEFAULT '',
	  `tax_ctel` varchar(14) NOT NULL DEFAULT '',
	  `tax_type` char(1) NOT NULL DEFAULT 'N',
	  `tax_rate` tinyint(3) unsigned NOT NULL DEFAULT '0',
	  `tax_mid` varchar(4) NOT NULL DEFAULT '',
	  `tax_tid` varchar(6) NOT NULL DEFAULT '',
	  `tax_scd` varchar(5) DEFAULT NULL,
	  `regdate` varchar(14) NOT NULL DEFAULT '',
	  `regip` varchar(15) NOT NULL DEFAULT '',
	  `cr_ok` char(1) NOT NULL DEFAULT 'N',
	  `cr_maxprice` int(11) unsigned NOT NULL DEFAULT '0',
	  `cr_unit` int(11) unsigned NOT NULL DEFAULT '0',
	  `cr_limit` int(4) unsigned NOT NULL DEFAULT '0',
	  `cr_sdate` int(4) unsigned NOT NULL DEFAULT '0',
	  `cr_edate` int(4) unsigned NOT NULL DEFAULT '0',
	  `recom_memreserve_type` varchar(10) NOT NULL DEFAULT '',
	  `recom_url_ok` char(1) NOT NULL DEFAULT 'N',
	  `sns_ok` char(1) NOT NULL DEFAULT 'N',
	  `sns_reserve_type` varchar(10) NOT NULL DEFAULT '0',
	  `sns_recomreserve` int(5) NOT NULL DEFAULT '0',
	  `sns_memreserve` int(5) NOT NULL DEFAULT '0',
	  `auto_order_cancel` char(1) NOT NULL DEFAULT 'N',
	  `pester_state` char(1) NOT NULL DEFAULT 'N',
	  `wholesalemember` char(1) NOT NULL DEFAULT 'N' COMMENT '����ȸ�� ��� ����'
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblshopsnsinfo` (
	  `type` char(1) NOT NULL DEFAULT '',
	  `appid` varchar(40) NOT NULL,
	  `secret` varchar(80) NOT NULL,
	  `icon_img` varchar(100) NOT NULL DEFAULT '',
	  `state` char(1) NOT NULL DEFAULT 'N',
	  PRIMARY KEY (`type`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblsmsaddress` (
	  `name` varchar(20) NOT NULL DEFAULT '',
	  `mobile` varchar(14) NOT NULL DEFAULT '',
	  `addr_group` varchar(20) NOT NULL DEFAULT '',
	  `memo` varchar(150) DEFAULT NULL,
	  `date` varchar(14) NOT NULL DEFAULT '',
	  PRIMARY KEY (`mobile`),
	  KEY `idx_tblsmsaddress_1` (`name`),
	  KEY `idx_tblsmsaddress_2` (`addr_group`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblsmsinfo` (
	  `id` varchar(20) NOT NULL DEFAULT '',
	  `authkey` varchar(32) NOT NULL DEFAULT '',
	  `sms_uname` varchar(30) NOT NULL DEFAULT '',
	  `mem_join` char(1) DEFAULT 'N',
	  `mem_order` char(1) DEFAULT 'N',
	  `mem_ordervender` char(1) DEFAULT 'N',
	  `mem_delivery` char(1) DEFAULT 'N',
	  `mem_delinum` char(1) DEFAULT 'N',
	  `mem_bank` char(1) DEFAULT 'N',
	  `mem_bankok` char(1) DEFAULT 'N',
	  `mem_bankokvender` char(1) DEFAULT 'N',
	  `mem_birth` char(1) DEFAULT 'N',
	  `mem_auth` char(1) DEFAULT 'N',
	  `mem_passwd` char(1) DEFAULT 'N',
	  `admin_join` char(1) DEFAULT 'N',
	  `admin_order` char(1) DEFAULT 'N',
	  `vender_order` char(1) DEFAULT 'N',
	  `admin_cancel` char(1) DEFAULT 'N',
	  `admin_board` char(1) DEFAULT 'N',
	  `admin_soldout` char(1) DEFAULT 'N',
	  `msg_mem_join` varchar(100) DEFAULT NULL,
	  `msg_mem_order` varchar(100) DEFAULT NULL,
	  `msg_mem_delivery` varchar(100) DEFAULT NULL,
	  `msg_mem_delinum` varchar(100) DEFAULT NULL,
	  `msg_mem_bank` varchar(100) DEFAULT NULL,
	  `msg_mem_bankok` varchar(100) DEFAULT NULL,
	  `msg_mem_birth` varchar(100) DEFAULT NULL,
	  `msg_mem_auth` varchar(100) DEFAULT NULL,
	  `admin_tel` varchar(15) DEFAULT NULL,
	  `subadmin1_tel` varchar(15) DEFAULT NULL,
	  `subadmin2_tel` varchar(15) DEFAULT NULL,
	  `subadmin3_tel` varchar(15) DEFAULT NULL,
	  `sleep_time1` tinyint(3) unsigned DEFAULT '0',
	  `sleep_time2` tinyint(3) unsigned DEFAULT '0',
	  `return_tel` varchar(15) DEFAULT NULL,
	  `use_mms` enum('N','Y') NOT NULL DEFAULT 'N',
	  `mem_gift` char(1) DEFAULT 'N',
	  `msg_mem_gift` varchar(100) DEFAULT NULL,
	  `socialshopping` char(1) DEFAULT 'N',
	  `msg_socialshopping` varchar(100) DEFAULT NULL,
	  `product_hongbo` char(1) DEFAULT 'N',
	  `mem_present` char(1) NOT NULL DEFAULT 'N',
	  `msg_mem_present` varchar(100) DEFAULT NULL,
	  `mem_pester` char(1) NOT NULL DEFAULT 'N',
	  `msg_mem_pester` varchar(100) DEFAULT NULL,
	  `mem_join_msg` varchar(100) DEFAULT NULL,
	  `pr_buy_msg` varchar(100) DEFAULT NULL,
	  `pr_cancel_msg` varchar(100) DEFAULT NULL,
	  `pr_soldout_msg` varchar(100) DEFAULT NULL,
	  `new_post_msg` varchar(100) DEFAULT NULL
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblsnsboard` (
	  `code` varchar(20) NOT NULL DEFAULT '',
	  `board` varchar(30) NOT NULL DEFAULT '',
	  `num` int(11) NOT NULL DEFAULT '0',
	  `id` varchar(20) DEFAULT NULL,
	  `chk` int(1) NOT NULL DEFAULT '0',
	  PRIMARY KEY (`code`),
	  KEY `idx_tblsnsboard1` (`code`),
	  KEY `idx_tblsnsboard2` (`board`,`num`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblsnscomment` (
	  `seq` int(11) NOT NULL DEFAULT '0',
	  `id` varchar(20) NOT NULL DEFAULT '',
	  `pcode` varchar(18) NOT NULL DEFAULT '',
	  `comment` text,
	  `sns_type` varchar(10) NOT NULL DEFAULT '',
	  `regidate` int(11) NOT NULL DEFAULT '0',
	  PRIMARY KEY (`seq`),
	  KEY `idx_tblsnsproduct1` (`id`,`pcode`),
	  KEY `idx_tblsnscomment2` (`regidate`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblsnsGonggu` (
	  `code` varchar(20) NOT NULL DEFAULT '',
	  `id` varchar(20) NOT NULL DEFAULT '',
	  `pcode` varchar(18) NOT NULL DEFAULT '',
	  `count` int(11) NOT NULL DEFAULT '0',
	  PRIMARY KEY (`code`),
	  KEY `idx_tblsnsproduct1` (`id`,`pcode`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblsnsGongguCmt` (
	  `seq` int(11) NOT NULL DEFAULT '0',
	  `id` varchar(20) NOT NULL DEFAULT '',
	  `c_seq` int(11) NOT NULL DEFAULT '0',
	  `c_order` int(11) NOT NULL DEFAULT '0',
	  `pcode` varchar(18) NOT NULL DEFAULT '',
	  `comment` text,
	  `sns_type` varchar(10) NOT NULL DEFAULT '',
	  `count` int(11) NOT NULL DEFAULT '0',
	  `regidate` int(11) NOT NULL DEFAULT '0',
	  `rqt_state` enum('1','2','3','4') NOT NULL DEFAULT '1',
	  `etc` varchar(5) DEFAULT NULL,
	  `reg_prdt` varchar(18) NOT NULL DEFAULT 'N',
	  `send_chk` char(1) NOT NULL DEFAULT 'N',
	  PRIMARY KEY (`seq`),
	  KEY `idx_tblsnsproduct1` (`id`,`pcode`),
	  KEY `idx_tblsnscomment2` (`regidate`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblsnsproduct` (
	  `code` varchar(20) NOT NULL DEFAULT '',
	  `id` varchar(20) NOT NULL DEFAULT '',
	  `pcode` varchar(18) NOT NULL DEFAULT '',
	  `count` int(11) NOT NULL DEFAULT '0',
	  PRIMARY KEY (`code`),
	  KEY `idx_tblsnsproduct1` (`id`,`pcode`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblsnsproductLog` (
	  `idx` int(11) NOT NULL AUTO_INCREMENT COMMENT '�ε���',
	  `code` varchar(20) NOT NULL COMMENT 'ȫ���ڵ�',
	  `authkey` varchar(80) NOT NULL COMMENT '����Ű',
	  `memid` varchar(20) NOT NULL COMMENT 'ȸ�����̵�',
	  `ordercode` varchar(25) NOT NULL COMMENT '�ֹ��ڵ�',
	  `accessTime` datetime NOT NULL COMMENT '���ٽð�',
	  `orderTime` datetime NOT NULL COMMENT '�ֹ��ð�',
	  `orderOkTime` datetime NOT NULL COMMENT '�����ݿϷ�ð�',
	  PRIMARY KEY (`idx`)
	) ENGINE=MyISAM  DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblsocial_mailing` (
	  `idx` int(11) NOT NULL AUTO_INCREMENT,
	  `email` varchar(50) NOT NULL DEFAULT '',
	  `mobile` varchar(50) NOT NULL DEFAULT '',
	  `regidate` int(11) NOT NULL,
	  `state` char(1) NOT NULL DEFAULT 'Y',
	  PRIMARY KEY (`idx`)
	) ENGINE=MyISAM  DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblspecialcode` (
	  `code` varchar(12) NOT NULL DEFAULT '',
	  `special` char(1) NOT NULL DEFAULT '',
	  `special_list` text,
	  PRIMARY KEY (`code`,`special`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblspecialmain` (
	  `special` char(1) NOT NULL DEFAULT '',
	  `special_list` text,
	  PRIMARY KEY (`special`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblsurveymain` (
	  `survey_code` varchar(20) NOT NULL DEFAULT '',
	  `time_start` int(11) unsigned NOT NULL DEFAULT '0',
	  `time_end` int(11) unsigned NOT NULL DEFAULT '0',
	  `display` char(1) NOT NULL DEFAULT '',
	  `ip_yn` char(1) NOT NULL DEFAULT 'N',
	  `grant_type` char(2) NOT NULL DEFAULT 'YY',
	  `survey_content` varchar(150) NOT NULL DEFAULT '',
	  `survey_select1` varchar(100) NOT NULL DEFAULT '',
	  `survey_select2` varchar(100) NOT NULL DEFAULT '',
	  `survey_select3` varchar(100) NOT NULL DEFAULT '',
	  `survey_select4` varchar(100) NOT NULL DEFAULT '',
	  `survey_select5` varchar(100) NOT NULL DEFAULT '',
	  `survey_cnt1` smallint(5) unsigned NOT NULL DEFAULT '0',
	  `survey_cnt2` smallint(5) unsigned NOT NULL DEFAULT '0',
	  `survey_cnt3` smallint(5) unsigned NOT NULL DEFAULT '0',
	  `survey_cnt4` smallint(5) unsigned NOT NULL DEFAULT '0',
	  `survey_cnt5` smallint(5) unsigned NOT NULL DEFAULT '0',
	  PRIMARY KEY (`survey_code`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblsurveyresult` (
	  `survey_code` varchar(20) NOT NULL DEFAULT '',
	  `no` int(8) unsigned NOT NULL DEFAULT '0',
	  `name` varchar(10) NOT NULL DEFAULT '',
	  `ip` varchar(15) NOT NULL DEFAULT '',
	  `subject` varchar(250) NOT NULL DEFAULT '',
	  `date` varchar(14) NOT NULL DEFAULT '',
	  PRIMARY KEY (`survey_code`,`no`),
	  KEY `idx_tblsurveyresult` (`date`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tbltagproduct` (
	  `productcode` varchar(18) NOT NULL DEFAULT '',
	  `tagname` varchar(50) CHARACTER SET euckr COLLATE euckr_bin NOT NULL DEFAULT '',
	  `cnt` smallint(5) unsigned NOT NULL DEFAULT '1',
	  `ids` text NOT NULL,
	  PRIMARY KEY (`productcode`,`tagname`),
	  KEY `idx_tbltagproduct_1` (`cnt`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tbltagsearch` (
	  `date` varchar(8) NOT NULL DEFAULT '',
	  `tagname` varchar(50) CHARACTER SET euckr COLLATE euckr_bin NOT NULL DEFAULT '',
	  `cnt` smallint(5) unsigned NOT NULL DEFAULT '1',
	  PRIMARY KEY (`date`,`tagname`),
	  KEY `idx_tbltagsearch_1` (`date`),
	  KEY `idx_tbltagsearch_2` (`cnt`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tbltagsearchall` (
	  `tagname` varchar(50) CHARACTER SET euckr COLLATE euckr_bin NOT NULL DEFAULT '',
	  `cnt` int(11) unsigned NOT NULL DEFAULT '1',
	  PRIMARY KEY (`tagname`),
	  KEY `idx_tbltagsearchall_1` (`cnt`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tbltaxsavelist` (
	  `ordercode` varchar(25) NOT NULL DEFAULT '',
	  `tsdtime` varchar(14) NOT NULL DEFAULT '',
	  `tr_code` char(1) DEFAULT NULL,
	  `tax_no` varchar(10) DEFAULT NULL,
	  `id_info` varchar(20) DEFAULT NULL,
	  `name` varchar(20) DEFAULT NULL,
	  `tel` varchar(14) DEFAULT NULL,
	  `email` varchar(30) DEFAULT NULL,
	  `productname` varchar(30) DEFAULT NULL,
	  `amt1` varchar(12) DEFAULT NULL,
	  `amt2` varchar(12) DEFAULT NULL,
	  `amt3` varchar(12) DEFAULT NULL,
	  `amt4` varchar(12) DEFAULT NULL,
	  `type` char(1) DEFAULT 'N',
	  `authno` varchar(9) DEFAULT NULL,
	  `mtrsno` varchar(12) DEFAULT NULL,
	  `oktime` varchar(14) NOT NULL DEFAULT '',
	  `error_msg` varchar(100) DEFAULT NULL,
	  PRIMARY KEY (`ordercode`),
	  KEY `idx_tbltaxsavelist_1` (`tsdtime`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tbltempletinfo` (
	  `icon_type` smallint(3) unsigned zerofill NOT NULL DEFAULT '001',
	  `top_type` varchar(10) NOT NULL DEFAULT '',
	  `main_type` varchar(10) NOT NULL DEFAULT '',
	  `menu_type` varchar(10) NOT NULL DEFAULT '',
	  `frame_type` char(1) NOT NULL DEFAULT 'N',
	  `top_height` int(3) NOT NULL DEFAULT '55',
	  `rightmargin` tinyint(2) NOT NULL DEFAULT '0',
	  `default_css` text,
	  PRIMARY KEY (`icon_type`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblvenderaccount` (
	  `vender` int(10) unsigned NOT NULL DEFAULT '0',
	  `date` varchar(8) NOT NULL DEFAULT '',
	  `price` int(11) NOT NULL DEFAULT '0',
	  `confirm` char(1) NOT NULL DEFAULT 'N',
	  `bank_account` varchar(100) NOT NULL DEFAULT '',
	  `memo` text,
	  PRIMARY KEY (`vender`,`date`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblvenderadminnotice` (
	  `vender` int(10) unsigned NOT NULL DEFAULT '0',
	  `date` varchar(14) NOT NULL DEFAULT '',
	  `access` int(11) unsigned NOT NULL DEFAULT '0',
	  `ip` varchar(15) NOT NULL DEFAULT '',
	  `subject` varchar(150) NOT NULL DEFAULT '',
	  `content` text NOT NULL,
	  PRIMARY KEY (`vender`,`date`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblvenderadminqna` (
	  `vender` int(10) unsigned NOT NULL DEFAULT '0',
	  `date` varchar(14) NOT NULL DEFAULT '',
	  `access` int(11) unsigned NOT NULL DEFAULT '0',
	  `subject` varchar(150) NOT NULL DEFAULT '',
	  `filename` varchar(50) NOT NULL DEFAULT '',
	  `content` text NOT NULL,
	  `re_date` varchar(14) DEFAULT NULL,
	  `re_content` text,
	  PRIMARY KEY (`vender`,`date`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblvenderboxgroupcolor` (
	  `seq` int(11) unsigned NOT NULL AUTO_INCREMENT,
	  `color` varchar(6) NOT NULL DEFAULT '',
	  `leftcolor` varchar(6) NOT NULL DEFAULT '',
	  `fontcolor` varchar(6) NOT NULL DEFAULT '',
	  PRIMARY KEY (`seq`)
	) ENGINE=MyISAM  DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblvendercodedesign` (
	  `vender` int(10) unsigned NOT NULL DEFAULT '0',
	  `code` char(3) NOT NULL DEFAULT '',
	  `tgbn` char(2) NOT NULL DEFAULT '10',
	  `hot_used` char(1) NOT NULL DEFAULT '0',
	  `hot_dispseq` tinyint(3) unsigned DEFAULT NULL,
	  `hot_linktype` char(1) NOT NULL DEFAULT '1',
	  `code_toptype` varchar(5) DEFAULT NULL,
	  `code_topdesign` text,
	  PRIMARY KEY (`vender`,`code`,`tgbn`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblvenderfee` (
	  `vender` int(10) unsigned NOT NULL DEFAULT '0',
	  `date` varchar(6) NOT NULL DEFAULT '',
	  `memo` text,
	  PRIMARY KEY (`vender`,`date`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblvenderinfo` (
	  `vender` int(10) unsigned NOT NULL AUTO_INCREMENT,
	  `id` varchar(20) NOT NULL DEFAULT '',
	  `passwd` varchar(60) NOT NULL DEFAULT '',
	  `grant_product` varchar(4) NOT NULL DEFAULT 'NNNN',
	  `product_max` int(11) unsigned NOT NULL DEFAULT '0',
	  `rate` tinyint(3) unsigned NOT NULL DEFAULT '0',
	  `bank_account` varchar(100) NOT NULL DEFAULT '',
	  `account_date` varchar(100) NOT NULL,
	  `fee_type` varchar(20) NOT NULL DEFAULT 'N',
	  `fee_price` int(11) unsigned DEFAULT NULL,
	  `deli_super` char(1) NOT NULL DEFAULT 'N',
	  `deli_price` int(5) NOT NULL DEFAULT '0',
	  `deli_pricetype` char(1) NOT NULL DEFAULT 'Y',
	  `deli_mini` int(6) unsigned NOT NULL DEFAULT '0',
	  `deli_area` text NOT NULL,
	  `deli_limit` varchar(200) NOT NULL DEFAULT '',
	  `deli_area_limit` text NOT NULL,
	  `com_name` varchar(40) NOT NULL DEFAULT '',
	  `com_num` varchar(20) NOT NULL DEFAULT '',
	  `com_owner` varchar(20) NOT NULL DEFAULT '',
	  `com_post` varchar(6) NOT NULL DEFAULT '',
	  `com_addr` varchar(150) NOT NULL DEFAULT '',
	  `com_biz` varchar(40) NOT NULL DEFAULT '',
	  `com_item` varchar(40) NOT NULL DEFAULT '',
	  `com_tel` varchar(20) DEFAULT NULL,
	  `com_fax` varchar(20) DEFAULT NULL,
	  `com_homepage` varchar(50) NOT NULL DEFAULT '',
	  `p_name` varchar(10) NOT NULL DEFAULT '',
	  `p_mobile` varchar(14) NOT NULL DEFAULT '',
	  `p_email` varchar(50) NOT NULL DEFAULT '',
	  `p_buseo` varchar(20) NOT NULL DEFAULT '',
	  `p_level` varchar(20) NOT NULL DEFAULT '',
	  `logindate` varchar(14) NOT NULL DEFAULT '',
	  `authkey` varchar(32) NOT NULL DEFAULT '',
	  `regdate` varchar(14) NOT NULL DEFAULT '',
	  `disabled` tinyint(1) unsigned NOT NULL DEFAULT '1',
	  `delflag` char(1) NOT NULL DEFAULT 'N',
	  `com_image` varchar(30) NOT NULL COMMENT '��ǥ�̹���',
	  `ec_num` varchar(20) NOT NULL COMMENT '����ǸŽŰ�',
	  `com_type` varchar(20) NOT NULL COMMENT '����ڱ���',
	  `com_nametech` int(1) NOT NULL DEFAULT '0' COMMENT '������ �������',
	  PRIMARY KEY (`vender`),
	  UNIQUE KEY `idx_tblvenderinfo_1` (`id`),
	  KEY `idx_tblvenderinfo_2` (`delflag`)
	) ENGINE=MyISAM  DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblvenderlog` (
	  `vender` int(10) unsigned NOT NULL DEFAULT '0',
	  `date` varchar(14) NOT NULL DEFAULT '',
	  `ip` varchar(15) NOT NULL DEFAULT '',
	  `content` varchar(250) NOT NULL DEFAULT '',
	  PRIMARY KEY (`date`),
	  KEY `idx_tblvenderlog_1` (`vender`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblvendernotice` (
	  `vender` int(10) unsigned NOT NULL DEFAULT '0',
	  `date` varchar(14) NOT NULL DEFAULT '',
	  `access` int(11) unsigned NOT NULL DEFAULT '0',
	  `ip` varchar(15) NOT NULL DEFAULT '',
	  `subject` varchar(150) NOT NULL DEFAULT '',
	  `content` text NOT NULL,
	  PRIMARY KEY (`vender`,`date`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblVenderProposal` (
	  `idx` int(11) NOT NULL AUTO_INCREMENT,
	  `type` text NOT NULL,
	  `category` text NOT NULL,
	  `company` varchar(50) NOT NULL,
	  `comp_zip` varchar(7) NOT NULL,
	  `comp_addr1` varchar(200) NOT NULL,
	  `comp_addr2` varchar(200) NOT NULL,
	  `comp_site` varchar(50) NOT NULL,
	  `pre_sell` varchar(20) NOT NULL,
	  `comp_mem_no` varchar(10) NOT NULL,
	  `etc_mall` varchar(200) NOT NULL,
	  `mng_name` varchar(20) NOT NULL,
	  `mng_tell` varchar(20) NOT NULL,
	  `mng_phone` varchar(20) NOT NULL,
	  `mng_mail` varchar(100) NOT NULL,
	  `contents` text NOT NULL,
	  `mem_id` varchar(30) NOT NULL,
	  `reg_date` datetime NOT NULL,
	  `smsMSG` varchar(90) NOT NULL,
	  `mailMSG` text NOT NULL,
	  `chk_date` datetime NOT NULL,
	  PRIMARY KEY (`idx`)
	) ENGINE=MyISAM  DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblVenderProposalType` (
	  `idx` int(11) NOT NULL AUTO_INCREMENT,
	  `name` varchar(20) NOT NULL,
	  PRIMARY KEY (`idx`)
	) ENGINE=MyISAM  DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblvendersectdisplist` (
	  `seq` int(11) unsigned NOT NULL AUTO_INCREMENT,
	  `disptype` varchar(10) NOT NULL DEFAULT '',
	  `dispname` varchar(50) NOT NULL DEFAULT '',
	  `dispcnt` tinyint(3) unsigned DEFAULT NULL,
	  `disabled` tinyint(1) unsigned NOT NULL DEFAULT '0',
	  PRIMARY KEY (`seq`)
	) ENGINE=MyISAM  DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblvendersession` (
	  `authkey` varchar(32) NOT NULL DEFAULT '',
	  `vender` int(10) unsigned NOT NULL DEFAULT '0',
	  `ip` varchar(15) NOT NULL DEFAULT '',
	  `date` varchar(14) NOT NULL DEFAULT '',
	  PRIMARY KEY (`authkey`),
	  KEY `idx_tblvendersession_1` (`vender`),
	  KEY `idx_tblvendersession_2` (`date`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblvenderspecialcode` (
	  `vender` int(10) unsigned NOT NULL DEFAULT '0',
	  `code` char(3) NOT NULL DEFAULT '',
	  `tgbn` char(2) NOT NULL DEFAULT '10',
	  `special` varchar(10) NOT NULL DEFAULT '',
	  `special_list` text,
	  PRIMARY KEY (`vender`,`code`,`tgbn`,`special`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblvenderspecialmain` (
	  `vender` int(10) unsigned NOT NULL DEFAULT '0',
	  `special` varchar(10) NOT NULL DEFAULT '',
	  `special_list` text,
	  PRIMARY KEY (`vender`,`special`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblvenderstore` (
	  `vender` int(10) unsigned NOT NULL DEFAULT '0',
	  `id` varchar(20) NOT NULL DEFAULT '',
	  `grade` char(2) NOT NULL DEFAULT '',
	  `brand_name` varchar(30) NOT NULL DEFAULT '',
	  `brand_description` varchar(200) NOT NULL DEFAULT '',
	  `shop_width` smallint(5) NOT NULL DEFAULT '980',
	  `skin` varchar(10) NOT NULL DEFAULT '',
	  `cust_info` varchar(200) NOT NULL DEFAULT '',
	  `deli_info` text NOT NULL,
	  `code_distype` char(2) NOT NULL DEFAULT 'YY',
	  `hot_used` char(1) NOT NULL DEFAULT '0',
	  `hot_dispseq` tinyint(3) unsigned DEFAULT NULL,
	  `hot_linktype` char(1) NOT NULL DEFAULT '1',
	  `new_used` char(1) NOT NULL DEFAULT '0',
	  `new_dispseq` tinyint(3) unsigned DEFAULT NULL,
	  `prlist_display` char(1) NOT NULL DEFAULT 'I',
	  `prlist_num` tinyint(2) unsigned NOT NULL DEFAULT '12',
	  `main_toptype` varchar(5) DEFAULT NULL,
	  `main_topdesign` text,
	  PRIMARY KEY (`vender`),
	  UNIQUE KEY `idx_tblvenderstore_1` (`id`),
	  UNIQUE KEY `idx_tblvenderstore_2` (`brand_name`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblvenderstorecount` (
	  `vender` int(10) unsigned NOT NULL DEFAULT '0',
	  `prdt_allcnt` smallint(5) unsigned NOT NULL DEFAULT '0',
	  `prdt_cnt` smallint(5) unsigned NOT NULL DEFAULT '0',
	  `cust_cnt` smallint(5) unsigned NOT NULL DEFAULT '0',
	  `count_total` int(11) unsigned NOT NULL DEFAULT '0',
	  PRIMARY KEY (`vender`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblvenderstorevisit` (
	  `vender` int(10) unsigned NOT NULL DEFAULT '0',
	  `date` varchar(8) NOT NULL DEFAULT '',
	  `cnt` int(11) unsigned NOT NULL DEFAULT '1',
	  PRIMARY KEY (`vender`,`date`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblvenderstorevisittmp` (
	  `vender` int(10) unsigned NOT NULL DEFAULT '0',
	  `date` varchar(8) NOT NULL DEFAULT '',
	  `ip` varchar(15) NOT NULL DEFAULT '',
	  PRIMARY KEY (`vender`,`date`,`ip`),
	  KEY `idx_tblvenderstorevisittmp_1` (`date`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblvenderthemecode` (
	  `vender` int(10) unsigned NOT NULL DEFAULT '0',
	  `codeA` char(3) NOT NULL DEFAULT '',
	  `codeB` char(3) NOT NULL DEFAULT '',
	  `code_name` varchar(100) NOT NULL DEFAULT '',
	  `sequence` varchar(4) DEFAULT NULL,
	  PRIMARY KEY (`vender`,`codeA`,`codeB`),
	  KEY `idx_tblvenderthemecode_1` (`sequence`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblvenderthemeproduct` (
	  `vender` int(10) unsigned NOT NULL DEFAULT '0',
	  `themecode` varchar(6) NOT NULL DEFAULT '',
	  `productcode` varchar(18) NOT NULL DEFAULT '',
	  `date` varchar(14) NOT NULL DEFAULT '',
	  PRIMARY KEY (`vender`,`themecode`,`productcode`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblvendertitleskin` (
	  `seq` int(11) unsigned NOT NULL AUTO_INCREMENT,
	  `listorder` int(11) unsigned NOT NULL DEFAULT '0',
	  `backimg` varchar(20) NOT NULL DEFAULT '',
	  PRIMARY KEY (`seq`),
	  KEY `idx_tblvendertitleskin_1` (`listorder`)
	) ENGINE=MyISAM  DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tblwishlist` (
	  `id` varchar(20) NOT NULL DEFAULT '',
	  `productcode` varchar(18) NOT NULL DEFAULT '',
	  `opt1_idx` tinyint(3) NOT NULL DEFAULT '0',
	  `opt2_idx` tinyint(4) NOT NULL DEFAULT '0',
	  `optidxs` varchar(32) NOT NULL DEFAULT '0',
	  `wish_idx` int(11) unsigned NOT NULL AUTO_INCREMENT,
	  `date` varchar(14) NOT NULL DEFAULT '',
	  `marks` tinyint(1) NOT NULL DEFAULT '3',
	  `memo` varchar(250) NOT NULL DEFAULT '',
	  PRIMARY KEY (`id`,`productcode`,`opt1_idx`,`opt2_idx`,`optidxs`),
	  UNIQUE KEY `idx_tblwishlist_1` (`wish_idx`),
	  KEY `idx_tblwishlist_2` (`date`),
	  KEY `idx_tblwishlist_3` (`marks`)
	) ENGINE=MyISAM  DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `tbl_csManager` (
	  `idx` int(11) NOT NULL AUTO_INCREMENT COMMENT 'index',
	  `vender` int(3) NOT NULL DEFAULT '0' COMMENT '����',
	  `order` varchar(25) DEFAULT NULL COMMENT '�ֹ��ڵ�',
	  `product` varchar(18) DEFAULT NULL COMMENT '��ǰ�ڵ�',
	  `member` varchar(30) NOT NULL DEFAULT '0' COMMENT 'ȸ�����̵�',
	  `title` varchar(30) NOT NULL COMMENT '����',
	  `type` varchar(20) NOT NULL COMMENT '��������',
	  `adminMemo` text NOT NULL COMMENT '��������',
	  `adminRegDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '�����ð�',
	  `delivery` varchar(6) NOT NULL DEFAULT 'admin' COMMENT '��ü���',
	  `customer` int(2) NOT NULL DEFAULT '0' COMMENT '�����',
	  `venderMemo` text NOT NULL COMMENT 'ó������',
	  `venderBackMemo` text NOT NULL COMMENT 'ȸ��ó������',
	  `backCHK` int(1) NOT NULL DEFAULT '0',
	  `deli_com` int(1) NOT NULL COMMENT '�����ȸ��',
	  `deli_num` varchar(20) NOT NULL COMMENT '������ȣ',
	  `back_deli_com` int(1) NOT NULL COMMENT 'ȸ�������ȸ��',
	  `back_deli_num` varchar(20) NOT NULL COMMENT 'ȸ��������ȣ',
	  `venderRegDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'ó���ð�',
	  `completeRegDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '�Ϸ�ð�',
	  `deliPay` int(11) NOT NULL COMMENT 'ȸ����ۺ�',
	  `orderPay` int(11) NOT NULL COMMENT '�߰������ۺ�',
	  `orderPayMemo` varchar(50) NOT NULL COMMENT '�߰��������',
	  PRIMARY KEY (`idx`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `todaysale` (
	  `pridx` int(11) NOT NULL,
	  `start` datetime NOT NULL,
	  `end` datetime DEFAULT NULL,
	  `addquantity` int(11) DEFAULT NULL,
	  `salecnt` int(11) DEFAULT NULL,
	  PRIMARY KEY (`pridx`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `vender_commission_history` (
	  `seq` int(11) NOT NULL AUTO_INCREMENT COMMENT '������',
	  `vender` int(11) NOT NULL COMMENT '������ ������',
	  `reg_date` datetime NOT NULL COMMENT '�����',
	  `memo` varchar(500) DEFAULT NULL COMMENT '��������',
	  `type` tinyint(4) NOT NULL COMMENT '���� 1:��->�� 2:��->�� 3:��->��',
	  `admin_id` varchar(20) NOT NULL COMMENT '������ ���̵�',
	  PRIMARY KEY (`seq`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
$tbllist[]="
	CREATE TABLE IF NOT EXISTS `vender_more_info` (
	  `vender` int(11) NOT NULL COMMENT '������ü ������',
	  `commission_type` tinyint(4) DEFAULT '0' COMMENT '������ ���� 0:��ü 1:��ǰ��',
	  `rq_commission_type` tinyint(4) DEFAULT '0' COMMENT '��û ������ �����',
	  `rq_rate` int(11) DEFAULT '0' COMMENT '��û ������',
	  `commission_status` tinyint(4) DEFAULT '0' COMMENT '������ ���� 0:Ȯ�� 1:��û, 2:�ź�',
	  `close_date` tinyint(4) NOT NULL DEFAULT '7' COMMENT '�����',
	  `etc` text COMMENT '��Ÿ����',
	  `admin_memo` text COMMENT '������ �޸�',
	  `reserve_use` tinyint(4) NOT NULL DEFAULT '0' COMMENT '�����ݻ�뿩��',
	  `coupon_use` tinyint(4) NOT NULL DEFAULT '0' COMMENT '���� ��뿩��',
	  `adjust_lastday` tinyint(4) NOT NULL DEFAULT '0' COMMENT '���� ���� �̿뿩�� 0:�Ⱦ�, 1:���ϸ� 2:15�ϰ� ����',
	  UNIQUE KEY `vender` (`vender`)
	) ENGINE=MyISAM DEFAULT CHARSET=euckr;
";
?>