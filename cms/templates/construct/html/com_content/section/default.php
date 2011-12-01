<?php defined('_JEXEC') or die;
/**
* @package		Unified HTML5 Template Framework for Joomla!
* @author		Cristina Solana http://nightshiftcreative.com
* @author		Matt Thomas http://construct-framework.com | http://betweenbrain.com
* @copyright	Copyright (C) 2009 - 2011 Matt Thomas. All rights reserved.
* @license		GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
*/

$cparams = JComponentHelper::getParams ('com_media'); ?>

<section class="section-list<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">

	<?php if ($this->params->get('show_page_title',1)) : ?>
	    <h1>
		    <?php echo $this->escape($this->params->get('page_title')); ?>
	    </h1>
	<?php endif; ?>
	
	<?php if ($this->params->def('show_description', 1) || $this->params->def('show_description_image', 1)) : ?>
		<section class="section-desc clearfix">
			<?php if ($this->params->get('show_description_image') && $this->section->image) : ?>
				<img src="<?php echo $this->baseurl . '/' . $this->escape($cparams->get('image_path')).'/'.$this->escape($this->section->image); ?>" class="image_<?php echo $this->escape($this->section->image_position); ?>" />
			<?php endif; ?>
			<p class="section-desc-text">
				<?php if ($this->params->get('show_description') && $this->section->description) : ?>
					<?php echo $this->section->description; ?>
				<?php endif; ?>
			</p>
		</section>
	<?php endif; ?>
	
	<?php if ($this->params->def('show_categories', 1) && count($this->categories)) : ?>
	    <ul>
		    <?php foreach ($this->categories as $category) : ?>
			    <?php if (!$this->params->get('show_empty_categories') && !$category->numitems) : ?>
				    <?php continue; ?>
			    <?php endif; ?>
			    <li>
				    <span class="item-title">
					    <a href="<?php echo $category->link; ?>" class="category">
						    <?php echo $this->escape($category->title); ?>
					    </a>
				    </span>
				    <?php if ($this->params->get('show_cat_num_articles')) : ?>
				    <dl>
					    <dt>
						    <?php echo JText::_('Number of items') ; ?>:
					    </dt>
					    <dd>
						    <?php if ($category->numitems==1) {
							    echo $category->numitems ." ". JText::_( 'item' );	}
						    else {
							    echo $category->numitems ." ". JText::_( 'items' );} ?>
					    </dd>
				    </dl>
				    <?php endif; ?>
	
				    <?php if ($this->params->def('show_category_description', 1) && $category->description) : ?>
				    <div class="category-desc">
					    <?php echo $category->description; ?>
				    </div>
				    <?php endif; ?>
		    </li>
		    <?php endforeach; ?>
	    </ul>
	<?php endif; ?>
</section>
