<?php
/**
 * @package     Molajo
 * @subpackage  Helper
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Extension
 *
 * @package     Molajo
 * @subpackage  Helper
 * @since       1.0
 */
abstract class MolajoExtensionHelper
{
    /**
     * get
     *
     * Retrieves Extension data from the extension and extension instances
     * Verifies access for user, application and site
     *
     * @param   $asset_type_id
     * @param   $extension
     *
     * @return  bool|mixed
     * @since   1.0
     */
    public static function get($asset_type_id = 0, $extension = null)
    {
        $db = Services::DB();
        $query = $db->getQuery(true);
        $now = Services::Date()->getDate()->toSql();
        $nullDate = $db->getNullDate();

        /**
         *  a. Extensions Instances Table
         */
        $query->select('a.' . $db->nq('id') . ' as extension_instance_id');
        $query->select('a.' . $db->nq('asset_type_id'));
        $query->select('a.' . $db->nq('title'));
        $query->select('a.' . $db->nq('subtitle'));
        $query->select('a.' . $db->nq('alias'));
        $query->select('a.' . $db->nq('content_text'));
        $query->select('a.' . $db->nq('protected'));
        $query->select('a.' . $db->nq('featured'));
        $query->select('a.' . $db->nq('stickied'));
        $query->select('a.' . $db->nq('status'));
        $query->select('a.' . $db->nq('custom_fields'));
        $query->select('a.' . $db->nq('parameters'));
        $query->select('a.' . $db->nq('metadata'));
        $query->select('a.' . $db->nq('ordering'));
        $query->select('a.' . $db->nq('language'));

        $query->from($db->nq('#__extension_instances') . ' as a');
        $query->where('a.' . $db->nq('extension_id') . ' > 0 ');

        /** extension specified by id, title or request for list */
        if ((int)$extension > 0) {
            $query->where('(a.' . $db->nq('id') .
                    ' = ' . (int)$extension . ')'
            );
        } else if ($extension == null) {
        } else {
            $query->where('(a.' . $db->nq('title') .
                    ' = ' . $db->q($extension) . ')'
            );

        }
        if ((int)$asset_type_id > 0) {
            $query->where('a.' . $db->nq('asset_type_id') .
                    ' = ' . (int)$asset_type_id
            );
        }

        $query->where('a.' . $db->nq('status') .
                ' = ' . MOLAJO_STATUS_PUBLISHED
        );
        $query->where('(a.start_publishing_datetime = ' .
                $db->q($nullDate) .
                ' OR a.start_publishing_datetime <= ' . $db->q($now) . ')'
        );
        $query->where('(a.stop_publishing_datetime = ' .
                $db->q($nullDate) .
                ' OR a.stop_publishing_datetime >= ' . $db->q($now) . ')'
        );

        /** Assets Join and View Access Check */
        Services::Access()
            ->setQueryViewAccess(
            $query,
            array('join_to_prefix' => 'a',
                'join_to_primary_key' => 'id',
                'asset_prefix' => 'b_assets',
                'select' => true
            )
        );

        /** b_asset_types. Asset Types Table  */
        $query->select($db->nq('b_asset_types.title') . ' as asset_type_title');
        $query->from($db->nq('#__asset_types') . ' as b_asset_types');
        $query->where('b_assets.asset_type_id = b_asset_types.id');
        $query->where('b_asset_types.' .
                $db->nq('component_option') .
                ' = ' . $db->q('extensions')
        );

        /**
         *  c. Application Table
         *      Extension Instances must be enabled for the Application
         */
        $query->from($db->nq('#__application_extension_instances') .
            ' as c');
        $query->where('c.' . $db->nq('extension_instance_id') .
            ' = a.' . $db->nq('id'));
        $query->where('c.' . $db->nq('application_id') .
            ' = ' . MOLAJO_APPLICATION_ID);

        /**
         *  d. Site Table
         *      Extension Instances must be enabled for the Site
         */
        $query->from($db->nq('#__site_extension_instances') .
            ' as d');
        $query->where('d.' . $db->nq('extension_instance_id') .
            ' = a.' . $db->nq('id'));
        $query->where('d.' . $db->nq('site_id') .
            ' = ' . SITE_ID);

        /**
         *  Run Query
         */
        $db->setQuery($query->__toString());
        $extensions = $db->loadObjectList();

        if ($error = $db->getErrorMsg()) {
            MolajoError::raiseWarning(500, $error);
            return false;
        }

        return $extensions;
    }

