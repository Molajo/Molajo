<?php
/**
 * Create Plugin
 *
 * @package    Molajo
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugin\Create;

use Molajo\Controller\CreateController;
use CommonApi\Event\CreateInterface;
use Molajo\Plugin\CreateEventPlugin;
use CommonApi\Exception\RuntimeException;
use Molajo\Service;

/**
 * Create Plugin
 *
 * @package     Molajo
 * @license     http://www.opensource.org/licenses/mit-license.html MIT License
 * @since       1.0
 */
class CreatePlugin extends CreateEventPlugin implements CreateInterface
{
    /**
     * Post-create processing for new extension
     *
     * @return  $this
     * @since   1.0
     */
    public function onAfterCreate()
    {
        return $this;
        if ($this->query_results->catalog_type_id >= CATALOG_TYPE_BEGIN
            AND $this->query_results->catalog_type_id <= CATALOG_TYPE_END
        ) {
        } else {
            return $this;
        }

        //if ($this->runtime_data->create_extension == 1) {

        $results = $this->createExtension();
        $results = $this->cloneGridMenuItem();
        //}

        //if ($results === false) {
        //	return $this;
        //}

        /** Sample Data */
        //if ($this->runtime_data->create_sample_data == 1) {

        $this->createSampleContent();

        return $this;
    }

    /**
     * Create extension folders and files using samples
     *
     * @return   bool
     * @since    1.0
     */
    protected function createExtension()
    {
        $source_folder   = 'Samples';
        $catalog_type    = CATALOG_TYPE_RESOURCE_LITERAL;
        $catalog_type_id = $this->runtime_data->criteria_catalog_type_id;

        /** Determine Source Folder for files to copy */
        $source_folder = $this->getSourceFolder($catalog_type, $source_folder);
        if ($source_folder === false) {
            return $this;
        }

        /** Determine Destination Folder for target location */
        $destinationFolder = $this->getDestinationFolder($catalog_type, $this->query_results->title);
        if ($destinationFolder === false) {
            return $this;
        }

        /** Copy Source to Destination */
        $results = $this->copy($source_folder, $destinationFolder);
        if ($results === false) {
            return $this;
        }

        /** Traverse Folders */
        $replace = ucfirst(strtolower('Samples'));
        $with    = ucfirst(strtolower($this->query_results->title));

        $results = $this->traverseFolders($destinationFolder, $replace, $with, $catalog_type_id);
        if ($results === false) {
            return $this;
        }

        return $this;
    }

    /**
     * Post-delete processing
     *
     * @return  $this
     * @since   1.0
     */
    public function onAfterDelete()
    {
        /** Determine Destination Folder for target location */
        $destinationFolder = $this->getFolderToDelete(CATALOG_TYPE_RESOURCE_LITERAL, $this->query_results->title);
        if ($destinationFolder === false) {
            return $this;
        }

        /** Delete the Destination */
        $results = $this->delete($destinationFolder);
        if ($results === false) {
            return $this;
        }

        return $this;
    }

    /**
     * determine source path
     *
     * @param   string $catalog_type
     * @param   string $source_folder
     *
     * @return  string
     * @since   1.0
     */
    protected function getSourceFolder($catalog_type, $source_folder)
    {
        if (is_dir(dirname(__FILE__) . '/' . $catalog_type . '/' . $source_folder)) {
            return dirname(__FILE__) . '/' . $catalog_type . '/' . $source_folder;
        }

        throw new RuntimeException
        ('CreatePlugin getSourceFolder: No resource for ' . $catalog_type . '/' . $source_folder);
    }

