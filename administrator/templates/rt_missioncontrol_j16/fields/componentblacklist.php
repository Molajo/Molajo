<?php
/**
 * @version  1.6.2 June 9, 2011
 * @author  �RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license  http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
defined('JPATH_BASE') or die();

jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('checkboxes');

/**
 * @package     missioncontrol
 * @subpackage  admin.elements
 */
class JFormFieldComponentBlacklist extends JFormFieldCheckboxes  {
    /**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'ComponentBlacklist';

	/**
	 * Flag to tell the field to always be in multiple values mode.
	 *
	 * @var		boolean
	 * @since	1.6
	 */
	protected $forceMultiple = true;

    public function getOptions(){

        		// Initialize variables.
		$options = array();

        $components = $this->getComponents(false);
        foreach ($components as $component){
            $tmp = JHtml::_('select.option', (string) $component->title, trim((string) $component->alias), 'value', 'text',false);
            $options[] = $tmp;
        }

		foreach ($this->element->children() as $option) {
			// Only add <option /> elements.
			if ($option->getName() != 'option') {
				continue;
			}
			// Create a new option object based on the <option /> element.
			$tmp = JHtml::_('select.option', (string) $option['value'], trim((string) $option), 'value', 'text', ((string) $option['disabled']=='true'));
			// Set some option attributes.
			$tmp->class = (string) $option['class'];
			// Set some JavaScript option attributes.
			$tmp->onclick = (string) $option['onclick'];
			// Add the option object to the result set.
			$options[] = $tmp;
		}
		reset($options);


		return $options;
    }

    /**
	 * Get a list of the authorised, non-special components to display in the components menu.
	 *
	 * @param	boolean	$authCheck	An optional switch to turn off the auth check (to support custom layouts 'grey out' behaviour).
	 *
	 * @return	array	A nest array of component objects and submenus
	 * @since	1.6
	 */
	public function getComponents($authCheck = true)
	{
		// Initialise variables.
		$lang	= JFactory::getLanguage();
		$user	= JFactory::getUser();
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$result	= array();
		$langs	= array();

		// Prepare the query.
		$query->select('m.id, m.title, m.alias, m.link, m.parent_id, m.img, e.element');
		$query->from('#__menu AS m');

		// Filter on the enabled states.
		$query->leftJoin('#__extensions AS e ON m.component_id = e.extension_id');
		$query->where('m.application_id = 1');
		$query->where('e.enabled = 1');
		$query->where('m.id > 1');

		// Order by lft.
		$query->order('m.lft');

		$db->setQuery($query);
		// component list
		$components	= $db->loadObjectList();

		// Parse the list of extensions.
		foreach ($components as &$component) {
			// Trim the menu link.
			$component->link = trim($component->link);

			if ($component->parent_id == 1) {
				// Only add this top level if it is authorised and enabled.
				if ($authCheck == false || ($authCheck && $user->authorise('manage', $component->element))) {
					// Root level.
					$result[$component->id] = $component;
					if (!isset($result[$component->id]->submenu)) {
						$result[$component->id]->submenu = array();
					}

					// If the root menu link is empty, add it in.
					if (empty($component->link)) {
						$component->link = 'index.php?option='.$component->element;
					}

					if (!empty($component->element)) {
						// Load the core file then
						// Load extension-local file.
						$lang->load($component->element.'.sys', JPATH_BASE, null, false, false)
					||	$lang->load($component->element.'.sys', JPATH_ADMINISTRATOR.'/components/'.$component->element, null, false, false)
					||	$lang->load($component->element.'.sys', JPATH_BASE, $lang->getDefault(), false, false)
					||	$lang->load($component->element.'.sys', JPATH_ADMINISTRATOR.'/components/'.$component->element, $lang->getDefault(), false, false);
					}
					$component->text = $lang->hasKey($component->title) ? JText::_($component->title) : $component->alias;
				}
			} else {
				// Sub-menu level.
				if (isset($result[$component->parent_id])) {
					// Add the submenu link if it is defined.
					if (isset($result[$component->parent_id]->submenu) && !empty($component->link)) {
						$component->text = $lang->hasKey($component->title) ? JText::_($component->title) : $component->alias;
						$result[$component->parent_id]->submenu[] = &$component;
					}
				}
			}
		}

		$result = JArrayHelper::sortObjects($result, 'text', 1, true, $lang->getLocale());

		return $result;
	}

}