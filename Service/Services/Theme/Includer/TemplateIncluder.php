<?php
/**
 * Theme Service Template Includer
 *
 * @package    Niambie
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    MIT
 */
namespace Molajo\Service\Services\Theme\Includer;

use Molajo\Service\Services\Theme\Includer;

defined('NIAMBIE') or die;

/**
 * The Template Includer prepares parameter values needed by the MVC to render the requested
 * Template and Wrap for the specific <include:type name=statement/> parsed by the Theme Service.
 * Once all parameter values have been determined, the data is passed to the MVC for rendering and
 * the rendered result is passed back through the Template Includer to the Theme Service.
 *
 * @author       Amy Stephen
 * @license      MIT
 * @copyright    2012 Amy Stephen. All rights reserved.
 * @since        1.0
 */
Class TemplateIncluder extends Includer
{
    /**
     * Uses Attributes and Extension Definitions to:
     *
     * 1. Determine which Template has been requested
     * 2. Set Parameter Values for the Template, Wrap, and Model
     *
     * @return  bool
     * @since   1.0
     */
    protected function setRenderCriteria()
    {
        // get id for name - or name for id
        if ($this->get('name', null) === null) {
            throw new \Exception ('TemplateIncluder: No Name provided for Template Include');
        }

        if (is_numeric($this->get('name')) {
            $template_id = $this->extensionHelper->getId(CATALOG_TYPE_TEMPLATE_VIEW, $this->get('name'));
        }

        $template_id = (int)Services::Registry()->get('parameters', 'template_view_id');

        if ((int)$template_id == 0) {
            $template_title = Services::Registry()->get('parameters', 'template_view_path_node');
            if (trim($template_title) == '') {
            } else {
                $template_id = $this->extensionHelper
                    ->getId(CATALOG_TYPE_TEMPLATE_VIEW, $template_title);
                Services::Registry()->set('include', 'template_view_id', $template_id);
            }
        }

        if ((int)$template_id == 0) {
            $template_id = $this->viewHelper->getDefault(CATALOG_TYPE_TEMPLATE_VIEW_LITERAL);
            Services::Registry()->set('include', 'template_view_id', $template_id);
        }

        if ((int)$template_id == 0) {
            return false;
        }

        $this->viewHelper->get($template_id, CATALOG_TYPE_TEMPLATE_VIEW_LITERAL);

        if (is_array($saveTemplate) && count($saveTemplate) > 0) {
            foreach ($saveTemplate as $key => $value) {
                Services::Registry()->set('include', $key, $value);
            }
        }

        // extract parameters and populate $this->set('thing', value, 'parameters);
        // loop thru parameter names and overaly with matching attributes

        // get model
        $fields = Services::Registry()->get(CONFIGURATION_LITERAL, 'application*');
        if (count($fields) === 0 || $fields === false) {
        } else {
            foreach ($fields as $key => $value) {
                Services::Registry()->set('include', $key, $value);
            }
        }

        $fields = Services::Registry()->getArray('Tempattributes');
        if (count($fields) === 0 || $fields === false) {
        } else {
            foreach ($fields as $key => $value) {
                Services::Registry()->set('include', $key, $value);
            }
        }

        $message = 'Includer: Render Criteria '
            . 'Name ' . strtolower($this->name)
            . ' Type ' . $this->type
            . ' Template ' . Services::Registry()->get('include', 'template_view_title')
            . ' Model Type ' . Services::Registry()->get('include', 'model_type')
            . ' Model Name ' . Services::Registry()->get('include', 'model_name');

        Services::Profiler()->set($message, PROFILER_RENDERING, VERBOSE);

        return true;
    }

    /**
     * Loads Media CSS and JS files for Template and Template Views
     *
     * @return  object
     * @since   1.0
     */
    protected function loadViewMedia()
    {
        if ($this->type == 'asset' || $this->type == METADATA_LITERAL) {
            return $this;
        }

        $priority = Services::Registry()->get('include', 'criteria_media_priority_other_extension', 400);

        $file_path = Services::Registry()->get('include', 'template_view_path');
        $url_path = Services::Registry()->get('include', 'template_view_path_url');

        Services::Asset($this->assets)->addCssFolder($file_path, $url_path, $priority);
        Services::Asset($this->assets)->addJsFolder($file_path, $url_path, $priority, 0);
        Services::Asset($this->assets)->addJsFolder($file_path, $url_path, $priority, 1);

        return $this;
    }
}
