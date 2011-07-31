<?php
/**
 * @version     $id: item_body.php
 * @package     Molajo
 * @subpackage  Latest News Layout
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die; ?>
<li class="latest-news-item">
    <a href="<?php echo $this->row->url; ?>"><h3><?php echo $this->row->title; ?></h3></a>
    <?php echo JText::_('MOLAJO_WRITTEN_BY').' '.$this->row->display_author_name; ?>
    <?php echo ' '.$this->row->published_pretty_date; ?>
</li>
