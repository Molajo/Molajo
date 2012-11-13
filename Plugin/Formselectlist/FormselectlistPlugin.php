<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Formselectlist;

use Molajo\Plugin\Plugin\Plugin;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class FormselectlistPlugin extends Plugin
{

    /**
     * Prepares listbox contents
     *
     * @return boolean
     * @since   1.0
     */
    public function onAfterReadall()
    {
        if (strtolower($this->get('template_view_path_node')) == 'formselectlist') {
        } else {
            return true;
        }

        $datalist = Services::Registry()->get('Parameters', 'datalist');

        if ($datalist === false || trim($datalist) == '') {
            return true;
        }

        $items = Services::Text()->getList($datalist, $this->parameters);

        if ($items === false) {
            return true;
        }

        if (isset($this->parameters['selected'])) {
            $selected = $this->parameters['selected'];
        } else {
            $selected = null;
        }
        $this->data = Services::Text()->buildSelectlist($datalist, $items, 0, 5, $selected);

        return true;
    }
}
