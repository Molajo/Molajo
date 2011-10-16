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
<body>
	<div class="container">
    <jdoc:include type="modules" name="header" wrap="header" />
    <jdoc:include type="message" />
    <section>
        <jdoc:include type="component" />
    </section>
<jdoc:include type="modules" name="footer" wrap="footer" />
</body>
<noscript>
    <?php echo MolajoText::_('JGLOBAL_WARNJAVASCRIPT') ?>
</noscript>
</html>