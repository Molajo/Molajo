<?php
/**
 * Username Plugin
 *
 * @package      Niambie
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\Plugin\Username;

use Molajo\Plugin\Plugin\Plugin;

defined('NIAMBIE') or die;

/**
 * Username Plugin that runs on before Create, Update and After Update to handle pre and post
 * update processing for the Username Field.
 *
 * @author       Amy Stephen
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 * @since        1.0
 */
class UsernamePlugin extends Plugin
{
    /**
     * Pre-create processing
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
     * @return boolean
     * @since   1.0
     */
    public function onAfterUpdate()
    {
        return true;
    }
}
