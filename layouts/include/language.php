<?php
/**
 * @version     $id: item.php
 * @package     Molajo
 * @subpackage  Standard Driver
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Language
 *
 * Automatically includes the following files (if existing)
 *
 * 1. Master Layout folder Language Files found in => layout/[current-language]/
 * 2. Current Layout folder Language Files found in => layout/current-layout/[current-language]/
 */
$language = JFactory::getLanguage();
$language->load('layouts', MOLAJO_LAYOUTS, $language->getDefault(), true, true);
$language->load('layouts_'.$this->state->get('request.layout'), $this->layoutFolder, $language->getDefault(), true, true);