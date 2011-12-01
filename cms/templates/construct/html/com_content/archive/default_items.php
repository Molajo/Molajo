<?php defined('_JEXEC') or die;
/**
* @package		Unified HTML5 Template Framework for Joomla!
* @author		Cristina Solana http://nightshiftcreative.com
* @author		Matt Thomas http://construct-framework.com | http://betweenbrain.com
* @copyright	Copyright (C) 2009 - 2011 Matt Thomas. All rights reserved.
* @license		GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
*/

if (substr(JVERSION, 0, 3) >= '1.6') {
// Joomla 1.6+ ?>

    <?php JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');
    $params = &$this->params; ?>

    <ul id="archive-items">
    <?php foreach ($this->items as $i => $item) : ?>
	    <li class="row<?php echo $i % 2; ?>">

		    <h2>
		    <?php if ($params->get('link_titles')): ?>
			    <a href="<?php echo JRoute::_(ContentHelperRoute::getArticleRoute($item->slug,$item->catslug)); ?>">
				    <?php echo $this->escape($item->title); ?></a>
		    <?php else: ?>
				    <?php echo $this->escape($item->title); ?>
		    <?php endif; ?>
		    </h2>

    <?php $useDefList = (($params->get('show_author')) or ($params->get('show_parent_category')) or ($params->get('show_category')) or ($params->get('show_create_date')) or ($params->get('show_modify_date')) or ($params->get('show_publish_date'))  or ($params->get('show_hits'))); ?>
	
	<?php if ($useDefList) : ?>     
         <header class="article-info">
            <hgroup>
                 <h3 class="article-info-term">
                    <?php  echo JText::_('CONTENT_ARTICLE_INFO'); ?>
                </h3>
    <?php endif; ?>
    
                <?php if ($params->get('show_parent_category')) : ?>
                <h4 class="parent-category-name">   
                    <?php	$title = $this->escape($item->parent_title);
                            $url = '<a href="'.JRoute::_(ContentHelperRoute::getCategoryRoute($item->parent_slug)).'">'.$title.'</a>';?>
                    <?php if ($params->get('link_parent_category') && $item->parent_slug) : ?>
                        <?php echo JText::sprintf('CONTENT_PARENT', $url); ?>
                        <?php else : ?>
                        <?php echo JText::sprintf('CONTENT_PARENT', $title); ?>
                    <?php endif; ?>
                </h4>
                <?php endif; ?>

                <?php if ($params->get('show_category')) : ?>
                <h5 class="category-name">
	                <?php	$title = $this->escape($item->category_title);
			                $url = '<a href="' . JRoute::_(ContentHelperRoute::getCategoryRoute($item->catslug)) . '">' . $title . '</a>'; ?>
	                <?php if ($params->get('link_category') && $item->catslug) : ?>
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
                <?php echo JText::sprintf('CONTENT_CREATED_DATE_ON', JHtml::_('date',$item->created, JText::_('DATE_FORMAT_LC2'))); ?>
            </time>
            <?php endif; ?>
            <?php if ($params->get('show_modify_date')) : ?>
            <time class="modified">
             <?php echo JText::sprintf('CONTENT_LAST_UPDATED', JHtml::_('date',$item->modified, JText::_('DATE_FORMAT_LC2'))); ?>
            </time>
            <?php endif; ?>
                <?php if ($params->get('show_publish_date')) : ?>
            <time class="published">
            <?php echo JText::sprintf('CONTENT_PUBLISHED_DATE', JHtml::_('date',$item->publish_up, JText::_('DATE_FORMAT_LC2'))); ?>
            </time>
            <?php endif; ?>
            <?php if ($params->get('show_author') && !empty($item->author )) : ?>
            <address class="createdby" rel="author"> 
                <?php $author =  $item->author; ?>
                <?php $author = ($item->created_by_alias ? $item->created_by_alias : $author);?>

	                <?php if (!empty($item->contactid ) &&  $params->get('link_author') == true):?>
		                <?php 	echo JText::sprintf('CONTENT_WRITTEN_BY' ,
		                 JHtml::_('link',JRoute::_('index.php?option=com_contact&view=contact&id='.$item->contactid),$author)); ?>

	                <?php else :?>
		                <?php echo JText::sprintf('CONTENT_WRITTEN_BY', $author); ?>
	                <?php endif; ?>
            </address>
            <?php endif; ?>
            <?php if ($params->get('show_hits')) : ?>
            <span class="hits">
                <?php echo JText::sprintf('CONTENT_ARTICLE_HITS', $item->hits); ?>
            </span>
            <?php endif; ?>
	<?php if ($useDefList) : ?>
	    </header>
	<?php endif; ?>

    <?php if ($params->get('show_intro')) :?>
	    <p class="intro">
		    <?php echo JHtml::_('string.truncate', $item->introtext, $params->get('introtext_limit')); ?>
	    </p>
    <?php endif; ?>
	    </li>
    <?php endforeach; ?>
    </ul>

    <nav class="pagination">
	    <p class="counter">
		    <?php echo $this->pagination->getPagesCounter(); ?>
	    </p>
	    <?php echo $this->pagination->getPagesLinks(); ?>
    </nav>

<?php
}
else {
// Joomla 1.5 ?>

<ul id="archive-items">
<?php foreach ($this->items as $i => $item) : ?>
	<li class="row<?php echo $i % 2; ?>">
		<h2>
		    <?php if ($params->get('link_titles')): ?>
			    <a href="<?php echo JRoute::_(ContentHelperRoute::getArticleRoute($item->slug,$item->catslug)); ?>">
				    <?php echo $this->escape($item->title); ?></a>
		    <?php else: ?>
				    <?php echo $this->escape($item->title); ?>
		    <?php endif; ?>
		</h2>


    <?php $useDefList = (($params->get('show_author')) or ($params->get('show_parent_category')) or ($params->get('show_category')) or ($params->get('show_create_date')) or ($params->get('show_modify_date')) or ($params->get('show_publish_date'))  or ($params->get('show_hits'))); ?>
    <?php if ($useDefList) : ?>	    
		<header class="article-info">
		    <hgroup>
	<?php endif; ?>	        
                <?php if ($params->get('show_parent_category')) : ?>
                <h3 class="parent-category-name">
                    <?php	$title = $this->escape($item->parent_title);
                            $url = '<a href="'.JRoute::_(ContentHelperRoute::getCategoryRoute($item->catslug)).'">'.$title.'</a>';?>
                    <?php if ($params->get('link_parent_category') && $item->parent_slug) : ?>
                        <?php echo JText::sprintf('CONTENT_PARENT', $url); ?>
                        <?php else : ?>
                        <?php echo JText::sprintf('CONTENT_PARENT', $title); ?>
                    <?php endif; ?>
                </h3>
                <?php endif; ?>

                <?php if ($params->get('show_category')) : ?>
                <h4 class="category-name">
                    <?php	$title = $this->escape($item->category_title);
	                        $url = '<a href="' . JRoute::_(ContentHelperRoute::getCategoryRoute($item->catslug)) . '">' . $title . '</a>'; ?>
                    <?php if ($params->get('link_category') && $item->catslug) : ?>
                        <?php echo JText::sprintf('CONTENT_CATEGORY', $url); ?>
                        <?php else : ?>
                        <?php echo JText::sprintf('CONTENT_CATEGORY', $title); ?>
                    <?php endif; ?>
                </h4>
                <?php endif; ?>		
	    <?php if ($useDefList) : ?>
	        </hgroup>
	    <?php endif; ?>		
		
		    <?php if ($params->get('show_create_date')) : ?>
		    <time class="create">
		        <?php echo JText::sprintf('CONTENT_CREATED_DATE_ON', JHtml::_('date',$item->created, JText::_('DATE_FORMAT_LC2'))); ?>
		    </time>
		    <?php endif; ?>
		    <?php if ($params->get('show_modify_date')) : ?>
		    <time class="modified">
		        <?php echo JText::sprintf('CONTENT_LAST_UPDATED', JHtml::_('date',$item->modified, JText::_('DATE_FORMAT_LC2'))); ?>
		    </time>
		    <?php endif; ?>
		    <?php if ($params->get('show_publish_date')) : ?>
		    <time class="published">
		        <?php echo JText::sprintf('CONTENT_PUBLISHED_DATE', JHtml::_('date',$item->publish_up, JText::_('DATE_FORMAT_LC2'))); ?>
		    </time>
		    <?php endif; ?>
		    <?php if ($params->get('show_author') && !empty($item->author )) : ?>
		    <address class="createdby" rel="author"> 
			    <?php $author =  $item->author; ?>
			    <?php $author = ($item->created_by_alias ? $item->created_by_alias : $author);?>		
			    <?php if (!empty($item->contactid ) &&  $params->get('link_author') == true):?>
				    <?php 	echo JText::sprintf('CONTENT_WRITTEN_BY' ,
				     JHtml::_('link',JRoute::_('index.php?option=com_contact&view=contact&id='.$item->contactid),$author)); ?>		
			    <?php else :?>
				    <?php echo JText::sprintf('CONTENT_WRITTEN_BY', $author); ?>
			    <?php endif; ?>
		    </address>
		    <?php endif; ?>	
		    <?php if ($params->get('show_hits')) : ?>
		    <span class="hits">
		        <?php echo JText::sprintf('CONTENT_ARTICLE_HITS', $item->hits); ?>
		    </span>
		    <?php endif; ?>
	<?php if ($useDefList) : ?>
	    </header>
	<?php endif; ?>
		
		<?php if ($params->get('show_intro')) :?>
		<p class="intro">
			<?php echo JHtml::_('string.truncate', $item->introtext, $params->get('introtext_limit')); ?>
		</p>		
		<?php endif; ?>
	</li>
<?php endforeach; ?>
</ul>

<nav class="pagination">
	<p class="counter">
		<?php echo $this->pagination->getPagesCounter(); ?>
	</p>
	<?php echo $this->pagination->getPagesLinks(); ?>
</nav>

<ul id="archive-list">
<?php foreach ($this->items as $item) : ?>
    <li class="row<?php echo ($item->odd +1 ); ?>">
	    <h4 class="contentheading">
		    <a href="<?php echo JRoute::_(ContentHelperRoute::getArticleRoute($item->slug)); ?>">
			    <?php echo $this->escape($item->title); ?></a>
	    </h4>

	    <?php if (($this->params->get('show_section') && $item->sectionid) || ($this->params->get('show_category') && $item->catid)) : ?>
		    <p>
		    <?php if ($this->params->get('show_section') && $item->sectionid && isset($item->section)) : ?>
			    <span>
			    <?php if ($this->params->get('link_section')) : ?>
				    <?php echo '<a href="'.JRoute::_(ContentHelperRoute::getSectionRoute($item->sectionid)).'">'; ?>
			    <?php endif; ?>

			    <?php echo $this->escape($item->section); ?>

			    <?php if ($this->params->get('link_section')) : ?>
				    <?php echo '</a>'; ?>
			    <?php endif; ?>

			    <?php if ($this->params->get('show_category')) : ?>
				    <?php echo ' - '; ?>
			    <?php endif; ?>
			    </span>
		    <?php endif; ?>
		    <?php if ($this->params->get('show_category') && $item->catid) : ?>
			    <span>
			    <?php if ($this->params->get('link_category')) : ?>
				    <?php echo '<a href="'.JRoute::_(ContentHelperRoute::getCategoryRoute($item->catslug, $item->sectionid)).'">'; ?>
			    <?php endif; ?>
			    <?php echo $this->escape($item->category); ?>
			    <?php if ($this->params->get('link_category')) : ?>
				    <?php echo '</a>'; ?>
			    <?php endif; ?>
			    </span>
		    <?php endif; ?>
		    </p>
	    <?php endif; ?>

	    <h5 class="metadata">
	    <?php if ($this->params->get('show_create_date')) : ?>
		    <span class="created-date">
			    <?php echo JText::_('Created') .': '.  JHTML::_( 'date', $item->created, JText::_('DATE_FORMAT_LC2')) ?>
		    </span>
		    <?php endif; ?>
		    <?php if ($this->params->get('show_author')) : ?>
		    <span class="author">
			    <?php echo JText::_('Author').': '; echo $this->escape($item->created_by_alias) ? $this->escape($item->created_by_alias) : $this->escape($item->author); ?>
		    </span>
	    <?php endif; ?>
	    </h5>
	    <section class="intro">
		    <?php echo substr(strip_tags($item->introtext), 0, 255);  ?>...
	    </section>
    </li>
<?php endforeach; ?>
</ul>
<nav id="navigation">
    <p><?php echo $this->pagination->getPagesLinks(); ?></p>
    <p><?php echo $this->pagination->getPagesCounter(); ?></p>
</nav>

<?php }
