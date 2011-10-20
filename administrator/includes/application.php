<?php
/**
 * @package     Molajo
 * @subpackage  Application
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Molajo Administrator
 *
 * Interacts with the Application Class for the Site Application
 *
 * @package		Molajo
 * @subpackage	Application
 * @since       1.0
 */
class MolajoAdministrator extends MolajoApplication
{
	/**
     * __construct
     *
	 * Class constructor
	 *
	 * @param	array	An optional associative array of configuration settings.
	 * Recognized key values include 'applicationId' (this list is not meant to be comprehensive).
	 *
	 * @since	1.0
	 */
	public function __construct($config = array())
	{
		$config['applicationId'] = 1;
		parent::__construct($config);

		//Set the root in the URI based on the application name
		JURI::root(null, str_ireplace('/'.$this->getName(), '', JURI::base(true)));
	}

	/**
     * initialise
     *
	 * Initialise the application.
	 *
	 * @param	array	$options	An optional associative array of configuration settings.
	 *
	 * @return	void
	 * @since	1.0
	 */
	function initialise($options = array())
	{
		$config = MolajoFactory::getConfig();

		// if a language was specified it has priority
		// otherwise use user or default language settings
		if (empty($options['language']))
		{
			$user	= MolajoFactory::getUser();
			$lang	= $user->getParam('admin_language');

			// Make sure that the user's language exists
			if ($lang && JLanguage::exists($lang)) {
				$options['language'] = $lang;
			} else {
				$params = MolajoComponentHelper::getParams('com_languages');
				$application = MolajoApplicationHelper::getApplicationInfo($this->getApplicationId());
				$options['language'] = $params->get($application->name, $config->get('language','en-GB'));
			}
		}

		// One last check to make sure we have something
		if (JLanguage::exists($options['language'])) {
        } else {
			$lang = $config->get('language','en-GB');
			if (JLanguage::exists($lang)) {
				$options['language'] = $lang;
			} else {
				$options['language'] = 'en-GB'; // as a last ditch fail to english
			}
		}

		// Execute the parent initialise method.
		parent::initialise($options);

		// Load Library language
		$lang = MolajoFactory::getLanguage();
		$lang->load('lib_joomla', MOLAJO_PATH_ADMINISTRATOR);
	}

	/**
     * route
     * 
	 * Route the application
	 *
	 * @return	void
	 * @since	1.0
	 */
	public function route()
	{
		$uri = JURI::getInstance();

		if ($this->getCfg('force_ssl') >= 1
            && strtolower($uri->getScheme()) != 'https') {
			$uri->setScheme('https');
			$this->redirect((string)$uri);
		}

		// Trigger the onAfterRoute event.
		MolajoPluginHelper::importPlugin('system');
		$this->triggerEvent('onAfterRoute');
	}

	/**
     * getRouter
     *
	 * Return a reference to the MolajoRouter object.
	 *
	 * @return	MolajoRouter
	 * @since	1.0
	 */
	static public function getRouter($name = null, array $options = array())
	{
		$router = parent::getRouter('administrator');
		return $router;
	}

	/**
     * dispatch
     *
	 * Dispatch the application
	 *
	 * @param	string	$component	The component to dispatch.
	 *
	 * @return	void
	 * @since	1.0
	 */
	public function dispatch($component = null)
	{
		try
		{
			if ($component === null) {
				$component = MolajoAdministratorHelper::findOption();
			}

			$request    = parent::getRequest();
			$document	= MolajoFactory::getDocument();
			$user		= MolajoFactory::getUser();

			switch ($document->getType()) {
				case 'html':
					$document->setMetaData('keywords', $this->getCfg('MetaKeys'));
					break;

				default:
					break;
			}
			$document->setTitle($this->getCfg('sitename'). ' - ' .MolajoText::_('JADMINISTRATION'));
			$document->setDescription($this->getCfg('MetaDesc'));

			$contents = MolajoComponentHelper::renderComponent($request);

			$document->setBuffer($contents, 'component');

			// Trigger the onAfterDispatch event.
			MolajoPluginHelper::importPlugin('system');
			$this->triggerEvent('onAfterDispatch');
		}

		// Uncaught exceptions.
		catch (Exception $e)
		{
			$code = $e->getCode();
			JError::raiseError($code ? $code : 500, $e->getMessage());
		}
	}

