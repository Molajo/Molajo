<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Extension\Plugin\Catalogtypeid;

use Molajo\Extension\Plugin\Content\ContentPlugin;

defined('MOLAJO') or die;

/**
 * CatalogtypeId
 *
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class CatalogtypeidPlugin extends ContentPlugin
{
    /**
     * After-read processing
     *
     * @return boolean
     * @since   1.0
     */
    public function onAfterRead()
    {
        return true;
    }
}
