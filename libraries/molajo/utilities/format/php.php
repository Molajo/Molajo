<?php
/**
 * @package     Molajo
 * @subpackage  MolajoFactory
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * PHP class format handler for MolajoRegistry
 *
 * @package     Joomla.Platform
 * @subpackage  Registry
 * @since       11.1
 */
class MolajoRegistryFormatPHP extends MolajoRegistryFormat {

	/**
	 * Converts an object into a php class string.
	 *	- NOTE: Only one depth level is supported.
	 *
	 * @param   object   Data Source Object
	 * @param   array    Parameters used by the formatter
	 * @return  string   Config class formatted string
	 */
	public function objectToString($object, $params = array())
	{
		// Build the object variables string
		$vars = '';
		foreach (get_object_vars($object) as $k => $v) {
			if (is_scalar($v)) {
				$vars .= "\tpublic $". $k . " = '" . addcslashes($v, '\\\'') . "';\n";
			} else if (is_array($v)) {
				$vars .= "\tpublic $". $k . " = " . $this->_getArrayString($v) . ";\n";
			}
		}

		$str = "<?php\nclass ".$params['class']." {\n";
		$str .= $vars;
		$str .= "}";

		// Use the closing tag if it not set to false in parameters.
		if (!isset($params['closingtag']) || $params['closingtag'] !== false) {
			$str .= "\n?>";
		}

		return $str;
	}

	/**
	 * Placeholder method
	 *
	 * @return  boolean  True
	 */
	function stringToObject($data, $namespace='')
	{
		return true;
	}

	protected function _getArrayString($a)
	{
		$s = 'array(';
		$i = 0;
		foreach ($a as $k => $v) {
			$s .= ($i) ? ', ' : '';
			$s .= '"'.$k.'" => ';
			if (is_array($v)) {
				$s .= $this->_getArrayString($v);
			} else {
				$s .= '"'.addslashes($v).'"';
			}
			$i++;
		}
		$s .= ')';
		return $s;
	}
}
