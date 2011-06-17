<?php
/**
 * @package     Molajo
 * @subpackage  Editor Button Helper
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined( 'MOLAJO' ) or die( 'Restricted access' );

class MolajoEditorbuttonHelper extends JPlugin {
    
    /**
     * MolajoHelperEditorbutton::MolajoOnDisplay
     *
     * Content Component Plugin that applies text and URL functions to content object
     *
     * $name == formname
     *
     * @param	string		$name
     * @since	1.0
     */
    function checkCriteria ($name)
    {
        /** parameters **/

        $molajoSystemPlugin =& JPluginHelper::getPlugin('system', 'molajo');
        $systemParams = new JParameter($molajoSystemPlugin->params);

        $editorButtonsArray = new object;

        /** process buttons in left to right sequence **/
        $loadButtonArray = array();

        for ($i=1; $i < 99; $i++) {

            $buttonName = $systemParams->def('editor_button_sequence'.$i);
//echo 'Next Button'.' '.$buttonName .'<br />';
//echo 'Category Parameter'.' '.var_dump($systemParams->def('enable_editor_'.$buttonName.'_categories')) .'<br />';
//echo 'above'.'<br />';
            /** end of filter processing **/
            if ($buttonName == null) { break; }

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
                $categoryArray = $systemParams->def('enable_editor_'.$buttonName.'_categories', array());

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
                        $componentCategoriesModel = JModel::getInstance(ucfirst(JRequest::getCmd('default_view')).'Model', ucfirst(JRequest::getCmd('default_view')), array('ignore_request' => true));
                        if ($componentCategoriesModel->checkCategories ($categoryArray)) {
                            $categoryFound = true;
//        echo 'found category for component<br />';
                        }
                    }
                }

                /** build button if criteria met **/
                if ($categoryFound === true) {
                    require_once dirname(__FILE__).'/'.$buttonName.'/driver.php';
                    $className = 'MolajoEditorButton'.ucfirst($buttonName);
                    $buttonClass = new $className ();
                    $editorButtonsArray[] = $buttonClass->buildButton($name);
                }
            }
        }
        return $editorButtonsArray;
    }
}