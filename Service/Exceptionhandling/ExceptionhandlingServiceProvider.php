<?php
/**
 * Exception Handling Service Provider
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajo\Service\Exceptionhandling;

use Whoops\Run;
use Whoops\Handler\PrettyPageHandler;
use Molajo\IoC\AbstractServiceProvider;
use CommonApi\IoC\ServiceProviderInterface;

/**
 * Exception Handling Exception
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0
 */
class ExceptionhandlingServiceProvider extends AbstractServiceProvider implements ServiceProviderInterface
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
        $options['service_namespace']        = 'Molajo\\Controller\\ExceptionHandling';
        $options['store_instance_indicator'] = true;
        $options['service_name']             = basename(__DIR__);

        parent::__construct($options);
    }

    /**
     * Service Provider Controller triggers the Service Provider to create the Class for the Service
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException;
     */
    public function instantiateService()
    {
        $run     = new Run;
        $handler = new PrettyPageHandler;

// Add a custom table to the layout:
// $handler->addDataTable('Ice-cream I like', array(
//                'Chocolate' => 'yes',
//                'Coffee & chocolate' => 'a lot',
//                'Strawberry & chocolate' => 'it\'s alright',
        //               'Vanilla' => 'ew'
//            ));

        $run->pushHandler($handler);

// Example: tag all frames inside a function with their function name
        $run->pushHandler(
            function ($exception, $inspector, $run) {

                $inspector->getFrames()->map(
                    function ($frame) {

                        if ($function = $frame->getFunction()) {
                            $frame->addComment("This frame is within function '$function'", 'cpt-obvious');
                        }

                        return $frame;
                    }
                );
            }
        );

        $run->register();

        $class = 'Molajo\\Controller\\ExceptionHandling';
//todo        $this->service_instance = new $class();

        return $this;
    }
}
