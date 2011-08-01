<?php
/**
 * @package     Molajo
 * @subpackage  Helper
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Component Helper
 *
 * @package     Molajo
 * @subpackage  Component Helper
 * @since       1.0
 */
class MolajoComponentHelper
{

    /**
     * @var array $_components - list of components from cache
	 * @since  1.0
     */
	protected static $_components = array();

	/**
	 * getComponent
     * 
     * Get the component information.
	 *
	 * @param   string   $option  component option.
	 * @param   boolean  $string  If set and the component does not exist, the enabled attribute will be set to false
	 *
	 * @return  object   An object with information about the component.
	 * @since  1.0
	 */
	public static function getComponent($option, $strict = false)
	{
		if (isset(self::$_components[$option])) {
            $result = self::$_components[$option];
        } else {
			if (self::_load($option)){
				$result = self::$_components[$option];
			} else {
				$result				= new stdClass;
				$result->enabled	= $strict ? false : true;
				$result->params		= new JRegistry;
			}
		}

		return $result;
	}

	/**
	 * isEnabled
     *
     * Checks if the component is enabled
	 *
	 * @param   string   $option  The component option.
	 * @param   boolean  $string  If set and the component does not exist, false will be returned
	 *
	 * @return  boolean
	 * @since  1.0
	 */
	public static function isEnabled($option, $strict = false)
	{
		$result = self::getComponent($option, $strict);

		return ($result->enabled | MolajoFactory::getApplication()->isAdmin());
	}

	/**
	 * getParams
     *
     * Gets the parameter object for the component
	 *
	 * @param   string   $option  The option for the component.
	 * @param   boolean  $strict  If set and the component does not exist, false will be returned
	 *
	 * @return  JRegistry  A JRegistry object.
	 *
	 * @see     JRegistry
	 * @since  1.0
	 */
	public static function getParams($option, $strict = false)
	{
		$component = self::getComponent($option, $strict);

		return $component->params;
	}

