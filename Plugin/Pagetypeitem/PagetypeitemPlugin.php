<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Pagetypeitem;

use Molajo\Plugin\Plugin\Plugin;
use Molajo\Plugin\Pagetypeconfiguration;

defined('MOLAJO') or die;

/**
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class PagetypeitemPlugin extends Plugin
{
    /**
     * Prepares data for Pagetypeitem
     *
     * @return boolean
     * @since   1.0
     */
    public function onBeforeParse()
    {
		if (strtolower($this->get('page_type', '', 'parameters')) == QUERY_OBJECT_ITEM) {
		} else {
			return true;
		}

		$resource_model_type = $this->get('model_type', '', 'parameters');
		$resource_model_name = $this->get('model_name', '', 'parameters');

        $resource_table_registry = ucfirst(strtolower($this->get('model_name', '', 'parameters')))
            . ucfirst(strtolower($this->get('model_type', '', 'parameters')));


        $controller->set('request_model_type', $this->get('model_type', '', 'parameters'), 'model_registry');
        $controller->set('request_model_name', $this->get('model_name', '', 'parameters'), 'model_registry');

        $controller->set('model_type', DATA_OBJECT_LITERAL, 'model_registry');
        $controller->set('model_name', PRIMARY_LITERAL, 'model_registry');
        $controller->set('model_query_object', QUERY_OBJECT_LIST, 'model_registry');

        $controller->set('model_type', QUERY_OBJECT_LIST, 'model_registry');
        $controller->set('model_name', PRIMARY_LITERAL, 'model_registry');

        /** ContentHelper already placed data already in registry */
        return true;
    }

}
