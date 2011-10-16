<?php
/**
 * @version     $id: layout
 * @package     Molajo
 * @subpackage  Single View
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;
?>
<div class="width-60 fltlft">
        <fieldset class="adminform">
                <legend><?php echo ($this->slider_id == 0) ? MolajoText::_('MOLAJO_NEW_'.strtoupper($this->state->get('request.view'))) : MolajoText::sprintf(strtoupper($this->request['option']).'_EDIT_'.strtoupper($this->state->get('request.view')), $this->slider_id); ?></legend>

