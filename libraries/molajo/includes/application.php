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
 * Base class for an application.
 *
 * Acts as a Factory class for application specific objects and provides many
 * supporting API functions. Derived clases should supply the route(), dispatch()
 * and render() functions.
 */
class MolajoApplication extends JObject
{
	/**
	 * The application identifier.
	 *
	 * @var    integer
	 * @since  1.0
	 */
	protected $_applicationId = null;

	/**
	 * The application message queue.
	 *
	 * @var    array
	 * @since  1.0
	 */
	protected $_messageQueue = array();

	/**
	 * The name of the application.
	 *
	 * @var    array
	 * @since  1.0
	 */
	protected $_name = null;

	/**
	 * The scope of the application.
	 *
	 * @var    string
	 * @since  1.0
	 */
	public $scope = null;

	/**
	 * The time the request was made.
	 *
	 * @var    date
	 * @since  1.0
	 */
	public $requestTime = null;

	/**
	 * The time the request was made as Unix timestamp.
	 *
	 * @var    integer
	 * @since  1.0
	 */
	public $startTime = null;

	/**
     * __construct
     * 
	 * Class constructor.
	 *
	 * @param   array  $config  A configuration array including optional elements such as session
	 *                   session_name, applicationId and others. This is not exhaustive.
	 *
	 * @since  1.0
	 */
	public function __construct($config = array())
	{
		$this->_name		    = $this->getName();
		$this->_applicationId	= $config['applicationId'];

		// Enable sessions by default.
		if (!isset($config['session'])) {
			$config['session'] = true;
		}

		// Set the session default name.
		if (!isset($config['session_name'])) {
			$config['session_name'] = $this->_name;
		}

		// Set the default configuration file.
		if (!isset($config['config_file'])) {
			$config['config_file'] = 'configuration.php';
		}

		// Create the configuration object.
        if ($this->_name == 'installation') {
        } else {
		    $this->_createConfiguration(MOLAJO_PATH_CONFIGURATION.'/'.$config['config_file']);
        }
        
		// Create the session if a session name is passed.
		if ($config['session'] !== false) {
			$this->_createSession(JUtility::getHash($config['session_name']));
		}

		$this->set('requestTime', gmdate('Y-m-d H:i'));

		// Used by task system to ensure that the system doesn't go over time.
		$this->set('startTime', JProfiler::getmicrotime());
	}

	/**
     * getInstance
     *
	 * Returns the global MolajoApplication object, only creating it if it
	 * doesn't already exist.
	 *
	 * @param   mixed   $application  A application identifier or name.
	 * @param   array   $config  An optional associative array of configuration settings.
	 * @param   strong  $prefx   A prefix for class names
	 *
	 * @return  MolajoApplication A MolajoApplication object.
	 * @since  1.0
	 */
	public static function getInstance($application, $config = array(), $prefix = 'Molajo')
	{
		static $instances;

		if (!isset($instances)) {
			$instances = array();
		}

		if (empty($instances[$application])) {

			$info = MolajoApplicationHelper::getApplicationInfo($application, true);
			$path = $info->path.'/includes/application.php';

			if (file_exists($path)) {
				require_once $path;
				$classname = $prefix.ucfirst($application);
				$instance = new $classname($config);

			} else {
				$error = JError::raiseError(500, JText::sprintf('JLIB_APPLICATION_ERROR_APPLICATION_LOAD', $application));
				return $error;
			}

			$instances[$application] = &$instance;
		}

		return $instances[$application];
	}

	/**
     * initialise
     *
	 * Initialise the application.
	 *
	 * @param   array  $options  An optional associative array of configuration settings.
	 *
	 * @since  1.0
	 */
	public function initialise($options = array())
	{
		// Set the language in the class.
		$config = MolajoFactory::getConfig();

		// Check that we were given a language in the array (since by default may be blank).
		if (isset($options['language'])) {
			$config->set('language', $options['language']);
		}

		// Set user specific editor.
		$user	= MolajoFactory::getUser();
		$editor	= $user->getParam('editor', $this->getCfg('editor'));
		if (!MolajoPluginHelper::isEnabled('editors', $editor)) {
			$editor	= $this->getCfg('editor');
			if (!MolajoPluginHelper::isEnabled('editors', $editor)) {
				$editor	= 'none';
			}
		}

		$config->set('editor', $editor);

		// Trigger the onAfterInitialise event.
		MolajoPluginHelper::importPlugin('system');
		$this->triggerEvent('onAfterInitialise');
	}

