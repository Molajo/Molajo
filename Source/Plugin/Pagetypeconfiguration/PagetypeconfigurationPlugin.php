<?php
/**
 * Page Type Configuration Plugin
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugin\Pagetypeconfiguration;

use CommonApi\Event\DisplayInterface;
use Molajo\Plugin\DisplayEventPlugin;

/**
 * Page Type Configuration Plugin
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
class PagetypeconfigurationPlugin extends DisplayEventPlugin implements DisplayInterface
{
    /**
     * Prepares Configuration Data
     *
     * @return  $this
     * @since    1.0
     */
    public function onBeforeRender()
    {
        if (strtolower($this->runtime_data->route->page_type) == 'configuration') {
        } else {
            return $this;
        }

        $controller_class_namespace = $this->controller_namespace;
        $controller                 = new $controller_class_namespace();
        $controller->getModelRegistry(
            $this->get('model_type', '', 'runtime_data'),
            $this->get('model_name', '', 'runtime_data'),
            1
        );
        $controller->set('get_customfields', 2);
        $controller->set('use_special_joins', 1);
        $controller->set('process_events', 1);

        /** Array - All Pages in Set
         * 2, {{Access,noformfields}}{{Editor,editor}}{{Grid,grid}}{{Form,form}}{{Item,item}}{{List,list}}
         */
        $temp  = $this->get('configuration_array', '', 'runtime_data');
        $pages = explode('{{', $temp);

        /** Determine Current Page of Set */
        $temp    = $this->get('request_filters', array(), 'runtime_data');
        $filters = explode(',', $temp);

        $page = 1;
        if ($filters == '' || count($filters) == 0) {
            $page = 1;
        } else {
            foreach ($filters as $x) {
                if (trim($x) == '') {
                } else {
                    $pair = explode(':', $x);
                    if (strtolower($pair[0]) == 'page') {
                        $page = (int)$pair[1];
                        break;
                    }
                }
            }
        }

        if ($page < count($pages)) {
        } else {
            $page = 1;
        }
        $page_number = $page;

        /** Resource Submenu: Links to various Form Pages (Tabs) - ex. Basic, Metadata, Fields, etc. */
        $pageArray = array();
        $i         = 0;
        foreach ($pages as $item) {

            if ($item == '') {
            } else {
                $i ++;
                $temp_row     = new \stdClass();
                $temp_row->id = $i;
                if ($i == $page_number) {
                    $temp_row->current = 1;
                } else {
                    $temp_row->current = 0;
                }

                $temp_row->id    = $i;
                $temp_row->title = substr($item, 0, strpos($item, ','));
                $temp_row->url   = $this->runtime_data->page->urls['page'] . '/page/' . $i;

                $pageArray[] = $temp_row;
            }
        }
        $this->runtime_data->page->menu['SectionSubmenu'] = $pageArray;

        /** Even tho links are created to each form page, generate Form for the current page, only */
        $current_page = '{{' . $pages[$page_number];

        /** Build Fieldsets and Fields */
        $form = Services::Form();

        /** Resource
         * 1. {{Basic,basic}}
         * 3. {{Fields,customfields,Customfields}}
         * 4. {{Editor,editor}}
         * 5. {{Grid,grid}}
         * 6. {{Form,form}}
         * 7. {{Item,item}}
         * 8. {{List,list}}
         */
        if ($page_number == 1 || $page_number == 3 || $page_number == 4
            || $page_number == 5 || $page_number == 6 || $page_number == 7
            || $page_number == 8
        ) {

            $pageFieldsets = $this->getResourceConfiguration($form, $current_page);
        }

        /** Set the View Model Parameters and Populate the Registry used as the Model */
        $current_page = $form->getPages(
            $pageFieldsets[0]->page_array,
            $pageFieldsets[0]->page_count
        );

        $controller->set('request_model_type', $this->get('model_type', '', 'runtime_data'));
        $controller->set('request_model_name', $this->get('model_name', '', 'runtime_data'));

        $controller->set('model_type', 'Dataobject');
        $controller->set('model_name', 'Primary');
        $controller->set('model_query_object', 'list');

        $controller->set('model_type', 'list');
        $controller->set('model_name', 'Primary');

        $this->registry->set(
            'Primary',
            'Data',
            $current_page
        );

        return $this;
    }

    /**
     * Prepares Configuration Data
     *
     * @param   string $form
     * @param   string $current_page
     *
     * @return  $this
     * @since   1.0
     */
    protected function getResourceConfiguration($form, $current_page)
    {
        $this->content_helper->getResourceExtensionParameters(
            (int)$this->runtime_data->criteria_extension_instance_id
        );

        /** Set Input */
        $form->set('namespace', strtolower($this->runtime_data->route->page_type));

        $form->set('model_type', $this->get('model_type', '', 'runtime_data'));
        $form->set('model_name', $this->get('model_name', '', 'runtime_data'));
        $form->set(
            'model_registry_name',
            ucfirst(strtolower($this->get('model_name', '', 'runtime_data'))) . ucfirst(
                strtolower($this->get('model_type', '', 'runtime_data'))
            )
        );

        $form->set('extension_instance_id', $this->get('criteria_extension_instance_id'));

        $form->set('data', array());

        /** Parameters */
        $runtime_data = $this->registry->getArray('ResourceSystemParameters');
        $array2       = $this->registry->getArray('Parameters');

        foreach ($array2 as $key => $value) {
            if (substr($key, 0, strlen('Configuration')) == 'Configuration') {
                $runtime_data[$key] = $value;
            }
        }

        $form->set('Parameters', $runtime_data);
        $form->set('parameter_fields', $this->registry->get('ResourceSystem', 'Parameters'));

        /** Metadata */
        $form->set('Metadata', $this->registry->getArray('ResourceSystemMetadata'));
        $form->set('metadata_fields', $this->registry->get('ResourceSystem', 'Metadata'));

        /** Customfields */
        $form->set('Customfields', $this->registry->getArray('ResourceSystemCustomfields'));
        $form->set('customfields_fields', $this->registry->get('ResourceSystem', 'Customfields'));

        /** Build Fieldsets and Fields */
        return $form->execute($current_page);
    }
}
