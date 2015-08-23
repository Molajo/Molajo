<?php
/**
 * Status Plugin
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Status;

use CommonApi\Event\ReadEventInterface;
use Molajo\Plugins\ReadEvent;

/**
 * Status Plugin
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
final class StatusPlugin extends ReadEvent implements ReadEventInterface
{
    /**
     * Provides Text for Status ID
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onAfterReadRow()
    {
        if ($this->existFields('status') === false) {
            return $this;
        }

        $this->processFieldsByType('processStatus', $this->hold_fields);

        return $this;
    }

    /**
     * Process Status
     *
     * @param   array $field
     *
     * @return  array
     * @since   1.0.0
     */
    protected function processStatus(array $field = array())
    {
        $hold_field = $field;
        $name       = $field['name'];
        $status     = $this->getFieldValue($field);

        $status_array = array(
            '2'   => 'Archived',
            '1'   => 'Published',
            '-1'  => 'Trashed',
            '-2'  => 'Spammed',
            '-5'  => 'Draft',
            '-10' => 'Version',
            '0'   => 'Unpublished'
        );

        $value = $this->language->translateString($status_array[$status]);

        $field['type']       = 'string';
        $field['calculated'] = 1;
        $this->setField($name . '_name', $value, $field);

        return $hold_field;
    }
}