	/**
     * route
     *
	 * Route the application.
	 *
	 * Routing is the process of examining the request environment to determine which
	 * component should receive the request. The component optional parameters
	 * are then set in the request object to be processed when the application is being
	 * dispatched.
	 *
	 * @return  void;
	 * @since  1.0
	 */
	public function route()
	{
		$uri	= clone JURI::getInstance();

		$router = $this->getRouter();
		$result = $router->parse($uri);

		JRequest::set($result, 'get', false);

		MolajoPluginHelper::importPlugin('system');
		$this->triggerEvent('onAfterRoute');
	}

	/**
	 * Dispatch the applicaiton.
	 *
	 * Dispatching is the process of pulling the option from the request object and
	 * mapping them to a component. If the component does not exist, it handles
	 * determining a default component to dispatch.
	 *
	 * @param   string  $component	The component to dispatch.
	 *
	 * @return  void
	 * @since  1.0
	 */
	public function dispatch($component = null)
	{
		$document = MolajoFactory::getDocument();

		$document->setTitle($this->getCfg('sitename'). ' - ' .JText::_('JADMINISTRATION'));
		$document->setDescription($this->getCfg('MetaDesc'));

		$contents = MolajoComponentHelper::renderComponent($component);
		$document->setBuffer($contents, 'component');

		// Trigger the onAfterDispatch event.
		MolajoPluginHelper::importPlugin('system');
		$this->triggerEvent('onAfterDispatch');
	}

