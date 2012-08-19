<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Adminheader;

use Molajo\Plugin\Content\ContentPlugin;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class AdminheaderPlugin extends ContentPlugin
{
	/**
	 * Prepares data for the Administrator Header
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function onAfterRead()
	{
		if (strtolower($this->get('template_view_path_node')) == 'adminheader') {
		} else {
			return true;
		}

		$title = Services::Registry()->get('Plugindata', 'PageTitle');
		if ($title == '') {
			$title = $this->row->criteria_title;
		} else {
			$title .= '-' . $this->row->criteria_title;
		}
		$this->saveField(null, 'title', $title);

		$homeURL = Services::Registry()->get('Configuration', 'application_base_url');

		$this->saveField(null, 'home_url', $homeURL);

		return true;
	}
}
