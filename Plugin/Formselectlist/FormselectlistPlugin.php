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

        $results = Services::Text()->getList($datalist, $this->parameters);

        if ($results === false) {
            return true;

        }

        $selected = $this->get('selected', NULL);

        $query_results = Services::Text()->buildSelectlist(
            $datalist,
            $results[0]->listitems,
            $results[0]->multiple,
            $results[0]->size,
            $this->get('selected', NULL)
        );


        $this->set('model_type', DATAOBJECT_MODEL_TYPE);
        $this->set('model_name', DATALIST_MODEL_NAME);
        $this->set('model_query_object', QUERY_OBJECT_LIST);

        $this->parameters['model_type'] = DATAOBJECT_MODEL_TYPE;
        $this->parameters['model_name'] = TEMPLATEVIEWNAME_MODEL_NAME;

        Services::Registry()->set(
            TEMPLATEVIEWNAME_MODEL_NAME,
            'Formselectlist',
            $query_results
        );

        return true;
    }
}
