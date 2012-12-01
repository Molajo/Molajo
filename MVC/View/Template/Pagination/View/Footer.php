<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
defined('MOLAJO') or die;

if ($this->row->next_page === 0) {
    $class = 'class="arrow unavailable"';
    $link = '';
    $linkend = '';
} else {
    $class = 'class="arrow"';
    $link = '<a href="' . $this->row->next_link . '">';
    $linkend = '</a>';
}
?>
    <li <?php echo $class; ?>>
        <?php echo $link; ?>&raquo;<?php echo $linkend; ?>
    </li>
</ul>