    /**
     * getRequest
     *
     * Gets the Request Object and populates Page Session Variables for Component
     *
     * @return bool
     */
    public function getRequest ()
    {
        /** initialization */
        $option = '';
        $task = '';
        $view = '';
        $layout = '';
        $format = '';
        $componentTable = '';

        /** MolajoModelConfiguration Model */
        $molajoConfig = new MolajoModelConfiguration ($option);

        /** 1. Option */
        $option = JRequest::getCmd('option', null);
        if ($option == null) {
            $option = $molajoConfig->getOptionValue (MOLAJO_CONFIG_OPTION_ID_DEFAULT_OPTION + (int) MOLAJO_APPLICATION_ID);
            if ($option === false) {
                MolajoFactory::getApplication()->enqueueMessage(JText::_('MOLAJO_NO_DEFAULT_OPTION_DEFINED'), 'error');
                return false;
            }
        }

        /** set component paths */
        if (defined('MOLAJO_PATH_COMPONENT')) { } else { define('MOLAJO_PATH_COMPONENT', strtolower(MOLAJO_PATH_BASE.'/components/'.$option)); }
        if (defined('MOLAJO_PATH_COMPONENT_ADMINISTRATOR')) { } else { define('MOLAJO_PATH_COMPONENT_ADMINISTRATOR', strtolower(MOLAJO_PATH_ADMINISTRATOR.'/components/'.$option)); }
        if (defined('JPATH_COMPONENT')) { } else { define('JPATH_COMPONENT', MOLAJO_PATH_COMPONENT); }
        if (defined('JPATH_COMPONENT_ADMINISTRATOR')) { } else { define('JPATH_COMPONENT_ADMINISTRATOR', MOLAJO_PATH_COMPONENT_ADMINISTRATOR); }

        /** 2. Task */
        $task = JRequest::getCmd('task', 'display');
        if (strpos($task,'.')) {
            $task = substr($task, (strpos($task,'.')+1), 99);
        }

        /** 3. Controller */
        $controller = $molajoConfig->getOptionLiteralValue (MOLAJO_CONFIG_OPTION_ID_TASK_TO_CONTROLLER, $task);
        if ($controller === false) {
            JError::raiseError(500, JText::_('MOLAJO_INVALID_TASK_DISPLAY_CONTROLLER').' '.$task);
            return false;
        }

        if ($task == 'display') {

            /** 4. View **/
            $view = JRequest::getCmd('view', null);
            if ($view == null) {
                $results = false;
            } else {
                $results = $molajoConfig->getOptionLiteralValue (MOLAJO_CONFIG_OPTION_ID_VIEWS, $view);
            }

            if ($results === false) {
                $view = $molajoConfig->getOptionValue (MOLAJO_CONFIG_OPTION_ID_DEFAULT_VIEW);
                if ($view === false) {
                    MolajoFactory::getApplication()->enqueueMessage(JText::_('MOLAJO_NO_DEFAULT_VIEW_DEFINED'), 'error');
                    return false;
                }
            }

            /** 5. Layout **/
            $layout = JRequest::getCmd('layout', null);
            if ($layout == null) {
                $results = false;
            } else {
                if ($view == 'edit') {
                    $results = $molajoConfig->getOptionLiteralValue (MOLAJO_CONFIG_OPTION_ID_EDIT_VIEW_LAYOUTS, $layout);
                } else {
                    $results = $molajoConfig->getOptionLiteralValue (MOLAJO_CONFIG_OPTION_ID_DISPLAY_VIEW_LAYOUTS, $layout);
                }
            }

            if ($results === false) {
                if ($view == 'edit') {
                    $layout = $molajoConfig->getOptionValue (MOLAJO_CONFIG_OPTION_ID_DEFAULT_EDIT_VIEW_LAYOUTS);
                } else {
                    $layout = $molajoConfig->getOptionValue (MOLAJO_CONFIG_OPTION_ID_DEFAULT_DISPLAY_VIEW_LAYOUTS);
                }
                if ($layout === false) {
                    MolajoFactory::getApplication()->enqueueMessage(JText::_('MOLAJO_NO_DEFAULT_LAYOUT_FOR_VIEW_DEFINED'), 'error');
                    return false;
                }
            }

            /** 6. Format */
            $format = JRequest::getCmd('format', null);
            if ($format == null) {
                $results = false;
            } else {
                if ($view == 'edit') {
                    $results = $molajoConfig->getOptionLiteralValue (MOLAJO_CONFIG_OPTION_ID_EDIT_VIEW_FORMATS, $format);
                } else {
                    $results = $molajoConfig->getOptionLiteralValue (MOLAJO_CONFIG_OPTION_ID_DISPLAY_VIEW_FORMATS, $format);
                }
            }

            if ($results === false) {
                if ($view == 'edit') {
                    $format = $molajoConfig->getOptionValue (MOLAJO_CONFIG_OPTION_ID_DEFAULT_EDIT_VIEW_FORMATS);
                } else {
                    $format = $molajoConfig->getOptionValue (MOLAJO_CONFIG_OPTION_ID_DEFAULT_DISPLAY_VIEW_FORMATS);
                }
                if ($format === false) {
                    MolajoFactory::getApplication()->enqueueMessage(JText::_('MOLAJO_NO_DEFAULT_LAYOUT_FOR_VIEW_DEFINED'), 'error');
                    return false;
                }
            }
        } else {
            /** amy: come back and get redirect stuff later */
            $view = '';
            $layout = '';
            $format = '';
        }

        /** 7. id and cid */
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

        /** 8. acl implementation */
        $aclImplementation = $molajoConfig->getOptionValue (MOLAJO_CONFIG_OPTION_ID_ACL_IMPLEMENTATION);
        if ($aclImplementation == false) {
            $aclImplementation = 'core';
        }

        /** 9. component table */
        $componentTable = $molajoConfig->getOptionValue (MOLAJO_CONFIG_OPTION_ID_TABLE);
        if ($aclImplementation == false) {
            $aclImplementation = 'core';
        }

        JRequest::setVar('controller', $controller);

        JRequest::setVar('option', $option);
        JRequest::setVar('view', $view);
        JRequest::setVar('layout', $layout);
        JRequest::setVar('task', $task);
        JRequest::setVar('format', $format);

        JRequest::setVar('id', (int) $id);
        JRequest::setVar('cid', (array) $cids);

        /** Set Session Variables for molajoPage */
        $session = JFactory::getSession();

        $session->set('molajoPageController', $controller);

        $session->set('molajoPageOption', $option);
        $session->set('molajoPageView', $view);
        $session->set('molajoPageLayout', $layout);
        $session->set('molajoPageTask', $task);
        $session->set('molajoPageFormat', $format);

        $session->set('molajoPageID', (int) $id);
        $session->set('molajoPageCID', (array) $cids);

        $session->set('molajoPageComponentTable', $layout);
        $session->set('molajoPageFilterFieldName', $task);
        $session->set('molajoPageSelectFieldName', $format);
        $session->set('molajoPageACLImplementation', $task);
        $session->set('molajoPageInitiatingExtension', 'component');

$debug == true;
if ($debug) {

    echo 'option '.$option.'<br />';
    echo 'view '.$view.'<br />';
    echo 'layout '.$layout.'<br />';
    echo 'task '.$task.'<br />';
    echo 'format '.$format.'<br />';
    echo 'controller '.$controller.'<br />';
    echo 'id '.$id.'<br />';
    echo 'cid '.var_dump($cids).'<br />';
}
        return true;
    }

