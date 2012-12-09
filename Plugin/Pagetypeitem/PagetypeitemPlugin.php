<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Pagetypeitem;

use Molajo\Plugin\Plugin\Plugin;
use Molajo\Plugin\Pagetypeconfiguration;
use Molajo\Service\Services;


defined('MOLAJO') or die;

/**
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class PagetypeitemPlugin extends Plugin
{
    /**
     * Switches the model registry for an item since the Content Query already retrieved the data
     *  and saved it into the registry
     *
     * @return boolean
     * @since   1.0
     */
    public function onBeforeInclude()
    {
        if (strtolower($this->get('catalog_page_type', '', 'parameters')) == strtolower(PAGE_TYPE_ITEM)) {
        } else {
            return true;
        }

        $model_type = ucfirst(strtolower($this->get('model_type', '', 'parameters')));
        $model_name = ucfirst(strtolower($this->get('model_name', '', 'parameters')));

        $this->set('request_model_type', $model_type, 'parameters');
        $this->set('request_model_name', $this->get('model_name', '', 'parameters'), 'parameters');
        $this->set('request_model_registry', $model_name . $model_type, 'parameters');

        $this->set('model_name', ucfirst(strtolower(PRIMARY_LITERAL)), 'parameters');
        $this->set('model_type', ucfirst(strtolower(DATA_OBJECT_LITERAL)), 'parameters');
        $this->set('model_registry', ucfirst(strtolower(PRIMARY_LITERAL)).ucfirst(strtolower(DATA_OBJECT_LITERAL)), 'parameters');

        $controllerClass = CONTROLLER_CLASS;
        $controller = new $controllerClass();
        $controller->getModelRegistry($this->get('model_type', '', 'parameters'), $this->get('model_name', '', 'parameters'));

        Services::Registry()->merge(
            $model_name . $model_type,
            ucfirst(strtolower(PRIMARY_LITERAL)).ucfirst(strtolower(DATA_OBJECT_LITERAL)),
            false, 0
        );

        $this->set('model_query_object', QUERY_OBJECT_ITEM, 'parameters');
        $this->set('page_type', PAGE_TYPE_ITEM, 'parameters');

        return true;
    }
}
