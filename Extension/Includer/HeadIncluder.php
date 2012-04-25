<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
namespace Molajo\Extension\Includer;

defined('MOLAJO') or die;

use Molajo\Application\Includer;
use Molajo\Application\Services;

/**
 * Head
 *
 * @package   Molajo
 * @subpackage  Includer
 * @since       1.0
 */
Class HeadIncluder extends Includer
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
        $this->parameters->set('html_display_filter', false);
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
        $this->set('model', 'HeadModel');
        $this->set('task', 'display');

        if ((int)$this->get('template_view_id', 0) == 0) {
            $this->set('template_view_id',
                Services::Registry()->get('Configuration', 'head_template_view_id', 'DocumentHead'));
        }

        if ((int)$this->get('wrap_view_id', 0) == 0) {
            $this->set('wrap_view_id',
                Services::Registry()->get('Configuration', 'head_wrap_view_id', 'none'));
        }

        if ($this->type == 'defer') {
            $this->parameters->set('defer', 1);
        } else {
            $this->parameters->set('defer', 0);
        }

        return true;
    }
}
