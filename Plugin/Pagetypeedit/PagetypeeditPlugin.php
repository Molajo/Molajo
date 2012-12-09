<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Pagetypeedit;

use Molajo\Plugin\Plugin\Plugin;
use Molajo\Service\Services;
use Molajo\Helpers;

defined('MOLAJO') or die;

/**
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class PagetypeeditPlugin extends Plugin
{
    /**
     * Prepares Configuration Data
     *
     * @return   boolean
     * @since    1.0
     */
    public function onBeforeParse()
    {
        if (strtolower($this->get('page_type', '', 'parameters')) == 'edit') {
        } else {
            return true;
        }

        $resource_model_type = $this->get('model_type', '', 'parameters');
        $resource_model_name = $this->get('model_name', '', 'parameters');

        //todo - submenu
        Services::Registry()->set(PAGE_LITERAL, 'SectionSubmenu', array());

        /** Form Service */
        $form = Services::Form();

        /** Set Input */
        $form->set('namespace', strtolower($this->get('page_type', '', 'parameters')));

        $form->set('model_type', $this->get('model_type', '', 'parameters'));
        $form->set('model_name', $this->get('model_name', '', 'parameters'));
        $form->set('model_registry_name',
            ucfirst(strtolower($this->get('model_name', '', 'parameters'))) . ucfirst(strtolower($this->get('model_type', '', 'parameters')))
        );

        $form->set('extension_instance_id', $this->get('criteria_extension_instance_id'));

        $form->set('data', Services::Registry()->get(DATA_OBJECT_LITERAL, PRIMARY_LITERAL));

        /** Parameters */
        $form->set(PARAMETERS_LITERAL, Services::Registry()->getArray('ResourcesSystemParameters'));
        $form->set('parameter_fields', Services::Registry()->get('ResourcesSystem', PARAMETERS_LITERAL));

        /** Metadata */
        $form->set(METADATA_LITERAL, Services::Registry()->getArray('ResourcesSystemMetadata'));
        $form->set('metadata_fields', Services::Registry()->get('ResourcesSystem', METADATA_LITERAL));

        /** Customfields */
        $form->set(CUSTOMFIELDS_LITERAL, Services::Registry()->getArray('ResourcesSystemCustomfields'));
        $form->set('customfields_fields', Services::Registry()->get('ResourcesSystem', CUSTOMFIELDS_LITERAL));
echo Services::Registry()->get('ResourcesSystemParameters', 'edit_array');

        /** Build Fieldsets and Fields */
        $pageFieldsets = $form->execute(Services::Registry()->get('ResourcesSystemParameters', 'edit_array'));

        /** Set the View Model Parameters and Populate the Registry used as the Model */
        $current_page = $form->getPages(
            $pageFieldsets[0]->page_array,
            $pageFieldsets[0]->page_count
        );

        $controller->set('request_model_type', $this->get('model_type', '', 'parameters'), 'model_registry');
        $controller->set('request_model_name', $this->get('model_name', '', 'parameters'), 'model_registry');

        $controller->set('model_type', DATA_OBJECT_LITERAL, 'model_registry');
        $controller->set('model_name', PRIMARY_LITERAL, 'model_registry');
        $controller->set('model_query_object', QUERY_OBJECT_LIST, 'model_registry');

        $controller->set('model_type', QUERY_OBJECT_LIST, 'model_registry');
        $controller->set('model_name', PRIMARY_LITERAL, 'model_registry');

        Services::Registry()->set(
            PRIMARY_LITERAL,
            DATA_LITERAL,
            $current_page
        );

        return true;
    }
}
