<?php
/**
 * @package    Niambie
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @license    MIT
 */
namespace Molajo\Plugin\Dataobject;

use Molajo\Plugin\Plugin\Plugin;
use Molajo\Service\Services;

defined('NIAMBIE') or die;

/**
 * @package     Niambie
 * @license     MIT
 * @since       1.0
 */
class DataobjectPlugin extends Plugin
{
    /**
     * Data object specific logic following common data object connection in Controller
     *
     * @return  boolean
     * @since   1.0
     */
    public function onConnectDatabase()
    {
        if ($this->get('data_object', 'Database', 'model_registry') == 'Database') {
        } else {
            return true;
        }

        if ($this->get('data_object_service_class', 'Database', 'model_registry') == '') {
            $this->set('data_object_service_class', 'Database', 'model_registry');
        }

        if ($this->get('data_object_service_class', 'Database', 'model_registry') == 'Database') {
            $service_class = $this->get('data_object_service_class', 'Database', 'model_registry');
            $this->set('db', Services::$service_class()->connect($this->get('model_registry')), 'model');
            $this->set('query', $this->get('db', '', 'model')->getQuery($this->get('db', '', 'model')), 'model');
            $this->set('null_date', $this->get('db', '', 'model')->getNullDate(), 'model');
            try {
                $this->set('now', Services::Date()->getDate(), 'model');

            } catch (\Exception $e) {
                // ignore error due to Date Service activation later in sequence for some use
                $this->set('now', $this->get('model')->get('null_date'), 'model');
            }
        }

        return true;
    }

    /**
     * Prepares list of Dataobject Types
     *
     * @return  boolean
     * @since   1.0
     */
    public function onAfterRoute()
    {
        if (APPLICATION_ID == 2) {
        } else {
            return true;
        }

        $files = Services::Filesystem()->folderFiles(PLATFORM_FOLDER . '/Dataobject');

        if (count($files) === 0 || $files === false) {
            $dataobjectLists = array();
        } else {
            $dataobjectLists = $this->processFiles($files);
        }

        $resourceFiles = Services::Filesystem()->folderFiles(
            $this->get('extension_path', '', 'parameters')
                . '/Dataobject'
        );

        if (count($resourceFiles) == 0 || $resourceFiles === false) {
            $resourceLists = array();
        } else {
            $resourceLists = $this->processFiles($resourceFiles);
        }

        $new   = array_merge($dataobjectLists, $resourceLists);
        $newer = array_unique($new);
        sort($newer);

        $dataobject = array();

        foreach ($newer as $file) {
            $temp_row        = new \stdClass();
            $temp_row->value = $file;
            $temp_row->id    = $file;
            $dataobject[]    = $temp_row;
        }

        Services::Registry()->set(DATALIST_LITERAL, 'Dataobject', $dataobject);

        return true;
    }

    /**
     * Prepares list of Dataobject Lists
     *
     * @return  boolean
     * @since   1.0
     */
    protected function processFiles($files)
    {
        $fileList = array();

        foreach ($files as $file) {

            $length = strlen($file) - strlen('.xml');
            $value  = substr($file, 0, $length);

            $fileList[] = $value;
        }

        return $fileList;
    }
}
