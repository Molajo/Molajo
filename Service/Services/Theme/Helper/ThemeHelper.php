<?php
/**
 * @package    Niambie
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Service\Services\Theme\Helper;

use Molajo\Service\Services;
use Molajo\Service\Services\Theme\Helper\ExtensionHelper;

defined('NIAMBIE') or die;

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
     * @return  void
     * @since   1.0
     */
    public function __construct()
    {
        $this->extensionHelper = new ExtensionHelper();

        return;
    }

    /**
     * Get requested theme data
     *
     * @param   int      $theme_id
     * @param   string   $registry
     *
     * @return  boolean
     * @since   1.0
     */
    public function get($theme_id = 0, $registry = null)
    {
        if ((int)$theme_id == 0) {
            $theme_id = Services::Registry()->get(CONFIGURATION_LITERAL, 'application_default_theme_id');
        }

        if ($registry === null) {
            $registry = strtolower(PARAMETERS_LITERAL);
        }

        Services::Registry()->set($registry, 'theme_id', (int)$theme_id);

        $node = $this->extensionHelper->getExtensionNode((int)$theme_id);

        Services::Registry()->set($registry, 'theme_path_node', $node);

        Services::Registry()->set(
            $registry,
            'theme_path',
            $this->extensionHelper->getPath(CATALOG_TYPE_THEME_LITERAL, $node, $registry)
        );

        Services::Registry()->set(
            $registry,
            'theme_namespace',
            $this->extensionHelper->getNamespace(CATALOG_TYPE_THEME_LITERAL, $node, $registry)
        );

        Services::Registry()->set(
            $registry,
            'theme_path_include',
            $this->extensionHelper->getPath(CATALOG_TYPE_THEME_LITERAL, $node, $registry) . '/index.php'
        );

        Services::Registry()->set(
            $registry,
            'theme_path_url',
            $this->extensionHelper->getPathURL(CATALOG_TYPE_THEME_LITERAL, $node, $registry)
        );

        Services::Registry()->set(
            $registry,
            'theme_favicon',
            $this->extensionHelper->getFavicon($registry)
        );

        $item = $this->extensionHelper->get($theme_id, CATALOG_TYPE_THEME, null, null, 1);
        if (count($item) == 0) {

            if ($theme_id == $this->extensionHelper->getId(CATALOG_TYPE_THEME, SYSTEM_LITERAL)) {
                Services::Error()->set(500, 'System Theme not found');
                throw new \Exception('ThemeIncluder: Not found ' . $theme_id);
            }

            $theme_id = $this->extensionHelper->getId(CATALOG_TYPE_THEME, SYSTEM_LITERAL);
            Services::Registry()->set($registry, 'theme_id', (int)$theme_id);

            $node = $this->extensionHelper->getExtensionNode((int)$theme_id);
            Services::Registry()->set($registry, 'theme_path_node', $node);

            Services::Registry()->set(
                $registry,
                'theme_path',
                $this->extensionHelper->getPath(CATALOG_TYPE_THEME, $node, $registry)
            );
            Services::Registry()->set(
                $registry,
                'theme_namespace',
                $this->extensionHelper->getNamespace(CATALOG_TYPE_THEME, $node, $registry)
            );
            Services::Registry()->set(
                $registry,
                'theme_path_include',
                $this->extensionHelper->getPath(CATALOG_TYPE_THEME, $node, $registry) . '/index.php'
            );
            Services::Registry()->set(
                $registry,
                'theme_path_url',
                $this->extensionHelper->getPathURL(CATALOG_TYPE_THEME, $node, $registry)
            );
            Services::Registry()->set(
                $registry,
                'theme_favicon',
                $this->extensionHelper->getFavicon($registry)
            );

            $item = $this->extensionHelper->get($theme_id, CATALOG_TYPE_THEME_LITERAL, $node, 1);
            if (count($item) == 0) {
                Services::Error()->set(500, 'System Theme not found');
                //throw error
                die();
            }
        }

        Services::Registry()->set($registry, 'theme_title', $item->title);
        Services::Registry()->set($registry, 'theme_translation_of_id', (int)$item->translation_of_id);
        Services::Registry()->set($registry, 'theme_language', $item->language);
        Services::Registry()->set($registry, 'theme_view_group_id', $item->catalog_view_group_id);
        Services::Registry()->set($registry, 'theme_catalog_id', $item->catalog_id);
        Services::Registry()->set($registry, 'theme_catalog_type_id', (int)$item->catalog_view_group_id);
        Services::Registry()->set($registry, 'theme_catalog_type_title', $item->catalog_types_title);
        Services::Registry()->set($registry, 'theme_model_registry', $item->model_registry);

        return true;
    }
}
