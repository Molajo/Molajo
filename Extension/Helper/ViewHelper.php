<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License version 2 or later; see LICENSE
 */
namespace Molajo\Extension\Helper;

use Molajo\Service\Services;
use Molajo\Extension\Helpers;

defined('MOLAJO') or die;

/**
 * View Helper
 *
 * @package       Molajo
 * @subpackage    Helper
 * @since         1.0
 */
Class ViewHelper
{
    /**
     * Static instance
     *
     * @var    object
     * @since  1.0
     */
    protected static $instance;

    /**
     * getInstance
     *
     * @static
     * @return bool|object
     * @since  1.0
     */
    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new ViewHelper();
        }

        return self::$instance;
    }

    /**
     * Get requested page_view data
     *
     * @param int $id
     *
     * @return boolean
     * @since   1.0
     */
    public function get($id = 0, $type)
    {
        $type = ucfirst(strtolower($type));
        if ($type == 'Page' || $type == 'Template' || $type == 'Wrap') {
        } else {
            return false;
        }

        if ($id == 0) {
            $id = $this->getDefault($type);
            if ((int) $id == 0) {
                return false;
            }
        }

        /** Retrieve Node and verify the view exists */
        $node = Helpers::Extension()->getExtensionNode((int) $id);
        if ($node == false || $node == '') {
            $id = $this->getDefault($type);
            $node = Helpers::Extension()->getExtensionNode((int) $id);
            if ($node == false || $node == '') {
                return false;
            }
        }

        Services::Registry()->set('Parameters', $type . '_view_id', (int) $id);

        Services::Registry()->set('Parameters', $type . '_view_path_node', $node);
        Services::Registry()->set('Parameters', $type . '_view_path', $this->getPath($node, $type));
        Services::Registry()->set('Parameters', $type . '_view_path_include',
            $this->getPath($node, $type) . '/index.php');
        Services::Registry()->set('Parameters', $type . '_view_path_url', $this->getPathURL($node, $type));

        /** Retrieve the query results */
        $item = Helpers::Extension()->get($id, $type, $node);
        if (count($item) == 0 || $item == false) {
            return false;
        }

        Services::Registry()->set('Parameters', $type . '_view_title', $item->title);
        Services::Registry()->set('Parameters', $type . '_view_translation_of_id', (int) $item->translation_of_id);
        Services::Registry()->set('Parameters', $type . '_view_language', $item->language);
        Services::Registry()->set('Parameters', $type . '_view_view_group_id', $item->view_group_id);
        Services::Registry()->set('Parameters', $type . '_view_catalog_id', $item->catalog_id);
        Services::Registry()->set('Parameters', $type . '_view_catalog_type_id', (int) $item->catalog_type_id);
        Services::Registry()->set('Parameters', $type . '_view_catalog_type_title', $item->catalog_types_title);

        Services::Registry()->set('Parameters', $type . '_view_table_registry_name', $item->table_registry_name);

        /** Merge in each custom field namespace  */
        $customFieldTypes = Services::Registry()->get($item->table_registry_name, 'CustomFieldGroups');

        if (count($customFieldTypes) > 0) {
            foreach ($customFieldTypes as $customFieldName) {
                $customFieldName = ucfirst(strtolower($customFieldName));
                Services::Registry()->merge($item->table_registry_name . $customFieldName, $customFieldName);
                Services::Registry()->deleteRegistry($item->table_registry_name . $customFieldName);
            }
        }

        return true;
    }

    /**
     * Get default for View Type
     *
     * @param $type
     * @return mixed
     */
    public function getDefault($type)
    {
        if ($type == 'Page') {
            $catalog_type_id = CATALOG_TYPE_EXTENSION_PAGE_VIEW;
        } elseif ($type == 'Template') {
            $catalog_type_id = CATALOG_TYPE_EXTENSION_TEMPLATE_VIEW;
        } else {
            $catalog_type_id = CATALOG_TYPE_EXTENSION_WRAP_VIEW;
        }

        return Helpers::Extension()->getInstanceID($catalog_type_id, 'Default');
    }

    /**
     * Return path for selected View
     *
     * Note: Expects known path for Theme and Extension
     *
     * @param $node
     * @param $type
     *
     * @return bool|string
     * @since  1.0
     */
    public function getPath($node, $type)
    {
        $type = ucfirst(strtolower($type));
        if ($type == 'Page' || $type == 'Template' || $type == 'Wrap') {
        } else {
            return false;
        }

        $plus = '/View/' . $type . '/' . ucfirst(strtolower($node));

        /** 1. Theme */
        if (file_exists(Services::Registry()->get('Parameters', 'theme_path') . $plus . '/Configuration.xml')) {
            return Services::Registry()->get('Parameters', 'theme_path') . $plus;
        }

        /** 2. Extension */
        if (file_exists(Services::Registry()->get('Parameters', 'extension_path') . $plus . '/Configuration.xml')) {
            return Services::Registry()->get('Parameters', 'extension_path') . $plus;
        }

        /** 3. View */
        if (file_exists(EXTENSIONS_VIEWS . '/' . $type . '/' . ucfirst(strtolower($node)) . '/Configuration.xml')) {
            return EXTENSIONS_VIEWS . '/' . $type . '/' . ucfirst(strtolower($node));
        }

        return false;
    }

    /**
     * getURLPath - Return URL path for selected View
     *
     * @param bool $node
     * @param $type
     *
     * @return bool|string
     * @since  1.0
     */
    public function getPathURL($node = false, $type)
    {
        $type = ucfirst(strtolower($type));
        if ($type == 'Page' || $type == 'Template' || $type == 'Wrap') {
        } else {
            return false;
        }

        $plus = '/View/' . $type . '/' . ucfirst(strtolower($node));

        /** 1. Theme */
        if (file_exists(Services::Registry()->get('Parameters', 'theme_path') . $plus . '/Configuration.xml')) {
            return Services::Registry()->get('Parameters', 'theme_path_url') . $plus;
        }

        /** 2. Extension */
        if (file_exists(Services::Registry()->get('Parameters', 'extension_path') . $plus . '/Configuration.xml')) {
            return Services::Registry()->get('Parameters', 'extension_path_url') . $plus;
        }

        /** 3. View */
        if (file_exists(EXTENSIONS_VIEWS . '/' . $type . '/' . ucfirst(strtolower($node)) . '/Configuration.xml')) {
            return EXTENSIONS_VIEWS_URL . '/' . $type . '/' . ucfirst(strtolower($node));
        }

        return '';
    }
}
