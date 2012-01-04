<?php
/**
 * @package     Joomla.Platform
 * @subpackage  HTML
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('MOLAJO') or die;

/**
 * Utility class working with content language select lists
 *
 * @package     Joomla.Platform
 * @subpackage  HTML
 * @since       11.1
 */
abstract class MolajoHtmlContentLanguage
{
    /**
     * Cached array of the content language items.
     *
     * @var    array
     * @since  11.1
     */
    protected static $items = null;

    /**
     * Get a list of the available content language items.
     *
     * @param   boolean  $all        True to include All (*)
     * @param   boolean  $translate  True to translate All
     *
     * @return  string
     *
     * @since   1.0
     *
     * @see     JFormFieldContentLanguage
     */
    public static function existing($all = false, $translate = false)
    {
        if (empty(self::$items)) {
            // Get the database object and a new query object.
            $db = MolajoController::getDbo();
            $query = $db->getQuery(true);

            // Build the query.
            $query->select('a.lang_code AS value, a.title AS text, a.title_native');
            $query->from('#__languages AS a');
            $query->where('a.published >= 0');
            $query->order('a.title');

            // Set the query and load the options.
            $db->setQuery($query);
            self::$items = $db->loadObjectList();
            if ($all) {
                array_unshift(self::$items, new JObject(array('value' => '*', 'text' => $translate
                                                      ? MolajoTextHelper::alt('JALL', 'language') : 'JALL_LANGUAGE')));
            }

            // Detect errors
            if ($db->getErrorNum()) {
                MolajoError::raiseWarning(500, $db->getErrorMsg());
            }
        }
        return self::$items;
    }
}
