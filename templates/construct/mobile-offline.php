<?php defined('_JEXEC') or die;
/**
* @package		Template Framework for Molajo 1.6
* @author		Joomla Engineering http://joomlaengineering.com
* @copyright	Copyright (C) 2010, 2011 Matt Thomas | Joomla Engineering. All rights reserved.
* @license		GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
*/	

// To enable use of site configuration
$app 					= JFactory::getApplication();
// Get the base URL of the website
$baseUrl 				= JURI::base();
?>

<!DOCTYPE html> 
<html>
	<head>
		<meta http-equiv="Content-Type" content="<?php echo $contenttype; ?>; charset=utf-8" />
		<link rel="stylesheet" href="<?php echo $baseUrl.'templates/'.$this->template; ?>/css/mobile.css" type="text/css" media="screen" />
		<link rel="stylesheet" href="http://code.jquery.com/mobile/1.0a4.1/jquery.mobile-1.0a4.1.min.css" />
		<script src="http://code.jquery.com/jquery-1.5.2.min.js"></script>
		<script src="http://code.jquery.com/mobile/1.0a4.1/jquery.mobile-1.0a4.1.min.js"></script>
		<script>
			$(document).ready(function() {
				$('body').removeClass("noscript");
			});
		</script>
	</head>

<body class="noscript">	
	<div data-role="page" data-theme="<?php echo $mPageDataTheme; ?>">
		<div id="header" data-role="header" data-theme="<?php echo $mHeaderDataTheme; ?>">
			<h1><a href="<?php echo $baseUrl; ?>/" title="<?php echo $app->getCfg('sitename'); ?>"><?php echo $app->getCfg('sitename'); ?></a></h1>
		</div>
	
		<?php if ( $mNavPosition && ($this->countModules('nav'))) : ?>
			<div id="nav">
				<jdoc:include type="modules" name="nav" style="raw" />
			</div><!-- end nav-->
		<?php endif; ?>
		
		<div id="content-container" data-role="content" data-theme="<?php echo $mContentDataTheme; ?>">	  
	
			<?php if ($this->getBuffer('message')) : ?>
				<jdoc:include type="message" />
			<?php endif; ?>
			<p>
				<?php echo $app->getCfg('offline_message'); ?>
			</p>
			<form action="index.php" method="post" name="login" id="form-login">
			<fieldset class="input">
				<p id="form-login-username">
					<label for="username"><?php echo JText::_('JGLOBAL_USERNAME') ?></label>
					<input name="username" id="username" type="text" class="inputbox" alt="<?php echo JText::_('JGLOBAL_USERNAME') ?>" size="18" />
				</p>
				<p id="form-login-password">
					<label for="passwd"><?php echo JText::_('JGLOBAL_PASSWORD') ?></label>
					<input type="password" name="password" class="inputbox" size="18" alt="<?php echo JText::_('JGLOBAL_PASSWORD') ?>" id="passwd" />
				</p>
				<p id="form-login-remember">
					<label for="remember"><?php echo JText::_('JGLOBAL_REMEMBER_ME') ?></label>
					<input type="checkbox" name="remember" class="inputbox" value="yes" alt="<?php echo JText::_('JGLOBAL_REMEMBER_ME') ?>" id="remember" />
				</p>
				<input type="submit" name="Submit" class="button" value="<?php echo JText::_('JLOGIN') ?>" />
				<input type="hidden" name="option" value="com_users" />
				<input type="hidden" name="task" value="user.login" />
				<input type="hidden" name="return" value="<?php echo base64_encode(JURI::base()) ?>" />
				<?php echo JHtml::_('form.token'); ?>
			</fieldset>
			</form>						
			
		</div>
		
		<?php if ( !$mNavPosition && ($this->countModules('nav'))) : ?>
			<div id="nav">
				<jdoc:include type="modules" name="nav" style="raw" />
			</div><!-- end nav-->
		<?php endif; ?>		
									
		<div id="footer" data-role="footer" data-theme="<?php echo $mFooterDataTheme; ?>">
			<?php if ($this->countModules('footer')) : ?>
				<jdoc:include type="modules" name="footer" style="xhtml" />
			<?php endif; ?>
		</div>
	</div>
	  
</body>
</html>
