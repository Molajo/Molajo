<?php
/**
 * @package     Molajo
 * @subpackage  Installation Check
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 *  Installation Check
 */
define('INSTALL_CHECK', false);
if (MOLAJO_APPLICATION == 'installation'
    || (INSTALL_CHECK === false
            && file_exists(MOLAJO_SITE.'/configuration.php')) ) {

} else {
    if (!file_exists(MOLAJO_SITE.'/configuration.php')
        || filesize(MOLAJO_SITE.'/configuration.php' < 10)
        || file_exists(MOLAJO_SITE_INSTALLATION.'/index.php')) {

        if (MOLAJO_APPLICATION == 'site') {
            $redirect = substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'],'index.php')).'installation/index.php';
        } else {
            $redirect = '../installation/index.php';
        }
        header('Location: '.$redirect);
        exit();
    }
}