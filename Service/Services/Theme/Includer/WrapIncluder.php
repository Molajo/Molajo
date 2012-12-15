<?php
/**
 * @package    Niambie
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Service\Services\Theme\Includer;

use Molajo\Service\Services;
use Molajo\Service\Services\Theme\Includer;

defined('NIAMBIE') or die;

/**
 * Wrap
 *
 * @package     Niambie
 * @subpackage  Includer
 * @since       1.0
 */
Class WrapIncluder extends Includer
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
     * Loads Media CSS and JS files for Template and Wrap Views
     *
     * @return null
     * @since   1.0
     */
    protected function loadViewMedia()
    {
        $priority = Services::Registry()->get('include', 'criteria_media_priority_other_extension', 400);

        $file_path = Services::Registry()->get('include', 'wrap_view_path');
        $url_path = Services::Registry()->get('include', 'wrap_view_path_url');

        Services::Asset()->addCssFolder($file_path, $url_path, $priority);
        Services::Asset()->addJsFolder($file_path, $url_path, $priority, 0);
        Services::Asset()->addJsFolder($file_path, $url_path, $priority, 1);

        return $this;
    }
}
