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
		$controllerClass = 'Molajo\\MVC\\Controller\\Controller';
		$connect = new $controllerClass();
		$results = $connect->connect('dbo', 'Assets');
		if ($results === false) {
			return false;
		}
		$connect->set('model_parameter', 'JsDefer');
		$query_results = $connect->getData('getAssets');

		Services::Registry()->set('Plugindata', 'jsdefer', $query_results);

		/** JS Declarations */
		$controllerClass = 'Molajo\\MVC\\Controller\\Controller';
		$connect = new $controllerClass();
		$results = $connect->connect('dbo', 'Assets');
		if ($results === false) {
			return false;
		}
		$connect->set('model_parameter', 'JsDeclarationsDefer');
		$query_results = $connect->getData('getAssets');

		Services::Registry()->set('Plugindata', 'jsdeclarationsdefer', $query_results);

		return true;
	}
}
