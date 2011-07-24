<?php
/**
 * @package     Molajo
 * @subpackage  Extend
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

/**
 * Supports an HTML select list of Author Names (List of Titles from Author Category Articles)
 *
 * @package		Joomla.Framework
 * @subpackage	Form
 * @since		1.6
 */
class JFormFieldAuthor extends JFormFieldList
{
    /**
     * The form field type.
     *
     * @var		string
     * @since	1.6
     */
    public $tagtype = 'Author';

    /**
     * Method to get the field options.
     *
     * @return	array	The field option objects.
     * @since	1.6
     */
    protected function getOptions()
    {
        /** retrieve extend parameters **/
        $extendContentPlugin =& JPluginHelper::getPlugin('content', 'extend');
        $extendParams = new JParameter($extendContentPlugin->params);

        /** initialization **/
        $options = array();
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        /** build query **/

        /** build query **/
        $query->select('a.id AS value, a.title AS text');
        $query->from('#__content a');
        $query->from('#__categories b');
        $query->where('a.state = 1');
        $query->where('b.id = a.catid');
        $query->where('b.title = "Authors"');
        $query->order('a.ordering, a.title');

        /** run query **/
        $db->setQuery($query);
        $options = $db->loadObjectList();
        if ($db->getErrorNum()) {
            JError::raiseWarning(500, $db->getErrorMsg());
        }
        return $options;
    }
}