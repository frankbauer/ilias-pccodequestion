<#1>
<?php
/**
 * Question plugin example: database update script
 *
 * @author Frank Bauer <frank.bauer@fau.de>
 * @version $Id$
 */ 
?>

CREATE TABLE `copg_pgcp_codeqstpage` (
	`code_id` int(11) NOT NULL AUTO_INCREMENT,
	`data` LONGTEXT NOT NULL,
	PRIMARY KEY (`code_id`)
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;	
?>