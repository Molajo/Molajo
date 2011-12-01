<?php defined('_JEXEC') or die;
/**
 * @version        $Id: default_items.php 21321 2011-05-11 01:05:59Z dextercowley $
 * @package        Joomla.Site
 * @subpackage    com_contact
 * @copyright    Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

// Joomla 1.6+ only

$class = ' class="first"';

?>

<?php if (count($this->items[$this->parent->id]) > 0 && $this->maxLevelcat != 0) : ?>

<ol>
    <?php foreach ($this->items[$this->parent->id] as $id => $item) : ?>
    <?php
            if ($this->params->get('show_empty_categories_cat') || $item->numitems || count($item->getChildren())) :
        if (!isset($this->items[$this->parent->id][$id + 1])) {
            $class = ' class="last"';
        }
        ?>
        <li<?php echo $class; ?>>
            <?php $class = ''; ?>
            <h4 class="item-title"><a href="<?php echo JRoute::_(ContactHelperRoute::getCategoryRoute($item->id));?>">
                <?php echo $this->escape($item->title); ?></a>
            </h4>

            <?php if ($this->params->get('show_subcat_desc_cat') == 1) : ?>
            <?php if ($item->description) : ?>
                <section class="category-desc">
                    <?php echo JHtml::_('content.prepare', $item->description); ?>
                </section>
                <?php endif; ?>
            <?php endif; ?>

            <?php if ($this->params->get('show_cat_items_cat') == 1) : ?>
            <dl>
                <dt><?php echo JText::_('CONTACT_COUNT'); ?></dt>
                <dd><?php echo $item->numitems; ?></dd>
            </dl>
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
</ol>
<?php endif; ?>
