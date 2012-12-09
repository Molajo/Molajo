<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Service\Services\Theme\Includer;

use Molajo\Service\Services;
use Molajo\Service\Services\Theme\Includer;

defined('MOLAJO') or die;

/**
 * Template
 *
 * @package     Molajo
 * @subpackage  Includer
 * @since       1.0
 */
Class TemplateIncluder extends Includer
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

        if (file_exists(Services::Registry()->get(PARAMETERS_LITERAL, 'theme_path_include'))) {
        } else {
            Services::Error()->set(500, 'Theme not found');
            return false;
        }
        $class = 'Molajo\\Service\\Services\\Theme\\Includer\\ThemeIncluder';

        if (class_exists($class)) {
            $rc = new $class (THEME_LITERAL);
            $results = $rc->process();
        } else {
            throw new \Exception('Parse: Instantiating ThemeIncluder Class failed');
        }







        $first = true;

        Services::Profiler()->set(
            'ParseService renderLoop Parse Body using Theme:'
                . Services::Registry()->get(PARAMETERS_LITERAL, 'theme_path_include')
                . ' and Page View: '
                . Services::Registry()->get(PARAMETERS_LITERAL, 'page_view_path_include'),
            PROFILER_RENDERING
        );

        ob_start();
        require Services::Registry()->get(PARAMETERS_LITERAL, 'theme_path_include');
        $this->rendered_output = ob_get_contents();
        ob_end_clean();

        return $this;
    }

    /**
     * Loads Media CSS and JS files for Template and Template Views
     *
     * @return object
     * @since   1.0
     */
    protected function loadViewMedia()
    {
        if ($this->type == 'asset' || $this->type == METADATA_LITERAL) {
            return $this;
        }

        $priority = Services::Registry()->get('parameters', 'criteria_media_priority_other_extension', 400);

        $file_path = Services::Registry()->get('parameters', 'template_view_path');
        $url_path = Services::Registry()->get('parameters', 'template_view_path_url');

        Services::Asset()->addCssFolder($file_path, $url_path, $priority);
        Services::Asset()->addJsFolder($file_path, $url_path, $priority, 0);
        Services::Asset()->addJsFolder($file_path, $url_path, $priority, 1);

        return $this;
    }
}
