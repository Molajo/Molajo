<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Extension\Includer;

use Molajo\Extension\Helpers;
use Molajo\Service\Services;
use Molajo\Extension\Includer;

defined('MOLAJO') or die;

/**
 * Wrap
 *
 * @package     Molajo
 * @subpackage  Includer
 * @since       1.0
 */
Class WrapIncluder extends Includer
{
    /**
     * __construct
     *
     * Class constructor.
     *
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
     *  setRenderCriteria
     *
     *  Retrieve default values, if not provided by extension
     *
     * @return bool
     * @since   1.0
     */
    protected function setRenderCriteria()
    {
        Services::Registry()->set('Parameters', 'extension_title', 'Wrap');

        $wrap_this = Services::Registry()->get('Parameters', 'wrap_model_query_object');
        if (substr(trim($wrap_this), 0, 1) == '{'
            && substr(trim($wrap_this), strlen(trim($wrap_this)) - 1, 1) == '}'
        ) {
            $value = trim(substr(trim($wrap_this), 1, strlen(trim($wrap_this)) - 2));
            $wrap_this = $value;
        }

        Services::Registry()->set('Parameters', 'display_view_on_no_results', 1);

        Services::Registry()->merge('Configuration', 'Parameters', true);

        /* Yes, this is done before, too. Get over it or fix it. */
        Services::Registry()->set('Parameters', 'model_name', 'Wraps');
        Services::Registry()->set('Parameters', 'model_type', 'Table');
        Services::Registry()->set('Parameters', 'model_query_object', $wrap_this);

        /** Wrap  */
        Helpers::WrapView()->get(Services::Registry()->get('Parameters', 'wrap_view_id'));

        Services::Registry()->delete('Parameters', 'item*');
        Services::Registry()->delete('Parameters', 'list*');
        Services::Registry()->delete('Parameters', 'form*');

        /** Sort */
        Services::Registry()->sort('Parameters');

        return;
    }

    /**
     * Loads Language Files for extension
     *
     * @return null
     * @since   1.0
     */
    protected function loadLanguage()
    {
        return $this;
    }

    /**
     * Loads Media CSS and JS files for Template and Wrap Views
     *
     * @return null
     * @since   1.0
     */
    protected function loadViewMedia()
    {
        $priority = Services::Registry()->get('Parameters', 'criteria_media_priority_other_extension', 400);

        $file_path = Services::Registry()->get('Parameters', 'wrap_view_path');
        $url_path = Services::Registry()->get('Parameters', 'wrap_view_path_url');

        Services::Document()->add_css_folder($file_path, $url_path, $priority);
        Services::Document()->add_js_folder($file_path, $url_path, $priority, 0);
        Services::Document()->add_js_folder($file_path, $url_path, $priority, 1);

        return $this;
    }
}
