<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
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
        $this->getModelRegistry(
            $this->get('model_type', '', 'parameters'),
            $this->get('model_name', '', 'parameters')
        );

        $this->setDataobject();

        $value = Services::Registry()->get(
            $this->get('template_view_model_registry', '', 'parameters'),
            'process_plugins'
        );
        $this->set('process_template_plugins', $value, 'model_registry');
        $this->getData($this->get('model_query_object', '', 'parameters'));

        if (strtolower($this->get('extension_title', '', 'parameters')) == 'commentsXXXXXXX') {
            echo '<pre>';
            var_dump($this->query_results);
            echo '</pre>';
        }

        if (PROFILER_ON
            && Services::Registry()->get(CONFIGURATION_LITERAL, 'profiler_output_queries_query_results', 0) == 1
        ) {

            $profiler_message = 'DisplayController->execute '
                . ' <br />Includer: ' . $this->get('includer_type', '', 'parameters')
                . ' <br />Model Type: ' . $this->get('model_type', '', 'parameters')
                . ' <br />Model Name: ' . $this->get('model_name', '', 'parameters')
                . ' <br />Model Query Object: ' . $this->get('model_query_object', '', 'parameters')
                . ' <br />Template Path: ' . $this->get('template_view_path', '', 'parameters')
                . ' <br />Wrap Path: ' . $this->get('wrap_view_path', '', 'parameters');

            ob_start();
            echo '<pre>';
            var_dump($this->query_results);
            echo '</pre>';
            $profiler_message .= ob_get_contents();
            ob_end_clean();

            Services::Profiler()->set(
                'Controller->onAfterExecute ' . $profiler_message,
                PROFILER_PLUGINS,
                VERBOSE
            );
        }

        if (count($this->query_results) == 0
            && (int)$this->get('criteria_display_view_on_no_results', 0) == 0
        ) {
            return '';
        }

        if (strtolower($this->get('includer_name', '', 'parameters')) == CATALOG_TYPE_WRAP_VIEW_LITERAL) {
            $this->rendered_output = $this->query_results;

        } else {

            $this->set('view_css_id', $this->get('template_view_css_id', '', 'parameters'), 'parameters');
            $this->set('view_css_class', $this->get('template_view_css_class', '', 'parameters'), 'parameters');

            $this->view_path = $this->get('template_view_path', '', 'parameters');
            $this->view_path_url = $this->get('template_view_path_url', '', 'parameters');

            $this->onBeforeViewRender();

            $this->set('rendered_output', $this->renderView());

            $this->onAfterViewRender();
        }

        if ($this->get('wrap_view_path_node', '', 'parameters') == '') {
            return $this->rendered_output;
        } else {
            return $this->wrapView();
        }
    }

    /**
     * Wrap View for wrapping Template View Rendered Output
     *
     * @return  string
     * @since   1.0
     */
    public function wrapView()
    {
        $this->query_results = array();

        $temp = new \stdClass();

        $this->set('view_css_id', $this->get('wrap_view_css_id', '', 'parameters'), 'parameters');
        $this->set('view_css_class', $this->get('wrap_view_css_class', '', 'parameters'), 'parameters');

        $temp->content = $this->rendered_output;

        $this->query_results[] = $this->rendered_output;

        $this->set('view_path', $this->get('wrap_view_path', '', 'parameters'));
        $this->set('view_path_url', $this->get('wrap_view_path_url', '', 'parameters'));

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

        /** 1. view handles loop and event processing */
        if (file_exists($this->get('view_path') . '/View/Custom.php')) {
            include $this->get('view_path') . '/View/Custom.php';

        } else {

            /** 2. controller manages loop and event processing */
            $totalRows = count($this->query_results);

            if (count($this->query_results) > 0) {

                $first = true;
                foreach ($this->query_results as $this->row) {

                    if ($first === true) {
                        $first = false;
                        if (file_exists($this->get('view_path') . '/View/Header.php')) {
                            include $this->get('view_path') . '/View/Header.php';
                        }
                    }

                    if (file_exists($this->get('view_path') . '/View/Body.php')) {
                        include $this->get('view_path') . '/View/Body.php';
                    }
                }

                if (file_exists($this->get('view_path') . '/View/Footer.php')) {
                    include $this->get('view_path') . '/View/Footer.php';
                }
            }
        }

        $output = ob_get_contents();
        ob_end_clean();

        return $output;
    }

    /**
     * Schedule Event onBeforeViewRender Event - could update query_results objects
     *
     * @return  bool
     * @since   1.0
     */
    protected function onBeforeViewRender()
    {
        $items = $this->query_results;
        $total_rows = count($items);
        $row_count = 1;
        $this->query_results = array();

        $first = 1;
        $even_or_odd = 'odd';

        if (count($items) == 0) {
        } else {
            foreach ($items as $item) {

                if ($row_count == $total_rows) {
                    $last_row = 1;
                } else {
                    $last_row = 0;
                }

                $this->set('row_count', $row_count, 'parameters');
                $this->set('even_or_odd', $even_or_odd, 'parameters');
                $this->set('total_rows', $total_rows, 'parameters');
                $this->set('last_row', $last_row, 'parameters');
                $this->set('first', $first, 'parameters');

                $arguments = array(
                    'model' => $this->get('model'),
                    'model_registry' => $this->get('model_registry'),
                    'parameters' => $this->get('parameters'),
                    'query_results' => $item,
                    'data' => array(),
                    'rendered_output' => array(),
                    'include_parse_sequence' => null,
                    'include_parse_exclude_until_final' => null
                );

                $arguments = Services::Event()->scheduleEvent(
                    'onBeforeViewRender',
                    $arguments,
                    $this->get('plugins', $this->get('plugins'))
                );

                $this->setPluginResultProperties($arguments);

                if ($even_or_odd == 'odd') {
                    $even_or_odd = 'even';
                } else {
                    $even_or_odd = 'odd';
                }
                $row_count++;
                $first = 0;
            }
        }

        return true;
    }

    /**
     * Schedule Event onAfterViewRender Event - can update rendered results
     *
     * @return  bool
     * @since   1.0
     */
    protected function onAfterViewRender()
    {
        $arguments = array(
            'model' => $this->get('model'),
            'model_registry' => $this->get('model_registry'),
            'parameters' => $this->get('parameters'),
            'query_results' => $this->query_results,
            'data' => array(),
            'rendered_output' => $this->get('rendered_output'),
            'include_parse_sequence' => null,
            'include_parse_exclude_until_final' => null
        );

        $arguments = Services::Event()->scheduleEvent(
            'onAfterViewRender',
            $arguments,
            $this->get('plugins', $this->get('plugins'))
        );

        $this->setPluginResultProperties($arguments);

        return;
    }
}
