<?php
/**
 * Cache Service Plugin
 *
 * @package      Niambie
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\Service;

use Molajo\Service\Services;

defined('NIAMBIE') or die;

/**
 * Services Plugin
 *
 * @author       Amy Stephen
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 * @since        1.0
 */
Class ServicesPlugin
{
    /**
     * Services Class Instance
     *
     * @var    array
     * @since  1.0
     */
    protected $service_class = null;

    /**
     * Get Value for Specified Key
     *
     * @return  void
     * @since   1.0
     */
    public function get($key, $default)
    {
        if ($this->$key === null) {
            $this->$key = $default;
        }

        return $this->$key;
    }

    /**
     * Set Value for Specified Key
     *
     * @return  void
     * @since   1.0
     */
    public function set($key, $value)
    {
        $this->$key = $value;

        return $this->$key;
    }
}
