<?php
/**
 * @package     Molajo
 * @subpackage  Renderer
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Message
 *
 * @package     Molajo
 * @subpackage  Renderer
 * @since       1.0
 */
class MolajoRendererMessage extends MolajoRenderer
{
    /**
     * __construct
     *
     * Class constructor.
     *
     * @param  null $name
     * @param  array $request
     *
     * @return  null
     * @since   1.0
     */
    public function __construct($name = null, $request = array(), $type = null)
    {
        $this->_extension_required = false;
        parent::__construct($name, $request, $type);
    }

    /**
     * _getExtension
     *
     * Retrieve extension information using either the ID or the name
     *
     * @return bool
     * @since 1.0
     */
    protected function _getExtension()
    {
        $this->mvc->set('mvc_model', 'MolajoModelMessages');
        $this->mvc->set('mvc_task', 'display');
        $this->parameters->set('extension_suppress_no_results', 1);

        return true;
    }

    /**
     *  _getApplicationDefaults
     *
     *  Retrieve default values, if not provided by extension
     *
     * @return  bool
     * @since   1.0
     */
    protected function _getApplicationDefaults()
    {
        if ((int)$this->mvc->get('view_template_id', 0) == 0) {
            $this->mvc->set('view_template_id', MolajoController::getApplication()->get('message_view_template_id'));
        }
        if ((int)$this->mvc->get('view_wrap_id', 0) == 0) {
            $this->mvc->set('view_wrap_id', MolajoController::getApplication()->get('message_view_wrap_id'));
        }
        return true;
    }
}

