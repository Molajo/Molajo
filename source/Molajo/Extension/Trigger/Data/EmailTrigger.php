<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Model\Trigger;

defined('MOLAJO') or die;

/**
 * Email
 *
 * @package     Molajo
 * @subpackage  Trigger
 * @since       1.0
 */
class EmailTrigger extends Trigger
{
    /**
     * __construct
     *
     * Constructor.
     *
     * @since  1.0
     */
    public function __construct()
    {

    }

    /**
     * Pre-create processing
     *
     * @param   $data
     * @param   $model
     *
     * @return  $data
     * @since   1.0
     */
    protected function onBeforeCreate($data, $model)
    {
        return $data;
    }

    /**
     * Post-create processing
     *
     * @param $data, $model
     *
     * @return  $data
     * @since   1.0
     */
    protected function onAfterCreate($data, $model)
    {
        return $data;
    }

    /**
     * Pre-read processing
     *
     * @param   $data
     * @param   $model
     *
     * @return  $data
     * @since   1.0
     */
    protected function onBeforeRead($data, $model)
    {
        return $data;
    }

    /**
     * Post-read processing
     *
     * @param   $data
     * @param   $model
     *
     * @return  $data
     * @since   1.0
     */
    protected function onAfterRead($data, $model)
    {
        return $data;
    }

    /**
     * Pre-update processing
     *
     * @param   $data
     * @param   $model
     *
     * @return  $data
     * @since   1.0
     */
    protected function onBeforeUpdate($data, $model)
    {
        return $data;
    }

    /**
     * Post-update processing
     *
     * @param   $data
     * @param   $model
     *
     * @return  $data
     * @since   1.0
     */
    protected function onAfterUpdate($data, $model)
    {
        return $data;
    }

    /**
     * Pre-delete processing
     *
     * @param   $data
     * @param   $model
     *
     * @return  $data
     * @since   1.0
     */
    protected function onBeforeDelete($data, $model)
    {
        return $data;
    }

    /**
     * Post-read processing
     *
     * @param   $data
     * @param   $model
     *
     * @return  $data
     * @since   1.0
     */
    protected function onAfterDelete($data, $model)
    {
        return $data;
    }
}
