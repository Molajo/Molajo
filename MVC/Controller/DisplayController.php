<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\MVC\Controller;

use Molajo\Application;
use Molajo\Service\Services;
use Molajo\MVC\Controller\Controller;

defined('MOLAJO') or die;

/**
 * Display
 *
 * @package     Molajo
 * @subpackage  Controller
 * @since       1.0
 */
class DisplayController extends Controller
{
    /**
     * Interact with the model to connect with data object, run query, schedule events, push
     *  data into Template View and then push rendered results into Wrap View,
     *  returning rendered output to Includer
     *
     * @return  string  Rendered output
     * @since   1.0
     */
    public function execute()
    {
        $this->getModelRegistry($this->get('model_type'), $this->get('model_name'));

        $this->setDataobject();

        $this->set('process_template_plugins',
            Services::Registry()->get(
                $this->get('template_view_model_registry'), 'process_plugins')
        );

        $this->getData($this->get('model_query_object'));

        if (Services::Registry()->get('Configuration', 'profiler_output_queries_query_results', 0) == 1) {

            $profiler_message = 'DisplayController->execute '
                . ' <br />Includer: ' . $this->get('includer_type', '')
                . ' <br />Model Type: ' . $this->get('model_type', '')
                . ' <br />Model Name: ' . $this->get('model_name', '')
                . ' <br />Model Query Object: ' . $this->get('model_query_object', '')
                . ' <br />Template Path: ' . $this->get('template_view_path', '')
                . ' <br />Wrap Path: ' . $this->get('wrap_view_path', '');

            ob_start();
            echo '<pre>';
            var_dump($this->query_results);
            echo '</pre>';
            $profiler_message .= ob_get_contents();
            ob_end_clean();

            Services::Profiler()->set('Controller->onAfterExecute ' . $profiler_message,
                LOG_OUTPUT_PLUGINS, VERBOSE);
        }

        if (count($this->query_results) == 0
            && (int) $this->get('criteria_display_view_on_no_results', 0) == 0
        ) {
            return '';
        }

        if (strtolower($this->get('includer_name')) == CATALOG_TYPE_WRAP_VIEW_LITERAL) {
            $rendered_output = $this->query_results;

        } else {

            $this->set('view_css_id', $this->get('template_view_css_id'));
            $this->set('view_css_class', $this->get('template_view_css_class'));

            $this->view_path = $this->get('template_view_path');
            $this->view_path_url = $this->get('template_view_path_url');

            $this->onBeforeViewRender();

            $rendered_output = $this->renderView();

            $rendered_output = $this->onAfterViewRender($rendered_output);
        }

        if ($this->get('wrap_view_path_node') == '') {
            return $rendered_output;
        } else {
            return $this->wrapView($rendered_output);
        }
    }

    /**
     * Wrap View for wrapping Template View Rendered Output
     *
     * @param   $rendered_output
     *
     * @return  string
     * @since   1.0
     */
    public function wrapView($rendered_output)
    {
        $this->query_results = array();

        $temp = new \stdClass();

        $this->set('view_css_id', $this->get('wrap_view_css_id'));
        $this->set('view_css_class', $this->get('wrap_view_css_class'));

        $temp->content = $rendered_output;

        $this->query_results[] = $temp;

        $this->view_path = $this->get('wrap_view_path');
        $this->view_path_url = $this->get('wrap_view_path_url');

        return $this->renderView();
    }

