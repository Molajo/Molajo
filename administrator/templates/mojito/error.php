<?php
/**
 * @package     Molajo
 * @subpackage  Mojito
 * @copyright   Copyright (C) 2011 Cristina Solana. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

$app = MolajoFactory::getApplication();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo  $this->language; ?>" lang="<?php echo  $this->language; ?>" dir="<?php echo  $this->direction; ?>" >
<head>
	<jdoc:include type="head" />
	<link rel="stylesheet" href="templates/system/css/system.css" type="text/css" />
	<link href="templates/<?php echo  $this->template ?>/css/template.css" rel="stylesheet" type="text/css" />

	<?php if ($this->direction == 'rtl') : ?>
		<link href="templates/<?php echo  $this->template ?>/css/template_rtl.css" rel="stylesheet" type="text/css" />
	<?php endif; ?>

	<!--[if gte IE 7]>
	<link href="templates/<?php echo  $this->template ?>/css/ie7.css" rel="stylesheet" type="text/css" />
	<![endif]-->
</head>
<body id="minwidth-body">
	<div id="border-top" class="h_blue">
		<div>
			<div>
				<span class="logo">
                    <a href="http://molajo.org" target="_blank">
                        <img src="<?php echo MOLAJO_BASE_URL; ?>/administrator/templates/<?php echo  $this->template ?>/images/logo.png" alt="Molajo" />
                    </a>
                </span>
			</div>
		</div>
	</div>
	<div id="content-box">
		<div class="border">
			<div class="padding">
				<h1><?php echo $this->error->getCode() ?> - <?php echo JText::_('JERROR_AN_ERROR_HAS_OCCURRED') ?></h1>
				<p><?php echo $this->error->getMessage(); ?></p>
				<p><a href="index.php"><?php echo JText::_('JGLOBAL_TPL_CPANEL_LINK_TEXT') ?></a></p>
				<p><?php if ($this->debug) :
					echo $this->renderBacktrace();
				endif; ?></p>
			</div>
		</div>
	</div>
	<div class="clr"></div>
	<noscript>
			<?php echo  JText::_('JGLOBAL_WARNJAVASCRIPT') ?>
	</noscript>
	<div class="clr"></div>
	<div id="border-bottom"><div><div></div></div></div>

</body>
</html>