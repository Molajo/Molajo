<?php
/**
 * Pagination Template View
 *
 * @package      Niambie
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
use Molajo\Service\Services;

defined('NIAMBIE') or die;

if ($this->row->previous_page === 0) {
    $class   = 'class="arrow unavailable"';
    $link    = '';
    $linkend = '';

} else {
    $class   = 'class="arrow"';
    $link    = '<a href="' . $this->row->prev_link . '">';
    $linkend = '</a>';
}
?>
<ul class="pagination">
    <li <?php echo $class; ?>>
        <?php echo $link; ?>&laquo;<?php echo $linkend; ?>
    </li>
