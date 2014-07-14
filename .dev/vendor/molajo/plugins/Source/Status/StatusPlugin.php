<?php
/**
 * Status Plugin
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Status;

use CommonApi\Event\ReadInterface;
use Molajo\Plugins\ReadEventPlugin;

/**
 * Status Plugin
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
class StatusPlugin extends ReadEventPlugin implements ReadInterface
{
    /**
     * Provides Text for Status ID
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onAfterRead()
    {
        if ($this->countGetFields('status') === false) {
            return $this;
        }

        return $this->setStatus();
    }

    /**
     * Provides Text for Status ID
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setStatus()
    {
        return $this->processFieldsByType($this->hold_fields, 'processStatus');
    }

    /**
     * Process Smilies
     *
     * @param   object $field
     *
     * @return  object
     * @since   1.0.0
     */
    protected function processStatus($field)
    {
        $name   = $field['name'];
        $status = $this->getFieldValue($field);

        $status_array = array(
            '2'   => 'Archived',
            '1'   => 'Published',
            '-1'  => 'Trashed',
            '-2'  => 'Spammed',
            '-5'  => 'Draft',
            '-10' => 'Version',
            '0'   => 'Unpublished'
        );

        $value = $this->language_controller->translateString($status_array[$status]);

        $this->setField($field, $name . '_name', $value);

        return $field;
    }
}
