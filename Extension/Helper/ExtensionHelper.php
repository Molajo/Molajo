<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
namespace Molajo\Extension\Helper;

use Molajo\MVC\Model\DisplayModel;
use Molajo\MVC\Model\TableModel;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * Extension
 *
 * @package   Molajo
 * @subpackage  Helper
 * @since       1.0
 */
abstract class ExtensionHelper
{
    /**
     * get
     *
     * Retrieves Extension data from the extension and extension instances
     * Verifies access for user, application and site
     *
     * @param   $catalog_type_id
     * @param   $extension
     *
     * @return  bool|mixed
     * @since   1.0
     */
    public static function get($catalog_type_id = 0, $extension = null)
    {
        $m = new DisplayModel();

        /**
         *  a. Extensions Instances Table
         */
        $m->query->select('a.' . $m->db->qn('id') . ' as extension_instance_id');
        $m->query->select('a.' . $m->db->qn('catalog_type_id'));
        $m->query->select('a.' . $m->db->qn('title'));
        $m->query->select('a.' . $m->db->qn('subtitle'));
        $m->query->select('a.' . $m->db->qn('alias'));
        $m->query->select('a.' . $m->db->qn('content_text'));
        $m->query->select('a.' . $m->db->qn('protected'));
        $m->query->select('a.' . $m->db->qn('featured'));
        $m->query->select('a.' . $m->db->qn('stickied'));
        $m->query->select('a.' . $m->db->qn('status'));
        $m->query->select('a.' . $m->db->qn('custom_fields'));
        $m->query->select('a.' . $m->db->qn('parameters'));
        $m->query->select('a.' . $m->db->qn('metadata'));
        $m->query->select('a.' . $m->db->qn('ordering'));
        $m->query->select('a.' . $m->db->qn('language'));

        $m->query->from($m->db->qn('#__extension_instances') . ' as a');

        $m->query->where('a.' . $m->db->qn('extension_id') . ' > 0 ');

        /** extension specified by id, title or request for list */
        if ((int)$extension > 0) {
            $m->query->where('(a.' . $m->db->qn('id') .
                    ' = ' . (int)$extension . ')'
            );
        } else if ($extension == null) {
        } else {
            $m->query->where('(a.' . $m->db->qn('title') .
                    ' = ' . $m->db->q($extension) . ')'
            );

        }
        if ((int)$catalog_type_id > 0) {
            $m->query->where('a.' . $m->db->qn('catalog_type_id') .
                    ' = ' . (int)$catalog_type_id
            );
        }

        $m->query->where('a.' . $m->db->qn('status') .
                ' > ' . STATUS_UNPUBLISHED
        );
        $m->query->where('(a.start_publishing_datetime = ' .
                $m->db->q($m->nullDate) .
                ' OR a.start_publishing_datetime <= ' . $m->db->q($m->now) . ')'
        );
        $m->query->where('(a.stop_publishing_datetime = ' .
                $m->db->q($m->nullDate) .
                ' OR a.stop_publishing_datetime >= ' . $m->db->q($m->now) . ')'
        );

        /** Catalog Join and View Access Check */
        Services::Access()
            ->setQueryViewAccess(
            $m->query,
            $m->db,
            array('join_to_prefix' => 'a',
                'join_to_primary_key' => 'id',
                'catalog_prefix' => 'b_catalog',
                'select' => true
            )
        );

        /** b_catalog_types. Catalog Types Table  */
        $m->query->select($m->db->qn('b_catalog_types.title') . ' as catalog_type_title');
        $m->query->from($m->db->qn('#__catalog_types') . ' as b_catalog_types');
        $m->query->where('b_catalog.catalog_type_id = b_catalog_types.id');
        $m->query->where('b_catalog_types.' .
                $m->db->qn('component_option') .
                ' = ' . $m->db->q('extensions')
        );

        /**
         *  c. Application Table
         *      Extension Instances must be enabled for the Application
         */
        $m->query->from($m->db->qn('#__application_extension_instances') .
            ' as c');
        $m->query->where('c.' . $m->db->qn('extension_instance_id') .
            ' = a.' . $m->db->qn('id'));
        $m->query->where('c.' . $m->db->qn('application_id') .
            ' = ' . APPLICATION_ID);

        /**
         *  d. Site Table
         *      Extension Instances must be enabled for the Site
         */
        $m->query->from($m->db->qn('#__site_extension_instances') .
            ' as d');
        $m->query->where('d.' . $m->db->qn('extension_instance_id') .
            ' = a.' . $m->db->qn('id'));
        $m->query->where('d.' . $m->db->qn('site_id') .
            ' = ' . SITE_ID);

        /**
         *  Run Query
         */
        $extensions = $m->loadObject();
        return $extensions;
    }

    /**
     * getInstanceID
     *
     * Retrieves Extension ID, given title
     *
     * @static
     *
     * @param  $catalog_type_id
     * @param  $title
     *
     * @return  bool|mixed
     * @since   1.0
     */
    public static function getInstanceID($catalog_type_id, $title)
    {
        $m = new TableModel('ExtensionInstances');

        $m->query->select('a.' . $m->db->qn('id'));
        $m->query->where('a.' . $m->db->qn('title') . ' = ' .
            $m->db->q($title));
        $m->query->where('a.' . $m->db->qn('catalog_type_id') .
            ' = ' . (int)$catalog_type_id);

        return $m->loadResult();
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
        $m = new TableModel('ExtensionInstances');

        $m->query->select('a.' . $m->db->qn('title'));
        $m->query->where('a.' . $m->db->qn('id') .
            ' = ' . (int)$extension_instance_id);

        return $m->loadResult();
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
    public static function formatNameForClass($extension_name)
    {
        return ucfirst(str_replace(array('-', '_'), '', $extension_name));
    }

    /**
     * mergeParameters
     *
     * Page Request object that will be populated by this class
     * with overall processing requirements for the page
     *
     * Access via Services::Registry()->get('Request', 'property')
     *
     * @param   Registry $parameters
     *
     * @return  null
     * @since  1.0
     */
    public static function mergeParameters($merge_in_parameters, $merged_parameters)
    {
        $mergeIn = $merge_in_parameters->toArray();

        while (list($name, $value) = each($mergeIn)) {
            if (isset($merged_parameters[$name])) {
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
     * @return mixed
     * @since 1.0
     */
    static public function getPath($catalog_type_id, $name)
    {
        if ($catalog_type_id == CATALOG_TYPE_EXTENSION_COMPONENT) {
            return ComponentHelper::getPath($name);
        } else if ($catalog_type_id == CATALOG_TYPE_EXTENSION_MODULE) {
            return ModuleHelper::getPath($name);
        } else if ($catalog_type_id == CATALOG_TYPE_EXTENSION_THEME) {
            return ThemeHelper::getPath($name);
        } else if ($catalog_type_id == CATALOG_TYPE_EXTENSION_TRIGGER) {
            return TriggerHelper::getPath($name);
        }
        return false;
    }

    /**
     * loadLanguage
     *
     * Loads Language Files for extension
     *
     * @return  boolean
     * @since   1.0
     */
    public static function loadLanguage($path)
    {
        $path .= '/language';

        if (Services::Filesystem()->folderExists($path)) {
        } else {
            return false;
        }

        Services::Language()
            ->load($path, Services::Language()->get('tag'), false, false);
        return true;
    }
}
