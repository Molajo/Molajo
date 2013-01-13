<?php
/**
 * Filesystem Connect Interface
 *
 * @package      Molajo
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\Filesystem;

defined('MOLAJO') or die;

/**
 * Defines a Filesystem Connect Interface
 *
 * The options field MAY be an array of values necessary for authentication with the
 *   specified filesystem service adapter
 *
 * @package      Molajo
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 * @since        1.0
 *
 * Full interface specification:
 *  See https://github.com/Molajo/FilesystemInterface/filesystem-interface.md
 */
interface FilesystemConnectInterface
{
    /**
     * Tests existence of the file or folder specified in $name
     *
     * @param   string  $name
     *
     * @return  null
     * @since   1.0
     */
    public function __construct($options = array());

    /**
     * Returns true if the value specified in $name is a file, else it returns false
     *
     * @param   string  $name
     *
     * @return  null
     * @since   1.0
     */
    public function authenticate($name);
}