    /**
     * getRequest
     *
     * Gets the Request Object and populates Page Session Variables for Component
     *
     * @return bool
     */
    protected function getRequest ()
    {
        /** initialization */
        $option = '';
        $task = '';
        $view = '';
        $layout = '';
        $format = '';
        $component_table = '';

        /** 1. Option */
        $option = JRequest::getCmd('option', null);

        $molajoConfig = new MolajoModelConfiguration ($option);
        if ($option == null) {
            $option = $molajoConfig->getOptionValue (MOLAJO_CONFIG_OPTION_ID_DEFAULT_OPTION + (int) MOLAJO_APPLICATION_ID);
            if ($option === false) {
                $this->enqueueMessage(JText::_('MOLAJO_NO_DEFAULT_OPTION_DEFINED'), 'error');
                return false;
            }
        }

        /** 2. Paths */
        if (defined('MOLAJO_PATH_COMPONENT')) { } else { define('MOLAJO_PATH_COMPONENT', strtolower(MOLAJO_PATH_BASE.'/components/'.$option)); }
        if (defined('MOLAJO_PATH_COMPONENT_ADMINISTRATOR')) { } else { define('MOLAJO_PATH_COMPONENT_ADMINISTRATOR', strtolower(MOLAJO_PATH_ADMINISTRATOR.'/components/'.$option)); }
        if (defined('JPATH_COMPONENT')) { } else { define('JPATH_COMPONENT', MOLAJO_PATH_COMPONENT); }
        if (defined('JPATH_COMPONENT_ADMINISTRATOR')) { } else { define('JPATH_COMPONENT_ADMINISTRATOR', MOLAJO_PATH_COMPONENT_ADMINISTRATOR); }

        /** 3. Task */
        $task = JRequest::getCmd('task', 'display');
        if (strpos($task,'.')) {
            $task = substr($task, (strpos($task,'.')+1), 99);
        }

        /** 4. Controller */
        $controller = $molajoConfig->getOptionLiteralValue (MOLAJO_CONFIG_OPTION_ID_TASK_TO_CONTROLLER, $task);
        if ($controller === false) {
            JError::raiseError(500, JText::_('MOLAJO_INVALID_TASK_DISPLAY_CONTROLLER').' '.$task);
            return false;
        }

        if ($task == 'display') {

            /** 5. View **/
            $view = JRequest::getCmd('view', null);
            if ($view == null) {
                $results = false;
            } else {
                $results = $molajoConfig->getOptionLiteralValue (MOLAJO_CONFIG_OPTION_ID_VIEWS + (int) MOLAJO_APPLICATION_ID, $view);
            }

            if ($results === false) {
                $view = $molajoConfig->getOptionValue (MOLAJO_CONFIG_OPTION_ID_DEFAULT_VIEW);
                if ($view === false) {
                    $this->enqueueMessage(JText::_('MOLAJO_NO_DEFAULT_VIEW_DEFINED'), 'error');
                    return false;
                }
            }

            /** 6. Layout **/
            $layout = JRequest::getCmd('layout', null);
            if ($layout == null) {
                $results = false;
            } else {
                if ($view == 'edit') {
                    $results = $molajoConfig->getOptionLiteralValue (MOLAJO_CONFIG_OPTION_ID_EDIT_VIEW_LAYOUTS + (int) MOLAJO_APPLICATION_ID, $layout);
                } else {
                    $results = $molajoConfig->getOptionLiteralValue (MOLAJO_CONFIG_OPTION_ID_DISPLAY_VIEW_LAYOUTS + (int) MOLAJO_APPLICATION_ID, $layout);
                }
            }

            if ($results === false) {
                if ($view == 'edit') {
                    $layout = $molajoConfig->getOptionValue (MOLAJO_CONFIG_OPTION_ID_DEFAULT_EDIT_VIEW_LAYOUTS + (int) MOLAJO_APPLICATION_ID);
                } else {
                    $layout = $molajoConfig->getOptionValue (MOLAJO_CONFIG_OPTION_ID_DEFAULT_DISPLAY_VIEW_LAYOUTS + (int) MOLAJO_APPLICATION_ID);
                }
                if ($layout === false) {
                    $this->enqueueMessage(JText::_('MOLAJO_NO_DEFAULT_LAYOUT_FOR_VIEW_DEFINED'), 'error');
                    return false;
                }
            }

            /** 7. Format */
            $format = JRequest::getCmd('format', null);
            if ($format == null) {
                $results = false;
            } else {
                if ($view == 'edit') {
                    $results = $molajoConfig->getOptionLiteralValue (MOLAJO_CONFIG_OPTION_ID_EDIT_VIEW_FORMATS + (int) MOLAJO_APPLICATION_ID, $format);
                } else {
                    $results = $molajoConfig->getOptionLiteralValue (MOLAJO_CONFIG_OPTION_ID_DISPLAY_VIEW_FORMATS + (int) MOLAJO_APPLICATION_ID, $format);
                }
            }

            if ($results === false) {
                if ($view == 'edit') {
                    $format = $molajoConfig->getOptionValue (MOLAJO_CONFIG_OPTION_ID_DEFAULT_EDIT_VIEW_FORMATS + (int) MOLAJO_APPLICATION_ID);
                } else {
                    $format = $molajoConfig->getOptionValue (MOLAJO_CONFIG_OPTION_ID_DEFAULT_DISPLAY_VIEW_FORMATS + (int) MOLAJO_APPLICATION_ID);
                }
                if ($format === false) {
                    $this->enqueueMessage(JText::_('MOLAJO_NO_DEFAULT_LAYOUT_FOR_VIEW_DEFINED'), 'error');
                    return false;
                }
                echo $format;
            }
        } else {
            /** amy: come back and get redirect stuff later */
            $view = '';
            $layout = '';
            $format = '';
        }

        /** 8. id, cid and catid */
        $id = JRequest::getInt('id');
        $cids = JRequest::getVar('cid', array(), '', 'array');
        JArrayHelper::toInteger($cids);

        if ($task == 'add') {
            $id = 0;
            $cids = array();

        } else if ($task == 'edit' || $task == 'restore') {

            if ($id > 0 && count($cids) == 0) {
            } else if ($id == 0 && count($cids) == 1) {
                $id = $cids[0];
                $cids = array();
            } else if ($id == 0 && count($cids) == 0) {
                JError::raiseError(500, JText::_('MOLAJO_ERROR_TASK_MUST_HAVE_REQUEST_ID_TO_EDIT'));
                return false;
            } else if (count($cids) > 1) {
                JError::raiseError(500, JText::_('MOLAJO_ERROR_TASK_MAY_NOT_HAVE_MULTIPLE_REQUEST_IDS'));
                return false;
            }
        }
        $catid = JRequest::getInt('catid');

        /** 9. acl implementation */
        $acl_implementation = $molajoConfig->getOptionValue (MOLAJO_CONFIG_OPTION_ID_ACL_IMPLEMENTATION);
        if ($acl_implementation == false) {
            $acl_implementation = 'core';
        }

        /** 10. component table */
        $component_table = $molajoConfig->getOptionValue (MOLAJO_CONFIG_OPTION_ID_TABLE);
        if ($component_table == false) {
            $component_table = '_common';
        }

        /** Request Object */
        JRequest::setVar('option', $option);
        JRequest::setVar('view', $view);
        JRequest::setVar('layout', $layout);
        JRequest::setVar('task', $task);
        JRequest::setVar('format', $format);

        JRequest::setVar('id', (int) $id);
        JRequest::setVar('cid', (array) $cids);

        /** Page Session Variables */
        $session = JFactory::getSession();

        $session->set('page.application_id', MOLAJO_APPLICATION_ID);
        $session->set('page.current_url', MOLAJO_CURRENT_URL);
        $session->set('page.base_url', JURI::base());
        $session->set('page.item_id', JRequest::getInt('Itemid', 0));

        $session->set('page.controller', $controller);
        $session->set('page.option', $option);
        $session->set('page.view', $view);
        $session->set('page.layout', $layout);
        $session->set('page.task', $task);
        $session->set('page.format', $format);

        $session->set('page.id', (int) $id);
        $session->set('page.cid', (array) $cids);
        $session->set('page.catid', (int) $catid);

        $session->set('page.acl_implementation', $acl_implementation);
        $session->set('page.component_table', $component_table);
        $session->set('page.filter_fieldname', 'config_manager_list_filters');
        $session->set('page.select_fieldname', 'config_manager_grid_column');

        return true;
    }

