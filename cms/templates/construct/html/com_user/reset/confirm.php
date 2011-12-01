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


<section class="reset-confirm<?php echo $this->escape($this->params->get('pageclass_sfx')) ?>">
    <h2>
        <?php echo JText::_('Confirm your Account'); ?>
    </h2>

    <form action="<?php echo JRoute::_('index.php?option=com_user&task=confirmreset'); ?>" method="post"
          class="josForm form-validate">
        <fieldset>
            <p>
                <?php echo JText::_('RESET_PASSWORD_CONFIRM_DESCRIPTION'); ?>
            </p>
            <label for="username" class="hasTip"
                   title="<?php echo JText::_('RESET_PASSWORD_USERNAME_TIP_TITLE'); ?>::<?php echo JText::_('RESET_PASSWORD_USERNAME_TIP_TEXT'); ?>">
                <?php echo JText::_('User Name'); ?>:
                <input id="username" name="username" type="text" class="required" size="36">
            </label>
            <label for="token" class="hasTip"
                   title="<?php echo JText::_('RESET_PASSWORD_TOKEN_TIP_TITLE'); ?>::<?php echo JText::_('RESET_PASSWORD_TOKEN_TIP_TEXT'); ?>">
                <?php echo JText::_('Token'); ?>:
                <input id="token" name="token" type="text" class="required" size="36">
            </label>
        </fieldset>
        <button type="submit" class="validate"><?php echo JText::_('Submit'); ?></button>
        <?php echo JHTML::_('form.token'); ?>
    </form>
</section>