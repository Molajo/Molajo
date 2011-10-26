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
 * Plugin Helper
 *
 * @package     Molajo
 * @subpackage  Plugin Helper
 * @since       1.0
 */
abstract class MolajoPluginHelper
{
	/**
	 * getPlugin
     *
     * Get the plugin data of a specific type if no specific plugin is specified
	 * otherwise only the specific plugin data is returned.
	 *
	 * @param   string   $type	The plugin type, relates to the sub-directory in the plugins directory.
	 * @param   string   $plugin	The plugin name.
	 *
	 * @return  mixed    An array of plugin data objects, or a plugin data object.
	 * @since   1.0
	 */
	public static function getPlugin($type, $plugin = null)
	{
		$result		= array();
		$plugins	= self::_load();

		if ($plugin) {

            foreach($plugins as $p) {
                // Is this plugin in the right group?
                if ($p->type == $type && $p->name == $plugin) {
                    $result = $p;
                    break;
                }
            }

		} else {

            foreach($plugins as $p) {

                if ($p->type == $type) {
                    $result[] = $p;
                }
            }
		}

		return $result;
	}

	/**
	 * isEnabled
     *
     * Checks if a plugin is enabled.
	 *
	 * @param   string   $type	The plugin type, relates to the sub-directory in the plugins directory.
	 * @param   string   $plugin	The plugin name.
	 *
	 * @return  boolean
	 * @since   1.0
	 */
	public static function isEnabled($type, $plugin = null)
	{
		$result = self::getPlugin($type, $plugin);
		return (!empty($result));
	}

	/**
	 * importPlugin
     *
     * Loads all the plugin files for a particular type if no specific plugin is specified
	 * otherwise only the specific plugin is loaded.
	 *
	 * @param   string   $type	The plugin type, relates to the sub-directory in the plugins directory.
	 * @param   string   $plugin	The plugin name.
	 * @param   bool     $autocreate
	 * @param   JDispatcher	$dispatcher	Optionally allows the plugin to use a different dispatcher.
	 *
	 * @return  boolean		True on success.
	 * @since   1.0
	 */
	public static function importPlugin($type, $plugin = null, $autocreate = true, $dispatcher = null)
	{
		static $loaded = Array();

		// check for the default args, if so we can optimise cheaply
		$defaults = false;
		if (is_null($plugin)
            && $autocreate == true
            && is_null($dispatcher)) {
			$defaults = true;
		}

		if (!isset($loaded[$type]) || !$defaults) {
			$results = null;

			// Load the plugins from the database.
			$plugins = self::_load($type);

			// Get the specified plugin(s).
			for ($i = 0, $t = count($plugins); $i < $t; $i++) {

				if ($plugins[$i]->type == $type
                    && ($plugins[$i]->name == $plugin ||  $plugin === null)) {
					self::_import($plugins[$i], $autocreate, $dispatcher);
					$results = true;
				}

 			}

			// Bail out early if we're not using default args
			if($defaults) {
            } else {
				return $results;
			}

			$loaded[$type] = $results;
		}

		return $loaded[$type];
	}

	/**
	 * _import
     *
     * Loads the plugin file.
	 *
	 * @param   MolajoPlugin		$plugin		The plugin.
	 * @param   boolean  	$autocreate
	 * @param   JDispatcher	$dispatcher	Optionally allows the plugin to use a different dispatcher.
	 *
	 * @return  boolean		True on success.
	 * @since   1.0
	 */
	protected static function _import($plugin, $autocreate = true, $dispatcher = null)
	{
		static $paths = array();

		$plugin->type = preg_replace('/[^A-Z0-9_\.-]/i', '', $plugin->type);
		$plugin->name = preg_replace('/[^A-Z0-9_\.-]/i', '', $plugin->name);

		$path = MOLAJO_PATH_PLUGINS.'/'.$plugin->type.'/'.$plugin->name.'/'.$plugin->name.'.php';
		require_once $path;

        if ($autocreate) {

            if (is_object($dispatcher)) {
            } else {
                $dispatcher = JDispatcher::getInstance();
            }

            $className = 'plg'.$plugin->type.$plugin->name;
            if (class_exists($className)) {

            } else {
                $plugin = self::getPlugin($plugin->type, $plugin->name);
                new $className($dispatcher, (array)($plugin));
            }
        }
	}

	/**
	 * _load
     *
     * Loads the published plugins.
	 *
	 * @return  void
	 * @since   1.0
	 */
	protected static function _load()
	{
		static $plugins;

		if (isset($plugins)) {
			return $plugins;
		}

		$cache 	= MolajoFactory::getCache('com_plugins', '');

        $db		= MolajoFactory::getDbo();
        $query	= $db->getQuery(true);

        $query->select('folder AS type, element AS name, params')
            ->from('#__extensions')
            ->where('enabled >= 1')
            ->where('element != "sef"')
            ->where('element != "joomla"')
            ->where('type ='.$db->Quote('plugin').$folderClause)
            ->where('state >= 0')
            ->order('ordering');

        $acl = new MolajoACL ();
        $acl->getQueryInformation ('', $query, 'viewaccess', array('table_prefix'=>''));

        /** run query **/
        $hash = hash('md5',$query->__toString(), false);
        $plugins = $cache->get($hash);

        if ($plugins === false) {
			$plugins = $db->setQuery($query)
				->loadObjectList();

			if ($error = $db->getErrorMsg()) {
				JError::raiseWarning(500, $error);
				return false;
			}

			$cache->store($plugins, $hash);
		}

		return $plugins;
	}
}