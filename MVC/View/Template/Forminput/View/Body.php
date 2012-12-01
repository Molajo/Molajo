<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
defined('MOLAJO') or die;
if ($this->row->value == $this->row->key) {
    echo $this->row->key .' ';
} else {
    echo $this->row->key . '="' . $this->row->value .'" ';
}
