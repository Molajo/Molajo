<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Service\Services\Theme\Helper;

use Molajo\Service\Services;
use Molajo\Service\Services\Theme\Helper\ExtensionHelper;

defined('MOLAJO') or die;

/**
 * ThemeHelper
 *
 * @package       Molajo
 * @subpackage    Helper
 * @since         1.0
 */
Class ThemeHelper
{
    /**
     * Helpers
     *
     * @var    object
     * @since  1.0
     */
    protected $extensionHelper;

    /**
     * @return  null
     * @since   1.0
     */
    public function __construct($include_name = null, $include_type = null)
    {
        $this->extensionHelper = new ExtensionHelper();
        return;
    }

    /**
     * Get requested theme data
     *
     * @param   int $theme_id
     *
     * @return  boolean
     * @since   1.0
     */
    public function get($theme_id = 0)
    {
        if ((int)$theme_id == 0) {
            $theme_id = Services::Registry()->get(CONFIGURATION_LITERAL, 'application_default_theme_id');
        }

        Services::Registry()->set(PARAMETERS_LITERAL, 'theme_id', (int)$theme_id);

        $node = $this->extensionHelper->getExtensionNode((int)$theme_id);

        Services::Registry()->set(PARAMETERS_LITERAL, 'theme_path_node', $node);

        Services::Registry()->set(PARAMETERS_LITERAL, 'theme_path',
            $this->extensionHelper->getPath(CATALOG_TYPE_THEME_LITERAL, $node));

        Services::Registry()->set(PARAMETERS_LITERAL, 'theme_namespace',
            $this->extensionHelper->getNamespace(CATALOG_TYPE_THEME_LITERAL, $node));

        Services::Registry()->set(PARAMETERS_LITERAL, 'theme_path_include',
            $this->extensionHelper->getPath(CATALOG_TYPE_THEME_LITERAL, $node) . '/index.php');

        Services::Registry()->set(PARAMETERS_LITERAL, 'theme_path_url',
            $this->extensionHelper->getPathURL(CATALOG_TYPE_THEME_LITERAL, $node));

        Services::Registry()->set(PARAMETERS_LITERAL, 'theme_favicon',
            $this->extensionHelper->getFavicon(CATALOG_TYPE_THEME_LITERAL, $node));

        $item = $this->extensionHelper->get($theme_id, CATALOG_TYPE_THEME, null, null, 1);
        if (count($item) == 0) {

            if ($theme_id == $this->extensionHelper->getId(CATALOG_TYPE_THEME, SYSTEM_LITERAL)) {
                Services::Error()->set(500, 'System Theme not found');
                throw new \Exception('ThemeIncluder: Not found ' . $theme_id);
            }

            $theme_id = $this->extensionHelper->getId(CATALOG_TYPE_THEME, SYSTEM_LITERAL);
            Services::Registry()->set(PARAMETERS_LITERAL, 'theme_id', (int)$theme_id);

            $node = $this->extensionHelper->getExtensionNode((int)$theme_id);
            Services::Registry()->set(PARAMETERS_LITERAL, 'theme_path_node', $node);

            Services::Registry()->set(PARAMETERS_LITERAL, 'theme_path', $this->extensionHelper->getPath(CATALOG_TYPE_THEME, $node));
            Services::Registry()->set(PARAMETERS_LITERAL, 'theme_namespace', $this->extensionHelper->getNamespace(CATALOG_TYPE_THEME, $node));
            Services::Registry()->set(PARAMETERS_LITERAL, 'theme_path_include', $this->extensionHelper->getPath(CATALOG_TYPE_THEME, $node) . '/index.php');
            Services::Registry()->set(PARAMETERS_LITERAL, 'theme_path_url', $this->extensionHelper->getPathURL(CATALOG_TYPE_THEME, $node));
            Services::Registry()->set(PARAMETERS_LITERAL, 'theme_favicon', $this->extensionHelper->getFavicon(CATALOG_TYPE_THEME, $node));

            $item = $this->extensionHelper->get($theme_id, CATALOG_TYPE_THEME_LITERAL, $node, 1);
            if (count($item) == 0) {
                Services::Error()->set(500, 'System Theme not found');
                //throw error
                die();
            }
        }

        Services::Registry()->set(PARAMETERS_LITERAL, 'theme_title', $item->title);
        Services::Registry()->set(PARAMETERS_LITERAL, 'theme_translation_of_id', (int)$item->translation_of_id);
        Services::Registry()->set(PARAMETERS_LITERAL, 'theme_language', $item->language);
        Services::Registry()->set(PARAMETERS_LITERAL, 'theme_view_group_id', $item->catalog_view_group_id);
        Services::Registry()->set(PARAMETERS_LITERAL, 'theme_catalog_id', $item->catalog_id);
        Services::Registry()->set(PARAMETERS_LITERAL, 'theme_catalog_type_id', (int)$item->catalog_view_group_id);
        Services::Registry()->set(PARAMETERS_LITERAL, 'theme_catalog_type_title', $item->catalog_types_title);
        Services::Registry()->set(PARAMETERS_LITERAL, 'theme_model_registry', $item->model_registry);

        $customFieldTypes = Services::Registry()->get($item->model_registry, CUSTOMFIELDGROUPS_LITERAL);

        if (count($customFieldTypes) > 0) {
            foreach ($customFieldTypes as $customFieldName) {
                $customFieldName = ucfirst(strtolower($customFieldName));
                Services::Registry()->merge($item->model_registry . $customFieldName, $customFieldName);
                Services::Registry()->deleteRegistry($item->model_registry . $customFieldName);
            }
        }

        return true;
    }
}
