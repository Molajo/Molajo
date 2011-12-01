<?php defined('_JEXEC') or die;
/**
 * @package        Unified HTML5 Template Framework for Joomla!
 * @author        Cristina Solana http://nightshiftcreative.com
 * @author        Matt Thomas http://construct-framework.com | http://betweenbrain.com
 * @copyright    Copyright (C) 2009 - 2011 Matt Thomas. All rights reserved.
 * @license        GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 */

// Joomla 1.5 only

?>

<h4>
    <?php echo $poll->title; ?>
</h4>
<form name="form2" method="post" action="index.php" class="poll">
    <fieldset>
        <?php for ($i = 0, $n = count($options); $i < $n; $i++) : ?>
        <label for="voteid<?php echo $options[$i]->id; ?>">
            <?php echo $options[$i]->text; ?>
            <input type="radio" name="voteid" id="voteid<?php echo $options[$i]->id; ?>"
                   value="<?php echo $options[$i]->id; ?>" alt="<?php echo $options[$i]->id; ?>">
        </label>
        <?php endfor; ?>
        <button type="submit" name="task_button" class="button">
            <?php echo JText::_('Vote'); ?>
        </button>
        <a href="<?php echo JRoute::_('index.php?option=com_poll&id=' . $poll->slug . $itemid . '#content'); ?>">
            <?php echo JText::_('Results'); ?>
        </a>
    </fieldset>
    <input type="hidden" name="option" value="com_poll">
    <input type="hidden" name="id" value="<?php echo $poll->id; ?>">
    <input type="hidden" name="task" value="vote">
    <?php echo JHTML::_('form.token'); ?>
</form>