<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
namespace Molajo\Controller;

use Molajo\Application;
use Molajo\Service\Services;
use Molajo\Controller\Controller;

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
     * Display action is used to render view output
     *
     * @return string Rendered output
     * @since   1.0
     */
    public function execute()
    {
        /**
        if ($action == 'add') {
        } elseif ($action == 'edit') {
        } elseif ($action == 'display') {
        }
	echo 		' <br /><br /><br /><br />Includer: ' . $this->get('includer_type', '')
				. ' <br />Model Type: ' . $this->get('model_type', '')
				. ' <br />Model Type: ' . $this->get('model_type', '')
				. ' <br />Model Name: ' . $this->get('model_name', '')
				. ' <br />Model Parameter: ' . $this->get('model_parameter', '')
				. ' <br />Model Query Object: ' . $this->get('model_query_object', '')
				. ' <br />Template Path: ' . $this->get('template_view_path', '')
				. ' <br />Wrap Path: ' . $this->get('wrap_view_path', '');
		**/
		if ($this->get('model_name', '') == '') {
            $this->query_results = array();

        } else {
            $this->connect($this->get('model_type'), $this->get('model_name'));

            if ((int) $this->get('content_id') == 0) {
//todo end up with: 1. result, 2. item, 3. list 4. distinct (listbox) (dbo needs to change - add parameter for specific query, don't hijack
            } elseif (strtolower($this->get('model_type', '')) == 'dbo') {

            } else {
                $this->set('id', $this->get('content_id'));
                $this->set('model_query_object', 'item');
            }

            /** Run Query */
            $this->getData($this->get('model_query_object', 'item'));

            if (Services::Registry()->get('Configuration', 'profiler_output_queries_query_results', 0) == 1) {

                $profiler_message = 'DisplayController->execute '
                    . ' <br />Includer: ' . $this->get('includer_type', '')
                    . ' <br />Model Type: ' . $this->get('model_type', '')
                    . ' <br />Model Type: ' . $this->get('model_type', '')
                    . ' <br />Model Name: ' . $this->get('model_name', '')
                    . ' <br />Model Parameter: ' . $this->get('model_parameter', '')
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
                    LOG_OUTPUT_TRIGGERS, VERBOSE);
            }
        }

        /** no results */
        if (count($this->query_results) == 0
            && (int) $this->get('criteria_display_view_on_no_results', 0) == 0
        ) {
            return '';
        }

        if (strtolower($this->get('includer_name', '')) == 'wrap') {
            $rendered_output = $this->query_results;

        } else {

            /** Template View */
            $this->set('view_css_id', $this->get('template_view_css_id'));
            $this->set('view_css_class', $this->get('template_view_css_class'));

            $this->view_path = $this->get('template_view_path');
            $this->view_path_url = $this->get('template_view_path_url');

            /** Trigger Pre-View Render Event */
            $this->onBeforeViewRender();

            /**
             *  For primary content (the extension determined in Application::Request),
             *      save query results in the Request object for reuse by other
             *      extensions.
             */
            if ($this->get('extension_primary') == true) {
                Services::Registry()->set('RouteParameters', 'query_resultset', $this->query_results);
            }

            /** Render View */
            $rendered_output = $this->renderView();

            /** Trigger After-View Render Event */
            $rendered_output = $this->onAfterViewRender($rendered_output);
        }

        /** Wrap template view results */
        return $this->wrapView($this->get('wrap_view_title'), $rendered_output);
    }

    /**
     * wrapView
     *
     * @param  $view
     * @param  $rendered_output
     *
     * @return string
     * @since  1.0
     */
    public function wrapView($view, $rendered_output)
    {
        $this->query_results = array();

        $temp = new \stdClass();

        $this->set('view_css_id', $this->get('wrap_view_css_id'));
        $this->set('view_css_class', $this->get('wrap_view_css_class'));

        $temp->content = $rendered_output;

        $this->query_results[] = $temp;

        /** paths */
        $this->view_path = $this->get('wrap_view_path');
        $this->view_path_url = $this->get('wrap_view_path_url');

        return $this->renderView();
    }

    /**
     * renderView
     *
     * Depending on the files within view/view-type/view-name/View/*.*:
     *
     * 1. Include a single Custom.php file to process all query results in $this->query_results
     *
     * 2. Include Header.php, Body.php, and/or Footer.php views for Molajo to
     *  perform the looping, injecting $row into each of the three views
     *
     * On no query results
     *
     * @return string
     * @since   1.0
     */
    protected function renderView()
    {
//todo think about empty queryresults processing when parameter set to true (custom and footer?)
//todo think about the result, item, and list processing - get dbo's in shape, triggers
//todo when close to done - do encoding - bring in filters - how?

        /** start collecting output */
        ob_start();

        /** 1. view handles loop and event processing */
        if (file_exists($this->view_path . '/View/Custom.php')) {
            include $this->view_path . '/View/Custom.php';

        } else {

            /** 2. controller manages loop and event processing */
            $totalRows = count($this->query_results);
            if (count($this->query_results) > 0) {

                $first = true;
                foreach ($this->query_results as $this->row) {

                    /** header: before any rows are processed */
                    if ($first == true) {
                        $first = false;
                        if (file_exists($this->view_path . '/View/Header.php')) {
                            include $this->view_path . '/View/Header.php';
                        }
                    }

                    /** body: once for each row */
                    if (file_exists($this->view_path . '/View/Body.php')) {
                        include $this->view_path . '/View/Body.php';
                    }
                }

                /** footer: after all rows are processed */
                if (file_exists($this->view_path . '/View/Footer.php')) {
                    include $this->view_path . '/View/Footer.php';
                }
            }
        }

        /** collect and return rendered output */
        $output = ob_get_contents();
        ob_end_clean();

        return $output;
    }

    /**
     * Schedule onBeforeViewRender Event - could update query_results objects
     *
     * @return bool
     * @since  1.0
     */
    protected function onBeforeViewRender()
    {
        if ((int) $this->get('process_triggers') == 0) {
            return true;
        }
		if (count($this->query_results) == 0) {
			return true;
		}

		/** Process each item, one at a time */
		$items = $this->query_results;
		$this->query_results = array();

		$this->parameters['row_count'] = 1;
		$this->parameters['even_or_odd'] = 'odd';
		$this->parameters['total_rows'] = count($items);


		foreach ($items as $item) {

			$arguments = array(
				'table_registry_name' => $this->table_registry_name,
				'parameters' => $this->parameters,
				'data' => $item,
				'model_name' => $this->get('model_name')
			);

			Services::Profiler()->set('DisplayController->onBeforeViewRender Schedules onBeforeViewRender', LOG_OUTPUT_TRIGGERS, VERBOSE);

			$arguments = Services::Event()->schedule('onBeforeViewRender', $arguments);

			if ($arguments == false) {
				Services::Profiler()->set('DisplayController->onBeforeViewRender Schedules onBeforeViewRender', LOG_OUTPUT_TRIGGERS, VERBOSE);
				return false;
			}

			Services::Profiler()->set('DisplayController->onBeforeViewRender Schedules onBeforeViewRender', LOG_OUTPUT_TRIGGERS, VERBOSE);

			$this->parameters = $arguments['parameters'];
			$this->query_results[] = $arguments['data'];

			if ($this->parameters['even_or_odd'] == 'odd') {
				$this->parameters['even_or_odd'] = 'even';
			} else {
				$this->parameters['even_or_odd'] = 'odd';
			}
			$this->parameters['row_count']++;
		}

        return true;
    }

    /**
     * Schedule onAfterViewRender Event - can update rendered results
     *
     * Position where mustache and Twig can process on rendered results
     *
     * @return bool
     * @since   1.0
     */
    protected function onAfterViewRender($rendered_output)
    {
        $arguments = array(
 			'table_registry_name' => $this->table_registry_name,
            'parameters' => $this->parameters,
			'rendered_output' => $rendered_output,
            'model_name' => $this->get('model_name')
        );

		Services::Profiler()->set('DisplayController->onAfterViewRender Schedules onAfterViewRender', LOG_OUTPUT_TRIGGERS, VERBOSE);

        $arguments = Services::Event()->schedule('onAfterViewRender', $arguments);

        if ($arguments == false) {
			Services::Profiler()->set('DisplayController->onAfterViewRender Schedules onAfterViewRender', LOG_OUTPUT_TRIGGERS, VERBOSE);
            return false;
        }

		Services::Profiler()->set('DisplayController->onAfterViewRender Schedules onAfterViewRender', LOG_OUTPUT_TRIGGERS, VERBOSE);

		$rendered_output = $arguments['rendered_output'];
        return $rendered_output;
    }
}
