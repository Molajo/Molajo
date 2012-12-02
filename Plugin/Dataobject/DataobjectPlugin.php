<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Dataobject;

use Molajo\Plugin\Plugin\Plugin;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * @package     Molajo
 * @subpackage  Plugin
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
    public function onAfterSetDataobject()
    {
        if ($this->get('data_object', DATABASE_LITERAL, 'model_registry') == DATABASE_LITERAL) {
        } else {
            return true;
        }

        if ($this->get('data_object_service_class', DATABASE_LITERAL, 'model_registry') == '') {
            $this->set('data_object_service_class', DATABASE_LITERAL, 'model_registry');
        }

        if ($this->get('data_object_service_class', DATABASE_LITERAL, 'model_registry') == DATABASE_LITERAL) {

            $service_class = $this->get('data_object_service_class', DATABASE_LITERAL, 'model_registry');

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

        $files = Services::Filesystem()->folderFiles(
            PLATFORM_FOLDER . '/Dataobject'
        );

        if (count($files) === 0 || $files === false) {
            $dataobjectLists = array();
        } else {
            $dataobjectLists = $this->processFiles($files);
        }

        $resourceFiles = Services::Filesystem()->folderFiles(
            Services::Registry()->get('parameters', 'extension_path') . '/Dataobject'
        );

        if (count($resourceFiles) == 0 || $resourceFiles === false) {
            $resourceLists = array();
        } else {
            $resourceLists = $this->processFiles($resourceFiles);
        }

        $new = array_merge($dataobjectLists, $resourceLists);
        $newer = array_unique($new);
        sort($newer);

        $dataobject = array();

        foreach ($newer as $file) {
            $row = new \stdClass();
            $row->value = $file;
            $row->id = $file;
            $dataobject[] = $row;
        }

        Services::Registry()->set(DATALIST_LITERAL, 'Dataobjects', $dataobject);

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
            $value = substr($file, 0, $length);

            $fileList[] = $value;
        }

        return $fileList;
    }
}
