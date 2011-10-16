<?php
/**
 * @package     Molajo
 * @subpackage  Installation
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;
?>
<div id="step">
	<div class="far-right">

		<a class="button white" href="<?php echo JURI::root(); ?>" title="<?php echo MolajoText::_('JSITE'); ?>"><?php echo MolajoText::_('JSITE'); ?></a>
		<a class="button white" href="<?php echo JURI::root(); ?>administrator/" title="<?php echo MolajoText::_('JADMINISTRATOR'); ?>"><?php echo MolajoText::_('JADMINISTRATOR'); ?></a>

	</div>
	<span class="steptitle"><?php echo MolajoText::_('INSTL_COMPLETE_REMOVE_FOLDER'); ?></span>
</div>
<div id="installer">
	<p class="error remove"><?php echo MolajoText::_('INSTL_COMPLETE_REMOVE_INSTALLATION'); ?></p>
</div>
