-- phpMyAdmin SQL Dump
-- version 3.3.9.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 09, 2011 at 01:33 PM
-- Server version: 5.5.9
-- PHP Version: 5.3.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `molajo`
--

-- --------------------------------------------------------

--
-- Table structure for table `molajo_actions`
--

CREATE TABLE `molajo_actions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Actions Primary Key',
  `title` varchar(255) NOT NULL DEFAULT ' ',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_actions_table_title` (`title`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `molajo_actions`
--

INSERT INTO `molajo_actions` VALUES(7, 'admin');
INSERT INTO `molajo_actions` VALUES(2, 'create');
INSERT INTO `molajo_actions` VALUES(6, 'delete');
INSERT INTO `molajo_actions` VALUES(4, 'edit');
INSERT INTO `molajo_actions` VALUES(1, 'login');
INSERT INTO `molajo_actions` VALUES(5, 'publish');
INSERT INTO `molajo_actions` VALUES(3, 'view');

-- --------------------------------------------------------

--
-- Table structure for table `molajo_applications`
--

CREATE TABLE `molajo_applications` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Application Primary Key',
  `name` varchar(255) NOT NULL DEFAULT ' ' COMMENT 'Title',
  `path` varchar(2048) NOT NULL DEFAULT ' ' COMMENT 'URL Alias',
  `description` mediumtext,
  `parameters` mediumtext COMMENT 'Configurable Parameter Values',
  `custom_fields` mediumtext,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `molajo_applications`
--

INSERT INTO `molajo_applications` VALUES(1, 'site', '', 'Primary application for site visitors', '{}', '{}');
INSERT INTO `molajo_applications` VALUES(2, 'administrator', 'administrator', 'Administrative site area for site construction', '{}', '{}');
INSERT INTO `molajo_applications` VALUES(3, 'content', 'content', 'Area for content development', '{}', '{}');

-- --------------------------------------------------------

--
-- Table structure for table `molajo_application_extensions`
--

CREATE TABLE `molajo_application_extensions` (
  `application_id` int(11) unsigned NOT NULL,
  `extension_id` int(11) unsigned NOT NULL,
  `extension_instance_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`application_id`,`extension_id`,`extension_instance_id`),
  KEY `fk_application_extensions_applications2` (`application_id`),
  KEY `fk_application_extensions_extensions2` (`extension_id`),
  KEY `fk_application_extensions_extension_instances2` (`extension_instance_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `molajo_application_extensions`
--

INSERT INTO `molajo_application_extensions` VALUES(1, 3, 3);
INSERT INTO `molajo_application_extensions` VALUES(1, 9, 9);
INSERT INTO `molajo_application_extensions` VALUES(1, 10, 10);
INSERT INTO `molajo_application_extensions` VALUES(1, 11, 11);
INSERT INTO `molajo_application_extensions` VALUES(1, 16, 16);
INSERT INTO `molajo_application_extensions` VALUES(1, 20, 33);
INSERT INTO `molajo_application_extensions` VALUES(1, 21, 34);
INSERT INTO `molajo_application_extensions` VALUES(1, 22, 36);
INSERT INTO `molajo_application_extensions` VALUES(1, 23, 37);
INSERT INTO `molajo_application_extensions` VALUES(1, 24, 38);
INSERT INTO `molajo_application_extensions` VALUES(1, 25, 39);
INSERT INTO `molajo_application_extensions` VALUES(1, 26, 40);
INSERT INTO `molajo_application_extensions` VALUES(1, 27, 41);
INSERT INTO `molajo_application_extensions` VALUES(1, 28, 42);
INSERT INTO `molajo_application_extensions` VALUES(1, 29, 43);
INSERT INTO `molajo_application_extensions` VALUES(1, 30, 44);
INSERT INTO `molajo_application_extensions` VALUES(1, 31, 45);
INSERT INTO `molajo_application_extensions` VALUES(1, 32, 46);
INSERT INTO `molajo_application_extensions` VALUES(1, 33, 47);
INSERT INTO `molajo_application_extensions` VALUES(1, 34, 48);
INSERT INTO `molajo_application_extensions` VALUES(1, 35, 49);
INSERT INTO `molajo_application_extensions` VALUES(1, 36, 50);
INSERT INTO `molajo_application_extensions` VALUES(1, 37, 51);
INSERT INTO `molajo_application_extensions` VALUES(1, 38, 52);
INSERT INTO `molajo_application_extensions` VALUES(1, 39, 53);
INSERT INTO `molajo_application_extensions` VALUES(1, 40, 54);
INSERT INTO `molajo_application_extensions` VALUES(1, 41, 55);
INSERT INTO `molajo_application_extensions` VALUES(1, 42, 56);
INSERT INTO `molajo_application_extensions` VALUES(1, 43, 57);
INSERT INTO `molajo_application_extensions` VALUES(1, 44, 58);
INSERT INTO `molajo_application_extensions` VALUES(1, 45, 59);
INSERT INTO `molajo_application_extensions` VALUES(1, 46, 60);
INSERT INTO `molajo_application_extensions` VALUES(1, 47, 61);
INSERT INTO `molajo_application_extensions` VALUES(1, 48, 62);
INSERT INTO `molajo_application_extensions` VALUES(1, 49, 63);
INSERT INTO `molajo_application_extensions` VALUES(1, 50, 64);
INSERT INTO `molajo_application_extensions` VALUES(1, 51, 65);
INSERT INTO `molajo_application_extensions` VALUES(1, 52, 66);
INSERT INTO `molajo_application_extensions` VALUES(1, 53, 67);
INSERT INTO `molajo_application_extensions` VALUES(1, 54, 68);
INSERT INTO `molajo_application_extensions` VALUES(1, 55, 69);
INSERT INTO `molajo_application_extensions` VALUES(1, 56, 70);
INSERT INTO `molajo_application_extensions` VALUES(1, 57, 71);
INSERT INTO `molajo_application_extensions` VALUES(1, 58, 72);
INSERT INTO `molajo_application_extensions` VALUES(1, 59, 73);
INSERT INTO `molajo_application_extensions` VALUES(1, 60, 74);
INSERT INTO `molajo_application_extensions` VALUES(1, 61, 75);
INSERT INTO `molajo_application_extensions` VALUES(1, 62, 76);
INSERT INTO `molajo_application_extensions` VALUES(1, 63, 77);
INSERT INTO `molajo_application_extensions` VALUES(1, 64, 78);
INSERT INTO `molajo_application_extensions` VALUES(1, 65, 79);
INSERT INTO `molajo_application_extensions` VALUES(1, 66, 80);
INSERT INTO `molajo_application_extensions` VALUES(1, 67, 81);
INSERT INTO `molajo_application_extensions` VALUES(1, 68, 82);
INSERT INTO `molajo_application_extensions` VALUES(1, 69, 83);
INSERT INTO `molajo_application_extensions` VALUES(1, 70, 84);
INSERT INTO `molajo_application_extensions` VALUES(1, 71, 85);
INSERT INTO `molajo_application_extensions` VALUES(1, 72, 86);
INSERT INTO `molajo_application_extensions` VALUES(1, 73, 87);
INSERT INTO `molajo_application_extensions` VALUES(1, 74, 88);
INSERT INTO `molajo_application_extensions` VALUES(1, 75, 89);
INSERT INTO `molajo_application_extensions` VALUES(1, 76, 90);
INSERT INTO `molajo_application_extensions` VALUES(1, 77, 91);
INSERT INTO `molajo_application_extensions` VALUES(1, 78, 92);
INSERT INTO `molajo_application_extensions` VALUES(1, 88, 99);
INSERT INTO `molajo_application_extensions` VALUES(1, 89, 100);
INSERT INTO `molajo_application_extensions` VALUES(1, 90, 101);
INSERT INTO `molajo_application_extensions` VALUES(1, 91, 102);
INSERT INTO `molajo_application_extensions` VALUES(1, 92, 103);
INSERT INTO `molajo_application_extensions` VALUES(1, 94, 105);
INSERT INTO `molajo_application_extensions` VALUES(1, 95, 106);
INSERT INTO `molajo_application_extensions` VALUES(1, 96, 107);
INSERT INTO `molajo_application_extensions` VALUES(1, 98, 109);
INSERT INTO `molajo_application_extensions` VALUES(1, 99, 110);
INSERT INTO `molajo_application_extensions` VALUES(1, 100, 111);
INSERT INTO `molajo_application_extensions` VALUES(1, 101, 112);
INSERT INTO `molajo_application_extensions` VALUES(1, 103, 114);
INSERT INTO `molajo_application_extensions` VALUES(1, 104, 115);
INSERT INTO `molajo_application_extensions` VALUES(1, 105, 116);
INSERT INTO `molajo_application_extensions` VALUES(1, 106, 117);
INSERT INTO `molajo_application_extensions` VALUES(1, 107, 118);
INSERT INTO `molajo_application_extensions` VALUES(1, 108, 119);
INSERT INTO `molajo_application_extensions` VALUES(1, 109, 120);
INSERT INTO `molajo_application_extensions` VALUES(1, 110, 121);
INSERT INTO `molajo_application_extensions` VALUES(1, 111, 122);
INSERT INTO `molajo_application_extensions` VALUES(1, 112, 123);
INSERT INTO `molajo_application_extensions` VALUES(1, 113, 124);
INSERT INTO `molajo_application_extensions` VALUES(1, 114, 125);
INSERT INTO `molajo_application_extensions` VALUES(1, 115, 126);
INSERT INTO `molajo_application_extensions` VALUES(1, 116, 127);
INSERT INTO `molajo_application_extensions` VALUES(1, 117, 128);
INSERT INTO `molajo_application_extensions` VALUES(1, 118, 129);
INSERT INTO `molajo_application_extensions` VALUES(1, 119, 130);
INSERT INTO `molajo_application_extensions` VALUES(1, 120, 131);
INSERT INTO `molajo_application_extensions` VALUES(1, 121, 132);
INSERT INTO `molajo_application_extensions` VALUES(1, 122, 133);
INSERT INTO `molajo_application_extensions` VALUES(1, 123, 134);
INSERT INTO `molajo_application_extensions` VALUES(1, 124, 135);
INSERT INTO `molajo_application_extensions` VALUES(1, 125, 136);
INSERT INTO `molajo_application_extensions` VALUES(1, 126, 137);
INSERT INTO `molajo_application_extensions` VALUES(1, 127, 138);
INSERT INTO `molajo_application_extensions` VALUES(1, 128, 139);
INSERT INTO `molajo_application_extensions` VALUES(1, 129, 140);
INSERT INTO `molajo_application_extensions` VALUES(1, 130, 141);
INSERT INTO `molajo_application_extensions` VALUES(1, 131, 142);
INSERT INTO `molajo_application_extensions` VALUES(1, 132, 143);
INSERT INTO `molajo_application_extensions` VALUES(1, 133, 144);
INSERT INTO `molajo_application_extensions` VALUES(1, 134, 145);
INSERT INTO `molajo_application_extensions` VALUES(1, 135, 146);
INSERT INTO `molajo_application_extensions` VALUES(1, 136, 147);
INSERT INTO `molajo_application_extensions` VALUES(1, 137, 148);
INSERT INTO `molajo_application_extensions` VALUES(1, 138, 149);
INSERT INTO `molajo_application_extensions` VALUES(1, 139, 150);
INSERT INTO `molajo_application_extensions` VALUES(1, 140, 151);
INSERT INTO `molajo_application_extensions` VALUES(1, 141, 152);
INSERT INTO `molajo_application_extensions` VALUES(1, 142, 153);
INSERT INTO `molajo_application_extensions` VALUES(1, 143, 154);
INSERT INTO `molajo_application_extensions` VALUES(1, 144, 155);
INSERT INTO `molajo_application_extensions` VALUES(1, 145, 177);
INSERT INTO `molajo_application_extensions` VALUES(1, 148, 180);
INSERT INTO `molajo_application_extensions` VALUES(1, 1060, 212);
INSERT INTO `molajo_application_extensions` VALUES(1, 1060, 213);
INSERT INTO `molajo_application_extensions` VALUES(1, 1060, 214);
INSERT INTO `molajo_application_extensions` VALUES(1, 1060, 215);
INSERT INTO `molajo_application_extensions` VALUES(1, 1060, 216);
INSERT INTO `molajo_application_extensions` VALUES(1, 1060, 217);
INSERT INTO `molajo_application_extensions` VALUES(1, 1060, 218);
INSERT INTO `molajo_application_extensions` VALUES(1, 1060, 219);
INSERT INTO `molajo_application_extensions` VALUES(2, 2, 2);
INSERT INTO `molajo_application_extensions` VALUES(2, 3, 3);
INSERT INTO `molajo_application_extensions` VALUES(2, 4, 4);
INSERT INTO `molajo_application_extensions` VALUES(2, 5, 5);
INSERT INTO `molajo_application_extensions` VALUES(2, 6, 6);
INSERT INTO `molajo_application_extensions` VALUES(2, 7, 7);
INSERT INTO `molajo_application_extensions` VALUES(2, 8, 8);
INSERT INTO `molajo_application_extensions` VALUES(2, 9, 9);
INSERT INTO `molajo_application_extensions` VALUES(2, 10, 10);
INSERT INTO `molajo_application_extensions` VALUES(2, 11, 11);
INSERT INTO `molajo_application_extensions` VALUES(2, 12, 12);
INSERT INTO `molajo_application_extensions` VALUES(2, 13, 13);
INSERT INTO `molajo_application_extensions` VALUES(2, 14, 14);
INSERT INTO `molajo_application_extensions` VALUES(2, 15, 15);
INSERT INTO `molajo_application_extensions` VALUES(2, 16, 16);
INSERT INTO `molajo_application_extensions` VALUES(2, 17, 17);
INSERT INTO `molajo_application_extensions` VALUES(2, 18, 18);
INSERT INTO `molajo_application_extensions` VALUES(2, 19, 19);
INSERT INTO `molajo_application_extensions` VALUES(2, 20, 33);
INSERT INTO `molajo_application_extensions` VALUES(2, 21, 34);
INSERT INTO `molajo_application_extensions` VALUES(2, 22, 36);
INSERT INTO `molajo_application_extensions` VALUES(2, 23, 37);
INSERT INTO `molajo_application_extensions` VALUES(2, 24, 38);
INSERT INTO `molajo_application_extensions` VALUES(2, 25, 39);
INSERT INTO `molajo_application_extensions` VALUES(2, 26, 40);
INSERT INTO `molajo_application_extensions` VALUES(2, 27, 41);
INSERT INTO `molajo_application_extensions` VALUES(2, 28, 42);
INSERT INTO `molajo_application_extensions` VALUES(2, 29, 43);
INSERT INTO `molajo_application_extensions` VALUES(2, 30, 44);
INSERT INTO `molajo_application_extensions` VALUES(2, 31, 45);
INSERT INTO `molajo_application_extensions` VALUES(2, 32, 46);
INSERT INTO `molajo_application_extensions` VALUES(2, 33, 47);
INSERT INTO `molajo_application_extensions` VALUES(2, 34, 48);
INSERT INTO `molajo_application_extensions` VALUES(2, 35, 49);
INSERT INTO `molajo_application_extensions` VALUES(2, 36, 50);
INSERT INTO `molajo_application_extensions` VALUES(2, 37, 51);
INSERT INTO `molajo_application_extensions` VALUES(2, 38, 52);
INSERT INTO `molajo_application_extensions` VALUES(2, 39, 53);
INSERT INTO `molajo_application_extensions` VALUES(2, 40, 54);
INSERT INTO `molajo_application_extensions` VALUES(2, 41, 55);
INSERT INTO `molajo_application_extensions` VALUES(2, 42, 56);
INSERT INTO `molajo_application_extensions` VALUES(2, 43, 57);
INSERT INTO `molajo_application_extensions` VALUES(2, 44, 58);
INSERT INTO `molajo_application_extensions` VALUES(2, 45, 59);
INSERT INTO `molajo_application_extensions` VALUES(2, 46, 60);
INSERT INTO `molajo_application_extensions` VALUES(2, 47, 61);
INSERT INTO `molajo_application_extensions` VALUES(2, 48, 62);
INSERT INTO `molajo_application_extensions` VALUES(2, 49, 63);
INSERT INTO `molajo_application_extensions` VALUES(2, 50, 64);
INSERT INTO `molajo_application_extensions` VALUES(2, 51, 65);
INSERT INTO `molajo_application_extensions` VALUES(2, 52, 66);
INSERT INTO `molajo_application_extensions` VALUES(2, 53, 67);
INSERT INTO `molajo_application_extensions` VALUES(2, 54, 68);
INSERT INTO `molajo_application_extensions` VALUES(2, 55, 69);
INSERT INTO `molajo_application_extensions` VALUES(2, 56, 70);
INSERT INTO `molajo_application_extensions` VALUES(2, 57, 71);
INSERT INTO `molajo_application_extensions` VALUES(2, 58, 72);
INSERT INTO `molajo_application_extensions` VALUES(2, 59, 73);
INSERT INTO `molajo_application_extensions` VALUES(2, 60, 74);
INSERT INTO `molajo_application_extensions` VALUES(2, 61, 75);
INSERT INTO `molajo_application_extensions` VALUES(2, 62, 76);
INSERT INTO `molajo_application_extensions` VALUES(2, 63, 77);
INSERT INTO `molajo_application_extensions` VALUES(2, 64, 78);
INSERT INTO `molajo_application_extensions` VALUES(2, 65, 79);
INSERT INTO `molajo_application_extensions` VALUES(2, 66, 80);
INSERT INTO `molajo_application_extensions` VALUES(2, 67, 81);
INSERT INTO `molajo_application_extensions` VALUES(2, 68, 82);
INSERT INTO `molajo_application_extensions` VALUES(2, 69, 83);
INSERT INTO `molajo_application_extensions` VALUES(2, 70, 84);
INSERT INTO `molajo_application_extensions` VALUES(2, 71, 85);
INSERT INTO `molajo_application_extensions` VALUES(2, 72, 86);
INSERT INTO `molajo_application_extensions` VALUES(2, 73, 87);
INSERT INTO `molajo_application_extensions` VALUES(2, 74, 88);
INSERT INTO `molajo_application_extensions` VALUES(2, 75, 89);
INSERT INTO `molajo_application_extensions` VALUES(2, 76, 90);
INSERT INTO `molajo_application_extensions` VALUES(2, 77, 91);
INSERT INTO `molajo_application_extensions` VALUES(2, 78, 92);
INSERT INTO `molajo_application_extensions` VALUES(2, 89, 100);
INSERT INTO `molajo_application_extensions` VALUES(2, 90, 101);
INSERT INTO `molajo_application_extensions` VALUES(2, 91, 102);
INSERT INTO `molajo_application_extensions` VALUES(2, 92, 103);
INSERT INTO `molajo_application_extensions` VALUES(2, 93, 104);
INSERT INTO `molajo_application_extensions` VALUES(2, 94, 105);
INSERT INTO `molajo_application_extensions` VALUES(2, 95, 106);
INSERT INTO `molajo_application_extensions` VALUES(2, 96, 107);
INSERT INTO `molajo_application_extensions` VALUES(2, 97, 108);
INSERT INTO `molajo_application_extensions` VALUES(2, 98, 109);
INSERT INTO `molajo_application_extensions` VALUES(2, 99, 110);
INSERT INTO `molajo_application_extensions` VALUES(2, 100, 111);
INSERT INTO `molajo_application_extensions` VALUES(2, 102, 113);
INSERT INTO `molajo_application_extensions` VALUES(2, 103, 114);
INSERT INTO `molajo_application_extensions` VALUES(2, 104, 115);
INSERT INTO `molajo_application_extensions` VALUES(2, 105, 116);
INSERT INTO `molajo_application_extensions` VALUES(2, 106, 117);
INSERT INTO `molajo_application_extensions` VALUES(2, 107, 118);
INSERT INTO `molajo_application_extensions` VALUES(2, 108, 119);
INSERT INTO `molajo_application_extensions` VALUES(2, 109, 120);
INSERT INTO `molajo_application_extensions` VALUES(2, 110, 121);
INSERT INTO `molajo_application_extensions` VALUES(2, 111, 122);
INSERT INTO `molajo_application_extensions` VALUES(2, 112, 123);
INSERT INTO `molajo_application_extensions` VALUES(2, 113, 124);
INSERT INTO `molajo_application_extensions` VALUES(2, 114, 125);
INSERT INTO `molajo_application_extensions` VALUES(2, 115, 126);
INSERT INTO `molajo_application_extensions` VALUES(2, 116, 127);
INSERT INTO `molajo_application_extensions` VALUES(2, 117, 128);
INSERT INTO `molajo_application_extensions` VALUES(2, 118, 129);
INSERT INTO `molajo_application_extensions` VALUES(2, 119, 130);
INSERT INTO `molajo_application_extensions` VALUES(2, 120, 131);
INSERT INTO `molajo_application_extensions` VALUES(2, 121, 132);
INSERT INTO `molajo_application_extensions` VALUES(2, 122, 133);
INSERT INTO `molajo_application_extensions` VALUES(2, 123, 134);
INSERT INTO `molajo_application_extensions` VALUES(2, 124, 135);
INSERT INTO `molajo_application_extensions` VALUES(2, 125, 136);
INSERT INTO `molajo_application_extensions` VALUES(2, 126, 137);
INSERT INTO `molajo_application_extensions` VALUES(2, 127, 138);
INSERT INTO `molajo_application_extensions` VALUES(2, 128, 139);
INSERT INTO `molajo_application_extensions` VALUES(2, 129, 140);
INSERT INTO `molajo_application_extensions` VALUES(2, 130, 141);
INSERT INTO `molajo_application_extensions` VALUES(2, 131, 142);
INSERT INTO `molajo_application_extensions` VALUES(2, 132, 143);
INSERT INTO `molajo_application_extensions` VALUES(2, 133, 144);
INSERT INTO `molajo_application_extensions` VALUES(2, 134, 145);
INSERT INTO `molajo_application_extensions` VALUES(2, 135, 146);
INSERT INTO `molajo_application_extensions` VALUES(2, 136, 147);
INSERT INTO `molajo_application_extensions` VALUES(2, 137, 148);
INSERT INTO `molajo_application_extensions` VALUES(2, 138, 149);
INSERT INTO `molajo_application_extensions` VALUES(2, 139, 150);
INSERT INTO `molajo_application_extensions` VALUES(2, 140, 151);
INSERT INTO `molajo_application_extensions` VALUES(2, 141, 152);
INSERT INTO `molajo_application_extensions` VALUES(2, 142, 153);
INSERT INTO `molajo_application_extensions` VALUES(2, 143, 154);
INSERT INTO `molajo_application_extensions` VALUES(2, 144, 155);
INSERT INTO `molajo_application_extensions` VALUES(2, 147, 179);
INSERT INTO `molajo_application_extensions` VALUES(2, 148, 180);
INSERT INTO `molajo_application_extensions` VALUES(2, 1000, 184);
INSERT INTO `molajo_application_extensions` VALUES(2, 1010, 185);
INSERT INTO `molajo_application_extensions` VALUES(2, 1010, 186);
INSERT INTO `molajo_application_extensions` VALUES(2, 1010, 187);
INSERT INTO `molajo_application_extensions` VALUES(2, 1010, 188);
INSERT INTO `molajo_application_extensions` VALUES(2, 1010, 189);
INSERT INTO `molajo_application_extensions` VALUES(2, 1020, 190);
INSERT INTO `molajo_application_extensions` VALUES(2, 1020, 191);
INSERT INTO `molajo_application_extensions` VALUES(2, 1020, 192);
INSERT INTO `molajo_application_extensions` VALUES(2, 1020, 193);
INSERT INTO `molajo_application_extensions` VALUES(2, 1020, 194);
INSERT INTO `molajo_application_extensions` VALUES(2, 1020, 195);
INSERT INTO `molajo_application_extensions` VALUES(2, 1030, 196);
INSERT INTO `molajo_application_extensions` VALUES(2, 1030, 197);
INSERT INTO `molajo_application_extensions` VALUES(2, 1030, 198);
INSERT INTO `molajo_application_extensions` VALUES(2, 1030, 199);
INSERT INTO `molajo_application_extensions` VALUES(2, 1030, 200);
INSERT INTO `molajo_application_extensions` VALUES(2, 1040, 201);
INSERT INTO `molajo_application_extensions` VALUES(2, 1040, 202);
INSERT INTO `molajo_application_extensions` VALUES(2, 1040, 203);
INSERT INTO `molajo_application_extensions` VALUES(2, 1040, 204);
INSERT INTO `molajo_application_extensions` VALUES(2, 1040, 205);
INSERT INTO `molajo_application_extensions` VALUES(2, 1050, 206);
INSERT INTO `molajo_application_extensions` VALUES(2, 1050, 207);
INSERT INTO `molajo_application_extensions` VALUES(2, 1050, 208);
INSERT INTO `molajo_application_extensions` VALUES(2, 1050, 209);
INSERT INTO `molajo_application_extensions` VALUES(2, 1050, 210);
INSERT INTO `molajo_application_extensions` VALUES(2, 1050, 211);
INSERT INTO `molajo_application_extensions` VALUES(3, 3, 3);
INSERT INTO `molajo_application_extensions` VALUES(3, 9, 9);
INSERT INTO `molajo_application_extensions` VALUES(3, 10, 10);
INSERT INTO `molajo_application_extensions` VALUES(3, 11, 11);
INSERT INTO `molajo_application_extensions` VALUES(3, 16, 16);
INSERT INTO `molajo_application_extensions` VALUES(3, 20, 33);
INSERT INTO `molajo_application_extensions` VALUES(3, 21, 34);
INSERT INTO `molajo_application_extensions` VALUES(3, 22, 36);
INSERT INTO `molajo_application_extensions` VALUES(3, 23, 37);
INSERT INTO `molajo_application_extensions` VALUES(3, 24, 38);
INSERT INTO `molajo_application_extensions` VALUES(3, 25, 39);
INSERT INTO `molajo_application_extensions` VALUES(3, 26, 40);
INSERT INTO `molajo_application_extensions` VALUES(3, 27, 41);
INSERT INTO `molajo_application_extensions` VALUES(3, 28, 42);
INSERT INTO `molajo_application_extensions` VALUES(3, 29, 43);
INSERT INTO `molajo_application_extensions` VALUES(3, 30, 44);
INSERT INTO `molajo_application_extensions` VALUES(3, 31, 45);
INSERT INTO `molajo_application_extensions` VALUES(3, 32, 46);
INSERT INTO `molajo_application_extensions` VALUES(3, 33, 47);
INSERT INTO `molajo_application_extensions` VALUES(3, 34, 48);
INSERT INTO `molajo_application_extensions` VALUES(3, 35, 49);
INSERT INTO `molajo_application_extensions` VALUES(3, 36, 50);
INSERT INTO `molajo_application_extensions` VALUES(3, 37, 51);
INSERT INTO `molajo_application_extensions` VALUES(3, 38, 52);
INSERT INTO `molajo_application_extensions` VALUES(3, 39, 53);
INSERT INTO `molajo_application_extensions` VALUES(3, 40, 54);
INSERT INTO `molajo_application_extensions` VALUES(3, 41, 55);
INSERT INTO `molajo_application_extensions` VALUES(3, 42, 56);
INSERT INTO `molajo_application_extensions` VALUES(3, 43, 57);
INSERT INTO `molajo_application_extensions` VALUES(3, 44, 58);
INSERT INTO `molajo_application_extensions` VALUES(3, 45, 59);
INSERT INTO `molajo_application_extensions` VALUES(3, 46, 60);
INSERT INTO `molajo_application_extensions` VALUES(3, 47, 61);
INSERT INTO `molajo_application_extensions` VALUES(3, 48, 62);
INSERT INTO `molajo_application_extensions` VALUES(3, 49, 63);
INSERT INTO `molajo_application_extensions` VALUES(3, 50, 64);
INSERT INTO `molajo_application_extensions` VALUES(3, 51, 65);
INSERT INTO `molajo_application_extensions` VALUES(3, 52, 66);
INSERT INTO `molajo_application_extensions` VALUES(3, 53, 67);
INSERT INTO `molajo_application_extensions` VALUES(3, 54, 68);
INSERT INTO `molajo_application_extensions` VALUES(3, 55, 69);
INSERT INTO `molajo_application_extensions` VALUES(3, 56, 70);
INSERT INTO `molajo_application_extensions` VALUES(3, 57, 71);
INSERT INTO `molajo_application_extensions` VALUES(3, 58, 72);
INSERT INTO `molajo_application_extensions` VALUES(3, 59, 73);
INSERT INTO `molajo_application_extensions` VALUES(3, 60, 74);
INSERT INTO `molajo_application_extensions` VALUES(3, 61, 75);
INSERT INTO `molajo_application_extensions` VALUES(3, 62, 76);
INSERT INTO `molajo_application_extensions` VALUES(3, 63, 77);
INSERT INTO `molajo_application_extensions` VALUES(3, 64, 78);
INSERT INTO `molajo_application_extensions` VALUES(3, 65, 79);
INSERT INTO `molajo_application_extensions` VALUES(3, 66, 80);
INSERT INTO `molajo_application_extensions` VALUES(3, 67, 81);
INSERT INTO `molajo_application_extensions` VALUES(3, 68, 82);
INSERT INTO `molajo_application_extensions` VALUES(3, 69, 83);
INSERT INTO `molajo_application_extensions` VALUES(3, 70, 84);
INSERT INTO `molajo_application_extensions` VALUES(3, 71, 85);
INSERT INTO `molajo_application_extensions` VALUES(3, 72, 86);
INSERT INTO `molajo_application_extensions` VALUES(3, 73, 87);
INSERT INTO `molajo_application_extensions` VALUES(3, 74, 88);
INSERT INTO `molajo_application_extensions` VALUES(3, 75, 89);
INSERT INTO `molajo_application_extensions` VALUES(3, 76, 90);
INSERT INTO `molajo_application_extensions` VALUES(3, 77, 91);
INSERT INTO `molajo_application_extensions` VALUES(3, 78, 92);
INSERT INTO `molajo_application_extensions` VALUES(3, 88, 99);
INSERT INTO `molajo_application_extensions` VALUES(3, 89, 100);
INSERT INTO `molajo_application_extensions` VALUES(3, 90, 101);
INSERT INTO `molajo_application_extensions` VALUES(3, 91, 102);
INSERT INTO `molajo_application_extensions` VALUES(3, 92, 103);
INSERT INTO `molajo_application_extensions` VALUES(3, 94, 105);
INSERT INTO `molajo_application_extensions` VALUES(3, 95, 106);
INSERT INTO `molajo_application_extensions` VALUES(3, 96, 107);
INSERT INTO `molajo_application_extensions` VALUES(3, 98, 109);
INSERT INTO `molajo_application_extensions` VALUES(3, 99, 110);
INSERT INTO `molajo_application_extensions` VALUES(3, 100, 111);
INSERT INTO `molajo_application_extensions` VALUES(3, 101, 112);
INSERT INTO `molajo_application_extensions` VALUES(3, 103, 114);
INSERT INTO `molajo_application_extensions` VALUES(3, 104, 115);
INSERT INTO `molajo_application_extensions` VALUES(3, 105, 116);
INSERT INTO `molajo_application_extensions` VALUES(3, 106, 117);
INSERT INTO `molajo_application_extensions` VALUES(3, 107, 118);
INSERT INTO `molajo_application_extensions` VALUES(3, 108, 119);
INSERT INTO `molajo_application_extensions` VALUES(3, 109, 120);
INSERT INTO `molajo_application_extensions` VALUES(3, 110, 121);
INSERT INTO `molajo_application_extensions` VALUES(3, 111, 122);
INSERT INTO `molajo_application_extensions` VALUES(3, 112, 123);
INSERT INTO `molajo_application_extensions` VALUES(3, 113, 124);
INSERT INTO `molajo_application_extensions` VALUES(3, 114, 125);
INSERT INTO `molajo_application_extensions` VALUES(3, 115, 126);
INSERT INTO `molajo_application_extensions` VALUES(3, 116, 127);
INSERT INTO `molajo_application_extensions` VALUES(3, 117, 128);
INSERT INTO `molajo_application_extensions` VALUES(3, 118, 129);
INSERT INTO `molajo_application_extensions` VALUES(3, 119, 130);
INSERT INTO `molajo_application_extensions` VALUES(3, 120, 131);
INSERT INTO `molajo_application_extensions` VALUES(3, 121, 132);
INSERT INTO `molajo_application_extensions` VALUES(3, 122, 133);
INSERT INTO `molajo_application_extensions` VALUES(3, 123, 134);
INSERT INTO `molajo_application_extensions` VALUES(3, 124, 135);
INSERT INTO `molajo_application_extensions` VALUES(3, 125, 136);
INSERT INTO `molajo_application_extensions` VALUES(3, 126, 137);
INSERT INTO `molajo_application_extensions` VALUES(3, 127, 138);
INSERT INTO `molajo_application_extensions` VALUES(3, 128, 139);
INSERT INTO `molajo_application_extensions` VALUES(3, 129, 140);
INSERT INTO `molajo_application_extensions` VALUES(3, 130, 141);
INSERT INTO `molajo_application_extensions` VALUES(3, 131, 142);
INSERT INTO `molajo_application_extensions` VALUES(3, 132, 143);
INSERT INTO `molajo_application_extensions` VALUES(3, 133, 144);
INSERT INTO `molajo_application_extensions` VALUES(3, 134, 145);
INSERT INTO `molajo_application_extensions` VALUES(3, 135, 146);
INSERT INTO `molajo_application_extensions` VALUES(3, 136, 147);
INSERT INTO `molajo_application_extensions` VALUES(3, 137, 148);
INSERT INTO `molajo_application_extensions` VALUES(3, 138, 149);
INSERT INTO `molajo_application_extensions` VALUES(3, 139, 150);
INSERT INTO `molajo_application_extensions` VALUES(3, 140, 151);
INSERT INTO `molajo_application_extensions` VALUES(3, 141, 152);
INSERT INTO `molajo_application_extensions` VALUES(3, 142, 153);
INSERT INTO `molajo_application_extensions` VALUES(3, 143, 154);
INSERT INTO `molajo_application_extensions` VALUES(3, 144, 155);
INSERT INTO `molajo_application_extensions` VALUES(3, 145, 177);
INSERT INTO `molajo_application_extensions` VALUES(3, 148, 180);
INSERT INTO `molajo_application_extensions` VALUES(3, 1060, 212);
INSERT INTO `molajo_application_extensions` VALUES(3, 1060, 213);
INSERT INTO `molajo_application_extensions` VALUES(3, 1060, 214);
INSERT INTO `molajo_application_extensions` VALUES(3, 1060, 215);
INSERT INTO `molajo_application_extensions` VALUES(3, 1060, 216);
INSERT INTO `molajo_application_extensions` VALUES(3, 1060, 217);
INSERT INTO `molajo_application_extensions` VALUES(3, 1060, 218);
INSERT INTO `molajo_application_extensions` VALUES(3, 1060, 219);

-- --------------------------------------------------------

--
-- Table structure for table `molajo_assets`
--

CREATE TABLE `molajo_assets` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Assets Primary Key',
  `source_table_id` int(11) unsigned NOT NULL DEFAULT '0',
  `source_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Content Primary Key',
  `title` varchar(255) NOT NULL DEFAULT ' ',
  `sef_request` varchar(2048) NOT NULL DEFAULT ' ' COMMENT 'URL',
  `request` varchar(2048) NOT NULL DEFAULT ' ' COMMENT 'The actually link the menu item refers to.',
  `language` char(7) NOT NULL DEFAULT 'en-GB',
  `translation_of_id` int(11) unsigned NOT NULL DEFAULT '0',
  `redirect_to_id` int(11) unsigned NOT NULL DEFAULT '0',
  `view_group_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'FK to the #__groupings table',
  PRIMARY KEY (`id`),
  KEY `fk_assets_source_tables2` (`source_table_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=268 ;

--
-- Dumping data for table `molajo_assets`
--

INSERT INTO `molajo_assets` VALUES(2, 6, 4, 'Administrator', '', '', 'en-GB', 0, 0, 1);
INSERT INTO `molajo_assets` VALUES(3, 6, 2, 'Guest', '', '', 'en-GB', 0, 0, 1);
INSERT INTO `molajo_assets` VALUES(4, 6, 1, 'Public', '', '', 'en-GB', 0, 0, 1);
INSERT INTO `molajo_assets` VALUES(5, 6, 3, 'Registered', '', '', 'en-GB', 0, 0, 1);
INSERT INTO `molajo_assets` VALUES(10, 1, 1, 'site', '', '', 'en-GB', 0, 0, 1);
INSERT INTO `molajo_assets` VALUES(11, 1, 2, 'administrator', 'administrator', '', 'en-GB', 0, 0, 1);
INSERT INTO `molajo_assets` VALUES(12, 1, 3, 'content', 'content', '', 'en-GB', 0, 0, 1);
INSERT INTO `molajo_assets` VALUES(13, 4, 2, 'com_admin', 'extensions/components/2', 'index.php?option=com_extensions&view=components&id=2', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(14, 4, 3, 'com_articles', 'extensions/components/3', 'index.php?option=com_extensions&view=components&id=3', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(15, 4, 4, 'com_categories', 'extensions/components/4', 'index.php?option=com_extensions&view=components&id=4', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(16, 4, 5, 'com_config', 'extensions/components/5', 'index.php?option=com_extensions&view=components&id=5', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(17, 4, 6, 'com_dashboard', 'extensions/components/6', 'index.php?option=com_extensions&view=components&id=6', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(18, 4, 7, 'com_extensions', 'extensions/components/7', 'index.php?option=com_extensions&view=components&id=7', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(19, 4, 8, 'com_installer', 'extensions/components/8', 'index.php?option=com_extensions&view=components&id=8', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(20, 4, 9, 'com_layouts', 'extensions/components/9', 'index.php?option=com_extensions&view=components&id=9', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(21, 4, 10, 'com_login', 'extensions/components/10', 'index.php?option=com_extensions&view=components&id=10', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(22, 4, 11, 'com_media', 'extensions/components/11', 'index.php?option=com_extensions&view=components&id=11', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(23, 4, 12, 'com_menus', 'extensions/components/12', 'index.php?option=com_extensions&view=components&id=12', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(24, 4, 13, 'com_modules', 'extensions/components/13', 'index.php?option=com_extensions&view=components&id=13', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(25, 4, 14, 'com_plugins', 'extensions/components/14', 'index.php?option=com_extensions&view=components&id=14', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(26, 4, 15, 'com_redirect', 'extensions/components/15', 'index.php?option=com_extensions&view=components&id=15', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(27, 4, 16, 'com_search', 'extensions/components/16', 'index.php?option=com_extensions&view=components&id=16', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(28, 4, 17, 'com_templates', 'extensions/components/17', 'index.php?option=com_extensions&view=components&id=17', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(29, 4, 18, 'com_admin', 'extensions/components/18', 'index.php?option=com_extensions&view=components&id=18', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(30, 4, 19, 'com_users', 'extensions/components/19', 'index.php?option=com_extensions&view=components&id=19', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(44, 2, 33, 'English (UK)', 'extensions/languages/33', 'index.php?option=com_extensions&view=languages&id=33', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(45, 2, 34, 'English (US)', 'extensions/languages/34', 'index.php?option=com_extensions&view=languages&id=34', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(47, 2, 36, 'head', 'extensions/layouts/36', 'index.php?option=com_extensions&view=layouts&id=36', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(48, 2, 37, 'messages', 'extensions/layouts/37', 'index.php?option=com_extensions&view=layouts&id=37', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(49, 2, 38, 'errors', 'extensions/layouts/38', 'index.php?option=com_extensions&view=layouts&id=38', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(50, 2, 39, 'atom', 'extensions/layouts/39', 'index.php?option=com_extensions&view=layouts&id=39', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(51, 2, 40, 'rss', 'extensions/layouts/40', 'index.php?option=com_extensions&view=layouts&id=40', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(52, 2, 41, 'admin_acl_panel', 'extensions/layouts/41', 'index.php?option=com_extensions&view=layouts&id=41', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(53, 2, 42, 'admin_activity', 'extensions/layouts/42', 'index.php?option=com_extensions&view=layouts&id=42', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(54, 2, 43, 'admin_edit', 'extensions/layouts/43', 'index.php?option=com_extensions&view=layouts&id=43', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(55, 2, 44, 'admin_favorites', 'extensions/layouts/44', 'index.php?option=com_extensions&view=layouts&id=44', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(56, 2, 45, 'admin_feed', 'extensions/layouts/45', 'index.php?option=com_extensions&view=layouts&id=45', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(57, 2, 46, 'admin_footer', 'extensions/layouts/46', 'index.php?option=com_extensions&view=layouts&id=46', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(58, 2, 47, 'admin_header', 'extensions/layouts/47', 'index.php?option=com_extensions&view=layouts&id=47', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(59, 2, 48, 'admin_inbox', 'extensions/layouts/48', 'index.php?option=com_extensions&view=layouts&id=48', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(60, 2, 49, 'admin_launchpad', 'extensions/layouts/49', 'index.php?option=com_extensions&view=layouts&id=49', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(61, 2, 50, 'admin_list', 'extensions/layouts/50', 'index.php?option=com_extensions&view=layouts&id=50', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(62, 2, 51, 'admin_login', 'extensions/layouts/51', 'index.php?option=com_extensions&view=layouts&id=51', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(63, 2, 52, 'admin_modal', 'extensions/layouts/52', 'index.php?option=com_extensions&view=layouts&id=52', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(64, 2, 53, 'admin_pagination', 'extensions/layouts/53', 'index.php?option=com_extensions&view=layouts&id=53', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(65, 2, 54, 'admin_toolbar', 'extensions/layouts/54', 'index.php?option=com_extensions&view=layouts&id=54', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(66, 2, 55, 'audio', 'extensions/layouts/55', 'index.php?option=com_extensions&view=layouts&id=55', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(67, 2, 56, 'contact_form', 'extensions/layouts/56', 'index.php?option=com_extensions&view=layouts&id=56', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(68, 2, 57, 'default', 'extensions/layouts/57', 'index.php?option=com_extensions&view=layouts&id=57', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(69, 2, 58, 'dummy', 'extensions/layouts/58', 'index.php?option=com_extensions&view=layouts&id=58', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(70, 2, 59, 'faq', 'extensions/layouts/59', 'index.php?option=com_extensions&view=layouts&id=59', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(71, 2, 60, 'item', 'extensions/layouts/60', 'index.php?option=com_extensions&view=layouts&id=60', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(72, 2, 61, 'list', 'extensions/layouts/61', 'index.php?option=com_extensions&view=layouts&id=61', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(73, 2, 62, 'items', 'extensions/layouts/62', 'index.php?option=com_extensions&view=layouts&id=62', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(74, 2, 63, 'list', 'extensions/layouts/63', 'index.php?option=com_extensions&view=layouts&id=63', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(75, 2, 64, 'pagination', 'extensions/layouts/64', 'index.php?option=com_extensions&view=layouts&id=64', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(76, 2, 65, 'social_bookmarks', 'extensions/layouts/65', 'index.php?option=com_extensions&view=layouts&id=65', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(77, 2, 66, 'syntaxhighlighter', 'extensions/layouts/66', 'index.php?option=com_extensions&view=layouts&id=66', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(78, 2, 67, 'table', 'extensions/layouts/67', 'index.php?option=com_extensions&view=layouts&id=67', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(79, 2, 68, 'tree', 'extensions/layouts/68', 'index.php?option=com_extensions&view=layouts&id=68', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(80, 2, 69, 'twig_example', 'extensions/layouts/69', 'index.php?option=com_extensions&view=layouts&id=69', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(81, 2, 70, 'video', 'extensions/layouts/70', 'index.php?option=com_extensions&view=layouts&id=70', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(82, 2, 71, 'button', 'extensions/layouts/71', 'index.php?option=com_extensions&view=layouts&id=71', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(83, 2, 72, 'colorpicker', 'extensions/layouts/72', 'index.php?option=com_extensions&view=layouts&id=72', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(84, 2, 73, 'list', 'extensions/layouts/73', 'index.php?option=com_extensions&view=layouts&id=73', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(85, 2, 74, 'media', 'extensions/layouts/74', 'index.php?option=com_extensions&view=layouts&id=74', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(86, 2, 75, 'number', 'extensions/layouts/75', 'index.php?option=com_extensions&view=layouts&id=75', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(87, 2, 76, 'option', 'extensions/layouts/76', 'index.php?option=com_extensions&view=layouts&id=76', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(88, 2, 77, 'rules', 'extensions/layouts/77', 'index.php?option=com_extensions&view=layouts&id=77', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(89, 2, 78, 'spacer', 'extensions/layouts/78', 'index.php?option=com_extensions&view=layouts&id=78', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(90, 2, 79, 'text', 'extensions/layouts/79', 'index.php?option=com_extensions&view=layouts&id=79', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(91, 2, 80, 'textarea', 'extensions/layouts/80', 'index.php?option=com_extensions&view=layouts&id=80', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(92, 2, 81, 'user', 'extensions/layouts/81', 'index.php?option=com_extensions&view=layouts&id=81', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(93, 2, 82, 'article', 'extensions/layouts/82', 'index.php?option=com_extensions&view=layouts&id=82', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(94, 2, 83, 'aside', 'extensions/layouts/83', 'index.php?option=com_extensions&view=layouts&id=83', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(95, 2, 84, 'div', 'extensions/layouts/84', 'index.php?option=com_extensions&view=layouts&id=84', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(96, 2, 85, 'footer', 'extensions/layouts/85', 'index.php?option=com_extensions&view=layouts&id=85', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(97, 2, 86, 'horizontal', 'extensions/layouts/86', 'index.php?option=com_extensions&view=layouts&id=86', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(98, 2, 87, 'nav', 'extensions/layouts/87', 'index.php?option=com_extensions&view=layouts&id=87', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(99, 2, 88, 'none', 'extensions/layouts/88', 'index.php?option=com_extensions&view=layouts&id=88', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(100, 2, 89, 'outline', 'extensions/layouts/89', 'index.php?option=com_extensions&view=layouts&id=89', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(101, 2, 90, 'section', 'extensions/layouts/90', 'index.php?option=com_extensions&view=layouts&id=90', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(102, 2, 91, 'table', 'extensions/layouts/91', 'index.php?option=com_extensions&view=layouts&id=91', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(103, 2, 92, 'tabs', 'extensions/layouts/92', 'index.php?option=com_extensions&view=layouts&id=92', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(110, 4, 99, 'mod_breadcrumbs', 'extensions/modules/99', 'index.php?option=com_extensions&view=modules&id=99', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(111, 4, 100, 'mod_content', 'extensions/modules/100', 'index.php?option=com_extensions&view=modules&id=100', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(112, 4, 101, 'mod_custom', 'extensions/modules/101', 'index.php?option=com_extensions&view=modules&id=101', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(113, 4, 102, 'mod_feed', 'extensions/modules/102', 'index.php?option=com_extensions&view=modules&id=102', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(114, 4, 103, 'mod_header', 'extensions/modules/103', 'index.php?option=com_extensions&view=modules&id=103', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(115, 4, 104, 'mod_launchpad', 'extensions/modules/104', 'index.php?option=com_extensions&view=modules&id=104', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(116, 4, 105, 'mod_layout', 'extensions/modules/105', 'index.php?option=com_extensions&view=modules&id=105', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(117, 4, 106, 'mod_login', 'extensions/modules/106', 'index.php?option=com_extensions&view=modules&id=106', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(118, 4, 107, 'mod_logout', 'extensions/modules/107', 'index.php?option=com_extensions&view=modules&id=107', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(119, 4, 108, 'mod_members', 'extensions/modules/108', 'index.php?option=com_extensions&view=modules&id=108', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(120, 4, 109, 'mod_menu', 'extensions/modules/109', 'index.php?option=com_extensions&view=modules&id=109', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(121, 4, 110, 'mod_pagination', 'extensions/modules/110', 'index.php?option=com_extensions&view=modules&id=110', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(122, 4, 111, 'mod_search', 'extensions/modules/111', 'index.php?option=com_extensions&view=modules&id=111', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(123, 4, 112, 'mod_syndicate', 'extensions/modules/112', 'index.php?option=com_extensions&view=modules&id=112', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(124, 4, 113, 'mod_toolbar', 'extensions/modules/113', 'index.php?option=com_extensions&view=modules&id=113', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(125, 4, 114, 'example', 'extensions/plugins/114', 'index.php?option=com_extensions&view=plugins&id=114', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(126, 4, 115, 'molajo', 'extensions/plugins/115', 'index.php?option=com_extensions&view=plugins&id=115', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(127, 4, 116, 'broadcast', 'extensions/plugins/116', 'index.php?option=com_extensions&view=plugins&id=116', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(128, 4, 117, 'content', 'extensions/plugins/117', 'index.php?option=com_extensions&view=plugins&id=117', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(129, 4, 118, 'emailcloak', 'extensions/plugins/118', 'index.php?option=com_extensions&view=plugins&id=118', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(130, 4, 119, 'links', 'extensions/plugins/119', 'index.php?option=com_extensions&view=plugins&id=119', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(131, 4, 120, 'loadmodule', 'extensions/plugins/120', 'index.php?option=com_extensions&view=plugins&id=120', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(132, 4, 121, 'media', 'extensions/plugins/121', 'index.php?option=com_extensions&view=plugins&id=121', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(133, 4, 122, 'protect', 'extensions/plugins/122', 'index.php?option=com_extensions&view=plugins&id=122', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(134, 4, 123, 'responses', 'extensions/plugins/123', 'index.php?option=com_extensions&view=plugins&id=123', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(135, 4, 124, 'aloha', 'extensions/plugins/124', 'index.php?option=com_extensions&view=plugins&id=124', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(136, 4, 125, 'none', 'extensions/plugins/125', 'index.php?option=com_extensions&view=plugins&id=125', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(137, 4, 126, 'article', 'extensions/plugins/126', 'index.php?option=com_extensions&view=plugins&id=126', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(138, 4, 127, 'editor', 'extensions/plugins/127', 'index.php?option=com_extensions&view=plugins&id=127', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(139, 4, 128, 'image', 'extensions/plugins/128', 'index.php?option=com_extensions&view=plugins&id=128', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(140, 4, 129, 'pagebreak', 'extensions/plugins/129', 'index.php?option=com_extensions&view=plugins&id=129', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(141, 4, 130, 'readmore', 'extensions/plugins/130', 'index.php?option=com_extensions&view=plugins&id=130', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(142, 4, 131, 'molajo', 'extensions/plugins/131', 'index.php?option=com_extensions&view=plugins&id=131', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(143, 4, 132, 'extend', 'extensions/plugins/132', 'index.php?option=com_extensions&view=plugins&id=132', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(144, 4, 133, 'minifier', 'extensions/plugins/133', 'index.php?option=com_extensions&view=plugins&id=133', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(145, 4, 134, 'search', 'extensions/plugins/134', 'index.php?option=com_extensions&view=plugins&id=134', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(146, 4, 135, 'tags', 'extensions/plugins/135', 'index.php?option=com_extensions&view=plugins&id=135', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(147, 4, 136, 'urls', 'extensions/plugins/136', 'index.php?option=com_extensions&view=plugins&id=136', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(148, 4, 137, 'molajosample', 'extensions/plugins/137', 'index.php?option=com_extensions&view=plugins&id=137', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(149, 4, 138, 'categories', 'extensions/plugins/138', 'index.php?option=com_extensions&view=plugins&id=138', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(150, 4, 139, 'articles', 'extensions/plugins/139', 'index.php?option=com_extensions&view=plugins&id=139', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(151, 4, 140, 'cache', 'extensions/plugins/140', 'index.php?option=com_extensions&view=plugins&id=140', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(152, 4, 141, 'compress', 'extensions/plugins/141', 'index.php?option=com_extensions&view=plugins&id=141', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(153, 4, 142, 'create', 'extensions/plugins/142', 'index.php?option=com_extensions&view=plugins&id=142', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(154, 4, 143, 'debug', 'extensions/plugins/143', 'index.php?option=com_extensions&view=plugins&id=143', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(155, 4, 144, 'languagefilter', 'extensions/plugins/144', 'index.php?option=com_extensions&view=plugins&id=144', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(156, 4, 145, 'log', 'extensions/plugins/145', 'index.php?option=com_extensions&view=plugins&id=145', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(157, 4, 146, 'logout', 'extensions/plugins/146', 'index.php?option=com_extensions&view=plugins&id=146', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(158, 4, 147, 'molajo', 'extensions/plugins/147', 'index.php?option=com_extensions&view=plugins&id=147', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(159, 4, 148, 'p3p', 'extensions/plugins/148', 'index.php?option=com_extensions&view=plugins&id=148', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(160, 4, 149, 'parameters', 'extensions/plugins/149', 'index.php?option=com_extensions&view=plugins&id=149', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(161, 4, 150, 'redirect', 'extensions/plugins/150', 'index.php?option=com_extensions&view=plugins&id=150', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(162, 4, 151, 'remember', 'extensions/plugins/151', 'index.php?option=com_extensions&view=plugins&id=151', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(163, 4, 152, 'system', 'extensions/plugins/152', 'index.php?option=com_extensions&view=plugins&id=152', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(164, 4, 153, 'webservices', 'extensions/plugins/153', 'index.php?option=com_extensions&view=plugins&id=153', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(165, 4, 154, 'molajo', 'extensions/plugins/154', 'index.php?option=com_extensions&view=plugins&id=154', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(166, 4, 155, 'profile', 'extensions/plugins/155', 'index.php?option=com_extensions&view=plugins&id=155', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(188, 4, 177, 'construct', 'extensions/templates/177', 'index.php?option=com_extensions&view=templates&id=177', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(189, 4, 178, 'install', 'extensions/templates/178', 'index.php?option=com_extensions&view=templates&id=178', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(190, 4, 179, 'molajito', 'extensions/templates/179', 'index.php?option=com_extensions&view=templates&id=179', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(191, 4, 180, 'system', 'extensions/templates/180', 'index.php?option=com_extensions&view=templates&id=180', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(195, 4, 184, 'Home', 'extensions/menuitem/184', 'index.php?option=com_extensions&view=menuitem&id=184', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(196, 4, 185, 'Configure', 'extensions/menuitem/185', 'index.php?option=com_extensions&view=menuitem&id=185', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(197, 4, 186, 'Access', 'extensions/menuitem/186', 'index.php?option=com_extensions&view=menuitem&id=186', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(198, 4, 187, 'Create', 'extensions/menuitem/187', 'index.php?option=com_extensions&view=menuitem&id=187', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(199, 4, 188, 'Build', 'extensions/menuitem/188', 'index.php?option=com_extensions&view=menuitem&id=188', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(200, 4, 189, 'Search', 'extensions/menuitem/189', 'index.php?option=com_extensions&view=menuitem&id=189', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(201, 4, 190, 'Profile', 'extensions/menuitem/190', 'index.php?option=com_extensions&view=menuitem&id=190', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(202, 4, 191, 'System', 'extensions/menuitem/191', 'index.php?option=com_extensions&view=menuitem&id=191', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(203, 4, 192, 'Checkin', 'extensions/menuitem/192', 'index.php?option=com_extensions&view=menuitem&id=192', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(204, 4, 193, 'Cache', 'extensions/menuitem/193', 'index.php?option=com_extensions&view=menuitem&id=193', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(205, 4, 194, 'Backup', 'extensions/menuitem/194', 'index.php?option=com_extensions&view=menuitem&id=194', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(206, 4, 195, 'Redirects', 'extensions/menuitem/195', 'index.php?option=com_extensions&view=menuitem&id=195', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(207, 4, 196, 'Users', 'extensions/menuitem/196', 'index.php?option=com_extensions&view=menuitem&id=196', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(208, 4, 197, 'Groups', 'extensions/menuitem/197', 'index.php?option=com_extensions&view=menuitem&id=197', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(209, 4, 198, 'Permissions', 'extensions/menuitem/198', 'index.php?option=com_extensions&view=menuitem&id=198', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(210, 4, 199, 'Messages', 'extensions/menuitem/199', 'index.php?option=com_extensions&view=menuitem&id=199', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(211, 4, 200, 'Activity', 'extensions/menuitem/200', 'index.php?option=com_extensions&view=menuitem&id=200', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(212, 4, 201, 'Articles', 'extensions/menuitem/201', 'index.php?option=com_extensions&view=menuitem&id=201', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(213, 4, 202, 'Tags', 'extensions/menuitem/202', 'index.php?option=com_extensions&view=menuitem&id=202', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(214, 4, 203, 'Comments', 'extensions/menuitem/203', 'index.php?option=com_extensions&view=menuitem&id=203', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(215, 4, 204, 'Media', 'extensions/menuitem/204', 'index.php?option=com_extensions&view=menuitem&id=204', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(216, 4, 205, 'Categories', 'extensions/menuitem/205', 'index.php?option=com_extensions&view=menuitem&id=205', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(217, 4, 206, 'Extensions', 'extensions/menuitem/206', 'index.php?option=com_extensions&view=menuitem&id=206', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(218, 4, 207, 'Languages', 'extensions/menuitem/207', 'index.php?option=com_extensions&view=menuitem&id=207', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(219, 4, 208, 'Layouts', 'extensions/menuitem/208', 'index.php?option=com_extensions&view=menuitem&id=208', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(220, 4, 209, 'Modules', 'extensions/menuitem/209', 'index.php?option=com_extensions&view=menuitem&id=209', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(221, 4, 210, 'Plugins', 'extensions/menuitem/210', 'index.php?option=com_extensions&view=menuitem&id=210', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(222, 4, 211, 'Templates', 'extensions/menuitem/211', 'index.php?option=com_extensions&view=menuitem&id=211', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(223, 4, 212, 'Home', 'extensions/menuitem/212', 'index.php?option=com_extensions&view=menuitem&id=212', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(224, 4, 213, 'New Article', 'extensions/menuitem/213', 'index.php?option=com_extensions&view=menuitem&id=213', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(225, 4, 214, 'Article', 'extensions/menuitem/214', 'index.php?option=com_extensions&view=menuitem&id=214', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(226, 4, 215, 'Blog', 'extensions/menuitem/215', 'index.php?option=com_extensions&view=menuitem&id=215', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(227, 4, 216, 'List', 'extensions/menuitem/216', 'index.php?option=com_extensions&view=menuitem&id=216', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(228, 4, 217, 'Table', 'extensions/menuitem/217', 'index.php?option=com_extensions&view=menuitem&id=217', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(229, 4, 218, 'Login', 'extensions/menuitem/218', 'index.php?option=com_extensions&view=menuitem&id=218', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(230, 4, 219, 'Search', 'extensions/menuitem/219', 'index.php?option=com_extensions&view=menuitem&id=219', 'en-GB', 0, 0, 5);
INSERT INTO `molajo_assets` VALUES(258, 2, 1, 'ROOT', 'categories/1', 'index.php?option=com_categories&id=1', 'en-GB', 0, 0, 1);
INSERT INTO `molajo_assets` VALUES(259, 2, 2, 'Articles', 'categories/2', 'index.php?option=com_categories&id=2', 'en-GB', 0, 0, 1);
INSERT INTO `molajo_assets` VALUES(261, 3, 1, 'My First Article', 'articles/1', 'index.php?option=com_articles&view=article&id=1', 'en-GB', 0, 0, 1);
INSERT INTO `molajo_assets` VALUES(262, 3, 2, 'My Second Article', 'articles/2', 'index.php?option=com_articles&view=article&id=2', 'en-GB', 0, 0, 1);
INSERT INTO `molajo_assets` VALUES(263, 3, 3, 'My Third Article', 'articles/3', 'index.php?option=com_articles&view=article&id=3', 'en-GB', 0, 0, 1);
INSERT INTO `molajo_assets` VALUES(264, 3, 4, 'My Fourth Article', 'articles/4', 'index.php?option=com_articles&view=article&id=4', 'en-GB', 0, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `molajo_categories`
--

CREATE TABLE `molajo_categories` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `subtitle` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(255) NOT NULL DEFAULT '',
  `content_text` mediumtext,
  `status` int(3) unsigned NOT NULL DEFAULT '0',
  `start_publishing_datetime` datetime DEFAULT '0000-00-00 00:00:00',
  `stop_publishing_datetime` datetime DEFAULT '0000-00-00 00:00:00',
  `version` int(11) unsigned NOT NULL DEFAULT '0',
  `version_of_id` int(11) unsigned NOT NULL DEFAULT '0',
  `status_prior_to_version` int(11) unsigned NOT NULL DEFAULT '0',
  `checked_out_by` int(11) unsigned NOT NULL DEFAULT '0',
  `checked_out_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) unsigned NOT NULL DEFAULT '0',
  `created_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(11) unsigned NOT NULL DEFAULT '0',
  `modified_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `parent_id` int(11) unsigned NOT NULL DEFAULT '0',
  `lft` int(11) unsigned NOT NULL DEFAULT '0',
  `rgt` int(11) unsigned NOT NULL DEFAULT '0',
  `level` int(11) unsigned NOT NULL DEFAULT '0',
  `metakey` text COMMENT 'The meta keywords for the page.',
  `metadesc` text COMMENT 'The meta description for the page.',
  `metadata` text COMMENT 'JSON encoded metadata properties.',
  `custom_fields` mediumtext,
  `parameters` mediumtext COMMENT 'Configurable Parameter Values',
  `language` char(7) NOT NULL DEFAULT 'en-GB',
  `translation_of_id` int(11) unsigned NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `cat_idx` (`status`),
  KEY `idx_checkout` (`checked_out_by`),
  KEY `idx_alias` (`alias`),
  KEY `idx_language` (`language`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `molajo_categories`
--

INSERT INTO `molajo_categories` VALUES(1, 'ROOT', '', 'root', '<p>Root category</p>', 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, 'en-GB', 0, 1);
INSERT INTO `molajo_categories` VALUES(2, 'Articles', 'com_articles', 'articles', '<p>Category for Articles</p>', 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 1, 1, 2, 1, NULL, NULL, NULL, NULL, NULL, 'en-GB', 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `molajo_extension_configurations`
--

CREATE TABLE `molajo_extension_configurations` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `extension_instances_id` int(11) unsigned NOT NULL,
  `option_id` int(11) unsigned NOT NULL DEFAULT '0',
  `option_value` varchar(80) NOT NULL DEFAULT ' ',
  `option_value_literal` varchar(255) NOT NULL DEFAULT ' ',
  `ordering` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_component_option_id_value_key` (`extension_instances_id`,`option_id`,`option_value`),
  KEY `fk_configurations_extension_instances2` (`extension_instances_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=494 ;

--
-- Dumping data for table `molajo_extension_configurations`
--

INSERT INTO `molajo_extension_configurations` VALUES(1, 1, 100, '', '', 0);
INSERT INTO `molajo_extension_configurations` VALUES(2, 1, 100, '__common', '__common', 1);
INSERT INTO `molajo_extension_configurations` VALUES(3, 1, 200, '', '', 0);
INSERT INTO `molajo_extension_configurations` VALUES(4, 1, 200, 'access', 'MOLAJO_FIELD_ACCESS_LABEL', 1);
INSERT INTO `molajo_extension_configurations` VALUES(5, 1, 200, 'alias', 'MOLAJO_FIELD_ALIAS_LABEL', 2);
INSERT INTO `molajo_extension_configurations` VALUES(6, 1, 200, 'asset_id', 'MOLAJO_FIELD_ASSET_ID_LABEL', 3);
INSERT INTO `molajo_extension_configurations` VALUES(7, 1, 200, 'attribs', 'MOLAJO_FIELD_ATTRIBS_LABEL', 4);
INSERT INTO `molajo_extension_configurations` VALUES(8, 1, 200, 'catid', 'MOLAJO_FIELD_CATID_LABEL', 5);
INSERT INTO `molajo_extension_configurations` VALUES(9, 1, 200, 'checked_out', 'MOLAJO_FIELD_CHECKED_OUT_LABEL', 6);
INSERT INTO `molajo_extension_configurations` VALUES(10, 1, 200, 'checked_out_time', 'MOLAJO_FIELD_CHECKED_OUT_TIME_LABEL', 7);
INSERT INTO `molajo_extension_configurations` VALUES(11, 1, 200, 'component_id', 'MOLAJO_FIELD_COMPONENT_ID_LABEL', 8);
INSERT INTO `molajo_extension_configurations` VALUES(12, 1, 200, 'content_table', 'MOLAJO_FIELD_CONTENT_TABLE_LABEL', 9);
INSERT INTO `molajo_extension_configurations` VALUES(13, 1, 200, 'content_email_address', 'MOLAJO_FIELD_CONTENT_EMAIL_ADDRESS_LABEL', 10);
INSERT INTO `molajo_extension_configurations` VALUES(14, 1, 200, 'content_file', 'MOLAJO_FIELD_CONTENT_FILE_LABEL', 11);
INSERT INTO `molajo_extension_configurations` VALUES(15, 1, 200, 'content_link', 'MOLAJO_FIELD_CONTENT_LINK_LABEL', 12);
INSERT INTO `molajo_extension_configurations` VALUES(16, 1, 200, 'content_numeric_value', 'MOLAJO_FIELD_CONTENT_NUMERIC_VALUE_LABEL', 13);
INSERT INTO `molajo_extension_configurations` VALUES(17, 1, 200, 'content_text', 'MOLAJO_FIELD_CONTENT_TEXT_LABEL', 14);
INSERT INTO `molajo_extension_configurations` VALUES(18, 1, 200, 'content_type', 'MOLAJO_FIELD_CONTENT_TYPE_LABEL', 15);
INSERT INTO `molajo_extension_configurations` VALUES(19, 1, 200, 'created', 'MOLAJO_FIELD_CREATED_LABEL', 16);
INSERT INTO `molajo_extension_configurations` VALUES(20, 1, 200, 'created_by', 'MOLAJO_FIELD_CREATED_BY_LABEL', 17);
INSERT INTO `molajo_extension_configurations` VALUES(21, 1, 200, 'created_by_alias', 'MOLAJO_FIELD_CREATED_BY_ALIAS_LABEL', 18);
INSERT INTO `molajo_extension_configurations` VALUES(22, 1, 200, 'created_by_email', 'MOLAJO_FIELD_CREATED_BY_EMAIL_LABEL', 19);
INSERT INTO `molajo_extension_configurations` VALUES(23, 1, 200, 'created_by_ip_address', 'MOLAJO_FIELD_CREATED_BY_IP_ADDRESS_LABEL', 20);
INSERT INTO `molajo_extension_configurations` VALUES(24, 1, 200, 'created_by_referer', 'MOLAJO_FIELD_CREATED_BY_REFERER_LABEL', 21);
INSERT INTO `molajo_extension_configurations` VALUES(25, 1, 200, 'created_by_website', 'MOLAJO_FIELD_CREATED_BY_WEBSITE_LABEL', 22);
INSERT INTO `molajo_extension_configurations` VALUES(26, 1, 200, 'featured', 'MOLAJO_FIELD_FEATURED_LABEL', 23);
INSERT INTO `molajo_extension_configurations` VALUES(27, 1, 200, 'id', 'MOLAJO_FIELD_ID_LABEL', 24);
INSERT INTO `molajo_extension_configurations` VALUES(28, 1, 200, 'language', 'MOLAJO_FIELD_LANGUAGE_LABEL', 25);
INSERT INTO `molajo_extension_configurations` VALUES(29, 1, 200, 'level', 'MOLAJO_FIELD_LEVEL_LABEL', 26);
INSERT INTO `molajo_extension_configurations` VALUES(30, 1, 200, 'lft', 'MOLAJO_FIELD_LFT_LABEL', 27);
INSERT INTO `molajo_extension_configurations` VALUES(31, 1, 200, 'metadata', 'MOLAJO_FIELD_METADATA_LABEL', 28);
INSERT INTO `molajo_extension_configurations` VALUES(32, 1, 200, 'metadesc', 'MOLAJO_FIELD_METADESC_LABEL', 29);
INSERT INTO `molajo_extension_configurations` VALUES(33, 1, 200, 'metakey', 'MOLAJO_FIELD_METAKEY_LABEL', 30);
INSERT INTO `molajo_extension_configurations` VALUES(34, 1, 200, 'meta_author', 'MOLAJO_FIELD_META_AUTHOR_LABEL', 31);
INSERT INTO `molajo_extension_configurations` VALUES(35, 1, 200, 'meta_rights', 'MOLAJO_FIELD_META_RIGHTS_LABEL', 32);
INSERT INTO `molajo_extension_configurations` VALUES(36, 1, 200, 'meta_robots', 'MOLAJO_FIELD_META_ROBOTS_LABEL', 33);
INSERT INTO `molajo_extension_configurations` VALUES(37, 1, 200, 'modified', 'MOLAJO_FIELD_MODIFIED_LABEL', 34);
INSERT INTO `molajo_extension_configurations` VALUES(38, 1, 200, 'modified_by', 'MOLAJO_FIELD_MODIFIED_BY_LABEL', 35);
INSERT INTO `molajo_extension_configurations` VALUES(39, 1, 200, 'ordering', 'MOLAJO_FIELD_ORDERING_LABEL', 36);
INSERT INTO `molajo_extension_configurations` VALUES(40, 1, 200, 'stop_publishing_datetime', 'MOLAJO_FIELD_PUBLISH_DOWN_LABEL', 37);
INSERT INTO `molajo_extension_configurations` VALUES(41, 1, 200, 'start_publishing_datetime', 'MOLAJO_FIELD_PUBLISH_UP_LABEL', 38);
INSERT INTO `molajo_extension_configurations` VALUES(42, 1, 200, 'rgt', 'MOLAJO_FIELD_RGT_LABEL', 39);
INSERT INTO `molajo_extension_configurations` VALUES(43, 1, 200, 'state', 'MOLAJO_FIELD_STATE_LABEL', 40);
INSERT INTO `molajo_extension_configurations` VALUES(44, 1, 200, 'state_prior_to_version', 'MOLAJO_FIELD_STATE_PRIOR_TO_VERSION_LABEL', 41);
INSERT INTO `molajo_extension_configurations` VALUES(45, 1, 200, 'stickied', 'MOLAJO_FIELD_STICKIED_LABEL', 42);
INSERT INTO `molajo_extension_configurations` VALUES(46, 1, 200, 'user_default', 'MOLAJO_FIELD_USER_DEFAULT_LABEL', 43);
INSERT INTO `molajo_extension_configurations` VALUES(47, 1, 200, 'category_default', 'MOLAJO_FIELD_CATEGORY_DEFAULT_LABEL', 44);
INSERT INTO `molajo_extension_configurations` VALUES(48, 1, 200, 'title', 'MOLAJO_FIELD_TITLE_LABEL', 45);
INSERT INTO `molajo_extension_configurations` VALUES(49, 1, 200, 'subtitle', 'MOLAJO_FIELD_SUBTITLE_LABEL', 46);
INSERT INTO `molajo_extension_configurations` VALUES(50, 1, 200, 'version', 'MOLAJO_FIELD_VERSION_LABEL', 47);
INSERT INTO `molajo_extension_configurations` VALUES(51, 1, 200, 'version_of_id', 'MOLAJO_FIELD_VERSION_OF_ID_LABEL', 48);
INSERT INTO `molajo_extension_configurations` VALUES(52, 1, 210, '', '', 0);
INSERT INTO `molajo_extension_configurations` VALUES(53, 1, 210, 'access', 'MOLAJO_FIELD_ACCESS_LABEL', 1);
INSERT INTO `molajo_extension_configurations` VALUES(54, 1, 210, 'featured', 'MOLAJO_FIELD_FEATURED_LABEL', 2);
INSERT INTO `molajo_extension_configurations` VALUES(55, 1, 210, 'ordering', 'MOLAJO_FIELD_ORDERING_LABEL', 3);
INSERT INTO `molajo_extension_configurations` VALUES(56, 1, 210, 'stop_publishing_datetime', 'MOLAJO_FIELD_PUBLISH_DOWN_LABEL', 4);
INSERT INTO `molajo_extension_configurations` VALUES(57, 1, 210, 'start_publishing_datetime', 'MOLAJO_FIELD_PUBLISH_UP_LABEL', 5);
INSERT INTO `molajo_extension_configurations` VALUES(58, 1, 210, 'state', 'MOLAJO_FIELD_STATE_LABEL', 6);
INSERT INTO `molajo_extension_configurations` VALUES(59, 1, 210, 'stickied', 'MOLAJO_FIELD_STICKIED_LABEL', 7);
INSERT INTO `molajo_extension_configurations` VALUES(60, 1, 220, '', '', 0);
INSERT INTO `molajo_extension_configurations` VALUES(61, 1, 220, 'attribs', 'MOLAJO_FIELD_ATTRIBS_LABEL', 1);
INSERT INTO `molajo_extension_configurations` VALUES(62, 1, 220, 'metadata', 'MOLAJO_FIELD_METADATA_LABEL', 2);
INSERT INTO `molajo_extension_configurations` VALUES(63, 1, 220, 'params', 'MOLAJO_FIELD_PARAMETERS_LABEL', 3);
INSERT INTO `molajo_extension_configurations` VALUES(64, 1, 230, '', '', 0);
INSERT INTO `molajo_extension_configurations` VALUES(65, 1, 230, 'content_type', 'Content Type', 1);
INSERT INTO `molajo_extension_configurations` VALUES(66, 1, 250, '', '', 0);
INSERT INTO `molajo_extension_configurations` VALUES(67, 1, 250, '2', 'MOLAJO_OPTION_ARCHIVED', 1);
INSERT INTO `molajo_extension_configurations` VALUES(68, 1, 250, '1', 'MOLAJO_OPTION_PUBLISHED', 2);
INSERT INTO `molajo_extension_configurations` VALUES(69, 1, 250, '0', 'MOLAJO_OPTION_UNPUBLISHED', 3);
INSERT INTO `molajo_extension_configurations` VALUES(70, 1, 250, '-1', 'MOLAJO_OPTION_TRASHED', 4);
INSERT INTO `molajo_extension_configurations` VALUES(71, 1, 250, '-2', 'MOLAJO_OPTION_SPAMMED', 5);
INSERT INTO `molajo_extension_configurations` VALUES(72, 1, 250, '-10', 'MOLAJO_OPTION_VERSION', 6);
INSERT INTO `molajo_extension_configurations` VALUES(73, 1, 300, '', '', 0);
INSERT INTO `molajo_extension_configurations` VALUES(74, 1, 300, 'archive', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_ARCHIVE', 1);
INSERT INTO `molajo_extension_configurations` VALUES(75, 1, 300, 'checkin', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_CHECKIN', 2);
INSERT INTO `molajo_extension_configurations` VALUES(76, 1, 300, 'delete', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_DELETE', 3);
INSERT INTO `molajo_extension_configurations` VALUES(77, 1, 300, 'edit', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_EDIT', 4);
INSERT INTO `molajo_extension_configurations` VALUES(78, 1, 300, 'feature', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_FEATURE', 5);
INSERT INTO `molajo_extension_configurations` VALUES(79, 1, 300, 'help', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_HELP', 6);
INSERT INTO `molajo_extension_configurations` VALUES(80, 1, 300, 'new', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_NEW', 7);
INSERT INTO `molajo_extension_configurations` VALUES(81, 1, 300, 'options', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_OPTIONS', 8);
INSERT INTO `molajo_extension_configurations` VALUES(82, 1, 300, 'publish', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_PUBLISH', 9);
INSERT INTO `molajo_extension_configurations` VALUES(83, 1, 300, 'restore', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_RESTORE', 10);
INSERT INTO `molajo_extension_configurations` VALUES(84, 1, 300, 'separator', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_SEPARATOR', 11);
INSERT INTO `molajo_extension_configurations` VALUES(85, 1, 300, 'spam', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_SPAM', 12);
INSERT INTO `molajo_extension_configurations` VALUES(86, 1, 300, 'sticky', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_STICKY', 13);
INSERT INTO `molajo_extension_configurations` VALUES(87, 1, 300, 'trash', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_TRASH', 14);
INSERT INTO `molajo_extension_configurations` VALUES(88, 1, 300, 'unpublish', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_UNPUBLISH', 15);
INSERT INTO `molajo_extension_configurations` VALUES(89, 1, 310, '', '', 0);
INSERT INTO `molajo_extension_configurations` VALUES(90, 1, 310, 'apply', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_APPLY', 1);
INSERT INTO `molajo_extension_configurations` VALUES(91, 1, 310, 'close', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_CLOSE', 2);
INSERT INTO `molajo_extension_configurations` VALUES(92, 1, 310, 'help', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_HELP', 3);
INSERT INTO `molajo_extension_configurations` VALUES(93, 1, 310, 'restore', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_RESTORE', 4);
INSERT INTO `molajo_extension_configurations` VALUES(94, 1, 310, 'save', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_SAVE', 5);
INSERT INTO `molajo_extension_configurations` VALUES(95, 1, 310, 'save2new', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_SAVE_AND_NEW', 6);
INSERT INTO `molajo_extension_configurations` VALUES(96, 1, 310, 'save2copy', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_SAVE_AS_COPY', 7);
INSERT INTO `molajo_extension_configurations` VALUES(97, 1, 310, 'separator', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_SEPARATOR', 8);
INSERT INTO `molajo_extension_configurations` VALUES(98, 1, 320, '', '', 0);
INSERT INTO `molajo_extension_configurations` VALUES(99, 1, 320, 'category', 'MOLAJO_CONFIG_MANAGER_SUB_MENU_CATEGORY', 1);
INSERT INTO `molajo_extension_configurations` VALUES(100, 1, 320, 'default', 'MOLAJO_CONFIG_MANAGER_SUB_MENU_DEFAULT', 2);
INSERT INTO `molajo_extension_configurations` VALUES(101, 1, 320, 'featured', 'MOLAJO_CONFIG_MANAGER_SUB_MENU_FEATURED', 3);
INSERT INTO `molajo_extension_configurations` VALUES(102, 1, 320, 'revisions', 'MOLAJO_CONFIG_MANAGER_SUB_MENU_REVISIONS', 4);
INSERT INTO `molajo_extension_configurations` VALUES(103, 1, 320, 'stickied', 'MOLAJO_CONFIG_MANAGER_SUB_MENU_STICKIED', 5);
INSERT INTO `molajo_extension_configurations` VALUES(104, 1, 320, 'unpublished', 'MOLAJO_CONFIG_MANAGER_SUB_MENU_UNPUBLISHED', 6);
INSERT INTO `molajo_extension_configurations` VALUES(105, 1, 330, '', '', 0);
INSERT INTO `molajo_extension_configurations` VALUES(106, 1, 330, 'access', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_ACCESS', 1);
INSERT INTO `molajo_extension_configurations` VALUES(107, 1, 330, 'alias', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_ALIAS', 2);
INSERT INTO `molajo_extension_configurations` VALUES(108, 1, 330, 'created_by', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_AUTHOR', 3);
INSERT INTO `molajo_extension_configurations` VALUES(109, 1, 330, 'catid', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_CATEGORY', 4);
INSERT INTO `molajo_extension_configurations` VALUES(110, 1, 330, 'content_type', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_CONTENT_TYPE', 5);
INSERT INTO `molajo_extension_configurations` VALUES(111, 1, 330, 'created', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_CREATE_DATE', 6);
INSERT INTO `molajo_extension_configurations` VALUES(112, 1, 330, 'featured', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_FEATURED', 7);
INSERT INTO `molajo_extension_configurations` VALUES(113, 1, 330, 'language', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_LANGUAGE', 9);
INSERT INTO `molajo_extension_configurations` VALUES(114, 1, 330, 'modified', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_UPDATE_DATE', 10);
INSERT INTO `molajo_extension_configurations` VALUES(115, 1, 330, 'start_publishing_datetime', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_PUBLISH_DATE', 11);
INSERT INTO `molajo_extension_configurations` VALUES(116, 1, 330, 'state', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_STATE', 12);
INSERT INTO `molajo_extension_configurations` VALUES(117, 1, 330, 'stickied', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_STICKIED', 13);
INSERT INTO `molajo_extension_configurations` VALUES(118, 1, 330, 'title', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_TITLE', 14);
INSERT INTO `molajo_extension_configurations` VALUES(119, 1, 330, 'subtitle', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_SUBTITLE', 15);
INSERT INTO `molajo_extension_configurations` VALUES(120, 1, 340, '', '', 0);
INSERT INTO `molajo_extension_configurations` VALUES(121, 1, 340, 'article', 'MOLAJO_CONFIG_MANAGER_EDITOR_BUTTON_ARTICLE', 1);
INSERT INTO `molajo_extension_configurations` VALUES(122, 1, 340, 'audio', 'MOLAJO_CONFIG_MANAGER_EDITOR_BUTTON_AUDIO', 2);
INSERT INTO `molajo_extension_configurations` VALUES(123, 1, 340, 'file', 'MOLAJO_CONFIG_MANAGER_EDITOR_BUTTON_FILE', 3);
INSERT INTO `molajo_extension_configurations` VALUES(124, 1, 340, 'gallery', 'MOLAJO_CONFIG_MANAGER_EDITOR_BUTTON_GALLERY', 4);
INSERT INTO `molajo_extension_configurations` VALUES(125, 1, 340, 'image', 'MOLAJO_CONFIG_MANAGER_EDITOR_BUTTON_IMAGE', 5);
INSERT INTO `molajo_extension_configurations` VALUES(126, 1, 340, 'pagebreak', 'MOLAJO_CONFIG_MANAGER_EDITOR_BUTTON_PAGEBREAK', 6);
INSERT INTO `molajo_extension_configurations` VALUES(127, 1, 340, 'readmore', 'MOLAJO_CONFIG_MANAGER_EDITOR_BUTTON_READMORE', 7);
INSERT INTO `molajo_extension_configurations` VALUES(128, 1, 340, 'video', 'MOLAJO_CONFIG_MANAGER_EDITOR_BUTTON_VIDEO', 8);
INSERT INTO `molajo_extension_configurations` VALUES(129, 1, 400, '', '', 0);
INSERT INTO `molajo_extension_configurations` VALUES(130, 1, 400, 'rtp-enc-aescm128', 'rtp-enc-aescm128', 1);
INSERT INTO `molajo_extension_configurations` VALUES(131, 1, 400, 'sp-midi', 'sp-midi', 2);
INSERT INTO `molajo_extension_configurations` VALUES(132, 1, 400, 'vnd.3gpp.iufp', 'vnd.3gpp.iufp', 3);
INSERT INTO `molajo_extension_configurations` VALUES(133, 1, 400, 'vnd.4SB', 'vnd.4SB', 4);
INSERT INTO `molajo_extension_configurations` VALUES(134, 1, 400, 'vnd.CELP', 'vnd.CELP', 5);
INSERT INTO `molajo_extension_configurations` VALUES(135, 1, 400, 'vnd.audiokoz', 'vnd.audiokoz', 6);
INSERT INTO `molajo_extension_configurations` VALUES(136, 1, 400, 'vnd.cisco.nse', 'vnd.cisco.nse', 7);
INSERT INTO `molajo_extension_configurations` VALUES(137, 1, 400, 'vnd.cmles.radio-events', 'vnd.cmles.radio-events', 8);
INSERT INTO `molajo_extension_configurations` VALUES(138, 1, 400, 'vnd.cns.anp1', 'vnd.cns.anp1', 9);
INSERT INTO `molajo_extension_configurations` VALUES(139, 1, 400, 'vnd.cns.inf1', 'vnd.cns.inf1', 10);
INSERT INTO `molajo_extension_configurations` VALUES(140, 1, 400, 'vnd.dece.audio', 'vnd.dece.audio', 11);
INSERT INTO `molajo_extension_configurations` VALUES(141, 1, 400, 'vnd.digital-winds', 'vnd.digital-winds', 12);
INSERT INTO `molajo_extension_configurations` VALUES(142, 1, 400, 'vnd.dlna.adts', 'vnd.dlna.adts', 13);
INSERT INTO `molajo_extension_configurations` VALUES(143, 1, 400, 'vnd.dolby.heaac.1', 'vnd.dolby.heaac.1', 14);
INSERT INTO `molajo_extension_configurations` VALUES(144, 1, 400, 'vnd.dolby.heaac.2', 'vnd.dolby.heaac.2', 15);
INSERT INTO `molajo_extension_configurations` VALUES(145, 1, 400, 'vnd.dolby.mlp', 'vnd.dolby.mlp', 16);
INSERT INTO `molajo_extension_configurations` VALUES(146, 1, 400, 'vnd.dolby.mps', 'vnd.dolby.mps', 17);
INSERT INTO `molajo_extension_configurations` VALUES(147, 1, 400, 'vnd.dolby.pl2', 'vnd.dolby.pl2', 18);
INSERT INTO `molajo_extension_configurations` VALUES(148, 1, 400, 'vnd.dolby.pl2x', 'vnd.dolby.pl2x', 19);
INSERT INTO `molajo_extension_configurations` VALUES(149, 1, 400, 'vnd.dolby.pl2z', 'vnd.dolby.pl2z', 20);
INSERT INTO `molajo_extension_configurations` VALUES(150, 1, 400, 'vnd.dolby.pulse.1', 'vnd.dolby.pulse.1', 21);
INSERT INTO `molajo_extension_configurations` VALUES(151, 1, 400, 'vnd.dra', 'vnd.dra', 22);
INSERT INTO `molajo_extension_configurations` VALUES(152, 1, 400, 'vnd.dts', 'vnd.dts', 23);
INSERT INTO `molajo_extension_configurations` VALUES(153, 1, 400, 'vnd.dts.hd', 'vnd.dts.hd', 24);
INSERT INTO `molajo_extension_configurations` VALUES(154, 1, 400, 'vnd.dvb.file', 'vnd.dvb.file', 25);
INSERT INTO `molajo_extension_configurations` VALUES(155, 1, 400, 'vnd.everad.plj', 'vnd.everad.plj', 26);
INSERT INTO `molajo_extension_configurations` VALUES(156, 1, 400, 'vnd.hns.audio', 'vnd.hns.audio', 27);
INSERT INTO `molajo_extension_configurations` VALUES(157, 1, 400, 'vnd.lucent.voice', 'vnd.lucent.voice', 28);
INSERT INTO `molajo_extension_configurations` VALUES(158, 1, 400, 'vnd.ms-playready.media.pya', 'vnd.ms-playready.media.pya', 29);
INSERT INTO `molajo_extension_configurations` VALUES(159, 1, 400, 'vnd.nokia.mobile-xmf', 'vnd.nokia.mobile-xmf', 30);
INSERT INTO `molajo_extension_configurations` VALUES(160, 1, 400, 'vnd.nortel.vbk', 'vnd.nortel.vbk', 31);
INSERT INTO `molajo_extension_configurations` VALUES(161, 1, 400, 'vnd.nuera.ecelp4800', 'vnd.nuera.ecelp4800', 32);
INSERT INTO `molajo_extension_configurations` VALUES(162, 1, 400, 'vnd.nuera.ecelp7470', 'vnd.nuera.ecelp7470', 33);
INSERT INTO `molajo_extension_configurations` VALUES(163, 1, 400, 'vnd.nuera.ecelp9600', 'vnd.nuera.ecelp9600', 34);
INSERT INTO `molajo_extension_configurations` VALUES(164, 1, 400, 'vnd.octel.sbc', 'vnd.octel.sbc', 35);
INSERT INTO `molajo_extension_configurations` VALUES(165, 1, 400, 'vnd.qcelp', 'vnd.qcelp', 36);
INSERT INTO `molajo_extension_configurations` VALUES(166, 1, 400, 'vnd.rhetorex.32kadpcm', 'vnd.rhetorex.32kadpcm', 37);
INSERT INTO `molajo_extension_configurations` VALUES(167, 1, 400, 'vnd.rip', 'vnd.rip', 38);
INSERT INTO `molajo_extension_configurations` VALUES(168, 1, 400, 'vnd.sealedmedia.softseal-mpeg', 'vnd.sealedmedia.softseal-mpeg', 39);
INSERT INTO `molajo_extension_configurations` VALUES(169, 1, 400, 'vnd.vmx.cvsd', 'vnd.vmx.cvsd', 40);
INSERT INTO `molajo_extension_configurations` VALUES(170, 1, 410, '', '', 0);
INSERT INTO `molajo_extension_configurations` VALUES(171, 1, 410, 'cgm', 'cgm', 1);
INSERT INTO `molajo_extension_configurations` VALUES(172, 1, 410, 'jp2', 'jp2', 2);
INSERT INTO `molajo_extension_configurations` VALUES(173, 1, 410, 'jpm', 'jpm', 3);
INSERT INTO `molajo_extension_configurations` VALUES(174, 1, 410, 'jpx', 'jpx', 4);
INSERT INTO `molajo_extension_configurations` VALUES(175, 1, 410, 'naplps', 'naplps', 5);
INSERT INTO `molajo_extension_configurations` VALUES(176, 1, 410, 'png', 'png', 6);
INSERT INTO `molajo_extension_configurations` VALUES(177, 1, 410, 'prs.btif', 'prs.btif', 7);
INSERT INTO `molajo_extension_configurations` VALUES(178, 1, 410, 'prs.pti', 'prs.pti', 8);
INSERT INTO `molajo_extension_configurations` VALUES(179, 1, 410, 'vnd-djvu', 'vnd-djvu', 9);
INSERT INTO `molajo_extension_configurations` VALUES(180, 1, 410, 'vnd-svf', 'vnd-svf', 10);
INSERT INTO `molajo_extension_configurations` VALUES(181, 1, 410, 'vnd-wap-wbmp', 'vnd-wap-wbmp', 11);
INSERT INTO `molajo_extension_configurations` VALUES(182, 1, 410, 'vnd.adobe.photoshop', 'vnd.adobe.photoshop', 12);
INSERT INTO `molajo_extension_configurations` VALUES(183, 1, 410, 'vnd.cns.inf2', 'vnd.cns.inf2', 13);
INSERT INTO `molajo_extension_configurations` VALUES(184, 1, 410, 'vnd.dece.graphic', 'vnd.dece.graphic', 14);
INSERT INTO `molajo_extension_configurations` VALUES(185, 1, 410, 'vnd.dvb.subtitle', 'vnd.dvb.subtitle', 15);
INSERT INTO `molajo_extension_configurations` VALUES(186, 1, 410, 'vnd.dwg', 'vnd.dwg', 16);
INSERT INTO `molajo_extension_configurations` VALUES(187, 1, 410, 'vnd.dxf', 'vnd.dxf', 17);
INSERT INTO `molajo_extension_configurations` VALUES(188, 1, 410, 'vnd.fastbidsheet', 'vnd.fastbidsheet', 18);
INSERT INTO `molajo_extension_configurations` VALUES(189, 1, 410, 'vnd.fpx', 'vnd.fpx', 19);
INSERT INTO `molajo_extension_configurations` VALUES(190, 1, 410, 'vnd.fst', 'vnd.fst', 20);
INSERT INTO `molajo_extension_configurations` VALUES(191, 1, 410, 'vnd.fujixerox.edmics-mmr', 'vnd.fujixerox.edmics-mmr', 21);
INSERT INTO `molajo_extension_configurations` VALUES(192, 1, 410, 'vnd.fujixerox.edmics-rlc', 'vnd.fujixerox.edmics-rlc', 22);
INSERT INTO `molajo_extension_configurations` VALUES(193, 1, 410, 'vnd.globalgraphics.pgb', 'vnd.globalgraphics.pgb', 23);
INSERT INTO `molajo_extension_configurations` VALUES(194, 1, 410, 'vnd.microsoft.icon', 'vnd.microsoft.icon', 24);
INSERT INTO `molajo_extension_configurations` VALUES(195, 1, 410, 'vnd.mix', 'vnd.mix', 25);
INSERT INTO `molajo_extension_configurations` VALUES(196, 1, 410, 'vnd.ms-modi', 'vnd.ms-modi', 26);
INSERT INTO `molajo_extension_configurations` VALUES(197, 1, 410, 'vnd.net-fpx', 'vnd.net-fpx', 27);
INSERT INTO `molajo_extension_configurations` VALUES(198, 1, 410, 'vnd.radiance', 'vnd.radiance', 28);
INSERT INTO `molajo_extension_configurations` VALUES(199, 1, 410, 'vnd.sealed-png', 'vnd.sealed-png', 29);
INSERT INTO `molajo_extension_configurations` VALUES(200, 1, 410, 'vnd.sealedmedia.softseal-gif', 'vnd.sealedmedia.softseal-gif', 30);
INSERT INTO `molajo_extension_configurations` VALUES(201, 1, 410, 'vnd.sealedmedia.softseal-jpg', 'vnd.sealedmedia.softseal-jpg', 31);
INSERT INTO `molajo_extension_configurations` VALUES(202, 1, 410, 'vnd.xiff', 'vnd.xiff', 32);
INSERT INTO `molajo_extension_configurations` VALUES(203, 1, 420, '', '', 0);
INSERT INTO `molajo_extension_configurations` VALUES(204, 1, 420, 'n3', 'n3', 1);
INSERT INTO `molajo_extension_configurations` VALUES(205, 1, 420, 'prs.fallenstein.rst', 'prs.fallenstein.rst', 2);
INSERT INTO `molajo_extension_configurations` VALUES(206, 1, 420, 'prs.lines.tag', 'prs.lines.tag', 3);
INSERT INTO `molajo_extension_configurations` VALUES(207, 1, 420, 'rtf', 'rtf', 4);
INSERT INTO `molajo_extension_configurations` VALUES(208, 1, 420, 'rtp-enc-aescm128', 'rtp-enc-aescm128', 5);
INSERT INTO `molajo_extension_configurations` VALUES(209, 1, 420, 'tab-separated-values', 'tab-separated-values', 6);
INSERT INTO `molajo_extension_configurations` VALUES(210, 1, 420, 'turtle', 'turtle', 7);
INSERT INTO `molajo_extension_configurations` VALUES(211, 1, 420, 'vnd-curl', 'vnd-curl', 8);
INSERT INTO `molajo_extension_configurations` VALUES(212, 1, 420, 'vnd.DMClientScript', 'vnd.DMClientScript', 9);
INSERT INTO `molajo_extension_configurations` VALUES(213, 1, 420, 'vnd.IPTC.NITF', 'vnd.IPTC.NITF', 10);
INSERT INTO `molajo_extension_configurations` VALUES(214, 1, 420, 'vnd.IPTC.NewsML', 'vnd.IPTC.NewsML', 11);
INSERT INTO `molajo_extension_configurations` VALUES(215, 1, 420, 'vnd.abc', 'vnd.abc', 12);
INSERT INTO `molajo_extension_configurations` VALUES(216, 1, 420, 'vnd.curl', 'vnd.curl', 13);
INSERT INTO `molajo_extension_configurations` VALUES(217, 1, 420, 'vnd.dvb.subtitle', 'vnd.dvb.subtitle', 14);
INSERT INTO `molajo_extension_configurations` VALUES(218, 1, 420, 'vnd.esmertec.theme-descriptor', 'vnd.esmertec.theme-descriptor', 15);
INSERT INTO `molajo_extension_configurations` VALUES(219, 1, 420, 'vnd.fly', 'vnd.fly', 16);
INSERT INTO `molajo_extension_configurations` VALUES(220, 1, 420, 'vnd.fmi.flexstor', 'vnd.fmi.flexstor', 17);
INSERT INTO `molajo_extension_configurations` VALUES(221, 1, 420, 'vnd.graphviz', 'vnd.graphviz', 18);
INSERT INTO `molajo_extension_configurations` VALUES(222, 1, 420, 'vnd.in3d.3dml', 'vnd.in3d.3dml', 19);
INSERT INTO `molajo_extension_configurations` VALUES(223, 1, 420, 'vnd.in3d.spot', 'vnd.in3d.spot', 20);
INSERT INTO `molajo_extension_configurations` VALUES(224, 1, 420, 'vnd.latex-z', 'vnd.latex-z', 21);
INSERT INTO `molajo_extension_configurations` VALUES(225, 1, 420, 'vnd.motorola.reflex', 'vnd.motorola.reflex', 22);
INSERT INTO `molajo_extension_configurations` VALUES(226, 1, 420, 'vnd.ms-mediapackage', 'vnd.ms-mediapackage', 23);
INSERT INTO `molajo_extension_configurations` VALUES(227, 1, 420, 'vnd.net2phone.commcenter.command', 'vnd.net2phone.commcenter.command', 24);
INSERT INTO `molajo_extension_configurations` VALUES(228, 1, 420, 'vnd.si.uricatalogue', 'vnd.si.uricatalogue', 25);
INSERT INTO `molajo_extension_configurations` VALUES(229, 1, 420, 'vnd.sun.j2me.app-descriptor', 'vnd.sun.j2me.app-descriptor', 26);
INSERT INTO `molajo_extension_configurations` VALUES(230, 1, 420, 'vnd.trolltech.linguist', 'vnd.trolltech.linguist', 27);
INSERT INTO `molajo_extension_configurations` VALUES(231, 1, 420, 'vnd.wap-wml', 'vnd.wap-wml', 28);
INSERT INTO `molajo_extension_configurations` VALUES(232, 1, 420, 'vnd.wap.si', 'vnd.wap.si', 29);
INSERT INTO `molajo_extension_configurations` VALUES(233, 1, 420, 'vnd.wap.wmlscript', 'vnd.wap.wmlscript', 30);
INSERT INTO `molajo_extension_configurations` VALUES(234, 1, 430, '', '', 0);
INSERT INTO `molajo_extension_configurations` VALUES(235, 1, 430, 'jpm', 'jpm', 1);
INSERT INTO `molajo_extension_configurations` VALUES(236, 1, 430, 'mj2', 'mj2', 2);
INSERT INTO `molajo_extension_configurations` VALUES(237, 1, 430, 'quicktime', 'quicktime', 3);
INSERT INTO `molajo_extension_configurations` VALUES(238, 1, 430, 'rtp-enc-aescm128', 'rtp-enc-aescm128', 4);
INSERT INTO `molajo_extension_configurations` VALUES(239, 1, 430, 'vnd-mpegurl', 'vnd-mpegurl', 5);
INSERT INTO `molajo_extension_configurations` VALUES(240, 1, 430, 'vnd-vivo', 'vnd-vivo', 6);
INSERT INTO `molajo_extension_configurations` VALUES(241, 1, 430, 'vnd.CCTV', 'vnd.CCTV', 7);
INSERT INTO `molajo_extension_configurations` VALUES(242, 1, 430, 'vnd.dece-mp4', 'vnd.dece-mp4', 8);
INSERT INTO `molajo_extension_configurations` VALUES(243, 1, 430, 'vnd.dece.hd', 'vnd.dece.hd', 9);
INSERT INTO `molajo_extension_configurations` VALUES(244, 1, 430, 'vnd.dece.mobile', 'vnd.dece.mobile', 10);
INSERT INTO `molajo_extension_configurations` VALUES(245, 1, 430, 'vnd.dece.pd', 'vnd.dece.pd', 11);
INSERT INTO `molajo_extension_configurations` VALUES(246, 1, 430, 'vnd.dece.sd', 'vnd.dece.sd', 12);
INSERT INTO `molajo_extension_configurations` VALUES(247, 1, 430, 'vnd.dece.video', 'vnd.dece.video', 13);
INSERT INTO `molajo_extension_configurations` VALUES(248, 1, 430, 'vnd.directv-mpeg', 'vnd.directv-mpeg', 14);
INSERT INTO `molajo_extension_configurations` VALUES(249, 1, 430, 'vnd.directv.mpeg-tts', 'vnd.directv.mpeg-tts', 15);
INSERT INTO `molajo_extension_configurations` VALUES(250, 1, 430, 'vnd.dvb.file', 'vnd.dvb.file', 16);
INSERT INTO `molajo_extension_configurations` VALUES(251, 1, 430, 'vnd.fvt', 'vnd.fvt', 17);
INSERT INTO `molajo_extension_configurations` VALUES(252, 1, 430, 'vnd.hns.video', 'vnd.hns.video', 18);
INSERT INTO `molajo_extension_configurations` VALUES(253, 1, 430, 'vnd.iptvforum.1dparityfec-1010', 'vnd.iptvforum.1dparityfec-1010', 19);
INSERT INTO `molajo_extension_configurations` VALUES(254, 1, 430, 'vnd.iptvforum.1dparityfec-2005', 'vnd.iptvforum.1dparityfec-2005', 20);
INSERT INTO `molajo_extension_configurations` VALUES(255, 1, 430, 'vnd.iptvforum.2dparityfec-1010', 'vnd.iptvforum.2dparityfec-1010', 21);
INSERT INTO `molajo_extension_configurations` VALUES(256, 1, 430, 'vnd.iptvforum.2dparityfec-2005', 'vnd.iptvforum.2dparityfec-2005', 22);
INSERT INTO `molajo_extension_configurations` VALUES(257, 1, 430, 'vnd.iptvforum.ttsavc', 'vnd.iptvforum.ttsavc', 23);
INSERT INTO `molajo_extension_configurations` VALUES(258, 1, 430, 'vnd.iptvforum.ttsmpeg2', 'vnd.iptvforum.ttsmpeg2', 24);
INSERT INTO `molajo_extension_configurations` VALUES(259, 1, 430, 'vnd.motorola.video', 'vnd.motorola.video', 25);
INSERT INTO `molajo_extension_configurations` VALUES(260, 1, 430, 'vnd.motorola.videop', 'vnd.motorola.videop', 26);
INSERT INTO `molajo_extension_configurations` VALUES(261, 1, 430, 'vnd.mpegurl', 'vnd.mpegurl', 27);
INSERT INTO `molajo_extension_configurations` VALUES(262, 1, 430, 'vnd.ms-playready.media.pyv', 'vnd.ms-playready.media.pyv', 28);
INSERT INTO `molajo_extension_configurations` VALUES(263, 1, 430, 'vnd.nokia.interleaved-multimedia', 'vnd.nokia.interleaved-multimedia', 29);
INSERT INTO `molajo_extension_configurations` VALUES(264, 1, 430, 'vnd.nokia.videovoip', 'vnd.nokia.videovoip', 30);
INSERT INTO `molajo_extension_configurations` VALUES(265, 1, 430, 'vnd.objectvideo', 'vnd.objectvideo', 31);
INSERT INTO `molajo_extension_configurations` VALUES(266, 1, 430, 'vnd.sealed-swf', 'vnd.sealed-swf', 32);
INSERT INTO `molajo_extension_configurations` VALUES(267, 1, 430, 'vnd.sealed.mpeg1', 'vnd.sealed.mpeg1', 33);
INSERT INTO `molajo_extension_configurations` VALUES(268, 1, 430, 'vnd.sealed.mpeg4', 'vnd.sealed.mpeg4', 34);
INSERT INTO `molajo_extension_configurations` VALUES(269, 1, 430, 'vnd.sealed.swf', 'vnd.sealed.swf', 35);
INSERT INTO `molajo_extension_configurations` VALUES(270, 1, 430, 'vnd.sealedmedia.softseal-mov', 'vnd.sealedmedia.softseal-mov', 36);
INSERT INTO `molajo_extension_configurations` VALUES(271, 1, 430, 'vnd.uvvu.mp4', 'vnd.uvvu.mp4', 37);
INSERT INTO `molajo_extension_configurations` VALUES(272, 1, 1100, '', '', 0);
INSERT INTO `molajo_extension_configurations` VALUES(273, 1, 1100, 'add', 'display', 1);
INSERT INTO `molajo_extension_configurations` VALUES(274, 1, 1100, 'edit', 'display', 2);
INSERT INTO `molajo_extension_configurations` VALUES(275, 1, 1100, 'display', 'display', 3);
INSERT INTO `molajo_extension_configurations` VALUES(276, 1, 1100, 'apply', 'edit', 4);
INSERT INTO `molajo_extension_configurations` VALUES(277, 1, 1100, 'cancel', 'edit', 5);
INSERT INTO `molajo_extension_configurations` VALUES(278, 1, 1100, 'create', 'edit', 6);
INSERT INTO `molajo_extension_configurations` VALUES(279, 1, 1100, 'save', 'edit', 7);
INSERT INTO `molajo_extension_configurations` VALUES(280, 1, 1100, 'save2copy', 'edit', 8);
INSERT INTO `molajo_extension_configurations` VALUES(281, 1, 1100, 'save2new', 'edit', 9);
INSERT INTO `molajo_extension_configurations` VALUES(282, 1, 1100, 'restore', 'edit', 10);
INSERT INTO `molajo_extension_configurations` VALUES(283, 1, 1100, 'archive', 'multiple', 11);
INSERT INTO `molajo_extension_configurations` VALUES(284, 1, 1100, 'publish', 'multiple', 12);
INSERT INTO `molajo_extension_configurations` VALUES(285, 1, 1100, 'unpublish', 'multiple', 13);
INSERT INTO `molajo_extension_configurations` VALUES(286, 1, 1100, 'spam', 'multiple', 14);
INSERT INTO `molajo_extension_configurations` VALUES(287, 1, 1100, 'trash', 'multiple', 15);
INSERT INTO `molajo_extension_configurations` VALUES(288, 1, 1100, 'feature', 'multiple', 16);
INSERT INTO `molajo_extension_configurations` VALUES(289, 1, 1100, 'unfeature', 'multiple', 17);
INSERT INTO `molajo_extension_configurations` VALUES(290, 1, 1100, 'sticky', 'multiple', 18);
INSERT INTO `molajo_extension_configurations` VALUES(291, 1, 1100, 'unsticky', 'multiple', 19);
INSERT INTO `molajo_extension_configurations` VALUES(292, 1, 1100, 'checkin', 'multiple', 20);
INSERT INTO `molajo_extension_configurations` VALUES(293, 1, 1100, 'reorder', 'multiple', 21);
INSERT INTO `molajo_extension_configurations` VALUES(294, 1, 1100, 'orderup', 'multiple', 22);
INSERT INTO `molajo_extension_configurations` VALUES(295, 1, 1100, 'orderdown', 'multiple', 23);
INSERT INTO `molajo_extension_configurations` VALUES(296, 1, 1100, 'saveorder', 'multiple', 24);
INSERT INTO `molajo_extension_configurations` VALUES(297, 1, 1100, 'delete', 'multiple', 25);
INSERT INTO `molajo_extension_configurations` VALUES(298, 1, 1100, 'copy', 'multiple', 26);
INSERT INTO `molajo_extension_configurations` VALUES(299, 1, 1100, 'move', 'multiple', 27);
INSERT INTO `molajo_extension_configurations` VALUES(300, 1, 1100, 'login', 'login', 28);
INSERT INTO `molajo_extension_configurations` VALUES(301, 1, 1100, 'logout', 'logout', 29);
INSERT INTO `molajo_extension_configurations` VALUES(302, 1, 1101, '', '', 0);
INSERT INTO `molajo_extension_configurations` VALUES(303, 1, 1101, 'add', 'display', 1);
INSERT INTO `molajo_extension_configurations` VALUES(304, 1, 1101, 'edit', 'display', 2);
INSERT INTO `molajo_extension_configurations` VALUES(305, 1, 1101, 'display', 'display', 3);
INSERT INTO `molajo_extension_configurations` VALUES(306, 1, 1101, 'apply', 'edit', 4);
INSERT INTO `molajo_extension_configurations` VALUES(307, 1, 1101, 'cancel', 'edit', 5);
INSERT INTO `molajo_extension_configurations` VALUES(308, 1, 1101, 'create', 'edit', 6);
INSERT INTO `molajo_extension_configurations` VALUES(309, 1, 1101, 'save', 'edit', 7);
INSERT INTO `molajo_extension_configurations` VALUES(310, 1, 1101, 'save2copy', 'edit', 8);
INSERT INTO `molajo_extension_configurations` VALUES(311, 1, 1101, 'save2new', 'edit', 9);
INSERT INTO `molajo_extension_configurations` VALUES(312, 1, 1101, 'restore', 'edit', 10);
INSERT INTO `molajo_extension_configurations` VALUES(313, 1, 1101, 'archive', 'multiple', 11);
INSERT INTO `molajo_extension_configurations` VALUES(314, 1, 1101, 'publish', 'multiple', 12);
INSERT INTO `molajo_extension_configurations` VALUES(315, 1, 1101, 'unpublish', 'multiple', 13);
INSERT INTO `molajo_extension_configurations` VALUES(316, 1, 1101, 'spam', 'multiple', 14);
INSERT INTO `molajo_extension_configurations` VALUES(317, 1, 1101, 'trash', 'multiple', 15);
INSERT INTO `molajo_extension_configurations` VALUES(318, 1, 1101, 'feature', 'multiple', 16);
INSERT INTO `molajo_extension_configurations` VALUES(319, 1, 1101, 'unfeature', 'multiple', 17);
INSERT INTO `molajo_extension_configurations` VALUES(320, 1, 1101, 'sticky', 'multiple', 18);
INSERT INTO `molajo_extension_configurations` VALUES(321, 1, 1101, 'unsticky', 'multiple', 19);
INSERT INTO `molajo_extension_configurations` VALUES(322, 1, 1101, 'checkin', 'multiple', 20);
INSERT INTO `molajo_extension_configurations` VALUES(323, 1, 1101, 'reorder', 'multiple', 21);
INSERT INTO `molajo_extension_configurations` VALUES(324, 1, 1101, 'orderup', 'multiple', 22);
INSERT INTO `molajo_extension_configurations` VALUES(325, 1, 1101, 'orderdown', 'multiple', 23);
INSERT INTO `molajo_extension_configurations` VALUES(326, 1, 1101, 'saveorder', 'multiple', 24);
INSERT INTO `molajo_extension_configurations` VALUES(327, 1, 1101, 'delete', 'multiple', 25);
INSERT INTO `molajo_extension_configurations` VALUES(328, 1, 1101, 'copy', 'multiple', 26);
INSERT INTO `molajo_extension_configurations` VALUES(329, 1, 1101, 'move', 'multiple', 27);
INSERT INTO `molajo_extension_configurations` VALUES(330, 1, 1101, 'login', 'login', 28);
INSERT INTO `molajo_extension_configurations` VALUES(331, 1, 1101, 'logout', 'login', 29);
INSERT INTO `molajo_extension_configurations` VALUES(332, 1, 1800, '', '', 0);
INSERT INTO `molajo_extension_configurations` VALUES(333, 1, 1800, '2552', '2552', 1);
INSERT INTO `molajo_extension_configurations` VALUES(334, 1, 1801, '', '', 0);
INSERT INTO `molajo_extension_configurations` VALUES(335, 1, 1801, '2559', '2559', 1);
INSERT INTO `molajo_extension_configurations` VALUES(336, 1, 2000, '', '', 0);
INSERT INTO `molajo_extension_configurations` VALUES(337, 1, 2000, 'display', 'display', 1);
INSERT INTO `molajo_extension_configurations` VALUES(338, 1, 2000, 'edit', 'edit', 2);
INSERT INTO `molajo_extension_configurations` VALUES(339, 1, 2100, '', '', 0);
INSERT INTO `molajo_extension_configurations` VALUES(340, 1, 2100, 'display', 'display', 1);
INSERT INTO `molajo_extension_configurations` VALUES(341, 1, 2001, '', '', 0);
INSERT INTO `molajo_extension_configurations` VALUES(342, 1, 2001, 'display', 'display', 1);
INSERT INTO `molajo_extension_configurations` VALUES(343, 1, 2001, 'edit', 'edit', 2);
INSERT INTO `molajo_extension_configurations` VALUES(344, 1, 2101, '', '', 0);
INSERT INTO `molajo_extension_configurations` VALUES(345, 1, 2101, 'display', 'display', 1);
INSERT INTO `molajo_extension_configurations` VALUES(346, 1, 3000, '', '', 0);
INSERT INTO `molajo_extension_configurations` VALUES(347, 1, 3000, 'default', 'default', 1);
INSERT INTO `molajo_extension_configurations` VALUES(348, 1, 3000, 'item', 'item', 1);
INSERT INTO `molajo_extension_configurations` VALUES(349, 1, 3000, 'items', 'items', 1);
INSERT INTO `molajo_extension_configurations` VALUES(350, 1, 3000, 'table', 'table', 1);
INSERT INTO `molajo_extension_configurations` VALUES(351, 1, 3100, '', '', 0);
INSERT INTO `molajo_extension_configurations` VALUES(352, 1, 3100, 'default', 'default', 1);
INSERT INTO `molajo_extension_configurations` VALUES(353, 1, 3200, '', '', 0);
INSERT INTO `molajo_extension_configurations` VALUES(354, 1, 3200, 'default', 'default', 1);
INSERT INTO `molajo_extension_configurations` VALUES(355, 1, 3300, '', '', 0);
INSERT INTO `molajo_extension_configurations` VALUES(356, 1, 3300, 'default', 'default', 1);
INSERT INTO `molajo_extension_configurations` VALUES(357, 1, 3001, '', '', 0);
INSERT INTO `molajo_extension_configurations` VALUES(358, 1, 3001, 'default', 'default', 1);
INSERT INTO `molajo_extension_configurations` VALUES(359, 1, 3101, '', '', 0);
INSERT INTO `molajo_extension_configurations` VALUES(360, 1, 3101, 'default', 'default', 1);
INSERT INTO `molajo_extension_configurations` VALUES(361, 1, 3201, '', '', 0);
INSERT INTO `molajo_extension_configurations` VALUES(362, 1, 3201, 'default', 'default', 1);
INSERT INTO `molajo_extension_configurations` VALUES(363, 1, 3301, '', '', 0);
INSERT INTO `molajo_extension_configurations` VALUES(364, 1, 3301, 'default', 'default', 1);
INSERT INTO `molajo_extension_configurations` VALUES(365, 1, 4000, '', '', 0);
INSERT INTO `molajo_extension_configurations` VALUES(366, 1, 4000, 'html', 'html', 1);
INSERT INTO `molajo_extension_configurations` VALUES(367, 1, 4100, '', '', 0);
INSERT INTO `molajo_extension_configurations` VALUES(368, 1, 4100, 'html', 'html', 1);
INSERT INTO `molajo_extension_configurations` VALUES(369, 1, 4200, '', '', 0);
INSERT INTO `molajo_extension_configurations` VALUES(370, 1, 4200, 'error', 'error', 1);
INSERT INTO `molajo_extension_configurations` VALUES(371, 1, 4200, 'feed', 'feed', 2);
INSERT INTO `molajo_extension_configurations` VALUES(372, 1, 4200, 'html', 'html', 3);
INSERT INTO `molajo_extension_configurations` VALUES(373, 1, 4200, 'json', 'json', 4);
INSERT INTO `molajo_extension_configurations` VALUES(374, 1, 4200, 'opensearch', 'opensearch', 5);
INSERT INTO `molajo_extension_configurations` VALUES(375, 1, 4200, 'raw', 'raw', 6);
INSERT INTO `molajo_extension_configurations` VALUES(376, 1, 4200, 'xls', 'xls', 7);
INSERT INTO `molajo_extension_configurations` VALUES(377, 1, 4200, 'xml', 'xml', 8);
INSERT INTO `molajo_extension_configurations` VALUES(378, 1, 4200, 'xmlrpc', 'xmlrpc', 9);
INSERT INTO `molajo_extension_configurations` VALUES(379, 1, 4300, '', '', 0);
INSERT INTO `molajo_extension_configurations` VALUES(380, 1, 4300, 'html', 'html', 1);
INSERT INTO `molajo_extension_configurations` VALUES(381, 1, 4001, '', '', 0);
INSERT INTO `molajo_extension_configurations` VALUES(382, 1, 4001, 'html', 'html', 1);
INSERT INTO `molajo_extension_configurations` VALUES(383, 1, 4101, '', '', 0);
INSERT INTO `molajo_extension_configurations` VALUES(384, 1, 4101, 'html', 'html', 1);
INSERT INTO `molajo_extension_configurations` VALUES(385, 1, 4201, '', '', 0);
INSERT INTO `molajo_extension_configurations` VALUES(386, 1, 4201, 'error', 'error', 1);
INSERT INTO `molajo_extension_configurations` VALUES(387, 1, 4201, 'feed', 'feed', 2);
INSERT INTO `molajo_extension_configurations` VALUES(388, 1, 4201, 'html', 'html', 3);
INSERT INTO `molajo_extension_configurations` VALUES(389, 1, 4201, 'json', 'json', 4);
INSERT INTO `molajo_extension_configurations` VALUES(390, 1, 4201, 'opensearch', 'opensearch', 5);
INSERT INTO `molajo_extension_configurations` VALUES(391, 1, 4201, 'raw', 'raw', 6);
INSERT INTO `molajo_extension_configurations` VALUES(392, 1, 4201, 'xls', 'xls', 7);
INSERT INTO `molajo_extension_configurations` VALUES(393, 1, 4201, 'xml', 'xml', 8);
INSERT INTO `molajo_extension_configurations` VALUES(394, 1, 4201, 'xmlrpc', 'xmlrpc', 9);
INSERT INTO `molajo_extension_configurations` VALUES(395, 1, 4301, '', '', 0);
INSERT INTO `molajo_extension_configurations` VALUES(396, 1, 4301, 'html', 'html', 1);
INSERT INTO `molajo_extension_configurations` VALUES(397, 1, 5000, '', '', 0);
INSERT INTO `molajo_extension_configurations` VALUES(398, 1, 5000, 'display', 'display', 1);
INSERT INTO `molajo_extension_configurations` VALUES(399, 1, 5000, 'edit', 'edit', 2);
INSERT INTO `molajo_extension_configurations` VALUES(400, 1, 5001, '', '', 0);
INSERT INTO `molajo_extension_configurations` VALUES(401, 1, 5001, 'display', 'display', 1);
INSERT INTO `molajo_extension_configurations` VALUES(402, 1, 5001, 'edit', 'edit', 2);
INSERT INTO `molajo_extension_configurations` VALUES(403, 1, 6000, '', '', 0);
INSERT INTO `molajo_extension_configurations` VALUES(404, 1, 6000, 'content', 'content', 1);
INSERT INTO `molajo_extension_configurations` VALUES(405, 1, 10000, '', '', 0);
INSERT INTO `molajo_extension_configurations` VALUES(406, 1, 10000, '1', 'Core ACL Implementation', 1);
INSERT INTO `molajo_extension_configurations` VALUES(407, 1, 10100, '', '', 0);
INSERT INTO `molajo_extension_configurations` VALUES(408, 1, 10100, 'view', 'view', 1);
INSERT INTO `molajo_extension_configurations` VALUES(409, 1, 10100, 'create', 'create', 2);
INSERT INTO `molajo_extension_configurations` VALUES(410, 1, 10100, 'edit', 'edit', 3);
INSERT INTO `molajo_extension_configurations` VALUES(411, 1, 10100, 'publish', 'publish', 4);
INSERT INTO `molajo_extension_configurations` VALUES(412, 1, 10100, 'delete', 'delete', 5);
INSERT INTO `molajo_extension_configurations` VALUES(413, 1, 10100, 'admin', 'admin', 6);
INSERT INTO `molajo_extension_configurations` VALUES(414, 1, 10200, '', '', 0);
INSERT INTO `molajo_extension_configurations` VALUES(415, 1, 10200, 'add', 'create', 1);
INSERT INTO `molajo_extension_configurations` VALUES(416, 1, 10200, 'admin', 'admin', 2);
INSERT INTO `molajo_extension_configurations` VALUES(417, 1, 10200, 'apply', 'edit', 3);
INSERT INTO `molajo_extension_configurations` VALUES(418, 1, 10200, 'archive', 'publish', 4);
INSERT INTO `molajo_extension_configurations` VALUES(419, 1, 10200, 'cancel', '', 5);
INSERT INTO `molajo_extension_configurations` VALUES(420, 1, 10200, 'checkin', 'admin', 6);
INSERT INTO `molajo_extension_configurations` VALUES(421, 1, 10200, 'close', '', 7);
INSERT INTO `molajo_extension_configurations` VALUES(422, 1, 10200, 'copy', 'create', 8);
INSERT INTO `molajo_extension_configurations` VALUES(423, 1, 10200, 'create', 'create', 9);
INSERT INTO `molajo_extension_configurations` VALUES(424, 1, 10200, 'delete', 'delete', 10);
INSERT INTO `molajo_extension_configurations` VALUES(425, 1, 10200, 'view', 'view', 11);
INSERT INTO `molajo_extension_configurations` VALUES(426, 1, 10200, 'edit', 'edit', 12);
INSERT INTO `molajo_extension_configurations` VALUES(427, 1, 10200, 'editstate', 'publish', 13);
INSERT INTO `molajo_extension_configurations` VALUES(428, 1, 10200, 'feature', 'publish', 14);
INSERT INTO `molajo_extension_configurations` VALUES(429, 1, 10200, 'login', 'login', 15);
INSERT INTO `molajo_extension_configurations` VALUES(430, 1, 10200, 'logout', 'logout', 16);
INSERT INTO `molajo_extension_configurations` VALUES(431, 1, 10200, 'manage', 'edit', 17);
INSERT INTO `molajo_extension_configurations` VALUES(432, 1, 10200, 'move', 'edit', 18);
INSERT INTO `molajo_extension_configurations` VALUES(433, 1, 10200, 'orderdown', 'publish', 19);
INSERT INTO `molajo_extension_configurations` VALUES(434, 1, 10200, 'orderup', 'publish', 20);
INSERT INTO `molajo_extension_configurations` VALUES(435, 1, 10200, 'publish', 'publish', 21);
INSERT INTO `molajo_extension_configurations` VALUES(436, 1, 10200, 'reorder', 'publish', 22);
INSERT INTO `molajo_extension_configurations` VALUES(437, 1, 10200, 'restore', 'publish', 23);
INSERT INTO `molajo_extension_configurations` VALUES(438, 1, 10200, 'save', 'edit', 24);
INSERT INTO `molajo_extension_configurations` VALUES(439, 1, 10200, 'save2copy', 'edit', 25);
INSERT INTO `molajo_extension_configurations` VALUES(440, 1, 10200, 'save2new', 'edit', 26);
INSERT INTO `molajo_extension_configurations` VALUES(441, 1, 10200, 'saveorder', 'publish', 27);
INSERT INTO `molajo_extension_configurations` VALUES(442, 1, 10200, 'search', 'view', 28);
INSERT INTO `molajo_extension_configurations` VALUES(443, 1, 10200, 'spam', 'publish', 29);
INSERT INTO `molajo_extension_configurations` VALUES(444, 1, 10200, 'state', 'publish', 30);
INSERT INTO `molajo_extension_configurations` VALUES(445, 1, 10200, 'sticky', 'publish', 31);
INSERT INTO `molajo_extension_configurations` VALUES(446, 1, 10200, 'trash', 'publish', 32);
INSERT INTO `molajo_extension_configurations` VALUES(447, 1, 10200, 'unfeature', 'publish', 33);
INSERT INTO `molajo_extension_configurations` VALUES(448, 1, 10200, 'unpublish', 'publish', 34);
INSERT INTO `molajo_extension_configurations` VALUES(449, 1, 10200, 'unsticky', 'publish', 35);
INSERT INTO `molajo_extension_configurations` VALUES(450, 10, 100, '', '', 0);
INSERT INTO `molajo_extension_configurations` VALUES(451, 10, 100, '__dummy', '__dummy', 1);
INSERT INTO `molajo_extension_configurations` VALUES(452, 10, 1100, '', '', 0);
INSERT INTO `molajo_extension_configurations` VALUES(453, 10, 1100, 'display', 'display', 3);
INSERT INTO `molajo_extension_configurations` VALUES(454, 10, 1100, 'login', 'login', 28);
INSERT INTO `molajo_extension_configurations` VALUES(455, 10, 1100, 'logout', 'login', 29);
INSERT INTO `molajo_extension_configurations` VALUES(456, 10, 1101, '', '', 0);
INSERT INTO `molajo_extension_configurations` VALUES(457, 10, 1101, 'display', 'display', 3);
INSERT INTO `molajo_extension_configurations` VALUES(458, 10, 1101, 'login', 'login', 28);
INSERT INTO `molajo_extension_configurations` VALUES(459, 10, 1101, 'logout', 'login', 29);
INSERT INTO `molajo_extension_configurations` VALUES(460, 10, 2000, '', '', 0);
INSERT INTO `molajo_extension_configurations` VALUES(461, 10, 2000, 'display', 'display', 1);
INSERT INTO `molajo_extension_configurations` VALUES(462, 10, 2100, '', '', 0);
INSERT INTO `molajo_extension_configurations` VALUES(463, 10, 2100, 'display', 'display', 1);
INSERT INTO `molajo_extension_configurations` VALUES(464, 10, 2001, '', '', 0);
INSERT INTO `molajo_extension_configurations` VALUES(465, 10, 2001, 'display', 'display', 1);
INSERT INTO `molajo_extension_configurations` VALUES(466, 10, 2101, '', '', 0);
INSERT INTO `molajo_extension_configurations` VALUES(467, 10, 2101, 'display', 'display', 1);
INSERT INTO `molajo_extension_configurations` VALUES(468, 10, 3000, '', '', 0);
INSERT INTO `molajo_extension_configurations` VALUES(469, 10, 3000, 'login', 'login', 1);
INSERT INTO `molajo_extension_configurations` VALUES(470, 10, 3100, '', '', 0);
INSERT INTO `molajo_extension_configurations` VALUES(471, 10, 3100, 'login', 'login', 1);
INSERT INTO `molajo_extension_configurations` VALUES(472, 10, 3001, '', '', 0);
INSERT INTO `molajo_extension_configurations` VALUES(473, 10, 3001, 'admin_login', 'admin_login', 1);
INSERT INTO `molajo_extension_configurations` VALUES(474, 10, 3101, '', '', 0);
INSERT INTO `molajo_extension_configurations` VALUES(475, 10, 3101, 'admin_login', 'admin_login', 1);
INSERT INTO `molajo_extension_configurations` VALUES(476, 10, 4000, '', '', 0);
INSERT INTO `molajo_extension_configurations` VALUES(477, 10, 4000, 'html', 'html', 1);
INSERT INTO `molajo_extension_configurations` VALUES(478, 10, 4001, 'html', 'html', 1);
INSERT INTO `molajo_extension_configurations` VALUES(479, 10, 5000, '', '', 0);
INSERT INTO `molajo_extension_configurations` VALUES(480, 10, 5000, 'dummy', 'dummy', 1);
INSERT INTO `molajo_extension_configurations` VALUES(481, 10, 5001, '', '', 0);
INSERT INTO `molajo_extension_configurations` VALUES(482, 10, 5001, 'dummy', 'dummy', 1);
INSERT INTO `molajo_extension_configurations` VALUES(483, 10, 6000, '', '', 0);
INSERT INTO `molajo_extension_configurations` VALUES(484, 10, 6000, 'user', 'user', 1);
INSERT INTO `molajo_extension_configurations` VALUES(485, 10, 10000, '', '', 0);
INSERT INTO `molajo_extension_configurations` VALUES(486, 10, 10000, '1', 'Core ACL Implementation', 1);
INSERT INTO `molajo_extension_configurations` VALUES(487, 10, 10100, '', '', 0);
INSERT INTO `molajo_extension_configurations` VALUES(488, 10, 10100, 'view', 'view', 1);
INSERT INTO `molajo_extension_configurations` VALUES(489, 10, 10200, '', '', 0);
INSERT INTO `molajo_extension_configurations` VALUES(490, 10, 10200, 'login', 'login', 15);
INSERT INTO `molajo_extension_configurations` VALUES(491, 10, 10200, 'logout', 'logout', 16);
INSERT INTO `molajo_extension_configurations` VALUES(492, 3, 100, '', '', 0);
INSERT INTO `molajo_extension_configurations` VALUES(493, 3, 100, '__articles', '__articles', 1);

-- --------------------------------------------------------

--
-- Table structure for table `molajo_content`
--

CREATE TABLE `molajo_content` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary Key',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT 'Title',
  `subtitle` varchar(255) NOT NULL DEFAULT '' COMMENT 'Subtitle',
  `alias` varchar(255) NOT NULL DEFAULT '' COMMENT 'URL Alias',
  `content_type` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'Content Type: Links to #__configuration.option_id = 10 and component_option values matching ',
  `content_text` mediumtext COMMENT 'Content Primary Text Field, can include break to designate Introductory and Full text',
  `content_link` varchar(2083) DEFAULT NULL COMMENT 'Content Link for Weblink or Newsfeed Field',
  `content_email_address` varchar(255) DEFAULT NULL COMMENT 'Content Email Field',
  `content_numeric_value` tinyint(4) DEFAULT NULL COMMENT 'Content Numeric Value, ex. vote on poll',
  `content_file` varchar(255) NOT NULL DEFAULT '' COMMENT 'Content Network Path to File',
  `featured` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT 'Featured 1-Featured 0-Not Featured',
  `stickied` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT 'Stickied 1-Stickied 0-Not Stickied',
  `user_default` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT 'User DEFAULT 1-DEFAULT 0-Not DEFAULT',
  `category_default` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT 'Category DEFAULT 1-DEFAULT 0-Not DEFAULT',
  `status` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT 'Published State 2: Archived 1: Published 0: Unpublished -1: Trashed -2: Spam -10 Version',
  `start_publishing_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Publish Begin Date and Time',
  `stop_publishing_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Publish End Date and Time',
  `version` int(11) unsigned NOT NULL DEFAULT '1' COMMENT 'Version Number',
  `version_of_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Primary ID for this Version',
  `status_prior_to_version` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'State value prior to creating this version copy and changing the state to Version',
  `created_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Created Date and Time',
  `created_by` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Created by User ID',
  `created_by_alias` varchar(255) NOT NULL DEFAULT '' COMMENT 'Created by Alias',
  `created_by_email` varchar(255) NOT NULL DEFAULT '' COMMENT 'Created By Email Address',
  `created_by_website` varchar(255) NOT NULL DEFAULT '' COMMENT 'Created By Website',
  `created_by_ip_address` char(15) NOT NULL DEFAULT '' COMMENT 'Created By IP Address',
  `created_by_referer` varchar(255) NOT NULL DEFAULT '' COMMENT 'Created By Referer',
  `modified_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Modified Date',
  `modified_by` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Modified By User ID',
  `checked_out_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Checked out Date and Time',
  `checked_out_by` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Checked out by User Id',
  `parent_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Nested set parent',
  `lft` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Nested set lft',
  `rgt` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Nested set rgt',
  `level` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'The cached level in the nested tree',
  `metakey` text COMMENT 'Meta Key',
  `metadesc` text COMMENT 'Meta Description',
  `metadata` text COMMENT 'Meta Data',
  `custom_fields` mediumtext COMMENT 'Attributes (Custom Fields)',
  `parameters` mediumtext COMMENT 'Configurable Parameter Values',
  `language` char(7) DEFAULT 'en-GB',
  `translation_of_id` int(11) unsigned NOT NULL DEFAULT '0',
  `extension_instance_id` int(11) unsigned NOT NULL DEFAULT '0',
  `ordering` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Ordering',
  PRIMARY KEY (`id`),
  KEY `idx_component_component_id_id` (`id`),
  KEY `idx_checkout` (`checked_out_by`),
  KEY `idx_state` (`status`),
  KEY `idx_createdby` (`created_by`),
  KEY `idx_featured_catid` (`featured`),
  KEY `idx_stickied_catid` (`stickied`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `molajo_content`
--

INSERT INTO `molajo_content` VALUES(1, 'My First Article', 'Subtitle for My First Article', 'my-first-article', 10, '<p>Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. Aenean ultricies mi vitae est. Mauris placerat eleifend leo.</p>', NULL, NULL, NULL, '', 1, 0, 1, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 42, 'Harry Potter', 'HarryPotter@example.com', 'http://example.com', '127.1.0.0', 'http://example.com', '2011-11-01 00:00:00', 42, '0000-00-00 00:00:00', 0, 0, 0, 0, 0, '', '', '{"robots":"","author":"","rights":""}', '{"text_entry_tag":"","tags":[""],"tag_category":[""]}', '', 'en-GB', 0, 3, 1);
INSERT INTO `molajo_content` VALUES(2, 'My Second Article', 'Subtitle for My Second Article', 'my-second-article', 10, '<h1>HTML Ipsum Presents</h1>\r\n	       \r\n<p><strong>Pellentesque habitant morbi tristique</strong> senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. <em>Aenean ultricies mi vitae est.</em> Mauris placerat eleifend leo. Quisque sit amet est et sapien ullamcorper pharetra. Vestibulum erat wisi, condimentum sed, <code>commodo vitae</code>, ornare sit amet, wisi. Aenean fermentum, elit eget tincidunt condimentum, eros ipsum rutrum orci, sagittis tempus lacus enim ac dui. <a href="#">Donec non enim</a> in turpis pulvinar facilisis. Ut felis.</p>\r\n\r\n<h2>Header Level 2</h2>\r\n	       \r\n<ol>\r\n   <li>Lorem ipsum dolor sit amet, consectetuer adipiscing elit.</li>\r\n   <li>Aliquam tincidunt mauris eu risus.</li>\r\n</ol>\r\n\r\n<blockquote><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus magna. Cras in mi at felis aliquet congue. Ut a est eget ligula molestie gravida. Curabitur massa. Donec eleifend, libero at sagittis mollis, tellus est malesuada tellus, at luctus turpis elit sit amet quam. Vivamus pretium ornare est.</p></blockquote>\r\n\r\n<h3>Header Level 3</h3>\r\n\r\n<ul>\r\n   <li>Lorem ipsum dolor sit amet, consectetuer adipiscing elit.</li>\r\n   <li>Aliquam tincidunt mauris eu risus.</li>\r\n</ul>\r\n\r\n<pre><code>\r\n#header h1 a { \r\n	display: block; \r\n	width: 300px; \r\n	height: 80px; \r\n}\r\n</code></pre>', NULL, NULL, NULL, '', 0, 0, 0, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 42, 'Harry Potter', 'HarryPotter@example.com', 'http://example.com', '127.1.0.0', 'http://example.com', '2011-11-01 00:00:00', 42, '0000-00-00 00:00:00', 0, 0, 0, 0, 0, '', '', '{"robots":"","author":"","rights":""}', '{"text_entry_tag":"","tags":[""],"tag_category":[""]}', '', 'en-GB', 0, 3, 2);
INSERT INTO `molajo_content` VALUES(3, 'My Third Article', 'Subtitle for My Third Article', 'my-third-article', 10, '<p>Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.</p>\r\n\r\n<ol>\r\n   <li>Lorem ipsum dolor sit amet, consectetuer adipiscing elit.</li>\r\n   <li>Aliquam tincidunt mauris eu risus.</li>\r\n   <li>Vestibulum auctor dapibus neque.</li>\r\n</ol>\r\n\r\n<p>Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.</p>\r\n	       ', NULL, NULL, NULL, '', 0, 0, 0, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 42, 'Harry Potter', 'HarryPotter@example.com', 'http://example.com', '127.1.0.0', 'http://example.com', '2011-11-01 00:00:00', 42, '0000-00-00 00:00:00', 0, 0, 0, 0, 0, '', '', '{"robots":"","author":"","rights":""}', '{"text_entry_tag":"","tags":[""],"tag_category":[""]}', '', 'en-GB', 0, 3, 3);
INSERT INTO `molajo_content` VALUES(4, 'My Fourth Article', 'Subtitle for My Fourth Article', 'my-fourth-article', 10, '<p>Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. Aenean ultricies mi vitae est. Mauris placerat eleifend leo.</p>', NULL, NULL, NULL, '', 0, 0, 0, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 42, 'Harry Potter', 'HarryPotter@example.com', 'http://example.com', '127.1.0.0', 'http://example.com', '2011-11-01 00:00:00', 42, '0000-00-00 00:00:00', 0, 0, 0, 0, 0, '', '', '{"robots":"","author":"","rights":""}', '{"text_entry_tag":"","tags":[""],"tag_category":[""]}', '', 'en-GB', 0, 3, 4);

-- --------------------------------------------------------

--
-- Table structure for table `molajo_content_categories`
--

CREATE TABLE `molajo_content_categories` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Actions Primary Key',
  `category_id` int(11) unsigned NOT NULL DEFAULT '0',
  `content_id` int(11) unsigned NOT NULL DEFAULT '0',
  `content_table_id` int(11) unsigned NOT NULL DEFAULT '0',
  `ordering` int(11) unsigned NOT NULL DEFAULT '0',
  `primary_content_category` tinyint(4) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_content_categories_categories2` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `molajo_content_categories`
--


-- --------------------------------------------------------

--
-- Table structure for table `molajo_extensions`
--

CREATE TABLE `molajo_extensions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `extension_type_id` int(11) unsigned NOT NULL,
  `element` varchar(100) NOT NULL,
  `folder` varchar(255) NOT NULL,
  `update_site_id` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_extensions_extension_types2` (`extension_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1061 ;

--
-- Dumping data for table `molajo_extensions`
--

INSERT INTO `molajo_extensions` VALUES(1, 'Core', 0, '', '', 1);
INSERT INTO `molajo_extensions` VALUES(2, 'com_admin', 1, '', '', 1);
INSERT INTO `molajo_extensions` VALUES(3, 'com_articles', 1, '', '', 1);
INSERT INTO `molajo_extensions` VALUES(4, 'com_categories', 1, '', '', 1);
INSERT INTO `molajo_extensions` VALUES(5, 'com_config', 1, '', '', 1);
INSERT INTO `molajo_extensions` VALUES(6, 'com_dashboard', 1, '', '', 1);
INSERT INTO `molajo_extensions` VALUES(7, 'com_extensions', 1, '', '', 1);
INSERT INTO `molajo_extensions` VALUES(8, 'com_installer', 1, '', '', 1);
INSERT INTO `molajo_extensions` VALUES(9, 'com_layouts', 1, '', '', 1);
INSERT INTO `molajo_extensions` VALUES(10, 'com_login', 1, '', '', 1);
INSERT INTO `molajo_extensions` VALUES(11, 'com_media', 1, '', '', 1);
INSERT INTO `molajo_extensions` VALUES(12, 'com_menus', 1, '', '', 1);
INSERT INTO `molajo_extensions` VALUES(13, 'com_modules', 1, '', '', 1);
INSERT INTO `molajo_extensions` VALUES(14, 'com_plugins', 1, '', '', 1);
INSERT INTO `molajo_extensions` VALUES(15, 'com_redirect', 1, '', '', 1);
INSERT INTO `molajo_extensions` VALUES(16, 'com_search', 1, '', '', 1);
INSERT INTO `molajo_extensions` VALUES(17, 'com_templates', 1, '', '', 1);
INSERT INTO `molajo_extensions` VALUES(18, 'com_admin', 1, '', '', 1);
INSERT INTO `molajo_extensions` VALUES(19, 'com_users', 1, '', '', 1);
INSERT INTO `molajo_extensions` VALUES(20, 'English (UK)', 2, '', '', 1);
INSERT INTO `molajo_extensions` VALUES(21, 'English (US)', 2, '', '', 1);
INSERT INTO `molajo_extensions` VALUES(22, 'head', 3, '', 'document', 1);
INSERT INTO `molajo_extensions` VALUES(23, 'messages', 3, '', 'document', 1);
INSERT INTO `molajo_extensions` VALUES(24, 'errors', 3, '', 'document', 1);
INSERT INTO `molajo_extensions` VALUES(25, 'atom', 3, '', 'document', 1);
INSERT INTO `molajo_extensions` VALUES(26, 'rss', 3, '', 'document', 1);
INSERT INTO `molajo_extensions` VALUES(27, 'admin_acl_panel', 3, '', 'extension', 1);
INSERT INTO `molajo_extensions` VALUES(28, 'admin_activity', 3, '', 'extension', 1);
INSERT INTO `molajo_extensions` VALUES(29, 'admin_edit', 3, '', 'extension', 1);
INSERT INTO `molajo_extensions` VALUES(30, 'admin_favorites', 3, '', 'extension', 1);
INSERT INTO `molajo_extensions` VALUES(31, 'admin_feed', 3, '', 'extension', 1);
INSERT INTO `molajo_extensions` VALUES(32, 'admin_footer', 3, '', 'extension', 1);
INSERT INTO `molajo_extensions` VALUES(33, 'admin_header', 3, '', 'extension', 1);
INSERT INTO `molajo_extensions` VALUES(34, 'admin_inbox', 3, '', 'extension', 1);
INSERT INTO `molajo_extensions` VALUES(35, 'admin_launchpad', 3, '', 'extension', 1);
INSERT INTO `molajo_extensions` VALUES(36, 'admin_list', 3, '', 'extension', 1);
INSERT INTO `molajo_extensions` VALUES(37, 'admin_login', 3, '', 'extension', 1);
INSERT INTO `molajo_extensions` VALUES(38, 'admin_modal', 3, '', 'extension', 1);
INSERT INTO `molajo_extensions` VALUES(39, 'admin_pagination', 3, '', 'extension', 1);
INSERT INTO `molajo_extensions` VALUES(40, 'admin_toolbar', 3, '', 'extension', 1);
INSERT INTO `molajo_extensions` VALUES(41, 'audio', 3, '', 'extension', 1);
INSERT INTO `molajo_extensions` VALUES(42, 'contact_form', 3, '', 'extension', 1);
INSERT INTO `molajo_extensions` VALUES(43, 'default', 3, '', 'extension', 1);
INSERT INTO `molajo_extensions` VALUES(44, 'dummy', 3, '', 'extension', 1);
INSERT INTO `molajo_extensions` VALUES(45, 'faq', 3, '', 'extension', 1);
INSERT INTO `molajo_extensions` VALUES(46, 'item', 3, '', 'extension', 1);
INSERT INTO `molajo_extensions` VALUES(47, 'list', 3, '', 'extension', 1);
INSERT INTO `molajo_extensions` VALUES(48, 'items', 3, '', 'extension', 1);
INSERT INTO `molajo_extensions` VALUES(49, 'list', 3, '', 'extension', 1);
INSERT INTO `molajo_extensions` VALUES(50, 'pagination', 3, '', 'extension', 1);
INSERT INTO `molajo_extensions` VALUES(51, 'social_bookmarks', 3, '', 'extension', 1);
INSERT INTO `molajo_extensions` VALUES(52, 'syntaxhighlighter', 3, '', 'extension', 1);
INSERT INTO `molajo_extensions` VALUES(53, 'table', 3, '', 'extension', 1);
INSERT INTO `molajo_extensions` VALUES(54, 'tree', 3, '', 'extension', 1);
INSERT INTO `molajo_extensions` VALUES(55, 'twig_example', 3, '', 'extension', 1);
INSERT INTO `molajo_extensions` VALUES(56, 'video', 3, '', 'extension', 1);
INSERT INTO `molajo_extensions` VALUES(57, 'button', 3, '', 'formfields', 1);
INSERT INTO `molajo_extensions` VALUES(58, 'colorpicker', 3, '', 'formfields', 1);
INSERT INTO `molajo_extensions` VALUES(59, 'list', 3, '', 'formfields', 1);
INSERT INTO `molajo_extensions` VALUES(60, 'media', 3, '', 'formfields', 1);
INSERT INTO `molajo_extensions` VALUES(61, 'number', 3, '', 'formfields', 1);
INSERT INTO `molajo_extensions` VALUES(62, 'option', 3, '', 'formfields', 1);
INSERT INTO `molajo_extensions` VALUES(63, 'rules', 3, '', 'formfields', 1);
INSERT INTO `molajo_extensions` VALUES(64, 'spacer', 3, '', 'formfields', 1);
INSERT INTO `molajo_extensions` VALUES(65, 'text', 3, '', 'formfields', 1);
INSERT INTO `molajo_extensions` VALUES(66, 'textarea', 3, '', 'formfields', 1);
INSERT INTO `molajo_extensions` VALUES(67, 'user', 3, '', 'formfields', 1);
INSERT INTO `molajo_extensions` VALUES(68, 'article', 3, '', 'wrap', 1);
INSERT INTO `molajo_extensions` VALUES(69, 'aside', 3, '', 'wrap', 1);
INSERT INTO `molajo_extensions` VALUES(70, 'div', 3, '', 'wrap', 1);
INSERT INTO `molajo_extensions` VALUES(71, 'footer', 3, '', 'wrap', 1);
INSERT INTO `molajo_extensions` VALUES(72, 'horizontal', 3, '', 'wrap', 1);
INSERT INTO `molajo_extensions` VALUES(73, 'nav', 3, '', 'wrap', 1);
INSERT INTO `molajo_extensions` VALUES(74, 'none', 3, '', 'wrap', 1);
INSERT INTO `molajo_extensions` VALUES(75, 'outline', 3, '', 'wrap', 1);
INSERT INTO `molajo_extensions` VALUES(76, 'section', 3, '', 'wrap', 1);
INSERT INTO `molajo_extensions` VALUES(77, 'table', 3, '', 'wrap', 1);
INSERT INTO `molajo_extensions` VALUES(78, 'tabs', 3, '', 'wrap', 1);
INSERT INTO `molajo_extensions` VALUES(88, 'mod_breadcrumbs', 6, '', '', 1);
INSERT INTO `molajo_extensions` VALUES(89, 'mod_content', 6, '', '', 1);
INSERT INTO `molajo_extensions` VALUES(90, 'mod_custom', 6, '', '', 1);
INSERT INTO `molajo_extensions` VALUES(91, 'mod_feed', 6, '', '', 1);
INSERT INTO `molajo_extensions` VALUES(92, 'mod_header', 6, '', '', 1);
INSERT INTO `molajo_extensions` VALUES(93, 'mod_launchpad', 6, '', '', 1);
INSERT INTO `molajo_extensions` VALUES(94, 'mod_layout', 6, '', '', 1);
INSERT INTO `molajo_extensions` VALUES(95, 'mod_login', 6, '', '', 1);
INSERT INTO `molajo_extensions` VALUES(96, 'mod_logout', 6, '', '', 1);
INSERT INTO `molajo_extensions` VALUES(97, 'mod_members', 6, '', '', 1);
INSERT INTO `molajo_extensions` VALUES(98, 'mod_menu', 6, '', '', 1);
INSERT INTO `molajo_extensions` VALUES(99, 'mod_pagination', 6, '', '', 1);
INSERT INTO `molajo_extensions` VALUES(100, 'mod_search', 6, '', '', 1);
INSERT INTO `molajo_extensions` VALUES(101, 'mod_syndicate', 6, '', '', 1);
INSERT INTO `molajo_extensions` VALUES(102, 'mod_toolbar', 6, '', '', 1);
INSERT INTO `molajo_extensions` VALUES(103, 'example', 8, '', 'acl', 1);
INSERT INTO `molajo_extensions` VALUES(104, 'molajo', 8, '', 'authentication', 1);
INSERT INTO `molajo_extensions` VALUES(105, 'broadcast', 8, '', 'content', 1);
INSERT INTO `molajo_extensions` VALUES(106, 'content', 8, '', 'content', 1);
INSERT INTO `molajo_extensions` VALUES(107, 'emailcloak', 8, '', 'content', 1);
INSERT INTO `molajo_extensions` VALUES(108, 'links', 8, '', 'content', 1);
INSERT INTO `molajo_extensions` VALUES(109, 'loadmodule', 8, '', 'content', 1);
INSERT INTO `molajo_extensions` VALUES(110, 'media', 8, '', 'content', 1);
INSERT INTO `molajo_extensions` VALUES(111, 'protect', 8, '', 'content', 1);
INSERT INTO `molajo_extensions` VALUES(112, 'responses', 8, '', 'content', 1);
INSERT INTO `molajo_extensions` VALUES(113, 'aloha', 8, '', 'editors', 1);
INSERT INTO `molajo_extensions` VALUES(114, 'none', 8, '', 'editors', 1);
INSERT INTO `molajo_extensions` VALUES(115, 'article', 8, '', 'editor-buttons', 1);
INSERT INTO `molajo_extensions` VALUES(116, 'editor', 8, '', 'editor-buttons', 1);
INSERT INTO `molajo_extensions` VALUES(117, 'image', 8, '', 'editor-buttons', 1);
INSERT INTO `molajo_extensions` VALUES(118, 'pagebreak', 8, '', 'editor-buttons', 1);
INSERT INTO `molajo_extensions` VALUES(119, 'readmore', 8, '', 'editor-buttons', 1);
INSERT INTO `molajo_extensions` VALUES(120, 'molajo', 8, '', 'extension', 1);
INSERT INTO `molajo_extensions` VALUES(121, 'extend', 8, '', 'molajo', 1);
INSERT INTO `molajo_extensions` VALUES(122, 'minifier', 8, '', 'molajo', 1);
INSERT INTO `molajo_extensions` VALUES(123, 'search', 8, '', 'molajo', 1);
INSERT INTO `molajo_extensions` VALUES(124, 'tags', 8, '', 'molajo', 1);
INSERT INTO `molajo_extensions` VALUES(125, 'urls', 8, '', 'molajo', 1);
INSERT INTO `molajo_extensions` VALUES(126, 'molajosample', 8, '', 'query', 1);
INSERT INTO `molajo_extensions` VALUES(127, 'categories', 8, '', 'search', 1);
INSERT INTO `molajo_extensions` VALUES(128, 'articles', 8, '', 'search', 1);
INSERT INTO `molajo_extensions` VALUES(129, 'cache', 8, '', 'system', 1);
INSERT INTO `molajo_extensions` VALUES(130, 'compress', 8, '', 'system', 1);
INSERT INTO `molajo_extensions` VALUES(131, 'create', 8, '', 'system', 1);
INSERT INTO `molajo_extensions` VALUES(132, 'debug', 8, '', 'system', 1);
INSERT INTO `molajo_extensions` VALUES(133, 'languagefilter', 8, '', 'system', 1);
INSERT INTO `molajo_extensions` VALUES(134, 'log', 8, '', 'system', 1);
INSERT INTO `molajo_extensions` VALUES(135, 'logout', 8, '', 'system', 1);
INSERT INTO `molajo_extensions` VALUES(136, 'molajo', 8, '', 'system', 1);
INSERT INTO `molajo_extensions` VALUES(137, 'p3p', 8, '', 'system', 1);
INSERT INTO `molajo_extensions` VALUES(138, 'parameters', 8, '', 'system', 1);
INSERT INTO `molajo_extensions` VALUES(139, 'redirect', 8, '', 'system', 1);
INSERT INTO `molajo_extensions` VALUES(140, 'remember', 8, '', 'system', 1);
INSERT INTO `molajo_extensions` VALUES(141, 'system', 8, '', 'system', 1);
INSERT INTO `molajo_extensions` VALUES(142, 'webservices', 8, '', 'system', 1);
INSERT INTO `molajo_extensions` VALUES(143, 'molajo', 8, '', 'user', 1);
INSERT INTO `molajo_extensions` VALUES(144, 'profile', 8, '', 'user', 1);
INSERT INTO `molajo_extensions` VALUES(145, 'construct', 9, '', '', 1);
INSERT INTO `molajo_extensions` VALUES(146, 'install', 9, '', '', 1);
INSERT INTO `molajo_extensions` VALUES(147, 'molajito', 9, '', '', 1);
INSERT INTO `molajo_extensions` VALUES(148, 'system', 9, '', '', 1);
INSERT INTO `molajo_extensions` VALUES(1000, 'Administrator Home', 5, '', '', 1);
INSERT INTO `molajo_extensions` VALUES(1010, 'Launchpad Main Menu', 5, '', '', 1);
INSERT INTO `molajo_extensions` VALUES(1020, 'Launchpad Configure', 5, '', '', 1);
INSERT INTO `molajo_extensions` VALUES(1030, 'Launchpad Access', 5, '', '', 1);
INSERT INTO `molajo_extensions` VALUES(1040, 'Launchpad Create', 5, '', '', 1);
INSERT INTO `molajo_extensions` VALUES(1050, 'Launchpad Build', 5, '', '', 1);
INSERT INTO `molajo_extensions` VALUES(1060, 'Main Menu', 5, '', '', 1);

-- --------------------------------------------------------

--
-- Table structure for table `molajo_extension_criteria`
--

CREATE TABLE `molajo_extension_criteria` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `extension_instance_id` int(11) unsigned NOT NULL,
  `extension_type_id` int(11) unsigned NOT NULL,
  `position` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_extension_criteria_extension_types2` (`extension_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `molajo_extension_criteria`
--


-- --------------------------------------------------------

--
-- Table structure for table `molajo_extension_instances`
--

CREATE TABLE `molajo_extension_instances` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary Key',
  `extension_type_id` int(11) unsigned NOT NULL DEFAULT '0',
  `extension_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Primary Key for Component Content',
  `title` varchar(255) NOT NULL DEFAULT ' ' COMMENT 'Title',
  `subtitle` varchar(255) NOT NULL DEFAULT ' ' COMMENT 'Subtitle',
  `alias` varchar(255) NOT NULL DEFAULT ' ',
  `content_text` mediumtext COMMENT 'Content Primary Text Field, can include break to designate Introductory and Full text',
  `protected` tinyint(1) NOT NULL DEFAULT '0',
  `enabled` tinyint(1) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Published State 2: Archived 1: Published 0: Unpublished -1: Trashed -2: Spam -10 Version',
  `start_publishing_datetime` datetime DEFAULT '0000-00-00 00:00:00' COMMENT 'Publish Begin Date and Time',
  `stop_publishing_datetime` datetime DEFAULT '0000-00-00 00:00:00' COMMENT 'Publish End Date and Time',
  `version` int(11) unsigned NOT NULL DEFAULT '1' COMMENT 'Version Number',
  `version_of_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Primary ID for this Version',
  `status_prior_to_version` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'State value prior to creating this version copy and changing the state to Version',
  `created_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Created Date and Time',
  `created_by` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Created by User ID',
  `modified_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Modified Date',
  `modified_by` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Modified By User ID',
  `checked_out_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Checked out Date and Time',
  `checked_out_by` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Checked out by User Id',
  `custom_fields` mediumtext,
  `parameters` mediumtext COMMENT 'Attributes (Custom Fields)',
  `position` varchar(50) NOT NULL DEFAULT ' ' COMMENT 'User DEFAULT 1-DEFAULT 0-Not DEFAULT',
  `menu_item_parent_id` int(11) unsigned NOT NULL DEFAULT '0',
  `menu_item_level` int(11) NOT NULL DEFAULT '0',
  `menu_item_type` varchar(45) NOT NULL DEFAULT '',
  `menu_item_extension_id` int(11) unsigned NOT NULL DEFAULT '0',
  `menu_item_template_id` int(11) unsigned NOT NULL DEFAULT '0',
  `menu_item_link_target` varchar(45) DEFAULT NULL,
  `menu_item_lft` int(11) unsigned NOT NULL DEFAULT '0',
  `menu_item_rgt` int(11) unsigned NOT NULL DEFAULT '0',
  `menu_item_home` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `menu_item_sef_request` varchar(2048) DEFAULT '',
  `menu_item_request` varchar(2048) DEFAULT '',
  `language` char(7) DEFAULT 'en-GB',
  `translation_of_id` int(11) unsigned NOT NULL DEFAULT '0',
  `ordering` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Ordering',
  PRIMARY KEY (`id`),
  KEY `idx_component_component_id_id` (`extension_id`,`id`),
  KEY `idx_checkout` (`checked_out_by`),
  KEY `idx_state` (`status`),
  KEY `idx_createdby` (`created_by`),
  KEY `fk_extension_instances_extensions2` (`extension_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=220 ;

--
-- Dumping data for table `molajo_extension_instances`
--

INSERT INTO `molajo_extension_instances` VALUES(1, 0, 1, 'Core', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(2, 1, 2, 'com_admin', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(3, 1, 3, 'com_articles', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(4, 1, 4, 'com_categories', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(5, 1, 5, 'com_config', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(6, 1, 6, 'com_dashboard', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(7, 1, 7, 'com_extensions', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(8, 1, 8, 'com_installer', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(9, 1, 9, 'com_layouts', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(10, 1, 10, 'com_login', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(11, 1, 11, 'com_media', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(12, 1, 12, 'com_menus', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(13, 1, 13, 'com_modules', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(14, 1, 14, 'com_plugins', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(15, 1, 15, 'com_redirect', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(16, 1, 16, 'com_search', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(17, 1, 17, 'com_templates', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(18, 1, 18, 'com_admin', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(19, 1, 19, 'com_users', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(33, 2, 20, 'English (UK)', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(34, 2, 21, 'English (US)', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(36, 3, 22, 'head', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(37, 3, 23, 'messages', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(38, 3, 24, 'errors', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(39, 3, 25, 'atom', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(40, 3, 26, 'rss', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(41, 3, 27, 'admin_acl_panel', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(42, 3, 28, 'admin_activity', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(43, 3, 29, 'admin_edit', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(44, 3, 30, 'admin_favorites', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(45, 3, 31, 'admin_feed', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(46, 3, 32, 'admin_footer', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(47, 3, 33, 'admin_header', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(48, 3, 34, 'admin_inbox', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(49, 3, 35, 'admin_launchpad', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(50, 3, 36, 'admin_list', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(51, 3, 37, 'admin_login', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(52, 3, 38, 'admin_modal', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(53, 3, 39, 'admin_pagination', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(54, 3, 40, 'admin_toolbar', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(55, 3, 41, 'audio', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(56, 3, 42, 'contact_form', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(57, 3, 43, 'default', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(58, 3, 44, 'dummy', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(59, 3, 45, 'faq', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(60, 3, 46, 'item', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(61, 3, 47, 'list', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(62, 3, 48, 'items', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(63, 3, 49, 'list', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(64, 3, 50, 'pagination', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(65, 3, 51, 'social_bookmarks', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(66, 3, 52, 'syntaxhighlighter', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(67, 3, 53, 'table', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(68, 3, 54, 'tree', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(69, 3, 55, 'twig_example', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(70, 3, 56, 'video', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(71, 3, 57, 'button', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(72, 3, 58, 'colorpicker', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(73, 3, 59, 'list', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(74, 3, 60, 'media', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(75, 3, 61, 'number', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(76, 3, 62, 'option', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(77, 3, 63, 'rules', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(78, 3, 64, 'spacer', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(79, 3, 65, 'text', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(80, 3, 66, 'textarea', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(81, 3, 67, 'user', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(82, 3, 68, 'article', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(83, 3, 69, 'aside', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(84, 3, 70, 'div', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(85, 3, 71, 'footer', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(86, 3, 72, 'horizontal', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(87, 3, 73, 'nav', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(88, 3, 74, 'none', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(89, 3, 75, 'outline', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(90, 3, 76, 'section', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(91, 3, 77, 'table', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(92, 3, 78, 'tabs', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(99, 6, 88, 'mod_breadcrumbs', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(100, 6, 89, 'mod_content', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(101, 6, 90, 'mod_custom', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(102, 6, 91, 'mod_feed', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(103, 6, 92, 'mod_header', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(104, 6, 93, 'mod_launchpad', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(105, 6, 94, 'mod_layout', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(106, 6, 95, 'mod_login', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(107, 6, 96, 'mod_logout', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(108, 6, 97, 'mod_members', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(109, 6, 98, 'mod_menu', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(110, 6, 99, 'mod_pagination', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(111, 6, 100, 'mod_search', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(112, 6, 101, 'mod_syndicate', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(113, 6, 102, 'mod_toolbar', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(114, 8, 103, 'example', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(115, 8, 104, 'molajo', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(116, 8, 105, 'broadcast', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(117, 8, 106, 'content', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(118, 8, 107, 'emailcloak', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(119, 8, 108, 'links', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(120, 8, 109, 'loadmodule', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(121, 8, 110, 'media', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(122, 8, 111, 'protect', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(123, 8, 112, 'responses', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(124, 8, 113, 'aloha', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(125, 8, 114, 'none', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(126, 8, 115, 'article', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(127, 8, 116, 'editor', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(128, 8, 117, 'image', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(129, 8, 118, 'pagebreak', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(130, 8, 119, 'readmore', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(131, 8, 120, 'molajo', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(132, 8, 121, 'extend', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(133, 8, 122, 'minifier', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(134, 8, 123, 'search', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(135, 8, 124, 'tags', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(136, 8, 125, 'urls', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(137, 8, 126, 'molajosample', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(138, 8, 127, 'categories', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(139, 8, 128, 'articles', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(140, 8, 129, 'cache', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(141, 8, 130, 'compress', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(142, 8, 131, 'create', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(143, 8, 132, 'debug', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(144, 8, 133, 'languagefilter', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(145, 8, 134, 'log', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(146, 8, 135, 'logout', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(147, 8, 136, 'molajo', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(148, 8, 137, 'p3p', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(149, 8, 138, 'parameters', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(150, 8, 139, 'redirect', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(151, 8, 140, 'remember', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(152, 8, 141, 'system', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(153, 8, 142, 'webservices', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(154, 8, 143, 'molajo', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(155, 8, 144, 'profile', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(177, 9, 145, 'construct', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(178, 9, 146, 'install', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(179, 9, 147, 'molajito', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(180, 9, 148, 'system', ' ', ' ', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', '', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(184, 5, 1000, 'Home', ' ', 'home', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', 'index.php?option=com_dashboard', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(185, 5, 1010, 'Configure', ' ', 'configure', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, 'configure', 'index.php?option=com_dashboard&type=configure', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(186, 5, 1010, 'Access', ' ', 'access', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, 'access', 'index.php?option=com_dashboard&type=access', 'en-GB', 0, 2);
INSERT INTO `molajo_extension_instances` VALUES(187, 5, 1010, 'Create', ' ', 'create', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, 'create', 'index.php?option=com_dashboard&type=create', 'en-GB', 0, 3);
INSERT INTO `molajo_extension_instances` VALUES(188, 5, 1010, 'Build', ' ', 'build', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, 'build', 'index.php?option=com_dashboard&type=build', 'en-GB', 0, 4);
INSERT INTO `molajo_extension_instances` VALUES(189, 5, 1010, 'Search', ' ', 'search', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, 'search', 'index.php?option=com_dashboard&type=search', 'en-GB', 0, 5);
INSERT INTO `molajo_extension_instances` VALUES(190, 5, 1020, 'Profile', ' ', 'profile', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, 'configure/profile', 'index.php?option=com_profile', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(191, 5, 1020, 'System', ' ', 'system', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, 'configure/system', 'index.php?option=com_config', 'en-GB', 0, 2);
INSERT INTO `molajo_extension_instances` VALUES(192, 5, 1020, 'Checkin', ' ', 'checkin', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, 'configure/checkin', 'index.php?option=com_checkin', 'en-GB', 0, 3);
INSERT INTO `molajo_extension_instances` VALUES(193, 5, 1020, 'Cache', ' ', 'cache', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, 'configure/cache', 'index.php?option=com_cache', 'en-GB', 0, 4);
INSERT INTO `molajo_extension_instances` VALUES(194, 5, 1020, 'Backup', ' ', 'backup', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, 'configure/backup', 'index.php?option=com_backup', 'en-GB', 0, 5);
INSERT INTO `molajo_extension_instances` VALUES(195, 5, 1020, 'Redirects', ' ', 'redirects', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, 'configure/redirects', 'index.php?option=com_redirects', 'en-GB', 0, 6);
INSERT INTO `molajo_extension_instances` VALUES(196, 5, 1030, 'Users', ' ', 'users', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, 'access/users', 'index.php?option=com_users', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(197, 5, 1030, 'Groups', ' ', 'groups', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, 'access/groups', 'index.php?option=com_groups', 'en-GB', 0, 2);
INSERT INTO `molajo_extension_instances` VALUES(198, 5, 1030, 'Permissions', ' ', 'permissions', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, 'access/permissions', 'index.php?option=com_permissions', 'en-GB', 0, 3);
INSERT INTO `molajo_extension_instances` VALUES(199, 5, 1030, 'Messages', ' ', 'messages', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, 'access/messages', 'index.php?option=com_messages', 'en-GB', 0, 4);
INSERT INTO `molajo_extension_instances` VALUES(200, 5, 1030, 'Activity', ' ', 'activity', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, 'access/activity', 'index.php?option=com_activity', 'en-GB', 0, 5);
INSERT INTO `molajo_extension_instances` VALUES(201, 5, 1040, 'Articles', ' ', 'articles', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, 'create/articles', 'index.php?option=com_articles', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(202, 5, 1040, 'Tags', ' ', 'tags', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, 'create/tags', 'index.php?option=com_tags', 'en-GB', 0, 2);
INSERT INTO `molajo_extension_instances` VALUES(203, 5, 1040, 'Comments', ' ', 'comments', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, 'create/comments', 'index.php?option=com_comments', 'en-GB', 0, 3);
INSERT INTO `molajo_extension_instances` VALUES(204, 5, 1040, 'Media', ' ', 'media', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, 'create/media', 'index.php?option=com_media', 'en-GB', 0, 4);
INSERT INTO `molajo_extension_instances` VALUES(205, 5, 1040, 'Categories', ' ', 'categories', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, 'create/categories', 'index.php?option=com_categories', 'en-GB', 0, 5);
INSERT INTO `molajo_extension_instances` VALUES(206, 5, 1050, 'Extensions', ' ', 'extensions', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, 'build/extensions', 'index.php?option=com_extensions', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(207, 5, 1050, 'Languages', ' ', 'languages', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, 'build/languages', 'index.php?option=com_languages', 'en-GB', 0, 2);
INSERT INTO `molajo_extension_instances` VALUES(208, 5, 1050, 'Layouts', ' ', 'layouts', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, 'build/layouts', 'index.php?option=com_layouts', 'en-GB', 0, 3);
INSERT INTO `molajo_extension_instances` VALUES(209, 5, 1050, 'Modules', ' ', 'modules', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, 'build/modules', 'index.php?option=com_modules', 'en-GB', 0, 4);
INSERT INTO `molajo_extension_instances` VALUES(210, 5, 1050, 'Plugins', ' ', 'plugins', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, 'build/plugins', 'index.php?option=com_plugins', 'en-GB', 0, 5);
INSERT INTO `molajo_extension_instances` VALUES(211, 5, 1050, 'Templates', ' ', 'templates', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, 'build/templates', 'index.php?option=com_templates', 'en-GB', 0, 6);
INSERT INTO `molajo_extension_instances` VALUES(212, 5, 1060, 'Home', ' ', 'home', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, '', 'index.php?option=com_articles', 'en-GB', 0, 1);
INSERT INTO `molajo_extension_instances` VALUES(213, 5, 1060, 'New Article', ' ', 'new-article', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, 'new-article', 'index.php?option=com_articles&view=article&layout=edit', 'en-GB', 0, 2);
INSERT INTO `molajo_extension_instances` VALUES(214, 5, 1060, 'Article', ' ', 'article', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, 'article', 'index.php?option=com_articles&view=articles&layout=item&id=5', 'en-GB', 0, 3);
INSERT INTO `molajo_extension_instances` VALUES(215, 5, 1060, 'Blog', ' ', 'blog', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, 'blog', 'index.php?option=com_articles&view=articles&layout=items&catid=2', 'en-GB', 0, 4);
INSERT INTO `molajo_extension_instances` VALUES(216, 5, 1060, 'List', ' ', 'list', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, 'list', 'index.php?option=com_articles&view=articles&layout=table&catid=2', 'en-GB', 0, 5);
INSERT INTO `molajo_extension_instances` VALUES(217, 5, 1060, 'Table', ' ', 'table', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, 'table', 'index.php?option=com_articles&type=search', 'en-GB', 0, 6);
INSERT INTO `molajo_extension_instances` VALUES(218, 5, 1060, 'Login', ' ', 'login', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, 'login', 'index.php?option=com_users&view=login', 'en-GB', 0, 7);
INSERT INTO `molajo_extension_instances` VALUES(219, 5, 1060, 'Search', ' ', 'search', NULL, 1, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, 0, '2011-11-01 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, NULL, NULL, ' ', 0, 0, '', 0, 0, NULL, 0, 0, 0, 'search', 'index.php?option=com_search&type=search', 'en-GB', 0, 8);

-- --------------------------------------------------------

--
-- Table structure for table `molajo_extension_types`
--

CREATE TABLE `molajo_extension_types` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Actions Primary Key',
  `extension_type` varchar(255) NOT NULL DEFAULT ' ',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_actions_table_title` (`extension_type`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=28 ;

--
-- Dumping data for table `molajo_extension_types`
--

INSERT INTO `molajo_extension_types` VALUES(1, 'components');
INSERT INTO `molajo_extension_types` VALUES(0, 'core');
INSERT INTO `molajo_extension_types` VALUES(2, 'languages');
INSERT INTO `molajo_extension_types` VALUES(3, 'layouts');
INSERT INTO `molajo_extension_types` VALUES(4, 'manifests');
INSERT INTO `molajo_extension_types` VALUES(5, 'menus');
INSERT INTO `molajo_extension_types` VALUES(6, 'modules');
INSERT INTO `molajo_extension_types` VALUES(7, 'parameters');
INSERT INTO `molajo_extension_types` VALUES(8, 'plugins');
INSERT INTO `molajo_extension_types` VALUES(9, 'templates');

-- --------------------------------------------------------

--
-- Table structure for table `molajo_groups`
--

CREATE TABLE `molajo_groups` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Group Primary Key',
  `title` varchar(255) NOT NULL DEFAULT '  ',
  `subtitle` varchar(255) NOT NULL DEFAULT ' ',
  `description` mediumtext NOT NULL,
  `protected` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'If true, protects group from system removal via the interface.',
  `custom_fields` mediumtext,
  `parameters` mediumtext,
  `parent_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Parent ID',
  `lft` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Nested set lft.',
  `rgt` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Nested set rgt.',
  `ordering` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_usergroup_parent_title_lookup` (`parent_id`,`title`),
  KEY `idx_usergroup_title_lookup` (`title`),
  KEY `idx_usergroup_adjacency_lookup` (`parent_id`),
  KEY `idx_usergroup_nested_set_lookup` (`lft`,`rgt`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `molajo_groups`
--

INSERT INTO `molajo_groups` VALUES(1, 'Public', '', 'All visitors regardless of authentication status', 1, NULL, NULL, 0, 0, 1, 1);
INSERT INTO `molajo_groups` VALUES(2, 'Guest', '', 'Visitors not authenticated', 1, NULL, NULL, 0, 2, 3, 2);
INSERT INTO `molajo_groups` VALUES(3, 'Registered', '', 'Authentication visitors', 1, NULL, NULL, 0, 4, 5, 3);
INSERT INTO `molajo_groups` VALUES(4, 'Administrator', '', 'System Administrator', 1, NULL, NULL, 0, 6, 7, 4);

-- --------------------------------------------------------

--
-- Table structure for table `molajo_group_permissions`
--

CREATE TABLE `molajo_group_permissions` (
  `group_id` int(11) unsigned NOT NULL COMMENT 'Foreign Key to #_groups.id',
  `asset_id` int(11) unsigned NOT NULL COMMENT 'Foreign Key to #__assets.id',
  `action_id` int(11) unsigned NOT NULL COMMENT 'Foreign Key to #__actions.id',
  PRIMARY KEY (`action_id`,`asset_id`,`group_id`),
  KEY `fk_group_permissions_groups2` (`group_id`),
  KEY `fk_group_permissions_assets2` (`asset_id`),
  KEY `fk_group_permissions_actions2` (`action_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `molajo_group_permissions`
--

INSERT INTO `molajo_group_permissions` VALUES(1, 2, 3);
INSERT INTO `molajo_group_permissions` VALUES(1, 3, 3);
INSERT INTO `molajo_group_permissions` VALUES(1, 4, 3);
INSERT INTO `molajo_group_permissions` VALUES(1, 5, 3);
INSERT INTO `molajo_group_permissions` VALUES(1, 10, 3);
INSERT INTO `molajo_group_permissions` VALUES(1, 11, 3);
INSERT INTO `molajo_group_permissions` VALUES(1, 12, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 13, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 14, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 15, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 16, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 17, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 18, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 19, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 20, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 21, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 22, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 23, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 24, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 25, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 26, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 27, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 28, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 29, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 30, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 44, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 45, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 47, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 48, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 49, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 50, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 51, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 52, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 53, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 54, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 55, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 56, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 57, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 58, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 59, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 60, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 61, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 62, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 63, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 64, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 65, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 66, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 67, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 68, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 69, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 70, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 71, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 72, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 73, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 74, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 75, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 76, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 77, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 78, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 79, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 80, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 81, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 82, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 83, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 84, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 85, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 86, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 87, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 88, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 89, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 90, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 91, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 92, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 93, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 94, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 95, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 96, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 97, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 98, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 99, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 100, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 101, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 102, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 103, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 110, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 111, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 112, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 113, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 114, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 115, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 116, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 117, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 118, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 119, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 120, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 121, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 122, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 123, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 124, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 125, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 126, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 127, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 128, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 129, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 130, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 131, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 132, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 133, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 134, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 135, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 136, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 137, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 138, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 139, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 140, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 141, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 142, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 143, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 144, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 145, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 146, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 147, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 148, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 149, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 150, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 151, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 152, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 153, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 154, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 155, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 156, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 157, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 158, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 159, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 160, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 161, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 162, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 163, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 164, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 165, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 166, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 188, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 189, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 190, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 191, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 195, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 196, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 197, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 198, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 199, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 200, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 201, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 202, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 203, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 204, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 205, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 206, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 207, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 208, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 209, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 210, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 211, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 212, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 213, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 214, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 215, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 216, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 217, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 218, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 219, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 220, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 221, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 222, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 223, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 224, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 225, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 226, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 227, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 228, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 229, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 230, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 13, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 14, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 15, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 16, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 17, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 18, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 19, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 20, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 21, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 22, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 23, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 24, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 25, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 26, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 27, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 28, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 29, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 30, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 44, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 45, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 47, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 48, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 49, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 50, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 51, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 52, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 53, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 54, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 55, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 56, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 57, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 58, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 59, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 60, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 61, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 62, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 63, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 64, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 65, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 66, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 67, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 68, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 69, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 70, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 71, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 72, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 73, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 74, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 75, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 76, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 77, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 78, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 79, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 80, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 81, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 82, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 83, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 84, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 85, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 86, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 87, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 88, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 89, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 90, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 91, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 92, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 93, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 94, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 95, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 96, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 97, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 98, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 99, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 100, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 101, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 102, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 103, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 110, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 111, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 112, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 113, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 114, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 115, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 116, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 117, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 118, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 119, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 120, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 121, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 122, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 123, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 124, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 125, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 126, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 127, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 128, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 129, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 130, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 131, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 132, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 133, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 134, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 135, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 136, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 137, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 138, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 139, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 140, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 141, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 142, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 143, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 144, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 145, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 146, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 147, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 148, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 149, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 150, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 151, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 152, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 153, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 154, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 155, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 156, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 157, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 158, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 159, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 160, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 161, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 162, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 163, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 164, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 165, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 166, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 188, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 189, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 190, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 191, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 195, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 196, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 197, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 198, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 199, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 200, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 201, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 202, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 203, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 204, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 205, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 206, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 207, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 208, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 209, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 210, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 211, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 212, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 213, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 214, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 215, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 216, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 217, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 218, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 219, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 220, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 221, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 222, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 223, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 224, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 225, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 226, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 227, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 228, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 229, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 230, 3);

-- --------------------------------------------------------

--
-- Table structure for table `molajo_group_view_groups`
--

CREATE TABLE `molajo_group_view_groups` (
  `group_id` int(11) unsigned NOT NULL COMMENT 'FK to the #__group table.',
  `view_group_id` int(11) unsigned NOT NULL COMMENT 'FK to the #__groupings table.',
  PRIMARY KEY (`view_group_id`,`group_id`),
  KEY `fk_group_view_groups_groups2` (`group_id`),
  KEY `fk_group_view_groups_view_groups2` (`view_group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `molajo_group_view_groups`
--

INSERT INTO `molajo_group_view_groups` VALUES(1, 1);
INSERT INTO `molajo_group_view_groups` VALUES(2, 2);
INSERT INTO `molajo_group_view_groups` VALUES(3, 3);
INSERT INTO `molajo_group_view_groups` VALUES(3, 5);
INSERT INTO `molajo_group_view_groups` VALUES(4, 4);
INSERT INTO `molajo_group_view_groups` VALUES(4, 5);

-- --------------------------------------------------------

--
-- Table structure for table `molajo_sessions`
--

CREATE TABLE `molajo_sessions` (
  `session_id` varchar(32) NOT NULL,
  `application_id` int(11) unsigned NOT NULL,
  `guest` int(1) unsigned NOT NULL DEFAULT '1',
  `session_time` varchar(14) DEFAULT ' ',
  `data` longtext,
  `userid` int(11) unsigned DEFAULT '0',
  PRIMARY KEY (`session_id`),
  KEY `fk_sessions_applications2` (`application_id`),
  KEY `fk_sessions_users2` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `molajo_sessions`
--


-- --------------------------------------------------------

--
-- Table structure for table `molajo_sites`
--

CREATE TABLE `molajo_sites` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Application Primary Key',
  `name` varchar(255) NOT NULL DEFAULT ' ' COMMENT 'Title',
  `path` varchar(2048) NOT NULL DEFAULT ' ' COMMENT 'URL Alias',
  `base_url` varchar(2048) NOT NULL DEFAULT ' ',
  `description` mediumtext,
  `parameters` mediumtext COMMENT 'Configurable Parameter Values',
  `custom_fields` mediumtext,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `molajo_sites`
--

INSERT INTO `molajo_sites` VALUES(1, 'Molajo', '1', '', 'Primary Site', '{}', '{}');
INSERT INTO `molajo_sites` VALUES(2, 'Molajo Site 2', '2', '', 'Second Site', '{}', '{}');

-- --------------------------------------------------------

--
-- Table structure for table `molajo_site_applications`
--

CREATE TABLE `molajo_site_applications` (
  `site_id` int(11) unsigned NOT NULL,
  `application_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`site_id`,`application_id`),
  KEY `fk_site_applications_sites2` (`site_id`),
  KEY `fk_site_applications_applications2` (`application_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `molajo_site_applications`
--

INSERT INTO `molajo_site_applications` VALUES(1, 1);
INSERT INTO `molajo_site_applications` VALUES(1, 2);
INSERT INTO `molajo_site_applications` VALUES(1, 3);
INSERT INTO `molajo_site_applications` VALUES(2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `molajo_source_tables`
--

CREATE TABLE `molajo_source_tables` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Actions Primary Key',
  `source_table` varchar(255) NOT NULL DEFAULT ' ',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `molajo_source_tables`
--

INSERT INTO `molajo_source_tables` VALUES(1, '__applications');
INSERT INTO `molajo_source_tables` VALUES(2, '__categories');
INSERT INTO `molajo_source_tables` VALUES(3, '__content');
INSERT INTO `molajo_source_tables` VALUES(4, '__extension_instances');
INSERT INTO `molajo_source_tables` VALUES(5, '__users');
INSERT INTO `molajo_source_tables` VALUES(6, '__groups');

-- --------------------------------------------------------

--
-- Table structure for table `molajo_update_sites`
--

CREATE TABLE `molajo_update_sites` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT ' ',
  `enabled` tinyint(1) NOT NULL DEFAULT '0',
  `extension_type_id` int(11) unsigned NOT NULL DEFAULT '0',
  `location` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_update_sites_extension_types2` (`extension_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `molajo_update_sites`
--

INSERT INTO `molajo_update_sites` VALUES(1, 'Molajo Core', 0, 0, '1');
INSERT INTO `molajo_update_sites` VALUES(2, 'Molajo Directory', 0, 0, '1');

-- --------------------------------------------------------

--
-- Table structure for table `molajo_users`
--

CREATE TABLE `molajo_users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(150) DEFAULT NULL,
  `content_text` mediumtext,
  `email` varchar(255) DEFAULT '  ',
  `password` varchar(100) NOT NULL DEFAULT '  ',
  `block` tinyint(4) NOT NULL DEFAULT '0',
  `activated` tinyint(4) NOT NULL DEFAULT '0',
  `send_email` tinyint(4) NOT NULL DEFAULT '0',
  `register_datetimetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_visit_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `parameters` mediumtext COMMENT 'Configurable Parameter Values',
  `custom_fields` mediumtext,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=101 ;

--
-- Dumping data for table `molajo_users`
--

INSERT INTO `molajo_users` VALUES(42, 'admin', 'Administrator', '', '', 'admin@example.com', 'admin', 0, 1, 0, '2011-11-01 00:00:00', '0000-00-00 00:00:00', NULL, '');
INSERT INTO `molajo_users` VALUES(100, 'mark', 'Mark', 'Robinson', '<p>Great guy who sells insurance and coaches Little League.</p>', 'mark.robinson@example.com', 'mark', 0, 1, 0, '2011-11-02 17:45:17', '0000-00-00 00:00:00', NULL, '{"favorite_color":"red","nickname":"Fred","claim_to_fame":"No search results for Mark on Google."}');

-- --------------------------------------------------------

--
-- Table structure for table `molajo_user_applications`
--

CREATE TABLE `molajo_user_applications` (
  `user_id` int(11) unsigned NOT NULL COMMENT 'Foreign Key to #__users.id',
  `application_id` int(11) unsigned NOT NULL COMMENT 'Foreign Key to #__applications.id',
  PRIMARY KEY (`application_id`,`user_id`),
  KEY `fk_user_applications_users` (`user_id`),
  KEY `fk_user_applications_applications` (`application_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `molajo_user_applications`
--

INSERT INTO `molajo_user_applications` VALUES(42, 1);
INSERT INTO `molajo_user_applications` VALUES(42, 2);
INSERT INTO `molajo_user_applications` VALUES(42, 3);
INSERT INTO `molajo_user_applications` VALUES(100, 1);
INSERT INTO `molajo_user_applications` VALUES(100, 3);

-- --------------------------------------------------------

--
-- Table structure for table `molajo_user_groups`
--

CREATE TABLE `molajo_user_groups` (
  `user_id` int(11) unsigned NOT NULL COMMENT 'Foreign Key to #__users.id',
  `group_id` int(11) unsigned NOT NULL COMMENT 'Foreign Key to #__groups.id',
  PRIMARY KEY (`group_id`,`user_id`),
  KEY `fk_molajo_user_groups_molajo_users2` (`user_id`),
  KEY `fk_molajo_user_groups_molajo_groups2` (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `molajo_user_groups`
--

INSERT INTO `molajo_user_groups` VALUES(42, 3);
INSERT INTO `molajo_user_groups` VALUES(42, 4);
INSERT INTO `molajo_user_groups` VALUES(100, 3);

-- --------------------------------------------------------

--
-- Table structure for table `molajo_user_view_groups`
--

CREATE TABLE `molajo_user_view_groups` (
  `user_id` int(11) unsigned NOT NULL COMMENT 'Foreign Key to #__users.id',
  `view_group_id` int(11) unsigned NOT NULL COMMENT 'Foreign Key to #__groupings.id',
  PRIMARY KEY (`user_id`,`view_group_id`),
  KEY `fk_user_view_groups_users2` (`user_id`),
  KEY `fk_user_view_groups_view_groups2` (`view_group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `molajo_user_view_groups`
--

INSERT INTO `molajo_user_view_groups` VALUES(42, 3);
INSERT INTO `molajo_user_view_groups` VALUES(42, 4);
INSERT INTO `molajo_user_view_groups` VALUES(100, 3);

-- --------------------------------------------------------

--
-- Table structure for table `molajo_view_groups`
--

CREATE TABLE `molajo_view_groups` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `view_group_name_list` text NOT NULL,
  `view_group_id_list` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `molajo_view_groups`
--

INSERT INTO `molajo_view_groups` VALUES(1, 'Public', '1');
INSERT INTO `molajo_view_groups` VALUES(2, 'Guest', '2');
INSERT INTO `molajo_view_groups` VALUES(3, 'Registered', '3');
INSERT INTO `molajo_view_groups` VALUES(4, 'Administrator', '4');
INSERT INTO `molajo_view_groups` VALUES(5, 'Registered, Administrator', '4,5');

-- --------------------------------------------------------

--
-- Table structure for table `molajo_view_group_permissions`
--

CREATE TABLE `molajo_view_group_permissions` (
  `view_group_id` int(11) unsigned NOT NULL COMMENT 'Foreign Key to #__groups.id',
  `asset_id` int(11) unsigned NOT NULL COMMENT 'Foreign Key to #__assets.id',
  `action_id` int(11) unsigned NOT NULL COMMENT 'Foreign Key to #__actions.id',
  PRIMARY KEY (`view_group_id`,`asset_id`,`action_id`),
  KEY `fk_view_group_permissions_view_groups2` (`view_group_id`),
  KEY `fk_view_group_permissions_actions2` (`action_id`),
  KEY `fk_view_group_permissions_assets2` (`asset_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `molajo_view_group_permissions`
--

INSERT INTO `molajo_view_group_permissions` VALUES(1, 2, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(1, 3, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(1, 4, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(1, 5, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(1, 10, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(1, 11, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(1, 12, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 13, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 14, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 15, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 16, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 17, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 18, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 19, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 20, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 21, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 22, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 23, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 24, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 25, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 26, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 27, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 28, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 29, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 30, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 44, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 45, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 47, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 48, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 49, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 50, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 51, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 52, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 53, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 54, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 55, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 56, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 57, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 58, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 59, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 60, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 61, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 62, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 63, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 64, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 65, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 66, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 67, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 68, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 69, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 70, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 71, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 72, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 73, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 74, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 75, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 76, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 77, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 78, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 79, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 80, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 81, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 82, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 83, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 84, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 85, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 86, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 87, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 88, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 89, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 90, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 91, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 92, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 93, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 94, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 95, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 96, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 97, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 98, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 99, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 100, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 101, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 102, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 103, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 110, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 111, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 112, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 113, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 114, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 115, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 116, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 117, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 118, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 119, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 120, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 121, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 122, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 123, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 124, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 125, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 126, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 127, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 128, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 129, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 130, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 131, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 132, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 133, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 134, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 135, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 136, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 137, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 138, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 139, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 140, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 141, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 142, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 143, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 144, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 145, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 146, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 147, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 148, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 149, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 150, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 151, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 152, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 153, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 154, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 155, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 156, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 157, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 158, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 159, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 160, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 161, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 162, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 163, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 164, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 165, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 166, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 188, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 189, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 190, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 191, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 195, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 196, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 197, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 198, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 199, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 200, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 201, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 202, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 203, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 204, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 205, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 206, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 207, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 208, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 209, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 210, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 211, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 212, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 213, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 214, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 215, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 216, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 217, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 218, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 219, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 220, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 221, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 222, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 223, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 224, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 225, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 226, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 227, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 228, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 229, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 230, 3);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `molajo_application_extensions`
--
ALTER TABLE `molajo_application_extensions`
  ADD CONSTRAINT `fk_application_extensions_applications1` FOREIGN KEY (`application_id`) REFERENCES `molajo_applications` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_application_extensions_extensions1` FOREIGN KEY (`extension_id`) REFERENCES `molajo_extensions` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_application_extensions_extension_instances1` FOREIGN KEY (`extension_instance_id`) REFERENCES `molajo_extension_instances` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `molajo_assets`
--
ALTER TABLE `molajo_assets`
  ADD CONSTRAINT `fk_assets_source_tables1` FOREIGN KEY (`source_table_id`) REFERENCES `molajo_source_tables` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `molajo_extension_configurations`
--
ALTER TABLE `molajo_extension_configurations`
  ADD CONSTRAINT `fk_configurations_extension_instances1` FOREIGN KEY (`extension_instances_id`) REFERENCES `molajo_extension_instances` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `molajo_content_categories`
--
ALTER TABLE `molajo_content_categories`
  ADD CONSTRAINT `fk_content_categories_categories1` FOREIGN KEY (`category_id`) REFERENCES `molajo_categories` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `molajo_extensions`
--
ALTER TABLE `molajo_extensions`
  ADD CONSTRAINT `fk_extensions_extension_types1` FOREIGN KEY (`extension_type_id`) REFERENCES `molajo_extension_types` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `molajo_extension_criteria`
--
ALTER TABLE `molajo_extension_criteria`
  ADD CONSTRAINT `fk_extension_criteria_extension_types1` FOREIGN KEY (`extension_type_id`) REFERENCES `molajo_extension_types` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `molajo_extension_instances`
--
ALTER TABLE `molajo_extension_instances`
  ADD CONSTRAINT `fk_extension_instances_extensions1` FOREIGN KEY (`extension_id`) REFERENCES `molajo_extensions` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `molajo_group_permissions`
--
ALTER TABLE `molajo_group_permissions`
  ADD CONSTRAINT `fk_group_permissions_groups1` FOREIGN KEY (`group_id`) REFERENCES `molajo_groups` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_group_permissions_assets1` FOREIGN KEY (`asset_id`) REFERENCES `molajo_assets` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_group_permissions_actions1` FOREIGN KEY (`action_id`) REFERENCES `molajo_actions` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `molajo_group_view_groups`
--
ALTER TABLE `molajo_group_view_groups`
  ADD CONSTRAINT `fk_group_view_groups_groups1` FOREIGN KEY (`group_id`) REFERENCES `molajo_groups` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_group_view_groups_view_groups1` FOREIGN KEY (`view_group_id`) REFERENCES `molajo_view_groups` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `molajo_sessions`
--
ALTER TABLE `molajo_sessions`
  ADD CONSTRAINT `fk_sessions_applications1` FOREIGN KEY (`application_id`) REFERENCES `molajo_applications` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_sessions_users1` FOREIGN KEY (`userid`) REFERENCES `molajo_users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `molajo_site_applications`
--
ALTER TABLE `molajo_site_applications`
  ADD CONSTRAINT `fk_site_applications_sites` FOREIGN KEY (`site_id`) REFERENCES `molajo_sites` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_site_applications_applications` FOREIGN KEY (`application_id`) REFERENCES `molajo_applications` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `molajo_update_sites`
--
ALTER TABLE `molajo_update_sites`
  ADD CONSTRAINT `fk_update_sites_extension_types1` FOREIGN KEY (`extension_type_id`) REFERENCES `molajo_extension_types` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `molajo_user_applications`
--
ALTER TABLE `molajo_user_applications`
  ADD CONSTRAINT `fk_user_applications_users1` FOREIGN KEY (`user_id`) REFERENCES `molajo_users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_user_applications_applications1` FOREIGN KEY (`application_id`) REFERENCES `molajo_applications` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `molajo_user_groups`
--
ALTER TABLE `molajo_user_groups`
  ADD CONSTRAINT `fk_user_groups_users1` FOREIGN KEY (`user_id`) REFERENCES `molajo_users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_user_groups_groups1` FOREIGN KEY (`group_id`) REFERENCES `molajo_groups` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `molajo_user_view_groups`
--
ALTER TABLE `molajo_user_view_groups`
  ADD CONSTRAINT `fk_user_view_groups_users1` FOREIGN KEY (`user_id`) REFERENCES `molajo_users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_user_view_groups_view_groups1` FOREIGN KEY (`view_group_id`) REFERENCES `molajo_view_groups` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `molajo_view_group_permissions`
--
ALTER TABLE `molajo_view_group_permissions`
  ADD CONSTRAINT `fk_view_group_permissions_view_groups1` FOREIGN KEY (`view_group_id`) REFERENCES `molajo_view_groups` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_view_group_permissions_actions1` FOREIGN KEY (`action_id`) REFERENCES `molajo_actions` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_view_group_permissions_assets1` FOREIGN KEY (`asset_id`) REFERENCES `molajo_assets` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
