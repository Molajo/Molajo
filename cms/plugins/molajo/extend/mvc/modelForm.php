<?php
/**
 * @package     Molajo
 * @subpackage  Extend
 * @copyright   Copyright (C) 2010-2011 Amy Stephen. All rights reserved. See http://molajo.org/copyright
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

jimport('joomla.form.form');

/**
 * modelForm
 *
 * Interacts with JForm to add and retrieve form fields and values
 *
 * @package    Content
 * @subpackage    Extend
 * @version    1.6
 */
class modelForm
{

    /**
     * getRequestForm
     *
     * Loads Request Form from JRequest
     *
     * @return object form
     */
    public function getRequestForm()
    {
        return JRequest::getVar('jform', array(), 'post', 'array');
    }

    /**
     * getCustomFields
     *
     * Retrieves a list of Custom Fields for a specific Content Type
     *
     * @param string $contentType
     * @param string $path
     *
     * @return object Custom Fields for the specified Content Type
     */
    public function getCustomFields($contentType, $path = null)
    {
        /** default path **/
        if ($path == null) {
            $path = MOLAJO_EXTEND_ROOT . '/contenttypes';
        }

        /** add path for JForm **/
        JForm::addFormPath($path);

        /** initialise **/
        $customFields = array();
        $nameInd = 0;

        /** load content type form **/
        $contentTypeForm = new JForm ($contentType);
        $contentTypeForm->loadFile($contentType, false);

        /** retrieve custom field names and add to array **/
        $contentTypeFieldSets = $contentTypeForm->getFieldsets();
        foreach ($contentTypeFieldSets as $contentTypeFieldSet => $nameSet) :

            foreach ($contentTypeForm->getFieldset($contentTypeFieldSet) as $customField) :

                $customFields [$nameInd]->name = substr($customField->name, (stripos($customField->name, '[') + 1), (stripos($customField->name, ']') - stripos($customField->name, '[') - 1));

                if (isset($customField->multiple) && $customField->multiple == true) {
                    $customFields[$nameInd]->multiple = 1;
                } else {
                    $customFields[$nameInd]->multiple = 0;
                }
                $nameInd++;

            endforeach;
        endforeach;

        /** success **/
        return $customFields;
    }

    /**
     * addCustomFieldsForm
     *
     * Loads Custom Fields into the Component Form Object for a specific Content Type
     *
     * @param string $contentType
     * @param object $form
     * @param string $path
     *
     * @return boolean
     */
    public function addCustomFieldsForm($contentType, $form, $path = null)
    {
        /** load language files **/
        $language = MolajoFactory::getLanguage();
        $language->load('plg_system_extend_' . $contentType, MOLAJO_EXTEND_ROOT, $language->getDefault(), true, true);

        /** default path **/
        if ($path == null) {
            $path = MOLAJO_EXTEND_ROOT . '/contenttypes';
        }

        /** custom fields folder **/
        JForm::addFormPath($path);

        /** load custom fields into component form object **/
        $form->loadFile($contentType, false);

        /** complete **/
        return true;
    }
}