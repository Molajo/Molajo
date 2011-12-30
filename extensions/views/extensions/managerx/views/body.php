<?php
/**
 * @version     $id: view
 * @package     Molajo
 * @subpackage  Multiple View
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/** loop through columns **/
for ($i = 1; $i < 1000; $i++) {

    $this->tempColumnName = $this->parameters->get('config_manager_grid_column' . $i);

    if ($this->tempColumnName == null) {
        break;
    } else if ($this->tempColumnName == '0') {
    } else {

        /** custom column file **/
        $filename = dirname(__FILE__) . '/form/' . strtolower('table_body_row_column_' . $this->tempColumnName) . '.php';
        $fileExists = JFile::exists($filename);

        if ($fileExists === false) {

            /** custom field rendering **/
            $nameClassName = 'MolajoField' . ucfirst($this->tempColumnName);
            $mf = new MolajoField ();
            $mf->getClass($this->tempColumnName, false);

            if (class_exists($nameClassName)) {
                $MolajoFieldClass = new $nameClassName();
                if (method_exists($MolajoFieldClass, 'render')) {
                    $mfc = new $MolajoFieldClass ();
                    $results = $mfc->render($view = 'admin', $this->row, $this->row->rowCount);
                    if ($results == false) {
                    } else {
                        $this->render = $results;
                    }
                }
            }

            /** default field rendering **/
            if ($results == false) {
                $this->render['class'] = '';
                $this->render['valign'] = 'top';
                $this->render['align'] = 'left';
                $this->render['sortable'] = true;
                $this->render['checkbox'] = false;
                $this->render['data_type'] = true;
                $this->render['column_name'] = $this->tempColumnName;
                $columnName = $this->tempColumnName;
                $this->render['print_value'] = $this->row->$columnName;
                $this->render['link_value'] = false;
            }
            /** render **/
            include dirname(__FILE__) . '/form/table_body_row_column_default.php';
        } else {
            /** custom column file **/
            include $filename;
        }
    }
}