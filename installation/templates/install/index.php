<?php
/**
 * @package     Molajo
 * @subpackage  Mojito
 * @copyright   Copyright (C) 2011 Cristina Solana. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;
include dirname(__FILE__).'/include/head.php';
$configHTML = true;
if ($configHTML == true): ?>
<!DOCTYPE html>
<?php else: ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php endif; ?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo MOLAJO_PATH_ROOT; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>" >
<head>
    <jdoc:include type="head" />
</head>
<body class="<?php echo $lcbrowser . ' ' . $lcbrowser.$ver . ' ' . strtolower($browser->getPlatform()); ?>">
	<div id="wrap">
		<div id="top">
			<h1>
				<a href="http://www.molajo.org" title="Click here to open the Molajo website in a new window" target="_blank">Molajo
				<span>Click here to view the Molajo website</span></a>
			</h1>
			<strong>Version 1.6.1 <span>Step 2 of 4</span></strong>
		</div>
<!-- <jdoc:include type="modules" name="header" wrap="header" /> -->
<!--  <jdoc:include type="message" /> -->

		<div id="main" class="step2">
			<div class="inner">
                <jdoc:include type="component" />
<!--                <jdoc:include type="modules" name="footer" wrap="footer" /> -->
				<div id="actions">
					<a href="index.html" class="btn-secondary">&laquo; <strong>P</strong>revious</a>
					<a href="step2.html" class="btn-primary"><strong>N</strong>ext &raquo;</a>
				</div>

			</div>
		</div>
	</div>
</body>
<noscript>
    <?php echo MolajoText::_('JGLOBAL_WARNJAVASCRIPT') ?>
</noscript>
</html>