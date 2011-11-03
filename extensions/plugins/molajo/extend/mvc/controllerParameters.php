<?php
/**
 * @package     Molajo
 * @subpackage  Extend 
 * @copyright   Copyright (C) 2010-2011 Amy Stephen. All rights reserved. See http://molajo.org/copyright
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * extendControllerParameters::verifyContentType
 *
 * extendController::initiation invokes the extendControllerParameters methods
 *
 *  verifyGlobal - compare properties of the current request to the global Custom Fields Parameters to see if processing is required
 *  verifyContentType - compare properties of the current request to the Content Type Custom Fields Parameters to see if processing is required
 *  display - create all form parameters needed for Content Type and load into the Request Form Object for Display
 *  save - Update the Extensions Params field for the Custom Field Plugin Parameters
 *
 */
class extendControllerParameters {

    /**
     * verifyParametersGlobal
     *
     * Verifies Global Parameters
     *
     * @return boolean
     */
    public function verifyGlobal ($task, $component_option, $category, $form)
    {
        $app = MolajoFactory::getApplication();
        $systemPlugin =& MolajoPluginHelper::getPlugin('system', 'extend');
        $fieldParams = new JParameter($systemPlugin->params);

        /** client **/
        if ($fieldParams->def('global_restriction_client', '') == '') {
        } else if ($fieldParams->def('global_restriction_client', '') == MOLAJO_APPLICATION_ID) {
        } else {
            return false;
        }

        /** component **/
        if (is_array($fieldParams->get('global_restriction_component'))) {
            if (implode('', $fieldParams->get('global_restriction_component')) == '') {
            } else if (in_array($component_option, $fieldParams->get('global_restriction_component'))) {
            } else {
                return false;
            }
        }

        /** category **/
        if ($category == null) {      /** new content and some components do not use category **/
        } else {
            if ($task == 'display' || $task == 'edit') {
                if (is_array($fieldParams->get('global_restriction_category'))) {
                    if (implode('', $fieldParams->get('global_restriction_category')) == '') {
                    } else if (in_array($category, $fieldParams->get('global_restriction_category'))) {
                    } else {
                        return false;
                    }
                }
            }
        }

        /** named forms **/
        if ($task == 'edit' || $task == 'save') {
            if (is_array($fieldParams->get('global_restriction_forms'))) {
                if (implode('', $fieldParams->get('global_restriction_forms')) == '') {
                } else if (in_array($form->getName(), $fieldParams->get('global_restriction_forms'))) {
                } else {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * verifyParametersContentType
     *
     * Verifies Parameters for the Content Type
     *
     * @param string $contentType
     *
     * @return boolean
     */
    public function verifyContentType ($task, $component_option, $category, $form, $contentType)
    {
        $user = MolajoFactory::getUser();
        $db = MolajoFactory::getDbo();
        $app = MolajoFactory::getApplication();
        $systemPlugin =& MolajoPluginHelper::getPlugin('system', 'extend');
        $fieldParams = new JParameter($systemPlugin->params);

        /** enabled **/
        if ((int) $fieldParams->def($contentType.'_enable', 0) == 0) {
            return false;
        }

        /** client **/
        if ($fieldParams->def($contentType.'_client_criteria', '') == '') {
        } else if ($fieldParams->def($contentType.'_client_criteria', '') == MOLAJO_APPLICATION_ID) {
        } else {
            return false;
        }

        /** component **/
        if (is_array($fieldParams->get($contentType.'_component_criteria'))) {
            if (implode('', $fieldParams->get($contentType.'_component_criteria')) == '') {
            } else if (in_array($component_option, $fieldParams->get($contentType.'_component_criteria'))) {
            } else {
                return false;
            }
        }

        /** category **/
        if (is_array($fieldParams->get($contentType.'_category_criteria'))) {
            if (implode('', $fieldParams->get($contentType.'_category_criteria')) == '') {
            } else if ($category == null) {
                return false;
            } else if (in_array($category, $fieldParams->get($contentType.'_category_criteria'))) {
            } else {
                return false;
            }
        }

        /** named forms **/
        if ($task == 'edit' || $task == 'save') {
            if (is_array($fieldParams->get($contentType.'_forms_criteria'))) {
                if (implode('', $fieldParams->get($contentType.'_forms_criteria')) == '') {
                } else if (in_array($form->getName(), $fieldParams->get($contentType.'_forms_criteria'))) {
                } else {
                    return false;
                }
            }
        }

        /** menu items **/
        if ($app->getMenu()->getActive() == null) {
        } else {
            if (is_array($fieldParams->get($contentType.'_menu_item_criteria'))) {
                if (implode('', $fieldParams->get($contentType.'_menu_item_criteria')) == '') {
                } else if (in_array($app->getMenu()->getActive(), $fieldParams->get($contentType.'_menu_item_criteria'))) {
                } else {
                    return false;
                }
            }
        }

        /** published state and date checks **/
        if (!(int) $fieldParams->def($contentType.'_published_criteria', 1) == 1) { return false; }
        $publishUp = $fieldParams->def($contentType.'_start_publishing_datetime_criteria', '');
        $publishDown = $fieldParams->def($contentType.'_stop_publishing_datetime_criteria', '');
        if ($publishUp == '' && $publishDown == '') {
        } else if (date('Y-m-d') > $publishUp && date('Y-m-d') < $publishDown) {
        } else {
            return false;
        }

        /** access **/
        $acl	= new MolajoACL();
        $groups = $acl->getList('Viewaccess');
        if (!in_array($fieldParams->def($contentType.'_access_criteria', 0), $groups)) {
            return false;
        }

        /** language **/
        if ($fieldParams->def($contentType.'_language_criteria', '*') == '*') {
        } else {
        }

        return true;
    }

    /**
     * display
     *
     * Special purpose controller for administrative functions for
     *      the System Extend Plugin when the user is editing the Plugin Manager
     *
     * This function reads the $contentTypeFilenames array containing the names of the
     *      files from the contenttype folder. Each of these are called "Content Types."
     *      Using a pattern file, the Parameter XML is automatically created for each
     *      Content Type and appended into the Plugin Form Object
     *
     * @param object $contentTypeFilenames - list of contentype folder filenames
     * @param form $form - MolajoPlugin Parameter Form Object from the Plugin Component
     * @param stromg $path - path to the pattern parameter file
     *
     */
    public function display ($contentTypeFilenames, $form, $path=null)
    {

        /** language files per content type must be loaded **/
        $language = MolajoFactory::getLanguage();
        if ($path == null) {
            $path = MOLAJO_EXTEND_ROOT.'/parameters/';
        }

        /** read pattern file into buffer **/
        $pattern = JFile::read($path.'__________.xml');

        /** load form parameters and language files for each content type **/
        foreach ($contentTypeFilenames as $contentTypeFilename) {
            $contentType = substr($contentTypeFilename, 0, stripos($contentTypeFilename,'.xml'));
            $language->load('plg_system_extend_'.$contentType, MOLAJO_EXTEND_ROOT, $language->getDefault(), true, true);
            $output = str_replace('XYZXYZ', strtoupper($contentType), $pattern);
            $output = str_replace('xyzxyz', strtolower($contentType), $output);
            $form->load($output, false);
        }
        return;
    }

    /**
     * save
     *
     * Special purpose controller for administrative functions for
     *      the System Extend Plugin when the user is editing the Plugin Manager
     *
     * This function reads the names of the files in the $contentTypeFilenames folder
     *      and, using a pattern file, automatically creates the Parameter XML
     *      and loads the Custom Field Form Data back into the Form Object
     *      Uses JForm to filter and validate the content
     *      Retrieves the params field saved by Molajo and appends in the Custom Field Params
     *      updating the Extensions table
     *
     * This function creates itself.
     *
     * @param object $contentTypeFilenames - list of contentype folder filenames
     * @param form $form - MolajoPlugin Parameter Form Object
     * @param stromg $path - path to the pattern parameter file
     *
     */
    public function save ($contentTypeFilenames, $path=null)
    {
        /** temporary form for working purposes **/
        $contentTypeForm = new JForm ('params');

        /** initialization **/
        $language = MolajoFactory::getLanguage();
        if ($path == null) {
            $path = MOLAJO_EXTEND_ROOT.'/parameters/';
        }
        $pattern = JFile::read($path.'__________.xml');

        /** load form parameters and language files for each content type **/
        foreach ($contentTypeFilenames as $contentTypeFilename) :
            $contentType = substr($contentTypeFilename, 0, stripos($contentTypeFilename,'.xml'));
            $language->load('plg_system_extend_'.$contentType, MOLAJO_EXTEND_ROOT, $language->getDefault(), true, true);
            $output = str_replace('XYZXYZ', strtoupper($contentType), $pattern);
            $output = str_replace('xyzxyz', strtolower($contentType), $output);
            $contentTypeForm->load($output, false);
        endforeach;

        /** load data from request back into form **/
        $formdata = modelForm::getRequestForm ();
        if ($formdata == false) {
            return false;
        }

        /** filter **/
        $filteredData = $contentTypeForm->filter($formdata);
        /** validate **/
        if (!$contentTypeForm->validate($filteredData)) { return false; }

        /** retrieve portion of params saved normally **/
        $p = modelParameter::getData ();
        if ($p == false) {
            return false;
        }

        /** merge "normal" params with custom fields params **/
	$existingParams = new JRegistry($p);
	$contenttypeParams = new JRegistry($filteredData['params']);
        $newParams = substr($existingParams, 0, strlen($existingParams) - 1).','.substr($contenttypeParams, 1, strlen($contenttypeParams) - 1);

        /** update extensions params field for full value **/
        return modelParameter::updateData ($newParams);
    }
}