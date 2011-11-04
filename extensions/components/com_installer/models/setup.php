<?php
/**
 * @package     Molajo
 * @subpackage  Installation
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Setup model for the Installer.
 *
 * @package		Molajo
 * @subpackage  Installation
 * @since		1.6
 */
class MolajoInstallationModelSetup extends JModel
{
	/**
     * getOptions
     *
	 * Get the current setup options from the session.
	 *
	 * @return	array
	 * @since	1.6
	 */
	public function getOptions()
	{
		$session = MolajoFactory::getSession();
		$options = $session->get('setup.options', array());

		return $options;
	}

	/**
     * storeOptions
     *
	 * Store the current setup options in the session.
	 *
	 * @param	array	$options
	 *
	 * @return	array
	 * @since	1.6
	 */
	public function storeOptions($options)
	{
		// Get the current setup options from the session.
		$session = MolajoFactory::getSession();
		$old = $session->get('setup.options', array());

		// Merge the new setup options into the current ones and store in the session.
		$options = array_merge($old, (array)$options);
		$session->set('setup.options', $options);

		// If the setup language is set in the options, set it separately in the session and JLanguage.
		if (!empty($options['language'])) {
			$session->set('setup.language', $options['language']);
			MolajoFactory::getLanguage()->setLanguage($options['language']);
		}

		return $options;
	}

	/**
     * getForm
     *
	 * Method to get the link form.
	 *
	 * @return	mixed	JForm object on success, false on failure.
	 * @since	1.6
	 */
	public function getForm($view = null)
	{
		// Initialise variables.
		$false = false;

		if (!$view) {
			$view = JRequest::getWord('view', 'language');
		}

		// Get the form.
		MolajoForm::addFormPath(MOLAJO_PATH_BASE.'/models/forms');
		MolajoForm::addFieldPath(MOLAJO_PATH_BASE.'/models/fields');
		MolajoForm::addRulePath(MOLAJO_PATH_BASE.'/models/rules');

		try
		{
			$form = MolajoForm::getInstance('jform', $view, array('control' => 'jform'));
		}
		catch (Exception $e)
		{
			$this->setError($e->getMessage());
			return false;
		}

		// Check the session for previously entered form data.
		$data = (array) $this->getOptions();

		// Bind the form data if present.
		if (!empty($data)) {
			$form->bind($data);
		}

		return $form;
	}

	/**
     * setDboptions
     *
	 * @return	array
	 * @since	1.6
	 */
	public function getDboptions()
	{
		// Initialise variables.
		$options = array();

		// Create an array of known database connect functions.
		$map = array(
			'MySQL'  => 'mysql_connect',
			'MySQLi' => 'mysqli_connect',
		);

		// Iterate over the options, building an array.
		$found = false;

		foreach ($map as $k => $v)
		{
			// Only list available options.
			if (!function_exists($v)) {
				continue;
			}

			// Create the option object.
			$option = new stdClass;
			$option->text = $k;
			$option->value = strtolower($k);

			// Select the first available.
			if (!$found) {
				$option->selected = ' selected="selected"';
				$found = true;
			}

			$options[] = $option;
		}

		return $options;
	}

	/**
     * getLanguages
     *
	 * Generate a panel of language choices for the user to select their language
	 *
	 * @return	boolean True if successful
	 * @since	1.6
	 */
	public function getLanguages()
	{
		// Initialise variables.
		$app = MolajoFactory::getApplication();

		// Detect the native language.
		$native = MolajoLanguageHelper::detectLanguage();

		if (empty($native)) {
			$native = 'en-GB';
		}

		// Get a forced language if it exists.
		$forced = $app->getLocalise();

		if (!empty($forced['language'])) {
			$native = $forced['language'];
		}

		// Get the list of available languages.
		$list = MolajoLanguageHelper::createLanguageList($native);

		if (!$list || MolajoError::isError($list)) {
			$list = array();
		}

		return $list;
	}

