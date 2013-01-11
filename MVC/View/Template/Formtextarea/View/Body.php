<?php
/**
 * Form Text Area
 *
 * @package      Niambie
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
defined('NIAMBIE') or die;
if ($this->row->value == $this->row->key) {
    echo $this->row->key . ' ';
} else {
    echo $this->row->key . '="' . $this->row->value . '" ';
}
