<?php
/**
 * @package     Molajo
 * @subpackage  Other
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/** Additional Libraries (Add as configuration options later) */
$filehelper->requireClassFile(LIBRARIES.'/akismet/Akismet.class.php', 'Akismet');
$filehelper->requireClassFile(LIBRARIES.'/mollom/mollom.php', 'Mollom');
require_once LIBRARIES.'/recaptcha/recaptchalib.php';
$filehelper->requireClassFile(LIBRARIES.'/securimage/securimage.php', 'Securimage');
$filehelper->requireClassFile(LIBRARIES.'/wideimage/WideImage.php', 'WideImage');