	/**
     * getIniParserAvailability
     *
	 * Checks the availability of the parse_ini_file and parse_ini_string functions.
	 *
	 * @return	boolean
	 * @since	1.6
	 */
	public function getIniParserAvailability()
	{
		$disabled_functions = ini_get('disable_functions');

		if (!empty($disabled_functions)) {
			// Attempt to detect them in the disable_functions black list
			$disabled_functions = explode(',', trim($disabled_functions));
			$number_of_disabled_functions = count($disabled_functions);

			for ($i = 0; $i < $number_of_disabled_functions; $i++)
			{
				$disabled_functions[$i] = trim($disabled_functions[$i]);
			}

			if (phpversion() >= '5.3.0') {
				$result = !in_array('parse_ini_string', $disabled_functions);
			} else {
				$result = !in_array('parse_ini_file', $disabled_functions);
			}
		} else {

			// Attempt to detect their existence; even pure PHP implementation of them will trigger a positive response, though.
			if (phpversion() >= '5.3.0') {
				$result = function_exists('parse_ini_string');
			} else {
				$result = function_exists('parse_ini_file');
			}
		}

		return $result;
	}

	/**
	 * Gets PHP options.
	 *
	 * @return	array
	 * @since	1.6
	 */
	public function getPhpOptions()
	{
		// Initialise variables.
		$options = array();

		// Check the PHP Version.
		$option = new stdClass;
		$option->label  = MolajoText::_('INSTL_PHP_VERSION').' >= 5.2.4';
		$option->state  = version_compare(PHP_VERSION, '5.2.4', '>=');
		$option->notice = null;
		$options[] = $option;

		// Check for zlib support.
		$option = new stdClass;
		$option->label  = MolajoText::_('INSTL_ZLIB_COMPRESSION_SUPPORT');
		$option->state  = extension_loaded('zlib');
		$option->notice = null;
		$options[] = $option;

		// Check for XML support.
		$option = new stdClass;
		$option->label  = MolajoText::_('INSTL_XML_SUPPORT');
		$option->state  = extension_loaded('xml');
		$option->notice = null;
		$options[] = $option;

		// Check for MySQL support.
		$option = new stdClass;
		$option->label  = MolajoText::_('INSTL_MYSQL_SUPPORT');
		$option->state  = (function_exists('mysql_connect') || function_exists('mysqli_connect'));
		$option->notice = null;
		$options[] = $option;

		// Check for mbstring options.
		if (extension_loaded('mbstring')) {
			// Check for default MB language.
			$option = new stdClass;
			$option->label  = MolajoText::_('INSTL_MB_LANGUAGE_IS_DEFAULT');
			$option->state  = (strtolower(ini_get('mbstring.language')) == 'neutral');
			$option->notice = ($option->state) ? null : MolajoText::_('INSTL_NOTICEMBLANGNOTDEFAULT');
			$options[] = $option;

			// Check for MB function overload.
			$option = new stdClass;
			$option->label  = MolajoText::_('INSTL_MB_STRING_OVERLOAD_OFF');
			$option->state  = (ini_get('mbstring.func_overload') == 0);
			$option->notice = ($option->state) ? null : MolajoText::_('INSTL_NOTICEMBSTRINGOVERLOAD');
			$options[] = $option;
		}

		// Check for a missing native parse_ini_file implementation
		$option = new stdClass;
		$option->label = MolajoText::_('INSTL_PARSE_INI_FILE_AVAILABLE');
		$option->state = $this->getIniParserAvailability();
		$option->notice = null;
		$options[] = $option;

		// Check for missing native json_encode / json_decode support
		$option = new stdClass;
		$option->label = MolajoText::_('INSTL_JSON_SUPPORT_AVAILABLE');
		$option->state = function_exists('json_encode') && function_exists('json_decode');
		$option->notice = null;
		$options[] = $option;

		// Check for configuration file writeable.
		$option = new stdClass;
		$option->label  = 'configuration.php '.MolajoText::_('INSTL_WRITABLE');
		$option->state  = (is_writable('../configuration.php') || (!file_exists('../configuration.php') && is_writable('../')));
		$option->notice = ($option->state) ? null : MolajoText::_('INSTL_NOTICEYOUCANSTILLINSTALL');
		$options[] = $option;

		return $options;
	}

