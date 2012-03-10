<?php
/**
 * @package     Molajo
 * @subpackage  Theme
 * @copyright   Copyright (C) 2012 Cristina Solana. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
namespace Molajo\Application\Helper;
namespace Molajo\Theme\Helper;

defined('MOLAJO') or die;

/**
 * Mustache
 *
 * @package     Molajo
 * @subpackage  Helper
 * @since       1.0
 */
Class ThemeCleanSlateHelper extends ThemeHelper
{
    /**
     * hello
     *
     * Accesses User Object and returns a text message
     *
     * @results  object
     * @since    1.0
     */
    public function hello()
    {
        return 'Hello '.Services::User()->get('name').'!';
    }
}
