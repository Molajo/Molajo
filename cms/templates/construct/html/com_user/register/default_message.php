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

<section class="registration-complete<?php echo $this->escape($this->params->get('pageclass_sfx')) ?>">
    <h2>
        <?php echo $this->escape($this->message->title); ?>
    </h2>

    <p class="message">
        <?php echo $this->escape($this->message->text); ?>
    </p>
</section>
