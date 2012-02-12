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
    public static function get($asset_type_id, $extension = null)
    {
        $db = Services::DB();
        $query = $db->getQuery(true);
        $now = Services::Date()->getDate()->toSql();
        $nullDate = $db->getNullDate();

        /**
         *  a. Extensions Instances Table
         */
        $query->select('a.' . $db->namequote('id') . ' as extension_instance_id');
        $query->select('a.' . $db->namequote('title'));
        $query->select('a.' . $db->namequote('subtitle'));
        $query->select('a.' . $db->namequote('alias'));
        $query->select('a.' . $db->namequote('content_text'));
        $query->select('a.' . $db->namequote('protected'));
        $query->select('a.' . $db->namequote('featured'));
        $query->select('a.' . $db->namequote('stickied'));
        $query->select('a.' . $db->namequote('status'));
        $query->select('a.' . $db->namequote('custom_fields'));
        $query->select('a.' . $db->namequote('parameters'));
        $query->select('a.' . $db->namequote('metadata'));
        $query->select('a.' . $db->namequote('ordering'));
        $query->select('a.' . $db->namequote('language'));

        $query->from($db->namequote('#__extension_instances') . ' as a');
        $query->where('a.' . $db->namequote('extension_id') . ' > 0 ');

        /** extension specified by id, title or request for list */
        if ((int)$extension > 0) {
            $query->where('(a.' . $db->namequote('id') .
                ' = ' . (int)$extension . ')'
            );
        } else if ($extension == null) {
        } else {
            $query->where('(a.' . $db->namequote('title') .
                ' = ' . $db->quote($extension) . ')'
            );
        }

        $query->where('a.' . $db->namequote('asset_type_id') .
            ' = ' . (int)$asset_type_id
        );

        $query->where('a.' . $db->namequote('status') .
            ' = ' . MOLAJO_STATUS_PUBLISHED
        );
        $query->where('(a.start_publishing_datetime = ' .
            $db->quote($nullDate) .
            ' OR a.start_publishing_datetime <= ' . $db->quote($now) . ')'
        );
        $query->where('(a.stop_publishing_datetime = ' .
            $db->quote($nullDate) .
            ' OR a.stop_publishing_datetime >= ' . $db->quote($now) . ')'
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
        $query->from($db->namequote('#__asset_types') .
            ' as b_asset_types');
        $query->where('b_assets.asset_type_id = b_asset_types.id');
        $query->where('b_asset_types.' .
            $db->namequote('component_option') .
            ' = ' . $db->quote('extensions')
        );

        /**
         *  c. Application Table
         *      Extension Instances must be enabled for the Application
         */
        $query->from($db->namequote('#__application_extension_instances') .
            ' as c');
        $query->where('c.' . $db->namequote('extension_instance_id') .
            ' = a.' . $db->namequote('id'));
        $query->where('c.' . $db->namequote('application_id') .
            ' = ' . MOLAJO_APPLICATION_ID);

        /**
         *  d. Site Table
         *      Extension Instances must be enabled for the Site
         */
        $query->from($db->namequote('#__site_extension_instances') .
            ' as d');
        $query->where('d.' . $db->namequote('extension_instance_id') .
            ' = a.' . $db->namequote('id'));
        $query->where('d.' . $db->namequote('site_id') .
            ' = ' . MOLAJO_SITE_ID);

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
        $now = Services::Date()->getDate()->toSql();
        $nullDate = $db->getNullDate();

        $query->select('a.' . $db->namequote('id'));
        $query->from($db->namequote('#__extension_instances') . ' as a');
        $query->where('a.' . $db->namequote('title') . ' = ' .
            $db->quote($title));
        $query->where('a.' . $db->namequote('asset_type_id') .
            ' = ' . (int)$asset_type_id);
        $query->where('a.' . $db->namequote('status') .
            ' = ' . MOLAJO_STATUS_PUBLISHED);
        $query->where('(a.start_publishing_datetime = ' .
            $db->quote($nullDate) . ' OR a.start_publishing_datetime <= ' .
            $db->quote($now) . ')');
        $query->where('(a.stop_publishing_datetime = ' .
            $db->quote($nullDate) . ' OR a.stop_publishing_datetime >= ' .
            $db->quote($now) . ')');

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
        $now = Services::Date()->getDate()->toSql();
        $nullDate = $db->getNullDate();

        $query->select('a.' . $db->namequote('title'));
        $query->from($db->namequote('#__extension_instances') . ' as a');
        $query->where('a.' . $db->namequote('id') .
            ' = ' . (int)$extension_instance_id);
        $query->where('a.' . $db->namequote('status') .
            ' = ' . MOLAJO_STATUS_PUBLISHED);
        $query->where('(a.start_publishing_datetime = ' .
            $db->quote($nullDate) . ' OR a.start_publishing_datetime <= ' .
            $db->quote($now) . ')');
        $query->where('(a.stop_publishing_datetime = ' .
            $db->quote($nullDate) . ' OR a.stop_publishing_datetime >= ' .
            $db->quote($now) . ')');

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

        $db->setQuery($query->__toString());
        $name = $db->loadResult();

        if ($error = $db->getErrorMsg()) {
            MolajoError::raiseWarning(500, $error);
            return false;
        }

        return $name;
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
            ->load ($path,
                    Services::Language()->get('tag'),
                    false,
                    false
                    );
    }

    /**
     * getExtensionRequestObject
     *
     * Retrieve Component information using either the ID or the Name
     *
     * @return bool
     * @since 1.0
     */
    static public function getExtensionRequestObject($request)
    {
        if ((int)$request->get('extension_instance_id') > 0) {
            $extension = (int)$request->get('extension_instance_id');
            $request->set(
                'extension_instance_name',
                ExtensionHelper::getInstanceTitle(
                    $request->get('extension_instance_id')
                )
            );
        } else {
            $request->set('extension_instance_id',
                ExtensionHelper::getInstanceID(
                    $request->get('extension_asset_type_id'),
                    $request->get('extension_instance_name')
                )
            );
        }

        $rows = ExtensionHelper::get(
            (int)$request->get('extension_asset_type_id'),
            (int)$request->get('extension_instance_id')
        );

        if (count($rows) == 0) {
            return false;
        }

        foreach ($rows as $row) {
        }

        $request->set('extension_instance_name', $row->title);
        $request->set('extension_asset_id', $row->asset_id);
        $request->set('extension_view_group_id', $row->view_group_id);

        $custom_fields = new Registry;
        $custom_fields->loadString($row->custom_fields);
        $request->set('category_custom_fields', $custom_fields);

        $metadata = new Registry;
        $metadata->loadString($row->metadata);
        $request->set('category_metadata', $metadata);

        $parameters = new Registry;
        $parameters->loadString($row->parameters);
        $request->set('extension_parameters', $parameters);

        /** mvc */
        if ($request->get('mvc_controller', '') == '') {
            $request->set('mvc_controller',
                $parameters->def('controller', '')
            );
        }
        if ($request->get('mvc_task', '') == '') {
            $request->set('mvc_task',
                $parameters->def('task', 'display')
            );
        }
        if ($request->get('mvc_model', '') == '') {
            $request->set('mvc_model',
                $parameters->def('model', '')
            );
        }
        if ((int)$request->get('mvc_id', 0) == 0) {
            $request->set('mvc_id',
                $parameters->def('id', 0)
            );
        }
        if ((int)$request->get('mvc_category_id', 0) == 0) {
            $request->set('mvc_category_id',
                $parameters->def('category_id', 0)
            );
        }
        if ((int)$request->get('mvc_suppress_no_results', 0) == 0) {
            $request->set('mvc_suppress_no_results',
                $parameters->def('suppress_no_results', 0)
            );
        }

        $request->set('extension_event_type',
            $parameters->def(
                'plugin_type',
                array('content')
            )
        );

        if ($request->get('request_suppress_no_results', '') == '') {
            $request->set('request_suppress_no_results',
                $parameters->def('suppress_no_results')
            );
        }
        return $request;
    }
}
