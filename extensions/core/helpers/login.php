<?php
/**
 * @package     Molajo
 * @subpackage  Helper
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * MolajoLoginHelper
 *
 * @package     Molajo
 * @subpackage  Login Helper
 * @since       1.0
 */
class MolajoLoginHelper
{
    /**
     * getLanguageList
     *
     * Get an HTML select list of the available languages.
     *
     * @return    string
     */
    public static function getLanguageList()
    {
        $languages = array();
        $languages = MolajoLanguageHelper::createLanguageList(null, MOLAJO_BASE_FOLDER, false, true);
        array_unshift($languages, MolajoHTML::_('select.option', '', MolajoTextHelper::_('JDEFAULT')));
        return MolajoHTML::_('select.genericlist', $languages, 'language', ' class="inputbox"', 'value', 'text', null);
    }

    /**
     * getReturnURI
     *
     * Get the redirect URI after login.
     *
     * @return    string
     */
    public static function getReturnURI()
    {
        $uri = MolajoController::getURI();
        $return = 'index.php' . $uri->toString(array('query'));
        if ($return == 'index.php?option=login') {
            return base64_encode('index.php');
        } else {
            return base64_encode($return);
        }
    }
}