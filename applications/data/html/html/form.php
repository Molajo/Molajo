<?php
/**
 * @package     Joomla.Platform
 * @subpackage  HTML
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('MOLAJO') or die;

/**
 * Utility class for form elements
 *
 * @package     Joomla.Platform
 * @subpackage  HTML
 * @since       11.1
 */
abstract class MolajoHtmlForm
{
    /**
     * Displays a hidden token field to reduce the risk of CSRF exploits
     *
     * Use in conjuction with JRequest::checkToken
     *
     * @return  void
     *
     * @see     JRequest::checkToken
     * @since   1.0
     */
    public static function token()
    {
        return '<input type="hidden" name="' . JUtility::getToken() . '" value="1" />';
    }
}
