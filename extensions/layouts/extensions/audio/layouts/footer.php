<?php
/**
 * @package     Molajo
 * @subpackage  Audio
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

$this->audio_file_loader .= ' });';
MolajoFactory::getApplication()->addScriptDeclaration($this->audio_file_loader);
