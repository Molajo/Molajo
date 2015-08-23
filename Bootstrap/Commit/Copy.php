<?php
/**
 * Copy folder and files to github folder
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Copy folder and files to github folder
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @since      1.0.0
 */
final class Copy
{
    /**
     * Base Path
     *
     * @var    string
     * @since  1.0.0
     */
    protected $base_path;

    /**
     * Source Path
     *
     * @var    string
     * @since  1.0.0
     */
    protected $source_path;

    /**
     * Target Path
     *
     * @var    string
     * @since  1.0.0
     */
    protected $target_path;

    /**
     * Exclude Folders
     *
     * @var    array
     * @since  1.0.0
     */
    protected $exclude_folders = array();

    /**
     * Folders
     *
     * @var    array
     * @since  1.0.0
     */
    protected $folders = array();

    /**
     * Files
     *
     * @var    array
     * @since  1.0.0
     */
    protected $files = array();

    /**
     * Constructor
     *
     * @param  string $base_path
     * @param  string $source_path
     * @param  string $target_path
     * @param  array  $exclude_folders
     *
     * @since  1.0.0
     */
    public function __construct(
        $base_path,
        $source_path,
        $target_path,
        array $exclude_folders
    ) {
        $this->base_path       = $base_path;
        $this->source_path     = $source_path;
        $this->target_path     = $target_path;
        $this->exclude_folders = $exclude_folders;
    }

    /**
     * Process Request
     *
     * @return  $this
     * @since   1.0.0
     */
    public function process()
    {
        $this->removeTargetFiles($this->base_path . $this->target_path);
        $this->copySourceToTarget();

        return $this;
    }

    /**
     * Remove Directory and all contents
     *
     * @param   string $directory
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function removeTargetFiles($directory)
    {
        $file_paths = scandir($directory);

        foreach ($file_paths as $file_path) {

            foreach ($this->exclude_folders as $exclude) {
                if (($exclude === $file_path) || strpos($file_path, $exclude) > 0 ) {
                    $file_path = '';
                }
            }

            if ($file_path === '' || $file_path === '.' || $file_path === '..') {

            } else {

                if (filetype($directory . '/' . $file_path) === 'dir') {
                    $this->removeTargetFiles($directory . '/' . $file_path);

                } else {
                    unlink($directory . '/' . $file_path);
                }
            }
        }

        if ($directory === $this->base_path . $this->target_path) {
        } else {
            rmdir($directory);
        }

        return $this;
    }

    /**
     * Copy Source to Target
     *
     * @return  object
     * @since   1.0.0
     */
    protected function copySourceToTarget()
    {
        foreach (
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($this->base_path . $this->source_path,
                    RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::SELF_FIRST) as $item
        ) {
            if ($item->isDir()) {
                mkdir($this->base_path . $this->target_path . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
            } else {
                copy($item, $this->base_path . $this->target_path . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
            }
        }
    }
}
