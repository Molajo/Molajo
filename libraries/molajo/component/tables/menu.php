<?php
/**
 * @package     Molajo
 * @subpackage  Table
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Menu Types table
 *
 * @package     Joomla.Platform
 * @subpackage  Table
 * @since       11.1
 */
class MolajoTableMenu extends MolajoTable
{
	/**
	 * Constructor
	 *
	 * @param database A database connector object
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__menus', 'id', $db);
	}

	/**
	 * @return boolean
	 */
	function check()
	{
		$this->menu_id = JApplication::stringURLSafe($this->menu_id);
		if (empty($this->menu_id)) {
			$this->setError(JText::_('MOLAJO_DATABASE_ERROR_MENU_EMPTY'));
			return false;
		}

		if (trim($this->title) == '') {
			$this->title = $this->menu_id;
		}

		$db	= $this->getDbo();

		// Check for unique menu_id.
		$db->setQuery(
			'SELECT COUNT(id)' .
			' FROM #__menus' .
			' WHERE menu_id = '.$db->quote($this->menu_id).
			'  AND id <> '.(int) $this->id
		);

		if ($db->loadResult())
		{
			$this->setError(JText::sprintf('MOLAJO_DATABASE_ERROR_MENU_EXISTS', $this->menu_id));
			return false;
		}

		return true;
	}
	/**
	 * Method to store a row in the database from the MolajoTable instance properties.
	 * If a primary key value is set the row with that primary key value will be
	 * updated with the instance property values.  If no primary key value is set
	 * a new row will be inserted into the database with the properties from the
	 * MolajoTable instance.
	 *
	 * @param   boolean True to update fields even if they are null.
	 * @return  boolean  True on success.
	 * @since   11.1
	 * @link	http://docs.joomla.org/MolajoTable/store
	 */
	public function store($updateNulls = false)
	{
		if ($this->id) {
			// Get the user id
			$userId = JFactory::getUser()->id;

			// Get the old value of the table
			$table = MolajoTable::getInstance('Menu','MolajoTable');
			$table->load($this->id);

			// Verify that no items are cheched out
			$query = $this->_db->getQuery(true);
			$query->select('id');
			$query->from('#__menu_items');
			$query->where('menu_id='.$this->_db->quote($table->menu_id));
			$query->where('checked_out !='.(int) $userId);
			$query->where('checked_out !=0');
			$this->_db->setQuery($query);
			if ($this->_db->loadRowList()) {
				$this->setError(JText::sprintf('MOLAJO_DATABASE_ERROR_STORE_FAILED', get_class($this), JText::_('MOLAJO_DATABASE_ERROR_MENU_CHECKOUT')));
				return false;
			}

			// Verify that no module for this menu are cheched out
			$query = $this->_db->getQuery(true);
			$query->select('id');
			$query->from('#__modules');
			$query->where('module='.$this->_db->quote('mod_menu'));
			$query->where('params LIKE '.$this->_db->quote('%"menu_id":'.json_encode($table->menu_id).'%'));
			$query->where('checked_out !='.(int) $userId);
			$query->where('checked_out !=0');
			$this->_db->setQuery($query);
			if ($this->_db->loadRowList()) {
				$this->setError(JText::sprintf('MOLAJO_DATABASE_ERROR_STORE_FAILED', get_class($this), JText::_('MOLAJO_DATABASE_ERROR_MENU_CHECKOUT')));
				return false;
			}

			// Update the menu items
			$query = $this->_db->getQuery(true);
			$query->update('#__menu_items');
			$query->set('menu_id='.$this->_db->quote($this->menu_id));
			$query->where('menu_id='.$this->_db->quote($table->menu_id));
			$this->_db->setQuery($query);
			if (!$this->_db->query()) {
				$this->setError(JText::sprintf('MOLAJO_DATABASE_ERROR_STORE_FAILED', get_class($this), $this->_db->getErrorMsg()));
				return false;
			}

			// Update the module items
			$query = $this->_db->getQuery(true);
			$query->update('#__modules');
			$query->set('params=REPLACE(params,'.$this->_db->quote('"menu_id":'.json_encode($table->menu_id)).','.$this->_db->quote('"menu_id":'.json_encode($this->menu_id)).')');
			$query->where('module='.$this->_db->quote('mod_menu'));
			$query->where('params LIKE '.$this->_db->quote('%"menu_id":'.json_encode($table->menu_id).'%'));
			$this->_db->setQuery($query);
			if (!$this->_db->query()) {
				$this->setError(JText::sprintf('MOLAJO_DATABASE_ERROR_STORE_FAILED', get_class($this), $this->_db->getErrorMsg()));
				return false;
			}
		}
		return parent::store($updateNulls);
	}
	/**
	 * Method to delete a row from the database table by primary key value.
	 *
	 * @param   mixed    An optional primary key value to delete.  If not set the
	 *					instance property value is used.
	 * @return  boolean  True on success.
	 * @since   11.1
	 * @link	http://docs.joomla.org/MolajoTable/delete
	 */
	public function delete($pk = null)
	{
		// Initialise variables.
		$k = $this->_tbl_key;
		$pk = (is_null($pk)) ? $this->$k : $pk;

		// If no primary key is given, return false.
		if ($pk !== null)
		{
			// Get the user id
			$userId = JFactory::getUser()->id;

			// Get the old value of the table
			$table = MolajoTable::getInstance('Menu','MolajoTable');
			$table->load($pk);

			// Verify that no items are cheched out
			$query = $this->_db->getQuery(true);
			$query->select('id');
			$query->from('#__menu_items');
			$query->where('menu_id='.$this->_db->quote($table->menu_id));
			$query->where('client_id=0');
			$query->where('(checked_out NOT IN (0,'.(int) $userId.') OR home=1 AND language='.$this->_db->quote('*').')');
			$this->_db->setQuery($query);
			if ($this->_db->loadRowList()) {
				$this->setError(JText::sprintf('MOLAJO_DATABASE_ERROR_DELETE_FAILED', get_class($this), JText::_('MOLAJO_DATABASE_ERROR_MENU')));
				return false;
			}

			// Verify that no module for this menu are cheched out
			$query = $this->_db->getQuery(true);
			$query->select('id');
			$query->from('#__modules');
			$query->where('module='.$this->_db->quote('mod_menu'));
			$query->where('params LIKE '.$this->_db->quote('%"menu_id":'.json_encode($table->menu_id).'%'));
			$query->where('checked_out !='.(int) $userId);
			$query->where('checked_out !=0');
			$this->_db->setQuery($query);
			if ($this->_db->loadRowList()) {
				$this->setError(JText::sprintf('MOLAJO_DATABASE_ERROR_DELETE_FAILED', get_class($this), JText::_('MOLAJO_DATABASE_ERROR_MENU')));
				return false;
			}

			// Delete the menu items
			$query = $this->_db->getQuery(true);
			$query->delete();
			$query->from('#__menu_items');
			$query->where('menu_id='.$this->_db->quote($table->menu_id));
			$query->where('client_id=0');
			$this->_db->setQuery($query);
			if (!$this->_db->query()) {
				$this->setError(JText::sprintf('MOLAJO_DATABASE_ERROR_DELETE_FAILED', get_class($this), $this->_db->getErrorMsg()));
				return false;
			}

			// Update the module items
			$query = $this->_db->getQuery(true);
			$query->delete();
			$query->from('#__modules');
			$query->where('module='.$this->_db->quote('mod_menu'));
			$query->where('params LIKE '.$this->_db->quote('%"menu_id":'.json_encode($table->menu_id).'%'));
			$this->_db->setQuery($query);
			if (!$this->_db->query()) {
				$this->setError(JText::sprintf('MOLAJO_DATABASE_ERROR_DELETE_FAILED', get_class($this), $this->_db->getErrorMsg()));
				return false;
			}
		}
		return parent::delete($pk);
	}
}