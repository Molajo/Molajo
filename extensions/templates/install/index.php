<?php
/**
 * @package     Molajo
 * @subpackage  Install
 * @copyright   Copyright (C) 2011 Chris Rault. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;
include dirname(__FILE__).'/lib/functions.php';
if ($configHTML == true): ?>
<!DOCTYPE html>
<?php else: ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php endif; ?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
    <jdoc:include type="head" />
    <?php include dirname(__FILE__).'/lib/media.php'; ?>
</head>
<body class="<?php echo $lcbrowser.' '.$lcbrowser.$ver.' '.strtolower($browser->getPlatform()); ?>">
	<div id="wrap">
        <?php include dirname(__FILE__).'/lib/header.php'; ?>
		<div id="main" class="step<?php echo $stepNumber; ?>">
            <jdoc:include type="component" />
        </div>
		<?php include dirname(__FILE__).'/lib/footer.php'; ?>
	</div>
</body>
<noscript>
    <?php echo MolajoText::_('Warning! JavaScript must be enabled for proper operation of the installer.') ?>
</noscript>
</html>