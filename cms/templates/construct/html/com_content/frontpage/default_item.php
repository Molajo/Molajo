<?php defined('_JEXEC') or die;
/**
 * @package        Unified HTML5 Template Framework for Joomla!
 * @author        Cristina Solana http://nightshiftcreative.com
 * @author        Matt Thomas http://construct-framework.com | http://betweenbrain.com
 * @copyright    Copyright (C) 2009 - 2011 Matt Thomas. All rights reserved.
 * @license        GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 */
?>

<?php if ($this->item->params->get('show_title')) : ?>
<h2>
    <?php if ($this->item->params->get('link_titles') && $this->item->readmore_link != '') : ?>
    <a href="<?php echo $this->item->readmore_link; ?>" class="contentpagetitle">
        <?php echo $this->escape($this->item->title); ?></a>
    <?php  else :
    echo $this->escape($this->item->title);
endif; ?>
</h2>
<?php endif; ?>

<?php if (!$this->item->params->get('show_intro')) : ?>
<?php echo $this->item->event->afterDisplayTitle; ?>
<?php endif; ?>

<?php echo $this->item->event->beforeDisplayContent; ?>

<?php if ($this->item->params->get('show_pdf_icon') || $this->item->params->get('show_print_icon') || $this->item->params->get('show_email_icon') || $this->user->authorize('com_content', 'edit', 'content', 'all') || $this->user->authorize('com_content', 'edit', 'content', 'own')): ?>
<ul class="actions">
    <?php if ($this->item->params->get('show_pdf_icon')) : ?>
    <li class="pdf-icon">
        <?php echo JHTML::_('icon.pdf', $this->item, $this->item->params, $this->access); ?>
    </li>
    <?php endif; ?>
    <?php if ($this->item->params->get('show_print_icon')) : ?>
    <li class="print-icon">
        <?php echo JHTML::_('icon.print_popup', $this->item, $this->item->params, $this->access); ?>
    </li>
    <?php endif; ?>
    <?php if ($this->item->params->get('show_email_icon')) : ?>
    <li class="email-icon">
        <?php echo JHTML::_('icon.email', $this->item, $this->item->params, $this->access); ?>
    </li>
    <?php endif; ?>

    <?php if ($this->user->authorize('com_content', 'edit', 'content', 'all') || $this->user->authorize('com_content', 'edit', 'content', 'own')) : ?>
    <li class="edit-icon">
        <?php echo JHTML::_('icon.edit', $this->item, $this->item->params, $this->access); ?>
    </li>
    <?php endif; ?>
</ul>
<?php endif; ?>

<?php $useDefList = (($this->item->params->get('show_section') && $this->item->sectionid) || ($this->item->params->get('show_category') && $this->item->catid) || (intval($this->item->modified) != 0 && $this->item->params->get('show_modify_date')) || ($this->item->params->get('show_author') && ($this->item->author != "")) || ($this->item->params->get('show_create_date')) || ($this->item->params->get('show_url') && $this->item->urls)); ?>

<?php if ($useDefList) : ?>		
<header class="article-info">
    <hgroup>
		<h3 class="article-info-term">
            <?php echo JText::_('Details'); ?>
        </h3>
    <?php endif; ?>
<?php if ($this->item->params->get('show_section') && $this->item->sectionid && isset($this->section->title)) : ?>
    <h4 class="section-name">
        <?php if ($this->item->params->get('link_section')) : ?>
		       <a href="<?php echo JRoute::_(ContentHelperRoute::getSectionRoute($this->item->sectionid)); ?>">
		    <?php endif; ?>
        <?php echo $this->escape($this->section->title); ?>
        <?php if ($this->item->params->get('link_section')) : ?>
        <?php echo '</a>'; ?>
        <?php endif; ?>
        <?php if ($this->item->params->get('show_category')) : ?>
        <?php echo ' -&nbsp;'; ?>
        <?php endif; ?>
    </h4>
    <?php endif; ?>
<?php if ($this->item->params->get('show_category') && $this->item->catid) : ?>
    <h5 class="category-name">
        <?php if ($this->item->params->get('link_category')) : ?>
			    <a href="<?php echo JRoute::_(ContentHelperRoute::getCategoryRoute($this->item->catslug, $this->item->sectionid)); ?>">
		    <?php endif; ?>
        <?php echo $this->escape($this->item->category); ?>
        <?php if ($this->item->params->get('link_category')) : ?>
        <?php echo '</a>'; ?>
        <?php endif; ?>
    </h5>
    <?php endif; ?>
<?php if ($useDefList) : ?>
    </hgroup>
    <?php endif; ?>
<?php if ($this->item->params->get('show_create_date')) : ?>
    <time class="create">
        <?php echo JHTML::_('date', $this->item->created, JText::_('DATE_FORMAT_LC2')); ?>
    </time>
    <?php endif; ?>

<?php if (intval($this->item->modified) != 0 && $this->item->params->get('show_modify_date')) : ?>
    <time class="modified">
        <?php echo JText::sprintf('LAST_UPDATED2', JHTML::_('date', $this->item->modified, JText::_('DATE_FORMAT_LC2'))); ?>
    </time>
    <?php endif; ?>

<?php if (($this->item->params->get('show_author')) && ($this->item->author != "")) : ?>
    <address class="createdby" rel="author">
        <?php JText::printf('Written by', ($this->item->created_by_alias ? $this->escape($this->item->created_by_alias)
                : $this->escape($this->item->author))); ?>
    </address>
    <?php endif; ?>

<?php if ($this->item->params->get('show_url') && $this->item->urls) : ?>
    <span class="hits">
	    <a href="<?php echo $this->escape($this->item->urls); ?>" target="_blank">
            <?php echo $this->escape($this->item->urls); ?></a>
    </span>
    <?php endif; ?>

<?php if (isset ($this->item->toc)) : ?>
    <?php echo $this->item->toc; ?>
    <?php endif; ?>

<?php if ($useDefList) : ?>				
</header>
<?php endif; ?>


<?php echo JFilterOutput::ampReplace($this->item->text); ?>

<?php if ($this->item->params->get('show_readmore') && $this->item->readmore) : ?>
<p class="readmore">
    <a href="<?php echo $this->item->readmore_link; ?>" class="readon">
        <?php if ($this->item->readmore_register) :
        echo JText::_('Register to read more...');
    elseif ($readmore = $this->item->params->get('readmore')) :
        echo $readmore;
    else :
        echo JText::sprintf('Read more', $this->escape($this->item->title));
    endif; ?></a>
</p>
<?php endif; ?>

<?php echo $this->item->event->afterDisplayContent;
