<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Model\Trigger;

defined('MOLAJO') or die;

/**
 * Trigger
 *
 * Base class for Model Triggers
 *
 * @package     Molajo
 * @subpackage  Model
 * @since       1.0
 */
class Trigger
{
    /**
     * __construct
     *
     * Constructor.
     *
     * @param  $id
     * @since  1.0
     */
    public function __construct()
    {

    }

    /**
     * Pre-create processing
     *
     * @param $data
     *
     * @return  $data
     * @since   1.0
     */
    protected function onBeforeCreate($data)
    {
        return $data;
    }

    /**
     * Post-create processing
     *
     * @param $data
     *
     * @return  $data
     * @since   1.0
     */
    protected function onAfterCreate($data)
    {
        return $data;
    }

    /**
     * Pre-read processing
     *
     * @param $data
     *
     * @return  $data
     * @since   1.0
     */
    protected function onBeforeRead($data)
    {
        return $data;
    }

    /**
     * Post-read processing
     *
     * @param $data
     *
     * @return  $data
     * @since   1.0
     */
    protected function onAfterRead($data)
    {
        return $data;
    }

    /**
     * Pre-update processing
     *
     * @param $data
     *
     * @return  $data
     * @since   1.0
     */
    protected function onBeforeUpdate($data)
    {
        return $data;
    }

    /**
     * Post-update processing
     *
     * @param $data
     *
     * @return  $data
     * @since   1.0
     */
    protected function onAfterUpdate($data)
    {
        return $data;
    }

    /**
     * Pre-delete processing
     *
     * @param $data
     *
     * @return  $data
     * @since   1.0
     */
    protected function onBeforeDelete($data)
    {
        return $data;
    }

    /**
     * Post-delete processing
     *
     * @param $data
     *
     * @return  $data
     * @since   1.0
     */
    protected function onAfterDelete($data)
    {
        return $data;
    }
}
