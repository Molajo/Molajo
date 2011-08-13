<?php
/**
 * @version     $id: layout
 * @package     Molajo
 * @subpackage  Single View
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/** loop through columns **/
$count = 0;
for ($i=1; $i < 1000; $i++) {
    $this->tempColumnName = $this->params->def($this->tempSection.$i);

    if ($this->tempColumnName == null) {
        break;
    } else if ($this->tempColumnName == '0') {
    } else {
        if ($count == 0) {
            include dirname(__FILE__) . '/form/' . 'section_begin.php';
        }
        $count++;
        /** see if column exists, if not use default handler **/
        $filename = dirname(__FILE__).'/form/'.strtolower('edit_'.$this->tempColumnName).'.php';
        $fileExists = JFile::exists($filename);

        if ($fileExists === false) {
            if ($this->tempSection == 'config_manager_editor_primary_column') {
                include dirname(__FILE__) . '/form/' . 'primary.php';
            } else {
                include dirname(__FILE__) . '/form/' . 'standard_list_item.php';
            }
        } else {
            include $filename;
        }
    }
}
if ($count > 0) {
    include dirname(__FILE__) . '/form/' . 'section_end.php';
}