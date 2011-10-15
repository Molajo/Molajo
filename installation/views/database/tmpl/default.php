<?php
/**
 * @version		$Id: default.php 21766 2011-07-08 12:20:23Z eddieajau $
 * @package		Joomla.Installation
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>
<div id="step">
	<div class="far-right">

		<a class="button white" href="index.php?view=preinstall" onclick="return Install.goToPage('preinstall');" rel="prev" title="<?php echo JText::_('JPrevious'); ?>"><?php echo JText::_('JPrevious'); ?></a>
		<a class="button white" href="#" onclick="Install.submitform();" rel="next" title="<?php echo JText::_('JNext'); ?>"><?php echo JText::_('JNext'); ?></a>

	</div>
	<span class="steptitle"><?php echo JText::_('INSTL_DATABASE'); ?></span>
</div>
<form action="index.php" method="post" id="adminForm" class="form-validate">
	<div id="installer">
		<div class="m">
			
			<div class="install-text">
					<?php echo JText::_('INSTL_DATABASE_DESC'); ?>
			</div>
			<div class="install-body">
				<div class="m">
				<fieldset>	
						<ol class="list-reset forms">
						<li>
							<span class="inner-wrap">
								<label>	<?php echo $this->form->getLabel('db_type'); ?> </label>
								<?php echo $this->form->getInput('db_type'); ?>
								<span class="note"><?php echo JText::_('INSTL_DATABASE_TYPE_DESC'); ?></span>
							</span>
						</li>
                        
                        <li>
							<span class="inner-wrap">
								<label>	<?php echo $this->form->getLabel('db_host'); ?> </label>
								<?php echo $this->form->getInput('db_host'); ?>
								<span class="note"><?php echo JText::_('INSTL_DATABASE_HOST_DESC'); ?></span>
							</span>
						</li>
                         <li>
							<span class="inner-wrap">
								<label>	<?php echo $this->form->getLabel('db_user'); ?> </label>
								<?php echo $this->form->getInput('db_user'); ?>
								<span class="note"><?php echo JText::_('INSTL_DATABASE_USER_DESC'); ?></span>
							</span>
						</li>
                         <li>
							<span class="inner-wrap">
								<label>	<?php echo $this->form->getLabel('db_pass'); ?> </label>
								<?php echo $this->form->getInput('db_pass'); ?>
								<span class="note"><?php echo JText::_('INSTL_DATABASE_PASSWORD_DESC'); ?></span>
							</span>
						</li>
                        
                        <li>
							<span class="inner-wrap">
								<label>	<?php echo $this->form->getLabel('db_name'); ?> </label>
								<?php echo $this->form->getInput('db_name'); ?>
								<span class="note"><?php echo JText::_('INSTL_DATABASE_NAME_DESC'); ?></span>
							</span>
						</li>
                        
                         <li>
							<span class="inner-wrap">
								<label>	<?php echo $this->form->getLabel('db_prefix'); ?> </label>
								<?php echo $this->form->getInput('db_prefix'); ?>
								<span class="note"><?php echo JText::_('INSTL_DATABASE_PREFIX_DESC'); ?></span>
							</span>
						</li>
                        </ol>
                        
                        
											
						
							
                            </fieldset>
                            <fieldset>
                            <ol class="list-reset forms">
						
						<li>
							
							<label <?php echo $this->form->getLabel('db_old'); ?></label>
							<?php echo $this->form->getInput('db_old'); ?>
							<span class="note"><?php echo JText::_('INSTL_DATABASE_OLD_PROCESS_DESC'); ?></span>
						</li>
					</ol>
							
							 </fieldset>
						
				</div>
			</div>
			<div class="clr"></div>
		</div>
	</div>
	<input type="hidden" name="task" value="setup.database" />
	<?php echo JHtml::_('form.token'); ?>
</form>
