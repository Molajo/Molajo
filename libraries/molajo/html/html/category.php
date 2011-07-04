<?php
/**
 * @version     $id: category
 * @package     Molajo
 * @subpackage  HTML Class
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die();

/**
 * Utility class for categories
 *
 * @static
 * @package		Joomla.Framework
 * @subpackage	HTML
 * @since		1.5
 */
abstract class MolajoHtmlCategory
{
	/**
	 * @var	array	Cached array of the category items.
	 */
	protected static $items = array();

	/**
	 * Returns an array of categories for the given extension.
	 *
	 * @param	string	The extension option.
	 * @param	array	An array of configuration options. By default, only published and unpulbished categories are returned.
	 *
	 * @return	array
	 */
	public static function options($extension=null, $config = array('filter.published' => array(0,1)))
	{
		$hash = md5($extension.'.'.serialize($config));

		if (!isset(self::$items[$hash])) {
			$config	= (array) $config;
			$db	= JFactory::getDbo();
			$query	= $db->getQuery(true);

			$query->select('a.id, a.title, a.level, a.extension');
			$query->from('#__categories AS a');
			$query->where('a.parent_id > 0');

			// Filter on extension.
                        if ($extension == null) {
                        } else {
                            $query->where('extension = '.$db->quote($extension));
                        }
			// Filter on the published state
			if (isset($config['filter.published'])) {
				if (is_numeric($config['filter.published'])) {
					$query->where('a.published = '.(int) $config['filter.published']);
				} else if (is_array($config['filter.published'])) {
					JArrayHelper::toInteger($config['filter.published']);
					$query->where('a.published IN ('.implode(',', $config['filter.published']).')');
				}
			}

			$query->order('a.extension, a.lft');

			$db->setQuery($query);
			$results = $db->loadObjectList();

                        $lang = JFactory::getLanguage();
			self::$items[$hash] = array();

                        $categoryExtension = '';

			foreach ($results as $item) {

                            if ($extension == null) {
                                if ($categoryExtension == $item->extension) {
                                } else {
                                   if ($categoryExtension == '') {
                                   } else {
                                       self::$items[$hash][] = JHTML::_('select.option',  '</OPTGROUP>' );
                                   }
                                   /*   Process new Extension Group */
                                   $categoryExtension = $item->extension;
                                   $lang->load($item->extension, JPATH_ADMINISTRATOR, null, false, false);
                                   $categoryExtensionText = JText::_($item->extension);

                                   self::$items[$hash][] = JHTML::_('select.option',  '<OPTGROUP>', ' - '.$categoryExtensionText.' - ' );
                                }
                            }

                           $repeat = ( $item->level - 1 >= 0 ) ? $item->level - 1 : 0;
                           self::$items[$hash][] = JHtml::_('select.option', $item->id, str_repeat('- ', $repeat).$item->title);
			}
		}

		return self::$items[$hash];
	}

	/**
	 * Returns an array of categories for the given extension.
	 *
	 * @param	string	The extension option.
	 * @param	array	An array of configuration options. By default, only published and unpulbished categories are returned.
	 *
	 * @return	array
	 */
	public static function categories($extension, $config = array('filter.published' => array(0,1)))
	{
		$hash = md5($extension.'.'.serialize($config));

		if (!isset(self::$items[$hash])) {
			$config	= (array) $config;
			$db		= JFactory::getDbo();
			$query	= $db->getQuery(true);

			$query->select('a.id, a.title, a.level, a.parent_id');
			$query->from('#__categories AS a');
			$query->where('a.parent_id > 0');

			// Filter on extension.
			$query->where('extension = '.$db->quote($extension));

			// Filter on the published state
			if (isset($config['filter.published'])) {
				if (is_numeric($config['filter.published'])) {
					$query->where('a.published = '.(int) $config['filter.published']);
				} else if (is_array($config['filter.published'])) {
					JArrayHelper::toInteger($config['filter.published']);
					$query->where('a.published IN ('.implode(',', $config['filter.published']).')');
				}
			}

			$query->order('a.lft');

			$db->setQuery($query);
			$items = $db->loadObjectList();

			// Assemble the list options.
			self::$items[$hash] = array();

				foreach ($items as $item) {
					$repeat = ( $item->level - 1 >= 0 ) ? $item->level - 1 : 0;
					$item->title = str_repeat('- ', $repeat).$item->title;
					self::$items[$hash][] = JHtml::_('select.option', $item->id, $item->title);
				}
				// Special "Add to root" option:
				self::$items[$hash][] = JHtml::_('select.option', '1', JText::_('JLIB_HTML_ADD_TO_ROOT'));
		}

		return self::$items[$hash];
	}
}