<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Includer;

use Molajo\Service\Services;
use Molajo\Includer;

defined('MOLAJO') or die;

/**
 * User Interface Includer
 *
 * @package     Molajo
 * @subpackage  Includer
 * @since       1.0
 */
class UiIncluder extends Includer
{

	/**
	 * process - render include statement for configured UI Library
	 *
	 * @param   $attributes
	 *
	 * @return mixed
	 * @since   1.0
	 */
	public function process($attributes = array())
	{
		$this->attributes = $attributes;
		parent::getAttributes();

		$includer = '<include:template';

		foreach ($this->attributes as $key => $value) {
			if ($key == 'name') {
				$temp = Services::Registry()->get('Configuration', 'Ui-' . $value);
				if (trim($temp) == '' || $temp === null) {
					$value = 'Ui-' . $value . '-foundation ';
				}
			}
			$includer .= ' ' . trim($key) . '=' . trim($value);
		}
		$includer .= '/>';

		return $includer;
	}
}
