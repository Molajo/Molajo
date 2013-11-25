<?php
/**
 * Resources Data Dependency Injector
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\Service\Resourcesdata;

use Exception;
use Molajo\IoC\Handler\AbstractInjector;
use CommonApi\IoC\ServiceHandlerInterface;
use CommonApi\Exception\RuntimeException;

/**
 * Resources Data Dependency Injector
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @since      1.0
 */
class ResourcesdataInjector extends AbstractInjector implements ServiceHandlerInterface
{
    /**
     * Valid Data Object Types
     *
     * @var    array
     * @since  1.0
     */
    protected $valid_dataobject_types;

    /**
     * Valid Data Object Attributes
     *
     * @var    array
     * @since  1.0
     */
    protected $valid_dataobject_attributes;

    /**
     * Valid Model Types
     *
     * @var    array
     * @since  1.0
     */
    protected $valid_model_types;

    /**
     * Valid Model Attributes
     *
     * @var    array
     * @since  1.0
     */
    protected $valid_model_attributes;

    /**
     * Valid Data Types
     *
     * @var    array
     * @since  1.0
     */
    protected $valid_data_types;

    /**
     * Valid Query Attributes
     *
     * @var    array
     * @since  1.0
     */
    protected $valid_queryelements_attributes;

    /**
     * Valid Field Attributes
     *
     * @var    array
     * @since  1.0
     */
    protected $valid_field_attributes;

    /**
     * Valid Join Attributes
     *
     * @var    array
     * @since  1.0
     */
    protected $valid_join_attributes;

    /**
     * Valid Foreignkey Attributes
     *
     * @var    array
     * @since  1.0
     */
    protected $valid_foreignkey_attributes;

    /**
     * Valid Criteria Attributes
     *
     * @var    array
     * @since  1.0
     */
    protected $valid_criteria_attributes;

    /**
     * Valid Children Attributes
     *
     * @var    array
     * @since  1.0
     */
    protected $valid_children_attributes;

    /**
     * Valid Plugin Attributes
     *
     * @var    array
     * @since  1.0
     */
    protected $valid_plugin_attributes;

    /**
     * Valid Value Attributes
     *
     * @var    array
     * @since  1.0
     */
    protected $valid_value_attributes;

    /**
     * Valid Field Attribute Defaults
     *
     * @var    array
     * @since  1.0
     */
    protected $valid_field_attributes_default;

    /**
     * Datalists
     *
     * @var    array
     * @since  1.0
     */
    protected $valid_datalists;

    /**
     * Constructor
     *
     * @param  array $option
     *
     * @since  1.0
     */
    public function __construct(array $options = array())
    {
        $options['service_namespace']        = 'Molajo\\Resources\\Configuration\\Data';
        $options['store_instance_indicator'] = true;
        $options['service_name']             = basename(__DIR__);

        parent::__construct($options);

        $this->options['Resources'] = $options['Resources'];
    }

    /**
     * Retrieve and load valid properties for fields, data models and data objects
     *
     * @return  object
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException;
     */
    public function setDependencies(array $reflection = null)
    {
        parent::setDependencies($reflection);

        $options                             = array();
        $options['service_namespace']        = 'Molajo\\Resources\\Configuration\\Registry';
        $options['store_instance_indicator'] = true;
        $options['service_name']             = 'Registry';
        $this->dependencies['Registry']      = $options;

        $options = array();

        $fields = $this->options['Resources']->get('xml:///Molajo//Application//Fields.xml');

        /** Data Objects */
        $this->loadFieldProperties(
            $fields,
            'dataobjecttypes',
            'dataobjecttype',
            'valid_dataobject_types'
        );
        $this->loadFieldPropertiesWithAttributes(
            $fields,
            'dataobjectattributes',
            'dataobjectattribute',
            'valid_dataobject_attributes'
        );

        /** Models */
        $this->loadFieldProperties(
            $fields,
            'modeltypes',
            'modeltype',
            'valid_model_types'
        );
        $this->loadFieldPropertiesWithAttributes(
            $fields,
            'modelattributes',
            'modelattribute',
            'valid_model_attributes'
        );

        /** Data Types */
        $this->loadFieldPropertiesWithAttributes(
            $fields,
            'datatypeattributes',
            'datatypeattribute',
            'valid_data_types'
        );
        $this->loadFieldProperties(
            $fields,
            'queryelements',
            'queryelement',
            'valid_queryelements_attributes'
        );

        $list = $this->valid_queryelements_attributes;

        foreach ($list as $item) {
            $field = explode(',', $item);
            $this->loadFieldProperties($fields, $field[0], $field[1], $field[2]);
        }

        $datalistsArray = array();
        $datalistsArray = $this->loadDatalists(
            $datalistsArray,
            BASE_FOLDER . '/Application/Model/Datalist'
        );
        $datalistsArray = array_unique($datalistsArray);

        $this->valid_datalists = $datalistsArray;

        return $this->dependencies;
    }

