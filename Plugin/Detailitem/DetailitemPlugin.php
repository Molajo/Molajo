<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
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
        if ($this->get('menuitem_id', PRIMARY_LITERAL, 'parameters')) {
            if ((int) $this->get('menuitem_id', PRIMARY_LITERAL, 'parameters') == 0) {
            } else {
                return true;
            }
        }

            if ((int) $this->get('criteria_source_id', PRIMARY_LITERAL, 'parameters') == 0) {
                return true; // request for list;
            } else {
                // request for item is handled by this method
            }

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
        $controller->getModelRegistry(DATA_SOURCE_LITERAL, 'Comments');
        $controller->setDataobject();
        $controller->connectDatabase();

        $controller->model->query->where('a.root = ' . $this->get('id'));
        $controller->set('model_offset', 0, 'model_registry');
        $controller->set('model_count', 15, 'model_registry');

        $query_results = $controller->getData(QUERY_OBJECT_LIST);

        echo '<pre><br /><br />';
        var_dump($query_results);
        echo '<br /><br /></pre>';

        echo '<br /><br />';
        echo $controller->model->query->__toString();
        echo '<br /><br />';

        die;

        Services::Registry()->set(DATA_OBJECT_LITERAL, DATALIST_LITERAL, $query_results);

        return true;
    }
}
