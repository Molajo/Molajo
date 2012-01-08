<?php
/**
 * @package     Molajo
 * @subpackage  Renderers
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Message
 *
 * @package     Molajo
 * @subpackage  Renderers
 * @since       1.0
 */
class MolajoMessageRenderer
{
    /**
     * Name
     *
     * From Molajo Extension
     *
     * @var    string
     * @since  1.0
     */
    protected $name = null;

    /**
     * Request Array
     *
     * From Molajo Extension
     *
     * @var    array
     * @since  1.0
     */
    protected $requestArray = array();

    /**
     * Attributes
     *
     * Extracted in Format Class from Template/Page
     * <include:message statement attr1=x attr2=y attrN="and-so-on" />
     *
     * @var    array
     * @since  1.0
     */
    protected $attributes = array();

    /**
     * __construct
     *
     * Class constructor.
     *
     * @param null $name
     * @param array $requestArray
     * @since 1.0
     */
    public function __construct($name = null, $requestArray = array())
    {
        $this->name = $name;
        $this->requestArray = $requestArray;
    }

    /**
     * render
     *
     * Render the message.
     *
     * @param $attributes
     * @return mixed
     */
    public function render($attributes)
    {
        /** @var $attributes  */
        $this->attributes = $attributes;

        /** retrieve parameters */
        $this->_getRequest();

        /** Instantiate Controller */
        $controller = new MolajoControllerDisplay ($this->requestArray);
        return $controller->Display();
    }

    /**
     * _getRequest
     *
     *  From <include:message attr=1 attr=2 etc />
     */
    protected function _getRequest()
    {
        $this->requestArray['view'] = MolajoController::getApplication()->get('message_view', 'messages');
        $this->requestArray['wrap'] = MolajoController::getApplication()->get('message_wrap', 'div');

        foreach ($this->attributes as $name => $value) {

            if ($name == 'wrap') {
                $this->requestArray['wrap'] = $value;

            } else if ($name == 'view') {
                $this->requestArray['view'] = $value;

            } else if ($name == 'id' || $name == 'wrap_id') {
                $this->requestArray['wrap_id'] = $value;

            } else if ($name == 'class' || $name == 'wrap_class') {
                $this->requestArray['wrap_class'] = $value;
            }
        }

        /** Model */
        $this->requestArray['model'] = 'MolajoModelMessages';

        /** View Path */
        $this->requestArray['view_type'] = 'extensions';
        $viewHelper = new MolajoViewHelper($this->requestArray['view'], $this->requestArray['view_type'], $this->requestArray['option'], $this->requestArray['extension_type'], ' ', $this->requestArray['template_name']);
        $this->requestArray['view_path'] = $viewHelper->view_path;
        $this->requestArray['view_path_url'] = $viewHelper->view_path_url;

        /** Wrap Path */
        $wrapHelper = new MolajoViewHelper($this->requestArray['wrap'], 'wraps', $this->requestArray['option'], $this->requestArray['extension_type'], ' ', $this->requestArray['template_name']);
        $this->requestArray['wrap_path'] = $wrapHelper->view_path;
        $this->requestArray['wrap_path_url'] = $wrapHelper->view_path_url;

        $this->requestArray['suppress_no_results'] = true;
    }
}