	/**
	 * Render the application.
	 *
	 * Rendering is the process of pushing the document buffers into the template
	 * placeholders, retrieving data from the document and pushing it into
	 * the JResponse buffer.
	 *
	 * @return  void;
	 * @since  1.0
	 */
	public function render()
	{
		$params = array(
			'template'	=> $this->getTemplate(),
			'file'		=> 'index.php',
			'directory'	=> MOLAJO_PATH_THEMES,
			'params'	=> '$template->params'
		);

		// Parse the document.
		$document = MolajoFactory::getDocument();
		$document->parse($params);

		// Trigger the onBeforeRender event.
		MolajoPluginHelper::importPlugin('system');
		$this->triggerEvent('onBeforeRender');

		// Render the document.
		$caching = ($this->getCfg('caching') >= 2) ? true : false;
		JResponse::setBody($document->render($caching, $params));

		// Trigger the onAfterRender event.
		$this->triggerEvent('onAfterRender');
	}

	/**
	 * Exit the application.
	 *
	 * @param    integer  $code  Exit code
	 *
	 * @return   void  Exits the application.
	 * @since    11.1
	 */
	public function close($code = 0)
	{
        $session = JFactory::getSession();

        $session->clear('page.application_id');
        $session->clear('page.current_url');
        $session->clear('page.base_url');

        $session->clear('page.extension_id');
        $session->clear('page.extension_access');
        $session->clear('page.extension_asset_id');
        $session->clear('page.extension_enabled');
        $session->clear('page.extension_params');

        $session->clear('page.item_id');

        $session->clear('page.controller');
        $session->clear('page.option');
        $session->clear('page.view');
        $session->clear('page.model');
        $session->clear('page.layout');
        $session->clear('page.task');
        $session->clear('page.format');

        $session->clear('page.id');
        $session->clear('page.cid');
        $session->clear('page.catid');

        $session->clear('page.acl_implementation');
        $session->clear('page.component_table');
        $session->clear('page.filter_fieldname');
        $session->clear('page.select_fieldname');

		exit($code);
	}

	/**
	 * Redirect to another URL.
	 *
	 * Optionally enqueues a message in the system message queue (which will be displayed
	 * the next time a page is loaded) using the enqueueMessage method. If the headers have
	 * not been sent the redirect will be accomplished using a "301 Moved Permanently"
	 * code in the header pointing to the new location. If the headers have already been
	 * sent this will be accomplished using a JavaScript statement.
	 *
	 * @param   string   $url      The URL to redirect to. Can only be http/https URL
	 * @param   string   $msg      An optional message to display on redirect.
	 * @param   string   $msgType  An optional message type. Defaults to message.
	 * @param   boolean  $moved    True if the page is 301 Permanently Moved, otherwise 303 See Other is assumed.
	 *
	 * @return  void  Calls exit().
	 *
	 * @see     MolajoApplication::enqueueMessage()
	 * @since  1.0
	 */
	public function redirect($url, $msg='', $msgType='message', $moved = false)
	{
		// Check for relative internal links.
		if (preg_match('#^index2?\.php#', $url)) {
			$url = JURI::base() . $url;
		}

		// Strip out any line breaks.
		$url = preg_split("/[\r\n]/", $url);
		$url = $url[0];

		// If we don't start with a http we need to fix this before we proceed.
		// We could validly start with something else (e.g. ftp), though this would
		// be unlikely and isn't supported by this API.
		if (!preg_match('#^http#i', $url)) {
			$uri = JURI::getInstance();
			$prefix = $uri->toString(Array('scheme', 'user', 'pass', 'host', 'port'));

			if ($url[0] == '/') {
				// We just need the prefix since we have a path relative to the root.
				$url = $prefix . $url;
			}
			else {
				// It's relative to where we are now, so lets add that.
				$parts = explode('/', $uri->toString(Array('path')));
				array_pop($parts);
				$path = implode('/',$parts).'/';
				$url = $prefix . $path . $url;
			}
		}


		// If the message exists, enqueue it.
		if (trim($msg)) {
			$this->enqueueMessage($msg, $msgType);
		}

		// Persist messages if they exist.
		if (count($this->_messageQueue)) {
			$session = MolajoFactory::getSession();
			$session->set('application.queue', $this->_messageQueue);
		}

		// If the headers have been sent, then we cannot send an additional location header
		// so we will output a javascript redirect statement.
		if (headers_sent()) {
			echo "<script>document.location.href='$url';</script>\n";
		}
		else {
			$document = MolajoFactory::getDocument();
			$navigator = JBrowser::getInstance();
			if ($navigator->isBrowser('msie')) {
				// MSIE type browser and/or server cause issues when url contains utf8 character,so use a javascript redirect method
 				echo '<html><head><meta http-equiv="content-type" content="text/html; charset='.$document->getCharset().'" /><script>document.location.href=\''.$url.'\';</script></head><body></body></html>';
			}
			elseif (!$moved and $navigator->isBrowser('konqueror')) {
				// WebKit browser (identified as konqueror by Joomla!) - Do not use 303, as it causes subresources reload (https://bugs.webkit.org/show_bug.cgi?id=38690)
				echo '<html><head><meta http-equiv="refresh" content="0; url='. $url .'" /><meta http-equiv="content-type" content="text/html; charset='.$document->getCharset().'" /></head><body></body></html>';
			}
			else {
				// All other browsers, use the more efficient HTTP header method
				header($moved ? 'HTTP/1.1 301 Moved Permanently' : 'HTTP/1.1 303 See other');
				header('Location: '.$url);
				header('Content-Type: text/html; charset='.$document->getCharset());
			}
		}
		$this->close();
	}

