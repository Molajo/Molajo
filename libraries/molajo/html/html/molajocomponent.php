<?php
/**
 * @version     $id: molajocomponent
 * @package     Molajo
 * @subpackage  HTML Class
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Molajo Components in a Select List
 *
 * @static
 * @package		Joomla.Framework
 * @subpackage	HTML
 * @since		1.5
 */
abstract class MolajoHtmlMolajoComponent
{
	/**
	 * @var	array	Cached array of the component items.
	 */
	protected static $results = array();

	/**
	 * Returns an array of Molajo Components
	 *
	 * @param	string	The extension option.
	 * @param	array	An array of configuration options. 
	 *
	 * @return	array
	 */
	public function options()
	{
            $options = array();
            $db	= MolajoFactory::getDbo();
            $query = $db->getQuery(true);

            $query->select('DISTINCT component_option as value, option_value_literal as text');
            $query->from('#__configuration');
            $query->where('option_id = 0');
            $query->where('component_option <> "core"');

            $db->setQuery($query);
            $results = $db->loadObjectList();
            if ($db->getErrorNum()) {
                MolajoError::raiseWarning(500, $db->getErrorMsg());
                return;
            }

            if (count($results) > 0) {

                $translated = array();
                foreach($results as $item) {
                    $translated[$i]->value = MolajoText::_($item->value);
                    $translated[$i]->text = $item->text;
                    $i++;
                }

                /** sort by translated value **/
                $translatedSorted = array();
                sort($translated);
                $translatedSorted = $translated;

                /** load into select list **/
                foreach ($translatedSorted as $item) {
                    $options[]	= MolajoHTML::_('select.option', $item->value, MolajoText::_($item->text));
                }
            }

            return $options;
	}
}