	/**
     * render
     *
	 * Display the application.
	 *
	 * @return	void
	 * @since	1.0
	 */
	public function render()
	{
        $session = MolajoFactory::getSession();
		$component	= $session->get('page.option');
		$template	= $this->getTemplate(true);

		$file		= JRequest::getCmd('tmpl', 'index');

		$params = array(
			'template'	=> $template->template,
			'file'		=> $file.'.php',
			'directory'	=> MOLAJO_PATH_THEMES,
			'params'	=> $template->params
		);

		$document = MolajoFactory::getDocument();

		$document->parse($params);

		$this->triggerEvent('onBeforeRender');
		$renderedOutput = $document->render(false, $params);

		JResponse::setBody($renderedOutput);
		$this->triggerEvent('onAfterRender');
	}

	/**
	 * getTemplate
     *
     * Get the template
	 *
	 * @return	string	The template name
	 * @since	1.0
	 */
	public function getTemplate($params = false)
	{
		static $template;

		if (isset($template)) {
        } else {
			$admin_style = MolajoFactory::getUser()->getParam('admin_style');

			// Load the template name from the database
			$db = MolajoFactory::getDbo();
			$query = $db->getQuery(true);

			$query->select('a.title as template, b.params as params');
			$query->from('#__templates as a');
			$query->from('#__template_styles as b');
			$query->where('a.application_id = '. (int) MOLAJO_APPLICATION_ID);
			$query->where('a.id = b.template_id');

			if ($admin_style) {
				$query->where('id = '.(int) $admin_style);
			} else{
				$query->where('`default` = 1');
			}

			$db->setQuery($query->__toString());
			$template = $db->loadObject();

			$template->template = JFilterInput::getInstance()->clean($template->template, 'cmd');
			$template->params = new JRegistry($template->params);

			if (file_exists(MOLAJO_PATH_THEMES.DS.$template->template.DS.'index.php')) {
            } else {
				$template->params = new JRegistry();
				$template->template = MOLAJO_APPLICATION_DEFAULT_TEMPLATE;
			}
		}
		if ($params) {
			return $template;
		}

		return $template->template;
	}

	/**
	 * purgeMessages
     *
     * Purge the table of old messages
	 *
	 * @return	void
	 * @since	1.0
	 */
	public static function purgeMessages()
	{
		$db		= MolajoFactory::getDbo();
		$user	= MolajoFactory::getUser();

		$userid = $user->get('id');

		$query = 'SELECT *'
		. ' FROM #__messages_cfg'
		. ' WHERE user_id = ' . (int) $userid
		. ' AND cfg_name = "auto_purge"'
		;
		$db->setQuery($query);
		$config = $db->loadObject();

		// check if auto_purge value set
		if (is_object($config) and $config->cfg_name == 'auto_purge') {
			$purge	= $config->cfg_value;
		} else {
			// if no value set, default is 7 days
			$purge	= 7;
		}
		// calculation of past date

		// if purge value is not 0, then allow purging of old messages
		if ($purge > 0) {
			// purge old messages at day set in message configuration
			$past = MolajoFactory::getDate(time() - $purge * 86400);
			$pastStamp = $past->toMySQL();

			$query = 'DELETE FROM #__messages'
			. ' WHERE date_time < ' . $db->Quote($pastStamp)
			. ' AND user_id_to = ' . (int) $userid
			;
			$db->setQuery($query);
			$db->query();
		}
	}


    /**
     * Deprecated
     */
	public function getClientId()
	{
		return parent::getApplicationId();
	}
}