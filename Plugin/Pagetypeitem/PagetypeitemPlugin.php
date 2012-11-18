<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
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
		if (strtolower($this->get('page_type')) == 'item') {
		} else {
			return true;
		}

		$resource_model_type = $this->get('model_type');
		$resource_model_name = $this->get('model_name');

        $resource_table_registry = ucfirst(strtolower($this->get('model_name')))
            . ucfirst(strtolower($this->get('model_type')));

		/** Item Data */
        $this->set('model_type', 'Plugindata');
        $this->set('model_name', PRIMARY_QUERY_RESULTS);
        $this->set('model_query_object', 'list');

        $this->parameters['model_type'] = 'Plugindata';
        $this->parameters['model_name'] = PRIMARY_QUERY_RESULTS;


        return true;
    }

}
