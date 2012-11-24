<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Detailitem;

use Molajo\Service\Services;
use Molajo\Plugin\Plugin\Plugin;

defined('MOLAJO') or die;

/**
 * Detailitem
 *
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class DetailitemPlugin extends Plugin
{

    /**
     * Prepares Data for non-menuitem single content item requests
     *
     * @return boolean
     * @since   1.0
     */
    public function onBeforeParse()
    {
        return true;
        if (Services::Registry()->exists('Parameters', 'menuitem_id')) {
            if ((int) Services::Registry()->get('Parameters', 'menuitem_id') == 0) {
            } else {
                return true;
            }
        }

        if (Services::Registry()->exists('Parameters', 'criteria_source_id')) {
            if ((int) Services::Registry()->get('Parameters', 'criteria_source_id') == 0) {
                return true; // request for list;
            } else {
                // request for item is handled by this method
            }
        }

        $this->set('request_model_type', $this->get('model_type'));
        $this->set('request_model_name', $this->get('model_name'));

        $this->set('model_type', DATAOBJECT_MODEL_TYPE);
        $this->set('model_name', PRIMARY_QUERY_RESULTS_MODEL_NAME);
        $this->set('model_query_object', QUERY_OBJECT_LIST);

        $this->parameters['model_type'] = DATAOBJECT_MODEL_TYPE;
        $this->parameters['model_name'] = PRIMARY_QUERY_RESULTS_MODEL_NAME;

        //$this->getComments();
        return true;
    }

    /**
     * Grid Query: results stored in Plugin registry
     *
     * @return bool
     * @since   1.0
     */
    protected function getComments()
    {
        $controllerClass = CONTROLLER_CLASS;
        $controller = new $controllerClass();
        $results = $controller->getModelRegistry('Datasource', 'Comments');
        if ($results === false) {
            return false;
        }

        $results = $controller->setDataobject();
        if ($results === false) {
            return false;
        }

        $controller->model->query->where('a.root = ' . $this->get('id'));
        $controller->set('model_offset', 0);
        $controller->set('model_count', 10);

        $query_results = $controller->getData(QUERY_OBJECT_LIST);

        echo '<pre><br /><br />';
        var_dump($query_results);
        echo '<br /><br /></pre>';

        echo '<br /><br />';
        echo $controller->model->query->__toString();
        echo '<br /><br />';

        die;

        Services::Registry()->set('Plugindata', 'PrimaryRequestComments', $query_results);

        return true;
    }
}
