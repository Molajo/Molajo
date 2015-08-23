<?php
/**
 * Configuration Lists Class
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Pagetypeconfiguration;

use CommonApi\Event\DisplayEventInterface;

/**
 * Configuration Lists Class
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
abstract class Lists extends GridMenuitem implements DisplayEventInterface
{
    /**
     * View Types
     *
     * @var    array
     * @since  1.0.0
     */
    protected $view_types
        = array(
            'form',
            'item',
            'list'
        );

    /**
     * Group Types
     *
     * @var    array
     * @since  1.0.0
     */
    protected $group_types
        = array(
            'access_create_groups',
            'access_read_groups',
            'access_update_groups',
            'access_delete_groups',
            'access_publish_groups',
            'access_administer_groups'
        );

    /**
     * Set Permission Groups
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setPermissionGroups()
    {
        foreach ($this->group_types as $view_type) {

            $source      = 'datalist_groups';
            $destination = 'datalist_' . $view_type;

            $this->cloneList($source, $destination, $view_type, 1, 5);
        }

        return $this;
    }

    /**
     * Set View Groups
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setViews()
    {
        foreach ($this->view_types as $type) {
            $this->setViewType($type, 'themes');
            $this->setViewType($type, 'pageviews');
        }

        return $this;
    }

    /**
     * Set View Groups
     *
     * @param   string  $type
     * @param   string  $view_type
     * @param   integer $multiple
     * @param   integer $size
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setViewType($type, $view_type, $multiple = 0, $size = 10)
    {
        $source      = 'datalist_' . $view_type;
        $destination = 'datalist_' . $view_type . '_' . $type;
        $field_name  = $view_type . '_' . $type;

        $this->cloneList($source, $destination, $field_name, $multiple, $size);

        return $this;
    }
}
