<?php
/**
 * @package     Molajo
 * @subpackage  Renderer
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Head
 *
 * @package     Molajo
 * @subpackage  Renderer
 * @since       1.0
 */
class MolajoHeadRenderer
{
    /**
     * Name
     *
     * @var    string
     * @since  1.0
     */
    protected $_name = null;

    /**
     * Request
     *
     * @var    object
     * @since  1.0
     */
    protected $_request;

    /**
     * Attributes
     *
     * Extracted in Format Class from Template/Page
     * <include:message statement attr1=x attr2=y attrN="and-so-on" />
     *
     * @var    array
     * @since  1.0
     */
    protected $_attributes = array();

    /**
     * __construct
     *
     * Class constructor.
     *
     * @param null $name
     * @param array $request
     * @since 1.0
     */
    public function __construct($name = null, $request = array())
    {
        $this->_name = $name;
        $this->_request = $request;
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
        /** @var $attributes from template */
        $this->_attributes = $attributes;

        /** retrieve parameters */
        $this->_setParameters();

        /** Instantiate Controller */
        $controller = new MolajoControllerDisplay ($this->_request);
        return $controller->Display();
    }

    /**
     * _setRequest
     *
     *  From <include:message attr=1 attr=2 etc />
     */
    protected function _setRequest()
    {
        $this->_request->set('view', MolajoController::getApplication()->get('head_view', 'head'));
        $this->_request->set('wrap', MolajoController::getApplication()->get('head_wrap', 'none'));

        foreach ($this->_attributes as $name => $value) {

            if ($name == 'wrap') {
                $this->_request->set('wrap', $value);

            } else if ($name == 'view') {
                $this->_request->set('view', $value);

            } else if ($name == 'id' || $name == 'wrap_id') {
                $this->_request->set('wrap_id', $value);

            } else if ($name == 'class' || $name == 'wrap_class') {
                $this->_request->set('wrap_class', $value);
            }
        }

        /** Model */
        $this->_request->set('model', 'MolajoModelHead');

        /** View Path */
        $this->_request->set('view_type', 'extensions');
        $viewHelper = new MolajoViewHelper($this->_request->get('view'),
            $this->_request->get('view_type'),
            $this->_request->get('option'),
            $this->_request->get('extension_type'),
            ' ',
            $this->_request->get('template_name')
        );
        $this->_request->set('view_path', $viewHelper->view_path);
        $this->_request->set('view_path_url', $viewHelper->view_path_url);

        /** Wrap Path */
        $wrapHelper = new MolajoViewHelper($this->_request->get('wrap'),
            'wraps',
            $this->_request->get('option'),
            $this->_request->get('extension_type'),
            ' ',
            $this->_request->get('template_name')
        );
        $this->_request->set('wrap_path', $wrapHelper->view_path);
        $this->_request->set('wrap_path_url', $wrapHelper->view_path_url);

        $this->_request->set('suppress_no_results', true);
    }
}
