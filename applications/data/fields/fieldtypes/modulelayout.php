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
 * Form Field to display a list of the layouts for a module view from the module or template overrides.
 *
 * @package    Molajo
 * @subpackage  Form
 * @since       1.0
 */
class MolajoFormFieldModuleLayout extends MolajoFormField
{
    /**
     * The form field type.
     *
     * @var    string
     * @since  1.0
     */
    protected $type = 'ModuleLayout';

    /**
     * Method to get the field calendar.
     *
     * @return  string  The field calendar.
     * @since   1.0
     */
    protected function getInput()
    {
        // Initialize variables.

        // Get the application id.
        $applicationName = $this->element['application_id'];

        // Get the application id.
        $application_id = $this->element['application_id'];

        if (is_null($application_id) && $this->form instanceof MolajoForm) {
            $application_id = $this->form->getValue('application_id');
        }
        $application_id = (int)$application_id;

        $application = MolajoApplicationHelper::getApplicationInfo($application_id);

        // Get the module.
        $module = (string)$this->element['module'];

        if (empty($module) && ($this->form instanceof MolajoForm)) {
            $module = $this->form->getValue('module');
        }

        $module = preg_replace('#\W#', '', $module);

        // Get the template.
        $template = (string)$this->element['template'];
        $template = preg_replace('#\W#', '', $template);

        // Get the style.
        if ($this->form instanceof MolajoForm) {
            $template_id = $this->form->getValue('template_id');
        }

        $template_id = preg_replace('#\W#', '', $template_id);

        // If an extension and view are present build the options.
        if ($module && $application) {

            // Load language file
            $lang = MolajoFactory::getLanguage();
            $lang->load($module . '.sys', $application->path, null, false, false)
            || $lang->load($module . '.sys', $application->path . '/modules/' . $module, null, false, false)
            || $lang->load($module . '.sys', $application->path, $lang->getDefault(), false, false)
            || $lang->load($module . '.sys', $application->path . '/modules/' . $module, $lang->getDefault(), false, false);

            // Get the database object and a new query object.
            $db = MolajoFactory::getDbo();
            $query = $db->getQuery(true);

            // Build the query.
            $query->select('element, name');
            $query->from('#__extensions as e');
            $query->where('e.application_id = ' . (int)$application_id);
            $query->where('e.type = ' . $db->quote('template'));
            $query->where('e.enabled = 1');

            if ($template) {
                $query->where('e.element = ' . $db->quote($template));
            }

            if ($template_id) {
                $query->join('LEFT', '#__template_styles as s on s.template=e.element');
                $query->where('s.id=' . (int)$template_id);
            }

            // Set the query and load the templates.
            $db->setQuery($query);
            $templates = $db->loadObjectList('element');

            // Check for a database error.
            if ($db->getErrorNum()) {
                MolajoError::raiseWarning(500, $db->getErrorMsg());
            }

            // Build the search paths for module layouts.
            $module_path = JPath::clean($application->path . '/modules/' . $module . '/layouts');

            // Prepare array of component layouts
            $module_layouts = array();

            // Prepare the grouped list
            $groups = array();

            // Add the layout options from the module path.
            if (is_dir($module_path) && ($module_layouts = JFolder::files($module_path, '^[^_]*\.php$'))) {
                // Create the group for the module
                $groups['_'] = array();
                $groups['_']['id'] = $this->id . '__';
                $groups['_']['text'] = MolajoTextHelper::sprintf('JOPTION_FROM_MODULE');
                $groups['_']['items'] = array();

                foreach ($module_layouts as $file)
                {
                    // Add an option to the module group
                    $value = JFile::stripExt($file);
                    $text = $lang->hasKey($key = strtoupper($module . '_LAYOUT_' . $value)) ? MolajoTextHelper::_($key)
                            : $value;
                    $groups['_']['items'][] = MolajoHTML::_('select.option', '_:' . $value, $text);
                }
            }

            // Loop on all templates
            if ($templates) {
                foreach ($templates as $template)
                {
                    // Load language file
                    $lang->load('template_' . $template->element . '.sys', $application->path, null, false, false)
                    || $lang->load('template_' . $template->element . '.sys', $application->path . '/templates/' . $template->element, null, false, false)
                    || $lang->load('template_' . $template->element . '.sys', $application->path, $lang->getDefault(), false, false)
                    || $lang->load('template_' . $template->element . '.sys', $application->path . '/templates/' . $template->element, $lang->getDefault(), false, false);

                    $template_path = JPath::clean($application->path . '/templates/' . $template->element . '/html/' . $module);

                    // Add the layout options from the template path.
                    if (is_dir($template_path) && ($files = JFolder::files($template_path, '^[^_]*\.php$'))) {
                        foreach ($files as $i => $file)
                        {
                            // Remove layout that already exist in component ones
                            if (in_array($file, $module_layouts)) {
                                unset($files[$i]);
                            }
                        }

                        if (count($files)) {
                            // Create the group for the template
                            $groups[$template->element] = array();
                            $groups[$template->element]['id'] = $this->id . '_' . $template->element;
                            $groups[$template->element]['text'] = MolajoTextHelper::sprintf('JOPTION_FROM_TEMPLATE', $template->name);
                            $groups[$template->element]['items'] = array();

                            foreach ($files as $file)
                            {
                                // Add an option to the template group
                                $value = JFile::stripExt($file);
                                $text = $lang->hasKey($key = strtoupper('TPL_' . $template->element . '_' . $module . '_LAYOUT_' . $value))
                                        ? MolajoTextHelper::_($key) : $value;
                                $groups[$template->element]['items'][] = MolajoHTML::_('select.option', $template->element . ':' . $value, $text);
                            }
                        }
                    }
                }
            }
            // Compute attributes for the grouped list
            $attr = $this->element['size'] ? ' size="' . (int)$this->element['size'] . '"' : '';

            // Prepare HTML code
            $html = array();

            // Compute the current selected values
            $selected = array($this->value);

            // Add a grouped list
            $html[] = MolajoHTML::_('select.groupedlist', $groups, $this->name, array('id' => $this->id, 'group.id' => 'id', 'list.attr' => $attr, 'list.select' => $selected));

            return implode($html);
        }
        else {

            return '';
        }
    }
}
