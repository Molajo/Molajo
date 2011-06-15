<?php
/**
 * @version		$Id: mod_login.php 20196 2011-01-09 02:40:25Z ian $
 * @package		Joomla.Administrator
 * @subpackage	mod_login
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

// Include the syndicate functions only once
require_once dirname(__FILE__).'/helper.php';

$langs	= modLoginHelper::getLanguageList();
$return	= modLoginHelper::getReturnURI();
require JModuleHelper::getLayoutPath('mod_login', $params->get('layout', 'default'));