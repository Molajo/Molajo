<?php
/**
 * @package     Molajo
 * @subpackage  Template
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2011 Individual Molajo Contributors. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;
$app = MolajoFactory::getApplication();
JHtml::_('behavior.noframes');
JHtml::_('behavior.framework');

$this->document->addStyleSheet($urlPath.'/css/'.$file);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>" >
<head>
<jdoc:include type="head" />

<link rel="stylesheet" href="templates/system/css/system.css" type="text/css" />
<link href="templates/<?php echo $this->template ?>/css/template.css" rel="stylesheet" type="text/css" />

<?php  if ($this->direction == 'rtl') : ?>
	<link href="templates/<?php echo $this->template ?>/css/template_rtl.css" rel="stylesheet" type="text/css" />
<?php  endif; ?>

<!--[if IE 7]>
<link href="templates/<?php echo  $this->template ?>/css/ie7.css" rel="stylesheet" type="text/css" />
<![endif]-->

<?php  if ($this->params->get('useRoundedCorners')) : ?>
	<link rel="stylesheet" type="text/css" href="templates/<?php echo $this->template ?>/css/rounded.css" />
<?php  else : ?>
	<link rel="stylesheet" type="text/css" href="templates/<?php echo $this->template ?>/css/norounded.css" />
<?php  endif; ?>

<script type="text/javascript">
	window.addEvent('domready', function () {
		document.getElementById('form-login').username.select();
		document.getElementById('form-login').username.focus();
	});
</script>
</head>
<body>
	<div id="border-top" class="h_blue">
		<div>
			<div>
				<span class="title"><a href="index.php"><?php echo $this->params->get('showSiteName') ? $app->getCfg('sitename') : JText::_('TPL_MOLAJO_HEADER'); ?></a></span>
			</div>
		</div>
	</div>
	<div id="content-box">
		<div class="padding">
			<div class="clr"></div>
			<div id="element-box" class="login">
				<div class="t">
					<div class="t">
						<div class="t"></div>
					</div>
				</div>
				<div class="m wbg">
					<h1><?php echo JText::_('COM_LOGIN_ADMINISTRATION_LOGIN') ?></h1>
					<jdoc:include type="message" />
					<jdoc:include type="component" />
					<p><?php echo JText::_('COM_LOGIN_VALID') ?></p>
					<p><a href="<?php echo JURI::root(); ?>"><?php echo JText::_('COM_LOGIN_RETURN_TO_SITE_HOME_PAGE') ?></a></p>
					<div id="lock"></div>
					<div class="clr"></div>
				</div>
				<div class="b">
					<div class="b">
						<div class="b"></div>
					</div>
				</div>
			</div>
			<noscript>
				<?php echo JText::_('JGLOBAL_WARNJAVASCRIPT') ?>
			</noscript>
			<div class="clr"></div>
		</div>
	</div>
	<div id="border-bottom"><div><div></div></div>
</div>
<div id="footer">
	<p class="copyright">
		<?php $molajo = '<a href="http://molajo.org">Molajo&#174;</a>';
		echo JText::sprintf('MOLAJO_IS_FREESOFTWARE', $molajo) ?>
	</p>
</div>
</body>
</html>
