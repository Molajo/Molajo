<?php
/**
 * @package   Molajo
 * @subpackage  Theme
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
namespace Molajo\Theme;
defined('MOLAJO') or die;
use Molajo\Extension\Helper\MustacheHelper;

/**
 * Helper
 *
 * @package   Molajo
 * @subpackage  Helper
 * @since       1.0
 */
Class BootstrapHelper extends MustacheHelper
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
        return 'Hello ' . Service::User()->get('name') . '!';
    }
}
