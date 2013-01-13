<?php
/**
 * Filesystem Adapter Interface
 *
 * @package      Molajo
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\Filesystem;

defined('MOLAJO') or die;

/**
 * Describes an Adapter-aware Filesystem Instance
 *
 * The path MUST be a string which defines the path containing the file
 *
 * @package      Molajo
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 * @since        1.0
 *
 * Full interface specification:
 *  See https://github.com/Molajo/FilesystemInterface/filesystem-interface.md
 */
interface FilesystemAdapterAwareInterface
{
    /**
     * Establishes a Filesystem Adapter instance on the object
     *
     * @param   FilesystemInterface  $filesystem
     *
     * @return  null
     * @since   1.0
     */
    public function setFilesystemAdapter(FilesystemInterface $filesystem);
}
