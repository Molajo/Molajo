<?php
/**
 * @package     Molajo
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Application\Service;
use Joomla\filesystem\File;

defined('MOLAJO') or die;

/**
 * File
 *
 * Service::File()->exists
 * Service::File()->getName
 * Service::File()->read
 * Service::File()->write
 * Service::File()->delete
 * Service::File()->copy
 * Service::File()->move
 * Service::File()->get_ext
 * Service::File()->remove_ext
 * Service::File()->upload
 *
 * @package     Molajo
 * @subpackage  Services
 * @since       1.0
 */
class FileService
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
            self::$instance = new FileService();
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
    }

    /**
     * exists
     *
     * Returns true if the file exists
     *
     * Example usage:
     * if (Service::File()->exists($formatXML)) {
     *
     * @param   string  $file  path and file name
     *
     * @return  boolean
     * @since   1.0
     */
    public function exists($file)
    {
        return is_file($file);
    }

    /**
     * getName
     *
     * Returns the name of the file, without any path.
     *
     * Example usage:
     * echo Service::File()->getName(MOLAJO_BASE_FOLDER.'/autoload.php');
     *
     * @param   string  $file  path and file name
     *
     * @return  string  filename
     * @since   1.0
     */
    public function getName($file)
    {
        return basename($file);
    }

    /**
     * read
     *
     * Read the contents of a file
     *
     * @param   string   $filename   The full file path
     * @param   boolean  $incpath    Use include path
     * @param   integer  $amount     Amount of file to read
     * @param   integer  $chunksize  Size of chunks to read
     * @param   integer  $offset     Offset of the file
     *
     * @return  mixed  Returns file contents or boolean False if failed
     * @since   1.0
     */
    public function read($filename, $incpath = false, $amount = 0, $chunksize = 8192, $offset = 0)
    {
        return File::read($filename, $incpath, $amount, $chunksize, $offset);
    }

    /**
     * write
     *
     * Write contents to a file
     *
     * @param   string   $file
     * @param   string   $buffer
     *
     * @return  boolean  True on success
     * @since   1.0
     */
    public function write($file, $buffer)
    {
        return File::write($file, $buffer, false);
    }

    /**
     * delete
     *
     * Delete a file or array of files
     *
     * @param   mixed  $file  The file name or an array of file names
     *
     * @return  boolean  True on success
     * @since   1.0
     */
    public function delete($file)
    {
        return File::delete($file);
    }

    /**
     * copy
     *
     * Copies a file
     *
     * @param   string   $src          The path to the source file
     * @param   string   $dest         The path to the destination file
     * @param   string   $path         An optional base path to prefix to the file names
     *
     * @return  boolean  True on success
     * @since   1.0
     */
    public function copy($src, $dest, $path = null)
    {
        return File::copy($src, $dest, $path, false);
    }

    /**
     * move
     *
     * Moves a file
     *
     * @param   string   $src          The path to the source file
     * @param   string   $dest         The path to the destination file
     * @param   string   $path         An optional base path to prefix to the file names
     * @param   boolean  $use_streams  false
     *
     * @return  boolean  True on success
     * @since   1.0
     */
    public function move($src, $dest, $path = '')
    {
        return File::move($src, $dest, $path, false);
    }

    /**
     * get_ext
     *
     * Gets the extension of a file name
     *
     * @param   string  $file  The file name
     *
     * @return  string  The file extension
     * @since   1.0
     */
    public function get_ext($file)
    {
        return File::getExt($file);
    }

    /**
     * remove_ext
     *
     * Strips the last extension off of a file name
     *
     * @param   string  $file  The file name
     *
     * @return  string  The file name without the extension
     * @since   1.0
     */
    public function remove_ext($file)
    {
        return preg_replace('#\.[^.]*$#', '', $file);
    }

    /**
     * upload
     *
     * Uploads a file to the destinated folder
     *
     * @param   string   $source
     * @param   string   $destination
     *
     * @return  boolean  True on success
     * @since   1.0
     */
    public function upload($source, $destination)
    {
        return File::upload($source, $destination, false);
    }
}
