<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Extension\Plugin\Language;

use Molajo\Extension\Plugin\Content\ContentPlugin;

defined('MOLAJO') or die;

/**
 * Parameters
 *
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class LanguagePlugin extends ContentPlugin
{

    /**
     * Pre-create processing
     *
     * @param   $this->data
     * @param   $model
     *
     * @return boolean
     * @since   1.0
     */
    public function onBeforeCreate()
    {
        return true;
    }

    /**
     * Pre-update processing
     *
     * @param   $this->data
     * @param   $model
     *
     * @return boolean
     * @since   1.0
     */
    public function onBeforeUpdate()
    {
        return true;
    }

    /**
     * Pre-delete processing
     *
     * @param   $this->data
     * @param   $model
     *
     * @return boolean
     * @since   1.0
     */
    public function onBeforeDelete()
    {
        return true;
    }
}
