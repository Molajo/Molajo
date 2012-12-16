<?php
/**
 * @package    Niambie
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    MIT, see License folder
 */
namespace Molajo\Service\Services\Theme\Includer;

use Molajo\Helpers;
use Molajo\Service\Services;
use Molajo\Service\Services\Theme\Includer;

defined('NIAMBIE') or die;

/**
 * Message
 *
 * @package     Niambie
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
    public function __construct($include_name = null, $include_type = null)
    {
        Services::Registry()->set('include', 'extension_catalog_type_id', 0);
        parent::__construct($include_name, $include_type);
        Services::Registry()->set('include', 'criteria_html_display_filter', false);

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
        Services::Registry()->set('include', 'template_view_id',
            Services::Registry()->get(CONFIGURATION_LITERAL, 'message_template_view_id'));
        Services::Registry()->set('include', 'wrap_view_id',
            Services::Registry()->get(CONFIGURATION_LITERAL, 'message_wrap_view_id'));

        Services::Registry()->set('include', 'criteria_display_view_on_no_results', 0);

        /** Template  */
        $this->viewHelper->get(Services::Registry()->get('include', 'template_view_id'), CATALOG_TYPE_TEMPLATE_VIEW_LITERAL);

        /** Wrap  */
        $this->viewHelper->get(Services::Registry()->get('include', 'wrap_view_id'), CATALOG_TYPE_WRAP_VIEW_LITERAL);

        /** Merge Configuration in */
        Services::Registry()->merge(CONFIGURATION_LITERAL, 'include', true);

        /** DBO  */
        Services::Registry()->set('include', 'model_type', DATA_OBJECT_LITERAL);
        Services::Registry()->set('include', 'model_name', 'Messages');
        Services::Registry()->set('include', 'model_query_object', QUERY_OBJECT_LIST);

        /** Cleanup */
        Services::Registry()->delete('include', 'item*');
        Services::Registry()->delete('include', 'list*');
        Services::Registry()->delete('include', 'form*');

        /** Sort */
        Services::Registry()->sort('include');

        return true;
    }
}
