<?php
/**
 * @package     Molajo
 * @subpackage  View
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Molajo View 
 *
 * @package	    Molajo
 * @subpackage	View
 * @since	    1.0
 */
class MolajoView extends MolajoLayout
{

    /**
     * display
     *
     * View for Display View that uses no forms
     *
     * @param null $tpl
     * 
     * @return bool
     */
    public function display($tpl = null)
    {
        /** @var $this->app */
        $this->app = MolajoFactory::getApplication();
        
        /** @var $this->system */
        $this->system = MolajoFactory::getConfig();

        /** @var $this->document */
        $this->document = MolajoFactory::getDocument();

        /** @var $this->user */
        $this->user = MolajoFactory::getUser();

        /** Set Page Meta */
//		$pageModel = JModel::getInstance('Page', 'MolajoModel', array('ignore_request' => true));
//		$pageModel->setState('params', $this->app->getParams());
    }

    /**
     * getColumns
     *
     * Displays system variable names and values
     *
     * $this->params
     *
     * $this->getColumns ('system');
     *
     * @param  $type
     * @return void
     */
    protected function getColumns ($type, $layout='system')
    {
        /** @var $this->rowset */
        $this->rowset = array();

        /** @var $registry */
        $registry = new JRegistry();

        /** @var $tempIndex */
        $columnIndex = 0;

        if ($type == 'user') {
            foreach ($this->$type as $column=>$value) {
                if ($column == 'params') {
                    $registry->loadJSON($value);
                    $options = $registry->toArray();
                    $this->getColumnsJSONArray ($type, $options);
                } else {
                    $this->getColumnsFormatting ($type, $column, $value);
                }
            }

        } else if ($type == 'system') {
                $registry->loadJSON($this->$type);
                $options = $registry->toArray();
                $this->getColumnsJSONArray ($type, $options);

        } else {
            return false;
        }

        /**
         *  Display Results
         */
        $this->layout_path = $this->findPath($layout);
        echo $this->renderLayout ($this->layout_path, 'system');

        return;
    }

    /**
     * getColumnsJSONArray
     *
     * Process Array from converted JSON Object
     *
     * @param  $type
     * @param  $options
     * @return void
     */
    private function getColumnsJSONArray ($type, $options)
    {
        foreach ($options as $column=>$value) {
            $this->getColumnsFormatting ($type, $column, $value);
        }
    }

    /**
     * getColumnsFormatting
     *
     * Process Columns from Object
     *
     * @param  $type
     * @param  $column
     * @param  $value
     * @return void
     */
    private function getColumnsFormatting ($type, $column, $value, $columnIndex)
    {
        $this->rowset[$columnIndex]['column'] = $column;

        if (is_array($value)) {
            $this->rowset[$columnIndex]['syntax'] = '$list = $this->'.$type."->get('".$column."');<br />";
            $this->rowset[$columnIndex]['syntax'] .= 'foreach ($list as $item=>$itemValue) { <br />';
            $this->rowset[$columnIndex]['syntax'] .= '&nbsp;&nbsp;&nbsp;&nbsp;echo $item.'."': '".'.$itemValue;';
            $this->rowset[$columnIndex]['syntax'] .= '<br />'.'}';
            $temp = '';
            $list = $this->$type->get($column);
            foreach ($list as $item=>$itemValue) {
                $temp .= $item.': '.$itemValue.'<br />';
            }
            $this->rowset[$columnIndex]['value'] = $temp;
        } else {
            $this->rowset[$columnIndex]['syntax'] = 'echo $this->'.$type."->get('".$column."');  ";
            $this->rowset[$columnIndex]['value'] = $value;
        }

        $columnIndex++;
    }
}