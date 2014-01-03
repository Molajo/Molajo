<?php
/**
 * Defer Plugin
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugin\Defer;

use CommonApi\Event\ReadInterface;
use Molajo\Plugin\ReadEventPlugin;

/**
 * Defer Plugin
 *
 * @package     Molajo
 * @license     http://www.opensource.org/licenses/mit-license.html MIT License
 * @since       1.0
 */
class DeferPlugin extends ReadEventPlugin implements ReadInterface
{
    /**
     * Prepares data for the JS links and Declarations for the Head
     *
     * @return  $this
     * @since   1.0
     */
    public function onBeforeRead()
    {
        return $this;

        if (strtolower($this->get('template_view_path_node', '', 'runtime_data')) == 'defer') {
        } else {
            return $this;
        }

        /** JS */
        $controller_class_namespace = $this->controller_namespace;
        $controller                 = new $controller_class_namespace();
        $controller->getModelRegistry('Assets', JS_DEFER_LITERAL);
        $controller->setDataobject();
        $controller->connectDatabase();

        $temp_row = $controller->getData('list');

        $this->registry->set('Assets', JS_DEFER_LITERAL, $temp_row);

        /** JS Declarations */
        $controller_class_namespace = $this->controller_namespace;
        $controller                 = new $controller_class_namespace();
        $controller->getModelRegistry('Assets', JS_DECLARATIONS_DEFER_LITERAL);
        $controller->set('model_parameter', JS_DECLARATIONS_DEFER_LITERAL, 'runtime_data');
        $controller->connectDatabase();

        $temp_row = $controller->getData('list');

        $this->registry->set('Assets', JS_DECLARATIONS_DEFER_LITERAL, $temp_row);

        return $this;
    }
}