	/**
	 * Enqueue a system message.
	 *
	 * @param   string   $msg   The message to enqueue.
	 * @param   string   $type  The message type. Default is message.
	 *
	 * @return  void
	 *
	 * @since  1.0
	 */
	public function enqueueMessage($msg, $type = 'message')
	{
		// For empty queue, if messages exists in the session, enqueue them first.
		if (!count($this->_messageQueue)) {
			$session = MolajoFactory::getSession();
			$sessionQueue = $session->get('application.queue');

			if (count($sessionQueue)) {
				$this->_messageQueue = $sessionQueue;
				$session->set('application.queue', null);
			}
		}

		// Enqueue the message.
		$this->_messageQueue[] = array('message' => $msg, 'type' => strtolower($type));
	}

	/**
	 * Get the system message queue.
	 *
	 * @return  array  The system message queue.
	 *
	 * @since  1.0
	 */
	public function getMessageQueue()
	{
		// For empty queue, if messages exists in the session, enqueue them.
		if (!count($this->_messageQueue)) {
			$session = MolajoFactory::getSession();
			$sessionQueue = $session->get('application.queue');

			if (count($sessionQueue)) {
				$this->_messageQueue = $sessionQueue;
				$session->set('application.queue', null);
			}
		}

		return $this->_messageQueue;
	}

	/**
	 * Gets a configuration value.
	 *
	 * An example is in application/japplication-getcfg.php Getting a configuration
	 *
	 * @param   string   The name of the value to get.
	 * @param   string   Default value to return
	 *
	 * @return  mixed    The user state.
	 *
	 * @since  1.0
	 */
	public function getCfg($varname, $default=null)
	{
		$config = MolajoFactory::getConfig();
		return $config->get('' . $varname, $default);
	}

	/**
	 * Method to get the application name.
	 *
	 * The dispatcher name is by default parsed using the classname, or it can be set
	 * by passing a $config['name'] in the class constructor.
	 *
	 * @return  string  The name of the dispatcher.
	 *
	 * @since  1.0
	 */
	public function getName()
	{
		$name = $this->_name;

		if (empty($name)) {
			$r = null;
			if (!preg_match('/Molajo(.*)/i', get_class($this), $r)) {
				JError::raiseError(500, JText::_('JLIB_APPLICATION_ERROR_APPLICATION_GET_NAME'));
			}
			$name = strtolower($r[1]);
		}

		return $name;
	}

	/**
	 * Gets a user state.
	 *
	 * @param   string  The path of the state.
	 * @param   mixed   Optional default value, returned if the internal value is null.
	 *
	 * @return  mixed  The user state or null.
	 *
	 * @since  1.0
	 */
	public function getUserState($key, $default = null)
	{
		$session	= MolajoFactory::getSession();
		$registry	= $session->get('registry');

		if (!is_null($registry)) {
			return $registry->get($key, $default);
		}

		return $default;
	}

	/**
	 * Sets the value of a user state variable.
	 *
	 * @param   string  The path of the state.
	 * @param   string  The value of the variable.
	 *
	 * @return  mixed   The previous state, if one existed.
	 *
	 * @since  1.0
	 */
	public function setUserState($key, $value)
	{
		$session	= MolajoFactory::getSession();
		$registry	= $session->get('registry');

		if (!is_null($registry)) {
			return $registry->set($key, $value);
		}

		return null;
	}

