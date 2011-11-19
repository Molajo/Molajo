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
     * @param   string   $type    The plugin type, relates to the sub-directory in the plugins directory.
     * @param   string   $plugin    The plugin name.
     *
     * @return  mixed    An array of plugin data objects, or a plugin data object.
     * @since   1.0
     */
    public static function getPlugin($type, $plugin = null)
    {
        $result = array();
        $plugins = self::_load();

        if ($plugin) {

            foreach ($plugins as $p) {
                // Is this plugin in the right group?
                if ($p->folder == $type && $p->title == $plugin) {
                    $result = $p;
                    break;
                }
            }

        } else {

            foreach ($plugins as $p) {

                if ($p->folder == $type) {
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
     * @param   string   $type    The plugin type, relates to the sub-directory in the plugins directory.
     * @param   string   $plugin    The plugin name.
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
     * @param   string   $type    The plugin type, relates to the sub-directory in the plugins directory.
     * @param   string   $plugin    The plugin name.
     * @param   bool     $autocreate
     * @param   JDispatcher    $dispatcher    Optionally allows the plugin to use a different dispatcher.
     *
     * @return  boolean        True on success.
     * @since   1.0
     */
    public static function importPlugin($type, $plugin = null, $autocreate = true, $dispatcher = null)
    {
        static $loaded = Array();

        $defaults = false;

        if (is_null($plugin)
            && $autocreate === true
            && is_null($dispatcher)
        ) {
            $defaults = true;
        }

        if (isset($loaded[$type]) && $defaults === true) {
        } else {
            $results = null;

            $plugins = self::_load($type);

            for ($i = 0, $t = count($plugins); $i < $t; $i++) {

                if (($plugins[$i]->folder == $type)
                    && ($plugins[$i]->title == $plugin || $plugin === null)
                ) {

                    self::_import($plugins[$i], $autocreate, $dispatcher);

                    $results = true;
                }

            }

            if ($defaults === true) {
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
     * @param   plugin      $plugin        The plugin.
     * @param   boolean      $autocreate
     * @param   JDispatcher    $dispatcher    Optionally allows the plugin to use a different dispatcher.
     *
     * @return  boolean        True on success.
     * @since   1.0
     */
    protected static function _import($plugin, $autocreate = true, $dispatcher = null)
    {
        static $paths = array();

        $plugin->folder = preg_replace('/[^A-Z0-9_\.-]/i', '', $plugin->folder);
        $plugin->title = preg_replace('/[^A-Z0-9_\.-]/i', '', $plugin->title);

        $path = MOLAJO_EXTENSION_PLUGINS.'/'.$plugin->folder.'/'.$plugin->title.'/'.$plugin->title.'.php';

        if (JFile::exists($path)) {
            require_once $path;
        } else {
            return false;
        }

        if ($autocreate) {

            if (is_object($dispatcher)) {
            } else {
                $dispatcher = JDispatcher::getInstance();
            }

            $className = 'plg'.$plugin->folder.$plugin->title;
            if (class_exists($className)) {

            } else {
                $plugin = self::getPlugin($plugin->folder, $plugin->title);
                new $className($dispatcher, (array)($plugin));
            }
        }
    }

    /**
     * _load
     *
     * Loads the published plugins.
     *
     * @static
     * @return bool|mixed
     */
    protected static function _load()
    {
        static $plugins;

        if (isset($plugins)) {
            return $plugins;
        }

        $plugins = MolajoExtensionHelper::getExtensions(MOLAJO_CONTENT_TYPE_EXTENSION_PLUGINS);
        return $plugins;
    }
}