<?php
/**
 * @package     Molajo
 * @subpackage  Service
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Application\Service;

defined('MOLAJO') or die;

/**
 * Install
 *
 * @package     Molajo
 * @subpackage  Install
 * @since       1.0
 */
Class InstallService
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
            self::$instance = new InstallService();
        }
        return self::$instance;
    }

    /**
     * __construct
     *
     * Class constructor.
     *
     * @since  1.0
     */
    public function __construct()
    {
        $this->_initialize();
    }

    public function _initialize()
    {

        if ($element && is_a($element, 'SimpleXMLElement')) {
            $this->type = (string)$element->attributes()->type;
            $this->id = (string)$element->attributes()->id;

            switch ($this->type)
            {
                case 'component':
                    break;

                case 'module':
                case 'theme':
                case 'language':
                    break;

                case 'plugin':
                    break;

                default:
                    break;
            }
            $this->filename = (string)$element;
        }
    }

    /**
     * Custom loadLanguage method
     *
     * @param   string  $path  The path where we find language files
     *
     * @return  void
     *
     * @since   1.0
     */
    public function loadLanguage($path = null)
    {

    }

    /**
     * Custom install method
     *
     * @return  boolean  True on success
     *
     * @since   1.0
     */
    public function install()
    {

    }

    /**
     * Custom update method
     *
     * This is really a shell for the install system
     *
     * @return  boolean  True on success.
     *
     * @since   1.0
     */
    function update()
    {

    }

    /**
     * Custom discover method
     *
     * @return  array  JExtension list of extensions available
     *
     * @since   1.0
     */
    public function discover()
    {

    }

    /**
     * Custom discover_install method
     *
     * @return void
     *
     * @since   1.0
     */
    function discover_install()
    {

    }

    /**
     * Refreshes the extension table cache
     *
     * @return  boolean  Result of operation, true if updated, false on failure.
     *
     * @since   1.0
     */
    public function refreshManifestCache()
    {

    }

    /**
     * Custom uninstall method
     *
     * @param   integer  $id  The id of the module to uninstall
     *
     * @return  boolean  True on success
     *
     * @since   1.0
     */
    public function uninstall($id)
    {

    }
}

