<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Commentform;

use Molajo\Plugin\Plugin\Plugin;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class CommentformPlugin extends Plugin
{
	/**
	 * Prepares data for Edit
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function onAfterReadall()
	{
		if (strtolower($this->get('template_view_path_node')) == 'commentform') {
		} else {
			return true;
		}
		return true;
		/** Get configuration menuitem settings for this resource */
//		$menuitem = Helpers::Content()->getResourceMenuitemParameters('Configuration', 17000);

		/** Tab Group Class */
//		$tab_class = Services::Registry()->get('ConfigurationMenuitemParameters', 'configuration_tab_class');

		/** Create Tabs */
		$namespace = 'Comments';

		$tab_array = Services::Registry()->get('ConfigurationMenuitemParameters', 'commentform_tab_array');
  		$tab_array = '{{Comments,visitor*,email*,website*,ip*,spam*}}';

		/*
		visitor_name
		email_address
		website
		ip_address
		spam_protection
		*/

		$tabs = Services::Form()->setTabArray(
			'System',
			'Comments',
			'Comments',
			$tab_array,
			'comments_tab_',
			'Comment',
			'Commenttab',
			$tab_class,
			17000,
			array()
		);

		$this->set('model_name', 'Plugindata');
		$this->set('model_type', 'dbo');
		$this->set('model_query_object', 'getPlugindata');
		$this->set('model_parameter', 'Edit');

		$this->parameters['model_name'] = 'Plugindata';
		$this->parameters['model_type'] = 'dbo';

		Services::Registry()->set('Plugindata', 'Commentform', $tabs);



		echo '<pre>';
		var_dump($tabs);
		echo '</pre>';



		echo '<pre>';
		var_dump(Services::Registry()->get('Plugindata', 'Commentform'));
		echo '</pre>';
die;
		return true;
	}
}
