<?php
/**
 * @package     Molajo
 * @subpackage  Mojito
 * @copyright   Copyright (C) 2011 Cristina Solana. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

$document = MolajoFactory::getDocument();
$browser = JBrowser::getInstance();
$lang = MolajoFactory::getLanguage();

$document->addStyleSheet($url=JURI::base().'templates/'.$this->template.'/css/jquery.ui.core.css', $type='text/css', $media=null, $attribs=array(), $priority=999);
$document->addStyleSheet($url=JURI::base().'templates/'.$this->template.'/css/jquery.ui.accordion.css', $type='text/css', $media=null, $attribs=array(), $priority=999);
$document->addStyleSheet($url=JURI::base().'templates/'.$this->template.'/css/jquery.ui.tabs.css', $type='text/css', $media=null, $attribs=array(), $priority=999);
$document->addStyleSheet($url=JURI::base().'templates/'.$this->template.'/css/custom.css', $type='text/css', $media=null, $attribs=array(), $priority=999);
$document->addScript($url=JURI::base().'templates/'.$this->template.'/js/jquery-ui-1.8.15.custom.js');

/** cristina - is there RTL and/or IE concerns? What is IE? */
if ($this->direction == 'rtl') :

endif;

if ($browser->getBrowser()=='msie') {
    if ($browser->getMajor() <= 7) {

    } else {

    }
}

