<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Includer;

use Molajo\Helpers;
use Molajo\Service\Services;
use Molajo\Includer;

defined('MOLAJO') or die;

/**
 * Message
 *
 * @package     Molajo
 * @subpackage  Includer
 * @since       1.0
 */
Class MessageIncluder extends Includer
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
            Services::Registry()->get(CONFIGURATION_LITERAL, 'message_template_view_id'));
        Services::Registry()->set(PARAMETERS_LITERAL, 'wrap_view_id',
            Services::Registry()->get(CONFIGURATION_LITERAL, 'message_wrap_view_id'));

        Services::Registry()->set(PARAMETERS_LITERAL, 'criteria_display_view_on_no_results', 0);

        /** Template  */
        Helpers::View()->get(Services::Registry()->get('parameters', 'template_view_id'), CATALOG_TYPE_TEMPLATE_VIEW_LITERAL);

        /** Wrap  */
        Helpers::View()->get(Services::Registry()->get('parameters', 'wrap_view_id'), CATALOG_TYPE_WRAP_VIEW_LITERAL);

        /** Merge Configuration in */
        Services::Registry()->merge(CONFIGURATION_LITERAL, PARAMETERS_LITERAL, true);

        /** DBO  */
        Services::Registry()->set(PARAMETERS_LITERAL, 'model_type', DATA_OBJECT_LITERAL);
        Services::Registry()->set(PARAMETERS_LITERAL, 'model_name', 'Messages');
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
