<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Defer;

use Molajo\Plugin\Plugin\Plugin;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * @package     Molajo
 * @subpackage  Plugin
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
        if (strtolower($this->get('template_view_path_node')) == 'defer') {
        } else {
            return true;
        }

        /** JS */
        $controllerClass = CONTROLLER_CLASS;
        $controller = new $controllerClass();
        $controller->getModelRegistry(ASSETS_LITERAL, JS_DEFER_LITERAL);
        $controller->setDataobject();

        $query_results = $controller->getData(QUERY_OBJECT_LIST);

        Services::Registry()->set(ASSETS_LITERAL, JS_DEFER_LITERAL, $query_results);

        /** JS Declarations */
        $controllerClass = CONTROLLER_CLASS;
        $controller = new $controllerClass();
        $controller->getModelRegistry(ASSETS_LITERAL, JS_DECLARATIONS_DEFER_LITERAL);

        $controller->set('model_parameter', JS_DECLARATIONS_DEFER_LITERAL);
        $query_results = $controller->getData(QUERY_OBJECT_LIST);

        Services::Registry()->set(ASSETS_LITERAL, JS_DECLARATIONS_DEFER_LITERAL, $query_results);

        return true;
    }
}