	/**
	 * Gets the value of a user state variable.
	 *
	 * @param   string   $key      The key of the user state variable.
	 * @param   string   $request  The name of the variable passed in a request.
	 * @param   string   $default  The default value for the variable if not found. Optional.
	 * @param   string   $type     Filter for the variable, for valid values see {@link JFilterInput::clean()}. Optional.
	 *
	 * @return  The request user state.
	 *
	 * @since  1.0
	 */
	public function getUserStateFromRequest($key, $request, $default = null, $type = 'none')
	{
		$cur_state = $this->getUserState($key, $default);
		$new_state = JRequest::getVar($request, null, 'default', $type);

		// Save the new value only if it was set in this request.
		if ($new_state !== null) {
			$this->setUserState($key, $new_state);
		}
		else {
			$new_state = $cur_state;
		}

		return $new_state;
	}

	/**
	 * Registers a handler to a particular event group.
	 *
	 * @param   string  $event    The event name.
	 * @param   mixed   $handler  The handler, a function or an instance of a event object.
	 *
	 * @return  void
	 *
	 * @since  1.0
	 */
	public static function registerEvent($event, $handler)
	{
		$dispatcher = JDispatcher::getInstance();
		$dispatcher->register($event, $handler);
	}

	/**
	 * Calls all handlers associated with an event group.
	 *
	 * @param   string  $event  The event name.
	 * @param   array   $args   An array of arguments.
	 *
	 * @return  array  An array of results from each function call.
	 *
	 * @since  1.0
	 */
	function triggerEvent($event, $args=null)
	{
		$dispatcher = JDispatcher::getInstance();

		return $dispatcher->trigger($event, $args);
	}

	/**
	 * Login authentication function.
	 *
	 * Username and encoded password are passed the the onUserLogin event which
	 * is responsible for the user validation. A successful validation updates
	 * the current session record with the user's details.
	 *
	 * Username and encoded password are sent as credentials (along with other
	 * possibilities) to each observer (authentication plugin) for user
	 * validation.  Successful validation will update the current session with
	 * the user details.
	 *
	 * @param   array  $credentials  Array('username' => string, 'password' => string)
	 * @param   array  $options      Array('remember' => boolean)
	 *
	 * @return  boolean  True on success.
	 *
	 * @since  1.0
	 */
	public function login($credentials, $options = array())
	{
		$authenticate = JAuthentication::getInstance();
		$response	= $authenticate->authenticate($credentials, $options);

		if ($response->status === JAUTHENTICATE_STATUS_SUCCESS) {
			// Import the user plugin group.
			MolajoPluginHelper::importPlugin('user');

			// OK, the credentials are authenticated.  Lets fire the onLogin event.
			$results = $this->triggerEvent('onUserLogin', array((array)$response, $options));

			/*
			 * If any of the user plugins did not successfully complete the login routine
			 * then the whole method fails.
			 *
			 * Any errors raised should be done in the plugin as this provides the ability
			 * to provide much more information about why the routine may have failed.
			 */

			if (!in_array(false, $results, true)) {
				// Set the remember me cookie if enabled.
				if (isset($options['remember']) && $options['remember']) {

					// Create the encryption key, apply extra hardening using the user agent string.
					$key = JUtility::getHash(@$_SERVER['HTTP_USER_AGENT']);

					$crypt = new JSimpleCrypt($key);
					$rcookie = $crypt->encrypt(serialize($credentials));
					$lifetime = time() + 365*24*60*60;

					// Use domain and path set in config for cookie if it exists.
					$cookie_domain = $this->getCfg('cookie_domain', '');
					$cookie_path = $this->getCfg('cookie_path', '/');
					setcookie( JUtility::getHash('JLOGIN_REMEMBER'), $rcookie, $lifetime, $cookie_path, $cookie_domain );
				}

				return true;
			}
		}

		// Trigger onUserLoginFailure Event.
		$this->triggerEvent('onUserLoginFailure', array((array)$response));

		// If silent is set, just return false.
		if (isset($options['silent']) && $options['silent']) {
			return false;
		}
	
		// If status is success, any error will ahve been raised by the user plugin
		if ($response->status !== JAUTHENTICATE_STATUS_SUCCESS) {
			JError::raiseWarning('SOME_ERROR_CODE', JText::_('JLIB_LOGIN_AUTHENTICATE'));
		}
		
		return false;
	}

