<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
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
     * Get requested page_view data
     *
     * @param   int $id
     *
     * @return  boolean
     * @since   1.0
     */
    public function get($id = 0, $type)
    {
        if ($type == CATALOG_TYPE_PAGE_VIEW_LITERAL
            || $type == CATALOG_TYPE_TEMPLATE_VIEW_LITERAL
            || $type == CATALOG_TYPE_WRAP_VIEW_LITERAL
        ) {
        } else {
            return false;
        }

        if ($id == 0) {
            $id = $this->getDefault($type);
            if ((int)$id == 0) {
                return false;
            }
        }

        $node = Helpers::Extension()->getExtensionNode((int)$id);

        if ($node === false || $node == '') {
            $id = $this->getDefault($type);
            $node = Helpers::Extension()->getExtensionNode((int)$id);
            if ($node === false || $node == '') {
                return false;
            }
        }

        Services::Registry()->set('Parameters', $type . '_view_id', (int)$id);
        Services::Registry()->set('Parameters', $type . '_view_path_node', $node);
        Services::Registry()->set('Parameters', $type . '_view_path', $this->getPath($node, $type));
        Services::Registry()->set(
            'Parameters',
            $type . '_view_path_include',
            $this->getPath($node, $type) . '/index.php'
        );
        Services::Registry()->set('Parameters', $type . '_view_path_url', $this->getPathURL($node, $type));
        Services::Registry()->set('Parameters', $type . '_view_namespace', $this->getNamespace($node, $type));

        $item = Helpers::Extension()->get($id, $type, $node, 1);
        if (count($item) == 0 || $item === false) {
            return false;
        }

        Services::Registry()->set('Parameters', $type . '_view_title', $item->title);
        Services::Registry()->set('Parameters', $type . '_view_translation_of_id', (int)$item->translation_of_id);
        Services::Registry()->set('Parameters', $type . '_view_language', $item->language);
        Services::Registry()->set('Parameters', $type . '_view_view_group_id', $item->catalog_view_group_id);
        Services::Registry()->set('Parameters', $type . '_view_catalog_id', $item->catalog_id);
        Services::Registry()->set('Parameters', $type . '_view_catalog_type_id', (int)$item->catalog_type_id);
        Services::Registry()->set('Parameters', $type . '_view_catalog_type_title', $item->catalog_types_title);
        Services::Registry()->set('Parameters', $type . '_view_model_registry', $item->model_registry);

        if ($type == CATALOG_TYPE_PAGE_VIEW_LITERAL) {
            $this->setParameters(CATALOG_TYPE_PAGE_VIEW_LITERAL, $item->model_registry . 'Parameters');

        } elseif ($type == CATALOG_TYPE_TEMPLATE_VIEW_LITERAL) {

            $this->setParameters('template', $item->model_registry . 'Parameters');
            $this->setParameters('wrap', $item->model_registry . 'Parameters');
            $this->setParameters('cache', $item->model_registry . 'Parameters');
            $this->setParameters('model', $item->model_registry . 'Parameters');
            $this->setParameters('criteria', $item->model_registry . 'Parameters');

        } else {
            $this->setParameters('wrap', $item->model_registry . 'Parameters');
        }

        Services::Registry()->delete($item->model_registry . 'Parameters', $type . '_view_id');
        Services::Registry()->copy($item->model_registry . 'Parameters', 'Parameters');

        return true;
    }

    /**
     * Retrieves parameter set (form, item, list, or menuitem) and populates Parameters registry
     *
     * @param   $requestTypeNamespace
     * @param   $parameterNamespace
     *
     * @return  bool
     * @since   1.0
     */
    public function setParameters($requestTypeNamespace, $parameterNamespace)
    {
        $newParameters = Services::Registry()->get($parameterNamespace, $requestTypeNamespace . '*');
        if (is_array($newParameters) && count($newParameters) > 0) {
            $this->processParameterSet($newParameters);
        }

        $applicationDefaults = Services::Registry()->get('Configuration', $requestTypeNamespace . '*');
        if (count($applicationDefaults) > 0) {
            $this->processParameterSet($applicationDefaults);
        }

        return true;
    }

    /**
     * processParameterSet iterates a new parameter set to determine whether or not it should be applied
     *
     * @param   $parameterSet
     *
     * @return  $bool
     * @since   1.0
     */
    protected function processParameterSet($parameterSet)
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
     * @param   $parameterSet
     * @param   $requestTypeNamespace
     *
     * @return  $bool
     * @since   1.0
     */
    public function getDefault($type)
    {
        if ($type == CATALOG_TYPE_PAGE_VIEW_LITERAL) {
            return Helpers::Extension()->getInstanceID(CATALOG_TYPE_PAGE_VIEW, 'Default');

        } elseif ($type == CATALOG_TYPE_TEMPLATE_VIEW_LITERAL) {
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
     * @param   $node
     * @param   $type
     *
     * @return  bool|string
     * @since   1.0
     */
    public function getPath($node, $type)
    {
        $type = ucfirst(strtolower($type));
        if ($type == CATALOG_TYPE_PAGE_VIEW_LITERAL
            || $type == CATALOG_TYPE_TEMPLATE_VIEW_LITERAL
            || $type == CATALOG_TYPE_WRAP_VIEW_LITERAL
        ) {
        } else {
            return false;
        }

        $plus = '/View/' . $type . '/' . ucfirst(strtolower($node));

        if (file_exists(Services::Registry()->get('Parameters', 'theme_path') . $plus . '/Configuration.xml')) {
            return Services::Registry()->get('Parameters', 'theme_path') . $plus;
        }

        if (file_exists(Services::Registry()->get('Parameters', 'extension_path') . $plus . '/Configuration.xml')) {
            return Services::Registry()->get('Parameters', 'extension_path') . $plus;
        }

        if (file_exists(EXTENSIONS_VIEWS . '/' . $type . '/' . ucfirst(strtolower($node)) . '/Configuration.xml')) {
            return EXTENSIONS_VIEWS . '/' . $type . '/' . ucfirst(strtolower($node));
        }

        if (file_exists(CORE_VIEWS . '/' . $type . '/' . ucfirst(strtolower($node)) . '/Configuration.xml')) {
            return CORE_VIEWS . '/' . $type . '/' . ucfirst(strtolower($node));
        }

        return false;
    }

    /**
     * Return URL path for selected View
     *
     * @param   bool $node
     * @param   $type
     *
     * @return  bool|string
     * @since   1.0
     */
    public function getPathURL($node = false, $type)
    {
        $type = ucfirst(strtolower($type));
        if ($type == CATALOG_TYPE_PAGE_VIEW_LITERAL
            || $type == CATALOG_TYPE_TEMPLATE_VIEW_LITERAL
            || $type == CATALOG_TYPE_WRAP_VIEW_LITERAL
        ) {
        } else {
            return false;
        }

        $plus = '/View/' . $type . '/' . ucfirst(strtolower($node));

        if (file_exists(Services::Registry()->get('Parameters', 'theme_path') . $plus . '/Configuration.xml')) {
            return Services::Registry()->get('Parameters', 'theme_path_url') . $plus;
        }

        if (file_exists(Services::Registry()->get('Parameters', 'extension_path') . $plus . '/Configuration.xml')) {
            return Services::Registry()->get('Parameters', 'extension_path_url') . $plus;
        }

        if (file_exists(EXTENSIONS_VIEWS . '/' . $type . '/' . ucfirst(strtolower($node)) . '/Configuration.xml')) {
            return EXTENSIONS_VIEWS_URL . '/' . $type . '/' . ucfirst(strtolower($node));
        }

        if (file_exists(CORE_VIEWS . '/' . $type . '/' . ucfirst(strtolower($node)) . '/Configuration.xml')) {
            return CORE_VIEWS_URL . '/' . $type . '/' . ucfirst(strtolower($node));
        }

        return false;
    }

    /**
     * Return Namespace for selected View
     *
     * @param   bool $node
     * @param   $type
     *
     * @return  bool|string
     * @since   1.0
     */
    public function getNamespace($node = false, $type)
    {
        $type = ucfirst(strtolower($type));
        if ($type == CATALOG_TYPE_PAGE_VIEW_LITERAL
            || $type == CATALOG_TYPE_TEMPLATE_VIEW_LITERAL
            || $type == CATALOG_TYPE_WRAP_VIEW_LITERAL
        ) {
        } else {
            return false;
        }

        $plus = 'View\\' . $type . '\\' . ucfirst(strtolower($node));

        if (file_exists(Services::Registry()->get('Parameters', 'theme_path') . $plus . '/Configuration.xml')) {
            return 'Extension\\Theme\\' . Services::Registry()->get('Parameters', 'theme_path_node') . '\\' . $plus;
        }

        if (file_exists(Services::Registry()->get('Parameters', 'extension_path') . $plus . '/Configuration.xml')) {
            return 'Extension\\Resource\\' . Services::Registry()->get('Parameters', 'extension_title') . '\\' . $plus;
        }

        if (file_exists(EXTENSIONS_VIEWS . '/' . $type . '/' . ucfirst(strtolower($node)) . '/Configuration.xml')) {
            return 'Extension\\' . $plus;
        }

        if (file_exists(CORE_VIEWS . '/' . $type . '/' . ucfirst(strtolower($node)) . '/Configuration.xml')) {
            return 'Molajo\\MVC\\' . $plus;
        }

        return false;
    }
}
