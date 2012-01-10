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
     * @var    string
     * @since  1.0
     */
    protected $name = null;

    /**
     * Request 
     * 
     * @var    object
     * @since  1.0
     */
    protected $request;

    /**
     * Attributes
     *
     * Extracted in Document Class from Template/Page
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
     * @param array $request
     * @since 1.0
     */
    public function __construct($name = null, JObject $request)
    {
        $this->name = $name;
        $this->request = $request;
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
        /** from template rendering */
        $this->attributes = $attributes;

        /** retrieve parameters */
        $this->_getRequest();

        /** Instantiate Controller */
        $controller = new MolajoControllerDisplay ($this->request);
        return $controller->Display();
    }

    /**
     * _getRequest
     *
     *  From <include:message attr=1 attr=2 etc />
     */
    protected function _getRequest()
    {
        $this->request->set('view',  MolajoController::getApplication()->get('message_view', 'messages'));
        $this->request->set('wrap', MolajoController::getApplication()->get('message_wrap', 'div'));

        foreach ($this->attributes as $name => $value) {

            if ($name == 'wrap') {
                $this->request->get('wrap', $value);

            } else if ($name == 'view') {
                $this->request->get('view', $value);

            } else if ($name == 'id' || $name == 'wrap_id') {
                $this->request->get('wrap_id', $value);

            } else if ($name == 'class' || $name == 'wrap_class') {
                $this->request->get('wrap_class', $value);
            }
        }

        /** Model */
        $this->request->get('model', 'MolajoModelMessages');

        /** View Path */
        $this->request->get('view_type', 'extensions');

        $viewHelper = new MolajoViewHelper($this->request->get('view'),
                                            $this->request->get('view_type'),
                                            $this->request->get('option'),
                                            $this->request->get('extension_type'),
                                            ' ',
                                            $this->request->get('template_name')
                                            );
        $this->request->set('view_path', $viewHelper->view_path);
        $this->request->set('view_path_url', $viewHelper->view_path_url);

        /** Wrap Path */
        $wrapHelper = new MolajoViewHelper($this->request->get('wrap'),
                                            'wraps',
                                            $this->request->get('option'),
                                            $this->request->get('extension_type'),
                                            ' ',
                                            $this->request->get('template_name')
                                        );
        $this->request->set('wrap_path', $wrapHelper->view_path);
        $this->request->set('wrap_path_url', $wrapHelper->view_path_url);

        $this->request->set('suppress_no_results', true);
    }
}
