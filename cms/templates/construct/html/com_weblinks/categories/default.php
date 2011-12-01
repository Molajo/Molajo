<?php defined('_JEXEC') or die;
/**
 * @package        Unified HTML5 Template Framework for Joomla!
 * @author        Cristina Solana http://nightshiftcreative.com
 * @author        Matt Thomas http://construct-framework.com | http://betweenbrain.com
 * @copyright    Copyright (C) 2009 - 2011 Matt Thomas. All rights reserved.
 * @license        GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 */

if (substr(JVERSION, 0, 3) >= '1.6') {
    // Joomla 1.6+
    ?>

<?php JHtml::addIncludePath(JPATH_COMPONENT . '/helpers'); ?>

<section class="categories-list<?php echo $this->pageclass_sfx;?>">
    <?php if ($this->params->get('show_page_heading', 1)) : ?>
    <h1>
        <?php echo $this->escape($this->params->get('page_heading')); ?>
    </h1>
    <?php endif; ?>

    <?php if ($this->params->get('show_base_description')) : ?>
    <?php //If there is a description in the menu parameters use that; ?>
    <?php if ($this->params->get('categories_description')) : ?>
        <p class="category-desc base-desc">
            <?php echo  JHtml::_('content.prepare', $this->params->get('categories_description')); ?>
        </p>
        <?php else: ?>
        <?php //Otherwise get one from the database if it exists. ?>
        <?php if ($this->parent->description) : ?>
            <section class="category-desc base-desc">
                <?php  echo JHtml::_('content.prepare', $this->parent->description); ?>
            </section>
            <?php endif; ?>
        <?php  endif; ?>
    <?php endif; ?>
    <?php
            echo $this->loadTemplate('items');
    ?>
</section>

    <?php

}
else {
    // Joomla 1.5
    $cparams = JComponentHelper::getParams('com_media');
    ?>

<section class="categories-list<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">

    <?php if ($this->params->get('show_page_title', 1)) : ?>
    <h2>
        <?php echo $this->escape($this->params->get('page_title')); ?>
    </h2>
    <?php endif; ?>

    <?php if ($this->params->def('show_comp_description', 1) || $this->params->def('image', -1) != -1) : ?>
    <p class="category-desc base-desc">
        <?php if ($this->params->def('image', -1) != -1) : ?>
        <img src="<?php echo $this->baseurl . $this->escape($cparams->get('image_path')) . '/' . $this->escape($this->params->get('image')); ?>"
             alt="" class="image_<?php echo $this->escape($this->params->get('image_align')); ?>">
        <?php endif; ?>
        <?php if ($this->params->get('show_comp_description')) : ?>
        <?php echo $this->params->get('comp_description'); ?>
        <?php endif; ?>
    </p>
    <?php endif; ?>

    <?php if (count($this->categories)) : ?>
    <ol>
        <?php foreach ($this->categories as $category) : ?>
        <li>
            <h3 class="item-title">
                <a href="<?php echo $category->link; ?>" class="category">
                    <?php echo $this->escape($category->title); ?>
                </a>
            </h3>
            <dl>
                <dt>
                    <?php echo JText::_('Number of links'); ?>:
                </dt>
                <dd>
                    <?php echo (int)$category->numlinks ?>
                </dd>
            </dl>
        </li>
        <?php endforeach; ?>
    </ol>
    <?php endif; ?>
</section>

<?php }
