<?php
/**
 * @version     $id: version.php
 * @package     Molajo
 * @subpackage  Application
 * @copyright   Copyright (C) 2011 Individual Molajo Contributors. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die();

/**
 * Asset Table Class
 *
 * @package     Molajo
 * @subpackage  Database
 * @since       1
 * @link
 */
class MolajoTableAsset extends JTable
{
	/**
	 * The primary key of the asset.
	 *
	 * @var int
	 */
	public $id = null;

	/**
	 * The source table to which the asset belongs
	 *
	 * @var string
	 */
	public $table = null;

	/**
	 * @param database A database connector object
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__assets', 'id', $db);
	}

	/**
	 * Method to load an asset by it's name.
	 *
	 * @param   string  The name of the asset.
	 *
	 * @return  integer
	 */
	public function loadByName($name)
	{
		// Get the asset id for the asset.
		$this->_db->setQuery(
			'SELECT '.$this->_db->quoteName('id') .
			' FROM '.$this->_db->quoteName('#__assets') .
			' WHERE '.$this->_db->quoteName('name').' = '.$this->_db->Quote($name)
		);
		$assetId = (int) $this->_db->loadResult();
		if (empty($assetId)) {
			return false;
		}
		// Check for a database error.
		if ($error = $this->_db->getErrorMsg())
		{
			$this->setError($error);
			return false;
		}
		return $this->load($assetId);
	}

	/**
	 * Asset that the nested set data is valid.
	 *
	 * @return  bool  True if the instance is sane and able to be stored in the database.
	 *
	 * @link	http://docs.joomla.org/JTable/check
	 * @since   11.1
	 */
	public function check()
	{
		$this->parent_id = (int) $this->parent_id;

		// JTableNested does not allow parent_id = 0, override this.
		if ($this->parent_id > 0)
		{
			$this->_db->setQuery(
				'SELECT COUNT(id)' .
				' FROM '.$this->_db->quoteName($this->_tbl).
				' WHERE '.$this->_db->quoteName('id').' = '.$this->parent_id
			);
			if ($this->_db->loadResult()) {
				return true;
			}
			else
			{
				if ($error = $this->_db->getErrorMsg()) {
					$this->setError($error);
				}
				else {
					$this->setError(JText::_('JLIB_DATABASE_ERROR_INVALID_PARENT_ID'));
				}
				return false;
			}
		}

		return true;
	}
}