    /**
     * Instantiate Class
     *
     * @return  object
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException;
     */
    public function instantiateService()
    {
        try {
            $class = $this->service_namespace;

            $this->service_instance = new $class(
                $this->valid_dataobject_types,
                $this->valid_dataobject_attributes,
                $this->valid_model_types,
                $this->valid_model_attributes,
                $this->valid_data_types,
                $this->valid_queryelements_attributes,
                $this->valid_field_attributes,
                $this->valid_join_attributes,
                $this->valid_foreignkey_attributes,
                $this->valid_criteria_attributes,
                $this->valid_children_attributes,
                $this->valid_plugin_attributes,
                $this->valid_value_attributes,
                $this->valid_field_attributes_default,
                $this->valid_datalists
            );
        } catch (Exception $e) {

            throw new RuntimeException
            ('IoC: Injector Instance Failed for ' . $this->service_namespace
            . ' failed.' . $e->getMessage());
        }

        return $this;
    }

    /**
     * Follows the completion of the instantiate service method
     *
     * @return  object
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException;
     */
    public function performAfterInstantiationLogic()
    {
        $dataObjectHandler = $this->createDataobjectHandler();
        $modelHandler      = $this->createModelHandler();
        $xmlHandler        = $this->createXmlHandler($modelHandler, $dataObjectHandler);
        $this->options['Resources']->setHandlerInstance('XmlHandler', $xmlHandler);

        return $this;
    }

    /**
     * Set these services in the Container
     *
     * @return  object
     * @since   1.0
     */
    public function setService()
    {
        $this->set_container_instance['Resources'] = $this->options['Resources'];
    }

    /**
     * Schedule the Next Service
     *
     * @return  $this
     * @since   1.0
     */
    public function scheduleNextService()
    {
        $this->schedule_service                      = array();
        $this->options                               = array();
        $this->schedule_service['Dispatcher']        = $this->options;
        $this->schedule_service['Registry']          = $this->options;
        $this->schedule_service['Resources']         = $this->options;
        $this->schedule_service['Data']              = $this->options;
        $this->schedule_service['Exceptionhandling'] = $this->options;
        $this->schedule_service['Fieldhandler']      = $this->options;

        $options                               = array();
        $options['service_namespace']          = 'Molajo\\Http\\Request';
        $options['store_properties_indicator'] = true;
        $options['service_name']               = 'Request';
        $this->schedule_service['Request']     = $options;

        $options                               = array();
        $options['service_namespace']          = 'Molajo\\Http\\Server';
        $options['store_properties_indicator'] = true;
        $options['service_name']               = 'Server';
        $this->schedule_service['Server']      = $options;

        $options                               = array();
        $options['service_namespace']          = 'Molajo\\Http\\Client';
        $options['store_properties_indicator'] = true;
        $options['service_name']               = 'Client';
        $this->schedule_service['Client']      = $options;

        $options                             = array();
        $options['service_namespace']        = 'Molajo\\Http\\Redirect';
        $options['store_instance_indicator'] = true;
        $options['service_name']             = 'Redirect';
        $this->schedule_service['Redirect']  = $options;

        return $this->schedule_service;
    }

