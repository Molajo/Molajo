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

		<a class="button white" href="<?php echo JURI::root(); ?>" title="<?php echo JText::_('JSITE'); ?>"><?php echo JText::_('JSITE'); ?></a>
		<a class="button white" href="<?php echo JURI::root(); ?>administrator/" title="<?php echo JText::_('JADMINISTRATOR'); ?>"><?php echo JText::_('JADMINISTRATOR'); ?></a>


	</div>
	<span class="steptitle"><?php echo JText::_('INSTL_COMPLETE'); ?></span>
</div>

<form action="index.php" method="post" id="adminForm" class="form-validate">
	<div id="installer">
		<div class="m">
			<h2><?php echo JText::_('INSTL_COMPLETE_TITLE'); ?></h2>
			<div class="install-text">
				<p><?php echo JText::_('INSTL_COMPLETE_DESC1'); ?></p>
				<p><?php echo JText::_('INSTL_COMPLETE_DESC2'); ?></p>
				<p><?php echo JText::_('INSTL_COMPLETE_DESC3'); ?></p>
			</div>
			<div class="install-body">
				<div class="m">
					<fieldset>
						
								<p class="error">
									<?php echo JText::_('INSTL_COMPLETE_REMOVE_INSTALLATION'); ?>
								</p>
							
							<input class="button white" type="button" name="instDefault"
                                           value="<?php echo JText::_('INSTL_COMPLETE_REMOVE_FOLDER'); ?>"
                                           onclick="Install.removeFolder(this);"/>
                              <br />
                              
							<p class="message inlineError" id="theDefaultError" style="display: none">
								
									<dl>
										<dt class="error"><?php echo JText::_('JERROR'); ?></dt>
										<dd id="theDefaultErrorMessage"></dd>
									</dl>
								
							<p>
							
									<h3>
									<?php echo JText::_('INSTL_COMPLETE_ADMINISTRATION_LOGIN_DETAILS'); ?>
									</h3>
								
							<dl>
								<dt class="notice">
									<?php echo JText::_('JUSERNAME'); ?> : </dt>
                                   
                                   <dd>     <strong><?php echo $this->options['admin_user']; ?></strong>
								</dd>
							
							<dl>
								
								<p class="notice">
									
									</div>
								</p>
							
							<?php if ($this->config) : ?>
							<p>
								<?php echo JText::_('INSTL_CONFPROBLEM'); ?>
							</p>
							
						<textarea rows="5" cols="49" name="configcode" onclick="this.form.configcode.focus();this.form.configcode.select();" ><?php echo $this->config; ?></textarea>
								
							<?php endif; ?>
						
					</fieldset>
				</div>
			</div>
			<div class="clr"></div>
		</div>
	</div>
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</form>