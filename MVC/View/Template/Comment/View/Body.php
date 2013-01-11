<?php
/**
 * Comment Template View
 *
 * @package      Niambie
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
defined('NIAMBIE') or die; ?>

if ($this->row->closed == 1) {
    $comment = $this->row->closed_comment;

} elseif ($this->row->count_of_comments == 0) {
    $comment = $this->row->content_text;

} else {
    $comment = $this->row->content_text . ' ' . $this->row->count_of_comments . '.';
} ?>
<div class="comment-head">
    <h3><?php echo $this->row->title; ?></h3>

    <p><?php echo $comment; ?><p>
</div>
