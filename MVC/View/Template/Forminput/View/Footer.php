<?php
/**
 * Forminput Template View
 *
 * @package      Molajo
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
defined('MOLAJO') or die;
if ($this->row->placeholder == '') {
} else {
    echo 'placeholder' . '="' . $this->row->placeholder . '" ';
} ?>>
