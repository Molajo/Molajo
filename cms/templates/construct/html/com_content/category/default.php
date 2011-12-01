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

    //Access template parameters
    $templateParams = JFactory::getApplication()->getTemplate(true)->params;
    $outputType = $templateParams->get('coreOutput');
    ?>

<section class="category-list<?php echo $this->pageclass_sfx;?>">
    <?php if (($this->params->get('show_page_heading', 1)) && ($this->params->get('show_category_title', 1) OR $this->params->get('page_subheading'))) : ?>
		<header>
		    <hgroup>
		<?php elseif (($this->params->get('show_page_heading', 1)) || ($this->params->get('show_category_title', 1) OR $this->params->get('page_subheading')))  : ?>
		<header>
		<?php endif; ?>

    <?php if ($this->params->get('show_page_heading', 1)) : ?>
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
    <?php endif; ?>

    <?php if (($this->params->get('show_page_heading', 1)) && ($this->params->get('show_category_title', 1) OR $this->params->get('page_subheading'))) : ?>
			</hgroup>
		</header>
		<?php elseif (($this->params->get('show_page_heading', 1)) || ($this->params->get('show_category_title', 1) OR $this->params->get('page_subheading')))  : ?>
    </header>
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

    <section class="cat-items">
        <?php echo $this->loadTemplate('articles'); ?>
    </section>

    <?php if (!empty($this->children[$this->category->id]) && $this->maxLevel != 0) : ?>
    <section class="cat-children">
        <h3>
            <?php echo JTEXT::_('JGLOBAL_SUBCATEGORIES'); ?>
        </h3>

        <?php echo $this->loadTemplate('children'); ?>
    </section>
    <?php endif; ?>
</section>

<?php

}
else {
    // Joomla! 1.5
    $cparams = JComponentHelper::getParams('com_media');
    ?>

<section class="category-list<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
    <?php if ($this->params->get('show_page_title', 1)) : ?>
    <h1>
        <?php echo $this->escape($this->params->get('page_title')); ?>
    </h1>
    <?php endif; ?>

    <?php if ($this->params->def('show_description', 1) || $this->params->def('show_description_image', 1)) : ?>
    <section class="category-desc clearfix">
        <?php if ($this->params->get('show_description_image') && $this->category->image) : ?>
        <img src="<?php echo $this->baseurl . '/' . $cparams->get('image_path') . '/' . $this->category->image; ?>"
             class="image_<?php echo $this->category->image_position; ?>"/>
        <?php endif; ?>
        <p class="category-desc-text">
            <?php if ($this->params->get('show_description') && $this->category->description) : ?>
            <?php echo $this->category->description; ?>
            <?php endif; ?>
        </p>
    </section>
    <?php endif; ?>

    <section class="cat-items">
        <?php $this->items =& $this->getItems(); ?>
        <?php echo $this->loadTemplate('items'); ?>
    </section>

</section>

<?php }
