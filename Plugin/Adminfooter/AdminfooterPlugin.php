<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Adminfooter;

use Molajo\Plugin\Content\ContentPlugin;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class AdminfooterPlugin extends ContentPlugin
{
	/**
	 * Prepares data for the Administrator Footer
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function onAfterRead()
	{
		if (strtolower($this->get('template_view_path_node')) == 'adminfooter') {
		} else {
			return true;
		}

		$current_year = Services::Date()->getDate()->format('Y');

		/** Retrieve created_by field definition */
		$first_year_field = $this->getField('copyright_first_year');
		if ($first_year_field == false) {
			$first_year = null;
		} else {
			$first_year = $this->getFieldValue($first_year_field);
		}

		if ($first_year == null || $first_year == '') {
			$ccDateSpan = $current_year;

		} elseif ($first_year == $current_year) {
			$ccDateSpan = $first_year;

		} else {
			$ccDateSpan = $first_year . '-' . $current_year;
		}

		$copyright_holder_field = $this->getField('copyright_holder');
		if ($copyright_holder_field == false) {
			$copyright_holder = null;
		} else {
			$copyright_holder = $this->getFieldValue($copyright_holder_field);
		}
		if ($copyright_holder == null || $copyright_holder == '') {
			$copyright_holder = 'Molajo';
		}

		$copyright_statement = '&#169;' . ' ' . $ccDateSpan . ' ' . $copyright_holder;
		$this->saveField(null, Services::Language()->translate('copyright_statement'), $copyright_statement);

		return true;
	}
}
