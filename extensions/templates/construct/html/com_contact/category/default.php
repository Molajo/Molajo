<?php defined('_JEXEC') or die;
/**
* @package		Unified HTML5 Template Framework for Joomla!
* @author		Cristina Solana http://nightshiftcreative.com
* @author		Matt Thomas http://construct-framework.com | http://betweenbrain.com
* @copyright	Copyright (C) 2009 - 2011 Matt Thomas. All rights reserved.
* @license		GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
*/

if (substr(JVERSION, 0, 3) >= '1.6') {
//Joomla 1.6+
?>
 
	<section class="contact-category<?php echo $this->pageclass_sfx;?>">
	<?php if ($this->params->def('show_page_heading', 1)) : ?>
		<h1>
			<?php echo $this->escape($this->params->get('page_heading')); ?>
		</h1>
	<?php endif; ?>
	<?php if($this->params->get('show_category_title', 1)) : ?>
		<h2>
			<?php echo JHtml::_('content.prepare', $this->category->title); ?>
		</h2>
	<?php endif; ?>
	<?php if ($this->params->def('show_description', 1) || $this->params->def('show_description_image', 1)) : ?>
		<section class="category-desc clearfix">
		    <?php if ($this->params->get('show_description_image') && $this->category->getParams()->get('image')) : ?>
			    <img src="<?php echo $this->category->getParams()->get('image'); ?>"/>
		    <?php endif; ?>
		    <?php if ($this->params->get('show_description') && $this->category->description) : ?>
			    <?php echo JHtml::_('content.prepare', $this->category->description); ?>
		    <?php endif; ?>
		</section>
	<?php endif; ?>

	<?php echo $this->loadTemplate('items'); ?>

	<?php if (!empty($this->children[$this->category->id])&& $this->maxLevel != 0) : ?>
		<section class="cat-children">
			<h3><?php echo JText::_('JGLOBAL_SUBCATEGORIES') ; ?></h3>
			<?php echo $this->loadTemplate('children'); ?>
		</section>
	<?php endif; ?>
	</section> 
 
<?php
}
else {
// Joomla 1.5
?>

	<section class="contact-category<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">

		<?php if ($this->params->get('show_page_title',1)) : ?>
			<h1>
				<?php echo $this->escape($this->params->get('page_title')); ?>
			</h1>
		<?php endif; ?>

		<?php if ($this->category->image || $this->category->description) : ?>
			<section class="category-desc clearfix">
				<?php if ($this->params->get('image') != -1 && $this->params->get('image') != '') : ?>
					<img src="<?php echo $this->baseurl .'/'. 'images/stories' . '/'. $this->params->get('image'); ?>" class="image_<?php echo $this->params->get('image_align'); ?>" alt="<?php echo JText::_( 'Contacts' ); ?>" />
				<?php elseif($this->category->image): ?>
					<img src="<?php echo $this->baseurl .'/'. 'images/stories' . '/'. $this->category->image; ?>" class="image_<?php echo $this->category->image_position; ?>" alt="<?php echo JText::_( 'Contacts' ); ?>" />
				<?php endif; ?>
				<?php echo $this->category->description; ?>
			</section>
		<?php endif; ?>

		<script type="text/javascript">
		function tableOrdering( order, dir, task )
		{
			var form = document.adminForm;
		
			form.filter_order.value	 = order;
			form.filter_order_Dir.value	= dir;
			document.adminForm.submit( task );
		}
		</script>

		<form action="<?php echo $this->action; ?>" method="post" name="adminForm">
				<?php if ($this->params->get('display')) : ?>
				<p class="display">
					<?php echo JText::_('Display Num'); ?>&nbsp;
				</p>
				<?php endif; ?>
			<input type="hidden" name="catid" value="<?php echo (int)$this->category->id; ?>" />
			<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
			<input type="hidden" name="filter_order_Dir" value="" />			
		</form>

		<table class="category">
			<tbody>
				<?php if ($this->params->get('show_headings')) : ?>
					<thead>
						<tr>	
							<th id="item-count">
								<?php echo JText::_('Num'); ?>
							</th>
					
							<?php if ($this->params->get('show_position')) : ?>
							<th id="item-position">
								<?php echo JHTML::_('grid.sort', 'Position', 'cd.con_position', $this->lists['order_Dir'], $this->lists['order'] ); ?>
							</th>
							<?php endif; ?>
					
							<th id="item-name">
								<?php echo JHTML::_('grid.sort', 'Name', 'cd.name', $this->lists['order_Dir'], $this->lists['order'] ); ?>
							</th>
					
							<?php if ($this->params->get('show_email')) : ?>
							<th id="item-email">
								<?php echo JText::_('Email'); ?>
							</th>
							<?php endif; ?>
					
							<?php if ( $this->params->get('show_telephone')) : ?>
							<th id="item-phone">
								<?php echo JText::_('Phone'); ?>
							</th>
							<?php endif; ?>
					
							<?php if ($this->params->get('show_mobile')) : ?>
							<th id="item-fax">
								<?php echo JText::_('Mobile'); ?>
							</th>
							<?php endif; ?>
					
							<?php if ( $this->params->get('show_fax')) : ?>
							<th id="item-mobile">
								<?php echo JText::_('Fax'); ?>
							</th>
							<?php endif; ?>
						</tr>
					</thead>
				<?php endif; ?>		
		
				<?php echo $this->loadTemplate('items'); ?>
			</tbody>
		</table>

		<nav class="pagination">
			<p class="counter">
				<?php echo $this->pagination->getPagesCounter(); ?>
			</p>	
			<?php echo $this->pagination->getPagesLinks(); ?>
		</nav>

	</section>
<?php }
