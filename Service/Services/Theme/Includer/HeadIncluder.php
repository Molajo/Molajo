<?php
/**
 * @package    Molajo
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @license    MIT
 */
namespace Molajo\Service\Services\Theme\Includer;

use Molajo\Service\Services;
use Molajo\Service\Services\Theme\Includer;

defined('MOLAJO') or die;

/**
 * Head
 *
 * @package     Molajo
 * @subpackage  Includer
 * @since       1.0
 */
Class HeadIncluder extends Includer
{

    /**
     * Helpers
     *
     * @var    object
     * @since  1.0
     */
    protected $contentHelper;
    protected $extensionHelper;
    protected $themeHelper;
    protected $viewHelper;

    /**
     * @return  null
     * @since   1.0
     */
    public function __construct($include_name = null, $include_type = null)
    {
        Services::Registry()->set('include', 'extension_catalog_type_id', 0);
        parent::__construct($include_name, $include_type);
        Services::Registry()->set('include', 'criteria_html_display_filter', false);

        return;
    }

    /**
     *  Retrieve default values for Rendering, if not provided by extension
     *
     * @return  bool
     * @since   1.0
     */
    protected function setRenderCriteria()
    {
        Services::Registry()->set('include', 'criteria_display_view_on_no_results', 1);

        Services::Registry()->set('include', 'model_type', 'Assets');

        if ($this->type == 'defer') {

            if ((int)Services::Registry()->get('include', 'template_view_id', 0) == 0) {
                Services::Registry()->set(
                    'include',
                    'template_view_id',
                    Services::Application()->get('defer_template_view_id')
                );
            }

            if ((int)Services::Registry()->get('include', 'wrap_view_id', 0) == 0) {
                Services::Registry()->set(
                    'include',
                    'wrap_view_id',
                    Services::Application()->get('defer_wrap_view_id')
                );
            }

        } else {
            if ((int)Services::Registry()->get('include', 'template_view_id', 0) == 0) {
                Services::Registry()->set(
                    'include',
                    'template_view_id',
                    Services::Application()->get('head_template_view_id')
                );
            }
            if ((int)Services::Registry()->get('include', 'wrap_view_id', 0) == 0) {
                Services::Registry()->set(
                    'include',
                    'wrap_view_id',
                    Services::Application()->get('head_wrap_view_id')
                );
            }
        }

        /** Save existing parameters */
        $savedParameters = array();
        $temp            = Services::Registry()->getArray('include');

        if (is_array($temp) && count($temp) > 0) {
            foreach ($temp as $key => $value) {
                if (is_array($value)) {
                    $savedParameters[$key] = $value;

                } elseif ($value === 0 || trim($value) == '' || $value === null) {

                } else {
                    $savedParameters[$key] = $value;
                }
            }
        }

        /** Template  */
        $this->viewHelper->get(
            Services::Registry()->get('include', 'template_view_id'),
            CATALOG_TYPE_TEMPLATE_VIEW_LITERAL
        );

        /** Merge Parameters in (Pre-wrap) */
        if (is_array($savedParameters) && count($savedParameters) > 0) {
            foreach ($savedParameters as $key => $value) {
                Services::Registry()->set('include', $key, $value);
            }
        }
        /** Default Wrap if needed */
        $wrap_view_id = Services::Registry()->get('include', 'wrap_view_id');
        Services::Registry()->set(
            'include',
            'wrap_view_path_node',
            $this->extensionHelper->getExtensionNode((int)$wrap_view_id)
        );
        $wrap_view_title = Services::Registry()->get('include', 'wrap_view_path_node');

        Services::Registry()->set('include', 'wrap_view_title', $wrap_view_title);
        Services::Registry()->set(
            'include',
            'wrap_view_path',
            $this->extensionHelper->getPath($wrap_view_title, CATALOG_TYPE_WRAP_VIEW_LITERAL)
        );
        Services::Registry()->set(
            'include',
            'wrap_view_path_url',
            $this->extensionHelper->getPathURL($wrap_view_title, CATALOG_TYPE_WRAP_VIEW_LITERAL)
        );
        Services::Registry()->set(
            'include',
            'wrap_view_namespace',
            $this->extensionHelper->getNamespace($wrap_view_title, CATALOG_TYPE_WRAP_VIEW_LITERAL)
        );

        if (Services::Registry()->exists('include', 'wrap_view_role')) {
        } else {
            Services::Registry()->set('include', 'wrap_view_role', '');
        }
        if (Services::Registry()->exists('include', 'wrap_view_property')) {
        } else {
            Services::Registry()->set('include', 'wrap_view_property', '');
        }
        if (Services::Registry()->exists('include', 'wrap_view_header_level')) {
        } else {
            Services::Registry()->set('include', 'wrap_view_header_level', '');
        }
        if (Services::Registry()->exists('include', 'wrap_view_show_title')) {
        } else {
            Services::Registry()->set('include', 'wrap_view_show_title', '');
        }
        if (Services::Registry()->exists('include', 'wrap_view_show_subtitle')) {
        } else {
            Services::Registry()->set('include', 'wrap_view_show_subtitle', '');
        }
        Services::Registry()->delete('include', 'item*');
        Services::Registry()->delete('include', 'list*');
        Services::Registry()->delete('include', 'form*');
        Services::Registry()->delete('include', 'menuitem');

        Services::Registry()->sort('include');

        return true;
    }
}
