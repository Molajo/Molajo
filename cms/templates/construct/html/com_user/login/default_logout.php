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

<section class="logout<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">

    <?php if ($this->params->get('show_logout_title')) : ?>
    <h1>
        <?php echo $this->params->get('header_logout'); ?>
    </h1>
    <?php endif; ?>

    <?php if ($this->params->get('description_logout') || isset($this->image)) : ?>
    <p class="logout-description clearfix">
        <?php if ($this->params->get('description_logout')) : ?>
        <?php echo $this->params->get('description_logout_text'); ?>
        <?php endif; ?>
        <?php if (isset ($this->image)) : ?>
        <?php echo $this->image; ?>
        <?php endif; ?>
    </p>
    <?php endif; ?>

    <form action="<?php echo JRoute::_('index.php'); ?>" method="post">
        <button type="submit" name="submit" class="button"><?php echo JText::_('Logout'); ?></button>
        <input type="hidden" name="option" value="com_user"/>
        <input type="hidden" name="task" value="logout"/>
        <input type="hidden" name="return" value="<?php echo $this->return; ?>"/>
    </form>
</section>
