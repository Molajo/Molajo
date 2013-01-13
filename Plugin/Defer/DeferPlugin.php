<?php
/**
 * @package    Molajo
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @license    MIT
 */
namespace Molajo\Plugin\Defer;

use Molajo\Plugin\Plugin\Plugin;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * @package     Molajo
 * @license     MIT
 * @since       1.0
 */
class DeferPlugin extends Plugin
{
    /**
     * Prepares data for the JS links and Declarations for the Head
     *
     * @return boolean
     * @since   1.0
     */
    public function onBeforeRead()
    {
        if (strtolower($this->get('template_view_path_node', '', 'parameters')) == 'defer') {
        } else {
            return true;
        }

        /** JS */
        $controllerClass = CONTROLLER_CLASS_NAMESPACE;
        $controller      = new $controllerClass();
        $controller->getModelRegistry('Assets', JS_DEFER_LITERAL);
        $controller->setDataobject();
        $controller->connectDatabase();

        $temp_query_results = $controller->getData(QUERY_OBJECT_LIST);

        Services::Registry()->set('Assets', JS_DEFER_LITERAL, $temp_query_results);

        /** JS Declarations */
        $controllerClass = CONTROLLER_CLASS_NAMESPACE;
        $controller      = new $controllerClass();
        $controller->getModelRegistry('Assets', JS_DECLARATIONS_DEFER_LITERAL);
        $controller->set('model_parameter', JS_DECLARATIONS_DEFER_LITERAL, 'parameters');
        $controller->connectDatabase();

        $temp_query_results = $controller->getData(QUERY_OBJECT_LIST);

        Services::Registry()->set('Assets', JS_DECLARATIONS_DEFER_LITERAL, $temp_query_results);

        return true;
    }
}
