<?php
/**
 * @package     Molajo
 * @subpackage  Layouts
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 * 
 * footer.php runs one time for each row processed after the header.php and the body.php
 * put html in here that you want to display as a footer to row information
 * 
 */
defined('MOLAJO') or die; ?>

<p class="small">
    <?php echo JText::_('MOLAJO_WRITTEN_BY').' '.$this->row->display_author_name; ?>
</p>
<p class="small">
    <?php echo $this->row->published_pretty_date; ?>
</p>   