<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * Install
 *
 * @package    Molajo
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
        $this->_initialise();
    }

    public function _initialise()
    {

        if ($element && is_a($element, 'SimpleXMLElement')) {
            $this->type = (string) $element->attributes()->type;
            $this->id = (string) $element->attributes()->id;

            switch ($this->type) {
                case 'resource':
                    break;

                case 'module':
                case 'theme':
                case 'language':
                    break;

                case PLUGIN_LITERAL:
                    break;

                default:
                    break;
            }
            $this->filename = (string) $element;
        }
    }

    /**
     * Custom loadLanguage method
     *
     * @param string $path The path where we find language files
     *
     * @return void
     *
     * @since   1.0
     */
    public function loadLanguage($path = null)
    {

    }

    /**
     * Custom install method
     *
     * @return boolean True on success
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
     * @return boolean True on success.
     *
     * @since   1.0
     */
    public function update()
    {

    }

    /**
     * Custom discover method
     *
     * @return array JExtension list of extensions available
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
    public function discover_install()
    {

    }

    /**
     * Refreshes the extension table cache
     *
     * @return boolean Result of operation, true if updated, false on failure.
     *
     * @since   1.0
     */
    public function refreshManifestCache()
    {

    }

    /**
     * Custom uninstall method
     *
     * @param integer $id The id of the module to uninstall
     *
     * @return boolean True on success
     *
     * @since   1.0
     */
    public function uninstall($id)
    {

    }

    /**
     * parseManifestXML
     *
     * @param string $xml path to the XML file
     *
     * @return array|bool XML metadata.
     * @since   1.0
     */
    public static function parseManifestXML($path)
    {
        if (Services::Filesystem()->folderExists($path)) {
        } else {
            return false;
        }

        $xml = Services::Configuration()->getFile('Application', 'Manifest');

        $data = array();

        $data['type'] = (string) $xml->type;
        $data['name'] = (string) $xml->name;
        $data['author'] = (string) $xml->author();
        $data['create_date'] = (string) $xml->create_date();
        $data['copyright'] = (string) $xml->copyright;
        $data['license'] = (string) $xml->license;
        $data['author_email'] = (string) $xml->author_email;
        $data['author_url'] = (string) $xml->author_url;
        $data['version'] = (string) $xml->version;
        $data['description'] = (string) $xml->description;

        return $data;
    }

    /**
     * Downloads a package
     *
     * @param string $url    URL of file to download
     * @param string $target Download target filename [optional]
     *
     * @return mixed Path to downloaded package or boolean false on failure
     *
     * @since   1.0
     */
    public static function downloadPackage($url, $target = false)
    {
        $config = Services::Registry()->get(CONFIGURATION_LITERAL);

        // Capture PHP errors
        $php_errormsg = 'Error Unknown';
        $track_errors = ini_get('track_errors');
        ini_set('track_errors', true);

        // Set user agent
        jimport('joomla.version');
        $version = new JVersion;
        ini_set('user_agent', $version->getUserAgent('Installer'));

        // Open the remote server socket for reading
        //turn on furl amd then off
        $inputHandle = fopen($url, "r");
        $error = strstr($php_errormsg, 'failed to open stream:');
        if (!$inputHandle) {
            MolajoError::raiseWarning(42, Services::Language()->sprintf('JLIB_INSTALLER_ERROR_DOWNLOAD_SERVER_CONNECT', $error));

            return false;
        }

        $meta_data = stream_get_meta_data($inputHandle);
        foreach ($meta_data['wrapper_data'] as $wrapper_data) {
            if (substr($wrapper_data, 0, strlen("Content-Disposition")) == "Content-Disposition") {
                $contentfilename = explode("\"", $wrapper_data);
                $target = $contentfilename[1];
            }
        }

        // Set the target path if not given
        if (!$target) {
            $target = $config->get('system_temp_folder') . '/' . self::getFilenameFromURL($url);
        } else {
            $target = $config->get('system_temp_folder') . '/' . basename($target);
        }

        // Initialise contents buffer
        $contents = null;

        while (!feof($inputHandle)) {
            $contents .= fread($inputHandle, 4096);
            if ($contents === false) {
                MolajoError::raiseWarning(44, Services::Language()->sprintf('JLIB_INSTALLER_ERROR_FAILED_READING_NETWORK_RESOURCES', $php_errormsg));

                return false;
            }
        }

        // Write buffer to file
        Services::Filesystem()->fileWrite($target, $contents);

        // Close file pointer resource
        fclose($inputHandle);

        // Restore error tracking to what it was before
        ini_set('track_errors', $track_errors);

        // bump the max execution time because not using built in php zip libs are slow
        set_time_limit(ini_get('max_execution_time'));

        // Return the name of the downloaded package
        return basename($target);
    }

    /**
     * Unpacks a file and verifies it as a Joomla element package
     * Supports .gz .tar .tar.gz and .zip
     *
     * @param string $p_filename The uploaded package filename or install directory
     *
     * @return array Two elements: extractdir and packagefile
     *
     * @since   1.0
     */
    public static function unpack($p_filename)
    {
        // Path to the archive
        $archivename = $p_filename;

        // Temporary folder to extract the archive into
        $tmpdir = uniqid('install_');

        // Clean the paths to use for archive extraction
        $extractdir = JPath::clean(dirname($p_filename) . '/' . $tmpdir);
        $archivename = JPath::clean($archivename);

        // Do the unpacking of the archive
        $result = JArchive::extract($archivename, $extractdir);

        if ($result === false) {
            return false;
        }

        /*
* Let's set the extraction directory and package file in the result array so we can
* cleanup everything properly later on.
*/
        $retval['extractdir'] = $extractdir;
        $retval['packagefile'] = $archivename;

        /*
* Try to find the correct install directory.  In case the package is inside a
* subdirectory detect this and set the install directory to the correct path.
*
* List all the items in the installation directory.  If there is only one, and
* it is a folder, then we will set that folder to be the installation folder.
*/
        $dirList = array_merge(Services::Filesystem()->folderFiles($extractdir, ''), Services::Folder()->folders($extractdir, ''));

        if (count($dirList) == 1) {
            if (Services::Filesystem()->folderExists($extractdir . '/' . $dirList[0])) {
                $extractdir = JPath::clean($extractdir . '/' . $dirList[0]);
            }
        }

        /*
* We have found the install directory so lets set it and then move on
* to detecting the extension type.
*/
        $retval['dir'] = $extractdir;

        /*
* Get the extension type and return the directory/type array on success or
* false on fail.
*/
        if ($retval['type'] = self::detectType($extractdir)) {
            return $retval;
        } else {
            return false;
        }
    }

    /**
     * Method to detect the extension type from a package directory
     *
     * @param string $p_dir Path to package directory
     *
     * @return mixed Extension type string or boolean false on fail
     *
     * @since   1.0
     */
    public static function detectType($p_dir)
    {
        // Search the install dir for an XML file
        $files = Services::Filesystem()->folderFiles($p_dir, '\.xml$', 1, true);

        if (!count($files)) {
            MolajoError::raiseWarning(1, Services::Language()->translate('JLIB_INSTALLER_ERROR_NOTFINDXMLSETUPFILE'));

            return false;
        }

        foreach ($files as $file) {
            $xml = simplexml_load_file($file);
            if (!$xml) {
                continue;
            }

            if ($xml->getName() == 'extension') {
            } else {
                unset($xml);
                continue;
            }

            $type = (string) $xml->attributes()->type;
            // Free up memory
            unset($xml);

            return $type;
        }

        MolajoError::raiseWarning(1, Services::Language()->translate('JLIB_INSTALLER_ERROR_NOTFINDJOOMLAXMLSETUPFILE'));
        // Free up memory.
        unset($xml);

        return false;
    }

    /**
     * Gets a file name out of a url
     *
     * @param string $url URL to get name from
     *
     * @return mixed String filename or boolean false if failed
     *
     * @since   1.0
     */
    public static function getFilenameFromURL($url)
    {
        if (is_string($url)) {
            $parts = explode('/', $url);

            return $parts[count($parts) - 1];
        }

        return false;
    }

    /**
     * Clean up temporary uploaded package and unpacked extension
     *
     * @param string $package   Path to the uploaded package file
     * @param string $resultdir Path to the unpacked extension
     *
     * @return boolean True on success
     *
     * @since   1.0
     */
    public static function cleanupInstall($package, $resultdir)
    {
        $config = Services::Registry()->get(CONFIGURATION_LITERAL);

        // Does the unpacked extension directory exist?
        if (is_dir($resultdir)) {
            Services::Filesystem()->folderDelete($resultdir);
        }

        // Is the package file a valid file?
        if (is_file($package)) {
            Services::Filesystem()->fileDelete($package);
        } elseif (is_file(JPath::clean($config->get('system_temp_folder') . '/' . $package))) {
            // It might also be just a base filename
            Services::Filesystem()->fileDelete(JPath::clean($config->get('system_temp_folder') . '/' . $package));
        }
    }

    /**
     * splitSql
     *
     * Splits sql file into array of discreet queries separated by ';'.
     *
     * @param string $sql The SQL statement.
     *
     * @return array Array of queries
     * @since   1.0
     */
    public static function splitSql($sql)
    {
        return Services::DB()->splitSql($sql);
    }
}
