CREATE TABLE `tblerpiaorder` (
     `Gseq` INT(10) NOT NULL AUTO_INCREMENT COMMENT 'erpai 주문 연동용 식별 번호',
     `vender` INT(10) NOT NULL DEFAULT '0',
     `ordercode` VARCHAR(25) NOT NULL DEFAULT 'X',
     `tempkey` VARCHAR(32) NOT NULL DEFAULT '',
     `productcode` VARCHAR(18) NOT NULL DEFAULT '',
     `opt1_name` VARCHAR(50) NOT NULL DEFAULT '',
     `opt2_name` VARCHAR(50) NOT NULL DEFAULT '',
     `package_idx` SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0',
     `assemble_idx` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
     `modifydate` DATE NOT NULL,
     PRIMARY KEY (`Gseq`),
     UNIQUE INDEX `uniKey` (`vender`, `ordercode`, `tempkey`, `productcode`, `opt1_name`, `opt2_name`, `package_idx`, `assemble_idx`),
     INDEX `modifydate` (`modifydate`)
)
COMMENT='erpia 주문 연동 관련 보조 테이블'
COLLATE='euckr_korean_ci'
ENGINE=MyISAM;