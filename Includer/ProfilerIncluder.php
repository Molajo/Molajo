<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Includer;

use Molajo\Helpers;
use Molajo\Service\Services;
use Molajo\Includer;

defined('MOLAJO') or die;

/**
 * Profiler
 *
 * @package     Molajo
 * @subpackage  Includer
 * @since       1.0
 */
Class ProfilerIncluder extends Includer
{
    /**
     * @param string $name
     * @param string $type
     *
     * @return null
     * @since   1.0
     */
    public function __construct($name = null, $type = null)
    {
        Services::Registry()->set(PARAMETERS_LITERAL, 'extension_catalog_type_id', 0);
        parent::__construct($name, $type);
        Services::Registry()->set(PARAMETERS_LITERAL, 'criteria_html_display_filter', false);

        return $this;
    }

    /**
     * setRenderCriteria
     *
     * Retrieve default values, if not provided by extension
     *
     * @return bool
     * @since   1.0
     */
    protected function setRenderCriteria()
    {
        Services::Registry()->set(PARAMETERS_LITERAL, 'template_view_id',
            Services::Registry()->get(CONFIGURATION_LITERAL, 'profiler_console_template_view_id'));

        Services::Registry()->set(PARAMETERS_LITERAL, 'wrap_view_id',
            Services::Registry()->get(CONFIGURATION_LITERAL, 'profiler_console_wrap_view_id'));

        Services::Registry()->set(PARAMETERS_LITERAL, 'criteria_display_view_on_no_results', 1);

        /** Template  */
        Helpers::View()->get(Services::Registry()->get(PARAMETERS_LITERAL, 'template_view_id'), CATALOG_TYPE_TEMPLATE_VIEW_LITERAL);

        /** Wrap  */
        Helpers::View()->get(Services::Registry()->get(PARAMETERS_LITERAL, 'wrap_view_id'), CATALOG_TYPE_WRAP_VIEW_LITERAL);

        /** Merge Configuration in */
        Services::Registry()->merge(CONFIGURATION_LITERAL, PARAMETERS_LITERAL, true);

        /** DBO  */
        Services::Registry()->set(PARAMETERS_LITERAL, 'model_type', DATA_OBJECT_LITERAL);
        Services::Registry()->set(PARAMETERS_LITERAL, 'model_name', DATA_OBJECT_PROFILER);
        Services::Registry()->set(PARAMETERS_LITERAL, 'model_query_object', QUERY_OBJECT_LIST);

        /** Cleanup */
        Services::Registry()->delete(PARAMETERS_LITERAL, 'item*');
        Services::Registry()->delete(PARAMETERS_LITERAL, 'list*');
        Services::Registry()->delete(PARAMETERS_LITERAL, 'form*');

        /** Sort */
        Services::Registry()->sort(PARAMETERS_LITERAL);

        return true;
    }
}
