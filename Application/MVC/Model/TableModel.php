<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Application\MVC\Model;

defined('MOLAJO') or die;

/**
 * Actions
 *
 * @package     Molajo
 * @subpackage  Model
 * @since       1.0
 */
Class TableModel extends DisplayModel
{
	/**
	 * Constructor.
	 *
	 * @param  $id
	 * @since  1.0
	 */
	public function __construct($table = null, $id = null, $path = null)
	{
		$this->name = get_class($this);

		return parent::__construct($table, $id, $path);
	}
}
