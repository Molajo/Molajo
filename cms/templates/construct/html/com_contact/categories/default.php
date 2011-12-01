<?php defined('_JEXEC') or die;
/**
 * @version        $Id: default.php 21097 2011-04-07 15:38:03Z dextercowley $
 * @package        Joomla.Site
 * @subpackage    com_contact
 * @copyright    Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

// Joomla 1.6+ only

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');

?>

<section class="categories-list<?php echo $this->pageclass_sfx;?>">
    <?php if ($this->params->get('show_page_heading', 1)) : ?>
    <h1>
        <?php echo $this->escape($this->params->get('page_heading')); ?>
    </h1>
    <?php endif; ?>

    <?php if ($this->params->get('show_base_description')
) :
    //If there is a description in the menu parameters use that;
    ?>
    <?php if ($this->params->get('categories_description')) : ?>
    <section class="category-desc base-desc">
        <?php echo  JHtml::_('content.prepare', $this->params->get('categories_description')); ?>
    </section>
    <?php  else:
    //Otherwise get one from the database if it exists.
    ?>
    <?php if ($this->parent->description) : ?>
    <section class="category-desc base-desc">
        <?php  echo JHtml::_('content.prepare', $this->parent->description); ?>
    </section>
    <?php endif; ?>
    <?php  endif; ?>
    <?php endif; ?>

    <?php echo $this->loadTemplate('items'); ?>
</section>
