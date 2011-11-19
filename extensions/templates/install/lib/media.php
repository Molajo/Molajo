<?php
/**
 * @package     Molajo
 * @subpackage  Install
 * @copyright   Copyright (C) 2011 Chris Rault. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;
?>
<link rel="stylesheet" href="<?php echo JURI::base().'templates/'.$this->template; ?>/css/install.css" />
<?php if ($lcbrowser == 'firefox') { ?>
<link rel="stylesheet" href="<?php echo JURI::base().'templates/'.$this->template; ?>/css/firefox.css" />
<?php } ?>
<?php if ($lcbrowser == 'chrome' || $lcbrowser == 'safari'){ ?>
<link rel="stylesheet" href="<?php echo JURI::base().'templates/'.$this->template; ?>/css/webkit.css" />
<?php } ?>
<?php if ($lcbrowser == 'opera') { ?>
<link rel="stylesheet" href="<?php echo JURI::base().'templates/'.$this->template; ?>/css/opera.css" />
<?php } ?>
<?php if ($lcbrowser == 'internetexplorer') { ?>
<link rel="stylesheet" href="<?php echo JURI::base().'templates/'.$this->template; ?>/css/ie.css" />
<?php } ?>
<?php if ($lcbrowser == 'opera') { ?>
<link rel="icon" type="image/png" href="http://prototype.dev/molajo-installer/images/quick-dial.png">
<?php } ?>
<?php if ($lcbrowser == 'internetexplorer' && ($ver == '60' || $ver == '70' || $ver == '80')) { ?>
<!-- Compliance patch for Microsoft browsers -->
<!--[if lt IE 9]><script src="<?php echo JURI::base().'templates/'.$this->template; ?>/ie7/IE9.js"></script><![endif]-->
<?php } ?>

<script type="text/javascript" src="<?php echo JURI::base().'templates/'.$this->template; ?>/js/jquery.js"></script>
<script type="text/javascript" src="<?php echo JURI::base().'templates/'.$this->template; ?>/js/install.js"></script>
