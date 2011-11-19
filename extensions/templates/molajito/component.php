<?php
/**
 * @package     Molajo
 * @subpackage  Molajito
 * @copyright   Copyright (C) 2011 Cristina Solana. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

if (MolajoFactory::getApplication()->getConfig('html5', true)): ?>
    <!DOCTYPE html>
<?php else: ?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php endif; ?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo MOLAJO_BASE_FOLDER; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>" >
<head>
    <jdoc:include type="head" />
</head>
<body class="contentpane">
	<jdoc:include type="message" />
	<jdoc:include type="component" />
</body>
</html>
