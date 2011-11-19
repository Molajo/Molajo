<?php
/**
 * @version     $id: custom.php
 * @package     Molajo
 * @subpackage  Twig Display Example
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

foreach ($this->rowset as $this->row) {
    include $this->layoutFolder.'/layouts/top.php';
}