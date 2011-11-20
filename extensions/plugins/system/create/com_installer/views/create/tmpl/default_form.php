<?php
/**
 * @version     $id: com_installer
 * @package     Molajo
 * @subpackage  Create
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;
/** Create MVC files
Note: was not able to use the create controller - the form submit of create.create did not find the folder/file
Change the task to create and added the create method to the display controller
JLoader::register('InstallerControllerCreate', MOLAJO_LIBRARY_COM_INSTALLER.'/controllers/create.php');
require_once MOLAJO_LIBRARY_COM_INSTALLER.'/controllers/create.php';
 **/
?>
<script type="text/javascript">
	Joomla.submitbutton_component = function(pressbutton) {
		var form = document.getElementById('adminForm');

		// do field validation
		if (form.component_singular_item_name.value == "") {
			alert("<?php echo MolajoText::_('PLG_SYSTEM_CREATE_SPECIFY_A_SINGULAR_ITEM_NAME', true); ?>");
		} else if (form.component_singular_item_name.value == "item") {
			alert("<?php echo MolajoText::_('PLG_SYSTEM_CREATE_SPECIFY_A_SINGULAR_ITEM_NAME', true); ?>");
		} else if (form.component_plural_item_name.value == "") {
			alert("<?php echo MolajoText::_('PLG_SYSTEM_CREATE_SPECIFY_A_PLURAL_ITEM_NAME', true); ?>");
		} else if (form.component_plural_item_name.value == "items") {
			alert("<?php echo MolajoText::_('PLG_SYSTEM_CREATE_SPECIFY_A_PLURAL_ITEM_NAME', true); ?>");
		} else {
			form.createtype.value = 'component';
			form.submit();
		}
	}

	Joomla.submitbutton_module = function(pressbutton) {
		var form = document.getElementById('adminForm');

		// do field validation
		if (form.module_name.value == "") {
			alert("<?php echo MolajoText::_('PLG_SYSTEM_CREATE_SPECIFY_A_MODULE_NAME', true); ?>");
		} else if (form.module_component.value == "") {
			alert("<?php echo MolajoText::_('PLG_SYSTEM_CREATE_SPECIFY_A_MODULE_COMPONENT', true); ?>");
		} else {
			form.createtype.value = 'module';
			form.submit();
		}
	}

	Joomla.submitbutton_plugin = function(pressbutton) {
		var form = document.getElementById('adminForm');

		// do field validation
		if (form.plugin_name.value == "") {
			alert("<?php echo MolajoText::_('PLG_SYSTEM_CREATE_SPECIFY_A_PLUGIN_NAME', true); ?>");
		} else if (form.plugin_type.value == "") {
			alert("<?php echo MolajoText::_('PLG_SYSTEM_CREATE_SPECIFY_A_PLUGIN_TYPE', true); ?>");
		} else {
			form.createtype.value = 'plugin';
			form.submit();
		}
	}

	Joomla.submitbutton_template = function(pressbutton) {
		var form = document.getElementById('adminForm');

		// do field validation
		if (form.template_name.value == "") {
			alert("<?php echo MolajoText::_('PLG_SYSTEM_CREATE_SPECIFY_A_TEMPLATE_NAME', true); ?>");
		} else {
			form.createtype.value = 'template';
			form.submit();
		}
	}
</script>

