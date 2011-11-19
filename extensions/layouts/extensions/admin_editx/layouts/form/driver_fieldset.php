<?php
/**
 * @version		$Id: attribs.php
 * @package		Joomla.Administrator
 * @subpackage          com_all
 * @copyright           Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('MOLAJO') or die;

$fieldSets = $this->form->getFieldsets();
foreach ($fieldSets as $name => $fieldSet) { ?>

        <?php echo MolajoHTML::_('sliders.panel',MolajoText::_($fieldSet->label), $name.'-options');?>
        <?php if (isset($fieldSet->description) && trim($fieldSet->description)) :?>
                <p class="tip"><?php echo $this->escape(MolajoText::_($fieldSet->description));?></p>
        <?php endif;?>
        <fieldset class="panelform">
                <ul class="adminformlist">
                <?php foreach ($this->form->getFieldset($name) as $field) : ?>
                        <li><?php echo $field->label; ?><?php echo $field->input; ?></li>
                <?php endforeach; ?>
                </ul>
        </fieldset>
<?php } ?>