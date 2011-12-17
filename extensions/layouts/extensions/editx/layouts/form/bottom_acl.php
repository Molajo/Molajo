<?php
/**
 * @version     $id: layout
 * @package     Molajo
 * @subpackage  Single View
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

if ($this->row->canEditstate === true) {
    echo '<div class="width-100 fltlft">';
    echo MolajoHTML::_('sliders.start', 'permissions-sliders-' . $this->slider_id, array('useCookie' => 1));
    echo MolajoHTML::_('sliders.panel', MolajoTextHelper::_('MOLAJO_FIELDSET_RULES'), 'access-rules'); ?>

<fieldset class="panelform">
    <?php echo $this->form->getLabel('rules'); ?>
    <?php echo $this->form->getInput('rules'); ?>
</fieldset>

<?php
    echo MolajoHTML::_('sliders.end');
    echo '</div>';
}