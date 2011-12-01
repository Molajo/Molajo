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
    ?>
<nav class="items-more">
    <h3><?php echo JText::_('CONTENT_MORE_ARTICLES'); ?></h3>
    <ol>
        <?php
                    foreach ($this->link_items as &$item) :
        ?>
        <li>
            <a href="<?php echo JRoute::_(ContentHelperRoute::getArticleRoute($item->slug, $item->catid)); ?>">
                <?php echo $item->title; ?></a>
        </li>
        <?php endforeach; ?>
    </ol>
</nav>

<?php	
}
else {
    // Joomla! 1.5
    ?>
<nav class="items-more">
    <h3>
        <?php echo JText::_('More Articles...'); ?>
    </h3>
    <ol>
        <?php foreach ($this->links as $link) : ?>
        <li>
            <a href="<?php echo JRoute::_(ContentHelperRoute::getArticleRoute($link->slug, $link->catslug, $link->sectionid)); ?>">
                <?php echo $this->escape($link->title); ?></a>
        </li>
        <?php endforeach; ?>
    </ol>
</nav>
<?php }
