<?php
/**
 * @version        $Id: default.php 21020 2011-03-27 06:52:01Z infograf768 $
 * @package        Joomla.Site
 * @subpackage    syndicate
 * @copyright    Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('MOLAJO') or die;
?>
<a href="<?php echo $link ?>" class="syndicate-module<?php echo $layout_class_suffix ?>">
    <?php echo MolajoHTML::_('image', 'system/livemarks.png', 'feed-image', NULL, true); ?>
    <span><?php echo $text ?></span></a>
