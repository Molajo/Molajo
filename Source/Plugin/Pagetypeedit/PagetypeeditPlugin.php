<?php
/**
 * Page Type Edit Plugin
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugin\Pagetypeedit;

use CommonApi\Event\DisplayInterface;
use Molajo\Plugin\DisplayEventPlugin;

/**
 * Page Type Edit Plugin
 *
 * @package     Molajo
 * @license     http://www.opensource.org/licenses/mit-license.html MIT License
 * @since       1.0
 */
class PagetypeeditPlugin extends DisplayEventPlugin implements DisplayInterface
{
    /**
     * Prepares Configuration Data
     *
     * @return  $this
     * @since   1.0
     */
    public function onBeforeRender()
    {
        if (strtolower($this->runtime_data->route->page_type) == 'edit') {
        } else {
            return $this;
        }

        $resource_model_type = $this->get('model_type', '', 'runtime_data');
        $resource_model_name = $this->get('model_name', '', 'runtime_data');

        //@todo - submenu
        $this->runtime_data->page->menu['SectionSubmenu'] = array();

        /** Form Service */
        $form = Services::Form();

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

        $form->set('data', $this->registry->get('Dataobject', 'Primary'));

        /** Parameters */
        $form->set('Parameters', $this->registry->getArray('ResourceSystemParameters'));
        $form->set('parameter_fields', $this->registry->get('ResourceSystem', 'Parameters'));

        /** Metadata */
        $form->set('Metadata', $this->registry->getArray('ResourceSystemMetadata'));
        $form->set('metadata_fields', $this->registry->get('ResourceSystem', 'Metadata'));

        /** Customfields */
        $form->set('Customfields', $this->registry->getArray('ResourceSystemCustomfields'));
        $form->set('customfields_fields', $this->registry->get('ResourceSystem', 'Customfields'));
        echo $this->registry->get('ResourceSystemParameters', 'edit_array');

        /** Build Fieldsets and Fields */
        $pageFieldsets = $form->execute($this->registry->get('ResourceSystemParameters', 'edit_array'));

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
}
