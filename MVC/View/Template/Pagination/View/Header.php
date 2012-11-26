<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
defined('MOLAJO') or die;

if ($this->row->previous_page === 0) {
    $class = 'class="arrow unavailable"';
    $link = '';
    $linkend = '';

} else {
    $class = 'class="arrow"';
    $link = '<a href="' . $this->row->prev_link . '">';
    $linkend = '</a>';
}
?>
<ul class="pagination">
    <li <?php echo $class; ?>>
        <?php echo $link; ?>&laquo;<?php echo $linkend; ?>
    </li>
