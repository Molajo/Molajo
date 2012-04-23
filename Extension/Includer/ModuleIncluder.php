<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
namespace Molajo\Extension\Helper;

defined('MOLAJO') or die;

use Molajo\Application\Services;
use Molajo\Extension\Helper\ModuleHelper;

/**
 * Module
 *
 * @package   Molajo
 * @subpackage  Helper
 * @since       1.0
 */
abstract class ModuleHelper
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
            'extension_catalog_type_id',
            CATALOG_TYPE_EXTENSION_MODULE
        );
        $results = parent::_getExtension();

        if ($results === false) {
            if (Services::Registry()->get('Configuration\\debug', 0) == 1) {
                Services::Debug()->set('ModuleIncluder::_getExtension');
                Services::Debug()->set('Module not found: ' . $this->get('extension_instance_name'));
            }
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
     * Extension classes, if existing
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
            EXTENSIONS_MODULES_URL . '/' . $this->get('extension_instance_name'),
            SITE_MEDIA_URL . '/' . $this->get('extension_instance_name'),
            Services::Registry()->get('Configuration\\media_priority_module', 400)
        );
    }
}
