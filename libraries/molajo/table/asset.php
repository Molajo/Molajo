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
 * Asset Table Class
 *
 * @package     Molajo
 * @subpackage  Table
 * @since       1.0
 * @link
 */
class MolajoTableAsset extends MolajoTable {

    /**
     * The primary key of the asset.
     *
     * @var int
     */
    public $id = null;

    /**
     * content table associated with the asset.
     *
     * @var string
     */
    public $content_table = null;


    /**
     * @param database A database connector object
     */
    public function __construct(&$db)
    {
        parent::__construct('#__assets', 'id', $db);
    }

	/**
	 * Method to provide a shortcut to binding, checking and storing a MolajoTable
	 * instance to the database table.  The method will check a row in once the
	 * data has been stored and if an ordering filter is present will attempt to
	 * reorder the table rows based on the filter.  The ordering filter is an instance
	 * property name.  The rows that will be reordered are those whose value matches
	 * the MolajoTable instance for the property specified.
	 *
	 * @param   mixed   An associative array or object to bind to the MolajoTable instance.
	 * @param   string  Filter for the order updating
	 * @param   mixed   An optional array or space separated list of properties
	 *					to ignore while binding.
	 *
	 * @return  boolean  True on success.
	 *
	 * @link	http://docs.joomla.org/MolajoTable/save
	 * @since   11.1
	 */
	public function save($src, $orderingFilter = '', $ignore = '')
	{

		// Run any sanity checks on the instance and verify that it is ready for storage.
		if (!$this->check()) {
			return false;
		}

		// Attempt to store the properties to the database table.
		if (!$this->store()) {
			return false;
		}

		// Set the error to empty and return true.
		$this->setError('');

		return true;
	}


    /**
     * Check for necessary data
     *
     * @return  bool  True if the instance is sane and able to be stored in the database.
     *
     * @link	http://docs.joomla.org/MolajoTable/check
     * @since   11.1
     */
    public function check()
    {
        if ($this->content_table == null) {
            $this->setError(JText::_('ASSET_TABLE_MUST_HAVE_CONTENT_TABLE_VALUE'));
            return false;
        }
        return true;
    }
}