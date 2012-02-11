<?php
/**
 * @package     Molajo
 * @subpackage  Application
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2012 Individual Molajo Contributors. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Joomla! Package Manifest File
 *
 * @package     Joomla.Platform
 * @subpackage  Installer
 * @since       11.1
 */
class MolajoInstallerPackagemanifest extends JObject
{
    /**
     * @var string name Name of the package
     */
    var $name = '';

    /**
     * @var string packagename Unique name of the package
     */
    var $packagename = '';

    /**
     * @var string url Website for the package
     */
    var $url = '';

    /**
     * @var string description Description for the package
     */
    var $description = '';

    /**
     * @var string packager Packager of the package
     */
    var $packager = '';

    /**
     * @var string packagerurl Packager's URL of the package
     */
    var $packagerurl = '';

    /**
     * @var string update Update site for the package
     */
    var $update = '';

    /**
     * @var string version Version of the package
     */
    var $version = '';

    /**
     *
     * @var JExtension[] filelist List of files in this package
     */
    var $filelist = array();

    /**
     * @var string manifest_file Path to the manifest file
     */
    var $manifest_file = '';

    /**
     * Constructor
     *
     * @param   string  $xmlpath  Path to XML manifest file.
     *
     * @return  object  JPackageManifest
     *
     * @since
     */
    function __construct($xmlpath = '')
    {
        if (strlen($xmlpath)) {
            $this->loadManifestFromXML($xmlpath);
        }
    }

    /**
     * Load a manifest from an XML file
     *
     * @param   string  $xmlfile  Path to XML manifest file
     *
     * @return  boolean    Result of load
     *
     * @since   1.0
     */
    function loadManifestFromXML($xmlfile)
    {
        $this->manifest_file = JFile::stripExt(basename($xmlfile));

        $xml = simplexml_load_file($xmlfile);

        if (!$xml) {
            $this->_errors[] = TextService::sprintf('JLIB_INSTALLER_ERROR_LOAD_XML', $xmlfile);

            return false;
        }
        else
        {
            $this->name = (string)$xml->name;
            $this->packagename = (string)$xml->packagename;
            $this->update = (string)$xml->update;
            $this->authorurl = (string)$xml->author_url;
            $this->author = (string)$xml->author;
            $this->authoremail = (string)$xml->author_email;
            $this->description = (string)$xml->description;
            $this->packager = (string)$xml->packager;
            $this->packagerurl = (string)$xml->packagerurl;
            $this->version = (string)$xml->version;

            if (isset($xml->files->file) && count($xml->files->file)) {
                foreach ($xml->files->file as $file)
                {
                    // NOTE: JExtension doesn't expect a string.
                    // DO NOT CAST $file
                    $this->filelist[] = new JExtension($file);
                }
            }

            return true;
        }
    }
}
