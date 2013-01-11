<?php
/**
 * Item Template View
 *
 * @package      Niambie
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
use Molajo\Service\Services;

defined('NIAMBIE') or die; ?>
<h1><?php echo $this->row->title; ?></h1>

<div><?php echo $this->row->content_text; ?></div>
