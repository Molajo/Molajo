<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Service\Services\Theme\Helper;

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
     * Helpers
     *
     * @var    object
     * @since  1.0
     */
    protected $extensionHelper;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->extensionHelper = new ExtensionHelper();
    }

    /**
     * Retrieve View
     *
     * @param   $value         Numeric Key, Title, or Node (Extension Name)
     *                         If no value sent in, the default for Catalog Type will be used
     * @param   $catalog_type  Numeric or textual key for View Catalog Type
     *
     * @return  bool
     * @since   1.0
     */
    public function get($value = null, $catalog_type)
    {
        if ($catalog_type == CATALOG_TYPE_PAGE_VIEW
            || $catalog_type == CATALOG_TYPE_TEMPLATE_VIEW
            || $catalog_type == CATALOG_TYPE_WRAP_VIEW) {
            $catalog_type = $this->extensionHelper->getType(0, $catalog_type);
        }

        $catalog_type = ucfirst(strtolower($catalog_type));

        if ($catalog_type == CATALOG_TYPE_PAGE_VIEW_LITERAL
            || $catalog_type == CATALOG_TYPE_TEMPLATE_VIEW_LITERAL
            || $catalog_type == CATALOG_TYPE_WRAP_VIEW_LITERAL) {
        } else {
            throw new \RuntimeException('ViewHelper: Catalog Type for View is Invalid: ' . $catalog_type);
        }

        if (is_numeric($value)) {
            $id = $value;
        } else {
            $id = $this->getId($value, $catalog_type);
        }

        if ($id == 0) {
            $id = $this->getDefault($catalog_type);
            if ((int)$id == 0) {
                return false;
            }
        }

        $node = $this->extensionHelper->getExtensionNode((int)$id);

        if ($node === false || $node == '') {
            $id = $this->getDefault($catalog_type);
            $node = $this->extensionHelper->getExtensionNode((int)$id);
            if ($node === false || $node == '') {
                return false;
            }
        }

        Services::Registry()->set(PARAMETERS_LITERAL, $catalog_type . '_view_id', (int)$id);
        Services::Registry()->set(PARAMETERS_LITERAL, $catalog_type . '_view_path_node', $node);
        Services::Registry()->set(PARAMETERS_LITERAL, $catalog_type . '_view_path',
            $this->extensionHelper->getPath($node, $catalog_type));
        Services::Registry()->set(
            PARAMETERS_LITERAL,
            $catalog_type . '_view_path_include',
            $this->getPath($node, $catalog_type) . '/index.php'
        );
        Services::Registry()->set(PARAMETERS_LITERAL, $catalog_type . '_view_path_url',
            $this->extensionHelper->getPathURL($node, $catalog_type));
        Services::Registry()->set(PARAMETERS_LITERAL, $catalog_type . '_view_namespace',
            $this->extensionHelper->getNamespace($node, $catalog_type));

        $item = $this->extensionHelper->get($id, $catalog_type, $node, 1);
        if (count($item) == 0 || $item === false) {
            return false;
        }

        Services::Registry()->set(PARAMETERS_LITERAL, $catalog_type . '_view_title', $item->title);
        Services::Registry()->set(PARAMETERS_LITERAL, $catalog_type . '_view_translation_of_id',
            (int)$item->translation_of_id);
        Services::Registry()->set(PARAMETERS_LITERAL, $catalog_type . '_view_language', $item->language);
        Services::Registry()->set(PARAMETERS_LITERAL, $catalog_type . '_view_view_group_id',
            $item->catalog_view_group_id);
        Services::Registry()->set(PARAMETERS_LITERAL, $catalog_type . '_view_catalog_id', $item->catalog_id);
        Services::Registry()->set(PARAMETERS_LITERAL, $catalog_type . '_view_catalog_type_id',
            (int)$item->catalog_type_id);
        Services::Registry()->set(PARAMETERS_LITERAL, $catalog_type . '_view_catalog_type_title',
            $item->catalog_types_title);
        Services::Registry()->set(PARAMETERS_LITERAL, $catalog_type . '_view_model_registry',
            $item->model_registry);

        if ($catalog_type == CATALOG_TYPE_PAGE_VIEW_LITERAL) {
            $this->setParameters(CATALOG_TYPE_PAGE_VIEW_LITERAL, $item->model_registry . PARAMETERS_LITERAL);

        } elseif ($catalog_type == CATALOG_TYPE_TEMPLATE_VIEW_LITERAL) {

            $this->setParameters('template', $item->model_registry . PARAMETERS_LITERAL);
            $this->setParameters('wrap', $item->model_registry . PARAMETERS_LITERAL);
            $this->setParameters('cache', $item->model_registry . PARAMETERS_LITERAL);
            $this->setParameters('model', $item->model_registry . PARAMETERS_LITERAL);
            $this->setParameters('criteria', $item->model_registry . PARAMETERS_LITERAL);

        } else {
            $this->setParameters('wrap', $item->model_registry . PARAMETERS_LITERAL);
        }

        Services::Registry()->delete($item->model_registry . PARAMETERS_LITERAL, $catalog_type . '_view_id');
        Services::Registry()->copy($item->model_registry . PARAMETERS_LITERAL, PARAMETERS_LITERAL);

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

        $applicationDefaults = Services::Registry()->get(CONFIGURATION_LITERAL, $requestTypeNamespace . '*');
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
            $existing = Services::Registry()->get('parameters', $key);
            if ($existing === 0 || trim($existing) == '' || $existing == null) {
                if ($value === 0 || trim($value) == '' || $value == null) {
                } else {
                    Services::Registry()->set(PARAMETERS_LITERAL, $key, $value);
                }
            }
        }
    }
}
