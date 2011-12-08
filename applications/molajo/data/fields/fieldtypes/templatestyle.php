<?php
/**
 * @package     Molajo
 * @subpackage  Form
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
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
class MolajoFormFieldTemplateStyle extends MolajoFormFieldGroupedList
{
    /**
     * The form field type.
     *
     * @var    string
     * @since  1.0
     */
    public $type = 'TemplateStyle';

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
        $lang = MolajoFactory::getLanguage();

        // Get the application and application_id.
        $applicationName = $this->element['application'] ? (string)$this->element['application'] : 'site';
        $application = MolajoApplicationHelper::getApplicationInfo($applicationName, true);

        // Get the template.
        $template = (string)$this->element['template'];

        // Get the database object and a new query object.
        $db = MolajoFactory::getDBO();
        $query = $db->getQuery(true);

        // Build the query.
        $query->select('s.id, s.title, e.name as name, s.template');
        $query->from('#__template_styles as s');
        $query->where('s.application_id = ' . (int)$application->id);
        $query->order('template');
        $query->order('title');
        if ($template) {
            $query->where('s.template = ' . $db->quote($template));
        }
        $query->join('LEFT', '#__extensions as e on e.element=s.template');
        $query->where('e.enabled=1');

        // Set the query and load the styles.
        $db->setQuery($query);
        $styles = $db->loadObjectList();

        // Build the grouped list array.
        if ($styles) {
            foreach ($styles as $style) {
                $template = $style->template;
                $lang->load('template_' . $template . '.sys', $application->path, null, false, false)
                || $lang->load('template_' . $template . '.sys', $application->path . '/templates/' . $template, null, false, false)
                || $lang->load('template_' . $template . '.sys', $application->path, $lang->getDefault(), false, false)
                || $lang->load('template_' . $template . '.sys', $application->path . '/templates/' . $template, $lang->getDefault(), false, false);
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
