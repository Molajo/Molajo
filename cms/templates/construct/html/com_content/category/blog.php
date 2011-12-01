<?php defined('_JEXEC') or die;
/**
 * @package        Unified HTML5 Template Framework for Joomla!
 * @author        Cristina Solana http://nightshiftcreative.com
 * @author        Matt Thomas http://construct-framework.com | http://betweenbrain.com
 * @copyright    Copyright (C) 2009 - 2011 Matt Thomas. All rights reserved.
 * @license        GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 */

if (substr(JVERSION, 0, 3) >= '1.6') {
    // Joomla! 1.6+

    JHtml::addIncludePath(JPATH_COMPONENT . '/helpers'); ?>

<section class="blog<?php echo $this->pageclass_sfx;?>">
    <?php if ($this->params->get('show_page_heading', 1)) : ?>
	    <hgroup>
	        <h1>
                <?php echo $this->escape($this->params->get('page_heading')); ?>
            </h1>
    <?php endif; ?>

    <?php if ($this->params->get('show_category_title', 1) OR $this->params->get('page_subheading')) : ?>
    <h2>
        <?php echo $this->escape($this->params->get('page_subheading')); ?>
        <?php if ($this->params->get('show_category_title')) : ?>
        <span class="subheading-category"><?php echo $this->category->title;?></span>
        <?php endif; ?>
    </h2>
	    </hgroup>
	    <?php endif; ?>

    <?php if ($this->params->get('show_description', 1) || $this->params->def('show_description_image', 1)) : ?>
    <section class="category-desc clearfix">
        <?php if ($this->params->get('show_description_image') && $this->category->getParams()->get('image')) : ?>
        <img src="<?php echo $this->category->getParams()->get('image'); ?>"/>
        <?php endif; ?>
        <?php if ($this->params->get('show_description') && $this->category->description) : ?>
        <?php echo JHtml::_('content.prepare', $this->category->description); ?>
        <?php endif; ?>
    </section>
    <?php endif; ?>

    <?php $leadingcount = 0; ?>
    <?php if (!empty($this->lead_items)) : ?>
    <section class="items-leading">
        <?php foreach ($this->lead_items as &$item) : ?>
        <article class="leading-<?php echo $leadingcount; ?><?php echo $item->state == 0 ? ' system-unpublished'
                : null; ?>">
            <?php
                                    $this->item = &$item;
            echo $this->loadTemplate('item');
            ?>
        </article>
            <?php
                                $leadingcount++;
        ?>
        <?php endforeach; ?>
    </section>
    <?php endif; ?>
    <?php
                $introcount = (count($this->intro_items));
    $counter = 0;
    ?>
    <?php if (!empty($this->intro_items)) : ?>
    <section class="items-intro">

        <?php foreach ($this->intro_items as $key => &$item) : ?>

        <?php
                        $key = ($key - $leadingcount) + 1;
        $rowcount = (((int)$key - 1) % (int)$this->columns) + 1;
        $row = $counter / $this->columns;

        if ($rowcount == 1) : ?>
				<div class="items-row cols-<?php echo (int)$this->columns;?> <?php echo 'row-' . $row; ?> clearfix">
		        <?php endif; ?>

        <article class="item column-<?php echo $rowcount;?><?php echo $item->state == 0 ? ' system-unpublished"'
                : null; ?>">
        <?php
                                    $this->item = &$item;
            echo $this->loadTemplate('item');
            ?>
        </article>

        <?php $counter++; ?>

        <?php if (($rowcount == $this->columns) or ($counter == $introcount)): ?>
					</div>					
				<?php endif; ?>
        <?php endforeach; ?>
    </section>
    <?php endif; ?>


    <?php if (!empty($this->link_items)) : ?>

    <?php echo $this->loadTemplate('links'); ?>

    <?php endif; ?>

    <?php if (!empty($this->children[$this->category->id]) && $this->maxLevel != 0) : ?>
    <section class="cat-children">
        <h3>
            <?php echo JTEXT::_('JGLOBAL_SUBCATEGORIES'); ?>
        </h3>
        <?php echo $this->loadTemplate('children'); ?>
    </section>
    <?php endif; ?>

    <?php if (($this->params->def('show_pagination', 1) == 1 || ($this->params->get('show_pagination') == 2)) && ($this->pagination->get('pages.total') > 1)) : ?>
    <nav class="pagination">
        <?php  if ($this->params->def('show_pagination_results', 1)) : ?>
        <p class="counter">
            <?php echo $this->pagination->getPagesCounter(); ?>
        </p>
        <?php endif; ?>

        <?php echo $this->pagination->getPagesLinks(); ?>
    </nav>
    <?php endif; ?>

</section>

    <?php

}
else {
    // Joomla! 1.5 Output
    $cparams = JComponentHelper::getParams('com_media');
    ?>

<section class="blog<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">

    <?php if ($this->params->get('show_page_title')) : ?>
    <h1>
        <?php echo $this->escape($this->params->get('page_title')); ?>
    </h1>
    <?php endif; ?>

    <?php if ($this->params->def('show_description', 1) || $this->params->def('show_description_image', 1)) : ?>
    <p class="category-desc">
        <?php if ($this->params->get('show_description_image') && $this->category->image) : ?>
        <img src="<?php echo $this->baseurl . '/' . $cparams->get('image_path') . '/' . $this->category->image; ?>"
             class="image_<?php echo $this->category->image_position; ?>"/>
        <?php endif; ?>
        <?php if ($this->params->get('show_description') && $this->category->description) : ?>
        <?php echo $this->category->description; ?>
        <?php endif; ?>
    </p>
    <?php endif; ?>

    <?php
            $i = $this->pagination->limitstart;
    $rowcount = $this->params->def('num_leading_articles', 1);?>
    <section class="items-leading">
        <?php for ($y = 0; $y < $rowcount && $i < $this->total; $y++, $i++) : ?>
        <article class="leading-<?php echo $y;?>">
            <?php $this->item =& $this->getItem($i, $this->params);
            echo $this->loadTemplate('item'); ?>
        </article>
        <?php endfor; ?>
    </section>

    <?php $introcount = $this->params->def('num_intro_articles', 4);
    if ($introcount) :
        $colcount = (int)$this->params->def('num_columns', 2);
        if ($colcount == 0) :
            $colcount = 1;
        endif;
        $rowcount = (int)$introcount / $colcount;
        $ii = 0;?>
        <section class="items-intro">
            <?php for ($y = 0; $y < $rowcount && $i < $this->total; $y++) : ?>
            <div class="items-row cols-<?php echo $colcount; ?> row-<?php echo $y; ?> clearfix">
                <?php for ($z = 0; $z < $colcount && $ii < $introcount && $i < $this->total; $z++, $i++, $ii++) : ?>
                <article class="item column-<?php echo $z + 1; ?>">
                    <?php $this->item =& $this->getItem($i, $this->params);
                    echo $this->loadTemplate('item'); ?>
                </article>
                <?php endfor; ?>
            </div>
            <?php endfor;?>
        </section>
        <?php endif; ?>

    <?php $numlinks = $this->params->def('num_links', 4);
    if ($numlinks && $i < $this->total) : ?>
        <?php $this->links = array_slice($this->items, $i - $this->pagination->limitstart, $i - $this->pagination->limitstart + $numlinks);
        echo $this->loadTemplate('links'); ?>
        <?php endif; ?>

    <?php if ($this->params->def('show_pagination', 2) == 1 || ($this->params->get('show_pagination') == 2 && $this->pagination->get('pages.total') > 1)) : ?>
    <nav class="pagination">
        <?php if ($this->params->def('show_pagination_results', 1)) : ?>
        <p class="counter">
            <?php echo $this->pagination->getPagesCounter(); ?>
        </p>
        <?php endif; ?>
        <?php echo $this->pagination->getPagesLinks(); ?>
    </nav>
    <?php endif; ?>
</section>

<?php }
