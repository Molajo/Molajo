<?php
/**
 * @version     $id: view
 * @package     Molajo
 * @subpackage  Multiple View
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/** begin table head **/
include dirname(__FILE__) . '/form/table_head_begin.php';
/** begin table head row **/
include dirname(__FILE__) . '/form/table_head_row_begin.php';
/** begin table head row column first **/
include dirname(__FILE__) . '/form/table_head_column_first.php';

/** loop thru header columns **/
$this->tempColumnCount = 1;
for ($i = 1; $i < 1000; $i++) {
    $this->tempColumnName = $this->parameters->def('config_manager_grid_column' . $i, 0);
    if ($this->tempColumnName == null) {
        break;
    } else if ($this->tempColumnName == '0') {
    } else {
        $this->tempColumnCount++;
        /** see if column exists, if not use default handler **/
        $filename = dirname(__FILE__) . '/form/' . strtolower('table_head_column_' . $this->tempColumnName) . '.php';
        $fileExists = JFile::exists($filename);
        if ($fileExists === false) {
            include dirname(__FILE__) . '/form/' . (strtolower('table_head_column_default.php'));
        } else {
            include $filename;
        }
    }
}
/** end of head row **/
include dirname(__FILE__) . '/form/table_head_row_end.php';
/** end of head **/
include dirname(__FILE__) . '/form/table_head_end.php';