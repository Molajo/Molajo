<?php
/**
 * @package     Molajo
 * @subpackage  Services
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * File
 *
 * @package     Molajo
 * @subpackage  Services
 * @since       1.0
 */
class MolajoFileService
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
            self::$instance = new MolajoFileService();
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
        return JFile::read($filename, $incpath, $amount, $chunksize, $offset);
    }

    /**
     * write
     *
     * Write contents to a file
     *
     * @param   string   $file
     * @param   string   $buffer
     * @param   boolean  $use_streams
     *
     * @return  boolean  True on success
     * @since   1.0
     */
    public function write($file, $buffer, $use_streams = false)
    {
        return JFile::write($file, $buffer, $use_streams);
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
        return JFile::delete($file);
    }

    /**
     * copy
     *
     * Copies a file
     *
     * @param   string   $src          The path to the source file
     * @param   string   $dest         The path to the destination file
     * @param   string   $path         An optional base path to prefix to the file names
     * @param   boolean  $use_streams  True to use streams
     *
     * @return  boolean  True on success
     * @since   1.0
     */
    public function copy($src, $dest, $path = null, $use_streams = false)
    {
        return JFile::copy($src, $dest, $path, $use_streams);
    }

    /**
     * move
     *
     * Moves a file
     *
     * @param   string   $src          The path to the source file
     * @param   string   $dest         The path to the destination file
     * @param   string   $path         An optional base path to prefix to the file names
     * @param   boolean  $use_streams  True to use streams
     *
     * @return  boolean  True on success
     * @since   1.0
     */
    public function move($src, $dest, $path = '', $use_streams = false)
    {
        return JFile::move($src, $dest, $path, $use_streams);
    }

    /**
     * get_extension
     *
     * Gets the extension of a file name
     *
     * @param   string  $file  The file name
     *
     * @return  string  The file extension
     * @since   1.0
     */
    public function get_extension($file)
    {
        return JFile::getExt($file);
    }

    /**
     * no_extension
     *
     * Strips the last extension off of a file name
     *
     * @param   string  $file  The file name
     *
     * @return  string  The file name without the extension
     * @since   1.0
     */
    public function no_extension($file)
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
        return JFile::upload($source, $destination, false);
    }
}
