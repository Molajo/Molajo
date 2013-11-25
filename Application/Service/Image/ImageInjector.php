<?php
/**
 * Image Dependency Injector
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\Service\Image;

use Molajo\IoC\Handler\AbstractInjector;
use CommonApi\IoC\ServiceHandlerInterface;

/**
 * Image Controller Dependency Injector
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @since      1.0
 */
class ImageInjector extends AbstractInjector implements ServiceHandlerInterface
{
    /**
     * Constructor
     *
     * @param   $options
     *
     * @since   1.0
     */
    public function __construct(array $options = array())
    {
        $options['service_namespace']        = 'Molajo\\Controller\\ImageController';
        $options['store_instance_indicator'] = true;
        $options['service_name']             = basename(__DIR__);

        parent::__construct($options);
    }

    /**
     * Identify Class Dependencies for Constructor Injection
     *
     * @return  array
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException;
     */
    public function setDependencies(array $reflection = null)
    {
        $reflection = null;

        $this->dependencies['Runtimedata'] = array();

        return $this->dependencies;
    }

    /**
     * Instantiate Class
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException;
     */
    public function instantiateService()
    {
        $media_folder  = $this->dependencies['Runtimedata']->site->media_folder;
        $standard_size = array(
            'thumbnail' => array('width' => 50, 'height' => 50),
            'small'     => array('width' => 75, 'height' => 75),
            'medium'    => array('width' => 150, 'height' => 150),
            'large'     => array('width' => 300, 'height' => 300),
            'xlarge'    => array('width' => 500, 'height' => 500),
            'normal'    => array('width' => null, 'height' => null)
        );
        $standard_type = array('portrait', 'landscape', 'auto', 'crop');
        $default_size  = 'normal';
        $default_type  = 'auto';

        $class                  = 'Molajo\\Controller\\ImageController';
        $this->service_instance = new $class(
            $media_folder,
            $standard_size,
            $standard_type,
            $default_size,
            $default_type
        );

        return $this;
    }
}
