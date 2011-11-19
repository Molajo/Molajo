<?php
/**
 * @package     Molajo
 * @subpackage  Installation Check
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

if (defined('MOLAJO_INSTALL_CHECK')) { } else { define('MOLAJO_INSTALL_CHECK', false); }

if (MOLAJO_APPLICATION == 'installation'
    || (MOLAJO_INSTALL_CHECK === false
            && file_exists(MOLAJO_SITE_PATH.'/configuration.php')) ) {

} else {
    if (!file_exists(MOLAJO_SITE_PATH.'/configuration.php')
        || filesize(MOLAJO_SITE_PATH.'/configuration.php' < 10)
        || file_exists(MOLAJO_BASE_FOLDER.'/installation/index.php')) {

        $redirect = MOLAJO_BASE_URL.'/installation/';
        header('Location: '.$redirect);
        exit();
    }
}