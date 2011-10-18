<?php
/**
 * @package     Molajo
 * @subpackage  Installation
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

if ($this->sample_installed) : ?>
<script type="text/javascript">
	window.addEvent('domready', function() {
		var select = document.getElementById('jform_sample_file');
		var button = document.getElementById('theDefault').children[0];
		button.setAttribute('disabled','disabled');
		button.setAttribute('value','<?php echo MolajoText::_('INSTL_SITE_SAMPLE_LOADED', true); ?>');
		select.setAttribute('disabled','disabled');
	});
</script>
<?php endif; ?>

<div id="step">
	<div class="far-right">

		<a class="button white" href="index.php?view=filesystem" onclick="return Install.goToPage('filesystem');" rel="prev" title="<?php echo MolajoText::_('JPrevious'); ?>"><?php echo MolajoText::_('JPrevious'); ?></a>
		<a class="button white" href="#" onclick="Install.submitform();" rel="next" title="<?php echo MolajoText::_('JNext'); ?>"><?php echo MolajoText::_('JNext'); ?></a>

	</div>
	<span class="steptitle"><?php echo MolajoText::_('INSTL_SITE'); ?></span>
</div>
		
<div id="installer">
	<div class="m">
		<form action="index.php" method="post" id="adminForm" class="form-validate">
			<h2><?php echo MolajoText::_('INSTL_SITE_NAME_TITLE'); ?></h2>
			<div class="install-text">
				<?php echo MolajoText::_('INSTL_SITE_NAME_DESC'); ?>
			</div>
			<div class="install-body">
				<div class="m">
					<h3 class="title-smenu" title="<?php echo MolajoText::_('INSTL_BASIC_SETTINGS'); ?>">
						<?php echo MolajoText::_('INSTL_BASIC_SETTINGS'); ?>
					</h3>
					<div class="section-smenu">
						<fieldset>	
							<label>	<?php echo $this->form->getLabel('site_name'); ?> </label>
									<?php echo $this->form->getInput('site_name'); ?>
						<br />
						</fieldset>
					</div>

					<h3 class="title-smenu moofx-toggler" title="<?php echo MolajoText::_('INSTL_SITE_META_ADVANCED_SETTINGS'); ?>">
						<a href="#"><?php echo MolajoText::_('INSTL_SITE_META_ADVANCED_SETTINGS'); ?></a>
					</h3>
					<div class="section-smenu moofx-slider">
							
							<fieldset>	
									<label>	<?php echo $this->form->getLabel('site_metadesc'); ?> </label>                                   
											<?php echo $this->form->getInput('site_metadesc'); ?>
							<br />
								
								<label>	<?php echo $this->form->getLabel('site_metakeys'); ?> </label>  
										<?php echo $this->form->getInput('site_metakeys'); ?>
							<br />
							</fieldset>
					
						</div>
					</div>
				</div>

				<div class="newsection"></div>

				<h2><?php echo MolajoText::_('INSTL_SITE_CONF_TITLE'); ?></h2>
				<div class="install-text">
					<?php echo MolajoText::_('INSTL_SITE_CONF_DESC'); ?>
				</div>
				<div class="install-body">
					<div class="m">
						<fieldset>
							
								<label>	<?php echo $this->form->getLabel('admin_email'); ?> </label>  
										<?php echo $this->form->getInput('admin_email'); ?>
								<br />
								
								<label>	<?php echo $this->form->getLabel('admin_user'); ?> </label> 
										<?php echo $this->form->getInput('admin_user'); ?>
								<br />
								
								<label>	<?php echo $this->form->getLabel('admin_password'); ?> </label>
										<?php echo $this->form->getInput('admin_password'); ?>
								<br />
								
								<label>	<?php echo $this->form->getLabel('admin_password2'); ?> </label>
										<?php echo $this->form->getInput('admin_password2'); ?>
								<br />
							
						</fieldset>
					</div>
					<input type="hidden" name="task" value="setup.saveconfig" />
					<?php echo JHtml::_('form.token'); ?>
					<?php echo $this->form->getInput('sample_installed'); ?>
				</div>
			</form>

			<div class="clr"></div>

			<form enctype="multipart/form-data" action="index.php" method="post" id="filename">
				<h2><?php echo MolajoText::_('INSTL_SITE_LOAD_SAMPLE_TITLE'); ?></h2>
				<div class="install-text">
					<p><?php echo MolajoText::_('INSTL_SITE_LOAD_SAMPLE_DESC1'); ?></p>
					<p><?php echo MolajoText::_('INSTL_SITE_LOAD_SAMPLE_DESC2'); ?></p>
					<p><?php echo MolajoText::_('INSTL_SITE_LOAD_SAMPLE_DESC3'); ?></p>
					<p><?php echo MolajoText::_('INSTL_SITE_LOAD_SAMPLE_DESC4'); ?></p>
					<p><?php echo MolajoText::_('INSTL_SITE_LOAD_SAMPLE_DESC8'); ?></p>
				</div>
				<div class="install-body">
					<div class="m">
						<fieldset>
							
								<label>	<?php echo $this->form->getLabel('sample_file'); ?> </label>
										<?php echo $this->form->getInput('sample_file'); ?>
								<br />
							<span id="theDefault"><input class="button white" type="button" name="instDefault" value="<?php echo MolajoText::_('INSTL_SITE_INSTALL_SAMPLE_LABEL'); ?>" onclick=		"Install.sampleData(this, <?php echo $this->form->getField('sample_file')->id;?>);"/></span>
								<br />
									<p>
										<em><?php echo MolajoText::_('INSTL_SITE_INSTALL_SAMPLE_DESC'); ?></em>
									</p>
								
						</fieldset>

						<div class="message inlineError" id="theDefaultError" style="display: none">
							<dl>
								<dt class="error"><?php echo MolajoText::_('JERROR'); ?></dt>
								<dd id="theDefaultErrorMessage"></dd>
							</dl>
						</div>
					</div>
					<?php echo $this->form->getInput('type'); ?>
					<?php echo JHtml::_('form.token'); ?>
				</div>
			</form>

		<div class="clr"></div>
	</div>
</div>
