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
     * getComponentName
     *
	 * Return the name of the request component [main component]
     * Moved in from the Application Helper
	 *
	 * @param   string  $default The default option
	 * @return  string  Option
	 * @since   1.0
	 */
	public static function getComponentName($default = NULL)
	{
		static $option;

		if ($option) {
			return $option;
		}

		$option = strtolower(JRequest::getCmd('option'));

		if (empty($option)) {
			$option = $default;
		}

		JRequest::setVar('option', $option);
		return $option;
	}

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
        $date = MolajoFactory::getDate();
        $now = $date->toMySQL();
        $nullDate = $db->getNullDate();

		$query->select('a.'.$db->namequote('id'));
		$query->select('a.'.$db->namequote('parameters'));
		$query->select('a.'.$db->namequote('enabled'));
		$query->select('a.'.$db->namequote('asset_id'));

		$query->from($db->namequote('#__extension_instances as a'));

        $query->where('a.'.$db->namequote('extension_type_id').' = 1');
        $query->where('a.'.$db->namequote('status').' = '.MOLAJO_STATE_PUBLISHED);
        $query->where('(a.start_publishing_datetime = '.$db->Quote($nullDate).' OR a.start_publishing_datetime <= '.$db->Quote($now).')');
        $query->where('(a.stop_publishing_datetime = '.$db->Quote($nullDate).' OR a.stop_publishing_datetime >= '.$db->Quote($now).')');

        /** extensions table */
        $query->select('b.'.$db->namequote('name').' as "option"');
		$query->from($db->namequote('#__extensions as b'));
		$query->where('b.'.$db->namequote('id').' = a.'.$db->namequote('extension_id'));

        /** application extensions */
		$query->from($db->namequote('#__molajo_application_extensions as c'));
		$query->where('c.'.$db->namequote('application_id').' = '.MOLAJO_APPLICATION_ID);
		$query->where('c.'.$db->namequote('extension_id').' = b.'.$db->namequote('id'));
		$query->where('c.'.$db->namequote('extension_instance_id').' = a.'.$db->namequote('id'));

        /** site applications */
		$query->from($db->namequote('#__molajo_site_applications as d'));
		$query->where('c.'.$db->namequote('site_id').' = '.MOLAJO_SITE_ID);
		$query->where('c.'.$db->namequote('application_id').' = '.MOLAJO_APPLICATION_ID);

        $acl = new MolajoACL ();
        $acl->getQueryInformation ('', $query, 'viewaccess', array('table_prefix'=>''));
//echo '<pre>';var_dump($request);'</pre>';

		$db->setQuery($query->__toString());

        if (MolajoFactory::getConfig()->get('caching') > 0) {
            $cache = MolajoFactory::getCache('_system','callback');
		    self::$_components[$option] = $cache->get(array($db, 'loadObject'), null, $option, false);
        } else {
            self::$_components[$option] = $db->loadObject();
        }

		if ($error = $db->getErrorMsg()
            || empty(self::$_components[$option])) {
			MolajoError::raiseWarning(500, MolajoText::sprintf('MOLAJO_APPLICATION_ERROR_COMPONENT_NOT_LOADING', $option, $error));
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
		/** record scope */
		$scope = MolajoFactory::getApplication()->scope;
		MolajoFactory::getApplication()->scope = $request['option'];

        /** extension path and entry point */
        $path = $request['component_path'].'/'.$request['no_com_option'].'.php';

        /** installation does not have enabled extensions */
        if ($request['application_id'] == 0
            && file_exists($path)) {

        } elseif (self::isEnabled($request['option'])
                && file_exists($path)) {
            
        } else {
			MolajoError::raiseError(404, MolajoText::_('MOLAJO_APPLICATION_ERROR_COMPONENT_NOT_FOUND'));
		}

        /** execute the component */
        ob_start();
        require_once $path;
        $output = ob_get_contents();
        ob_end_clean();

		/** Revert scope */
		MolajoFactory::getApplication()->scope = $scope;
        
        return $output;
   }
}