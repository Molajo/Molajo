<?php
/**
 * @package     Molajo
 * @subpackage  Mojito
 * @copyright   Copyright (C) 2011 Cristina Solana. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;
include dirname(__FILE__).'/include/css.php';

if (MolajoFactory::getApplication()->getCfg('html5', true)): ?>
<!DOCTYPE html>
<?php else: ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php endif; ?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo MOLAJO_PATH_ROOT; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>" >
<head>
    <jdoc:include type="head" />
</head>
<body id="minwidth-body">
	<div class="container">
        <jdoc:include type="modules" name="header" wrap="header" />
        <jdoc:include type="modules" name="launchpad" wrap="nav" />
        <?php
        if (MolajoFactory::getUser()->id == 0) :
            include dirname(__FILE__).'/include/login.php';
        else :
        ?>
        <div class="container">
            <section>
            <?php
                if (MolajoFactory::getSession()->get('page.option') == 'com_dashboard') :
                    include dirname(__FILE__).'/include/dashboard.php';
                else :
                    include dirname(__FILE__).'/include/component.php';
                endif;
                ?>
            </section>
        </div>
        <?php
        endif;
        ?>
        <jdoc:include type="modules" name="footer" wrap="footer" />
    </div>
</body>
<noscript>
    <?php echo  JText::_('JGLOBAL_WARNJAVASCRIPT') ?>
</noscript>
</html>