<?php
/**
 * @version        $Id: attribs.php
 * @package        Joomla.Administrator
 * @subpackage          all
 * @copyright           Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('MOLAJO') or die;

$nameSets = $this->form->getFieldsets();
foreach ($nameSets as $name => $nameSet) {
    ?>

<?php echo MolajoHTML::_('sliders.panel', MolajoTextHelper::_($nameSet->label), $name . '-options'); ?>
<?php if (isset($nameSet->description) && trim($nameSet->description)) : ?>
    <p class="tip"><?php echo $this->escape(MolajoTextHelper::_($nameSet->description));?></p>
    <?php endif; ?>
<fieldset class="panelform">
    <ul class="adminformlist">
        <?php foreach ($this->form->getFieldset($name) as $name) : ?>
        <li><?php echo $name->label; ?><?php echo $name->input; ?></li>
        <?php endforeach; ?>
    </ul>
</fieldset>
<?php } ?>