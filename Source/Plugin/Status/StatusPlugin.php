<?php
/**
 * Status Plugin
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugin\Status;

use CommonApi\Event\ReadInterface;
use Molajo\Plugin\ReadEventPlugin;

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
     * @since   1.0
     */
    public function onAfterRead()
    {
        $fields = $this->getFieldsByType('status');

        if (is_array($fields) && count($fields) > 0) {

            foreach ($fields as $field) {

                $name = $field['name'];

                $status = $this->getFieldValue($field);

                if ($status == '2') {
                    $status_name = $this->language_controller->translate('Archived');
                } elseif ($status == '1') {
                    $status_name = $this->language_controller->translate('Published');
                } elseif ($status == '-1') {
                    $status_name = $this->language_controller->translate('Trashed');
                } elseif ($status == '-2') {
                    $status_name = $this->language_controller->translate('Spammed');
                } elseif ($status == '-5') {
                    $status_name = $this->language_controller->translate('Draft');
                } elseif ($status == '-10') {
                    $status_name = $this->language_controller->translate('Version');
                } else {
                    $status_name = $this->language_controller->translate('Unpublished');
                }

                $this->setField($field, $name . '_name', $status_name);
            }
        }

        return $this;
    }
}
