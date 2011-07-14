<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.html.html');
jimport('joomla.language.helper');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

/**
 * Form Field class for the Joomla Framework.
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @since       11.1
 */
class MolajoFormFieldLanguage extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $type = 'Language';

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 * @since   11.1
	 */
	protected function getOptions()
	{


		// Initialise variables.
		$app = JFactory::getApplication();

		// Detect the native language.
		$native = JLanguageHelper::detectLanguage();
		if (empty($native)) {
			$native = 'en-GB';
		}

		// Get a forced language if it exists.
		$forced = $app->getLocalise();
		if (!empty($forced['language'])) {
			$native = $forced['language'];
		}

		// If a language is already set in the session, use this instead
		$session = JFactory::getSession()->get('setup.options', array());
		if (!empty($session['language'])){
			$native = $session['language'];
		}

		// Get the list of available languages.
		$options = JLanguageHelper::createLanguageList($native);
		if (!$options || JError::isError($options)) {
			$options = array();
		}

		// Set the default value from the native language.
		$this->value = $native;

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
