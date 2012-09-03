<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Adminconfiguration;

use Molajo\Plugin\Content\ContentPlugin;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class AdminconfigurationPlugin extends ContentPlugin
{
	/**
	 * Prepares Grid
	 *
	 * @return  boolean
	 * @since   1.0
	 */
	public function onAfterReadall()
	{
		if (APPLICATION_ID == 2) {
		} else {
			return true;
		}

		if (strtolower($this->get('template_view_path_node')) == 'adminconfiguration') {
		} else {
			return true;
		}

		/** Tab Group Class */
		$tab_class = str_replace(',', ' ', $this->get('tab_class'));

		/** Create Tabs */
		$tabArray = $this->setTabArray();

		if ($tabArray === false) {
			$tabCount = 0;
		} else {
			$tabCount = count($tabArray);
		}

		/** Build Query Results for View */
		$query_results = array();

		$row = new \stdClass();
		$row->tab_count = $tabCount;
		$row->tab_class = $tab_class;
		$row->tab_array = '';

		if ($tabCount === 0) {
			$row->tab_array = null;
		} else {
			foreach ($tabArray as $tab) {
				$row->tab_array .= trim($tab);
			}
		}

		$query_results[] = $row;

		$this->data = $query_results;

		return true;
	}

	/**
	 * Create Tabs based upon Page Type
	 *
	 * @return array
	 * @since  1.0
	 */
	protected function setTabArray()
	{
		//"configuration_array":"{{Basic,basic}}{{Parameters,parameters}}{{Fields,fields}}"
		$tabs = array();

		$configuration = $this->get('configuration_array');
		$configurationArray = array();
		$temp = explode('}}', $configuration);

		foreach ($temp as $set) {
			$set = str_replace(',', ' ', $set);
			$set = str_replace(':', '=', $set);
			$set = str_replace('{{', '', $set);
			$set = str_replace('http=', 'http:', $set);
			if (trim($set) == '') {
			} else {
				$configurationArray[] = trim($set);
			}
		}

		foreach ($configurationArray as $config) {

			$split = explode(' ', $config);

			$tabTitle = str_replace(
				' ',
				'&nbsp;',
				htmlentities(Services::Language()->translate(
					$split[0]
				), ENT_COMPAT, 'UTF-8')
			);
			$tabLink = $split[1];

			$tabIncludeName = 'adminconfiguration-' . $split[1];

			$tabs[] = $this->createTabEntry($tabTitle, $tabLink, $tabIncludeName);

		}

		return $tabs;
	}

	/**
	 * createTabEntry
	 *
	 * @param  $tabTitle
	 * @param  $tabLink
	 * @param  $tabIncludeName
	 *
	 * @return  string
	 * @since   1.0
	 */
	protected function createTabEntry($tabTitle, $tabLink, $tabIncludeName)
	{
		$tabArray = 'tab_title:' . $tabTitle
			. ',' . 'tab_link:' . $tabLink
			. ',' . 'tab_include_name:' . $tabIncludeName;

		return '{{' . trim($tabArray) . '}}';
	}
}
