<?php
/**
 * Image Plugin
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Image;

use CommonApi\Event\DisplayEventInterface;
use CommonApi\Event\ReadEventInterface;

/**
 * Image Plugin
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
final class ImagePlugin extends Html implements ReadEventInterface, DisplayEventInterface
{
    /**
     * Executes after reading row
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onAfterReadRow()
    {
        if ($this->checkEditModel() === true) {
            return false;
        }

        if ($this->existFields('html') === true) {
            $this->processFieldsByType('processHtmlField', $this->hold_fields);
        }

        if ($this->existFields('image') === true) {
            $this->processFieldsByType('addImageKey', $this->hold_fields);
        }

        return $this;
    }

    /**
     * Prepare Data for Injecting into Template
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onGetTemplateData()
    {
        if ($this->checkProcessPlugin() === false) {
            return $this;
        }

        $this->setContent();

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
        if ($this->checkEditModel() === true) {
            return false;
        }

        if ($this->controller['parameters']->token->name === 'image') {
        } else {
            return false;
        }

        if (isset($this->controller['parameters']->token->attributes['image_id'])) {
        } else {
            return false;
        }

        return true;
    }

    /**
     * Set Row Content for Image
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setContent()
    {
        if (trim($this->controller['parameters']->token->model_name) === '') {
            $this->controller['parameters']->token->model_type = 'plugin_data';
            $this->controller['parameters']->token->model_name = 'image';
        }

        $x = $this->plugin_data->{$this->controller['parameters']->token->attributes['image_id']};

        if (isset($this->controller['parameters']->token->attributes['align'])) {
            $x->data[0]->align = $this->controller['parameters']->token->attributes['align'];
        } else {
            $x->data[0]->align = '';
        }

        if (isset($this->controller['parameters']->token->attributes['size'])) {
            $x->data[0]->size = $this->controller['parameters']->token->attributes['size'];
        } else {
            $x->data[0]->size = '';
        }

        $this->plugin_data->{$this->controller['parameters']->token->model_name} = $x;

        return $this;
    }
}
