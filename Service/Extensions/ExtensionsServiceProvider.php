<?php
/**
 * Extensions Service Provider
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\Service\Extensions;

use Exception;
use Molajo\IoC\AbstractServiceProvider;
use CommonApi\IoC\ServiceProviderInterface;
use CommonApi\Exception\RuntimeException;

/**
 * Rendering Extensions Service Provider
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @since      1.0
 */
class ExtensionsServiceProvider extends AbstractServiceProvider implements ServiceProviderInterface
{
    /**
     * Controller
     *
     * @var    object  CommonApi\Controller\ReadInterface
     * @since  1.0
     */
    protected $controller = null;

    /**
     * Constructor
     *
     * @param  $options
     *
     * @since  1.0
     */
    public function __construct(array $options = array())
    {
        $options['service_name']             = basename(__DIR__);
        $options['store_instance_indicator'] = true;
        $options['service_namespace']        = 'Molajo\\Resource\\ExtensionMap';

        parent::__construct($options);
    }

    /**
     * Define Dependencies for the Service
     *
     * @return  array
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException;
     */
    public function setDependencies(array $reflection = null)
    {
        parent::setDependencies($reflection);

        $options                           = array();
        $this->dependencies['Resource']   = $options;
        $this->dependencies['Runtimedata'] = $options;
        $this->dependencies['Cache']       = $options;

        return $this->dependencies;
    }

    /**
     * Set Dependencies for Instantiation
     *
     * @return  array
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException;
     */
    public function onBeforeInstantiation(array $dependency_values = null)
    {
        parent::onBeforeInstantiation($dependency_values);

        $this->dependencies['extensions_filename']
            = BASE_FOLDER . '/vendor/molajo/resource/Source/Files/Output/Extensions.json';

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
        $cache_results = $this->dependencies['Cache']->get('Extensions');

        if ($cache_results === false || $cache_results->isHit === false) {

            try {
                $this->service_instance = new $this->service_namespace(
                    $this->dependencies['Resource'],
                    $this->dependencies['Runtimedata'],
                    $this->dependencies['extensions_filename']
                );
            } catch (Exception $e) {
                throw new RuntimeException
                ('Render: Could not instantiate Handler: ' . $this->service_namespace);
            }

            $extensions             = $this->service_instance->createMap();
            $this->service_instance = $extensions;
            $this->dependencies['Cache']->set('Extensions', $extensions);

        } else {
            $this->service_instance = $cache_results->value;
        }

        return $this;
    }
}
