<?php
/**
 * @version     $id: other.php
 * @package     Molajo
 * @subpackage  Define other libraries
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die();

/** file helper */
if (class_exists('MolajoFileHelper')) {
} else {
    if (file_exists(MOLAJO_LIBRARY.'/helpers/file.php')) {
        JLoader::register('MolajoFileHelper', MOLAJO_LIBRARY.'/helpers/file.php');
    } else {
        JError::raiseNotice(500, JText::_('MOLAJO_OVERRIDE_CREATE_MISSING_CLASS_FILE'.' '.'MolajoFileHelper'));
        return;
    }
}
$filehelper = new MolajoFileHelper();

/** Additional Libraries */
$filehelper->requireClassFile(JPATH_ROOT.'/libraries/akismet/Akismet.class.php', 'Akismet');
$filehelper->requireClassFile(JPATH_ROOT.'/libraries/curl/curl.php', 'curl');
$filehelper->requireClassFile(JPATH_ROOT.'/libraries/mollom/mollom.php', 'Mollom');
require_once JPATH_ROOT.'/libraries/recaptcha/recaptchalib.php';
$filehelper->requireClassFile(JPATH_ROOT.'/libraries/securimage/securimage.php', 'Securimage');
$filehelper->requireClassFile(JPATH_ROOT.'/libraries/wideimage/WideImage.php', 'WideImage');