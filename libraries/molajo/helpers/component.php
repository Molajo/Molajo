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
        $molajoConfig = new MolajoModelConfiguration;

        $layout = '';
        $format = '';
        $componentTable = '';

        /** loads keys for configuration */
        $option = JRequest::getCmd('option', 'com_articles');
        $molajoConfig->getOptionOverrides ($option);

        /** login - necessary to reset for timeout */
        if ($option == 'com_login') {
            JRequest::setVar('view', 'login');
            JRequest::setVar('layout', 'default');
        }

        /** set component paths */
        if (defined('MOLAJO_PATH_COMPONENT')) { } else { define('MOLAJO_PATH_COMPONENT', strtolower(MOLAJO_PATH_BASE.'/components/'.$option)); }
        if (defined('MOLAJO_PATH_COMPONENT_ADMINISTRATOR')) { } else { define('MOLAJO_PATH_COMPONENT_ADMINISTRATOR', strtolower(MOLAJO_PATH_ADMINISTRATOR.'/components/'.$option)); }
        if (defined('JPATH_COMPONENT')) { } else { define('JPATH_COMPONENT', MOLAJO_PATH_COMPONENT); }
        if (defined('JPATH_COMPONENT_ADMINISTRATOR')) { } else { define('JPATH_COMPONENT_ADMINISTRATOR', MOLAJO_PATH_COMPONENT_ADMINISTRATOR); }

        /** 1. View and Layout */
        $view = JRequest::getCmd('view', '');
        $layout = JRequest::getCmd('layout', 'default');

        /** 2. Task **/
        $task = JRequest::getCmd('task', null);
        if ($task == null) {
            if ($layout == 'editor') {
                if (JRequest::getInt('id') == 0) {
                    $task = 'add';
                } else {
                    $task = 'edit';
                }
            } else {
                $task = 'display';
            }
        }

        /** 3. Controller **/
        if (strpos($task,'.')) {
            $controller = substr($task, 0, strpos($task,'.'));
            $task = substr($task, (strpos($task,'.')+1), 99);
        } else {
            $controller = '';
        }

        if ($view == '') {
            $view = $controller;
        }

        /** 4. Default View **/
        if ($view == '') {
            $lookupView = $molajoConfig->getDefaultView (MOLAJO_CONFIG_OPTION_ID_VIEW_PAIRS);

            if ($lookupView === false) {
                MolajoFactory::getApplication()->enqueueMessage(JText::_('MOLAJO_NO_DEFAULT_VIEW_DEFINED'), 'error');
                return false;
            }

        }
