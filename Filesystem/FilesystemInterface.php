<?php

namespace Molajo\Filesystem;

/**
 * Describes a filesystem instance
 *
 * The name MUST be a string which defines the location of a file or folder. In the case
 *  of a file, the $name contains both the path and the filename
 *
 * The permission MUST be an integer that defines read, write, and execute permissions
 *  for owner, owner's group, and everyone else
 *
 * See https://github.com/AmyStephen/FilesystemInterface/filesystem-interface.md
 * for the full interface specification.
 */
interface FilesystemInterface
{
    /**
     * Tests existence of the file or folder specified in $name
     *
     * @param   string  $name
     *
     * @return  null
     * @since   1.0
     */
    public function exists($name);

    /**
     * Returns true if the value specified in $name is a file, else it returns false
     *
     * @param   string  $name
     *
     * @return  null
     * @since   1.0
     */
    public function isFile($name);

    /**
     * Returns true if the value specified in $name is a folder, otherwise returns false
     *
     * @param   string  $name
     *
     * @return  null
     * @since   1.0
     */
    public function isFolder($name);

    /**
     * Retrieves permissions for the file or folder specified in $name
     *
     * Example: returns associative array with indicators specifying whether or not
     *  the file or folder specified in $name is readable, updatable, or executable
     *
     * @param   string  $name
     *
     * @return  null
     * @since   1.0
     */
    public function getPermissions($name);

    /**
     * Sets permissions for the file or folder specified in $name
     *
     * Example: Sets the read, u readable, updatable, or executable,
     *  and the associated group and owner
     *
     * @param   string  $name
     * @param   int     $mode
     *
     * @return  null
     * @since   1.0
     */
    public function setPermissions($name, $permission);
}
