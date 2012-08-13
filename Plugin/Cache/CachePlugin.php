<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Cache;

use Molajo\Plugin\Content\ContentPlugin;

defined('MOLAJO') or die;

/**
 * Cache
 *
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class CachePlugin extends ContentPlugin
{
    /**
     * After-create processing
     *
     * @return boolean
     * @since   1.0
     */
    public function onAfterCreate()
    {
        return true;
    }
}
