<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Dataobject;

use Molajo\Plugin\Plugin\Plugin;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**SE
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

        if ($this->model_registry['data_object'] == DATABASE_LITERAL) {
        } else {
            return true;
        }

        if ($this->model_registry['data_object_service_class'] == '') {
            $this->model_registry['data_object_service_class'] = DATABASE_LITERAL;
        }

        if ($this->model_registry['data_object_service_class'] == DATABASE_LITERAL) {

            $service_class = $this->model_registry['data_object_service_class'];

            $this->model->db = Services::$service_class()->connect();

            $this->model->set('query', $this->model->db->getQuery($this->model->db));

            $this->model->set('null_date', $this->model->db->getNullDate());

            try {
                $this->model->set('now', Services::Date()->getDate());

            } catch (\Exception $e) {
                // ignore error due to Date Service activation later in sequence for some use
                $this->model->set('now', $this->model->get('null_date'));
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
            Services::Registry()->get(PARAMETERS_LITERAL, 'extension_path') . '/Dataobject'
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
