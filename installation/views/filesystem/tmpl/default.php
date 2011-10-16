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

		<a class="button white" href="index.php?view=database" onclick="return Install.goToPage('database');" rel="prev" title="<?php echo MolajoText::_('JPrevious'); ?>"><?php echo MolajoText::_('JPrevious'); ?></a>
		<a class="button white" href="#" onclick="Install.submitform();" rel="next" title="<?php echo MolajoText::_('JNext'); ?>"><?php echo MolajoText::_('JNext'); ?></a>
        
	</div>
	<span class="steptitle"><?php echo MolajoText::_('INSTL_FTP'); ?></span>
</div>

<form action="index.php" method="post" id="adminForm" class="form-validate">	
	<div id="installer">
		<div class="m">
			<h2>
				<?php echo MolajoText::_('INSTL_FTP_TITLE'); ?>
			</h2>
			<div class="install-text">
				<?php echo MolajoText::_('INSTL_FTP_DESC'); ?>
			</div>
			<div class="install-body">
				<div class="m">
					<h3 class="title-smenu" title="<?php echo MolajoText::_('INSTL_BASIC_SETTINGS'); ?>">
						<?php echo MolajoText::_('INSTL_BASIC_SETTINGS'); ?>
					</h3>
					<div class="section-smenu">
						
							<label>	<?php echo $this->form->getLabel('ftp_enable'); ?> </label>
									<?php echo $this->form->getInput('ftp_enable'); ?>
						<br />
							<label>	<?php echo $this->form->getLabel('ftp_user'); ?> </label>
									<?php echo $this->form->getInput('ftp_user'); ?>
								
								<span> <em>
									<?php echo MolajoText::_('INSTL_FTP_USER_DESC'); ?>
								</em> </span>	
						<br />
							<label>	<?php echo $this->form->getLabel('ftp_pass'); ?> </label>
									<?php echo $this->form->getInput('ftp_pass'); ?>
								
									<span> <em>
									<?php echo MolajoText::_('INSTL_FTP_PASSWORD_DESC'); ?>
									</em> </span>
						<br />
							
							<div id="rootPath">
								<label>	<?php echo $this->form->getLabel('ftp_root'); ?> </label>
									
										<?php echo $this->form->getInput('ftp_root'); ?>
						<br />
							</div>
						

						<input type="button" id="findbutton" class="button white" value="<?php echo MolajoText::_('INSTL_AUTOFIND_FTP_PATH'); ?>" onclick="Install.detectFtpRoot(this);" />
						<input type="button" id="verifybutton" class="button white" value="<?php echo MolajoText::_('INSTL_VERIFY_FTP_SETTINGS'); ?>" onclick="Install.verifyFtpSettings(this);" />
						<br /><br />
					</div>

					<h3 class="title-smenu moofx-toggler" title="<?php echo MolajoText::_('INSTL_ADVANCED_SETTINGS'); ?>">
						<a href="#"><?php echo MolajoText::_('INSTL_ADVANCED_SETTINGS'); ?></a>
					</h3>
					<div class="section-smenu moofx-slider">
						
							<div id="host">
								<label>	<?php echo $this->form->getLabel('ftp_host'); ?> </label>
																	
									<?php echo $this->form->getInput('ftp_host'); ?>
							<br />
                            </div>
							<div id="port">
								<label>	<?php echo $this->form->getLabel('ftp_port'); ?> </label>
								
									<?php echo $this->form->getInput('ftp_port'); ?>
							<br />
							</div>
							<label>	<?php echo $this->form->getLabel('ftp_save'); ?> </label>
																	
									<?php echo $this->form->getInput('ftp_save'); ?>
							<br />
							
					
					</div>
				</div>
			</div>
			<div class="clr"></div>
		</div>
	</div>
	<input type="hidden" name="task" value="setup.filesystem" />
	<?php echo JHtml::_('form.token'); ?>
</form>
