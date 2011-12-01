<?php defined('_JEXEC') or die;
/**
* @package		Unified HTML5 Template Framework for Joomla!
* @author		Cristina Solana http://nightshiftcreative.com
* @author		Matt Thomas http://construct-framework.com | http://betweenbrain.com
* @copyright	Copyright (C) 2009 - 2011 Matt Thomas. All rights reserved.
* @license		GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
*/

if (substr(JVERSION, 0, 3) >= '1.6') {
// Joomla 1.6+ ?>

    <?php if($this->error): ?>
        <section class="error">
	        <?php echo $this->escape($this->error); ?>
        </section>
    <?php endif; ?>

<?php
}
else {
// Joomla 1.5 ?>

    <?php if($this->error): ?>
	    <section class="error">
		    <?php echo $this->escape($this->error); ?>
	    </section>
    <?php endif; ?>

<?php }
