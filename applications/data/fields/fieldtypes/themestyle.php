<?php
/**
 * @package     Molajo
 * @subpackage  Form
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2012 Cristina Solano. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Form Field class for the Joomla Framework.
 *
 * @package    Molajo
 * @subpackage  Form
 * @since       1.0
 */
class MolajoFormFieldThemeStyle extends MolajoFormFieldGroupedList
{
    /**
     * The form field type.
     *
     * @var    string
     * @since  1.0
     */
    public $type = 'ThemeStyle';

    /**
     * Method to get the field option groups.
     *
     * @return  array  The field option objects as a nested array in groups.
     * @since   1.0
     */
    protected function getGroups()
    {
        // Initialize variables.
        $groups = array();
        $lang = MolajoController::getApplication()->getLanguage();

        // Get the application and application_id.
        $applicationName = $this->element['application'] ? (string)$this->element['application'] : 'site';
        $application = MolajoApplicationHelper::getApplicationInfo($applicationName, true);

        // Get the theme.
        $theme = (string)$this->element['theme'];

        // Get the database object and a new query object.
        $db = MolajoController::getDbo();
        $query = $db->getQuery(true);

        // Build the query.
        $query->select('s.id, s.title, e.name as name, s.theme');
        $query->from('#__theme_styles as s');
        $query->where('s.application_id = ' . (int)$application->id);
        $query->order('theme');
        $query->order('title');
        if ($theme) {
            $query->where('s.theme = ' . $db->quote($theme));
        }
        $query->join('LEFT', '#__extensions as e on e.element=s.theme');
        $query->where('e.enabled=1');

        // Set the query and load the styles.
        $db->setQuery($query);
        $styles = $db->loadObjectList();

        // Build the grouped list array.
        if ($styles) {
            foreach ($styles as $style) {
                $theme = $style->theme;
                $lang->load('theme_' . $theme . '.sys', $application->path, null, false, false)
                || $lang->load('theme_' . $theme . '.sys', $application->path . '/themes/' . $theme, null, false, false)
                || $lang->load('theme_' . $theme . '.sys', $application->path, $lang->getDefault(), false, false)
                || $lang->load('theme_' . $theme . '.sys', $application->path . '/themes/' . $theme, $lang->getDefault(), false, false);
                $name = MolajoTextHelper::_($style->name);
                // Initialize the group if necessary.
                if (!isset($groups[$name])) {
                    $groups[$name] = array();
                }

                $groups[$name][] = MolajoHTML::_('select.option', $style->id, $style->title);
            }
        }

        // Merge any additional groups in the XML definition.
        $groups = array_merge(parent::getGroups(), $groups);

        return $groups;
    }
}
