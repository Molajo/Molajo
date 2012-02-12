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
class MolajoHeadRenderer extends MolajoRenderer
{
    /**
     * __construct
     *
     * Class constructor.
     *
     * @param  string $name
     * @param  string $type
     * @param  array  $items (used for event processing renderers, only)
     *
     * @return  null
     * @since   1.0
     */
    public function __construct($name = null, $type = null, $items = null)
    {
        $this->extension_required = false;
        parent::__construct($name, $type, $items);
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
        if ($this->type == 'defer') {
            $this->set('model', 'MolajoDeferModel');
        } else {
            $this->set('model', 'MolajoHeadModel');
        }
        $this->set('task', 'display');

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
        if ($this->type == 'defer') {
            if ((int)$this->get('template_view_id', 0) == 0) {
                $this->set('template_view_id',
                    Services::Configuration()->get('defer_template_view_id', 'document-defer'));
            }
            if ((int)$this->get('wrap_view_id', 0) == 0) {
                $this->set('wrap_view_id',
                    Services::Configuration()->get('defer_wrap_view_id', 'none'));
            }
        } else {
            if ((int)$this->get('template_view_id', 0) == 0) {
                $this->set('template_view_id',
                    Services::Configuration()->get('head_template_view_id', 'document-head'));
            }
            if ((int)$this->get('wrap_view_id', 0) == 0) {
                $this->set('wrap_view_id',
                    Services::Configuration()->get('head_wrap_view_id', 'none'));
            }
        }

        return true;
    }
}