    /**
     * getInstanceID
     *
     * Retrieves Extension ID, given title
     *
     * @static
     *
     * @param  $asset_type_id
     * @param  $title
     *
     * @return  bool|mixed
     * @since   1.0
     */
    public static function getInstanceID($asset_type_id, $title)
    {
        $db = Services::DB();
        $query = $db->getQuery(true);

        $query->select('a.' . $db->nq('id'));
        $query->from($db->nq('#__extension_instances') . ' as a');
        $query->where('a.' . $db->nq('title') . ' = ' .
            $db->q($title));
        $query->where('a.' . $db->nq('asset_type_id') .
            ' = ' . (int)$asset_type_id);
        $db->setQuery($query->__toString());
        $id = $db->loadResult();

        if ($error = $db->getErrorMsg()) {
            MolajoError::raiseWarning(500, $error);
            return false;
        }

        return $id;
    }

    /**
     * getInstanceTitle
     *
     * Retrieves Extension Name, given the extension_instance_id
     *
     * @static
     * @param   $extension_instance_id
     *
     * @return  bool|mixed
     * @since   1.0
     */
    public static function getInstanceTitle($extension_instance_id)
    {
        $db = Services::DB();
        $query = $db->getQuery(true);

        $query->select('a.' . $db->nq('title'));
        $query->from($db->nq('#__extension_instances') . ' as a');
        $query->where('a.' . $db->nq('id') .
            ' = ' . (int)$extension_instance_id);
        $db->setQuery($query->__toString());
        $name = $db->loadResult();

        if ($error = $db->getErrorMsg()) {
            MolajoError::raiseWarning(500, $error);
            return false;
        }

        return $name;
    }

    /**
     * formatNameForClass
     *
     * Extension names can include dashes (or underscores). This method
     * prepares the name for use as a component of a classname.
     *
     * @param $extension_name
     *
     * @return string
     * @since  1.0
     */
    public function formatNameForClass($extension_name)
    {
        return ucfirst(str_replace(array('-', '_'), '', $extension_name));
    }

    /**
     * mergeParameters
     *
     * Page Request object that will be populated by this class
     * with overall processing requirements for the page
     *
     * Access via Molajo::Request()->get('property')
     *
     * @param   Registry $parameters
     *
     * @return  null
     * @since  1.0
     */
    public function mergeParameters($merge_in_parameters, $merged_parameters)
    {
        $mergeIn = $merge_in_parameters->toArray();

        while (list($name, $value) = each($mergeIn)) {
            if (isset($this->merged_parameters[$name])) {
            } else if (substr($name, 0, strlen('extension')) == 'extension') {
            } else if (substr($name, 0, strlen('extension')) == 'source') {
            } else if (substr($name, 0, strlen('theme')) == 'theme') {
            } else if (substr($name, 0, strlen('page')) == 'page') {
            } else if (substr($name, 0, strlen('template')) == 'template') {
            } else if (substr($name, 0, strlen('wrap')) == 'wrap') {
            } else if (substr($name, 0, strlen('default')) == 'default') {
            } else if ($name == 'controller'
                || $name == 'task'
                || $name == 'model'
            ) {
            } else {
                $merged_parameters[$name] = $value;
            }
        }

        return $merged_parameters;
    }

    /**
     * getPath
     *
     * Return path for Extension
     *
     * @return bool|string
     * @since 1.0
     */
    static public function getPath($asset_type_id, $name)
    {
        if ($asset_type_id == MOLAJO_ASSET_TYPE_EXTENSION_COMPONENT) {
            return ComponentHelper::getPath($name);
        } else if ($asset_type_id == MOLAJO_ASSET_TYPE_EXTENSION_MODULE) {
            return ModuleHelper::getPath($name);
        } else if ($asset_type_id == MOLAJO_ASSET_TYPE_EXTENSION_THEME) {
            return ThemeHelper::getPath($name);
        } else if ($asset_type_id == MOLAJO_ASSET_TYPE_EXTENSION_PLUGIN) {
            return PluginHelper::getPath($name);
        }
    }

    /**
     * loadLanguage
     *
     * Loads Language Files for extension
     *
     * @return  null
     * @since   1.0
     */
    public static function loadLanguage($path)
    {
        $path .= '/language';

        if (JFolder::exists($path)) {
        } else {
            return false;
        }

        Services::Language()
            ->load($path,
            Services::Language()->get('tag'),
            false,
            false
        );
    }
}
