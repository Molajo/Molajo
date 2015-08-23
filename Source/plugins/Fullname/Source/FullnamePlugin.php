<?php
/**
 * Fullname Plugin
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Fullname;

use Molajo\Plugins\ReadEvent;
use CommonApi\Event\ReadEventInterface;

/**
 * Fullname Plugin
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
final class FullnamePlugin extends ReadEvent implements ReadEventInterface
{
    /**
     * Creates Full Name field using both first and last
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onAfterReadRow()
    {
        if ($this->checkProcessPlugin() === false) {
            return $this;
        }

        $this->setFullname();

        return $this;
    }

    /**
     * Should plugin be executed?
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function checkProcessPlugin()
    {
        if (isset($this->controller['row']->first_name)
            && isset($this->controller['row']->last_name)
        ) {
        } else {
            return false;
        }

        if (isset($this->controller['row']->full_name)) {
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
        $field           = $this->controller['model_registry']['fields']['last_name'];
        $field['type']   = 'string';
        $field['locked'] = 1;
        $field['value']  = $this->controller['row']->first_name . ' ' . $this->controller['row']->last_name;
        $field['name']   = 'full_name';
        $field['source'] = 'fields';

        $this->setField($field['name'], $field['value'], $field);

        return $this;
    }
}
