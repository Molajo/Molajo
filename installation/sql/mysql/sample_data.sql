# @version		$Id: sample_data.sql 21061 2011-04-03 16:50:11Z dextercowley $
#
# IMPORTANT - THIS FILE MUST BE SAVED WITH UTF-8 ENCODING ONLY. BEWARE IF EDITING!
#
--TRUNCATE `#__assets`;
--
-- Dumping data for table `#__assets`
--


--TRUNCATE `#__categories`;
--
-- Dumping data for table `#__categories` (remove existing rows first)
--


--  --  --
-- Update rgt value of root category row
--
SET @max_rgt = (SELECT MAX(rgt)+1 FROM `#__categories` WHERE `id` <> 1);
UPDATE `#__categories` SET rgt = @max_rgt WHERE id = 1;


--
-- Dumping data for table `#__menu` (remove existing rows first)
--
--TRUNCATE `#__menu`;

--INSERT IGNORE INTO `#__menu`
