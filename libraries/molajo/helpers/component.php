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
     *
	 * @since  1.0
     */
	protected static $_components = array();

	/**
	 * getComponent
     * 
     * Get component information.
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

		return $result->enabled;
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
     * _load
     *
	 * Load installed components into the _components array.
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

		$query->select($db->namequote('extension_id').' as "id"');
		$query->select($db->namequote('element').' as "option"');
		$query->select($db->namequote('params'));
		$query->select($db->namequote('enabled'));
		$query->select($db->namequote('access'));
		$query->select($db->namequote('asset_id'));
		$query->from($db->namequote('#__extensions'));
		$query->where($db->namequote('type').' = '.$db->quote('component'));
		$query->where($db->namequote('element').' = '.$db->quote($option));
		$query->where($db->namequote('enabled').' = 1');
		$query->where('application_id = '.MOLAJO_APPLICATION_ID);

        $acl = new MolajoACL ();
        $acl->getQueryInformation ('', $query, 'viewaccess', array('table_prefix'=>''));

		$db->setQuery($query->__toString());

        if (JFactory::getConfig()->get('caching') > 0) {
            $cache = MolajoFactory::getCache('_system','callback');
		    self::$_components[$option] = $cache->get(array($db, 'loadObject'), null, $option, false);
        } else {
            self::$_components[$option] = $db->loadObject();
        }

		if ($error = $db->getErrorMsg()
            || empty(self::$_components[$option])) {
			JError::raiseWarning(500, JText::sprintf('MOLAJO_APPLICATION_ERROR_COMPONENT_NOT_LOADING', $option, $error));
			return false;
		}

		if (is_string(self::$_components[$option]->params)) {
			$temp = new JRegistry;
			$temp->loadString(self::$_components[$option]->params);
			self::$_components[$option]->params = $temp;
		}

		return true;
	}

	/**
	 * renderComponent
     *
     * Render the component.
	 *
	 * @param   string  $request An array of component information
	 * @param   array   $params  The component parameters
	 *
	 * @return  object
	 * @since  1.0
	 */
	public static function renderComponent($request, $params = array())
	{
		// Record the scope (what is this scope?)
		$scope = MolajoFactory::getApplication()->scope;

		// Set scope to extension name
		MolajoFactory::getApplication()->scope = $request['option'];

        /** extension path and entry point */
        $path = $request['component_path'].'/'.$request['no_com_option'].'.php';

        /** verify extension is enabled */
		if (self::isEnabled($request['option'])
                && file_exists($path)) {
        } else {
			JError::raiseError(404, JText::_('MOLAJO_APPLICATION_ERROR_COMPONENT_NOT_FOUND'));
		}

		//  Execute Extension
		require_once $path;

		// Revert scope
		MolajoFactory::getApplication()->scope = $scope;
	}
}