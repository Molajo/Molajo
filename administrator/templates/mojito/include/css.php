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
$lang = JFactory::getLanguage();

$document->addStyleSheet($url=JURI::base().'templates/'.$this->template.'/css/jquery.ui.all.css', $type='text/css', $media=null, $attribs=array(), $priority=999);
$document->addStyleSheet($url=JURI::base().'templates/'.$this->template.'/css/custom.css', $type='text/css', $media=null, $attribs=array(), $priority=999);
$this->document->addScript($urlPath.'/js/jquery-1.6.2.js');
$this->document->addScript($urlPath.'/js/ui/jquery-ui-1.8.15.custom.js');

/** cristina - is there RTL and/or IE concerns? */
if ($this->direction == 'rtl') :

endif;

if ($browser->getBrowser()=='msie') {
    if ($browser->getMajor() <= 7) {

    } else {

    }
}

