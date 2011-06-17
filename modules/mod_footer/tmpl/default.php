<?php
/**
 * @version		$Id: default.php 18117 2010-07-13 18:09:01Z infograf768 $
 * @package		Joomla.Site
 * @subpackage	mod_footer
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('JPATH_PLATFORM') or die;
?>
<div class="footer1<?php echo $params->get('moduleclass_sfx') ?>"><?php echo $lineone; ?></div>
<div class="footer2<?php echo $params->get('moduleclass_sfx') ?>"><?php echo JText::_('MOD_FOOTER_LINE2'); ?></div>