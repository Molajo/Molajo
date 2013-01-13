<?php
/**
 * File Interface
 *
 * @package      Molajo
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\Filesystem;

defined('MOLAJO') or die;

/**
 * Defines a File Instance
 *
 * The path MUST be a string which defines the path containing the file
 *
 * The name MUST be a string which defines the file name, including the extension type
 *
 * The path_type MAY be 'relative', 'absolute', or left blank, which then defaults to 'relative'
 *
 * The permission value MUST be an integer defining read, write, and execute permissions to be
 *  assigned for the owner, owner's group, and everyone else
 *
 * The data value to be saved as file content MUST be a string
 *
 * The replace MAY be true if a file can be replaced, otherwise the value defaults to false
 *
 * The create_folders MAY be true if it is requested that missing folders automatically be
 *  created, otherwise the value defaults to false

 *
 * @package      Molajo
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 * @since        1.0
 *
 * Full interface specification:
 *  See https://github.com/Molajo/FilesystemInterface/filesystem-interface.md
 */
interface FileInterface extends FilesystemInterface
{
    /**
     * Retrieves metadata for the file specified in $file_name and $path fields amd populates an associative
     *  array with minimally the following elements: file_or_folder_indicator, last_accessed_date,
     *  last_updated_date, size, mimetype, absolute_path, relative_path, filename, and file_extension.
     *
     *  The value for metadata entries that cannot be determined for the file are set to false.
     *
     *  Additional elements can be provided, based on implementation
     *
     * InvalidArgumentException are thrown for the following errors:
     *  1. No value is specified for path.
     *  2. No file is specified for file_name.
     *  3. The path does not exist
     *  4. The file_name does not exist
     *  5. Permission to retrieve the Metadata was denied.
     *
     * @param   string  $file_name
     * @param   string  $file_name
     *
     * @return  null
     * @since   1.0
     */
    public function getMetadata($path, $file_name);

    /**
     * Retrieves the name of the file for the combined path and file sent in as the value for $path
     *
     * InvalidArgumentException are thrown for the following errors:
     *  1. No value is specified for $path.
     *  2. No file is identified as the last node in the path statement
     *  3. The path does not exist
     *  4. The file does not exist
     *  5. Permission to retrieve the filename was denied.
     *
     * @param   string  $file_name
     *
     * @return  null
     * @since   1.0
     */
    public function getFilename($path);

    /**
     * The requested path type, either 'absolute' or 'relative', is returned for the combined
     *  path and file sent in as the value for $file_name
     *
     * InvalidArgumentException are thrown for the following errors:
     *  1. No value is specified for path.
     *  2. No value is specified for file_name.
     *  3. The path does not exist
     *  4. The file_name does not exist
     *  5. The path_type value is not 'absolute', 'relative',  or spaces (defaults to 'relative')
     *  6. Permission to read the file is denied.
     *
     * @param   string  $path
     * @param   string  $file_name
     * @param   string  $path_type
     *
     * @return  null
     * @since   1.0
     */
    public function getPath($path, $file_name, $path_type = 'relative');

    /**
     * The path for the file is returned for the filename identified in the path and file_name sent in
     *
     * InvalidArgumentException are thrown for the following errors:
     *  1. No value is specified for path.
     *  2. No value is specified for file_name.
     *  3. The path does not exist
     *  4. The file_name does not exist
     *  5. Permission to read the file is denied.
     *
     * @param   string  $file_name
     *
     * @return  null
     * @since   1.0
     */
    public function getExtension($path, $file_name);

    /**
     * Retrieves the contents of the file named file_name that is located in the path folder
     *
     * InvalidArgumentException are thrown for the following errors:
     *  1. No value is specified for path.
     *  2. No value is specified for file_name.
     *  3. The path does not exist
     *  4. The file_name does not exist
     *  5. Permission to read the file is denied.
     *
     * @param   string  $path
     * @param   string  $file_name
     *
     * @return  null
     * @since   1.0
     */
    public function read($path, $file_name);

