<?php
/**
 * @package     Molajo
 * @subpackage  Install
 * @copyright   Copyright (C) 2011 Chris Rault. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;
include dirname(__FILE__).'/lib/functions.php';
include dirname(__FILE__).'/lib/head.php';

$configHTML = true;
if ($configHTML == true): ?>
<!DOCTYPE html>
<?php else: ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php endif; ?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
    <jdoc:include type="head" />
</head>
<body class="<?php echo $lcbrowser . ' ' . $lcbrowser.$ver . ' ' . strtolower($browser->getPlatform()); ?>">
	<div id="wrap">
		<div id="top">
			<h1>
				<a href="http://molajo.org" title="Click here to open the Molajo website in a new window" target="_blank">Molajo
				<span>Click here to view the Molajo website</span></a>
			</h1>
			<strong>Version <?php echo $version; ?> <span>Step <?php echo $stepNumber; ?> of 4</span></strong>
		</div>
		<div id="main" class="step1">
            <jdoc:include type="component" />
            <title>Molajo Installer - Step 1 of 4</title>
        </div>
		<?php include dirname(__FILE__).'/lib/footer.php'; ?>
        <!--                <jdoc:include type="module" name="footer" wrap="footer" /> -->
	</div>
</body>
<noscript>
    <?php echo MolajoText::_('JGLOBAL_WARNJAVASCRIPT') ?>
</noscript>
</html>