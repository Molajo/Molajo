<?php
/**
 * @package    Molajo
 * @subpackage  Application
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * MolajoMenu Class
 *
 * @package     Molajo
 * @subpackage  Application
 * @since       1.0
 */
class MolajoMenu extends JObject
{
    /**
     * Array to hold the menu items
     *
     * @var    array
     * @since   1.0
     */
    protected $_items = array();

    /**
     * Identifier of the default menu item
     *
     * @var    integer
     * @since   1.0
     */
    protected $_default = array();

    /**
     * Identifier of the active menu item
     *
     * @var    integer
     * @since   1.0
     */
    protected $_active = 0;

    /**
     * getInstance
     *
     * Returns a MolajoMenu object
     *
     * @param   string  $application   The name of the application
     * @param   array   $options  An associative array of options
     *
     * @return  MolajoMenu  A menu object.
     * @since   1.0
     */
    public static function getInstance($application, $options = array())
    {
        static $instances;

        if (!isset($instances)) {
            $instances = array();
        }

        if (empty($instances[$application])) {
            $classname = 'Molajo'.ucfirst($application).'Menu';
            $instance = new $classname($options);

            $instances[$application] = & $instance;
        }

        return $instances[$application];
    }

    /**
     * __construct
     *
     * Class constructor
     *
     * Loads all Menus and Menu Items for the Application
     *
     * @param   array    $options  An array of configuration options.
     *
     * @return  Menu object
     * @since   1.0
     */
    public function __construct($options = array())
    {
        $this->load();

        foreach ($this->_items as $k => $item)
        {
            if ($item->home) {
                $this->_default[$item->language] = $item->id;
            }

            /** Menu Parameters */
            $menu = new JRegistry;
            $menu->loadJSON($item->parameters);
            $item->menu_parameters = $menu;

             /** Menu Custom Fields */
            $menuFields = new JRegistry;
            $menuFields->loadJSON($item->custom_fields);
            $item->menu_custom_fields = $menuFields;

            /** Menu Item Parameters */
            $menuItem = new JRegistry;
            $menuItem->loadJSON($item->menu_item_parameters);
            $item->menu_item_parameters = $menuItem;

            /** Menu Item Custom Fields */
            $menuItemFields = new JRegistry;
            $menuItemFields->loadJSON($item->menu_item_custom_fields);
            $item->menu_item_custom_fields = $menuItemFields;
        }
    }

    /**
     * getItem
     *
     * Get menu item by id
     *
     * @param   integer  $id  The item id
     *
     * @return  mixed  The item object, or null if not found
     * @since   1.0
     */
    public function getItem($id)
    {
        $result = null;
        if (isset($this->_items[$id])) {
            $result = &$this->_items[$id];
        }

        return $result;
    }

    /**
     * setDefault
     *
     * Set the default item by id and language code.
     *
     * @param   integer  $id            The menu item id.
     * @param   string   $language    The language cod (since 1.6).
     *
     * @return  boolean  True, if succesfull
     * @since   1.0
     */
    public function setDefault($id, $language = '')
    {
        if (isset($this->_items[$id])) {
            $this->_default[$language] = $id;
            return true;
        }

        return false;
    }

    /**
     * getDefault
     *
     * Get the default item by language code.
     *
     * @param   string   $language   The language code, default * meaning all.
     *
     * @return  object   The item object
     * @since   1.0
     */
    function getDefault($language = '*')
    {
        if (array_key_exists($language, $this->_default)) {
            return $this->_items[$this->_default[$language]];
        }
        else if (array_key_exists('*', $this->_default)) {
            return $this->_items[$this->_default['*']];
        }
        else {
            return 0;
        }
    }

    /**
     * setActive
     *
     * Set the default item by id
     *
     * @param   integer  $id    The item id
     *
     * @return  mixed  If successfull the active item, otherwise null
     */
    public function setActive($id)
    {
        if (isset($this->_items[$id])) {
            $this->_active = $id;
            $result = &$this->_items[$id];
            return $result;
        }

        return null;
    }

    /**
     * getActive
     *
     * Get menu item by id.
     *
     * @return  object  The item object.
     */
    public function getActive()
    {
        if ($this->_active) {
            $item = &$this->_items[$this->_active];
            return $item;
        }

        return null;
    }

    /**
     * getItems
     *
     * Get all menu items for a specific menu using the extension_instance_id
     *
     * @param   string   $attributes  The field name (extension_instance_id)
     * @param   string   $values      The value of the field
     * @param   boolean  $firstonly   If true, only returns the first item found
     *
     * @return  array
     */
    public function getItems($attributes, $values, $firstonly = false)
    {
        $items = null;
        $attributes = (array)$attributes;
        $values = (array)$values;

        foreach ($this->_items as $item)
        {
            if (!is_object($item)) {
                continue;
            }

            $test = true;
            for ($i = 0, $count = count($attributes); $i < $count; $i++)
            {
                if (is_array($values[$i])) {
                    if (!in_array($item->$attributes[$i], $values[$i])) {
                        $test = false;
                        break;
                    }

                } else {
                    if ($item->$attributes[$i] != $values[$i]) {
                        $test = false;
                        break;
                    }
                }
            }
            if ($test) {
                if ($firstonly) {
                    return $item;
                }

                $items[] = $item;
            }
        }

        return $items;
    }

    /**
     * getParameters
     *
     * Gets the parameter object for a certain menu item
     *
     * @param   integer  $id  The item id
     *
     * @return  JRegistry  A JRegistry object
     */
    public function getParameters($id)
    {
        if ($menu = $this->getItem($id)) {
            return $menu->menu_item_parameters;
        }
        else {
            return new JRegistry;
        }
    }

    /**
     * getMenu
     *
     * Getter for the menu array
     *
     * @return  array
     */
    public function getMenu()
    {
        return $this->_items;
    }

    /**
     * authorise
     *
     * All Menus and Menu Items are already filtered by ACL in Molajo
     * @deprecated
     */
    public function authorise($id)
    {
        return true;
    }

    /**
     * load
     *
     * Loads the menus
     *
     * @return  array
     * @since   1.0
     */
    public function load()
    {
        $this->_items  = MolajoExtensionHelper::getExtensions(MOLAJO_CONTENT_TYPE_EXTENSION_MENUS);

        foreach($this->_items as &$item) {

            $parent_tree = array();
            if (isset($this->_items[$item->menu_item_parent_id]->tree)) {
                $parent_tree = $this->_items[$item->menu_item_parent_id]->tree;
            }

            // Create tree.
            $parent_tree[] = $item->id;
            $item->tree = $parent_tree;

            // Create the query array.
            $url = str_replace('index.php?', '', $item->request);
            $url = str_replace('&amp;', '&', $url);
            parse_str($url, $item->query);
        }

        return $this->_items;
    }
}