    /**
     * loadFieldProperties
     *
     * @param   string $xml
     * @param   string $plural
     * @param   string $singular
     * @param   string $parameter_name
     *
     * @return  $this
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

        $this->$parameter_name = $typeArray;

        return $this;
    }

    /**
     * loadFieldPropertiesWithAttributes
     *
     * @param   string $xml
     * @param   string $plural
     * @param   string $singular
     * @param   string $parameter_name
     *
     * @return  $this
     * @since   1.0
     */
    protected function loadFieldPropertiesWithAttributes($xml, $plural, $singular, $parameter_name)
    {
        if (isset($xml->$plural->$singular)) {
        } else {
            return $this;
        }

        $typeArray        = array();
        $typeDefaultArray = array();
        foreach ($xml->$plural->$singular as $type) {
            $typeArray[]                             = (string)$type['name'];
            $typeDefaultArray[(string)$type['name']] = (string)$type['default'];
        }

        $this->$parameter_name = $typeArray;
        $temp                  = $parameter_name . '_defaults';
        $this->$temp           = $typeDefaultArray;

        return $this;
    }

    /**
     * loadDatalists
     *
     * @param   string $datalistsArray
     * @param   string $folder
     *
     * @return  array
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException;
     */
    protected function loadDatalists($datalistsArray, $folder)
    {
        try {

            $dirRead = dir($folder);
            $path    = $dirRead->path;

            while (false !== ($entry = $dirRead->read())) {
                if (is_dir($path . '/' . $entry)) {
                } else {
                    $datalistsArray[] = substr($entry, 0, strlen($entry) - 4);
                }
            }

            $dirRead->close();
        } catch (RuntimeException $e) {
            throw new RuntimeException
            ('IoC Injector Configuration: loadDatalists cannot find Datalists file for folder: ' . $folder);
        }

        return $datalistsArray;
    }

    /**
     * Create Dataobject Handler
     *
     * @return  object
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException;
     */
    protected function createDataobjectHandler()
    {
        $class = 'Molajo\\Resources\\Configuration\\DataobjectHandler';

        try {
            $handler = new $class (
                $this->service_instance,
                $this->dependencies['Registry'],
                $this->options['Resources']
            );
        } catch (Exception $e) {
            throw new RuntimeException ('Resources Data Injector createDataobjectHandler failed: '
            . $e->getMessage());
        }

        return $handler;
    }

    /**
     * Create Model Handler
     *
     * @return  object
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException;
     */
    protected function createModelHandler()
    {
        $class = 'Molajo\\Resources\\Configuration\\ModelHandler';

        try {
            $handler = new $class (
                $this->service_instance,
                $this->dependencies['Registry'],
                $this->options['Resources']
            );

        } catch (Exception $e) {
            throw new RuntimeException ('Resources Data Injector createModelHandler failed: '
            . $e->getMessage());
        }

        return $handler;
    }

    /**
     * Create Resource Handler Instances that are dependent upon configuration information
     *
     * @param   object $model_handler
     * @param   object $dataobject_handler
     *
     * @return  object
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException;
     */
    public function createXmlHandler($model_handler, $dataobject_handler)
    {
        $scheme = $this->createScheme();

        $resource_map = $this->readFile(BASE_FOLDER . '/Vendor/Molajo/Resources/Files/Output/ResourceMap.json');

        $class = 'Molajo\\Resources\\Handler\\XmlHandler';

        try {
            $xmlHandler = new $class (
                BASE_FOLDER,
                $resource_map,
                array(),
                $scheme->getScheme('Xml')->include_file_extensions,
                $model_handler,
                $dataobject_handler
            );
        } catch (Exception $e) {
            throw new RuntimeException ('Resources Data Injector createXmlHandler failed: '
            . $e->getMessage());
        }

        return $xmlHandler;
    }

    /**
     * Create Scheme Instance
     *
     * @return  object
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException;
     */
    protected function createScheme()
    {
        $class = 'Molajo\\Resources\\Scheme';

        $input = BASE_FOLDER . '/Vendor/Molajo/Resources/Files/Input/SchemeArray.json';

        try {
            $scheme = new $class ($input);
        } catch (Exception $e) {
            throw new RuntimeException ('Resources Scheme ' . $class
            . ' Exception during Instantiation: ' . $e->getMessage());
        }

        return $scheme;
    }

    /**
     * Read File
     *
     * @param  string $file_name
     *
     * @return array
     * @since  1.0
     */
    protected function readFile($file_name)
    {
        $temp_array = array();

        if (file_exists($file_name)) {
        } else {
            return array();
        }

        $input = file_get_contents($file_name);

        $temp = json_decode($input);

        if (count($temp) > 0) {
            $temp_array = array();
            foreach ($temp as $key => $value) {
                $temp_array[$key] = $value;
            }
        }

        return $temp_array;
    }
}
