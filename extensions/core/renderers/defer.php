<?php
/**
 * @package     Molajo
 * @subpackage  Renderer
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Defer
 *
 * @package     Molajo
 * @subpackage  Renderer
 * @since       1.0
 */
class MolajoRendererDefer extends MolajoRenderer
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
    public $request;

    /**
     * Attributes
     *
     * Extracted in Format Class from Template/Page
     * <include:defer statement attr1=x attr2=y attrN="and-so-on" />
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
        /** @var $attributes from template */
        $this->_attributes = $attributes;

        /** retrieve parameters */
        $this->_setParameters();

        /** Instantiate Controller */
        $controller = new MolajoControllerDisplay ($this->request);
        return $controller->Display();
    }

    /**
     * _setRequest
     *
     *  From <include:message attr=1 attr=2 etc />
     */
    protected function _setRequest()
    {
        $this->request->set('view', MolajoController::getApplication()->get('defer_view_id', 'defer'));
        $this->request->set('wrap', MolajoController::getApplication()->get('defer_wrap_id', 'none'));

        foreach ($this->_attributes as $name => $value) {

            if ($name == 'wrap') {
                $this->request->set('wrap', $value);

            } else if ($name == 'view') {
                $this->request->set('view', $value);

            } else if ($name == 'id' || $name == 'wrap_id') {
                $this->request->set('wrap_id', $value);

            } else if ($name == 'class' || $name == 'wrap_class') {
                $this->request->set('wrap_class', $value);
            }
        }

        /** Model */
        $this->request->set('model', 'MolajoModelDefer');

        /** View Path */
        $this->request->set('view_type', 'extensions');
        $viewHelper = new MolajoViewHelper($this->request->get('view'),
            $this->request->get('view_type'),
            $this->request->get('mvc_extension_instance_name'),
            $this->request->get('mvc_extension_instance_name'),
            ' ',
            $this->request->get('template_name')
        );
        $this->request->set('view_path', $viewHelper->view_path);
        $this->request->set('view_path_url', $viewHelper->view_path_url);

        /** Wrap Path */
        $wrapHelper = new MolajoViewHelper($this->request->get('wrap'),
            'wraps',
            $this->request->get('mvc_extension_instance_name'),
            $this->request->get('mvc_extension_instance_name'),
            ' ',
            $this->request->get('template_name')
        );
        $this->request->set('wrap_path', $wrapHelper->view_path);
        $this->request->set('wrap_path_url', $wrapHelper->view_path_url);

        $this->request->set('extension_suppress_no_results', true);
    }
}
