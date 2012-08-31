<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Adminpagetitle;

use Molajo\Plugin\Content\ContentPlugin;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class AdminpagetitlePlugin extends ContentPlugin
{
	/**
	 * Prepares Page Title and Buttons for Rendering
	 *
	 * @return  void
	 * @since   1.0
	 */
	public function onAfterReadall()
	{
		if (APPLICATION_ID == 2) {
		} else {
			return true;
		}

		if (strtolower($this->get('template_view_path_node')) == 'adminpagetitle') {
		} else {
			return true;
		}

		/** Standard Headings */
		$resource = Services::Registry()->get('RouteParameters', 'extension_title');
		$page_type = Services::Registry()->get('RouteParameters', 'page_type');
		$request_action = Services::Registry()->get('RouteParameters', 'request_action');

		$heading1 = Services::Language()->translate(ucfirst(strtolower($resource)));

		$heading2 = Services::Language()->translate(ucfirst(strtolower($request_action))
			. ' ' . ucfirst(strtolower($page_type)));

		/** Create Buttons for Page Type */
		$buttonArray = $this->setButtonArray($page_type);
		$buttonCount = count($buttonArray);

		/** Build Query Results for View */
		$query_results = array();

		$row = new \stdClass();
		$row->heading1 = $heading1;
		$row->heading2 = $heading2;
		$row->button_count = $buttonCount;
		$row->button_array = '';

		if ($buttonCount === 0) {
			$row->button_array = null;
		} else {
			foreach ($buttonArray as $button) {
				$row->button_array .= trim($button);
			}
		}

		$query_results[] = $row;

		$this->data = $query_results;

		return true;
	}

	/**
	 * Create Buttons based upon Page Type
	 *
	 * @return array
	 * @since  1.0
	 */
	protected function setButtonArray($page_type)
	{

		if ($page_type == 'item') {
			return $this->setItemButtons();

		} elseif ($page_type == 'form') {
			return $this->setEditButtons();
		}

		return false;
	}

	/**
	 * Create Item Buttons
	 *
	 * @return array
	 * @since  1.0
	 */
	protected function setItemButtons()
	{

		$buttons = array();

		/** Button 1: Back to Grid */
		$buttonTitle = str_replace(
			' ',
			'&nbsp;',
			htmlentities(Services::Language()->translate('Back to Grid'), ENT_COMPAT, 'UTF-8')
		);
		$buttonIcon = htmlentities('icon-list-alt', ENT_COMPAT, 'UTF-8');
		$linkURL = '/admin/' . Services::Registry()->get('Parameters', 'catalog_slug');
		$buttonArray = 'button_title:'
			. trim($buttonTitle)
			. ','
			. 'button_type:secondary,'
			. 'button_link:'
			. $linkURL
			. ','
			. 'button_icon_prepend:'
			. $buttonIcon;

		$buttons[] = '{{' . trim($buttonArray) . '}}';

		/** Button 2: Edit Button */
		$buttonTitle = str_replace(
			' ',
			'&nbsp;',
			htmlentities(Services::Language()->translate('Edit'), ENT_COMPAT, 'UTF-8')
		);
		$buttonIcon = htmlentities('icon-edit', ENT_COMPAT, 'UTF-8');
		$linkURL = Services::Registry()->get('Plugindata', 'page_url') . '/edit';
		$buttonArray = 'button_title:'
			. trim($buttonTitle)
			. ','
			. 'button_type:secondary,'
			. 'button_link:'
			. $linkURL
			. ','
			. 'button_icon_prepend:'
			. $buttonIcon;

		$buttons[] = '{{' . trim($buttonArray) . '}}';

		/** Button 3: Delete Button */
		$buttonTitle = str_replace(
			' ',
			'&nbsp;',
			htmlentities(Services::Language()->translate('Delete'), ENT_COMPAT, 'UTF-8')
		);
		$buttonIcon = htmlentities('icon-trash', ENT_COMPAT, 'UTF-8');
		$linkURL = Services::Registry()->get('Plugindata', 'page_url') . '/delete';
		$buttonArray = 'button_title:'
			. trim($buttonTitle)
			. ','
			. 'button_type:alert,'
			. 'button_link:'
			. $linkURL
			. ','
			. 'button_icon_prepend:'
			. $buttonIcon;

		$buttons[] = '{{' . trim($buttonArray) . '}}';

		return $buttons;
	}

	/**
	 * Create Edit Buttons
	 *
	 * @return array
	 * @since  1.0
	 */
	protected function setEditButtons()
	{
		$buttons = array();

		/** Button 1: Back to Grid */
		$buttonTitle = str_replace(
			' ',
			'&nbsp;',
			htmlentities(Services::Language()->translate('Back to Grid'), ENT_COMPAT, 'UTF-8')
		);
		$buttonIcon = htmlentities('icon-list-alt', ENT_COMPAT, 'UTF-8');
		$linkURL = '/admin/' . Services::Registry()->get('Parameters', 'catalog_slug');
		$buttonArray = 'button_title:'
			. trim($buttonTitle)
			. ','
			. 'button_type:secondary,'
			. 'button_link:'
			. $linkURL
			. ','
			. 'button_icon_prepend:'
			. $buttonIcon;

		$buttons[] = '{{' . trim($buttonArray) . '}}';

		/** Button 2: Revisions */
		$buttonTitle = str_replace(
			' ',
			'&nbsp;',
			htmlentities(Services::Language()->translate('Revisions'), ENT_COMPAT, 'UTF-8')
		);
		$buttonLinkExtra = htmlentities('data-reveal-id:item-revisions', ENT_COMPAT, 'UTF-8');
		$buttonIcon = htmlentities('icon-time', ENT_COMPAT, 'UTF-8');
		$linkURL = $linkURL = Services::Registry()->get('Plugindata', 'page_url');
		$buttonArray = 'button_title:'
			. $buttonTitle
			. ','
			. 'button_type:secondary,'
			. 'button_link:' .
			$linkURL . ','
			. 'button_link_extra:'
			. $buttonLinkExtra . ','
			. 'button_icon_prepend:'
			. $buttonIcon;

		$buttons[] = '{{' . trim($buttonArray) . '}}';

		/** Button 3: Options */
		$buttonTitle = str_replace(
			' ',
			'&nbsp;',
			htmlentities(Services::Language()->translate('Options'), ENT_COMPAT, 'UTF-8')
		);
		$buttonLinkExtra = htmlentities('data-reveal-id:item-options', ENT_COMPAT, 'UTF-8');
		$buttonIcon = htmlentities('icon-wrench', ENT_COMPAT, 'UTF-8');
		$linkURL = Services::Registry()->get('Plugindata', 'page_url');
		$buttonArray = 'button_title:'
			. $buttonTitle
			. ','
			. 'button_type:secondary,'
			. 'button_link:'
			. $linkURL
			. ','
			. 'button_link_extra:'
			. $buttonLinkExtra
			. ','
			. 'button_icon_prepend:'
			. $buttonIcon;

		$buttons[] = '{{' . trim($buttonArray) . '}}';

		return $buttons;
	}
}
