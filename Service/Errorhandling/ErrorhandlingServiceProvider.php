<?php
/**
 * Error Handling Service Provider
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\Service\Errorhandling;

use Molajo\IoC\AbstractServiceProvider;
use CommonApi\IoC\ServiceProviderInterface;

/**
 * Error Handling Exception
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @since      1.0
 */
class ErrorhandlingServiceProvider extends AbstractServiceProvider implements ServiceProviderInterface
{
    /**
     * Constructor
     *
     * @param  $options
     *
     * @since  1.0
     */
    public function __construct(array $options = array())
    {
        $options['service_namespace']        = 'Molajo\\Controller\\ErrorHandling';
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
        parent::setDependencies($reflection);

        $this->dependencies['Runtimedata'] = array();

        return $this->dependencies;
    }

    /**
     * Set Dependency Values
     *
     * @param   array $dependency_values
     *
     * @return  $this|object
     * @since   1.0
     */
    public function onBeforeInstantiation(array $dependency_values = null)
    {
        $this->dependencies['error_theme']
            = $this->dependencies['Runtimedata']->application->parameters->error_theme_id;
        $this->dependencies['error_page_view']
            = $this->dependencies['Runtimedata']->application->parameters->error_page_view_id;
        $this->dependencies['error_message_not_authorised']
            = $this->dependencies['Runtimedata']->application->parameters->error_403_message;
        $this->dependencies['error_message_not_found']
            = $this->dependencies['Runtimedata']->application->parameters->error_404_message;
        $this->dependencies['error_message_internal_server_error']
            = $this->dependencies['Runtimedata']->application->parameters->error_500_message;
        $this->dependencies['offline_switch']
            = $this->dependencies['Runtimedata']->application->parameters->offline;
        $this->dependencies['error_offline_theme']
            = $this->dependencies['Runtimedata']->application->parameters->offline_theme_id;
        $this->dependencies['error_page_offline_view']
            = $this->dependencies['Runtimedata']->application->parameters->offline_page_view_id;
        $this->dependencies['error_message_offline_switch']
            = $this->dependencies['Runtimedata']->application->parameters->offline_message;

        return $this;
    }
}
