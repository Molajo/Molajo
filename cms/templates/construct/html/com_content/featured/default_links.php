<?php
/**
 * @version        $Id: default_links.php 20196 2011-01-09 02:40:25Z ian $
 * @package        Joomla.Site
 * @subpackage    com_content
 * @copyright    Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
?>
<h3><?php echo JText::_('CONTENT_MORE_ARTICLES'); ?></h3>

<ol>
    <?php foreach ($this->link_items as &$item) : ?>
    <li>
        <a href="<?php echo JRoute::_(ContentHelperRoute::getArticleRoute($item->slug, $item->catslug)); ?>">
            <?php echo $item->title; ?></a>
    </li>
    <?php endforeach; ?>
</ol>