	/**
	 * renderComponent
     *
     * Render the component.
	 *
	 * @param   string  $option  The component option.
	 * @param   array   $params  The component parameters
	 *
	 * @return  object
	 * @since  1.0
	 */
	public static function renderComponent($option, $params = array())
	{
        /** @var $session */
        $session = JFactory::getSession();
echo 'from last time '. $session->get('molajoComponent');

        // Validate request
        $results = self::getRequest();
        
		// Initialise variables.
		$app	= MolajoFactory::getApplication();

		// Load template language files.
		$template	= $app->getTemplate(true)->template;

		$lang = MolajoFactory::getLanguage();
			$lang->load('tpl_'.$template, MOLAJO_PATH_BASE, null, false, false)
		||	$lang->load('tpl_'.$template, MOLAJO_PATH_THEMES."/$template", null, false, false)
		||	$lang->load('tpl_'.$template, MOLAJO_PATH_BASE, $lang->getDefault(), false, false)
		||	$lang->load('tpl_'.$template, MOLAJO_PATH_THEMES."/$template", $lang->getDefault(), false, false);

		if (empty($option)) {
			JError::raiseError(404, JText::_('JLIB_APPLICATION_ERROR_COMPONENT_NOT_FOUND'));
			return;
		}

		 // Record the scope
		$scope = $app->scope;
		// Set scope to component name
		$app->scope = $option;

        /** component path and entry point */
		$option	= preg_replace('/[^A-Z0-9_\.-]/i', '', $option);
		$file	= substr($option, 4);
        $path   = MOLAJO_PATH_COMPONENT.'/'.$file.'.php';

        /** verify component is enabled */
		if (self::isEnabled($option)
                && file_exists($path)) {
        } else {
			JError::raiseError(404, JText::_('JLIB_APPLICATION_ERROR_COMPONENT_NOT_FOUND'));
		}

		$task = JRequest::getString('task');

		// Load common and local language files.
			$lang->load($option, MOLAJO_PATH_BASE, null, false, false)
		||	$lang->load($option, MOLAJO_PATH_COMPONENT, null, false, false)
		||	$lang->load($option, MOLAJO_PATH_BASE, $lang->getDefault(), false, false)
		||	$lang->load($option, MOLAJO_PATH_COMPONENT, $lang->getDefault(), false, false);

		// Handle template preview outlining.
		$contents = null;

		// Execute the component.
		ob_start();
		require_once $path;
		$contents = ob_get_contents();
		ob_end_clean();

		if (($path = MolajoApplicationHelper::getPath('toolbar')) && $app->isAdmin()) {
			// Get the task again, in case it has changed
			$task = JRequest::getString('task');

			// Make the toolbar
			include_once $path;
		}

		// Revert the scope
		$app->scope = $scope;
## Erase cart session data
$session->clear('cart');
		return $contents;
	}

	/**
	 * Load the installed components into the _components property.
	 *
	 * @param   string  $option  The element value for the extension
	 *
	 * @return  bool  True on success
	 * @since  1.0
	 */
	protected static function _load($option)
	{
		$db		= MolajoFactory::getDbo();
		$query	= $db->getQuery(true);
		$query->select('extension_id AS "id", element AS "option", params, enabled, access, asset_id ');
		$query->from('#__extensions');
		$query->where('`type` = '.$db->quote('component'));
		$query->where('`element` = '.$db->quote($option));
		$db->setQuery($query);

		$cache = MolajoFactory::getCache('_system','callback');

		self::$_components[$option] =  $cache->get(array($db, 'loadObject'), null, $option, false);

		if ($error = $db->getErrorMsg()
            || empty(self::$_components[$option])) {
			JError::raiseWarning(500, JText::sprintf('JLIB_APPLICATION_ERROR_COMPONENT_NOT_LOADING', $option, $error));
			return false;
		}

        /** Convert parameters to an object */
		if (is_string(self::$_components[$option]->params)) {
			$temp = new JRegistry;
			$temp->loadString(self::$_components[$option]->params);
			self::$_components[$option]->params = $temp;
		}

		return true;
	}
}