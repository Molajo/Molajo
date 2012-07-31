<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Extension\Trigger\Customfields;

use Molajo\Extension\Trigger\Content\ContentTrigger;

defined('MOLAJO') or die;

/**
 * Custom Fields
 *
 * @package     Molajo
 * @subpackage  Trigger
 * @since       1.0
 */
class CustomFieldsTrigger extends ContentTrigger
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
     * Post-read processing
     *
     * @param   $this->data
     * @param   $model
     *
     * @return boolean
     * @since   1.0
     */
    public function onAfterRead()
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
     * Post-update processing
     *
     * @param   $this->data
     * @param   $model
     *
     * @return boolean
     * @since   1.0
     */
    public function onAfterUpdate()
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

    /**
     * Post-read processing
     *
     * @param   $this->data
     * @param   $model
     *
     * @return boolean
     * @since   1.0
     */
    public function onAfterDelete()
    {
        return true;
    }
}
