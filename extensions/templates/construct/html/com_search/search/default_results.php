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

    <dl class="search-results<?php echo $this->pageclass_sfx; ?>">
        <?php foreach($this->results as $result) : ?>
	        <dt class="result-title">
		        <?php echo $this->pagination->limitstart + $result->count.'. ';?>
		        <?php if ($result->href) :?>
			        <a href="<?php echo JRoute::_($result->href); ?>"<?php if ($result->browsernav == 1) :?> target="_blank"<?php endif;?>>
				        <?php echo $this->escape($result->title);?>
			        </a>
		        <?php else:?>
			        <?php echo $this->escape($result->title);?>
		        <?php endif; ?>
	        </dt>
	        <?php if ($result->section) : ?>
		        <dd class="result-category">
			        <?php echo $this->escape($result->section); ?>
		        </dd>
	        <?php endif; ?>
	        <dd class="result-text">
		        <?php echo $result->text; ?>
	        </dd>
	        <?php if ($this->params->get('show_date')) : ?>
		        <time class="result-created">
			        <?php echo JText::sprintf('JGLOBAL_CREATED_DATE_ON', $result->created); ?>
		        </time>
	        <?php endif; ?>
        <?php endforeach; ?>
    </dl>

    <nav class="pagination">
	    <?php echo $this->pagination->getPagesLinks(); ?>
    </nav>

<?php
}
else {
// Joomla 1.5 ?>

    <h3><?php echo JText :: _('Search_result'); ?></h3>	

    <dl class="search-results">		
	    <?php foreach ($this->results as $result) : ?>
		    <dt class="result-title">
			    <?php echo $this->pagination->limitstart + $result->count.'. ';?>
			    <?php if ($result->href) : ?>
			    <a href="<?php echo JRoute :: _($result->href) ?>">
					    <?php echo $this->escape($result->title); ?>
			    </a>
			    <?php else : ?>
				    <?php echo $this->escape($result->title); ?>
			    <?php endif; ?>
		    </dt>
		    <?php if ($result->section) : ?>
		    <dd class="result-category">
			    <?php echo JText::_('Category') ?>:
			    <span>
				    <?php echo $this->escape($result->section); ?>
			    </span>	
		    </dd>			
		    <?php endif; ?>
		    <dd class="result-text">
			    <?php echo $result->text; ?>
		    </dd>
		    <?php if ( $this->params->get( 'show_date' )) : ?>
		    <time class="result-created">
			    <?php echo $result->created; ?>	
		    </time>
		    <?php endif; ?>
	    <?php endforeach; ?>
    </dl>

    <nav class="pagination">
	    <?php echo $this->pagination->getPagesLinks(); ?>
    </nav>

<?php }
