<?php defined('_JEXEC') or die;
/**
* @package		Unified HTML5 Template Framework for Joomla!
* @author		Cristina Solana http://nightshiftcreative.com
* @author		Matt Thomas http://construct-framework.com | http://betweenbrain.com
* @copyright	Copyright (C) 2009 - 2011 Matt Thomas. All rights reserved.
* @license		GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
*/

$app = JFactory::getApplication();
$sitename = $app->getCfg('sitename');
?>

<p class="footer-text">All rights reserved. &copy; <?php echo $cur_year ?> <a href="<?php echo JURI::base( true ) ?>" title="<?php echo $sitename ?>"><?php echo $sitename ?></a>.</p>
<p class="footer-text">Developed using the <a href="http://construct-framework.com">Construct&trade; Template Development Framework</a>.</p>