	/**
	 * Logout authentication function.
	 *
	 * Passed the current user information to the onUserLogout event and reverts the current
	 * session record back to 'anonymous' parameters.
	 * If any of the authentication plugins did not successfully complete
	 * the logout routine then the whole method fails.  Any errors raised
	 * should be done in the plugin as this provides the ability to give
	 * much more information about why the routine may have failed.
	 *
	 * @param   integer  $userid   The user to load - Can be an integer or string - If string, it is converted to ID automatically
	 * @param   array    $options  Array('applicationid' => array of application id's)
	 *
	 * @return  boolean  True on success
	 *
	 * @since  1.0
	 */
	public function logout($userid = null, $options = array())
	{
		// Initialise variables.
		$retval = false;

		// Get a user object from the MolajoApplication.
		$user = MolajoFactory::getUser($userid);

		// Build the credentials array.
		$parameters['username']	= $user->get('username');
		$parameters['id']		= $user->get('id');

		// Set applicationid in the options array if it hasn't been set already.
		if (!isset($options['applicationid'])) {
			$options['applicationid']= $this->getApplicationId();
		}

		// Import the user plugin group.
		MolajoPluginHelper::importPlugin('user');

		// OK, the credentials are built. Lets fire the onLogout event.
		$results = $this->triggerEvent('onUserLogout', array($parameters, $options));

		// Check if any of the plugins failed. If none did, success.

		if (!in_array(false, $results, true)) {
			// Use domain and path set in config for cookie if it exists.
			$cookie_domain = $this->getCfg('cookie_domain', '');
			$cookie_path = $this->getCfg('cookie_path', '/');
			setcookie(JUtility::getHash('JLOGIN_REMEMBER'), false, time() - 86400, $cookie_path, $cookie_domain);

			return true;
		}

		// Trigger onUserLoginFailure Event.
		$this->triggerEvent('onUserLogoutFailure', array($parameters));

		return false;
	}

	/**
	 * Gets the name of the current template.
	 *
	 * @param   array    $params  An optional associative array of configuration settings
	 *
	 * @return  string   System is the fallback.
	 *
	 * @since  1.0
	 */
	public function getTemplate($params = false)
	{
		return 'system';
	}

	/**
	 * Returns the application JRouter object.
	 *
	 * @param   string  $name     The name of the application.
	 * @param   array   $options  An optional associative array of configuration settings.
	 *
	 * @return  JRouter  A JRouter object
	 *
	 * @since  1.0
	 */
	static public function getRouter($name = null, array $options = array())
	{
		if (!isset($name)) {
			$app = MolajoFactory::getApplication();
			$name = $app->getName();
		}

		$router = JRouter::getInstance($name, $options);

		if (JError::isError($router)) {
			return null;
		}

		return $router;
	}

	/**
	 * This method transliterates a string into an URL
	 * safe string or returns a URL safe UTF-8 string
	 * based on the global configuration
	 *
	 * @param   string  $string  String to process
	 *
	 * @return  string  Processed string
	 *
	 * @since  1.0
	 */
	static public function stringURLSafe($string)
	{
		$app = MolajoFactory::getApplication();

		if (self::getCfg('unicodeslugs') == 1) {
			$output = JFilterOutput::stringURLUnicodeSlug($string);
		}
		else {
			$output = JFilterOutput::stringURLSafe($string);
		}

		return $output;
	}

	/**
	 * Returns the application JPathway object.
	 *
	 * @param   string  $name     The name of the application.
	 * @param   array   $options  An optional associative array of configuration settings.
	 *
	 * @return  JPathway  A JPathway object
	 *
	 * @since  1.0
	 */
	public function getPathway($name = null, $options = array())
	{
		if (!isset($name)) {
			$name = $this->_name;
		}

		$pathway = JPathway::getInstance($name, $options);

		if (JError::isError($pathway)) {
			return null;
		}

		return $pathway;
	}

	/**
	 * Returns the Menu object.
	 *
	 * @param   string  $name     The name of the application/application.
	 * @param   array   $options  An optional associative array of configuration settings.
	 *
	 * @return  MolajoMenu  MolajoMenu object.
	 *
	 * @since  1.0
	 */
	public function getMenu($name = null, $options = array())
	{
		if (!isset($name)) {
			$name = $this->_name;
		}

		$menu = MolajoMenu::getInstance($name, $options);

		if (JError::isError($menu)) {
			return null;
		}

		return $menu;
	}

	/**
	 * Provides a secure hash based on a seed
	 *
	 * @param   string   $seed  Seed string.
	 *
	 * @return  string   A secure hash
	 *
	 * @since  1.0
	 */
	public static function getHash($seed)
	{
		$conf = MolajoFactory::getConfig();

		return md5($conf->get('secret').$seed);
	}

