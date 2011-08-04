<?php
/** 
 * @package     Minima
 * @subpackage  mod_myshortcuts
 * @author      Marco Barbosa
 * @copyright   Copyright (C) 2010 Marco Barbosa. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// Check to ensure this file is included in Molajo
defined('_JEXEC') or die;

require_once dirname(__FILE__).DS.'helper.php';

// set the params
ModMyShortcutsHelper::setParams($params);

require MolajoModuleHelper::getLayoutPath('mod_myshortcuts');