<form enctype="multipart/form-data" action="<?php echo MolajoRoute::_('index.php?option=com_installer&view=create');?>" method="post" name="adminForm" id="adminForm">

	<?php if ($this->ftp) : ?>
		<?php echo $this->loadTemplate('ftp'); ?>
	<?php endif; ?>

	<div class="width-70 fltlft">

		<fieldset class="uploadform">

			<legend><?php echo MolajoText::_('PLG_SYSTEM_CREATE_CREATE_COMPONENT'); ?></legend>

                <label for="component_singular_item_name">
                    <?php echo MolajoText::_('PLG_SYSTEM_CREATE_COMPONENT_SINGULAR_ITEM_NAME'); ?>
                </label>
                    <input type="text" id="component_singular_item_name" name="component_singular_item_name" class="input_box" size="70" value="<?php echo $this->state->get('create.component_singular_item_name'); ?>" />

                <label for="component_plural_item_name">
                    <?php echo MolajoText::_('PLG_SYSTEM_CREATE_COMPONENT_PLURAL_ITEM_NAME'); ?>
                </label>
                    <input type="text" id="component_plural_item_name" name="component_plural_item_name" class="input_box" size="70" value="<?php echo $this->state->get('create.component_plural_item_name'); ?>" />

                <input class="button" type="button" value="<?php echo MolajoText::_('PLG_SYSTEM_CREATE_CREATE_COMPONENT'); ?>" onclick="Joomla.submitbutton_component()" />
		</fieldset>

        <div class="clr"></div>

		<fieldset class="uploadform">
			<legend><?php echo MolajoText::_('PLG_SYSTEM_CREATE_CREATE_MODULE'); ?></legend>

			<label for="module_name">
                <?php echo MolajoText::_('PLG_SYSTEM_CREATE_MODULE_NAME'); ?>
            </label>
                <input type="text" id="module_name" name="module_name" class="input_box" size="70" value="<?php echo $this->state->get('create.module_name'); ?>" />

            <label for="module_component">
                <?php echo MolajoText::_('PLG_SYSTEM_CREATE_MODULE_COMPONENT'); ?>
            </label>
                <select name="module_component" class="input_box">
                    <option value=""><?php echo MolajoText::_('PLG_SYSTEM_CREATE_SELECT_COMPONENT');?></option>
                    <?php echo JHtml::_('select.options', JHtml::_('molajocomponent.options'), 'value', 'text', $this->state->get('create.module_component'));?>
                </select>

			<input class="button" type="button" value="<?php echo MolajoText::_('PLG_SYSTEM_CREATE_CREATE_MODULE'); ?>" onclick="Joomla.submitbutton_module()" />
		</fieldset>

        <div class="clr"></div>

		<fieldset class="uploadform">
			<legend><?php echo MolajoText::_('PLG_SYSTEM_CREATE_CREATE_PLUGIN'); ?></legend>

			<label for="plugin_name">
                <?php echo MolajoText::_('PLG_SYSTEM_CREATE_PLUGIN_NAME'); ?>
            </label>
                <input type="text" id="plugin_name" name="plugin_name" class="input_box" size="70" value="<?php echo $this->state->get('create.plugin_name'); ?>" />

            <label for="plugin_type">
                <?php echo MolajoText::_('PLG_SYSTEM_CREATE_PLUGIN_TYPE'); ?>
            </label>
                <select name="plugin_type" class="input_box">
                    <option value=""><?php echo MolajoText::_('PLG_SYSTEM_CREATE_SELECT_PLUGIN_TYPE');?></option>
                    <?php echo JHtml::_('select.options', JHtml::_('plugintype.options'), 'value', 'text', $this->state->get('create.plugin_option'));?>
                </select>

			<input class="button" type="button" value="<?php echo MolajoText::_('PLG_SYSTEM_CREATE_CREATE_PLUGIN'); ?>" onclick="Joomla.submitbutton_plugin()" />
		</fieldset>

        <div class="clr"></div>

		<fieldset class="uploadform">
			<legend><?php echo MolajoText::_('PLG_SYSTEM_CREATE_CREATE_TEMPLATE'); ?></legend>

			<label for="template_name">
                <?php echo MolajoText::_('PLG_SYSTEM_CREATE_TEMPLATE_NAME'); ?>
            </label>
                <input type="text" id="template_name" name="template_name" class="input_box" size="70" value="<?php echo $this->state->get('create.template_name'); ?>" />

			<input class="button" type="button" value="<?php echo MolajoText::_('PLG_SYSTEM_CREATE_CREATE_TEMPLATE'); ?>" onclick="Joomla.submitbutton_template()" />
		</fieldset>

		<input type="hidden" name="type" value="" />
		<input type="hidden" name="createtype" value="component" />
		<input type="hidden" name="task" value="create" />

		<?php echo JHtml::_('form.token'); ?>
	</div>   
</form>