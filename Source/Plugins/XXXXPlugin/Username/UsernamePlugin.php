<?php
/**
 * Username Plugin
 *
 * Runs on before Create, Update and After Update to handle pre and post
 * update processing for the Username Field.
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajo\Plugins\Username;

use CommonApi\Event\UpdateInterface;
use Molajo\Plugins\UpdateEventPlugin;

/**
 * Username Plugin
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0
 */
class UsernamePlugin extends UpdateEventPlugin implements UpdateInterface
{
    /**
     * Pre-create processing
     *
     * @return  $this
     * @since   1.0
     */
    public function onBeforeCreate()
    {
        return $this;
    }

    /**
     * Pre-update processing
     *
     * @return  $this
     * @since   1.0
     */
    public function onBeforeUpdate()
    {
        return $this;
    }

    /**
     * Post-update processing
     *
     * @return  $this
     * @since   1.0
     */
    public function onAfterUpdate()
    {
        return $this;
    }
}
