<?php
/**
 * Extensions Plugin
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Extensions;

use CommonApi\Event\SystemEventInterface;

/**
 * Extensions Plugin
 *
 * @package     Molajo
 * @license     http://www.opensource.org/licenses/mit-license.html MIT License
 * @since       1.0
 */
final class ExtensionsPlugin extends Extensions implements SystemEventInterface
{
    /**
     * After Start Processing
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onAfterStart()
    {
        return $this;

        if ($this->checkProcessPlugin() === false) {
            return $this;
        }

        $this->setExtensionFolders();

        $this->processExtensionCatalogTypeId();

        $this->processThemeFolders(8000, 'Pageviews', '/Views/Pages/', '\\Views\\Pages\\');
        $this->processThemeFolders(9000, 'Templateviews', '/Views/Templates/', '\\Views\\Templates\\');
        $this->processThemeFolders(10000, 'Wrapviews', '/Views/Wraps/', '\\Views\\Wraps\\');

        return $this;
    }

    /**
     * Should plugin be executed?
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function checkProcessPlugin()
    {
        if ((int)$this->runtime_data->user->administrator === 1) {
            return true;
        }

        return false;
    }

    /**
     * Set Extension Folders
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setExtensionFolders()
    {
        if (isset($this->runtime_data->extension_folders['/Plugins/'])
            && isset($this->runtime_data->extension_folders['/Themes/'])
            && isset($this->runtime_data->extension_folders['/Resources/'])
        ) {
        } else {
            return $this;
        }

        $this->extension_folders        = array();
        $this->extension_folders[5000]  = $this->runtime_data->extension_folders['/Plugins/'];
        $this->extension_folders[7000]  = $this->runtime_data->extension_folders['/Themes/'];
        $this->extension_folders[12000] = $this->runtime_data->extension_folders['/Resources/'];

        return $this;
    }

    /**
     * Process each Extension Catalog Type Id looking for new extensions
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function processExtensionCatalogTypeId()
    {
        foreach ($this->extension_catalog_type_ids as $catalog_type_id => $catalog_type) {
            $this->processExtensionFolders($catalog_type_id, $catalog_type);
        }

        return $this;
    }

    /**
     * Process each extension folder for catalog_type_id
     *
     * @param   integer $catalog_type_id
     * @param   string  $catalog_type
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function processExtensionFolders($catalog_type_id, $catalog_type)
    {
        foreach ($this->extension_folders[$catalog_type_id] as $folder_path) {

            $this->processExtensionFolder(
                $catalog_type_id,
                $catalog_type,
                $folder_path,
                'Molajo\\' . ucfirst(strtolower($catalog_type)) . '\\',
                ''
            );
        }

        return $this;
    }

    /**
     * Process each extension folder for catalog_type_id
     *
     * @param   integer $catalog_type_id
     * @param   string  $catalog_type
     * @param   string  $view_path
     * @param   string  $view_path_ns
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function processThemeFolders($catalog_type_id, $catalog_type, $view_path, $view_path_ns)
    {
        foreach ($this->installed_themes as $theme => $folder_path_array) {

            foreach ($folder_path_array as $folder_path) {

                $namespace = 'Molajo\\Themes\\' . ucfirst(strtolower($theme)) . $view_path_ns;

                $this->processExtensionFolder(
                    $catalog_type_id,
                    $catalog_type,
                    $folder_path . $view_path,
                    $namespace,
                    $theme
                );
            }
        }

        return $this;
    }

    /**
     * Check folders for not-installed extensions
     *
     * @param   integer $catalog_type_id
     * @param   string  $catalog_type
     * @param   string  $folder_path
     * @param   string  $namespace
     * @param   string  $theme
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function processExtensionFolder($catalog_type_id, $catalog_type, $folder_path, $namespace, $theme = '')
    {
        $extensions = array();

        foreach (array_filter(glob($this->runtime_data->base_path . '/' . $folder_path . '*'), 'is_dir') as $path) {
            $extensions[] = basename($path);
        }

        foreach ($extensions as $extension_name) {
            if (substr(strtolower($extension_name), 0, 3) === 'xxx'
                || $extension_name === 'Views'
            ) {
            } else {

                $this->processExtension(
                    $catalog_type_id,
                    $catalog_type,
                    $extension_name,
                    addslashes($namespace . ucfirst(strtolower($extension_name))),
                    $theme
                );

                if ($catalog_type_id === 7000) {
                    $this->setInstalledThemeFolders($extension_name, $folder_path);
                }
            }
        }

        return $this;
    }

    /**
     * Set Installed Themes Folders
     *
     * @param   string $theme
     * @param   string $folder_path
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setInstalledThemeFolders($theme, $folder_path)
    {
        if (isset($this->installed_themes[$theme])) {
            $paths = $this->installed_themes[$theme];
        } else {
            $paths = array();
        }

        $paths[] = $folder_path . $theme;

        $this->installed_themes[$theme] = $paths;

        return $this;
    }
}
