<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Extension\Plugin\Collection;

use Molajo\Extension\Plugin\Content\ContentPlugin;

defined('MOLAJO') or die;

/**
 * Full name
 *
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class CollectionPlugin extends ContentPlugin
{

    /**
     * Adds full_name to recordset containing first_name and last_name
     *
     * @return boolean
     * @since   1.0
     */
    public function onAfterRead()
    {
        return true;
    }
}
