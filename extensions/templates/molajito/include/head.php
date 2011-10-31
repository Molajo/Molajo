<?php
/**
 * @package     Molajo
 * @subpackage  Mojito
 * @copyright   Copyright (C) 2011 Cristina Solana. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

$document = MolajoFactory::getDocument();
$lang = MolajoFactory::getLanguage();

$document->addStyleSheet($url=JURI::base().'templates/'.$this->template.'/css/jquery.ui.all.css', $type='text/css', $media=null, $attribs=array(), $priority=999);
$document->addStyleSheet($url=JURI::base().'templates/'.$this->template.'/css/custom.css', $type='text/css', $media=null, $attribs=array(), $priority=999);
$document->addScript($url=JURI::base().'templates/'.$this->template.'/js/jquery-ui-1.6.2.js');
$document->addScript($url=JURI::base().'templates/'.$this->template.'/js/jquery-ui-1.8.15.custom.js');
$document->addScript($url=JURI::base().'templates/'.$this->template.'/js/scripts.js');