    /**
     * Get destination folder
     *
     * @param   string $catalog_type
     * @param   string $extension_name
     *
     * @return  string
     * @since   1.0
     */
    protected function getDestinationFolder($catalog_type, $extension_name)
    {
        if (is_dir(BASE_FOLDER . '/Source' . '/' . $catalog_type)) {
            if (is_dir(BASE_FOLDER . '/Source' . '/' . $catalog_type . '/' . $extension_name)) {
                // error extension already exists
                return $this;
            } else {
                return BASE_FOLDER . '/Source' . '/' . $catalog_type . '/' . $extension_name;
            }
        } elseif (is_dir(BASE_FOLDER . '/Source' . '/' . 'Views' . '/' . $catalog_type)) {
            if (is_dir(
                BASE_FOLDER . '/Source' . '/' . 'Views'
                . '/' . $catalog_type . '/' . $extension_name
            )
            ) {
                // error extension already exists
                return $this;
            } else {
                return BASE_FOLDER . '/Source' . '/' . 'Views' . '/' . $catalog_type . '/' . $extension_name;
            }
        }

        throw new RuntimeException
        ('CreatePlugin getDestinationFolder: No target folder for ' . $catalog_type . '/' . $extension_name);
    }

    /**
     * copy files and folders from source to destination for new extension
     *
     * @param   string $source
     * @param   string $destination
     *
     * @return  $this
     * @since   1.0
     *          throws   /Molajo/Plugins/Exception/RuntimeException
     */
    protected function copy($source, $destination)
    {
        $results = copy($source, $destination);
        if ($results === false) {
            throw new RuntimeException
            ('CreatePlugin copy: Could not copy: ' . $source . ' to ' . $destination);
        }

        return $this;
    }

    /**
     * copy files and folders from source to destination for new extension
     *
     * @param   string $destination
     * @param   string $replace
     * @param   string $with
     * @param   int    $catalog_type_id
     *
     * @return bool
     * @since  1.0
     */
    protected function traverseFolders($destination, $replace, $with, $catalog_type_id)
    {
        /** retrieve all folder names for destination **/
        $folders = Services::Filesystem()->folderFolders(
            $destination,
            $filter = '',
            $recurse = true,
            $fullpath = true,
            $exclude = array('.git')
        );

        $folders[] = $destination;

        /** process files in each folder **/
        foreach ($folders as $folder) {

            /** retrieve all file names in folder **/
            $files = Services::Filesystem()->folderFiles($folder);

            /** process each file **/
            foreach ($files as $file) {

                /** retrieve current file extension **/
                $file_extension = Services::Filesystem()->fileExtension($file);

                /** rename files, if needed **/
                if (strtolower($file) == $replace . '.' . $file_extension) {

                    $this->renameFile(
                        $existingName = $replace . '.' . $file_extension,
                        $newName = $with . '.' . $file_extension,
                        $folder
                    );

                    $this->changeWords($folder, $newName, $replace, $with, $catalog_type_id);
                } else {

                    $this->changeWords($folder, $file, $replace, $with, $catalog_type_id);
                }
            }
        }

        /** process each folder for renames last **/
        foreach ($folders as $folder) {

            /** rename folders, as needed **/
            if (basename($folder) == $replace) {
                /** see if the parent folders have been renamed **/
                $parentPath = dirname($folder);
                if (is_dir(dirname($parentPath))) {
                } else {
                    $parentPath = str_replace($replace, strtolower($with), $parentPath);
                }
                /** rename folder **/
                $this->renameFolder(
                    $existingName = $replace,
                    $newName = $with,
                    $parentPath
                );
            }
        }
    }

    /**
     * renameFolder
     *
     * @param   string $existingName
     * @param   string $newName
     * @param   string $path
     *
     * @return  $this
     * @since   1.0
     */
    protected function renameFolder($existingName, $newName, $path)
    {
        if (is_dir($path)) {
        } else {
            return $this;
        }
        if (is_dir($path . '/' . $existingName)) {
        } else {
            return $this;
        }

        $results = Services::Filesystem()->folderMove($existingName, $newName, $path);
        if ($results === false) {
            return $this;
        }

        return $this;
    }

