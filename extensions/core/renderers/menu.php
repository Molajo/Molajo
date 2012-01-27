<?php
/**
 * @package     Molajo
 * @subpackage  Renderer
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Menu
 *
 * @package     Molajo
 * @subpackage  Renderer
 * @since       1.0
 */
class MolajoRendererMenu extends MolajoRenderer
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
     * <include:menu statement attr1=x attr2=y attrN="and-so-on" />
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
        $this->mvc = $request;
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
        $controller = new MolajoControllerDisplay ($this->mvc);
        return $controller->Display();
    }

    /**
     * _setRequest
     *
     *  From <include:message attr=1 attr=2 etc />
     */
    protected function _setRequest()
    {
        $this->mvc->set('view', MolajoController::getApplication()->get('menu_view_id', 'menu'));
        $this->mvc->set('wrap', MolajoController::getApplication()->get('menu_wrap_id', 'none'));

        foreach ($this->_attributes as $name => $value) {

            if ($name == 'wrap') {
                $this->mvc->set('wrap', $value);

            } else if ($name == 'view') {
                $this->mvc->set('view', $value);

            } else if ($name == 'id' || $name == 'wrap_id') {
                $this->mvc->set('wrap_id', $value);

            } else if ($name == 'class' || $name == 'wrap_class') {
                $this->mvc->set('wrap_class', $value);
            }
        }

        /** Model */
        $this->mvc->set('model', 'MolajoModelMenu');

        /** View Path */
        $this->mvc->set('view_type', 'extensions');
        $viewHelper = new MolajoViewHelper($this->mvc->get('view'),
            $this->mvc->get('view_type'),
            $this->mvc->get('extension_instance_name'),
            $this->mvc->get('extension_instance_name'),
            ' ',
            $this->mvc->get('template_name')
        );
        $this->mvc->set('view_path', $viewHelper->view_path);
        $this->mvc->set('view_path_url', $viewHelper->view_path_url);

        /** Wrap Path */
        $wrapHelper = new MolajoViewHelper($this->mvc->get('wrap'),
            'wraps',
            $this->mvc->get('extension_instance_name'),
            $this->mvc->get('extension_instance_name'),
            ' ',
            $this->mvc->get('template_name')
        );
        $this->mvc->set('wrap_path', $wrapHelper->view_path);
        $this->mvc->set('wrap_path_url', $wrapHelper->view_path_url);

        $this->mvc->set('extension_suppress_no_results', true);
    }
}
