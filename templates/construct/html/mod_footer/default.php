<?php defined('_JEXEC') or die;
/**
* @package		Template Framework for Molajo 1.6
* @author		Joomla Engineering http://joomlaengineering.com
* @copyright	Copyright (C) 2010 Matt Thomas | Joomla Engineering. All rights reserved.
* @license		GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
*/
$app = MolajoFactory::getApplication();
$sitename = $app->getCfg('sitename');
?>
<p id="siteinfo-legal">All rights reserved. &copy; <?php echo $cur_year ?> <a href="<?php echo JURI::base( true ) ?>" title="<?php echo $sitename ?>"><?php echo $sitename ?></a>.<br/>
Developed using the <a href="http://joomlaengineering.com">Construct</a>&trade; Template Development Framework.<br/>Molajo is a registered trademark of Open Source Matters, Inc. - <a class="modal" href="index.php?option=com_content&view=article&id=30&tmpl=modal" rel="{handler: 'iframe', size: {x: 640, y: 480}}">Disclaimer</a></p>