    /**
     * Rename File
     *
     * @param   string $existingName
     * @param   string $newName
     * @param   string $path
     *
     * @return  $this
     * @since   1.0
     */
    protected function renameFile($existingName, $newName, $path)
    {
        if (is_dir($path)) {
        } else {
            return $this;
        }
        if (file_exists($path . '/' . $existingName)) {
        } else {
            return $this;
        }
        if (file_exists($path . '/' . $newName)) {
            return $this;
        }

        $results = Services::Filesystem()->fileMove($existingName, $newName, $path);
        if ($results === false) {
            return $this;
        }

        return $this;
    }

    /**
     * Changes words in file with consideration for case logic
     *
     * @param   string $path
     * @param   string $file
     * @param   string $replace
     * @param   string $with
     * @param   int    $catalog_type_id
     *
     * @return  $this
     * @since   1.0
     */
    protected function changeWords($path, $file, $replace, $with, $catalog_type_id)
    {
        if (file_exists($path . '/' . $file)) {
        } else {
            return $this;
        }

        $body = Services::Filesystem()->fileRead($path . '/' . $file);

        $body = str_replace(strtolower($replace), strtolower($with), $body);
        $body = str_replace(strtoupper($replace), strtoupper($with), $body);
        $body = str_replace(ucfirst(strtolower($replace)), ucfirst(strtolower($with)), $body);
        $body = str_replace('xxxxx', $catalog_type_id, $body);

        //@todo get the ftp stream stuff working -
        file_put_contents($file, $body);

        return $this;
    }

    /**
     * Create a Grid Menu Item using any other same menu item type as a guide
     *
     * @return  $this
     * @since   1.0
     */
    protected function cloneGridMenuItem()
    {
        $controller_class_namespace = $this->controller_namespace;
        $controller                 = new $controller_class_namespace();
        $controller->getModelRegistry($this->runtime_data->reference_data->catalog_type_menuitem_id_LITERAL, 'Grid');

        $controller->setDataobject();
        $controller->connectDatabase();

        $model_registry = 'GridMenuitem';

        $name_key       = $controller->get('name_key');
        $primary_key    = $controller->get('primary_key', 'id');
        $primary_prefix = $controller->get('primary_prefix', 'a');

        $controller->model->query->where(
            $controller->model->database->qn($primary_prefix)
            . '.' . $controller->model->database->qn('extension_instance_id') . ' = 100 '
        );
        $controller->model->query->where(
            $controller->model->database->qn($primary_prefix)
            . '.' . $controller->model->database->qn('lvl') . ' = 3 '
        );

        $item = $controller->getData('item');

        /**
         * echo '<br /><br /><br />';
         * echo $controller->model->query->__toString();
         * echo '<br /><br /><br />';
         */
        $fields = $this->registry->get($model_registry, 'Fields');
        if (count($fields) == 0 || $fields === null) {
            return $this;
        }

        $data = new \stdClass();
        /** Clone */
        foreach ($fields as $f) {
            foreach ($f as $key => $value) {
                if ($key == 'name') {
                    if (isset($item->$value)) {
                        $data->$value = $item->$value;
                    } else {
                        $data->$value = null;
                    }
                }
            }
        }

        if (isset($item->catalog_id)) {
            $data->catalog_id = $item->catalog_id;
        }
        $data->model_name = 'Grid';

        /** Overlay for this extension */
        $data->id    = null;
        $data->title = $this->query_results->title;
        $data->alias = Services::Filter()->filter($this->query_results->title, 'alias', 0, $this->query_results->title);

        $data->start_publishing_datetime = null;
        $data->stop_publishing_datetime  = null;
        $data->created_datetime          = null;
        $data->created_by                = 0;
        $data->modified_datetime         = null;
        $data->modified_by               = 0;
        $data->checked_out_datetime      = null;
        $data->checked_out_by            = 0;
        $data->catalog_id                = 0;
        $data->catalog_sef_request       = null;
        $data->version                   = 1;
        $data->version_of_id             = 0;
        $data->status_prior_to_version   = 0;
        $data->protected                 = 0;

        $data->customfields = array();
        $this->registry->sort($model_registry . 'Customfields');
        $customfields = $this->registry->getArray($model_registry . 'Customfields');
        if (count($customfields) > 0) {
            foreach ($customfields as $key => $value) {
                $data->customfields[$key] = '';
            }
        }

        $data->parameters = array();
        $this->registry->sort($model_registry . 'Parameters');
        $this->runtime_data = $this->registry->getArray($model_registry . 'Parameters');
        if (count($this->runtime_data) > 0) {
            foreach ($this->runtime_data as $key => $value) {

                if ($key == 'criteria_title') {
                    $data->parameters[$key] = $this->query_results->title;
                    //@todo get rid of one of these variables
                } elseif ($key == 'criteria_catalog_type_id') {
                    $data->parameters[$key] = $this->query_results->catalog_type_id;
                } elseif ($key == 'criteria_extension_instance_id') {
                    $data->parameters[$key] = $this->query_results->id;
                } elseif ($key == 'model_name') {
                    $data->parameters[$key] = $this->query_results->title;
                    $data->model_name       = 'Grid';
                    $data->model_type       = $this->runtime_data->reference_data->catalog_type_menuitem_id_LITERAL;
                } else {
                    $data->parameters[$key] = $value;
                }
            }
        }

        $data->metadata = array();
        $this->registry->sort($model_registry . 'Metadata');
        $this->runtime_data = $this->registry->getArray($model_registry . 'Metadata');

        if (count($this->runtime_data) > 0) {
            foreach ($this->runtime_data as $key => $value) {
                if ($key == 'title') {
                    $data->metadata[$key] = $this->query_results->title;
                } else {
                    $data->metadata[$key] = '';
                }
            }
        }

        /** Create Menu Item  */
        $controller       = new CreateController();
        $controller->data = $data;
        $data->id         = $controller->execute();

        if ($data->id === false) {
            //install failed
            return $this;
        }

        /** Create Catalog for Menu Item (it will plugin more) */
        $controller = new CreateController();

        $data2                        = new \stdClass();
        $data2->catalog_type_id       = $this->query_results->catalog_type_id;
        $data2->source_id             = $this->id;
        $data2->view_group_id         = 1;
        $data2->extension_instance_id = $this->id;
        $data2->model_name            = 'Catalog';
        $data2->sef_request           = $this->query_results->alias;
        $data2->page_type             = $this->query_results->page_type;
        $data2->routable              = 1;

        $controller->data = $data2;

        $controller->execute();

        return $this;
    }

