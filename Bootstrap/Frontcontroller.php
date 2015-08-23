<?php
/**
 * Front Controller
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
//'Authorisation';

$steps = array('Route', 'Dispatcher', 'Execute', 'Response');

$dependencies = array(
    'runtime_data'   => 'Runtimedata',
    'plugin_data'    => 'Plugindata',
    'parameters'     => 'parameters',
    'row'            => 'row',
    'query'          => 'query',
    'model_registry' => 'model_registry',
    'query_results'  => 'query_results',
    'rendered_view'  => 'rendered_view',
    'rendered_page'  => 'rendered_page',
    'user'           => 'User',
    'exclude_tokens' => 'exclude_tokens',
    'token_objects'  => 'token_objects'
);

$debug_types = array(
    'Service',
    'Step',
    'Initialise',
    'Container',
    'Event',
    'onAfterStart',
    'onBeforeAuthenticate',
    'onAfterAuthenticate',
    'onBeforeLogout',
    'onAfterLogout',
    'onBeforeLogout',
    'onBeforeExecute',
    'onBeforeParseHead',
    'onBeforeRenderTemplate',
    'onAfterRenderTemplate',
    'onBeforeRead',
    'onAfterReadRow',
    'onAfterRead',
    'onBeforeCreate',
    'onBeforeUpdate',
    'onAfterUpdate',
);

$debug_types = array();

$front_controller_class = 'Molajo\\Controller\\FrontController';

$front_controller = new $front_controller_class (
    $schedule_factory,
    $requests,
    $steps,
    $dependencies,
    $error_handler = null,
    $exception_handler = null,
    $debug_handler = null,
    $debug_types,
    $debug = false
);

$front_controller->process();
