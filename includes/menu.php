<?php
/**
 * @version		$Id: menu.php 20196 2011-01-09 02:40:25Z ian $
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('JPATH_PLATFORM') or die;

/**
 * MolajoMenu class
 *
 * @package		Joomla.Site
 * @subpackage	Application
 * @since		1.5
 */
class MolajoMenuSite extends JMenu
{
	/**
	 * Loads the entire menu table into memory.
	 *
	 * @return array
	 */
	public function load()
	{
		$cache = JFactory::getCache('mod_menu', '');  // has to be mod_menu or this cache won't get cleaned

		if (!$data = $cache->get('menu_items'.JFactory::getLanguage()->getTag())) {
			// Initialise variables.
			$db		= JFactory::getDbo();
			$app	= JFactory::getApplication();
			$query	= $db->getQuery(true);

			$query->select('m.id, m.menutype, m.title, m.alias, m.path AS route, m.link, m.type, m.level');
			$query->select('m.browserNav, m.access, m.params, m.home, m.img, m.template_style_id');
			$query->select('m.component_id, m.parent_id, m.language');
			$query->select('e.element as component');
			$query->from('#__menu AS m');
			$query->leftJoin('#__extensions AS e ON m.component_id = e.extension_id');
			$query->where('m.published = 1');
			$query->where('m.parent_id > 0');
			$query->where('m.client_id = 0');
			$query->order('m.lft');

            $acl = new MolajoACL ();
            $acl->getQueryInformation ('', $query, 'viewaccess', array('table_prefix'=>'m'));

            $db->setQuery($query->__toString());
            $menus = $db->loadObjectList();

            if ($db->getError()) {
                JFactory::getApplication()->enqueueMessage($db->getErrorMsg(), 'error');
                return false;
            }

            if (count($menus) > 0) {
                foreach ($menus as &$menu) {
                    // Get parent information.
                    $parent_tree = array();
                    if (isset($menus[$menu->parent_id])) {
                        $parent_tree  = $menus[$menu->parent_id]->tree;
                    }

                    // Create tree.
                    $parent_tree[] = $menu->id;
                    $menu->tree = $parent_tree;

                    // Create the query array.
                    $url = str_replace('index.php?', '', $menu->link);
                    $url = str_replace('&amp;','&',$url);

                    parse_str($url, $menu->query);
                }
            }
			$cache->store($menus, 'menu_items'.JFactory::getLanguage()->getTag());

			$this->_items = $menus;
		} else {
			$this->_items = $data;
		}
	}
    
	/**
	 * Gets menu items by attribute
	 *
	 * @param	string	$attributes	The field name
	 * @param	string	$values		The value of the field
	 * @param	boolean	$firstonly	If true, only returns the first item found
	 *
	 * @return	array
	 */
	public function getItems($attributes, $values, $firstonly = false)
	{
		$attributes = (array) $attributes;
		$values = (array) $values;
		$app	= JFactory::getApplication();
		// Filter by language if not set
		if ($app->isSite() && $app->getLanguageFilter() && !array_key_exists('language',$attributes)) {
			$attributes[]='language';
			$values[]=array(JFactory::getLanguage()->getTag(), '*');
		}
		return parent::getItems($attributes, $values, $firstonly);
	}

	/**
	 * Get menu item by id
	 *
	 * @param	string	$language	The language code.
	 *
	 * @return	object	The item object
	 * @since	1.5
	 */
	function getDefault($language='*')
	{
		if (array_key_exists($language, $this->_default) && JFactory::getApplication()->getLanguageFilter()) {
			return $this->_items[$this->_default[$language]];
		}
		else if (array_key_exists('*', $this->_default)) {
			return $this->_items[$this->_default['*']];
		}
		else {
			return 0;
		}
	}
}
