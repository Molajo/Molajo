<?php
/**
 * Theme Service Theme Helper
 *
 * @package      Niambie
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\Service\Services\Theme\Helper;

use Molajo\Service\Services;
use Molajo\Service\Services\Theme\Helper\ExtensionHelper;

defined('NIAMBIE') or die;

/**
 * Theme Helper retrieves values needed to render the selected Theme index.php file, load plugins
 * in the Theme folder and load assets defined by the Theme.
 *
 * @author       Amy Stephen
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 * @since        1.0
 */
Class ThemeHelper
{
    /**
     * Extension Helper
     *
     * @var    object
     * @since  1.0
     */
    protected $extensionHelper;

    /**
     * Class Constructor
     *
     * @return  void
     * @since   1.0
     */
    public function __construct()
    {
        $this->extensionHelper = new ExtensionHelper();

        return;
    }

    /**
     * Get information for rendering the specified Theme index.php file. Calling process sends in the
     * name of the Registry to use when storing results. Defaults to "Parameters" registry.
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
            $theme_id = Services::Registry()->get('Configuration', 'application_default_theme_id');
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
