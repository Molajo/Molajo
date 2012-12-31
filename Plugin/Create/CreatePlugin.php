<?php
/**
 * @package    Niambie
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @license    MIT
 */
namespace Molajo\Plugin\Create;

use Molajo\Plugin\Plugin\Plugin;
use Molajo\MVC\Controller\CreateController;
use Molajo\Service\Services;

defined('NIAMBIE') or die;

/**
 * Create
 *
 * @package     Niambie
 * @license     MIT
 * @since       1.0
 */
class CreatePlugin extends Plugin
{
    /**
     * Post-create processing for new extension
     *
     * @return boolean
     * @since   1.0
     */
    public function onAfterCreate()
    {
        if ($this->row->catalog_type_id >= CATALOG_TYPE_BEGIN
            AND $this->row->catalog_type_id <= CATALOG_TYPE_END
        ) {
        } else {
            return true;
        }

        //if ($this->parameters->create_extension == 1) {

        $results = $this->createExtension();
        $results = $this->cloneGridMenuItem();
        //}

        //if ($results === false) {
        //	return false;
        //}

        /** Sample Data */
        //if ($this->parameters->create_sample_data == 1) {

        $this->createSampleContent();

        return true;
    }

    /**
     * Create extension folders and files using samples
     *
     * @return bool
     * @since  1.0
     */
    protected function createExtension()
    {
        $sourceFolder    = 'Samples';
        $catalog_type    = CATALOG_TYPE_RESOURCE_LITERAL;
        $catalog_type_id = $this->parameters['criteria_catalog_type_id'];

        /** Determine Source Folder for files to copy */
        $sourceFolder = $this->getSourceFolder($catalog_type, $sourceFolder);
        if ($sourceFolder === false) {
            return false;
        }

        /** Determine Destination Folder for target location */
        $destinationFolder = $this->getDestinationFolder($catalog_type, $this->row->title);
        if ($destinationFolder === false) {
            return false;
        }

        /** Copy Source to Destination */
        $results = $this->copy($sourceFolder, $destinationFolder);
        if ($results === false) {
            return false;
        }

        /** Traverse Folders */
        $replace = ucfirst(strtolower('Samples'));
        $with    = ucfirst(strtolower($this->row->title));

        $results = $this->traverseFolders($destinationFolder, $replace, $with, $catalog_type_id);
        if ($results === false) {
            return false;
        }

        return true;
    }

    /**
     * Post-delete processing
     *
     * @param   $this->row
     * @param   $model
     *
     * @return boolean
     * @since   1.0
     */
    public function onAfterDelete()
    {
        /** Determine Destination Folder for target location */
        $destinationFolder = $this->getFolderToDelete(CATALOG_TYPE_RESOURCE_LITERAL, $this->row->title);
        if ($destinationFolder === false) {
            return false;
        }

        /** Delete the Destination */
        $results = $this->delete($destinationFolder);
        if ($results === false) {
            return false;
        }

        return true;
    }

    /**
     * determine source path
     *
     * @param $source
     *
     * @return mixed
     * @since  1.0
     */
    protected function getSourceFolder($catalog_type, $sourceFolder)
    {
        if (Services::Filesystem()->folderExists(dirname(__FILE__) . '/' . $catalog_type . '/' . $sourceFolder)) {
            return dirname(__FILE__) . '/' . $catalog_type . '/' . $sourceFolder;
        }

        //error - no source folder available to copy
        return false;
    }

    /**
     * Get destination folder
     *
     * @param $catalog_type
     * @param $extension_name
     *
     * @return mixed
     * @since  1.0
     */
    protected function getDestinationFolder($catalog_type, $extension_name)
    {
        if (Services::Filesystem()->folderExists(EXTENSIONS . '/' . $catalog_type)) {
            if (Services::Filesystem()->folderExists(EXTENSIONS . '/' . $catalog_type . '/' . $extension_name)) {
                // error extension already exists
                return false;
            } else {
                return EXTENSIONS . '/' . $catalog_type . '/' . $extension_name;
            }
        } elseif (Services::Filesystem()->folderExists(EXTENSIONS . '/' . 'Views' . '/' . $catalog_type)) {
            if (Services::Filesystem()->folderExists(
                EXTENSIONS . '/' . 'Views'
                    . '/' . $catalog_type . '/' . $extension_name
            )
            ) {
                // error extension already exists
                return false;
            } else {
                return EXTENSIONS . '/' . 'Views' . '/' . $catalog_type . '/' . $extension_name;
            }
        }
    }

