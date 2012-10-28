<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license   GNU General Public License version 2 or later; see LICENSE
 */
namespace Molajo\Helper;

use Molajo\Service\Services;
use Molajo\Helpers;

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

        if ($node === false || $node == '') {
            $id = $this->getDefault($type);
            $node = Helpers::Extension()->getExtensionNode((int) $id);
            if ($node === false || $node == '') {
                return false;
            }
        }

        Services::Registry()->set('Parameters', $type . '_view_id', (int) $id);
        Services::Registry()->set('Parameters', $type . '_view_path_node', $node);
        Services::Registry()->set('Parameters', $type . '_view_path', $this->getPath($node, $type));
        Services::Registry()->set('Parameters', $type . '_view_path_include',
            $this->getPath($node, $type) . '/index.php');
        Services::Registry()->set('Parameters', $type . '_view_path_url', $this->getPathURL($node, $type));
        Services::Registry()->set('Parameters', $type . '_view_namespace', $this->getNamespace($node, $type));

        /** Retrieve the query results */
        $item = Helpers::Extension()->get($id, $type, $node, 1);

        if (count($item) == 0 || $item === false) {
            return false;
        }

        Services::Registry()->set('Parameters', $type . '_view_title', $item->title);
        Services::Registry()->set('Parameters', $type . '_view_translation_of_id', (int) $item->translation_of_id);
        Services::Registry()->set('Parameters', $type . '_view_language', $item->language);
        Services::Registry()->set('Parameters', $type . '_view_view_group_id', $item->catalog_view_group_id);
        Services::Registry()->set('Parameters', $type . '_view_catalog_id', $item->catalog_id);
        Services::Registry()->set('Parameters', $type . '_view_catalog_type_id', (int) $item->catalog_type_id);
        Services::Registry()->set('Parameters', $type . '_view_catalog_type_title', $item->catalog_types_title);

        Services::Registry()->set('Parameters', $type . '_view_table_registry_name', $item->table_registry_name);

        if ($type == 'Page') {
            $this->setParameters('page', $item->table_registry_name . 'Parameters');

        } elseif ($type == 'Template') {

            $this->setParameters('template', $item->table_registry_name . 'Parameters');
            $this->setParameters('wrap', $item->table_registry_name . 'Parameters');
            $this->setParameters('cache', $item->table_registry_name . 'Parameters');
            $this->setParameters('model', $item->table_registry_name . 'Parameters');
            $this->setParameters('criteria', $item->table_registry_name . 'Parameters');

        } else {
            $this->setParameters('wrap', $item->table_registry_name . 'Parameters');
        }

        /** Copy Parameters (but do not overlay the ID value) */
		Services::Registry()->delete($item->table_registry_name . 'Parameters', $type . '_view_id');
        Services::Registry()->copy($item->table_registry_name . 'Parameters', 'Parameters');

        return true;
    }

    /**
     * Retrieves parameter set (form, item, list, or menuitem) and populates Parameters registry
     *
     * @param   $requestTypeNamespace
     * @param   $parameterNamespace
     *
     * @return bool
     * @since   1.0
     */
    public function setParameters($requestTypeNamespace, $parameterNamespace)
    {
        /** 1. Parameters from Query */
        $newParameters = Services::Registry()->get($parameterNamespace, $requestTypeNamespace . '*');
        if (is_array($newParameters) && count($newParameters) > 0) {
            $this->processParameterSet($newParameters, $requestTypeNamespace);
        }

        /** 2. Application defaults */
        $applicationDefaults = Services::Registry()->get('Configuration', $requestTypeNamespace . '*');
        if (count($applicationDefaults) > 0) {
            $this->processParameterSet($applicationDefaults, $requestTypeNamespace);
        }

        return true;
    }

    /**
     * processParameterSet iterates a new parameter set to determine whether or not it should be applied
     *
     * @param $parameterSet
     * @param $requestTypeNamespace
     */
    protected function processParameterSet($parameterSet, $requestTypeNamespace)
    {
        foreach ($parameterSet as $key => $value) {
            $existing = Services::Registry()->get('Parameters', $key);
            if ($existing === 0 || trim($existing) == '' || $existing == null) {
                if ($value === 0 || trim($value) == '' || $value == null) {
                } else {
                    Services::Registry()->set('Parameters', $key, $value);
                }
            }
        }
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
            return Helpers::Extension()->getInstanceID(CATALOG_TYPE_PAGE_VIEW, 'Default');

        } elseif ($type == 'Template') {
            return Helpers::Extension()->getInstanceID(CATALOG_TYPE_TEMPLATE_VIEW, 'Default');

        } else {
            return Helpers::Extension()->getInstanceID(CATALOG_TYPE_WRAP_VIEW, 'None');
        }
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

        /** 4. Core */
        if (file_exists(CORE_VIEWS . '/' . $type . '/' . ucfirst(strtolower($node)) . '/Configuration.xml')) {
            return CORE_VIEWS . '/' . $type . '/' . ucfirst(strtolower($node));
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

        /** 4. View */
        if (file_exists(CORE_VIEWS . '/' . $type . '/' . ucfirst(strtolower($node)) . '/Configuration.xml')) {
            return CORE_VIEWS_URL . '/' . $type . '/' . ucfirst(strtolower($node));
        }

        return '';
    }

    /**
     * getNamespace - Return Namespace for selected View
     *
     * @param bool $node
     * @param $type
     *
     * @return bool|string
     * @since  1.0
     */
    public function getNamespace($node = false, $type)
    {
        $type = ucfirst(strtolower($type));
        if ($type == 'Page' || $type == 'Template' || $type == 'Wrap') {
        } else {
            return false;
        }

        $plus = 'View\\' . $type . '\\' . ucfirst(strtolower($node));

        /** 1. Theme */
        if (file_exists(Services::Registry()->get('Parameters', 'theme_path') . $plus . '/Configuration.xml')) {
            return 'Extension\\Theme\\' . Services::Registry()->get('Parameters', 'theme_path_node') . '\\' . $plus;
        }

        /** 2. Resource */
        if (file_exists(Services::Registry()->get('Parameters', 'extension_path') . $plus . '/Configuration.xml')) {
            return 'Extension\\Resource\\' . Services::Registry()->get('Parameters', 'extension_title') . '\\' . $plus;
        }

        /** 3. Extension View */
        if (file_exists(EXTENSIONS_VIEWS . '/' . $type . '/' . ucfirst(strtolower($node)) . '/Configuration.xml')) {
            return 'Extension\\' . $plus;
        }

        /** 4. Platform View */
        if (file_exists(CORE_VIEWS . '/' . $type . '/' . ucfirst(strtolower($node)) . '/Configuration.xml')) {
            return 'Molajo\\MVC\\' . $plus;
        }

        return '';
    }
}
