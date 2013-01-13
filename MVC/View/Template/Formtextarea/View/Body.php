<?php
/**
 * Form Text Area
 *
 * @package      Molajo
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
defined('MOLAJO') or die;
if ($this->row->value == $this->row->key) {
    echo $this->row->key . ' ';
} else {
    echo $this->row->key . '="' . $this->row->value . '" ';
}
