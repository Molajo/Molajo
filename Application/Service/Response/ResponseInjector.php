<?php
/**
 * Response Dependency Injector
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\Service\Response;

use Molajo\IoC\Handler\AbstractInjector;
use CommonApi\IoC\ServiceHandlerInterface;

/**
 * Resources Data Dependency Injector
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @since      1.0
 */
class ResponseInjector extends AbstractInjector implements ServiceHandlerInterface
{
    /**
     * Constructor
     *
     * @param  array $option
     *
     * @since  1.0
     */
    public function __construct(array $options = array())
    {
        $options['service_namespace']        = 'Molajo\\Http\\Response';
        $options['store_instance_indicator'] = true;
        $options['service_name']             = basename(__DIR__);

        parent::__construct($options);
    }
}
