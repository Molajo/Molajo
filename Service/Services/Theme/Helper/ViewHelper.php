<?php
/**
 * Theme Service View Helper
 *
 * @package      Niambie
 * @license      MIT
 * @copyright    2012 Amy Stephen. All rights reserved.
 */
namespace Molajo\Service\Services\Theme\Helper;

use Molajo\Service\Services;

defined('NIAMBIE') or die;

/**
 * The View Helper provides access to Page View, Template View, and Wrap View data.
 *
 * @author       Amy Stephen
 * @license      MIT
 * @copyright    2012 Amy Stephen. All rights reserved.
 * @since        1.0
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
     * @param   string  $value         Numeric Key, Title, or Node (Extension Name)
     *                                 If no value sent in, the default for Catalog Type will be used
     * @param   string  $catalog_type  Numeric or textual key for View Catalog Type
     * @param   string  $registry      Registry to store results in, or 'parameters' default
     *
     * @return  bool
     * @since   1.0
     */

    public function get($value = null, $catalog_type, $registry = null)
    {
        if ($registry === null) {
            $registry = strtolower(PARAMETERS_LITERAL);
        }

        if ($catalog_type == CATALOG_TYPE_PAGE_VIEW
            || $catalog_type == CATALOG_TYPE_TEMPLATE_VIEW
            || $catalog_type == CATALOG_TYPE_WRAP_VIEW
        ) {

            $catalog_type = $this->extensionHelper->getType(0, $catalog_type);
        }

        $catalog_type = ucfirst(strtolower($catalog_type));

        if ($catalog_type == CATALOG_TYPE_PAGE_VIEW_LITERAL
            || $catalog_type == CATALOG_TYPE_TEMPLATE_VIEW_LITERAL
            || $catalog_type == CATALOG_TYPE_WRAP_VIEW_LITERAL
        ) {
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

        Services::Registry()->set($registry, $catalog_type . '_view_id', (int)$id);
        Services::Registry()->set($registry, $catalog_type . '_view_path_node', $node);
        Services::Registry()->set(
            $registry,
            $catalog_type . '_view_path',
            $this->extensionHelper->getPath($catalog_type, $node, $registry)
        );
        Services::Registry()->set(
            $registry,
            $catalog_type . '_view_path_include',
            $this->extensionHelper->getPath($catalog_type, $node, $registry) . '/index.php'
        );
        Services::Registry()->set(
            $registry,
            $catalog_type . '_view_path_url',
            $this->extensionHelper->getPathURL($catalog_type, $node, $registry)
        );
        Services::Registry()->set(
            $registry,
            $catalog_type . '_view_namespace',
            $this->extensionHelper->getNamespace($catalog_type, $node, $registry)
        );

        /** Load View Model before extension query to use $registry value for Theme search */
        $controllerClass = CONTROLLER_CLASS;
        $controller = new $controllerClass();
        $controller->getModelRegistry($catalog_type, $node, 1, $registry);

        $item = $this->extensionHelper->get($id, null, $catalog_type, $node, 1);
        if (count($item) == 0 || $item === false) {
            return false;
        }

        Services::Registry()->set($registry, $catalog_type . '_view_title', $item->title);
        Services::Registry()->set(
            $registry,
            $catalog_type . '_view_translation_of_id',
            (int)$item->translation_of_id
        );
        Services::Registry()->set($registry, $catalog_type . '_view_language', $item->language);
        Services::Registry()->set(
            $registry,
            $catalog_type . '_view_view_group_id',
            $item->catalog_view_group_id
        );
        Services::Registry()->set($registry, $catalog_type . '_view_catalog_id', $item->catalog_id);
        Services::Registry()->set(
            $registry,
            $catalog_type . '_view_catalog_type_id',
            (int)$item->catalog_type_id
        );
        Services::Registry()->set(
            $registry,
            $catalog_type . '_view_catalog_type_title',
            $item->catalog_types_title
        );
        Services::Registry()->set(
            $registry,
            $catalog_type . '_view_model_registry',
            $item->model_registry
        );

        if ($catalog_type == CATALOG_TYPE_PAGE_VIEW_LITERAL) {
            $this->setParameters(CATALOG_TYPE_PAGE_VIEW_LITERAL, $item->model_registry . 'Parameters', $registry);

        } elseif ($catalog_type == CATALOG_TYPE_TEMPLATE_VIEW_LITERAL) {

            $this->setParameters('template', $item->model_registry . 'Parameters', $registry);
            $this->setParameters('wrap', $item->model_registry . 'Parameters', $registry);
            $this->setParameters('cache', $item->model_registry . 'Parameters', $registry);
            $this->setParameters('model', $item->model_registry . 'Parameters', $registry);
            $this->setParameters('criteria', $item->model_registry . 'Parameters', $registry);

        } else {
            $this->setParameters('wrap', $item->model_registry . 'Parameters', $registry);
        }

        Services::Registry()->delete($item->model_registry . 'Parameters', $catalog_type . '_view_id');
        Services::Registry()->copy($item->model_registry . 'Parameters', $registry);

        return true;
    }

    /**
     * Retrieves parameter set (form, item, list, or menuitem) and populates Parameters registry
     *
     * @param   string  $requestTypeNamespace
     * @param   string  $parameterNamespace
     * @param   string  $registry
     *
     * @return  bool
     * @since   1.0
     */
    public function setParameters($requestTypeNamespace, $parameterNamespace, $registry)
    {
        $newParameters = Services::Registry()->get($parameterNamespace, $requestTypeNamespace . '*');
        if (is_array($newParameters) && count($newParameters) > 0) {
            $this->processParameterSet($newParameters, $registry);
        }

        $applicationDefaults = Services::Registry()->get(CONFIGURATION_LITERAL, $requestTypeNamespace . '*');
        if (count($applicationDefaults) > 0) {
            $this->processParameterSet($applicationDefaults, $registry);
        }

        return true;
    }

    /**
     * processParameterSet iterates a new parameter set to determine whether or not it should be applied
     *
     * @param   string  $parameter_set
     * @param   string  $registry
     *
     * @return  void
     * @since   1.0
     */
    protected function processParameterSet($parameter_set, $registry)
    {
        foreach ($parameter_set as $key => $value) {
            $existing = Services::Registry()->get($registry, $key);
            if ($existing === 0 || trim($existing) == '' || $existing == null) {
                if ($value === 0 || trim($value) == '' || $value == null) {
                } else {
                    Services::Registry()->set($registry, $key, $value);
                }
            }
        }
    }
}
