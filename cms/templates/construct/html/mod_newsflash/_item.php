<?php defined('_JEXEC') or die;
/**
 * @package        Unified HTML5 Template Framework for Joomla!
 * @author        Cristina Solana http://nightshiftcreative.com
 * @author        Matt Thomas http://construct-framework.com | http://betweenbrain.com
 * @copyright    Copyright (C) 2009 - 2011 Matt Thomas. All rights reserved.
 * @license        GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 */
?>

<?php if ($params->get('item_title')) : ?>
<h5>
    <?php if ($params->get('link_titles') && (isset($item->linkOn))) : ?>
    <a href="<?php echo JRoute::_($item->linkOn); ?>"
       class="newsflash-title<?php echo $params->get('moduleclass_sfx'); ?>">
        <?php echo $item->title; ?></a>
    <?php  else :
    echo $item->title;
endif; ?>
</h5>
<?php endif; ?>

<?php if (!$params->get('intro_only')) : ?>
<?php echo $item->afterDisplayTitle; ?>
<?php endif; ?>

<?php echo $item->beforeDisplayContent; ?>

<?php echo JFilterOutput::ampReplace($item->text); ?>

<?php $itemparams = new JParameter($item->attribs); ?>
<?php $readmoretxt = $itemparams->get('readmore', JText::_('Read more text')); ?>

<?php if (isset($item->linkOn) && $item->readmore && $params->get('readmore')) : ?>
<a href="<?php echo $item->linkOn; ?>">
    <?php echo $readmoretxt . ' ' . $item->title; ?>
</a>
<?php endif; ?>