    /**
     * Two ways that Template Views can be rendered:
     *
     * 1. If there is a Custom.php file in the Template View folder, then all query
     *      results are pushed into the View using the $this->query_results array/object.
     *      The Custom.php View must handle it's own loop iteration, if necessary
     *
     * 2. If there is a Header.php, and/or Body.php, and/or Footer.php Template View(s)
     *      then data is injected into the View, one row at a time, using the $this->row object.
     *      Header.php (if existing) - used one time for the first row in the resultset
     *      Body.php (if existing) - injected with $this->row for each row within $this->query_results
     *      Footer.php (if existing) - used one time for the last row in the resultset
     *
     * @return  string
     * @since   1.0
     */
    protected function renderView()
    {
//todo when close to done - do encoding - bring in filters given field definitions

        ob_start();
        echo '<pre>';
        var_dump($this->query_results);
        echo '</pre>';
        /** 1. view handles loop and event processing */
        if (file_exists($this->view_path . '/View/Custom.php')) {
            include $this->view_path . '/View/Custom.php';

        } else {

            /** 2. controller manages loop and event processing */
            $totalRows = count($this->query_results);

            if (count($this->query_results) > 0) {

                $first = true;
                foreach ($this->query_results as $this->row) {

                    if ($first === true) {
                        $first = false;
                        if (file_exists($this->view_path . '/View/Header.php')) {
                            include $this->view_path . '/View/Header.php';
                        }
                    }

                    if (file_exists($this->view_path . '/View/Body.php')) {
                        include $this->view_path . '/View/Body.php';
                    }
                }

                if (file_exists($this->view_path . '/View/Footer.php')) {
                    include $this->view_path . '/View/Footer.php';
                }
            }
        }

        $output = ob_get_contents();
        ob_end_clean();

        echo '<br />';
        echo $this->view_path;
        echo '<br />';
        echo $output;
        echo '<br />';

        return $output;
    }

    /**
     * Schedule onBeforeViewRender Event - could update query_results objects
     *
     * @return  bool
     * @since   1.0
     */
    protected function onBeforeViewRender()
    {
		if ((int) $this->get('process_plugins') == 0) {
            return true;
        }
        if (count($this->query_results) == 0) {
            return true;
        }

        $items = $this->query_results;
        $this->query_results = array();

        $this->parameters['row_count'] = 1;
        $this->parameters['even_or_odd'] = 'odd';
        $this->parameters['total_rows'] = count($items);

        if ((int) count($items) === 0 || $items === false) {
        } else {
            foreach ($items as $item) {

                $arguments = array(
                    'model_registry' => $this->model_registry,
                    'parameters' => $this->parameters,
                    'data' => $item,
                    'model_type' => $this->get('model_type'),
                    'model_name' => $this->get('model_name')
                );

                Services::Profiler()->set('DisplayController->onBeforeViewRender Schedules onBeforeViewRender',
                    LOG_OUTPUT_PLUGINS, VERBOSE);

                $arguments = Services::Event()->schedule('onBeforeViewRender', $arguments);

                if ($arguments === false) {
                    Services::Profiler()->set('DisplayController->onBeforeViewRender Schedules onBeforeViewRender',
                        LOG_OUTPUT_PLUGINS, VERBOSE);
                    return false;
                }

                $this->parameters = $arguments['parameters'];
                $this->query_results[] = $arguments['data'];

                if ($this->parameters['even_or_odd'] == 'odd') {
                    $this->parameters['even_or_odd'] = 'even';
                } else {
                    $this->parameters['even_or_odd'] = 'odd';
                }
                $this->parameters['row_count']++;
            }
        }

        return true;
    }

    /**
     * Schedule onAfterViewRender Event - can update rendered results
     *
     * @return  bool
     * @since   1.0
     */
    protected function onAfterViewRender($rendered_output)
    {
        $arguments = array(
            'model_registry' => $this->model_registry,
            'parameters' => $this->parameters,
            'rendered_output' => $rendered_output,
            'model_type' => $this->get('model_type'),
            'model_name' => $this->get('model_name')
        );

        Services::Profiler()->set('DisplayController->onAfterViewRender Schedules onAfterViewRender',
            LOG_OUTPUT_PLUGINS, VERBOSE);

        $arguments = Services::Event()->schedule('onAfterViewRender', $arguments);

        if ($arguments === false) {
            Services::Profiler()->set('DisplayController->onAfterViewRender Schedules onAfterViewRender',
                LOG_OUTPUT_PLUGINS, VERBOSE);
            return false;
        }

        $rendered_output = $arguments['rendered_output'];

        return $rendered_output;
    }
}
