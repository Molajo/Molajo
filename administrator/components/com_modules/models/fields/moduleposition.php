<?php
/**
 * @version		$Id: moduleposition.php 21097 2011-04-07 15:38:03Z dextercowley $
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

jimport('joomla.form.formfield');
JFormHelper::loadFieldClass('text');

/**
 * Supports a modal article picker.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_modules
 * @since		1.6
 */
class JFormFieldModulePosition extends JFormFieldText
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'ModulePosition';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
	protected function getInput()
	{
		// Get the application id.
		$applicationId = $this->element['application_id'];
		if (!isset($applicationId))
		{
			$applicationName = $this->element['application'];
			if (isset($applicationName))
			{
				$application = JApplicationHelper::getApplicationInfo($applicationName, true);
				$applicationId = $application->id;
			}
		}
		if (!isset($applicationId) && $this->form instanceof JForm) {
			$applicationId = $this->form->getValue('application_id');
		}
		$applicationId = (int) $applicationId;

		// Load the modal behavior script.
		JHtml::_('behavior.modal', 'a.modal');

		// Build the script.
		$script = array();
		$script[] = '	function jSelectPosition_'.$this->id.'(name) {';
		$script[] = '		document.id("'.$this->id.'").value = name;';
		$script[] = '		SqueezeBox.close();';
		$script[] = '	}';

		// Add the script to the document head.
		JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));

		// Setup variables for display.
		$html	= array();
		$link	= 'index.php?option=com_modules&amp;view=positions&amp;layout=modal&amp;tmpl=component&amp;function=jSelectPosition_'.$this->id.'&amp;application_id='.$applicationId;

		// The current user display field.
		$html[] = '<div class="fltlft">';
		$html[] = parent::getInput();
		$html[] = '</div>';

		// The user select button.
		$html[] = '<div class="button2-left">';
		$html[] = '  <div class="blank">';
		$html[] = '	<a class="modal" title="'.JText::_('COM_MODULES_CHANGE_POSITION_TITLE').'"  href="'.$link.'" rel="{handler: \'iframe\', size: {x: 800, y: 450}}">'.JText::_('COM_MODULES_CHANGE_POSITION_BUTTON').'</a>';
		$html[] = '  </div>';
		$html[] = '</div>';

		return implode("\n", $html);
	}
}
