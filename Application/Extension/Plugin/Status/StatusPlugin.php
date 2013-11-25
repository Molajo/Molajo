<?php
/**
 * Status Plugin
 *
 * @package    Molajo
 * @copyright  2013 Amy Stephen. All rights reserved.
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
        if (isset($this->runtime_data->render->token)
            && $this->runtime_data->render->token->type == 'template'
            && strtolower($this->runtime_data->render->token->name) == 'status'
        ) {
        } else {
            return $this;
        }

        $statusField = $this->getField('status');

        if ($statusField === null) {
            return $this;
        }

        $status = $this->getFieldValue($statusField);

        if ($status == '2') {
            $status_name = $this->language_controller->translate('Archived');
        } elseif ($status == '1') {
            $status_name = $this->language_controller->translate('Published');
        } elseif ($status == '0') {
            $status_name = $this->language_controller->translate('Unpublished');
        } elseif ($status == '-1') {
            $status_name = $this->language_controller->translate('Trashed');
        } elseif ($status == '-2') {
            $status_name = $this->language_controller->translate('Spammed');
        } elseif ($status == '-5') {
            $status_name = $this->language_controller->translate('Draft');
        } elseif ($status == '-10') {
            $status_name = $this->language_controller->translate('Version');
        } else {
            return $this;
        }

        $this->setField($statusField, 'status_name', $status_name);

        return $this;
    }
}
