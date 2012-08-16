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
	 * process - render include statement for template configured for UI Object
	 *
	 * @param   $attributes <include:ui attr1=x attr2=y attr3=z ... />
	 *
	 * @return mixed 		<include:template attr1=x attr2=y attr3=z ... />
	 * @since   1.0
	 */
	public function process($attributes = array())
	{
		/** Retrieve Attributes from the include statement */
		$this->attributes = $attributes;
		parent::getAttributes();

		/** Retrieve Configuration option for User Interface Template */
		$name = Services::Registry()->get('Configuration', 'ui-template-' . $this->name);
		if (trim($name) == '' || $name === null) {
			$name = 'ui-' . $this->name;
		}

		/** Create Include Template Statement and return as rendered output */
		return '<include:template '
			. ' name=' . $this->name
			. explode(' ', $this->attributes)
			. '/>';

	}
}
