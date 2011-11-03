-- phpMyAdmin SQL Dump
-- version 3.3.9.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 02, 2011 at 11:06 PM
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
  `asset_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'FK to the molajo_assets table.',
  `parameters` mediumtext COMMENT 'Configurable Parameter Values',
  `custom_fields` mediumtext,
  `default_template_extension_id` int(11) NOT NULL DEFAULT '0',
  `default_application_indicator` int(3) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `molajo_applications`
--

INSERT INTO `molajo_applications` VALUES(1, 'site', '', 'Primary application for site visitors', 7018, '{}', '{}', 207, 1);
INSERT INTO `molajo_applications` VALUES(2, 'administrator', 'administrator', 'Administrative site area for site construction', 7019, '{}', '{}', 209, 0);
INSERT INTO `molajo_applications` VALUES(3, 'content', 'content', 'Area for content development', 7020, '{}', '{}', 207, 0);

-- --------------------------------------------------------

--
-- Table structure for table `molajo_application_extensions`
--

CREATE TABLE `molajo_application_extensions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `application_id` int(11) NOT NULL,
  `extension_id` int(11) NOT NULL,
  `extension_instance_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=596 ;

--
-- Dumping data for table `molajo_application_extensions`
--

INSERT INTO `molajo_application_extensions` VALUES(1, 1, 2569, 32);
INSERT INTO `molajo_application_extensions` VALUES(2, 2, 2569, 32);
INSERT INTO `molajo_application_extensions` VALUES(3, 3, 2569, 32);
INSERT INTO `molajo_application_extensions` VALUES(4, 1, 2570, 33);
INSERT INTO `molajo_application_extensions` VALUES(5, 2, 2570, 33);
INSERT INTO `molajo_application_extensions` VALUES(6, 3, 2570, 33);
INSERT INTO `molajo_application_extensions` VALUES(8, 1, 2571, 35);
INSERT INTO `molajo_application_extensions` VALUES(9, 2, 2571, 35);
INSERT INTO `molajo_application_extensions` VALUES(10, 3, 2571, 35);
INSERT INTO `molajo_application_extensions` VALUES(11, 1, 2572, 36);
INSERT INTO `molajo_application_extensions` VALUES(12, 2, 2572, 36);
INSERT INTO `molajo_application_extensions` VALUES(13, 3, 2572, 36);
INSERT INTO `molajo_application_extensions` VALUES(14, 1, 2573, 37);
INSERT INTO `molajo_application_extensions` VALUES(15, 2, 2573, 37);
INSERT INTO `molajo_application_extensions` VALUES(16, 3, 2573, 37);
INSERT INTO `molajo_application_extensions` VALUES(17, 1, 2574, 38);
INSERT INTO `molajo_application_extensions` VALUES(18, 2, 2574, 38);
INSERT INTO `molajo_application_extensions` VALUES(19, 3, 2574, 38);
INSERT INTO `molajo_application_extensions` VALUES(20, 1, 2575, 39);
INSERT INTO `molajo_application_extensions` VALUES(21, 2, 2575, 39);
INSERT INTO `molajo_application_extensions` VALUES(22, 3, 2575, 39);
INSERT INTO `molajo_application_extensions` VALUES(23, 1, 2576, 40);
INSERT INTO `molajo_application_extensions` VALUES(24, 2, 2576, 40);
INSERT INTO `molajo_application_extensions` VALUES(25, 3, 2576, 40);
INSERT INTO `molajo_application_extensions` VALUES(26, 1, 2577, 41);
INSERT INTO `molajo_application_extensions` VALUES(27, 2, 2577, 41);
INSERT INTO `molajo_application_extensions` VALUES(28, 3, 2577, 41);
INSERT INTO `molajo_application_extensions` VALUES(29, 1, 2578, 42);
INSERT INTO `molajo_application_extensions` VALUES(30, 2, 2578, 42);
INSERT INTO `molajo_application_extensions` VALUES(31, 3, 2578, 42);
INSERT INTO `molajo_application_extensions` VALUES(32, 1, 2579, 43);
INSERT INTO `molajo_application_extensions` VALUES(33, 2, 2579, 43);
INSERT INTO `molajo_application_extensions` VALUES(34, 3, 2579, 43);
INSERT INTO `molajo_application_extensions` VALUES(35, 1, 2580, 44);
INSERT INTO `molajo_application_extensions` VALUES(36, 2, 2580, 44);
INSERT INTO `molajo_application_extensions` VALUES(37, 3, 2580, 44);
INSERT INTO `molajo_application_extensions` VALUES(38, 1, 2581, 45);
INSERT INTO `molajo_application_extensions` VALUES(39, 2, 2581, 45);
INSERT INTO `molajo_application_extensions` VALUES(40, 3, 2581, 45);
INSERT INTO `molajo_application_extensions` VALUES(41, 1, 2582, 46);
INSERT INTO `molajo_application_extensions` VALUES(42, 2, 2582, 46);
INSERT INTO `molajo_application_extensions` VALUES(43, 3, 2582, 46);
INSERT INTO `molajo_application_extensions` VALUES(44, 1, 2583, 47);
INSERT INTO `molajo_application_extensions` VALUES(45, 2, 2583, 47);
INSERT INTO `molajo_application_extensions` VALUES(46, 3, 2583, 47);
INSERT INTO `molajo_application_extensions` VALUES(47, 1, 2584, 48);
INSERT INTO `molajo_application_extensions` VALUES(48, 2, 2584, 48);
INSERT INTO `molajo_application_extensions` VALUES(49, 3, 2584, 48);
INSERT INTO `molajo_application_extensions` VALUES(50, 1, 2585, 49);
INSERT INTO `molajo_application_extensions` VALUES(51, 2, 2585, 49);
INSERT INTO `molajo_application_extensions` VALUES(52, 3, 2585, 49);
INSERT INTO `molajo_application_extensions` VALUES(53, 1, 2586, 50);
INSERT INTO `molajo_application_extensions` VALUES(54, 2, 2586, 50);
INSERT INTO `molajo_application_extensions` VALUES(55, 3, 2586, 50);
INSERT INTO `molajo_application_extensions` VALUES(56, 1, 2587, 51);
INSERT INTO `molajo_application_extensions` VALUES(57, 2, 2587, 51);
INSERT INTO `molajo_application_extensions` VALUES(58, 3, 2587, 51);
INSERT INTO `molajo_application_extensions` VALUES(59, 1, 2588, 52);
INSERT INTO `molajo_application_extensions` VALUES(60, 2, 2588, 52);
INSERT INTO `molajo_application_extensions` VALUES(61, 3, 2588, 52);
INSERT INTO `molajo_application_extensions` VALUES(62, 1, 2589, 53);
INSERT INTO `molajo_application_extensions` VALUES(63, 2, 2589, 53);
INSERT INTO `molajo_application_extensions` VALUES(64, 3, 2589, 53);
INSERT INTO `molajo_application_extensions` VALUES(65, 1, 2590, 54);
INSERT INTO `molajo_application_extensions` VALUES(66, 2, 2590, 54);
INSERT INTO `molajo_application_extensions` VALUES(67, 3, 2590, 54);
INSERT INTO `molajo_application_extensions` VALUES(68, 1, 2591, 55);
INSERT INTO `molajo_application_extensions` VALUES(69, 2, 2591, 55);
INSERT INTO `molajo_application_extensions` VALUES(70, 3, 2591, 55);
INSERT INTO `molajo_application_extensions` VALUES(71, 1, 2592, 56);
INSERT INTO `molajo_application_extensions` VALUES(72, 2, 2592, 56);
INSERT INTO `molajo_application_extensions` VALUES(73, 3, 2592, 56);
INSERT INTO `molajo_application_extensions` VALUES(74, 1, 2593, 57);
INSERT INTO `molajo_application_extensions` VALUES(75, 2, 2593, 57);
INSERT INTO `molajo_application_extensions` VALUES(76, 3, 2593, 57);
INSERT INTO `molajo_application_extensions` VALUES(77, 1, 2594, 58);
INSERT INTO `molajo_application_extensions` VALUES(78, 2, 2594, 58);
INSERT INTO `molajo_application_extensions` VALUES(79, 3, 2594, 58);
INSERT INTO `molajo_application_extensions` VALUES(80, 1, 2595, 59);
INSERT INTO `molajo_application_extensions` VALUES(81, 2, 2595, 59);
INSERT INTO `molajo_application_extensions` VALUES(82, 3, 2595, 59);
INSERT INTO `molajo_application_extensions` VALUES(83, 1, 2596, 60);
INSERT INTO `molajo_application_extensions` VALUES(84, 2, 2596, 60);
INSERT INTO `molajo_application_extensions` VALUES(85, 3, 2596, 60);
INSERT INTO `molajo_application_extensions` VALUES(86, 1, 2597, 61);
INSERT INTO `molajo_application_extensions` VALUES(87, 2, 2597, 61);
INSERT INTO `molajo_application_extensions` VALUES(88, 3, 2597, 61);
INSERT INTO `molajo_application_extensions` VALUES(89, 1, 2598, 62);
INSERT INTO `molajo_application_extensions` VALUES(90, 2, 2598, 62);
INSERT INTO `molajo_application_extensions` VALUES(91, 3, 2598, 62);
INSERT INTO `molajo_application_extensions` VALUES(92, 1, 2599, 63);
INSERT INTO `molajo_application_extensions` VALUES(93, 2, 2599, 63);
INSERT INTO `molajo_application_extensions` VALUES(94, 3, 2599, 63);
INSERT INTO `molajo_application_extensions` VALUES(95, 1, 2600, 64);
INSERT INTO `molajo_application_extensions` VALUES(96, 2, 2600, 64);
INSERT INTO `molajo_application_extensions` VALUES(97, 3, 2600, 64);
INSERT INTO `molajo_application_extensions` VALUES(98, 1, 2601, 65);
INSERT INTO `molajo_application_extensions` VALUES(99, 2, 2601, 65);
INSERT INTO `molajo_application_extensions` VALUES(100, 3, 2601, 65);
INSERT INTO `molajo_application_extensions` VALUES(101, 1, 2602, 66);
INSERT INTO `molajo_application_extensions` VALUES(102, 2, 2602, 66);
INSERT INTO `molajo_application_extensions` VALUES(103, 3, 2602, 66);
INSERT INTO `molajo_application_extensions` VALUES(104, 1, 2603, 67);
INSERT INTO `molajo_application_extensions` VALUES(105, 2, 2603, 67);
INSERT INTO `molajo_application_extensions` VALUES(106, 3, 2603, 67);
INSERT INTO `molajo_application_extensions` VALUES(107, 1, 2604, 68);
INSERT INTO `molajo_application_extensions` VALUES(108, 2, 2604, 68);
INSERT INTO `molajo_application_extensions` VALUES(109, 3, 2604, 68);
INSERT INTO `molajo_application_extensions` VALUES(110, 1, 2605, 69);
INSERT INTO `molajo_application_extensions` VALUES(111, 2, 2605, 69);
INSERT INTO `molajo_application_extensions` VALUES(112, 3, 2605, 69);
INSERT INTO `molajo_application_extensions` VALUES(113, 1, 2606, 70);
INSERT INTO `molajo_application_extensions` VALUES(114, 2, 2606, 70);
INSERT INTO `molajo_application_extensions` VALUES(115, 3, 2606, 70);
INSERT INTO `molajo_application_extensions` VALUES(116, 1, 2607, 71);
INSERT INTO `molajo_application_extensions` VALUES(117, 2, 2607, 71);
INSERT INTO `molajo_application_extensions` VALUES(118, 3, 2607, 71);
INSERT INTO `molajo_application_extensions` VALUES(119, 1, 2608, 72);
INSERT INTO `molajo_application_extensions` VALUES(120, 2, 2608, 72);
INSERT INTO `molajo_application_extensions` VALUES(121, 3, 2608, 72);
INSERT INTO `molajo_application_extensions` VALUES(122, 1, 2609, 73);
INSERT INTO `molajo_application_extensions` VALUES(123, 2, 2609, 73);
INSERT INTO `molajo_application_extensions` VALUES(124, 3, 2609, 73);
INSERT INTO `molajo_application_extensions` VALUES(125, 1, 2610, 74);
INSERT INTO `molajo_application_extensions` VALUES(126, 2, 2610, 74);
INSERT INTO `molajo_application_extensions` VALUES(127, 3, 2610, 74);
INSERT INTO `molajo_application_extensions` VALUES(128, 1, 2611, 75);
INSERT INTO `molajo_application_extensions` VALUES(129, 2, 2611, 75);
INSERT INTO `molajo_application_extensions` VALUES(130, 3, 2611, 75);
INSERT INTO `molajo_application_extensions` VALUES(131, 1, 2612, 76);
INSERT INTO `molajo_application_extensions` VALUES(132, 2, 2612, 76);
INSERT INTO `molajo_application_extensions` VALUES(133, 3, 2612, 76);
INSERT INTO `molajo_application_extensions` VALUES(134, 1, 2613, 77);
INSERT INTO `molajo_application_extensions` VALUES(135, 2, 2613, 77);
INSERT INTO `molajo_application_extensions` VALUES(136, 3, 2613, 77);
INSERT INTO `molajo_application_extensions` VALUES(137, 1, 2614, 78);
INSERT INTO `molajo_application_extensions` VALUES(138, 2, 2614, 78);
INSERT INTO `molajo_application_extensions` VALUES(139, 3, 2614, 78);
INSERT INTO `molajo_application_extensions` VALUES(140, 1, 2615, 79);
INSERT INTO `molajo_application_extensions` VALUES(141, 2, 2615, 79);
INSERT INTO `molajo_application_extensions` VALUES(142, 3, 2615, 79);
INSERT INTO `molajo_application_extensions` VALUES(143, 1, 2616, 80);
INSERT INTO `molajo_application_extensions` VALUES(144, 2, 2616, 80);
INSERT INTO `molajo_application_extensions` VALUES(145, 3, 2616, 80);
INSERT INTO `molajo_application_extensions` VALUES(146, 1, 2617, 81);
INSERT INTO `molajo_application_extensions` VALUES(147, 2, 2617, 81);
INSERT INTO `molajo_application_extensions` VALUES(148, 3, 2617, 81);
INSERT INTO `molajo_application_extensions` VALUES(149, 1, 2618, 82);
INSERT INTO `molajo_application_extensions` VALUES(150, 2, 2618, 82);
INSERT INTO `molajo_application_extensions` VALUES(151, 3, 2618, 82);
INSERT INTO `molajo_application_extensions` VALUES(152, 1, 2619, 83);
INSERT INTO `molajo_application_extensions` VALUES(153, 2, 2619, 83);
INSERT INTO `molajo_application_extensions` VALUES(154, 3, 2619, 83);
INSERT INTO `molajo_application_extensions` VALUES(155, 1, 2620, 84);
INSERT INTO `molajo_application_extensions` VALUES(156, 2, 2620, 84);
INSERT INTO `molajo_application_extensions` VALUES(157, 3, 2620, 84);
INSERT INTO `molajo_application_extensions` VALUES(158, 1, 2621, 85);
INSERT INTO `molajo_application_extensions` VALUES(159, 2, 2621, 85);
INSERT INTO `molajo_application_extensions` VALUES(160, 3, 2621, 85);
INSERT INTO `molajo_application_extensions` VALUES(161, 1, 2622, 86);
INSERT INTO `molajo_application_extensions` VALUES(162, 2, 2622, 86);
INSERT INTO `molajo_application_extensions` VALUES(163, 3, 2622, 86);
INSERT INTO `molajo_application_extensions` VALUES(164, 1, 2623, 87);
INSERT INTO `molajo_application_extensions` VALUES(165, 2, 2623, 87);
INSERT INTO `molajo_application_extensions` VALUES(166, 3, 2623, 87);
INSERT INTO `molajo_application_extensions` VALUES(167, 1, 2624, 88);
INSERT INTO `molajo_application_extensions` VALUES(168, 2, 2624, 88);
INSERT INTO `molajo_application_extensions` VALUES(169, 3, 2624, 88);
INSERT INTO `molajo_application_extensions` VALUES(170, 1, 2625, 89);
INSERT INTO `molajo_application_extensions` VALUES(171, 2, 2625, 89);
INSERT INTO `molajo_application_extensions` VALUES(172, 3, 2625, 89);
INSERT INTO `molajo_application_extensions` VALUES(173, 1, 2626, 90);
INSERT INTO `molajo_application_extensions` VALUES(174, 2, 2626, 90);
INSERT INTO `molajo_application_extensions` VALUES(175, 3, 2626, 90);
INSERT INTO `molajo_application_extensions` VALUES(176, 1, 2627, 91);
INSERT INTO `molajo_application_extensions` VALUES(177, 2, 2627, 91);
INSERT INTO `molajo_application_extensions` VALUES(178, 3, 2627, 91);
INSERT INTO `molajo_application_extensions` VALUES(263, 1, 1060, 243);
INSERT INTO `molajo_application_extensions` VALUES(264, 1, 1060, 244);
INSERT INTO `molajo_application_extensions` VALUES(265, 1, 1060, 245);
INSERT INTO `molajo_application_extensions` VALUES(266, 1, 1060, 246);
INSERT INTO `molajo_application_extensions` VALUES(267, 1, 1060, 247);
INSERT INTO `molajo_application_extensions` VALUES(268, 1, 1060, 248);
INSERT INTO `molajo_application_extensions` VALUES(269, 1, 1060, 249);
INSERT INTO `molajo_application_extensions` VALUES(270, 1, 1060, 250);
INSERT INTO `molajo_application_extensions` VALUES(271, 1, 1060, 251);
INSERT INTO `molajo_application_extensions` VALUES(272, 2, 1060, 243);
INSERT INTO `molajo_application_extensions` VALUES(273, 2, 1060, 244);
INSERT INTO `molajo_application_extensions` VALUES(274, 2, 1060, 245);
INSERT INTO `molajo_application_extensions` VALUES(275, 2, 1060, 246);
INSERT INTO `molajo_application_extensions` VALUES(276, 2, 1060, 247);
INSERT INTO `molajo_application_extensions` VALUES(277, 2, 1060, 248);
INSERT INTO `molajo_application_extensions` VALUES(278, 2, 1060, 249);
INSERT INTO `molajo_application_extensions` VALUES(279, 2, 1060, 250);
INSERT INTO `molajo_application_extensions` VALUES(280, 2, 1060, 251);
INSERT INTO `molajo_application_extensions` VALUES(294, 3, 1000, 215);
INSERT INTO `molajo_application_extensions` VALUES(295, 3, 1010, 216);
INSERT INTO `molajo_application_extensions` VALUES(296, 3, 1010, 217);
INSERT INTO `molajo_application_extensions` VALUES(297, 3, 1010, 218);
INSERT INTO `molajo_application_extensions` VALUES(298, 3, 1010, 219);
INSERT INTO `molajo_application_extensions` VALUES(299, 3, 1010, 220);
INSERT INTO `molajo_application_extensions` VALUES(300, 3, 1020, 221);
INSERT INTO `molajo_application_extensions` VALUES(301, 3, 1020, 222);
INSERT INTO `molajo_application_extensions` VALUES(302, 3, 1020, 223);
INSERT INTO `molajo_application_extensions` VALUES(303, 3, 1020, 224);
INSERT INTO `molajo_application_extensions` VALUES(304, 3, 1020, 225);
INSERT INTO `molajo_application_extensions` VALUES(305, 3, 1020, 226);
INSERT INTO `molajo_application_extensions` VALUES(306, 3, 1030, 227);
INSERT INTO `molajo_application_extensions` VALUES(307, 3, 1030, 228);
INSERT INTO `molajo_application_extensions` VALUES(308, 3, 1030, 229);
INSERT INTO `molajo_application_extensions` VALUES(309, 3, 1030, 230);
INSERT INTO `molajo_application_extensions` VALUES(310, 3, 1030, 231);
INSERT INTO `molajo_application_extensions` VALUES(311, 3, 1040, 232);
INSERT INTO `molajo_application_extensions` VALUES(312, 3, 1040, 233);
INSERT INTO `molajo_application_extensions` VALUES(313, 3, 1040, 234);
INSERT INTO `molajo_application_extensions` VALUES(314, 3, 1040, 235);
INSERT INTO `molajo_application_extensions` VALUES(315, 3, 1040, 236);
INSERT INTO `molajo_application_extensions` VALUES(316, 3, 1050, 237);
INSERT INTO `molajo_application_extensions` VALUES(317, 3, 1050, 238);
INSERT INTO `molajo_application_extensions` VALUES(318, 3, 1050, 239);
INSERT INTO `molajo_application_extensions` VALUES(319, 3, 1050, 240);
INSERT INTO `molajo_application_extensions` VALUES(320, 3, 1050, 241);
INSERT INTO `molajo_application_extensions` VALUES(321, 3, 1050, 242);
INSERT INTO `molajo_application_extensions` VALUES(325, 1, 2636, 113);
INSERT INTO `molajo_application_extensions` VALUES(326, 2, 2636, 113);
INSERT INTO `molajo_application_extensions` VALUES(327, 1, 2637, 114);
INSERT INTO `molajo_application_extensions` VALUES(328, 2, 2637, 114);
INSERT INTO `molajo_application_extensions` VALUES(329, 1, 2638, 115);
INSERT INTO `molajo_application_extensions` VALUES(330, 2, 2638, 115);
INSERT INTO `molajo_application_extensions` VALUES(331, 1, 2639, 116);
INSERT INTO `molajo_application_extensions` VALUES(332, 2, 2639, 116);
INSERT INTO `molajo_application_extensions` VALUES(333, 1, 2640, 117);
INSERT INTO `molajo_application_extensions` VALUES(334, 2, 2640, 117);
INSERT INTO `molajo_application_extensions` VALUES(335, 1, 2642, 119);
INSERT INTO `molajo_application_extensions` VALUES(336, 2, 2642, 119);
INSERT INTO `molajo_application_extensions` VALUES(337, 1, 2643, 120);
INSERT INTO `molajo_application_extensions` VALUES(338, 2, 2643, 120);
INSERT INTO `molajo_application_extensions` VALUES(339, 1, 2644, 121);
INSERT INTO `molajo_application_extensions` VALUES(340, 2, 2644, 121);
INSERT INTO `molajo_application_extensions` VALUES(341, 1, 2646, 123);
INSERT INTO `molajo_application_extensions` VALUES(342, 2, 2646, 123);
INSERT INTO `molajo_application_extensions` VALUES(343, 1, 2647, 124);
INSERT INTO `molajo_application_extensions` VALUES(344, 2, 2647, 124);
INSERT INTO `molajo_application_extensions` VALUES(345, 1, 2648, 125);
INSERT INTO `molajo_application_extensions` VALUES(346, 2, 2648, 125);
INSERT INTO `molajo_application_extensions` VALUES(347, 1, 2649, 126);
INSERT INTO `molajo_application_extensions` VALUES(348, 2, 2649, 126);
INSERT INTO `molajo_application_extensions` VALUES(349, 1, 2651, 128);
INSERT INTO `molajo_application_extensions` VALUES(350, 2, 2651, 128);
INSERT INTO `molajo_application_extensions` VALUES(351, 1, 2652, 129);
INSERT INTO `molajo_application_extensions` VALUES(352, 2, 2652, 129);
INSERT INTO `molajo_application_extensions` VALUES(353, 1, 2653, 130);
INSERT INTO `molajo_application_extensions` VALUES(354, 2, 2653, 130);
INSERT INTO `molajo_application_extensions` VALUES(355, 1, 2654, 131);
INSERT INTO `molajo_application_extensions` VALUES(356, 2, 2654, 131);
INSERT INTO `molajo_application_extensions` VALUES(357, 1, 2655, 132);
INSERT INTO `molajo_application_extensions` VALUES(358, 2, 2655, 132);
INSERT INTO `molajo_application_extensions` VALUES(359, 1, 2657, 134);
INSERT INTO `molajo_application_extensions` VALUES(360, 2, 2657, 134);
INSERT INTO `molajo_application_extensions` VALUES(361, 1, 2658, 135);
INSERT INTO `molajo_application_extensions` VALUES(362, 2, 2658, 135);
INSERT INTO `molajo_application_extensions` VALUES(363, 1, 2659, 136);
INSERT INTO `molajo_application_extensions` VALUES(364, 2, 2659, 136);
INSERT INTO `molajo_application_extensions` VALUES(365, 1, 2661, 138);
INSERT INTO `molajo_application_extensions` VALUES(366, 2, 2661, 138);
INSERT INTO `molajo_application_extensions` VALUES(367, 1, 2662, 139);
INSERT INTO `molajo_application_extensions` VALUES(368, 2, 2662, 139);
INSERT INTO `molajo_application_extensions` VALUES(369, 1, 2663, 140);
INSERT INTO `molajo_application_extensions` VALUES(370, 2, 2663, 140);
INSERT INTO `molajo_application_extensions` VALUES(371, 1, 2664, 141);
INSERT INTO `molajo_application_extensions` VALUES(372, 2, 2664, 141);
INSERT INTO `molajo_application_extensions` VALUES(388, 3, 2637, 114);
INSERT INTO `molajo_application_extensions` VALUES(389, 3, 2638, 115);
INSERT INTO `molajo_application_extensions` VALUES(390, 3, 2639, 116);
INSERT INTO `molajo_application_extensions` VALUES(391, 3, 2640, 117);
INSERT INTO `molajo_application_extensions` VALUES(392, 3, 2641, 118);
INSERT INTO `molajo_application_extensions` VALUES(393, 3, 2642, 119);
INSERT INTO `molajo_application_extensions` VALUES(394, 3, 2643, 120);
INSERT INTO `molajo_application_extensions` VALUES(395, 3, 2644, 121);
INSERT INTO `molajo_application_extensions` VALUES(396, 3, 2645, 122);
INSERT INTO `molajo_application_extensions` VALUES(397, 3, 2646, 123);
INSERT INTO `molajo_application_extensions` VALUES(398, 3, 2647, 124);
INSERT INTO `molajo_application_extensions` VALUES(399, 3, 2648, 125);
INSERT INTO `molajo_application_extensions` VALUES(400, 3, 2650, 127);
INSERT INTO `molajo_application_extensions` VALUES(401, 3, 2652, 129);
INSERT INTO `molajo_application_extensions` VALUES(402, 3, 2653, 130);
INSERT INTO `molajo_application_extensions` VALUES(403, 3, 2654, 131);
INSERT INTO `molajo_application_extensions` VALUES(404, 3, 2655, 132);
INSERT INTO `molajo_application_extensions` VALUES(405, 3, 2656, 133);
INSERT INTO `molajo_application_extensions` VALUES(406, 3, 2657, 134);
INSERT INTO `molajo_application_extensions` VALUES(407, 3, 2658, 135);
INSERT INTO `molajo_application_extensions` VALUES(408, 3, 2659, 136);
INSERT INTO `molajo_application_extensions` VALUES(409, 3, 2660, 137);
INSERT INTO `molajo_application_extensions` VALUES(410, 3, 2661, 138);
INSERT INTO `molajo_application_extensions` VALUES(411, 3, 2662, 139);
INSERT INTO `molajo_application_extensions` VALUES(412, 3, 2663, 140);
INSERT INTO `molajo_application_extensions` VALUES(413, 3, 2665, 142);
INSERT INTO `molajo_application_extensions` VALUES(419, 1, 2666, 144);
INSERT INTO `molajo_application_extensions` VALUES(420, 2, 2666, 144);
INSERT INTO `molajo_application_extensions` VALUES(421, 3, 2666, 144);
INSERT INTO `molajo_application_extensions` VALUES(422, 1, 2667, 145);
INSERT INTO `molajo_application_extensions` VALUES(423, 2, 2667, 145);
INSERT INTO `molajo_application_extensions` VALUES(424, 3, 2667, 145);
INSERT INTO `molajo_application_extensions` VALUES(425, 1, 2668, 146);
INSERT INTO `molajo_application_extensions` VALUES(426, 2, 2668, 146);
INSERT INTO `molajo_application_extensions` VALUES(427, 3, 2668, 146);
INSERT INTO `molajo_application_extensions` VALUES(428, 1, 2669, 147);
INSERT INTO `molajo_application_extensions` VALUES(429, 2, 2669, 147);
INSERT INTO `molajo_application_extensions` VALUES(430, 3, 2669, 147);
INSERT INTO `molajo_application_extensions` VALUES(431, 1, 2670, 148);
INSERT INTO `molajo_application_extensions` VALUES(432, 2, 2670, 148);
INSERT INTO `molajo_application_extensions` VALUES(433, 3, 2670, 148);
INSERT INTO `molajo_application_extensions` VALUES(434, 1, 2671, 149);
INSERT INTO `molajo_application_extensions` VALUES(435, 2, 2671, 149);
INSERT INTO `molajo_application_extensions` VALUES(436, 3, 2671, 149);
INSERT INTO `molajo_application_extensions` VALUES(437, 1, 2672, 150);
INSERT INTO `molajo_application_extensions` VALUES(438, 2, 2672, 150);
INSERT INTO `molajo_application_extensions` VALUES(439, 3, 2672, 150);
INSERT INTO `molajo_application_extensions` VALUES(440, 1, 2673, 151);
INSERT INTO `molajo_application_extensions` VALUES(441, 2, 2673, 151);
INSERT INTO `molajo_application_extensions` VALUES(442, 3, 2673, 151);
INSERT INTO `molajo_application_extensions` VALUES(443, 1, 2674, 152);
INSERT INTO `molajo_application_extensions` VALUES(444, 2, 2674, 152);
INSERT INTO `molajo_application_extensions` VALUES(445, 3, 2674, 152);
INSERT INTO `molajo_application_extensions` VALUES(446, 1, 2675, 153);
INSERT INTO `molajo_application_extensions` VALUES(447, 2, 2675, 153);
INSERT INTO `molajo_application_extensions` VALUES(448, 3, 2675, 153);
INSERT INTO `molajo_application_extensions` VALUES(449, 1, 2678, 154);
INSERT INTO `molajo_application_extensions` VALUES(450, 2, 2678, 154);
INSERT INTO `molajo_application_extensions` VALUES(451, 3, 2678, 154);
INSERT INTO `molajo_application_extensions` VALUES(452, 1, 2679, 155);
INSERT INTO `molajo_application_extensions` VALUES(453, 2, 2679, 155);
INSERT INTO `molajo_application_extensions` VALUES(454, 3, 2679, 155);
INSERT INTO `molajo_application_extensions` VALUES(455, 1, 2680, 156);
INSERT INTO `molajo_application_extensions` VALUES(456, 2, 2680, 156);
INSERT INTO `molajo_application_extensions` VALUES(457, 3, 2680, 156);
INSERT INTO `molajo_application_extensions` VALUES(458, 1, 2681, 157);
INSERT INTO `molajo_application_extensions` VALUES(459, 2, 2681, 157);
INSERT INTO `molajo_application_extensions` VALUES(460, 3, 2681, 157);
INSERT INTO `molajo_application_extensions` VALUES(461, 1, 2682, 158);
INSERT INTO `molajo_application_extensions` VALUES(462, 2, 2682, 158);
INSERT INTO `molajo_application_extensions` VALUES(463, 3, 2682, 158);
INSERT INTO `molajo_application_extensions` VALUES(464, 1, 2676, 159);
INSERT INTO `molajo_application_extensions` VALUES(465, 2, 2676, 159);
INSERT INTO `molajo_application_extensions` VALUES(466, 3, 2676, 159);
INSERT INTO `molajo_application_extensions` VALUES(467, 1, 2677, 160);
INSERT INTO `molajo_application_extensions` VALUES(468, 2, 2677, 160);
INSERT INTO `molajo_application_extensions` VALUES(469, 3, 2677, 160);
INSERT INTO `molajo_application_extensions` VALUES(470, 1, 2683, 161);
INSERT INTO `molajo_application_extensions` VALUES(471, 2, 2683, 161);
INSERT INTO `molajo_application_extensions` VALUES(472, 3, 2683, 161);
INSERT INTO `molajo_application_extensions` VALUES(473, 1, 2684, 162);
INSERT INTO `molajo_application_extensions` VALUES(474, 2, 2684, 162);
INSERT INTO `molajo_application_extensions` VALUES(475, 3, 2684, 162);
INSERT INTO `molajo_application_extensions` VALUES(476, 1, 2685, 163);
INSERT INTO `molajo_application_extensions` VALUES(477, 2, 2685, 163);
INSERT INTO `molajo_application_extensions` VALUES(478, 3, 2685, 163);
INSERT INTO `molajo_application_extensions` VALUES(479, 1, 2686, 164);
INSERT INTO `molajo_application_extensions` VALUES(480, 2, 2686, 164);
INSERT INTO `molajo_application_extensions` VALUES(481, 3, 2686, 164);
INSERT INTO `molajo_application_extensions` VALUES(482, 1, 2687, 165);
INSERT INTO `molajo_application_extensions` VALUES(483, 2, 2687, 165);
INSERT INTO `molajo_application_extensions` VALUES(484, 3, 2687, 165);
INSERT INTO `molajo_application_extensions` VALUES(485, 1, 2688, 166);
INSERT INTO `molajo_application_extensions` VALUES(486, 2, 2688, 166);
INSERT INTO `molajo_application_extensions` VALUES(487, 3, 2688, 166);
INSERT INTO `molajo_application_extensions` VALUES(488, 1, 2689, 167);
INSERT INTO `molajo_application_extensions` VALUES(489, 2, 2689, 167);
INSERT INTO `molajo_application_extensions` VALUES(490, 3, 2689, 167);
INSERT INTO `molajo_application_extensions` VALUES(491, 1, 2690, 168);
INSERT INTO `molajo_application_extensions` VALUES(492, 2, 2690, 168);
INSERT INTO `molajo_application_extensions` VALUES(493, 3, 2690, 168);
INSERT INTO `molajo_application_extensions` VALUES(494, 1, 2691, 169);
INSERT INTO `molajo_application_extensions` VALUES(495, 2, 2691, 169);
INSERT INTO `molajo_application_extensions` VALUES(496, 3, 2691, 169);
INSERT INTO `molajo_application_extensions` VALUES(497, 1, 2692, 170);
INSERT INTO `molajo_application_extensions` VALUES(498, 2, 2692, 170);
INSERT INTO `molajo_application_extensions` VALUES(499, 3, 2692, 170);
INSERT INTO `molajo_application_extensions` VALUES(500, 1, 2693, 171);
INSERT INTO `molajo_application_extensions` VALUES(501, 2, 2693, 171);
INSERT INTO `molajo_application_extensions` VALUES(502, 3, 2693, 171);
INSERT INTO `molajo_application_extensions` VALUES(503, 1, 2694, 172);
INSERT INTO `molajo_application_extensions` VALUES(504, 2, 2694, 172);
INSERT INTO `molajo_application_extensions` VALUES(505, 3, 2694, 172);
INSERT INTO `molajo_application_extensions` VALUES(506, 1, 2695, 173);
INSERT INTO `molajo_application_extensions` VALUES(507, 2, 2695, 173);
INSERT INTO `molajo_application_extensions` VALUES(508, 3, 2695, 173);
INSERT INTO `molajo_application_extensions` VALUES(509, 1, 2696, 174);
INSERT INTO `molajo_application_extensions` VALUES(510, 2, 2696, 174);
INSERT INTO `molajo_application_extensions` VALUES(511, 3, 2696, 174);
INSERT INTO `molajo_application_extensions` VALUES(512, 1, 2697, 175);
INSERT INTO `molajo_application_extensions` VALUES(513, 2, 2697, 175);
INSERT INTO `molajo_application_extensions` VALUES(514, 3, 2697, 175);
INSERT INTO `molajo_application_extensions` VALUES(515, 1, 2698, 176);
INSERT INTO `molajo_application_extensions` VALUES(516, 2, 2698, 176);
INSERT INTO `molajo_application_extensions` VALUES(517, 3, 2698, 176);
INSERT INTO `molajo_application_extensions` VALUES(518, 1, 2699, 177);
INSERT INTO `molajo_application_extensions` VALUES(519, 2, 2699, 177);
INSERT INTO `molajo_application_extensions` VALUES(520, 3, 2699, 177);
INSERT INTO `molajo_application_extensions` VALUES(521, 1, 2700, 178);
INSERT INTO `molajo_application_extensions` VALUES(522, 2, 2700, 178);
INSERT INTO `molajo_application_extensions` VALUES(523, 3, 2700, 178);
INSERT INTO `molajo_application_extensions` VALUES(524, 1, 2701, 179);
INSERT INTO `molajo_application_extensions` VALUES(525, 2, 2701, 179);
INSERT INTO `molajo_application_extensions` VALUES(526, 3, 2701, 179);
INSERT INTO `molajo_application_extensions` VALUES(527, 1, 2702, 180);
INSERT INTO `molajo_application_extensions` VALUES(528, 2, 2702, 180);
INSERT INTO `molajo_application_extensions` VALUES(529, 3, 2702, 180);
INSERT INTO `molajo_application_extensions` VALUES(530, 1, 2703, 181);
INSERT INTO `molajo_application_extensions` VALUES(531, 2, 2703, 181);
INSERT INTO `molajo_application_extensions` VALUES(532, 3, 2703, 181);
INSERT INTO `molajo_application_extensions` VALUES(533, 1, 2704, 182);
INSERT INTO `molajo_application_extensions` VALUES(534, 2, 2704, 182);
INSERT INTO `molajo_application_extensions` VALUES(535, 3, 2704, 182);
INSERT INTO `molajo_application_extensions` VALUES(536, 1, 2705, 183);
INSERT INTO `molajo_application_extensions` VALUES(537, 2, 2705, 183);
INSERT INTO `molajo_application_extensions` VALUES(538, 3, 2705, 183);
INSERT INTO `molajo_application_extensions` VALUES(539, 1, 2706, 184);
INSERT INTO `molajo_application_extensions` VALUES(540, 2, 2706, 184);
INSERT INTO `molajo_application_extensions` VALUES(541, 3, 2706, 184);
INSERT INTO `molajo_application_extensions` VALUES(542, 1, 2707, 185);
INSERT INTO `molajo_application_extensions` VALUES(543, 2, 2707, 185);
INSERT INTO `molajo_application_extensions` VALUES(544, 3, 2707, 185);
INSERT INTO `molajo_application_extensions` VALUES(546, 1, 2708, 207);
INSERT INTO `molajo_application_extensions` VALUES(547, 3, 2708, 207);
INSERT INTO `molajo_application_extensions` VALUES(549, 2, 2710, 209);
INSERT INTO `molajo_application_extensions` VALUES(550, 1, 2552, 2);
INSERT INTO `molajo_application_extensions` VALUES(551, 2, 2552, 2);
INSERT INTO `molajo_application_extensions` VALUES(552, 1, 2558, 8);
INSERT INTO `molajo_application_extensions` VALUES(553, 2, 2558, 8);
INSERT INTO `molajo_application_extensions` VALUES(554, 1, 2559, 9);
INSERT INTO `molajo_application_extensions` VALUES(555, 2, 2559, 9);
INSERT INTO `molajo_application_extensions` VALUES(556, 1, 2560, 10);
INSERT INTO `molajo_application_extensions` VALUES(557, 2, 2560, 10);
INSERT INTO `molajo_application_extensions` VALUES(558, 1, 2565, 15);
INSERT INTO `molajo_application_extensions` VALUES(559, 2, 2565, 15);
INSERT INTO `molajo_application_extensions` VALUES(565, 3, 2551, 1);
INSERT INTO `molajo_application_extensions` VALUES(566, 3, 2552, 2);
INSERT INTO `molajo_application_extensions` VALUES(567, 3, 2553, 3);
INSERT INTO `molajo_application_extensions` VALUES(568, 3, 2554, 4);
INSERT INTO `molajo_application_extensions` VALUES(569, 3, 2555, 5);
INSERT INTO `molajo_application_extensions` VALUES(570, 3, 2556, 6);
INSERT INTO `molajo_application_extensions` VALUES(571, 3, 2557, 7);
INSERT INTO `molajo_application_extensions` VALUES(572, 3, 2558, 8);
INSERT INTO `molajo_application_extensions` VALUES(573, 3, 2559, 9);
INSERT INTO `molajo_application_extensions` VALUES(574, 3, 2560, 10);
INSERT INTO `molajo_application_extensions` VALUES(575, 3, 2561, 11);
INSERT INTO `molajo_application_extensions` VALUES(576, 3, 2562, 12);
INSERT INTO `molajo_application_extensions` VALUES(577, 3, 2563, 13);
INSERT INTO `molajo_application_extensions` VALUES(578, 3, 2564, 14);
INSERT INTO `molajo_application_extensions` VALUES(579, 3, 2565, 15);
INSERT INTO `molajo_application_extensions` VALUES(580, 3, 2566, 16);
INSERT INTO `molajo_application_extensions` VALUES(581, 3, 2567, 17);
INSERT INTO `molajo_application_extensions` VALUES(582, 3, 2568, 18);

-- --------------------------------------------------------

--
-- Table structure for table `molajo_assets`
--

CREATE TABLE `molajo_assets` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Assets Primary Key',
  `title` varchar(255) NOT NULL DEFAULT ' ',
  `source_table_id` int(11) NOT NULL,
  `source_id` int(11) unsigned NOT NULL COMMENT 'Content Primary Key',
  `path` varchar(2048) NOT NULL DEFAULT ' ' COMMENT 'URL',
  `link` varchar(2048) NOT NULL DEFAULT ' ' COMMENT 'The actually link the menu item refers to.',
  `view_group_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'FK to the #__groupings table',
  `language` char(7) NOT NULL DEFAULT 'en-GB',
  `translation_of_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_source_table_id_join` (`source_table_id`,`id`),
  UNIQUE KEY `idx_source_table_content_id_join` (`source_table_id`,`source_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7277 ;

--
-- Dumping data for table `molajo_assets`
--

INSERT INTO `molajo_assets` VALUES(7011, 'Administrator', 6, 4, '', '', 1, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7012, 'Guest', 6, 2, '', '', 1, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7013, 'Public', 6, 1, '', '', 1, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7014, 'Registered', 6, 3, '', '', 1, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7018, 'site', 1, 1, '', '', 1, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7019, 'administrator', 1, 2, 'administrator', '', 1, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7020, 'content', 1, 3, 'content', '', 1, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7021, 'com_admin', 4, 1, 'extensions/components/1', 'index.php?option=com_extensions&view=component&id=1', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7022, 'com_articles', 4, 2, 'extensions/components/2', 'index.php?option=com_extensions&view=component&id=2', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7023, 'com_categories', 4, 3, 'extensions/components/3', 'index.php?option=com_extensions&view=component&id=3', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7024, 'com_config', 4, 4, 'extensions/components/4', 'index.php?option=com_extensions&view=component&id=4', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7025, 'com_dashboard', 4, 5, 'extensions/components/5', 'index.php?option=com_extensions&view=component&id=5', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7026, 'com_extensions', 4, 6, 'extensions/components/6', 'index.php?option=com_extensions&view=component&id=6', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7027, 'com_installer', 4, 7, 'extensions/components/7', 'index.php?option=com_extensions&view=component&id=7', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7028, 'com_layouts', 4, 8, 'extensions/components/8', 'index.php?option=com_extensions&view=component&id=8', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7029, 'com_login', 4, 9, 'extensions/components/9', 'index.php?option=com_extensions&view=component&id=9', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7030, 'com_media', 4, 10, 'extensions/components/10', 'index.php?option=com_extensions&view=component&id=10', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7031, 'com_menus', 4, 11, 'extensions/components/11', 'index.php?option=com_extensions&view=component&id=11', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7032, 'com_modules', 4, 12, 'extensions/components/12', 'index.php?option=com_extensions&view=component&id=12', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7033, 'com_plugins', 4, 13, 'extensions/components/13', 'index.php?option=com_extensions&view=component&id=13', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7034, 'com_redirect', 4, 14, 'extensions/components/14', 'index.php?option=com_extensions&view=component&id=14', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7035, 'com_search', 4, 15, 'extensions/components/15', 'index.php?option=com_extensions&view=component&id=15', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7036, 'com_templates', 4, 16, 'extensions/components/16', 'index.php?option=com_extensions&view=component&id=16', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7037, 'com_admin', 4, 17, 'extensions/components/17', 'index.php?option=com_extensions&view=component&id=17', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7038, 'com_users', 4, 18, 'extensions/components/18', 'index.php?option=com_extensions&view=component&id=18', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7052, 'English (UK)', 2, 32, 'extensions/languages/32', 'index.php?option=com_extensions&view=language&id=32', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7053, 'English (US)', 2, 33, 'extensions/languages/33', 'index.php?option=com_extensions&view=language&id=33', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7055, 'head', 2, 35, 'extensions/layouts/35', 'index.php?option=com_extensions&view=layouts&id=35', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7056, 'messages', 2, 36, 'extensions/layouts/36', 'index.php?option=com_extensions&view=layouts&id=36', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7057, 'errors', 2, 37, 'extensions/layouts/37', 'index.php?option=com_extensions&view=layouts&id=37', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7058, 'atom', 2, 38, 'extensions/layouts/38', 'index.php?option=com_extensions&view=layouts&id=38', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7059, 'rss', 2, 39, 'extensions/layouts/39', 'index.php?option=com_extensions&view=layouts&id=39', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7060, 'admin_acl_panel', 2, 40, 'extensions/layouts/40', 'index.php?option=com_extensions&view=layouts&id=40', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7061, 'admin_activity', 2, 41, 'extensions/layouts/41', 'index.php?option=com_extensions&view=layouts&id=41', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7062, 'admin_edit', 2, 42, 'extensions/layouts/42', 'index.php?option=com_extensions&view=layouts&id=42', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7063, 'admin_favorites', 2, 43, 'extensions/layouts/43', 'index.php?option=com_extensions&view=layouts&id=43', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7064, 'admin_feed', 2, 44, 'extensions/layouts/44', 'index.php?option=com_extensions&view=layouts&id=44', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7065, 'admin_footer', 2, 45, 'extensions/layouts/45', 'index.php?option=com_extensions&view=layouts&id=45', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7066, 'admin_header', 2, 46, 'extensions/layouts/46', 'index.php?option=com_extensions&view=layouts&id=46', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7067, 'admin_inbox', 2, 47, 'extensions/layouts/47', 'index.php?option=com_extensions&view=layouts&id=47', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7068, 'admin_launchpad', 2, 48, 'extensions/layouts/48', 'index.php?option=com_extensions&view=layouts&id=48', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7069, 'admin_list', 2, 49, 'extensions/layouts/49', 'index.php?option=com_extensions&view=layouts&id=49', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7070, 'admin_login', 2, 50, 'extensions/layouts/50', 'index.php?option=com_extensions&view=layouts&id=50', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7071, 'admin_modal', 2, 51, 'extensions/layouts/51', 'index.php?option=com_extensions&view=layouts&id=51', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7072, 'admin_pagination', 2, 52, 'extensions/layouts/52', 'index.php?option=com_extensions&view=layouts&id=52', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7073, 'admin_toolbar', 2, 53, 'extensions/layouts/53', 'index.php?option=com_extensions&view=layouts&id=53', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7074, 'audio', 2, 54, 'extensions/layouts/54', 'index.php?option=com_extensions&view=layouts&id=54', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7075, 'contact_form', 2, 55, 'extensions/layouts/55', 'index.php?option=com_extensions&view=layouts&id=55', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7076, 'default', 2, 56, 'extensions/layouts/56', 'index.php?option=com_extensions&view=layouts&id=56', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7077, 'dummy', 2, 57, 'extensions/layouts/57', 'index.php?option=com_extensions&view=layouts&id=57', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7078, 'faq', 2, 58, 'extensions/layouts/58', 'index.php?option=com_extensions&view=layouts&id=58', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7079, 'item', 2, 59, 'extensions/layouts/59', 'index.php?option=com_extensions&view=layouts&id=59', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7080, 'list', 2, 60, 'extensions/layouts/60', 'index.php?option=com_extensions&view=layouts&id=60', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7081, 'items', 2, 61, 'extensions/layouts/61', 'index.php?option=com_extensions&view=layouts&id=61', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7082, 'list', 2, 62, 'extensions/layouts/62', 'index.php?option=com_extensions&view=layouts&id=62', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7083, 'pagination', 2, 63, 'extensions/layouts/63', 'index.php?option=com_extensions&view=layouts&id=63', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7084, 'social_bookmarks', 2, 64, 'extensions/layouts/64', 'index.php?option=com_extensions&view=layouts&id=64', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7085, 'syntaxhighlighter', 2, 65, 'extensions/layouts/65', 'index.php?option=com_extensions&view=layouts&id=65', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7086, 'table', 2, 66, 'extensions/layouts/66', 'index.php?option=com_extensions&view=layouts&id=66', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7087, 'tree', 2, 67, 'extensions/layouts/67', 'index.php?option=com_extensions&view=layouts&id=67', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7088, 'twig_example', 2, 68, 'extensions/layouts/68', 'index.php?option=com_extensions&view=layouts&id=68', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7089, 'video', 2, 69, 'extensions/layouts/69', 'index.php?option=com_extensions&view=layouts&id=69', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7090, 'button', 2, 70, 'extensions/layouts/70', 'index.php?option=com_extensions&view=layouts&id=70', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7091, 'colorpicker', 2, 71, 'extensions/layouts/71', 'index.php?option=com_extensions&view=layouts&id=71', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7092, 'list', 2, 72, 'extensions/layouts/72', 'index.php?option=com_extensions&view=layouts&id=72', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7093, 'media', 2, 73, 'extensions/layouts/73', 'index.php?option=com_extensions&view=layouts&id=73', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7094, 'number', 2, 74, 'extensions/layouts/74', 'index.php?option=com_extensions&view=layouts&id=74', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7095, 'option', 2, 75, 'extensions/layouts/75', 'index.php?option=com_extensions&view=layouts&id=75', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7096, 'rules', 2, 76, 'extensions/layouts/76', 'index.php?option=com_extensions&view=layouts&id=76', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7097, 'spacer', 2, 77, 'extensions/layouts/77', 'index.php?option=com_extensions&view=layouts&id=77', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7098, 'text', 2, 78, 'extensions/layouts/78', 'index.php?option=com_extensions&view=layouts&id=78', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7099, 'textarea', 2, 79, 'extensions/layouts/79', 'index.php?option=com_extensions&view=layouts&id=79', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7100, 'user', 2, 80, 'extensions/layouts/80', 'index.php?option=com_extensions&view=layouts&id=80', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7101, 'article', 2, 81, 'extensions/layouts/81', 'index.php?option=com_extensions&view=layouts&id=81', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7102, 'aside', 2, 82, 'extensions/layouts/82', 'index.php?option=com_extensions&view=layouts&id=82', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7103, 'div', 2, 83, 'extensions/layouts/83', 'index.php?option=com_extensions&view=layouts&id=83', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7104, 'footer', 2, 84, 'extensions/layouts/84', 'index.php?option=com_extensions&view=layouts&id=84', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7105, 'horizontal', 2, 85, 'extensions/layouts/85', 'index.php?option=com_extensions&view=layouts&id=85', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7106, 'nav', 2, 86, 'extensions/layouts/86', 'index.php?option=com_extensions&view=layouts&id=86', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7107, 'none', 2, 87, 'extensions/layouts/87', 'index.php?option=com_extensions&view=layouts&id=87', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7108, 'outline', 2, 88, 'extensions/layouts/88', 'index.php?option=com_extensions&view=layouts&id=88', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7109, 'section', 2, 89, 'extensions/layouts/89', 'index.php?option=com_extensions&view=layouts&id=89', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7110, 'table', 2, 90, 'extensions/layouts/90', 'index.php?option=com_extensions&view=layouts&id=90', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7111, 'tabs', 2, 91, 'extensions/layouts/91', 'index.php?option=com_extensions&view=layouts&id=91', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7118, 'akismet', 4, 98, 'extensions/components/98', 'index.php?option=com_extensions&view=component&id=98', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7119, 'Doctrine', 4, 99, 'extensions/components/99', 'index.php?option=com_extensions&view=component&id=99', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7120, 'includes', 4, 100, 'extensions/components/100', 'index.php?option=com_extensions&view=component&id=100', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7121, 'jplatform', 4, 101, 'extensions/components/101', 'index.php?option=com_extensions&view=component&id=101', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7122, 'molajo', 4, 102, 'extensions/components/102', 'index.php?option=com_extensions&view=component&id=102', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7123, 'mollom', 4, 103, 'extensions/components/103', 'index.php?option=com_extensions&view=component&id=103', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7124, 'recaptcha', 4, 104, 'extensions/components/104', 'index.php?option=com_extensions&view=component&id=104', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7125, 'Twig', 4, 105, 'extensions/components/105', 'index.php?option=com_extensions&view=component&id=105', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7133, 'mod_breadcrumbs', 4, 113, 'extensions/components/113', 'index.php?option=com_extensions&view=component&id=113', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7134, 'mod_content', 4, 114, 'extensions/components/114', 'index.php?option=com_extensions&view=component&id=114', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7135, 'mod_custom', 4, 115, 'extensions/components/115', 'index.php?option=com_extensions&view=component&id=115', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7136, 'mod_feed', 4, 116, 'extensions/components/116', 'index.php?option=com_extensions&view=component&id=116', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7137, 'mod_header', 4, 117, 'extensions/components/117', 'index.php?option=com_extensions&view=component&id=117', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7138, 'mod_launchpad', 4, 118, 'extensions/components/118', 'index.php?option=com_extensions&view=component&id=118', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7139, 'mod_layout', 4, 119, 'extensions/components/119', 'index.php?option=com_extensions&view=component&id=119', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7140, 'mod_login', 4, 120, 'extensions/components/120', 'index.php?option=com_extensions&view=component&id=120', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7141, 'mod_logout', 4, 121, 'extensions/components/121', 'index.php?option=com_extensions&view=component&id=121', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7142, 'mod_members', 4, 122, 'extensions/components/122', 'index.php?option=com_extensions&view=component&id=122', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7143, 'mod_menu', 4, 123, 'extensions/components/123', 'index.php?option=com_extensions&view=component&id=123', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7144, 'mod_pagination', 4, 124, 'extensions/components/124', 'index.php?option=com_extensions&view=component&id=124', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7145, 'mod_search', 4, 125, 'extensions/components/125', 'index.php?option=com_extensions&view=component&id=125', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7146, 'mod_syndicate', 4, 126, 'extensions/components/126', 'index.php?option=com_extensions&view=component&id=126', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7147, 'mod_toolbar', 4, 127, 'extensions/components/127', 'index.php?option=com_extensions&view=component&id=127', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7148, 'mod_breadcrumbs', 4, 128, 'extensions/components/128', 'index.php?option=com_extensions&view=component&id=128', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7149, 'mod_content', 4, 129, 'extensions/components/129', 'index.php?option=com_extensions&view=component&id=129', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7150, 'mod_custom', 4, 130, 'extensions/components/130', 'index.php?option=com_extensions&view=component&id=130', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7151, 'mod_feed', 4, 131, 'extensions/components/131', 'index.php?option=com_extensions&view=component&id=131', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7152, 'mod_header', 4, 132, 'extensions/components/132', 'index.php?option=com_extensions&view=component&id=132', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7153, 'mod_launchpad', 4, 133, 'extensions/components/133', 'index.php?option=com_extensions&view=component&id=133', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7154, 'mod_layout', 4, 134, 'extensions/components/134', 'index.php?option=com_extensions&view=component&id=134', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7155, 'mod_login', 4, 135, 'extensions/components/135', 'index.php?option=com_extensions&view=component&id=135', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7156, 'mod_logout', 4, 136, 'extensions/components/136', 'index.php?option=com_extensions&view=component&id=136', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7157, 'mod_members', 4, 137, 'extensions/components/137', 'index.php?option=com_extensions&view=component&id=137', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7158, 'mod_menu', 4, 138, 'extensions/components/138', 'index.php?option=com_extensions&view=component&id=138', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7159, 'mod_pagination', 4, 139, 'extensions/components/139', 'index.php?option=com_extensions&view=component&id=139', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7160, 'mod_search', 4, 140, 'extensions/components/140', 'index.php?option=com_extensions&view=component&id=140', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7161, 'mod_syndicate', 4, 141, 'extensions/components/141', 'index.php?option=com_extensions&view=component&id=141', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7162, 'mod_toolbar', 4, 142, 'extensions/components/142', 'index.php?option=com_extensions&view=component&id=142', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7164, 'example', 4, 144, 'extensions/plugins/144', 'index.php?option=com_extensions&view=plugin&id=144', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7165, 'molajo', 4, 145, 'extensions/plugins/145', 'index.php?option=com_extensions&view=plugin&id=145', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7166, 'broadcast', 4, 146, 'extensions/plugins/146', 'index.php?option=com_extensions&view=plugin&id=146', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7167, 'content', 4, 147, 'extensions/plugins/147', 'index.php?option=com_extensions&view=plugin&id=147', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7168, 'emailcloak', 4, 148, 'extensions/plugins/148', 'index.php?option=com_extensions&view=plugin&id=148', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7169, 'links', 4, 149, 'extensions/plugins/149', 'index.php?option=com_extensions&view=plugin&id=149', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7170, 'loadmodule', 4, 150, 'extensions/plugins/150', 'index.php?option=com_extensions&view=plugin&id=150', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7171, 'media', 4, 151, 'extensions/plugins/151', 'index.php?option=com_extensions&view=plugin&id=151', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7172, 'protect', 4, 152, 'extensions/plugins/152', 'index.php?option=com_extensions&view=plugin&id=152', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7173, 'responses', 4, 153, 'extensions/plugins/153', 'index.php?option=com_extensions&view=plugin&id=153', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7174, 'article', 4, 154, 'extensions/plugins/154', 'index.php?option=com_extensions&view=plugin&id=154', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7175, 'editor', 4, 155, 'extensions/plugins/155', 'index.php?option=com_extensions&view=plugin&id=155', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7176, 'image', 4, 156, 'extensions/plugins/156', 'index.php?option=com_extensions&view=plugin&id=156', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7177, 'pagebreak', 4, 157, 'extensions/plugins/157', 'index.php?option=com_extensions&view=plugin&id=157', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7178, 'readmore', 4, 158, 'extensions/plugins/158', 'index.php?option=com_extensions&view=plugin&id=158', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7179, 'aloha', 4, 159, 'extensions/plugins/159', 'index.php?option=com_extensions&view=plugin&id=159', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7180, 'none', 4, 160, 'extensions/plugins/160', 'index.php?option=com_extensions&view=plugin&id=160', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7181, 'molajo', 4, 161, 'extensions/plugins/161', 'index.php?option=com_extensions&view=plugin&id=161', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7182, 'extend', 4, 162, 'extensions/plugins/162', 'index.php?option=com_extensions&view=plugin&id=162', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7183, 'minifier', 4, 163, 'extensions/plugins/163', 'index.php?option=com_extensions&view=plugin&id=163', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7184, 'search', 4, 164, 'extensions/plugins/164', 'index.php?option=com_extensions&view=plugin&id=164', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7185, 'tags', 4, 165, 'extensions/plugins/165', 'index.php?option=com_extensions&view=plugin&id=165', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7186, 'urls', 4, 166, 'extensions/plugins/166', 'index.php?option=com_extensions&view=plugin&id=166', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7187, 'molajosample', 4, 167, 'extensions/plugins/167', 'index.php?option=com_extensions&view=plugin&id=167', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7188, 'categories', 4, 168, 'extensions/plugins/168', 'index.php?option=com_extensions&view=plugin&id=168', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7189, 'articles', 4, 169, 'extensions/plugins/169', 'index.php?option=com_extensions&view=plugin&id=169', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7190, 'cache', 4, 170, 'extensions/plugins/170', 'index.php?option=com_extensions&view=plugin&id=170', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7191, 'compress', 4, 171, 'extensions/plugins/171', 'index.php?option=com_extensions&view=plugin&id=171', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7192, 'create', 4, 172, 'extensions/plugins/172', 'index.php?option=com_extensions&view=plugin&id=172', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7193, 'debug', 4, 173, 'extensions/plugins/173', 'index.php?option=com_extensions&view=plugin&id=173', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7194, 'languagefilter', 4, 174, 'extensions/plugins/174', 'index.php?option=com_extensions&view=plugin&id=174', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7195, 'log', 4, 175, 'extensions/plugins/175', 'index.php?option=com_extensions&view=plugin&id=175', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7196, 'logout', 4, 176, 'extensions/plugins/176', 'index.php?option=com_extensions&view=plugin&id=176', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7197, 'molajo', 4, 177, 'extensions/plugins/177', 'index.php?option=com_extensions&view=plugin&id=177', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7198, 'p3p', 4, 178, 'extensions/plugins/178', 'index.php?option=com_extensions&view=plugin&id=178', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7199, 'parameters', 4, 179, 'extensions/plugins/179', 'index.php?option=com_extensions&view=plugin&id=179', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7200, 'redirect', 4, 180, 'extensions/plugins/180', 'index.php?option=com_extensions&view=plugin&id=180', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7201, 'remember', 4, 181, 'extensions/plugins/181', 'index.php?option=com_extensions&view=plugin&id=181', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7202, 'system', 4, 182, 'extensions/plugins/182', 'index.php?option=com_extensions&view=plugin&id=182', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7203, 'webservices', 4, 183, 'extensions/plugins/183', 'index.php?option=com_extensions&view=plugin&id=183', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7204, 'molajo', 4, 184, 'extensions/plugins/184', 'index.php?option=com_extensions&view=plugin&id=184', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7205, 'profile', 4, 185, 'extensions/plugins/185', 'index.php?option=com_extensions&view=plugin&id=185', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7227, 'construct', 4, 207, 'extensions/plugins/207', 'index.php?option=com_extensions&view=plugin&id=207', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7228, 'install', 4, 208, 'extensions/plugins/208', 'index.php?option=com_extensions&view=plugin&id=208', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7229, 'molajito', 4, 209, 'extensions/plugins/209', 'index.php?option=com_extensions&view=plugin&id=209', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7230, 'system', 4, 210, 'extensions/plugins/210', 'index.php?option=com_extensions&view=plugin&id=210', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7234, 'Home', 4, 214, 'extensions/menuitem/214', 'index.php?option=com_extensions&view=menuitem&id=214', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7235, 'Home', 4, 215, 'extensions/menuitem/215', 'index.php?option=com_extensions&view=menuitem&id=215', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7236, 'Configure', 4, 216, 'extensions/menuitem/216', 'index.php?option=com_extensions&view=menuitem&id=216', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7237, 'Access', 4, 217, 'extensions/menuitem/217', 'index.php?option=com_extensions&view=menuitem&id=217', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7238, 'Create', 4, 218, 'extensions/menuitem/218', 'index.php?option=com_extensions&view=menuitem&id=218', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7239, 'Build', 4, 219, 'extensions/menuitem/219', 'index.php?option=com_extensions&view=menuitem&id=219', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7240, 'Search', 4, 220, 'extensions/menuitem/220', 'index.php?option=com_extensions&view=menuitem&id=220', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7241, 'Profile', 4, 221, 'extensions/menuitem/221', 'index.php?option=com_extensions&view=menuitem&id=221', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7242, 'System', 4, 222, 'extensions/menuitem/222', 'index.php?option=com_extensions&view=menuitem&id=222', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7243, 'Checkin', 4, 223, 'extensions/menuitem/223', 'index.php?option=com_extensions&view=menuitem&id=223', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7244, 'Cache', 4, 224, 'extensions/menuitem/224', 'index.php?option=com_extensions&view=menuitem&id=224', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7245, 'Backup', 4, 225, 'extensions/menuitem/225', 'index.php?option=com_extensions&view=menuitem&id=225', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7246, 'Redirects', 4, 226, 'extensions/menuitem/226', 'index.php?option=com_extensions&view=menuitem&id=226', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7247, 'Users', 4, 227, 'extensions/menuitem/227', 'index.php?option=com_extensions&view=menuitem&id=227', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7248, 'Groups', 4, 228, 'extensions/menuitem/228', 'index.php?option=com_extensions&view=menuitem&id=228', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7249, 'Permissions', 4, 229, 'extensions/menuitem/229', 'index.php?option=com_extensions&view=menuitem&id=229', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7250, 'Messages', 4, 230, 'extensions/menuitem/230', 'index.php?option=com_extensions&view=menuitem&id=230', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7251, 'Activity', 4, 231, 'extensions/menuitem/231', 'index.php?option=com_extensions&view=menuitem&id=231', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7252, 'Articles', 4, 232, 'extensions/menuitem/232', 'index.php?option=com_extensions&view=menuitem&id=232', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7253, 'Tags', 4, 233, 'extensions/menuitem/233', 'index.php?option=com_extensions&view=menuitem&id=233', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7254, 'Comments', 4, 234, 'extensions/menuitem/234', 'index.php?option=com_extensions&view=menuitem&id=234', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7255, 'Media', 4, 235, 'extensions/menuitem/235', 'index.php?option=com_extensions&view=menuitem&id=235', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7256, 'Categories', 4, 236, 'extensions/menuitem/236', 'index.php?option=com_extensions&view=menuitem&id=236', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7257, 'Extensions', 4, 237, 'extensions/menuitem/237', 'index.php?option=com_extensions&view=menuitem&id=237', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7258, 'Languages', 4, 238, 'extensions/menuitem/238', 'index.php?option=com_extensions&view=menuitem&id=238', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7259, 'Layouts', 4, 239, 'extensions/menuitem/239', 'index.php?option=com_extensions&view=menuitem&id=239', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7260, 'Modules', 4, 240, 'extensions/menuitem/240', 'index.php?option=com_extensions&view=menuitem&id=240', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7261, 'Plugins', 4, 241, 'extensions/menuitem/241', 'index.php?option=com_extensions&view=menuitem&id=241', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7262, 'Templates', 4, 242, 'extensions/menuitem/242', 'index.php?option=com_extensions&view=menuitem&id=242', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7263, 'Main Menu', 4, 243, 'extensions/menuitem/243', 'index.php?option=com_extensions&view=menuitem&id=243', 5, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7267, 'ROOT', 2, 1, 'category/1', 'index.php?option=com_categories&id=1', 1, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7268, 'articles', 2, 2, 'category/2', 'index.php?option=com_categories&id=2', 1, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7270, 'My First Article', 3, 1, 'articles/1', 'index.php?option=com_articles&id=1', 1, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7271, 'My Second Article', 3, 2, 'articles/2', 'index.php?option=com_articles&id=2', 1, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7272, 'My Third Article', 3, 3, 'articles/3', 'index.php?option=com_articles&id=3', 1, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7273, 'My Fourth Article', 3, 4, 'articles/4', 'index.php?option=com_articles&id=4', 1, 'en-GB', NULL);
INSERT INTO `molajo_assets` VALUES(7274, 'My Fifth Article', 3, 5, 'articles/5', 'index.php?option=com_articles&id=5', 1, 'en-GB', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `molajo_categories`
--

CREATE TABLE `molajo_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `subtitle` varchar(255) NOT NULL DEFAULT ' ',
  `alias` varchar(255) NOT NULL DEFAULT ' ',
  `content_text` mediumtext,
  `status` int(3) unsigned NOT NULL DEFAULT '0',
  `start_publishing_datetime` datetime DEFAULT NULL,
  `stop_publishing_datetime` datetime DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '0',
  `version_of_id` int(11) NOT NULL DEFAULT '0',
  `status_prior_to_version` int(11) DEFAULT NULL,
  `checked_out_by` int(11) unsigned NOT NULL DEFAULT '0',
  `checked_out_datetime` datetime NOT NULL,
  `created_by` int(11) unsigned NOT NULL DEFAULT '0',
  `created_datetime` datetime NOT NULL,
  `modified_by` int(11) unsigned NOT NULL DEFAULT '0',
  `modified_datetime` datetime NOT NULL,
  `asset_id` int(11) unsigned NOT NULL COMMENT 'FK to the molajo_assets table.',
  `extension_instance_id` int(11) NOT NULL,
  `parent_id` varchar(45) DEFAULT NULL,
  `lft` int(11) DEFAULT NULL,
  `rgt` int(11) DEFAULT NULL,
  `level` int(11) DEFAULT NULL,
  `metakey` text COMMENT 'The meta keywords for the page.',
  `metadesc` text COMMENT 'The meta description for the page.',
  `metadata` text COMMENT 'JSON encoded metadata properties.',
  `custom_fields` mediumtext,
  `parameters` mediumtext COMMENT 'Configurable Parameter Values',
  `language` char(7) NOT NULL DEFAULT '',
  `translation_of_id` int(11) DEFAULT NULL,
  `ordering` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_asset_table_id_join` (`asset_id`),
  KEY `cat_idx` (`status`),
  KEY `idx_checkout` (`checked_out_by`),
  KEY `idx_alias` (`alias`),
  KEY `idx_language` (`language`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `molajo_categories`
--

INSERT INTO `molajo_categories` VALUES(1, 'ROOT', '', 'root', '<p>Root category</p>', 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, NULL, 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 7267, 3, '0', 0, 0, 0, NULL, NULL, NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_categories` VALUES(2, 'articles', 'com_articles', 'Articles', '<p>Category for Articles</p>', 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, 0, NULL, 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 7268, 3, '1', 1, 2, 1, NULL, NULL, NULL, NULL, NULL, 'en-GB', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `molajo_configuration`
--

CREATE TABLE `molajo_configuration` (
  `component_option` varchar(50) NOT NULL DEFAULT ' ',
  `option_id` int(11) unsigned NOT NULL DEFAULT '0',
  `option_value` varchar(80) NOT NULL DEFAULT ' ',
  `option_value_literal` varchar(255) NOT NULL DEFAULT ' ',
  `ordering` int(11) NOT NULL DEFAULT '0',
  UNIQUE KEY `idx_component_option_id_value_key` (`component_option`,`option_id`,`option_value`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `molajo_configuration`
--

INSERT INTO `molajo_configuration` VALUES('com_articles', 100, '', '', 0);
INSERT INTO `molajo_configuration` VALUES('com_articles', 100, '__articles', '__articles', 1);
INSERT INTO `molajo_configuration` VALUES('com_login', 100, '', '', 0);
INSERT INTO `molajo_configuration` VALUES('com_login', 100, '__dummy', '__dummy', 1);
INSERT INTO `molajo_configuration` VALUES('com_login', 1100, '', '', 0);
INSERT INTO `molajo_configuration` VALUES('com_login', 1100, 'display', 'display', 3);
INSERT INTO `molajo_configuration` VALUES('com_login', 1100, 'login', 'login', 28);
INSERT INTO `molajo_configuration` VALUES('com_login', 1100, 'logout', 'login', 29);
INSERT INTO `molajo_configuration` VALUES('com_login', 1101, '', '', 0);
INSERT INTO `molajo_configuration` VALUES('com_login', 1101, 'display', 'display', 3);
INSERT INTO `molajo_configuration` VALUES('com_login', 1101, 'login', 'login', 28);
INSERT INTO `molajo_configuration` VALUES('com_login', 1101, 'logout', 'login', 29);
INSERT INTO `molajo_configuration` VALUES('com_login', 2000, '', '', 0);
INSERT INTO `molajo_configuration` VALUES('com_login', 2000, 'display', 'display', 1);
INSERT INTO `molajo_configuration` VALUES('com_login', 2001, '', '', 0);
INSERT INTO `molajo_configuration` VALUES('com_login', 2001, 'display', 'display', 1);
INSERT INTO `molajo_configuration` VALUES('com_login', 2100, '', '', 0);
INSERT INTO `molajo_configuration` VALUES('com_login', 2100, 'display', 'display', 1);
INSERT INTO `molajo_configuration` VALUES('com_login', 2101, '', '', 0);
INSERT INTO `molajo_configuration` VALUES('com_login', 2101, 'display', 'display', 1);
INSERT INTO `molajo_configuration` VALUES('com_login', 3000, '', '', 0);
INSERT INTO `molajo_configuration` VALUES('com_login', 3000, 'login', 'login', 1);
INSERT INTO `molajo_configuration` VALUES('com_login', 3001, '', '', 0);
INSERT INTO `molajo_configuration` VALUES('com_login', 3001, 'admin_login', 'admin_login', 1);
INSERT INTO `molajo_configuration` VALUES('com_login', 3100, '', '', 0);
INSERT INTO `molajo_configuration` VALUES('com_login', 3100, 'login', 'login', 1);
INSERT INTO `molajo_configuration` VALUES('com_login', 3101, '', '', 0);
INSERT INTO `molajo_configuration` VALUES('com_login', 3101, 'admin_login', 'admin_login', 1);
INSERT INTO `molajo_configuration` VALUES('com_login', 4000, '', '', 0);
INSERT INTO `molajo_configuration` VALUES('com_login', 4000, 'html', 'html', 1);
INSERT INTO `molajo_configuration` VALUES('com_login', 4001, 'html', 'html', 1);
INSERT INTO `molajo_configuration` VALUES('com_login', 5000, '', '', 0);
INSERT INTO `molajo_configuration` VALUES('com_login', 5000, 'dummy', 'dummy', 1);
INSERT INTO `molajo_configuration` VALUES('com_login', 5001, '', '', 0);
INSERT INTO `molajo_configuration` VALUES('com_login', 5001, 'dummy', 'dummy', 1);
INSERT INTO `molajo_configuration` VALUES('com_login', 6000, '', '', 0);
INSERT INTO `molajo_configuration` VALUES('com_login', 6000, 'user', 'user', 1);
INSERT INTO `molajo_configuration` VALUES('com_login', 10000, '', '', 0);
INSERT INTO `molajo_configuration` VALUES('com_login', 10000, 'core', 'Core ACL Implementation', 1);
INSERT INTO `molajo_configuration` VALUES('com_login', 10100, '', '', 0);
INSERT INTO `molajo_configuration` VALUES('com_login', 10100, 'view', 'view', 1);
INSERT INTO `molajo_configuration` VALUES('com_login', 10200, '', '', 0);
INSERT INTO `molajo_configuration` VALUES('com_login', 10200, 'login', 'login', 15);
INSERT INTO `molajo_configuration` VALUES('com_login', 10200, 'logout', 'logout', 16);
INSERT INTO `molajo_configuration` VALUES('core', 100, '', '', 0);
INSERT INTO `molajo_configuration` VALUES('core', 100, '__common', '__common', 1);
INSERT INTO `molajo_configuration` VALUES('core', 200, '', '', 0);
INSERT INTO `molajo_configuration` VALUES('core', 200, 'access', 'MOLAJO_FIELD_ACCESS_LABEL', 1);
INSERT INTO `molajo_configuration` VALUES('core', 200, 'alias', 'MOLAJO_FIELD_ALIAS_LABEL', 2);
INSERT INTO `molajo_configuration` VALUES('core', 200, 'asset_id', 'MOLAJO_FIELD_ASSET_ID_LABEL', 3);
INSERT INTO `molajo_configuration` VALUES('core', 200, 'attribs', 'MOLAJO_FIELD_ATTRIBS_LABEL', 4);
INSERT INTO `molajo_configuration` VALUES('core', 200, 'category_default', 'MOLAJO_FIELD_CATEGORY_DEFAULT_LABEL', 44);
INSERT INTO `molajo_configuration` VALUES('core', 200, 'catid', 'MOLAJO_FIELD_CATID_LABEL', 5);
INSERT INTO `molajo_configuration` VALUES('core', 200, 'checked_out', 'MOLAJO_FIELD_CHECKED_OUT_LABEL', 6);
INSERT INTO `molajo_configuration` VALUES('core', 200, 'checked_out_time', 'MOLAJO_FIELD_CHECKED_OUT_TIME_LABEL', 7);
INSERT INTO `molajo_configuration` VALUES('core', 200, 'component_id', 'MOLAJO_FIELD_COMPONENT_ID_LABEL', 8);
INSERT INTO `molajo_configuration` VALUES('core', 200, 'content_email_address', 'MOLAJO_FIELD_CONTENT_EMAIL_ADDRESS_LABEL', 10);
INSERT INTO `molajo_configuration` VALUES('core', 200, 'content_file', 'MOLAJO_FIELD_CONTENT_FILE_LABEL', 11);
INSERT INTO `molajo_configuration` VALUES('core', 200, 'content_link', 'MOLAJO_FIELD_CONTENT_LINK_LABEL', 12);
INSERT INTO `molajo_configuration` VALUES('core', 200, 'content_numeric_value', 'MOLAJO_FIELD_CONTENT_NUMERIC_VALUE_LABEL', 13);
INSERT INTO `molajo_configuration` VALUES('core', 200, 'content_table', 'MOLAJO_FIELD_CONTENT_TABLE_LABEL', 9);
INSERT INTO `molajo_configuration` VALUES('core', 200, 'content_text', 'MOLAJO_FIELD_CONTENT_TEXT_LABEL', 14);
INSERT INTO `molajo_configuration` VALUES('core', 200, 'content_type', 'MOLAJO_FIELD_CONTENT_TYPE_LABEL', 15);
INSERT INTO `molajo_configuration` VALUES('core', 200, 'created', 'MOLAJO_FIELD_CREATED_LABEL', 16);
INSERT INTO `molajo_configuration` VALUES('core', 200, 'created_by', 'MOLAJO_FIELD_CREATED_BY_LABEL', 17);
INSERT INTO `molajo_configuration` VALUES('core', 200, 'created_by_alias', 'MOLAJO_FIELD_CREATED_BY_ALIAS_LABEL', 18);
INSERT INTO `molajo_configuration` VALUES('core', 200, 'created_by_email', 'MOLAJO_FIELD_CREATED_BY_EMAIL_LABEL', 19);
INSERT INTO `molajo_configuration` VALUES('core', 200, 'created_by_ip_address', 'MOLAJO_FIELD_CREATED_BY_IP_ADDRESS_LABEL', 20);
INSERT INTO `molajo_configuration` VALUES('core', 200, 'created_by_referer', 'MOLAJO_FIELD_CREATED_BY_REFERER_LABEL', 21);
INSERT INTO `molajo_configuration` VALUES('core', 200, 'created_by_website', 'MOLAJO_FIELD_CREATED_BY_WEBSITE_LABEL', 22);
INSERT INTO `molajo_configuration` VALUES('core', 200, 'featured', 'MOLAJO_FIELD_FEATURED_LABEL', 23);
INSERT INTO `molajo_configuration` VALUES('core', 200, 'id', 'MOLAJO_FIELD_ID_LABEL', 24);
INSERT INTO `molajo_configuration` VALUES('core', 200, 'language', 'MOLAJO_FIELD_LANGUAGE_LABEL', 25);
INSERT INTO `molajo_configuration` VALUES('core', 200, 'level', 'MOLAJO_FIELD_LEVEL_LABEL', 26);
INSERT INTO `molajo_configuration` VALUES('core', 200, 'lft', 'MOLAJO_FIELD_LFT_LABEL', 27);
INSERT INTO `molajo_configuration` VALUES('core', 200, 'metadata', 'MOLAJO_FIELD_METADATA_LABEL', 28);
INSERT INTO `molajo_configuration` VALUES('core', 200, 'metadesc', 'MOLAJO_FIELD_METADESC_LABEL', 29);
INSERT INTO `molajo_configuration` VALUES('core', 200, 'metakey', 'MOLAJO_FIELD_METAKEY_LABEL', 30);
INSERT INTO `molajo_configuration` VALUES('core', 200, 'meta_author', 'MOLAJO_FIELD_META_AUTHOR_LABEL', 31);
INSERT INTO `molajo_configuration` VALUES('core', 200, 'meta_rights', 'MOLAJO_FIELD_META_RIGHTS_LABEL', 32);
INSERT INTO `molajo_configuration` VALUES('core', 200, 'meta_robots', 'MOLAJO_FIELD_META_ROBOTS_LABEL', 33);
INSERT INTO `molajo_configuration` VALUES('core', 200, 'modified', 'MOLAJO_FIELD_MODIFIED_LABEL', 34);
INSERT INTO `molajo_configuration` VALUES('core', 200, 'modified_by', 'MOLAJO_FIELD_MODIFIED_BY_LABEL', 35);
INSERT INTO `molajo_configuration` VALUES('core', 200, 'ordering', 'MOLAJO_FIELD_ORDERING_LABEL', 36);
INSERT INTO `molajo_configuration` VALUES('core', 200, 'publish_down', 'MOLAJO_FIELD_PUBLISH_DOWN_LABEL', 37);
INSERT INTO `molajo_configuration` VALUES('core', 200, 'publish_up', 'MOLAJO_FIELD_PUBLISH_UP_LABEL', 38);
INSERT INTO `molajo_configuration` VALUES('core', 200, 'rgt', 'MOLAJO_FIELD_RGT_LABEL', 39);
INSERT INTO `molajo_configuration` VALUES('core', 200, 'state', 'MOLAJO_FIELD_STATE_LABEL', 40);
INSERT INTO `molajo_configuration` VALUES('core', 200, 'state_prior_to_version', 'MOLAJO_FIELD_STATE_PRIOR_TO_VERSION_LABEL', 41);
INSERT INTO `molajo_configuration` VALUES('core', 200, 'stickied', 'MOLAJO_FIELD_STICKIED_LABEL', 42);
INSERT INTO `molajo_configuration` VALUES('core', 200, 'subtitle', 'MOLAJO_FIELD_SUBTITLE_LABEL', 46);
INSERT INTO `molajo_configuration` VALUES('core', 200, 'title', 'MOLAJO_FIELD_TITLE_LABEL', 45);
INSERT INTO `molajo_configuration` VALUES('core', 200, 'user_default', 'MOLAJO_FIELD_USER_DEFAULT_LABEL', 43);
INSERT INTO `molajo_configuration` VALUES('core', 200, 'version', 'MOLAJO_FIELD_VERSION_LABEL', 47);
INSERT INTO `molajo_configuration` VALUES('core', 200, 'version_of_id', 'MOLAJO_FIELD_VERSION_OF_ID_LABEL', 48);
INSERT INTO `molajo_configuration` VALUES('core', 210, '', '', 0);
INSERT INTO `molajo_configuration` VALUES('core', 210, 'access', 'MOLAJO_FIELD_ACCESS_LABEL', 1);
INSERT INTO `molajo_configuration` VALUES('core', 210, 'featured', 'MOLAJO_FIELD_FEATURED_LABEL', 2);
INSERT INTO `molajo_configuration` VALUES('core', 210, 'ordering', 'MOLAJO_FIELD_ORDERING_LABEL', 3);
INSERT INTO `molajo_configuration` VALUES('core', 210, 'publish_down', 'MOLAJO_FIELD_PUBLISH_DOWN_LABEL', 4);
INSERT INTO `molajo_configuration` VALUES('core', 210, 'publish_up', 'MOLAJO_FIELD_PUBLISH_UP_LABEL', 5);
INSERT INTO `molajo_configuration` VALUES('core', 210, 'state', 'MOLAJO_FIELD_STATE_LABEL', 6);
INSERT INTO `molajo_configuration` VALUES('core', 210, 'stickied', 'MOLAJO_FIELD_STICKIED_LABEL', 7);
INSERT INTO `molajo_configuration` VALUES('core', 220, '', '', 0);
INSERT INTO `molajo_configuration` VALUES('core', 220, 'attribs', 'MOLAJO_FIELD_ATTRIBS_LABEL', 1);
INSERT INTO `molajo_configuration` VALUES('core', 220, 'metadata', 'MOLAJO_FIELD_METADATA_LABEL', 2);
INSERT INTO `molajo_configuration` VALUES('core', 220, 'params', 'MOLAJO_FIELD_PARAMETERS_LABEL', 3);
INSERT INTO `molajo_configuration` VALUES('core', 230, '', '', 0);
INSERT INTO `molajo_configuration` VALUES('core', 230, 'content_type', 'Content Type', 1);
INSERT INTO `molajo_configuration` VALUES('core', 250, '', '', 0);
INSERT INTO `molajo_configuration` VALUES('core', 250, '-1', 'MOLAJO_OPTION_TRASHED', 4);
INSERT INTO `molajo_configuration` VALUES('core', 250, '-10', 'MOLAJO_OPTION_VERSION', 6);
INSERT INTO `molajo_configuration` VALUES('core', 250, '-2', 'MOLAJO_OPTION_SPAMMED', 5);
INSERT INTO `molajo_configuration` VALUES('core', 250, '0', 'MOLAJO_OPTION_UNPUBLISHED', 3);
INSERT INTO `molajo_configuration` VALUES('core', 250, '1', 'MOLAJO_OPTION_PUBLISHED', 2);
INSERT INTO `molajo_configuration` VALUES('core', 250, '2', 'MOLAJO_OPTION_ARCHIVED', 1);
INSERT INTO `molajo_configuration` VALUES('core', 300, '', '', 0);
INSERT INTO `molajo_configuration` VALUES('core', 300, 'archive', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_ARCHIVE', 1);
INSERT INTO `molajo_configuration` VALUES('core', 300, 'checkin', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_CHECKIN', 2);
INSERT INTO `molajo_configuration` VALUES('core', 300, 'delete', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_DELETE', 3);
INSERT INTO `molajo_configuration` VALUES('core', 300, 'edit', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_EDIT', 4);
INSERT INTO `molajo_configuration` VALUES('core', 300, 'feature', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_FEATURE', 5);
INSERT INTO `molajo_configuration` VALUES('core', 300, 'help', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_HELP', 6);
INSERT INTO `molajo_configuration` VALUES('core', 300, 'new', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_NEW', 7);
INSERT INTO `molajo_configuration` VALUES('core', 300, 'options', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_OPTIONS', 8);
INSERT INTO `molajo_configuration` VALUES('core', 300, 'publish', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_PUBLISH', 9);
INSERT INTO `molajo_configuration` VALUES('core', 300, 'restore', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_RESTORE', 10);
INSERT INTO `molajo_configuration` VALUES('core', 300, 'separator', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_SEPARATOR', 11);
INSERT INTO `molajo_configuration` VALUES('core', 300, 'spam', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_SPAM', 12);
INSERT INTO `molajo_configuration` VALUES('core', 300, 'sticky', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_STICKY', 13);
INSERT INTO `molajo_configuration` VALUES('core', 300, 'trash', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_TRASH', 14);
INSERT INTO `molajo_configuration` VALUES('core', 300, 'unpublish', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_UNPUBLISH', 15);
INSERT INTO `molajo_configuration` VALUES('core', 310, '', '', 0);
INSERT INTO `molajo_configuration` VALUES('core', 310, 'apply', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_APPLY', 1);
INSERT INTO `molajo_configuration` VALUES('core', 310, 'close', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_CLOSE', 2);
INSERT INTO `molajo_configuration` VALUES('core', 310, 'help', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_HELP', 3);
INSERT INTO `molajo_configuration` VALUES('core', 310, 'restore', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_RESTORE', 4);
INSERT INTO `molajo_configuration` VALUES('core', 310, 'save', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_SAVE', 5);
INSERT INTO `molajo_configuration` VALUES('core', 310, 'save2copy', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_SAVE_AS_COPY', 7);
INSERT INTO `molajo_configuration` VALUES('core', 310, 'save2new', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_SAVE_AND_NEW', 6);
INSERT INTO `molajo_configuration` VALUES('core', 310, 'separator', 'MOLAJO_CONFIG_MANAGER_OPTION_BUTTON_SEPARATOR', 8);
INSERT INTO `molajo_configuration` VALUES('core', 320, '', '', 0);
INSERT INTO `molajo_configuration` VALUES('core', 320, 'category', 'MOLAJO_CONFIG_MANAGER_SUB_MENU_CATEGORY', 1);
INSERT INTO `molajo_configuration` VALUES('core', 320, 'default', 'MOLAJO_CONFIG_MANAGER_SUB_MENU_DEFAULT', 2);
INSERT INTO `molajo_configuration` VALUES('core', 320, 'featured', 'MOLAJO_CONFIG_MANAGER_SUB_MENU_FEATURED', 3);
INSERT INTO `molajo_configuration` VALUES('core', 320, 'revisions', 'MOLAJO_CONFIG_MANAGER_SUB_MENU_REVISIONS', 4);
INSERT INTO `molajo_configuration` VALUES('core', 320, 'stickied', 'MOLAJO_CONFIG_MANAGER_SUB_MENU_STICKIED', 5);
INSERT INTO `molajo_configuration` VALUES('core', 320, 'unpublished', 'MOLAJO_CONFIG_MANAGER_SUB_MENU_UNPUBLISHED', 6);
INSERT INTO `molajo_configuration` VALUES('core', 330, '', '', 0);
INSERT INTO `molajo_configuration` VALUES('core', 330, 'access', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_ACCESS', 1);
INSERT INTO `molajo_configuration` VALUES('core', 330, 'alias', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_ALIAS', 2);
INSERT INTO `molajo_configuration` VALUES('core', 330, 'catid', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_CATEGORY', 4);
INSERT INTO `molajo_configuration` VALUES('core', 330, 'content_type', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_CONTENT_TYPE', 5);
INSERT INTO `molajo_configuration` VALUES('core', 330, 'created', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_CREATE_DATE', 6);
INSERT INTO `molajo_configuration` VALUES('core', 330, 'created_by', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_AUTHOR', 3);
INSERT INTO `molajo_configuration` VALUES('core', 330, 'featured', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_FEATURED', 7);
INSERT INTO `molajo_configuration` VALUES('core', 330, 'language', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_LANGUAGE', 9);
INSERT INTO `molajo_configuration` VALUES('core', 330, 'modified', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_UPDATE_DATE', 10);
INSERT INTO `molajo_configuration` VALUES('core', 330, 'publish_up', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_PUBLISH_DATE', 11);
INSERT INTO `molajo_configuration` VALUES('core', 330, 'state', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_STATE', 12);
INSERT INTO `molajo_configuration` VALUES('core', 330, 'stickied', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_STICKIED', 13);
INSERT INTO `molajo_configuration` VALUES('core', 330, 'subtitle', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_SUBTITLE', 15);
INSERT INTO `molajo_configuration` VALUES('core', 330, 'title', 'MOLAJO_CONFIG_MANAGER_OPTION_FILTER_TITLE', 14);
INSERT INTO `molajo_configuration` VALUES('core', 340, '', '', 0);
INSERT INTO `molajo_configuration` VALUES('core', 340, 'article', 'MOLAJO_CONFIG_MANAGER_EDITOR_BUTTON_ARTICLE', 1);
INSERT INTO `molajo_configuration` VALUES('core', 340, 'audio', 'MOLAJO_CONFIG_MANAGER_EDITOR_BUTTON_AUDIO', 2);
INSERT INTO `molajo_configuration` VALUES('core', 340, 'file', 'MOLAJO_CONFIG_MANAGER_EDITOR_BUTTON_FILE', 3);
INSERT INTO `molajo_configuration` VALUES('core', 340, 'gallery', 'MOLAJO_CONFIG_MANAGER_EDITOR_BUTTON_GALLERY', 4);
INSERT INTO `molajo_configuration` VALUES('core', 340, 'image', 'MOLAJO_CONFIG_MANAGER_EDITOR_BUTTON_IMAGE', 5);
INSERT INTO `molajo_configuration` VALUES('core', 340, 'pagebreak', 'MOLAJO_CONFIG_MANAGER_EDITOR_BUTTON_PAGEBREAK', 6);
INSERT INTO `molajo_configuration` VALUES('core', 340, 'readmore', 'MOLAJO_CONFIG_MANAGER_EDITOR_BUTTON_READMORE', 7);
INSERT INTO `molajo_configuration` VALUES('core', 340, 'video', 'MOLAJO_CONFIG_MANAGER_EDITOR_BUTTON_VIDEO', 8);
INSERT INTO `molajo_configuration` VALUES('core', 400, '', '', 0);
INSERT INTO `molajo_configuration` VALUES('core', 400, 'rtp-enc-aescm128', 'rtp-enc-aescm128', 1);
INSERT INTO `molajo_configuration` VALUES('core', 400, 'sp-midi', 'sp-midi', 2);
INSERT INTO `molajo_configuration` VALUES('core', 400, 'vnd.3gpp.iufp', 'vnd.3gpp.iufp', 3);
INSERT INTO `molajo_configuration` VALUES('core', 400, 'vnd.4SB', 'vnd.4SB', 4);
INSERT INTO `molajo_configuration` VALUES('core', 400, 'vnd.audiokoz', 'vnd.audiokoz', 6);
INSERT INTO `molajo_configuration` VALUES('core', 400, 'vnd.CELP', 'vnd.CELP', 5);
INSERT INTO `molajo_configuration` VALUES('core', 400, 'vnd.cisco.nse', 'vnd.cisco.nse', 7);
INSERT INTO `molajo_configuration` VALUES('core', 400, 'vnd.cmles.radio-events', 'vnd.cmles.radio-events', 8);
INSERT INTO `molajo_configuration` VALUES('core', 400, 'vnd.cns.anp1', 'vnd.cns.anp1', 9);
INSERT INTO `molajo_configuration` VALUES('core', 400, 'vnd.cns.inf1', 'vnd.cns.inf1', 10);
INSERT INTO `molajo_configuration` VALUES('core', 400, 'vnd.dece.audio', 'vnd.dece.audio', 11);
INSERT INTO `molajo_configuration` VALUES('core', 400, 'vnd.digital-winds', 'vnd.digital-winds', 12);
INSERT INTO `molajo_configuration` VALUES('core', 400, 'vnd.dlna.adts', 'vnd.dlna.adts', 13);
INSERT INTO `molajo_configuration` VALUES('core', 400, 'vnd.dolby.heaac.1', 'vnd.dolby.heaac.1', 14);
INSERT INTO `molajo_configuration` VALUES('core', 400, 'vnd.dolby.heaac.2', 'vnd.dolby.heaac.2', 15);
INSERT INTO `molajo_configuration` VALUES('core', 400, 'vnd.dolby.mlp', 'vnd.dolby.mlp', 16);
INSERT INTO `molajo_configuration` VALUES('core', 400, 'vnd.dolby.mps', 'vnd.dolby.mps', 17);
INSERT INTO `molajo_configuration` VALUES('core', 400, 'vnd.dolby.pl2', 'vnd.dolby.pl2', 18);
INSERT INTO `molajo_configuration` VALUES('core', 400, 'vnd.dolby.pl2x', 'vnd.dolby.pl2x', 19);
INSERT INTO `molajo_configuration` VALUES('core', 400, 'vnd.dolby.pl2z', 'vnd.dolby.pl2z', 20);
INSERT INTO `molajo_configuration` VALUES('core', 400, 'vnd.dolby.pulse.1', 'vnd.dolby.pulse.1', 21);
INSERT INTO `molajo_configuration` VALUES('core', 400, 'vnd.dra', 'vnd.dra', 22);
INSERT INTO `molajo_configuration` VALUES('core', 400, 'vnd.dts', 'vnd.dts', 23);
INSERT INTO `molajo_configuration` VALUES('core', 400, 'vnd.dts.hd', 'vnd.dts.hd', 24);
INSERT INTO `molajo_configuration` VALUES('core', 400, 'vnd.dvb.file', 'vnd.dvb.file', 25);
INSERT INTO `molajo_configuration` VALUES('core', 400, 'vnd.everad.plj', 'vnd.everad.plj', 26);
INSERT INTO `molajo_configuration` VALUES('core', 400, 'vnd.hns.audio', 'vnd.hns.audio', 27);
INSERT INTO `molajo_configuration` VALUES('core', 400, 'vnd.lucent.voice', 'vnd.lucent.voice', 28);
INSERT INTO `molajo_configuration` VALUES('core', 400, 'vnd.ms-playready.media.pya', 'vnd.ms-playready.media.pya', 29);
INSERT INTO `molajo_configuration` VALUES('core', 400, 'vnd.nokia.mobile-xmf', 'vnd.nokia.mobile-xmf', 30);
INSERT INTO `molajo_configuration` VALUES('core', 400, 'vnd.nortel.vbk', 'vnd.nortel.vbk', 31);
INSERT INTO `molajo_configuration` VALUES('core', 400, 'vnd.nuera.ecelp4800', 'vnd.nuera.ecelp4800', 32);
INSERT INTO `molajo_configuration` VALUES('core', 400, 'vnd.nuera.ecelp7470', 'vnd.nuera.ecelp7470', 33);
INSERT INTO `molajo_configuration` VALUES('core', 400, 'vnd.nuera.ecelp9600', 'vnd.nuera.ecelp9600', 34);
INSERT INTO `molajo_configuration` VALUES('core', 400, 'vnd.octel.sbc', 'vnd.octel.sbc', 35);
INSERT INTO `molajo_configuration` VALUES('core', 400, 'vnd.qcelp', 'vnd.qcelp', 36);
INSERT INTO `molajo_configuration` VALUES('core', 400, 'vnd.rhetorex.32kadpcm', 'vnd.rhetorex.32kadpcm', 37);
INSERT INTO `molajo_configuration` VALUES('core', 400, 'vnd.rip', 'vnd.rip', 38);
INSERT INTO `molajo_configuration` VALUES('core', 400, 'vnd.sealedmedia.softseal-mpeg', 'vnd.sealedmedia.softseal-mpeg', 39);
INSERT INTO `molajo_configuration` VALUES('core', 400, 'vnd.vmx.cvsd', 'vnd.vmx.cvsd', 40);
INSERT INTO `molajo_configuration` VALUES('core', 410, '', '', 0);
INSERT INTO `molajo_configuration` VALUES('core', 410, 'cgm', 'cgm', 1);
INSERT INTO `molajo_configuration` VALUES('core', 410, 'jp2', 'jp2', 2);
INSERT INTO `molajo_configuration` VALUES('core', 410, 'jpm', 'jpm', 3);
INSERT INTO `molajo_configuration` VALUES('core', 410, 'jpx', 'jpx', 4);
INSERT INTO `molajo_configuration` VALUES('core', 410, 'naplps', 'naplps', 5);
INSERT INTO `molajo_configuration` VALUES('core', 410, 'png', 'png', 6);
INSERT INTO `molajo_configuration` VALUES('core', 410, 'prs.btif', 'prs.btif', 7);
INSERT INTO `molajo_configuration` VALUES('core', 410, 'prs.pti', 'prs.pti', 8);
INSERT INTO `molajo_configuration` VALUES('core', 410, 'vnd-djvu', 'vnd-djvu', 9);
INSERT INTO `molajo_configuration` VALUES('core', 410, 'vnd-svf', 'vnd-svf', 10);
INSERT INTO `molajo_configuration` VALUES('core', 410, 'vnd-wap-wbmp', 'vnd-wap-wbmp', 11);
INSERT INTO `molajo_configuration` VALUES('core', 410, 'vnd.adobe.photoshop', 'vnd.adobe.photoshop', 12);
INSERT INTO `molajo_configuration` VALUES('core', 410, 'vnd.cns.inf2', 'vnd.cns.inf2', 13);
INSERT INTO `molajo_configuration` VALUES('core', 410, 'vnd.dece.graphic', 'vnd.dece.graphic', 14);
INSERT INTO `molajo_configuration` VALUES('core', 410, 'vnd.dvb.subtitle', 'vnd.dvb.subtitle', 15);
INSERT INTO `molajo_configuration` VALUES('core', 410, 'vnd.dwg', 'vnd.dwg', 16);
INSERT INTO `molajo_configuration` VALUES('core', 410, 'vnd.dxf', 'vnd.dxf', 17);
INSERT INTO `molajo_configuration` VALUES('core', 410, 'vnd.fastbidsheet', 'vnd.fastbidsheet', 18);
INSERT INTO `molajo_configuration` VALUES('core', 410, 'vnd.fpx', 'vnd.fpx', 19);
INSERT INTO `molajo_configuration` VALUES('core', 410, 'vnd.fst', 'vnd.fst', 20);
INSERT INTO `molajo_configuration` VALUES('core', 410, 'vnd.fujixerox.edmics-mmr', 'vnd.fujixerox.edmics-mmr', 21);
INSERT INTO `molajo_configuration` VALUES('core', 410, 'vnd.fujixerox.edmics-rlc', 'vnd.fujixerox.edmics-rlc', 22);
INSERT INTO `molajo_configuration` VALUES('core', 410, 'vnd.globalgraphics.pgb', 'vnd.globalgraphics.pgb', 23);
INSERT INTO `molajo_configuration` VALUES('core', 410, 'vnd.microsoft.icon', 'vnd.microsoft.icon', 24);
INSERT INTO `molajo_configuration` VALUES('core', 410, 'vnd.mix', 'vnd.mix', 25);
INSERT INTO `molajo_configuration` VALUES('core', 410, 'vnd.ms-modi', 'vnd.ms-modi', 26);
INSERT INTO `molajo_configuration` VALUES('core', 410, 'vnd.net-fpx', 'vnd.net-fpx', 27);
INSERT INTO `molajo_configuration` VALUES('core', 410, 'vnd.radiance', 'vnd.radiance', 28);
INSERT INTO `molajo_configuration` VALUES('core', 410, 'vnd.sealed-png', 'vnd.sealed-png', 29);
INSERT INTO `molajo_configuration` VALUES('core', 410, 'vnd.sealedmedia.softseal-gif', 'vnd.sealedmedia.softseal-gif', 30);
INSERT INTO `molajo_configuration` VALUES('core', 410, 'vnd.sealedmedia.softseal-jpg', 'vnd.sealedmedia.softseal-jpg', 31);
INSERT INTO `molajo_configuration` VALUES('core', 410, 'vnd.xiff', 'vnd.xiff', 32);
INSERT INTO `molajo_configuration` VALUES('core', 420, '', '', 0);
INSERT INTO `molajo_configuration` VALUES('core', 420, 'n3', 'n3', 1);
INSERT INTO `molajo_configuration` VALUES('core', 420, 'prs.fallenstein.rst', 'prs.fallenstein.rst', 2);
INSERT INTO `molajo_configuration` VALUES('core', 420, 'prs.lines.tag', 'prs.lines.tag', 3);
INSERT INTO `molajo_configuration` VALUES('core', 420, 'rtf', 'rtf', 4);
INSERT INTO `molajo_configuration` VALUES('core', 420, 'rtp-enc-aescm128', 'rtp-enc-aescm128', 5);
INSERT INTO `molajo_configuration` VALUES('core', 420, 'tab-separated-values', 'tab-separated-values', 6);
INSERT INTO `molajo_configuration` VALUES('core', 420, 'turtle', 'turtle', 7);
INSERT INTO `molajo_configuration` VALUES('core', 420, 'vnd-curl', 'vnd-curl', 8);
INSERT INTO `molajo_configuration` VALUES('core', 420, 'vnd.abc', 'vnd.abc', 12);
INSERT INTO `molajo_configuration` VALUES('core', 420, 'vnd.curl', 'vnd.curl', 13);
INSERT INTO `molajo_configuration` VALUES('core', 420, 'vnd.DMClientScript', 'vnd.DMClientScript', 9);
INSERT INTO `molajo_configuration` VALUES('core', 420, 'vnd.dvb.subtitle', 'vnd.dvb.subtitle', 14);
INSERT INTO `molajo_configuration` VALUES('core', 420, 'vnd.esmertec.theme-descriptor', 'vnd.esmertec.theme-descriptor', 15);
INSERT INTO `molajo_configuration` VALUES('core', 420, 'vnd.fly', 'vnd.fly', 16);
INSERT INTO `molajo_configuration` VALUES('core', 420, 'vnd.fmi.flexstor', 'vnd.fmi.flexstor', 17);
INSERT INTO `molajo_configuration` VALUES('core', 420, 'vnd.graphviz', 'vnd.graphviz', 18);
INSERT INTO `molajo_configuration` VALUES('core', 420, 'vnd.in3d.3dml', 'vnd.in3d.3dml', 19);
INSERT INTO `molajo_configuration` VALUES('core', 420, 'vnd.in3d.spot', 'vnd.in3d.spot', 20);
INSERT INTO `molajo_configuration` VALUES('core', 420, 'vnd.IPTC.NewsML', 'vnd.IPTC.NewsML', 11);
INSERT INTO `molajo_configuration` VALUES('core', 420, 'vnd.IPTC.NITF', 'vnd.IPTC.NITF', 10);
INSERT INTO `molajo_configuration` VALUES('core', 420, 'vnd.latex-z', 'vnd.latex-z', 21);
INSERT INTO `molajo_configuration` VALUES('core', 420, 'vnd.motorola.reflex', 'vnd.motorola.reflex', 22);
INSERT INTO `molajo_configuration` VALUES('core', 420, 'vnd.ms-mediapackage', 'vnd.ms-mediapackage', 23);
INSERT INTO `molajo_configuration` VALUES('core', 420, 'vnd.net2phone.commcenter.command', 'vnd.net2phone.commcenter.command', 24);
INSERT INTO `molajo_configuration` VALUES('core', 420, 'vnd.si.uricatalogue', 'vnd.si.uricatalogue', 25);
INSERT INTO `molajo_configuration` VALUES('core', 420, 'vnd.sun.j2me.app-descriptor', 'vnd.sun.j2me.app-descriptor', 26);
INSERT INTO `molajo_configuration` VALUES('core', 420, 'vnd.trolltech.linguist', 'vnd.trolltech.linguist', 27);
INSERT INTO `molajo_configuration` VALUES('core', 420, 'vnd.wap-wml', 'vnd.wap-wml', 28);
INSERT INTO `molajo_configuration` VALUES('core', 420, 'vnd.wap.si', 'vnd.wap.si', 29);
INSERT INTO `molajo_configuration` VALUES('core', 420, 'vnd.wap.wmlscript', 'vnd.wap.wmlscript', 30);
INSERT INTO `molajo_configuration` VALUES('core', 430, '', '', 0);
INSERT INTO `molajo_configuration` VALUES('core', 430, 'jpm', 'jpm', 1);
INSERT INTO `molajo_configuration` VALUES('core', 430, 'mj2', 'mj2', 2);
INSERT INTO `molajo_configuration` VALUES('core', 430, 'quicktime', 'quicktime', 3);
INSERT INTO `molajo_configuration` VALUES('core', 430, 'rtp-enc-aescm128', 'rtp-enc-aescm128', 4);
INSERT INTO `molajo_configuration` VALUES('core', 430, 'vnd-mpegurl', 'vnd-mpegurl', 5);
INSERT INTO `molajo_configuration` VALUES('core', 430, 'vnd-vivo', 'vnd-vivo', 6);
INSERT INTO `molajo_configuration` VALUES('core', 430, 'vnd.CCTV', 'vnd.CCTV', 7);
INSERT INTO `molajo_configuration` VALUES('core', 430, 'vnd.dece-mp4', 'vnd.dece-mp4', 8);
INSERT INTO `molajo_configuration` VALUES('core', 430, 'vnd.dece.hd', 'vnd.dece.hd', 9);
INSERT INTO `molajo_configuration` VALUES('core', 430, 'vnd.dece.mobile', 'vnd.dece.mobile', 10);
INSERT INTO `molajo_configuration` VALUES('core', 430, 'vnd.dece.pd', 'vnd.dece.pd', 11);
INSERT INTO `molajo_configuration` VALUES('core', 430, 'vnd.dece.sd', 'vnd.dece.sd', 12);
INSERT INTO `molajo_configuration` VALUES('core', 430, 'vnd.dece.video', 'vnd.dece.video', 13);
INSERT INTO `molajo_configuration` VALUES('core', 430, 'vnd.directv-mpeg', 'vnd.directv-mpeg', 14);
INSERT INTO `molajo_configuration` VALUES('core', 430, 'vnd.directv.mpeg-tts', 'vnd.directv.mpeg-tts', 15);
INSERT INTO `molajo_configuration` VALUES('core', 430, 'vnd.dvb.file', 'vnd.dvb.file', 16);
INSERT INTO `molajo_configuration` VALUES('core', 430, 'vnd.fvt', 'vnd.fvt', 17);
INSERT INTO `molajo_configuration` VALUES('core', 430, 'vnd.hns.video', 'vnd.hns.video', 18);
INSERT INTO `molajo_configuration` VALUES('core', 430, 'vnd.iptvforum.1dparityfec-1010', 'vnd.iptvforum.1dparityfec-1010', 19);
INSERT INTO `molajo_configuration` VALUES('core', 430, 'vnd.iptvforum.1dparityfec-2005', 'vnd.iptvforum.1dparityfec-2005', 20);
INSERT INTO `molajo_configuration` VALUES('core', 430, 'vnd.iptvforum.2dparityfec-1010', 'vnd.iptvforum.2dparityfec-1010', 21);
INSERT INTO `molajo_configuration` VALUES('core', 430, 'vnd.iptvforum.2dparityfec-2005', 'vnd.iptvforum.2dparityfec-2005', 22);
INSERT INTO `molajo_configuration` VALUES('core', 430, 'vnd.iptvforum.ttsavc', 'vnd.iptvforum.ttsavc', 23);
INSERT INTO `molajo_configuration` VALUES('core', 430, 'vnd.iptvforum.ttsmpeg2', 'vnd.iptvforum.ttsmpeg2', 24);
INSERT INTO `molajo_configuration` VALUES('core', 430, 'vnd.motorola.video', 'vnd.motorola.video', 25);
INSERT INTO `molajo_configuration` VALUES('core', 430, 'vnd.motorola.videop', 'vnd.motorola.videop', 26);
INSERT INTO `molajo_configuration` VALUES('core', 430, 'vnd.mpegurl', 'vnd.mpegurl', 27);
INSERT INTO `molajo_configuration` VALUES('core', 430, 'vnd.ms-playready.media.pyv', 'vnd.ms-playready.media.pyv', 28);
INSERT INTO `molajo_configuration` VALUES('core', 430, 'vnd.nokia.interleaved-multimedia', 'vnd.nokia.interleaved-multimedia', 29);
INSERT INTO `molajo_configuration` VALUES('core', 430, 'vnd.nokia.videovoip', 'vnd.nokia.videovoip', 30);
INSERT INTO `molajo_configuration` VALUES('core', 430, 'vnd.objectvideo', 'vnd.objectvideo', 31);
INSERT INTO `molajo_configuration` VALUES('core', 430, 'vnd.sealed-swf', 'vnd.sealed-swf', 32);
INSERT INTO `molajo_configuration` VALUES('core', 430, 'vnd.sealed.mpeg1', 'vnd.sealed.mpeg1', 33);
INSERT INTO `molajo_configuration` VALUES('core', 430, 'vnd.sealed.mpeg4', 'vnd.sealed.mpeg4', 34);
INSERT INTO `molajo_configuration` VALUES('core', 430, 'vnd.sealed.swf', 'vnd.sealed.swf', 35);
INSERT INTO `molajo_configuration` VALUES('core', 430, 'vnd.sealedmedia.softseal-mov', 'vnd.sealedmedia.softseal-mov', 36);
INSERT INTO `molajo_configuration` VALUES('core', 430, 'vnd.uvvu.mp4', 'vnd.uvvu.mp4', 37);
INSERT INTO `molajo_configuration` VALUES('core', 1100, '', '', 0);
INSERT INTO `molajo_configuration` VALUES('core', 1100, 'add', 'display', 1);
INSERT INTO `molajo_configuration` VALUES('core', 1100, 'apply', 'edit', 4);
INSERT INTO `molajo_configuration` VALUES('core', 1100, 'archive', 'multiple', 11);
INSERT INTO `molajo_configuration` VALUES('core', 1100, 'cancel', 'edit', 5);
INSERT INTO `molajo_configuration` VALUES('core', 1100, 'checkin', 'multiple', 20);
INSERT INTO `molajo_configuration` VALUES('core', 1100, 'copy', 'multiple', 26);
INSERT INTO `molajo_configuration` VALUES('core', 1100, 'create', 'edit', 6);
INSERT INTO `molajo_configuration` VALUES('core', 1100, 'delete', 'multiple', 25);
INSERT INTO `molajo_configuration` VALUES('core', 1100, 'display', 'display', 3);
INSERT INTO `molajo_configuration` VALUES('core', 1100, 'edit', 'display', 2);
INSERT INTO `molajo_configuration` VALUES('core', 1100, 'feature', 'multiple', 16);
INSERT INTO `molajo_configuration` VALUES('core', 1100, 'login', 'login', 28);
INSERT INTO `molajo_configuration` VALUES('core', 1100, 'logout', 'logout', 29);
INSERT INTO `molajo_configuration` VALUES('core', 1100, 'move', 'multiple', 27);
INSERT INTO `molajo_configuration` VALUES('core', 1100, 'orderdown', 'multiple', 23);
INSERT INTO `molajo_configuration` VALUES('core', 1100, 'orderup', 'multiple', 22);
INSERT INTO `molajo_configuration` VALUES('core', 1100, 'publish', 'multiple', 12);
INSERT INTO `molajo_configuration` VALUES('core', 1100, 'reorder', 'multiple', 21);
INSERT INTO `molajo_configuration` VALUES('core', 1100, 'restore', 'edit', 10);
INSERT INTO `molajo_configuration` VALUES('core', 1100, 'save', 'edit', 7);
INSERT INTO `molajo_configuration` VALUES('core', 1100, 'save2copy', 'edit', 8);
INSERT INTO `molajo_configuration` VALUES('core', 1100, 'save2new', 'edit', 9);
INSERT INTO `molajo_configuration` VALUES('core', 1100, 'saveorder', 'multiple', 24);
INSERT INTO `molajo_configuration` VALUES('core', 1100, 'spam', 'multiple', 14);
INSERT INTO `molajo_configuration` VALUES('core', 1100, 'sticky', 'multiple', 18);
INSERT INTO `molajo_configuration` VALUES('core', 1100, 'trash', 'multiple', 15);
INSERT INTO `molajo_configuration` VALUES('core', 1100, 'unfeature', 'multiple', 17);
INSERT INTO `molajo_configuration` VALUES('core', 1100, 'unpublish', 'multiple', 13);
INSERT INTO `molajo_configuration` VALUES('core', 1100, 'unsticky', 'multiple', 19);
INSERT INTO `molajo_configuration` VALUES('core', 1101, '', '', 0);
INSERT INTO `molajo_configuration` VALUES('core', 1101, 'add', 'display', 1);
INSERT INTO `molajo_configuration` VALUES('core', 1101, 'apply', 'edit', 4);
INSERT INTO `molajo_configuration` VALUES('core', 1101, 'archive', 'multiple', 11);
INSERT INTO `molajo_configuration` VALUES('core', 1101, 'cancel', 'edit', 5);
INSERT INTO `molajo_configuration` VALUES('core', 1101, 'checkin', 'multiple', 20);
INSERT INTO `molajo_configuration` VALUES('core', 1101, 'copy', 'multiple', 26);
INSERT INTO `molajo_configuration` VALUES('core', 1101, 'create', 'edit', 6);
INSERT INTO `molajo_configuration` VALUES('core', 1101, 'delete', 'multiple', 25);
INSERT INTO `molajo_configuration` VALUES('core', 1101, 'display', 'display', 3);
INSERT INTO `molajo_configuration` VALUES('core', 1101, 'edit', 'display', 2);
INSERT INTO `molajo_configuration` VALUES('core', 1101, 'feature', 'multiple', 16);
INSERT INTO `molajo_configuration` VALUES('core', 1101, 'login', 'login', 28);
INSERT INTO `molajo_configuration` VALUES('core', 1101, 'logout', 'login', 29);
INSERT INTO `molajo_configuration` VALUES('core', 1101, 'move', 'multiple', 27);
INSERT INTO `molajo_configuration` VALUES('core', 1101, 'orderdown', 'multiple', 23);
INSERT INTO `molajo_configuration` VALUES('core', 1101, 'orderup', 'multiple', 22);
INSERT INTO `molajo_configuration` VALUES('core', 1101, 'publish', 'multiple', 12);
INSERT INTO `molajo_configuration` VALUES('core', 1101, 'reorder', 'multiple', 21);
INSERT INTO `molajo_configuration` VALUES('core', 1101, 'restore', 'edit', 10);
INSERT INTO `molajo_configuration` VALUES('core', 1101, 'save', 'edit', 7);
INSERT INTO `molajo_configuration` VALUES('core', 1101, 'save2copy', 'edit', 8);
INSERT INTO `molajo_configuration` VALUES('core', 1101, 'save2new', 'edit', 9);
INSERT INTO `molajo_configuration` VALUES('core', 1101, 'saveorder', 'multiple', 24);
INSERT INTO `molajo_configuration` VALUES('core', 1101, 'spam', 'multiple', 14);
INSERT INTO `molajo_configuration` VALUES('core', 1101, 'sticky', 'multiple', 18);
INSERT INTO `molajo_configuration` VALUES('core', 1101, 'trash', 'multiple', 15);
INSERT INTO `molajo_configuration` VALUES('core', 1101, 'unfeature', 'multiple', 17);
INSERT INTO `molajo_configuration` VALUES('core', 1101, 'unpublish', 'multiple', 13);
INSERT INTO `molajo_configuration` VALUES('core', 1101, 'unsticky', 'multiple', 19);
INSERT INTO `molajo_configuration` VALUES('core', 1800, '', '', 0);
INSERT INTO `molajo_configuration` VALUES('core', 1800, 'com_articles', 'com_articles', 1);
INSERT INTO `molajo_configuration` VALUES('core', 1801, '', '', 0);
INSERT INTO `molajo_configuration` VALUES('core', 1801, 'com_login', 'com_login', 1);
INSERT INTO `molajo_configuration` VALUES('core', 2000, '', '', 0);
INSERT INTO `molajo_configuration` VALUES('core', 2000, 'display', 'display', 1);
INSERT INTO `molajo_configuration` VALUES('core', 2000, 'edit', 'edit', 2);
INSERT INTO `molajo_configuration` VALUES('core', 2001, '', '', 0);
INSERT INTO `molajo_configuration` VALUES('core', 2001, 'display', 'display', 1);
INSERT INTO `molajo_configuration` VALUES('core', 2001, 'edit', 'edit', 2);
INSERT INTO `molajo_configuration` VALUES('core', 2100, '', '', 0);
INSERT INTO `molajo_configuration` VALUES('core', 2100, 'display', 'display', 1);
INSERT INTO `molajo_configuration` VALUES('core', 2101, '', '', 0);
INSERT INTO `molajo_configuration` VALUES('core', 2101, 'display', 'display', 1);
INSERT INTO `molajo_configuration` VALUES('core', 3000, '', '', 0);
INSERT INTO `molajo_configuration` VALUES('core', 3000, 'default', 'default', 1);
INSERT INTO `molajo_configuration` VALUES('core', 3000, 'item', 'item', 1);
INSERT INTO `molajo_configuration` VALUES('core', 3000, 'items', 'items', 1);
INSERT INTO `molajo_configuration` VALUES('core', 3000, 'table', 'table', 1);
INSERT INTO `molajo_configuration` VALUES('core', 3001, '', '', 0);
INSERT INTO `molajo_configuration` VALUES('core', 3001, 'default', 'default', 1);
INSERT INTO `molajo_configuration` VALUES('core', 3100, '', '', 0);
INSERT INTO `molajo_configuration` VALUES('core', 3100, 'default', 'default', 1);
INSERT INTO `molajo_configuration` VALUES('core', 3101, '', '', 0);
INSERT INTO `molajo_configuration` VALUES('core', 3101, 'default', 'default', 1);
INSERT INTO `molajo_configuration` VALUES('core', 3200, '', '', 0);
INSERT INTO `molajo_configuration` VALUES('core', 3200, 'default', 'default', 1);
INSERT INTO `molajo_configuration` VALUES('core', 3201, '', '', 0);
INSERT INTO `molajo_configuration` VALUES('core', 3201, 'default', 'default', 1);
INSERT INTO `molajo_configuration` VALUES('core', 3300, '', '', 0);
INSERT INTO `molajo_configuration` VALUES('core', 3300, 'default', 'default', 1);
INSERT INTO `molajo_configuration` VALUES('core', 3301, '', '', 0);
INSERT INTO `molajo_configuration` VALUES('core', 3301, 'default', 'default', 1);
INSERT INTO `molajo_configuration` VALUES('core', 4000, '', '', 0);
INSERT INTO `molajo_configuration` VALUES('core', 4000, 'html', 'html', 1);
INSERT INTO `molajo_configuration` VALUES('core', 4001, '', '', 0);
INSERT INTO `molajo_configuration` VALUES('core', 4001, 'html', 'html', 1);
INSERT INTO `molajo_configuration` VALUES('core', 4100, '', '', 0);
INSERT INTO `molajo_configuration` VALUES('core', 4100, 'html', 'html', 1);
INSERT INTO `molajo_configuration` VALUES('core', 4101, '', '', 0);
INSERT INTO `molajo_configuration` VALUES('core', 4101, 'html', 'html', 1);
INSERT INTO `molajo_configuration` VALUES('core', 4200, '', '', 0);
INSERT INTO `molajo_configuration` VALUES('core', 4200, 'error', 'error', 1);
INSERT INTO `molajo_configuration` VALUES('core', 4200, 'feed', 'feed', 2);
INSERT INTO `molajo_configuration` VALUES('core', 4200, 'html', 'html', 3);
INSERT INTO `molajo_configuration` VALUES('core', 4200, 'json', 'json', 4);
INSERT INTO `molajo_configuration` VALUES('core', 4200, 'opensearch', 'opensearch', 5);
INSERT INTO `molajo_configuration` VALUES('core', 4200, 'raw', 'raw', 6);
INSERT INTO `molajo_configuration` VALUES('core', 4200, 'xls', 'xls', 7);
INSERT INTO `molajo_configuration` VALUES('core', 4200, 'xml', 'xml', 8);
INSERT INTO `molajo_configuration` VALUES('core', 4200, 'xmlrpc', 'xmlrpc', 9);
INSERT INTO `molajo_configuration` VALUES('core', 4201, '', '', 0);
INSERT INTO `molajo_configuration` VALUES('core', 4201, 'error', 'error', 1);
INSERT INTO `molajo_configuration` VALUES('core', 4201, 'feed', 'feed', 2);
INSERT INTO `molajo_configuration` VALUES('core', 4201, 'html', 'html', 3);
INSERT INTO `molajo_configuration` VALUES('core', 4201, 'json', 'json', 4);
INSERT INTO `molajo_configuration` VALUES('core', 4201, 'opensearch', 'opensearch', 5);
INSERT INTO `molajo_configuration` VALUES('core', 4201, 'raw', 'raw', 6);
INSERT INTO `molajo_configuration` VALUES('core', 4201, 'xls', 'xls', 7);
INSERT INTO `molajo_configuration` VALUES('core', 4201, 'xml', 'xml', 8);
INSERT INTO `molajo_configuration` VALUES('core', 4201, 'xmlrpc', 'xmlrpc', 9);
INSERT INTO `molajo_configuration` VALUES('core', 4300, '', '', 0);
INSERT INTO `molajo_configuration` VALUES('core', 4300, 'html', 'html', 1);
INSERT INTO `molajo_configuration` VALUES('core', 4301, '', '', 0);
INSERT INTO `molajo_configuration` VALUES('core', 4301, 'html', 'html', 1);
INSERT INTO `molajo_configuration` VALUES('core', 5000, '', '', 0);
INSERT INTO `molajo_configuration` VALUES('core', 5000, 'display', 'display', 1);
INSERT INTO `molajo_configuration` VALUES('core', 5000, 'edit', 'edit', 2);
INSERT INTO `molajo_configuration` VALUES('core', 5001, '', '', 0);
INSERT INTO `molajo_configuration` VALUES('core', 5001, 'display', 'display', 1);
INSERT INTO `molajo_configuration` VALUES('core', 5001, 'edit', 'edit', 2);
INSERT INTO `molajo_configuration` VALUES('core', 6000, '', '', 0);
INSERT INTO `molajo_configuration` VALUES('core', 6000, 'content', 'content', 1);
INSERT INTO `molajo_configuration` VALUES('core', 10000, '', '', 0);
INSERT INTO `molajo_configuration` VALUES('core', 10000, 'core', 'Core ACL Implementation', 1);
INSERT INTO `molajo_configuration` VALUES('core', 10100, '', '', 0);
INSERT INTO `molajo_configuration` VALUES('core', 10100, 'admin', 'admin', 6);
INSERT INTO `molajo_configuration` VALUES('core', 10100, 'create', 'create', 2);
INSERT INTO `molajo_configuration` VALUES('core', 10100, 'delete', 'delete', 5);
INSERT INTO `molajo_configuration` VALUES('core', 10100, 'edit', 'edit', 3);
INSERT INTO `molajo_configuration` VALUES('core', 10100, 'publish', 'publish', 4);
INSERT INTO `molajo_configuration` VALUES('core', 10100, 'view', 'view', 1);
INSERT INTO `molajo_configuration` VALUES('core', 10200, '', '', 0);
INSERT INTO `molajo_configuration` VALUES('core', 10200, 'add', 'create', 1);
INSERT INTO `molajo_configuration` VALUES('core', 10200, 'admin', 'admin', 2);
INSERT INTO `molajo_configuration` VALUES('core', 10200, 'apply', 'edit', 3);
INSERT INTO `molajo_configuration` VALUES('core', 10200, 'archive', 'publish', 4);
INSERT INTO `molajo_configuration` VALUES('core', 10200, 'cancel', '', 5);
INSERT INTO `molajo_configuration` VALUES('core', 10200, 'checkin', 'admin', 6);
INSERT INTO `molajo_configuration` VALUES('core', 10200, 'close', '', 7);
INSERT INTO `molajo_configuration` VALUES('core', 10200, 'copy', 'create', 8);
INSERT INTO `molajo_configuration` VALUES('core', 10200, 'create', 'create', 9);
INSERT INTO `molajo_configuration` VALUES('core', 10200, 'delete', 'delete', 10);
INSERT INTO `molajo_configuration` VALUES('core', 10200, 'edit', 'edit', 12);
INSERT INTO `molajo_configuration` VALUES('core', 10200, 'editstate', 'publish', 13);
INSERT INTO `molajo_configuration` VALUES('core', 10200, 'feature', 'publish', 14);
INSERT INTO `molajo_configuration` VALUES('core', 10200, 'login', 'login', 15);
INSERT INTO `molajo_configuration` VALUES('core', 10200, 'logout', 'logout', 16);
INSERT INTO `molajo_configuration` VALUES('core', 10200, 'manage', 'edit', 17);
INSERT INTO `molajo_configuration` VALUES('core', 10200, 'move', 'edit', 18);
INSERT INTO `molajo_configuration` VALUES('core', 10200, 'orderdown', 'publish', 19);
INSERT INTO `molajo_configuration` VALUES('core', 10200, 'orderup', 'publish', 20);
INSERT INTO `molajo_configuration` VALUES('core', 10200, 'publish', 'publish', 21);
INSERT INTO `molajo_configuration` VALUES('core', 10200, 'reorder', 'publish', 22);
INSERT INTO `molajo_configuration` VALUES('core', 10200, 'restore', 'publish', 23);
INSERT INTO `molajo_configuration` VALUES('core', 10200, 'save', 'edit', 24);
INSERT INTO `molajo_configuration` VALUES('core', 10200, 'save2copy', 'edit', 25);
INSERT INTO `molajo_configuration` VALUES('core', 10200, 'save2new', 'edit', 26);
INSERT INTO `molajo_configuration` VALUES('core', 10200, 'saveorder', 'publish', 27);
INSERT INTO `molajo_configuration` VALUES('core', 10200, 'search', 'view', 28);
INSERT INTO `molajo_configuration` VALUES('core', 10200, 'spam', 'publish', 29);
INSERT INTO `molajo_configuration` VALUES('core', 10200, 'state', 'publish', 30);
INSERT INTO `molajo_configuration` VALUES('core', 10200, 'sticky', 'publish', 31);
INSERT INTO `molajo_configuration` VALUES('core', 10200, 'trash', 'publish', 32);
INSERT INTO `molajo_configuration` VALUES('core', 10200, 'unfeature', 'publish', 33);
INSERT INTO `molajo_configuration` VALUES('core', 10200, 'unpublish', 'publish', 34);
INSERT INTO `molajo_configuration` VALUES('core', 10200, 'unsticky', 'publish', 35);
INSERT INTO `molajo_configuration` VALUES('core', 10200, 'view', 'view', 11);

-- --------------------------------------------------------

--
-- Table structure for table `molajo_content`
--

CREATE TABLE `molajo_content` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary Key',
  `catid` int(11) unsigned NOT NULL COMMENT 'Category ID associated with the Primary Key',
  `title` varchar(255) NOT NULL DEFAULT ' ' COMMENT 'Title',
  `subtitle` varchar(255) NOT NULL DEFAULT ' ' COMMENT 'Subtitle',
  `alias` varchar(255) NOT NULL DEFAULT ' ' COMMENT 'URL Alias',
  `content_type` tinyint(3) NOT NULL DEFAULT '0' COMMENT 'Content Type: Links to molajo_configuration.option_id = 10 and component_option values matching ',
  `content_text` mediumtext COMMENT 'Content Primary Text Field, can include break to designate Introductory and Full text',
  `content_link` varchar(2083) DEFAULT NULL COMMENT 'Content Link for Weblink or Newsfeed Field',
  `content_email_address` varchar(255) DEFAULT NULL COMMENT 'Content Email Field',
  `content_numeric_value` tinyint(3) DEFAULT NULL COMMENT 'Content Numeric Value, ex. vote on poll',
  `content_file` varchar(255) NOT NULL DEFAULT ' ' COMMENT 'Content Network Path to File',
  `featured` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Featured 1-Featured 0-Not Featured',
  `stickied` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Stickied 1-Stickied 0-Not Stickied',
  `user_default` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'User DEFAULT 1-DEFAULT 0-Not DEFAULT',
  `category_default` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Category DEFAULT 1-DEFAULT 0-Not DEFAULT',
  `status` tinyint(3) NOT NULL DEFAULT '0' COMMENT 'Published State 2: Archived 1: Published 0: Unpublished -1: Trashed -2: Spam -10 Version',
  `start_publishing_datetime` datetime NOT NULL COMMENT 'Publish Begin Date and Time',
  `stop_publishing_datetime` datetime NOT NULL COMMENT 'Publish End Date and Time',
  `version` int(11) unsigned NOT NULL DEFAULT '1' COMMENT 'Version Number',
  `version_of_id` int(11) DEFAULT NULL COMMENT 'Primary ID for this Version',
  `status_prior_to_version` int(11) unsigned DEFAULT NULL COMMENT 'State value prior to creating this version copy and changing the state to Version',
  `created_datetime` datetime NOT NULL COMMENT 'Created Date and Time',
  `created_by` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Created by User ID',
  `created_by_alias` varchar(255) NOT NULL DEFAULT ' ' COMMENT 'Created by Alias',
  `created_by_email` varchar(255) DEFAULT NULL COMMENT 'Created By Email Address',
  `created_by_website` varchar(255) DEFAULT NULL COMMENT 'Created By Website',
  `created_by_ip_address` char(15) DEFAULT NULL COMMENT 'Created By IP Address',
  `created_by_referer` varchar(255) DEFAULT NULL COMMENT 'Created By Referer',
  `modified_datetime` datetime NOT NULL COMMENT 'Modified Date',
  `modified_by` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Modified By User ID',
  `checked_out_datetime` datetime NOT NULL COMMENT 'Checked out Date and Time',
  `checked_out_by` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Checked out by User Id',
  `asset_id` int(11) unsigned NOT NULL COMMENT 'FK to the molajo_assets table.',
  `extension_id` int(11) unsigned NOT NULL COMMENT 'Primary Key for Component Content',
  `parent_id` int(11) DEFAULT NULL COMMENT 'Nested set parent',
  `lft` int(11) DEFAULT NULL COMMENT 'Nested set lft',
  `rgt` int(11) DEFAULT NULL COMMENT 'Nested set rgt',
  `level` int(11) DEFAULT '0' COMMENT 'The cached level in the nested tree',
  `metakey` text COMMENT 'Meta Key',
  `metadesc` text COMMENT 'Meta Description',
  `metadata` text COMMENT 'Meta Data',
  `custom_fields` mediumtext COMMENT 'Attributes (Custom Fields)',
  `parameters` mediumtext COMMENT 'Configurable Parameter Values',
  `language` char(7) DEFAULT 'en-GB',
  `translation_of_id` int(11) DEFAULT NULL,
  `ordering` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Ordering',
  PRIMARY KEY (`id`),
  KEY `idx_component_component_id_id` (`extension_id`,`id`),
  KEY `idx_checkout` (`checked_out_by`),
  KEY `idx_state` (`status`),
  KEY `idx_catid` (`catid`),
  KEY `idx_createdby` (`created_by`),
  KEY `idx_featured_catid` (`featured`,`catid`),
  KEY `idx_stickied_catid` (`stickied`,`catid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `molajo_content`
--

INSERT INTO `molajo_content` VALUES(1, 2, 'My First Article', 'Subtitle for My First Article', 'my-first-article', 10, '<p>Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. Aenean ultricies mi vitae est. Mauris placerat eleifend leo.</p>', NULL, NULL, NULL, '', 1, 0, 1, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 42, 'Harry Potter', 'HarryPotter@example.com', 'http://example.com', '127.1.0.0', 'http://example.com', '2011-11-01 00:00:00', 42, '0000-00-00 00:00:00', 0, 7270, 2, 0, NULL, NULL, 0, '', '', '{"robots":"","author":"","rights":""}', '{"text_entry_tag":"","tags":[""],"tag_category":[""]}', '', 'en-GB', NULL, 1);
INSERT INTO `molajo_content` VALUES(2, 2, 'My Second Article', 'Subtitle for My Second Article', 'my-second-article', 10, '<h1>HTML Ipsum Presents</h1>\r\n	       \r\n<p><strong>Pellentesque habitant morbi tristique</strong> senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. <em>Aenean ultricies mi vitae est.</em> Mauris placerat eleifend leo. Quisque sit amet est et sapien ullamcorper pharetra. Vestibulum erat wisi, condimentum sed, <code>commodo vitae</code>, ornare sit amet, wisi. Aenean fermentum, elit eget tincidunt condimentum, eros ipsum rutrum orci, sagittis tempus lacus enim ac dui. <a href="#">Donec non enim</a> in turpis pulvinar facilisis. Ut felis.</p>\r\n\r\n<h2>Header Level 2</h2>\r\n	       \r\n<ol>\r\n   <li>Lorem ipsum dolor sit amet, consectetuer adipiscing elit.</li>\r\n   <li>Aliquam tincidunt mauris eu risus.</li>\r\n</ol>\r\n\r\n<blockquote><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus magna. Cras in mi at felis aliquet congue. Ut a est eget ligula molestie gravida. Curabitur massa. Donec eleifend, libero at sagittis mollis, tellus est malesuada tellus, at luctus turpis elit sit amet quam. Vivamus pretium ornare est.</p></blockquote>\r\n\r\n<h3>Header Level 3</h3>\r\n\r\n<ul>\r\n   <li>Lorem ipsum dolor sit amet, consectetuer adipiscing elit.</li>\r\n   <li>Aliquam tincidunt mauris eu risus.</li>\r\n</ul>\r\n\r\n<pre><code>\r\n#header h1 a { \r\n	display: block; \r\n	width: 300px; \r\n	height: 80px; \r\n}\r\n</code></pre>', NULL, NULL, NULL, '', 0, 0, 0, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 42, 'Harry Potter', 'HarryPotter@example.com', 'http://example.com', '127.1.0.0', 'http://example.com', '2011-11-01 00:00:00', 42, '0000-00-00 00:00:00', 0, 7271, 2, 0, NULL, NULL, 0, '', '', '{"robots":"","author":"","rights":""}', '{"text_entry_tag":"","tags":[""],"tag_category":[""]}', '', 'en-GB', NULL, 2);
INSERT INTO `molajo_content` VALUES(3, 2, 'My Third Article', 'Subtitle for My Third Article', 'my-third-article', 10, '<p>Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.</p>\r\n\r\n<ol>\r\n   <li>Lorem ipsum dolor sit amet, consectetuer adipiscing elit.</li>\r\n   <li>Aliquam tincidunt mauris eu risus.</li>\r\n   <li>Vestibulum auctor dapibus neque.</li>\r\n</ol>\r\n\r\n<p>Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.</p>\r\n	       ', NULL, NULL, NULL, '', 0, 0, 0, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 42, 'Harry Potter', 'HarryPotter@example.com', 'http://example.com', '127.1.0.0', 'http://example.com', '2011-11-01 00:00:00', 42, '0000-00-00 00:00:00', 0, 7272, 2, 0, NULL, NULL, 0, '', '', '{"robots":"","author":"","rights":""}', '{"text_entry_tag":"","tags":[""],"tag_category":[""]}', '', 'en-GB', NULL, 3);
INSERT INTO `molajo_content` VALUES(4, 2, 'My Fourth Article', 'Subtitle for My Fourth Article', 'my-fourth-article', 10, '<p>Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. Aenean ultricies mi vitae est. Mauris placerat eleifend leo.</p>', NULL, NULL, NULL, '', 0, 0, 0, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 42, 'Harry Potter', 'HarryPotter@example.com', 'http://example.com', '127.1.0.0', 'http://example.com', '2011-11-01 00:00:00', 42, '0000-00-00 00:00:00', 0, 7273, 2, 0, NULL, NULL, 0, '', '', '{"robots":"","author":"","rights":""}', '{"text_entry_tag":"","tags":[""],"tag_category":[""]}', '', 'en-GB', NULL, 4);
INSERT INTO `molajo_content` VALUES(5, 2, 'My Fifth Article', 'Subtitle for My Fifth Article', 'my-fifth-article', 10, '<dl> <dt>Definition list</dt>\r\n   <dd>Consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna \r\naliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea \r\ncommodo consequat.</dd>\r\n   <dt>Lorem ipsum dolor sit amet</dt>\r\n   <dd>Consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna \r\naliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea \r\ncommodo consequat.</dd>\r\n</dl>', NULL, NULL, NULL, '', 0, 0, 0, 0, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 42, 'Harry Potter', 'HarryPotter@example.com', 'http://example.com', '127.1.0.0', 'http://example.com', '2011-11-01 00:00:00', 42, '0000-00-00 00:00:00', 0, 7274, 2, 0, NULL, NULL, 0, '', '', '{"robots":"","author":"","rights":""}', '{"text_entry_tag":"","tags":[""],"tag_category":[""]}', '', 'en-GB', NULL, 5);

-- --------------------------------------------------------

--
-- Table structure for table `molajo_content_categories`
--

CREATE TABLE `molajo_content_categories` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Actions Primary Key',
  `category_id` int(11) NOT NULL,
  `content_id` int(11) NOT NULL,
  `content_table_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_actions_table_title` (`content_table_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `molajo_content_categories`
--


-- --------------------------------------------------------

--
-- Table structure for table `molajo_extensions`
--

CREATE TABLE `molajo_extensions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `extension_type_id` int(11) DEFAULT NULL,
  `folder` varchar(255) NOT NULL,
  `update_site_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `element_folder_application_id` (`folder`),
  KEY `extension` (`extension_type_id`,`folder`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2712 ;

--
-- Dumping data for table `molajo_extensions`
--

INSERT INTO `molajo_extensions` VALUES(1000, 'Administrator Home', 5, '', 1);
INSERT INTO `molajo_extensions` VALUES(1010, 'Launchpad Main Menu', 5, '', 1);
INSERT INTO `molajo_extensions` VALUES(1020, 'Launchpad Configure', 5, '', 1);
INSERT INTO `molajo_extensions` VALUES(1030, 'Launchpad Access', 5, '', 1);
INSERT INTO `molajo_extensions` VALUES(1040, 'Launchpad Create', 5, '', 1);
INSERT INTO `molajo_extensions` VALUES(1050, 'Launchpad Build', 5, '', 1);
INSERT INTO `molajo_extensions` VALUES(1060, 'Main Menu', 5, '', 1);
INSERT INTO `molajo_extensions` VALUES(2551, 'com_admin', 1, '', 1);
INSERT INTO `molajo_extensions` VALUES(2552, 'com_articles', 1, '', 1);
INSERT INTO `molajo_extensions` VALUES(2553, 'com_categories', 1, '', 1);
INSERT INTO `molajo_extensions` VALUES(2554, 'com_config', 1, '', 1);
INSERT INTO `molajo_extensions` VALUES(2555, 'com_dashboard', 1, '', 1);
INSERT INTO `molajo_extensions` VALUES(2556, 'com_extensions', 1, '', 1);
INSERT INTO `molajo_extensions` VALUES(2557, 'com_installer', 1, '', 1);
INSERT INTO `molajo_extensions` VALUES(2558, 'com_layouts', 1, '', 1);
INSERT INTO `molajo_extensions` VALUES(2559, 'com_login', 1, '', 1);
INSERT INTO `molajo_extensions` VALUES(2560, 'com_media', 1, '', 1);
INSERT INTO `molajo_extensions` VALUES(2561, 'com_menus', 1, '', 1);
INSERT INTO `molajo_extensions` VALUES(2562, 'com_modules', 1, '', 1);
INSERT INTO `molajo_extensions` VALUES(2563, 'com_plugins', 1, '', 1);
INSERT INTO `molajo_extensions` VALUES(2564, 'com_redirect', 1, '', 1);
INSERT INTO `molajo_extensions` VALUES(2565, 'com_search', 1, '', 1);
INSERT INTO `molajo_extensions` VALUES(2566, 'com_templates', 1, '', 1);
INSERT INTO `molajo_extensions` VALUES(2567, 'com_admin', 1, '', 1);
INSERT INTO `molajo_extensions` VALUES(2568, 'com_users', 1, '', 1);
INSERT INTO `molajo_extensions` VALUES(2569, 'English (UK)', 2, '', 1);
INSERT INTO `molajo_extensions` VALUES(2570, 'English (US)', 2, '', 1);
INSERT INTO `molajo_extensions` VALUES(2571, 'head', 3, 'document', 1);
INSERT INTO `molajo_extensions` VALUES(2572, 'messages', 3, 'document', 1);
INSERT INTO `molajo_extensions` VALUES(2573, 'errors', 3, 'document', 1);
INSERT INTO `molajo_extensions` VALUES(2574, 'atom', 3, 'document', 1);
INSERT INTO `molajo_extensions` VALUES(2575, 'rss', 3, 'document', 1);
INSERT INTO `molajo_extensions` VALUES(2576, 'admin_acl_panel', 3, 'extension', 1);
INSERT INTO `molajo_extensions` VALUES(2577, 'admin_activity', 3, 'extension', 1);
INSERT INTO `molajo_extensions` VALUES(2578, 'admin_edit', 3, 'extension', 1);
INSERT INTO `molajo_extensions` VALUES(2579, 'admin_favorites', 3, 'extension', 1);
INSERT INTO `molajo_extensions` VALUES(2580, 'admin_feed', 3, 'extension', 1);
INSERT INTO `molajo_extensions` VALUES(2581, 'admin_footer', 3, 'extension', 1);
INSERT INTO `molajo_extensions` VALUES(2582, 'admin_header', 3, 'extension', 1);
INSERT INTO `molajo_extensions` VALUES(2583, 'admin_inbox', 3, 'extension', 1);
INSERT INTO `molajo_extensions` VALUES(2584, 'admin_launchpad', 3, 'extension', 1);
INSERT INTO `molajo_extensions` VALUES(2585, 'admin_list', 3, 'extension', 1);
INSERT INTO `molajo_extensions` VALUES(2586, 'admin_login', 3, 'extension', 1);
INSERT INTO `molajo_extensions` VALUES(2587, 'admin_modal', 3, 'extension', 1);
INSERT INTO `molajo_extensions` VALUES(2588, 'admin_pagination', 3, 'extension', 1);
INSERT INTO `molajo_extensions` VALUES(2589, 'admin_toolbar', 3, 'extension', 1);
INSERT INTO `molajo_extensions` VALUES(2590, 'audio', 3, 'extension', 1);
INSERT INTO `molajo_extensions` VALUES(2591, 'contact_form', 3, 'extension', 1);
INSERT INTO `molajo_extensions` VALUES(2592, 'default', 3, 'extension', 1);
INSERT INTO `molajo_extensions` VALUES(2593, 'dummy', 3, 'extension', 1);
INSERT INTO `molajo_extensions` VALUES(2594, 'faq', 3, 'extension', 1);
INSERT INTO `molajo_extensions` VALUES(2595, 'item', 3, 'extension', 1);
INSERT INTO `molajo_extensions` VALUES(2596, 'list', 3, 'extension', 1);
INSERT INTO `molajo_extensions` VALUES(2597, 'items', 3, 'extension', 1);
INSERT INTO `molajo_extensions` VALUES(2598, 'list', 3, 'extension', 1);
INSERT INTO `molajo_extensions` VALUES(2599, 'pagination', 3, 'extension', 1);
INSERT INTO `molajo_extensions` VALUES(2600, 'social_bookmarks', 3, 'extension', 1);
INSERT INTO `molajo_extensions` VALUES(2601, 'syntaxhighlighter', 3, 'extension', 1);
INSERT INTO `molajo_extensions` VALUES(2602, 'table', 3, 'extension', 1);
INSERT INTO `molajo_extensions` VALUES(2603, 'tree', 3, 'extension', 1);
INSERT INTO `molajo_extensions` VALUES(2604, 'twig_example', 3, 'extension', 1);
INSERT INTO `molajo_extensions` VALUES(2605, 'video', 3, 'extension', 1);
INSERT INTO `molajo_extensions` VALUES(2606, 'button', 3, 'formfields', 1);
INSERT INTO `molajo_extensions` VALUES(2607, 'colorpicker', 3, 'formfields', 1);
INSERT INTO `molajo_extensions` VALUES(2608, 'list', 3, 'formfields', 1);
INSERT INTO `molajo_extensions` VALUES(2609, 'media', 3, 'formfields', 1);
INSERT INTO `molajo_extensions` VALUES(2610, 'number', 3, 'formfields', 1);
INSERT INTO `molajo_extensions` VALUES(2611, 'option', 3, 'formfields', 1);
INSERT INTO `molajo_extensions` VALUES(2612, 'rules', 3, 'formfields', 1);
INSERT INTO `molajo_extensions` VALUES(2613, 'spacer', 3, 'formfields', 1);
INSERT INTO `molajo_extensions` VALUES(2614, 'text', 3, 'formfields', 1);
INSERT INTO `molajo_extensions` VALUES(2615, 'textarea', 3, 'formfields', 1);
INSERT INTO `molajo_extensions` VALUES(2616, 'user', 3, 'formfields', 1);
INSERT INTO `molajo_extensions` VALUES(2617, 'article', 3, 'wrap', 1);
INSERT INTO `molajo_extensions` VALUES(2618, 'aside', 3, 'wrap', 1);
INSERT INTO `molajo_extensions` VALUES(2619, 'div', 3, 'wrap', 1);
INSERT INTO `molajo_extensions` VALUES(2620, 'footer', 3, 'wrap', 1);
INSERT INTO `molajo_extensions` VALUES(2621, 'horizontal', 3, 'wrap', 1);
INSERT INTO `molajo_extensions` VALUES(2622, 'nav', 3, 'wrap', 1);
INSERT INTO `molajo_extensions` VALUES(2623, 'none', 3, 'wrap', 1);
INSERT INTO `molajo_extensions` VALUES(2624, 'outline', 3, 'wrap', 1);
INSERT INTO `molajo_extensions` VALUES(2625, 'section', 3, 'wrap', 1);
INSERT INTO `molajo_extensions` VALUES(2626, 'table', 3, 'wrap', 1);
INSERT INTO `molajo_extensions` VALUES(2627, 'tabs', 3, 'wrap', 1);
INSERT INTO `molajo_extensions` VALUES(2628, 'akismet', 10, '', 1);
INSERT INTO `molajo_extensions` VALUES(2629, 'Doctrine', 10, '', 1);
INSERT INTO `molajo_extensions` VALUES(2630, 'includes', 10, '', 1);
INSERT INTO `molajo_extensions` VALUES(2631, 'jplatform', 10, '', 1);
INSERT INTO `molajo_extensions` VALUES(2632, 'molajo', 10, '', 1);
INSERT INTO `molajo_extensions` VALUES(2633, 'mollom', 10, '', 1);
INSERT INTO `molajo_extensions` VALUES(2634, 'recaptcha', 10, '', 1);
INSERT INTO `molajo_extensions` VALUES(2635, 'Twig', 10, '', 1);
INSERT INTO `molajo_extensions` VALUES(2636, 'mod_breadcrumbs', 6, '', 1);
INSERT INTO `molajo_extensions` VALUES(2637, 'mod_content', 6, '', 1);
INSERT INTO `molajo_extensions` VALUES(2638, 'mod_custom', 6, '', 1);
INSERT INTO `molajo_extensions` VALUES(2639, 'mod_feed', 6, '', 1);
INSERT INTO `molajo_extensions` VALUES(2640, 'mod_header', 6, '', 1);
INSERT INTO `molajo_extensions` VALUES(2641, 'mod_launchpad', 6, '', 1);
INSERT INTO `molajo_extensions` VALUES(2642, 'mod_layout', 6, '', 1);
INSERT INTO `molajo_extensions` VALUES(2643, 'mod_login', 6, '', 1);
INSERT INTO `molajo_extensions` VALUES(2644, 'mod_logout', 6, '', 1);
INSERT INTO `molajo_extensions` VALUES(2645, 'mod_members', 6, '', 1);
INSERT INTO `molajo_extensions` VALUES(2646, 'mod_menu', 6, '', 1);
INSERT INTO `molajo_extensions` VALUES(2647, 'mod_pagination', 6, '', 1);
INSERT INTO `molajo_extensions` VALUES(2648, 'mod_search', 6, '', 1);
INSERT INTO `molajo_extensions` VALUES(2649, 'mod_syndicate', 6, '', 1);
INSERT INTO `molajo_extensions` VALUES(2650, 'mod_toolbar', 6, '', 1);
INSERT INTO `molajo_extensions` VALUES(2651, 'mod_breadcrumbs', 6, '', 1);
INSERT INTO `molajo_extensions` VALUES(2652, 'mod_content', 6, '', 1);
INSERT INTO `molajo_extensions` VALUES(2653, 'mod_custom', 6, '', 1);
INSERT INTO `molajo_extensions` VALUES(2654, 'mod_feed', 6, '', 1);
INSERT INTO `molajo_extensions` VALUES(2655, 'mod_header', 6, '', 1);
INSERT INTO `molajo_extensions` VALUES(2656, 'mod_launchpad', 6, '', 1);
INSERT INTO `molajo_extensions` VALUES(2657, 'mod_layout', 6, '', 1);
INSERT INTO `molajo_extensions` VALUES(2658, 'mod_login', 6, '', 1);
INSERT INTO `molajo_extensions` VALUES(2659, 'mod_logout', 6, '', 1);
INSERT INTO `molajo_extensions` VALUES(2660, 'mod_members', 6, '', 1);
INSERT INTO `molajo_extensions` VALUES(2661, 'mod_menu', 6, '', 1);
INSERT INTO `molajo_extensions` VALUES(2662, 'mod_pagination', 6, '', 1);
INSERT INTO `molajo_extensions` VALUES(2663, 'mod_search', 6, '', 1);
INSERT INTO `molajo_extensions` VALUES(2664, 'mod_syndicate', 6, '', 1);
INSERT INTO `molajo_extensions` VALUES(2665, 'mod_toolbar', 6, '', 1);
INSERT INTO `molajo_extensions` VALUES(2666, 'example', 8, 'acl', 1);
INSERT INTO `molajo_extensions` VALUES(2667, 'molajo', 8, 'authentication', 1);
INSERT INTO `molajo_extensions` VALUES(2668, 'broadcast', 8, 'content', 1);
INSERT INTO `molajo_extensions` VALUES(2669, 'content', 8, 'content', 1);
INSERT INTO `molajo_extensions` VALUES(2670, 'emailcloak', 8, 'content', 1);
INSERT INTO `molajo_extensions` VALUES(2671, 'links', 8, 'content', 1);
INSERT INTO `molajo_extensions` VALUES(2672, 'loadmodule', 8, 'content', 1);
INSERT INTO `molajo_extensions` VALUES(2673, 'media', 8, 'content', 1);
INSERT INTO `molajo_extensions` VALUES(2674, 'protect', 8, 'content', 1);
INSERT INTO `molajo_extensions` VALUES(2675, 'responses', 8, 'content', 1);
INSERT INTO `molajo_extensions` VALUES(2676, 'aloha', 8, 'editors', 1);
INSERT INTO `molajo_extensions` VALUES(2677, 'none', 8, 'editors', 1);
INSERT INTO `molajo_extensions` VALUES(2678, 'article', 8, 'editor-buttons', 1);
INSERT INTO `molajo_extensions` VALUES(2679, 'editor', 8, 'editor-buttons', 1);
INSERT INTO `molajo_extensions` VALUES(2680, 'image', 8, 'editor-buttons', 1);
INSERT INTO `molajo_extensions` VALUES(2681, 'pagebreak', 8, 'editor-buttons', 1);
INSERT INTO `molajo_extensions` VALUES(2682, 'readmore', 8, 'editor-buttons', 1);
INSERT INTO `molajo_extensions` VALUES(2683, 'molajo', 8, 'extension', 1);
INSERT INTO `molajo_extensions` VALUES(2684, 'extend', 8, 'molajo', 1);
INSERT INTO `molajo_extensions` VALUES(2685, 'minifier', 8, 'molajo', 1);
INSERT INTO `molajo_extensions` VALUES(2686, 'search', 8, 'molajo', 1);
INSERT INTO `molajo_extensions` VALUES(2687, 'tags', 8, 'molajo', 1);
INSERT INTO `molajo_extensions` VALUES(2688, 'urls', 8, 'molajo', 1);
INSERT INTO `molajo_extensions` VALUES(2689, 'molajosample', 8, 'query', 1);
INSERT INTO `molajo_extensions` VALUES(2690, 'categories', 8, 'search', 1);
INSERT INTO `molajo_extensions` VALUES(2691, 'articles', 8, 'search', 1);
INSERT INTO `molajo_extensions` VALUES(2692, 'cache', 8, 'system', 1);
INSERT INTO `molajo_extensions` VALUES(2693, 'compress', 8, 'system', 1);
INSERT INTO `molajo_extensions` VALUES(2694, 'create', 8, 'system', 1);
INSERT INTO `molajo_extensions` VALUES(2695, 'debug', 8, 'system', 1);
INSERT INTO `molajo_extensions` VALUES(2696, 'languagefilter', 8, 'system', 1);
INSERT INTO `molajo_extensions` VALUES(2697, 'log', 8, 'system', 1);
INSERT INTO `molajo_extensions` VALUES(2698, 'logout', 8, 'system', 1);
INSERT INTO `molajo_extensions` VALUES(2699, 'molajo', 8, 'system', 1);
INSERT INTO `molajo_extensions` VALUES(2700, 'p3p', 8, 'system', 1);
INSERT INTO `molajo_extensions` VALUES(2701, 'parameters', 8, 'system', 1);
INSERT INTO `molajo_extensions` VALUES(2702, 'redirect', 8, 'system', 1);
INSERT INTO `molajo_extensions` VALUES(2703, 'remember', 8, 'system', 1);
INSERT INTO `molajo_extensions` VALUES(2704, 'system', 8, 'system', 1);
INSERT INTO `molajo_extensions` VALUES(2705, 'webservices', 8, 'system', 1);
INSERT INTO `molajo_extensions` VALUES(2706, 'molajo', 8, 'user', 1);
INSERT INTO `molajo_extensions` VALUES(2707, 'profile', 8, 'user', 1);
INSERT INTO `molajo_extensions` VALUES(2708, 'construct', 9, '', 1);
INSERT INTO `molajo_extensions` VALUES(2709, 'install', 9, '', 1);
INSERT INTO `molajo_extensions` VALUES(2710, 'molajito', 9, '', 1);
INSERT INTO `molajo_extensions` VALUES(2711, 'system', 9, '', 1);

-- --------------------------------------------------------

--
-- Table structure for table `molajo_extension_instances`
--

CREATE TABLE `molajo_extension_instances` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary Key',
  `extension_type_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL DEFAULT ' ' COMMENT 'Title',
  `sub_title` varchar(255) NOT NULL DEFAULT ' ' COMMENT 'Subtitle',
  `alias` varchar(255) DEFAULT ' ',
  `content_text` mediumtext COMMENT 'Content Primary Text Field, can include break to designate Introductory and Full text',
  `protected` int(3) DEFAULT '0',
  `status` tinyint(3) NOT NULL DEFAULT '0' COMMENT 'Published State 2: Archived 1: Published 0: Unpublished -1: Trashed -2: Spam -10 Version',
  `start_publishing_datetime` datetime DEFAULT NULL COMMENT 'Publish Begin Date and Time',
  `stop_publishing_datetime` datetime DEFAULT NULL COMMENT 'Publish End Date and Time',
  `version` int(11) unsigned NOT NULL DEFAULT '1' COMMENT 'Version Number',
  `version_of_id` int(11) DEFAULT NULL COMMENT 'Primary ID for this Version',
  `status_prior_to_version` int(11) unsigned DEFAULT NULL COMMENT 'State value prior to creating this version copy and changing the state to Version',
  `created_datetime` datetime DEFAULT NULL COMMENT 'Created Date and Time',
  `created_by` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Created by User ID',
  `modified_datetime` datetime DEFAULT NULL COMMENT 'Modified Date',
  `modified_by` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Modified By User ID',
  `checked_out_datetime` datetime DEFAULT NULL COMMENT 'Checked out Date and Time',
  `checked_out_by` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Checked out by User Id',
  `asset_id` int(11) NOT NULL,
  `extension_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Primary Key for Component Content',
  `custom_fields` mediumtext,
  `parameters` mediumtext COMMENT 'Attributes (Custom Fields)',
  `module_position` varchar(50) NOT NULL DEFAULT ' ' COMMENT 'User DEFAULT 1-DEFAULT 0-Not DEFAULT',
  `menu_item_parent_id` int(11) DEFAULT NULL,
  `menu_item_level` int(11) DEFAULT NULL,
  `menu_item_type` varchar(45) DEFAULT NULL,
  `menu_item_extension_id` varchar(45) DEFAULT NULL,
  `menu_item_template_id` int(11) DEFAULT NULL,
  `menu_item_link_target` varchar(45) DEFAULT NULL,
  `menu_item_path` varchar(255) NOT NULL,
  `menu_item_link` varchar(255) NOT NULL,
  `menu_item_lft` int(11) DEFAULT NULL,
  `menu_item_rgt` int(11) DEFAULT NULL,
  `menu_item_home` tinyint(3) DEFAULT NULL,
  `language` char(7) DEFAULT 'en-GB',
  `translation_of_id` int(11) DEFAULT NULL,
  `ordering` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Ordering',
  PRIMARY KEY (`id`),
  KEY `idx_component_component_id_id` (`extension_id`,`id`),
  KEY `idx_checkout` (`checked_out_by`),
  KEY `idx_state` (`status`),
  KEY `idx_createdby` (`created_by`),
  KEY `asset_id_UNIQUE` (`asset_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=252 ;

--
-- Dumping data for table `molajo_extension_instances`
--

INSERT INTO `molajo_extension_instances` VALUES(1, 1, 'com_admin', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7018, 2551, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(2, 1, 'com_articles', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7019, 2552, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(3, 1, 'com_categories', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7020, 2553, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(4, 1, 'com_config', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7024, 2554, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(5, 1, 'com_dashboard', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7025, 2555, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(6, 1, 'com_extensions', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7026, 2556, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(7, 1, 'com_installer', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7027, 2557, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(8, 1, 'com_layouts', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7028, 2558, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(9, 1, 'com_login', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7029, 2559, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(10, 1, 'com_media', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7030, 2560, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(11, 1, 'com_menus', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7031, 2561, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(12, 1, 'com_modules', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7032, 2562, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(13, 1, 'com_plugins', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7033, 2563, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(14, 1, 'com_redirect', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7034, 2564, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(15, 1, 'com_search', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7035, 2565, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(16, 1, 'com_templates', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7036, 2566, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(17, 1, 'com_admin', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7037, 2567, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(18, 1, 'com_users', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7038, 2568, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(32, 2, 'English (UK)', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7052, 2569, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(33, 2, 'English (US)', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7053, 2570, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(35, 3, 'head', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7055, 2571, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(36, 3, 'messages', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7056, 2572, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(37, 3, 'errors', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7057, 2573, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(38, 3, 'atom', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7058, 2574, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(39, 3, 'rss', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7059, 2575, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(40, 3, 'admin_acl_panel', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7060, 2576, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(41, 3, 'admin_activity', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7061, 2577, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(42, 3, 'admin_edit', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7062, 2578, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(43, 3, 'admin_favorites', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7063, 2579, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(44, 3, 'admin_feed', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7064, 2580, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(45, 3, 'admin_footer', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7065, 2581, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(46, 3, 'admin_header', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7066, 2582, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(47, 3, 'admin_inbox', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7067, 2583, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(48, 3, 'admin_launchpad', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7068, 2584, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(49, 3, 'admin_list', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7069, 2585, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(50, 3, 'admin_login', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7070, 2586, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(51, 3, 'admin_modal', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7071, 2587, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(52, 3, 'admin_pagination', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7072, 2588, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(53, 3, 'admin_toolbar', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7073, 2589, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(54, 3, 'audio', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7074, 2590, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(55, 3, 'contact_form', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7075, 2591, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(56, 3, 'default', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7076, 2592, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(57, 3, 'dummy', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7077, 2593, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(58, 3, 'faq', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7078, 2594, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(59, 3, 'item', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7079, 2595, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(60, 3, 'list', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7080, 2596, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(61, 3, 'items', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7081, 2597, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(62, 3, 'list', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7082, 2598, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(63, 3, 'pagination', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7083, 2599, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(64, 3, 'social_bookmarks', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7084, 2600, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(65, 3, 'syntaxhighlighter', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7085, 2601, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(66, 3, 'table', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7086, 2602, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(67, 3, 'tree', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7087, 2603, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(68, 3, 'twig_example', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7088, 2604, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(69, 3, 'video', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7089, 2605, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(70, 3, 'button', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7090, 2606, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(71, 3, 'colorpicker', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7091, 2607, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(72, 3, 'list', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7092, 2608, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(73, 3, 'media', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7093, 2609, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(74, 3, 'number', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7094, 2610, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(75, 3, 'option', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7095, 2611, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(76, 3, 'rules', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7096, 2612, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(77, 3, 'spacer', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7097, 2613, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(78, 3, 'text', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7098, 2614, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(79, 3, 'textarea', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7099, 2615, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(80, 3, 'user', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7100, 2616, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(81, 3, 'article', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7101, 2617, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(82, 3, 'aside', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7102, 2618, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(83, 3, 'div', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7103, 2619, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(84, 3, 'footer', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7104, 2620, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(85, 3, 'horizontal', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7105, 2621, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(86, 3, 'nav', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7106, 2622, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(87, 3, 'none', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7107, 2623, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(88, 3, 'outline', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7108, 2624, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(89, 3, 'section', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7109, 2625, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(90, 3, 'table', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7110, 2626, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(91, 3, 'tabs', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7111, 2627, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(98, 10, 'akismet', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7118, 2628, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(99, 10, 'Doctrine', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7119, 2629, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(100, 10, 'includes', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7120, 2630, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(101, 10, 'jplatform', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7121, 2631, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(102, 10, 'molajo', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7122, 2632, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(103, 10, 'mollom', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7123, 2633, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(104, 10, 'recaptcha', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7124, 2634, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(105, 10, 'Twig', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7125, 2635, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(113, 6, 'mod_breadcrumbs', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7133, 2636, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(114, 6, 'mod_content', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7134, 2637, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(115, 6, 'mod_custom', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7135, 2638, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(116, 6, 'mod_feed', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7136, 2639, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(117, 6, 'mod_header', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7137, 2640, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(118, 6, 'mod_launchpad', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7138, 2641, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(119, 6, 'mod_layout', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7139, 2642, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(120, 6, 'mod_login', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7140, 2643, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(121, 6, 'mod_logout', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7141, 2644, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(122, 6, 'mod_members', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7142, 2645, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(123, 6, 'mod_menu', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7143, 2646, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(124, 6, 'mod_pagination', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7144, 2647, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(125, 6, 'mod_search', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7145, 2648, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(126, 6, 'mod_syndicate', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7146, 2649, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(127, 6, 'mod_toolbar', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7147, 2650, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(128, 6, 'mod_breadcrumbs', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7148, 2651, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(129, 6, 'mod_content', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7149, 2652, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(130, 6, 'mod_custom', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7150, 2653, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(131, 6, 'mod_feed', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7151, 2654, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(132, 6, 'mod_header', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7152, 2655, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(133, 6, 'mod_launchpad', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7153, 2656, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(134, 6, 'mod_layout', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7154, 2657, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(135, 6, 'mod_login', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7155, 2658, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(136, 6, 'mod_logout', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7156, 2659, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(137, 6, 'mod_members', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7157, 2660, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(138, 6, 'mod_menu', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7158, 2661, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(139, 6, 'mod_pagination', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7159, 2662, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(140, 6, 'mod_search', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7160, 2663, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(141, 6, 'mod_syndicate', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7161, 2664, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(142, 6, 'mod_toolbar', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7162, 2665, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(144, 8, 'example', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7164, 2666, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(145, 8, 'molajo', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7165, 2667, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(146, 8, 'broadcast', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7166, 2668, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(147, 8, 'content', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7167, 2669, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(148, 8, 'emailcloak', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7168, 2670, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(149, 8, 'links', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7169, 2671, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(150, 8, 'loadmodule', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7170, 2672, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(151, 8, 'media', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7171, 2673, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(152, 8, 'protect', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7172, 2674, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(153, 8, 'responses', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7173, 2675, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(154, 8, 'article', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7174, 2678, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(155, 8, 'editor', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7175, 2679, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(156, 8, 'image', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7176, 2680, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(157, 8, 'pagebreak', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7177, 2681, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(158, 8, 'readmore', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7178, 2682, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(159, 8, 'aloha', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7179, 2676, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(160, 8, 'none', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7180, 2677, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(161, 8, 'molajo', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7181, 2683, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(162, 8, 'extend', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7182, 2684, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(163, 8, 'minifier', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7183, 2685, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(164, 8, 'search', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7184, 2686, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(165, 8, 'tags', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7185, 2687, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(166, 8, 'urls', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7186, 2688, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(167, 8, 'molajosample', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7187, 2689, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(168, 8, 'categories', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7188, 2690, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(169, 8, 'articles', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7189, 2691, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(170, 8, 'cache', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7190, 2692, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(171, 8, 'compress', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7191, 2693, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(172, 8, 'create', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7192, 2694, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(173, 8, 'debug', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7193, 2695, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(174, 8, 'languagefilter', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7194, 2696, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(175, 8, 'log', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7195, 2697, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(176, 8, 'logout', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7196, 2698, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(177, 8, 'molajo', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7197, 2699, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(178, 8, 'p3p', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7198, 2700, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(179, 8, 'parameters', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7199, 2701, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(180, 8, 'redirect', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7200, 2702, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(181, 8, 'remember', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7201, 2703, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(182, 8, 'system', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7202, 2704, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(183, 8, 'webservices', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7203, 2705, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(184, 8, 'molajo', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7204, 2706, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(185, 8, 'profile', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7205, 2707, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(207, 9, 'construct', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7227, 2708, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(208, 9, 'install', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7228, 2709, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(209, 9, 'molajito', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7229, 2710, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(210, 9, 'system', ' ', ' ', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7230, 2711, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(215, 5, 'Home', ' ', 'home', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7235, 1000, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', 'index.php?option=com_dashboard', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(216, 5, 'Configure', ' ', 'configure', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7236, 1010, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, 'configure', 'index.php?option=com_dashboard&type=configure', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(217, 5, 'Access', ' ', 'access', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7237, 1010, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, 'access', 'index.php?option=com_dashboard&type=access', NULL, NULL, NULL, 'en-GB', NULL, 2);
INSERT INTO `molajo_extension_instances` VALUES(218, 5, 'Create', ' ', 'create', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7238, 1010, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, 'create', 'index.php?option=com_dashboard&type=create', NULL, NULL, NULL, 'en-GB', NULL, 3);
INSERT INTO `molajo_extension_instances` VALUES(219, 5, 'Build', ' ', 'build', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7239, 1010, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, 'build', 'index.php?option=com_dashboard&type=build', NULL, NULL, NULL, 'en-GB', NULL, 4);
INSERT INTO `molajo_extension_instances` VALUES(220, 5, 'Search', ' ', 'search', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7240, 1010, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, 'search', 'index.php?option=com_dashboard&type=search', NULL, NULL, NULL, 'en-GB', NULL, 5);
INSERT INTO `molajo_extension_instances` VALUES(221, 5, 'Profile', ' ', 'profile', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7241, 1020, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, 'configure/profile', 'index.php?option=com_profile', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(222, 5, 'System', ' ', 'system', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7242, 1020, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, 'configure/system', 'index.php?option=com_config', NULL, NULL, NULL, 'en-GB', NULL, 2);
INSERT INTO `molajo_extension_instances` VALUES(223, 5, 'Checkin', ' ', 'checkin', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7243, 1020, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, 'configure/checkin', 'index.php?option=com_checkin', NULL, NULL, NULL, 'en-GB', NULL, 3);
INSERT INTO `molajo_extension_instances` VALUES(224, 5, 'Cache', ' ', 'cache', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7244, 1020, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, 'configure/cache', 'index.php?option=com_cache', NULL, NULL, NULL, 'en-GB', NULL, 4);
INSERT INTO `molajo_extension_instances` VALUES(225, 5, 'Backup', ' ', 'backup', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7245, 1020, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, 'configure/backup', 'index.php?option=com_backup', NULL, NULL, NULL, 'en-GB', NULL, 5);
INSERT INTO `molajo_extension_instances` VALUES(226, 5, 'Redirects', ' ', 'redirects', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7246, 1020, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, 'configure/redirects', 'index.php?option=com_redirects', NULL, NULL, NULL, 'en-GB', NULL, 6);
INSERT INTO `molajo_extension_instances` VALUES(227, 5, 'Users', ' ', 'users', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7247, 1030, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, 'access/users', 'index.php?option=com_users', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(228, 5, 'Groups', ' ', 'groups', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7248, 1030, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, 'access/groups', 'index.php?option=com_groups', NULL, NULL, NULL, 'en-GB', NULL, 2);
INSERT INTO `molajo_extension_instances` VALUES(229, 5, 'Permissions', ' ', 'permissions', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7249, 1030, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, 'access/permissions', 'index.php?option=com_permissions', NULL, NULL, NULL, 'en-GB', NULL, 3);
INSERT INTO `molajo_extension_instances` VALUES(230, 5, 'Messages', ' ', 'messages', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7250, 1030, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, 'access/messages', 'index.php?option=com_messages', NULL, NULL, NULL, 'en-GB', NULL, 4);
INSERT INTO `molajo_extension_instances` VALUES(231, 5, 'Activity', ' ', 'activity', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7251, 1030, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, 'access/activity', 'index.php?option=com_activity', NULL, NULL, NULL, 'en-GB', NULL, 5);
INSERT INTO `molajo_extension_instances` VALUES(232, 5, 'Articles', ' ', 'articles', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7252, 1040, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, 'create/articles', 'index.php?option=com_articles', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(233, 5, 'Tags', ' ', 'tags', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7253, 1040, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, 'create/tags', 'index.php?option=com_tags', NULL, NULL, NULL, 'en-GB', NULL, 2);
INSERT INTO `molajo_extension_instances` VALUES(234, 5, 'Comments', ' ', 'comments', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7254, 1040, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, 'create/comments', 'index.php?option=com_comments', NULL, NULL, NULL, 'en-GB', NULL, 3);
INSERT INTO `molajo_extension_instances` VALUES(235, 5, 'Media', ' ', 'media', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7255, 1040, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, 'create/media', 'index.php?option=com_media', NULL, NULL, NULL, 'en-GB', NULL, 4);
INSERT INTO `molajo_extension_instances` VALUES(236, 5, 'Categories', ' ', 'categories', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7256, 1040, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, 'create/categories', 'index.php?option=com_categories', NULL, NULL, NULL, 'en-GB', NULL, 5);
INSERT INTO `molajo_extension_instances` VALUES(237, 5, 'Extensions', ' ', 'extensions', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7257, 1050, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, 'build/extensions', 'index.php?option=com_extensions', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(238, 5, 'Languages', ' ', 'languages', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7258, 1050, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, 'build/languages', 'index.php?option=com_languages', NULL, NULL, NULL, 'en-GB', NULL, 2);
INSERT INTO `molajo_extension_instances` VALUES(239, 5, 'Layouts', ' ', 'layouts', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7259, 1050, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, 'build/layouts', 'index.php?option=com_layouts', NULL, NULL, NULL, 'en-GB', NULL, 3);
INSERT INTO `molajo_extension_instances` VALUES(240, 5, 'Modules', ' ', 'modules', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7260, 1050, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, 'build/modules', 'index.php?option=com_modules', NULL, NULL, NULL, 'en-GB', NULL, 4);
INSERT INTO `molajo_extension_instances` VALUES(241, 5, 'Plugins', ' ', 'plugins', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7261, 1050, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, 'build/plugins', 'index.php?option=com_plugins', NULL, NULL, NULL, 'en-GB', NULL, 5);
INSERT INTO `molajo_extension_instances` VALUES(242, 5, 'Templates', ' ', 'templates', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7262, 1050, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, 'build/templates', 'index.php?option=com_templates', NULL, NULL, NULL, 'en-GB', NULL, 6);
INSERT INTO `molajo_extension_instances` VALUES(243, 5, 'Main Menu', ' ', 'templates', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 7263, 1060, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, 'en-GB', NULL, 7);
INSERT INTO `molajo_extension_instances` VALUES(244, 5, 'Home', ' ', 'home', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 0, 1060, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, '', 'index.php?option=com_articles', NULL, NULL, NULL, 'en-GB', NULL, 1);
INSERT INTO `molajo_extension_instances` VALUES(245, 5, 'New Article', ' ', 'new-article', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 0, 1060, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, 'new-article', 'index.php?option=com_articles&view=article&layout=edit', NULL, NULL, NULL, 'en-GB', NULL, 2);
INSERT INTO `molajo_extension_instances` VALUES(246, 5, 'Article', ' ', 'article', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 0, 1060, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, 'article', 'index.php?option=com_articles&view=articles&layout=item&id=5', NULL, NULL, NULL, 'en-GB', NULL, 3);
INSERT INTO `molajo_extension_instances` VALUES(247, 5, 'Blog', ' ', 'blog', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 0, 1060, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, 'blog', 'index.php?option=com_articles&view=articles&layout=items&catid=2', NULL, NULL, NULL, 'en-GB', NULL, 4);
INSERT INTO `molajo_extension_instances` VALUES(248, 5, 'List', ' ', 'list', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 0, 1060, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, 'list', 'index.php?option=com_articles&view=articles&layout=table&catid=2', NULL, NULL, NULL, 'en-GB', NULL, 5);
INSERT INTO `molajo_extension_instances` VALUES(249, 5, 'Table', ' ', 'table', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 0, 1060, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, 'table', 'index.php?option=com_articles&type=search', NULL, NULL, NULL, 'en-GB', NULL, 6);
INSERT INTO `molajo_extension_instances` VALUES(250, 5, 'Login', ' ', 'login', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 0, 1060, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, 'login', 'index.php?option=com_users&view=login', NULL, NULL, NULL, 'en-GB', NULL, 7);
INSERT INTO `molajo_extension_instances` VALUES(251, 5, 'Search', ' ', 'search', NULL, 1, 1, '2011-11-01 00:00:00', '0000-00-00 00:00:00', 1, NULL, NULL, '2011-11-01 00:00:00', 0, NULL, 0, NULL, 0, 0, 1060, NULL, NULL, ' ', NULL, NULL, NULL, NULL, NULL, NULL, 'search', 'index.php?option=com_search&type=search', NULL, NULL, NULL, 'en-GB', NULL, 8);

-- --------------------------------------------------------

--
-- Table structure for table `molajo_extension_types`
--

CREATE TABLE `molajo_extension_types` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Actions Primary Key',
  `extension_type` varchar(255) NOT NULL DEFAULT ' ',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_actions_table_title` (`extension_type`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Dumping data for table `molajo_extension_types`
--

INSERT INTO `molajo_extension_types` VALUES(1, 'components');
INSERT INTO `molajo_extension_types` VALUES(2, 'languages');
INSERT INTO `molajo_extension_types` VALUES(3, 'layouts');
INSERT INTO `molajo_extension_types` VALUES(10, 'libraries');
INSERT INTO `molajo_extension_types` VALUES(4, 'manifests');
INSERT INTO `molajo_extension_types` VALUES(5, 'menus');
INSERT INTO `molajo_extension_types` VALUES(6, 'modules');
INSERT INTO `molajo_extension_types` VALUES(7, 'parameters');
INSERT INTO `molajo_extension_types` VALUES(8, 'plugins');
INSERT INTO `molajo_extension_types` VALUES(9, 'templates');

-- --------------------------------------------------------

--
-- Table structure for table `molajo_extension_usage`
--

CREATE TABLE `molajo_extension_usage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `extension_id` int(11) NOT NULL,
  `asset_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `molajo_extension_usage`
--


-- --------------------------------------------------------

--
-- Table structure for table `molajo_groups`
--

CREATE TABLE `molajo_groups` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Group Primary Key',
  `title` varchar(255) NOT NULL DEFAULT '  ',
  `subtitle` varchar(255) NOT NULL DEFAULT ' ',
  `description` mediumtext NOT NULL,
  `parent_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Parent ID',
  `lft` int(11) NOT NULL DEFAULT '0' COMMENT 'Nested set lft.',
  `rgt` int(11) NOT NULL DEFAULT '0' COMMENT 'Nested set rgt.',
  `type_id` int(11) unsigned NOT NULL DEFAULT '1' COMMENT 'Users: 0, Groups: 1',
  `asset_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'FK to the #__assets table.',
  `protected` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'If true, protects group from system removal via the interface.',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_usergroup_parent_title_lookup` (`parent_id`,`title`,`type_id`),
  UNIQUE KEY `idx_asset_table_id_join` (`asset_id`),
  KEY `idx_usergroup_title_lookup` (`title`),
  KEY `idx_usergroup_adjacency_lookup` (`parent_id`),
  KEY `idx_usergroup_type_id` (`type_id`),
  KEY `idx_usergroup_nested_set_lookup` (`lft`,`rgt`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `molajo_groups`
--

INSERT INTO `molajo_groups` VALUES(1, 'Public', '', 'All visitors regardless of authentication status', 0, 0, 1, 1, 7013, 1);
INSERT INTO `molajo_groups` VALUES(2, 'Guest', '', 'Visitors not authenticated', 0, 2, 3, 1, 7012, 1);
INSERT INTO `molajo_groups` VALUES(3, 'Registered', '', 'Authentication visitors', 0, 4, 5, 1, 7014, 1);
INSERT INTO `molajo_groups` VALUES(4, 'Administrator', '', 'System Administrator', 0, 6, 7, 1, 7011, 1);

-- --------------------------------------------------------

--
-- Table structure for table `molajo_group_permissions`
--

CREATE TABLE `molajo_group_permissions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Permissions Primary Key',
  `group_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Foreign Key to #_groups.id',
  `asset_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Foreign Key to molajo_assets.id',
  `action_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Foreign Key to molajo_actions.id',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_asset_action_to_group_lookup` (`asset_id`,`action_id`,`group_id`),
  UNIQUE KEY `idx_group_to_asset_action_lookup` (`group_id`,`asset_id`,`action_id`),
  KEY `fk_molajo_permissions_groups_molajo_groups1` (`group_id`),
  KEY `fk_molajo_permissions_groups_molajo_assets1` (`asset_id`),
  KEY `fk_molajo_permissions_groups_molajo_actions1` (`action_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=512 ;

--
-- Dumping data for table `molajo_group_permissions`
--

INSERT INTO `molajo_group_permissions` VALUES(1, 1, 7011, 3);
INSERT INTO `molajo_group_permissions` VALUES(2, 1, 7012, 3);
INSERT INTO `molajo_group_permissions` VALUES(3, 1, 7013, 3);
INSERT INTO `molajo_group_permissions` VALUES(4, 1, 7014, 3);
INSERT INTO `molajo_group_permissions` VALUES(5, 1, 7018, 3);
INSERT INTO `molajo_group_permissions` VALUES(6, 1, 7019, 3);
INSERT INTO `molajo_group_permissions` VALUES(7, 1, 7020, 3);
INSERT INTO `molajo_group_permissions` VALUES(15, 3, 7021, 3);
INSERT INTO `molajo_group_permissions` VALUES(206, 4, 7021, 3);
INSERT INTO `molajo_group_permissions` VALUES(16, 3, 7022, 3);
INSERT INTO `molajo_group_permissions` VALUES(207, 4, 7022, 3);
INSERT INTO `molajo_group_permissions` VALUES(17, 3, 7023, 3);
INSERT INTO `molajo_group_permissions` VALUES(208, 4, 7023, 3);
INSERT INTO `molajo_group_permissions` VALUES(18, 3, 7024, 3);
INSERT INTO `molajo_group_permissions` VALUES(209, 4, 7024, 3);
INSERT INTO `molajo_group_permissions` VALUES(19, 3, 7025, 3);
INSERT INTO `molajo_group_permissions` VALUES(210, 4, 7025, 3);
INSERT INTO `molajo_group_permissions` VALUES(20, 3, 7026, 3);
INSERT INTO `molajo_group_permissions` VALUES(211, 4, 7026, 3);
INSERT INTO `molajo_group_permissions` VALUES(21, 3, 7027, 3);
INSERT INTO `molajo_group_permissions` VALUES(212, 4, 7027, 3);
INSERT INTO `molajo_group_permissions` VALUES(22, 3, 7028, 3);
INSERT INTO `molajo_group_permissions` VALUES(213, 4, 7028, 3);
INSERT INTO `molajo_group_permissions` VALUES(23, 3, 7029, 3);
INSERT INTO `molajo_group_permissions` VALUES(214, 4, 7029, 3);
INSERT INTO `molajo_group_permissions` VALUES(24, 3, 7030, 3);
INSERT INTO `molajo_group_permissions` VALUES(215, 4, 7030, 3);
INSERT INTO `molajo_group_permissions` VALUES(25, 3, 7031, 3);
INSERT INTO `molajo_group_permissions` VALUES(216, 4, 7031, 3);
INSERT INTO `molajo_group_permissions` VALUES(26, 3, 7032, 3);
INSERT INTO `molajo_group_permissions` VALUES(217, 4, 7032, 3);
INSERT INTO `molajo_group_permissions` VALUES(27, 3, 7033, 3);
INSERT INTO `molajo_group_permissions` VALUES(218, 4, 7033, 3);
INSERT INTO `molajo_group_permissions` VALUES(28, 3, 7034, 3);
INSERT INTO `molajo_group_permissions` VALUES(219, 4, 7034, 3);
INSERT INTO `molajo_group_permissions` VALUES(29, 3, 7035, 3);
INSERT INTO `molajo_group_permissions` VALUES(220, 4, 7035, 3);
INSERT INTO `molajo_group_permissions` VALUES(30, 3, 7036, 3);
INSERT INTO `molajo_group_permissions` VALUES(221, 4, 7036, 3);
INSERT INTO `molajo_group_permissions` VALUES(31, 3, 7037, 3);
INSERT INTO `molajo_group_permissions` VALUES(222, 4, 7037, 3);
INSERT INTO `molajo_group_permissions` VALUES(32, 3, 7038, 3);
INSERT INTO `molajo_group_permissions` VALUES(223, 4, 7038, 3);
INSERT INTO `molajo_group_permissions` VALUES(33, 3, 7052, 3);
INSERT INTO `molajo_group_permissions` VALUES(224, 4, 7052, 3);
INSERT INTO `molajo_group_permissions` VALUES(34, 3, 7053, 3);
INSERT INTO `molajo_group_permissions` VALUES(225, 4, 7053, 3);
INSERT INTO `molajo_group_permissions` VALUES(35, 3, 7055, 3);
INSERT INTO `molajo_group_permissions` VALUES(226, 4, 7055, 3);
INSERT INTO `molajo_group_permissions` VALUES(36, 3, 7056, 3);
INSERT INTO `molajo_group_permissions` VALUES(227, 4, 7056, 3);
INSERT INTO `molajo_group_permissions` VALUES(37, 3, 7057, 3);
INSERT INTO `molajo_group_permissions` VALUES(228, 4, 7057, 3);
INSERT INTO `molajo_group_permissions` VALUES(38, 3, 7058, 3);
INSERT INTO `molajo_group_permissions` VALUES(229, 4, 7058, 3);
INSERT INTO `molajo_group_permissions` VALUES(39, 3, 7059, 3);
INSERT INTO `molajo_group_permissions` VALUES(230, 4, 7059, 3);
INSERT INTO `molajo_group_permissions` VALUES(40, 3, 7060, 3);
INSERT INTO `molajo_group_permissions` VALUES(231, 4, 7060, 3);
INSERT INTO `molajo_group_permissions` VALUES(41, 3, 7061, 3);
INSERT INTO `molajo_group_permissions` VALUES(232, 4, 7061, 3);
INSERT INTO `molajo_group_permissions` VALUES(42, 3, 7062, 3);
INSERT INTO `molajo_group_permissions` VALUES(233, 4, 7062, 3);
INSERT INTO `molajo_group_permissions` VALUES(43, 3, 7063, 3);
INSERT INTO `molajo_group_permissions` VALUES(234, 4, 7063, 3);
INSERT INTO `molajo_group_permissions` VALUES(44, 3, 7064, 3);
INSERT INTO `molajo_group_permissions` VALUES(235, 4, 7064, 3);
INSERT INTO `molajo_group_permissions` VALUES(45, 3, 7065, 3);
INSERT INTO `molajo_group_permissions` VALUES(236, 4, 7065, 3);
INSERT INTO `molajo_group_permissions` VALUES(46, 3, 7066, 3);
INSERT INTO `molajo_group_permissions` VALUES(237, 4, 7066, 3);
INSERT INTO `molajo_group_permissions` VALUES(47, 3, 7067, 3);
INSERT INTO `molajo_group_permissions` VALUES(238, 4, 7067, 3);
INSERT INTO `molajo_group_permissions` VALUES(48, 3, 7068, 3);
INSERT INTO `molajo_group_permissions` VALUES(239, 4, 7068, 3);
INSERT INTO `molajo_group_permissions` VALUES(49, 3, 7069, 3);
INSERT INTO `molajo_group_permissions` VALUES(240, 4, 7069, 3);
INSERT INTO `molajo_group_permissions` VALUES(50, 3, 7070, 3);
INSERT INTO `molajo_group_permissions` VALUES(241, 4, 7070, 3);
INSERT INTO `molajo_group_permissions` VALUES(51, 3, 7071, 3);
INSERT INTO `molajo_group_permissions` VALUES(242, 4, 7071, 3);
INSERT INTO `molajo_group_permissions` VALUES(52, 3, 7072, 3);
INSERT INTO `molajo_group_permissions` VALUES(243, 4, 7072, 3);
INSERT INTO `molajo_group_permissions` VALUES(53, 3, 7073, 3);
INSERT INTO `molajo_group_permissions` VALUES(244, 4, 7073, 3);
INSERT INTO `molajo_group_permissions` VALUES(54, 3, 7074, 3);
INSERT INTO `molajo_group_permissions` VALUES(245, 4, 7074, 3);
INSERT INTO `molajo_group_permissions` VALUES(55, 3, 7075, 3);
INSERT INTO `molajo_group_permissions` VALUES(246, 4, 7075, 3);
INSERT INTO `molajo_group_permissions` VALUES(56, 3, 7076, 3);
INSERT INTO `molajo_group_permissions` VALUES(247, 4, 7076, 3);
INSERT INTO `molajo_group_permissions` VALUES(57, 3, 7077, 3);
INSERT INTO `molajo_group_permissions` VALUES(248, 4, 7077, 3);
INSERT INTO `molajo_group_permissions` VALUES(58, 3, 7078, 3);
INSERT INTO `molajo_group_permissions` VALUES(249, 4, 7078, 3);
INSERT INTO `molajo_group_permissions` VALUES(59, 3, 7079, 3);
INSERT INTO `molajo_group_permissions` VALUES(250, 4, 7079, 3);
INSERT INTO `molajo_group_permissions` VALUES(60, 3, 7080, 3);
INSERT INTO `molajo_group_permissions` VALUES(251, 4, 7080, 3);
INSERT INTO `molajo_group_permissions` VALUES(61, 3, 7081, 3);
INSERT INTO `molajo_group_permissions` VALUES(252, 4, 7081, 3);
INSERT INTO `molajo_group_permissions` VALUES(62, 3, 7082, 3);
INSERT INTO `molajo_group_permissions` VALUES(253, 4, 7082, 3);
INSERT INTO `molajo_group_permissions` VALUES(63, 3, 7083, 3);
INSERT INTO `molajo_group_permissions` VALUES(254, 4, 7083, 3);
INSERT INTO `molajo_group_permissions` VALUES(64, 3, 7084, 3);
INSERT INTO `molajo_group_permissions` VALUES(255, 4, 7084, 3);
INSERT INTO `molajo_group_permissions` VALUES(65, 3, 7085, 3);
INSERT INTO `molajo_group_permissions` VALUES(256, 4, 7085, 3);
INSERT INTO `molajo_group_permissions` VALUES(66, 3, 7086, 3);
INSERT INTO `molajo_group_permissions` VALUES(257, 4, 7086, 3);
INSERT INTO `molajo_group_permissions` VALUES(67, 3, 7087, 3);
INSERT INTO `molajo_group_permissions` VALUES(258, 4, 7087, 3);
INSERT INTO `molajo_group_permissions` VALUES(68, 3, 7088, 3);
INSERT INTO `molajo_group_permissions` VALUES(259, 4, 7088, 3);
INSERT INTO `molajo_group_permissions` VALUES(69, 3, 7089, 3);
INSERT INTO `molajo_group_permissions` VALUES(260, 4, 7089, 3);
INSERT INTO `molajo_group_permissions` VALUES(70, 3, 7090, 3);
INSERT INTO `molajo_group_permissions` VALUES(261, 4, 7090, 3);
INSERT INTO `molajo_group_permissions` VALUES(71, 3, 7091, 3);
INSERT INTO `molajo_group_permissions` VALUES(262, 4, 7091, 3);
INSERT INTO `molajo_group_permissions` VALUES(72, 3, 7092, 3);
INSERT INTO `molajo_group_permissions` VALUES(263, 4, 7092, 3);
INSERT INTO `molajo_group_permissions` VALUES(73, 3, 7093, 3);
INSERT INTO `molajo_group_permissions` VALUES(264, 4, 7093, 3);
INSERT INTO `molajo_group_permissions` VALUES(74, 3, 7094, 3);
INSERT INTO `molajo_group_permissions` VALUES(265, 4, 7094, 3);
INSERT INTO `molajo_group_permissions` VALUES(75, 3, 7095, 3);
INSERT INTO `molajo_group_permissions` VALUES(266, 4, 7095, 3);
INSERT INTO `molajo_group_permissions` VALUES(76, 3, 7096, 3);
INSERT INTO `molajo_group_permissions` VALUES(267, 4, 7096, 3);
INSERT INTO `molajo_group_permissions` VALUES(77, 3, 7097, 3);
INSERT INTO `molajo_group_permissions` VALUES(268, 4, 7097, 3);
INSERT INTO `molajo_group_permissions` VALUES(78, 3, 7098, 3);
INSERT INTO `molajo_group_permissions` VALUES(269, 4, 7098, 3);
INSERT INTO `molajo_group_permissions` VALUES(79, 3, 7099, 3);
INSERT INTO `molajo_group_permissions` VALUES(270, 4, 7099, 3);
INSERT INTO `molajo_group_permissions` VALUES(80, 3, 7100, 3);
INSERT INTO `molajo_group_permissions` VALUES(271, 4, 7100, 3);
INSERT INTO `molajo_group_permissions` VALUES(81, 3, 7101, 3);
INSERT INTO `molajo_group_permissions` VALUES(272, 4, 7101, 3);
INSERT INTO `molajo_group_permissions` VALUES(82, 3, 7102, 3);
INSERT INTO `molajo_group_permissions` VALUES(273, 4, 7102, 3);
INSERT INTO `molajo_group_permissions` VALUES(83, 3, 7103, 3);
INSERT INTO `molajo_group_permissions` VALUES(274, 4, 7103, 3);
INSERT INTO `molajo_group_permissions` VALUES(84, 3, 7104, 3);
INSERT INTO `molajo_group_permissions` VALUES(275, 4, 7104, 3);
INSERT INTO `molajo_group_permissions` VALUES(85, 3, 7105, 3);
INSERT INTO `molajo_group_permissions` VALUES(276, 4, 7105, 3);
INSERT INTO `molajo_group_permissions` VALUES(86, 3, 7106, 3);
INSERT INTO `molajo_group_permissions` VALUES(277, 4, 7106, 3);
INSERT INTO `molajo_group_permissions` VALUES(87, 3, 7107, 3);
INSERT INTO `molajo_group_permissions` VALUES(278, 4, 7107, 3);
INSERT INTO `molajo_group_permissions` VALUES(88, 3, 7108, 3);
INSERT INTO `molajo_group_permissions` VALUES(279, 4, 7108, 3);
INSERT INTO `molajo_group_permissions` VALUES(89, 3, 7109, 3);
INSERT INTO `molajo_group_permissions` VALUES(280, 4, 7109, 3);
INSERT INTO `molajo_group_permissions` VALUES(90, 3, 7110, 3);
INSERT INTO `molajo_group_permissions` VALUES(281, 4, 7110, 3);
INSERT INTO `molajo_group_permissions` VALUES(91, 3, 7111, 3);
INSERT INTO `molajo_group_permissions` VALUES(282, 4, 7111, 3);
INSERT INTO `molajo_group_permissions` VALUES(92, 3, 7118, 3);
INSERT INTO `molajo_group_permissions` VALUES(283, 4, 7118, 3);
INSERT INTO `molajo_group_permissions` VALUES(93, 3, 7119, 3);
INSERT INTO `molajo_group_permissions` VALUES(284, 4, 7119, 3);
INSERT INTO `molajo_group_permissions` VALUES(94, 3, 7120, 3);
INSERT INTO `molajo_group_permissions` VALUES(285, 4, 7120, 3);
INSERT INTO `molajo_group_permissions` VALUES(95, 3, 7121, 3);
INSERT INTO `molajo_group_permissions` VALUES(286, 4, 7121, 3);
INSERT INTO `molajo_group_permissions` VALUES(96, 3, 7122, 3);
INSERT INTO `molajo_group_permissions` VALUES(287, 4, 7122, 3);
INSERT INTO `molajo_group_permissions` VALUES(97, 3, 7123, 3);
INSERT INTO `molajo_group_permissions` VALUES(288, 4, 7123, 3);
INSERT INTO `molajo_group_permissions` VALUES(98, 3, 7124, 3);
INSERT INTO `molajo_group_permissions` VALUES(289, 4, 7124, 3);
INSERT INTO `molajo_group_permissions` VALUES(99, 3, 7125, 3);
INSERT INTO `molajo_group_permissions` VALUES(290, 4, 7125, 3);
INSERT INTO `molajo_group_permissions` VALUES(100, 3, 7133, 3);
INSERT INTO `molajo_group_permissions` VALUES(291, 4, 7133, 3);
INSERT INTO `molajo_group_permissions` VALUES(101, 3, 7134, 3);
INSERT INTO `molajo_group_permissions` VALUES(292, 4, 7134, 3);
INSERT INTO `molajo_group_permissions` VALUES(102, 3, 7135, 3);
INSERT INTO `molajo_group_permissions` VALUES(293, 4, 7135, 3);
INSERT INTO `molajo_group_permissions` VALUES(103, 3, 7136, 3);
INSERT INTO `molajo_group_permissions` VALUES(294, 4, 7136, 3);
INSERT INTO `molajo_group_permissions` VALUES(104, 3, 7137, 3);
INSERT INTO `molajo_group_permissions` VALUES(295, 4, 7137, 3);
INSERT INTO `molajo_group_permissions` VALUES(105, 3, 7138, 3);
INSERT INTO `molajo_group_permissions` VALUES(296, 4, 7138, 3);
INSERT INTO `molajo_group_permissions` VALUES(106, 3, 7139, 3);
INSERT INTO `molajo_group_permissions` VALUES(297, 4, 7139, 3);
INSERT INTO `molajo_group_permissions` VALUES(107, 3, 7140, 3);
INSERT INTO `molajo_group_permissions` VALUES(298, 4, 7140, 3);
INSERT INTO `molajo_group_permissions` VALUES(108, 3, 7141, 3);
INSERT INTO `molajo_group_permissions` VALUES(299, 4, 7141, 3);
INSERT INTO `molajo_group_permissions` VALUES(109, 3, 7142, 3);
INSERT INTO `molajo_group_permissions` VALUES(300, 4, 7142, 3);
INSERT INTO `molajo_group_permissions` VALUES(110, 3, 7143, 3);
INSERT INTO `molajo_group_permissions` VALUES(301, 4, 7143, 3);
INSERT INTO `molajo_group_permissions` VALUES(111, 3, 7144, 3);
INSERT INTO `molajo_group_permissions` VALUES(302, 4, 7144, 3);
INSERT INTO `molajo_group_permissions` VALUES(112, 3, 7145, 3);
INSERT INTO `molajo_group_permissions` VALUES(303, 4, 7145, 3);
INSERT INTO `molajo_group_permissions` VALUES(113, 3, 7146, 3);
INSERT INTO `molajo_group_permissions` VALUES(304, 4, 7146, 3);
INSERT INTO `molajo_group_permissions` VALUES(114, 3, 7147, 3);
INSERT INTO `molajo_group_permissions` VALUES(305, 4, 7147, 3);
INSERT INTO `molajo_group_permissions` VALUES(115, 3, 7148, 3);
INSERT INTO `molajo_group_permissions` VALUES(306, 4, 7148, 3);
INSERT INTO `molajo_group_permissions` VALUES(116, 3, 7149, 3);
INSERT INTO `molajo_group_permissions` VALUES(307, 4, 7149, 3);
INSERT INTO `molajo_group_permissions` VALUES(117, 3, 7150, 3);
INSERT INTO `molajo_group_permissions` VALUES(308, 4, 7150, 3);
INSERT INTO `molajo_group_permissions` VALUES(118, 3, 7151, 3);
INSERT INTO `molajo_group_permissions` VALUES(309, 4, 7151, 3);
INSERT INTO `molajo_group_permissions` VALUES(119, 3, 7152, 3);
INSERT INTO `molajo_group_permissions` VALUES(310, 4, 7152, 3);
INSERT INTO `molajo_group_permissions` VALUES(120, 3, 7153, 3);
INSERT INTO `molajo_group_permissions` VALUES(311, 4, 7153, 3);
INSERT INTO `molajo_group_permissions` VALUES(121, 3, 7154, 3);
INSERT INTO `molajo_group_permissions` VALUES(312, 4, 7154, 3);
INSERT INTO `molajo_group_permissions` VALUES(122, 3, 7155, 3);
INSERT INTO `molajo_group_permissions` VALUES(313, 4, 7155, 3);
INSERT INTO `molajo_group_permissions` VALUES(123, 3, 7156, 3);
INSERT INTO `molajo_group_permissions` VALUES(314, 4, 7156, 3);
INSERT INTO `molajo_group_permissions` VALUES(124, 3, 7157, 3);
INSERT INTO `molajo_group_permissions` VALUES(315, 4, 7157, 3);
INSERT INTO `molajo_group_permissions` VALUES(125, 3, 7158, 3);
INSERT INTO `molajo_group_permissions` VALUES(316, 4, 7158, 3);
INSERT INTO `molajo_group_permissions` VALUES(126, 3, 7159, 3);
INSERT INTO `molajo_group_permissions` VALUES(317, 4, 7159, 3);
INSERT INTO `molajo_group_permissions` VALUES(127, 3, 7160, 3);
INSERT INTO `molajo_group_permissions` VALUES(318, 4, 7160, 3);
INSERT INTO `molajo_group_permissions` VALUES(128, 3, 7161, 3);
INSERT INTO `molajo_group_permissions` VALUES(319, 4, 7161, 3);
INSERT INTO `molajo_group_permissions` VALUES(129, 3, 7162, 3);
INSERT INTO `molajo_group_permissions` VALUES(320, 4, 7162, 3);
INSERT INTO `molajo_group_permissions` VALUES(130, 3, 7164, 3);
INSERT INTO `molajo_group_permissions` VALUES(321, 4, 7164, 3);
INSERT INTO `molajo_group_permissions` VALUES(131, 3, 7165, 3);
INSERT INTO `molajo_group_permissions` VALUES(322, 4, 7165, 3);
INSERT INTO `molajo_group_permissions` VALUES(132, 3, 7166, 3);
INSERT INTO `molajo_group_permissions` VALUES(323, 4, 7166, 3);
INSERT INTO `molajo_group_permissions` VALUES(133, 3, 7167, 3);
INSERT INTO `molajo_group_permissions` VALUES(324, 4, 7167, 3);
INSERT INTO `molajo_group_permissions` VALUES(134, 3, 7168, 3);
INSERT INTO `molajo_group_permissions` VALUES(325, 4, 7168, 3);
INSERT INTO `molajo_group_permissions` VALUES(135, 3, 7169, 3);
INSERT INTO `molajo_group_permissions` VALUES(326, 4, 7169, 3);
INSERT INTO `molajo_group_permissions` VALUES(136, 3, 7170, 3);
INSERT INTO `molajo_group_permissions` VALUES(327, 4, 7170, 3);
INSERT INTO `molajo_group_permissions` VALUES(137, 3, 7171, 3);
INSERT INTO `molajo_group_permissions` VALUES(328, 4, 7171, 3);
INSERT INTO `molajo_group_permissions` VALUES(138, 3, 7172, 3);
INSERT INTO `molajo_group_permissions` VALUES(329, 4, 7172, 3);
INSERT INTO `molajo_group_permissions` VALUES(139, 3, 7173, 3);
INSERT INTO `molajo_group_permissions` VALUES(330, 4, 7173, 3);
INSERT INTO `molajo_group_permissions` VALUES(140, 3, 7174, 3);
INSERT INTO `molajo_group_permissions` VALUES(331, 4, 7174, 3);
INSERT INTO `molajo_group_permissions` VALUES(141, 3, 7175, 3);
INSERT INTO `molajo_group_permissions` VALUES(332, 4, 7175, 3);
INSERT INTO `molajo_group_permissions` VALUES(142, 3, 7176, 3);
INSERT INTO `molajo_group_permissions` VALUES(333, 4, 7176, 3);
INSERT INTO `molajo_group_permissions` VALUES(143, 3, 7177, 3);
INSERT INTO `molajo_group_permissions` VALUES(334, 4, 7177, 3);
INSERT INTO `molajo_group_permissions` VALUES(144, 3, 7178, 3);
INSERT INTO `molajo_group_permissions` VALUES(335, 4, 7178, 3);
INSERT INTO `molajo_group_permissions` VALUES(145, 3, 7179, 3);
INSERT INTO `molajo_group_permissions` VALUES(336, 4, 7179, 3);
INSERT INTO `molajo_group_permissions` VALUES(146, 3, 7180, 3);
INSERT INTO `molajo_group_permissions` VALUES(337, 4, 7180, 3);
INSERT INTO `molajo_group_permissions` VALUES(147, 3, 7181, 3);
INSERT INTO `molajo_group_permissions` VALUES(338, 4, 7181, 3);
INSERT INTO `molajo_group_permissions` VALUES(148, 3, 7182, 3);
INSERT INTO `molajo_group_permissions` VALUES(339, 4, 7182, 3);
INSERT INTO `molajo_group_permissions` VALUES(149, 3, 7183, 3);
INSERT INTO `molajo_group_permissions` VALUES(340, 4, 7183, 3);
INSERT INTO `molajo_group_permissions` VALUES(150, 3, 7184, 3);
INSERT INTO `molajo_group_permissions` VALUES(341, 4, 7184, 3);
INSERT INTO `molajo_group_permissions` VALUES(151, 3, 7185, 3);
INSERT INTO `molajo_group_permissions` VALUES(342, 4, 7185, 3);
INSERT INTO `molajo_group_permissions` VALUES(152, 3, 7186, 3);
INSERT INTO `molajo_group_permissions` VALUES(343, 4, 7186, 3);
INSERT INTO `molajo_group_permissions` VALUES(153, 3, 7187, 3);
INSERT INTO `molajo_group_permissions` VALUES(344, 4, 7187, 3);
INSERT INTO `molajo_group_permissions` VALUES(154, 3, 7188, 3);
INSERT INTO `molajo_group_permissions` VALUES(345, 4, 7188, 3);
INSERT INTO `molajo_group_permissions` VALUES(155, 3, 7189, 3);
INSERT INTO `molajo_group_permissions` VALUES(346, 4, 7189, 3);
INSERT INTO `molajo_group_permissions` VALUES(156, 3, 7190, 3);
INSERT INTO `molajo_group_permissions` VALUES(347, 4, 7190, 3);
INSERT INTO `molajo_group_permissions` VALUES(157, 3, 7191, 3);
INSERT INTO `molajo_group_permissions` VALUES(348, 4, 7191, 3);
INSERT INTO `molajo_group_permissions` VALUES(158, 3, 7192, 3);
INSERT INTO `molajo_group_permissions` VALUES(349, 4, 7192, 3);
INSERT INTO `molajo_group_permissions` VALUES(159, 3, 7193, 3);
INSERT INTO `molajo_group_permissions` VALUES(350, 4, 7193, 3);
INSERT INTO `molajo_group_permissions` VALUES(160, 3, 7194, 3);
INSERT INTO `molajo_group_permissions` VALUES(351, 4, 7194, 3);
INSERT INTO `molajo_group_permissions` VALUES(161, 3, 7195, 3);
INSERT INTO `molajo_group_permissions` VALUES(352, 4, 7195, 3);
INSERT INTO `molajo_group_permissions` VALUES(162, 3, 7196, 3);
INSERT INTO `molajo_group_permissions` VALUES(353, 4, 7196, 3);
INSERT INTO `molajo_group_permissions` VALUES(163, 3, 7197, 3);
INSERT INTO `molajo_group_permissions` VALUES(354, 4, 7197, 3);
INSERT INTO `molajo_group_permissions` VALUES(164, 3, 7198, 3);
INSERT INTO `molajo_group_permissions` VALUES(355, 4, 7198, 3);
INSERT INTO `molajo_group_permissions` VALUES(165, 3, 7199, 3);
INSERT INTO `molajo_group_permissions` VALUES(356, 4, 7199, 3);
INSERT INTO `molajo_group_permissions` VALUES(166, 3, 7200, 3);
INSERT INTO `molajo_group_permissions` VALUES(357, 4, 7200, 3);
INSERT INTO `molajo_group_permissions` VALUES(167, 3, 7201, 3);
INSERT INTO `molajo_group_permissions` VALUES(358, 4, 7201, 3);
INSERT INTO `molajo_group_permissions` VALUES(168, 3, 7202, 3);
INSERT INTO `molajo_group_permissions` VALUES(359, 4, 7202, 3);
INSERT INTO `molajo_group_permissions` VALUES(169, 3, 7203, 3);
INSERT INTO `molajo_group_permissions` VALUES(360, 4, 7203, 3);
INSERT INTO `molajo_group_permissions` VALUES(170, 3, 7204, 3);
INSERT INTO `molajo_group_permissions` VALUES(361, 4, 7204, 3);
INSERT INTO `molajo_group_permissions` VALUES(171, 3, 7205, 3);
INSERT INTO `molajo_group_permissions` VALUES(362, 4, 7205, 3);
INSERT INTO `molajo_group_permissions` VALUES(172, 3, 7227, 3);
INSERT INTO `molajo_group_permissions` VALUES(363, 4, 7227, 3);
INSERT INTO `molajo_group_permissions` VALUES(173, 3, 7228, 3);
INSERT INTO `molajo_group_permissions` VALUES(364, 4, 7228, 3);
INSERT INTO `molajo_group_permissions` VALUES(174, 3, 7229, 3);
INSERT INTO `molajo_group_permissions` VALUES(365, 4, 7229, 3);
INSERT INTO `molajo_group_permissions` VALUES(175, 3, 7230, 3);
INSERT INTO `molajo_group_permissions` VALUES(366, 4, 7230, 3);
INSERT INTO `molajo_group_permissions` VALUES(176, 3, 7234, 3);
INSERT INTO `molajo_group_permissions` VALUES(367, 4, 7234, 3);
INSERT INTO `molajo_group_permissions` VALUES(177, 3, 7235, 3);
INSERT INTO `molajo_group_permissions` VALUES(368, 4, 7235, 3);
INSERT INTO `molajo_group_permissions` VALUES(178, 3, 7236, 3);
INSERT INTO `molajo_group_permissions` VALUES(369, 4, 7236, 3);
INSERT INTO `molajo_group_permissions` VALUES(179, 3, 7237, 3);
INSERT INTO `molajo_group_permissions` VALUES(370, 4, 7237, 3);
INSERT INTO `molajo_group_permissions` VALUES(180, 3, 7238, 3);
INSERT INTO `molajo_group_permissions` VALUES(371, 4, 7238, 3);
INSERT INTO `molajo_group_permissions` VALUES(181, 3, 7239, 3);
INSERT INTO `molajo_group_permissions` VALUES(372, 4, 7239, 3);
INSERT INTO `molajo_group_permissions` VALUES(182, 3, 7240, 3);
INSERT INTO `molajo_group_permissions` VALUES(373, 4, 7240, 3);
INSERT INTO `molajo_group_permissions` VALUES(183, 3, 7241, 3);
INSERT INTO `molajo_group_permissions` VALUES(374, 4, 7241, 3);
INSERT INTO `molajo_group_permissions` VALUES(184, 3, 7242, 3);
INSERT INTO `molajo_group_permissions` VALUES(375, 4, 7242, 3);
INSERT INTO `molajo_group_permissions` VALUES(185, 3, 7243, 3);
INSERT INTO `molajo_group_permissions` VALUES(376, 4, 7243, 3);
INSERT INTO `molajo_group_permissions` VALUES(186, 3, 7244, 3);
INSERT INTO `molajo_group_permissions` VALUES(377, 4, 7244, 3);
INSERT INTO `molajo_group_permissions` VALUES(187, 3, 7245, 3);
INSERT INTO `molajo_group_permissions` VALUES(378, 4, 7245, 3);
INSERT INTO `molajo_group_permissions` VALUES(188, 3, 7246, 3);
INSERT INTO `molajo_group_permissions` VALUES(379, 4, 7246, 3);
INSERT INTO `molajo_group_permissions` VALUES(189, 3, 7247, 3);
INSERT INTO `molajo_group_permissions` VALUES(380, 4, 7247, 3);
INSERT INTO `molajo_group_permissions` VALUES(190, 3, 7248, 3);
INSERT INTO `molajo_group_permissions` VALUES(381, 4, 7248, 3);
INSERT INTO `molajo_group_permissions` VALUES(191, 3, 7249, 3);
INSERT INTO `molajo_group_permissions` VALUES(382, 4, 7249, 3);
INSERT INTO `molajo_group_permissions` VALUES(192, 3, 7250, 3);
INSERT INTO `molajo_group_permissions` VALUES(383, 4, 7250, 3);
INSERT INTO `molajo_group_permissions` VALUES(193, 3, 7251, 3);
INSERT INTO `molajo_group_permissions` VALUES(384, 4, 7251, 3);
INSERT INTO `molajo_group_permissions` VALUES(194, 3, 7252, 3);
INSERT INTO `molajo_group_permissions` VALUES(385, 4, 7252, 3);
INSERT INTO `molajo_group_permissions` VALUES(195, 3, 7253, 3);
INSERT INTO `molajo_group_permissions` VALUES(386, 4, 7253, 3);
INSERT INTO `molajo_group_permissions` VALUES(196, 3, 7254, 3);
INSERT INTO `molajo_group_permissions` VALUES(387, 4, 7254, 3);
INSERT INTO `molajo_group_permissions` VALUES(197, 3, 7255, 3);
INSERT INTO `molajo_group_permissions` VALUES(388, 4, 7255, 3);
INSERT INTO `molajo_group_permissions` VALUES(198, 3, 7256, 3);
INSERT INTO `molajo_group_permissions` VALUES(389, 4, 7256, 3);
INSERT INTO `molajo_group_permissions` VALUES(199, 3, 7257, 3);
INSERT INTO `molajo_group_permissions` VALUES(390, 4, 7257, 3);
INSERT INTO `molajo_group_permissions` VALUES(200, 3, 7258, 3);
INSERT INTO `molajo_group_permissions` VALUES(391, 4, 7258, 3);
INSERT INTO `molajo_group_permissions` VALUES(201, 3, 7259, 3);
INSERT INTO `molajo_group_permissions` VALUES(392, 4, 7259, 3);
INSERT INTO `molajo_group_permissions` VALUES(202, 3, 7260, 3);
INSERT INTO `molajo_group_permissions` VALUES(393, 4, 7260, 3);
INSERT INTO `molajo_group_permissions` VALUES(203, 3, 7261, 3);
INSERT INTO `molajo_group_permissions` VALUES(394, 4, 7261, 3);
INSERT INTO `molajo_group_permissions` VALUES(204, 3, 7262, 3);
INSERT INTO `molajo_group_permissions` VALUES(395, 4, 7262, 3);
INSERT INTO `molajo_group_permissions` VALUES(205, 3, 7263, 3);
INSERT INTO `molajo_group_permissions` VALUES(396, 4, 7263, 3);
INSERT INTO `molajo_group_permissions` VALUES(8, 1, 7267, 3);
INSERT INTO `molajo_group_permissions` VALUES(9, 1, 7268, 3);
INSERT INTO `molajo_group_permissions` VALUES(10, 1, 7270, 3);
INSERT INTO `molajo_group_permissions` VALUES(11, 1, 7271, 3);
INSERT INTO `molajo_group_permissions` VALUES(12, 1, 7272, 3);
INSERT INTO `molajo_group_permissions` VALUES(13, 1, 7273, 3);
INSERT INTO `molajo_group_permissions` VALUES(14, 1, 7274, 3);

-- --------------------------------------------------------

--
-- Table structure for table `molajo_group_view_groups`
--

CREATE TABLE `molajo_group_view_groups` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Group to Group Primary Key',
  `group_id` int(11) unsigned NOT NULL COMMENT 'FK to the molajo_group table.',
  `view_group_id` int(11) unsigned NOT NULL COMMENT 'FK to the molajo_groupings table.',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_group_to_groupings_id` (`group_id`,`view_group_id`),
  KEY `fk_molajo_group_to_groupings_molajo_groups1` (`group_id`),
  KEY `fk_molajo_group_to_groupings_molajo_groupings1` (`view_group_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13 ;

--
-- Dumping data for table `molajo_group_view_groups`
--

INSERT INTO `molajo_group_view_groups` VALUES(7, 1, 1);
INSERT INTO `molajo_group_view_groups` VALUES(8, 2, 2);
INSERT INTO `molajo_group_view_groups` VALUES(9, 3, 3);
INSERT INTO `molajo_group_view_groups` VALUES(11, 3, 5);
INSERT INTO `molajo_group_view_groups` VALUES(10, 4, 4);
INSERT INTO `molajo_group_view_groups` VALUES(12, 4, 5);

-- --------------------------------------------------------

--
-- Table structure for table `molajo_session`
--

CREATE TABLE `molajo_session` (
  `session_id` varchar(32) NOT NULL DEFAULT ' ',
  `application_id` int(11) unsigned NOT NULL DEFAULT '0',
  `guest` int(3) unsigned DEFAULT '1',
  `session_time` varchar(14) DEFAULT ' ',
  `data` longtext,
  `userid` int(11) DEFAULT '0',
  `username` varchar(150) DEFAULT ' ',
  PRIMARY KEY (`session_id`),
  KEY `whosonline` (`guest`),
  KEY `userid` (`userid`),
  KEY `time` (`session_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `molajo_session`
--


-- --------------------------------------------------------

--
-- Table structure for table `molajo_sites`
--

CREATE TABLE `molajo_sites` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Application Primary Key',
  `name` varchar(255) NOT NULL DEFAULT ' ' COMMENT 'Title',
  `path` varchar(2048) NOT NULL DEFAULT ' ' COMMENT 'URL Alias',
  `description` mediumtext,
  `parameters` mediumtext COMMENT 'Configurable Parameter Values',
  `custom_fields` mediumtext,
  `base_url` varchar(2048) NOT NULL DEFAULT ' ',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `molajo_sites`
--

INSERT INTO `molajo_sites` VALUES(1, 'Molajo', '1', 'Primary Site', '{}', '{}', '');
INSERT INTO `molajo_sites` VALUES(2, 'Molajo Site 2', '2', 'Second Site', '{}', '{}', 'molajo2');

-- --------------------------------------------------------

--
-- Table structure for table `molajo_site_applications`
--

CREATE TABLE `molajo_site_applications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) NOT NULL,
  `application_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `molajo_site_applications`
--

INSERT INTO `molajo_site_applications` VALUES(1, 1, 1);
INSERT INTO `molajo_site_applications` VALUES(2, 1, 2);
INSERT INTO `molajo_site_applications` VALUES(3, 1, 3);
INSERT INTO `molajo_site_applications` VALUES(4, 2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `molajo_source_tables`
--

CREATE TABLE `molajo_source_tables` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Actions Primary Key',
  `source_table` varchar(255) NOT NULL DEFAULT ' ',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_source_table_title` (`source_table`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `molajo_source_tables`
--

INSERT INTO `molajo_source_tables` VALUES(1, '__applications');
INSERT INTO `molajo_source_tables` VALUES(2, '__categories');
INSERT INTO `molajo_source_tables` VALUES(3, '__content');
INSERT INTO `molajo_source_tables` VALUES(4, '__extension_instances');
INSERT INTO `molajo_source_tables` VALUES(6, '__groups');
INSERT INTO `molajo_source_tables` VALUES(5, '__users');

-- --------------------------------------------------------

--
-- Table structure for table `molajo_update_sites`
--

CREATE TABLE `molajo_update_sites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT ' ',
  `type` varchar(20) DEFAULT ' ',
  `location` text NOT NULL,
  `enabled` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `molajo_update_sites`
--

INSERT INTO `molajo_update_sites` VALUES(1, 'Molajo Core', 'collection', 'http://update.molajo.org/core/list.xml', 1);
INSERT INTO `molajo_update_sites` VALUES(2, 'Molajo Directory', 'collection', 'http://update.molajo.org/directory/list.xml', 1);

-- --------------------------------------------------------

--
-- Table structure for table `molajo_users`
--

CREATE TABLE `molajo_users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL DEFAULT '  ',
  `full_name` varchar(255) NOT NULL DEFAULT '  ',
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `content_text` mediumtext,
  `email` varchar(255) NOT NULL DEFAULT '  ',
  `password` varchar(100) NOT NULL DEFAULT '  ',
  `block` int(3) NOT NULL DEFAULT '0',
  `activated` int(3) DEFAULT NULL,
  `send_email` int(3) DEFAULT '0',
  `register_datetime` datetime DEFAULT NULL,
  `last_visit_datetime` datetime DEFAULT NULL,
  `parameters` mediumtext COMMENT 'Configurable Parameter Values',
  `custom_fields` mediumtext,
  `asset_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'FK to the molajo_assets table.',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_asset_table_id_join` (`asset_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=101 ;

--
-- Dumping data for table `molajo_users`
--

INSERT INTO `molajo_users` VALUES(42, 'admin', 'Administrator', '', '', '', 'admin@example.com', 'admin', 0, 1, 0, '2011-11-01 00:00:00', '0000-00-00 00:00:00', NULL, '', 2001);
INSERT INTO `molajo_users` VALUES(100, 'mark', 'Mark Robinson', 'Mark', 'Robinson', '<p>Great guy who sells insurance and coaches Little League.</p>', 'mark.robinson@example.com', 'mark', 0, 1, 0, '2011-11-02 17:45:17', '0000-00-00 00:00:00', NULL, '{"favorite_color":"red","nickname":"Fred","claim_to_fame":"No search results for Mark on Google."}', 2000);

-- --------------------------------------------------------

--
-- Table structure for table `molajo_user_applications`
--

CREATE TABLE `molajo_user_applications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL COMMENT 'Foreign Key to molajo_users.id',
  `application_id` int(11) unsigned NOT NULL COMMENT 'Foreign Key to molajo_applications.id',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `fk_molajo_user_applications_molajo_users1` (`application_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `molajo_user_applications`
--

INSERT INTO `molajo_user_applications` VALUES(1, 42, 1);
INSERT INTO `molajo_user_applications` VALUES(2, 42, 2);
INSERT INTO `molajo_user_applications` VALUES(3, 42, 3);
INSERT INTO `molajo_user_applications` VALUES(4, 100, 1);
INSERT INTO `molajo_user_applications` VALUES(5, 100, 3);

-- --------------------------------------------------------

--
-- Table structure for table `molajo_user_groups`
--

CREATE TABLE `molajo_user_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL COMMENT 'Foreign Key to molajo_users.id',
  `group_id` int(11) unsigned NOT NULL COMMENT 'Foreign Key to molajo_groups.id',
  PRIMARY KEY (`id`),
  KEY `fk_molajo_user_groups_molajo_users1` (`user_id`),
  KEY `fk_molajo_user_groups_molajo_groups1` (`group_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

--
-- Dumping data for table `molajo_user_groups`
--

INSERT INTO `molajo_user_groups` VALUES(3, 42, 3);
INSERT INTO `molajo_user_groups` VALUES(4, 42, 4);
INSERT INTO `molajo_user_groups` VALUES(8, 100, 3);

-- --------------------------------------------------------

--
-- Table structure for table `molajo_user_view_groups`
--

CREATE TABLE `molajo_user_view_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL COMMENT 'Foreign Key to molajo_users.id',
  `view_group_id` int(11) unsigned NOT NULL COMMENT 'Foreign Key to molajo_groupings.id',
  PRIMARY KEY (`id`,`user_id`,`view_group_id`),
  KEY `fk_molajo_user_groupings_molajo_users1` (`user_id`),
  KEY `fk_molajo_user_groupings_molajo_groupings1` (`view_group_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `molajo_user_view_groups`
--

INSERT INTO `molajo_user_view_groups` VALUES(1, 42, 3);
INSERT INTO `molajo_user_view_groups` VALUES(2, 42, 4);
INSERT INTO `molajo_user_view_groups` VALUES(3, 100, 3);

-- --------------------------------------------------------

--
-- Table structure for table `molajo_view_groups`
--

CREATE TABLE `molajo_view_groups` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Groupings Primary Key',
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
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Permissions Primary Key',
  `view_group_id` int(11) unsigned NOT NULL COMMENT 'Foreign Key to molajo_groups.id',
  `asset_id` int(11) unsigned NOT NULL COMMENT 'Foreign Key to molajo_assets.id',
  `action_id` int(11) unsigned NOT NULL COMMENT 'Foreign Key to molajo_actions.id',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_asset_action_to_group_lookup` (`asset_id`,`action_id`,`view_group_id`),
  UNIQUE KEY `idx_group_to_asset_action_lookup` (`view_group_id`,`asset_id`,`action_id`),
  KEY `fk_molajo_permissions_groupings_molajo_groupings1` (`view_group_id`),
  KEY `fk_molajo_permissions_groupings_molajo_assets1` (`asset_id`),
  KEY `fk_molajo_permissions_groupings_molajo_actions1` (`action_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=257 ;

--
-- Dumping data for table `molajo_view_group_permissions`
--

INSERT INTO `molajo_view_group_permissions` VALUES(1, 1, 7011, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(2, 1, 7012, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(3, 1, 7013, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(4, 1, 7014, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(5, 1, 7018, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(6, 1, 7019, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(7, 1, 7020, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(8, 5, 7021, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(9, 5, 7022, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(10, 5, 7023, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(11, 5, 7024, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(12, 5, 7025, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(13, 5, 7026, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(14, 5, 7027, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(15, 5, 7028, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(16, 5, 7029, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(17, 5, 7030, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(18, 5, 7031, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(19, 5, 7032, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(20, 5, 7033, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(21, 5, 7034, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(22, 5, 7035, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(23, 5, 7036, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(24, 5, 7037, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(25, 5, 7038, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(26, 5, 7052, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(27, 5, 7053, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(28, 5, 7055, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(29, 5, 7056, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(30, 5, 7057, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(31, 5, 7058, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(32, 5, 7059, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(33, 5, 7060, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(34, 5, 7061, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(35, 5, 7062, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(36, 5, 7063, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(37, 5, 7064, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(38, 5, 7065, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(39, 5, 7066, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(40, 5, 7067, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(41, 5, 7068, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(42, 5, 7069, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(43, 5, 7070, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(44, 5, 7071, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(45, 5, 7072, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(46, 5, 7073, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(47, 5, 7074, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(48, 5, 7075, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(49, 5, 7076, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(50, 5, 7077, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(51, 5, 7078, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(52, 5, 7079, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(53, 5, 7080, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(54, 5, 7081, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(55, 5, 7082, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(56, 5, 7083, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(57, 5, 7084, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(58, 5, 7085, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(59, 5, 7086, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(60, 5, 7087, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(61, 5, 7088, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(62, 5, 7089, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(63, 5, 7090, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(64, 5, 7091, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(65, 5, 7092, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(66, 5, 7093, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(67, 5, 7094, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(68, 5, 7095, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(69, 5, 7096, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(70, 5, 7097, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(71, 5, 7098, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(72, 5, 7099, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(73, 5, 7100, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(74, 5, 7101, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(75, 5, 7102, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(76, 5, 7103, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(77, 5, 7104, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(78, 5, 7105, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(79, 5, 7106, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(80, 5, 7107, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(81, 5, 7108, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(82, 5, 7109, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(83, 5, 7110, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(84, 5, 7111, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(85, 5, 7118, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(86, 5, 7119, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(87, 5, 7120, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(88, 5, 7121, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(89, 5, 7122, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(90, 5, 7123, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(91, 5, 7124, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(92, 5, 7125, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(93, 5, 7133, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(94, 5, 7134, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(95, 5, 7135, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(96, 5, 7136, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(97, 5, 7137, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(98, 5, 7138, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(99, 5, 7139, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(100, 5, 7140, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(101, 5, 7141, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(102, 5, 7142, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(103, 5, 7143, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(104, 5, 7144, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(105, 5, 7145, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(106, 5, 7146, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(107, 5, 7147, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(108, 5, 7148, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(109, 5, 7149, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(110, 5, 7150, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(111, 5, 7151, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(112, 5, 7152, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(113, 5, 7153, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(114, 5, 7154, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(115, 5, 7155, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(116, 5, 7156, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(117, 5, 7157, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(118, 5, 7158, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(119, 5, 7159, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(120, 5, 7160, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(121, 5, 7161, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(122, 5, 7162, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(123, 5, 7164, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(124, 5, 7165, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(125, 5, 7166, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(126, 5, 7167, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(127, 5, 7168, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(128, 5, 7169, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(129, 5, 7170, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(130, 5, 7171, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(131, 5, 7172, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(132, 5, 7173, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(133, 5, 7174, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(134, 5, 7175, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(135, 5, 7176, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(136, 5, 7177, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(137, 5, 7178, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(138, 5, 7179, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(139, 5, 7180, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(140, 5, 7181, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(141, 5, 7182, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(142, 5, 7183, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(143, 5, 7184, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(144, 5, 7185, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(145, 5, 7186, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(146, 5, 7187, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(147, 5, 7188, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(148, 5, 7189, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(149, 5, 7190, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(150, 5, 7191, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(151, 5, 7192, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(152, 5, 7193, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(153, 5, 7194, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(154, 5, 7195, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(155, 5, 7196, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(156, 5, 7197, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(157, 5, 7198, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(158, 5, 7199, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(159, 5, 7200, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(160, 5, 7201, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(161, 5, 7202, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(162, 5, 7203, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(163, 5, 7204, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(164, 5, 7205, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(165, 5, 7227, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(166, 5, 7228, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(167, 5, 7229, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(168, 5, 7230, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(169, 5, 7234, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(170, 5, 7235, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(171, 5, 7236, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(172, 5, 7237, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(173, 5, 7238, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(174, 5, 7239, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(175, 5, 7240, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(176, 5, 7241, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(177, 5, 7242, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(178, 5, 7243, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(179, 5, 7244, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(180, 5, 7245, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(181, 5, 7246, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(182, 5, 7247, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(183, 5, 7248, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(184, 5, 7249, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(185, 5, 7250, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(186, 5, 7251, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(187, 5, 7252, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(188, 5, 7253, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(189, 5, 7254, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(190, 5, 7255, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(191, 5, 7256, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(192, 5, 7257, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(193, 5, 7258, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(194, 5, 7259, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(195, 5, 7260, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(196, 5, 7261, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(197, 5, 7262, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(198, 5, 7263, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(199, 1, 7267, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(200, 1, 7268, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(201, 1, 7270, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(202, 1, 7271, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(203, 1, 7272, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(204, 1, 7273, 3);
INSERT INTO `molajo_view_group_permissions` VALUES(205, 1, 7274, 3);
