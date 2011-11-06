<?php
/**
 * @package     Molajo
 * @subpackage  Mojito
 * @copyright   Copyright (C) 2011 Cristina Solana. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

$document = MolajoFactory::getDocument();
$document->template = 'molajito';           //todo: amy fix
$lang = MolajoFactory::getLanguage();

$document->addStyleSheet($url=MOLAJO_BASE_URL_NOAPP.'extensions/templates/'.$document->template.'/css/jquery.ui.all.css', $type='text/css', $media=null, $attribs=array(), $priority=999);
$document->addStyleSheet($url=MOLAJO_BASE_URL_NOAPP.'extensions/templates/'.$document->template.'/css/custom.css', $type='text/css', $media=null, $attribs=array(), $priority=999);
$document->addScript($url=MOLAJO_BASE_URL_NOAPP.'extensions/templates/'.$document->template.'/js/jquery-1.6.2.js');
$document->addScript($url=MOLAJO_BASE_URL_NOAPP.'extensions/templates/'.$document->template.'/js/jquery-ui-1.8.15.custom.js');
$document->addScript($url=MOLAJO_BASE_URL_NOAPP.'extensions/templates/'.$document->template.'/js/scripts.js');