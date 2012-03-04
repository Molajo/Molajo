<?php
/**
 * @package     Molajo
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Application\Service;

defined('MOLAJO') or die;

/**
 * Folder
 *
 * Service::Folder()->exists
 * Service::Folder()->getName
 * Service::Folder()->create
 * Service::Folder()->delete
 * Service::Folder()->copy
 * Service::Folder()->move
 * Service::Folder()->files
 * Service::Folder()->folders
 * Service::Folder()->listFolderTree
 *
 * @package     Molajo
 * @subpackage  Services
 * @since       1.0
 */
Class FolderService
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
            self::$instance = new FolderService();
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
     * Returns true if the folder exists
     *
     * @param   string  $folder  path and folder name
     *
     * @return  boolean
     * @since   1.0
     */
    public function exists($path)
    {
        return is_dir($path);
    }

    /**
     * getName
     *
     * Returns the name of the folder, without any path.
     *
     * @param   string  $folder  path and folder name
     *
     * @return  string  foldername
     * @since   1.0
     */
    public function getName($folder)
    {
        return basename($folder);
    }

    /**
     * create
     *
     * Create a folder
     *
     * @param   string   $path
     * @param   boolean  $mode
     *
     * @return  boolean  True on success
     * @since   1.0
     */
    public function create($path = '', $mode = 0755)
    {
        return JFolder::create($path, $mode);
    }


    /**
     * delete
     *
     * Delete a folder
     *
     * @param   mixed  $folder  The folder name or an array of folder names
     *
     * @return  boolean  True on success
     * @since   1.0
     */
    public function delete($path)
    {
        return JFolder::delete($path);
    }

    /**
     * copy
     *
     * Copies a folder
     *
     * @param   string   $src          The path to the source folder
     * @param   string   $dest         The path to the destination folder
     * @param   string   $path         An optional base path to prefix to the folder names
     * @param   string   $force        Force copy.
     * @param   boolean  $use_streams  True to use streams
     *
     * @return  boolean  True on success
     * @since   1.0
     */
    public function copy($src, $dest, $path = null, $force = false, $use_streams = false)
    {
        return JFolder::copy($src, $dest, $path, $use_streams);
    }

    /**
     * move
     *
     * Moves a folder
     *
     * @param   string   $src          The path to the source folder
     * @param   string   $dest         The path to the destination folder
     * @param   string   $path         An optional base path to prefix to the folder names
     * @param   boolean  $use_streams  True to use streams
     *
     * @return  boolean  True on success
     * @since   1.0
     */
    public function move($src, $dest, $path = '', $use_streams = false)
    {
        return JFolder::move($src, $dest, $path, $use_streams);
    }

    /**
     * Utility function to read the files in a folder.
     *
     * @param   string   $path           The path of the folder to read.
     * @param   string   $filter         A filter for file names.
     * @param   mixed    $recurse        True to recursively search into sub-folders, or an integer to specify the maximum depth.
     * @param   boolean  $full           True to return the full path to the file.
     * @param   array    $exclude        Array with names of files which should not be shown in the result.
     * @param   array    $excludefilter  Array of filter to exclude
     *
     * @return  array  Files in the given folder.
     *
     * @since   11.1
     */
    public function files($path, $filter = '.', $recurse = false, $full = false, $exclude = array('.svn', 'CVS', '.DS_Store', '__MACOSX'),
                          $excludefilter = array('^\..*', '.*~'))
    {
        return JFolder::files($path, $filter, $recurse, $full, $exclude, $excludefilter);
    }

    /**
     * folders
     *
     * Utility function to read the folders in a folder.
     *
     * @param   string   $path           The path of the folder to read.
     * @param   string   $filter         A filter for folder names.
     * @param   mixed    $recurse        True to recursively search into sub-folders, or an integer to specify the maximum depth.
     * @param   boolean  $full           True to return the full path to the folders.
     * @param   array    $exclude        Array with names of folders which should not be shown in the result.
     * @param   array    $excludefilter  Array with regular expressions matching folders which should not be shown in the result.
     *
     * @return  array  Folders in the given folder.
     *
     * @since   11.1
     */
    public function folders($path, $filter = '.', $recurse = false, $full = false, $exclude = array('.svn', 'CVS', '.DS_Store', '__MACOSX'),
                            $excludefilter = array('^\..*'))
    {
        return JFolder::folders($path, $filter, $recurse, $full, $exclude, $excludefilter = array('^\..*'));
    }

    /**
     * listFolderTree
     *
     * Lists folder in format suitable for tree display.
     *
     * @param   string   $path      The path of the folder to read.
     * @param   string   $filter    A filter for folder names.
     * @param   integer  $maxLevel  The maximum number of levels to recursively read, defaults to three.
     * @param   integer  $level     The current level, optional.
     * @param   integer  $parent    Unique identifier of the parent folder, if any.
     *
     * @return  array  Folders in the given folder.
     *
     * @since   11.1
     */
    public function listFolderTree($path, $filter, $maxLevel = 3, $level = 0, $parent = 0)
    {
        return JFolder::listFolderTree($path, $filter, $maxLevel, $level, $parent);
    }
}
