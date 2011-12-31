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
 * Menu table
 *
 * @package     Joomla.Platform
 * @subpackage  Table
 * @since       11.1
 */
class MolajoTableMenuitem extends MolajoTableNested
{
    /**
     * Constructor
     *
     * @param database A database connector object
     */
    public function __construct(&$db)
    {
        parent::__construct('#__content', 'id', $db);

        // Set the default access level.
        $this->access = (int)MolajoController::getApplication()->get('access');
    }

    /**
     * Overloaded bind function
     *
     * @param   array  $hash  named array
     *
     * @return  mixed  null is operation was satisfactory, otherwise returns an error
     *
     * @see     MolajoTable:bind
     * @since   1.0
     */
    public function bind($array, $ignore = '')
    {
        // Verify that the default home menu is not unset
        if ($this->home == '1' && $this->language == '*' && ($array['home'] == '0')) {
            $this->setError(MolajoTextHelper::_('MOLAJO_DATABASE_ERROR_MENU_CANNOT_UNSET_DEFAULT_DEFAULT'));
            return false;
        }
        //Verify that the default home menu set to "all" languages" is not unset
        if ($this->home == '1' && $this->language == '*' && ($array['language'] != '*')) {
            $this->setError(MolajoTextHelper::_('MOLAJO_DATABASE_ERROR_MENU_CANNOT_UNSET_DEFAULT'));
            return false;
        }

        // Verify that the default home menu is not unpublished
        if ($this->home == '1' && $this->language == '*' && $array['published'] != '1') {
            $this->setError(MolajoTextHelper::_('MOLAJO_DATABASE_ERROR_MENU_UNPUBLISH_DEFAULT_HOME'));
            return false;
        }

        if (isset($array['parameters']) && is_array($array['parameters'])) {
            $registry = new JRegistry();
            $registry->loadArray($array['parameters']);
            $array['parameters'] = (string)$registry;
        }

        return parent::bind($array, $ignore);
    }

    /**
     * Overloaded check function
     *
     * @return  boolean
     * @see     MolajoTable::check
     * @since   1.0
     */
    public function check()
    {
        // If the alias field is empty, set it to the title.
        $this->alias = trim($this->alias);
        if ((empty($this->alias)) && ($this->type != 'alias' && $this->type != 'url')) {
            $this->alias = $this->title;
        }

        // Make the alias URL safe.
        $this->alias = MolajoController::getApplication()->stringURLSafe($this->alias);
        if (trim(str_replace('-', '', $this->alias)) == '') {
            $this->alias = MolajoController::getDate()->format('Y-m-d-H-i-s');
        }

        // Cast the home property to an int for checking.
        $this->home = (int)$this->home;

        // Verify that a first level menu item alias is not 'component'.
        if ($this->parent_id == 1 && $this->alias == 'component') {
            $this->setError(MolajoTextHelper::_('MOLAJO_DATABASE_ERROR_MENU_ROOT_ALIAS_COMPONENT'));
            return false;
        }

        // Verify that a first level menu item alias is not the name of a folder.
        if ($this->parent_id == 1 && in_array($this->alias, JFolder::folders(MOLAJO_BASE_FOLDER))) {
            $this->setError(MolajoTextHelper::sprintf('MOLAJO_DATABASE_ERROR_MENU_ROOT_ALIAS_FOLDER', $this->alias, $this->alias));
            return false;
        }

        // Verify that the home item a component.
        if ($this->home && $this->type != 'component') {
            $this->setError(MolajoTextHelper::_('MOLAJO_DATABASE_ERROR_MENU_HOME_NOT_COMPONENT'));
            return false;
        }

        return true;
    }

    /**
     * Overloaded store function
     *
     * @return  boolean
     * @see     MolajoTable::store
     * @since   1.0
     */
    public function store($updateNulls = false)
    {
        $db = MolajoController::getDbo();
        // Verify that the alias is unique
        $table = MolajoTable::getInstance('Menuitem', 'MolajoTable');
        if ($table->load(array('alias' => $this->alias, 'parent_id' => $this->parent_id, 'client_id' => $this->client_id)) && ($table->id != $this->id || $this->id == 0)) {
            if ($this->menu_id == $table->menu_id) {
                $this->setError(MolajoTextHelper::_('MOLAJO_DATABASE_ERROR_MENU_UNIQUE_ALIAS'));
            }
            else {
                $this->setError(MolajoTextHelper::_('MOLAJO_DATABASE_ERROR_MENU_UNIQUE_ALIAS_ROOT'));
            }
            return false;
        }
        // Verify that the home page for this language is unique
        if ($this->home == '1') {
            $table = MolajoTable::getInstance('Menuitem', 'MolajoTable');
            if ($table->load(array('home' => '1', 'language' => $this->language))) {
                if ($table->checked_out && $table->checked_out != $this->checked_out) {
                    $this->setError(MolajoTextHelper::_('MOLAJO_DATABASE_ERROR_MENU_DEFAULT_CHECKIN_USER_MISMATCH'));
                    return false;
                }
                $table->home = 0;
                $table->checked_out = 0;
                $table->checked_out_time = $db->getNullDate();
                $table->store();
            }
            // Verify that the home page for this menu is unique.
            if ($table->load(array('home' => '1', 'menu_id' => $this->menu_id)) && ($table->id != $this->id || $this->id == 0)) {
                $this->setError(MolajoTextHelper::_('MOLAJO_DATABASE_ERROR_MENU_HOME_NOT_UNIQUE_IN_MENU'));
                return false;
            }
        }
        if (!parent::store($updateNulls)) {
            return false;
        }
        // Get the new path in case the node was moved
        $pathNodes = $this->getPath();
        $segments = array();
        foreach ($pathNodes as $node) {
            // Don't include root in path
            if ($node->alias != 'root') {
                $segments[] = $node->alias;
            }
        }
        $newPath = trim(implode('/', $segments), ' /\\');
        // Use new path for partial rebuild of table
        // rebuild will return positive integer on success, false on failure
        return ($this->rebuild($this->{$this->_tbl_key}, $this->lft, $this->level, $newPath) > 0);
    }
}
