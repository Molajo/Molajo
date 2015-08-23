<?php
/**
 * Wrap Plugin
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Wrap;

use CommonApi\Event\DisplayEventInterface;
use Molajo\Plugins\DisplayEvent;
use stdClass;

/**
 * Wrap Plugin
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
final class WrapPlugin extends DisplayEvent implements DisplayEventInterface
{
    /**
     * Before Rendering Template
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onAfterRenderTemplate()
    {
        if ($this->checkProcessPlugin() === false) {
            return $this;
        }

        return $this->setWrapIncludeStatement();
    }

    /**
     * Should plugin be executed?
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function checkProcessPlugin()
    {
        if (isset($this->controller['parameters']->token->attributes['wrap'])) {
        } else {
            return false;
        }

        if (trim($this->controller['parameters']->token->attributes['wrap']) === '') {
            return false;
        }

        return true;
    }

    /**
     * Before Wrap View is Rendered
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onBeforeRenderWrap()
    {
        $this->setRenderTokenView();

        if ($this->plugin_data->render->extension->title === $this->controller['parameters']->token->name) {
        } else {
            $this->controller['parameters']->token->name = $this->plugin_data->render->extension->title;
        }

        $this->setContent();

        return $this;
    }

    /**
     * Set Row Content for Wrap
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setContent()
    {
        $row           = new stdClass();
        $row->title    = '';
        $row->subtitle = '';
        $row->wrap_name = $this->controller['parameters']->token->name;

        $key = '';
        if (isset($this->controller['parameters']->token->attributes['data'])) {
            $key = $this->controller['parameters']->token->attributes['data'];
        }

        $data = '';
        if (isset($this->plugin_data->$key)) {
            $data = $this->plugin_data->$key;
        }

        $row->content = $data;

        foreach ($this->controller['parameters']->token->attributes as $key => $value) {
            if (in_array($key, array('data', 'wrap'))) {
            } else {
                $row->$key = $value;
            }
        }

        $this->controller['query_results'] = array($row);

        return $this;
    }

    /**
     * Set Wrap Include Statement
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setWrapIncludeStatement()
    {
        $key = md5($this->rendered_view);

        $this->setWrapPluginData($key);

        $wrap = trim($this->controller['parameters']->token->attributes['wrap']);

        $this->rendered_view = '{I wrap=' . ucfirst(strtolower(trim($wrap)));
        $this->rendered_view .= ' data=' . $key;

        foreach ($this->controller['parameters']->token->attributes as $key => $value) {
            if ($key === 'wrap') {
            } else {
                $this->rendered_view .= ' ' . $key . '=' . $value;
            }
        }

        $this->rendered_view .= ' I} ';

        return $this;
    }

    /**
     * Set Wrap Plugin Data
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setWrapPluginData($key)
    {
        $this->plugin_data->$key = $this->rendered_view;

        return $this;
    }
}
