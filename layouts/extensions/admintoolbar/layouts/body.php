<?php
/**
 * @package     Molajo
 * @subpackage  Layout
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

// Import dependancies.
// jimport('joomla.html.toolbar');


// Get the toolbar.
$toolbar = MolajoToolbar::getInstance('toolbar')->render('toolbar');

require MolajoModuleHelper::getLayoutPath('mod_toolbar', $params->get('layout', 'default'));