<link rel="stylesheet" href="css/install.css" />
<?php if ($lcbrowser == 'firefox'){ ?><link rel="stylesheet" href="css/firefox.css" /><?php } ?>
<?php if ($lcbrowser == 'chrome' || $lcbrowser == 'safari'){ ?><link rel="stylesheet" href="css/webkit.css" /><?php } ?>
<?php if ($lcbrowser == 'opera'){ ?><link rel="stylesheet" href="css/opera.css" /><?php } ?>
<?php if ($lcbrowser == 'internetexplorer'){ ?><link rel="stylesheet" href="css/ie.css" /><?php } ?>
<?php if ($lcbrowser == 'opera'){ ?><link rel="icon" type="image/png" href="http://prototype.dev/molajo-installer/images/quick-dial.png"><?php } ?>
<?php if ($lcbrowser == 'internetexplorer' && ($ver == '60' || $ver == '70' || $ver == '80')){ ?>
<!-- Compliance patch for Microsoft browsers -->
<!--[if lt IE 9]><script src="ie7/IE9.js"></script><![endif]-->
<?php } ?>

<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/install.js"></script>
