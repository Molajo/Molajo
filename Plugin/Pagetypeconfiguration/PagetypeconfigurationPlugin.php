<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Pagetypeconfiguration;

use Molajo\Plugin\Plugin\Plugin;
use Molajo\Service\Services;
use Molajo\Helpers;

defined('MOLAJO') or die;

/**
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class PagetypeconfigurationPlugin extends Plugin
{
    /**
     * Prepares Configuration Data
     *
     * @return 	boolean
     * @since	1.0
     */
    public function onBeforeParse()
    {
        if (strtolower($this->get('page_type')) == 'configuration') {
        } else {
            return true;
        }

        $resource_model_type = $this->get('model_type');
        $resource_model_name = $this->get('model_name');

        Helpers::Content()->getResourceExtensionParameters(
			(int) $this->parameters['criteria_extension_instance_id']
		);

        $namespace = $this->get('configuration_namespace');
        $namespace = ucfirst(strtolower($namespace));

		$temp = $this->get('configuration_array');
		$pages = explode('{{', $temp);

		/** Determine Current Page Number */
		$temp = Services::Registry()->get('Parameters', 'request_filters', array());
		$filters = explode(',', $temp);

		$page = 1;
		if ($filters == '' || count($filters) == 0) {
			$page = 1;
		} else {
			foreach ($filters as $x) {
				if (trim($x) == '') {
				} else {
					$pair = explode(':', $x);
					if (strtolower($pair[0]) == 'page') {
						$page = (int) $pair[1];
						break;
					}
				}
			}
		}

		if ($page < count($pages)) {
		} else {
			$page = 1;
		}
		$page_number = $page;

		/** Resource Submenu: Links to various Form Pages (Tabs) - ex. Basic, Metadata, Fields, etc. */
		$pageArray = array();
		$i = 0;
		foreach ($pages as $item) {
			if ($item == '') {
			} else {
				$i++;
				$row = new \stdClass();
				$row->id = $i;
				if ($i == $item) {
					$row->current = 1;
				} else {
					$row->current = 0;
				}
				$row->title = substr($item, 0, strpos($item, ','));
				$row->url = Services::Registry()->get('Plugindata', 'page_url') . '/page/' . $i;

				$pageArray[] = $row;
			}
		}
		Services::Registry()->set('Plugindata', 'ResourceSubmenu', $pageArray);

		/**
		 * $pageFieldsets - two fields (page_count and page_array) for the Extension Template Page Title
		 *	and to create the include statement for the Template defined in page_form_fieldset_handler_view
		 *  The registry used by the form handler view is defined in page_include_parameter
		 */
		$page_array = '{{' . $pages[$page_number];

		$pageFieldsets = Services::Form()->setPageArray(
			$page_array,
			'configuration',
			$resource_model_type,
			$resource_model_name,
			$this->get('criteria_extension_instance_id'),
			array()
		);

		/** Prepare recordset for Page Form Fieldset View */
		$page_array = $this->getPages($pageFieldsets[0]->page_array, $pageFieldsets[0]->page_count);

		$this->set('model_name', 'Plugindata');
		$this->set('model_type', 'dbo');
		$this->set('model_query_object', 'getPlugindata');
		$this->set('model_parameter', 'PrimaryRequestQueryResults');

		$this->parameters['model_name'] = 'Plugindata';
		$this->parameters['model_type'] = 'dbo';

		Services::Registry()->set('Plugindata', 'PrimaryRequestQueryResults', $page_array);
		Services::Registry()->get('Plugindata', 'PrimaryRequestQueryResults', $page_array);


		return true;
    }

	/**
	 * Get Form Page Fieldsets
	 *
	 * @param $pages
	 * @return array
	 */
	protected function getPages($pages, $page_count)
	{
		$page_array = array();
		$temp_array = array();
		$temp = explode('}}', $pages);

		foreach ($temp as $set) {
			$set = str_replace(',', ' ', $set);
			$set = str_replace(':', '=', $set);
			$set = str_replace('{{', '', $set);
			$set = str_replace('http=', 'http:', $set);
			if (trim($set) == '') {
			} else {
				$temp_array[] = trim($set);
			}
		}

		$current_page_number = count($temp_array);
		$current_page_number_word = $this->convertNumberToWord($current_page_number);

		foreach ($temp_array as $set) {
			$fields = explode(' ', $set);
			foreach ($fields as $field) {
				$temp = explode('=', $field);
				$pairs[$temp[0]] = $temp[1];
			}

			$row = new \stdClass();
			foreach ($pairs as $key=>$value) {
				$row->$key = $value;
				$row->current_page_number = $current_page_number;
				$row->current_page_number_word = $current_page_number_word;
				$row->total_page_count = $page_count;
			}
			$page_array[] = $row;
		}

		return $page_array;
	}

	/**
	 * convertNumberToWord
	 *
	 * Converts numbers from 1-24 as their respective written word
	 *
	 * @return string
	 * @since   1.0
	 */
	public function convertNumberToWord($number)
	{
		$key = $number-1;
		$words = array('one','two','three','four','five','six','seven','eight','nine','ten','eleven','twelve','thirteen','fourteen','fifteen','sixteen','seventeen','eighteen','nineteen','twenty','twentyone','twentytwo','twentythree','twentyfour');
		if (array_key_exists($key, $words)) {
			return $words[$key];
		}

		return false;
	}
}
