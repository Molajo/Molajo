<?php
/**
 * Fullname Plugin
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Fullname;

use Molajo\Plugins\ReadEventPlugin;
use CommonApi\Event\ReadInterface;

/**
 * Fullname Plugin
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
class FullnamePlugin extends ReadEventPlugin implements ReadInterface
{
    /**
     * Creates Full Name field using both first and last
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onAfterRead()
    {
        if ($this->processFullnamePlugin() === false) {
            return $this;
        }

        return $this->setFullname();
    }

    /**
     * Should plugin be executed?
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function processFullnamePlugin()
    {
        if (isset($this->row->first_name)
            && isset($this->row->last_name)
        ) {
        } else {
            return false;
        }

        if (isset($this->row->full_name)) {
            return false;
        }

        return true;
    }

    /**
     * Adds full_name to recordset containing first_name and last_name
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setFullname()
    {
        $this->setField(null, 'full_name', $this->row->first_name . ' ' . $this->row->last_name);

        return $this;
    }
}
