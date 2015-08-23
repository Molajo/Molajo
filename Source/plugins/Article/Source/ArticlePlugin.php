<?php
/**
 * Article Plugin
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Article;

use CommonApi\Event\DisplayEventInterface;
use Molajo\Plugins\DisplayEvent;

/**
 * Article Plugin
 *
 * @package     Molajo
 * @license     http://www.opensource.org/licenses/mit-license.html MIT License
 * @since       1.0
 */
final class ArticlePlugin extends DisplayEvent implements DisplayEventInterface
{
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

        $this->processPlugin();

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
        if (strtolower($this->controller['parameters']->token->name) === 'article') {
            return true;
        }

        return false;
    }

    /**
     * Set plugin data for the Comment Heading, Form, and List Template Views
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function processPlugin()
    {
        $this->setImageContent(1, '');
        $this->setImageContent(2, 'left');
        $this->setImageContent(3, 'right');

        return $this;
    }

    /**
     * Set Row Content for Image
     *
     * @param   integer $number
     * @param   string  $align
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setImageContent($number, $align = '')
    {
        $image_file = trim($this->runtime_data->base
            . '/'
            . $this->runtime_data->resource->data->customfields->{'image' . $number});

        if (is_file($image_file)) {
        } else {
            return false;
        }

        $image_caption = trim($this->runtime_data->resource->data->customfields->{'image_caption' . $number});

        $this->setImagePluginData(
            $image_file,
            $image_caption,
            $align
        );

        return $this;
    }
}