    /**
     * copy files and folders from source to destination for new extension
     *
     * @param $source
     * @param $destination
     *
     * @return bool
     */
    protected function copy($source, $destination)
    {
        $results = Services::Filesystem()->folderCopy($source, $destination);
        if ($results === false) {
            //error copying source to destination
            return false;
        }
    }

    /**
     * copy files and folders from source to destination for new extension
     *
     * @param $destination
     * @param $replace
     * @param $with
     * @param $catalog_type_id
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
                if (Services::Filesystem()->folderExists(dirname($parentPath))) {
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
     * @param $existingName
     * @param $newName
     * @param $path
     *
     * @return boolean
     * @since  1.0
     */
    protected function renameFolder($existingName, $newName, $path)
    {
        if (Services::Filesystem()->folderExists($path)) {
        } else {
            return false;
        }
        if (Services::Filesystem()->folderExists($path . '/' . $existingName)) {
        } else {
            return false;
        }

        $results = Services::Filesystem()->folderMove($existingName, $newName, $path);
        if ($results === false) {
            return false;
        }

        return true;
    }

    /**
     * renameFile
     *
     * @param string $file
     *
     * @return boolean
     * @since  1.0
     */
    protected function renameFile($existingName, $newName, $path)
    {
        if (Services::Filesystem()->folderExists($path)) {
        } else {
            return false;
        }
        if (Services::Filesystem()->fileExists($path . '/' . $existingName)) {
        } else {
            return false;
        }
        if (Services::Filesystem()->fileExists($path . '/' . $newName)) {
            return false;
        }

        $results = Services::Filesystem()->fileMove($existingName, $newName, $path);
        if ($results === false) {
            return false;
        }

        return true;
    }

    /**
     * Changes words in file with consideration for case logic
     *
     * @param $path
     * @param $file
     * @param $replace
     * @param $with
     * @param $catalog_type_id
     *
     * @return bool
     * @since  1.0
     */
    protected function changeWords($path, $file, $replace, $with, $catalog_type_id)
    {
        if (Services::Filesystem()->fileExists($path . '/' . $file)) {
        } else {
            return false;
        }

        $body = Services::Filesystem()->fileRead($path . '/' . $file);

        $body = str_replace(strtolower($replace), strtolower($with), $body);
        $body = str_replace(strtoupper($replace), strtoupper($with), $body);
        $body = str_replace(ucfirst(strtolower($replace)), ucfirst(strtolower($with)), $body);
        $body = str_replace('xxxxx', $catalog_type_id, $body);

        //@todo get the ftp stream stuff working -
        Services::Filesystem()->fileWrite($file, $body);

        return true;
    }

