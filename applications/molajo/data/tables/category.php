<?php
/**
 * @package     Molajo
 * @subpackage  Table
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Category Table Class
 *
 * @package     Molajo
 * @subpackage  Table
 * @since       1.0
 * @link
 */
class MolajoTableCategory extends MolajoTableNested
{
    /**
     * @param database A database connector object
     */
    public function __construct(&$db)
    {
        parent::__construct('#__categories', 'id', $db);

        $this->access = (int)MolajoFactory::getConfig()->get('access');
    }

    /**
     * Method to compute the default name of the asset.
     * The default name is in the form `table_name.id`
     * where id is the value of the primary key of the table.
     *
     * @return  string
     */
    protected function _getAssetName()
    {
        $k = $this->_tbl_key;
        return $this->extension . '.category.' . (int)$this->$k;
    }

    /**
     * Method to return the title to use for the asset table.
     *
     * @return  string
     * @since   1.0
     */
    protected function _getAssetTitle()
    {
        return $this->title;
    }

    /**
     * Get the parent asset id for the record
     *
     * @return  integer
     */
    protected function _getAssetParentId($table = null, $id = null)
    {
        // Initialise variables.
        $assetId = null;
        $db = $this->getDbo();

        // This is a category under a category.
        if ($this->parent_id > 1) {
            // Build the query to get the asset id for the parent category.
            $query = $db->getQuery(true);
            $query->select('asset_id');
            $query->from('#__categories');
            $query->where('id = ' . (int)$this->parent_id);

            // Get the asset id from the database.
            $db->setQuery($query);
            if ($result = $db->loadResult()) {
                $assetId = (int)$result;
            }
        }
            // This is a category that needs to parent with the extension.
        elseif ($assetId === null) {
            // Build the query to get the asset id for the parent category.
            $query = $db->getQuery(true);
            $query->select('id');
            $query->from('#__assets');
            $query->where('name = ' . $db->quote($this->extension));

            // Get the asset id from the database.
            $db->setQuery($query);
            if ($result = $db->loadResult()) {
                $assetId = (int)$result;
            }
        }

        // Return the asset id.
        if ($assetId) {
            return $assetId;
        } else {
            return parent::_getAssetParentId($table, $id);
        }
    }

    /**
     * Override check function
     *
     * @return  bool
     *
     * @see        MolajoTable::check
     * @since   1.0
     */
    public function check()
    {
        // Check for a title.
        if (trim($this->title) == '') {
            $this->setError(MolajoTextHelper::_('MOLAJO_DATABASE_ERROR_MUSTCONTAIN_A_TITLE_CATEGORY'));
            return false;
        }
        $this->alias = trim($this->alias);
        if (empty($this->alias)) {
            $this->alias = $this->title;
        }

        $this->alias = MolajoApplication::stringURLSafe($this->alias);
        if (trim(str_replace('-', '', $this->alias)) == '') {
            $this->alias = MolajoFactory::getDate()->format('Y-m-d-H-i-s');
        }

        return true;
    }

    /**
     * Overloaded bind function.
     *
     * @param   array  named array
     *
     * @return  null|string    null is operation was satisfactory, otherwise returns an error
     *
     * @see        MolajoTable:bind
     * @since   1.0
     */
    public function bind($array, $ignore = '')
    {
        if (isset($array['parameters']) && is_array($array['parameters'])) {
            $registry = new JRegistry();
            $registry->loadArray($array['parameters']);
            $array['parameters'] = (string)$registry;
        }

        if (isset($array['metadata']) && is_array($array['metadata'])) {
            $registry = new JRegistry();
            $registry->loadArray($array['metadata']);
            $array['metadata'] = (string)$registry;
        }

        // Bind the rules.
        if (isset($array['rules']) && is_array($array['rules'])) {
            $rules = new JRules($array['rules']);
            $this->setRules($rules);
        }

        return parent::bind($array, $ignore);
    }

    /**
     * Overriden MolajoTable::store to set created/modified and user id.
     *
     * @param   boolean  True to update fields even if they are null.
     *
     * @return  boolean  True on success.
     *
     * @since   1.0
     */
    public function store($updateNulls = false)
    {
        $date = MolajoFactory::getDate();
        $user = MolajoFactory::getUser();

        if ($this->id) {
            // Existing category
            $this->modified_time = $date->toMySQL();
            $this->modified_user_id = $user->get('id');
        } else {
            // New category
            $this->created_time = $date->toMySQL();
            $this->created_user_id = $user->get('id');
        }
        // Verify that the alias is unique
        $table = MolajoTable::getInstance('Category', 'MolajoTable');
        if ($table->load(array('alias' => $this->alias,
                              'parent_id' => $this->parent_id,
                              'extension' => $this->extension))
            && ($table->id != $this->id || $this->id == 0)
        ) {

            $this->setError(MolajoTextHelper::_('MOLAJO_DATABASE_ERROR_CATEGORY_UNIQUE_ALIAS'));
            return false;
        }
        return parent::store($updateNulls);
    }
}
