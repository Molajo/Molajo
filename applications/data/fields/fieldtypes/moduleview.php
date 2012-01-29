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
 * Form Field to display a list of the views for a module view from the module or theme overrides.
 *
 * @package    Molajo
 * @subpackage  Form
 * @since       1.0
 */
class MolajoFormFieldModuleView extends MolajoFormField
{
    /**
     * The form field type.
     *
     * @var    string
     * @since  1.0
     */
    protected $type = 'ModuleView';

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

        // Get the theme.
        $theme = (string)$this->element['theme'];
        $theme = preg_replace('#\W#', '', $theme);

        // Get the style.
        if ($this->form instanceof MolajoForm) {
            $theme_id = $this->form->getValue('theme_id');
        }

        $theme_id = preg_replace('#\W#', '', $theme_id);

        // If an extension and view are present build the options.
        if ($module && $application) {

            // Load language file
            $lang = MolajoController::getApplication()->getLanguage();
            $lang->load($module . '.sys', $application->path, null, false, false)
            || $lang->load($module . '.sys', $application->path . '/modules/' . $module, null, false, false)
            || $lang->load($module . '.sys', $application->path, $lang->getDefault(), false, false)
            || $lang->load($module . '.sys', $application->path . '/modules/' . $module, $lang->getDefault(), false, false);

            // Get the database object and a new query object.
            $db = MolajoController::getDbo();
            $query = $db->getQuery(true);

            // Build the query.
            $query->select('element, name');
            $query->from('#__extensions as e');
            $query->where('e.application_id = ' . (int)$application_id);
            $query->where('e.type = ' . $db->quote('theme'));
            $query->where('e.enabled = 1');

            if ($theme) {
                $query->where('e.element = ' . $db->quote($theme));
            }

            if ($theme_id) {
                $query->join('LEFT', '#__theme_styles as s on s.theme=e.element');
                $query->where('s.id=' . (int)$theme_id);
            }

            // Set the query and load the themes.
            $db->setQuery($query);
            $themes = $db->loadObjectList('element');

            // Check for a database error.
            if ($db->getErrorNum()) {
                MolajoError::raiseWarning(500, $db->getErrorMsg());
            }

            // Build the search paths for module views.
            $module_path = JPath::clean($application->path . '/modules/' . $module . '/views');

            // Prepare array of component views
            $module_views = array();

            // Prepare the grouped list
            $groups = array();

            // Add the view options from the module path.
            if (is_dir($module_path) && ($module_views = JFolder::files($module_path, '^[^_]*\.php$'))) {
                // Create the group for the module
                $groups['_'] = array();
                $groups['_']['id'] = $this->id . '__';
                $groups['_']['text'] = MolajoTextHelper::sprintf('JOPTION_FROM_MODULE');
                $groups['_']['items'] = array();

                foreach ($module_views as $file)
                {
                    // Add an option to the module group
                    $value = JFile::stripExt($file);
                    $text = $lang->hasKey($key = strtoupper($module . '_VIEW_' . $value)) ? MolajoTextHelper::_($key)
                            : $value;
                    $groups['_']['items'][] = MolajoHTML::_('select.option', '_:' . $value, $text);
                }
            }

            // Loop on all themes
            if ($themes) {
                foreach ($themes as $theme)
                {
                    // Load language file
                    $lang->load('theme_' . $theme->element . '.sys', $application->path, null, false, false)
                    || $lang->load('theme_' . $theme->element . '.sys', $application->path . '/themes/' . $theme->element, null, false, false)
                    || $lang->load('theme_' . $theme->element . '.sys', $application->path, $lang->getDefault(), false, false)
                    || $lang->load('theme_' . $theme->element . '.sys', $application->path . '/themes/' . $theme->element, $lang->getDefault(), false, false);

                    $theme_path = JPath::clean($application->path . '/themes/' . $theme->element . '/html/' . $module);

                    // Add the view options from the theme path.
                    if (is_dir($theme_path) && ($files = JFolder::files($theme_path, '^[^_]*\.php$'))) {
                        foreach ($files as $i => $file)
                        {
                            // Remove view that already exist in component ones
                            if (in_array($file, $module_views)) {
                                unset($files[$i]);
                            }
                        }

                        if (count($files)) {
                            // Create the group for the theme
                            $groups[$theme->element] = array();
                            $groups[$theme->element]['id'] = $this->id . '_' . $theme->element;
                            $groups[$theme->element]['text'] = MolajoTextHelper::sprintf('JOPTION_FROM_THEME', $theme->name);
                            $groups[$theme->element]['items'] = array();

                            foreach ($files as $file)
                            {
                                // Add an option to the theme group
                                $value = JFile::stripExt($file);
                                $text = $lang->hasKey($key = strtoupper('TPL_' . $theme->element . '_' . $module . '_VIEW_' . $value))
                                        ? MolajoTextHelper::_($key) : $value;
                                $groups[$theme->element]['items'][] = MolajoHTML::_('select.option', $theme->element . ':' . $value, $text);
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
