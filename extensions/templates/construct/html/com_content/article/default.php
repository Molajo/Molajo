<?php defined('_JEXEC') or die;
/**
* @package		Unified HTML5 Template Framework for Joomla!
* @author		Cristina Solana http://nightshiftcreative.com
* @author		Matt Thomas http://construct-framework.com | http://betweenbrain.com
* @copyright	Copyright (C) 2009 - 2011 Matt Thomas. All rights reserved.
* @license		GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
*/

if (substr(JVERSION, 0, 3) >= '1.6') {
// Joomla! 1.6+
	
	JHtml::addIncludePath(JPATH_COMPONENT.DS.'helpers');

	// Create shortcuts to some parameters.
	$params		= $this->item->params;
	$canEdit	= $this->item->params->get('access-edit');
	$user		= JFactory::getUser();
	?>
	<article class="item-page<?php echo $this->pageclass_sfx?>">
		<?php if ($this->params->get('show_page_heading', 1)) : ?>
	    <h1>
		    <?php echo $this->escape($this->params->get('page_heading')); ?>
	    </h1>
		<?php endif; ?>
		<?php if ($params->get('show_title')) : ?>
	    <h2>
		    <?php if ($params->get('link_titles') && !empty($this->item->readmore_link)) : ?>
		    <a href="<?php echo $this->item->readmore_link; ?>">
			    <?php echo $this->escape($this->item->title); ?>
		    </a>
		    <?php else : ?>
		    <?php echo $this->escape($this->item->title); ?>
		    <?php endif; ?>
	    </h2>
		<?php endif; ?>

		<?php if ($canEdit ||  $params->get('show_print_icon') || $params->get('show_email_icon')) : ?>
	    <ul class="actions">
	    <?php if (!$this->print) : ?>
		    <?php if ($params->get('show_print_icon')) : ?>
		        <li class="print-icon">
		            <?php echo JHtml::_('icon.print_popup',  $this->item, $params); ?>
		        </li>
		    <?php endif; ?>

		    <?php if ($params->get('show_email_icon')) : ?>
		        <li class="email-icon">
		            <?php echo JHtml::_('icon.email',  $this->item, $params); ?>
		        </li>
		    <?php endif; ?>

		    <?php if ($canEdit) : ?>
		        <li class="edit-icon">
		            <?php echo JHtml::_('icon.edit', $this->item, $params); ?>
		        </li>
		    <?php endif; ?>

		    <?php else : ?>
		        <li>
		            <?php echo JHtml::_('icon.print_screen',  $this->item, $params); ?>
		        </li>
		    <?php endif; ?>
	    </ul>
		<?php endif; ?>

	<?php  if (!$params->get('show_intro')) :
		echo $this->item->event->afterDisplayTitle;
	endif; ?>

	<?php echo $this->item->event->beforeDisplayContent; ?>

	<?php $useDefList = (($params->get('show_author')) OR ($params->get('show_category')) OR ($params->get('show_parent_category')) OR ($params->get('show_create_date')) OR ($params->get('show_modify_date')) OR ($params->get('show_publish_date')) OR ($params->get('show_hits'))); ?>

	<?php if ($useDefList) : ?>
	    <header class="article-info">
	        <hgroup>
		        <h3 class="article-info-term">
        		    <?php  echo JText::_('COM_CONTENT_ARTICLE_INFO'); ?>
        		</h3>
	<?php endif; ?>
	            <?php if ($params->get('show_parent_category') && $this->item->parent_slug != '1:root') : ?>
                <h4 class="parent-category-name">
	                <?php	$title = $this->escape($this->item->parent_title);
	                $url = '<a href="'.JRoute::_(ContentHelperRoute::getCategoryRoute($this->item->parent_slug)).'">'.$title.'</a>';?>
	                <?php if ($params->get('link_parent_category') AND $this->item->parent_slug) : ?>
		                <?php echo JText::sprintf('COM_CONTENT_PARENT', $url); ?>
	                <?php else : ?>
		                <?php echo JText::sprintf('COM_CONTENT_PARENT', $title); ?>
	                <?php endif; ?>
	            </h4>
	            <?php endif; ?>
	            <?php if ($params->get('show_category')) : ?>
                <h5 class="category-name">
	                <?php 	$title = $this->escape($this->item->category_title);
	                $url = '<a href="'.JRoute::_(ContentHelperRoute::getCategoryRoute($this->item->catslug)).'">'.$title.'</a>';?>
	                <?php if ($params->get('link_category') AND $this->item->catslug) : ?>
		                <?php echo JText::sprintf('COM_CONTENT_CATEGORY', $url); ?>
	                <?php else : ?>
		                <?php echo JText::sprintf('COM_CONTENT_CATEGORY', $title); ?>
	                <?php endif; ?>
                </h5>
	            <?php endif; ?>
	            
        <?php if ($useDefList) : ?>	            
            </hgroup>
        <?php endif; ?>
	        
	        <?php if ($params->get('show_create_date')) : ?>
	        <time class="create">
	            <?php echo JText::sprintf('COM_CONTENT_CREATED_DATE_ON', JHtml::_('date',$this->item->created, JText::_('DATE_FORMAT_LC2'))); ?>
	        </time>
	        <?php endif; ?>
	        <?php if ($params->get('show_modify_date')) : ?>
	        <time class="modified">
	            <?php echo JText::sprintf('COM_CONTENT_LAST_UPDATED', JHtml::_('date',$this->item->modified, JText::_('DATE_FORMAT_LC2'))); ?>
	        </time>
	        <?php endif; ?>
	        <?php if ($params->get('show_publish_date')) : ?>
	        <time class="published">
	            <?php echo JText::sprintf('COM_CONTENT_PUBLISHED_DATE', JHtml::_('date',$this->item->publish_up, JText::_('DATE_FORMAT_LC2'))); ?>
	        </time>
	        <?php endif; ?>
	        <?php if ($params->get('show_author') && !empty($this->item->author )) : ?>
	        <address class="createdby" rel="author"> 
	        <?php $author = $this->item->created_by_alias ? $this->item->created_by_alias : $this->item->author; ?>
	        <?php if (!empty($this->item->contactid) && $params->get('link_author') == true): ?>
	        <?php
		        $needle = 'index.php?option=com_contact&view=contact&id=' . $this->item->contactid;
		        $item = JSite::getMenu()->getItems('link', $needle, true);
		        $cntlink = !empty($item) ? $needle . '&Itemid=' . $item->id : $needle;
	        ?>
		        <?php echo JText::sprintf('COM_CONTENT_WRITTEN_BY', JHtml::_('link', JRoute::_($cntlink), $author)); ?>
	        <?php else: ?>
		        <?php echo JText::sprintf('COM_CONTENT_WRITTEN_BY', $author); ?>
	        <?php endif; ?>
	        </address>
	        <?php endif; ?>
	        <?php if ($params->get('show_hits')) : ?>
	        <span class="hits">
		        <?php echo JText::sprintf('COM_CONTENT_ARTICLE_HITS', $this->item->hits); ?>
	        </span>
	        <?php endif; ?>
	<?php if ($useDefList) : ?>
	    </header>
	<?php endif; ?>

	<?php if (isset ($this->item->toc)) : ?>
		<?php echo $this->item->toc; ?>
	<?php endif; ?>
	<?php if ($params->get('access-view')):?>
		<?php echo $this->item->text; ?>

		<?php //optional teaser intro text for guests ?>
	<?php elseif ($params->get('show_noauth') == true AND  $user->get('guest') ) : ?>
		<?php echo $this->item->introtext; ?>
		<?php //Optional link to let them register to see the whole article. ?>
		<?php if ($params->get('show_readmore') && $this->item->fulltext != null) :
			$link1 = JRoute::_('index.php?option=com_users&view=login');
			$link = new JURI($link1);?>
			<p class="readmore">
				<a href="<?php echo $link; ?>">
				<?php $attribs = json_decode($this->item->attribs);  ?>
				<?php
				if ($attribs->alternative_readmore == null) :
					echo JText::_('COM_CONTENT_REGISTER_TO_READ_MORE');
				elseif ($readmore = $this->item->alternative_readmore) :
					echo $readmore;
					if ($params->get('show_readmore_title', 0) != 0) :
						echo JHtml::_('string.truncate', ($this->item->title), $params->get('readmore_limit'));
					endif;
				elseif ($params->get('show_readmore_title', 0) == 0) :
					echo JText::sprintf('COM_CONTENT_READ_MORE_TITLE');
				else :
					echo JText::_('COM_CONTENT_READ_MORE');
					echo JHtml::_('string.truncate', ($this->item->title), $params->get('readmore_limit'));
				endif; ?>
				</a>
			</p>
		<?php endif; ?>
	<?php endif; ?>
	<?php echo $this->item->event->afterDisplayContent; ?>
	</article>	

<?php
}
else {
// Joomla! 1.5
?>

	<article id="item-page<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
		<?php if ($this->params->get('show_page_title',1) && $this->params->get('page_title') != $this->article->title) : ?>
		<h1>
			<?php echo $this->escape($this->params->get('page_title')); ?>
		</h1>		
		<?php endif; ?>
	
		<?php if ($this->params->get('show_title')) : ?>
	    <h2>
		    <?php if ($this->params->get('link_titles') && $this->article->readmore_link != '') : ?>
		    <a href="<?php echo $this->article->readmore_link; ?>">
			    <?php echo $this->escape($this->article->title); ?></a>
		    <?php else :
			    echo $this->escape($this->article->title);
		    endif; ?>
	    </h2>
		<?php endif; ?>
	
		<?php if (($this->user->authorize('com_content', 'edit', 'content', 'all') || $this->user->authorize('com_content', 'edit', 'content', 'own')) || ($this->params->get('show_pdf_icon')) || ($this->params->get('show_print_icon')) || ($this->params->get('show_email_icon'))): ?>
				
		<ul class="actions">
			<?php if (!$this->print) : ?>				
			<?php if ($this->params->get('show_pdf_icon')) : ?>
			    <li class="pdf-icon">	
				    <?php echo JHTML::_('icon.pdf', $this->article, $this->params, $this->access); ?>	
			    </li>	
			<?php endif; ?>
			<?php if ($this->params->get('show_print_icon')) : ?>
			    <li class="print-icon">
				    <?php echo JHTML::_('icon.print_popup', $this->article, $this->params, $this->access); ?>
			    </li>
			<?php endif; ?>
			<?php if ($this->params->get('show_email_icon')) : ?>
			    <li class="print-icon">
				    <?php echo JHTML::_('icon.email', $this->article, $this->params, $this->access); ?>
			    </li>
			<?php endif; ?>		
			<?php if ($this->user->authorize('com_content', 'edit', 'content', 'all') || $this->user->authorize('com_content', 'edit', 'content', 'own')) : ?>
			    <li class="edit-icon">
				    <?php echo JHTML::_('icon.edit', $this->article, $this->params, $this->access); ?>
			    </li>
			<?php endif; ?>
			<?php else : ?>
			    <li>
				    <?php echo JHTML::_('icon.print_screen', $this->article, $this->params, $this->access); ?>
			    </li>		
			<?php endif; ?>
		</ul>
		<?php endif; ?>
			
		<?php if (!$this->params->get('show_intro')) :
			echo $this->article->event->afterDisplayTitle;
		endif; ?>
	
		<?php echo $this->article->event->beforeDisplayContent; ?>
	
		<?php $useDefList = (($this->params->get('show_section') && $this->article->sectionid) || ($this->params->get('show_category') && $this->article->catid) ||	(intval($this->article->modified) !=0 && $this->params->get('show_modify_date')) || ($this->params->get('show_author') && ($this->article->author != "")) ||	($this->params->get('show_create_date'))); ?>
	    
	    <?php if ($useDefList) : ?>
			<header class="article-info">
			    <hgroup>
         <?php endif;?>			 
				    <?php if ($this->params->get('show_section') && $this->article->sectionid) : ?>
			        <h3 class="section-name">
				        <?php if ($this->params->get('link_section')) : ?>
					        <?php echo '<a href="'.JRoute::_(ContentHelperRoute::getSectionRoute($this->article->sectionid)).'">'; ?>
				        <?php endif; ?>
				        <?php echo $this->escape($this->article->section); ?>
				        <?php if ($this->params->get('link_section')) : ?>
					        <?php echo '</a>'; ?>
				        <?php endif; ?>
				        <?php if ($this->params->get('show_category')) : ?>
					        <?php echo ' -&nbsp;'; ?>
				        <?php endif; ?>
			        </h3>
				    <?php endif; ?>
				
				    <?php if ($this->params->get('show_category') && $this->article->catid) : ?>
			        <h4 class="category-name">
				        <?php if ($this->params->get('link_category')) : ?>
					        <?php echo '<a href="'.JRoute::_(ContentHelperRoute::getCategoryRoute($this->article->catslug, $this->article->sectionid)).'">'; ?>
				        <?php endif; ?>
				        <?php echo $this->escape($this->article->category); ?>
				        <?php if ($this->params->get('link_category')) : ?>
					        <?php echo '</a>'; ?>
				        <?php endif; ?>
			        </h4>
				    <?php endif; ?>
	        <?php if ($useDefList) : ?>				    
		        </hgroup>
            <?php endif; ?>
				<?php if (intval($this->article->modified) !=0 && $this->params->get('show_modify_date')) : ?>
			    <time class="modified">
				    <?php echo JText::sprintf('LAST_UPDATED2', JHTML::_('date', $this->article->modified, JText::_('DATE_FORMAT_LC2'))); ?>
			    </time>
				<?php endif; ?>
			
				<?php if (($this->params->get('show_author')) && ($this->article->author != "")) : ?>
			    <address class="createdby" rel="author"> 
				    <?php JText::printf('Written by', ($this->article->created_by_alias ? $this->escape($this->article->created_by_alias) : $this->escape($this->article->author))); ?>
			    </address>
				<?php endif; ?>
			
				<?php if ($this->params->get('show_create_date')) : ?>
			    <time class="create">
				    <?php echo JHTML::_('date', $this->article->created, JText::_('DATE_FORMAT_LC2')); ?>
			    </time>
				<?php endif; ?>
				<?php if ($this->params->get('show_url') && $this->article->urls) : ?>
			    <span class="hits">
				    <a href="<?php echo $this->escape($this->article->urls); ?>">
					    <?php echo $this->escape($this->article->urls); ?></a>
			    </span>
				<?php endif; ?>
    	<?php if ($useDefList) : ?>				
			</header>
		<?php endif; ?>	
	
		<?php if (isset ($this->article->toc)) :
			echo $this->article->toc;
		endif; ?>
	
		<?php echo JFilterOutput::ampReplace($this->article->text); ?>
	
		<?php echo $this->article->event->afterDisplayContent; ?>
	
	</article>

<?php }
