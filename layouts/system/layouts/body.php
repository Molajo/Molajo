<?php
/**
 * @version     $id: body.php
 * @package     Molajo
 * @subpackage  System Layout
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;
foreach ($this->row as $name=>$value) {
    echo '<td align="left" valign="top">'.$value.'</td>';
}