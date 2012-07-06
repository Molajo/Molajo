<?php
/**
 * @package   Molajo
 * @subpackage  Attributes
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
defined('MOLAJO') or die;

/**
 * MolajoAttributeChecked
 *
 * Populate Checked Attribute
 *
 * @package   Molajo
 * @subpackage  Attributes
 * @since       1.0
 */
class CheckedControllerFormfield extends InputControllerFormfield
{
	/**
	 * __construct
	 *
	 * Method to instantiate the Checked object.
	 *
	 * @param array $input
	 * @param array $query_results
	 *
	 * @return void
	 *
	 * @since   1.0
	 */
	public function __construct($input = array(), $query_results = array())
	{
		parent::__construct();
		parent::__set('name', 'Checked');
		parent::__set('input', $input);
		parent::__set('resultset', $query_results);
	}

	/**
	 * setValue
	 *
	 * Method to set the Attribute Value
	 *
	 * @return array $query_results
	 *
	 * @since   1.1
	 */
	protected function setValue()
	{
		$checked = $this->element['checked'];
		$value = $this->verifyValue($checked);

		parent::__set('value', $value);

		/** $this->query_results */
		$this->query_results[0]['checked'] = $this->value;

		/** return array of attributes */

		return $this->query_results;
	}

	/**
	 * verifyValue
	 *
	 * Method to determine whether or not the Checked exists
	 *
	 * @return array $query_results
	 *
	 * @since   1.1
	 */
	protected function verifyValue($checked)
	{
		if ((boolean)$checked === true) {
			$value = 'checked="checked"';
		} else {
			$value = '';
		}

		return $value;
	}
}
