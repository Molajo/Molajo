<?php
/**
 * Configuration Service Plugin
 *
 * @package      Niambie
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\Service\Services\Configuration;

use Molajo\Service\Services;
use Molajo\Service\ServicesPlugin;

defined('NIAMBIE') or die;

/**
 * Configuration Service Plugin
 *
 * @author       Amy Stephen
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 * @since        1.0
 */
Class ConfigurationServicePlugin extends ServicesPlugin
{
    /**
     * On Before Startup for Configuration - runs after class instantiation
     *
     * @return  void
     * @since   1.0
     */
    public function onBeforeStartup()
    {
        $this->getFieldProperties();

        return;
    }

    /**
     * Retrieve and load valid properties for fields, data models and data objects
     *
     * @return  object
     * @throws  \Exception
     * @since   1.0
     */
    protected function getFieldProperties()
    {
        $xml = $this->service_class->getFile('Application', 'Fields');
        if ($xml === false) {
            throw new \Exception
            ('Configuration Plugin: getFieldProperties File Model Type: Application Model_name: Fields not found.');
        }

        $this->loadFieldProperties
        (
            $xml,
            'dataobjecttypes',
            'dataobjecttype',
            'valid_dataobject_types'
        );

        $this->loadFieldPropertiesWithAttributes
        (
            $xml,
            'dataobjectattributes',
            'dataobjectattribute',
            'valid_dataobject_attributes'
        );

        $this->loadFieldProperties
        (
            $xml,
            'modeltypes',
            'modeltype',
            'valid_model_types'
        );

        $this->loadFieldPropertiesWithAttributes
        (
            $xml,
            'modelattributes',
            'modelattribute',
            'valid_model_attributes'
        );

        $this->loadFieldProperties
        (
            $xml,
            'datatypes',
            'datatype',
            'valid_data_types'
        );

        $this->loadFieldProperties
        (
            $xml,
            'queryelements',
            'queryelement',
            'valid_queryelements_attributes'
        );

        $list = $this->service_class->get('valid_queryelements_attributes');

        foreach ($list as $item) {
            $field = explode(',', $item);
            $this->loadFieldProperties($xml, $field[0], $field[1], $field[2]);
        }

        $datalistsArray = array();
        $extensionArray = array();
        $datalistsArray = $this->loadDatalists($datalistsArray, PLATFORM_MVC . '/Model/Datalist');
        $datalistsArray = array_unique($datalistsArray);

        $this->service_class->set('valid_datalists', $datalistsArray);

        return;
    }

    /**
     * loadFieldProperties
     *
     * @param   string  $input
     * @param   string  $plural
     * @param   string  $singular
     * @param   string  $parameter_name
     *
     * @return  bool
     * @since   1.0
     */
    protected function loadFieldProperties($xml, $plural, $singular, $parameter_name)
    {
        if (isset($xml->$plural->$singular)) {
        } else {
            return false;
        }

        $types = $xml->$plural->$singular;
        if (count($types) === 0) {
            return false;
        }

        $typeArray = array();
        foreach ($types as $type) {
            $typeArray[] = (string)$type;
        }

        $this->service_class->set($parameter_name, $typeArray);

        return true;
    }

    /**
     * loadFieldPropertiesWithAttributes
     *
     * @param   string  $input
     * @param   string  $plural
     * @param   string  $singular
     * @param   string  $parameter_name
     *
     * @return  bool
     * @since   1.0
     */
    protected function loadFieldPropertiesWithAttributes($xml, $plural, $singular, $parameter_name)
    {
        if (isset($xml->$plural->$singular)) {
        } else {
            return false;
        }

        $typeArray        = array();
        $typeDefaultArray = array();
        foreach ($xml->$plural->$singular as $type) {
            $typeArray[]                             = (string)$type['name'];
            $typeDefaultArray[(string)$type['name']] = (string)$type['default'];
        }

        $this->service_class->set($parameter_name, $typeArray);
        $this->service_class->set($parameter_name . '_defaults', $typeDefaultArray);

        return true;
    }

    /**
     * loadDatalists
     *
     * @param   string  $datalistsArray
     * @param   string  $folder
     *
     * @return  array
     * @since   1.0
     * @throws  \Exception
     */
    protected function loadDatalists($datalistsArray, $folder)
    {
        try {

            $dirRead = dir($folder);

            $path = $dirRead->path;

            while (false !== ($entry = $dirRead->read())) {
                if (is_dir($path . '/' . $entry)) {
                } else {
                    $datalistsArray[] = substr($entry, 0, strlen($entry) - 4);
                }
            }

            $dirRead->close();

        } catch (\Exception $e) {
            throw new \Exception
            ('Configuration Plugin: loadDatalists cannot find Datalists file for folder: ' . $folder);
        }

        return $datalistsArray;
    }

    /**
     * On After Startup for Configuration
     *
     * @return  void
     * @since   1.0
     */
    public function onAfterStartup()
    {
        return;
    }
}
