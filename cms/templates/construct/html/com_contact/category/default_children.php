<?php defined('_JEXEC') or die;
/**
 * @version        $Id: default_children.php 21321 2011-05-11 01:05:59Z dextercowley $
 * @package        Joomla.Site
 * @subpackage    com_contact
 * @copyright    Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

// Joomla 1.6+ only

$class = ' class="first"';

?>

<?php if (count($this->children[$this->category->id]) > 0 && $this->maxLevel != 0) : ?>
<ol>
    <?php foreach ($this->children[$this->category->id] as $id => $child) : ?>
    <?php
            if ($this->params->get('show_empty_categories') || $child->numitems || count($child->getChildren())) :
        if (!isset($this->children[$this->category->id][$id + 1])) {
            $class = ' class="last"';
        }
        ?>
        <li<?php echo $class; ?>>
            <?php $class = ''; ?>
            <h4 class="item-title"><a href="<?php echo JRoute::_(ContactHelperRoute::getCategoryRoute($child->id));?>">
                <?php echo $this->escape($child->title); ?></a>
            </h4>

            <?php if ($this->params->get('show_subcat_desc') == 1) : ?>
            <?php if ($child->description) : ?>
                <section class="category-desc">
                    <?php echo JHtml::_('content.prepare', $child->description); ?>
                </section>
                <?php endif; ?>
            <?php endif; ?>

            <?php if ($this->params->get('show_cat_items') == 1) : ?>
            <dl>
                <dt>
                    <?php echo JText::_('CONTACT_CAT_NUM'); ?>
                </dt>
                <dd>
                    <?php echo $child->numitems; ?>
                </dd>
            </dl>
            <?php endif; ?>
            <?php if (count($child->getChildren()) > 0) :
            $this->children[$child->id] = $child->getChildren();
            $this->category = $child;
            $this->maxLevel--;
            echo $this->loadTemplate('children');
            $this->category = $child->getParent();
            $this->maxLevel++;
        endif; ?>
        </li>
        <?php endif; ?>
    <?php endforeach; ?>
</ol>
<?php endif;
