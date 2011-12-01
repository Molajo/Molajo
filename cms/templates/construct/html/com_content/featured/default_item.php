<?php defined('_JEXEC') or die;
/**
 * @version		$Id: default_item.php 21092 2011-04-06 17:12:16Z infograf768 $
 * @package		Joomla.Site
 * @subpackage	com_content
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// Create a shortcut for params.
$params 	= &$this->item->params;
$canEdit	= $this->item->params->get('access-edit');
?>

    <?php if ($this->item->state == 0) : ?>
    <section class="system-unpublished">
    <?php endif; ?>
    
    <?php if ($params->get('show_title')) : ?>
    <h2>
        <?php if ($params->get('link_titles') && $params->get('access-view')) : ?>
	        <a href="<?php echo JRoute::_(ContentHelperRoute::getArticleRoute($this->item->slug, $this->item->catid)); ?>">
	        <?php echo $this->escape($this->item->title); ?></a>
        <?php else : ?>
	        <?php echo $this->escape($this->item->title); ?>
        <?php endif; ?>
    </h2>
    <?php endif; ?>

    <?php if ($params->get('show_print_icon') || $params->get('show_email_icon') || $canEdit) : ?>
    <ul class="actions">
        <?php if ($params->get('show_print_icon')) : ?>
        <li class="print-icon">
	        <?php echo JHtml::_('icon.print_popup', $this->item, $params); ?>
        </li>
        <?php endif; ?>
        <?php if ($params->get('show_email_icon')) : ?>
        <li class="email-icon">
	        <?php echo JHtml::_('icon.email', $this->item, $params); ?>
        </li>
        <?php endif; ?>

        <?php if ($canEdit) : ?>
        <li class="edit-icon">
	        <?php echo JHtml::_('icon.edit', $this->item, $params); ?>
        </li>
        <?php endif; ?>
    </ul>
    <?php endif; ?>

    <?php if (!$params->get('show_intro')) : ?>
        <?php echo $this->item->event->afterDisplayTitle; ?>
    <?php endif; ?>

    <?php echo $this->item->event->beforeDisplayContent; ?>

    <?php $useDefList = (($params->get('show_author')) or ($params->get('show_category')) or ($params->get('show_create_date')) or ($params->get('show_modify_date')) or ($params->get('show_publish_date')) or ($params->get('show_parent_category')) or ($params->get('show_hits'))); ?>
    
    <?php if ($useDefList) : ?>     
    <header class="article-info">
        <hgroup>
             <h3 class="article-info-term">
                <?php  echo JText::_('CONTENT_ARTICLE_INFO'); ?>
            </h3>
        <?php endif; ?>

            <?php if ($params->get('show_parent_category') && $this->item->parent_id != 1) : ?>
            <h4 class="parent-category-name">    
                 <?php $title = $this->escape($this->item->parent_title);
                    $url = '<a href="' . JRoute::_(ContentHelperRoute::getCategoryRoute($this->item->parent_slug)) . '">' . $title . '</a>'; ?>
                <?php if ($params->get('link_parent_category') AND $this->item->parent_slug) : ?>
                    <?php echo JText::sprintf('CONTENT_PARENT', $url); ?>
                    <?php else : ?>
                    <?php echo JText::sprintf('CONTENT_PARENT', $title); ?>
                <?php endif; ?>
            </h4>
            <?php endif; ?>
            <?php if ($params->get('show_category')) : ?>
            <h5 class="category-name">
                <?php $title = $this->escape($this->item->category_title);
                    $url = '<a href="'.JRoute::_(ContentHelperRoute::getCategoryRoute($this->item->catslug)).'">'.$title.'</a>';?>
                <?php if ($params->get('link_category') AND $this->item->catslug) : ?>
                    <?php echo JText::sprintf('CONTENT_CATEGORY', $url); ?>
                    <?php else : ?>
                    <?php echo JText::sprintf('CONTENT_CATEGORY', $title); ?>
                <?php endif; ?>
            </h5>
            <?php endif; ?>
        <?php if ($useDefList) : ?>     
        </hgroup>
        <?php endif; ?>              

        <?php if ($params->get('show_create_date')) : ?>
        <time class="create">
            <?php echo JText::sprintf('CONTENT_CREATED_DATE_ON', JHtml::_('date',$this->item->created, JText::_('DATE_FORMAT_LC2'))); ?>
        </time>
        <?php endif; ?>
        <?php if ($params->get('show_modify_date')) : ?>
        <time class="modified">
            <?php echo JText::sprintf('CONTENT_LAST_UPDATED', JHtml::_('date',$this->item->modified, JText::_('DATE_FORMAT_LC2'))); ?>
        </time>
        <?php endif; ?>
        <?php if ($params->get('show_publish_date')) : ?>
        <time class="published">
            <?php echo JText::sprintf('CONTENT_PUBLISHED_DATE', JHtml::_('date',$this->item->publish_up, JText::_('DATE_FORMAT_LC2'))); ?>
        </time>
        <?php endif; ?>
        <?php if ($params->get('show_author') && !empty($this->item->author )) : ?>
        <address class="createdby" rel="author"> 
            <?php $author =  $this->item->author; ?>
            <?php $author = ($this->item->created_by_alias ? $this->item->created_by_alias : $author);?>
            <?php if (!empty($this->item->contactid ) &&  $params->get('link_author') == true):?>
                <?php 	echo JText::sprintf('CONTENT_WRITTEN_BY' ,
                 JHtml::_('link',JRoute::_('index.php?option=com_contact&view=contact&id='.$this->item->contactid),$author)); ?>

            <?php else :?>
                <?php echo JText::sprintf('CONTENT_WRITTEN_BY', $author); ?>
            <?php endif; ?>
        </address>
        <?php endif; ?>	
        <?php if ($params->get('show_hits')) : ?>
        <span class="hits">
            <?php echo JText::sprintf('CONTENT_ARTICLE_HITS', $this->item->hits); ?>
        </span>
        <?php endif; ?>
    <?php if ($useDefList) : ?>     
    </header>
    <?php endif; ?>

    <?php echo $this->item->introtext; ?>

    <?php if ($params->get('show_readmore') && $this->item->readmore) :
        if ($params->get('access-view')) :
	        $link = JRoute::_(ContentHelperRoute::getArticleRoute($this->item->slug, $this->item->catid));
        else :
	        $menu = JFactory::getApplication()->getMenu();
	        $active = $menu->getActive();
	        $itemId = $active->id;
	        $link1 = JRoute::_('index.php?option=com_users&view=login&&Itemid=' . $itemId);
	        $returnURL = JRoute::_(ContentHelperRoute::getArticleRoute($this->item->slug, $this->item->catid));
	        $link = new JURI($link1);
	        $link->setVar('return', base64_encode($returnURL));
        endif;
    ?>
    
    <a class="readmore" href="<?php echo $link; ?>">
        <?php if (!$params->get('access-view')) :
            echo JText::_('CONTENT_REGISTER_TO_READ_MORE');
        elseif ($readmore = $this->item->alternative_readmore) :
            echo $readmore;
            if ($params->get('show_readmore_title', 0) != 0) :
                echo JHtml::_('string.truncate', ($this->item->title), $params->get('readmore_limit'));
            endif;
        elseif ($params->get('show_readmore_title', 0) == 0) :
            echo JText::sprintf('CONTENT_READ_MORE_TITLE');
        else :
            echo JText::_('CONTENT_READ_MORE');
            echo JHtml::_('string.truncate', ($this->item->title), $params->get('readmore_limit'));
        endif; ?>
    </a>
        
    <?php endif; ?>

    <?php if ($this->item->state == 0) : ?>
    </section>
    <?php endif; ?>


    <?php echo $this->item->event->afterDisplayContent; ?>
