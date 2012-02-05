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
class MolajoRendererHead extends MolajoRenderer
{
    /**
     * __construct
     *
     * Class constructor.
     *
     * @param  null   $name
     * @param  array  $request
     * @param  string $type
     *
     * @return  null
     * @since   1.0
     */
    public function __construct($name = null, $type = null)
    {
        $this->_extension_required = false;
        parent::__construct($name, $type);
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
        if ($this->_type == 'defer') {
            $this->mvc->set('mvc_model', 'MolajoModelDefer');
        } else {
            $this->mvc->set('mvc_model', 'MolajoModelHead');
        }
        $this->mvc->set('mvc_task', 'display');

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
        if ($this->_type == 'defer') {
            if ((int)$this->mvc->get('template_view_id', 0) == 0) {
                $this->mvc->set('template_view_id',
                    MolajoController::getApplication()->get('defer_template_view_id', 'document-defer'));
            }
            if ((int)$this->mvc->get('wrap_view_id', 0) == 0) {
                $this->mvc->set('wrap_view_id',
                    MolajoController::getApplication()->get('defer_wrap_view_id', 'none'));
            }
        } else {
            if ((int)$this->mvc->get('template_view_id', 0) == 0) {
                $this->mvc->set('template_view_id',
                    MolajoController::getApplication()->get('head_template_view_id', 'document-head'));
            }
            if ((int)$this->mvc->get('wrap_view_id', 0) == 0) {
                $this->mvc->set('wrap_view_id',
                    MolajoController::getApplication()->get('head_wrap_view_id', 'none'));
            }
        }

        return true;
    }
}
