<?php
/**
 * @package    Niambie
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    MIT, see License folder
 */
defined('NIAMBIE') or die;

if ($this->row->current === 0) {
    $class = '';
    $link = '<a href="' . $this->row->link . '">';
    $linkend = '</a>';
} else {
    $class = 'class="unavailable"';
    $link = '';
    $linkend = '';
}
?>
    <li <?php echo $class; ?>>
        <?php echo $link; ?><?php echo $this->row->link_text; ?><?php echo $linkend; ?>
    </li>