    /**
     * Get destination folder
     *
     * @param   string $catalog_type
     * @param   string $extension_name
     *
     * @return  mixed
     * @since   1.0
     */
    protected function getFolderToDelete($catalog_type, $extension_name)
    {
        if (is_dir(BASE_FOLDER . '/Source' . '/' . $catalog_type)) {
            if (is_dir(BASE_FOLDER . '/Source' . '/' . $catalog_type . '/' . $extension_name)) {
                // error extension already exists
                return BASE_FOLDER . '/Source' . '/' . $catalog_type . '/' . $extension_name;
            }
        } elseif (is_dir(BASE_FOLDER . '/Source' . '/' . 'Views' . '/' . $catalog_type)) {
            if (is_dir(
                BASE_FOLDER . '/Source' . '/' . 'Views'
                . '/' . $catalog_type . '/' . $extension_name
            )
            ) {
                return BASE_FOLDER . '/Source' . '/' . 'Views' . '/' . $catalog_type . '/' . $extension_name;
            }
        }
    }

    /**
     * Delete files and folders when removing an extension
     *
     * @param   string $destination
     *
     * @return  $this
     * @since   1.0
     */
    protected function delete($destination)
    {
        echo ' I am now in delete. Here is the folder name: ' . $destination . '<br />';

        if (is_dir($destination)) {
            chmod($destination, "0777");
            $results = Services::Filesystem()->folderDelete($destination);
        }

        echo 'after delete';

        //@todo - test to see if the folder is there since a false is returned from j!
//		if ($results === false) {
//			//error copying source to destination
//			return $this;
//		}
        return $this;
    }
}
