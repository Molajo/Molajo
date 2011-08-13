<?php
/**
 * @package     Molajo
 * @subpackage  Template
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2011 Individual Molajo Contributors. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;
include dirname(__FILE__).'/include/css.php';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo MOLAJO_PATH_ROOT; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>" >
<head>
    <jdoc:include type="head" />
</head>
<body id="minwidth-body">
    <div id="border-top" class="h_blue">
        <jdoc:include type="modules" name="header" />
    </div>
    <?php
    if (MolajoFactory::getUser()->id == 0) :
        include dirname(__FILE__).'/include/login.php';
    else :
    ?>
        <div id="header-box">
            <div id="module-menu">
                <jdoc:include type="modules" name="menu" />
            </div>
            <div id="module-status">
                <jdoc:include type="modules" name="status" />
                <jdoc:include type="modules" name="logout" />
            </div>
            <div class="clr"></div>
        </div>
<?php
    if (MolajoFactory::getSession()->get('page.option') == 'com_control-panel') :
        include dirname(__FILE__).'/include/control_panel.php';
    else :
        include dirname(__FILE__).'/include/default.php';
    endif;
endif;
?>
		<jdoc:include type="modules" name="footer" style="none"  />
</body>
</html>