	/**
	 * Create the configuration registry.
	 *
	 * @param   string  $file  The path to the configuration file
	 *
	 * return   object  A MolajoConfig object
	 *
	 * @since  1.0
	 */
	protected function _createConfiguration($file = null)
	{
		require_once $file;

		// Create the MolajoConfig object.
		$config = new MolajoConfig();

		// Get the global configuration object.
		$registry = MolajoFactory::getConfig();

		// Load the configuration values into the registry.
		$registry->loadObject($config);

		return $config;
	}

	/**
	 * Create the user session.
	 *
	 * Old sessions are flushed based on the configuration value for the cookie
	 * lifetime. If an existing session, then the last access time is updated.
	 * If a new session, a session id is generated and a record is created in
	 * the #__sessions table.
	 *
	 * @param   string  $name  The sessions name.
	 *
	 * @return  JSession  JSession on success. May call exit() on database error.
	 *
	 * @since  1.0
	 */
	protected function _createSession($name)
	{
		$options = array();
		$options['name'] = $name;

		switch($this->_applicationId)
		{
			case 0:
				if ($this->getCfg('force_ssl') == 2) {
					$options['force_ssl'] = true;
				}
				break;

			case 1:
				if ($this->getCfg('force_ssl') >= 1) {
					$options['force_ssl'] = true;
				}
				break;
		}

		$session = MolajoFactory::getSession($options);

		//TODO: At some point we need to get away from having session data always in the db.

		$db = MolajoFactory::getDBO();

		// Remove expired sessions from the database.
		$time = time();
		if ($time % 2) {
			// The modulus introduces a little entropy, making the flushing less accurate
			// but fires the query less than half the time.
			$db->setQuery(
				'DELETE FROM `#__session`' .
				' WHERE `time` < '.(int) ($time - $session->getExpire())
			);
			$db->query();
		}

		// Check to see the the session already exists.
		if (($this->getCfg('session_handler') != 'database' && ($time % 2 || $session->isNew()))
			||
			($this->getCfg('session_handler') == 'database' && $session->isNew())
		)
		{
			$this->checkSession();
		}

		return $session;
	}

	/**
	 * Checks the user session.
	 *
	 * If the session record doesn't exist, initialise it.
	 * If session is new, create session variables
	 *
	 * @return  void
	 *
	 * @since  1.0
	 */
	public function checkSession()
	{
		$db 		= MolajoFactory::getDBO();
		$session 	= MolajoFactory::getSession();
		$user		= MolajoFactory::getUser();

		$db->setQuery(
			'SELECT `session_id`' .
			' FROM `#__session`' .
			' WHERE `session_id` = '.$db->quote($session->getId()), 0, 1
		);
		$exists = $db->loadResult();

		// If the session record doesn't exist initialise it.
		if (!$exists) {
			if ($session->isNew()) {
				$db->setQuery(
					'INSERT INTO `#__session` (`session_id`, `application_id`, `time`)' .
					' VALUES ('.$db->quote($session->getId()).', '.(int) $this->getApplicationId().', '.(int) time().')'
				);
			}
			else {
				$db->setQuery(
					'INSERT INTO `#__session` (`session_id`, `application_id`, `guest`, `time`, `userid`, `username`)' .
					' VALUES ('.$db->quote($session->getId()).', '.(int) $this->getApplicationId().', '.(int) $user->get('guest').', '.(int) $session->get('session.timer.start').', '.(int) $user->get('id').', '.$db->quote($user->get('username')).')'
				);
			}

			// If the insert failed, exit the application.
			if (!$db->query()) {
				jexit($db->getErrorMSG());
			}

			// Session doesn't exist yet, so create session variables
			if ($session->isNew()) {
				$session->set('registry',	new JRegistry('session'));
				$session->set('user',		new JUser());
			}
		}
	}

	/**
	 * Gets the application id of the current running application.
	 *
	 * @return  integer  A application identifier.
	 *
	 * @since  1.0
	 */
	public function getApplicationId()
	{
		return $this->_applicationId;
	}

	/**
	 * Is admin interface?
	 *
	 * @return  boolean  True if this application is administrator.
	 *
	 * @since  1.0
	 */
	public function isAdmin()
	{
		return ($this->_applicationId == 1);
	}

	/**
	 * Is site interface?
	 *
	 * @return  boolean  True if this application is site.
	 *
	 * @since  1.0
	 */
	public function isSite()
	{
		return ($this->_applicationId == 0);
	}

	/**
	 * Method to determine if the host OS is  Windows
	 *
	 * @return  boolean  True if Windows OS
	 *
	 * @since  1.0
	 */
	static function isWinOS()
	{
		return strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
	}

	/**
	 * Returns the response as a string.
	 *
	 * @return  string  The response
	 *
	 * @since  1.0
	 */
	public function __toString()
	{
		$compress = $this->getCfg('gzip', false);

		return JResponse::toString($compress);
	}
}
