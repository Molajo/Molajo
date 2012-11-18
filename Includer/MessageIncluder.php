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
        Services::Registry()->set('Parameters', 'extension_catalog_type_id', 0);
        parent::__construct($name, $type);
        Services::Registry()->set('Parameters', 'criteria_html_display_filter', false);

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
        Services::Registry()->set('Parameters', 'template_view_id',
            Services::Registry()->get('Configuration', 'message_template_view_id'));
        Services::Registry()->set('Parameters', 'wrap_view_id',
            Services::Registry()->get('Configuration', 'message_wrap_view_id'));

        Services::Registry()->set('Parameters', 'criteria_display_view_on_no_results', 0);

        /** Template  */
        Helpers::View()->get(Services::Registry()->get('Parameters', 'template_view_id'), CATALOG_TYPE_TEMPLATE_LITERAL);

        /** Wrap  */
        Helpers::View()->get(Services::Registry()->get('Parameters', 'wrap_view_id'), CATALOG_TYPE_WRAP_LITERAL);

        /** Merge Configuration in */
        Services::Registry()->merge('Configuration', 'Parameters', true);

        /** DBO  */
        Services::Registry()->set('Parameters', 'model_type', 'Plugindata');
        Services::Registry()->set('Parameters', 'model_name', 'alertmessage');
        Services::Registry()->set('Parameters', 'model_query_object', 'list');

        /** Cleanup */
        Services::Registry()->delete('Parameters', 'item*');
        Services::Registry()->delete('Parameters', 'list*');
        Services::Registry()->delete('Parameters', 'form*');

        /** Sort */
        Services::Registry()->sort('Parameters');

        return true;
    }
}
