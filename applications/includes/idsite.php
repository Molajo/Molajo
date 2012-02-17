<?php
/**
 * @package     Molajo
 * @subpackage  Defines
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/*                                              */
/*  Identify the Site                           */
/*                                              */
if (defined('SITES_MEDIA_FOLDER')) {
} else {
    define('SITES_MEDIA_FOLDER', SITES . '/media');
}
if (defined('SITES_MEDIA_URL')) {
} else {
    define('SITES_MEDIA_URL', MOLAJO_BASE_URL . 'sites/media');
}
if (defined('SITES_TEMP_FOLDER')) {
} else {
    define('SITES_TEMP_FOLDER', SITES . '/temp');
}
if (defined('SITES_TEMP_URL')) {
} else {
    define('SITES_TEMP_URL', MOLAJO_BASE_URL . 'sites/temp');
}

$siteBase = substr(MOLAJO_BASE_URL, strlen(MOLAJO_PROTOCOL), 999);
if (defined('SITE_BASE_URL')) {
} else {
    $sites = simplexml_load_file(SITES . '/sites.xml', 'SimpleXMLElement');
    foreach ($sites->site as $single) {
        if ($single->base == $siteBase) {
            define('SITE_BASE_URL', $single->base);
            define('SITE_FOLDER_PATH', $single->folderpath);
            define('SITE_APPEND_TO_BASE_URL', $single->appendtobaseurl);
            define('SITE_ID', $single->id);
            break;
        }
    }
    if (defined('SITE_BASE_URL')) {
    } else {
        echo 'Fatal Error: Cannot identify site for: ' . $siteBase;
        die;
    }
}
