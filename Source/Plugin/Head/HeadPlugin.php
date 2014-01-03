<?php
/**
 * Head Plugin
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugin\Head;

use Molajo\Plugin\ReadEventPlugin;
use CommonApi\Event\ReadInterface;

/**
 * Head Plugin
 *
 * @package     Molajo
 * @license     http://www.opensource.org/licenses/mit-license.html MIT License
 * @since       1.0
 */
class HeadPlugin extends ReadEventPlugin implements ReadInterface
{
    /**
     * Prepares data for the JS links and Declarations for the Head
     *
     * @return  $this
     * @since   1.0
     */
    public function onBeforeRead()
    {
        return;
        if (strtolower($this->get('template_view_path_node', '', 'runtime_data')) == 'head') {
        } else {
            return $this;
        }

        /** JS */
        $controller_class_namespace = $this->controller_namespace;
        $controller                 = new $controller_class_namespace();
        $controller->getModelRegistry('dbo', 'Assets');
        $controller->setDataobject();
        $controller->connectDatabase();
        $controller->set('model_parameter', 'Js');

        $temp_row = $controller->getData('getAssets');

        $this->registry->set('Assets', 'js', $temp_row);

        /** JS Declarations */
        $controller_class_namespace = $this->controller_namespace;
        $controller                 = new $controller_class_namespace();
        $controller->getModelRegistry('dbo', 'Assets');
        $controller->setDataobject();
        $controller->connectDatabase();

        $controller->set('model_parameter', 'JsDeclarations');
        $temp_row = $controller->getData('getAssets');

        $this->registry->set('Assets', 'jsdeclarations', $temp_row);

        return $this;
    }
}