echo 'option '.$option.'<br />';
echo 'view '.$view.'<br />';
echo 'layout '.$layout.'<br />';
echo 'task '.$task.'<br />';
echo 'controller '.$controller.'<br />';
die();
        /** 5. For view in hand, determine if it's edit or display view */
        if ($lookupView == '') {
            $lookupView = $view;
        }
        $lookupViewType = $molajoConfig->getViewType (MOLAJO_CONFIG_OPTION_ID_VIEW_PAIRS, $lookupView);
        if ($lookupViewType === false) {
            MolajoFactory::getApplication()->enqueueMessage(JText::_('MOLAJO_INVALID_VIEW_TYPE').' '.$lookupView, 'error');
            return false;

        }

        /** 6. Knowing both view and type, determine values for single and default **/
        $otherView = $molajoConfig->getViewMatch (MOLAJO_CONFIG_OPTION_ID_VIEW_PAIRS, $lookupView, $lookupViewType);
        if ($otherView === false) {
            MolajoFactory::getApplication()->enqueueMessage(JText::_('MOLAJO_INVALID_MATCHING_VIEW').' '.$lookupView, 'error');
            return false;
        }

        if ($lookupViewType == 'default') {
            $defaultView = $lookupView;
            $editView = $otherView;
        } else {
            $defaultView = $otherView;
            $editView = $lookupView;
        }

        /** 8. display controller for add and edit */
        if ($task == 'add' || $task == 'edit') {
            $view = $editView;
            $controller = '';

        } else if ($task == 'display') {
            $view = $defaultView;
            $controller = '';

        } else if ($view == '' && ($controller == $defaultView || $controller == $editView)) {
            /** used in redirects, not for an actual view/layout **/
            $view = $controller;
        }

        /** 9. validate task and controller **/
        if ($controller == '') {
            $results = $molajoConfig->validateTask($task, MOLAJO_CONFIG_OPTION_ID_MULTIPLE_CONTROLLER_TASKS);
            if ($results == false) {
                JError::raiseError(500, JText::_('MOLAJO_INVALID_TASK_DISPLAY_CONTROLLER').' '.$task);
                return false;
            }

        } else if ($controller == $defaultView) {
            $results = $molajoConfig->validateTask ($task, MOLAJO_CONFIG_OPTION_ID_DISPLAY_CONTROLLER_TASKS);
            if ($results == false) {
                JError::raiseError(500, JText::_('MOLAJO_INVALID_TASK_DISPLAY_CONTROLLER').' '.$task);
                return false;
            }

        } else if ($controller == $editView) {
            $results = $molajoConfig->validateTask ($task, MOLAJO_CONFIG_OPTION_ID_EDIT_CONTROLLER_TASKS);
            if ($results == false) {
                JError::raiseError(500, JText::_('MOLAJO_INVALID_TASK_EDIT_CONTROLLER').' '.$task);
                return false;
            }

        } else  {
                JError::raiseError(500, JText::_('MOLAJO_INVALID_CONTROLLER').' '.$controller);
                return false;
        }

        if ($task == 'display' || $task == 'add' || $task == 'edit') {

            /** 4. display controller */
            if ($task == 'add' || $task == 'edit') {
                $view = $editView;
            } else {
                $view = $defaultView;
            }

            /** 10. validate view (display only) **/
            $folder = MOLAJO_PATH_COMPONENT.'/components/'.$option.'/views/'.$view;
            if (JFolder::exists($folder)) {
            } else {
                MolajoFactory::getApplication()->enqueueMessage(JText::_('MOLAJO_INVALID_VIEW_FOLDERNAMES').' '.$folder, 'error');
                return false;
            }

           /** 12. validate format (display only) **/
            $format = JRequest::getCmd('format', 'html');
            $results = $molajoConfig->validateFormat ($format, MOLAJO_CONFIG_OPTION_ID_FORMAT);
            if ($results == false) {
                JError::raiseError(500, JText::_('MOLAJO_INVALID_FORMAT').' '.$format);
                return false;
            }

            /** 13. validate layout (display only) **/
            $layout = JRequest::getCmd('layout', '');

            if ($layout == '') {
                $layout = 'default';
            }

            if (MolajoFactory::getApplication()->getName() == 'administrator') {
                $fileName = MOLAJO_PATH_ADMINISTRATOR.'/components/'.$option.'/views/'.$view.'/tmpl/'.$layout.'.php';
                if ($view == $defaultView && $layout == 'default') {
                    $layout = 'manager';
                }
                if ($view == $editView && $layout == 'default') {
                    $layout = 'edit';
                }
            } else {
                $fileName = MOLAJO_PATH_SITE.'/components/'.$option.'/views/'.$view.'/tmpl/'.$layout.'.php';
            }

            if (JFile::exists($fileName)) {

            } else {
                MolajoFactory::getApplication()->enqueueMessage(JText::_('MOLAJO_INVALID_LAYOUT').' '.$fileName, 'error');
                return false;
            }

        }

        /** 15. set acl implementation selected for component **/
        $aclImplementation = $molajoConfig->getSingleConfigurationValue (MOLAJO_CONFIG_OPTION_ID_ACL_IMPLEMENTATION);
        if ($aclImplementation == false) {
            $aclImplementation = 'core';
        }

        /** 15. id **/
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
                JError::raiseError(500, JText::_('MOLAJO_ERROR_TASK_MAY_NOT_HAVE_REQUEST_ID'));
                return false;
            }
        }


        /** 11. validate table (display only) **/
        $componentTable = $molajoConfig->getSingleConfigurationValue (MOLAJO_CONFIG_OPTION_ID_TABLE);
        if ($componentTable == false || $componentTable == '') {
             $componentTable = '__'.$defaultView;
        }

        /** frontend */

        if ($layout == 'editor') {
            if ((int) $id == 0) {
                $task = 'add';
            } else {
                $task = 'edit';
            }
        }

        /** add '.' back to interface with JController **/
        if ($task == 'edit') {
            $layout = 'editor';
        }

        /** add '.' back to interface with JController **/
        if ($task == 'add' || $task == 'edit' || $task == 'display') {
        } else {
            $task = $controller.'.'.$task;
        }
//                    $menus = MolajoFactory::getApplication()->getMenu('site');
//                    $active = $menus->getActive();

//                    if ($active && $active->component == $option) {
//                        echo $active->id;
//                    }

        /** set request to validated values **/
        JRequest::setVar('EditView', $editView);
        JRequest::setVar('DefaultView', $defaultView);

        JRequest::setVar('format', $format);
        JRequest::setVar('id', (int) $id);
        JRequest::setVar('cid', (array) $cids);

        JRequest::setVar('ComponentTable', $componentTable);
        JRequest::setVar('filterFieldName', 'config_manager_list_filters');
        JRequest::setVar('selectFieldName', 'config_manager_grid_column');
        JRequest::setVar('aclImplementation', $aclImplementation);

        JRequest::setVar('controller', $controller);

        JRequest::setVar('task', $task);
        JRequest::setVar('layout', $layout);
        JRequest::setVar('view', $view);

        /** @var $session */
        $session = JFactory::getSession();

        $session->set('molajoPageOption', $option);
        $session->set('molajoPageTask', $task);
        $session->set('molajoPageView', $view);
        $session->set('molajoPageLayout', $layout);

        $session->set('molajoPageEditView', $option);
        $session->set('molajoPageTask', $task);
        $session->set('molajoPageView', $view);
        $session->set('molajoPageLayout', $layout);

$debug == true;
if ($debug) {
echo 'the very end of validate'.'<br />';
echo $session->get('molajoComponent');
echo '$lookupViewType '.$lookupViewType.'<br />';
echo '$controller '.$controller.'<br />';
echo '$view '.$view.'<br />';
echo '$defaultView '.$defaultView.'<br />';
echo '$editView '.$editView.'<br />';
echo '$layout '.$layout.'<br />';
echo '$task '.$task.'<br />';
echo '$controller '.$controller.'<br />';
echo 'the very end of validate'.'<br />';
die();
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