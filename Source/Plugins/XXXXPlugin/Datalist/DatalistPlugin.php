<?php
/**
 * Datalist Plugin
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Datalist;

use stdClass;
use Molajo\Plugins\AbstractPlugin;

/**
 * Datalist Plugin
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
class DatalistPlugin extends AbstractPlugin
{
    /**
     * Prepares list of Datalist Lists
     *
     * @return  $this
     * @since   1.0
     */
    public function onAfterRoute()
    {
        $options             = array();
        $options['multiple'] = true;

        $folders       = $this->resource->get('folder:///Molajo//Model//Datalist', $options);
        $all_datalists = array();

        foreach ($folders as $folder) {

            $datalist = array();

            $files = glob($folder . '*.xml');

            if (count($files) === 0 || $files === false) {
            } else {
                $datalists = $this->processFiles($files);
            }

            $all_datalists = array_unique($datalists);
            sort($all_datalists);
        }

        foreach ($all_datalists as $file) {
            $temp_row                  = new stdClass();
            $temp_row->value           = 'Molajo//Model//Datalist//' . basename($file);
            $temp_row->id              = basename($file);
            $datalist[basename($file)] = $temp_row;
        }

        $this->plugin_data->datalists->datalist = $datalist;

        return $this;
    }

    /**
     * Prepares list of Datalist Lists
     *
     * @param   array $files
     *
     * @return  array
     * @since   1.0
     */
    protected function processFiles($files)
    {
        $filelist = array();

        foreach ($files as $file) {

            $length = strlen($file) - strlen('.xml');
            $value  = substr($file, 0, $length);

            $filelist[] = $value;
        }

        return $filelist;
    }
}
