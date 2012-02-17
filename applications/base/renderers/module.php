<?php
/**
 * @package     Molajo
 * @subpackage  Renderer
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Module
 *
 * @package     Molajo
 * @subpackage  Renderer
 * @since       1.0
 */
class MolajoModuleRenderer extends MolajoRenderer
{
    /**
     * _getExtension
     *
     * Retrieve extension information using either the ID or the name
     *
     * @return bool
     * @since 1.0
     */
    protected function _getExtension()
    {
        $this->set(
            'extension_asset_type_id',
            MOLAJO_ASSET_TYPE_EXTENSION_MODULE
        );
        $results = parent::_getExtension();

        if ($results === false) {
            echo 'remove from MolajoModuleRenderer when .
            dev is done. Module not found: '.
                $this->get('extension_instance_name').'<br />';
            return false;
        }

        $this->set(
            'extension_path',
            ModuleHelper::getPath(
                strtolower($this->get('extension_instance_name')))
        );

        $this->set('extension_type', 'module');

        return true;
    }

    /**
     * import
     *
     * lazy load imports of extension folders and files
     * Files and classes do not have to exist if system
     * defaults can be used
     *
     * @since 1.0
     */
    protected function _importClasses()
    {
        $load = new LoadHelper();
        $name = ucfirst($this->get('extension_instance_name'));
        $name = str_replace(array('-', '_'), '', $name);
        $name = 'Molajo' . $name;

        /** Controller */
        if (file_exists($this->get('extension_path') . '/controller.php')) {
            $load->requireClassFile(
                $this->get('extension_path') . '/controller.php',
                $name . 'ModuleController');
        }

        /** Model */
        if (file_exists($this->get('extension_path') . '/model.php')) {
            $load->requireClassFile(
                $this->get('extension_path') . '/model.php',
                $name . 'ModuleModel');
        }
    }

    /**
     * _loadMedia
     *
     * Loads Media Files for Site, Application, User, and Theme
     *
     * @return  boolean  True, if the file has successfully loaded.
     * @since   1.0
     */
    protected function _loadMedia()
    {
        parent::_loadMedia(
            MOLAJO_EXTENSIONS_MODULES_URL . '/' . $this->get('extension_instance_name'),
            SITE_MEDIA_URL . '/' . $this->get('extension_instance_name'),
            Services::Configuration()->get('media_priority_module', 400)
        );
    }
}