    /**
     * Creates or updates the file identified in the $file_name field using the content contained
     *  within the $data field.
     *
     * InvalidArgumentException are thrown for the following errors:
     *  1. No value is specified for path.
     *  2. No value is specified for file_name.
     *  3. The path does not exist
     *  4. The file_name does not exist
     *  5. Data is empty
     *  6. The file to be saved already exists and replace is set to false
     *  7. Folders must be created in order to save the file at the specified location
     *      and the create folders indicator is set to false
     *  8. Permission to update the file is denied.
     *
     * When permission to save the file is not available, an exception is thrown
     *
     * @param   string  $file_name
     * @param           $data
     * @param   bool    $replace
     * @param   bool    $create_folders
     *
     * @return  null
     * @since   1.0
     */
    public function save($path, $file_name, $data, $replace = false, $create_folders = false);

    /**
     * Copies the file identified in the $file_name field to the location identified in the $destination
     *  field, replacing the file if so indicated, and if the file exists, and creating missing folders
     *  when the indicator is set to do so.
     *
     * Exceptions are thrown in the following cases:
     *  1. No value is specified for path.
     *  2. No value is specified for file_name.
     *  3. The path does not exist
     *  4. The file_name does not exist
     *  5. No value is specified for destination_path.
     *  6. The destination_path specified does not exist and create_folders is false
     *  7. The filename already exists at destination_path but create_folders is false.
     *
     * @param   string  $file_name
     * @param   string  $destination_path
     * @param   bool    $replace
     * @param   bool    $create_folders
     *
     * @return  null
     * @since   1.0
     */
    public function copy($path, $file_name, $destination_path, $replace = false, $create_folders = false);

    /**
     * Moves the file identified in the $file_name field to the location identified in the $destination
     *  field, replacing the file if so indicated, and if the file exists, and creating missing folders
     *  when the indicator is set to do so.
     *
     * The Move method should also be used for Rename needs.
     *
     * Exceptions are thrown in the following cases:
     *  1. No value is specified for name.
     *  2. The file identified in the name field does not exist.
     *  3. No value is specified for destination.
     *  4. The destination specified does not exist and create_folders is false
     *  5. The file already exists but create_folders is false.
     *
     * @param   string  $file_name
     * @param   string  $destination_path
     * @param   bool    $replace
     * @param   bool    $create_folders
     *
     * @return  null
     * @since   1.0
     */
    public function move($path, $file_name, $destination_path, $replace = false, $create_folders = false);

    /**
     * Renames the file identified in the $file_name field to the value identified in the $new_name field
     *  within the existing folder identified in the $path field
     *
     * Exceptions are thrown in the following cases:
     *  1. No value is specified for name.
     *  2. No value is specified for path.
     *  3. No value is specified for path.
     *  2. The file identified in the name field does not exist.
     *  3. No value is specified for destination.
     *  4. The destination specified does not exist and create_folders is false
     *  5. The file already exists but create_folders is false.
     *
     * @param   string  $file_name
     * @param   string  $new_name
     *
     * @return  null
     * @since   1.0
     */
    public function rename($path, $file_name, $new_name);

    /**
     * Delete the file identified in the $file_name field. If no other files exist in the folder
     *  following the delete, also delete the folder if the delete_empty_folder indicator is true
     *
     * Exceptions are thrown in the following cases:
     *  1. No value is specified for name.
     *  2. The file identified in the name field does not exist.
     *  3. No value is specified for destination.
     *  4. The destination specified does not exist and create_folders is false
     *  5. The file already exists but create_folders is false.
     *
     * @param   string  $file_name
     * @param   string  $delete_empty_folder
     *
     * @return  null
     * @since   1.0
     */
    public function delete($path, $file_name, $delete_empty_folder = true);
}
