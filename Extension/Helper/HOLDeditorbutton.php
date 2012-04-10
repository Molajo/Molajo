<?php
/**
 * @package   Molajo
 * @subpackage  Helper
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Editor Button Helper
 *
 * @package   Molajo
 * @subpackage  Editor Button Helper
 * @since       1.0
 */
class EditorbuttonHelper extends MolajoPluginHelper
{

    /**
     * ApplicationHelperEditorbutton::MolajoOnDisplay
     *
     * Content Component Plugin that applies text and URL functions to content object
     *
     * $name == formname
     *
     * @param    string        $name
     * @since    1.0
     */
    function checkCriteria($name)
    {
        /** parameters **/
        $molajoSystemPlugin =& MolajoPluginHelper::getPlugin('system', 'molajo');
        $systemParameters = new JParameter($molajoSystemPlugin->parameters);

        $editorButtonsArray = new object;

        /** process buttons in left to right sequence **/
        $loadButtonArray = array();

        for ($i = 1; $i < 99; $i++) {

            $buttonName = $systemParameters->def('editor_button_sequence' . $i);
            //echo 'Next Button'.' '.$buttonName .'<br />';
            //echo 'Category Parameter'.' '.var_dump($systemParameters->def('enable_editor_'.$buttonName.'_categories')) .'<br />';
            //echo 'above'.'<br />';
            /** end of filter processing **/
            if ($buttonName == null) {
                break;
            }

            /** configuration option not selected **/
            if ($buttonName == '') {

                /** do not repeat buttons **/
            } else if (in_array($buttonName, $loadButtonArray)) {

            } else {
                /** process selected button **/

                /** add to used button array **/
                $loadButtonArray[] = $buttonName;

                /** categories **/
                $categoryFound = false;
                $categoryArray = $systemParameters->def('enable_editor_' . $buttonName . '_categories', array());

                /** none **/
                if ($categoryFound === false && (is_array($categoryArray) === false || count($categoryArray) == 0 || $categoryArray[0] == 'none')) {

                } else {

                    /** all **/
                    if ($categoryFound === false && count($categoryArray) == 1 && $categoryArray[0] == 'all') {
                        $categoryFound = true;
                    }

                    /** current category **/
                    if ($categoryFound === false && in_array(JRequest::setVar('item_category'), $categoryArray)) {
                        $categoryFound = true;
                        //    echo 'found category for item'.' '.JRequest::setVar('item_category') .'<br />';
                    }

                    /** component  **/
                    if ($categoryFound === false) {
                        $componentCategoriesModel = JModel::getInstance(ucfirst(JRequest::getCmd('DefaultView')) . 'Model', ucfirst(JRequest::getCmd('DefaultView')), array('ignore_request' => true));
                        if ($componentCategoriesModel->checkCategories($categoryArray)) {
                            $categoryFound = true;
                            //        echo 'found category for component<br />';
                        }
                    }
                }

                /** build button if criteria met **/
                if ($categoryFound === true) {
                    require_once __DIR__ . '/' . $buttonName . '/driver.php';
                    $className = 'EditorButton' . ucfirst($buttonName);
                    $buttonClass = new $className ();
                    $editorButtonsArray[] = $buttonClass->buildButton($name);
                }
            }
        }
        return $editorButtonsArray;
    }
}