    /**
     * Create a Grid Menu Item using any other same menu item type as a guide
     *
     * @return bool
     * @since  1.0
     */
    protected function cloneGridMenuItem()
    {
        $controllerClass = CONTROLLER_CLASS;
        $controller      = new $controllerClass();
        $controller->getModelRegistry(CATALOG_TYPE_MENUITEM_LITERAL, 'Grid');
        $controller->setDataobject();
        $controller->connectDatabase();

        $model_registry = 'GridMenuitem';

        $name_key       = $controller->get('name_key');
        $primary_key    = $controller->get('primary_key', 'id', 'model_registry');
        $primary_prefix = $controller->get('primary_prefix', 'a', 'model_registry');

        $controller->model->query->where(
            $controller->model->db->qn($primary_prefix)
                . '.' . $controller->model->db->qn('extension_instance_id') . ' = 100 '
        );
        $controller->model->query->where(
            $controller->model->db->qn($primary_prefix)
                . '.' . $controller->model->db->qn('lvl') . ' = 3 '
        );

        $item = $controller->getData(QUERY_OBJECT_ITEM);

        /**
        echo '<br /><br /><br />';
        echo $controller->model->query->__toString();
        echo '<br /><br /><br />';
         */
        $fields = Services::Registry()->get($model_registry, 'Fields');
        if (count($fields) == 0 || $fields === null) {
            return false;
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
        $data->title = $this->row->title;
        $data->alias = Services::Filter()->filter($this->row->title, 'alias', 0, $this->row->title);

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
        Services::Registry()->sort($model_registry . 'Customfields');
        $customfields = Services::Registry()->getArray($model_registry . 'Customfields');
        if (count($customfields) > 0) {
            foreach ($customfields as $key => $value) {
                $data->customfields[$key] = '';
            }
        }

        $data->parameters = array();
        Services::Registry()->sort($model_registry . PARAMETERS_LITERAL);
        $parameters = Services::Registry()->getArray($model_registry . PARAMETERS_LITERAL);
        if (count($parameters) > 0) {
            foreach ($parameters as $key => $value) {

                if ($key == 'criteria_title') {
                    $data->parameters[$key] = $this->row->title;

                    //@todo get rid of one of these variables
                } elseif ($key == 'criteria_catalog_type_id') {
                    $data->parameters[$key] = $this->row->catalog_type_id;

                } elseif ($key == 'criteria_extension_instance_id') {
                    $data->parameters[$key] = $this->row->id;

                } elseif ($key == 'model_name') {
                    $data->parameters[$key] = $this->row->title;
                    $data->model_name       = 'Grid';
                    $data->model_type       = CATALOG_TYPE_MENUITEM_LITERAL;

                } else {
                    $data->parameters[$key] = $value;
                }
            }
        }

        $data->metadata = array();
        Services::Registry()->sort($model_registry . 'Metadata');
        $parameters = Services::Registry()->getArray($model_registry . 'Metadata');

        if (count($parameters) > 0) {
            foreach ($parameters as $key => $value) {
                if ($key == 'title') {
                    $data->metadata[$key] = $this->row->title;
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
            return false;
        }

        /** Create Catalog for Menu Item (it will plugin more) */
        $controller = new CreateController();

        $data2                        = new \stdClass();
        $data2->catalog_type_id       = $this->row->catalog_type_id;
        $data2->source_id             = $this->id;
        $data2->view_group_id         = 1;
        $data2->extension_instance_id = $this->id;
        $data2->model_name            = 'Catalog';
        $data2->sef_request           = $this->row->alias;
        $data2->page_type             = $this->row->page_type;
        $data2->routable              = 1;

        $controller->data = $data2;

        $controller->execute();

        return true;
    }

    /**
     * Get destination folder
     *
     * @param $catalog_type
     * @param $extension_name
     *
     * @return mixed
     * @since  1.0
     */
    protected function getFolderToDelete($catalog_type, $extension_name)
    {
        if (Services::Filesystem()->folderExists(EXTENSIONS . '/' . $catalog_type)) {
            if (Services::Filesystem()->folderExists(EXTENSIONS . '/' . $catalog_type . '/' . $extension_name)) {
                // error extension already exists
                return EXTENSIONS . '/' . $catalog_type . '/' . $extension_name;
            }
        } elseif (Services::Filesystem()->folderExists(EXTENSIONS . '/' . 'Views' . '/' . $catalog_type)) {
            if (Services::Filesystem()->folderExists(
                EXTENSIONS . '/' . 'Views'
                    . '/' . $catalog_type . '/' . $extension_name
            )
            ) {
                return EXTENSIONS . '/' . 'Views' . '/' . $catalog_type . '/' . $extension_name;
            }
        }
    }

    /**
     * Delete files and folders when removing an extension
     *
     * @param $destination
     *
     * @return bool
     * @since  1.0
     */
    protected function delete($destination)
    {
        echo ' I am now in delete. Here is the folder name: ' . $destination . '<br />';

        if (Services::Filesystem()->folderExists($destination)) {
            chmod($destination, "0777");
            $results = Services::Filesystem()->folderDelete($destination);
        }

        echo 'after delete';

        //@todo - test to see if the folder is there since a false is returned from j!
//		if ($results === false) {
//			//error copying source to destination
//			return false;
//		}
        return true;
    }

}
