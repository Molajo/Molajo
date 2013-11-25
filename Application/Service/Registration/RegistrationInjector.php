<?php
/**
 * Registration Dependency Injector
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\Service\Registration;

use Molajo\IoC\Handler\AbstractInjector;
use CommonApi\Exception\RuntimeException;
use CommonApi\IoC\ServiceHandlerInterface;

/**
 * Event Registration Dependency Injector
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @since      1.0
 */
class RegistrationInjector extends AbstractInjector implements ServiceHandlerInterface
{
    /**
     * Constructor
     *
     * @param  array $options
     *
     * @since  1.0
     */
    public function __construct(array $options = array())
    {
        $options['service_namespace']        = 'Molajo\\Event\\Registration';
        $options['store_instance_indicator'] = true;
        $options['service_name']             = basename(__DIR__);

        parent::__construct($options);
    }
}
