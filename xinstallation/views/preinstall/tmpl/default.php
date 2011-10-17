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

		<a class="button white" href="index.php?view=preinstall" onclick="return Install.goToPage('preinstall');" title="<?php echo MolajoText::_('JCheck_Again'); ?>"><?php echo MolajoText::_('JCheck_Again'); ?></a>
		<a class="button white" href="index.php?view=language" onclick="return Install.goToPage('language');" rel="prev" title="<?php echo MolajoText::_('JPrevious'); ?>"><?php echo MolajoText::_('JPrevious'); ?></a>
	<?php if ($this->sufficient) : ?>
	<a class="button white" href="index.php?view=database" onclick="return Install.goToPage('database');" rel="next" title="<?php echo MolajoText::_('JNext'); ?>"><?php echo MolajoText::_('JNext'); ?></a>
	<?php endif; ?>

	</div>
	<span class="steptitle"><?php echo MolajoText::_('INSTL_PRECHECK_TITLE'); ?></span>
</div>
<form action="index.php" method="post" id="adminForm" class="form-validate">
	<div id="installer">
		<div class="m">
			<h2><?php echo MolajoText::sprintf('INSTL_PRECHECK_FOR_VERSION', $this->version->getLongVersion()); ?></h2>
			<div class="install-text">
				<?php echo MolajoText::_('INSTL_PRECHECK_DESC'); ?>
			</div>
			<div class="install-body">
				<div class="m">
			<fieldset>						
                <?php foreach ($this->options as $option) : ?>       
	 		<dl>						
					
							
								<dt class="item">
									<?php echo $option->label; ?>
								</dt>
								<dd>
									<span class="<?php echo ($option->state) ? 'green' : 'red'; ?>">
										<?php echo MolajoText::_(($option->state) ? 'JYES' : 'JNO'); ?>
									</span>
									<span class="small">
										<?php echo $option->notice; ?>&#160;
									</span>
								</dd>
						
					
			</dl>
				<?php endforeach; ?>
			</fieldset>
				</div>
			</div>

			<div class="newsection"></div>

			<h2><?php echo MolajoText::_('INSTL_PRECHECK_RECOMMENDED_SETTINGS_TITLE'); ?></h2>
			<div class="install-text">
				<?php echo MolajoText::_('INSTL_PRECHECK_RECOMMENDED_SETTINGS_DESC'); ?>
			</div>
			<div class="install-body">
				<div class="m">
					<fieldset>
						
							<dl>
						
								<dt class="toggle">
									<?php echo MolajoText::_('INSTL_PRECHECK_DIRECTIVE'); ?>
								</dt>
								<dd class="toggle">
									<?php echo MolajoText::_('INSTL_PRECHECK_RECOMMENDED'); ?>
								</dd>
								<dd class="toggle">
									<?php echo MolajoText::_('INSTL_PRECHECK_ACTUAL'); ?>
								</dd>
							
							</dl>
						
					<?php foreach ($this->settings as $setting) : ?>
								<dl>
									<dt class="item">
										<?php echo $setting->label; ?>
									</dt>
									<dd class="toggle">
										
										<?php echo MolajoText::_(($setting->recommended) ? 'JON' : 'JOFF'); ?>
									
									</dd>
									<dd>
										<span class="<?php echo ($setting->state === $setting->recommended) ? 'green' : 'red'; ?>">
										<?php echo MolajoText::_(($setting->state) ? 'JON' : 'JOFF'); ?>
										</span>
									</dd>
								
					
							</dl>
						<?php endforeach; ?>
					</fieldset>
				</div>
			</div>
			<div class="clr"></div>
		</div>
	</div>
	<input type="hidden" name="task" value="" />
	<?php //echo JHtml::_('form.token'); ?>
</form>
