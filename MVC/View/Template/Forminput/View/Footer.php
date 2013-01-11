<?php
/**
 * Forminput Template View
 *
 * @package      Niambie
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
defined('NIAMBIE') or die;
if ($this->row->placeholder == '') {
} else {
    echo 'placeholder' . '="' . $this->row->placeholder . '" ';
} ?>>
