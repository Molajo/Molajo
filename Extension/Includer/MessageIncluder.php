<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
namespace Molajo\Extension\Includer;

defined('MOLAJO') or die;

use Molajo\Application\Includer;
use Molajo\Service\Services;

/**
 * Message
 *
 * @package   Molajo
 * @subpackage  Includer
 * @since       1.0
 */
Class MessageIncluder extends Includer
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
     *  _getApplicationDefaults
     *
     *  Retrieve default values, if not provided by extension
     *
     * @return  bool
     * @since   1.0
     */
    protected function _getApplicationDefaults()
    {
        $this->set('model', 'MessagesModel');
        $this->set('task', 'display');

        $this->parameters = Services::Registry()->initialise();
        $this->parameters->set('suppress_no_results', 1);

        if ((int)$this->get('template_view_id', 0) == 0) {
            $this->set('template_view_id',
                Services::Registry()->get('Configuration', 'message_template_view_id'));
        }
        if ((int)$this->get('wrap_view_id', 0) == 0) {
            $this->set('wrap_view_id',
                Services::Registry()->get('Configuration', 'message_wrap_view_id'));
        }

        return true;
    }
}

