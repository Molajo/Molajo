<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Head;

use Molajo\Plugin\Plugin\Plugin;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class HeadPlugin extends Plugin
{
    /**
     * Prepares data for the JS links and Declarations for the Head
     *
     * @return boolean
     * @since   1.0
     */
    public function onBeforeRead()
    {
        if (strtolower($this->get('template_view_path_node')) == 'head') {
        } else {
            return true;
        }

        /** JS */
        $controllerClass = CONTROLLER_CLASS;
        $controller = new $controllerClass();
        $results = $controller->getModelRegistry('dbo', 'Assets');
        if ($results === false) {
            return false;
        }

        $results = $controller->setDataobject();
        if ($results === false) {
            return false;
        }

        $controller->set('model_parameter', 'Js');
        $query_results = $controller->getData('getAssets');

        Services::Registry()->set('Plugindata', 'js', $query_results);

        /** JS Declarations */
        $controllerClass = CONTROLLER_CLASS;
        $controller = new $controllerClass();
        $results = $controller->getModelRegistry('dbo', 'Assets');
        if ($results === false) {
            return false;
        }

        $results = $controller->setDataobject();
        if ($results === false) {
            return false;
        }

        $controller->set('model_parameter', 'JsDeclarations');
        $query_results = $controller->getData('getAssets');

        Services::Registry()->set('Plugindata', 'jsdeclarations', $query_results);

        return true;
    }
}
