<?php defined('_JEXEC') or die;
/**
* @package		Unified HTML5 Template Framework for Joomla!
* @author		Cristina Solana http://nightshiftcreative.com
* @author		Matt Thomas http://construct-framework.com | http://betweenbrain.com
* @copyright	Copyright (C) 2009 - 2011 Matt Thomas. All rights reserved.
* @license		GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
*/

// Joomla 1.5 only

?>

<?php if($this->params->get('show_page_title',1)) : ?>
	<h1>
		<?php echo $this->params->get('page_title') ?>
	</h1>
<?php endif; ?>

<?php echo $this->loadTemplate( $this->type ); ?>
