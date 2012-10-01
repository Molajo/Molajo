<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Head;

use Molajo\Plugin\Content\ContentPlugin;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class HeadPlugin extends ContentPlugin
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
		$controllerClass = 'Molajo\\MVC\\Controller\\Controller';
		$connect = new $controllerClass();
		$results = $connect->connect('dbo', 'Assets');
		if ($results === false) {
			return false;
		}
		$connect->set('model_parameter', 'Js');
		$query_results = $connect->getData('getAssets');

		Services::Registry()->set('Plugindata', 'js', $query_results);

		/** JS Declarations */
		$controllerClass = 'Molajo\\MVC\\Controller\\Controller';
		$connect = new $controllerClass();
		$results = $connect->connect('dbo', 'Assets');
		if ($results === false) {
			return false;
		}
		$connect->set('model_parameter', 'JsDeclarations');
		$query_results = $connect->getData('getAssets');

		Services::Registry()->set('Plugindata', 'jsdeclarations', $query_results);

		return true;
	}
}
