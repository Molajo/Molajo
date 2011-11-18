<?php defined('_JEXEC') or die;
/**
 * @version		$Id: default.php 21097 2011-04-07 15:38:03Z dextercowley $
 * @package		Joomla.Site
 * @subpackage	com_contact
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// Joomla 1.6+ only

JHtml::addIncludePath(JPATH_COMPONENT.DS.'helpers');

?>

<section class="blog-featured<?php echo $this->pageclass_sfx;?>">
    <?php if ($this->params->get('show_page_heading')!=0 ): ?>
	        <h1>
	        <?php echo $this->escape($this->params->get('page_heading')); ?>
	        </h1>
        <?php endif; ?>

        <?php echo $this->loadTemplate('items'); ?>
        <?php if ($this->params->def('show_pagination', 2) == 1  || ($this->params->get('show_pagination') == 2 && $this->pagination->get('pages.total') > 1)) : ?>
	        <nav class="pagination">
		        <?php if ($this->params->def('show_pagination_results', 1)) : ?>
			        <p class="counter">
				        <?php echo $this->pagination->getPagesCounter(); ?>
			        </p>
		        <?php  endif; ?>
		        <?php echo $this->pagination->getPagesLinks(); ?>
	        </nav>
    <?php endif; ?>
</section>
