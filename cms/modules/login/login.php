<?php
/**
 * @version		$Id: login.php 20806 2011-02-21 19:44:59Z dextercowley $
 * @package		Joomla.Site
 * @subpackage	login
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('JPATH_PLATFORM') or die;

// Include the syndicate functions only once
require_once dirname(__FILE__) . '/helper.php';

$parameters->def('greeting', 1);

$type	= modLoginHelper::getType();
$return	= modLoginHelper::getReturnURL($parameters, $type);
$user	= MolajoFactory::getUser();

require MolajoApplicationModule::getLayoutPath('login', $parameters->get('layout', 'default'));