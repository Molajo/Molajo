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

<section class="profile<?php echo $this->escape($this->params->get('pageclass_sfx')) ?>">
    <?php if ($this->params->get('show_page_title', 1)) : ?>
    <header>
        <h2>
            <?php echo $this->escape($this->params->get('page_title')) ?>
        </h2>
    </header>
    <?php endif; ?>
    <p>
        <?php echo JText::_('Welcome!'); ?>
    </p>

    <p>
        <?php echo $this->params->get('welcome_desc', JText::_('WELCOME_DESC'));; ?>
    </p>
</section>