<?php

/**
 *
 * @package    Niambie
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @license    MIT
 */
defined('NIAMBIE') or die;

$this->row->button_title = str_replace('&nbsp;', ' ', htmlspecialchars_decode($this->row->button_title));
$this->row->button_link_extra = str_replace('&nbsp;', ' ', htmlspecialchars_decode($this->row->button_link_extra));

if (trim($this->row->button_link_extra) == '') {
} else {
    $this->row->button_link_extra = ' ' . trim($this->row->button_link_extra);
}
if (trim($this->row->button_id) == '') {
} else {
    $this->row->button_id = ' ' . 'id="' . trim($this->row->button_id) . '"';
}
if (trim($this->row->button_icon_prepend) == '') {
} else {
    $this->row->button_title = '<i class="' . trim(
        $this->row->button_icon_prepend
    ) . '"></i>' . $this->row->button_title;
}
if (trim($this->row->button_icon_append) == '') {
} else {
    $this->row->button_title .= '<i class="' . trim($this->row->button_icon_append) . '"></i>';
}

switch ($this->row->button_tag) {
    case 'button':
        echo '<button' . $this->row->button_id . $this->row->button_class . $this->row->button_link_extra . '>' . $this->row->button_title . '</button>';
        break;

    default:
        echo '<a href="' . $this->row->button_link . '"' . $this->row->button_id . $this->row->button_class . $this->row->button_link_extra . '>' . $this->row->button_title . '</a>';
        break;
}
