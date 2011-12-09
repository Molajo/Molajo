<?php
/**
 * @package     Molajo
 * @subpackage  Application
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2012 Individual Molajo Contributors. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Extension object
 *
 * @package     Joomla.Platform
 * @subpackage  Installer
 * @since       11.1
 */
class MolajoInstallerExtension extends JObject
{
    /**
     * Filename of the extension
     *
     * @var    string
     * @since  11.1
     */
    var $filename = '';

    /**
     * Type of the extension
     *
     * @var    string
     * @since  11.1
     */
    var $type = '';

    /**
     * Unique Identifier for the extension
     *
     * @var    string
     * @since  11.1
     */
    var $id = '';

    /**
     * The status of the extension
     *
     * @var    boolean
     * @since  11.1
     */
    var $published = false;

    /**
     * String representation of application. Valid for modules, templates and languages.
     * Set by default to site.
     *
     * @var    string
     * @since  11.1
     */
    var $application = 'site';

    /**
     * The group name of the plugin. Not used for other known extension types (only plugins)
     *
     * @var string
     * @since  11.1
     */
    var $group = '';

    /**
     * An object representation of the manifest file stored metadata
     *
     * @var object
     * @since  11.1
     */
    var $manifest_cache = null;

    /**
     * An object representation of the extension parameters
     *
     * @var    object
     * @since  11.1
     */
    var $parameters = null;

    /**
     * Constructor
     *
     * @param   SimpleXMLElement  $element  A SimpleXMLElement from which to load data from
     *
     * @return  JExtension
     *
     * @since  11.1
     */
    function __construct(SimpleXMLElement $element = null)
    {
        if ($element && is_a($element, 'SimpleXMLElement')) {
            $this->type = (string)$element->attributes()->type;
            $this->id = (string)$element->attributes()->id;

            switch ($this->type)
            {
                case 'component':
                    // By default a component doesn't have anything
                    break;

                case 'module':
                case 'template':
                case 'language':
                    $this->application = (string)$element->attributes()->application;
                    $tmp_application_id = MolajoApplicationHelper::getApplicationInfo($this->application, 1);
                    if ($tmp_application_id == null) {
                        MolajoError::raiseWarning(100, MolajoTextHelper::_('JLIB_INSTALLER_ERROR_EXTENSION_INVALID_CLIENT_IDENTIFIER'));
                    }
                    else
                    {
                        $this->application_id = $tmp_application_id->id;
                    }
                    break;

                case 'plugin':
                    $this->group = (string)$element->attributes()->group;
                    break;

                default:
                    // Catch all
                    // Get and set application and group if we don't recognise the extension
                    if ($application = (string)$element->attributes()->application) {
                        $this->application_id = MolajoApplicationHelper::getApplicationInfo($this->application, 1);
                        $this->application_id = $this->application_id->id;
                    }
                    if ($group = (string)$element->attributes()->group) {
                        $this->group = (string)$element->attributes()->group;
                    }
                    break;
            }
            $this->filename = (string)$element;
        }
    }
}