	/**
     * getPhpOptionsSufficient
     *
	 * Checks if all of the mandatory PHP options are met
	 *
	 * @return	boolean
	 * @since	1.6
	 */
	public function getPhpOptionsSufficient()
	{
		$result = true;
		$options = $this->getPhpOptions();

		foreach ($options as $option)
		{
			if (is_null($option->notice)) {
				$result = ($result && $option->state);
			}
		}

		return $result;
	}

	/**
     * getPhpSettings
     *
	 * Gets PHP Settings.
	 *
	 * @return	array
	 * @since	1.6
	 */
	public function getPhpSettings()
	{
		// Initialise variables.
		$settings = array();

		// Check for safe mode.
		$setting = new stdClass;
		$setting->label = MolajoText::_('INSTL_SAFE_MODE');
		$setting->state = (bool) ini_get('safe_mode');
		$setting->recommended = false;
		$settings[] = $setting;

		// Check for display errors.
		$setting = new stdClass;
		$setting->label = MolajoText::_('INSTL_DISPLAY_ERRORS');
		$setting->state = (bool) ini_get('display_errors');
		$setting->recommended = false;
		$settings[] = $setting;

		// Check for file uploads.
		$setting = new stdClass;
		$setting->label = MolajoText::_('INSTL_FILE_UPLOADS');
		$setting->state = (bool) ini_get('file_uploads');
		$setting->recommended = true;
		$settings[] = $setting;

		// Check for magic quotes.
		$setting = new stdClass;
		$setting->label = MolajoText::_('INSTL_MAGIC_QUOTES_RUNTIME');
		$setting->state = (bool) ini_get('magic_quotes_runtime');
		$setting->recommended = false;
		$settings[] = $setting;

		// Check for register globals.
		$setting = new stdClass;
		$setting->label = MolajoText::_('INSTL_REGISTER_GLOBALS');
		$setting->state = (bool) ini_get('register_globals');
		$setting->recommended = false;
		$settings[] = $setting;

		// Check for output buffering.
		$setting = new stdClass;
		$setting->label = MolajoText::_('INSTL_OUTPUT_BUFFERING');
		$setting->state = (bool) ini_get('output_buffering');
		$setting->recommended = false;
		$settings[] = $setting;

		// Check for session auto-start.
		$setting = new stdClass;
		$setting->label = MolajoText::_('INSTL_SESSION_AUTO_START');
		$setting->state = (bool) ini_get('session.auto_start');
		$setting->recommended = false;
		$settings[] = $setting;

		return $settings;
	}

	/**
     * validate
     *
	 * Method to validate the form data.
	 *
	 * @param	array	$data	The form data.
	 * @param	string	$view	The view.
	 *
	 * @return	mixed	Array of filtered data if valid, false otherwise.
	 * @since	1.6
	 */
	public function validate($data, $view = null)
	{
		// Get the form.
		$form = $this->getForm($view);

		// Check for an error.
		if ($form === false) {
			return false;
		}

		// Filter and validate the form data.
		$data = $form->filter($data);
		$return	= $form->validate($data);

		// Check for an error.
		if (MolajoError::isError($return)) {
			$this->setError($return->getMessage());
			return false;
		}

		// Check the validation results.
		if ($return === false) {
			// Get the validation messages from the form.
			foreach ($form->getErrors() as $message)
			{
				$this->setError($message);
			}

			return false;
		}

		return $data;
	}
}
