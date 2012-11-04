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

		$page_array = '{{' . $pages[$page];

        $pageFieldsets = Services::Form()->setPageArray(
            $resource_model_type,
            $resource_model_name,
            $namespace,
            $page_array,
            'configuration_',
			'Formpage',
            'Formpage',
            $this->get('criteria_extension_instance_id'),
            array()
        );

		/** Resource Submenu - ex. Basic, Metadata, Fields, Page, Template, Wrap, etc. */
		$pageFieldsets[0]->page_count = count($pages) - 1;

		$pageArray = array();
		$i = 0;
		foreach ($pages as $page) {
			if ($page == '') {
			} else {
				$i++;
				$row = new \stdClass();
				$row->id = $i;
				if ($i == $page) {
					$row->current = 1;
				} else {
					$row->current = 0;
				}
				$row->title = substr($page, 0, strpos($page, ','));
				$row->url = Services::Registry()->get('Plugindata', 'page_url') . '/page/' . $i;

				$pageArray[] = $row;
			}
		}
		Services::Registry()->set('Plugindata', 'ResourceSubmenu', $pageArray);
//STOP AMY. this below - needs to go into the PrimaryRequestQueryResults
//$array = Services::Registry()->get('Plugindata', 'Formpageconfigbasic');
//		echo '<pre>';
//		var_dump($array);
//		die;

		/** Prepare recordset for Page Form Fieldset View */
		$page_array = $this->getPages($pageFieldsets[0]->page_array, $pageFieldsets[0]->page_count);

		$this->set('model_name', 'Plugindata');
		$this->set('model_type', 'dbo');
		$this->set('model_query_object', 'getPlugindata');
		$this->set('model_parameter', 'PrimaryRequestQueryResults');

		$this->parameters['model_name'] = 'Plugindata';
		$this->parameters['model_type'] = 'dbo';

		Services::Registry()->set('Plugindata', 'PrimaryRequestQueryResults', $page_array);

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

		$number_of_pages = $this->convertNumberToWord(count($temp_array));

		foreach ($temp_array as $set) {

			$fields = explode(' ', $set);
			foreach ($fields as $field) {
				$temp = explode('=', $field);
				$pairs[$temp[0]] = $temp[1];
			}

			$row = new \stdClass();
			foreach ($pairs as $key=>$value) {
				$row->$key = $value;
				$row->number_of_pages = $number_of_pages;
				$row->count_of_pages = $page_count;
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
