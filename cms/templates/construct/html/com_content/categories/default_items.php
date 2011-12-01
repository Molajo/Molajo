<?php defined('_JEXEC') or die;

/**
 * @version		$Id: default_items.php 20788 2011-02-20 05:54:44Z infograf768 $
 * @package		Joomla.Site
 * @subpackage	com_content
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access

$class = ' class="first"';
if (count($this->items[$this->parent->id]) > 0 && $this->maxLevelcat != 0) : ?>
    <ul>
        <?php foreach($this->items[$this->parent->id] as $id => $item) : ?>
	        <?php
	        if ($this->params->get('show_empty_categories_cat') || $item->numitems || count($item->getChildren())) :
	        if (!isset($this->items[$this->parent->id][$id + 1]))
	        {
		        $class = ' class="last"';
	        }
	        ?>
	        <li<?php echo $class; ?>>
	        <?php $class = ''; ?>
		        <h2 class="item-title"><a href="<?php echo JRoute::_(ContentHelperRoute::getCategoryRoute($item->id));?>">
			        <?php echo $this->escape($item->title); ?></a>
		        </h2>
		        <?php if ($this->params->get('show_subcat_desc_cat') == 1) :?>
		        <?php if ($item->description) : ?>
			        <p class="category-desc">
				        <?php echo JHtml::_('content.prepare', $item->description); ?>
			        </p>
		        <?php endif; ?>
                <?php endif; ?>
		        <?php if ($this->params->get('show_cat_num_articles_cat') == 1) :?>
                    	<p><?php echo JText::_('CONTENT_NUM_ITEMS'); ?>
    				        <span class="category-items-count"><?php echo $item->numitems; ?></span>
				        </p>
		        <?php endif; ?>

		        <?php if (count($item->getChildren()) > 0) :
			        $this->items[$item->id] = $item->getChildren();
			        $this->parent = $item;
			        $this->maxLevelcat--;
			        echo $this->loadTemplate('items');
			        $this->parent = $item->getParent();
			        $this->maxLevelcat++;
		        endif; ?>
	        </li>
	        <?php endif; ?>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
