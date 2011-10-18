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
		<a href="index.php?view=preinstall" class="button white" onclick="Install.submitform();" rel="next" title="<?php echo MolajoText::_('JNext'); ?>"><?php echo MolajoText::_('JNext'); ?></a>
	</div>
	<span class="steptitle"><?php echo MolajoText::_('INSTL_LANGUAGE_TITLE'); ?></span>
</div>
<form action="index.php" method="post" id="adminForm" class="form-validate">
	<div id="installer">
		<div class="m">
			<h2><?php echo MolajoText::_('INSTL_SELECT_LANGUAGE_TITLE'); ?></h2>
			<div class="install-text">
				<?php echo MolajoText::_('INSTL_SELECT_LANGUAGE_DESC'); ?>
			</div>
			<div class="install-body">
				<div class="m">
					<fieldset>
                
						<?php //echo $this->form->getInput('language'); ?>
					</fieldset>
				</div>
				<div class="clr"></div>
			</div>
			<div class="clr"></div>
		</div>
	</div>
	<input type="hidden" name="task" value="setup.setlanguage" />
	<?php //echo JHtml::_('form.token'); ?>
</form>